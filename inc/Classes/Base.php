<?php
namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

use Tourfic\Admin\Backend_Booking\TF_Apartment_Backend_Booking;
use Tourfic\Admin\Backend_Booking\TF_Hotel_Backend_Booking;
use Tourfic\Admin\Backend_Booking\TF_Tour_Backend_Booking;
use Tourfic\Admin\Booking_Details\Tour\Tour_Booking_Details;
use Tourfic\Admin\Booking_Details\Hotel\Hotel_Booking_Details;
use Tourfic\Admin\Booking_Details\Apartment\Apartment_Booking_Details;
use Tourfic\App\Widgets\TF_Widget_Base;
use Tourfic\Admin\TF_Promo_Notice;
use Tourfic\Admin\Emails\TF_Handle_Emails;
use Tourfic\Classes\Helper;

class Base {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Database;

	public function __construct() {
		$this->init();
		$this->load_shortcodes();
	}

	public function init() {
		add_action( 'admin_init', array($this, 'create_enquiry_database_table') );

		if ( file_exists( TF_INC_PATH . 'functions.php' ) ) {
			require_once TF_INC_PATH . 'functions.php';
		} else {
			tf_file_missing( TF_INC_PATH . 'functions.php' );
		}

		\Tourfic\Classes\Migrator::instance();
		\Tourfic\Classes\Helper::instance();
		\Tourfic\Classes\Enqueue::instance();
		\Tourfic\Classes\TF_Activator::instance();
		\Tourfic\Classes\TF_Deactivator::instance();

		if(is_admin()) {
			\Tourfic\Admin\TF_Setup_Wizard::instance();
			\Tourfic\Admin\TF_Options\TF_Options::instance();

			// Backend Bookings
			TF_Apartment_Backend_Booking::instance();
			TF_Hotel_Backend_Booking::instance();
			TF_Tour_Backend_Booking::instance();

			// Booking Details
			Tour_Booking_Details::instance();
			Hotel_Booking_Details::instance();
			Apartment_Booking_Details::instance();

			//Enquiry
			\Tourfic\Admin\Enquiry\Hotel_Enquiry::instance();
			\Tourfic\Admin\Enquiry\Tour_Enquiry::instance();
			\Tourfic\Admin\Enquiry\Apartment_Enquiry::instance();

			// Promo Notice
			TF_Promo_Notice::instance();
		}

		if ( Helper::tf_is_woo_active() ) {
			
			// Tourfic Widgets
			TF_Widget_Base::instance();
		}

		if ( Helper::tfopt( 'disable-services' ) && in_array( 'hotel', Helper::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Hotel\Hotel_CPT::instance();
		}
		if ( Helper::tfopt( 'disable-services' ) && in_array( 'tour', Helper::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Tour\Tour_CPT::instance();
		}
		if ( Helper::tfopt( 'disable-services' ) && in_array( 'apartment', Helper::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Apartment\Apartment_CPT::instance();
		}

		\Tourfic\Admin\Emails\TF_Handle_Emails::instance();
		\Tourfic\App\Wishlist::instance();
		\Tourfic\Classes\TF_Review::instance();

	}

	function load_shortcodes() {
		\Tourfic\App\Shortcodes\Hotels::instance();
		\Tourfic\App\Shortcodes\Hotel_Locations::instance();
		\Tourfic\App\Shortcodes\Recent_Hotel::instance();
		\Tourfic\App\Shortcodes\Hotel_External_Listings::instance();

		\Tourfic\App\Shortcodes\Tours::instance();
		\Tourfic\App\Shortcodes\Tour_Destinations::instance();
		\Tourfic\App\Shortcodes\Recent_Tour::instance();
		\Tourfic\App\Shortcodes\Tour_External_Listings::instance();

		\Tourfic\App\Shortcodes\Apartments::instance();
		\Tourfic\App\Shortcodes\Apartment_Locations::instance();
		\Tourfic\App\Shortcodes\Recent_Apartment::instance();
		\Tourfic\App\Shortcodes\Apartment_External_Listings::instance();

		\Tourfic\App\Shortcodes\Recent_Blog::instance();
		\Tourfic\App\Shortcodes\Reviews::instance();
		\Tourfic\App\Shortcodes\Wishlist::instance();
		\Tourfic\App\Shortcodes\Search_Form::instance();
		\Tourfic\App\Shortcodes\Search_Result::instance();
		\Tourfic\App\Shortcodes\Vendor_Post::instance();
	}
}

