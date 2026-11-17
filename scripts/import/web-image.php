<?php
/**
 * Shrink an image to at most its web box (2× the display size · core-1117) — pure PHP + GD, no WordPress.
 * Used by the import (media.php sh_imp_attachment(): crop + fit when copying into uploads) and by scripts/deploy.sh
 * through the CLI below (fit only, so the bundle that goes to the host is small · the crop happens at import).
 *
 *   php scripts/import/web-image.php <src> <dest> <box-w> <box-h> [#RRGGBB matte]   (0 = no limit on that side)
 *   stdout: "resized <w>x<h>" | "jpeg <w>x<h>" | "copied" (already small enough / not an image GD reads) ·
 *   exit 1 = error or no GD (the bundle must not carry unshrunk files silently).
 *
 * Never enlarges · PNG keeps its alpha · a PNG result > 1 MB with no transparent pixel — or with a matte colour the
 * image always sits on — → JPEG 82 (core-1117b: trail covers 2240×980 were 4–5 MB, transparent only at the crop edge) · JPEG quality 85 · WebP quality 86 · no GD = plain copy reported as "nogd".
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
	if ( $w <= 0 || $h <= 0 ) {
		return array( 0, 0 ); // No size limit (JPEG rule only).
	}
	if ( ! empty( $spec['crop'] ) ) {
		$w /= max( 0.01, (float) $spec['crop'][2] );
		$h /= max( 0.01, (float) $spec['crop'][3] );
	}
	return array( (int) ceil( $w ), (int) ceil( $h ) );
}

/** A PNG result larger than this (bytes) with no transparent pixel is written as JPEG instead (core-1117b). */
const SH_WEB_IMAGE_JPEG_OVER = 1000000;
/** JPEG quality of a shrunk JPEG. */
const SH_WEB_IMAGE_JPEG_QUALITY = 85;
/** JPEG quality of the PNG → JPEG rule (2240×980 covers: avg 462 KB, max 575 KB · q85 = avg 509 / max 632). */
const SH_WEB_IMAGE_PNG_TO_JPEG_QUALITY = 82;

/**
 * True when every pixel of a GD image is fully opaque (alpha 0) — scans every pixel (only for the large PNGs of
 * the JPEG rule).
 *
 * @param GdImage $img Image.
 */
function sh_web_image_opaque( $img ): bool {
	if ( ! imageistruecolor( $img ) ) {
		return -1 === imagecolortransparent( $img );
	}
	$w = imagesx( $img );
	$h = imagesy( $img );
	for ( $y = 0; $y < $h; $y++ ) {
		for ( $x = 0; $x < $w; $x++ ) {
			if ( ( imagecolorat( $img, $x, $y ) >> 24 ) & 0x7F ) {
				return false;
			}
		}
	}
	return true;
}

/**
 * Write $src to $dest cropped (fractions) and fitted inside $box (0 = no limit on that side). A PNG whose result is
 * over SH_WEB_IMAGE_JPEG_OVER bytes and has no transparent pixel (or $matte is given) becomes a JPEG (quality 82) — $dest keeps its name,
 * the caller reads the real type from the file (media.php renames it .jpg).
 * Returns "resized WxH" | "jpeg WxH" | "copied" (already small · not an image GD reads) | "nogd" (GD missing but a
 * crop / resize / conversion was needed → copied as is) | "" (failed).
 *
 * @param string                 $src  Source file.
 * @param string                 $dest Destination file.
 * @param array<int, int>        $box  [w, h] maximum (0 = no limit).
 * @param array<int, float>|null $crop  [x, y, w, h] fractions or null.
 * @param string                 $matte "#RRGGBB" the image always sits on (e.g. the white trail page): a large PNG
 *                                      with transparent pixels is flattened onto it and written as JPEG ('' = only
 *                                      fully opaque PNGs become JPEG).
 */
