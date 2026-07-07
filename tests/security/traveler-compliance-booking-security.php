<?php
/**
 * Static regression checks for tour traveler compliance booking flow.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/traveler-compliance-booking-security.php
 */

$root = dirname( __DIR__, 2 );

$files = array(
	'tour_booking' => $root . '/inc/functions/woocommerce/wc-tour.php',
	'functions'    => $root . '/inc/functions.php',
	'tour_js'      => $root . '/sass/app/js/free/tour.js',
	'tourfic_js'   => $root . '/sass/app/js/free/tourfic.js',
);

foreach ( $files as $label => $file ) {
	if ( ! is_readable( $file ) ) {
		fwrite( STDERR, "Missing fixture {$label}: {$file}\n" );
		exit( 1 );
	}
}

function tf_traveler_compliance_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tf_traveler_compliance_file( $file ) {
	return file_get_contents( $file );
}

function tf_traveler_compliance_function_body( $source, $function ) {
	$offset = strpos( $source, 'function ' . $function );
	tf_traveler_compliance_assert( false !== $offset, "Function {$function} not found." );

	$brace = strpos( $source, '{', $offset );
	tf_traveler_compliance_assert( false !== $brace, "Function {$function} has no body." );

	$depth  = 0;
	$length = strlen( $source );
	for ( $i = $brace; $i < $length; $i++ ) {
		if ( '{' === $source[ $i ] ) {
			$depth++;
		} elseif ( '}' === $source[ $i ] ) {
			$depth--;
			if ( 0 === $depth ) {
				return substr( $source, $brace, $i - $brace + 1 );
			}
		}
	}

	tf_traveler_compliance_assert( false, "Function {$function} body is not balanced." );
}

$tour_booking_source = tf_traveler_compliance_file( $files['tour_booking'] );
$functions_source    = tf_traveler_compliance_file( $files['functions'] );
$tour_js_source      = tf_traveler_compliance_file( $files['tour_js'] );
$tourfic_js_source   = tf_traveler_compliance_file( $files['tourfic_js'] );

$tour_booking_body = tf_traveler_compliance_function_body( $tour_booking_source, 'tf_tours_booking_function' );

tf_traveler_compliance_assert(
	false !== strpos( $tour_booking_body, 'wp_send_json(' ),
	'Tour booking nonce failure must return structured JSON.'
);
tf_traveler_compliance_assert(
	false !== strpos( $tour_booking_body, "'without_payment' => 'false'" ),
	'Tour booking nonce failure must preserve the existing without_payment error envelope.'
);
tf_traveler_compliance_assert(
	false !== strpos( $tour_booking_body, 'Your booking session has expired. Please refresh the page and try again.' ),
	'Tour booking nonce failure must tell the user how to recover.'
);

tf_traveler_compliance_assert(
	false !== strpos( $tour_js_source, "\$this.find('.tour-extra-single')" ),
	'Tour submit must collect extras from the submitted form only.'
);
tf_traveler_compliance_assert(
	false === strpos( $tour_js_source, "jQuery('.tour-extra-single" ),
	'Tour submit must not read extras through a global selector.'
);
tf_traveler_compliance_assert(
	false !== strpos( $tour_js_source, "\$this.find('.tf-booking-content-package input[name=\"tf_package\"]:checked').first()" ),
	'Tour submit must read the selected package from the submitted form only.'
);
tf_traveler_compliance_assert(
	false !== strpos( $tour_js_source, "\$selectedPackage.closest('.tf-single-package')" ),
	'Tour submit must read package time from the selected package container.'
);
tf_traveler_compliance_assert(
	false === strpos( $tour_js_source, "var selectedPackage = $('.tf-booking-content-package input[name=\"tf_package\"]:checked').val();" ),
	'Tour submit must not read selected package through a global package selector.'
);
tf_traveler_compliance_assert(
	false === strpos( $tour_js_source, "\$('#package-' + selectedPackage)" ),
	'Tour submit must not read package time through a global package id selector.'
);

tf_traveler_compliance_assert(
	false !== strpos( $tour_js_source, 'function tfParseTourBookingResponse' ),
	'Tour submit must safely parse AJAX responses.'
);
tf_traveler_compliance_assert(
	false !== strpos( $tour_js_source, 'function tfShowTourBookingErrors' ),
	'Tour submit must display AJAX errors to the user.'
);
tf_traveler_compliance_assert(
	false !== strpos( $tour_js_source, 'data.responseJSON' ) && false !== strpos( $tour_js_source, 'data.responseText' ),
	'Tour submit error callback must handle both JSON and text error responses.'
);

$expected_formats = array( 'Y/m/d', 'd/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'm-d-Y', 'Y.m.d', 'd.m.Y', 'm.d.Y' );
$php_formats_body = tf_traveler_compliance_function_body( $functions_source, 'tf_tour_get_supported_date_formats' );

foreach ( $expected_formats as $format ) {
	tf_traveler_compliance_assert(
		false !== strpos( $php_formats_body, "'{$format}'" ),
		"PHP traveler age validation must support {$format}."
	);
	tf_traveler_compliance_assert(
		false !== strpos( $tourfic_js_source, "'{$format}'" ),
		"Frontend traveler age validation must support {$format}."
	);
}

$parse_user_date_body = tf_traveler_compliance_function_body( $functions_source, 'tf_tour_parse_user_date' );
tf_traveler_compliance_assert(
	false !== strpos( $parse_user_date_body, 'tf_split_date_range( $date_string, false )' ),
	'Traveler age validation must derive the reference date from the selected date/range.'
);

$validate_age_body = tf_traveler_compliance_function_body( $functions_source, 'tf_tour_validate_traveler_age_limits' );
tf_traveler_compliance_assert(
	false !== strpos( $validate_age_body, 'tf_tour_parse_user_date( $tour_date )' ),
	'Server age validation must parse the selected travel date before comparing ages.'
);
tf_traveler_compliance_assert(
	false !== strpos( $validate_age_body, 'tf_traveler_booking_date_required' ),
	'Server age validation must return a booking-date error for invalid selected travel dates.'
);
tf_traveler_compliance_assert(
	false !== strpos( $validate_age_body, 'You must select booking date' ),
	'Invalid selected travel dates must not be reported as DOB mismatches.'
);
tf_traveler_compliance_assert(
	false === strpos( $validate_age_body, 'tf_tour_get_reference_timestamp( $tour_date )' ),
	'Server age validation must not fall back to today for invalid selected travel dates.'
);

echo "Traveler compliance booking regression checks passed.\n";
