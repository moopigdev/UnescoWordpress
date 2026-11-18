<?php
/**
 * Refresh step of the 2026-11-18 round (elephanttext-1118 · D61): the 15 elephant objects use the title / caption of the
 * current Figma (seed objects.json: title · title_raw · caption · the values before D61 in title_prev · title_raw_prev ·
 * caption_prev) and the two timeline cards whose Figma name differs from the old one (timeline-elephant.json
 * items[].title_prev).
 * Rule: a field is rewritten only while the site still holds a value the IMPORTER wrote (what the seed held before D61 ·
 * every earlier importer value of legacy-values.json) — a value an admin typed stays and is listed in kept_admin. A field that
 * already has the new text is left as it is (the 2026-11-11 import already wrote the Figma text to objects: nothing to do).
 * Never touched: slug (post_name · live URLs) · anything missing on the site (nothing is created or deleted).
 * Works with SH_IMPORT_ONLY=refresh (deploy.sh --refresh-only) and on a fresh import (finds everything already set).
 * Step 2026-11-18-home-hero (homehero-1118 · BACKLOG #43): the Home hero image becomes the one of the current Figma
 * (88d09cb8 "Southeast Asia") only while the Home page still holds the attachment the importer made from the old ref.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Steps of this file: id => what it changes (merged into sh_import_refresh_steps()).
 *
 * @return array<string, string>
 */
function sh_import_refresh_steps_2026_11_18(): array {
	return array(
		// elephanttext-1118 (BACKLOG #42 · D61): elephant object titles / captions + timeline card names follow the new Figma.
		'2026-11-18-elephant-text' => 'elephant objects post_title · sh_title_raw · sh_caption + timeline card names → new Figma text, only while the site still has what the importer wrote (title_prev · title_raw_prev · caption_prev) · slug unchanged · an admin edit stays: kept_admin',
		// homehero-1118 (BACKLOG #43): Home hero image = Figma 88d09cb8 (the text in the picture reads "Southeast Asia").
		'2026-11-18-home-hero'     => 'Home page sh_home_hero_image 2e3fa9c4 (South-East Asia) → 88d09cb8 (Southeast Asia), only while the field is still the attachment the importer made from 2e3fa9c4 · an image an admin chose stays: kept_admin · the old attachment stays in the media library',
	);
}

/** Figma imageRef the Home hero used before 2026-11-18 (image-map.json pages.home.hero until homehero-1118). */
const SH_IMPORT_HOME_HERO_PREV_1118 = '2e3fa9c46cbd208659aa21cdcd503d65ff002aee';

/**
 * Step 2026-11-18-home-hero: pages.home.hero of image-map.json is the new image. The Home page's sh_home_hero_image
 * is switched to it only while it is exactly the attachment the importer made from the old ref (import key
 * asset:<old ref>). Anything else (an admin picked another image · the field was cleared) = kept_admin. The old
 * attachment is never deleted (the Introduction cover still uses it). Page missing on this site = nothing to do.
 * Returns false when the new file is missing (warning raised) — the step runs again on the next import.
 *
 * @param array<string, mixed>                    $map   image-map.json.
 * @param array<string, array{id:int,write:bool}> $pages Pages.
 */
function sh_import_refresh_home_hero_1118( array $map, array $pages ): bool {
	$page = isset( $pages['front']['id'] ) ? (int) $pages['front']['id'] : 0;
	$ref  = isset( $map['pages']['home']['hero'] ) ? (string) $map['pages']['home']['hero'] : '';
	if ( ! $page || '' === $ref || SH_IMPORT_HOME_HERO_PREV_1118 === $ref ) {
		return true;
	}
	$new = sh_imp_image( $map, $ref, 'Home hero' );
	if ( ! $new ) {
		return false;
	}
	$cur = (int) get_post_meta( $page, 'sh_home_hero_image', true );
	if ( $cur === $new ) {
		return true; // Already the new image (fresh import / done before).
	}
	$prev = sh_imp_find_attachment( 'asset:' . SH_IMPORT_HOME_HERO_PREV_1118 );
	if ( $cur && $prev && $cur === $prev ) {
		sh_imp_meta( 'post', $page, array( 'sh_home_hero_image' => $new ) );
		sh_imp_count( 'updated', 'home_hero_swapped' );
	} else {
		sh_imp_keep( "page:front sh_home_hero_image (site attachment $cur is not the one the import made from the old image · new Figma image not applied)" );
	}
	return true;
}

