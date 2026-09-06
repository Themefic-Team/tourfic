<?php
/**
 * Behavioral regression checks for booking screen and record authorization.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/booking-details-authorization.php
 */

namespace {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

	$GLOBALS['tourfic_test_capabilities'] = array();
	$GLOBALS['tourfic_test_user']         = (object) array(
		'roles' => array(),
		'ID'    => 0,
	);
	$GLOBALS['tourfic_test_authors']      = array();

	function add_action() {}

	function sanitize_key( $value ) {
		return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $value ) );
	}

	function absint( $value ) {
		return abs( (int) $value );
	}

	function current_user_can( $capability ) {
		return ! empty( $GLOBALS['tourfic_test_capabilities'][ $capability ] );
	}

	function wp_get_current_user() {
		return $GLOBALS['tourfic_test_user'];
	}

	function get_current_user_id() {
		return absint( $GLOBALS['tourfic_test_user']->ID );
	}

	function get_post_field( $field, $post_id ) {
		if ( 'post_author' !== $field ) {
			return '';
		}

		return $GLOBALS['tourfic_test_authors'][ absint( $post_id ) ] ?? 0;
	}
}

namespace Tourfic\Traits {
	trait Singleton {}
}

namespace {
	require_once dirname( __DIR__, 2 ) . '/inc/Core/TF_Booking_Details.php';

	class Tourfic_Test_Booking_Details extends \Tourfic\Core\TF_Booking_Details {
		public function voucher_details( $tour_details, $order_details, $billing_details ) {}

		public function voucher_quick_view( $tour_details, $order_details, $billing_details ) {}

		public function check_in_out_status( $order_details ) {}

		public function can_access_screen() {
			return $this->tf_current_user_can_access_booking_screen();
		}

		public function can_manage_record( $record ) {
			return $this->tf_current_user_can_manage_booking( $record );
		}
	}

	function tourfic_booking_authorization_assert( $condition, $message ) {
		if ( ! $condition ) {
			fwrite( STDERR, "FAIL: {$message}\n" );
			exit( 1 );
		}
	}

	$booking_details = new Tourfic_Test_Booking_Details(
		array(
			'booking_type' => 'hotel',
		)
	);

	$GLOBALS['tourfic_test_capabilities']['edit_tf_hotels'] = true;
	$GLOBALS['tourfic_test_user']                           = (object) array(
		'roles' => array( 'tf_vendor' ),
		'ID'    => 17,
	);
	$GLOBALS['tourfic_test_authors']                        = array(
		101 => 17,
		102 => 29,
	);

	tourfic_booking_authorization_assert(
		$booking_details->can_access_screen(),
		'A vendor with the booking-type capability must be able to open the booking list.'
	);
	tourfic_booking_authorization_assert(
		$booking_details->can_manage_record(
			(object) array(
				'post_type' => 'hotel',
				'post_id'   => 101,
			)
		),
		'A vendor must be able to access a booking for their own listing.'
	);
	tourfic_booking_authorization_assert(
		! $booking_details->can_manage_record(
			(object) array(
				'post_type' => 'hotel',
				'post_id'   => 102,
			)
		),
		'A vendor must not be able to access another vendor\'s booking.'
	);

	$GLOBALS['tourfic_test_capabilities']['edit_tf_hotels'] = false;
	tourfic_booking_authorization_assert(
		! $booking_details->can_access_screen(),
		'A user without the booking-type capability must not access the booking list.'
	);

	echo "Booking details authorization regression checks passed.\n";
}
