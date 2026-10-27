<?php
/**
 * Read API for templates (docs/CONTENT-MODEL.md §API). Works with or without ACF (reads meta directly).
 * Returns raw values — templates escape on output.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post meta value (single). Default post = current post.
 *
 * @param string $key     Meta key (sh_…).
 * @param int    $post_id Post id (0 = current).
 * @return mixed '' when missing.
 */
function sh_get( string $key, int $post_id = 0 ) {
	$post_id = $post_id ? $post_id : (int) get_the_ID();
	return $post_id ? get_post_meta( $post_id, $key, true ) : '';
}

/**
 * Term meta value (single).
 *
 * @param string      $key  Meta key.
 * @param int|WP_Term $term Term or term id.
 * @return mixed '' when missing.
 */
function sh_get_term( string $key, $term ) {
	$term_id = $term instanceof WP_Term ? $term->term_id : (int) $term;
	return $term_id ? get_term_meta( $term_id, $key, true ) : '';
}

/**
 * Site option (group "site"), with the documented default.
 *
 * @param string $key Option name (sh_label_read_more …).
 * @return mixed
 */
function sh_get_option( string $key ) {
	return get_option( $key );
}

/**
 * Page id by route role: front (Home) · intro · timeline. 0 when missing.
 *
 * @param string $role Role.
 */
function sh_page_id( string $role ): int {
	if ( 'front' === $role ) {
		return 'page' === get_option( 'show_on_front' ) ? (int) get_option( 'page_on_front' ) : 0;
	}
	$page = get_page_by_path( sanitize_title( $role ), OBJECT, 'page' );
	return $page instanceof WP_Post ? (int) $page->ID : 0;
}

/**
 * Split a textarea into paragraphs (blank-line separated; single line breaks kept inside).
 *
 * @param string $text Raw text.
 * @return string[]
 */
function sh_paragraphs( string $text ): array {
	$parts = preg_split( '/\R\s*\R/u', trim( $text ) );
	return array_values( array_filter( array_map( 'trim', is_array( $parts ) ? $parts : array() ), 'strlen' ) );
}

/**
 * Trails ordered for a place: home (sh_order_home_icons) · zone_grid (only sh_show_in_zone_grid,
 * by sh_zone_grid_order) · timeline_nav (sh_timeline_nav_order).
 *
 * @param string $order home|zone_grid|timeline_nav.
 * @return WP_Term[]
 */
function sh_trails( string $order = 'home' ): array {
	$keys  = array(
		'home'         => 'sh_order_home_icons',
		'zone_grid'    => 'sh_zone_grid_order',
		'timeline_nav' => 'sh_timeline_nav_order',
	);
	$key   = isset( $keys[ $order ] ) ? $keys[ $order ] : $keys['home'];
	$terms = get_terms(
		array(
			'taxonomy'   => 'trail',
			'hide_empty' => false,
		)
	);
	if ( ! is_array( $terms ) ) {
		return array();
	}
	if ( 'zone_grid' === $order ) {
		$terms = array_filter( $terms, static fn( WP_Term $t ): bool => (bool) get_term_meta( $t->term_id, 'sh_show_in_zone_grid', true ) );
	}
	usort(
		$terms,
		static function ( WP_Term $a, WP_Term $b ) use ( $key ): int {
			$oa = (int) get_term_meta( $a->term_id, $key, true );
			$ob = (int) get_term_meta( $b->term_id, $key, true );
			$oa = $oa > 0 ? $oa : PHP_INT_MAX;
			$ob = $ob > 0 ? $ob : PHP_INT_MAX;
			return $oa <=> $ob ?: strcasecmp( $a->name, $b->name );
		}
	);
	return array_values( $terms );
}

/**
 * Zones ordered by sh_number.
 *
 * @return WP_Term[]
 */
function sh_zones(): array {
	$terms = get_terms(
		array(
			'taxonomy'   => 'zone',
			'hide_empty' => false,
		)
	);
	if ( ! is_array( $terms ) ) {
		return array();
	}
	usort( $terms, static fn( WP_Term $a, WP_Term $b ): int => (int) get_term_meta( $a->term_id, 'sh_number', true ) <=> (int) get_term_meta( $b->term_id, 'sh_number', true ) );
	return $terms;
}

/**
 * Published objects of a trail, in the trail's sh_object_order (unlisted ones after, A–Z).
 *
 * @param int|WP_Term $trail Trail term or id.
 * @return WP_Post[]
 */
function sh_trail_objects( $trail ): array {
	$term_id = $trail instanceof WP_Term ? $trail->term_id : (int) $trail;
	$posts   = get_posts(
		array(
			'post_type'      => 'object',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy'         => 'trail',
					'field'            => 'term_id',
					'terms'            => $term_id,
					'include_children' => false,
				),
			),
		)
	);
	$order = get_term_meta( $term_id, 'sh_object_order', true );
	$rank  = is_array( $order ) ? array_flip( array_map( 'intval', array_values( $order ) ) ) : array();
	usort(
		$posts,
		static function ( WP_Post $a, WP_Post $b ) use ( $rank ): int {
			$ra = isset( $rank[ $a->ID ] ) ? $rank[ $a->ID ] : PHP_INT_MAX;
			$rb = isset( $rank[ $b->ID ] ) ? $rank[ $b->ID ] : PHP_INT_MAX;
			return $ra <=> $rb ?: strcasecmp( $a->post_title, $b->post_title );
		}
	);
	return $posts;
}

/**
 * Published timeline cards of a trail ordered by sh_card_order.
 *
 * @param int|WP_Term $trail Trail term or id.
 * @return WP_Post[]
 */
function sh_timeline_items( $trail ): array {
	$term_id = $trail instanceof WP_Term ? $trail->term_id : (int) $trail;
	return get_posts(
		array(
			'post_type'      => 'timeline_item',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => 'sh_card_order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => array(
				'meta_value_num' => 'ASC',
				'ID'             => 'ASC',
			),
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'sh_trail',
					'value' => (string) $term_id,
				),
			),
		)
	);
}

/**
 * Panel images of an object (sh_extra_image_1..6, empty ones skipped).
 *
 * @param int $post_id Object id.
 * @return int[] Attachment ids.
 */
function sh_object_extra_images( int $post_id ): array {
	$ids = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$id = absint( get_post_meta( $post_id, 'sh_extra_image_' . $i, true ) );
		if ( $id ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * Hero media of an object as URLs ('' when missing).
 *
 * @param int $post_id Object id.
 * @return array{webm:string,hevc:string,poster:string,image_id:int,has_alpha:bool}
 */
function sh_object_hero( int $post_id ): array {
	$url = static function ( string $key ) use ( $post_id ): string {
		$id = absint( get_post_meta( $post_id, $key, true ) );
		$u  = $id ? wp_get_attachment_url( $id ) : false;
		return is_string( $u ) ? $u : '';
	};
	return array(
		'webm'      => $url( 'sh_hero_video_webm' ),
		'hevc'      => $url( 'sh_hero_video_hevc' ),
		'poster'    => $url( 'sh_hero_poster' ),
		'image_id'  => absint( get_post_meta( $post_id, 'sh_hero_image', true ) ),
		'has_alpha' => (bool) get_post_meta( $post_id, 'sh_hero_video_has_alpha', true ),
	);
}

/**
 * Detail layout (sh_layout JSON) as an array; [] when empty/invalid.
 *
 * @param int $post_id Object id.
 * @return array<string, mixed>
 */
function sh_object_layout( int $post_id ): array {
	$data = json_decode( (string) get_post_meta( $post_id, 'sh_layout', true ), true );
	return is_array( $data ) ? $data : array();
}
