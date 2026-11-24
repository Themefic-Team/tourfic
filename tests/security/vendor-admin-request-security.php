<?php
/**
 * Exercise Vendor's actual admin AJAX callbacks with isolated in-memory services.
 * Run: php tests/security/vendor-admin-request-security.php [vendor-source-root]
 * No WordPress bootstrap, network, browser, or database connection is used.
 */

if ( 'cli' !== PHP_SAPI || defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$tourfic_vendor_source = $argv[1] ?? dirname( __DIR__, 3 ) . '/tourfic-vendor';
$tourfic_vendor_worker = isset( $argv[2] ) && '--worker' === $argv[2];

if ( $tourfic_vendor_worker ) {
	define( 'ABSPATH', __DIR__ . '/' );
	define( 'ARRAY_A', 'ARRAY_A' );
	define( 'MINUTE_IN_SECONDS', 60 );
	$fixture = json_decode( base64_decode( $argv[3] ), true );
	$fixture += array( 'reads' => array(), 'writes' => array(), 'prepared' => array(), 'status' => 200 );
	$fixture['post'] += array( 'tf_reg_nonce' => 'fixture:check_reg_nonce' );
	$_POST = $fixture['post'];
	$GLOBALS['wp_version'] = 'fixture';

	class Tourfic_Vendor_Fixture_Response extends RuntimeException {}
	class WP_Error {
		private $messages = array();
		public function add( $key, $message ) { $this->messages[] = $message; }
		public function get_error_messages() { return $this->messages; }
	}
	class WP_User {
		private $id;
		public function __construct( $id ) { $this->id = $id; }
		public function add_cap( $cap, $value = true ) {
			$GLOBALS['fixture']['writes'][] = array( 'capability', $this->id, $cap, $value );
		}
	}
	class Tourfic_Vendor_Fixture_DB {
		public $prefix = 'fixture_';
		public function prepare( $sql, ...$args ) {
			$args = isset( $args[0] ) && is_array( $args[0] ) ? $args[0] : $args;
			$GLOBALS['fixture']['prepared'][] = array( $sql, $args );
			return $sql;
		}
		public function get_row( $sql ) {
			$GLOBALS['fixture']['reads'][] = array( 'row', $sql );
			if ( false !== strpos( $sql, 'tf_vendor_withdraw' ) ) {
				return (object) array( 'id' => 9, 'vendor_id' => 7, 'amount' => 25,
					'udate' => '2026-11-24', 'rdate' => '2026-11-25', 'payment_method' => 'paypal',
					'note' => 'Fixture payment', 'wstatus' => 'pending' );
			}
			return (object) array( 'total_amount' => 100, 'total_withdraw' => 0,
				'pending_amount' => 25, 'withdrawable_amount' => 75 );
		}
		public function get_results( $sql, $format = null ) {
			$GLOBALS['fixture']['reads'][] = array( 'results', $sql );
			return array();
		}
		public function query( $sql ) {
			$GLOBALS['fixture']['writes'][] = array( 'query', $sql );
			return 1;
		}
	}
	$wpdb = new Tourfic_Vendor_Fixture_DB();

	function add_action( ...$args ) {}
	function add_filter( ...$args ) {}
	function wp_next_scheduled( $action ) { return true; }
	function __( $value, $domain = '' ) { return $value; }
	function esc_html__( $value, $domain = '' ) { return esc_html( $value ); }
	function esc_html_e( $value, $domain = '' ) { echo esc_html( $value ); }
	function esc_attr_e( $value, $domain = '' ) { echo esc_attr( $value ); }
	function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
	function esc_attr( $value ) { return esc_html( $value ); }
	function wp_unslash( $value ) {
		return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( $value );
	}
	function sanitize_text_field( $value ) {
		return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : '';
	}
	function sanitize_email( $value ) { return filter_var( $value, FILTER_SANITIZE_EMAIL ); }
	function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ); }
	function sanitize_title( $value ) { return sanitize_key( $value ); }
	function is_user_logged_in() { return 'guest' !== $GLOBALS['fixture']['role']; }
	function current_user_can( $cap, ...$args ) {
		return 'administrator' === $GLOBALS['fixture']['role'];
	}
	function wp_verify_nonce( $nonce, $action ) {
		return is_string( $nonce ) && hash_equals( 'fixture:' . $action, $nonce );
	}
	function wp_send_json_error( $data, $status = 200 ) {
		$GLOBALS['fixture']['status'] = $status;
		$GLOBALS['fixture']['error'] = $data;
		throw new Tourfic_Vendor_Fixture_Response();
	}
	function wp_json_encode( $value ) { return json_encode( $value ); }
	function get_userdata( $id ) {
		$GLOBALS['fixture']['reads'][] = array( 'user', $id );
		if ( 7 === (int) $id ) {
			return (object) array( 'ID' => 7, 'roles' => array( 'tf_vendor' ), 'user_login' => 'fixture-vendor' );
		}
		if ( 1 === (int) $id ) {
			return (object) array( 'ID' => 1, 'roles' => array( 'administrator' ) );
		}
		return false;
	}
	function get_posts( $args ) {
		$GLOBALS['fixture']['reads'][] = array( 'posts', $args );
		return array();
	}
	function get_users( $args ) { return array( get_userdata( 7 ) ); }
	function update_user_meta( $id, $key, $value ) {
		$GLOBALS['fixture']['writes'][] = array( 'meta', $id, $key, $value );
	}
	function get_user_meta( ...$args ) { return ''; }
	function wp_update_post( $post ) { $GLOBALS['fixture']['writes'][] = array( 'post', $post ); }
	function wp_insert_user( $data ) {
		$GLOBALS['fixture']['writes'][] = array( 'create-user', $data );
		return 7;
	}
	function wp_update_user( $data ) {
		$GLOBALS['fixture']['writes'][] = array( 'update-user', $data );
		return (int) $data['ID'];
	}
	function wp_new_user_notification( ...$args ) {}
	function username_exists( $value ) { return false; }
	function email_exists( $value ) { return false; }
	function validate_username( $value ) { return true; }
	function is_email( $value ) { return false !== filter_var( $value, FILTER_VALIDATE_EMAIL ); }
	function is_wp_error( $value ) { return $value instanceof WP_Error; }
	function tfopt( $key ) {
		if ( 'log_reg_settings' === $key && ! empty( $GLOBALS['fixture']['custom_fields'] ) ) {
			return array( 'vendor-registration' => $GLOBALS['fixture']['custom_fields'] );
		}
		return array();
	}
	function tourfic_data_types( $value ) { return $value; }
	function home_url( $path ) { return 'https://example.invalid' . $path; }

	ob_start();
	register_shutdown_function( function() {
		$GLOBALS['fixture']['output'] = ob_get_clean();
		echo json_encode( $GLOBALS['fixture'] );
	} );
	set_error_handler( function( $severity, $message, $file, $line ) {
		throw new ErrorException( $message, 0, $severity, $file, $line );
	} );
	try {
		require $tourfic_vendor_source . '/functions/tf-vendor-accounts.php';
		call_user_func( $fixture['callback'] );
	} catch ( Tourfic_Vendor_Fixture_Response $response ) {
		// The real callback's JSON exit is represented without a web server.
	} catch ( Throwable $error ) {
		$fixture['exception'] = $error->getMessage();
	}
	exit;
}

