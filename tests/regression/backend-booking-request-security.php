<?php
/**
 * Isolated behavioral checks for backend booking request boundaries.
 *
 * Run: php tests/regression/backend-booking-request-security.php
 * No WordPress bootstrap, database, network, or real booking writes are used.
 * Pricing is mocked; valid callbacks are intercepted at the storage boundary.
 */

namespace {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
	$GLOBALS['tourfic_test_assertions'] = 0;
	$GLOBALS['tourfic_test_caps']       = array();
	$GLOBALS['tourfic_test_posts']      = array();
	$GLOBALS['tourfic_test_meta']       = array();
	$GLOBALS['tourfic_test_user']       = (object) array(
		'ID' => 17, 'display_name' => 'Actual Agent', 'user_login' => 'actual-agent',
	);

	class Tourfic_Test_Request_Stop extends \RuntimeException {}

	set_error_handler( function ( $severity, $message, $file, $line ) {
		throw new \ErrorException( $message, 0, $severity, $file, $line );
	} );

	function add_action() {}
	function current_user_can( $capability ) {
		return ! empty( $GLOBALS['tourfic_test_caps'][ $capability ] );
	}
	function wp_get_current_user() {
		return $GLOBALS['tourfic_test_user'];
	}
	function get_current_user_id() {
		return (int) wp_get_current_user()->ID;
	}
	function get_post( $post_id ) {
		return $GLOBALS['tourfic_test_posts'][ $post_id ] ?? null;
	}
	function get_post_type_object( $post_type ) {
		++$GLOBALS['tourfic_test_cpt_reads'];
		if ( ! empty( $GLOBALS['tourfic_test_cpt_unregistered'] ) ) {
			return null;
		}
		return (object) array( 'cap' => (object) array( 'edit_others_posts' => 'edit_others_' . $post_type . 's' ) );
	}
	class WP_Query {
		public function __construct( $args ) {
			$GLOBALS['tourfic_test_query_args'] = $args;
		}
		public function have_posts() { return false; }
	}
	function wp_reset_postdata() {}
	function get_the_title( $post_id ) { return 'Listing ' . $post_id; }
	function wc_price( $price ) { return '$' . $price; }
	function get_post_meta( $post_id, $key, $single = false ) {
		++$GLOBALS['tourfic_test_meta_reads'];
		return $GLOBALS['tourfic_test_meta'][ $post_id ][ $key ] ?? array();
	}
	function wp_unslash( $value ) {
		return is_array( $value ) ? array_map( 'wp_unslash', $value ) : ( is_string( $value ) ? stripslashes( $value ) : $value );
	}
	function sanitize_text_field( $value ) {
		return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $value ) ) );
	}
	function sanitize_email( $value ) {
		return filter_var( $value, FILTER_SANITIZE_EMAIL );
	}
	function is_email( $value ) {
		return filter_var( $value, FILTER_VALIDATE_EMAIL );
	}
	function absint( $value ) {
		return abs( (int) $value );
	}
	function esc_html__( $text, $domain = 'tourfic' ) {
		return $text;
	}
	function wp_rand() {
		return 123456;
	}
	function wp_json_encode( $value ) {
		return json_encode( $value );
	}
	function wp_die() {
		throw new Tourfic_Test_Request_Stop( 'response' );
	}
	function wp_send_json_error( $data ) {
		echo wp_json_encode( array( 'success' => false, 'data' => $data ) );
		wp_die();
	}
	function wp_send_json_success( $data ) {
		wp_send_json( array( 'success' => true, 'data' => $data ) );
	}
	function wp_send_json( $data ) {
		echo wp_json_encode( $data );
		wp_die();
	}
	function check_ajax_referer( $action, $key ) {
		$GLOBALS['tourfic_test_nonce_calls'][] = array( $action, $key );
		if ( ! isset( $_POST[ $key ] ) || 'valid:' . $action !== $_POST[ $key ] ) {
			throw new Tourfic_Test_Request_Stop( 'nonce' );
		}
		return 1;
	}
	function apply_filters( $hook, $value, ...$args ) {
		if ( 'tourfic_tour_extra_meta' === $hook ) {
			return $GLOBALS['tourfic_test_extras'];
		}
		return $value;
	}
}

