<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Car_Rental\Pricing;
use \Tourfic\App\TF_Review;
?>
<?php
$booking_btn_text = !empty(Helper::tfopt('car_booking_form_button_text')) ? Helper::tfopt('car_booking_form_button_text') : esc_html__('Continue', 'tourfic');
$pickup_date_query = !empty($_GET['pickup_date']) ? sanitize_text_field( wp_unslash($_GET['pickup_date']) ) : '';
if ( empty( $pickup_date_query ) && !empty($_GET['pickup-date']) ) {
	$pickup_date_query = sanitize_text_field( wp_unslash($_GET['pickup-date']) );
}
$dropoff_date_query = !empty($_GET['dropoff_date']) ? sanitize_text_field( wp_unslash($_GET['dropoff_date']) ) : '';
if ( empty( $dropoff_date_query ) && !empty($_GET['dropoff-date']) ) {
	$dropoff_date_query = sanitize_text_field( wp_unslash($_GET['dropoff-date']) );
}
$tf_pickup_date = !empty($pickup_date_query) && function_exists('tf_normalize_date') ? tf_normalize_date($pickup_date_query) : $pickup_date_query;
$tf_dropoff_date = !empty($dropoff_date_query) && function_exists('tf_normalize_date') ? tf_normalize_date($dropoff_date_query) : $dropoff_date_query;

// Pull options from settings or set fallback values
$disable_car_time_slot = !empty(Helper::tfopt('disable-car-time-slots')) ? boolval(Helper::tfopt('disable-car-time-slots')) : false;
$car_time_slots = !empty(Helper::tfopt('car_time_slots')) ? Helper::tfopt('car_time_slots') : '';
$unserialize_car_time_slots = !empty($car_time_slots) ? unserialize($car_time_slots) : array();

$time_interval = 30;
$start_time_str = '00:00';
$end_time_str   = '23:30';
$default_time_str = '10:00';
$next_current_day = gmdate('l', strtotime('+1 day'));
$date_format_for_users         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

if($disable_car_time_slot){
    $time_interval = !empty(Helper::tfopt('car_time_interval')) ? intval(Helper::tfopt('car_time_interval')) : 30;
    if (!empty($unserialize_car_time_slots)) {
        foreach ($unserialize_car_time_slots as $slot) {
            if (isset($slot['day']) && strtolower($slot['day']) == strtolower($next_current_day)) {
                $start_time_str = !empty($slot['pickup_time']) ? $slot['pickup_time'] : $start_time_str;
                $end_time_str   = !empty($slot['drop_time']) ? $slot['drop_time'] : $end_time_str;
                if ( strtotime($start_time_str) >= strtotime('10:00') ) {
                    $default_time_str = $start_time_str;
                }
                break; 
            }
        }
    }
}

// Convert string times to timestamps
$start_time = strtotime($start_time_str);
$end_time   = strtotime($end_time_str);
$default_time = gmdate('g:i A', strtotime($default_time_str));

// Use selected time from GET or fall back to default
$selected_pickup_time = !empty($_GET['pickup_time']) ? sanitize_text_field( wp_unslash($_GET['pickup_time']) ) : '';
if ( empty( $selected_pickup_time ) && !empty($_GET['pickup-time']) ) {
	$selected_pickup_time = sanitize_text_field( wp_unslash($_GET['pickup-time']) );
}
if ( empty( $selected_pickup_time ) ) {
	$selected_pickup_time = $default_time;
}
$selected_dropoff_time = !empty($_GET['dropoff_time']) ? sanitize_text_field( wp_unslash($_GET['dropoff_time']) ) : '';
if ( empty( $selected_dropoff_time ) && !empty($_GET['dropoff-time']) ) {
	$selected_dropoff_time = sanitize_text_field( wp_unslash($_GET['dropoff-time']) );
}
if ( empty( $selected_dropoff_time ) ) {
	$selected_dropoff_time = $default_time;
}

