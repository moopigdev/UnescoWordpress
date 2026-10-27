<?php
/**
 * Shared Heritage theme bootstrap.
 *
 * Keep this file a loader only. Theme setup and asset loading live in inc/.
 * Content structure (CPT / meta / REST) lives in the sharedheritage-core mu-plugin.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

define( 'SH_THEME_DIR', get_template_directory() );
define( 'SH_THEME_URI', get_template_directory_uri() );

require_once SH_THEME_DIR . '/inc/setup.php';
require_once SH_THEME_DIR . '/inc/enqueue.php';
require_once SH_THEME_DIR . '/inc/fonts.php';
require_once SH_THEME_DIR . '/inc/template-tags.php';
