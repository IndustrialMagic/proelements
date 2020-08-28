<?php

/**
 * Plugin Name: PRO Elements
 * Description: This plugin enables GPL features of Elementor Pro: widgets, theme builder, dynamic colors & content, forms & popup builder, and more. Note that PRO Elements is not a substitute for Elementor Pro. If you need all Elementor Pro features, including access to pro templates library and dedicated support, we encourage you to <a href="https://elementor.com/pro/" target="_blank">purchase Elementor Pro</a>.
 * Plugin URI: https://proelements.org/
 * Author: PROElements.org
 * Version: 3.0.1
 * Author URI: https://proelements.org/
 *
 * Text Domain: elementor-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function pro_elements_plugin_load_plugin() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'pro_elements_plugin_fail_load' );
		return;
	}
	if ( defined('ELEMENTOR_PRO_VERSION' )) {
		return;
	}

	define( 'ELEMENTOR_PRO_VERSION', '3.0.1' );
	define( 'ELEMENTOR_PRO_PREVIOUS_STABLE_VERSION', '2.10.3' );

	define( 'ELEMENTOR_PRO__FILE__', __FILE__ );
	define( 'ELEMENTOR_PRO_PLUGIN_BASE', plugin_basename( ELEMENTOR_PRO__FILE__ ) );
	define( 'ELEMENTOR_PRO_PATH', plugin_dir_path( ELEMENTOR_PRO__FILE__ ) );
	define( 'ELEMENTOR_PRO_ASSETS_PATH', ELEMENTOR_PRO_PATH . 'assets/' );
	define( 'ELEMENTOR_PRO_MODULES_PATH', ELEMENTOR_PRO_PATH . 'modules/' );
	define( 'ELEMENTOR_PRO_URL', plugins_url( '/', ELEMENTOR_PRO__FILE__ ) );
	define( 'ELEMENTOR_PRO_ASSETS_URL', ELEMENTOR_PRO_URL . 'assets/' );
	define( 'ELEMENTOR_PRO_MODULES_URL', ELEMENTOR_PRO_URL . 'modules/' );
	define( 'IS_PRO_ELEMENTS', 'true' );

	load_plugin_textdomain( 'elementor-pro' );

	$elementor_version_required = '3.0.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'pro_elements_plugin_fail_load_out_of_date' );

		return;
	}

	require ELEMENTOR_PRO_PATH . 'plugin.php';
}

pro_elements_plugin_load_plugin();

function pro_elements_plugin_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'Pro Elements module is not working because you are using an old version of Elementor.', 'elementor-pro' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'elementor-pro' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function pro_elements_plugin_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

		$message = '<p>' . __( 'Pro Elements is not working because you need to activate the Elementor plugin.', 'elementor-pro' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', 'elementor-pro' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		$message = '<p>' . __( 'Pro Elements is not working because you need to install the Elementor plugin.', 'elementor-pro' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Elementor Now', 'elementor-pro' ) ) . '</p>';
	}

	echo '<div class="error"><p>' . $message . '</p></div>';
}

if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}
