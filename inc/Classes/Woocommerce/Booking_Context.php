<?php
namespace Tourfic\Classes\Woocommerce;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Availability as Room_Availability;
use Tourfic\Classes\Apartment\Availability as Apartment_Availability;

/** Resolve bookable selections before any price calculation or cart mutation. */
class Booking_Context {

	/** Unslash once and validate a declared booking field without coercing malformed identifiers. */
	public static function request( $key, $default = null ) {
		$ids = array( 'post_id', 'room_id', 'room', 'hotel_room_selected' );
		$counts = array( 'adult', 'child', 'adults', 'children', 'childrens', 'infant', 'infants' );
		$strings = array( 'pickup_date', 'dropoff_date', 'pickup_time', 'dropoff_time', 'partial_payment' );
		$arrays = array( 'extra_ids', 'extra_qty', 'protection' );
		if ( ! in_array( $key, array_merge( $ids, $counts, $strings, $arrays ), true ) ) { return false; }
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Each owning booking handler verifies its action nonce before this read-only parser.
		if ( ! isset( $_POST[ $key ] ) ) { return $default; }
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing -- Owning handlers verify their nonce first; the schema below rejects malformed values before returning them.
		$value = wp_unslash( $_POST[ $key ] );
		if ( in_array( $key, $ids, true ) ) { return Booking_Validation::id( $value ); }
		if ( in_array( $key, $counts, true ) ) { return self::count( $value ); }
		if ( in_array( $key, $strings, true ) ) {
			return is_string( $value ) && sanitize_text_field( $value ) === $value ? $value : false;
		}
		if ( ! is_array( $value ) ) { return false; }
		$result = array();
		foreach ( $value as $index => $item ) {
			if ( false === self::count( $index ) ) { return false; }
			if ( 'extra_qty' === $key ) {
				$item = self::count( $item );
				if ( false === $item ) { return false; }
			} elseif ( ( ! is_int( $item ) && ! is_string( $item ) ) || ! preg_match( '/^[A-Za-z0-9_-]+$/D', (string) $item ) ) {
				return false;
			}
			$result[ $index ] = $item;
		}
		return $result;
	}

	public static function total( $value ) {
		if ( ! Booking_Validation::amount( $value ) ) { return Booking_Validation::error(); }
		$total = (float) wc_format_decimal( (float) $value, wc_get_price_decimals() );
		return (float) $value > 0 && $total <= 0 ? Booking_Validation::error() : $total;
	}

	public static function count( $value ) {
		return 0 === $value || '0' === $value ? 0 : Booking_Validation::id( $value );
	}

	public static function reject( $result ) {
		if ( is_wp_error( $result ) ) {
			wp_send_json( array( 'status' => 'error', 'errors' => array( $result->get_error_message() ) ), 400 );
		}
	}

	private static function rates( $data, $keys ) {
		if ( ! is_array( $data ) ) { return false; }
		foreach ( $keys as $key ) {
			if ( ! Booking_Validation::amount( $data[ $key ] ?? null ) ) { return false; }
		}
		return true;
	}

	private static function discount_valid( $data, $type_key, $amount_key ) {
		$type = $data[ $type_key ] ?? 'none';
		if ( '' === $type || 'none' === $type ) { return true; }
		return in_array( $type, array( 'fixed', 'percent' ), true )
			&& Booking_Validation::amount( $data[ $amount_key ] ?? null )
			&& ( 'percent' !== $type || (float) $data[ $amount_key ] <= 100 );
	}

	private static function nights( $start, $end ) {
		return new \DatePeriod( Booking_Validation::date( $start ), new \DateInterval( 'P1D' ), Booking_Validation::date( $end ) );
	}

	public static function hotel( $hotel_id, $selection ) {
		$room = Booking_Validation::hotel_room( $hotel_id, $selection['room_id'] ?? null );
		if ( is_wp_error( $room ) || ! Booking_Validation::dates( $selection['check_in'] ?? null, $selection['check_out'] ?? null ) ) {
			return Booking_Validation::error();
		}
		if ( ! self::discount_valid( $room, 'discount_hotel_type', 'discount_hotel_price' ) ) { return Booking_Validation::error(); }
		foreach ( array( 'adult', 'child', 'room' ) as $key ) {
			$count = self::count( $selection[ $key ] ?? null );
			if ( false === $count || ( 'child' !== $key && 0 === $count ) ) { return Booking_Validation::error(); }
			$selection[ $key ] = $count;
		}
		foreach ( array( 'adult', 'child' ) as $key ) {
			if ( $selection[ $key ] > (int) ( $room[ $key ] ?? 0 ) * $selection['room'] ) { return Booking_Validation::error(); }
		}
		$rates = self::hotel_rates( $room, $selection );
		if ( is_wp_error( $rates ) ) { return $rates; }
		$availability = new Room_Availability( $selection['room_id'], $selection['check_in'], $selection['check_out'] );
		if ( ! $availability->is_available() || $availability->get_available_count() < $selection['room'] ) { return Booking_Validation::error(); }
		return $selection;
	}

