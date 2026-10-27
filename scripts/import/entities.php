<?php
/**
 * Importer: pages (Home / Introduction / Timeline), trail + zone terms, site options + menus.
 * Objects and timeline cards: objects.php.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/objects.php';

/**
 * Create a page by route slug (adopts an existing page with that path). Returns [id, write].
 *
 * @param string               $role  front|intro|timeline.
 * @param string               $slug  Page slug.
 * @param string               $title Page title.
 * @return array{id:int,write:bool}
 */
function sh_import_page( string $role, string $slug, string $title ): array {
	$key      = 'page:' . $role;
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing instanceof WP_Post && ! sh_imp_find_post( 'page', $key ) ) {
		update_post_meta( $existing->ID, '_sh_import_key', $key ); // adopt a page made by hand.
	}
	list( $id, $write ) = sh_imp_post(
		'page',
		$key,
		array(
			'post_type'  => 'page',
			'post_title' => $title,
			'post_name'  => $slug,
		)
	);
	return array(
		'id'    => $id,
		'write' => $write,
	);
}

/**
 * Home "Trails" footprint video = manifest entry with usage "home-trails" (walking.webm · D12).
 *
 * @param array<string, mixed> $manifest media manifest.
 * @return array{webm:int,hevc:int,poster:int,has_alpha:bool,still:int}
 */
function sh_imp_home_trails_video( array $manifest ): array {
	return sh_imp_video( sh_imp_manifest_entry( $manifest, 'usage', 'home-trails' ), 'Home footprints animation' );
}

/**
 * Pages of the route table + their fields (Timeline fields are written by sh_import_timeline()).
 *
 * @param array<string, mixed> $seed     Seed files.
 * @param array<string, mixed> $map      image-map.json.
 * @param array<string, mixed> $manifest media manifest.
 * @return array<string, array{id:int,write:bool}>
 */
function sh_import_pages( array $seed, array $map, array $manifest ): array {
	$articles = array();
	foreach ( $seed['articles']['articles'] as $a ) {
		$articles[ $a['slug'] ] = $a;
	}
	$zone4 = array_values( array_filter( $seed['zones']['zones'], static fn( $z ) => 4 === (int) $z['number'] ) );

	$pages = array(
		'intro'    => sh_import_page( 'intro', 'intro', (string) $articles['introduction']['title'] ),
		'timeline' => sh_import_page( 'timeline', 'timeline', $zone4 ? (string) $zone4[0]['name'] : 'Timeline' ),
		'front'    => sh_import_page( 'front', 'home', 'Home' ),
	);

	// Home = static front page (front-page.php). Never replace a front page chosen by an admin.
	if ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) || 'overwrite' === sh_imp_config()['mode'] ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $pages['front']['id'] );
	}

	$home = $seed['site']['home'];
	$pm   = $map['pages'];
	if ( $pages['front']['write'] ) {
		$video = sh_imp_home_trails_video( $manifest );
		sh_imp_meta(
			'post',
			$pages['front']['id'],
			array(
				'sh_home_hero_image'          => sh_imp_image( $map, $pm['home']['hero'], 'Home hero' ),
				'sh_home_intro_title'         => $home['introduction']['title'],
				'sh_home_intro_paragraph_1'   => $home['introduction']['paragraphs'][0]['text'],
				'sh_home_intro_paragraph_2'   => $home['introduction']['paragraphs'][1]['text'],
				'sh_home_intro_image'         => sh_imp_image( $map, $pm['home']['intro_image'], 'Home introduction' ),
				'sh_home_intro_link'          => $pages['intro']['id'],
				'sh_home_zone_bar_image'      => sh_imp_image( $map, $pm['home']['zone_bar'], 'Zone bar' ),
				'sh_home_trails_title'        => $home['trails_section']['title'],
				'sh_home_trails_tagline'      => $home['trails_section']['tagline'],
				'sh_home_trails_paragraph_1'  => $home['trails_section']['paragraph_1'],
				'sh_home_trails_paragraph_2'  => $home['trails_section']['paragraph_2'],
				'sh_home_trails_video_webm'   => $video['webm'],
				'sh_home_trails_video_poster' => $video['poster'],
			)
		);
	}

	if ( $pages['intro']['write'] ) {
		$intro = $articles['introduction'];
		$know  = $articles['knowing-southeast-asia'];
		$meta  = array(
			'sh_intro_cover_image'         => sh_imp_image( $map, $pm['intro']['cover'], 'Home hero' ),
			'sh_intro_title'               => $intro['title'],
			'sh_intro_paragraph_1'         => $intro['summary_paragraphs'][0]['text'],
			'sh_intro_paragraph_2'         => $intro['summary_paragraphs'][1]['text'],
			'sh_intro_body'                => $intro['body'],
			'sh_intro_image'               => sh_imp_image( $map, $pm['intro']['intro_image'], 'Introduction' ),
			'sh_intro_knowing_title'       => $know['title'],
			'sh_intro_knowing_paragraph_1' => $know['summary_paragraphs'][0]['text'],
			'sh_intro_knowing_paragraph_2' => $know['summary_paragraphs'][1]['text'],
			'sh_intro_knowing_body'        => $know['body'],
			'sh_intro_knowing_image'       => sh_imp_image( $map, $pm['intro']['knowing_image'], 'Knowing Southeast Asia' ),
			'sh_intro_map_image'           => sh_imp_image( $map, $pm['intro']['map_image'], 'Map of Southeast Asia' ),
		);
		foreach ( $seed['articles']['articles'] as $a ) {
			if ( empty( $a['map_slide'] ) ) {
				continue;
			}
			$n = (int) $a['map_slide'];
			$meta[ "sh_intro_map_slide_{$n}_title" ] = $a['title'];
			$meta[ "sh_intro_map_slide_{$n}_body" ]  = implode( "\n\n", array_map( static fn( $p ) => (string) $p['text'], $a['paragraphs'] ) );
		}
		sh_imp_meta( 'post', $pages['intro']['id'], $meta );
	}
	return $pages;
}

