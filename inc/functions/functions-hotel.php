<?php
// don't load directly
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
        'menu_position'      => 25,
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
 */
add_action( 'wp_ajax_tf_room_availability', 'tf_room_availability_callback' );
add_action( 'wp_ajax_nopriv_tf_room_availability', 'tf_room_availability_callback' );
function tf_room_availability_callback() {

    // Check nonce security
    if ( !isset( $_POST['tf_room_avail_nonce'] ) || !wp_verify_nonce( $_POST['tf_room_avail_nonce'], 'check_room_avail_nonce' ) ) {
        return;
    }

    $form_post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : '';
    $form_adult = isset($_POST['adult']) ? sanitize_text_field($_POST['adult']) : '';
    $form_child = isset($_POST['child']) ? sanitize_text_field($_POST['child']) : '';
    $form_total_person = $form_adult + $form_child;
    $form_check_in_out = isset($_POST['check_in_out']) ? sanitize_text_field($_POST['check_in_out']) : '';
    if ($form_check_in_out) {
        $form_check_in = substr($form_check_in_out,0,10);
        $form_check_in_stt = strtotime($form_check_in);
        $form_check_out = substr($form_check_in_out,14,10);
        $form_check_out_stt = strtotime($form_check_out);
    } 

    $meta  = get_post_meta( $form_post_id, 'tf_hotel', true );
    $rooms = !empty($meta['room']) ? $meta['room'] : '';
    $locations = get_the_terms( $form_post_id, 'hotel_location' );
    if ($locations) {
        $first_location_name = $locations[0]->name;
    }
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
            <!-- Start Single Room -->
            <?php 
                $room_id = -1;
                foreach ($rooms as $room) {
                    
                    $room_id++;

                    $enable = !empty($room['enable']) ? $room['enable'] : '';

                    if ($enable == '1') {

                        $footage = !empty($room['footage']) ? $room['footage'] : '';
                        $bed = !empty($room['bed']) ? $room['bed'] : '';
                        $adult_number = !empty($room['adult']) ? $room['adult'] : '0';
                        $child_number = !empty($room['child']) ? $room['child'] : '0';
                        $total_person = $adult_number + $child_number;	
                        $pricing_by = !empty($room['pricing-by']) ? $room['pricing-by'] : '';

                    if ($form_total_person <= $total_person) {                                                                 
                ?>
            <tr>
                <td class="description">
                    <div class="tf-room-type">
                        <div class="tf-room-title"><?php echo esc_html( $room['title'] ); ?></div>
                        <div class="bed-facilities"><?php echo $room['description']; ?></div>
                    </div>

                    <div class="tf-room-title">
                        <?php esc_html_e( 'Key Features', 'tourfic' ); ?>
                    </div>

                    <?php if ($footage) { ?>
                    <div class="tf-tooltip tf-d-ib">
                        <div class="room-detail-icon">
                            <span class="room-icon-wrap"><i class="fas fa-ruler-combined"></i></span>
                            <span class="icon-text tf-d-b"><?php echo $footage; ?> sft</span>
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

                    <div class="room-features">
                        <div class="tf-room-title"><?php esc_html_e( 'Amenities', 'tourfic' ); ?></div>
                        <ul class="room-feature-list">

                            <?php foreach ($room['features'] as $feature) {

                                    $room_f_meta = get_term_meta( $feature, 'hotel_feature', true );

                                    if ($room_f_meta['icon-type'] == 'fa') {
                                        $room_feature_icon = '<i class="' .$room_f_meta['icon-fa']. '"></i>';
                                    } elseif ($room_f_meta['icon-type'] == 'c') {
                                        $room_feature_icon = '<img src="' .$room_f_meta['icon-c']["url"]. '" style="min-width: ' .$room_f_meta['dimention']["width"]. 'px; height: ' .$room_f_meta['dimention']["width"]. 'px;" />';
                                    }

                                    $room_term = get_term( $feature ); ?>
                            <li class="tf-tooltip">
                                <?php echo $room_feature_icon; ?>
                                <div class="tf-top">
                                    <?php echo $room_term->name; ?>
                                    <i class="tool-i"></i>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </td>
                <td class="pax">

                    <?php if ($adult_number) { ?>
                    <div class="tf-tooltip tf-d-b">
                        <div class="room-detail-icon">
                            <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                            <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                        </div>
                        <div class="tf-top">
                            <?php _e( 'No. Adults', 'tourfic' ); ?>
                            <i class="tool-i"></i>
                        </div>
                    </div>
                    <?php }
                        if ($child_number) { ?>
                    <div class="tf-tooltip tf-d-b">
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
                </td>
                <td class="pricing">
                    <div class="tf-price-column">
                        <?php if ($pricing_by == '1') { ?>
                        <span class="tf-price"><?php echo wc_price( $room['price'] ); ?></span>
                        <div class="price-per-night"><?php esc_html_e( 'per night', 'tourfic' ); ?></div>
                        <?php } elseif ($pricing_by == '2') { ?>
                        <span class="tf-price"><?php echo wc_price( $room['adult_price'] ); ?></span>
                        <div class="price-per-night"><?php esc_html_e( 'per person/night', 'tourfic' ); ?></div>
                        <?php } ?>
                    </div>
                </td>
                <td class="reserve">
                    <form class="tf-room">
                        <?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
                        <div class="room-selection-wrap">
                            <select name="hotel_room_selected" id="hotel-room-selected">
                                <?php
                                foreach (range(1,8) as $value) {
                                    echo '<option>' . $value . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="room-submit-wrap">
                            <input type="hidden" name="post_id" value="<?php echo $form_post_id; ?>">
                            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
                            <input type="hidden" name="location" value="<?php echo $first_location_name; ?>">
                            <input type="hidden" name="adult" value="<?php echo $form_adult; ?>">
                            <input type="hidden" name="child" value="<?php echo $form_child; ?>">
                            <input type="hidden" name="check_in_date" value="<?php echo $form_check_in; ?>">
                            <input type="hidden" name="check_out_date" value="<?php echo $form_check_out; ?>">
                            <button class="hotel-room-book" type="submit">I'll reserve</button>
                        </div>
                        <div class="tf_desc"></div>
                    </form>
                </td>
            </tr>
            <?php } }
            } ?>
        </tbody>
    </table>
</div>

<?php
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
                    <?php echo tourfic_get_svg('person'); ?>
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
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e('Check-in - Check-out', 'tourfic'); ?>">
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
function tf_hotel_sidebar_booking_form() {

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
    //Get hotel meta values
    $meta = get_post_meta( get_the_ID(), 'tf_hotel', true );
    // Location
    $address  = !empty($meta['address']) ? $meta['address'] : '';

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
                            echo '<div class="tf_map-link">';
                            echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' .$address. '</span>';
                            echo '</div>';
                        }
                        ?>	                    
					</div>
					<?php tourfic_item_review_block();?>
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
?>
