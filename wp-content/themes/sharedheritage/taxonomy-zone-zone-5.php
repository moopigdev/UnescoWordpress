<?php
/**
 * Zone 5 main page `/zone/zone-5/` "The Curious Minds’ Play Zone" (D40 · DESIGN-SPEC §4.11a · Figma 1514 `3117:3955`).
 *
 * WordPress picks this file before taxonomy-zone.php for the term `zone-5` (asset slug = `taxonomy-zone-zone-5`).
 * Layout at 1440 (column gap 25): hero (Cover 551×384 + H1 / intro) · ZONE bar (clickable, Zone 5 current) ·
 * 4 cards 280×459 (hover swap) · 4 strip images 280×123 · History 1120×453 (hover swap) · Back [160,1965].
 * Everything comes from the zone term (CONTENT-MODEL §5 · tab "Zone page cards") and Settings (`sh_label_coming_soon`);
 * every part is skipped when its data is empty, so an unfilled zone shows its name, the bar and Back only.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_zone = get_queried_object();

get_header();

if ( $sh_zone instanceof WP_Term ) :
	?>
	<div class="sh-container sh-z5">
		<?php
		get_template_part( 'template-parts/zone5/hero', null, array( 'zone' => $sh_zone ) );
		get_template_part(
			'template-parts/zone4/zone-bar',
			null,
			array(
				'zone'  => $sh_zone,
				'links' => true,
			)
		);
		get_template_part( 'template-parts/zone5/cards', null, array( 'zone' => $sh_zone ) );
		get_template_part( 'template-parts/zone5/strip', null, array( 'zone' => $sh_zone ) );
		get_template_part( 'template-parts/zone5/history', null, array( 'zone' => $sh_zone ) );
		?>
		<div class="sh-z5__back">
			<?php get_template_part( 'template-parts/common/back-button', null, array( 'url' => home_url( '/' ) ) ); ?>
		</div>
	</div>
	<?php
endif;

get_footer();
