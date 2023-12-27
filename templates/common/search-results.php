<?php get_header('tourfic'); 

$tf_tour_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';

if( ( !empty($_GET['type']) && $_GET['type']=="tf_tours" && $tf_tour_arc_selected_template=="design-1" ) || ( !empty($_GET['type']) && $_GET['type']=="tf_hotel" && $tf_hotel_arc_selected_template=="design-1" ) ){
	include TF_TEMPLATE_PART_PATH . 'search/design-1.php';
}elseif( ( !empty($_GET['type']) && $_GET['type']=="tf_tours" && $tf_tour_arc_selected_template=="design-2" ) || ( !empty($_GET['type']) && $_GET['type']=="tf_hotel" && $tf_hotel_arc_selected_template=="design-2" ) ){
	include TF_TEMPLATE_PART_PATH . 'search/design-2.php';
}else{
	include TF_TEMPLATE_PART_PATH . 'search/design-default.php';
}
while ( have_posts() ) :

	the_post();

	the_content();
endwhile;
get_footer('tourfic');
