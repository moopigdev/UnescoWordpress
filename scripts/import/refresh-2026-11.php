<?php
/**
 * Refresh steps of the Figma 2026-11-11 round (core-hzd · D37 D38 D40 · BACKLOG #5 #7 #8 #13 #16 #19) for sites
 * imported before it (production). Rules — an admin's edit is never overwritten:
 *   existing field → replaced only while its value is still one an older import wrote (legacy-values.json, built from
 *                    the git history of the seed by build-legacy-values.py · exact, modulo the encoding WordPress
 *                    stores: the registered sanitize / HTML entities)
 *   new field      → filled only while empty (true/false: only while never saved)
 *   file           → the importer's own attachment is rewritten in place only while its bytes are an older import's
 * A step that needs a file that is missing (or seed data that is not there yet) is not recorded as done: it runs again
 * on the next import. Every step runs in every mode (overwrite already writes the new values → nothing to change).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/content-2026-11.php';

/**
 * Steps of this file: id => what it changes (merged into sh_import_refresh_steps()).
 *
 * @return array<string, string>
 */
function sh_import_refresh_steps_2026_11(): array {
	return array(
		'2026-11-11-zones'         => 'zone 1–3 name · card title · banner (only old import values) · intro / full text (only empty)',
		'2026-11-11-objects'       => 'object title · title_raw · caption · detail rows (only old import values · slug unchanged)',
		'2026-11-11-chooser'       => 'zone 4 own page (link to Timeline removed) · chooser background / zone bar · trail chooser images (only empty)',
		'2026-11-11-zone5'         => 'zone 5 has a page (D40) · page images / text / coming-soon (only empty) — waits for the seed',
		'2026-11-11-media-replace' => 'media/out files changed under the same name (walking.webm v2): importer copy rewritten in place',
	);
}

/**
 * legacy-values.json (cached).
 *
 * @return array<string, mixed>
 */
function sh_imp_legacy(): array {
	static $data = null;
	if ( null === $data ) {
		$data = sh_imp_json( __DIR__ . '/legacy-values.json' );
	}
	return $data;
}

/**
 * Field definition of a meta key in a group (null = not a registered field, e.g. post_title).
 *
 * @param string $group Group id.
 * @param string $key   Meta key.
 * @return array<string, mixed>|null
 */
function sh_imp_field_def( string $group, string $key ): ?array {
	foreach ( sh_core_group_fields( $group ) as $f ) {
		if ( $key === $f['name'] ) {
			return $f;
		}
	}
	return null;
}

/**
 * Import key of the attachment a seed asset value becomes ('asset:' + file without extension · '' = no file).
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $value Seed value.
 */
function sh_imp_seed_asset_key( array $map, string $value ): string {
	$file = sh_import_seed_asset_file( $map, $value, sh_imp_config()['assets'] );
	return '' === $file ? '' : 'asset:' . preg_replace( '/\.[A-Za-z0-9]+$/', '', $file );
}

/**
 * Same text as stored by WordPress: identical, identical after the field's sanitize, or identical HTML-decoded
 * (term names / titles keep "&" as "&amp;").
 *
 * @param mixed                     $current Stored value.
 * @param string                    $value   Seed value.
 * @param array<string, mixed>|null $field   Field definition (null = no sanitize).
 */
