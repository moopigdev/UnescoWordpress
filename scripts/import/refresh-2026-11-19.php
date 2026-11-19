<?php
/**
 * Refresh step of the 2026-11-19 round (slidecard-1119 · BACKLOG #51 · D62): the card image on the time line of a slide
 * whose Figma imageTransform rotates / skews (today only Hornbill 7 "Badau" · timeline_item key timeline:hornbill:7).
 * Production holds a blank 500×282 bake of it (the old slide-image.php could not draw a rotation). The fixed baker gives that
 * unit a new name (slides.php SH_SLIDE_AFFINE_BAKE) = a new attachment; this step puts it into sh_thumbnail only while the field
 * is still the attachment the importer made with the OLD bake (import key asset:slide-<old unit name>) — any other value (an image an
 * admin chose · the field cleared) stays and is listed in kept_admin. The old attachment is never deleted.
 * Works with SH_IMPORT_ONLY=refresh (deploy.sh --refresh-only) and on a fresh import (the slides step already used the new bake).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Steps of this file: id => what it changes (merged into sh_import_refresh_steps()).
 *
 * @return array<string, string>
 */
function sh_import_refresh_steps_2026_11_19(): array {
	return array(
		// slidecard-1119 (BACKLOG #51 · D62): the blank card image of Hornbill 7 "Badau" → the new bake.
		'2026-11-19-badau-card' => 'timeline_item hornbill 7 (Badau) sh_thumbnail: blank card image → new bake of the rotated Figma image, only while the field is still the attachment the importer made with the old baker · an image an admin chose stays: kept_admin · the old attachment stays in the media library',
	);
}

/**
 * Step 2026-11-19-badau-card: every slide of the seed whose card image is drawn by the affine baker (spec "bake" marker).
 * Returns false when the new file is missing (warning raised) — the step runs again on the next import.
 *
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_badau_card_1119( array $seed ): bool {
	if ( ! isset( $seed['timeline-slides'] ) ) {
		return true;
	}
	$slides = (array) $seed['timeline-slides'];
	$ok     = true;
	foreach ( sh_slides_animals( $slides ) as $slug => $row ) {
		if ( 'elephant' === $slug ) {
			continue; // The main timeline is never touched by the slides seed.
		}
		foreach ( (array) $row['items'] as $it ) {
			$units = sh_slides_item_units( (array) $it );
			$unit  = $units['top'];
			if ( ! is_array( $unit ) || ! isset( $unit['spec']['bake'] ) ) {
				continue;
			}
			$n    = (int) $it['order'];
			$post = sh_imp_find_post( 'timeline_item', 'timeline:' . $slug . ':' . $n );
			if ( ! $post ) {
				continue; // Not on this site (the slides step creates it with the new bake).
			}
			$old_spec = $unit['spec'];
			unset( $old_spec['bake'] );
			$old = sh_imp_find_attachment( 'asset:slide-' . sh_slides_unit_name( array( 'ref' => $unit['ref'], 'spec' => $old_spec ) ) );
			$new = sh_imp_find_attachment( 'asset:slide-' . sh_slides_unit_name( $unit ) );
			$cur = absint( get_post_meta( $post, 'sh_thumbnail', true ) );
			if ( $cur && $cur === $new ) {
				continue; // Already the new bake (fresh import / done before).
			}
			if ( ! $cur || ! $old || $cur !== $old ) {
				sh_imp_keep( "timeline_item:$slug:$n sh_thumbnail (site attachment $cur is not the one the import made with the old baker · new card image not applied)" );
				continue;
			}
			$att = sh_slides_attachment( $slides, $unit, (string) $it['title'] . ' card' );
			if ( ! $att ) {
				$ok = false;
				continue;
			}
			sh_imp_meta( 'post', $post, array( 'sh_thumbnail' => $att ) );
			sh_imp_count( 'updated', 'slide_card_redrawn' );
		}
	}
	return $ok;
}

/**
 * Run one step of this file. Returns true when done.
 *
 * @param string               $step Step id.
 * @param array<string, mixed> $seed Seed files.
 */
function sh_import_refresh_2026_11_19( string $step, array $seed ): bool {
	if ( '2026-11-19-badau-card' === $step ) {
		return sh_import_refresh_badau_card_1119( $seed );
	}
	return true;
}
