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
		'show_in_rest'       => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'menu_icon'          => 'dashicons-admin-home',
		'rewrite'            => array( 'slug' => $apartment_slug, 'with_front' => false ),
		'capability_type'    => array( 'tf_apartment', 'tf_apartments' ),
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

if ( tfopt( 'disable-services' ) && in_array( 'apartment', tfopt( 'disable-services' ) ) ) {
} else {
	add_action( 'init', 'register_tf_apartment_post_type' );
}

add_filter( 'use_block_editor_for_post_type', function ( $enabled, $post_type ) {
	return ( 'tf_apartment' === $post_type ) ? false : $enabled;
}, 10, 2 );

/**
 * Register taxonomies for tf_apartment
 *
 * apartment_location, apartment_feature, apartment_type
 */
function tf_apartment_taxonomies_register() {

	/**
	 * Taxonomy: apartment_location
	 */
	$apartment_location_slug   = apply_filters( 'apartment_location_slug', 'apartment-location' );
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
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_apartment',
			'edit_terms'   => 'edit_tf_apartment',
		),
	);

	/**
	 * Taxonomy: apartment_feature
	 */
	$apartment_feature_slug   = apply_filters( 'apartment_feature_slug', 'apartment-feature' );
	$apartment_feature_labels = [
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

	$apartment_feature_args = [
		"labels"                => $apartment_feature_labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => $apartment_feature_slug, 'with_front' => true ],
		"show_admin_column"     => true,
		"show_in_rest"          => true,
		'meta_box_cb'           => false,
		"rest_base"             => "apartment_feature",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_apartment',
			'edit_terms'   => 'edit_tf_apartment',
		),
	];

	/**
	 * Taxonomy: apartment_type
	 */
	$apartment_type_slug   = apply_filters( 'apartment_type_slug', 'apartment-type' );
	$apartment_type_labels = [
		"name"                       => __( "Types", 'tourfic' ),
		"singular_name"              => __( "Type", 'tourfic' ),
		"menu_name"                  => __( "Types", 'tourfic' ),
		"all_items"                  => __( "All Types", 'tourfic' ),
		"edit_item"                  => __( "Edit Type", 'tourfic' ),
		"view_item"                  => __( "View Type", 'tourfic' ),
		"update_item"                => __( "Update Type", 'tourfic' ),
		"add_new_item"               => __( "Add new Type", 'tourfic' ),
		"new_item_name"              => __( "New Type name", 'tourfic' ),
		"parent_item"                => __( "Parent Type", 'tourfic' ),
		"parent_item_colon"          => __( "Parent Type:", 'tourfic' ),
		"search_items"               => __( "Search Type", 'tourfic' ),
		"popular_items"              => __( "Popular Types", 'tourfic' ),
		"separate_items_with_commas" => __( "Separate Types with commas", 'tourfic' ),
		"add_or_remove_items"        => __( "Add or remove Types", 'tourfic' ),
		"choose_from_most_used"      => __( "Choose from the most used Types", 'tourfic' ),
		"not_found"                  => __( "No Types found", 'tourfic' ),
		"no_terms"                   => __( "No Types", 'tourfic' ),
		"items_list_navigation"      => __( "Types list navigation", 'tourfic' ),
		"items_list"                 => __( "Types list", 'tourfic' ),
		"back_to_items"              => __( "Back to Types", 'tourfic' ),
	];

	$apartment_type_args = [
		"labels"                => $apartment_type_labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => $apartment_type_slug, 'with_front' => true ],
		"show_admin_column"     => true,
		"show_in_rest"          => true,
		"rest_base"             => "apartment_type",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_apartment',
			'edit_terms'   => 'edit_tf_apartment',
		),
	];

	register_taxonomy( 'apartment_location', 'tf_apartment', apply_filters( 'apartment_location_args', $apartment_location_args ) );
	register_taxonomy( 'apartment_feature', 'tf_apartment', apply_filters( 'apartment_feature_args', $apartment_feature_args ) );
	register_taxonomy( 'apartment_type', 'tf_apartment', apply_filters( 'apartment_type_args', $apartment_type_args ) );

}

if ( tfopt( 'disable-services' ) && in_array( 'apartment', tfopt( 'disable-services' ) ) ) {
} else {
	add_action( 'init', 'tf_apartment_taxonomies_register' );
}

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
 * WooCommerce hotel Functions
 *
 * @include
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' ) ) {
	require_once TF_INC_PATH . 'functions/woocommerce/wc-apartment.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' );
}

/**
 * Apartment Search form
 * @author Foysal
 */
