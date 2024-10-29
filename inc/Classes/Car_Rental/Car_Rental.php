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
        <!-- <form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
            <div class="tf_hotel_searching">
                <div class="tf_form_innerbody">
                    <div class="tf_form_fields">
                        <div class="tf_destination_fields">
                            <label class="tf_label_location">
                                <span class="tf-label"><?php esc_html_e( 'Destination', 'tourfic' ); ?></span>
                                <div class="tf_form_inners tf_form-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z"
                                                fill="#FAEEDD"/>
                                    </svg>
                                    <input type="text" name="place-name" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="tf-destination" class=""
                                            placeholder="<?php esc_html_e( 'Enter Destination', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" id="tf-search-tour" class="tf-place-input"/>
                                </div>
                            </label>
                        </div>

                        <div class="tf_checkin_date">
                            <label class="tf_label_checkin tf_tour_check_in_out_date">
                                <span class="tf-label"><?php esc_html_e( 'Start Date', 'tourfic' ); ?></span>
                                <div class="tf_form_inners">
                                    <div class="tf_checkin_dates">
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
                                </div>
                            </label>

                            <input type="text" name="check-in-out-date" class="tf-tour-check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                    placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_tour_search' ) ? 'required' : ''; ?>>
                        </div>

                        <div class="tf_checkin_date tf_tour_check_in_out_date">
                            <label class="tf_label_checkin">
                                <span class="tf-label"><?php esc_html_e( 'End Date', 'tourfic' ); ?></span>
                                <div class="tf_form_inners">
                                    <div class="tf_checkout_dates">
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
                                </div>
                            </label>
                        </div>

                        <div class="tf_guest_info tf_selectperson-wrap">
                            <label class="tf_label_checkin tf_input-inner">
                                <span class="tf-label"><?php esc_html_e( 'Guests', 'tourfic' ); ?></span>
                                <div class="tf_form_inners">
                                    <div class="tf_guest_calculation">
                                        <div class="tf_guest_number">
                                            <span class="guest"><?php esc_html_e( '1', 'tourfic' ); ?></span>
                                            <span class="label"><?php esc_html_e( 'Guests', 'tourfic' ); ?></span>
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
                                        <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
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
                                            <input type="tel" class="adults-style2" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1'; ?>" readonly>
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
                                    <?php
                                    if ( empty( $disable_child_search ) ) {
                                        ?>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
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
                                                <input type="tel" name="children" class="childs-style2" id="children" min="0" value="0" readonly>
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
                                    <?php }
                                    if ( empty( $disable_infant_search ) ) {
                                        ?>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
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
                                                <input type="tel" name="infant" class="infant-style2" id="infant" min="0" value="0" readonly>
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
                    <div class="tf_availability_checker_box">
                        <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
                        <?php
                        if ( $author ) { ?>
                            <input type="hidden" name="tf-author" value="<?php echo esc_attr( $author ); ?>" class="tf-post-type"/>
                        <?php } ?>
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

                    $(".tf_tour_check_in_out_date").on("click", function () {
                        $(".tf-tour-check-in-out-date").trigger("click");
                    });
                    $(".tf-tour-check-in-out-date").flatpickr({
                        enableTime: false,
                        mode: "range",
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
                        if (selectedDates.length === 2) {
                            const monthNames = [
                                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                            ];
                            if (selectedDates[0]) {
                                const startDate = selectedDates[0];
                                $(".tf_tour_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
                                $(".tf_tour_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
                            }
                            if (selectedDates[1]) {
                                const endDate = selectedDates[1];
                                $(".tf_tour_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
                                $(".tf_tour_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
                            }
                        }
                    }

                });
            })(jQuery);
        </script> -->

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