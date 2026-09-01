<?php
/**
 * Security checks for structured request and JSON sanitization.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/structured-input-sanitization.php
 */

namespace {
	if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
	}
	defined( 'ABSPATH' ) || exit;

	function sanitize_text_field( $value ) {
		return trim( preg_replace( '/<[^>]*>/', '', (string) $value ) );
	}

	function sanitize_key( $value ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) );
	}

	function absint( $value ) {
		return abs( (int) $value );
	}

	function wp_unslash( $value ) {
		if ( is_array( $value ) ) {
			return array_map( 'wp_unslash', $value );
		}

		return is_string( $value ) ? stripslashes( $value ) : $value;
	}

	function apply_filters( $hook, $value ) {
		if ( 'tourfic_availability_request_schema' === $hook ) {
			return $GLOBALS['tourfic_test_availability_schema'] ?? $value;
		}

		return $value;
	}

	function add_filter() {}
	function add_action() {}
}

namespace Tourfic\Traits {
	trait Singleton {}
	trait TF_Fonts {}
	trait Action_Helper {}
}

namespace Tourfic\Classes\Room {
	class Availability {}
	class Room {}
}

namespace {
	$root     = dirname( __DIR__, 2 );
	$pro_root = dirname( $root ) . '/tourfic-pro';

	require_once $root . '/inc/Classes/Helper.php';
	require_once $root . '/inc/Admin/TF_Options/TF_Options.php';
	require_once $pro_root . '/inc/classes/TF_Pro_Availability.php';

	function tf_structured_input_assert( $condition, $message ) {
		if ( ! $condition ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
			echo "FAIL: {$message}\n";
			exit( 1 );
		}
	}

	$recursive = \Tourfic\Classes\Helper::tf_sanitize_recursive_input(
		array(
			'Unsafe Key<script>' => '<b>safe</b><script>alert(1)</script>',
			'rows'               => array(
				2 => array( 'Title' => '<em>Package</em>' ),
			),
		)
	);
	tf_structured_input_assert( isset( $recursive['unsafekeyscript'] ), 'Recursive sanitizer must sanitize associative keys.' );
	tf_structured_input_assert( false === strpos( $recursive['unsafekeyscript'], '<script>' ), 'Recursive sanitizer must remove markup from scalar values.' );
	tf_structured_input_assert( isset( $recursive['rows'][2]['title'] ), 'Recursive sanitizer must preserve numeric indexes.' );

	$options_class = new \ReflectionClass( \Tourfic\Admin\TF_Options\TF_Options::class );
	$options       = $options_class->newInstanceWithoutConstructor();
	$decode        = $options_class->getMethod( 'tf_safe_json_decode_assoc' );
	$decode->setAccessible( true );
	$sanitize_request = $options_class->getMethod( 'tf_sanitize_availability_request_data' );
	$sanitize_request->setAccessible( true );

	$availability_json = json_encode(
		array(
			'2026/09/02 - 2026/09/03' => array(
				'check_in'             => '2026/09/02',
				'check_out'            => '2026/09/03',
				'pricing_type'         => 'package',
				'adult_price'          => '25.50',
				'status'               => 'available<script>',
				'tf_option_title_0'    => '<b>Standard</b><script>alert(1)</script>',
				'selected_packages'    => array( '0', '2' ),
				'allowed_time'         => array( 'time' => array( '<b>09:00 AM</b>' ) ),
				'undeclared_extension' => '<script>remove me</script>',
			),
			'not-a-date' => array(
				'check_in' => 'invalid',
			),
		)
	);
	$strict_rules      = $decode->invoke( $options, $availability_json, false );

	tf_structured_input_assert( 1 === count( $strict_rules ), 'Invalid availability rule keys must be rejected.' );
	$strict_rule = $strict_rules['2026/09/02 - 2026/09/03'];
	tf_structured_input_assert( ! isset( $strict_rule['undeclared_extension'] ), 'Request JSON must drop undeclared availability fields.' );
	tf_structured_input_assert( '25.50' === $strict_rule['adult_price'], 'Valid decimal availability prices must be retained.' );
	tf_structured_input_assert( 'available' === $strict_rule['status'], 'Availability status must be allowlisted.' );
	tf_structured_input_assert( false === strpos( $strict_rule['tf_option_title_0'], '<' ), 'Dynamic package titles must be sanitized.' );
	tf_structured_input_assert( '09:00 AM' === $strict_rule['allowed_time']['time'][0], 'Nested availability arrays must be sanitized.' );

