<?php
/**
 * Site header (DESIGN-SPEC §3.1 Header #1 + §3.2 Header #02). Opens <main id="main">; footer.php closes it.
 *
 * Header #1 [0,0 1440×86]: white + raster brand band (Figma image 709572bf — UNESCO, TEMASEK FOUNDATION,
 * SHARED HERITAGE logos are part of the image) · Drop Shadow/100 · bottom rule #AACFD8 1.5 inside on
 * every page except Home/Intro (O-8): pass get_header( null, array( 'header_rule' => false ) ) to hide it
 * (front page hides it by default, see sh_header_has_rule()).
 * Header #02 [0,86 1440×64]: white · Drop Shadow/100 · menu location "primary" right-aligned.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_header_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_header_cls  = 'sh-header' . ( sh_header_has_rule( $sh_header_args ) ? ' sh-header--rule' : '' );
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

<header class="<?php echo esc_attr( $sh_header_cls ); ?>" role="banner">
	<div class="sh-header__top">
		<a class="sh-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
		</a>
	</div>

	<div class="sh-header__bar">
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<button class="sh-nav-toggle" type="button" aria-expanded="false" aria-controls="sh-primary-nav">
				<span class="sh-nav-toggle__icon" aria-hidden="true"></span>
				<span class="sh-nav-toggle__label"><?php esc_html_e( 'Menu', 'sharedheritage' ); ?></span>
			</button>
			<nav id="sh-primary-nav" class="sh-nav" aria-label="<?php esc_attr_e( 'Main', 'sharedheritage' ); ?>">
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
