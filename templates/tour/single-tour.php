<?php
/**
 * Template: Signle Tour (Full width)
 */
// Get header

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Tour\Tour_Price;
use \Tourfic\App\Wishlist;

get_header();

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
	$post_id = get_the_ID();

	// Get Tour Meta
	$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
	/**
	 * Show/hide sections
	 */
	$disable_review_sec   = ! empty( $meta['t-review'] ) ? $meta['t-review'] : '';
	$disable_related_tour = ! empty( $meta['t-related'] ) ? $meta['t-related'] : '';
	$disable_wishlist_tour = ! empty( $meta['t-wishlist'] ) ? $meta['t-wishlist'] : 0;

	/**
	 * Get global settings value
	 */
	$s_review  = ! empty( Helper::tfopt( 't-review' ) ) ? Helper::tfopt( 't-review' ) : '';
	$s_related = ! empty( Helper::tfopt( 't-related' ) ) ?Helper::tfopt( 't-related' ) : '';

	/**
	 * Disable Review Section
	 */
	$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

	/**
	 * Disable Related Tour
	 */
	$disable_related_tour = ! empty( $disable_related_tour ) ? $disable_related_tour : $s_related;


	// Get destination
	$destinations           = get_the_terms( $post_id, 'tour_destination' );
	$first_destination_slug = ! empty( $destinations ) ? $destinations[0]->slug : '';

	// Wishlist
	$post_type       = substr( get_post_type(), 3, - 1 );
	$has_in_wishlist = Wishlist::tf_has_item_in_wishlist( $post_id );

	// tour type meta
	$tour_type = ! empty( $meta['type'] ) ? $meta['type'] : '';
	// Repeated Fixed Tour meta
	if(!empty($tour_type) && ($tour_type == 'fixed')) {
		$tf_start_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
		$tf_repeated_fixed_tour_switch = ! empty( $meta['fixed_availability']["tf-repeat-months-switch"] ) ? $meta['fixed_availability']["tf-repeat-months-switch"] : 0;
		$tf_tour_repeat_months = ($tf_repeated_fixed_tour_switch == 1) && !empty($meta['fixed_availability']['tf-repeat-months-checkbox']) ? $meta['fixed_availability']['tf-repeat-months-checkbox'] : array();
	}

	// date format for users
	$tf_tour_date_format_for_users  = !empty(Helper::tfopt( "tf-date-format-for-users")) ? Helper::tfopt( "tf-date-format-for-users") : "Y/m/d";


	if(!function_exists('tf_fixed_tour_start_date_changer')) {
		function tf_fixed_tour_start_date_changer($date, $months) {
			if( (count($months) > 0) && !empty($date)) {
				preg_match('/(\d{4})\/(\d{2})\/(\d{2})/', $date, $matches);

				$new_months[] = $matches[0];
				
				foreach($months as $month) {

					if($month < gmdate('m')) {
						$year = $matches[1] + 1;

					} else $year = $matches[1];

					$day_selected = gmdate('d', strtotime($date));
					$last_day_of_month = gmdate('t', strtotime(gmdate('Y').'-'.$month.'-01'));
					$matches[2] = $month;
					$changed_date = sprintf("%s/%s/%s", $year, $matches[2], $matches[3]);

					if(($day_selected == "31") && ($last_day_of_month != "31")) {
						$new_months[] = gmdate('Y/m/d', strtotime($changed_date . ' -1 day'));
					} else {
						$new_months[] = $changed_date;
					}
				}
				return $new_months;

			} else return array();
		}
	}

	//Social Share
	$share_text = get_the_title();
	$share_link = get_permalink( $post_id );
	$disable_share_opt  = ! empty( $meta['t-share'] ) ? $meta['t-share'] : '';
	$t_share  = ! empty( Helper::tfopt( 't-share' ) ) ? Helper::tfopt( 't-share' ) : 0;
	$disable_share_opt = ! empty( $disable_share_opt ) ? $disable_share_opt : $t_share;
	
	// Location
	if( !empty($meta['location']) && Helper::tf_data_types($meta['location'])){
		$location = !empty( Helper::tf_data_types($meta['location'])['address'] ) ? Helper::tf_data_types($meta['location'])['address'] : '';

		$location_latitude = !empty( Helper::tf_data_types($meta['location'])['latitude'] ) ? Helper::tf_data_types($meta['location'])['latitude'] : '';
		$location_longitude = !empty( Helper::tf_data_types($meta['location'])['longitude'] ) ? Helper::tf_data_types($meta['location'])['longitude'] : '';
		$location_zoom = !empty( Helper::tf_data_types($meta['location'])['zoom'] ) ? Helper::tf_data_types($meta['location'])['zoom'] : '';

    }
	// Gallery
	$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : array();
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery );
	}
	$hero_title = ! empty( $meta['hero_title'] ) ? $meta['hero_title'] : '';

	// Map Type
	$tf_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";
	$tf_google_map_key = !empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';

	// Highlights
	$highlights = ! empty( $meta['additional_information'] ) ? $meta['additional_information'] : '';
	// Informations
	$tour_duration = ! empty( $meta['duration'] ) ? $meta['duration'] : '';
	$tour_refund_policy = ! empty( $meta['refund_des'] ) ? $meta['refund_des'] : '';
	$info_tour_type = ! empty( $meta['tour_types'] ) ? $meta['tour_types'] : [];
	$duration_time = ! empty( $meta['duration_time'] ) ? $meta['duration_time'] : 'Day';
	$night         = ! empty( $meta['night'] ) ? $meta['night'] : false;
	$night_count   = ! empty( $meta['night_count'] ) ? $meta['night_count'] : '';
	$group_size    = ! empty( $meta['group_size'] ) ? $meta['group_size'] : '';
	$language      = ! empty( $meta['language'] ) ? $meta['language'] : '';
	$email         = ! empty( $meta['email'] ) ? $meta['email'] : '';
	$phone         = ! empty( $meta['phone'] ) ? $meta['phone'] : '';
	$fax           = ! empty( $meta['fax'] ) ? $meta['fax'] : '';
	$website       = ! empty( $meta['website'] ) ? $meta['website'] : '';
	$itinerary_map = ! empty( Helper::tfopt('itinerary_map') ) && function_exists('is_tf_pro') && is_tf_pro() ? Helper::tfopt('itinerary_map') : 0;
	$vendor_contact_info = !empty(Helper::tfopt("multi-vendor-setings")["vendor-contact-info"]) ? Helper::tfopt("multi-vendor-setings")["vendor-contact-info"] : 0;
	$author = !empty(get_userdata( get_post()->post_author )) ? get_userdata( get_post()->post_author) : array();

	if ((is_plugin_active("tourfic-vendor/tourfic-vendor.php"))) {

		if($vendor_contact_info == 1) {
			if ( in_array( 'tf_vendor', $author->roles ) ) {
				$email = !empty(Helper::tfopt("multi-vendor-setings")["email"]) ? Helper::tfopt("multi-vendor-setings")["email"] : "";
				$phone = !empty(Helper::tfopt("multi-vendor-setings")["phone"]) ? Helper::tfopt("multi-vendor-setings")["phone"] : "";
				$fax = !empty(Helper::tfopt("multi-vendor-setings")["fax"]) ? Helper::tfopt("multi-vendor-setings")["fax"] : "";
				$website = !empty(Helper::tfopt("multi-vendor-setings")["website"]) ? Helper::tfopt("multi-vendor-setings")["website"] : "";
			}
		}
	}

	/**
	 * Get features
	 * hotel_feature
	 */
	$features = ! empty( get_the_terms( $post_id, 'tour_features' ) ) ? get_the_terms( $post_id, 'tour_features' ) : '';

	$min_days = ! empty( $meta['min_days'] ) ? $meta['min_days'] : '';

	$faqs            = !empty($meta['faqs']) ? $meta['faqs'] : null;
	if( !empty($faqs) && gettype($faqs)=="string" ){
        $tf_hotel_faqs_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $faqs );
        $faqs = unserialize( $tf_hotel_faqs_value );
    }
	$inc             = !empty($meta['inc']) ? $meta['inc'] : null;
	if( !empty($inc) && gettype($inc)=="string" ){
        $tf_hotel_inc_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $inc );
        $inc = unserialize( $tf_hotel_inc_value );
    }
	$exc             = !empty($meta['exc']) ? $meta['exc'] : null;
	if( !empty($exc) && gettype($exc)=="string" ){
        $tf_hotel_exc_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $exc );
        $exc = unserialize( $tf_hotel_exc_value );
	}

	$inc_icon        = ! empty( $meta['inc_icon'] ) ? $meta['inc_icon'] : null;
	$exc_icon        = ! empty( $meta['exc_icon'] ) ? $meta['exc_icon'] : null;
	$custom_inc_icon = ! empty( $inc_icon ) ? "custom-inc-icon" : '';
	$custom_exc_icon = ! empty( $exc_icon ) ? "custom-exc-icon" : '';
	$itineraries     = !empty($meta['itinerary']) ? $meta['itinerary'] : null;
	if( !empty($itineraries) && gettype($itineraries)=="string" ){
        $tf_hotel_itineraries_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $itineraries );
        $itineraries = unserialize( $tf_hotel_itineraries_value );
    }

	$terms_and_conditions = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';
	$tf_faqs              = ( get_post_meta( $post->ID, 'tf_faqs', true ) ) ? get_post_meta( $post->ID, 'tf_faqs', true ) : array();

	/**
	 * Review query
	 */
	$args           = array(
		'post_id' => $post_id,
		'status'  => 'approve',
		'type'    => 'comment',
	);
	$comments_query = new WP_Comment_Query( $args );
	$comments       = $comments_query->comments;

	/**
	 * Pricing
	 */
	$pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
	$tour_type    = ! empty( $meta['type'] ) ? $meta['type'] : '';
	if ( $tour_type && $tour_type == 'continuous' ) {
		$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
	}
	$discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : 'none';
	$disable_adult  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
	$disable_child  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
	$disable_infant = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
	if ( $tour_type == 'continuous' && $custom_avail == true ) {
		$pricing_rule = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : 'person';
	}

	# Get Pricing
	$tour_price = new Tour_Price( $meta );

	// Single Template
	$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
	if("single"==$tf_tour_layout_conditions){
		$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
	}
	$tf_tour_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour'] : 'design-1';
	$tf_tour_selected_check = !empty($tf_tour_single_template) ? $tf_tour_single_template : $tf_tour_global_template;

	$tf_tour_selected_template = $tf_tour_selected_check;

	if( $tf_tour_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'tour/design-1.php';
	}elseif( $tf_tour_selected_template == "design-2" ){
		include TF_TEMPLATE_PART_PATH . 'tour/design-2.php';
	}else{
		include TF_TEMPLATE_PART_PATH . 'tour/design-default.php';
	}
	?>
<?php
endwhile;
?>
<?php
get_footer();
