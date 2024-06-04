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
            'menu_title' => esc_html__('Apartment Booking Details', 'tourfic'),
            'menu_slug' => 'tf_apartment_booking',
            'capability' => 'edit_tf_apartments',
			'booking_type' => 'apartment',
            'booking_title' => esc_html__('Apartment', 'tourfic'),
        );

        parent::__construct($booking_args);

    }

    function voucher_details( $tf_tour_details, $tf_order_details, $tf_billing_details ) {}
    function voucher_quick_view( $tour_ides, $tf_order_details, $tf_billing_details ) {}

}
 