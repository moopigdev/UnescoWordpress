<?php
/**
 * Refresh steps of the 2026-11-17 round (core-1117 · D47–D53) for sites imported before it (production: 15 elephant
 * objects · timeline · 17 trail terms · zones · pages · options · menus — UNESCO may have edited any of it).
 * Rule: **add only what is missing, never overwrite an admin's value** —
 *   objects        missing ones are created (generic 16-trail records · hold_* as draft); an existing object only
 *                  gets new/empty fields filled (scientific name · tiles in other trails …) — title/text never touched
 *   trails         term description / H1 / cover / layout / tiles without a page / guide card: filled only while empty ·
 *                  sh_object_order replaced only while it is exactly the list an older import wrote (elephant objects
 *                  of that trail in seed order) — an admin's order or selection is kept
 *   pages          Home animal-guide fields · Introduction 4-chapter fields: filled only while empty
 *   site           header/footer logo options + footer text: set only when never set / empty · Primary menu: "About the
 *                  Project" appended when no item has that title or URL (nothing removed, renamed or reordered)
 * Everything left alone because an admin changed it is listed in the report "kept_admin" (+ skipped.kept_admin_value).
 * A step whose files are missing (a new warning during the step) is not recorded as done: it runs again next time.
 * Works with SH_IMPORT_ONLY=refresh (deploy.sh --refresh-only) and on a fresh import (finds everything already set).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/content-2026-11-17.php';

/**
 * Steps of this file: id => what it changes (merged into sh_import_refresh_steps()).
 *
 * @return array<string, string>
 */
function sh_import_refresh_steps_2026_11_17(): array {
	return array(
		'2026-11-17-objects' => 'objects of the 16 other trails created when missing (hold_* = draft) · new fields of existing objects filled only where empty',
		'2026-11-17-trails'  => 'trail description / H1 / cover / layout / tiles without a page / animal guide card (only empty) · object order (only while the old import list)',
		'2026-11-17-pages'   => 'Home animal guide section · Introduction 4 chapters (only empty fields)',
		'2026-11-17-site'    => 'header logo · footer logos / text (only never set) · Primary menu "About the Project" (only when missing)',
		// core-1117b (P-340): sh_trail_layout gains "grid" (tile grid box) — sites imported by core-1117 have it without.
		'2026-11-17-trail-grid' => 'trail sh_trail_layout + grid box (only while empty or still the layout the core-1117 import wrote)',
		// makara-1117 (BACKLOG #33): Makara tiles 1 / 3 get a page — sites imported before show them as "tiles without a page".
		'2026-11-17-makara'     => 'Makara: objects saltwater-crocodile-skull + sungai-mas-makara created when missing · their old tile-without-a-page removed (only the one the import wrote) · trail order (only while the old list)',
		// rooster-1117 (BACKLOG #34): Rooster #11 = the multi-trail object mother-of-pearl-furniture-depicting-animals.
		'2026-11-17-rooster'    => 'Rooster #11 links to mother-of-pearl-furniture-depicting-animals: rooster term + its rooster tile added to the object (only where missing) · the old tile-without-a-page removed (only the one the import wrote) · trail order (only while the old list)',
		// fb-1117 (BACKLOG #37): the designer put the side image into the two Makara frames (the slot was empty).
		'2026-11-17-makara-side' => 'Makara 2: side panel image (sh_extra_image_1) + its layout box filled only while no panel image is set — an image an admin put there stays (kept_admin)',
		// hover-1117 (BACKLOG #35): the designer fixed the names inside two hover images (seashell #12 / #13).
		'2026-11-17-hover'      => 'trail tile hover image replaced where the seed has a new one (tile_image_hover_ref_prev) · only while the field is still the attachment the importer made from the old image',
		// fb-1117 (BACKLOG #39 · D57): Zone 1–3 pages show a grid of objects (numbered fields sh_zone_tile_<n>_* of the zone term).
		'2026-11-17-zone-grid'  => 'Zone 1–3 object grid: sh_zone_tile_<n>_object / _image / _image_hover filled from zones-2026-11-17.json only where empty (an admin value stays: kept_admin) · tile images = the attachments the objects already have',
		// fb-1117 (BACKLOG #36 · D58): belt buckle title as in Figma, every character.
		'2026-11-17-belt-title' => 'object pending-3 title (+ title_raw): "Pending (Traditional Belt Buckle)" → "Pending (traditional belt buckle)" only while the site still has the value the importer wrote (slug unchanged · an admin edit stays: kept_admin)',
		// multitrail-1117 (BACKLOG #41 · D59): an object in several trails opens in the look of the trail it was clicked from.
		'2026-11-17-multitrail' => 'trail versions of the multi-trail objects (sh_variant_<n>_*: hero video · colours · side images · layout · text that differs) added only where empty (an admin value stays: kept_admin) · zone grid tile trail (sh_zone_tile_<n>_trail) set only where empty and the tile still holds the seed object · D60: Đông Sơn drum of Seashell = its own object (new object created · seashell term off the old one · trail order · zone 3 tile → new object, only while still the importer\'s values) · nothing deleted',
	);
}