if ( ! function_exists( 'tf_apartment_search_form_horizontal' ) ) {
	function tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced = false ) {
		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}
		// location
		$location = ! empty( $_GET['place'] ) ? sanitize_text_field( $_GET['place'] ) : '';
		// Adults
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		// children
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		// room
		$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';

		// date format for appartments
		$date_format_change_appartments  = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";

		?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_apartment_booking" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">

			<?php if ( $title ): ?>
                <div class="tf_widget-title"><h2><?php echo esc_html( $title ); ?></h2></div>
			<?php endif; ?>

			<?php if ( $subtitle ): ?>
                <div class="tf_widget-subtitle"><?php echo esc_html( $subtitle ); ?></div>
			<?php endif; ?>


            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Location', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>
                                    <input type="text" required="" name="place-name" id="tf-apartment-location" class="" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" class="tf-place-input">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

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
                                    <input type="number" name="adults" id="adults" min="1" value="1"/>
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="children" id="children" min="0" value="0"/>
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="infant" id="infant" min="0" value="0"/>
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
                                <span class="tf-label"><?php _e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                           placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo tfopt( 'date_apartment_search' ) ? 'required' : ''; ?>>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

				<?php if ( $advanced ): ?>
                    <div class="tf_selectdate-wrap tf_more_info_selections">
                        <div class="tf_input-inner">
                            <label class="tf_label-row" style="width: 100%;">
                                <span class="tf-label"><?php _e( 'More', 'tourfic' ); ?></span>
                                <span style="text-decoration: none; display: block; cursor: pointer;"><?php _e( 'Filter', 'tourfic' ); ?>  <i class="fas fa-angle-down"></i></span>
                            </label>
                        </div>
                        <div class="tf-more-info">
                            <h3><?php _e( 'Filter Price (Per Night)', 'tourfic' ); ?></h3>
                            <div class="tf-filter-price-range">
                                <div class="tf-apartment-filter-range"></div>
                            </div>

                            <h3 style="margin-top: 20px"><?php _e( 'Apartment Features', 'tourfic' ); ?></h3>
							<?php
							$tf_apartment_feature = get_terms( array(
								'taxonomy'     => 'apartment_feature',
								'orderby'      => 'title',
								'order'        => 'ASC',
								'hide_empty'   => true,
								'hierarchical' => 0,
							) );
							if ( $tf_apartment_feature ) : ?>
                                <div class="tf-apartment-features" style="overflow: hidden">
									<?php foreach ( $tf_apartment_feature as $term ) : ?>
                                        <div class="form-group form-check">
                                            <input type="checkbox" name="features[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                                            <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                                        </div>
									<?php endforeach; ?>
                                </div>
							<?php endif; ?>

                            <h3 style="margin-top: 20px"><?php _e( 'Apartment Types', 'tourfic' ); ?></h3>
							<?php
							$tf_apartment_type = get_terms( array(
								'taxonomy'     => 'apartment_type',
								'orderby'      => 'title',
								'order'        => 'ASC',
								'hide_empty'   => true,
								'hierarchical' => 0,
							) );
							if ( $tf_apartment_type ) : ?>
                                <div class="tf-apartment-types" style="overflow: hidden">
									<?php foreach ( $tf_apartment_type as $term ) : ?>
                                        <div class="form-group form-check">
                                            <input type="checkbox" name="types[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                                            <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                                        </div>
									<?php endforeach; ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
				<?php endif; ?>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_apartment" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php _e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>

        <script>
            (function ($) {
                $(document).ready(function () {

                    $("#tf_apartment_booking #check-in-out-date").flatpickr({
                        enableTime: false,
                        mode: "range",
                        dateFormat: "Y/m/d",
						altInput: true,
						altFormat: '<?php echo $date_format_change_appartments; ?>',
                        minDate: "today",
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
                        defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
                    });

                });
            })(jQuery);
        </script>
		<?php
	}
}

/**
 * Single Apartment Sidebar Booking Form
 * @author Foysal
 */
