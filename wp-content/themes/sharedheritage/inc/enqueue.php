<?php
/**
 * Asset loading.
 *
 * Every page: assets/css/tokens.css -> assets/css/base.css (and assets/js/base.js if present).
 * Per page (auto, no functions.php edit needed): the template WordPress picked is resolved to
 * a page slug (see sh_template_slug_map()); if assets/css/<slug>.css or assets/js/<slug>.js
 * exists it is enqueued. Version = filemtime. Local files only, no CDN.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template file (basename without .php) -> page asset slug.
 * Route table (orchestrator PLAN, fixed): Home · Intro · Zone 1–3 · Trail · Timeline · Detail.
 * Templates not listed here use their own basename as slug (e.g. index -> index.css).
 *
 * @return array<string, string>
 */
function sh_template_slug_map(): array {
	$map = array(
		'front-page'     => 'home',
		'page-intro'     => 'intro',
		'taxonomy-zone'  => 'zone',
		'taxonomy-trail' => 'trail',
		'page-timeline'  => 'timeline',
		'single-object'  => 'detail',
	);

	/**
	 * Filter the template -> asset slug map.
	 *
	 * @param array<string, string> $map Template basename => asset slug.
	 */
	return (array) apply_filters( 'sh_template_slug_map', $map );
}

/**
 * Remember which template file WordPress is about to load.
 * Runs last on template_include so it sees the final choice; returns the path unchanged.
 *
 * @param string $template Absolute path of the chosen template.
 * @return string
 */
function sh_capture_template( $template ) {
	if ( is_string( $template ) && '' !== $template ) {
		$GLOBALS['sh_current_template'] = basename( $template, '.php' );
	}
	return $template;
}
add_filter( 'template_include', 'sh_capture_template', PHP_INT_MAX );

/**
 * Asset slug of the current request, or '' when unknown.
 */
function sh_current_asset_slug(): string {
	$template = isset( $GLOBALS['sh_current_template'] ) ? (string) $GLOBALS['sh_current_template'] : '';
	if ( '' === $template ) {
		return '';
	}

	$map  = sh_template_slug_map();
	$slug = isset( $map[ $template ] ) ? (string) $map[ $template ] : $template;

	// Slug becomes part of a file path: allow only lowercase letters, digits and hyphens.
	return preg_match( '/^[a-z0-9-]+$/', $slug ) ? $slug : '';
}

/**
 * Register a theme asset if the file exists. Returns true when enqueued.
 *
 * @param string   $handle Script/style handle.
 * @param string   $rel    Path relative to the theme root.
 * @param string[] $deps   Dependencies.
 */
function sh_enqueue_asset( string $handle, string $rel, array $deps = array() ): bool {
	$path = SH_THEME_DIR . '/' . $rel;
	if ( ! is_file( $path ) ) {
		return false;
	}

	$ver = (string) filemtime( $path );
	$uri = SH_THEME_URI . '/' . $rel;

	if ( str_ends_with( $rel, '.css' ) ) {
		wp_enqueue_style( $handle, $uri, $deps, $ver );
	} else {
		wp_enqueue_script(
			$handle,
			$uri,
			$deps,
			$ver,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
	return true;
}

/**
 * Enqueue global and per-page assets.
 */
function sh_enqueue_assets(): void {
	sh_enqueue_asset( 'sh-tokens', 'assets/css/tokens.css' );
	$css_deps = sh_enqueue_asset( 'sh-base', 'assets/css/base.css', array( 'sh-tokens' ) ) ? array( 'sh-base' ) : array( 'sh-tokens' );
	$js_deps  = sh_enqueue_asset( 'sh-base', 'assets/js/base.js' ) ? array( 'sh-base' ) : array();

	$slug = sh_current_asset_slug();
	if ( '' === $slug || 'tokens' === $slug || 'base' === $slug ) {
		return;
	}

	sh_enqueue_asset( 'sh-page-' . $slug, 'assets/css/' . $slug . '.css', $css_deps );
	sh_enqueue_asset( 'sh-page-' . $slug, 'assets/js/' . $slug . '.js', $js_deps );
}
add_action( 'wp_enqueue_scripts', 'sh_enqueue_assets' );