/**
 * Step 2026-11-17-trails.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_trails_1117( array $seed ): void {
	$ids = array();
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$id = sh_imp_find_post( 'object', 'object:' . $o['slug'] );
		if ( $id && 'publish' === get_post_status( $id ) ) {
			$ids[ (string) $o['slug'] ] = $id;
		}
	}
	foreach ( (array) $seed['trails']['trails'] as $t ) {
		if ( ! is_array( $t ) || ! empty( $t['not_a_trail'] ) ) {
			continue;
		}
		$slug = (string) $t['slug'];
		$term = get_term_by( 'slug', $slug, 'trail' );
		if ( ! $term instanceof WP_Term ) {
			continue; // No term on this site: the refresh creates no term.
		}
		if ( 'elephant' !== $slug && '' === trim( (string) $term->description ) && ! empty( $t['description'] ) ) {
			$res = wp_update_term( $term->term_id, 'trail', wp_slash( array( 'description' => (string) $t['description'] ) ) );
			if ( is_wp_error( $res ) ) {
				throw new RuntimeException( 'trail ' . $slug . ': ' . $res->get_error_message() );
			}
			sh_imp_count( 'updated', 'trail_description' );
		} elseif ( 'elephant' !== $slug && ! empty( $t['description'] ) && ! sh_imp_same_text( $term->description, (string) $t['description'], null ) ) {
			sh_imp_keep( "trail:$slug description (admin text kept)" );
		}
		$meta = sh_import_trail_meta_1117( $seed, $t );
		foreach ( array( 'sh_title_on_trail_page', 'sh_guide_name', 'sh_guide_words', 'sh_guide_follow' ) as $k ) {
			$cur = (string) get_term_meta( $term->term_id, $k, true );
			if ( isset( $meta[ $k ] ) && '' !== $cur && ! sh_imp_same_text( $cur, (string) $meta[ $k ], sh_imp_field_def( 'trail', $k ) ) ) {
				sh_imp_keep( "trail:$slug $k (site \"$cur\" · seed \"{$meta[ $k ]}\")" );
			}
		}
		sh_imp_fill_empty( 'term', $term->term_id, $meta, 'trail_1117' );

		// Object order: the full new list only while the stored one is exactly what an older import wrote.
		$new = array();
		$old = array();
		foreach ( sh_import_all_objects( $seed ) as $o ) {
			if ( isset( $o['trail_order'][ $slug ], $ids[ (string) $o['slug'] ] ) ) {
				$new[ $ids[ (string) $o['slug'] ] ] = (int) $o['trail_order'][ $slug ];
				if ( ! empty( $o['trail_order']['elephant'] ) ) {
					$old[ $ids[ (string) $o['slug'] ] ] = (int) $o['trail_order'][ $slug ];
				}
			}
		}
		asort( $new );
		asort( $old );
		$cur = get_term_meta( $term->term_id, 'sh_object_order', true );
		$cur = is_array( $cur ) ? array_values( array_map( 'intval', $cur ) ) : array();
		if ( array_keys( $new ) === $cur ) {
			continue;
		}
		if ( array_keys( $old ) === $cur ) {
			sh_imp_meta( 'term', $term->term_id, array( 'sh_object_order' => array_keys( $new ) ) );
			sh_imp_count( 'updated', 'trail_object_order' );
		} else {
			sh_imp_keep( "trail:$slug sh_object_order (admin order kept · new objects come after it, A–Z)" );
		}
	}
}

/**
 * Step 2026-11-17-pages: Home animal guide + Introduction 4 chapters, empty fields only.
 *
 * @param array<string, mixed>                    $seed     Seed files.
 * @param array<string, mixed>                    $manifest Manifest.
 * @param array<string, array{id:int,write:bool}> $pages    Pages.
 */
function sh_import_refresh_pages_1117( array $seed, array $manifest, array $pages ): void {
	$front = isset( $pages['front']['id'] ) ? (int) $pages['front']['id'] : 0;
	if ( $front ) {
		sh_imp_fill_empty( 'post', $front, sh_import_home_meta_1117( $seed ), 'home_1117' );
	}
	$intro = isset( $pages['intro']['id'] ) ? (int) $pages['intro']['id'] : 0;
	if ( $intro ) {
		sh_imp_fill_empty( 'post', $intro, sh_import_intro_meta_1117( $seed, $manifest ), 'intro_1117' );
	}
}

/**
 * Step 2026-11-17-site: options never set (or empty) + the About the Project menu item.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_site_1117( array $seed ): void {
	foreach ( sh_import_site_options_1117( $seed ) as $name => $value ) {
		$cur = get_option( $name, false );
		if ( false === $cur || '' === $cur || 0 === $cur || '0' === $cur ) {
			if ( ! ( is_int( $value ) && 0 === $value ) && update_option( $name, $value ) ) { // false = already that (registered default).
				sh_imp_count( 'updated', 'site_option' );
			}
		} elseif ( (string) $cur !== (string) $value && ! is_int( $value ) ) {
			sh_imp_keep( "option $name (site \"$cur\")" );
		}
	}
	sh_import_menu_1117( $seed );
}

/**
 * Step 2026-11-17-trail-grid (P-340): sh_trail_layout → seed layout + "grid" while it is empty or still exactly the
 * layout the core-1117 import wrote (seed trail_page_layout, compared with refs.php sh_import_layout_fingerprint():
 * key order / number format ignored). An admin's layout is kept (kept_admin). Elephant has no layout (untouched).
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_trail_grid_1117( array $seed ): void {
	foreach ( (array) $seed['trails']['trails'] as $t ) {
		if ( ! is_array( $t ) || ! empty( $t['not_a_trail'] ) || 'elephant' === $t['slug'] ) {
			continue;
		}
		$new  = sh_import_trail_layout_1117( $t );
		$term = get_term_by( 'slug', (string) $t['slug'], 'trail' );
		if ( null === $new || ! $term instanceof WP_Term ) {
			continue;
		}
		$raw = (string) get_term_meta( $term->term_id, 'sh_trail_layout', true );
		$cur = json_decode( $raw, true );
		if ( '' !== trim( $raw ) && ! is_array( $cur ) ) {
			sh_imp_keep( "trail:{$t['slug']} sh_trail_layout (not JSON — admin value kept)" );
			continue;
		}
		if ( is_array( $cur ) && sh_import_layout_fingerprint( $cur ) === sh_import_layout_fingerprint( $new ) ) {
			continue; // Already the current layout.
		}
		if ( is_array( $cur ) && sh_import_layout_fingerprint( $cur ) !== sh_import_layout_fingerprint( (array) $t['trail_page_layout'] ) ) {
			sh_imp_keep( "trail:{$t['slug']} sh_trail_layout (edited in admin — no grid added)" );
			continue;
		}
		sh_imp_meta( 'term', $term->term_id, array( 'sh_trail_layout' => (string) wp_json_encode( $new, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
		sh_imp_count( 'updated', 'trail_layout_grid' );
	}
}

/** Makara tiles 1 / 3 (makara-1117): slug => [position, label the core-1117 import wrote on the tile without a page]. */
const SH_IMPORT_MAKARA_1117 = array(
	'saltwater-crocodile-skull' => array( 1, 'Skull of Saltwater Crocodile' ),
	'sungai-mas-makara'         => array( 3, 'Sungai Mas Makara' ),
);

/**
 * Step 2026-11-17-makara (BACKLOG #33): the two new Makara objects (created when missing, an object made in admin
 * with the slug is adopted and only filled where empty) · on the Makara trail the "tile without a page" the earlier
 * import made at their position is removed (only when position + label are still what the import wrote — an admin's
 * tile stays) · sh_object_order gets the two only while it is still the old list of the other three (else the new ones
 * come after the admin order, A–Z).
 *
 * @param array<string, mixed> $seed     Seed files.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $manifest Manifest.
 */
