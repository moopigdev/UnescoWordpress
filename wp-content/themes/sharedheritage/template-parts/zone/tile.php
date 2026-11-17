<?php
/**
 * Zone object tile (§3.6 · §8.2 · D57): default 184×184 image; hover swaps to the 210×210 image (a different file) ·
 * 0.3s ease-in-out. A tile with a published object links to its detail page (`?from=zone-<n>` tells the Back button
 * which zone page the visitor came from · see template-parts/detail/panel.php); a tile without one is the same
 * picture, no link (same hover, normal cursor).
 *
 * Args: tile (array from sh_zone_grid(): object · url · label · image · hover) · zone (WP_Term).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_tile = isset( $sh_args['tile'] ) && is_array( $sh_args['tile'] ) ? $sh_args['tile'] : array();
$sh_zone = isset( $sh_args['zone'] ) && $sh_args['zone'] instanceof WP_Term ? $sh_args['zone'] : null;
$sh_img  = (int) ( $sh_tile['image'] ?? 0 );
if ( ! $sh_img ) {
	return;
}
$sh_hover = (int) ( $sh_tile['hover'] ?? 0 );
$sh_name  = (string) ( $sh_tile['label'] ?? '' );
$sh_url   = (string) ( $sh_tile['url'] ?? '' );
if ( '' !== $sh_url && $sh_zone ) {
	$sh_url = add_query_arg( 'from', $sh_zone->slug, $sh_url );
}

$sh_img_html   = wp_get_attachment_image(
	$sh_img,
	'full',
	false,
	array(
		'class'    => 'sh-tile__img',
		'alt'      => '' !== $sh_url ? '' : $sh_name,
		'loading'  => 'lazy',
		'decoding' => 'async',
		'sizes'    => '(min-width: 1168px) 184px, 20vw',
	)
);
$sh_hover_html = $sh_hover ? wp_get_attachment_image(
	$sh_hover,
	'full',
	false,
	array(
		'class'       => 'sh-tile__img sh-tile__img--hover',
		'alt'         => '',
		'aria-hidden' => 'true',
		'loading'     => 'lazy',
		'decoding'    => 'async',
		'sizes'       => '(min-width: 1168px) 210px, 23vw',
	)
) : '';
if ( '' === $sh_img_html ) {
	return;
}
$sh_class = 'sh-tile' . ( '' === $sh_hover_html ? ' sh-tile--no-hover' : '' );
?>
<li class="<?php echo esc_attr( $sh_class ); ?>">
	<?php if ( '' !== $sh_url ) : ?>
		<a class="sh-tile__link" href="<?php echo esc_url( $sh_url ); ?>"<?php echo '' !== $sh_name ? ' aria-label="' . esc_attr( $sh_name ) . '"' : ''; ?>>
	<?php endif; ?>
	<?php echo $sh_img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
	<?php echo $sh_hover_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
	<?php if ( '' !== $sh_url ) : ?>
		</a>
	<?php endif; ?>
</li>
