<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if(tourfic_is_block_theme()){
    wp_head();
    tourfic_render_block_header_area();
}else{
    get_header('tourfic');
}

$tourfic_tour_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
$tourfic_hotel_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
$tourfic_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';
$tourfic_car_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] : 'design-1';
$tourfic_room_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] : 'design-1';

// Check nonce security
if ( !isset( $_GET['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_GET['_nonce'])), 'tf_ajax_nonce' ) ) {
	return;
}

if ( Helper::tf_is_woo_active() ) {
	if ( ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_tours" && $tourfic_tour_arc_selected_template == "design-1" ) || 
		( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_hotel" && $tourfic_hotel_arc_selected_template == "design-1" ) || 
		( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_room" && $tourfic_room_arc_selected_template == "design-1" )) {
		include TOURFIC_TEMPLATE_PART_PATH . 'search/design-1.php';
	} elseif ( ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_tours" && $tourfic_tour_arc_selected_template == "design-2" ) || ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_hotel" && $tourfic_hotel_arc_selected_template == "design-2" ) || ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_apartment" && $tourfic_apartment_arc_selected_template == "design-1" ) ) {
		include TOURFIC_TEMPLATE_PART_PATH . 'search/design-2.php';
	}  elseif ( ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_tours" && $tourfic_tour_arc_selected_template == "design-3" ) ||
                ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_hotel" && $tourfic_hotel_arc_selected_template == "design-3" ) ||
	            ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_apartment" && $tourfic_apartment_arc_selected_template == "design-2" )
    ) {
		include TOURFIC_TEMPLATE_PART_PATH . 'search/design-3.php';
	} else {
		include TOURFIC_TEMPLATE_PART_PATH . 'search/design-legacy.php';
	}
} else {
	?>
	<div class="tf-container">
		<div class="tf-notice tf-notice-danger">
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to use this feature.', 'tourfic' ); ?>
		</div>
	</div>
	<?php
}
while ( have_posts() ) :

	the_post();

	the_content();
endwhile;
if(tourfic_is_block_theme()){
    wp_footer();
    tourfic_render_block_footer_area();
 }else{
	get_footer('tourfic');
 }
