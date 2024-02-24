<?php
/**
 * Plugin Name:     Tourfic - Travel and Hotel Booking Solution for WooCommerce
 * Plugin URI:      https://themefic.com/tourfic
 * Description:     The ultimate WordPress travel booking Plugin for hotel booking, travel booking and travel agency websites. Manage all your online Travel Booking system along with order system and any payment of WooCommerce.
 * Author:          Themefic
 * Author URI:      https://themefic.com
 * Text Domain:     tourfic
 * Domain Path:     /lang/
 * Version:         2.11.10
 * Tested up to:    6.4.3
 * WC tested up to: 8.6
 * Requires PHP:    7.2
 * Elementor tested up to: 3.19.0
 */

// don't load directly
defined( 'ABSPATH' ) || exit;
final class Tourfic {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '2.11.10';

	/**
	 * Minimum PHP version required.
	 *
	 * @var string
	 */
	const MINIMUM_PHP_VERSION = '7.2';

	/**
	 * Minimum WooCommerce version required.
	 *
	 * @var string
	 */
	const MINIMUM_WC_VERSION = '3.0';

	/**
	 * The single instance of the class.
	 *
	 * @var Tourfic
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Tourfic Instance.
	 *
	 * Ensures only one instance of Tourfic is loaded or can be loaded.
	 *
	 * @return Tourfic - Main instance.
	 * @see Tourfic()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Tourfic Constructor.
	 */
	public function __construct() {
		$this->define_constants();

		$this->init_hooks();
		$this->includes();
	}

	/**
	 * Define Tourfic Constants.
	 */
	private function define_constants() {
		define( 'TF_VERSION', self::VERSION );
		define( 'TF_MINIMUM_PHP_VERSION', self::MINIMUM_PHP_VERSION );
		define( 'TF_MINIMUM_WC_VERSION', self::MINIMUM_WC_VERSION );
		define( 'TF_URL', plugin_dir_url( __FILE__ ) );
		define( 'TF_TEMPLATES_URL', TF_URL . 'templates/' );
		define( 'TF_ADMIN_URL', TF_URL . 'admin/' );
		define( 'TF_ASSETS_URL', TF_URL . 'assets/' );
		define( 'TF_APP_ASSETS_URL', TF_ASSETS_URL . 'app/' );
		define( 'TF_ADMIN_ASSETS_URL', TF_ASSETS_URL . 'admin/' );
		define( 'TF_PATH', plugin_dir_path( __FILE__ ) );
		define( 'TF_ADMIN_PATH', TF_PATH . 'admin/' );
		define( 'TF_INC_PATH', TF_PATH . 'inc/' );
		define( 'TF_TEMPLATE_PATH', TF_PATH . 'templates/' );
		define( 'TF_TEMPLATE_PART_PATH', TF_TEMPLATE_PATH . 'template-parts/' );
		define( 'TF_OPTIONS_PATH', TF_ADMIN_PATH . 'options/' );
		define( 'TF_ASSETS_PATH', TF_PATH . 'assets/' );
		define( 'TF_EMAIL_TEMPLATES_PATH', TF_PATH . 'admin/emails/templates/' );
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		// plugin loaded action hook.
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		// Load the text domain for translation.
		add_action( 'plugins_loaded', array( $this, 'tf_load_textdomain' ) );
	}

	/**
	 * Init the plugin after plugins_loaded so environment variables are set.
	 */
	public function init_plugin() {
		// autoloader
		require_once TF_PATH . 'autoloader.php';

		\TOURFIC\Classes\Core::instance();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		if ( ! class_exists( 'Appsero\Client' ) ) {
			require_once( TF_INC_PATH . 'app/src/Client.php' );
		}
		if ( ! defined( 'TOURFIC_PRO_SCRIPT' ) ) {
			require_once TF_INC_PATH . 'style-script.php';
		}
	}

	function tf_load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'tourfic' );
		// Allow upgrade safe, site specific language files in /wp-content/languages/tourfic/
		load_textdomain( 'tourfic', WP_LANG_DIR . '/tourfic/tourfic-' . $locale . '.mo' );
		// Then check for a language file in /wp-content/plugins/tourfic/lang/ (this will be overriden by any file already loaded)
		load_plugin_textdomain( 'tourfic', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
}
Tourfic::instance();