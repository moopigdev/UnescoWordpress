<?php
/**
 * Importer: which seed objects are imported, their Final frame, and which design/assets file a style "*_ref"
 * value points to. Pure PHP (no WordPress) — shared by the import (objects.php · media.php · refresh.php) and by
 * bundle-files.php, which scripts/deploy.sh runs on the local machine to list every file the import reads.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || defined( 'SH_IMPORT_CLI' ) || exit;

/**
 * Seed objects in scope (D7), ordered by elephant trail order.
 *
 * @param array<string, mixed> $seed Seed files.
 * @return array<int, array<string, mixed>>
 */
function sh_import_scope_objects( array $seed ): array {
	$list = array_values( array_filter( $seed['objects']['objects'], static fn( $o ) => ! empty( $o['trail_order']['elephant'] ) ) );
	usort( $list, static fn( $a, $b ) => (int) $a['trail_order']['elephant'] <=> (int) $b['trail_order']['elephant'] );
	return $list;
}

/**
 * The Figma record of the Final page ("Page 2  Version #Final …") — the frame the detail page must match.
 *
 * @param array<string, mixed> $o Seed object.
 * @return array<string, mixed>
 */
function sh_import_final_frame( array $o ): array {
	foreach ( $o['figma'] as $f ) {
		if ( str_contains( (string) $f['page'], 'Final' ) ) {
			return $f;
		}
	}
	return $o['figma'][0];
}

/**
 * Every "*_ref" key (any depth) with a non-empty string value: list of [path (keys from the root), value].
 *
 * @param array<int|string, mixed> $node Style (sub)tree.
 * @param array<int, int|string>   $path Keys above $node.
 * @return array<int, array{0: array<int, int|string>, 1: string}>
 */
function sh_import_style_refs( array $node, array $path = array() ): array {
	$out = array();
	foreach ( $node as $k => $v ) {
		$here = array_merge( $path, array( $k ) );
		if ( is_string( $k ) && str_ends_with( $k, '_ref' ) && is_string( $v ) && '' !== $v ) {
			$out[] = array( $here, $v );
		} elseif ( is_array( $v ) ) {
			$out = array_merge( $out, sh_import_style_refs( $v, $here ) );
		}
	}
	return $out;
}

/**
 * File name in $dir that a "*_ref" value points to ('' = none): image-map "files" entry of the value, the value
 * itself, or the value + png/jpg/jpeg/webp. Never leaves $dir (basename · no dot-files).
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $value Value of the *_ref key.
 * @param string               $dir   design/assets directory.
 */
function sh_import_ref_file( array $map, string $value, string $dir ): string {
	$name = basename( $value );
	$cand = array();
	if ( isset( $map['files'][ $name ] ) ) {
		$cand[] = (string) $map['files'][ $name ];
	}
	$cand[] = $name;
	foreach ( array( 'png', 'jpg', 'jpeg', 'webp' ) as $ext ) {
		$cand[] = $name . '.' . $ext;
	}
	foreach ( $cand as $file ) {
		if ( '' !== $file && '.' !== $file[0] && ! str_contains( $file, '/' ) && is_file( $dir . '/' . $file ) ) {
			return $file;
		}
	}
	return '';
}

/**
 * File (relative to $dir) that a timeline card's "line_art_ref" points to ('' = none): first design/assets/lineart/
 * (DESIGN-SPEC §4.5c.3b · design-dev export) → "lineart/<file>", else the same rule as any "*_ref" at the top of $dir.
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $value Value of items[].line_art_ref.
 * @param string               $dir   design/assets directory.
 */
function sh_import_line_art_file( array $map, string $value, string $dir ): string {
	$file = sh_import_ref_file( array(), $value, $dir . '/lineart' );
	return '' !== $file ? 'lineart/' . $file : sh_import_ref_file( $map, $value, $dir );
}

