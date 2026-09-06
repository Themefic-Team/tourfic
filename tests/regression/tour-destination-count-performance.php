<?php
/**
 * Regression checks for Tour Destination count query behavior.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/tour-destination-count-performance.php
 */

namespace Tourfic\Core {
	class Shortcodes {}
}

namespace Tourfic\Traits {
	trait Singleton {}
}

namespace Tourfic\Classes {
	class Helper {}
}

namespace {
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
	}

	class WP_Error {}

	$test_expired_tour_ids    = array();
	$test_expired_destinations = array();
	$test_get_posts_calls     = 0;
	$test_terms_result        = array();

	function get_posts( $args ) {
		global $test_expired_tour_ids, $test_get_posts_calls;

		++$test_get_posts_calls;
		tourfic_destination_count_assert( 'tf_tours' === $args['post_type'], 'Expired count query must target Tour posts.' );
		tourfic_destination_count_assert( 'expired' === $args['post_status'], 'Expired count query must exclude other post statuses.' );
		tourfic_destination_count_assert( 'ids' === $args['fields'], 'Expired count query must request IDs only.' );

		return $test_expired_tour_ids;
	}

	function wp_get_object_terms( $object_ids, $taxonomy, $args ) {
		global $test_expired_destinations, $test_terms_result;

		tourfic_destination_count_assert( 'tour_destination' === $taxonomy, 'Expired tours must be mapped through the destination taxonomy.' );
		tourfic_destination_count_assert( 'all_with_object_id' === $args['fields'], 'Expired destination mapping must retain object relationships.' );
		tourfic_destination_count_assert( ! empty( $object_ids ), 'Expired destination mapping must receive Tour IDs.' );

		return $test_terms_result ?: $test_expired_destinations;
	}

	function is_wp_error( $value ) {
		return $value instanceof WP_Error;
	}

	function tourfic_destination_count_assert( $condition, $message ) {
		if ( ! $condition ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
			echo "FAIL: {$message}\n";
			exit( 1 );
		}
	}

	require_once dirname( __DIR__, 2 ) . '/inc/App/Shortcodes/Tour_Destinations.php';

	$shortcode = new \Tourfic\App\Shortcodes\Tour_Destinations();
	$method    = new \ReflectionMethod( $shortcode, 'get_destination_tour_counts' );
	$method->setAccessible( true );

	$destinations = array(
		(object) array(
			'term_id' => 10,
			'count'   => 3,
		),
		(object) array(
			'term_id' => 20,
			'count'   => 1,
		),
	);

	$published_counts = $method->invoke( $shortcode, $destinations, false );
	tourfic_destination_count_assert( array( 10 => 3, 20 => 1 ) === $published_counts, 'Published term counts must remain unchanged.' );
	tourfic_destination_count_assert( 0 === $test_get_posts_calls, 'Published-only counts must not run a posts query.' );

	$test_expired_tour_ids = array( 101, 102, 103, 104 );
	$test_expired_destinations = array(
		(object) array( 'term_id' => 10, 'object_id' => 101 ),
		(object) array( 'term_id' => 10, 'object_id' => 102 ),
		(object) array( 'term_id' => 20, 'object_id' => 103 ),
		(object) array( 'term_id' => 30, 'object_id' => 104 ),
	);
	$combined_counts = $method->invoke( $shortcode, $destinations, true );
	tourfic_destination_count_assert( array( 10 => 5, 20 => 2 ) === $combined_counts, 'Expired counts must be added once per displayed destination relationship.' );
	tourfic_destination_count_assert( 1 === $test_get_posts_calls, 'Expired counts must use one posts query regardless of destination count.' );

	$test_terms_result = new WP_Error();
	$fallback_counts   = $method->invoke( $shortcode, $destinations, true );
	tourfic_destination_count_assert( array( 10 => 3, 20 => 1 ) === $fallback_counts, 'A failed expired-term lookup must retain published counts.' );

	$source = file_get_contents( dirname( __DIR__, 2 ) . '/inc/App/Shortcodes/Tour_Destinations.php' );
	tourfic_destination_count_assert( false === strpos( $source, 'new \\WP_Query' ), 'Tour Destinations must not query once per rendered destination.' );
	tourfic_destination_count_assert( false === strpos( $source, "'tax_query'" ), 'Tour Destinations must not contain the flagged taxonomy query.' );

	echo "PASS: Tour Destination count performance checks passed.\n";
}
