<?php
/**
 * Zone 4 slide image (D62 · zone4slides-1118): one Figma image box → one small WebP (pure PHP + GD, no WordPress).
 * Used by the deploy bundle (scripts/deploy.sh through the CLI below — the 4K originals never leave the machine) and by
 * the import when a file is not baked yet (local runs read design/assets directly · slides.php sh_slides_unit_file()).
 *
 *   php scripts/import/slide-image.php <src> <dest.webp> '<spec json>'
 *   spec = {"box":[W,H],"res":2,"fit":{"mode":"FIT|FILL|STRETCH","transform":[[a,b,tx],[c,d,ty]]},"trim":true|false}
 *   stdout "baked <w>x<h>" · exit 1 = failed / no GD WebP.
 *
 * What it does: draws the source in an W×H Figma box the way Figma did (FIT = contain · FILL = cover · STRETCH = the
 * visible part given by the image transform, the seed's "*_fit"), at `res` pixels per Figma px, transparent where the
 * box is not covered (RGBA kept); `trim` then cuts the transparent margin (the object on the time line stands on the
 * line · its size on the page = pixels ÷ res). Quality 82 · never larger than the box × res.
 * STRETCH transform = Figma's imageTransform, a 2×3 affine matrix from the box (x/W, y/H) to the image (px/width, py/height):
 * image_uv = [[a,b,tx],[c,d,ty]] × (box_u, box_v, 1). b = c = 0 (scale + translate only, a/d > 0) keeps the plain crop + resample
 * (every earlier bake stays byte-identical); any rotation / skew / flip is drawn pixel by pixel (sh_slide_image_affine · bilinear,
 * transparent outside the image) — slidecard-1119 (Hornbill 7 "Badau" card was blank: its transform rotates).
 *
 * @package sharedheritage-core
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'SH_IMPORT_CLI' ) && 'cli' !== PHP_SAPI ) {
	exit;
}

/** WebP quality of a baked slide image. */
const SH_SLIDE_WEBP_QUALITY = 82;
/** GD alpha (0 opaque … 127 clear) below which a pixel counts as part of the object when trimming. */
const SH_SLIDE_TRIM_ALPHA = 110;

/**
 * Source rectangle [x, y, w, h] and destination rectangle [x, y, w, h] (floats) of a fit mode.
 *
 * @param array<string, mixed> $fit  {mode, transform?}.
 * @param float                $sw   Source width px.
 * @param float                $sh   Source height px.
 * @param float                $cw   Canvas width px.
 * @param float                $ch   Canvas height px.
 * @return array{0: float[], 1: float[]}
 */
function sh_slide_image_rects( array $fit, float $sw, float $sh, float $cw, float $ch ): array {
	$mode = isset( $fit['mode'] ) ? (string) $fit['mode'] : 'FIT';
	if ( 'FILL' === $mode ) {
		$s = max( $cw / $sw, $ch / $sh );
		$w = $cw / $s;
		$h = $ch / $s;
		return array( array( ( $sw - $w ) / 2, ( $sh - $h ) / 2, $w, $h ), array( 0.0, 0.0, $cw, $ch ) );
	}
	$t = isset( $fit['transform'] ) && is_array( $fit['transform'] ) ? $fit['transform'] : null;
	if ( 'STRETCH' === $mode && $t && isset( $t[0][0], $t[0][2], $t[1][1], $t[1][2] ) && (float) $t[0][0] > 0 && (float) $t[1][1] > 0 ) {
		// Visible part of the file = [tx, tx + a] × [ty, ty + d] of its size, stretched over the whole box.
		$rx = (float) $t[0][2] * $sw;
		$ry = (float) $t[1][2] * $sh;
		$rw = (float) $t[0][0] * $sw;
		$rh = (float) $t[1][1] * $sh;
		$xs = $cw / $rw;
		$ys = $ch / $rh;
		$x0 = max( $rx, 0.0 );
		$y0 = max( $ry, 0.0 );
		$x1 = min( $rx + $rw, $sw );
		$y1 = min( $ry + $rh, $sh );
		return array( array( $x0, $y0, $x1 - $x0, $y1 - $y0 ), array( ( $x0 - $rx ) * $xs, ( $y0 - $ry ) * $ys, ( $x1 - $x0 ) * $xs, ( $y1 - $y0 ) * $ys ) );
	}
	$s = min( $cw / $sw, $ch / $sh ); // FIT (and a STRETCH without a usable transform).
	return array( array( 0.0, 0.0, $sw, $sh ), array( ( $cw - $sw * $s ) / 2, ( $ch - $sh * $s ) / 2, $sw * $s, $sh * $s ) );
}

