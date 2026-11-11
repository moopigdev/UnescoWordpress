<?php
/**
 * Field definitions — the single source for: ACF field groups (admin UI), register_*_meta (storage,
 * sanitize, REST) and the site settings page. Must match docs/CONTENT-MODEL.md 1:1
 * (checked by scripts/import/check-contract.py).
 *
 * Field keys:  name   meta key / option name (prefix sh_)
 *              type   ACF type: text textarea number color_picker image file true_false post_object taxonomy relationship (+ tab = UI only)
 *              rest   show_in_rest (default true)
 *              format 'json' = textarea holding a JSON object (validated)
 *              other keys are passed to ACF as-is (instructions, rows, mime_types, post_type, taxonomy …)
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * One field definition.
 *
 * @param string               $name  Meta key.
 * @param string               $type  ACF field type.
 * @param string               $label Admin label.
 * @param array<string, mixed> $extra Extra keys.
 * @return array<string, mixed>
 */
function sh_f( string $name, string $type, string $label, array $extra = array() ): array {
	return array_merge(
		array(
			'name'  => $name,
			'type'  => $type,
			'label' => $label,
		),
		$extra
	);
}

/**
 * A tab (ACF UI grouping only — no meta).
 *
 * @param string $label Tab label.
 * @return array<string, mixed>
 */
function sh_tab( string $label ): array {
	return array(
		'name'  => '',
		'type'  => 'tab',
		'label' => $label,
	);
}

/**
 * All field groups, keyed by group id.
 * target: post_type => <type> | taxonomy => <tax> | page => front|intro|timeline | option => true
 *
 * @return array<string, array<string, mixed>>
 */
