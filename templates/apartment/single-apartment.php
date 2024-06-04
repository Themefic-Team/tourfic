<?php
/**
 * Template: Single Apartment (Full Width)
 */

get_header();

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
	 * Get apartment meta values
	 */
	$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );

	$disable_share_opt   = ! empty( $meta['disable-apartment-share'] ) ? $meta['disable-apartment-share'] : '';
	$disable_review_sec  = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
	$disable_related_sec = ! empty( $meta['disable-related-apartment'] ) ? $meta['disable-related-apartment'] : '';

	/**
	 * Get global settings value
	 */
	$s_share   = ! empty( Helper::tfopt( 'disable-apartment-share' ) ) ? Helper::tfopt( 'disable-apartment-share' ) : 0;
	$s_review  = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
	$s_related = ! empty( Helper::tfopt( 'disable-related-apartment' ) ) ? Helper::tfopt( 'disable-related-apartment' ) : 0;

	/**
	 * Disable Share and Review section
	 */
	$disable_share_opt   = ! empty( $disable_share_opt ) ? $disable_share_opt : $s_share;
	$disable_review_sec  = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;
	$disable_related_sec = ! empty( $disable_related_sec ) ? $disable_related_sec : $s_related;

	// Wishlist
	$post_type       = str_replace( 'tf_', '', get_post_type() );
	$has_in_wishlist = Wishlist::tf_has_item_in_wishlist( $post_id );

	/**
	 * Get locations
	 * apartment_location
	 */
	$locations = ! empty( get_the_terms( $post_id, 'apartment_location' ) ) ? get_the_terms( $post_id, 'apartment_location' ) : array();
	if ( $locations ) {
		$first_location_id   = $locations[0]->term_id;
		$first_location_term = get_term( $first_location_id );
		$first_location_name = $locations[0]->name;
		$first_location_slug = $locations[0]->slug;
		$first_location_url  = get_term_link( $first_location_term );
	}

	// Location
	$map     = ! empty( $meta['map'] ) ? $meta['map'] : '';
	if ( ! empty( $map ) && gettype( $map ) == "string" ) {
		$tf_apartment_map_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $map );
		$map                    = unserialize( $tf_apartment_map_value );
        $address = ! empty($map['address'] ) ? $map['address'] : '';
	}else{
		$address = ! empty($map['address'] ) ? $map['address'] : '';
	}

	// Map Type
	$tf_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";
	$tf_google_map_key = !empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';

	// Apartment Gallery
	$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
	}
	$video = ! empty( $meta['video'] ) ? $meta['video'] : '';

	$share_text = get_the_title();
	$share_link = get_permalink( $post_id );

    // Single Template Style
	$tf_apartment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
	if("single"==$tf_apartment_layout_conditions){
		$tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
	}
	$tf_apartment_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] : 'default';

	$tf_apartment_selected_check = !empty($tf_apartment_single_template) ? $tf_apartment_single_template : $tf_apartment_global_template;

	$tf_apartment_selected_template = $tf_apartment_selected_check;
    
    if( $tf_apartment_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'apartment/design-1.php';
	}else{
		include TF_TEMPLATE_PART_PATH . 'apartment/design-default.php';
	}

endwhile;

get_footer();