<?php
/**
 * "Read more >>" link — DESIGN-SPEC §3.4.
 *
 * Variants:
 *   'home'    — Figma "Read more 1/2/3" (670:1784 · 886:1800 · 886:1826): Komet-Regular 16/22 #212529
 *               → hover Komet-Bold 16/22 #D65F47 underline (variant2, no transition).
 *   'default' — intro / trail / timeline: Barlow Condensed Regular 20/22 #212529; hover has no Figma
 *               variant → same behaviour as Home (Bold + underline #D65F47) per DESIGN-SPEC §3.4.
 *
 * Usage:
 *   get_template_part( 'template-parts/common/read-more', null, array(
 *       'url'      => get_permalink(),  // link target; empty => <button> (e.g. Home "Trails" expand text)
 *       'label'    => __( 'Read more >>', 'sharedheritage' ),
 *       'variant'  => 'home',           // 'home' | 'default'
 *       'align'    => 'right',          // '' (inline, no wrapper) | 'left' | 'center' | 'right'
 *       'controls' => 'element-id',     // button only: aria-controls (page JS toggles aria-expanded)
 *       'class'    => 'my-extra-class',
 *   ) );
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args     = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_url      = isset( $sh_args['url'] ) && is_string( $sh_args['url'] ) ? $sh_args['url'] : '';
$sh_label    = isset( $sh_args['label'] ) && is_string( $sh_args['label'] ) && '' !== $sh_args['label'] ? $sh_args['label'] : __( 'Read more >>', 'sharedheritage' );
$sh_variant  = ( isset( $sh_args['variant'] ) && 'home' === $sh_args['variant'] ) ? 'home' : 'default';
$sh_align    = ( isset( $sh_args['align'] ) && in_array( $sh_args['align'], array( 'left', 'center', 'right' ), true ) ) ? $sh_args['align'] : '';
$sh_controls = isset( $sh_args['controls'] ) && is_string( $sh_args['controls'] ) ? $sh_args['controls'] : '';

$sh_class = 'sh-read-more sh-read-more--' . $sh_variant;
if ( ! empty( $sh_args['class'] ) && is_string( $sh_args['class'] ) ) {
	$sh_class .= ' ' . implode( ' ', array_map( 'sanitize_html_class', preg_split( '/\s+/', trim( $sh_args['class'] ) ) ) );
}

if ( '' !== $sh_align ) {
	echo '<div class="' . esc_attr( 'sh-read-more-row sh-read-more-row--' . $sh_align ) . '">';
}

if ( '' !== $sh_url ) :
	?>
	<a class="<?php echo esc_attr( $sh_class ); ?>" href="<?php echo esc_url( $sh_url ); ?>"><?php echo esc_html( $sh_label ); ?></a>
	<?php
else :
	?>
	<button class="<?php echo esc_attr( $sh_class ); ?>" type="button" aria-expanded="false"<?php echo '' !== $sh_controls ? ' aria-controls="' . esc_attr( $sh_controls ) . '"' : ''; ?>><?php echo esc_html( $sh_label ); ?></button>
	<?php
endif;

if ( '' !== $sh_align ) {
	echo '</div>';
}
