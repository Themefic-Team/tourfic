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
		<div class="tf-other-infos tf-flex">
			<?php TF_Review::tf_archive_single_rating(); ?>
			<div class="tf-tags-box">
				<ul>
					<?php
					if(!empty($badges)){
					foreach($badges as $key => $badge){ 
					if(!empty($badge['title']) && $key < 4){
					?>
					<li>
						<?php if(!empty($badge['badge_icon'])){ ?>
						<i class="<?php echo esc_attr($badge['badge_icon']); ?>"></i>
						<?php } ?>
						<?php echo esc_html($badge['title']); ?>
					</li>
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
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g clip-path="url(#clip0_257_3930)">
						<path d="M13 2.24999C11.2177 1.55755 9.25142 1.49912 7.4311 2.0845C5.61079 2.66988 4.04716 3.86345 3.00251 5.46499C1.95787 7.06652 1.49576 8.97859 1.69372 10.8804C1.89167 12.7823 2.73764 14.5582 4.08972 15.9103C5.44179 17.2624 7.21771 18.1083 9.11955 18.3063C11.0214 18.5042 12.9335 18.0421 14.535 16.9975C16.1365 15.9528 17.3301 14.3892 17.9155 12.5689C18.5009 10.7486 18.4424 8.78233 17.75 6.99999M11.1667 8.83332L15.8333 4.16665M11.6667 9.99999C11.6667 10.9205 10.9205 11.6667 10 11.6667C9.07953 11.6667 8.33334 10.9205 8.33334 9.99999C8.33334 9.07951 9.07953 8.33332 10 8.33332C10.9205 8.33332 11.6667 9.07951 11.6667 9.99999Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</g>
					<defs>
						<clipPath id="clip0_257_3930">
						<rect width="20" height="20" fill="white"/>
						</clipPath>
					</defs>
					</svg>
					<?php echo $unlimited_mileage ? esc_html_e("Unlimited", "tourfic") : $total_mileage.' '.$mileage_type; ?>
				</li>

				<?php if(!empty($fuel_types)){ ?>
					<li class="tf-flex tf-flex-gap-8 tf-flex-align-center">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2.5 18.3334H12.5M3.33333 7.50002H11.6667M11.6667 18.3334V3.33335C11.6667 2.89133 11.4911 2.4674 11.1785 2.15484C10.866 1.84228 10.442 1.66669 10 1.66669H5C4.55797 1.66669 4.13405 1.84228 3.82149 2.15484C3.50893 2.4674 3.33333 2.89133 3.33333 3.33335V18.3334M11.6667 10.8334H13.3333C13.7754 10.8334 14.1993 11.0089 14.5118 11.3215C14.8244 11.6341 15 12.058 15 12.5V14.1667C15 14.6087 15.1756 15.0326 15.4882 15.3452C15.8007 15.6578 16.2246 15.8334 16.6667 15.8334C17.1087 15.8334 17.5326 15.6578 17.8452 15.3452C18.1577 15.0326 18.3333 14.6087 18.3333 14.1667V8.19169C18.3335 7.97176 18.2902 7.75397 18.2058 7.55088C18.1214 7.34779 17.9976 7.1634 17.8417 7.00835L15 4.16669" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>	
					<?php echo esc_html($fuel_types); ?>
					</li>
				<?php } ?>

				<?php if(!empty($engine_years)){ ?>
					<li class="tf-flex tf-flex-gap-8 tf-flex-align-center">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2 8V14" stroke="#566676" stroke-width="1.5" stroke-linecap="round"/>
					<path d="M13 2L7 2" stroke="#566676" stroke-width="1.5" stroke-linecap="round"/>
					<path d="M2 11H5" stroke="#566676" stroke-width="1.5" stroke-linecap="round"/>
					<path d="M10 2L10 5" stroke="#566676" stroke-width="1.5" stroke-linecap="round"/>
					<path d="M5 7V11V13C5 13.6295 5.29639 14.2223 5.8 14.6L8.46667 16.6C8.81286 16.8596 9.23393 17 9.66667 17H12.9296C13.5983 17 14.2228 16.6658 14.5937 16.1094L15.6132 14.5801C15.8549 14.2177 16.2616 14 16.6972 14C17.4167 14 18 13.4167 18 12.6972V10.2361C18 9.55341 17.4466 9 16.7639 9C16.2957 9 15.8677 8.73548 15.6584 8.31672L14.5528 6.10557C14.214 5.428 13.5215 5 12.7639 5H7C5.89543 5 5 5.89543 5 7Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round"/>
					</svg> 	
					<?php echo esc_html($engine_years); ?>
					</li>
				<?php } ?>

				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M16.6667 5V10H3.33337M10 5V15M3.33337 5V15" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M18.3334 3.33335C18.3334 3.77538 18.1578 4.1993 17.8452 4.51186C17.5326 4.82443 17.1087 5.00002 16.6667 5.00002C16.2247 5.00002 15.8007 4.82443 15.4882 4.51186C15.1756 4.1993 15 3.77538 15 3.33335C15 2.89133 15.1756 2.4674 15.4882 2.15484C15.8007 1.84228 16.2247 1.66669 16.6667 1.66669C17.1087 1.66669 17.5326 1.84228 17.8452 2.15484C18.1578 2.4674 18.3334 2.89133 18.3334 3.33335ZM11.6667 3.33335C11.6667 3.77538 11.4911 4.1993 11.1785 4.51186C10.866 4.82443 10.442 5.00002 10 5.00002C9.55799 5.00002 9.13407 4.82443 8.82151 4.51186C8.50895 4.1993 8.33335 3.77538 8.33335 3.33335C8.33335 2.89133 8.50895 2.4674 8.82151 2.15484C9.13407 1.84228 9.55799 1.66669 10 1.66669C10.442 1.66669 10.866 1.84228 11.1785 2.15484C11.4911 2.4674 11.6667 2.89133 11.6667 3.33335ZM5.00002 3.33335C5.00002 3.77538 4.82443 4.1993 4.51186 4.51186C4.1993 4.82443 3.77538 5.00002 3.33335 5.00002C2.89133 5.00002 2.4674 4.82443 2.15484 4.51186C1.84228 4.1993 1.66669 3.77538 1.66669 3.33335C1.66669 2.89133 1.84228 2.4674 2.15484 2.15484C2.4674 1.84228 2.89133 1.66669 3.33335 1.66669C3.77538 1.66669 4.1993 1.84228 4.51186 2.15484C4.82443 2.4674 5.00002 2.89133 5.00002 3.33335ZM11.6667 16.6667C11.6667 17.1087 11.4911 17.5326 11.1785 17.8452C10.866 18.1578 10.442 18.3334 10 18.3334C9.55799 18.3334 9.13407 18.1578 8.82151 17.8452C8.50895 17.5326 8.33335 17.1087 8.33335 16.6667C8.33335 16.2247 8.50895 15.8007 8.82151 15.4882C9.13407 15.1756 9.55799 15 10 15C10.442 15 10.866 15.1756 11.1785 15.4882C11.4911 15.8007 11.6667 16.2247 11.6667 16.6667ZM5.00002 16.6667C5.00002 17.1087 4.82443 17.5326 4.51186 17.8452C4.1993 18.1578 3.77538 18.3334 3.33335 18.3334C2.89133 18.3334 2.4674 18.1578 2.15484 17.8452C1.84228 17.5326 1.66669 17.1087 1.66669 16.6667C1.66669 16.2247 1.84228 15.8007 2.15484 15.4882C2.4674 15.1756 2.89133 15 3.33335 15C3.77538 15 4.1993 15.1756 4.51186 15.4882C4.82443 15.8007 5.00002 16.2247 5.00002 16.6667ZM16.6667 18.3334C17.1087 18.3334 17.5326 18.1578 17.8452 17.8452C18.1578 17.5326 18.3334 17.1087 18.3334 16.6667C18.3334 16.2247 18.1578 15.8007 17.8452 15.4882C17.5326 15.1756 17.1087 15 16.6667 15C16.2247 15 15.8007 15.1756 15.4882 15.4882C15.1756 15.8007 15 16.2247 15 16.6667C15 17.1087 15.1756 17.5326 15.4882 17.8452C15.8007 18.1578 16.2247 18.3334 16.6667 18.3334Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<?php echo $auto_transmission ? esc_html_e("Auto", "tourfic") : esc_html_e("Manual", "tourfic"); ?>
				</li>

				<?php if(!empty($passengers)){ ?>
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14.1667 3.75002C14.1667 4.91669 13.25 5.83335 12.0833 5.83335C10.9167 5.83335 10 4.91669 10 3.75002C10 2.58335 10.9167 1.66669 12.0833 1.66669C13.25 1.66669 14.1667 2.58335 14.1667 3.75002ZM12.5 6.66669H11.8333C10.0833 6.66669 8.41667 5.66669 7.58333 4.08335C7.5 4.00002 7.41667 3.91669 7.41667 3.83335L5.91667 4.50002C6.33333 5.66669 7.66667 7.16669 9.58333 7.91669L8.08333 12.0834L4.83333 11.1667L2.5 15.75L4.16667 16.1667L5.66667 13.1667L9.41667 14.1667C10.25 14.3334 11.0833 13.9167 11.4167 13.1667L13.3333 7.83335C13.5 7.25002 13.0833 6.66669 12.5 6.66669ZM15.75 5.83335L12.9167 13.6667C12.4167 15 11.1667 15.8334 9.83333 15.8334C9.58333 15.8334 9.25 15.8334 9 15.75L6.58333 15.0834L5.83333 16.5834L7.5 17L8.66667 17.3334C9.08333 17.4167 9.5 17.5 9.91667 17.5C12 17.5 13.8333 16.25 14.5833 14.25L17.5 5.83335H15.75Z" fill="#566676"/>
				</svg>	
				<?php echo esc_attr($passengers); ?> <?php esc_html_e("Person", "tourfic"); ?></li>
				<?php } ?>

        		<?php if(!empty($baggage)){ ?>
				<li class="tf-flex tf-flex-gap-8 tf-flex-align-center">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M10 10H10.0084M13.3334 5.00002V3.33335C13.3334 2.89133 13.1578 2.4674 12.8452 2.15484C12.5326 1.84228 12.1087 1.66669 11.6667 1.66669H8.33335C7.89133 1.66669 7.4674 1.84228 7.15484 2.15484C6.84228 2.4674 6.66669 2.89133 6.66669 3.33335V5.00002M18.3334 10.8334C15.8607 12.4658 12.963 13.3361 10 13.3361C7.03706 13.3361 4.13936 12.4658 1.66669 10.8334M3.33335 5.00002H16.6667C17.5872 5.00002 18.3334 5.74621 18.3334 6.66669V15C18.3334 15.9205 17.5872 16.6667 16.6667 16.6667H3.33335C2.41288 16.6667 1.66669 15.9205 1.66669 15V6.66669C1.66669 5.74621 2.41288 5.00002 3.33335 5.00002Z" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>	
				<?php echo esc_attr($baggage); ?> <?php esc_html_e("Bags", "tourfic"); ?></li>
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

function tf_getBestRefundPolicy($cancellations) {
    $bestPolicy = null;
    
    foreach ($cancellations as $cancellation) {
        // Check if it's a free cancellation
        if ($cancellation['cancellation_type'] === 'free') {
            // If we don't have a policy yet, or if this free cancellation has a longer time before cancellation
            if (!$bestPolicy || $cancellation['before_cancel_time'] > $bestPolicy['before_cancel_time']) {
                $bestPolicy = $cancellation;
            }
        }
    }

    // If no free cancellation was found, choose the best paid one
    if (!$bestPolicy) {
        foreach ($cancellations as $cancellation) {
            if ($cancellation['cancellation_type'] === 'paid') {
                // If we don't have a policy yet, or if this one has a higher refund amount
                if (!$bestPolicy || $cancellation['refund_amount'] > $bestPolicy['refund_amount']) {
                    $bestPolicy = $cancellation;
                }
            }
        }
    }

    return $bestPolicy;
}

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
	$car_calcellation_policy = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['calcellation_policy'] ) ? $meta['calcellation_policy'] : '';

	$pickup_date = ! empty( $_POST['pickup_date'] ) ? $_POST['pickup_date'] : '';
	$pickup_time = ! empty( $_POST['pickup_time'] ) ? $_POST['pickup_time'] : '';

	$bestRefundPolicy = tf_getBestRefundPolicy($car_calcellation_policy);

	$today = new DateTime(); // Current date and time
	$less_current_day = false;

	// Combine pickup date and time into a single string and convert it to a DateTime object
	$pickupDateTime = DateTime::createFromFormat('Y/m/d H:i', $pickup_date . ' ' . $pickup_time);

	// Extract the "before_cancel_time" and "cancellation-times" (hours, days, etc.)
	$beforeCancelTime = (int) $bestRefundPolicy['before_cancel_time'];
	$cancelTimeUnit = $bestRefundPolicy['cancellation-times']; // Could be 'hour', 'day', etc.

	// Adjust the pickup date and time based on the policy
	switch ($cancelTimeUnit) {
		case 'hour':
			$pickupDateTime->modify("-{$beforeCancelTime} hours");
			break;
		case 'day':
			$pickupDateTime->modify("-{$beforeCancelTime} days");
			break;
		// Add other cases as necessary (e.g., weeks, minutes, etc.)
	}

	// Compare calculated before date with the current day
	if ($pickupDateTime < $today) {
		// If the calculated cancellation time is before today, adjust it to the current date and time
		// $pickupDateTime = $today;
		$less_current_day = true;
	}

	// Get the final "before" date and time (ensured to not be before today)
	$beforeDate = $pickupDateTime->format('Y/m/d');
	$beforeTime = $pickupDateTime->format('H:i');

 	?>
	<?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !$less_current_day && !empty($bestRefundPolicy) ){ ?>
	<div class="tf-cancellation-notice">
		<span class="tf-flex tf-flex-align-center tf-flex-gap-16">
			<i class="ri-information-line"></i>
			<?php if('free'==$bestRefundPolicy['cancellation_type']){ ?> <b><?php esc_html_e("Free cancellation", "tourfic"); ?></b> <?php }else{ ?>
			<?php echo 'paid'==$bestRefundPolicy['cancellation_type'] && 'percent'==$bestRefundPolicy['refund_amount_type'] ? '<b>'.$bestRefundPolicy['refund_amount'].'% Cancellation fee</b>' : '<b>'.wc_price($bestRefundPolicy['refund_amount']).' Cancellation fee</b>'; ?>
			<?php } ?>
			<?php esc_html_e("before", "tourfic"); ?> <?php echo $beforeDate.' '.$beforeTime; ?>
		</span>
	</div>
	<?php } ?>

	<div class="tf-booking-tabs">
		<ul>
			<?php if(!empty($car_protection_section_status) && !empty($car_protections)){ ?>
				<li class="protection active"><?php esc_html_e("Protections", "tourfic"); ?></li>
			<?php } ?>
			<?php if(function_exists( 'is_tf_pro' ) && is_tf_pro() && $car_booking_by=='3'){ ?>
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
					<td align="center"></td>
					<td align="center"><?php esc_html_e("With protection", "tourfic"); ?></td>
				</tr>

				<?php 
				if(!empty($car_protections)){
					foreach($car_protections as $pkey => $protection){ ?>
					<tr>
						<th>
							<div class="tf-flex tf-flex-align-center">
								<div class="tf-protection-select">
									<input id="tf_single_protection_price" type="hidden" value="<?php echo !empty($protection['price']) ? esc_attr($protection['price']) : 0; ?> ">
									<label>
										<input type="checkbox" class="protection-checkbox" name="protections[]" value="<?php echo esc_attr($pkey); ?>">
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="tf-single-protection-title tf-flex">
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
							</div>
						</th>
						<td align="center">
							
						</td>
						<td align="center">
							<?php 
							if(!empty($protection['price'])){
								echo wc_price($protection['price']);
							}else{
								echo wc_price(0.0);
							}
							?>
							
						</td>
					</tr>
				<?php } } ?>


				<tfoot>
					<tr>
						<th width="50%" align="right"></th>
						<th align="center">
							<?php esc_html_e("Total", "tourfic"); ?>:
							<input type="hidden" id="tf_total_proteciton_price" value="0">
						</th>
						<th align="center" id="tf_proteciton_subtotal">
							<?php echo wc_price(0.0); ?>
						</th>
					</tr>
				</tfoot>

			</table>
		</div>
	</div>

	<div class="tf-booking-bar tf-flex tf-flex-gap-24">
		<button class="with-charge <?php echo function_exists( 'is_tf_pro' ) && is_tf_pro() && '3'==$car_booking_by ? esc_attr('booking-next') : esc_attr('booking-process'); ?>">
			<?php esc_html_e("Next", "tourfic"); ?>
			<i class="ri-arrow-right-s-line"></i>
		</button>
	</div>

	<?php } ?>
	
	<div class="tf-booking-form-fields" style="<?php echo function_exists( 'is_tf_pro' ) && is_tf_pro() && $car_booking_by=='3' && empty($car_protection_section_status) ? esc_attr('display: block') : ''; ?>">
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

add_action("admin_init", "tf_remove_sidebar_category_meta_box");
function tf_remove_sidebar_category_meta_box() {
	remove_meta_box( 'carrental_branddiv', array( 'tf_carrental' ), 'normal' );
}

add_action( 'wp_ajax_nopriv_tf_car_price_calculation', 'tf_car_price_calculation_callback' );
add_action( 'wp_ajax_tf_car_price_calculation', 'tf_car_price_calculation_callback' );
function tf_car_price_calculation_callback() {
	// Check nonce security
	if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
		return;
	}

	/**
	 * Get car meta values
	 */
	$post_id   = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
	$tf_pickup_date  = isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
	$tf_dropoff_date  = isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
	$tf_pickup_time  = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
	$tf_dropoff_time  = isset( $_POST['dropoff_time'] ) ? sanitize_text_field( $_POST['dropoff_time'] ) : '';
	$extra_ids  = isset( $_POST['extra_ids'] ) ? $_POST['extra_ids'] : '';
	$extra_qty  = isset( $_POST['extra_qty'] ) ? $_POST['extra_qty'] : '';

	$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
	$get_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
	$total_prices = $get_prices['sale_price'] ? $get_prices['sale_price'] : 0;

	if(!empty($extra_ids)){
		$total_extra = Pricing::set_extra_price($meta, $extra_ids, $extra_qty, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
		$total_prices = $total_prices + $total_extra['price'];
	}

	if(!empty($total_prices)){
		echo sprintf( esc_html__( 'Total: %1$s', 'tourfic' ), wc_price($total_prices) );
	}

	wp_die();
}