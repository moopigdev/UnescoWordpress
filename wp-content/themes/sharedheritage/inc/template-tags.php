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
 * Figma (O-8): every page has the rule except Home (670:9386) and Intro (699:11020).
 * Templates choose with get_header( null, array( 'header_rule' => false ) ); without the arg the
 * front page has no rule and every other page has it.
 *
 * @param array<string, mixed> $args Args passed to get_header().
 */
function sh_header_has_rule( array $args = array() ): bool {
	$rule = array_key_exists( 'header_rule', $args ) ? (bool) $args['header_rule'] : ! is_front_page();

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
