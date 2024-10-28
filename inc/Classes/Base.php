<?php
namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

use Tourfic\Admin\Backend_Booking\TF_Apartment_Backend_Booking;
use Tourfic\Admin\Backend_Booking\TF_Hotel_Backend_Booking;
use Tourfic\Admin\Backend_Booking\TF_Tour_Backend_Booking;
use Tourfic\Admin\Booking_Details\Apartment_Booking_Details;
use Tourfic\Admin\Booking_Details\Hotel_Booking_Details;
use Tourfic\Admin\Booking_Details\Tour_Booking_Details;
use Tourfic\Admin\TF_Promo_Notice;
use Tourfic\App\Widgets\TF_Widget_Base;

class Base {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Database;

	public function __construct() {
		$this->init();
		$this->load_shortcodes();
	}

	public function init() {
		add_action( 'admin_init', array($this, 'create_enquiry_database_table') );
		add_action('admin_init', array($this, 'tf_order_table_create'));
		add_action( 'admin_init', array($this, 'tf_admin_table_alter_order_data') );

		if ( Helper::tf_is_woo_active() ) {
			\Tourfic\Classes\Woocommerce\Woocommerce::instance();
		}

		if ( file_exists( TF_INC_PATH . 'functions.php' ) ) {
			require_once TF_INC_PATH . 'functions.php';
		} else {
			tf_file_missing( TF_INC_PATH . 'functions.php' );
		}

		\Tourfic\Classes\Migrator::instance();
		\Tourfic\Classes\Helper::instance();
		\Tourfic\Classes\Enqueue::instance();
		\Tourfic\Classes\Activator::instance();
		\Tourfic\Classes\Deactivator::instance();

		//Enquiry
		\Tourfic\Admin\Enquiry\Hotel_Enquiry::instance();
		\Tourfic\Admin\Enquiry\Tour_Enquiry::instance();
		\Tourfic\Admin\Enquiry\Apartment_Enquiry::instance();

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

			
			\Tourfic\Admin\TF_Duplicator::instance();

			// Admin Notices
			\Tourfic\Admin\Notice_Update::instance();
		}
		// Promo Notice
		TF_Promo_Notice::instance();
		if ( Helper::tf_is_woo_active() ) {
			TF_Widget_Base::instance();
		}

		if ( Helper::tfopt( 'disable-services' ) && in_array( 'hotel', Helper::tfopt( 'disable-services' ) ) ) {
		} else {
			\Tourfic\Classes\Hotel\Hotel::instance();
			\Tourfic\Classes\Room\Room::instance();
		}
		if ( Helper::tfopt( 'disable-services' ) && in_array( 'tour', Helper::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Tour\Tour::instance();
		}
		if ( Helper::tfopt( 'disable-services' ) && in_array( 'apartment', Helper::tfopt( 'disable-services' ) ) ) {
		} else {
			 \Tourfic\Classes\Apartment\Apartment::instance();
		}

		\Tourfic\Admin\Emails\TF_Handle_Emails::instance();
		\Tourfic\App\Wishlist::instance();
		\Tourfic\App\TF_Review::instance();

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

