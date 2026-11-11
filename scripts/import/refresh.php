<?php
/**
 * Importer: one-time refresh steps for sites imported by an older importer.
 *
 * Mode "missing" never rewrites existing entities, so a value the importer itself got wrong would stay wrong
 * forever. A refresh step fixes exactly the listed fields, once per site (ids kept in option
 * sh_import_refresh_done), and only where the admin has not chosen something else:
 *   a field is rewritten only when it is empty or still points to an attachment the importer created
 *   (has _sh_import_key). A file the admin uploaded/picked from the media library is never replaced.
 * Mode "overwrite" already rewrites the fields — only the file part of a step runs (attachments are never re-copied).
 * Step 2026-10-28-layout-refs has its own rule (text values, not attachments — see sh_import_refresh_layout_refs())
 * and is recorded as done only when nothing is left unresolved (a run without the crop files retries next time).
 * Re-run a step on purpose: SH_IMPORT_REFRESH=<id>[,<id>] (or "all").
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/refresh-2026-11.php'; // Figma 2026-11-11 round (core-hzd): own rules, see that file.

/**
 * Steps in order: id => what it fixes.
 *
 * @return array<string, string>
 */
function sh_import_refresh_steps(): array {
	return sh_import_refresh_steps_2026_11() + array(
		// core-import-refresh 2026-10-27: hero by manifest object_slug (+ still) · ICC → sRGB · intro map 39907d2c (P-150).
		'2026-10-27-media'       => 'object hero (manifest object_slug) · Home trails video · intro map · ICC to sRGB',
		// deploy-crops 2026-10-28: production import ran without the design-dev crop files (not in the deploy bundle).
		'2026-10-28-layout-refs' => 'object sh_layout *_ref still the seed file name → attachment id',
	);
}

/**
 * True when $id is 0/empty or an attachment made by the importer (safe to replace).
 *
 * @param mixed $id Current meta value.
 */
function sh_imp_replaceable( $id ): bool {
	$id = absint( $id );
	return 0 === $id || ( 'attachment' === get_post_type( $id ) && '' !== (string) get_post_meta( $id, '_sh_import_key', true ) );
}

/**
 * Write $fields (meta => value) of post $post_id where sh_imp_replaceable(); booleans follow $lead.
 *
 * @param int                  $post_id Post.
 * @param array<string, mixed> $fields  New values (attachment ids; bool = flag that follows $lead).
 * @param string               $lead    Field whose change also writes the boolean flags.
 * @param string               $kind    Counter name.
 */
function sh_imp_refresh_fields( int $post_id, array $fields, string $lead, string $kind ): void {
	$write = array();
	foreach ( $fields as $key => $value ) {
		if ( is_bool( $value ) || null === $value ) {
			continue;
		}
		$cur = get_post_meta( $post_id, $key, true );
		if ( absint( $cur ) !== (int) $value && sh_imp_replaceable( $cur ) ) {
			$write[ $key ] = (int) $value;
		}
	}
	if ( ! $write ) {
		return;
	}
	if ( isset( $write[ $lead ] ) ) {
		foreach ( $fields as $key => $value ) {
			if ( is_bool( $value ) ) {
				$write[ $key ] = $value;
			}
		}
	}
	sh_imp_meta( 'post', $post_id, $write );
	sh_imp_count( 'updated', $kind );
}

/**
 * Step 2026-10-27-media.
 *
 * @param array<string, mixed>                    $seed     Seed files.
 * @param array<string, mixed>                    $map      image-map.json.
 * @param array<string, mixed>                    $manifest media manifest.
 * @param array<string, int>                      $objects  slug => post id.
 * @param array<string, array{id:int,write:bool}> $pages    Pages.
 * @param bool                                    $fields   false = files only (mode overwrite wrote the fields).
 */