function sh_import_refresh_makara_1117( array $seed, array $map, array $manifest ): void {
	$slugs = array_keys( SH_IMPORT_MAKARA_1117 );
	sh_import_objects_1117( $seed, $map, $manifest, true, $slugs );
	$term = get_term_by( 'slug', 'makara', 'trail' );
	if ( ! $term instanceof WP_Term ) {
		return; // No Makara trail on this site: the refresh creates no term.
	}
	$new = array();
	$old = array();
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$id = sh_imp_find_post( 'object', 'object:' . $o['slug'] );
		if ( ! $id || 'publish' !== get_post_status( $id ) || ! isset( $o['trail_order']['makara'] ) ) {
			continue;
		}
		$new[ $id ] = (int) $o['trail_order']['makara'];
		if ( ! in_array( (string) $o['slug'], $slugs, true ) ) {
			$old[ $id ] = (int) $o['trail_order']['makara'];
		}
	}
	asort( $new );
	asort( $old );

	// Tiles without a page of the two positions (sh_static_tile_1..3).
	for ( $n = 1; $n <= 3; $n++ ) {
		$pos   = (int) get_term_meta( $term->term_id, "sh_static_tile_{$n}_position", true );
		$label = (string) get_term_meta( $term->term_id, "sh_static_tile_{$n}_label", true );
		foreach ( SH_IMPORT_MAKARA_1117 as $slug => $was ) {
			$obj = sh_imp_find_post( 'object', 'object:' . $slug );
			if ( $pos !== $was[0] || ! $obj || 'publish' !== get_post_status( $obj ) ) {
				continue;
			}
			if ( sh_imp_same_text( $label, $was[1], null ) ) {
				sh_imp_meta(
					'term',
					$term->term_id,
					array(
						"sh_static_tile_{$n}_position"    => '',
						"sh_static_tile_{$n}_image"       => '',
						"sh_static_tile_{$n}_image_hover" => '',
						"sh_static_tile_{$n}_label"       => '',
					)
				);
				sh_imp_count( 'updated', 'trail_static_tile_removed' );
			} else {
				sh_imp_keep( "trail:makara tile without a page $n (admin edited · position $pos \"$label\")" );
			}
		}
	}

	$cur = get_term_meta( $term->term_id, 'sh_object_order', true );
	$cur = is_array( $cur ) ? array_values( array_map( 'intval', $cur ) ) : array();
	if ( array_keys( $new ) === $cur ) {
		return;
	}
	if ( ! $cur || array_keys( $old ) === $cur ) {
		sh_imp_meta( 'term', $term->term_id, array( 'sh_object_order' => array_keys( $new ) ) );
		sh_imp_count( 'updated', 'trail_object_order' );
	} else {
		sh_imp_keep( 'trail:makara sh_object_order (admin order kept · new objects come after it, A–Z)' );
	}
}

/** Rooster #11 (rooster-1117): the object that is now the multi-trail object of the trail's last tile. */
const SH_IMPORT_ROOSTER_1117 = array(
	'slug'     => 'mother-of-pearl-furniture-depicting-animals',
	'trail'    => 'rooster',
	'position' => 11,
);

/**
 * Step 2026-11-17-rooster (BACKLOG #34 · P-360): Rooster tile #11 used to be a tile without a page (the record
 * "pending" held back as a draft) — it is now a link to the object mother-of-pearl-furniture-depicting-animals
 * (primary trail Seashell). Refresh-only, nothing of an admin overwritten, nothing created or deleted:
 *   object   the rooster term is added when missing (existing terms kept) · its rooster tile (sh_trail_tile_n) filled
 *            only into a slot that is that trail's or empty · every other field of the object left alone (an object
 *            not on the site — never imported / trashed — is not created)
 *   trail    the old tile without a page at position 11 removed only while position + label + both images are
 *            exactly what the import wrote (else kept_admin) · sh_object_order gets the object only while it is
 *            still the old list of the other 10 (else the object comes after the admin order, A–Z)
 *   "pending" stays a draft (not touched)
 *
 * @param array<string, mixed> $seed     Seed files.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $manifest Manifest.
 */
function sh_import_refresh_rooster_1117( array $seed, array $map, array $manifest ): void {
	unset( $map, $manifest );
	$slug = SH_IMPORT_ROOSTER_1117['slug'];
	$pos  = SH_IMPORT_ROOSTER_1117['position'];
	$term = get_term_by( 'slug', SH_IMPORT_ROOSTER_1117['trail'], 'trail' );
	$id   = sh_imp_find_post( 'object', 'object:' . $slug );
	$seed_o = null;
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		if ( $slug === (string) $o['slug'] ) {
			$seed_o = $o;
		}
	}
	if ( ! $term instanceof WP_Term || ! $id || ! is_array( $seed_o ) ) {
		return; // No Rooster trail / no such object on this site: the refresh creates neither.
	}

	// Object: rooster term + rooster tile (slot of that trail, else the first empty one of 4).
	if ( ! has_term( $term->term_id, 'trail', $id ) ) {
		wp_set_object_terms( $id, array( (int) $term->term_id ), 'trail', true );
		sh_imp_count( 'updated', 'object_trail_term' );
	}
	$tile  = sh_import_object_tiles( $seed_o )[ $term->slug ] ?? null;
	$title = sh_import_object_text_2026_11( $seed_o )['post_title'];
	$img   = null;
	$hover = null;
	if ( is_array( $tile ) ) {
		$img   = sh_imp_1117_image( $tile['image'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile(), "$title tile ({$term->slug})" );
		$hover = sh_imp_1117_image( $tile['hover'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover(), "$title tile hover ({$term->slug})" );
		$slot  = 0;
		for ( $n = 1; $n <= 4 && ! $slot; $n++ ) {
			if ( (int) get_post_meta( $id, "sh_trail_tile_{$n}_trail", true ) === (int) $term->term_id ) {
				$slot = $n;
			}
		}
		for ( $n = 1; $n <= 4 && ! $slot; $n++ ) {
			if ( '' === (string) get_post_meta( $id, "sh_trail_tile_{$n}_trail", true ) && '' === (string) get_post_meta( $id, "sh_trail_tile_{$n}_image", true ) && '' === (string) get_post_meta( $id, "sh_trail_tile_{$n}_image_hover", true ) ) {
				$slot = $n;
			}
		}
		if ( $slot ) {
			sh_imp_fill_empty(
				'post',
				$id,
				array(
					"sh_trail_tile_{$slot}_trail"       => (int) $term->term_id,
					"sh_trail_tile_{$slot}_image"       => $img,
					"sh_trail_tile_{$slot}_image_hover" => $hover,
				),
				'object_trail_tile'
			);
		} else {
			sh_imp_keep( "object:$slug rooster tile (4 trail-tile slots used by the admin — none added)" );
		}
	}

	// Trail: the old tile without a page of position 11 (sh_static_tile_1..3).
	if ( 'publish' === get_post_status( $id ) ) {
		for ( $n = 1; $n <= 3; $n++ ) {
			if ( (int) get_term_meta( $term->term_id, "sh_static_tile_{$n}_position", true ) !== $pos ) {
				continue;
			}
			$label = (string) get_term_meta( $term->term_id, "sh_static_tile_{$n}_label", true );
			$cur_i = (int) get_term_meta( $term->term_id, "sh_static_tile_{$n}_image", true );
			$cur_h = (int) get_term_meta( $term->term_id, "sh_static_tile_{$n}_image_hover", true );
			if ( '' === trim( $label ) && $img && $cur_i === $img && ( ! $hover || $cur_h === $hover ) ) {
				sh_imp_meta(
					'term',
					$term->term_id,
					array(
						"sh_static_tile_{$n}_position"    => '',
						"sh_static_tile_{$n}_image"       => '',
						"sh_static_tile_{$n}_image_hover" => '',
						"sh_static_tile_{$n}_label"       => '',
					)
				);
				sh_imp_count( 'updated', 'trail_static_tile_removed' );
			} else {
				sh_imp_keep( "trail:rooster tile without a page $n (admin edited · position $pos \"$label\")" );
			}
		}
	}

	// sh_object_order: the object joins the old list of the other tiles.
	$new = array();
	$old = array();
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$oid = sh_imp_find_post( 'object', 'object:' . $o['slug'] );
		if ( ! $oid || 'publish' !== get_post_status( $oid ) || ! isset( $o['trail_order'][ $term->slug ] ) ) {
			continue;
		}
		$new[ $oid ] = (int) $o['trail_order'][ $term->slug ];
		if ( $slug !== (string) $o['slug'] ) {
			$old[ $oid ] = (int) $o['trail_order'][ $term->slug ];
		}
	}
	asort( $new );
	asort( $old );
	$cur = get_term_meta( $term->term_id, 'sh_object_order', true );
	$cur = is_array( $cur ) ? array_values( array_map( 'intval', $cur ) ) : array();
	if ( array_keys( $new ) === $cur ) {
		return;
	}
	if ( ! $cur || array_keys( $old ) === $cur ) {
		sh_imp_meta( 'term', $term->term_id, array( 'sh_object_order' => array_keys( $new ) ) );
		sh_imp_count( 'updated', 'trail_object_order' );
	} else {
		sh_imp_keep( 'trail:rooster sh_object_order (admin order kept · the object comes after it, A–Z)' );
	}
}

