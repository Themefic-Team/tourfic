<?php
namespace Tourfic\Admin\Booking_Details\Apartment;
defined( 'ABSPATH' ) || exit;

class Apartment_Booking_Details extends \Tourfic\Admin\Booking_Details\Booking_Details
{
    use \Tourfic\Traits\Singleton;

    public function __construct()
    {

        $booking_args = array(
            'post_type' => 'tf_apartment',
            'menu_title' => 'Apartment Booking Details',
            'menu_slug' => 'tf_apartment_booking',
            'capability' => 'edit_tf_apartments',
			'booking_type' => 'apartment',
            'booking_title' => 'Apartment'
        );

        parent::__construct($booking_args);

    }

}