function sh_import_refresh_media( array $seed, array $map, array $manifest, array $objects, array $pages, bool $fields ): void {
	// ICC → sRGB on files the importer already copied (new copies are normalised in sh_imp_attachment()).
	sh_imp_find_attachment( '' ); // Warm the key cache.
	$s = sh_imp_state();
	foreach ( $s['att'] as $key => $att ) {
		$file = get_attached_file( $att );
		if ( ! str_starts_with( (string) $key, 'asset:' ) || ! is_string( $file ) || ! sh_imp_icc_normalize( $file, (string) get_post_mime_type( $att ) ) ) {
			continue;
		}
		$meta = wp_get_attachment_metadata( $att );
		if ( is_array( $meta ) && ! empty( $meta['sizes'] ) ) { // Sub-sizes were cut from the old file → redo them.
			require_once ABSPATH . 'wp-admin/includes/image.php';
			wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $file ) );
		}
		sh_imp_count( 'updated', 'icc_to_srgb' );
	}
	if ( ! $fields ) {
		return;
	}

	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		$id = isset( $objects[ $o['slug'] ] ) ? (int) $objects[ $o['slug'] ] : 0;
		// The video group (webm · hevc · poster · has_alpha) belongs together: an admin's own WebM keeps the whole group.
		$video_ok = $id && sh_imp_replaceable( get_post_meta( $id, 'sh_hero_video_webm', true ) );
		if ( ! $id || ( ! $video_ok && ! sh_imp_replaceable( get_post_meta( $id, 'sh_hero_image', true ) ) ) ) {
			continue; // Nothing the importer may touch: do not even create attachments.
		}
		$hero = sh_import_object_hero( $manifest, $map, $o );
		if ( ! $video_ok ) {
			$hero = array( 'sh_hero_image' => $hero['sh_hero_image'] );
		}
		sh_imp_refresh_fields( $id, $hero, 'sh_hero_video_webm', 'object_hero' );
	}

	$front = $pages['front']['id'];
	if ( $front && sh_imp_replaceable( get_post_meta( $front, 'sh_home_trails_video_webm', true ) ) ) {
		$video = sh_imp_home_trails_video( $manifest );
		sh_imp_refresh_fields(
			$front,
			array(
				'sh_home_trails_video_webm'   => $video['webm'],
				'sh_home_trails_video_poster' => $video['poster'],
			),
			'sh_home_trails_video_webm',
			'home_trails_video'
		);
	}

	$intro = $pages['intro']['id'];
	if ( $intro && sh_imp_replaceable( get_post_meta( $intro, 'sh_intro_map_image', true ) ) ) {
		sh_imp_refresh_fields( $intro, array( 'sh_intro_map_image' => sh_imp_image( $map, $map['pages']['intro']['map_image'], 'Map of Southeast Asia' ) ), 'sh_intro_map_image', 'intro_map' );
	}
}

/**
 * Value at $path inside $data (null = no such path).
 *
 * @param mixed                  $data Decoded JSON.
 * @param array<int, int|string> $path Keys.
 * @return mixed
 */
function sh_imp_path_get( $data, array $path ) {
	foreach ( $path as $k ) {
		if ( ! is_array( $data ) || ! array_key_exists( $k, $data ) ) {
			return null;
		}
		$data = $data[ $k ];
	}
	return $data;
}

/**
 * Set the value at an existing $path inside $data.
 *
 * @param array<int|string, mixed> $data  Decoded JSON (by reference).
 * @param array<int, int|string>   $path  Keys (must exist — check with sh_imp_path_get() first).
 * @param mixed                    $value New value.
 */
function sh_imp_path_set( array &$data, array $path, $value ): void {
	$node = &$data;
	foreach ( $path as $k ) {
		$node = &$node[ $k ];
	}
	$node = $value;
	unset( $node );
}

