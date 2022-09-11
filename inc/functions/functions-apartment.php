<?php
# don't load directly
defined( 'ABSPATH' ) || exit;

#################################
# Custom post types, taxonomies #
#################################
/**
 * Register tf_apartment
 */
function register_tf_apartment_post_type() {
    $apartment_slug = !empty(get_option( 'apartment_slug' )) ? get_option( 'apartment_slug' ) : apply_filters( 'tf_apartment_slug', 'apartments' );

    $apartment_labels = apply_filters( 'tf_apartment_labels', array(
      'name'               => _x( 'Apartments', 'post type general name', 'tourfic' ),
      'singular_name'      => _x( 'Apartment', 'post type singular name', 'tourfic' ),
      'add_new'            => _x( 'Add New', 'tourfic' ),
      'add_new_item'       => __( 'Add New Apartment', 'tourfic' ),
      'edit_item'          => __( 'Edit Apartment', 'tourfic' ),
      'new_item'           => __( 'New Apartment', 'tourfic' ),
      'all_items'          => __( 'All Apartment', 'tourfic' ),
      'view_item'          => __( 'View Apartment', 'tourfic' ),
      'view_items'          => __( 'View Apartments', 'tourfic' ),
      'search_items'       => __( 'Search Apartments', 'tourfic' ),
      'not_found'          => __( 'No apartments found', 'tourfic' ),
      'not_found_in_trash' => __( 'No apartments found in the Trash', 'tourfic' ), 
      'parent_item_colon'  => '',
      'menu_name'          => 'Apartments',
      'featured_image'        => __( 'Apartment Featured Image', 'tourfic' ),
        'set_featured_image'    => __( 'Set Apartment Featured Image', 'tourfic' ),
        'remove_featured_image' => __( 'Remove Apartment Featured Image', 'tourfic' ),
        'use_featured_image'    => __( 'Use as Apartment Featured Image', 'tourfic' ),
        'attributes'            => __( 'Apartment Attributes', 'tourfic' ),
        'filter_items_list'     => __( 'Filter Apartment list', 'tourfic' ),
        'items_list_navigation' => __( 'Apartment list navigation', 'tourfic' ),
        'items_list'            => __( 'Apartment list', 'tourfic' )
    ) );
    $apartment_args = array(
        'labels'             => $apartment_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'menu_icon'          => 'dashicons-admin-home',
        'rewrite'            => array( 'slug' => $apartment_slug, 'with_front' => false ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 26.4,
        'supports'           => apply_filters( 'tf_apartment_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
    );
    register_post_type( 'tf_apartment', $apartment_args ); 
  }
  add_action( 'init', 'register_tf_apartment_post_type' );
?>