/**
 * Photo(s) under the line of a timeline card (D39/D41 · D45): field => ref/file name ('' never returned).
 * image-map.json `timeline_items.<card_order>.below` (list: [0] → sh_below_image · [1] → sh_below_image_2) when the
 * key exists — D45 swapped cards 1·7·9 by hand there, like their images on the line — else seed items[].below_ref
 * (+ below_ref_2). One rule for the import, the refresh step and the deploy bundle.
 *
 * @param array<string, mixed> $it  Seed items[] entry.
 * @param array<string, mixed> $map image-map.json.
 * @return array<string, string>
 */
function sh_import_timeline_below_refs( array $it, array $map ): array {
	$n    = isset( $it['card_order'] ) ? (string) (int) $it['card_order'] : '';
	$list = isset( $map['timeline_items'][ $n ] ) && is_array( $map['timeline_items'][ $n ] ) && array_key_exists( 'below', $map['timeline_items'][ $n ] )
		? array_values( (array) $map['timeline_items'][ $n ]['below'] )
		: array( isset( $it['below_ref'] ) ? $it['below_ref'] : '', isset( $it['below_ref_2'] ) ? $it['below_ref_2'] : '' );
	$out  = array();
	foreach ( array( 'sh_below_image', 'sh_below_image_2' ) as $i => $field ) {
		if ( isset( $list[ $i ] ) && is_string( $list[ $i ] ) && '' !== $list[ $i ] ) {
			$out[ $field ] = $list[ $i ];
		}
	}
	return $out;
}

/**
 * Every design/assets file the import reads: image-map "files" + the "*_ref" files of the in-scope objects'
 * Final frames (the same objects and frames sh_import_objects() / sh_import_layout() use) + the timeline cards'
 * "line_art_ref" files (sh_import_timeline() · refresh 2026-11-11-line-art; "lineart/<file>" = one sub-folder) + their
 * photos under the line (D39/D45 sh_below_image (+ _2) · sh_import_timeline_below_refs(); top of $dir).
 *
 * @param array<string, mixed> $seed Seed files (needs "objects" · optional "timeline-elephant").
 * @param array<string, mixed> $map  image-map.json.
 * @param string               $dir  design/assets directory.
 * @return array{map: string[], refs: string[], missing: string[], unsafe: string[]}
 *         map = image-map files · refs = *_ref files not already in map · missing = referenced but not in $dir.
 */
function sh_import_bundle_files( array $seed, array $map, string $dir ): array {
	$out = array(
		'map'     => array(),
		'refs'    => array(),
		'missing' => array(),
		'unsafe'  => array(),
	);
	foreach ( (array) ( isset( $map['files'] ) ? $map['files'] : array() ) as $file ) {
		$file = (string) $file;
		if ( '' === $file || '.' === $file[0] || str_contains( $file, '/' ) ) {
			$out['unsafe'][] = $file;
		} elseif ( ! is_file( $dir . '/' . $file ) ) {
			$out['missing'][] = 'image-map: ' . $file;
		} else {
			$out['map'][ $file ] = $file;
		}
	}
	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		foreach ( sh_import_style_refs( (array) sh_import_final_frame( $o )['style'] ) as $ref ) {
			$file = sh_import_ref_file( $map, $ref[1], $dir );
			if ( '' === $file ) {
				$out['missing'][] = '*_ref ' . $o['slug'] . ' ' . implode( '.', $ref[0] ) . ': ' . $ref[1];
			} elseif ( ! isset( $out['map'][ $file ] ) ) {
				$out['refs'][ $file ] = $file;
			}
		}
	}
	$items = isset( $seed['timeline-elephant']['items'] ) ? (array) $seed['timeline-elephant']['items'] : array();
	foreach ( $items as $it ) {
		$value = isset( $it['line_art_ref'] ) && is_string( $it['line_art_ref'] ) ? $it['line_art_ref'] : '';
		if ( '' === $value ) {
			continue;
		}
		$file = sh_import_line_art_file( $map, $value, $dir );
		if ( '' === $file ) {
			$out['missing'][] = 'line_art_ref card ' . ( isset( $it['card_order'] ) ? (int) $it['card_order'] : 0 ) . ': ' . $value;
		} elseif ( ! isset( $out['map'][ $file ] ) ) {
			$out['refs'][ $file ] = $file;
		}
	}
	foreach ( $items as $it ) { // D39/D45 photo(s) under the line → sh_below_image (+ _2) (file at the top of $dir).
		foreach ( sh_import_timeline_below_refs( $it, $map ) as $value ) {
			$file = sh_import_ref_file( $map, $value, $dir );
			if ( '' === $file ) {
				$out['missing'][] = 'below_ref card ' . ( isset( $it['card_order'] ) ? (int) $it['card_order'] : 0 ) . ': ' . $value;
			} elseif ( ! isset( $out['map'][ $file ] ) ) {
				$out['refs'][ $file ] = $file;
			}
		}
	}
	foreach ( array( 'map', 'refs', 'missing', 'unsafe' ) as $k ) {
		$out[ $k ] = array_values( array_unique( $out[ $k ] ) );
		sort( $out[ $k ] );
	}
	return $out;
}

