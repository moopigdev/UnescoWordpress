<?php
/**
 * Detail text column without layout text_blocks (objects outside the 15 Final frames · DOD B6).
 *
 * Title (sh_title_raw, else post title) → metadata block when sh_meta_color is set (label Bold · value
 * Regular, 24/30 when the layout's text lines are direct panel children, else 20/25) or one
 * "Collection:" line 20/25 (P-206) → "Caption:" + paragraphs (sh_paragraphs()).
 *
 * Args: post_id (int).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id   = isset( $sh_args['post_id'] ) ? (int) $sh_args['post_id'] : (int) get_the_ID();

$sh_title_raw  = trim( (string) sh_get( 'sh_title_raw', $sh_id ) );
$sh_title      = '' !== $sh_title_raw ? $sh_title_raw : get_the_title( $sh_id );
$sh_meta_c     = sanitize_hex_color( (string) sh_get( 'sh_meta_color', $sh_id ) );
$sh_collection = trim( (string) sh_get( 'sh_collection', $sh_id ) );
$sh_paragraphs = sh_paragraphs( (string) sh_get( 'sh_caption', $sh_id ) );
$sh_meta_rows  = array();
if ( is_string( $sh_meta_c ) && '' !== $sh_meta_c ) {
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
?>
<div class="sh-detail__text">
	<h1 id="sh-detail-title" class="sh-detail__title"><?php echo esc_html( $sh_title ); ?></h1>

	<?php if ( $sh_meta_rows ) : ?>
		<dl class="sh-detail__meta-block">
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
		<div class="sh-detail__caption-generic">
			<p class="sh-detail__caption-label"><?php esc_html_e( 'Caption:', 'sharedheritage' ); ?></p>
			<?php foreach ( $sh_paragraphs as $sh_para ) : ?>
				<p><?php echo nl2br( esc_html( $sh_para ) ); ?></p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
