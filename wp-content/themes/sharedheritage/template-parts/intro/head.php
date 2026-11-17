<?php
/**
 * Orange header of the Introduction page (DESIGN-SPEC §9.4 B · Figma 3339:6122) — same for all four chapters:
 * emblem · title · four paragraphs · credit · "continue down" (scrolls to the map · P-273 default).
 * The three crop pictures of the mock-up (section markers · scroll indicator) are not shown (P-273 · owner-side decision).
 *
 * Args: head (sh_intro_head() array) · target (id of the element "continue down" scrolls to).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args   = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_head   = isset( $sh_args['head'] ) && is_array( $sh_args['head'] ) ? $sh_args['head'] : array();
$sh_target = isset( $sh_args['target'] ) ? sanitize_html_class( (string) $sh_args['target'] ) : '';
$sh_lay    = isset( $sh_head['layout']['header'] ) && is_array( $sh_head['layout']['header'] ) ? $sh_head['layout']['header'] : array();

$sh_bg    = isset( $sh_lay['bg'] ) ? sanitize_hex_color( (string) $sh_lay['bg'] ) : '';
$sh_emb   = isset( $sh_lay['emblem'] ) && is_array( $sh_lay['emblem'] ) ? $sh_lay['emblem'] : array();
$sh_style = sh_intro_vars( array( 'bg' => is_string( $sh_bg ) ? $sh_bg : '' ) );

$sh_title_vars = sh_intro_vars( sh_intro_font_vars( $sh_lay['title']['font'] ?? array() ) );
$sh_para_vars  = sh_intro_vars( sh_intro_font_vars( $sh_lay['paragraphs'][0]['font'] ?? array() ) );
$sh_cred_vars  = sh_intro_vars( sh_intro_font_vars( $sh_lay['credit']['font'] ?? array() ) );
$sh_emblem     = sh_intro_img(
	(int) ( $sh_head['emblem'] ?? 0 ),
	0,
	array(
		'class' => 'sh-intro__crop',
		'style' => sh_intro_crop_style( $sh_emb['crop'] ?? null ),
		'sizes' => '326px',
	)
);
?>
<section class="sh-intro__head" style="<?php echo esc_attr( $sh_style ); ?>" aria-labelledby="sh-intro-title">
	<div class="sh-intro__head-in">
		<div class="sh-intro__head-top">
			<?php if ( '' !== $sh_emblem ) : ?>
				<div class="sh-intro__emblem"><?php echo $sh_emblem; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes. ?></div>
			<?php endif; ?>
			<h1 id="sh-intro-title" class="sh-intro__head-title" style="<?php echo esc_attr( $sh_title_vars ); ?>"><?php echo wp_kses( nl2br( esc_html( (string) ( $sh_head['title'] ?? '' ) ), false ), array( 'br' => array() ) ); ?></h1>
		</div>
		<div class="sh-intro__head-text">
			<?php foreach ( (array) ( $sh_head['paragraphs'] ?? array() ) as $sh_p ) : ?>
				<p class="sh-intro__head-p" style="<?php echo esc_attr( $sh_para_vars ); ?>"><?php echo esc_html( (string) $sh_p ); ?></p>
			<?php endforeach; ?>
			<?php if ( '' !== trim( (string) ( $sh_head['credit'] ?? '' ) ) ) : ?>
				<p class="sh-intro__head-credit" style="<?php echo esc_attr( $sh_cred_vars ); ?>"><?php echo esc_html( (string) $sh_head['credit'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( '' !== $sh_target ) : ?>
		<a class="sh-intro__down" href="#<?php echo esc_attr( $sh_target ); ?>" data-sh-intro-down>
			<span class="screen-reader-text"><?php esc_html_e( 'Continue down to the map', 'sharedheritage' ); ?></span>
			<svg width="30" height="17" viewBox="0 0 30 17" aria-hidden="true" focusable="false"><path d="M2 2l13 13L28 2" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</a>
	<?php endif; ?>
</section>
