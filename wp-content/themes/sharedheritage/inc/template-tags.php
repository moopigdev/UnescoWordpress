<?php
/**
 * Small helpers used by header.php and template-parts/common/*.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether Header #1 shows its bottom rule (#AACFD8 1.5px inside).
 *
 * Figma, per page (O-8 · D47 · DESIGN-SPEC §9.1 / P-276): the new header frames (page 3339:* — Home, Zone 1–3, Intro)
 * have no rule; the older frames still used by trail · detail · timeline · Zone 4 chooser · Zone 5 show it.
 * Templates choose with get_header( null, array( 'header_rule' => bool ) ); without the arg the front page and the
 * Zone 1–3 pages have no rule and every other page has it.
 *
 * @param array<string, mixed> $args Args passed to get_header().
 */
function sh_header_has_rule( array $args = array() ): bool {
	$zone_main = is_tax( 'zone' ) && ! is_tax( 'zone', array( 'zone-4', 'zone-5' ) );
	$rule      = array_key_exists( 'header_rule', $args ) ? (bool) $args['header_rule'] : ! ( is_front_page() || $zone_main );

	/**
	 * Filter whether Header #1 shows its bottom rule.
	 *
	 * @param bool                 $rule Show the rule.
	 * @param array<string, mixed> $args Args passed to get_header().
	 */
	return (bool) apply_filters( 'sh_header_rule', $rule, $args );
}

/**
 * Inline SVG of the Back arrow (design/assets/svg/back-arrow.svg, "weui:arrow-filled").
 *
 * The 17×34 box and the vector offset (2.2, 8) come from Button Back B1 (layout/comp-button-back-b1.md);
 * the right-pointing source path is rotated 180° about its own centre (DESIGN-SPEC §3.3).
 * Static markup — no user data.
 */
function sh_back_arrow_svg(): string {
	return '<svg class="sh-back__icon" width="17" height="34" viewBox="-2.2 -8 17 34" aria-hidden="true" focusable="false">'
		. '<path transform="rotate(180 5.216 9.01565)" fill="currentColor" fill-rule="evenodd" '
		. 'd="M10.0172 10.0173L2.00317 18.0313L0 16.0282L7.0125 9.01567L0 2.00317L2.00317 0L10.0172 8.01408C10.2828 8.27975 10.432 8.64002 10.432 9.01567C10.432 9.39132 10.2828 9.75159 10.0172 10.0173Z"/>'
		. '</svg>';
}

/**
 * Header menu links that open in a new tab (Appearance › Menus "Open link in a new tab" — About the Project, D47):
 * rel gets noopener noreferrer and the accessible name says it opens a new tab.
 *
 * @param array<string, string> $atts Link attributes.
 * @param WP_Post               $item Menu item.
 * @param stdClass|object       $args wp_nav_menu() args.
 * @return array<string, string>
 */
function sh_nav_link_attributes( $atts, $item, $args ) {
	if ( ! is_array( $atts ) || empty( $args->theme_location ) || 'primary' !== $args->theme_location || ! isset( $atts['target'] ) || '_blank' !== $atts['target'] ) {
		return $atts;
	}
	$rel                = isset( $atts['rel'] ) ? preg_split( '/\s+/', trim( (string) $atts['rel'] ) ) : array();
	$rel                = array_unique( array_filter( array_merge( is_array( $rel ) ? $rel : array(), array( 'noopener', 'noreferrer' ) ) ) );
	$atts['rel']        = implode( ' ', $rel );
	$title              = isset( $item->title ) ? wp_strip_all_tags( (string) $item->title ) : '';
	$atts['aria-label'] = sprintf(
		/* translators: %s: menu item title. */
		__( '%s (opens in a new tab)', 'sharedheritage' ),
		$title
	);
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'sh_nav_link_attributes', 10, 3 );
