<?php
/**
 * Exercise the report handler with in-memory order fixtures, without WordPress bootstrap.
 * Run: php tests/security/month-report-request-security.php
 * TOURFIC_REPORT_ACTION_SOURCE can select an original or preview Action_Helper.php.
 */

if ( 'cli' !== PHP_SAPI || defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$tourfic_root = dirname( __DIR__, 2 );
define( 'ABSPATH', $tourfic_root . '/' );

$tourfic_report_checks = 0;
$tourfic_report_failures = array();
$tourfic_report_capability = false;
$tourfic_report_reads = 0;
$tourfic_report_orders = array();

class Tourfic_Report_Fixture_Response extends RuntimeException {
	public $data;
	public $status;
	public function __construct( $data, $status ) {
		$this->data = $data;
		$this->status = $status;
	}
}

function current_user_can( $capability ) {
	return 'manage_options' === $capability && $GLOBALS['tourfic_report_capability'];
}
function wp_unslash( $value ) { return is_string( $value ) ? stripslashes( $value ) : $value; }
function sanitize_text_field( $value ) { return is_string( $value ) ? trim( strip_tags( $value ) ) : ''; }
function sanitize_key( $value ) { return is_scalar( $value ) ? preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $value ) ) : ''; }
function esc_html__( $value, $domain = '' ) { return $value; }
function wp_verify_nonce( $nonce, $action ) { return 'valid-' . $action === $nonce; }
function check_ajax_referer( $action, $field ) {
	if ( ! wp_verify_nonce( $_POST[ $field ] ?? '', $action ) ) {
		throw new Tourfic_Report_Fixture_Response( array(), 403 );
	}
}
function wp_send_json_error( $data, $status = 200 ) {
	throw new Tourfic_Report_Fixture_Response( array( 'success' => false, 'data' => $data ), $status );
}
function wp_json_encode( $data ) {
	// Intercept the final JSON before the production handler terminates the CLI fixture.
	throw new Tourfic_Report_Fixture_Response( $data, 200 );
}

class WC_Order_Query {
	public function __construct( $args ) { ++$GLOBALS['tourfic_report_reads']; }
	public function get_orders() { return array_keys( $GLOBALS['tourfic_report_orders'] ); }
}
function wc_get_order( $id ) { return $GLOBALS['tourfic_report_orders'][ $id ]; }
class Tourfic_Report_Fixture_Order {
	private $date;
	private $status;
	public function __construct( $date, $status ) { $this->date = new Tourfic_Report_Fixture_Date( $date ); $this->status = $status; }
	public function get_date_created() { return $this->date; }
	public function get_status() { return $this->status; }
}
class Tourfic_Report_Fixture_Date extends DateTimeImmutable {
	public function date( $format ) { return $this->format( $format ); }
}

require getenv( 'TOURFIC_REPORT_ACTION_SOURCE' ) ?: $tourfic_root . '/inc/Traits/Action_Helper.php';
class Tourfic_Report_Fixture_Handler {
	use \Tourfic\Traits\Action_Helper;
}

function tourfic_report_expect( $condition, $label ) {
	++$GLOBALS['tourfic_report_checks'];
	if ( ! $condition ) {
		$GLOBALS['tourfic_report_failures'][] = $label;
	}
}
function tourfic_report_request( $can_manage, $fields ) {
	$GLOBALS['tourfic_report_capability'] = $can_manage;
	$GLOBALS['tourfic_report_reads'] = 0;
	$_POST = $fields;
	try {
		( new Tourfic_Report_Fixture_Handler() )->tf_month_chart_filter_callback();
	} catch ( Tourfic_Report_Fixture_Response $response ) {
		return $response;
	} catch ( Throwable $error ) {
		tourfic_report_expect( false, 'Unexpected handler error: ' . $error->getMessage() );
		return null;
	}
	return null;
}

$tourfic_report_orders = array(
	1 => new Tourfic_Report_Fixture_Order( '2026-01-03', 'completed' ),
	2 => new Tourfic_Report_Fixture_Order( '2026-01-03', 'refunded' ),
	3 => new Tourfic_Report_Fixture_Order( '2026-02-04', 'completed' ),
);
$valid = array( '_nonce' => 'valid-tourfic_month_reports', 'month' => '1', 'year' => '26' );