namespace Tourfic\Classes {
	class Helper {
		public static function tfopt( $key ) { return array(); }
		public static function tf_data_types( $value ) { return $value; }
		public static function tf_set_order( $order ) {
			$GLOBALS['tourfic_test_orders'][] = $order;
			throw new \Tourfic_Test_Request_Stop( 'storage' );
		}
	}
}

namespace Tourfic\Classes\Room {
	class Room {
		public static function get_hotel_rooms( $hotel_id ) {
			return $GLOBALS['tourfic_test_rooms'][ $hotel_id ] ?? array();
		}
	}
}

namespace Tourfic\Classes\Apartment {
	class Pricing {
		public static function instance( $post_id ) {
			return new self();
		}
		public function set_dates( $from, $to ) {
			return $this;
		}
		public function set_persons( $adults, $children, $infants ) {
			return $this;
		}
		public function set_total_price() {
			return $this;
		}
		public function get_total_price() {
			return 120;
		}
		public function get_availability() {
			return 120;
		}
	}
}

namespace {
	require_once ABSPATH . 'inc/Traits/Singleton.php';
	require_once ABSPATH . 'inc/Core/TF_Backend_Booking.php';
	require_once ABSPATH . 'inc/Admin/Backend_Booking/TF_Tour_Backend_Booking.php';
	require_once ABSPATH . 'inc/Admin/Backend_Booking/TF_Hotel_Backend_Booking.php';
	require_once ABSPATH . 'inc/Admin/Backend_Booking/TF_Apartment_Backend_Booking.php';

	class Tourfic_Test_Backend_Request extends \Tourfic\Core\TF_Backend_Booking {
		public function __construct( $post_type = 'tf_hotel' ) {
			$this->args = array( 'post_type' => $post_type, 'caps' => 'edit_' . $post_type . 's' );
		}
		public function read( $schema, $booking = false ) {
			return $this->read_request( $schema, $booking );
		}
		public function sanitize( $value, $type ) {
			return $this->sanitize_request_value( $value, $type );
		}
		public function can_book( $post_id ) {
			return $this->can_book_listing( $post_id );
		}
		public function query_args() {
			return $this->listing_query_args();
		}
		public function scope_field( $field ) {
			return $this->scope_listing_field( $field );
		}
		protected function check_avaibility_callback() {}
		protected function check_price_callback() {}
		protected function backend_booking_callback() {}
	}

	class Tourfic_Test_Backend_Tour extends \Tourfic\Admin\Backend_Booking\TF_Tour_Backend_Booking {
		public function __construct() {}
		public function tf_get_tour_total_price( $post_id, $date, $time, $extras, $adult, $child, $infant, $package = '' ) {
			$GLOBALS['tourfic_test_price_args'] = func_get_args();
			return array(
				'start_date' => $date, 'end_date' => $date, 'tour_date' => $date,
				'tf_tour_time_title' => $time, 'tf_tour_extra_title' => implode( ',', $extras ),
				'tf_tour_price' => 120, 'response' => array(),
			);
		}
	}

	class Tourfic_Test_Backend_Hotel extends \Tourfic\Admin\Backend_Booking\TF_Hotel_Backend_Booking {
		public function __construct() {}
		public function tf_get_room_total_price( $hotel_id, $room, $from, $to, $count, $adult, $child, $service ) {
			return array( 'price_total' => 120, 'air_service_info' => '' );
		}
	}

	class Tourfic_Test_Backend_Apartment extends \Tourfic\Admin\Backend_Booking\TF_Apartment_Backend_Booking {
		public function __construct() {}
	}

	function tourfic_backend_assert( $condition, $message ) {
		++$GLOBALS['tourfic_test_assertions'];
		if ( ! $condition ) {
			throw new \RuntimeException( 'FAIL: ' . $message );
		}
	}

	function tourfic_backend_call( callable $callback, array $post ) {
		$_POST = $post;
		$GLOBALS['tourfic_test_orders']      = array();
		$GLOBALS['tourfic_test_meta_reads']  = 0;
		$GLOBALS['tourfic_test_nonce_calls'] = array();
		ob_start();
		try {
			$value = $callback();
			$stop  = 'returned';
		} catch ( Tourfic_Test_Request_Stop $exception ) {
			$stop  = $exception->getMessage();
			$value = null;
		} finally {
			$output = ob_get_clean();
		}
		return array( 'stop' => $stop, 'value' => $value, 'output' => $output, 'json' => json_decode( $output, true ) );
	}

