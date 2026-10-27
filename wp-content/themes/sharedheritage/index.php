<?php
/**
 * Fallback template. Page tasks add their own templates (front-page, archive-object,
 * page-timeline, single-object); this file renders anything they do not cover.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="sh-container">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'sh-entry' ); ?>>
				<?php if ( is_singular() ) : ?>
					<h1 class="sh-entry__title"><?php echo esc_html( get_the_title() ); ?></h1>
					<div class="sh-entry__content">
						<?php
						// Post content: WordPress core filters (kses on save for non-unfiltered_html users, the_content filters on output).
						the_content();
						?>
					</div>
				<?php else : ?>
					<h2 class="sh-entry__title">
						<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
					</h2>
					<div class="sh-entry__summary">
						<?php echo wp_kses_post( get_the_excerpt() ); ?>
					</div>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p class="sh-empty"><?php esc_html_e( 'Nothing found.', 'sharedheritage' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