$tourfic_vendor_callbacks = array(
	'tf_vendor_bulk' => 'tf_vendor_bulk_filter_callback',
	'tf_vendor_activation' => 'tf_vendor_activation_filter_callback',
	'tf_vendor_registration' => 'tf_vendor_registration_callback',
	'tf_vendor_update' => 'tf_vendor_update_callback',
	'tf_vendor_payouts' => 'tf_vendor_payouts_callback',
	'tf_vendor_payouts_amount_preview' => 'tf_vendor_payouts_amount_preview_callback',
	'tf_vendor_payouts_decline' => 'tf_vendor_payouts_decline_callback',
	'tf_vendor_month_reports' => 'tf_vendor_month_reports_callback',
	'tf_vendor_payouts_create' => 'tf_vendor_payouts_callback_create',
	'tf_vendor_payouts_update' => 'tf_vendor_payouts_update_callback',
	'tf_vendor_payouts_info_update' => 'tf_vendor_payouts_info_update_callback',
);
$tourfic_vendor_valid_post = array(
	'balkaction' => 'approved', 'vendorlist' => array( array( 'name' => 'vendor_id', 'value' => '7' ) ),
	'status' => 'enabled', 'vendorid' => '7', 'tf_vendor_id' => '7',
	'tf_username' => 'fixture-vendor', 'tf_user_email' => 'fixture@example.invalid',
	'tf_user_password' => 'fixture-password', 'tf_user_commission' => '15',
	'withdraw_id' => '9', 'vendor_id' => '7', 'month' => '11', 'chartval' => array( '1', '2', '3' ),
	'vendor' => '7', 'amount' => '25', 'payment_method' => 'Paypal',
	'payment_note' => "Fixture O\\'Brien", 'payment_date' => '2026-11-24',
	'payment_release_date' => '2026-11-25', 'payment_id' => '9',
);
$tourfic_vendor_checks = 0;
$tourfic_vendor_failures = array();

