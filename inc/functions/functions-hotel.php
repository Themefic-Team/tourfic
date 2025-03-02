<?php
# don't load directly
defined( 'ABSPATH' ) || exit;

#################################
# Custom post types, taxonomies #
#################################
/**
 * Register tf_hotel
 */
function register_tf_hotel_post_type() {

    $hotel_slug = !empty(get_option( 'hotel_slug' )) ? get_option( 'hotel_slug' ) : apply_filters( 'tf_hotel_slug', 'hotels' );

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
        'featured_image'        => __( '%1$s Main Image', 'tourfic' ),
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
        'rewrite'            => array( 'slug' => $hotel_slug, 'with_front' => false ),
        'capability_type'    => array( 'tf_hotel', 'tf_hotels' ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 26.2,
        'supports'           => apply_filters( 'tf_hotel_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
    );

    register_post_type( 'tf_hotel', apply_filters( 'tf_hotel_post_type_args', $hotel_args ) );
}
// Enable/disable check
if(tfopt('disable-services') && in_array('hotel', tfopt('disable-services'))) {} else {
    add_action( 'init', 'register_tf_hotel_post_type' );
}

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
 * hotel_location, hotel_feature
 */
function tf_hotel_taxonomies_register() {

    /**
     * Taxonomy: hotel_country
     */
    $hotel_country_slug = apply_filters( 'hotel_country_slug', 'hotel-country-type' );

    $hotel_country_labels = array(
        'name'                       => __( 'Country', 'tourfic' ),
        'singular_name'              => __( 'Country', 'tourfic' ),
        'menu_name'                  => __( 'Country', 'tourfic' ),
        'all_items'                  => __( 'All Country', 'tourfic' ),
        'edit_item'                  => __( 'Edit Country', 'tourfic' ),
        'view_item'                  => __( 'View Country', 'tourfic' ),
        'update_item'                => __( 'Update Country name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Country', 'tourfic' ),
        'new_item_name'              => __( 'New Country name', 'tourfic' ),
        'parent_item'                => __( 'Parent Country', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Country:', 'tourfic' ),
        'search_items'               => __( 'Search Country', 'tourfic' ),
        'popular_items'              => __( 'Popular Country', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Country with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Country', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Country', 'tourfic' ),
        'not_found'                  => __( 'No Country found', 'tourfic' ),
        'no_terms'                   => __( 'No Country', 'tourfic' ),
        'items_list_navigation'      => __( 'Country list navigation', 'tourfic' ),
        'items_list'                 => __( 'Country list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Country', 'tourfic' ),
    );

    $hotel_country_args = array(
        'labels'                => $hotel_country_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_country_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_country',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_country', 'tf_hotel', apply_filters( 'hotel_country_args', $hotel_country_args ) );
    }

    /**
     * Taxonomy: hotel_month
     */
    $hotel_month_slug = apply_filters( 'hotel_month_slug', 'hotel-month-type' );

    $hotel_month_labels = array(
        'name'                       => __( 'Available Months', 'tourfic' ),
        'singular_name'              => __( 'Month', 'tourfic' ),
        'menu_name'                  => __( 'Month', 'tourfic' ),
        'all_items'                  => __( 'All Month', 'tourfic' ),
        'edit_item'                  => __( 'Edit Month', 'tourfic' ),
        'view_item'                  => __( 'View Month', 'tourfic' ),
        'update_item'                => __( 'Update Month name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Month', 'tourfic' ),
        'new_item_name'              => __( 'New Month name', 'tourfic' ),
        'parent_item'                => __( 'Parent Month', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Month:', 'tourfic' ),
        'search_items'               => __( 'Search Month', 'tourfic' ),
        'popular_items'              => __( 'Popular Month', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Month with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Month', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Month', 'tourfic' ),
        'not_found'                  => __( 'No Month found', 'tourfic' ),
        'no_terms'                   => __( 'No Month', 'tourfic' ),
        'items_list_navigation'      => __( 'Month list navigation', 'tourfic' ),
        'items_list'                 => __( 'Month list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Month', 'tourfic' ),
    );

    $hotel_month_args = array(
        'labels'                => $hotel_month_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_month_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_month',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_month', 'tf_hotel', apply_filters( 'hotel_month_args', $hotel_month_args ) );
    }


    /**
     * Taxonomy: hotel_type_property
     */
    $hotel_type_property_slug = apply_filters( 'hotel_type_property_slug', 'hotel-property-type' );

    $hotel_type_property_labels = array(
        'name'                       => __( 'Type of Property', 'tourfic' ),
        'singular_name'              => __( 'Type of Property', 'tourfic' ),
        'menu_name'                  => __( 'Type of Property', 'tourfic' ),
        'all_items'                  => __( 'All Type of Property', 'tourfic' ),
        'edit_item'                  => __( 'Edit Type of Property', 'tourfic' ),
        'view_item'                  => __( 'View Type of Property', 'tourfic' ),
        'update_item'                => __( 'Update Type of Property name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Type of Property', 'tourfic' ),
        'new_item_name'              => __( 'New Type of Property name', 'tourfic' ),
        'parent_item'                => __( 'Parent Type of Property', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Type of Property:', 'tourfic' ),
        'search_items'               => __( 'Search Type of Property', 'tourfic' ),
        'popular_items'              => __( 'Popular Type of Property', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Type of Property with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Type of Property', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Type of Property', 'tourfic' ),
        'not_found'                  => __( 'No Type of Property found', 'tourfic' ),
        'no_terms'                   => __( 'No Type of Property', 'tourfic' ),
        'items_list_navigation'      => __( 'Type of Property list navigation', 'tourfic' ),
        'items_list'                 => __( 'Type of Property list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Type of Property', 'tourfic' ),
    );

    $hotel_type_property_args = array(
        'labels'                => $hotel_type_property_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_type_property_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_type_property',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_type_property', 'tf_hotel', apply_filters( 'hotel_type_property_args', $hotel_type_property_args ) );
    }


    /**
     * Taxonomy: hotel_location
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
        'rewrite'               => array('slug' => $hotel_location_slug, 'with_front' => false ),
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
     * Taxonomy: hotel_style_property
     */
    $hotel_style_property_slug = apply_filters( 'hotel_style_property_slug', 'hotel-property-style' );

    $hotel_style_property_labels = array(
        'name'                       => __( 'Style of Property', 'tourfic' ),
        'singular_name'              => __( 'Style of Property', 'tourfic' ),
        'menu_name'                  => __( 'Style of Property', 'tourfic' ),
        'all_items'                  => __( 'All Style of Property', 'tourfic' ),
        'edit_item'                  => __( 'Edit Style of Property', 'tourfic' ),
        'view_item'                  => __( 'View Style of Property', 'tourfic' ),
        'update_item'                => __( 'Update Style of Property name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Style of Property', 'tourfic' ),
        'new_item_name'              => __( 'New Style of Property name', 'tourfic' ),
        'parent_item'                => __( 'Parent Style of Property', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Style of Property:', 'tourfic' ),
        'search_items'               => __( 'Search Style of Property', 'tourfic' ),
        'popular_items'              => __( 'Popular Style of Property', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Style of Property with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Style of Property', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Style of Property', 'tourfic' ),
        'not_found'                  => __( 'No Style of Property found', 'tourfic' ),
        'no_terms'                   => __( 'No Style of Property', 'tourfic' ),
        'items_list_navigation'      => __( 'Style of Property list navigation', 'tourfic' ),
        'items_list'                 => __( 'Style of Property list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Style of Property', 'tourfic' ),
    );

    $hotel_style_property_args = array(
        'labels'                => $hotel_style_property_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_style_property_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_style_property',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_style_property', 'tf_hotel', apply_filters( 'hotel_style_property_args', $hotel_style_property_args ) );
    }


    /**
     * Taxonomy: hotel_rating
     */
    $hotel_rating_slug = apply_filters( 'hotel_rating_slug', 'hotel-property-style' );

    $hotel_rating_labels = array(
        'name'                       => __( 'Stars', 'tourfic' ),
        'singular_name'              => __( 'Stars', 'tourfic' ),
        'menu_name'                  => __( 'Stars', 'tourfic' ),
        'all_items'                  => __( 'All Stars', 'tourfic' ),
        'edit_item'                  => __( 'Edit Stars', 'tourfic' ),
        'view_item'                  => __( 'View Stars', 'tourfic' ),
        'update_item'                => __( 'Update Stars name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Stars', 'tourfic' ),
        'new_item_name'              => __( 'New Stars name', 'tourfic' ),
        'parent_item'                => __( 'Parent Stars', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Stars:', 'tourfic' ),
        'search_items'               => __( 'Search Stars', 'tourfic' ),
        'popular_items'              => __( 'Popular Stars', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Stars with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Stars', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Stars', 'tourfic' ),
        'not_found'                  => __( 'No Stars found', 'tourfic' ),
        'no_terms'                   => __( 'No Stars', 'tourfic' ),
        'items_list_navigation'      => __( 'Stars list navigation', 'tourfic' ),
        'items_list'                 => __( 'Stars list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Stars', 'tourfic' ),
    );

    $hotel_rating_args = array(
        'labels'                => $hotel_rating_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_rating_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_rating',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_rating', 'tf_hotel', apply_filters( 'hotel_rating_args', $hotel_rating_args ) );
    }

    /**
     * Taxonomy: hotel_day
     */
    $hotel_day_slug = apply_filters( 'hotel_day_slug', 'hotel-property-style' );

    $hotel_day_labels = array(
        'name'                       => __( 'Days Stay', 'tourfic' ),
        'singular_name'              => __( 'Days Stay', 'tourfic' ),
        'menu_name'                  => __( 'Days Stay', 'tourfic' ),
        'all_items'                  => __( 'All Days Stay', 'tourfic' ),
        'edit_item'                  => __( 'Edit Days Stay', 'tourfic' ),
        'view_item'                  => __( 'View Days Stay', 'tourfic' ),
        'update_item'                => __( 'Update Days Stay name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Days Stay', 'tourfic' ),
        'new_item_name'              => __( 'New Days Stay name', 'tourfic' ),
        'parent_item'                => __( 'Parent Days Stay', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Days Stay:', 'tourfic' ),
        'search_items'               => __( 'Search Days Stay', 'tourfic' ),
        'popular_items'              => __( 'Popular Days Stay', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Days Stay with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Days Stay', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Days Stay', 'tourfic' ),
        'not_found'                  => __( 'No Days Stay found', 'tourfic' ),
        'no_terms'                   => __( 'No Days Stay', 'tourfic' ),
        'items_list_navigation'      => __( 'Days Stay list navigation', 'tourfic' ),
        'items_list'                 => __( 'Days Stay list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Days Stay', 'tourfic' ),
    );

    $hotel_day_args = array(
        'labels'                => $hotel_day_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_day_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_day',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_day', 'tf_hotel', apply_filters( 'hotel_day_args', $hotel_day_args ) );
    }

    /**
     * Taxonomy: hotel_meals
     */
    $hotel_meals_slug = apply_filters( 'hotel_meals_slug', 'hotel-meals-style' );

    $hotel_meals_labels = array(
        'name'                       => __( 'Meals', 'tourfic' ),
        'singular_name'              => __( 'Meals', 'tourfic' ),
        'menu_name'                  => __( 'Meals', 'tourfic' ),
        'all_items'                  => __( 'All Meals', 'tourfic' ),
        'edit_item'                  => __( 'Edit Meals', 'tourfic' ),
        'view_item'                  => __( 'View Meals', 'tourfic' ),
        'update_item'                => __( 'Update Meals name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Meals', 'tourfic' ),
        'new_item_name'              => __( 'New Meals name', 'tourfic' ),
        'parent_item'                => __( 'Parent Meals', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Meals:', 'tourfic' ),
        'search_items'               => __( 'Search Meals', 'tourfic' ),
        'popular_items'              => __( 'Popular Meals', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Meals with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Meals', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Meals', 'tourfic' ),
        'not_found'                  => __( 'No Meals found', 'tourfic' ),
        'no_terms'                   => __( 'No Meals', 'tourfic' ),
        'items_list_navigation'      => __( 'Meals list navigation', 'tourfic' ),
        'items_list'                 => __( 'Meals list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Meals', 'tourfic' ),
    );

    $hotel_meals_args = array(
        'labels'                => $hotel_meals_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_meals_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_meals',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_meals', 'tf_hotel', apply_filters( 'hotel_meals_args', $hotel_meals_args ) );
    }


    /**
     * Taxonomy: hotel_theme
     */
    $hotel_theme_slug = apply_filters( 'hotel_theme_slug', 'hotel-theme-style' );

    $hotel_theme_labels = array(
        'name'                       => __( 'Theme', 'tourfic' ),
        'singular_name'              => __( 'Theme', 'tourfic' ),
        'menu_name'                  => __( 'Theme', 'tourfic' ),
        'all_items'                  => __( 'All Theme', 'tourfic' ),
        'edit_item'                  => __( 'Edit Theme', 'tourfic' ),
        'view_item'                  => __( 'View Theme', 'tourfic' ),
        'update_item'                => __( 'Update Theme name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Theme', 'tourfic' ),
        'new_item_name'              => __( 'New Theme name', 'tourfic' ),
        'parent_item'                => __( 'Parent Theme', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Theme:', 'tourfic' ),
        'search_items'               => __( 'Search Theme', 'tourfic' ),
        'popular_items'              => __( 'Popular Theme', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Theme with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Theme', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Theme', 'tourfic' ),
        'not_found'                  => __( 'No Theme found', 'tourfic' ),
        'no_terms'                   => __( 'No Theme', 'tourfic' ),
        'items_list_navigation'      => __( 'Theme list navigation', 'tourfic' ),
        'items_list'                 => __( 'Theme list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Theme', 'tourfic' ),
    );

    $hotel_theme_args = array(
        'labels'                => $hotel_theme_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_theme_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_theme',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );

    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_theme', 'tf_hotel', apply_filters( 'hotel_theme_args', $hotel_theme_args ) );
    }

    /**
     * Taxonomy: hotel_activities
     */
    $hotel_activities_slug = apply_filters( 'hotel_activities_slug', 'hotel-activities-style' );

    $hotel_activities_labels = array(
        'name'                       => __( 'Activities', 'tourfic' ),
        'singular_name'              => __( 'Activities', 'tourfic' ),
        'menu_name'                  => __( 'Activities', 'tourfic' ),
        'all_items'                  => __( 'All Activities', 'tourfic' ),
        'edit_item'                  => __( 'Edit Activities', 'tourfic' ),
        'view_item'                  => __( 'View Activities', 'tourfic' ),
        'update_item'                => __( 'Update Activities name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Activities', 'tourfic' ),
        'new_item_name'              => __( 'New Activities name', 'tourfic' ),
        'parent_item'                => __( 'Parent Activities', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Activities:', 'tourfic' ),
        'search_items'               => __( 'Search Activities', 'tourfic' ),
        'popular_items'              => __( 'Popular Activities', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Activities with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Activities', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Activities', 'tourfic' ),
        'not_found'                  => __( 'No Activities found', 'tourfic' ),
        'no_terms'                   => __( 'No Activities', 'tourfic' ),
        'items_list_navigation'      => __( 'Activities list navigation', 'tourfic' ),
        'items_list'                 => __( 'Activities list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Activities', 'tourfic' ),
    );

    $hotel_activities_args = array(
        'labels'                => $hotel_activities_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $hotel_activities_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'hotel_activities',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    );
    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        register_taxonomy( 'hotel_activities', 'tf_hotel', apply_filters( 'hotel_activities_args', $hotel_activities_args ) );
    }

    /**
     * Taxonomy: hotel_feature.
     */
    
    $labels = [
        "name"                       => __( "Features", 'tourfic' ),
        "singular_name"              => __( "Feature", 'tourfic' ),
        "menu_name"                  => __( "Features", 'tourfic' ),
        "all_items"                  => __( "All Features", 'tourfic' ),
        "edit_item"                  => __( "Edit Feature", 'tourfic' ),
        "view_item"                  => __( "View Feature", 'tourfic' ),
        "update_item"                => __( "Update Feature", 'tourfic' ),
        "add_new_item"               => __( "Add new Feature", 'tourfic' ),
        "new_item_name"              => __( "New Feature name", 'tourfic' ),
        "parent_item"                => __( "Parent Feature", 'tourfic' ),
        "parent_item_colon"          => __( "Parent Feature:", 'tourfic' ),
        "search_items"               => __( "Search Feature", 'tourfic' ),
        "popular_items"              => __( "Popular Features", 'tourfic' ),
        "separate_items_with_commas" => __( "Separate Features with commas", 'tourfic' ),
        "add_or_remove_items"        => __( "Add or remove Features", 'tourfic' ),
        "choose_from_most_used"      => __( "Choose from the most used Features", 'tourfic' ),
        "not_found"                  => __( "No Features found", 'tourfic' ),
        "no_terms"                   => __( "No Features", 'tourfic' ),
        "items_list_navigation"      => __( "Features list navigation", 'tourfic' ),
        "items_list"                 => __( "Features list", 'tourfic' ),
        "back_to_items"              => __( "Back to Features", 'tourfic' ),
    ];

    $args = [
        "labels"                => $labels,
        "public"                => true,
        "publicly_queryable"    => true,
        "hierarchical"          => true,
        "show_ui"               => true,
        "show_in_menu"          => true,
        "show_in_nav_menus"     => true,
        "query_var"             => true,
        "rewrite"               => ['slug' => 'hotel_feature', 'with_front' => true],
        "show_admin_column"     => true,
        "show_in_rest"          => true,
        "rest_base"             => "hotel_feature",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit"    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_hotel',
            'edit_terms' => 'edit_tf_hotel',
         ),
    ];
    register_taxonomy( 'hotel_feature', 'tf_hotel', apply_filters( 'tf_feature_tax_args', $args ) );


    

}
add_action( 'init', 'tf_hotel_taxonomies_register' );

###############################################
# Functions related to post types, taxonomies #
###############################################

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
 * Get Hotel Locations
 * 
 * {taxonomy-hotel_location}
 */
if ( !function_exists( 'get_hotel_locations' ) ) {
    function get_hotel_locations() {
        
        $locations = array();

        $location_terms = get_terms( array(
            'taxonomy'   => 'hotel_location',
            'hide_empty' => false,
        ) );

        foreach ( $location_terms as $location_term ) {
            $locations[$location_term->slug] = $location_term->name;
        }

        return $locations;
    }
}



#################################
# Air port Service Price        #
#################################

add_action( 'wp_ajax_tf_hotel_airport_service_price', 'tf_hotel_airport_service_callback' );
add_action( 'wp_ajax_nopriv_tf_hotel_airport_service_price', 'tf_hotel_airport_service_callback' );

function tf_hotel_airport_service_callback(){

$meta = get_post_meta( sanitize_key( $_POST['id'] ), 'tf_hotel', true );
$airport_service  = !empty($meta['airport_service']) ? $meta['airport_service'] : '';

if(1==$airport_service){ 
    
    $post_id   = isset( $_POST['id'] ) ? intval( sanitize_text_field( $_POST['id'] ) ) : null;
    $room_id   = isset( $_POST['roomid'] ) ? intval( sanitize_text_field( $_POST['roomid'] ) ) : null;
    $adult         = isset( $_POST['hoteladult'] ) ? intval( sanitize_text_field( $_POST['hoteladult'] ) ) : '0';
    $child         = isset( $_POST['hotelchildren'] ) ? intval( sanitize_text_field( $_POST['hotelchildren'] ) ) : '0';
    $room_selected = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
    $check_in      = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
    $check_out     = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
    $deposit     = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;
    $hotel_pack = isset($_POST['hotel_pack']) ? sanitize_text_field($_POST['hotel_pack']) : '';
    $hotel_meal = isset($_POST['hotel_meal']) ? sanitize_text_field($_POST['hotel_meal']) : '';


    # Calculate night number
    if($check_in && $check_out) {
        $check_in_stt   = strtotime( $check_in . ' +1 day' );
        $check_out_stt  = strtotime( $check_out );
        $day_difference = round(  (  ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
    }
    
    
    $meta          = get_post_meta( $post_id, 'tf_hotel', true );
    $rooms         = !empty( $meta['room'] ) ? $meta['room'] : '';

    $avail_by_date = !empty( $rooms[$room_id]['avil_by_date'] ) && $rooms[$room_id]['avil_by_date'];
    if ( $avail_by_date ) {
        $repeat_by_date = !empty( $rooms[$room_id]['repeat_by_date'] ) ? $rooms[$room_id]['repeat_by_date'] : [];
    }
    
    $pricing_by      = $rooms[$room_id]['pricing-by'];
    $price_multi_day = !empty( $rooms[$room_id]['price_multi_day'] ) ? $rooms[$room_id]['price_multi_day'] : false;
   

    /**
     * Calculate Pricing
     */
    // if ( $avail_by_date && defined( 'TF_PRO' ) ) {
        
    //     // Check availability by date option
    //     $period = new DatePeriod(
    //         new DateTime( $check_in . ' 00:00' ),
    //         new DateInterval( 'P1D' ),
    //         new DateTime( $check_out . ' 00:00' )
    //     );
        
    //     $total_price = 0;
    //     foreach ( $period as $date ) {
                    
    //     $available_rooms = array_values( array_filter( $repeat_by_date, function ($date_availability ) use ( $date ) {
    //         $date_availability_from = strtotime( $date_availability['availability']['from'] . ' 00:00' );
    //         $date_availability_to   = strtotime( $date_availability['availability']['to'] . ' 23:59' );
    //         return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
    //     } ) );

    //     if ( is_iterable($available_rooms) && count( $available_rooms ) >=1) {                    
    //         $room_price    = !empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $rooms[$room_id]['price'];
    //         $adult_price   = !empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $rooms[$room_id]['adult_price'];
    //         $child_price   = !empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $rooms[$room_id]['child_price'];
    //         $total_price += $pricing_by == '1' ? $room_price : (  ( $adult_price * $adult ) + ( $child_price * $child ) );
            
    //     } ;
            
    //     } 
        
    //     $price_total = $total_price*$room_selected;
    // } else {

    //     if ( $pricing_by == '1' ) {
    //         $total_price = $rooms[$room_id]['price'];
            
    //     } elseif ( $pricing_by == '2' ) {
    //         $adult_price = $rooms[$room_id]['adult_price'];
    //         $adult_price = $adult_price * $adult;
    //         $child_price = $rooms[$room_id]['child_price'];
    //         $child_price = $child_price * $child;
    //         $total_price = $adult_price + $child_price;    
            
    //     }

    //     # Multiply pricing by night number
    //     if(!empty($day_difference) && $price_multi_day == true) {
    //         $price_total = $total_price*$room_selected*$day_difference;
    //     } else {
    //         $price_total = $total_price*$room_selected;
    //     }

    // }

    // $roompackageprice = $rooms[$room_id]['tf-'.$hotel_pack.'-days'];
    // $price_total = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$adult;

    $roompackageprice = $rooms[$room_id]['tf-'.$hotel_pack.'-days'];
    if(!empty($hotel_meal)){
        $price_total = ($roompackageprice['tf-room'] + $roompackageprice[''.$hotel_meal.''])*$adult;
    }else{
        $price_total = $roompackageprice['tf-room']*$adult;
    }

    if($deposit=="true"){
        tf_get_deposit_amount($rooms[$room_id], $price_total, $deposit_amount, $has_deposit);
        if (defined( 'TF_PRO' ) && $has_deposit == true &&  !empty($deposit_amount) ) {
            $deposit_amount;
        }
    }

if("pickup"==$_POST['service_type']){
    $airport_pickup_price  = !empty($meta['airport_pickup_price']) ? $meta['airport_pickup_price'] : '';
    if("per_person"==$airport_pickup_price['airport_pickup_price_type']){
        $service_fee = (sanitize_key($_POST['hoteladult'])*$airport_pickup_price['airport_service_fee_adult']) + (sanitize_key($_POST['hotelchildren'])*$airport_pickup_price['airport_service_fee_children']);
        if(sanitize_key( $_POST['hotelchildren'] ) != 0){
            echo "<span>Airport Pickup Fee Adult ( ". sanitize_key( $_POST['hoteladult'] ) ." × ". wc_price($airport_pickup_price['airport_service_fee_adult']). " ) + Child ( " . sanitize_key( $_POST['hotelchildren'] ) ." × ". wc_price($airport_pickup_price['airport_service_fee_children'])." ) : <b>".wc_price($service_fee)."</b></span></br>";
        }else{
            echo "<span>Airport Pickup Fee Adult ( ". sanitize_key( $_POST['hoteladult'] ) ." × ". wc_price($airport_pickup_price['airport_service_fee_adult'])." ) : <b>".wc_price($service_fee)."</b></span></br>";
        }
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)." + ".wc_price($service_fee)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total+$service_fee)."</b></span>";
        }
    }
    if("fixed"==$airport_pickup_price['airport_pickup_price_type']){
        $service_fee = $airport_pickup_price['airport_service_fee_fixed'];
        echo "<span>Airport Pickup Fee (Fixed): <b>".wc_price($service_fee)."</b></span></br>";
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)." + ".wc_price($service_fee)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total+$service_fee)."</b></span>";
        }
    }
    if("free"==$airport_pickup_price['airport_pickup_price_type']){
        echo "<span>Airport Pickup Fee: <b>Free</b></span></br>";
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total)."</b></span>";
        }
    }
}
if("dropoff"==$_POST['service_type']){
    $airport_dropoff_price  = !empty($meta['airport_dropoff_price']) ? $meta['airport_dropoff_price'] : '';
    if("per_person"==$airport_dropoff_price['airport_pickup_price_type']){
        $service_fee = (sanitize_key($_POST['hoteladult'])*$airport_dropoff_price['airport_service_fee_adult']) + (sanitize_key($_POST['hotelchildren'])*$airport_dropoff_price['airport_service_fee_children']);
        if(sanitize_key( $_POST['hotelchildren'] ) != 0){
            echo "<span>Airport Dropoff Fee Adult ( ". sanitize_key( $_POST['hoteladult'] ) ." × ". wc_price($airport_dropoff_price['airport_service_fee_adult']). " ) + Child ( " . sanitize_key( $_POST['hotelchildren'] ) ." × ". wc_price($airport_dropoff_price['airport_service_fee_children'])." ) : <b>".wc_price($service_fee)."</b></span></br>";
        }else{
            echo "<span>Airport Dropoff Fee Adult ( ". sanitize_key( $_POST['hoteladult'] ) ." × ". wc_price($airport_dropoff_price['airport_service_fee_adult'])." ) : <b>".wc_price($service_fee)."</b></span></br>";
        }
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)." + ".wc_price($service_fee)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total+$service_fee)."</b></span>";
        }
    }
    if("fixed"==$airport_dropoff_price['airport_pickup_price_type']){
        $service_fee = $airport_dropoff_price['airport_service_fee_fixed'];
        echo "<span>Airport Dropoff Fee (Fixed): <b>".wc_price($service_fee)."</b></span></br>";
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)." + ".wc_price($service_fee)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total+$service_fee)."</b></span>";
        }
    }
    if("free"==$airport_dropoff_price['airport_pickup_price_type']){
        echo "<span>Airport Dropoff Fee: <b>Free</b></span></br>";
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total)."</b></span>";
        }
    }
}
if("both"==$_POST['service_type']){
    $airport_pickup_dropoff_price  = !empty($meta['airport_pickup_dropoff_price']) ? $meta['airport_pickup_dropoff_price'] : '';
    if("per_person"==$airport_pickup_dropoff_price['airport_pickup_price_type']){
        $service_fee = (sanitize_key($_POST['hoteladult'])*$airport_pickup_dropoff_price['airport_service_fee_adult']) + (sanitize_key($_POST['hotelchildren'])*$airport_pickup_dropoff_price['airport_service_fee_children']);
        if(sanitize_key( $_POST['hotelchildren'] ) != 0){
            echo "<span>Airport Pickup & Dropoff Fee Adult ( ". sanitize_key( $_POST['hoteladult'] ) ." × ". wc_price($airport_pickup_dropoff_price['airport_service_fee_adult']). " ) + Child ( " . sanitize_key( $_POST['hotelchildren'] ) ." × ". wc_price($airport_pickup_dropoff_price['airport_service_fee_children'])." ) : <b>".wc_price($service_fee)."</b></span></br>";
        }else{
            echo "<span>Airport Pickup & Dropoff Fee Adult ( ". sanitize_key( $_POST['hoteladult'] ) ." × ". wc_price($airport_pickup_dropoff_price['airport_service_fee_adult'])." ) : <b>".wc_price($service_fee)."</b></span></br>";
        }
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)." + ".wc_price($service_fee)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total+$service_fee)."</b></span>";
        }
        
    }
    if("fixed"==$airport_pickup_dropoff_price['airport_pickup_price_type']){
        $service_fee = $airport_pickup_dropoff_price['airport_service_fee_fixed'];
        echo "<span>Airport Pickup & Dropoff Fee (Fixed): <b>".wc_price($service_fee)."</b></span></br>";

        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)." + ".wc_price($service_fee)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total+$service_fee)."</b></span>";
        }
    }
    if("free"==$airport_pickup_dropoff_price['airport_pickup_price_type']){
        echo "<span>Airport Pickup & Dropoff Fee: <b>Free</b></span></br>";
        if($deposit=="true"){
            echo "<span>Due Amount : <b>".wc_price($price_total-$deposit_amount)."</b></span></br>";
            echo "<span>Total Payable Amount : <b>".wc_price($deposit_amount)."</b></span>";
        }else{
            echo "<span>Total Payable Amount : <b>".wc_price($price_total)."</b></span>";
        }
    }
}

}
wp_die();    
}



