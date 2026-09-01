<?php

namespace Tourfic\Admin\TF_Options;
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Availability;
use Tourfic\Classes\Room\Room;

class TF_Options {

	private static $instance = null;

	/**
	 * Singleton instance
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		//load files
		$this->load_files();

		//load metaboxes
		$this->load_metaboxes();

		//load options
		$this->load_options();

		//load taxonomy
		$this->load_taxonomy();

		add_action( 'wp_ajax_tourfic_load_more_icons', array( $this, 'tf_load_more_icons' ) );
		add_action( 'wp_ajax_tourfic_icon_search', array( $this, 'tf_icon_search' ) );

		add_action( 'wp_ajax_tourfic_add_hotel_room_availability', array( $this, 'tf_add_hotel_room_availability' ) );
		add_action( 'wp_ajax_tourfic_get_hotel_room_availability', array( $this, 'tf_get_hotel_room_availability' ) );
		add_action( 'wp_ajax_tourfic_reset_room_availability', array( $this, 'tf_reset_room_availability' ) );
		add_action( 'save_post', array( $this, 'tf_update_room_avail_date_price' ), 9999, 2 );
		add_action( 'wp_ajax_tourfic_add_apartment_availability', array( $this, 'tf_add_apartment_availability' ) );
		add_action( 'wp_ajax_tourfic_get_apartment_availability', array( $this, 'tf_get_apartment_availability' ) );
		add_action( 'wp_ajax_tourfic_reset_apt_availability', array( $this, 'tf_reset_apt_availability' ) );

		add_action( 'wp_ajax_tourfic_add_tour_availability', array( $this, 'tf_add_tour_availability' ) );
		add_action( 'wp_ajax_tourfic_get_tour_availability', array( $this, 'tf_get_tour_availability' ) );
		add_action( 'wp_ajax_tourfic_reset_tour_availability', array( $this, 'tf_reset_tour_availability' ) );
		add_action( 'save_post', array( $this, 'tf_update_apt_availability_price' ), 99, 2 );
		add_action( 'wp_ajax_tourfic_insert_category_data', array( $this, 'tf_insert_category_data_callback' ) );
		add_action( 'wp_ajax_tourfic_delete_category_data', array( $this, 'tf_delete_category_data_callback' ) );
		add_action( 'wp_ajax_tourfic_insert_post_data', array( $this, 'tf_insert_post_data_callback' ) );
		add_action( 'wp_ajax_tourfic_delete_post_data', array( $this, 'tf_delete_post_data_callback' ) );
	}

	public function tf_options_file_path( $file_path = '' ) {
		return plugin_dir_path( __FILE__ ) . $file_path;
	}

	public function tf_options_file_url( $file_url = '' ) {
		return plugin_dir_url( __FILE__ ) . $file_url;
	}

	/**
	 * Get the Free airport-service controls for hotel administration.
	 *
	 * @return array
	 */
	public static function hotel_airport_service_fields() {
		return array(
			array(
				'id'       => 'airport_service',
				'type'     => 'switch',
				'label'    => esc_html__( 'Airport Pickup Service', 'tourfic' ),
				'subtitle' => esc_html__(
					'Activate this feature to provide airport pickup services as an added convenience for your guests.',
					'tourfic'
				),
				'default'  => false,
			),
			array(
				'id'         => 'airport_service_type',
				'type'       => 'checkbox',
				'label'      => esc_html__( 'Service Type', 'tourfic' ),
				'inline'     => true,
				'dependency' => array(
					array( 'airport_service', '==', '1' ),
				),
				'options'    => array(
					'pickup'  => esc_html__( 'Pickup', 'tourfic' ),
					'dropoff' => esc_html__( 'Drop-off', 'tourfic' ),
					'both'    => esc_html__( 'Pickup & Drop-off', 'tourfic' ),
				),
			),
			self::hotel_airport_service_pricing_field(
				'airport_pickup_price',
				esc_html__( 'Pickup Service', 'tourfic' ),
				'pickup',
				esc_html__( 'Pickup', 'tourfic' ),
				esc_html__( 'Pickup Pricing Type', 'tourfic' )
			),
			self::hotel_airport_service_pricing_field(
				'airport_dropoff_price',
				esc_html__( 'Drop-off Service', 'tourfic' ),
				'dropoff',
				esc_html__( 'Drop-off', 'tourfic' ),
				esc_html__( 'Drop-off Pricing Type', 'tourfic' )
			),
			self::hotel_airport_service_pricing_field(
				'airport_pickup_dropoff_price',
				esc_html__( 'Pickup & Drop-off Service', 'tourfic' ),
				'both',
				esc_html__( 'Pickup & Drop-off', 'tourfic' ),
				esc_html__( 'Pickup & Drop-off Pricing Type', 'tourfic' )
			),
		);
	}

