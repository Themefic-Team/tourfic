<?php
defined( 'ABSPATH' ) || exit;

#################################
# Custom post types, taxonomies #
#################################

/**
 * Register post type: tf_tours
 * 
 * @since 1.0
 * @return void
 */
function register_tf_tours_post_type() {

    $tour_slug = !empty(get_option( 'tour_slug' )) ? get_option( 'tour_slug' ) : apply_filters( 'tf_tours_slug', 'tours' );

    $tour_labels = apply_filters( 'tf_tours_labels', array(
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
        'menu_name'             => _x( 'Tours', 'tourfic post type menu name', 'tourfic' ),
        'featured_image'        => __( '%1$s Image', 'tourfic' ),
        'set_featured_image'    => __( 'Set %1$s Image', 'tourfic' ),
        'remove_featured_image' => __( 'Remove %1$s Image', 'tourfic' ),
        'use_featured_image'    => __( 'Use as %1$s Image', 'tourfic' ),
        'attributes'            => __( '%1$s Attributes', 'tourfic' ),
        'filter_items_list'     => __( 'Filter %2$s list', 'tourfic' ),
        'items_list_navigation' => __( '%2$s list navigation', 'tourfic' ),
        'items_list'            => __( '%2$s list', 'tourfic' ),
    ) );

    foreach ( $tour_labels as $key => $value ) {
        $tour_labels[$key] = sprintf( $value, tf_tours_singular_label(), tf_tours_plural_label() );
    }

    $tour_args = array(
        'labels'             => $tour_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'menu_icon'          => 'dashicons-location-alt',
        'rewrite'            => array( 'slug' => $tour_slug, 'with_front' => false ),
        'capability_type'    => array( 'tf_tours', 'tf_tourss' ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 26.3,
        'supports'           => apply_filters( 'tf_tours_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
    );

    register_post_type( 'tf_tours', apply_filters( 'tf_tour_post_type_args', $tour_args ) );
}
// Enable/disable check
if(tfopt('disable-services') && in_array('tour', tfopt('disable-services'))) {} else {
    add_action( 'init', 'register_tf_tours_post_type' );
}

/**
 * Get Default Labels
 *
 * @since 1.0
 * @return array $defaults Default labels
 */
function tf_tours_default_labels() {
    $default_tour = array(
        'singular' => __( 'Tour', 'tourfic' ),
        'plural'   => __( 'Tours', 'tourfic' ),
    );
    return apply_filters( 'tf_tours_name', $default_tour );
}

/**
 * Get Singular Label
 *
 * @since 1.0
 *
 * @param bool $lowercase
 * @return string $defaults['singular'] Singular label
 */
function tf_tours_singular_label( $lowercase = false ) {
    $default_tour = tf_tours_default_labels();
    return ( $lowercase ) ? strtolower( $default_tour['singular'] ) : $default_tour['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0
 * @return string $defaults['plural'] Plural label
 */
function tf_tours_plural_label( $lowercase = false ) {
    $default_tour = tf_tours_default_labels();
    return ( $lowercase ) ? strtolower( $default_tour['plural'] ) : $default_tour['plural'];
}

/**
 * Register taxonomies for tf_tours
 * 
 * tour_destination,attraction,activities
 */
function tf_tours_taxonomies_register() {

    /**
     * Taxonomy: tour_destination,tour_attraction,tour_activities
     */
    $tour_destination_slug = apply_filters( 'tour_destination_slug', 'tour-destination' );
    $tour_attraction_slug = apply_filters( 'tour_attraction_slug', 'tour-attraction' );
    $tour_actvities_slug = apply_filters( 'tour_actvities_slug', 'tour-activities' );
    $tour_features_slug = apply_filters( 'tour_features_slug', 'tour-features' );

    $tour_destination_labels = array(
        'name'                       => __( 'Tour Destinations', 'tourfic' ),
        'singular_name'              => __( 'Tour Destination', 'tourfic' ),
        'menu_name'                  => __( 'Destination', 'tourfic' ),
        'all_items'                  => __( 'All Destinations', 'tourfic' ),
        'edit_item'                  => __( 'Edit Destinations', 'tourfic' ),
        'view_item'                  => __( 'View Destinations', 'tourfic' ),
        'update_item'                => __( 'Update Destinations name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Destinations', 'tourfic' ),
        'new_item_name'              => __( 'New Destinations name', 'tourfic' ),
        'parent_item'                => __( 'Parent Destinations', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Destinations:', 'tourfic' ),
        'search_items'               => __( 'Search Destination', 'tourfic' ),
        'popular_items'              => __( 'Popular Destination', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Destination with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Destination', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Destination', 'tourfic' ),
        'not_found'                  => __( 'No Destination found', 'tourfic' ),
        'no_terms'                   => __( 'No Destination', 'tourfic' ),
        'items_list_navigation'      => __( 'Destination list navigation', 'tourfic' ),
        'items_list'                 => __( 'Destination list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Destination', 'tourfic' ),
    );

    $tour_destination_args = array(
        'labels'                => $tour_destination_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $tour_destination_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'tour_destination',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_tours',
            'edit_terms' => 'edit_tf_tours',
         ),
    );

    $tour_attraction_labels = array(
        'name'                       => __( 'Tour Attractions', 'tourfic' ),
        'singular_name'              => __( 'Tour Attractions', 'tourfic' ),
        'menu_name'                  => __( 'Attraction', 'tourfic' ),
        'all_items'                  => __( 'All Attractions', 'tourfic' ),
        'edit_item'                  => __( 'Edit Attractions', 'tourfic' ),
        'view_item'                  => __( 'View Attractions', 'tourfic' ),
        'update_item'                => __( 'Update Attractions name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Attractions', 'tourfic' ),
        'new_item_name'              => __( 'New Attractions name', 'tourfic' ),
        'parent_item'                => __( 'Parent Attractions', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Attractions:', 'tourfic' ),
        'search_items'               => __( 'Search Attractions', 'tourfic' ),
        'popular_items'              => __( 'Popular Attractions', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Attraction with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Attraction', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Attraction', 'tourfic' ),
        'not_found'                  => __( 'No Attraction found', 'tourfic' ),
        'no_terms'                   => __( 'No Attraction', 'tourfic' ),
        'items_list_navigation'      => __( 'Attraction list navigation', 'tourfic' ),
        'items_list'                 => __( 'Attraction list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Attraction', 'tourfic' ),
    );
    
    $tour_attraction_args = array(
        'labels'                => $tour_attraction_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $tour_attraction_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'tour_attraction',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_tours',
            'edit_terms' => 'edit_tf_tours',
         ),
    );
    
    //Tour Activities Taxonomy
    $tour_activities_labels = array(
        'name'                       => __( 'Tour Activities', 'tourfic' ),
        'singular_name'              => __( 'Tour Activity', 'tourfic' ),
        'menu_name'                  => __( 'Activities', 'tourfic' ),
        'all_items'                  => __( 'All Activities', 'tourfic' ),
        'edit_item'                  => __( 'Edit Activity', 'tourfic' ),
        'view_item'                  => __( 'View Activity', 'tourfic' ),
        'update_item'                => __( 'Update Activity name', 'tourfic' ),
        'add_new_item'               => __( 'Add New Activity', 'tourfic' ),
        'new_item_name'              => __( 'New Activity name', 'tourfic' ),
        'parent_item'                => __( 'Parent Activity', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Activity', 'tourfic' ),
        'search_items'               => __( 'Search Activities', 'tourfic' ),
        'popular_items'              => __( 'Popular Activities', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Activities with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove activity', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used activity', 'tourfic' ),
        'not_found'                  => __( 'No activity found', 'tourfic' ),
        'no_terms'                   => __( 'No activity', 'tourfic' ),
        'items_list_navigation'      => __( 'Activity list navigation', 'tourfic' ),
        'items_list'                 => __( 'Activity list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Activities', 'tourfic' ),
    );
    
    $tour_activities_args = array(
        'labels'                => $tour_activities_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $tour_actvities_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'tour_activities',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_tours',
            'edit_terms' => 'edit_tf_tours',
         ),
    );

    //Tour Features Taxonomy
    $tour_features_labels = array(
        'name'                       => __( 'Tour Features', 'tourfic' ),
        'singular_name'              => __( 'Tour Feature', 'tourfic' ),
        'menu_name'                  => __( 'Features', 'tourfic' ),
        'all_items'                  => __( 'All Features', 'tourfic' ),
        'edit_item'                  => __( 'Edit Feature', 'tourfic' ),
        'view_item'                  => __( 'View Feature', 'tourfic' ),
        'update_item'                => __( 'Update Feature name', 'tourfic' ),
        'add_new_item'               => __( 'Add New Feature', 'tourfic' ),
        'new_item_name'              => __( 'New Feature name', 'tourfic' ),
        'parent_item'                => __( 'Parent Feature', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Feature', 'tourfic' ),
        'search_items'               => __( 'Search Features', 'tourfic' ),
        'popular_items'              => __( 'Popular Features', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate features with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove feature', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used feature', 'tourfic' ),
        'not_found'                  => __( 'No Feature found', 'tourfic' ),
        'no_terms'                   => __( 'No activity', 'tourfic' ),
        'items_list_navigation'      => __( 'Feature list navigation', 'tourfic' ),
        'items_list'                 => __( 'Feature list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Features', 'tourfic' ),
    );

    $tour_features_args = array(
        'labels'                => $tour_features_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => $tour_features_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'meta_box_cb'          => false,
        'rest_base'             => 'tour_features',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_tours',
            'edit_terms' => 'edit_tf_tours',
        ),
    );

    register_taxonomy( 'tour_destination', 'tf_tours', apply_filters( 'tour_destination_args', $tour_destination_args ) );
    register_taxonomy( 'tour_attraction', 'tf_tours', apply_filters( 'tour_attraction_args', $tour_attraction_args ) );
    register_taxonomy( 'tour_activities', 'tf_tours', apply_filters( 'tour_activities_args', $tour_activities_args ) );
    register_taxonomy( 'tour_features', 'tf_tours', apply_filters( 'tour_features_args', $tour_features_args ) );

}
add_action( 'init', 'tf_tours_taxonomies_register' );

###############################################
# Functions related to post types, taxonomies #
###############################################

/**
 * Flushing Rewrite on Tourfic Activation
 * 
 * tf_tours post type
 * tour_destination taxonomy
 */
function tf_tours_rewrite_flush() {

    register_tf_tours_post_type();
    tf_tours_taxonomies_register();
    flush_rewrite_rules();

}
register_activation_hook( TF_PATH . 'tourfic.php', 'tf_tours_rewrite_flush' );

/**
 * Get tour destinations
 * 
 * {taxonomy-tour_destination}
 */
if ( !function_exists( 'get_tour_destinations' ) ) {
    function get_tour_destinations() {

        $destinations = array();

        $destination_terms = get_terms( array(
            'taxonomy'   => 'tour_destination',
            'hide_empty' => true,
        ) );

        foreach ( $destination_terms as $destination_term ) {
            $destinations[$destination_term->slug] = $destination_term->name;
        }

        return $destinations;

    }
}

#################################
# All the forms                 #
# Search form, booking form     #
#################################

/**
 * Tour Search form
 * 
 * Horizontal
 * 
 * Called in shortcodes
 */
if ( !function_exists('tf_tour_search_form_horizontal') ) {
    function tf_tour_search_form_horizontal( $classes, $title, $subtitle ) {

        ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">


            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                    <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e('Destination', 'tourfic'); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="place-name" required id="tf-destination" class="" placeholder="<?php _e('Enter Destination', 'tourfic'); ?>" value="">
                                    <input type="hidden" name="place" id="tf-search-tour" class="tf-place-input" />                    </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="adults-text"><?php _e('1 Adults', 'tourfic'); ?></div>
                        <div class="person-sep"></div>
                        <div class="child-text"><?php _e('0 Children', 'tourfic'); ?></div>
                        <div class="person-sep"></div>
                        <div class="infant-text"><?php _e('0 Infant', 'tourfic'); ?></div>
                    </div>
                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="1" value="1">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="0">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Infant', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="infant" id="infant" min="0" value="0">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tf_selectdate-wrap">
                <!-- @KK Merged two inputs into one  -->
                <div class="tf_input-inner">
                    <label class="tf_label-row">
                                <span class="tf-label"><?php _e('Check-in & Check-out date', 'tourfic'); ?></span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e('Select Date', 'tourfic'); ?>" <?php echo tfopt('date_tour_search')? 'required' : ''; ?>>
                                </div>
                            </label>
                    </div>
                </div>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>
    <script>
    (function($) {
        $(document).ready(function() {

            $("#tf_tour_aval_check #check-in-out-date").flatpickr({
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
            });

        });
    })(jQuery);
    </script>
    <?php
    }
}


/**
 * Tour Advance Search form
 * 
 * Horizontal
 * 
 * Called in shortcodes
 */
if ( !function_exists('tf_tour_advanced_search_form_horizontal') ) {
    function tf_tour_advanced_search_form_horizontal( $classes, $title, $subtitle ) {

        ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">

            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                    <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e('Destination', 'tourfic'); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>            
                                    <input type="text" name="place-name" required id="tf-tour-location-adv" class="tf-tour-preview-place" placeholder="<?php _e('Enter Location', 'tourfic'); ?>" >
                                    <input type="hidden" name="place" id="tf-tour-place">           
                                    <div class="tf-hotel-results tf-tour-results">
                                        <ul id="ui-id-2">
                                            <?php 
                                            $tf_tour_destination = get_terms( array(
                                                'taxonomy' => 'tour_destination',
                                                'orderby' => 'title',
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'hierarchical' => 0,
                                            ) );
                                            if ( $tf_tour_destination ) { 
                                            foreach( $tf_tour_destination as $term ) {
                                            if( !empty($term->name) ){
                                            ?>
                                            <li data-name="<?php echo $term->name; ?>" data-slug="<?php echo $term->slug; ?>"><i class="fa fa-map-marker"></i><?php echo $term->name; ?></li>
                                            <?php
                                            } } }
                                            ?>
                                        </ul>
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
                        <div class="adults-text"><?php _e('1 Adults', 'tourfic'); ?></div>
                        <div class="person-sep"></div>
                        <div class="child-text"><?php _e('0 Children', 'tourfic'); ?></div>
                        <div class="person-sep"></div>
                        <div class="infant-text"><?php _e('0 Infant', 'tourfic'); ?></div>
                    </div>
                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="1" value="1">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="0">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Infant', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="infant" id="infant" min="0" value="0">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tf_selectdate-wrap">
                <!-- @KK Merged two inputs into one  -->
                <div class="tf_input-inner">
                    <label class="tf_label-row">
                                <span class="tf-label"><?php _e('Check-in & Check-out date', 'tourfic'); ?></span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e('Select Date', 'tourfic'); ?>" <?php echo tfopt('date_tour_search')? 'required' : ''; ?>>
                                </div>
                            </label>
                    </div>
                </div>
                <div class="tf_selectdate-wrap tf_more_info_selections">
                    <div class="tf_input-inner">
                        <label class="tf_label-row" style="width: 100%;">
                            <span class="tf-label"><?php _e('More', 'tourfic'); ?></span>
                            <span style="text-decoration: none; display: block; cursor: pointer;"><?php _e('Filter', 'tourfic'); ?>  <i class="fas fa-angle-down"></i></span>
                        </label>
                    </div>
                    <div class="tf-more-info">
                    <span><?php _e('Filter Price', 'tourfic'); ?></span>
                    <div class="tf-filter-price-range">
                        <div class="tf-tour-filter-range"></div>
                    </div>
                    </div>
                </div>
                
                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
                    <button class="tf_button tf-submit tf-tours-btn btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>
    <script>
    (function($) {
        $(document).ready(function() {

            $("#tf_tour_aval_check #check-in-out-date").flatpickr({
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
            });

        });
    })(jQuery);
    </script>
    <?php
    }
}

/**
 * Single Tour Booking Bar
 * 
 * Single Tour Page
 */
function tf_single_tour_booking_form( $post_id ) {

    // Value from URL
    // Adults
    $adults = !empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
    // children
    $child = !empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
    // room
    $infant = !empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
    // Check-in & out date
    $check_in_out = !empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
    
    $meta = get_post_meta( $post_id, 'tf_tours_opt', true );
    $tour_type = !empty( $meta['type'] ) ? $meta['type'] : '';
    // Continuous custom availability
    $custom_avail = !empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : '';

    if ( $tour_type == 'fixed' ) {
        if( !empty($meta['fixed_availability']) && gettype($meta['fixed_availability'])=="string" ){
            $tf_tour_fixed_avail = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
            }, $meta['fixed_availability'] );
            $tf_tour_fixed_date = unserialize( $tf_tour_fixed_avail );
            $departure_date = !empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
            $return_date = !empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
            $min_people = !empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
            $max_people = !empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
        }else{
            $departure_date = !empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
            $return_date = !empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
            $min_people = !empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
            $max_people = !empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
        }

    } elseif ( $tour_type == 'continuous' ) {

        $disabled_day = !empty( $meta['disabled_day'] ) ? $meta['disabled_day'] : '';
        $disable_range = !empty( $meta['disable_range'] ) ? $meta['disable_range'] : '';
        if( !empty($disable_range) && gettype($disable_range)=="string" ){
            $disable_range_unserial = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
            }, $disable_range );
            $disable_range = unserialize( $disable_range_unserial );
    
        }
        $disable_specific = !empty( $meta['disable_specific'] ) ? $meta['disable_specific'] : '';
        $disable_specific = str_replace( ', ', '", "', $disable_specific );

        if ( $custom_avail == true ) {

            $cont_custom_date = !empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';

            if( !empty($cont_custom_date) && gettype($cont_custom_date)=="string" ){
                $cont_custom_date_unserial = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                }, $cont_custom_date );
                $cont_custom_date = unserialize( $cont_custom_date_unserial );
        
            }

        }     

    }

    $disable_adult_price  = !empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
    $disable_child_price  = !empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
    $disable_infant_price = !empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
    $pricing_rule         = !empty( $meta['pricing'] ) ? $meta['pricing'] : '';
    $group_price          = !empty( $meta['group_price'] ) ? $meta['group_price'] : false;
    $adult_price          = !empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
    $child_price          = !empty( $meta['child_price'] ) ? $meta['child_price'] : false;
    $infant_price         = !empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
    $tour_extras          = isset( $meta['tour-extra'] ) ? $meta['tour-extra'] : null;
    if( !empty($tour_extras) && gettype($tour_extras)=="string" ){

        $tour_extras_unserial = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $tour_extras );
        $tour_extras = unserialize( $tour_extras_unserial );

    }
    $times = [];
    if( !empty($meta['cont_custom_date']) && gettype($meta['cont_custom_date'])=="string" ){

        $tf_tour_unserial_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $meta['cont_custom_date'] );
        $tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );
        
        if( !empty($tf_tour_unserial_custom_date) ) {
            if ( $custom_avail == true && !empty( $meta['cont_custom_date'] ) ) {
                $allowed_times = array_map(function ($v) {
                    return $times[] = ['date' => $v['date'], 'times' => array_map(function ($v) {
                        return $v['time'];
                    }, $v['allowed_time'] ?? [])];
                }, $tf_tour_unserial_custom_date);
            }
        }
        
    }else{
        if ( $custom_avail == true && !empty( $meta['cont_custom_date'] ) ) {
            $allowed_times = array_map(function ($v) {
                if(!empty($v['date'])){
                    return $times[] = ['date' => $v['date'], 'times' => array_map(function ($v) {
                        return $v['time'];
                    }, $v['allowed_time'] ?? [])];
                }
            }, $meta['cont_custom_date']);
        }
        
    }

    if( !empty($meta['allowed_time']) && gettype($meta['allowed_time'])=="string" ){

        $tf_tour_unserial_custom_time = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $meta['allowed_time'] );
        $tf_tour_unserial_custom_time = unserialize( $tf_tour_unserial_custom_time );
        if( !empty($tf_tour_unserial_custom_time) ){
            if ( $custom_avail == false && !empty( $meta['allowed_time'] ) ) {
                $allowed_times = array_map(function ($v) {
                    return $v['time'];          
                }, $tf_tour_unserial_custom_time ?? []);
            }
        }
    }else{
        if ( $custom_avail == false && !empty( $meta['allowed_time'] ) ) {
            $allowed_times = array_map(function ($v) {
                return $v['time'];          
            }, $meta['allowed_time'] ?? []);
        }
    }
    // Single Template Check
    $tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
	$tf_tour_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-tour'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-tour'] : 'design-1';
	$tf_tour_selected_template = !empty($tf_tour_single_template) ? $tf_tour_single_template : $tf_tour_global_template;

    ob_start();
	if( $tf_tour_selected_template == "design-1" ){
	?>
    <form class="tf_tours_booking">
        <div class="tf-field-group tf-mrtop-8">
            <i class="fa-sharp fa-solid fa-calendar-days"></i>
            <input type='text' name='check-in-out-date' id='check-in-out-date' class='tf-field tours-check-in-out' onkeypress="return false;" placeholder='<?php _e( "Select Date", "tourfic" ); ?>' value='' required />
        </div>
        <?php if (function_exists('is_tf_pro') && is_tf_pro() && $tour_type != 'fixed') { ?>
        <div class="tf-field-group tf-mrtop-8" id="check-in-time-div" style="display: none;">
            <i class="fa-regular fa-clock"></i>
            <select class="tf-field" name="check-in-time" id="check-in-time" style="min-width: 100px;"></select>
        </div>
        <?php } ?>
        
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <script>
            (function ($) {
                $(document).ready(function () {

                    const allowed_times = JSON.parse('<?php echo wp_json_encode($allowed_times ?? []) ?>');
                    const custom_avail = '<?php echo $custom_avail; ?>';
                    if (custom_avail == false && allowed_times.length > 0) {
                        populateTimeSelect(allowed_times)
                    }

                    function populateTimeSelect(times) {
                        let timeSelect = $('#check-in-time');
                        let timeSelectDiv = $("#check-in-time-div");
                        timeSelect.empty();
                        if (times.length > 0) {
                            timeSelect.append(`<option value="" selected hidden><?php _e( "Select Time", "tourfic" ); ?></option>`);
                            $.each(times, function(i, v) {
                                timeSelect.append(`<option value="${v}">${v}</option>`);
                            });
                            timeSelectDiv.show();
                        } else timeSelectDiv.hide();
                    }

                    $("#check-in-out-date").flatpickr({  
                        enableTime: false,
                        dateFormat: "Y/m/d",                               
                        <?php
                        // Flatpickt locale for translation
                        tf_flatpickr_locale();

                    if ($tour_type && $tour_type == 'fixed') { ?>

                        mode: "range",
                        defaultDate: ["<?php echo $departure_date; ?>", "<?php echo $return_date; ?>"],
                        enable: [
                            {
                                from: "<?php echo $departure_date; ?>",
                                to: "<?php echo $return_date; ?>"
                            }
                        ],
                        onReady: function(selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },

                    <?php } elseif ($tour_type && $tour_type == 'continuous'){ ?>

                        minDate: "today",

                        <?php if ($custom_avail && $custom_avail == true){ ?>

                        enable: [

                        <?php foreach ($cont_custom_date as $item) {
                            echo '{
                                    from: "' .$item["date"]["from"]. '",
                                    to: "' .$item["date"]["to"]. '"
                                },';
                        } ?>

                        ],

                        <?php }
                        if ($custom_avail == false) {
                            if ($disabled_day || $disable_range || $disable_specific) {
                        ?>

                        "disable": [
                            <?php if ($disabled_day) { ?>
                            function(date) {
                                return (date.getDay() === 8 <?php foreach($disabled_day as $dis_day) { echo '|| date.getDay() === ' .$dis_day. ' '; } ?>);
                            },
                            <?php }
                            if ($disable_range) {
                                foreach ($disable_range as $d_item) {
                                    echo '{
                                        from: "' .$d_item["date"]["from"]. '",
                                        to: "' .$d_item["date"]["to"]. '"
                                    },';
                                }
                            }

                            if ($disable_specific) {
                                echo '"' .$disable_specific. '"';
                            }
                            ?>
                        ],
                    <?php 
                        }
                        }
                        
                    } 
                    ?>

                    onChange: function(selectedDates, dateStr, instance) {
                        if (custom_avail == true) {

                            let times = allowed_times.filter((v) => {
                                let date_str = Date.parse(dateStr);
                                let start_date = Date.parse(v.date.from);
                                let end_date = Date.parse(v.date.to);
                                return start_date <= date_str && end_date >= date_str;
                            });
                            times = times.length > 0 && times[0].times ? times[0].times : null;
                            populateTimeSelect(times);
                        }

                    },

                    });

                });
            })(jQuery);
        </script>
        <div class="tf-booking-person tf-mrtop-30">
            <div class="tf-form-title">
                <p><?php _e("Person Info","tourfic"); ?></p>
            </div>
            <?php if ($custom_avail == true || (!$disable_adult_price && $pricing_rule == 'person' && $adult_price != false) || (!$disable_adult_price && $pricing_rule == 'group' && $group_price != false)) { ?>
            <div class="tf-field-group tf-mrtop-16 tf_acrselection">
                <div class="tf-field tf-flex">
                    <div class="acr-label tf-flex">
                        <i class="fa-regular fa-user"></i>
                        <?php _e('Adults', 'tourfic'); ?>
                    </div>
                    <div class="acr-select">
                        <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="0" value="<?php echo !empty($adults) ? $adults : '0'; ?>">
                        <div class="acr-inc">+</div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($custom_avail == true || (!$disable_child_price && $pricing_rule == 'person' && $child_price != false) || (!$disable_child_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                <div class="tf-field-group tf-mrtop-16 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-solid fa-child"></i>
                            <?php _e('Children', 'tourfic'); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                                <input type="number" name="childrens" id="children" min="0" value="<?php echo !empty($child) ? $child : '0'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($custom_avail == true || (!$disable_infant_price && $pricing_rule == 'person' && $infant_price != false) || (!$disable_infant_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                <div class="tf-field-group tf-mrtop-16 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-solid fa-baby"></i>
                            <?php _e('Infant', 'tourfic'); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                                <input type="number" name="infants" id="infant" min="0" value="<?php echo !empty($infant) ? $infant : '0'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if ( function_exists('is_tf_pro') && is_tf_pro() && $tour_extras ) { 
        if((!empty($tour_extras[0]['title']) && !empty($tour_extras[0]['desc']) && !empty($tour_extras[0]['price'])) || !empty($tour_extras[1]['title']) && !empty($tour_extras[1]['desc']) && !empty($tour_extras[1]['price'])){  
        ?>
        <div class="tf-tour-extra-area tf-mrtop-30">
            <div class="tf-form-title tf-tour-extra">
                <p><?php _e("Tour Extra","tourfic"); ?>
                <i class="fa-solid fa-plus"></i>
                <i class="fa-solid fa-minus"></i>
                </p>
            </div>
            <div class="tf-tour-extra-box">
            <?php foreach( $tour_extras as $tour_extra ){ 
                if(!empty($tour_extra['title']) && !empty($tour_extra['desc']) && !empty($tour_extra['price'])){
            ?>
            <div class="tf-tour-extra tf-mrtop-8">
                <div class="tf-tour-extra-price tf-flex tf-flex-align-top tf-flex-space-bttn tour-extra-single">
                    <div class="tf-tour-extra-input tf-flex tf-flex-align-top tf-flex-gap-8">
                        <input type="checkbox" value="<?php echo esc_attr( $tour_extra['price'] ); ?>" data-title="<?php echo esc_attr( $tour_extra['title'] ); ?>">
                        <p><?php _e( $tour_extra['title'] ); ?></p>
                    </div>
                    <div class="tf-tour-extra-price">
                    <?php echo wc_price( $tour_extra['price'] ); ?>
                    </div>
                </div>
                <?php if ($tour_extra['desc']) { ?>
                <div class="tf-tour-extra-details tf-mrtop-8">
                <p><?php echo esc_html( $tour_extra['desc'] ); ?></p>
                </div>
                <?php } ?>
            </div>
            <?php } } ?>
            </div>
        </div>
        
        <?php } } ?>
        <div class="tf-tours-booking-btn tf-booking-bttns tf-mrtop-30">
        <input type="hidden" name="location" value="">
        <input type="hidden" name="deposit" value="0">
        <?php if ( function_exists('is_tf_pro') && is_tf_pro() && ! empty( $meta['allow_deposit'] ) && $meta['allow_deposit'] == '1' && ! empty( $meta['deposit_amount'] )) { ?>
            <div class="tf-btn"><a data-fancybox class="tf-bttn-normal bttn-secondary" data-src="#tour-deposit" href="javascript:;"><?php _e('Make a Partial Payment', 'tourfic'); ?></a></div>
            <div style="display: none;" id="tour-deposit">
                <div class="tf-tours-booking-deposit">
                    <div class="tf-tours-booking-deposit-text">
                        <h3><?php _e(tfopt('deposit-title', 'Do you want to deposit amount for booking the tour?'), 'tourfic'); ?></h3>
                        <p><?php _e(tfopt('deposit-subtitle', 'You can deposit amount for booking the tour. After booking the tour, you can pay the rest amount after the tour is completed.'), 'tourfic'); ?></p>
                    </div>
                    <div class="tf-tours-booking-deposit-amount">
                            <span><?php _e(tfopt('deposit-amount','Amount of deposit on total price'), 'tourfic'); ?></span>
                            <span><?php echo $meta['deposit_type'] == 'fixed' ? wc_price( $meta['deposit_amount'] ) : $meta['deposit_amount']. '%'; ?> </span>
                    </div>
                    <div class="tf_button_group">
                        <button class="tf_button btn-styled" type="submit" data-deposit="false"><?php _e(tfopt('deposit-full-payment','Pay full amount'), 'tourfic'); ?></button>
                        <button class="tf_button btn-styled" type="submit" data-deposit="true"><?php _e(tfopt('deposit-payment','Make a partial payment'), 'tourfic'); ?></button>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="tf-btn ">
                <button class="tf-bttn-normal bttn-primary" type="submit"><?php _e('Book Now', 'tourfic'); ?></button>
            </div>
        <?php } ?>
    </div>
    </form>
<?php
	}else{
    ?>
        <div class="tf-tour-booking-wrap">
            <form class="tf_tours_booking">
                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <?php if ($custom_avail == true || (!$disable_adult_price && $pricing_rule == 'person' && $adult_price != false) || (!$disable_adult_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                            <div class="adults-text"><?php echo (!empty($adults) ? $adults : '0') . ' ' . __("Adults", "tourfic"); ?></div>
                        <?php } ?>
                        <?php if ($custom_avail == true || (!$disable_child_price && $pricing_rule == 'person' && $child_price != false) || (!$disable_child_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php echo (!empty($child) ? $child : '0') . ' ' . __("Children", "tourfic"); ?></div>
                        <?php } ?>
                        <?php if ($custom_avail == true || (!$disable_infant_price && $pricing_rule == 'person' && $infant_price != false) || (!$disable_infant_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php echo (!empty($infant) ? $infant : '0') . ' ' . __("Infant", "tourfic"); ?></div>
                        <?php } ?>
                    </div>
                    <div class="tf_acrselection-wrap" style="display: none;">
                        <div class="tf_acrselection-inner">
                            <?php if ($custom_avail == true || (!$disable_adult_price && $pricing_rule == 'person' && $adult_price != false) || (!$disable_adult_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="0" value="<?php echo !empty($adults) ? $adults : '0'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($custom_avail == true || (!$disable_child_price && $pricing_rule == 'person' && $child_price != false) || (!$disable_child_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="childrens" id="children" min="0" value="<?php echo !empty($child) ? $child : '0'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($custom_avail == true || (!$disable_infant_price && $pricing_rule == 'person' && $infant_price != false) || (!$disable_infant_price && $pricing_rule == 'group' && $group_price != false)) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Infant', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="infants" id="infant" min="0" value="<?php echo !empty($infant) ? $infant : '0'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class='tf_form-row'>
	    	        <label class='tf_label-row'>
	    		        <div class='tf_form-inner'>
                            <input type='text' name='check-in-out-date' id='check-in-out-date' class='tours-check-in-out' onkeypress="return false;" placeholder='<?php _e( "Select Date", "tourfic" ); ?>' value='' required />
				        </div>
			        </label>
		        </div>

                <?php if (function_exists('is_tf_pro') && is_tf_pro() && $tour_type != 'fixed') { ?>
                <div class='tf_form-row' id="check-in-time-div" style="display: none;">
                    <label class='tf_label-row'>
                        <div class='tf_form-inner'>
                            <select name="check-in-time" id="check-in-time" style="min-width: 100px;">
                            </select>
                        </div>
                    </label>
                </div>
                <?php } ?>

                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <?php if ( function_exists('is_tf_pro') && is_tf_pro() && $tour_extras ) { 
                if((!empty($tour_extras[0]['title']) && !empty($tour_extras[0]['desc']) && !empty($tour_extras[0]['price'])) || !empty($tour_extras[1]['title']) && !empty($tour_extras[1]['desc']) && !empty($tour_extras[1]['price'])){  
                ?>
                <div class="tour-extra">
                    <a data-fancybox data-src="#tour-extra" href="javascript:;"><i class="far fa-plus-square"></i><?php _e('Tour Extras', 'tourfic') ?></a>
                    <div style="display: none;" id="tour-extra">
                        <div class="tour-extra-container">
                        <?php foreach( $tour_extras as $tour_extra ){ 
                            if(!empty($tour_extra['title']) && !empty($tour_extra['desc']) && !empty($tour_extra['price'])){
                        ?>
                            <div class="tour-extra-single">
                                <div class="tour-extra-left">
                                    <h4><?php _e( $tour_extra['title'] ); ?></h4>
                                    <?php if ($tour_extra['desc']) { ?><p><?php echo esc_html( $tour_extra['desc'] ); ?></p><?php } ?>
                                </div>
                                <div class="tour-extra-right">
                                    <span><?php echo wc_price( $tour_extra['price'] ); ?></span>
                                    <input type="checkbox" value="<?php echo esc_attr( $tour_extra['price'] ); ?>" data-title="<?php echo esc_attr( $tour_extra['title'] ); ?>">
                                </div>												
                            </div>
                        <?php } } ?>
                        </div>
                    </div>
                </div>	
                <?php } } ?>	
                <div class="tf-tours-booking-btn">
                    <input type="hidden" name="location" value="">
                    <input type="hidden" name="deposit" value="0">
	                <?php if ( function_exists('is_tf_pro') && is_tf_pro() && ! empty( $meta['allow_deposit'] ) && $meta['allow_deposit'] == '1' && ! empty( $meta['deposit_amount'] )) { ?>
                        <div class="tf-btn"><a data-fancybox class="tf_button btn-styled" data-src="#tour-deposit" href="javascript:;"><?php _e('Book Now', 'tourfic'); ?></a></div>
                        <div style="display: none;" id="tour-deposit">
                            <div class="tf-tours-booking-deposit">
                                <div class="tf-tours-booking-deposit-text">
                                    <h3><?php _e(tfopt('deposit-title', 'Do you want to deposit amount for booking the tour?'), 'tourfic'); ?></h3>
                                    <p><?php _e(tfopt('deposit-subtitle', 'You can deposit amount for booking the tour. After booking the tour, you can pay the rest amount after the tour is completed.'), 'tourfic'); ?></p>
                                </div>
                                <div class="tf-tours-booking-deposit-amount">
                                        <span><?php _e(tfopt('deposit-amount','Amount of deposit on total price'), 'tourfic'); ?></span>
                                        <span><?php echo $meta['deposit_type'] == 'fixed' ? wc_price( $meta['deposit_amount'] ) : $meta['deposit_amount']. '%'; ?> </span>
                                </div>
                                <div class="tf_button_group">
                                    <button class="tf_button btn-styled" type="submit" data-deposit="false"><?php _e(tfopt('deposit-full-payment','Pay full amount'), 'tourfic'); ?></button>
                                    <button class="tf_button btn-styled" type="submit" data-deposit="true"><?php _e(tfopt('deposit-payment','Make a partial payment'), 'tourfic'); ?></button>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="tf-btn">
                            <button class="tf_button btn-styled" type="submit"><?php _e('Book Now', 'tourfic'); ?></button>
                        </div>
                    <?php } ?>
                </div>
            </form>
	    </div>
    
	<?php
    }
?>
    <script>
    (function ($) {
        $(document).ready(function () {

            const allowed_times = JSON.parse('<?php echo wp_json_encode($allowed_times ?? []) ?>');
            const custom_avail = '<?php echo $custom_avail; ?>';
            if (custom_avail == false && allowed_times.length > 0) {
                populateTimeSelect(allowed_times)
            }

            function populateTimeSelect(times) {
                let timeSelect = $('#check-in-time');
                let timeSelectDiv = $("#check-in-time-div");
                timeSelect.empty();
                if (times.length > 0) {
                    timeSelect.append(`<option value="" selected hidden><?php _e( "Select Time", "tourfic" ); ?></option>`);
                    $.each(times, function(i, v) {
                        timeSelect.append(`<option value="${v}">${v}</option>`);
                    });
                    timeSelectDiv.show();
                } else timeSelectDiv.hide();
            }

            $("#check-in-out-date").flatpickr({  
                enableTime: false,
                dateFormat: "Y/m/d",                               
                <?php
                // Flatpickt locale for translation
                tf_flatpickr_locale();

            if ($tour_type && $tour_type == 'fixed') { ?>

                mode: "range",
                defaultDate: ["<?php echo $departure_date; ?>", "<?php echo $return_date; ?>"],
                enable: [
                    {
                        from: "<?php echo $departure_date; ?>",
                        to: "<?php echo $return_date; ?>"
                    }
                ],
                onReady: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },

            <?php } elseif ($tour_type && $tour_type == 'continuous'){ ?>

                minDate: "today",

                <?php if ($custom_avail && $custom_avail == true){ ?>

                enable: [

                <?php foreach ($cont_custom_date as $item) {
                    echo '{
                            from: "' .$item["date"]["from"]. '",
                            to: "' .$item["date"]["to"]. '"
                        },';
                } ?>

                ],

                <?php }
                if ($custom_avail == false) {
                    if ($disabled_day || $disable_range || $disable_specific) {
                ?>

                "disable": [
                    <?php if ($disabled_day) { ?>
                    function(date) {
                        return (date.getDay() === 8 <?php foreach($disabled_day as $dis_day) { echo '|| date.getDay() === ' .$dis_day. ' '; } ?>);
                    },
                    <?php }
                    if ($disable_range) {
                        foreach ($disable_range as $d_item) {
                            echo '{
                                from: "' .$d_item["date"]["from"]. '",
                                to: "' .$d_item["date"]["to"]. '"
                            },';
                        }
                    }

                    if ($disable_specific) {
                        echo '"' .$disable_specific. '"';
                    }
                    ?>
                ],
            <?php 
                }
                }
                
            } 
            ?>

            onChange: function(selectedDates, dateStr, instance) {
                if (custom_avail == true) {

                    let times = allowed_times.filter((v) => {
                        let date_str = Date.parse(dateStr);
                        let start_date = Date.parse(v.date.from);
                        let end_date = Date.parse(v.date.to);
                        return start_date <= date_str && end_date >= date_str;
                    });
                    times = times.length > 0 && times[0].times ? times[0].times : null;
                    populateTimeSelect(times);
                }

            },

            });

        });
    })(jQuery);
    </script>
    <script>
        (function ($) {
            $(document).on('click', "#tour-deposit > div > div.tf_button_group > button", function(e) {
                e.preventDefault();
                var form = $(document).find('form.tf_tours_booking');
                var has_deposit = $(this).data('deposit');
                if (has_deposit === true) {
                    form.find('input[name="deposit"]').val(1);
                } else {
                    form.find('input[name="deposit"]').val(0);
                }
                form.submit();
            });
        })(jQuery);

    </script>
<?php
return ob_get_clean();
}

#################################
# Layouts                       #
#################################

/**
 * Tours Archive
 */
function tf_tour_archive_single_item($adults='', $child='', $check_in_out='', $startprice='', $endprice='') {

    // get post id
    $post_id = get_the_ID();
    //Get hotel meta values
    $meta = get_post_meta( get_the_ID(),'tf_tours_opt',true );
    // Location
    $location  = !empty($meta['text_location']) ? $meta['text_location'] : '';
    // Featured
    $featured  = !empty($meta['tour_as_featured']) ? $meta['tour_as_featured'] : '';

    // Adults
    if(empty($adults)) {
        $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
    }
    // children
    if(empty($child)) {
        $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
    }
    // room
    $infant = !empty($_GET['infant']) ? sanitize_text_field($_GET['infant']) : '';
    // Check-in & out date
    if(empty($check_in_out)) {
        $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';
    }

    $disable_adult_price  = !empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
    $disable_child_price  = !empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
    $disable_infant_price = !empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
    $pricing_rule         = !empty( $meta['pricing'] ) ? $meta['pricing'] : '';
    $custom_pricing_by_rule = !empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : '';
    $group_price          = !empty( $meta['group_price'] ) ? $meta['group_price'] : false;
    $adult_price          = !empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
    $child_price          = !empty( $meta['child_price'] ) ? $meta['child_price'] : false;
    $infant_price         = !empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;

    if( !empty( $check_in_out ) ){
		list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
	}

	if ( ! empty( $check_in_out ) ) {
		$period = new DatePeriod(
			new DateTime( $tf_form_start ),
			new DateInterval( 'P1D' ),
			new DateTime( !empty($tf_form_end) ? $tf_form_end : $tf_form_start . '23:59' )
		);
	} else {
		$period = '';
	}


    // Single link
    $url = get_the_permalink();
    $url = add_query_arg( array(
            'adults' => $adults,
            'children' => $child,
            'infant' => $infant
    ), $url );

    // Tour Starting Price
    $tour_price = [];
    if( $pricing_rule  && $pricing_rule == 'group' ){
        if(!empty($check_in_out)){
            if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                if ($custom_availability) {
                    foreach ( $meta['cont_custom_date'] as $repval ) {
                        //Initial matching date array
                        $show_tour = [];
                        $dates = $repval['date'];
                        // Check if any date range match with search form date range and set them on array
                        if ( ! empty( $period ) ) {
                            foreach ( $period as $date ) {
                                $show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
                            }
                        }
                        if ( ! in_array( 0, $show_tour ) ) {
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                if(! empty( $repval['group_price'] )){
                                    $tour_price[] = $repval['group_price'];
                                }
                            }
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                if(!empty($repval['adult_price']) && !$disable_adult_price){
                                    $tour_price[] = $repval['adult_price'];
                                }
                                if(!empty($repval['child_price']) && !$disable_child_price){
                                    $tour_price[] = $repval['child_price'];
                                }
                                if(!empty($repval['infant_price']) && !$disable_infant_price){
                                    $tour_price[] = $repval['infant_price'];
                                }
                            }
                        }
                    }
                }
            }
        }else{
            if(!empty($meta['group_price'])){
                $tour_price[] = $meta['group_price'];
            }
        }
    }
    if( $pricing_rule  && $pricing_rule == 'person' ){
        if(!empty($check_in_out)){
            if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                if ($custom_availability) {
                    foreach ( $meta['cont_custom_date'] as $repval ) {
                        //Initial matching date array
                        $show_tour = [];
                        $dates = $repval['date'];
                        // Check if any date range match with search form date range and set them on array
                        if ( ! empty( $period ) ) {
                            foreach ( $period as $date ) {
                                $show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
                            }
                        }
                        if ( ! in_array( 0, $show_tour ) ) {
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                if(! empty( $repval['group_price'] )){
                                    $tour_price[] = $repval['group_price'];
                                }
                            }
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                if(!empty($repval['adult_price']) && !$disable_adult_price){
                                    $tour_price[] = $repval['adult_price'];
                                }
                                if(!empty($repval['child_price']) && !$disable_child_price){
                                    $tour_price[] = $repval['child_price'];
                                }
                                if(!empty($repval['infant_price']) && !$disable_infant_price){
                                    $tour_price[] = $repval['infant_price'];
                                }
                            }
                        }
                    }
                }
            }
        }else{
            if(!empty($meta['adult_price']) && !$disable_adult_price){
                $tour_price[] = $meta['adult_price'];
            }
            if(!empty($meta['child_price']) && !$disable_child_price){
                $tour_price[] = $meta['child_price'];
            }
            if(!empty($meta['infant_price']) && !$disable_infant_price){
                $tour_price[] = $meta['infant_price'];
            }
        }
    }
    if( ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['tour-archive']=="design-1"){
    ?>
    <div class="tf-item-card tf-flex">
        <div class="tf-item-featured">
            <a href="<?php echo esc_url($url); ?>">
            <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail( 'full' );
                } else {
                    echo '<img src="' .TF_ASSETS_APP_URL . "images/feature-default.jpg". '" class="attachment-full size-full wp-post-image">';
                }
            ?>
            </a>
            <?php 
            $tf_discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
            $tf_discount_amount = !empty($meta['discount_price']) ? $meta['discount_price'] : '';
            ?>
            <div class="tf-features-box tf-flex">
                <?php 
                if( !empty($tf_discount_type) && $tf_discount_type!="none" && !empty($tf_discount_amount) ){
                ?>
                <div class="tf-discount"><?php echo $tf_discount_type == "percent" ? $tf_discount_amount."%" : wc_price($tf_discount_amount); ?> <?php _e("Off", "tourfic"); ?></div>
                <?php } ?>

                <?php if( $featured ): ?>
                    <div class="tf-feature">
                    <?php 
						echo !empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" );
					?>    
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tf-item-details">
            <?php 
            if(!empty($location)){
            ?>
            <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                <i class="fa-solid fa-location-dot"></i>
                <p><?php echo $location; ?></p>
            </div>
            <?php } ?>
            <div class="tf-title tf-mrtop-16">
                <h2><a href="<?php echo esc_url($url); ?>"><?php the_title();?></a></h2>
            </div>
            
            <?php tf_archive_single_rating();?>
            
            <div class="tf-details tf-mrtop-16">
                <p><?php echo substr(wp_strip_all_tags(get_the_content()), 0, 160). '...'; ?></p>
            </div>
            <div class="tf-post-footer tf-flex tf-flex-align-center tf-flex-space-bttn tf-mrtop-16">
                <div class="tf-pricing">

                <?php
                if ( ! empty( $tour_price ) ):
                    $lowest_price = wc_price( min( $tour_price ) );
                    echo __( "From ", "tourfic" ) . $lowest_price;
                endif; ?>

                </div>
                <div class="tf-booking-bttns">
                    <a class="tf-bttn-normal bttn-secondary" href="<?php echo esc_url($url); ?>"><?php _e("View Details","tourfic"); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
    }else{
    ?>
	<div class="single-tour-wrap <?php echo $featured ? esc_attr('tf-featured') : '' ?>">
		<div class="single-tour-inner">
            <?php if( $featured ): ?>
            <div class="tf-featured-badge">
				<span><?php _e( 'Featured','tourfic' ) ?></span>
			</div>
            <?php endif; ?>
			<div class="tourfic-single-left">
                <a href="<?php echo esc_url($url); ?>">
				<?php
                if (has_post_thumbnail()) {
					the_post_thumbnail( 'full' );
				} else {
                    echo '<img width="100%" height="100%" src="' .TF_ASSETS_APP_URL . "images/feature-default.jpg". '" class="attachment-full size-full wp-post-image">';
                }
                ?>
                </a>
			</div>
			<div class="tourfic-single-right">
				<div class="tf_property_block_main_row">
					<div class="tf_item_main_block">
						<div class="tf-hotel__title-wrap tf-tours-title-wrap">
                            <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php the_title();?></h3></a>
						</div>
						<?php
                        if($location) {
                            echo '<div class="tf-map-link">';
                            echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' .$location. '</span>';
                            echo '</div>'; 
                        }
                        ?>
					</div>
					<?php tf_archive_single_rating();?>
				</div>
				<div class="tf-tour-desc">
					<p><?php echo substr(wp_strip_all_tags(get_the_content()), 0, 160). '...'; ?></p>
				</div>

				<div class="availability-btn-area tour-search">
					<a href="<?php echo $url; ?>" class="tf_button btn-styled"><?php esc_html_e( 'View Details', 'tourfic' );?></a>
				</div>
                    
                <?php 
                $tour_price = [];
                if( $pricing_rule  && $pricing_rule == 'group' ){
                    if(!empty($check_in_out)){
                        if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                            $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                            if ($custom_availability) {
                                foreach ( $meta['cont_custom_date'] as $repval ) {
                                    //Initial matching date array
                                    $show_tour = [];
                                    $dates = $repval['date'];
                                    // Check if any date range match with search form date range and set them on array
                                    if ( ! empty( $period ) ) {
                                        foreach ( $period as $date ) {
                                            $show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
                                        }
                                    }
                                    if ( ! in_array( 0, $show_tour ) ) {
                                        if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                            if(! empty( $repval['group_price'] )){
                                                $tour_price[] = $repval['group_price'];
                                            }
                                        }
                                        if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                            if(!empty($repval['adult_price']) && !$disable_adult_price){
                                                $tour_price[] = $repval['adult_price'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        if(!empty($meta['group_price'])){
                            $tour_price[] = $meta['group_price'];
                        }
                    }
                }
                if( $pricing_rule  && $pricing_rule == 'person' ){
                    if(!empty($check_in_out)){
                        if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                            $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                            if ($custom_availability) {
                                foreach ( $meta['cont_custom_date'] as $repval ) {
                                    //Initial matching date array
                                    $show_tour = [];
                                    $dates = $repval['date'];
                                    // Check if any date range match with search form date range and set them on array
                                    if ( ! empty( $period ) ) {
                                        foreach ( $period as $date ) {
                                            $show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
                                        }
                                    }
                                    if ( ! in_array( 0, $show_tour ) ) {
                                        if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                            if(! empty( $repval['group_price'] )){
                                                $tour_price[] = $repval['group_price'];
                                            }
                                        }
                                        if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                            if(!empty($repval['adult_price']) && !$disable_adult_price){
                                                $tour_price[] = $repval['adult_price'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        if(!empty($meta['adult_price']) && !$disable_adult_price){
                            $tour_price[] = $meta['adult_price'];
                        }
                    }
                }
                ?>
                <?php
                if ( ! empty( $tour_price ) ):
                    ?>
                    <div class="tf-tour-price">
                        <?php
                        //get the lowest price from all available room price
                        $tf_tour_min_price = min( $tour_price );
                        $tf_tour_full_price = min( $tour_price );
                        $tf_tour_discount_type = !empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
                        $tf_tour_discount_price = !empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';
                        if( !empty($tf_tour_discount_type) && !empty($tf_tour_min_price) && !empty($tf_tour_discount_price) ){
                            if($tf_tour_discount_type=="percent"){
                                $tf_tour_min_discount = ($tf_tour_min_price*$tf_tour_discount_price)/100;
                                $tf_tour_min_price = $tf_tour_min_price - $tf_tour_min_discount;
                            }
                            if($tf_tour_discount_type=="fixed"){
                                $tf_tour_min_discount = $tf_tour_discount_price;
                                $tf_tour_min_price = $tf_tour_min_price - $tf_tour_discount_price;
                            }
                        }
                        $lowest_price = wc_price( $tf_tour_min_price );
                        echo __( "From ", "tourfic" ) . $lowest_price;
                        if( !empty($tf_tour_min_discount) ){
                            echo "<del>". wc_price( $tf_tour_full_price ). "</del>";
                        }
                        ?>
                    </div>
                <?php endif; ?>
			</div>
		</div>
	</div>
	<?php
    }
}

#################################
# WooCommerce integration       #
#################################
/**
 * WooCommerce Tour Functions
 * 
 * @include
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-tour.php' ) ) {
    require_once TF_INC_PATH . 'functions/woocommerce/wc-tour.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/woocommerce/wc-tour.php');
}

/**
 * Filter tours on search result page by checkin checkout dates set by backend
 *
 *
 * @author devkabir, fida
 *
 * @param DatePeriod $period    collection of dates by user input;
 * @param array      $not_found collection of tour exists
 * @param array      $data      user input for sidebar form
 */
function tf_filter_tour_by_date( $period, &$total_posts, array &$not_found, array $data = [] ): void {
    if(isset($data[3]) && isset($data[4])){
        [$adults, $child, $check_in_out, $startprice, $endprice] = $data;
    }else{
        [$adults, $child, $check_in_out] = $data;
    }
    // Get tour meta options
    $meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

    // Set initial tour availability status
    $has_tour = false;

    // Total People
    $total_people = intval( $adults ) + intval( $child );

    if ( !empty($meta['type'] ) && $meta['type'] === 'fixed' ) {

        if( !empty($meta['fixed_availability']) && gettype($meta['fixed_availability'])=="string" ){
            $tf_tour_unserial_fixed_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
            }, $meta['fixed_availability'] );
            $tf_tour_unserial_fixed_date = unserialize( $tf_tour_unserial_fixed_date );
            $fixed_availability = !empty( $tf_tour_unserial_fixed_date ) ? $tf_tour_unserial_fixed_date['date'] : [];
        }else{
            $fixed_availability = !empty( $meta['fixed_availability'] ) ? $meta['fixed_availability']['date'] : [];
        }

        $people_counter = 0;

        // Max & Min People Check
        if ( !empty($tf_tour_unserial_fixed_date['max_seat']) && $tf_tour_unserial_fixed_date['max_seat'] >= $total_people && $tf_tour_unserial_fixed_date['max_seat'] != 0 && !empty($tf_tour_unserial_fixed_date['min_seat']) && $tf_tour_unserial_fixed_date['min_seat'] <= $total_people && $tf_tour_unserial_fixed_date['min_seat'] != 0 ) {
            $people_counter ++;
        }
        if( $people_counter > 0 ){
            $show_fixed_tour    = [];

            foreach ( $period as $date ) {

                $show_fixed_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $fixed_availability['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $fixed_availability['to'] ) );

            }

            
            if(!empty($startprice) && !empty($endprice)){
                if(!empty($meta['adult_price'])){
                    if($startprice<=$meta['adult_price'] && $meta['adult_price']<=$endprice){
                        $has_tour = !in_array( 0, $show_fixed_tour );
                    }
                }
                if(!empty($meta['child_price'])){
                    if($startprice<=$meta['child_price'] && $meta['child_price']<=$endprice){
                        $has_tour = !in_array( 0, $show_fixed_tour );
                    }
                }
                if(!empty($meta['infant_price'])){
                    if($startprice<=$meta['infant_price'] && $meta['infant_price']<=$endprice){
                        $has_tour = !in_array( 0, $show_fixed_tour );
                    }
                }
                if(!empty($meta['group_price'])){
                    if($startprice<=$meta['group_price'] && $meta['group_price']<=$endprice){
                        $has_tour = !in_array( 0, $show_fixed_tour );
                    }
                }
            }else{
                $has_tour = true;
            }
        }
    }

    if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {

        $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;

        if ($custom_availability) {

            if( !empty($meta['cont_custom_date']) && gettype($meta['cont_custom_date'])=="string" ){
                $tf_tour_unserial_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                }, $meta['cont_custom_date'] );
                $tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );
                $custom_dates = wp_list_pluck( $tf_tour_unserial_custom_date, 'date' );
            }else{
                $custom_dates = wp_list_pluck( $meta['cont_custom_date'], 'date' );
            }
            $people_counter = 0;
            if(!empty($meta['cont_custom_date'])){
                foreach($meta['cont_custom_date'] as $minmax){
                    // Max & Min People Check
                    if ( !empty($minmax['max_people']) && $minmax['max_people'] >= $total_people && $minmax['max_people'] != 0 && !empty($minmax['min_people']) && $minmax['min_people'] <= $total_people && $minmax['min_people'] != 0 ) {
                        $people_counter ++;
                    }
                }
            }
            if($people_counter > 0){
                foreach ( $custom_dates as $custom_date ) {
                    $show_continuous_tour = [];
                    foreach ( $period as $date ) {
                        $show_continuous_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $custom_date['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $custom_date['to'] ) );
                    }
                    if ( !in_array( 0, $show_continuous_tour ) ) {
                        if(!empty($startprice) && !empty($endprice)){
                            foreach($meta['cont_custom_date'] as $single_avail){
                                if(!empty($single_avail['adult_price'])){
                                    if($startprice<=$single_avail['adult_price'] && $single_avail['adult_price']<=$endprice){
                                        $has_tour = true; 
                                    }
                                }
                                if(!empty($single_avail['child_price'])){
                                    if($startprice<=$single_avail['child_price'] && $single_avail['child_price']<=$endprice){
                                        $has_tour = true; 
                                    }
                                }
                                if(!empty($single_avail['infant_price'])){
                                    if($startprice<=$single_avail['infant_price'] && $single_avail['infant_price']<=$endprice){
                                        $has_tour = true; 
                                    }
                                }
                                if(!empty($single_avail['group_price'])){
                                    if($startprice<=$single_avail['group_price'] && $single_avail['group_price']<=$endprice){
                                        $has_tour = true; 
                                    }
                                }
                            }
                        }else{
                            $has_tour = true;
                        }
                        break;
                    }
                }
            }

        } else {

            $people_counter = 0;

            // Max & Min People Check
            if ( !empty($meta['cont_max_people']) && $meta['cont_max_people'] >= $total_people && $meta['cont_max_people'] != 0 && !empty($meta['cont_min_people']) && $meta['cont_min_people'] <= $total_people && $meta['cont_min_people'] != 0 ) {
                $people_counter ++;
            }
            if($people_counter > 0){
                if(!empty($startprice) && !empty($endprice)){
                    if(!empty($meta['adult_price'])){
                        if($startprice<=$meta['adult_price'] && $meta['adult_price']<=$endprice){
                            $has_tour = true; 
                        }
                    }
                    if(!empty($meta['child_price'])){
                        if($startprice<=$meta['child_price'] && $meta['child_price']<=$endprice){
                            $has_tour = true; 
                        }
                    }
                    if(!empty($meta['infant_price'])){
                        if($startprice<=$meta['infant_price'] && $meta['infant_price']<=$endprice){
                            $has_tour = true; 
                        }
                    }
                    if(!empty($meta['group_price'])){
                        if($startprice<=$meta['group_price'] && $meta['group_price']<=$endprice){
                            $has_tour = true; 
                        }
                    }
                }else{
                    $has_tour = true;
                }
            }

        }

    }
    if ( $has_tour ) {
        
        $not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 0,
        );

    } else {
        $not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 1,
        );

    }
}

/**
 * Filter tours on search result page by without date dates set by backend
 *
 *
 * @author Jahid
 *
 * @param DatePeriod $period    collection of dates by user input;
 * @param array      $not_found collection of tour exists
 * @param array      $data      user input for sidebar form
 */
function tf_filter_tour_by_without_date( $period, &$total_posts, array &$not_found, array $data = [] ): void {
    if(isset($data[3]) && isset($data[4])){
        [$adults, $child, $check_in_out, $startprice, $endprice] = $data;
    }else{
        [$adults, $child, $check_in_out] = $data;
    }
    // Get tour meta options
    $meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

    // Set initial tour availability status
    $has_tour = false;

    if ( !empty($meta['type'] ) && $meta['type'] === 'fixed' ) {

        $show_fixed_tour    = [];
        
        if(!empty($startprice) && !empty($endprice)){
            if(!empty($meta['adult_price'])){
                if($startprice<=$meta['adult_price'] && $meta['adult_price']<=$endprice){
                    $has_tour = !in_array( 0, $show_fixed_tour );
                }
            }
            if(!empty($meta['child_price'])){
                if($startprice<=$meta['child_price'] && $meta['child_price']<=$endprice){
                    $has_tour = !in_array( 0, $show_fixed_tour );
                }
            }
            if(!empty($meta['infant_price'])){
                if($startprice<=$meta['infant_price'] && $meta['infant_price']<=$endprice){
                    $has_tour = !in_array( 0, $show_fixed_tour );
                }
            }
            if(!empty($meta['group_price'])){
                if($startprice<=$meta['group_price'] && $meta['group_price']<=$endprice){
                    $has_tour = !in_array( 0, $show_fixed_tour );
                }
            }
        }else{
            $has_tour = true;
        }

    }

    if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {

        $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
        if ($custom_availability) {

            if( !empty($meta['cont_custom_date']) && gettype($meta['cont_custom_date'])=="string" ){
                $tf_tour_unserial_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                }, $meta['cont_custom_date'] );
                $tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );
                $custom_dates = wp_list_pluck( $tf_tour_unserial_custom_date, 'date' );
            }else{
                $custom_dates = wp_list_pluck( $meta['cont_custom_date'], 'date' );
            }

            foreach ( $custom_dates as $custom_date ) {

                $show_continuous_tour = [];

                if ( !in_array( 0, $show_continuous_tour ) ) {
                    if(!empty($startprice) && !empty($endprice)){
                        if(!empty($meta['adult_price'])){
                            if($startprice<=$meta['adult_price'] && $meta['adult_price']<=$endprice){
                                $has_tour = true; 
                            }
                        }
                        if(!empty($meta['child_price'])){
                            if($startprice<=$meta['child_price'] && $meta['child_price']<=$endprice){
                                $has_tour = true; 
                            }
                        }
                        if(!empty($meta['infant_price'])){
                            if($startprice<=$meta['infant_price'] && $meta['infant_price']<=$endprice){
                                $has_tour = true; 
                            }
                        }
                        if(!empty($meta['group_price'])){
                            if($startprice<=$meta['group_price'] && $meta['group_price']<=$endprice){
                                $has_tour = true; 
                            }
                        }
                    }else{
                        $has_tour = true;
                    }
                    
                    break;

                }

            }

        } else {

            if(!empty($startprice) && !empty($endprice)){
                if(!empty($meta['adult_price'])){
                    if($startprice<=$meta['adult_price'] && $meta['adult_price']<=$endprice){
                        $has_tour = true; 
                    }
                }
                if(!empty($meta['child_price'])){
                    if($startprice<=$meta['child_price'] && $meta['child_price']<=$endprice){
                        $has_tour = true; 
                    }
                }
                if(!empty($meta['infant_price'])){
                    if($startprice<=$meta['infant_price'] && $meta['infant_price']<=$endprice){
                        $has_tour = true; 
                    }
                }
                if(!empty($meta['group_price'])){
                    if($startprice<=$meta['group_price'] && $meta['group_price']<=$endprice){
                        $has_tour = true; 
                    }
                }
            }else{
                $has_tour = true;
            }

        }

    }
    if ( $has_tour ) {
        
        $not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 0,
        );

    } else {
        $not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 1,
        );

    }
}

