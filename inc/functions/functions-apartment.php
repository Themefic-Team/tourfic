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
	function tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced, $design ) {
		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';

		// date format for apartments
		$date_format_change_apartments = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		$disable_apartment_child_search  = ! empty( tfopt( 'disable_apartment_child_search' ) ) ? tfopt( 'disable_apartment_child_search' ) : '';
		$disable_apartment_infant_search  = ! empty( tfopt( 'disable_apartment_infant_search' ) ) ? tfopt( 'disable_apartment_infant_search' ) : '';
		if( !empty($design) && 2==$design ){
		?>
		<form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2" id="tf_apartment_booking" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
			<div class="tf_hotel_searching">
				<div class="tf_form_innerbody">
					<div class="tf_form_fields">
						<div class="tf_destination_fields">
							<label class="tf_label_location">
								<span class="tf-label"><?php _e( 'Location', 'tourfic' ); ?></span>
								<div class="tf_form_inners tf_form-inner">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
									<path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z" fill="#FAEEDD"/>
									</svg>
									<input type="text" required="" name="place-name" id="tf-apartment-location" class="" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" class="tf-place-input">
								</div>
							</label>
						</div>
						
						<div class="tf_checkin_date">
							<label class="tf_label_checkin tf_apartment_check_in_out_date">
								<span class="tf-label"><?php _e( 'Check in', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_checkin_dates">
										<span class="date"><?php echo date('d'); ?></span>
										<span class="month"><?php echo date('M'); ?></span>
									</div>
									<div class="tf_check_arrow">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
										</svg>
									</div>
								</div>
							</label>

							<input type="text" name="check-in-out-date" class="tf-apartment-check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo tfopt( 'date_apartment_search' ) ? 'required' : ''; ?>>
						</div>
						
						<div class="tf_checkin_date tf_apartment_check_in_out_date">
							<label class="tf_label_checkin">
								<span class="tf-label"><?php _e( 'Check Out', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_checkout_dates">
										<span class="date"><?php echo date('d'); ?></span>
										<span class="month"><?php echo date('M'); ?></span>
									</div>
									<div class="tf_check_arrow">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
										</svg>
									</div>
								</div>
							</label>
						</div>

						<div class="tf_guest_info tf_selectperson-wrap">
							<label class="tf_label_checkin tf_input-inner">
								<span class="tf-label"><?php _e( 'Guests', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_guest_calculation">
										<div class="tf_guest_number">
											<span class="guest"><?php _e( '1', 'tourfic' ); ?></span>
											<span class="label"><?php _e( 'Guests', 'tourfic' ); ?></span>
										</div>
									</div>
									<div class="tf_check_arrow">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
										</svg>
									</div>
								</div>
							</label>

							<div class="tf_acrselection-wrap">
								<div class="tf_acrselection-inner">
									<div class="tf_acrselection">
										<div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">-</div>
											<input type="tel" name="adults" id="adults" min="1" value="1"/>
											<div class="acr-inc">+</div>
										</div>
									</div>
									<?php if ( empty($disable_apartment_child_search) ): ?>
										<div class="tf_acrselection">
											<div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
											<div class="acr-select">
												<div class="acr-dec">-</div>
												<input type="tel" name="children" id="children" min="0" value="0"/>
												<div class="acr-inc">+</div>
											</div>
										</div>
									<?php endif; ?>
									<?php if ( empty($disable_apartment_infant_search) ): ?>
										<div class="tf_acrselection">
											<div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
											<div class="acr-select">
												<div class="acr-dec">-</div>
												<input type="tel" name="infant" id="infant" min="0" value="0"/>
												<div class="acr-inc">+</div>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>

						</div>
					</div>
					<div class="tf_availability_checker_box">
						<input type="hidden" name="type" value="tf_apartment" class="tf-post-type"/>
						<button><?php echo _e("Check availability", "tourfic"); ?></button>
					</div>
				</div>
			</div>

		</form>
		<script>
			(function ($) {
				$(document).ready(function () {

					// flatpickr locale first day of Week
					<?php tf_flatpickr_locale("root"); ?>

					$(".tf_apartment_check_in_out_date").click(function(){
						$(".tf-apartment-check-in-out-date").click();
					});
					$(".tf-apartment-check-in-out-date").flatpickr({
						enableTime: false,
						mode: "range",
						dateFormat: "Y/m/d",
						minDate: "today",

						// flatpickr locale
						<?php tf_flatpickr_locale(); ?>
						
						onReady: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							dateSetToFields(selectedDates, instance);
						},
						onChange: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							dateSetToFields(selectedDates, instance);
						},
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const monthNames = [
								"Jan", "Feb", "Mar", "Apr", "May", "Jun",
								"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
							];
							if(selectedDates[0]){
								const startDate = selectedDates[0];
								$(".tf_apartment_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
								$(".tf_apartment_check_in_out_date .tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
							}
							if(selectedDates[1]){
								const endDate = selectedDates[1];
								$(".tf_apartment_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
								$(".tf_apartment_check_in_out_date .tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
							}
						}
					}

				});
			})(jQuery);
		</script>
		<?php } else{ ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_apartment_booking" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
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
						<?php if ( empty( $disable_apartment_child_search ) ): ?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php _e( '0 Children', 'tourfic' ); ?></div>
						<?php endif; ?>
						<?php if ( empty( $disable_apartment_infant_search ) ): ?>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php _e( '0 Infant', 'tourfic' ); ?></div>
						<?php endif; ?>
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
							<?php if ( empty( $disable_apartment_child_search ) ): ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="0"/>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php endif; ?>
							<?php if ( empty( $disable_apartment_infant_search ) ): ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="infant" id="infant" min="0" value="0"/>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php endif; ?>
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
                        altFormat: '<?php echo $date_format_change_apartments; ?>',
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
}

/**
 * Single Apartment Sidebar Booking Form
 * @author Foysal
 */
if ( ! function_exists( 'tf_apartment_single_booking_form' ) ) {
	function tf_apartment_single_booking_form( $comments, $disable_review_sec ) {

		$meta                = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
		$min_stay            = ! empty( $meta['min_stay'] ) ? $meta['min_stay'] : 1;
		$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
		$price_per_night     = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
		$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$infant_price        = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
		$discount_type       = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : 'none';
		$discount            = ! empty( $meta['discount'] ) ? $meta['discount'] : 0;
		$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
		$apt_availability    = ! empty( $meta['apt_availability'] ) ? $meta['apt_availability'] : '';
		$booked_dates        = tf_apartment_booked_days( get_the_ID() );
		$apt_reserve_button_text = !empty(tfopt('apartment_booking_form_button_text')) ? stripslashes(sanitize_text_field(tfopt('apartment_booking_form_button_text'))) : __("Reserve", 'tourfic');

		$tf_booking_type = '1';
		$tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = '';
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
			$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
			$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
			$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
			$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
			$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
		}

		// date format for apartment
		$date_format_change_appartments = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
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

		$apt_disable_dates = [];
		$tf_apt_enable_dates = [];
		if ( $enable_availability === '1' && ! empty( $apt_availability ) && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$apt_availability_arr = json_decode( $apt_availability, true );
			//iterate all the available disabled dates
			if ( ! empty( $apt_availability_arr ) && is_array( $apt_availability_arr ) ) {
				foreach ( $apt_availability_arr as $date ) {
					if ( $date['status'] === 'unavailable' ) {
						$apt_disable_dates[] = $date['check_in'];
					}
					if ( $date['status'] === 'available' ) {
						$tf_apt_enable_dates[] = $date['check_in'];
					}
				}
			}
		}

		$apartment_min_price = get_apartment_min_max_price( get_the_ID() );
		?>

        <!-- Start Booking widget -->
        <form id="tf-apartment-booking" class="tf-apartment-side-booking" method="get" autocomplete="off">
            <h4><?php ! empty( $meta['booking_form_title'] ) ? _e( $meta['booking_form_title'] ) : _e( 'Book your Apartment', 'tourfic' ); ?></h4>
            <div class="tf-apartment-form-header">
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                    <h3 class="tf-apartment-price-per-night">
                        <span class="tf-apartment-base-price">
						<?php
							//get the lowest price from all available room price
							$apartment_min_main_price = $apartment_min_price["min"];
							if ( ! empty( $discount_type ) && ! empty( $apartment_min_price["min"]  ) && ! empty( $discount ) ) {
								if ( $discount_type == "percent" ) {
									$apartment_min_discount = ( $apartment_min_price["min"] * (int) $discount ) / 100;
									$apartment_min_price    = $apartment_min_price["min"] - $apartment_min_discount;
								}
								if ( $discount_type == "fixed" ) {
									$apartment_min_discount = $discount;
									$apartment_min_price    = $apartment_min_price["min"] - (int) $apartment_min_discount;
								}
							}
							$lowest_price = wc_price( $apartment_min_price );
							
							if ( ! empty( $apartment_min_discount ) ) {
								echo "<b>" . __("From ", "tourfic") . "</b>" . "<del>" . strip_tags(wc_price( $apartment_min_main_price )) . "</del>" . " " . $lowest_price;
							} else {
								echo __("From ", "tourfic") . wc_price( $apartment_min_main_price );
							}
							?>
						</span>
						<?php if ( $pricing_type == "per_night") : ?>
                        	<span><?php _e( '/per night', 'tourfic' ) ?></span>
						<?php else : ?>
							<span><?php _e( '/per person', 'tourfic' ) ?></span>
						<?php endif; ?>

                    </h3>
				<?php endif; ?>
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

			<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 ) : ?>
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
			<?php endif; ?>

            <div class="tf_form-row">
				<?php $ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type(); ?>
                <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
                <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>

                <div class="tf-btn">
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 ) : ?>
                        <?php if (!empty($apt_reserve_button_text)) : ?>
							<button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( $apt_reserve_button_text, 'tourfic' ); ?></button>
						<?php endif; ?>
					<?php else: ?>
						<?php if (!empty($apt_reserve_button_text)) : ?>
							<a href="<?php echo esc_url( $tf_booking_url ); ?>"
							class="tf_button tf-submit btn-styled" <?php echo ! empty( $tf_booking_attribute ) ? $tf_booking_attribute : ''; ?> target="_blank"><?php esc_html_e( $apt_reserve_button_text , 'tourfic' ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
                </div>
            </div>

            <ul class="tf-apartment-price-list">
                <li class="total-days-price-wrap" style="display: none">
                    <span class="total-days tf-price-list-label"></span>
                    <span class="days-total-price tf-price-list-price"></span>
                </li>

				<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
					<?php foreach ( $additional_fees as $key => $additional_fee ) : ?>
                        <li class="additional-fee-wrap" style="display: none">
                            <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee['additional_fee_label']; ?></span>
                            <span class="additional-fee-<?php echo esc_attr( $key ) ?> tf-price-list-price"></span>
                        </li>
					<?php endforeach; ?>
				<?php elseif ( ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
                    <li class="additional-fee-wrap" style="display: none">
                        <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee_label; ?></span>
                        <span class="additional-fee tf-price-list-price"></span>
                    </li>
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

					// First Day of Week
					<?php tf_flatpickr_locale("root"); ?>

                    let minStay = <?php echo $min_stay ?>;

                    const bookingCalculation = (selectedDates) => {
						<?php if ( ( $pricing_type === 'per_night' && ! empty( $price_per_night ) ) || ( $pricing_type === 'per_person' && ! empty( $adult_price ) ) ): ?>
                        //calculate total days
                        if (selectedDates[0] && selectedDates[1]) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                            if (days > 0) {
                                var pricing_type = '<?php echo $pricing_type; ?>';
                                var price_per_night = <?php echo $price_per_night; ?>;
                                var adult_price = <?php echo $adult_price; ?>;
                                var child_price = <?php echo $child_price; ?>;
                                var infant_price = <?php echo $infant_price; ?>;
                                var enable_availability = '<?php echo $enable_availability; ?>';
                                var apt_availability = '<?php echo $apt_availability; ?>';
                                apt_availability = JSON.parse(apt_availability);

                                if (enable_availability !== '1') {
                                    if (pricing_type === 'per_night') {
                                        var total_price = price_per_night * days;
                                        var total_days_price_html = '<?php echo wc_price( 0 ); ?>';
                                        var wc_price_per_night = '<?php echo wc_price( $price_per_night ); ?>';
                                        if (total_price > 0) {
                                            $('.total-days-price-wrap').show();
                                            total_days_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                        }
                                        $('.total-days-price-wrap .total-days').html(wc_price_per_night + ' x ' + days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                        $('.total-days-price-wrap .days-total-price').html(total_days_price_html);
                                    } else {
                                        let totalPersonPrice = (adult_price * $('#adults').val()) + (child_price * $('#children').val()) + (infant_price * $('#infant').val());
                                        var total_price = totalPersonPrice * days;
                                        var total_days_price_html = '<?php echo wc_price( 0 ); ?>';
                                        var wc_price_per_person = '<?php echo wc_price( 0 ); ?>'.replace('0.00', totalPersonPrice.toFixed(2));
                                        if (total_price > 0) {
                                            $('.total-days-price-wrap').show();
                                            total_days_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                        }
                                        $('.total-days-price-wrap .total-days').html(wc_price_per_person + ' x ' + days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                        $('.total-days-price-wrap .days-total-price').html(total_days_price_html);
                                    }
                                } else {
                                    var total_price = 0;
                                    var total_price_html = '<?php echo wc_price( 0 ); ?>';
                                    var checkInDate = new Date(selectedDates[0]);
                                    var checkOutDate = new Date(selectedDates[1]);

                                    for (var date in apt_availability) {
                                        let d = new Date(date);

                                        if (d.getTime() >= checkInDate.getTime() && d.getTime() <= checkOutDate.getTime()) {

                                            if (d.getTime() !== checkInDate.getTime()) {
                                                var availabilityData = apt_availability[date];
                                                var pricing_type = availabilityData.pricing_type;
                                                var price = availabilityData.price ? parseFloat(availabilityData.price) : 0;
                                                var adultPrice = availabilityData.adult_price ? parseFloat(availabilityData.adult_price) : 0;
                                                var childPrice = availabilityData.child_price ? parseFloat(availabilityData.child_price) : 0;
                                                var infantPrice = availabilityData.infant_price ? parseFloat(availabilityData.infant_price) : 0;

                                                if (pricing_type === 'per_night' && price > 0) {
                                                    total_price += price;
                                                } else if (pricing_type === 'per_person') {
                                                    var totalPersonPrice = (adultPrice * $('#adults').val()) + (childPrice * $('#children').val()) + (infantPrice * $('#infant').val());
                                                    total_price += totalPersonPrice;
                                                    // console.log('total_price', total_price);
                                                }
                                            }
                                        }
                                    }

                                    if (total_price > 0) {
                                        $('.total-days-price-wrap').show();
                                        total_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                    }
                                    $('.total-days-price-wrap .total-days').html(days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                    $('.total-days-price-wrap .days-total-price').html(total_price_html);
                                }
								//discount
                                var discount = <?php echo $discount; ?>;
								var discountType = "<?php echo $discount_type; ?>";
                                var discount_html = '<?php echo wc_price( 0 ); ?>';
                                if (discount > 0 && discountType != "none") {
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


                                let totalPerson = parseInt($('.tf_acrselection #adults').val()) + parseInt($('.tf_acrselection #children').val()) + parseInt($('.tf_acrselection #infant').val());

                                //additional fee
								<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
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

                                if (totalAdditionalFee_<?php echo $key ?> > 0 ) {
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
                                //end additional fee

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
						<?php if (!empty($tf_apt_enable_dates) && is_array($tf_apt_enable_dates)) : ?>
							enable: [ <?php array_walk($tf_apt_enable_dates, function($date) {echo '"'. $date . '",';}); ?> ],
						<?php endif; ?>
                        disable: [
							<?php foreach ( $booked_dates as $booked_date ) : ?>
                            {
                                from: "<?php echo $booked_date['check_in']; ?>",
                                to: "<?php echo $booked_date['check_out']; ?>"
                            },
							<?php endforeach; ?>
							<?php foreach ( $apt_disable_dates as $apt_disable_date ) : ?>
                            {
                                from: "<?php echo $apt_disable_date; ?>",
                                to: "<?php echo $apt_disable_date; ?>"
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
		$map = ! empty( $meta['map'] ) ? $meta['map'] : '';
		if ( ! empty( $map ) && gettype( $map ) == "string" ) {
			$tf_apartment_map_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $map );
			$map                    = unserialize( $tf_apartment_map_value );
			$address                = ! empty( $map['address'] ) ? $map['address'] : '';
		}
		$featured        = ! empty( $meta['apartment_as_featured'] ) ? $meta['apartment_as_featured'] : '';
		$pricing_type    = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
		$apartment_multiple_tags = !empty($meta['tf-apartment-tags']) ? $meta['tf-apartment-tags'] : [];


		// Single link
		$url = get_the_permalink();
		$url = add_query_arg( array(
			'adults'            => $adults,
			'children'          => $child,
			'infant'            => $infant,
			'check-in-out-date' => $check_in_out,
		), $url );

		$apartment_min_price = get_apartment_min_max_price( get_the_ID() );
		?>
        <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
            <div class="single-tour-inner">
				<?php if ( $featured ): ?>
                    <div class="tf-featured-badge">
                        <span><?php echo ! empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" ); ?></span>
                    </div>
				<?php endif; ?>
                <div class="tourfic-single-left">
                	<div class="default-tags-container">

					<?php
					if(sizeof($apartment_multiple_tags) > 0) {
						foreach($apartment_multiple_tags as $tag) {
							$tag_title = !empty($tag["apartment-tag-title"]) ? __($tag["apartment-tag-title"], 'tourfic') : '';
							$tag_background_color = !empty($tag["apartment-tag-color-settings"]["background"]) ? $tag["apartment-tag-color-settings"]["background"] : "#003162";
							$tag_font_color = !empty($tag["apartment-tag-color-settings"]["font"]) ? $tag["apartment-tag-color-settings"]["font"] : "#fff";

							echo <<<EOD
								<span class="default-single-tag" style="color: $tag_font_color; background-color: $tag_background_color">$tag_title</span>
							EOD;
						}
					}
					?>
					</div>
                    <a href="<?php echo $url; ?>">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img width="100%" height="100%" src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
                <div class="tourfic-single-right">
                    <div class="tf_property_block_main_row">
                        <div class="tf_item_main_block">
                            <div class="tf-hotel__title-wrap">
                                <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php echo get_the_title($post_id); ?></h3></a>
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
										<?php echo substr( wp_strip_all_tags( get_the_content($post_id) ), 0, 160 ) . '...'; ?>
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
                                                    <div class="tf-room-price">
                                                        <h6 class="tf-apartment-price-per-night">
                                                            <span class="tf-apartment-base-price"><?php echo wc_price( $apartment_min_price['min'] ) ?></span>
                                                            <span><?php echo $pricing_type === 'per_night' ? __( '/per night', 'tourfic' ) : __( '/per person', 'tourfic' ) ?></span>
                                                        </h6>
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

				$tfperiod = new DatePeriod(
					new DateTime( $checkInOutDate[0] . ' 00:00' ),
					new DateInterval( 'P1D' ),
					new DateTime( $checkInOutDate[1] . ' 23:59' )
				);

				$avail_searching_date = [];
				foreach ( $tfperiod as $date ) {
					$avail_searching_date[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
				}

				//skip apartment if min stay is grater than selected days
				if ( ! empty( $meta['min_stay'] ) && intval( $meta['min_stay'] ) <= $days && $meta['min_stay'] != 0 ) {
					if ( ! empty( $meta['max_adults'] ) && $meta['max_adults'] >= $adults && $meta['max_adults'] != 0 ) {
						if ( ! empty( $child ) && ! empty( $meta['max_children'] ) ) {
							if ( ! empty( $meta['max_children'] ) && $meta['max_children'] >= $child && $meta['max_children'] != 0 ) {

								if ( ! empty( $infant ) && ! empty( $meta['max_infants'] ) ) {
									if ( ! empty( $meta['max_infants'] ) && $meta['max_infants'] >= $infant && $meta['max_infants'] != 0 ) {
										if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
											$tf_aprt_booked_dates = [];
											if ( ! empty( $booked_dates ) ) {
												foreach ( $booked_dates as $booked_date ) {
													$booked_from = $booked_date['check_in'];
													$booked_to   = $booked_date['check_out'];

													$tfbookedperiod = new DatePeriod(
														new DateTime( $booked_from . ' 00:00' ),
														new DateInterval( 'P1D' ),
														new DateTime( $booked_to . ' 23:59' )
													);

													foreach ( $tfbookedperiod as $date ) {
														$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
													}
												}
											}
											$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
											$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
											if(!empty($avil_by_date) && !empty($apt_availability_dates)){
												$tf_check_in_date = 0;
												$searching_period = [];
												// Check if any date range match with search form date range and set them on array
												if ( ! empty( $period ) ) {
													foreach ( $period as $datekey => $date ) {
														if(0==$datekey){
															$tf_check_in_date = $date->format( 'Y/m/d' );
														}
														$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
													}
												}

												$availability_dates = [];
												$tf_check_in_date_price = [];
												// Run loop through custom date range repeater and filter out only the dates
											
												if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
													$apt_availability_dates = json_decode( $apt_availability_dates, true );
													foreach($apt_availability_dates as $sdate){
														if($tf_check_in_date==$sdate['check_in']){
															$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
														}
														if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
															$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
														}
													}
												}
												
												$tf_common_dates = array_intersect($availability_dates, $searching_period);
												if (count($tf_common_dates) === count($searching_period)) {
													if ( ! empty( $tf_check_in_date_price['price'] ) ) {
														if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
															$has_apartment = true;
														}
													}
												}
											}else{
												if ( ! empty( $meta['price_per_night'] ) && $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
													$tf_booked_dates = [];
													if ( ! empty( $booked_dates ) ) {
														foreach ( $booked_dates as $booked_date ) {
															$booked_from = $booked_date['check_in'];
															$booked_to   = $booked_date['check_out'];

															$tfbookedperiod = new DatePeriod(
																new DateTime( $booked_from . ' 00:00' ),
																new DateInterval( 'P1D' ),
																new DateTime( $booked_to . ' 23:59' )
															);

															foreach ( $tfbookedperiod as $date ) {
																$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
															}
														}
														foreach ( $avail_searching_date as $searching ) {
															if ( array_key_exists( $searching, $tf_booked_dates ) ) {
																$has_apartment = false;
																break;
															} else {
																$has_apartment = true;
															}
														}
													} else {
														$has_apartment = true;
													}
												}
											}
										} else {

											$tf_aprt_booked_dates = [];
											if ( ! empty( $booked_dates ) ) {
												foreach ( $booked_dates as $booked_date ) {
													$booked_from = $booked_date['check_in'];
													$booked_to   = $booked_date['check_out'];

													$tfbookedperiod = new DatePeriod(
														new DateTime( $booked_from . ' 00:00' ),
														new DateInterval( 'P1D' ),
														new DateTime( $booked_to . ' 23:59' )
													);

													foreach ( $tfbookedperiod as $date ) {
														$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
													}
												}
											}
											$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
											$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
											if(!empty($avil_by_date) && !empty($apt_availability_dates)){
												$tf_check_in_date = 0;
												$searching_period = [];
												// Check if any date range match with search form date range and set them on array
												if ( ! empty( $period ) ) {
													foreach ( $period as $datekey => $date ) {
														if(0==$datekey){
															$tf_check_in_date = $date->format( 'Y/m/d' );
														}
														$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
													}
												}

												$availability_dates = [];
												$tf_check_in_date_price = [];
												// Run loop through custom date range repeater and filter out only the dates
											
												if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
													$apt_availability_dates = json_decode( $apt_availability_dates, true );
													foreach($apt_availability_dates as $sdate){
														if($tf_check_in_date==$sdate['check_in']){
															$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
														}
														if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
															$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
														}
													}
												}
												
												$tf_common_dates = array_intersect($availability_dates, $searching_period);
												if (count($tf_common_dates) === count($searching_period)) {
													$has_apartment = true;
												}
											}else{
												$tf_booked_dates = [];
												if ( ! empty( $booked_dates ) ) {
													foreach ( $booked_dates as $booked_date ) {
														$booked_from = $booked_date['check_in'];
														$booked_to   = $booked_date['check_out'];

														$tfbookedperiod = new DatePeriod(
															new DateTime( $booked_from . ' 00:00' ),
															new DateInterval( 'P1D' ),
															new DateTime( $booked_to . ' 23:59' )
														);

														foreach ( $tfbookedperiod as $date ) {
															$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
														}
													}
													foreach ( $avail_searching_date as $searching ) {
														if ( array_key_exists( $searching, $tf_booked_dates ) ) {
															$has_apartment = false;
															break;
														} else {
															$has_apartment = true;
														}
													}
												} else {
													$has_apartment = true;
												}
											}
										}
									}
								} else {
									if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
										$tf_aprt_booked_dates = [];
										if ( ! empty( $booked_dates ) ) {
											foreach ( $booked_dates as $booked_date ) {
												$booked_from = $booked_date['check_in'];
												$booked_to   = $booked_date['check_out'];

												$tfbookedperiod = new DatePeriod(
													new DateTime( $booked_from . ' 00:00' ),
													new DateInterval( 'P1D' ),
													new DateTime( $booked_to . ' 23:59' )
												);

												foreach ( $tfbookedperiod as $date ) {
													$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
												}
											}
										}
										$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
										$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
										if(!empty($avil_by_date) && !empty($apt_availability_dates)){
											$tf_check_in_date = 0;
											$searching_period = [];
											// Check if any date range match with search form date range and set them on array
											if ( ! empty( $period ) ) {
												foreach ( $period as $datekey => $date ) {
													if(0==$datekey){
														$tf_check_in_date = $date->format( 'Y/m/d' );
													}
													$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
												}
											}

											$availability_dates = [];
											$tf_check_in_date_price = [];
											// Run loop through custom date range repeater and filter out only the dates
										
											if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
												$apt_availability_dates = json_decode( $apt_availability_dates, true );
												foreach($apt_availability_dates as $sdate){
													if($tf_check_in_date==$sdate['check_in']){
														$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
													}
													if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
														$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
													}
												}
											}
											
											$tf_common_dates = array_intersect($availability_dates, $searching_period);
											if (count($tf_common_dates) === count($searching_period)) {
												if ( ! empty( $tf_check_in_date_price['price'] ) ) {
													if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
														$has_apartment = true;
													}
												}
											}
										}else{
											if ( ! empty( $meta['price_per_night'] ) && $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
												$tf_booked_dates = [];
												if ( ! empty( $booked_dates ) ) {
													foreach ( $booked_dates as $booked_date ) {
														$booked_from = $booked_date['check_in'];
														$booked_to   = $booked_date['check_out'];

														$tfbookedperiod = new DatePeriod(
															new DateTime( $booked_from . ' 00:00' ),
															new DateInterval( 'P1D' ),
															new DateTime( $booked_to . ' 23:59' )
														);

														foreach ( $tfbookedperiod as $date ) {
															$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
														}
													}
													foreach ( $avail_searching_date as $searching ) {
														if ( array_key_exists( $searching, $tf_booked_dates ) ) {
															$has_apartment = false;
															break;
														} else {
															$has_apartment = true;
														}
													}
												} else {
													$has_apartment = true;
												}
											}
										}
									} else {

										$tf_aprt_booked_dates = [];
										if ( ! empty( $booked_dates ) ) {
											foreach ( $booked_dates as $booked_date ) {
												$booked_from = $booked_date['check_in'];
												$booked_to   = $booked_date['check_out'];

												$tfbookedperiod = new DatePeriod(
													new DateTime( $booked_from . ' 00:00' ),
													new DateInterval( 'P1D' ),
													new DateTime( $booked_to . ' 23:59' )
												);

												foreach ( $tfbookedperiod as $date ) {
													$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
												}
											}
										}
										$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
										$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
										if(!empty($avil_by_date) && !empty($apt_availability_dates)){
											$tf_check_in_date = 0;
											$searching_period = [];
											// Check if any date range match with search form date range and set them on array
											if ( ! empty( $period ) ) {
												foreach ( $period as $datekey => $date ) {
													if(0==$datekey){
														$tf_check_in_date = $date->format( 'Y/m/d' );
													}
													$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
												}
											}

											$availability_dates = [];
											$tf_check_in_date_price = [];
											// Run loop through custom date range repeater and filter out only the dates
										
											if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
												$apt_availability_dates = json_decode( $apt_availability_dates, true );
												foreach($apt_availability_dates as $sdate){
													if($tf_check_in_date==$sdate['check_in']){
														$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
													}
													if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
														$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
													}
												}
											}
											
											$tf_common_dates = array_intersect($availability_dates, $searching_period);
											if (count($tf_common_dates) === count($searching_period)) {
												$has_apartment = true;
											}
										}else{
											$tf_booked_dates = [];
											if ( ! empty( $booked_dates ) ) {
												foreach ( $booked_dates as $booked_date ) {
													$booked_from = $booked_date['check_in'];
													$booked_to   = $booked_date['check_out'];

													$tfbookedperiod = new DatePeriod(
														new DateTime( $booked_from . ' 00:00' ),
														new DateInterval( 'P1D' ),
														new DateTime( $booked_to . ' 23:59' )
													);

													foreach ( $tfbookedperiod as $date ) {
														$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
													}
												}
												foreach ( $avail_searching_date as $searching ) {
													if ( array_key_exists( $searching, $tf_booked_dates ) ) {
														$has_apartment = false;
														break;
													} else {
														$has_apartment = true;
													}
												}
											} else {
												$has_apartment = true;
											}
										}
									}
								}
							}
						} else {
							if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
								$tf_aprt_booked_dates = [];
								if ( ! empty( $booked_dates ) ) {
									foreach ( $booked_dates as $booked_date ) {
										$booked_from = $booked_date['check_in'];
										$booked_to   = $booked_date['check_out'];

										$tfbookedperiod = new DatePeriod(
											new DateTime( $booked_from . ' 00:00' ),
											new DateInterval( 'P1D' ),
											new DateTime( $booked_to . ' 23:59' )
										);

										foreach ( $tfbookedperiod as $date ) {
											$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
										}
									}
								}
								$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
								$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
								if(!empty($avil_by_date) && !empty($apt_availability_dates)){
									$tf_check_in_date = 0;
									$searching_period = [];
									// Check if any date range match with search form date range and set them on array
									if ( ! empty( $period ) ) {
										foreach ( $period as $datekey => $date ) {
											if(0==$datekey){
												$tf_check_in_date = $date->format( 'Y/m/d' );
											}
											$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
										}
									}

									$availability_dates = [];
									$tf_check_in_date_price = [];
									// Run loop through custom date range repeater and filter out only the dates
								
									if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
										$apt_availability_dates = json_decode( $apt_availability_dates, true );
										foreach($apt_availability_dates as $sdate){
											if($tf_check_in_date==$sdate['check_in']){
												$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
											}
											if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
												$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
											}
										}
									}
									
									$tf_common_dates = array_intersect($availability_dates, $searching_period);
									if (count($tf_common_dates) === count($searching_period)) {
										if ( ! empty( $tf_check_in_date_price['price'] ) ) {
											if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
												$has_apartment = true;
											}
										}
									}
								}else{
									if ( ! empty( $meta['price_per_night'] ) && $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
										$tf_booked_dates = [];
										if ( ! empty( $booked_dates ) ) {
											foreach ( $booked_dates as $booked_date ) {
												$booked_from = $booked_date['check_in'];
												$booked_to   = $booked_date['check_out'];

												$tfbookedperiod = new DatePeriod(
													new DateTime( $booked_from . ' 00:00' ),
													new DateInterval( 'P1D' ),
													new DateTime( $booked_to . ' 23:59' )
												);

												foreach ( $tfbookedperiod as $date ) {
													$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
												}
											}
											foreach ( $avail_searching_date as $searching ) {
												if ( array_key_exists( $searching, $tf_booked_dates ) ) {
													$has_apartment = false;
													break;
												} else {
													$has_apartment = true;
												}
											}
										} else {
											$has_apartment = true;
										}
									}
								}
							} else {
								$tf_aprt_booked_dates = [];
								if ( ! empty( $booked_dates ) ) {
									foreach ( $booked_dates as $booked_date ) {
										$booked_from = $booked_date['check_in'];
										$booked_to   = $booked_date['check_out'];

										$tfbookedperiod = new DatePeriod(
											new DateTime( $booked_from . ' 00:00' ),
											new DateInterval( 'P1D' ),
											new DateTime( $booked_to . ' 23:59' )
										);

										foreach ( $tfbookedperiod as $date ) {
											$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
										}
									}
								}
								$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
								$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
								if(!empty($avil_by_date) && !empty($apt_availability_dates)){
									$tf_check_in_date = 0;
									$searching_period = [];
									// Check if any date range match with search form date range and set them on array
									if ( ! empty( $period ) ) {
										foreach ( $period as $datekey => $date ) {
											if(0==$datekey){
												$tf_check_in_date = $date->format( 'Y/m/d' );
											}
											$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
										}
									}

									$availability_dates = [];
									$tf_check_in_date_price = [];
									// Run loop through custom date range repeater and filter out only the dates
								
									if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
										$apt_availability_dates = json_decode( $apt_availability_dates, true );
										foreach($apt_availability_dates as $sdate){
											if($tf_check_in_date==$sdate['check_in']){
												$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
											}
											if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
												$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
											}
										}
									}
									
									$tf_common_dates = array_intersect($availability_dates, $searching_period);
									if (count($tf_common_dates) === count($searching_period)) {
										$has_apartment = true;
									}
								}else{
									$tf_booked_dates = [];
									if ( ! empty( $booked_dates ) ) {
										foreach ( $booked_dates as $booked_date ) {
											$booked_from = $booked_date['check_in'];
											$booked_to   = $booked_date['check_out'];

											$tfbookedperiod = new DatePeriod(
												new DateTime( $booked_from . ' 00:00' ),
												new DateInterval( 'P1D' ),
												new DateTime( $booked_to . ' 23:59' )
											);

											foreach ( $tfbookedperiod as $date ) {
												$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
											}
										}
										foreach ( $avail_searching_date as $searching ) {
											if ( array_key_exists( $searching, $tf_booked_dates ) ) {
												$has_apartment = false;
												break;
											} else {
												$has_apartment = true;
											}
										}
									} else {
										$has_apartment = true;
									}
								}
							}
						}
					}
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
	function get_apartment_min_max_price( $post_id = null ) {
		$min_max_price = array();

		$apartment_args = array(
			'post_type'      => 'tf_apartment',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		);

		if ( isset( $post_id ) && ! empty( $post_id ) ) {
			$apartment_args['post__in'] = array( $post_id );
		}
		$apartment_query = new WP_Query( $apartment_args );

		if ( $apartment_query->have_posts() ) {
			while ( $apartment_query->have_posts() ) {
				$apartment_query->the_post();
				$meta                = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
				$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
				$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
				$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
				if ( $enable_availability === '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
					$apt_availability = ! empty( $meta['apt_availability'] ) ? json_decode( $meta['apt_availability'], true ) : [];

					if ( ! empty( $apt_availability ) && is_array( $apt_availability ) ) {
						foreach ( $apt_availability as $single_avail ) {
							if ( $pricing_type === 'per_night' ) {
								$min_max_price[] = ! empty( $single_avail['price'] ) ? intval( $single_avail['price'] ) : 0;

							} else {
								$min_max_price[] = ! empty( $single_avail['adult_price'] ) ? intval( $single_avail['adult_price'] ) : 0;
							}
						}
					}

				} else {
					$min_max_price[] = $pricing_type === 'per_night' && ! empty( $meta['price_per_night'] ) ? intval( $meta['price_per_night'] ) : intval( $adult_price );
				}
			}
		}

		$min_max_price = array_filter($min_max_price);

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
