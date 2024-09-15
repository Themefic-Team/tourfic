<?php

namespace Tourfic\Classes\Car_Rental;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\App\TF_Review;

class Car_Rental {
	use \Tourfic\Traits\Singleton;

	public function __construct() {

        /**
         * WooCommerce Car Functions
         *
         * @include
         */
        if ( Helper::tf_is_woo_active() ) {
            if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-car.php' ) ) {
                require_once TF_INC_PATH . 'functions/woocommerce/wc-car.php';
            } else {
                tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-car.php' );
            }
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
        
        ?>
        
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