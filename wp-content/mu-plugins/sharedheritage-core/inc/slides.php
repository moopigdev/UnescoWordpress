<?php
/**
 * Zone 4 slides of the 16 other animals (D62 · task zone4slides-1118): URL `/timeline/<trail-slug>/` and the lookups the
 * theme needs to link to it. The elephant stays on the Timeline page itself (`/timeline/` · page meta `sh_timeline_trail`).
 *
 * No rewrite rule (nothing to flush on a live site): the `request` filter turns the path `timeline/<trail-slug>` into the Timeline
 * page + query var `sh_slide=<slug>`; the template
 * (page-timeline.php) shows that trail's cards. Data = `timeline_item` posts with `sh_trail` = the trail (CONTENT-MODEL §3).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trail term ids that have at least one published timeline card (one query per request).
 *
 * @return int[]
 */
function sh_timeline_trail_ids(): array {
	static $ids = null;
	if ( null === $ids ) {
		global $wpdb;
		// No user input in this query (fixed meta key / post type / status).
		$rows = $wpdb->get_col( "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = 'sh_trail' AND p.post_type = 'timeline_item' AND p.post_status = 'publish'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$ids  = array_values( array_filter( array_map( 'absint', is_array( $rows ) ? $rows : array() ) ) );
	}
	return $ids;
}

/**
 * Term id of the trail the Timeline page itself shows (the elephant · page meta `sh_timeline_trail`) · 0 = none.
 */
function sh_timeline_page_trail_id(): int {
	$page = sh_page_id( 'timeline' );
	return $page ? absint( get_post_meta( $page, 'sh_timeline_trail', true ) ) : 0;
}

/**
 * URL of the timeline (slide) page of a trail: the Timeline page for the trail it shows (the elephant), `/timeline/<slug>/`
 * for another trail with published cards, '' when the trail has no timeline.
 *
 * @param WP_Term|int $trail Trail term or id.
 */
function sh_timeline_url( $trail ): string {
	$term = $trail instanceof WP_Term ? $trail : get_term( (int) $trail, 'trail' );
	$page = sh_page_id( 'timeline' );
	if ( ! $term instanceof WP_Term || ! $page || 'publish' !== get_post_status( $page ) ) {
		return '';
	}
	$url = get_permalink( $page );
	if ( ! is_string( $url ) ) {
		return '';
	}
	if ( (int) $term->term_id === sh_timeline_page_trail_id() ) {
		return $url;
	}
	return in_array( (int) $term->term_id, sh_timeline_trail_ids(), true ) ? trailingslashit( $url ) . $term->slug . '/' : '';
}

/**
 * `timeline/<trail-slug>` → the Timeline page with `sh_slide=<slug>`. WordPress reads that path as an attachment (rule
 * `[^/]+/([^/]+)/?$`, before the page rule) or as a missing child page, so the request path decides, not the matched rule.
 * Only a path whose last part is a trail slug is taken over (any other path is left to WordPress: a real 404 / child page).
 *
 * @param array<string, mixed> $vars Query vars of the request.
 * @return array<string, mixed>
 */
function sh_slides_request( array $vars ): array {
	global $wp;
	$path = isset( $wp->request ) ? (string) $wp->request : '';
	if ( 1 !== preg_match( '#^timeline/([a-z0-9-]+)$#', $path, $m ) || ! get_term_by( 'slug', $m[1], 'trail' ) instanceof WP_Term ) {
		return $vars;
	}
	if ( get_page_by_path( $path, OBJECT, 'page' ) instanceof WP_Post ) {
		return $vars; // A real child page wins.
	}
	return array(
		'pagename' => 'timeline',
		'sh_slide' => $m[1],
	);
}
add_filter( 'request', 'sh_slides_request' );

/**
 * The trail of a `/timeline/<slug>/` request: term when valid and it has slides, else null (the elephant = null too).
 */
function sh_slide_request_trail(): ?WP_Term {
	$slug = (string) get_query_var( 'sh_slide' );
	if ( '' === $slug ) {
		return null;
	}
	$term = get_term_by( 'slug', $slug, 'trail' );
	return ( $term instanceof WP_Term && (int) $term->term_id !== sh_timeline_page_trail_id() && in_array( (int) $term->term_id, sh_timeline_trail_ids(), true ) ) ? $term : null;
}

/**
 * `/timeline/elephant/` (the Timeline page's own trail) → 301 `/timeline/` · a trail without slides → its trail page.
 * (A slug that is no trail never gets here — sh_slides_request() leaves it to WordPress, i.e. 404.)
 */
function sh_slides_route(): void {
	$slug = (string) get_query_var( 'sh_slide' );
	if ( '' === $slug || ! is_page() ) {
		return;
	}
	$term = get_term_by( 'slug', $slug, 'trail' );
	if ( ! $term instanceof WP_Term ) {
		return;
	}
	if ( (int) $term->term_id === sh_timeline_page_trail_id() ) {
		wp_safe_redirect( (string) get_permalink( sh_page_id( 'timeline' ) ), 301 );
		exit;
	}
	if ( null === sh_slide_request_trail() ) {
		$link = get_term_link( $term );
		if ( is_string( $link ) ) {
			wp_safe_redirect( $link, 302 ); // A real trail without a slide set: its trail page.
			exit;
		}
	}
}
add_action( 'template_redirect', 'sh_slides_route', 5 );

/**
 * WordPress' canonical redirect would send `/timeline/<slug>/` to `/timeline/` (the queried page's permalink). For a slide URL
 * only the missing trailing slash is fixed instead (`/timeline/bird` → 301 `/timeline/bird/` · query string kept), nothing else.
 *
 * @param string|false $redirect  Target URL.
 * @param string       $requested Requested URL.
 * @return string|false
 */
function sh_slides_no_canonical( $redirect, $requested = '' ) {
	if ( '' === (string) get_query_var( 'sh_slide' ) ) {
		return $redirect;
	}
	$term  = sh_slide_request_trail();
	$parts = wp_parse_url( (string) $requested );
	$path  = is_array( $parts ) && isset( $parts['path'] ) ? (string) $parts['path'] : '';
	$url   = $term instanceof WP_Term ? sh_timeline_url( $term ) : '';
	if ( '' === $url || '' === $path || '/' === substr( $path, -1 ) ) {
		return false;
	}
	return esc_url_raw( $url . ( ! empty( $parts['query'] ) ? '?' . $parts['query'] : '' ) );
}
add_filter( 'redirect_canonical', 'sh_slides_no_canonical', 10, 2 );

/**
 * `<link rel="canonical">` of a slide page = its own URL (not the Timeline page's).
 *
 * @param string|false $canonical Canonical URL.
 * @return string|false
 */
function sh_slides_canonical_url( $canonical ) {
	$term = sh_slide_request_trail();
	$url  = $term instanceof WP_Term ? sh_timeline_url( $term ) : '';
	return '' !== $url ? $url : $canonical;
}
add_filter( 'get_canonical_url', 'sh_slides_canonical_url' );

/**
 * `<title>` of a slide page starts with the trail name (admin-editable term name) instead of the Timeline page title.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function sh_slides_document_title( array $parts ): array {
	$term = sh_slide_request_trail();
	if ( $term instanceof WP_Term && '' !== $term->name ) {
		$parts['title'] = $term->name;
	}
	return $parts;
}
add_filter( 'document_title_parts', 'sh_slides_document_title' );
