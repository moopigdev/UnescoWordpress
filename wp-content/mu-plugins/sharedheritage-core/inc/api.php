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
 * @param int $variant Trail version slot (sh_variant_<n>_hero_*, D59) · 0 = the object's own hero. A version has no still image.
 * @return array{webm:string,hevc:string,poster:string,image_id:int,has_alpha:bool}
 */
function sh_object_hero( int $post_id, int $variant = 0 ): array {
	$p   = $variant > 0 ? "sh_variant_{$variant}_hero_" : 'sh_hero_';
	$url = static function ( string $key ) use ( $post_id ): string {
		$id = absint( get_post_meta( $post_id, $key, true ) );
		$u  = $id ? wp_get_attachment_url( $id ) : false;
		return is_string( $u ) ? $u : '';
	};
	return array(
		'webm'      => $url( $p . 'video_webm' ),
		'hevc'      => $url( $p . 'video_hevc' ),
		'poster'    => $url( $p . 'poster' ),
		'image_id'  => $variant > 0 ? 0 : absint( get_post_meta( $post_id, 'sh_hero_image', true ) ),
		'has_alpha' => (bool) get_post_meta( $post_id, $p . 'video_has_alpha', true ),
	);
}

/**
 * Trail versions of an object (D59): slot n => trail term id, for every slot that has a trail
 * (meta sh_variant_<n>_trail · CONTENT-MODEL §2b). The main trail's look is the object's own fields (no slot).
 *
 * @param int $post_id Object id.
 * @return array<int, int>
 */
function sh_object_variant_slots( int $post_id ): array {
	$out = array();
	for ( $n = 1; $n <= SH_VARIANT_SLOTS; $n++ ) {
		$term_id = absint( get_post_meta( $post_id, "sh_variant_{$n}_trail", true ) );
		if ( $term_id && ! in_array( $term_id, $out, true ) ) {
			$out[ $n ] = $term_id;
		}
	}
	return $out;
}

/**
 * Trail version of an object for the trail the visitor came from (D59): null = show the object as it is (main trail ·
 * unknown/forged trail · trail the object is not in · trail without a version). The look is the object's own fields
 * overridden field by field — a version field that is empty keeps the object's value, except the info rows (any filled
 * = only the filled rows).
 *
 * @param int    $post_id    Object id.
 * @param string $trail_slug Trail slug asked for (untrusted: sanitized and checked against the object's trails).
 * @return array{slot:int,trail:WP_Term,hero:array{webm:string,hevc:string,poster:string,image_id:int,has_alpha:bool},layout:array<string,mixed>,colors:array<string,string>,images:array<int,int>,title:string,caption:string,info:array<string,string>}|null
 */
function sh_object_variant( int $post_id, string $trail_slug ): ?array {
	$trail_slug = sanitize_key( $trail_slug );
	if ( '' === $trail_slug || ! $post_id ) {
		return null;
	}
	$term = get_term_by( 'slug', $trail_slug, 'trail' );
	if ( ! $term instanceof WP_Term || ! has_term( $term->term_id, 'trail', $post_id ) ) {
		return null;
	}
	$slot = array_search( (int) $term->term_id, sh_object_variant_slots( $post_id ), true );
	if ( false === $slot ) {
		return null;
	}
	$n    = (int) $slot;
	$meta = static fn( string $key ): string => trim( (string) get_post_meta( $post_id, "sh_variant_{$n}_{$key}", true ) );

	$hero = sh_object_hero( $post_id, $n );
	if ( '' === $hero['webm'] && '' === $hero['hevc'] ) {
		$hero = sh_object_hero( $post_id ); // No video in this version: the object's own hero.
	}
	$colors = array();
	foreach ( array( 'panel_bg', 'title_color', 'text_color', 'meta_color', 'collection_color' ) as $key ) {
		$hex = sanitize_hex_color( $meta( $key ) );
		if ( is_string( $hex ) && '' !== $hex ) {
			$colors[ 'sh_' . $key ] = $hex;
		}
	}
	$images = array();
	for ( $k = 1; $k <= 2; $k++ ) {
		$id = absint( get_post_meta( $post_id, "sh_variant_{$n}_image_{$k}", true ) );
		if ( $id ) {
			$images[ $k ] = $id;
		}
	}
	$info = array();
	foreach ( array_keys( sh_core_variant_info_keys() ) as $key ) {
		if ( '' !== $meta( $key ) ) {
			$info[ $key ] = $meta( $key );
		}
	}
	return array(
		'slot'    => $n,
		'trail'   => $term,
		'hero'    => $hero,
		'layout'  => sh_json_value( get_post_meta( $post_id, "sh_variant_{$n}_layout", true ) ),
		'colors'  => $colors,
		'images'  => $images,
		'title'   => $meta( 'title' ),
		'caption' => $meta( 'caption' ),
		'info'    => $info,
	);
}

