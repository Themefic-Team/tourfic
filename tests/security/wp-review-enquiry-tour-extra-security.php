<?php
/**
 * Static regression checks for WordPress review enquiry and tour-extra findings.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/wp-review-enquiry-tour-extra-security.php
 */

$root = dirname( __DIR__, 2 );

$files = array(
	'enquiry'  => $root . '/inc/Core/Enquiry.php',
	'wc_tour'  => $root . '/inc/functions/woocommerce/wc-tour.php',
	'tour_cls' => $root . '/inc/Classes/Tour/Tour.php',
);

foreach ( $files as $label => $file ) {
	if ( ! is_readable( $file ) ) {
		fwrite( STDERR, "Missing fixture {$label}: {$file}\n" );
		exit( 1 );
	}
}

function tf_wp_review_security_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tf_wp_review_security_file( $file ) {
	return file_get_contents( $file );
}

function tf_wp_review_security_function_body( $source, $function ) {
	$matched = preg_match( '/function\s+' . preg_quote( $function, '/' ) . '\s*\(/', $source, $matches, PREG_OFFSET_CAPTURE );
	tf_wp_review_security_assert( 1 === $matched, "Function {$function} not found." );

	$offset = $matches[0][1];
	$brace  = strpos( $source, '{', $offset );
	tf_wp_review_security_assert( false !== $brace, "Function {$function} has no body." );

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

	tf_wp_review_security_assert( false, "Function {$function} body is not balanced." );
}

function tf_wp_review_security_assert_order( $body, $first, $second, $message ) {
	$first_offset  = strpos( $body, $first );
	$second_offset = strpos( $body, $second );

	tf_wp_review_security_assert( false !== $first_offset, "Missing first marker for order check: {$first}" );
	tf_wp_review_security_assert( false !== $second_offset, "Missing second marker for order check: {$second}" );
	tf_wp_review_security_assert( $first_offset < $second_offset, $message );
}

$enquiry_source = tf_wp_review_security_file( $files['enquiry'] );
$wc_tour_source = tf_wp_review_security_file( $files['wc_tour'] );
$tour_cls_source = tf_wp_review_security_file( $files['tour_cls'] );

foreach ( array( 'tf_enquiry_filter_post_callback', 'tf_enquiry_filter_mail_callback' ) as $callback ) {
	$body = tf_wp_review_security_function_body( $enquiry_source, $callback );

	tf_wp_review_security_assert_order(
		$body,
		"check_ajax_referer( 'updates', '_ajax_nonce', false )",
		'enquiry_table_data',
		"{$callback} must verify nonce before reading enquiry data."
	);
	tf_wp_review_security_assert_order(
		$body,
		'tf_current_user_can_access_enquiry_post',
		'enquiry_table_data',
		"{$callback} must authorize before reading enquiry data."
	);
	tf_wp_review_security_assert(
		false !== strpos( $body, 'tf_current_user_can_manage_all_enquiries' ),
		"{$callback} must scope non-manager users to their own enquiries."
	);
}

$reply_body = tf_wp_review_security_function_body( $enquiry_source, 'tf_enquiry_reply_email_callback' );
tf_wp_review_security_assert_order(
	$reply_body,
	"check_ajax_referer( 'updates', '_ajax_nonce', false )",
	"SELECT * FROM {\$wpdb->prefix}tf_enquiry_data WHERE id = %d",
	'Reply callback must verify nonce before reading reply data.'
);
tf_wp_review_security_assert_order(
	$reply_body,
	'tf_current_user_can_reply_to_enquiry',
	'wp_mail(',
	'Reply callback must authorize before sending mail.'
);
tf_wp_review_security_assert_order(
	$reply_body,
	'tf_current_user_can_reply_to_enquiry',
	'UPDATE {$wpdb->prefix}tf_enquiry_data SET enquiry_status',
	'Reply callback must authorize before updating enquiry data.'
);
tf_wp_review_security_assert(
	false === strpos( $reply_body, "if ( ! current_user_can( 'manage_options' ) )" ),
	'Reply callback must not keep the old non-terminal manage_options gate.'
);
tf_wp_review_security_assert(
	false !== strpos( $reply_body, 'is_email( $reply_mail )' ),
	'Reply callback must validate the recipient email after sanitizing it.'
);

$bulk_body = tf_wp_review_security_function_body( $enquiry_source, 'tf_enquiry_bulk_action_callback' );
tf_wp_review_security_assert_order(
	$bulk_body,
	'tf_current_user_can_access_enquiry',
	"DELETE FROM {\$wpdb->prefix}tf_enquiry_data",
	'Bulk delete must authorize each enquiry before deleting.'
);
tf_wp_review_security_assert_order(
	$bulk_body,
	'tf_current_user_can_access_enquiry',
	"UPDATE {\$wpdb->prefix}tf_enquiry_data SET enquiry_status",
	'Bulk status changes must authorize each enquiry before updating.'
);

$wc_tour_booking = tf_wp_review_security_function_body( $wc_tour_source, 'tf_tours_booking_function' );
tf_wp_review_security_assert(
	false !== strpos( $wc_tour_source, "wp_ajax_nopriv_tf_tours_booking" ),
	'Tour booking must remain publicly reachable for frontend bookings.'
);
tf_wp_review_security_assert(
	false !== strpos( $wc_tour_booking, "\$tour_extra_title = '';" ),
	'WooCommerce tour booking must keep tour_extra_title initialized for tours without extras.'
);
tf_wp_review_security_assert(
	false !== strpos( $wc_tour_booking, 'max( 0, intval( $tour_extra_quantity[$extra_key] ) )' ),
	'WooCommerce tour booking must normalize quantity-priced extras to non-negative integers.'
);
tf_wp_review_security_assert(
	false !== strpos( $wc_tour_booking, '$tour_extra_total = max( 0, $tour_extra_total );' ),
	'WooCommerce tour booking must floor computed tour extra total before storage.'
);
tf_wp_review_security_assert(
	false === strpos( $wc_tour_booking, '* $tour_extra_quantity[$extra_key]' ),
	'WooCommerce tour booking must not multiply directly by client-supplied extra quantity.'
);

tf_wp_review_security_assert(
	false !== strpos( $tour_cls_source, 'max( 0, intval( $tour_extra_quantity[ $extra_key ] ) )' ),
	'Tour details pricing must normalize quantity-priced extras to non-negative integers.'
);
tf_wp_review_security_assert(
	false !== strpos( $tour_cls_source, '$tour_extra_total = max( 0, $tour_extra_total );' ),
	'Tour details pricing must floor computed tour extra total.'
);
tf_wp_review_security_assert(
	false === strpos( $tour_cls_source, '* $tour_extra_quantity[ $extra_key ]' ),
	'Tour details pricing must not multiply directly by client-supplied extra quantity.'
);

echo "WordPress review enquiry and tour-extra security regression checks passed.\n";
