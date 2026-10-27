<?php
/**
 * Zone page, opening part: cover (§4.3 [160,150 1120×410]) and zone banner ([160,579 1120×166] r22 + shadow 200).
 * Zone name sits inside the cover image (D8 · text-in-image) → the <h1> is visually hidden when a cover exists.
 *
 * Args: zone (WP_Term).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_zone = isset( $sh_args['zone'] ) && $sh_args['zone'] instanceof WP_Term ? $sh_args['zone'] : null;
if ( ! $sh_zone ) {
	return;
}

$sh_number = (int) sh_get_term( 'sh_number', $sh_zone );
$sh_cover  = absint( sh_get_term( 'sh_cover_image', $sh_zone ) );
$sh_banner = absint( sh_get_term( 'sh_banner_image', $sh_zone ) );

$sh_cover_html = $sh_cover ? wp_get_attachment_image(
	$sh_cover,
	'full',
	false,
	array(
		'class'         => 'sh-zone__cover-img',
		'alt'           => '',
		'loading'       => 'eager',
		'decoding'      => 'async',
		'fetchpriority' => 'high',
		'sizes'         => '(min-width: 1168px) 1120px, 100vw',
	)
) : '';

$sh_banner_html = '';
$sh_banner_crop = false;
if ( $sh_banner ) {
	$sh_banner_html = wp_get_attachment_image(
		$sh_banner,
		'full',
		false,
		array(
			'class'    => 'sh-zone__banner-img',
			/* translators: %d: zone number. */
			'alt'      => sprintf( __( 'Zone %d of 5', 'sharedheritage' ), $sh_number ),
			'loading'  => 'eager',
			'decoding' => 'async',
			'sizes'    => '(min-width: 1168px) 1180px, 106vw',
		)
	);
	// Figma crops the 4096×965 banner export to the 1120×166 frame (imageTransform); other shapes simply fill.
	$sh_meta        = wp_get_attachment_metadata( $sh_banner );
	$sh_w           = is_array( $sh_meta ) && ! empty( $sh_meta['width'] ) ? (float) $sh_meta['width'] : 0.0;
	$sh_h           = is_array( $sh_meta ) && ! empty( $sh_meta['height'] ) ? (float) $sh_meta['height'] : 0.0;
	$sh_banner_crop = $sh_w > 0 && $sh_h > 0 && abs( ( $sh_w / $sh_h ) / ( 4096 / 965 ) - 1 ) < 0.01;
}
?>
<h1 class="<?php echo esc_attr( '' !== $sh_cover_html ? 'screen-reader-text' : 'sh-t-page-title sh-zone__title' ); ?>"><?php echo esc_html( $sh_zone->name ); ?></h1>

<?php if ( '' !== $sh_cover_html ) : ?>
	<figure class="sh-zone__cover">
		<?php echo $sh_cover_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
	</figure>
<?php endif; ?>

<?php if ( '' !== $sh_banner_html ) : ?>
	<figure class="sh-zone__banner<?php echo $sh_banner_crop ? ' sh-zone__banner--crop' : ''; ?>">
		<div class="sh-zone__banner-view" data-sh-zone-banner data-sh-label="<?php echo esc_attr__( 'Zones 1 to 5', 'sharedheritage' ); ?>">
			<div class="sh-zone__banner-strip">
				<?php echo $sh_banner_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
			</div>
		</div>
	</figure>
<?php endif; ?>
