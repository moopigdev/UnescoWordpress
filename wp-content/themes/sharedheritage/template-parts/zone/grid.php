<?php
/**
 * Zone page, closing part: trail grid (§4.3 `Frame 114472` · 3 rows × 5 tiles 184×184, gap 50) and the Back button.
 * Tiles = sh_trails( 'zone_grid' ) (CONTENT-MODEL §4 · sorted by sh_zone_grid_order). Zones without a page
 * (sh_has_page = false, Zone 5) show no grid.
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

$sh_trails = (bool) sh_get_term( 'sh_has_page', $sh_zone ) ? sh_trails( 'zone_grid' ) : array();
?>
<?php if ( $sh_trails ) : ?>
	<ul class="sh-zone__grid">
		<?php
		foreach ( $sh_trails as $sh_trail ) {
			get_template_part( 'template-parts/zone/tile', null, array( 'trail' => $sh_trail ) );
		}
		?>
	</ul>
<?php endif; ?>

<div class="sh-zone__back">
	<?php get_template_part( 'template-parts/common/back-button', null, array( 'url' => home_url( '/' ) ) ); ?>
</div>
