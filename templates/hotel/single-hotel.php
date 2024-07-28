<?php
/**
 * Template: Single Hotel (Full Width)
 */

use \Tourfic\Classes\Helper;
use \Tourfic\App\Wishlist;

get_header();

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
	$post_id = $post->ID;

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
	 * Get hotel meta values
	 */
	$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );

	$disable_share_opt    = ! empty( $meta['h-share'] ) ? $meta['h-share'] : '';
	$disable_review_sec   = ! empty( $meta['h-review'] ) ? $meta['h-review'] : '';
	$disable_wishlist_sec = ! empty( $meta['h-wishlist'] ) ? $meta['h-wishlist'] : 0;

	/**
	 * Get global settings value
	 */
	$s_share  = ! empty( Helper::tfopt( 'h-share' ) ) ? Helper::tfopt( 'h-share' ) : 0;
	$s_review = ! empty( Helper::tfopt( 'h-review' ) ) ? Helper::tfopt( 'h-review' ) : 0;

	/**
	 * Disable Share Option
	 */
	$disable_share_opt = ! empty( $disable_share_opt ) ? $disable_share_opt : $s_share;

	/**
	 * Disable Review Section
	 */
	$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

	/**
	 * Assign all values to variables
	 *
	 */

	// Wishlist
	$post_type       = str_replace( 'tf_', '', get_post_type() );
	$has_in_wishlist = Wishlist::tf_has_item_in_wishlist( $post_id );

	/**
	 * Get locations
	 *
	 * hotel_location
	 */
	$locations = ! empty( get_the_terms( $post_id, 'hotel_location' ) ) ? get_the_terms( $post_id, 'hotel_location' ) : '';
	if ( $locations ) {
		$first_location_id   = $locations[0]->term_id;
		$first_location_term = get_term( $first_location_id );
		$first_location_name = $locations[0]->name;
		$first_location_slug = $locations[0]->slug;
		$first_location_url  = get_term_link( $first_location_term );
	}

	/**
	 * Get features
	 * hotel_feature
	 */
	$features = ! empty( get_the_terms( $post_id, 'hotel_feature' ) ) ? get_the_terms( $post_id, 'hotel_feature' ) : '';

	// Location

	if( !empty($meta['map']) && Helper::tf_data_types($meta['map'])){
		$address = !empty( Helper::tf_data_types($meta['map'])['address'] ) ? Helper::tf_data_types($meta['map'])['address'] : '';

		$address_latitude = !empty( Helper::tf_data_types($meta['map'])['latitude'] ) ? Helper::tf_data_types($meta['map'])['latitude'] : '';
		$address_longitude = !empty( Helper::tf_data_types($meta['map'])['longitude'] ) ? Helper::tf_data_types($meta['map'])['longitude'] : '';
		$address_zoom = !empty( Helper::tf_data_types($meta['map'])['zoom'] ) ? Helper::tf_data_types($meta['map'])['zoom'] : '';

    }

	// Hotel Detail
	$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
	}
	$video = ! empty( $meta['video'] ) ? $meta['video'] : '';
	// Room Details
	$rooms = \Tourfic\Classes\Room\Room::get_hotel_rooms( $post_id );

	// Hotel facilitiles
	$hotel_facilities = ! empty( $meta['hotel-facilities'] ) ? $meta['hotel-facilities'] : '';
	$hotel_facilities_categories = ! empty( Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) : '';

	// FAQ
	$faqs = ! empty( $meta['faq'] ) ? $meta['faq'] : '';
	if ( ! empty( $faqs ) && gettype( $faqs ) == "string" ) {
		$tf_hotel_faqs_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $faqs );
		$faqs                = unserialize( $tf_hotel_faqs_value );
	}
	// Terms & condition
	$tc = ! empty( $meta['tc'] ) ? $meta['tc'] : '';

	$share_text = get_the_title();
	$share_link = get_permalink( $post_id );
	// Map Type
	$tf_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";

	// Single Template Style
	$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
	if("single"==$tf_hotel_layout_conditions){
		$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
	}
	$tf_hotel_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel'] : 'design-1';

	$tf_hotel_selected_check = !empty($tf_hotel_single_template) ? $tf_hotel_single_template : $tf_hotel_global_template;

	$tf_hotel_selected_template = $tf_hotel_selected_check;

    if( $tf_hotel_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'hotel/design-1.php';
	}elseif( $tf_hotel_selected_template == "design-2" ){
		include TF_TEMPLATE_PART_PATH . 'hotel/design-2.php';
	}else{
		include TF_TEMPLATE_PART_PATH . 'hotel/design-default.php';
	}
endwhile;
get_footer();
