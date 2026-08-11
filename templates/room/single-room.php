<?php
/**
 * Template: Single Room (Full Width)
 */
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

if(tf_is_block_theme()){
    wp_head();
    tf_render_block_header_area();
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
	$tourfic_post_id = $post->ID;
	$tourfic_hotel_id = Room::get_hotel_id_by_room_id($tourfic_post_id);

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
	 * Get room meta values
	 */
	$tourfic_meta = get_post_meta( $tourfic_post_id, 'tf_room_opt', true );

	$tourfic_disable_review_sec   = ! empty( $tourfic_meta['disable-room-review'] ) ? $tourfic_meta['disable-room-review'] : '';
	$tourfic_settings_review = ! empty( Helper::tfopt( 'disable-room-review' ) ) ? Helper::tfopt( 'disable-room-review' ) : 0;
	$tourfic_disable_review_sec = ! empty( $tourfic_disable_review_sec ) ? $tourfic_disable_review_sec : $tourfic_settings_review;

	$tourfic_features = ! empty( $tourfic_meta['features'] ) ? $tourfic_meta['features'] : '';
	$tourfic_gallery = ! empty( $tourfic_meta['gallery'] ) ? $tourfic_meta['gallery'] : '';
	if ( $tourfic_gallery ) {
		$tourfic_gallery_ids = explode( ',', $tourfic_gallery );
	}

	$tourfic_calcellation_policy_title = apply_filters( 'tf_cancellation_policy_title_meta', '', $tourfic_post_id, $tourfic_meta );
	$tourfic_calcellation_policy       = apply_filters( 'tf_cancellation_policy_meta', [], $tourfic_post_id, $tourfic_meta );
	
	// Single Template Style
	$tourfic_room_layout_conditions = ! empty( $tourfic_meta['tf_single_room_layout_opt'] ) ? $tourfic_meta['tf_single_room_layout_opt'] : 'global';
	if("single"==$tourfic_room_layout_conditions){
		$tourfic_room_single_template = ! empty( $tourfic_meta['tf_single_room_template'] ) ? $tourfic_meta['tf_single_room_template'] : 'design-1';
	}
	$tourfic_room_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-room'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-room'] : 'design-1';
	$tourfic_room_selected_check = !empty($tourfic_room_single_template) ? $tourfic_room_single_template : $tourfic_room_global_template;
	$tourfic_room_selected_template = $tourfic_room_selected_check;

    if( $tourfic_room_selected_template == "design-1" ){
		include TF_TEMPLATE_PART_PATH . 'room/design-1.php';
	}
endwhile;

if(tf_is_block_theme()){
    wp_footer();
    tf_render_block_footer_area();
 }else{
	get_footer();
 }
