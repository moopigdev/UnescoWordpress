<?php
/**
 * Komet @font-face (D6 / P-205).
 *
 * Komet is a paid font; the owner delivers the web-licensed files later. Drop them into
 * assets/fonts/komet/ as Komet-<Style>.woff2 (and/or .woff) — see assets/fonts/komet/README.txt.
 * A rule is printed only for a style whose file exists, so a missing file never causes a 404;
 * until then the --sh-font-komet stack in tokens.css falls back to system fonts.
 *
 * Barlow Condensed / Roboto Flex are static @font-face rules in tokens.css (files are committed).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

/**
 * Komet styles: file stem => CSS font-weight.
 * Weights follow the PostScript name (DESIGN-SPEC §2.2), matching --sh-fw-komet-* in tokens.css.
 *
 * @return array<string, int>
 */
function sh_komet_styles(): array {
	return array(
		'Komet-Light'   => 300,
		'Komet-Regular' => 400,
		'Komet-Medium'  => 500,
		'Komet-Bold'    => 700,
		'Komet-Heavy'   => 800,
	);
}

/**
 * Build @font-face CSS for the Komet files that exist on disk ('' when none).
 */
function sh_komet_font_face_css(): string {
	$dir = SH_THEME_DIR . '/assets/fonts/komet/';
	$uri = SH_THEME_URI . '/assets/fonts/komet/';
	$css = '';

	foreach ( sh_komet_styles() as $stem => $weight ) {
		$src = array();
		foreach ( array( 'woff2', 'woff' ) as $ext ) {
			$file = $stem . '.' . $ext;
			if ( is_file( $dir . $file ) ) {
				$url   = add_query_arg( 'ver', (string) filemtime( $dir . $file ), $uri . $file );
				// CSS context: esc_url_raw (no &#038; entity encoding) — path is built from fixed names only.
				$src[] = 'url("' . esc_url_raw( $url ) . '") format("' . $ext . '")';
			}
		}
		if ( empty( $src ) ) {
			continue;
		}
		$css .= '@font-face{font-family:"Komet";font-style:normal;font-weight:' . (int) $weight
			. ';font-display:swap;src:' . implode( ',', $src ) . ';}';
	}

	return $css;
}

/**
 * Attach the Komet rules to the tokens stylesheet (runs after inc/enqueue.php registered it).
 */
function sh_enqueue_komet(): void {
	$css = sh_komet_font_face_css();
	if ( '' !== $css && wp_style_is( 'sh-tokens', 'enqueued' ) ) {
		wp_add_inline_style( 'sh-tokens', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'sh_enqueue_komet', 20 );
