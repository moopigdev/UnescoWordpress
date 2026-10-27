<?php
/**
 * Home page helpers (front-page.php only). Loaded once via require_once.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'sh_home_img' ) ) {
	/**
	 * <img> markup for an attachment id ('' when the id is empty / not an image → layout must not break, DOD B6).
	 * Every attribute is escaped here; pass the result through sh_home_kses() when echoing.
	 *
	 * @param int                  $id   Attachment id (0 = none).
	 * @param array<string, mixed> $opts alt_fallback · class · sizes · eager (bool) · priority (bool).
	 */
	function sh_home_img( int $id, array $opts = array() ): string {
		if ( $id <= 0 ) {
			return '';
		}
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( ! is_array( $src ) || empty( $src[0] ) ) {
			return '';
		}

		$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		if ( '' === $alt && isset( $opts['alt_fallback'] ) ) {
			$alt = (string) $opts['alt_fallback'];
		}

		$html = '<img src="' . esc_url( $src[0] ) . '"';

		$srcset = wp_get_attachment_image_srcset( $id, 'full' );
		if ( is_string( $srcset ) && '' !== $srcset ) {
			$html .= ' srcset="' . esc_attr( $srcset ) . '"';
			if ( ! empty( $opts['sizes'] ) ) {
				$html .= ' sizes="' . esc_attr( (string) $opts['sizes'] ) . '"';
			}
		}
		if ( ! empty( $src[1] ) && ! empty( $src[2] ) ) {
			$html .= ' width="' . (int) $src[1] . '" height="' . (int) $src[2] . '"';
		}
		$html .= ' alt="' . esc_attr( $alt ) . '"';
		if ( ! empty( $opts['class'] ) ) {
			$html .= ' class="' . esc_attr( (string) $opts['class'] ) . '"';
		}
		$html .= empty( $opts['eager'] ) ? ' loading="lazy"' : ' loading="eager"';
		if ( ! empty( $opts['priority'] ) ) {
			$html .= ' fetchpriority="high"';
		}
		return $html . ' decoding="async">';
	}
}

if ( ! function_exists( 'sh_home_kses' ) ) {
	/**
	 * Echo-safe filter for sh_home_img() output.
	 *
	 * @param string $html Markup from sh_home_img().
	 */
	function sh_home_kses( string $html ): string {
		return wp_kses(
			$html,
			array(
				'img' => array(
					'src'           => true,
					'srcset'        => true,
					'sizes'         => true,
					'width'         => true,
					'height'        => true,
					'alt'           => true,
					'class'         => true,
					'loading'       => true,
					'decoding'      => true,
					'fetchpriority' => true,
				),
			)
		);
	}
}

if ( ! function_exists( 'sh_home_lines' ) ) {
	/**
	 * Text with single line breaks kept (seed uses "\n" inside a paragraph) → escaped HTML with <br>.
	 *
	 * @param string $text Raw textarea value.
	 */
	function sh_home_lines( string $text ): string {
		$lines = preg_split( '/\R/u', $text );
		$lines = array_map( 'trim', is_array( $lines ) ? $lines : array() );
		return implode( '<br>', array_map( 'esc_html', array_filter( $lines, 'strlen' ) ) );
	}
}
