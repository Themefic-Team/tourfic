<?php
/**
 * Regression checks for Free and Pro availability ownership.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/availability-free-pro-ownership.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root      = dirname( __DIR__, 2 );
$pro_root  = dirname( $root ) . '/tourfic-pro';
$ical_root = dirname( $root ) . '/tourfic-ical';

function tf_availability_ownership_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$free_server = file_get_contents( $root . '/inc/Admin/TF_Options/TF_Options.php' );
$free_room   = file_get_contents( $root . '/inc/Admin/TF_Options/fields/room_availability/TF_room_availability.php' );
$free_tour   = file_get_contents( $root . '/inc/Admin/TF_Options/fields/tourAvailabilityCal/TF_tourAvailabilityCal.php' );
$free_apt    = file_get_contents( $root . '/inc/Admin/TF_Options/fields/aptAvailabilityCal/TF_aptAvailabilityCal.php' );
$free_tour_metabox = file_get_contents( $root . '/inc/Admin/TF_Options/metaboxes/tf-tour-metabox.php' );
$free_js     = file_get_contents( $root . '/sass/admin/js/free/tf-options.js' );
$free_asset  = file_get_contents( $root . '/assets/admin/js/tourfic-admin-scripts.js' );
$free_runtime = file_get_contents( $root . '/inc/Classes/Tour/Tour.php' );
$free_checkout = file_get_contents( $root . '/inc/functions/woocommerce/wc-tour.php' );
$free_backend_booking = file_get_contents( $root . '/inc/Admin/Backend_Booking/TF_Tour_Backend_Booking.php' );
$free_frontend_js = file_get_contents( $root . '/sass/app/js/free/tourfic.js' );
$readme      = file_get_contents( $root . '/readme.txt' );

foreach (
	array(
		'room_availability/TF_room_availability.php',
		'tourAvailabilityCal/TF_tourAvailabilityCal.php',
		'aptAvailabilityCal/TF_aptAvailabilityCal.php',
	) as $renderer
) {
	tf_availability_ownership_assert(
		file_exists( $root . '/inc/Admin/TF_Options/fields/' . $renderer ),
		'Free is missing availability renderer ' . $renderer . '.'
	);
}

$metabox_expectations = array(
	'inc/Admin/TF_Options/metaboxes/tf-room-metabox.php'      => array( "'id'      => 'avil_by_date'", "'id'            => 'avail_date'", "'type'          => 'room_availability'" ),
	'inc/Admin/TF_Options/metaboxes/tf-tour-metabox.php'      => array( "'id'   => 'tour_availability'", "'type' => 'tourAvailabilityCal'" ),
	'inc/Admin/TF_Options/metaboxes/tf-apartment-metabox.php' => array( "'id'         => 'enable_availability'", "'id'         => 'apt_availability'", "'type'       => 'aptAvailabilityCal'" ),
);

foreach ( $metabox_expectations as $file => $markers ) {
	$source = file_get_contents( $root . '/' . $file );
	foreach ( $markers as $marker ) {
		tf_availability_ownership_assert( false !== strpos( $source, $marker ), $file . ' is missing ' . $marker . '.' );
	}
}

foreach (
	array(
		'tourfic_room_availability_rule_data',
		'tourfic_room_availability_calendar_event',
		'tourfic_apartment_availability_rule_data',
		'tourfic_apartment_availability_calendar_event',
		'tourfic_tour_availability_rule_data',
		'tourfic_tour_availability_calendar_event',
		"current_user_can( 'edit_post', \$post_id )",
	) as $marker
) {
	tf_availability_ownership_assert( false !== strpos( $free_server, $marker ), 'Free availability server is missing ' . $marker . '.' );
}

tf_availability_ownership_assert(
	false !== strpos( $free_room, 'name="tf_room_price"' )
		&& false !== strpos( $free_apt, 'name="tf_apt_price"' )
		&& false !== strpos( $free_tour, 'name="tf_tour_adult_price"' ),
	'Free must render its complete core room, apartment, and tour pricing controls.'
);
tf_availability_ownership_assert(
	false !== strpos( $readme, '* Room Availability by date' )
		&& false === strpos( $readme, '* Room Availability by date (Pro)' )
		&& false !== strpos( $readme, '* Apartment Availability by Date' )
		&& false !== strpos( $readme, '* Availability based on Time & Dates (Pro)' )
		&& false !== strpos( $readme, '* Fixed Tour Scheduling (Pro)' ),
	'Readme availability labels must match Free ownership.'
);

tf_availability_ownership_assert(
	false === strpos( $free_tour_metabox, 'How often does this Tour run?' )
		&& false === strpos( $free_tour_metabox, 'tf_tour_avail_type' )
		&& false === strpos( $free_tour, 'Add Start Time' )
		&& false === strpos( $free_tour, 'allowed_time' )
		&& false === strpos( $free_server, "\$_POST['allowed_time']" )
		&& false === strpos( $free_js, 'setTourAllowedTimes' ),
	'Free must not author or process Pro-only Fixed tour and start-time availability.'
);

$free_schedule_runtime = $free_runtime . $free_checkout . $free_backend_booking . $free_frontend_js;
foreach ( array( 'allowed_time', 'package_start_time', 'name="check-in-time"', 'check_in_time', "\$tour_type == 'fixed'", "\$meta['type']" ) as $marker ) {
	tf_availability_ownership_assert(
		false === strpos( $free_schedule_runtime, $marker ),
		'Free runtime still implements Pro schedule marker ' . $marker . '.'
	);
}
foreach (
	array(
		'tourfic_tour_booking_schedule_context',
		'tourfic_tour_booking_schedule_field',
		'tourfic:tour-booking:state',
		'tourfic:tour-booking:request-data',
		'tourfic:tour-popup:response',
	) as $extension_point
) {
	tf_availability_ownership_assert(
		false !== strpos( $free_schedule_runtime, $extension_point ),
		'Free is missing neutral Pro extension point ' . $extension_point . '.'
	);
}

$free_availability_surface = $free_server . $free_room . $free_tour . $free_apt . $free_js;
foreach (
	array(
		'is_tf_pro',
		'TourficPro',
		'TF_PRO',
		'save_tour_package_pricing',
		'selected_packages[]',
		'tf_room_adult_price',
		'tf_apt_adult_price',
		'tf_option_group_price_',
	) as $pro_marker
) {
	tf_availability_ownership_assert(
		false === strpos( $free_availability_surface, $pro_marker ),
		'Free availability still contains Pro-only marker ' . $pro_marker . '.'
	);
}

foreach ( array( 'room', 'apartment', 'tour' ) as $feature ) {
	$event = 'tourfic:' . $feature . '-availability:prepare-request';
	tf_availability_ownership_assert( false !== strpos( $free_js, $event ), 'Free source is missing lifecycle event ' . $event . '.' );
	tf_availability_ownership_assert( false !== strpos( $free_asset, $event ), 'Built Free asset is missing lifecycle event ' . $event . '.' );
}

$rest_files = array(
	'inc/Classes/REST_API/TF_Tour_Rest_API.php'      => 'tf_tour_availability',
	'inc/Classes/REST_API/TF_Apartment_Rest_API.php' => 'tf_apt_availability',
	'inc/Classes/REST_API/TF_Hotel_Rest_API.php'     => 'tf_hotel_avail_date',
);
foreach ( $rest_files as $file => $temporary_option ) {
	$source = file_get_contents( $root . '/' . $file );
	tf_availability_ownership_assert(
		false === strpos( $source, "get_option( '{$temporary_option}' )" )
			&& false === strpos( $source, "delete_option( '{$temporary_option}' )" ),
		$file . ' must not consume or delete temporary state from a GET request.'
	);
}

$pro_server = file_get_contents( $pro_root . '/inc/classes/TF_Pro_Availability.php' );
$pro_js     = file_get_contents( $pro_root . '/sass/admin/js/pro/availability.js' );
$pro_asset  = file_get_contents( $pro_root . '/assets/admin/js/tourfic-pro-availability.js' );
$pro_loader = file_get_contents( $pro_root . '/tourfic-pro.php' );
$pro_tour   = file_get_contents( $pro_root . '/admin/tf-options/fields/tourAvailabilityCal/TF_tourAvailabilityCal.php' );
$pro_tour_metabox = file_get_contents( $pro_root . '/admin/tf-options/metaboxes/tf-tour-metabox.php' );
$pro_frontend_js = file_get_contents( $pro_root . '/sass/app/js/pro/tourfic-pro.js' );

foreach (
	array(
		'tourfic_room_availability_rule_data',
		'tourfic_apartment_availability_rule_data',
		'tourfic_tour_availability_rule_data',
		'save_tour_package_pricing',
		'tf_room_adult_price',
		'tf_apt_adult_price',
		'tf_option_group_price_',
	) as $marker
) {
	tf_availability_ownership_assert( false !== strpos( $pro_server, $marker ), 'Pro availability extension is missing ' . $marker . '.' );
}

tf_availability_ownership_assert(
	false !== strpos( $pro_server, "current_user_can( 'edit_post', \$post_id )" ),
	'Pro availability mutations must authorize the concrete tour.'
);
tf_availability_ownership_assert(
	false !== strpos( $pro_loader, 'array_merge( $pro_fields ?: array(), $fields )' ),
	'Pro enhanced renderers must load before matching Free renderer classes.'
);
tf_availability_ownership_assert(
	false !== strpos( $pro_loader, "'tf-pro-availability'" ),
	'Pro availability extension asset is not enqueued.'
);
tf_availability_ownership_assert(
	false !== strpos( $pro_tour_metabox, 'How often does this Tour run?' )
		&& false !== strpos( $pro_tour_metabox, 'tf_tour_avail_type' )
		&& false !== strpos( $pro_tour, 'Add Start Time' )
		&& false !== strpos( $pro_tour, 'allowed_time' )
		&& false !== strpos( $pro_server, "'allowed_time'" )
		&& false !== strpos( $pro_js, 'populateTourTimes' ),
	'Pro must own Fixed tour and start-time availability authoring and processing.'
);
foreach (
	array(
		'tourfic_tour_booking_schedule_context',
		'tourfic_tour_booking_schedule_field',
		'tourfic_tour_backend_booking_fields',
		'tourfic_tour_cart_schedule_item_data',
		"'fixed' === \$schedule_type",
		"'allowed_time'",
	) as $marker
) {
	tf_availability_ownership_assert( false !== strpos( $pro_server, $marker ), 'Pro runtime is missing schedule marker ' . $marker . '.' );
}
foreach ( array( 'package_start_time', 'check-in-time', 'check_in_time', 'tourfic:tour-booking:request-data', 'tourfic:tour-popup:response', 'tourfic:backend-tour-date-change' ) as $marker ) {
	$pro_schedule_js = $pro_frontend_js . $pro_js;
	tf_availability_ownership_assert( false !== strpos( $pro_schedule_js, $marker ), 'Pro JavaScript is missing schedule marker ' . $marker . '.' );
}

foreach ( array( 'room', 'apartment', 'tour' ) as $feature ) {
	$event = 'tourfic:' . $feature . '-availability:prepare-request';
	tf_availability_ownership_assert( false !== strpos( $pro_js, $event ), 'Pro source is missing lifecycle listener ' . $event . '.' );
	tf_availability_ownership_assert( false !== strpos( $pro_asset, $event ), 'Built Pro asset is missing lifecycle listener ' . $event . '.' );
}

$ical_room = file_get_contents( $ical_root . '/inc/classes/TF_Hotel_iCal.php' );
$ical_apt  = file_get_contents( $ical_root . '/inc/classes/TF_Apartment_iCal.php' );
$ical_js   = file_get_contents( $ical_root . '/sass/admin/js/addon/ical/tourfic-ical.js' );
tf_availability_ownership_assert(
	false !== strpos( $ical_room, "['avail_date']" )
		&& false !== strpos( $ical_apt, "['apt_availability']" )
		&& false !== strpos( $ical_js, ".avail_date" )
		&& false !== strpos( $ical_js, ".apt_availability" ),
	'iCal must continue using the unchanged Free availability storage contract.'
);

echo "Free, Pro, and iCal availability ownership regression checks passed.\n";
