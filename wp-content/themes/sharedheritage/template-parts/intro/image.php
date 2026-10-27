<?php
/**
 * Image from an attachment-id field (no hardcoded path — D8/D10). Renders nothing when the field is empty (DOD B6).
 *
 * Args: 'id' (int attachment) · 'class' (string) · 'sizes' (string) · 'eager' (bool: above the fold).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id    = isset( $sh_args['id'] ) ? (int) $sh_args['id'] : 0;
$sh_attrs = array(
	'class'    => isset( $sh_args['class'] ) ? (string) $sh_args['class'] : '',
	'decoding' => 'async',
	'loading'  => ! empty( $sh_args['eager'] ) ? 'eager' : 'lazy',
);
if ( ! empty( $sh_args['sizes'] ) ) {
	$sh_attrs['sizes'] = (string) $sh_args['sizes'];
}
if ( ! empty( $sh_args['eager'] ) ) {
	$sh_attrs['fetchpriority'] = 'high';
}

if ( $sh_id > 0 ) {
	// wp_get_attachment_image() escapes its attributes; returns '' for a missing attachment.
	echo wp_get_attachment_image( $sh_id, 'full', false, $sh_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
