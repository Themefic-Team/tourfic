<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * WC Product Extend
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' ) ) {
	function fida() {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php';
	}

	add_action( 'init', 'fida' );
} else {
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' );
}

/**
 * Helper Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-helper.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-helper.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-helper.php' );
}

/**
 * Hotel Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-hotel.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-hotel.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-hotel.php' );
}

/**
 * Apartment Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-apartment.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-apartment.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-apartment.php' );
}

/**
 * Tour Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-tour.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-tour.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-tour.php' );
}

/**
 * WooCommerce Common Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-common.php' ) ) {
	require_once TF_INC_PATH . 'functions/woocommerce/wc-common.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-common.php' );
}

/**
 * Booking Details Functions
 */
if ( file_exists( TF_INC_PATH . 'booking-details/booking_details.php' ) ) {
	require_once TF_INC_PATH . 'booking-details/booking_details.php';
} else {
	tf_file_missing( TF_INC_PATH . 'booking-details/booking_details.php' );
}

/**
 * Wishlist Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-wishlist.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-wishlist.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-wishlist.php' );
}

/**
 * Review Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-review.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-review.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-review.php' );
}

/**
 * inquiry Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_enquiry.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions_enquiry.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions_enquiry.php' );
}

/**
 * Order page Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_order.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions_order.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions_order.php' );
}
/**
 * Include export import function file
 */
if( file_exists( TF_INC_PATH . 'functions/functions-settings-import-export.php' ) ){
	require_once TF_INC_PATH . 'functions/functions-settings-import-export.php';
}else{
	tf_file_missing( TF_INC_PATH . 'functions/functions-settings-import-export.php' );
}

/**
 * Include Post Duplicator function file
 */
if( file_exists( TF_INC_PATH . 'functions/functions_duplicator.php' ) ){
	require_once TF_INC_PATH . 'functions/functions_duplicator.php';
}else{
	tf_file_missing( TF_INC_PATH . 'functions/functions_duplicator.php' );
}

/**
 * Including CSS & JS
 *
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'enqueues.php' ) ) {
	require_once TF_INC_PATH . 'enqueues.php';
} else {
	tf_file_missing( TF_INC_PATH . 'enqueues.php' );
}

/**
 * Shortcodes
 *
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'functions/shortcodes.php' ) ) {
	require_once TF_INC_PATH . 'functions/shortcodes.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/shortcodes.php' );
}

/**
 * Widgets
 *
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'functions/widgets.php' ) ) {
	require_once TF_INC_PATH . 'functions/widgets.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/widgets.php' );
}

# Google Fonts
if ( file_exists( TF_INC_PATH . 'functions/functions-fonts.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-fonts.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-fonts.php' );
}

/**
 * Elementor Widgets
 *
 */
function tf_add_elelmentor_addon() {

	// Check if Elementor installed and activated
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}
	// Once we get here, We have passed all validation checks so we can safely include our plugin
	if ( file_exists( TF_INC_PATH . 'elementor/widget-register.php' ) ) {
		require_once TF_INC_PATH . 'elementor/widget-register.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'elementor/widget-register.php' );
	}

}

add_action( 'plugins_loaded', 'tf_add_elelmentor_addon' );

/**
 * Notice
 *
 * Update
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-notice_update.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-notice_update.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-notice_update.php' );
}

/**
 * Necessary Image Sizes
 *
 * @since 1.0
 */
if ( ! function_exists( 'tf_image_sizes' ) ) {
	function tf_image_sizes() {
		// Hotel gallery, hard crop
		add_image_size( 'tf_apartment_gallery_large', 819, 475, true );
		add_image_size( 'tf_apartment_gallery_small', 333, 231, true );
		add_image_size( 'tf_apartment_single_thumb', 1170, 500, true );
		add_image_size( 'tf_gallery_thumb', 900, 490, true );
		add_image_size( 'tf-thumb-480-320', 480, 320, true );
	}

	add_filter( 'after_setup_theme', 'tf_image_sizes' );
}


/**
 * Assign Single Template
 *
 * @since 1.0
 */
if ( ! function_exists( 'tf_single_page_template' ) ) {
	function tf_single_page_template( $single_template ) {

		global $post;

		/**
		 * Hotel Single
		 *
		 * single-hotel.php
		 */
		if ( 'tf_hotel' === $post->post_type ) {

			$theme_files     = array( 'tourfic/hotel/single-hotel.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "hotel/single-hotel.php";
			}
		}

		/**
		 * Apartment Single
		 *
		 * single-apartment.php
		 */
		if ( 'tf_apartment' === $post->post_type ) {

			$theme_files     = array( 'tourfic/apartment/single-apartment.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "apartment/single-apartment.php";
			}
		}

		/**
		 * Tour Single
		 *
		 * single-tour.php
		 */
		if ( $post->post_type == 'tf_tours' ) {

			$theme_files     = array( 'tourfic/tour/single-tour.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "tour/single-tour.php";
			}
		}

		return $single_template;
	}

	add_filter( 'single_template', 'tf_single_page_template' );
}

/**
 * Assign Archive Template
 *
 * @since 1.0
 */
if ( ! function_exists( 'tourfic_archive_page_template' ) ) {
	function tourfic_archive_page_template( $template ) {
		if ( is_post_type_archive( 'tf_hotel' ) ) {
			$theme_files     = array( 'tourfic/hotel/archive-hotels.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'hotel/archive-hotels.php';
			}
		}


		if ( is_post_type_archive( 'tf_apartment' ) ) {
			$theme_files     = array( 'tourfic/apartment/archive-apartments.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'apartment/archive-apartments.php';
			}
		}

		if ( is_post_type_archive( 'tf_tours' ) ) {
			$theme_files     = array( 'tourfic/tour/archive-tours.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'tour/archive-tours.php';
			}
		}

		return $template;
	}

	add_filter( 'template_include', 'tourfic_archive_page_template' );
}

/**
 * Assign Review Template Part
 *
 * @since 1.0
 */
if ( ! function_exists( 'load_comment_template' ) ) {
	function load_comment_template( $comment_template ) {
		global $post;

		if ( 'tf_hotel' === $post->post_type || 'tf_tours' === $post->post_type || 'tf_apartment' === $post->post_type ) {
			$theme_files     = array( 'tourfic/template-parts/review.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'template-parts/review.php';
			}
		}

	}

	add_filter( 'comments_template', 'load_comment_template' );
}

/*
 * Asign Destination taxonomy template
 */

add_filter( 'template_include', 'taxonomy_template' );
function taxonomy_template( $template ) {

	if ( is_tax( 'hotel_location' ) ) {

		$theme_files     = array( 'tourfic/hotel/taxonomy-hotel_locations.php' );
		$exists_in_theme = locate_template( $theme_files, false );

		if ( $exists_in_theme ) {
			$template = $exists_in_theme;
		} else {
			$template = TF_TEMPLATE_PATH . 'hotel/taxonomy-hotel_locations.php';
		}
	}

	if ( is_tax( 'apartment_location' ) ) {
		$theme_files     = array( 'tourfic/apartment/taxonomy-apartment_locations.php' );
		$exists_in_theme = locate_template( $theme_files, false );

		if ( $exists_in_theme ) {
			$template = $exists_in_theme;
		} else {
			$template = TF_TEMPLATE_PATH . 'apartment/taxonomy-apartment_locations.php';
		}
	}

	if ( is_tax( 'tour_destination' ) ) {

		$theme_files     = array( 'tourfic/tour/taxonomy-tour_destinations.php' );
		$exists_in_theme = locate_template( $theme_files, false );

		if ( $exists_in_theme ) {
			$template = $exists_in_theme;
		} else {
			$template = TF_TEMPLATE_PATH . 'tour/taxonomy-tour_destinations.php';
		}

	}

	return $template;

}

/**
 * Add tour & hotel capabilities to admin & editor
 *
 * tf_tours & tf_hotel
 */
if ( ! function_exists( 'tf_admin_role_caps' ) ) {
	function tf_admin_role_caps() {

		if ( get_option( 'tf_admin_caps' ) < 2 ) {
			$admin_role  = get_role( 'administrator' );
			$editor_role = get_role( 'editor' );

			// Add a new capability.
			$caps = array(
				// Hotels
				'edit_tf_hotel',
				'read_tf_hotel',
				'delete_tf_hotel',
				'edit_tf_hotels',
				'edit_others_tf_hotels',
				'publish_tf_hotels',
				'read_private_tf_hotels',
				'delete_tf_hotels',
				'delete_private_tf_hotels',
				'delete_published_tf_hotels',
				'delete_others_tf_hotels',
				'edit_private_tf_hotels',
				'edit_published_tf_hotels',
				'create_tf_hotels',
				// Apartment
				'edit_tf_apartment',
				'read_tf_apartment',
				'delete_tf_apartment',
				'edit_tf_apartments',
				'edit_others_tf_apartments',
				'publish_tf_apartments',
				'read_private_tf_apartments',
				'delete_tf_apartments',
				'delete_private_tf_apartments',
				'delete_published_tf_apartments',
				'delete_others_tf_apartments',
				'edit_private_tf_apartments',
				'edit_published_tf_apartments',
				'create_tf_apartments',
				// Tours
				'edit_tf_tours',
				'read_tf_tours',
				'delete_tf_tours',
				'edit_tf_tourss',
				'edit_others_tf_tourss',
				'publish_tf_tourss',
				'read_private_tf_tourss',
				'delete_tf_tourss',
				'delete_private_tf_tourss',
				'delete_published_tf_tourss',
				'delete_others_tf_tourss',
				'edit_private_tf_tourss',
				'edit_published_tf_tourss',
				'create_tf_tourss',
			);

			foreach ( $caps as $cap ) {
				$admin_role->add_cap( $cap );
				$editor_role->add_cap( $cap );
			}

			update_option( 'tf_admin_caps', 2 );
		}
	}

	add_action( 'admin_init', 'tf_admin_role_caps', 999 );
}

/**
 * Search Result Sidebar form
 */
