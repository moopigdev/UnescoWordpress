<?php
/**
 * Importer: which seed objects are imported, their Final frame, and which design/assets file a style "*_ref"
 * value points to. Pure PHP (no WordPress) — shared by the import (objects.php · media.php · refresh.php) and by
 * bundle-files.php, which scripts/deploy.sh runs on the local machine to list every file the import reads.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || defined( 'SH_IMPORT_CLI' ) || exit;

/**
 * Seed objects in scope (D7), ordered by elephant trail order.
 *
 * @param array<string, mixed> $seed Seed files.
 * @return array<int, array<string, mixed>>
 */
function sh_import_scope_objects( array $seed ): array {
	$list = array_values( array_filter( $seed['objects']['objects'], static fn( $o ) => ! empty( $o['trail_order']['elephant'] ) ) );
	usort( $list, static fn( $a, $b ) => (int) $a['trail_order']['elephant'] <=> (int) $b['trail_order']['elephant'] );
	return $list;
}

/**
 * The Figma record of the Final page ("Page 2  Version #Final …") — the frame the detail page must match.
 *
 * @param array<string, mixed> $o Seed object.
 * @return array<string, mixed>
 */
function sh_import_final_frame( array $o ): array {
	foreach ( $o['figma'] as $f ) {
		if ( str_contains( (string) $f['page'], 'Final' ) ) {
			return $f;
		}
	}
	return $o['figma'][0];
}

/**
 * Every "*_ref" key (any depth) with a non-empty string value: list of [path (keys from the root), value].
 *
 * @param array<int|string, mixed> $node Style (sub)tree.
 * @param array<int, int|string>   $path Keys above $node.
 * @return array<int, array{0: array<int, int|string>, 1: string}>
 */
function sh_import_style_refs( array $node, array $path = array() ): array {
	$out = array();
	foreach ( $node as $k => $v ) {
		$here = array_merge( $path, array( $k ) );
		if ( is_string( $k ) && str_ends_with( $k, '_ref' ) && is_string( $v ) && '' !== $v ) {
			$out[] = array( $here, $v );
		} elseif ( is_array( $v ) ) {
			$out = array_merge( $out, sh_import_style_refs( $v, $here ) );
		}
	}
	return $out;
}

/**
 * File name in $dir that a "*_ref" value points to ('' = none): image-map "files" entry of the value, the value
 * itself, or the value + png/jpg/jpeg/webp. Never leaves $dir (basename · no dot-files).
 *
 * @param array<string, mixed> $map   image-map.json.
 * @param string               $value Value of the *_ref key.
 * @param string               $dir   design/assets directory.
 */
function sh_import_ref_file( array $map, string $value, string $dir ): string {
	$name = basename( $value );
	$cand = array();
	if ( isset( $map['files'][ $name ] ) ) {
		$cand[] = (string) $map['files'][ $name ];
	}
	$cand[] = $name;
	foreach ( array( 'png', 'jpg', 'jpeg', 'webp' ) as $ext ) {
		$cand[] = $name . '.' . $ext;
	}
	foreach ( $cand as $file ) {
		if ( '' !== $file && '.' !== $file[0] && ! str_contains( $file, '/' ) && is_file( $dir . '/' . $file ) ) {
			return $file;
		}
	}
	return '';
}

/**
 * Every design/assets file the import reads: image-map "files" + the "*_ref" files of the in-scope objects'
 * Final frames (the same objects and frames sh_import_objects() / sh_import_layout() use).
 *
 * @param array<string, mixed> $seed Seed files (needs "objects").
 * @param array<string, mixed> $map  image-map.json.
 * @param string               $dir  design/assets directory.
 * @return array{map: string[], refs: string[], missing: string[], unsafe: string[]}
 *         map = image-map files · refs = *_ref files not already in map · missing = referenced but not in $dir.
 */
function sh_import_bundle_files( array $seed, array $map, string $dir ): array {
	$out = array(
		'map'     => array(),
		'refs'    => array(),
		'missing' => array(),
		'unsafe'  => array(),
	);
	foreach ( (array) ( isset( $map['files'] ) ? $map['files'] : array() ) as $file ) {
		$file = (string) $file;
		if ( '' === $file || '.' === $file[0] || str_contains( $file, '/' ) ) {
			$out['unsafe'][] = $file;
		} elseif ( ! is_file( $dir . '/' . $file ) ) {
			$out['missing'][] = 'image-map: ' . $file;
		} else {
			$out['map'][ $file ] = $file;
		}
	}
	foreach ( sh_import_scope_objects( $seed ) as $o ) {
		foreach ( sh_import_style_refs( (array) sh_import_final_frame( $o )['style'] ) as $ref ) {
			$file = sh_import_ref_file( $map, $ref[1], $dir );
			if ( '' === $file ) {
				$out['missing'][] = '*_ref ' . $o['slug'] . ' ' . implode( '.', $ref[0] ) . ': ' . $ref[1];
			} elseif ( ! isset( $out['map'][ $file ] ) ) {
				$out['refs'][ $file ] = $file;
			}
		}
	}
	foreach ( array( 'map', 'refs', 'missing', 'unsafe' ) as $k ) {
		$out[ $k ] = array_values( array_unique( $out[ $k ] ) );
		sort( $out[ $k ] );
	}
	return $out;
}
