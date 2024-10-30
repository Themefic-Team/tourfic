<?php
/**
 * Plugin Name:     Tourfic - Travel, Hotel, and Apartment Booking Solution for WooCommerce
 * Plugin URI:      https://themefic.com/tourfic
 * Description:     The Ultimate WordPress plugin for tour, travel, accommodation, and hotel bookings. Effortlessly manage your entire online travel booking system, including orders and any WooCommerce payment method.
 * Author:          Themefic
 * Author URI:      https://themefic.com
 * Text Domain:     tourfic
 * Domain Path:     /lang/
 * Version:         2.13.14
 * Tested up to:    6.6
 * WC tested up to: 9.3
 * Requires PHP:    7.4
 * Elementor tested up to: 3.24
 */

// don't load directly
defined( 'ABSPATH' ) || exit;

final class Tourfic {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */

	const VERSION = '2.13.14';

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
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->define_constants();

		//Check if WooCommerce is active, and if it isn't, disable the plugin.
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( $this, 'tf_is_woo' ) );

			//Ajax install & activate WooCommerce
			add_action( "wp_ajax_tf_ajax_install_plugin", "wp_ajax_install_plugin" );
		}

		$this->init_hooks();
	}

	/**
	 * Define Tourfic Constants.
	 */
	private function define_constants() {
		define( 'TOURFIC', self::VERSION );
		define( 'TF_VERSION', self::VERSION );
		define( 'TF_MINIMUM_PHP_VERSION', self::MINIMUM_PHP_VERSION );
		define( 'TF_MINIMUM_WC_VERSION', self::MINIMUM_WC_VERSION );
		define( 'TF_URL', plugin_dir_url( __FILE__ ) );
		define( 'TF_TEMPLATES_URL', TF_URL . 'templates/' );
		define( 'TF_ADMIN_URL', TF_URL . 'admin/' );
		define( 'TF_ASSETS_URL', TF_URL . 'assets/' );
		define( 'TF_ASSETS_APP_URL', TF_ASSETS_URL . 'app/' );
		define( 'TF_ASSETS_ADMIN_URL', TF_ASSETS_URL . 'admin/' );
		define( 'TF_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'TF_ADMIN_PATH', TF_PATH . 'inc/Admin/' );
		define( 'TF_INC_PATH', TF_PATH . 'inc/' );
		define( 'TF_TEMPLATE_PATH', TF_PATH . 'templates/' );
		define( 'TF_TEMPLATE_PART_PATH', TF_TEMPLATE_PATH . 'template-parts/' );
		define( 'TF_OPTIONS_PATH', TF_ADMIN_PATH . 'options/' );
		define( 'TF_ASSETS_PATH', TF_PATH . 'assets/' );
		define( 'TF_EMAIL_TEMPLATES_PATH', TF_ADMIN_PATH . 'Emails/templates/' );
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		// plugin loaded action hook.
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		// Load the text domain for translation.
		add_action( 'plugins_loaded', array( $this, 'tf_load_textdomain' ) );
		//Compatibility with custom order tables for the WooCommerce plugin
		add_action( 'before_woocommerce_init', array( $this, 'tf_woocommerce_compatibility' ) );
	}

	/**
	 * Init the plugin after plugins_loaded so environment variables are set.
	 */
	public function init_plugin() {
		// autoloader
		require_once TF_PATH . 'autoloader.php';

		if ( class_exists( "\Tourfic\Classes\Base" ) ) {
			\Tourfic\Classes\Base::instance();
		} else {
			add_action( 'admin_notices', function () {
				?>
                <div class="notice notice-error">
                    <p><?php esc_html_e( 'Something went Wrong. Please Reinstall the Tourfic Plugin ', 'tourfic' ); ?></p>
                </div>
				<?php
			} );

			return;
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		// Classes
//		 if ( file_exists( TF_INC_PATH . 'classes.php' ) ) {
//		 	require_once TF_INC_PATH . 'classes.php';
//		 } else {
//		 	tf_file_missing( TF_INC_PATH . 'classes.php' );
//		 }
	}

	function tf_load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'tourfic' );
		// Allow upgrade safe, site specific language files in /wp-content/languages/tourfic/
		load_textdomain( 'tourfic', WP_LANG_DIR . '/tourfic/tourfic-' . $locale . '.mo' );
		// Then check for a language file in /wp-content/plugins/tourfic/lang/ (this will be overriden by any file already loaded)
		load_plugin_textdomain( 'tourfic', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	}

	/**
	 * Called when WooCommerce is inactive to display an inactive notice.
	 *
	 * @since 1.0
	 */
	function tf_is_woo() {
		if ( current_user_can( 'activate_plugins' ) ) {
			if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
				?>
                <div id="message" class="error">
					<?php /* translators: %1$s: WooCommerce plugin url start, %2$s: WooCommerce plugin url end */ ?>
                    <p><?php printf( esc_html__( 'Tourfic requires %1$s WooCommerce %2$s to be activated.', 'tourfic' ), '<strong><a href="https://wordpress.org/plugins/woocommerce/" target="_blank">', '</a></strong>' ); ?></p>
                    <p><a id="tf_wooinstall" class="install-now button" data-plugin-slug="woocommerce"><?php esc_html_e( 'Install Now', 'tourfic' ); ?></a></p>
                </div>

                <script>
                    jQuery(document).on('click', '#tf_wooinstall', function (e) {
                        e.preventDefault();
                        var current = jQuery(this);
                        var plugin_slug = current.attr("data-plugin-slug");
                        var ajax_url = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) )?>';

                        current.addClass('updating-message').text('Installing...');

                        var data = {
                            action: 'tf_ajax_install_plugin',
                            _ajax_nonce: '<?php echo esc_html( wp_create_nonce( 'updates' ) ); ?>',
                            slug: plugin_slug,
                        };

                        jQuery.post(ajax_url, data, function (response) {
                            current.removeClass('updating-message');
                            current.addClass('updated-message').text('Installing...');
                            current.attr("href", response.data.activateUrl);
                        })
                            .fail(function () {
                                current.removeClass('updating-message').text('Install Failed');
                            })
                            .always(function () {
                                current.removeClass('install-now updated-message').addClass('activate-now button-primary').text('Activating...');
                                current.unbind(e);
                                current[0].click();
                            });
                    });
                </script>

				<?php
			} elseif ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) && file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
				?>

                <div id="message" class="error">
					<?php /* translators: %1$s: WooCommerce plugin url start, %2$s: WooCommerce plugin url end */ ?>
                    <p><?php printf( esc_html__( 'Tourfic requires %1$s WooCommerce %2$s to be activated.', 'tourfic' ), '<strong><a href="https://wordpress.org/plugins/woocommerce/" target="_blank">', '</a></strong>' ); ?></p>
                    <p>
                        <a href="<?php echo esc_url( get_admin_url() ); ?>plugins.php?_wpnonce=<?php echo esc_attr( wp_create_nonce( 'activate-plugin_woocommerce/woocommerce.php' ) ); ?>&action=activate&plugin=woocommerce/woocommerce.php"
                           class="button activate-now button-primary"><?php esc_html_e( 'Activate', 'tourfic' ); ?></a></p>
                </div>
				<?php
			} elseif ( version_compare( get_option( 'woocommerce_db_version' ), '2.5', '<' ) ) {
				?>

                <div id="message" class="error">
					<?php /* translators: %1$s: strong tag start, %2$s: strong tag end, %3$s: plugin url start, %4$s: plugin url end */ ?>
                    <p><?php printf( esc_html__( '%1$sTourfic is inactive.%2$s This plugin requires WooCommerce 2.5 or newer. Please %3$supdate WooCommerce to version 2.5 or newer%4$s', 'tourfic' ), '<strong>', '</strong>', '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
                </div>

				<?php
			}
		}
	}

	function tf_woocommerce_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}

Tourfic::instance();

function tf_active_template_settings_callback() {
	//all code goes here if need
	update_option( 'tourfic_template_installed', true );
}

//Register activation hook
register_activation_hook( __FILE__, 'tf_active_template_settings_callback' );