function tf_search_result_sidebar_form( $placement = 'single' ) {

	// Unwanted Slashes Remove
	if ( isset( $_GET ) ) {
		$_GET = array_map( 'stripslashes_deep', $_GET );
	}

	// Get post type
	$post_type             = $_GET['type'] ?? '';
	$place_title           = '';
	$date_format_for_users = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

	if ( ! empty( $post_type ) ) {

		$place_input_id = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
		if ( $post_type == 'tf_apartment' ) {
			$place_input_id = 'tf-apartment-location';
		}
		$place_placeholder = ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) ? __( 'Enter Location', 'tourfic' ) : __( 'Enter Destination', 'tourfic' );

		$place_key   = 'place';
		$place_value = $_GET[ $place_key ] ?? '';
		$place_title = ! empty( $_GET['place-name'] ) ? $_GET['place-name'] : '';

		$taxonomy = $post_type == 'tf_hotel' ? 'hotel_location' : ( $post_type == 'tf_tour' ? 'tour_destination' : 'apartment_location' );
		// $place_name = ! empty( $place_value ) ? get_term_by( 'slug', $place_value, $taxonomy )->name : '';
		$place_name = ! empty( $place_value ) ? $place_value : '';

		$room = $_GET['room'] ?? 0;
	}

	$adult      = $_GET['adults'] ?? 0;
	$children   = $_GET['children'] ?? 0;
	$infant     = $_GET['infant'] ?? 0;
	$date       = $_GET['check-in-out-date'] ?? '';
	$startprice = $_GET['from'] ?? '';
	$endprice   = $_GET['to'] ?? '';

	$tf_tour_arc_selected_template  = ! empty( tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
	$tf_hotel_arc_selected_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';

	$disable_child_search = ! empty( tfopt( 'disable_child_search' ) ) ? tfopt( 'disable_child_search' ) : '';
	$disable_infant_search = ! empty( tfopt( 'disable_infant_search' ) ) ? tfopt( 'disable_infant_search' ) : '';
	$disable_hotel_child_search = ! empty( tfopt( 'disable_hotel_child_search' ) ) ? tfopt( 'disable_hotel_child_search' ) : '';
	$disable_apartment_child_search = ! empty( tfopt( 'disable_apartment_child_search' ) ) ? tfopt( 'disable_apartment_child_search' ) : '';
	$disable_apartment_infant_search = ! empty( tfopt( 'disable_apartment_infant_search' ) ) ? tfopt( 'disable_apartment_infant_search' ) : '';

	if ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-1" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-1" ) ) {
		?>
        <div class="tf-box-wrapper tf-box tf-mrbottom-30">
            <form class="widget tf-hotel-side-booking" method="get" autocomplete="off"
                  action="<?php echo tf_booking_search_action(); ?>" id="tf-widget-booking-search">

                <div class="tf-field-group tf-destination-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <input type="text" id="<?php echo $place_input_id ?? ''; ?>" required="" class="tf-field" placeholder="<?php echo $place_placeholder ?? __( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo ! empty( $place_title ) ? $place_title : ''; ?>">
                    <input type="hidden" name="place" id="tf-place" value="<?php echo $place_value ?? ''; ?>"/>
                </div>
                <div class="tf-field-group tf-mt-8 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-regular fa-user"></i>
							<?php _e( 'Adults', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adult ) ? $adult : 1; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>

                <div class="tf-field-group tf-mt-16 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-solid fa-child"></i>
							<?php _e( 'Children', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $children ) ? $children : 0; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>

                <div class="tf-field-group tf-mt-8">
                    <i class="fa-solid fa-calendar-days"></i>
                    <input type="text" class="tf-field time" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                           placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" required value="<?php echo $date ?>">
                </div>


				<?php if ( $post_type == 'tf_hotel' ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa fa-building"></i>
								<?php _e( 'Rooms', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? $room : 1; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
				<?php } ?>

                <div class="tf-booking-bttns tf-mt-30">
					<?php
					$ptype = $_GET['type'] ?? get_post_type();
					?>
                    <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
                    <button class="tf-btn-normal btn-primary tf-submit"
                            type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                </div>
            </form>
        </div>
		<script>
			(function ($) {
				$(document).ready(function () {

					$(".tf-hotel-side-booking #check-in-out-date").flatpickr({
						enableTime: false,
						minDate: "today",
						altInput: true,
						altFormat: '<?php echo $date_format_for_users; ?>',
						mode: "range",
						dateFormat: "Y/m/d",
						onReady: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
						onChange: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
						defaultDate: <?php echo json_encode( explode( '-', $date ) ) ?>,
					});

				});
			})(jQuery);
		</script>

		<?php if ( is_active_sidebar( 'tf_search_result' ) ) { ?>
			<div id="tf__booking_sidebar">
				<?php dynamic_sidebar( 'tf_search_result' ); ?>
			</div>
		<?php } ?>

	<?php }
	if ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-2" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-2" ) ) { ?>
		<div class="tf-booking-form-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
			<div class="tf-booking-form-location">
				<span class="tf-booking-form-title"><?php _e("Location", "tourfic"); ?></span>
				<label for="tf-search-location" class="tf-booking-location-wrap">
					<svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
					<path d="M8.5 13.9317L11.7998 10.6318C13.6223 8.80943 13.6223 5.85464 11.7998 4.0322C9.9774 2.20975 7.02261 2.20975 5.20017 4.0322C3.37772 5.85464 3.37772 8.80943 5.20017 10.6318L8.5 13.9317ZM8.5 15.8173L4.25736 11.5747C1.91421 9.2315 1.91421 5.43254 4.25736 3.08939C6.60051 0.746245 10.3995 0.746245 12.7427 3.08939C15.0858 5.43254 15.0858 9.2315 12.7427 11.5747L8.5 15.8173ZM8.5 8.66536C9.2364 8.66536 9.83333 8.06843 9.83333 7.33203C9.83333 6.59565 9.2364 5.9987 8.5 5.9987C7.7636 5.9987 7.16667 6.59565 7.16667 7.33203C7.16667 8.06843 7.7636 8.66536 8.5 8.66536ZM8.5 9.9987C7.02724 9.9987 5.83333 8.80476 5.83333 7.33203C5.83333 5.85927 7.02724 4.66536 8.5 4.66536C9.97273 4.66536 11.1667 5.85927 11.1667 7.33203C11.1667 8.80476 9.97273 9.9987 8.5 9.9987Z" fill="#595349"/>
					</svg>

					<input type="text" id="<?php echo $place_input_id ?? ''; ?>" required="" class="tf-field" placeholder="<?php echo $place_placeholder ?? __( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo !empty($place_title) ? $place_title : ''; ?>">
                    <input type="hidden" name="place" id="tf-place" value="<?php echo $place_value ?? ''; ?>"/>
				</label>
			</div>
			<?php if ( $post_type == 'tf_hotel' ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php _e("Check in", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo date('M'); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
				<div class="tf_booking-dates">
					<div class="tf_label-row"></div>
				</div>
			</div>
			<div class="tf-booking-form-checkout">
				<span class="tf-booking-form-title"><?php _e("Check out", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo date('M'); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
				<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" value="<?php echo $date ?>" required>

			</div>
			<?php } ?>

			<?php if ( $post_type == 'tf_tours' ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php _e("Date", "tourfic"); ?></span>
				<div class="tf-tour-searching-date-block">
					<div class="tf-booking-date-wrap tf-tour-start-date">
						<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo date('M'); ?></span>
						</span>
					</div>
					<div class="tf-duration">
						<span>-</span>
					</div>
					<div class="tf-booking-date-wrap tf-tour-end-date">
						<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo date('M'); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
					</div>
					<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $date ) ? 'value="' . $date . '"' : '' ?> required>
				</div>
			</div>
			<?php } ?>

			<div class="tf-booking-form-guest-and-room">
				<?php if ( $post_type == 'tf_hotel' ) { ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php _e("Guests & rooms", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap tf-archive-guest-info">
						<span class="tf-guest"><?php echo $adult+$children ?> </span> <?php _e("guest", "tourfic"); ?> <span class="tf-room"><?php echo $room; ?></span> <?php _e("Rooms", "tourfic"); ?>
					</div>
					<div class="tf-arrow-icons">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</div>
				</div>
				<?php } if ( $post_type == 'tf_tours' ) { ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php _e("Guests", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap">
						<span class="tf-guest tf-booking-date">
							0<?php echo $adult+$children ?>
						</span>
						<span class="tf-booking-month">
							<span><?php _e("Guest", "tourfic"); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
					</div>
				</div>
				<?php } ?>

				<div class="tf_acrselection-wrap">
					<div class="tf_acrselection-inner">
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Adults", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adult ) ? $adult : 1; ?>">
								<div class="acr-inc">+</div>
							</div>
						</div>
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Children", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $children ) ? $children : 0; ?>">
								<div class="acr-inc">+</div>
							</div>
						</div>
						<?php if ( $post_type == 'tf_hotel' ) { ?>
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Rooms", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? $room : 1; ?>">
								<div class="acr-inc">+</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="tf-booking-form-submit">
			<?php
			$ptype = $_GET['type'] ?? get_post_type();
			?>
			<input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
            <button class="tf-btn-normal btn-primary tf-submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
		</div>
		<?php if ( $post_type == 'tf_tours' ) { ?>
		<script>
			(function ($) {
				$(document).ready(function () {
					// flatpickr locale first day of Week
					<?php tf_flatpickr_locale("root"); ?>

					$(".tf-template-3 .tf-booking-date-wrap").click(function(){
						$("#check-in-out-date").click();
					});
					$("#check-in-out-date").flatpickr({
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
						<?php
						if(!empty($date)){ ?>
						defaultDate: <?php echo json_encode( explode( '-', $date ) ) ?>,
						<?php } ?>
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const monthNames = [
								"Jan", "Feb", "Mar", "Apr", "May", "Jun",
								"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
							];
							if(selectedDates[0]){
								const startDate = selectedDates[0];
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-date").html(startDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
							}
							if(selectedDates[1]){
								const endDate = selectedDates[1];
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-date").html(endDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
							}
						}
					}

				});
			})(jQuery);
		</script>
		<?php } ?>

		<?php if ( $post_type == 'tf_hotel' ) { ?>
		
			<script>
			(function ($) {
				$(document).ready(function () {
				
					// flatpickr locale
					<?php tf_flatpickr_locale("root"); ?>

					$(".tf-template-3 .tf-booking-date-wrap").click(function(){
						$("#check-in-out-date").click();
					});
					$("#check-in-out-date").flatpickr({
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
						defaultDate: <?php echo json_encode( explode( '-', $date ) ) ?>,
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const monthNames = [
								"Jan", "Feb", "Mar", "Apr", "May", "Jun",
								"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
							];
							if(selectedDates[0]){
								const startDate = selectedDates[0];
								$(".tf-template-3 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()+1]);
							}
							if(selectedDates[1]){
								const endDate = selectedDates[1];
								$(".tf-template-3 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()+1]);
							}
						}
					}

				});
			})(jQuery);
		</script>
		<?php } ?>
	<?php } else { ?>
        <!-- Start Booking widget -->
        <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
              action="<?php echo tf_booking_search_action(); ?>" id="tf-widget-booking-search">

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" id="<?php echo $place_input_id ?? ''; ?>" required="" class="" placeholder="<?php echo $place_placeholder ?? __( 'Location/Destination', 'tourfic' ); ?>"
                               value="<?php echo ! empty( $place_title ) ? $place_title : ''; ?>">
                        <input type="hidden" name="place" id="tf-place" value="<?php echo $place_value ?? ''; ?>"/>
                    </div>
                </label>
            </div>

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="fas fa-user-friends"></i>
                        <select name="adults" id="adults" class="">
                            <option <?php echo 1 == $adult ? 'selected' : null ?> value="1">1 <?php _e( 'Adult', 'tourfic' ); ?></option>
							<?php foreach ( range( 2, 8 ) as $value ) {
								$selected = $value == $adult ? 'selected' : null;
								echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __( "Adults", "tourfic" ) . '</option>';
							} ?>
                        </select>
                    </div>
                </label>
            </div>
			<?php if ( $post_type == 'tf_tours' && empty( $disable_child_search ) ) : ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-child"></i>
                            <select name="children" id="children" class="">
                                <option value="0">0 <?php _e( 'Children', 'tourfic' ); ?></option>
                                <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 <?php _e( 'Children', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $children ? 'selected' : null;
									echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __( "Children", "tourfic" ) . '</option>';
								} ?>

                            </select>
                        </div>
                    </label>
                </div>
			<?php endif; ?>
			<?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
			           ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
			) { ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-child"></i>
                            <select name="children" id="children" class="">
                                <option value="0">0 <?php _e( 'Children', 'tourfic' ); ?></option>
                                <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 <?php _e( 'Children', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $children ? 'selected' : null;
									echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __( "Children", "tourfic" ) . '</option>';
								} ?>

                            </select>
                        </div>
                    </label>
                </div>
			<?php } ?>
			<?php if ( ( $post_type == 'tf_tours' && empty( $disable_infant_search ) ) ||
			           ( $post_type == 'tf_apartment' && empty( $disable_apartment_infant_search ) )
			): ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-child"></i>
                            <select name="infant" id="infant" class="">
                                <option value="0">0 <?php _e( 'Infant', 'tourfic' ); ?></option>
                                <option <?php echo 1 == $infant ? 'selected' : null ?> value="1">1 <?php _e( 'Infant', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $infant ? 'selected' : null;
									echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __( "Infant", "tourfic" ) . '</option>';
								} ?>

                            </select>
                        </div>
                    </label>
                </div>
			<?php endif; ?>
			<?php if ( $post_type == 'tf_hotel' ) { ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-couch"></i>
                            <select name="room" id="room" class="">
                                <option <?php echo 1 == $room ? 'selected' : null ?> value="1">1 <?php _e( 'Room', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $room ? 'selected' : null;
									echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __( "Rooms", "tourfic" ) . '</option>';
								} ?>
                            </select>
                        </div>
                    </label>
                </div>
			<?php } ?>
            <div class="tf_booking-dates">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                   placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" required value="<?php echo $date ?>">
                        </div>
                    </label>
                </div>
            </div>

            <div class="tf_form-row">
				<?php
				if ( ! empty( $startprice ) && ! empty( $endprice ) ) { ?>
                    <input type="hidden" id="startprice" value="<?php echo $startprice; ?>">
                    <input type="hidden" id="endprice" value="<?php echo $endprice; ?>">
				<?php } ?>
				<?php
				if ( ! empty( $_GET['tf-author'] ) ) { ?>
                    <input type="hidden" id="tf_author" value="<?php echo esc_html( $_GET['tf-author'] ); ?>">
				<?php } ?>
				<?php
				$ptype = $_GET['type'] ?? get_post_type();
				?>
                <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
                <button class="tf_button tf-submit btn-styled"
                        type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
            </div>

        </form>
		<script>
			(function ($) {
				$(document).ready(function () {

					// flatpickr locale first day of Week
					<?php tf_flatpickr_locale("root"); ?>

					$(".tf-hotel-side-booking #check-in-out-date").flatpickr({
						enableTime: false,
						minDate: "today",
						altInput: true,
						altFormat: '<?php echo $date_format_for_users; ?>',
						mode: "range",
						dateFormat: "Y/m/d",

						// flatpickr locale
						<?php tf_flatpickr_locale(); ?>

						onReady: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
						onChange: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
						defaultDate: <?php echo json_encode( explode( '-', $date ) ) ?>,
					});

				});
			})(jQuery);
		</script>

		<?php if ( is_active_sidebar( 'tf_search_result' ) ) { ?>
			<div id="tf__booking_sidebar">
				<?php dynamic_sidebar( 'tf_search_result' ); ?>
			</div>
		<?php } ?>

	<?php } ?>

<?php
}

/**
 * Archive Sidebar Search Form
 */
function tf_archive_sidebar_search_form( $post_type, $taxonomy = '', $taxonomy_name = '', $taxonomy_slug = '' ) {
	$place = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
	if ( $post_type == 'tf_apartment' ) {
		$place = 'tf-apartment-location';
	}
	$place_text            = $post_type == 'tf_hotel' ? __( 'Enter Location', 'tourfic' ) : __( 'Enter Destination', 'tourfic' );
	$date_format_for_users = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

	$tf_tour_arc_selected_template  = ! empty( tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
	$tf_hotel_arc_selected_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';

	if ( ( is_post_type_archive( 'tf_hotel' ) && $tf_hotel_arc_selected_template == "design-1" ) || ( is_post_type_archive( 'tf_tours' ) && $tf_tour_arc_selected_template == "design-1" ) || ( $post_type == 'tf_hotel' && $tf_hotel_arc_selected_template == "design-1" ) || ( $post_type == 'tf_tours' && $tf_tour_arc_selected_template == "design-1" ) ) {
		?>
        <div class="tf-box-wrapper tf-box tf-mrbottom-30">
            <form action="<?php echo tf_booking_search_action(); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking">
                <div class="tf-field-group tf-destination-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <input type="text" required="" id="<?php echo $place; ?>" class="tf-field" placeholder="<?php echo $place_text; ?>" value="<?php echo ! empty( $taxonomy_name ) ? $taxonomy_name : ''; ?>">
                    <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? $taxonomy_slug : ''; ?>"/>
                </div>
                <div class="tf-field-group tf-mt-8 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-regular fa-user"></i>
							<?php _e( 'Adults', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="1" value="1">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>

                <div class="tf-field-group tf-mt-16 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-solid fa-child"></i>
							<?php _e( 'Children', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="childrens" id="children" min="0" value="0">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>

				<?php if ( $post_type !== 'tf_tours' ) { ?>

                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa fa-building"></i>
								<?php _e( 'Room', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="room" id="room" min="1" value="1">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
				<?php } ?>

                <div class="tf-field-group tf-mt-8">
                    <i class="fa-solid fa-calendar-days"></i>
                    <input type="text" class="tf-field time" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                           placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" required value="" style="width: 100% !important">
                </div>
                <div class="tf_booking-dates">
                    <div class="tf_label-row"></div>
                </div>
                <div class="tf-booking-bttns tf-mt-30">
                    <input type="hidden" name="type" value="<?php echo $post_type; ?>" class="tf-post-type"/>
                    <button class="tf-btn-normal btn-primary tf-submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                </div>
            </form>
        </div>
		<script>
			(function ($) {
				$(document).ready(function () {
					<?php tf_flatpickr_locale('root'); ?>

					$(document).on("focus",".tf-hotel-side-booking #check-in-out-date", function(e) {
						let calander = flatpickr( this, {
						enableTime: false,
						minDate: "today",
						mode: "range",
						dateFormat: "Y/m/d",
						altInput: true,
						altFormat: '<?php echo $date_format_for_users; ?>',

						// flatpickr locale
						<?php tf_flatpickr_locale(); ?>
						
						onChange: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
					});

					// open flatpickr on focus
					calander.open();
					})
				});
			})(jQuery);
		</script>

		<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
        <div id="tf__booking_sidebar">
			<?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
        </div>
		<?php } ?>

	<?php
	}
	elseif ( ( is_post_type_archive( 'tf_hotel' ) && $tf_hotel_arc_selected_template == "design-2" ) || ( is_post_type_archive( 'tf_tours' ) && $tf_tour_arc_selected_template == "design-2" ) || ( $post_type == 'tf_hotel' && $tf_hotel_arc_selected_template == "design-2" ) || ( $post_type == 'tf_tours' && $tf_tour_arc_selected_template == "design-2" ) ) { ?>
		<div class="tf-booking-form-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
			<div class="tf-booking-form-location">
				<span class="tf-booking-form-title"><?php _e("Location", "tourfic"); ?></span>
				<label for="tf-search-location" class="tf-booking-location-wrap">
					<svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
					<path d="M8.5 13.9317L11.7998 10.6318C13.6223 8.80943 13.6223 5.85464 11.7998 4.0322C9.9774 2.20975 7.02261 2.20975 5.20017 4.0322C3.37772 5.85464 3.37772 8.80943 5.20017 10.6318L8.5 13.9317ZM8.5 15.8173L4.25736 11.5747C1.91421 9.2315 1.91421 5.43254 4.25736 3.08939C6.60051 0.746245 10.3995 0.746245 12.7427 3.08939C15.0858 5.43254 15.0858 9.2315 12.7427 11.5747L8.5 15.8173ZM8.5 8.66536C9.2364 8.66536 9.83333 8.06843 9.83333 7.33203C9.83333 6.59565 9.2364 5.9987 8.5 5.9987C7.7636 5.9987 7.16667 6.59565 7.16667 7.33203C7.16667 8.06843 7.7636 8.66536 8.5 8.66536ZM8.5 9.9987C7.02724 9.9987 5.83333 8.80476 5.83333 7.33203C5.83333 5.85927 7.02724 4.66536 8.5 4.66536C9.97273 4.66536 11.1667 5.85927 11.1667 7.33203C11.1667 8.80476 9.97273 9.9987 8.5 9.9987Z" fill="#595349"/>
					</svg>

					<input type="text" required="" id="<?php echo $place; ?>" class="tf-field" placeholder="<?php echo $place_text; ?>" value="<?php echo ! empty( $taxonomy_name ) ? $taxonomy_name : ''; ?>">
                    <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? $taxonomy_slug : ''; ?>"/>
				</label>
			</div>

			<?php if ( $post_type == 'tf_hotel' ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php _e("Check in", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo date('M'); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
			</div>
			<div class="tf-booking-form-checkout">
				<span class="tf-booking-form-title"><?php _e("Check out", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo date('M'); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
				<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required>
			</div>
			<?php } ?>

			<?php if ( $post_type == 'tf_tours' ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php _e("Date", "tourfic"); ?></span>
				<div class="tf-tour-searching-date-block">
					<div class="tf-booking-date-wrap tf-tour-start-date">
						<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo date('M'); ?></span>
						</span>
					</div>
					<div class="tf-duration">
						<span>-</span>
					</div>
					<div class="tf-booking-date-wrap tf-tour-end-date">
						<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo date('M'); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
					</div>
					<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required>
				</div>
			</div>
			<?php } ?>
			<div class="tf-booking-form-guest-and-room">
				<?php if ( $post_type !== 'tf_tours' ) { ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php _e("Guests & rooms", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap tf-archive-guest-info">
						<span class="tf-guest"><?php _e("01", "tourfic"); ?></span> <?php _e("guest", "tourfic"); ?> <span class="tf-room"><?php _e("01", "tourfic"); ?></span> <?php _e("rooms", "tourfic"); ?>
					</div>
					<div class="tf-arrow-icons">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</div>
				</div>
				<?php }else{ ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php _e("Guests", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap">
						<span class="tf-guest tf-booking-date">
							<?php _e("01", "tourfic"); ?>
						</span>
						<span class="tf-booking-month">
							<span><?php _e("guest", "tourfic"); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
					</div>
				</div>
				<?php } ?>

				<div class="tf_acrselection-wrap">
					<div class="tf_acrselection-inner">
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Adults", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="adults" id="adults" min="1" value="1">
								<div class="acr-inc">+</div>
							</div>
						</div>
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Children", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="childrens" id="children" min="0" value="0">
								<div class="acr-inc">+</div>
							</div>
						</div>
						<?php if ( $post_type !== 'tf_tours' ) { ?>
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Rooms", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="room" id="room" min="1" value="1">
								<div class="acr-inc">+</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="tf-booking-form-submit">
			<input type="hidden" name="type" value="<?php echo $post_type; ?>" class="tf-post-type"/>
            <button class="tf-btn-normal btn-primary tf-submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
		</div>

		<?php if ( $post_type == 'tf_tours' ) { ?>
		<script>
			(function ($) {
				$(document).ready(function () {
					// flatpickr locale first day of Week
					<?php tf_flatpickr_locale("root"); ?>

					$(".tf-template-3 .tf-booking-date-wrap").click(function(){

						$("#check-in-out-date").click();
					});
					$("#check-in-out-date").flatpickr({
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
						<?php
						if(!empty($check_in_out)){ ?>
						defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
						<?php } ?>
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const monthNames = [
								"Jan", "Feb", "Mar", "Apr", "May", "Jun",
								"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
							];
							if(selectedDates[0]){
								const startDate = selectedDates[0];
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-date").html(startDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
							}
							if(selectedDates[1]){
								const endDate = selectedDates[1];
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-date").html(endDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
							}
						}
					}

				});
			})(jQuery);
		</script>
		<?php } ?>

		<?php if ( $post_type == 'tf_hotel' ) { ?>
		<script>
			(function ($) {
				$(document).ready(function () {
					// flatpickr locale first day of Week
					<?php tf_flatpickr_locale("root"); ?>

					$(".tf-template-3 .tf-booking-date-wrap").click(function(){

						$("#check-in-out-date").click();
					});
					$("#check-in-out-date").flatpickr({
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
						<?php
						if(!empty($check_in_out)){ ?>
						defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
						<?php } ?>
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const monthNames = [
								"Jan", "Feb", "Mar", "Apr", "May", "Jun",
								"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
							];
							if(selectedDates[0]){
								const startDate = selectedDates[0];
								$(".tf-template-3 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
							}
							if(selectedDates[1]){
								const endDate = selectedDates[1];
								$(".tf-template-3 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
								$(".tf-template-3 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
							}
						}
					}

				});
			})(jQuery);
		</script>
		<?php } ?>

	<?php } else { ?>
        <form class="tf_archive_search_result tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
              action="<?php echo tf_booking_search_action(); ?>">

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" required="" id="<?php echo $place; ?>" class="" placeholder="<?php echo $place_text; ?>" value="<?php echo ! empty( $taxonomy_name ) ? $taxonomy_name : ''; ?>">
                        <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? $taxonomy_slug : ''; ?>"/>
                    </div>
                </label>
            </div>

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="fas fa-user-friends"></i>
                        <select name="adults" id="adults" class="">
							<?php
							echo '<option value="1">1 ' . __( "Adult", "tourfic" ) . '</option>';
							foreach ( range( 2, 8 ) as $value ) {
								echo '<option value="' . $value . '">' . $value . ' ' . __( "Adults", "tourfic" ) . '</option>';
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
							echo '<option value="0">0 ' . __( "Children", "tourfic" ) . '</option>';
							foreach ( range( 1, 8 ) as $value ) {
								echo '<option value="' . $value . '">' . $value . ' ' . __( "Children", "tourfic" ) . '</option>';
							}
							?>
                        </select>
                    </div>
                </label>
            </div>

			<?php if ( $post_type == 'tf_apartment' ): ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-child"></i>
                            <select name="infant" id="infant" class="">
                                <option value="0">0 <?php _e( 'Infant', 'tourfic' ); ?></option>
								<?php foreach ( range( 1, 8 ) as $value ) {
									echo '<option value="' . $value . '">' . $value . ' ' . __( "Infant", "tourfic" ) . '</option>';
								} ?>

                            </select>
                        </div>
                    </label>
                </div>
			<?php endif; ?>

			<?php if ( $post_type == 'tf_hotel' ) { ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-couch"></i>
                            <select name="room" id="room" class="">
								<?php
								echo '<option value="1">1 ' . __( "Room", "tourfic" ) . '</option>';
								foreach ( range( 2, 8 ) as $value ) {
									echo '<option value="' . $value . '">' . $value . ' ' . __( "Rooms", "tourfic" ) . '</option>';
								}
								?>
                            </select>
                        </div>
                    </label>
                </div>
			<?php } ?>
            <div class="tf_booking-dates">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                   placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" required value="">
                        </div>
                    </label>
                </div>
            </div>

            <div class="tf_form-row">
                <input type="hidden" name="type" value="<?php echo $post_type; ?>" class="tf-post-type"/>
                <button class="tf_button tf-submit btn-styled"
                        type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
            </div>

        </form>

		<script>
			(function ($) {
				$(document).ready(function () {
					<?php tf_flatpickr_locale('root'); ?>

					$(document).on("focus",".tf-hotel-side-booking #check-in-out-date", function(e) {
						let calander = flatpickr( this, {
						enableTime: false,
						minDate: "today",
						mode: "range",
						dateFormat: "Y/m/d",
						altInput: true,
						altFormat: '<?php echo $date_format_for_users; ?>',

						// flatpickr locale
						<?php tf_flatpickr_locale(); ?>

						onChange: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
						});
					});
				});
			})(jQuery);
		</script>

		<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
        <div id="tf__booking_sidebar">
			<?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
        </div>
		<?php } ?>
	<?php } ?>
<?php
}

/**
 * Search Result Sidebar check availability
 *
 * Hotel Filter by Feature
 *
 * Ajax function
 */
add_action( 'wp_ajax_nopriv_tf_trigger_filter', 'tf_search_result_ajax_sidebar' );
add_action( 'wp_ajax_tf_trigger_filter', 'tf_search_result_ajax_sidebar' );
function tf_search_result_ajax_sidebar() {

	/**
	 * Get form data
	 */
	$adults       = ! empty( $_POST['adults'] ) ? sanitize_text_field( $_POST['adults'] ) : '';
	$child        = ! empty( $_POST['children'] ) ? sanitize_text_field( $_POST['children'] ) : '';
	$infant       = ! empty( $_POST['infant'] ) ? sanitize_text_field( $_POST['infant'] ) : '';
	$room         = ! empty( $_POST['room'] ) ? sanitize_text_field( $_POST['room'] ) : '';
	$check_in_out = ! empty( $_POST['checked'] ) ? sanitize_text_field( $_POST['checked'] ) : '';

	$relation        = tfopt( 'search_relation', 'AND' );
	$filter_relation = tfopt( 'filter_relation', 'OR' );

	$search                = ( $_POST['dest'] ) ? sanitize_text_field( $_POST['dest'] ) : null;
	$filters               = ( $_POST['filters'] ) ? explode( ',', sanitize_text_field( $_POST['filters'] ) ) : null;
	$features              = ( $_POST['features'] ) ? explode( ',', sanitize_text_field( $_POST['features'] ) ) : null;
	$tf_hotel_types        = ( $_POST['tf_hotel_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_hotel_types'] ) ) : null;
	$tour_features         = ( $_POST['tour_features'] ) ? explode( ',', sanitize_text_field( $_POST['tour_features'] ) ) : null;
	$attractions           = ( $_POST['attractions'] ) ? explode( ',', sanitize_text_field( $_POST['attractions'] ) ) : null;
	$activities            = ( $_POST['activities'] ) ? explode( ',', sanitize_text_field( $_POST['activities'] ) ) : null;
	$tf_tour_types         = ( $_POST['tf_tour_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_tour_types'] ) ) : null;
	$tf_apartment_features = ( $_POST['tf_apartment_features'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_features'] ) ) : null;
	$tf_apartment_types    = ( $_POST['tf_apartment_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_types'] ) ) : null;
	$posttype              = $_POST['type'] ? sanitize_text_field( $_POST['type'] ) : 'tf_hotel';
	# Separate taxonomy input for filter query
	$place_taxonomy  = $posttype == 'tf_tours' ? 'tour_destination' : ( $posttype == 'tf_apartment' ? 'apartment_location' : 'hotel_location' );
	$filter_taxonomy = $posttype == 'tf_tours' ? 'null' : 'hotel_feature';
	# Take dates for filter query
	$checkin    = isset( $_POST['checkin'] ) ? trim( $_POST['checkin'] ) : array();
	$startprice = ! empty( $_POST['startprice'] ) ? $_POST['startprice'] : '';
	$endprice   = ! empty( $_POST['endprice'] ) ? $_POST['endprice'] : '';

	// Author Id if any
	$tf_author_ids = ! empty( $_POST['tf_author'] ) ? $_POST['tf_author'] : '';

	if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
		if ( $posttype == "tf_tours" ) {
			$data = array( $adults, $child, $check_in_out, $startprice, $endprice );
		} elseif ( $posttype == "tf_hotel" ) {
			$data = array( $adults, $child, $room, $check_in_out, $startprice, $endprice );
		} else {
			$data = array( $adults, $child, $infant, $check_in_out, $startprice, $endprice );
		}
	} else {
		if ( $posttype == "tf_tours" ) {
			$data = array( $adults, $child, $check_in_out );
		} elseif ( $posttype == "tf_hotel" ) {
			$data = array( $adults, $child, $room, $check_in_out );
		} else {
			$data = array( $adults, $child, $infant, $check_in_out );
		}
	}

	if ( ! empty( $check_in_out ) ) {
		list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
	}

	if ( ! empty( $check_in_out ) ) {
		$period = new DatePeriod(
			new DateTime( $tf_form_start ),
			new DateInterval( 'P1D' ),
			new DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
		);
	} else {
		$period = '';
	}
	if ( $check_in_out ) {
		$form_check_in      = substr( $check_in_out, 0, 10 );
		$form_check_in_stt  = strtotime( $form_check_in );
		$form_check_out     = substr( $check_in_out, 13, 10 );
		$form_check_out_stt = strtotime( $form_check_out );
	}

	$post_per_page = tfopt( 'posts_per_page' ) ? tfopt( 'posts_per_page' ) : 10;
	// $paged = !empty($_POST['page']) ? absint( $_POST['page'] ) : 1;
	// Properties args
	if ( $posttype == "tf_tours" ) {
		$tf_expired_tour_showing = ! empty( tfopt( 't-show-expire-tour' ) ) ? tfopt( 't-show-expire-tour' ) : '';
		if ( ! empty( $tf_expired_tour_showing ) ) {
			$tf_tour_posts_status = array( 'publish', 'expired' );
		} else {
			$tf_tour_posts_status = array( 'publish' );
		}

		$args = array(
			'post_type'      => $posttype,
			'post_status'    => $tf_tour_posts_status,
			'posts_per_page' => - 1,
			'author'         => $tf_author_ids,
		);
	} else {
		$args = array(
			'post_type'      => $posttype,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'author'         => $tf_author_ids,
		);
	}

	if ( $search ) {

		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => $place_taxonomy,
				'field'    => 'slug',
				'terms'    => sanitize_title( $search, '' ),
			),
		);
	}

	if ( $filters ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => $filter_taxonomy,
				'terms'    => $filters,
			);
		} else {
			$args['tax_query']['tf_filters']['relation'] = 'AND';

			foreach ( $filters as $key => $term_id ) {
				$args['tax_query']['tf_filters'][] = array(
					'taxonomy' => $filter_taxonomy,
					'terms'    => array( $term_id ),
				);
			}
		}
	}

	//Query for the features filter of hotel
	if ( $features ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'tf_feature',
				'terms'    => $features,
			);
		} else {
			$args['tax_query']['tf_feature']['relation'] = 'AND';

			foreach ( $filters as $key => $term_id ) {
				$args['tax_query']['tf_feature'][] = array(
					'taxonomy' => 'tf_feature',
					'terms'    => array( $term_id ),
				);
			}
		}
	}

	//Query for the types filter of hotel
	if ( $tf_hotel_types ) {

		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'hotel_type',
				'terms'    => $tf_hotel_types,
			);
		} else {
			$args['tax_query']['hotel_type']['relation'] = 'AND';

			foreach ( $tf_hotel_types as $key => $term_id ) {
				$args['tax_query']['hotel_type'][] = array(
					'taxonomy' => 'hotel_type',
					'terms'    => array( $term_id ),
				);
			}
		}
	}

	//Query for the features filter of Tour
	if ( $tour_features ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'tour_features',
				'terms'    => $tour_features,
			);
		} else {
			$args['tax_query']['tour_features']['relation'] = 'AND';

			foreach ( $tour_features as $key => $term_id ) {
				$args['tax_query']['tour_features'][] = array(
					'taxonomy' => 'tour_features',
					'terms'    => array( $term_id ),
				);
			}

		}

	}

	//Query for the attractions filter of tours
	if ( $attractions ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'tour_attraction',
				'terms'    => $attractions,
			);
		} else {
			$args['tax_query']['tour_attraction']['relation'] = 'AND';

			foreach ( $attractions as $key => $term_id ) {
				$args['tax_query']['tour_attraction'][] = array(
					'taxonomy' => 'tour_attraction',
					'terms'    => array( $term_id ),
				);
			}

		}

	}

	//Query for the activities filter of tours
	if ( $activities ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'tour_activities',
				'terms'    => $activities,
			);
		} else {
			$args['tax_query']['tour_activities']['relation'] = 'AND';

			foreach ( $activities as $key => $term_id ) {
				$args['tax_query']['tour_activities'][] = array(
					'taxonomy' => 'tour_activities',
					'terms'    => array( $term_id ),
				);
			}

		}

	}

	//Query for the types filter of tours
	if ( $tf_tour_types ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'tour_type',
				'terms'    => $tf_tour_types,
			);
		} else {
			$args['tax_query']['tour_type']['relation'] = 'AND';

			foreach ( $tf_tour_types as $key => $term_id ) {
				$args['tax_query']['tour_type'][] = array(
					'taxonomy' => 'tour_type',
					'terms'    => array( $term_id ),
				);
			}
		}
	}

	//Query for the features filter of apartments
	if ( $tf_apartment_features ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'apartment_feature',
				'terms'    => $tf_apartment_features,
			);
		} else {
			$args['tax_query']['apartment_feature']['relation'] = 'AND';

			foreach ( $tf_apartment_features as $key => $term_id ) {
				$args['tax_query']['apartment_feature'][] = array(
					'taxonomy' => 'apartment_feature',
					'terms'    => array( $term_id ),
				);
			}
		}
	}

	//Query for the types filter of apartments
	if ( $tf_apartment_types ) {
		$args['tax_query']['relation'] = $relation;

		if ( $filter_relation == "OR" ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'apartment_type',
				'terms'    => $tf_apartment_types,
			);
		} else {
			$args['tax_query']['apartment_type']['relation'] = 'AND';

			foreach ( $tf_apartment_types as $key => $term_id ) {
				$args['tax_query']['apartment_type'][] = array(
					'taxonomy' => 'apartment_type',
					'terms'    => array( $term_id ),
				);
			}
		}
	}

	$loop = new WP_Query( $args );

	//get total posts count
	$total_posts = $loop->found_posts;
	if ( $loop->have_posts() ) {
		$not_found = [];
		while ( $loop->have_posts() ) {

			$loop->the_post();

			if ( $posttype == 'tf_hotel' ) {

				if ( empty( $check_in_out ) ) {
					tf_filter_hotel_without_date( $period, $not_found, $data );
				} else {
					tf_filter_hotel_by_date( $period, $not_found, $data );
				}

			} elseif ( $posttype == 'tf_tours' ) {
				if ( empty( $check_in_out ) ) {
					/**
					 * Check if minimum and maximum people limit matches with the search query
					 */
					$total_person = intval( $adults ) + intval( $child );
					$meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

					//skip the tour if the search form total people  exceeds the maximum number of people in tour
					if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
						$total_posts --;
						continue;
					}

					//skip the tour if the search form total people less than the maximum number of people in tour
					if ( ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
						$total_posts --;
						continue;
					}
					tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
				} else {
					tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
				}
			} else {
				if ( empty( $check_in_out ) ) {
					tf_filter_apartment_without_date( $period, $not_found, $data );
				} else {
					tf_filter_apartment_by_date( $period, $not_found, $data );
				}
			}
		}
		$tf_total_results = 0;
		$tf_total_filters = [];
		foreach ( $not_found as $not ) {
			if ( $not['found'] != 1 ) {
				$tf_total_results   = $tf_total_results + 1;
				$tf_total_filters[] = $not['post_id'];
			}
		}

		if ( empty( $tf_total_filters ) ) {
			echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
		}
		$post_per_page = tfopt( 'posts_per_page' ) ? tfopt( 'posts_per_page' ) : 10;

		$total_filtered_results = count( $tf_total_filters );
		$current_page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$offset                 = ( $current_page - 1 ) * $post_per_page;
		$displayed_results      = array_slice( $tf_total_filters, $offset, $post_per_page );
		if ( ! empty( $displayed_results ) ) {
			$filter_args = array(
				'post_type'      => $posttype,
				'posts_per_page' => $post_per_page,
				'post__in'       => $displayed_results,
			);

			$result_query = new WP_Query( $filter_args );
			if ( $result_query->have_posts() ) {
				while ( $result_query->have_posts() ) {
					$result_query->the_post();

					if ( $posttype == 'tf_hotel' ) {
						if ( ! empty( $data ) ) {
							if ( isset( $data[4] ) && isset( $data[5] ) ) {
								[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
								tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
							} else {
								[ $adults, $child, $room, $check_in_out ] = $data;
								tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
							}
						} else {
							tf_hotel_archive_single_item();
						}
					} elseif ( $posttype == 'tf_tours' ) {
						if ( ! empty( $data ) ) {
							if ( isset( $data[3] ) && isset( $data[4] ) ) {
								[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
								tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
							} else {
								[ $adults, $child, $check_in_out ] = $data;
								tf_tour_archive_single_item( $adults, $child, $check_in_out );
							}
						} else {
							tf_tour_archive_single_item();
						}
					} else {
						if ( ! empty( $data ) ) {
							if ( isset( $data[4] ) && isset( $data[5] ) ) {
								tf_apartment_archive_single_item( $data );
							} else {
								tf_apartment_archive_single_item( $data );
							}
						} else {
							tf_apartment_archive_single_item();
						}
					}

				}
			}
			$total_pages = ceil( $total_filtered_results / $post_per_page );
			echo "<div class='tf_posts_navigation tf_posts_ajax_navigation'>";
			echo paginate_links( array(
				'total'   => $total_pages,
				'current' => $current_page
			) );
			echo "</div>";
		}
	} else {

		echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';

	}

	echo "<span hidden=hidden class='tf-posts-count'>";
	echo ! empty( $tf_total_results ) ? $tf_total_results : 0;
	echo "</span>";
	wp_reset_postdata();

	die();
}


