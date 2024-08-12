<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

abstract class Post_Type {
	use \Tourfic\Traits\Singleton;

	protected $post_args;
	protected $tax_args;

	public function __construct() {
		add_action( 'init', array( $this, 'tf_post_type_register' ) );
	}

	public function set_post_args(array $post_args) {
		$this->post_args = $post_args;
		return $this;
	}

	public function set_tax_args(array $tax_args) {
		$this->tax_args = $tax_args;
		return $this;
	}

	public function tf_post_type_register() {
		$post_args = $this->post_args;

		$labels = array(
			'name'                  => $post_args['name'],
			'singular_name'         => $post_args['singular_name'],
			'add_new'               => esc_html__( 'Add New', 'tourfic' ),
			/* translators: %s: post type singular name */
			'add_new_item'          => sprintf( esc_html__( 'Add New %s', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type singular name */
			'edit_item'             => sprintf( esc_html__( 'Edit %s', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type singular name */
			'new_item'              => sprintf( esc_html__( 'New %s', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type name */
			'all_items'             => sprintf( esc_html__( 'All %s', 'tourfic' ), $post_args['name'] ),
			/* translators: %s: post type singular name */
			'view_item'             => sprintf( esc_html__( 'View %s', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type name */
			'view_items'            => sprintf( esc_html__( 'View %s', 'tourfic' ), $post_args['name'] ),
			/* translators: %s: post type name */
			'search_items'          => sprintf( esc_html__( 'Search %s', 'tourfic' ), $post_args['name'] ),
			/* translators: %s: post type singular name */
			'not_found'             => sprintf( esc_html__( 'No %s found', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type singular name */
			'not_found_in_trash'    => sprintf( esc_html__( 'No %s found in Trash', 'tourfic' ), $post_args['singular_name'] ),
			'parent_item_colon'     => '',
			'menu_name'             => $post_args['name'],
			/* translators: %s: post type singular name */
			'featured_image'        => sprintf( esc_html__( '%s Image', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type singular name */
			'set_featured_image'    => sprintf( esc_html__( 'Set %s Image', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type singular name */
			'remove_featured_image' => sprintf( esc_html__( 'Remove %s Image', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type singular name */
			'use_featured_image'    => sprintf( esc_html__( 'Use as %s Image', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type singular name */
			'attributes'            => sprintf( esc_html__( '%s Attributes', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type name */
			'filter_items_list'     => sprintf( esc_html__( 'Filter %s list', 'tourfic' ), $post_args['name'] ),
			/* translators: %s: post type singular name */
			'items_list_navigation' => sprintf( esc_html__( '%s list navigation', 'tourfic' ), $post_args['singular_name'] ),
			/* translators: %s: post type name */
			'items_list'            => sprintf( esc_html__( '%s list', 'tourfic' ), $post_args['name'] ),
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
				'name'                       => $tax_args['name'],
				'singular_name'              => $tax_args['singular_name'],
				'menu_name'                  => $tax_args['name'],
				/* translators: %s: taxonomy name */
				'all_items'                  => sprintf( esc_html__( 'All %s', 'tourfic' ), $tax_args['name'] ),
				/* translators: %s: taxonomy singular name */
				'edit_item'                  => sprintf( esc_html__( 'Edit %s', 'tourfic' ), $tax_args['singular_name'] ),
				/* translators: %s: taxonomy singular name */
				'view_item'                  => sprintf( esc_html__( 'View %s', 'tourfic' ), $tax_args['singular_name'] ),
				/* translators: %s: taxonomy singular name */
				'update_item'                => sprintf( esc_html__( 'Update %s name', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'add_new_item'               => sprintf( esc_html__( 'Add New %s', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'new_item_name'              => sprintf( esc_html__( 'New %s name', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'parent_item'                => sprintf( esc_html__( 'Parent %s', 'tourfic' ), $tax_args['singular_name'] ),
				/* translators: %s: taxonomy singular name */
				'parent_item_colon'          => sprintf( esc_html__( 'Parent : %s', 'tourfic' ), $tax_args['singular_name'] ),
				/* translators: %s: taxonomy singular name */
				'search_items'               => sprintf( esc_html__( 'Search %s', 'tourfic' ), $tax_args['singular_name'] ),
				/* translators: %s: taxonomy singular name */
				'popular_items'              => sprintf( esc_html__( 'Popular %s', 'tourfic' ), $tax_args['singular_name'] ),
				/* translators: %s: taxonomy singular name */
				'separate_items_with_commas' => sprintf( esc_html__( 'Separate %s with commas', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %s', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'choose_from_most_used'      => sprintf( esc_html__( 'Choose from the most used %s', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'not_found'                  => sprintf( esc_html__( 'No %s found.', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'no_terms'                   => sprintf( esc_html__( 'No %s', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
				/* translators: %s: taxonomy singular name */
				'items_list_navigation'      => sprintf( esc_html__( '%s list navigation', 'tourfic' ), $tax_args['singular_name'] ),
				/* translators: %s: taxonomy name */
				'items_list'                 => sprintf( esc_html__( '%s list', 'tourfic' ), $tax_args['name'] ),
				/* translators: %s: taxonomy singular name */
				'back_to_items'              => sprintf( esc_html__( 'Back to %s', 'tourfic' ), strtolower( $tax_args['singular_name'] ) ),
			);
			$tax_labels = apply_filters( 'tf_' . $tax_args['taxonomy'] . '_labels', $tax_labels );
			
			$hidden_taxonomies = array( );
			
			if( !empty( $this->post_args['name'] ) && $this->post_args['name'] == 'Apartments' ) {
				$hidden_taxonomies = array( 'Features' );
			}
			else if( !empty( $this->post_args['name'] ) && $this->post_args['name'] == 'Tours' ) {
				$hidden_taxonomies = array( 'Features', "Types" );
			} else {
				$hidden_taxonomies = array( 'Features' );
			}

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

			if(!empty( $hidden_taxonomies ) && in_array( $tax_args['name'], $hidden_taxonomies )) {
				$tf_tax_args['meta_box_cb'] = false;
			}

			register_taxonomy( $tax_args['taxonomy'], $this->post_args['slug'], $tf_tax_args );
		}
	}
}