/**
 * Step 2026-11-17-makara-side (fb-1117 · BACKLOG #37): the designer's side image of the two Makara objects
 * (style.images[0].crop_ref → sh_extra_image_1 · DESIGN-SPEC §8.6). A fresh import already has it; a site imported
 * before has the empty slot (layout image_slot_empty · no panel image). Rules — nothing of an admin overwritten:
 *   panel image   sh_extra_image_1..6 all empty → sh_extra_image_1 = the crop · already that crop = nothing to do ·
 *                 any other panel image (an admin put it there) → kept_admin, nothing written, no unused file created
 *   sh_layout     only when the panel image is the designer's (filled now or before) and the layout is still the empty-slot one
 *                 (image_slot_empty set · no images) → the seed layout (image placed at its box) · else kept as is
 * An object not on the site is not created (step 2026-11-17-makara does that).
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $map  image-map.json.
 */
function sh_import_refresh_makara_side_1117( array $seed, array $map ): void {
	$dir = (string) sh_imp_config()['assets'];
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$slug = (string) $o['slug'];
		if ( ! isset( SH_IMPORT_MAKARA_1117[ $slug ] ) ) {
			continue;
		}
		$id = sh_imp_find_post( 'object', 'object:' . $slug );
		if ( ! $id ) {
			continue;
		}
		$title  = sh_import_object_text_2026_11( $o )['post_title'];
		$frame  = sh_import_primary_frame( $o );
		$images = isset( $frame['style']['images'] ) && is_array( $frame['style']['images'] ) ? array_values( $frame['style']['images'] ) : array();
		$ref    = isset( $images[0]['crop_ref'] ) ? (string) $images[0]['crop_ref'] : '';
		if ( '' === $ref ) {
			continue; // The seed has no side image for it.
		}

		$current = array();
		for ( $i = 1; $i <= 6; $i++ ) {
			$att = absint( get_post_meta( $id, 'sh_extra_image_' . $i, true ) );
			if ( $att ) {
				$current[ $i ] = $att;
			}
		}
		// The attachment this step would write, found without creating it (key = asset:<file without extension>).
		$file   = sh_import_ref_file( $map, $ref, $dir );
		$ext    = '' === $file ? '' : (string) pathinfo( $file, PATHINFO_EXTENSION );
		$have   = '' === $file ? 0 : sh_imp_find_attachment( 'asset:' . ( '' === $ext ? $file : substr( $file, 0, -strlen( $ext ) - 1 ) ) );
		if ( $have && isset( $current[1] ) && $current[1] === $have ) {
			$att = $have; // Already the designer's image (fresh import · an earlier run): only the layout may still need it.
		} elseif ( $current ) {
			sh_imp_keep( "object:$slug sh_extra_image_" . (int) array_key_first( $current ) . ' (panel image set in admin · the designer side image not filled)' );
			continue;
		} else {
			$att = sh_imp_asset_ref( $map, $ref, $title . ' image' ); // Creates the attachment (a warning + 0 when the file is missing).
			if ( ! $att ) {
				continue;
			}
			sh_imp_meta( 'post', $id, array( 'sh_extra_image_1' => $att ) );
			sh_imp_count( 'updated', 'makara_side_image' );
		}

		$layout = json_decode( (string) get_post_meta( $id, 'sh_layout', true ), true );
		if ( is_array( $layout ) && ! empty( $layout['image_slot_empty'] ) && empty( $layout['images'] ) ) {
			sh_imp_meta( 'post', $id, array( 'sh_layout' => sh_import_layout( $frame, $map, $title ) ) );
			sh_imp_count( 'updated', 'makara_side_layout' );
		} elseif ( ! is_array( $layout ) || empty( $layout['images'] ) ) {
			sh_imp_keep( "object:$slug sh_layout (not the empty-slot layout the import wrote · image placed in the column box)" );
		} // Else the layout already places images (fresh import): nothing to do.
	}
}

/**
 * Hover swaps of the seed (hover-1117 · BACKLOG #35): every tile whose seed carries tile_image_hover_ref_prev (the
 * ref an older import used) next to the new tile_image_hover_ref. Generic — nothing is listed by slug. Where the
 * image lives follows the import (content-2026-11-17.php):
 *   object main trail   post meta sh_tile_image_hover (record-level hover · generic objects only)
 *   object other trail  post meta sh_trail_tile_<n>_image_hover of the slot whose sh_trail_tile_<n>_trail is that trail
 *   tile without a page term meta sh_static_tile_<n>_image_hover of the slot at that position (trails.json tiles)
 *
 * @param array<string, mixed> $seed Seed files.
 * @return array<int, array{type:string,find:array<string,mixed>,prev:string,new:string,title:string,what:string}>
 */
