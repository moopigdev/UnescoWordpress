<?php
/**
 * Zone 5 image row (`Frame 114492` [160,1254 1120×123] · 4 × 280×123, no gap) — DESIGN-SPEC §4.11a #4.
 * `sh_page_strip_image_<1..4>` · decoration (alt "", no hover/link) · an empty slot keeps its place; no image = no row.
 *
 * Args: zone (WP_Term).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_zone = isset( $sh_args['zone'] ) && $sh_args['zone'] instanceof WP_Term ? $sh_args['zone'] : null;
if ( ! $sh_zone ) {
	return;
}

$sh_items = array();
for ( $sh_n = 1; $sh_n <= 4; $sh_n++ ) {
	$sh_id      = absint( sh_get_term( 'sh_page_strip_image_' . $sh_n, $sh_zone ) );
	$sh_items[] = $sh_id ? wp_get_attachment_image(
		$sh_id,
		'full',
		false,
		array(
			'class'    => 'sh-z5__strip-img',
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'sizes'    => '(min-width: 1168px) 280px, (min-width: 768px) 25vw, 50vw',
		)
	) : '';
}
if ( '' === implode( '', $sh_items ) ) {
	return;
}
?>
<div class="sh-z5__strip">
	<?php foreach ( $sh_items as $sh_html ) : ?>
		<div class="sh-z5__strip-item"><?php echo $sh_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?></div>
	<?php endforeach; ?>
</div>
