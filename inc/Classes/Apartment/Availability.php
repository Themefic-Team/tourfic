<?php

namespace Tourfic\Classes\Apartment;
# don't load directly
defined( 'ABSPATH' ) || exit;

class Availability {

	public static function instance( $apt_id ) {
		return new self( $apt_id );
	}

	protected $apt_id;
	protected $meta;
	protected $days;
	protected $period;
	protected $check_in;
	protected $check_out;
	protected $persons;
	public static $is_available = false;

	

	function __construct( $apt_id ){
		$this->apt_id = $apt_id;
		$this->meta = get_post_meta( $this->apt_id, 'tf_apartment_opt', true );
	}

	public function set_dates($check_in, $check_out) {
		if ( !empty($check_in) && !empty($check_out) ) {
			$this->check_in = $check_in;
			$this->check_out = $check_out;
			
			$check_in_stt  = !empty($check_in) ? strtotime( $check_in . ' +1 day' ) : 0;
			$check_out_stt = !empty($check_out) ? strtotime( $check_out ) : 0; 
			$days = !empty($check_in_stt) && !empty($check_out_stt) ? ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 : 0;

			$tfperiod = new \DatePeriod(
				new \DateTime( $check_in . ' 00:00' ),
				new \DateInterval( 'P1D' ),
				new \DateTime( $check_out . ' 23:59' )
			);
		}
		$this->days = !empty($days) ? $days : 0;
		$this->period = !empty($tfperiod) ? $tfperiod : 0;

		return $this;
	}

	public function set_persons($adult, $child, $infant) {
		$this->persons = array(
			'adult' => !empty($adult) ? $adult : 0,
			'child' => !empty($child) ? $child : 0,
			'infant' => !empty($infant) ? $infant : 0,
		);

		return $this;
	}

	function get_total_price() {

		$meta = $this->meta;
		$enable_availability = !empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
		$pricing_type = !empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
		$total_price = 0;
		
		if ( $enable_availability === '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$apt_availability = ! empty( $meta['apt_availability'] ) ? json_decode( $meta['apt_availability'], true ) : [];

			if ( ! empty( $apt_availability ) && is_array( $apt_availability ) ) {
				$period_dates = $this->get_period_dates();

				if ( ! self::are_dates_available_for_rules( $apt_availability, $period_dates ) ) {
					return 0;
				}

				if ( ! self::has_explicit_available_rules( $apt_availability ) ) {
					return $this->get_base_total_price( $pricing_type );
				}

				$check_in_stt  = strtotime( $this->check_in );
				$check_out_stt = strtotime( $this->check_out );

				foreach ( $apt_availability as $key => $single_avail ) {
					$availability_date = ! empty( $single_avail['check_in'] ) ? $single_avail['check_in'] : $key;
					$date_stt          = strtotime( $availability_date );

					if( $date_stt >= $check_in_stt && $date_stt < $check_out_stt && self::get_rule_status( $single_avail ) !== 'unavailable' ) {
						if ( $pricing_type === 'per_night' ) {
							$total_price += ! empty( $single_avail['price'] ) ? intval( $single_avail['price'] ) : 0;
						} else {
							$total_price += !empty( $this->persons ) ? ( ( (int) $single_avail['adult_price'] * (int) $this->persons['adult'] ) + ( (int) $single_avail['child_price'] * (int) $this->persons["child"] ) + ( (int) $single_avail['infant_price'] * (int) $this->persons["infant"] ) ) : 0;
						}
					}
				}
			}
		}

		return $total_price;

	}

	private function get_base_total_price( $pricing_type ) {
		$meta = $this->meta;
		$days = ! empty( $this->days ) ? (int) $this->days : 0;

		if ( $days <= 0 ) {
			return 0;
		}

		if ( 'per_night' === $pricing_type ) {
			return ! empty( $meta['price_per_night'] ) ? ( (int) $meta['price_per_night'] * $days ) : 0;
		}

		$adult_price  = ! empty( $meta['adult_price'] ) ? (int) $meta['adult_price'] : 0;
		$child_price  = ! empty( $meta['child_price'] ) ? (int) $meta['child_price'] : 0;
		$infant_price = ! empty( $meta['infant_price'] ) ? (int) $meta['infant_price'] : 0;
		$adult_count  = ! empty( $this->persons['adult'] ) ? (int) $this->persons['adult'] : 0;
		$child_count  = ! empty( $this->persons['child'] ) ? (int) $this->persons['child'] : 0;
		$infant_count = ! empty( $this->persons['infant'] ) ? (int) $this->persons['infant'] : 0;

		return ( ( $adult_price * $adult_count ) + ( $child_price * $child_count ) + ( $infant_price * $infant_count ) ) * $days;
	}

	private function get_period_dates() {
		if ( empty( $this->check_in ) || empty( $this->check_out ) ) {
			return array();
		}

		$period_dates = array();
		$period       = new \DatePeriod(
			new \DateTime( $this->check_in ),
			new \DateInterval( 'P1D' ),
			new \DateTime( $this->check_out )
		);

		foreach ( $period as $date ) {
			$period_dates[] = $date->format( 'Y/m/d' );
		}

		return $period_dates;
	}

	public static function has_explicit_available_rules( $apt_availability ) {
		$apt_availability = self::normalize_availability_rules( $apt_availability );

		foreach ( $apt_availability as $availability_rule ) {
			if ( 'unavailable' !== self::get_rule_status( $availability_rule ) ) {
				return true;
			}
		}

		return false;
	}

	public static function are_dates_available_for_rules( $apt_availability, array $date_strings ) {
		$date_strings = array_values( array_filter( array_map( 'sanitize_text_field', $date_strings ) ) );

		foreach ( $date_strings as $date_string ) {
			if ( ! self::is_date_available_for_rules( $apt_availability, $date_string ) ) {
				return false;
			}
		}

		return true;
	}

	public static function get_rule_dates_by_status( $apt_availability, $status ) {
		$apt_availability = self::normalize_availability_rules( $apt_availability );
		$status           = function_exists( 'sanitize_key' ) ? sanitize_key( $status ) : preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $status ) );
		$date_keys        = array();

		foreach ( $apt_availability as $availability_rule ) {
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

	public static function is_date_available_for_rules( $apt_availability, $date_string ) {
		$apt_availability = self::normalize_availability_rules( $apt_availability );

		if ( empty( $apt_availability ) ) {
			return true;
		}

		$has_explicit_available_rules = self::has_explicit_available_rules( $apt_availability );
		$has_available_match          = false;

		foreach ( $apt_availability as $availability_rule ) {
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

	public static function normalize_availability_rules( $apt_availability ) {
		if ( is_string( $apt_availability ) ) {
			$decoded_rules    = json_decode( $apt_availability, true );
			$apt_availability = is_array( $decoded_rules ) ? $decoded_rules : array();
		}

		if ( ! is_array( $apt_availability ) ) {
			return array();
		}

		$normalized_rules = array();

		foreach ( $apt_availability as $availability_rule ) {
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

	private static function is_associative_rule( array $availability_rule ) {
		return array_keys( $availability_rule ) !== range( 0, count( $availability_rule ) - 1 );
	}

	// Apt_Availability::instance($id)->set_dates( $chekin, $checkout )->set_person()->get_total_price(); 
}