/**
 * Text with every run of white space (line breaks included) as one space.
 *
 * @param mixed $s Text.
 */
function sh_imp_squash( $s ): string {
	return trim( (string) preg_replace( '/\s+/u', ' ', is_string( $s ) ? $s : '' ) );
}

/**
 * Importer values of one field: the seed "prev" value + the older importer values of legacy-values.json.
 *
 * @param array<string, mixed> $o     Seed object.
 * @param string               $field Meta key or post_title.
 * @param string[]             $prev  Values the seed held before D61.
 * @return string[]
 */
function sh_import_elephant_text_olds( array $o, string $field, array $prev ): array {
	$legacy = sh_imp_legacy();
	$slug   = (string) $o['slug'];
	return array_merge( $prev, isset( $legacy['objects'][ $slug ][ $field ] ) ? array_filter( (array) $legacy['objects'][ $slug ][ $field ], 'is_string' ) : array() );
}

/**
 * Elephant object $o of the seed → the site's object (if any): post_title, then sh_title_raw (only together with a
 * title that is now the new one), then sh_caption.
 *
 * @param array<string, mixed> $o Seed object (has title_prev / caption_prev).
 */
function sh_import_refresh_elephant_object_text( array $o ): void {
	$slug = (string) $o['slug'];
	$id   = sh_imp_find_post( 'object', 'object:' . $slug );
	$post = $id ? get_post( $id ) : null;
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	$new       = sh_import_object_text_2026_11( $o );
	$title_old = isset( $o['title_prev'] ) && is_string( $o['title_prev'] ) ? $o['title_prev'] : '';
	$raw_old   = isset( $o['title_raw_prev'] ) && is_string( $o['title_raw_prev'] ) ? $o['title_raw_prev'] : '';

	// post_title.
	$title_ok = sh_imp_same_text( $post->post_title, $new['post_title'], null );
	if ( ! $title_ok ) {
		if ( sh_imp_was_imported( $post->post_title, sh_import_elephant_text_olds( $o, 'post_title', array( $title_old ) ), null ) ) {
			$res = wp_update_post(
				wp_slash(
					array(
						'ID'         => $post->ID,
						'post_title' => $new['post_title'],
						'post_name'  => $post->post_name, // Live URL stays (D61).
					)
				),
				true
			);
			if ( is_wp_error( $res ) ) {
				throw new RuntimeException( 'object ' . $slug . ': ' . $res->get_error_message() );
			}
			sh_imp_count( 'updated', 'elephant_title' );
			$title_ok = true;
		} else {
			sh_imp_keep( "object:$slug post_title (site \"" . $post->post_title . '" · seed "' . $new['post_title'] . '")' );
		}
	}

	// sh_title_raw: '' on the site = "same as the title". Old importer value = what the seed held ('' when it equalled the
	// title) · compared with line breaks as one space (the team flattened Pod Duang's on 2026-10-28 · P-401).
	$cur_raw  = (string) get_post_meta( $post->ID, 'sh_title_raw', true );
	$raw_def  = sh_imp_field_def( 'object', 'sh_title_raw' );
	$raw_same = sh_imp_same_text( $cur_raw, $new['sh_title_raw'], $raw_def ) || sh_imp_squash( $cur_raw ) === sh_imp_squash( '' !== $new['sh_title_raw'] ? $new['sh_title_raw'] : $new['post_title'] );
	if ( ! $raw_same ) {
		$olds = sh_import_elephant_text_olds( $o, 'sh_title_raw', array( $raw_old !== $title_old ? $raw_old : '' ) );
		$was  = false;
		foreach ( $olds as $old ) {
			$was = $was || ( '' === $old ? '' === $cur_raw : sh_imp_squash( $cur_raw ) === sh_imp_squash( $old ) );
		}
		if ( $was && $title_ok ) {
			update_post_meta( $post->ID, 'sh_title_raw', wp_slash( $new['sh_title_raw'] ) );
			sh_imp_count( 'updated', 'elephant_title_raw' );
		} else {
			sh_imp_keep( "object:$slug sh_title_raw (" . ( $was ? 'the title is an admin value' : 'admin value' ) . ' · site "' . $cur_raw . '" · seed "' . $new['sh_title_raw'] . '")' );
		}
	}

	// sh_caption: caption_prev null = the seed had none (the site's is empty = the importer's).
	$cur_cap = get_post_meta( $post->ID, 'sh_caption', true );
	$cap_def = sh_imp_field_def( 'object', 'sh_caption' );
	if ( '' !== $new['sh_caption'] && ! sh_imp_same_text( $cur_cap, $new['sh_caption'], $cap_def ) ) {
		$cap_old = isset( $o['caption_prev'] ) && is_string( $o['caption_prev'] ) ? $o['caption_prev'] : '';
		if ( '' === $cur_cap || sh_imp_was_imported( $cur_cap, sh_import_elephant_text_olds( $o, 'sh_caption', array( $cap_old ) ), $cap_def ) ) {
			sh_imp_meta( 'post', $post->ID, array( 'sh_caption' => $new['sh_caption'] ) );
			sh_imp_count( 'updated', 'elephant_caption' );
		} else {
			sh_imp_keep( "object:$slug sh_caption (admin text kept)" );
		}
	}
}

