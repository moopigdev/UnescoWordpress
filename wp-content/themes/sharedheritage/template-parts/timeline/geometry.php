<?php
/**
 * Timeline v3 (D26 · D27) — design constants that are layout, not content (DESIGN-SPEC §4.5c · §6.4c).
 *
 * Source: content/seed/timeline-elephant-v2.json key `v3` (read only · D39 `v3.d39_images`) + DESIGN-SPEC §4.5c.3 table
 * (#14 sizes 30/60/100) + §4.5c.3d (D39/D41 images on/under the line).
 * Content (title · images · metadata · link) comes from the timeline_item fields only (CONTENT-MODEL §3).
 *
 * `items` is keyed by the stable card number N (1–15) = the number in the import key `<key_prefix><N>` (post meta
 * _sh_import_key, helpers.php sh_tl_geo_entry()) — not by slug or title, which an admin or a heading change (D31) can alter.
 * The comment after each key is the original post slug, for reference only. Per item:
 *   photo = [w, h] the on-line image contained in 320×265 (≤ the file's trimmed pixels — Pod Duang 226) = 100% of the
 *           size rule #14: the object on the line is photo × rule.rest (0.30) → × rule.active (0.60) · it is also the
 *           photo under the line (centred at y_rel 561) unless the card has `below`
 *   below / below_images = (D39/D41 · §4.5c.3d) the photo under the line when it is `sh_below_image` (cards 1·7·9):
 *           [w, h] contain 320×265 + the image(s) like `images` · used while that attachment is still the design file
 *   before_scale / before_src = (D39 · card 5) the "before" image (`sh_line_art`) is contained in before_scale × the
 *           photo under the line, while it is still the design file before_src [w, h] (else the §4.5c.3b rule)
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
	'key_prefix' => 'timeline:elephant:', // _sh_import_key prefix of the cards these entries belong to.
	// §4.5c.3 rule for items without an entry (or with a replaced image): photo/mini "contain" in these boxes.
	// #14 [C]: the object on the line = the photo under the line × 0.30 at rest → × 0.60 active (scale ×2, every item).
	'rule'  => array(
		'photo'  => array( 320, 265 ),
		'mini'   => array( 48, 38 ),
		'rest'   => 0.30,
		'active' => 0.60,
	),
	'items' => array(
		// D41: under the line = skeleton line drawing + standing man `bbc1e161` (Figma 3103:1037 · trim 1295×701).
		1 => array( // elephant-leg-bone-elephas-maximus-indicus
			'photo' => array( 320, 169 ), 'mini' => array( 48, 25 ), 'ratio' => 1360 / 720,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 276, 188, 1636, 908 ) ) ),
			'below'  => array( 320, 173 ),
			'below_images' => array( $sh_full( 'sh_below_image', array( 1421, 1080 ), array( 54, 227, 1349, 928 ) ) ),
		),
		2 => array( // singhasari-ganesha
			'photo' => array( 188, 265 ), 'mini' => array( 27, 38 ), 'ratio' => 1340 / 1885,
			'images' => array( $sh_full( 'sh_thumbnail', array( 4096, 2304 ), array( 1378, 179, 2718, 2064 ) ) ),
		),
		3 => array( // ganesha
			'photo' => array( 221, 265 ), 'mini' => array( 32, 38 ), 'ratio' => 1563 / 1878,
			'images' => array( $sh_full( 'sh_thumbnail', array( 4096, 2304 ), array( 1267, 179, 2830, 2057 ) ) ),
		),
		4 => array( // elephant-molar-coconut-grater-dwarf-elephant-bone
			'photo' => array( 320, 160 ), 'mini' => array( 48, 24 ), 'ratio' => 767 / 383,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 577, 348, 1344, 731 ) ) ),
		),
		// D39: on + under the line = `cba74a7d` (Figma 3160:1508 · same size/crop as the old 5d9931b4) · the "before" image
		// (small elephant 567×382) at 0.62 × the photo under the line (Figma 0.616 · owner-basin-before-smaller.png).
		5 => array( // embossed-brass-basin-with-mythical-animal-motifs
			'photo' => array( 320, 229 ), 'mini' => array( 48, 34 ), 'ratio' => 1343 / 960,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1344, 961 ), array( 1, 1, 1344, 961 ) ) ),
			'before_scale' => 0.62,
			'before_src'   => array( 567, 382 ),
		),
		// Slides 49/51: left = pointed piece (sh_thumbnail_2, full height 163) · right = bottle with base (sh_thumbnail, 130 high,
		// same base line) · gap ≈7 px, no overlap. Group 107×163: piston 503/1586 → 0.483 wide · bottle 533/1430 → 0.452 wide.
		6 => array( // ivory-fire-piston-ivory-fire-syringe
			'photo' => array( 174, 265 ), 'mini' => array( 25, 38 ), 'ratio' => 107 / 163,
			'images' => array(
				array( 'field' => 'sh_thumbnail_2', 'src' => array( 4096, 3084 ), 'crop' => array( 1813, 749, 2316, 2335 ), 'box' => array( 0, 0, 0.483, 1 ) ),
				array( 'field' => 'sh_thumbnail', 'src' => array( 4096, 3084 ), 'crop' => array( 1782, 1283, 2315, 2713 ), 'box' => array( 0.548, 0.204, 0.452, 0.796 ) ),
			),
		),
		// D39: on the line = small kendi + water `621646b5` (Figma 3103:1181 · trim 2842×3844) · under the line = kendi + two
		// line-drawn people `555d365b` (3103:1187 · trim 1150×898). Active 118×159 → title bottom y_rel 200.2 unchanged.
		7 => array( // elephant-and-mahout-kendi
			'photo' => array( 196, 265 ), 'mini' => array( 28, 38 ), 'ratio' => 2842 / 3844,
			'images' => array( $sh_full( 'sh_thumbnail', array( 3403, 4096 ), array( 145, 76, 2987, 3920 ) ) ),
			'below'  => array( 320, 250 ),
			'below_images' => array( $sh_full( 'sh_below_image', array( 1421, 1080 ), array( 101, 111, 1251, 1009 ) ) ),
		),
		8 => array( // patola-cloth-cindai
			'photo' => array( 279, 265 ), 'mini' => array( 40, 38 ), 'ratio' => 611 / 580,
			'images' => array( $sh_full( 'sh_thumbnail', array( 625, 589 ), array( 13, 1, 624, 581 ) ) ),
		),
		// Not in Slides → Figma Group 33 `1232:1764` 180×150 (§4.5c.3): 1765 [28.6,35.4 151.4×114.6] · 1766 [0,0 110.3×101.8]
		// on top; crops = the nodes' imageTransform (UV × file px).
		9 => array( // elephant-shaped-relic-containers
			'photo' => array( 318, 265 ), 'mini' => array( 46, 38 ), 'ratio' => 180 / 150,
			'images' => array(
				array( 'field' => 'sh_thumbnail', 'src' => array( 1381, 922 ), 'crop' => array( 298.25, 199.74, 991.2, 724.36 ), 'box' => array( 28.595 / 180, 35.377 / 150, 151.406 / 180, 114.623 / 150 ) ),
				array( 'field' => 'sh_thumbnail_2', 'src' => array( 1381, 922 ), 'crop' => array( 214.58, 200.0, 703.0, 651.0 ), 'box' => array( 0, 0, 110.346 / 180, 101.792 / 150 ) ),
			),
			// D41: under the line = both containers outlined in one image `1c0fc05b` (Figma 3103:1220 · trim 3405×1940).
			'below'  => array( 320, 182 ),
			'below_images' => array( $sh_full( 'sh_below_image', array( 4096, 2305 ), array( 485, 250, 3890, 2190 ) ) ),
		),
		// Photo capped at the file (226 px, §4.5c.3 "ห้ามขยายเกินพิกเซลไฟล์").
		10 => array( // pod-duang-bullet-money-stamped-with-the-chakra-and-great-crown-marks-reign-of-king-rama-iv
			'photo' => array( 226, 226 ), 'mini' => array( 38, 38 ), 'ratio' => 1,
			'images' => array( $sh_full( 'sh_thumbnail', array( 227, 227 ), array( 0, 1, 226, 227 ) ) ),
		),
		11 => array( // hand-carved-ivory-and-metal-opium-pipe
			'photo' => array( 320, 43 ), 'mini' => array( 48, 6 ), 'ratio' => 2017 / 270,
			'images' => array( $sh_full( 'sh_thumbnail', array( 2098, 411 ), array( 47, 54, 2064, 324 ) ) ),
		),
		12 => array( // tin-ingot-in-the-shape-of-elephant
			'photo' => array( 320, 214 ), 'mini' => array( 48, 32 ), 'ratio' => 954 / 637,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 483, 221, 1437, 858 ) ) ),
		),
		13 => array( // wooden-cold-press-cake-molds-with-elephanthorse-and-fowl-motifs
			'photo' => array( 320, 98 ), 'mini' => array( 48, 15 ), 'ratio' => 936 / 287,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 598, 397, 1534, 684 ) ) ),
		),
		14 => array( // siwaih
			'photo' => array( 320, 82 ), 'mini' => array( 48, 12 ), 'ratio' => 981 / 252,
			'images' => array( $sh_full( 'sh_thumbnail', array( 1920, 1080 ), array( 478, 437, 1459, 689 ) ) ),
		),
		15 => array( // procession-of-a-king-or-noble-relief-from-the-life-of-the-buddha
			'photo' => array( 320, 99 ), 'mini' => array( 48, 15 ), 'ratio' => 4096 / 1270,
			'images' => array( $sh_full( 'sh_thumbnail', array( 4096, 1270 ), array( 0, 0, 4096, 1270 ) ) ),
		),
	),
);
