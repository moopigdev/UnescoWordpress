<?php
/**
 * Zone page — taxonomy `zone` (Zone 1–3: DESIGN-SPEC §4.3 + §4.3b · frames 3117:3684 / 3713 / 3742).
 *
 * One layout for every zone: cover (1120×410) · zone banner (1120×166) · text block (heading · sh_intro · Read more ↔
 * Hide) · trail grid (3×5 tiles 184×184) · Back.
 * Everything shown comes from the zone term (docs/CONTENT-MODEL §5) and `sh_trails( 'zone_grid' )` (§4).
 * Zone 4 has no zone page of its own (its term points at the Timeline page via sh_link_page → redirect);
 * Zone 5 has no content (sh_has_page = false) → heading + Back only, never an error.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_zone = get_queried_object();

// A zone that is shown by another page (Zone 4 → Timeline) is not rendered by this template.
if ( $sh_zone instanceof WP_Term ) {
	$sh_link_page = absint( sh_get_term( 'sh_link_page', $sh_zone ) );
	if ( $sh_link_page && 'publish' === get_post_status( $sh_link_page ) ) {
		wp_safe_redirect( get_permalink( $sh_link_page ) );
		exit;
	}
}

get_header();

if ( $sh_zone instanceof WP_Term ) :
	?>
	<div class="sh-container sh-zone" data-zone="<?php echo esc_attr( (string) (int) sh_get_term( 'sh_number', $sh_zone ) ); ?>">
		<?php
		get_template_part( 'template-parts/zone/hero', null, array( 'zone' => $sh_zone ) );
		get_template_part( 'template-parts/zone/intro', null, array( 'zone' => $sh_zone ) );
		get_template_part( 'template-parts/zone/grid', null, array( 'zone' => $sh_zone ) );
		?>
	</div>
	<?php
endif;

get_footer();
