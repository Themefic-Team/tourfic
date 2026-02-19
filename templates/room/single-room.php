<?php
/**
 * Template: Single Room (Full Width)
 */
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

if(wp_is_block_theme()){
    wp_head();
    block_header_area();
}else{
    get_header();
}

if ( !Helper::tf_is_woo_active() ) {
	?>
	<div class="tf-container">
		<div class="tf-notice tf-notice-danger">
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to view room details.', 'tourfic' ); ?>
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
	$hotel_id = Room::get_hotel_id_by_room_id($post_id);

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
	 * Get room meta values
	 */
	$meta = get_post_meta( $post_id, 'tf_room_opt', true );

	$disable_review_sec   = ! empty( $meta['disable-room-review'] ) ? $meta['disable-room-review'] : '';
	$settings_review = ! empty( Helper::tfopt( 'disable-room-review' ) ) ? Helper::tfopt( 'disable-room-review' ) : 0;
	$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $settings_review;

	$features = ! empty( $meta['features'] ) ? $meta['features'] : '';
	$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery );
	}

	$calcellation_policy_title = function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($meta['cancelation-section-title']) ? esc_html($meta['cancelation-section-title']) : '';
	$calcellation_policy = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['calcellation_policy'] ) ? (array) $meta['calcellation_policy'] : [];
	
	// Single Template Style
	$tf_room_layout_conditions = ! empty( $meta['tf_single_room_layout_opt'] ) ? $meta['tf_single_room_layout_opt'] : 'global';
	if("single"==$tf_room_layout_conditions){
		$tf_room_single_template = ! empty( $meta['tf_single_room_template'] ) ? $meta['tf_single_room_template'] : 'design-1';
	}
	$tf_room_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-room'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-room'] : 'design-1';
	$tf_room_selected_check = !empty($tf_room_single_template) ? $tf_room_single_template : $tf_room_global_template;
	$tf_room_selected_template = $tf_room_selected_check;

    if( $tf_room_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'room/design-1.php';
	}
endwhile;

if(wp_is_block_theme()){
    wp_footer();
    block_footer_area();
 }else{
	get_footer();
 }
