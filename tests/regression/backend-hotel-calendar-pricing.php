<?php
/**
 * Isolated checks for the actual WP admin hotel price provider and room options.
 *
 * Run: php tests/regression/backend-hotel-calendar-pricing.php [provider-file]
 * The optional provider file supports a saved pre-fix negative control.
 * No WordPress bootstrap, database, network, or booking writes are used.
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
$GLOBALS['tourfic_pricing_meta']     = array();
$GLOBALS['tourfic_pricing_posts']    = array();
$GLOBALS['tourfic_pricing_mode']     = null;
$GLOBALS['tourfic_pricing_warnings'] = array();
$GLOBALS['tourfic_pricing_checks']   = 0;
$GLOBALS['tourfic_pricing_failures'] = 0;

set_error_handler( function ( $severity, $message ) {
	$GLOBALS['tourfic_pricing_warnings'][] = $message;
	return true;
} );

function get_post_meta( $post_id, $key, $single = true ) {
	return $GLOBALS['tourfic_pricing_meta'][ $post_id ][ $key ] ?? '';
}
function get_posts( $args ) {
	return array_values( array_filter( $GLOBALS['tourfic_pricing_posts'], function ( $post ) {
		return 'publish' === $post->post_status;
	} ) );
}
function get_the_title( $post_id ) {
	return $GLOBALS['tourfic_pricing_posts'][ $post_id ]->post_title;
}
function absint( $value ) {
	return abs( (int) $value );
}
function sanitize_text_field( $value ) {
	return trim( strip_tags( (string) $value ) );
}
function apply_filters( $hook, $value, ...$args ) {
	if ( 'tourfic_room_pricing_mode' === $hook && is_callable( $GLOBALS['tourfic_pricing_mode'] ) ) {
		return call_user_func( $GLOBALS['tourfic_pricing_mode'], $value, $args[0] );
	}
	return $value;
}
function esc_html__( $text, $domain ) {
	return $text;
}
function wc_price( $price ) {
	return '$' . number_format( $price, 2, '.', '' );
}
function wp_strip_all_tags( $text ) {
	return strip_tags( $text );
}
function update_post_meta() {
	throw new RuntimeException( 'Unexpected metadata write in isolated pricing check' );
}

class Tourfic_Pricing_Response extends RuntimeException {
	public $data;
	public function __construct( $data ) {
		parent::__construct( 'Isolated response' );
		$this->data = $data;
	}
}
function wp_send_json_success( $data ) {
	throw new Tourfic_Pricing_Response( $data );
}

require_once ABSPATH . 'inc/Traits/Singleton.php';
require_once ABSPATH . 'inc/Classes/Room/Room.php';
require_once ABSPATH . 'inc/Classes/Room/Availability.php';
require_once ABSPATH . 'inc/Core/TF_Backend_Booking.php';
require_once ( $argv[1] ?? ABSPATH . 'inc/Admin/Backend_Booking/TF_Hotel_Backend_Booking.php' );

class Tourfic_Pricing_Backend_Hotel extends \Tourfic\Admin\Backend_Booking\TF_Hotel_Backend_Booking {
	// This diagnostic exercises room selection; request security has its own suite.
	protected function read_request( array $schema, $booking = false ) {
		return $_POST;
	}
	protected function authorize_listing( $post_id, $booking = false ) {}
}

function tourfic_pricing_check( $condition, $message ) {
	++$GLOBALS['tourfic_pricing_checks'];
	if ( ! $condition ) {
		++$GLOBALS['tourfic_pricing_failures'];
		fwrite( STDERR, 'FAIL: ' . $message . "\n" );
	}
}

function tourfic_pricing_rule( $from, $to, $price, $adult_price = 0, $child_price = 0 ) {
	return array(
		'check_in' => $from, 'check_out' => $to, 'status' => 'available',
		'price' => $price, 'adult_price' => $adult_price, 'child_price' => $child_price,
	);
}

$backend = ( new ReflectionClass( 'Tourfic_Pricing_Backend_Hotel' ) )->newInstanceWithoutConstructor();
$room = array(
	'price' => 100, 'adult_price' => 30, 'child_price' => 10, 'pricing-by' => '1',
	'price_multi_day' => '1', 'avil_by_date' => '1',
	'avail_date' => json_encode( array( tourfic_pricing_rule( '2026/11/25', '2026/11/30', 100 ) ) ),
);
$person_room = array_replace( $room, array(
	'pricing-by' => '2',
	'avail_date' => json_encode( array(
		tourfic_pricing_rule( '2026/11/25', '2026/11/25', 100, 30, 10 ),
		tourfic_pricing_rule( '2026/11/26', '2026/11/26', 100, 40, 15 ),
	) ),
) );
$fixed_pickup = array(
	'airport_service' => '1',
	'airport_pickup_price' => array( 'airport_pickup_price_type' => 'fixed', 'airport_service_fee_fixed' => 25 ),
);
$cases = array(
	'Calendar room, one night' => array( 'room' => $room, 'expected' => 100 ),
	'Calendar room, three nights and two rooms' => array(
		'room' => $room, 'to' => '2026/11/28', 'rooms' => 2, 'expected' => 600,
	),
	'Calendar prices vary by night' => array(
		'room' => array_replace( $room, array( 'avail_date' => json_encode( array(
			tourfic_pricing_rule( '2026/11/25', '2026/11/25', 90 ),
			tourfic_pricing_rule( '2026/11/26', '2026/11/26', 120 ),
		) ) ) ), 'to' => '2026/11/27', 'expected' => 210,
	),
	'Calendar person pricing' => array(
		'room' => $person_room, 'person_mode' => true, 'to' => '2026/11/27',
		'rooms' => 2, 'adults' => 2, 'children' => 1, 'expected' => 330,
	),
	'Base nightly room pricing' => array(
		'room' => array_replace( $room, array( 'avil_by_date' => '0' ) ),
		'to' => '2026/11/28', 'rooms' => 2, 'expected' => 600,
	),
	'Base price without nightly multiplication' => array(
		'room' => array_replace( $room, array( 'avil_by_date' => '0', 'price_multi_day' => '0' ) ),
		'to' => '2026/11/28', 'rooms' => 2, 'expected' => 200,
	),
	'Base person pricing' => array(
		'room' => array_replace( $person_room, array( 'avil_by_date' => '0' ) ),
		'person_mode' => true, 'to' => '2026/11/27', 'rooms' => 2,
		'adults' => 2, 'children' => 1, 'expected' => 280,
	),
	'Calendar percentage discount' => array(
		'room' => array_replace( $room, array( 'discount_hotel_type' => 'percent', 'discount_hotel_price' => 10 ) ),
		'to' => '2026/11/27', 'expected' => 180,
	),
	'Calendar fixed discount' => array(
		'room' => array_replace( $room, array( 'discount_hotel_type' => 'fixed', 'discount_hotel_price' => 25 ) ),
		'to' => '2026/11/27', 'expected' => 175,
	),
	'Selected fixed pickup service' => array(
		'room' => $room, 'hotel' => $fixed_pickup, 'service' => 'pickup', 'expected' => 125,
		'air_price' => 25, 'air_info' => '( Fixed ) = $25.00',
	),
	'Offered pickup service left unselected' => array(
		'room' => $room, 'hotel' => $fixed_pickup, 'expected' => 100,
	),
);

foreach ( $cases as $label => $case ) {
	$GLOBALS['tourfic_pricing_meta'][501]['tf_hotels_opt'] = $case['hotel'] ?? array();
	$GLOBALS['tourfic_pricing_warnings'] = array();
	// Pro supplies this same pricing-mode choice through the shared filter.
	$GLOBALS['tourfic_pricing_mode'] = ! empty( $case['person_mode'] )
		? function ( $default, $meta ) { return $meta['pricing-by']; } : null;
	try {
		$result = $backend->tf_get_room_total_price(
			501, $case['room'], '2026/11/25', $case['to'] ?? '2026/11/26',
			$case['rooms'] ?? 1, $case['adults'] ?? 1, $case['children'] ?? 0, $case['service'] ?? ''
		);
		tourfic_pricing_check( abs( $result['price_total'] - $case['expected'] ) < 0.00001,
			$label . ': expected ' . $case['expected'] . ', got ' . $result['price_total'] );
		tourfic_pricing_check( ( $case['air_price'] ?? 0 ) === $result['air_service_price'],
			$label . ': airport price stays correct' );
		tourfic_pricing_check( ( $case['air_info'] ?? '' ) === $result['air_service_info'],
			$label . ': airport description stays correct' );
	} catch ( Throwable $error ) {
		tourfic_pricing_check( false, $label . ': ' . $error->getMessage() );
	}
	tourfic_pricing_check( array() === $GLOBALS['tourfic_pricing_warnings'],
		$label . ': no PHP warnings: ' . implode( '; ', $GLOBALS['tourfic_pricing_warnings'] ) );
}

// Distinguish a room-option limit from the current published/enabled/date filters.
$GLOBALS['tourfic_pricing_meta'] = array( 501 => array( 'tf_hotels_opt' => array() ) );
$GLOBALS['tourfic_pricing_warnings'] = array();
foreach ( range( 1, 8 ) as $number ) {
	$id = 1000 + $number;
	$GLOBALS['tourfic_pricing_posts'][$id] = (object) array(
		'ID' => $id, 'post_title' => 'Fixture room ' . $number,
		'post_status' => 7 === $number ? 'draft' : 'publish',
	);
	$meta = array_replace( $room, array(
		'tf_hotel' => 8 === $number ? 502 : 501, 'unique_id' => 'fixture-room-' . $number,
		'enable' => 5 === $number ? '0' : '1',
	) );
	if ( 6 === $number ) {
		$meta['avail_date'] = json_encode( array( tourfic_pricing_rule( '2026/01/01', '2026/01/02', 100 ) ) );
	}
	$GLOBALS['tourfic_pricing_meta'][$id]['tf_room_opt'] = $meta;
}
$_POST = array( 'hotel_id' => 501, 'from' => '2026/11/25', 'to' => '2026/11/27' );
try {
	$backend->tf_check_available_room();
	tourfic_pricing_check( false, 'Room callback returns its options' );
} catch ( Tourfic_Pricing_Response $response ) {
	tourfic_pricing_check( array_keys( $response->data['rooms'] ) === array(
		'fixture-room-1', 'fixture-room-2', 'fixture-room-3', 'fixture-room-4',
	), 'Four eligible rooms remain; disabled, out-of-date, draft and foreign rooms are excluded' );
} catch ( Throwable $error ) {
	tourfic_pricing_check( false, 'Room-option diagnostic: ' . $error->getMessage() );
}
tourfic_pricing_check( array() === $GLOBALS['tourfic_pricing_warnings'],
	'Room-option diagnostic emits no PHP warnings' );

restore_error_handler();
printf( "Backend hotel calendar pricing: %d/%d checks passed.\n",
	$GLOBALS['tourfic_pricing_checks'] - $GLOBALS['tourfic_pricing_failures'], $GLOBALS['tourfic_pricing_checks'] );
exit( $GLOBALS['tourfic_pricing_failures'] ? 1 : 0 );
