<?php
/**
 * Site options (group "site" in fields.php) — Settings › Shared Heritage.
 * ACF free has no options pages, so this uses the WordPress Settings API (nonce + manage_options
 * handled by options.php). Values are wp_options rows named after the field (sh_label_read_more …).
 *
 * @package sharedheritage-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register each site field as a setting (sanitize + REST under /wp/v2/settings).
 */
function sh_core_register_settings(): void {
	foreach ( sh_core_group_fields( 'site' ) as $field ) {
		$type = sh_core_meta_type( $field );
		register_setting(
			'sh_site',
			$field['name'],
			array(
				'type'              => $type,
				'description'       => (string) $field['label'],
				'sanitize_callback' => static fn( $value ) => sh_core_sanitize( $field, $value ),
				'show_in_rest'      => isset( $field['rest'] ) ? (bool) $field['rest'] : true,
				'default'           => isset( $field['default'] ) ? $field['default'] : ( 'integer' === $type ? 0 : '' ),
			)
		);
	}
}
add_action( 'init', 'sh_core_register_settings', 20 );

/**
 * Add the settings page.
 */
function sh_core_settings_menu(): void {
	add_options_page( 'Shared Heritage', 'Shared Heritage', 'manage_options', 'sh-site', 'sh_core_settings_page' );
}
add_action( 'admin_menu', 'sh_core_settings_menu' );

/**
 * Media picker for image options (only on our page).
 *
 * @param string $hook Current admin page hook.
 */
function sh_core_settings_assets( string $hook ): void {
	if ( 'settings_page_sh-site' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	$js = <<<'JS'
document.addEventListener('click', function (e) {
	var btn = e.target.closest('[data-sh-pick]');
	if (!btn || !window.wp || !wp.media) { return; }
	e.preventDefault();
	var input = document.getElementById(btn.getAttribute('data-sh-pick'));
	var frame = wp.media({ library: { type: 'image' }, multiple: false });
	frame.on('select', function () { input.value = frame.state().get('selection').first().get('id'); });
	frame.open();
});
JS;
	wp_add_inline_script( 'media-editor', $js );
}
add_action( 'admin_enqueue_scripts', 'sh_core_settings_assets' );

/**
 * Render the settings page.
 */
function sh_core_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Shared Heritage — site', 'sharedheritage' ) . '</h1>';
	echo '<p>' . esc_html__( 'Menus are edited in Appearance › Menus.', 'sharedheritage' ) . '</p>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'sh_site' );
	echo '<table class="form-table" role="presentation">';
	foreach ( sh_core_group_fields( 'site' ) as $field ) {
		$name  = (string) $field['name'];
		$value = get_option( $name );
		echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( (string) $field['label'] ) . '</label></th><td>';
		if ( 'image' === $field['type'] ) {
			$id = absint( $value );
			echo '<input type="number" min="0" class="small-text" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $id ) . '"> ';
			echo '<button type="button" class="button" data-sh-pick="' . esc_attr( $name ) . '">' . esc_html__( 'Choose image', 'sharedheritage' ) . '</button>';
			if ( $id ) {
				echo '<p>' . wp_get_attachment_image( $id, 'medium' ) . '</p>';
			}
			echo '<p class="description">' . esc_html__( 'Attachment ID (0 = none).', 'sharedheritage' ) . '</p>';
		} else {
			echo '<input type="text" class="regular-text" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '">';
		}
		echo '</td></tr>';
	}
	echo '</table>';
	submit_button();
	echo '</form></div>';
}
