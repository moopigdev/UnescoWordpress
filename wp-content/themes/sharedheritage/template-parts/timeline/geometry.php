<?php
/**
 * Timeline v3 (D26 · D27) — design constants that are layout, not content (DESIGN-SPEC §4.5c · §6.4c).
 *
 * Source: content/seed/timeline-elephant-v2.json key `v3` (read only) + DESIGN-SPEC §4.5c.3 table.
 * Content (title · images · metadata · link) comes from the timeline_item fields only (CONTENT-MODEL §3).
 *
 * Per timeline_item slug:
 *   rest  = [w, h] object on the line when not centred (px at 1440) · scale = active ÷ rest (uniform, no stretch)
 *   photo = [w, h] photo under the line (≤ 340×324 and ≤ the file's trimmed pixels — Pod Duang 226)
 *   mini  = [w, h] mini-map thumbnail (≤ 48×38)
 *   ratio = w ÷ h of the visible group (trimmed bbox, or the 2-image group)
 *   images = each image of the group: field · src [file w, h] · crop [x0, y0, x1, y1] in file px (transparent
 *            margin trimmed — seed items[].thumbnails[].opaque_bbox_px; card 9 = Figma crop of Group 33) ·
 *            box [x, y, w, h] as fractions of the group box (later image = drawn on top).
 * A crop is used only while the attachment is still that file (same pixel size, or WordPress' "-scaled" copy
 * with the same aspect ratio); otherwise the item falls back to the rule (helpers.php sh_tl_rule_sizes()).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_full = static function ( string $field, array $src, array $crop ): array {
	return array( 'field' => $field, 'src' => $src, 'crop' => $crop, 'box' => array( 0, 0, 1, 1 ) );
};

return array(
	// §4.5c.3 rule for items without an entry (or with a replaced image): "contain" in these boxes.
	'rule'  => array(
		'rest'        => array( 163, 180 ),
		'active'      => array( 247, 250 ),
		'wide_rest'   => array( 200, 180 ),
		'wide_active' => array( 304, 250 ),
		'wide_ratio'  => 3,
		'photo'       => array( 340, 324 ),
		'mini'        => array( 48, 38 ),
	),
	'items' => array(
		'elephant-leg-bone-elephas-maximus-indicus' => array(
			'rest' => array( 163, 86 ), 'scale' => 1.515, 'photo' => array( 340, 180 ), 'mini' => array( 48, 25 ), 'ratio' => 1360 / 720,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 276, 188, 1636, 908 ) ) ),
		),
		'singhasari-ganesha' => array(
			'rest' => array( 126, 178 ), 'scale' => 1.413, 'photo' => array( 230, 324 ), 'mini' => array( 27, 38 ), 'ratio' => 1340 / 1885,
			'images' => array( $sh_full( 'sh_thumbnail', array( 4096, 2304 ), array( 1378, 179, 2718, 2064 ) ) ),
		),
		'ganesha' => array(
			'rest' => array( 149, 180 ), 'scale' => 1.342, 'photo' => array( 270, 324 ), 'mini' => array( 32, 38 ), 'ratio' => 1563 / 1878,
			'images' => array( $sh_full( 'sh_thumbnail', array( 4096, 2304 ), array( 1267, 179, 2830, 2057 ) ) ),
		),
		'elephant-molar-coconut-grater-dwarf-elephant-bone' => array(
			'rest' => array( 163, 81 ), 'scale' => 1.515, 'photo' => array( 340, 170 ), 'mini' => array( 48, 24 ), 'ratio' => 767 / 383,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 577, 348, 1344, 731 ) ) ),
		),
		'embossed-brass-basin-with-mythical-animal-motifs' => array(
			'rest' => array( 163, 113 ), 'scale' => 1.515, 'photo' => array( 340, 243 ), 'mini' => array( 48, 34 ), 'ratio' => 1343 / 960,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1344, 961 ), array( 1, 1, 1344, 961 ) ) ),
		),
		// Slides 47–51: the two pieces at equal height side by side, overlapping ≈6 px at 163 high (group 107×163).
		// Height 1: bottle 533/1430 = 0.37273 · piston 503/1586 = 0.31715 · overlap 6/163 = 0.03681 → width 0.65307.
		'ivory-fire-piston-ivory-fire-syringe' => array(
			'rest' => array( 107, 163 ), 'scale' => 1.523, 'photo' => array( 213, 324 ), 'mini' => array( 44, 38 ), 'ratio' => 0.65307,
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'src' => array( 4096, 3084 ), 'crop' => array( 1782, 1283, 2315, 2713 ), 'box' => array( 0, 0, 0.57074, 1 ) ),
				array( 'field' => 'sh_thumbnail_2', 'src' => array( 4096, 3084 ), 'crop' => array( 1813, 749, 2316, 2335 ), 'box' => array( 0.51437, 0, 0.48563, 1 ) ),
			),
		),
		'elephant-and-mahout-kendi' => array(
			'rest' => array( 114, 180 ), 'scale' => 1.395, 'photo' => array( 206, 324 ), 'mini' => array( 24, 38 ), 'ratio' => 1401 / 2203,
			'images' => array( $sh_full( 'sh_thumbnail', array( 4096, 2731 ), array( 1266, 279, 2667, 2482 ) ) ),
		),
		'patola-cloth-cindai' => array(
			'rest' => array( 163, 155 ), 'scale' => 1.515, 'photo' => array( 340, 323 ), 'mini' => array( 40, 38 ), 'ratio' => 611 / 580,
			'images' => array( $sh_full( 'sh_thumbnail', array( 625, 589 ), array( 13, 1, 624, 581 ) ) ),
		),
		// Not in Slides → Figma Group 33 `1232:1764` 180×150 (§4.5c.3): 1765 [28.6,35.4 151.4×114.6] · 1766 [0,0 110.3×101.8]
		// on top; crops = the nodes' imageTransform (UV × file px).
		'elephant-shaped-relic-containers' => array(
			'rest' => array( 163, 136 ), 'scale' => 1.515, 'photo' => array( 340, 283 ), 'mini' => array( 46, 38 ), 'ratio' => 180 / 150,
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'src' => array( 1381, 922 ), 'crop' => array( 298.25, 199.74, 991.2, 724.36 ), 'box' => array( 28.595 / 180, 35.377 / 150, 151.406 / 180, 114.623 / 150 ) ),
				array( 'field' => 'sh_thumbnail_2', 'src' => array( 1381, 922 ), 'crop' => array( 214.58, 200.0, 703.0, 651.0 ), 'box' => array( 0, 0, 110.346 / 180, 101.792 / 150 ) ),
			),
		),
		// Photo capped at the file (226 px, §4.5c.3 "ห้ามขยายเกินพิกเซลไฟล์").
		'pod-duang-bullet-money-stamped-with-the-chakra-and-great-crown-marks-reign-of-king-rama-iv' => array(
			'rest' => array( 163, 163 ), 'scale' => 1.515, 'photo' => array( 226, 226 ), 'mini' => array( 38, 38 ), 'ratio' => 1,
			'images' => array( $sh_full( 'sh_thumbnail', array( 227, 227 ), array( 0, 1, 226, 227 ) ) ),
		),
		'hand-carved-ivory-and-metal-opium-pipe' => array(
			'rest' => array( 200, 27 ), 'scale' => 1.52, 'photo' => array( 340, 46 ), 'mini' => array( 48, 6 ), 'ratio' => 2017 / 270,
			'images' => array( $sh_full( 'sh_thumbnail', array( 2098, 411 ), array( 47, 54, 2064, 324 ) ) ),
		),
		'tin-ingot-in-the-shape-of-elephant' => array(
			'rest' => array( 163, 109 ), 'scale' => 1.515, 'photo' => array( 340, 227 ), 'mini' => array( 48, 32 ), 'ratio' => 954 / 637,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 483, 221, 1437, 858 ) ) ),
		),
		'wooden-cold-press-cake-molds-with-elephanthorse-and-fowl-motifs' => array(
			'rest' => array( 200, 61 ), 'scale' => 1.52, 'photo' => array( 340, 104 ), 'mini' => array( 48, 15 ), 'ratio' => 936 / 287,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 598, 397, 1534, 684 ) ) ),
		),
		'siwaih' => array(
			'rest' => array( 200, 51 ), 'scale' => 1.52, 'photo' => array( 340, 87 ), 'mini' => array( 48, 12 ), 'ratio' => 981 / 252,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 478, 437, 1459, 689 ) ) ),
		),
		'procession-of-a-king-or-noble-relief-from-the-life-of-the-buddha' => array(
			'rest' => array( 200, 62 ), 'scale' => 1.52, 'photo' => array( 340, 105 ), 'mini' => array( 48, 15 ), 'ratio' => 4096 / 1270,
			'images' => array( $sh_full( 'sh_thumbnail', array( 4096, 1270 ), array( 0, 0, 4096, 1270 ) ) ),
		),
	),
);
