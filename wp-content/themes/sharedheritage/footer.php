<?php
/**
 * Site footer. Closes <main> opened in header.php.
 *
 * Nav is a skeleton (menu location "footer") — not yet bound to Figma.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="sh-footer" role="contentinfo">
	<div class="sh-footer__inner">
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="sh-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'sharedheritage' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'sh-footer__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<p class="sh-footer__site">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
