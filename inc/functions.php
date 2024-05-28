<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

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

	if ( Helper::tf_is_woo_active() ) {
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
 * Order page Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_order.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions_order.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions_order.php' );
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
if ( Helper::tf_is_woo_active() ) {
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
if ( Helper::tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/shortcodes.php' ) ) {
		require_once TF_INC_PATH . 'functions/shortcodes.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/shortcodes.php' );
	}
}

# Google Fonts
if ( file_exists( TF_INC_PATH . 'functions/functions-fonts.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-fonts.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-fonts.php' );
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