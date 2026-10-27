<?php
/**
 * Turn sh_core_field_groups() into: registered meta (sanitize + REST, works without ACF) and
 * ACF local field groups (edit UI). ACF missing → admin notice, data still readable via get_*_meta.
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Storage type of an ACF field type.
 *
 * @param array<string, mixed> $field Field definition.
 */
function sh_core_meta_type( array $field ): string {
	switch ( $field['type'] ) {
		case 'number':
		case 'image':
		case 'file':
		case 'post_object':
		case 'taxonomy':
			return 'integer';
		case 'true_false':
			return 'boolean';
		case 'relationship':
			return 'array';
		default:
			return 'string';
	}
}

/**
 * Sanitize one value for a field (also used by the importer through update_*_meta).
 *
 * @param array<string, mixed> $field Field definition.
 * @param mixed                $value Raw value.
 * @return mixed
 */
function sh_core_sanitize( array $field, $value ) {
	switch ( $field['type'] ) {
		case 'number':
			return is_numeric( $value ) ? (int) $value : 0;
		case 'image':
		case 'file':
		case 'post_object':
		case 'taxonomy':
			return is_numeric( $value ) ? absint( $value ) : 0;
		case 'true_false':
			return rest_sanitize_boolean( $value );
		case 'relationship':
			$ids = is_array( $value ) ? $value : ( '' === $value || null === $value ? array() : explode( ',', (string) $value ) );
			return array_values( array_filter( array_map( 'absint', $ids ) ) );
		case 'color_picker':
			return (string) sanitize_hex_color( is_string( $value ) ? trim( $value ) : '' );
		case 'textarea':
			if ( isset( $field['format'] ) && 'json' === $field['format'] ) {
				return sh_core_sanitize_json( $value );
			}
			return sanitize_textarea_field( is_scalar( $value ) ? (string) $value : '' );
		default:
			return sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
	}
}

/**
 * JSON object text → normalised JSON text, or '' when not a JSON object.
 *
 * @param mixed $value Raw value (string or array).
 */
function sh_core_sanitize_json( $value ): string {
	if ( is_array( $value ) ) {
		$data = $value;
	} else {
		// update_metadata() has already unslashed the value before sanitize_meta().
		$data = json_decode( is_string( $value ) ? $value : '', true );
	}
	if ( ! is_array( $data ) ) {
		return '';
	}
	$json = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	return is_string( $json ) ? $json : '';
}

/**
 * register_meta() args for a field.
 *
 * @param array<string, mixed> $field Field definition.
 * @param string               $cap   Capability checked against the object id (edit_post / edit_term).
 * @return array<string, mixed>
 */
function sh_core_meta_args( array $field, string $cap ): array {
	$type = sh_core_meta_type( $field );
	$rest = isset( $field['rest'] ) ? (bool) $field['rest'] : true;
	if ( $rest && 'array' === $type ) {
		$rest = array(
			'schema' => array(
				'type'  => 'array',
				'items' => array( 'type' => 'integer' ),
			),
		);
	}
	return array(
		'type'              => $type,
		'single'            => true,
		'description'       => (string) $field['label'],
		'show_in_rest'      => $rest,
		'sanitize_callback' => static fn( $value ) => sh_core_sanitize( $field, $value ),
		'auth_callback'     => static fn( $allowed, $meta_key, $object_id ) => current_user_can( $cap, $object_id ),
	);
}

/**
 * Register every field as post/term meta (pages share the 'page' subtype: keys are unique per page group).
 */
function sh_core_register_meta(): void {
	foreach ( sh_core_field_groups() as $id => $group ) {
		$target = $group['target'];
		if ( isset( $target['option'] ) ) {
			continue; // Options: inc/settings.php.
		}
		foreach ( sh_core_group_fields( $id ) as $field ) {
			if ( isset( $target['taxonomy'] ) ) {
				register_term_meta( $target['taxonomy'], $field['name'], sh_core_meta_args( $field, 'edit_term' ) );
			} else {
				$post_type = isset( $target['post_type'] ) ? $target['post_type'] : 'page';
				register_post_meta( $post_type, $field['name'], sh_core_meta_args( $field, 'edit_post' ) );
			}
		}
	}
}
add_action( 'init', 'sh_core_register_meta', 20 );

