<?php
/**
 * Template: Single Car (Full Width)
 */

 // Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\Wishlist;

get_header();

if ( !Helper::tf_is_woo_active() ) {
	?>
	<div class="tf-container">
		<div class="tf-notice tf-notice-danger">
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to view car details.', 'tourfic' ); ?>
		</div>
	</div>
	<?php
	get_footer();
	return;
}

/**
 * Query start
 */
while ( have_posts() ) : the_post();

	// get post id
	$tourfic_post_id = $post->ID;

	/**
	 * Review query
	 */
	$tourfic_args           = array(
		'post_id' => $tourfic_post_id,
		'status'  => 'approve',
		'type'    => 'comment',
	);
	$tourfic_comments_query = new WP_Comment_Query( $tourfic_args );
	$tourfic_comments       = $tourfic_comments_query->comments;

	/**
	 * Get car meta values
	 */
	$tourfic_meta = get_post_meta( $tourfic_post_id, 'tf_carrental_opt', true );
	$tourfic_meta = function_exists( 'tf_normalize_car_meta' ) ? tf_normalize_car_meta( $tourfic_meta ) : $tourfic_meta;

	$tourfic_disable_share_opt    = ! empty( $tourfic_meta['c-share'] ) ? $tourfic_meta['c-share'] : '';
	$tourfic_disable_wishlist_sec = ! empty( $tourfic_meta['c-wishlist'] ) ? $tourfic_meta['c-wishlist'] : 0;

	/**
	 * Get global settings value
	 */
	$tourfic_s_share  = ! empty( Helper::tfopt( 'disable-car-share' ) ) ? Helper::tfopt( 'disable-car-share' ) : 0;

	/**
	 * Disable Share Option
	 */
	$tourfic_disable_share_opt = ! empty( $tourfic_disable_share_opt ) ? $tourfic_disable_share_opt : $tourfic_s_share;


	/**
	 * Assign all values to variables
	 *
	 */

	// Wishlist
	$tourfic_post_type       = str_replace( 'tf_', '', get_post_type() );
	$tourfic_has_in_wishlist = Wishlist::tf_has_item_in_wishlist( $tourfic_post_id );

	/**
	 * Get locations
	 *
	 * carrental_location
	 */
	$tourfic_locations = ! empty( get_the_terms( $tourfic_post_id, 'carrental_location' ) ) ? get_the_terms( $tourfic_post_id, 'carrental_location' ) : '';
	if ( $tourfic_locations ) {
		$tourfic_first_location_id   = $tourfic_locations[0]->term_id;
		$tourfic_first_location_term = get_term( $tourfic_first_location_id );
		$tourfic_first_location_name = $tourfic_locations[0]->name;
		$tourfic_first_location_slug = $tourfic_locations[0]->slug;
		$tourfic_first_location_url  = get_term_link( $tourfic_first_location_term );
	}

	/**
	 * Get features
	 * hotel_feature
	 */
	$tourfic_features = ! empty( get_the_terms( $tourfic_post_id, 'hotel_feature' ) ) ? get_the_terms( $tourfic_post_id, 'hotel_feature' ) : '';

	// Location
	$tourfic_location_title = ! empty( $tourfic_meta['location_title'] ) ? $tourfic_meta['location_title'] : '';
	if( !empty($tourfic_meta['map']) && Helper::tf_data_types($tourfic_meta['map'])){
		$tourfic_address = !empty( Helper::tf_data_types($tourfic_meta['map'])['address'] ) ? Helper::tf_data_types($tourfic_meta['map'])['address'] : '';

		$tourfic_address_latitude = !empty( Helper::tf_data_types($tourfic_meta['map'])['latitude'] ) ? Helper::tf_data_types($tourfic_meta['map'])['latitude'] : '';
		$tourfic_address_longitude = !empty( Helper::tf_data_types($tourfic_meta['map'])['longitude'] ) ? Helper::tf_data_types($tourfic_meta['map'])['longitude'] : '';
		$tourfic_address_zoom = !empty( Helper::tf_data_types($tourfic_meta['map'])['zoom'] ) ? Helper::tf_data_types($tourfic_meta['map'])['zoom'] : '';

    }

	// Car Detail
	$tourfic_gallery = ! empty( $tourfic_meta['car_gallery'] ) ? $tourfic_meta['car_gallery'] : '';
	if ( $tourfic_gallery ) {
		$tourfic_gallery_ids = explode( ',', $tourfic_gallery ); // Comma seperated list to array
	}

	// Car Info 
	$tourfic_car_info_title = ! empty( $tourfic_meta['car_info_sec_title'] ) ? $tourfic_meta['car_info_sec_title'] : '';
	$tourfic_passengers = ! empty( $tourfic_meta['passengers'] ) ? $tourfic_meta['passengers'] : '';
	$tourfic_baggage = ! empty( $tourfic_meta['baggage'] ) ? $tourfic_meta['baggage'] : '';
	$tourfic_car_custom_info = ! empty( $tourfic_meta['car_custom_info'] ) ? $tourfic_meta['car_custom_info'] : '';
	$tourfic_unlimited_mileage = ! empty( $tourfic_meta['unlimited_mileage'] ) ? $tourfic_meta['unlimited_mileage'] : 0;
	$tourfic_mileage_type = ! empty( $tourfic_meta['mileage_type'] ) ? $tourfic_meta['mileage_type'] : 'Km';
	$tourfic_total_mileage = ! empty( $tourfic_meta['mileage'] ) ? $tourfic_meta['mileage'] : '';
	$tourfic_auto_transmission = ! empty( $tourfic_meta['auto_transmission'] ) ? $tourfic_meta['auto_transmission'] : '';
	$tourfic_carplay_android_auto = ! empty( $tourfic_meta['carplay_android_auto'] ) ? $tourfic_meta['carplay_android_auto'] : '';
	$tourfic_fuel_included = ! empty( $tourfic_meta['fuel_included'] ) ? $tourfic_meta['fuel_included'] : '';
	$tourfic_shuttle_car = ! empty( $tourfic_meta['shuttle_car'] ) ? $tourfic_meta['shuttle_car'] : '';
	$tourfic_shuttle_car_fee_type = ! empty( $tourfic_meta['shuttle_car_fee_type'] ) ? $tourfic_meta['shuttle_car_fee_type'] : 'free';
	$tourfic_shuttle_car_fee = ! empty( $tourfic_meta['shuttle_car_fee'] ) ? $tourfic_meta['shuttle_car_fee'] : '';

	// Fuel Type
	$tourfic_fuel_type_terms = wp_get_post_terms($tourfic_post_id, 'carrental_fuel_type');
	$tourfic_fuel_types = '';
	if (!is_wp_error($tourfic_fuel_type_terms) && !empty($tourfic_fuel_type_terms)) {
		foreach ($tourfic_fuel_type_terms as $tourfic_term) {
			$tourfic_fuel_types = $tourfic_term->name;
		}
	}
	// Engine Year
	$tourfic_engine_year_terms = wp_get_post_terms($tourfic_post_id, 'carrental_engine_year');
	$tourfic_engine_years = '';
	if (!is_wp_error($tourfic_engine_year_terms) && !empty($tourfic_engine_year_terms)) {
		foreach ($tourfic_engine_year_terms as $tourfic_term) {
			$tourfic_engine_years = $tourfic_term->name;
		}
	}

	// Benefits 
	$tourfic_benefits_status = ! empty( $tourfic_meta['benefits_section'] ) ? $tourfic_meta['benefits_section'] : '';
	$tourfic_benefits = ! empty( $tourfic_meta['benefits'] ) ? $tourfic_meta['benefits'] : '';

	// Include Exclude 
	$tourfic_inc_exc_status = ! empty( $tourfic_meta['inc_exc_section'] ) ? $tourfic_meta['inc_exc_section'] : '';
	$tourfic_includes = ! empty( $tourfic_meta['inc'] ) ? $tourfic_meta['inc'] : '';
	$tourfic_include_icon = ! empty( $tourfic_meta['inc_icon'] ) ? $tourfic_meta['inc_icon'] : '';
	$tourfic_excludes = ! empty( $tourfic_meta['exc'] ) ? $tourfic_meta['exc'] : '';
	$tourfic_exclude_icon = ! empty( $tourfic_meta['exc_icon'] ) ? $tourfic_meta['exc_icon'] : '';
	$tourfic_inc_sec_title = ! empty( $tourfic_meta['inc_sec_title'] ) ? $tourfic_meta['inc_sec_title'] : '';
	$tourfic_exc_sec_title = ! empty( $tourfic_meta['exc_sec_title'] ) ? $tourfic_meta['exc_sec_title'] : '';

	// Driver Info 
	$tourfic_driver_sec_title = ! empty( $tourfic_meta['driver_sec_title'] ) ? $tourfic_meta['driver_sec_title'] : '';
	$tourfic_car_driver_incude = ! empty( $tourfic_meta['driver_included'] ) ? $tourfic_meta['driver_included'] : '';
	$tourfic_car_driverinfo_status = ! empty( $tourfic_meta['car_driverinfo_section'] ) ? $tourfic_meta['car_driverinfo_section'] : '';
	$tourfic_driver_name = ! empty( $tourfic_meta['driver_name'] ) ? $tourfic_meta['driver_name'] : '';
	$tourfic_driver_email = ! empty( $tourfic_meta['driver_email'] ) ? $tourfic_meta['driver_email'] : '';
	$tourfic_driver_phone = ! empty( $tourfic_meta['driver_phone'] ) ? $tourfic_meta['driver_phone'] : '';
	$tourfic_driver_age = ! empty( $tourfic_meta['driver_age'] ) ? $tourfic_meta['driver_age'] : '';
	$tourfic_driver_address = ! empty( $tourfic_meta['driver_address'] ) ? $tourfic_meta['driver_address'] : '';
	$tourfic_driver_image = ! empty( $tourfic_meta['driver_image'] ) ? $tourfic_meta['driver_image'] : '';

	// Booking
	$tourfic_car_booking_by = ! empty( $tourfic_meta['booking-by'] ) ? $tourfic_meta['booking-by'] : '1';
	
	// Protection
	$tourfic_benefits_sec_title = ! empty( $tourfic_meta['benefits_sec_title'] ) ? $tourfic_meta['benefits_sec_title'] : '';
	$tourfic_car_protection_section_status = ! empty( $tourfic_meta['protection_section'] ) ? $tourfic_meta['protection_section'] : '';
	$tourfic_car_protection_content = ! empty( $tourfic_meta['protection_content'] ) ? $tourfic_meta['protection_content'] : '';
	$tourfic_car_protections = ! empty( $tourfic_meta['protections'] ) ? $tourfic_meta['protections'] : '';

	//instructions
	$tourfic_car_instructions_section_status = ! empty( $tourfic_meta['instructions_section'] ) ? $tourfic_meta['instructions_section'] : '';
	$tourfic_car_instructions_content = ! empty( $tourfic_meta['instructions_content'] ) ? $tourfic_meta['instructions_content'] : '';

	// Information
	$tourfic_car_information_section_status = ! empty( $tourfic_meta['information_section'] ) ? $tourfic_meta['information_section'] : '';
	$tourfic_car_owner_name = ! empty( $tourfic_meta['owner_name'] ) ? $tourfic_meta['owner_name'] : '';
	$tourfic_car_owner_email = ! empty( $tourfic_meta['email'] ) ? $tourfic_meta['email'] : '';
	$tourfic_car_owner_phone = ! empty( $tourfic_meta['phone'] ) ? $tourfic_meta['phone'] : '';
	$tourfic_car_owner_website = ! empty( $tourfic_meta['website'] ) ? $tourfic_meta['website'] : '';
	$tourfic_car_owner_fax = ! empty( $tourfic_meta['fax'] ) ? $tourfic_meta['fax'] : '';
	$tourfic_car_owner_owner_image = ! empty( $tourfic_meta['owner_image'] ) ? $tourfic_meta['owner_image'] : '';
	$tourfic_owner_sec_title  = ! empty( $tourfic_meta['owner_sec_title'] ) ? $tourfic_meta['owner_sec_title'] : '';

	// Car Extras
	$tourfic_car_extra_sec_title  = apply_filters( 'tf_car_extra_sec_title', '', $tourfic_post_id, $tourfic_meta );
	$tourfic_car_extras = apply_filters( 'tf_car_extra_meta', null, $tourfic_post_id, $tourfic_meta );

	// Car Deposit
	$tourfic_car_allow_deposit = apply_filters( 'tf_allow_deposit_feature', false, $tourfic_meta );
	$tourfic_car_deposit_type = ! empty( $tourfic_meta['deposit_type'] ) ? $tourfic_meta['deposit_type'] : 'none';
	$tourfic_car_deposit_amount = ! empty( $tourfic_meta['deposit_amount'] ) ? $tourfic_meta['deposit_amount'] : '';

	// FAQ
	$tourfic_faqs = ! empty( $tourfic_meta['faq'] ) ? $tourfic_meta['faq'] : '';
	$tourfic_faq_sec_title  = ! empty( $tourfic_meta['faq_sec_title'] ) ? $tourfic_meta['faq_sec_title'] : '';

	// Terms & condition
	$tourfic_tc_title = ! empty( $tourfic_meta['car-tc-section-title'] ) ? $tourfic_meta['car-tc-section-title'] : '';
	$tourfic_tc = ! empty( $tourfic_meta['terms_conditions'] ) ? $tourfic_meta['terms_conditions'] : '';

	$tourfic_share_text = get_the_title();
	$tourfic_share_link = get_permalink( $tourfic_post_id );
	$tourfic_review_sec_title  = ! empty( $tourfic_meta['review_sec_title'] ) ? $tourfic_meta['review_sec_title'] : '';
	// Map Type
	$tourfic_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";

	// Single Template Style
	$tourfic_car_layout_conditions = ! empty( $meta['tf_single_car_layout_opt'] ) ? $meta['tf_single_car_layout_opt'] : 'global';
	if("single"==$tourfic_car_layout_conditions){
		$tourfic_car_single_template = ! empty( $meta['tf_single_car_template'] ) ? $meta['tf_single_car_template'] : 'design-1';
	}
	$tourfic_car_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-car'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-car'] : 'design-1';

	$tourfic_car_selected_check = !empty($tourfic_car_single_template) ? $tourfic_car_single_template : $tourfic_car_global_template;

	$tourfic_car_selected_template = $tourfic_car_selected_check;

    if( $tourfic_car_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'car/design-1.php';
	}else{
		include TF_TEMPLATE_PART_PATH . 'car/design-1.php';
	}
endwhile;
get_footer();
