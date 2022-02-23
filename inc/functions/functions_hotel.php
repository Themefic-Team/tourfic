<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

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

/**
 * Single Hotel Sidebar Booking Form
 */
function tf_hotel_sidebar_booking_form( $placement = 'single' ) { ?>

	<!-- Start Booking widget -->
	<form id="tf-single-hotel-avail" class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off">

    <?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>

		<div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
                        <option value="1">1 adult</option>
                        <option value="2">2 adults</option>
                        <option value="3">3 adults</option>
                        <option value="4">4 adults</option>
                        <option value="5">5 adults</option>
                        <option value="6">6 adults</option>
                    </select>
				</div>
			</label>
		</div>

        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                <i class="fas fa-child"></i>
                    <select name="children" id="children" class="">
                        <option value="0">0 child</option>
                        <option value="1">1 child</option>
                        <option value="2">2 childrens</option>
                        <option value="3">3 childrens</option>
                        <option value="4">4 childrens</option>
                        <option value="5">5 childrens</option>
                    </select>
				</div>
			</label>
		</div>

		<div class="tf_booking-dates">
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <span class="tf-label">Check-in &amp; Check-out date</span>
                    <div class="tf_form-inner">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="Select Date" required>
                    </div>
			    </label>
		    </div>
		</div>

		<div class="tf_form-row">
			<?php
				$ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();
			?>
			<input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type" />
            <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>
			<button class="tf_button tf-submit" type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' );?></button>
		</div>

	</form>
    
    <script>
        (function ($) {
            $(document).ready(function () {

                $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                    enableTime: false,
                    mode: "range",
                    dateFormat: "Y/m/d",
                    allowInput: true,
                });

            });
        })(jQuery);
    </script>

	<?php if ( $placement == 'single' ) { ?>
		<?php if ( is_active_sidebar( 'tf_single_booking_sidebar' ) ) { ?>
		    <div id="tf__booking_sidebar">
		        <?php dynamic_sidebar( 'tf_single_booking_sidebar' ); ?>
		        <br>
		    </div>
		<?php } ?>
	<?php } else { ?>
		<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
		    <div id="tf__booking_sidebar">
		        <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
		        <br>
		    </div>
		<?php } ?>
	<?php } ?>

	<?php
}

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
    <div class="tf_room-table">
        <table class="availability-table">
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

                    if ($form_total_person < $total_person) {                                                                 
                ?>
                <tr>
                    <td class="description">
                        <div class="tf-room-type">
                            <div class="tf-room-title"><?php echo esc_html( $room['title'] ); ?></div>
                            <div class="bed-facilities"><?php echo $room['description']; ?></div>
                        </div>
                    </td>
                    <td class="details">

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
                        <?php }
                        if ($adult_number) { ?>
                        <div class="tf-tooltip tf-d-ib">
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
                        <form class="tf-room">
                            <?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
                            <div class="room-selection-wrap">
                                <select name="room-selected" id="room-selected">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
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
                                <button class="tf_button tf-room-book" type="submit">I'll reserve</button>
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


/**
 * Archive Hotel Sidebar Booking Form
 */
function tourfic_get_sidebar( $placement = 'single' ) { ?>

	<!-- Start Booking widget -->
	<form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off" action="<?php echo tourfic_booking_search_action(); ?>">

        <div class="tf_form-row">
            <label class="tf_label-row">
                <span class="tf-label">Enter Your Destination:</span>
                <div class="tf_form-inner">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="destination" required="" id="destination" class="" placeholder="Destination" value="">
                </div>
			</label>
		</div>

		<div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
                        <option value="1">1 adult</option>
                        <option value="2">2 adults</option>
                        <option value="3">3 adults</option>
                        <option value="4">4 adults</option>
                        <option value="5">5 adults</option>
                        <option value="6">6 adults</option>
                    </select>
				</div>
			</label>
		</div>

        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                <i class="fas fa-child"></i>
                    <select name="children" id="children" class="">
                        <option value="0">0 child</option>
                        <option value="1">1 child</option>
                        <option value="2">2 childrens</option>
                        <option value="3">3 childrens</option>
                        <option value="4">4 childrens</option>
                        <option value="5">5 childrens</option>
                    </select>
				</div>
			</label>
		</div>

		<div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-couch"></i>
                    <select name="room" id="room" class="">
                        <option value="1">1 room</option>
                        <option value="2">2 rooms</option>
                        <option value="3">3 rooms</option>
                        <option value="4">4 rooms</option>
                        <option value="5">5 rooms</option>
                    </select>
				</div>
			</label>
		</div>

		<div class="tf_booking-dates">
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <span class="tf-label">Check-in &amp; Check-out date</span>
                    <div class="tf_form-inner">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="Select Date" required>
                    </div>
			    </label>
		    </div>

			<div class="screen-reader-text">
				<?php //tourfic_booking_widget_field(
					// array(
					// 	'type'        => 'text',
					// 	'svg_icon'    => 'calendar_today',
					// 	'name'        => 'check-in-date',
					// 	'placeholder' => 'Check-in date',
					// 	'label'       => 'Check-in date',
					// 	'required'    => 'true',
					// 	'disabled'    => 'true',
					// 	'class'		  => 'tf-widget-check-in',
					// ));
				?>

				<?php //tourfic_booking_widget_field(
				// 	array(
				// 		'type'        => 'text',
				// 		'svg_icon'    => 'calendar_today',
				// 		'name'        => 'check-out-date',
				// 		'placeholder' => 'Check-out date',
				// 		'required'    => 'true',
				// 		'disabled'    => 'true',
				// 		'label'       => 'Check-out date',
				// 		'class'		  => 'tf-widget-check-out',
				// 	)
				// );?>
			</div>
		</div>

		<div class="tf_form-row">
			<?php
				$ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();
			?>
			<input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type" />
			<button class="tf_button tf-submit" type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' );?></button>
		</div>

	</form>
    
    <script>
        (function ($) {
            $(document).ready(function () {

                $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                    enableTime: false,
                    mode: "range",
                });

            });
        })(jQuery);
    </script>

	<?php if ( $placement == 'single' ) { ?>
		<?php if ( is_active_sidebar( 'tf_single_booking_sidebar' ) ) { ?>
		    <div id="tf__booking_sidebar">
		        <?php dynamic_sidebar( 'tf_single_booking_sidebar' ); ?>
		        <br>
		    </div>
		<?php } ?>
	<?php } else { ?>
		<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
		    <div id="tf__booking_sidebar">
		        <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
		        <br>
		    </div>
		<?php } ?>
	<?php } ?>

	<?php
}

/**
 * WooCommerce hotel Functions
 * 
 * @include
 */
require_once TF_INC_PATH . 'functions/woocommerce/wc-hotel.php';
?>