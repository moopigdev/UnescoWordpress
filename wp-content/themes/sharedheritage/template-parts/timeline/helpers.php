<?php
/**
 * Timeline v3 helpers (page-timeline.php only). Loaded with require_once — not a template part.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

/**
 * Number → short decimal string (max 4 decimals) for inline CSS.
 *
 * @param float|int $value Number.
 */
function sh_tl_num( $value ): string {
	$out = rtrim( rtrim( number_format( (float) $value, 4, '.', '' ), '0' ), '.' );
	return '-0' === $out ? '0' : $out;
}

/**
 * Inline style from CSS custom properties / properties (values are numbers built here; escaped on output).
 *
 * @param array<string, string> $props Property => value.
 */
function sh_tl_style( array $props ): string {
	$out = array();
	foreach ( $props as $name => $value ) {
		$out[] = $name . ':' . $value;
	}
	return implode( ';', $out );
}

/**
 * Width × height of an image "contained" in a box.
 *
 * @param float $ratio Width ÷ height.
 * @param float $w     Box width.
 * @param float $h     Box height.
 * @return float[] [w, h]
 */
function sh_tl_contain( float $ratio, float $w, float $h ): array {
	if ( $ratio <= 0 ) {
		return array( $w, $h );
	}
	return ( $w / $h > $ratio ) ? array( $h * $ratio, $h ) : array( $w, $w / $ratio );
}

/**
 * Is the attachment still the design file (same pixels, or WordPress' "-scaled" copy of it)?
 *
 * @param int   $id  Attachment id.
 * @param array $src [width, height] of the design file.
 */
function sh_tl_is_design_file( int $id, array $src ): bool {
	$meta = wp_get_attachment_metadata( $id );
	if ( ! is_array( $meta ) || empty( $meta['width'] ) || empty( $meta['height'] ) || empty( $src[0] ) || empty( $src[1] ) ) {
		return false;
	}
	if ( (int) $meta['width'] === (int) $src[0] && (int) $meta['height'] === (int) $src[1] ) {
		return true;
	}
	$same_ratio = abs( ( $meta['width'] / $meta['height'] ) / ( $src[0] / $src[1] ) - 1 ) < 0.003;
	return ! empty( $meta['original_image'] ) && $same_ratio;
}

/**
 * Design geometry entry of a card, found by its stable card number — never by title or slug (admin can change both).
 *
 * The number is the N of the import key `<key_prefix>N` (post meta _sh_import_key, set once by the importer).
 * No key, other trail, or no entry for N → empty array → the caller uses the §4.5c.3 rule.
 *
 * @param WP_Post $item timeline_item.
 * @param array   $geo  geometry.php.
 * @return array<string, mixed>
 */
function sh_tl_geo_entry( WP_Post $item, array $geo ): array {
	$prefix = isset( $geo['key_prefix'] ) ? (string) $geo['key_prefix'] : '';
	$key    = (string) get_post_meta( $item->ID, '_sh_import_key', true );
	if ( '' === $prefix || 0 !== strpos( $key, $prefix ) || ! preg_match( '/^[0-9]+$/', substr( $key, strlen( $prefix ) ) ) ) {
		return array();
	}
	$n = (int) substr( $key, strlen( $prefix ) );
	return isset( $geo['items'][ $n ] ) && is_array( $geo['items'][ $n ] ) ? $geo['items'][ $n ] : array();
}

/**
 * Everything one timeline stop needs (CONTENT-MODEL §3 fields + §4.5c geometry).
 *
 * @param WP_Post $item timeline_item.
 * @param array   $geo  geometry.php.
 * @return array<string, mixed>|null null when the item has no image at all.
 */
