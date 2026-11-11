<?php
/**
 * Detail panel — DESIGN-SPEC §4.8 (panel y/h/fill/padding · columns · Back) · §4.8.1 (inner keys).
 *
 * Text column → template-parts/detail/text.php · image column → template-parts/detail/images.php.
 * Panel images: sh_layout images[] (crop_ref = attachment id, already cropped/rotated to its bbox —
 * placed at the bbox) · without them sh_object_extra_images() (contained in the column box).
 * Colours: sh_panel_bg (empty = no fill) · sh_title_color · sh_text_color · sh_meta_color · sh_collection_color.
 *
 * Args: post_id (int) · geometry (array from single-object.php) · layout (sh_object_layout()).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args   = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id     = isset( $sh_args['post_id'] ) ? (int) $sh_args['post_id'] : (int) get_the_ID();
$sh_geo    = isset( $sh_args['geometry'] ) && is_array( $sh_args['geometry'] ) ? $sh_args['geometry'] : array();
$sh_layout = isset( $sh_args['layout'] ) && is_array( $sh_args['layout'] ) ? $sh_args['layout'] : array();

$sh_num   = static fn( $v ): string => (string) round( (float) $v, 3 );
$sh_color = static function ( string $key ) use ( $sh_id ): string {
	$c = sanitize_hex_color( (string) sh_get( $key, $sh_id ) );
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
$sh_placed = $sh_items && $sh_media_col;
if ( ! $sh_items ) {
	foreach ( array_filter( sh_object_extra_images( $sh_id ), 'wp_attachment_is_image' ) as $sh_aid ) {
		$sh_items[] = array(
			'id'  => (int) $sh_aid,
			'box' => null,
		);
	}
}
$sh_two_cols = $sh_media_col && $sh_items;

// Geometry → CSS custom properties (numbers = px at the 1440 frame).
$sh_vars = array(
	'--sh-d-hero-gap'   => $sh_num( $sh_geo['hero_gap'] ?? 0 ),
	'--sh-d-panel-h'    => $sh_num( $sh_geo['panel_h'] ?? 0 ),
	'--sh-d-pad-top'    => $sh_num( $sh_geo['pad_top'] ?? 60 ),
	'--sh-d-pad-bottom' => $sh_num( $sh_geo['pad_bottom'] ?? 60 ),
	'--sh-d-tail'       => $sh_num( $sh_geo['tail'] ?? 0 ),
	'--sh-d-gap'        => $sh_num( $sh_geo['gap'] ?? 22 ),
	'--sh-d-back-gap'   => $sh_num( $sh_geo['back_gap'] ?? 22 ),
	'--sh-d-text-w'     => $sh_num( $sh_two_cols ? $sh_text_w : max( $sh_text_w, $sh_cw ) ),
	'--sh-d-text-h'     => $sh_num( $sh_text_col ? $sh_text_col[3] : 0 ),
	'--sh-d-text-pad'   => $sh_num( $sh_geo['text_pad'] ?? 0 ),
);
if ( $sh_two_cols ) {
	$sh_vars['--sh-d-media-x'] = $sh_num( (float) $sh_media_col[0] - $sh_cx );
	$sh_vars['--sh-d-media-w'] = $sh_num( $sh_media_col[2] );
	$sh_vars['--sh-d-media-h'] = $sh_num( $sh_media_col[3] );
	// #10: the image column starts at its own Figma y (images[] sit at their bbox y inside it).
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

// Back: history back (base.js); fallback = the object's first trail page, else home.
$sh_back_url = home_url( '/' );
$sh_trails   = get_the_terms( $sh_id, 'trail' );
if ( is_array( $sh_trails ) && $sh_trails ) {
	$sh_link = get_term_link( $sh_trails[0] );
	if ( is_string( $sh_link ) ) {
		$sh_back_url = $sh_link;
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
				)
			);
			if ( $sh_items ) {
				get_template_part(
					'template-parts/detail/images',
					null,
					array(
						'items'     => $sh_items,
						'placed'    => $sh_placed,
						'media_col' => $sh_media_col,
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
