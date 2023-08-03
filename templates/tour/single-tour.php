<?php
/**
 * Template: Signle Tour (Full width)
 */
// Get header
get_header();

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

	/**
	 * Get global settings value
	 */
	$s_review  = ! empty( tfopt( 't-review' ) ) ? tfopt( 't-review' ) : '';
	$s_related = ! empty( tfopt( 't-related' ) ) ? tfopt( 't-related' ) : '';

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
	$has_in_wishlist = tf_has_item_in_wishlist( $post_id );

	// Address
	$location = isset( $meta['text_location'] ) ? $meta['text_location'] : '';

	//Social Share

	$share_text = get_the_title();
	$share_link = get_permalink( $post_id );
	$disable_share_opt  = ! empty( $meta['t-share'] ) ? $meta['t-share'] : '';
	$t_share  = ! empty( tfopt( 't-share' ) ) ? tfopt( 't-share' ) : 0;
	$disable_share_opt = ! empty( $disable_share_opt ) ? $disable_share_opt : $t_share;
	
	if( !empty($meta['location']) && tf_data_types($meta['location'])){
		$location = !empty( tf_data_types($meta['location'])['address'] ) ? tf_data_types($meta['location'])['address'] : $location;

		$location_latitude = !empty( tf_data_types($meta['location'])['latitude'] ) ? tf_data_types($meta['location'])['latitude'] : '';
		$location_longitude = !empty( tf_data_types($meta['location'])['longitude'] ) ? tf_data_types($meta['location'])['longitude'] : '';
		$location_zoom = !empty( tf_data_types($meta['location'])['zoom'] ) ? tf_data_types($meta['location'])['zoom'] : '';

    }
	// Gallery
	$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : array();
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery );
	}
	$hero_title = ! empty( $meta['hero_title'] ) ? $meta['hero_title'] : '';

	// Map Type
	$tf_openstreet_map = ! empty( tfopt( 'google-page-option' ) ) ? tfopt( 'google-page-option' ) : "default";
	$tf_google_map_key = !empty( tfopt( 'tf-googlemapapi' ) ) ? tfopt( 'tf-googlemapapi' ) : '';

	// Highlights
	$highlights = ! empty( $meta['additional_information'] ) ? $meta['additional_information'] : '';
	// Informations
	$tour_duration = ! empty( $meta['duration'] ) ? $meta['duration'] : '';
	$tour_refund_policy = ! empty( $meta['refund_des'] ) ? $meta['refund_des'] : '';
	$info_tour_type = ! empty( $meta['tour_types'] ) ? $meta['tour_types'] : 'Continues';
	$duration_time = ! empty( $meta['duration_time'] ) ? $meta['duration_time'] : '';
	$night         = ! empty( $meta['night'] ) ? $meta['night'] : false;
	$night_count   = ! empty( $meta['night_count'] ) ? $meta['night_count'] : '';
	$group_size    = ! empty( $meta['group_size'] ) ? $meta['group_size'] : '';
	$language      = ! empty( $meta['language'] ) ? $meta['language'] : '';
	$email         = ! empty( $meta['email'] ) ? $meta['email'] : '';
	$phone         = ! empty( $meta['phone'] ) ? $meta['phone'] : '';
	$fax           = ! empty( $meta['fax'] ) ? $meta['fax'] : '';
	$website       = ! empty( $meta['website'] ) ? $meta['website'] : '';
	$itinerary_map = ! empty( tfopt('itinerary_map') ) && function_exists('is_tf_pro') && is_tf_pro() ? tfopt('itinerary_map') : 0;

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
	//continuous tour
	$share_text = get_the_title();
	$share_link = esc_url( home_url( "/?p=" ) . $post_id );

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
	$tf_tour_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-tour'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-tour'] : 'design-1';
	$tf_tour_selected_check = !empty($tf_tour_single_template) ? $tf_tour_single_template : $tf_tour_global_template;

	$tf_plugin_installed = get_option('tourfic_template_installed'); 
	if (!empty($tf_plugin_installed)) {
	    $tf_tour_selected_template = $tf_tour_selected_check;
	}else{
		if("single"==$tf_tour_layout_conditions){
			$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'default';
		}
		$tf_tour_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-tour'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-tour'] : 'default';
		$tf_tour_selected_check = !empty($tf_tour_single_template) ? $tf_tour_single_template : $tf_tour_global_template;
		
	    $tf_tour_selected_template = $tf_tour_selected_check ? $tf_tour_selected_check : 'default';
	}

	if( $tf_tour_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'tour/design-1.php';
	}else{
		include TF_TEMPLATE_PART_PATH . 'tour/design-default.php';
	}
	?>