function sh_tl_item( WP_Post $item, array $geo ): ?array {
	$entry = sh_tl_geo_entry( $item, $geo );
	$rule  = isset( $geo['rule'] ) && is_array( $geo['rule'] ) ? $geo['rule'] : array();
	$ids   = array(
		'sh_thumbnail'   => absint( sh_get( 'sh_thumbnail', $item->ID ) ),
		'sh_thumbnail_2' => absint( sh_get( 'sh_thumbnail_2', $item->ID ) ),
	);

	// Design geometry while every image of the entry is still the design file (and no other image field is set).
	$images = sh_tl_design_images( isset( $entry['images'] ) ? (array) $entry['images'] : array(), $ids );

	if ( ! empty( $images ) ) {
		$out = array(
			'photo' => $entry['photo'],
			'mini'  => $entry['mini'],
			'ratio' => (float) $entry['ratio'],
		);
	} else {
		// Rule (§4.5c.3): whole images side by side at equal height, contained in the boxes.
		$group = sh_tl_rule_group( $ids, $rule['photo'] );
		if ( ! $group ) {
			return null;
		}
		$images = $group['images'];
		$out    = array(
			'photo' => $group['photo'],
			'mini'  => sh_tl_contain( $group['ratio'], ...$rule['mini'] ),
			'ratio' => $group['ratio'],
		);
	}

	// #14 [C] (§4.5c.3): on the line = photo × 0.30 (rest) → × 0.60 (active) · same ratio · scale = 2. D39 (§4.5c.3d): the
	// base is always the on-line image contained in 320×265 (`photo`), even when the photo under the line is another file.
	$f_rest       = isset( $rule['rest'] ) ? (float) $rule['rest'] : 0.30;
	$f_active     = isset( $rule['active'] ) ? (float) $rule['active'] : 0.60;
	$out['rest']  = array( $out['photo'][0] * $f_rest, $out['photo'][1] * $f_rest );
	$out['scale'] = $f_active / $f_rest;

	// Metadata: one line per row, without the "Section :" row (§4.5c.4 · DEFERRED: case-insensitive).
	$lines  = array();
	$period = '';
	foreach ( preg_split( '/\R/', (string) sh_get( 'sh_card_meta', $item->ID ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line || preg_match( '/^section\s*:/i', $line ) ) {
			continue;
		}
		if ( '' === $period && preg_match( '/^period\s*:\s*(.+)$/i', $line, $m ) ) {
			$period = $m[1];
		}
		$lines[] = $line;
	}

	$object_id = absint( sh_get( 'sh_object', $item->ID ) );
	$below     = sh_tl_below( $item, $entry, $rule );
	$u_photo   = $below ? $below['photo'] : $out['photo'];

	return array_merge(
		$out,
		array(
			'below'  => $below,
			'line'   => sh_tl_line_art( $item, $rule, $entry, $u_photo ),
			'images' => $images,
			'title'  => get_the_title( $item ),
			'url'    => ( $object_id && 'publish' === get_post_status( $object_id ) ) ? (string) get_permalink( $object_id ) : '',
			'lines'  => $lines,
			'period' => $period,
		)
	);
}

/**
 * Images of a design entry with their attachment ids — only while every one is still its design file and no other image
 * field of $ids is set (e.g. an admin adds a 2nd image) · else [] (= use the rule).
 *
 * @param array $entry_images geometry.php `images` / `below_images`.
 * @param int[] $ids          field => attachment id (0 = empty).
 * @return array
 */
function sh_tl_design_images( array $entry_images, array $ids ): array {
	if ( empty( $entry_images ) || array_diff( array_keys( array_filter( $ids ) ), array_column( $entry_images, 'field' ) ) ) {
		return array();
	}
	$images = array();
	foreach ( $entry_images as $img ) {
		$id = isset( $ids[ $img['field'] ] ) ? $ids[ $img['field'] ] : 0;
		if ( ! $id || ! wp_attachment_is_image( $id ) || ! sh_tl_is_design_file( $id, $img['src'] ) ) {
			return array();
		}
		$images[] = array_merge( $img, array( 'id' => $id ) );
	}
	return $images;
}

/**
 * Rule (§4.5c.3): the images of $ids side by side at equal height (whole files), the group contained in $box and never
 * larger than the files' pixels. null = no image.
 *
 * @param int[]   $ids field => attachment id (0 = empty).
 * @param float[] $box [w, h].
 * @return array{images:array,photo:float[],ratio:float}|null
 */
function sh_tl_rule_group( array $ids, array $box ): ?array {
	$images = array();
	$ratio  = 0.0;
	foreach ( $ids as $field => $id ) {
		$src = ( $id && wp_attachment_is_image( $id ) ) ? wp_get_attachment_image_src( $id, 'full' ) : false;
		if ( ! $src || empty( $src[1] ) || empty( $src[2] ) ) {
			continue;
		}
		$images[] = array( 'field' => $field, 'id' => $id, 'src' => array( (int) $src[1], (int) $src[2] ), 'crop' => array( 0, 0, (int) $src[1], (int) $src[2] ), 'r' => $src[1] / $src[2] );
		$ratio   += $src[1] / $src[2];
	}
	if ( empty( $images ) ) {
		return null;
	}
	$x = 0.0;
	foreach ( $images as $n => $img ) {
		$images[ $n ]['box'] = array( $x / $ratio, 0, $img['r'] / $ratio, 1 );
		$x                  += $img['r'];
	}
	$photo = sh_tl_contain( $ratio, ...$box );
	$max_h = min( array_map( static fn( $i ) => ( $i['crop'][3] - $i['crop'][1] ) / $i['box'][3], $images ) );
	if ( $photo[1] > $max_h ) {
		$photo = array( $max_h * $ratio, $max_h ); // ⛔ never larger than the file's pixels (§4.5c.3).
	}
	return array( 'images' => $images, 'photo' => $photo, 'ratio' => $ratio );
}

/**
 * Photo under the line when it is another image than the one on the line (`sh_below_image` + optional `sh_below_image_2`
 * as a 2-image group · D39/D41 → D45 · §4.5c.3d). Design geometry (`below` + `below_images` [+ `below_ratio`]) while the
 * attachments are still those files, else the §4.5c.3 rule (side by side, contained in 320×265, never larger than the
 * files). Empty `sh_below_image` / not an image → null (= the on-line image(s)) · `_2` is used only with it.
 *
 * @param WP_Post $item  timeline_item.
 * @param array   $entry geometry.php item entry ([] = none).
 * @param array   $rule  geometry.php `rule`.
 * @return array{photo:float[],ratio:float,images:array}|null
 */
function sh_tl_below( WP_Post $item, array $entry, array $rule ): ?array {
	$ids = array(
		'sh_below_image'   => absint( sh_get( 'sh_below_image', $item->ID ) ),
		'sh_below_image_2' => absint( sh_get( 'sh_below_image_2', $item->ID ) ),
	);
	if ( ! $ids['sh_below_image'] || ! wp_attachment_is_image( $ids['sh_below_image'] ) || empty( $rule['photo'] ) ) {
		return null;
	}
	$images = ! empty( $entry['below'] ) ? sh_tl_design_images( isset( $entry['below_images'] ) ? (array) $entry['below_images'] : array(), $ids ) : array();
	if ( $images ) {
		list( $x0, $y0, $x1, $y1 ) = array_map( 'floatval', $images[0]['crop'] );
		return array(
			'photo'  => $entry['below'],
			'ratio'  => isset( $entry['below_ratio'] ) ? (float) $entry['below_ratio'] : ( $x1 - $x0 ) / max( 1.0, $y1 - $y0 ),
			'images' => $images,
		);
	}
	$group = sh_tl_rule_group( $ids, $rule['photo'] );
	return $group ? array( 'photo' => $group['photo'], 'ratio' => $group['ratio'], 'images' => $group['images'] ) : null;
}

/**
 * "Before" image of the photo under the line (`sh_line_art` · D35/D36 · replaces D32/D33's top row): while the item
 * comes to the centre the photo box shows it (α 0.40p), on settle it cross-fades to the colour photo (timeline.js).
 * Box = the photo rule of §4.5c.3: "contain" in 320×265 by the line art's own ratio, never larger than the file,
 * centred on the same point as the photo (x 720 · y_rel 561). Empty field / not an image → null (photo as before).
 * D39 (§4.5c.3d · card 5): entry `before_scale` s while the file is still `before_src` → contain in s × the photo under
 * the line ($u_photo) instead (0.62 × 320×229 → 198×134).
 *
 * @param WP_Post $item    timeline_item.
 * @param array   $rule    geometry.php `rule`.
 * @param array   $entry   geometry.php item entry ([] = none).
 * @param array   $u_photo [w, h] of the photo under the line at 1440.
 * @return array{id:int,w:float,ratio:float,scale:float}|null
 */
function sh_tl_line_art( WP_Post $item, array $rule, array $entry = array(), array $u_photo = array() ): ?array {
	$id  = absint( sh_get( 'sh_line_art', $item->ID ) );
	$src = ( $id && wp_attachment_is_image( $id ) ) ? wp_get_attachment_image_src( $id, 'full' ) : false;
	if ( ! $src || empty( $src[1] ) || empty( $src[2] ) || empty( $rule['photo'] ) ) {
		return null;
	}
	$meta  = wp_get_attachment_metadata( $id );
	$file  = is_array( $meta ) && ! empty( $meta['width'] ) ? array( (float) $meta['width'], (float) $meta['height'] ) : array( (float) $src[1], (float) $src[2] );
	$ratio = $src[1] / $src[2];
	$scale = isset( $entry['before_scale'], $entry['before_src'] ) && 2 === count( $u_photo ) && sh_tl_is_design_file( $id, $entry['before_src'] ) ? (float) $entry['before_scale'] : 1.0;
	$scale = ( $scale > 0 && $scale < 1 ) ? $scale : 1.0;
	$box   = 1.0 === $scale ? sh_tl_contain( $ratio, ...$rule['photo'] ) : sh_tl_contain( $ratio, $scale * $u_photo[0], $scale * $u_photo[1] );
	return array(
		'id'    => $id,
		'w'     => min( $box[0], $file[0], $file[1] * $ratio ), // ⛔ never larger than the file's pixels (§4.5c.3).
		'ratio' => $ratio,
		'scale' => $scale,
	);
}

/**
 * "Before" layer of the photo under the line: inside the photo link, under the colour image, same centre (timeline.css).
 *
 * @param array  $item    sh_tl_item() with a `line`.
 * @param string $loading 'eager' | 'lazy'.
 */
function sh_tl_line_pic( array $item, string $loading ): string {
	$line = $item['line'];
	$tag  = wp_get_attachment_image(
		(int) $line['id'],
		'full',
		false,
		array(
			'class'     => 'sh-tl__line-img',
			'alt'       => '', // Decorative: the colour photo of the same object is the content (§6.4c D35/D36).
			'loading'   => $loading,
			'decoding'  => 'async',
			'draggable' => 'false',
			'sizes'     => (int) ceil( (float) $line['w'] ) . 'px',
		)
	);
	if ( '' === $tag ) {
		return '';
	}
	$style = sh_tl_style(
		array(
			'--lpw' => sh_tl_num( $line['w'] ),
			'--lar' => sh_tl_num( $line['ratio'] ),
			'--lbs' => sh_tl_num( isset( $line['scale'] ) ? $line['scale'] : 1 ), // D39: ≤ this × the photo (tablet/mobile).
		)
	);
	// $tag comes from wp_get_attachment_image() (attributes escaped by core).
	return '<span class="sh-tl__photo-line" style="' . esc_attr( $style ) . '" aria-hidden="true">' . $tag . '</span>';
}

/**
 * The item's image group: the box is sized by timeline.css; each image is cropped to its trimmed area.
 *
 * @param array  $item     sh_tl_item().
 * @param float  $width    Largest width the group is shown at (px at 1440) → `sizes`.
 * @param string $alt      Alt of the first image ('' = decorative duplicate).
 * @param string $loading  'eager' | 'lazy'.
 * @param string $priority '' | 'high'.
 */
function sh_tl_pic( array $item, float $width, string $alt, string $loading, string $priority = '' ): string {
	$html = '';
	foreach ( $item['images'] as $n => $img ) {
		list( $x0, $y0, $x1, $y1 ) = array_map( 'floatval', $img['crop'] );
		$cw    = max( 1.0, $x1 - $x0 );
		$ch    = max( 1.0, $y1 - $y0 );
		$attr  = array(
			'class'     => 'sh-tl-pic__img',
			'alt'       => 0 === $n ? $alt : '',
			'loading'   => $loading,
			'decoding'  => 'async',
			'draggable' => 'false',
			'sizes'     => (int) ceil( $width * $img['box'][2] * $img['src'][0] / $cw ) . 'px',
			'style'     => sh_tl_style(
				array(
					'width'  => sh_tl_num( 100 * $img['src'][0] / $cw ) . '%',
					'height' => sh_tl_num( 100 * $img['src'][1] / $ch ) . '%',
					'left'   => sh_tl_num( -100 * $x0 / $cw ) . '%',
					'top'    => sh_tl_num( -100 * $y0 / $ch ) . '%',
				)
			),
		);
		if ( '' !== $priority ) {
			$attr['fetchpriority'] = $priority;
		}
		$tag = wp_get_attachment_image( (int) $img['id'], 'full', false, $attr );
		if ( '' === $tag ) {
			continue;
		}
		$box   = array_map( static fn( $v ) => sh_tl_num( 100 * (float) $v ) . '%', $img['box'] );
		$style = sh_tl_style( array( 'left' => $box[0], 'top' => $box[1], 'width' => $box[2], 'height' => $box[3] ) );
		// $tag comes from wp_get_attachment_image() (attributes escaped by core).
		$html .= '<span class="sh-tl-pic__part" style="' . esc_attr( $style ) . '">' . $tag . '</span>';
	}
	return '<span class="sh-tl-pic">' . $html . '</span>';
}
