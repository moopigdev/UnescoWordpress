<?php
/**
 * Trail page helpers (taxonomy-trail.php only). Loaded once via require_once.
 *
 * The 16 trails added in D49 carry their Figma geometry in the term field sh_trail_layout (DESIGN-SPEC §8.1);
 * the Elephant trail has none and keeps the stylesheet defaults (§4.4).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'sh_trail_box' ) ) {
	/**
	 * [x,y,w,h] from a layout key, or null.
	 *
	 * @param array<string, mixed> $layout sh_trail_layout.
	 * @param string               $key    Key.
	 * @return float[]|null
	 */
	function sh_trail_box( array $layout, string $key ): ?array {
		$v = $layout[ $key ] ?? null;
		if ( ! is_array( $v ) || count( $v ) < 4 ) {
			return null;
		}
		$box = array();
		foreach ( array_slice( array_values( $v ), 0, 4 ) as $n ) {
			if ( ! ( is_int( $n ) || is_float( $n ) ) || ! is_finite( (float) $n ) ) {
				return null;
			}
			$box[] = (float) $n;
		}
		return $box;
	}
}

if ( ! function_exists( 'sh_trail_layout_vars' ) ) {
	/**
	 * CSS custom properties (px) that place the heading / grid / Back of one trail as in Figma at 1440
	 * (trail.css uses them from 1168px up). Heading frame = text height + 20 (padding 10/10), top 677.2, text top 687.2.
	 * Grid y: layout `grid` / `grid_y` when present, else 829.2 (Figma frames 114472 · all trails but Fish) and, for a
	 * description taller than 5 lines (Fish), 40 below the heading frame. Back gap = Back y − last tile row bottom.
	 *
	 * @param array<string, mixed> $layout     sh_trail_layout.
	 * @param int                  $tile_count Tiles in the grid.
	 */
	function sh_trail_layout_vars( array $layout, int $tile_count ): string {
		$desc  = sh_trail_box( $layout, 'description' );
		$title = sh_trail_box( $layout, 'title' );
		$back  = sh_trail_box( $layout, 'back_button' );
		$frame = isset( $layout['frame_size'] ) && is_array( $layout['frame_size'] ) ? array_values( $layout['frame_size'] ) : array();
		if ( ! $desc || ! $title || ! $back ) {
			return '';
		}
		$intro_min = max( $desc[3], $title[3] ) + 20.0;
		$grid_box  = sh_trail_box( $layout, 'grid' );
		if ( $grid_box ) {
			$grid_y = $grid_box[1];
		} elseif ( isset( $layout['grid_y'] ) && is_numeric( $layout['grid_y'] ) ) {
			$grid_y = (float) $layout['grid_y'];
		} else {
			$grid_y = $desc[3] > 115.0 ? 677.2 + $intro_min + 40.0 : 829.2;
		}
		$rows      = max( 1, (int) ceil( $tile_count / 5 ) );
		$last      = $grid_y + $rows * 184.0 + ( $rows - 1 ) * 50.0;
		$back_pad  = isset( $frame[1] ) && is_numeric( $frame[1] ) ? max( 0.0, (float) $frame[1] - ( $back[1] + $back[3] ) ) : 46.0;
		$out       = array(
			'--sh-trail-intro-min' => $intro_min,
			'--sh-trail-grid-gap'  => max( 0.0, $grid_y - ( 677.2 + $intro_min ) ),
			'--sh-trail-back-gap'  => max( 0.0, $back[1] - $last ),
			'--sh-trail-back-pad'  => $back_pad,
		);
		$css       = '';
		foreach ( $out as $name => $px ) {
			$css .= $name . ':' . round( $px, 3 ) . 'px;';
		}
		return $css;
	}
}

if ( ! function_exists( 'sh_trail_title_parts' ) ) {
	/**
	 * H1 lines. The field holds one line; when the layout says the Figma H1 is two lines tall (Seashell &
	 * Marine Invertebrates: "Seashell &⏎Marine Invertebrates Trail") the break goes after the "&".
	 *
	 * @param string               $title  H1 text.
	 * @param array<string, mixed> $layout sh_trail_layout.
	 * @return string[]
	 */
	function sh_trail_title_parts( string $title, array $layout ): array {
		$box = sh_trail_box( $layout, 'title' );
		if ( $box && $box[3] >= 80.0 && preg_match( '/^(.*?&)\s+(\S.*)$/u', $title, $m ) ) {
			return array( $m[1], $m[2] );
		}
		return array( $title );
	}
}
