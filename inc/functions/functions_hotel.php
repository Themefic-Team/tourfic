<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

function register_tf_hotel_post_type() {

    $hotel_slug = apply_filters( 'tf_hotel_slug', 'hotel' );

    $hotel_labels = apply_filters( 'tf_hotel_labels', array(
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
        'menu_name'             => _x( 'Hotels', 'tourfic post type menu name', 'tourfic' ),
        'featured_image'        => __( '%1$s Image', 'tourfic' ),
        'set_featured_image'    => __( 'Set %1$s Image', 'tourfic' ),
        'remove_featured_image' => __( 'Remove %1$s Image', 'tourfic' ),
        'use_featured_image'    => __( 'Use as %1$s Image', 'tourfic' ),
        'attributes'            => __( '%1$s Attributes', 'tourfic' ),
        'filter_items_list'     => __( 'Filter %2$s list', 'tourfic' ),
        'items_list_navigation' => __( '%2$s list navigation', 'tourfic' ),
        'items_list'            => __( '%2$s list', 'tourfic' ),
    ) );

    foreach ( $hotel_labels as $key => $value ) {
        $hotel_labels[$key] = sprintf( $value, tf_hotel_singular_label(), tf_hotel_plural_label() );
    }

    $hotel_args = array(
        'labels'             => $hotel_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'menu_icon'          => 'dashicons-building',
        'rewrite'            => array( 'slug' => $hotel_slug ),
        'capability_type'    => array( 'tf_hotel', 'tf_hotels' ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => apply_filters( 'tf_hotel_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
    );

    register_post_type( 'tf_hotel', apply_filters( 'tf_hotel_post_type_args', $hotel_args ) );
}
add_action( 'init', 'register_tf_hotel_post_type' );

/**
 * Get Default Labels
 *
 * @since 1.0
 * @return array $defaults Default labels
 */
function tf_hotel_default_labels() {
    $default_hotel = array(
        'singular' => __( 'Hotel', 'tourfic' ),
        'plural'   => __( 'Hotels', 'tourfic' ),
    );
    return apply_filters( 'tf_hotel_name', $default_hotel );
}

/**
 * Get Singular Label
 *
 * @since 1.0
 *
 * @param bool $lowercase
 * @return string $defaults['singular'] Singular label
 */
function tf_hotel_singular_label( $lowercase = false ) {
    $default_hotel = tf_hotel_default_labels();
    return ( $lowercase ) ? strtolower( $default_hotel['singular'] ) : $default_hotel['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0
 * @return string $defaults['plural'] Plural label
 */
function tf_hotel_plural_label( $lowercase = false ) {
    $default_hotel = tf_hotel_default_labels();
    return ( $lowercase ) ? strtolower( $default_hotel['plural'] ) : $default_hotel['plural'];
}

/**
 * Register taxonomies for tf_hotel
 * 
 * tour_destination
 */
function tf_hotel_taxonomies_register() {

    /**
     * Taxonomy: tour_destination.
     */
    $hotel_location_slug = apply_filters( 'hotel_location_slug', 'hotel-location' );

    $hotel_location_labels = array(
        'name'                       => __( 'Locations', 'tourfic' ),
        'singular_name'              => __( 'Location', 'tourfic' ),
        'menu_name'                  => __( 'Location', 'tourfic' ),
        'all_items'                  => __( 'All Locations', 'tourfic' ),
        'edit_item'                  => __( 'Edit Location', 'tourfic' ),
        'view_item'                  => __( 'View Location', 'tourfic' ),
        'update_item'                => __( 'Update location name', 'tourfic' ),
        'add_new_item'               => __( 'Add new location', 'tourfic' ),
        'new_item_name'              => __( 'New location name', 'tourfic' ),
        'parent_item'                => __( 'Parent Location', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Location:', 'tourfic' ),
        'search_items'               => __( 'Search Location', 'tourfic' ),
        'popular_items'              => __( 'Popular Location', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate location with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove location', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used location', 'tourfic' ),
        'not_found'                  => __( 'No location found', 'tourfic' ),
        'no_terms'                   => __( 'No location', 'tourfic' ),
        'items_list_navigation'      => __( 'Location list navigation', 'tourfic' ),
        'items_list'                 => __( 'Locations list', 'tourfic' ),
        'back_to_items'              => __( 'Back to location', 'tourfic' ),
    );

    $hotel_location_args = array(
        'labels'                => $hotel_location_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_location_slug),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_location',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );
    register_taxonomy( 'hotel_location', 'tf_hotel', apply_filters( 'hotel_location_args', $hotel_location_args ) );

    /**
     * Taxonomy: Filters.
     */
    $labels = [
        "name"                       => __( "Filters", 'tourfic' ),
        "singular_name"              => __( "Filter", 'tourfic' ),
        "menu_name"                  => __( "Filters", 'tourfic' ),
        "all_items"                  => __( "All Filters", 'tourfic' ),
        "edit_item"                  => __( "Edit Filter", 'tourfic' ),
        "view_item"                  => __( "View Filter", 'tourfic' ),
        "update_item"                => __( "Update Filter name", 'tourfic' ),
        "add_new_item"               => __( "Add new Filter", 'tourfic' ),
        "new_item_name"              => __( "New Filter name", 'tourfic' ),
        "parent_item"                => __( "Parent Filter", 'tourfic' ),
        "parent_item_colon"          => __( "Parent Filter:", 'tourfic' ),
        "search_items"               => __( "Search Filters", 'tourfic' ),
        "popular_items"              => __( "Popular Filters", 'tourfic' ),
        "separate_items_with_commas" => __( "Separate Filters with commas", 'tourfic' ),
        "add_or_remove_items"        => __( "Add or remove Filters", 'tourfic' ),
        "choose_from_most_used"      => __( "Choose from the most used Filters", 'tourfic' ),
        "not_found"                  => __( "No Filters found", 'tourfic' ),
        "no_terms"                   => __( "No Filters", 'tourfic' ),
        "items_list_navigation"      => __( "Filters list navigation", 'tourfic' ),
        "items_list"                 => __( "Filters list", 'tourfic' ),
        "back_to_items"              => __( "Back to Filters", 'tourfic' ),
    ];

    $args = [
        "label"                 => __( "Filters", 'tourfic' ),
        "labels"                => $labels,
        "public"                => true,
        "publicly_queryable"    => false,
        "hierarchical"          => false,
        "show_ui"               => true,
        "show_in_menu"          => true,
        "show_in_nav_menus"     => true,
        "show_in_quick_edit"    => true,
        "meta_box_cb"           => false,
        "query_var"             => true,
        "rewrite"               => ['slug' => 'tf_filters', 'with_front' => true],
        "show_admin_column"     => true,
        "show_in_rest"          => true,
        "rest_base"             => "tf_filters",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit"    => false,
    ];
    register_taxonomy( 'tf_filters', ['tf_hotel'], apply_filters( 'tf_filters_tax_args', $args ) );

}
add_action( 'init', 'tf_hotel_taxonomies_register' );

/**
 * Flushing Rewrite on Tourfic Activation
 * 
 * tf_hotel post type
 * hotel_destination taxonomy
 */
function tf_hotel_rewrite_flush() {

    register_tf_hotel_post_type();
    tf_hotel_taxonomies_register();
    flush_rewrite_rules();

}
register_activation_hook( TF_PATH . 'tourfic.php', 'tf_hotel_rewrite_flush' );

/**
 * Get Hotel Destinations
 * 
 * {taxonomy-hotel_destination}
 */
if ( !function_exists( 'get_hotel_destinations' ) ) {
    function get_hotel_destinations() {
        $destinations = array();

        $destination_terms = get_terms( array(
            'taxonomy'   => 'hotel_location',
            'hide_empty' => false,
        ) );

        foreach ( $destination_terms as $destination_term ) {
            $destinations[] = $destination_term->name;
        }

        return $destinations;
    }
}
?>