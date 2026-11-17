<?php
/**
 * Detail text column without layout text_blocks (generic mode · D49 objects of the 16 new trails · DOD B6).
 *
 * Title (sh_title_raw, else post title) → info rows → "Caption:" + paragraphs (sh_paragraphs()).
 * Info rows (D42 · P-186): every field that has a value, fixed order, empty values skipped, Section never shown:
 * Scientific name / Taxonomy → Period → Place of production / Findspot → Material → Dimensions → Local Name →
 * Collection → Other information / Object biography (label Komet-Bold · value Komet-Regular 20/25, colour sh_meta_color).
 * A value with line breaks (Giant Clams: two specimens) shows one line per entry inside its row.
 *
 * Trail version (D59): variant (sh_object_variant() | null) — its title / caption replace the object's when filled · its info rows replace
 * the whole set when any is filled (sh_object_info_value()).
 *
 * Args: post_id (int) · variant (array|null).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id   = isset( $sh_args['post_id'] ) ? (int) $sh_args['post_id'] : (int) get_the_ID();

$sh_var        = isset( $sh_args['variant'] ) && is_array( $sh_args['variant'] ) ? $sh_args['variant'] : null;
$sh_title_raw  = trim( (string) sh_get( 'sh_title_raw', $sh_id ) );
$sh_title      = '' !== $sh_title_raw ? $sh_title_raw : get_the_title( $sh_id );
$sh_caption    = (string) sh_get( 'sh_caption', $sh_id );
if ( $sh_var && '' !== $sh_var['title'] ) {
	$sh_title = $sh_var['title'];
}
if ( $sh_var && '' !== $sh_var['caption'] ) {
	$sh_caption = $sh_var['caption'];
}
$sh_paragraphs = sh_paragraphs( $sh_caption );
$sh_meta_rows  = array(
	array( __( 'Scientific name / Taxonomy:', 'sharedheritage' ), 'scientific_name' ),
	array( __( 'Period:', 'sharedheritage' ), 'period' ),
	array( __( 'Place of production / Findspot:', 'sharedheritage' ), 'place_of_production' ),
	array( __( 'Material:', 'sharedheritage' ), 'material' ),
	array( __( 'Dimensions:', 'sharedheritage' ), 'dimensions' ),
	array( __( 'Local Name:', 'sharedheritage' ), 'local_name' ),
	array( __( 'Collection:', 'sharedheritage' ), 'collection' ),
	array( __( 'Other information / Object biography:', 'sharedheritage' ), 'object_biography' ),
);
$sh_meta_rows = array_values(
	array_filter(
		array_map(
			static fn( array $row ): array => array( $row[0], sh_object_info_value( $sh_id, $row[1], $sh_var ) ),
			$sh_meta_rows
		),
		static fn( array $row ): bool => '' !== $row[1]
	)
);
?>
<div class="sh-detail__text">
	<h1 id="sh-detail-title" class="sh-detail__title"><?php echo esc_html( $sh_title ); ?></h1>

	<?php if ( $sh_meta_rows ) : ?>
		<dl class="sh-detail__meta-block">
			<?php foreach ( $sh_meta_rows as $sh_row ) : ?>
				<div class="sh-detail__meta-row">
					<dt><?php echo esc_html( $sh_row[0] ); ?></dt>
					<dd><?php echo nl2br( esc_html( $sh_row[1] ) ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
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
