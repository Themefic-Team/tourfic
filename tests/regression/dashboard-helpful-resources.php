<?php
/**
 * Regression coverage for dashboard Helpful Resources.
 *
 * Run with: php tests/regression/dashboard-helpful-resources.php
 */

$tourfic_root    = dirname( __DIR__, 2 );
$settings_source = file_get_contents( $tourfic_root . '/inc/Admin/TF_Options/classes/TF_Settings.php' );
$helper_source   = file_get_contents( $tourfic_root . '/inc/Classes/Helper.php' );

function tourfic_dashboard_resources_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$resources_start = strpos( $settings_source, '<div class="tf-quick-access">' );
$resources_end   = strpos( $settings_source, 'public function tf_get_sidebar_plugin_list', $resources_start );
tourfic_dashboard_resources_assert(
	false !== $resources_start && false !== $resources_end,
	'The dashboard Helpful Resources section must be discoverable.'
);
$resource_markup = substr( $settings_source, $resources_start, $resources_end - $resources_start );

tourfic_dashboard_resources_assert(
	false !== strpos( $resource_markup, "esc_html__( 'Documentation', 'tourfic' )" )
		&& false !== strpos( $resource_markup, "'utm_medium' => 'dashboard_doc_link'" )
		&& 1 === substr_count( $resource_markup, 'class="tf-access-item"' ),
	'The dashboard must retain its Documentation resource.'
);

$removed_resources = array(
	'Get Support',
	'Join our Community',
	'See our Roadmap',
	'Request a Feature',
	'dashboard_support_link',
	'https://app.loopedin.io/tourfic',
);

foreach ( $removed_resources as $removed_resource ) {
	tourfic_dashboard_resources_assert(
		false === strpos( $resource_markup, $removed_resource ),
		"The dashboard Helpful Resources markup must not contain {$removed_resource}."
	);
}

tourfic_dashboard_resources_assert(
	false === strpos( $helper_source, 'settings_header_menu_request' )
		&& false === strpos( $helper_source, 'Having troubles?' )
		&& false === strpos( $helper_source, '>Get help<' ),
	'The dashboard header must not render the support prompt.'
);

$help_start = strpos( $settings_source, 'public function tf_get_help_callback' );
$help_end   = strpos( $settings_source, 'public function tf_options_page', $help_start );
tourfic_dashboard_resources_assert(
	false !== $help_start && false !== $help_end,
	'The Get Help page markup must be discoverable.'
);
$help_markup = substr( $settings_source, $help_start, $help_end - $help_start );

$retained_help_cards = array(
	'Get Started Quickly',
	'Setup Wizard',
	'Documentation',
	'Video Tutorials',
	'Watch Video',
);

foreach ( $retained_help_cards as $retained_help_card ) {
	tourfic_dashboard_resources_assert(
		false !== strpos( $help_markup, $retained_help_card ),
		"The Get Help page must retain {$retained_help_card}."
	);
}

$removed_help_cards = array(
	'Need a Custom Solution?',
	'Request Customization',
	'Need a Hand?',
	'Join the community',
	'Email Support',
	'get_help_support',
	'Live Chat',
	'get_help_live_chat',
);

foreach ( $removed_help_cards as $removed_help_card ) {
	tourfic_dashboard_resources_assert(
		false === strpos( $help_markup, $removed_help_card ),
		"The Get Help page must not contain {$removed_help_card}."
	);
}

tourfic_dashboard_resources_assert(
	3 === substr_count( $help_markup, 'class="tf-single-support-card"' )
		&& 1 === substr_count( $help_markup, 'class="tf-support-cards tf-support-cards-resources"' )
		&& false === strpos( $help_markup, 'tf-support-cards-4' ),
	'The three retained Get Help cards must share one responsive grid.'
);

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
echo "Tourfic dashboard Helpful Resources checks passed.\n";