	function tourfic_backend_payload( $type, $post_id = 101 ) {
		$payload = array(
			'tf_backend_booking_nonce' => 'valid:tf_backend_booking_nonce_action',
			'tf_customer_first_name' => addslashes( "<b>O'Neil</b>" ),
			'tf_customer_last_name' => 'Guest', 'tf_customer_email' => 'guest@example.com',
			'tf_customer_phone' => '+880123456789', 'tf_customer_country' => 'BD',
			'tf_customer_address' => addslashes( 'C:\\Guest' ), 'tf_customer_address_2' => '',
			'tf_customer_city' => 'Dhaka', 'tf_customer_state' => 'Dhaka', 'tf_customer_zip' => '1200',
			'undeclared_payload' => array( 'unsafe' => '<script>alert(1)</script>' ),
		);
		if ( 'tour' === $type ) {
			return array_merge( $payload, array(
				'tf_tours_booked_by' => 'Spoofed Administrator', 'tf_available_tours' => (string) $post_id,
				'tf_tour_date' => '2028/02/29', 'tf_tour_time' => '09:30 AM',
				'tf_tour_adults_number' => '1', 'tf_tour_children_number' => '', 'tf_tour_infants_number' => '',
				'tf_tour_packages' => '0', 'tf_tour_extras' => array( '0', 'legacy-extra', 'legacy-extra' ),
			) );
		}
		$payload[ 'tf_' . $type . '_booked_by' ]       = 'Spoofed Administrator';
		$payload[ 'tf_available_' . $type . 's' ]       = (string) $post_id;
		$payload[ 'tf_' . $type . '_date' ]            = array( 'from' => '2028/02/29', 'to' => '2028/03/02' );
		$payload[ 'tf_' . $type . '_adults_number' ]   = '1';
		$payload[ 'tf_' . $type . '_children_number' ] = '';
		if ( 'hotel' === $type ) {
			$payload['tf_available_rooms']     = 'room_A-Z_9';
			$payload['tf_hotel_rooms_number']  = '1';
			$payload['tf_hotel_service_type']  = '';
		} else {
			$payload['tf_apartment_infant_number']   = '';
			$payload['tf_apartment_additional_fees'] = array( 'invented_fee' => '-9999' );
		}
		return $payload;
	}

	$reader = new Tourfic_Test_Backend_Request();
	$GLOBALS['tourfic_test_cpt_reads'] = 0;
	$GLOBALS['tourfic_test_cpt_unregistered'] = true;
	new \Tourfic\Admin\Backend_Booking\TF_Tour_Backend_Booking();
	tourfic_backend_assert( 0 === $GLOBALS['tourfic_test_cpt_reads'], 'Tour form construction must not freeze permissions before CPT registration.' );
	$GLOBALS['tourfic_test_cpt_unregistered'] = false;
	$GLOBALS['tourfic_test_caps'] = array( 'edit_tf_hotels' => true );
	foreach ( array( false, true ) as $booking ) {
		$nonce_key = $booking ? 'tf_backend_booking_nonce' : '_nonce';
		$action    = $booking ? 'tf_backend_booking_nonce_action' : 'updates';
		$post      = array( $nonce_key => 'valid:' . $action, 'name' => addslashes( "<b>O'Neil</b>" ), 'unlisted' => 'drop' );
		$call      = function () use ( $reader, $booking ) { return $reader->read( array( 'name' => 'text' ), $booking ); };
		$result    = tourfic_backend_call( $call, $post );
		tourfic_backend_assert( 'returned' === $result['stop'], 'Valid nonce and service capability must pass.' );
		tourfic_backend_assert( "O'Neil" === $result['value']['name'], 'Text must be unslashed once and sanitized.' );
		tourfic_backend_assert( ! isset( $result['value']['unlisted'] ), 'Unknown request fields must be discarded.' );
		tourfic_backend_assert( array( array( $action, $nonce_key ) ) === $GLOBALS['tourfic_test_nonce_calls'], 'Each flow must verify its actual form nonce.' );
		$post[ $nonce_key ] = 'invalid';
		$result = tourfic_backend_call( $call, $post );
		tourfic_backend_assert( 'nonce' === $result['stop'], 'Invalid nonce must stop before validation.' );
		unset( $post[ $nonce_key ] );
		tourfic_backend_assert( 'nonce' === tourfic_backend_call( $call, $post )['stop'], 'Missing nonce must fail.' );
		$post[ $nonce_key ] = 'valid:' . $action;
		$GLOBALS['tourfic_test_caps'] = array();
		$result = tourfic_backend_call( $call, $post );
		tourfic_backend_assert( 'response' === $result['stop'] && false === $result['json']['success'], 'Valid nonce must not replace operation capability.' );
		tourfic_backend_assert( isset( $result['json'][ $booking ? 'message' : 'data' ] ), 'Booking and read error envelopes must remain compatible.' );
		$GLOBALS['tourfic_test_caps'] = array( 'edit_tf_hotels' => true );
	}

