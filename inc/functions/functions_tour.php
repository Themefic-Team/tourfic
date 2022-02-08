<?php
defined( 'ABSPATH' ) || exit;

/**
 * Register post type: tf_tours
 * 
 * @since 1.0
 * @return void
 */
function register_tf_tours_post_type() {

    $tour_slug = apply_filters( 'tf_tours_slug', 'tour' );

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
        'rewrite'            => array( 'slug' => $tour_slug ),      
        'capability_type'    => array( 'tf_tours', 'tf_tourss' ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => apply_filters( 'tf_tours_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
    );

    register_post_type( 'tf_tours', apply_filters( 'tf_tour_post_type_args', $tour_args ) );
}
add_action( 'init', 'register_tf_tours_post_type' );

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
 * tour_destination
 */
function tf_tours_taxonomies_register() {

    /**
     * Taxonomy: tour_destination.
     */
    $tour_destination_slug = apply_filters( 'tour_destination_slug', 'tour-destination' );

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
        'rewrite'               => array('slug' => $tour_destination_slug),
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
    register_taxonomy( 'tour_destination', 'tf_tours', apply_filters( 'tour_destination_args', $tour_destination_args ) );

    /**
     * Taxonomy: tf_feature.
     */
    $labels = [
        "name"                       => __( "Tour Features", 'tourfic' ),
        "singular_name"              => __( "Tour Feature", 'tourfic' ),
        "menu_name"                  => __( "Tour Features", 'tourfic' ),
        "all_items"                  => __( "All Features", 'tourfic' ),
        "edit_item"                  => __( "Edit Feature", 'tourfic' ),
        "view_item"                  => __( "View Features", 'tourfic' ),
        "update_item"                => __( "Update Feature name", 'tourfic' ),
        "add_new_item"               => __( "Add new Feature", 'tourfic' ),
        "new_item_name"              => __( "New Feature name", 'tourfic' ),
        "parent_item"                => __( "Parent Feature", 'tourfic' ),
        "parent_item_colon"          => __( "Parent Feature:", 'tourfic' ),
        "search_items"               => __( "Search Features", 'tourfic' ),
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

    $feature_args = [
        "label"                 => __( "Tour Features", 'tourfic' ),
        "labels"                => $labels,
        "public"                => true,
        "publicly_queryable"    => true,
        "hierarchical"          => true,
        "show_ui"               => true,
        "show_in_menu"          => true,
        "show_in_nav_menus"     => true,
        "meta_box_cb"           => false,
        "query_var"             => true,
        "rewrite"               => ['slug' => 'tf_feature', 'with_front' => true],
        "show_admin_column"     => true,
        "show_in_rest"          => true,
        "rest_base"             => "tf_feature",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit"    => true,
    ];
    register_taxonomy( 'tf_feature', ['tf_tours'], apply_filters( 'tf_features_tax_args', $feature_args ) );

}
add_action( 'init', 'tf_tours_taxonomies_register' );

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
            'hide_empty' => false,
        ) );

        foreach ( $destination_terms as $destination_term ) {
            $destinations[] = $destination_term->name;
        }

        return $destinations;

    }
}


/**
 * Single Tour Booking Bar
 * 
 * Single Tour Page
 */