/**
 * Value of one info row (sh_<key>: period · place_of_production · material · dimensions · local_name · collection ·
 * object_biography · scientific_name) as the detail page shows it: the object's own value, or — when the trail version
 * (sh_object_variant()) has any info row filled — only the version's rows ('' for the others).
 *
 * @param int                       $post_id Object id.
 * @param string                    $key     Info key without the sh_ prefix.
 * @param array<string, mixed>|null $variant Result of sh_object_variant() or null.
 */
function sh_object_info_value( int $post_id, string $key, ?array $variant = null ): string {
	if ( is_array( $variant ) && ! empty( $variant['info'] ) && is_array( $variant['info'] ) ) {
		return isset( $variant['info'][ $key ] ) ? (string) $variant['info'][ $key ] : '';
	}
	return trim( (string) get_post_meta( $post_id, 'sh_' . $key, true ) );
}

/**
 * Link to an object as shown from a trail: the permalink, plus ?sh_trail=<slug> when the object has a trail version for
 * that trail (D59) — the detail page then opens in that trail's look. Without a version (single-trail object · the
 * object's main trail · empty trail) the plain permalink. The query name is not "trail" — that is WordPress's own
 * taxonomy query var (a forged value would 404 the page).
 *
 * @param WP_Post|int   $post  Object.
 * @param int|WP_Term|string $trail Trail the link is shown in (term, term id; 0/'' = none).
 */
