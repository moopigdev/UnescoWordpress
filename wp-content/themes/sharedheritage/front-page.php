<?php
/**
 * Home — Figma "Desktop - 1470" (670:1152 · DESIGN-SPEC §4.1). Data: CONTENT-MODEL §6 (page "home") · §5 zones · §4 trails.
 *
 * Sections: hero cover · introduction + zone bar · zone 1–3 cards · trails (footprint video → trail icons)
 * · zone 4/5 cards · navigator (4 dots). Header has no bottom rule on the front page by default.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

require_once get_theme_file_path( 'template-parts/home/helpers.php' );

get_header();

$sh_home_page_id = sh_page_id( 'front' );
$sh_home_args    = array( 'page_id' => $sh_home_page_id );
?>
<div class="sh-home">
	<?php
	get_template_part( 'template-parts/home/hero', null, $sh_home_args );
	get_template_part( 'template-parts/home/intro', null, $sh_home_args );
	get_template_part( 'template-parts/home/zones', null, array( 'group' => 'main' ) );
	get_template_part( 'template-parts/home/trails', null, $sh_home_args );
	get_template_part( 'template-parts/home/zones', null, array( 'group' => 'extra' ) );
	get_template_part( 'template-parts/home/navigator', null, $sh_home_args );
	?>
</div>
<?php
get_footer();
