<?php
/**
 * Importer, round 2026-11-17 (core-1117 · D47–D53): which seed records are imported, which design/assets and
 * media/out files they read and at what size. Pure PHP (no WordPress) — shared by the import
 * (content-2026-11-17.php · refresh-2026-11-17.php) and bundle-files.php (scripts/deploy.sh bundle list).
 *
 * Image size rule ("web spec"): a file is copied at most 2× its largest display box (DESIGN-SPEC §8/§9) —
 * spec = {box: [w, h] px after the crop, crop: [x, y, w, h] fractions of the source | null}. The deploy bundle is
 * only fitted to the box before the crop (sh_web_image_bundle_box()); the crop happens once, at import.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || defined( 'SH_IMPORT_CLI' ) || exit;

require_once __DIR__ . '/refs.php';
require_once __DIR__ . '/web-image.php';

/** Folder of the 2026-11-17 object images (design-objects-1117 · DESIGN-SPEC §8.4). */
const SH_IMPORT_OBJECTS_1117 = 'objects-1117';
/** Folder of the 2026-11-17 page images (design-pages-1117 · DESIGN-SPEC §9). */
const SH_IMPORT_PAGES_1117 = 'pages-1117';

/**
 * Slugs of the 17 trails (seed trails.json, without "not_a_trail").
 *
 * @param array<string, mixed> $seed Seed files (needs "trails").
 * @return string[]
 */
function sh_import_trail_slugs( array $seed ): array {
	$out = array();
	foreach ( (array) ( isset( $seed['trails']['trails'] ) ? $seed['trails']['trails'] : array() ) as $t ) {
		if ( is_array( $t ) && empty( $t['not_a_trail'] ) && isset( $t['slug'] ) ) {
			$out[] = (string) $t['slug'];
		}
	}
	return $out;
}

/**
 * Seed object held back (status "hold_*" · P-306): imported as a draft, never published by the import.
 *
 * @param array<string, mixed> $o Seed object.
 */
