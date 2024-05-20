<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

if ( ! function_exists( 'tf_is_woo_active' ) ) {
	function tf_is_woo_active() {
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}
}

/**
 * Show admin warning if a required file is missing
 */
function tf_file_missing( $files = '' ) {

	if ( is_admin() ) {
		if ( ! empty( $files ) ) {
			$class   = 'notice notice-error';
			$message = '<strong>' . $files . '</strong>' . esc_html__( ' file is missing! It is required to function Tourfic properly!', 'tourfic' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
		}
	}

}

add_action( 'admin_notices', 'tf_file_missing' );

/**
 * WC Product Extend
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' ) ) {
	function fida() {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php';
	}

	if ( tf_is_woo_active() ) {
		add_action( 'init', 'fida' );
	}
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
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-common.php' ) ) {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-common.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-common.php' );
	}
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
 * Include Functions Vat
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_vat.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions_vat.php';
} else {
    tf_file_missing( TF_INC_PATH . 'functions/functions_vat.php' );
}

/**
 * Shortcodes
 *
 * @since 1.0
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/shortcodes.php' ) ) {
		require_once TF_INC_PATH . 'functions/shortcodes.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/shortcodes.php' );
	}
}

/**
 * Widgets
 *
 * @since 1.0
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/widgets.php' ) ) {
		require_once TF_INC_PATH . 'functions/widgets.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/widgets.php' );
	}
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
 * Search Result Sidebar form
 */
function tf_search_result_sidebar_form( $placement = 'single' ) {

	// Unwanted Slashes Remove
	if ( isset( $_GET ) ) {
		$_GET = array_map( 'stripslashes_deep', $_GET );
	}

	// Get post type
	$post_type             = esc_attr($_GET['type']) ?? '';
	$place_title           = '';
	$date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
	$hotel_location_field_required = !empty( Helper::tfopt( "required_location_hotel_search" )) ? Helper::tfopt( "required_location_hotel_search" ) : 0;
	$tour_location_field_required = !empty( Helper::tfopt( "required_location_tour_search" )) ? Helper::tfopt( "required_location_tour_search" ) : 0;
	$place_input_id = '';

	if ( ! empty( $post_type ) ) {

		$place_input_id = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
		if ( $post_type == 'tf_apartment' ) {
			$place_input_id = 'tf-apartment-location';
		}
		$place_placeholder = ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) ? esc_html__( 'Enter Location', 'tourfic' ) : esc_html__( 'Enter Destination', 'tourfic' );

		$place_key   = 'place';
		$place_value = !empty($_GET[ $place_key ]) ? esc_attr($_GET[ $place_key ]) : '';
		$place_title = ! empty( $_GET['place-name'] ) ? esc_attr($_GET['place-name']) : '';

		$taxonomy = $post_type == 'tf_hotel' ? 'hotel_location' : ( $post_type == 'tf_tour' ? 'tour_destination' : 'apartment_location' );
		// $place_name = ! empty( $place_value ) ? get_term_by( 'slug', $place_value, $taxonomy )->name : '';
		$place_name = ! empty( $place_value ) ? esc_attr( $place_value ) : '';

		$room = !empty($_GET['room']) ? esc_attr($_GET['room']) : 0;
	}

	$adult      = !empty($_GET['adults']) ? esc_attr($_GET['adults']) : 0;
	$children   = !empty($_GET['children']) ? esc_attr($_GET['children']) : 0;
	$infant     = !empty($_GET['infant']) ? esc_attr($_GET['infant']) : 0;
	$date       = !empty($_GET['check-in-out-date']) ? esc_attr($_GET['check-in-out-date']) : '';
	$startprice = !empty($_GET['from']) ? esc_attr($_GET['from']) : '';
	$endprice   = !empty($_GET['to']) ? esc_attr($_GET['to']) : '';

	$tf_tour_arc_selected_template  = ! empty( tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] ) ? tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
	$tf_hotel_arc_selected_template = ! empty( tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] ) ? tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
	$tf_apartment_arc_selected_template = ! empty( tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';

	$disable_child_search = ! empty( Helper::tfopt( 'disable_child_search' ) ) ? Helper::tfopt( 'disable_child_search' ) : '';
	$disable_infant_search = ! empty( Helper::tfopt( 'disable_infant_search' ) ) ? Helper::tfopt( 'disable_infant_search' ) : '';
	$disable_hotel_child_search = ! empty( Helper::tfopt( 'disable_hotel_child_search' ) ) ? Helper::tfopt( 'disable_hotel_child_search' ) : '';
	$disable_apartment_child_search = ! empty( Helper::tfopt( 'disable_apartment_child_search' ) ) ? Helper::tfopt( 'disable_apartment_child_search' ) : '';
	$disable_apartment_infant_search = ! empty( Helper::tfopt( 'disable_apartment_infant_search' ) ) ? Helper::tfopt( 'disable_apartment_infant_search' ) : '';

	if ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-1" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-1" ) ) {
		?>
        <div class="tf-box-wrapper tf-box tf-mrbottom-30">
            <form class="widget tf-hotel-side-booking" method="get" autocomplete="off"
                  action="<?php echo esc_url( tf_booking_search_action() ); ?>" id="tf-widget-booking-search">

                <div class="tf-field-group tf-destination-box" <?php echo ($post_type == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1) || ($post_type == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1) ? 'style="display:none"' : ''?>>
                    <i class="fa-solid fa-location-dot"></i>

                    <?php if($post_type == "tf_hotel") { ?>
						<input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field" placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
					<?php } elseif( $post_type == "tf_tours" ) { ?>
						<input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field" placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
					<?php } else { ?>
						<input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" required class="tf-field" placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
					<?php } ?>

                    <input type="hidden" name="place" id="tf-place" value="<?php echo esc_attr( $place_value ) ?? ''; ?> "/>
                </div>
                <div class="tf-field-group tf-mt-8 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-regular fa-user"></i>
							<?php esc_html_e( 'Adults', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adult ) ? esc_attr( $adult ) : 1; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>

                <div class="tf-field-group tf-mt-16 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-solid fa-child"></i>
							<?php esc_html_e( 'Children', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $children ) ? esc_attr( $children ) : 0; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>

                <div class="tf-field-group tf-mt-8">
                    <i class="fa-solid fa-calendar-days"></i>
                    <input type="text" class="tf-field time" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                           placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="<?php echo esc_attr( $date ) ?>">
                </div>


				<?php if ( $post_type == 'tf_hotel' ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa fa-building"></i>
								<?php esc_html_e( 'Rooms', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? esc_attr( $room ) : 1; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
				<?php } ?>

                <div class="tf-booking-bttns tf-mt-30">
					<?php
					$ptype = esc_attr($_GET['type']) ?? get_post_type();
					?>
                    <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
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
						altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
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
						defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
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
	elseif ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-2" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-2" ) || ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template == "design-1" ) ) { ?>
		<div class="tf-booking-form-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
			<div class="tf-booking-form-location" <?php echo ($post_type == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1) || ($post_type == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1) ? 'style="display:none"' : ''?>>
				<span class="tf-booking-form-title"><?php esc_html_e("Location", "tourfic"); ?></span>
				<label for="tf-search-location" class="tf-booking-location-wrap">
					<svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
					<path d="M8.5 13.9317L11.7998 10.6318C13.6223 8.80943 13.6223 5.85464 11.7998 4.0322C9.9774 2.20975 7.02261 2.20975 5.20017 4.0322C3.37772 5.85464 3.37772 8.80943 5.20017 10.6318L8.5 13.9317ZM8.5 15.8173L4.25736 11.5747C1.91421 9.2315 1.91421 5.43254 4.25736 3.08939C6.60051 0.746245 10.3995 0.746245 12.7427 3.08939C15.0858 5.43254 15.0858 9.2315 12.7427 11.5747L8.5 15.8173ZM8.5 8.66536C9.2364 8.66536 9.83333 8.06843 9.83333 7.33203C9.83333 6.59565 9.2364 5.9987 8.5 5.9987C7.7636 5.9987 7.16667 6.59565 7.16667 7.33203C7.16667 8.06843 7.7636 8.66536 8.5 8.66536ZM8.5 9.9987C7.02724 9.9987 5.83333 8.80476 5.83333 7.33203C5.83333 5.85927 7.02724 4.66536 8.5 4.66536C9.97273 4.66536 11.1667 5.85927 11.1667 7.33203C11.1667 8.80476 9.97273 9.9987 8.5 9.9987Z" fill="#595349"/>
					</svg>

					<?php if($post_type == "tf_hotel" ) { ?>
						<input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field" placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo !empty($place_title) ? esc_attr( $place_title ) : ''; ?>">
					<?php } elseif( $post_type == "tf_tours" ) { ?>
						<input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field" placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo !empty($place_title) ? esc_attr( $place_title ) : ''; ?>">
					<?php } else { ?>
						<input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" required class="tf-field" placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                           value="<?php echo !empty($place_title) ? esc_attr( $place_title ) : ''; ?>">
					<?php } ?>

                    <input type="hidden" name="place" id="tf-place" value="<?php echo esc_attr( $place_value ) ?? ''; ?>"/>
				</label>
			</div>
			<?php if ( $post_type == 'tf_hotel' || $post_type == "tf_apartment" ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php esc_html_e("Check in", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo esc_html( gmdate('M') ); ?></span>
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
				<span class="tf-booking-form-title"><?php esc_html_e("Check out", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo esc_html( gmdate('M') ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
				<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo esc_attr( $date ) ?>" required>

			</div>
			<?php } ?>

			<?php if ( $post_type == 'tf_tours' ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php esc_html_e("Date", "tourfic"); ?></span>
				<div class="tf-tour-searching-date-block">
					<div class="tf-booking-date-wrap tf-tour-start-date">
						<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo esc_html( gmdate('M') ); ?></span>
						</span>
					</div>
					<div class="tf-duration">
						<span>-</span>
					</div>
					<div class="tf-booking-date-wrap tf-tour-end-date">
						<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo esc_html( gmdate('M') ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
					</div>
					<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $date ) ? 'value="' . esc_attr( $date ) . '"' : '' ?> required>
				</div>
			</div>
			<?php } ?>

			<div class="tf-booking-form-guest-and-room">
				<?php if ( $post_type == 'tf_hotel' ) { ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php esc_html_e("Guests & rooms", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap tf-archive-guest-info">
						<span class="tf-guest"><?php echo esc_html( $adult+$children ) ?> </span> <?php esc_html_e("guest", "tourfic"); ?> <span class="tf-room"><?php echo esc_html( $room ); ?></span> <?php esc_html_e("Rooms", "tourfic"); ?>
					</div>
					<div class="tf-arrow-icons">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</div>
				</div>
				<?php } if ( $post_type == 'tf_tours' || $post_type == 'tf_apartment' ) { ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php esc_html_e("Guests", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap">
						<span class="tf-guest tf-booking-date">
							0<?php echo esc_html( $adult+$children ) ?>
						</span>
						<span class="tf-booking-month">
							<span><?php esc_html_e("Guest", "tourfic"); ?></span>
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
							<div class="acr-label"><?php esc_html_e("Adults", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13094)">
										<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
									</g>
									<defs>
										<clipPath id="clip0_3229_13094">
										<rect width="20" height="20" fill="white"></rect>
										</clipPath>
									</defs>
									</svg>
								</div>
								<input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adult ) ? esc_attr( $adult ) : 1; ?>" readonly>
								<div class="acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13100)">
										<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
									</g>
									<defs>
										<clipPath id="clip0_3229_13100">
										<rect width="20" height="20" fill="white"></rect>
										</clipPath>
									</defs>
									</svg>
								</div>
							</div>
						</div>
						<div class="tf_acrselection">
							<div class="acr-label"><?php esc_html_e("Children", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13094)">
										<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
									</g>
									<defs>
										<clipPath id="clip0_3229_13094">
										<rect width="20" height="20" fill="white"></rect>
										</clipPath>
									</defs>
									</svg>
								</div>
								<input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $children ) ? esc_attr( $children ) : 0; ?>" readonly>
								<div class="acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13100)">
										<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
									</g>
									<defs>
										<clipPath id="clip0_3229_13100">
										<rect width="20" height="20" fill="white"></rect>
										</clipPath>
									</defs>
									</svg>
								</div>
							</div>
						</div>
						<?php if ( $post_type == 'tf_hotel' ) { ?>
						<div class="tf_acrselection">
							<div class="acr-label"><?php esc_html_e("Rooms", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13094)">
										<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
									</g>
									<defs>
										<clipPath id="clip0_3229_13094">
										<rect width="20" height="20" fill="white"></rect>
										</clipPath>
									</defs>
									</svg>
								</div>
								<input type="tel" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? esc_attr( $room ) : 1; ?>" readonly>
								<div class="acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13100)">
										<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
									</g>
									<defs>
										<clipPath id="clip0_3229_13100">
										<rect width="20" height="20" fill="white"></rect>
										</clipPath>
									</defs>
									</svg>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="tf-booking-form-submit">
			<?php
			$ptype = esc_attr($_GET['type']) ?? get_post_type();
			?>
			<input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
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
						defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
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

		<?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) { ?>

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
						defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
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
              action="<?php echo esc_url( tf_booking_search_action() ); ?>" id="tf-widget-booking-search">

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner" <?php echo ($post_type == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1) || ($post_type == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1) ? 'style="display:none"' : ''?>>
                        <i class="fas fa-map-marker-alt"></i>

                        <?php if($post_type == "tf_hotel" ) { ?>
							<input type="text" id="<?php echo isset($place_input_id) ? esc_attr( $place_input_id ) : ''; ?>" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> class="" placeholder="<?php echo isset( $place_placeholder ) ? esc_attr($place_placeholder) : esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                               value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } elseif( $post_type == "tf_tours" ) { ?>
							<input type="text" id="<?php echo isset($place_input_id) ? esc_attr( $place_input_id ) : ''; ?>" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> class="" placeholder="<?php echo isset( $place_placeholder ) ? esc_attr($place_placeholder) : esc_html__( 'Location/Destination', 'tourfic' ); ?>"
							value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } else { ?>
							<input type="text" id="<?php echo isset($place_input_id) ? esc_attr( $place_input_id ) : ''; ?>" required class="" placeholder="<?php echo isset( $place_placeholder ) ? esc_attr($place_placeholder) : esc_html__( 'Location/Destination', 'tourfic' ); ?>"
							value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } ?>
                        <input type="hidden" name="place" id="tf-place" value="<?php echo isset( $place_value ) ? esc_attr($place_value) : ''; ?>"/>
                    </div>
                </label>
            </div>

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="fas fa-user-friends"></i>
                        <select name="adults" id="adults" class="">
                            <option <?php echo 1 == $adult ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Adult', 'tourfic' ); ?></option>
							<?php foreach ( range( 2, 8 ) as $value ) {
								$selected = $value == $adult ? 'selected' : null;
								echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Adults", "tourfic" ) . '</option>';
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
                                <option value="0">0 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
                                <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $children ? 'selected' : null;
									echo '<option ' . esc_attr ( $selected ) . ' value="' . esc_attr ( $value ) . '">' . esc_html ( $value ) . ' ' . esc_html__( "Children", "tourfic" ) . '</option>';
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
                                <option value="0">0 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
                                <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $children ? 'selected' : null;
									echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Children", "tourfic" ) . '</option>';
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
                                <option value="0">0 <?php esc_html_e( 'Infant', 'tourfic' ); ?></option>
                                <option <?php echo 1 == $infant ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Infant', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $infant ? 'selected' : null;
									echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Infant", "tourfic" ) . '</option>';
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
                                <option <?php echo 1 == $room ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Room', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $room ? 'selected' : null;
									echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Rooms", "tourfic" ) . '</option>';
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
                                   placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="<?php echo esc_attr( $date ) ?>">
                        </div>
                    </label>
                </div>
            </div>

            <div class="tf_form-row">
				<?php
				if ( ! empty( $startprice ) && ! empty( $endprice ) ) { ?>
                    <input type="hidden" id="startprice" value="<?php echo esc_attr( $startprice ); ?>">
                    <input type="hidden" id="endprice" value="<?php echo esc_attr( $endprice ); ?>">
				<?php } ?>
				<?php
				if ( ! empty( $_GET['tf-author'] ) ) { ?>
                    <input type="hidden" id="tf_author" value="<?php echo esc_html( $_GET['tf-author'] ); ?>">
				<?php } ?>
				<?php
				$ptype = esc_attr($_GET['type']) ?? get_post_type();
				?>
                <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
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
						altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
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
						defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
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
	$place_text            = $post_type == 'tf_hotel' ? esc_html__( 'Enter Location', 'tourfic' ) : esc_html__( 'Enter Destination', 'tourfic' );
	$date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

	$tf_tour_arc_selected_template  = ! empty( tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] ) ? tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
	$tf_hotel_arc_selected_template = ! empty( tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] ) ? tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
	$tf_apartment_arc_selected_template = ! empty( tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';
	$hotel_location_field_required = !empty( Helper::tfopt( "required_location_hotel_search" )) ?  Helper::tfopt( "required_location_hotel_search" ) : 0;
	$tour_location_field_required = !empty( Helper::tfopt( "required_location_tour_search" )) ?  Helper::tfopt( "required_location_tour_search" ) : 0;

	if ( ( is_post_type_archive( 'tf_hotel' ) && $tf_hotel_arc_selected_template == "design-1" ) || ( is_post_type_archive( 'tf_tours' ) && $tf_tour_arc_selected_template == "design-1" ) || ( $post_type == 'tf_hotel' && $tf_hotel_arc_selected_template == "design-1" ) || ( $post_type == 'tf_tours' && $tf_tour_arc_selected_template == "design-1" ) ) {
		?>
        <div class="tf-box-wrapper tf-box tf-mrbottom-30">
            <form action="<?php echo esc_url( tf_booking_search_action() ); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking">
                <div class="tf-field-group tf-destination-box" <?php echo ($post_type == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1) || ( $post_type == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : ''?>>
                    <i class="fa-solid fa-location-dot"></i>

					<?php if(is_post_type_archive("tf_hotel" )) { ?>
						   <input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
					<?php } elseif( is_post_type_archive("tf_tours") ) { ?>
						<input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name) : ''; ?>">
					<?php } else { ?>
						<input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name) : ''; ?>">
					<?php } ?>
                    <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug  ): ''; ?>"/>

                </div>
                <div class="tf-field-group tf-mt-8 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-regular fa-user"></i>
							<?php esc_html_e( 'Adults', 'tourfic' ); ?>
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
							<?php esc_html_e( 'Children', 'tourfic' ); ?>
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
								<?php esc_html_e( 'Room', 'tourfic' ); ?>
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
                           placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="" style="width: 100% !important">
                </div>
                <div class="tf_booking-dates">
                    <div class="tf_label-row"></div>
                </div>
                <div class="tf-booking-bttns tf-mt-30">
                    <input type="hidden" name="type" value="<?php echo esc_attr( $post_type ); ?>" class="tf-post-type"/>
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
						altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

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
	elseif ( ( is_post_type_archive( 'tf_hotel' ) && $tf_hotel_arc_selected_template == "design-2" ) || ( is_post_type_archive( 'tf_tours' ) && $tf_tour_arc_selected_template == "design-2" ) || ( is_post_type_archive( 'tf_apartment' ) && $tf_apartment_arc_selected_template == "design-1" ) || ( $post_type == 'tf_hotel' && $tf_hotel_arc_selected_template == "design-2" ) || ( $post_type == 'tf_tours' && $tf_tour_arc_selected_template == "design-2" ) || ( $post_type == 'tf_apartment' && $tf_apartment_arc_selected_template == "design-1" ) ) { ?>
		<div class="tf-booking-form-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
			<div class="tf-booking-form-location" <?php echo ($post_type == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1) || ($post_type == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1) ? 'style="display:none"' : ''?>>
				<span class="tf-booking-form-title"><?php esc_html_e("Location", "tourfic"); ?></span>
				<label for="tf-search-location" class="tf-booking-location-wrap">
					<svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
					<path d="M8.5 13.9317L11.7998 10.6318C13.6223 8.80943 13.6223 5.85464 11.7998 4.0322C9.9774 2.20975 7.02261 2.20975 5.20017 4.0322C3.37772 5.85464 3.37772 8.80943 5.20017 10.6318L8.5 13.9317ZM8.5 15.8173L4.25736 11.5747C1.91421 9.2315 1.91421 5.43254 4.25736 3.08939C6.60051 0.746245 10.3995 0.746245 12.7427 3.08939C15.0858 5.43254 15.0858 9.2315 12.7427 11.5747L8.5 15.8173ZM8.5 8.66536C9.2364 8.66536 9.83333 8.06843 9.83333 7.33203C9.83333 6.59565 9.2364 5.9987 8.5 5.9987C7.7636 5.9987 7.16667 6.59565 7.16667 7.33203C7.16667 8.06843 7.7636 8.66536 8.5 8.66536ZM8.5 9.9987C7.02724 9.9987 5.83333 8.80476 5.83333 7.33203C5.83333 5.85927 7.02724 4.66536 8.5 4.66536C9.97273 4.66536 11.1667 5.85927 11.1667 7.33203C11.1667 8.80476 9.97273 9.9987 8.5 9.9987Z" fill="#595349"/>
					</svg>
					<?php if(is_post_type_archive("tf_hotel" )) { ?>
						<input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
					<?php } elseif( is_post_type_archive("tf_tours" ) ) { ?>
					<input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
					<?php } else { ?>
					<input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
					<?php } ?>
                    <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>
				</label>
			</div>

			<?php if ( $post_type == 'tf_hotel' ||  $post_type == 'tf_apartment' ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php esc_html_e("Check in", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo esc_html( gmdate('M') ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
			</div>
			<div class="tf-booking-form-checkout">
				<span class="tf-booking-form-title"><?php esc_html_e("Check out", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo esc_html( gmdate('M') ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
				<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
			</div>
			<?php } ?>

			<?php if ( $post_type == 'tf_tours' ) { ?>
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php esc_html_e("Date", "tourfic"); ?></span>
				<div class="tf-tour-searching-date-block">
					<div class="tf-booking-date-wrap tf-tour-start-date">
						<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo esc_html( gmdate('M') ); ?></span>
						</span>
					</div>
					<div class="tf-duration">
						<span>-</span>
					</div>
					<div class="tf-booking-date-wrap tf-tour-end-date">
						<span class="tf-booking-date"><?php esc_html_e("00", "tourfic"); ?></span>
						<span class="tf-booking-month">
							<span><?php echo esc_html( gmdate('M') ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
					</div>
					<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
				</div>
			</div>
			<?php } ?>
			<div class="tf-booking-form-guest-and-room">
				<?php if ( $post_type == 'tf_hotel' ) { ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php esc_html_e("Guests & rooms", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap tf-archive-guest-info">
						<span class="tf-guest"><?php esc_html_e("01", "tourfic"); ?></span> <?php esc_html_e("guest", "tourfic"); ?> <span class="tf-room"><?php esc_html_e("01", "tourfic"); ?></span> <?php esc_html_e("rooms", "tourfic"); ?>
					</div>
					<div class="tf-arrow-icons">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</div>
				</div>
				<?php }else{ ?>
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php esc_html_e("Guests", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap">
						<span class="tf-guest tf-booking-date">
							<?php esc_html_e("01", "tourfic"); ?>
						</span>
						<span class="tf-booking-month">
							<span><?php esc_html_e("guest", "tourfic"); ?></span>
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
							<div class="acr-label"><?php esc_html_e("Adults", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13094)">
										<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
									</g>
									<defs>
										<clipPath id="clip0_3229_13094">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
								<input type="tel" name="adults" id="adults" min="1" value="1" readonly>
								<div class="acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13100)">
										<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
									</g>
									<defs>
										<clipPath id="clip0_3229_13100">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
							</div>
						</div>
						<div class="tf_acrselection">
							<div class="acr-label"><?php esc_html_e("Children", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13094)">
										<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
									</g>
									<defs>
										<clipPath id="clip0_3229_13094">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
								<input type="tel" name="childrens" id="children" min="0" value="0" readonly>
								<div class="acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13100)">
										<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
									</g>
									<defs>
										<clipPath id="clip0_3229_13100">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
							</div>
						</div>
						<?php if ( $post_type == 'tf_hotel' ) { ?>
						<div class="tf_acrselection">
							<div class="acr-label"><?php esc_html_e("Rooms", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13094)">
										<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
									</g>
									<defs>
										<clipPath id="clip0_3229_13094">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
								<input type="tel" name="room" id="room" min="1" value="1" readonly>
								<div class="acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_3229_13100)">
										<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
									</g>
									<defs>
										<clipPath id="clip0_3229_13100">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="tf-booking-form-submit">
			<input type="hidden" name="type" value="<?php echo esc_attr( $post_type ); ?>" class="tf-post-type"/>
            <button class="tf-btn-normal btn-primary tf-submit"><?php echo esc_html__( 'Check Availability', 'tourfic' ); ?></button>
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
						defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
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

		<?php if ( $post_type == 'tf_hotel' ||  $post_type == 'tf_apartment' ) { ?>
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
						defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
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
              action="<?php echo esc_url( tf_booking_search_action() ); ?>">

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner" <?php echo ($post_type == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1) || ($post_type == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : ''?>>
                        <i class="fas fa-map-marker-alt"></i>
						
						<?php if(is_post_type_archive("tf_hotel" )) { ?>
							<input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } elseif(is_post_type_archive("tf_tours")) { ?>
							<input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } else { ?>
							<input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="" placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } ?>
                        
                        <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>
                    </div>
                </label>
            </div>

            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="fas fa-user-friends"></i>
                        <select name="adults" id="adults" class="">
							<?php
							echo '<option value="1">1 ' . esc_html__( "Adult", "tourfic" ) . '</option>';
							foreach ( range( 2, 8 ) as $value ) {
								echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Adults", "tourfic" ) . '</option>';
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
							echo '<option value="0">0 ' . esc_html__( "Children", "tourfic" ) . '</option>';
							foreach ( range( 1, 8 ) as $value ) {
								echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Children", "tourfic" ) . '</option>';
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
                                <option value="0">0 <?php esc_html_e( 'Infant', 'tourfic' ); ?></option>
								<?php foreach ( range( 1, 8 ) as $value ) {
									echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Infant", "tourfic" ) . '</option>';
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
								echo '<option value="1">1 ' . esc_html__( "Room", "tourfic" ) . '</option>';
								foreach ( range( 2, 8 ) as $value ) {
									echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Rooms", "tourfic" ) . '</option>';
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
                                   placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="">
                        </div>
                    </label>
                </div>
            </div>

            <div class="tf_form-row">
                <input type="hidden" name="type" value="<?php echo esc_attr( $post_type ); ?>" class="tf-post-type"/>
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
						altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

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
			$select .= '<option value="\'all\'">' . esc_html__( 'All', 'tourfic' ) . '</option>';
			foreach ( $terms as $term ) {
				$select .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
			}
			$select .= "</select>";
		} else {
			$select .= esc_html__( "Invalid taxonomy!!", 'tourfic' );
		}
		echo wp_kses( $select, Helper::tf_custom_wp_kses_allow_tags() );
	}
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
                <div class="tf-hero-btm-icon tf-hotel-video" data-fancybox="hotel-video" href="<?php echo esc_url( apply_filters( 'tf_hotel_gallery_video_url', $url ) ); ?>">
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




/*
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
                                <span style="margin-right: 15px;"><?php echo esc_html__( "Tourfic affiliate addon is not installed. Please install and activate it to use this feature.", "tourfic" ); ?> </span>
                                <a target="_blank" href="https://portal.themefic.com/my-account/downloads" class="tf-admin-btn tf-btn-secondary tf-submit-btn"
                                   style="margin-top: 5px;"><?php echo esc_html__( "Download", "tourfic" ); ?></a>
							<?php elseif ( ! is_plugin_active( 'tourfic-affiliate/tourfic-affiliate.php' ) && file_exists( WP_PLUGIN_DIR . '/tourfic-affiliate/tourfic-affiliate.php' ) ) : ?>
                                <span style="margin-right: 15px;"><?php echo esc_html__( "Tourfic affiliate addon is not activated. Please activate it to use this feature.", "tourfic" ); ?> </span>
                                <a href="#" class="tf-admin-btn tf-btn-secondary tf-affiliate-active" style="margin-top: 5px;"><?php echo esc_html__( 'Activate Tourfic Affiliate', 'tourfic' ); ?></a>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}
	}
}