/**
 * Step 2026-11-18-elephant-text: the objects with title_prev / caption_prev in the seed + the timeline cards with
 * items[].title_prev (post_title of the card, import key timeline:elephant:<card_order>).
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_elephant_text_1118( array $seed ): void {
	foreach ( sh_import_all_objects( $seed ) as $o ) {
		if ( array_key_exists( 'title_prev', $o ) || array_key_exists( 'caption_prev', $o ) ) {
			sh_import_refresh_elephant_object_text( $o );
		}
	}
	foreach ( (array) $seed['timeline-elephant']['items'] as $it ) {
		if ( empty( $it['title_prev'] ) || ! is_string( $it['title_prev'] ) ) {
			continue;
		}
		$n    = (int) $it['card_order'];
		$id   = sh_imp_find_post( 'timeline_item', 'timeline:elephant:' . $n );
		$post = $id ? get_post( $id ) : null;
		if ( ! $post instanceof WP_Post || sh_imp_same_text( $post->post_title, (string) $it['title'], null ) ) {
			continue;
		}
		if ( ! sh_imp_same_text( $post->post_title, $it['title_prev'], null ) ) {
			sh_imp_keep( "timeline_item:$n post_title (site \"" . $post->post_title . '" · seed "' . $it['title'] . '")' );
			continue;
		}
		$res = wp_update_post(
			wp_slash(
				array(
					'ID'         => $post->ID,
					'post_title' => (string) $it['title'],
					'post_name'  => $post->post_name,
				)
			),
			true
		);
		if ( is_wp_error( $res ) ) {
			throw new RuntimeException( 'timeline_item ' . $n . ': ' . $res->get_error_message() );
		}
		sh_imp_count( 'updated', 'timeline_card_title' );
	}
}

/**
 * Run one step of this file. Returns true when done.
 *
 * @param string                                  $step  Step id.
 * @param array<string, mixed>                    $seed  Seed files.
 * @param array<string, mixed>                    $map   image-map.json.
 * @param array<string, array{id:int,write:bool}> $pages Pages.
 */
function sh_import_refresh_2026_11_18( string $step, array $seed, array $map, array $pages ): bool {
	if ( '2026-11-18-elephant-text' === $step ) {
		sh_import_refresh_elephant_text_1118( $seed );
	}
	if ( '2026-11-18-home-hero' === $step ) {
		return sh_import_refresh_home_hero_1118( $map, $pages );
	}
	return true;
}
