<?php
/**
 * Navigator — 4 dots [176,587 30×150] (871:2239 · DESIGN-SPEC §3.5). Click scrolls to a section (0.3s, assets/js/home.js);
 * the href anchor is the no-JS fallback. Dots carry no text in the design → labels are screen-reader only.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_pid   = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_intro = (string) sh_get( 'sh_home_intro_title', $sh_pid );
$sh_trail = (string) sh_get( 'sh_home_trails_title', $sh_pid );

$sh_dots = array(
	'sh-home-intro'       => '' !== $sh_intro ? $sh_intro : __( 'Introduction', 'sharedheritage' ),
	'sh-home-zones'       => __( 'Zones 1 to 3', 'sharedheritage' ),
	'sh-home-trails'      => '' !== $sh_trail ? $sh_trail : __( 'Trails', 'sharedheritage' ),
	'sh-home-zones-extra' => __( 'Zones 4 and 5', 'sharedheritage' ),
);
?>
<nav class="sh-home-nav" aria-label="<?php esc_attr_e( 'Sections', 'sharedheritage' ); ?>">
	<?php foreach ( $sh_dots as $sh_target => $sh_label ) : ?>
		<a class="sh-home-nav__dot" href="<?php echo esc_url( '#' . $sh_target ); ?>" data-sh-scroll><span class="screen-reader-text"><?php echo esc_html( $sh_label ); ?></span></a>
	<?php endforeach; ?>
</nav>