function tourfic_vendor_fixture_expect( $condition, $label ) {
	++$GLOBALS['tourfic_vendor_checks'];
	if ( ! $condition ) {
		$GLOBALS['tourfic_vendor_failures'][] = $label;
	}
}

function tourfic_vendor_fixture_call( $action, $role = 'administrator', $changes = array(), $nonce = 'valid', $extra = array() ) {
	$post = array_replace( $GLOBALS['tourfic_vendor_valid_post'], $changes );
	if ( 'missing' !== $nonce ) {
		$post['nonce'] = 'valid' === $nonce ? 'fixture:tourfic_vendor_admin_' . $action : $nonce;
	}
	$job = array_merge( array( 'callback' => $GLOBALS['tourfic_vendor_callbacks'][ $action ], 'role' => $role, 'post' => $post ), $extra );
	$process = proc_open(
		array( PHP_BINARY, __FILE__, $GLOBALS['tourfic_vendor_source'], '--worker', base64_encode( json_encode( $job ) ) ),
		array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ),
		$pipes
	);
	fclose( $pipes[0] );
	$output = stream_get_contents( $pipes[1] );
	$error = stream_get_contents( $pipes[2] );
	fclose( $pipes[1] );
	fclose( $pipes[2] );
	proc_close( $process );
	$result = json_decode( $output, true );
	if ( ! is_array( $result ) ) {
		return array( 'status' => 500, 'reads' => array(), 'writes' => array(), 'exception' => $output . $error );
	}
	return $result;
}

foreach ( $tourfic_vendor_callbacks as $tourfic_action => $tourfic_callback ) {
	foreach ( array( 'guest', 'subscriber', 'tf_vendor' ) as $tourfic_role ) {
		$result = tourfic_vendor_fixture_call( $tourfic_action, $tourfic_role );
		tourfic_vendor_fixture_expect( 403 === $result['status'] && empty( $result['exception'] ), $tourfic_action . ': denies ' . $tourfic_role );
		tourfic_vendor_fixture_expect( empty( $result['reads'] ) && empty( $result['writes'] ), $tourfic_action . ': role denial precedes reads/writes' );
	}
	foreach ( array( 'missing', 'ordinary-invalid-nonce', 'fixture:tourfic_vendor_admin_other_action', array( 'ordinary-invalid-nonce' ) ) as $tourfic_nonce ) {
		$result = tourfic_vendor_fixture_call( $tourfic_action, 'administrator', array(), $tourfic_nonce );
		tourfic_vendor_fixture_expect( 403 === $result['status'] && empty( $result['exception'] ), $tourfic_action . ': rejects missing/malformed/wrong-action nonce' );
		tourfic_vendor_fixture_expect( empty( $result['reads'] ) && empty( $result['writes'] ), $tourfic_action . ': nonce denial precedes reads/writes' );
	}
	$result = tourfic_vendor_fixture_call( $tourfic_action );
	tourfic_vendor_fixture_expect( 200 === $result['status'] && empty( $result['exception'] ), $tourfic_action . ': authorized request works ' . ( $result['exception'] ?? '' ) );
	tourfic_vendor_fixture_expect( ! empty( $result['reads'] ) || ! empty( $result['writes'] ), $tourfic_action . ': authorized path reaches its service' );
}

