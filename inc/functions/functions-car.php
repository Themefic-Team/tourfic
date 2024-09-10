<?php
# don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Car_Rental\Pricing;

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

/**
 * Extra Adding Options
 *
 * @include
 */

add_action( 'wp_ajax_nopriv_tf_extra_add_to_booking', 'tf_extra_add_to_booking_callback' );
add_action( 'wp_ajax_tf_extra_add_to_booking', 'tf_extra_add_to_booking_callback' );
function tf_extra_add_to_booking_callback() {
// Check nonce security
if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
	return;
}

$meta = get_post_meta( $_POST['post_id'], 'tf_carrental_opt', true );
$car_extra = !empty($meta['extras']) ? $meta['extras'] : '';

foreach($_POST['qty'] as $key => $singleqty){
	if(!empty($singleqty)){
		$single_extra_info = !empty($car_extra[$key]) ? $car_extra[$key] : '';
		if(!empty($single_extra_info)){ ?>
			<div class="tf-single-added-extra tf-flex tf-flex-align-center tf-flex-space-bttn">
				<h4><?php echo !empty($single_extra_info['title']) ? esc_html($single_extra_info['title']) : ''; ?></h4>
				<div class="qty-price tf-flex">
					<i class="ri-close-line"></i> 
					<span class="qty"><?php echo $singleqty; ?></span> 
					<span class="price"><?php echo !empty($single_extra_info['price']) ? wc_price($single_extra_info['price']*$singleqty) : ''; ?></span>
					<span class="delete">
						<input type="hidden" value="<?php echo esc_attr($key); ?>" name="selected_extra[]" />
						<input type="hidden" value="<?php echo esc_attr($singleqty); ?>" name="selected_qty[]" />
						<i class="ri-delete-bin-line"></i>
					</span>
				</div>
			</div>
		<?php
		}
	}
}

wp_die();
}

function tf_car_archive_single_item($pickup = '', $dropoff = '', $pickup_date = '', $dropoff_date = '', $pickup_time = '', $dropoff_time = ''){
	$post_id = get_the_ID();
	$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
	// Single link
	$url = get_the_permalink();
	$url = add_query_arg( array(
		'pickup'           => $pickup,
		'dropoff'          => $dropoff,
		'pickup_date'      => $pickup_date,
		'dropoff_date'     => $dropoff_date,
		'pickup_time' 	   => $pickup_time,
		'dropoff_time'     => $dropoff_time
	), $url );

	// Car Info 
	$passengers = ! empty( $meta['passengers'] ) ? $meta['passengers'] : '';
	$baggage = ! empty( $meta['baggage'] ) ? $meta['baggage'] : '';
	$car_custom_info = ! empty( $meta['car_custom_info'] ) ? $meta['car_custom_info'] : '';
	// Badge
	$badges = ! empty( $meta['badge'] ) ? $meta['badge'] : '';
?>
<div class="tf-single-car-view">
	<div class="tf-car-image">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'full' );
		} else {
			echo '<img src="' . esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" . '">';
		}
		?>
		<div class="tf-other-infos tf-flex tf-flex-gap-64">
			<?php TF_Review::tf_archive_single_rating(); ?>
			<div class="tf-tags-box">
				<ul>
					<?php
					if(!empty($badges)){
					foreach($badges as $key => $badge){ 
					if(!empty($badge['title']) && $key < 4){
					?>
					<li><?php echo esc_html($badge['title']); ?></li>
					<?php }}} ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="tf-car-details">
		<div class="tf-car-content">
			<a href="<?php echo esc_url( $url ); ?>"><h3 class="tf-mb-24"><?php the_title(); ?></h3></a>
			<ul class="tf-flex tf-mb-24">
				<?php if(!empty($passengers)){ ?>
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="fa-solid fa-wheelchair"></i><?php echo esc_attr($passengers); ?></li>
				<?php } ?>
        		<?php if(!empty($baggage)){ ?>
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-briefcase-line"></i></i><?php echo esc_attr($baggage); ?></li>
				<?php } ?>
				<?php if(!empty($car_custom_info)){
            	foreach($car_custom_info as $info){ ?>
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center">
					<?php if(!empty($info['info_icon'])){ ?>
						<i class="<?php echo esc_attr($info['info_icon']); ?>"></i>
					<?php } ?>
					<?php echo !empty($info['title']) ? esc_html($info['title']) : ''; ?>
				</li>
				<?php }} ?>
			</ul>
		</div>
		<div class="tf-booking-btn tf-flex tf-flex-space-bttn">
			<div class="tf-price-info">
				<?php
				$total_prices = Pricing::set_total_price($meta, $pickup_date, $dropoff_date, $pickup_time, $dropoff_time);
				?>
				<h3><?php echo $total_prices['sale_price'] ? wc_price($total_prices['sale_price']) : '' ?> <small>/ <?php echo esc_html($total_prices['type']); ?></small></h3>
			</div>
			<?php if(!empty($pickup_date) && !empty($dropoff_date)){ ?>
				<input type="hidden" value="<?php echo esc_attr($pickup_date); ?>" id="pickup_date">
				<input type="hidden" value="<?php echo esc_attr($dropoff_date); ?>" id="dropoff_date">
				<input type="hidden" value="<?php echo esc_attr($pickup_time); ?>" id="pickup_time">
				<input type="hidden" value="<?php echo esc_attr($dropoff_time); ?>" id="dropoff_time">
				<input type="hidden" value="<?php echo esc_attr($post_id); ?>" id="post_id">
				<button class="quick-booking"><?php esc_html_e("Book now", "tourfic"); ?></button>
			<?php }else{ ?>
				<a class="view-more" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e("Details", "tourfic"); ?></a>
			<?php } ?>
		</div>
	</div>
