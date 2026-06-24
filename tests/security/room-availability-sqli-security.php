<?php
/**
 * Static regression checks for room availability SQL injection hardening.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/room-availability-sqli-security.php
 */

$root = dirname( __DIR__, 2 );

$files = array(
	'hotel'             => $root . '/inc/Classes/Hotel/Hotel.php',
	'helper'            => $root . '/inc/Classes/Helper.php',
	'functions'         => $root . '/inc/functions.php',
	'pro_functions'     => dirname( $root ) . '/tourfic-pro/inc/functions.php',
	'wc_hotel'          => $root . '/inc/functions/woocommerce/wc-hotel.php',
	'offline_hotel'     => $root . '/inc/App/Without_Payment/Hotel_Offline_Booking.php',
	'backend_hotel'     => $root . '/inc/Admin/Backend_Booking/TF_Hotel_Backend_Booking.php',
	'car_availability'  => $root . '/inc/Classes/Car_Rental/Availability.php',
	'pro_backend_hotel' => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Hotel_Backend_Booking_Rest_API.php',
);

foreach ( $files as $label => $file ) {
	if ( ! is_readable( $file ) ) {
		fwrite( STDERR, "Missing fixture {$label}: {$file}\n" );
		exit( 1 );
	}
}

function tf_room_sqli_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tf_room_sqli_file( $file ) {
	return file_get_contents( $file );
}

function tf_room_sqli_function_body( $source, $function ) {
	$matched = preg_match( '/function\s+' . preg_quote( $function, '/' ) . '\s*\(/', $source, $matches, PREG_OFFSET_CAPTURE );
	tf_room_sqli_assert( 1 === $matched, "Function {$function} not found." );

	$offset = $matches[0][1];
	$brace  = strpos( $source, '{', $offset );
	tf_room_sqli_assert( false !== $brace, "Function {$function} has no body." );

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

	tf_room_sqli_assert( false, "Function {$function} body is not balanced." );
}

$hotel_source     = tf_room_sqli_file( $files['hotel'] );
$helper_source    = tf_room_sqli_file( $files['helper'] );
$functions_source = tf_room_sqli_file( $files['functions'] );
$pro_functions    = tf_room_sqli_file( $files['pro_functions'] );

$room_availability_body = tf_room_sqli_function_body( $hotel_source, 'tf_room_availability_callback' );
tf_room_sqli_assert(
	false !== strpos( $room_availability_body, "absint( wp_unslash( \$_POST['post_id'] ) )" ),
	'Room availability callback must cast posted hotel ID with absint().'
);
tf_room_sqli_assert(
	false !== strpos( $room_availability_body, 'get_post( $hotel_id )' ),
	'Room availability callback must validate that the posted hotel exists.'
);
tf_room_sqli_assert(
	false !== strpos( $room_availability_body, "'tf_hotel' !== \$hotel_post->post_type" ),
	'Room availability callback must reject non-hotel post IDs.'
);
tf_room_sqli_assert(
	false === strpos( $room_availability_body, "sanitize_text_field( \$_POST['post_id'] )" ),
	'Room availability callback must not use text sanitization as ID validation.'
);
tf_room_sqli_assert(
	1 !== preg_match( '/[\'"]query[\'"]\s*=>/', $room_availability_body ),
	'Room availability callback must not pass raw SQL query fragments to the order helper.'
);

$helper_body = tf_room_sqli_function_body( $helper_source, 'tourfic_order_table_data' );
tf_room_sqli_assert(
	false !== strpos( $helper_body, 'tf_order_table_structured_sql' ),
	'Order helper must build filters through the structured SQL helper.'
);
tf_room_sqli_assert(
	false === strpos( $helper_body, "\$query['query']" ),
	'Order helper must not append caller-provided raw query strings.'
);

$fallback_body = tf_room_sqli_function_body( $functions_source, 'tourfic_order_table_data' );
tf_room_sqli_assert(
	false === strpos( $fallback_body, "\$query['query']" ),
	'Fallback order helper must not append caller-provided raw query strings.'
);

$vendor_body = tf_room_sqli_function_body( $pro_functions, 'tourfic_vendor_order_table_data' );
tf_room_sqli_assert(
	false === strpos( $vendor_body, "\$query['query']" ),
	'Pro vendor order helper must not append caller-provided raw query strings.'
);

foreach ( array( $root . '/inc', dirname( $root ) . '/tourfic-pro/inc' ) as $scan_root ) {
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $scan_root ) );
	foreach ( $iterator as $file ) {
		if ( ! $file->isFile() || 'php' !== $file->getExtension() ) {
			continue;
		}

		$source = file_get_contents( $file->getPathname() );
		tf_room_sqli_assert(
			1 !== preg_match( '/[\'"]query[\'"]\s*=>/', $source ),
			'Raw order-table query array remains in ' . $file->getPathname()
		);
	}
}

echo "Room availability SQL injection regression checks passed.\n";
