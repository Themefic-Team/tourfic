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
	$apartment_slug = ! empty( get_option( 'apartment_slug' ) ) ? get_option( 'apartment_slug' ) : apply_filters( 'tf_apartment_slug', 'apartments' );

	$apartment_labels = apply_filters( 'tf_apartment_labels', array(
		'name'                  => _x( 'Apartments', 'post type general name', 'tourfic' ),
		'singular_name'         => _x( 'Apartment', 'post type singular name', 'tourfic' ),
		'add_new'               => _x( 'Add New', 'tourfic' ),
		'add_new_item'          => __( 'Add New Apartment', 'tourfic' ),
		'edit_item'             => __( 'Edit Apartment', 'tourfic' ),
		'new_item'              => __( 'New Apartment', 'tourfic' ),
		'all_items'             => __( 'All Apartment', 'tourfic' ),
		'view_item'             => __( 'View Apartment', 'tourfic' ),
		'view_items'            => __( 'View Apartments', 'tourfic' ),
		'search_items'          => __( 'Search Apartments', 'tourfic' ),
		'not_found'             => __( 'No apartments found', 'tourfic' ),
		'not_found_in_trash'    => __( 'No apartments found in the Trash', 'tourfic' ),
		'parent_item_colon'     => '',
		'menu_name'             => __( 'Apartments', 'tourfic' ),
		'featured_image'        => __( 'Apartment Featured Image', 'tourfic' ),
		'set_featured_image'    => __( 'Set Apartment Featured Image', 'tourfic' ),
		'remove_featured_image' => __( 'Remove Apartment Featured Image', 'tourfic' ),
		'use_featured_image'    => __( 'Use as Apartment Featured Image', 'tourfic' ),
		'attributes'            => __( 'Apartment Attributes', 'tourfic' ),
		'filter_items_list'     => __( 'Filter Apartment list', 'tourfic' ),
		'items_list_navigation' => __( 'Apartment list navigation', 'tourfic' ),
		'items_list'            => __( 'Apartment list', 'tourfic' )
	) );
	$apartment_args   = array(
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

/**
 * Register taxonomies for tf_apartment
 *
 * apartment_location, apartment_feature
 */
function tf_apartment_taxonomies_register() {

	/**
	 * Taxonomy: apartment_location
	 */
	$apartment_location_slug = apply_filters( 'apartment_location_slug', 'apartment-location' );

	$apartment_location_labels = array(
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

	$apartment_location_args = array(
		'labels'                => $apartment_location_labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => $apartment_location_slug, 'with_front' => false ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'apartment_location',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit'    => true,
//		'capabilities'          => array(
//			'assign_terms' => 'edit_tf_hotel',
//			'edit_terms'   => 'edit_tf_hotel',
//		),
	);
	register_taxonomy( 'apartment_location', 'tf_apartment', apply_filters( 'apartment_location_args', $apartment_location_args ) );

	/**
	 * Taxonomy: apartment_feature
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
		"rewrite"               => [ 'slug' => 'apartment_feature', 'with_front' => true ],
		"show_admin_column"     => true,
		"show_in_rest"          => true,
		"rest_base"             => "apartment_feature",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => true,
//		'capabilities'          => array(
//			'assign_terms' => 'edit_tf_apartment',
//			'edit_terms'   => 'edit_tf_apartment',
//		),
	];
	register_taxonomy( 'apartment_feature', 'tf_apartment', apply_filters( 'apartment_feature_tax_args', $args ) );

}

add_action( 'init', 'tf_apartment_taxonomies_register' );

/**
 * Flushing Rewrite on Tourfic Activation
 *
 * tf_apartment post type
 * apartment_feature taxonomy
 */
function tf_apartment_rewrite_flush() {

	register_tf_apartment_post_type();
	tf_apartment_taxonomies_register();
	flush_rewrite_rules();

}

register_activation_hook( TF_PATH . 'tourfic.php', 'tf_apartment_rewrite_flush' );

/**
 * Single Hotel Sidebar Booking Form
 */
function tf_apartment_single_booking_form( $b_check_in = '', $b_check_out = '' ) {

	$meta            = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
	$max_adults      = ! empty( $meta['max_adults'] ) ? $meta['max_adults'] : '';
	$max_children    = ! empty( $meta['max_children'] ) ? $meta['max_children'] : '';
	$max_infants     = ! empty( $meta['max_infants'] ) ? $meta['max_infants'] : '';
	$price_per_night = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
	$service_fee     = ! empty( $meta['service_fee'] ) ? $meta['service_fee'] : 0;
	$cleaning_fee    = ! empty( $meta['cleaning_fee'] ) ? $meta['cleaning_fee'] : 0;
	?>

    <!-- Start Booking widget -->
    <form id="tf-apartment-booking" class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off">

		<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>

        <div class="tf-apartment-form-header">
            <h3 class="tf-apartment-price-per-night"><?php echo wc_price( $price_per_night ) ?><span><?php _e( '/per night', 'tourfic' ) ?></span></h3>
        </div>

        <div class="tf_form-row">
            <label class="tf_label-row">
                <span class="tf-label"><?php _e( 'Guests', 'tourfic' ); ?></span>
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <div class="tf_selectperson-wrap">
                        <div class="tf_input-inner">
                            <div class="adults-text"><?php _e( '1 Adults', 'tourfic' ); ?></div>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php _e( '0 Children', 'tourfic' ); ?></div>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php _e( '0 Infant', 'tourfic' ); ?></div>
                        </div>
                        <div class="tf_acrselection-wrap">
                            <div class="tf_acrselection-inner">
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="1" value="1" max="<?php echo esc_attr( $max_adults ); ?>"/>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="0" max="<?php echo esc_attr( $max_children ); ?>"/>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="infant" id="infant" min="0" value="0" max="<?php echo esc_attr( $max_infants ); ?>"/>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <div class="tf_booking-dates">
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <span class="tf-label"><?php _e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                    <div class="tf_form-inner">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                               placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required>
                    </div>
                </label>
            </div>
        </div>

        <div class="tf_form-row">
			<?php $ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type(); ?>
            <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
            <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>

            <div class="tf-btn">
                <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Reserve', 'tourfic' ); ?></button>
            </div>
        </div>

        <ul class="tf-apartment-price-list">
            <li class="total-days-price-wrap">
                <span class="total-days"></span>
                <span class="days-total-price"></span>
            </li>
            <li class="service-fee-wrap">
                <span class="service-fee-label"><?php _e( 'Service Fee', 'tourfic' ); ?></span>
                <span class="service-fee"></span>
            </li>

			<?php if ( $cleaning_fee ): ?>
                <li class="cleaning-fee-wrap">
                    <span class="cleaning-fee-label"><?php _e( 'Cleaning Fee', 'tourfic' ); ?></span>
                    <span class="cleaning-fee"><?php echo wc_price( $cleaning_fee ); ?></span>
                </li>
			<?php endif; ?>

            <li class="total-price-wrap">
                <span class="total-price-label"><?php _e( 'Total Price', 'tourfic' ); ?></span>
                <span class="total-price"></span>
            </li>
        </ul>

    </form>

    <script>
        (function ($) {
            $(document).ready(function () {

                const checkinoutdateange = flatpickr("#tf-apartment-booking #check-in-out-date", {
                    enableTime: false,
                    mode: "range",
                    minDate: "today",
                    dateFormat: "Y/m/d",
                    onReady: function (selectedDates, dateStr, instance) {
                        //minimum 5 days
                    },
                    onChange: function (selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');

                        //calculate total days
                        if (selectedDates[0] && selectedDates[1]) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                            var price_per_night = <?php echo $price_per_night; ?>;
                            var total_price = price_per_night * days;
                            var total_price_html = '<?php echo wc_price( 0 ); ?>';
                            if (total_price > 0) {
                                total_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0', total_price);
                            }
                            $('.total-days-price-wrap .total-days').html(<?php echo $price_per_night; ?> +' x ' + days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                            $('.total-days-price-wrap .days-total-price').html(total_price_html);
                        }
                    },

					<?php
					// Flatpickt locale for translation
					tf_flatpickr_locale();
					?>
                });

            });
        })(jQuery);
    </script>

	<?php
}