	$stored_rules = $decode->invoke( $options, $availability_json, true );
	tf_structured_input_assert( isset( $stored_rules['2026/09/02 - 2026/09/03']['undeclared_extension'] ), 'Stored extension fields must remain compatible.' );
	tf_structured_input_assert(
		false === strpos( $stored_rules['2026/09/02 - 2026/09/03']['undeclared_extension'], '<script>' ),
		'Stored extension values must still be sanitized.'
	);

	$legacy_rules = array(
		array(
			'check_in'  => '2026/09/04',
			'check_out' => '2026/09/04',
			'status'    => 'available',
		),
		'2026/09/05_block' => array(
			'check_in'  => '2026/09/05',
			'check_out' => '2026/09/05',
			'status'    => 'unavailable',
			'source'    => 'ical',
		),
	);
	$stored_legacy_rules = $decode->invoke( $options, $legacy_rules, true );
	$posted_legacy_rules = $decode->invoke( $options, $legacy_rules, false );
	tf_structured_input_assert( isset( $stored_legacy_rules[0] ), 'Stored numeric availability keys must remain unchanged.' );
	tf_structured_input_assert( isset( $stored_legacy_rules['2026/09/05_block'] ), 'Legacy iCal block keys must remain compatible.' );
	tf_structured_input_assert( 'ical' === $stored_legacy_rules['2026/09/05_block']['source'], 'Stored iCal source metadata must be preserved.' );
	tf_structured_input_assert( isset( $posted_legacy_rules['2026/09/04'] ), 'Posted numeric rules must be normalized from their dates.' );

	$GLOBALS['tourfic_test_availability_schema'] = array(
		'fields' => array(
			'pricing_type'      => 'key',
			'options_count'      => 'absint',
			'selected_packages' => 'absint_array',
		),
		'patterns' => array(
			'/^tf_option_title_\d+$/' => 'text',
		),
	);
	$_POST = array(
		'pricing_type'       => 'PACKAGE<script>',
		'options_count'       => '3',
		'selected_packages'   => array( '0', '2' ),
		'tf_option_title_0'   => '<b>Standard</b>',
		'undeclared_attacker' => '<script>alert(1)</script>',
	);
	$request = $sanitize_request->invoke( $options, 'tour', array(), 123 );
	tf_structured_input_assert( ! isset( $request['undeclared_attacker'] ), 'Undeclared request fields must not reach availability filters.' );
	tf_structured_input_assert( 'packagescript' === $request['pricing_type'], 'Key fields must be normalized.' );
	tf_structured_input_assert( array( 0, 2 ) === $request['selected_packages'], 'Integer lists must retain valid indexes.' );
	tf_structured_input_assert( 'Standard' === $request['tf_option_title_0'], 'Pattern-declared text fields must be sanitized.' );

	$pro          = new \TF_Pro_Availability();
	$room_schema  = $pro->availability_request_schema( array(), 'room' );
	$tour_schema  = $pro->availability_request_schema( array(), 'tour' );
	tf_structured_input_assert( 'number' === $room_schema['fields']['tf_room_adult_price'], 'Pro must declare room extension prices.' );
	tf_structured_input_assert( 'text_array' === $tour_schema['fields']['allowed_time'], 'Pro must declare tour schedule arrays.' );
	tf_structured_input_assert(
		'package' === $pro->tour_rule_type( 'person', array(), 123, array( 'pricing_type' => 'package' ) ),
		'Pro rule types must consume the sanitized payload supplied by Free.'
	);

	$free_options = file_get_contents( $root . '/inc/Admin/TF_Options/TF_Options.php' );
	$builder      = file_get_contents( $root . '/inc/Traits/Action_Helper.php' );
	$settings     = file_get_contents( $root . '/inc/Admin/TF_Options/classes/TF_Settings.php' );
	tf_structured_input_assert( false === strpos( $free_options, "map_deep( wp_unslash( \$_POST ), 'sanitize_text_field' )" ), 'Free must not forward the complete POST body.' );
	tf_structured_input_assert( false === strpos( $builder, 'json_decode( sanitize_textarea_field' ), 'Builder JSON must be decoded before field sanitization.' );
	tf_structured_input_assert( false !== strpos( $builder, "array( 'ID', 'author', 'title', 'date'" ), 'Builder query ordering must be allowlisted.' );
	tf_structured_input_assert( false !== strpos( $settings, 'JSON_ERROR_NONE !== json_last_error()' ), 'Settings imports must reject malformed JSON.' );

	echo "PASS: structured input sanitization checks passed.\n";
}
