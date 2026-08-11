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


if(tf_is_block_theme()){
    wp_head();
    tf_render_block_header_area();
}else{
    get_header();
}


$tourfic_hotel_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';

if ( Helper::tf_is_woo_active() ) {
	if ( $tourfic_hotel_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-1.php';
	} elseif ( $tourfic_hotel_arc_selected_template == "design-2" ) {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-2.php';
	} elseif ( $tourfic_hotel_arc_selected_template == "design-3" ) {
		include TF_TEMPLATE_PATH . 'hotel/archive/design-3.php';
	} else {
		$tourfic_template = apply_filters( 'tf_hotel_archive_legacy_template', TF_TEMPLATE_PATH . 'hotel/archive/design-legacy.php' );
		include $tourfic_template;
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

if(tf_is_block_theme()){
    wp_footer();
    tf_render_block_footer_area();
 }else{
	get_footer('tourfic');
 }
