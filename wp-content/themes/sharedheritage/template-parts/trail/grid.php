<?php
/**
 * Trail object grid — Frame 114472 [160,829.2 1120×652]: 5 columns of 184×184 tiles, gap 50 (§4.4).
 * Tile = trail background colour (sh_tile_bg) + object tile image; hover = 210×210 image (§3.6).
 * Tiles come from sh_trail_tiles(): each object links to its detail page; a tile without a page (url '' — Makara 2 ·
 * Chicken & Rooster #11, DESIGN-SPEC §8.2) is a plain image: no link (same hover zoom, normal cursor).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_trail = isset( $sh_args['trail'] ) && $sh_args['trail'] instanceof WP_Term ? $sh_args['trail'] : null;
if ( null === $sh_trail ) {
	return;
}

$sh_tiles = isset( $sh_args['tiles'] ) && is_array( $sh_args['tiles'] ) ? $sh_args['tiles'] : sh_trail_tiles( $sh_trail );
$sh_bg      = sanitize_hex_color( (string) sh_get_term( 'sh_tile_bg', $sh_trail ) );
$sh_style   = $sh_bg ? '--sh-trail-tile-bg:' . $sh_bg . ';' : '';
?>
<section class="sh-trail__grid-wrap">
	<?php if ( $sh_tiles ) : ?>
		<ul class="sh-trail__grid" style="<?php echo esc_attr( $sh_style ); ?>">
			<?php
			foreach ( $sh_tiles as $sh_tile ) :
				$sh_tile_id  = (int) ( $sh_tile['image'] ?? 0 );
				$sh_hover_id = (int) ( $sh_tile['hover'] ?? 0 );
				$sh_name     = (string) ( $sh_tile['label'] ?? '' );
				$sh_url      = (string) ( $sh_tile['url'] ?? '' );
				$sh_link     = '' !== $sh_url;
				?>
				<li class="sh-trail__item">
					<?php if ( $sh_link ) : ?>
						<a class="sh-trail-tile" href="<?php echo esc_url( $sh_url ); ?>" aria-label="<?php echo esc_attr( $sh_name ); ?>">
					<?php else : ?>
						<span class="sh-trail-tile sh-trail-tile--static">
					<?php endif; ?>
						<span class="sh-trail-tile__box">
							<?php if ( $sh_tile_id ) : ?>
								<?php
								echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
									$sh_tile_id,
									'full',
									false,
									array(
										'class'    => 'sh-trail-tile__img',
										'alt'      => $sh_link ? '' : $sh_name,
										'loading'  => 'lazy',
										'decoding' => 'async',
										'sizes'    => '184px',
									)
								);
								?>
							<?php elseif ( '' !== $sh_name ) : ?>
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
					<?php echo $sh_link ? '</a>' : '</span>'; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="sh-trail__empty sh-t-body"><?php esc_html_e( 'No objects in this trail yet.', 'sharedheritage' ); ?></p>
	<?php endif; ?>
</section>
