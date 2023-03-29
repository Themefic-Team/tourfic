<?php get_header('tourfic'); 

if( ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['tour-archive']=="design-1"){
	include TF_TEMPLATE_PART_PATH . 'search/design-1.php';
}else{
	include TF_TEMPLATE_PART_PATH . 'search/design-default.php';
}

get_footer('tourfic');