function sh_core_field_groups(): array {
	static $groups = null;
	if ( null !== $groups ) {
		return $groups;
	}

	$img   = static fn( string $name, string $label, string $help = '' ): array => sh_f( $name, 'image', $label, array( 'instructions' => $help ) );
	$text  = static fn( string $name, string $label, string $help = '' ): array => sh_f( $name, 'text', $label, array( 'instructions' => $help ) );
	$area  = static fn( string $name, string $label, string $help = '', int $rows = 4 ): array => sh_f(
		$name,
		'textarea',
		$label,
		array(
			'instructions' => $help,
			'rows'         => $rows,
		)
	);
	$color = static fn( string $name, string $label, string $help = '' ): array => sh_f( $name, 'color_picker', $label, array( 'instructions' => $help ) );
	$num   = static fn( string $name, string $label, string $help = '' ): array => sh_f( $name, 'number', $label, array( 'instructions' => $help ) );
	$bool  = static fn( string $name, string $label, string $help = '' ): array => sh_f(
		$name,
		'true_false',
		$label,
		array(
			'instructions' => $help,
			'ui'           => 1,
		)
	);
	$page  = static fn( string $name, string $label, string $help = '' ): array => sh_f(
		$name,
		'post_object',
		$label,
		array(
			'instructions' => $help,
			'post_type'    => array( 'page' ),
			'allow_null'   => 1,
		)
	);
	$trail = static fn( string $name, string $label, string $help = '' ): array => sh_f(
		$name,
		'taxonomy',
		$label,
		array(
			'instructions' => $help,
			'taxonomy'     => 'trail',
			'field_type'   => 'select',
			'allow_null'   => 1,
			'add_term'     => 0,
			'save_terms'   => 0,
			'load_terms'   => 0,
		)
	);
	$video = static fn( string $name, string $label, string $mime, string $help = '' ): array => sh_f(
		$name,
		'file',
		$label,
		array(
			'instructions' => $help,
			'mime_types'   => $mime,
		)
	);

	$groups = array(
		'object'        => array(
			'title'  => 'Object',
			'target' => array( 'post_type' => 'object' ),
			'fields' => array(
				sh_tab( 'Text' ),
				$area( 'sh_title_raw', 'Title with line breaks', 'Only when the title must break at fixed places (empty = use the post title).', 2 ),
				$text( 'sh_collection', 'Collection' ),
				$text( 'sh_thematic_section', 'Section', 'e.g. Cosmology Religion & Spirituality' ),
				$text( 'sh_period', 'Period' ),
				$text( 'sh_place_of_production', 'Place of production / findspot' ),
				$text( 'sh_material', 'Material' ),
				$text( 'sh_dimensions', 'Dimensions' ),
				$text( 'sh_local_name', 'Local name' ),
				$area( 'sh_object_biography', 'Specimen / object biography', '', 3 ),
				$area( 'sh_caption', 'Caption', 'Paragraphs separated by a blank line.', 12 ),
				sh_tab( 'Hero media' ),
				$video( 'sh_hero_video_webm', 'Hero video (WebM)', 'webm' ),
				$video( 'sh_hero_video_hevc', 'Hero video fallback for Safari/iOS (HEVC with alpha, .mov)', 'mov', 'Only for videos with transparency.' ),
				$bool( 'sh_hero_video_has_alpha', 'Hero video has transparency' ),
				$img( 'sh_hero_poster', 'Hero video poster' ),
				$img( 'sh_hero_image', 'Hero still image', 'Shown when there is no video.' ),
				sh_tab( 'Images' ),
				$img( 'sh_tile_image', 'Trail grid tile (184×184)' ),
				$img( 'sh_tile_image_hover', 'Trail grid tile — hover (210×210)' ),
				$img( 'sh_extra_image_1', 'Panel image 1' ),
				$img( 'sh_extra_image_2', 'Panel image 2' ),
				$img( 'sh_extra_image_3', 'Panel image 3' ),
				$img( 'sh_extra_image_4', 'Panel image 4' ),
				$img( 'sh_extra_image_5', 'Panel image 5' ),
				$img( 'sh_extra_image_6', 'Panel image 6' ),
				sh_tab( 'Detail colours' ),
				$color( 'sh_panel_bg', 'Panel background', 'Empty = no fill (white).' ),
				$color( 'sh_title_color', 'Title colour' ),
				$color( 'sh_text_color', 'Text / caption colour' ),
				$color( 'sh_meta_color', 'Metadata colour', 'Empty = text colour.' ),
				$color( 'sh_collection_color', 'Collection colour', 'Empty = text colour.' ),
				sh_tab( 'Layout' ),
				sh_f(
					'sh_layout',
					'textarea',
					'Detail layout (JSON from Figma)',
					array(
						'instructions' => 'Geometry of the Figma detail frame (panel_y, panel_h, paddings, hero and panel boxes, back button). Must be a JSON object.',
						'rows'         => 8,
						'format'       => 'json',
					)
				),
			),
		),
		'timeline_item' => array(
			'title'  => 'Timeline card',
			'target' => array( 'post_type' => 'timeline_item' ),
			'fields' => array(
				$trail( 'sh_trail', 'Trail', 'The trail whose timeline shows this card.' ),
				sh_f(
					'sh_object',
					'post_object',
					'Object',
					array(
						'instructions' => 'The object this card links to (Read more).',
						'post_type'    => array( 'object' ),
						'allow_null'   => 1,
					)
				),
				$num( 'sh_card_order', 'Card order', 'Left to right, 1 = first.' ),
				$num( 'sh_illustration_order', 'Point on the illustration', 'Order of this object along the timeline illustration.' ),
				$text( 'sh_era_label', 'Era label' ),
				$area( 'sh_card_meta', 'Card metadata', 'One line per item, e.g. "Period: 10th–11th century".', 6 ),
				$img( 'sh_thumbnail', 'Card image' ),
				$img( 'sh_thumbnail_2', 'Card image 2' ),
				$img( 'sh_below_image', 'Photo under the line (optional)', 'Timeline: the large photo under the line when this object is in the centre. If empty, the card image is used. The object on the line, the mini-map and the object page always use the card image.' ),
				$img( 'sh_line_art', 'Before image (optional)', 'Timeline: shown in the large photo under the line while this object moves to the centre, then it cross-fades to the card image. Often a line drawing (transparent PNG); any image works. If empty, the card image is shown as before.' ),
			),
		),
		'trail'         => array(
			'title'  => 'Trail',
			'target' => array( 'taxonomy' => 'trail' ),
			'fields' => array(
				$text( 'sh_title_on_trail_page', 'Title on the trail page', 'e.g. Elephant’s Trail' ),
				$num( 'sh_order_home_icons', 'Order on the home page (1–17)' ),
				$img( 'sh_icon_home', 'Home icon (165×165)' ),
				$img( 'sh_icon_home_hover', 'Home icon — hover (185×185)' ),
				$bool( 'sh_show_in_zone_grid', 'Show in the zone page grid' ),
				$num( 'sh_zone_grid_order', 'Order in the zone page grid' ),
				$img( 'sh_tile_zone_grid', 'Zone grid tile (184×184)' ),
				$img( 'sh_tile_zone_grid_hover', 'Zone grid tile — hover (210×210)' ),
				$color( 'sh_tile_bg', 'Tile background' ),
				$num( 'sh_timeline_nav_order', 'Order in the timeline trail bar' ),
				$img( 'sh_icon_timeline', 'Timeline bar icon (35×35)' ),
				$color( 'sh_icon_group_color', 'Timeline icon group colour' ),
				$img( 'sh_chooser_image', 'Zone 4 "choose an animal" image', 'Transparent PNG of the animal on the Zone 4 page. It links to this trail\'s timeline page when one is set; otherwise it is not a link.' ),
				$img( 'sh_cover_image', 'Trail page cover (1120×497)' ),
				$page( 'sh_timeline_page', 'Timeline page', 'Empty = this trail has no timeline.' ),
				sh_f(
					'sh_object_order',
					'relationship',
					'Object order on the trail page',
					array(
						'instructions' => 'Drag to reorder. Objects of this trail that are not listed come after, A–Z.',
						'post_type'    => array( 'object' ),
						'filters'      => array( 'search' ),
						'return_format' => 'id',
					)
				),
			),
		),
		'zone'          => array(
			'title'  => 'Zone',
			'target' => array( 'taxonomy' => 'zone' ),
			'fields' => array(
				$num( 'sh_number', 'Zone number' ),
				$area( 'sh_card_title', 'Card title (with line break)', '', 2 ),
				$color( 'sh_color', 'Zone colour' ),
				$img( 'sh_card_image', 'Home card image' ),
				$img( 'sh_card_image_hover', 'Home card image — hover' ),
				$img( 'sh_cover_image', 'Zone page cover (1120×410)', 'Zone name is part of the image.' ),
				$img( 'sh_banner_image', 'Zone page banner (1120×166)' ),
				$bool( 'sh_has_page', 'Has a page' ),
				$page( 'sh_link_page', 'Page this zone links to', 'Empty = the zone page itself.' ),
				$area( 'sh_intro', 'Intro text', 'Zone page text under the banner (Zone 5: next to the cover). Paragraphs separated by a blank line.', 6 ),
				$area( 'sh_intro_full', 'Full text (after Read more)', 'Replaces the intro text when the visitor clicks Read more (Hide folds it back). Empty = no Read more. Paragraphs separated by a blank line.', 12 ),
				sh_tab( 'Zone page cards' ),
				$img( 'sh_page_card_1_image', 'Card 1 image (280×459)', 'Zone 5 page: four cards under the zone bar.' ),
				$img( 'sh_page_card_1_image_hover', 'Card 1 image — hover' ),
				$page( 'sh_page_card_1_link', 'Card 1 links to', 'Empty = not a link.' ),
				$bool( 'sh_page_card_1_soon', 'Card 1: show "Coming soon"', 'For a card whose page is not ready yet (no link).' ),
				$img( 'sh_page_card_2_image', 'Card 2 image (280×459)' ),
				$img( 'sh_page_card_2_image_hover', 'Card 2 image — hover' ),
				$page( 'sh_page_card_2_link', 'Card 2 links to', 'Empty = not a link.' ),
				$bool( 'sh_page_card_2_soon', 'Card 2: show "Coming soon"' ),
				$img( 'sh_page_card_3_image', 'Card 3 image (280×459)' ),
				$img( 'sh_page_card_3_image_hover', 'Card 3 image — hover' ),
				$page( 'sh_page_card_3_link', 'Card 3 links to', 'Empty = not a link.' ),
				$bool( 'sh_page_card_3_soon', 'Card 3: show "Coming soon"' ),
				$img( 'sh_page_card_4_image', 'Card 4 image (280×459)' ),
				$img( 'sh_page_card_4_image_hover', 'Card 4 image — hover' ),
				$page( 'sh_page_card_4_link', 'Card 4 links to', 'Empty = not a link.' ),
				$bool( 'sh_page_card_4_soon', 'Card 4: show "Coming soon"' ),
				$img( 'sh_page_strip_image_1', 'Image row 1 (280×123, under card 1)' ),
				$img( 'sh_page_strip_image_2', 'Image row 2 (under card 2)' ),
				$img( 'sh_page_strip_image_3', 'Image row 3 (under card 3)' ),
				$img( 'sh_page_strip_image_4', 'Image row 4 (under card 4)' ),
				$img( 'sh_page_history_image', 'History panel image (1120×453)' ),
				$img( 'sh_page_history_image_hover', 'History panel image — hover' ),
			),
		),
		'home'          => array(
			'title'  => 'Home page',
			'target' => array( 'page' => 'front' ),
			'fields' => array(
				sh_tab( 'Hero' ),
				$img( 'sh_home_hero_image', 'Hero image (1120×466)', 'The headline is part of the image.' ),
				sh_tab( 'Introduction' ),
				$text( 'sh_home_intro_title', 'Title' ),
				$area( 'sh_home_intro_paragraph_1', 'Paragraph 1', '', 3 ),
				$area( 'sh_home_intro_paragraph_2', 'Paragraph 2', '', 3 ),
				$img( 'sh_home_intro_image', 'Image (551×284)' ),
				$page( 'sh_home_intro_link', 'Read more → page' ),
				$img( 'sh_home_zone_bar_image', 'Zone bar image (1120×166)' ),
				sh_tab( 'Trails' ),
				$text( 'sh_home_trails_title', 'Title' ),
				$text( 'sh_home_trails_tagline', 'Tagline' ),
				$area( 'sh_home_trails_paragraph_1', 'Paragraph 1 (after Read more)', '', 4 ),
				$area( 'sh_home_trails_paragraph_2', 'Paragraph 2 (after Read more)', '', 4 ),
				$video( 'sh_home_trails_video_webm', 'Footprints animation (WebM)', 'webm' ),
				$img( 'sh_home_trails_video_poster', 'Footprints animation poster' ),
			),
		),
		'intro'         => array(
			'title'  => 'Introduction page',
			'target' => array( 'page' => 'intro' ),
			'fields' => array(
				sh_tab( 'Introduction' ),
				$img( 'sh_intro_cover_image', 'Cover (1120×466)' ),
				$text( 'sh_intro_title', 'Title' ),
				$area( 'sh_intro_paragraph_1', 'Paragraph 1', '', 3 ),
				$area( 'sh_intro_paragraph_2', 'Paragraph 2', '', 3 ),
				$area( 'sh_intro_body', 'Body', 'Paragraphs separated by a blank line.', 10 ),
				$img( 'sh_intro_image', 'Image (549×284)' ),
				sh_tab( 'Knowing Southeast Asia' ),
				$text( 'sh_intro_knowing_title', 'Title' ),
				$area( 'sh_intro_knowing_paragraph_1', 'Paragraph 1', '', 3 ),
				$area( 'sh_intro_knowing_paragraph_2', 'Paragraph 2', '', 3 ),
				$area( 'sh_intro_knowing_body', 'Body', 'Paragraphs separated by a blank line.', 10 ),
				$img( 'sh_intro_knowing_image', 'Image (549×284)' ),
				sh_tab( 'Map' ),
				$img( 'sh_intro_map_image', 'Map (1120×620)' ),
				$text( 'sh_intro_map_slide_1_title', 'Slide 1 title' ),
				$area( 'sh_intro_map_slide_1_body', 'Slide 1 text', 'Paragraphs separated by a blank line.', 6 ),
				$text( 'sh_intro_map_slide_2_title', 'Slide 2 title' ),
				$area( 'sh_intro_map_slide_2_body', 'Slide 2 text', 'Paragraphs separated by a blank line.', 6 ),
				$text( 'sh_intro_map_slide_3_title', 'Slide 3 title' ),
				$area( 'sh_intro_map_slide_3_body', 'Slide 3 text', 'Paragraphs separated by a blank line.', 6 ),
				$text( 'sh_intro_map_slide_4_title', 'Slide 4 title' ),
				$area( 'sh_intro_map_slide_4_body', 'Slide 4 text', 'Paragraphs separated by a blank line.', 6 ),
			),
		),
		'timeline'      => array(
			'title'  => 'Timeline page',
			'target' => array( 'page' => 'timeline' ),
			'fields' => array(
				$trail( 'sh_timeline_trail', 'Trail', 'Cards = timeline cards of this trail.' ),
				$img( 'sh_timeline_illustration', 'Timeline illustration (11000×791)', 'Names and era labels are part of the image.' ),
				$text( 'sh_timeline_reflection_title', 'Reflection title' ),
				$area( 'sh_timeline_reflection_body', 'Reflection text', 'Paragraphs separated by a blank line.', 10 ),
			),
		),
		'site'          => array(
			'title'  => 'Shared Heritage — site',
			'target' => array( 'option' => true ),
			'fields' => array(
				sh_f( 'sh_label_read_more', 'text', 'Label: Read more', array( 'default' => 'Read more >>' ) ),
				sh_f( 'sh_label_hide', 'text', 'Label: Hide (Read more when open)', array( 'default' => 'Hide >>' ) ),
				sh_f( 'sh_label_coming_soon', 'text', 'Label: Coming soon', array( 'default' => 'Coming soon' ) ),
				sh_f( 'sh_label_back', 'text', 'Label: Back', array( 'default' => 'Back' ) ),
				sh_f( 'sh_label_zone_prefix', 'text', 'Label: zone prefix', array( 'default' => 'ZONE' ) ),
				$img( 'sh_header_image', 'Header background (logos are part of the image)' ),
			),
		),
	);

	return $groups;
}

/**
 * Flat list of data fields (no tabs) of a group.
 *
 * @param string $group Group id.
 * @return array<int, array<string, mixed>>
 */
function sh_core_group_fields( string $group ): array {
	$groups = sh_core_field_groups();
	if ( ! isset( $groups[ $group ] ) ) {
		return array();
	}
	return array_values( array_filter( $groups[ $group ]['fields'], static fn( array $f ): bool => 'tab' !== $f['type'] ) );
}
