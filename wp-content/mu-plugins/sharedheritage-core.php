<?php
/**
 * Plugin Name: Shared Heritage — core (content model)
 * Description: CPT / taxonomies / fields of the Shared Heritage site. Contract: docs/CONTENT-MODEL.md (1:1). Fields are edited with ACF (free) and registered as WP meta so they work, and show in REST, without ACF too.
 * Version:     1.0.0
 * Author:      Shared Heritage
 * Text Domain: sharedheritage
 *
 * mu-plugins only auto-load top-level files: this file loads sharedheritage-core/.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

define( 'SH_CORE_VERSION', '1.0.0' );
define( 'SH_CORE_DIR', __DIR__ . '/sharedheritage-core' );

require_once SH_CORE_DIR . '/inc/post-types.php';
require_once SH_CORE_DIR . '/inc/fields.php';
require_once SH_CORE_DIR . '/inc/register.php';
require_once SH_CORE_DIR . '/inc/settings.php';
require_once SH_CORE_DIR . '/inc/api.php';
