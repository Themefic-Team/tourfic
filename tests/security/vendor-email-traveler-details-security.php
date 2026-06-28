<?php
/**
 * Static regression checks for vendor-only traveler details in email templates.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/vendor-email-traveler-details-security.php
 */

$root = dirname( __DIR__, 2 );
$file = $root . '/inc/Admin/Emails/TF_Handle_Emails.php';

if ( ! is_readable( $file ) ) {
	fwrite( STDERR, "Missing email handler: {$file}\n" );
	exit( 1 );
}

function tf_vendor_email_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tf_vendor_email_method_body( $source, $method ) {
	$matched = preg_match( '/function\s+' . preg_quote( $method, '/' ) . '\s*\(/', $source, $matches, PREG_OFFSET_CAPTURE );
	tf_vendor_email_assert( 1 === $matched, "Method {$method} not found." );
	$offset = $matches[0][1];
	$brace  = strpos( $source, '{', $offset );
	tf_vendor_email_assert( false !== $brace, "Method {$method} has no body." );

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

	tf_vendor_email_assert( false, "Method {$method} body is not balanced." );
}

$source = file_get_contents( $file );

tf_vendor_email_assert(
	false !== strpos( $source, 'public function replace_mail_tags( $template, $order_id, $context = array() )' ),
	'WooCommerce email tag replacement must accept recipient context.'
);
tf_vendor_email_assert(
	false !== strpos( $source, 'public function offline_replace_mail_tags( $template, $order_id, $order_data, $context = array() )' ),
	'Offline email tag replacement must accept recipient context.'
);
tf_vendor_email_assert(
	1 === substr_count( $source, 'tf_get_vendor_emails( $order_id )' ),
	'Vendor send paths must not use the email-only recipient helper for rendering.'
);

$replace_body = tf_vendor_email_method_body( $source, 'replace_mail_tags' );
tf_vendor_email_assert(
	false !== strpos( $replace_body, '$this->email_item_belongs_to_vendor' ),
	'WooCommerce vendor email rendering must filter order items by vendor id.'
);
tf_vendor_email_assert(
	false !== strpos( $replace_body, "\$meta_data['key'] != '_visitor_details'" ),
	'Raw _visitor_details metadata must remain excluded from generic booking-details output.'
);
tf_vendor_email_assert(
	false !== strpos( $replace_body, '$this->render_email_traveler_details( $visitor_details, $order_type )' ),
	'WooCommerce vendor email rendering must append formatted traveler details only through the formatter.'
);

$offline_body = tf_vendor_email_method_body( $source, 'offline_replace_mail_tags' );
tf_vendor_email_assert(
	false !== strpos( $offline_body, "\$order_items['visitor_details']" ),
	'Offline vendor email rendering must read stored order_details visitor_details.'
);
tf_vendor_email_assert(
	false !== strpos( $offline_body, "\$this->render_email_traveler_details( \$order_items['visitor_details'], \$order_data['post_type'] )" ),
	'Offline vendor email rendering must append formatted traveler details only through the formatter.'
);

$recipient_body = tf_vendor_email_method_body( $source, 'tf_get_vendor_recipients' );
tf_vendor_email_assert(
	false !== strpos( $recipient_body, "in_array( 'tf_vendor', (array) \$vendor->roles, true )" ),
	'Vendor recipients must be restricted to tf_vendor users.'
);

$formatter_body = tf_vendor_email_method_body( $source, 'render_email_traveler_details' );
tf_vendor_email_assert(
	false !== strpos( $source, 'JSON_ERROR_NONE !== json_last_error()' ),
	'Malformed traveler JSON must be rejected safely.'
);
tf_vendor_email_assert(
	false !== strpos( $source, "\$has_string_keys = ! empty( array_filter( \$decoded_keys, 'is_string' ) );" ),
	'Single traveler records with custom-only field keys must be treated as one traveler.'
);
tf_vendor_email_assert(
	false !== strpos( $source, 'tf_tour_get_traveler_document_download_url' ),
	'Traveler file fields must use the secure document download URL helper.'
);
tf_vendor_email_assert(
	false !== strpos( $source, 'return ! empty( $file_name ) ? esc_html( $file_name ) : esc_html( $empty_label );' ),
	'File fields without attachment IDs must fall back to a safe filename when available.'
);
tf_vendor_email_assert(
	false !== strpos( $formatter_body, 'Guest Details' ) && false !== strpos( $formatter_body, 'Traveler Details' ),
	'Traveler formatter must distinguish hotel guest details from tour traveler details.'
);

tf_vendor_email_assert(
	substr_count( $source, "'include_traveler_details' => true" ) >= 7,
	'Every vendor send and resend path must opt into traveler details explicitly.'
);
tf_vendor_email_assert(
	false === strpos( $source, "recipient'                => 'admin',\n" )
		&& false === strpos( $source, "recipient'                => 'customer',\n" ),
	'Admin and customer email paths must not opt into traveler details.'
);

echo "Vendor email traveler-details regression checks passed.\n";
