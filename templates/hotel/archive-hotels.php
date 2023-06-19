<?php
/**
 * Template: Hotel Archive
 *
 * Display all hotels here
 * 
 * Default slug: /hotels 
 */


get_header(); 

$tf_plugin_installed = get_option('tourfic_template_installed'); 
if (!empty($tf_plugin_installed)) {
	$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
}else{
	$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'default';
}

if( $tf_hotel_arc_selected_template=="design-1"){
	include TF_TEMPLATE_PATH . 'hotel/archive/design-1.php';
}else{
	include TF_TEMPLATE_PATH . 'hotel/archive/design-default.php';
}

get_footer('tourfic');