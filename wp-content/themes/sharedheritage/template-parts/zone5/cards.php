<?php
/**
 * Zone 5 cards (`Frame 114485` [160,770 1120×459] · 4 × 280×459.28, no gap) — DESIGN-SPEC §4.11a #3.
 * Card n = `sh_page_card_<n>_image` (+ `_image_hover` cross-fade) · `_soon` → "Coming soon" label
 * (`sh_label_coming_soon` · P-173) and never a link (D40) · otherwise `_link` (published page) makes it a link.
 * A card without an image is not shown.
 *
 * Args: zone (WP_Term).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_zone = isset( $sh_args['zone'] ) && $sh_args['zone'] instanceof WP_Term ? $sh_args['zone'] : null;
if ( ! $sh_zone ) {
	return;
}

$sh_cards = array();
for ( $sh_n = 1; $sh_n <= 4; $sh_n++ ) {
	$sh_image = absint( sh_get_term( 'sh_page_card_' . $sh_n . '_image', $sh_zone ) );
	if ( ! $sh_image || ! wp_attachment_is_image( $sh_image ) ) {
		continue;
	}
	$sh_soon = (bool) sh_get_term( 'sh_page_card_' . $sh_n . '_soon', $sh_zone );
	$sh_page = $sh_soon ? 0 : absint( sh_get_term( 'sh_page_card_' . $sh_n . '_link', $sh_zone ) );
	$sh_url  = $sh_page && 'publish' === get_post_status( $sh_page ) ? (string) get_permalink( $sh_page ) : '';

	$sh_cards[] = array(
		'image' => $sh_image,
		'hover' => absint( sh_get_term( 'sh_page_card_' . $sh_n . '_image_hover', $sh_zone ) ),
		'soon'  => $sh_soon,
		'url'   => $sh_url,
		// Accessible name of a linked card when its image has no alt text: the linked page's title.
		'label' => '' !== $sh_url && '' === trim( (string) get_post_meta( $sh_image, '_wp_attachment_image_alt', true ) ) ? get_the_title( $sh_page ) : '',
	);
}
if ( ! $sh_cards ) {
	return;
}

$sh_soon_label = (string) sh_get_option( 'sh_label_coming_soon' );
if ( '' === trim( $sh_soon_label ) ) {
	$sh_soon_label = __( 'Coming soon', 'sharedheritage' );
}
?>
<ul class="sh-z5__cards">
	<?php foreach ( $sh_cards as $sh_card ) : ?>
		<?php
		$sh_tag  = '' !== $sh_card['url'] ? 'a' : 'div';
		$sh_attr = '' !== $sh_card['url'] ? ' href="' . esc_url( $sh_card['url'] ) . '"' : '';
		if ( '' !== $sh_card['label'] ) {
			$sh_attr .= ' aria-label="' . esc_attr( wp_strip_all_tags( $sh_card['label'] ) ) . '"';
		}
		?>
		<li class="sh-z5__card<?php echo $sh_card['soon'] ? ' sh-z5__card--soon' : ''; ?>">
			<<?php echo esc_html( $sh_tag ); ?> class="sh-z5__card-box"<?php echo $sh_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above. ?>>
				<?php
				get_template_part(
					'template-parts/zone5/swap',
					null,
					array(
						'image' => $sh_card['image'],
						'hover' => $sh_card['hover'],
						'class' => 'sh-z5__card',
						'sizes' => '(min-width: 1168px) 280px, (min-width: 768px) 25vw, 50vw',
					)
				);
				?>
				<?php if ( $sh_card['soon'] ) : ?>
					<span class="sh-z5__soon"><?php echo esc_html( $sh_soon_label ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $sh_tag ); ?>>
		</li>
	<?php endforeach; ?>
</ul>