if ( ! function_exists( 'tf_apartment_single_booking_form' ) ) {
	function tf_apartment_single_booking_form( $comments, $disable_review_sec ) {

		$meta            = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
		$min_stay        = ! empty( $meta['min_stay'] ) ? $meta['min_stay'] : 1;
		$max_adults      = ! empty( $meta['max_adults'] ) ? $meta['max_adults'] : '';
		$max_children    = ! empty( $meta['max_children'] ) ? $meta['max_children'] : '';
		$max_infants     = ! empty( $meta['max_infants'] ) ? $meta['max_infants'] : '';
		$price_per_night = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
		$discount_type   = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$discount        = ! empty( $meta['discount'] ) ? $meta['discount'] : 0;
		$booked_dates    = tf_apartment_booked_days( get_the_ID() );

		// date format for apartment
		$date_format_change_appartments  = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";

		if ( defined( 'TF_PRO' ) ) {
			$additional_fees = ! empty( $meta['additional_fees'] ) ? $meta['additional_fees'] : array();
		} else {
			$additional_fee_label = ! empty( $meta['additional_fee_label'] ) ? $meta['additional_fee_label'] : '';
			$additional_fee       = ! empty( $meta['additional_fee'] ) ? $meta['additional_fee'] : 0;
			$fee_type             = ! empty( $meta['fee_type'] ) ? $meta['fee_type'] : '';
		}

		$adults       = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		$child        = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		$infant       = ! empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? $_GET['check-in-out-date'] : '';
		?>

        <!-- Start Booking widget -->
        <form id="tf-apartment-booking" class="tf-apartment-side-booking" method="get" autocomplete="off">
            <h4><?php ! empty( $meta['booking_form_title'] ) ? _e( $meta['booking_form_title'] ) : _e( 'Book your Apartment', 'tourfic' ); ?></h4>
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
                    <div class="tf-check-in-out-date">
                        <label class="tf_label-row">
                            <span class="tf-label"><?php _e( 'Check in & out date', 'tourfic' ); ?></span>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                   placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?>
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
                                    <div class="adults-text"><?php echo sprintf( __( '%s Adults', 'tourfic' ), ! empty( $adults ) ? $adults : 1 ); ?></div>
                                    <div class="person-sep"></div>
                                    <div class="child-text"><?php echo sprintf( __( '%s Children', 'tourfic' ), ! empty( $child ) ? $child : 0 ); ?></div>
                                    <div class="person-sep"></div>
                                    <div class="infant-text"><?php echo sprintf( __( '%s Infant', 'tourfic' ), ! empty( $infant ) ? $infant : 0 ); ?></div>
                                </div>
                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">-</div>
                                                <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1' ?>"/>
                                                <div class="acr-inc">+</div>
                                            </div>
                                        </div>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">-</div>
                                                <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0' ?>"/>
                                                <div class="acr-inc">+</div>
                                            </div>
                                        </div>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">-</div>
                                                <input type="number" name="infant" id="infant" min="0" value="<?php echo ! empty( $infant ) ? $infant : '0' ?>"/>
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
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Reserve', 'tourfic' ); ?></button>
                </div>
            </div>

            <ul class="tf-apartment-price-list">
                <li class="total-days-price-wrap" style="display: none">
                    <span class="total-days tf-price-list-label"></span>
                    <span class="days-total-price tf-price-list-price"></span>
                </li>

				<?php if ( ! defined( 'TF_PRO' ) && ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
                    <li class="additional-fee-wrap" style="display: none">
                        <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee_label; ?></span>
                        <span class="additional-fee tf-price-list-price"></span>
                    </li>
				<?php elseif ( defined( 'TF_PRO' ) ): ?>
					<?php foreach ( $additional_fees as $key => $additional_fee ) : ?>
                        <li class="additional-fee-wrap" style="display: none">
                            <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee['additional_fee_label']; ?></span>
                            <span class="additional-fee-<?php echo esc_attr( $key ) ?> tf-price-list-price"></span>
                        </li>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( ! empty( $discount ) ): ?>
                    <li class="apartment-discount-wrap" style="display: none">
                        <span class="apartment-discount-label tf-price-list-label"><?php _e( 'Discount', 'tourfic' ); ?></span>
                        <span class="apartment-discount tf-price-list-price"></span>
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
                    let minStay = <?php echo $min_stay ?>;

                    const bookingCalculation = (selectedDates) => {
						<?php if ( ! empty( $price_per_night ) ): ?>
                        //calculate total days
                        if (selectedDates[0] && selectedDates[1]) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                            if (days > 0) {
                                var price_per_night = <?php echo $price_per_night; ?>;
                                var wc_price_per_night = '<?php echo wc_price( $price_per_night ); ?>';
                                var total_price = price_per_night * days;
                                var total_days_price_html = '<?php echo wc_price( 0 ); ?>';
                                if (total_price > 0) {
                                    $('.total-days-price-wrap').show();
                                    total_days_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                }
                                $('.total-days-price-wrap .total-days').html(wc_price_per_night + ' x ' + days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                $('.total-days-price-wrap .days-total-price').html(total_days_price_html);


                                let totalPerson = parseInt($('.tf_acrselection #adults').val()) + parseInt($('.tf_acrselection #children').val()) + parseInt($('.tf_acrselection #infant').val());

                                //additional fee
								<?php if ( defined( 'TF_PRO' ) ): ?>
								<?php foreach ($additional_fees as $key => $item) : ?>
                                let additional_fee_<?php echo $key ?> = <?php echo $item['additional_fee']; ?>;
                                let additional_fee_html_<?php echo $key ?> = '<?php echo wc_price( 0 ); ?>';
                                let totalAdditionalFee_<?php echo $key ?> = 0;

								<?php if ( $item['fee_type'] == 'per_night' ): ?>
                                totalAdditionalFee_<?php echo $key ?> = additional_fee_<?php echo $key ?> * days;
								<?php elseif($item['fee_type'] == 'per_person'): ?>
                                totalAdditionalFee_<?php echo $key ?> = additional_fee_<?php echo $key ?> * totalPerson;
								<?php else: ?>
                                totalAdditionalFee_<?php echo $key ?> = additional_fee_<?php echo $key ?>;
								<?php endif; ?>

                                if (totalAdditionalFee_<?php echo $key ?> > 0) {
                                    $('.additional-fee-wrap').show();
                                    total_price = total_price + totalAdditionalFee_<?php echo $key ?>;
                                    additional_fee_html_<?php echo $key ?> = '<?php echo wc_price( 0 ); ?>'.replace('0.00', totalAdditionalFee_<?php echo $key ?>.toFixed(2));
                                }
                                $('.additional-fee-wrap .additional-fee-<?php echo $key ?>').html(additional_fee_html_<?php echo $key ?>);
								<?php endforeach; ?>
								<?php else: ?>
								<?php if ( ! empty( $additional_fee ) ): ?>
                                let additional_fee = <?php echo $additional_fee; ?>;
                                let additional_fee_html = '<?php echo wc_price( 0 ); ?>';
                                let totalAdditionalFee = 0;

								<?php if ( $fee_type == 'per_night' ): ?>
                                totalAdditionalFee = additional_fee * days;
								<?php elseif($fee_type == 'per_person'): ?>
                                totalAdditionalFee = additional_fee * totalPerson;
								<?php else: ?>
                                totalAdditionalFee = additional_fee;
								<?php endif; ?>

                                if (totalAdditionalFee > 0) {
                                    $('.additional-fee-wrap').show();
                                    total_price = total_price + totalAdditionalFee;
                                    additional_fee_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', totalAdditionalFee.toFixed(2));
                                }
                                $('.additional-fee-wrap .additional-fee').html(additional_fee_html);
								<?php endif; ?>
								<?php endif; ?>

                                //discount
                                var discount = <?php echo $discount; ?>;
                                var discount_html = '<?php echo wc_price( 0 ); ?>';
                                if (discount > 0) {
                                    $('.apartment-discount-wrap').show();

									<?php if ( $discount_type == 'percent' ): ?>
                                    discount_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', (total_price * discount / 100).toFixed(2));
                                    total_price = total_price - (total_price * discount / 100);
									<?php else: ?>
                                    discount_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', discount.toFixed(2));
                                    total_price = total_price - discount;
									<?php endif; ?>
                                }
                                $('.apartment-discount-wrap .apartment-discount').html('-' + discount_html);

                                //total price
                                var total_price_html = '<?php echo wc_price( 0 ); ?>';
                                if (total_price > 0) {
                                    $('.total-price-wrap').show();
                                    total_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                }
                                $('.total-price-wrap .total-price').html(total_price_html);
                            } else {
                                $('.total-days-price-wrap').hide();
                                $('.additional-fee-wrap').hide();
                                $('.total-price-wrap').hide();
                            }
                        }
						<?php endif; ?>

                        //minimum stay
                        if (selectedDates[0] && selectedDates[1] && minStay > 0) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                            if (days < minStay) {
                                $('.tf-submit').attr('disabled', 'disabled');
                                $('.tf-submit').addClass('disabled');
                                $('.tf-check-in-out-date .tf_label-row .tf-err-msg').remove();
                                $('.tf-check-in-out-date .tf_label-row').append('<span class="tf-err-msg"><?php echo sprintf( __( 'Minimum stay is %s nights', 'tourfic' ), $min_stay ); ?></span>');
                            } else {
                                $('.tf-submit').removeAttr('disabled');
                                $('.tf-submit').removeClass('disabled');
                                $('.tf-check-in-out-date .tf_label-row .tf-err-msg').remove();
                            }
                        }
                    }

                    const checkinoutdateange = flatpickr("#tf-apartment-booking #check-in-out-date", {
                        enableTime: false,
                        mode: "range",
                        minDate: "today",
						altInput: true,
						altFormat: '<?php echo $date_format_change_appartments; ?>',
                        dateFormat: "Y/m/d",
                        defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            bookingCalculation(selectedDates);
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            bookingCalculation(selectedDates);
                        },
                        disable: [
							<?php foreach ( $booked_dates as $booked_date ) : ?>
                            {
                                from: "<?php echo $booked_date['check_in']; ?>",
                                to: "<?php echo $booked_date['check_out']; ?>"
                            },
							<?php endforeach; ?>
                        ],
						<?php tf_flatpickr_locale(); ?>
                    });

                    $(document).on('change', '.tf_acrselection #adults, .tf_acrselection #children, .tf_acrselection #infant', function () {
                        if ($('#tf-apartment-booking #check-in-out-date').val() !== '') {
                            bookingCalculation(checkinoutdateange.selectedDates);
                        }
                    });
                });
            })(jQuery);

        </script>
		<?php
	}
}

