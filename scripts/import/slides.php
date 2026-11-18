<?php
/**
 * Importer: Zone 4 slides of the 16 animals other than the elephant (D62 · task zone4slides-1118 · DESIGN-SPEC §4.5d ·
 * seed content/seed/timeline-slides.json · CONTENT-MODEL §3 + §4).
 *
 * Data: one `timeline_item` per slide (import key `timeline:<trail slug>:<order>` — the elephant's keys are
 * `timeline:elephant:<n>` and are never touched): post_title = the slide title (Figma text) · sh_trail · sh_object · sh_card_order ·
 * sh_card_meta = the Figma info text · sh_thumbnail = top image 250×141 (on the line + mini-map) · sh_step_1..3 = the bottom row
 * (adjacent duplicates dropped) · no bottom row (Bird 1) = sh_below_image = the large image. Reflection = term meta of the trail
 * (sh_slide_reflection_title / _body), only while empty.
 * Images: every use of a Figma image (box · fit mode · trim) is baked into one small WebP (slide-image.php): deploy.sh bakes them
 * into the bundle (zone4-slides-web/<unit>.webp — the 4K originals never leave the machine), a local import without that folder
 * bakes from design/assets itself. One unit = one attachment (key `asset:slide-<unit>`), shared between animals.
 * Rule (prod refresh step): create what does not exist · an existing slide only gets its EMPTY fields filled · nothing is deleted ·
 * an admin's value stays (Reflection differing from the seed = kept_admin).
 *
 * @package sharedheritage-core
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'SH_IMPORT_CLI' ) && 'cli' !== PHP_SAPI ) {
	exit; // Also loaded by bundle-files.php (CLI · no WordPress) for the unit list.
}

require_once __DIR__ . '/slide-image.php';

/** Folder (inside the assets dir / bundle) of the baked slide images. */
const SH_SLIDES_WEB_DIR = 'zone4-slides-web';

/**
 * The animals of the seed: trail slug => {items[], reflection}. (Keys starting with "_" and "animals" are not animals.)
 *
 * @param array<string, mixed> $slides content/seed/timeline-slides.json.
 * @return array<string, array<string, mixed>>
 */
function sh_slides_animals( array $slides ): array {
	$out = array();
	foreach ( $slides as $slug => $row ) {
		if ( is_string( $slug ) && '_' !== $slug[0] && 'animals' !== $slug && is_array( $row ) && ! empty( $row['items'] ) ) {
			$out[ $slug ] = $row;
		}
	}
	return $out;
}

/**
 * Baked image units of one slide: role => [{ref, spec}] — top (card image) · steps (bottom row, adjacent duplicates dropped,
 * 0 steps = the large image as a single `below`).
 *
 * @param array<string, mixed> $it Seed item.
 * @return array{top: array<string, mixed>|null, steps: array<int, array<string, mixed>>, below: array<string, mixed>|null}
 */
