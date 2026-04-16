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
                    <?php 
                    \Tourfic\App\Templates\Components\Global\Single\Booking_Form::render(['wrapper' => 'no']);
                    
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