/**
 * Step 2026-10-28-layout-refs: an object whose sh_layout still holds a seed "*_ref" value as text (the import had no
 * file for it) gets the attachment id now that the file is there. Only a value that is exactly the seed text at the
 * same place (same key path in the Final frame style) is touched — anything an admin changed (other text · an id ·
 * a removed/moved key · invalid JSON) stays as it is, and the rest of the layout JSON is kept as stored.
 * Runs in every mode: overwrite already writes ids for files that exist, so it only finds what is still missing.
 *
 * @param array<string, mixed> $seed    Seed files.
 * @param array<string, mixed> $map     image-map.json.
 * @param array<string, int>   $objects slug => post id.
 * @return int Values still text because the file is missing (each one is also an import warning).
 */
function sh_import_refresh_layout_refs( array $seed, array $map, array $objects ): int {
	$left = 0;
	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		$id     = isset( $objects[ $o['slug'] ] ) ? (int) $objects[ $o['slug'] ] : 0;
		$layout = $id ? json_decode( (string) get_post_meta( $id, 'sh_layout', true ), true ) : null;
		if ( ! is_array( $layout ) ) {
			continue;
		}
		$changed = 0;
		foreach ( sh_import_style_refs( (array) sh_import_final_frame( $o )['style'] ) as $ref ) {
			list( $path, $value ) = $ref;
			if ( sh_imp_path_get( $layout, $path ) !== $value ) {
				continue; // Already an id, or changed by an admin.
			}
			$att = sh_imp_asset_ref( $map, $value, (string) $o['title'] . ' ' . substr( (string) end( $path ), 0, -4 ) );
			if ( ! $att ) {
				++$left;
				continue;
			}
			sh_imp_path_set( $layout, $path, $att );
			++$changed;
			sh_imp_count( 'updated', 'layout_ref' );
		}
		if ( $changed ) {
			sh_imp_meta( 'post', $id, array( 'sh_layout' => (string) wp_json_encode( $layout, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
			sh_imp_count( 'updated', 'object_layout' );
		}
	}
	return $left;
}

/**
 * Run the refresh steps not yet applied on this site. Returns the ids run now.
 *
 * @param array<string, mixed>                    $seed     Seed files.
 * @param array<string, mixed>                    $map      image-map.json.
 * @param array<string, mixed>                    $manifest media manifest.
 * @param array<string, int>                      $objects  slug => post id.
 * @param array<string, array{id:int,write:bool}> $pages    Pages.
 * @return string[]
 */
function sh_import_refresh( array $seed, array $map, array $manifest, array $objects, array $pages ): array {
	$cfg   = sh_imp_config();
	$done  = get_option( 'sh_import_refresh_done', array() );
	$done  = is_array( $done ) ? $done : array();
	$force = array_filter( array_map( 'trim', explode( ',', (string) ( defined( 'SH_IMPORT_REFRESH' ) ? constant( 'SH_IMPORT_REFRESH' ) : getenv( 'SH_IMPORT_REFRESH' ) ) ) ) );
	$ran   = array();
	foreach ( array_keys( sh_import_refresh_steps() ) as $step ) {
		if ( isset( $done[ $step ] ) && ! in_array( $step, $force, true ) && ! in_array( 'all', $force, true ) ) {
			continue;
		}
		if ( array_key_exists( $step, sh_import_refresh_steps_2026_11() ) ) {
			$ran[] = $step;
			if ( sh_import_refresh_2026_11( $step, $seed, $map, $manifest, $objects, $pages ) ) {
				$done[ $step ] = gmdate( 'c' );
			} else {
				unset( $done[ $step ] ); // Files / seed data still missing: try again on the next import.
			}
			continue;
		}
		if ( '2026-10-27-media' === $step ) {
			sh_import_refresh_media( $seed, $map, $manifest, $objects, $pages, 'overwrite' !== $cfg['mode'] );
		}
		$ran[] = $step;
		if ( '2026-10-28-layout-refs' === $step && sh_import_refresh_layout_refs( $seed, $map, $objects ) > 0 ) {
			unset( $done[ $step ] ); // Files still missing: try again on the next import.
			continue;
		}
		$done[ $step ] = gmdate( 'c' );
	}
	update_option( 'sh_import_refresh_done', $done, false );
	return $ran;
}
