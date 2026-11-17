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

if ( ! function_exists( 'sh_intro_group_rows' ) ) {
	/**
	 * Figma geometry of one section → flow structure (so longer admin text pushes the rest down instead of overlapping).
	 * Blocks that overlap vertically form a row; blocks of a row that overlap horizontally form a column (stacked).
	 * Every distance is the Figma gap (design coordinates), so with the original texts the result equals the frame.
	 * A block that starts above its section (Figma overhang, e.g. the boat) is returned in 'over' (absolute, picture only).
	 *
	 * @param array<int, array<string, mixed>> $items  Blocks: x · y · w · h (design px, y in frame) + render data.
	 * @param float                            $sec_y  Section top (frame y).
	 * @param float                            $sec_h  Section height.
	 * @return array{over:array<int,array<string,mixed>>,pt:float,pb:float,rows:array<int,array<string,mixed>>}
	 */
	function sh_intro_group_rows( array $items, float $sec_y, float $sec_h ): array {
		$over = array();
		$flow = array();
		foreach ( $items as $it ) {
			if ( $it['y'] < $sec_y - 0.5 ) {
				$over[] = $it;
			} else {
				$flow[] = $it;
			}
		}
		usort(
			$flow,
			static function ( $a, $b ) {
				return array( $a['y'], $a['x'] ) <=> array( $b['y'], $b['x'] );
			}
		);

		$raw = array();
		foreach ( $flow as $it ) {
			$last = count( $raw ) - 1;
			if ( $last >= 0 && $it['y'] < $raw[ $last ]['bottom'] - 0.5 ) {
				$raw[ $last ]['items'][] = $it;
				$raw[ $last ]['bottom']  = max( $raw[ $last ]['bottom'], $it['y'] + $it['h'] );
			} else {
				$raw[] = array(
					'top'    => $it['y'],
					'bottom' => $it['y'] + $it['h'],
					'items'  => array( $it ),
				);
			}
		}

		$rows       = array();
		$prev_bot   = $sec_y;
		$pt         = 0.0;
		foreach ( $raw as $i => $row ) {
			$its = $row['items'];
			usort(
				$its,
				static function ( $a, $b ) {
					return array( $a['x'], $a['y'] ) <=> array( $b['x'], $b['y'] );
				}
			);
			$cols = array();
			foreach ( $its as $it ) {
				$placed = false;
				foreach ( $cols as $ci => $col ) {
					if ( $it['x'] < $col['right'] - 0.5 && $it['x'] + $it['w'] > $col['x'] + 0.5 ) {
						$cols[ $ci ]['items'][] = $it;
						$cols[ $ci ]['x']       = min( $col['x'], $it['x'] );
						$cols[ $ci ]['right']   = max( $col['right'], $it['x'] + $it['w'] );
						$placed                 = true;
						break;
					}
				}
				if ( ! $placed ) {
					$cols[] = array(
						'x'     => $it['x'],
						'right' => $it['x'] + $it['w'],
						'items' => array( $it ),
					);
				}
			}

			$out_cols  = array();
			$prev_right = 0.0;
			foreach ( $cols as $col ) {
				$col_items = $col['items'];
				usort(
					$col_items,
					static function ( $a, $b ) {
						return $a['y'] <=> $b['y'];
					}
				);
				$blocks = array();
				$bottom = $row['top'];
				foreach ( $col_items as $k => $it ) {
					$blocks[] = array(
						'mt'   => 0 === $k ? max( 0.0, $it['y'] - $row['top'] ) : max( 0.0, $it['y'] - $bottom ),
						'ox'   => $it['x'] - $col['x'],
						'item' => $it,
					);
					$bottom = $it['y'] + $it['h'];
				}
				$out_cols[] = array(
					'ml'     => $col['x'] - $prev_right,
					'w'      => $col['right'] - $col['x'],
					'blocks' => $blocks,
				);
				$prev_right = $col['right'];
			}

			if ( 0 === $i ) {
				$pt = max( 0.0, $row['top'] - $sec_y );
			}
			$rows[]   = array(
				'mt'   => 0 === $i ? 0.0 : max( 0.0, $row['top'] - $prev_bot ),
				'cols' => $out_cols,
			);
			$prev_bot = $row['bottom'];
		}

		return array(
			'over' => $over,
			'pt'   => $pt,
			'pb'   => max( 0.0, $sec_y + $sec_h - $prev_bot ),
			'rows' => $rows,
		);
	}
}

if ( ! function_exists( 'sh_intro_block_markup' ) ) {
	/**
	 * Markup of one text/picture block (safe: texts are sh_rich_paragraphs() HTML, <img> from wp_get_attachment_image()).
	 *
	 * @param array<string, mixed>  $it    Block from sh_intro_group_rows() ('kind' text|image · 'heading' · 'paras' · 'img' · 'fit' · 'font').
	 * @param array<string, string> $extra Extra custom properties (mt · ox · y · x).
	 */
	function sh_intro_block_markup( array $it, array $extra = array() ): string {
		$vars = $extra + array(
			'bw' => sh_intro_num( $it['w'] ),
			'bh' => sh_intro_num( $it['h'] ),
		);
		if ( 'text' === $it['kind'] ) {
			$style = esc_attr( sh_intro_vars( $vars + (array) $it['font'] ) );
			if ( ! empty( $it['heading'] ) ) {
				return '<h3 class="sh-intro__block sh-intro__h" style="' . $style . '">' . wp_kses_post( implode( ' ', (array) $it['paras'] ) ) . '</h3>';
			}
			$html = '';
			foreach ( (array) $it['paras'] as $para ) {
				$html .= '<p>' . wp_kses_post( (string) $para ) . '</p>';
			}
			return '<div class="sh-intro__block sh-intro__p" style="' . $style . '">' . $html . '</div>';
		}
		return '<div class="sh-intro__block sh-intro__fig' . ( ! empty( $it['fit'] ) ? ' sh-intro__fig--fit' : '' ) . '" style="' . esc_attr( sh_intro_vars( $vars ) ) . '">' . $it['img'] . '</div>'; // $it['img'] = wp_get_attachment_image() output (escaped).
	}
}
