<?php
/**
 * Importer: colour profile normalisation (ICC → sRGB) for imported JPEG/PNG files.
 *
 * Figma draws image pixels as stored and ignores an embedded ICC profile; browsers apply the profile.
 * Measured (zone 1 frame 1472, tile 8428e71b): Figma (241,173,160) = raw pixel · Chrome with the embedded
 * Apple "Color LCD"/Display P3 profile → (253,169,158). So "to sRGB" here = re-tag the raw pixels as sRGB:
 * the non-sRGB profile is removed (untagged = sRGB in every browser) — lossless, no re-encode, pure PHP
 * (no GD/Imagick needed, same result in Playground and on the server). Profiles named sRGB are kept.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Profile description (ICC 'desc' tag, v2 textDescriptionType or v4 multiLocalizedUnicodeType). '' = unknown.
 *
 * @param string $icc Raw ICC profile.
 */
function sh_imp_icc_desc( string $icc ): string {
	if ( strlen( $icc ) < 132 ) {
		return '';
	}
	$count = unpack( 'N', substr( $icc, 128, 4 ) )[1];
	for ( $i = 0; $i < $count && 132 + 12 * ( $i + 1 ) <= strlen( $icc ); $i++ ) {
		$e = unpack( 'a4sig/Noff/Nsize', substr( $icc, 132 + 12 * $i, 12 ) );
		if ( 'desc' !== $e['sig'] ) {
			continue;
		}
		$tag = substr( $icc, $e['off'], $e['size'] );
		if ( strlen( $tag ) < 16 ) {
			return '';
		}
		if ( 'desc' === substr( $tag, 0, 4 ) ) {
			$len = unpack( 'N', substr( $tag, 8, 4 ) )[1];
			return rtrim( substr( $tag, 12, $len ), "\0" );
		}
		if ( 'mluc' === substr( $tag, 0, 4 ) && strlen( $tag ) >= 28 ) {
			$rec = unpack( 'Nlen/Noff', substr( $tag, 20, 8 ) );
			$raw = substr( $tag, $rec['off'], $rec['len'] );
			return function_exists( 'mb_convert_encoding' ) ? (string) mb_convert_encoding( $raw, 'UTF-8', 'UTF-16BE' ) : str_replace( "\0", '', $raw );
		}
		return '';
	}
	return '';
}

/**
 * True when the profile is an sRGB profile (kept as is).
 *
 * @param string $icc Raw ICC profile.
 */
function sh_imp_icc_is_srgb( string $icc ): bool {
	return 1 === preg_match( '/^\s*sRGB\b/i', sh_imp_icc_desc( $icc ) ); // Not "DCI-P3 D65 Gamut with sRGB Transfer".
}

/**
 * JPEG without its non-sRGB ICC profile (APP2 "ICC_PROFILE" segments). null = nothing to change.
 *
 * @param string $jpg File bytes.
 */
function sh_imp_jpeg_strip_icc( string $jpg ): ?string {
	if ( "\xFF\xD8" !== substr( $jpg, 0, 2 ) ) {
		return null;
	}
	$out   = "\xFF\xD8";
	$icc   = array();
	$pos   = 2;
	$total = strlen( $jpg );
	while ( $pos + 4 <= $total ) {
		if ( "\xFF" !== $jpg[ $pos ] ) {
			return null; // Malformed: leave the file alone.
		}
		$marker = ord( $jpg[ $pos + 1 ] );
		if ( 0xFF === $marker ) {
			++$pos;
			continue;
		}
		if ( 0xDA === $marker || 0xD9 === $marker ) { // Start of scan / end: the rest is image data.
			$out .= substr( $jpg, $pos );
			break;
		}
		if ( 0x01 === $marker || ( $marker >= 0xD0 && $marker <= 0xD7 ) ) {
			$out .= substr( $jpg, $pos, 2 );
			$pos += 2;
			continue;
		}
		$seg = substr( $jpg, $pos, 2 + unpack( 'n', substr( $jpg, $pos + 2, 2 ) )[1] );
		if ( 0xE2 === $marker && "ICC_PROFILE\0" === substr( $seg, 4, 12 ) && strlen( $seg ) > 18 ) {
			$icc[ ord( $seg[16] ) ] = substr( $seg, 18 );
		} else {
			$out .= $seg;
		}
		$pos += strlen( $seg );
	}
	if ( ! $icc ) {
		return null;
	}
	ksort( $icc );
	return sh_imp_icc_is_srgb( implode( '', $icc ) ) ? null : $out;
}

/**
 * PNG without its non-sRGB iCCP chunk (and cICP/cHRM/gAMA, which browsers would apply instead). null = nothing to change.
 *
 * @param string $png File bytes.
 */
function sh_imp_png_strip_icc( string $png ): ?string {
	if ( "\x89PNG\r\n\x1a\n" !== substr( $png, 0, 8 ) ) {
		return null;
	}
	$chunks = array();
	$icc    = null;
	$pos    = 8;
	while ( $pos + 12 <= strlen( $png ) ) {
		$len  = unpack( 'N', substr( $png, $pos, 4 ) )[1];
		$type = substr( $png, $pos + 4, 4 );
		$all  = substr( $png, $pos, 12 + $len );
		if ( 'iCCP' === $type ) {
			$data = substr( $all, 8, $len );
			$nul  = strpos( $data, "\0" );
			$icc  = false === $nul ? '' : (string) @gzuncompress( substr( $data, $nul + 2 ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- corrupt profile = treat as unknown.
		}
		$chunks[] = array( $type, $all );
		$pos     += 12 + $len;
		if ( 'IEND' === $type ) {
			break;
		}
	}
	if ( null === $icc || sh_imp_icc_is_srgb( $icc ) ) {
		return null;
	}
	$out = substr( $png, 0, 8 );
	foreach ( $chunks as $c ) {
		$out .= in_array( $c[0], array( 'iCCP', 'cICP', 'cHRM', 'gAMA' ), true ) ? '' : $c[1];
	}
	return $out;
}

/**
 * Rewrite an image file in place without its non-sRGB colour profile. true = file changed.
 *
 * @param string $file Absolute path.
 * @param string $mime Mime type.
 */
function sh_imp_icc_normalize( string $file, string $mime ): bool {
	if ( ! in_array( $mime, array( 'image/jpeg', 'image/png' ), true ) || ! is_readable( $file ) ) {
		return false;
	}
	$bytes = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$new   = 'image/jpeg' === $mime ? sh_imp_jpeg_strip_icc( $bytes ) : sh_imp_png_strip_icc( $bytes );
	if ( null === $new ) {
		return false;
	}
	if ( false === file_put_contents( $file, $new ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		throw new RuntimeException( 'cannot rewrite ' . $file );
	}
	return true;
}
