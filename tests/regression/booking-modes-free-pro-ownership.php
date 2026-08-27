<?php
/**
 * Regression checks for Booking Modes ownership.
 *
 * Run from the Tourfic Free plugin root:
 * TOURFIC_PRO_ROOT=/path/to/tourfic-pro php tests/regression/booking-modes-free-pro-ownership.php
 */

$root     = dirname( __DIR__, 2 );
$pro_root = getenv( 'TOURFIC_PRO_ROOT' ) ?: dirname( $root ) . '/tourfic-pro';

function tf_booking_modes_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function tf_booking_modes_read( $file ) {
	tf_booking_modes_assert( is_readable( $file ), "Missing fixture: {$file}" );

	return file_get_contents( $file );
}

$free_paths = array(
	$root . '/inc',
	$root . '/templates',
	$root . '/sass',
	$root . '/assets/demo',
	$root . '/assets/app/js/tourfic-scripts.js',
	$root . '/assets/app/js/tourfic-scripts.min.js',
	$root . '/assets/admin/js/tourfic-admin-scripts.js',
	$root . '/assets/admin/js/tourfic-admin-scripts.min.js',
);
$pro_markers = array(
	'booking-by',
	'external-booking-type',
	'booking-code',
	'booking-url',
	'booking-query',
	'booking-attribute',
	'book-confirm-field',
	'hotel-book-confirm-field',
	'car-book-confirm-field',
	'enable_inline_booking',
	'without_payment',
	'Without_Payment',
);

foreach ( $free_paths as $path ) {
	$files = is_dir( $path )
		? new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ) )
		: array( new SplFileInfo( $path ) );

	foreach ( $files as $file ) {
		if ( ! $file->isFile() || ! preg_match( '/\.(?:php|js|csv)$/', $file->getFilename() ) ) {
			continue;
		}

		$source = tf_booking_modes_read( $file->getPathname() );
		foreach ( $pro_markers as $marker ) {
			tf_booking_modes_assert(
				false === strpos( $source, $marker ),
				$file->getPathname() . " still contains Pro Booking Modes marker {$marker}."
			);
		}
	}
}

$free_contracts = array(
	'inc/functions/woocommerce/wc-tour.php'      => array(
		"apply_filters(\n\t\t\t'tourfic_tour_booking_mode_response'",
	),
	'inc/functions/woocommerce/wc-hotel.php'     => array(
		"apply_filters(\n\t\t\t'tourfic_hotel_booking_mode_response'",
	),
	'inc/functions/woocommerce/wc-apartment.php' => array(
		"apply_filters( 'tourfic_apartment_core_booking', true",
		"apply_filters(\n\t\t\t'tourfic_apartment_booking_mode_response'",
	),
	'inc/functions/woocommerce/wc-car.php'       => array(
		"apply_filters(\n\t\t\t'tourfic_car_booking_mode_response'",
	),
	'inc/Classes/Tour/Tour.php'                   => array(
		"apply_filters( 'tourfic_tour_booking_form_visibility', true",
		"apply_filters( 'tourfic_tour_booking_type', '1'",
		"do_action( 'tourfic_tour_booking_payment_options', \$post_id, \$meta, \$booking_type )",
	),
	'inc/Classes/Apartment/Apartment.php'          => array( "'tourfic_apartment_booking_display'" ),
	'sass/app/js/free/hotel.js'                    => array(
		"\$('body').on('submit', 'form.tf-room'",
		'tourfic:hotel-booking:form-data',
	),
	'inc/App/Templates/Components/Shared/Single/Booking_Form.php' => array(
		"apply_filters( 'tourfic_hotel_booking_form_visibility', true",
		"apply_filters( 'tourfic_tour_booking_form_visibility', true",
	),
);

foreach ( $free_contracts as $file => $markers ) {
	$source = tf_booking_modes_read( $root . '/' . $file );
	foreach ( $markers as $marker ) {
		tf_booking_modes_assert( false !== strpos( $source, $marker ), "Free is missing neutral Booking Modes contract {$marker} in {$file}." );
	}
}

tf_booking_modes_assert(
	file_exists( $root . '/inc/Core/Booking_Popup.php' )
		&& file_exists( $root . '/inc/App/Hotel_Booking_Popup.php' )
		&& ! file_exists( $root . '/inc/Core/Without_Payment_Booking.php' )
		&& ! file_exists( $root . '/inc/App/Without_Payment/Hotel_Offline_Booking.php' ),
	'Free shared booking popup classes still expose the Pro without-payment implementation.'
);

