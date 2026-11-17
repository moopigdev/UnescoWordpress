<?php
/**
 * ZONE 1–5 bar of a zone page (`sh_banner_image` · 1120×166 · Drop Shadow/200) — Zone 4 chooser (§4.10) and Zone 5 (§4.11).
 *
 * The bar is one raster image (labels baked in · D8). With `links` on (Zone 4 chooser · D40 / P-248) five transparent
 * hotspots sit on the tab row (x = 0/20/40/60/80 % · tab row y 833–876 of the bar 710–876 → from 74.1 %): other zones
 * link to their zone page (`sh_link_page` when set, else the term link) and the current zone is marked aria-current.
 * A 4096×965 export (Figma imageTransform x 2.556 %–97.46 % · y 14.10 %–73.80 %, the zone 1–3/5 bar source) is cropped
 * like the zone 1–3 banner; any other shape fills the box.
 *
 * The hotspots come from template-parts/zone/tabs.php (shared with the zone 1–3 pages and Home · D56).
 *
 * Args: zone (WP_Term, current zone) · links (bool, default false).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_zone  = isset( $sh_args['zone'] ) && $sh_args['zone'] instanceof WP_Term ? $sh_args['zone'] : null;
$sh_links = ! empty( $sh_args['links'] );
if ( ! $sh_zone ) {
	return;
}

$sh_banner = absint( sh_get_term( 'sh_banner_image', $sh_zone ) );
if ( ! $sh_banner ) {
	return;
}

$sh_number = (int) sh_get_term( 'sh_number', $sh_zone );
$sh_img    = wp_get_attachment_image(
	$sh_banner,
	'full',
	false,
	array(
		'class'    => 'sh-zbar__img',
		/* translators: %d: zone number. */
		'alt'      => $sh_links ? '' : sprintf( __( 'Zone %d of 5', 'sharedheritage' ), $sh_number ),
		'loading'  => 'eager',
		'decoding' => 'async',
		'sizes'    => '(min-width: 1168px) 1120px, 100vw',
	)
);
if ( '' === $sh_img ) {
	return;
}

$sh_meta = wp_get_attachment_metadata( $sh_banner );
$sh_w    = is_array( $sh_meta ) && ! empty( $sh_meta['width'] ) ? (float) $sh_meta['width'] : 0.0;
$sh_h    = is_array( $sh_meta ) && ! empty( $sh_meta['height'] ) ? (float) $sh_meta['height'] : 0.0;
$sh_crop = $sh_w > 0 && $sh_h > 0 && abs( ( $sh_w / $sh_h ) / ( 4096 / 965 ) - 1 ) < 0.01;
?>
<figure class="sh-zbar<?php echo $sh_crop ? ' sh-zbar--crop' : ''; ?>">
	<div class="sh-zbar__view" data-sh-zbar data-sh-zone="<?php echo esc_attr( (string) $sh_number ); ?>" data-sh-label="<?php echo esc_attr__( 'Zones 1 to 5', 'sharedheritage' ); ?>">
		<div class="sh-zbar__strip">
			<?php echo $sh_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
			<?php
			if ( $sh_links ) {
				get_template_part( 'template-parts/zone/tabs', null, array( 'current' => $sh_zone ) );
			}
			?>
		</div>
	</div>
</figure>
