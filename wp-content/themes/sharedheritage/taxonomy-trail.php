<?php
/**
 * Trail page — Figma "Desktop - 1501" Elephant's Trail (DESIGN-SPEC §4.4 · CONTENT-MODEL §4).
 * Route /trail/<slug>/ · assets: assets/css/trail.css (auto-enqueued). Every trail uses this template (D7).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sh_trail = get_queried_object();

if ( $sh_trail instanceof WP_Term ) {
	get_template_part( 'template-parts/trail/cover', null, array( 'trail' => $sh_trail ) );
	get_template_part( 'template-parts/trail/intro', null, array( 'trail' => $sh_trail ) );
	get_template_part( 'template-parts/trail/grid', null, array( 'trail' => $sh_trail ) );
}
?>
<div class="sh-trail__back">
	<?php
	get_template_part(
		'template-parts/common/back-button',
		null,
		array(
			'url' => home_url( '/' ),
		)
	);
	?>
</div>
<?php
get_footer();
