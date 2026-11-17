<?php
/**
 * Detail panel — DESIGN-SPEC §4.8 (panel y/h/fill/padding · columns · Back) · §4.8.1 (inner keys).
 *
 * Text column → template-parts/detail/text.php · image column → template-parts/detail/images.php.
 * Panel images: sh_layout images[] (crop_ref = attachment id, already cropped/rotated to its bbox —
 * placed at the bbox) · without them sh_object_extra_images() (contained in the column box).
 * Colours: sh_panel_bg (empty = no fill) · sh_title_color · sh_text_color · sh_meta_color · sh_collection_color.
 *
 * Trail version (D59): variant = sh_object_variant() of the trail the visitor came from — its colours replace the object's (field by
 * field), its side images replace the layout images in order, its title/caption/info rows go to the text column.
 *
 * Args: post_id (int) · geometry (array from single-object.php) · layout (sh_object_layout() or the version's layout) · variant (array|null).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args   = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id     = isset( $sh_args['post_id'] ) ? (int) $sh_args['post_id'] : (int) get_the_ID();
$sh_geo    = isset( $sh_args['geometry'] ) && is_array( $sh_args['geometry'] ) ? $sh_args['geometry'] : array();
$sh_layout = isset( $sh_args['layout'] ) && is_array( $sh_args['layout'] ) ? $sh_args['layout'] : array();
$sh_ver    = isset( $sh_args['variant'] ) && is_array( $sh_args['variant'] ) ? $sh_args['variant'] : null;

$sh_num   = static fn( $v ): string => (string) round( (float) $v, 3 );
$sh_color = static function ( string $key ) use ( $sh_id, $sh_ver ): string {
	$own = $sh_ver && ! empty( $sh_ver['colors'][ $key ] ) ? $sh_ver['colors'][ $key ] : sh_get( $key, $sh_id ); // D59: the version's colour first.
	$c   = sanitize_hex_color( (string) $own );
	return is_string( $c ) ? $c : '';
};

$sh_panel_bg = $sh_color( 'sh_panel_bg' );

// Dark fill (e.g. Pod Duang #363638): Figma switches the Back fill to #FFFFFF@10%.
$sh_dark = false;
if ( '' !== $sh_panel_bg ) {
	$sh_hex  = ltrim( $sh_panel_bg, '#' );
	$sh_hex  = 3 === strlen( $sh_hex ) ? $sh_hex[0] . $sh_hex[0] . $sh_hex[1] . $sh_hex[1] . $sh_hex[2] . $sh_hex[2] : $sh_hex;
	$sh_rgb  = array_map( 'hexdec', str_split( $sh_hex, 2 ) );
	$sh_dark = ( 0.2126 * $sh_rgb[0] + 0.7152 * $sh_rgb[1] + 0.0722 * $sh_rgb[2] ) / 255 < 0.35;
}

$sh_text_col  = isset( $sh_geo['text_col'] ) && is_array( $sh_geo['text_col'] ) ? $sh_geo['text_col'] : null;
$sh_media_col = isset( $sh_geo['media_col'] ) && is_array( $sh_geo['media_col'] ) ? $sh_geo['media_col'] : null;
$sh_cx        = (float) ( $sh_geo['container_x'] ?? 160 );
$sh_cw        = (float) ( $sh_geo['container_w'] ?? 1120 );
$sh_text_w    = $sh_text_col ? (float) $sh_text_col[2] : $sh_cw;

// Panel images: layout images[] at their bbox, else the extra image fields.
$sh_items = array();
foreach ( (array) ( $sh_layout['images'] ?? array() ) as $sh_img ) {
	$sh_aid = is_array( $sh_img ) && isset( $sh_img['crop_ref'] ) && is_int( $sh_img['crop_ref'] ) ? $sh_img['crop_ref'] : 0;
	$sh_box = is_array( $sh_img ) && isset( $sh_img['bbox'] ) && is_array( $sh_img['bbox'] ) ? array_map( 'floatval', array_slice( array_values( $sh_img['bbox'] ), 0, 4 ) ) : array();
	if ( $sh_aid > 0 && wp_attachment_is_image( $sh_aid ) && 4 === count( $sh_box ) && $sh_box[2] > 0 && $sh_box[3] > 0 ) {
		$sh_items[] = array(
			'id'    => $sh_aid,
			'box'   => $sh_box,
			'order' => (int) ( $sh_img['order'] ?? count( $sh_items ) ),
		);
	}
}
usort( $sh_items, static fn( $a, $b ) => $a['order'] <=> $b['order'] );
if ( $sh_ver && ! empty( $sh_ver['images'] ) ) { // D59: a side image the admin set on the version replaces the layout's image at that place.
	foreach ( $sh_ver['images'] as $sh_k => $sh_aid ) {
		if ( isset( $sh_items[ $sh_k - 1 ] ) && wp_attachment_is_image( (int) $sh_aid ) ) {
			$sh_items[ $sh_k - 1 ]['id'] = (int) $sh_aid;
		}
	}
}
$sh_placed = $sh_items && $sh_media_col;
if ( ! $sh_items ) {
	foreach ( array_filter( sh_object_extra_images( $sh_id ), 'wp_attachment_is_image' ) as $sh_aid ) {
		$sh_items[] = array(
			'id'  => (int) $sh_aid,
			'box' => null,
		);
	}
}
/*
 * Empty image slot (makara-1117 · DESIGN-SPEC §8.6): the layout keeps the image column of a frame whose side image the
 * designer adds later (style.image_slot_empty). Without panel images the column stays — nothing is drawn in it and the
 * text does not take its place; an image the admin adds (sh_extra_image_*) lands in the column box (images.php).
 */