/**
 * 17 trail terms (seed "etc" is not a trail). Icon group colours: DESIGN-SPEC §2.1 + §4.5 groups.
 *
 * @param array<string, mixed>                    $seed  Seed files.
 * @param array<string, mixed>                    $map   image-map.json.
 * @param array<string, array{id:int,write:bool}> $pages Pages.
 * @return array<string, array{id:int,write:bool}>
 */
function sh_import_trails( array $seed, array $map, array $pages ): array {
	$group_color = array(
		'#168E73' => array( 'bird', 'hornbill', 'water-bird', 'rooster' ),
		'#E7553C' => array( 'garuda', 'phoenix', 'dragon', 'naga' ),
		'#C3BC85' => array( 'elephant', 'buffalo', 'horse', 'deer', 'lion' ),
		'#B36F94' => array( 'makara', 'turtle', 'fish', 'seashell' ),
	);
	$timeline_frame = strtok( (string) $seed['timeline-elephant']['frame'], ' ' );
	$out            = array();
	foreach ( $seed['trails']['trails'] as $t ) {
		if ( ! empty( $t['not_a_trail'] ) ) {
			continue;
		}
		$slug                 = (string) $t['slug'];
		list( $id, $write )   = sh_imp_term( 'trail', $slug, (string) $t['name'], isset( $t['description'] ) ? (string) $t['description'] : '' );
		$out[ $slug ]         = array(
			'id'    => $id,
			'write' => $write,
		);
		if ( ! $write ) {
			continue;
		}
		$m     = isset( $map['trails'][ $slug ] ) ? $map['trails'][ $slug ] : array();
		$ref   = static fn( string $k ) => isset( $m[ $k ] ) ? $m[ $k ] : null;
		$color = '';
		foreach ( $group_color as $hex => $members ) {
			$color = in_array( $slug, $members, true ) ? $hex : $color;
		}
		$has_timeline = ! empty( $t['timeline_page_frame'] ) && strtok( (string) $t['timeline_page_frame'], ' ' ) === $timeline_frame;
		sh_imp_meta(
			'term',
			$id,
			array(
				'sh_title_on_trail_page'  => isset( $t['title_on_trail_page'] ) ? $t['title_on_trail_page'] : '',
				'sh_order_home_icons'     => (int) $t['order_home_icons'],
				'sh_icon_home'            => sh_imp_image( $map, $ref( 'icon_home' ), $t['name'] . ' icon' ),
				'sh_icon_home_hover'      => sh_imp_image( $map, $ref( 'icon_home_hover' ), $t['name'] . ' icon hover' ),
				'sh_show_in_zone_grid'    => ! empty( $t['in_zone_grid_frame_1472_1474'] ),
				'sh_zone_grid_order'      => (int) $ref( 'zone_grid_order' ),
				'sh_tile_zone_grid'       => sh_imp_image( $map, $ref( 'tile_zone_grid' ), $t['name'] . ' zone tile' ),
				'sh_tile_zone_grid_hover' => sh_imp_image( $map, $ref( 'tile_zone_grid_hover' ), $t['name'] . ' zone tile hover' ),
				'sh_tile_bg'              => isset( $t['tile_bg']['hex'] ) ? $t['tile_bg']['hex'] : '',
				'sh_timeline_nav_order'   => (int) $ref( 'timeline_nav_order' ),
				'sh_icon_timeline'        => sh_imp_image( $map, $ref( 'icon_timeline' ), $t['name'] . ' timeline icon' ),
				'sh_icon_group_color'     => $color,
				'sh_cover_image'          => sh_imp_image( $map, $ref( 'cover' ), $t['name'] . ' trail cover' ),
				'sh_timeline_page'        => $has_timeline ? $pages['timeline']['id'] : 0,
			)
		);
	}
	return $out;
}

