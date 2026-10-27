<?php
/**
 * Importer: files → media library (attachments), deduplicated by _sh_import_key.
 * Sources live outside the repo: Unesco/design/assets (images, by Figma imageRef) and
 * Unesco/media/out (videos/posters, from content/media-manifest.json).
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
 * Video, HEVC fallback and poster of a manifest entry.
 *
 * @param array<string, mixed> $manifest content/media-manifest.json.
 * @param string|null          $id       Manifest id.
 * @param string               $title    Title.
 * @return array{webm:?int,hevc:?int,poster:?int,has_alpha:?bool}
 */
function sh_imp_video( array $manifest, ?string $id, string $title ): array {
	$out = array(
		'webm'      => null,
		'hevc'      => null,
		'poster'    => null,
		'has_alpha' => null,
	);
	$entry = null;
	foreach ( (array) $manifest['files'] as $f ) {
		if ( null !== $id && isset( $f['id'] ) && $f['id'] === $id ) {
			$entry = $f;
		}
	}
	if ( null === $entry ) {
		return $out;
	}
	$dir   = sh_imp_config()['media'];
	$video = array(
		'width'  => isset( $entry['width'] ) ? (int) $entry['width'] : 0,
		'height' => isset( $entry['height'] ) ? (int) $entry['height'] : 0,
		'length' => isset( $entry['duration_s'] ) ? (int) round( (float) $entry['duration_s'] ) : 0,
	);
	$file  = static fn( string $p ): string => $dir . '/' . basename( $p );

	$out['webm']      = sh_imp_attachment( $file( (string) $entry['out'] ), 'media:' . basename( (string) $entry['out'] ), $title . ' video', $video );
	$out['poster']    = sh_imp_attachment( $file( (string) $entry['poster'] ), 'media:' . basename( (string) $entry['poster'] ), $title . ' poster' );
	$out['has_alpha'] = ! empty( $entry['has_alpha'] );
	$out['hevc']      = 0;
	// Only real video fallbacks (opaque files have type "poster-only" = the poster above).
	if ( ! empty( $entry['fallback']['file'] ) && isset( $entry['fallback']['type'] ) && 'hevc-alpha-mov' === $entry['fallback']['type'] ) {
		$fb          = (string) $entry['fallback']['file'];
		$out['hevc'] = sh_imp_attachment( $file( $fb ), 'media:' . basename( $fb ), $title . ' video hevc', $video );
	}
	return $out;
}
