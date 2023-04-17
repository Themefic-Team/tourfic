<?php get_header('tourfic'); 

if( ( !empty($_GET['type']) && $_GET['type']=="tf_tours" && ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['tour-archive']=="design-1" ) || ( !empty($_GET['type']) && $_GET['type']=="tf_hotel" && ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['hotel-archive']=="design-1" ) ){
	include TF_TEMPLATE_PART_PATH . 'search/design-1.php';
}else{
	include TF_TEMPLATE_PART_PATH . 'search/design-default.php';
}

get_footer('tourfic');
