<?php
/**
 * Cover [160,150 1120×466] — one raster image (title text is part of the picture · P-207). Field: sh_intro_cover_image.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_page = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
?>
<div class="sh-intro__cover">
	<?php
	get_template_part(
		'template-parts/intro/image',
		null,
		array(
			'id'    => (int) sh_get( 'sh_intro_cover_image', $sh_page ),
			'class' => 'sh-intro__img',
			'sizes' => '(min-width: 1168px) 1120px, 100vw',
			'eager' => true,
		)
	);
	?>
</div>