	private static function hotel_rates( $room, $selection ) {
		$mode = (string) apply_filters( 'tourfic_room_pricing_mode', 1, $room );
		if ( ! in_array( $mode, array( '1', '2', '3' ), true ) ) { return Booking_Validation::error(); }
		$rules = array();
		if ( ! empty( $room['avil_by_date'] ) ) {
			$raw = $room['avail_date'] ?? '';
			if ( ! is_string( $raw ) ) { return Booking_Validation::error(); }
			$rules = '' === $raw ? array() : json_decode( $raw, true );
			if ( ! is_array( $rules ) ) { return Booking_Validation::error(); }
		}
		$explicit = is_array( $rules ) && Room_Availability::has_explicit_available_rules( $rules );
		$end = Booking_Validation::date( $selection['check_out'] );
		if ( empty( $room['price_multi_day'] ) ) { $end->modify( '+1 day' ); }
		foreach ( self::nights( $selection['check_in'], $end->format( 'Y-m-d' ) ) as $date ) {
			$rate = $room;
			if ( $explicit ) {
				$rate = null;
				foreach ( $rules as $rule ) {
					if ( is_array( $rule ) && Booking_Validation::dates( $rule['check_in'] ?? null, $rule['check_out'] ?? null, true )
						&& $date >= Booking_Validation::date( $rule['check_in'] ) && $date <= Booking_Validation::date( $rule['check_out'] ) ) { $rate = $rule; break; }
				}
			}
			$result = self::hotel_rate( $room, $rate, $selection, $mode, $explicit );
			if ( is_wp_error( $result ) ) { return $result; }
		}
		return $selection;
	}

	private static function hotel_rate( $room, $rate, $selection, $mode, $explicit ) {
		if ( '3' === $mode ) {
			$option = $selection['option_id'] ?? null;
			$prefix = (string) ( $room['unique_id'] ?? '' ) . '_';
			if ( ! is_string( $option ) || '' === (string) ( $room['unique_id'] ?? '' ) || 0 !== strpos( $option, $prefix ) ) { return Booking_Validation::error(); }
			$key = substr( $option, strlen( $prefix ) );
			if ( ! preg_match( '/^(0|[1-9][0-9]*)$/D', $key ) ) { return Booking_Validation::error(); }
			if ( $explicit ) {
				if ( ! Booking_Validation::id( $rate['options_count'] ?? null ) || (int) $key >= (int) $rate['options_count'] ) { return Booking_Validation::error(); }
				if ( ! is_array( $rate ) || '1' !== (string) ( $rate[ 'tf_room_option_' . $key ] ?? '' ) ) { return Booking_Validation::error(); }
				$type = $rate[ 'tf_option_pricing_type_' . $key ] ?? '';
				$keys = 'per_room' === $type ? array( 'tf_option_room_price_' . $key ) : array( 'tf_option_adult_price_' . $key );
				if ( 'per_person' === $type && $selection['child'] > 0 ) { $keys[] = 'tf_option_child_price_' . $key; }
			} else {
				$rate = $room['room-options'][ $key ] ?? null;
				$type = $rate['option_pricing_type'] ?? 'per_room';
				$keys = 'per_room' === $type ? array( 'option_price' ) : array( 'option_adult_price' );
				if ( 'per_person' === $type && $selection['child'] > 0 ) { $keys[] = 'option_child_price'; }
			}
			if ( ! in_array( $type, array( 'per_room', 'per_person' ), true ) || ! self::rates( $rate, $keys ) ) { return Booking_Validation::error(); }
		} else {
			$keys = '1' === $mode ? array( 'price' ) : array( 'adult_price' );
			if ( '2' === $mode && $selection['child'] > 0 ) { $keys[] = 'child_price'; }
			if ( '1' === $mode && $explicit && empty( $rate['price'] ) ) { $rate['price'] = $room['price'] ?? null; }
			if ( ! self::rates( $rate, $keys ) ) { return Booking_Validation::error(); }
		}
		return true;
	}

