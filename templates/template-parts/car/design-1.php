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
                <?php \Tourfic\App\Templates\Components\Shared\Single\Sticky_Nav::render(); ?>
                
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
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Gallery::render(); ?>

                    <?php \Tourfic\App\Templates\Components\Shared\Single\Sticky_Nav::render(); ?>
                    
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
                    <?php 
                    \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(['wrapper' => 'no']);
                    
                    \Tourfic\App\Templates\Components\Car_Rental\Single\Car_Driver_Info::render([
                        'wrapper_open' => '<div class="tf-mb-30">',
                        'wrapper_close' => '</div>'
                    ]);
                    
                    \Tourfic\App\Templates\Components\Car_Rental\Single\Car_Contact_Info::render(); 
                    ?>
                </div>
                <?php do_action("tf_car_after_single_booking_form"); ?>
            </div>
            
            <?php \Tourfic\App\Templates\Components\Shared\Single\Terms_And_Conditions::render(); ?>

            <?php \Tourfic\App\Templates\Components\Shared\Single\Review::render(); ?>
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
