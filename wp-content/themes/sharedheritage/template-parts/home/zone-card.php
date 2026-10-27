<?php
/**
 * Zone card (DESIGN-SPEC §3.7): image 373.333×487 (zone 1–3) / 560×294 (zone 4–5) + gap 16 + title 20/25 Heavy centered.
 * Hover: the image swaps to the hover file (0.4s). Link: zone page (term link) when sh_has_page, or sh_link_page
 * (zone 4 → Timeline). No page (zone 5, P-204) → plain block, no link.
 *
 * Title: the Figma text node has a bold first line and — when the title has a colon — a regular-weight rest
 * ("Beyond the Visible:" bold / "Animal Lore & Cosmic Order" regular · frame 1470). Rule applied to the field value:
 * text up to and including the first colon stays Heavy, the remainder is Regular.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_zone  = isset( $sh_args['zone'] ) && $sh_args['zone'] instanceof WP_Term ? $sh_args['zone'] : null;
$sh_extra = ! empty( $sh_args['extra'] );
if ( ! $sh_zone ) {
	return;
}

$sh_title = (string) sh_get_term( 'sh_card_title', $sh_zone );
if ( '' === trim( $sh_title ) ) {
	$sh_title = $sh_zone->name;
}

// Link target.
$sh_href = '';
$sh_page = (int) sh_get_term( 'sh_link_page', $sh_zone );
if ( $sh_page > 0 ) {
	$sh_href = (string) get_permalink( $sh_page );
} elseif ( (bool) sh_get_term( 'sh_has_page', $sh_zone ) ) {
	$sh_term_link = get_term_link( $sh_zone );
	$sh_href      = is_wp_error( $sh_term_link ) ? '' : (string) $sh_term_link;
}

$sh_sizes = $sh_extra ? '(min-width: 1168px) 560px, (min-width: 768px) 50vw, calc(100vw - 48px)' : '(min-width: 1168px) 374px, (min-width: 768px) 33vw, calc(100vw - 48px)';
$sh_img   = sh_home_img(
	(int) sh_get_term( 'sh_card_image', $sh_zone ),
	array(
		'alt_fallback' => '',
		'class'        => 'sh-home-zone__img',
		'sizes'        => $sh_sizes,
	)
);
$sh_hover = sh_home_img(
	(int) sh_get_term( 'sh_card_image_hover', $sh_zone ),
	array(
		'alt_fallback' => '',
		'class'        => 'sh-home-zone__img sh-home-zone__img--hover',
		'sizes'        => $sh_sizes,
	)
);

// Title → lines (the field keeps Figma's "\n"), first-colon weight rule.
$sh_lines  = preg_split( '/\R/u', $sh_title );
$sh_lines  = array_values( array_filter( array_map( 'trim', is_array( $sh_lines ) ? $sh_lines : array() ), 'strlen' ) );
$sh_html   = array();
$sh_seen   = false; // true once the colon has been passed.
foreach ( $sh_lines as $sh_line ) {
	$sh_pos = $sh_seen ? false : strpos( $sh_line, ':' );
	if ( false !== $sh_pos ) {
		$sh_html[] = '<strong>' . esc_html( substr( $sh_line, 0, $sh_pos + 1 ) ) . '</strong>' . ( strlen( $sh_line ) > $sh_pos + 1 ? '<span class="sh-home-zone__sub">' . esc_html( substr( $sh_line, $sh_pos + 1 ) ) . '</span>' : '' );
		$sh_seen   = true;
	} elseif ( $sh_seen ) {
		$sh_html[] = '<span class="sh-home-zone__sub">' . esc_html( $sh_line ) . '</span>';
	} else {
		$sh_html[] = '<strong>' . esc_html( $sh_line ) . '</strong>';
	}
}

$sh_class = 'sh-home-zone sh-home-zone--' . ( $sh_extra ? 'wide' : 'tall' ) . ' sh-home-zone--n' . (int) sh_get_term( 'sh_number', $sh_zone );
$sh_inner = '<span class="sh-home-zone__media">' . sh_home_kses( $sh_img ) . sh_home_kses( $sh_hover ) . '</span>'
	. '<span class="sh-home-zone__title sh-t-card-title">' . wp_kses(
		implode( '<br>', $sh_html ),
		array(
			'br'     => array(),
			'strong' => array(),
			'span'   => array( 'class' => true ),
		)
	) . '</span>';

if ( '' !== $sh_href ) {
	echo '<a class="' . esc_attr( $sh_class ) . '" href="' . esc_url( $sh_href ) . '">' . $sh_inner . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parts escaped above.
} else {
	echo '<div class="' . esc_attr( $sh_class ) . '">' . $sh_inner . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parts escaped above.
}