/**
 * Zone terms 1–5 (slug zone-<n>). Zone whose Figma page frame is the timeline frame links to the Timeline page.
 *
 * @param array<string, mixed>                    $seed  Seed files.
 * @param array<string, mixed>                    $map   image-map.json.
 * @param array<string, array{id:int,write:bool}> $pages Pages.
 * @return array<int, array{id:int,write:bool}>
 */
function sh_import_zones( array $seed, array $map, array $pages ): array {
	$timeline_frame = strtok( (string) $seed['timeline-elephant']['frame'], ' ' );
	$out            = array();
	foreach ( $seed['zones']['zones'] as $z ) {
		$n                  = (int) $z['number'];
		list( $id, $write ) = sh_imp_term( 'zone', 'zone-' . $n, (string) $z['name'] );
		$out[ $n ]          = array(
			'id'    => $id,
			'write' => $write,
		);
		if ( ! $write ) {
			continue;
		}
		$m       = isset( $map['zones'][ (string) $n ] ) ? $map['zones'][ (string) $n ] : array();
		$ref     = static fn( string $k ) => isset( $m[ $k ] ) ? $m[ $k ] : null;
		$to_page = ! empty( $z['page_frame'] ) && strtok( (string) $z['page_frame'], ' ' ) === $timeline_frame;
		sh_imp_meta(
			'term',
			$id,
			array(
				'sh_number'           => $n,
				'sh_card_title'       => isset( $z['name_raw'] ) ? $z['name_raw'] : $z['name'],
				'sh_color'            => $z['color_hex'],
				'sh_card_image'       => sh_imp_image( $map, $ref( 'card' ), 'Zone ' . $n . ' card' ),
				'sh_card_image_hover' => sh_imp_image( $map, $ref( 'card_hover' ), 'Zone ' . $n . ' card hover' ),
				'sh_cover_image'      => sh_imp_image( $map, $ref( 'cover' ), 'Zone ' . $n . ' cover' ),
				'sh_banner_image'     => sh_imp_image( $map, $ref( 'banner' ), 'Zone ' . $n . ' banner' ),
				'sh_has_page'         => ! empty( $z['has_page'] ),
				'sh_link_page'        => $to_page ? $pages['timeline']['id'] : 0,
			)
		);
	}
	return $out;
}

/**
 * Site options (labels, header image) and the two menus from site.json (links "#": P-211).
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $map  image-map.json.
 */
function sh_import_site( array $seed, array $map ): void {
	$overwrite = 'overwrite' === sh_imp_config()['mode'];
	$labels    = $seed['site']['labels'];
	$options   = array(
		'sh_label_read_more'   => $labels['read_more'],
		'sh_label_back'        => $labels['back'],
		'sh_label_zone_prefix' => $labels['zone_prefix'],
		'sh_header_image'      => sh_imp_image( $map, $map['site']['header_image'], 'Header background' ),
	);
	foreach ( $options as $name => $value ) {
		if ( $overwrite || false === get_option( $name, false ) ) {
			update_option( $name, $value );
		}
	}

	$front_id  = (int) get_option( 'page_on_front' );
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations = is_array( $locations ) ? $locations : array();
	foreach ( array( 'primary' => 'nav_primary', 'utility' => 'nav_utility' ) as $location => $seed_key ) {
		$name = ucfirst( $location );
		$menu = wp_get_nav_menu_object( $name );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $name );
			if ( is_wp_error( $menu_id ) ) {
				throw new RuntimeException( 'menu ' . $name . ': ' . $menu_id->get_error_message() );
			}
			foreach ( $seed['site'][ $seed_key ] as $i => $label ) {
				$item = array(
					'menu-item-title'    => wp_slash( (string) $label ),
					'menu-item-url'      => '#',
					'menu-item-type'     => 'custom',
					'menu-item-status'   => 'publish',
					'menu-item-position' => $i + 1,
				);
				// "Main Page" = Home 1470 (every prototype link targets 670:1152); the rest have no target yet (P-211).
				if ( 'Main Page' === $label && $front_id ) {
					$item = array_merge(
						$item,
						array(
							'menu-item-type'      => 'post_type',
							'menu-item-object'    => 'page',
							'menu-item-object-id' => $front_id,
							'menu-item-url'       => '',
						)
					);
				}
				wp_update_nav_menu_item( $menu_id, 0, $item );
			}
			sh_imp_count( 'created', 'menu' );
		} else {
			$menu_id = (int) $menu->term_id;
		}
		// Bind even if the theme has not registered the location yet (core-chrome registers "primary").
		if ( empty( $locations[ $location ] ) || $overwrite ) {
			$locations[ $location ] = $menu_id;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}
