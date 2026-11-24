<?php
namespace Tourfic\Classes\Woocommerce;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

/** Validate the public booking context independently of WooCommerce's placeholder product price. */
class Booking_Validation {
	private static $types = array(
		'hotel' => array( 'tf_hotel', 'tf_hotels_opt', 'tf_hotel_data', 'post_id', 'price_total' ),
		'tour' => array( 'tf_tours', 'tf_tours_opt', 'tf_tours_data', 'tour_id', 'price' ),
		'apartment' => array( 'tf_apartment', 'tf_apartment_opt', 'tf_apartment_data', 'post_id', 'total_price' ),
		'car' => array( 'tf_carrental', 'tf_carrental_opt', 'tf_car_data', 'post_id', 'price_total' ),
	);

	public function __construct() {
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_item' ), 20, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'restore_item' ), 20, 3 );
		add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart' ), 20 );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_checkout' ), 20, 2 );
		add_action( 'woocommerce_store_api_cart_errors', array( $this, 'check_store_cart' ), 20, 2 );
		add_action( 'woocommerce_checkout_create_order', array( $this, 'check_order' ), 5, 2 );
	}

	public static function id( $value ) {
		return ( is_int( $value ) || is_string( $value ) )
			&& 1 === preg_match( '/^[1-9][0-9]*$/D', (string) $value )
			&& (string) (int) $value === (string) $value ? (int) $value : false;
	}

	public static function amount( $value ) {
		return ( is_int( $value ) || is_float( $value ) || is_string( $value ) )
			&& is_numeric( $value ) && is_finite( (float) $value ) && (float) $value >= 0;
	}

	public static function error() {
		return new \WP_Error( 'tourfic_invalid_booking', __( 'This booking is unavailable or has changed. Please select it again.', 'tourfic' ), array( 'status' => 400 ) );
	}

	public static function product( $id, $kind ) {
		$id = self::id( $id );
		if ( ! $id || ! isset( self::$types[ $kind ] )
			|| self::$types[ $kind ][0] !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
			return self::error();
		}
		$meta = get_post_meta( $id, self::$types[ $kind ][1], true );
		if ( 'apartment' === $kind ) { $meta = \Tourfic\Classes\Apartment\Pricing::get_normalized_meta( $id ); }
		if ( 'car' === $kind && function_exists( 'tourfic_normalize_car_meta' ) ) {
			$meta = tourfic_normalize_car_meta( $meta );
		}
		if ( ! is_array( $meta ) || ! $meta
			|| ! is_scalar( $meta['booking-by'] ?? '1' )
			|| ! in_array( (string) ( $meta['booking-by'] ?? '1' ), array( '1', '2', '3' ), true ) ) {
			return self::error();
		}
		return $meta;
	}

	public static function date( $value ) {
		if ( ! is_string( $value ) ) { return false; }
		foreach ( array_unique( array( 'Y/m/d', 'Y-m-d', Helper::tfopt( 'tf-date-format-for-users' ) ?: 'Y/m/d' ) ) as $format ) {
			$date = \DateTime::createFromFormat( '!' . $format, $value, wp_timezone() );
			if ( $date && $date->format( $format ) === $value ) { return $date; }
		}
		return false;
	}

	public static function dates( $from, $to, $same_day = false ) {
		$start = self::date( $from ); $end = self::date( $to );
		return $start && $end && ( $same_day ? $end >= $start : $end > $start );
	}

	public static function canonical( $id, $type ) {
		$id = self::id( $id );
		if ( $id && apply_filters( 'wpml_is_translated_post_type', false, $type ) ) {
			$language = apply_filters( 'wpml_default_language', null );
			if ( ! is_string( $language ) || '' === $language ) { return false; }
			$id = self::id( apply_filters( 'wpml_object_id', $id, $type, false, $language ) );
		}
		return $id;
	}

	public static function hotel_room( $hotel_id, $room_id ) {
		$hotel = self::product( $hotel_id, 'hotel' );
		$room_id = self::id( $room_id );
		if ( is_wp_error( $hotel ) || ! $room_id || 'tf_room' !== get_post_type( $room_id ) || 'publish' !== get_post_status( $room_id ) ) {
			return self::error();
		}
		$room = get_post_meta( $room_id, 'tf_room_opt', true );
		$canonical = self::canonical( $hotel_id, 'tf_hotel' );
		$inventory_id = self::canonical( $room_id, 'tf_room' );
		if ( ! $canonical || 'tf_hotel' !== get_post_type( $canonical ) || 'publish' !== get_post_status( $canonical )
			|| ! $inventory_id || 'tf_room' !== get_post_type( $inventory_id ) || 'publish' !== get_post_status( $inventory_id ) ) {
			return self::error();
		}
		$inventory = get_post_meta( $inventory_id, 'tf_room_opt', true );
		foreach ( array( $room, $inventory ) as $record ) {
			$parent = is_array( $record ) ? self::id( $record['tf_hotel'] ?? null ) : false;
			if ( ! $parent || ! is_scalar( $record['unique_id'] ?? null ) || empty( $record['unique_id'] ) || 'tf_hotel' !== get_post_type( $parent ) || 'publish' !== get_post_status( $parent )
				|| $canonical !== self::canonical( $parent, 'tf_hotel' ) ) { return self::error(); }
		}
		return $room;
	}

	private static function fingerprint( $id, $kind, $payload ) {
		$meta = self::product( $id, $kind );
		if ( is_wp_error( $meta ) ) { return false; }
		$context = array( 'meta' => $meta, 'author' => get_post_field( 'post_author', $id ), 'pro' => defined( 'TF_PRO' ) ? TF_PRO : '', 'currency' => get_woocommerce_currency(), 'decimals' => wc_get_price_decimals() );
		if ( 'hotel' === $kind ) {
			$room = self::hotel_room( $id, $payload['room_id'] ?? null );
			if ( is_wp_error( $room ) ) { return false; }
			unset( $room['order_id'] );
			$context['room'] = $room;
			$context['hotel_id'] = self::canonical( $id, 'tf_hotel' );
			$context['author'] = get_post_field( 'post_author', $context['hotel_id'] );
			$context['room_id'] = self::canonical( $payload['room_id'], 'tf_room' );
			$inventory = get_post_meta( $context['room_id'], 'tf_room_opt', true );
			unset( $inventory['order_id'] );
			$context['inventory_room'] = $inventory;
		}
		unset( $context['meta']['order_id'] );
		return hash( 'sha256', wp_json_encode( $context ) );
	}

	/** Mint only after the owning handler has resolved its selection and price. */
	public static function seal( $kind, array $data ) {
		if ( ! isset( self::$types[ $kind ] ) ) { return self::error(); }
		$map = self::$types[ $kind ];
		$payload = $data[ $map[2] ] ?? array();
		if ( ! is_array( $payload ) ) { return self::error(); }
		$id = self::id( $payload[ $map[3] ] ?? null );
		if ( ! $id || ! self::amount( $payload[ $map[4] ] ?? null ) ) { return self::error(); }
		if ( 'tour' === $kind && ! self::amount( $payload['tour_extra_total'] ?? 0 ) ) { return self::error(); }
		foreach ( array( $map[4], 'tour_extra_total', 'due' ) as $key ) {
			if ( ! array_key_exists( $key, $payload ) ) { continue; }
			if ( ! self::amount( $payload[ $key ] ) ) { return self::error(); }
			$rounded = (float) wc_format_decimal( (float) $payload[ $key ], wc_get_price_decimals() );
			if ( (float) $payload[ $key ] > 0 && $rounded <= 0 ) { return self::error(); }
			$payload[ $key ] = $rounded;
		}
		$data[ $map[2] ] = $payload;
		$fingerprint = self::fingerprint( $id, $kind, $payload );
		if ( ! $fingerprint ) { return self::error(); }
		$data['tourfic_booking_instance'] = wp_generate_uuid4();
		$data['tourfic_booking_quote'] = hash_hmac( 'sha256', $kind . '|' . $id . '|' . $fingerprint . '|' . wp_json_encode( $payload ), wp_salt( 'auth' ) );
		return $data;
	}

	public static function validate_item( $item ) {
		if ( ! is_array( $item ) ) { return self::error(); }
		$kinds = array();
		foreach ( self::$types as $kind => $map ) {
			if ( array_key_exists( $map[2], $item ) ) { $kinds[] = $kind; }
		}
		$id = self::id( $item['product_id'] ?? null );
		if ( ! $kinds ) {
			return $id && in_array( get_post_type( $id ), array_column( self::$types, 0 ), true ) ? self::error() : $item;
		}
		if ( 1 !== count( $kinds ) || ! $id ) { return self::error(); }
		$kind = $kinds[0]; $map = self::$types[ $kind ]; $payload = $item[ $map[2] ];
		$meta = self::product( $id, $kind );
		if ( ! is_array( $payload ) || is_wp_error( $meta ) || '1' !== (string) ( $meta['booking-by'] ?? '1' )
			|| $id !== self::id( $payload[ $map[3] ] ?? null ) || $kind !== ( $payload['order_type'] ?? null )
			|| 1 !== self::id( $item['quantity'] ?? null ) || ! empty( $item['variation_id'] )
			|| ! isset( $item['data'] ) || ! $item['data'] instanceof \WC_Product || $id !== $item['data']->get_id() ) {
			return self::error();
		}
		$sealed = self::seal( $kind, array( $map[2] => $payload ) );
		if ( is_wp_error( $sealed ) || ! is_string( $item['tourfic_booking_quote'] ?? null )
			|| ! hash_equals( $sealed['tourfic_booking_quote'], $item['tourfic_booking_quote'] ) ) { return self::error(); }
		if ( 'car' !== $kind ) {
			$selection = $payload['tourfic_booking_selection'] ?? array();
			if ( ! is_array( $selection ) || is_wp_error( Booking_Context::$kind( $id, $selection ) ) ) { return self::error(); }
		}
		if ( 'tour' === $kind && is_wp_error( Booking_Context::tour_inventory( $id, $payload['tourfic_booking_selection'] ) ) ) { return self::error(); }
		if ( 'car' === $kind ) {
			$quote = \Tourfic\Classes\Car_Rental\Booking::quote( $id, $payload['tourfic_booking_selection'] ?? array() );
			if ( is_wp_error( $quote ) || (float) $payload['price_total'] !== $quote['payable'] ) { return self::error(); }
		}
		return $item;
	}

	public static function set_prices( $cart, $kind ) {
		$map = self::$types[ $kind ];
		foreach ( $cart->get_cart() as $key => $item ) {
			if ( ! array_key_exists( $map[2], $item ) ) { continue; }
			$validated = self::validate_item( $item );
			if ( is_wp_error( $validated ) ) { continue; }
			$price = (float) $item[ $map[2] ][ $map[4] ];
			if ( 'tour' === $kind ) { $price += (float) ( $item[ $map[2] ]['tour_extra_total'] ?? 0 ); }
			$item['data']->set_price( $price );
		}
	}

	public function add_item( $item ) {
		$validated = self::validate_item( $item );
		if ( is_wp_error( $validated ) ) { throw new \Exception( esc_html( $validated->get_error_message() ) ); }
		return $validated;
	}

	public function restore_item( $item ) {
		$result = self::validate_item( $item );
		if ( is_wp_error( $result ) ) { $item['tourfic_booking_invalid'] = true; }
		return $item;
	}

	private function cart_errors( $cart ) {
		$errors = array();
		foreach ( $cart->get_cart() as $item ) {
			$result = self::validate_item( $item );
			if ( is_wp_error( $result ) ) { $errors[] = $result->get_error_message(); }
		}
		return array_unique( $errors );
	}

	public function check_cart() {
		foreach ( $this->cart_errors( WC()->cart ) as $message ) {
			if ( ! wc_has_notice( $message, 'error' ) ) { wc_add_notice( $message, 'error' ); }
		}
	}

	public function check_checkout( $data, $errors ) {
		$this->check_store_cart( $errors, WC()->cart );
	}

	public function check_store_cart( $errors, $cart ) {
		foreach ( $this->cart_errors( $cart ) as $message ) { $errors->add( 'tourfic_invalid_booking', $message ); }
	}

	public function check_order() {
		$errors = $this->cart_errors( WC()->cart );
		if ( $errors ) { throw new \Exception( esc_html( reset( $errors ) ) ); }
	}
}
