<?php
/**
 * Plugin Name:     Tourfic - Travel, Hotel, and Apartment Booking Solution for WooCommerce
 * Plugin URI:      https://themefic.com/tourfic
 * Description:     The Ultimate WordPress plugin for tour, travel, accommodation, and hotel bookings. Effortlessly manage your entire online travel booking system, including orders and any WooCommerce payment method.
 * Author:          Themefic
 * Author URI:      https://themefic.com
 * Text Domain:     tourfic
 * Domain Path:     /lang/
 * Version:         2.12.1
 * Tested up to:    6.5
 * WC tested up to: 8.8
 * Requires PHP:    7.4
 * Elementor tested up to: 3.21
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

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! defined( 'TOURFIC' ) ) {
	define( 'TOURFIC', get_plugin_data( __FILE__ )["Version"] );
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
 * WooCommerce is required to be installed and activated
 * @since 1.0
 */
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	add_action( 'admin_notices', 'tf_is_woo' );

	/**
	 * Ajax install & activate WooCommerce
	 *
	 * @since 1.0
	 * @link https://developer.wordpress.org/reference/functions/wp_ajax_install_plugin/
	 */
	add_action( "wp_ajax_tf_ajax_install_plugin", "wp_ajax_install_plugin" );
}

/**
 * Called when WooCommerce is inactive to display an inactive notice.
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
                    var ajax_url= '<?php echo esc_url( admin_url( 'admin-ajax.php' ) )?>';

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
				<p><a href="<?php echo esc_url( get_admin_url() ); ?>plugins.php?_wpnonce=<?php echo esc_attr( wp_create_nonce( 'activate-plugin_woocommerce/woocommerce.php' ) ); ?>&action=activate&plugin=woocommerce/woocommerce.php"
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
// Functions
if ( file_exists( TF_INC_PATH . 'functions.php' ) ) {
	require_once TF_INC_PATH . 'functions.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions.php' );
}
// Classes
if ( file_exists( TF_INC_PATH . 'classes.php' ) ) {
	require_once TF_INC_PATH . 'classes.php';
} else {
	tf_file_missing( TF_INC_PATH . 'classes.php' );
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
