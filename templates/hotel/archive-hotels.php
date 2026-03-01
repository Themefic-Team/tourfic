<?php
/**
 * Template: Hotel Archive
 *
 * Display all hotels here
 * 
 * Default slug: /hotels 
 */
// Don't load directly
defined( 'ABSPATH' ) || exit;

 use \Tourfic\Classes\Helper;


if(wp_is_block_theme()){
    wp_head();
    block_header_area();
}else{
    get_header();
}


$tf_hotel_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';

if ( Helper::tf_is_woo_active() ) {
	if ( $tf_hotel_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-1.php';
	} elseif ( $tf_hotel_arc_selected_template == "design-2" ) {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-2.php';
	} elseif( $tf_hotel_arc_selected_template=="design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()){
		include TF_TEMPLATE_PATH . 'hotel/archive/design-3.php';
	} else {
		$template = apply_filters( 'tf_hotel_archive_legacy_template', TF_TEMPLATE_PATH . 'hotel/archive/design-legacy.php' );
		include $template;
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