<div class="tf-withoutpayment-booking">
	<div class="tf-withoutpayment-popup">
		<div class="tf-booking-tabs">
			<div class="tf-booking-tab-menu">
				<ul>
					<li class="tf-booking-step tf-booking-step-1 active">
						<i class="ri-price-tag-3-line"></i> <?php echo __("Tour extra","tourfic"); ?>
					</li>
					<li class="tf-booking-step tf-booking-step-2">
						<i class="ri-group-line"></i> <?php echo __("Traveler details","tourfic"); ?>
					</li>
				</ul>
			</div>
			<div class="tf-booking-times">
				<span>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
				<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
				<path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
				<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
				</svg>
				</span>
			</div>
		</div>
		<div class="tf-booking-content-summery">
			<div class="tf-booking-content show tf-booking-content-1">
				<div class="tf-booking-content-extra">
					<p><?php echo __("Here we include our tour extra services. If you want take any of the service. Start and end in Edinburgh! With the In-depth Cultural","tourfic"); ?></p>
					<div class="tf-single-tour-extra">
						<input type="checkbox">
						<label for="">
							<h5>Premium service <span>$200</span></h5>
							<p>Breakfast, pool, home service included</p>
						</label>
					</div>
					<div class="tf-single-tour-extra">
						<input type="checkbox">
						<label for="">
							<h5>Premium service <span>$200</span></h5>
							<p>Breakfast, pool, home service included</p>
						</label>
					</div>
					<div class="tf-single-tour-extra">
						<input type="checkbox">
						<label for="">
							<h5>Premium service <span>$200</span></h5>
							<p>Breakfast, pool, home service included</p>
						</label>
					</div>
					<div class="tf-single-tour-extra">
						<input type="checkbox">
						<label for="">
							<h5>Premium service <span>$200</span></h5>
							<p>Breakfast, pool, home service included</p>
						</label>
					</div>
					<div class="tf-single-tour-extra">
						<input type="checkbox">
						<label for="">
							<h5>Premium service <span>$200</span></h5>
							<p>Breakfast, pool, home service included</p>
						</label>
					</div>
				</div>
				<div class="tf-control-pagination">
					<a href="#" class="tf-next-control tf-tabs-control" data-step="2"><?php echo __("Continue", "tourfic"); ?></a>
				</div>
			</div>
			<div class="tf-booking-content tf-booking-content-2">
				<div class="tf-booking-content-traveller">
					<p><?php echo __("All of your information will be confidential and the reason of this is for your privacy purpose","tourfic"); ?></p>
					<div class="tf-single-tour-traveller">
						<h4><?php echo __("Traveler 01","tourfic"); ?></h4>
						<div class="traveller-info">
							<div class="traveller-single-info">
								<label for=""><?php echo __("Full Name","tourfic"); ?></label>
								<input type="text">
							</div>
							<div class="traveller-single-info">
								<label for=""><?php echo __("Date of birth","tourfic"); ?></label>
								<input type="text">
							</div>
							<div class="traveller-single-info">
								<label for=""><?php echo __("NID","tourfic"); ?></label>
								<input type="text">
							</div>
						</div>
					</div>
					
					<div class="tf-single-tour-traveller">
						<h4><?php echo __("Traveler 02","tourfic"); ?></h4>
						<div class="traveller-info">
							<div class="traveller-single-info">
								<label for=""><?php echo __("Full Name","tourfic"); ?></label>
								<input type="text">
							</div>
							<div class="traveller-single-info">
								<label for=""><?php echo __("Date of birth","tourfic"); ?></label>
								<input type="text">
							</div>
							<div class="traveller-single-info">
								<label for=""><?php echo __("NID","tourfic"); ?></label>
								<input type="text">
							</div>
						</div>
					</div>
				</div>
				<div class="tf-diposit-switcher">
					<label class="switch">
						<input type="checkbox" class="diposit-status-switcher" value="49" checked="">
						<span class="switcher round"></span>
					</label>
					<h4><?php echo __("Partial payment of 25% on total","tourfic"); ?></h4>
				</div>
				<div class="tf-control-pagination">
					<a href="#" class="tf-back-control tf-step-back" data-step="1"><i class="fa fa-angle-left"></i><?php echo __("Back", "tourfic"); ?></a>
					<a href="#" class="tf-next-control tf-tabs-control" data-step="2"><?php echo __("Continue", "tourfic"); ?></a>
				</div>
			</div>
			<div class="tf-booking-summery">
				<div class="tf-booking-fixed-summery">
					<h5><?php echo __("Booking summery","tourfic"); ?></h5>
					<h4><?php echo __("Ecstatic Shimla 4 Night 5 Day Tour Package","tourfic"); ?></h4>
				</div>
				<div class="tf-booking-traveller-info">
					<h6><?php echo __("On 23 august 2023","tourfic"); ?></h6>
					<table class="table">
						<thead>
							<tr>
								<th align="left"><?php echo __("Traveller","tourfic"); ?></th>
								<th align="right"><?php echo __("Price","tourfic"); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td align="left"><?php echo __("2 adults ($200/person)","tourfic"); ?></td>
								<td align="right"><?php echo __("$400","tourfic"); ?></td>
							</tr>
							<tr>
								<td align="left"><?php echo __("2 adults ($200/person)","tourfic"); ?></td>
								<td align="right"><?php echo __("$400","tourfic"); ?></td>
							</tr>
							<tr>
								<td align="left"><?php echo __("2 adults ($200/person)","tourfic"); ?></td>
								<td align="right"><?php echo __("$400","tourfic"); ?></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th align="left"><?php echo __("Total","tourfic"); ?></th>
								<th align="right"><?php echo __("$1200","tourfic"); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
endwhile;
?>
<?php
get_footer();