$tourfic_vendor_id_fields = array(
	'tf_vendor_activation' => 'vendorid', 'tf_vendor_update' => 'tf_vendor_id',
	'tf_vendor_payouts' => 'withdraw_id', 'tf_vendor_payouts_amount_preview' => 'vendor_id',
	'tf_vendor_payouts_decline' => 'withdraw_id', 'tf_vendor_payouts_create' => 'vendor',
	'tf_vendor_payouts_update' => 'withdraw_id', 'tf_vendor_payouts_info_update' => 'payment_id',
);
foreach ( $tourfic_vendor_id_fields as $tourfic_action => $tourfic_field ) {
	foreach ( array( '0', '-1', '1.5', '7x', '07', '999999999999999999999', array( '7' ) ) as $tourfic_invalid ) {
		$result = tourfic_vendor_fixture_call( $tourfic_action, 'administrator', array( $tourfic_field => $tourfic_invalid ) );
		tourfic_vendor_fixture_expect( 400 === $result['status'] && empty( $result['writes'] ) && empty( $result['exception'] ), $tourfic_action . ': malformed identifier rejected without writes' );
	}
}

$tourfic_vendor_invalid_cases = array(
	array( 'tf_vendor_bulk', array( 'balkaction' => 'ordinary-invalid-action' ) ),
	array( 'tf_vendor_bulk', array( 'vendorlist' => 'ordinary-invalid-selection' ) ),
	array( 'tf_vendor_bulk', array( 'vendorlist' => array( array( 'value' => '7' ), array( 'value' => '0' ) ) ) ),
	array( 'tf_vendor_activation', array( 'status' => 'ordinary-invalid-status' ) ),
	array( 'tf_vendor_activation', array( 'vendorid' => '1' ) ),
	array( 'tf_vendor_activation', array( 'vendorid' => '404' ) ),
	array( 'tf_vendor_update', array( 'tf_vendor_id' => '1' ) ),
	array( 'tf_vendor_registration', array( 'tf_username' => array( 'fixture-vendor' ) ) ),
	array( 'tf_vendor_update', array( 'tf_user_email' => array( 'fixture@example.invalid' ) ) ),
	array( 'tf_vendor_registration', array( 'tf_vendor_enabled' => 'ordinary-invalid-status' ) ),
	array( 'tf_vendor_update', array( 'tf_vendor_posts' => 'ordinary-invalid-status' ) ),
	array( 'tf_vendor_update', array( 'tf_vendor_featured_permission' => 'ordinary-invalid-status' ) ),
	array( 'tf_vendor_month_reports', array( 'month' => '13' ) ),
	array( 'tf_vendor_month_reports', array( 'chartval' => '1' ) ),
	array( 'tf_vendor_month_reports', array( 'chartval' => array( '4' ) ) ),
);
foreach ( array( 'tf_vendor_payouts_create', 'tf_vendor_payouts_info_update' ) as $tourfic_action ) {
	foreach ( array( '0', '-5', 'ordinary-invalid-amount', '1e999', array( '25' ) ) as $tourfic_amount ) {
		$tourfic_vendor_invalid_cases[] = array( $tourfic_action, array( 'amount' => $tourfic_amount ) );
	}
	foreach ( array( '2026-02-30', '0000-01-01', "2026-11-24\0", array( '2026-11-24' ) ) as $tourfic_date ) {
		$tourfic_vendor_invalid_cases[] = array( $tourfic_action, array( 'payment_date' => $tourfic_date ) );
	}
	$tourfic_vendor_invalid_cases[] = array( $tourfic_action, array( 'payment_method' => 'ordinary-invalid-method' ) );
	$tourfic_vendor_invalid_cases[] = array( $tourfic_action, array( 'payment_note' => array( 'ordinary-note' ) ) );
}
foreach ( $tourfic_vendor_invalid_cases as $tourfic_case ) {
	$result = tourfic_vendor_fixture_call( $tourfic_case[0], 'administrator', $tourfic_case[1] );
	tourfic_vendor_fixture_expect( 400 === $result['status'] && empty( $result['writes'] ) && empty( $result['exception'] ), $tourfic_case[0] . ': invalid structured field rejected without writes' );
}

