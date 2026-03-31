<?php
/**
 * Template: Hotel Location Archive
 */
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\Templates\Components\Room\Archive\Listings;

if(wp_is_block_theme()){
    wp_head();
    block_header_area();
}else{
    get_header();
}

if ( !Helper::tf_is_woo_active() ) {
	?>
    <div class="tf-container">
        <div class="tf-notice tf-notice-danger">
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to use this feature.', 'tourfic' ); ?>
        </div>
    </div>
	<?php
    get_footer();
	return;
}

$post_type = 'tf_room';
$max = '8';

$tf_room_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] : 'design-1';

if( $post_type == "tf_room" && $tf_room_arc_selected_template=="design-1" ){
?>
<div class="tf-archive-template__one sp-0">
    <?php Helper::tf_archive_sidebar_search_form('tf_room'); ?>
    <?php Listings::render_design_1(); ?>
</div>
<?php }

if(wp_is_block_theme()){
    wp_footer();
    block_footer_area();
 }else{
	get_footer();
 }