	public static function apartment( $post_id, $selection ) {
		$meta = Booking_Validation::product( $post_id, 'apartment' );
		if ( is_wp_error( $meta ) || ! Booking_Validation::dates( $selection['check_in'] ?? null, $selection['check_out'] ?? null ) ) { return Booking_Validation::error(); }
		if ( ! self::discount_valid( $meta, 'discount_type', 'discount' ) ) { return Booking_Validation::error(); }
		foreach ( array( 'adults', 'children', 'infant' ) as $key ) {
			$count = self::count( $selection[ $key ] ?? null );
			$capacity = array( 'adults' => 'max_adults', 'children' => 'max_children', 'infant' => 'max_infants' );
			if ( false === $count || ( 'adults' === $key && 0 === $count ) || $count > (int) ( $meta[ $capacity[ $key ] ] ?? 0 ) ) { return Booking_Validation::error(); }
			$selection[ $key ] = $count;
		}
		$nights = (int) Booking_Validation::date( $selection['check_in'] )->diff( Booking_Validation::date( $selection['check_out'] ) )->days;
		if ( $nights < max( 1, (int) ( $meta['min_stay'] ?? 1 ) ) ) { return Booking_Validation::error(); }
		$mode = $meta['pricing_type'] ?? 'per_night';
		if ( ! in_array( $mode, array( 'per_night', 'per_person' ), true ) ) { return Booking_Validation::error(); }
		$keys = 'per_night' === $mode ? array( 'price_per_night' ) : array( 'adult_price' );
		if ( 'per_person' === $mode ) {
			if ( $selection['children'] > 0 ) { $keys[] = 'child_price'; }
			if ( $selection['infant'] > 0 ) { $keys[] = 'infant_price'; }
		}
		if ( ! empty( $meta['enable_availability'] ) && ( ! is_string( $meta['apt_availability'] ?? null ) || ! is_array( json_decode( $meta['apt_availability'], true ) ) ) ) { return Booking_Validation::error(); }
		$rules = ! empty( $meta['enable_availability'] ) ? Apartment_Availability::normalize_availability_rules( $meta['apt_availability'] ?? '' ) : array();
		if ( ! empty( $meta['enable_availability'] ) && ! $rules ) { return Booking_Validation::error(); }
		$explicit = Apartment_Availability::has_explicit_available_rules( $rules );
		foreach ( self::nights( $selection['check_in'], $selection['check_out'] ) as $date ) {
			if ( ! Apartment_Availability::is_date_available_for_rules( $rules, $date->format( 'Y/m/d' ) ) ) { return Booking_Validation::error(); }
			$rate = $meta;
			if ( $explicit ) {
				$rate = null;
				foreach ( $rules as $rule ) {
					$day = Booking_Validation::date( $rule['check_in'] ?? null );
					if ( $day && $day == $date ) { $rate = $rule; break; }
				}
				$rate_keys = 'per_night' === $mode ? array( 'price' ) : $keys;
			} else { $rate_keys = $keys; }
			if ( ! self::rates( $rate, $rate_keys ) ) { return Booking_Validation::error(); }
		}
		foreach ( \Tourfic\Classes\Apartment\Apartment::tf_apartment_booked_days( $post_id ) as $booking ) {
			$start = Booking_Validation::date( $booking['check_in'] ?? null ); $end = Booking_Validation::date( $booking['check_out'] ?? null );
			if ( $start && $end && $start < Booking_Validation::date( $selection['check_out'] ) && $end > Booking_Validation::date( $selection['check_in'] ) ) { return Booking_Validation::error(); }
		}
		return $selection;
	}

