<?php
/**
 * Zone page, closing part: object grid (D57 · DESIGN-SPEC §10.3 · 5 columns of 184×184 tiles, gap 50 · 15 / 42 / 83
 * tiles) and the Back button (grid bottom + 74). Tiles = sh_zone_grid() (CONTENT-MODEL §5b · slot order). Zones without
 * a page (sh_has_page = false, Zone 5) show no grid.
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

$sh_tiles = (bool) sh_get_term( 'sh_has_page', $sh_zone ) ? sh_zone_grid( $sh_zone ) : array();
?>
<?php if ( $sh_tiles ) : ?>
	<ul class="sh-zone__grid">
		<?php
		foreach ( $sh_tiles as $sh_tile ) {
			get_template_part(
				'template-parts/zone/tile',
				null,
				array(
					'tile' => $sh_tile,
					'zone' => $sh_zone,
				)
			);
		}
		?>
	</ul>
<?php endif; ?>

<div class="sh-zone__back">
	<?php get_template_part( 'template-parts/common/back-button', null, array( 'url' => home_url( '/' ) ) ); ?>
</div>
