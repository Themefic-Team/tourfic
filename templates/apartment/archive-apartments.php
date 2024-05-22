<?php
/**
 * Template: Apartment Archive
 *
 * Display all apartments here
 * 
 * Default slug: /apartments
 * @author Foysal
 */

 use \Tourfic\Classes\Helper;


get_header();

$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';

if ( Helper::tf_is_woo_active() ) {
	if ( $tf_apartment_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'apartment/archive/design-1.php';
	} else {
		include TF_TEMPLATE_PATH . 'apartment/archive/design-default.php';
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