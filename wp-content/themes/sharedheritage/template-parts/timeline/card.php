<?php
/**
 * Timeline card — Figma "Frame 1144xx" in Frame 114464 (DESIGN-SPEC §4.5 · CONTENT-MODEL §3).
 *
 * [image or 2-image group] + gap 10 + text column (title 20/25 Komet-Heavy · gap 15 · meta 14/24 Komet-Regular),
 * vertically centred. The card's "Read more >>" is hidden in Figma (visible:false) — title and image link
 * to the object (sh_object) instead, with no visual change.
 *
 * Args: 'item' WP_Post (timeline_item) · 'geo' array (template-parts/timeline/geometry.php entry, or []).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_item = ( isset( $args['item'] ) && $args['item'] instanceof WP_Post ) ? $args['item'] : null;
if ( ! $sh_item ) {
	return;
}
$sh_geo = ( isset( $args['geo'] ) && is_array( $args['geo'] ) ) ? $args['geo'] : array();

$sh_title = get_the_title( $sh_item );
$sh_meta  = (string) sh_get( 'sh_card_meta', $sh_item->ID );

$sh_object_id = absint( sh_get( 'sh_object', $sh_item->ID ) );
$sh_url       = ( $sh_object_id && 'publish' === get_post_status( $sh_object_id ) ) ? get_permalink( $sh_object_id ) : '';

// Images: geometry entry (Figma boxes) or the generic layout (thumbnail, thumbnail_2 side by side).
$sh_images = array();
if ( ! empty( $sh_geo['images'] ) && is_array( $sh_geo['images'] ) ) {
	foreach ( $sh_geo['images'] as $sh_box ) {
		$sh_field = isset( $sh_box['field'] ) && in_array( $sh_box['field'], array( 'sh_thumbnail', 'sh_thumbnail_2' ), true ) ? $sh_box['field'] : '';
		if ( '' !== $sh_field ) {
			$sh_images[] = array( absint( sh_get( $sh_field, $sh_item->ID ) ), $sh_box );
		}
	}
} else {
	foreach ( array( 'sh_thumbnail', 'sh_thumbnail_2' ) as $sh_field ) {
		$sh_images[] = array( absint( sh_get( $sh_field, $sh_item->ID ) ), array() );
	}
}

$sh_media = '';
foreach ( $sh_images as $sh_image ) {
	$sh_media .= sh_timeline_image( $sh_image[0], $sh_image[1] );
}

// Card geometry → CSS custom properties (timeline.css falls back to the generic layout when absent).
$sh_vars = array();
if ( ! empty( $sh_geo ) ) {
	$sh_vars['pl']  = sh_timeline_px( isset( $sh_geo['pad'][0] ) ? (float) $sh_geo['pad'][0] : 0 );
	$sh_vars['pr']  = sh_timeline_px( isset( $sh_geo['pad'][1] ) ? (float) $sh_geo['pad'][1] : 0 );
	$sh_vars['col'] = sh_timeline_px( isset( $sh_geo['col'] ) ? (float) $sh_geo['col'] : 477 );
	$sh_vars['tw']  = ( isset( $sh_geo['title'] ) && 'hug' === $sh_geo['title'] ) ? 'max-content' : sh_timeline_px( isset( $sh_geo['title'] ) ? (float) $sh_geo['title'] : 477 );
	$sh_vars['mw']  = sh_timeline_px( isset( $sh_geo['meta'] ) ? (float) $sh_geo['meta'] : 477 );
	$sh_vars['lh']  = sh_timeline_px( isset( $sh_geo['lh'] ) ? (float) $sh_geo['lh'] : 24 );
	if ( isset( $sh_geo['media']['w'], $sh_geo['media']['h'] ) ) {
		$sh_vars['gw'] = sh_timeline_px( (float) $sh_geo['media']['w'] );
		$sh_vars['gh'] = sh_timeline_px( (float) $sh_geo['media']['h'] );
	}
}
$sh_cls = 'sh-tl-card' . ( empty( $sh_geo ) ? ' sh-tl-card--generic' : '' ) . ( count( $sh_images ) > 1 && ! empty( $sh_geo ) ? ' sh-tl-card--group' : '' );
?>
<li class="<?php echo esc_attr( $sh_cls ); ?>" style="<?php echo esc_attr( sh_timeline_style( $sh_vars ) ); ?>" data-sh-timeline-stop>
	<?php if ( '' !== $sh_media ) : ?>
		<?php if ( '' !== $sh_url ) : ?>
			<a class="sh-tl-card__media" href="<?php echo esc_url( $sh_url ); ?>" tabindex="-1" aria-hidden="true"><?php echo $sh_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by sh_timeline_image() (escaped there). ?></a>
		<?php else : ?>
			<span class="sh-tl-card__media"><?php echo $sh_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by sh_timeline_image() (escaped there). ?></span>
		<?php endif; ?>
	<?php endif; ?>
	<div class="sh-tl-card__text">
		<h2 class="sh-tl-card__title sh-t-card-title">
			<?php if ( '' !== $sh_url ) : ?>
				<a href="<?php echo esc_url( $sh_url ); ?>"><?php echo esc_html( $sh_title ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $sh_title ); ?>
			<?php endif; ?>
		</h2>
		<?php if ( '' !== trim( $sh_meta ) ) : ?>
			<p class="sh-tl-card__meta sh-t-timeline-meta"><?php echo esc_html( trim( $sh_meta ) ); ?></p>
		<?php endif; ?>
	</div>
</li>
