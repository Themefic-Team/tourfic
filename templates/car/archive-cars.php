<?php
/**
 * Template: Car Archive
 *
 * Display all car here
 * 
 * Default slug: /cars
 * @author Foysal
 */
// Don't load directly
defined( 'ABSPATH' ) || exit;

 use \Tourfic\Classes\Helper;


get_header();

$tf_car_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] : 'design-1';

if ( Helper::tf_is_woo_active() ) {
	if ( $tf_car_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'car/archive/design-1.php';
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