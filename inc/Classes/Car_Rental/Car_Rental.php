<?php

namespace Tourfic\Classes\Car_Rental;

defined('ABSPATH') || exit;

use ParagonIE\Sodium\Core\Curve25519\H;
use Tourfic\Classes\Helper;
use Tourfic\App\TF_Review;

class Car_Rental
{
    use \Tourfic\Traits\Singleton;

    public function __construct()
    {
        add_action('wp_after_insert_post', array($this, 'tf_car_assign_inline_taxonomies'), 100, 3);

        // Car CPT
        Car_Rental_CPT::instance();
    }


    function tf_car_assign_inline_taxonomies($post_id, $post, $old_status)
    {
        if ('tf_carrental' !== $post->post_type) {
            return;
        }
        $meta = get_post_meta($post_id, 'tf_carrental_opt', true);
        if (isset($meta['brands']) && ! empty($meta['brands'])) {
            $car_brands = array();
            $car_brands[] = intval($meta['brands']);
            wp_set_object_terms($post_id, $car_brands, 'carrental_brand');
        }
        if (isset($meta['fuel_types']) && ! empty($meta['fuel_types'])) {
            $car_fuel_types = array();
            $car_fuel_types[] = intval($meta['fuel_types']);
            wp_set_object_terms($post_id, $car_fuel_types, 'carrental_fuel_type');
        }
        if (isset($meta['engine_year']) && ! empty($meta['engine_year'])) {
            $car_engine_year = array();
            $car_engine_year[] = intval($meta['engine_year']);
            wp_set_object_terms($post_id, $car_engine_year, 'carrental_engine_year');
        }
    }

    /**
     * Car Search form
     * @author Jahid
     */