foreach ( array( 'valid-updates', 'valid-tourfic_month_reports', '' ) as $nonce ) {
	$response = tourfic_report_request( false, array_merge( $valid, array( '_nonce' => $nonce ) ) );
	tourfic_report_expect( $response && 403 === $response->status, 'A user without dashboard permission is denied.' );
	tourfic_report_expect( 0 === $tourfic_report_reads, 'Denied users cannot query store orders.' );
}

foreach ( array( null, '', 'invalid', 'valid-updates', array( 'valid-tourfic_month_reports' ) ) as $nonce ) {
	$fields = array_merge( $valid, array( '_nonce' => $nonce ) );
	if ( null === $nonce ) { unset( $fields['_nonce'] ); }
	$response = tourfic_report_request( true, $fields );
	tourfic_report_expect( $response && 403 === $response->status, 'An administrator still needs the dedicated report nonce.' );
	tourfic_report_expect( 0 === $tourfic_report_reads, 'Invalid nonces cause no order reads.' );
}

foreach ( array(
	array( 'month' => '0' ), array( 'month' => '13' ), array( 'month' => '1.5' ),
	array( 'month' => '1month' ), array( 'month' => '-1' ), array( 'month' => array( '1' ) ),
	array( 'year' => '2026' ), array( 'year' => '26year' ), array( 'year' => '-1' ),
	array( 'year' => array( '26' ) ), array( 'year' => '' ), array( 'month' => '' ),
) as $invalid ) {
	$response = tourfic_report_request( true, array_merge( $valid, $invalid ) );
	tourfic_report_expect( $response && 400 === $response->status, 'Malformed dates are rejected.' );
	tourfic_report_expect( 0 === $tourfic_report_reads, 'Malformed dates cause no order reads.' );
}

$response = tourfic_report_request( true, $valid );
tourfic_report_expect( $response && 200 === $response->status && 1 === $tourfic_report_reads, 'Authorized report request still reads orders.' );
if ( $response && 200 === $response->status ) {
	$data = $response->data;
	tourfic_report_expect( array( 'months_day_number', 'tf_complete_orders', 'tf_cancel_orders', 'tf_search_month' ) === array_keys( $data ), 'The chart JSON shape is preserved.' );
	tourfic_report_expect( range( 1, 31 ) === $data['months_day_number'] && 'January' === $data['tf_search_month'], 'January labels are preserved.' );
	tourfic_report_expect( 1 === $data['tf_complete_orders'][2] && 1 === $data['tf_cancel_orders'][2], 'Completed and refunded counts are preserved.' );
	tourfic_report_expect( 1 === array_sum( $data['tf_complete_orders'] ) && 1 === array_sum( $data['tf_cancel_orders'] ), 'Orders outside the requested month are excluded.' );
}
foreach ( array( '24' => 29, '26' => 28, '00' => 29 ) as $year => $days ) {
	$response = tourfic_report_request( true, array_merge( $valid, array( 'month' => '2', 'year' => (string) $year ) ) );
	tourfic_report_expect( $response && 200 === $response->status && $days === count( $response->data['months_day_number'] ), 'Two-digit report years preserve correct February length.' );
}

$enqueue = file_get_contents( $tourfic_root . '/inc/Classes/Enqueue.php' );
tourfic_report_expect( 2 === substr_count( $enqueue, "'month_reports_nonce'" ) && 2 === substr_count( $enqueue, "wp_create_nonce( 'tourfic_month_reports' )" ), 'Both admin localizations publish the report nonce.' );
foreach ( array( 'sass/admin/js/free/tf-options.js', 'assets/admin/js/tourfic-admin-scripts.js' ) as $file ) {
	$source = file_get_contents( $tourfic_root . '/' . $file );
	tourfic_report_expect( 2 === preg_match_all( "/action: 'tourfic_month_reports',\\s*_nonce: tourficAdminParams\\.month_reports_nonce/", $source ), 'Both chart requests use the dedicated nonce in ' . $file );
}
$bundle = file_get_contents( $tourfic_root . '/assets/admin/js/tourfic-admin-scripts.min.js' );
tourfic_report_expect( 2 === preg_match_all( '/action:["\']tourfic_month_reports["\'],_nonce:tourficAdminParams\.month_reports_nonce/', $bundle ), 'The shipped minified chart callers use the dedicated nonce.' );

foreach ( $tourfic_report_failures as $failure ) {
	echo 'FAIL: ' . $failure . "\n";
}
echo json_encode( array( 'checks' => $tourfic_report_checks, 'failures' => count( $tourfic_report_failures ) ) ) . "\n";
exit( $tourfic_report_failures ? 1 : 0 );
