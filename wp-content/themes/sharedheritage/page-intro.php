<?php
/**
 * Introduction / Knowing Southeast Asia — page slug "intro" (Figma Desktop - 1471 · DESIGN-SPEC §4.2 / §4.7).
 *
 * Data: CONTENT-MODEL §7 (sh_intro_* meta of this page). Sections in Figma order:
 * Cover · Introduction · Knowing Southeast Asia · Map slider · Back.
 * Header #1 has no bottom rule on this page (Figma 699:11020).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

get_header( null, array( 'header_rule' => false ) );

$sh_intro_id = (int) get_queried_object_id();
?>
<div class="sh-intro sh-container">
	<?php
	get_template_part( 'template-parts/intro/cover', null, array( 'page_id' => $sh_intro_id ) );

	get_template_part(
		'template-parts/intro/section',
		null,
		array(
			'page_id' => $sh_intro_id,
			'prefix'  => 'sh_intro_',
			'class'   => 'sh-intro__section--intro',
			'heading' => 1,
			'image_first' => true,
		)
	);

	get_template_part(
		'template-parts/intro/section',
		null,
		array(
			'page_id' => $sh_intro_id,
			'prefix'  => 'sh_intro_knowing_',
			'class'   => 'sh-intro__section--knowing',
			'heading' => 2,
			'image_first' => false,
		)
	);

	get_template_part( 'template-parts/intro/map', null, array( 'page_id' => $sh_intro_id ) );

	get_template_part(
		'template-parts/common/back-button',
		null,
		array(
			'url'   => home_url( '/' ),
			'label' => (string) sh_get_option( 'sh_label_back' ),
			'class' => 'sh-intro__back',
		)
	);
	?>
</div>
<?php
get_footer();