	/**
	 * Build one airport-service pricing tab.
	 *
	 * @param string $field_id          Stored field ID.
	 * @param string $title             Field title.
	 * @param string $service_type      Service dependency value.
	 * @param string $tab_title         Pricing tab title.
	 * @param string $pricing_type_label Pricing type label.
	 * @return array
	 */
	private static function hotel_airport_service_pricing_field(
		$field_id,
		$title,
		$service_type,
		$tab_title,
		$pricing_type_label
	) {
		return array(
			'id'         => $field_id,
			'type'       => 'tab',
			'title'      => $title,
			'dependency' => array(
				array( 'airport_service_type', 'any', $service_type ),
				array( 'airport_service', '==', '1' ),
			),
			'tabs'       => array(
				array(
					'id'     => 'tab-1',
					'title'  => $tab_title,
					'icon'   => 'fa fa-heart',
					'fields' => array(
						array(
							'id'      => 'airport_pickup_price_type',
							'type'    => 'select',
							'label'   => $pricing_type_label,
							'options' => array(
								'per_person' => esc_html__( 'Per Person', 'tourfic' ),
								'fixed'      => esc_html__( 'Fixed Price', 'tourfic' ),
								'free'       => esc_html__( 'Free / Complimentary', 'tourfic' ),
							),
							'default' => 'per_person',
						),
						array(
							'id'          => 'airport_service_fee_adult',
							'type'        => 'number',
							'dependency'  => array(
								array( 'airport_pickup_price_type', '==', 'per_person' ),
							),
							'label'       => esc_html__( 'Adult Price', 'tourfic' ),
							'subtitle'    => esc_html__( 'Price per adult. Insert number only (No currency sign needed).', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'airport_service_fee_children',
							'type'        => 'number',
							'dependency'  => array(
								array( 'airport_pickup_price_type', '==', 'per_person' ),
							),
							'label'       => esc_html__( 'Children Price', 'tourfic' ),
							'subtitle'    => esc_html__( 'Price per child. Insert number only (No currency sign needed).', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
						array(
							'id'         => 'airport_service_fee_fixed',
							'type'       => 'number',
							'dependency' => array(
								array( 'airport_pickup_price_type', '==', 'fixed' ),
							),
							'label'      => esc_html__( 'Fixed Price', 'tourfic' ),
							'subtitle'   => esc_html__( 'Insert number only (No currency sign needed).', 'tourfic' ),
							'attributes' => array(
								'min' => '0',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Get total day count for a month/year pair.
	 *
	 * Supports environments where PHP `ext-calendar` is unavailable.
	 *
	 * @param int|string $month Month number.
	 * @param int|string $year  Year number.
	 * @return int
	 */
	private function tf_get_days_in_month( $month, $year ) {
		$month = (int) $month;
		$year  = (int) $year;

		if ( $month < 1 || $month > 12 || $year < 1 ) {
			return 0;
		}

		if ( function_exists( 'cal_days_in_month' ) ) {
			return (int) cal_days_in_month( CAL_GREGORIAN, $month, $year );
		}

		$month_start = strtotime( sprintf( '%04d-%02d-01', $year, $month ) );

		return $month_start ? (int) gmdate( 't', $month_start ) : 0;
	}

	/**
	 * Resolve bulk-edit day numbers for a month.
	 *
	 * When both day numbers and weekdays are selected, dates must satisfy both
	 * conditions instead of being generated by separate passes.
	 *
	 * @param int|string $month        Month number.
	 * @param int|string $year         Year number.
	 * @param mixed      $repeat_days  Selected day numbers.
	 * @param mixed      $repeat_weeks Selected weekdays.
	 * @return array
	 */
	private function tf_get_tour_bulk_edit_days( $month, $year, $repeat_days, $repeat_weeks ) {
		$days_in_month = $this->tf_get_days_in_month( $month, $year );
		if ( $days_in_month <= 0 ) {
			return array();
		}

		$resolved_days = range( 1, $days_in_month );

		if ( ! empty( $repeat_days ) && is_array( $repeat_days ) ) {
			$normalized_days = array();
			foreach ( $repeat_days as $day ) {
				$day = is_scalar( $day ) ? trim( (string) $day ) : '';

				if ( '' === $day || ! preg_match( '/^-?\d+$/', $day ) ) {
					continue;
				}

				$normalized_days[] = (int) $day;
			}

			$resolved_days = array_values(
				array_filter(
					array_unique( $normalized_days ),
					static function( $day ) use ( $days_in_month ) {
						return $day >= 1 && $day <= $days_in_month;
					}
				)
			);
		}

		if ( ! empty( $repeat_weeks ) && is_array( $repeat_weeks ) ) {
			$normalized_weeks = array();
			foreach ( $repeat_weeks as $week_day ) {
				$week_day = is_scalar( $week_day ) ? trim( (string) $week_day ) : '';

				if ( '' === $week_day || ! preg_match( '/^-?\d+$/', $week_day ) ) {
					continue;
				}

				$normalized_weeks[] = (int) $week_day;
			}

			$valid_weeks = array_values(
				array_filter(
					array_unique( $normalized_weeks ),
					static function( $week_day ) {
						return $week_day >= 0 && $week_day <= 6;
					}
				)
			);

			if ( empty( $valid_weeks ) ) {
				return array();
			}

			$month_padded  = str_pad( (string) $month, 2, '0', STR_PAD_LEFT );
			$resolved_days = array_values(
				array_filter(
					$resolved_days,
					static function( $day ) use ( $month_padded, $year, $valid_weeks ) {
						$timestamp = strtotime( sprintf( '%04d-%02d-%02d', (int) $year, (int) $month_padded, (int) $day ) );

						return false !== $timestamp && in_array( (int) gmdate( 'w', $timestamp ), $valid_weeks, true );
					}
				)
			);
		}

		sort( $resolved_days );

		return $resolved_days;
	}

	/**
	 * Decode and sanitize availability JSON that may already be an array.
	 *
	 * Availability fields can be stored as JSON strings or as arrays
	 * (e.g. when updated/reset by older code paths).
	 *
	 * @param mixed $value             Raw value.
	 * @param bool  $preserve_unknown Preserve sanitized extension fields loaded from storage.
	 * @return array
	 */
	private function tf_safe_json_decode_assoc( $value, $preserve_unknown = false ) {
		if ( empty( $value ) ) {
			return array();
		}

		if ( is_string( $value ) ) {
			$decoded = json_decode( $value, true );
			$value   = JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ? $decoded : array();
		} elseif ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized = array();
		foreach ( $value as $rule_key => $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			$rule_key = $this->tf_sanitize_availability_rule_key( $rule_key, $rule, $preserve_unknown );
			if ( null === $rule_key ) {
				continue;
			}

			$sanitized[ $rule_key ] = $this->tf_sanitize_availability_rule( $rule, $preserve_unknown );
		}

		return $sanitized;
	}

	/**
	 * Validate an availability date or date-range array key.
	 *
	 * @param mixed $rule_key         Raw rule key.
	 * @param array $rule             Availability rule associated with the key.
	 * @param bool  $preserve_unknown Whether the rule came from existing storage.
	 * @return string|int|null
	 */
	private function tf_sanitize_availability_rule_key( $rule_key, $rule, $preserve_unknown = false ) {
		if ( is_int( $rule_key ) ) {
			if ( $preserve_unknown ) {
				return $rule_key;
			}

			return $this->tf_availability_rule_date_key( $rule );
		}

		if ( ! is_string( $rule_key ) ) {
			return null;
		}

		$rule_key = sanitize_text_field( $rule_key );
		if ( preg_match( '/^(\d{4}\/\d{2}\/\d{2})_block$/', $rule_key, $matches ) ) {
			$date = $this->tf_sanitize_availability_date( $matches[1] );

			return '' !== $date ? $date . '_block' : null;
		}

		$dates    = explode( ' - ', $rule_key );
		if ( count( $dates ) > 2 ) {
			return null;
		}

		foreach ( $dates as $date ) {
			if ( '' === $this->tf_sanitize_availability_date( $date ) ) {
				return null;
			}
		}

		return implode( ' - ', $dates );
	}

	/**
	 * Build a normalized date key for legacy numerically indexed request rules.
	 *
	 * @param array $rule Availability rule.
	 * @return string|null
	 */
	private function tf_availability_rule_date_key( $rule ) {
		$check_in  = $this->tf_sanitize_availability_date( $rule['check_in'] ?? '' );
		$check_out = $this->tf_sanitize_availability_date( $rule['check_out'] ?? '' );

		if ( '' === $check_in ) {
			return null;
		}

		return '' !== $check_out && $check_in !== $check_out
			? $check_in . ' - ' . $check_out
			: $check_in;
	}

	/**
	 * Validate one normalized availability date.
	 *
	 * @param mixed $date Raw date.
	 * @return string
	 */
	private function tf_sanitize_availability_date( $date ) {
		$date = is_scalar( $date ) ? sanitize_text_field( (string) $date ) : '';
		if ( ! preg_match( '/^\d{4}\/\d{2}\/\d{2}$/', $date ) ) {
			return '';
		}

		$date_object = \DateTimeImmutable::createFromFormat( '!Y/m/d', $date );

		return $date_object && $date_object->format( 'Y/m/d' ) === $date ? $date : '';
	}

	/**
	 * Sanitize one availability rule using its stored field types.
	 *
	 * @param array $rule             Raw rule.
	 * @param bool  $preserve_unknown Preserve sanitized extension fields loaded from storage.
	 * @return array
	 */
	private function tf_sanitize_availability_rule( $rule, $preserve_unknown = false ) {
		$sanitized = array();

		foreach ( $rule as $key => $value ) {
			if ( ! is_string( $key ) ) {
				continue;
			}

			$key = sanitize_key( $key );
			if ( '' === $key ) {
				continue;
			}

			if ( in_array( $key, array( 'check_in', 'check_out' ), true ) ) {
				$date = $this->tf_sanitize_availability_date( $value );
				if ( '' !== $date ) {
					$sanitized[ $key ] = $date;
				}
				continue;
			}

			if ( 'status' === $key || preg_match( '/^tf_option_status_\d+$/', $key ) ) {
				$sanitized[ $key ] = $this->tf_sanitize_availability_status( $value );
				continue;
			}

			if ( 'price_by' === $key ) {
				$value = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
				if ( in_array( $value, array( '1', '2', '3' ), true ) ) {
					$sanitized[ $key ] = $value;
				}
				continue;
			}

			if ( 'pricing_type' === $key ) {
				$value = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
				if ( in_array( $value, array( 'per_night', 'per_person', 'person', 'group', 'package' ), true ) ) {
					$sanitized[ $key ] = $value;
				}
				continue;
			}

			$is_number = in_array(
				$key,
				array( 'price', 'adult_price', 'child_price', 'infant_price', 'min_person', 'max_person', 'max_capacity' ),
				true
			) || preg_match( '/^tf_option_(?:room|adult|child|infant|group)_price_\d+$/', $key );
			if ( $is_number ) {
				$sanitized[ $key ] = $this->tf_sanitize_availability_number( $value );
				continue;
			}

			if ( 'options_count' === $key ) {
				$sanitized[ $key ] = absint( $value );
				continue;
			}

			if ( 'selected_packages' === $key ) {
				$sanitized[ $key ] = is_array( $value )
					? array_values( array_unique( array_map( 'strval', array_map( 'absint', $value ) ) ) )
					: array();
				continue;
			}

			if ( 'allowed_time' === $key || preg_match( '/^tf_option_(?:times|group_discount)_\d+$/', $key ) ) {
				$sanitized[ $key ] = is_array( $value ) ? Helper::tf_sanitize_recursive_input( $value ) : array();
				continue;
			}

			if ( preg_match( '/^tf_room_option_\d+$/', $key ) ) {
				$sanitized[ $key ] = empty( $value ) ? '' : '1';
				continue;
			}

			if ( preg_match( '/^tf_option_title_\d+$/', $key ) ) {
				$sanitized[ $key ] = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
				continue;
			}

			if ( preg_match( '/^tf_option_pricing_type_\d+$/', $key ) ) {
				$value = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
				if ( in_array( $value, array( 'per_room', 'per_person', 'person', 'group' ), true ) ) {
					$sanitized[ $key ] = $value;
				}
				continue;
			}

			if ( $preserve_unknown ) {
				$sanitized[ $key ] = Helper::tf_sanitize_recursive_input( $value );
			}
		}

		return $sanitized;
	}

	/**
	 * Build a sanitized availability extension payload from declared fields.
	 *
	 * @param string $context Availability context.
	 * @param array  $meta    Listing meta.
	 * @param int    $post_id Listing post ID.
	 * @return array
	 */
	private function tf_sanitize_availability_request_data( $context, $meta, $post_id ) {
		$schema = apply_filters(
			'tourfic_availability_request_schema',
			array(
				'fields'   => array(),
				'patterns' => array(),
			),
			$context,
			$meta,
			$post_id
		);
		$schema = is_array( $schema ) ? $schema : array();
		$fields = is_array( $schema['fields'] ?? null ) ? $schema['fields'] : array();
		$patterns = is_array( $schema['patterns'] ?? null ) ? $schema['patterns'] : array();
		$request = array();

		foreach ( $fields as $key => $type ) {
			if ( ! is_string( $key ) || ! array_key_exists( $key, $_POST ) ) {
				continue;
			}

			$request[ $key ] = $this->tf_sanitize_availability_request_value( $_POST[ $key ], $type );
		}

		foreach ( $_POST as $raw_key => $value ) {
			if ( ! is_string( $raw_key ) ) {
				continue;
			}

			$key = sanitize_key( $raw_key );
			if ( '' === $key || array_key_exists( $key, $request ) ) {
				continue;
			}

			foreach ( $patterns as $pattern => $type ) {
				if ( is_string( $pattern ) && 1 === preg_match( $pattern, $key ) ) {
					$request[ $key ] = $this->tf_sanitize_availability_request_value( $value, $type );
					break;
				}
			}
		}

		return $request;
	}

	/**
	 * Sanitize one field declared by an availability request schema.
	 *
	 * @param mixed  $value Raw request value.
	 * @param string $type  Sanitizer type.
	 * @return mixed
	 */
	private function tf_sanitize_availability_request_value( $value, $type ) {
		$value = wp_unslash( $value );

		switch ( $type ) {
			case 'absint':
				return absint( $value );
			case 'absint_array':
				return is_array( $value ) ? array_values( array_unique( array_map( 'absint', $value ) ) ) : array();
			case 'boolean':
				return empty( $value ) ? '' : '1';
			case 'key':
				return is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
			case 'number':
				return $this->tf_sanitize_availability_number( $value );
			case 'status':
				return $this->tf_sanitize_availability_status( $value );
			case 'text_array':
				return is_array( $value ) ? Helper::tf_sanitize_recursive_input( $value ) : array();
			case 'text':
				return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
			default:
				return '';
		}
	}

	/**
	 * Validate an availability request against its concrete post.
	 *
	 * @param mixed  $post_id            Requested post ID.
	 * @param string $expected_post_type Expected Tourfic post type.
	 * @return int Authorized post ID.
	 */
	private function tf_authorize_availability_post( $post_id, $expected_post_type ) {
		$post_id = absint( $post_id );
		$post    = $post_id ? get_post( $post_id ) : null;

		if ( ! $post || $expected_post_type !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'You do not have permission to edit this availability.', 'tourfic' ),
				),
				403
			);
		}

		return $post_id;
	}

	/**
	 * Sanitize a non-negative availability price or capacity value.
	 *
	 * Empty values are retained so an existing date-specific value can be
	 * preserved by the rule merger.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private function tf_sanitize_availability_number( $value ) {
		$value = is_scalar( $value ) ? sanitize_text_field( wp_unslash( (string) $value ) ) : '';

		if ( '' === $value || ! is_numeric( $value ) || (float) $value < 0 ) {
			return '';
		}

		return $value;
	}

	/**
	 * Sanitize a nested availability input array.
	 *
	 * @param mixed $value Raw array.
	 * @return array
	 */
	private function tf_sanitize_availability_array( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		return Helper::tf_sanitize_recursive_input( wp_unslash( $value ) );
	}

	/**
	 * Normalize an availability rule status.
	 *
	 * @param mixed $status Raw status.
	 * @return string
	 */
	private function tf_sanitize_availability_status( $status ) {
		$status = is_scalar( $status ) ? sanitize_key( wp_unslash( (string) $status ) ) : '';

		return in_array( $status, array( 'available', 'unavailable' ), true ) ? $status : 'available';
	}

	/**
	 * Load files
	 * @author Foysal
	 */
	public function load_files() {
		// Metaboxes Class
		require_once $this->tf_options_file_path( 'classes/TF_Metabox.php' );
		// Settings Class
		require_once $this->tf_options_file_path( 'classes/TF_Settings.php' );
		//Shortcodes Class
		require_once $this->tf_options_file_path( 'classes/TF_Shortcodes.php' );
		//Taxonomy Class
		require_once $this->tf_options_file_path( 'classes/TF_Taxonomy_Metabox.php' );

		require_once $this->tf_options_file_path( 'fields/icon/fontawesome-4.php' );
		require_once $this->tf_options_file_path( 'fields/icon/fontawesome-5.php' );
		require_once $this->tf_options_file_path( 'fields/icon/fontawesome-6.php' );
		require_once $this->tf_options_file_path( 'fields/icon/remix-icon.php' );
	}

	/**
	 * Load metaboxes
	 * @author Foysal
	 */
	public function load_metaboxes() {
		$metaboxes = apply_filters(
			'tourfic_admin_metabox_files',
			glob( $this->tf_options_file_path( 'metaboxes/*.php' ) )
		);

		if ( ! empty( $metaboxes ) ) {
			foreach ( $metaboxes as $metabox ) {
				if ( file_exists( $metabox ) ) {
					require_once $metabox;
				}
			}
		}
	}

	/**
	 * Load Options
	 * @author Foysal
	 */
	public function load_options() {
		$options = apply_filters(
			'tourfic_admin_option_files',
			glob( $this->tf_options_file_path( 'options/*.php' ) )
		);

		if ( ! empty( $options ) ) {
			foreach ( $options as $option ) {
				if ( file_exists( $option ) ) {
					require_once $option;
				}
			}
		}
	}

	/**
	 * Load Taxonomy
	 * @author Foysal
	 */
	public function load_taxonomy() {
		$taxonomies = apply_filters(
			'tourfic_admin_taxonomy_files',
			glob( $this->tf_options_file_path( 'taxonomies/*.php' ) )
		);

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( file_exists( $taxonomy ) ) {
					require_once $taxonomy;
				}
			}
		}
	}

	/*
	 * Field Base
	 * @author Foysal
	 */
	public function field( $field, $value, $settings_id = '', $parent = '', $related_value = '' ) {
		if ( $field['type'] == 'repeater' ) {
			$id = ( ! empty( $settings_id ) ) ? $settings_id . '[' . $field['id'] . '][0]' . '[' . $field['id'] . ']' : $field['id'] . '[0]' . '[' . $field['id'] . ']';
		} else {
			$id = $settings_id . '[' . $field['id'] . ']';
		}

		$class = isset( $field['class'] ) ? $field['class'] : '';

		$tf_meta_box_dep_value = get_post_meta( get_the_ID(), $settings_id, true );


		$depend = '';
		if ( ! empty( $field['dependency'] ) ) {

			$dependency      = $field['dependency'];
			$depend_visible  = '';
			$data_controller = '';
			$data_condition  = '';
			$data_value      = '';
			$data_global     = '';

			if ( is_array( $dependency[0] ) ) {
				$data_controller = implode( '|', array_column( $dependency, 0 ) );
				$data_condition  = implode( '|', array_column( $dependency, 1 ) );
				$data_value      = implode( '|', array_column( $dependency, 2 ) );
				$data_global     = implode( '|', array_column( $dependency, 3 ) );
				$depend_visible  = implode( '|', array_column( $dependency, 4 ) );
			} else {
				$data_controller = ( ! empty( $dependency[0] ) ) ? $dependency[0] : '';
				$data_condition  = ( ! empty( $dependency[1] ) ) ? $dependency[1] : '';
				$data_value      = ( ! empty( $dependency[2] ) ) ? $dependency[2] : '';
				$data_global     = ( ! empty( $dependency[3] ) ) ? $dependency[3] : '';
				$depend_visible  = ( ! empty( $dependency[4] ) ) ? $dependency[4] : '';
			}

			$depend .= ' data-controller="' . esc_attr( $data_controller ) . '' . $parent . '"';
			$depend .= ' data-condition="' . esc_attr( $data_condition ) . '"';
			$depend .= ' data-value="' . esc_attr( $data_value ) . '"';
			$depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';

			$visible = ( ! empty( $depend_visible ) ) ? ' tf-depend-visible' : ' tf-depend-hidden';
		}

		//field width
		$field_width = isset( $field['field_width'] ) && ! empty( $field['field_width'] ) ? esc_attr( $field['field_width'] ) : '100';
		if ( $field_width == '100' ) {
			$field_style = 'width:100%;';
		} else {
			$field_style = 'width:calc(' . $field_width . '% - 10px);';
		}
		?>

        <div class="tf-field tf-field-<?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $class ); ?> <?php echo ! empty( $visible ) ? wp_kses_post( $visible ) : ''; ?>" <?php echo ! empty( $depend ) ? wp_kses_post( $depend ) : ''; ?>
             style="<?php echo esc_attr( $field_style ); ?>">

			<?php if ( ! empty( $field['label'] ) && $field['type']!='switch' && $field['type']!='accordion' && $field['type']!='heading' ){ ?>
                <label for="<?php echo esc_attr( $id ) ?>" class="tf-field-label">
					<?php echo esc_html( $field['label'] ) ?>
					<?php if ( ! empty( $field['subtitle'] ) ) : ?>
					<span class="tf-desc-tooltip">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_1017_4247)">
								<path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</g>
							<defs>
								<clipPath id="clip0_1017_4247">
								<rect width="16" height="16" fill="white"/>
								</clipPath>
							</defs>
						</svg>
						<div class="tf-desc-tooltip-content">
							<?php echo wp_kses_post( $field['subtitle'] ) ?>
						</div>
					</span>
					<?php endif; ?>
				</label>
				<?php if ( $field['type']=='repeater' ){ ?>
				<?php if ( ! empty( $field['description'] ) ): ?>
					<span class="tf-field-sub-title tf-field-repeater-desc"><?php echo wp_kses_post( $field['description'] ) ?></span>
				<?php endif; } ?>
			<?php } ?>

            <div class="tf-fieldset">
				<?php
				$fieldClass = 'Tourfic_' . $field['type'];
				if ( class_exists( $fieldClass ) ) {
					$_field = new $fieldClass( $field, $value, $settings_id, $parent, $related_value );
					$_field->render();
				} else {
					echo '<p>' . esc_html__( 'Field not found!', 'tourfic' ) . '</p>';
				}
				?>
            </div>

			<?php if ( $field['type']!='repeater' ){ ?>
			<?php if ( ! empty( $field['description'] ) ): ?>
                <span class="tf-field-sub-title"><?php echo wp_kses_post( $field['description'] ) ?></span>
			<?php endif; } ?>
        </div>
		<?php
	}

	function get_icon_list() {
		$icons = array(
			'all'           => array(
				'label'      => esc_html__( 'All Icons', 'tourfic' ),
				'label_icon' => 'ri-grid-fill',
				'icons'      => array_merge( tourfic_fontawesome_four_icons(), tourfic_fontawesome_five_icons(), tourfic_fontawesome_six_icons(), tourfic_remix_icon() ),
			),
			'fontawesome_4' => array(
				'label'      => esc_html__( 'Font Awesome 4', 'tourfic' ),
				'label_icon' => 'fa-regular fa-font-awesome',
				'icons'      => tourfic_fontawesome_four_icons(),
			),
			'fontawesome_5' => array(
				'label'      => esc_html__( 'Font Awesome 5', 'tourfic' ),
				'label_icon' => 'fa-regular fa-font-awesome',
				'icons'      => tourfic_fontawesome_five_icons(),
			),
			'fontawesome_6' => array(
				'label'      => esc_html__( 'Font Awesome 6', 'tourfic' ),
				'label_icon' => 'fa-regular fa-font-awesome',
				'icons'      => tourfic_fontawesome_six_icons(),
			),
			'remixicon'     => array(
				'label'      => esc_html__( 'Remix Icon', 'tourfic' ),
				'label_icon' => 'ri-remixicon-line',
				'icons'      => tourfic_remix_icon(),
			),
		);

		$icons = apply_filters( 'tourfic_icon_list', $icons );

		return $icons;
	}

	function tf_load_more_icons() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$start_index = isset( $_POST['start_index'] ) ? intval( $_POST['start_index'] ) : 0;
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash($_POST['type']) ) : 'all';
		$search      = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash($_POST['search']) ) : '';
		$icon_list   = $this->get_icon_list();
		$icons       = array_slice( $icon_list[ $type ]['icons'], $start_index, 100 );

		if ( ! empty( $search ) ) {
			$icons = array_filter( $icons, function ( $icon ) use ( $search ) {
				return strpos( $icon, $search ) !== false;
			} );
		}

		$icons_html = '';
		foreach ( $icons as $key => $icon ) {
			$icons_html .= '<li data-icon="' . esc_attr( $icon ) . '">
                            <div class="tf-icon-inner">
                                <i title="' . esc_attr( $icon ) . '" class="tf-main-icon ' . esc_attr( $icon ) . '"></i>
                                <span class="check-icon">
                                    <i class="ri-check-line"></i>
                                </span>
                            </div>
                        </li>';
		}

		wp_send_json_success( $icons_html );
	}

	function tf_icon_search() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$search_text = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash($_POST['search']) ) : '';
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash($_POST['type']) ) : 'all';
		$icon_list   = $this->get_icon_list();
		$icons       = $icon_list[ $type ]['icons'];

		$icons = array_filter( $icons, function ( $icon ) use ( $search_text ) {
			return strpos( $icon, $search_text ) !== false;
		} );

		$icons_html = '';
		foreach ( $icons as $key => $icon ) {
			$icons_html .= '<li data-icon="' . esc_attr( $icon ) . '">
                            <div class="tf-icon-inner">
                                <i title="' . esc_attr( $icon ) . '" class="tf-main-icon ' . esc_attr( $icon ) . '"></i>
                                <span class="check-icon">
                                    <i class="ri-check-line"></i>
                                </span>
                            </div>
                        </li>';
		}

		wp_send_json_success( array(
			'html'  => $icons_html,
			'count' => count( $icons )
		) );
	}

	/**
	 * Room availability calendar update
	 * @author Foysal
	 */
	function tf_add_hotel_room_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$room_id = $this->tf_authorize_availability_post(
			isset( $_POST['room_id'] ) ? absint( wp_unslash( $_POST['room_id'] ) ) : 0,
			'tf_room'
		); //phpcs:ignore
		$date_format = ! empty( Helper::tfopt( 'tf-date-format-for-users' ) ) ? Helper::tfopt( 'tf-date-format-for-users' ) : 'Y/m/d';
		$new_post    = isset( $_POST['new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['new_post'] ) ) : '';
		$check_in    = isset( $_POST['tf_room_check_in'] ) ? sanitize_text_field( wp_unslash( $_POST['tf_room_check_in'] ) ) : '';
		$check_out   = isset( $_POST['tf_room_check_out'] ) ? sanitize_text_field( wp_unslash( $_POST['tf_room_check_out'] ) ) : '';
		$status      = isset( $_POST['tf_room_status'] ) ? $this->tf_sanitize_availability_status( sanitize_key( wp_unslash( $_POST['tf_room_status'] ) ) ) : 'available';
		$price       = isset( $_POST['tf_room_price'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_room_price'] ) ) ) : '';
		$avail_date  = isset( $_POST['avail_date'] ) && is_string( $_POST['avail_date'] ) ? wp_unslash( $_POST['avail_date'] ) : '';
		$room_meta   = get_post_meta( $room_id, 'tf_room_opt', true );
		$room_meta   = is_array( $room_meta ) ? $room_meta : array();

		if ( '' === $check_in || '' === $check_out ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' ),
				)
			);
		}

		$check_in_timestamp  = strtotime( $this->tf_convert_date_format( $check_in, $date_format ) );
		$check_out_timestamp = strtotime( $this->tf_convert_date_format( $check_out, $date_format ) );

		if ( false === $check_in_timestamp || false === $check_out_timestamp || $check_in_timestamp > $check_out_timestamp ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'Check in date must be less than or equal to check out date.', 'tourfic' ),
				)
			);
		}

		$existing_availability = 'true' === $new_post
			? $this->tf_safe_json_decode_assoc( $avail_date )
			: $this->tf_safe_json_decode_assoc( $room_meta['avail_date'] ?? array(), true );
		$request_data          = $this->tf_sanitize_availability_request_data( 'room', $room_meta, $room_id );
		$rule_type             = (string) apply_filters( 'tourfic_room_availability_rule_type', '1', $room_meta, $room_id, $request_data );
		$rule_type             = sanitize_key( $rule_type );
		$updated_availability  = array();

		for ( $timestamp = $check_in_timestamp; $timestamp <= $check_out_timestamp; $timestamp = strtotime( '+1 day', $timestamp ) ) {
			$date          = gmdate( 'Y/m/d', $timestamp );
			$existing_rule = isset( $existing_availability[ $date ] ) && is_array( $existing_availability[ $date ] )
				? $existing_availability[ $date ]
				: array();
			$core_rule     = array(
				'check_in'  => $date,
				'check_out' => $date,
				'price_by'  => $rule_type,
				'price'     => $price,
				'status'    => $status,
			);
			$rule          = Availability::merge_rule_prices( array_merge( $existing_rule, $core_rule ), $existing_rule );
			$rule          = apply_filters(
				'tourfic_room_availability_rule_data',
				$rule,
				$request_data,
				$room_meta,
				$existing_rule,
				$room_id
			);
			$rule          = is_array( $rule ) ? $this->tf_sanitize_availability_rule( $rule, true ) : $core_rule;
			$rule['check_in']  = $date;
			$rule['check_out'] = $date;
			$rule['price_by']  = $rule_type;
			$rule['status']    = $this->tf_sanitize_availability_status( $rule['status'] ?? $status );

			$updated_availability[ $date ] = $rule;
		}

		$updated_availability = array_merge( $existing_availability, $updated_availability );

		if ( 'true' !== $new_post ) {
			$room_meta['avail_date'] = wp_json_encode( $updated_availability );
			update_post_meta( $room_id, 'tf_room_opt', $room_meta );
		}

		wp_send_json_success(
			array(
				'status'     => true,
				'message'    => esc_html__( 'Availability updated successfully.', 'tourfic' ),
				'avail_date' => wp_json_encode( $updated_availability ),
			)
		);
	}

	/*
     * Get room availability calendar
     * @author Foysal
     */
	function tf_get_hotel_room_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$room_id = $this->tf_authorize_availability_post(
			isset( $_POST['room_id'] ) ? absint( wp_unslash( $_POST['room_id'] ) ) : 0, 
			'tf_room'
		);
		$new_post   = isset( $_POST['new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['new_post'] ) ) : '';
		$avail_date = isset( $_POST['avail_date'] ) && is_string( $_POST['avail_date'] ) ? wp_unslash( $_POST['avail_date'] ) : '';
		$option_arr = isset( $_POST['option_arr'] ) && is_array( $_POST['option_arr'] ) ? map_deep( wp_unslash( $_POST['option_arr'] ), 'sanitize_text_field' ) : array();
		$room_meta  = get_post_meta( $room_id, 'tf_room_opt', true );
		$room_meta  = is_array( $room_meta ) ? $room_meta : array();

		$availability = 'true' === $new_post
			? $this->tf_safe_json_decode_assoc( $avail_date )
			: $this->tf_safe_json_decode_assoc( $room_meta['avail_date'] ?? array(), true );
		$events       = array();

		foreach ( $availability as $rule ) {
			if ( ! is_array( $rule ) || empty( $rule['check_in'] ) ) {
				continue;
			}

			$event          = $rule;
			$event['start'] = gmdate( 'Y-m-d', strtotime( $rule['check_in'] ) );
			$event['title'] = esc_html__( 'Price: ', 'tourfic' ) . wc_price( $rule['price'] ?? '' );
			$event          = apply_filters( 'tourfic_room_availability_calendar_event', $event, $room_meta, $room_id );

			if ( ! is_array( $event ) ) {
				continue;
			}

			$event['title'] = wp_kses_post( $event['title'] ?? '' );
			if ( 'unavailable' === ( $rule['status'] ?? '' ) ) {
				$event['display'] = 'background';
				$event['color']   = '#003c79';
			}

			$events[] = $event;
		}

		$editor_html = (string) apply_filters(
			'tourfic_room_availability_editor_html',
			'',
			$room_meta,
			$option_arr,
			$room_id
		);

		wp_send_json(
			array(
				'avail_data'   => $events,
				'options_html' => $editor_html,
			)
		);
	}

	/*
     * Update room avail_date price based on pricing type
     * @auther Foysal
     */
	function tf_update_room_avail_date_price( $post_id, $post ) {
		if ( ! $post || 'tf_room' !== $post->post_type || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$room = get_post_meta( $post_id, 'tf_room_opt', true );
		if ( ! is_array( $room ) || '1' !== (string) ( $room['avil_by_date'] ?? '' ) ) {
			return;
		}

		$rule_type    = sanitize_key( (string) apply_filters( 'tourfic_room_availability_rule_type', '1', $room, $post_id ) );
		$base_price   = $this->tf_sanitize_availability_number( $room['price'] ?? '' );
		$availability = $this->tf_safe_json_decode_assoc( $room['avail_date'] ?? array(), true );

		if ( empty( $availability ) ) {
			for ( $offset = 0; $offset <= 500; $offset++ ) {
				$date = gmdate( 'Y/m/d', strtotime( "+{$offset} day" ) );
				$rule = array(
					'check_in'  => $date,
					'check_out' => $date,
					'price_by'  => $rule_type,
					'price'     => $base_price,
					'status'    => 'available',
				);
				$rule = apply_filters( 'tourfic_room_availability_default_rule_data', $rule, $room, array(), $post_id );

				$availability[ $date ] = is_array( $rule ) ? $this->tf_sanitize_availability_rule( $rule, true ) : array();
			}
		} else {
			foreach ( $availability as $date => $rule ) {
				if ( ! is_array( $rule ) ) {
					continue;
				}

				if ( ! array_key_exists( 'price', $rule ) ) {
					$rule['price'] = $base_price;
				}
				$rule['price_by'] = $rule_type;
				$rule = apply_filters( 'tourfic_room_availability_default_rule_data', $rule, $room, $availability[ $date ], $post_id );

				$availability[ $date ] = is_array( $rule )
					? $this->tf_sanitize_availability_rule( $rule, true )
					: $availability[ $date ];
			}
		}

		$room['avail_date'] = wp_json_encode( $availability );
		update_post_meta( $post_id, 'tf_room_opt', $room );
	}

	/*
     * Reset room availability calendar
     * @auther Foysal
     */
	function tf_reset_room_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$room_id = $this->tf_authorize_availability_post(
			isset( $_POST['room_id'] ) ? absint( wp_unslash( $_POST['room_id'] ) ) : 0, 
			'tf_room'
		);
		$room_data = get_post_meta( $room_id, 'tf_room_opt', true );
		$room_data = is_array( $room_data ) ? $room_data : array();

		$room_data['avail_date'] = wp_json_encode( [] );

		update_post_meta( $room_id, 'tf_room_opt', $room_data );
		wp_send_json_success(
			array(
				'status'     => true,
				'message'    => esc_html__( 'Availability reset successfully.', 'tourfic' ),
				'avail_date' => wp_json_encode( array() ),
			)
		);
	}

	/*
	 * Apartment availability calendar update
	 * @auther Foysal
	 */
	function tf_add_apartment_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$apartment_id = $this->tf_authorize_availability_post(
			isset( $_POST['apartment_id'] ) ? absint( wp_unslash( $_POST['apartment_id'] ) ) : 0,
			'tf_apartment'
		);
		$date_format      = ! empty( Helper::tfopt( 'tf-date-format-for-users' ) ) ? Helper::tfopt( 'tf-date-format-for-users' ) : 'Y/m/d';
		$new_post         = isset( $_POST['new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['new_post'] ) ) : '';
		$check_in         = isset( $_POST['tf_apt_check_in'] ) ? sanitize_text_field( wp_unslash( $_POST['tf_apt_check_in'] ) ) : '';
		$check_out        = isset( $_POST['tf_apt_check_out'] ) ? sanitize_text_field( wp_unslash( $_POST['tf_apt_check_out'] ) ) : '';
		$status           = isset( $_POST['tf_apt_status'] ) ? $this->tf_sanitize_availability_status( sanitize_key( wp_unslash( $_POST['tf_apt_status'] ) ) ) : 'available';
		$price            = isset( $_POST['tf_apt_price'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_apt_price'] ) ) ) : '';
		$posted_rules     = isset( $_POST['apt_availability'] ) && is_string( $_POST['apt_availability'] ) ? wp_unslash( $_POST['apt_availability'] ) : '';
		$apartment_meta   = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		$apartment_meta   = is_array( $apartment_meta ) ? $apartment_meta : array();

		if ( '' === $check_in || '' === $check_out ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' ),
				)
			);
		}

		$check_in_timestamp  = strtotime( $this->tf_convert_date_format( $check_in, $date_format ) );
		$check_out_timestamp = strtotime( $this->tf_convert_date_format( $check_out, $date_format ) );

		if ( false === $check_in_timestamp || false === $check_out_timestamp || $check_in_timestamp > $check_out_timestamp ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'Check in date must be less than or equal to check out date.', 'tourfic' ),
				)
			);
		}

		$existing_availability = 'true' === $new_post
			? $this->tf_safe_json_decode_assoc( $posted_rules )
			: $this->tf_safe_json_decode_assoc( $apartment_meta['apt_availability'] ?? array(), true );
		$request_data          = $this->tf_sanitize_availability_request_data( 'apartment', $apartment_meta, $apartment_id );
		$rule_type             = sanitize_key(
			(string) apply_filters( 'tourfic_apartment_availability_rule_type', 'per_night', $apartment_meta, $apartment_id, $request_data )
		);
		$updated_availability  = array();

		for ( $timestamp = $check_in_timestamp; $timestamp <= $check_out_timestamp; $timestamp = strtotime( '+1 day', $timestamp ) ) {
			$date          = gmdate( 'Y/m/d', $timestamp );
			$existing_rule = isset( $existing_availability[ $date ] ) && is_array( $existing_availability[ $date ] )
				? $existing_availability[ $date ]
				: array();
			$core_rule     = array(
				'check_in'     => $date,
				'check_out'    => $date,
				'pricing_type' => $rule_type,
				'price'        => $price,
				'status'       => $status,
			);
			$rule          = Availability::merge_rule_prices( array_merge( $existing_rule, $core_rule ), $existing_rule );
			$rule          = apply_filters(
				'tourfic_apartment_availability_rule_data',
				$rule,
				$request_data,
				$apartment_meta,
				$existing_rule,
				$apartment_id
			);
			$rule          = is_array( $rule ) ? $this->tf_sanitize_availability_rule( $rule, true ) : $core_rule;
			$rule['check_in']     = $date;
			$rule['check_out']    = $date;
			$rule['pricing_type'] = $rule_type;
			$rule['status']       = $this->tf_sanitize_availability_status( $rule['status'] ?? $status );

			$updated_availability[ $date ] = $rule;
		}

		$updated_availability              = array_merge( $existing_availability, $updated_availability );
		$apartment_meta['apt_availability'] = wp_json_encode( $updated_availability );
		update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_meta );

		wp_send_json_success(
			array(
				'status'           => true,
				'message'          => esc_html__( 'Availability updated successfully.', 'tourfic' ),
				'apt_availability' => wp_json_encode( $updated_availability ),
			)
		);
	}

	/*
     * Get apartment availability calendar
     * @auther Foysal
     */
	function tf_get_apartment_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$apartment_id = $this->tf_authorize_availability_post(
			isset( $_POST['apartment_id'] ) ? absint( wp_unslash( $_POST['apartment_id'] ) ) : 0,
			'tf_apartment'
		);
		$new_post     = isset( $_POST['new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['new_post'] ) ) : '';
		$posted_rules = isset( $_POST['apt_availability'] ) && is_string( $_POST['apt_availability'] ) ? wp_unslash( $_POST['apt_availability'] ) : '';
		$meta         = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		$meta         = is_array( $meta ) ? $meta : array();
		$availability = 'true' === $new_post
			? $this->tf_safe_json_decode_assoc( $posted_rules )
			: $this->tf_safe_json_decode_assoc( $meta['apt_availability'] ?? array(), true );
		$events       = array();

		foreach ( $availability as $rule ) {
			if ( ! is_array( $rule ) || empty( $rule['check_in'] ) ) {
				continue;
			}

			$event          = $rule;
			$event['start'] = gmdate( 'Y-m-d', strtotime( $rule['check_in'] ) );
			$event['title'] = esc_html__( 'Price: ', 'tourfic' ) . wc_price( $rule['price'] ?? '' );
			$event          = apply_filters( 'tourfic_apartment_availability_calendar_event', $event, $meta, $apartment_id );

			if ( ! is_array( $event ) ) {
				continue;
			}

			$event['title'] = wp_kses_post( $event['title'] ?? '' );
			if ( 'unavailable' === ( $rule['status'] ?? '' ) ) {
				$event['display'] = 'background';
				$event['color']   = '#003c79';
			}

			$events[] = $event;
		}

		wp_send_json( $events );
	}

	/*
     * Reset apartment availability calendar
     * @auther Foysal
     */
	function tf_reset_apt_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$apartment_id = $this->tf_authorize_availability_post(
			isset( $_POST['apartment_id'] ) ? absint( wp_unslash( $_POST['apartment_id'] ) ) : 0,
			'tf_apartment'
		);
		$apartment_data = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		$apartment_data = is_array( $apartment_data ) ? $apartment_data : array();
		
		$apartment_data['apt_availability'] = wp_json_encode( [] );

		update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_data );
		wp_send_json_success(
			array(
				'status'           => true,
				'message'          => esc_html__( 'Availability reset successfully.', 'tourfic' ),
				'apt_availability' => wp_json_encode( array() ),
			)
		);
	}

	/*
	 * Tour availability calendar update
	 * @auther Jahid
	 */
	function tf_add_tour_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$tour_id = $this->tf_authorize_availability_post(
			isset( $_POST['tour_id'] ) ? absint( wp_unslash( $_POST['tour_id'] ) ) : 0,
			'tf_tours'
		);
		$date_format       = ! empty( Helper::tfopt( 'tf-date-format-for-users' ) ) ? Helper::tfopt( 'tf-date-format-for-users' ) : 'Y/m/d';
		$new_post          = isset( $_POST['new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['new_post'] ) ) : '';
		$check_in          = isset( $_POST['tf_tour_check_in'] ) ? sanitize_text_field( wp_unslash( $_POST['tf_tour_check_in'] ) ) : '';
		$check_out         = isset( $_POST['tf_tour_check_out'] ) ? sanitize_text_field( wp_unslash( $_POST['tf_tour_check_out'] ) ) : '';
		$status            = isset( $_POST['tf_tour_status'] ) ? $this->tf_sanitize_availability_status( sanitize_key( wp_unslash( $_POST['tf_tour_status'] ) ) ) : 'available';
		$adult_price       = isset( $_POST['tf_tour_adult_price'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_tour_adult_price'] ) ) ) : '';
		$child_price       = isset( $_POST['tf_tour_child_price'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_tour_child_price'] ) ) ) : '';
		$infant_price      = isset( $_POST['tf_tour_infant_price'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_tour_infant_price'] ) ) ) : '';
		$min_person        = isset( $_POST['tf_tour_min_person'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_tour_min_person'] ) ) ) : '';
		$max_person        = isset( $_POST['tf_tour_max_person'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_tour_max_person'] ) ) ) : '';
		$max_capacity      = isset( $_POST['tf_tour_max_capacity'] ) ? $this->tf_sanitize_availability_number( sanitize_text_field( wp_unslash( $_POST['tf_tour_max_capacity'] ) ) ) : '';
		$posted_rules      = isset( $_POST['tour_availability'] ) && is_string( $_POST['tour_availability'] ) ? wp_unslash( $_POST['tour_availability'] ) : '';
		$is_bulk_edit      = (bool) filter_input( INPUT_POST, 'bulk_edit_option', FILTER_VALIDATE_BOOLEAN );
		$meta              = get_post_meta( $tour_id, 'tf_tours_opt', true );
		$meta              = is_array( $meta ) ? $meta : array();
		$request_data      = $this->tf_sanitize_availability_request_data( 'tour', $meta, $tour_id );
		$rule_type         = sanitize_key( (string) apply_filters( 'tourfic_tour_availability_rule_type', 'person', $meta, $tour_id, $request_data ) );

		if ( '' === $adult_price ) {
			$adult_price = $this->tf_sanitize_availability_number( $meta['adult_price'] ?? '' );
		}
		if ( '' === $child_price ) {
			$child_price = $this->tf_sanitize_availability_number( $meta['child_price'] ?? '' );
		}
		if ( '' === $infant_price ) {
			$infant_price = $this->tf_sanitize_availability_number( $meta['infant_price'] ?? '' );
		}

		$existing_availability = 'true' === $new_post
			? $this->tf_safe_json_decode_assoc( $posted_rules )
			: $this->tf_safe_json_decode_assoc( $meta['tour_availability'] ?? array(), true );
		$updated_availability  = array();
		$build_rule            = function( $start_date, $end_date ) use (
			$adult_price,
			$child_price,
			$existing_availability,
			$infant_price,
			$max_capacity,
			$max_person,
			$meta,
			$min_person,
			$request_data,
			$rule_type,
			$status,
			$tour_id
		) {
			$rule_key      = $start_date . ' - ' . $end_date;
			$existing_rule = isset( $existing_availability[ $rule_key ] ) && is_array( $existing_availability[ $rule_key ] )
				? $existing_availability[ $rule_key ]
				: array();
			$core_rule     = array(
				'check_in'     => $start_date,
				'check_out'    => $end_date,
				'pricing_type' => $rule_type,
				'adult_price'  => $adult_price,
				'child_price'  => $child_price,
				'infant_price' => $infant_price,
				'min_person'   => $min_person,
				'max_person'   => $max_person,
				'max_capacity' => $max_capacity,
				'status'       => $status,
			);
			$rule          = array_merge( $existing_rule, $core_rule );
			$rule          = apply_filters(
				'tourfic_tour_availability_rule_data',
				$rule,
				$request_data,
				$meta,
				$existing_rule,
				$tour_id
			);
			$rule          = is_array( $rule ) ? $this->tf_sanitize_availability_rule( $rule, true ) : $core_rule;
			$rule['check_in']     = $start_date;
			$rule['check_out']    = $end_date;
			$rule['pricing_type'] = $rule_type;
			$rule['status']       = $this->tf_sanitize_availability_status( $rule['status'] ?? $status );

			return array( $rule_key, $rule );
		};

		if ( $is_bulk_edit ) {
			$months      = isset( $_POST['tf_tour_repeat_month'] ) && is_array( $_POST['tf_tour_repeat_month'] ) ? array_values( array_unique( array_map( 'absint', wp_unslash( $_POST['tf_tour_repeat_month'] ) ) ) ) : array();
			$years       = isset( $_POST['tf_tour_repeat_year'] ) && is_array( $_POST['tf_tour_repeat_year'] ) ? array_values( array_unique( array_map( 'absint', wp_unslash( $_POST['tf_tour_repeat_year'] ) ) ) ) : array();
			$repeat_days = isset( $_POST['tf_tour_repeat_day'] ) && is_array( $_POST['tf_tour_repeat_day'] ) ? array_map( 'absint', wp_unslash( $_POST['tf_tour_repeat_day'] ) ) : array();
			$weekdays    = isset( $_POST['tf_tour_repeat_week'] ) && is_array( $_POST['tf_tour_repeat_week'] ) ? array_map( 'absint', wp_unslash( $_POST['tf_tour_repeat_week'] ) ) : array();
			$months      = array_values( array_filter( $months, static function( $month ) {
				return $month >= 1 && $month <= 12;
			} ) );
			$years       = array_values( array_filter( $years, static function( $year ) {
				return $year >= 1970 && $year <= 2100;
			} ) );

			if ( empty( $months ) || empty( $years ) ) {
				wp_send_json_error(
					array(
						'status'  => false,
						'message' => esc_html__( 'Please select valid months and years.', 'tourfic' ),
					)
				);
			}

			foreach ( $years as $year ) {
				foreach ( $months as $month ) {
					$days = $this->tf_get_tour_bulk_edit_days( $month, $year, $repeat_days, $weekdays );
					foreach ( $days as $day ) {
						$timestamp = strtotime( sprintf( '%04d-%02d-%02d', $year, $month, $day ) );
						if ( false === $timestamp ) {
							continue;
						}

						$date                  = gmdate( 'Y/m/d', $timestamp );
						list( $key, $rule )    = $build_rule( $date, $date );
						$updated_availability[ $key ] = $rule;
					}
				}
			}
		} else {
			if ( '' === $check_in || '' === $check_out ) {
				wp_send_json_error(
					array(
						'status'  => false,
						'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' ),
					)
				);
			}

			$check_in_timestamp  = strtotime( $this->tf_convert_date_format( $check_in, $date_format ) );
			$check_out_timestamp = strtotime( $this->tf_convert_date_format( $check_out, $date_format ) );
			if ( false === $check_in_timestamp || false === $check_out_timestamp || $check_in_timestamp > $check_out_timestamp ) {
				wp_send_json_error(
					array(
						'status'  => false,
						'message' => esc_html__( 'Check in date must be less than or equal to check out date.', 'tourfic' ),
					)
				);
			}

			$start_date = gmdate( 'Y/m/d', $check_in_timestamp );
			$end_date   = gmdate( 'Y/m/d', $check_out_timestamp );
			list( $key, $rule ) = $build_rule( $start_date, $end_date );
			$updated_availability[ $key ] = $rule;
		}

		$updated_availability       = array_merge( $existing_availability, $updated_availability );
		$meta['tour_availability'] = wp_json_encode( $updated_availability );
		update_post_meta( $tour_id, 'tf_tours_opt', $meta );

		wp_send_json_success(
			array(
				'status'            => true,
				'message'           => esc_html__( 'Availability updated successfully.', 'tourfic' ),
				'tour_availability' => wp_json_encode( $updated_availability ),
			)
		);
	}

	/*
     * Get tour availability calendar
     * @auther Jahid
     */
	function tf_get_tour_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$tour_id = $this->tf_authorize_availability_post(
			isset( $_POST['tour_id'] ) ? absint(wp_unslash( $_POST['tour_id'] ) ) : 0,
			'tf_tours'
		);
		$new_post        = isset( $_POST['new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['new_post'] ) ) : '';
		$posted_rules    = isset( $_POST['tour_availability'] ) && is_string( $_POST['tour_availability'] ) ? wp_unslash( $_POST['tour_availability'] ) : '';
		$option_arr      = isset( $_POST['option_arr'] ) && is_array( $_POST['option_arr'] ) ? map_deep( wp_unslash( $_POST['option_arr'] ), 'sanitize_text_field' ) : array();
		$group_option_arr = isset( $_POST['group_option_arr'] ) && is_array( $_POST['group_option_arr'] ) ? map_deep( wp_unslash( $_POST['group_option_arr'] ), 'sanitize_text_field' ) : array();
		$meta            = get_post_meta( $tour_id, 'tf_tours_opt', true );
		$meta            = is_array( $meta ) ? $meta : array();
		$availability    = 'true' === $new_post
			? $this->tf_safe_json_decode_assoc( $posted_rules )
			: $this->tf_safe_json_decode_assoc( $meta['tour_availability'] ?? array(), true );
		$events          = array();

		foreach ( $availability as $rule ) {
			if ( ! is_array( $rule ) || empty( $rule['check_in'] ) || empty( $rule['check_out'] ) ) {
				continue;
			}

			$event          = $rule;
			$event['start'] = gmdate( 'Y-m-d', strtotime( $rule['check_in'] ) );
			$event['end']   = gmdate( 'Y-m-d', strtotime( $rule['check_out'] . ' +1 day' ) );
			$event['title'] = esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $rule['adult_price'] ?? '' )
				. '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $rule['child_price'] ?? '' )
				. '<br>' . esc_html__( 'Infant: ', 'tourfic' ) . wc_price( $rule['infant_price'] ?? '' );

			$event = apply_filters( 'tourfic_tour_availability_calendar_event', $event, $meta, $tour_id );
			if ( ! is_array( $event ) ) {
				continue;
			}

			$event['title'] = wp_kses_post( $event['title'] ?? '' );
			if ( 'unavailable' === ( $rule['status'] ?? '' ) ) {
				$event['customClass'] = 'tf_tour_disable_date';
			}

			$events[] = $event;
		}

		$editor_html = (string) apply_filters(
			'tourfic_tour_availability_editor_html',
			'',
			$meta,
			$option_arr,
			$group_option_arr,
			$tour_id
		);

		wp_send_json(
			array(
				'avail_data'   => $events,
				'options_html' => $editor_html,
			)
		);
	}

	/*
     * Reset tour availability calendar
     * @auther Jahid
     */
	function tf_reset_tour_availability() {
		check_ajax_referer( 'updates', '_nonce' );

		$tour_id = $this->tf_authorize_availability_post(
			isset( $_POST['tour_id'] ) ? absint(wp_unslash( $_POST['tour_id'] )) : 0,
			'tf_tours'
		);
		$tour_data = get_post_meta( $tour_id, 'tf_tours_opt', true );
		$tour_data = is_array( $tour_data ) ? $tour_data : array();
		
		$tour_data['tour_availability'] = wp_json_encode( [] );
		update_post_meta( $tour_id, 'tf_tours_opt', $tour_data );
		wp_send_json_success(
			array(
				'status'            => true,
				'message'           => esc_html__( 'Availability reset successfully.', 'tourfic' ),
				'tour_availability' => wp_json_encode( array() ),
			)
		);
	}
	/*
     * Update apt_availability price based on pricing type
     * @auther Foysal
     */
	function tf_update_apt_availability_price( $post_id, $post ) {
		if ( ! $post || 'tf_apartment' !== $post->post_type || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
		if ( ! is_array( $meta ) || '1' !== (string) ( $meta['enable_availability'] ?? '' ) ) {
			return;
		}

		$rule_type    = sanitize_key(
			(string) apply_filters( 'tourfic_apartment_availability_rule_type', 'per_night', $meta, $post_id )
		);
		$base_price   = $this->tf_sanitize_availability_number( $meta['price_per_night'] ?? '' );
		$availability = $this->tf_safe_json_decode_assoc( $meta['apt_availability'] ?? array(), true );

		if ( empty( $availability ) ) {
			$today = strtotime( gmdate( 'Y-m-d' ) );
			$end   = strtotime( '+5 years', $today );
			for ( $timestamp = $today; $timestamp <= $end; $timestamp = strtotime( '+1 day', $timestamp ) ) {
				$date = gmdate( 'Y/m/d', $timestamp );
				$rule = array(
					'check_in'     => $date,
					'check_out'    => $date,
					'pricing_type' => $rule_type,
					'price'        => $base_price,
					'status'       => 'available',
				);
				$rule = apply_filters( 'tourfic_apartment_availability_default_rule_data', $rule, $meta, array(), $post_id );

				$availability[ $date ] = is_array( $rule ) ? $this->tf_sanitize_availability_rule( $rule, true ) : array();
			}
		} else {
			foreach ( $availability as $date => $rule ) {
				if ( ! is_array( $rule ) ) {
					continue;
				}

				if ( ! array_key_exists( 'price', $rule ) ) {
					$rule['price'] = $base_price;
				}
				$rule['pricing_type'] = $rule_type;
				$rule = apply_filters( 'tourfic_apartment_availability_default_rule_data', $rule, $meta, $availability[ $date ], $post_id );

				$availability[ $date ] = is_array( $rule )
					? $this->tf_sanitize_availability_rule( $rule, true )
					: $availability[ $date ];
			}
		}

		$meta['apt_availability'] = wp_json_encode( $availability );
		update_post_meta( $post_id, 'tf_apartment_opt', $meta );
	}


	function tf_convert_date_format( $date, $currentFormat ) {
		$dateTime = \DateTime::createFromFormat( $currentFormat, $date );

		if ( $dateTime === false ) {
			return false;
		}

		return $dateTime->format( 'Y/m/d' );
	}

	/**
	 * Insert Category Data
	 *
	 * @author Jahid
	 */
	function tf_insert_category_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$categoryName = !empty($_POST['categoryName']) ? sanitize_title( wp_unslash($_POST['categoryName']) ) : '';
		$categoryTitle = !empty($_POST['categoryTitle']) ? sanitize_text_field( wp_unslash($_POST['categoryTitle']) ) : '';
		$parentCategory = !empty($_POST['parentCategory']) ? sanitize_key( wp_unslash($_POST['parentCategory']) ) : '';

		$response = [];
		if ( !empty($categoryName) && !empty($categoryTitle) ) {
			// Insert the term
			$term = wp_insert_term(
				$categoryTitle,   // The term
				$categoryName, // The taxonomy
				array(
					'slug'   => sanitize_title($categoryTitle),
					'parent' => !empty($parentCategory) ? intval($parentCategory) : ''
				)
			);
			$insert_Date = array(
				'id' => $term['term_id'],
				'title' => get_term_field('name', $term['term_id'], $categoryName)
			);

			$response ['insert_category'] = $insert_Date;
		}
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Delete Category Data
	 *
	 * @author Jahid
	 */
	function tf_delete_category_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$categoryName = !empty($_POST['categoryName']) ? sanitize_title( wp_unslash($_POST['categoryName']) ) : '';
		$term_id = !empty($_POST['term_id']) ? sanitize_text_field( wp_unslash($_POST['term_id']) ) : '';

		$response = [];

		if (!empty($term_id)) {
			$result = wp_delete_term($term_id, $categoryName); // Replace 'category' with your taxonomy if it's different

			if (!is_wp_error($result)) {
				$response['success'] = true;
			} else {
				$response['error'] = $result->get_error_message();
			}
		} else {
			$response['error'] = 'Invalid term ID.';
		}

		echo wp_json_encode($response);
		wp_die();
	}

	/**
	 * Insert Post Data
	 *
	 * @author Foysal
	 */
	function tf_insert_post_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$postType = !empty($_POST['postType']) ? sanitize_title( wp_unslash($_POST['postType']) ) : '';
		$postTitle = !empty($_POST['postTitle']) ? sanitize_text_field( wp_unslash($_POST['postTitle']) ) : '';
		$fieldId = !empty($_POST['fieldId']) ? sanitize_text_field( wp_unslash($_POST['fieldId']) ) : '';
		$postId = !empty($_POST['postId']) ? sanitize_text_field( wp_unslash($_POST['postId']) ) : '';

		$response = [];
		if ( !empty($postType) && !empty($postTitle) ) {
			// Insert the post
			$post_id = wp_insert_post(array(
				'post_type'    => $postType,
				'post_title'   => $postTitle,
				'post_status'  => 'publish'
			));

			if($fieldId == 'tf_rooms'){
				$room_meta['tf_hotel'] = $postId;
				update_post_meta($post_id, 'tf_room_opt', $room_meta);
			}

			$insert_Data = array(
				'id' => $post_id,
				'title' => get_the_title($post_id),
				'edit_url' => esc_url( get_edit_post_link( $post_id ) ),
			);

			$response ['insert_post'] = $insert_Data;
		}
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Delete Post Data
	 *
	 * @author Foysal
	 */
	function tf_delete_post_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$categoryName = !empty($_POST['categoryName']) ? sanitize_title( wp_unslash($_POST['categoryName']) ) : '';
		$term_id = !empty($_POST['term_id']) ? sanitize_text_field( wp_unslash($_POST['term_id']) ) : '';

		$response = [];

		if (!empty($term_id)) {
			$result = wp_delete_term($term_id, $categoryName); // Replace 'category' with your taxonomy if it's different

			if (!is_wp_error($result)) {
				$response['success'] = true;
			} else {
				$response['error'] = $result->get_error_message();
			}
		} else {
			$response['error'] = 'Invalid term ID.';
		}

		echo wp_json_encode($response);
		wp_die();
	}
}
