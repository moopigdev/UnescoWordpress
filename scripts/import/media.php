<?php
/**
 * Importer: files → media library (attachments), deduplicated by _sh_import_key.
 * Sources live outside the repo: Unesco/design/assets (images, by Figma imageRef) and
 * Unesco/media/out (videos/posters, from content/media-manifest.json). JPEG/PNG with a non-sRGB ICC profile
 * are re-tagged sRGB on copy (icc.php).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attachment id by import key (cached for the whole run).
 *
 * @param string $key Import key.
 */
function sh_imp_find_attachment( string $key ): int {
	$s = &sh_imp_state();
	if ( null === $s['att'] ) {
		$s['att'] = array();
		$ids      = get_posts(
			array(
				'post_type'        => 'attachment',
				'post_status'      => 'inherit',
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'suppress_filters' => true,
				'meta_key'         => '_sh_import_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
		foreach ( $ids as $id ) {
			$s['att'][ (string) get_post_meta( (int) $id, '_sh_import_key', true ) ] = (int) $id;
		}
	}
	return isset( $s['att'][ $key ] ) ? $s['att'][ $key ] : 0;
}

/**
 * Copy a file into uploads and create its attachment (once per key). 0 when the file is missing.
 *
 * @param string               $src   Absolute source path.
 * @param string               $key   Import key.
 * @param string               $title Attachment title (also the file name).
 * @param array<string, mixed> $video Video metadata (width/height/length) for video files.
 */
function sh_imp_attachment( string $src, string $key, string $title, array $video = array() ): int {
	$found = sh_imp_find_attachment( $key );
	if ( $found ) {
		return $found;
	}
	if ( ! is_readable( $src ) ) {
		sh_imp_warn( 'missing file: ' . $src . ' (' . $title . ')' );
		return 0;
	}
	$ext   = strtolower( (string) pathinfo( $src, PATHINFO_EXTENSION ) );
	$name  = sanitize_file_name( sanitize_title( $title ) . '.' . $ext );
	$check = wp_check_filetype( $name );
	if ( empty( $check['type'] ) ) {
		sh_imp_warn( 'file type not allowed: ' . $src );
		return 0;
	}
	$up = wp_upload_dir();
	if ( ! empty( $up['error'] ) ) {
		throw new RuntimeException( 'uploads: ' . $up['error'] );
	}
	$name = wp_unique_filename( $up['path'], $name );
	$dest = $up['path'] . '/' . $name;
	if ( ! copy( $src, $dest ) ) {
		throw new RuntimeException( 'copy failed: ' . $src );
	}
	if ( sh_imp_icc_normalize( $dest, (string) $check['type'] ) ) { // Display P3 etc. → sRGB as Figma shows it (icc.php).
		sh_imp_count( 'updated', 'icc_to_srgb' );
	}
	$id = wp_insert_attachment(
		wp_slash(
			array(
				'post_mime_type' => $check['type'],
				'post_title'     => $title,
				'post_status'    => 'inherit',
				'guid'           => $up['url'] . '/' . $name,
			)
		),
		$dest,
		0,
		true
	);
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( 'attachment ' . $key . ': ' . $id->get_error_message() );
	}
	update_post_meta( $id, '_sh_import_key', $key );
	wp_update_attachment_metadata( $id, sh_imp_attachment_meta( $id, $dest, $check['type'], $video ) );

	$s                = &sh_imp_state();
	$s['att'][ $key ] = (int) $id;
	sh_imp_count( 'created', 'attachment' );
	return (int) $id;
}

/**
 * Attachment metadata. Default: size only (no sub-sizes — originals are large and Playground is slow);
 * SH_IMPORT_SUBSIZES=1 → full wp_generate_attachment_metadata() (use on a real server).
 *
 * @param int                  $id    Attachment id.
 * @param string               $file  Absolute file.
 * @param string               $mime  Mime type.
 * @param array<string, mixed> $video Video metadata.
 * @return array<string, mixed>
 */
function sh_imp_attachment_meta( int $id, string $file, string $mime, array $video ): array {
	if ( str_starts_with( $mime, 'image/' ) && sh_imp_config()['subsizes'] ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		return wp_generate_attachment_metadata( $id, $file );
	}
	$meta = array(
		'file'     => _wp_relative_upload_path( $file ),
		'filesize' => (int) filesize( $file ),
	);
	if ( str_starts_with( $mime, 'image/' ) ) {
		$size = wp_getimagesize( $file );
		if ( is_array( $size ) ) {
			$meta['width']  = (int) $size[0];
			$meta['height'] = (int) $size[1];
		}
		$meta['sizes']      = array();
		$meta['image_meta'] = array();
		return $meta;
	}
	$meta['mime_type'] = $mime;
	foreach ( array( 'width', 'height', 'length' ) as $k ) {
		if ( isset( $video[ $k ] ) ) {
			$meta[ $k ] = $video[ $k ];
		}
	}
	return $meta;
}

/**
 * Image attachment for a Figma imageRef (file name from image-map.json "files"). 0 when none.
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string|null          $ref   imageRef.
 * @param string               $title Title.
 */
function sh_imp_image( array $map, ?string $ref, string $title ): ?int {
	if ( null === $ref || '' === $ref ) {
		return null; // No image in Figma → leave the field untouched.
	}
	$file = isset( $map['files'][ $ref ] ) ? (string) $map['files'][ $ref ] : $ref . '.png';
	return sh_imp_attachment( sh_imp_config()['assets'] . '/' . $file, 'asset:' . $ref, $title );
}

/**
 * First manifest entry whose $field equals $value (e.g. object_slug = seed slug · usage = home-trails). null = none.
 *
 * @param array<string, mixed> $manifest content/media-manifest.json.
 * @param string               $field    Entry key.
 * @param string               $value    Wanted value.
 * @return array<string, mixed>|null
 */
function sh_imp_manifest_entry( array $manifest, string $field, string $value ): ?array {
	foreach ( (array) ( isset( $manifest['files'] ) ? $manifest['files'] : array() ) as $f ) {
		if ( is_array( $f ) && isset( $f[ $field ] ) && $f[ $field ] === $value ) {
			return $f;
		}
	}
	return null;
}

/**
 * Attachments of a manifest entry: kind "video" → WebM + poster (+ HEVC only for fallback.type hevc-alpha-mov,
 * "poster-only" = the poster) · kind "still" → the still image (out = null). No entry → all 0 (field cleared).
 *
 * @param array<string, mixed>|null $entry Manifest entry.
 * @param string                    $title Title.
 * @return array{webm:int,hevc:int,poster:int,has_alpha:bool,still:int}
 */
function sh_imp_video( ?array $entry, string $title ): array {
	$out = array(
		'webm'      => 0,
		'hevc'      => 0,
		'poster'    => 0,
		'has_alpha' => false,
		'still'     => 0,
	);
	if ( null === $entry ) {
		return $out;
	}
	$dir  = sh_imp_config()['media'];
	$file = static fn( string $p ): string => $dir . '/' . basename( $p );
	$key  = static fn( string $p ): string => 'media:' . basename( $p );
	if ( isset( $entry['kind'] ) && 'still' === $entry['kind'] ) {
		if ( ! empty( $entry['still'] ) ) {
			$out['still'] = sh_imp_attachment( $file( (string) $entry['still'] ), $key( (string) $entry['still'] ), $title . ' hero' );
		}
		return $out;
	}
	if ( empty( $entry['out'] ) ) {
		return $out;
	}
	$video = array(
		'width'  => isset( $entry['width'] ) ? (int) $entry['width'] : 0,
		'height' => isset( $entry['height'] ) ? (int) $entry['height'] : 0,
		'length' => isset( $entry['duration_s'] ) ? (int) round( (float) $entry['duration_s'] ) : 0,
	);

	$out['webm']      = sh_imp_attachment( $file( (string) $entry['out'] ), $key( (string) $entry['out'] ), $title . ' video', $video );
	$out['poster']    = empty( $entry['poster'] ) ? 0 : sh_imp_attachment( $file( (string) $entry['poster'] ), $key( (string) $entry['poster'] ), $title . ' poster' );
	$out['has_alpha'] = ! empty( $entry['has_alpha'] );
	// Only real video fallbacks (opaque files have type "poster-only" = the poster above).
	if ( ! empty( $entry['fallback']['file'] ) && isset( $entry['fallback']['type'] ) && 'hevc-alpha-mov' === $entry['fallback']['type'] ) {
		$fb          = (string) $entry['fallback']['file'];
		$out['hevc'] = sh_imp_attachment( $file( $fb ), $key( $fb ), $title . ' video hevc', $video );
	}
	return $out;
}

/**
 * Attachment for a design-asset reference used inside seed style keys "*_ref" (design-dev crops:
 * "<ref>.crop-<node>.png"). Accepts a file name in design/assets (with or without extension) or a Figma
 * imageRef from image-map.json. 0 (+ warning) when no file — the caller keeps the original value.
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $value Value of the *_ref key.
 * @param string               $title Attachment title.
 */
function sh_imp_asset_ref( array $map, string $value, string $title ): int {
	$dir  = sh_imp_config()['assets'];
	$file = sh_import_ref_file( $map, $value, $dir ); // refs.php — the same rule deploy.sh bundles by.
	if ( '' !== $file ) {
		$ext = (string) pathinfo( $file, PATHINFO_EXTENSION );
		return sh_imp_attachment( $dir . '/' . $file, 'asset:' . ( '' === $ext ? $file : substr( $file, 0, -strlen( $ext ) - 1 ) ), $title );
	}
	sh_imp_warn( 'style *_ref without file (kept as text): ' . $value . ' (' . $title . ')' );
	return 0;
}

/**
 * Attachment for a timeline card's "line_art_ref" (D32/D33 · file in design/assets/lineart/ — refs.php
 * sh_import_line_art_file(), the same rule deploy.sh bundles by). 0 (+ warning) when no file.
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $value Value of items[].line_art_ref.
 * @param string               $title Attachment title.
 */
function sh_imp_line_art( array $map, string $value, string $title ): int {
	$dir  = sh_imp_config()['assets'];
	$file = sh_import_line_art_file( $map, $value, $dir );
	if ( '' !== $file ) {
		$ext = (string) pathinfo( $file, PATHINFO_EXTENSION );
		return sh_imp_attachment( $dir . '/' . $file, 'asset:' . ( '' === $ext ? $file : substr( $file, 0, -strlen( $ext ) - 1 ) ), $title );
	}
	sh_imp_warn( 'line_art_ref without file: ' . $value . ' (' . $title . ')' );
	return 0;
}
