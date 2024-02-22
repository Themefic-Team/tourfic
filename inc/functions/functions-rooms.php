<?php
# don't load directly
defined( 'ABSPATH' ) || exit;

#################################
# Custom post types, taxonomies #
#################################
/**
 * Register tf_room
 */
function register_tf_room_post_type() {

	$tf_room_setting_permalink_slug = ! empty(tfopt( 'room-permalink-setting' )) ? tfopt( 'room-permalink-setting' ) : "rooms";

	update_option("room_slug", $tf_room_setting_permalink_slug);

	$room_slug = get_option( 'room_slug' );

	$room_labels = apply_filters( 'tf_room_labels', array(
		'name'                  => _x( '%2$s', 'tourfic post type name', 'tourfic' ),
		'singular_name'         => _x( '%1$s', 'singular tourfic post type name', 'tourfic' ),
		'add_new'               => __( 'Add New', 'tourfic' ),
		'add_new_item'          => __( 'Add New %1$s', 'tourfic' ),
		'edit_item'             => __( 'Edit %1$s', 'tourfic' ),
		'new_item'              => __( 'New %1$s', 'tourfic' ),
		'all_items'             => __( 'All %2$s', 'tourfic' ),
		'view_item'             => __( 'View %1$s', 'tourfic' ),
		'view_items'            => __( 'View %2$s', 'tourfic' ),
		'search_items'          => __( 'Search %2$s', 'tourfic' ),
		'not_found'             => __( 'No %2$s found', 'tourfic' ),
		'not_found_in_trash'    => __( 'No %2$s found in Trash', 'tourfic' ),
		'parent_item_colon'     => '',
		'menu_name'             => _x( '%2$s', 'tourfic post type menu name', 'tourfic' ),
		'featured_image'        => __( '%1$s Image', 'tourfic' ),
		'set_featured_image'    => __( 'Set %1$s Image', 'tourfic' ),
		'remove_featured_image' => __( 'Remove %1$s Image', 'tourfic' ),
		'use_featured_image'    => __( 'Use as %1$s Image', 'tourfic' ),
		'attributes'            => __( '%1$s Attributes', 'tourfic' ),
		'filter_items_list'     => __( 'Filter %2$s list', 'tourfic' ),
		'items_list_navigation' => __( '%2$s list navigation', 'tourfic' ),
		'items_list'            => __( '%2$s list', 'tourfic' ),
	) );

	foreach ( $room_labels as $key => $value ) {
		$room_labels[ $key ] = sprintf( $value, tf_room_singular_label(), tf_room_plural_label() );
	}

	$room_args = array(
		'labels'             => $room_labels,
		'public'             => true,
		'show_in_rest'       => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'menu_icon'          => 'dashicons-building',
		'rewrite'            => array( 'slug' => $room_slug, 'with_front' => false ),
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 26.3,
		'supports'           => apply_filters( 'tf_room_supports', array( 'title', 'author' ) ),
	);

	register_post_type( 'tf_room', apply_filters( 'tf_room_post_type_args', $room_args ) );
}


add_action( 'init', 'register_tf_room_post_type' );


/**
 * Get Default Labels
 *
 * @return array $defaults Default labels
 * @since 1.0
 */
function tf_room_default_labels() {
	$default_room = array(
		'singular' => __( 'Room', 'tourfic' ),
		'plural'   => __( 'Rooms', 'tourfic' ),
	);

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		$tf_room_single_name = ! empty(tfopt( 'tf-room-post-rename-singular' )) ? tfopt( 'tf-room-post-rename-singular' ) : __("room", "tourfic");
		$tf_room_plural_name = ! empty(tfopt( 'tf-room-post-rename-plural' )) ? tfopt( 'tf-room-post-rename-plural' ) : __('rooms', 'tourfic');

		$default_room = array(
			'singular' => $tf_room_single_name,
			'plural'   => $tf_room_plural_name
		);

	}

	return apply_filters( 'tf_room_name', $default_room );
}

/**
 * Get Singular Label
 *
 * @param bool $lowercase
 *
 * @return string $defaults['singular'] Singular label
 * @since 1.0
 *
 */
function tf_room_singular_label( $lowercase = false ) {
	$default_room = tf_room_default_labels();

	return ( $lowercase ) ? strtolower( $default_room['singular'] ) : $default_room['singular'];
}

/**
 * Get Plural Label
 *
 * @return string $defaults['plural'] Plural label
 * @since 1.0
 */
function tf_room_plural_label( $lowercase = false ) {
	$default_room = tf_room_default_labels();

	return ( $lowercase ) ? strtolower( $default_room['plural'] ) : $default_room['plural'];
}