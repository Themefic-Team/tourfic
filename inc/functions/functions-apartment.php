<?php
# don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
use \Tourfic\Classes\Apartment\Availability as Apt_Availability;

/**
 * Flushing Rewrite on Tourfic Activation
 *
 * tf_apartment post type
 * apartment_feature taxonomy
 */
//function tf_apartment_rewrite_flush() {
//
//	register_tf_apartment_post_type();
//	tf_apartment_taxonomies_register();
//	flush_rewrite_rules();
//
//}
//
//register_activation_hook( TF_PATH . 'tourfic.php', 'tf_apartment_rewrite_flush' );

/**
 * WooCommerce hotel Functions
 *
 * @include
 */
if ( Helper::tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' ) ) {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-apartment.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' );
	}
}


/**
 * Filter apartments on search result page by checkin checkout dates set by backend
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of hotels exists
 * @param array $data user input for sidebar form
 *
 * @author Foysal
 *
 */

/**
 * Filter apartments on search result page without checkin checkout dates
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of hotels exists
 * @param array $data user input for sidebar form
 *
 * @author Foysal
 *
 */

/**
 * Apartment booked days
 * @author Foysal
 */

/**
 * Get Apartment Locations
 *
 * {taxonomy-apartment_location}
 * @author Foysal
 */

/**
 * Apartment host rating
 *
 * @param $author_id
 *
 * @author Foysal
 */

/**
 * Apartment room quick view
 * @author Foysal

/**
 * Assign taxonomy(apartment_feature) from the single post metabox
 * to a Tour when updated or published
 * @return array();
 * @author Foysal
 * @since 2.9.23
 */