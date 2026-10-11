<?php
/**
 * Regression coverage for automatic Tourfic Pro booking-email delivery.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/pro-booking-email-idempotency.php
 */

namespace Tourfic\Admin\Emails {
	class TF_Handle_Emails {
		public static function instance() {
			return new self();
		}

		public static function get_email_template( $template, $content, $recipient ) {
			return $template . '-' . $recipient;
		}

		public function replace_mail_tags( $content, $order_id, $context ) {
			return $content . '-' . $order_id . '-' . ( $context['recipient'] ?? 'default' );
		}

		public function offline_replace_mail_tags( $content, $order_id, $order_data, $context ) {
			return $content . '-' . $order_id . '-' . ( $context['recipient'] ?? 'default' );
		}

		public function email_body_open( $logo, $heading, $background ) {
			return '<body>{booking_id}';
		}

		public function email_body_close() {
			return '</body>';
		}

		public function tf_get_vendor_recipients( $order_id ) {
			return array( 42 => 'vendor@example.test' );
		}
	}
}

namespace Tourfic\Classes {
	class Helper {
		public static function tfopt( $key ) {
			return $GLOBALS['tourfic_test_email_settings'][ $key ] ?? array();
		}
	}
}

namespace {
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
	}
	if ( ! defined( 'ARRAY_A' ) ) {
		define( 'ARRAY_A', 'ARRAY_A' );
	}

	$tourfic_test_actions        = array();
	$tourfic_test_booking_row    = array();
	$tourfic_test_email_settings = array();
	$tourfic_test_mail_results   = array();
	$tourfic_test_messages       = array();
	$tourfic_test_options        = array();
	$tourfic_test_orders         = array();
	$tourfic_test_uuid_counter   = 0;
	$tourfic_test_admin_emails   = 'admin@example.test';
	$tourfic_test_order_meta     = array();

	function tourfic_pro_email_assert( $condition, $message ) {
		if ( ! $condition ) {
			echo "FAIL: {$message}\n";
			exit( 1 );
		}
	}

	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		global $tourfic_test_actions;
		$tourfic_test_actions[ $hook ] = array( $callback, $priority, $accepted_args );
	}

	function do_action( $hook, ...$args ) {
		$action = $GLOBALS['tourfic_test_actions'][ $hook ] ?? false;
		if ( $action ) {
			call_user_func_array( $action[0], array_slice( $args, 0, $action[2] ) );
		}
	}

	function apply_filters( $hook, $value, ...$args ) {
		return $value;
	}

	function wc_get_order( $order_id ) {
		global $tourfic_test_orders;
		return $tourfic_test_orders[ $order_id ] ?? false;
	}

	function absint( $value ) {
		return abs( (int) $value );
	}

	function sanitize_key( $value ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) );
	}

	function sanitize_text_field( $value ) {
		return trim( (string) $value );
	}

	function sanitize_email( $value ) {
		$value = strtolower( trim( (string) $value ) );
		return filter_var( $value, FILTER_VALIDATE_EMAIL ) ? $value : '';
	}

	function wp_generate_uuid4() {
		global $tourfic_test_uuid_counter;
		++$tourfic_test_uuid_counter;
		return '00000000-0000-4000-8000-' . str_pad( (string) $tourfic_test_uuid_counter, 12, '0', STR_PAD_LEFT );
	}

	function add_option( $name, $value, $deprecated = '', $autoload = null ) {
		global $tourfic_test_options;
		if ( array_key_exists( $name, $tourfic_test_options ) ) {
			return false;
		}
		$tourfic_test_options[ $name ] = $value;
		return true;
	}

	function get_option( $name, $default = false ) {
		global $tourfic_test_options;
		return array_key_exists( $name, $tourfic_test_options ) ? $tourfic_test_options[ $name ] : $default;
	}

	function update_option( $name, $value, $autoload = null ) {
		global $tourfic_test_options;
		$tourfic_test_options[ $name ] = $value;
		return true;
	}

	function delete_option( $name ) {
		global $tourfic_test_options;
		unset( $tourfic_test_options[ $name ] );
		return true;
	}

	function get_post( $post_id ) {
		return $post_id ? (object) array( 'post_content' => 'Template ' . $post_id ) : false;
	}

	function get_post_meta( $post_id, $key, $single = false ) {
		global $tourfic_test_admin_emails;
		return array(
			'sale_notification_email' => $tourfic_test_admin_emails,
			'email_subject'            => 'Booking {booking_id}',
			'email_from_name'          => 'Tourfic',
			'email_from_email'         => 'sender@example.test',
		);
	}

	function get_bloginfo( $key ) {
		return 'admin_email' === $key ? 'admin@example.test' : 'Tourfic';
	}

	function __( $text, $domain = 'default' ) {
		return $text;
	}

	function wp_kses_post( $content ) {
		return $content;
	}

	function wp_mail( $email, $subject, $message, $headers = '', $attachments = array() ) {
		global $tourfic_test_mail_results, $tourfic_test_messages;
		$tourfic_test_messages[] = $email;
		return $tourfic_test_mail_results ? (bool) array_shift( $tourfic_test_mail_results ) : true;
	}

	function current_user_can( $capability, ...$args ) {
		return true;
	}

	function get_post_field( $field, $post_id ) {
		return 42;
	}

	function wp_get_current_user() {
		return (object) array( 'roles' => array( 'administrator' ) );
	}

	function get_userdata( $user_id ) {
		return (object) array(
			'roles'      => array( 'tf_vendor' ),
			'user_email' => 'vendor@example.test',
		);
	}

	class Tourfic_Test_Order_Item {
		private $order_type;

		public function __construct( $order_type ) {
			$this->order_type = $order_type;
		}

		public function get_meta( $key, $single = true ) {
			return '_order_type' === $key ? $this->order_type : '';
		}
	}

	class Tourfic_Test_Order {
		private $order_id;
		private $meta = array();
		public $status = 'processing';
		public $order_type = 'tour';

		public function __construct( $order_id ) {
			$this->order_id = $order_id;
		}

		public function get_id() {
			return $this->order_id;
		}

		public function get_items() {
			return array( new Tourfic_Test_Order_Item( $this->order_type ) );
		}

		public function is_paid() {
			return in_array( $this->status, array( 'processing', 'completed' ), true );
		}

		public function get_billing_email() {
			return 'customer@example.test';
		}

		public function get_meta( $key, $single = true ) {
			return $this->meta[ $key ] ?? $GLOBALS['tourfic_test_order_meta'][ $this->order_id ][ $key ] ?? '';
		}

		public function update_meta_data( $key, $value ) {
			$this->meta[ $key ] = $value;
		}

		public function save_meta_data() {
			$GLOBALS['tourfic_test_order_meta'][ $this->order_id ] = array_replace(
				$GLOBALS['tourfic_test_order_meta'][ $this->order_id ] ?? array(),
				$this->meta
			);
			$this->meta = array();
			return true;
		}
	}

	class Tourfic_Test_WPDB {
		public $prefix = 'wp_';

		public function prepare( $query, ...$args ) {
			return $query;
		}

		public function get_row( $query, $output = OBJECT ) {
			global $tourfic_test_booking_row;
			return $tourfic_test_booking_row;
		}
	}

	$wpdb = new Tourfic_Test_WPDB();

	function tourfic_test_email_settings( $enabled = array( 'admin', 'vendor', 'customer' ) ) {
		$settings = array(
			'admin_confirmation_email_template'           => 11,
			'vendor_confirmation_email_template'          => 12,
			'customer_confirmation_email_template'        => 13,
			'admin_cancellation_email_template'            => 14,
			'vendor_cancellation_email_template'           => 15,
			'customer_cancellation_email_template'         => 16,
			'admin_offline_confirmation_email_template'   => 17,
			'vendor_offline_confirmation_email_template'  => 18,
			'customer_offline_confirmation_email_template'=> 19,
		);
		foreach ( array( 'admin', 'vendor', 'customer' ) as $recipient ) {
			$settings[ 'enable_' . $recipient . '_conf_email' ]         = in_array( $recipient, $enabled, true );
			$settings[ 'enable_' . $recipient . '_canc_email' ]         = in_array( $recipient, $enabled, true );
			$settings[ 'enable_offline_' . $recipient . '_conf_email' ] = in_array( $recipient, $enabled, true );
		}

		return array( 'email_template_settings' => $settings );
	}

	$tourfic_test_email_settings = tourfic_test_email_settings();
	$tourfic_test_orders[1001]   = new Tourfic_Test_Order( 1001 );
	$tourfic_test_orders[2002]   = new Tourfic_Test_Order( 2002 );
	$tourfic_test_orders[3003]   = new Tourfic_Test_Order( 3003 );
	$tourfic_test_orders[4004]   = new Tourfic_Test_Order( 4004 );
	$tourfic_test_orders[5005]   = new Tourfic_Test_Order( 5005 );

	$pro_root = dirname( dirname( __DIR__, 2 ) ) . '/tourfic-pro';
	require_once $pro_root . '/inc/classes/Advanced_Email_Handler.php';

	$handler = \TourficPro\Emails\Advanced_Email_Handler::instance();

	if ( '--persisted-delivery' === ( $argv[1] ?? '' ) ) {
		$tourfic_test_order_meta = json_decode( base64_decode( $argv[2] ), true );
		do_action( 'woocommerce_thankyou', 1001 );
		do_action( 'woocommerce_thankyou', 1001 );
		echo json_encode( array( 'mail_calls' => count( $tourfic_test_messages ) ) );
		exit;
	}

	tourfic_pro_email_assert(
		isset( $tourfic_test_actions['woocommerce_thankyou'] )
		&& ! isset( $tourfic_test_actions['woocommerce_order_status_changed'] )
		&& ! isset( $tourfic_test_actions['woocommerce_payment_complete'] )
		&& ! isset( $tourfic_test_actions['tourfic_pro_retry_booking_confirmation'] ),
		'Duplicate prevention must preserve the existing confirmation timing without a new paid-only or cron policy.'
	);

	for ( $render = 0; $render < 3; ++$render ) {
		$tourfic_test_orders[1001] = new Tourfic_Test_Order( 1001 );
		do_action( 'woocommerce_thankyou', 1001 );
		do_action( 'woocommerce_thankyou', 1001 );
	}
	tourfic_pro_email_assert(
		3 === count( $tourfic_test_messages ),
		'Repeated renders and duplicate hooks must send each recipient once, using markers persisted across order objects.'
	);

	// phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- CLI fixture starts only itself with PHP_BINARY and no shell.
	$process = proc_open(
		array( PHP_BINARY, __FILE__, '--persisted-delivery', base64_encode( json_encode( $tourfic_test_order_meta ) ) ),
		array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ),
		$pipes
	);
	tourfic_pro_email_assert( is_resource( $process ), 'The fresh-process persistence check must start.' );
	$output = stream_get_contents( $pipes[1] );
	$error  = stream_get_contents( $pipes[2] );
	fclose( $pipes[1] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Close a process pipe.
	fclose( $pipes[2] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Close a process pipe.
	tourfic_pro_email_assert(
		0 === proc_close( $process ) && array( 'mail_calls' => 0 ) === json_decode( $output, true ),
		'Saved WooCommerce delivery metadata must prevent sends in a fresh PHP process. ' . $error
	);

	$handler->send_cancellation( 1001 );
	$handler->send_cancellation( 1001 );
	tourfic_pro_email_assert(
		6 === count( $tourfic_test_messages ),
		'Cancellation delivery must be independent from confirmation and remain idempotent.'
	);

	$offline_data = array(
		'post_id'          => 501,
		'shipping_details' => array( 'tf_email' => 'offline@example.test' ),
	);
	$handler->send_offline_confirmation( 80000001, $offline_data );
	$handler->send_offline_confirmation( 80000001, $offline_data );
	tourfic_pro_email_assert(
		9 === count( $tourfic_test_messages ),
		'Repeated offline confirmation hooks must not resend the same automatic messages.'
	);

	$tourfic_test_email_settings = tourfic_test_email_settings( array( 'customer' ) );
	$tourfic_test_mail_results   = array( false, true );
	$handler->send_confirmation( 2002 );
	$handler->send_confirmation( 2002 );
	tourfic_pro_email_assert(
		11 === count( $tourfic_test_messages ),
		'A transport failure must release its claim so the failed recipient can be retried.'
	);

	$tourfic_test_booking_row = array(
		'id'             => 77,
		'order_id'       => 1001,
		'post_id'        => 501,
		'payment_method' => 'woocommerce',
	);
	$tourfic_test_orders[1001]->status = 'pending';
	$handler->resend_email( 'customer', 1001, 77 );
	$handler->resend_email( 'customer', 1001, 77 );
	tourfic_pro_email_assert(
		13 === count( $tourfic_test_messages ),
		'Authorized manual resends must intentionally bypass automatic-delivery markers.'
	);
	$tourfic_test_orders[1001]->status = 'processing';

	$tourfic_test_email_settings = tourfic_test_email_settings( array( 'admin' ) );
	$tourfic_test_admin_emails   = 'admin@example.test, admin@example.test';
	$handler->send_confirmation( 3003 );
	tourfic_pro_email_assert(
		14 === count( $tourfic_test_messages ),
		'Duplicate configured admin addresses must produce only one message.'
	);

	$tourfic_test_email_settings = tourfic_test_email_settings( array( 'customer' ) );
	$stale_option_name           = \TourficPro\Emails\Advanced_Email_Handler::DELIVERY_CLAIM_OPTION_PREFIX
		. hash( 'sha256', 'woocommerce|4004|confirmation|customer|address|customer@example.test' );
	$tourfic_test_options[ $stale_option_name ] = array(
		'state'      => 'sending',
		'token'      => 'interrupted-request',
		'claimed_at' => time() - \TourficPro\Emails\Advanced_Email_Handler::DELIVERY_CLAIM_TTL - 1,
	);
	$handler->send_confirmation( 4004 );
	$handler->send_confirmation( 4004 );
	tourfic_pro_email_assert(
		15 === count( $tourfic_test_messages ) && ! isset( $tourfic_test_options[ $stale_option_name ] ),
		'An interrupted automatic delivery must become retryable after the bounded claim timeout.'
	);

	$active_option_name = \TourficPro\Emails\Advanced_Email_Handler::DELIVERY_CLAIM_OPTION_PREFIX
		. hash( 'sha256', 'woocommerce|5005|confirmation|customer|address|customer@example.test' );
	$tourfic_test_options[ $active_option_name ] = array(
		'state'      => 'sending',
		'token'      => 'active-request',
		'claimed_at' => time(),
	);
	$handler->send_confirmation( 5005 );
	tourfic_pro_email_assert(
		15 === count( $tourfic_test_messages ),
		'An active delivery claim must prevent a concurrent request from sending the same message.'
	);

	$tourfic_test_email_settings = tourfic_test_email_settings( array( 'vendor', 'customer' ) );
	foreach ( array( 'pending', 'on-hold', 'processing', 'completed' ) as $index => $status ) {
		$order_id = 6006 + $index;
		$before   = count( $tourfic_test_messages );
		for ( $render = 0; $render < 3; ++$render ) {
			$tourfic_test_orders[ $order_id ] = new Tourfic_Test_Order( $order_id );
			$tourfic_test_orders[ $order_id ]->status = $status;
			do_action( 'woocommerce_thankyou', $order_id );
			do_action( 'woocommerce_thankyou', $order_id );
		}
		tourfic_pro_email_assert(
			array( 'vendor@example.test', 'customer@example.test' ) === array_slice( $tourfic_test_messages, $before ),
			'Existing ' . $status . ' booking confirmations must remain eligible but must not repeat on refresh.'
		);
	}

	$tourfic_test_orders[7007] = new Tourfic_Test_Order( 7007 );
	$tourfic_test_mail_results = array( true, false, true );
	$before = count( $tourfic_test_messages );
	do_action( 'woocommerce_thankyou', 7007 );
	$tourfic_test_orders[7007] = new Tourfic_Test_Order( 7007 );
	do_action( 'woocommerce_thankyou', 7007 );
	do_action( 'woocommerce_thankyou', 7007 );
	tourfic_pro_email_assert(
		array( 'vendor@example.test', 'customer@example.test', 'customer@example.test' )
			=== array_slice( $tourfic_test_messages, $before ),
		'A failed customer send must remain retryable without resending the successful vendor copy.'
	);

	$tourfic_test_orders[8008] = new Tourfic_Test_Order( 8008 );
	$tourfic_test_email_settings = tourfic_test_email_settings( array() );
	$before = count( $tourfic_test_messages );
	do_action( 'woocommerce_thankyou', 8008 );
	tourfic_pro_email_assert( $before === count( $tourfic_test_messages ), 'Disabled confirmation settings must remain honored.' );

	$tourfic_test_email_settings = tourfic_test_email_settings();
	$tourfic_test_orders[9009] = new Tourfic_Test_Order( 9009 );
	$tourfic_test_orders[9009]->order_type = '';
	do_action( 'woocommerce_thankyou', 9009 );
	do_action( 'woocommerce_thankyou', 0 );
	tourfic_pro_email_assert( $before === count( $tourfic_test_messages ), 'Missing and non-Tourfic orders must not send Tourfic mail.' );

	echo "Tourfic Pro booking-email idempotency regression checks passed.\n";
}