function tf_single_tour_booking_form( $post_id ) {
    
    $meta = get_post_meta( $post_id, 'tf_tours_option', true );
    $tour_type = !empty($meta['type']) ? $meta['type'] : '';

    if ($tour_type == 'fixed') {

        $departure_date = !empty($meta['fixed_availability']['date']['from']) ? $meta['fixed_availability']['date']['from'] : '';
        $return_date = !empty($meta['fixed_availability']['date']['to']) ? $meta['fixed_availability']['date']['to'] : '';
        $min_people = !empty($meta['fixed_availability']['min_seat']) ? $meta['fixed_availability']['min_seat'] : '';
        $max_people = !empty($meta['fixed_availability']['max_seat']) ? $meta['fixed_availability']['max_seat'] : '';

    } elseif ($tour_type == 'continuous') {

        $custom_avail = !empty($meta['custom_avail']) ? $meta['custom_avail'] : '';
        $disabled_day = !empty($meta['disabled_day']) ? $meta['disabled_day'] : '';
        $disable_range = !empty($meta['disable_range']) ? $meta['disable_range'] : '';
        $disable_specific = !empty($meta['disable_specific']) ? $meta['disable_specific'] : '';
        $disable_specific = str_replace(', ', '", "', $disable_specific);

        if ($custom_avail == true) {

            $cont_custom_date = !empty($meta['cont_custom_date']) ? $meta['cont_custom_date'] : '';

        }     

    }

	$tour_extras = isset($meta['tour-extra']) ? $meta['tour-extra'] : null;
	
    ob_start();
    ?>
        <div class="tf-tour-booking-wrap">
            <form class="tf_tours_booking">
                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M16.5 6a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0zM18 6A6 6 0 1 0 6 6a6 6 0 0 0 12 0zM3 23.25a9 9 0 1 1 18 0 .75.75 0 0 0 1.5 0c0-5.799-4.701-10.5-10.5-10.5S1.5 17.451 1.5 23.25a.75.75 0 0 0 1.5 0z"></path></svg>
                        </span>
                        <div class="adults-text">0 Adults</div>
                        <div class="person-sep"></div>
                        <div class="child-text">0 Children</div>
                        <div class="person-sep"></div>
                        <div class="infant-text">0 Infant</div>
                    </div>
                    <div class="tf_acrselection-wrap" style="display: none;">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label">Adults</div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="0" value="0">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label">Children</div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="childrens" id="children" min="0" value="0">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label">Infant</div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="infants" id="infant" min="0" value="0">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='tf_form-row'>
	    	        <label class='tf_label-row'>
	    		        <div class='tf_form-inner'>
                            <span class='icon'>
                                <?php tourfic_get_svg('calendar_today'); ?>
                            </span>
                            <input type='text' name='check-in-out-date' id='check-in-out-date' class='tours-check-in-out' onkeypress="return false;" placeholder='Select Date' value='' required />
				        </div>
			        </label>
		        </div>
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <script>
                    (function ($) {
                        $(document).ready(function () {

                            $("#check-in-out-date").flatpickr({  
                                enableTime: false,
                                dateFormat: "Y/m/d",                               

                            <?php if ($tour_type && $tour_type == 'fixed') { ?>

                                mode: "range",
                                defaultDate: ["<?php echo $departure_date; ?>", "<?php echo $return_date; ?>"],
                                enable: [
                                    {
                                        from: "<?php echo $departure_date; ?>",
                                        to: "<?php echo $return_date; ?>"
                                    }
                                ],

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
                            });

                        });
                    })(jQuery);
                </script>

                <?php if (defined( 'TF_PRO' ) && $tour_extras) { ?>
                <div class="tour-extra">
                    <a data-fancybox data-src="#tour-extra" href="javascript:;">Package <i class="far fa-plus-square"></i></a>
                    <div style="display: none;" id="tour-extra">
                        <div class="tour-extra-container">
                        <?php foreach( $tour_extras as $tour_extra ){ ?>
                            <div class="tour-extra-single">
                                <div class="tour-extra-left">
                                    <h4><?php _e( $tour_extra['title'] ); ?></h4>
                                    <?php if ($tour_extra['desc']) { ?><p><?php _e( $tour_extra['desc'] ); ?></p><?php } ?>
                                </div>
                                <div class="tour-extra-right">
                                    <span><?php _e( $tour_extra['price'] ); ?></span>
                                    <input type="checkbox" value="<?php _e( $tour_extra['price'] ); ?>" data-title="<?php _e( $tour_extra['title'] ); ?>">
                                </div>												
                            </div>					
                        <?php } ?>
                        </div>
                    </div>
                </div>	
                <?php } ?>	
                <?php echo tourfic_tours_booking_submit_button( "Book Now" ); ?>
            </form>
	    </div>
	<?php
return ob_get_clean();
}

/**
 * WooCommerce Tour Functions
 * 
 * @include
 */
require_once TF_INC_PATH . 'functions/woocommerce/wc-tour.php';
?>