/**
 * Migrate data from v2.0.4 to v2.1.0
 *
 * run once
 */
function tf_migrate_data() {

	if ( get_option( 'tf_migrate_data_204_210' ) < 1 ) {

		global $wpdb;
		// $wpdb->update( $wpdb->posts, [ 'post_type' => 'tf_hotel' ], [ 'post_type' => 'tourfic' ] );
		// $wpdb->update( $wpdb->term_taxonomy, [ 'taxonomy' => 'hotel_location' ], [ 'taxonomy' => 'destination' ] );
		// $wpdb->update( $wpdb->term_taxonomy, [ 'taxonomy' => 'hotel_feature' ], [ 'taxonomy' => 'tf_filters' ] );


		/** Hotels Migrations */
		$hotels = get_posts( [ 'post_type' => 'tf_hotel', 'numberposts' => - 1, ] );


		foreach ( $hotels as $hotel ) {
			$old_meta = get_post_meta( $hotel->ID );
			if ( ! empty( $old_meta['tf_hotel'] ) ) {
				continue;
			}
			$new_meta = [];
			if ( ! empty( $old_meta['formatted_location'] ) ) {
				$new_meta['address'] = join( ',', $old_meta['formatted_location'] );
			}
			if ( ! empty( $old_meta['tf_gallery_ids'] ) ) {
				$new_meta['gallery'] = join( ',', $old_meta['tf_gallery_ids'] );
			}
			if ( ! empty( $old_meta['additional_information'] ) ) {
				$new_meta['highlights'] = $old_meta['additional_information'];
			}
			if ( ! empty( $old_meta['terms_and_conditions'] ) ) {
				$new_meta['tc'] = join( ' ', $old_meta['terms_and_conditions'] );
			}

			if ( ! empty( $old_meta['tf_room'] ) ) {
				$rooms = unserialize( $old_meta['tf_room'][0] );
				foreach ( $rooms as $room ) {
					$new_meta['room'][] = [
						"enable"      => "1",
						"title"       => $room['name'],
						"adult"       => $room['pax'],
						"description" => $room['short_desc'],
						"pricing-by"  => "1",
						"price"       => $room['sale_price'] ?? $room['price'],
					];
				}
			}

			if ( ! empty( $old_meta['tf_faqs'] ) ) {
				$faqs = unserialize( $old_meta['tf_faqs'][0] );
				foreach ( $faqs as $faq ) {
					$new_meta['faq'][] = [
						'title'       => $faq['name'],
						'description' => $faq['desc'],
					];
				}
			}

			update_post_meta(
				$hotel->ID,
				'tf_hotel',
				$new_meta
			);

		}

		/** Hotels Location Taxonomy Migration */
		$hotel_locations = get_terms( [
			'taxonomy'   => 'hotel_location',
			'hide_empty' => false,
		] );

		foreach ( $hotel_locations as $hotel_location ) {

			$old_locations_meta = get_term_meta(
				$hotel_location->term_id,
				'category-image-id',
				true
			);
			$new_meta           = [
				"image" => [
					"url"         => wp_get_attachment_url( $old_locations_meta ),
					"id"          => $old_locations_meta,
					"width"       => "1920",
					"height"      => "1080",
					"thumbnail"   => wp_get_attachment_thumb_url( $old_locations_meta ),
					"alt"         => "",
					"title"       => "",
					"description" => ""
				]
			];
			// If the meta field for the term does not exist, it will be added.
			update_term_meta(
				$hotel_location->term_id,
				"hotel_location",
				$new_meta
			);
		}

		/** Tour Destinations Image Fix */
		$tour_destinations = get_terms( [
			'taxonomy'   => 'tour_destination',
			'hide_empty' => false,
		] );

		foreach ( $tour_destinations as $tour_destination ) {
			$old_term_metadata = get_term_meta( $tour_destination->term_id, 'tour_destination_meta', true )['tour_destination_meta'] ?? null;
			if ( ! empty( $old_term_metadata ) ) {
				$image_id = attachment_url_to_postid( $old_term_metadata );
				$new_meta = [
					"image" => [
						"url"         => wp_get_attachment_url( $image_id ),
						"id"          => $image_id,
						"width"       => "1920",
						"height"      => "1080",
						"thumbnail"   => wp_get_attachment_thumb_url( $image_id ),
						"alt"         => "",
						"title"       => "",
						"description" => ""
					]
				];
				// If the meta field for the term does not exist, it will be added.
				update_term_meta(
					$tour_destination->term_id,
					"tour_destination",
					$new_meta
				);
			}
		}

		/** Tour Type Fix */
		$tours = get_posts( [ 'post_type' => 'tf_tours', 'numberposts' => - 1, ] );
		foreach ( $tours as $tour ) {
			$old_meta             = get_post_meta( $tour->ID );
			$tour_options         = unserialize( $old_meta['tf_tours_option'][0] );
			$tour_options['type'] = 'continuous';
			update_post_meta(
				$tour->ID,
				'tf_tours_option',
				$tour_options
			);
		}


		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_migrate_data_204_210', 1 );

	}

}