function sh_import_hover_swaps_1117( array $seed ): array {
	$out = array();
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$slug  = (string) $o['slug'];
		$title = sh_import_object_text_2026_11( $o )['post_title'];
		$main  = sh_import_object_main_trail( $o );
		if ( sh_import_object_generic( $o ) && ! empty( $o['tile_image_hover_ref_prev'] ) && ! empty( $o['tile_image_hover_ref'] ) ) {
			$out[] = array(
				'type'  => 'post',
				'find'  => array(
					'object' => $slug,
					'key'    => 'sh_tile_image_hover',
				),
				'prev'  => (string) $o['tile_image_hover_ref_prev'],
				'new'   => (string) $o['tile_image_hover_ref'],
				'title' => $title . ' tile hover',
				'what'  => "object:$slug sh_tile_image_hover",
			);
		}
		foreach ( (array) ( isset( $o['tiles_by_trail'] ) ? $o['tiles_by_trail'] : array() ) as $t => $v ) {
			if ( ! is_array( $v ) || empty( $v['tile_image_hover_ref_prev'] ) || empty( $v['tile_image_hover_ref'] ) ) {
				continue;
			}
			if ( (string) $t === $main ) {
				continue; // The main tile is the record-level one above (not a sh_trail_tile slot).
			}
			$out[] = array(
				'type'  => 'post',
				'find'  => array(
					'object' => $slug,
					'trail'  => (string) $t,
				),
				'prev'  => (string) $v['tile_image_hover_ref_prev'],
				'new'   => (string) $v['tile_image_hover_ref'],
				'title' => "$title tile hover ($t)",
				'what'  => "object:$slug tile of trail $t",
			);
		}
	}
	foreach ( (array) ( isset( $seed['trails']['trails'] ) ? $seed['trails']['trails'] : array() ) as $t ) {
		if ( ! is_array( $t ) || ! empty( $t['not_a_trail'] ) || 'elephant' === $t['slug'] ) {
			continue;
		}
		$static = array();
		foreach ( sh_import_trail_static_tiles( $seed, $t ) as $st ) {
			$static[ (int) $st['position'] ] = $st; // Only tiles without a page (not a link).
		}
		foreach ( (array) ( isset( $t['tiles'] ) ? $t['tiles'] : array() ) as $tile ) {
			$pos = isset( $tile['order'] ) ? (int) $tile['order'] : 0;
			if ( empty( $tile['tile_image_hover_ref_prev'] ) || empty( $tile['tile_image_hover_ref'] ) || ! isset( $static[ $pos ] ) ) {
				continue;
			}
			$out[] = array(
				'type'  => 'term',
				'find'  => array(
					'trail'    => (string) $t['slug'],
					'position' => $pos,
				),
				'prev'  => (string) $tile['tile_image_hover_ref_prev'],
				'new'   => (string) $tile['tile_image_hover_ref'],
				'title' => ( isset( $t['name'] ) ? (string) $t['name'] : (string) $t['slug'] ) . " tile $pos hover",
				'what'  => "trail:{$t['slug']} tile without a page $pos",
			);
		}
	}
	return $out;
}

/**
 * Step 2026-11-17-hover (BACKLOG #35): the hover image of a tile becomes the new attachment only while the field is
 * still exactly the attachment the importer made from the seed's previous ref (tile_image_hover_ref_prev). Anything
 * else — an image an admin picked, a cleared field — is kept (kept_admin). Old attachments are never deleted. A tile
 * that is not on this site (object / term / slot missing) is skipped; the new file missing = warning (step retried).
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_hover_1117( array $seed ): void {
	$trail_ids = sh_imp_trail_ids();
	foreach ( sh_import_hover_swaps_1117( $seed ) as $sw ) {
		$id   = 0;
		$key  = '';
		$find = $sw['find'];
		if ( 'post' === $sw['type'] ) {
			$id = sh_imp_find_post( 'object', 'object:' . $find['object'] );
			if ( $id && isset( $find['key'] ) ) {
				$key = (string) $find['key'];
			} elseif ( $id && isset( $trail_ids[ $find['trail'] ] ) ) {
				for ( $n = 1; $n <= 4 && '' === $key; $n++ ) {
					if ( (int) get_post_meta( $id, "sh_trail_tile_{$n}_trail", true ) === $trail_ids[ $find['trail'] ] ) {
						$key = "sh_trail_tile_{$n}_image_hover";
					}
				}
			}
		} else {
			$term = get_term_by( 'slug', $find['trail'], 'trail' );
			if ( $term instanceof WP_Term ) {
				$id = (int) $term->term_id;
				for ( $n = 1; $n <= 3 && '' === $key; $n++ ) {
					if ( (int) get_term_meta( $id, "sh_static_tile_{$n}_position", true ) === (int) $find['position'] ) {
						$key = "sh_static_tile_{$n}_image_hover";
					}
				}
			}
		}
		if ( ! $id || '' === $key ) {
			continue; // Not on this site (or no slot for that trail): nothing to swap.
		}
		$new = sh_imp_1117_image( $sw['new'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover(), $sw['title'] );
		if ( ! $new ) {
			continue; // File missing (warning raised) — the step runs again next time.
		}
		$cur = (int) ( 'post' === $sw['type'] ? get_post_meta( $id, $key, true ) : get_term_meta( $id, $key, true ) );
		if ( $cur === $new ) {
			continue; // Already the new image (fresh import / done before).
		}
		// The importer's key of the old image: "asset:<file without extension>" — file relative to design/assets, as
		// sh_imp_1117_image() wrote it (objects-1117/<ref> · or the bare ref of an older rule). The old file need not be
		// on this machine (the deploy bundle lists only what the seed uses now), so both spellings are tried.
		$name = (string) pathinfo( basename( (string) preg_replace( '/\s.*$/s', '', trim( $sw['prev'] ) ) ), PATHINFO_FILENAME );
		$prev = array_filter(
			array(
				sh_imp_find_attachment( 'asset:' . SH_IMPORT_OBJECTS_1117 . '/' . $name ),
				sh_imp_find_attachment( 'asset:' . $name ),
			)
		);
		if ( in_array( $cur, $prev, true ) ) {
			sh_imp_meta( $sw['type'], $id, array( $key => $new ) ); // The old attachment stays in the media library.
			sh_imp_count( 'updated', 'tile_hover_swapped' );
		} else {
			sh_imp_keep( "{$sw['what']} hover (site attachment $cur is not the one the import made · new seed image not applied)" );
		}
	}
}

/**
 * Existing attachment of a zone grid tile ref (no upload): the import key the objects' tiles were made under —
 * asset:<file without extension> of the objects-1117 file or of the top-level file (elephant / image-map), or asset:<ref>.
 *
 * @param string $ref Figma imageRef of the zone seed.
 */
