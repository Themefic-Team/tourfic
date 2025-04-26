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


 if(wp_is_block_theme()){
    wp_head();
    block_header_area();
}else{
    get_header();
}

$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';

if ( Helper::tf_is_woo_active() ) {
	if ( $tf_apartment_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'apartment/archive/design-1.php';
	} elseif( $tf_apartment_arc_selected_template == "design-2" && function_exists( 'is_tf_pro' ) && is_tf_pro()){
		include TF_TEMPLATE_PATH . 'apartment/archive/design-2.php';
	} else {
		include TF_TEMPLATE_PATH . 'apartment/archive/design-legacy.php';
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

if(wp_is_block_theme()){
    wp_footer();
    block_footer_area();
 }else{
	get_footer('tourfic');
 }