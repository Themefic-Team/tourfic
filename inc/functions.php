<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

if ( ! function_exists( 'tf_is_woo_active' ) ) {
	function tf_is_woo_active() {
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}
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
 * WC Product Extend
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' ) ) {
	function fida() {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php';
	}

	if ( tf_is_woo_active() ) {
		add_action( 'init', 'fida' );
	}
} else {
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' );
}

/**
 * Helper Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-helper.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-helper.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-helper.php' );
}

/**
 * Hotel Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-hotel.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-hotel.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-hotel.php' );
}

/**
 * Apartment Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-apartment.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-apartment.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-apartment.php' );
}

/**
 * Tour Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-tour.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-tour.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-tour.php' );
}

/**
 * WooCommerce Common Functions
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-common.php' ) ) {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-common.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-common.php' );
	}
}

/**
 * Wishlist Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-wishlist.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-wishlist.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-wishlist.php' );
}

/**
 * Review Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-review.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-review.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-review.php' );
}

/**
 * inquiry Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_enquiry.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions_enquiry.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions_enquiry.php' );
}

/**
 * Include export import function file
 */
if( file_exists( TF_INC_PATH . 'functions/functions-settings-import-export.php' ) ){
	require_once TF_INC_PATH . 'functions/functions-settings-import-export.php';
}else{
	tf_file_missing( TF_INC_PATH . 'functions/functions-settings-import-export.php' );
}

/**
 * Include Post Duplicator function file
 */
if( file_exists( TF_INC_PATH . 'functions/functions_duplicator.php' ) ){
	require_once TF_INC_PATH . 'functions/functions_duplicator.php';
}else{
	tf_file_missing( TF_INC_PATH . 'functions/functions_duplicator.php' );
}

/**
 * Include Functions Vat
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_vat.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions_vat.php';
} else {
    tf_file_missing( TF_INC_PATH . 'functions/functions_vat.php' );
}

/**
 * Shortcodes
 *
 * @since 1.0
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/shortcodes.php' ) ) {
		require_once TF_INC_PATH . 'functions/shortcodes.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/shortcodes.php' );
	}
}

/**
 * Widgets
 *
 * @since 1.0
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/widgets.php' ) ) {
		require_once TF_INC_PATH . 'functions/widgets.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/widgets.php' );
	}
}

# Google Fonts
if ( file_exists( TF_INC_PATH . 'functions/functions-fonts.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-fonts.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-fonts.php' );
}

/**
 * Elementor Widgets
 *
 */
function tf_add_elelmentor_addon() {

	// Check if Elementor installed and activated
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}
	// Once we get here, We have passed all validation checks so we can safely include our plugin
	if ( file_exists( TF_INC_PATH . 'elementor/widget-register.php' ) ) {
		require_once TF_INC_PATH . 'elementor/widget-register.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'elementor/widget-register.php' );
	}

}

add_action( 'plugins_loaded', 'tf_add_elelmentor_addon' );

/**
 * Notice
 *
 * Update
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-notice_update.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-notice_update.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-notice_update.php' );
}