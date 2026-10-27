<?php
/**
 * Site header. Opens <main id="main">; footer.php closes it.
 *
 * Nav is a skeleton (menu location "primary", managed in Appearance > Menus) — not yet bound to Figma.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="sh-skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'sharedheritage' ); ?></a>

<header class="sh-header" role="banner">
	<div class="sh-header__inner">
		<a class="sh-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
		</a>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav class="sh-nav" aria-label="<?php esc_attr_e( 'Primary', 'sharedheritage' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'sh-nav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</header>

<main id="main" class="sh-main">
