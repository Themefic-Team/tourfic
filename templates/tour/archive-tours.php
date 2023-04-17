<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 */


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

if( ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['tour-archive']=="design-1"){
	include TF_TEMPLATE_PATH . 'tour/archive/design-1.php';
}else{
	include TF_TEMPLATE_PATH . 'tour/archive/design-default.php';
}

?>

<?php
get_footer('tourfic');