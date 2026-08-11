<?php

/**
 * Template: Room Archive
 *
 * Display all Rooms here
 * 
 * Default slug: /rooms
 * @author Mofazzal Hossain
 */

use \Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

get_header();

$tourfic_room_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] : 'design-1';

if (Helper::tf_is_woo_active()) {
    if ( $tourfic_room_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'room/archive/design-1.php';
	}
} else {
?>
    <div class="tf-container">
        <div class="tf-notice tf-notice-danger">
            <?php esc_html_e('Please install and activate WooCommerce plugin to use this feature.', 'tourfic'); ?>
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
