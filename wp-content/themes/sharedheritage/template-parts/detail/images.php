<?php
/**
 * Detail panel images — DESIGN-SPEC §4.8.1 `images[]` / `image_col`.
 *
 * placed = true: each image is the design-dev crop (already cropped/rotated to its bbox, radius 0) drawn
 * at its bbox inside the image column (later images on top, e.g. 1507's overlapping pair).
 * placed = false: extra image fields stacked in the column box, contained (no crop).
 *
 * Args: items (array of {id:int, box:[x,y,w,h]|null}) · placed (bool) · media_col ([x,y,w,h]|null) · img_top (float|null: y of the topmost image).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args   = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_items  = isset( $sh_args['items'] ) && is_array( $sh_args['items'] ) ? $sh_args['items'] : array();
$sh_col    = isset( $sh_args['media_col'] ) && is_array( $sh_args['media_col'] ) ? $sh_args['media_col'] : null;
$sh_placed = ! empty( $sh_args['placed'] ) && $sh_col;
// D43: images sit relative to the topmost one (= the column top), not to the Figma column y.
$sh_top    = isset( $sh_args['img_top'] ) && is_numeric( $sh_args['img_top'] ) ? (float) $sh_args['img_top'] : ( $sh_col ? (float) $sh_col[1] : 0.0 );

if ( ! $sh_items ) {
	return;
}
?>
<div class="sh-detail__media<?php echo $sh_placed ? ' sh-detail__media--placed' : ''; ?>">
	<?php
	foreach ( $sh_items as $sh_item ) {
		$sh_attr = array(
			'class'    => 'sh-detail__image',
			'loading'  => 'lazy',
			'decoding' => 'async',
		);
		if ( $sh_placed && is_array( $sh_item['box'] ) ) {
			$sh_box           = $sh_item['box'];
			$sh_attr['style'] = sprintf(
				'--sh-d-img-x:%1$s;--sh-d-img-y:%2$s;--sh-d-img-w:%3$s;--sh-d-img-h:%4$s;',
				round( $sh_box[0] - (float) $sh_col[0], 3 ),
				round( $sh_box[1] - $sh_top, 3 ),
				round( $sh_box[2], 3 ),
				round( $sh_box[3], 3 )
			);
		}
		echo wp_get_attachment_image( (int) $sh_item['id'], 'full', false, $sh_attr );
	}
	?>
</div>