</div>

<?php
}


/**
 * Car Filter 
 *
 * @include
 */

function tf_car_availability_response($car_meta, $pickup='', $dropoff='', $tf_pickup_date='', $tf_dropoff_date='', $tf_pickup_time='', $tf_dropoff_time='', $tf_startprice='', $tf_endprice='', array &$not_found) {

	$has_car = false;
	$pricing_type = !empty($car_meta["pricing_type"]) ? $car_meta["pricing_type"] : 'day_hour';

	$date_pricing = !empty($meta["date_prices"]) ? $meta["date_prices"] : '';
	$day_pricing = !empty($meta["day_prices"]) ? $meta["day_prices"] : '';

	if( !empty($tf_pickup_date) && !empty($tf_dropoff_date) && 'date'==$pricing_type && !empty($date_pricing) ){

		if ( ! empty( $tf_startprice ) && ! empty( $tf_endprice ) ) {

			foreach ($date_pricing as $entry) {
				$startDate = strtotime($entry['date']['from']);
				$endDate = strtotime($entry['date']['to']);
				if($startDate==strtotime($tf_pickup_date) && $endDate==strtotime($tf_dropoff_date)){

					if($tf_startprice <= $entry['price'] && $entry['price'] <= $tf_endprice){
						$has_car = true;
					}else{
						$has_car = false;
					}
					break;

				}else{
					$has_car = false;
				}
			}

		}else{
			$has_car = true;
		}

	} else if( !empty($tf_pickup_date) && !empty($tf_dropoff_date) && 'day_hour'==$pricing_type && !empty($day_pricing) ){

		if ( ! empty( $tf_startprice ) && ! empty( $tf_endprice ) ) {

			if($tf_startprice <= $entry['price'] && $entry['price'] <= $tf_endprice){
				$has_car = true;
			}else{
				$has_car = false;
			}

		}else{
			$has_car = true;
		}

	}else{
		$has_car = true;
	}

	// Conditional hotel showing
	if ( $has_car ) {

		$not_found[] = array(
			'post_id' => get_the_ID(),
			'found'   => 0,
		);

	} else {

		$not_found[] = array(
			'post_id' => get_the_ID(),
			'found'   => 1,
		);
	}

}

/**
 * Car Min Max Price 
 *
 * @include
 */
