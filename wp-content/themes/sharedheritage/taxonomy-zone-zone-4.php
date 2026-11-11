<?php
/**
 * Zone 4 — "choose an animal" page `/zone/zone-4/` (D37 · D38 · D40 · DESIGN-SPEC §4.10 · Figma 1513 `3117:3946`).
 *
 * WordPress picks this file before taxonomy-zone.php for the term `zone-4` (template hierarchy; asset slug =
 * `taxonomy-zone-zone-4`). Layout at 1440: Header · background + 17 animals [160,150 1120×540] · ZONE bar
 * [160,710 1120×166] (clickable, Zone 4 current) · Back [160,940]. Everything comes from the zone / trail terms
 * (CONTENT-MODEL §4 `sh_chooser_image` `sh_timeline_page` · §5 `sh_cover_image` `sh_banner_image`).
 * Not redirected by `sh_link_page` (cleared for zone 4 since D37; the home card links here).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_zone = get_queried_object();

get_header();

if ( $sh_zone instanceof WP_Term ) :
	?>
	<div class="sh-container sh-z4">
		<h1 class="screen-reader-text"><?php echo esc_html( $sh_zone->name ); ?></h1>
		<?php
		get_template_part( 'template-parts/zone4/animals', null, array( 'zone' => $sh_zone ) );
		get_template_part(
			'template-parts/zone4/zone-bar',
			null,
			array(
				'zone'  => $sh_zone,
				'links' => true,
			)
		);
		?>
		<div class="sh-z4__back">
			<?php get_template_part( 'template-parts/common/back-button', null, array( 'url' => home_url( '/' ) ) ); ?>
		</div>
	</div>
	<?php
endif;

get_footer();