/**
 * Sub-folders of design/assets the import may read from (one level only · design-dev exports that are not a Figma
 * imageRef). scripts/deploy.sh accepts "<sub>/<file>" bundle names only for these (+ lineart/ of the timeline).
 *
 * @return string[]
 */
function sh_import_asset_subdirs(): array {
	return array( 'zone-banners-2026-11', 'zone4-animals', 'zone5' );
}

/**
 * File (relative to $dir) a seed asset value points to ('' = none). Accepts "design/assets/<sub>/<file>",
 * "<sub>/<file>" (sub in sh_import_asset_subdirs(), one level, no dot-files) or a top-level file / Figma imageRef
 * (sh_import_ref_file()). Text after the first space is a note in the seed ("file.png (4096×1971 …)") and is ignored.
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $value Seed value.
 * @param string               $dir   design/assets directory.
 */
function sh_import_seed_asset_file( array $map, string $value, string $dir ): string {
	$value = (string) preg_replace( '/\s.*$/s', '', trim( $value ) );
	if ( str_starts_with( $value, 'design/assets/' ) ) {
		$value = substr( $value, strlen( 'design/assets/' ) );
	}
	if ( '' === $value ) {
		return '';
	}
	if ( ! str_contains( $value, '/' ) ) {
		return sh_import_ref_file( $map, $value, $dir );
	}
	$parts = explode( '/', $value );
	if ( 2 !== count( $parts ) || ! in_array( $parts[0], sh_import_asset_subdirs(), true ) || '' === $parts[1] || '.' === $parts[1][0] ) {
		return '';
	}
	return is_file( $dir . '/' . $value ) ? $value : '';
}

/**
 * Every seed asset value the 2026-11 import reads (zones.json banners · Zone 5 page images · zone4-chooser.json):
 * list of [label, value]. sh_import_zones() / sh_import_trails() / refresh-2026-11.php read the same keys.
 *
 * @param array<string, mixed> $seed Seed files (optional "zones", "zone4-chooser").
 * @return array<int, array{0: string, 1: string}>
 */
function sh_import_seed_asset_values( array $seed ): array {
	$out = array();
	$add = static function ( string $label, $value ) use ( &$out ): void {
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			$out[] = array( $label, $value );
		}
	};
	foreach ( (array) ( isset( $seed['zones']['zones'] ) ? $seed['zones']['zones'] : array() ) as $z ) {
		$n = 'zone ' . ( isset( $z['number'] ) ? (int) $z['number'] : 0 );
		$add( $n . ' banner_image.file', isset( $z['banner_image']['file'] ) ? $z['banner_image']['file'] : null );
		foreach ( sh_import_zone_page_images( $z ) as $k => $v ) {
			$add( $n . ' page.' . $k, $v );
		}
	}
	$ch = isset( $seed['zone4-chooser'] ) && is_array( $seed['zone4-chooser'] ) ? $seed['zone4-chooser'] : array();
	$add( 'chooser cover_bg.file_cropped', isset( $ch['_meta']['cover_bg']['file_cropped'] ) ? $ch['_meta']['cover_bg']['file_cropped'] : null );
	$add( 'chooser zone_bar.file_cropped', isset( $ch['_meta']['zone_bar']['file_cropped'] ) ? $ch['_meta']['zone_bar']['file_cropped'] : null );
	foreach ( (array) ( isset( $ch['animals'] ) ? $ch['animals'] : array() ) as $a ) {
		$add( 'chooser ' . ( isset( $a['trail_slug'] ) ? (string) $a['trail_slug'] : '?' ), isset( $a['asset'] ) ? $a['asset'] : null );
	}
	return $out;
}

