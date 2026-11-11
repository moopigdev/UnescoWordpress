<?php
/**
 * Seed importer — content/seed/*.json + content/media-manifest.json + scripts/import/image-map.json
 * → WordPress (D7: trail elephant only — 15 objects, 15 timeline cards; all 17 trail terms; zones; pages).
 *
 * Run inside WordPress:
 *   local:  scripts/local.sh (blueprint step, automatic)
 *   server: SH_IMPORT_ASSETS_DIR=… SH_IMPORT_MEDIA_DIR=… wp eval-file scripts/import/import.php
 * Env/constants: SH_IMPORT_ASSETS_DIR (Unesco/design/assets) · SH_IMPORT_MEDIA_DIR (Unesco/media/out)
 *   SH_IMPORT_MODE missing (default: never touch what exists) | overwrite (seed wins)
 *   SH_IMPORT_REPORT=<file.json> · SH_IMPORT_SUBSIZES=1 (generate image sub-sizes)
 *   SH_IMPORT_REFRESH=<id>|all  re-run a refresh step (refresh.php) that this site already ran
 *   SH_IMPORT_ONLY=refresh  run only the refresh steps on a site that already has the content: creates no post,
 *     term, page, option or menu (finds the existing ones by _sh_import_key) — deploy.sh --refresh-only
 * Idempotent: running twice creates nothing new (lookup by _sh_import_key / term slug / page path).
 * Fixes to values an older importer wrote: refresh.php steps (once per site, never over an admin's own file).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit( "run inside WordPress (wp eval-file)\n" );

require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/refs.php';
require_once __DIR__ . '/icc.php';
require_once __DIR__ . '/media.php';
require_once __DIR__ . '/entities.php';
require_once __DIR__ . '/refresh.php';

/**
 * SH_IMPORT_ONLY=refresh: pages that exist (by import key) — nothing is created. id 0 = not on this site.
 *
 * @return array<string, array{id:int,write:bool}>
 */
function sh_import_find_pages(): array {
	$out = array();
	foreach ( array( 'intro', 'timeline', 'front' ) as $role ) {
		$out[ $role ] = array(
			'id'    => sh_imp_find_post( 'page', 'page:' . $role ),
			'write' => false,
		);
	}
	return $out;
}

/**
 * SH_IMPORT_ONLY=refresh: in-scope objects that exist (by import key) — slug => post id · nothing is created.
 *
 * @param array<string, mixed> $seed Seed files.
 * @return array<string, int>
 */
function sh_import_find_objects( array $seed ): array {
	$out = array();
	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		$id = sh_imp_find_post( 'object', 'object:' . $o['slug'] );
		if ( $id ) {
			$out[ (string) $o['slug'] ] = $id;
		}
	}
	return $out;
}

/**
 * Run the import; returns the report.
 *
 * @return array<string, mixed>
 */
function sh_import_run(): array {
	if ( ! post_type_exists( 'object' ) || ! taxonomy_exists( 'trail' ) ) {
		throw new RuntimeException( 'mu-plugin sharedheritage-core is not loaded' );
	}
	$cfg  = sh_imp_config();
	$seed = array();
	foreach ( array( 'objects', 'trails', 'zones', 'timeline-elephant', 'articles', 'site', 'zone4-chooser' ) as $name ) {
		$seed[ $name ] = sh_imp_json( $cfg['content'] . '/seed/' . $name . '.json' );
	}
	$manifest = sh_imp_json( $cfg['content'] . '/media-manifest.json' );
	$map      = sh_imp_json( $cfg['map'] );

	if ( 'refresh' === $cfg['only'] ) {
		$pages   = sh_import_find_pages();
		$objects = sh_import_find_objects( $seed );
	} else {
		$pages   = sh_import_pages( $seed, $map, $manifest );
		$trails  = sh_import_trails( $seed, $map, $pages );
		$zones   = sh_import_zones( $seed, $map, $pages );
		$objects = sh_import_objects( $seed, $map, $manifest, $trails );
		sh_import_trail_order( $seed, $trails, $objects );
		sh_import_timeline( $seed, $map, $trails, $objects, $pages );
		sh_import_site( $seed, $map );
	}
	$refresh = sh_import_refresh( $seed, $map, $manifest, $objects, $pages );

	$s      = sh_imp_state();
	$counts = array(
		'object'        => (int) wp_count_posts( 'object' )->publish,
		'timeline_item' => (int) wp_count_posts( 'timeline_item' )->publish,
		'trail'         => (int) wp_count_terms( array( 'taxonomy' => 'trail', 'hide_empty' => false ) ),
		'zone'          => (int) wp_count_terms( array( 'taxonomy' => 'zone', 'hide_empty' => false ) ),
		'page'          => count( array_filter( $pages ) ),
		'attachment'    => (int) wp_count_posts( 'attachment' )->inherit,
	);
	return array(
		'mode'     => $cfg['mode'],
		'only'     => $cfg['only'],
		'refresh'  => $refresh,
		'counts'   => $counts,
		'created'  => $s['created'],
		'updated'  => $s['updated'],
		'skipped'  => $s['skipped'],
		'warnings' => $s['warnings'],
	);
}

try {
	$sh_report = sh_import_run();
	$sh_cfg    = sh_imp_config();
	if ( '' !== $sh_cfg['report'] ) {
		$sh_prev   = is_readable( $sh_cfg['report'] ) ? json_decode( (string) file_get_contents( $sh_cfg['report'] ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$sh_runs   = is_array( $sh_prev ) && isset( $sh_prev['runs'] ) ? $sh_prev['runs'] : array();
		$sh_runs[] = $sh_report;
		file_put_contents( $sh_cfg['report'], wp_json_encode( array( 'runs' => $sh_runs ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}
	echo 'sh-import OK ' . wp_json_encode( $sh_report['counts'] ) . ' created=' . wp_json_encode( $sh_report['created'] ) . ' warnings=' . count( $sh_report['warnings'] ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
	foreach ( $sh_report['warnings'] as $sh_w ) { // Same list as runs[-1].warnings of the report — never only a count.
		echo 'sh-import WARNING: ' . $sh_w . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
	}
} catch ( Throwable $e ) {
	echo 'sh-import FAILED: ' . $e->getMessage() . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output.
	throw $e;
}
