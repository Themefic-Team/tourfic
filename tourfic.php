<?php
/**
 * Plugin Name:     Tourfic - Travel, Hotel, and Apartment Booking Solution for WooCommerce
 * Plugin URI:      https://themefic.com/tourfic
 * Description:     The Ultimate WordPress plugin for tour, travel, accommodation, and hotel bookings. Effortlessly manage your entire online travel booking system, including orders and any WooCommerce payment method.
 * Author:          Themefic
 * Author URI:      https://themefic.com
 * Text Domain:     tourfic
 * Domain Path:     /lang/
 * Version:         2.11.22
 * Tested up to:    6.4.3
 * WC tested up to: 8.6
 * Requires PHP:    7.4
 * Elementor tested up to: 3.19.2
 */

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Including Plugin file
 *
 * @since 1.0
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Tourfic All the Defines
 *
 * @since 1.0
 */
// URLs
define( 'TOURFIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TOURFIC_TEMPLATES_URL', TOURFIC_PLUGIN_URL . 'templates/' );
define( 'TOURFIC_ADMIN_URL', TOURFIC_PLUGIN_URL . 'admin/' );
define( 'TF_URL', plugin_dir_url( __FILE__ ) );
define( 'TF_TEMPLATES_URL', TF_URL . 'templates/' );
define( 'TF_ADMIN_URL', TF_URL . 'admin/' );
define( 'TF_ASSETS_URL', TF_URL . 'assets/' );
define( 'TF_ASSETS_APP_URL', TF_ASSETS_URL . 'app/' );
define( 'TF_ASSETS_ADMIN_URL', TF_ASSETS_URL . 'admin/' );
// Paths
define( 'TF_PATH', plugin_dir_path( __FILE__ ) );
define( 'TF_ADMIN_PATH', TF_PATH . 'admin/' );
define( 'TF_INC_PATH', TF_PATH . 'inc/' );
define( 'TF_TEMPLATE_PATH', TF_PATH . 'templates/' );
define( 'TF_TEMPLATE_PART_PATH', TF_TEMPLATE_PATH . 'template-parts/' );
define( 'TF_OPTIONS_PATH', TF_ADMIN_PATH . 'options/' );
define( 'TF_ASSETS_PATH', TF_PATH . 'assets/' );
define( 'TF_EMAIL_TEMPLATES_PATH', TF_PATH . 'admin/emails/templates/' );

/**
 * Tourfic Define
 *
 * @since 1.0
 */
if ( ! defined( 'TOURFIC' ) ) {
	define( 'TOURFIC', '2.11.22' );
}

// Styles & Scripts
if ( ! defined( 'TOURFIC_PRO_SCRIPT' ) ) {
	require_once TF_INC_PATH . 'style-script.php';
}

/**
 * Show admin warning if a required file is missing
 */
function tf_file_missing( $files = '' ) {

	if ( is_admin() ) {
		if ( ! empty( $files ) ) {
			$class   = 'notice notice-error';
			$message = '<strong>' . $files . '</strong>' . esc_html__( ' file is missing! It is required to function Tourfic properly!', 'tourfic' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
		}
	}

}

add_action( 'admin_notices', 'tf_file_missing' );


/**
 * Load the text domain to make the plugin's strings available for localisation.
 *
 * @since 1.0.0
 */
function tf_load_textdomain() {

	$locale = apply_filters( 'plugin_locale', get_locale(), 'tourfic' );
	// Allow upgrade safe, site specific language files in /wp-content/languages/tourfic/
	load_textdomain( 'tourfic', WP_LANG_DIR . '/tourfic/tourfic-' . $locale . '.mo' );
	// Then check for a language file in /wp-content/plugins/tourfic/lang/ (this will be overriden by any file already loaded)
	load_plugin_textdomain( 'tourfic', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

}

add_action( 'plugins_loaded', 'tf_load_textdomain' );

/**
 * Plugins Loaded Actions
 *
 * Including Option Panel
 *
 * Including Options
 */
if ( ! function_exists( 'tf_plugin_loaded_action' ) ) {
	function tf_plugin_loaded_action() {

		if ( file_exists( TF_ADMIN_PATH . 'tf-options/TF_Options.php' ) ) {
			require_once TF_ADMIN_PATH . 'tf-options/TF_Options.php';
		} else {
			tf_file_missing( TF_ADMIN_PATH . 'tf-options/TF_Options.php' );
		}

	}
}
add_action( 'plugins_loaded', 'tf_plugin_loaded_action' );

/**
 * Global Admin Get Option
 */
if ( ! function_exists( 'tfopt' ) ) {
	function tfopt( $option = '', $default = null ) {
		$options = get_option( 'tf_settings' );

		return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
	}
}

/**
 * All the requires
 */
// Classes
if ( file_exists( TF_INC_PATH . 'classes.php' ) ) {
	require_once TF_INC_PATH . 'classes.php';
} else {
	tf_file_missing( TF_INC_PATH . 'classes.php' );
}

// Functions
if ( file_exists( TF_INC_PATH . 'functions.php' ) ) {
	require_once TF_INC_PATH . 'functions.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions.php' );
}

// Admin Functions
if ( file_exists( TF_ADMIN_PATH . 'inc/functions.php' ) ) {
	require_once TF_ADMIN_PATH . 'inc/functions.php';
} else {
	tf_file_missing( TF_ADMIN_PATH . 'inc/functions.php' );
}
// Admin Functions
if ( file_exists( TF_ADMIN_PATH . 'emails/class-tf-handle-emails.php' ) ) {
	require_once TF_ADMIN_PATH . 'emails/class-tf-handle-emails.php';
} else {
	tf_file_missing( TF_ADMIN_PATH . 'emails/class-tf-handle-emails.php' );
}

function tf_active_template_settings_callback() {
	//all code goes here if need
	update_option( 'tourfic_template_installed', true );
}

//Register activation hook
register_activation_hook( __FILE__, 'tf_active_template_settings_callback' );

/**
 * Compatibility with custom order tables for the WooCommerce plugin
 *
 * @since 2.10.4
 * @access public
 * @return void
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
