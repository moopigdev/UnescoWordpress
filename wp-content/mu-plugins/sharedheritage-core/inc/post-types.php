<?php
/**
 * Post types and taxonomies (docs/CONTENT-MODEL.md §1).
 *
 * object        CPT  public  /object/<slug>/      → single-object.php (detail)
 * timeline_item CPT  admin-only (cards of a trail timeline, shown on the Timeline page)
 * trail         tax  on object  /trail/<slug>/    → taxonomy-trail.php (trail)
 * zone          tax  on object  /zone/<slug>/     → taxonomy-zone.php (zone)
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register CPTs and taxonomies.
 */
function sh_core_register_types(): void {
	register_post_type(
		'object',
		array(
			'labels'          => array(
				'name'          => 'Objects',
				'singular_name' => 'Object',
				'add_new_item'  => 'Add new object',
				'edit_item'     => 'Edit object',
				'all_items'     => 'All objects',
				'search_items'  => 'Search objects',
				'not_found'     => 'No objects found',
			),
			'public'          => true,
			'has_archive'     => false,
			'show_in_rest'    => true,
			'menu_position'   => 20,
			'menu_icon'       => 'dashicons-art',
			// No 'editor': every text is a field (classic edit screen with field boxes).
			'supports'        => array( 'title', 'custom-fields', 'revisions' ),
			'rewrite'         => array(
				'slug'       => 'object',
				'with_front' => false,
			),
			'capability_type' => 'post',
		)
	);

	register_post_type(
		'timeline_item',
		array(
			'labels'              => array(
				'name'          => 'Timeline cards',
				'singular_name' => 'Timeline card',
				'add_new_item'  => 'Add new timeline card',
				'edit_item'     => 'Edit timeline card',
				'all_items'     => 'Timeline cards',
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-clock',
			'supports'            => array( 'title', 'custom-fields', 'revisions' ),
			'rewrite'             => false,
			'capability_type'     => 'post',
		)
	);

	register_taxonomy(
		'trail',
		array( 'object' ),
		array(
			'labels'            => array(
				'name'          => 'Trails',
				'singular_name' => 'Trail',
				'edit_item'     => 'Edit trail',
				'add_new_item'  => 'Add new trail',
				'all_items'     => 'All trails',
			),
			'public'            => true,
			'hierarchical'      => true, // checkbox list in the object edit screen (no parent trails are used).
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'         => 'trail',
				'with_front'   => false,
				'hierarchical' => false,
			),
		)
	);

	register_taxonomy(
		'zone',
		array( 'object' ),
		array(
			'labels'            => array(
				'name'          => 'Zones',
				'singular_name' => 'Zone',
				'edit_item'     => 'Edit zone',
				'add_new_item'  => 'Add new zone',
				'all_items'     => 'All zones',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => false,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'         => 'zone',
				'with_front'   => false,
				'hierarchical' => false,
			),
		)
	);
}
add_action( 'init', 'sh_core_register_types', 5 );

/**
 * Flush rewrite rules once after the content model version changes (new CPT/taxonomy slugs).
 */
function sh_core_maybe_flush_rewrites(): void {
	if ( get_option( 'sh_core_rewrite_version' ) === SH_CORE_VERSION ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'sh_core_rewrite_version', SH_CORE_VERSION, true );
}
add_action( 'init', 'sh_core_maybe_flush_rewrites', 99 );