$result = tourfic_vendor_fixture_call( 'tf_vendor_payouts_amount_preview' );
tourfic_vendor_fixture_expect( array( 'vendor_amount' => 100 ) === json_decode( $result['output'], true ), 'Amount preview retains its JSON envelope.' );
$result = tourfic_vendor_fixture_call( 'tf_vendor_payouts_update' );
tourfic_vendor_fixture_expect( false !== strpos( $result['output'], 'tf-payment-id' ), 'Payout edit retains its HTML form.' );
$result = tourfic_vendor_fixture_call( 'tf_vendor_payouts_create' );
$args = $result['prepared'][0][1] ?? array();
tourfic_vendor_fixture_expect( array( 7, 25, 'paypal', "Fixture O'Brien", 'pending', '2026-11-24', '2026-11-25' ) == $args, 'Create passes normalized known fields to prepared SQL, with one unslash.' );

foreach ( array( 'Paypal', 'Payoneer', 'Wise', 'Bank', 'Others' ) as $tourfic_method ) {
	foreach ( array( 'tf_vendor_payouts_create', 'tf_vendor_payouts_info_update' ) as $tourfic_action ) {
		$result = tourfic_vendor_fixture_call( $tourfic_action, 'administrator', array( 'payment_method' => $tourfic_method ) );
		$args = $result['prepared'][0][1] ?? array();
		tourfic_vendor_fixture_expect( 200 === $result['status'] && empty( $result['exception'] ) && strtolower( $tourfic_method ) === ( $args[2] ?? '' ), $tourfic_action . ': accepts payment method ' . $tourfic_method );
	}
}
foreach ( array( 'tf_vendor_registration', 'tf_vendor_update' ) as $tourfic_action ) {
	$result = tourfic_vendor_fixture_call( $tourfic_action, 'administrator', array( 'tf_vendor_enabled' => 'on', 'tf_vendor_posts' => 'on', 'tf_vendor_featured_permission' => 'on' ) );
	tourfic_vendor_fixture_expect( 200 === $result['status'] && empty( $result['exception'] ) && false !== strpos( $result['output'], 'tf_success' ), $tourfic_action . ': checked switches preserve HTML success response' );
}
$result = tourfic_vendor_fixture_call( 'tf_vendor_activation', 'administrator', array( 'status' => 'disabled' ) );
tourfic_vendor_fixture_expect( 200 === $result['status'] && empty( $result['exception'] ) && ! empty( $result['writes'] ), 'Vendor deactivation remains available to administrators.' );

foreach ( $tourfic_vendor_failures as $tourfic_failure ) {
	echo 'FAIL: ' . $tourfic_failure . "\n";
}
echo json_encode( array( 'checks' => $tourfic_vendor_checks, 'failures' => count( $tourfic_vendor_failures ) ) ) . "\n";
exit( $tourfic_vendor_failures ? 1 : 0 );
