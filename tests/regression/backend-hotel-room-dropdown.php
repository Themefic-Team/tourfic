<?php
/**
 * Exercise both real manual-booking room dropdown providers without WordPress.
 *
 * Run: php tests/regression/backend-hotel-room-dropdown.php [free-file] [pro-file]
 * Optional files support pre-fix controls and a reviewed companion preview.
 * No bootstrap, database, network, metadata writes, or bookings are used.
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
$GLOBALS['tourfic_dropdown_posts'] = array();
$GLOBALS['tourfic_dropdown_meta'] = array();
$GLOBALS['tourfic_dropdown_warnings'] = array();
$checks = 0;
$failures = 0;

set_error_handler( function ( $severity, $message ) {
	$GLOBALS['tourfic_dropdown_warnings'][] = $message;
	return true;
} );

function get_post_meta( $id, $key, $single = true ) {
	return $GLOBALS['tourfic_dropdown_meta'][$id][$key] ?? '';
}
function get_posts( $args ) {
	return array_values( array_filter( $GLOBALS['tourfic_dropdown_posts'], function ( $post ) {
		return 'publish' === $post->post_status;
	} ) );
}
function get_the_title( $id ) {
	return $GLOBALS['tourfic_dropdown_posts'][$id]->post_title;
}
function absint( $value ) { return abs( (int) $value ); }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function esc_html__( $text, $domain ) { return $text; }
function apply_filters( $hook, $value, ...$args ) { return $value; }
function rest_ensure_response( $data ) { return $data; }
function update_post_meta() { throw new RuntimeException( 'Unexpected metadata write' ); }

class Tourfic_Dropdown_Response extends RuntimeException {
	public $data;
	public function __construct( $data ) {
		parent::__construct( 'Isolated response' );
		$this->data = $data;
	}
}
function wp_send_json_success( $data ) { throw new Tourfic_Dropdown_Response( $data ); }

require_once ABSPATH . 'inc/Traits/Singleton.php';
require_once ABSPATH . 'inc/Classes/Room/Room.php';
require_once ABSPATH . 'inc/Classes/Room/Availability.php';
require_once ABSPATH . 'inc/Core/TF_Backend_Booking.php';
require_once ( $argv[1] ?? ABSPATH . 'inc/Admin/Backend_Booking/TF_Hotel_Backend_Booking.php' );

// Keep request security separate: its existing suite exercises 187 assertions.
class Tourfic_Dropdown_Free extends \Tourfic\Admin\Backend_Booking\TF_Hotel_Backend_Booking {
	protected function read_request( array $schema, $booking = false ) { return $_POST; }
	protected function authorize_listing( $id, $booking = false ) {}
}
class TF_FD_Rest_API { public function __construct() {} }
function get_hotel_rooms( $hotel_id ) {
	// Canonical fixtures use the same published parent association in both providers.
	return \Tourfic\Classes\Room\Room::get_hotel_rooms( $hotel_id );
}
require_once ( $argv[2] ?? dirname( rtrim( ABSPATH, '/' ) )
	. '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Hotel_Backend_Booking_Rest_API.php' );

$free = ( new ReflectionClass( 'Tourfic_Dropdown_Free' ) )->newInstanceWithoutConstructor();
$pro = ( new ReflectionClass( 'TF_FD_Hotel_Backend_Booking_Rest_API' ) )->newInstanceWithoutConstructor();
$request = new class {
	public function get_param( $key ) { return $_POST[$key]; }
};

function tourfic_dropdown_rule( $from, $to, $status = 'available' ) {
	return array(
		'check_in' => $from, 'check_out' => $to, 'price' => 100,
		'adult_price' => 0, 'child_price' => 0, 'status' => $status,
	);
}
function tourfic_dropdown_room( $rules = array(), $nightly = '1', $overrides = array() ) {
	return array_replace( array(
		'tf_hotel' => 501, 'unique_id' => 'fixture-room-1', 'enable' => '1',
		'price' => 100, 'pricing-by' => '1', 'price_multi_day' => $nightly,
		'avil_by_date' => '1', 'avail_date' => json_encode( $rules ),
	), $overrides );
}

$check_in_only = array( tourfic_dropdown_rule( '2026/11/25', '2026/11/25' ) );
$check_out_only = array( tourfic_dropdown_rule( '2026/11/26', '2026/11/26' ) );
$complete = array( tourfic_dropdown_rule( '2026/11/25', '2026/11/30' ) );
$cases = array(
	'Four eligible rooms include two check-in-only calendar rooms' => array(
		'rooms' => array(
			tourfic_dropdown_room( array(), '1', array( 'avil_by_date' => '0' ) ),
			tourfic_dropdown_room( array(), '1', array( 'avil_by_date' => '0' ) ),
			tourfic_dropdown_room( $check_in_only ), tourfic_dropdown_room( $check_in_only ),
		), 'expected' => array( 1, 2, 3, 4 ),
	),
	'Nightly stay includes check-in' => array(
		'room' => tourfic_dropdown_room( $check_in_only ), 'expected' => array( 1 ), 'expected_price' => 100,
	),
	'Nightly stay excludes checkout' => array( 'room' => tourfic_dropdown_room( $check_out_only ), 'expected' => array() ),
	'Nightly full coverage excludes checkout' => array(
		'room' => tourfic_dropdown_room( array( tourfic_dropdown_rule( '2026/11/25', '2026/11/26' ) ) ),
		'to' => '2026/11/27', 'expected' => array( 1 ),
	),
	'Partial stay is rejected' => array(
		'room' => tourfic_dropdown_room( $check_out_only ), 'to' => '2026/11/28', 'expected' => array(),
	),
	'Daily stay requires check-in coverage' => array( 'room' => tourfic_dropdown_room( $check_out_only, '0' ), 'expected' => array() ),
	'Daily stay requires checkout coverage' => array( 'room' => tourfic_dropdown_room( $check_in_only, '0' ), 'expected' => array() ),
	'Daily full coverage and pricing include checkout' => array(
		'room' => tourfic_dropdown_room( $complete, '0' ), 'expected' => array( 1 ), 'expected_price' => 200,
	),
	'Unavailable override blocks occupied night' => array(
		'room' => tourfic_dropdown_room( array_merge( $complete, array(
			tourfic_dropdown_rule( '2026/11/26', '2026/11/26', 'unavailable' ),
		) ) ), 'to' => '2026/11/28', 'expected' => array(),
	),
	'Nightly checkout blackout does not block stay' => array(
		'room' => tourfic_dropdown_room( array_merge( $check_in_only, array(
			tourfic_dropdown_rule( '2026/11/26', '2026/11/26', 'unavailable' ),
		) ) ), 'expected' => array( 1 ),
	),
	'Daily checkout blackout blocks stay' => array(
		'room' => tourfic_dropdown_room( array_merge( $complete, array(
			tourfic_dropdown_rule( '2026/11/26', '2026/11/26', 'unavailable' ),
			) ), '0' ), 'expected' => array(),
	),
	'Blackout-only calendar stays excluded without explicit priced dates' => array(
		'room' => tourfic_dropdown_room( array( tourfic_dropdown_rule( '2026/01/01', '2026/01/01', 'unavailable' ) ) ),
		'expected' => array(),
	),
	'Disabled room is excluded' => array( 'room' => tourfic_dropdown_room( $complete, '1', array( 'enable' => '0' ) ), 'expected' => array() ),
	'Foreign hotel room is excluded' => array( 'room' => tourfic_dropdown_room( $complete, '1', array( 'tf_hotel' => 502 ) ), 'expected' => array() ),
	'Draft room is excluded' => array( 'room' => tourfic_dropdown_room( $complete ), 'status' => 'draft', 'expected' => array() ),
	'No-calendar room remains available' => array( 'room' => tourfic_dropdown_room( array(), '1', array( 'avil_by_date' => '0' ) ), 'expected' => array( 1 ) ),
	'Empty enabled calendar stays excluded' => array( 'room' => tourfic_dropdown_room(), 'expected' => array() ),
	'Blank enabled calendar stays excluded' => array( 'room' => tourfic_dropdown_room( array(), '1', array( 'avail_date' => '' ) ), 'expected' => array() ),
	'Malformed JSON stays excluded without warnings' => array( 'room' => tourfic_dropdown_room( array(), '1', array( 'avail_date' => '{' ) ), 'expected' => array() ),
	'Non-array JSON stays excluded without warnings' => array( 'room' => tourfic_dropdown_room( array(), '1', array( 'avail_date' => '"invalid"' ) ), 'expected' => array() ),
	'Malformed dated rule stays excluded' => array( 'room' => tourfic_dropdown_room( array( array( 'status' => 'unavailable' ) ) ), 'expected' => array() ),
);

foreach ( $cases as $label => $case ) {
	$GLOBALS['tourfic_dropdown_posts'] = array();
	$GLOBALS['tourfic_dropdown_meta'] = array( 501 => array( 'tf_hotels_opt' => array() ) );
	foreach ( $case['rooms'] ?? array( $case['room'] ) as $index => $room ) {
		$id = 1001 + $index;
		$room['unique_id'] = 'fixture-room-' . ( $index + 1 );
		$GLOBALS['tourfic_dropdown_meta'][$id]['tf_room_opt'] = $room;
		$GLOBALS['tourfic_dropdown_posts'][$id] = (object) array(
			'ID' => $id, 'post_title' => 'Fixture room ' . ( $index + 1 ), 'post_status' => $case['status'] ?? 'publish',
		);
	}
	$_POST = array( 'hotel_id' => 501, 'from' => '2026/11/25', 'to' => $case['to'] ?? '2026/11/26' );
	$expected = array_map( function ( $number ) { return 'fixture-room-' . $number; }, $case['expected'] );
	foreach ( array( 'Free' => $free, 'Pro' => $pro ) as $provider => $instance ) {
		$GLOBALS['tourfic_dropdown_warnings'] = array();
		$error = '';
		$actual = null;
		try {
			if ( 'Free' === $provider ) {
				$instance->tf_check_available_room();
			} else {
				$result = $instance->tf_fd_available_hotel_room_and_service_type( $request );
				$actual = array_keys( $result['rooms'] );
			}
		} catch ( Tourfic_Dropdown_Response $response ) {
			$actual = array_keys( $response->data['rooms'] );
		} catch ( Throwable $exception ) {
			$error = $exception->getMessage();
		}
		if ( isset( $case['expected_price'] ) ) {
			try {
				$price = $instance->tf_get_room_total_price(
					501, $case['room'], $_POST['from'], $_POST['to'], 1, 1, 0, ''
				);
				if ( (float) $price['price_total'] !== (float) $case['expected_price'] ) {
					$error .= ' Expected price ' . $case['expected_price'] . ', got ' . $price['price_total'] . '.';
				}
			} catch ( Throwable $exception ) {
				$error .= $exception->getMessage();
			}
		}
		++$checks;
		if ( $actual !== $expected || '' !== $error || $GLOBALS['tourfic_dropdown_warnings'] ) {
			++$failures;
			fwrite( STDERR, $provider . ' FAIL: ' . $label . '; expected ' . json_encode( $expected )
				. ', got ' . json_encode( $actual ) . '; ' . $error
				. implode( '; ', $GLOBALS['tourfic_dropdown_warnings'] ) . "\n" );
		}
	}
}
restore_error_handler();
printf( "Backend hotel room dropdown: %d/%d provider scenarios passed.\n", $checks - $failures, $checks );
exit( $failures ? 1 : 0 );
