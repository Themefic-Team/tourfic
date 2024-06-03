<?php
namespace Tourfic\Admin\Booking_Details\Tour;

defined( 'ABSPATH' ) || exit;

class Tour_Booking_Details extends \Tourfic\Admin\Booking_Details\Booking_Details
{
    use \Tourfic\Traits\Singleton;

    public function __construct()
    {

        $booking_args = array(
            'post_type' => 'tf_tours',
            'menu_title' => 'Tour Booking Details',
            'menu_slug' => 'tf_tours_booking',
            'capability' => 'edit_tf_tourss',
			'booking_type' => 'tour',
            'booking_title' => 'Tour'
        );

        parent::__construct($booking_args);

    }

}