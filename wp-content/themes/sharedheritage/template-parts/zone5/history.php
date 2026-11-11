<?php
/**
 * Zone 5 History (`History` [160,1402 1120×453]) — DESIGN-SPEC §4.11a #5: `sh_page_history_image` with
 * `sh_page_history_image_hover` cross-faded on hover · not a link (no target in Figma).
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

$sh_image = absint( sh_get_term( 'sh_page_history_image', $sh_zone ) );
if ( ! $sh_image || ! wp_attachment_is_image( $sh_image ) ) {
	return;
}
?>
<figure class="sh-z5__history">
	<?php
	get_template_part(
		'template-parts/zone5/swap',
		null,
		array(
			'image' => $sh_image,
			'hover' => absint( sh_get_term( 'sh_page_history_image_hover', $sh_zone ) ),
			'class' => 'sh-z5__history',
			'sizes' => '(min-width: 1168px) 1120px, 100vw',
		)
	);
	?>
</figure>
