<?php
/**
 * Back button — Figma "Button Back B1" (component set 670:1810 · DESIGN-SPEC §3.3).
 *
 * 115×50 · fill #000000@10% · radius 5 · padding 8/20 · gap 15 · arrow 17×34 + "Back" Roboto Flex 20/23.4375
 * #343A40. Hover (variant 670:1815): fill #F8F9FA, glow ellipse #D65F47@40% grows 142→212 · 0.4s ease-in-out.
 * Click = go back in history (assets/js/base.js); the href is the no-JS / no-history fallback.
 *
 * Usage:
 *   get_template_part( 'template-parts/common/back-button', null, array(
 *       'url'   => home_url( '/' ),   // fallback target (default: home)
 *       'label' => __( 'Back', 'sharedheritage' ),
 *       'class' => 'my-extra-class',  // extra classes for positioning by the page
 *   ) );
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_url   = isset( $sh_args['url'] ) && is_string( $sh_args['url'] ) && '' !== $sh_args['url'] ? $sh_args['url'] : home_url( '/' );
$sh_label = isset( $sh_args['label'] ) && is_string( $sh_args['label'] ) && '' !== $sh_args['label'] ? $sh_args['label'] : __( 'Back', 'sharedheritage' );
$sh_class = 'sh-back';
if ( ! empty( $sh_args['class'] ) && is_string( $sh_args['class'] ) ) {
	$sh_class .= ' ' . implode( ' ', array_map( 'sanitize_html_class', preg_split( '/\s+/', trim( $sh_args['class'] ) ) ) );
}
?>
<a class="<?php echo esc_attr( $sh_class ); ?>" href="<?php echo esc_url( $sh_url ); ?>" data-sh-back>
	<?php
	// Static SVG markup from sh_back_arrow_svg() (no user data).
	echo sh_back_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	<span class="sh-back__label"><?php echo esc_html( $sh_label ); ?></span>
</a>
