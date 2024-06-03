<?php 
use \Tourfic\Classes\Helper;

get_header('tourfic'); 

$tf_tour_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
$tf_hotel_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';

// Check nonce security
if ( !isset( $_GET['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_GET['_nonce'])), 'tf_ajax_nonce' ) ) {
	return;
}

if ( Helper::tf_is_woo_active() ) {
	if ( ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_tours" && $tf_tour_arc_selected_template == "design-1" ) || ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_hotel" && $tf_hotel_arc_selected_template == "design-1" ) ) {
		include TF_TEMPLATE_PART_PATH . 'search/design-1.php';
	} elseif ( ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_tours" && $tf_tour_arc_selected_template == "design-2" ) || ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_hotel" && $tf_hotel_arc_selected_template == "design-2" ) || ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_apartment" && $tf_apartment_arc_selected_template == "design-1" ) ) {
		include TF_TEMPLATE_PART_PATH . 'search/design-2.php';
	} else {
		include TF_TEMPLATE_PART_PATH . 'search/design-default.php';
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
get_footer('tourfic');
