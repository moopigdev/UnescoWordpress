<?php
/**
 * Home section 3 — Zone bar (Frame 12684 [160,1030 1120×166] · DESIGN-SPEC §9.2 row 3): the ZONE 1–5 strip image.
 * Own section since 2026-11-17 (it used to live inside Introduction). Field: sh_home_zone_bar_image.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_pid  = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_bar  = sh_home_img(
	(int) sh_get( 'sh_home_zone_bar_image', $sh_pid ),
	array(
		'alt_fallback' => __( 'Zones 1 to 5', 'sharedheritage' ),
		'class'        => 'sh-home-zonebar__img',
		'sizes'        => '(min-width: 1168px) 1120px, 100vw',
	)
);
?>
<section id="sh-home-zonebar" class="sh-home-zonebar sh-container" aria-label="<?php esc_attr_e( 'Zones 1 to 5', 'sharedheritage' ); ?>">
	<div class="sh-home-zonebar__scroll">
		<div class="sh-home-zonebar__box">
			<?php echo sh_home_kses( $sh_bar ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?>
		</div>
	</div>
</section>
