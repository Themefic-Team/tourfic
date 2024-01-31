<?php
/**
 * Template: Hotel Archive
 *
 * Display all hotels here
 * 
 * Default slug: /hotels 
 */


get_header(); 


$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';


if( $tf_hotel_arc_selected_template=="design-1"){
	include TF_TEMPLATE_PATH . 'hotel/archive/design-1.php';
}elseif( $tf_hotel_arc_selected_template=="design-2"){
	include TF_TEMPLATE_PATH . 'hotel/archive/design-2.php';
}else{
	include TF_TEMPLATE_PATH . 'hotel/archive/design-default.php';
}

get_footer('tourfic');