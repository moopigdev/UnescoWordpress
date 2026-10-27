<?php
/**
 * Trail object grid — Frame 114472 [160,829.2 1120×652]: 5 columns of 184×184 tiles, gap 50 (§4.4).
 * Tile = trail background colour (sh_tile_bg) + object tile image; hover = 210×210 image (§3.6).
 * Each tile links to the object's detail page.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_trail = isset( $sh_args['trail'] ) && $sh_args['trail'] instanceof WP_Term ? $sh_args['trail'] : null;
if ( null === $sh_trail ) {
	return;
}

$sh_objects = sh_trail_objects( $sh_trail );
$sh_bg      = sanitize_hex_color( (string) sh_get_term( 'sh_tile_bg', $sh_trail ) );
$sh_style   = $sh_bg ? '--sh-trail-tile-bg:' . $sh_bg . ';' : '';
?>
<section class="sh-trail__grid-wrap">
	<?php if ( $sh_objects ) : ?>
		<ul class="sh-trail__grid" style="<?php echo esc_attr( $sh_style ); ?>">
			<?php
			foreach ( $sh_objects as $sh_object ) :
				$sh_tile_id  = (int) sh_get( 'sh_tile_image', $sh_object->ID );
				$sh_hover_id = (int) sh_get( 'sh_tile_image_hover', $sh_object->ID );
				$sh_name     = get_the_title( $sh_object );
				?>
				<li class="sh-trail__item">
					<a class="sh-trail-tile" href="<?php echo esc_url( get_permalink( $sh_object ) ); ?>" aria-label="<?php echo esc_attr( $sh_name ); ?>">
						<span class="sh-trail-tile__box">
							<?php if ( $sh_tile_id ) : ?>
								<?php
								echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
									$sh_tile_id,
									'full',
									false,
									array(
										'class'    => 'sh-trail-tile__img',
										'alt'      => '',
										'loading'  => 'lazy',
										'decoding' => 'async',
										'sizes'    => '184px',
									)
								);
								?>
							<?php else : ?>
								<span class="sh-trail-tile__name" aria-hidden="true"><?php echo esc_html( $sh_name ); ?></span>
							<?php endif; ?>
							<?php if ( $sh_hover_id ) : ?>
								<?php
								echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
									$sh_hover_id,
									'full',
									false,
									array(
										'class'    => 'sh-trail-tile__img sh-trail-tile__img--hover',
										'alt'      => '',
										'loading'  => 'lazy',
										'decoding' => 'async',
										'sizes'    => '210px',
									)
								);
								?>
							<?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="sh-trail__empty sh-t-body"><?php esc_html_e( 'No objects in this trail yet.', 'sharedheritage' ); ?></p>
	<?php endif; ?>
</section>
