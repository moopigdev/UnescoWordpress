<?php
/**
 * Trail heading — Frame 12683 [160,677.2 1120×112]: title (36/45 Heavy) + description (Komet 18/22.5).
 * The Figma "Read more >>" (797:1883) is hidden in the design, so it is not rendered.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_trail = isset( $sh_args['trail'] ) && $sh_args['trail'] instanceof WP_Term ? $sh_args['trail'] : null;
if ( null === $sh_trail ) {
	return;
}

$sh_layout = isset( $sh_args['layout'] ) && is_array( $sh_args['layout'] ) ? $sh_args['layout'] : array();
$sh_title  = (string) sh_get_term( 'sh_title_on_trail_page', $sh_trail );
if ( '' === $sh_title ) {
	$sh_title = $sh_trail->name;
}
$sh_paras = sh_paragraphs( (string) $sh_trail->description );
// D49 trails: description justified (Figma text align JUSTIFIED · DESIGN-SPEC §8.1); Elephant keeps §4.4 as built.
$sh_justify = 'JUSTIFIED' === ( $sh_layout['description_font']['align'] ?? '' );
?>
<section class="sh-trail__intro">
	<h1 class="sh-trail__title sh-t-page-title"><?php echo implode( '<br>', array_map( 'esc_html', sh_trail_title_parts( $sh_title, $sh_layout ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each line escaped. ?></h1>
	<?php if ( $sh_paras ) : ?>
		<div class="sh-trail__desc<?php echo $sh_justify ? ' sh-trail__desc--justify' : ''; ?>">
			<?php foreach ( $sh_paras as $sh_para ) : ?>
				<p><?php echo esc_html( $sh_para ); ?></p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
