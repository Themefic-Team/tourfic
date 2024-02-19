<?php
/**
 * Template: Apartment Archive
 *
 * Display all apartments here
 * 
 * Default slug: /apartments
 * @author Foysal
 */


get_header();

$tf_apartment_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['apartment-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['apartment-archive'] : 'default';


if( $tf_apartment_arc_selected_template=="design-1"){
	include TF_TEMPLATE_PATH . 'apartment/archive/design-1.php';
}else{
	include TF_TEMPLATE_PATH . 'apartment/archive/design-default.php';
}

get_footer('tourfic');