$sh_slot     = ! $sh_placed && $sh_media_col && isset( $sh_layout['image_slot_empty'] ) && is_array( $sh_layout['image_slot_empty'] );
$sh_two_cols = $sh_media_col && ( $sh_items || $sh_slot );

// Geometry → CSS custom properties (numbers = px at the 1440 frame).
$sh_vars = array(
	'--sh-d-hero-gap'   => $sh_num( $sh_geo['hero_gap'] ?? 0 ),
	'--sh-d-panel-h'    => $sh_num( $sh_geo['panel_h'] ?? 0 ),
	'--sh-d-pad-top'    => $sh_num( $sh_geo['pad_top'] ?? 60 ),
	'--sh-d-pad-bottom' => $sh_num( $sh_geo['pad_bottom'] ?? 60 ),
	'--sh-d-tail'       => $sh_num( $sh_geo['tail'] ?? 0 ),
	'--sh-d-gap'        => $sh_num( $sh_geo['gap'] ?? 22 ),
	'--sh-d-text-gap'   => $sh_num( $sh_geo['text_gap'] ?? ( $sh_geo['gap'] ?? 22 ) ),
	'--sh-d-back-gap'   => $sh_num( $sh_geo['back_gap'] ?? 22 ),
	'--sh-d-text-w'     => $sh_num( $sh_two_cols ? $sh_text_w : max( $sh_text_w, $sh_cw ) ),
	'--sh-d-text-h'     => $sh_num( $sh_text_col ? $sh_text_col[3] : 0 ),
	'--sh-d-text-pad'   => $sh_num( $sh_geo['text_pad'] ?? 0 ),
);
if ( $sh_two_cols ) {
	$sh_vars['--sh-d-media-x'] = $sh_num( (float) $sh_media_col[0] - $sh_cx );
	$sh_vars['--sh-d-media-w'] = $sh_num( $sh_media_col[2] );
	$sh_vars['--sh-d-media-h'] = $sh_num( $sh_media_col[3] );
	// D43: media_col y = the Figma info block y (single-object.php); detail.js sets the measured value.
	$sh_vars['--sh-d-media-y'] = $sh_num( $sh_text_col ? (float) $sh_media_col[1] - (float) $sh_text_col[1] : 0 );
}
foreach (
	array(
		'--sh-d-panel-bg' => $sh_panel_bg,
		'--sh-d-title'    => $sh_color( 'sh_title_color' ),
		'--sh-d-text'     => $sh_color( 'sh_text_color' ),
		'--sh-d-meta'     => $sh_color( 'sh_meta_color' ),
		'--sh-d-coll'     => $sh_color( 'sh_collection_color' ),
	) as $sh_var => $sh_val
) {
	if ( '' !== $sh_val ) {
		$sh_vars[ $sh_var ] = $sh_val;
	}
}
$sh_style = '';
foreach ( $sh_vars as $sh_var => $sh_val ) {
	$sh_style .= $sh_var . ':' . $sh_val . ';';
}