	$valid_values = array(
		array( '0', 'number', 0 ), array( '', 'number', 0 ), array( '1', 'positive', 1 ),
		array( '0', 'selection', 0 ), array( '', 'selection', '' ),
		array( '2028/02/29', 'date', '2028/02/29' ), array( '2028-02-29', 'date', '2028-02-29' ),
		array( 'room_A-Z_9', 'text', 'room_A-Z_9' ), array( '09:30 AM', 'text', '09:30 AM' ),
		array( array( '0', 'legacy-extra', '0' ), 'extra_ids', array( '0', 'legacy-extra' ) ),
		array( array( 'from' => '2028/02/29', 'to' => '2028/03/01', 'unlisted' => 'drop' ), 'date_range', array( 'from' => '2028/02/29', 'to' => '2028/03/01' ) ),
	);
	foreach ( $valid_values as list( $value, $type, $expected ) ) {
		tourfic_backend_assert( $expected === $reader->sanitize( $value, $type ), 'Valid ' . $type . ' value must preserve its contract.' );
	}
	$invalid_values = array(
		array( '-1', 'number' ), array( '1.5', 'number' ), array( '1e3', 'number' ), array( 'NaN', 'number' ),
		array( '999999999999999999999999', 'number' ), array( true, 'number' ), array( '0', 'positive' ),
		array( '', 'positive' ), array( 'not-an-index', 'selection' ), array( 'guest@@example.com', 'email' ),
		array( '2027/02/29', 'date' ), array( '2028/02/30', 'date' ), array( 'tomorrow', 'date' ),
		array( '2028/02-29', 'date' ), array( '2028/02/29 12:00', 'date' ),
		array( '2028/02/29', 'date_range' ), array( array( 'from' => '2028/02/29' ), 'date_range' ),
		array( array( 'from' => '2028/03/01', 'to' => '2028/02/29' ), 'date_range' ),
		array( array( 'from' => '2028/02/29', 'to' => '2028/02/29' ), 'date_range' ),
		array( array( 'from' => array( '2028/02/29' ), 'to' => '2028/03/01' ), 'date_range' ),
		array( '0,1', 'extra_ids' ), array( array( array( '0' ) ), 'extra_ids' ), array( array( '' ), 'extra_ids' ),
	);
	foreach ( array( 'text', 'email', 'date', 'positive', 'number', 'selection' ) as $type ) {
		$invalid_values[] = array( array( 'injected' ), $type );
		$invalid_values[] = array( new \stdClass(), $type );
	}
	foreach ( $invalid_values as list( $value, $type ) ) {
		tourfic_backend_assert( null === $reader->sanitize( $value, $type ), 'Malformed ' . $type . ' values must fail without coercion.' );
	}
	$result = tourfic_backend_call( function () use ( $reader ) {
		return $reader->read( array( 'count' => 'number' ), true );
	}, array( 'tf_backend_booking_nonce' => 'valid:tf_backend_booking_nonce_action', 'count' => '-2' ) );
	tourfic_backend_assert( isset( $result['json']['fieldErrors']['count_error'] ), 'Invalid booking fields must retain fieldErrors naming.' );