/*
 * Tour search ajax
 * @since 2.9.7
 * @author Foysal
 */
add_action( 'wp_ajax_tf_tour_search', 'tf_tour_search_ajax_callback' );
add_action( 'wp_ajax_nopriv_tf_tour_search', 'tf_tour_search_ajax_callback' );
if ( ! function_exists( 'tf_tour_search_ajax_callback' ) ) {
	function tf_tour_search_ajax_callback() {
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		if ( ! isset( $_POST['place'] ) || empty( $_POST['place'] ) ) {
			$response['message'] = esc_html__( 'Please enter your location', 'tourfic' );
		} elseif ( tfopt( 'date_tour_search' ) && ( ! isset( $_POST['check-in-out-date'] ) || empty( $_POST['check-in-out-date'] ) ) ) {
			$response['message'] = esc_html__( 'Please select a date', 'tourfic' );
		}

		if(tfopt( 'date_tour_search' )){
			if ( ! empty( $_POST['place'] ) && ! empty( $_POST['check-in-out-date'] ) ) {
				$response['query_string'] = str_replace( '&action=tf_tour_search', '', http_build_query( $_POST ) );
				$response['status']       = 'success';
			}
		} else {
			if ( ! empty( $_POST['place'] ) ) {
				$response['query_string'] = str_replace( '&action=tf_tour_search', '', http_build_query( $_POST ) );
				$response['status']       = 'success';
			}
		}

		echo json_encode( $response );
		wp_die();
	}
}