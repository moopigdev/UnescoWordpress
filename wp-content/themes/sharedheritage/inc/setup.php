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

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'sharedheritage' ),
			'footer'  => __( 'Footer navigation', 'sharedheritage' ),
		)
	);
}
add_action( 'after_setup_theme', 'sh_setup' );
