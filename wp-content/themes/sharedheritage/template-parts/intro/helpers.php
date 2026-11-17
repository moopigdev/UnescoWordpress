<?php
/**
 * Introduction page helpers (page-intro.php only). Loaded once via require_once.
 * Values come from the layout JSON (sh_intro_head() / sh_intro_chapters()) and are sanitised before they reach a style attribute.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'sh_intro_num' ) ) {
	/**
	 * Number from layout data as a CSS-safe string (no unit) · '' when missing.
	 *
	 * @param mixed $value Raw value.
	 */
	function sh_intro_num( $value ): string {
		return is_numeric( $value ) ? (string) round( (float) $value, 4 ) : '';
	}
}

if ( ! function_exists( 'sh_intro_vars' ) ) {
	/**
	 * Inline custom properties ("--x:1;--y:2;") from name => value; empty values are left out.
	 *
	 * @param array<string, string> $vars Property (without --) => already sanitised value.
	 */
	function sh_intro_vars( array $vars ): string {
		$css = '';
		foreach ( $vars as $name => $value ) {
			if ( '' !== $value && preg_match( '/^[a-z0-9-]+$/', (string) $name ) ) {
				$css .= '--' . $name . ':' . $value . ';';
			}
		}
		return $css;
	}
}

if ( ! function_exists( 'sh_intro_font_vars' ) ) {
	/**
	 * Layout "font" object ({size, weight_figma, line_height, align, color}) → custom properties.
	 * Komet weights follow the PostScript names (tokens.css): Figma 400→300 · 500→400 · 600→500 · 700→700 · 800→800.
	 *
	 * @param mixed $font Layout font object.
	 * @return array<string, string>
	 */
	function sh_intro_font_vars( $font ): array {
		$font = is_array( $font ) ? $font : array();
		$map  = array(
			400 => 'var(--sh-fw-komet-light)',
			500 => 'var(--sh-fw-komet-regular)',
			600 => 'var(--sh-fw-komet-medium)',
			700 => 'var(--sh-fw-komet-bold)',
			800 => 'var(--sh-fw-komet-heavy)',
		);
		$ta   = array(
			'left'      => 'left',
			'center'    => 'center',
			'justified' => 'justify',
		);
		$size = sh_intro_num( $font['size'] ?? null );
		$lh   = sh_intro_num( $font['line_height'] ?? null );
		$wf   = isset( $font['weight_figma'] ) ? (int) $font['weight_figma'] : 0;
		$al   = isset( $font['align'] ) ? (string) $font['align'] : '';
		$col  = isset( $font['color'] ) ? sanitize_hex_color( (string) $font['color'] ) : '';
		return array(
			'fs' => '' !== $size ? $size . 'px' : '',
			'lh' => '' !== $lh ? $lh . 'px' : '',
			'fw' => $map[ $wf ] ?? '',
			'ta' => $ta[ $al ] ?? '',
			'c'  => is_string( $col ) ? $col : '',
		);
	}
}

if ( ! function_exists( 'sh_intro_img' ) ) {
	/**
	 * <img> of an attachment. $anim = animated WebP: src stays the still (no animation until the chapter is shown ·
	 * assets/js/intro.js swaps in data-sh-anim); without a still the animation is the src. '' when nothing to show (DOD B6).
	 *
	 * @param int                   $id   Still image attachment (0 = none).
	 * @param int                   $anim Animated image attachment (0 = none).
	 * @param array<string, string> $opts class · style · sizes.
	 */
	function sh_intro_img( int $id, int $anim = 0, array $opts = array() ): string {
		$main = $id > 0 ? $id : $anim;
		if ( $main <= 0 ) {
			return '';
		}
		$attrs = array(
			'class'    => isset( $opts['class'] ) ? $opts['class'] : '',
			'decoding' => 'async',
			'loading'  => 'lazy',
		);
		if ( ! empty( $opts['sizes'] ) ) {
			$attrs['sizes'] = $opts['sizes'];
		}
		if ( ! empty( $opts['style'] ) ) {
			$attrs['style'] = $opts['style'];
		}
		if ( $id > 0 && $anim > 0 ) {
			$url = wp_get_attachment_url( $anim );
			if ( is_string( $url ) && '' !== $url ) {
				$attrs['data-sh-anim'] = $url;
			}
		}
		// wp_get_attachment_image() escapes every attribute; '' for a missing attachment.
		return (string) wp_get_attachment_image( $main, 'full', false, $attrs );
	}
}

if ( ! function_exists( 'sh_intro_crop_style' ) ) {
	/**
	 * Inline style placing an <img> so the Figma crop {x,y,w,h} (fractions of the source) fills its box
	 * (STRETCH · values < 0 or > 1 = picture smaller than the box). FILL/FIT have no crop → CSS class decides.
	 *
	 * @param mixed $crop Layout crop object.
	 */
	function sh_intro_crop_style( $crop ): string {
		if ( ! is_array( $crop ) ) {
			return '';
		}
		$x = (float) ( $crop['x'] ?? 0 );
		$y = (float) ( $crop['y'] ?? 0 );
		$w = (float) ( $crop['w'] ?? 1 );
		$h = (float) ( $crop['h'] ?? 1 );
		if ( $w <= 0 || $h <= 0 ) {
			return '';
		}
		return sprintf(
			'width:%s%%;height:%s%%;left:%s%%;top:%s%%;',
			round( 100 / $w, 4 ),
			round( 100 / $h, 4 ),
			round( -100 * $x / $w, 4 ),
			round( -100 * $y / $h, 4 )
		);
	}
}