/**
 * Apartment Archive Single Item Layout
 * @author Foysal
 */
if ( ! function_exists( 'tf_apartment_archive_single_item' ) ) {
	function tf_apartment_archive_single_item( array $data = [ 1, 0, 0, '' ] ): void {

		$post_id  = get_the_ID();
		$features = ! empty( get_the_terms( $post_id, 'apartment_feature' ) ) ? get_the_terms( $post_id, 'apartment_feature' ) : '';

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $infant, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $infant, $check_in_out ] = $data;
		}

		// Get apartment meta options
		$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
		if ( empty( $meta ) ) {
			return;
		}

		// Location
		$address         = ! empty( $meta['address'] ) ? $meta['address'] : '';
		$featured        = ! empty( $meta['apartment_as_featured'] ) ? $meta['apartment_as_featured'] : '';
		$price_per_night = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;

		// Single link
		$url = get_the_permalink();
		$url = add_query_arg( array(
			'adults'            => $adults,
			'children'          => $child,
			'infant'            => $infant,
			'check-in-out-date' => $check_in_out,
		), $url );
		?>
        <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
            <div class="single-tour-inner">
				<?php if ( $featured ): ?>
                    <div class="tf-featured-badge">
                        <span><?php echo ! empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" ); ?></span>
                    </div>
				<?php endif; ?>
                <div class="tourfic-single-left">
                    <a href="<?php echo $url; ?>">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img width="100%" height="100%" src="' . TF_ASSETS_URL . "img/img-not-available.svg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
                <div class="tourfic-single-right">
                    <div class="tf_property_block_main_row">
                        <div class="tf_item_main_block">
                            <div class="tf-hotel__title-wrap">
                                <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php the_title(); ?></h3></a>
                            </div>
							<?php
							if ( $address ) {
								echo '<div class="tf-map-link">';
								echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . $address . '</span>';
								echo '</div>';
							}
							?>
                        </div>
						<?php tf_archive_single_rating(); ?>
                    </div>

                    <div class="sr_rooms_table_block">
                        <div class="room_details">
                            <div class="featuredRooms">
                                <div class="prco-ltr-right-align-helper">
                                    <div class="tf-archive-shortdesc">
										<?php echo substr( wp_strip_all_tags( get_the_content() ), 0, 160 ) . '...'; ?>
                                    </div>
                                </div>
                                <div class="roomNameInner">
                                    <div class="room_link">
                                        <div class="roomrow_flex">
											<?php if ( $features ) { ?>
                                                <div class="roomName_flex">
                                                    <ul class="tf-archive-desc">
														<?php foreach ( $features as $feature ) {
															$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_apartment_feature', true );
															if ( ! empty( $feature_meta ) ) {
																$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
															}
															if ( ! empty( $f_icon_type ) && $f_icon_type == 'icon' ) {
																$feature_icon = ! empty( $feature_meta['apartment-feature-icon'] ) ? '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
															} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'custom' ) {
																$feature_icon = ! empty( $feature_meta['apartment-feature-icon-custom'] ) ? '<img src="' . $feature_meta['apartment-feature-icon-custom'] . '" style="min-width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
															} else {
																$feature_icon = '<i class="fas fa-bread-slice"></i>';
															}
															?>
                                                            <li class="tf-tooltip">
																<?php
																if ( ! empty( $feature_icon ) ) {
																	echo $feature_icon;
																} ?>
                                                                <div class="tf-top">
																	<?php echo $feature->name; ?>
                                                                    <i class="tool-i"></i>
                                                                </div>
                                                            </li>
														<?php } ?>
                                                    </ul>
                                                </div>
											<?php } ?>
                                            <div class="roomPrice roomPrice_flex sr_discount" style="<?php echo empty( $features ) ? 'text-align:left' : ''; ?>">
                                                <div class="availability-btn-area">
                                                    <a href="<?php echo $url; ?>" class="tf_button btn-styled"><?php esc_html_e( 'View Details', 'tourfic' ); ?></a>
                                                </div>
                                                <!-- Show minimum price @author - Hena -->
                                                <div class="tf-room-price-area">
													<?php

													if ( ! empty( $price_per_night ) ):
														?>
                                                        <div class="tf-room-price">
                                                            <h6 class="tf-apartment-price-per-night">
                                                                <span class="tf-apartment-base-price"><?php echo wc_price( $price_per_night ) ?></span>
                                                                <span><?php _e( '/per night', 'tourfic' ) ?></span>
                                                            </h6>
                                                        </div>
													<?php endif; ?>
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
 * Filter apartments on search result page by checkin checkout dates set by backend
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of hotels exists
 * @param array $data user input for sidebar form
 *
 * @author Foysal
 *
 */
