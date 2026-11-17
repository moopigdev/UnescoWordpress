<?php
/**
 * Importer, round 2026-11-17 (core-1117 · D47–D53 · BACKLOG #25–#30) — values of the new seeds, shared by the fresh
 * import (import.php) and the refresh steps for sites imported before (refresh-2026-11-17.php):
 *   objects   every trail (D49 replaces D7): the 102 generic records of 16 trails (+ tiles of the elephant records
 *             in other trails) · hold_* = draft (P-306) · hero = manifest key drive-1117/<hero_video_src> (P-303)
 *   trails    H1 · description · cover · layout · tiles without a page (§8.2) · animal guide card (guides.json · D48)
 *   home      "Your animal guide is..." section texts + card back (D48)
 *   intro     Introduction 4 chapters (intro-2026-11.json · D50): numbered text/image slots + layout JSON
 *   site      header logo · footer logos/text (D47) · Primary menu "About the Project" (new tab)
 * The *_meta functions return meta key => value (null = nothing in the seed → field untouched · 0 = file missing).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/refs-2026-11-17.php';

/**
 * Attachment for a 2026-11-17 seed image value (refs-2026-11-17.php sh_import_1117_file()) — copied at most 2× its
 * display size (spec). null = no value · 0 (+ warning) = file missing.
 *
 * @param mixed                     $value Seed value.
 * @param string                    $sub   Folder tried first.
 * @param array<string, mixed>|null $spec  Web spec.
 * @param string                    $title Attachment title.
 * @param string                    $alt   Alt set when the attachment has none.
 */