add_action( 'init', 'tf_migrate_data' );


/*
 * TF Options Migrator
 * @author: Sydur Rahman
 * */
function tf_migrate_option_data() {

	if ( empty( get_option( 'tf_migrate_data_204_210_2022' ) ) ) {

		/** Tours Migrations */
		$tours = get_posts( [ 'post_type' => 'tf_tours', 'numberposts' => - 1, ] );
		foreach ( $tours as $tour ) {
			$old_meta = get_post_meta( $tour->ID );
			if ( ! empty( $old_meta['tf_tours_option'] ) ) {
				$tour_options = unserialize( $old_meta['tf_tours_option'][0] );

				if ( isset( $tour_options['hightlights_thumbnail'] ) && is_array( $tour_options['hightlights_thumbnail'] ) ) {
					$tour_options['hightlights_thumbnail'] = $tour_options['hightlights_thumbnail']['url'];
				}
				if ( isset( $tour_options['include-exclude-bg'] ) && is_array( $tour_options['include-exclude-bg'] ) ) {
					$tour_options['include-exclude-bg'] = $tour_options['include-exclude-bg']['url'];
				}
				update_post_meta(
					$tour->ID,
					'tf_tours_opt',
					$tour_options
				);
			}

		}
		/** Tour Destinations Image Fix */
		$tour_destinations = get_terms( [
			'taxonomy'   => 'tour_destination',
			'hide_empty' => false,
		] );


		foreach ( $tour_destinations as $tour_destination ) {
			$old_term_metadata = get_term_meta( $tour_destination->term_id, 'tour_destination', true );

			if ( ! empty( $old_term_metadata ) ) {
				if ( isset( $old_term_metadata['image'] ) && is_array( $old_term_metadata['image'] ) ) {
					$old_term_metadata['image'] = $old_term_metadata['image']['url'];
				}

				// If the meta field for the term does not exist, it will be added.
				update_term_meta(
					$tour_destination->term_id,
					"tf_tour_destination",
					$old_term_metadata
				);
			}
		}

		/** Hotel Migrations */
		$hotels = get_posts( [ 'post_type' => 'tf_hotel', 'numberposts' => - 1, ] );

		foreach ( $hotels as $hotel ) {
			$old_meta = get_post_meta( $hotel->ID );
			if ( ! empty( $old_meta['tf_hotel'] ) ) {
				$hotel_options = unserialize( $old_meta['tf_hotel'][0] );


				// $tour_options = serialize( $tour_options );
				update_post_meta(
					$hotel->ID,
					'tf_hotels_opt',
					$hotel_options
				);
			}

		}

		/** Hotel Location Image Fix */
		$hotel_location = get_terms( [
			'taxonomy'   => 'hotel_location',
			'hide_empty' => false,
		] );


		foreach ( $hotel_location as $_hotel_location ) {
			$old_term_metadata = get_term_meta( $_hotel_location->term_id, 'hotel_location', true );
			if ( ! empty( $old_term_metadata ) ) {
				if ( isset( $old_term_metadata['image'] ) && is_array( $old_term_metadata['image'] ) ) {
					$old_term_metadata['image'] = $old_term_metadata['image']['url'];
				}

				// If the meta field for the term does not exist, it will be added.
				update_term_meta(
					$_hotel_location->term_id,
					"tf_hotel_location",
					$old_term_metadata
				);
			}
		}

		/** Hotel Feature Image Fix */
		$hotel_feature = get_terms( [
			'taxonomy'   => 'hotel_feature',
			'hide_empty' => false,
		] );


		foreach ( $hotel_feature as $_hotel_feature ) {
			$old_term_metadata = get_term_meta( $_hotel_feature->term_id, 'hotel_feature', true );
			if ( ! empty( $old_term_metadata ) ) {
				if ( isset( $old_term_metadata['icon-c'] ) && is_array( $old_term_metadata['icon-c'] ) ) {
					$old_term_metadata['icon-c'] = $old_term_metadata['icon-c']['url'];
				}
				if ( isset( $old_term_metadata['dimention'] ) && is_array( $old_term_metadata['dimention'] ) ) {
					$old_term_metadata['dimention'] = $old_term_metadata['dimention']['width'];
				}

				// If the meta field for the term does not exist, it will be added.
				update_term_meta(
					$_hotel_feature->term_id,
					"tf_hotel_feature",
					$old_term_metadata
				);
			}
		}


		/** settings option migration */
		// company_logo
		$old_setting_option = get_option( 'tourfic_opt' );
		if ( isset( $old_setting_option['itinerary-builder-setings']['company_logo'] ) && is_array( $old_setting_option['itinerary-builder-setings']['company_logo'] ) ) {
			$old_setting_option['itinerary-builder-setings']['company_logo'] = $old_setting_option['itinerary-builder-setings']['company_logo']['url'];
		}
		if ( isset( $old_setting_option['itinerary-builder-setings']['expert_logo'] ) && is_array( $old_setting_option['itinerary-builder-setings']['expert_logo'] ) ) {
			$old_setting_option['itinerary-builder-setings']['expert_logo'] = $old_setting_option['itinerary-builder-setings']['expert_logo']['url'];
		}
		update_option( 'tf_settings', $old_setting_option );

		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_migrate_data_204_210_2022', 2 );
	}


	if ( empty( get_option( 'tf_license_data_migrate_data_204_210_2022' ) ) ) {

		/** License Migrate */

		$old_setting_option = get_option( 'tourfic_opt' );
		if ( ! empty( $old_setting_option['license-key'] ) && ! empty( $old_setting_option['license-email'] ) ) {
			$tf_settings['license-key']   = $old_setting_option['license-key'];
			$tf_settings['license-email'] = $old_setting_option['license-email'];
			update_option( 'tf_license_settings', $tf_settings ) || add_option( 'tf_license_settings', $tf_settings );
		} else {
			$tf_setting_option            = get_option( 'tf_settings' );
			$tf_settings['license-key']   = ! empty( $tf_setting_option['license-key'] ) ? $tf_setting_option['license-key'] : '';
			$tf_settings['license-email'] = ! empty( $tf_setting_option['license-email'] ) ? $tf_setting_option['license-email'] : '';
			update_option( 'tf_license_settings', $tf_settings ) || add_option( 'tf_license_settings', $tf_settings );
		}

		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_license_data_migrate_data_204_210_2022', 2 );
	}


}

