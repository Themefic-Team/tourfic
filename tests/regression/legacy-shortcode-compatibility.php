<?php
/**
 * Regression checks for legacy shortcode content compatibility.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/legacy-shortcode-compatibility.php
 */

namespace Tourfic\Traits {
	trait Singleton {
	}
}

namespace Tourfic\Classes {
	class Helper {
		public static function tf_is_woo_active() {
			return false;
		}
	}
}

namespace Tourfic\Classes\Room {
	class Room {
	}
}

namespace Elementor {
	class Widget_Base {
	}
}

namespace {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
	define( 'ARRAY_A', 'ARRAY_A' );

	$GLOBALS['tourfic_shortcode_test_actions'] = array();
	$GLOBALS['tourfic_shortcode_test_filters'] = array();
	$GLOBALS['tourfic_shortcode_test_options'] = array(
		'tourfic_option_name_migration'    => '1.0.0',
		'tourfic_shortcode_name_migration' => '1.0.0',
	);
	$GLOBALS['tourfic_shortcode_test_post_fields']  = array();
	$GLOBALS['tourfic_shortcode_test_post_updates'] = array();
	$GLOBALS['tourfic_shortcode_test_meta_updates'] = array();
	$GLOBALS['tourfic_shortcode_test_post_update_result'] = 1;
	$GLOBALS['tourfic_shortcode_test_rendered_shortcodes'] = array();

	class WP_Error {
	}

	class Tourfic_Shortcode_Test_WPDB {
		public $posts = 'wp_posts';
		public $postmeta = 'wp_postmeta';
		public $options = 'wp_options';
		public $post_ids = array();
		public $meta_rows = array();
		public $option_names = array();
		public $queries = array();

		public function prepare( $query, ...$args ) {
			unset( $args );
			return $query;
		}

		public function esc_like( $value ) {
			return $value;
		}

		public function get_col( $query ) {
			$this->queries[] = $query;

			if ( false !== strpos( $query, $this->posts ) ) {
				return $this->post_ids;
			}
			if ( false !== strpos( $query, $this->options ) ) {
				return $this->option_names;
			}

			return array();
		}

