<?php
/**
 * Trail page — Figma "Desktop - 1501" Elephant's Trail (DESIGN-SPEC §4.4 · CONTENT-MODEL §4).
 * Route /trail/<slug>/ · assets: assets/css/trail.css (auto-enqueued). Every trail uses this template (D7).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

get_header();
require_once get_theme_file_path( 'template-parts/trail/helpers.php' );

$sh_trail = get_queried_object();

$sh_tiles  = array();
$sh_layout = array();
$sh_vars   = '';
if ( $sh_trail instanceof WP_Term ) {
	$sh_tiles  = sh_trail_tiles( $sh_trail );
	$sh_layout = sh_json_value( sh_get_term( 'sh_trail_layout', $sh_trail ) );
	$sh_vars   = sh_trail_layout_vars( $sh_layout, count( $sh_tiles ) );
}
?>
<div class="sh-trail" style="<?php echo esc_attr( $sh_vars ); ?>">
<?php
if ( $sh_trail instanceof WP_Term ) {
	$sh_part_args = array(
		'trail'  => $sh_trail,
		'layout' => $sh_layout,
		'tiles'  => $sh_tiles,
	);
	get_template_part( 'template-parts/trail/cover', null, $sh_part_args );
	get_template_part( 'template-parts/trail/intro', null, $sh_part_args );
	get_template_part( 'template-parts/trail/grid', null, $sh_part_args );
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
</div>
<?php
get_footer();