#################################
# Ajax functions                #
#################################
/**
 * Ajax hotel room availability
 * 
 * @author fida
 */
add_action( 'wp_ajax_tf_room_availability', 'tf_room_availability_callback' );
add_action( 'wp_ajax_nopriv_tf_room_availability', 'tf_room_availability_callback' );
function tf_room_availability_callback() {

    // Check nonce security
    if ( !isset( $_POST['tf_room_avail_nonce'] ) || !wp_verify_nonce( $_POST['tf_room_avail_nonce'], 'check_room_avail_nonce' ) ) {
        return;
    }

    /**
     * Form data
     */
    $form_post_id      = !empty( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
    $form_adult        = !empty( $_POST['adult'] ) ? sanitize_text_field( $_POST['adult'] ) : 0;
    $form_child        = !empty( $_POST['child'] ) ? sanitize_text_field( $_POST['child'] ) : 0;
    $form_check_in_out = !empty( $_POST['check_in_out'] ) ? sanitize_text_field( $_POST['check_in_out'] ) : '';
    $form_total_person = $form_adult + $form_child;
    if ($form_check_in_out) {
        list( $form_start, $form_end ) = explode( ' - ', $form_check_in_out );
    }
    $form_check_in = $form_start;
    $form_start = date( 'Y/m/d', strtotime( $form_start ) );
    /**
     * Backend data
     */
    $meta  = get_post_meta( $form_post_id, 'tf_hotel', true );
    $rooms = !empty($meta['room']) ? $meta['room'] : '';
    $locations = get_the_terms( $form_post_id, 'hotel_location' );
    $first_location_name = !empty( $locations ) ? $locations[0]->name : '';

    // start table
    ob_start();
    ?>

    <h2 class="section-heading"><?php esc_html_e( 'Available Rooms', 'tourfic' ); ?></h2>
    <div class="tf-room-table hotel-room-wrap">
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo TF_ASSETS_URL ?>img/loader.gif" alt="">
            </div>
        </div>
        <table class="availability-table">
            <thead>
                <tr>
                    <th class="description"><?php _e( 'ROOM DETAILS', 'tourfic' ); ?></th>
                    <th class="pax"><?php _e( 'PERSONS', 'tourfic' ); ?></th>
                    <th class="pricing"><?php _e( 'Price', 'tourfic' ); ?></th>
                    <th class="reserve"><?php _e( 'Select Rooms', 'tourfic' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo ob_get_clean();
                $error = $rows = null;
                $has_room = false;

                // generate table rows
                if ( !empty( $rooms ) ) {
                    ob_start();
                    foreach ( $rooms as $room_id => $room ) {
                        // Check if room is enabled
                        $enable = !empty($room['enable']) && boolval($room['enable']);

                        if ( $enable )  {                         
                            
                            /*
                            * Backend room options
                            */
                            $footage          = !empty( $room['footage'] ) ? $room['footage'] : 0;
                            $bed              = !empty( $room['bed'] ) ? $room['bed'] : 0;
                            $adult_number     = !empty( $room['adult'] ) ? $room['adult'] : 0;
                            $child_number     = !empty( $room['child'] ) ? $room['child'] : 0;
                            $pricing_by       = !empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                            $room_price       = !empty( $room['price'] ) ? $room['price'] : 0;
                            $room_adult_price = !empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
                            $room_child_price = !empty( $room['child_price'] ) ? $room['child_price'] : 0;
                            $total_person     = $adult_number + $child_number;
                            $price            = $pricing_by == '1' ? $room_price : $room_adult_price + $room_child_price;
                            $form_check_out = $form_end;

                            // Check availability by date option
                            $period = new DatePeriod(
                                new DateTime( $form_start . ' 00:00' ),
                                new DateInterval( 'P1D' ),
                                new DateTime( $form_end . ' 23:59' )
                            );

                            $durationdate = [];
                            foreach ( $period as $date ) {
                                $durationdate[$date->format( 'Y/m/d')] = $date->format( 'Y/m/d');
                            }

                            $days = iterator_count( $period );
                            if($days==7){
                            $roompackageprice = $room['tf-8-days'];
                            
                            $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                            } elseif($days==14){
                                $roompackageprice = $room['tf-16-days'];
                                $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                            } elseif($days==21){
                                $roompackageprice = $room['tf-24-days'];
                                $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                            } elseif($days==28){
                                $roompackageprice = $room['tf-32-days'];
                                $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                            }

                            /**
                             * Set room availability
                             */
                            $unique_id          = !empty($room['unique_id']) ? $room['unique_id'] : '';
                            $order_ids          = !empty($room['order_id']) ? $room['order_id'] : '';
                            $reduce_num_room    = !empty($room['reduce_num_room']) ? $room['reduce_num_room'] : '';
                            $number_orders      = '0';
                            $avil_by_date       = !empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : '';      // Room Available by date enabled or  not ?
                            if($avil_by_date) {
                                $repeat_by_date = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                            }else{
                                $num_room_available = !empty($room['num-room']) ? $room['num-room'] : 1;
                            }

                            if( !empty( $order_ids ) && defined( 'TF_PRO' ) && $reduce_num_room == true ) {

                                # Get backend available date range as an array
                                if ( $avil_by_date ) {

                                    $order_date_ranges = array();

                                    $backend_date_ranges = array();
                                    foreach($repeat_by_date as $single_date_range) {

                                        array_push( $backend_date_ranges, array( strtotime( $single_date_range["availability"]["from"] ), strtotime( $single_date_range["availability"]["to"] ) ) );

                                    }
                                }
                                
                                # Convert order ids to array
                                $order_ids = explode(',', $order_ids);

                                # Run foreach loop through oder ids
                                foreach( $order_ids as $order_id ) {

                                    # Get $order object from order ID
                                    $order = wc_get_order( $order_id );

                                    # Get Only the completed orders
                                    if ( $order && $order->get_status() == 'completed' ) {

                                        # Get and Loop Over Order Items
                                        foreach ( $order->get_items() as $item_id => $item ) {

                                            /**
                                             * Order item data
                                             */                                          
                                            $ordered_number_of_room = $item->get_meta( 'number_room_booked', true );

                                            if ( $avil_by_date ) { 
                                                $order_check_in_date = strtotime( $item->get_meta( 'check_in', true ) );
                                                $order_check_out_date = strtotime( $item->get_meta( 'check_out', true ) );

                                                foreach($repeat_by_date as $single_date_range) {
                                                    
                                                    
                                                    $startdatesearch = array_search($single_date_range["availability"]["from"],$durationdate,true);
                                                    $enddatesearch = array_search($single_date_range["availability"]["to"],$durationdate,true);

                                                    if( !empty($startdatesearch) || !empty($enddatesearch) ) {
                                                        
                                                        $num_room_available = !empty($single_date_range['room_number']) ? $single_date_range['room_number'] : 1;
                                                        
                                                        $startorderdatesearch = array_search($item->get_meta( 'check_in', true ),$durationdate,true);
                                                        $enddateordersearch = array_search($item->get_meta( 'check_out', true ),$durationdate,true);
                                                        if( !empty($startorderdatesearch) || !empty($enddateordersearch) ) {
                                                        $number_orders = $number_orders + $ordered_number_of_room;
                                                        }
                                                                                                        
                                                    }
            
                                                }

                                                array_push( $order_date_ranges, array( $order_check_in_date, $order_check_out_date ) );

                                            } else {

                                                # Total number of room booked
                                                $number_orders = $number_orders + $ordered_number_of_room;

                                            }

                                        }
                                    }
                                }   
                                
                                if(!empty($num_room_available)){
                                    # Calculate available room number after order
                                    $num_room_available = $num_room_available - $number_orders; // Calculate
                                    $num_room_available = max($num_room_available, 0); // If negetive value make that 0
                                }else{
                                    $num_room_available = !empty($room['num-room']) ? $room['num-room'] : 1;
                                    $num_room_available = $num_room_available - $number_orders; // Calculate
                                    $num_room_available = max($num_room_available, 0); 
                                }
 
                            }

                            if ( $avil_by_date && defined( 'TF_PRO' ) ) {

                                // split date range
                                $check_in       = strtotime( $form_start . ' 00:00' );
                                $check_out      = strtotime( $form_end . ' 23:59' );
                                $price          = 0;
                                $has_room       = [];
                                
                                // extract price from available room options
                                foreach ( $period as $date ) {
                                  
                                    $available_rooms = array_values( array_filter( $repeat_by_date, function ($date_availability ) use ( $date ) {

                                        $date_availability_from = strtotime( $date_availability['availability']['from'] . ' 00:00' );
                                        $date_availability_to   = strtotime( $date_availability['availability']['to'] . ' 23:59' );

                                        // return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
                                        // return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;


                                        return  $date_availability["availability"]["from"] == $date->format( 'Y/m/d') || $date_availability["availability"]["to"] == $date->format( 'Y/m/d');

                                    } ) );
                                    
                                    if($available_rooms){
                                    
                                    // var_dump(count( $available_rooms ));
                                    if ( is_iterable($available_rooms) && count( $available_rooms ) >=1) {
                                        
                                        $room_price    = !empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_price;
                                        $adult_price   = !empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_adult_price;
                                        $child_price   = !empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room['child_price'];
                                        $price_by_date = $pricing_by == '1' ? $room_price : (  ( $adult_price * $form_adult ) + ( $child_price * $form_child ) );
                                        $price += $price_by_date;

                                        if($days==7){
                                        $roompackageprice = $room['tf-8-days'];
                                        
                                        $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                        } elseif($days==14){
                                            $roompackageprice = $room['tf-16-days'];
                                            $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                        } elseif($days==21){
                                            $roompackageprice = $room['tf-24-days'];
                                            $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                        } elseif($days==28){
                                            $roompackageprice = $room['tf-32-days'];
                                            $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                        }
                                        
                                        $num_room_available = !empty($available_rooms[0]['num-room']) ? $available_rooms[0]['num-room'] : $room['num-room'];        

                                        $has_room[] = 1; 

                                    } else $has_room[] = 0;
                                }

                                }

                                // Check if date is provided and within date range
                                if ( !in_array( 0, $has_room )  ) {
	                                tf_get_deposit_amount($room, $price, $deposit_amount, $has_deposit);

                                    if ( $form_total_person <= $total_person ) {

                                        include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';

                                    } else {

                                        $error = __( 'No Room Available! Total person number exceeds!', 'tourfic' );
                                    }

                                } else {

                                    $error = __( 'No Room Available within this Date Range!', 'tourfic' );

                                }

                            } else {
                   
                                if($days==7){
                                $roompackageprice = $room['tf-8-days'];
                                
                                $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                } elseif($days==14){
                                    $roompackageprice = $room['tf-16-days'];
                                    $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                } elseif($days==21){
                                    $roompackageprice = $room['tf-24-days'];
                                    $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                } elseif($days==28){
                                    $roompackageprice = $room['tf-32-days'];
                                    $price = ($roompackageprice['tf-room']+$roompackageprice['tf-breakfast']+$roompackageprice['tf-half-b']+$roompackageprice['tf-full-b'])*$form_adult;
                                }

                                tf_get_deposit_amount($room, $price, $deposit_amount, $has_deposit);
                                
                                if ( $form_total_person <= $total_person ) {

                                    include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';

                                } else {

                                    $error = __( 'No Room Available! Total person number exceeds!', 'tourfic' );

                                } 

                            }
                
                        } else {

                            $error = __( 'No Room Available!', 'tourfic' );
                            
                        }
                    }

                    $rows .= ob_get_clean();

                } else {

                    $error = __( 'No Room Available!', 'tourfic' );

                } 

            if ( !empty( $rows  ) ) {

                echo $rows . '</tbody> </table> </div>';

            } else {

                echo sprintf( "<tr><td colspan=\"4\" style=\"text-align:center;font-weight:bold;\">%s</td></tr>", __( $error, "tourfic" ) ).'</tbody> </table> </div>';

            }

    wp_die();
}


                #################################
# All the forms                 #
# Search form, booking form     #
#################################

/**
 * Hotel Search form
 * 
 * Horizontal
 * 
 * Called in shortcodes
 */
if ( !function_exists('tf_hotel_search_form_horizontal') ) {
    function tf_hotel_search_form_horizontal( $classes, $title, $subtitle ){
        if ( isset( $_GET ) ) {
            $_GET = array_map( 'stripslashes_deep', $_GET );
        }
        // location
        $location = !empty($_GET['place']) ? sanitize_text_field($_GET['place']) : '';
        // Adults
        $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
        // children
        $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
        // room
        $room = !empty($_GET['room']) ? sanitize_text_field($_GET['room']) : '';
        // Check-in & out date
        $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';
        
        ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">

        <?php if( $title ): ?>
            <div class="tf_widget-title"><h2><?php esc_html_e( $title ); ?></h2></div>
        <?php endif; ?>

        <?php if( $subtitle ): ?>
            <div class="tf_widget-subtitle"><?php esc_html_e( $subtitle ); ?></div>
        <?php endif; ?>


    <div class="tf_homepage-booking">
        <div class="tf_destination-wrap">
            <div class="tf_input-inner">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <span class="tf-label"><?php _e('Location', 'tourfic'); ?>:</span>
                        <div class="tf_form-inner tf-d-g">
                            <i class="fas fa-search"></i>
                            <input type="text" required="" id="tf-location" class="" placeholder="<?php _e('Enter Location', 'tourfic'); ?>" value="">
                            <input type="hidden" name="place" class="tf-place-input">
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="tf_selectperson-wrap">

            <div class="tf_input-inner">
                <span class="tf_person-icon">
                    <i class="fas fa-user"></i>
                </span>
                <div class="adults-text"><?php echo (!empty($adults) ? $adults : '1') . ' ' . __('Adults', 'tourfic'); ?></div>
                <div class="person-sep"></div>
                <div class="child-text"><?php echo (!empty($child) ? $child : '0') . ' ' . __('Children', 'tourfic'); ?></div>
                <div class="person-sep"></div>
                <div class="room-text"><?php echo (!empty($room) ? $room : '1') . ' ' . __('Room', 'tourfic'); ?></div>
            </div>

            <div class="tf_acrselection-wrap">
                <div class="tf_acrselection-inner">
                    <div class="tf_acrselection">
                        <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="1" value="<?php echo !empty($adults) ? $adults : '1'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                    <div class="tf_acrselection">
                        <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="children" id="children" min="0" value="<?php echo !empty($child) ? $child : '0'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                    <div class="tf_acrselection">
                        <div class="acr-label">1 <?php _e('Rooms', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="room" id="room" min="1" value="<?php echo !empty($room) ? $room : '1'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="tf_selectdate-wrap">
            <div class="tf_input-inner">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <span class="tf-label"><?php _e('Check-in & Check-out date', 'tourfic'); ?></span>
                        <div class="tf_form-inner tf-d-g">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e('Check-in - Check-out', 'tourfic'); ?>" <?php echo tfopt('date_hotel_search')? 'required' : ''; ?>>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="tf_submit-wrap">
            <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>		
            <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
        </div>

    </div>

    </form>

    <script>
    (function($) {
        $(document).ready(function() {

            $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                onReady: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
                onChange: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
                defaultDate: <?php echo json_encode(explode('-', $check_in_out)) ?>,
            });

        });
    })(jQuery);
    </script>
    <?php
    }
}



/**
 * Hotel Advance Search form
 * 
 * Horizontal
 * 
 * Called in shortcodes
 */
if ( !function_exists('tf_hotel_advanced_search_form_horizontal') ) {
    function tf_hotel_advanced_search_form_horizontal( $classes, $title, $subtitle ){
        if ( isset( $_GET ) ) {
            $_GET = array_map( 'stripslashes_deep', $_GET );
        }
        // location
        $location = !empty($_GET['place']) ? sanitize_text_field($_GET['place']) : '';
        // Adults
        $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
        // children
        $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
        // room
        $room = !empty($_GET['room']) ? sanitize_text_field($_GET['room']) : '';
        // Check-in & out date
        $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';
        
        ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">

        <?php if( $title ): ?>
            <div class="tf_widget-title"><h2><?php esc_html_e( $title ); ?></h2></div>
        <?php endif; ?>

        <?php if( $subtitle ): ?>
            <div class="tf_widget-subtitle"><?php esc_html_e( $subtitle ); ?></div>
        <?php endif; ?>

    <?php 
    if ( !defined( 'TF_PRO' ) ){ ?>
    <div class="tf_homepage-booking">
        <div class="tf_destination-wrap">
            <div class="tf_input-inner">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <span class="tf-label"><?php _e('Location', 'tourfic'); ?>:</span>
                        <div class="tf_form-inner tf-d-g">
                            <i class="fas fa-search"></i>
                            <input type="text" name="place" required id="tf-destination-adv" class="tf-advance-destination" placeholder="<?php _e('Enter Location', 'tourfic'); ?>" value="">               
                            <div class="ui-widget ui-widget-content results tf-hotel-results tf-hotel-adv-results">
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="tf_selectperson-wrap">

            <div class="tf_input-inner">
                <span class="tf_person-icon">
                    <i class="fas fa-user"></i>
                </span>
                <div class="adults-text"><?php echo (!empty($adults) ? $adults : '1') . ' ' . __('Adults', 'tourfic'); ?></div>
                <div class="person-sep"></div>
                <div class="child-text"><?php echo (!empty($child) ? $child : '0') . ' ' . __('Children', 'tourfic'); ?></div>
                <div class="person-sep"></div>
                <div class="room-text"><?php echo (!empty($room) ? $room : '1') . ' ' . __('Room', 'tourfic'); ?></div>
            </div>

            <div class="tf_acrselection-wrap">
                <div class="tf_acrselection-inner">
                    <div class="tf_acrselection">
                        <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="1" value="<?php echo !empty($adults) ? $adults : '1'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                    <div class="tf_acrselection">
                        <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="children" id="children" min="0" value="<?php echo !empty($child) ? $child : '0'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                    <div class="tf_acrselection">
                        <div class="acr-label">1 <?php _e('Rooms', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="room" id="room" min="1" value="<?php echo !empty($room) ? $room : '1'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="tf_selectdate-wrap">
            <div class="tf_input-inner">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <span class="tf-label"><?php _e('Check-in & Check-out date', 'tourfic'); ?></span>
                        <div class="tf_form-inner tf-d-g">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e('Check-in - Check-out', 'tourfic'); ?>" <?php echo tfopt('date_hotel_search')? 'required' : ''; ?>>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="tf_selectdate-wrap tf_more_info_selections">
            <div class="tf_input-inner">
                <label class="tf_label-row" style="width: 100%;">
                    <span class="tf-label"><?php _e('More', 'tourfic'); ?></span>
                    <span style="text-decoration: none; display: block; cursor: pointer;"><?php _e('Filter', 'tourfic'); ?>  <i class="fas fa-angle-down"></i></a>
                </label>
            </div>
            <div class="tf-more-info">
            <span><?php _e('Filter Price', 'tourfic'); ?></span>
            <div class="tf-filter-price-range">
                <div class="tf-hotel-filter-range"></div>
            </div>

            <span><?php _e('Hotel Features', 'tourfic'); ?></span>
                <?php
                $tf_hotelfeature = get_terms( array(
                    'taxonomy' => 'hotel_feature',
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'hide_empty' => true,
                    'hierarchical' => 0,
                ) );
                if ( $tf_hotelfeature ) { ?>
                <?php foreach( $tf_hotelfeature as $term ) { ?>
                    <div class="form-group form-check">
                        <input type="checkbox" name="features[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                        <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                    </div>
                <?php } } ?>
            </div>
        </div>

        <div class="tf_submit-wrap">
            <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>		
            <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
        </div>

    </div>
    <?php }else{ ?>
    
        <div class="tf_homepage-booking">

        <div class="tf_destination-wrap">
            <div class="tf_input-inner">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner tf-d-g">
                            <i class="fas fa-search"></i>
                            <input type="text" name="country" required id="tf-country-name" class="tf-advance-destination" placeholder="<?php _e('Enter Country', 'tourfic'); ?>" value="">               
                            <div class="ui-widget ui-widget-content results tf-hotel-results tf-hotel-adv-results">
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="tf_destination-wrap">
            <div class="tf_input-inner">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner tf-d-g">
                            <i class="fas fa-search"></i>
                            <input type="text" name="month" required id="tf-month-name" class="tf-advance-destination" placeholder="<?php _e('Enter Month', 'tourfic'); ?>" value="">               
                            <div class="ui-widget ui-widget-content results tf-hotel-results tf-hotel-month-results">
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="tf_submit-wrap">
            <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>		
            <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
        </div>

    </div>

    <?php } ?>
    </form>

    <script>
    (function($) {
        $(document).ready(function() {

            $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                onReady: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
                onChange: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
                defaultDate: <?php echo json_encode(explode('-', $check_in_out)) ?>,
            });

        });
    })(jQuery);
    </script>
    <?php
    }
}