add_action( 'init', 'tf_migrate_option_data' );


/*
 * Search form tab type check
 * @author: Foysal
 * return: boolean
 */
function tf_is_search_form_tab_type( $type, $type_arr ) {
	if ( in_array( $type, $type_arr ) || in_array( 'all', $type_arr ) ) {
		return true;
	}

	return false;
}

/*
 * Search form tab type check
 * @author: Foysal
 * return: boolean
 */
function tf_is_search_form_single_tab( $type_arr ) {
	if ( count( $type_arr ) === 1 && $type_arr[0] !== 'all' ) {
		return true;
	}

	return false;
}

function tf_var_dump( $var ) {
	echo '<pre>';
	var_dump( $var );
	echo '</pre>';
}

/*
 * Data Retrive
 * @author: Jahid
 * return: array
 */
if ( ! function_exists( 'tf_data_types' ) ) {
	function tf_data_types( $var ) {
		if ( ! empty( $var ) && gettype( $var ) == "string" ) {
			$tf_serialize_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $var );

			return unserialize( $tf_serialize_date );
		} else {
			return $var;
		}
	}
}


# ================================== #
# Custom Option Fields               #
# ================================== #

/**
 * Custom permalink settings
 *
 * @since 2.2.0
 */
/**
 * Add settings field
 */
