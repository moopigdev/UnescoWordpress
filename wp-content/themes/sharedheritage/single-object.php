<?php
/**
 * Detail page of one object (CPT `object`) — DESIGN-SPEC §4.8 · §6.3 · CONTENT-MODEL §2.
 *
 * Geometry comes from the object's `sh_layout` (sh_object_layout(): Figma frame bboxes at 1440):
 * hero box · panel y/h/padding · panel columns (text_col / image_col, §4.8.1) · Back button y.
 * The panel parts also read text_blocks · caption · images from the same layout (D17: hero_bg not drawn). Everything is passed to the parts as
 * numbers (px at the 1440 frame) and turned into CSS custom properties; assets/css/detail.css scales
 * them with the viewport (1px at ≥1440). Objects without a layout fall back to the "standard" frame
 * of §4.8 (hero [0,150 1440×810] · panel y=960 · padding 60/60 · gap 22).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$sh_id     = (int) get_the_ID();
	$sh_layout = sh_object_layout( $sh_id );

	/*
	 * Number from the layout JSON (finite float) or the default.
	 * The values only become CSS numbers, never markup.
	 */
	$sh_num = static function ( $value, float $fallback ): float {
		return ( is_int( $value ) || is_float( $value ) ) && is_finite( (float) $value ) ? (float) $value : $fallback;
	};

	/* [x,y,w,h] bbox or null. */
	$sh_bbox = static function ( $value ) use ( $sh_num ): ?array {
		if ( ! is_array( $value ) || count( $value ) < 4 ) {
			return null;
		}
		$box = array_map( static fn( $v ) => $sh_num( $v, NAN ), array_slice( array_values( $value ), 0, 4 ) );
		return ( in_array( true, array_map( 'is_nan', $box ), true ) || $box[2] <= 0 || $box[3] <= 0 ) ? null : $box;
	};

	$sh_header_h = 150.0; // Header #1 (86) + Header #02 (64) — §2.3, content starts at y=150.

	// Hero box (first hero entry).
	$sh_hero_box = null;
	if ( ! empty( $sh_layout['hero'] ) && is_array( $sh_layout['hero'] ) ) {
		$sh_first    = reset( $sh_layout['hero'] );
		$sh_hero_box = is_array( $sh_first ) && isset( $sh_first['bbox'] ) ? $sh_bbox( $sh_first['bbox'] ) : null;
	}
	$sh_hero_box = $sh_hero_box ? $sh_hero_box : array( 0.0, $sh_header_h, 1440.0, 810.0 );

	// Panel.
	$sh_hero_bottom = $sh_hero_box[1] + $sh_hero_box[3];
	$sh_panel_y     = $sh_num( $sh_layout['panel_y'] ?? null, $sh_hero_bottom );
	$sh_panel_h     = $sh_num( $sh_layout['panel_h'] ?? null, 0.0 );
	$sh_pad         = isset( $sh_layout['panel_padding_top_bottom'] ) && is_array( $sh_layout['panel_padding_top_bottom'] ) ? array_values( $sh_layout['panel_padding_top_bottom'] ) : array();
	$sh_pad_top     = $sh_num( $sh_pad[0] ?? null, 60.0 );
	$sh_pad_bottom  = $sh_num( $sh_pad[1] ?? null, 60.0 );
	$sh_gap         = $sh_num( $sh_layout['panel_gap'] ?? null, 22.0 );

	/*
	 * Panel columns: the leftmost children form the text column (one frame, or the title/metadata/caption
	 * text nodes stacked full width); the first child further right is the image column.
	 */
	$sh_children = array();
	foreach ( (array) ( $sh_layout['panel_children'] ?? array() ) as $sh_child ) {
		$sh_box = is_array( $sh_child ) && isset( $sh_child['bbox'] ) ? $sh_bbox( $sh_child['bbox'] ) : null;
		if ( $sh_box ) {
			$sh_children[] = $sh_box;
		}
	}
	$sh_text_col   = null;
	$sh_media_col  = null;
	$sh_text_nodes = 0; // >1 = title/metadata/caption are separate panel children (frames 1468/1476).
	if ( $sh_children ) {
		$sh_min_x = min( array_column( $sh_children, 0 ) );
		foreach ( $sh_children as $sh_box ) {
			if ( abs( $sh_box[0] - $sh_min_x ) < 0.5 ) {
				++$sh_text_nodes;
				$sh_text_col = $sh_text_col
					? array(
						$sh_min_x,
						min( $sh_text_col[1], $sh_box[1] ),
						max( $sh_text_col[2], $sh_box[2] ),
						max( $sh_text_col[1] + $sh_text_col[3], $sh_box[1] + $sh_box[3] ) - min( $sh_text_col[1], $sh_box[1] ),
					)
					: $sh_box;
			} elseif ( ! $sh_media_col ) {
				$sh_media_col = $sh_box;
			}
		}
	}

	/*
	 * Inner panel keys (DESIGN-SPEC §4.8.1 · P-161): text_col / image_col boxes win over the derivation above.
	 * text_col.padding_top = where the first block starts inside the column (1510·1503·1483·1484 = 30).
	 */
	$sh_text_pad = 0.0;
	if ( isset( $sh_layout['text_col'] ) && is_array( $sh_layout['text_col'] ) ) {
		$sh_box = isset( $sh_layout['text_col']['bbox'] ) ? $sh_bbox( $sh_layout['text_col']['bbox'] ) : null;
		if ( $sh_box ) {
			$sh_text_col = $sh_box;
			$sh_text_pad = max( 0.0, $sh_num( $sh_layout['text_col']['padding_top'] ?? null, 0.0 ) );
		}
	}
	if ( isset( $sh_layout['image_col'] ) && is_array( $sh_layout['image_col'] ) ) {
		$sh_box = isset( $sh_layout['image_col']['bbox'] ) ? $sh_bbox( $sh_layout['image_col']['bbox'] ) : null;
		if ( $sh_box ) {
			$sh_media_col = $sh_box;
		}
	}

	// Back button: distance below the lowest column (Figma places it under the panel content; may be
	// slightly negative, e.g. 1484 Back sits 2px inside the image column box).
	$sh_cols_bottom = $sh_panel_y + $sh_pad_top;
	foreach ( array( $sh_text_col, $sh_media_col ) as $sh_box ) {
		if ( $sh_box ) {
			$sh_cols_bottom = max( $sh_cols_bottom, $sh_box[1] + $sh_box[3] );
		}
	}
	$sh_back_box = isset( $sh_layout['back_button'] ) ? $sh_bbox( $sh_layout['back_button'] ) : null;
	$sh_back_gap = $sh_back_box ? max( -$sh_gap, $sh_back_box[1] - $sh_cols_bottom ) : $sh_gap;

	// Space under Back = panel bottom − Back bottom (Figma panels are not hugging: 1468 has 68 under a pad of 80).
	if ( $sh_back_box && $sh_panel_h > 0 ) {
		$sh_pad_bottom = max( 0.0, ( $sh_panel_y + $sh_panel_h ) - ( $sh_back_box[1] + $sh_back_box[3] ) );
	}

	// White space under the panel to the frame bottom (1476: frame 2005, panel ends at 2003).
	$sh_frame = isset( $sh_layout['frame_size'] ) && is_array( $sh_layout['frame_size'] ) ? array_values( $sh_layout['frame_size'] ) : array();
	$sh_tail  = ( $sh_panel_h > 0 && isset( $sh_frame[1] ) ) ? max( 0.0, $sh_num( $sh_frame[1], 0.0 ) - ( $sh_panel_y + $sh_panel_h ) ) : 0.0;

	$sh_geometry = array(
		'header_h'     => $sh_header_h,
		'hero'         => $sh_hero_box,
		'hero_gap'     => max( 0.0, $sh_panel_y - $sh_hero_bottom ),
		'panel_h'      => $sh_panel_h,
		'pad_top'      => $sh_pad_top,
		'pad_bottom'   => $sh_pad_bottom,
		'tail'         => $sh_tail,
		'gap'          => $sh_gap,
		'text_col'     => $sh_text_col,
		'text_pad'     => $sh_text_pad,
		'text_nodes'   => $sh_text_nodes,
		'media_col'    => $sh_media_col,
		'back_gap'     => $sh_back_gap,
		'container_x'  => 160.0, // §2.3 / §4.8: panel padding left/right 160 → container 1120 at x=160.
		'container_w'  => 1120.0,
	);
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'sh-detail' ); ?>>
		<?php
		get_template_part(
			'template-parts/detail/hero',
			null,
			array(
				'post_id'  => $sh_id,
				'geometry' => $sh_geometry,
			)
		);
		get_template_part(
			'template-parts/detail/panel',
			null,
			array(
				'post_id'  => $sh_id,
				'geometry' => $sh_geometry,
				'layout'   => $sh_layout,
			)
		);
		?>
	</article>
	<?php
endwhile;

get_footer();
