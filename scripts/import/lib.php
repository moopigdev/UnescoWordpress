<?php
/**
 * Importer helpers (loaded by import.php inside a running WordPress).
 * Idempotent: every created post/term/attachment carries meta _sh_import_key and is looked up by it first.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Config from constants (playground blueprint) or environment (wp eval-file). Paths outside the repo.
 *
 * @return array{content:string,assets:string,media:string,map:string,mode:string,report:string,subsizes:bool}
 */
function sh_imp_config(): array {
	$get  = static function ( string $name, string $default ): string {
		if ( defined( $name ) ) {
			return (string) constant( $name );
		}
		$env = getenv( $name );
		return ( false !== $env && '' !== $env ) ? $env : $default;
	};
	$repo = dirname( __DIR__, 2 );
	$root = is_dir( dirname( $repo ) . '/design' ) ? dirname( $repo ) : dirname( $repo, 2 ); // repo or .worktrees/<wt>.
	$mode = $get( 'SH_IMPORT_MODE', 'missing' );
	return array(
		'content'  => rtrim( $get( 'SH_IMPORT_CONTENT_DIR', $repo . '/content' ), '/' ),
		'assets'   => rtrim( $get( 'SH_IMPORT_ASSETS_DIR', $root . '/design/assets' ), '/' ),
		'media'    => rtrim( $get( 'SH_IMPORT_MEDIA_DIR', $root . '/media/out' ), '/' ),
		'map'      => $get( 'SH_IMPORT_MAP', __DIR__ . '/image-map.json' ),
		'mode'     => in_array( $mode, array( 'missing', 'overwrite' ), true ) ? $mode : 'missing',
		'report'   => $get( 'SH_IMPORT_REPORT', '' ),
		'subsizes' => '1' === $get( 'SH_IMPORT_SUBSIZES', '0' ),
	);
}

/**
 * Read a JSON file or stop.
 *
 * @param string $path File.
 * @return array<string, mixed>
 */
function sh_imp_json( string $path ): array {
	$raw  = is_readable( $path ) ? file_get_contents( $path ) : false; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$data = false === $raw ? null : json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		throw new RuntimeException( 'cannot read JSON: ' . $path );
	}
	return $data;
}

/**
 * Run-wide state: config, counters, warnings, attachment cache.
 *
 * @return array<string, mixed>
 */
function &sh_imp_state(): array {
	static $state = array(
		'created'  => array(),
		'updated'  => array(),
		'skipped'  => array(),
		'warnings' => array(),
		'att'      => null,
	);
	return $state;
}

/**
 * Count an action per kind.
 *
 * @param string $what created|updated|skipped.
 * @param string $kind Entity kind.
 */
function sh_imp_count( string $what, string $kind ): void {
	$s                   = &sh_imp_state();
	$s[ $what ][ $kind ] = ( isset( $s[ $what ][ $kind ] ) ? $s[ $what ][ $kind ] : 0 ) + 1;
}

/**
 * Record a warning (missing file etc.).
 *
 * @param string $msg Message.
 */
function sh_imp_warn( string $msg ): void {
	$s               = &sh_imp_state();
	$s['warnings'][] = $msg;
}

/**
 * Find a post by import key (any status except trash).
 *
 * @param string $post_type Post type.
 * @param string $key       Import key.
 */
function sh_imp_find_post( string $post_type, string $key ): int {
	$ids = get_posts(
		array(
			'post_type'        => $post_type,
			'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future', 'inherit' ),
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'suppress_filters' => true,
			'meta_key'         => '_sh_import_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'       => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return $ids ? (int) $ids[0] : 0;
}

/**
 * Create or (mode overwrite) update a post. Returns [id, write_fields].
 *
 * @param string               $kind Entity kind for counters.
 * @param string               $key  Import key.
 * @param array<string, mixed> $post wp_insert_post args (unslashed).
 * @return array{0:int,1:bool}
 */
function sh_imp_post( string $kind, string $key, array $post ): array {
	$cfg = sh_imp_config();
	$id  = sh_imp_find_post( (string) $post['post_type'], $key );
	if ( $id && 'missing' === $cfg['mode'] ) {
		sh_imp_count( 'skipped', $kind );
		return array( $id, false );
	}
	if ( $id ) {
		$post['ID'] = $id;
	}
	$post['post_status'] = isset( $post['post_status'] ) ? $post['post_status'] : 'publish';
	$res                 = $id ? wp_update_post( wp_slash( $post ), true ) : wp_insert_post( wp_slash( $post ), true );
	if ( is_wp_error( $res ) ) {
		throw new RuntimeException( $kind . ' ' . $key . ': ' . $res->get_error_message() );
	}
	update_post_meta( (int) $res, '_sh_import_key', $key );
	sh_imp_count( $id ? 'updated' : 'created', $kind );
	return array( (int) $res, true );
}

/**
 * Create or (mode overwrite) update a term by slug. Returns [term_id, write_fields].
 *
 * @param string $taxonomy    Taxonomy.
 * @param string $slug        Slug.
 * @param string $name        Name.
 * @param string $description Description.
 * @return array{0:int,1:bool}
 */
function sh_imp_term( string $taxonomy, string $slug, string $name, string $description = '' ): array {
	$cfg  = sh_imp_config();
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term instanceof WP_Term && 'missing' === $cfg['mode'] ) {
		sh_imp_count( 'skipped', $taxonomy );
		return array( $term->term_id, false );
	}
	$args = wp_slash(
		array(
			'slug'        => $slug,
			'name'        => $name,
			'description' => $description,
		)
	);
	$res  = $term instanceof WP_Term ? wp_update_term( $term->term_id, $taxonomy, $args ) : wp_insert_term( wp_slash( $name ), $taxonomy, $args );
	if ( is_wp_error( $res ) ) {
		throw new RuntimeException( $taxonomy . ' ' . $slug . ': ' . $res->get_error_message() );
	}
	update_term_meta( (int) $res['term_id'], '_sh_import_key', $taxonomy . ':' . $slug );
	sh_imp_count( $term instanceof WP_Term ? 'updated' : 'created', $taxonomy );
	return array( (int) $res['term_id'], true );
}

/**
 * Write meta (post or term). null values are skipped; everything else goes through the registered sanitize.
 *
 * @param string               $type post|term.
 * @param int                  $id   Object id.
 * @param array<string, mixed> $meta key => value.
 */
function sh_imp_meta( string $type, int $id, array $meta ): void {
	foreach ( $meta as $key => $value ) {
		if ( null === $value ) {
			continue;
		}
		$value = is_string( $value ) ? wp_slash( $value ) : $value; // update_metadata() unslashes.
		'term' === $type ? update_term_meta( $id, $key, $value ) : update_post_meta( $id, $key, $value );
	}
}
