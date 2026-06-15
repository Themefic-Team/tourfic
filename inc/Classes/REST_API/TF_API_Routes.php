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
			'permission_callback' => array( $api, 'tf_order_permission_callback' ),
			'args'                => array(
				'post_type'    => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => function( $value ) {
						return in_array( $value, array( 'hotel', 'tf_hotel', 'tour', 'tf_tours', 'apartment', 'tf_apartment', 'car', 'tf_carrental' ), true );
					},
				),
				'post_id'      => array(
					'sanitize_callback' => 'absint',
					'validate_callback' => function( $value ) {
						return '' === $value || null === $value || ( is_numeric( $value ) && absint( $value ) > 0 );
					},
				),
				'checkinout'   => array(
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => function( $value ) {
						return '' === $value || null === $value || in_array( $value, array( 'in', 'out', 'not' ), true );
					},
				),
				'order_status' => array(
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => function( $value ) {
						return '' === $value || null === $value || in_array( $value, array( 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed', 'trash' ), true );
					},
				),
			),
		) );

		register_rest_route( 'tf/v1', '/order/(?P<id>\\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_order_details' ),
			'permission_callback' => array( $api, 'tf_order_permission_callback' ),
		) );
	}

	private function register_enquiry_routes() {
		$api = $this->api_classes['enquiry'];

		register_rest_route( 'tf/v1', '/enquiries', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_enquiries' ),
			'permission_callback' => array( $api, 'tf_enquiry_permission_callback' ),
			'args'                => array(
				'post_type' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => function( $value ) {
						return in_array( $value, array( 'tf_hotel', 'tf_tours', 'tf_apartment' ), true );
					},
				),
				'post_id'   => array(
					'sanitize_callback' => 'absint',
					'validate_callback' => function( $value ) {
						return '' === $value || null === $value || ( is_numeric( $value ) && absint( $value ) > 0 );
					},
				),
				'filters'   => array(
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => function( $value ) {
						return '' === $value || null === $value || in_array( $value, array( 'read', 'unread', 'replied', 'responded', 'not-replied', 'not-responded' ), true );
					},
				),
			),
		) );

		register_rest_route( 'tf/v1', '/enquiries/(?P<id>\\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $api, 'tf_get_enquiry_details' ),
			'permission_callback' => array( $api, 'tf_enquiry_permission_callback' ),
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
			'permission_callback' => array( $api, 'tf_user_permission_callback' ),
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
}
