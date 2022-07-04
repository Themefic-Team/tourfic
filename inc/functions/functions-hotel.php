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
        list( $form_start, $form_end ) = explode( ' to ', $form_check_in_out );
    }
    $form_check_in = $form_start;
    $form_start = date( 'Y/m/d', strtotime( $form_start . ' +1 day' ) );
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

    <div class="listing-title">
        <h4><?php esc_html_e( 'Availability', 'tourfic' ); ?></h4>
    </div>
    <div class="tf_room-table hotel-room-wrap">
        <table class="availability-table">
            <thead>
                <tr>
                    <th class="description"><?php _e( 'Room Details', 'tourfic' ); ?></th>
                    <th class="pax"><?php _e( 'Pax', 'tourfic' ); ?></th>
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

                            $days = iterator_count( $period );

                            /**
                             * Set room availability
                             */
                            $unique_id          = !empty($room['unique_id']) ? $room['unique_id'] : '';
                            $order_ids          = !empty($room['order_id']) ? $room['order_id'] : '';
                            $num_room_available = !empty($room['num-room']) ? $room['num-room'] : '1';
                            $reduce_num_room    = !empty($room['reduce_num_room']) ? $room['reduce_num_room'] : false;
                            $number_orders      = '0';
                            $avil_by_date       = !empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;      // Room Available by date enabled or  not ?
                            if($avil_by_date) {
                                $repeat_by_date = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
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

                                                    if( strtotime( $single_date_range["availability"]["from"] ) <= strtotime( $form_start ) && strtotime( $single_date_range["availability"]["to"] ) >= strtotime( $form_end ) ) {
    
                                                        if( strtotime( $single_date_range["availability"]["from"] ) <= $order_check_in_date && strtotime( $single_date_range["availability"]["to"] ) >= $order_check_out_date ) {

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
                                
                                # Calculate available room number after order
                                $num_room_available = $num_room_available - $number_orders; // Calculate
                                $num_room_available = max($num_room_available, 0); // If negetive value make that 0

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

                                        return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;

                                    } ) );

                                    if ( is_iterable($available_rooms) && count( $available_rooms ) >=1) {
                                        
                                        $room_price    = !empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_price;
                                        $adult_price   = !empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_adult_price;
                                        $child_price   = !empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room['child_price'];
                                        $price_by_date = $pricing_by == '1' ? $room_price : (  ( $adult_price * $form_adult ) + ( $child_price * $form_child ) );
                                        $price += $price_by_date;
                                        $number_of_rooms = !empty($available_rooms[0]['num-room']) ? $available_rooms[0]['num-room'] : $room['num-room'];                                     
                                        $has_room[] = 1; 

                                    } else $has_room[] = 0;

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
                   
                                if ($pricing_by == '1') {
                                    $price_by_date = $room_price;
                                } else {
                                    $price_by_date = (($room_adult_price * $form_adult) + ($room_child_price * $form_child));
                                }

                                $price =  $room['price_multi_day'] == '1' ? $price_by_date * $days : $price_by_date;

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
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">

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
            <button class="tf_button tf-submit" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
        </div>

    </div>

    </form>

    <script>
    (function($) {
        $(document).ready(function() {

            $(".tf_booking-widget #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                allowInput: true,
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

    // Adults
    $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
    // children
    $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
    // Check-in & out date
    $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';

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
                        echo '<option value="1">1 ' .__("Adult", "tourfic"). '</option>';
                        
                        foreach (range(2,8) as $value) {
                            $selected = $value == $adults ? 'selected' : null;
                            echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __("Adults", "tourfic") . '</option>';
                        }
                        ?>
                        
                    </select>
                </div>
            </label>
        </div>
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-child"></i>
                    <select name="children" id="children" class="">
                        <?php
                        echo '<option value="0">0 ' .__("Children", "tourfic"). '</option>';
                        
                        foreach (range(1,8) as $value) {
                            $selected = $value == $child ? 'selected' : null;
                            echo '<option ' .$selected. ' value="' .$value. '">' . $value . ' ' . __("Children", "tourfic") . '</option>';
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
            <button class="tf_button tf-submit"
                type="submit"><?php esc_html_e( 'Booking Availability', 'tourfic' );?></button>
        </div>
    
    </form>
    
    <script>
    (function($) {
        $(document).ready(function() {
    
            $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                allowInput: true,
                minDate: "today",
                <?php
                // Flatpickt locale for translation
                tf_flatpickr_locale();
                ?>
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
function tf_hotel_archive_single_item($adults='', $child='', $room='', $check_in_out='') {

    // get post id
    $post_id = get_the_ID();
    //Get hotel_feature
    $features = !empty(get_the_terms( $post_id, 'hotel_feature' )) ? get_the_terms( $post_id, 'hotel_feature' ) : '';
    
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

    // Check room check in/out time
    $room_date_matched = array();
    if(!empty($check_in_out)) {
        if(!empty($b_rooms)) {
            $b_room_id = -1;
            foreach ($b_rooms as $b_room) {
                
                $b_room_id++;

                $enable = !empty($b_room['enable']) ? $b_room['enable'] : '';

                // Check if room is enabled
                if ($enable == '1') {

                    $b_check_in = !empty($b_room['availability']['from']) ? $b_room['availability']['from'] : '';
                    if($b_check_in) {
                        $b_check_in_stt = strtotime($b_check_in);
                    }
                    $b_check_out = !empty($b_room['availability']['to']) ? $b_room['availability']['to'] : '';
                    if($b_check_out) {
                        $b_check_out_stt = strtotime($b_check_out);
                    }

                    if(empty($b_check_in) || empty($b_check_out) || ($form_check_in_stt >= $b_check_in_stt && $form_check_out_stt <= $b_check_out_stt)) {
                        array_push($room_date_matched, 'yes');                
                    }

                }
            }
        } else {
            array_push($room_date_matched, 'yes'); 
        }
    } else {
        array_push($room_date_matched, 'yes'); 
    }
    if(!empty($room_date_matched)) {
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
                                echo '<div class="tf_map-link">';
                                echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' .$address. '</span>';
                                echo '</div>';
                            }
                            ?>	                    
                        </div>
                        <?php tf_archive_single_rating();?>
                    </div>
                    
                    <div class="sr_rooms_table_block">
                        <div class="room_details">
                            <div class="featuredRooms">
                                <div class="prco-ltr-right-align-helper">
                                    <div class="tf-archive-shortdesc">
                                        <?php echo substr(wp_strip_all_tags(get_the_content()), 0, 200). '...'; ?>
                                </div>
                                </div>
                                <div class="roomNameInner">
                                    <div class="room_link">
                                        <div class="roomrow_flex">
                                            <?php if( $features ) { ?>
                                            <div class="roomName_flex">
                                                <strong><?php _e('Features', 'tourfic'); ?></strong>
                                                <ul class="tf-archive-desc">
                                                    <?php foreach($features as $feature) {
                                                    $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'hotel_feature', true );
                                                    $f_icon_type = !empty($feature_meta['icon-type']) ? $feature_meta['icon-type'] : '';
                                                    if ($f_icon_type == 'fa') {
                                                        $feature_icon = '<i class="' .$feature_meta['icon-fa']. '"></i>';
                                                    } elseif ($f_icon_type == 'c') {
                                                        $feature_icon = '<img src="' .$feature_meta['icon-c']["url"]. '" style="width: ' .$feature_meta['dimention']["width"]. 'px; height: ' .$feature_meta['dimention']["width"]. 'px;" />';
                                                    } ?>
                                                    <li class="tf-tooltip">
                                                        <?php echo $feature_icon; ?>
                                                        <div class="tf-top">
                                                            <?php echo $feature->name; ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <?php } ?>
                                            <div class="roomPrice roomPrice_flex sr_discount">
                                                <div class="availability-btn-area">
                                                    <a href="<?php echo $url; ?>" class="button tf_button"><?php esc_html_e( 'Details', 'tourfic' );?></a>
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
    [$adults, $child, $room, $check_in_out] = $data;

    // Get hotel meta options
    $meta = get_post_meta(get_the_ID(), 'tf_hotel', true);
    // Remove disabled rooms
    $meta = array_filter($meta['room'], function ($value) {
        return !empty($value) && $value['enable'] != '0';
    });

    // If no room return
    if (empty($meta)) {
        return;
    }

    // Set initial room availability status
    $has_hotel = false;

    /**
     * Adult Number Validation
     */
    $back_adults = array_column($meta, 'adult');
    $adult_counter = 0;
    foreach($back_adults as $back_adult) {
        if($back_adult >= $adults) {
            $adult_counter++;
        }
    }

    /**
     * Child Number Validation
     */
    $back_childs = array_column($meta, 'adult');
    $child_counter = 0;
    foreach($back_childs as $back_child) {
        if($back_child >= $child) {
            $child_counter++;
        }
    }

    // If adult and child number validation is true proceed
    if($adult_counter > 0 && $child_counter > 0) {

        // Check custom date range status of room
        $avil_by_date = array_column($meta, 'avil_by_date');

        // Check if any room available without custom date range
        if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) ) {

            $has_hotel = true; // Show that hotel

        } else {
            // If all the room has custom date range then filter the rooms by date
            
            // Get custom date range repeater
            $dates = array_column($meta, 'repeat_by_date');

            // If no date range return
            if (empty($dates)) {
                return;
            }

            // Initial available dates array
            $availability_dates = [];

            // Run loop through custom date range repeater and filter out only the dates
            foreach ($dates as $date) {
                $availability_dates[] = array_column($date, 'availability');
            }    

            // Run loop through custom dates & set custom dates on a single array
            foreach (tf_array_flatten($availability_dates, 1) as $dates) {

                //Initial matching date array
                $show_hotel = [];

                // Check if any date range match with search form date range and set them on array
                if(!empty($period)) {
                    foreach ( $period as $date ) {

                        $show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
    
                    }
                }              

                // If any date range matches show hotel
                if ( !in_array( 0, $show_hotel ) ) {
                    $has_hotel = true;
                    break;
                }

            }

        }

    }

    // Conditional hotel showing
    if ( $has_hotel ) {

        if ( !empty( $data ) ) {
            [$adults, $child, $room, $check_in_out] = $data;
            tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
        } else {
            tf_hotel_archive_single_item();
        }

        $not_found[] = 0;

    } else {

        $not_found[] = 1;
        
    }

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
