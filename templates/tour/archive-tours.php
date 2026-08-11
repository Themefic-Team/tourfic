<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

// Don't load directly
defined( 'ABSPATH' ) || exit;

 use Tourfic\Classes\Helper;

 if(tf_is_block_theme()){
    wp_head();
    tf_render_block_header_area();
}else{
    get_header('tourfic');
}

$tourfic_meta = get_post_meta( get_the_ID(),'tf_tours_opt',true );
$tourfic_pricing_rule = !empty($tourfic_meta['pricing']) ? $tourfic_meta['pricing'] : null;
if( $tourfic_pricing_rule == 'group'){
	$tourfic_price = !empty($tourfic_meta['group_price']) ? $tourfic_meta['group_price'] : null;
}else{
	$tourfic_price = !empty($tourfic_meta['adult_price']) ? $tourfic_meta['adult_price'] : null;
}
$tourfic_allow_discount    = ! empty( $tourfic_meta['allow_discount'] ) ? $tourfic_meta['allow_discount'] : '';
$tourfic_discount_type = !empty($tourfic_meta['discount_type']) ? $tourfic_meta['discount_type'] : null;
$tourfic_discounted_price = !empty($tourfic_meta['discount_price']) ? $tourfic_meta['discount_price'] : NULL;
if( !empty($tourfic_allow_discount) && $tourfic_discount_type == 'percent' ){
	$tourfic_sale_price = number_format( $tourfic_price - (( $tourfic_price / 100 ) * $tourfic_discounted_price) ,1 ); 
}elseif( !empty($tourfic_allow_discount) && $tourfic_discount_type == 'fixed'){
	$tourfic_sale_price = number_format( ( $tourfic_price - $tourfic_discounted_price ),1 );
}


$tourfic_expired_tour_showing = ! empty( Helper::tfopt( 't-show-expire-tour' ) ) ? Helper::tfopt( 't-show-expire-tour' ) : '';
if(!empty($tourfic_expired_tour_showing )){
	$tourfic_tour_posts_status = array('publish','expired');
}else{
	$tourfic_tour_posts_status = array('publish');
}

$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$tourfic_args = array(
    'post_type' 	 => "tf_tours",
    'orderby'   	 => apply_filters( 'tf_archive_post_orderby', 'date' ),
    'order'     	 => apply_filters( 'tf_archive_post_order', 'DESC' ),
    'post_status'    => $tourfic_tour_posts_status,
	'paged'          => $paged,
);
$tourfic_loop = new WP_Query( $tourfic_args );
$tourfic_total_posts = $tourfic_loop->found_posts;
$tourfic_total_results = 0;

$tourfic_tour_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] : 'design-1';

if ( Helper::tf_is_woo_active() ) {
	if ( $tourfic_tour_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'tour/archive/design-1.php';
	} elseif ( $tourfic_tour_arc_selected_template == "design-2" ) {
		include TF_TEMPLATE_PATH . 'tour/archive/design-2.php';
	} elseif ( $tourfic_tour_arc_selected_template == "design-3" ) {
		include TF_TEMPLATE_PATH . 'tour/archive/design-3.php';
	} else {
		include TF_TEMPLATE_PATH . 'tour/archive/design-legacy.php';
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