if ( ! function_exists( 'tf_filter_apartment_by_date' ) ) {
	function tf_filter_apartment_by_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $infant, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $infant, $check_in_out ] = $data;
		}

		// Get apartment meta options
		$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );

		// Set initial status
		$has_apartment = false;

		if ( ! empty( $check_in_out ) ) {
			$booked_dates   = tf_apartment_booked_days( get_the_ID() );
			$checkInOutDate = explode( ' - ', $check_in_out );
			if ( $checkInOutDate[0] && $checkInOutDate[1] ) {
				$check_in_stt  = strtotime( $checkInOutDate[0] . ' +1 day' );
				$check_out_stt = strtotime( $checkInOutDate[1] );
				$days          = ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1;
				//skip apartment if min stay is grater than selected days
				if ( ! empty( $meta['min_stay'] ) && intval( $meta['min_stay'] ) <= $days && $meta['min_stay'] != 0 ) {
					if ( ! empty( $meta['max_adults'] ) && $meta['max_adults'] >= $adults && $meta['max_adults'] != 0 ) {
						if ( ! empty( $child ) && ! empty( $meta['max_children'] ) ) {
							if ( ! empty( $meta['max_children'] ) && $meta['max_children'] >= $child && $meta['max_children'] != 0 ) {

								if ( ! empty( $infant ) && ! empty( $meta['max_infants'] ) ) {
									if ( ! empty( $meta['max_infants'] ) && $meta['max_infants'] >= $infant && $meta['max_infants'] != 0 ) {
										if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
											if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
												$has_apartment = true;
											}
										} else {
											$has_apartment = true;
										}
									}
								} else {
									if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
										if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
											$has_apartment = true;
										}
									} else {
										$has_apartment = true;
									}
								}
							}
						} else {
							if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
								if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
									$has_apartment = true;
								}
							} else {
								$has_apartment = true;
							}
						}
					}
				}

                /*foreach ( $booked_dates as $booked_date ) {
                    $booked_from = strtotime( $booked_date['check_in'] );
                    $booked_to   = strtotime( $booked_date['check_out'] );

                    if ( $check_in_stt >= $booked_from && $check_in_stt <= $booked_to ) {
                        $has_apartment = true;
                    }
                    if ( $check_out_stt >= $booked_from && $check_out_stt <= $booked_to ) {
                        $has_apartment = true;
                    }
                    if ( $check_in_stt <= $booked_from && $check_out_stt >= $booked_to ) {
                        $has_apartment = true;
                    }
                }*/
			}
		}

		// Conditional apartment showing
		if ( $has_apartment ) {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);
		} else {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}
}

