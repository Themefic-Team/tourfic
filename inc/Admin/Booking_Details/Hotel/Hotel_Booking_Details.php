<?php

namespace Tourfic\Admin\Booking_Details\Hotel;
defined( 'ABSPATH' ) || exit;

class Hotel_Booking_Details extends \Tourfic\Admin\Booking_Details\Booking_Details {
	use \Tourfic\Traits\Singleton;

	public function __construct() {

		$booking_args = array(
			'post_type'     => 'tf_hotel',
			'menu_title'    => __('Hotel Booking Details', 'tourfic'),
			'menu_slug'     => 'tf_hotel_booking',
			'capability'    => 'edit_tf_hotels',
			'booking_type'  => 'hotel',
			'booking_title' => 'Hotel'
		);

		parent::__construct( $booking_args );

	}

}