function sh_slides_item_units( array $it ): array {
	$unit = static function ( string $ref, array $box, array $fit, bool $trim ): array {
		$res = $trim ? 2.0 : min( 2.0, 640 / max( 1, $box[0] ), 530 / max( 1, $box[1] ) ); // Shown ≤ 320×265 · 2× for retina.
		return array(
			'ref'  => $ref,
			'spec' => array(
				'box'  => array( (int) $box[0], (int) $box[1] ),
				'res'  => round( $res, 3 ),
				'fit'  => isset( $fit['transform'] ) ? array( 'mode' => (string) $fit['mode'], 'transform' => $fit['transform'] ) : array( 'mode' => isset( $fit['mode'] ) ? (string) $fit['mode'] : 'FIT' ),
				'trim' => $trim,
			),
		);
	};
	$out = array( 'top' => null, 'steps' => array(), 'below' => null );
	if ( ! empty( $it['top_image_ref'] ) ) {
		$out['top'] = $unit( (string) $it['top_image_ref'], array( 250, 141 ), (array) ( isset( $it['top_fit'] ) ? $it['top_fit'] : array() ), true );
	}
	$prev = '';
	foreach ( array_values( (array) ( isset( $it['steps'] ) ? $it['steps'] : array() ) ) as $n => $ref ) {
		if ( ! is_string( $ref ) || '' === $ref || $ref === $prev ) {
			continue; // Same image twice in a row = one step (Hornbill 1).
		}
		$prev = $ref;
		$size = isset( $it['steps_size'] ) && is_array( $it['steps_size'] ) ? $it['steps_size'] : array( 500, 282 );
		$fit  = isset( $it['steps_fit'][ $n ] ) && is_array( $it['steps_fit'][ $n ] ) ? $it['steps_fit'][ $n ] : array();
		$out['steps'][] = $unit( $ref, $size, $fit, false );
		if ( count( $out['steps'] ) >= 3 ) {
			break;
		}
	}
	if ( ! $out['steps'] && ! empty( $it['large_image_ref'] ) ) {
		$out['below'] = $unit( (string) $it['large_image_ref'], isset( $it['large_size'] ) && is_array( $it['large_size'] ) ? $it['large_size'] : array( 500, 281 ), (array) ( isset( $it['large_fit'] ) ? $it['large_fit'] : array() ), false );
	}
	return $out;
}

/**
 * File name (without folder / extension) of a baked unit: 12 characters of the Figma imageRef + 8 of the spec's hash.
 *
 * @param array<string, mixed> $unit {ref, spec}.
 */
function sh_slides_unit_name( array $unit ): string {
	return substr( (string) $unit['ref'], 0, 12 ) . '-' . substr( sha1( (string) json_encode( $unit['spec'], JSON_UNESCAPED_SLASHES ) ), 0, 8 );
}

/**
 * Source file (relative to the assets dir) of an imageRef: seed `_meta.asset_files` (design/assets/[zone4-slides/]<ref>.png).
 * '' = not listed / unsafe name.
 *
 * @param array<string, mixed> $slides Seed.
 * @param string               $ref    imageRef.
 */
function sh_slides_source_rel( array $slides, string $ref ): string {
	$path = isset( $slides['_meta']['asset_files'][ $ref ] ) ? (string) $slides['_meta']['asset_files'][ $ref ] : '';
	$rel  = str_starts_with( $path, 'design/assets/' ) ? substr( $path, strlen( 'design/assets/' ) ) : $path;
	return 1 === preg_match( '#^(zone4-slides/)?[0-9a-f]{40}\.(png|jpe?g|webp)$#', $rel ) ? $rel : '';
}

/**
 * Every baked unit the seed needs: unit name => [src rel, spec] (deploy bundle · import). Missing sources → "missing" list.
 *
 * @param array<string, mixed> $slides Seed.
 * @param string               $dir    Assets dir ('' = do not check files).
 * @return array{units: array<string, array{0:string,1:array<string,mixed>}>, missing: string[]}
 */
function sh_slides_bundle_units( array $slides, string $dir ): array {
	$out = array( 'units' => array(), 'missing' => array() );
	foreach ( sh_slides_animals( $slides ) as $slug => $row ) {
		foreach ( (array) $row['items'] as $it ) {
			foreach ( sh_slides_item_units( (array) $it ) as $role => $list ) {
				foreach ( 'steps' === $role ? (array) $list : array( $list ) as $unit ) {
					if ( ! is_array( $unit ) ) {
						continue;
					}
					$rel = sh_slides_source_rel( $slides, (string) $unit['ref'] );
					if ( '' === $rel || ( '' !== $dir && ! is_file( $dir . '/' . $rel ) ) ) {
						$out['missing'][] = 'slides ' . $slug . ' ' . ( isset( $it['figma_frame'] ) ? $it['figma_frame'] : '?' ) . ': ' . $unit['ref'];
						continue;
					}
					$out['units'][ sh_slides_unit_name( $unit ) ] = array( $rel, $unit['spec'] );
				}
			}
		}
	}
	ksort( $out['units'] );
	$out['missing'] = array_values( array_unique( $out['missing'] ) );
	return $out;
}