function tf_add_custom_permalink_fields() {

	add_settings_section( 'tf_permalink', __( 'Tourfic Permalinks', 'tourfic' ), 'tf_permalink_section_callback', 'permalink' );
	// Tour
	add_settings_field( 'tour_slug', __( 'Tour slug', 'tourfic' ), 'tf_tour_slug_field_callback', 'permalink', 'tf_permalink', array( 'label_for' => 'tour_slug' ) );
	// Hotel
	add_settings_field( 'hotel_slug', __( 'Hotel slug', 'tourfic' ), 'tf_hotel_slug_field_callback', 'permalink', 'tf_permalink', array( 'label_for' => 'hotel_slug' ) );
	// Apartment
	add_settings_field( 'apartment_slug', __( 'Apartment slug', 'tourfic' ), 'tf_apartment_slug_field_callback', 'permalink', 'tf_permalink', array( 'label_for' => 'apartment_slug' ) );
}

add_action( 'admin_init', 'tf_add_custom_permalink_fields' );

// Tourfic Permalinks settings section callback function
function tf_permalink_section_callback() {
	_e( 'If you like, you may enter custom structures for your archive & single URLs here.', 'tourfic' );
}

// Tour slug callback
function tf_tour_slug_field_callback() { ?>
    <input name="tour_slug" id="tour_slug" type="text" value="<?php echo get_option( 'tour_slug' ) ? get_option( 'tour_slug' ) : ''; ?>" class="regular-text code">
    <p class="description"><?php printf( __( 'Leave blank for default value: %1stours%2s', 'tourfic' ), '<code>', '</code>' ); ?></p>
<?php }