/**
 * The 2×3 image transform of a STRETCH fit as floats [[a,b,tx],[c,d,ty]] (null = none / unusable).
 *
 * @param array<string, mixed> $fit {mode, transform?}.
 * @return float[][]|null
 */
function sh_slide_image_matrix( array $fit ): ?array {
	$t = isset( $fit['transform'] ) && is_array( $fit['transform'] ) ? $fit['transform'] : null;
	if ( ! isset( $fit['mode'] ) || 'STRETCH' !== $fit['mode'] || ! $t || ! isset( $t[0][0], $t[0][1], $t[0][2], $t[1][0], $t[1][1], $t[1][2] ) ) {
		return null;
	}
	return array(
		array( (float) $t[0][0], (float) $t[0][1], (float) $t[0][2] ),
		array( (float) $t[1][0], (float) $t[1][1], (float) $t[1][2] ),
	);
}

/**
 * True when a STRETCH transform needs the per-pixel path: rotation / skew (b or c ≠ 0), or a flip / zero scale (a or d ≤ 0).
 * A matrix without b / c (a 2×2 diagonal given as [[a,0,tx],[0,d,ty]] or only a · d · tx · ty) with a, d > 0 stays on the crop path.
 *
 * @param array<string, mixed> $fit {mode, transform?}.
 */
function sh_slide_image_is_affine( array $fit ): bool {
	$m = sh_slide_image_matrix( $fit );
	return null !== $m && ( abs( $m[0][1] ) > 1e-9 || abs( $m[1][0] ) > 1e-9 || $m[0][0] <= 0 || $m[1][1] <= 0 );
}

/**
 * Draw $img into the W×H canvas $out through a full affine imageTransform: every canvas pixel centre → box uv → image uv →
 * source pixel (bilinear · premultiplied alpha · outside the image = transparent). Figma STRETCH never tiles.
 *
 * @param \GdImage|resource $out Canvas (truecolor · alpha saved · cleared).
 * @param \GdImage|resource $img Source.
 * @param float[][]         $m   [[a,b,tx],[c,d,ty]].
 * @param int               $cw  Canvas width.
 * @param int               $ch  Canvas height.
 */
