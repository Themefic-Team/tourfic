<?php 
use \Tourfic\Classes\Helper;
$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['_archive_view'] : 'grid';
?>
<div class="tf-archive-car-section">
    <div class="tf-archive-car-banner">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1><?php esc_html_e("Search results", "tourfic"); ?></h1>
        </div>
    </div>

    <div class="tf-car-template-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">

                <div class="tf-archive-search-box">
                    <div class="tf-archive-search-box-wrapper">
                        <div class="tf-date-select-box tf-flex tf-flex-gap-8">
                            <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                                <div class="tf-select-date">
                                    <div class="tf-flex tf-flex-gap-4">
                                        <div class="icon">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 17.4161L14.1247 13.2913C16.4028 11.0133 16.4028 7.31977 14.1247 5.04171C11.8467 2.76365 8.15327 2.76365 5.87521 5.04171C3.59715 7.31977 3.59715 11.0133 5.87521 13.2913L10 17.4161ZM10 19.7731L4.6967 14.4698C1.76777 11.5408 1.76777 6.79214 4.6967 3.8632C7.62563 0.934271 12.3743 0.934271 15.3033 3.8632C18.2322 6.79214 18.2322 11.5408 15.3033 14.4698L10 19.7731ZM10 10.8332C10.9205 10.8332 11.6667 10.087 11.6667 9.1665C11.6667 8.24603 10.9205 7.49984 10 7.49984C9.0795 7.49984 8.33333 8.24603 8.33333 9.1665C8.33333 10.087 9.0795 10.8332 10 10.8332ZM10 12.4998C8.15905 12.4998 6.66667 11.0074 6.66667 9.1665C6.66667 7.32555 8.15905 5.83317 10 5.83317C11.8409 5.83317 13.3333 7.32555 13.3333 9.1665C13.3333 11.0074 11.8409 12.4998 10 12.4998Z" fill="#566676"/>
                                            </svg>
                                        </div>
                                        <div class="info-select">
                                            <h5><?php esc_html_e("Pick-up", "tourfic"); ?></h5>
                                            <input type="text" placeholder="Pick Up Location" id="tf_pickup_location" />
                                            <input type="hidden" id="tf_pickup_location_id">
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-select-date">
                                    <div class="tf-flex tf-flex-gap-4">
                                        <div class="icon">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 17.4161L14.1247 13.2913C16.4028 11.0133 16.4028 7.31977 14.1247 5.04171C11.8467 2.76365 8.15327 2.76365 5.87521 5.04171C3.59715 7.31977 3.59715 11.0133 5.87521 13.2913L10 17.4161ZM10 19.7731L4.6967 14.4698C1.76777 11.5408 1.76777 6.79214 4.6967 3.8632C7.62563 0.934271 12.3743 0.934271 15.3033 3.8632C18.2322 6.79214 18.2322 11.5408 15.3033 14.4698L10 19.7731ZM10 10.8332C10.9205 10.8332 11.6667 10.087 11.6667 9.1665C11.6667 8.24603 10.9205 7.49984 10 7.49984C9.0795 7.49984 8.33333 8.24603 8.33333 9.1665C8.33333 10.087 9.0795 10.8332 10 10.8332ZM10 12.4998C8.15905 12.4998 6.66667 11.0074 6.66667 9.1665C6.66667 7.32555 8.15905 5.83317 10 5.83317C11.8409 5.83317 13.3333 7.32555 13.3333 9.1665C13.3333 11.0074 11.8409 12.4998 10 12.4998Z" fill="#566676"/>
                                            </svg>
                                        </div>
                                        <div class="info-select">
                                            <h5><?php esc_html_e("Drop-off", "tourfic"); ?></h5>
                                            <input type="text" placeholder="Drop Off Location" id="tf_dropoff_location" />
                                            <input type="hidden" id="tf_dropoff_location_id">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                                <div class="tf-select-date">
                                    <div class="tf-flex tf-flex-gap-4">
                                        <div class="icon">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.49996 0.833496V2.50016H12.5V0.833496H14.1666V2.50016H17.5C17.9602 2.50016 18.3333 2.87326 18.3333 3.3335V16.6668C18.3333 17.1271 17.9602 17.5002 17.5 17.5002H2.49996C2.03973 17.5002 1.66663 17.1271 1.66663 16.6668V3.3335C1.66663 2.87326 2.03973 2.50016 2.49996 2.50016H5.83329V0.833496H7.49996ZM16.6666 9.16683H3.33329V15.8335H16.6666V9.16683ZM6.66663 10.8335V12.5002H4.99996V10.8335H6.66663ZM10.8333 10.8335V12.5002H9.16662V10.8335H10.8333ZM15 10.8335V12.5002H13.3333V10.8335H15ZM5.83329 4.16683H3.33329V7.50016H16.6666V4.16683H14.1666V5.8335H12.5V4.16683H7.49996V5.8335H5.83329V4.16683Z" fill="#566676"/>
                                            </svg>
                                        </div>
                                        <div class="info-select">
                                            <h5><?php esc_html_e("Pick-up date", "tourfic"); ?></h5>
                                            <input type="text" placeholder="Pick Up Date" class="tf_pickup_date" />
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-select-date">
                                        <div class="tf-flex tf-flex-gap-4">
                                            <div class="icon">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.99996 18.3332C5.39758 18.3332 1.66663 14.6022 1.66663 9.99984C1.66663 5.39746 5.39758 1.6665 9.99996 1.6665C14.6023 1.6665 18.3333 5.39746 18.3333 9.99984C18.3333 14.6022 14.6023 18.3332 9.99996 18.3332ZM9.99996 16.6665C13.6819 16.6665 16.6666 13.6818 16.6666 9.99984C16.6666 6.31794 13.6819 3.33317 9.99996 3.33317C6.31806 3.33317 3.33329 6.31794 3.33329 9.99984C3.33329 13.6818 6.31806 16.6665 9.99996 16.6665ZM10.8333 9.99984H14.1666V11.6665H9.16662V5.83317H10.8333V9.99984Z" fill="#566676"/>
                                            </svg>
                                        </div>
                                        <div class="info-select">
                                            <h5><?php esc_html_e("Time", "tourfic"); ?></h5>
                                            <input type="text" placeholder="Pick Up Time" class="tf_pickup_time" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                                <div class="tf-select-date">
                                    <div class="tf-flex tf-flex-gap-4">
                                        <div class="icon">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.49996 0.833496V2.50016H12.5V0.833496H14.1666V2.50016H17.5C17.9602 2.50016 18.3333 2.87326 18.3333 3.3335V16.6668C18.3333 17.1271 17.9602 17.5002 17.5 17.5002H2.49996C2.03973 17.5002 1.66663 17.1271 1.66663 16.6668V3.3335C1.66663 2.87326 2.03973 2.50016 2.49996 2.50016H5.83329V0.833496H7.49996ZM16.6666 9.16683H3.33329V15.8335H16.6666V9.16683ZM6.66663 10.8335V12.5002H4.99996V10.8335H6.66663ZM10.8333 10.8335V12.5002H9.16662V10.8335H10.8333ZM15 10.8335V12.5002H13.3333V10.8335H15ZM5.83329 4.16683H3.33329V7.50016H16.6666V4.16683H14.1666V5.8335H12.5V4.16683H7.49996V5.8335H5.83329V4.16683Z" fill="#566676"/>
                                            </svg>
                                        </div>
                                        <div class="info-select">
                                            <h5><?php esc_html_e("Drop-off date", "tourfic"); ?></h5>
                                            <input type="text" placeholder="Drop Off Date" class="tf_dropoff_date" />
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-select-date">
                                        <div class="tf-flex tf-flex-gap-4">
                                            <div class="icon">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.99996 18.3332C5.39758 18.3332 1.66663 14.6022 1.66663 9.99984C1.66663 5.39746 5.39758 1.6665 9.99996 1.6665C14.6023 1.6665 18.3333 5.39746 18.3333 9.99984C18.3333 14.6022 14.6023 18.3332 9.99996 18.3332ZM9.99996 16.6665C13.6819 16.6665 16.6666 13.6818 16.6666 9.99984C16.6666 6.31794 13.6819 3.33317 9.99996 3.33317C6.31806 3.33317 3.33329 6.31794 3.33329 9.99984C3.33329 13.6818 6.31806 16.6665 9.99996 16.6665ZM10.8333 9.99984H14.1666V11.6665H9.16662V5.83317H10.8333V9.99984Z" fill="#566676"/>
                                            </svg>
                                        </div>
                                        <div class="info-select">
                                            <h5><?php esc_html_e("Time", "tourfic"); ?></h5>
                                            <input type="text" placeholder="Drop Off Time" class="tf_dropoff_time" />
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
                                            <input type="checkbox" name="same_location">
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label><?php esc_html_e("Age of driver 18-40?", "tourfic"); ?>
                                            <input type="checkbox" name="driver_age">
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                            <div class="tf-submit-button">
                                <button class="tf-filter-cars"><?php esc_html_e("Search", "tourfic"); ?> <i class="ri-search-line"></i></button>
                            </div>

                            <script>
                                (function ($) {
                                    $(document).ready(function () {

                                        // flatpickr locale first day of Week
                                        <?php tf_flatpickr_locale("root"); ?>

                                        // Initialize the pickup date picker
                                        var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
                                            enableTime: false,
                                            dateFormat: "Y/m/d",
                                            minDate: "today",

                                            // flatpickr locale
                                            <?php tf_flatpickr_locale(); ?>

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
                                            <?php tf_flatpickr_locale(); ?>

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
                                            <?php tf_flatpickr_locale(); ?>

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
                                            <?php tf_flatpickr_locale(); ?>

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
                        </div>
                    </div>
                </div>


                <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
                    <div class="tf-archive-view">
                        <ul class="tf-flex tf-flex-gap-16">
                            <li class="<?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-view="grid"><i class="ri-layout-grid-line"></i></li>
                            <li class="<?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" data-view="list"><i class="ri-list-check"></i></li>
                        </ul>
                    </div>
                    <div class="tf-total-result-bar">
                        <span>Showing 8 of 15 Results</span>
                    </div>
                </div>
                <div class="tf-car-details-column tf-flex tf-flex-gap-32">
                    
                    <div class="tf-car-archive-sidebar">
                        <div class="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h4><?php esc_html_e("Filter", "tourfic") ?></h4>
                            <button><?php esc_html_e("Reset", "tourfic"); ?></button>
                        </div>
                        
                        <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                            <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                        <?php } ?>

                    </div>

                    <div class="tf-car-archive-result">
                        
                        <div class="tf-car-result tf-flex tf-flex-gap-32 <?php echo $tf_defult_views=="list" ? esc_attr('list-view') : esc_attr('grid-view'); ?>">
                            
                            <?php
                            if ( have_posts() ) {
                                while ( have_posts() ) {
                                    the_post();
                                    $car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
                                    if ( !empty( $car_meta[ "car_as_featured" ] ) && $car_meta[ "car_as_featured" ] == 1 ) {
                                        tf_car_archive_single_item();
                                    }
                                }
                                while ( have_posts() ) {
                                    the_post();
                                    $car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
                                    if ( empty($car_meta[ "car_as_featured" ]) ) {
                                        tf_car_archive_single_item();
                                    }
                                }
                            } else {
                                echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
                            }
                            ?>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>