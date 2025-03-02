<?php
/**
 * Plugin Name:     Tourfic - Travel and Hotel Booking Solution for WooCommerce
 * Plugin URI:      https://tourfic.com
 * Description:     The ultimate WordPress tour management plugin for hotel booking, tour operator and travel agency websites. Manage all your online Travel Booking system along with order system and any payment of WooCommerce.
 * Author:          Themefic
 * Author URI:      https://themefic.com
 * Text Domain:     tourfic
 * Domain Path:     /lang/
 * Version:         90.8.3
 * Tested up to: 6.0.1
 * WC tested up to: 6.8.0
 * Requires PHP: 7.2
 * Elementor tested up to: 3.7.2
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
define( 'TF_ASSETS_URL', TF_URL.'assets/' );
// Paths
define( 'TF_PATH', plugin_dir_path( __FILE__ ) );
define( 'TF_ADMIN_PATH', TF_PATH.'admin/' );
define( 'TF_INC_PATH', TF_PATH.'inc/' );
define( 'TF_TEMPLATE_PATH', TF_PATH.'templates/' );
define( 'TF_TEMPLATE_PART_PATH', TF_TEMPLATE_PATH.'template-parts/' );
define( 'TF_OPTIONS_PATH', TF_ADMIN_PATH.'options/' );
define( 'TF_ASSETS_PATH', TF_PATH.'assets/' );

/**
 * Enqueue Main Admin scripts
 * 
 * @since 1.0
 */
if ( !function_exists('tf_enqueue_main_admin_scripts') ) {
    function tf_enqueue_main_admin_scripts(){

        // Custom
        wp_enqueue_style('tf', TF_ADMIN_URL . 'assets/css/admin.css','', '2.1.0' );
        wp_enqueue_script( 'tf', TF_ADMIN_URL . 'assets/js/admin.js', array('jquery'), '2.1.0', true );   
        wp_localize_script( 'tf', 'tf_params',
            array(
                'tf_nonce' => wp_create_nonce( 'updates' ),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            )
        );    
    }
    add_action( 'admin_enqueue_scripts', 'tf_enqueue_main_admin_scripts' );
}

/**
 * Check if WooCommerce is active, and if it isn't, disable the plugin.
 *
 * @since 1.0
 */
if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	add_action( 'admin_notices', 'tf_is_woo' );

	/**
     * Ajax install & activate WooCommerce
     *
     * @since 1.0
     * @link https://developer.wordpress.org/reference/functions/wp_ajax_install_plugin/
     */
    add_action("wp_ajax_tf_ajax_install_plugin" , "wp_ajax_install_plugin");

	return;
}

/**
 * Tourfic Define
 *
 * @since 1.0
 */
if ( !defined( 'TOURFIC' ) ) {
    define( 'TOURFIC', '90.8.3' );
}

// Styles & Scripts
if (!defined( 'INSTANTIO_PRO_SCRIPT' )) {
	require_once TF_INC_PATH . '/style-script.php';
}

/**
 * Show admin warning if a required file is missing
 */
function tf_file_missing( $files = '' ) {

    if(is_admin()) {
        if(!empty($files)) {
            $class = 'notice notice-error';
            $message = '<strong>' .$files. '</strong>' .__(' file is missing! It is required to function Tourfic properly!', 'tourfic');
        
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
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
add_action( 'init', 'tf_load_textdomain' );

/**
 * Plugins Loaded Actions
 * 
 * Including Option Framework
 * 
 * Including Options
 */
if ( ! function_exists( 'tf_plugin_loaded_action' ) ) {
	function tf_plugin_loaded_action() {
		
        /**
         * Option Framework & options
         *
         * @since 1.0
         */
		// Options Framework
        if( !class_exists( 'CSF' ) ) {
		    require_once( TF_ADMIN_PATH .'framework/framework.php' );
        }
        // Options
        if( class_exists( 'CSF' ) ) {
            if ( file_exists( TF_OPTIONS_PATH . 'options.php' ) ) {
                require_once TF_OPTIONS_PATH . 'options.php';
            } else {
                tf_file_missing(TF_OPTIONS_PATH . 'options.php');
            }          
        }
		
	}
}
add_action( 'plugins_loaded', 'tf_plugin_loaded_action' );

/**
 * Global Admin Get Option
 */
if ( !function_exists( 'tfopt' ) ) {
    function tfopt( $option = '', $default = null ) {
        $options = get_option( 'tourfic_opt' );
        return ( isset( $options[$option] ) ) ? $options[$option] : $default;
    }
}

/**
 * All the requires
 */
// Classes
if ( file_exists( TF_INC_PATH . 'classes.php' ) ) {
    require_once TF_INC_PATH . 'classes.php';
} else {
    tf_file_missing(TF_INC_PATH . 'classes.php');
}

// Functions
if ( file_exists( TF_INC_PATH . 'functions.php' ) ) {
    require_once TF_INC_PATH . 'functions.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions.php');
}

// Admin Functions
if ( file_exists( TF_ADMIN_PATH . 'inc/functions.php' ) ) {
    require_once TF_ADMIN_PATH . 'inc/functions.php';
} else {
    tf_file_missing(TF_ADMIN_PATH . 'inc/functions.php');
}


/**
 * Called when WooCommerce is inactive to display an inactive notice.
 *
 * @since 1.0
 */
function tf_is_woo() {
    if ( current_user_can( 'activate_plugins' ) ) {
        if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) && !file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
        ?>

            <div id="message" class="error">
                <p><?php printf( __( 'Tourfic requires %1$s WooCommerce %2$s to be activated.', 'tourfic' ), '<strong><a href="https://wordpress.org/plugins/woocommerce/" target="_blank">', '</a></strong>' ); ?></p>
                <p><a class="install-now button" href="<?php echo esc_url( admin_url('/plugin-install.php?s=slug:woocommerce&tab=search&type=term') ); ?>"><?php esc_attr_e( 'Install Now', 'tourfic' ); ?></a></p>
            </div>

        <?php 
        } elseif ( !is_plugin_active( 'woocommerce/woocommerce.php' ) && file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
        ?>

            <div id="message" class="error">
                <p><?php printf( __( 'Tourfic requires %1$s WooCommerce %2$s to be activated.', 'tourfic' ), '<strong><a href="https://wordpress.org/plugins/woocommerce/" target="_blank">', '</a></strong>' ); ?></p>
                <p><a href="<?php echo get_admin_url(); ?>plugins.php?_wpnonce=<?php echo wp_create_nonce( 'activate-plugin_woocommerce/woocommerce.php' ); ?>&action=activate&plugin=woocommerce/woocommerce.php" class="button activate-now button-primary"><?php esc_attr_e( 'Activate', 'tourfic' ); ?></a></p>
            </div>

        <?php 
        } elseif ( version_compare( get_option( 'woocommerce_db_version' ), '2.5', '<' ) ) {
        ?>

            <div id="message" class="error">
                <p><?php printf( __( '%sTourfic is inactive.%s This plugin requires WooCommerce 2.5 or newer. Please %supdate WooCommerce to version 2.5 or newer%s', 'tourfic' ), '<strong>', '</strong>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
            </div>

        <?php 
        }
    }
}