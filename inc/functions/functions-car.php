<?php
# don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Car_Rental\Pricing;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Icons_Manager;

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
$response = [];
$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
// Get meta safely
$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );

$car_extra = !empty($meta['extras']) ? $meta['extras'] : '';
// Extra key from POST
$car_extra_pass = isset( $_POST['extra_key'] ) ? sanitize_text_field( wp_unslash( $_POST['extra_key'] ) ) : '';
// Quantity from POST
$extra_qty = isset( $_POST['qty'] ) ? absint( wp_unslash( $_POST['qty'] ) ) : 0;
$pickup_date = !empty($_POST['pickup_date']) ? sanitize_text_field(wp_unslash($_POST['pickup_date'])) : '';
$dropoff_date = !empty($_POST['dropoff_date']) ? sanitize_text_field(wp_unslash($_POST['dropoff_date'])) : '';
$pickup_time = !empty($_POST['pickup_time']) ? sanitize_text_field(wp_unslash($_POST['pickup_time'])) : '';
$dropoff_time = !empty($_POST['dropoff_time']) ? sanitize_text_field(wp_unslash($_POST['dropoff_time'])) : '';

$get_prices = Pricing::set_total_price($meta, $pickup_date, $dropoff_date, $pickup_time, $dropoff_time);
$total_prices = $get_prices['sale_price'] ? $get_prices['sale_price'] : 0;

if(!empty($car_extra_pass)){
	$total_extra = Pricing::set_extra_price($meta, $pickup_date, $dropoff_date, $pickup_time, $dropoff_time, $car_extra_pass, $extra_qty);
	$total_prices = $total_prices + $total_extra['price'];
}
/* translators: %1$s will return total price */
$response['total_price'] = sprintf( esc_html__( 'Total: %1$s', 'tourfic' ), wc_price($total_prices) );

$total_days = 1;
if( !empty($pickup_date) && !empty($dropoff_date) && !empty($pickup_time) && !empty($dropoff_time) ){
	// Combine date and time
	$pickup_datetime = new \DateTime("$pickup_date $pickup_time");
	$dropoff_datetime = new \DateTime("$dropoff_date $dropoff_time");

	// Calculate the difference
	$interval = $pickup_datetime->diff($dropoff_datetime);

	// Get total days
	$total_days = $interval->days;
	
	// If there are leftover hours that count as a partial day
	if ($interval->h > 0 || $interval->i > 0) {
		$total_days += 1;  // Add an extra day for any remaining hours
	}
}
ob_start();
foreach($extra_qty as $key => $singleqty){
	if(!empty($singleqty)){

		$extra_key = $car_extra_pass[$key];
		$single_extra_info = !empty($car_extra[$extra_key]) ? $car_extra[$extra_key] : '';
		if(!empty($single_extra_info)){ ?>
			<div class="tf-single-added-extra tf-flex tf-flex-align-center tf-flex-space-bttn">
				<?php 
					if( 'day'==$single_extra_info['price_type'] && !empty($pickup_date) && !empty($dropoff_date) && !empty($pickup_time) && !empty($dropoff_time) ){
						$calday = $total_days;
					}else{
						$calday = 1;
					}
				?>
				<h4><?php echo !empty($single_extra_info['title']) ? esc_html($single_extra_info['title']) : ''; ?></h4>
				<div class="qty-price tf-flex tf-flex-space-bttn">
					<div class="line-sum tf-flex tf-flex-align-center">
						<i class="ri-close-line"></i> 
						<span class="qty"><?php echo esc_attr($singleqty); ?></span> 
						<span class="price"><?php echo !empty($single_extra_info['price']) ? wp_kses_post( wc_price( ($single_extra_info['price'] * $calday) * $singleqty) ) : ''; ?></span>
						</div>
					<span class="delete">
						<input type="hidden" value="<?php echo esc_attr($extra_key); ?>" name="selected_extra[]" />
						<input type="hidden" value="<?php echo esc_attr($singleqty); ?>" name="selected_qty[]" />
						<i class="ri-delete-bin-line"></i>
					</span>
				</div>
			</div>
		<?php
		}
	}
}

$response['extra'] = ob_get_clean();
wp_send_json( $response );
wp_die();
}

function tf_car_archive_single_item($pickup = '', $dropoff = '', $pickup_date = '', $dropoff_date = '', $pickup_time = '', $dropoff_time = '', $settings = []){
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

	//elementor settings
	$show_image = isset($settings['show_image']) ? $settings['show_image'] : 'yes';
	$promotional_tags = isset($settings['promotional_tags']) ? $settings['promotional_tags'] : 'yes';
	$show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
	$title_length = isset($settings['title_length']) ? absint($settings['title_length']) : 55;
	$show_review = isset($settings['show_review']) ? $settings['show_review'] : 'yes';
	$show_price = isset($settings['show_price']) ? $settings['show_price'] : 'yes';
	$car_infos = isset($settings['car_infos']) ? $settings['car_infos'] : ['mileage', 'fuel_type', 'engine_year', 'transmission_type', 'passenger_capacity', 'luggage_capacity'];
	$show_view_details = isset($settings['show_view_details']) ? $settings['show_view_details'] : 'yes';
	$view_details_text = isset($settings['view_details_text']) ? sanitize_text_field($settings['view_details_text']) : esc_html__('Details', 'tourfic');

	// Thumbnail
	$thumbnail_html = '';
	if ( !empty($settings) && $show_image == 'yes' ) {
		$settings[ 'image_size_customize' ] = [
			'id' => get_post_thumbnail_id(),
		];
		$settings['image_size_customize_size'] = $settings['image_size'];
		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );

		if ( "" === $thumbnail_html && 'yes' === $settings['show_fallback_img'] && !empty( $settings['fallback_img']['url'] ) ) {
			$settings[ 'image_size_customize' ] = [
				'id' => $settings['fallback_img']['id'],
			];
			$settings['image_size_customize_size'] = $settings['image_size'];
			$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );
		} elseif("" === $thumbnail_html && 'yes' !== $settings['show_fallback_img']) {
			$thumbnail_html = '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
		}
	}
