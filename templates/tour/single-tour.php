<?php
/**
 * Template: Signle Tour (Full width)
 */
// Get header
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Tour\Tour_Price;
use \Tourfic\App\Wishlist;

if(tourfic_is_block_theme()){
    wp_head();
    tourfic_render_block_header_area();
}else{
    get_header();
}

if ( !Helper::tf_is_woo_active() ) {
	?>
	<div class="tf-container">
		<div class="tf-notice tf-notice-danger">
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to view tour details.', 'tourfic' ); ?>
		</div>
	</div>
	<?php
	get_footer();
	return;
}

// Main query
while ( have_posts() ) : the_post();

	// get post id
	$tourfic_post_id = get_the_ID();

	// Get Tour Meta
	$tourfic_meta = get_post_meta( $tourfic_post_id, 'tf_tours_opt', true );
	
	/**
	 * Show/hide sections
	 */
	$tourfic_disable_review_sec   = ! empty( $tourfic_meta['t-review'] ) ? $tourfic_meta['t-review'] : '';
	$tourfic_disable_related_tour = ! empty( $tourfic_meta['t-related'] ) ? $tourfic_meta['t-related'] : '';
	$tourfic_disable_wishlist_tour = ! empty( $tourfic_meta['t-wishlist'] ) ? $tourfic_meta['t-wishlist'] : 0;

	/**
	 * Get global settings value
	 */
	$tourfic_s_review  = ! empty( Helper::tfopt( 't-review' ) ) ? Helper::tfopt( 't-review' ) : '';
	$tourfic_s_related = ! empty( Helper::tfopt( 't-related' ) ) ?Helper::tfopt( 't-related' ) : '';

	/**
	 * Disable Review Section
	 */
	$tourfic_disable_review_sec = ! empty( $tourfic_disable_review_sec ) ? $tourfic_disable_review_sec : $tourfic_s_review;

	/**
	 * Disable Related Tour
	 */
	$tourfic_disable_related_tour = ! empty( $tourfic_disable_related_tour ) ? $tourfic_disable_related_tour : $tourfic_s_related;


	// Get destination
	$tourfic_destinations           = get_the_terms( $tourfic_post_id, 'tour_destination' );
	$tourfic_first_destination_slug = ! empty( $tourfic_destinations ) ? $tourfic_destinations[0]->slug : '';

	// Wishlist
	$tourfic_post_type       = substr( get_post_type(), 3, - 1 );
	$tourfic_has_in_wishlist = Wishlist::tf_has_item_in_wishlist( $tourfic_post_id );

	// date format for users
	$tourfic_tour_date_format_for_users  = !empty(Helper::tfopt( "tf-date-format-for-users")) ? Helper::tfopt( "tf-date-format-for-users") : "Y/m/d";

	//Social Share
	$tourfic_share_text = get_the_title();
	$tourfic_share_link = get_permalink( $tourfic_post_id );
	$tourfic_disable_share_opt  = ! empty( $tourfic_meta['t-share'] ) ? $tourfic_meta['t-share'] : '';
	$tourfic_t_share  = ! empty( Helper::tfopt( 't-share' ) ) ? Helper::tfopt( 't-share' ) : 0;
	$tourfic_disable_share_opt = ! empty( $tourfic_disable_share_opt ) ? $tourfic_disable_share_opt : $tourfic_t_share;
	$tourfic_tour_single_book_now_text = isset($tourfic_meta['single_tour_booking_form_button_text']) && ! empty( $tourfic_meta['single_tour_booking_form_button_text'] ) ? stripslashes( sanitize_text_field( $tourfic_meta['single_tour_booking_form_button_text'] ) ) : esc_html__( "Book Now", 'tourfic' );
	
	// Location
	if( !empty($tourfic_meta['location']) && Helper::tf_data_types($tourfic_meta['location'])){
		$tourfic_location = !empty( Helper::tf_data_types($tourfic_meta['location'])['address'] ) ? Helper::tf_data_types($tourfic_meta['location'])['address'] : '';

		$tourfic_location_latitude = !empty( Helper::tf_data_types($tourfic_meta['location'])['latitude'] ) ? Helper::tf_data_types($tourfic_meta['location'])['latitude'] : '';
		$tourfic_location_longitude = !empty( Helper::tf_data_types($tourfic_meta['location'])['longitude'] ) ? Helper::tf_data_types($tourfic_meta['location'])['longitude'] : '';
		$tourfic_location_zoom = !empty( Helper::tf_data_types($tourfic_meta['location'])['zoom'] ) ? Helper::tf_data_types($tourfic_meta['location'])['zoom'] : '';

    }
	// Gallery
	$tourfic_gallery = ! empty( $tourfic_meta['tour_gallery'] ) ? $tourfic_meta['tour_gallery'] : array();
	if ( $tourfic_gallery ) {
		$tourfic_gallery_ids = explode( ',', $tourfic_gallery );
	}
	$tourfic_hero_title = ! empty( $tourfic_meta['hero_title'] ) ? $tourfic_meta['hero_title'] : '';

	// Map Type
	$tourfic_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";
	$tourfic_google_map_key = !empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';

	// Highlights
	$tourfic_highlights = ! empty( $tourfic_meta['additional_information'] ) ? $tourfic_meta['additional_information'] : '';
	// Informations
	$tourfic_tour_duration = ! empty( $tourfic_meta['duration'] ) ? $tourfic_meta['duration'] : '';
	$tourfic_tour_refund_policy = ! empty( $tourfic_meta['refund_des'] ) ? $tourfic_meta['refund_des'] : '';
	$tourfic_info_tour_type = ! empty( $tourfic_meta['tour_types'] ) ? $tourfic_meta['tour_types'] : [];
	$tourfic_duration_time = ! empty( $tourfic_meta['duration_time'] ) ? $tourfic_meta['duration_time'] : 'Day';
	$tourfic_night         = ! empty( $tourfic_meta['night'] ) ? $tourfic_meta['night'] : false;
	$tourfic_night_count   = ! empty( $tourfic_meta['night_count'] ) ? $tourfic_meta['night_count'] : '';
	$tourfic_group_size    = ! empty( $tourfic_meta['group_size'] ) ? $tourfic_meta['group_size'] : '';
	$tourfic_language      = ! empty( $tourfic_meta['language'] ) ? $tourfic_meta['language'] : '';
	$tourfic_email         = ! empty( $tourfic_meta['email'] ) ? $tourfic_meta['email'] : '';
	$tourfic_phone         = ! empty( $tourfic_meta['phone'] ) ? $tourfic_meta['phone'] : '';
	$tourfic_fax           = ! empty( $tourfic_meta['fax'] ) ? $tourfic_meta['fax'] : '';
	$tourfic_website       = ! empty( $tourfic_meta['website'] ) ? $tourfic_meta['website'] : '';
	$tourfic_itinerary_map = ! empty( Helper::tfopt( 'itinerary_map' ) ) ? Helper::tfopt( 'itinerary_map' ) : 0;
	$tourfic_vendor_contact_info = !empty(Helper::tfopt("multi-vendor-setings")["vendor-contact-info"]) ? Helper::tfopt("multi-vendor-setings")["vendor-contact-info"] : 0;
	$tourfic_author = !empty(get_userdata( get_post()->post_author )) ? get_userdata( get_post()->post_author) : array();

	if ((is_plugin_active("tourfic-vendor/tourfic-vendor.php"))) {

		if($tourfic_vendor_contact_info == 1) {
			if ( in_array( 'tf_vendor', $tourfic_author->roles ) ) {
				$tourfic_email = !empty(Helper::tfopt("multi-vendor-setings")["email"]) ? Helper::tfopt("multi-vendor-setings")["email"] : "";
				$tourfic_phone = !empty(Helper::tfopt("multi-vendor-setings")["phone"]) ? Helper::tfopt("multi-vendor-setings")["phone"] : "";
				$tourfic_fax = !empty(Helper::tfopt("multi-vendor-setings")["fax"]) ? Helper::tfopt("multi-vendor-setings")["fax"] : "";
				$tourfic_website = !empty(Helper::tfopt("multi-vendor-setings")["website"]) ? Helper::tfopt("multi-vendor-setings")["website"] : "";
			}
		}
	}

	/**
	 * Get features
	 * hotel_feature
	 */
	$tourfic_features = ! empty( get_the_terms( $tourfic_post_id, 'tour_features' ) ) ? get_the_terms( $tourfic_post_id, 'tour_features' ) : '';

	$tourfic_min_days = ! empty( $tourfic_meta['min_days'] ) ? $tourfic_meta['min_days'] : '';

	$tourfic_faqs            = !empty($tourfic_meta['faqs']) ? $tourfic_meta['faqs'] : null;
	if( !empty($tourfic_faqs) && gettype($tourfic_faqs)=="string" ){
        $tourfic_hotel_faqs_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $tourfic_faqs );
        $tourfic_faqs = unserialize( $tourfic_hotel_faqs_value );
    }
	$tourfic_inc             = !empty($tourfic_meta['inc']) ? $tourfic_meta['inc'] : null;
	if( !empty($tourfic_inc) && gettype($tourfic_inc)=="string" ){
        $tourfic_hotel_inc_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $tourfic_inc );
        $tourfic_inc = unserialize( $tourfic_hotel_inc_value );
    }
	$tourfic_exc             = !empty($tourfic_meta['exc']) ? $tourfic_meta['exc'] : null;
	if( !empty($tourfic_exc) && gettype($tourfic_exc)=="string" ){
        $tourfic_hotel_exc_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $tourfic_exc );
        $tourfic_exc = unserialize( $tourfic_hotel_exc_value );
	}

	$tourfic_inc_icon        = ! empty( $tourfic_meta['inc_icon'] ) ? $tourfic_meta['inc_icon'] : null;
	$tourfic_exc_icon        = ! empty( $tourfic_meta['exc_icon'] ) ? $tourfic_meta['exc_icon'] : null;
	$tourfic_custom_inc_icon = ! empty( $tourfic_inc_icon ) ? "custom-inc-icon" : '';
	$tourfic_custom_exc_icon = ! empty( $tourfic_exc_icon ) ? "custom-exc-icon" : '';
	$tourfic_itineraries     = !empty($tourfic_meta['itinerary']) ? $tourfic_meta['itinerary'] : null;
	if( !empty($tourfic_itineraries) && gettype($tourfic_itineraries)=="string" ){
        $tourfic_hotel_itineraries_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $tourfic_itineraries );
        $tourfic_itineraries = unserialize( $tourfic_hotel_itineraries_value );
    }

	$tourfic_terms_and_conditions = ! empty( $tourfic_meta['terms_conditions'] ) ? $tourfic_meta['terms_conditions'] : '';
	$tourfic_faqs              = ( get_post_meta( $post->ID, 'tf_faqs', true ) ) ? get_post_meta( $post->ID, 'tf_faqs', true ) : array();

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
	 * Pricing
	 */
	$tourfic_pricing_rule = ! empty( $tourfic_meta['pricing'] ) ? $tourfic_meta['pricing'] : '';
	$tourfic_discount_type  = ! empty( $tourfic_meta['discount_type'] ) ? $tourfic_meta['discount_type'] : 'none';
	$tourfic_disable_adult  = ! empty( $tourfic_meta['disable_adult_price'] ) ? $tourfic_meta['disable_adult_price'] : false;
	$tourfic_disable_child  = ! empty( $tourfic_meta['disable_child_price'] ) ? $tourfic_meta['disable_child_price'] : false;
	$tourfic_disable_infant = ! empty( $tourfic_meta['disable_infant_price'] ) ? $tourfic_meta['disable_infant_price'] : false;

	# Get Pricing
	$tourfic_group_price    = ! empty( $tourfic_meta['group_price'] ) ? $tourfic_meta['group_price'] : 0;
	$tourfic_adult_price    = ! empty( $tourfic_meta['adult_price'] ) ? $tourfic_meta['adult_price'] : 0;
	$tourfic_children_price = ! empty( $tourfic_meta['child_price'] ) ? $tourfic_meta['child_price'] : 0;
	$tourfic_infant_price   = ! empty( $tourfic_meta['infant_price'] ) ? $tourfic_meta['infant_price'] : 0;
	$tourfic_tour_price = new Tour_Price( $tourfic_meta );

	// Single Template
	$tourfic_tour_layout_conditions = ! empty( $tourfic_meta['tf_single_tour_layout_opt'] ) ? $tourfic_meta['tf_single_tour_layout_opt'] : 'global';
	if("single"==$tourfic_tour_layout_conditions){
		$tourfic_tour_single_template = ! empty( $tourfic_meta['tf_single_tour_template'] ) ? $tourfic_meta['tf_single_tour_template'] : 'design-1';
	}
	$tourfic_tour_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour'] : 'design-1';
	$tourfic_tour_selected_check = !empty($tourfic_tour_single_template) ? $tourfic_tour_single_template : $tourfic_tour_global_template;

	$tourfic_tour_selected_template = $tourfic_tour_selected_check;

	$tourfic_tour_duration_icon = ! empty( $tourfic_meta['tf-tour-duration-icon'] ) ? $tourfic_meta['tf-tour-duration-icon'] : 'ri-history-line';    
	$tourfic_tour_type_icon = ! empty( $tourfic_meta['tf-tour-type-icon'] ) ? $tourfic_meta['tf-tour-type-icon'] : 'ri-menu-unfold-line';    
	$tourfic_tour_group_icon = ! empty( $tourfic_meta['tf-tour-group-icon'] ) ? $tourfic_meta['tf-tour-group-icon'] : 'ri-team-line';    
	$tourfic_tour_lang_icon = ! empty( $tourfic_meta['tf-tour-lang-icon'] ) ? $tourfic_meta['tf-tour-lang-icon'] : 'ri-global-line';

	if( $tourfic_tour_selected_template == "design-1" ){
		include TOURFIC_TEMPLATE_PART_PATH . 'tour/design-1.php';
	}elseif( $tourfic_tour_selected_template == "design-2" ){
		include TOURFIC_TEMPLATE_PART_PATH . 'tour/design-2.php';
	}else{
		include TOURFIC_TEMPLATE_PART_PATH . 'tour/design-legacy.php';
	}
	?>
<?php
endwhile;
if(tourfic_is_block_theme()){
    wp_footer();
    tourfic_render_block_footer_area();
 }else{
	get_footer();
}
