<?php
/**
 * List every design/assets file the import reads (CLI · no WordPress) — scripts/deploy.sh bundles exactly these.
 *
 *   php scripts/import/bundle-files.php <assets-dir> [content-dir] [image-map.json]
 *
 * stdout: one line per file "map<TAB><file>" (image-map "files") or "ref<TAB><file>" (style *_ref, e.g. design-dev
 * crops "<ref>.crop-<node>.png" · timeline line_art_ref "lineart/<file>" · below_ref "<file>" · 2026-11 seed assets
 * "<sub>/<file>" for sh_import_asset_subdirs() — path relative to <assets-dir>, at most one sub-folder). stderr: summary.
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

$sh_repo   = dirname( __DIR__, 2 );
$sh_dir    = rtrim( isset( $argv[1] ) ? (string) $argv[1] : '', '/' );
$sh_cont   = rtrim( isset( $argv[2] ) ? (string) $argv[2] : $sh_repo . '/content', '/' );
$sh_mapf   = isset( $argv[3] ) ? (string) $argv[3] : __DIR__ . '/image-map.json';
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
	echo "ref\t$sh_f\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
}
fwrite( $sh_stderr, 'bundle-files: image-map ' . count( $sh_res['map'] ) . ' · *_ref ' . count( $sh_res['refs'] ) . ' · total ' . ( count( $sh_res['map'] ) + count( $sh_res['refs'] ) ) . "\n" ); // phpcs:ignore
