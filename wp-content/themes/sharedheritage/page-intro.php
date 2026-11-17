<?php
/**
 * Introduction — page slug "intro" (D50 · DESIGN-SPEC §9.4 · Figma section 3339:6117).
 *
 * One page, four chapters switched in place (hash #1–#4 · assets/js/intro.js). Orange header + environment heading are the
 * same for every chapter; the map and the content below change. Data: sh_intro_head() · sh_intro_chapters() (CONTENT-MODEL §7).
 * Without JS the four chapters are shown one after another. No bottom rule under Header #1 on this page (§9.4 A).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

require_once get_theme_file_path( 'template-parts/intro/helpers.php' );

get_header( null, array( 'header_rule' => false ) );

$sh_intro_id = (int) get_queried_object_id();
$sh_head     = sh_intro_head( $sh_intro_id );
$sh_chapters = sh_intro_chapters( $sh_intro_id );
$sh_env_font = isset( $sh_head['layout']['environment_heading']['font'] ) ? sh_intro_vars( sh_intro_font_vars( $sh_head['layout']['environment_heading']['font'] ) ) : '';
$sh_map_ui   = isset( $sh_head['layout']['map_ui'] ) && is_array( $sh_head['layout']['map_ui'] ) ? $sh_head['layout']['map_ui'] : array();
?>
<div class="sh-intro" data-sh-intro>
	<?php
	get_template_part(
		'template-parts/intro/head',
		null,
		array(
			'head'   => $sh_head,
			'target' => 'sh-intro-env',
		)
	);
	?>
	<?php if ( '' !== trim( $sh_head['env_heading'] ) ) : ?>
		<div class="sh-intro__env" id="sh-intro-env">
			<h2 class="sh-intro__env-title" style="<?php echo esc_attr( $sh_env_font ); ?>"><?php echo wp_kses( nl2br( esc_html( $sh_head['env_heading'] ), false ), array( 'br' => array() ) ); ?></h2>
		</div>
	<?php endif; ?>
	<?php if ( $sh_chapters ) : ?>
		<?php // Show only the chapter of the URL hash from the first paint (intro.js removes these classes once it runs; without JS all chapters stay visible). ?>
		<script>(function(){var n=parseInt(location.hash.replace(/^#(chapter-)?/,''),10),c=<?php echo (int) count( $sh_chapters ); ?>,d=document.documentElement;n=n>=1&&n<=c?n:1;d.classList.add('sh-intro-js','sh-intro-c'+n);}());</script>
	<?php endif; ?>
	<div class="sh-intro__stage" data-sh-stage>
		<?php
		if ( $sh_chapters ) {
			get_template_part(
				'template-parts/intro/controls',
				null,
				array(
					'chapters' => $sh_chapters,
					'ui'       => $sh_map_ui,
				)
			);
		}
		foreach ( $sh_chapters as $sh_chapter ) {
			get_template_part( 'template-parts/intro/chapter', null, array( 'chapter' => $sh_chapter ) );
		}
		?>
	</div>
</div>
<?php
get_footer();