/**
 * Zone page images of a seed zone ("page" object · DESIGN-SPEC §4.11 · CONTENT-MODEL §12): meta key => seed value.
 * Keys: cover · zone_bar · cards[1..4].image / image_hover · strip[1..4] · history.image / image_hover.
 *
 * @param array<string, mixed> $z Seed zone.
 * @return array<string, string> meta key (sh_…) => seed value.
 */
function sh_import_zone_page_images( array $z ): array {
	$p   = isset( $z['page'] ) && is_array( $z['page'] ) ? $z['page'] : array();
	$out = array();
	$set = static function ( string $key, $value ) use ( &$out ): void {
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			$out[ $key ] = $value;
		}
	};
	$set( 'sh_cover_image', isset( $p['cover'] ) ? $p['cover'] : null );
	$set( 'sh_banner_image', isset( $p['zone_bar'] ) ? $p['zone_bar'] : null );
	foreach ( array_values( (array) ( isset( $p['cards'] ) ? $p['cards'] : array() ) ) as $i => $c ) {
		if ( $i < 4 && is_array( $c ) ) {
			$set( 'sh_page_card_' . ( $i + 1 ) . '_image', isset( $c['image'] ) ? $c['image'] : null );
			$set( 'sh_page_card_' . ( $i + 1 ) . '_image_hover', isset( $c['image_hover'] ) ? $c['image_hover'] : null );
		}
	}
	foreach ( array_values( (array) ( isset( $p['strip'] ) ? $p['strip'] : array() ) ) as $i => $v ) {
		if ( $i < 4 ) {
			$set( 'sh_page_strip_image_' . ( $i + 1 ), $v );
		}
	}
	$set( 'sh_page_history_image', isset( $p['history']['image'] ) ? $p['history']['image'] : null );
	$set( 'sh_page_history_image_hover', isset( $p['history']['image_hover'] ) ? $p['history']['image_hover'] : null );
	return $out;
}

/**
 * Alt text of zone page images from the seed "page" object (cards[].alt · history.alt): meta key => alt.
 * Other note keys in the seed (figma_nodes · figma_target · frame · strip_nodes) are not read.
 *
 * @param array<string, mixed> $z Seed zone.
 * @return array<string, string>
 */
function sh_import_zone_page_alts( array $z ): array {
	$p   = isset( $z['page'] ) && is_array( $z['page'] ) ? $z['page'] : array();
	$out = array();
	foreach ( array_values( (array) ( isset( $p['cards'] ) ? $p['cards'] : array() ) ) as $i => $c ) {
		if ( $i < 4 && is_array( $c ) && isset( $c['alt'] ) && is_string( $c['alt'] ) ) {
			$out[ 'sh_page_card_' . ( $i + 1 ) . '_image' ]       = $c['alt'];
			$out[ 'sh_page_card_' . ( $i + 1 ) . '_image_hover' ] = $c['alt'];
		}
	}
	if ( isset( $p['history']['alt'] ) && is_string( $p['history']['alt'] ) ) {
		$out['sh_page_history_image']       = $p['history']['alt'];
		$out['sh_page_history_image_hover'] = $p['history']['alt'];
	}
	return $out;
}

