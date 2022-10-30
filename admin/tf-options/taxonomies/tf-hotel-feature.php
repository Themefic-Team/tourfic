<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_hotel_feature', array(
	'title'    => 'Tour Settings',
	'taxonomy' => 'hotel_feature',
	'fields'   => array(
		
		array(
			'id'          => 'font-icons',
			'label'       => 'Select Font Awesome Icon',
			'type'        => 'icon'
		),
		array(
			'id'      => 'icons',
			'label'   => 'Upload Custom Icon',
			'type'    => 'image',
		),
	),
) );
