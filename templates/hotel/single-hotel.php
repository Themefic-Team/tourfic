<?php
/**
 * Template: Single Hotel (Full Width)
 */
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\Wishlist;
use \Tourfic\Classes\Hotel\Hotel;

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
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to view hotel details.', 'tourfic' ); ?>
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
	 * Get hotel meta values
	 */
	$tourfic_meta = Hotel::get_normalized_hotel_meta( $tourfic_post_id );

	$tourfic_disable_share_opt    = ! empty( $tourfic_meta['h-share'] ) ? $tourfic_meta['h-share'] : '';
	$tourfic_disable_review_sec   = ! empty( $tourfic_meta['h-review'] ) ? $tourfic_meta['h-review'] : '';
	$tourfic_disable_wishlist_sec = ! empty( $tourfic_meta['h-wishlist'] ) ? $tourfic_meta['h-wishlist'] : 0;

	/**
	 * Get global settings value
	 */
	$tourfic_s_share  = ! empty( Helper::tfopt( 'h-share' ) ) ? Helper::tfopt( 'h-share' ) : 0;
	$tourfic_s_review = ! empty( Helper::tfopt( 'h-review' ) ) ? Helper::tfopt( 'h-review' ) : 0;

	/**
	 * Disable Share Option
	 */
	$tourfic_disable_share_opt = ! empty( $tourfic_disable_share_opt ) ? $tourfic_disable_share_opt : $tourfic_s_share;

	/**
	 * Disable Review Section
	 */
	$tourfic_disable_review_sec = ! empty( $tourfic_disable_review_sec ) ? $tourfic_disable_review_sec : $tourfic_s_review;

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
	 * hotel_location
	 */
	$tourfic_locations = ! empty( get_the_terms( $tourfic_post_id, 'hotel_location' ) ) ? get_the_terms( $tourfic_post_id, 'hotel_location' ) : '';
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
	$tourfic_address           = '';
	$tourfic_address_latitude  = '';
	$tourfic_address_longitude = '';
	$tourfic_address_zoom      = '';

	$tourfic_map = Hotel::get_hotel_map_data( $tourfic_meta );
	if ( ! empty( $tourfic_map ) ) {
		$tourfic_address           = ! empty( $tourfic_map['address'] ) ? $tourfic_map['address'] : '';
		$tourfic_address_latitude  = ! empty( $tourfic_map['latitude'] ) ? $tourfic_map['latitude'] : '';
		$tourfic_address_longitude = ! empty( $tourfic_map['longitude'] ) ? $tourfic_map['longitude'] : '';
		$tourfic_address_zoom      = ! empty( $tourfic_map['zoom'] ) ? $tourfic_map['zoom'] : '';
	}

	// Hotel Detail
	$tourfic_gallery = ! empty( $tourfic_meta['gallery'] ) ? $tourfic_meta['gallery'] : '';
	if ( $tourfic_gallery ) {
		$tourfic_gallery_ids = explode( ',', $tourfic_gallery ); // Comma seperated list to array
	}
	$tourfic_video = ! empty( $tourfic_meta['video'] ) ? $tourfic_meta['video'] : '';
	// Room Details
	$tourfic_rooms = \Tourfic\Classes\Room\Room::get_hotel_rooms( $tourfic_post_id );

	// Hotel facilitiles
	$tourfic_hotel_facilities = ! empty( $tourfic_meta['hotel-facilities'] ) ? $tourfic_meta['hotel-facilities'] : '';
	$tourfic_hotel_facilities_categories = ! empty( Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) : '';
	$tourfic_hotel_reserve_button_text   = ! empty( Helper::tfopt( 'hotel_booking_form_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'hotel_booking_form_button_text' ) ) ) : esc_html__( "Reserve Now", 'tourfic' );

	// FAQ
	$tourfic_faqs = ! empty( $tourfic_meta['faq'] ) ? $tourfic_meta['faq'] : '';
	if ( ! empty( $tourfic_faqs ) && gettype( $tourfic_faqs ) == "string" ) {
		$tourfic_hotel_faqs_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $tourfic_faqs );
		$tourfic_faqs                = unserialize( $tourfic_hotel_faqs_value );
	}
	// Terms & condition
	$tourfic_tc = ! empty( $tourfic_meta['tc'] ) ? $tourfic_meta['tc'] : '';

	$tourfic_share_text = get_the_title();
	$tourfic_share_link = get_permalink( $tourfic_post_id );
	// Map Type
	$tourfic_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";

	// Single Template Style
	$tourfic_hotel_layout_conditions = ! empty( $tourfic_meta['tf_single_hotel_layout_opt'] ) ? $tourfic_meta['tf_single_hotel_layout_opt'] : 'global';
	if("single"==$tourfic_hotel_layout_conditions){
		$tourfic_hotel_single_template = ! empty( $tourfic_meta['tf_single_hotel_template'] ) ? $tourfic_meta['tf_single_hotel_template'] : 'design-1';
	}
	$tourfic_hotel_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel'] : 'design-1';
	$tourfic_hotel_selected_check = !empty($tourfic_hotel_single_template) ? $tourfic_hotel_single_template : $tourfic_hotel_global_template;
	$tourfic_hotel_selected_template = $tourfic_hotel_selected_check;

    if( $tourfic_hotel_selected_template == "design-1" ){
		include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1.php';
	}elseif( $tourfic_hotel_selected_template == "design-2" ){
		include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-2.php';
	}else{
		$tourfic_template = apply_filters(
			'tourfic_hotel_single_legacy_template',
			TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-legacy.php'
		);

		include $tourfic_template;
		
	}
endwhile;

if(tourfic_is_block_theme()){
    wp_footer();
    tourfic_render_block_footer_area();
 }else{
	get_footer();
 }
