<?php
/**
 * Zone 5 hero row (`Frame 114483` [160,150 1120×404] · padding-top 20 · gap 20): Cover [160,170 551×384]
 * (`sh_cover_image`) and the text column [731,170 549×347] (padding-top 40 · gap 20): H1 = zone name (Komet Heavy
 * 36/45 #212529) + `sh_intro` paragraphs (18/22 justified #000 · blank line between) — DESIGN-SPEC §4.11a #1.
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

$sh_cover      = absint( sh_get_term( 'sh_cover_image', $sh_zone ) );
$sh_cover_html = $sh_cover ? wp_get_attachment_image(
	$sh_cover,
	'full',
	false,
	array(
		'class'         => 'sh-z5__cover-img',
		'loading'       => 'eager',
		'decoding'      => 'async',
		'fetchpriority' => 'high',
		'sizes'         => '(min-width: 1168px) 551px, (min-width: 768px) 50vw, 100vw',
	)
) : '';
$sh_paras      = sh_paragraphs( (string) sh_get_term( 'sh_intro', $sh_zone ) );
?>
<div class="sh-z5__hero<?php echo '' === $sh_cover_html ? ' sh-z5__hero--no-cover' : ''; ?>">
	<?php if ( '' !== $sh_cover_html ) : ?>
		<figure class="sh-z5__cover">
			<?php echo $sh_cover_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
		</figure>
	<?php endif; ?>
	<div class="sh-z5__text">
		<h1 class="sh-t-page-title sh-z5__title"><?php echo esc_html( $sh_zone->name ); ?></h1>
		<?php if ( $sh_paras ) : ?>
			<div class="sh-t-body-home sh-z5__intro">
				<?php foreach ( $sh_paras as $sh_p ) : ?>
					<p><?php echo nl2br( esc_html( $sh_p ), false ); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