	public static function tour( $post_id, $selection ) {
		$meta = Booking_Validation::product( $post_id, 'tour' );
		if ( is_wp_error( $meta ) || ! Booking_Validation::date( $selection['date'] ?? null ) ) { return Booking_Validation::error(); }
		if ( ! empty( $meta['allow_discount'] ) && ! self::discount_valid( $meta, 'discount_type', 'discount_price' ) ) { return Booking_Validation::error(); }
		$total = 0;
		foreach ( array( 'adults', 'children', 'infant' ) as $key ) {
			$count = self::count( $selection[ $key ] ?? null );
			if ( false === $count ) { return Booking_Validation::error(); }
			$selection[ $key ] = $count; $total += $count;
		}
		if ( $total <= 0 ) { return Booking_Validation::error(); }
		if ( isset( $selection['time'] ) && ! is_string( $selection['time'] ) ) { return Booking_Validation::error(); }
		$selection['date'] = Booking_Validation::date( $selection['date'] )->format( 'Y/m/d' );
		$package = $selection['package'] ?? '';
		if ( ! is_string( $package ) ) { return Booking_Validation::error(); }
		$mode = $meta['pricing'] ?? '';
		$rules = $meta['tour_availability'] ?? array();
		if ( '' === $rules || null === $rules ) { $rules = array(); }
		if ( is_string( $rules ) ) { $rules = json_decode( $rules, true ); }
		if ( ! is_array( $rules ) ) { return Booking_Validation::error(); }
		$rule = 'package' === $mode ? Helper::tf_get_tour_matched_availability_for_package( $rules, $selection['date'], $package ) : Helper::tf_get_tour_matched_availability( $rules, $selection['date'], '' );
		if ( ! empty( $rules ) && ( empty( $rule ) || 'unavailable' === ( $rule['status'] ?? '' ) || 'unavailable' === ( $rule[ 'tf_option_status_' . $package ] ?? '' ) ) ) { return Booking_Validation::error(); }
		$rate = $meta; $keys = array();
		if ( 'package' === $mode ) {
			$rate = is_array( $meta['package_pricing'] ?? null ) ? ( $meta['package_pricing'][ $package ] ?? null ) : null;
			if ( ! is_array( $rate ) || '' === $package ) { return Booking_Validation::error(); }
			$mode = $rate['pricing_type'] ?? '';
		}
		if ( 'group' === $mode ) { $keys[] = 'group_price'; }
		elseif ( 'person' === $mode ) {
			foreach ( array( 'adults' => 'adult', 'children' => 'child', 'infant' => 'infant' ) as $key => $prefix ) {
				if ( $selection[ $key ] > 0 && empty( $meta[ 'disable_' . $prefix . '_price' ] ) ) { $keys[] = $prefix . '_price'; }
			}
		} else { return Booking_Validation::error(); }
		foreach ( $keys as $key ) {
			$price = $rate[ $key ] ?? null;
			if ( 'package' === ( $meta['pricing'] ?? '' ) ) {
				$prefix = explode( '_', $key )[0];
				$price = $rate[ $prefix . '_tabs' ][1][ $key ] ?? null;
				$override = 'tf_option_' . $key . '_' . $package;
			} else { $override = $key; }
			if ( ! empty( $rule[ $override ] ) ) { $price = $rule[ $override ]; }
			if ( ! Booking_Validation::amount( $price ) ) { return Booking_Validation::error(); }
		}
		return $selection;
	}

	/** Repeat the maintained tour schedule and capacity rules immediately before checkout. */
	public static function tour_inventory( $post_id, $selection ) {
		$meta = Booking_Validation::product( $post_id, 'tour' );
		if ( is_wp_error( $meta ) ) { return $meta; }
		$rules = $meta['tour_availability'] ?? array();
		if ( is_string( $rules ) ) { $rules = json_decode( $rules, true ) ?: array(); }
		$rule = 'package' === ( $meta['pricing'] ?? '' )
			? Helper::tf_get_tour_matched_availability_for_package( $rules, $selection['date'], $selection['package'] )
			: Helper::tf_get_tour_matched_availability( $rules, $selection['date'], '' );
		$people = $selection['adults'] + $selection['children'];
		$schedule = apply_filters( 'tourfic_tour_booking_schedule_context',
			array( 'skip_core' => false, 'start_date' => $selection['date'], 'end_date' => $selection['date'], 'errors' => array() ),
			array( 'post_id' => $post_id, 'meta' => $meta, 'tour_date' => $selection['date'],
				'tour_time' => $selection['time'] ?? '', 'matched_availability' => $rule,
				'is_date_unavailable' => false, 'pricing_rule' => $meta['pricing'] ?? '',
				'selected_package' => $selection['package'], 'total_people' => $people + $selection['infant'],
				'total_people_booking' => $people ) );
		if ( ! empty( $schedule['errors'] ) ) { return Booking_Validation::error(); }
		$limit = (int) ( $rule['max_capacity'] ?? 0 );
		if ( ! empty( $schedule['skip_core'] ) || 'package' === ( $meta['pricing'] ?? '' ) || $limit <= 0 ) { return true; }
		$orders = Helper::tourfic_order_table_data( array( 'select' => 'post_id,order_details', 'post_type' => 'tour',
			'where' => array( 'ostatus' => 'completed', 'post_id' => $post_id ) ) );
		if ( ! is_array( $orders ) ) { return Booking_Validation::error(); }
		foreach ( $orders as $order ) {
			$details = json_decode( $order['order_details'] ?? '', true );
			if ( Booking_Validation::date( $details['tour_date'] ?? null ) != Booking_Validation::date( $selection['date'] ) ) { continue; }
			foreach ( array( 'adult', 'child' ) as $key ) {
				$parts = explode( ' × ', (string) ( $details[ $key ] ?? '' ) );
				$people += absint( $parts[0] );
			}
		}
		return $people <= $limit ? true : Booking_Validation::error();
	}
}
