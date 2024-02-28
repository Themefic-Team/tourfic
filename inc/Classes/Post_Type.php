<?php
namespace Tourfic\Classes;

class Post_Type {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		$this->init();
	}

	public function init() {
		$this->register_post_type();
	}

	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Tours', 'post type general name', 'tourfic' ),
			'singular_name'      => _x( 'Tour', 'post type singular name', 'tourfic' ),
			'menu_name'          => _x( 'Tours', 'admin menu', 'tourfic' ),
			'name_admin_bar'     => _x( 'Tour', 'add new on admin bar', 'tourfic' ),
			'add_new'            => _x( 'Add New', 'tour', 'tourfic' ),
			'add_new_item'       => __( 'Add New Tour', 'tourfic' ),
			'new_item'           => __( 'New Tour', 'tourfic' ),
			'edit_item'          => __( 'Edit Tour', 'tourfic' ),
			'view_item'          => __( 'View Tour', 'tourfic' ),
			'all_items'          => __( 'All Tours', 'tourfic' ),
			'search_items'       => __( 'Search Tours', 'tourfic' ),
			'parent_item_colon'  => __( 'Parent Tours:', 'tourfic' ),
			'not_found'          => __( 'No tours found.', 'tourfic' ),
			'not_found_in_trash' => __( 'No tours found in Trash.', 'tourfic' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'tourfic' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'tour' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		);

	}
}