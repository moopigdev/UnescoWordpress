<?php
/**
 * Map controls (DESIGN-SPEC §9.4 C): number buttons 1–4 (tabs) and ‹ › arrows over the map.
 * Without JS the buttons are links to the chapters (all four are shown one after another) and the arrows stay hidden;
 * assets/js/intro.js switches the chapter in place.
 *
 * Args: chapters (sh_intro_chapters() array) · ui (layout map_ui) .
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_chs  = isset( $sh_args['chapters'] ) && is_array( $sh_args['chapters'] ) ? $sh_args['chapters'] : array();
$sh_ui   = isset( $sh_args['ui'] ) && is_array( $sh_args['ui'] ) ? $sh_args['ui'] : array();

$sh_btn_vars = sh_intro_vars(
	array(
		'inactive' => sanitize_hex_color( (string) ( $sh_ui['button']['fill_inactive'] ?? '' ) ) ? (string) $sh_ui['button']['fill_inactive'] : '',
		'active'   => sanitize_hex_color( (string) ( $sh_ui['button']['fill_active'] ?? '' ) ) ? (string) $sh_ui['button']['fill_active'] : '',
	)
);
$sh_chev = '<svg width="28.3" height="56.6" viewBox="0 0 29 57" aria-hidden="true" focusable="false"><path fill="currentColor" fill-rule="evenodd" d="M24.1228 30.1888L10.6874 43.6242L7.32914 40.2659L19.0854 28.5097L7.32914 16.7534L10.6874 13.3952L24.1228 26.8305C24.568 27.2759 24.8181 27.8799 24.8181 28.5097C24.8181 29.1394 24.568 29.7434 24.1228 30.1888Z"/></svg>';
?>
<div class="sh-intro__controls" style="<?php echo esc_attr( $sh_btn_vars ); ?>">
	<div class="sh-intro__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Introduction chapters', 'sharedheritage' ); ?>">
		<?php foreach ( $sh_chs as $sh_ch ) : ?>
			<?php $sh_n = (int) $sh_ch['n']; ?>
			<a class="sh-intro__tab<?php echo 1 === $sh_n ? ' is-active' : ''; ?>" id="sh-intro-tab-<?php echo esc_attr( (string) $sh_n ); ?>" role="tab" href="#chapter-<?php echo esc_attr( (string) $sh_n ); ?>" aria-selected="<?php echo 1 === $sh_n ? 'true' : 'false'; ?>" aria-controls="chapter-<?php echo esc_attr( (string) $sh_n ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: chapter number, 2: chapter title */ __( 'Chapter %1$d: %2$s', 'sharedheritage' ), $sh_n, (string) $sh_ch['title'] ) ); ?>" data-sh-tab="<?php echo esc_attr( (string) $sh_n ); ?>"><?php echo esc_html( (string) $sh_n ); ?></a>
		<?php endforeach; ?>
	</div>
	<button class="sh-intro__arrow sh-intro__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous chapter', 'sharedheritage' ); ?>" data-sh-prev hidden><?php echo $sh_chev; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static svg. ?></button>
	<button class="sh-intro__arrow sh-intro__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next chapter', 'sharedheritage' ); ?>" data-sh-next hidden><?php echo $sh_chev; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static svg. ?></button>
</div>