/**
 * Filter apartments on search result page without checkin checkout dates
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of hotels exists
 * @param array $data user input for sidebar form
 *
 * @author Foysal
 *
 */
if ( ! function_exists( 'tf_filter_apartment_without_date' ) ) {
	function tf_filter_apartment_without_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $infant, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $infant, $check_in_out ] = $data;
		}

		// Get apartment meta options
		$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );

		// Set initial status
		$has_apartment = false;

		if ( ! empty( $meta['max_adults'] ) && $meta['max_adults'] >= $adults && $meta['max_adults'] != 0 ) {
			if ( ! empty( $child ) && ! empty( $meta['max_children'] ) ) {
				if ( ! empty( $meta['max_children'] ) && $meta['max_children'] >= $child && $meta['max_children'] != 0 ) {

					if ( ! empty( $infant ) && ! empty( $meta['max_infants'] ) ) {
						if ( ! empty( $meta['max_infants'] ) && $meta['max_infants'] >= $infant && $meta['max_infants'] != 0 ) {
							if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
								if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
									$has_apartment = true;
								}
							} else {
								$has_apartment = true;
							}
						}
					} else {
						if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
							if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
								$has_apartment = true;
							}
						} else {
							$has_apartment = true;
						}
					}
				}
			} else {
				if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
						$has_apartment = true;
					}
				} else {
					$has_apartment = true;
				}
			}
		}


		// Conditional apartment showing
		if ( $has_apartment ) {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);
		} else {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}
}

