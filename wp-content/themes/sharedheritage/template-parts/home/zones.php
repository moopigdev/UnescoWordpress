<?php
/**
 * Home zone cards. group 'main' = Zone 1–3 (Zone 123 670:9479 [160,1198 1120×553], 3 × 373.333) ·
 * group 'extra' = Zone 4–5 (Frame 12792 670:9541 [160,2575 1120×360], 2 × 560). Zones come from sh_zones() (CONTENT-MODEL §5).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_extra = isset( $sh_args['group'] ) && 'extra' === $sh_args['group'];

$sh_zones = array_values(
	array_filter(
		sh_zones(),
		static function ( WP_Term $zone ) use ( $sh_extra ): bool {
			$number = (int) sh_get_term( 'sh_number', $zone );
			return $sh_extra ? $number >= 4 : ( $number >= 1 && $number <= 3 );
		}
	)
);
if ( ! $sh_zones ) {
	return;
}

$sh_section_id = $sh_extra ? 'sh-home-zones-extra' : 'sh-home-zones';
?>
<section id="<?php echo esc_attr( $sh_section_id ); ?>" class="<?php echo esc_attr( 'sh-home-zones sh-container ' . ( $sh_extra ? 'sh-home-zones--extra' : 'sh-home-zones--main' ) ); ?>">
	<div class="sh-home-zones__row">
		<?php
		foreach ( $sh_zones as $sh_zone ) {
			get_template_part( 'template-parts/home/zone-card', null, array( 'zone' => $sh_zone, 'extra' => $sh_extra ) );
		}
		?>
	</div>
</section>