		public function get_results( $query, $output ) {
			unset( $output );
			$this->queries[] = $query;
			return $this->meta_rows;
		}
	}

	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['tourfic_shortcode_test_actions'][] = compact( 'hook', 'callback', 'priority', 'accepted_args' );
	}

	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['tourfic_shortcode_test_filters'][] = compact( 'hook', 'callback', 'priority', 'accepted_args' );
	}

	function get_option( $option, $default = false ) {
		return $GLOBALS['tourfic_shortcode_test_options'][ $option ] ?? $default;
	}

	function update_option( $option, $value, $autoload = null ) {
		unset( $autoload );
		$changed = ! array_key_exists( $option, $GLOBALS['tourfic_shortcode_test_options'] )
			|| $GLOBALS['tourfic_shortcode_test_options'][ $option ] !== $value;
		$GLOBALS['tourfic_shortcode_test_options'][ $option ] = $value;
		return $changed;
	}

	function get_post_field( $field, $post_id, $context = 'display' ) {
		unset( $field, $context );
		return $GLOBALS['tourfic_shortcode_test_post_fields'][ $post_id ] ?? '';
	}

	function wp_update_post( $post_data, $wp_error = false ) {
		unset( $wp_error );
		$GLOBALS['tourfic_shortcode_test_post_updates'][] = $post_data;
		return $GLOBALS['tourfic_shortcode_test_post_update_result'];
	}

	function update_metadata_by_mid( $meta_type, $meta_id, $value ) {
		$GLOBALS['tourfic_shortcode_test_meta_updates'][] = compact( 'meta_type', 'meta_id', 'value' );
		return true;
	}

	function maybe_unserialize( $value ) {
		return $value;
	}

	function wp_slash( $value ) {
		if ( is_array( $value ) ) {
			return array_map( 'wp_slash', $value );
		}

		return is_string( $value ) ? addslashes( $value ) : $value;
	}

	function absint( $value ) {
		return abs( (int) $value );
	}

	function esc_attr( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8', false );
	}

	function do_shortcode( $shortcode ) {
		$GLOBALS['tourfic_shortcode_test_rendered_shortcodes'][] = $shortcode;
		return 'rendered';
	}

	function is_wp_error( $value ) {
		return $value instanceof WP_Error;
	}

	function tourfic_shortcode_compatibility_assert( $condition, $message ) {
		if ( ! $condition ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only diagnostics.
			echo "FAIL: {$message}\n";
			exit( 1 );
		}
	}

	$wpdb = new Tourfic_Shortcode_Test_WPDB();
	$GLOBALS['wpdb'] = $wpdb;

	require_once ABSPATH . 'inc/Classes/Migrator.php';

	$migrator   = new \Tourfic\Classes\Migrator();
	$reflection = new \ReflectionClass( $migrator );
	$map_method = $reflection->getMethod( 'tourfic_shortcode_name_map' );
	$map_method->setAccessible( true );
	$replace_method = $reflection->getMethod( 'tourfic_replace_shortcode_tags' );
	$replace_method->setAccessible( true );
	$shortcode_map = $map_method->invoke( $migrator );
	$action_hooks  = array_column( $GLOBALS['tourfic_shortcode_test_actions'], 'hook' );
	$filter_hooks  = array_column( $GLOBALS['tourfic_shortcode_test_filters'], 'hook' );

	tourfic_shortcode_compatibility_assert(
		in_array( 'added_post_meta', $action_hooks, true ) && in_array( 'updated_post_meta', $action_hooks, true ),
		'Post-meta compatibility hooks must cover both new and updated builder/import data.'
	);
	tourfic_shortcode_compatibility_assert(
		in_array( 'wp_insert_post_data', $filter_hooks, true ) && in_array( 'pre_update_option', $filter_hooks, true ),
		'Post-content and content-option compatibility filters must remain registered.'
	);

	$expected_map = array(
		'hotel_locations'                => 'tourfic_hotel_locations',
		'room_types'                     => 'tourfic_room_types',
		'tf-wishlist'                    => 'tourfic_wishlist',
		'tf_apartment'                   => 'tourfic_apartment',
		'tf_apartment_external_listings' => 'tourfic_apartment_external_listings',
		'tf_apartment_locations'         => 'tourfic_apartment_locations',
		'tf_carrental_brand'             => 'tourfic_carrental_brand',
		'tf_carrental_locations'         => 'tourfic_carrental_locations',
		'tf_cars'                        => 'tourfic_cars',
		'tf_hotel'                       => 'tourfic_hotel',
		'tf_hotel_external_listings'     => 'tourfic_hotel_external_listings',
		'tf_recent_apartment'            => 'tourfic_recent_apartment',
		'tf_recent_blog'                 => 'tourfic_recent_blog',
		'tf_recent_cars'                 => 'tourfic_recent_cars',
		'tf_recent_hotel'                => 'tourfic_recent_hotel',
		'tf_recent_room'                 => 'tourfic_recent_room',
		'tf_recent_tour'                 => 'tourfic_recent_tour',
		'tf_reviews'                     => 'tourfic_reviews',
		'tf_room'                        => 'tourfic_room',
		'tf_search_form'                 => 'tourfic_search_form',
		'tf_search_result'               => 'tourfic_search_result',
		'tf_tour'                        => 'tourfic_tour',
		'tf_tour_external_listings'      => 'tourfic_tour_external_listings',
		'tf_vendor_post'                 => 'tourfic_vendor_post',
		'tour_destinations'              => 'tourfic_tour_destinations',
		'tourfic_destinations'           => 'tourfic_hotel_locations',
	);

	tourfic_shortcode_compatibility_assert(
		$expected_map === $shortcode_map,
		'The legacy shortcode migration map is incomplete or has changed unexpectedly.'
	);

	foreach ( $shortcode_map as $legacy_tag => $current_tag ) {
		$legacy_content = '[' . $legacy_tag . ' foo="bar"]Content[/' . $legacy_tag . ']';
		$expected_content = '[' . $current_tag . ' foo="bar"]Content[/' . $current_tag . ']';
		tourfic_shortcode_compatibility_assert(
			$expected_content === $replace_method->invoke( $migrator, $legacy_content, $shortcode_map ),
			'Legacy shortcode was not converted exactly: ' . $legacy_tag . '.'
		);
	}

	$lookalike = '[tf_cars_extra] [tf_cars-text] tf_cars';
	tourfic_shortcode_compatibility_assert(
		$lookalike === $replace_method->invoke( $migrator, $lookalike, $shortcode_map ),
		'Non-shortcode lookalikes must remain unchanged.'
	);

	$post_data = $migrator->tourfic_normalize_shortcode_post_data(
		array( 'post_content' => '[tf_cars count=6 style=\'grid\']' )
	);
	tourfic_shortcode_compatibility_assert(
		'[tourfic_cars count=6 style=\'grid\']' === $post_data['post_content'],
		'Legacy shortcodes must be normalized when post content is saved.'
	);

	$migrator->tourfic_normalize_shortcode_post_meta(
		62816,
		6616,
		'_elementor_data',
		array( 'shortcode' => '[tf_cars count=6]' )
	);
	tourfic_shortcode_compatibility_assert(
		array( 'shortcode' => '[tourfic_cars count=6]' ) === $GLOBALS['tourfic_shortcode_test_meta_updates'][0]['value'],
		'Legacy shortcodes must be normalized in imported or builder post metadata.'
	);

	$widget_value = $migrator->tourfic_normalize_shortcode_option_value(
		array( 'text' => '[tf_search_form]' ),
		'widget_text',
		array()
	);
	tourfic_shortcode_compatibility_assert(
		array( 'text' => '[tourfic_search_form]' ) === $widget_value,
		'Legacy shortcodes must be normalized in widget options.'
	);

	$unrelated_value = array( 'text' => '[tf_search_form]' );
	tourfic_shortcode_compatibility_assert(
		$unrelated_value === $migrator->tourfic_normalize_shortcode_option_value( $unrelated_value, 'unrelated_option', array() ),
		'Unrelated options must not be modified on every option update.'
	);

	$GLOBALS['tourfic_shortcode_test_options']['tourfic_shortcode_name_migration'] = '1.0.0';
	$migrator->tourfic_migrate_shortcode_names();
	tourfic_shortcode_compatibility_assert(
		'1.0.1' === $GLOBALS['tourfic_shortcode_test_options']['tourfic_shortcode_name_migration'],
		'Sites that completed migration 1.0.0 must run the compatibility migration once more.'
	);

	$GLOBALS['tourfic_shortcode_test_options']['tourfic_shortcode_name_migration'] = '1.0.0';
	$GLOBALS['tourfic_shortcode_test_post_fields'][23] = '[tf_cars]';
	$GLOBALS['tourfic_shortcode_test_post_update_result'] = new WP_Error();
	$wpdb->post_ids = array( 23 );
	$migrator->tourfic_migrate_shortcode_names();
	tourfic_shortcode_compatibility_assert(
		'1.0.0' === $GLOBALS['tourfic_shortcode_test_options']['tourfic_shortcode_name_migration'],
		'A failed content update must not mark the compatibility migration complete.'
	);

	$pro_widget = dirname( ABSPATH ) . '/tourfic-pro/inc/elementor/widgets/external-listings.php';
	if ( file_exists( $pro_widget ) ) {
		$pro_source = file_get_contents( $pro_widget );
		tourfic_shortcode_compatibility_assert(
			false === strpos( $pro_source, "'[tf_external_listings" ),
			'The Pro External Listings widget must not execute the unregistered legacy shortcode.'
		);
		foreach ( array( 'tourfic_hotel_external_listings', 'tourfic_tour_external_listings', 'tourfic_apartment_external_listings' ) as $shortcode ) {
			tourfic_shortcode_compatibility_assert(
				false !== strpos( $pro_source, "'{$shortcode}'" ),
				'The Pro External Listings widget is missing provider mapping for ' . $shortcode . '.'
			);
		}

		require_once $pro_widget;

		class Tourfic_Shortcode_Test_External_Listings extends \TF_Pro_External_Listings {
			public $test_settings = array();

			public function get_settings_for_display() {
				return $this->test_settings;
			}

			public function render_for_test() {
				ob_start();
				parent::render();
				return ob_get_clean();
			}
		}

		$widget = new Tourfic_Shortcode_Test_External_Listings();
		$widget_cases = array(
			'hotel' => array(
				'expected_tag' => 'tourfic_hotel_external_listings',
				'locations'    => array( 3, 8 ),
			),
			'tour' => array(
				'expected_tag' => 'tourfic_tour_external_listings',
				'locations'    => array( 5, 13 ),
			),
			'apartment' => array(
				'expected_tag' => 'tourfic_apartment_external_listings',
				'locations'    => array( 21 ),
			),
			'unknown' => array(
				'expected_tag' => 'tourfic_hotel_external_listings',
				'locations'    => array( 3, 8 ),
			),
		);

		foreach ( $widget_cases as $type => $case ) {
			$widget->test_settings = array(
				'title'               => 'External "Offers"',
				'subtitle'            => 'Current listings',
				'count'               => 4,
				'style'               => 'slider',
				'type'                => $type,
				'hotel_locations'     => array( 3, 8 ),
				'tour_destinations'   => array( 5, 13 ),
				'apartment_locations' => array( 21 ),
			);
			$output = $widget->render_for_test();
			$rendered_shortcode = end( $GLOBALS['tourfic_shortcode_test_rendered_shortcodes'] );

			tourfic_shortcode_compatibility_assert(
				'rendered' === $output,
				'The Pro External Listings widget must render its selected shortcode output.'
			);
			tourfic_shortcode_compatibility_assert(
				0 === strpos( $rendered_shortcode, '[' . $case['expected_tag'] . ' ' ),
				'The Pro widget selected the wrong provider for type ' . $type . '.'
			);
			tourfic_shortcode_compatibility_assert(
				false !== strpos( $rendered_shortcode, 'locations="' . implode( ',', $case['locations'] ) . '"' ),
				'The Pro widget selected the wrong locations for type ' . $type . '.'
			);
			tourfic_shortcode_compatibility_assert(
				false !== strpos( $rendered_shortcode, 'title="External &quot;Offers&quot;"' ),
				'The Pro widget must safely encode shortcode attribute delimiters.'
			);
		}
	}

	echo "PASS: legacy shortcode content remains compatible without public aliases.\n";
}
