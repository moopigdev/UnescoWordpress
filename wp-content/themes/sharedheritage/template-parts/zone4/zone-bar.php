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

$sh_tabs = array();
if ( $sh_links ) {
	foreach ( sh_zones() as $sh_z ) {
		$sh_n = (int) sh_get_term( 'sh_number', $sh_z );
		if ( $sh_n < 1 || $sh_n > 5 ) {
			continue;
		}
		$sh_url  = '';
		$sh_page = absint( sh_get_term( 'sh_link_page', $sh_z ) );
		if ( $sh_z->term_id !== $sh_zone->term_id ) {
			if ( $sh_page && 'publish' === get_post_status( $sh_page ) ) {
				$sh_url = (string) get_permalink( $sh_page );
			} else {
				$sh_link = get_term_link( $sh_z );
				$sh_url  = is_string( $sh_link ) ? $sh_link : '';
			}
		}
		$sh_tabs[ $sh_n ] = array(
			'name'    => $sh_z->name,
			'url'     => $sh_url,
			'current' => $sh_z->term_id === $sh_zone->term_id,
		);
	}
	ksort( $sh_tabs );
}
?>
<figure class="sh-zbar<?php echo $sh_crop ? ' sh-zbar--crop' : ''; ?>">
	<div class="sh-zbar__view" data-sh-zbar data-sh-zone="<?php echo esc_attr( (string) $sh_number ); ?>" data-sh-label="<?php echo esc_attr__( 'Zones 1 to 5', 'sharedheritage' ); ?>">
		<div class="sh-zbar__strip">
			<?php echo $sh_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
			<?php if ( $sh_tabs ) : ?>
				<nav class="sh-zbar__nav" aria-label="<?php echo esc_attr__( 'Zones', 'sharedheritage' ); ?>">
					<ul class="sh-zbar__tabs">
						<?php foreach ( $sh_tabs as $sh_n => $sh_tab ) : ?>
							<?php
							$sh_label = sprintf(
								/* translators: 1: zone number, 2: zone name. */
								__( 'Zone %1$d: %2$s', 'sharedheritage' ),
								$sh_n,
								wp_specialchars_decode( $sh_tab['name'], ENT_QUOTES )
							);
							?>
							<li class="sh-zbar__tab" style="<?php echo esc_attr( '--sh-zbar-i:' . ( $sh_n - 1 ) ); ?>">
								<?php if ( $sh_tab['current'] ) : ?>
									<span class="sh-zbar__hit" aria-current="page"><span class="screen-reader-text"><?php echo esc_html( $sh_label ); ?></span></span>
								<?php elseif ( '' !== $sh_tab['url'] ) : ?>
									<a class="sh-zbar__hit sh-zbar__hit--link" href="<?php echo esc_url( $sh_tab['url'] ); ?>"><span class="screen-reader-text"><?php echo esc_html( $sh_label ); ?></span></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</figure>
