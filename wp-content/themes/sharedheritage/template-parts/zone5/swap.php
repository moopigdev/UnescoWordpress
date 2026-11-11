<?php
/**
 * Zone 5 image with an optional hover image stacked on top (cards · History — DESIGN-SPEC §4.11a: ON_HOVER
 * smart-animate 0.3s between two variants whose parts move → cross-fade). The hover image is decorative (alt "").
 *
 * Args: image (int) · hover (int) · class (string, base class: `<class>__img` / `<class>__img--hover`) · sizes (string).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_image = isset( $sh_args['image'] ) ? absint( $sh_args['image'] ) : 0;
$sh_hover = isset( $sh_args['hover'] ) ? absint( $sh_args['hover'] ) : 0;
$sh_class = isset( $sh_args['class'] ) && is_string( $sh_args['class'] ) ? sanitize_html_class( $sh_args['class'] ) : 'sh-z5-swap';
$sh_sizes = isset( $sh_args['sizes'] ) && is_string( $sh_args['sizes'] ) ? $sh_args['sizes'] : '100vw';
if ( ! $sh_image ) {
	return;
}

echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output.
	$sh_image,
	'full',
	false,
	array(
		'class'    => $sh_class . '__img',
		'loading'  => 'lazy',
		'decoding' => 'async',
		'sizes'    => $sh_sizes,
	)
);
if ( $sh_hover && $sh_hover !== $sh_image ) {
	echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output.
		$sh_hover,
		'full',
		false,
		array(
			'class'       => $sh_class . '__img ' . $sh_class . '__img--hover',
			'alt'         => '',
			'aria-hidden' => 'true',
			'loading'     => 'lazy',
			'decoding'    => 'async',
			'sizes'       => $sh_sizes,
		)
	);
}
