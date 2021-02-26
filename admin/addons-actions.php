<?php 
/**
 * Open modal popup.
 *
 * @since x.x.x
 */
function hfe_admin_modal() {

	// Run a security check.
	check_ajax_referer( 'hfe-admin-nonce', 'nonce' );

	update_user_meta( get_current_user_id(), 'hfe-popup', 'dismissed' );

}
add_action( 'wp_ajax_hfe_admin_modal', 'hfe_admin_modal' );

/**
 * Deactivate addon.
 *
 * @since x.x.x
 */
function hfe_deactivate_addon() {

	// Run a security check.
	check_ajax_referer( 'hfe-admin-nonce', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'deactivate_plugins' ) ) {
		wp_send_json_error( esc_html__( 'Plugin deactivation is disabled for you on this site.', 'header-footer-elementor' ) );
	}

	$type = 'addon';
	if ( ! empty( $_POST['type'] ) ) {
		$type = sanitize_key( $_POST['type'] );
	}

	if ( isset( $_POST['plugin'] ) ) {
		$plugin = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );

		deactivate_plugins( $plugin );

		do_action( 'hfe_plugin_deactivated', $plugin );

		if ( 'plugin' === $type ) {
			wp_send_json_success( esc_html__( 'Plugin deactivated.', 'header-footer-elementor' ) );
		} else {
			wp_send_json_success( esc_html__( 'Addon deactivated.', 'header-footer-elementor' ) );
		}
	}

	wp_send_json_error( esc_html__( 'Could not deactivate the addon. Please deactivate from the Plugins page.', 'header-footer-elementor' ) );
}
add_action( 'wp_ajax_hfe_deactivate_addon', 'hfe_deactivate_addon' );

/**
 * Activate addon.
 *
 * @since x.x.x
 */
function hfe_activate_addon() {

	// Run a security check.
	check_ajax_referer( 'hfe-admin-nonce', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error( esc_html__( 'Plugin activation is disabled for you on this site.', 'header-footer-elementor' ) );
	}

	if ( isset( $_POST['plugin'] ) ) {

		$type = 'addon';
		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}

		$plugin   = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
		$activate = activate_plugins( $plugin );

		do_action( 'hfe_plugin_activated', $plugin );

		if ( ! is_wp_error( $activate ) ) {
			if ( 'plugin' === $type ) {
				wp_send_json_success( esc_html__( 'Plugin activated.', 'header-footer-elementor' ) );
			} else {
				wp_send_json_success( esc_html__( 'Addon activated.', 'header-footer-elementor' ) );
			}
		}
	}

	wp_send_json_error( esc_html__( 'Could not activate addon. Please activate from the Plugins page.', 'header-footer-elementor' ) );
}
add_action( 'wp_ajax_hfe_activate_addon', 'hfe_activate_addon' );

/**
 * Install addon.
 *
 * @since x.x.x
 */
function hfe_install_addon() {

	// Run a security check.
	check_ajax_referer( 'hfe-admin-nonce', 'nonce' );

	$generic_error = esc_html__( 'There was an error while performing your request.', 'header-footer-elementor' );

	$type = 'addon';
	if ( ! empty( $_POST['type'] ) ) {
		$type = sanitize_key( $_POST['type'] );
	}

	// Check if new installations are allowed.
	if ( ! hfe_can_install( $type ) ) {
		wp_send_json_error( $generic_error );
	}

	$error = esc_html__( 'Could not install addon. Please download from wpforms.com and install manually.', 'header-footer-elementor' );

	if ( empty( $_POST['plugin'] ) ) {
		wp_send_json_error( $error );
	}

	// Set the current screen to avoid undefined notices.
	set_current_screen( 'appearance_page_hfe-about' );

	// Prepare variables.
	$url = esc_url_raw(
		add_query_arg(
			array(
				'page' => 'hfe-about',
			),
			admin_url( 'admin.php' )
		)
	);

	$creds = request_filesystem_credentials( $url, '', false, false, null );

	// Check for file system permissions.
	if ( false === $creds ) {
		wp_send_json_error( $error );
	}

	if ( ! WP_Filesystem( $creds ) ) {
		wp_send_json_error( $error );
	}

	/*
	 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
	 */
	require_once HFE_DIR . 'admin/class-skin-install.php';

	// Do not allow WordPress to search/download translations, as this will break JS output.
	remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

	// Create the plugin upgrader with our custom skin.
	$installer = new HFE\Inc\Helpers\HFE_PluginInstaller( new HFE_Skin_Install() );

	// Error check.
	if ( ! method_exists( $installer, 'install' ) || empty( $_POST['plugin'] ) ) {
		wp_send_json_error( $error );
	}

	$installer->install( $_POST['plugin'] ); // phpcs:ignore

	// Flush the cache and return the newly installed plugin basename.
	wp_cache_flush();

	$plugin_basename = $installer->plugin_info();

	if ( empty( $plugin_basename ) ) {
		wp_send_json_error( $error );
	}

	$result = array(
		'msg'          => $generic_error,
		'is_activated' => false,
		'basename'     => $plugin_basename,
	);

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		$result['msg'] = 'plugin' === $type ? esc_html__( 'Plugin installed.', 'header-footer-elementor' ) : esc_html__( 'Addon installed.', 'header-footer-elementor' );

		wp_send_json_success( $result );
	}

	// Activate the plugin silently.
	$activated = activate_plugin( $plugin_basename );

	if ( ! is_wp_error( $activated ) ) {
		$result['is_activated'] = true;
		$result['msg']          = 'plugin' === $type ? esc_html__( 'Plugin installed & activated.', 'header-footer-elementor' ) : esc_html__( 'Addon installed & activated.', 'header-footer-elementor' );

		wp_send_json_success( $result );
	}

	// Fallback error just in case.
	wp_send_json_error( $result );
}
add_action( 'wp_ajax_hfe_install_addon', 'hfe_install_addon' );

/**
 * Update Subscription
 */
function update_subscription() {

	check_ajax_referer( 'hfe-admin-nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'You can\'t perform this action.' );
	}

	$arguments = isset( $_POST['data'] ) ? array_map( 'sanitize_text_field', json_decode( stripslashes( $_POST['data'] ), true ) ) : array();

	// $url = add_query_arg( $arguments, $this->api_domain . 'wp-json/starter-templates/v1/subscribe/' ); // add URL of your site or mail API

	// $response = wp_remote_post( $url );
	// if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
	// 	$response = json_decode( wp_remote_retrieve_body( $response ), true );

	// 	// Successfully subscribed.
	// 	if ( isset( $response['success'] ) && $response['success'] ) {
	// 		update_user_meta( get_current_user_ID(), 'astra-sites-subscribed', 'yes' );
	// 	}
	// }

	// wp_send_json_success( $response );

	update_user_meta( get_current_user_ID(), 'hfe-subscribed', 'yes' );

	wp_send_json_success( 'true' );
}

add_action( 'wp_ajax_hfe-update-subscription', 'update_subscription' );

?>