    static function tf_car_search_form_horizontal($classes, $title, $subtitle, $advanced, $design)
    {
        
        // Check-in & out date
        $check_in_out = ! empty($_GET['check-in-out-date']) ? sanitize_text_field( wp_unslash( $_GET['check-in-out-date'] ) ) : '';

        // date format for apartments
        $date_format_change_apartments = ! empty(Helper::tfopt("tf-date-format-for-users")) ? Helper::tfopt("tf-date-format-for-users") : "Y/m/d";

        $disable_apartment_child_search  = ! empty(Helper::tfopt('disable_apartment_child_search')) ? Helper::tfopt('disable_apartment_child_search') : '';
        $disable_apartment_infant_search  = ! empty(Helper::tfopt('disable_apartment_infant_search')) ? Helper::tfopt('disable_apartment_infant_search') : '';


        // Pull options from settings or set fallback values
        $disable_car_time_slot = !empty(Helper::tfopt('disable-car-time-slots')) ? boolval(Helper::tfopt('disable-car-time-slots')) : false;
        $time_interval = 30;
        $start_time_str = '00:00';
        $end_time_str   = '23:30';
        $default_time_str = '10:00';
        if($disable_car_time_slot){
            $time_interval = !empty(Helper::tfopt('car_time_interval')) ? intval(Helper::tfopt('car_time_interval')) : 30;
            $start_time_str = !empty(Helper::tfopt('car_start_time')) ? Helper::tfopt('car_start_time') : '00:00'; 
            $end_time_str   = !empty(Helper::tfopt('car_end_time')) ? Helper::tfopt('car_end_time') : '23:30';
        }

        if ( strtotime($start_time_str) >= strtotime('10:00') ) {
            $default_time_str = $start_time_str;
        }

        // Convert string times to timestamps
        $start_time = strtotime($start_time_str);
        $end_time   = strtotime($end_time_str);
        $default_time = gmdate('g:i A', strtotime($default_time_str));

        // Use selected time from GET or fall back to default
        $selected_pickup_time = $default_time;
        $selected_dropoff_time = $default_time;

        $car_driver_min_age = ! empty(Helper::tf_data_types(Helper::tfopt('tf-template'))['car_archive_driver_min_age']) ? Helper::tf_data_types(Helper::tfopt('tf-template'))['car_archive_driver_min_age'] : 18;
        $car_driver_max_age = ! empty(Helper::tf_data_types(Helper::tfopt('tf-template'))['car_archive_driver_max_age']) ? Helper::tf_data_types(Helper::tfopt('tf-template'))['car_archive_driver_max_age'] : 40;

        if (!empty($design) && 2 == $design) {
?>
            <form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <div class="tf_hotel_searching">
                    <div class="tf_form_innerbody">
                        <div class="tf_form_fields">
                            <div class="tf_destination_fields">
                                <label class="tf_label_location">
                                    <span class="tf-label"><?php esc_html_e('Pick-up', 'tourfic'); ?></span>
                                    <div class="tf_form_inners tf_form-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z"
                                                fill="#FAEEDD" />
                                        </svg>
                                        <input type="text" name="pickup-name" id="tf_pickup_location" class="" placeholder="<?php esc_html_e('Enter Pickup Location', 'tourfic'); ?>" value="">
                                        <input type="hidden" name="pickup" class="tf-place-input">
                                    </div>
                                </label>
                            </div>

                            <div class="tf_destination_fields">
                                <label class="tf_label_location">
                                    <span class="tf-label"><?php esc_html_e('Drop-off', 'tourfic'); ?></span>
                                    <div class="tf_form_inners tf_form-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z"
                                                fill="#FAEEDD" />
                                        </svg>
                                        <input type="text" name="dropoff-name" id="tf_dropoff_location" class="" placeholder="<?php esc_html_e('Enter Dropoff Location', 'tourfic'); ?>" value="">
                                        <input type="hidden" name="dropoff" class="tf-place-input">
                                    </div>
                                </label>
                            </div>

                            <div class="tf_checkin_date">
                                <div class="tf_label_checkin">
                                    <span class="tf-label"><?php esc_html_e('Pick-up date & Time', 'tourfic'); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkin_dates">
                                            <div class="tf-select-date tf-car-search-pickup-date">
                                                <span class="date"><?php echo esc_html( gmdate('d', strtotime('+1 day')) ); ?></span>
                                                <span class="month">
                                                    <span><?php echo esc_html( gmdate('M', strtotime('+1 day')) ); ?></span>
                                                    <div class="tf_check_arrow">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4" />
                                                        </svg>
                                                    </div>
                                                </span>
                                            </div>
                                            <input type="hidden" name="pickup-date" class="tf_search_pickup_date" placeholder="<?php esc_html_e('Enter Pickup date', 'tourfic'); ?>" value="<?php echo esc_attr(gmdate('Y/m/d', strtotime('+1 day'))); ?>">
                                        </div>

                                        <div class="tf_checkin_dates info-select">
                                            <div class="selected-pickup-time">
                                                <div class="text">
                                                    <?php echo esc_html($selected_pickup_time); ?>
                                                </div>
                                                <div class="icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <input type="hidden" name="pickup-time" class="tf_pickup_time" id="tf_pickup_time" value="<?php echo esc_attr($selected_pickup_time); ?>">
                                            <div class="tf-select-time">
                                                <ul class="time-options-list tf-pickup-time">
                                                    <?php
                                                        for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                            $time_label = gmdate("g:i A", $time);
                                                            $selected = ($selected_pickup_time === $time_label) ? 'selected' : '';
                                                            echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="tf_checkin_date">
                                <div class="tf_label_checkin">
                                    <span class="tf-label"><?php esc_html_e('Drop-off date & Time', 'tourfic'); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkin_dates">
                                            <div class="tf-select-date tf-car-search-dropoff-date">
                                                <span class="date"><?php echo esc_html( gmdate('d', strtotime('+2 day')) ); ?></span>
                                                <span class="month">
                                                    <span><?php echo esc_html( gmdate('M', strtotime('+2 day')) ); ?></span>
                                                    <div class="tf_check_arrow">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4" />
                                                        </svg>
                                                    </div>
                                                </span>
                                            </div>
                                            <input type="hidden" name="dropoff-date" class="tf_search_dropoff_date" placeholder="<?php esc_html_e('Enter Drop-off date', 'tourfic'); ?>" value="<?php echo esc_attr( gmdate('Y/m/d', strtotime('+2 day')) ); ?>">
                                        </div>

                                        <div class="tf_checkin_dates info-select">
                                            <div class="selected-dropoff-time">
                                                <div class="text">
                                                    <?php echo esc_html($selected_dropoff_time); ?>
                                                </div>
                                                <div class="icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <input type="hidden" name="dropoff-time" class="tf_dropoff_time" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
                                            <div class="tf-select-time">
                                                <ul class="time-options-list tf-dropoff-time">
                                                    <?php
                                                        for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                            $time_label = gmdate("g:i A", $time);
                                                            $selected = ($selected_dropoff_time === $time_label) ? 'selected' : '';
                                                            echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tf_availability_checker_box">
                            <input type="hidden" name="type" value="tf_carrental" class="tf-post-type" />
                            <button class="tf_btn"><?php echo esc_html_e("Check availability", "tourfic"); ?></button>
                        </div>
                    </div>
                </div>

            </form>
            <script>
                (function($) {
                    $(document).ready(function() {

                        // flatpickr locale first day of Week
                        <?php Helper::tf_flatpickr_locale("root"); ?>

                        $(".tf-car-search-dropoff-date").on("click", function() {
                            $(".tf-car-search-pickup-date").trigger("click");
                        });
                        var pickupFlatpickr = $(".tf-car-search-pickup-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",
                            showMonths: $(window).width() >= 1240 ? 2 : 1,

                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },

                            onChange: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },
                            <?php if(! empty( $check_in_out )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                            <?php } ?>
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
                                $('.tf_search_pickup_date').val(flatpickr.formatDate(startDate, "Y/m/d"));
                            }
                            if (selectedDates[1]) {
                                const endDate = selectedDates[1];
                                $(".tf-car-search-dropoff-date span.date").html(endDate.getDate());
                                $(".tf-car-search-dropoff-date span.month span").html(monthNames[endDate.getMonth()]);
                                $(".tf_search_dropoff_date").val(flatpickr.formatDate(endDate, "Y/m/d"));
                            }
                        }

                    });
                })(jQuery);
            </script>
        <?php } elseif (!empty($design) && 3 == $design) { ?>
            <form class="tf-archive-search-box-wrapper <?php echo esc_attr($classes); ?>" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <div class="tf-date-select-box tf-flex tf-flex-gap-8 tf-date-selection-form">
                    <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn tf-pick-drop-location active">
                        <div class="tf-select-date">
                            <div class="tf-flex tf-flex-gap-4">
                                <div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3711)">
                                            <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3711">
                                                <rect width="20" height="20" fill="white" />
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
                                            <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3711">
                                                <rect width="20" height="20" fill="white" />
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
                                        <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="info-select">
                                    <h5><?php esc_html_e("Pick-up date", "tourfic"); ?></h5>
                                    <input type="text" name="pickup-date" class="tf_pickup_date" placeholder="<?php esc_html_e('Pickup date', 'tourfic'); ?>" value="<?php echo esc_attr(gmdate('Y/m/d', strtotime('+1 day'))); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="tf-select-date">
                            <div class="tf-flex tf-flex-gap-4">
                                <div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3728)">
                                            <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3728">
                                                <rect width="20" height="20" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="info-select">
                                    <h5><?php esc_html_e("Time", "tourfic"); ?></h5>
                                    <div class="selected-pickup-time">
                                        <div class="text">
                                            <?php echo esc_html($selected_pickup_time); ?>
                                        </div>
                                        <div class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pickup-time" class="tf_pickup_time" id="tf_pickup_time" value="<?php echo esc_attr($selected_pickup_time); ?>">
                                    <div class="tf-select-time">
                                        <ul class="time-options-list tf-pickup-time">
                                            <?php
                                                for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                    $time_label = gmdate("g:i A", $time);
                                                    $selected = ($selected_pickup_time === $time_label) ? 'selected' : '';
                                                    echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                        <div class="tf-select-date">
                            <div class="tf-flex tf-flex-gap-4">
                                <div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="info-select">
                                    <h5><?php esc_html_e("Drop-off date", "tourfic"); ?></h5>
                                    <input type="text" placeholder="Drop Off Date" name="dropoff-date" class="tf_dropoff_date" value="<?php echo esc_attr(gmdate('Y-m-d', strtotime('+2 day'))); ?>" readonly='' />
                                </div>
                            </div>
                        </div>

                        <div class="tf-select-date">
                            <div class="tf-flex tf-flex-gap-4">
                                <div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3728)">
                                            <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3728">
                                                <rect width="20" height="20" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="info-select">
                                    <h5><?php esc_html_e("Time", "tourfic"); ?></h5>
                                    <div class="selected-dropoff-time">
                                        <div class="text">
                                            <?php echo esc_html($selected_dropoff_time); ?>
                                        </div>
                                        <div class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <input type="hidden" name="dropoff-time" class="tf_dropoff_time" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
                                    <div class="tf-select-time">
                                        <ul class="time-options-list tf-dropoff-time">
                                            <?php
                                                for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                    $time_label = gmdate("g:i A", $time);
                                                    $selected = ($selected_dropoff_time === $time_label) ? 'selected' : '';
                                                    echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf-driver-location-box tf-flex tf-flex-space-bttn tf-flex-align-center">
                    <div class="tf-driver-location">
                        <ul>
                            <li>
                                <label><?php esc_html_e("Return in the same location", "tourfic"); ?>
                                    <input type="checkbox" name="same_location" checked>
                                    <span class="tf-checkmark"></span>
                                </label>
                            </li>
                            <li>
                                <label><?php esc_html_e("Age of driver ", "tourfic"); ?>
                                    <?php echo esc_attr($car_driver_min_age); ?>-<?php echo esc_attr($car_driver_max_age); ?>?
                                    <input type="checkbox" name="driver_age" checked>
                                    <span class="tf-checkmark"></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                    <div class="tf-submit-button">
                        <input type="hidden" name="type" value="tf_carrental" class="tf-post-type" />
                        <button type="submit" class="tf_btn tf-flex-align-center"><?php echo esc_html(apply_filters("tf_car_search_form_submit_button_text", 'Search')); ?> <i class="ri-search-line"></i></button>
                    </div>
                </div>
            </form>

            <script>
                (function($) {
                    $(document).ready(function() {

                        // flatpickr locale first day of Week
                        <?php Helper::tf_flatpickr_locale('root'); ?>

                        $(".tf_dropoff_date").on("click", function () {
                            $(".tf_pickup_date").trigger("click");
                        });
                        $(".tf_pickup_date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",
                            showMonths: $(window).width() >= 1240 ? 2 : 1,
                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },

                            onChange: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },
                            <?php if(! empty( $check_in_out )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                            <?php } ?>
                        });

                        function dateSetToFields(selectedDates, instance) {
                            if (selectedDates.length === 2) {
                                if (selectedDates[0]) {
                                    const startDate = flatpickr.formatDate(selectedDates[0], "Y/m/d");
                                    $(".tf_pickup_date").val(startDate);
                                }
                                if (selectedDates[1]) {
                                    const endDate = flatpickr.formatDate(selectedDates[1], "Y/m/d");
                                    $(".tf-select-date .tf_dropoff_date").val(endDate);
                                }
                            }
                        }

                    });
                })(jQuery);
            </script>
        <?php } elseif (!empty($design) && 4 == $design) { ?>
            <form class="tf-archive-search-box-wrapper tf-search__form tf-shortcode-design-4 <?php echo esc_attr($classes); ?>" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <fieldset class="tf-search__form__fieldset tf-search__form__car__fieldset">
                    <!-- Pickup -->
                    <div class="tf-search__form__fieldset__left tf-pick-drop-location active">
                        <div class="tf_pickup_location">
                            <label for="tf-search__form-pickup" class="tf-search__form__label">
                                <?php echo esc_html_e("Pick-up", "tourfic"); ?>
                            </label>
                            <div class="tf-search__form__field">
                                <input type="text" id="tf_pickup_location" class="tf-search__form__input" name="pickup-name" placeholder="<?php esc_html_e('Pickup Location', 'tourfic'); ?>" />
                                <input type="hidden" name="pickup" class="tf-place-input">
                                <span class="tf-search__form__field__icon icon--location">
                                    <svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.25 15.625C3.625 13.5938 0 8.75 0 6C0 2.6875 2.65625 0 6 0C9.3125 0 12 2.6875 12 6C12 8.75 8.34375 13.5938 6.71875 15.625C6.34375 16.0938 5.625 16.0938 5.25 15.625ZM6 8C7.09375 8 8 7.125 8 6C8 4.90625 7.09375 4 6 4C4.875 4 4 4.90625 4 6C4 7.125 4.875 8 6 8Z" fill="white" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="tf_dropoff_location">
                            <label for="tf-search__form-pickup" class="tf-search__form__label">
                                <?php echo esc_html_e("Drop-off", "tourfic"); ?>
                            </label>
                            <div class="tf-search__form__field">
                                <input type="text" id="tf_dropoff_location" class="tf-search__form__input" name="dropoff-name" placeholder="<?php esc_html_e('Dropoff Location', 'tourfic'); ?>" />
                                <input type="hidden" name="dropoff" class="tf-place-input">
                                <span class="tf-search__form__field__icon icon--location">
                                    <svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.25 15.625C3.625 13.5938 0 8.75 0 6C0 2.6875 2.65625 0 6 0C9.3125 0 12 2.6875 12 6C12 8.75 8.34375 13.5938 6.71875 15.625C6.34375 16.0938 5.625 16.0938 5.25 15.625ZM6 8C7.09375 8 8 7.125 8 6C8 4.90625 7.09375 4 6 4C4.875 4 4 4.90625 4 6C4 7.125 4.875 8 6 8Z" fill="white" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="tf-search__form__fieldset__middle">
                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>

                        <!-- Pickup Date -->
                        <div class="tf-search__form__group tf_car_date_time_picker">
                            <label class="tf-search__form__label">
                                <?php echo esc_html_e('Pick-up date & Time', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field">
                                <div class="tf_pickup_date tf-flex tf-flex-align-center">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                            <g clip-path="url(#clip0_2862_2140)">
                                                <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2862_2140">
                                                    <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="tf_pickup_dates tf-flex tf-flex-align-center">
                                        <span class="date field--title"><?php echo esc_html(gmdate('d', strtotime('+1 day'))); ?></span>
                                        <div class="tf-search__form__field__mthyr">
                                            <span class="month form--span"><?php echo esc_html(gmdate('M', strtotime('+1 day'))); ?></span>
                                            <span class="year form--span"><?php echo esc_html(gmdate('Y', strtotime('+1 day'))); ?></span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pickup-date" class="tf_pickup_date_input tf-check-inout-hidden" value="<?php echo esc_attr(gmdate('Y/m/d', strtotime('+1 day'))); ?>">
                                </div>
                                <div class="tf-time-picker info-select">
                                    <div class="tf-time-head selected-pickup-time">
                                        <span class="tf-dropoff-time-set">
                                            <span class="tf-time text"><?php echo esc_html($selected_pickup_time); ?></span>
                                            <!-- <span class="tf-time-meridiem">am</span> -->
                                        </span>
                                        <div class="tf-down-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                <path d="M9.51709 16.3828C10.0054 16.8711 10.7983 16.8711 11.2866 16.3828L18.7866 8.88281C19.2749 8.39453 19.2749 7.60156 18.7866 7.11328C18.2983 6.625 17.5054 6.625 17.0171 7.11328L10.3999 13.7305L3.78271 7.11719C3.29443 6.62891 2.50146 6.62891 2.01318 7.11719C1.5249 7.60547 1.5249 8.39844 2.01318 8.88672L9.51318 16.3867L9.51709 16.3828Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pickup-time" class="tf_pickup_time tf_pickup_time_field" id="tf_pickup_time" value="<?php echo esc_attr($selected_pickup_time); ?>">
                                    <div class="tf-select-time">
                                        <ul class="time-options-list tf-pickup-time">
                                            <?php
                                                for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                    $time_label = gmdate("g:i A", $time);
                                                    $selected = ($selected_pickup_time === $time_label) ? 'selected' : '';
                                                    echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>
                        <!-- Check-out -->
                        <div class="tf-search__form__group tf_car_date_time_picker">
                            <label class="tf-search__form__label">
                                <?php echo esc_html_e('Drop-off date & Time', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field">
                                <div class="tf_dropoff_date tf-flex tf-flex-align-center">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                            <g clip-path="url(#clip0_2862_2140)">
                                                <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2862_2140">
                                                    <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="tf_dropoff_dates tf-flex tf-flex-align-center">
                                        <span class="date field--title"><?php echo esc_html(gmdate('d', strtotime('+2 day'))); ?></span>
                                        <div class="tf-search__form__field__mthyr">
                                            <span class="month form--span"><?php echo esc_html(gmdate('M', strtotime('+2 day'))); ?></span>
                                            <span class="year form--span"><?php echo esc_html(gmdate('Y', strtotime('+2 day'))); ?></span>
                                        </div>
                                    </div>
                                    <input type="hidden" placeholder="Drop Off Date" name="dropoff-date" class="tf_dropoff_date_input tf-check-inout-hidden" />
                                </div>
                                <div class="tf-time-picker info-select">
                                    <div class="tf-time-head selected-dropoff-time">
                                        <span class="tf-dropoff-time-set">
                                            <span class="tf-time text"><?php echo esc_html($selected_dropoff_time); ?></span>
                                            <!-- <span class="tf-time-meridiem">am</span> -->
                                        </span>
                                        <div class="tf-down-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                <path d="M9.51709 16.3828C10.0054 16.8711 10.7983 16.8711 11.2866 16.3828L18.7866 8.88281C19.2749 8.39453 19.2749 7.60156 18.7866 7.11328C18.2983 6.625 17.5054 6.625 17.0171 7.11328L10.3999 13.7305L3.78271 7.11719C3.29443 6.62891 2.50146 6.62891 2.01318 7.11719C1.5249 7.60547 1.5249 8.39844 2.01318 8.88672L9.51318 16.3867L9.51709 16.3828Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>
                                    <input type="hidden" name="dropoff-time" class="tf_dropoff_time tf_dropoff_time_field" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
                                    <div class="tf-select-time">
                                        <ul class="time-options-list tf-dropoff-time">
                                            <?php
                                                for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                    $time_label = gmdate("g:i A", $time);
                                                    $selected = ($selected_dropoff_time === $time_label) ? 'selected' : '';
                                                    echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Driver Location -->
                    <div class="tf-driver-location tf-mobile-location">
                        <ul>
                            <li>
                                <label><?php esc_html_e("Return in the same location", "tourfic"); ?>
                                    <input type="checkbox" name="same_location" checked>
                                    <span class="tf-checkmark"></span>
                                </label>
                            </li>
                            <li>
                                <label><?php esc_html_e("Age of driver ", "tourfic"); ?>
                                    <?php echo esc_attr($car_driver_min_age); ?>-<?php echo esc_attr($car_driver_max_age); ?>?
                                    <input type="checkbox" name="driver_age" checked>
                                    <span class="tf-checkmark"></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                    <div class="tf-search__form__fieldset__right">
                        <!-- Submit Button -->
                        <input type="hidden" name="type" value="tf_carrental" class="tf-post-type" />
                        <button type="submit" class="tf-search__form__submit tf_btn">
                            <?php echo esc_html(apply_filters("tf_car_search_form_submit_button_text", 'Search')); ?>
                            <svg class="tf-search__form__submit__icon" width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                </fieldset>
                <!-- Driver Location -->
                <div class="tf-driver-location tf-desktop-location">
                    <ul>
                        <li>
                            <label><?php esc_html_e("Return in the same location", "tourfic"); ?>
                                <input type="checkbox" name="same_location" checked>
                                <span class="tf-checkmark"></span>
                            </label>
                        </li>
                        <li>
                            <label><?php esc_html_e("Age of driver ", "tourfic"); ?>
                                <?php echo esc_attr($car_driver_min_age); ?>-<?php echo esc_attr($car_driver_max_age); ?>?
                                <input type="checkbox" name="driver_age" checked>
                                <span class="tf-checkmark"></span>
                            </label>
                        </li>
                    </ul>
                </div>
            </form>
            <script>
                (function($) {
                    $(document).ready(function() {
                        // flatpickr locale first day of Week
                        <?php Helper::tf_flatpickr_locale("root"); ?>

                        $(".tf_dropoff_date").on("click", function() {
                            $(".tf_pickup_date").trigger("click");
                        });

                        var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",
                            showMonths: $(window).width() >= 1240 ? 2 : 1,

                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },

                            onChange: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },
                            <?php if(! empty( $check_in_out )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                            <?php } ?>
                        });

                        function dateSetToFields(selectedDates, instance) {
                            const monthNames = [
                                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                            ];
                            if (selectedDates[0]) {
                                const startDate = selectedDates[0];
                                $(".tf_pickup_date .date").html(startDate.getDate());
                                $(".tf_pickup_date .month").html(monthNames[startDate.getMonth()]);
                                $(".tf_pickup_date .year").html(startDate.getFullYear());
                                $(".tf_pickup_date_input").val(flatpickr.formatDate(startDate, "Y/m/d"));
                            }
                            if (selectedDates[1]) {
                                const endDate = selectedDates[1];
                                $(".tf_dropoff_date .date").html(endDate.getDate());
                                $(".tf_dropoff_date .month").html(monthNames[endDate.getMonth()]);
                                $(".tf_dropoff_date .year").html(endDate.getFullYear());
                                $(".tf_dropoff_date_input").val(flatpickr.formatDate(endDate, "Y/m/d"));
                            }
                        }
                    });
                })(jQuery);
            </script>

        <?php } else { ?>
            <form class="tf_booking-widget <?php echo esc_attr($classes); ?>" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <div class="tf_homepage-booking">
                    <div class="tf_destination-wrap">
                        <div class="tf_input-inner">
                            <div class="tf_form-row">
                                <label class="tf_label-row">
                                    <span class="tf-label"><?php esc_html_e('Pickup Location', 'tourfic'); ?>:</span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <input type="text" name="pickup-name" id="tf_pickup_location" class="" placeholder="<?php esc_html_e('Enter Pickup Location', 'tourfic'); ?>" value="">
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
                                    <span class="tf-label"><?php esc_html_e('Dropoff Location', 'tourfic'); ?>:</span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <input type="text" name="dropoff-name" id="tf_dropoff_location" class="" placeholder="<?php esc_html_e('Enter Dropoff Location', 'tourfic'); ?>" value="">
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
                                    <span class="tf-label"><?php esc_html_e('Pickup date', 'tourfic'); ?>:</span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="fa-solid fa-calendar-days"></i>
                                        </div>
                                        <input type="text" name="pickup-date" class="tf_pickup_date" placeholder="<?php esc_html_e('Enter Pickup date', 'tourfic'); ?>" value="<?php echo esc_attr(gmdate('Y/m/d', strtotime('+1 day'))); ?>">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="tf_destination-wrap">
                        <div class="tf_input-inner">
                            <div class="tf_form-row">
                                <label class="tf_label-row info-select">
                                    <span class="tf-label"><?php esc_html_e('Pickup time', 'tourfic'); ?>:</span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                        <div class="selected-pickup-time">
                                            <div class="text">
                                                <?php echo esc_html($selected_pickup_time); ?>
                                            </div>
                                            <div class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" name="pickup-time" class="tf_pickup_time" id="tf_pickup_time" value="<?php echo esc_attr($selected_pickup_time); ?>">
                                        <div class="tf-select-time">
                                            <ul class="time-options-list tf-pickup-time">
                                                <?php
                                                    for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                        $time_label = gmdate("g:i A", $time);
                                                        $selected = ($selected_pickup_time === $time_label) ? 'selected' : '';
                                                        echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                    }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="tf_destination-wrap">
                        <div class="tf_input-inner">
                            <div class="tf_form-row">
                                <label class="tf_label-row">
                                    <span class="tf-label"><?php esc_html_e('Dropoff date', 'tourfic'); ?>:</span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="fa-solid fa-calendar-days"></i>
                                        </div>
                                        <input type="text" name="dropoff-date" class="tf_dropoff_date" placeholder="<?php esc_html_e('Enter Dropoff date', 'tourfic'); ?>" value="<?php echo esc_attr(gmdate('Y/m/d', strtotime('+2 day'))); ?>">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="tf_destination-wrap">
                        <div class="tf_input-inner">
                            <div class="tf_form-row">
                                <label class="tf_label-row info-select">
                                    <span class="tf-label"><?php esc_html_e('Dropoff time', 'tourfic'); ?>:</span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                        <div class="selected-dropoff-time">
                                            <div class="text">
                                                <?php echo esc_html($selected_dropoff_time); ?>
                                            </div>
                                            <div class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" name="dropoff-time" class="tf_dropoff_time" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
                                        <div class="tf-select-time">
                                            <ul class="time-options-list tf-dropoff-time">
                                                <?php
                                                    for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                        $time_label = gmdate("g:i A", $time);
                                                        $selected = ($selected_dropoff_time === $time_label) ? 'selected' : '';
                                                        echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                    }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="tf_submit-wrap">
                        <input type="hidden" name="type" value="tf_carrental" class="tf-post-type" />
                        <button class="tf_btn tf-submit" type="submit"><?php echo esc_html(apply_filters("tf_car_search_form_submit_button_text", 'Search')); ?></button>
                    </div>

                </div>

            </form>

            <script>
                (function($) {
                    $(document).ready(function() {

                        // flatpickr locale first day of Week
                        <?php Helper::tf_flatpickr_locale('root'); ?>

                        $(".tf_dropoff_date").on("click", function () {
                            $(".tf_pickup_date").trigger("click");
                        });
                        // Initialize the pickup date picker
                        var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",
                            showMonths: $(window).width() >= 1240 ? 2 : 1,

                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },

                            onChange: function (selectedDates, dateStr, instance) {
                                dateSetToFields(selectedDates, instance);
                            },
                            <?php if(! empty( $check_in_out )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                            <?php } ?>
                        });

                        function dateSetToFields(selectedDates, instance) {
                            if (selectedDates.length === 2) {
                                if (selectedDates[0]) {
                                    const startDate = flatpickr.formatDate(selectedDates[0], "Y/m/d");
                                    $("#tf-car-booking-form .tf_pickup_date").val(startDate);
                                }
                                if (selectedDates[1]) {
                                    const endDate = flatpickr.formatDate(selectedDates[1], "Y/m/d");
                                    $("#tf-car-booking-form .tf_dropoff_date").val(endDate);
                                }
                            }
                        }

                    
                       

                    });
                })(jQuery);
            </script>
<?php
        }
    }
}
