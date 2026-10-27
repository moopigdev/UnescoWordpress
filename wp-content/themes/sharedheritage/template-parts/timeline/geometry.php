<?php
/**
 * Timeline (Zone 4 · Figma 670:1551) — design geometry that has no field in the content contract yet.
 *
 * Values are copied from design/figma-file.json (node ids per entry), not estimated:
 *   - illustration: ROOM4 ele 2 `1232:1711` box 11000×791.481 + its image crop (imageTransform).
 *   - cards: keyed by timeline_item slug · card frame node · padding left/right · text column width
 *     (`col`) · title width (`hug` = Figma HUG, no wrap) · meta width · meta line-height: 24 (PIXELS) or 18
 *     for the 3 metas set to INTRINSIC_% (lineHeightPx reads 17.5 but Figma renders 18 per line:
 *     1232:1729 = 144 / 8 lines · 1232:1805 = 198 / 11 lines · 1232:1723 = 180 / 10)
 *     · media box (image/group) · each image: CPT field, position in the media box, Figma box size,
 *     source pixel size and imageTransform `t` = [a, b, tx, c, d, ty] (node → image UV) or `fit`.
 * The crop is only applied while the attachment still has the Figma source size (`src`); a replaced
 * image is shown whole (object-fit: contain) so an admin edit never shows a wrong crop.
 * Cards without an entry use the generic layout in timeline.css (DESIGN-SPEC §4.5).
 * Proposed move into the contract: docs/PENDING-DECISIONS.md P-140.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

return array(
	'illustration' => array( 'w' => 11000, 'h' => 791.481, 'src' => array( 4096, 285 ), 't' => array( 0.967019, 0, 0.006978, 0, 0.999995, 0.000002 ) ),
	'cards'        => array(
		'elephant-leg-bone-elephas-maximus-indicus' => array(
			'node' => '1232:1713', 'pad' => array( 100, 100 ), 'col' => 502, 'title' => 477, 'meta' => 502, 'lh' => 24,
			'media' => array( 'w' => 300, 'h' => 202 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 300, 'h' => 202, 'src' => array( 1920, 1080 ), 't' => array( 0.835429, 0, 0.08395, 0, 1, 0.000228 ) ),
			),
		),
		'singhasari-ganesha' => array(
			'node' => '1232:1719', 'pad' => array( 0, 0 ), 'col' => 549, 'title' => 549, 'meta' => 549, 'lh' => 18,
			'media' => array( 'w' => 300, 'h' => 279 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 300, 'h' => 279, 'src' => array( 4096, 2304 ), 't' => array( 0.605769, 0, 0.197115, 0, 1, -0.005917 ) ),
			),
		),
		'ganesha' => array(
			'node' => '1232:1725', 'pad' => array( 0, 0 ), 'col' => 476, 'title' => 476, 'meta' => 476, 'lh' => 18,
			'media' => array( 'w' => 180, 'h' => 187 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 187, 'src' => array( 4096, 2304 ), 't' => array( 0.54, 0, 0.23, 0, 0.995556, 0.002222 ) ),
			),
		),
		'elephant-molar-coconut-grater-dwarf-elephant-bone' => array(
			'node' => '1232:1731', 'pad' => array( 0, 0 ), 'col' => 477, 'title' => 477, 'meta' => 477, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 95 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 95, 'src' => array( 1920, 1080 ), 't' => array( 0.54, 0, 0.235, 0, 0.506667, 0.242222 ) ),
			),
		),
		'embossed-brass-basin-with-mythical-animal-motifs' => array(
			'node' => '1232:1737', 'pad' => array( 0, 0 ), 'col' => 505, 'title' => 477, 'meta' => 505, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 129 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 129, 'src' => array( 1344, 961 ), 't' => array( 1.353845, 0, -0.175998, 0, 1.353788, -0.173303 ) ),
			),
		),
		'ivory-fire-piston-ivory-fire-syringe' => array(
			'node' => '1232:1743', 'pad' => array( 0, 0 ), 'col' => 605, 'title' => 477, 'meta' => 605, 'lh' => 24,
			'media' => array( 'w' => 174, 'h' => 152 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 122.585, 'h' => 151.652, 'src' => array( 4096, 3084 ), 't' => array( 0.404564, 0, 0.345031, 0, 0.664726, 0.296199 ) ),
				array( 'field' => 'sh_thumbnail_2', 'x' => 70.585, 'y' => 6.366, 'w' => 103.415, 'h' => 145.634, 'src' => array( 4096, 3084 ), 't' => array( 0.373892, 0, 0.327632, 0, 0.699317, 0.150342 ) ),
			),
		),
		'elephant-and-mahout-kendi' => array(
			'node' => '1232:1751', 'pad' => array( 0, 0 ), 'col' => 549, 'title' => 549, 'meta' => 462, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 168 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 168, 'src' => array( 4096, 2731 ), 'fit' => 'cover' ),
			),
		),
		'patola-cloth-cindai' => array(
			'node' => '1232:1757', 'pad' => array( 0, 0 ), 'col' => 297, 'title' => 'hug', 'meta' => 297, 'lh' => 24,
			'media' => array( 'w' => 200, 'h' => 188 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 200, 'h' => 188, 'src' => array( 625, 589 ), 't' => array( 1.189183, 0, -0.095297, 0, 1.186155, -0.09443 ) ),
			),
		),
		'elephant-shaped-relic-containers' => array(
			'node' => '1232:1763', 'pad' => array( 0, 0 ), 'col' => 477, 'title' => 477, 'meta' => 452, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 150 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 28.595, 'y' => 35.377, 'w' => 151.406, 'h' => 114.623, 'src' => array( 1381, 922 ), 't' => array( 0.50178, 0, 0.215965, 0, 0.568994, 0.216643 ) ),
				array( 'field' => 'sh_thumbnail_2', 'x' => 0, 'y' => 0, 'w' => 110.346, 'h' => 101.792, 'src' => array( 1381, 922 ), 't' => array( 0.353672, 0, 0.15538, 0, 0.489154, 0.21692 ) ),
			),
		),
		'pod-duang-bullet-money-stamped-with-the-chakra-and-great-crown-marks-reign-of-king-rama-iv' => array(
			'node' => '1232:1771', 'pad' => array( 0, 0 ), 'col' => 477, 'title' => 477, 'meta' => 472, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 180 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 180, 'src' => array( 227, 227 ), 't' => array( 1.56761, 0, -0.283204, 0, 1.56761, -0.285023 ) ),
			),
		),
		'hand-carved-ivory-and-metal-opium-pipe' => array(
			'node' => '1232:1777', 'pad' => array( 0, 0 ), 'col' => 549, 'title' => 477, 'meta' => 549, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 107 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 107, 'src' => array( 2098, 411 ), 't' => array( 1.025547, -0.167306, 0.068817, 1.442667, 3.099051, -1.77993 ) ),
			),
		),
		'tin-ingot-in-the-shape-of-elephant' => array(
			'node' => '1232:1783', 'pad' => array( 0, 0 ), 'col' => 377, 'title' => 'hug', 'meta' => 377, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 128 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 128, 'src' => array( 1920, 1080 ), 't' => array( 0.79, 0, 0.11, 0, 0.995556, 0.002222 ) ),
			),
		),
		'wooden-cold-press-cake-molds-with-elephanthorse-and-fowl-motifs' => array(
			'node' => '1232:1789', 'pad' => array( 0, 0 ), 'col' => 477, 'title' => 477, 'meta' => 440, 'lh' => 24,
			'media' => array( 'w' => 180, 'h' => 82 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 82, 'src' => array( 1920, 1080 ), 't' => array( 0.626087, 0, 0.233424, 0, 0.504348, 0.269565 ) ),
			),
		),
		'siwaih' => array(
			'node' => '1232:1795', 'pad' => array( 0, 100 ), 'col' => 477, 'title' => 477, 'meta' => 255, 'lh' => 24,
			'media' => array( 'w' => 200, 'h' => 112 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 200, 'h' => 112, 'src' => array( 1920, 1080 ), 't' => array( 0.601878, -0.083596, 0.253519, 0.265383, 0.599203, 0.071911 ) ),
			),
		),
		'procession-of-a-king-or-noble-relief-from-the-life-of-the-buddha' => array(
			'node' => '1232:1801', 'pad' => array( 0, 0 ), 'col' => 475, 'title' => 475, 'meta' => 475, 'lh' => 18,
			'media' => array( 'w' => 180, 'h' => 56 ),
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'x' => 0, 'y' => 0, 'w' => 180, 'h' => 56, 'src' => array( 4096, 1270 ), 'fit' => 'cover' ),
			),
		),
	),
);