/**
 * Bundle list for the seed asset values (sh_import_seed_asset_values()) — merged into sh_import_bundle_files() output
 * by bundle-files.php. Files already listed stay once; "<sub>/<file>" names keep their sub-folder.
 *
 * @param array<string, mixed>                                                      $seed Seed files.
 * @param array<string, mixed>                                                      $map  image-map.json.
 * @param string                                                                    $dir  design/assets directory.
 * @param array{map: string[], refs: string[], missing: string[], unsafe: string[]} $res  sh_import_bundle_files() result.
 * @return array{map: string[], refs: string[], missing: string[], unsafe: string[]}
 */
function sh_import_bundle_seed_files( array $seed, array $map, string $dir, array $res ): array {
	foreach ( sh_import_seed_asset_values( $seed ) as $v ) {
		$file = sh_import_seed_asset_file( $map, $v[1], $dir );
		if ( '' === $file ) {
			$res['missing'][] = 'seed ' . $v[0] . ': ' . $v[1];
		} elseif ( ! in_array( $file, $res['map'], true ) ) {
			$res['refs'][] = $file;
		}
	}
	foreach ( array( 'map', 'refs', 'missing', 'unsafe' ) as $k ) {
		$res[ $k ] = array_values( array_unique( $res[ $k ] ) );
		sort( $res[ $k ] );
	}
	return $res;
}

/**
 * Detail layout of a Final frame before "*_ref" values become attachment ids (objects.php sh_import_layout()):
 * frame_id + frame_name first, then the whole style · layer names of panel_children / hero shortened to 40 chars.
 *
 * @param array<string, mixed> $frame Figma record (frame_id · frame_name · style).
 * @return array<string, mixed>
 */
function sh_import_layout_base( array $frame ): array {
	$style = (array) $frame['style'];
	foreach ( array( 'panel_children', 'hero' ) as $list ) {
		foreach ( (array) ( isset( $style[ $list ] ) ? $style[ $list ] : array() ) as $i => $child ) {
			if ( is_array( $child ) && isset( $child['name'] ) ) {
				$style[ $list ][ $i ]['name'] = mb_substr( (string) $child['name'], 0, 40 );
			}
		}
	}
	return array_merge(
		array(
			'frame_id'   => $frame['frame_id'],
			'frame_name' => $frame['frame_name'],
		),
		$style
	);
}

/**
 * Fingerprint (sha256) of a detail layout that ignores how it was stored: key order, number formatting
 * (1000 = 1000.0 · 6 decimals) and "*_ref" as file name (extension dropped) or attachment id. $id_name maps an
 * attachment id to the file name it was imported from ('' = not an import attachment → kept as "id:<n>").
 * Same value from the seed (build-legacy-values.py, CLI) and from a site (refresh-2026-11.php).
 *
 * @param array<int|string, mixed> $layout  Layout array (decoded sh_layout or sh_import_layout_base()).
 * @param callable|null            $id_name fn( int $id ): string.
 */
function sh_import_layout_fingerprint( array $layout, ?callable $id_name = null ): string {
	$norm = static function ( array $node ) use ( &$norm, $id_name ): array {
		foreach ( $node as $k => $v ) {
			if ( is_string( $k ) && str_ends_with( $k, '_ref' ) && ( is_int( $v ) || is_string( $v ) ) ) {
				$name       = is_int( $v ) ? ( null !== $id_name ? (string) $id_name( $v ) : '' ) : basename( $v );
				$node[ $k ] = '' === $name ? 'id:' . $v : 'ref:' . preg_replace( '/\.(png|jpe?g|webp|gif)$/i', '', $name );
			} elseif ( is_array( $v ) ) {
				$node[ $k ] = $norm( $v );
			} elseif ( is_float( $v ) || is_int( $v ) ) {
				$node[ $k ] = 'n:' . rtrim( rtrim( sprintf( '%.6F', (float) $v ), '0' ), '.' );
			}
		}
		if ( ! array_is_list( $node ) ) {
			ksort( $node, SORT_STRING );
		}
		return $node;
	};
	return hash( 'sha256', (string) json_encode( $norm( $layout ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
}