/**
 * Apartment booked days
 * @author Foysal
 */
if ( ! function_exists( 'tf_apartment_booked_days' ) ) {
	function tf_apartment_booked_days( $post_id ) {
		$wc_orders = wc_get_orders( array(
			'post_status' => array( 'wc-completed' ),
			'limit'       => - 1,
		) );

		$booked_days = array();
		foreach ( $wc_orders as $wc_order ) {
			$order_items = $wc_order->get_items();

			foreach ( $order_items as $item_id => $item ) {
				$item_post_id = wc_get_order_item_meta( $item_id, '_post_id', true );
				if ( $item_post_id == $post_id ) {
					$check_in_out_date = wc_get_order_item_meta( $item_id, 'check_in_out_date', true );

					if ( ! empty( $check_in_out_date ) ) {
						$check_in_out_date = explode( ' - ', $check_in_out_date );
						$booked_days[]     = array(
							'check_in'  => $check_in_out_date[0],
							'check_out' => $check_in_out_date[1],
						);
					}
				}
			}
		}

		return $booked_days;
	}
}

/**
 * Get Apartment Locations
 *
 * {taxonomy-apartment_location}
 * @author Foysal
 */
if ( ! function_exists( 'get_apartment_locations' ) ) {
	function get_apartment_locations() {

		$locations = array();

		$location_terms = get_terms( array(
			'taxonomy'   => 'apartment_location',
			'hide_empty' => true,
		) );

		foreach ( $location_terms as $location_term ) {
			if ( ! empty( $location_term->slug ) ) {
				$locations[ $location_term->slug ] = $location_term->name;
			}
		}

		return $locations;
	}
}

/**
 * Get Apartment Min Max Price
 * @author Foysal
 */
if ( ! function_exists( 'get_apartment_min_max_price' ) ) {
	function get_apartment_min_max_price() {
		$min_max_price = array();

		$apartment_query = new WP_Query( array(
			'post_type'      => 'tf_apartment',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		) );

		if ( $apartment_query->have_posts() ) {
			while ( $apartment_query->have_posts() ) {
				$apartment_query->the_post();
				$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
				if ( ! empty( $meta ) ) {
					$min_max_price[] = ! empty( $meta['price_per_night'] ) ? intval( $meta['price_per_night'] ) : 0;
				}
			}
		}

		wp_reset_query();

		return array(
			'min' => ! empty( $min_max_price ) ? min( $min_max_price ) : 0,
			'max' => ! empty( $min_max_price ) ? max( $min_max_price ) : 0,
		);
	}
}

/**
 * Apartment host rating
 *
 * @param $author_id
 *
 * @author Foysal
 */
