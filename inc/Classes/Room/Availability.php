<?php

namespace Tourfic\Classes\Room;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

/**
 * Room Availability Class
 *
 * This class handles room availability checking for a given listing ID and date range.
 * It checks both booked dates from WooCommerce orders and custom availability rules.
 *
 * @since 2.3.0
 * @package Tourfic/Classes/Room
 */
class Availability {

	/**
	 * Room post ID
	 *
	 * @var int
	 */
	private $room_id;

	/**
	 * Hotel post ID
	 *
	 * @var int
	 */
	private $hotel_id;

	/**
	 * Check-in date (Y-m-d format)
	 *
	 * @var string
	 */
	private $check_in;

	/**
	 * Check-out date (Y-m-d format)
	 *
	 * @var string
	 */
	private $check_out;

	/**
	 * Room metadata
	 *
	 * @var array
	 */
	private $room_meta = array();

	/**
	 * Hotel metadata
	 *
	 * @var array
	 */
	private $hotel_meta = array();

	/**
	 * Available rooms count
	 *
	 * @var int
	 */
	private $available_rooms = 0;

	/**
	 * Constructor
	 *
	 * @param int    $room_id   Room post ID
	 * @param string $check_in  Check-in date (can be Y-m-d or format from settings)
	 * @param string $check_out Check-out date (can be Y-m-d or format from settings)
	 */
	public function __construct( $room_id, $check_in, $check_out ) {
		$this->room_id   = intval( $room_id );
		$this->check_in  = $this->normalize_date( $check_in );
		$this->check_out = $this->normalize_date( $check_out );

		// Get room metadata
		$this->room_meta = get_post_meta( $this->room_id, 'tf_room_opt', true );

		// Get hotel ID and metadata
		$this->hotel_id = ! empty( $this->room_meta['tf_hotel'] ) ? intval( $this->room_meta['tf_hotel'] ) : 0;
		if ( $this->hotel_id ) {
			$this->hotel_meta = get_post_meta( $this->hotel_id, 'tf_hotels_opt', true );
		}

		// Set available rooms count - must be greater than 0
		$this->available_rooms = ! empty( $this->room_meta['num-room'] ) ? intval( $this->room_meta['num-room'] ) : 0;
    }

	/**
	 * Normalize date to Y-m-d format
	 *
	 * Converts dates from various formats (including site settings format) to Y-m-d.
	 *
	 * @param string $date The date string in any format
	 * @return string The date in Y-m-d format, or empty string if invalid
	 */
	private function normalize_date( $date ) {
		if ( empty( $date ) ) {
			return '';
		}

		$date = sanitize_text_field( $date );

		// If already in Y-m-d format, return as is
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			return $date;
		}

		// Get the site's date format setting
		$site_date_format = ! empty( Helper::tfopt( 'tf-date-format-for-users' ) ) ? Helper::tfopt( 'tf-date-format-for-users' ) : 'Y/m/d';

		// Try to parse the date with the site's format
		$date_obj = \DateTime::createFromFormat( $site_date_format, $date );

		if ( $date_obj && $date_obj->format( $site_date_format ) === $date ) {
			// Successfully parsed with site format
			return $date_obj->format( 'Y-m-d' );
		}

		// Try common date formats
		$formats = array(
			'Y/m/d',
			'd/m/Y',
			'm/d/Y',
			'Y-m-d',
			'd-m-Y',
			'm-d-Y',
			'Y.m.d',
			'd.m.Y',
		);

		foreach ( $formats as $format ) {
			$date_obj = \DateTime::createFromFormat( $format, $date );
			if ( $date_obj && $date_obj->format( $format ) === $date ) {
				return $date_obj->format( 'Y-m-d' );
			}
		}

