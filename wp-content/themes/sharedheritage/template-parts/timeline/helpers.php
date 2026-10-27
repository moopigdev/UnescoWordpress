<?php
/**
 * Timeline helpers (page-timeline.php only). Loaded with require_once — not a template part.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

/**
 * Number → CSS px length (max 3 decimals, as in the Figma dump).
 *
 * @param float|int $value Pixels.
 */
function sh_timeline_px( $value ): string {
	return rtrim( rtrim( number_format( (float) $value, 3, '.', '' ), '0' ), '.' ) . 'px';
}

/**
 * Inline style from CSS custom properties (values are numbers/keywords built here, escaped on output).
 *
 * @param array<string, string> $vars Property name (without --) => value.
 */
function sh_timeline_style( array $vars ): string {
	$out = array();
	foreach ( $vars as $name => $value ) {
		$out[] = '--' . $name . ':' . $value;
	}
	return implode( ';', $out );
}

/**
 * CSS matrix() that places an image (drawn at the box size) the way Figma's STRETCH imageTransform does.
 *
 * Figma t = [a, b, tx, c, d, ty] maps box-normalised coords p to image UV: uv = A·p + t.
 * The image is the inverse: p = A⁻¹·uv − A⁻¹·t, converted from normalised to pixel space (w × h).
 *
 * @param float[] $t Figma imageTransform (6 numbers).
 * @param float   $w Box width.
 * @param float   $h Box height.
 * @return string '' when the transform is not invertible.
 */
function sh_timeline_crop_matrix( array $t, float $w, float $h ): string {
	list( $a, $b, $tx, $c, $d, $ty ) = array_map( 'floatval', array_values( $t ) );
	$det = $a * $d - $b * $c;
	if ( abs( $det ) < 1e-9 || $w <= 0 || $h <= 0 ) {
		return '';
	}
	$m00 = $d / $det;
	$m01 = -$b / $det;
	$m10 = -$c / $det;
	$m11 = $a / $det;
	$o0  = -( $m00 * $tx + $m01 * $ty );
	$o1  = -( $m10 * $tx + $m11 * $ty );
	$num = static fn( float $v ): string => rtrim( rtrim( number_format( $v, 6, '.', '' ), '0' ), '.' );
	return 'matrix(' . implode( ',', array( $num( $m00 ), $num( $m10 * $h / $w ), $num( $m01 * $w / $h ), $num( $m11 ), $num( $w * $o0 ), $num( $h * $o1 ) ) ) . ')';
}

/**
 * An image inside a fixed box, cropped like Figma when the attachment is the Figma source file.
 *
 * Box: w, h (+ x, y inside a media group) · src [width, height] · t (imageTransform) or fit 'cover'.
 * Attachment of another size (replaced in admin) → whole image, object-fit: contain (never distorted).
 *
 * @param int                  $id    Attachment id.
 * @param array<string, mixed> $box   Geometry.
 * @param string               $class Extra class on the box.
 * @param array<string, string> $attr Extra <img> attributes (loading, alt …).
 * @return string Markup ('' when the attachment has no image).
 */
function sh_timeline_image( int $id, array $box, string $class = '', array $attr = array() ): string {
	if ( ! $id || ! wp_attachment_is_image( $id ) ) {
		return '';
	}
	$w    = isset( $box['w'] ) ? (float) $box['w'] : 0.0;
	$h    = isset( $box['h'] ) ? (float) $box['h'] : 0.0;
	$meta = wp_get_attachment_metadata( $id );
	$same = is_array( $meta ) && isset( $box['src'][0], $box['src'][1], $meta['width'], $meta['height'] )
		&& (int) $meta['width'] === (int) $box['src'][0] && (int) $meta['height'] === (int) $box['src'][1];

	$mode   = 'contain';
	$matrix = '';
	if ( $same && ! empty( $box['t'] ) && is_array( $box['t'] ) && 6 === count( $box['t'] ) ) {
		$matrix = sh_timeline_crop_matrix( $box['t'], $w, $h );
		$mode   = '' !== $matrix ? 'crop' : 'contain';
	} elseif ( isset( $box['fit'] ) && 'cover' === $box['fit'] ) {
		$mode = 'cover';
	}

	$vars = array();
	if ( $w > 0 && $h > 0 ) {
		$vars['bw'] = sh_timeline_px( $w );
		$vars['bh'] = sh_timeline_px( $h );
	}
	if ( isset( $box['x'], $box['y'] ) ) {
		$vars['bx'] = sh_timeline_px( (float) $box['x'] );
		$vars['by'] = sh_timeline_px( (float) $box['y'] );
	}
	$img_attr = array_merge(
		array(
			'class'     => 'sh-tl-img__img',
			'alt'       => '',
			'loading'   => 'lazy',
			'decoding'  => 'async',
			'draggable' => 'false',
		),
		$attr
	);
	if ( '' !== $matrix ) {
		$img_attr['style'] = 'transform:' . $matrix;
	}
	$img = wp_get_attachment_image( $id, 'full', false, $img_attr );
	if ( '' === $img ) {
		return '';
	}
	$cls = trim( 'sh-tl-img sh-tl-img--' . $mode . ' ' . $class );
	// $img is built by wp_get_attachment_image() (attributes escaped by core).
	return '<span class="' . esc_attr( $cls ) . '" style="' . esc_attr( sh_timeline_style( $vars ) ) . '">' . $img . '</span>';
}
