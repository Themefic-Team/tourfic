<?php
/**
 * Registers and sets up the Tourfic custom post type
 *
 * @since 1.0
 * @return void
 */
function tourfic_setup_tourfic_post_types() {

	$archives = defined( 'TOURFIC_DISABLE_ARCHIVE' ) && TOURFIC_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'TOURFIC_SLUG' ) ? TOURFIC_SLUG : apply_filters( 'tourfic_post_type_slug', 'tourfic' );
	$rewrite  = defined( 'TOURFIC_DISABLE_REWRITE' ) && TOURFIC_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$tourfic_labels =  apply_filters( 'tf_tourfic_labels', array(
		'name'                  => _x( '%2$s', 'tourfic post type name', 'tourfic' ),
		'singular_name'         => _x( '%1$s', 'singular tourfic post type name', 'tourfic' ),
		'add_new'               => __( 'Add New', 'tourfic' ),
		'add_new_item'          => __( 'Add New %1$s', 'tourfic' ),
		'edit_item'             => __( 'Edit %1$s', 'tourfic' ),
		'new_item'              => __( 'New %1$s', 'tourfic' ),
		'all_items'             => __( 'All %2$s', 'tourfic' ),
		'view_item'             => __( 'View %1$s', 'tourfic' ),
		'view_items'             => __( 'View %2$s', 'tourfic' ),
		'search_items'          => __( 'Search %2$s', 'tourfic' ),
		'not_found'             => __( 'No %2$s found', 'tourfic' ),
		'not_found_in_trash'    => __( 'No %2$s found in Trash', 'tourfic' ),
		'parent_item_colon'     => '',
		'menu_name'             => _x( 'Tourfic', 'tourfic post type menu name', 'tourfic' ),
		'featured_image'        => __( '%1$s Image', 'tourfic' ),
		'set_featured_image'    => __( 'Set %1$s Image', 'tourfic' ),
		'remove_featured_image' => __( 'Remove %1$s Image', 'tourfic' ),
		'use_featured_image'    => __( 'Use as %1$s Image', 'tourfic' ),
		'attributes'            => __( '%1$s Attributes', 'tourfic' ),
		'filter_items_list'     => __( 'Filter %2$s list', 'tourfic' ),
		'items_list_navigation' => __( '%2$s list navigation', 'tourfic' ),
		'items_list'            => __( '%2$s list', 'tourfic' ),
	) );

	foreach ( $tourfic_labels as $key => $value ) {
		$tourfic_labels[ $key ] = sprintf( $value, tourfic_get_label_singular(), tourfic_get_label_plural() );
	}

	$tourfic_args = array(
		'labels'             => $tourfic_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'menu_icon'          => 'dashicons-calendar',
		'rewrite'            => $rewrite,
		'capability_type'    => 'product',
		'map_meta_cap'       => true,
		'has_archive'        => $archives,
		'hierarchical'       => false,
		'supports'           => apply_filters( 'tf_tourfic_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
	);
	register_post_type( 'tourfic', apply_filters( 'tf_tourfic_post_type_args', $tourfic_args ) );

}
add_action( 'init', 'tourfic_setup_tourfic_post_types', 1 );

/**
 * Get Default Labels
 *
 * @since 1.0
 * @return array $defaults Default labels
 */
function tourfic_get_default_labels() {
	$defaults = array(
	   'singular' => __( 'Tour', 'tourfic' ),
	   'plural'   => __( 'Tours','tourfic' )
	);
	return apply_filters( 'tourfic_default_tours_name', $defaults );
}

/**
 * Get Singular Label
 *
 * @since 1.0
 *
 * @param bool $lowercase
 * @return string $defaults['singular'] Singular label
 */
