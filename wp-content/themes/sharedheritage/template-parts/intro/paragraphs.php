<?php
/**
 * Paragraph text from a textarea field: blank line = new <p>, single line break = <br> (Figma text nodes keep them).
 *
 * Args: 'text' (string) · 'class' (string, paragraph class).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_text  = isset( $sh_args['text'] ) ? (string) $sh_args['text'] : '';
$sh_class = isset( $sh_args['class'] ) ? (string) $sh_args['class'] : '';

foreach ( sh_paragraphs( $sh_text ) as $sh_para ) {
	$sh_lines = preg_split( '/\R/u', $sh_para );
	$sh_lines = array_map( static fn( $line ) => esc_html( trim( $line ) ), is_array( $sh_lines ) ? $sh_lines : array() );
	?>
	<p class="<?php echo esc_attr( $sh_class ); ?>"><?php echo implode( '<br>', $sh_lines ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every line passed through esc_html() above. ?></p>
	<?php
}