$sh_panel_cls = 'sh-detail__panel'
	. ( '' !== $sh_panel_bg ? ' sh-detail__panel--fill' : '' )
	. ( $sh_dark ? ' sh-detail__panel--dark' : '' )
	. ( $sh_two_cols ? ' sh-detail__panel--cols' : '' )
	. ( (int) ( $sh_geo['text_nodes'] ?? 0 ) > 1 ? ' sh-detail__panel--meta-lg' : '' );

// Back: history back (base.js) when the visitor came from this site — i.e. from the trail they clicked, also for an
// object that sits in several trails; fallback href = the object's main trail (the one that is not listed in its
// other-trail tile fields sh_trail_tile_<n>_trail · D49), else home — or the zone page it came from (below).
$sh_back_url = home_url( '/' );
$sh_trails   = get_the_terms( $sh_id, 'trail' );
if ( is_array( $sh_trails ) && $sh_trails ) {
	$sh_others = array();
	for ( $sh_n = 1; $sh_n <= 4; $sh_n++ ) {
		$sh_others[] = absint( sh_get( 'sh_trail_tile_' . $sh_n . '_trail', $sh_id ) );
	}
	usort( $sh_trails, static fn( $a, $b ) => $a->term_id <=> $b->term_id );
	$sh_main = $sh_trails[0];
	foreach ( $sh_trails as $sh_t ) {
		if ( ! in_array( $sh_t->term_id, $sh_others, true ) ) {
			$sh_main = $sh_t;
			break;
		}
	}
	$sh_link = get_term_link( $sh_ver ? $sh_ver['trail'] : $sh_main ); // D59: the trail the version belongs to (the one the visitor came from).
	if ( is_string( $sh_link ) ) {
		$sh_back_url = $sh_link;
	}
}
// D57: the visitor came from a tile of a Zone 1–3 grid (?from=zone-<n> · template-parts/zone/tile.php) → the fallback Back
// goes to that zone page (history back still wins when it is on this site). Only an existing zone 1–3 page counts.
$sh_from = isset( $_GET['from'] ) ? sanitize_key( wp_unslash( $_GET['from'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display choice only, checked against the zone terms.
if ( '' !== $sh_from ) {
	$sh_from_zone = get_term_by( 'slug', $sh_from, 'zone' );
	if ( $sh_from_zone instanceof WP_Term && (bool) sh_get_term( 'sh_has_page', $sh_from_zone ) && in_array( (int) sh_get_term( 'sh_number', $sh_from_zone ), array( 1, 2, 3 ), true ) ) {
		$sh_from_link = get_term_link( $sh_from_zone );
		if ( is_string( $sh_from_link ) ) {
			$sh_back_url = $sh_from_link;
		}
	}
}
?>
<section class="<?php echo esc_attr( $sh_panel_cls ); ?>" style="<?php echo esc_attr( $sh_style ); ?>" aria-labelledby="sh-detail-title">
	<div class="sh-detail__inner">
		<div class="sh-detail__cols">
			<?php
			get_template_part(
				'template-parts/detail/text',
				null,
				array(
					'post_id' => $sh_id,
					'layout'  => $sh_layout,
					'variant' => $sh_ver,
				)
			);
			if ( $sh_items || $sh_slot ) {
				get_template_part(
					'template-parts/detail/images',
					null,
					array(
						'items'     => $sh_items,
						'slot'      => $sh_slot,
						'placed'    => $sh_placed,
						'media_col' => $sh_media_col,
						'img_top'   => $sh_geo['img_top'] ?? null,
					)
				);
			}
			?>
		</div>

		<?php
		get_template_part(
			'template-parts/common/back-button',
			null,
			array(
				'url'   => $sh_back_url,
				'class' => 'sh-detail__back',
			)
		);
		?>
	</div>
</section>
