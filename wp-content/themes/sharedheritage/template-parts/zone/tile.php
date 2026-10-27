<?php
/**
 * Trail tile (§3.6 · component set `Bird 1` 1018:1372): default 184×184 image; hover swaps to the 210×210 image
 * (coloured background + object, a different file) · 0.3s ease-in-out. D15 (P-101): a tile links to /trail/<slug>/
 * only when the trail has published objects; otherwise it renders the same but without a link.
 *
 * Args: trail (WP_Term).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_trail = isset( $sh_args['trail'] ) && $sh_args['trail'] instanceof WP_Term ? $sh_args['trail'] : null;
if ( ! $sh_trail ) {
	return;
}

$sh_bg    = sanitize_hex_color( (string) sh_get_term( 'sh_tile_bg', $sh_trail ) );
$sh_img   = absint( sh_get_term( 'sh_tile_zone_grid', $sh_trail ) );
$sh_hover = absint( sh_get_term( 'sh_tile_zone_grid_hover', $sh_trail ) );

$sh_img_html   = $sh_img ? wp_get_attachment_image(
	$sh_img,
	'full',
	false,
	array(
		'class'    => 'sh-tile__img',
		'alt'      => $sh_trail->name,
		'loading'  => 'lazy',
		'decoding' => 'async',
		'sizes'    => '(min-width: 1168px) 184px, 20vw',
	)
) : '';
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

$sh_link  = sh_trail_objects( $sh_trail ) ? get_term_link( $sh_trail ) : '';
$sh_link  = ( '' !== $sh_link && ! is_wp_error( $sh_link ) ) ? (string) $sh_link : '';
$sh_class = 'sh-tile' . ( '' === $sh_hover_html ? ' sh-tile--no-hover' : '' );
?>
<li class="<?php echo esc_attr( $sh_class ); ?>"<?php echo $sh_bg ? ' style="' . esc_attr( '--sh-tile-bg:' . $sh_bg ) . '"' : ''; ?>>
	<?php if ( '' !== $sh_link ) : ?>
		<a class="sh-tile__link" href="<?php echo esc_url( $sh_link ); ?>">
	<?php endif; ?>
	<?php if ( '' !== $sh_img_html ) : ?>
		<?php echo $sh_img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
	<?php else : ?>
		<span class="sh-tile__name sh-t-card-title"><?php echo esc_html( $sh_trail->name ); ?></span>
	<?php endif; ?>
	<?php echo $sh_hover_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
	<?php if ( '' !== $sh_link ) : ?>
		</a>
	<?php endif; ?>
</li>
