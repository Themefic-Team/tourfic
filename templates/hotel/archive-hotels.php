<?php
/**
 * Template: Hotel Archive
 *
 * Display all hotels here
 * 
 * Default slug: /hotels 
 */

 use \Tourfic\Classes\Helper;


get_header(); 


$tf_hotel_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';

if ( Helper::tf_is_woo_active() ) {
	if ( $tf_hotel_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-1.php';
	} elseif ( $tf_hotel_arc_selected_template == "design-2" ) {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-2.php';
	} else {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-default.php';
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

get_footer('tourfic');