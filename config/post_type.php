<?php
$post_type_config = array(
	'hotel' => array(
		'post_args' => array(
			'name'          => esc_html__('Hotels', 'tourfic'),
			'singular_name' => esc_html__('Hotel', 'tourfic'),
			'slug'          => 'tf_hotel',
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 26.2,
			'supports'      => apply_filters( 'tf_hotel_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
			'capability'    => array( 'tf_hotel', 'tf_hotels' ),
			//'rewrite_slug'  => (new \Tourfic\Classes\Hotel\Hotel_CPT())->get_hotel_slug(),
		)
	),
);

return $post_type_config;