/**
 * ACF location rule for a group target. Pages other than the front page are matched by slug
 * (route table: page-intro.php / page-timeline.php) — resolved to the page id at load time.
 *
 * @param array<string, mixed> $target Group target.
 * @return array<int, array<int, array<string, string>>>|null Null = page not found (group not shown).
 */
function sh_core_acf_location( array $target ): ?array {
	if ( isset( $target['post_type'] ) ) {
		return array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => $target['post_type'] ) ) );
	}
	if ( isset( $target['taxonomy'] ) ) {
		return array( array( array( 'param' => 'taxonomy', 'operator' => '==', 'value' => $target['taxonomy'] ) ) );
	}
	if ( isset( $target['page'] ) && 'front' === $target['page'] ) {
		return array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) );
	}
	if ( isset( $target['page'] ) ) {
		$page_id = sh_page_id( (string) $target['page'] );
		return $page_id ? array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $page_id ) ) ) : null;
	}
	return null;
}

/**
 * Field definition → ACF field array.
 *
 * @param string               $group Group id.
 * @param array<string, mixed> $field Field definition.
 * @param int                  $i     Position (for tab keys).
 * @return array<string, mixed>
 */
function sh_core_acf_field( string $group, array $field, int $i ): array {
	$acf = $field;
	unset( $acf['rest'], $acf['format'], $acf['default'] );
	$acf['key'] = 'tab' === $field['type'] ? "field_sh_{$group}_tab_{$i}" : "field_{$group}_{$field['name']}";
	if ( in_array( $field['type'], array( 'image', 'file', 'post_object', 'relationship', 'taxonomy' ), true ) ) {
		$acf['return_format'] = 'id';
	}
	if ( 'image' === $field['type'] ) {
		$acf['preview_size'] = 'medium';
		$acf['library']      = 'all';
	}
	if ( 'textarea' === $field['type'] ) {
		$acf['new_lines'] = ''; // Raw text; templates split paragraphs themselves.
	}
	return $acf;
}

/**
 * Register ACF local field groups (versioned in git, not in the database).
 */
function sh_core_register_acf_groups(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$order = 0;
	foreach ( sh_core_field_groups() as $id => $group ) {
		if ( isset( $group['target']['option'] ) ) {
			continue; // ACF free has no options pages: inc/settings.php renders these.
		}
		$location = sh_core_acf_location( $group['target'] );
		if ( null === $location ) {
			continue;
		}
		$fields = array();
		foreach ( $group['fields'] as $i => $field ) {
			$fields[] = sh_core_acf_field( $id, $field, (int) $i );
		}
		acf_add_local_field_group(
			array(
				'key'                   => 'group_sh_' . $id,
				'title'                 => $group['title'],
				'fields'                => $fields,
				'location'              => $location,
				'menu_order'            => $order++,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => true,
				'show_in_rest'          => 0, // REST comes from register_meta (meta.sh_*), not from ACF.
			)
		);
	}
}
add_action( 'acf/init', 'sh_core_register_acf_groups' );

/**
 * Reject invalid JSON in format=json fields before ACF saves.
 *
 * @param mixed $valid Current validity (true or message).
 * @param mixed $value Submitted value.
 * @return mixed
 */
function sh_core_validate_json( $valid, $value ) {
	if ( true !== $valid || '' === $value || null === $value ) {
		return $valid;
	}
	// The submitted value may still carry request slashes here.
	$ok = '' !== sh_core_sanitize_json( $value ) || ( is_string( $value ) && '' !== sh_core_sanitize_json( wp_unslash( $value ) ) );
	return $ok ? $valid : 'Must be a JSON object, e.g. {"panel_y": 960}.';
}
add_filter( 'acf/validate_value/name=sh_layout', 'sh_core_validate_json', 10, 2 );

/**
 * ACF missing → tell admins (data stays readable by templates and REST; editing needs ACF).
 */
function sh_core_acf_notice(): void {
	if ( class_exists( 'ACF' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Shared Heritage: the plugin "Advanced Custom Fields" is not active — content fields cannot be edited in their boxes. Install/activate ACF (free) from Plugins.', 'sharedheritage' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'sh_core_acf_notice' );
