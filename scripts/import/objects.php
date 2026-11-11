<?php
/**
 * Importer: objects (D7 — the 15 objects of the elephant trail = seed objects with trail_order.elephant),
 * per-trail object order, timeline cards + Timeline page fields. Scope + Final frame: refs.php.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/refs.php';

/**
 * Detail layout JSON from the Final frame style — the whole style object (unknown keys pass through), layer names
 * shortened (they repeat the whole text). Every key ending in "_ref" (any depth) whose file exists in design/assets
 * becomes an attachment id (design-dev crops "<ref>.crop-<node>.png"); no file → value kept, warning only.
 *
 * @param array<string, mixed> $frame Figma record.
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $title Object title (attachment titles).
 */
function sh_import_layout( array $frame, array $map, string $title ): string {
	// sh_import_layout_base() (refs.php): frame ids + style · names shortened — shared with the 2026-11 refresh fingerprint.
	return (string) wp_json_encode( sh_import_layout_refs( sh_import_layout_base( $frame ), $map, $title ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
}

/**
 * Replace "*_ref" values (any depth) by attachment ids when the file exists.
 *
 * @param array<int|string, mixed> $node  Style (sub)tree.
 * @param array<string, mixed>     $map   image-map.json.
 * @param string                   $title Object title.
 * @return array<int|string, mixed>
 */
function sh_import_layout_refs( array $node, array $map, string $title ): array {
	foreach ( $node as $k => $v ) {
		if ( is_string( $k ) && str_ends_with( $k, '_ref' ) && is_string( $v ) && '' !== $v ) {
			$id         = sh_imp_asset_ref( $map, $v, $title . ' ' . substr( $k, 0, -4 ) );
			$node[ $k ] = $id ? $id : $v;
		} elseif ( is_array( $v ) ) {
			$node[ $k ] = sh_import_layout_refs( $v, $map, $title );
		}
	}
	return $node;
}

/**
 * Hero fields of an object: manifest entry with object_slug = seed slug (kind video → WebM/poster/HEVC-alpha ·
 * kind still → the still as sh_hero_image) · otherwise the first Figma GIF frame (image-map hero_image).
 *
 * @param array<string, mixed> $manifest media manifest.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $o        Seed object.
 * @return array<string, int|bool|null>
 */
function sh_import_object_hero( array $manifest, array $map, array $o ): array {
	$slug  = (string) $o['slug'];
	$m     = isset( $map['objects'][ $slug ] ) ? $map['objects'][ $slug ] : array();
	$video = sh_imp_video( sh_imp_manifest_entry( $manifest, 'object_slug', $slug ), (string) $o['title'] );
	return array(
		'sh_hero_video_webm'      => $video['webm'],
		'sh_hero_video_hevc'      => $video['hevc'],
		'sh_hero_video_has_alpha' => $video['has_alpha'],
		'sh_hero_poster'          => $video['poster'],
		'sh_hero_image'           => $video['still'] ? $video['still'] : sh_imp_image( $map, isset( $m['hero_image'] ) ? $m['hero_image'] : null, $o['title'] . ' hero' ),
	);
}

/**
 * Objects → posts. Returns slug => post id.
 *
 * @param array<string, mixed>                    $seed     Seed files.
 * @param array<string, mixed>                    $map      image-map.json.
 * @param array<string, mixed>                    $manifest media manifest.
 * @param array<string, array{id:int,write:bool}> $trails   Trail terms.
 * @return array<string, int>
 */
function sh_import_objects( array $seed, array $map, array $manifest, array $trails ): array {
	$out = array();
	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		$slug               = (string) $o['slug'];
		$text               = sh_import_object_text_2026_11( $o ); // D40: Figma 2026-11-11 title/caption.
		list( $id, $write ) = sh_imp_post(
			'object',
			'object:' . $slug,
			array(
				'post_type'  => 'object',
				'post_title' => $text['post_title'],
				'post_name'  => $slug,
			)
		);
		$out[ $slug ]       = $id;
		if ( ! $write ) {
			continue;
		}
		$term_ids = array();
		foreach ( (array) $o['trails'] as $t ) {
			if ( isset( $trails[ $t ] ) ) {
				$term_ids[] = $trails[ $t ]['id'];
			}
		}
		wp_set_object_terms( $id, $term_ids, 'trail' );

		$frame = sh_import_final_frame( $o );
		$m     = isset( $map['objects'][ $slug ] ) ? $map['objects'][ $slug ] : array();
		$ds    = isset( $o['detail_style'] ) ? $o['detail_style'] : array();
		$txt   = static fn( string $k ): string => isset( $o[ $k ] ) ? (string) $o[ $k ] : '';
		$col   = static fn( $v ): string => is_string( $v ) ? $v : '';
		$meta  = array(
			'sh_title_raw'            => $text['sh_title_raw'],
			'sh_collection'           => $txt( 'collection' ),
			'sh_thematic_section'     => $txt( 'thematic_section' ),
			'sh_period'               => $txt( 'period' ),
			'sh_place_of_production'  => $txt( 'place_of_production' ),
			'sh_material'             => $txt( 'material' ),
			'sh_dimensions'           => $txt( 'dimensions' ),
			'sh_local_name'           => $txt( 'local_name' ),
			'sh_object_biography'     => $txt( 'object_biography' ),
			'sh_caption'              => $text['sh_caption'],
			'sh_tile_image'           => sh_imp_image( $map, isset( $m['tile'] ) ? $m['tile'] : null, $o['title'] . ' tile' ),
			'sh_tile_image_hover'     => sh_imp_image( $map, isset( $m['tile_hover'] ) ? $m['tile_hover'] : null, $o['title'] . ' tile hover' ),
			'sh_panel_bg'             => $col( isset( $ds['panel_bg'] ) ? $ds['panel_bg'] : null ),
			'sh_title_color'          => $col( isset( $ds['title_color'] ) ? $ds['title_color'] : null ),
			'sh_text_color'           => $col( isset( $ds['text_color'] ) ? $ds['text_color'] : null ),
			'sh_meta_color'           => $col( isset( $frame['style']['meta_color'] ) ? $frame['style']['meta_color'] : null ),
			'sh_collection_color'     => $col( isset( $ds['collection_color'] ) ? $ds['collection_color'] : null ),
			'sh_layout'               => sh_import_layout( $frame, $map, (string) $o['title'] ),
		) + sh_import_object_hero( $manifest, $map, $o );
		$extra = isset( $m['extra_images'] ) ? array_values( (array) $m['extra_images'] ) : array();
		for ( $i = 1; $i <= 6; $i++ ) {
			$meta[ 'sh_extra_image_' . $i ] = isset( $extra[ $i - 1 ] ) ? sh_imp_image( $map, $extra[ $i - 1 ], $o['title'] . ' image ' . $i ) : 0;
		}
		sh_imp_meta( 'post', $id, $meta );
	}
	return $out;
}

/**
 * sh_object_order of each trail = imported objects sorted by seed trail_order[trail].
 *
 * @param array<string, mixed>                    $seed    Seed files.
 * @param array<string, array{id:int,write:bool}> $trails  Trail terms.
 * @param array<string, int>                      $objects slug => post id.
 */
function sh_import_trail_order( array $seed, array $trails, array $objects ): void {
	foreach ( $trails as $slug => $trail ) {
		if ( ! $trail['write'] ) {
			continue;
		}
		$ranked = array();
		foreach ( sh_import_scope_objects( $seed ) as $o ) {
			if ( isset( $o['trail_order'][ $slug ], $objects[ $o['slug'] ] ) ) {
				$ranked[ $objects[ $o['slug'] ] ] = (int) $o['trail_order'][ $slug ];
			}
		}
		asort( $ranked );
		sh_imp_meta( 'term', $trail['id'], array( 'sh_object_order' => array_keys( $ranked ) ) );
	}
}

/**
 * Timeline cards (timeline-elephant.json → trail "elephant") + Timeline page fields.
 *
 * @param array<string, mixed>                    $seed    Seed files.
 * @param array<string, mixed>                    $map     image-map.json.
 * @param array<string, array{id:int,write:bool}> $trails  Trail terms.
 * @param array<string, int>                      $objects slug => post id.
 * @param array<string, array{id:int,write:bool}> $pages   Pages.
 */
function sh_import_timeline( array $seed, array $map, array $trails, array $objects, array $pages ): void {
	$tl       = $seed['timeline-elephant'];
	$trail_id = isset( $trails['elephant'] ) ? $trails['elephant']['id'] : 0;
	$by_card  = array();
	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		if ( isset( $o['timeline']['card_order'], $objects[ $o['slug'] ] ) ) {
			$by_card[ (int) $o['timeline']['card_order'] ] = $objects[ $o['slug'] ];
		}
	}
	foreach ( $tl['items'] as $it ) {
		$n                  = (int) $it['card_order'];
		list( $id, $write ) = sh_imp_post(
			'timeline_item',
			'timeline:elephant:' . $n,
			array(
				'post_type'  => 'timeline_item',
				'post_title' => (string) $it['title'],
				'menu_order' => $n,
			)
		);
		if ( ! $write ) {
			continue;
		}
		$thumbs = isset( $map['timeline_items'][ (string) $n ]['all'] ) ? array_values( (array) $map['timeline_items'][ (string) $n ]['all'] ) : array();
		$line   = isset( $it['line_art_ref'] ) && is_string( $it['line_art_ref'] ) && '' !== $it['line_art_ref'] ? sh_imp_line_art( $map, $it['line_art_ref'], $it['title'] . ' line art' ) : 0;
		$below  = array( 'sh_below_image' => null, 'sh_below_image_2' => null ); // No ref / no file = field untouched.
		foreach ( sh_import_timeline_below_refs( $it, $map ) as $field => $value ) { // D39/D41 · D45 (image-map timeline_items.<n>.below).
			$att             = sh_imp_below_image( $map, $value, $it['title'] . ' under the line' . ( 'sh_below_image_2' === $field ? ' 2' : '' ) );
			$below[ $field ] = $att ? $att : null;
		}
		sh_imp_meta(
			'post',
			$id,
			array(
				'sh_trail'              => $trail_id,
				'sh_object'             => isset( $by_card[ $n ] ) ? $by_card[ $n ] : 0,
				'sh_card_order'         => $n,
				'sh_illustration_order' => (int) $it['illustration_order'],
				'sh_era_label'          => isset( $it['illustration_era_label_raster'] ) ? (string) $it['illustration_era_label_raster'] : '',
				'sh_card_meta'          => (string) $it['meta_raw'],
				'sh_thumbnail'          => isset( $thumbs[0] ) ? sh_imp_image( $map, $thumbs[0], $it['title'] . ' card' ) : 0,
				'sh_thumbnail_2'        => isset( $thumbs[1] ) ? sh_imp_image( $map, $thumbs[1], $it['title'] . ' card 2' ) : 0,
				'sh_below_image'        => $below['sh_below_image'], // D39/D41/D45 photo under the line: cards 1·7·9.
				'sh_below_image_2'      => $below['sh_below_image_2'], // D45: 2nd photo of the group under the line (card 9).
				'sh_line_art'           => $line ? $line : null, // D35/D36 "before" image: cards 2–5 · no ref / no file = field untouched.
			)
		);
	}
	if ( $pages['timeline']['write'] ) {
		sh_imp_meta(
			'post',
			$pages['timeline']['id'],
			array(
				'sh_timeline_trail'            => $trail_id,
				'sh_timeline_illustration'     => sh_imp_image( $map, $map['pages']['timeline']['illustration'], 'Elephant timeline illustration' ),
				'sh_timeline_reflection_title' => (string) $tl['reflection']['title'],
				'sh_timeline_reflection_body'  => (string) $tl['reflection']['body'],
			)
		);
	}
}
