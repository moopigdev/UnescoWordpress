<?php
/**
 * Site footer — D47 · DESIGN-SPEC §9.1 Footer (Figma Frame 114511 3339:5248, drawn on Home, used on every page).
 * Closes <main> opened in header.php.
 *
 * One row [160,… 1120×95]: padding-left 267 (P-274: as in Figma, not centred) · padding top/bottom 5 · gap 40 (D65; 30 stacked on phones) ·
 * bottom-aligned: UNESCO logo 283.1×60 (option sh_logo_unesco_footer, 0 = the theme SVG) · Temasek group
 * ("With the Support of" 14/17.5 #49176D · gap 7 · logo 208×60, option sh_logo_temasek). 40 above · 135 below.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_unesco_id  = absint( sh_get_option( 'sh_logo_unesco_footer' ) );
$sh_temasek_id = absint( sh_get_option( 'sh_logo_temasek' ) );
$sh_support    = (string) sh_get_option( 'sh_footer_support_text' );

/**
 * Footer logo <img> of an attachment, alt = its alt text or $fallback.
 *
 * @param int    $id       Attachment id.
 * @param string $class    Class.
 * @param string $fallback Alt when the attachment has none.
 */
$sh_footer_img = static function ( int $id, string $class, string $fallback ): string {
	$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	return (string) wp_get_attachment_image(
		$id,
		'full',
		false,
		array(
			'class'    => $class,
			'alt'      => '' !== $alt ? $alt : $fallback,
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
};
?>
</main>

<footer class="sh-footer" role="contentinfo">
	<div class="sh-footer__row">
		<?php
		if ( $sh_unesco_id ) {
			echo $sh_footer_img( $sh_unesco_id, 'sh-footer__unesco', 'UNESCO' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
		} else {
			?>
			<img class="sh-footer__unesco" src="<?php echo esc_url( SH_THEME_URI . '/assets/img/common/unesco-footer-logo.svg' ); ?>" width="284" height="60" alt="UNESCO" loading="lazy" decoding="async">
			<?php
		}
		?>
		<?php if ( $sh_temasek_id || '' !== trim( $sh_support ) ) : ?>
			<div class="sh-footer__support">
				<?php if ( '' !== trim( $sh_support ) ) : ?>
					<p class="sh-footer__support-text"><?php echo esc_html( $sh_support ); ?></p>
				<?php endif; ?>
				<?php
				if ( $sh_temasek_id ) {
					echo $sh_footer_img( $sh_temasek_id, 'sh-footer__temasek', 'Temasek Foundation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
				}
				?>
			</div>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
