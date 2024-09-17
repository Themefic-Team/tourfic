<?php
# don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Car_Rental\Pricing;



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
				<div class="qty-price tf-flex tf-flex-space-bttn">
					<div class="line-sum tf-flex">
						<i class="ri-close-line"></i> 
						<span class="qty"><?php echo $singleqty; ?></span> 
						<span class="price"><?php echo !empty($single_extra_info['price']) ? wc_price($single_extra_info['price']*$singleqty) : ''; ?></span>
						</div>
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
	$unlimited_mileage = ! empty( $meta['unlimited_mileage'] ) ? $meta['unlimited_mileage'] : 0;
	$mileage_type = ! empty( $meta['mileage_type'] ) ? $meta['mileage_type'] : 'Km';
	$total_mileage = ! empty( $meta['mileage'] ) ? $meta['mileage'] : '';
	$auto_transmission = ! empty( $meta['auto_transmission'] ) ? $meta['auto_transmission'] : '';

	// Fuel Type
	$fuel_type_terms = wp_get_post_terms($post_id, 'carrental_fuel_type');
	$fuel_types = '';
	if (!is_wp_error($fuel_type_terms) && !empty($fuel_type_terms)) {
		foreach ($fuel_type_terms as $term) {
			$fuel_types = $term->name;
		}
	}
	// Engine Year
	$engine_year_terms = wp_get_post_terms($post_id, 'carrental_engine_year');
	$engine_years = '';
	if (!is_wp_error($engine_year_terms) && !empty($engine_year_terms)) {
		foreach ($engine_year_terms as $term) {
			$engine_years = $term->name;
		}
	}

	// Badge
	$badges = ! empty( $meta['badge'] ) ? $meta['badge'] : '';

	// Booking
	$car_booking_by = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : '1';

	// Protection
	$car_protection_section_status = ! empty( $meta['protection_section'] ) ? $meta['protection_section'] : '';
	$car_protection_content = ! empty( $meta['protection_content'] ) ? $meta['protection_content'] : '';
	$car_protections = ! empty( $meta['protections'] ) ? $meta['protections'] : '';
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
			
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center">
					<i class="ri-speed-up-line"></i>
					<?php echo $unlimited_mileage ? esc_html_e("Unlimited", "tourfic") : $total_mileage.' '.$mileage_type; ?>
				</li>

				<?php if(!empty($fuel_types)){ ?>
					<li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-gas-station-line"></i><?php echo esc_html($fuel_types); ?></li>
				<?php } ?>

				<?php if(!empty($engine_years)){ ?>
					<li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-car-line"></i><?php echo esc_html($engine_years); ?></li>
				<?php } ?>

				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-sound-module-fill"></i>
				<?php echo $auto_transmission ? esc_html_e("Auto", "tourfic") : esc_html_e("Manual", "tourfic"); ?>
				</li>

				<?php if(!empty($passengers)){ ?>
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="fa-solid fa-wheelchair"></i><?php echo esc_attr($passengers); ?> <?php esc_html_e("Person", "tourfic"); ?></li>
				<?php } ?>

        		<?php if(!empty($baggage)){ ?>
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-briefcase-line"></i><?php echo esc_attr($baggage); ?> <?php esc_html_e("Bags", "tourfic"); ?></li>
				<?php } ?>

			</ul>
		</div>
		<div class="tf-booking-btn tf-flex tf-flex-space-bttn">
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
				<?php if('2'==$car_booking_by){ ?>
					<button class="quick-booking"><?php esc_html_e("Book now", "tourfic"); ?></button>
				<?php }else{ ?>
					<button class="<?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('quick-booking') : esc_attr('tf-car-quick-booking'); ?>"><?php esc_html_e("Book now", "tourfic"); ?></button>
				<?php } ?>
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


/**
 * Booking Popup
 * @author Jahid
 */