	$handlers = array( 'hotel' => new Tourfic_Test_Backend_Hotel(), 'tour' => new Tourfic_Test_Backend_Tour(), 'apartment' => new Tourfic_Test_Backend_Apartment() );
	foreach ( $handlers as $type => $handler ) {
		$post_type = 'tour' === $type ? 'tf_tours' : 'tf_' . $type;
		$cap       = 'edit_' . $post_type . 's';
		$reader    = new Tourfic_Test_Backend_Request( $post_type );
		$payload   = tourfic_backend_payload( $type );
		$GLOBALS['tourfic_test_posts'] = array(
			101 => (object) array( 'ID' => 101, 'post_type' => $post_type, 'post_status' => 'publish', 'post_author' => 17 ),
			102 => (object) array( 'ID' => 102, 'post_type' => $post_type, 'post_status' => 'publish', 'post_author' => 29 ),
			103 => (object) array( 'ID' => 103, 'post_type' => 'post', 'post_status' => 'publish', 'post_author' => 17 ),
			104 => (object) array( 'ID' => 104, 'post_type' => $post_type, 'post_status' => 'draft', 'post_author' => 17 ),
		);
		$GLOBALS['tourfic_test_meta'] = array( 101 => array(
			'tf_tours_opt' => array( 'pricing' => 'package', 'package_pricing' => array( array( 'pack_status' => '1', 'pack_title' => 'Starter' ), array( 'pack_status' => '' ) ) ),
			'tf_hotels_opt' => array(), 'tf_apartment_opt' => array(),
		), 201 => array( 'tf_room_opt' => array( 'unique_id' => 'room_A-Z_9', 'enable' => '1', 'adult' => 2, 'child' => 2, 'title' => 'Room' ) ) );
		$GLOBALS['tourfic_test_rooms']  = array( 101 => array( (object) array( 'ID' => 201 ) ) );
		$GLOBALS['tourfic_test_extras'] = array( 0 => array( 'title' => 'Zero-index extra' ), 'legacy-extra' => array( 'title' => 'Legacy extra' ) );
		$GLOBALS['tourfic_test_caps']   = array( $cap => true );
		$field_definition = array( 'query_args' => array( 'post_type' => $post_type, 'posts_per_page' => 10 ) );
		tourfic_backend_assert( 17 === $reader->scope_field( $field_definition )['query_args']['author'], $type . ': rendered listing field must be owner-scoped.' );
		$GLOBALS['tourfic_test_caps']['edit_others_' . $post_type . 's'] = true;
		tourfic_backend_assert( $field_definition === $reader->scope_field( $field_definition ), $type . ': render-time CPT registration must preserve cross-author field queries without changing their other constraints.' );
		unset( $GLOBALS['tourfic_test_caps']['edit_others_' . $post_type . 's'] );
		tourfic_backend_assert( $reader->can_book( 101 ) && ! $reader->can_book( 102 ), $type . ': vendor must be owner-scoped.' );
		tourfic_backend_assert( ! $reader->can_book( 103 ) && ! $reader->can_book( 104 ) && ! $reader->can_book( 999 ), $type . ': wrong type, draft, and missing posts must fail.' );
		tourfic_backend_assert( 17 === $reader->query_args()['author'], $type . ': vendor collection must remain author-scoped.' );
		foreach ( array( 'tf_manager_options', 'edit_others_' . $post_type . 's' ) as $others_cap ) {
			$GLOBALS['tourfic_test_caps'] = array( $cap => true, $others_cap => true );
			tourfic_backend_assert( $reader->can_book( 102 ) && ! isset( $reader->query_args()['author'] ), $type . ': manager/editor can access other authors.' );
			$GLOBALS['tourfic_test_caps'][ $cap ] = false;
			tourfic_backend_assert( ! $reader->can_book( 101 ) && ! $reader->can_book( 102 ), $type . ': removed service capability must override manager/editor access.' );
		}
		foreach ( array( array(), array( 'tf_manager_options' => true ) ) as $caps ) {
			$GLOBALS['tourfic_test_caps'] = $caps;
			$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $payload );
			tourfic_backend_assert( 'response' === $result['stop'] && false === $result['json']['success'], $type . ': actual callback must reject insufficient capabilities.' );
			tourfic_backend_assert( ! $GLOBALS['tourfic_test_orders'] && 0 === $GLOBALS['tourfic_test_meta_reads'], $type . ': authorization must precede pricing/meta access and storage.' );
		}
		$GLOBALS['tourfic_test_caps'] = array( $cap => true );
		foreach ( array( 102, 103, 104, 999 ) as $invalid_id ) {
			$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), tourfic_backend_payload( $type, $invalid_id ) );
			tourfic_backend_assert( 'response' === $result['stop'] && ! $GLOBALS['tourfic_test_orders'] && 0 === $GLOBALS['tourfic_test_meta_reads'], $type . ': actual callback must reject inaccessible listing before metadata/storage.' );
		}
		$bad_nonce = $payload;
		$bad_nonce['tf_backend_booking_nonce'] = 'invalid';
		$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $bad_nonce );
		tourfic_backend_assert( 'nonce' === $result['stop'] && ! $GLOBALS['tourfic_test_orders'], $type . ': actual callback must verify nonce.' );
		$bad_count = $payload;
		$bad_count[ 'tf_' . $type . '_adults_number' ] = '-1';
		$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $bad_count );
		tourfic_backend_assert( 'response' === $result['stop'] && isset( $result['json']['fieldErrors'][ 'tf_' . $type . '_adults_number_error' ] ) && ! $GLOBALS['tourfic_test_orders'], $type . ': negative adults must never reach storage.' );
		$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $payload );
		tourfic_backend_assert( 'storage' === $result['stop'] && 1 === count( $GLOBALS['tourfic_test_orders'] ), $type . ': valid callback must reach mocked storage exactly once.' );
		$order = $GLOBALS['tourfic_test_orders'][0];
		tourfic_backend_assert( 'Actual Agent' === $order['order_details']['order_by'], $type . ': request actor spoof must be ignored.' );
		tourfic_backend_assert( "O'Neil" === $order['billing_details']['billing_first_name'] && 'C:\\Guest' === $order['billing_details']['billing_address_1'], $type . ': stored customer data must be sanitized and unslashed exactly once.' );
		tourfic_backend_assert( false === strpos( json_encode( $order ), 'undeclared_payload' ) && false === strpos( json_encode( $order ), 'invented_fee' ), $type . ': undeclared fields must not reach storage.' );
		tourfic_backend_assert( 1 === $order['order_details']['adult'] && 0 === $order['order_details']['child'], $type . ': normalized counts must reach storage.' );
		if ( 'tour' === $type ) {
			tourfic_backend_assert( 0 === $GLOBALS['tourfic_test_price_args'][7] && array( '0', 'legacy-extra' ) === $GLOBALS['tourfic_test_price_args'][3], 'Tour package zero and authoritative nonnumeric extra IDs must survive.' );
			tourfic_backend_assert( '09:30 AM' === $order['order_details']['tour_time'], 'Stored tour time tokens must remain intact.' );
			foreach ( array( 'tf_tour_packages' => '1', 'tf_tour_extras' => array( 'foreign-extra' ) ) as $key => $value ) {
				$invalid = $payload;
				$invalid[ $key ] = $value;
				$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $invalid );
				tourfic_backend_assert( 'response' === $result['stop'] && isset( $result['json']['fieldErrors'][ $key . '_error' ] ) && ! $GLOBALS['tourfic_test_orders'], 'Unavailable packages and foreign extras must fail before storage.' );
			}
			$default_package = $payload;
			unset( $default_package['tf_tour_packages'] );
			$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $default_package );
			tourfic_backend_assert( 'storage' === $result['stop'] && 0 === $GLOBALS['tourfic_test_price_args'][7], 'Omitted package selection must resolve the first active package, including index zero.' );
			$GLOBALS['tourfic_test_meta'][101]['tf_tours_opt']['package_pricing'][0]['pack_status'] = '';
			$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $default_package );
			tourfic_backend_assert( 'response' === $result['stop'] && ! $GLOBALS['tourfic_test_orders'], 'A package-priced tour with no active package cannot silently create a default-price order.' );
			$GLOBALS['tourfic_test_meta'][101]['tf_tours_opt']['package_pricing'][0]['pack_status'] = '1';
		}
		if ( 'hotel' === $type ) {
			tourfic_backend_assert( 'room_A-Z_9' === $order['order_details']['room_unique_id'], 'Nonnumeric room IDs must survive the actual callback.' );
			foreach ( array( 'tf_available_rooms' => 'foreign-room', 'tf_hotel_service_type' => 'pickup' ) as $key => $value ) {
				$invalid = $payload;
				$invalid[ $key ] = $value;
				$result = tourfic_backend_call( array( $handler, 'backend_booking_callback' ), $invalid );
				tourfic_backend_assert( 'response' === $result['stop'] && isset( $result['json']['fieldErrors'][ $key . '_error' ] ) && ! $GLOBALS['tourfic_test_orders'], 'Foreign rooms and unavailable hotel services must fail before storage.' );
			}
		}

		$read_payload = array( '_nonce' => 'valid:updates', 'from' => '2028/02/29', 'to' => '2028/03/02' );
		$read_actions = 'hotel' === $type ? array(
			'tf_check_available_hotel' => '',
			'tf_check_available_room' => 'hotel_id',
			'tf_update_room_fields' => 'hotel_id',
		) : ( 'tour' === $type ? array( 'tf_tour_date_time_update' => 'tour_id' ) : array(
			'check_avaibility_callback' => '',
			'tf_check_apartment_aditional_fees_callback' => 'apartment_id',
		) );
		foreach ( $read_actions as $method => $id_key ) {
			$post = $read_payload;
			$post['room_id'] = 'room_A-Z_9';
			if ( $id_key ) { $post[ $id_key ] = '101'; }
			$GLOBALS['tourfic_test_caps'] = array();
			$result = tourfic_backend_call( array( $handler, $method ), $post );
			tourfic_backend_assert( false === $result['json']['success'] && 0 === $GLOBALS['tourfic_test_meta_reads'], $method . ': valid nonce cannot bypass read capability checks.' );
			$GLOBALS['tourfic_test_caps'] = array( $cap => true );
			$invalid = $post;
			$invalid['_nonce'] = 'invalid';
			tourfic_backend_assert( 'nonce' === tourfic_backend_call( array( $handler, $method ), $invalid )['stop'], $method . ': actual auxiliary callback must check its nonce.' );
			if ( $id_key ) {
				foreach ( array( '102', '103', '104', array( '101' ) ) as $bad_id ) {
					$invalid = $post;
					$invalid[ $id_key ] = $bad_id;
					$result = tourfic_backend_call( array( $handler, $method ), $invalid );
					tourfic_backend_assert( false === $result['json']['success'] && 0 === $GLOBALS['tourfic_test_meta_reads'], $method . ': inaccessible or malformed IDs must fail before metadata access.' );
				}
			} else {
				$invalid = $post;
				$invalid['from'] = 'not-a-date';
				$result = tourfic_backend_call( array( $handler, $method ), $invalid );
				tourfic_backend_assert( false === $result['json']['success'], $method . ': malformed dates must fail before DatePeriod construction.' );
			}
			$result = tourfic_backend_call( array( $handler, $method ), $post );
			tourfic_backend_assert( 'response' === $result['stop'] && ! $GLOBALS['tourfic_test_orders'], $method . ': valid reads must complete without creating orders.' );
			if ( ! $id_key ) {
				tourfic_backend_assert( true === $result['json']['success'] && 17 === $GLOBALS['tourfic_test_query_args']['author'], $method . ': empty collection must remain a successful owner-scoped response.' );
				$GLOBALS['tourfic_test_caps']['tf_manager_options'] = true;
				tourfic_backend_call( array( $handler, $method ), $post );
				tourfic_backend_assert( ! isset( $GLOBALS['tourfic_test_query_args']['author'] ), $method . ': authorized managers retain cross-author collection access.' );
				unset( $GLOBALS['tourfic_test_caps']['tf_manager_options'] );
			}
			if ( 'tf_update_room_fields' === $method ) {
				tourfic_backend_assert( true === $result['json']['success'] && 2 === $result['json']['data']['adults'], 'Room field reads must preserve alphanumeric room identifiers end to end.' );
			}
			if ( 'tf_tour_date_time_update' === $method ) {
				tourfic_backend_assert( 'package' === $result['json']['pricing_rule'], 'Tour form data keeps its flat response contract.' );
			}
		}
	}

	echo 'Backend booking request security: ' . $GLOBALS['tourfic_test_assertions'] . " behavioral assertions passed.\n";
}
