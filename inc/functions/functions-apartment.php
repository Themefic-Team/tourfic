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
		'supports'           => apply_filters( 'tf_apartment_supports', array(
			'title',
			'editor',
			'thumbnail',
			'comments',
			'author'
		) ),
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
function tf_apartment_single_booking_form( $comments, $disable_review_sec ) {

	$meta             = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
	$max_adults       = ! empty( $meta['max_adults'] ) ? $meta['max_adults'] : '';
	$max_children     = ! empty( $meta['max_children'] ) ? $meta['max_children'] : '';
	$max_infants      = ! empty( $meta['max_infants'] ) ? $meta['max_infants'] : '';
	$price_per_night  = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
	$weekly_discount  = ! empty( $meta['weekly_discount'] ) ? $meta['weekly_discount'] : 0;
	$monthly_discount = ! empty( $meta['monthly_discount'] ) ? $meta['monthly_discount'] : 0;
	$service_fee      = ! empty( $meta['service_fee'] ) ? $meta['service_fee'] : 0;
	$cleaning_fee     = ! empty( $meta['cleaning_fee'] ) ? $meta['cleaning_fee'] : 0;
	$booked_dates     = tf_apartment_booked_days( get_the_ID() );
	?>

    <!-- Start Booking widget -->
    <form id="tf-apartment-booking" class="tf-apartment-side-booking" method="get" autocomplete="off">
        <h4><?php _e( 'Book your Apartment', 'tourfic' ) ?></h4>
        <div class="tf-apartment-form-header">
            <h3 class="tf-apartment-price-per-night">
                <span class="tf-apartment-base-price"><?php echo wc_price( $price_per_night ) ?></span>
                <span><?php _e( '/per night', 'tourfic' ) ?></span>
            </h3>
			<?php if ( $comments && ! $disable_review_sec == '1' ): ?>
                <div class="tf-top-review">
                    <a href="#tf-review">
                        <div class="tf-single-rating">
                            <i class="fas fa-star"></i> <span><?php echo tf_total_avg_rating( $comments ); ?></span>
                            (<?php tf_based_on_text( count( $comments ) ); ?>)
                        </div>
                    </a>
                </div>
			<?php endif; ?>
        </div>

        <div class="tf-apartment-form-fields">
            <div class="tf_booking-dates">
                <div class="tf-check-in-date">
                    <label class="tf_label-row">
                        <span class="tf-label"><?php _e( 'Check in', 'tourfic' ); ?></span>
                        <input type="text" name="check-in-date" id="check-in-date" onkeypress="return false;"
                               placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in ) ? 'value="' . $check_in . '"' : '' ?>
                               required>
                    </label>
                </div>
                <div class="tf-check-out-date">
                    <label class="tf_label-row">
                        <span class="tf-label"><?php _e( 'Check out', 'tourfic' ); ?></span>
                        <input type="text" name="check-out-date" id="check-out-date" onkeypress="return false;"
                               placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in ) ? 'value="' . $check_in . '"' : '' ?>
                               required>
                    </label>
                </div>
            </div>

            <div class="tf_form-row tf-apartment-guest-row">
                <label class="tf_label-row">
                    <span class="tf-label"><?php _e( 'Guests', 'tourfic' ); ?></span>
                    <div class="tf_form-inner">
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
                                            <input type="number" name="adults" id="adults" min="1" value="1"
                                                   max="<?php echo esc_attr( $max_adults ); ?>"/>
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="children" id="children" min="0" value="0"
                                                   max="<?php echo esc_attr( $max_children ); ?>"/>
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="infant" id="infant" min="0" value="0"
                                                   max="<?php echo esc_attr( $max_infants ); ?>"/>
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="tf_form-row">
			<?php $ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type(); ?>
            <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
            <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>

            <div class="tf-btn">
                <button class="tf_button tf-submit btn-styled tf_button_blue"
                        type="submit"><?php esc_html_e( 'Reserve', 'tourfic' ); ?></button>
            </div>
        </div>

        <ul class="tf-apartment-price-list">
            <li class="total-days-price-wrap" style="display: none">
                <span class="total-days tf-price-list-label"></span>
                <span class="days-total-price tf-price-list-price"></span>
            </li>

			<?php if ( ! empty( $weekly_discount ) ): ?>
                <li class="weekly-discount-wrap" style="display: none">
                    <span class="weekly-discount-label tf-price-list-label"><?php _e( 'Weekly discount', 'tourfic' ); ?></span>
                    <span class="weekly-discount tf-price-list-price"></span>
                </li>
			<?php endif; ?>

			<?php if ( ! empty( $monthly_discount ) ): ?>
                <li class="monthly-discount-wrap" style="display: none">
                    <span class="monthly-discount-label tf-price-list-label"><?php _e( 'Monthly discount', 'tourfic' ); ?></span>
                    <span class="monthly-discount tf-price-list-price"></span>
                </li>
			<?php endif; ?>

			<?php if ( ! empty( $service_fee ) ): ?>
                <li class="service-fee-wrap" style="display: none">
                    <span class="service-fee-label tf-price-list-label"><?php _e( 'Service Fee', 'tourfic' ); ?></span>
                    <span class="service-fee tf-price-list-price"></span>
                </li>
			<?php endif; ?>

			<?php if ( ! empty( $cleaning_fee ) ): ?>
                <li class="cleaning-fee-wrap" style="display: none">
                    <span class="cleaning-fee-label tf-price-list-label"><?php _e( 'Cleaning Fee', 'tourfic' ); ?></span>
                    <span class="cleaning-fee tf-price-list-price"><?php echo wc_price( $cleaning_fee ); ?></span>
                </li>
			<?php endif; ?>

            <li class="total-price-wrap" style="display: none">
                <span class="total-price-label tf-price-list-label"><?php _e( 'Total Price', 'tourfic' ); ?></span>
                <span class="total-price"></span>
            </li>
        </ul>

		<?php wp_nonce_field( 'tf_apartment_booking', 'tf_apartment_nonce' ); ?>
    </form>

    <script>
        (function ($) {
            $(document).ready(function () {

                const checInDate = flatpickr("#tf-apartment-booking #check-in-date", {
                    enableTime: false,
                    minDate: "today",
                    dateFormat: "Y/m/d",
                    disable: [
						<?php foreach ( $booked_dates as $booked_date ) : ?>
                        {
                            from: "<?php echo $booked_date['check_in']; ?>",
                            to: "<?php echo $booked_date['check_out']; ?>"
                        },
						<?php endforeach; ?>
                    ],
                    onChange: function (inSelectedDates, dateStr, instance) {
                        const checkOutDate = flatpickr("#tf-apartment-booking #check-out-date", {
                            enableTime: false,
                            minDate: new Date(inSelectedDates).fp_incr(1),
                            dateFormat: "Y/m/d",
                            disable: [
								<?php foreach ( $booked_dates as $booked_date ) : ?>
                                {
                                    from: "<?php echo $booked_date['check_in']; ?>",
                                    to: "<?php echo $booked_date['check_out']; ?>"
                                },
								<?php endforeach; ?>
                            ],
                            onChange: function (outSelectedDates, dateStr, instance) {
								<?php if ( ! empty( $price_per_night ) ): ?>
                                //calculate total days
                                if (inSelectedDates[0] && outSelectedDates[0]) {
                                    var diff = Math.abs(outSelectedDates[0] - inSelectedDates[0]);
                                    var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                                    if (days > 0) {
                                        var price_per_night = <?php echo $price_per_night; ?>;
                                        var wc_price_per_night = '<?php echo wc_price( $price_per_night ); ?>';
                                        var total_price = price_per_night * days;
                                        var total_price_html = '<?php echo wc_price( 0 ); ?>';
                                        if (total_price > 0) {
                                            $('.total-days-price-wrap').show();
                                            total_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0', total_price);
                                        }
                                        $('.total-days-price-wrap .total-days').html(wc_price_per_night + ' x ' + days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                        $('.total-days-price-wrap .days-total-price').html(total_price_html);

                                        //weekly discount (if more than 7 days)
                                        let base_price_wrapper = $('.tf-apartment-base-price');
                                        if (days >= 30) {
                                            $('.weekly-discount-wrap').hide();
                                            var monthly_discount = <?php echo $monthly_discount; ?>;
                                            var monthly_discount_html = '<?php echo wc_price( 0 ); ?>';
                                            if (monthly_discount > 0) {
                                                $('.monthly-discount-wrap').show();
                                                monthly_discount_html = '<?php echo wc_price( 0 ); ?>'.replace('0', monthly_discount * days);
                                            }

                                            $('.monthly-discount-wrap .monthly-discount').html('-' + monthly_discount_html);
                                            let base_price = (total_price - (monthly_discount * days)) / days;
                                            base_price_wrapper.html('<?php echo wc_price( 0 ); ?>'.replace('0', base_price));
                                        } else if (days >= 7) {
                                            $('.monthly-discount-wrap').hide();
                                            var weekly_discount = <?php echo $weekly_discount; ?>;
                                            var weekly_discount_html = '<?php echo wc_price( 0 ); ?>';
                                            if (weekly_discount > 0) {
                                                $('.weekly-discount-wrap').show();
                                                weekly_discount_html = '<?php echo wc_price( 0 ); ?>'.replace('0', weekly_discount * days);
                                            }

                                            $('.weekly-discount-wrap .weekly-discount').html('-' + weekly_discount_html);
                                            let base_price = (total_price - (weekly_discount * days)) / days;
                                            base_price_wrapper.html('<?php echo wc_price( 0 ); ?>'.replace('0', base_price));
                                        } else {
                                            $('.weekly-discount-wrap').hide();
                                            $('.monthly-discount-wrap').hide();

                                            base_price_wrapper.html(wc_price_per_night);
                                        }

                                        //service fee per night
										<?php if ( ! empty( $service_fee ) ): ?>
                                        var service_fee = <?php echo $service_fee; ?>;
                                        var service_fee_html = '<?php echo wc_price( 0 ); ?>';
                                        if (service_fee > 0) {
                                            $('.service-fee-wrap').show();
                                            service_fee_html = '<?php echo wc_price( 0 ); ?>'.replace('0', service_fee * days);
                                        }
                                        $('.service-fee-wrap .service-fee').html(service_fee_html);
										<?php endif; ?>

                                        //cleaning fee
										<?php if ( ! empty( $cleaning_fee ) ): ?>
                                        $('.cleaning-fee-wrap').show();
										<?php endif; ?>

                                        //total price
                                        var total_price_html = '<?php echo wc_price( 0 ); ?>';
                                        if (total_price > 0) {
                                            $('.total-price-wrap').show();
                                            total_price = total_price + (service_fee * days) + <?php echo $cleaning_fee; ?>;
                                            console.log(total_price);
                                            total_price = days >= 30 ? total_price - (monthly_discount * days) : (days >= 7 ? total_price - (weekly_discount * days) : total_price);
                                            total_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0', total_price);
                                        }
                                        $('.total-price-wrap .total-price').html(total_price_html);
                                    } else {
                                        $('.total-days-price-wrap').hide();
                                        $('.service-fee-wrap').hide();
                                        $('.cleaning-fee-wrap').hide();
                                        $('.total-price-wrap').hide();
                                    }
                                }
								<?php endif; ?>

                                //minimum 5 days
                                if (inSelectedDates[0] && outSelectedDates[0]) {
                                    var diff = Math.abs(outSelectedDates[0] - inSelectedDates[0]);
                                    var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                                    if (days < 5) {
                                        $('.tf-submit').attr('disabled', 'disabled');
                                        $('.tf-submit').addClass('disabled');
                                        $('.tf-check-out-date .tf_label-row').append('<span id="tf-required" class="required"><b><?php _e( 'Minimum 5 days', 'tourfic' ); ?></b></span>');
                                    } else {
                                        $('.tf-submit').removeAttr('disabled');
                                        $('.tf-submit').removeClass('disabled');
                                        $('#tf-required').remove();
                                    }
                                }
                            },
							<?php tf_flatpickr_locale();?>
                        });
                    }

					<?php tf_flatpickr_locale();?>
                });

                const checkOutDate = flatpickr("#tf-apartment-booking #check-out-date", {
                    enableTime: false,
                    minDate: "today",
                    dateFormat: "Y/m/d",
					<?php tf_flatpickr_locale();?>
                });

            });
        })(jQuery);

    </script>
	<?php
}

if ( ! function_exists( 'tf_apartment_booked_days' ) ) {
	function tf_apartment_booked_days( $post_id ) {
		$wc_orders = wc_get_orders( array(
			'post_status' => array( 'wc-processing' ),
			'limit'       => - 1,
		) );

		$booked_days = array();
		foreach ( $wc_orders as $wc_order ) {
			$order_items = $wc_order->get_items();
			foreach ( $order_items as $item_id => $item ) {
				$item_post_id = wc_get_order_item_meta( $item_id, '_post_id', true );
				if ( $item_post_id == $post_id ) {
					$check_in_date  = wc_get_order_item_meta( $item_id, 'check_in', true );
					$check_out_date = wc_get_order_item_meta( $item_id, 'check_out', true );
					$booked_days[]  = array(
						'check_in'  => $check_in_date,
						'check_out' => $check_out_date,
					);
				}
			}
		}

		return $booked_days;
	}
}

/**
 * WooCommerce hotel Functions
 *
 * @include
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' ) ) {
	require_once TF_INC_PATH . 'functions/woocommerce/wc-apartment.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' );
}