function sh_imp_1117_image( $value, string $sub, ?array $spec, string $title, string $alt = '' ): ?int {
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return null;
	}
	$dir  = sh_imp_config()['assets'];
	$file = sh_import_1117_file( $value, $dir, $sub );
	if ( '' === $file ) {
		sh_imp_warn( 'seed asset without file: ' . $value . ' (' . $title . ')' );
		return 0;
	}
	$ext = (string) pathinfo( $file, PATHINFO_EXTENSION );
	$id  = sh_imp_attachment( $dir . '/' . $file, 'asset:' . ( '' === $ext ? $file : substr( $file, 0, -strlen( $ext ) - 1 ) ), $title, array(), $spec );
	if ( $id && '' !== $alt && '' === (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $id;
}

/**
 * Seed HTML (<p>…</p> · <br> · <strong>/<em>) → stored text of a format=inline_html field: paragraphs separated by
 * a blank line, <br> = line break, inline tags kept.
 *
 * @param string $html Seed html.
 */
function sh_import_html_to_text( string $html ): string {
	$t = (string) preg_replace( '#</p>\s*<p[^>]*>#i', "\n\n", $html );
	$t = (string) preg_replace( '#<br\s*/?>#i', "\n", $t );
	$t = (string) preg_replace( '#</?p[^>]*>#i', '', $t );
	return trim( $t );
}

/**
 * Trail term ids by slug of the trails that exist on this site.
 *
 * @return array<string, int>
 */
function sh_imp_trail_ids(): array {
	$out   = array();
	$terms = get_terms(
		array(
			'taxonomy'   => 'trail',
			'hide_empty' => false,
		)
	);
	foreach ( is_array( $terms ) ? $terms : array() as $t ) {
		$out[ $t->slug ] = (int) $t->term_id;
	}
	return $out;
}

/**
 * Meta of a generic (16-trail) object from the seed — the same sources the elephant import uses, from the primary
 * frame (DESIGN-SPEC §8.3/§8.4).
 *
 * @param array<string, mixed> $o        Seed object.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $manifest Media manifest.
 * @return array<string, mixed>
 */
function sh_import_generic_object_meta( array $o, array $map, array $manifest ): array {
	$text  = sh_import_object_text_2026_11( $o );
	$title = $text['post_title'];
	$frame = sh_import_primary_frame( $o );
	$ds    = isset( $o['detail_style'] ) && is_array( $o['detail_style'] ) ? $o['detail_style'] : array();
	$txt   = static fn( string $k ): string => isset( $o[ $k ] ) && is_string( $o[ $k ] ) ? $o[ $k ] : '';
	$col   = static fn( $v ): string => is_string( $v ) ? $v : '';
	$video = sh_imp_video( sh_import_object_hero_entry( $manifest, $o ), $title );
	$meta  = array(
		'sh_title_raw'            => $text['sh_title_raw'],
		'sh_collection'           => $txt( 'collection' ),
		'sh_scientific_name'      => $txt( 'scientific_name' ),
		'sh_thematic_section'     => $txt( 'thematic_section' ),
		'sh_period'               => $txt( 'period' ),
		'sh_place_of_production'  => $txt( 'place_of_production' ),
		'sh_material'             => $txt( 'material' ),
		'sh_dimensions'           => $txt( 'dimensions' ),
		'sh_local_name'           => $txt( 'local_name' ),
		'sh_object_biography'     => $txt( 'object_biography' ),
		'sh_caption'              => $text['sh_caption'],
		'sh_tile_image'           => sh_imp_1117_image( isset( $o['tile_image_ref'] ) ? $o['tile_image_ref'] : null, SH_IMPORT_OBJECTS_1117, sh_import_spec_tile(), $title . ' tile' ),
		'sh_tile_image_hover'     => sh_imp_1117_image( isset( $o['tile_image_hover_ref'] ) ? $o['tile_image_hover_ref'] : null, SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover(), $title . ' tile hover' ),
		'sh_panel_bg'             => $col( isset( $ds['panel_bg'] ) ? $ds['panel_bg'] : null ),
		'sh_title_color'          => $col( isset( $ds['title_color'] ) ? $ds['title_color'] : null ),
		'sh_text_color'           => $col( isset( $ds['text_color'] ) ? $ds['text_color'] : null ),
		'sh_meta_color'           => $col( isset( $frame['style']['meta_color'] ) ? $frame['style']['meta_color'] : ( isset( $ds['meta_color'] ) ? $ds['meta_color'] : null ) ),
		'sh_collection_color'     => $col( isset( $ds['collection_color'] ) ? $ds['collection_color'] : null ),
		'sh_layout'               => sh_import_layout( $frame, $map, $title ),
		'sh_hero_video_webm'      => $video['webm'],
		'sh_hero_video_hevc'      => $video['hevc'],
		'sh_hero_video_has_alpha' => $video['has_alpha'],
		'sh_hero_poster'          => $video['poster'],
	);
	if ( $video['still'] ) {
		$meta['sh_hero_image'] = $video['still'];
	} elseif ( null === sh_import_object_hero_entry( $manifest, $o ) && '' !== sh_import_object_hero_still_ref( $o ) ) {
		// No WebM (makara-1117: Saltwater crocodile skull · Sungai Mas makara): hero = the line-art still of the seed.
		$meta['sh_hero_image'] = sh_imp_1117_image( sh_import_object_hero_still_ref( $o ), SH_IMPORT_OBJECTS_1117, sh_import_spec_hero_still(), $title . ' hero', $title );
	}
	// Panel images = the design-dev crops of the frame (style.images[].crop_ref · same attachments as in sh_layout).
	$images = isset( $frame['style']['images'] ) && is_array( $frame['style']['images'] ) ? array_values( $frame['style']['images'] ) : array();
	usort( $images, static fn( $a, $b ): int => (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ) );
	for ( $i = 1; $i <= 6; $i++ ) {
		$ref                            = isset( $images[ $i - 1 ]['crop_ref'] ) ? (string) $images[ $i - 1 ]['crop_ref'] : '';
		$meta[ 'sh_extra_image_' . $i ] = '' === $ref ? 0 : sh_imp_asset_ref( $map, $ref, $title . ' image' );
	}
	return $meta;
}

/**
 * Tiles of an object in its other trails (sh_trail_tile_<n>_* · max 4): tiles_by_trail without the main trail.
 *
 * @param array<string, mixed> $o         Seed object.
 * @param array<string, int>   $trail_ids slug => term id.
 * @param string               $title     Object title.
 * @return array<string, int|null>
 */
function sh_import_object_trail_tiles_meta( array $o, array $trail_ids, string $title ): array {
	$meta = array();
	$n    = 0;
	$main = sh_import_object_main_trail( $o );
	foreach ( sh_import_object_tiles( $o ) as $t => $tile ) {
		if ( $t === $main || ! isset( $trail_ids[ $t ] ) || $n >= 4 ) {
			continue;
		}
		++$n;
		$meta[ "sh_trail_tile_{$n}_trail" ]       = $trail_ids[ $t ];
		$meta[ "sh_trail_tile_{$n}_image" ]       = sh_imp_1117_image( $tile['image'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile(), "$title tile ($t)" );
		$meta[ "sh_trail_tile_{$n}_image_hover" ] = sh_imp_1117_image( $tile['hover'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover(), "$title tile hover ($t)" );
	}
	return $meta;
}

/**
 * Meta of one trail version of an object (D59 · multitrail-1117 · CONTENT-MODEL §2b): sh_variant_<slot>_* from the seed
 * variant (objects.json variants_by_trail.<trail> + the Figma record of its frame): trail · hero (manifest key
 * drive-1117/<hero_video_src> → WebM + poster + HEVC only for alpha) · colours · side images (the frame's crops, same
 * attachments as in the layout) · layout JSON · text that differs from the object's (title · caption · info rows).
 * Empty array = the trail is not a term of this site. A seed value that is null leaves the field out (untouched).
 *
 * @param array<string, mixed> $o         Seed object.
 * @param string               $trail     Trail slug of the version.
 * @param int                  $slot      Slot n (1..SH_VARIANT_SLOTS) on the site.
 * @param array<string, mixed> $ver       {variant, frame} of sh_import_object_variants().
 * @param array<string, int>   $trail_ids slug => term id.
 * @param array<string, mixed> $map       image-map.json.
 * @param array<string, mixed> $manifest  Media manifest.
 * @return array<string, mixed>
 */
function sh_import_variant_meta( array $o, string $trail, int $slot, array $ver, array $trail_ids, array $map, array $manifest ): array {
	if ( ! isset( $trail_ids[ $trail ] ) ) {
		return array();
	}
	$v     = $ver['variant'];
	$frame = $ver['frame'];
	$title = sh_import_object_text_2026_11( $o )['post_title'] . " ($trail)";
	$p     = "sh_variant_{$slot}_";
	$hex   = static fn( $c ) => is_string( $c ) && '' !== $c ? $c : null;
	$meta  = array(
		$p . 'trail' => $trail_ids[ $trail ],
	);
	if ( ! empty( $v['hero_video_src'] ) ) {
		$entry = sh_import_manifest_by_key( $manifest, 'drive-1117/' . $v['hero_video_src'] );
		if ( null === $entry ) {
			sh_imp_warn( "variant hero without manifest entry: drive-1117/{$v['hero_video_src']} ($title)" );
		} else {
			$video                               = sh_imp_video( $entry, $title );
			$meta[ $p . 'hero_video_webm' ]      = $video['webm'];
			$meta[ $p . 'hero_video_hevc' ]      = $video['hevc'];
			$meta[ $p . 'hero_video_has_alpha' ] = $video['has_alpha'];
			$meta[ $p . 'hero_poster' ]          = $video['poster'];
		}
	}
	$meta[ $p . 'panel_bg' ]         = $hex( isset( $v['panel_bg'] ) ? $v['panel_bg'] : null );
	$meta[ $p . 'title_color' ]      = $hex( isset( $v['title_color'] ) ? $v['title_color'] : null );
	$meta[ $p . 'text_color' ]       = $hex( isset( $v['caption_color'] ) ? $v['caption_color'] : null );
	$meta[ $p . 'meta_color' ]       = $hex( isset( $v['meta_color'] ) ? $v['meta_color'] : null );
	$meta[ $p . 'collection_color' ] = $hex( isset( $v['collection_color'] ) ? $v['collection_color'] : null );

	$images = isset( $frame['style']['images'] ) && is_array( $frame['style']['images'] ) ? array_values( $frame['style']['images'] ) : array();
	usort( $images, static fn( $a, $b ): int => (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ) );
	for ( $k = 1; $k <= 2; $k++ ) {
		$ref = isset( $images[ $k - 1 ]['crop_ref'] ) ? (string) $images[ $k - 1 ]['crop_ref'] : '';
		if ( '' !== $ref ) {
			$meta[ $p . 'image_' . $k ] = sh_imp_asset_ref( $map, $ref, "$title side image $k" );
		}
	}
	$meta[ $p . 'layout' ] = sh_import_layout( $frame, $map, $title );

	$to = isset( $v['text_override'] ) && is_array( $v['text_override'] ) ? $v['text_override'] : array();
	foreach ( array( 'title_raw', 'title' ) as $k ) { // title_raw keeps the Figma line breaks.
		if ( ! empty( $to[ $k ] ) && is_string( $to[ $k ] ) ) {
			$meta[ $p . 'title' ] = $to[ $k ];
			break;
		}
	}
	if ( ! empty( $to['caption'] ) && is_string( $to['caption'] ) ) {
		$meta[ $p . 'caption' ] = $to['caption'];
	}
	$keys = sh_core_variant_info_keys();
	foreach ( (array) ( isset( $to['info_rows'] ) ? $to['info_rows'] : array() ) as $row ) {
		if ( is_array( $row ) && isset( $row['key'], $row['value'] ) && isset( $keys[ (string) $row['key'] ] ) && is_string( $row['value'] ) && '' !== $row['value'] ) {
			$meta[ $p . $row['key'] ] = $row['value'];
		}
	}
	return $meta;
}

/**
 * Real trails of a seed object that exist on the site (term ids).
 *
 * @param array<string, mixed> $o         Seed object.
 * @param array<string, int>   $trail_ids slug => term id.
 * @return int[]
 */
function sh_import_object_term_ids( array $o, array $trail_ids ): array {
	$ids = array();
	foreach ( (array) $o['trails'] as $t ) {
		if ( isset( $trail_ids[ $t ] ) ) {
			$ids[] = $trail_ids[ $t ];
		}
	}
	return $ids;
}

/**
 * Generic objects (16 trails) → posts (created when missing · a post an admin made with the same slug is adopted and
 * left as it is) + the other-trail tiles of every object. Returns slug => post id of all in-scope objects found.
 *
 * @param array<string, mixed> $seed     Seed files.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $manifest Media manifest.
 * @param bool                 $fill     true = refresh: existing objects only get empty fields filled.
 * @param string[]|null        $only     Only these slugs (refresh step 2026-11-17-makara) · null = all.
 * @return array<string, int>
 */
function sh_import_objects_1117( array $seed, array $map, array $manifest, bool $fill = false, ?array $only = null ): array {
	$trail_ids = sh_imp_trail_ids();
	$out       = array();
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		$slug  = (string) $o['slug'];
		if ( null !== $only && ! in_array( $slug, $only, true ) ) {
			continue;
		}
		$title = sh_import_object_text_2026_11( $o )['post_title'];
		$key   = 'object:' . $slug;
		$id    = sh_imp_find_post( 'object', $key );
		if ( ! $id ) {
			$adopt = get_page_by_path( $slug, OBJECT, 'object' );
			if ( $adopt instanceof WP_Post ) { // Made by hand with the seed slug: adopt, never overwrite.
				update_post_meta( $adopt->ID, '_sh_import_key', $key );
				$id = (int) $adopt->ID;
				sh_imp_keep( "object:$slug adopted (made in admin) · fields only filled where empty" );
			}
		}
		// Records of this round: the generic 16-trail objects + records held back (hold_* · e.g. sumatran-serow, a
		// 2026-10-27 record without a Figma frame now) — same field sources (primary frame · its style).
		$generic = sh_import_object_generic( $o ) || sh_import_object_hold( $o );
		if ( ! $id && ! $generic ) {
			continue; // Elephant records: sh_import_objects() (refresh-only sites have them).
		}
		$write = false;
		if ( ! $id ) {
			list( $id, $write ) = sh_imp_post(
				'object',
				$key,
				array(
					'post_type'   => 'object',
					'post_title'  => $title,
					'post_name'   => $slug,
					'post_status' => sh_import_object_hold( $o ) ? 'draft' : 'publish', // P-306 hold_* = not published.
				)
			);
			wp_set_object_terms( $id, sh_import_object_term_ids( $o, $trail_ids ), 'trail' );
		} elseif ( ! $fill && 'overwrite' === sh_imp_config()['mode'] && $generic ) {
			list( $id, $write ) = sh_imp_post(
				'object',
				$key,
				array(
					'post_type'   => 'object',
					'post_title'  => $title,
					'post_name'   => $slug,
					'post_status' => get_post_status( $id ),
				)
			);
		}
		$out[ $slug ] = $id;
		$meta         = sh_import_object_trail_tiles_meta( $o, $trail_ids, $title );
		if ( $generic && ( $write || $fill ) ) {
			$meta = sh_import_generic_object_meta( $o, $map, $manifest ) + $meta;
		}
		if ( $write ) {
			sh_imp_meta( 'post', $id, $meta );
		} elseif ( $fill || ! $generic ) {
			$fields = array( 'sh_scientific_name' => isset( $o['scientific_name'] ) ? (string) $o['scientific_name'] : null ) + $meta;
			unset( $fields['sh_layout'], $fields['sh_hero_video_has_alpha'] ); // Never filled into an existing object.
			sh_imp_fill_empty( 'post', $id, array_map( static fn( $v ) => ( is_int( $v ) && 0 === $v ) ? null : $v, $fields ), 'object_1117' );
			if ( $fill ) {
				$post = get_post( $id );
				if ( $post instanceof WP_Post && ! sh_imp_same_text( $post->post_title, $title, null ) ) {
					sh_imp_keep( "object:$slug post_title (site \"" . $post->post_title . '" · seed "' . $title . '")' );
				}
			}
		}
	}
	return $out;
}

/**
 * sh_trail_layout of a seed trail (DESIGN-SPEC §8.1): trail_page_layout + "grid" [x, y, w, h] = the tile grid box
 * (Frame 114472) from the seed tiles' bboxes (P-340: Garuda's grid sits at y 875.2, Water-bird/Seashell at 829.2 with
 * the same heading height — the page reads it as is). null = no layout in the seed.
 *
 * @param array<string, mixed> $t Seed trail.
 * @return array<string, mixed>|null
 */
function sh_import_trail_layout_1117( array $t ): ?array {
	if ( empty( $t['trail_page_layout'] ) || ! is_array( $t['trail_page_layout'] ) ) {
		return null;
	}
	$layout = $t['trail_page_layout'];
	$top    = null;
	$bottom = null;
	$left   = null;
	foreach ( (array) ( isset( $t['tiles'] ) ? $t['tiles'] : array() ) as $tile ) {
		$b = isset( $tile['bbox'] ) && is_array( $tile['bbox'] ) ? array_values( $tile['bbox'] ) : array();
		if ( 4 !== count( $b ) ) {
			continue;
		}
		$top    = null === $top ? (float) $b[1] : min( $top, (float) $b[1] );
		$left   = null === $left ? (float) $b[0] : min( $left, (float) $b[0] );
		$bottom = null === $bottom ? (float) $b[1] + (float) $b[3] : max( $bottom, (float) $b[1] + (float) $b[3] );
	}
	if ( null !== $top ) {
		$layout['grid'] = array( $left, $top, 1120.0, round( $bottom - $top, 1 ) ); // Frame 114472 is 1120 wide (§8.1).
	}
	return $layout;
}

/**
 * Trail term meta of the 2026-11-17 seed (16 trails · elephant keeps its values): H1 · cover · layout · tiles without
 * a page · animal guide card. The description is a term field (sh_import_trail_description()).
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $t    Seed trail.
 * @return array<string, mixed>
 */
function sh_import_trail_meta_1117( array $seed, array $t ): array {
	$slug = (string) $t['slug'];
	$name = (string) $t['name'];
	$meta = array();
	if ( 'elephant' !== $slug ) {
		$meta['sh_title_on_trail_page'] = isset( $t['title_on_trail_page'] ) ? (string) $t['title_on_trail_page'] : null;
		$meta['sh_cover_image']         = sh_imp_1117_image( isset( $t['cover_crop_ref'] ) ? $t['cover_crop_ref'] : null, SH_IMPORT_OBJECTS_1117, sh_import_spec_trail_cover(), $name . ' trail cover' );
		$layout                         = sh_import_trail_layout_1117( $t );
		$meta['sh_trail_layout']        = null !== $layout ? (string) wp_json_encode( $layout, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : null;
		foreach ( array_slice( sh_import_trail_static_tiles( $seed, $t ), 0, 3 ) as $i => $st ) {
			$n                                       = $i + 1;
			$meta[ "sh_static_tile_{$n}_position" ]    = $st['position'];
			$meta[ "sh_static_tile_{$n}_image" ]       = sh_imp_1117_image( $st['image'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile(), "$name tile {$st['position']}", $st['label'] );
			$meta[ "sh_static_tile_{$n}_image_hover" ] = sh_imp_1117_image( $st['hover'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover(), "$name tile {$st['position']} hover", $st['label'] );
			$meta[ "sh_static_tile_{$n}_label" ]       = '' !== $st['label'] ? $st['label'] : null;
		}
	}
	foreach ( (array) ( isset( $seed['guides']['cards'] ) ? $seed['guides']['cards'] : array() ) as $c ) {
		if ( ! is_array( $c ) || ( $c['trail_slug'] ?? '' ) !== $slug ) {
			continue;
		}
		$meta['sh_guide_name']   = (string) $c['name'];
		$meta['sh_guide_words']  = isset( $c['words_text'] ) ? (string) $c['words_text'] : implode( ' · ', (array) $c['words'] );
		$meta['sh_guide_body']   = isset( $c['body_html'] ) ? sh_import_html_to_text( (string) $c['body_html'] ) : (string) $c['body'];
		$meta['sh_guide_follow'] = (string) $c['follow_text'];
		$meta['sh_guide_card']   = sh_imp_1117_image( isset( $c['card_image']['file'] ) ? $c['card_image']['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec_guide_card(), (string) $c['name'] . ' card', (string) $c['name'] );
	}
	return array_filter( $meta, static fn( $v ): bool => null !== $v );
}

/**
 * Home page fields of the animal guide section (D48 · guides.json "section").
 *
 * @param array<string, mixed> $seed Seed files.
 * @return array<string, mixed>
 */
function sh_import_home_meta_1117( array $seed ): array {
	$s = isset( $seed['guides']['section'] ) && is_array( $seed['guides']['section'] ) ? $seed['guides']['section'] : array();
	if ( ! $s ) {
		return array();
	}
	return array(
		'sh_home_guide_title'        => (string) $s['title'],
		'sh_home_guide_opening'      => (string) $s['opening'],
		'sh_home_guide_heritage'     => (string) $s['heritage'],
		'sh_home_guide_question'     => (string) $s['question'],
		'sh_home_guide_instructions' => (string) $s['instructions'],
		'sh_home_guide_cta'          => (string) $s['cta'],
		'sh_home_guide_card_back'    => sh_imp_1117_image( isset( $s['card_back']['file'] ) ? $s['card_back']['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec_card_back(), 'Animal guide card back', 'Pick a card' ),
	);
}

/**
 * Attachment(s) of one Introduction image (sh_import_intro_images() entry): the media/out file of its Drive original
 * (manifest key intro-1117/<drive_file> · still · animation = poster + animated WebP) · else the Figma fill in
 * design/assets/pages-1117 shrunk to 2× (web spec).
 *
 * @param array<string, mixed> $im       Entry.
 * @param array<string, mixed> $manifest Manifest.
 * @return array{still:int,animation:int}
 */
function sh_imp_intro_image( array $im, array $manifest ): array {
	$title = 'Introduction ' . $im['label'];
	$entry = '' !== $im['drive_file'] ? sh_import_manifest_by_key( $manifest, 'intro-1117/' . $im['drive_file'] ) : null;
	if ( null !== $entry ) {
		return sh_imp_manifest_images( $entry, $title );
	}
	return array(
		'still'     => (int) sh_imp_1117_image( $im['file'], SH_IMPORT_PAGES_1117, $im['spec'], $title ),
		'animation' => 0,
	);
}

/**
 * Introduction page fields (D50 · intro-2026-11.json): header texts + emblem + head layout, and per chapter title ·
 * accent · map (+ animation) · text slots 1..7 · image slots 1..5 (+ animation) · layout JSON (blocks → slots).
 *
 * @param array<string, mixed> $seed     Seed files.
 * @param array<string, mixed> $manifest Manifest.
 * @return array<string, mixed>
 */
function sh_import_intro_meta_1117( array $seed, array $manifest ): array {
	$in = isset( $seed['intro-2026-11'] ) && is_array( $seed['intro-2026-11'] ) ? $seed['intro-2026-11'] : array();
	if ( ! $in ) {
		return array();
	}
	$h    = $in['header'];
	$meta = array(
		'sh_intro_head_title'  => (string) $h['title']['text'],
		'sh_intro_head_credit' => (string) $h['credit']['text'],
		'sh_intro_env_heading' => (string) $in['environment_heading']['text'],
	);
	foreach ( array_values( (array) $h['paragraphs'] ) as $i => $p ) {
		if ( $i < 5 ) {
			$meta[ 'sh_intro_head_paragraph_' . ( $i + 1 ) ] = (string) $p['text'];
		}
	}
	$strip = static function ( array $b ): array { // Geometry only: texts / file names live in the fields.
		return array_diff_key( $b, array_flip( array( 'text', 'html', 'figma_fill_ref', 'figma_fill_file', 'figma_fill_px', 'drive_file', 'drive_kind', 'file', 'note', 'animated', 'node' ) ) );
	};
	$decor  = array();
	$images = array();
	foreach ( sh_import_intro_images( $in ) as $im ) {
		$att = sh_imp_intro_image( $im, $manifest );
		if ( -1 === $im['slot'] ) {
			$meta['sh_intro_head_emblem'] = $att['still'];
		} elseif ( -2 === $im['slot'] ) {
			$decor[ $im['chapter'] ][ $im['decor'] ] = $att['still'];
		} else {
			$images[ $im['chapter'] ][ $im['slot'] ] = $att;
		}
	}
	$head = array(
		'header'              => array_diff_key( $h, array_flip( array( 'title', 'paragraphs', 'credit', 'emblem', 'decor', 'node' ) ) ) + array(
			'title'      => $strip( $h['title'] ),
			'paragraphs' => array_map( $strip, array_values( (array) $h['paragraphs'] ) ),
			'credit'     => $strip( $h['credit'] ),
			'emblem'     => $strip( $h['emblem'] ),
			'decor'      => array(),
		),
		'environment_heading' => $strip( $in['environment_heading'] ),
		'map_ui'              => $in['map_ui'],
		'url'                 => $in['url'],
	);
	foreach ( (array) ( isset( $h['decor'] ) ? $h['decor'] : array() ) as $k => $d ) {
		if ( is_array( $d ) && isset( $d['rel'] ) ) {
			$head['header']['decor'][ $k ] = array(
				'rel'       => $d['rel'],
				'image_ref' => isset( $decor[0][ $k ] ) ? $decor[0][ $k ] : 0,
			);
		}
	}
	$meta['sh_intro_head_layout'] = (string) wp_json_encode( $head, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

	foreach ( (array) $in['chapters'] as $c ) {
		$n                                   = (int) $c['n'];
		$meta[ "sh_intro_ch{$n}_title" ]     = (string) $c['title'];
		$meta[ "sh_intro_ch{$n}_accent" ]    = (string) $c['accent_color'];
		$map                                 = isset( $images[ $n ][0] ) ? $images[ $n ][0] : array( 'still' => 0, 'animation' => 0 );
		$meta[ "sh_intro_ch{$n}_map_image" ] = $map['still'];
		if ( $map['animation'] ) {
			$meta[ "sh_intro_ch{$n}_map_animation" ] = $map['animation'];
		}
		$layout = array(
			'n'           => $n,
			'slug'        => $c['slug'],
			'hash'        => $c['hash'],
			'button'      => $c['button'],
			'frame'       => $c['frame'],
			'frame_h'     => $c['frame_h'],
			'map'         => $strip( $c['map'] ),
			'map_buttons' => $c['map_buttons'],
			'arrows'      => $c['arrows'],
			'sections'    => array(),
		);
		$ts = 0;
		$is = 0;
		foreach ( (array) $c['sections'] as $si => $s ) {
			$sec = array_diff_key( $s, array( 'blocks' => 1 ) ) + array( 'blocks' => array() );
			foreach ( (array) $s['blocks'] as $bi => $b ) {
				$block = $strip( $b );
				if ( 'text' === $b['kind'] ) {
					++$ts;
					$block['slot'] = $ts;
					if ( $ts <= 7 ) {
						$meta[ "sh_intro_ch{$n}_text_{$ts}" ] = sh_import_html_to_text( (string) $b['html'] );
					}
				} elseif ( ! empty( $b['decor'] ) ) {
					$block['kind']      = 'decor';
					$block['image_ref'] = isset( $decor[ $n ][ "$si.$bi" ] ) ? $decor[ $n ][ "$si.$bi" ] : 0;
				} else {
					++$is;
					$block['slot'] = $is;
					$att           = isset( $images[ $n ][ $is ] ) ? $images[ $n ][ $is ] : array( 'still' => 0, 'animation' => 0 );
					if ( $is <= 5 ) {
						$meta[ "sh_intro_ch{$n}_image_{$is}" ] = $att['still'];
						if ( $att['animation'] ) {
							$meta[ "sh_intro_ch{$n}_image_{$is}_animation" ] = $att['animation'];
						}
					}
				}
				$sec['blocks'][] = $block;
			}
			$layout['sections'][] = $sec;
		}
		if ( $ts > 7 || $is > 5 ) {
			sh_imp_warn( "intro chapter $n has $ts texts / $is images — more than the 7 / 5 fields (CONTENT-MODEL §7)" );
		}
		$meta[ "sh_intro_ch{$n}_layout" ] = (string) wp_json_encode( $layout, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	}
	return $meta;
}

/**
 * Site options of D47 (header logo · footer logos / text). sh_logo_unesco_footer stays 0 = the theme SVG.
 *
 * @param array<string, mixed> $seed Seed files.
 * @return array<string, mixed>
 */
function sh_import_site_options_1117( array $seed ): array {
	$b = isset( $seed['site']['brand'] ) ? $seed['site']['brand'] : array();
	return array_filter(
		array(
			'sh_logo_header'         => sh_imp_1117_image( isset( $b['header_logo']['file'] ) ? $b['header_logo']['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec_logo_header( $seed ), 'Shared Heritage logo', isset( $b['header_logo']['alt'] ) ? (string) $b['header_logo']['alt'] : '' ),
			'sh_logo_temasek'        => sh_imp_1117_image( isset( $b['footer']['temasek']['file'] ) ? $b['footer']['temasek']['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec( 208, 60 ), 'Temasek Foundation logo', isset( $b['footer']['temasek']['alt'] ) ? (string) $b['footer']['temasek']['alt'] : '' ),
			'sh_footer_support_text' => isset( $b['footer']['temasek']['caption']['text'] ) ? (string) $b['footer']['temasek']['caption']['text'] : null,
		),
		static fn( $v ): bool => null !== $v
	);
}

/**
 * Menu item args of an external "nav_primary_links" entry (About the Project · D47): custom link, new tab, rel.
 *
 * @param array<string, mixed> $seed  Seed files.
 * @param string               $label Item label.
 * @return array<string, mixed>|null null = not an external link in the seed.
 */
function sh_import_menu_link_1117( array $seed, string $label ): ?array {
	$l = isset( $seed['site']['nav_primary_links'][ $label ] ) ? $seed['site']['nav_primary_links'][ $label ] : null;
	if ( ! is_array( $l ) || empty( $l['url'] ) ) {
		return null;
	}
	return array(
		'menu-item-title'  => wp_slash( $label ),
		'menu-item-url'    => esc_url_raw( (string) $l['url'] ),
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
		'menu-item-target' => isset( $l['target'] ) ? (string) $l['target'] : '_blank',
		'menu-item-xfn'    => isset( $l['rel'] ) ? (string) $l['rel'] : 'noopener noreferrer',
	);
}

/**
 * Primary menu: add the external items of the seed (About the Project) when the menu has no item with that title or
 * URL — appended at the end · nothing removed or reordered (admin order kept). Returns items added.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_menu_1117( array $seed ): int {
	$menu = wp_get_nav_menu_object( 'Primary' );
	if ( ! $menu ) {
		return 0;
	}
	$items = wp_get_nav_menu_items( $menu->term_id, array( 'post_status' => 'any' ) );
	$items = is_array( $items ) ? $items : array();
	$added = 0;
	foreach ( (array) $seed['site']['nav_primary'] as $label ) {
		$args = sh_import_menu_link_1117( $seed, (string) $label );
		if ( null === $args ) {
			continue;
		}
		foreach ( $items as $it ) {
			if ( 0 === strcasecmp( trim( wp_strip_all_tags( (string) $it->title ) ), (string) $label ) || untrailingslashit( (string) $it->url ) === untrailingslashit( (string) $args['menu-item-url'] ) ) {
				continue 2; // Already there (maybe edited by an admin): keep it.
			}
		}
		// After the last item: max(menu_order) + 1 (orders may have gaps after admin edits · count + 1 could collide).
		// The stored menu_order (wp_get_nav_menu_items() renumbers its result 1..n, gaps hidden).
		$last                       = $items ? max( array_map( static fn( $it ): int => (int) get_post_field( 'menu_order', (int) $it->ID, 'raw' ), $items ) ) : 0;
		$args['menu-item-position'] = $last + 1 + $added;
		$res                        = wp_update_nav_menu_item( $menu->term_id, 0, $args );
		if ( is_wp_error( $res ) ) {
			throw new RuntimeException( 'menu item ' . $label . ': ' . $res->get_error_message() );
		}
		++$added;
		sh_imp_count( 'created', 'menu_item' );
	}
	foreach ( (array) $seed['site']['nav_primary'] as $label ) { // Report seed items the admin renamed / removed.
		$found = false;
		foreach ( $items as $it ) {
			$found = $found || 0 === strcasecmp( trim( wp_strip_all_tags( (string) $it->title ) ), (string) $label );
		}
		if ( ! $found && null === sh_import_menu_link_1117( $seed, (string) $label ) ) {
			sh_imp_keep( 'menu Primary: no item "' . $label . '" (renamed / removed in admin · not added back)' );
		}
	}
	return $added;
}
