<?php
/**
 * Home section 1 — Cover [160,150 1120×466] (670:9366). One raster image; its text is part of the picture (P-207 · D8).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_pid  = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_img  = sh_home_img(
	(int) sh_get( 'sh_home_hero_image', $sh_pid ),
	array(
		'alt_fallback' => get_bloginfo( 'name' ),
		'class'        => 'sh-home-hero__img',
		'sizes'        => '(min-width: 1168px) 1120px, calc(100vw - 48px)',
		'eager'        => true,
		'priority'     => true,
	)
);
?>
<section class="sh-home-hero sh-container" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
	<div class="sh-home-hero__frame">
		<?php echo sh_home_kses( $sh_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?>
	</div>
</section>