function sh_import_object_hold( array $o ): bool {
	foreach ( (array) ( isset( $o['status'] ) ? $o['status'] : array() ) as $s ) {
		if ( is_string( $s ) && str_starts_with( $s, 'hold_' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Seed objects of every trail (D49 replaces D7): a position in a real trail (trail_order, "etc" is not a trail) or
 * held back (hold_* → draft). Elephant objects first in elephant order, then A–Z by slug (stable).
 *
 * @param array<string, mixed> $seed Seed files ("objects" · "trails" optional).
 * @return array<int, array<string, mixed>>
 */
function sh_import_all_objects( array $seed ): array {
	$trails = isset( $seed['trails'] ) ? sh_import_trail_slugs( $seed ) : array();
	$list   = array();
	foreach ( (array) $seed['objects']['objects'] as $o ) {
		$real = false;
		foreach ( (array) ( isset( $o['trail_order'] ) ? $o['trail_order'] : array() ) as $t => $pos ) {
			$real = $real || ( 'etc' !== $t && (int) $pos > 0 && ( ! $trails || in_array( (string) $t, $trails, true ) ) );
		}
		if ( $real || sh_import_object_hold( $o ) ) {
			$list[] = $o;
		}
	}
	usort(
		$list,
		static function ( array $a, array $b ): int {
			$ea = isset( $a['trail_order']['elephant'] ) ? (int) $a['trail_order']['elephant'] : PHP_INT_MAX;
			$eb = isset( $b['trail_order']['elephant'] ) ? (int) $b['trail_order']['elephant'] : PHP_INT_MAX;
			return $ea <=> $eb ?: strcmp( (string) $a['slug'], (string) $b['slug'] );
		}
	);
	return $list;
}

/**
 * Figma record whose style is the object's detail layout: the Final page (elephant) · else is_primary · else the
 * frame_id = primary_frame · else the first.
 *
 * @param array<string, mixed> $o Seed object.
 * @return array<string, mixed>
 */
function sh_import_primary_frame( array $o ): array {
	$figma = array_values( (array) $o['figma'] );
	foreach ( $figma as $f ) {
		if ( str_contains( (string) $f['page'], 'Final' ) ) {
			return $f;
		}
	}
	foreach ( $figma as $f ) {
		if ( ! empty( $f['is_primary'] ) ) {
			return $f;
		}
	}
	foreach ( $figma as $f ) {
		if ( isset( $o['primary_frame'], $f['frame_id'] ) && $f['frame_id'] === $o['primary_frame'] ) {
			return $f;
		}
	}
	return $figma[0];
}

/**
 * True for an object laid out with the generic detail template (DESIGN-SPEC §8.3).
 *
 * @param array<string, mixed> $o Seed object.
 */
function sh_import_object_generic( array $o ): bool {
	return isset( $o['detail_style_mode'] ) && 'generic' === $o['detail_style_mode'];
}

/**
 * Web spec (see file header).
 *
 * @param float                  $w    Display width.
 * @param float                  $h    Display height.
 * @param array<int, float>|null $crop Crop fractions [x, y, w, h] or null.
 * @return array{box: array{0:int,1:int}, crop: array<int, float>|null}
 */
function sh_import_spec( float $w, float $h, ?array $crop = null ): array {
	return array(
		'box'  => array( (int) ceil( 2 * $w ), (int) ceil( 2 * $h ) ),
		'crop' => $crop,
	);
}

/**
 * Crop fractions of a seed "crop" object ({x,y,w,h}) or null when it is the whole image.
 *
 * @param mixed $c Seed crop.
 * @return array<int, float>|null
 */
function sh_import_seed_crop( $c ): ?array {
	if ( ! is_array( $c ) || ! isset( $c['x'], $c['y'], $c['w'], $c['h'] ) ) {
		return null;
	}
	$r = array( (float) $c['x'], (float) $c['y'], (float) $c['w'], (float) $c['h'] );
	if ( $r[2] <= 0 || $r[3] <= 0 || ( $r[0] <= 0.0005 && $r[1] <= 0.0005 && $r[2] >= 0.999 && $r[3] >= 0.999 ) ) {
		return null;
	}
	return $r;
}

/**
 * File (relative to $dir) of a 2026-11-17 seed image value ('' = none): "design/assets/…" / "<allowed sub>/<file>" /
 * a bare name looked up in $sub first (objects-1117 for the object seeds · pages-1117 for page seeds' "guides/…",
 * "fills/…" values) then at the top of $dir (Figma imageRef rule).
 *
 * @param string $value Seed value.
 * @param string $dir   design/assets directory.
 * @param string $sub   Folder to try first ('' = none).
 */
function sh_import_1117_file( string $value, string $dir, string $sub ): string {
	$value = (string) preg_replace( '/\s.*$/s', '', trim( $value ) );
	if ( str_starts_with( $value, 'design/assets/' ) ) {
		$value = substr( $value, strlen( 'design/assets/' ) );
	}
	if ( '' === $value ) {
		return '';
	}
	if ( '' !== $sub && ! str_starts_with( $value, $sub . '/' ) ) {
		$try = sh_import_seed_asset_file( array(), $sub . '/' . $value, $dir );
		if ( '' !== $try ) {
			return $try;
		}
	}
	return sh_import_seed_asset_file( array(), $value, $dir );
}

/**
 * Object tile images: [trail slug => {image, hover}] — the seed's main tiles (tile_image_ref · trail = first trail
 * with a position, i.e. the trail of the primary frame) + tiles_by_trail of every trail.
 *
 * @param array<string, mixed> $o Seed object.
 * @return array<string, array{image:string,hover:string}>
 */
function sh_import_object_tiles( array $o ): array {
	$out = array();
	foreach ( (array) ( isset( $o['tiles_by_trail'] ) ? $o['tiles_by_trail'] : array() ) as $t => $v ) {
		if ( is_array( $v ) && ! empty( $v['tile_image_ref'] ) ) {
			$out[ (string) $t ] = array(
				'image' => (string) $v['tile_image_ref'],
				'hover' => isset( $v['tile_image_hover_ref'] ) ? (string) $v['tile_image_hover_ref'] : '',
			);
		}
	}
	return $out;
}

/**
 * Main trail of an object (its tiles go to sh_tile_image): trail of the primary frame for a generic object ·
 * "elephant" for the elephant records (their tiles come from image-map) · else the first trail.
 *
 * @param array<string, mixed> $o Seed object.
 */
function sh_import_object_main_trail( array $o ): string {
	if ( ! empty( $o['trail_order']['elephant'] ) ) {
		return 'elephant';
	}
	$f = sh_import_primary_frame( $o );
	if ( ! empty( $f['trail'] ) ) {
		return (string) $f['trail'];
	}
	$t = (array) $o['trails'];
	return $t ? (string) $t[0] : '';
}

/**
 * Seed trail tiles that are not an imported, published object (shown, not a link · DESIGN-SPEC §8.2): object_slug
 * null (Makara 1/3) or an object held back (hold_* — Rooster #11). [{position, image, hover, label}].
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $t    Seed trail.
 * @return array<int, array{position:int,image:string,hover:string,label:string}>
 */
function sh_import_trail_static_tiles( array $seed, array $t ): array {
	$hold = array();
	foreach ( (array) $seed['objects']['objects'] as $o ) {
		if ( sh_import_object_hold( $o ) ) {
			$hold[ (string) $o['slug'] ] = true;
		}
	}
	$out = array();
	foreach ( (array) ( isset( $t['tiles'] ) ? $t['tiles'] : array() ) as $tile ) {
		$slug = isset( $tile['object_slug'] ) && is_string( $tile['object_slug'] ) ? $tile['object_slug'] : '';
		if ( '' !== $slug && ! isset( $hold[ $slug ] ) ) {
			continue;
		}
		$out[] = array(
			'position' => (int) $tile['order'],
			'image'    => (string) $tile['tile_image_ref'],
			'hover'    => isset( $tile['tile_image_hover_ref'] ) ? (string) $tile['tile_image_hover_ref'] : '',
			'label'    => '' === $slug && isset( $tile['title_note'] ) ? (string) $tile['title_note'] : '',
		);
	}
	return $out;
}

/**
 * Size kept as it is (design-dev crops are already 2×) — only the PNG → JPEG rule of web-image.php applies.
 *
 * @return array{box: array{0:int,1:int}, crop: null}
 */
function sh_import_spec_as_is(): array {
	return array(
		'box'  => array( 0, 0 ),
		'crop' => null,
	);
}

/** Tile 184×184 → 368². */
function sh_import_spec_tile(): array {
	return sh_import_spec( 184, 184 );
}

/** Tile hover 210×210 → 420². */
function sh_import_spec_tile_hover(): array {
	return sh_import_spec( 210, 210 );
}

/** Detail hero still 1440×810 → 2880×1620 (makara-1117: object without a WebM · DESIGN-SPEC §8.6). */
function sh_import_spec_hero_still(): array {
	return sh_import_spec( 1440, 810 );
}

/**
 * Hero still ref of a seed object that has no hero video (hero_video_src null → hero = this still) · '' = none.
 *
 * @param array<string, mixed> $o Seed object.
 */
function sh_import_object_hero_still_ref( array $o ): string {
	return ( empty( $o['hero_video_src'] ) && isset( $o['hero_still_ref'] ) && is_string( $o['hero_still_ref'] ) ) ? trim( $o['hero_still_ref'] ) : '';
}

/**
 * Trail cover 1120×490 (seed crop is already 2240×980) · on the white trail page (§8.1 fill #FFFFFF) — matte white:
 * the PNG crops are transparent only along the crop edge, so they become JPEG (4–5 MB → ≤ 0.5 MB · core-1117b).
 */
function sh_import_spec_trail_cover(): array {
	return sh_import_spec( 1120, 490 ) + array( 'matte' => '#FFFFFF' );
}

/** Guide card: big 230×363 (small 120×190 uses the same file). */
function sh_import_spec_guide_card(): array {
	return sh_import_spec( 230, 363 );
}

// Slots of the zone grid fields (mu-plugin inc/fields.php); defined here too for the CLI bundle list (no WordPress).
if ( ! defined( 'SH_ZONE_GRID_SLOTS' ) ) {
	define( 'SH_ZONE_GRID_SLOTS', 90 );
}

/**
 * Hover swaps of the seed (hover-1117): previous ref (no extension) => new ref (no extension) — the designer fixed the
 * names inside a hover image, the zone grid of the same Figma still carries the previous one (fb-1117).
 *
 * @param array<string, mixed> $seed Seed files (objects).
 * @return array<string, string>
 */
function sh_import_hover_prev_map( array $seed ): array {
	$map  = array();
	$walk = static function ( $node ) use ( &$walk, &$map ): void {
		if ( ! is_array( $node ) ) {
			return;
		}
		if ( ! empty( $node['tile_image_hover_ref_prev'] ) && ! empty( $node['tile_image_hover_ref'] ) && is_string( $node['tile_image_hover_ref_prev'] ) && is_string( $node['tile_image_hover_ref'] ) ) {
			$map[ (string) preg_replace( '/\.[a-z]+$/i', '', $node['tile_image_hover_ref_prev'] ) ] = (string) preg_replace( '/\.[a-z]+$/i', '', $node['tile_image_hover_ref'] );
		}
		foreach ( $node as $child ) {
			$walk( $child );
		}
	};
	$walk( isset( $seed['objects']['objects'] ) ? $seed['objects']['objects'] : array() );
	return $map;
}

/**
 * File (relative to $dir) of a zone grid tile ref ('' = none): the objects-1117 file the objects' tiles were imported
 * from (refs there carry an extension — same file, same attachment), else the top-level file (image-map / elephant).
 *
 * @param string $ref Figma imageRef (no extension) of the zone seed.
 * @param string $dir design/assets directory.
 */
function sh_import_zone_tile_file( string $ref, string $dir ): string {
	$ref = basename( trim( $ref ) );
	if ( '' === $ref || '.' === $ref[0] ) {
		return '';
	}
	foreach ( array( 'png', 'jpg', 'jpeg', 'webp' ) as $ext ) {
		if ( is_file( $dir . '/' . SH_IMPORT_OBJECTS_1117 . '/' . $ref . '.' . $ext ) ) {
			return SH_IMPORT_OBJECTS_1117 . '/' . $ref . '.' . $ext;
		}
	}
	return sh_import_ref_file( array(), $ref, $dir );
}

/**
 * Tiles of the Zone 1–3 object grid from seed zones-2026-11-17.json (D57), per zone number, by slot (= `order`):
 * object_slug (null = a picture without a link) · tile_ref · hover_ref (a hover the designer replaced in hover-1117 is
 * mapped to the new one). Evidence rows without a tile_ref are skipped.
 *
 * @param array<string, mixed> $seed Seed files (zones-2026-11-17 · objects).
 * @return array<int, array<int, array{object_slug:string|null,tile_ref:string,hover_ref:string}>>
 */
function sh_import_zone_grid_tiles( array $seed ): array {
	$out  = array();
	$prev = sh_import_hover_prev_map( $seed );
	foreach ( (array) ( isset( $seed['zones-2026-11-17']['zones'] ) ? $seed['zones-2026-11-17']['zones'] : array() ) as $z ) {
		$n = isset( $z['zone'] ) ? (int) $z['zone'] : 0;
		if ( $n < 1 || $n > 3 ) {
			continue;
		}
		foreach ( (array) ( isset( $z['tiles'] ) ? $z['tiles'] : array() ) as $t ) {
			$slot = isset( $t['order'] ) ? (int) $t['order'] : 0;
			$tile = isset( $t['tile_ref'] ) && is_string( $t['tile_ref'] ) ? trim( $t['tile_ref'] ) : '';
			if ( $slot < 1 || $slot > SH_ZONE_GRID_SLOTS || '' === $tile ) {
				continue;
			}
			$hover                = isset( $t['hover_ref'] ) && is_string( $t['hover_ref'] ) ? trim( $t['hover_ref'] ) : '';
			$out[ $n ][ $slot ] = array(
				'object_slug' => ! empty( $t['object_slug'] ) && is_string( $t['object_slug'] ) ? $t['object_slug'] : null,
				'tile_ref'    => $tile,
				'hover_ref'   => isset( $prev[ $hover ] ) ? $prev[ $hover ] : $hover,
			);
		}
	}
	return $out;
}

/**
 * Every 2026-11-17 design/assets image the import reads: [label, value, sub, spec]. One list for the import and
 * the deploy bundle (sh_import_bundle_1117_files()).
 *
 * @param array<string, mixed> $seed Seed files (objects · trails · guides · site · intro-2026-11 · media manifest as
 *                                   "manifest" to skip intro fills that have a media/out file).
 * @return array<int, array{0:string,1:string,2:string,3:array<string,mixed>|null}>
 */
function sh_import_1117_asset_values( array $seed ): array {
	$out = array();
	$add = static function ( string $label, $value, string $sub, ?array $spec ) use ( &$out ): void {
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			$out[] = array( $label, $value, $sub, $spec );
		}
	};
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		if ( ! sh_import_object_generic( $o ) && empty( $o['tiles_by_trail'] ) ) {
			continue; // Elephant records without other trails: image-map (refs.php) as before.
		}
		$slug = (string) $o['slug'];
		if ( sh_import_object_generic( $o ) ) {
			$add( "object $slug tile", isset( $o['tile_image_ref'] ) ? $o['tile_image_ref'] : null, SH_IMPORT_OBJECTS_1117, sh_import_spec_tile() );
			$add( "object $slug tile hover", isset( $o['tile_image_hover_ref'] ) ? $o['tile_image_hover_ref'] : null, SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover() );
			$add( "object $slug hero still", sh_import_object_hero_still_ref( $o ), SH_IMPORT_OBJECTS_1117, sh_import_spec_hero_still() );
			foreach ( sh_import_style_refs( (array) sh_import_primary_frame( $o )['style'] ) as $ref ) {
				$add( "object $slug " . implode( '.', $ref[0] ), $ref[1], SH_IMPORT_OBJECTS_1117, sh_import_spec_as_is() ); // Crops: already 2× (JPEG rule only).
			}
		}
		foreach ( sh_import_object_tiles( $o ) as $t => $tile ) {
			$add( "object $slug tile $t", $tile['image'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile() );
			$add( "object $slug tile hover $t", $tile['hover'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover() );
		}
	}
	foreach ( (array) ( isset( $seed['trails']['trails'] ) ? $seed['trails']['trails'] : array() ) as $t ) {
		if ( ! is_array( $t ) || ! empty( $t['not_a_trail'] ) || 'elephant' === $t['slug'] ) {
			continue; // Elephant keeps its trail page values (DESIGN-SPEC §8.1 · seed figma_2026_11_17 block not used).
		}
		$slug = (string) $t['slug'];
		$add( "trail $slug cover", isset( $t['cover_crop_ref'] ) ? $t['cover_crop_ref'] : null, SH_IMPORT_OBJECTS_1117, sh_import_spec_trail_cover() );
		foreach ( sh_import_trail_static_tiles( $seed, $t ) as $st ) {
			$add( "trail $slug static tile {$st['position']}", $st['image'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile() );
			$add( "trail $slug static tile hover {$st['position']}", $st['hover'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover() );
		}
	}
	// D57 zone 1–3 object grid: tile images the importer may have to create (a file the objects' tiles already list is the same
	// file — one bundle entry). Label "zone grid …": a missing file is not an error (the slot falls back to the object's tile).
	foreach ( sh_import_zone_grid_tiles( $seed ) as $zn => $slots ) {
		foreach ( $slots as $slot => $t ) {
			$add( "zone grid $zn tile $slot", $t['tile_ref'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile() );
			$add( "zone grid $zn tile hover $slot", $t['hover_ref'], SH_IMPORT_OBJECTS_1117, sh_import_spec_tile_hover() );
		}
	}
	if ( isset( $seed['guides'] ) ) {
		foreach ( (array) $seed['guides']['cards'] as $c ) {
			$add( 'guide ' . $c['trail_slug'], isset( $c['card_image']['file'] ) ? $c['card_image']['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec_guide_card() );
		}
		$back = isset( $seed['guides']['section']['card_back'] ) ? $seed['guides']['section']['card_back'] : array();
		$add( 'guide card back', isset( $back['file'] ) ? $back['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec_card_back() );
	}
	if ( isset( $seed['site'] ) ) {
		$b = $seed['site']['brand'];
		$add( 'header logo', isset( $b['header_logo']['file'] ) ? $b['header_logo']['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec_logo_header( $seed ) );
		$add( 'footer temasek', isset( $b['footer']['temasek']['file'] ) ? $b['footer']['temasek']['file'] : null, SH_IMPORT_PAGES_1117, sh_import_spec( 208, 60 ) );
	}
	if ( isset( $seed['intro-2026-11'] ) ) {
		$manifest = isset( $seed['manifest'] ) && is_array( $seed['manifest'] ) ? $seed['manifest'] : array();
		foreach ( sh_import_intro_images( $seed['intro-2026-11'] ) as $im ) {
			if ( '' !== $im['drive_file'] && null !== sh_import_manifest_by_key( $manifest, 'intro-1117/' . $im['drive_file'] ) ) {
				continue; // media/out file (media-dev already sized it).
			}
			$add( 'intro ' . $im['label'], $im['file'], SH_IMPORT_PAGES_1117, $im['spec'] );
		}
	}
	return $out;
}

/**
 * Card back "?" (§9.3): 120×190 with the Figma crop (border inside the image).
 */
function sh_import_spec_card_back(): array {
	return sh_import_spec( 120, 190, array( 0.016, 0.017, 0.968, 0.971 ) );
}

/**
 * Header logo (§9.1): 220×77 with the seed crop.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_spec_logo_header( array $seed ): array {
	$b = isset( $seed['site']['brand']['header_logo'] ) ? $seed['site']['brand']['header_logo'] : array();
	$box = isset( $b['box'] ) && is_array( $b['box'] ) ? $b['box'] : array( 220, 77 );
	return sh_import_spec( (float) $box[0], (float) $box[1], sh_import_seed_crop( isset( $b['crop'] ) ? $b['crop'] : null ) );
}

/**
 * Manifest entry by key (media-1117: "drive-1117/<path>" · "intro-1117/<path>") or null.
 *
 * @param array<string, mixed> $manifest content/media-manifest.json.
 * @param string               $key      Key.
 * @return array<string, mixed>|null
 */
function sh_import_manifest_by_key( array $manifest, string $key ): ?array {
	foreach ( (array) ( isset( $manifest['files'] ) ? $manifest['files'] : array() ) as $f ) {
		if ( is_array( $f ) && isset( $f['key'] ) && ( $f['key'] === $key || ( isset( $f['key_normalized'] ) && $f['key_normalized'] === $key ) ) ) {
			return $f;
		}
	}
	return null;
}

/**
 * Path under media/out of a manifest path ("media/out/objects-1117/bird/x.webm" → "objects-1117/bird/x.webm") ·
 * '' when unsafe (absolute, "..", dot-files, more than 2 folders).
 *
 * @param string $path Manifest value.
 */
function sh_import_media_rel( string $path ): string {
	$rel   = str_starts_with( $path, 'media/out/' ) ? substr( $path, strlen( 'media/out/' ) ) : basename( $path );
	$parts = explode( '/', $rel );
	if ( '' === $rel || count( $parts ) > 3 ) {
		return '';
	}
	foreach ( $parts as $p ) {
		if ( '' === $p || '.' === $p[0] ) {
			return '';
		}
	}
	return $rel;
}

/**
 * Manifest entry of an object's hero (object_slug = slug · else key "drive-1117/<hero_video_src>") or null.
 *
 * @param array<string, mixed> $manifest Manifest.
 * @param array<string, mixed> $o        Seed object.
 * @return array<string, mixed>|null
 */
function sh_import_object_hero_entry( array $manifest, array $o ): ?array {
	foreach ( (array) ( isset( $manifest['files'] ) ? $manifest['files'] : array() ) as $f ) {
		if ( is_array( $f ) && isset( $f['object_slug'] ) && $f['object_slug'] === $o['slug'] ) {
			return $f;
		}
	}
	return ! empty( $o['hero_video_src'] ) ? sh_import_manifest_by_key( $manifest, 'drive-1117/' . $o['hero_video_src'] ) : null;
}

/**
 * Images of the Introduction seed (D50) in import order: header emblem, then per chapter the map and every image
 * block (decor blocks too). [{label, chapter (0 = header), slot (image slot · 0 = map · -1 = emblem · -2 = decor),
 * drive_file, file (pages-1117 fallback), spec, decor key}].
 *
 * @param array<string, mixed> $intro intro-2026-11.json.
 * @return array<int, array{label:string,chapter:int,slot:int,drive_file:string,file:string,spec:array<string,mixed>,decor:string}>
 */
function sh_import_intro_images( array $intro ): array {
	$out = array();
	$add = static function ( string $label, int $ch, int $slot, array $b, string $decor = '' ) use ( &$out ): void {
		$box   = isset( $b['box'] ) && is_array( $b['box'] ) ? $b['box'] : ( isset( $b['rel'][2], $b['rel'][3] ) ? array( $b['rel'][2], $b['rel'][3] ) : array( 1440, 810 ) );
		$out[] = array(
			'label'      => $label,
			'chapter'    => $ch,
			'slot'       => $slot,
			'drive_file' => isset( $b['drive_file'] ) && is_string( $b['drive_file'] ) ? $b['drive_file'] : '',
			'file'       => isset( $b['file'] ) && is_string( $b['file'] ) ? $b['file'] : ( isset( $b['figma_fill_file'] ) && is_string( $b['figma_fill_file'] ) ? $b['figma_fill_file'] : '' ),
			'spec'       => sh_import_spec( (float) $box[0], (float) $box[1] ),
			'decor'      => $decor,
		);
	};
	if ( isset( $intro['header']['emblem'] ) ) {
		$add( 'emblem', 0, -1, $intro['header']['emblem'] );
	}
	foreach ( (array) ( isset( $intro['header']['decor'] ) ? $intro['header']['decor'] : array() ) as $k => $d ) {
		if ( is_array( $d ) && isset( $d['file'] ) ) {
			$add( 'header ' . $k, 0, -2, $d + array( 'box' => array( $d['rel'][2], $d['rel'][3] ) ), (string) $k );
		}
	}
	foreach ( (array) $intro['chapters'] as $c ) {
		$n = (int) $c['n'];
		$add( "chapter $n map", $n, 0, $c['map'] );
		$slot = 0;
		foreach ( (array) $c['sections'] as $si => $s ) {
			foreach ( (array) $s['blocks'] as $bi => $b ) {
				if ( 'image' !== $b['kind'] ) {
					continue;
				}
				if ( ! empty( $b['decor'] ) ) {
					$add( "chapter $n decor $si.$bi", $n, -2, $b, "$si.$bi" );
					continue;
				}
				++$slot;
				$add( "chapter $n image $slot", $n, $slot, $b );
			}
		}
	}
	return $out;
}

/**
 * Bundle list of the 2026-11-17 images + media/out files (merged into the bundle-files.php output).
 * design/assets: "ref" lines with a fit box (before the crop) · media: every manifest file the import reads.
 *
 * @param array<string, mixed> $seed     Seed files (+ "manifest").
 * @param string               $dir      design/assets directory.
 * @param string               $mediadir media/out directory.
 * @return array{refs: array<string, string>, media: string[], missing: string[]}
 *         refs = file => "WxH[@RRGGBB]" (fit box · 0x0 = no size limit · @matte = flatten large PNGs onto it as JPEG ·
 *         '' = copy as is).
 */
function sh_import_bundle_1117_files( array $seed, string $dir, string $mediadir ): array {
	$out = array(
		'refs'    => array(),
		'media'   => array(),
		'missing' => array(),
	);
	foreach ( sh_import_1117_asset_values( $seed ) as $v ) {
		list( $label, $value, $sub, $spec ) = $v;
		$file = str_starts_with( $label, 'zone grid ' ) ? sh_import_zone_tile_file( $value, $dir ) : sh_import_1117_file( $value, $dir, $sub );
		if ( '' === $file ) {
			if ( ! str_starts_with( $label, 'zone grid ' ) ) {
				$out['missing'][] = "1117 $label: $value";
			}
			continue;
		}
		$box = null === $spec ? '' : implode( 'x', sh_web_image_bundle_box( $spec ) ) . ( ! empty( $spec['matte'] ) ? '@' . ltrim( (string) $spec['matte'], '#' ) : '' );
		if ( isset( $out['refs'][ $file ] ) && ( '0x0' === $out['refs'][ $file ] || '0x0' === $box ) ) {
			$box = '0x0'; // One use keeps the full size: no shrink (JPEG rule only).
		} elseif ( isset( $out['refs'][ $file ] ) && '' !== $out['refs'][ $file ] && '' !== $box ) {
			$a   = explode( 'x', $out['refs'][ $file ] );
			$b   = explode( 'x', $box );
			$box = max( (int) $a[0], (int) $b[0] ) . 'x' . max( (int) $a[1], (int) $b[1] ); // Same file, two uses: larger.
		} elseif ( isset( $out['refs'][ $file ] ) ) {
			$box = ''; // One use without a size = keep the file as it is.
		}
		$out['refs'][ $file ] = $box;
	}
	$manifest = isset( $seed['manifest'] ) && is_array( $seed['manifest'] ) ? $seed['manifest'] : array();
	foreach ( (array) ( isset( $manifest['files'] ) ? $manifest['files'] : array() ) as $f ) {
		if ( ! is_array( $f ) ) {
			continue;
		}
		foreach ( array( $f['out'] ?? null, $f['poster'] ?? null, $f['still'] ?? null, $f['fallback']['file'] ?? null ) as $p ) {
			if ( ! is_string( $p ) || '' === $p ) {
				continue;
			}
			$rel = sh_import_media_rel( $p );
			if ( '' === $rel || ! is_file( $mediadir . '/' . $rel ) ) {
				$out['missing'][] = 'media ' . $p;
				continue;
			}
			$out['media'][ $rel ] = $rel;
		}
	}
	ksort( $out['refs'] );
	$out['media'] = array_values( $out['media'] );
	sort( $out['media'] );
	return $out;
}