if ( ! function_exists( 'tf_apartment_host_rating' ) ) {
	function tf_apartment_host_rating( $author_id ) {
		$author_posts = get_posts( array(
			'author'         => $author_id,
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );

		//get post comments
		$comments_array = array();
		foreach ( $author_posts as $author_post ) {
			$comments_array[] = get_comments( array(
				'post_id' => $author_post->ID,
				'status'  => 'approve',
			) );
		}

		$total_comment_rating = [];
		$comment_count        = 0;
		foreach ( $comments_array as $comments ) {
			if ( ! empty( $comments ) ) {
				$total_comment_rating[] = tf_total_avg_rating( $comments );
			}
			$comment_count += count( $comments );
		}

		if ( $comments ) {
			ob_start();
			?>
            <div class="tf-host-rating-wrapper">
                <i class="fas fa-star"></i>
                <div class="tf-host-rating">
					<?php echo tf_average_ratings( array_values( $total_comment_rating ?? [] ) ); ?>
                </div>
                <h6>(<?php tf_based_on_text( $comment_count ); ?>)</h6>
            </div>

			<?php
			echo ob_get_clean();
		}
	}
}

/**
 * Apartment room quick view
 * @author Foysal
 */
if ( ! function_exists( 'tf_apartment_room_quick_view' ) ) {
	function tf_apartment_room_quick_view() {
		?>
        <div class="tf-hotel-quick-view" style="display: flex">
			<?php
			$meta = get_post_meta( sanitize_text_field( $_POST['post_id'] ), 'tf_apartment_opt', true );

			foreach ( tf_data_types( $meta['rooms'] ) as $key => $room ) :
				if ( $key == sanitize_text_field( $_POST['id'] ) ):
					$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
					?>
                    <div class="tf-hotel-details-qc-gallelry" style="width: 545px;">
						<?php if ( ! empty( $tf_room_gallery ) ) :
							$tf_room_gallery_ids = explode( ',', $tf_room_gallery );
							?>

                            <div class="tf-details-qc-slider tf-details-qc-slider-single">
								<?php
								if ( ! empty( $tf_room_gallery_ids ) ) {
									foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
										?>
                                        <div class="tf-details-qcs">
											<?php
											$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
											echo '<img src="' . $image_url . '" alt="">';
											?>
                                        </div>
									<?php }
								} ?>
                            </div>
                            <div class="tf-details-qc-slider tf-details-qc-slider-nav">
								<?php
								if ( ! empty( $tf_room_gallery_ids ) ) {
									foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
										?>
                                        <div class="tf-details-qcs">
											<?php
											$image_url = wp_get_attachment_url( $gallery_item_id, 'thumbnail' );
											echo '<img src="' . $image_url . '" alt="">';
											?>
                                        </div>
									<?php }
								} ?>
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
                                    .on('init', function (event, slick) {
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

                                jQuery('.tf-details-qc-slider-single').on('afterChange', function (event, slick, currentSlide) {
                                    jQuery('.tf-details-qc-slider-nav').slick('slickGoTo', currentSlide);
                                    var currrentNavSlideElem = '.tf-details-qc-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
                                    jQuery('.tf-details-qc-slider-nav .slick-slide.is-active').removeClass('is-active');
                                    jQuery(currrentNavSlideElem).addClass('is-active');
                                });

                                jQuery('.tf-details-qc-slider-nav').on('click', '.slick-slide', function (event) {
                                    event.preventDefault();
                                    var goToSingleSlide = jQuery(this).data('slick-index');

                                    jQuery('.tf-details-qc-slider-single').slick('slickGoTo', goToSingleSlide);
                                });
                            </script>
						<?php else : ?>
                            <img src="<?php echo esc_url( $room['thumbnail'] ) ?>" alt="room-thumbnail">
						<?php endif; ?>
                    </div>
                    <div class="tf-hotel-details-info" style="width:440px; padding-left: 35px;max-height: 470px;padding-top: 25px; overflow-y: auto;">
						<?php
						$footage       = ! empty( $room['footage'] ) ? $room['footage'] : '';
						$bed           = ! empty( $room['bed'] ) ? $room['bed'] : '';
						$adult_number  = ! empty( $room['adult'] ) ? $room['adult'] : '0';
						$child_number  = ! empty( $room['child'] ) ? $room['child'] : '0';
						$infant_number = ! empty( $room['infant'] ) ? $room['infant'] : '0';
						?>
                        <h3><?php echo esc_html( $room['title'] ); ?></h3>
                        <p><?php echo $room['description']; ?></p>
                        <div class="tf-room-title description">
							<?php if ( $footage ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-ruler-combined"></i></span>
                                        <span class="icon-text tf-d-b"><?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php _e( 'Room Footage', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php }
							if ( $bed ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php _e( 'Number of Beds', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php } ?>
							<?php if ( $adult_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php _e( 'Number of Adults', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php }
							if ( $child_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php _e( 'Number of Children', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php }
							if ( $infant_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $infant_number; ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php _e( 'Number of Infants', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
				<?php
				endif;
			endforeach;
			?>
        </div>
		<?php
		wp_die();
	}

	add_action( 'wp_ajax_tf_apt_room_details_qv', 'tf_apartment_room_quick_view' );
	add_action( 'wp_ajax_nopriv_tf_apt_room_details_qv', 'tf_apartment_room_quick_view' );
}

/**
 * Assign taxonomy(tour_type) from the single post metabox
 * to a Tour when updated or published
 * @return array();
 * @author Foysal
 * @since 2.9.23
 */
if ( ! function_exists( 'tf_apartment_feature_assign_taxonomies' ) ) {
	add_action( 'wp_after_insert_post', 'tf_apartment_feature_assign_taxonomies', 100, 3 );
	function tf_apartment_feature_assign_taxonomies( $post_id, $post, $old_status ) {
		if ( 'tf_apartment' !== $post->post_type ) {
			return;
		}
		$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
		if ( isset( $meta['amenities'] ) && ! empty( tf_data_types( $meta['amenities'] ) ) ) {
			$apartment_features = array();
			foreach ( tf_data_types( $meta['amenities'] ) as $amenity ) {
				$apartment_features[] = intval( $amenity['feature'] );
			}
			wp_set_object_terms( $post_id, $apartment_features, 'apartment_feature' );
		}
	}
}