function sh_object_trail_url( $post, $trail ): string {
	$post_id = $post instanceof WP_Post ? (int) $post->ID : (int) $post;
	$url     = $post_id ? get_permalink( $post_id ) : false;
	if ( ! is_string( $url ) ) {
		return '';
	}
	$term = $trail instanceof WP_Term ? $trail : ( $trail ? get_term( (int) $trail, 'trail' ) : null );
	if ( $term instanceof WP_Term && in_array( (int) $term->term_id, sh_object_variant_slots( $post_id ), true ) ) {
		return add_query_arg( 'sh_trail', $term->slug, $url );
	}
	return $url;
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

/**
 * A format=inline_html textarea as paragraphs of safe HTML (blank line = paragraph · single line break = <br> ·
 * only <strong>/<b>/<em>/<i>/<br> kept, no attributes). Ready to echo inside <p> — no further escaping.
 *
 * @param string $text Raw value.
 * @return string[]
 */
function sh_rich_paragraphs( string $text ): array {
	$out = array();
	foreach ( sh_paragraphs( $text ) as $p ) {
		$out[] = nl2br( wp_kses( $p, sh_core_inline_tags() ), false );
	}
	return $out;
}

/**
 * JSON object stored in a meta/option value as an array ([] when empty/invalid).
 *
 * @param mixed $value Stored value.
 * @return array<string, mixed>
 */
function sh_json_value( $value ): array {
	$data = is_string( $value ) ? json_decode( $value, true ) : null;
	return is_array( $data ) ? $data : array();
}

/**
 * Tile images of an object in one trail: its "Tiles in other trails" entry for that trail when it has an image,
 * else sh_tile_image / sh_tile_image_hover.
 *
 * @param int         $post_id Object id.
 * @param int|WP_Term $trail   Trail term or id.
 * @return array{image:int,hover:int}
 */
function sh_object_trail_tile( int $post_id, $trail ): array {
	$term_id = $trail instanceof WP_Term ? $trail->term_id : (int) $trail;
	for ( $n = 1; $n <= 4; $n++ ) {
		if ( $term_id && absint( get_post_meta( $post_id, "sh_trail_tile_{$n}_trail", true ) ) === $term_id ) {
			$image = absint( get_post_meta( $post_id, "sh_trail_tile_{$n}_image", true ) );
			if ( $image ) {
				return array(
					'image' => $image,
					'hover' => absint( get_post_meta( $post_id, "sh_trail_tile_{$n}_image_hover", true ) ),
				);
			}
		}
	}
	return array(
		'image' => absint( get_post_meta( $post_id, 'sh_tile_image', true ) ),
		'hover' => absint( get_post_meta( $post_id, 'sh_tile_image_hover', true ) ),
	);
}

/**
 * Tiles of a trail page grid in order: published objects (sh_trail_objects() order · tile images for this trail ·
 * link to the object) with the trail's "tiles without a page" (sh_static_tile_<n>_*: image, no link) placed at their
 * position (1 = first; a position past the end = after the objects).
 *
 * @param int|WP_Term $trail Trail term or id.
 * @return array<int, array{object:WP_Post|null,url:string,label:string,image:int,hover:int}>
 */
function sh_trail_tiles( $trail ): array {
	$term_id = $trail instanceof WP_Term ? $trail->term_id : (int) $trail;
	$static  = array();
	for ( $n = 1; $n <= 3; $n++ ) {
		$pos   = (int) get_term_meta( $term_id, "sh_static_tile_{$n}_position", true );
		$image = absint( get_term_meta( $term_id, "sh_static_tile_{$n}_image", true ) );
		if ( $pos > 0 && $image ) {
			$static[] = array(
				'pos'  => $pos,
				'tile' => array(
					'object' => null,
					'url'    => '',
					'label'  => (string) get_term_meta( $term_id, "sh_static_tile_{$n}_label", true ),
					'image'  => $image,
					'hover'  => absint( get_term_meta( $term_id, "sh_static_tile_{$n}_image_hover", true ) ),
				),
			);
		}
	}
	usort( $static, static fn( array $a, array $b ): int => $a['pos'] <=> $b['pos'] );
	$objects = array();
	foreach ( sh_trail_objects( $term_id ) as $post ) {
		$objects[] = array(
			'object' => $post,
			'url'    => sh_object_trail_url( $post, $term_id ), // D59: ?sh_trail= when the object has a version for this trail.
			'label'  => get_the_title( $post ),
		) + sh_object_trail_tile( $post->ID, $term_id );
	}
	$out = array();
	while ( $objects || $static ) {
		if ( $static && ( count( $out ) + 1 >= $static[0]['pos'] || ! $objects ) ) {
			$out[] = array_shift( $static )['tile'];
		} else {
			$out[] = array_shift( $objects );
		}
	}
	return $out;
}

/**
 * Tiles of the Zone 1–3 page grid (D57) in slot order: sh_zone_tile_<n>_object / _image / _image_hover of the zone term.
 * A published object links to its page (image/hover empty = the object's own tile · sh_object_trail_tile() without a
 * trail); no object or an unpublished one = a picture without a link (needs an image). Empty slots are skipped. The link
 * carries ?sh_trail= of the tile's trail (sh_zone_tile_<n>_trail) when the object has a version for it (D59).
 *
 * @param int|WP_Term $zone Zone term or id.
 * @return array<int, array{slot:int,object:WP_Post|null,url:string,label:string,image:int,hover:int}>
 */
function sh_zone_grid( $zone ): array {
	$term_id = $zone instanceof WP_Term ? $zone->term_id : (int) $zone;
	$out     = array();
	if ( ! $term_id ) {
		return $out;
	}
	for ( $n = 1; $n <= SH_ZONE_GRID_SLOTS; $n++ ) {
		$obj_id = absint( get_term_meta( $term_id, "sh_zone_tile_{$n}_object", true ) );
		$image  = absint( get_term_meta( $term_id, "sh_zone_tile_{$n}_image", true ) );
		$hover  = absint( get_term_meta( $term_id, "sh_zone_tile_{$n}_image_hover", true ) );
		$post   = $obj_id ? get_post( $obj_id ) : null;
		$post   = ( $post instanceof WP_Post && 'object' === $post->post_type && 'publish' === $post->post_status ) ? $post : null;
		if ( $post ) {
			$own   = sh_object_trail_tile( $post->ID, 0 );
			$image = $image ? $image : $own['image'];
			$hover = $hover ? $hover : $own['hover'];
		}
		if ( ! $image ) {
			continue; // Nothing to show (an empty slot, or an unpublished object without its own picture).
		}
		$trail = absint( get_term_meta( $term_id, "sh_zone_tile_{$n}_trail", true ) );
		$out[] = array(
			'slot'   => $n,
			'object' => $post,
			'url'    => $post ? sh_object_trail_url( $post, $trail ) : '', // D59: the trail of the tile decides which look the page opens in.
			'label'  => $post ? get_the_title( $post ) : '',
			'image'  => $image,
			'hover'  => $hover,
		);
	}
	return $out;
}

/**
 * Home "Your animal guide is..." cards (D48): every trail with a card image (sh_guide_card), in home icon order.
 * body = sh_rich_paragraphs() of sh_guide_body (safe HTML) · url = the trail page.
 *
 * @return array<int, array{trail:WP_Term,name:string,words:string,body:string[],follow:string,card:int,url:string}>
 */
function sh_guide_cards(): array {
	$out = array();
	foreach ( sh_trails( 'home' ) as $t ) {
		$card = absint( get_term_meta( $t->term_id, 'sh_guide_card', true ) );
		if ( ! $card ) {
			continue;
		}
		$url   = get_term_link( $t );
		$out[] = array(
			'trail'  => $t,
			'name'   => (string) get_term_meta( $t->term_id, 'sh_guide_name', true ),
			'words'  => (string) get_term_meta( $t->term_id, 'sh_guide_words', true ),
			'body'   => sh_rich_paragraphs( (string) get_term_meta( $t->term_id, 'sh_guide_body', true ) ),
			'follow' => (string) get_term_meta( $t->term_id, 'sh_guide_follow', true ),
			'card'   => $card,
			'url'    => is_string( $url ) ? $url : '',
		);
	}
	return $out;
}

/**
 * Introduction page (D50) — header part shared by the four chapters. Texts raw (escape on output) except
 * paragraphs (non-empty sh_intro_head_paragraph_1..5, raw strings).
 *
 * @param int $page_id Intro page (0 = sh_page_id( 'intro' )).
 * @return array{emblem:int,title:string,paragraphs:string[],credit:string,env_heading:string,layout:array<string,mixed>}
 */
function sh_intro_head( int $page_id = 0 ): array {
	$page_id    = $page_id ? $page_id : sh_page_id( 'intro' );
	$paragraphs = array();
	for ( $k = 1; $k <= 5; $k++ ) {
		$p = (string) get_post_meta( $page_id, "sh_intro_head_paragraph_{$k}", true );
		if ( '' !== trim( $p ) ) {
			$paragraphs[] = $p;
		}
	}
	return array(
		'emblem'      => absint( get_post_meta( $page_id, 'sh_intro_head_emblem', true ) ),
		'title'       => (string) get_post_meta( $page_id, 'sh_intro_head_title', true ),
		'paragraphs'  => $paragraphs,
		'credit'      => (string) get_post_meta( $page_id, 'sh_intro_head_credit', true ),
		'env_heading' => (string) get_post_meta( $page_id, 'sh_intro_env_heading', true ),
		'layout'      => sh_json_value( get_post_meta( $page_id, 'sh_intro_head_layout', true ) ),
	);
}

/**
 * Introduction chapters 1–4 (D50). texts[k] = sh_rich_paragraphs() of sh_intro_ch<n>_text_<k> (safe HTML, slot k
 * 1..7, empty slots left out) · images[k] = {id: still image, animation: animated image or 0} (slot 1..5, empty
 * left out) · layout = sh_intro_ch<n>_layout (sections → blocks that point to text/image slots).
 *
 * @param int $page_id Intro page (0 = sh_page_id( 'intro' )).
 * @return array<int, array{n:int,title:string,accent:string,map_image:int,map_animation:int,texts:array<int,string[]>,images:array<int,array{id:int,animation:int}>,layout:array<string,mixed>}>
 */
function sh_intro_chapters( int $page_id = 0 ): array {
	$page_id = $page_id ? $page_id : sh_page_id( 'intro' );
	$get     = static fn( string $key ) => get_post_meta( $page_id, $key, true );
	$out     = array();
	for ( $n = 1; $n <= 4; $n++ ) {
		$texts = array();
		for ( $k = 1; $k <= 7; $k++ ) {
			$p = sh_rich_paragraphs( (string) $get( "sh_intro_ch{$n}_text_{$k}" ) );
			if ( $p ) {
				$texts[ $k ] = $p;
			}
		}
		$images = array();
		for ( $k = 1; $k <= 5; $k++ ) {
			$id   = absint( $get( "sh_intro_ch{$n}_image_{$k}" ) );
			$anim = absint( $get( "sh_intro_ch{$n}_image_{$k}_animation" ) );
			if ( $id || $anim ) {
				$images[ $k ] = array(
					'id'        => $id,
					'animation' => $anim,
				);
			}
		}
		$out[ $n ] = array(
			'n'             => $n,
			'title'         => (string) $get( "sh_intro_ch{$n}_title" ),
			'accent'        => (string) $get( "sh_intro_ch{$n}_accent" ),
			'map_image'     => absint( $get( "sh_intro_ch{$n}_map_image" ) ),
			'map_animation' => absint( $get( "sh_intro_ch{$n}_map_animation" ) ),
			'texts'         => $texts,
			'images'        => $images,
			'layout'        => sh_json_value( $get( "sh_intro_ch{$n}_layout" ) ),
		);
	}
	return $out;
}
