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
					foreach($badges as $badge){ 
					if(!empty($badge['title'])){
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

function tf_car_availability_response($car_meta, $pickup='', $dropoff='', $tf_pickup_date='', $tf_dropoff_date='', $tf_pickup_time='', $tf_dropoff_time='') {

	$pricing_type = !empty($car_meta["pricing_type"]) ? $car_meta["pricing_type"] : 'day_hour';

	$pickup   = isset( $_POST['pickup'] ) ? sanitize_text_field( $_POST['pickup'] ) : '';
	$dropoff = isset( $_POST['dropoff'] ) ? sanitize_text_field( $_POST['dropoff'] ) : '';
	$tf_pickup_date  = isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
	$tf_dropoff_date  = isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
	$tf_pickup_time  = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
	$tf_dropoff_time  = isset( $_POST['dropoff_time'] ) ? sanitize_text_field( $_POST['dropoff_time'] ) : '';

	$date_pricing = !empty($meta["date_prices"]) ? $meta["date_prices"] : '';
	if( !empty($tf_pickup_date) && !empty($tf_dropoff_date) && 'date'==$pricing_type && !empty($date_pricing) ){
		foreach ($date_pricing as $entry) {
			$startDate = strtotime($entry['date']['from']);
			$endDate = strtotime($entry['date']['to']);
			if($startDate==strtotime($tf_pickup_date) && $endDate==strtotime($tf_dropoff_date)){
				return true;
				break;
			}else{
				return false;
			}
		}
	}else{
		return true;
	}
}