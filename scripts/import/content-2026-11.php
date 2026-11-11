<?php
/**
 * Importer: content of the Figma 2026-11-11 round (BACKLOG #7 #8 #13 #16 #19 · D37 D38 D40) — shared by the fresh
 * import (entities.php · objects.php) and the refresh steps for existing sites (refresh-2026-11.php):
 *   zones 1–3  name / card title (seed name · name_raw) · banner (banner_image.file → sh_cover_image, alt = name_banner)
 *              · sh_intro (intro_short) · sh_intro_full (body_full)
 *   zone 4     Zone 4 chooser page (zone4-chooser.json): own zone page (sh_link_page empty) · sh_cover_image = dashed
 *              background · sh_banner_image = zone bar (Zone 4 active) · trail sh_chooser_image per animal
 *   zone 5     has_page + "page" object (CONTENT-MODEL §12) → cover · zone bar · cards · image row · history
 *   objects    post_title / sh_title_raw / sh_caption from figma_2026_11 (slug never changes)
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attachment for a seed asset value (refs.php sh_import_seed_asset_file(): "<sub>/<file>" or a top-level file /
 * imageRef). null = no value (field untouched) · 0 (+ warning) = file missing.
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param mixed                $value Seed value.
 * @param string               $title Attachment title.
 * @param string               $alt   Alt text set on the attachment when it has none ('' = leave).
 */