function sh_import_zone_tile_find( string $ref ): int {
	$dir  = (string) sh_imp_config()['assets'];
	$file = sh_import_zone_tile_file( $ref, $dir );
	$keys = array( 'asset:' . $ref, 'asset:' . SH_IMPORT_OBJECTS_1117 . '/' . $ref );
	if ( '' !== $file ) {
		$ext    = (string) pathinfo( $file, PATHINFO_EXTENSION );
		$keys[] = 'asset:' . ( '' === $ext ? $file : substr( $file, 0, -strlen( $ext ) - 1 ) );
	}
	foreach ( $keys as $key ) {
		$id = sh_imp_find_attachment( $key );
		if ( $id ) {
			return $id;
		}
	}
	return 0;
}

/**
 * Attachment of a zone grid tile ref: the existing one, else created from the file (0 = no file in design/assets —
 * the slot then shows the object's own tile image).
 *
 * @param string               $ref   Figma imageRef.
 * @param string               $title Attachment title.
 * @param array<string, mixed> $spec  sh_import_spec_tile() / sh_import_spec_tile_hover().
 */
function sh_import_zone_tile_attachment( string $ref, string $title, array $spec ): int {
	$have = sh_import_zone_tile_find( $ref );
	if ( $have ) {
		return $have;
	}
	$dir  = (string) sh_imp_config()['assets'];
	$file = sh_import_zone_tile_file( $ref, $dir );
	if ( '' === $file ) {
		sh_imp_count( 'skipped', 'zone_tile_no_file' );
		return 0;
	}
	$ext = (string) pathinfo( $file, PATHINFO_EXTENSION );
	return sh_imp_attachment( $dir . '/' . $file, 'asset:' . ( '' === $ext ? $file : substr( $file, 0, -strlen( $ext ) - 1 ) ), $title, array(), $spec );
}

/**
 * Step 2026-11-17-zone-grid (fb-1117 · D57): the Zone 1–3 pages show a grid of objects — slot n of zone term zone-<k> =
 * sh_zone_tile_<n>_object / _image / _image_hover (CONTENT-MODEL §5b) from seed zones-2026-11-17.json. Add only what is
 * missing, never overwrite: an empty field is filled; a field with another value (an admin changed it) stays, the rest
 * of that slot is left alone too (a picture beside his object would not match), and it is listed in kept_admin (one line
 * per field). Images = the attachments the objects' own tiles already have
 * (found by import key — no second upload); only a picture no object has (zone 1 slot 8, Buffalo 6) is created from the
 * bundle. A slot whose tile image has no file keeps an empty image (the object's own tile shows). A zone term not on
 * the site is skipped (nothing is created here); an object not on the site leaves that slot a picture without a link.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_zone_grid_1117( array $seed ): void {
	foreach ( sh_import_zone_grid_tiles( $seed ) as $zn => $slots ) {
		$term = get_term_by( 'slug', 'zone-' . $zn, 'zone' );
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		foreach ( $slots as $slot => $t ) {
			$title = $t['object_slug'] ? $t['object_slug'] : "zone $zn tile $slot";
			$obj   = $t['object_slug'] ? sh_imp_find_post( 'object', 'object:' . $t['object_slug'] ) : 0;
			$want  = array(
				"sh_zone_tile_{$slot}_object"      => array( $obj, null ),
				"sh_zone_tile_{$slot}_image"       => array( 0, array( $t['tile_ref'], sh_import_spec_tile(), "$title zone tile" ) ),
				"sh_zone_tile_{$slot}_image_hover" => array( 0, '' !== $t['hover_ref'] ? array( $t['hover_ref'], sh_import_spec_tile_hover(), "$title zone tile hover" ) : null ),
			);
			// A slot an admin changed stays whole (a picture filled next to his object would not match): when any field holds
			// another value than the seed's → nothing of the slot is written · one kept_admin line per such field.
			$expect_of = static fn( array $w ): int => $w[0] ? (int) $w[0] : ( is_array( $w[1] ) ? sh_import_zone_tile_find( $w[1][0] ) : 0 );
			$admin     = false;
			foreach ( $want as $key => $w ) {
				$cur    = absint( get_term_meta( $term->term_id, $key, true ) );
				$expect = $cur ? $expect_of( $w ) : 0;
				if ( $cur && $expect && $expect !== $cur ) {
					sh_imp_keep( "zone:$zn $key (admin value $cur · seed $expect · slot kept)" );
					$admin = true;
				}
			}
			if ( $admin ) {
				continue;
			}
			$write = array();
			foreach ( $want as $key => $w ) {
				if ( absint( get_term_meta( $term->term_id, $key, true ) ) ) {
					continue; // Already the seed's value.
				}
				$val = $w[0] ? (int) $w[0] : ( is_array( $w[1] ) ? sh_import_zone_tile_attachment( $w[1][0], $w[1][2], $w[1][1] ) : 0 );
				if ( $val ) {
					$write[ $key ] = $val;
				}
			}
			if ( $write ) {
				sh_imp_meta( 'term', $term->term_id, $write );
				sh_imp_count( 'updated', 'zone_grid_slot' );
			}
		}
	}
}

/** Belt buckle (pending-3 · D58): the title the earlier import wrote (the new one comes from the seed). */
const SH_IMPORT_BELT_1117 = array(
	'slug' => 'pending-3',
	'old'  => 'Pending (Traditional Belt Buckle)',
);