// Hotel slug callback
function tf_hotel_slug_field_callback() { ?>
    <input name="hotel_slug" id="hotel_slug" type="text" value="<?php echo get_option( 'hotel_slug' ) ? get_option( 'hotel_slug' ) : ''; ?>" class="regular-text code">
    <p class="description"><?php printf( __( 'Leave blank for default value: %1shotels%2s', 'tourfic' ), '<code>', '</code>' ); ?></p>
<?php }

// Apartment slug callback
function tf_apartment_slug_field_callback() { ?>
    <input name="apartment_slug" id="apartment_slug" type="text" value="<?php echo get_option( 'apartment_slug' ) ? get_option( 'apartment_slug' ) : ''; ?>" class="regular-text code">
    <p class="description"><?php printf( __( 'Leave blank for default value: %1sapartments%2s', 'tourfic' ), '<code>', '</code>' ); ?></p>
<?php }

/**
 * Register settings field
 */
function tf_save_custom_fields() {

	// Tour
	if ( isset( $_POST['tour_slug'] ) ) {
		update_option( 'tour_slug', $_POST['tour_slug'] );
	}
	// Hotel
	if ( isset( $_POST['hotel_slug'] ) ) {
		update_option( 'hotel_slug', $_POST['hotel_slug'] );
	}
	// Apartment
	if ( isset( $_POST['apartment_slug'] ) ) {
		update_option( 'apartment_slug', $_POST['apartment_slug'] );
	}

}

add_action( 'admin_init', 'tf_save_custom_fields' );


/**
 * Monthwise Chart Ajax function
 *
 * @author Jahid
 */
add_action( 'wp_ajax_nopriv_tf_month_reports', 'tf_month_chart_filter_callback' );
add_action( 'wp_ajax_tf_month_reports', 'tf_month_chart_filter_callback' );

function tf_month_chart_filter_callback() {
	$search_month = sanitize_key( $_POST['month'] );
	$search_year  = sanitize_key( $_POST['year'] );
	$month_dates  = cal_days_in_month( CAL_GREGORIAN, $search_month, $search_year );

	//Order Data Retrive
	$tf_old_order_limit = new WC_Order_Query( array(
		'limit'   => - 1,
		'orderby' => 'date',
		'order'   => 'ASC',
		'return'  => 'ids',
	) );
	$order              = $tf_old_order_limit->get_orders();
	$months_day_number  = [];
	for ( $i = 1; $i <= $month_dates; $i ++ ) {
		$months_day_number [] = $i;

		// Booking Month
		${"tf_co$i"} = 0;
		// Booking Cancel Month
		${"tf_cr$i"} = 0;
	}

	foreach ( $order as $item_id => $item ) {
		$itemmeta         = wc_get_order( $item );
		$tf_ordering_date = $itemmeta->get_date_created();
		for ( $i = 1; $i <= $month_dates; $i ++ ) {
			if ( $tf_ordering_date->date( 'n-j-y' ) == $search_month . '-' . $i . '-' . $search_year ) {
				if ( "completed" == $itemmeta->get_status() ) {
					${"tf_co$i"} += 1;
				}
				if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
					${"tf_cr$i"} += 1;
				}
			}
		}
	}
	$tf_complete_orders = [];
	$tf_cancel_orders   = [];
	for ( $i = 1; $i <= $month_dates; $i ++ ) {
		$tf_complete_orders [] = ${"tf_co$i"};
		$tf_cancel_orders []   = ${"tf_cr$i"};
	}

	$response['months_day_number']  = $months_day_number;
	$response['tf_complete_orders'] = $tf_complete_orders;
	$response['tf_cancel_orders']   = $tf_cancel_orders;
	$response['tf_search_month']    = date( "F", strtotime( '2000-' . $search_month . '-01' ) );
	echo wp_json_encode( $response );

	die();
}

/**
 * Generate custom taxonomies select dropdown
 * @author Abu Hena
 * @since 2.9.4
 */
if ( ! function_exists( 'tf_terms_dropdown' ) ) {
	function tf_terms_dropdown( $term, $attribute, $id, $class, $multiple = false ) {

		//get the terms
		$terms = get_terms( array(
			'taxonomy'   => $term,
			'hide_empty' => false,
		) );

		//define if select field would be multiple or not
		if ( $multiple == true ) {
			$multiple = 'multiple';
		} else {
			$multiple = "";
		}
		$select = '';
		//output the select field
		if ( ! empty( $terms ) && is_array( $terms ) ) {
			$select .= '<select data-placeholder=" Select from Dropdown" id="' . $id . '" data-term="' . $attribute . '" name="' . $term . '" class="tf-shortcode-select2 ' . $class . '" ' . $multiple . '>';
			$select .= '<option value="\'all\'">' . __( 'All', 'tourfic' ) . '</option>';
			foreach ( $terms as $term ) {
				$select .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
			}
			$select .= "</select>";
		} else {
			$select .= __( "Invalid taxonomy!!", 'tourfic' );
		}
		echo $select;
	}
}

/**
 * Remove icon add to order item
 * @since 2.9.6
 * @author Foysal
 */
add_filter( 'woocommerce_cart_item_subtotal', 'tf_remove_icon_add_to_order_item', 10, 3 );
function tf_remove_icon_add_to_order_item( $subtotal, $cart_item, $cart_item_key ) {
	if ( ! is_checkout() ) {
		return $subtotal;
	}
	$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	?>
    <div class="tf-product-total">
		<?php echo $subtotal; ?>
		<?php
		echo sprintf(
			'<a href="#" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
//		        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
			esc_attr__( 'Remove this item', 'woocommerce' ),
			esc_attr( $product_id ),
			esc_attr( $cart_item_key ),
			esc_attr( $_product->get_sku() )
		);
		?>
    </div>
	<?php
}

/**
 * Remove cart item from checkout page
 * @since 2.9.6
 * @author Foysal
 */
add_action( 'wp_ajax_tf_checkout_cart_item_remove', 'tf_checkout_cart_item_remove' );
add_action( 'wp_ajax_nopriv_tf_checkout_cart_item_remove', 'tf_checkout_cart_item_remove' );
function tf_checkout_cart_item_remove() {
	if ( isset( $_POST['cart_item_key'] ) ) {
		$cart_item_key = sanitize_key( $_POST['cart_item_key'] );

		// Remove cart item
		WC()->cart->remove_cart_item( $cart_item_key );
	}

	die();
}

