<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 */


 use Tourfic\Classes\Helper;

get_header('tourfic');

$meta = get_post_meta( get_the_ID(),'tf_tours_opt',true );
$pricing_rule = !empty($meta['pricing']) ? $meta['pricing'] : null;
$tour_type = !empty($meta['type']) ? $meta['type'] : null;
if( $pricing_rule == 'group'){
	$price = !empty($meta['group_price']) ? $meta['group_price'] : null;
}else{
	$price = !empty($meta['adult_price']) ? $meta['adult_price'] : null;
}
$discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : null;
$discounted_price = !empty($meta['discount_price']) ? $meta['discount_price'] : NULL;
if( $discount_type == 'percent' ){
	$sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) ,1 ); 
}elseif( $discount_type == 'fixed'){
	$sale_price = number_format( ( $price - $discounted_price ),1 );
}


$tf_expired_tour_showing = ! empty( Helper::tfopt( 't-show-expire-tour' ) ) ? Helper::tfopt( 't-show-expire-tour' ) : '';
if(!empty($tf_expired_tour_showing )){
	$tf_tour_posts_status = array('publish','expired');
}else{
	$tf_tour_posts_status = array('publish');
}

$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$args = array(
    'post_type' 	 => "tf_tours",
    'orderby'   	 => apply_filters( 'tf_archive_post_orderby', 'date' ),
    'order'     	 => apply_filters( 'tf_archive_post_order', 'DESC' ),
    'post_status'    => $tf_tour_posts_status,
	'paged'          => $paged,
);
$loop = new WP_Query( $args );
$total_posts = $loop->found_posts;
$tf_total_results = 0;

$tf_tour_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] : 'design-1';

if ( Helper::tf_is_woo_active() ) {
	if ( $tf_tour_arc_selected_template == "design-1" ) {
		include TF_TEMPLATE_PATH . 'tour/archive/design-1.php';
	} elseif ( $tf_tour_arc_selected_template == "design-2" ) {
		include TF_TEMPLATE_PATH . 'tour/archive/design-2.php';
	} else {
		include TF_TEMPLATE_PATH . 'tour/archive/design-default.php';
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

?>

<?php
get_footer('tourfic');