function sh_slide_image_affine( $out, $img, array $m, int $cw, int $ch ): void {
	$sw  = imagesx( $img );
	$sh  = imagesy( $img );
	$px  = static function ( int $x, int $y ) use ( $img, $sw, $sh ): array { // [r·a, g·a, b·a, a] (a 0…1), transparent outside.
		if ( $x < 0 || $y < 0 || $x >= $sw || $y >= $sh ) {
			return array( 0.0, 0.0, 0.0, 0.0 );
		}
		$c = imagecolorat( $img, $x, $y );
		$a = 1.0 - ( ( $c >> 24 ) & 0x7F ) / 127.0;
		return array( ( ( $c >> 16 ) & 0xFF ) * $a, ( ( $c >> 8 ) & 0xFF ) * $a, ( $c & 0xFF ) * $a, $a );
	};
	for ( $y = 0; $y < $ch; $y++ ) {
		$bv = ( $y + 0.5 ) / $ch;
		for ( $x = 0; $x < $cw; $x++ ) {
			$bu = ( $x + 0.5 ) / $cw;
			$fx = ( $m[0][0] * $bu + $m[0][1] * $bv + $m[0][2] ) * $sw - 0.5; // Source pixel coordinates (pixel centres at .5).
			$fy = ( $m[1][0] * $bu + $m[1][1] * $bv + $m[1][2] ) * $sh - 0.5;
			if ( $fx <= -1 || $fy <= -1 || $fx >= $sw || $fy >= $sh ) {
				continue;
			}
			$ix = (int) floor( $fx );
			$iy = (int) floor( $fy );
			$tx = $fx - $ix;
			$ty = $fy - $iy;
			$p  = array( $px( $ix, $iy ), $px( $ix + 1, $iy ), $px( $ix, $iy + 1 ), $px( $ix + 1, $iy + 1 ) );
			$w  = array( ( 1 - $tx ) * ( 1 - $ty ), $tx * ( 1 - $ty ), ( 1 - $tx ) * $ty, $tx * $ty );
			$v  = array( 0.0, 0.0, 0.0, 0.0 );
			for ( $k = 0; $k < 4; $k++ ) {
				for ( $j = 0; $j < 4; $j++ ) {
					$v[ $j ] += $p[ $k ][ $j ] * $w[ $k ];
				}
			}
			if ( $v[3] <= 0.0 ) {
				continue;
			}
			$a = min( 1.0, $v[3] );
			imagesetpixel(
				$out,
				$x,
				$y,
				imagecolorallocatealpha(
					$out,
					max( 0, min( 255, (int) round( $v[0] / $a ) ) ),
					max( 0, min( 255, (int) round( $v[1] / $a ) ) ),
					max( 0, min( 255, (int) round( $v[2] / $a ) ) ),
					max( 0, min( 127, (int) round( ( 1.0 - $a ) * 127 ) ) )
				)
			);
		}
	}
}

/**
 * Bake one image (see file header). Returns "baked WxH" | "nogd" (no GD / WebP) | "" (failed).
 *
 * @param string               $src  Source PNG / JPEG / WebP.
 * @param string               $dest Destination (.webp).
 * @param array<string, mixed> $spec {box:[W,H], res, fit:{mode,transform?}, trim}.
 */
