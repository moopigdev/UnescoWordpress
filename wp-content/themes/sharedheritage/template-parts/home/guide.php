<?php
/**
 * Home section 5 — "Your animal guide is..." (D48 · DESIGN-SPEC §9.3 · Figma 3339:5265 [160,1849 1120×440]).
 * Start state = card "?" (sh_home_guide_card_back). A click on the card or the CTA picks 1 of the 17 cards at random
 * (assets/js/home.js · never the one on show); "Read more >>" unfolds the full text, "Hide >>" folds it.
 * Data: sh_home_guide_* (Home page) + sh_guide_cards() (one per trail · nothing hardcoded). Every card is a <template>
 * (inert: no image requested until it is picked); without JS the section shows the "?" card and the text.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args    = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_pid     = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_title   = (string) sh_get( 'sh_home_guide_title', $sh_pid );
$sh_cta     = (string) sh_get( 'sh_home_guide_cta', $sh_pid );
$sh_paras   = array();
foreach ( array( 'opening', 'heritage', 'question', 'instructions' ) as $sh_key ) {
	$sh_text = (string) sh_get( 'sh_home_guide_' . $sh_key, $sh_pid );
	if ( '' !== trim( $sh_text ) ) {
		$sh_paras[] = $sh_text;
	}
}
$sh_cards = sh_guide_cards();
if ( '' === $sh_title && ! $sh_paras && ! $sh_cards ) {
	return;
}

$sh_hide = (string) get_option( 'sh_label_hide', '' );
if ( '' === trim( $sh_hide ) ) {
	$sh_hide = __( 'Hide >>', 'sharedheritage' );
}
$sh_more = (string) get_option( 'sh_label_read_more', '' );
if ( '' === trim( $sh_more ) ) {
	$sh_more = __( 'Read more >>', 'sharedheritage' );
}

$sh_back = sh_home_img(
	(int) sh_get( 'sh_home_guide_card_back', $sh_pid ),
	array(
		'alt_fallback' => '',
		'class'        => 'sh-guide__card-img',
		'sizes'        => '120px',
		'eager'        => true,
	)
);
if ( '' === $sh_cta ) {
	$sh_cta = __( 'CHOOSE A CARD', 'sharedheritage' );
}
$sh_kses_html = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
);
$sh_pick = $sh_cards ? 'button' : 'span'; // no cards (admin emptied them) → inert, nothing broken.
?>
<section id="sh-home-guide" class="sh-guide sh-container" aria-labelledby="sh-guide-title"
	data-sh-guide data-label-more="<?php echo esc_attr( $sh_more ); ?>" data-label-hide="<?php echo esc_attr( $sh_hide ); ?>"
	data-label-first="<?php esc_attr_e( 'Pick a card', 'sharedheritage' ); ?>" data-label-again="<?php esc_attr_e( 'Pick another card', 'sharedheritage' ); ?>">
	<?php if ( '' !== $sh_title ) : ?>
		<h2 id="sh-guide-title" class="sh-guide__title"><?php echo esc_html( $sh_title ); ?></h2>
	<?php endif; ?>

	<div class="sh-guide__row">
		<div class="sh-guide__text">
			<?php foreach ( $sh_paras as $sh_para ) : ?>
				<p class="sh-guide__p"><?php echo wp_kses( sh_home_lines( $sh_para ), array( 'br' => array() ) ); ?></p>
			<?php endforeach; ?>
			<<?php echo esc_attr( $sh_pick ); ?> class="sh-guide__cta"<?php echo $sh_cards ? ' type="button" data-sh-guide-pick' : ''; ?>><?php echo esc_html( $sh_cta ); ?></<?php echo esc_attr( $sh_pick ); ?>>
		</div>
		<<?php echo esc_attr( $sh_pick ); ?> class="sh-guide__card"<?php echo $sh_cards ? ' type="button" data-sh-guide-pick aria-label="' . esc_attr__( 'Pick a card', 'sharedheritage' ) . '"' : ''; ?>>
			<?php echo sh_home_kses( $sh_back ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?>
			<?php if ( '' === $sh_back ) : ?>
				<span class="sh-guide__card-fallback" aria-hidden="true">?</span>
			<?php endif; ?>
		</<?php echo esc_attr( $sh_pick ); ?>>
	</div>

	<p class="screen-reader-text" role="status" aria-live="polite" data-sh-guide-live></p>

	<?php if ( $sh_cards ) : ?>
		<div class="sh-guide__result" data-sh-guide-result>
			<div class="sh-guide__result-clip">
				<div class="sh-guide__result-pad" data-sh-guide-slot></div>
			</div>
		</div>

		<?php foreach ( $sh_cards as $sh_i => $sh_c ) : ?>
			<?php
			$sh_big = sh_home_img(
				(int) $sh_c['card'],
				array(
					'alt_fallback' => $sh_c['name'],
					'class'        => 'sh-guide__big-img',
					'sizes'        => '230px',
				)
			);
			?>
			<template data-sh-guide-card="<?php echo (int) $sh_i; ?>">
				<h3 class="sh-guide__name"><?php echo esc_html( $sh_c['name'] ); ?></h3>
				<p class="sh-guide__words"><?php echo esc_html( $sh_c['words'] ); ?></p>
				<div class="sh-guide__more" id="sh-guide-more">
					<div class="sh-guide__more-clip">
						<div class="sh-guide__more-pad">
							<div class="sh-guide__body">
								<?php if ( '' !== $sh_c['url'] && '' !== $sh_big ) : ?>
									<a class="sh-guide__big" href="<?php echo esc_url( $sh_c['url'] ); ?>"><?php echo sh_home_kses( $sh_big ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?></a>
								<?php elseif ( '' !== $sh_big ) : ?>
									<span class="sh-guide__big"><?php echo sh_home_kses( $sh_big ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?></span>
								<?php endif; ?>
								<div class="sh-guide__copy">
									<?php foreach ( $sh_c['body'] as $sh_p ) : ?>
										<p><?php echo wp_kses( $sh_p, $sh_kses_html ); ?></p>
									<?php endforeach; ?>
								</div>
							</div>
							<?php if ( '' !== $sh_c['follow'] && '' !== $sh_c['url'] ) : ?>
								<a class="sh-guide__follow" href="<?php echo esc_url( $sh_c['url'] ); ?>"><?php echo esc_html( $sh_c['follow'] ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="sh-guide__toggle">
					<?php
					get_template_part(
						'template-parts/common/read-more',
						null,
						array(
							'url'      => '',
							'label'    => $sh_more,
							'variant'  => 'home',
							'controls' => 'sh-guide-more',
							'class'    => 'sh-guide__toggle-btn',
						)
					);
					?>
				</div>
			</template>
		<?php endforeach; ?>
	<?php endif; ?>
</section>
