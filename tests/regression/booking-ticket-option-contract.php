<?php
/**
 * Regression checks for booking order lookup and ticket check-in option keys.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/booking-ticket-option-contract.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$tourfic_test_options = array();
$tourfic_test_orders  = array();

function absint( $value ) {
	return abs( (int) $value );
}

function get_option( $name, $default = false ) {
	global $tourfic_test_options;

	return array_key_exists( $name, $tourfic_test_options ) ? $tourfic_test_options[ $name ] : $default;
}

function update_option( $name, $value, $autoload = null ) {
	global $tourfic_test_options;
	$changed                       = ! array_key_exists( $name, $tourfic_test_options ) || $tourfic_test_options[ $name ] !== $value;
	$tourfic_test_options[ $name ] = $value;

	return $changed;
}

function delete_option( $name ) {
	global $tourfic_test_options;
	$exists = array_key_exists( $name, $tourfic_test_options );
	unset( $tourfic_test_options[ $name ] );

	return $exists;
}

function wc_get_order( $order_id ) {
	global $tourfic_test_orders;

	return isset( $tourfic_test_orders[ $order_id ] ) ? $tourfic_test_orders[ $order_id ] : false;
}

class Tourfic_Booking_Test_Item {
	private $meta;

	public function __construct( $unique_id, $order_type = 'tour', $tour_id = 1 ) {
		$this->meta = array(
			'_order_type'    => $order_type,
			'_tour_unique_id' => $unique_id,
			'_tour_id'        => $tour_id,
		);
	}

	public function get_meta( $key, $single = true ) {
		return isset( $this->meta[ $key ] ) ? $this->meta[ $key ] : '';
	}
}

class Tourfic_Booking_Test_Order {
	private $items;

	public function __construct( $unique_ids ) {
		$this->items = array_map(
			static function ( $item ) {
				if ( is_array( $item ) ) {
					return new Tourfic_Booking_Test_Item(
						$item['unique_id'],
						isset( $item['order_type'] ) ? $item['order_type'] : 'tour',
						isset( $item['tour_id'] ) ? $item['tour_id'] : 1
					);
				}

				return new Tourfic_Booking_Test_Item( $item );
			},
			$unique_ids
		);
	}

	public function get_items() {
		return $this->items;
	}
}

class Tourfic_Booking_Test_Wpdb {
	public $prefix = 'wp_';
	public $candidate_order_ids = array();
	public $prepared_args = array();

	public function prepare( $query, ...$args ) {
		$this->prepared_args = $args;

		return $query;
	}

	public function get_col( $query ) {
		return $this->candidate_order_ids;
	}
}

$wpdb = new Tourfic_Booking_Test_Wpdb();

require_once dirname( __DIR__, 2 ) . '/inc/Traits/Singleton.php';
require_once dirname( __DIR__, 2 ) . '/inc/Traits/TF_Fonts.php';
require_once dirname( __DIR__, 2 ) . '/inc/Traits/Action_Helper.php';
require_once dirname( __DIR__, 2 ) . '/inc/Classes/Helper.php';

use Tourfic\Classes\Helper;

function tourfic_booking_ticket_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

tourfic_booking_ticket_assert(
	'tourfic_booking_order_id_123' === Helper::tourfic_booking_order_id_option_name( '123' )
		&& 'tourfic_booking_checkin_status_123' === Helper::tourfic_booking_checkin_status_option_name( '123' )
		&& Helper::tourfic_booking_order_id_option_name( '123' ) !== Helper::tourfic_booking_checkin_status_option_name( '123' ),
	'Order lookup and check-in status must use distinct canonical option keys.'
);

tourfic_booking_ticket_assert(
	'' === Helper::tourfic_booking_unique_id( '../123' )
		&& '' === Helper::tourfic_booking_order_id_option_name( '<b>123</b>' )
		&& '' === Helper::tourfic_booking_unique_id( array( '123' ) )
		&& '' === Helper::tourfic_booking_unique_id( (object) array( 'id' => 123 ) )
		&& 0 === Helper::tourfic_get_booking_order_id_by_unique_id( '123 OR 1=1' ),
	'Voucher IDs must be scalar complete numeric values.'
);

$tourfic_test_orders[501]                             = new Tourfic_Booking_Test_Order( array( '123' ) );
$tourfic_test_options['tourfic_booking_order_id_123'] = 999;
$wpdb->candidate_order_ids                            = array( 501 );
tourfic_booking_ticket_assert(
	501 === Helper::tourfic_get_booking_order_id_by_unique_id( '123' )
		&& 501 === $tourfic_test_options['tourfic_booking_order_id_123']
		&& array( '_tour_unique_id', '123' ) === $wpdb->prepared_args,
	'Authoritative item metadata must repair a stale canonical order cache.'
);

$tourfic_test_orders[502]     = new Tourfic_Booking_Test_Order( array( '124' ) );
$tourfic_test_options['124']  = 999;
$wpdb->candidate_order_ids    = array( 502 );
tourfic_booking_ticket_assert(
	502 === Helper::tourfic_get_booking_order_id_by_unique_id( '124' )
		&& 502 === $tourfic_test_options['tourfic_booking_order_id_124'],
	'Authoritative item metadata must recover a booking without trusting its historical raw option.'
);

$tourfic_test_orders[503]                 = new Tourfic_Booking_Test_Order( array( '125' ) );
$tourfic_test_options['tourfic_125']      = 999;
$wpdb->candidate_order_ids                = array( 503 );
tourfic_booking_ticket_assert(
	503 === Helper::tourfic_get_booking_order_id_by_unique_id( '125' )
		&& 503 === $tourfic_test_options['tourfic_booking_order_id_125'],
	'Authoritative item metadata must recover a booking without trusting the collided numeric option.'
);

$tourfic_test_orders[504]            = new Tourfic_Booking_Test_Order( array( '126' ) );
$tourfic_test_options['tourfic_126'] = 'in';
$wpdb->candidate_order_ids           = array( 504 );
tourfic_booking_ticket_assert(
	504 === Helper::tourfic_get_booking_order_id_by_unique_id( '126' )
		&& 504 === $tourfic_test_options['tourfic_booking_order_id_126']
		&& 'in' === Helper::tourfic_get_booking_checkin_status( '126' ),
	'A collided status key must recover its order from the authoritative line item without losing status.'
);

$tourfic_test_orders[505]       = new Tourfic_Booking_Test_Order( array( '999' ) );
$tourfic_test_options['127']    = 505;
$wpdb->candidate_order_ids      = array( 505 );
tourfic_booking_ticket_assert( 0 === Helper::tourfic_get_booking_order_id_by_unique_id( '127' ), 'A mismatched order-item candidate must fail closed.' );

$tourfic_test_orders[506]  = new Tourfic_Booking_Test_Order( array( '128' ) );
$tourfic_test_orders[507]  = new Tourfic_Booking_Test_Order( array( '128' ) );
$wpdb->candidate_order_ids = array( 506, 507 );
tourfic_booking_ticket_assert( 0 === Helper::tourfic_get_booking_order_id_by_unique_id( '128' ), 'An ambiguous voucher ID must fail closed.' );

$tourfic_test_orders[508]  = new Tourfic_Booking_Test_Order( array( '129', '129' ) );
$wpdb->candidate_order_ids = array( 508, 508 );
tourfic_booking_ticket_assert( 0 === Helper::tourfic_get_booking_order_id_by_unique_id( '129' ), 'Duplicate voucher IDs within one order must fail closed.' );

$tourfic_test_orders[509]  = new Tourfic_Booking_Test_Order( array( '130', '131' ) );
$wpdb->candidate_order_ids = array( 509 );
tourfic_booking_ticket_assert(
	509 === Helper::tourfic_get_booking_order_id_by_unique_id( '130' )
		&& 509 === Helper::tourfic_get_booking_order_id_by_unique_id( '131' ),
	'Each voucher in a multi-tour order must resolve independently.'
);

$tourfic_test_orders[510] = new Tourfic_Booking_Test_Order(
	array(
		array( 'unique_id' => '132', 'tour_id' => 10 ),
		array( 'unique_id' => '133', 'tour_id' => 20 ),
	)
);
$wpdb->candidate_order_ids = array( 510 );
tourfic_booking_ticket_assert(
	'132' === Helper::tourfic_get_single_tour_booking_unique_id( 510, 10 )
		&& '133' === Helper::tourfic_get_single_tour_booking_unique_id( 510, 20 )
		&& Helper::tourfic_booking_order_matches_unique_id( 510, '132', 10 )
		&& ! Helper::tourfic_booking_order_matches_unique_id( 510, '132', 20 ),
	'A missing booking-row ID may be recovered only from the one matching tour line item.'
);

$tourfic_test_orders[511] = new Tourfic_Booking_Test_Order(
	array(
		array( 'unique_id' => '134', 'tour_id' => 30 ),
		array( 'unique_id' => '135', 'tour_id' => 30 ),
	)
);
tourfic_booking_ticket_assert(
	'' === Helper::tourfic_get_single_tour_booking_unique_id( 511, 30 )
		&& '' === Helper::tourfic_get_single_tour_booking_unique_id( 510, 0 )
		&& ! Helper::tourfic_booking_order_matches_unique_id( 508, '129', 1 ),
	'A booking row shared by multiple matching tour items must not guess a voucher ID.'
);

$tourfic_test_options['tf_136'] = 'check in';
tourfic_booking_ticket_assert(
	'in' === Helper::tourfic_get_booking_checkin_status( '136' )
		&& 'in' === $tourfic_test_options['tourfic_booking_checkin_status_136'],
	'Historical check-in values must normalize and cache under the canonical status key.'
);

$tourfic_test_options['tourfic_booking_order_id_136'] = 512;
Helper::tourfic_update_booking_checkin_status( '136', 'out' );
tourfic_booking_ticket_assert(
	'' === Helper::tourfic_get_booking_checkin_status( '136' )
		&& '' === $tourfic_test_options['tourfic_booking_checkin_status_136']
		&& 512 === $tourfic_test_options['tourfic_booking_order_id_136']
		&& 'check in' === $tourfic_test_options['tf_136'],
	'Clearing check-in must override legacy state without changing order lookup or deleting compatibility data.'
);

echo "Tourfic booking ticket option contract regression checks passed.\n";
