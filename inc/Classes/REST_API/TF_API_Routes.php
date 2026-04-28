<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * GET-only REST API routes for Tourfic free plugin.
 *
 * This keeps read endpoints available when Tourfic Pro is not active.
 */
class TF_API_Routes {

	/**
	 * Singleton instance.
	 *
	 * @var TF_API_Routes|null
	 */
	private static $instance = null;

	/**
	 * Instances of callback API classes.
	 *
	 * @var array<string, object>
	 */
	private $api_classes = array();

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->api_classes = array(
			'rest'          => TF_Rest_API::get_instance(),
			'tour'          => TF_Tour_Rest_API::get_instance(),
			'apartment'     => TF_Apartment_Rest_API::get_instance(),
			'rental'        => TF_Rental_Rest_API::get_instance(),
			'hotel'         => TF_Hotel_Rest_API::get_instance(),
			'room'          => TF_Room_Rest_API::get_instance(),
			'booking'       => TF_Booking_Rest_API::get_instance(),
			'enquiry'       => TF_Enquiry_Rest_API::get_instance(),
			'user'          => TF_User_Rest_API::get_instance(),
			'vendor'        => TF_Vendor_Rest_API::get_instance(),
			'hotel_booking' => TF_Hotel_Backend_Booking_Rest_API::get_instance(),
			'tour_booking'  => TF_Tour_Backend_Booking_Rest_API::get_instance(),
			'integration'   => TF_Integration_Rest_API::get_instance(),
		);

		add_action( 'rest_api_init', array( $this, 'register_get_routes' ) );
	}

	public function register_get_routes() {
		$this->register_base_routes();
		$this->register_tour_routes();
		$this->register_apartment_routes();
		$this->register_rental_routes();
		$this->register_hotel_routes();
		$this->register_room_routes();
		$this->register_booking_routes();
		$this->register_enquiry_routes();
		$this->register_user_routes();
		$this->register_vendor_routes();
		$this->register_hotel_booking_routes();
		$this->register_tour_booking_routes();
		$this->register_integration_routes();
	}

	private function register_base_routes() {
		register_rest_route( 'tf/v1', '/tf-settings', array(
			'methods'             => 'GET',
			'callback'            => array( $this->api_classes['rest'], 'tf_get_tf_settings' ),
			'permission_callback' => array( $this->api_classes['rest'], 'tf_permission_callback' ),
		) );
	}

	private function register_tour_routes() {
		$api = $this->api_classes['tour'];

		register_rest_route( 'tf/v1', '/tours', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_tours' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/tour-availability', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_tour_availability' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_apartment_routes() {
		$api = $this->api_classes['apartment'];

		register_rest_route( 'tf/v1', '/apartments', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_apartments' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/apartment-availability', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_apartment_availability' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_rental_routes() {
		$api = $this->api_classes['rental'];

		register_rest_route( 'tf/v1', '/rentals', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_rentals' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_hotel_routes() {
		$api = $this->api_classes['hotel'];

		register_rest_route( 'tf/v1', '/hotels', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_hotels' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/hotel-room-availability', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_hotel_room_availability' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_room_routes() {
		$api = $this->api_classes['room'];

		register_rest_route( 'tf/v1', '/hotel-rooms', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_hotel_rooms' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/rooms', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_rooms' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_booking_routes() {
		$api = $this->api_classes['booking'];

		register_rest_route( 'tf/v1', '/orders', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_orders' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/order/(?P<id>\\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_order_details' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_enquiry_routes() {
		$api = $this->api_classes['enquiry'];

		register_rest_route( 'tf/v1', '/enquiries', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_enquiries' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/enquiries/(?P<id>\\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_enquiry_details' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_user_routes() {
		$api = $this->api_classes['user'];

		register_rest_route( 'tf/v1', '/users', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_users' ),
			'permission_callback' => array( $api, 'tf_admin_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/user/(?P<id>\\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_user' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/user-bookings', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_user_bookings' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/user-wishlist', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_user_wishlist' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_vendor_routes() {
		$api = $this->api_classes['vendor'];

		register_rest_route( 'tf/v1', '/reports', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_tf_reports' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/vendor-reports', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_tf_vendor_reports' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/commissions', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_commissions' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/payouts', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_payouts' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/payout/(?P<id>\\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_payout' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_hotel_booking_routes() {
		$api = $this->api_classes['hotel_booking'];

		register_rest_route( 'tf/v1', '/hotel/available', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_available_hotel' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/hotel/available/room/service', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_available_hotel_room_and_service_type' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );

		register_rest_route( 'tf/v1', '/hotel/available/room/number', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_available_hotel_room_number' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_tour_booking_routes() {
		$api = $this->api_classes['tour_booking'];

		register_rest_route( 'tf/v1', '/tour/available/date/time', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_available_tour_date_time' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}

	private function register_integration_routes() {
		$api = $this->api_classes['integration'];

		register_rest_route( 'tf/v1', '/get-google-access-token-url', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_google_access_token_url' ),
			'permission_callback' => array( $api, 'tf_permission_callback' ),
		) );
	}
}