if ( ! function_exists( 'get_cars_min_max_price' ) ) {
	function get_cars_min_max_price(){
		$tf_car_min_max = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_carrental',
			'post_status'    => 'publish'
		);

		$tf_car_min_max_query = new \WP_Query( $tf_car_min_max );
		$tf_car_min_maxprices = array();
		$tf_car_min_max_seat = array();
		if ( $tf_car_min_max_query->have_posts() ):
			while ( $tf_car_min_max_query->have_posts() ) : $tf_car_min_max_query->the_post();

				$meta = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
				
				if ( ! empty( $meta['passengers'] ) ) {
					$tf_car_min_max_seat[] = $meta['passengers'];
				}

				if ( ! empty( $meta['car_rent'] ) ) {
					$tf_car_min_maxprices[] = $meta['car_rent'];
				}

				if ( ! empty( $meta['date_prices'] ) ) {
					foreach ( $meta['date_prices'] as $minmax ) {
						if ( ! empty( $minmax['price'] ) ) {
							$tf_car_min_maxprices[] = $minmax['price'];
						}
					}
				}
				
				if ( ! empty( $meta['day_prices'] ) ) {
					foreach ( $meta['day_prices'] as $minmax ) {
						if ( ! empty( $minmax['price'] ) ) {
							$tf_car_min_maxprices[] = $minmax['price'];
						}
					}
				}
			endwhile;

		endif;
		wp_reset_query();
		if ( ! empty( $tf_car_min_maxprices ) && count( $tf_car_min_maxprices ) > 1 ) {
			$car_max_price_val = max( $tf_car_min_maxprices );
			$car_min_price_val = min( $tf_car_min_maxprices );
			if ( $car_max_price_val == $car_min_price_val ) {
				$car_max_price = max( $tf_car_min_maxprices );
				$car_min_price = 1;
			} else {
				$car_max_price = max( $tf_car_min_maxprices );
				$car_min_price = min( $tf_car_min_maxprices );
			}
		}
		if ( ! empty( $tf_car_min_maxprices ) && count( $tf_car_min_maxprices ) == 1 ) {
			$car_max_price = max( $tf_car_min_maxprices );
			$car_min_price = 1;
		}
		if ( empty( $tf_car_min_maxprices ) ) {
			$car_max_price = 0;
			$car_min_price = 0;
		}

		if ( ! empty( $tf_car_min_max_seat ) && count( $tf_car_min_max_seat ) > 1 ) {
			$car_max_seat_val = max( $tf_car_min_max_seat );
			$car_min_seat_val = min( $tf_car_min_max_seat );
			if ( $car_max_seat_val == $car_min_seat_val ) {
				$car_max_seat = max( $tf_car_min_max_seat );
				$car_min_seat = 1;
			} else {
				$car_max_seat = max( $tf_car_min_max_seat );
				$car_min_seat = min( $tf_car_min_max_seat );
			}
		}
		if ( ! empty( $tf_car_min_max_seat ) && count( $tf_car_min_max_seat ) == 1 ) {
			$car_max_seat = max( $tf_car_min_max_seat );
			$car_min_seat = 1;
		}
		if ( empty( $tf_car_min_max_seat ) ) {
			$car_max_seat = 0;
			$car_min_seat = 0;
		}

		return array(
			'min' => $car_min_price,
			'max' => $car_max_price,
			'min_seat' => $car_min_seat,
			'max_seat' => $car_max_seat
		);
	}
}

/**
 * Car Search form
 * @author Jahid
 */
if ( ! function_exists( 'tf_car_search_form_horizontal' ) ) {
	function tf_car_search_form_horizontal( $classes, $title, $subtitle, $advanced, $design ) {
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
		
        <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_car_booking" method="get" autocomplete="off" action="<?php echo esc_url( tf_booking_search_action() ); ?>">
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
                                        <i class="fas fa-search"></i>
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
                                        <i class="fas fa-search"></i>
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
                                        <i class="fas fa-search"></i>
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
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <input type="text" name="dropoff-time" class="tf_dropoff_time" placeholder="<?php esc_html_e( 'Enter Dropoff Time', 'tourfic' ); ?>" value="">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_carrental" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>

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
		<?php
	}
}

/*
 * Car search ajax
 * @author Jahid
 */
add_action( 'wp_ajax_tf_car_search', 'tf_car_search_ajax_callback' );
add_action( 'wp_ajax_nopriv_tf_car_search', 'tf_car_search_ajax_callback' );
if ( ! function_exists( 'tf_car_search_ajax_callback' ) ) {
	function tf_car_search_ajax_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
			return;
		}
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		if ( Helper::tfopt( 'pick_drop_car_search' ) && (empty( $_POST['pickup-name'] ) || empty( $_POST['dropoff-name'] )) ) {
			$response['message'] = esc_html__( 'Please enter Pickup & Dropoff location', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		} elseif ( Helper::tfopt( 'pick_drop_date_car_search' ) && (empty( $_POST['pickup-date'] ) || empty( $_POST['dropoff-date'] )) ) {
			$response['message'] = esc_html__( 'Please Select Pickup & Dropoff date', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		}

		
		$response['query_string'] = str_replace( '&action=tf_hotel_search', '', http_build_query( $_POST ) );
		$response['status']       = 'success';

		echo wp_json_encode( $response );
		wp_die();
	}
}