/**
 * Step 2026-11-17-belt-title (fb-1117 · D58): post_title of object pending-3 (and sh_title_raw when it is the old title)
 * becomes the seed's Figma title only while the site still holds exactly what the earlier import wrote; any other value
 * (an admin edited it) stays and is listed in kept_admin. The slug (live URL) is never touched. An object not on the
 * site is skipped.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_belt_title_1117( array $seed ): void {
	$slug = SH_IMPORT_BELT_1117['slug'];
	$old  = SH_IMPORT_BELT_1117['old'];
	$post = null;
	$id   = sh_imp_find_post( 'object', 'object:' . $slug );
	if ( $id ) {
		$post = get_post( $id );
	}
	$seed_o = null;
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		if ( $slug === (string) $o['slug'] ) {
			$seed_o = $o;
		}
	}
	if ( ! $post instanceof WP_Post || ! is_array( $seed_o ) ) {
		return;
	}
	$text  = sh_import_object_text_2026_11( $seed_o );
	$new   = $text['post_title'];
	if ( '' === $new || sh_imp_same_text( $post->post_title, $new, null ) ) {
		return; // Fresh import / already the Figma title.
	}
	if ( ! sh_imp_same_text( $post->post_title, $old, null ) ) {
		sh_imp_keep( "object:$slug post_title (site \"" . $post->post_title . '" · seed "' . $new . '")' );
		return;
	}
	wp_update_post(
		array(
			'ID'         => $post->ID,
			'post_title' => wp_slash( $new ),
		)
	);
	$raw = (string) get_post_meta( $post->ID, 'sh_title_raw', true );
	if ( '' === $raw || sh_imp_same_text( $raw, $old, null ) ) {
		update_post_meta( $post->ID, 'sh_title_raw', wp_slash( $text['sh_title_raw'] ) );
	}
	sh_imp_count( 'updated', 'belt_title' );
}

/**
 * Fill the empty fields of an object with seed values and leave the rest alone (D59 step): an empty field gets the
 * value · a field with the same value (text compared as stored) is fine · a field with another value (an admin
 * changed it) stays and is listed in kept_admin. A missing file (0 · already a warning) writes nothing.
 *
 * @param int                  $id    Object id.
 * @param array<string, mixed> $meta  key => value (null = nothing in the seed).
 * @param array<string, mixed> $defs  name => field definition (sanitize rule of the text compare).
 * @param string               $label Report label of the entity.
 */
function sh_import_fill_or_keep( int $id, array $meta, array $defs, string $label ): void {
	$write = array();
	foreach ( $meta as $key => $value ) {
		if ( null === $value ) {
			continue;
		}
		if ( is_bool( $value ) ) {
			if ( ! metadata_exists( 'post', $id, $key ) ) {
				$write[ $key ] = $value;
			}
			continue;
		}
		$cur = get_post_meta( $id, $key, true );
		if ( '' === $cur || 0 === $cur || '0' === $cur ) {
			if ( ! ( is_int( $value ) && 0 === $value ) && '' !== $value ) {
				$write[ $key ] = $value;
			}
			continue;
		}
		$same = is_int( $value ) ? absint( $cur ) === $value : sh_imp_same_text( $cur, (string) $value, isset( $defs[ $key ] ) ? $defs[ $key ] : null );
		if ( ! $same ) {
			sh_imp_keep( "$label $key (site value kept · seed differs)" );
		}
	}
	if ( $write ) {
		sh_imp_meta( 'post', $id, $write );
		sh_imp_count( 'updated', 'object_trail_version' );
	}
}

/** Đông Sơn drum (D60): one object per trail — the water-bird one keeps its slug, the seashell one is a new object. */
const SH_IMPORT_DRUM_1117 = array(
	'old'       => 'ng-s-n-drum',
	'new'       => 'dong-son-drum-thung-yang',
	'trail'     => 'seashell',
	'was_trails' => array( 'water-bird', 'seashell' ),
);

/**
 * Part of step 2026-11-17-multitrail (D60): the Đông Sơn drum of the Seashell trail (Thung Yang · National Museum
 * Bangkok) is its own object. A site imported before has ONE object (ng-s-n-drum) in water-bird + seashell:
 *   new object     dong-son-drum-thung-yang created when missing (the usual object import · hero · tiles · layout)
 *   old object     the seashell term taken off only while its trails are exactly the two the import gave it (an admin
 *                  who changed them: kept_admin) · its seashell tile fields stay (harmless · nothing deleted)
 *   seashell trail sh_object_order gets the new object in the old one's place only while it is still the old import list
 *   zone grid      the Zone 1–3 tile that still holds the old object while the seed says the new one → the new object
 *                  (+ its trail); a tile holding another object stays (kept_admin) · images are the seashell tile's already
 *
 * @param array<string, mixed> $seed     Seed files.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $manifest Manifest.
 */
function sh_import_refresh_drum_split_1117( array $seed, array $map, array $manifest ): void {
	$c    = SH_IMPORT_DRUM_1117;
	$term = get_term_by( 'slug', $c['trail'], 'trail' );
	if ( ! $term instanceof WP_Term ) {
		return;
	}
	$in_seed = false;
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$in_seed = $in_seed || (string) $o['slug'] === $c['new'];
	}
	if ( ! $in_seed ) {
		return;
	}
	if ( ! sh_imp_find_post( 'object', 'object:' . $c['new'] ) ) {
		sh_import_objects_1117( $seed, $map, $manifest, true, array( $c['new'] ) );
	}
	$new_id = sh_imp_find_post( 'object', 'object:' . $c['new'] );
	$old_id = sh_imp_find_post( 'object', 'object:' . $c['old'] );
	if ( ! $new_id || ! $old_id ) {
		return;
	}
	if ( has_term( $term->term_id, 'trail', $old_id ) ) {
		$slugs = wp_get_object_terms( $old_id, 'trail', array( 'fields' => 'slugs' ) );
		if ( is_array( $slugs ) && ! array_diff( $slugs, $c['was_trails'] ) ) {
			wp_remove_object_terms( $old_id, (int) $term->term_id, 'trail' );
			sh_imp_count( 'updated', 'object_trail_term_removed' );
		} else {
			sh_imp_keep( "object:{$c['old']} trails (admin changed them · seashell term not taken off)" );
		}
	}
	// sh_object_order of the trail: the new object in the old one's place (old import list only).
	$new = array();
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$oid = sh_imp_find_post( 'object', 'object:' . $o['slug'] );
		if ( $oid && 'publish' === get_post_status( $oid ) && isset( $o['trail_order'][ $term->slug ] ) ) {
			$new[ $oid ] = (int) $o['trail_order'][ $term->slug ];
		}
	}
	asort( $new );
	$old = array();
	foreach ( array_keys( $new ) as $oid ) {
		$old[] = $oid === $new_id ? $old_id : $oid;
	}
	$cur = get_term_meta( $term->term_id, 'sh_object_order', true );
	$cur = is_array( $cur ) ? array_values( array_map( 'intval', $cur ) ) : array();
	if ( array_keys( $new ) !== $cur ) {
		if ( ! $cur || $old === $cur ) {
			sh_imp_meta( 'term', $term->term_id, array( 'sh_object_order' => array_keys( $new ) ) );
			sh_imp_count( 'updated', 'trail_object_order' );
		} else {
			sh_imp_keep( "trail:{$c['trail']} sh_object_order (admin order kept · the new drum comes after it, A–Z)" );
		}
	}
	// Zone grid tiles of the new object.
	foreach ( sh_import_zone_grid_tiles( $seed ) as $zn => $slots ) {
		$zone = get_term_by( 'slug', 'zone-' . $zn, 'zone' );
		foreach ( $slots as $slot => $t ) {
			if ( ! $zone instanceof WP_Term || $t['object_slug'] !== $c['new'] ) {
				continue;
			}
			$key = "sh_zone_tile_{$slot}_object";
			$cur = absint( get_term_meta( $zone->term_id, $key, true ) );
			if ( $cur === $old_id ) {
				sh_imp_meta( 'term', $zone->term_id, array( $key => $new_id ) );
				sh_imp_count( 'updated', 'zone_tile_object' );
			} elseif ( $cur && $cur !== $new_id ) {
				sh_imp_keep( "zone:$zn $key (admin value $cur · seed object {$c['new']} not set)" );
			}
		}
	}
}

