<?php
# don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Car_Rental\Pricing;

/**
 * WooCommerce Car Functions
 *
 * @include
 */
if ( Helper::tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-car.php' ) ) {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-car.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-car.php' );
	}
}