/**
 * Hotel gallery video content initialize by this hook
 * can be filtered the video url by "tf_hotel_gallery_video_url" Filter
 * @since 2.9.7
 * @author Abu Hena
 */
if ( ! function_exists( 'tf_hotel_gallery_video' ) ) {
	function tf_hotel_gallery_video( $meta ) {

		//Hotel video section in the hero
		$url = ! empty( $meta['video'] ) ? $meta['video'] : '';
		if ( ! empty( $url ) ) {
			?>
            <div class="tf-hotel-video">
                <div class="tf-hero-btm-icon tf-hotel-video" data-fancybox="hotel-video" href="<?php echo apply_filters( 'tf_hotel_gallery_video_url', $url ); ?>">
                    <i class="fab fa-youtube"></i>
                </div>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'tourfic_template_settings' ) ) {
	function tourfic_template_settings() {
		$tf_plugin_installed = get_option( 'tourfic_template_installed' );
		if ( ! empty( $tf_plugin_installed ) ) {
			$template = 'design-1';
		} else {
			$template = 'default';
		}

		return $template;
	}
}
/**
 * Update options of email templates[admin,vendor, customer]
 *
 * @return void
 *
 * @since 2.9.19
 * @author Abu Hena
 */
add_action( 'admin_init', 'tf_update_email_template_default_content' );
function tf_update_email_template_default_content() {

	$tf_settings = get_option( 'tf_settings' );
	if ( isset( $tf_settings['email-settings'] ) ) {
		$tf_settings = $tf_settings['email-settings'];

		if ( ! is_array( $tf_settings ) ) {
			return;
		}

		//update email template for admin
		if ( empty( $tf_settings['admin_booking_email_template'] ) ) {
			update_option( $tf_settings['admin_booking_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'admin' ) );
		}
		//update email template for vendor
		if ( empty( $tf_settings['vendor_booking_email_template'] )) {
			if ( array_key_exists ('vendor_booking_email_template', $tf_settings ) )
				update_option( $tf_settings['vendor_booking_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'vendor' ) );
		}
		//update email template for customer
		if ( empty( $tf_settings['customer_confirm_email_template'] ) ) {
			update_option( $tf_settings['customer_confirm_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'customer' ) );
		}
	}
}

/**
 * Retrive Orders Data
 *
 * @return void
 *
 * @since 2.9.26
 * @author Jahid
 */

if ( ! function_exists( 'tourfic_order_table_data' ) ) {
	function tourfic_order_table_data( $query ) {
		global $wpdb;
		$query_type          = $query['post_type'];
		$query_select        = $query['select'];
		$query_where         = $query['query'];
		$tf_tour_book_orders = $wpdb->get_results( $wpdb->prepare( "SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s $query_where", $query_type ), ARRAY_A );

		return $tf_tour_book_orders;
	}
}

/*
 * Affiliate callback function
 */
if ( ! function_exists( 'tf_affiliate_callback' ) ) {
	function tf_affiliate_callback() {
		if ( current_user_can( 'activate_plugins' ) ) {
			?>
            <div class="tf-field tf-field-notice" style="width:100%;">
                <div class="tf-fieldset" style="margin: 0px;">
                    <div class="tf-field-notice-inner tf-notice-info">
                        <div class="tf-field-notice-content has-content">
							<?php if ( ! is_plugin_active( 'tourfic-affiliate/tourfic-affiliate.php' ) && ! file_exists( WP_PLUGIN_DIR . '/tourfic-affiliate/tourfic-affiliate.php' ) ) : ?>
                                <span style="margin-right: 15px;"><?php echo __( "Tourfic affiliate addon is not installed. Please install and activate it to use this feature.", "tourfic" ); ?> </span>
                                <a target="_blank" href="https://portal.themefic.com/my-account/downloads" class="tf-admin-btn tf-btn-secondary tf-submit-btn"
                                   style="margin-top: 5px;"><?php echo __( "Download", "tourfic" ); ?></a>
							<?php elseif ( ! is_plugin_active( 'tourfic-affiliate/tourfic-affiliate.php' ) && file_exists( WP_PLUGIN_DIR . '/tourfic-affiliate/tourfic-affiliate.php' ) ) : ?>
                                <span style="margin-right: 15px;"><?php echo __( "Tourfic affiliate addon is not activated. Please activate it to use this feature.", "tourfic" ); ?> </span>
                                <a href="#" class="tf-admin-btn tf-btn-secondary tf-affiliate-active" style="margin-top: 5px;"><?php echo __( 'Activate Tourfic Affiliate', 'tourfic' ); ?></a>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}
	}
}

/*
 * Activate Tourfic Affiliate
 */
add_action( 'wp_ajax_tf_affiliate_active', 'tf_affiliate_active_callback' );
function tf_affiliate_active_callback() {
	$response = [
		'status'  => 'error',
		'message' => __( 'Something went wrong. Please try again.', 'tourfic' )
	];
//    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
//    if ( ! wp_verify_nonce( $nonce, 'tf_affiliate_active' ) ) {
//        wp_send_json_error( $response );
//    }
	if ( current_user_can( 'activate_plugins' ) ) {
		$plugin = 'tourfic-affiliate/tourfic-affiliate.php';
		$result = activate_plugin( $plugin );
		if ( is_wp_error( $result ) ) {
			$response['message'] = $result->get_error_message();
		} else {
			$response['status']  = 'success';
			$response['message'] = __( 'Tourfic Affiliate activated successfully.', 'tourfic' );
		}
	}

	echo wp_json_encode( $response );
	die();
}

/*
 * Install and active Tourfic Affiliate
 */
add_action( 'wp_ajax_tf_affiliate_install', 'tf_affiliate_install_callback' );
function tf_affiliate_install_callback() {
	$response = [
		'status'  => 'error',
		'message' => __( 'Something went wrong. Please try again.', 'tourfic' )
	];
	$nonce    = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'tf_affiliate_install' ) ) {
		wp_send_json_error( $response );
	}
	if ( current_user_can( 'activate_plugins' ) ) {
		$plugin = 'tourfic-affiliate';
		$result = install_plugin_install_status( $plugin );
		if ( is_wp_error( $result ) ) {
			$response['message'] = $result->get_error_message();
		} else {
			$response['status']  = 'success';
			$response['message'] = __( 'Tourfic Affiliate installed successfully.', 'tourfic' );
		}
	}

	echo wp_json_encode( $response );
	die();
}

if ( ! function_exists( 'tourfic_vendor_order_table_data' ) ) {
	function tourfic_vendor_order_table_data( $query ) {
		global $wpdb;
		$query_select = $query['select'];
		$query_type   = $query['post_type'];
		$query_author = $query['author'];
		$query_limit  = $query['limit'];

		$vendor_query  = $wpdb->prepare(
			"SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s AND post_id IN (
				SELECT ID FROM {$wpdb->posts} WHERE post_author = %d
			) ORDER BY order_id DESC $query_limit",
			$query_type, $query_author
		);
		$orders_result = $wpdb->get_results( $vendor_query, ARRAY_A );

		return $orders_result;
	}
}

/**
 * Template 3 Default Data Migration v2.10.3
 *
 * run once
 */
function tf_template_3_migrate_data() {

	if ( empty( get_option( 'tf_template_3_migrate_data' ) ) ) {

		$options = get_option( 'tf_settings' );
		if( !empty($options["tf-template"]) ){
			$options["tf-template"]["single-hotel-layout-part-1"] = array(
				array(
					"hotel-section" => "Description",
					"hotel-section-slug" => "description",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section" => "Features",
					"hotel-section-slug" => "features",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section" => "Room",
					"hotel-section-slug" => "rooms",
					"hotel-section-status" => "1"
				)
			);
			$options["tf-template"]["single-hotel-layout-part-2"] = array(
				array(
					"hotel-section" => "Facilities",
					"hotel-section-slug" => "facilities",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section" => "Review",
					"hotel-section-slug" => "review",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section" => "FAQ",
					"hotel-section-slug" => "faq",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section" => "Terms & Conditions",
					"hotel-section-slug" => "trams-condition",
					"hotel-section-status" => "1"
				)
			);

			$options["tf-template"]["single-tour-layout-part-1"] = array(
				array(
					"tour-section" => "Description",
					"tour-section-slug" => "description",
					"tour-section-status" => "1"
				),
				array(
					"tour-section" => "Information",
					"tour-section-slug" => "information",
					"tour-section-status" => "1"
				),
				array(
					"tour-section" => "Highlights",
					"tour-section-slug" => "highlights",
					"tour-section-status" => "1"
				),
				array(
					"tour-section" => "Include & Exclude",
					"tour-section-slug" => "include-exclude",
					"tour-section-status" => "1"
				),
				array(
					"tour-section" => "Itinerary",
					"tour-section-slug" => "itinerary",
					"tour-section-status" => "1"
				)
			);

			$options["tf-template"]["single-tour-layout-part-2"] = array(
				array(
					"tour-section" => "FAQ",
					"tour-section-slug" => "faq",
					"tour-section-status" => "1"
				),
				array(
					"tour-section" => "Review",
					"tour-section-slug" => "review",
					"tour-section-status" => "1"
				),
				array(
					"tour-section" => "Terms & Conditions",
					"tour-section-slug" => "trams-condition",
					"tour-section-status" => "1"
				)
			);
		}

		update_option( 'tf_settings', $options );
		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_template_3_migrate_data', 1 );

	}

}

add_action( 'init', 'tf_template_3_migrate_data' );

/* Text Limit 
* @Author Jahid
*/
if( ! function_exists('tourfic_character_limit_callback') ){
    function tourfic_character_limit_callback($str, $limit, $dots = true)
    {
		if(strlen($str) > $limit ){
			if($dots == true) {
				return substr($str, 0, $limit) . '...';
			} else {
				return substr($str, 0, $limit);
			}
		}else{
			return $str;
		}
    }
}
// External listing dynamic shortcode callback

add_action( "wp_ajax_tf_shortcode_type_to_location", "tf_shortcode_type_to_location_callback" );

function tf_shortcode_type_to_location_callback() {
    $term_name = $_POST['termName'] ? sanitize_text_field( $_POST['termName'] ) : 'tf_hotel';

	$terms = get_terms( array(
		'taxonomy'   => $term_name,
		'hide_empty' => false,
	) );

	wp_reset_postdata();

	wp_send_json_success( array(
		'value' => $terms,
		"termName" => $term_name
	) );
}