/**
 * Step 2026-11-17-multitrail (BACKLOG #41 · D59): (1) every seed object that sits in several trails gets one trail version
 * (sh_variant_<n>_*, CONTENT-MODEL §2b) per trail other than its main one — the slot where that trail already is, else the
 * first free slot; fields are filled only while empty (sh_import_fill_or_keep()) · an object not on the site, a trail that is
 * not a term of the site or one the object was taken out of, is skipped · nothing is removed (D60 drum split: see
 * sh_import_refresh_drum_split_1117()). (2) the Zone 1–3 grid tile
 * gets its trail (sh_zone_tile_<n>_trail) only while empty and while the tile still holds the seed's object.
 *
 * @param array<string, mixed> $seed     Seed files.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $manifest Manifest.
 */
function sh_import_refresh_multitrail_1117( array $seed, array $map, array $manifest ): void {
	sh_import_refresh_drum_split_1117( $seed, $map, $manifest ); // D60 first: the zone tile / trail the rest reads.
	$trail_ids = sh_imp_trail_ids();
	$defs      = array();
	foreach ( sh_core_group_fields( 'object_variant' ) as $f ) {
		$defs[ (string) $f['name'] ] = $f;
	}
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$versions = sh_import_object_variants( $o );
		$slug     = (string) $o['slug'];
		$id       = $versions ? sh_imp_find_post( 'object', 'object:' . $slug ) : 0;
		if ( ! $id ) {
			continue;
		}
		foreach ( $versions as $trail => $ver ) {
			if ( ! isset( $trail_ids[ $trail ] ) || ! has_term( $trail_ids[ $trail ], 'trail', $id ) ) {
				continue;
			}
			$slot = 0;
			$free = 0;
			for ( $n = 1; $n <= SH_VARIANT_SLOTS; $n++ ) {
				$cur = absint( get_post_meta( $id, "sh_variant_{$n}_trail", true ) );
				if ( $cur === $trail_ids[ $trail ] ) {
					$slot = $n;
					break;
				}
				if ( ! $cur && ! $free ) {
					$free = $n;
				}
			}
			$slot = $slot ? $slot : $free;
			if ( ! $slot ) {
				sh_imp_keep( "object:$slug version $trail (no free trail version slot · not added)" );
				continue;
			}
			sh_import_fill_or_keep( $id, sh_import_variant_meta( $o, $trail, $slot, $ver, $trail_ids, $map, $manifest ), $defs, "object:$slug version $trail" );
		}
	}
	foreach ( sh_import_zone_grid_tiles( $seed ) as $zn => $slots ) {
		$term = get_term_by( 'slug', 'zone-' . $zn, 'zone' );
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		foreach ( $slots as $slot => $t ) {
			$obj = $t['object_slug'] ? sh_imp_find_post( 'object', 'object:' . $t['object_slug'] ) : 0;
			if ( ! $obj || null === $t['trail'] || ! isset( $trail_ids[ $t['trail'] ] ) ) {
				continue;
			}
			$cur_obj = absint( get_term_meta( $term->term_id, "sh_zone_tile_{$slot}_object", true ) );
			$key     = "sh_zone_tile_{$slot}_trail";
			$cur     = absint( get_term_meta( $term->term_id, $key, true ) );
			if ( $cur_obj && $cur_obj !== $obj ) {
				sh_imp_keep( "zone:$zn tile $slot trail (the tile holds another object · not set)" );
			} elseif ( $cur_obj && ! $cur ) {
				sh_imp_meta( 'term', $term->term_id, array( $key => $trail_ids[ $t['trail'] ] ) );
				sh_imp_count( 'updated', 'zone_tile_trail' );
			} elseif ( $cur && $cur !== $trail_ids[ $t['trail'] ] ) {
				sh_imp_keep( "zone:$zn $key (admin value $cur · seed {$trail_ids[ $t['trail'] ]})" );
			}
		}
	}
}

/**
 * Run one step of this file. Returns true when done (false = a file was missing: run again on the next import).
 *
 * @param string                                  $step     Step id.
 * @param array<string, mixed>                    $seed     Seed files.
 * @param array<string, mixed>                    $map      image-map.json.
 * @param array<string, mixed>                    $manifest Manifest.
 * @param array<string, array{id:int,write:bool}> $pages    Pages.
 */
function sh_import_refresh_2026_11_17( string $step, array $seed, array $map, array $manifest, array $pages ): bool {
	$before = count( sh_imp_state()['warnings'] );
	switch ( $step ) {
		case '2026-11-17-objects':
			sh_import_objects_1117( $seed, $map, $manifest, true );
			break;
		case '2026-11-17-trails':
			sh_import_refresh_trails_1117( $seed );
			break;
		case '2026-11-17-pages':
			sh_import_refresh_pages_1117( $seed, $manifest, $pages );
			break;
		case '2026-11-17-site':
			sh_import_refresh_site_1117( $seed );
			break;
		case '2026-11-17-trail-grid':
			sh_import_refresh_trail_grid_1117( $seed );
			break;
		case '2026-11-17-makara':
			sh_import_refresh_makara_1117( $seed, $map, $manifest );
			break;
		case '2026-11-17-rooster':
			sh_import_refresh_rooster_1117( $seed, $map, $manifest );
			break;
		case '2026-11-17-makara-side':
			sh_import_refresh_makara_side_1117( $seed, $map );
			break;
		case '2026-11-17-hover':
			sh_import_refresh_hover_1117( $seed );
			break;
		case '2026-11-17-zone-grid':
			sh_import_refresh_zone_grid_1117( $seed );
			break;
		case '2026-11-17-belt-title':
			sh_import_refresh_belt_title_1117( $seed );
			break;
		case '2026-11-17-multitrail':
			sh_import_refresh_multitrail_1117( $seed, $map, $manifest );
			break;
	}
	return count( sh_imp_state()['warnings'] ) === $before;
}
