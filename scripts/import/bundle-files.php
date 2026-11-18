<?php
/**
 * List every design/assets file the import reads (CLI · no WordPress) — scripts/deploy.sh bundles exactly these.
 *
 *   php scripts/import/bundle-files.php <assets-dir> [content-dir] [image-map.json] [media-dir]
 *
 * stdout: one line per file "map<TAB><file>" (image-map "files") or "ref<TAB><file>[<TAB><W>x<H>]" (style *_ref, e.g.
 * design-dev crops "<ref>.crop-<node>.png" · timeline line_art_ref "lineart/<file>" · below_ref "<file>" · 2026-11
 * seed assets "<sub>/<file>" for sh_import_asset_subdirs() — path relative to <assets-dir> · 2026-11-17 images with
 * the box deploy.sh shrinks them to before upload: 2× the display size, refs-2026-11-17.php) or "media<TAB><path>"
 * (every media/out file of content/media-manifest.json, path relative to <media-dir>, e.g. objects-1117/bird/x.webm ·
 * only when media-dir is given) or "bake<TAB><dest><TAB><src><TAB><spec json>" (D62 slide images: deploy.sh bakes the original
 * `<src>` into `<dest>` (zone4-slides-web/<unit>.webp · slide-image.php) — the 4K originals are never bundled). stderr: summary.
 * Exit 1 = a referenced file is missing / unsafe (nothing printed
 * on stdout) · exit 2 = bad arguments / unreadable JSON.
 *
 * @package sharedheritage-core
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}
define( 'SH_IMPORT_CLI', true );
require __DIR__ . '/refs.php';
require __DIR__ . '/refs-2026-11-17.php';
require __DIR__ . '/slides.php';

$sh_repo   = dirname( __DIR__, 2 );
$sh_dir    = rtrim( isset( $argv[1] ) ? (string) $argv[1] : '', '/' );
$sh_cont   = rtrim( isset( $argv[2] ) ? (string) $argv[2] : $sh_repo . '/content', '/' );
$sh_mapf   = isset( $argv[3] ) ? (string) $argv[3] : __DIR__ . '/image-map.json';
$sh_media  = rtrim( isset( $argv[4] ) ? (string) $argv[4] : '', '/' );
$sh_stderr = fopen( 'php://stderr', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

if ( '' === $sh_dir || ! is_dir( $sh_dir ) ) {
	fwrite( $sh_stderr, "bundle-files: usage: php bundle-files.php <assets-dir> [content-dir] [image-map.json] (assets-dir not found: $sh_dir)\n" ); // phpcs:ignore
	exit( 2 );
}
$sh_json = static function ( string $path ) use ( $sh_stderr ): array {
	$data = is_readable( $path ) ? json_decode( (string) file_get_contents( $path ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! is_array( $data ) ) {
		fwrite( $sh_stderr, "bundle-files: cannot read JSON: $path\n" ); // phpcs:ignore
		exit( 2 );
	}
	return $data;
};

$sh_seed = array(
	'objects'           => $sh_json( $sh_cont . '/seed/objects.json' ),
	'timeline-elephant' => $sh_json( $sh_cont . '/seed/timeline-elephant.json' ),
);
$sh_res  = sh_import_bundle_files( $sh_seed, $sh_json( $sh_mapf ), $sh_dir );

// 2026-11 seed assets (zone banners · Zone 5 page · Zone 4 chooser — "<sub>/<file>" for sh_import_asset_subdirs()).
$sh_res = sh_import_bundle_seed_files(
	array(
		'zones'         => $sh_json( $sh_cont . '/seed/zones.json' ),
		'zone4-chooser' => $sh_json( $sh_cont . '/seed/zone4-chooser.json' ),
	),
	$sh_json( $sh_mapf ),
	$sh_dir,
	$sh_res
);

// 2026-11-17 (core-1117): objects of all trails · trail pages · guide cards · logos · Introduction + media/out files.
$sh_1117 = sh_import_bundle_1117_files(
	array(
		'objects'       => $sh_json( $sh_cont . '/seed/objects.json' ),
		'trails'        => $sh_json( $sh_cont . '/seed/trails.json' ),
		'guides'        => $sh_json( $sh_cont . '/seed/guides.json' ),
		'site'          => $sh_json( $sh_cont . '/seed/site.json' ),
		'intro-2026-11' => $sh_json( $sh_cont . '/seed/intro-2026-11.json' ),
		'zones-2026-11-17' => $sh_json( $sh_cont . '/seed/zones-2026-11-17.json' ),
		'manifest'      => $sh_json( $sh_cont . '/media-manifest.json' ),
	),
	$sh_dir,
	'' !== $sh_media ? $sh_media : $sh_dir
);
// D62 (zone4slides-1118): baked slide images of the 16 animals.
$sh_slides = sh_slides_bundle_units( $sh_json( $sh_cont . '/seed/timeline-slides.json' ), $sh_dir );
$sh_res['missing'] = array_merge( $sh_res['missing'], $sh_slides['missing'] );
$sh_box = array();
foreach ( $sh_1117['refs'] as $sh_f => $sh_b ) {
	if ( in_array( $sh_f, $sh_res['map'], true ) ) {
		continue; // Already listed (image-map file): copied as is.
	}
	if ( ! in_array( $sh_f, $sh_res['refs'], true ) ) {
		$sh_res['refs'][] = $sh_f;
	}
	$sh_box[ $sh_f ] = $sh_b;
}
sort( $sh_res['refs'] );
$sh_res['missing'] = array_merge( $sh_res['missing'], '' !== $sh_media ? $sh_1117['missing'] : array_values( array_filter( $sh_1117['missing'], static fn( $m ) => ! str_starts_with( $m, 'media ' ) ) ) );

if ( $sh_res['unsafe'] || $sh_res['missing'] ) {
	foreach ( $sh_res['unsafe'] as $sh_f ) {
		fwrite( $sh_stderr, "bundle-files: unsafe file name in image-map: $sh_f\n" ); // phpcs:ignore
	}
	foreach ( $sh_res['missing'] as $sh_f ) {
		fwrite( $sh_stderr, "bundle-files: missing in $sh_dir — $sh_f\n" ); // phpcs:ignore
	}
	exit( 1 );
}
foreach ( $sh_res['map'] as $sh_f ) {
	echo "map\t$sh_f\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
}
foreach ( $sh_res['refs'] as $sh_f ) {
	echo "ref\t$sh_f" . ( ! empty( $sh_box[ $sh_f ] ) ? "\t" . $sh_box[ $sh_f ] : '' ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
}
foreach ( $sh_slides['units'] as $sh_n => $sh_u ) {
	echo "bake\t" . SH_SLIDES_WEB_DIR . '/' . $sh_n . ".webp\t" . $sh_u[0] . "\t" . json_encode( $sh_u[1], JSON_UNESCAPED_SLASHES ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.WP.AlternativeFunctions.json_encode_json_encode -- CLI output.
}
if ( '' !== $sh_media ) {
	foreach ( $sh_1117['media'] as $sh_f ) {
		echo "media\t$sh_f\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
	}
}
fwrite( $sh_stderr, 'bundle-files: image-map ' . count( $sh_res['map'] ) . ' · *_ref ' . count( $sh_res['refs'] ) . ' (shrink to 2× ' . count( array_filter( $sh_box ) ) . ') · slide images to bake ' . count( $sh_slides['units'] ) . ' · total ' . ( count( $sh_res['map'] ) + count( $sh_res['refs'] ) ) . ( '' !== $sh_media ? ' · media/out ' . count( $sh_1117['media'] ) : '' ) . "\n" ); // phpcs:ignore
