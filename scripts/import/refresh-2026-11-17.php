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
	}
	return count( sh_imp_state()['warnings'] ) === $before;
}
