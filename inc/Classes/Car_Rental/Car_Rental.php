<?php

namespace Tourfic\Classes\Car_Rental;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\App\TF_Review;

class Car_Rental {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
        add_action( 'wp_after_insert_post', array( $this, 'tf_car_assign_inline_taxonomies' ), 100, 3 );

        // Car CPT
        Car_Rental_CPT::instance();
    }


    function tf_car_assign_inline_taxonomies( $post_id, $post, $old_status ) {
		if ( 'tf_carrental' !== $post->post_type ) {
			return;
		}
		$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
		if ( isset( $meta['brands'] ) && ! empty( $meta['brands'] ) ) {
            $car_brands = array();
            $car_brands[] = intval( $meta['brands'] );
			wp_set_object_terms( $post_id, $car_brands, 'carrental_brand' );
		}
        if ( isset( $meta['fuel_types'] ) && ! empty( $meta['fuel_types'] ) ) {
            $car_fuel_types = array();
            $car_fuel_types[] = intval( $meta['fuel_types'] );
			wp_set_object_terms( $post_id, $car_fuel_types, 'carrental_fuel_type' );
		}
        if ( isset( $meta['engine_year'] ) && ! empty( $meta['engine_year'] ) ) {
            $car_engine_year = array();
            $car_engine_year[] = intval( $meta['engine_year'] );
			wp_set_object_terms( $post_id, $car_engine_year, 'carrental_engine_year' );
		}
	}

    /**
     * Car Search form
     * @author Jahid
     */

    static function tf_car_search_form_horizontal( $classes, $title, $subtitle, $advanced, $design ) {
        if ( isset( $_GET ) ) {
            $_GET = array_map( 'stripslashes_deep', $_GET );
        }
        // Check-in & out date
        $check_in_out = ! empty( $_GET['check-in-out-date'] ) ? esc_html( $_GET['check-in-out-date'] ) : '';

        // date format for apartments
        $date_format_change_apartments = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

        $disable_apartment_child_search  = ! empty( Helper::tfopt( 'disable_apartment_child_search' ) ) ? Helper::tfopt( 'disable_apartment_child_search' ) : '';
        $disable_apartment_infant_search  = ! empty( Helper::tfopt( 'disable_apartment_infant_search' ) ) ? Helper::tfopt( 'disable_apartment_infant_search' ) : '';
        
        if( !empty($design) && 2==$design ){
        ?>
        <form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
            <div class="tf_hotel_searching">
                <div class="tf_form_innerbody">
                    <div class="tf_form_fields">
                        <div class="tf_destination_fields">
                            <label class="tf_label_location">
                                <span class="tf-label"><?php esc_html_e( 'Pick-up', 'tourfic' ); ?></span>
                                <div class="tf_form_inners tf_form-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z"
                                                fill="#FAEEDD"/>
                                    </svg>
                                    <input type="text" name="pickup-name" id="tf_pickup_location" class="" placeholder="<?php esc_html_e( 'Enter Pickup Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="pickup" class="tf-place-input">
                                </div>
                            </label>
                        </div>

                        <div class="tf_destination_fields">
                            <label class="tf_label_location">
                                <span class="tf-label"><?php esc_html_e( 'Drop-off', 'tourfic' ); ?></span>
                                <div class="tf_form_inners tf_form-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z"
                                                fill="#FAEEDD"/>
                                    </svg>
                                    <input type="text" name="dropoff-name" id="tf_dropoff_location" class="" placeholder="<?php esc_html_e( 'Enter Dropoff Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="dropoff" class="tf-place-input">
                                </div>
                            </label>
                        </div>

                        <div class="tf_checkin_date">
                            <div class="tf_label_checkin">
                                <span class="tf-label"><?php esc_html_e( 'Pick-up date & Time', 'tourfic' ); ?></span>
                                <div class="tf_form_inners">
                                    <div class="tf_checkin_dates">
                                        <div class="tf-select-date tf-car-search-pickup-date">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
                                            <span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
                                            <div class="tf_check_arrow">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                                </svg>
                                            </div>
                                            </span>
                                        </div>
                                        <input type="text" name="pickup-date" class="tf_search_pickup_date" placeholder="<?php esc_html_e( 'Enter Pickup date', 'tourfic' ); ?>" value="">
                                    </div>

                                    <div class="tf_checkin_dates">
                                        <div class="tf-select-date tf-car-pickup-time">
                                            <span class="date"><?php echo esc_html( gmdate( 'h:m' ) ); ?></span>
                                            <span class="month">
                                            <div class="tf_check_arrow">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                                </svg>
                                            </div>
                                            </span>
                                        </div>
                                        <input type="text" name="pickup-time" class="tf_search_pickup_time" placeholder="<?php esc_html_e( 'Enter Pickup Time', 'tourfic' ); ?>" value="">
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="tf_checkin_date">
                            <div class="tf_label_checkin">
                                <span class="tf-label"><?php esc_html_e( 'Drop-off date & Time', 'tourfic' ); ?></span>
                                <div class="tf_form_inners">
                                    <div class="tf_checkin_dates">
                                        <div class="tf-select-date tf-car-search-dropoff-date">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
                                            <span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
                                            <div class="tf_check_arrow">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                                </svg>
                                            </div>
                                            </span>
                                        </div>
                                        <input type="text" name="dropoff-date" class="tf_search_dropoff_date" placeholder="<?php esc_html_e( 'Enter Drop-off date', 'tourfic' ); ?>" value="">
                                    </div>

                                    <div class="tf_checkin_dates">
                                        <div class="tf-select-date tf-car-dropoff-time">
                                            <span class="date"><?php echo esc_html( gmdate( 'h:m' ) ); ?></span>
                                            <span class="month">
                                            <div class="tf_check_arrow">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                                </svg>
                                            </div>
                                            </span>
                                        </div>
                                        <input type="text" name="dropoff-time" class="tf_search_dropoff_time" placeholder="<?php esc_html_e( 'Enter Drop-off Time', 'tourfic' ); ?>" value="">
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tf_availability_checker_box">
                        <input type="hidden" name="type" value="tf_carrental" class="tf-post-type"/>
                        <button><?php echo esc_html_e( "Check availability", "tourfic" ); ?></button>
                    </div>
                </div>
            </div>

        </form>
        <script>
            (function ($) {
                $(document).ready(function () {

                    // flatpickr locale first day of Week
                    <?php Helper::tf_flatpickr_locale( "root" ); ?>

                    $(".tf-car-search-pickup-date").on("click", function () {
                        $(".tf_search_pickup_date").trigger("click");
                    });
                    $(".tf_search_pickup_date").flatpickr({
                        enableTime: false,
                        dateFormat: "Y/m/d",
                        minDate: "today",

                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            dateSetToPickupFields(selectedDates, instance);
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            dateSetToPickupFields(selectedDates, instance);
                            dropoffFlatpickr.set("minDate", dateStr);
                        },
                    });

                    function dateSetToPickupFields(selectedDates, instance) {
                        const monthNames = [
                            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                        ];
                        if (selectedDates[0]) {
                            const startDate = selectedDates[0];
                            $(".tf-car-search-pickup-date span.date").html(startDate.getDate());
                            $(".tf-car-search-pickup-date span.month span").html(monthNames[startDate.getMonth()]);
                        }
                    }

                    $(".tf-car-search-dropoff-date").on("click", function () {
                        $(".tf_search_dropoff_date").trigger("click");
                    });
                    const dropoffFlatpickr = $(".tf_search_dropoff_date").flatpickr({
                        enableTime: false,
                        dateFormat: "Y/m/d",
                        minDate: "today",

                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            dateSetToDropoffFields(selectedDates, instance);
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            dateSetToDropoffFields(selectedDates, instance);
                        },
                    });

                    function dateSetToDropoffFields(selectedDates, instance) {
                        const monthNames = [
                            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                        ];
                        if (selectedDates[0]) {
                            const startDate = selectedDates[0];
                            $(".tf-car-search-dropoff-date span.date").html(startDate.getDate());
                            $(".tf-car-search-dropoff-date span.month span").html(monthNames[startDate.getMonth()]);
                        }
                    }

                    $(".tf-car-pickup-time").on("click", function () {
                        $(".tf_search_pickup_time").trigger("click");
                    });
                    // Initialize the pickup time picker
                    $(".tf_search_pickup_time").flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",

                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            $(".tf-car-pickup-time span.date").html(dateStr);
                        }
                    });

                    $(".tf-car-dropoff-time").on("click", function () {
                        $(".tf_search_dropoff_time").trigger("click");
                    });
                    // Initialize the dropoff time picker
                    $(".tf_search_dropoff_time").flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",

                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            $(".tf-car-dropoff-time span.date").html(dateStr);
                        }
                    });

                });
            })(jQuery);
        </script>
        <?php }elseif( !empty($design) && 3==$design ){ ?>

            <form class="tf-archive-search-box-wrapper <?php echo esc_attr( $classes ); ?>" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
				<div class="tf-date-select-box tf-flex tf-flex-gap-8 tf-date-selection-form">
					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn tf-pick-drop-location">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_257_3711)">
                                        <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_257_3711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Pick-up", "tourfic"); ?></h5>
									<input type="text" placeholder="Pick Up Location" id="tf_pickup_location" name="pickup-name" />
                                    <input type="hidden" name="pickup" class="tf-place-input">
								</div>
							</div>
						</div>

						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_257_3711)">
                                        <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_257_3711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Drop-off", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Location" id="tf_dropoff_location" name="dropoff-name" />
                                    <input type="hidden" name="dropoff" class="tf-place-input">
								</div>
							</div>
						</div>
					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Pick-up date", "tourfic"); ?></h5>
                                    <input type="text" name="pickup-date" class="tf_pickup_date" placeholder="<?php esc_html_e( 'Pickup date', 'tourfic' ); ?>" value="">
								</div>
							</div>
						</div>

						<div class="tf-select-date">
								<div class="tf-flex tf-flex-gap-4">
									<div class="icon">
									    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3728)">
                                            <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3728">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                        </svg>
								    </div>
								<div class="info-select">
									<h5><?php esc_html_e("Time", "tourfic"); ?></h5>
									<input type="text" placeholder="Pick Up Time" name="pickup-time" class="tf_pickup_time" />
								</div>
							</div>
						</div>
					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Drop-off date", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Date" name="dropoff-date" class="tf_dropoff_date" />
								</div>
							</div>
						</div>

						<div class="tf-select-date">
								<div class="tf-flex tf-flex-gap-4">
									<div class="icon">
									    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3728)">
                                            <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3728">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                        </svg>
								    </div>
								<div class="info-select">
									<h5><?php esc_html_e("Time", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Time" class="tf_dropoff_time" name="dropoff-time" />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="tf-driver-location-box tf-flex tf-flex-space-bttn tf-flex-align-center">
					<div class="tf-driver-location">
						
					</div>
					<div class="tf-submit-button">
                        <input type="hidden" name="type" value="tf_carrental" class="tf-post-type"/>
						<button type="submit"><?php esc_html_e( apply_filters("tf_car_search_form_submit_button_text", 'Search' ), 'tourfic' ); ?> <i class="ri-search-line"></i></button>
					</div>
				</div>
            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr locale first day of Week
                        <?php Helper::tf_flatpickr_locale('root'); ?>

                        // Initialize the pickup date picker
                        var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
                            enableTime: false,
                            dateFormat: "Y/m/d",
                            minDate: "today",

                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                // Update minDate for the dropoff date picker
                                dropoffFlatpickr.set("minDate", dateStr);
                            }
                        });

                        // Initialize the dropoff date picker
                        var dropoffFlatpickr = $(".tf_dropoff_date").flatpickr({
                            enableTime: false,
                            dateFormat: "Y/m/d",
                            minDate: "today",

                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            }
                        });

                        // Initialize the pickup time picker
                        var pickupTimeFlatpickr = $(".tf_pickup_time").flatpickr({
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",

                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                // Update minDate for the dropoff date picker
                                dropoffTimeFlatpickr.set("minTime", dateStr);
                            }
                        });

                        var dropoffTimeFlatpickr = $(".tf_dropoff_time").flatpickr({
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                // Update minDate for the dropoff date picker
                                dropoffFlatpickr.set("minDate", dateStr);
                            }
                        });

                    });
                })(jQuery);
            </script>
        <?php }else{ ?>
        <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Pickup Location', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <input type="text" name="pickup-name" id="tf_pickup_location" class="" placeholder="<?php esc_html_e( 'Enter Pickup Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="pickup" class="tf-place-input">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Dropoff Location', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <input type="text" name="dropoff-name" id="tf_dropoff_location" class="" placeholder="<?php esc_html_e( 'Enter Dropoff Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="dropoff" class="tf-place-input">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Pickup date', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </div>
                                    <input type="text" name="pickup-date" class="tf_pickup_date" placeholder="<?php esc_html_e( 'Enter Pickup date', 'tourfic' ); ?>" value="">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Pickup time', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="fa-regular fa-clock"></i>
                                    </div>
                                    <input type="text" name="pickup-time" class="tf_pickup_time" placeholder="<?php esc_html_e( 'Enter Pickup Time', 'tourfic' ); ?>" value="">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Dropoff date', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </div>
                                    <input type="text" name="dropoff-date" class="tf_dropoff_date" placeholder="<?php esc_html_e( 'Enter Dropoff date', 'tourfic' ); ?>" value="">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Dropoff time', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="fa-regular fa-clock"></i>
                                    </div>
                                    <input type="text" name="dropoff-time" class="tf_dropoff_time" placeholder="<?php esc_html_e( 'Enter Dropoff Time', 'tourfic' ); ?>" value="">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_carrental" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( apply_filters("tf_car_search_form_submit_button_text", 'Search' ), 'tourfic' ); ?></button>
                </div>

            </div>

        </form>

        <script>
            (function ($) {
                $(document).ready(function () {

                    // flatpickr locale first day of Week
                    <?php Helper::tf_flatpickr_locale('root'); ?>

                    // Initialize the pickup date picker
                    var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
                        enableTime: false,
                        dateFormat: "Y/m/d",
                        minDate: "today",

                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            // Update minDate for the dropoff date picker
                            dropoffFlatpickr.set("minDate", dateStr);
                        }
                    });

                    // Initialize the dropoff date picker
                    var dropoffFlatpickr = $(".tf_dropoff_date").flatpickr({
                        enableTime: false,
                        dateFormat: "Y/m/d",
                        minDate: "today",

                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        }
                    });

                    // Initialize the pickup time picker
                    var pickupTimeFlatpickr = $(".tf_pickup_time").flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",

                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            // Update minDate for the dropoff date picker
                            dropoffTimeFlatpickr.set("minTime", dateStr);
                        }
                    });

                    var dropoffTimeFlatpickr = $(".tf_dropoff_time").flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                        // flatpickr locale
                        <?php Helper::tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            // Update minDate for the dropoff date picker
                            dropoffFlatpickr.set("minDate", dateStr);
                        }
                    });

                });
            })(jQuery);

        </script>
        <?php
        }
    }


}