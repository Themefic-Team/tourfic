<?php

namespace Tourfic\Classes\Car_Rental;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\App\TF_Review;

class Car_Rental {
	use \Tourfic\Traits\Singleton;

	public function __construct() {

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
                                    <div class="tf_checkin_dates tf-car-search-pickup-date">
                                        <input type="text" name="pickup-date" class="tf_search_pickup_date" placeholder="<?php esc_html_e( 'Enter Pickup date', 'tourfic' ); ?>" value="">
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
                                    <div class="tf_checkin_dates tf-car-pickup-time">
                                        <input type="text" name="pickup-time" class="tf_search_pickup_time" placeholder="<?php esc_html_e( 'Enter Pickup Time', 'tourfic' ); ?>" value="">
                                        <span class="date"><?php echo esc_html( gmdate( 'h:m' ) ); ?></span>
                                        <span class="month">
                                        <div class="tf_check_arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                            </svg>
                                        </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tf_checkin_date">
                            <div class="tf_label_checkin">
                                <span class="tf-label"><?php esc_html_e( 'Drop-off date & Time', 'tourfic' ); ?></span>
                                <div class="tf_form_inners">
                                    <div class="tf_checkin_dates tf-car-search-pickup-date">
                                        <input type="text" name="pickup-date" class="tf_search_pickup_date" placeholder="<?php esc_html_e( 'Enter Pickup date', 'tourfic' ); ?>" value="">
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
                                    <div class="tf_checkin_dates tf-car-pickup-time">
                                        <input type="text" name="pickup-time" class="tf_search_pickup_time" placeholder="<?php esc_html_e( 'Enter Pickup Time', 'tourfic' ); ?>" value="">
                                        <span class="date"><?php echo esc_html( gmdate( 'h:m' ) ); ?></span>
                                        <span class="month">
                                        <div class="tf_check_arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                            </svg>
                                        </div>
                                        </span>
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
                            dateSetToFields(selectedDates, instance);
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            dateSetToFields(selectedDates, instance);
                        },
                    });

                    function dateSetToFields(selectedDates, instance) {
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
                            // Update minDate for the dropoff date picker
                            // dropoffTimeFlatpickr.set("minTime", dateStr);
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