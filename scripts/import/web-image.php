<?php
/**
 * Shrink an image to at most its web box (2× the display size · core-1117) — pure PHP + GD, no WordPress.
 * Used by the import (media.php sh_imp_attachment(): crop + fit when copying into uploads) and by scripts/deploy.sh
 * through the CLI below (fit only, so the bundle that goes to the host is small · the crop happens at import).
 *
 *   php scripts/import/web-image.php <src> <dest> <box-w> <box-h>
 *   stdout: "resized <w>x<h>" | "copied" (already small enough / not an image GD reads) · exit 1 = error.
 *
 * Never enlarges · PNG keeps its alpha · JPEG quality 86 · WebP quality 86 · no GD = plain copy.
 * Colour profile: GD writes the raw pixel values without a profile — the same result as icc.php (Figma shows the
 * raw values, CONTENT-MODEL §12).
 *
 * @package sharedheritage-core
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'SH_IMPORT_CLI' ) && 'cli' !== PHP_SAPI ) {
	exit;
}

/**
 * Size of the file the bundle may carry for a spec: the box before the crop (crop happens at import).
 *
 * @param array<string, mixed> $spec {box:[w,h], crop:[x,y,w,h]|null}.
 * @return array{0:int,1:int}
 */
function sh_web_image_bundle_box( array $spec ): array {
	$w = (float) $spec['box'][0];
	$h = (float) $spec['box'][1];
	if ( ! empty( $spec['crop'] ) ) {
		$w /= max( 0.01, (float) $spec['crop'][2] );
		$h /= max( 0.01, (float) $spec['crop'][3] );
	}
	return array( (int) ceil( $w ), (int) ceil( $h ) );
}

/**
 * Write $src to $dest cropped (fractions) and fitted inside $box. Returns "resized" | "copied" | "" (failed).
 *
 * @param string                 $src  Source file.
 * @param string                 $dest Destination file (same format).
 * @param array<int, int>        $box  [w, h] maximum.
 * @param array<int, float>|null $crop [x, y, w, h] fractions or null.
 */
function sh_web_image_write( string $src, string $dest, array $box, ?array $crop = null ): string {
	$info = @getimagesize( $src ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- not an image = plain copy.
	$type = is_array( $info ) ? (int) $info[2] : 0;
	$load = array(
		IMAGETYPE_PNG  => 'imagecreatefrompng',
		IMAGETYPE_JPEG => 'imagecreatefromjpeg',
		IMAGETYPE_WEBP => 'imagecreatefromwebp',
	);
	$w    = is_array( $info ) ? (int) $info[0] : 0;
	$h    = is_array( $info ) ? (int) $info[1] : 0;
	$cx   = 0;
	$cy   = 0;
	$cw   = $w;
	$ch   = $h;
	if ( null !== $crop && $w > 0 && $h > 0 ) {
		$cx = (int) round( $crop[0] * $w );
		$cy = (int) round( $crop[1] * $h );
		$cw = max( 1, min( $w - $cx, (int) round( $crop[2] * $w ) ) );
		$ch = max( 1, min( $h - $cy, (int) round( $crop[3] * $h ) ) );
	}
	$scale = ( $cw > 0 && $ch > 0 ) ? min( 1.0, $box[0] / $cw, $box[1] / $ch ) : 1.0;
	$noop  = null === $crop && $scale >= 1.0;
	if ( $noop || ! isset( $load[ $type ] ) || ! function_exists( $load[ $type ] ) || ! function_exists( 'imagecopyresampled' ) ) {
		return copy( $src, $dest ) ? 'copied' : '';
	}
	$img = @$load[ $type ]( $src ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( false === $img ) {
		return copy( $src, $dest ) ? 'copied' : '';
	}
	$nw  = max( 1, (int) round( $cw * $scale ) );
	$nh  = max( 1, (int) round( $ch * $scale ) );
	$out = imagecreatetruecolor( $nw, $nh );
	if ( IMAGETYPE_JPEG !== $type ) {
		imagealphablending( $out, false );
		imagesavealpha( $out, true );
		imagefill( $out, 0, 0, imagecolorallocatealpha( $out, 0, 0, 0, 127 ) );
	}
	imagecopyresampled( $out, $img, 0, 0, $cx, $cy, $nw, $nh, $cw, $ch );
	$ok = false;
	if ( IMAGETYPE_PNG === $type ) {
		$ok = imagepng( $out, $dest, 9 );
	} elseif ( IMAGETYPE_JPEG === $type ) {
		$ok = imagejpeg( $out, $dest, 86 );
	} elseif ( IMAGETYPE_WEBP === $type && function_exists( 'imagewebp' ) ) {
		$ok = imagewebp( $out, $dest, 86 );
	}
	if ( ! $ok ) {
		return copy( $src, $dest ) ? 'copied' : '';
	}
	return 'resized ' . $nw . 'x' . $nh;
}

// CLI: deploy bundle (fit only).
if ( 'cli' === PHP_SAPI && isset( $argv[0] ) && realpath( (string) $argv[0] ) === __FILE__ ) {
	if ( 5 !== count( $argv ) || ! ctype_digit( (string) $argv[3] ) || ! ctype_digit( (string) $argv[4] ) ) {
		fwrite( STDERR, "usage: php web-image.php <src> <dest> <box-w> <box-h>\n" ); // phpcs:ignore
		exit( 2 );
	}
	$sh_res = sh_web_image_write( (string) $argv[1], (string) $argv[2], array( (int) $argv[3], (int) $argv[4] ) );
	if ( '' === $sh_res ) {
		fwrite( STDERR, 'web-image: failed ' . $argv[1] . "\n" ); // phpcs:ignore
		exit( 1 );
	}
	echo $sh_res . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
}
