<?php
namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

class Base {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Enquiry;
	use \Tourfic\Traits\Helper;

	public function __construct() {
		$this->init();
	}

	public function init() {
		 \Tourfic\Classes\Helper::instance();
		 \Tourfic\Classes\Enqueue::instance();
//		\Tourfic\Admin\Functions::instance();
		// \Tourfic\Admin\TF_Options\TF_Options::instance();

		if ( self::tfopt( 'disable-services' ) && in_array( 'hotel', self::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Hotel\Hotel_CPT::instance();
		}
		if ( self::tfopt( 'disable-services' ) && in_array( 'tour', self::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Tour\Tour_CPT::instance();
		}
		if ( self::tfopt( 'disable-services' ) && in_array( 'apartment', self::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Apartment\Apartment_CPT::instance();
		}

		// \Tourfic\Admin\Booking_Details\Hotel\Hotel_Booking_Details::instance();
//		\Tourfic\Admin\Enquiry\Hotel\Hotel_Enquiry::instance();
//		\Tourfic\Admin\Booking_Details\Tour\Tour_Booking_Details::instance();
//		\Tourfic\Admin\Booking_Details\Apartment\Apartment_Booking_Details::instance();
	}

	function init_hooks() {
//		add_action( 'admin_menu', array( $this, 'tf_add_enquiry_submenu' ) );
	}
}