function tourfic_get_label_singular( $lowercase = false ) {
	$defaults = tourfic_get_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0
 * @return string $defaults['plural'] Plural label
 */
function tourfic_get_label_plural( $lowercase = false ) {
	$defaults = tourfic_get_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}

/**
 * Change default 'Enter title here' input
 *
 * @since 1.0
 * @param string $title Default title placeholder text
 * @return string $title New placeholder text
 */
function tourfic_change_default_title( $title ) {
	 // If a frontend plugin uses this filter (check extensions before changing this function)
	 if ( !is_admin() ) {
		$label = tourfic_get_label_singular();
		$title = sprintf( __( 'Enter %s name here', 'tourfic' ), $label );
		return $title;
	 }

	 $screen = get_current_screen();

	 if ( 'tourfic' == $screen->post_type ) {
		$label = tourfic_get_label_singular();
		$title = sprintf( __( 'Enter %s name here', 'tourfic' ), $label );
	 }

	 return $title;
}
add_filter( 'enter_title_here', 'tourfic_change_default_title' );



/**
 * Taxonomy: Destination.
 */
function tourfic_register_taxes_destination() {

    $labels = [
        'name' => __( 'Destination', 'tourfic' ),
        'singular_name' => __( 'Destinations', 'tourfic' ),
        'menu_name' => __( 'Destination', 'tourfic' ),
        'all_items' => __( 'All Destination', 'tourfic' ),
        'edit_item' => __( 'Edit Destinations', 'tourfic' ),
        'view_item' => __( 'View Destinations', 'tourfic' ),
        'update_item' => __( 'Update Destinations name', 'tourfic' ),
        'add_new_item' => __( 'Add new Destinations', 'tourfic' ),
        'new_item_name' => __( 'New Destinations name', 'tourfic' ),
        'parent_item' => __( 'Parent Destinations', 'tourfic' ),
        'parent_item_colon' => __( 'Parent Destinations:', 'tourfic' ),
        'search_items' => __( 'Search Destination', 'tourfic' ),
        'popular_items' => __( 'Popular Destination', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Destination with commas', 'tourfic' ),
        'add_or_remove_items' => __( 'Add or remove Destination', 'tourfic' ),
        'choose_from_most_used' => __( 'Choose from the most used Destination', 'tourfic' ),
        'not_found' => __( 'No Destination found', 'tourfic' ),
        'no_terms' => __( 'No Destination', 'tourfic' ),
        'items_list_navigation' => __( 'Destination list navigation', 'tourfic' ),
        'items_list' => __( 'Destination list', 'tourfic' ),
        'back_to_items' => __( 'Back to Destination', 'tourfic' ),
    ];

    $destination_args = [
        'label' => __( 'Destination', 'tourfic' ),
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'query_var' => true,
        'rewrite' => [ 'slug' => 'destination', 'with_front' => true, ],
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rest_base' => 'destination',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit' => true,
    ];
    register_taxonomy( 'destination', [ 'tourfic' ], apply_filters( 'tf_destination_tax_args', $destination_args ) );


	/**
	 * Taxonomy: Filters.
	 */
	$labels = [
		"name" => __( "Filters", 'tourfic' ),
		"singular_name" => __( "Filter", 'tourfic' ),
		"menu_name" => __( "Filters", 'tourfic' ),
		"all_items" => __( "All Filters", 'tourfic' ),
		"edit_item" => __( "Edit Filter", 'tourfic' ),
		"view_item" => __( "View Filter", 'tourfic' ),
		"update_item" => __( "Update Filter name", 'tourfic' ),
		"add_new_item" => __( "Add new Filter", 'tourfic' ),
		"new_item_name" => __( "New Filter name", 'tourfic' ),
		"parent_item" => __( "Parent Filter", 'tourfic' ),
		"parent_item_colon" => __( "Parent Filter:", 'tourfic' ),
		"search_items" => __( "Search Filters", 'tourfic' ),
		"popular_items" => __( "Popular Filters", 'tourfic' ),
		"separate_items_with_commas" => __( "Separate Filters with commas", 'tourfic' ),
		"add_or_remove_items" => __( "Add or remove Filters", 'tourfic' ),
		"choose_from_most_used" => __( "Choose from the most used Filters", 'tourfic' ),
		"not_found" => __( "No Filters found", 'tourfic' ),
		"no_terms" => __( "No Filters", 'tourfic' ),
		"items_list_navigation" => __( "Filters list navigation", 'tourfic' ),
		"items_list" => __( "Filters list", 'tourfic' ),
		"back_to_items" => __( "Back to Filters", 'tourfic' ),
	];

	$args = [
		"label" => __( "Filters", 'tourfic' ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => false,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"show_in_quick_edit" => true,
    	"meta_box_cb" => false,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'tf_filters', 'with_front' => true, ],
		"show_admin_column" => true,
		"show_in_rest" => true,
		"rest_base" => "tf_filters",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
	];
	register_taxonomy( 'tf_filters', [ 'tourfic' ], apply_filters( 'tf_filters_tax_args', $args ) );

}
add_action( 'init', 'tourfic_register_taxes_destination' );