/**
 * Single Hotel Sidebar Booking Form
 */
function tf_hotel_sidebar_booking_form($b_check_in='',$b_check_out='') {
   
    if ( isset( $_GET ) ) {
        $_GET = array_map( 'stripslashes_deep', $_GET );
    }
    
    // get post id
    $post_id = get_the_ID();

    // Get Tour Meta
    $meta = get_post_meta( $post_id,'tf_hotel',true );

    // Adults
    $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
    // children
    $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
    // Check-in & out date
    $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';
    $eheckinout_date = isset($meta['tf-ct-checkinout']) ? $meta['tf-ct-checkinout'] : '';

    $maxadults = [];
    $hotels_rooms = !empty($meta['room']) ? $meta['room'] : '';
    foreach($hotels_rooms as $singleroom){
        $maxadults[]=$singleroom['adult'];
    }

    if(!empty($maxadults)){
        $max_adults_numbers = max($maxadults);
    }else{
        $max_adults_numbers = 1;
    }
	
    $hotel_month  = !empty(get_the_terms( $post_id, 'hotel_month' )) ? get_the_terms( $post_id, 'hotel_month' ) : '';

    ?>

    <!-- Start Booking widget -->
    <form id="tf-single-hotel-avail" class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off">
    
        <?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
                        <?php                        
                        foreach (range(1,$max_adults_numbers) as $value) {
                            $selected = $value == $adults ? 'selected' : null;
                            echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __("Adults", "tourfic") . '</option>';
                        }
                        ?>
                        
                    </select>
                </div>
            </label>
        </div>
    
        <div class="tf_booking-dates">
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <span class="tf-label"><?php _e('Check-in &amp; Check-out date', 'tourfic'); ?></span>
                    <div class="tf_form-inner">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                            placeholder="<?php _e('Select Date', 'tourfic'); ?>" <?php echo !empty($check_in_out) ? 'value="' . $check_in_out . '"' : '' ?> required>
                    </div>
                </label>
            </div>
        </div>
    
        <div class="tf_form-row">
            <?php
                    $ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();
                ?>
            <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type" />
            <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>" />
            <div class="tf-btn"><button class="tf_button tf-submit btn-styled"
                type="submit"><?php esc_html_e( 'Booking Availability', 'tourfic' );?></button></div>


        </div>
    
    </form>
    
    <script>
        (function($) {
            $(document).ready(function() {

                const checkinoutdateange = flatpickr(".tf-hotel-side-booking #check-in-out-date", {
                    enableTime: false,
                    mode: "range",
                    dateFormat: "Y/m/d",
                    minDate: "today",
                    onReady: function(selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                    },
                    enable: [
                        function(date) {
                            <?php 
                            if (!empty($hotel_month)) {
                                $all_months = [
                                    'Enero', 
                                    'Febrero', 
                                    'March', 
                                    'Abril', 
                                    'Mayo', 
                                    'Junio', 
                                    'Julio', 
                                    'Agosto', 
                                    'Septiembre', 
                                    'Octubre', 
                                    'Noviembre', 
                                    'Diciembre'
                                ];
                                // Generate an array of allowed months
                                $allowed_months = [];
                                foreach ($hotel_month as $month) {
                                    $month_name = $month->name;
                                    $index = array_search($month_name, $all_months); // Find the index in $all_months
                                    if ($index !== false) { // Check if the month is found
                                        $allowed_months[] = $index; // Use the index (zero-based)
                                    }
                                }
                                // Get the allowed day of the week (0-6, Sunday-Saturday)
                                $allowed_day = isset($eheckinout_date) ? intval($eheckinout_date) : null;
                            ?>
                            const allowedMonths = <?php echo json_encode($allowed_months); ?>;
                            const allowedDay = <?php echo json_encode($allowed_day); ?>;
                            // Check if the month and day match the allowed criteria
                            return allowedMonths.includes(date.getMonth()) && date.getDay() === allowedDay;
                            <?php } else { ?>
                            return false; // No dates allowed if $hotel_month is empty
                            <?php } ?>
                        }
                    ],
                    defaultDate: <?php echo json_encode(explode('-', $check_in_out)); ?>,
                    <?php tf_flatpickr_locale(); // Flatpickr locale for translation ?>
                });

            });
        })(jQuery);
    </script>


    
    <?php if ( is_active_sidebar( 'tf_single_booking_sidebar' ) ) { ?>
        <div id="tf__booking_sidebar">
            <?php dynamic_sidebar( 'tf_single_booking_sidebar' ); ?>
            <br>
        </div>
    <?php }
}