function sh_web_image_write( string $src, string $dest, array $box, ?array $crop = null, string $matte = '' ): string {
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
	$bw    = (int) $box[0] > 0 ? (int) $box[0] : PHP_INT_MAX;
	$bh    = (int) $box[1] > 0 ? (int) $box[1] : PHP_INT_MAX;
	$scale = ( $cw > 0 && $ch > 0 ) ? min( 1.0, $bw / $cw, $bh / $ch ) : 1.0;
	$big   = IMAGETYPE_PNG === $type && (int) filesize( $src ) > SH_WEB_IMAGE_JPEG_OVER; // May become a JPEG.
	$noop  = null === $crop && $scale >= 1.0;
	if ( ! isset( $load[ $type ] ) || ( $noop && ! $big ) ) {
		return copy( $src, $dest ) ? 'copied' : '';
	}
	if ( ! function_exists( $load[ $type ] ) || ! function_exists( 'imagecopyresampled' ) ) {
		return copy( $src, $dest ) ? 'nogd' : '';
	}
	$img = @$load[ $type ]( $src ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( false === $img ) {
		return copy( $src, $dest ) ? 'copied' : '';
	}
	$nw = max( 1, (int) round( $cw * $scale ) );
	$nh = max( 1, (int) round( $ch * $scale ) );
	if ( $noop ) {
		$out = $img; // Only the JPEG rule applies.
	} else {
		$out = imagecreatetruecolor( $nw, $nh );
		if ( IMAGETYPE_JPEG !== $type ) {
			imagealphablending( $out, false );
			imagesavealpha( $out, true );
			imagefill( $out, 0, 0, imagecolorallocatealpha( $out, 0, 0, 0, 127 ) );
		}
		imagecopyresampled( $out, $img, 0, 0, $cx, $cy, $nw, $nh, $cw, $ch );
	}
	$ok = false;
	if ( IMAGETYPE_PNG === $type ) {
		$ok = imagepng( $out, $dest, 9 );
		clearstatcache( true, $dest );
		$hex = preg_match( '/^#?([0-9a-f]{6})$/i', $matte, $m ) ? $m[1] : '';
		if ( $ok && (int) filesize( $dest ) > SH_WEB_IMAGE_JPEG_OVER && ( '' !== $hex || sh_web_image_opaque( $out ) ) ) {
			if ( ! imageistruecolor( $out ) ) {
				imagepalettetotruecolor( $out );
			}
			if ( '' !== $hex ) { // Flatten onto the background the image is always shown on.
				$flat = imagecreatetruecolor( imagesx( $out ), imagesy( $out ) );
				imagefill( $flat, 0, 0, imagecolorallocate( $flat, (int) hexdec( substr( $hex, 0, 2 ) ), (int) hexdec( substr( $hex, 2, 2 ) ), (int) hexdec( substr( $hex, 4, 2 ) ) ) );
				imagealphablending( $flat, true );
				imagecopy( $flat, $out, 0, 0, 0, 0, imagesx( $out ), imagesy( $out ) );
				$out = $flat;
			}
			if ( imagejpeg( $out, $dest, SH_WEB_IMAGE_PNG_TO_JPEG_QUALITY ) ) {
				return 'jpeg ' . $nw . 'x' . $nh;
			}
			$ok = imagepng( $out, $dest, 9 );
		}
		if ( $ok && $noop ) {
			return copy( $src, $dest ) ? 'copied' : ''; // Transparent / small after all: keep the original bytes.
		}
	} elseif ( IMAGETYPE_JPEG === $type ) {
		$ok = imagejpeg( $out, $dest, SH_WEB_IMAGE_JPEG_QUALITY );
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
	if ( ! in_array( count( $argv ), array( 5, 6 ), true ) || ! ctype_digit( (string) $argv[3] ) || ! ctype_digit( (string) $argv[4] ) || ( isset( $argv[5] ) && ! preg_match( '/^#?[0-9a-f]{6}$/i', (string) $argv[5] ) ) ) {
		fwrite( STDERR, "usage: php web-image.php <src> <dest> <box-w> <box-h> [matte #RRGGBB]\n" ); // phpcs:ignore
		exit( 2 );
	}
	$sh_res = sh_web_image_write( (string) $argv[1], (string) $argv[2], array( (int) $argv[3], (int) $argv[4] ), null, isset( $argv[5] ) ? (string) $argv[5] : '' );
	if ( '' === $sh_res || 'nogd' === $sh_res ) {
		fwrite( STDERR, 'web-image: ' . ( '' === $sh_res ? 'failed ' : 'PHP has no GD — cannot shrink ' ) . $argv[1] . "\n" ); // phpcs:ignore
		exit( 1 );
	}
	echo $sh_res . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
}