function sh_imp_seed_asset( array $map, $value, string $title, string $alt = '' ): ?int {
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return null;
	}
	$dir  = sh_imp_config()['assets'];
	$file = sh_import_seed_asset_file( $map, $value, $dir );
	if ( '' === $file ) {
		sh_imp_warn( 'seed asset without file: ' . $value . ' (' . $title . ')' );
		return 0;
	}
	$ext = (string) pathinfo( $file, PATHINFO_EXTENSION );
	$id  = sh_imp_attachment( $dir . '/' . $file, 'asset:' . ( '' === $ext ? $file : substr( $file, 0, -strlen( $ext ) - 1 ) ), $title );
	if ( $id && '' !== $alt && '' === (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $id;
}

/**
 * Seed text with one line break per paragraph (zones.json) → paragraphs separated by a blank line (sh_paragraphs()).
 *
 * @param mixed $text Seed text.
 */
function sh_import_blank_line_paragraphs( $text ): string {
	if ( ! is_string( $text ) ) {
		return '';
	}
	$parts = preg_split( '/\R+/u', trim( $text ) );
	return implode( "\n\n", array_values( array_filter( array_map( 'trim', is_array( $parts ) ? $parts : array() ), 'strlen' ) ) );
}

/**
 * Zone number that has the "choose an animal" page (D37: zone4-chooser.json = Zone 4) · 0 = no chooser seed.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_chooser_zone( array $seed ): int {
	return ! empty( $seed['zone4-chooser']['animals'] ) ? 4 : 0;
}

/**
 * 2026-11 zone fields (fresh import): meta key => value. Wins over the caller's array (sh_cover_image of zones 1–3).
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $map  image-map.json.
 * @param array<string, mixed> $z    Seed zone.
 * @return array<string, mixed>
 */
function sh_import_zone_meta_2026_11( array $seed, array $map, array $z ): array {
	$n    = (int) $z['number'];
	$meta = array(
		'sh_intro'      => isset( $z['intro_short'] ) ? sh_import_blank_line_paragraphs( $z['intro_short'] ) : null,
		'sh_intro_full' => isset( $z['body_full'] ) ? sh_import_blank_line_paragraphs( $z['body_full'] ) : null,
	);
	if ( isset( $z['banner_image']['file'] ) ) {
		$meta['sh_cover_image'] = sh_imp_seed_asset( $map, $z['banner_image']['file'], 'Zone ' . $n . ' banner', isset( $z['name_banner'] ) ? (string) $z['name_banner'] : '' );
	}
	if ( sh_import_chooser_zone( $seed ) === $n ) {
		$ch                      = $seed['zone4-chooser']['_meta'];
		$meta['sh_cover_image']  = sh_imp_seed_asset( $map, isset( $ch['cover_bg']['file_cropped'] ) ? $ch['cover_bg']['file_cropped'] : null, 'Zone ' . $n . ' choose an animal background' );
		$meta['sh_banner_image'] = sh_imp_seed_asset( $map, isset( $ch['zone_bar']['file_cropped'] ) ? $ch['zone_bar']['file_cropped'] : null, 'Zone ' . $n . ' zone bar' );
	}
	foreach ( sh_import_zone_page_images( $z ) as $key => $value ) {
		$meta[ $key ] = sh_imp_seed_asset( $map, $value, 'Zone ' . $n . ' ' . str_replace( array( 'sh_page_', 'sh_', '_' ), array( '', '', ' ' ), $key ) );
	}
	// null = nothing in the seed: dropped so the caller's value (e.g. the image-map cover of zones without a banner) stays.
	return array_filter( $meta, static fn( $v ): bool => null !== $v ) + sh_import_zone_page_flags( $z );
}

/**
 * Zone page card flags from the seed "page" object: sh_page_card_<n>_soon (D40 "coming soon").
 *
 * @param array<string, mixed> $z Seed zone.
 * @return array<string, bool>
 */
function sh_import_zone_page_flags( array $z ): array {
	$out   = array();
	$cards = isset( $z['page']['cards'] ) && is_array( $z['page']['cards'] ) ? array_values( $z['page']['cards'] ) : array();
	foreach ( $cards as $i => $c ) {
		if ( $i < 4 && is_array( $c ) && isset( $c['soon'] ) ) {
			$out[ 'sh_page_card_' . ( $i + 1 ) . '_soon' ] = (bool) $c['soon'];
		}
	}
	return $out;
}

/**
 * Seed chooser animal of a trail (zone4-chooser.json animals[] by trail_slug) or null.
 *
 * @param array<string, mixed> $seed Seed files.
 * @param string               $slug Trail slug.
 * @return array<string, mixed>|null
 */
function sh_import_chooser_animal( array $seed, string $slug ): ?array {
	foreach ( (array) ( isset( $seed['zone4-chooser']['animals'] ) ? $seed['zone4-chooser']['animals'] : array() ) as $a ) {
		if ( is_array( $a ) && isset( $a['trail_slug'] ) && $slug === $a['trail_slug'] ) {
			return $a;
		}
	}
	return null;
}

/**
 * sh_chooser_image of a trail (null = no animal in the seed).
 *
 * @param array<string, mixed> $seed Seed files.
 * @param array<string, mixed> $map  image-map.json.
 * @param string               $slug Trail slug.
 * @param string               $name Trail name (attachment title).
 */
function sh_import_trail_chooser_image( array $seed, array $map, string $slug, string $name ): ?int {
	$a = sh_import_chooser_animal( $seed, $slug );
	return null === $a ? null : sh_imp_seed_asset( $map, isset( $a['asset'] ) ? $a['asset'] : null, $name . ' (choose an animal)' );
}

/**
 * Object title texts (D40): figma_2026_11.{title,title_raw,caption} when present, else the main seed fields.
 *
 * @param array<string, mixed> $o Seed object.
 * @return array{post_title:string,sh_title_raw:string,sh_caption:string}
 */
function sh_import_object_text_2026_11( array $o ): array {
	$f     = isset( $o['figma_2026_11'] ) && is_array( $o['figma_2026_11'] ) ? $o['figma_2026_11'] : array();
	$pick  = static fn( string $k ): string => isset( $f[ $k ] ) && is_string( $f[ $k ] ) && '' !== $f[ $k ] ? $f[ $k ] : ( isset( $o[ $k ] ) && is_string( $o[ $k ] ) ? $o[ $k ] : '' );
	$title = $pick( 'title' );
	// title_raw belongs to its title: the Figma raw title only with the Figma title (missing = the title itself).
	$raw = isset( $f['title'] ) && is_string( $f['title'] ) && '' !== $f['title']
		? ( isset( $f['title_raw'] ) && is_string( $f['title_raw'] ) && '' !== $f['title_raw'] ? $f['title_raw'] : $title )
		: ( isset( $o['title_raw'] ) && is_string( $o['title_raw'] ) ? $o['title_raw'] : '' );
	return array(
		'post_title'   => $title,
		'sh_title_raw' => $raw !== $title ? $raw : '',
		'sh_caption'   => $pick( 'caption' ),
	);
}