?>
<div class="tf-single-car-view">
	<!-- Thumbnail -->
	<?php if($show_image == 'yes'): ?>
	<div class="tf-car-image">
		<?php
		if ( ! empty( $thumbnail_html ) ) {
			echo wp_kses_post( $thumbnail_html );
		} elseif ( has_post_thumbnail() ) {
			the_post_thumbnail( 'full' );
		} else {
			echo '<img src="' . esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" . '">';
		}
		?>
		<div class="tf-other-infos tf-flex">
			<!-- Review -->
			<?php if( $show_review == 'yes' ): ?>
				<?php TF_Review::tf_archive_single_rating(); ?>
			<?php endif; ?>

			<!-- Promotional Tags -->
			<?php if ( $promotional_tags == 'yes') : ?>
			<div class="tf-tags-box">
				<ul>
					<?php
					if(!empty($badges)){
					foreach($badges as $key => $badge){ 
					if(!empty($badge['title']) && $key < 4){
					?>
					<li>
						<?php echo esc_html($badge['title']); ?>
					</li>
					<?php }}} ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="tf-car-details" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">
		<div class="tf-car-content">
			<!-- Title -->
			<?php if( $show_title == 'yes' ): ?>
				<h3 class="tf-mb-24"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), $title_length ) ); ?></a></h3>
			<?php endif; ?>
			<ul class="list-items tf-mb-24">
				<?php if(!empty($car_infos) && is_array($car_infos) && in_array('mileage', $car_infos)) : ?>
				<li class="list">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g clip-path="url(#clip0_1049_4385)">
						<path d="M9.375 12.5L16.875 5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M4.40937 12.5C4.38648 12.2925 4.37501 12.0838 4.375 11.875C4.37576 10.9823 4.58879 10.1026 4.99652 9.30849C5.40425 8.51435 5.99499 7.82856 6.71999 7.3077C7.44498 6.78685 8.28345 6.44588 9.16618 6.31292C10.0489 6.17996 10.9506 6.25882 11.7969 6.54301" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M17.236 8.17496C17.7371 9.15824 18.0335 10.233 18.1071 11.3342C18.1807 12.4353 18.03 13.54 17.6641 14.5812C17.6214 14.7039 17.5415 14.8103 17.4355 14.8855C17.3295 14.9607 17.2027 15.001 17.0727 15.0007H2.92661C2.79641 15.0002 2.66959 14.9592 2.56366 14.8835C2.45773 14.8078 2.37791 14.7011 2.33521 14.5781C2.02273 13.6896 1.86703 12.7535 1.87505 11.8117C1.90943 7.34371 5.60396 3.71011 10.0782 3.74996C11.3392 3.76008 12.5805 4.06452 13.7032 4.63902" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</g>
					<defs>
						<clipPath id="clip0_1049_4385">
						<rect width="20" height="20" fill="white"/>
						</clipPath>
					</defs>
					</svg>
					<p><?php echo $unlimited_mileage ? esc_html__("Unlimited", "tourfic") : esc_html($total_mileage).' '.esc_html($mileage_type); ?></p>
				</li>
				<?php endif; ?>

				<?php if(!empty($fuel_types) && !empty($car_infos) && is_array($car_infos) && in_array('fuel_type', $car_infos)){ ?>
					<li class="list">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g clip-path="url(#clip0_1055_4099)">
						<path d="M11.6667 9.16667H12.5001C12.9421 9.16667 13.366 9.34226 13.6786 9.65482C13.9912 9.96738 14.1667 10.3913 14.1667 10.8333V13.3333C14.1667 13.6649 14.2984 13.9828 14.5329 14.2172C14.7673 14.4516 15.0852 14.5833 15.4167 14.5833C15.7483 14.5833 16.0662 14.4516 16.3006 14.2172C16.5351 13.9828 16.6667 13.6649 16.6667 13.3333V7.5L14.1667 5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M3.33325 16.6667V5.00004C3.33325 4.55801 3.50885 4.13409 3.82141 3.82153C4.13397 3.50897 4.55789 3.33337 4.99992 3.33337H9.99992C10.4419 3.33337 10.8659 3.50897 11.1784 3.82153C11.491 4.13409 11.6666 4.55801 11.6666 5.00004V16.6667" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M2.5 16.6666H12.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M15 5.83337V6.66671C15 6.88772 15.0878 7.09968 15.2441 7.25596C15.4004 7.41224 15.6123 7.50004 15.8333 7.50004H16.6667" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M3.33325 9.16663H11.6666" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</g>
					<defs>
						<clipPath id="clip0_1055_4099">
						<rect width="20" height="20" fill="white"/>
						</clipPath>
					</defs>
					</svg>
					<p><?php echo esc_html($fuel_types); ?></p>
					</li>
				<?php } ?>

				<?php if(!empty($engine_years) && !empty($car_infos) && is_array($car_infos) && in_array('engine_year', $car_infos)){ ?>
					<li class="list">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 16.5C12 16.697 12.0388 16.8921 12.1142 17.074C12.1895 17.256 12.3001 17.4214 12.4393 17.5607C12.5786 17.6999 12.744 17.8105 12.926 17.8858C13.1079 17.9612 13.303 18 13.5 18C13.697 18 13.8921 17.9612 14.074 17.8858C14.256 17.8105 14.4214 17.6999 14.5607 17.5607C14.6999 17.4214 14.8105 17.256 14.8858 17.074C14.9612 16.8921 15 16.697 15 16.5C15 16.303 14.9612 16.1079 14.8858 15.926C14.8105 15.744 14.6999 15.5786 14.5607 15.4393C14.4214 15.3001 14.256 15.1895 14.074 15.1142C13.8921 15.0388 13.697 15 13.5 15C13.303 15 13.1079 15.0388 12.926 15.1142C12.744 15.1895 12.5786 15.3001 12.4393 15.4393C12.3001 15.5786 12.1895 15.744 12.1142 15.926C12.0388 16.1079 12 16.303 12 16.5Z" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M4 16.5C4 16.8978 4.15803 17.2793 4.43934 17.5607C4.72065 17.8419 5.10217 18 5.5 18C5.89783 18 6.27935 17.8419 6.56066 17.5607C6.84197 17.2793 7 16.8978 7 16.5C7 16.1022 6.84197 15.7207 6.56066 15.4393C6.27935 15.1581 5.89783 15 5.5 15C5.10217 15 4.72065 15.1581 4.43934 15.4393C4.15803 15.7207 4 16.1022 4 16.5Z" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M12 17H7" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M3.5048 17H2.2C1.88174 17 1.57651 16.8796 1.35147 16.6653C1.12643 16.451 1 16.1602 1 15.8571V14.7143C1 14.1081 1.25286 13.5267 1.70294 13.0981C2.15303 12.6694 2.76348 12.4286 3.4 12.4286L4.868 9.63162C4.9677 9.44175 5.12096 9.28213 5.31058 9.17059C5.50021 9.05897 5.71871 8.99992 5.9416 9H9.8616C10.0845 8.99992 10.303 9.05897 10.4926 9.17059C10.6822 9.28213 10.8355 9.44175 10.9352 9.63162L12.4 12.4286H16.6C17.2365 12.4286 17.847 12.6694 18.297 13.0981C18.7471 13.5267 19 14.1081 19 14.7143V15.8571C19 16.1602 18.8736 16.451 18.6486 16.6653C18.4235 16.8796 18.1182 17 17.8 17H15.296" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M12 12H4" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M12 12L15.6 9H18" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M18.5 6C18.3357 5.82421 18.2063 5.61201 18.1204 5.3771C18.0344 5.1422 17.9936 4.88982 18.0008 4.6363C18.0008 3.72741 18.9992 3.27259 18.9992 2.3637C19.0063 2.11018 18.9657 1.8578 18.8797 1.6229C18.7937 1.38799 18.6643 1.17578 18.5 1" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M14.5 7C14.3357 6.82421 14.2063 6.61201 14.1204 6.3771C14.0344 6.1422 13.9936 5.88982 14.0008 5.6363C14.0008 4.72741 14.9992 4.27259 14.9992 3.3637C15.0063 3.11018 14.9657 2.8578 14.8797 2.6229C14.7937 2.38799 14.6643 2.17579 14.5 2" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<p><?php echo esc_html($engine_years); ?></p>
					</li>
				<?php } ?>

				<?php if(!empty($car_infos) && is_array($car_infos) && in_array('transmission_type', $car_infos)) : ?>
				<li class="list">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g clip-path="url(#clip0_1049_4515)">
					<path d="M2.5 5.00004C2.5 5.44207 2.67559 5.86599 2.98816 6.17855C3.30072 6.49111 3.72464 6.66671 4.16667 6.66671C4.60869 6.66671 5.03262 6.49111 5.34518 6.17855C5.65774 5.86599 5.83333 5.44207 5.83333 5.00004C5.83333 4.55801 5.65774 4.13409 5.34518 3.82153C5.03262 3.50897 4.60869 3.33337 4.16667 3.33337C3.72464 3.33337 3.30072 3.50897 2.98816 3.82153C2.67559 4.13409 2.5 4.55801 2.5 5.00004Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M8.33325 5.00004C8.33325 5.44207 8.50885 5.86599 8.82141 6.17855C9.13397 6.49111 9.55789 6.66671 9.99992 6.66671C10.4419 6.66671 10.8659 6.49111 11.1784 6.17855C11.491 5.86599 11.6666 5.44207 11.6666 5.00004C11.6666 4.55801 11.491 4.13409 11.1784 3.82153C10.8659 3.50897 10.4419 3.33337 9.99992 3.33337C9.55789 3.33337 9.13397 3.50897 8.82141 3.82153C8.50885 4.13409 8.33325 4.55801 8.33325 5.00004Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M14.1667 5.00004C14.1667 5.44207 14.3423 5.86599 14.6549 6.17855C14.9675 6.49111 15.3914 6.66671 15.8334 6.66671C16.2754 6.66671 16.6994 6.49111 17.0119 6.17855C17.3245 5.86599 17.5001 5.44207 17.5001 5.00004C17.5001 4.55801 17.3245 4.13409 17.0119 3.82153C16.6994 3.50897 16.2754 3.33337 15.8334 3.33337C15.3914 3.33337 14.9675 3.50897 14.6549 3.82153C14.3423 4.13409 14.1667 4.55801 14.1667 5.00004Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M2.5 15C2.5 15.4421 2.67559 15.866 2.98816 16.1786C3.30072 16.4911 3.72464 16.6667 4.16667 16.6667C4.60869 16.6667 5.03262 16.4911 5.34518 16.1786C5.65774 15.866 5.83333 15.4421 5.83333 15C5.83333 14.558 5.65774 14.1341 5.34518 13.8215C5.03262 13.509 4.60869 13.3334 4.16667 13.3334C3.72464 13.3334 3.30072 13.509 2.98816 13.8215C2.67559 14.1341 2.5 14.558 2.5 15Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M8.33325 15C8.33325 15.4421 8.50885 15.866 8.82141 16.1786C9.13397 16.4911 9.55789 16.6667 9.99992 16.6667C10.4419 16.6667 10.8659 16.4911 11.1784 16.1786C11.491 15.866 11.6666 15.4421 11.6666 15C11.6666 14.558 11.491 14.1341 11.1784 13.8215C10.8659 13.509 10.4419 13.3334 9.99992 13.3334C9.55789 13.3334 9.13397 13.509 8.82141 13.8215C8.50885 14.1341 8.33325 14.558 8.33325 15Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M4.16675 6.66663V13.3333" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M10 6.66663V13.3333" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M15.8334 6.66663V8.33329C15.8334 8.77532 15.6578 9.19924 15.3453 9.5118C15.0327 9.82436 14.6088 9.99996 14.1667 9.99996H4.16675" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</g>
				<defs>
					<clipPath id="clip0_1049_4515">
					<rect width="20" height="20" fill="white"/>
					</clipPath>
				</defs>
				</svg>
				<p><?php echo $auto_transmission ? esc_html__("Auto", "tourfic") : esc_html__("Manual", "tourfic"); ?></p>
				</li>
				<?php endif; ?>

				<?php if(!empty($passengers) && !empty($car_infos) && is_array($car_infos) && in_array('passenger_capacity', $car_infos)){ ?>
				<li class="list">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g clip-path="url(#clip0_1050_4532)">
					<path d="M6.66675 5.83333C6.66675 6.71739 7.01794 7.56523 7.64306 8.19036C8.26818 8.81548 9.11603 9.16667 10.0001 9.16667C10.8841 9.16667 11.732 8.81548 12.3571 8.19036C12.9822 7.56523 13.3334 6.71739 13.3334 5.83333C13.3334 4.94928 12.9822 4.10143 12.3571 3.47631C11.732 2.85119 10.8841 2.5 10.0001 2.5C9.11603 2.5 8.26818 2.85119 7.64306 3.47631C7.01794 4.10143 6.66675 4.94928 6.66675 5.83333Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M5 17.5V15.8333C5 14.9493 5.35119 14.1014 5.97631 13.4763C6.60143 12.8512 7.44928 12.5 8.33333 12.5H11.6667C12.5507 12.5 13.3986 12.8512 14.0237 13.4763C14.6488 14.1014 15 14.9493 15 15.8333V17.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</g>
				<defs>
					<clipPath id="clip0_1050_4532">
					<rect width="20" height="20" fill="white"/>
					</clipPath>
				</defs>
				</svg>
				<p><?php echo esc_attr($passengers); ?> <?php esc_html_e("Person", "tourfic"); ?></p>
				</li>
				<?php } ?>

        		<?php if(!empty($baggage) && !empty($car_infos) && is_array($car_infos) && in_array('luggage_capacity', $car_infos)){ ?>
				<li class="list">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M10.0003 5.19983C7.893 5.19983 5.69021 5.19983 3.84363 5.52986C2.9585 5.68804 2.26121 6.36397 2.07119 7.24281C1.77173 8.62763 1.77173 9.91936 1.77173 11.8855C1.77173 13.8517 1.77173 15.1434 2.07119 16.5283C2.26121 17.4071 2.9585 18.083 3.84363 18.2413C5.69021 18.5713 7.893 18.5713 10.0003 18.5713C12.1076 18.5713 14.3104 18.5713 16.157 18.2413C17.0421 18.083 17.7394 17.4071 17.9294 16.5283C18.2288 15.1434 18.2288 13.8517 18.2288 11.8855C18.2288 9.91936 18.2288 8.62763 17.9294 7.24281C17.7394 6.36397 17.0421 5.68804 16.157 5.52986C14.3104 5.19983 12.1076 5.19983 10.0003 5.19983Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M6.5708 5.20002V4.28573C6.5708 2.70777 7.84999 1.42859 9.42794 1.42859H10.5708C12.1488 1.42859 13.4279 2.70777 13.4279 4.28573V5.20002" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M1.78149 10L9.10039 12.4432C9.68761 12.6393 10.3226 12.6393 10.9098 12.4432L18.2287 10" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<p><?php echo esc_attr($baggage); ?> <?php esc_html_e("Bags", "tourfic"); ?></p>
				</li>
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
			<!-- Price -->
			<?php if($show_price == 'yes') : ?>
			<div class="tf-price-info">
				<?php
				$total_prices = Pricing::set_total_price($meta, $pickup_date, $dropoff_date, $pickup_time, $dropoff_time);
				?>
				<h3><?php echo $total_prices['sale_price'] ? wp_kses_post(wc_price($total_prices['sale_price'])) : '' ?> <small>/ <?php echo esc_html($total_prices['type']); ?></small></h3>
			</div>
			<?php endif; ?>

			<!-- View Details -->
			<?php if($show_view_details == 'yes') : ?>
			<?php if(!empty($pickup_date) && !empty($dropoff_date)){ ?>
				<input type="hidden" value="<?php echo esc_attr($pickup_date); ?>" id="pickup_date">
				<input type="hidden" value="<?php echo esc_attr($dropoff_date); ?>" id="dropoff_date">
				<input type="hidden" value="<?php echo esc_attr($pickup_time); ?>" id="pickup_time">
				<input type="hidden" value="<?php echo esc_attr($dropoff_time); ?>" id="dropoff_time">
				<input type="hidden" value="<?php echo esc_attr($post_id); ?>" id="post_id">
				<?php if('2'==$car_booking_by){ ?>
					<button class="quick-booking"><?php echo esc_html( $view_details_text ); ?></button>
				<?php }else{ ?>
					<button class="<?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('quick-booking') : esc_attr('tf-car-quick-booking'); ?>"><?php echo esc_html( $view_details_text ); ?></button>
				<?php } ?>
			<?php }else{ ?>
				<a class="view-more" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $view_details_text ); ?></a>
			<?php } ?>
			<?php endif; ?>
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