		// If all parsing fails, return empty string (will be caught in validation)
		return '';
	}

	/**
	 * Check if room is available for the given date range
	 *
	 * This method checks both booked dates and custom availability rules.
	 *
	 * @return bool True if room is available, false otherwise
	 */
	public function is_available() {
		// If num-room is 0, room is not available
		if ( $this->available_rooms <= 0 ) {
			return false;
		}

		// Validate dates
		if ( ! $this->validate_dates() ) {
			return false;
		}

		// Check if room is booked during this period
		if ( $this->is_fully_booked() ) {
			return false;
		}

		// Check custom availability rules
		if ( $this->has_availability_rules() && ! $this->passes_availability_rules() ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate check-in and check-out dates
	 *
	 * @return bool True if dates are valid, false otherwise
	 */
	private function validate_dates() {
		// Check if dates are in valid format
		$check_in_time  = strtotime( $this->check_in );
		$check_out_time = strtotime( $this->check_out );

		if ( ! $check_in_time || ! $check_out_time ) {
			return false;
		}

		// Check if check-out is after check-in
		if ( $check_out_time <= $check_in_time ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if room is fully booked during the date range
	 *
	 * @return bool True if room is fully booked, false otherwise
	 */
	private function is_fully_booked() {
		$booked_dates = $this->get_booked_dates();

		if ( empty( $booked_dates ) ) {
			return false;
		}

		$check_in_time  = strtotime( $this->check_in );
		$check_out_time = strtotime( $this->check_out );

		// Track booked rooms per day
		$room_booked_per_day = array();

		foreach ( $booked_dates as $booking ) {
			$booking_check_in  = strtotime( $booking['check_in'] );
			$booking_check_out = strtotime( $booking['check_out'] );
			$rooms_booked      = intval( $booking['room'] );

			// Loop through each day in the booking period
			for ( $day = $booking_check_in; $day < $booking_check_out; $day = strtotime( '+1 day', $day ) ) {
				$date = date( 'Y-m-d', $day );

				if ( ! isset( $room_booked_per_day[ $date ] ) ) {
					$room_booked_per_day[ $date ] = 0;
				}

				$room_booked_per_day[ $date ] += $rooms_booked;
			}
		}

		// Check if all days in the requested period have rooms booked
		$check_period = new \DatePeriod(
			new \DateTime( $this->check_in ),
			new \DateInterval( 'P1D' ),
			(new \DateTime( $this->check_out ))
		);

		foreach ( $check_period as $date ) {
			$date_string = $date->format( 'Y-m-d' );

			// If this day has all rooms booked
			if ( isset( $room_booked_per_day[ $date_string ] ) && 
				 intval( $room_booked_per_day[ $date_string ] ) >= $this->available_rooms ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get booked dates for this room from WooCommerce orders
	 *
	 * @return array Array of booked date ranges with room count
	 */
	private function get_booked_dates() {
		$wc_orders = wc_get_orders( array(
			'post_status' => array( 'wc-completed' ),
			'limit'       => -1,
		) );

		$booked_days = array();

		foreach ( $wc_orders as $wc_order ) {
			$order_items = $wc_order->get_items();

			foreach ( $order_items as $item_id => $item ) {
				// Check both hotel and room post IDs
				$item_post_id = wc_get_order_item_meta( $item_id, '_post_id', true );

				if ( intval( $item_post_id ) === $this->hotel_id ) {
					$check_in  = wc_get_order_item_meta( $item_id, 'check_in', true );
					$check_out = wc_get_order_item_meta( $item_id, 'check_out', true );
					$room      = wc_get_order_item_meta( $item_id, 'number_room_booked', true );

					if ( ! empty( $check_in ) && ! empty( $check_out ) && ! empty( $room ) ) {
						$booked_days[] = array(
							'check_in'  => $check_in,
							'check_out' => $check_out,
							'room'      => $room,
						);
					}
				}
			}
		}

		return $booked_days;
	}

	/**
	 * Check if custom availability rules are enabled
	 *
	 * @return bool True if availability rules are configured, false otherwise
	 */
	private function has_availability_rules() {
		$enable_availability = ! empty( $this->room_meta['avil_by_date'] ) ? $this->room_meta['avil_by_date'] : false;

		return $enable_availability && function_exists( 'is_tf_pro' ) && is_tf_pro();
	}

	/**
	 * Check if the date range passes custom availability rules
	 *
	 * @return bool True if date range is available according to rules, false otherwise
	 */
	private function passes_availability_rules() {
		$room_availability_arr = self::normalize_availability_rules( $this->room_meta['avail_date'] ?? '' );

		if ( empty( $room_availability_arr ) ) {
			return true;
		}

		// Check each day in the requested period
		$check_period = new \DatePeriod(
			new \DateTime( $this->check_in ),
			new \DateInterval( 'P1D' ),
			(new \DateTime( $this->check_out ))
		);

		foreach ( $check_period as $date ) {
			$date_string = $date->format( 'Y/m/d' );

			if ( ! self::is_date_available_for_rules( $room_availability_arr, $date_string ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Normalize mixed availability storage into a flat array of rule arrays.
	 *
	 * @param mixed $room_availability Raw availability payload.
	 * @return array
	 */
	public static function normalize_availability_rules( $room_availability ) {
		if ( is_string( $room_availability ) ) {
			$decoded_rules = json_decode( $room_availability, true );
			$room_availability = is_array( $decoded_rules ) ? $decoded_rules : array();
		}

		if ( ! is_array( $room_availability ) ) {
			return array();
		}

		$normalized_rules = array();

		foreach ( $room_availability as $availability_rule ) {
			if ( is_string( $availability_rule ) ) {
				$decoded_rule = json_decode( $availability_rule, true );

				if ( is_array( $decoded_rule ) ) {
					if ( self::is_associative_rule( $decoded_rule ) ) {
						$normalized_rules[] = $decoded_rule;
					} else {
						foreach ( $decoded_rule as $nested_rule ) {
							if ( is_array( $nested_rule ) ) {
								$normalized_rules[] = $nested_rule;
							}
						}
					}
				}

				continue;
			}

			if ( is_array( $availability_rule ) ) {
				$normalized_rules[] = $availability_rule;
			}
		}

		return $normalized_rules;
	}

	/**
	 * Check whether the rules contain explicit available dates.
	 *
	 * @param mixed $room_availability Raw or normalized availability rules.
	 * @return bool
	 */
	public static function has_explicit_available_rules( $room_availability ) {
		$room_availability = self::normalize_availability_rules( $room_availability );

		foreach ( $room_availability as $availability_rule ) {
			if ( 'unavailable' !== self::get_rule_status( $availability_rule ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether a specific date is available under the current rule set.
	 *
	 * @param mixed  $room_availability Raw or normalized availability rules.
	 * @param string $date_string       Date in `Y/m/d` format.
	 * @return bool
	 */
	public static function is_date_available_for_rules( $room_availability, $date_string ) {
		$room_availability = self::normalize_availability_rules( $room_availability );

		if ( empty( $room_availability ) ) {
			return true;
		}

		$has_explicit_available_rules = self::has_explicit_available_rules( $room_availability );
		$has_available_match          = false;

		foreach ( $room_availability as $availability_rule ) {
			if ( ! self::rule_matches_date( $availability_rule, $date_string ) ) {
				continue;
			}

			if ( 'unavailable' === self::get_rule_status( $availability_rule ) ) {
				return false;
			}

			$has_available_match = true;
		}

		return $has_explicit_available_rules ? $has_available_match : true;
	}

	/**
	 * Check whether every requested day is available under the current rule set.
	 *
	 * @param mixed $room_availability Raw or normalized availability rules.
	 * @param array $date_strings      Array of dates in `Y/m/d` format.
	 * @return bool
	 */
	public static function are_dates_available_for_rules( $room_availability, array $date_strings ) {
		$date_strings = array_values( array_filter( array_map( 'sanitize_text_field', $date_strings ) ) );

		foreach ( $date_strings as $date_string ) {
			if ( ! self::is_date_available_for_rules( $room_availability, $date_string ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get all date keys covered by rules with a specific status.
	 *
	 * @param mixed  $room_availability Raw or normalized availability rules.
	 * @param string $status            Rule status to collect.
	 * @return array
	 */
	public static function get_rule_dates_by_status( $room_availability, $status ) {
		$room_availability = self::normalize_availability_rules( $room_availability );
		$status            = function_exists( 'sanitize_key' ) ? sanitize_key( $status ) : preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $status ) );
		$date_keys         = array();

		foreach ( $room_availability as $availability_rule ) {
			$rule_status = self::get_rule_status( $availability_rule );

			if ( 'available' === $status && 'unavailable' === $rule_status ) {
				continue;
			}

			if ( 'available' !== $status && $status !== $rule_status ) {
				continue;
			}

			$date_keys = array_merge( $date_keys, self::get_rule_date_keys( $availability_rule ) );
		}

		return array_values( array_unique( $date_keys ) );
	}

	/**
	 * Determine whether an availability rule targets a specific date.
	 *
	 * @param array  $availability_rule Availability rule.
	 * @param string $date_string       Date in `Y/m/d` format.
	 * @return bool
	 */
	private static function rule_matches_date( array $availability_rule, $date_string ) {
		if ( empty( $availability_rule['check_in'] ) || empty( $availability_rule['check_out'] ) ) {
			return false;
		}

		$rule_from = strtotime( $availability_rule['check_in'] . ' 00:00' );
		$rule_to   = strtotime( $availability_rule['check_out'] . ' 23:59' );
		$rule_date = strtotime( $date_string );

		if ( false === $rule_from || false === $rule_to || false === $rule_date ) {
			return false;
		}

		return $rule_date >= $rule_from && $rule_date <= $rule_to;
	}

	/**
	 * Expand an availability rule into individual date keys.
	 *
	 * @param array $availability_rule Availability rule.
	 * @return array
	 */
	private static function get_rule_date_keys( array $availability_rule ) {
		if ( empty( $availability_rule['check_in'] ) || empty( $availability_rule['check_out'] ) ) {
			return array();
		}

		$rule_from = strtotime( $availability_rule['check_in'] . ' 00:00' );
		$rule_to   = strtotime( $availability_rule['check_out'] . ' 00:00' );

		if ( false === $rule_from || false === $rule_to ) {
			return array();
		}

		if ( $rule_to < $rule_from ) {
			$rule_to = $rule_from;
		}

		$date_keys = array();

		for ( $day = $rule_from; $day <= $rule_to; $day = strtotime( '+1 day', $day ) ) {
			$date_keys[] = date( 'Y/m/d', $day );
		}

		return $date_keys;
	}

	/**
	 * Get the normalized status for a rule.
	 *
	 * @param array $availability_rule Availability rule.
	 * @return string
	 */
	private static function get_rule_status( array $availability_rule ) {
		if ( empty( $availability_rule['status'] ) ) {
			return 'available';
		}

		$status = strtolower( (string) $availability_rule['status'] );

		if ( function_exists( 'sanitize_key' ) ) {
			return sanitize_key( $status );
		}

		return preg_replace( '/[^a-z0-9_\-]/', '', $status );
	}

	/**
	 * Determine whether a decoded rule array is associative.
	 *
	 * @param array $availability_rule Availability rule.
	 * @return bool
	 */
	private static function is_associative_rule( array $availability_rule ) {
		return array_keys( $availability_rule ) !== range( 0, count( $availability_rule ) - 1 );
	}

	/**
	 * Get available rooms count for the date range
	 *
	 * @return int Number of available rooms
	 */
	public function get_available_count() {
		$booked_dates = $this->get_booked_dates();

		if ( empty( $booked_dates ) ) {
			return $this->available_rooms;
		}

		$check_in_time  = strtotime( $this->check_in );
		$check_out_time = strtotime( $this->check_out );

		// Track booked rooms per day
		$room_booked_per_day = array();

		foreach ( $booked_dates as $booking ) {
			$booking_check_in  = strtotime( $booking['check_in'] );
			$booking_check_out = strtotime( $booking['check_out'] );
			$rooms_booked      = intval( $booking['room'] );

			// Loop through each day in the booking period
			for ( $day = $booking_check_in; $day < $booking_check_out; $day = strtotime( '+1 day', $day ) ) {
				$date = date( 'Y-m-d', $day );

				if ( ! isset( $room_booked_per_day[ $date ] ) ) {
					$room_booked_per_day[ $date ] = 0;
				}

				$room_booked_per_day[ $date ] += $rooms_booked;
			}
		}

		// Find the minimum available rooms across all days
		$min_available = $this->available_rooms;

		$check_period = new \DatePeriod(
			new \DateTime( $this->check_in ),
			new \DateInterval( 'P1D' ),
			(new \DateTime( $this->check_out ))
		);

		foreach ( $check_period as $date ) {
			$date_string = $date->format( 'Y-m-d' );

			$booked_today = isset( $room_booked_per_day[ $date_string ] ) ? intval( $room_booked_per_day[ $date_string ] ) : 0;
			$available_today = max( $this->available_rooms - $booked_today, 0 );

			$min_available = min( $min_available, $available_today );
		}

		return $min_available;
	}

	/**
	 * Check room availability by listing ID
	 *
	 * Static method for quick availability check
	 *
	 * @param int    $room_id   Room post ID
	 * @param string $check_in  Check-in date (Y-m-d format)
	 * @param string $check_out Check-out date (Y-m-d format)
	 *
	 * @return bool True if room is available, false otherwise
	 */
	public static function check_availability( $room_id, $check_in, $check_out ) {
		$availability = new self( $room_id, $check_in, $check_out );

		return $availability->is_available();
	}

	/**
	 * Get availability status for multiple rooms
	 *
	 * @param array $room_ids  Array of room post IDs
	 * @param string $check_in  Check-in date (Y-m-d format)
	 * @param string $check_out Check-out date (Y-m-d format)
	 *
	 * @return array Array with room IDs as keys and availability status as values
	 */
	public static function check_multiple_availability( $room_ids, $check_in, $check_out ) {
		$availability_status = array();

		foreach ( $room_ids as $room_id ) {
			$availability_status[ intval( $room_id ) ] = self::check_availability( $room_id, $check_in, $check_out );
		}

		return $availability_status;
	}
}
