<?php
/**
 * Zone page text block — "Detail text Zone 1" (component set 3117:5051 · DESIGN-SPEC §4.3b · BACKLOG #8).
 * [160,826 1120×H] column gap 20: heading = zone name (Komet Heavy 34/42.5 centred) · sh_intro (20/22 centred,
 * one paragraph per line, no paragraph gap) · "Read more >>" → sh_intro_full replaces the text and the button reads
 * sh_label_hide ("Hide >>"); clicking again folds it back (0.3s · assets/js/zone.js). Empty sh_intro_full = no button.
 * Without JS the full text is shown and the button is hidden.
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

$sh_short = sh_paragraphs( (string) sh_get_term( 'sh_intro', $sh_zone ) );
$sh_full  = sh_paragraphs( (string) sh_get_term( 'sh_intro_full', $sh_zone ) );
$sh_id    = 'sh-zone-intro-' . (int) $sh_zone->term_id;

$sh_hide = (string) get_option( 'sh_label_hide', '' );
if ( '' === trim( $sh_hide ) ) {
	$sh_hide = __( 'Hide >>', 'sharedheritage' );
}
?>
<section class="sh-zone__intro<?php echo $sh_full ? ' has-more' : ''; ?>" aria-labelledby="<?php echo esc_attr( $sh_id . '-title' ); ?>" data-sh-label-hide="<?php echo esc_attr( $sh_hide ); ?>">
	<h1 id="<?php echo esc_attr( $sh_id . '-title' ); ?>" class="sh-t-section-title sh-zone__intro-title"><?php echo esc_html( $sh_zone->name ); ?></h1>

	<?php if ( $sh_short || $sh_full ) : ?>
		<div id="<?php echo esc_attr( $sh_id ); ?>" class="sh-zone__intro-body">
			<?php if ( $sh_short ) : ?>
				<div class="sh-zone__intro-text sh-zone__intro-text--short">
					<?php foreach ( $sh_short as $sh_para ) : ?>
						<p class="sh-t-lead"><?php echo esc_html( $sh_para ); ?></p>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( $sh_full ) : ?>
				<div class="sh-zone__intro-text sh-zone__intro-text--full">
					<?php foreach ( $sh_full as $sh_para ) : ?>
						<p class="sh-t-lead"><?php echo esc_html( $sh_para ); ?></p>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php
	if ( $sh_full ) {
		get_template_part(
			'template-parts/common/read-more',
			null,
			array(
				'url'      => '',
				'label'    => (string) get_option( 'sh_label_read_more', '' ),
				'variant'  => 'home',
				'align'    => 'center',
				'controls' => $sh_id,
				'class'    => 'sh-zone__intro-toggle',
			)
		);
	}
	?>
</section>
