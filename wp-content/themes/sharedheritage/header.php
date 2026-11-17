<?php
/**
 * Site header — D47 · DESIGN-SPEC §9.1 (Figma Header 3339:5445). Opens <main id="main">; footer.php closes it.
 *
 * Header #1 [0,0 1440×86]: white · Drop Shadow/100 · Shared Heritage logo [160,4.5 220×77] (option sh_logo_header,
 * links to the home page) · bottom rule #AACFD8 1.5 inside only on pages whose Figma frame still shows it
 * (sh_header_has_rule() · P-276) — templates may force it with get_header( null, array( 'header_rule' => bool ) ).
 * Header #02 [0,86 1440×64]: white · Drop Shadow/100 · menu location "primary" right-aligned (6 items · About the
 * Project = external link in a new tab, see sh_nav_link_attributes()). Height 86 + 64 = 150 as before (§2.3).
 * The UNESCO / Temasek logos moved to footer.php (D47).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_header_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_header_cls  = 'sh-header' . ( sh_header_has_rule( $sh_header_args ) ? ' sh-header--rule' : '' );
$sh_logo_id     = absint( sh_get_option( 'sh_logo_header' ) );
$sh_site_name   = get_bloginfo( 'name' );
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
			<?php
			if ( $sh_logo_id ) {
				$sh_logo_alt = trim( (string) get_post_meta( $sh_logo_id, '_wp_attachment_image_alt', true ) );
				echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
					$sh_logo_id,
					'full',
					false,
					array(
						'class'         => 'sh-header__logo',
						'alt'           => '' !== $sh_logo_alt ? $sh_logo_alt : $sh_site_name,
						'loading'       => 'eager',
						'decoding'      => 'async',
						'fetchpriority' => 'high',
						'sizes'         => '220px',
					)
				);
			} else {
				echo '<span class="sh-header__name">' . esc_html( $sh_site_name ) . '</span>';
			}
			?>
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
