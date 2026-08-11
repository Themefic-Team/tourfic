<?php
/**
 * Template: Single Apartment (Full Width)
 */
// Don't load directly
defined( 'ABSPATH' ) || exit;

 if(tf_is_block_theme()){
    wp_head();
    tf_render_block_header_area();
}else{
    get_header();
}

use \Tourfic\Classes\Helper;
use \Tourfic\App\Wishlist;

if ( !Helper::tf_is_woo_active() ) {
	?>
	<div class="tf-container">
		<div class="tf-notice tf-notice-danger">
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to view apartment details.', 'tourfic' ); ?>
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
	 * Get apartment meta values
	 */
	$tourfic_meta = get_post_meta( $tourfic_post_id, 'tf_apartment_opt', true );

	$tourfic_disable_share_opt   = ! empty( $tourfic_meta['disable-apartment-share'] ) ? $tourfic_meta['disable-apartment-share'] : '';
	$tourfic_disable_review_sec  = ! empty( $tourfic_meta['disable-apartment-review'] ) ? $tourfic_meta['disable-apartment-review'] : '';
	$tourfic_disable_related_sec = ! empty( $tourfic_meta['disable-related-apartment'] ) ? $tourfic_meta['disable-related-apartment'] : '';

	/**
	 * Get global settings value
	 */
	$tourfic_s_share   = ! empty( Helper::tfopt( 'disable-apartment-share' ) ) ? Helper::tfopt( 'disable-apartment-share' ) : 0;
	$tourfic_s_review  = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
	$tourfic_s_related = ! empty( Helper::tfopt( 'disable-related-apartment' ) ) ? Helper::tfopt( 'disable-related-apartment' ) : 0;

	/**
	 * Disable Share and Review section
	 */
	$tourfic_disable_share_opt   = ! empty( $tourfic_disable_share_opt ) ? $tourfic_disable_share_opt : $tourfic_s_share;
	$tourfic_disable_review_sec  = ! empty( $tourfic_disable_review_sec ) ? $tourfic_disable_review_sec : $tourfic_s_review;
	$tourfic_disable_related_sec = ! empty( $tourfic_disable_related_sec ) ? $tourfic_disable_related_sec : $tourfic_s_related;

	// Wishlist
	$tourfic_post_type       = str_replace( 'tf_', '', get_post_type() );
	$tourfic_has_in_wishlist = Wishlist::tf_has_item_in_wishlist( $tourfic_post_id );

	/**
	 * Get locations
	 * apartment_location
	 */
	$tourfic_locations = ! empty( get_the_terms( $tourfic_post_id, 'apartment_location' ) ) ? get_the_terms( $tourfic_post_id, 'apartment_location' ) : array();
	if ( $tourfic_locations ) {
		$tourfic_first_location_id   = $tourfic_locations[0]->term_id;
		$tourfic_first_location_term = get_term( $tourfic_first_location_id );
		$tourfic_first_location_name = $tourfic_locations[0]->name;
		$tourfic_first_location_slug = $tourfic_locations[0]->slug;
		$tourfic_first_location_url  = get_term_link( $tourfic_first_location_term );
	}

	// Location
	$tourfic_map     = ! empty( $tourfic_meta['map'] ) ? $tourfic_meta['map'] : '';
	if ( ! empty( $tourfic_map ) && gettype( $tourfic_map ) == "string" ) {
		$tourfic_apartment_map_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $tourfic_map );
		$tourfic_map                    = unserialize( $tourfic_apartment_map_value );
        $tourfic_address = ! empty($tourfic_map['address'] ) ? $tourfic_map['address'] : '';
	}else{
		$tourfic_address = ! empty($tourfic_map['address'] ) ? $tourfic_map['address'] : '';
	}

	// Map Type
	$tourfic_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";
	$tourfic_google_map_key = !empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';

	// Apartment Gallery
	$tourfic_gallery = ! empty( $tourfic_meta['apartment_gallery'] ) ? $tourfic_meta['apartment_gallery'] : '';
	if ( $tourfic_gallery ) {
		$tourfic_gallery_ids = explode( ',', $tourfic_gallery ); // Comma seperated list to array
	}
	$tourfic_video = ! empty( $tourfic_meta['video'] ) ? $tourfic_meta['video'] : '';

	$tourfic_share_text = get_the_title();
	$tourfic_share_link = get_permalink( $tourfic_post_id );

    // Single Template Style
	$tourfic_apartment_layout_conditions = ! empty( $tourfic_meta['tf_single_apartment_layout_opt'] ) ? $tourfic_meta['tf_single_apartment_layout_opt'] : 'global';
	if("single"==$tourfic_apartment_layout_conditions){
		$tourfic_apartment_single_template = ! empty( $tourfic_meta['tf_single_apartment_template'] ) ? $tourfic_meta['tf_single_apartment_template'] : 'default';
	}
	$tourfic_apartment_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] : 'default';

	$tourfic_apartment_selected_check = !empty($tourfic_apartment_single_template) ? $tourfic_apartment_single_template : $tourfic_apartment_global_template;

	$tourfic_apartment_selected_template = $tourfic_apartment_selected_check;
    
    if( $tourfic_apartment_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'apartment/design-1.php';
	}else{
		include TF_TEMPLATE_PART_PATH . 'apartment/design-legacy.php';
	}

endwhile;

if(tf_is_block_theme()){
    wp_footer();
    tf_render_block_footer_area();
 }else{
	get_footer();
 }