<?php
/**
 * Trail cover — Frame 12685 [160,150 1120×490] · image 1120×490 · no rule below (2026-11 · BACKLOG #6 · §4.4).
 * Renders nothing without a cover image (B6).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_trail = isset( $sh_args['trail'] ) && $sh_args['trail'] instanceof WP_Term ? $sh_args['trail'] : null;
if ( null === $sh_trail ) {
	return;
}

$sh_cover_id = (int) sh_get_term( 'sh_cover_image', $sh_trail );
$sh_cover    = $sh_cover_id ? wp_get_attachment_image_src( $sh_cover_id, 'full' ) : false;

// Figma crops the 1920×1080 original with imageTransform (scale .9363 × .7282 · offset .0349, .1250):
// apply that crop only to a 16:9 original; any other (admin-uploaded) image is simply cover-fitted.
$sh_crop = ( $sh_cover && ! empty( $sh_cover[1] ) && ! empty( $sh_cover[2] ) && abs( $sh_cover[1] / $sh_cover[2] - 16 / 9 ) < 0.01 );
?>
<section class="sh-trail__cover<?php echo $sh_cover ? '' : ' sh-trail__cover--empty'; ?>">
	<?php if ( $sh_cover ) : ?>
		<div class="sh-trail__cover-frame<?php echo $sh_crop ? ' sh-trail__cover-frame--figma-crop' : ''; ?>">
			<?php
			echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
				$sh_cover_id,
				'full',
				false,
				array(
					'class'         => 'sh-trail__cover-img',
					'alt'           => '',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'sizes'         => '(min-width: 1168px) 1120px, 100vw',
				)
			);
			?>
		</div>
	<?php endif; ?>
</section>