function sh_imp_same_text( $current, string $value, ?array $field ): bool {
	if ( ! is_string( $current ) ) {
		return false;
	}
	if ( $current === $value || ( null !== $field && $current === sh_core_sanitize( $field, $value ) ) ) {
		return true;
	}
	$dec = static fn( string $s ): string => html_entity_decode( $s, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return $dec( $current ) === $dec( $value );
}

/**
 * True when $current is one of the values an older import wrote.
 *
 * @param mixed                     $current Stored value.
 * @param mixed                     $olds    legacy-values list.
 * @param array<string, mixed>|null $field   Field definition.
 */
function sh_imp_was_imported( $current, $olds, ?array $field ): bool {
	foreach ( (array) $olds as $old ) {
		if ( is_string( $old ) && sh_imp_same_text( $current, $old, $field ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Write a term text field (or the name) when the current value is an old import value and differs from the new one.
 *
 * @param WP_Term $term  Term.
 * @param string  $key   Meta key or "name".
 * @param string  $value New value.
 * @param mixed   $olds  legacy-values list.
 */
function sh_imp_refresh_term_text( WP_Term $term, string $key, string $value, $olds ): void {
	$field   = 'name' === $key ? null : sh_imp_field_def( 'zone', $key );
	$current = 'name' === $key ? $term->name : get_term_meta( $term->term_id, $key, true );
	if ( sh_imp_same_text( $current, $value, $field ) ) {
		return;
	}
	if ( ! sh_imp_was_imported( $current, $olds, $field ) ) {
		sh_imp_count( 'skipped', 'kept_admin_value' );
		return;
	}
	if ( 'name' === $key ) {
		$res = wp_update_term(
			$term->term_id,
			$term->taxonomy,
			wp_slash(
				array(
					'name' => $value,
					'slug' => $term->slug,
				)
			)
		);
		if ( is_wp_error( $res ) ) {
			throw new RuntimeException( $term->taxonomy . ' ' . $term->slug . ': ' . $res->get_error_message() );
		}
	} else {
		sh_imp_meta( 'term', $term->term_id, array( $key => $value ) );
	}
	sh_imp_count( 'updated', 'zone_' . $key );
}

/**
 * Fill empty term/post fields (texts, images) · true/false only when never saved. null values = nothing in the seed.
 *
 * @param string               $type   term|post.
 * @param int                  $id     Object id.
 * @param array<string, mixed> $fields key => value.
 * @param string               $kind   Counter.
 * @return int Values that could not be filled (missing file = 0 attachment).
 */
function sh_imp_fill_empty( string $type, int $id, array $fields, string $kind ): int {
	$left  = 0;
	$write = array();
	foreach ( $fields as $key => $value ) {
		if ( null === $value ) {
			continue;
		}
		if ( is_bool( $value ) ) {
			if ( ! metadata_exists( $type, $id, $key ) ) {
				$write[ $key ] = $value;
			}
			continue;
		}
		$current = 'term' === $type ? get_term_meta( $id, $key, true ) : get_post_meta( $id, $key, true );
		if ( '' !== (string) $current && 0 !== $current && '0' !== $current ) {
			continue;
		}
		if ( is_int( $value ) && 0 === $value ) {
			++$left; // Image whose file is missing (already a warning).
			continue;
		}
		if ( '' !== $value ) {
			$write[ $key ] = $value;
		}
	}
	if ( $write ) {
		sh_imp_meta( $type, $id, $write );
		sh_imp_count( 'updated', $kind );
	}
	return $left;
}

/**
 * Step 2026-11-11-zones (D38 · BACKLOG #7 #8): zone name + card title + banner while still the old import value;
 * intro / full text while empty.
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $map  image-map.json.
 * @return int Files missing.
 */
function sh_import_refresh_zones( array $seed, array $map ): int {
	$legacy = sh_imp_legacy();
	$left   = 0;
	foreach ( (array) $seed['zones']['zones'] as $z ) {
		$n    = (int) $z['number'];
		$term = get_term_by( 'slug', 'zone-' . $n, 'zone' );
		$old  = isset( $legacy['zones'][ (string) $n ] ) ? $legacy['zones'][ (string) $n ] : array();
		if ( ! $term instanceof WP_Term || sh_import_chooser_zone( $seed ) === $n ) {
			continue; // The chooser zone: step 2026-11-11-chooser.
		}
		sh_imp_refresh_term_text( $term, 'name', (string) $z['name'], isset( $old['name'] ) ? $old['name'] : array() );
		sh_imp_refresh_term_text( $term, 'sh_card_title', (string) ( isset( $z['name_raw'] ) ? $z['name_raw'] : $z['name'] ), isset( $old['sh_card_title'] ) ? $old['sh_card_title'] : array() );
		if ( isset( $z['banner_image']['file'] ) ) {
			$cur = absint( get_term_meta( $term->term_id, 'sh_cover_image', true ) );
			$key = $cur ? (string) get_post_meta( $cur, '_sh_import_key', true ) : '';
			if ( $cur && in_array( $key, (array) ( isset( $old['sh_cover_image'] ) ? $old['sh_cover_image'] : array() ), true ) ) {
				$att = sh_imp_seed_asset( $map, $z['banner_image']['file'], 'Zone ' . $n . ' banner', isset( $z['name_banner'] ) ? (string) $z['name_banner'] : '' );
				if ( $att ) {
					sh_imp_meta( 'term', $term->term_id, array( 'sh_cover_image' => $att ) );
					sh_imp_count( 'updated', 'zone_sh_cover_image' );
				} else {
					++$left;
				}
			} elseif ( $cur && sh_imp_seed_asset_key( $map, (string) $z['banner_image']['file'] ) !== $key ) {
				sh_imp_count( 'skipped', 'kept_admin_value' ); // An admin's own image (or none: 0 stays 0).
			}
		}
		sh_imp_fill_empty(
			'term',
			$term->term_id,
			array(
				'sh_intro'      => isset( $z['intro_short'] ) ? sh_import_blank_line_paragraphs( $z['intro_short'] ) : null,
				'sh_intro_full' => isset( $z['body_full'] ) ? sh_import_blank_line_paragraphs( $z['body_full'] ) : null,
			),
			'zone_intro'
		);
	}
	return $left;
}

/**
 * Step 2026-11-11-objects (D40 · BACKLOG #12 #19): title / title_raw / caption / detail rows of each in-scope object
 * → the current seed, only while the stored value is an old import value. The slug (post_name) is never changed.
 *
 * @param array<string, mixed> $seed    Seed files.
 * @param array<string, int>   $objects slug => post id.
 */
function sh_import_refresh_objects( array $seed, array $objects ): void {
	$legacy = sh_imp_legacy();
	$text   = array(
		'sh_collection'          => 'collection',
		'sh_thematic_section'    => 'thematic_section',
		'sh_period'              => 'period',
		'sh_place_of_production' => 'place_of_production',
		'sh_material'            => 'material',
		'sh_dimensions'          => 'dimensions',
		'sh_local_name'          => 'local_name',
		'sh_object_biography'    => 'object_biography',
	);
	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		$slug = (string) $o['slug'];
		$id   = isset( $objects[ $slug ] ) ? (int) $objects[ $slug ] : 0;
		$old  = isset( $legacy['objects'][ $slug ] ) ? $legacy['objects'][ $slug ] : null;
		$post = $id ? get_post( $id ) : null;
		if ( ! $post instanceof WP_Post || ! is_array( $old ) ) {
			continue;
		}
		$new = sh_import_object_text_2026_11( $o );
		foreach ( $text as $key => $k ) {
			$new[ $key ] = isset( $o[ $k ] ) && is_string( $o[ $k ] ) ? $o[ $k ] : '';
		}
		$write = array();
		foreach ( $new as $key => $value ) {
			$field   = 'post_title' === $key ? null : sh_imp_field_def( 'object', $key );
			$current = 'post_title' === $key ? $post->post_title : get_post_meta( $id, $key, true );
			if ( sh_imp_same_text( $current, $value, $field ) ) {
				continue;
			}
			if ( ! sh_imp_was_imported( $current, isset( $old[ $key ] ) ? $old[ $key ] : array(), $field ) ) {
				sh_imp_count( 'skipped', 'kept_admin_value' );
				continue;
			}
			$write[ $key ] = $value;
		}
		if ( isset( $write['post_title'] ) ) {
			$res = wp_update_post(
				wp_slash(
					array(
						'ID'         => $id,
						'post_title' => $write['post_title'],
						'post_name'  => $post->post_name, // Links keep working (D40).
					)
				),
				true
			);
			if ( is_wp_error( $res ) ) {
				throw new RuntimeException( 'object ' . $slug . ': ' . $res->get_error_message() );
			}
			unset( $write['post_title'] );
			sh_imp_count( 'updated', 'object_title' );
		}
		if ( $write ) {
			sh_imp_meta( 'post', $id, $write );
			sh_imp_count( 'updated', 'object_text' );
		}
	}
}

/**
 * Step 2026-11-11-chooser (D37 · BACKLOG #13): the chooser zone gets its own page — its link to the Timeline page
 * (what the import wrote) is removed · background / zone bar and each trail's chooser image fill empty fields.
 *
 * @param array<string, mixed>                    $seed  Seed files.
 * @param array<string, mixed>                    $map   image-map.json.
 * @param array<string, array{id:int,write:bool}> $pages Pages.
 * @return int Files missing.
 */
function sh_import_refresh_chooser( array $seed, array $map, array $pages ): int {
	$n = sh_import_chooser_zone( $seed );
	if ( ! $n ) {
		return 0;
	}
	$left = 0;
	$term = get_term_by( 'slug', 'zone-' . $n, 'zone' );
	if ( $term instanceof WP_Term ) {
		$timeline = isset( $pages['timeline']['id'] ) ? (int) $pages['timeline']['id'] : 0;
		$link     = absint( get_term_meta( $term->term_id, 'sh_link_page', true ) );
		if ( $link && $link === $timeline ) {
			sh_imp_meta( 'term', $term->term_id, array( 'sh_link_page' => 0 ) );
			sh_imp_count( 'updated', 'zone_sh_link_page' );
		} elseif ( $link ) {
			sh_imp_count( 'skipped', 'kept_admin_value' );
		}
		$ch    = $seed['zone4-chooser']['_meta'];
		$left += sh_imp_fill_empty(
			'term',
			$term->term_id,
			array(
				'sh_cover_image'  => ! absint( get_term_meta( $term->term_id, 'sh_cover_image', true ) ) ? sh_imp_seed_asset( $map, isset( $ch['cover_bg']['file_cropped'] ) ? $ch['cover_bg']['file_cropped'] : null, 'Zone ' . $n . ' choose an animal background' ) : null,
				'sh_banner_image' => ! absint( get_term_meta( $term->term_id, 'sh_banner_image', true ) ) ? sh_imp_seed_asset( $map, isset( $ch['zone_bar']['file_cropped'] ) ? $ch['zone_bar']['file_cropped'] : null, 'Zone ' . $n . ' zone bar' ) : null,
			),
			'zone_chooser'
		);
	}
	foreach ( (array) $seed['zone4-chooser']['animals'] as $a ) {
		$slug  = isset( $a['trail_slug'] ) ? (string) $a['trail_slug'] : '';
		$trail = '' !== $slug ? get_term_by( 'slug', $slug, 'trail' ) : false;
		if ( ! $trail instanceof WP_Term || absint( get_term_meta( $trail->term_id, 'sh_chooser_image', true ) ) ) {
			continue; // Not on this site, or already set (by the import or an admin).
		}
		$left += sh_imp_fill_empty( 'term', $trail->term_id, array( 'sh_chooser_image' => sh_import_trail_chooser_image( $seed, $map, $slug, $trail->name ) ), 'trail_chooser_image' );
	}
	return $left;
}

/**
 * Step 2026-11-11-zone5 (D40 · BACKLOG #16): a zone the seed now gives a page (has_page true + "page") → sh_has_page
 * true (the import wrote false) · page images / intro / coming-soon flags fill empty fields.
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $map  image-map.json.
 * @return int 1 + files missing while the seed has no page for a zone without one (step waits); 0 = done.
 */
function sh_import_refresh_zone_pages( array $seed, array $map ): int {
	$left = 0;
	foreach ( (array) $seed['zones']['zones'] as $z ) {
		$n    = (int) $z['number'];
		$term = get_term_by( 'slug', 'zone-' . $n, 'zone' );
		if ( 5 !== $n || ! $term instanceof WP_Term ) {
			continue; // D40: Zone 5 main page (zones 1–4: other steps).
		}
		if ( empty( $z['has_page'] ) || empty( $z['page'] ) ) {
			++$left; // Seed has no Zone 5 page yet (P-253): run again on the next import.
			continue;
		}
		if ( ! get_term_meta( $term->term_id, 'sh_has_page', true ) ) {
			sh_imp_meta( 'term', $term->term_id, array( 'sh_has_page' => true ) );
			sh_imp_count( 'updated', 'zone_sh_has_page' );
		}
		$fields = array( 'sh_intro' => isset( $z['intro_short'] ) ? sh_import_blank_line_paragraphs( $z['intro_short'] ) : null );
		foreach ( sh_import_zone_page_images( $z ) as $key => $value ) {
			$fields[ $key ] = absint( get_term_meta( $term->term_id, $key, true ) ) ? null : sh_imp_seed_asset( $map, $value, 'Zone ' . $n . ' ' . str_replace( array( 'sh_page_', 'sh_', '_' ), array( '', '', ' ' ), $key ) );
		}
		$left += sh_imp_fill_empty( 'term', $term->term_id, $fields + sh_import_zone_page_flags( $z ), 'zone_page' );
	}
	return $left;
}

/**
 * Step 2026-11-11-media-replace (BACKLOG #5): a media/out file that changed under the same name (walking.webm v2).
 * sh_imp_attachment() finds the old attachment by its key and never copies again — so the file of the importer's
 * attachment is rewritten in place (same id, URL, fields) while its bytes are an older import's (sha256 in
 * legacy-values.json "media"). A file an admin replaced (other bytes) stays. Poster unchanged.
 *
 * @param array<string, mixed> $manifest media manifest.
 * @return int Files that could not be checked (source missing).
 */
function sh_import_refresh_media_files( array $manifest ): int {
	$left = 0;
	$dir  = sh_imp_config()['media'];
	foreach ( (array) ( isset( sh_imp_legacy()['media'] ) ? sh_imp_legacy()['media'] : array() ) as $key => $old ) {
		$att  = sh_imp_find_attachment( (string) $key );
		$name = basename( substr( (string) $key, strlen( 'media:' ) ) );
		$dest = $att ? get_attached_file( $att ) : false;
		if ( ! $att || ! is_string( $dest ) || ! is_file( $dest ) ) {
			continue; // Not imported on this site (a fresh import copies the new file).
		}
		$src = $dir . '/' . $name;
		if ( ! is_readable( $src ) ) {
			sh_imp_warn( 'missing file: ' . $src . ' (media replace)' );
			++$left;
			continue;
		}
		$cur = (string) hash_file( 'sha256', $dest );
		if ( hash_file( 'sha256', $src ) === $cur ) {
			continue;
		}
		if ( ! in_array( $cur, (array) $old, true ) ) {
			sh_imp_count( 'skipped', 'kept_admin_file' );
			continue;
		}
		if ( ! copy( $src, $dest ) ) {
			throw new RuntimeException( 'copy failed: ' . $src . ' → ' . $dest );
		}
		$entry = null;
		foreach ( (array) ( isset( $manifest['files'] ) ? $manifest['files'] : array() ) as $f ) {
			if ( is_array( $f ) && isset( $f['out'] ) && basename( (string) $f['out'] ) === $name ) {
				$entry = $f;
			}
		}
		$video = array(
			'width'  => isset( $entry['width'] ) ? (int) $entry['width'] : 0,
			'height' => isset( $entry['height'] ) ? (int) $entry['height'] : 0,
			'length' => isset( $entry['duration_s'] ) ? (int) round( (float) $entry['duration_s'] ) : 0,
		);
		$meta = wp_get_attachment_metadata( $att );
		wp_update_attachment_metadata( $att, array_merge( is_array( $meta ) ? $meta : array(), sh_imp_attachment_meta( $att, $dest, (string) get_post_mime_type( $att ), $video ) ) );
		clean_post_cache( $att );
		sh_imp_count( 'updated', 'media_file' );
	}
	return $left;
}

/**
 * Run one step of this file. Returns true when it is done (false = run again on the next import).
 *
 * @param string                                  $step     Step id.
 * @param array<string, mixed>                    $seed     Seed files.
 * @param array<string, mixed>                    $map      image-map.json.
 * @param array<string, mixed>                    $manifest media manifest.
 * @param array<string, int>                      $objects  slug => post id.
 * @param array<string, array{id:int,write:bool}> $pages    Pages.
 */
function sh_import_refresh_2026_11( string $step, array $seed, array $map, array $manifest, array $objects, array $pages ): bool {
	switch ( $step ) {
		case '2026-11-11-zones':
			return 0 === sh_import_refresh_zones( $seed, $map );
		case '2026-11-11-objects':
			sh_import_refresh_objects( $seed, $objects );
			return true;
		case '2026-11-11-chooser':
			return 0 === sh_import_refresh_chooser( $seed, $map, $pages );
		case '2026-11-11-zone5':
			return 0 === sh_import_refresh_zone_pages( $seed, $map );
		case '2026-11-11-media-replace':
			return 0 === sh_import_refresh_media_files( $manifest );
	}
	return true;
}