$total_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $selected_pickup_time, $selected_dropoff_time); 
$show_total_regular_price = ! empty( $total_prices['regular_price'] ) && (float) $total_prices['regular_price'] > (float) $total_prices['sale_price'];
$display_total_price = ! empty( $total_prices['sale_price'] ) ? $total_prices['sale_price'] : ( ! empty( $total_prices['regular_price'] ) ? $total_prices['regular_price'] : 0 );
$tf_cars_slug = get_option('car_slug');
?>
<div class="tf-single-template__one">
    <div class="tf-single-booking-bar">
        <div class="tf-container">
            <div class="tf-top-booking-bar tf-flex tf-flex-space-bttn tf-flex-align-center">
                <?php \Tourfic\App\Templates\Components\Global\Single\Sticky_Nav::render(); ?>
                
                <div class="tf-top-bar-booking tf-flex tf-flex-gap-32">
                    <div class="tf-price-header">
                        <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                        <?php if ( $show_total_regular_price ) { ?><del><?php echo wp_kses_post( wc_price( $total_prices['regular_price'] ) ); ?></del> <?php } ?>
                        <?php echo ! empty( $display_total_price ) ? wp_kses_post( wc_price( $display_total_price ) ) : ''; ?></h2>
                        <p><?php echo wp_kses_post(Pricing::is_taxable($meta)); ?></p>
                    </div>
                    <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 tf-back-to-booking">
                        <?php echo esc_html( apply_filters("tf_car_booking_form_submit_button_text", 'Continue' ) ); ?>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tf-container">
        <div class="tf-container-inner">
            <div class="tf-single-car-details-warper">
                <div class="tf-car-details-column">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Gallery::render(); ?>

                    <?php \Tourfic\App\Templates\Components\Global\Single\Sticky_Nav::render(); ?>
                    
                    <div class="tf-template-part tf-flex tf-flex-gap-32 tf-flex-direction-column">
                        <?php
                        if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-car-layout'] ) ) {
                            foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-car-layout'] as $section ) {
                                if ( ! empty( $section['status'] ) && $section['status'] == "1" && ! empty( $section['slug'] ) ) {
                                    include TF_TEMPLATE_PART_PATH . 'car/design-1/' . $section['slug'] . '.php';
                                }
                            }
                        } else {
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/description.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/car-info.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/benefits.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/inc-exc.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/location.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/faq.php';
                        }
                        ?>
                    </div>

                </div>
                <?php do_action("tf_car_before_single_booking_form"); ?>
                <div class="tf-car-booking-form">

                    <div class="tf-price-header tf-mb-30">
                        <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                        <?php if(!empty($total_prices['regular_price'])){ ?><del><?php echo wp_kses_post(wc_price($total_prices['regular_price'])); ?></del>  <?php } ?>
                        <?php echo $total_prices['sale_price'] ? wp_kses_post(wc_price($total_prices['sale_price'])) : '' ?> <?php if( empty($tf_pickup_date) && !empty($total_prices['type'])){ ?>
                            <small class="pricing-type">/ <?php echo esc_html($total_prices['type']); ?></small> 
                            <?php } ?></h2>
                        <p><?php echo wp_kses_post(Pricing::is_taxable($meta)); ?></p>
                    </div>

                    <?php if(function_exists( 'is_tf_pro' ) && is_tf_pro()){ ?>
                    <div class="tf-extra-added-info">
                        <div class="tf-extra-added-box tf-flex tf-flex-gap-16 tf-flex-direction-column">
                            <h3><?php esc_html_e("Extras added", "tourfic"); ?></h3>
                            <div class="tf-added-extra tf-flex tf-flex-gap-16 tf-flex-direction-column">
                                
                            </div>
                        </div>
                    </div>
                    <?php } ?>


                    <div class="tf-date-select-box">

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
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
                                        <input type="text" placeholder="<?php echo esc_attr__("Pick Up Location", "tourfic"); ?>" id="tf_pickup_location" value="<?php echo !empty($_GET['pickup']) ? esc_html(get_term_by( 'slug', sanitize_text_field( wp_unslash($_GET['pickup']) ), 'carrental_location' )->name) : ''; ?>" />
                                        <input type="hidden" id="tf_pickup_location_id" value="<?php echo !empty($_GET['pickup']) ? esc_html(sanitize_text_field( wp_unslash($_GET['pickup']) )) : ''; ?>" />
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
                                        <input type="text" placeholder="<?php echo esc_attr__( 'Drop Off Location', 'tourfic' ); ?>" id="tf_dropoff_location" value="<?php echo !empty($_GET['dropoff']) ? esc_html(get_term_by( 'slug', sanitize_text_field( wp_unslash($_GET['dropoff']) ), 'carrental_location' )->name) : ''; ?>" />
                                        <input type="hidden" id="tf_dropoff_location_id" value="<?php echo !empty($_GET['dropoff']) ? esc_html(sanitize_text_field( wp_unslash($_GET['dropoff']) )) : ''; ?>" />
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
                                        <input type="text" placeholder="<?php esc_html_e("Pick Up Date", "tourfic"); ?>" id="tf_pickup_date" class="tf_pickup_date" />
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
                                        <input type="hidden" name="tf_pickup_time" class="tf_pickup_time" id="tf_pickup_time" value="<?php echo esc_attr($selected_pickup_time); ?>">
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
                                        <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php esc_html_e("Drop-off date", "tourfic"); ?></h5>
                                        <input type="text" placeholder="Drop Off Date" id="tf_dropoff_date" class="tf_dropoff_date" />
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
                                        <input type="hidden" value="<?php echo esc_attr($post_id); ?>" id="post_id" />
                                        <input type="hidden" name="tf_dropoff_time" class="tf_dropoff_time" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
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
                        <div class="tf-form-submit-btn">
                            <div class="error-notice"></div>
                            <?php 
                            if($car_deposit_type=='fixed'){
                                $due_amount = $car_deposit_amount;
                            }
                            if($car_deposit_type=='percent'){
                                $due_amount = ($total_prices['sale_price'] * $car_deposit_amount)/100;
                            }
                            if( function_exists( 'is_tf_pro' ) && is_tf_pro() && '2'==$car_booking_by ){ ?>
                                <button class="tf_btn tf-flex tf-flex-align-center tf-flex-justify-center booking-process tf-final-step tf-flex-gap-8">
                                    <?php echo esc_html( apply_filters("tf_car_booking_form_submit_button_text", $booking_btn_text ), 'tourfic' ); ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            <?php }else{ ?>
                                <?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($car_allow_deposit) && $car_deposit_type!='none' && !empty($car_deposit_amount) ){  ?>
                                    <div class="tf-partial-payment-button tf-flex tf-flex-direction-column tf-flex-gap-16">
                                        <button class="tf_btn tf-flex tf-flex-align-center tf-partial-button tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('yes'); ?>">
                                            <?php esc_html_e( 'Part Pay', 'tourfic' ); ?> <?php echo wp_kses_post(wc_price($due_amount)); ?>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.3299 10.3541L11.6835 10.0006L11.3299 9.64703L7.55867 5.87577L8.03008 5.40437L12.6263 10.0006L8.03008 14.5967L7.55867 14.1253L11.3299 10.3541Z" fill="#566676" stroke="#0866C4"/>
                                            </svg>
                                        </button>

                                        <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('no'); ?>">
                                            <?php esc_html_e( 'Full Pay', 'tourfic' ); ?> <?php echo wp_kses_post(wc_price($total_prices['sale_price'])); ?>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                <?php }else{ ?>
                                    <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>">
                                        <?php echo esc_html( apply_filters("tf_car_booking_form_submit_button_text", esc_html__('Continue', 'tourfic') ) ); ?>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <?php if($car_instructions_section_status){ ?>
                            <div class="tf-instraction-btn tf-mt-16">
                                <span class="tf-instraction-showing"><?php esc_html_e("Pick-up and Drop-off instructions", "tourfic"); ?></span>
                                
                                <div class="tf-car-instraction-popup">
                                    <div class="tf-instraction-popup-warp">

                                        <div class="tf-instraction-popup-header tf-flex tf-flex-align-center tf-flex-space-bttn">
                                            <h3><?php esc_html_e("Pick-up and Drop-off instructions", "tourfic"); ?></h3>
                                            <div class="tf-close-popup">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15 5L5 15M5 5L15 15" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <?php if(!empty($car_instructions_content)): ?>
                                            <div class="tf-instraction-content-wraper">
                                                <?php echo wp_kses_post($car_instructions_content); ?>
                                            </div>    
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php do_action( 'tf_car_cancellation', $post_id ); ?>
                    </div>
                    <div class="tf-mobile-booking-btn">
                        <div class="tf-price-header">
                            <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                            <?php if(!empty($total_prices['regular_price'])){ ?><del><?php echo wp_kses_post(wc_price($total_prices['regular_price'])); ?></del>  <?php } ?>
                            <?php echo $total_prices['sale_price'] ? wp_kses_post(wc_price($total_prices['sale_price'])) : '' ?> <?php if(!empty($total_prices['type'])){ ?><small class="pricing-type">/ <?php echo esc_html($total_prices['type']); ?></small> <?php } ?></h2>
                            <p><?php echo wp_kses_post(Pricing::is_taxable($meta)); ?></p>
                        </div>
                        <button><?php esc_html_e("Book Now", "tourfic"); ?></button>
                    </div>
                    <div class="tf-car-booking-popup">
                        <div class="tf-booking-popup-warp">

                            <div class="tf-booking-popup-header tf-flex tf-flex-align-center tf-flex-space-bttn">
                                <h3><?php esc_html_e("Additional information", "tourfic"); ?></h3>
                                <div class="tf-close-popup">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 5L5 15M5 5L15 15" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="tf-booking-content-wraper">

                            </div>

                        </div>
                    </div>

                    <div class="tf-withoutpayment-booking-confirm">
                        <div class="tf-confirm-popup">
                            <div class="tf-booking-times">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                                        <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                                        <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                                        </svg>
                                    </span>
                            </div>
                            <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/thank-you.gif" alt="Thank You">
                            <h2>
                                <?php
                                $booking_confirmation_msg = ! empty( Helper::tfopt( 'car-booking-confirmation-msg' ) ) ? Helper::tfopt( 'car-booking-confirmation-msg' ) : esc_html__('Booked Successfully', 'tourfic');
                                echo esc_html( $booking_confirmation_msg );
                                ?>
                            </h2>
                        </div>
                    </div>

                    <?php do_action( 'tf_car_extras', $car_extras, $post_id, $car_extra_sec_title ); ?>

                    <?php 
                    \Tourfic\App\Templates\Components\Car_Rental\Single\Car_Driver_Info::render([
                        'wrapper_open' => '<div class="tf-mb-30">',
                        'wrapper_close' => '</div>'
                    ]);
                    
                    \Tourfic\App\Templates\Components\Car_Rental\Single\Car_Contact_Info::render(); 
                    ?>

                </div>
                <?php do_action("tf_car_after_single_booking_form"); ?>
            </div>
            
            <?php \Tourfic\App\Templates\Components\Global\Single\Terms_And_Conditions::render(); ?>

            <?php
            global $current_user;
            // Check if user is logged in
            $is_user_logged_in = $current_user->exists();
            $post_id           = $post->ID;
            // Get settings value
            $tf_ratings_for   = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
            $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;

            $tf_comment_counts = get_comments( array(
                'post_id' => $post_id,
                'user_id' => $current_user->ID,
                'count'   => true,
            ) );

            ?>
            <div class="tf-review-section" id="tf-reviews">
            <?php if ( $comments ) {
            $tf_overall_rate = [];
            TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
            TF_Review::tf_get_review_fields( $fields );
            ?>
            <?php if(!empty($review_sec_title)){ ?>   
                <h3><?php echo esc_html($review_sec_title); ?></h3>
            <?php } ?>
            <div class="tf-review-data-inner">

                <div class="tf-review-data">
                    <div class="tf-review-data-average">
                        <span class="avg-review tf-flex tf-flex-align-center tf-flex-gap-8">
                            <?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
                            <i class="fa fa-star"></i>
                        </span>
                        <div class="tf-review-all-info">
                            <p><?php esc_html_e( "From ", "tourfic" ); ?><?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
                        </div>
                    </div>
                </div>

                <div class="tf-review-data-features">
                    <div class="tf-percent-progress">
                        <?php
                        if ( $tf_overall_rate ) {
                            foreach ( $tf_overall_rate as $key => $value ) {
                                if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                    continue;
                                }
                                $value = TF_Review::tf_average_ratings( $value );
                                ?>
                                <div class="tf-progress-item">
                                    <div class="tf-progress-bar">
                                        <span class="percent-progress" style="width: <?php echo esc_attr( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
                                    </div>
                                    <div class="tf-review-feature-label">
                                        <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                        <p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
                                    </div>
                                </div>
                            <?php }
                        } ?>

                    </div>
                </div>
            </div>
            <div class="tf-clients-reviews">
                <?php
                foreach ( $comments as $comment ) {
                    // Get rating details
                    $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                    if ( $tf_overall_rate == false ) {
                        $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                        $tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
                    }
                    $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                    $c_rating  = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                    // Comment details
                    $c_avatar      = get_avatar( $comment, '56' );
                    $c_author_name = $comment->comment_author;
                    $c_date        = $comment->comment_date;
                    $c_content     = $comment->comment_content;
                    ?>
                    <div class="tf-reviews-item tf-flex tf-flex-gap-16">
                        <div class="tf-reviews-avater">
                            <?php echo wp_kses_post( $c_avatar ); ?>
                        </div>
                        <div class="tf-reviews-text">
                            <span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
                            <span class="tf-reviews-meta"><?php echo esc_html( $c_author_name ); ?> <span class="tf-reviews-time">| <?php echo wp_kses_post( wp_date( "F Y", strtotime( $c_date ) ) ); ?></span></span>
                            <p><?php echo wp_kses_post( \Tourfic\Classes\Helper::tourfic_character_limit_callback( $c_content, 180 ) ); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php
                // Review moderation notice
                echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' );
            ?>
            <?php

            if ( ! empty( $tf_ratings_for ) && empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) {
                if ( $is_user_logged_in ) {
                    if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                        ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e( "Leave your review", "tourfic" ); ?></h3>
                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php
                    }
                } else {
                    if ( in_array( 'lo', $tf_ratings_for ) ) {
                        ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e( "Leave your review", "tourfic" ); ?></h3>
                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                    <?php }
                }
            } ?>
            </div>
        </div>
    </div>
</div>
 <script>
    (function ($) {
        $(document).ready(function () {
            let today = new Date();
            let tomorrow = new Date();
            tomorrow.setDate(today.getDate() + 1);
            let dayAfter = new Date();
            dayAfter.setDate(today.getDate() + 2);

            // flatpickr locale first day of Week
            <?php Helper::tf_flatpickr_locale( "root" ); ?>

            $(".tf-single-template__one #tf_dropoff_date").on("click", function () {
                $(".tf-single-template__one .tf_pickup_date").trigger("click");
            });
            $(".tf-single-template__one #tf_pickup_date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                minDate: "today",
                altInput: true,
                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                // flatpickr locale
                <?php Helper::tf_flatpickr_locale(); ?>

                onReady: function (selectedDates, dateStr, instance) {
                    dateSetToFields(selectedDates, instance);
                },
                onChange: function (selectedDates, dateStr, instance) {
                    dateSetToFields(selectedDates, instance);
                },
                <?php if(! empty( $tf_pickup_date ) && ! empty( $tf_dropoff_date ) ){ ?>
                    defaultDate: ["<?php echo esc_js( $tf_pickup_date ); ?>", "<?php echo esc_js( $tf_dropoff_date ); ?>"],
                <?php } else { ?>
                    defaultDate: [tomorrow, dayAfter],
                <?php } ?>
            });

            function dateSetToFields(selectedDates, instance) {
                const format = '<?php echo esc_html( $date_format_for_users ); ?>';
                if (selectedDates.length >= 1) {
                    const startDateObj = selectedDates[0];
                    const endDateObj = selectedDates.length === 2 ? selectedDates[1] : selectedDates[0];
                    const startDay = flatpickr.formatDate(startDateObj, "l");
                    const endDay = flatpickr.formatDate(endDateObj, "l");
                    if (startDateObj) {
                        const startDate = flatpickr.formatDate(startDateObj, format);
                        $(".tf-single-template__one .tf_pickup_date").val(startDate);
                    }
                    if (endDateObj) {
                        const endDate = flatpickr.formatDate(endDateObj, format);
                        $(".tf-single-template__one .tf_dropoff_date").val(endDate);
                    }

                    $.ajax({
                        url: <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ) ?>,
                        type: 'POST',
                        data: {
                            action: 'get_car_time_slots',
                            pickup_day: startDay,
                            drop_day: endDay
                        },
                        success: function(response) {
                        }
                    });
                }
            }
        });
    })(jQuery);
</script>