function sh_slide_image_bake( string $src, string $dest, array $spec ): string {
	if ( ! function_exists( 'imagewebp' ) || ! function_exists( 'imagecopyresampled' ) ) {
		return 'nogd';
	}
	$info = @getimagesize( $src ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- not an image = failure below.
	$load = array(
		IMAGETYPE_PNG  => 'imagecreatefrompng',
		IMAGETYPE_JPEG => 'imagecreatefromjpeg',
		IMAGETYPE_WEBP => 'imagecreatefromwebp',
	);
	$type = is_array( $info ) ? (int) $info[2] : 0;
	if ( ! isset( $load[ $type ] ) || empty( $spec['box'][0] ) || empty( $spec['box'][1] ) ) {
		return '';
	}
	if ( function_exists( 'ini_get' ) && function_exists( 'ini_set' ) ) {
		$limit = (string) ini_get( 'memory_limit' );
		if ( '-1' !== $limit && (int) $limit < 1024 && ! str_ends_with( strtoupper( $limit ), 'G' ) ) {
			@ini_set( 'memory_limit', '1024M' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.PHP.IniSet.memory_limit_Blacklisted -- a 4K RGBA source needs ~80 MB.
		}
	}
	$img = @$load[ $type ]( $src ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( false === $img ) {
		return '';
	}
	$sw  = (float) imagesx( $img );
	$sh  = (float) imagesy( $img );
	$res = isset( $spec['res'] ) && (float) $spec['res'] > 0 ? (float) $spec['res'] : 2.0;
	$cw  = max( 1, (int) round( (float) $spec['box'][0] * $res ) );
	$ch  = max( 1, (int) round( (float) $spec['box'][1] * $res ) );
	$fit = isset( $spec['fit'] ) && is_array( $spec['fit'] ) ? $spec['fit'] : array();
	list( $s, $d ) = sh_slide_image_rects( $fit, $sw, $sh, (float) $cw, (float) $ch );

	$out = imagecreatetruecolor( $cw, $ch );
	imagealphablending( $out, false );
	imagesavealpha( $out, true );
	imagefill( $out, 0, 0, imagecolorallocatealpha( $out, 0, 0, 0, 127 ) );
	if ( sh_slide_image_is_affine( $fit ) ) { // Rotation / skew / flip: per-pixel (the crop path below cannot express it).
		sh_slide_image_affine( $out, $img, (array) sh_slide_image_matrix( $fit ), $cw, $ch );
	} elseif ( $s[2] >= 1 && $s[3] >= 1 && $d[2] >= 1 && $d[3] >= 1 ) {
		imagecopyresampled( $out, $img, (int) round( $d[0] ), (int) round( $d[1] ), (int) round( $s[0] ), (int) round( $s[1] ), max( 1, (int) round( $d[2] ) ), max( 1, (int) round( $d[3] ) ), max( 1, (int) round( $s[2] ) ), max( 1, (int) round( $s[3] ) ) );
	}
	unset( $img );

	if ( ! empty( $spec['trim'] ) ) { // Opaque bounding box of the drawn box.
		$x0 = $cw;
		$y0 = $ch;
		$x1 = -1;
		$y1 = -1;
		for ( $y = 0; $y < $ch; $y++ ) {
			for ( $x = 0; $x < $cw; $x++ ) {
				if ( ( ( imagecolorat( $out, $x, $y ) >> 24 ) & 0x7F ) < SH_SLIDE_TRIM_ALPHA ) {
					$x0 = min( $x0, $x );
					$x1 = max( $x1, $x );
					$y0 = min( $y0, $y );
					$y1 = max( $y1, $y );
				}
			}
		}
		if ( $x1 >= $x0 && $y1 >= $y0 && ( $x1 - $x0 + 1 < $cw || $y1 - $y0 + 1 < $ch ) ) {
			$tw   = $x1 - $x0 + 1;
			$th   = $y1 - $y0 + 1;
			$trim = imagecreatetruecolor( $tw, $th );
			imagealphablending( $trim, false );
			imagesavealpha( $trim, true );
			imagefill( $trim, 0, 0, imagecolorallocatealpha( $trim, 0, 0, 0, 127 ) );
			imagecopy( $trim, $out, 0, 0, $x0, $y0, $tw, $th );
			$out = $trim;
			$cw  = $tw;
			$ch  = $th;
		}
	}
	return imagewebp( $out, $dest, SH_SLIDE_WEBP_QUALITY ) ? 'baked ' . $cw . 'x' . $ch : '';
}

// CLI: deploy bundle (scripts/deploy.sh).
if ( 'cli' === PHP_SAPI && isset( $argv[0] ) && realpath( (string) $argv[0] ) === __FILE__ ) {
	$sh_spec = 4 === count( $argv ) ? json_decode( (string) $argv[3], true ) : null;
	if ( ! is_array( $sh_spec ) ) {
		fwrite( STDERR, "usage: php slide-image.php <src> <dest.webp> '<spec json>'\n" ); // phpcs:ignore
		exit( 2 );
	}
	$sh_res = sh_slide_image_bake( (string) $argv[1], (string) $argv[2], $sh_spec );
	if ( '' === $sh_res || 'nogd' === $sh_res ) {
		fwrite( STDERR, 'slide-image: ' . ( '' === $sh_res ? 'failed ' : 'PHP has no GD WebP — cannot bake ' ) . $argv[1] . "\n" ); // phpcs:ignore
		exit( 1 );
	}
	echo $sh_res . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
}
