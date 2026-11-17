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
 * Number of numbered tile slots of the Zone 1–3 object grid (Figma: 15 · 42 · 83 tiles; room to add).
 */
const SH_ZONE_GRID_SLOTS = 90;

/**
 * Fields of the "zone_grid" group: per slot n an object, the tile image (184×184) and its hover image (210×210),
 * grouped in tabs of 10 slots. Image empty = the object's own tile (sh_tile_image / sh_tile_image_hover).
 *
 * @param callable $img Image field builder of sh_core_field_groups().
 * @return array<int, array<string, mixed>>
 */
function sh_core_zone_grid_fields( callable $img ): array {
	$fields = array();
	for ( $n = 1; $n <= SH_ZONE_GRID_SLOTS; $n++ ) {
		if ( 1 === $n % 10 ) {
			$last     = min( $n + 9, SH_ZONE_GRID_SLOTS );
			$fields[] = sh_tab( sprintf( 'Tiles %d–%d', $n, $last ) );
		}
		$fields[] = sh_f(
			"sh_zone_tile_{$n}_object",
			'post_object',
			"Tile {$n}: object",
			array(
				'instructions' => 1 === $n ? 'The tile links to this object\'s page (published objects only). Empty = no tile in this slot, or a picture without a link when the image below is set. Slots are shown in this order (1 = first, 5 per row).' : '',
				'post_type'    => array( 'object' ),
				'allow_null'   => 1,
			)
		);
		$fields[] = $img( "sh_zone_tile_{$n}_image", "Tile {$n}: image (184×184)", 1 === $n ? 'Empty = the object\'s own tile image.' : '' );
		$fields[] = $img( "sh_zone_tile_{$n}_image_hover", "Tile {$n}: image — hover (210×210)", 1 === $n ? 'Empty = the object\'s own hover image.' : '' );
	}
	return $fields;
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
	$rich  = static fn( string $name, string $label, string $help = '', int $rows = 6 ): array => sh_f(
		$name,
		'textarea',
		$label,
		array(
			'instructions' => trim( $help . ' Paragraphs separated by a blank line · a single line break stays a line break · <strong>bold</strong> and <em>italic</em> are allowed.' ),
			'rows'         => $rows,
			'format'       => 'inline_html',
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
				$area( 'sh_collection', 'Collection', 'Several collections (e.g. two specimens): one per line.', 2 ),
				$area( 'sh_scientific_name', 'Scientific name / Taxonomy', 'Natural specimens only. Several specimens: one per line.', 2 ),
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
				sh_tab( 'Tiles in other trails' ),
				$trail( 'sh_trail_tile_1_trail', 'Other trail 1', 'An object in several trails can have its own tile in another trail (the tile background colour is part of the image). Empty = that trail uses the tiles above.' ),
				$img( 'sh_trail_tile_1_image', 'Other trail 1: tile (184×184)' ),
				$img( 'sh_trail_tile_1_image_hover', 'Other trail 1: tile — hover (210×210)' ),
				$trail( 'sh_trail_tile_2_trail', 'Other trail 2' ),
				$img( 'sh_trail_tile_2_image', 'Other trail 2: tile (184×184)' ),
				$img( 'sh_trail_tile_2_image_hover', 'Other trail 2: tile — hover (210×210)' ),
				$trail( 'sh_trail_tile_3_trail', 'Other trail 3' ),
				$img( 'sh_trail_tile_3_image', 'Other trail 3: tile (184×184)' ),
				$img( 'sh_trail_tile_3_image_hover', 'Other trail 3: tile — hover (210×210)' ),
				$trail( 'sh_trail_tile_4_trail', 'Other trail 4' ),
				$img( 'sh_trail_tile_4_image', 'Other trail 4: tile (184×184)' ),
				$img( 'sh_trail_tile_4_image_hover', 'Other trail 4: tile — hover (210×210)' ),
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
				$img( 'sh_below_image_2', 'Photo under the line 2 (optional)', 'Timeline: a second photo shown beside the photo under the line (two pieces shown as one group). Used only together with "Photo under the line".' ),
				$img( 'sh_line_art', 'Before image (optional)', 'Timeline: shown in the large photo under the line while this object moves to the centre, then it cross-fades to the card image. Often a line drawing (transparent PNG); any image works. If empty, the card image is shown as before.' ),
			),
		),
		'trail'         => array(
			'title'  => 'Trail',
			'target' => array( 'taxonomy' => 'trail' ),
			'fields' => array(
				sh_tab( 'Trail' ),
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
				sh_tab( 'Tiles without a page' ),
				$num( 'sh_static_tile_1_position', 'Tile without a page 1: position in the grid', 'For a tile that has no object page yet (shown, not a link). 1 = first tile. Empty/0 = not shown. Remove it when the object gets its page.' ),
				$img( 'sh_static_tile_1_image', 'Tile without a page 1: image (184×184)' ),
				$img( 'sh_static_tile_1_image_hover', 'Tile without a page 1: hover (210×210)' ),
				$text( 'sh_static_tile_1_label', 'Tile without a page 1: name (image text for screen readers)' ),
				$num( 'sh_static_tile_2_position', 'Tile without a page 2: position in the grid' ),
				$img( 'sh_static_tile_2_image', 'Tile without a page 2: image (184×184)' ),
				$img( 'sh_static_tile_2_image_hover', 'Tile without a page 2: hover (210×210)' ),
				$text( 'sh_static_tile_2_label', 'Tile without a page 2: name' ),
				$num( 'sh_static_tile_3_position', 'Tile without a page 3: position in the grid' ),
				$img( 'sh_static_tile_3_image', 'Tile without a page 3: image (184×184)' ),
				$img( 'sh_static_tile_3_image_hover', 'Tile without a page 3: hover (210×210)' ),
				$text( 'sh_static_tile_3_label', 'Tile without a page 3: name' ),
				sh_tab( 'Animal guide card' ),
				$text( 'sh_guide_name', 'Card name', 'Home page "Your animal guide is...", e.g. The Chicken & Rooster.' ),
				$text( 'sh_guide_words', 'Three words', 'e.g. Renewal · Homeliness · Abundance' ),
				$rich( 'sh_guide_body', 'Card text (after Read more)', '', 12 ),
				$text( 'sh_guide_follow', 'Follow link text', 'e.g. → FOLLOW THE CHICKEN & ROOSTER TRAIL (links to this trail).' ),
				$img( 'sh_guide_card', 'Card image (230×363)', 'Empty = this animal is not drawn on the home page.' ),
				sh_tab( 'Layout' ),
				sh_f(
					'sh_trail_layout',
					'textarea',
					'Trail page layout (JSON from Figma)',
					array(
						'instructions' => 'Geometry of the Figma trail page (cover, title, description and Back boxes). Must be a JSON object.',
						'rows'         => 6,
						'format'       => 'json',
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
		// D57 (fb-1117): the Zone 1–3 pages show a grid of OBJECT tiles (not trail tiles). Numbered slots: slot n = n-th tile
		// (order of the grid, 1 = first). A slot with an object links to its page; with no object it is a picture only.
		'zone_grid'     => array(
			'title'  => 'Zone object grid',
			'target' => array( 'taxonomy' => 'zone' ),
			'fields' => sh_core_zone_grid_fields( $img ),
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
				sh_tab( 'Animal guide' ),
				$text( 'sh_home_guide_title', 'Title', 'e.g. Your animal guide is...' ),
				$area( 'sh_home_guide_opening', 'Paragraph 1', '', 2 ),
				$area( 'sh_home_guide_heritage', 'Paragraph 2', '', 3 ),
				$area( 'sh_home_guide_question', 'Paragraph 3', '', 2 ),
				$area( 'sh_home_guide_instructions', 'Paragraph 4', '', 2 ),
				$text( 'sh_home_guide_cta', 'Button text', 'e.g. CHOOSE A CARD (picks a random card).' ),
				$img( 'sh_home_guide_card_back', 'Card back "?" (120×190)', 'The 17 cards themselves are on each trail (Trails › edit › Animal guide card).' ),
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
				sh_tab( 'Introduction 2026: header' ),
				$img( 'sh_intro_head_emblem', 'Emblem (325×320)' ),
				$area( 'sh_intro_head_title', 'Headline', 'A line break here is a line break on the page.', 2 ),
				$area( 'sh_intro_head_paragraph_1', 'Paragraph 1', '', 4 ),
				$area( 'sh_intro_head_paragraph_2', 'Paragraph 2', '', 4 ),
				$area( 'sh_intro_head_paragraph_3', 'Paragraph 3', '', 4 ),
				$area( 'sh_intro_head_paragraph_4', 'Paragraph 4', '', 4 ),
				$area( 'sh_intro_head_paragraph_5', 'Paragraph 5', '', 4 ),
				$area( 'sh_intro_head_credit', 'Credit line', '', 2 ),
				$area( 'sh_intro_env_heading', 'Heading above the map', 'A line break here is a line break on the page.', 2 ),
				sh_f(
					'sh_intro_head_layout',
					'textarea',
					'Header and map layout (JSON from Figma)',
					array(
						'instructions' => 'Geometry, fonts and colours of the orange header, the heading and the map buttons. Must be a JSON object.',
						'rows'         => 6,
						'format'       => 'json',
					)
				),
				sh_tab( 'Introduction 2026: chapter 1' ),
				$text( 'sh_intro_ch1_title', 'Chapter 1: title' ),
				$color( 'sh_intro_ch1_accent', 'Chapter 1: accent colour' ),
				$img( 'sh_intro_ch1_map_image', 'Chapter 1: map (1440×808)', 'Still image (also shown where the animation cannot play).' ),
				$img( 'sh_intro_ch1_map_animation', 'Chapter 1: map animation (optional)', 'Animated WebP/GIF shown instead of the still map.' ),
				$rich( 'sh_intro_ch1_text_1', 'Chapter 1: text 1', '', 6 ),
				$rich( 'sh_intro_ch1_text_2', 'Chapter 1: text 2', '', 6 ),
				$rich( 'sh_intro_ch1_text_3', 'Chapter 1: text 3', '', 6 ),
				$rich( 'sh_intro_ch1_text_4', 'Chapter 1: text 4', '', 6 ),
				$rich( 'sh_intro_ch1_text_5', 'Chapter 1: text 5', '', 6 ),
				$rich( 'sh_intro_ch1_text_6', 'Chapter 1: text 6', '', 6 ),
				$rich( 'sh_intro_ch1_text_7', 'Chapter 1: text 7', '', 6 ),
				$img( 'sh_intro_ch1_image_1', 'Chapter 1: image 1' ),
				$img( 'sh_intro_ch1_image_1_animation', 'Chapter 1: image 1 animation (optional)', 'Animated WebP/GIF shown instead of image 1.' ),
				$img( 'sh_intro_ch1_image_2', 'Chapter 1: image 2' ),
				$img( 'sh_intro_ch1_image_2_animation', 'Chapter 1: image 2 animation (optional)', 'Animated WebP/GIF shown instead of image 2.' ),
				$img( 'sh_intro_ch1_image_3', 'Chapter 1: image 3' ),
				$img( 'sh_intro_ch1_image_3_animation', 'Chapter 1: image 3 animation (optional)', 'Animated WebP/GIF shown instead of image 3.' ),
				$img( 'sh_intro_ch1_image_4', 'Chapter 1: image 4' ),
				$img( 'sh_intro_ch1_image_4_animation', 'Chapter 1: image 4 animation (optional)', 'Animated WebP/GIF shown instead of image 4.' ),
				$img( 'sh_intro_ch1_image_5', 'Chapter 1: image 5' ),
				$img( 'sh_intro_ch1_image_5_animation', 'Chapter 1: image 5 animation (optional)', 'Animated WebP/GIF shown instead of image 5.' ),
				sh_f(
					'sh_intro_ch1_layout',
					'textarea',
					'Chapter 1: layout (JSON from Figma)',
					array(
						'instructions' => 'Where each text / image of this chapter sits (sections, background colours, boxes, crops, fonts). Texts and images are referred to by number ("slot"). Must be a JSON object.',
						'rows'         => 6,
						'format'       => 'json',
					)
				),
				sh_tab( 'Introduction 2026: chapter 2' ),
				$text( 'sh_intro_ch2_title', 'Chapter 2: title' ),
				$color( 'sh_intro_ch2_accent', 'Chapter 2: accent colour' ),
				$img( 'sh_intro_ch2_map_image', 'Chapter 2: map (1440×808)', 'Still image (also shown where the animation cannot play).' ),
				$img( 'sh_intro_ch2_map_animation', 'Chapter 2: map animation (optional)', 'Animated WebP/GIF shown instead of the still map.' ),
				$rich( 'sh_intro_ch2_text_1', 'Chapter 2: text 1', '', 6 ),
				$rich( 'sh_intro_ch2_text_2', 'Chapter 2: text 2', '', 6 ),
				$rich( 'sh_intro_ch2_text_3', 'Chapter 2: text 3', '', 6 ),
				$rich( 'sh_intro_ch2_text_4', 'Chapter 2: text 4', '', 6 ),
				$rich( 'sh_intro_ch2_text_5', 'Chapter 2: text 5', '', 6 ),
				$rich( 'sh_intro_ch2_text_6', 'Chapter 2: text 6', '', 6 ),
				$rich( 'sh_intro_ch2_text_7', 'Chapter 2: text 7', '', 6 ),
				$img( 'sh_intro_ch2_image_1', 'Chapter 2: image 1' ),
				$img( 'sh_intro_ch2_image_1_animation', 'Chapter 2: image 1 animation (optional)', 'Animated WebP/GIF shown instead of image 1.' ),
				$img( 'sh_intro_ch2_image_2', 'Chapter 2: image 2' ),
				$img( 'sh_intro_ch2_image_2_animation', 'Chapter 2: image 2 animation (optional)', 'Animated WebP/GIF shown instead of image 2.' ),
				$img( 'sh_intro_ch2_image_3', 'Chapter 2: image 3' ),
				$img( 'sh_intro_ch2_image_3_animation', 'Chapter 2: image 3 animation (optional)', 'Animated WebP/GIF shown instead of image 3.' ),
				$img( 'sh_intro_ch2_image_4', 'Chapter 2: image 4' ),
				$img( 'sh_intro_ch2_image_4_animation', 'Chapter 2: image 4 animation (optional)', 'Animated WebP/GIF shown instead of image 4.' ),
				$img( 'sh_intro_ch2_image_5', 'Chapter 2: image 5' ),
				$img( 'sh_intro_ch2_image_5_animation', 'Chapter 2: image 5 animation (optional)', 'Animated WebP/GIF shown instead of image 5.' ),
				sh_f(
					'sh_intro_ch2_layout',
					'textarea',
					'Chapter 2: layout (JSON from Figma)',
					array(
						'instructions' => 'Where each text / image of this chapter sits (sections, background colours, boxes, crops, fonts). Texts and images are referred to by number ("slot"). Must be a JSON object.',
						'rows'         => 6,
						'format'       => 'json',
					)
				),
				sh_tab( 'Introduction 2026: chapter 3' ),
				$text( 'sh_intro_ch3_title', 'Chapter 3: title' ),
				$color( 'sh_intro_ch3_accent', 'Chapter 3: accent colour' ),
				$img( 'sh_intro_ch3_map_image', 'Chapter 3: map (1440×808)', 'Still image (also shown where the animation cannot play).' ),
				$img( 'sh_intro_ch3_map_animation', 'Chapter 3: map animation (optional)', 'Animated WebP/GIF shown instead of the still map.' ),
				$rich( 'sh_intro_ch3_text_1', 'Chapter 3: text 1', '', 6 ),
				$rich( 'sh_intro_ch3_text_2', 'Chapter 3: text 2', '', 6 ),
				$rich( 'sh_intro_ch3_text_3', 'Chapter 3: text 3', '', 6 ),
				$rich( 'sh_intro_ch3_text_4', 'Chapter 3: text 4', '', 6 ),
				$rich( 'sh_intro_ch3_text_5', 'Chapter 3: text 5', '', 6 ),
				$rich( 'sh_intro_ch3_text_6', 'Chapter 3: text 6', '', 6 ),
				$rich( 'sh_intro_ch3_text_7', 'Chapter 3: text 7', '', 6 ),
				$img( 'sh_intro_ch3_image_1', 'Chapter 3: image 1' ),
				$img( 'sh_intro_ch3_image_1_animation', 'Chapter 3: image 1 animation (optional)', 'Animated WebP/GIF shown instead of image 1.' ),
				$img( 'sh_intro_ch3_image_2', 'Chapter 3: image 2' ),
				$img( 'sh_intro_ch3_image_2_animation', 'Chapter 3: image 2 animation (optional)', 'Animated WebP/GIF shown instead of image 2.' ),
				$img( 'sh_intro_ch3_image_3', 'Chapter 3: image 3' ),
				$img( 'sh_intro_ch3_image_3_animation', 'Chapter 3: image 3 animation (optional)', 'Animated WebP/GIF shown instead of image 3.' ),
				$img( 'sh_intro_ch3_image_4', 'Chapter 3: image 4' ),
				$img( 'sh_intro_ch3_image_4_animation', 'Chapter 3: image 4 animation (optional)', 'Animated WebP/GIF shown instead of image 4.' ),
				$img( 'sh_intro_ch3_image_5', 'Chapter 3: image 5' ),
				$img( 'sh_intro_ch3_image_5_animation', 'Chapter 3: image 5 animation (optional)', 'Animated WebP/GIF shown instead of image 5.' ),
				sh_f(
					'sh_intro_ch3_layout',
					'textarea',
					'Chapter 3: layout (JSON from Figma)',
					array(
						'instructions' => 'Where each text / image of this chapter sits (sections, background colours, boxes, crops, fonts). Texts and images are referred to by number ("slot"). Must be a JSON object.',
						'rows'         => 6,
						'format'       => 'json',
					)
				),
				sh_tab( 'Introduction 2026: chapter 4' ),
				$text( 'sh_intro_ch4_title', 'Chapter 4: title' ),
				$color( 'sh_intro_ch4_accent', 'Chapter 4: accent colour' ),
				$img( 'sh_intro_ch4_map_image', 'Chapter 4: map (1440×808)', 'Still image (also shown where the animation cannot play).' ),
				$img( 'sh_intro_ch4_map_animation', 'Chapter 4: map animation (optional)', 'Animated WebP/GIF shown instead of the still map.' ),
				$rich( 'sh_intro_ch4_text_1', 'Chapter 4: text 1', '', 6 ),
				$rich( 'sh_intro_ch4_text_2', 'Chapter 4: text 2', '', 6 ),
				$rich( 'sh_intro_ch4_text_3', 'Chapter 4: text 3', '', 6 ),
				$rich( 'sh_intro_ch4_text_4', 'Chapter 4: text 4', '', 6 ),
				$rich( 'sh_intro_ch4_text_5', 'Chapter 4: text 5', '', 6 ),
				$rich( 'sh_intro_ch4_text_6', 'Chapter 4: text 6', '', 6 ),
				$rich( 'sh_intro_ch4_text_7', 'Chapter 4: text 7', '', 6 ),
				$img( 'sh_intro_ch4_image_1', 'Chapter 4: image 1' ),
				$img( 'sh_intro_ch4_image_1_animation', 'Chapter 4: image 1 animation (optional)', 'Animated WebP/GIF shown instead of image 1.' ),
				$img( 'sh_intro_ch4_image_2', 'Chapter 4: image 2' ),
				$img( 'sh_intro_ch4_image_2_animation', 'Chapter 4: image 2 animation (optional)', 'Animated WebP/GIF shown instead of image 2.' ),
				$img( 'sh_intro_ch4_image_3', 'Chapter 4: image 3' ),
				$img( 'sh_intro_ch4_image_3_animation', 'Chapter 4: image 3 animation (optional)', 'Animated WebP/GIF shown instead of image 3.' ),
				$img( 'sh_intro_ch4_image_4', 'Chapter 4: image 4' ),
				$img( 'sh_intro_ch4_image_4_animation', 'Chapter 4: image 4 animation (optional)', 'Animated WebP/GIF shown instead of image 4.' ),
				$img( 'sh_intro_ch4_image_5', 'Chapter 4: image 5' ),
				$img( 'sh_intro_ch4_image_5_animation', 'Chapter 4: image 5 animation (optional)', 'Animated WebP/GIF shown instead of image 5.' ),
				sh_f(
					'sh_intro_ch4_layout',
					'textarea',
					'Chapter 4: layout (JSON from Figma)',
					array(
						'instructions' => 'Where each text / image of this chapter sits (sections, background colours, boxes, crops, fonts). Texts and images are referred to by number ("slot"). Must be a JSON object.',
						'rows'         => 6,
						'format'       => 'json',
					)
				),
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
				$img( 'sh_logo_header', 'Header logo (Shared Heritage · 220×77)', 'Top left of every page (D47).' ),
				$img( 'sh_logo_unesco_footer', 'Footer logo: UNESCO (283×60)', 'Bottom of every page. 0 = the UNESCO logo built into the theme (SVG).' ),
				$img( 'sh_logo_temasek', 'Footer logo: Temasek Foundation (208×60)', 'Bottom of every page.' ),
				sh_f( 'sh_footer_support_text', 'text', 'Footer: text above the Temasek logo', array( 'default' => 'With the Support of' ) ),
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