$pro_files = array(
	'owner'     => $pro_root . '/inc/classes/TF_Pro_Booking_Modes.php',
	'deposit'   => $pro_root . '/inc/classes/TF_Pro_Deposit.php',
	'loader'    => $pro_root . '/inc/functions.php',
	'field'     => $pro_root . '/admin/tf-options/fields/booking_code/TF_booking_code.php',
	'tour_ui'   => $pro_root . '/admin/tf-options/metaboxes/tf-tour-metabox.php',
	'hotel_ui'  => $pro_root . '/admin/tf-options/metaboxes/tf-hotel-metabox.php',
	'apt_ui'    => $pro_root . '/admin/tf-options/metaboxes/tf-apartment-metabox.php',
	'car_ui'    => $pro_root . '/admin/tf-options/metaboxes/tf-carrental-metabox.php',
	'frontend'  => $pro_root . '/sass/app/js/pro/tourfic-pro.js',
	'frontend_dist' => $pro_root . '/assets/app/js/tourfic-pro.js',
	'admin_js'  => $pro_root . '/sass/admin/js/pro/admin.js',
	'admin_dist' => $pro_root . '/assets/admin/js/tourfic-pro-admin.js',
	'tour_list' => $pro_root . '/inc/shortcodes/Tour_External_Listings.php',
);
$pro_sources = array();
foreach ( $pro_files as $label => $file ) {
	$pro_sources[ $label ] = tf_booking_modes_read( $file );
}

foreach ( array( 'booking-by', 'booking-url', 'booking-code', 'without_payment', 'tourfic_booking_customer_email_fields', 'external_post_ids' ) as $marker ) {
	tf_booking_modes_assert( false !== strpos( $pro_sources['owner'], $marker ), "Pro Booking Modes owner is missing {$marker}." );
}
tf_booking_modes_assert(
	false !== strpos( $pro_sources['loader'], "TF_PRO_INC_PATH . 'classes/TF_Pro_Booking_Modes.php'" ),
	'Pro does not load its Booking Modes owner.'
);
tf_booking_modes_assert(
	false !== strpos( $pro_sources['owner'], "add_filter( 'tourfic_tour_booking_type', array( \$this, 'tour_booking_type' ), 10, 3 )" )
		&& false !== strpos( $pro_sources['deposit'], "add_action( 'tourfic_tour_booking_payment_options', array( \$this, 'render_tour_payment_options' ), 10, 3 )" )
		&& false !== strpos( $pro_sources['deposit'], 'render_tour_payment_options( $post_id, $meta, $booking_type = null )' ),
	'Free/Pro Tour payment hooks do not preserve the backward-compatible three-argument contract.'
);
tf_booking_modes_assert(
	false !== strpos( $pro_sources['field'], 'class TF_booking_code' )
		&& false !== strpos( $pro_sources['field'], 'return $this->value;' ),
	'Pro does not own the external embed-code field behavior.'
);

foreach ( array( 'tour_ui', 'hotel_ui', 'apt_ui', 'car_ui' ) as $label ) {
	tf_booking_modes_assert( false !== strpos( $pro_sources[ $label ], "'booking-by'" ), "Pro {$label} is missing Booking Type settings." );
}
foreach ( array( 'booking_confirm[', 'without_payment', 'tourfic:apartment-booking:response' ) as $marker ) {
	tf_booking_modes_assert( false !== strpos( $pro_sources['frontend'], $marker ), "Pro frontend booking handler is missing {$marker}." );
	tf_booking_modes_assert( false !== strpos( $pro_sources['frontend_dist'], $marker ), "Pro generated frontend bundle is missing {$marker}." );
}
tf_booking_modes_assert(
	false !== strpos( $pro_sources['admin_js'], '.tf-single-repeater-book-confirm-field' ),
	'Pro admin JavaScript does not own confirmation-field initialization.'
);
tf_booking_modes_assert(
	false !== strpos( $pro_sources['admin_dist'], '.tf-single-repeater-book-confirm-field' ),
	'Pro generated admin bundle is missing confirmation-field initialization.'
);
tf_booking_modes_assert(
	false === strpos( $pro_sources['frontend'], "\$('body').on('submit', 'form.tf-room'" ),
	'Pro registers a duplicate generic Hotel form submission handler.'
);
tf_booking_modes_assert(
	false !== strpos( $pro_sources['tour_list'], "TF_Pro_Booking_Modes::external_post_ids" ),
	'Pro external listing shortcodes do not use the Pro-owned listing resolver.'
);

echo "Booking Modes Free/Pro ownership regression checks passed.\n";
