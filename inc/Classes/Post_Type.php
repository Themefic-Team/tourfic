<?php

namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

class Post_Type {
	use \Tourfic\Traits\Singleton;

	protected $post_args;
	protected $tax_args;

	public function __construct( $post_args = array(), $tax_args = array() ) {
		$this->post_args = $post_args;
		$this->tax_args  = $tax_args;

		add_action( 'init', array( $this, 'tf_post_type_register' ) );
	}

	public function tf_post_type_register() {
		$post_args = $this->post_args;

		$labels = array(
			'name'                  => _x( $post_args['name'], 'tourfic post type name', 'tourfic' ),
			'singular_name'         => _x( $post_args['singular_name'], 'singular tourfic post type name', 'tourfic' ),
			'add_new'               => __( 'Add New', 'tourfic' ),
			'add_new_item'          => __( 'Add New ' . $post_args['singular_name'], 'tourfic' ),
			'edit_item'             => __( 'Edit ' . $post_args['singular_name'], 'tourfic' ),
			'new_item'              => __( 'New ' . $post_args['singular_name'], 'tourfic' ),
			'all_items'             => __( 'All ' . $post_args['name'], 'tourfic' ),
			'view_item'             => __( 'View ' . $post_args['singular_name'], 'tourfic' ),
			'view_items'            => __( 'View ' . $post_args['name'], 'tourfic' ),
			'search_items'          => __( 'Search ' . $post_args['name'], 'tourfic' ),
			'not_found'             => __( 'No ' . $post_args['name'] . ' found', 'tourfic' ),
			'not_found_in_trash'    => __( 'No ' . $post_args['name'] . ' found in Trash', 'tourfic' ),
			'parent_item_colon'     => '',
			'menu_name'             => _x( $post_args['name'], 'tourfic post type menu name', 'tourfic' ),
			'featured_image'        => __( $post_args['singular_name'] . ' Image', 'tourfic' ),
			'set_featured_image'    => __( 'Set ' . $post_args['singular_name'] . ' Image', 'tourfic' ),
			'remove_featured_image' => __( 'Remove ' . $post_args['singular_name'] . ' Image', 'tourfic' ),
			'use_featured_image'    => __( 'Use as ' . $post_args['singular_name'] . ' Image', 'tourfic' ),
			'attributes'            => __( $post_args['singular_name'] . ' Attributes', 'tourfic' ),
			'filter_items_list'     => __( 'Filter ' . $post_args['name'] . ' list', 'tourfic' ),
			'items_list_navigation' => __( $post_args['name'] . ' list navigation', 'tourfic' ),
			'items_list'            => __( $post_args['name'] . ' list', 'tourfic' ),
		);

		$labels = apply_filters( $post_args['slug'] . '_labels', $labels );

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'show_in_rest'       => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'menu_icon'          => $post_args['menu_icon'],
			'rewrite'            => array( 'slug' => $post_args['rewrite_slug'], 'with_front' => false ),
			'capability_type'    => $post_args['capability'],
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => $post_args['menu_position'],
			'supports'           => $post_args['supports'],
		);

		$args = apply_filters( $post_args['slug'] . '_args', $args );

		register_post_type( $post_args['slug'], $args );
	}

	public function tf_post_type_taxonomy_register() {

		foreach ( $this->tax_args as $tax_args ) {

			$tax_labels = array(
				'name'                       => __( $tax_args['name'], 'tourfic' ),
				'singular_name'              => __( $tax_args['singular_name'], 'tourfic' ),
				'menu_name'                  => __( $tax_args['name'], 'tourfic' ),
				'all_items'                  => __( 'All ' . $tax_args['name'], 'tourfic' ),
				'edit_item'                  => __( 'Edit ' . $tax_args['singular_name'], 'tourfic' ),
				'view_item'                  => __( 'View ' . $tax_args['singular_name'], 'tourfic' ),
				'update_item'                => __( 'Update ' . strtolower( $tax_args['singular_name'] ) . ' name', 'tourfic' ),
				'add_new_item'               => __( 'Add new ' . strtolower( $tax_args['singular_name'] ), 'tourfic' ),
				'new_item_name'              => __( 'New ' . strtolower( $tax_args['singular_name'] ) . ' name', 'tourfic' ),
				'parent_item'                => __( 'Parent ' . $tax_args['singular_name'], 'tourfic' ),
				'parent_item_colon'          => __( 'Parent :' . $tax_args['singular_name'], 'tourfic' ),
				'search_items'               => __( 'Search ' . $tax_args['singular_name'], 'tourfic' ),
				'popular_items'              => __( 'Popular ' . $tax_args['singular_name'], 'tourfic' ),
				'separate_items_with_commas' => __( 'Separate ' . strtolower( $tax_args['singular_name'] ) . ' with commas', 'tourfic' ),
				'add_or_remove_items'        => __( 'Add or remove ' . strtolower( $tax_args['singular_name'] ), 'tourfic' ),
				'choose_from_most_used'      => __( 'Choose from the most used ' . strtolower( $tax_args['singular_name'] ), 'tourfic' ),
				'not_found'                  => __( 'No ' . strtolower( $tax_args['singular_name'] ) . ' found', 'tourfic' ),
				'no_terms'                   => __( 'No ' . strtolower( $tax_args['singular_name'] ), 'tourfic' ),
				'items_list_navigation'      => __( $tax_args['singular_name'] . ' list navigation', 'tourfic' ),
				'items_list'                 => __( $tax_args['name'] . ' list', 'tourfic' ),
				'back_to_items'              => __( 'Back to ' . strtolower( $tax_args['singular_name'] ), 'tourfic' ),
			);
			$tax_labels = apply_filters( 'tf_' . $tax_args['taxonomy'] . '_labels', $tax_labels );

			$tf_tax_args = array(
				'labels'                => $tax_labels,
				'public'                => true,
				'publicly_queryable'    => true,
				'hierarchical'          => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'show_in_nav_menus'     => true,
				'query_var'             => true,
				'rewrite'               => array( 'slug' => $tax_args['rewrite_slug'], 'with_front' => false ),
				'show_admin_column'     => true,
				'show_in_rest'          => true,
				'rest_base'             => $tax_args['taxonomy'],
				'rest_controller_class' => 'WP_REST_Terms_Controller',
				'show_in_quick_edit'    => true,
				'capabilities'          => $tax_args['capability'],
			);
			$tf_tax_args = apply_filters( 'tf_' . $tax_args['taxonomy'] . '_args', $tf_tax_args );

			register_taxonomy( $tax_args['taxonomy'], $this->post_args['slug'], $tf_tax_args );
		}
	}
}
