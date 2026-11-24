<?php
namespace Tourfic\Classes\Car_Rental;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Woocommerce\Booking_Validation as Validation;

/** A quote is reconstructed from selected identifiers and authoritative rental metadata. */
class Booking {
	public static function quote( $post_id, $selection ) {
		$meta = Validation::product( $post_id, 'car' );
		if ( is_wp_error( $meta ) || ! is_array( $selection ) ) { return Validation::error(); }
		$selection = self::selection( $selection );
		if ( is_wp_error( $selection ) ) { return $selection; }
		$start = self::datetime( $selection['pickup_date'], $selection['pickup_time'] );
		$end = self::datetime( $selection['dropoff_date'], $selection['dropoff_time'] );
		if ( ! $start || ! $end || $end <= $start ) { return Validation::error(); }
		$selection['pickup_date'] = $start->format( 'Y/m/d' );
		$selection['dropoff_date'] = $end->format( 'Y/m/d' );
		if ( '2' === (string) ( $meta['booking-by'] ?? '1' ) ) {
			return ! empty( $meta['booking-url'] ) ? array( 'meta' => $meta, 'selection' => $selection, 'total' => 0.0, 'payable' => 0.0, 'due' => 0.0, 'extras' => '', 'protection' => '' ) : Validation::error();
		}
		$prices = Pricing::set_total_price( $meta, $selection['pickup_date'], $selection['dropoff_date'], $selection['pickup_time'], $selection['dropoff_time'] );
		if ( empty( $prices['valid'] ) || ! Validation::amount( $prices['sale_price'] ?? null ) ) { return Validation::error(); }
		$extra = self::extras( $meta, $selection );
		if ( is_wp_error( $extra ) ) { return $extra; }
		$total = (float) $prices['sale_price'] + $extra['price'];
		if ( ! Validation::amount( $total ) || ! Availability::tf_car_inventory( $post_id, $meta, $selection['pickup_date'], $selection['dropoff_date'], $selection['pickup_time'], $selection['dropoff_time'] ) ) {
			return Validation::error();
		}
		$rounded = (float) wc_format_decimal( $total, wc_get_price_decimals() );
		if ( $total > 0 && $rounded <= 0 ) { return Validation::error(); }
		$payable = $rounded; $due = 0.0;
		if ( 'yes' === $selection['partial_payment'] && $rounded > 0 && apply_filters( 'tourfic_allow_deposit_feature', false, $meta ) ) {
			$amount = $meta['deposit_amount'] ?? null;
			if ( ! Validation::amount( $amount ) || (float) $amount <= 0 || ! in_array( $meta['deposit_type'] ?? '', array( 'fixed', 'percent' ), true )
				|| ( 'percent' === $meta['deposit_type'] && (float) $amount > 100 ) ) { return Validation::error(); }
			$deposit = 'percent' === $meta['deposit_type'] ? $rounded * (float) $amount / 100 : (float) $amount;
			$payable = min( $rounded, (float) wc_format_decimal( $deposit, wc_get_price_decimals() ) );
			if ( $payable <= 0 ) { return Validation::error(); }
			$due = $rounded - $payable;
		}
		return array( 'meta' => $meta, 'selection' => $selection, 'total' => $rounded, 'payable' => $payable, 'due' => $due, 'extras' => $extra['extras'], 'protection' => $extra['protection'] );
	}

	private static function selection( $input ) {
		$result = array();
		foreach ( array( 'pickup_date', 'dropoff_date', 'pickup_time', 'dropoff_time' ) as $key ) {
			if ( ! isset( $input[ $key ] ) || ! is_string( $input[ $key ] ) || '' === trim( $input[ $key ] ) ) { return Validation::error(); }
			$result[ $key ] = trim( $input[ $key ] );
		}
		$result['partial_payment'] = $input['partial_payment'] ?? 'no';
		if ( ! in_array( $result['partial_payment'], array( 'yes', 'no' ), true ) ) { return Validation::error(); }
		if ( is_array( $input['extra_ids'] ?? array() ) && is_array( $input['extra_qty'] ?? array() )
			&& array_keys( $input['extra_ids'] ?? array() ) !== array_keys( $input['extra_qty'] ?? array() ) ) { return Validation::error(); }
		foreach ( array( 'extra_ids', 'extra_qty', 'protection' ) as $key ) {
			$value = $input[ $key ] ?? array();
			if ( ! is_array( $value ) ) { return Validation::error(); }
			foreach ( $value as $item ) {
				if ( ! is_string( $item ) && ! is_int( $item ) ) { return Validation::error(); }
			}
			$result[ $key ] = array_values( $value );
		}
		if ( count( $result['extra_ids'] ) !== count( $result['extra_qty'] ) ) { return Validation::error(); }
		foreach ( $result['extra_qty'] as $key => $quantity ) {
			if ( '0' === (string) $quantity ) { $result['extra_qty'][ $key ] = 0; continue; }
			$quantity = Validation::id( $quantity );
			if ( ! $quantity ) { return Validation::error(); }
			$result['extra_qty'][ $key ] = $quantity;
		}
		$result['protection'] = array_values( array_unique( $result['protection'] ) );
		return $result;
	}

	private static function datetime( $date, $time ) {
		$day = Validation::date( $date );
		if ( ! $day ) { return false; }
		foreach ( array( 'H:i', 'h:i A', 'g:i A', 'h:i a', 'g:i a' ) as $format ) {
			$value = \DateTime::createFromFormat( '!Y/m/d ' . $format, $day->format( 'Y/m/d' ) . ' ' . $time, wp_timezone() );
			if ( $value && $value->format( $format ) === $time ) { return $value; }
		}
		return false;
	}

	private static function extras( $meta, $selection ) {
		$config = apply_filters( 'tourfic_car_extra_meta', null, null, $meta );
		foreach ( $selection['extra_ids'] as $id ) {
			$item = is_array( $config ) ? ( $config[ $id ] ?? null ) : null;
			if ( ! is_array( $item ) || ! Validation::amount( $item['price'] ?? null )
				|| ! in_array( $item['price_type'] ?? '', array( 'day', 'rental', 'fixed' ), true ) ) { return Validation::error(); }
		}
		foreach ( $selection['protection'] as $id ) {
			$item = $meta['protections'][ $id ] ?? null;
			if ( ! is_array( $item ) || ! Validation::amount( $item['price'] ?? null )
				|| ! in_array( $item['price_by'] ?? '', array( 'day', 'rental' ), true ) ) { return Validation::error(); }
		}
		$args = array( $meta, $selection['pickup_date'], $selection['dropoff_date'], $selection['pickup_time'], $selection['dropoff_time'] );
		$extras = $selection['extra_ids'] ? Pricing::set_extra_price( ...array_merge( $args, array( $selection['extra_ids'], $selection['extra_qty'] ) ) ) : array( 'price' => 0, 'title' => '' );
		$protection = $selection['protection'] ? Pricing::set_protection_price( $meta, $selection['protection'], $selection['pickup_date'], $selection['dropoff_date'], $selection['pickup_time'], $selection['dropoff_time'] ) : array( 'price' => 0, 'title' => '' );
		if ( ! Validation::amount( $extras['price'] ) || ! Validation::amount( $protection['price'] ) ) { return Validation::error(); }
		return array( 'price' => (float) $extras['price'] + (float) $protection['price'], 'extras' => $extras['title'], 'protection' => $protection['title'] );
	}
}
