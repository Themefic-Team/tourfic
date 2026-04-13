<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Pricing;
use \Tourfic\Classes\Hotel\Hotel;

//getting only selected features for rooms
$rm_features = [];
if ( ! empty( $rooms ) ) {
	foreach ( $rooms as $_room ) {
		$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
		//merge for each room's selected features
		if ( ! empty( $room['features'] ) ) {
			$rm_features = array_unique( array_merge( $rm_features, $room['features'] ) );
		}
	}
}

$tf_booking_type = '1';
$tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_ext_booking_code = '';
$tf_hide_external_price = "1";
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
    $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
    $tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
    $tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
    $tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
    $tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
    $tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
    $tf_hide_external_price = !empty( $meta["booking-by"] ) && $meta["booking-by"] == 2 ? ( !empty( $meta["hide_external_price"] ) ? $meta["hide_external_price"] : true ) : true;
    $tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
    $tf_ext_booking_code = !empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
}
if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
	$external_search_info = array(
		'{adult}'        => ! empty( $adults ) ? $adults : 1,
		'{child}'        => ! empty( $children ) ? $children : 0,
		'{infant}'       => ! empty( $infant ) ? $infant : 0,
		'{booking_date}' => ! empty( $tour_date ) ? $tour_date : '',
	);

	if ( ! empty( $tf_booking_attribute ) ) {
		$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
		if ( ! empty( $tf_booking_query_url ) ) {
			$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
		}
	}
}
$feature_filter = ! empty( Helper::tfopt( 'feature-filter' ) ) ? Helper::tfopt( 'feature-filter' ) : false;
$price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';
?>
<div id="room-availability" class="tf-pt-16">
	<span id="availability" class="tf-modify-search-btn">
		<?php esc_html_e( "Modify search", "tourfic" ); ?>
	</span>
	<!--Booking form start -->
	<?php if( ($tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 || $tf_booking_type == 3) : ?>
		<div class="tf-booking-form-wrapper">
			<?php Hotel::tf_hotel_sidebar_booking_form(); ?>
		</div>
	<?php endif; ?>
	<?php if( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && !empty($tf_ext_booking_code )): ?>
		<div id="tf-external-booking-embaded-form" class="tf-booking-form-wrapper">
			<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ); ?>
		</div>
	<?php endif; ?>
	<!-- Booking form end -->

</div>

<?php
\Tourfic\App\Templates\Components\Global\Single\Rooms::render([
	'room_style' => 'style2'
]);
?>