/**
 * Attachment id of a baked unit (0 + warning when the source is missing or cannot be baked).
 *
 * @param array<string, mixed> $slides Seed.
 * @param array<string, mixed> $unit   {ref, spec}.
 * @param string               $title  Attachment title.
 */
function sh_slides_attachment( array $slides, array $unit, string $title ): int {
	$name = sh_slides_unit_name( $unit );
	$key  = 'asset:slide-' . $name;
	$id   = sh_imp_find_attachment( $key );
	if ( $id ) {
		return $id;
	}
	$dir  = sh_imp_config()['assets'];
	$file = $dir . '/' . SH_SLIDES_WEB_DIR . '/' . $name . '.webp';
	$temp = '';
	if ( ! is_file( $file ) ) { // Not baked by the deploy bundle (local import): bake from the original.
		$rel = sh_slides_source_rel( $slides, (string) $unit['ref'] );
		if ( '' === $rel || ! is_file( $dir . '/' . $rel ) ) {
			sh_imp_warn( 'slide image source missing: ' . $unit['ref'] . ' (' . $title . ')' );
			return 0;
		}
		$temp = rtrim( sys_get_temp_dir(), '/' ) . '/sh-slide-' . $name . '.webp';
		$res  = sh_slide_image_bake( $dir . '/' . $rel, $temp, (array) $unit['spec'] );
		if ( 0 !== strpos( $res, 'baked' ) ) {
			sh_imp_warn( ( 'nogd' === $res ? 'PHP GD WebP missing — slide image not baked: ' : 'slide image could not be baked: ' ) . $unit['ref'] . ' (' . $title . ')' );
			return 0;
		}
		$file = $temp;
	}
	$id = sh_imp_attachment( $file, $key, $title );
	if ( '' !== $temp && is_file( $temp ) ) {
		unlink( $temp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- own temp file.
	}
	return $id;
}

/**
 * Create the slides + Reflections of the seed (see file header). Returns false when a file was missing (the refresh step
 * is then tried again on the next import).
 *
 * @param array<string, mixed> $slides Seed timeline-slides.json.
 */
function sh_import_slides( array $slides ): bool {
	$ok = true;
	foreach ( sh_slides_animals( $slides ) as $slug => $row ) {
		$term = get_term_by( 'slug', (string) $slug, 'trail' );
		if ( ! $term instanceof WP_Term ) {
			sh_imp_warn( 'slides: trail "' . $slug . '" does not exist on this site — its slides were not created' );
			$ok = false;
			continue;
		}
		foreach ( (array) $row['items'] as $it ) {
			$ok = sh_import_slide_item( $slides, $term, (array) $it ) && $ok;
		}
		sh_import_slide_reflection( $term, (array) ( isset( $row['reflection'] ) ? $row['reflection'] : array() ) );
	}
	return $ok;
}

/**
 * One slide: created when missing, else only its empty fields are filled.
 *
 * @param array<string, mixed> $slides Seed.
 * @param WP_Term              $term   Trail.
 * @param array<string, mixed> $it     Seed item.
 */
function sh_import_slide_item( array $slides, WP_Term $term, array $it ): bool {
	$n   = (int) $it['order'];
	$key = 'timeline:' . $term->slug . ':' . $n;
	$ttl = (string) $it['title'];
	list( $id, $write ) = sh_imp_post(
		'timeline_item',
		$key,
		array(
			'post_type'  => 'timeline_item',
			'post_title' => $ttl,
			'menu_order' => $n,
		)
	);
	$obj_id = ! empty( $it['object_slug'] ) ? sh_imp_find_post( 'object', 'object:' . $it['object_slug'] ) : 0;
	if ( ! empty( $it['object_slug'] ) && ! $obj_id ) {
		sh_imp_warn( 'slides: object "' . $it['object_slug'] . '" for ' . $key . ' not found — card has no link yet' );
	}
	$units = sh_slides_item_units( $it );
	$want  = array(
		'sh_trail'      => (int) $term->term_id,
		'sh_object'     => $obj_id,
		'sh_card_order' => $n,
		'sh_card_meta'  => (string) ( isset( $it['meta_raw'] ) ? $it['meta_raw'] : '' ),
	);
	$ok    = true;
	$img   = static function ( string $field, ?array $unit, string $label ) use ( $slides, $ttl, $id, $write, &$ok, &$want ): void {
		if ( null === $unit ) {
			return;
		}
		if ( ! $write && 0 !== absint( get_post_meta( $id, $field, true ) ) ) {
			return; // Existing slide with this image already: never replaced.
		}
		$att = sh_slides_attachment( $slides, $unit, $ttl . ' ' . $label );
		if ( $att ) {
			$want[ $field ] = $att;
		} else {
			$ok = false;
		}
	};
	$img( 'sh_thumbnail', $units['top'], 'card' );
	foreach ( $units['steps'] as $i => $unit ) {
		$img( 'sh_step_' . ( $i + 1 ), $unit, 'step ' . ( $i + 1 ) );
	}
	$img( 'sh_below_image', $units['below'], 'large' );

	if ( $write ) {
		sh_imp_meta( 'post', $id, $want );
		return $ok;
	}
	$fill = array();
	foreach ( $want as $field => $value ) {
		$cur = get_post_meta( $id, $field, true );
		if ( ( '' === $cur || 0 === $cur || '0' === $cur || false === $cur ) && ! ( '' === $value || 0 === $value ) ) {
			$fill[ $field ] = $value; // Only empty fields (a value an admin set stays).
		}
	}
	if ( $fill ) {
		sh_imp_meta( 'post', $id, $fill );
		sh_imp_count( 'updated', 'timeline_slide_filled' );
	}
	return $ok;
}

/**
 * Reflection of an animal (term meta) — written only while empty; a different value is kept (kept_admin).
 *
 * @param WP_Term              $term Trail.
 * @param array<string, mixed> $refl Seed reflection {title, body}.
 */
function sh_import_slide_reflection( WP_Term $term, array $refl ): void {
	$want = array(
		'sh_slide_reflection_title' => trim( (string) ( isset( $refl['title'] ) ? $refl['title'] : '' ) ),
		'sh_slide_reflection_body'  => trim( (string) ( isset( $refl['body'] ) ? $refl['body'] : '' ), " \t\r\n" ),
	);
	$fill = array();
	foreach ( $want as $field => $value ) {
		$cur = (string) get_term_meta( $term->term_id, $field, true );
		if ( '' === $value ) {
			continue;
		}
		if ( '' === trim( $cur ) ) {
			$fill[ $field ] = $value;
		} elseif ( $cur !== $value ) {
			sh_imp_keep( 'trail:' . $term->slug . ' ' . $field . ' (site text kept · seed differs)' );
		}
	}
	if ( $fill ) {
		sh_imp_meta( 'term', $term->term_id, $fill );
		sh_imp_count( 'updated', 'slide_reflection' );
	}
}

/**
 * Steps of this file: id => what it does (merged into sh_import_refresh_steps()).
 *
 * @return array<string, string>
 */
function sh_import_refresh_steps_slides(): array {
	return array(
		// zone4slides-1118 (BACKLOG #44 · D62): slides of the 16 animals — create what is missing, fill only empty fields.
		'2026-11-18-zone4-slides' => 'timeline_item per slide of the 16 animals (key timeline:<trail>:<n>) + their images + Reflection of each trail (term meta) · only what is missing: an existing slide gets only empty fields · the elephant timeline is never touched · nothing deleted',
	);
}

/**
 * Run the step. Returns true when done (false = a file was missing: tried again next import).
 *
 * @param string               $step   Step id.
 * @param array<string, mixed> $seed   Seed files.
 */
function sh_import_refresh_slides( string $step, array $seed ): bool {
	if ( '2026-11-18-zone4-slides' === $step && isset( $seed['timeline-slides'] ) ) {
		return sh_import_slides( (array) $seed['timeline-slides'] );
	}
	return true;
}
