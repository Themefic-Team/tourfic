<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Car_Rental\Pricing;
use \Tourfic\App\TF_Review;
?>
<?php
$tourfic_booking_btn_text = !empty(Helper::tfopt('car_booking_form_button_text')) ? Helper::tfopt('car_booking_form_button_text') : esc_html__('Continue', 'tourfic');
$tourfic_pickup_date_query = !empty($_GET['pickup_date']) ? sanitize_text_field( wp_unslash($_GET['pickup_date']) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( empty( $tourfic_pickup_date_query ) && !empty($_GET['pickup-date']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tourfic_pickup_date_query = sanitize_text_field( wp_unslash($_GET['pickup-date']) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
$tourfic_dropoff_date_query = !empty($_GET['dropoff_date']) ? sanitize_text_field( wp_unslash($_GET['dropoff_date']) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( empty( $tourfic_dropoff_date_query ) && !empty($_GET['dropoff-date']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tourfic_dropoff_date_query = sanitize_text_field( wp_unslash($_GET['dropoff-date']) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
$tourfic_pickup_date = !empty($tourfic_pickup_date_query) && function_exists('tf_normalize_date') ? tf_normalize_date($tourfic_pickup_date_query) : $tourfic_pickup_date_query;
$tourfic_dropoff_date = !empty($tourfic_dropoff_date_query) && function_exists('tf_normalize_date') ? tf_normalize_date($tourfic_dropoff_date_query) : $tourfic_dropoff_date_query;

// Pull options from settings or set fallback values
$tourfic_disable_car_time_slot = !empty(Helper::tfopt('disable-car-time-slots')) ? boolval(Helper::tfopt('disable-car-time-slots')) : false;
$tourfic_car_time_slots = !empty(Helper::tfopt('car_time_slots')) ? Helper::tfopt('car_time_slots') : '';
$tourfic_unserialize_car_time_slots = !empty($tourfic_car_time_slots) ? unserialize($tourfic_car_time_slots) : array();

$tourfic_time_interval = 30;
$tourfic_start_time_str = '00:00';
$tourfic_end_time_str   = '23:30';
$tourfic_default_time_str = '10:00';
$tourfic_next_current_day = gmdate('l', strtotime('+1 day'));
$tourfic_date_format_for_users         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

if($tourfic_disable_car_time_slot){
    $tourfic_time_interval = !empty(Helper::tfopt('car_time_interval')) ? intval(Helper::tfopt('car_time_interval')) : 30;
    if (!empty($tourfic_unserialize_car_time_slots)) {
        foreach ($tourfic_unserialize_car_time_slots as $tourfic_slot) {
            if (isset($tourfic_slot['day']) && strtolower($tourfic_slot['day']) == strtolower($tourfic_next_current_day)) {
                $tourfic_start_time_str = !empty($tourfic_slot['pickup_time']) ? $tourfic_slot['pickup_time'] : $tourfic_start_time_str;
                $tourfic_end_time_str   = !empty($tourfic_slot['drop_time']) ? $tourfic_slot['drop_time'] : $tourfic_end_time_str;
                if ( strtotime($tourfic_start_time_str) >= strtotime('10:00') ) {
                    $tourfic_default_time_str = $tourfic_start_time_str;
                }
                break; 
            }
        }
    }
}

// Convert string times to timestamps
$tourfic_start_time = strtotime($tourfic_start_time_str);
$tourfic_end_time   = strtotime($tourfic_end_time_str);
$tourfic_default_time = gmdate('g:i A', strtotime($tourfic_default_time_str));

// Use selected time from GET or fall back to default
$tourfic_selected_pickup_time = !empty($_GET['pickup_time']) ? sanitize_text_field( wp_unslash($_GET['pickup_time']) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( empty( $tourfic_selected_pickup_time ) && !empty($_GET['pickup-time']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tourfic_selected_pickup_time = sanitize_text_field( wp_unslash($_GET['pickup-time']) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
if ( empty( $tourfic_selected_pickup_time ) ) {
	$tourfic_selected_pickup_time = $tourfic_default_time;
}
$tourfic_selected_dropoff_time = !empty($_GET['dropoff_time']) ? sanitize_text_field( wp_unslash($_GET['dropoff_time']) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( empty( $tourfic_selected_dropoff_time ) && !empty($_GET['dropoff-time']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tourfic_selected_dropoff_time = sanitize_text_field( wp_unslash($_GET['dropoff-time']) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
if ( empty( $tourfic_selected_dropoff_time ) ) {
	$tourfic_selected_dropoff_time = $tourfic_default_time;
}

$tourfic_total_prices = Pricing::set_total_price($tourfic_meta, $tourfic_pickup_date, $tourfic_dropoff_date, $tourfic_selected_pickup_time, $tourfic_selected_dropoff_time);
$tourfic_show_total_regular_price = ! empty( $tourfic_total_prices['regular_price'] ) && (float) $tourfic_total_prices['regular_price'] > (float) $tourfic_total_prices['sale_price'];
$tourfic_display_total_price = ! empty( $tourfic_total_prices['sale_price'] ) ? $tourfic_total_prices['sale_price'] : ( ! empty( $tourfic_total_prices['regular_price'] ) ? $tourfic_total_prices['regular_price'] : 0 );
$tourfic_cars_slug = get_option('car_slug');
?>
<div class="tf-single-template__one">
    <div class="tf-single-booking-bar">
        <div class="tf-container">
            <div class="tf-top-booking-bar tf-flex tf-flex-space-bttn tf-flex-align-center">
                <?php \Tourfic\App\Templates\Components\Shared\Single\Sticky_Nav::render(); ?>
                
                <div class="tf-top-bar-booking tf-flex tf-flex-gap-32">
                    <div class="tf-price-header">
                        <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                        <?php if ( $tourfic_show_total_regular_price ) { ?><del><?php echo wp_kses_post( wc_price( $tourfic_total_prices['regular_price'] ) ); ?></del> <?php } ?>
                        <?php echo ! empty( $tourfic_display_total_price ) ? wp_kses_post( wc_price( $tourfic_display_total_price ) ) : ''; ?></h2>
                        <p><?php echo wp_kses_post(Pricing::is_taxable($tourfic_meta)); ?></p>
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
                            foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-car-layout'] as $tourfic_section ) {
                                if ( ! empty( $tourfic_section['status'] ) && $tourfic_section['status'] == "1" && ! empty( $tourfic_section['slug'] ) ) {
                                    include TF_TEMPLATE_PART_PATH . 'car/design-1/' . $tourfic_section['slug'] . '.php';
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
                altFormat: '<?php echo esc_html( $tourfic_date_format_for_users ); ?>',
                // flatpickr locale
                <?php Helper::tf_flatpickr_locale(); ?>

                onReady: function (selectedDates, dateStr, instance) {
                    dateSetToFields(selectedDates, instance);
                },
                onChange: function (selectedDates, dateStr, instance) {
                    dateSetToFields(selectedDates, instance);
                },
                <?php if(! empty( $tourfic_pickup_date ) && ! empty( $tourfic_dropoff_date ) ){ ?>
                    defaultDate: ["<?php echo esc_js( $tourfic_pickup_date ); ?>", "<?php echo esc_js( $tourfic_dropoff_date ); ?>"],
                <?php } else { ?>
                    defaultDate: [tomorrow, dayAfter],
                <?php } ?>
            });

            function dateSetToFields(selectedDates, instance) {
                const format = '<?php echo esc_html( $tourfic_date_format_for_users ); ?>';
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
                            drop_day: endDay,
                            nonce: <?php echo wp_json_encode( wp_create_nonce( 'tf_get_car_time_slots_nonce' ) ); ?>
                        },
                        success: function(response) {
                        }
                    });
                }
            }
        });
    })(jQuery);
</script>