#################################
# Layouts                       #
#################################

/**
 * Hotel Archive Single Item Layout
 */
function tf_hotel_archive_single_item($tf_stars='') {

    // get post id
    $post_id = get_the_ID();
    //Get hotel_feature
    $features = !empty(get_the_terms( $post_id, 'hotel_feature' )) ? get_the_terms( $post_id, 'hotel_feature' ) : '';
    $meta  = get_post_meta( $post_id, 'tf_hotel', true );
    // Location
    $address  = !empty($meta['address']) ? $meta['address'] : '';
    // Rooms
    $b_rooms = !empty($meta['room']) ? $meta['room'] : '';

    /**
     * All values from URL
     */
    // Adults
    if(empty($adults)) {
        $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
    }
    // children
    if(empty($child)) {
        $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
    }
    // room
    if(empty($room)) {
        $room = !empty($_GET['room']) ? sanitize_text_field($_GET['room']) : '';
    }
    // Check-in & out date
    if(empty($check_in_out)) {
        $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';
    }
    if ($check_in_out) {
        $form_check_in = substr($check_in_out,0,10);
        $form_check_in_stt = strtotime($form_check_in);
        $form_check_out = substr($check_in_out,14,10);
        $form_check_out_stt = strtotime($form_check_out);
    }

    // Single link
    $url = get_the_permalink() . '?adults=' . ($adults ?? '') . '&children=' . ($child ?? '') . '&room=' . ($room ?? '') . '&check-in-out-date=' . ($check_in_out ?? '');

    ?>


        <div class="single-tour-wrap">
            <div class="single-tour-inner">
                <div class="tourfic-single-left">
                    <a href="<?php echo $url; ?>">
                    <?php
                    if ( has_post_thumbnail() ){ 
                        the_post_thumbnail( 'full' );
                    } else {
                        echo '<img width="100%" height="100%" src="' .TF_ASSETS_URL . "img/img-not-available.svg". '" class="attachment-full size-full wp-post-image">';
                    }
                    ?>
                    </a>
                </div>
                <div class="tourfic-single-right">
                    <div class="tf_property_block_main_row">
                        <div class="tf_item_main_block">
                            <div class="tf-hotel__title-wrap">
                                <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php the_title();?></h3></a>
                            </div>			
                            <?php
                            if($address) {
                                echo '<div class="tf-map-link">';
                                echo '<span class="tf-d-ib"><a href="https://www.google.com/maps/search/'.$address.'" target="_blank">Show on Map </a> <i class="fas fa-map-marker-alt"></i> ' .$address. '</span>';
                                echo '</div>';
                            }
                            ?>	                    
                        </div>
                        <?php tf_archive_single_rating();?>
                    </div>
                    
                    <div class="sr_rooms_table_block">
                        <div class="room_details">
                            <div class="featuredRooms">
                                
                                <div class="roomNameInner">
                                    <div class="room_link">
										<?php if( $features ) { ?>
                                            <div class="roomName_flex">
                                                <ul class="tf-archive-desc">
                                                    <?php foreach($features as $feature) {
                                                    $feature_meta = get_term_meta( $feature->term_id, 'hotel_feature', true );
                                                    $f_icon_type = !empty($feature_meta['icon-type']) ? $feature_meta['icon-type'] : '';
                                                    if ($f_icon_type == 'fa' && !empty($feature_meta['icon-fa'])) {
                                                        $feature_icon = '<i class="' .$feature_meta['icon-fa']. '"></i>';
                                                    } elseif ($f_icon_type == 'c' && !empty($feature_meta['icon-c'])) {
                                                        $feature_icon = '<img src="' .$feature_meta['icon-c']["url"]. '" style="width: ' .$feature_meta['dimention']["width"]. 'px; height: ' .$feature_meta['dimention']["width"]. 'px;" />';
                                                    } 
                                                    if(!empty($feature_icon)){
                                                    ?>
                                                    <li class="tf-tooltip">
                                                        <?php echo $feature_icon; ?>
                                                        <div class="tf-top">
                                                            <?php echo $feature->name; ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </li>
                                                    <?php }} ?>
                                                </ul>
                                            </div>
                                            <?php } ?>
										
                                        <div class="roomrow_flex">
											<div class="roomrow-price-details">
                                            <?php 
                                            $tf_8_min_price = [];
                                            $tf_16_min_price = [];
                                            $tf_24_min_price = [];
                                            $tf_32_min_price = [];
                                            foreach($b_rooms as $sroom){
                                                $tf8days  = !empty($sroom['tf-8-days']) ? $sroom['tf-8-days'] : ''; 
                                                $tf16days  = !empty($sroom['tf-16-days']) ? $sroom['tf-16-days'] : ''; 
                                                $tf24days  = !empty($sroom['tf-24-days']) ? $sroom['tf-24-days'] : ''; 
                                                $tf32days  = !empty($sroom['tf-32-days']) ? $sroom['tf-32-days'] : ''; 
                                                
                                                // 8 Days
                                                if(!empty($tf8days['tf-room'])){
                                                    $tf_8_min_price[]=$tf8days['tf-room'];
                                                }
                                                if(!empty($tf8days['tf-breakfast'])){
                                                    $tf_8_min_price[]=$tf8days['tf-breakfast'];
                                                }
                                                if(!empty($tf8days['tf-half-b'])){
                                                    $tf_8_min_price[]=$tf8days['tf-half-b'];
                                                }
                                                if(!empty($tf8days['tf-full-b'])){
                                                    $tf_8_min_price[]=$tf8days['tf-full-b'];
                                                }
                                                if(!empty($tf8days['tf-inclusive'])){
                                                    $tf_8_min_price[]=$tf8days['tf-inclusive'];
                                                }
                                                if(!empty($tf8days['tf-inclusive-gold'])){
                                                    $tf_8_min_price[]=$tf8days['tf-inclusive-gold'];
                                                }

                                                // 16 Days
                                                if(!empty($tf16days['tf-room'])){
                                                    $tf_16_min_price[]=$tf16days['tf-room'];
                                                }
                                                if(!empty($tf16days['tf-breakfast'])){
                                                    $tf_16_min_price[]=$tf16days['tf-breakfast'];
                                                }
                                                if(!empty($tf16days['tf-half-b'])){
                                                    $tf_16_min_price[]=$tf16days['tf-half-b'];
                                                }
                                                if(!empty($tf16days['tf-full-b'])){
                                                    $tf_16_min_price[]=$tf16days['tf-full-b'];
                                                }
                                                if(!empty($tf16days['tf-inclusive'])){
                                                    $tf_16_min_price[]=$tf16days['tf-inclusive'];
                                                }
                                                if(!empty($tf16days['tf-inclusive-gold'])){
                                                    $tf_16_min_price[]=$tf16days['tf-inclusive-gold'];
                                                }

                                                // 24 Days
                                                if(!empty($tf24days['tf-room'])){
                                                    $tf_24_min_price[]=$tf24days['tf-room'];
                                                }
                                                if(!empty($tf24days['tf-breakfast'])){
                                                    $tf_24_min_price[]=$tf24days['tf-breakfast'];
                                                }
                                                if(!empty($tf24days['tf-half-b'])){
                                                    $tf_24_min_price[]=$tf24days['tf-half-b'];
                                                }
                                                if(!empty($tf24days['tf-full-b'])){
                                                    $tf_24_min_price[]=$tf24days['tf-full-b'];
                                                }
                                                if(!empty($tf24days['tf-inclusive'])){
                                                    $tf_24_min_price[]=$tf24days['tf-inclusive'];
                                                }
                                                if(!empty($tf24days['tf-inclusive-gold'])){
                                                    $tf_24_min_price[]=$tf24days['tf-inclusive-gold'];
                                                }

                                                // 32 Days
                                                if(!empty($tf32days['tf-room'])){
                                                    $tf_32_min_price[]=$tf32days['tf-room'];
                                                }
                                                if(!empty($tf32days['tf-breakfast'])){
                                                    $tf_32_min_price[]=$tf32days['tf-breakfast'];
                                                }
                                                if(!empty($tf32days['tf-half-b'])){
                                                    $tf_32_min_price[]=$tf32days['tf-half-b'];
                                                }
                                                if(!empty($tf32days['tf-full-b'])){
                                                    $tf_32_min_price[]=$tf32days['tf-full-b'];
                                                }
                                                if(!empty($tf32days['tf-inclusive'])){
                                                    $tf_32_min_price[]=$tf32days['tf-inclusive'];
                                                }
                                                if(!empty($tf32days['tf-inclusive-gold'])){
                                                    $tf_32_min_price[]=$tf32days['tf-inclusive-gold'];
                                                }

                                            }
                                            if( !empty($tf_8_min_price) ){
                                                $hotel_8_min_price = min($tf_8_min_price);
                                            }
                                            if( !empty($tf_16_min_price) ){
                                                $hotel_16_min_price = min($tf_16_min_price);
                                            }
                                            if( !empty($tf_24_min_price) ){
                                                $hotel_24_min_price = min($tf_24_min_price);
                                            }
                                            if( !empty($tf_32_min_price) ){
                                                $hotel_32_min_price = min($tf_32_min_price);
                                            }
                                            ?>
                                            <ul>
                                                <?php 
                                                if( !empty($hotel_8_min_price) ){ ?>
                                                <li><?php _e("8 DAYS stay: Starting on ","tourfic"); ?> <?php echo wc_price($hotel_8_min_price); ?> </li>
                                                <?php } ?>

                                                <?php 
                                                if( !empty($hotel_16_min_price) ){ ?>
                                                <li><?php _e("16 DAYS stay: Starting on ","tourfic"); ?> <?php echo wc_price($hotel_16_min_price); ?> </li>
                                                <?php } ?>

                                                <?php 
                                                if( !empty($hotel_24_min_price) ){ ?>
                                                <li><?php _e("24 DAYS stay: Starting on ","tourfic"); ?> <?php echo wc_price($hotel_24_min_price); ?> </li>
                                                <?php } ?>

                                                <?php 
                                                if( !empty($hotel_32_min_price) ){ ?>
                                                <li><?php _e("32 DAYS stay: Starting on ","tourfic"); ?> <?php echo wc_price($hotel_32_min_price); ?> </li>
                                                <?php } ?>
                                            </ul>

                                        </div>  
                                            
                                            <div class="roomPrice roomPrice_flex sr_discount">
                                                <div class="availability-btn-area">
                                                    <a href="<?php echo $url; ?>" class="tf_button btn-styled"><?php esc_html_e( 'See Availability', 'tourfic' );?> <i class="fas fa-angle-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}

/**
 * Filter hotels on search result page by checkin checkout dates set by backend
 *
 *
 * @author devkabir, fida
 *
 * @param DatePeriod $period    collection of dates by user input;
 * @param array      $not_found collection of hotels exists
 * @param array      $data      user input for sidebar form
 */
function tf_filter_hotel_by_date( $period, array &$not_found, array $data = [] ): void {

    // Form Data
    [$adults, $child, $room, $check_in_out, $tf_month_pricing] = $data;
    // Get hotel meta options
    $meta = get_post_meta(get_the_ID(), 'tf_hotel', true);

    $selected_months = get_the_terms( get_the_ID(), 'hotel_month' );

    $months_in_spanish = [
        "January" => "Enero",
        "February" => "Febrero",
        "March" => "March",
        "April" => "Abril",
        "May" => "Mayo",
        "June" => "Junio",
        "July" => "Julio",
        "August" => "Agosto",
        "September" => "Septiembre",
        "October" => "Octubre",
        "November" => "Noviembre",
        "December" => "Diciembre"
    ];

    // Extract months from $check_in_out
    preg_match_all('/\d{4}\/(\d{2})\/\d{2}/', $check_in_out, $matches);
    $check_in_out_months = array_unique(array_map(function($month) use ($months_in_spanish) {
        $lmonth = date('F', mktime(0, 0, 0, intval($month), 10)); // Get full month name
        return $months_in_spanish[$lmonth] ?? $lmonth; // Translate to Spanish or return original
    }, $matches[1]));

    $has_hotel = false;

    if (!empty($selected_months)) {
        $selected_month_names = array_map(function($term) {
            return $term->name; // Extract term names
        }, $selected_months);

        $exists = !array_diff($check_in_out_months, $selected_month_names); // Check if all months exist
    
        if ($exists) {
            if(empty($tf_month_pricing)){
                $has_hotel = true;
            }else{
                if(!empty($meta['room'])){
                    foreach($meta['room'] as $room){
                        $tf_full_month_price = $room["tf-32-days"];

                        $tf_room = $room["tf-32-days"]["tf-room"];
                        $tf_breakfast = $room["tf-32-days"]["tf-breakfast"];
                        $tf_half_b = $room["tf-32-days"]["tf-half-b"];
                        $tf_full_b = $room["tf-32-days"]["tf-full-b"];
                        $tf_inclusive = $room["tf-32-days"]["tf-inclusive"];
                        $tf_inclusive_gold = $room["tf-32-days"]["tf-inclusive-gold"];

                        if (filter_pricing($tf_month_pricing, $tf_room)) {
                            $has_hotel = true;
                        }elseif (filter_pricing($tf_month_pricing, $tf_breakfast)) {
                            $has_hotel = true;
                        }elseif (filter_pricing($tf_month_pricing, $tf_half_b)) {
                            $has_hotel = true;
                        }elseif (filter_pricing($tf_month_pricing, $tf_full_b)) {
                            $has_hotel = true;
                        }elseif (filter_pricing($tf_month_pricing, $tf_inclusive)) {
                            $has_hotel = true;
                        }elseif (filter_pricing($tf_month_pricing, $tf_inclusive_gold)) {
                            $has_hotel = true;
                        } else {
                            $has_hotel = false;
                        }
                    }
                }
            }
        } else {
            $has_hotel = false;
        }
    } else {
        $has_hotel = false;
    }

    // Conditional hotel showing
    if ( $has_hotel ) {
       
        tf_hotel_archive_single_item($tf_stars);

        $not_found[] = 0;

    } else {

        $not_found[] = 1;
        
    }

}

// Function to check if a value matches the pricing filters
function filter_pricing($pricing, $full_month_value) {
    foreach ($pricing as $range) {
        if (strpos($range, '-') !== false) {
            // Handle range (e.g., "1000-1500")
            [$min, $max] = explode('-', $range);
            if ($full_month_value >= $min && $full_month_value <= $max) {
                return true;
            }
        } else {
            
            if($range==1000){
                if ($full_month_value <= $range) {
                    return true;
                }
            }
            if($range==2000){
                if ($full_month_value >= $range) {
                    return true;
                }
            }
            
        }
    }
    return false;
}

/**
 * Remove room order ids
 */
function tf_remove_order_ids_from_room() {
    echo '
    <div class="csf-title">
        <h4>' .__("Reset Room Availability", "tourfic"). '</h4>
        <div class="csf-subtitle-text">' .__("Remove order ids linked with this room.<br><b style='color: red;'>Be aware! It is irreversible!</b>", "tourfic"). '</div>
    </div>
    <div class="csf-fieldset">
        <button type="button" class="button button-large csf-warning-primary remove-order-ids">' .__("Reset", "tourfic"). '</button>
    </div>
    <div class="clear"></div>
    ';
}

/**
 * Ajax remove room order ids
 */
add_action( 'wp_ajax_tf_remove_room_order_ids', 'tf_remove_room_order_ids' );
function tf_remove_room_order_ids() {

    # Get order id field's name
    $meta_field = isset( $_POST['meta_field'] ) ? sanitize_text_field( $_POST['meta_field'] ) : '';
    # Trim room id from order id name
    $room_id = trim($meta_field, "tf_hotel[room][][order_id");
    # Get post id
    $post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
    # Get hotel meta
    $meta = get_post_meta( $post_id, 'tf_hotel', true );

    # Set order id field's value to blank
    $meta['room'][$room_id]['order_id'] = '';

    # Update whole hotel meta
    update_post_meta( $post_id, 'tf_hotel', $meta );

    # Send success message
    wp_send_json_success( __( 'Order ids have been removed!', 'tourfic' ) );

    wp_die();
}

/**
 * Ajax hotel quick view
 */

function tf_hotel_quickview_callback(){
    ?>
    <div class="tf-hotel-quick-view" style="display: flex">
        <?php 
            $meta = get_post_meta( $_POST['post_id'], 'tf_hotel', true );
            $rooms = !empty($meta['room']) ? $meta['room'] : '';
            foreach ($rooms as $room_id => $room) {
            $enable = !empty($room['enable']) ? $room['enable'] : '';
            if ($enable == '1') {
            $tf_room_gallery = !empty($room['gallery']) ? $room['gallery'] : '';
            if($room['unique_id']==$_POST['uniqid_id'] && $room_id==$_POST['roomid_id']){
        ?>
        <div class="tf-hotel-details-qc-gallelry" style="width: 545px;">
            <?php
            if ($tf_room_gallery) {
                $tf_room_gallery_ids = explode( ',', $tf_room_gallery );
            }
                       
            ?>
                       
            <div class="tf-details-qc-slider tf-details-qc-slider-single">
                <?php 
                if ( !empty( $tf_room_gallery_ids ) ) {
                foreach ($tf_room_gallery_ids as $key => $gallery_item_id) {
                ?>    
				<div class="tf-details-qcs">
                    <?php 
                    $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                    echo '<img src="'.$image_url.'" alt="">';
                    ?>
                </div>
                <?php }} ?>
			</div>
			<div class="tf-details-qc-slider tf-details-qc-slider-nav">
                <?php 
                if ( !empty( $tf_room_gallery_ids ) ) {
                foreach ($tf_room_gallery_ids as $key => $gallery_item_id) {
                ?>    
				<div class="tf-details-qcs">
                    <?php 
                    $image_url = wp_get_attachment_url( $gallery_item_id, 'thumbnail' );
                    echo '<img src="'.$image_url.'" alt="">';
                    ?>
                </div>
                <?php }} ?>
			</div>

            <script>
               jQuery('.tf-details-qc-slider-single').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    fade: false,
                    adaptiveHeight: true,
                    infinite: true,
                    useTransform: true,
                    speed: 400,
                    cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
                });
            
                jQuery('.tf-details-qc-slider-nav')
                    .on('init', function(event, slick) {
                        jQuery('.tf-details-qc-slider-nav .slick-slide.slick-current').addClass('is-active');
                    })
                    .slick({
                        slidesToShow: 7,
                        slidesToScroll: 7,
                        dots: false,
                        focusOnSelect: false,
                        infinite: false,
                        responsive: [{
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 5,
                                slidesToScroll: 5,
                            }
                        }, {
                            breakpoint: 640,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                        }
                        }, {
                            breakpoint: 420,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                    }
                        }]
                    });
            
                    jQuery('.tf-details-qc-slider-single').on('afterChange', function(event, slick, currentSlide) {
                        jQuery('.tf-details-qc-slider-nav').slick('slickGoTo', currentSlide);
                    var currrentNavSlideElem = '.tf-details-qc-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
                    jQuery('.tf-details-qc-slider-nav .slick-slide.is-active').removeClass('is-active');
                    jQuery(currrentNavSlideElem).addClass('is-active');
                });
            
                jQuery('.tf-details-qc-slider-nav').on('click', '.slick-slide', function(event) {
                    event.preventDefault();
                    var goToSingleSlide = jQuery(this).data('slick-index');
            
                    jQuery('.tf-details-qc-slider-single').slick('slickGoTo', goToSingleSlide);
                });
            </script>

        </div>
        <div class="tf-hotel-details-info" style="width:440px; padding-left: 35px;">
            <?php 
            $footage = !empty($room['footage']) ? $room['footage'] : '';
            $bed = !empty($room['bed']) ? $room['bed'] : '';
            $adult_number = !empty($room['adult']) ? $room['adult'] : '0';
            $child_number = !empty($room['child']) ? $room['child'] : '0';
            $num_room = !empty($room['num-room']) ? $room['num-room'] : '0';
            ?>
            <h3><?php echo esc_html( $room['title'] ); ?></h3>
            <p><?php echo $room['description']; ?></p>
            <div class="tf-room-title description">
                <?php if ($num_room) { ?>
                <div class="tf-tooltip tf-d-ib">
                    <div class="room-detail-icon">
                        <span class="room-icon-wrap"><i class="fas fa-person-booth"></i></span>
                        <span class="icon-text tf-d-b"><?php echo $num_room; ?></span>
                    </div>
                    <div class="tf-top">
                        <?php _e( 'No. Room', 'tourfic' ); ?>
                        <i class="tool-i"></i>
                    </div>
                </div>
                <?php }
                if ($footage) { ?>
                <div class="tf-tooltip tf-d-ib">
                    <div class="room-detail-icon">
                        <span class="room-icon-wrap"><i
                                class="fas fa-ruler-combined"></i></span>
                        <span class="icon-text tf-d-b"><?php echo $footage; ?> <?php _e( 'sft', 'tourfic' ); ?></span>
                    </div>
                    <div class="tf-top">
                        <?php _e( 'Room Footage', 'tourfic' ); ?>
                        <i class="tool-i"></i>
                    </div>
                </div>
                <?php }
                if ($bed) { ?>
                <div class="tf-tooltip tf-d-ib">
                    <div class="room-detail-icon">
                        <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                        <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                    </div>
                    <div class="tf-top">
                        <?php _e( 'No. Beds', 'tourfic' ); ?>
                        <i class="tool-i"></i>
                    </div>
                </div>
                <?php } ?>

                <?php if(!empty($room['features'])) { ?>
                <div class="room-features">
                    <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
                    </div>
                    <ul class="room-feature-list" style="margin: 0;">

                        <?php foreach ($room['features'] as $feature) {

                            $room_f_meta = get_term_meta( $feature, 'hotel_feature', true );

                            $room_icon_type = !empty($room_f_meta['icon-type']) ? $room_f_meta['icon-type'] : '';

                            if ($room_icon_type == 'fa') {
                                $room_feature_icon = '<i class="' .$room_f_meta['icon-fa']. '"></i>';
                            } elseif ($room_icon_type == 'c') {
                                $room_feature_icon = '<img src="' .$room_f_meta['icon-c']["url"]. '" style="min-width: ' .$room_f_meta['dimention']["width"]. 'px; height: ' .$room_f_meta['dimention']["width"]. 'px;" />';
                            }

                            $room_term = get_term( $feature ); ?>
                        <li class="tf-tooltip tf-d-ib">
                            <?php echo !empty($room_feature_icon) ? $room_feature_icon : ''; ?>
                            <div class="tf-top">
                                <?php echo $room_term->name; ?>
                                <i class="tool-i"></i>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>
            </div>
            <div class="pax">
                
            <div class="tf-room-title"><h4><?php esc_html_e( 'Pax', 'tourfic' ); ?></h4>
            <?php if ($adult_number) { ?>
            <div class="tf-tooltip tf-d-ib">
                <div class="room-detail-icon">
                    <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                            class="fas fa-female"></i></span>
                    <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                </div>
                <div class="tf-top">
                    <?php _e( 'No. Adults', 'tourfic' ); ?>
                    <i class="tool-i"></i>
                </div>
            </div>
            
            <?php }
            if ($child_number) { ?>
            <div class="tf-tooltip tf-d-ib">
                <div class="room-detail-icon">
                    <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                    <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                </div>
                <div class="tf-top">
                    <?php _e( 'No. Children', 'tourfic' ); ?>
                    <i class="tool-i"></i>
                </div>
            </div>
            <?php } ?>
            </div>
            
        </div>
        <?php 
        }
        }
        } 
        ?>
    </div>
    <?php
    wp_die();
}