function tf_car_availability_response($car_meta, array &$not_found, $pickup='', $dropoff='', $tf_pickup_date='', $tf_dropoff_date='', $tf_pickup_time='', $tf_dropoff_time='', $tf_startprice='', $tf_endprice='') {

	$has_car = false;
	$pricing_type = !empty($car_meta["pricing_type"]) ? $car_meta["pricing_type"] : 'day_hour';
	$price_by = !empty($car_meta["price_by"]) ? $car_meta["price_by"] : 'day';
	
	$date_pricing = !empty($car_meta["date_prices"]) ? $car_meta["date_prices"] : '';
	$day_pricing = !empty($car_meta["day_prices"]) ? $car_meta["day_prices"] : '';

	$custom_availability = !empty($car_meta["custom_availability"]) ? $car_meta["custom_availability"] : '0';
	$pricing_type = !empty($car_meta["pricing_type"]) ? $car_meta["pricing_type"] : 'day_hour';
	$base_price = !empty($car_meta["car_rent"]) ? $car_meta["car_rent"] : 0;

	if(!empty($tf_startprice) && !empty($tf_endprice) && $custom_availability == '0' && ('day' == $price_by || 'hour' == $price_by) ){
		if ( ! empty( $base_price ) && $tf_startprice <= $base_price && $base_price <= $tf_endprice ) {
			$has_car = true;
		} else {
			$has_car = false;
		}
	}elseif( !empty($tf_pickup_date) && !empty($tf_dropoff_date) && 'date'==$pricing_type && !empty($date_pricing) ){

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
	function get_cars_min_max_price( $post_id = ''){
		$tf_car_min_max = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_carrental',
			'post_status'    => 'publish'
		);

		if( !empty($post_id) && is_numeric($post_id) ){
			$tf_car_min_max['p'] = $post_id;
		}

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
		wp_reset_postdata();
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

		if('on'==$_POST['same_location']){ // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$_POST['dropoff-name'] = !empty($_POST['pickup-name']) ? sanitize_text_field( wp_unslash($_POST['pickup-name']) ) : '';
			$_POST['dropoff'] = !empty($_POST['pickup']) ? sanitize_text_field( wp_unslash($_POST['pickup']) ) : '';
		}

		if ( Helper::tfopt( 'pick_drop_car_search' ) && (empty( $_POST['pickup-name'] ) || empty( $_POST['dropoff-name'] )) ) {
			$response['message'] = esc_html__( 'Please enter Pickup & Dropoff location', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		} elseif ( Helper::tfopt( 'pick_drop_date_car_search' ) && (empty( $_POST['pickup-date'] ) || empty( $_POST['dropoff-date'] )) ) {
			$response['message'] = esc_html__( 'Please Select Pickup & Dropoff date', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		}

		// Whitelist allowed fields
		$allowed_fields = [
			'pickup-name',
			'pickup',
			'dropoff-name',
			'dropoff',
			'pickup-date',
			'pickup-time',
			'dropoff-date',
			'dropoff-time',
			'type',
			'from',
			'to',
			'_nonce',
		];

		$fields = [];
		foreach ( $allowed_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				if ( is_array( $_POST[ $key ] ) ) {
					$fields[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) );
				} else {
					$fields[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
				}
			}
		}

		$response['query_string'] = http_build_query( $fields );
		$response['status']       = 'success';

		echo wp_json_encode( $response );
		wp_die();
	}
}


/**
 * Booking Popup
 * @author Jahid
 */

function tf_getBestRefundPolicy($cancellations, $pickup_date, $pickup_time) {
    $bestPolicy = null;

	$tf_default_time_zone = ! empty( Helper::tfopt( 'cancellation_time_zone' ) ) ? Helper::tfopt( 'cancellation_time_zone' ) : 'America/New_York';
	$timezone = new DateTimeZone($tf_default_time_zone);

	$today = new DateTime('now', $timezone);
	$pickupDateTime = DateTime::createFromFormat('Y/m/d H:i', $pickup_date . ' ' . $pickup_time, $timezone);

	if($today < $pickupDateTime){
		$interval = $today->diff($pickupDateTime);
		// Get days and hours separately
		$days = $interval->days;
		$hours = $interval->h;
	}

	if(!empty($cancellations)){
		foreach ($cancellations as $cancellation) {
			if('day'==$cancellation['cancellation-times']){
				// Check if it's a free cancellation
				if ($cancellation['cancellation_type'] === 'free' && !empty($days) && $days > $cancellation['before_cancel_time']) {
					// If we don't have a policy yet, or if this free cancellation has a longer time before cancellation
					if (!$bestPolicy || $cancellation['before_cancel_time'] > $bestPolicy['before_cancel_time']) {
						$bestPolicy = $cancellation;
					}
				}
			}
			if('hour'==$cancellation['cancellation-times']){
				// Check if it's a free cancellation
				if ($cancellation['cancellation_type'] === 'free' && !empty($hours) && $hours > $cancellation['before_cancel_time']) {
					// If we don't have a policy yet, or if this free cancellation has a longer time before cancellation
					if (!$bestPolicy || $cancellation['before_cancel_time'] > $bestPolicy['before_cancel_time']) {
						$bestPolicy = $cancellation;
					}
				}
			}
		}
	}

    // If no free cancellation was found, choose the best paid one
    if (!$bestPolicy) {
		if(!empty($cancellations)){
			foreach ($cancellations as $cancellation) {
				if ($cancellation['cancellation_type'] === 'paid') {
					// If we don't have a policy yet, or if this one has a higher refund amount
					if (!$bestPolicy || $cancellation['refund_amount'] > $bestPolicy['refund_amount']) {
						$bestPolicy = $cancellation;
					}
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
	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
	// Booking
	$car_booking_by = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : '1';

	// Protection
	$car_protection_section_status = ! empty( $meta['protection_section'] ) ? $meta['protection_section'] : '';
	$car_protection_content = ! empty( $meta['protection_content'] ) ? $meta['protection_content'] : '';
	$car_protections = ! empty( $meta['protections'] ) ? $meta['protections'] : '';
	$car_protection_tab_title = ! empty( $meta['protection_tab_title'] ) ? esc_html($meta['protection_tab_title']) : esc_html('Protection');

	$pickup_date = ! empty( $_POST['pickup_date'] ) ? sanitize_text_field( wp_unslash($_POST['pickup_date']) ) : '';
	$pickup_time = ! empty( $_POST['pickup_time'] ) ? sanitize_text_field( wp_unslash($_POST['pickup_time']) ) : '';

	$dropoff_date = ! empty( $_POST['dropoff_date'] ) ? sanitize_text_field( wp_unslash($_POST['dropoff_date']) ) : '';
	$dropoff_time = ! empty( $_POST['dropoff_time'] ) ? sanitize_text_field( wp_unslash($_POST['dropoff_time']) ) : '';



 	?>

	<div class="tf-booking-tabs">
		<ul>
			<?php if(!empty($car_protection_section_status) && !empty($car_protections)){ ?>
				<li class="protection active"><?php echo esc_html($car_protection_tab_title); ?></li>
			<?php } ?>
			<?php if( $car_booking_by=='3'){ ?>
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
					<td><?php esc_html_e("What is covered", "tourfic"); ?></td>
					<td align="center"><?php esc_html_e("With protection", "tourfic"); ?></td>
				</tr>

				<?php 
				if(!empty($car_protections)){
					foreach($car_protections as $pkey => $protection){ ?>
					<tr>
						<th>
							<?php
							if( 'day' == $protection['price_by'] ){
								// Combine date and time
								$pickup_datetime = new \DateTime("$pickup_date $pickup_time");
								$dropoff_datetime = new \DateTime("$dropoff_date $dropoff_time");
				
								// Calculate the difference
								$interval = $pickup_datetime->diff($dropoff_datetime);
				
								// Get total days
								$total_days = $interval->days;
											
								// If there are leftover hours that count as a partial day
								if ($interval->h > 0 || $interval->i > 0) {
									$total_days += 1;  // Add an extra day for any remaining hours
								}
							}else{
								$total_days = 1;
							}
							?>
							<div class="tf-flex tf-flex-align-center">
								<div class="tf-protection-select">
									<input id="tf_single_protection_price" type="hidden" value="<?php echo !empty($protection['price']) ? esc_attr($protection['price'] * $total_days) : 0; ?> ">
									<label>
										<input type="checkbox" class="protection-checkbox" name="protections[]" value="<?php echo esc_attr($pkey); ?>" <?php echo !empty($protection["protection_required"]) ? 'data-required=1' : '' ?>>
										<span class="tf-checkmark"></span>
									</label>
								</div>
								<div class="tf-single-protection-title tf-flex">
								<?php echo !empty($protection['title']) ? esc_html($protection['title']) : ''; ?> (<?php esc_html_e("Per ", "tourfic"); ?><?php echo esc_html($protection['price_by']); ?>) <?php echo !empty($protection["protection_required"]) ? esc_html('*') : ''; ?> 
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
							<?php 
							if(!empty($protection['price'])){
								echo wp_kses_post(wc_price($protection['price']));
							}else{
								echo wp_kses_post(wc_price(0.0));
							}
							?>
							
						</td>
					</tr>
				<?php } } ?>


				<tfoot>
					<tr>
						<th align="center">
							<?php esc_html_e("Total", "tourfic"); ?>:
							<input type="hidden" id="tf_total_proteciton_price" value="0">
						</th>
						<th align="center" id="tf_proteciton_subtotal">
							<?php echo wp_kses_post(wc_price(0.0)); ?>
						</th>
					</tr>
				</tfoot>

			</table>
		</div>
	</div>

	<div class="tf-booking-bar tf-flex tf-flex-gap-24">
		<button class="with-charge <?php echo '3'==$car_booking_by ? esc_attr('booking-next') : esc_attr('booking-process'); ?>">
			<?php esc_html_e("Next", "tourfic"); ?>
			<i class="ri-arrow-right-s-line"></i>
		</button>
	</div>

	<?php } ?>
	
	<div class="tf-booking-form-fields" style="<?php echo $car_booking_by=='3' && empty($car_protection_section_status) ? esc_attr('display: block') : ''; ?>">
		<div class="tf-form-fields tf-flex tf-flex-gap-24 tf-flex-w">
			<?php 
			$traveller_info_fields = ! empty( Helper::tf_data_types( Helper::tfopt( 'car-book-confirm-field' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'car-book-confirm-field' ) ) : '';

			if(empty($traveller_info_fields)){
			?>
			<div class="tf-single-field">
				<label for="tf_first_name"><?php esc_html_e("First Name", "tourfic"); ?></label>
				<input type="text" placeholder="First Name" id="tf_first_name" name="traveller[tf_first_name]" data-required="1">
				<div class="error-text" data-error-for="tf_first_name"></div>
			</div>
			<div class="tf-single-field">
				<label for="tf_last_name"><?php esc_html_e("Last Name", "tourfic"); ?></label>
				<input type="text" placeholder="Name" id="tf_last_name" name="traveller[tf_last_name]" data-required="1">
				<div class="error-text" data-error-for="tf_last_name"></div>
			</div>
			<div class="tf-single-field">
				<label for="tf_email"><?php esc_html_e("Email", "tourfic"); ?></label>
				<input type="text" placeholder="Email" id="tf_email" name="traveller[tf_email]" data-required="1">
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
	remove_meta_box( 'carrental_fuel_typediv', array( 'tf_carrental' ), 'normal' );
	remove_meta_box( 'carrental_engine_yeardiv', array( 'tf_carrental' ), 'normal' );
}

// tf refund Policy
function tf_getRefundPolicy($cancellations, $pickup_date, $pickup_time) {
    $freePolicies = [];
    $bestPaidPolicy = null;

    $tf_default_time_zone = !empty(Helper::tfopt('cancellation_time_zone')) ? Helper::tfopt('cancellation_time_zone') : 'America/New_York';
    $timezone = new DateTimeZone($tf_default_time_zone);

    $today = new DateTime('now', $timezone);
    $pickupDateTime = DateTime::createFromFormat('Y/m/d H:i', $pickup_date . ' ' . $pickup_time, $timezone);

    if ($today < $pickupDateTime) {
        $interval = $today->diff($pickupDateTime);
        // Get days and hours separately
        $days = $interval->days;
        $hours = $interval->h;
    }

	if(!empty($cancellations)){
		foreach ($cancellations as $cancellation) {
			$timeType = $cancellation['cancellation-times'];
			$cancelTime = (int)$cancellation['before_cancel_time'];

			// Normalize time for comparison
			$timeAvailable = 0;
			if ($timeType === 'day') {
				$timeAvailable = $days ?? 0;
			} elseif ($timeType === 'hour') {
				$timeAvailable = ($days ?? 0) * 24 + ($hours ?? 0); // Convert days to hours and add
			}

			// Check if it's a free cancellation
			if ($cancellation['cancellation_type'] === 'free' && $timeAvailable > $cancelTime) {
				$freePolicies[] = $cancellation;
			}

			// Check if it's a paid cancellation
			if ($cancellation['cancellation_type'] === 'paid' && $timeAvailable > $cancelTime) {
				// Select the best paid policy based on the lowest refund amount or longest time
				if (
					!$bestPaidPolicy ||
					$cancelTime > $bestPaidPolicy['before_cancel_time'] ||
					($cancelTime === $bestPaidPolicy['before_cancel_time'] && $cancellation['refund_amount'] < $bestPaidPolicy['refund_amount'])
				) {
					$bestPaidPolicy = $cancellation;
				}
			}
		}
	}

    // Sort free policies by cancellation time (descending)
    usort($freePolicies, function ($a, $b) {
        return $b['before_cancel_time'] - $a['before_cancel_time'];
    });

    // Prepare the final result
    $bestPolicies = array_slice($freePolicies, 0, 2); // Take up to 2 free policies

    // If less than 2 policies and there's a paid policy, add it
    if (count($bestPolicies) < 2 && $bestPaidPolicy) {
        $bestPolicies[] = $bestPaidPolicy;
    }

    return $bestPolicies;
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
	$post_id   = isset( $_POST['post_id'] ) ? intval( sanitize_text_field(wp_unslash( $_POST['post_id'] )) ) : null;
	$tf_pickup_date  = isset( $_POST['pickup_date'] ) ? sanitize_text_field(wp_unslash( $_POST['pickup_date'] )) : '';
	$tf_dropoff_date  = isset( $_POST['dropoff_date'] ) ? sanitize_text_field(wp_unslash( $_POST['dropoff_date'] )) : '';
	$tf_pickup_time  = isset( $_POST['pickup_time'] ) ? sanitize_text_field(wp_unslash( $_POST['pickup_time'] )) : '';
	$tf_dropoff_time  = isset( $_POST['dropoff_time'] ) ? sanitize_text_field(wp_unslash( $_POST['dropoff_time'] )) : '';


	$extra_ids = isset( $_POST['extra_ids'] ) && is_array( $_POST['extra_ids'] )
    ? array_map( 'sanitize_text_field', wp_unslash( $_POST['extra_ids'] ) )
    : [];
	$extra_qty = isset( $_POST['extra_qty'] ) && is_array( $_POST['extra_qty'] )
    ? array_map( 'sanitize_text_field', wp_unslash( $_POST['extra_qty'] ) )
    : [];

	$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
	$get_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);

	$total_prices = $get_prices['sale_price'] ? $get_prices['sale_price'] : 0;

	if(!empty($extra_ids)){
		$total_extra = Pricing::set_extra_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time, $extra_ids, $extra_qty);
		$total_prices = $total_prices + $total_extra['price'];
	}
	
	$car_calcellation_policy = ! empty( $meta['calcellation_policy'] ) ? $meta['calcellation_policy'] : '';
	$bestRefundPolicy = tf_getBestRefundPolicy($car_calcellation_policy, $tf_pickup_date, $tf_pickup_time);
	$twobestRefundPolicy = tf_getRefundPolicy($car_calcellation_policy, $tf_pickup_date, $tf_pickup_time);
	
	$tf_default_time_zone = ! empty( Helper::tfopt( 'cancellation_time_zone' ) ) ? Helper::tfopt( 'cancellation_time_zone' ) : 'America/New_York';
	$timezone = new DateTimeZone($tf_default_time_zone);

	$today = new DateTime('now', $timezone);
	$less_current_day = false;

	// Combine pickup date and time into a single string and convert it to a DateTime object
	$pickupDateTime = DateTime::createFromFormat('Y/m/d H:i', $tf_pickup_date . ' ' . $tf_pickup_time, $timezone);

	// Extract the "before_cancel_time" and "cancellation-times" (hours, days, etc.)
	$beforeCancelTime = !empty($bestRefundPolicy['before_cancel_time']) ? (int) $bestRefundPolicy['before_cancel_time'] : 0;
	$cancelTimeUnit = !empty($bestRefundPolicy['cancellation-times']) ? $bestRefundPolicy['cancellation-times'] : '';// Could be 'hour', 'day', etc.

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
		$less_current_day = true;
	}


	// Get the final "before" date and time (ensured to not be before today)
	$beforeDate = $pickupDateTime->format('Y/m/d');
	$beforeTime = $pickupDateTime->format('H:i');


	$cancellation = '';
	
	if( !$less_current_day && !empty($bestRefundPolicy) ){
		if ( isset( $bestRefundPolicy['cancellation_type'] ) ) {
			// Determine cancellation message
			if ( $bestRefundPolicy['cancellation_type'] === 'free' ) {
				$cancellation_message = esc_html__( "Free cancellation", "tourfic" );
			} elseif ( $bestRefundPolicy['cancellation_type'] === 'paid' ) {
				if ( $bestRefundPolicy['refund_amount_type'] === 'percent' ) {
					$cancellation_message = esc_html( $bestRefundPolicy['refund_amount'] ) . '% ' . esc_html__( "Cancellation fee", "tourfic" );
				} else {
					$cancellation_message = wc_price( $bestRefundPolicy['refund_amount'] ) . ' ' . esc_html__( "Cancellation fee", "tourfic" );
				}
			} else {
				$cancellation_message = esc_html__( "Cancellation policy not specified", "tourfic" );
			}
		
			// Construct the HTML dynamically
			$cancellation .= '
			<span class="tf-flex tf-flex-align-center tf-flex-gap-4">
				<i class="ri-information-line"></i>
				<b>' . $cancellation_message . '</b>
				' . esc_html__( "before ", "tourfic" ) . esc_html( $beforeDate ) . ' ' . esc_html( $beforeTime ) . '
			</span>';
		}		
	}

	if( !$less_current_day && !empty($bestRefundPolicy) ){
    $cancellation .= '
    <div class="tf-cancellation-timeline">
        <div class="tf-timeline">
            <ul>';
            
			// Add timeline items dynamically based on the best refund policies
			$twobestRefundPolicy = tf_getRefundPolicy($car_calcellation_policy, $tf_pickup_date, $tf_pickup_time);
			if (!empty($twobestRefundPolicy)) {
				foreach ($twobestRefundPolicy as $policy) {
					$policyType = $policy['cancellation_type'] === 'free' ? esc_html__('Fully refundable', 'tourfic') : esc_html__('Charged', 'tourfic');
					$policyTime = $policy['before_cancel_time'] . ' ' . $policy['cancellation-times'] === 'day' ? esc_html__('days', 'tourfic') : esc_html__('hours', 'tourfic');
					$refundAmount = !empty($policy['refund_amount']) ? $policy['refund_amount'] . ' ' . esc_html__('% refund', 'tourfic') : '';


					$inlineCSS = '';
					if (count($twobestRefundPolicy) === 1) {
						$inlineCSS = 'style="width: 100%;"';
					}

					$cancellation .= '
						<li ' . $inlineCSS . '>
							<span class="' . esc_attr($policy['cancellation_type']) . '">' . esc_html($policyType) . '</span>
						</li>';
				}
			}

		$cancellation .= '
				</ul>
			</div>
			<div class="tf-timeline-text">
				<ul>
					<li>' . esc_html__("Booking time", "tourfic") . '</li>
					<li>' . esc_html($beforeDate) . ' ' . esc_html($beforeTime) . '</li>
					<li>' . esc_html__("Trip started", "tourfic") . '</li>
				</ul>
			</div>
		</div>
		<div class="tf-cancelltion-popup-btn">
			<a href="#">' . esc_html__("See Cancellation Policy", "tourfic") . '</a>
		</div>';
	}

    // Send response
    wp_send_json_success( [
		/* translators: %s will return total price */
        'total_price' => sprintf( esc_html__( 'Total: %1$s', 'tourfic' ), wc_price( $total_prices ) ),
        'cancellation' => $cancellation,
    ] );

	wp_die();
}