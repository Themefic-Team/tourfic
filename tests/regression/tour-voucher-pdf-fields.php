<?php
/**
 * Regression coverage for customer-facing Tour voucher PDF fields.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/tour-voucher-pdf-fields.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

$tourfic_test_root = dirname( __DIR__, 2 );
$tourfic_pro_root  = dirname( $tourfic_test_root ) . '/tourfic-pro';

define( 'TF_PRO_INC_PATH', $tourfic_pro_root . '/inc/' );
define( 'TF_PRO_ASSETS_URL', 'https://example.test/tourfic-pro/' );

function tourfic_voucher_pdf_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}

function __( $text, $domain = 'default' ) {
	return $text;
}

function wp_timezone() {
	global $tourfic_test_site_timezone;

	return new DateTimeZone( $tourfic_test_site_timezone );
}

function wp_date( $format, $timestamp = null, $timezone = null ) {
	$timestamp = null === $timestamp ? time() : $timestamp;
	$timezone  = $timezone instanceof DateTimeZone ? $timezone : wp_timezone();

	return ( new DateTimeImmutable( '@' . $timestamp ) )->setTimezone( $timezone )->format( $format );
}

function wc_get_order_status_name( $status ) {
	return 'pending' === $status ? 'Pending payment' : ucfirst( $status );
}

function wp_strip_all_tags( $text ) {
	return strip_tags( $text );
}

function wp_specialchars_decode( $text, $quote_style = ENT_NOQUOTES ) {
	return html_entity_decode( $text, $quote_style, 'UTF-8' );
}

class Tourfic_Test_Voucher_Countries {
	public function get_formatted_address( $address, $separator = '<br/>' ) {
		return implode( $separator, array_filter( array_values( $address ) ) );
	}
}

function WC() {
	static $woocommerce;

	if ( ! $woocommerce ) {
		$woocommerce            = new stdClass();
		$woocommerce->countries = new Tourfic_Test_Voucher_Countries();
	}

	return $woocommerce;
}

class Tourfic_Test_Voucher_Order {
	private $paid;
	private $status;

	public function __construct( $paid, $status ) {
		$this->paid   = $paid;
		$this->status = $status;
	}

	public function is_paid() {
		return $this->paid;
	}

	public function get_status() {
		return $this->status;
	}

	public function get_address( $type ) {
		return array(
			'first_name' => 'Ada',
			'last_name'  => 'Lovelace',
			'company'    => '',
			'address_1'  => 'Kaya 1',
			'address_2'  => '',
			'city'       => 'Willemstad',
			'state'      => '',
			'postcode'   => '0000',
			'country'    => 'CW',
			'email'      => 'ada@example.test',
			'phone'      => '+5999000000',
		);
	}
}

date_default_timezone_set( 'UTC' );
$tourfic_test_site_timezone = 'America/Curacao';
require_once $tourfic_pro_root . '/inc/functions/functions_qr_code.php';

tourfic_voucher_pdf_assert(
	'09 October, 2026' === wp_date( 'd F, Y', strtotime( '2026/10/10' ) ),
	'The fixture must reproduce the reported negative-offset date shift.'
);
tourfic_voucher_pdf_assert(
	function_exists( 'tourfic_pro_format_voucher_date' )
		&& '10 October, 2026' === tourfic_pro_format_voucher_date( '2026/10/10' ),
	'The voucher formatter must preserve a calendar date in America/Curacao.'
);
tourfic_voucher_pdf_assert(
	'12 October, 2026' === tourfic_pro_format_voucher_date( '2026/10/12' )
		&& '' === tourfic_pro_format_voucher_date( '2026/02/30' ),
	'The voucher formatter must preserve valid range endpoints and reject invalid dates.'
);

$tourfic_test_site_timezone = 'Asia/Dhaka';
tourfic_voucher_pdf_assert(
	'10 October, 2026' === tourfic_pro_format_voucher_date( '2026/10/10' ),
	'The voucher formatter must preserve the same calendar date east of UTC.'
);

$paid_order    = new Tourfic_Test_Voucher_Order( true, 'completed' );
$pending_order = new Tourfic_Test_Voucher_Order( false, 'pending' );

tourfic_voucher_pdf_assert(
	function_exists( 'tourfic_pro_get_voucher_payment_status' )
		&& 'Paid' === tourfic_pro_get_voucher_payment_status( $paid_order )
		&& 'Pending payment' === tourfic_pro_get_voucher_payment_status( $pending_order ),
	'The voucher must show a customer-facing payment state instead of a gateway slug.'
);
tourfic_voucher_pdf_assert(
	function_exists( 'tourfic_pro_get_voucher_billing_address' )
		&& 'Kaya 1, Willemstad, 0000, CW' === tourfic_pro_get_voucher_billing_address( $paid_order ),
	'The voucher must format the order billing address without repeating customer contact fields.'
);

$tourfic_pro_qr_source = file_get_contents( $tourfic_pro_root . '/inc/functions/functions_qr_code.php' );
tourfic_voucher_pdf_assert(
	3 <= substr_count( $tourfic_pro_qr_source, 'tourfic_pro_format_voucher_date(' )
		&& false !== strpos( $tourfic_pro_qr_source, 'tourfic_pro_get_voucher_payment_status( $tf_order )' )
		&& false !== strpos( $tourfic_pro_qr_source, "esc_html__( 'Billing Address:', 'tourfic' )" )
		&& false === strpos( $tourfic_pro_qr_source, "esc_html__( 'Payment Status:', 'tourfic' ) . ' '. esc_html( \$tf_order->get_payment_method() )" ),
	'The PDF renderer must use the safe date, payment-status, and billing-address values.'
);

echo "Tourfic voucher PDF field regression checks passed.\n";