add_action( 'wp_ajax_tf_tour_details_qv', 'tf_hotel_quickview_callback' );
add_action( 'wp_ajax_nopriv_tf_tour_details_qv', 'tf_hotel_quickview_callback' );

#################################
# WooCommerce integration       #
#################################
/**
 * WooCommerce hotel Functions
 * 
 * @include
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-hotel.php' ) ) {
    require_once TF_INC_PATH . 'functions/woocommerce/wc-hotel.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/woocommerce/wc-hotel.php');
}

#################################
#           Temporary           #
#################################
/**
 * Add missing unique id to hotel room
 */
function tf_update_missing_room_id() {

    if ( get_option( 'tf_miss_room_id' ) < 1 ) {

        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'tf_hotel',
            'suppress_filters' => true 
        );
        $posts_array = get_posts( $args );
        foreach($posts_array as $post_array) {
            $meta = get_post_meta( $post_array->ID, 'tf_hotel', true );
            $rooms = !empty($meta['room']) ? $meta['room'] : '';
            $new_rooms = [];
            foreach($rooms as $room) {
                
                if(empty($room['unique_id'])) {
                    $room['unique_id']  = mt_rand(1, time());
                }
                $new_rooms[] = $room; 
            }
            $meta['room'] = $new_rooms;
            update_post_meta($post_array->ID, 'tf_hotel', $meta );
        
        }
        update_option( 'tf_miss_room_id', 1 );
    }
}
add_action( 'init', 'tf_update_missing_room_id' );

/**
 * Run Once
 * Add _price post_meta to all hotels & tours
 * 
 * Will be delete in future version
 */
function tf_update_meta_all_hotels_tours() {

    // Run once only
    if ( get_option( 'tf_update_hotel_price' ) < 1 ) {

        // Update hotels meta
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'tf_hotel',
            'suppress_filters' => true 
        );
        $posts_array = get_posts( $args );
        foreach( $posts_array as $post_array ) {
            update_post_meta( $post_array->ID, '_price', '0' );
        } 

        // Update tours meta
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'tf_tours',
            'suppress_filters' => true 
        );
        $posts_array = get_posts( $args );
        foreach( $posts_array as $post_array ) {
            update_post_meta( $post_array->ID, '_price', '0' );
        }

        update_option( 'tf_update_hotel_price', 1 );

    }
}
add_action( 'wp_loaded', 'tf_update_meta_all_hotels_tours' );
?>