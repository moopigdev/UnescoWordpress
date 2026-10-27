<?php
/**
 * Theme setup: textdomain, theme supports, menu locations.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function sh_setup(): void {
	load_theme_textdomain( 'sharedheritage', SH_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	// Header #02 menu (DESIGN-SPEC §3.2). Items + link targets are set in Appearance > Menus (P-211).
	// No footer location: the Figma design has no footer (§4.1 / P-211). The Header #1 utility menu
	// (Visit … Contact) is hidden in every Figma instance, so it is not registered (O-8 / DEFERRED).
	register_nav_menus(
		array(
			'primary' => __( 'Main menu (header)', 'sharedheritage' ),
		)
	);
}
add_action( 'after_setup_theme', 'sh_setup' );
