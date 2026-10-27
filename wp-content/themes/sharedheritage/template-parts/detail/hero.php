<?php
/**
 * Detail hero — DESIGN-SPEC §4.8 (hero bbox per frame) · §6.3 · CONVENTIONS "วิดีโอ".
 *
 * Video (sh_hero_video_hevc / sh_hero_video_webm + sh_hero_poster) when the object has one, else the
 * still sh_hero_image. Box = layout hero bbox (px at 1440, scaled by detail.css); media is contained
 * (never cropped or stretched). No video and no image → nothing is printed and the panel follows the
 * header (DOD B6). Plays once (no loop, D22), starting when the video first enters the viewport (assets/js/detail.js).
 *
 * Args: post_id (int) · geometry (array from single-object.php).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id   = isset( $sh_args['post_id'] ) ? (int) $sh_args['post_id'] : (int) get_the_ID();
$sh_geo  = isset( $sh_args['geometry'] ) && is_array( $sh_args['geometry'] ) ? $sh_args['geometry'] : array();
$sh_hero = sh_object_hero( $sh_id );

$sh_has_video = '' !== $sh_hero['webm'] || '' !== $sh_hero['hevc'];
$sh_image_id  = (int) $sh_hero['image_id'];
$sh_image_ok  = $sh_image_id && wp_attachment_is_image( $sh_image_id );

if ( ! $sh_has_video && ! $sh_image_ok ) {
	return;
}

$sh_box   = isset( $sh_geo['hero'] ) && is_array( $sh_geo['hero'] ) ? $sh_geo['hero'] : array( 0, 150, 1440, 810 );
$sh_top   = max( 0, (float) $sh_box[1] - (float) ( $sh_geo['header_h'] ?? 150 ) );
$sh_style = sprintf(
	'--sh-d-hero-x:%1$s;--sh-d-hero-top:%2$s;--sh-d-hero-w:%3$s;--sh-d-hero-h:%4$s;',
	round( (float) $sh_box[0], 3 ),
	round( $sh_top, 3 ),
	round( (float) $sh_box[2], 3 ),
	round( (float) $sh_box[3], 3 )
);
$sh_alt   = $sh_image_ok ? (string) get_post_meta( $sh_image_id, '_wp_attachment_image_alt', true ) : '';
$sh_alt   = '' !== $sh_alt ? $sh_alt : get_the_title( $sh_id );
?>
<div class="sh-detail__hero" style="<?php echo esc_attr( $sh_style ); ?>">
	<div class="sh-detail__hero-box">
		<?php if ( $sh_has_video ) : ?>
			<video class="sh-detail__hero-media" muted playsinline preload="metadata" data-sh-autoplay
				<?php echo '' !== $sh_hero['poster'] ? 'poster="' . esc_url( $sh_hero['poster'] ) . '"' : ''; ?>
				aria-label="<?php echo esc_attr( $sh_alt ); ?>">
				<?php if ( '' !== $sh_hero['hevc'] ) : ?>
					<source src="<?php echo esc_url( $sh_hero['hevc'] ); ?>" type='video/mp4; codecs="hvc1"'>
				<?php endif; ?>
				<?php if ( '' !== $sh_hero['webm'] ) : ?>
					<source src="<?php echo esc_url( $sh_hero['webm'] ); ?>" type="video/webm">
				<?php endif; ?>
			</video>
		<?php else : ?>
			<?php
			echo wp_get_attachment_image(
				$sh_image_id,
				'full',
				false,
				array(
					'class'         => 'sh-detail__hero-media',
					'alt'           => $sh_alt,
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
				)
			);
			?>
		<?php endif; ?>
	</div>
</div>
