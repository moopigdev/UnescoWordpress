<?php
/**
 * Navigator — 4 dots [176,587 30×150] (3117:3638 · DESIGN-SPEC §3.5). Click scrolls (0.3s, assets/js/home.js) to:
 * 1 the top of the page (Cover + Introduction) · 2 the ZONE 1–5 bar, kept fully in view above the zone 1–3 cards
 * (BACKLOG #20) · 3 Trails · 4 Zone 4–5. The href anchor is the no-JS fallback. Dots carry no text in the design →
 * labels are screen-reader only. The space kept above a target = its CSS scroll-margin-top (home.css).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_pid   = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_intro = (string) sh_get( 'sh_home_intro_title', $sh_pid );
$sh_trail = (string) sh_get( 'sh_home_trails_title', $sh_pid );

// Target id => label. '#top' scrolls to the very top of the page (HTML fragment rule, also without JS).
$sh_dots = array(
	'top'                 => '' !== $sh_intro ? $sh_intro : __( 'Introduction', 'sharedheritage' ),
	'sh-home-zonebar'     => __( 'Zones 1 to 3', 'sharedheritage' ),
	'sh-home-trails'      => '' !== $sh_trail ? $sh_trail : __( 'Trails', 'sharedheritage' ),
	'sh-home-zones-extra' => __( 'Zones 4 and 5', 'sharedheritage' ),
);
?>
<nav class="sh-home-nav" aria-label="<?php esc_attr_e( 'Sections', 'sharedheritage' ); ?>">
	<?php foreach ( $sh_dots as $sh_target => $sh_label ) : ?>
		<a class="sh-home-nav__dot" href="<?php echo esc_url( '#' . $sh_target ); ?>" data-sh-scroll><span class="screen-reader-text"><?php echo esc_html( $sh_label ); ?></span></a>
	<?php endforeach; ?>
</nav>
