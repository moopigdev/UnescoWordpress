<?php
/**
 * Detail panel — DESIGN-SPEC §4.8 (panel y/h/fill/padding · columns · Back) · §2.2 type levels.
 *
 * Text column: title (sh_title_raw, else post title — line breaks kept) → metadata → caption.
 *   - Metadata block (Section/Period/…, label Bold · value Regular) when the frame has one: the object has a
 *     metadata colour (sh_meta_color — set only for frames with a metadata block, CONTENT-MODEL §2).
 *     24/30 when the text lines are direct panel children (1468/1476), else 20/25 (1503/1504).
 *   - Otherwise one "Collection:" line 20/25 (Final standard, P-206) in sh_collection_color.
 *   - Caption: "Caption:" label + paragraphs (blank-line separated, sh_paragraphs()).
 * Image column: sh_object_extra_images() share the layout column box (width × height), contained —
 * never cropped/stretched (Figma crops/rotates them with image transforms that are not in sh_layout).
 * Colours: sh_panel_bg (empty = no fill) · sh_title_color · sh_text_color · sh_meta_color · sh_collection_color.
 *
 * Args: post_id (int) · geometry (array from single-object.php).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id   = isset( $sh_args['post_id'] ) ? (int) $sh_args['post_id'] : (int) get_the_ID();
$sh_geo  = isset( $sh_args['geometry'] ) && is_array( $sh_args['geometry'] ) ? $sh_args['geometry'] : array();

$sh_num   = static fn( $v ): string => (string) round( (float) $v, 3 );
$sh_color = static function ( string $key ) use ( $sh_id ): string {
	$c = sanitize_hex_color( (string) sh_get( $key, $sh_id ) );
	return is_string( $c ) ? $c : '';
};

$sh_panel_bg  = $sh_color( 'sh_panel_bg' );
$sh_title_c   = $sh_color( 'sh_title_color' );
$sh_text_c    = $sh_color( 'sh_text_color' );
$sh_meta_c    = $sh_color( 'sh_meta_color' );
$sh_coll_c    = $sh_color( 'sh_collection_color' );
$sh_has_block = '' !== $sh_meta_c;

// Dark fill (e.g. Pod Duang #363638): Figma switches the Back fill to #FFFFFF@10%.
$sh_dark = false;
if ( '' !== $sh_panel_bg ) {
	$sh_hex  = ltrim( $sh_panel_bg, '#' );
	$sh_hex  = 3 === strlen( $sh_hex ) ? $sh_hex[0] . $sh_hex[0] . $sh_hex[1] . $sh_hex[1] . $sh_hex[2] . $sh_hex[2] : $sh_hex;
	$sh_rgb  = array_map( 'hexdec', str_split( $sh_hex, 2 ) );
	$sh_dark = ( 0.2126 * $sh_rgb[0] + 0.7152 * $sh_rgb[1] + 0.0722 * $sh_rgb[2] ) / 255 < 0.35;
}

// Geometry → CSS custom properties (numbers = px at the 1440 frame).
$sh_cx    = (float) ( $sh_geo['container_x'] ?? 160 );
$sh_vars  = array(
	'--sh-d-hero-gap'   => $sh_num( $sh_geo['hero_gap'] ?? 0 ),
	'--sh-d-panel-h'    => $sh_num( $sh_geo['panel_h'] ?? 0 ),
	'--sh-d-pad-top'    => $sh_num( $sh_geo['pad_top'] ?? 60 ),
	'--sh-d-pad-bottom' => $sh_num( $sh_geo['pad_bottom'] ?? 60 ),
	'--sh-d-tail'       => $sh_num( $sh_geo['tail'] ?? 0 ),
	'--sh-d-gap'        => $sh_num( $sh_geo['gap'] ?? 22 ),
	'--sh-d-back-gap'   => $sh_num( $sh_geo['back_gap'] ?? 22 ),
);
$sh_text_col  = isset( $sh_geo['text_col'] ) && is_array( $sh_geo['text_col'] ) ? $sh_geo['text_col'] : null;
$sh_media_col = isset( $sh_geo['media_col'] ) && is_array( $sh_geo['media_col'] ) ? $sh_geo['media_col'] : null;
$sh_cw        = (float) ( $sh_geo['container_w'] ?? 1120 );
$sh_text_w    = $sh_text_col ? (float) $sh_text_col[2] : $sh_cw;
$sh_images    = sh_object_extra_images( $sh_id );
$sh_images    = array_values( array_filter( $sh_images, 'wp_attachment_is_image' ) );
$sh_two_cols  = $sh_media_col && $sh_images;

$sh_vars['--sh-d-text-w'] = $sh_num( $sh_two_cols ? $sh_text_w : max( $sh_text_w, $sh_cw ) );
$sh_vars['--sh-d-text-h'] = $sh_num( $sh_text_col ? $sh_text_col[3] : 0 );
if ( $sh_two_cols ) {
	$sh_vars['--sh-d-media-x'] = $sh_num( (float) $sh_media_col[0] - $sh_cx );
	$sh_vars['--sh-d-media-w'] = $sh_num( $sh_media_col[2] );
	$sh_vars['--sh-d-media-h'] = $sh_num( $sh_media_col[3] );
}
foreach (
	array(
		'--sh-d-panel-bg' => $sh_panel_bg,
		'--sh-d-title'    => $sh_title_c,
		'--sh-d-text'     => $sh_text_c,
		'--sh-d-meta'     => $sh_meta_c,
		'--sh-d-coll'     => $sh_coll_c,
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

// Text.
$sh_title_raw = (string) sh_get( 'sh_title_raw', $sh_id );
$sh_title     = '' !== trim( $sh_title_raw ) ? trim( $sh_title_raw ) : get_the_title( $sh_id );
$sh_meta_rows = array();
if ( $sh_has_block ) {
	$sh_meta_rows = array(
		array( __( 'Section :', 'sharedheritage' ), 'sh_thematic_section' ),
		array( __( 'Period:', 'sharedheritage' ), 'sh_period' ),
		array( __( 'Place of Production / Findspot:', 'sharedheritage' ), 'sh_place_of_production' ),
		array( __( 'Material:', 'sharedheritage' ), 'sh_material' ),
		array( __( 'Dimensions:', 'sharedheritage' ), 'sh_dimensions' ),
		array( __( 'Local Name:', 'sharedheritage' ), 'sh_local_name' ),
		array( __( 'Collection:', 'sharedheritage' ), 'sh_collection' ),
		array( __( 'Other Information / Object Biography:', 'sharedheritage' ), 'sh_object_biography' ),
	);
}
$sh_collection = trim( (string) sh_get( 'sh_collection', $sh_id ) );
$sh_paragraphs = sh_paragraphs( (string) sh_get( 'sh_caption', $sh_id ) );

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
			<div class="sh-detail__text">
				<h1 id="sh-detail-title" class="sh-detail__title"><?php echo esc_html( $sh_title ); ?></h1>

				<?php if ( $sh_has_block ) : ?>
					<dl class="sh-detail__meta">
						<?php
						foreach ( $sh_meta_rows as $sh_row ) :
							$sh_value = trim( (string) sh_get( $sh_row[1], $sh_id ) );
							if ( '' === $sh_value ) {
								continue;
							}
							?>
							<div class="sh-detail__meta-row">
								<dt><?php echo esc_html( $sh_row[0] ); ?></dt>
								<dd><?php echo esc_html( $sh_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php elseif ( '' !== $sh_collection ) : ?>
					<p class="sh-detail__collection">
						<strong><?php esc_html_e( 'Collection:', 'sharedheritage' ); ?></strong>
						<?php echo esc_html( $sh_collection ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $sh_paragraphs ) : ?>
					<div class="sh-detail__caption">
						<p class="sh-detail__caption-label"><?php esc_html_e( 'Caption:', 'sharedheritage' ); ?></p>
						<?php foreach ( $sh_paragraphs as $sh_para ) : ?>
							<p><?php echo nl2br( esc_html( $sh_para ) ); ?></p>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $sh_images ) : // No image column in the layout → images follow the text. ?>
				<div class="sh-detail__media">
					<?php
					foreach ( $sh_images as $sh_img ) {
						echo wp_get_attachment_image(
							$sh_img,
							'full',
							false,
							array(
								'class'    => 'sh-detail__image',
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						);
					}
					?>
				</div>
			<?php endif; ?>
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