add_action( 'wp_ajax_nopriv_tf_car_booking_pupup', 'tf_car_booking_pupup_callback' );
add_action( 'wp_ajax_tf_car_booking_pupup', 'tf_car_booking_pupup_callback' );
function tf_car_booking_pupup_callback() {
	// Check nonce security
	if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
		return;
	}
	/**
	 * Get car meta values
	 */
	$meta = get_post_meta( $_POST['post_id'], 'tf_carrental_opt', true );
	// Booking
	$car_booking_by = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : '1';

	// Protection
	$car_protection_section_status = ! empty( $meta['protection_section'] ) ? $meta['protection_section'] : '';
	$car_protection_content = ! empty( $meta['protection_content'] ) ? $meta['protection_content'] : '';
	$car_protections = ! empty( $meta['protections'] ) ? $meta['protections'] : '';
 	?>
	<div class="tf-cancellation-notice">
		<span class="tf-flex tf-flex-align-center tf-flex-gap-16">
			<i class="ri-information-line"></i>
			<b>Free cancellation</b>
			Full refund if you cancel your plan anytime before pick-up
		</span>
	</div>

	<div class="tf-booking-tabs">
		<ul>
			<?php if(!empty($car_protection_section_status) && !empty($car_protections)){ ?>
				<li class="protection active"><?php esc_html_e("Protections", "tourfic"); ?></li>
			<?php } ?>
			<?php if($car_booking_by=='3'){ ?>
			<li class="booking <?php echo empty($car_protection_section_status) ? esc_attr('active') : ''; ?>"><?php esc_html_e("Booking", "tourfic"); ?></li>
			<?php } ?>
		</ul>
	</div>
	<?php if(!empty($car_protection_section_status) && !empty($car_protections)){ ?>
	<div class="tf-protection-content tf-flex tf-flex-gap-24 tf-flex-direction-column">
		<?php if(!empty($car_protection_content)){ 
		echo wp_kses_post($car_protection_content);
		} ?>

		<div class="tf-protection-featured">
			<table>
				<tr>
					<td width="50%"><?php esc_html_e("What is covered", "tourfic"); ?></td>
					<td align="center"><?php esc_html_e("No protection", "tourfic"); ?></td>
					<td align="center"><?php esc_html_e("With protection", "tourfic"); ?></td>
				</tr>

				<?php 
				$total_protection_amount = 0;
				if(!empty($car_protections)){
					foreach($car_protections as $protection){ ?>
					<tr>
						<th>
							<div class="tf-flex">
								<?php echo !empty($protection['title']) ? esc_html($protection['title']) : ''; ?>
								<?php if(!empty($protection['content'])){ ?>
								<div class="tf-info-tooltip">
									<i class="ri-information-line"></i>
									<div class="tf-info-tooltip-content">
										<p><?php echo esc_html($protection['content']); ?></p>
									</div>
								</div>
								<?php } ?>
							</div>
						</th>
						<td align="center">
							<?php if(empty($protection['include'])){ ?>
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12.5001 7.49996L7.50008 12.5M7.50008 7.49996L12.5001 12.5M18.3334 9.99996C18.3334 14.6023 14.6025 18.3333 10.0001 18.3333C5.39771 18.3333 1.66675 14.6023 1.66675 9.99996C1.66675 5.39759 5.39771 1.66663 10.0001 1.66663C14.6025 1.66663 18.3334 5.39759 18.3334 9.99996Z" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php } ?>
						</td>
						<td align="center">
							<?php if(!empty($protection['include'])){ 
							if(!empty($protection['price'])){
								$total_protection_amount += $protection['price'];
							}   
							?>
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18.3334 9.2333V9.99997C18.3324 11.797 17.7505 13.5455 16.6745 14.9848C15.5986 16.4241 14.0862 17.477 12.3629 17.9866C10.6396 18.4961 8.7978 18.4349 7.11214 17.8121C5.42648 17.1894 3.98729 16.0384 3.00922 14.5309C2.03114 13.0233 1.56657 11.24 1.68481 9.4469C1.80305 7.65377 2.49775 5.94691 3.66531 4.58086C4.83288 3.21482 6.41074 2.26279 8.16357 1.86676C9.91641 1.47073 11.7503 1.65192 13.3918 2.3833M7.50009 9.16664L10.0001 11.6666L18.3334 3.3333" stroke="#27BE69" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php } ?>
						</td>
					</tr>
				<?php } } ?>


				<tfoot>
					<tr>
						<th width="50%" align="right"><?php esc_html_e("Charge", "tourfic"); ?>:</th>
						<th align="center"><?php echo wc_price(0.00); ?></th>
						<th align="center"><?php echo wc_price($total_protection_amount); ?></th>
					</tr>
				</tfoot>

			</table>
		</div>
	</div>

	<div class="tf-booking-bar tf-flex tf-flex-gap-24">
		<input type="hidden" id="protection_value" />
		<button data-charge="no" class="without-charge <?php echo '3'==$car_booking_by ? esc_attr('booking-next') : esc_attr('booking-process'); ?>">
			<?php esc_html_e("Book without protection", "tourfic"); ?>
			<i class="ri-arrow-right-s-line"></i>
		</button>
		<button data-charge="yes" class="with-charge <?php echo '3'==$car_booking_by ? esc_attr('booking-next') : esc_attr('booking-process'); ?>">
			<?php esc_html_e("Book with protection", "tourfic"); ?>
			<i class="ri-arrow-right-s-line"></i>
		</button>
	</div>

	<?php } ?>
	
	<div class="tf-booking-form-fields">
		<div class="tf-form-fields tf-flex tf-flex-gap-24 tf-flex-w">
			<?php 
			$traveller_info_fields = ! empty( Helper::tfopt( 'book-confirm-field' ) ) ? Helper::tf_data_types( Helper::tfopt( 'book-confirm-field' ) ) : '';
			if(empty($traveller_info_fields)){
			?>
			<div class="tf-single-field">
				<label for="tf_first_name"><?php esc_html_e("First Name", "tourfic"); ?></label>
				<input type="text" placeholder="First Name" id="tf_first_name" name="traveller['tf_first_name]" data-required="1">
				<div class="error-text" data-error-for="tf_first_name"></div>
			</div>
			<div class="tf-single-field">
				<label for="tf_last_name"><?php esc_html_e("Last Name", "tourfic"); ?></label>
				<input type="text" placeholder="Name" id="tf_last_name" name="traveller['tf_last_name]" data-required="1">
				<div class="error-text" data-error-for="tf_last_name"></div>
			</div>
			<div class="tf-single-field">
				<label for="tf_email"><?php esc_html_e("Email", "tourfic"); ?></label>
				<input type="text" placeholder="Email" id="tf_email" name="traveller['tf_email]" data-required="1">
				<div class="error-text" data-error-for="tf_email"></div>
			</div>
			<?php }else{ 
				foreach ( $traveller_info_fields as $field ) {
					if ( "text" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) {
						$reg_field_required = ! empty( $field['reg-field-required'] ) ? $field['reg-field-required'] : '';
						?>
						<div class="tf-single-field">
							<label for="<?php echo esc_attr($field['reg-field-name']); ?>"><?php echo esc_html($field['reg-field-label']); ?></label>
							<input type="<?php echo esc_attr($field['reg-fields-type']); ?>" name="traveller[<?php echo esc_attr($field['reg-field-name']); ?>]" data-required="<?php echo esc_attr($reg_field_required); ?>" id="<?php echo esc_attr($field['reg-field-name']); ?>" />
							<div class="error-text" data-error-for="<?php echo esc_attr($field['reg-field-name']); ?>"></div>
						</div>
					<?php } if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) { ?>
						<div class="tf-single-field">
							<label for="<?php echo esc_attr($field['reg-field-name']); ?>"><?php echo esc_html($field['reg-field-label']); ?></label>
							<select name="traveller[<?php echo esc_attr($field['reg-field-name']); ?>]" data-required="<?php echo esc_attr($reg_field_required); ?>" id="<?php echo esc_attr($field['reg-field-name']); ?>" >
							<?php 
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
								<option value="<?php echo esc_attr($sfield['option-value']); ?>"><?php echo esc_html($sfield['option-label']); ?></option>';
								<?php
								}
							}
							?>
							</select>
							<div class="error-text" data-error-for="<?php echo esc_attr($field['reg-field-name']); ?>"></div>
						</div>
					<?php } if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) { ?>

						<div class="tf-single-field">
							<label for="<?php echo esc_attr($field['reg-field-name']); ?>"><?php echo esc_html($field['reg-field-label']); ?></label>
							<?php 
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
									<div class="tf-single-checkbox">
									<input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="traveller[<?php echo esc_attr($field['reg-field-name']); ?>][]" id="<?php echo esc_attr($sfield['option-value']); ?>" value="<?php echo esc_attr($sfield['option-value']); ?>" data-required="<?php echo esc_attr($field['reg-field-required']); ?>" />
									<label for="<?php echo esc_attr($sfield['option-value']); ?>"><?php echo esc_html( $sfield['option-label'] ); ?></label></div>
								<?php }
							}
							?>
							<div class="error-text" data-error-for="<?php echo esc_attr($field['reg-field-name']); ?>"></div>
						</div>

					<?php } ?>

			<?php }} ?>
		</div>

		<div class="tf-booking-submission">
			<button class="booking-process tf-offline-booking">
				<?php esc_html_e("Continue to Pay", "tourfic"); ?>
				<i class="ri-arrow-right-s-line"></i>
			</button>
		</div>
	</div>
	

<?php
	wp_die();
}