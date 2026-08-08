<?php
/**
 * Regression checks for WordPress.org airport-service compliance.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/wordpress-org-airport-service-compliance.php
 */

$root = dirname( __DIR__, 2 );

function tf_airport_compliance_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$options_source  = file_get_contents( $root . '/inc/Admin/TF_Options/TF_Options.php' );
$metabox_source  = file_get_contents( $root . '/inc/Admin/TF_Options/metaboxes/tf-hotel-metabox.php' );
$settings_source = file_get_contents( $root . '/inc/Admin/TF_Options/options/tf-settings.php' );
$hotel_source    = file_get_contents( $root . '/inc/Classes/Hotel/Hotel.php' );
$pricing_source  = file_get_contents( $root . '/inc/Classes/Hotel/Pricing.php' );
$readme_source   = file_get_contents( $root . '/readme.txt' );

foreach (
	array(
		"'id'       => 'airport_service'",
		"'id'         => 'airport_service_type'",
		"'airport_pickup_price'",
		"'airport_dropoff_price'",
		"'airport_pickup_dropoff_price'",
	) as $field_marker
) {
	tf_airport_compliance_assert(
		false !== strpos( $options_source, $field_marker ),
		"Free airport settings are missing {$field_marker}."
	);
}

tf_airport_compliance_assert(
	false !== strpos( $metabox_source, 'TF_Options::hotel_airport_service_fields()' ),
	'Free hotel metabox must register the complete airport-service controls.'
);
tf_airport_compliance_assert(
	false !== strpos( $settings_source, "'id'      => 'hotel_service_popup_title'" )
		&& false !== strpos( $settings_source, "'id'      => 'hotel_service_popup_subtile'" )
		&& false !== strpos( $settings_source, "'id'      => 'hotel_service_popup_action'" ),
	'Free settings must expose the airport-service popup labels.'
);
tf_airport_compliance_assert(
	false !== strpos( $hotel_source, 'tf_hotel_airport_service_title_price' )
		&& false !== strpos( $pricing_source, "\$meta['airport_service']" ),
	'Free must retain the airport-service frontend and pricing implementation.'
);
tf_airport_compliance_assert(
	false !== strpos( $readme_source, '* Airport Pickup & Dropoff Service' )
		&& false === strpos( $readme_source, '* Airport Pickup & Dropoff Service (Pro)' ),
	'Free readme must identify airport service as a Free feature.'
);
tf_airport_compliance_assert(
	false === strpos( $options_source, 'is_tf_pro' )
		&& false === strpos( $metabox_source, 'is_tf_pro' ),
	'Free airport settings must not depend on Pro state.'
);

echo "WordPress.org airport-service compliance regression checks passed.\n";
