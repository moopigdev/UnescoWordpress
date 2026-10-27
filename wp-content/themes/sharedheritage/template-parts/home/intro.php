<?php
/**
 * Home section 2 — Introduction (Frame 12685 670:9368 [160,616 1120×552]): image card + text, then the zone bar image.
 * Fields: sh_home_intro_* · sh_home_zone_bar_image (CONTENT-MODEL §6).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_pid   = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_title = (string) sh_get( 'sh_home_intro_title', $sh_pid );
$sh_p1    = (string) sh_get( 'sh_home_intro_paragraph_1', $sh_pid );
$sh_p2    = (string) sh_get( 'sh_home_intro_paragraph_2', $sh_pid );
$sh_link  = (int) sh_get( 'sh_home_intro_link', $sh_pid );
$sh_url   = $sh_link > 0 ? (string) get_permalink( $sh_link ) : '';

$sh_card = sh_home_img(
	(int) sh_get( 'sh_home_intro_image', $sh_pid ),
	array(
		'alt_fallback' => $sh_title,
		'class'        => 'sh-home-intro__img',
		'sizes'        => '(min-width: 1168px) 551px, (min-width: 768px) 50vw, calc(100vw - 48px)',
	)
);
$sh_bar  = sh_home_img(
	(int) sh_get( 'sh_home_zone_bar_image', $sh_pid ),
	array(
		'alt_fallback' => __( 'Zones 1 to 5', 'sharedheritage' ),
		'class'        => 'sh-home-zonebar__img',
		'sizes'        => '(min-width: 1168px) 1120px, 100vw',
	)
);
?>
<section id="sh-home-intro" class="sh-home-intro sh-container" aria-labelledby="sh-home-intro-title">
	<div class="sh-home-intro__row">
		<div class="sh-home-intro__card">
			<?php echo sh_home_kses( $sh_card ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?>
		</div>
		<div class="sh-home-intro__text">
			<?php if ( '' !== $sh_title ) : ?>
				<h2 id="sh-home-intro-title" class="sh-t-page-title"><?php echo esc_html( $sh_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $sh_p1 ) : ?>
				<p class="sh-t-body-home"><?php echo wp_kses( sh_home_lines( $sh_p1 ), array( 'br' => array() ) ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $sh_p2 ) : ?>
				<p class="sh-t-body-home"><?php echo wp_kses( sh_home_lines( $sh_p2 ), array( 'br' => array() ) ); ?></p>
			<?php endif; ?>
			<?php
			if ( '' !== $sh_url ) {
				get_template_part(
					'template-parts/common/read-more',
					null,
					array(
						'url'     => $sh_url,
						'label'   => (string) get_option( 'sh_label_read_more', '' ),
						'variant' => 'home',
						'align'   => 'right',
					)
				);
			}
			?>
		</div>
	</div>

	<div class="sh-home-zonebar">
		<div class="sh-home-zonebar__scroll">
			<div class="sh-home-zonebar__box">
				<?php echo sh_home_kses( $sh_bar ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?>
			</div>
		</div>
	</div>
</section>
