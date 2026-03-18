<?php
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
use Tourfic\Classes\Helper;

/**
 * Apartment booking ajax function
 * @author Foysal
 */
add_action( 'wp_ajax_tf_apartment_booking', 'tf_apartment_booking_callback' );
add_action( 'wp_ajax_nopriv_tf_apartment_booking', 'tf_apartment_booking_callback' );
add_action( 'wp_ajax_tf_apartment_booking_popup', 'tf_apartment_booking_popup_callback' );
add_action( 'wp_ajax_nopriv_tf_apartment_booking_popup', 'tf_apartment_booking_popup_callback' );

/**
 * Parse apartment check-in/out range.
 *
 * @param string $check_in_out_date Check-in/out date string.
 * @return array<string, mixed>
 */
function tf_apartment_parse_check_in_out_date( $check_in_out_date ) {
	$date_parts = array( '', '' );
	$check_in   = '';
	$check_out  = '';
	$days       = 0;

	if ( ! empty( $check_in_out_date ) ) {
		$date_parts = array_map( 'trim', explode( ' - ', $check_in_out_date ) );
		$date_parts = array_pad( $date_parts, 2, '' );

		$check_in_stt  = ! empty( $date_parts[0] ) ? strtotime( $date_parts[0] ) : 0;
		$check_out_stt = ! empty( $date_parts[1] ) ? strtotime( $date_parts[1] ) : 0;

		if ( ! empty( $check_in_stt ) ) {
			$check_in = gmdate( 'Y-m-d', $check_in_stt );
		}
		if ( ! empty( $check_out_stt ) ) {
			$check_out = gmdate( 'Y-m-d', $check_out_stt );
		}
		if ( ! empty( $check_in ) && ! empty( $check_out ) ) {
			$check_in_night_stt = strtotime( $check_in . ' +1 day' );
			$check_out_date_stt = strtotime( $check_out );
			if ( ! empty( $check_in_night_stt ) && ! empty( $check_out_date_stt ) ) {
				$days = round( ( ( $check_out_date_stt - $check_in_night_stt ) / ( 60 * 60 * 24 ) ) + 1 );
			}
		}
	}

	return array(
		'date_parts' => $date_parts,
		'check_in'   => $check_in,
		'check_out'  => $check_out,
		'days'       => $days,
	);
}

/**
 * Validate apartment booking data.
 *
 * @param int   $post_id    Apartment post ID.
 * @param int   $adults     Adult count.
 * @param int   $children   Children count.
 * @param int   $infant     Infant count.
 * @param array $date_parts Check-in/out parts.
 * @param array $meta       Apartment meta.
 * @return array<int, string>
 */
function tf_apartment_get_booking_validation_errors( $post_id, $adults, $children, $infant, $date_parts, $meta ) {
	$errors       = array();
	$max_adults   = ( isset( $meta['max_adults'] ) && ! empty( $meta['max_adults'] ) ) ? intval( $meta['max_adults'] ) : 0;
	$max_children = ( isset( $meta['max_children'] ) && ! empty( $meta['max_children'] ) ) ? intval( $meta['max_children'] ) : 0;
	$max_infants  = ( isset( $meta['max_infants'] ) && ! empty( $meta['max_infants'] ) ) ? intval( $meta['max_infants'] ) : 0;

	if ( empty( $date_parts[0] ) ) {
		$errors[] = esc_html__( 'Check-in date missing.', 'tourfic' );
	}
	if ( empty( $date_parts[1] ) ) {
		$errors[] = esc_html__( 'Check-out date missing.', 'tourfic' );
	}
	if ( empty( $adults ) ) {
		$errors[] = esc_html__( 'Select Adult(s).', 'tourfic' );
	}
	if ( 0 === $max_adults && $adults > 0 ) {
		$errors[] = esc_html__( 'Adult not allowed.', 'tourfic' );
	} elseif ( $max_adults && $adults > $max_adults ) {
		/* translators: %s Adult Count */
		$errors[] = sprintf( esc_html__( 'Maximum %s Adult(s) allowed.', 'tourfic' ), $max_adults );
	}
	if ( 0 === $max_children && $children > 0 ) {
		$errors[] = esc_html__( 'Children not allowed.', 'tourfic' );
	} elseif ( $max_children && $children > $max_children ) {
		/* translators: %s Children Count */
		$errors[] = sprintf( esc_html__( 'Maximum %s Children(s) allowed.', 'tourfic' ), $max_children );
	}
	if ( 0 === $max_infants && $infant > 0 ) {
		$errors[] = esc_html__( 'Infant not allowed.', 'tourfic' );
	} elseif ( $max_infants && $infant > $max_infants ) {
		/* translators: %s Infant Count */
		$errors[] = sprintf( esc_html__( 'Maximum %s Infant(s) allowed.', 'tourfic' ), $max_infants );
	}
	if ( empty( $post_id ) ) {
		$errors[] = esc_html__( 'Unknown Error! Please try again.', 'tourfic' );
	}

	return $errors;
}

/**
 * Calculate apartment booking total.
 *
 * @param int    $post_id              Apartment post ID.
 * @param string $check_in             Check-in date.
 * @param string $check_out            Check-out date.
 * @param int    $adults               Adult count.
 * @param int    $children             Children count.
 * @param int    $infant               Infant count.
 * @param string $enable_availability  Availability mode.
 * @return float
 */
function tf_apartment_get_booking_total_price( $post_id, $check_in, $check_out, $adults, $children, $infant, $enable_availability ) {
	if ( empty( $check_in ) || empty( $check_out ) ) {
		return 0;
	}

	if ( '1' === $enable_availability && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		return (float) Apt_Pricing::instance( $post_id )->set_dates( $check_in, $check_out )->set_persons( $adults, $children, $infant )->get_availability();
	}

	return (float) Apt_Pricing::instance( $post_id )->set_dates( $check_in, $check_out )->set_persons( $adults, $children, $infant )->set_total_price()->get_total_price();
}

/**
 * Calculate payable amount and due for apartment partial payment.
 *
 * @param array   $meta          Apartment meta.
 * @param float   $total_price   Calculated total price.
 * @param string  $make_deposit  Deposit flag.
 * @param integer $booking_type  Booking type.
 * @return array<string, float>
 */
function tf_apartment_get_booking_payable_and_due( $meta, $total_price, $make_deposit, $booking_type ) {
	$payable = (float) $total_price;
	$due     = 0;

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && '1' == $booking_type && '1' === $make_deposit && $payable > 0 ) {
		$deposit_type   = ! empty( $meta['deposit_type'] ) ? $meta['deposit_type'] : '';
		$deposit_amount = null;
		$has_deposit    = false;

		Helper::tf_get_deposit_amount( $meta, $payable, $deposit_amount, $has_deposit );
		if ( $has_deposit && in_array( $deposit_type, array( 'percent', 'fixed' ), true ) ) {
			$deposit_amount = min( (float) $deposit_amount, $payable );
			if ( $deposit_amount > 0 ) {
				$due     = max( 0, $payable - $deposit_amount );
				$payable = $deposit_amount;
			}
		}
	}

	return array(
		'payable' => $payable,
		'due'     => $due,
	);
}

/**
 * Build billing/shipping details for apartment booking without payment.
 *
 * @param array $confirmation_details Confirmation fields from request.
 * @return array<string, array<string, string>>
 */
function tf_apartment_get_without_payment_customer_details( $confirmation_details ) {
	$billing_details  = array();
	$shipping_details = array();

	if ( ! is_array( $confirmation_details ) || empty( $confirmation_details ) ) {
		return array(
			'billing_details'  => $billing_details,
			'shipping_details' => $shipping_details,
		);
	}

	$tf_booking_fields = ! empty( Helper::tfopt( 'book-confirm-field' ) ) ? Helper::tf_data_types( Helper::tfopt( 'book-confirm-field' ) ) : '';

	if ( empty( $tf_booking_fields ) ) {
		$billing_details = array(
			'billing_first_name' => isset( $confirmation_details['tf_first_name'] ) ? sanitize_text_field( $confirmation_details['tf_first_name'] ) : '',
			'billing_last_name'  => isset( $confirmation_details['tf_last_name'] ) ? sanitize_text_field( $confirmation_details['tf_last_name'] ) : '',
			'billing_company'    => '',
			'billing_address_1'  => isset( $confirmation_details['tf_street_address'] ) ? sanitize_text_field( $confirmation_details['tf_street_address'] ) : '',
			'billing_address_2'  => '',
			'billing_city'       => isset( $confirmation_details['tf_town_city'] ) ? sanitize_text_field( $confirmation_details['tf_town_city'] ) : '',
			'billing_state'      => isset( $confirmation_details['tf_state_country'] ) ? sanitize_text_field( $confirmation_details['tf_state_country'] ) : '',
			'billing_postcode'   => isset( $confirmation_details['tf_postcode'] ) ? sanitize_text_field( $confirmation_details['tf_postcode'] ) : '',
			'billing_country'    => isset( $confirmation_details['tf_country'] ) ? sanitize_text_field( $confirmation_details['tf_country'] ) : '',
			'billing_email'      => isset( $confirmation_details['tf_email'] ) ? sanitize_email( $confirmation_details['tf_email'] ) : '',
			'billing_phone'      => isset( $confirmation_details['tf_phone'] ) ? sanitize_text_field( $confirmation_details['tf_phone'] ) : '',
		);

		$shipping_details = array(
			'tf_first_name'      => isset( $confirmation_details['tf_first_name'] ) ? sanitize_text_field( $confirmation_details['tf_first_name'] ) : '',
			'tf_last_name'       => isset( $confirmation_details['tf_last_name'] ) ? sanitize_text_field( $confirmation_details['tf_last_name'] ) : '',
			'shipping_company'   => '',
			'tf_street_address'  => isset( $confirmation_details['tf_street_address'] ) ? sanitize_text_field( $confirmation_details['tf_street_address'] ) : '',
			'shipping_address_2' => '',
			'tf_town_city'       => isset( $confirmation_details['tf_town_city'] ) ? sanitize_text_field( $confirmation_details['tf_town_city'] ) : '',
			'tf_state_country'   => isset( $confirmation_details['tf_state_country'] ) ? sanitize_text_field( $confirmation_details['tf_state_country'] ) : '',
			'tf_postcode'        => isset( $confirmation_details['tf_postcode'] ) ? sanitize_text_field( $confirmation_details['tf_postcode'] ) : '',
			'tf_country'         => isset( $confirmation_details['tf_country'] ) ? sanitize_text_field( $confirmation_details['tf_country'] ) : '',
			'tf_phone'           => isset( $confirmation_details['tf_phone'] ) ? sanitize_text_field( $confirmation_details['tf_phone'] ) : '',
			'tf_email'           => isset( $confirmation_details['tf_email'] ) ? sanitize_email( $confirmation_details['tf_email'] ) : '',
		);

		return array(
			'billing_details'  => $billing_details,
			'shipping_details' => $shipping_details,
		);
	}

	foreach ( $confirmation_details as $key => $details ) {
		$sanitized_detail = is_array( $details ) ? wp_json_encode( array_map( 'sanitize_text_field', $details ) ) : sanitize_text_field( $details );
		if ( 'tf_first_name' === $key ) {
			$billing_details['billing_first_name'] = $sanitized_detail;
			$shipping_details[ $key ]              = $sanitized_detail;
		} elseif ( 'tf_last_name' === $key ) {
			$billing_details['billing_last_name'] = $sanitized_detail;
			$shipping_details[ $key ]             = $sanitized_detail;
		} elseif ( 'tf_street_address' === $key ) {
			$billing_details['billing_address_1'] = $sanitized_detail;
			$shipping_details[ $key ]             = $sanitized_detail;
		} elseif ( 'tf_town_city' === $key ) {
			$billing_details['billing_city'] = $sanitized_detail;
			$shipping_details[ $key ]        = $sanitized_detail;
		} elseif ( 'tf_state_country' === $key ) {
			$billing_details['billing_state'] = $sanitized_detail;
			$shipping_details[ $key ]         = $sanitized_detail;
		} elseif ( 'tf_postcode' === $key ) {
			$billing_details['billing_postcode'] = $sanitized_detail;
			$shipping_details[ $key ]            = $sanitized_detail;
		} elseif ( 'tf_country' === $key ) {
			$billing_details['billing_country'] = $sanitized_detail;
			$shipping_details[ $key ]           = $sanitized_detail;
		} elseif ( 'tf_email' === $key ) {
			$billing_details['billing_email'] = sanitize_email( $details );
			$shipping_details[ $key ]         = sanitize_email( $details );
		} elseif ( 'tf_phone' === $key ) {
			$billing_details['billing_phone'] = $sanitized_detail;
			$shipping_details[ $key ]         = $sanitized_detail;
		} else {
			$billing_details[ $key ] = $sanitized_detail;
			$shipping_details[ $key ] = $sanitized_detail;
		}
	}

	return array(
		'billing_details'  => $billing_details,
		'shipping_details' => $shipping_details,
	);
}

/**
 * Get customer id for apartment booking without payment.
 *
 * @return int
 */
function tf_apartment_get_offline_customer_id() {
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		if ( ! empty( $current_user->ID ) ) {
			return (int) $current_user->ID;
		}
	}

	return 1;
}

function tf_apartment_booking_callback() {
	$response          = [];
	$tf_apartment_data = [];

	// Check nonce security
	if ( ! isset( $_POST['tf_apartment_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['tf_apartment_nonce'])), 'tf_apartment_booking' ) ) {
		return;
	}

	$post_id           = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
	$adults            = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : '0';
	$children          = isset( $_POST['children'] ) ? intval( sanitize_text_field( $_POST['children'] ) ) : '0';
	$infant            = isset( $_POST['infant'] ) ? intval( sanitize_text_field( $_POST['infant'] ) ) : '0';
	$check_in_out_date = isset( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
	$make_deposit      = isset( $_POST['deposit'] ) ? sanitize_text_field( wp_unslash( $_POST['deposit'] ) ) : '0';
	$tf_confirmation_details = ! empty( $_POST['booking_confirm'] ) ? wp_unslash( $_POST['booking_confirm'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	$product_id          = get_post_meta( $post_id, 'product_id', true );
	$post_author         = get_post_field( 'post_author', $post_id );
	$meta                = get_post_meta( $post_id, 'tf_apartment_opt', true );
	$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
	$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
	$quick_checkout      = ! empty( Helper::tfopt( 'tf-quick-checkout' ) ) ? Helper::tfopt( 'tf-quick-checkout' ) : 0;
	$instantio_is_active = 0;

	if( is_plugin_active('instantio/instantio.php') ){
		$instantio_is_active = 1;
	}

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		$additional_fees = ! empty( $meta['additional_fees'] ) ? $meta['additional_fees'] : array();
	} else {
		$additional_fee = ! empty( $meta['additional_fee'] ) ? $meta['additional_fee'] : 0;
		$fee_type       = ! empty( $meta['fee_type'] ) ? $meta['fee_type'] : '';
	}

	// Booking Type
	$tf_booking_type = 1;
	$tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = '';
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
		$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
		$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
		$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
	}

	$date_data    = tf_apartment_parse_check_in_out_date( $check_in_out_date );
	$check_in_out = $date_data['date_parts'];
	$check_in     = $date_data['check_in'];
	$check_out    = $date_data['check_out'];
	$days         = $date_data['days'];

	$validation_errors = tf_apartment_get_booking_validation_errors( $post_id, $adults, $children, $infant, $check_in_out, $meta );
	if ( ! empty( $validation_errors ) ) {
		$response['errors'] = $validation_errors;
	}

	/**
	 * If no errors then process
	 */
	if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

		$tf_apartment_data['tf_apartment_data']['order_type']        = 'apartment';
		$tf_apartment_data['tf_apartment_data']['post_id']           = $post_id;
		$tf_apartment_data['tf_apartment_data']['post_permalink']    = get_permalink( $post_id );
		$tf_apartment_data['tf_apartment_data']['post_author']       = $post_author;
		$tf_apartment_data['tf_apartment_data']['check_in_out_date'] = $check_in_out_date;
		$tf_apartment_data['tf_apartment_data']['adults']            = $adults;
		$tf_apartment_data['tf_apartment_data']['children']          = $children;
		$tf_apartment_data['tf_apartment_data']['infant']            = $infant;

		// Calculate price
		if ( $days > 0 ) {
			$total_price = tf_apartment_get_booking_total_price( $post_id, $check_in, $check_out, $adults, $children, $infant, $enable_availability );

			$tf_apartment_data['tf_apartment_data']['pricing_type'] = $pricing_type;
			$tf_apartment_data['tf_apartment_data']['total_price']  = $total_price;
		}

		if ( ! empty( $tf_apartment_data['tf_apartment_data']['total_price'] ) ) {
			$payable_info = tf_apartment_get_booking_payable_and_due( $meta, $tf_apartment_data['tf_apartment_data']['total_price'], $make_deposit, $tf_booking_type );
			if ( $payable_info['due'] > 0 ) {
				$tf_apartment_data['tf_apartment_data']['due'] = $payable_info['due'];
			}
			$tf_apartment_data['tf_apartment_data']['total_price'] = $payable_info['payable'];
		}

		if ( 3 == $tf_booking_type ) {
			$customer_details               = tf_apartment_get_without_payment_customer_details( $tf_confirmation_details );
			$without_payment_order_details  = array(
				'order_by'    => '',
				'check_in'    => $check_in,
				'check_out'   => $check_out,
				'adult'       => $adults,
				'child'       => $children,
				'infants'     => $infant,
				'total_price' => ! empty( $tf_apartment_data['tf_apartment_data']['total_price'] ) ? $tf_apartment_data['tf_apartment_data']['total_price'] : 0,
				'due_price'   => ! empty( $tf_apartment_data['tf_apartment_data']['due'] ) ? $tf_apartment_data['tf_apartment_data']['due'] : '',
			);
			$without_payment_order_data     = array(
				'post_id'          => $post_id,
				'post_type'        => 'apartment',
				'room_number'      => null,
				'check_in'         => $check_in,
				'check_out'        => $check_out,
				'billing_details'  => $customer_details['billing_details'],
				'shipping_details' => $customer_details['shipping_details'],
				'order_details'    => $without_payment_order_details,
				'payment_method'   => 'offline',
				'customer_id'      => tf_apartment_get_offline_customer_id(),
				'status'           => 'processing',
				'order_date'       => gmdate( 'Y-m-d H:i:s' ),
			);

			$order_id = Helper::tf_set_order( $without_payment_order_data );
			if ( ! empty( $order_id ) ) {
				$response['without_payment'] = 'true';
				$response['product_id']      = $product_id;
				$response['add_to_cart']     = 'true';

				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
					do_action( 'tf_offline_payment_booking_confirmation', $order_id, $without_payment_order_data );
					if (
						! empty( Helper::tf_data_types( Helper::tfopt( 'tf-integration' ) )['tf-new-order-google-calendar'] ) &&
						Helper::tf_data_types( Helper::tfopt( 'tf-integration' ) )['tf-new-order-google-calendar'] == '1'
					) {
						apply_filters( 'tf_after_booking_completed_calendar_data', $order_id, $without_payment_order_data, '' );
					}
				}
			} else {
				$response['status']   = 'error';
				$response['errors'][] = esc_html__( 'Unable to complete booking. Please try again.', 'tourfic' );
			}
		} elseif ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) ) {
			$external_search_info = array(
				'{adult}'    => $adults,
				'{child}'    => $children,
				'{infant}'   => $infant,
				'{checkin}'  => $check_in,
				'{checkout}' => $check_out,
			);
			if ( ! empty( $tf_booking_attribute ) ) {
				$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
				if ( ! empty( $tf_booking_query_url ) ) {
					$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
				}
			}

			$response['product_id']  = $product_id;
			$response['add_to_cart'] = 'true';
			$response['redirect_to'] = $tf_booking_url;
			$response['without_payment'] = 'false';
		} else {

			# Add product to cart with the custom cart item data
			$added_to_cart = WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_apartment_data );
			if ( ! $added_to_cart ) {
				$response['status']   = 'error';
				$response['errors'][] = esc_html__( 'Unable to add this apartment booking to cart. Please try again.', 'tourfic' );
			} else {
				$response['product_id']  = $product_id;
				$response['add_to_cart'] = 'true';
				$response['redirect_to'] = $instantio_is_active == 1 ? ( $quick_checkout == 0 ? wc_get_checkout_url() : '' ) : wc_get_checkout_url();
				$response['without_payment'] = 'false';
			}
		}
	} else {
		$response['status'] = 'error';
	}

	// Json Response
	echo wp_json_encode( $response );

	die();
}

/**
 * Apartment booking popup summary ajax callback.
 *
 * @return void
 */
function tf_apartment_booking_popup_callback() {
	if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
		return;
	}

	$response = array();

	$post_id           = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) ) : 0;
	$adults            = isset( $_POST['adults'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['adults'] ) ) ) : 0;
	$children          = isset( $_POST['children'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['children'] ) ) ) : 0;
	$infant            = isset( $_POST['infant'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['infant'] ) ) ) : 0;
	$check_in_out_date = isset( $_POST['check-in-out-date'] ) ? sanitize_text_field( wp_unslash( $_POST['check-in-out-date'] ) ) : '';
	$make_deposit      = isset( $_POST['deposit'] ) ? sanitize_text_field( wp_unslash( $_POST['deposit'] ) ) : '0';

	$meta                = get_post_meta( $post_id, 'tf_apartment_opt', true );
	$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
	$price_per_night     = ! empty( $meta['price_per_night'] ) ? (float) $meta['price_per_night'] : 0;
	$adult_price         = ! empty( $meta['adult_price'] ) ? (float) $meta['adult_price'] : 0;
	$child_price         = ! empty( $meta['child_price'] ) ? (float) $meta['child_price'] : 0;
	$infant_price        = ! empty( $meta['infant_price'] ) ? (float) $meta['infant_price'] : 0;
	$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
	$tf_booking_type     = 1;

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		$tf_booking_type = ! empty( $meta['booking-by'] ) ? intval( $meta['booking-by'] ) : 1;
	}

	$date_data    = tf_apartment_parse_check_in_out_date( $check_in_out_date );
	$check_in_out = $date_data['date_parts'];
	$check_in     = $date_data['check_in'];
	$check_out    = $date_data['check_out'];
	$days         = $date_data['days'];

	$validation_errors = tf_apartment_get_booking_validation_errors( $post_id, $adults, $children, $infant, $check_in_out, $meta );
	if ( ! empty( $validation_errors ) ) {
		$response['status'] = 'error';
		$response['errors'] = $validation_errors;
		echo wp_json_encode( $response );
		die();
	}

	$total_price = tf_apartment_get_booking_total_price( $post_id, $check_in, $check_out, $adults, $children, $infant, $enable_availability );
	if ( $total_price <= 0 ) {
		$response['status']   = 'error';
		$response['errors'][] = esc_html__( 'Unable to calculate booking total. Please try again.', 'tourfic' );
		echo wp_json_encode( $response );
		die();
	}

	$payable_info = tf_apartment_get_booking_payable_and_due( $meta, $total_price, $make_deposit, $tf_booking_type );
	$date_format  = ! empty( Helper::tfopt( 'tf-date-format-for-users' ) ) ? Helper::tfopt( 'tf-date-format-for-users' ) : 'Y/m/d';
	$summary      = '';

	if ( ! empty( $check_in ) && ! empty( $check_out ) ) {
		$summary .= '<h6>' . esc_html__( 'From ', 'tourfic' ) . esc_html( gmdate( $date_format, strtotime( $check_in ) ) ) . '</h6>';
		$summary .= '<h6>' . esc_html__( 'To ', 'tourfic' ) . esc_html( gmdate( $date_format, strtotime( $check_out ) ) ) . '</h6>';
	}

	$summary .= '
		<table class="table" style="width: 100%">
			<thead>
				<tr>
					<th align="left">' . esc_html__( 'Traveller', 'tourfic' ) . '</th>
					<th align="right">' . esc_html__( 'Price', 'tourfic' ) . '</th>
				</tr>
			</thead>
			<tbody>';

	if ( $days > 0 && 'per_night' === $pricing_type && $price_per_night > 0 && '1' !== $enable_availability ) {
		$summary .= '<tr>
			<td align="left">' . esc_html( $days ) . ' ' . esc_html__( 'nights', 'tourfic' ) . ' (' . wp_strip_all_tags( wc_price( $price_per_night ) ) . '/' . esc_html__( 'night', 'tourfic' ) . ')</td>
			<td align="right">' . wc_price( $price_per_night * $days ) . '</td>
		</tr>';
	}

	if ( $adults > 0 ) {
		$adult_total = ( 'per_person' === $pricing_type && '1' !== $enable_availability ) ? ( $adult_price * $adults * max( 1, $days ) ) : 0;
		$summary    .= '<tr>
			<td align="left">' . esc_html( $adults ) . ' ' . esc_html__( 'adults', 'tourfic' ) . '</td>
			<td align="right">' . ( $adult_total > 0 ? wc_price( $adult_total ) : '-' ) . '</td>
		</tr>';
	}

	if ( $children > 0 ) {
		$children_total = ( 'per_person' === $pricing_type && '1' !== $enable_availability ) ? ( $child_price * $children * max( 1, $days ) ) : 0;
		$summary       .= '<tr>
			<td align="left">' . esc_html( $children ) . ' ' . esc_html__( 'children', 'tourfic' ) . '</td>
			<td align="right">' . ( $children_total > 0 ? wc_price( $children_total ) : '-' ) . '</td>
		</tr>';
	}

	if ( $infant > 0 ) {
		$infant_total = ( 'per_person' === $pricing_type && '1' !== $enable_availability ) ? ( $infant_price * $infant * max( 1, $days ) ) : 0;
		$summary     .= '<tr>
			<td align="left">' . esc_html( $infant ) . ' ' . esc_html__( 'infants', 'tourfic' ) . '</td>
			<td align="right">' . ( $infant_total > 0 ? wc_price( $infant_total ) : '-' ) . '</td>
		</tr>';
	}

	$summary .= '<tr>
		<td align="left">' . esc_html__( 'Subtotal', 'tourfic' ) . '</td>
		<td align="right">' . wc_price( $total_price ) . '</td>
	</tr>';

	if ( ! empty( $payable_info['due'] ) ) {
		$summary .= '<tr>
			<td align="left">' . esc_html__( 'Due', 'tourfic' ) . '</td>
			<td align="right">' . wc_price( $payable_info['due'] ) . '</td>
		</tr>';
	}

	$summary .= '</tbody>
			<tfoot>
				<tr>
					<th align="left">' . esc_html__( 'Total', 'tourfic' ) . '</th>
					<th align="right">' . wc_price( $payable_info['payable'] ) . '</th>
				</tr>
			</tfoot>
		</table>';

	$response['status']          = 'success';
	$response['booking_summary'] = $summary;

	echo wp_json_encode( $response );
	die();
}

/**
 * Override WooCommerce Price
 * @author Foysal
 */
function tf_aprtment_set_order_price( $cart ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {

		if ( isset( $cart_item['tf_apartment_data']['total_price'] ) ) {
			$cart_item['data']->set_price( $cart_item['tf_apartment_data']['total_price'] );
		}
	}

}

add_action( 'woocommerce_before_calculate_totals', 'tf_aprtment_set_order_price', 30, 1 );

/*
 * Display custom cart item meta data (in cart and checkout)
 * @author Foysal
 */
function tf_apartment_cart_item_custom_meta_data( $item_data, $cart_item ) {

	if ( isset( $cart_item['tf_apartment_data']['adults'] ) && $cart_item['tf_apartment_data']['adults'] >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Adults', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['adults'],
		);
	}

	if ( isset( $cart_item['tf_apartment_data']['children'] ) && $cart_item['tf_apartment_data']['children'] >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Children', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['children'],
		);
	}

	if ( isset( $cart_item['tf_apartment_data']['infant'] ) && $cart_item['tf_apartment_data']['infant'] >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Infant', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['infant'],
		);
	}

	if ( isset( $cart_item['tf_apartment_data']['check_in_out_date'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Check-in-out', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['check_in_out_date'],
		);
	}

	if ( ! empty( $cart_item['tf_apartment_data']['due'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Due', 'tourfic' ),
			'value' => wp_strip_all_tags( wc_price( $cart_item['tf_apartment_data']['due'] ) ),
		);
	}

	return $item_data;

}

add_filter( 'woocommerce_get_item_data', 'tf_apartment_cart_item_custom_meta_data', 10, 2 );

/**
 * Change cart item permalink
 * @author Foysal
 */
function tf_apartment_cart_item_permalink( $permalink, $cart_item, $cart_item_key ) {

	$type = ! empty( $cart_item['tf_apartment_data']['order_type'] ) ? $cart_item['tf_apartment_data']['order_type'] : '';
	if ( is_cart() && $type == 'apartment' ) {
		$permalink = $cart_item['tf_apartment_data']['post_permalink'];
	}

	return $permalink;

}

add_filter( 'woocommerce_cart_item_permalink', 'tf_apartment_cart_item_permalink', 10, 3 );

/**
 * Show custom data in order details
 * @author Foysal
 */
function tf_apartment_custom_order_data( $item, $cart_item_key, $values, $order ) {

	// Assigning data into variables
	$order_type        = ! empty( $values['tf_apartment_data']['order_type'] ) ? $values['tf_apartment_data']['order_type'] : '';
	$post_author       = ! empty( $values['tf_apartment_data']['post_author'] ) ? $values['tf_apartment_data']['post_author'] : '';
	$post_id           = ! empty( $values['tf_apartment_data']['post_id'] ) ? $values['tf_apartment_data']['post_id'] : '';
	$adults            = ! empty( $values['tf_apartment_data']['adults'] ) ? $values['tf_apartment_data']['adults'] : '';
	$children          = ! empty( $values['tf_apartment_data']['children'] ) ? $values['tf_apartment_data']['children'] : '';
	$infant            = ! empty( $values['tf_apartment_data']['infant'] ) ? $values['tf_apartment_data']['infant'] : '';
	$check_in_out_date = ! empty( $values['tf_apartment_data']['check_in_out_date'] ) ? $values['tf_apartment_data']['check_in_out_date'] : '';
	$due               = ! empty( $values['tf_apartment_data']['due'] ) ? floatval( $values['tf_apartment_data']['due'] ) : null;

	/**
	 * Show data in order meta & email
	 */
	if ( $order_type ) {
		$item->update_meta_data( '_order_type', $order_type );
	}

	if ( $post_author ) {
		$item->update_meta_data( '_post_author', $post_author );
	}

	if ( $post_id ) {
		$item->update_meta_data( '_post_id', $post_id );
	}

	if ( $adults && $adults > 0 ) {
		$item->update_meta_data( 'adults', $adults );
	}

	if ( $children && $children > 0 ) {
		$item->update_meta_data( 'children', $children );
	}

	if ( $infant && $infant > 0 ) {
		$item->update_meta_data( 'infant', $infant );
	}

	if ( $check_in_out_date ) {
		$item->update_meta_data( 'check_in_out_date', $check_in_out_date );
	}

	if ( ! empty( $due ) ) {
		$item->update_meta_data( 'Due', wp_strip_all_tags( wc_price( $due ) ) );
		$item->update_meta_data( '_due_price', $due );
	}
}

add_action( 'woocommerce_checkout_create_order_line_item', 'tf_apartment_custom_order_data', 10, 4 );


/**
 * Add order id to the hotel room meta field
 *
 * runs during WooCommerce checkout process
 *
 * @author Jahid
 */
function tf_add_apartment_data_checkout_order_processed( $order_id, $posted_data, $order ) {

	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

		if ( "apartment" == $order_type ) {
			$post_id = $item->get_meta( '_post_id', true ); // Apartment id

			//Tax Calculation
			$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$tax_labels = array();
			if(!empty($meta['is_taxable'])){
				$single_price = $item->get_subtotal();
				$finding_location = array(
					'country' => !empty($order->get_billing_country()) ? $order->get_billing_country() : '',
					'state' => !empty($order->get_billing_state()) ? $order->get_billing_state() : '',
					'postcode' => !empty($order->get_billing_postcode()) ? $order->get_billing_postcode() : '',
					'city' => !empty($order->get_billing_city()) ? $order->get_billing_city() : '',
					'tax_class' => !empty($meta['taxable_class']) && "standard"!=$meta['taxable_class'] ? $meta['taxable_class'] : ''
				);
	
				$tax_rate = WC_Tax::find_rates( $finding_location );
				if(!empty($tax_rate)){
					foreach($tax_rate as $rate){
						$tf_vat =  (float)$single_price * $rate['rate'] / 100;
						$tax_labels [] = array(
							'label' => $rate['label'],
							'price' => $tf_vat
						);
					}
					
				}
			}
		
			$fee_sums = array();
			// Sum the prices for each label
			foreach ( $tax_labels as $fee ) {
				$label = $fee["label"];
				$price = $fee["price"];
				if ( isset( $fee_sums[ $label ] ) ) {
					$fee_sums[ $label ] += $price;
				} else {
					$fee_sums[ $label ] = $price;
				}
			}

			//Order Data Insert 
			$billinginfo = [
				'billing_first_name' => $order->get_billing_first_name(),
				'billing_last_name'  => $order->get_billing_last_name(),
				'billing_company'    => $order->get_billing_company(),
				'billing_address_1'  => $order->get_billing_address_1(),
				'billing_address_2'  => $order->get_billing_address_2(),
				'billing_city'       => $order->get_billing_city(),
				'billing_state'      => $order->get_billing_state(),
				'billing_postcode'   => $order->get_billing_postcode(),
				'billing_country'    => $order->get_billing_country(),
				'billing_email'      => $order->get_billing_email(),
				'billing_phone'      => $order->get_billing_phone()
			];

			$shippinginfo = [
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name'  => $order->get_shipping_last_name(),
				'shipping_company'    => $order->get_shipping_company(),
				'shipping_address_1'  => $order->get_shipping_address_1(),
				'shipping_address_2'  => $order->get_shipping_address_2(),
				'shipping_city'       => $order->get_shipping_city(),
				'shipping_state'      => $order->get_shipping_state(),
				'shipping_postcode'   => $order->get_shipping_postcode(),
				'shipping_country'    => $order->get_shipping_country(),
				'shipping_phone'      => $order->get_shipping_phone()
			];
		}

		// Apartment Item Data Insert
		if ( "apartment" == $order_type ) {
			$price             = $item->get_subtotal();
			$check_in_out_date = $item->get_meta( 'check_in_out_date', true );
			$adult             = $item->get_meta( 'adults', true );
			$child             = $item->get_meta( 'children', true );
			$infants           = $item->get_meta( 'infant', true );
			$due               = $item->get_meta( '_due_price', true );

			if ( $check_in_out_date ) {
				list( $check_in, $check_out ) = explode( ' - ', $check_in_out_date );
			}

			$iteminfo = [
				'check_in'    => $check_in,
				'check_out'   => $check_out,
				'adult'       => $adult,
				'child'       => $child,
				'infants'     => $infants,
				'total_price' => $price,
				'due_price'   => $due,
				'tax_info' => wp_json_encode($fee_sums)
			];

			$tf_integration_order_data[] = [
				'check_in'       => $check_in,
				'check_out'      => $check_out,
				'adult'          => $adult,
				'child'          => $child,
				'infants'        => $infants,
				'total_price'    => $price,
				'due_price'      => $due,
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

			$tf_integration_order_status = [
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

			$iteminfo_keys = array_keys( $iteminfo );
			$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

			$iteminfo_values = array_values( $iteminfo );
			$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

			$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}tf_order_data
				( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$check_in,
						$check_out,
						wp_json_encode( $billinginfo ),
						wp_json_encode( $shippinginfo ),
						wp_json_encode( $iteminfo ),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						gmdate( 'Y-m-d H:i:s' )
					)
				)
			);
		}

	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tf_integration_order_status ) ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
	}
}

add_action( 'woocommerce_checkout_order_processed', 'tf_add_apartment_data_checkout_order_processed', 10, 4 );



/**
 * Add order id to the apartment meta field
 * runs during WooCommerce checkout process for block checkout
 * @param $order
 * @return void
 * @since 2.11.10
 * @author Foysal
 */
function tf_add_apartment_data_checkout_order_processed_block_checkout( $order ) {

	$order_id = $order->get_id();
	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

		if ( "apartment" == $order_type ) {
			$post_id = $item->get_meta( '_post_id', true ); // Apartment id

			//Tax Calculation
			$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$tax_labels = array();
			if(!empty($meta['is_taxable'])){
				$single_price = $item->get_subtotal();
				$finding_location = array(
					'country' => !empty($order->get_billing_country()) ? $order->get_billing_country() : '',
					'state' => !empty($order->get_billing_state()) ? $order->get_billing_state() : '',
					'postcode' => !empty($order->get_billing_postcode()) ? $order->get_billing_postcode() : '',
					'city' => !empty($order->get_billing_city()) ? $order->get_billing_city() : '',
					'tax_class' => !empty($meta['taxable_class']) && "standard"!=$meta['taxable_class'] ? $meta['taxable_class'] : ''
				);
	
				$tax_rate = WC_Tax::find_rates( $finding_location );
				if(!empty($tax_rate)){
					foreach($tax_rate as $rate){
						$tf_vat =  (float)$single_price * $rate['rate'] / 100;
						$tax_labels [] = array(
							'label' => $rate['label'],
							'price' => $tf_vat
						);
					}
					
				}
			}
		
			$fee_sums = array();
			// Sum the prices for each label
			foreach ( $tax_labels as $fee ) {
				$label = $fee["label"];
				$price = $fee["price"];
				if ( isset( $fee_sums[ $label ] ) ) {
					$fee_sums[ $label ] += $price;
				} else {
					$fee_sums[ $label ] = $price;
				}
			}

			//Order Data Insert
			$billinginfo = [
				'billing_first_name' => $order->get_billing_first_name(),
				'billing_last_name'  => $order->get_billing_last_name(),
				'billing_company'    => $order->get_billing_company(),
				'billing_address_1'  => $order->get_billing_address_1(),
				'billing_address_2'  => $order->get_billing_address_2(),
				'billing_city'       => $order->get_billing_city(),
				'billing_state'      => $order->get_billing_state(),
				'billing_postcode'   => $order->get_billing_postcode(),
				'billing_country'    => $order->get_billing_country(),
				'billing_email'      => $order->get_billing_email(),
				'billing_phone'      => $order->get_billing_phone()
			];

			$shippinginfo = [
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name'  => $order->get_shipping_last_name(),
				'shipping_company'    => $order->get_shipping_company(),
				'shipping_address_1'  => $order->get_shipping_address_1(),
				'shipping_address_2'  => $order->get_shipping_address_2(),
				'shipping_city'       => $order->get_shipping_city(),
				'shipping_state'      => $order->get_shipping_state(),
				'shipping_postcode'   => $order->get_shipping_postcode(),
				'shipping_country'    => $order->get_shipping_country(),
				'shipping_phone'      => $order->get_shipping_phone()
			];
		}

		// Apartment Item Data Insert
		if ( "apartment" == $order_type ) {
			$price             = $item->get_subtotal();
			$check_in_out_date = $item->get_meta( 'check_in_out_date', true );
			$adult             = $item->get_meta( 'adults', true );
			$child             = $item->get_meta( 'children', true );
			$infants           = $item->get_meta( 'infant', true );
			$due               = $item->get_meta( '_due_price', true );

			if ( $check_in_out_date ) {
				list( $check_in, $check_out ) = explode( ' - ', $check_in_out_date );
			}

			$iteminfo = [
				'check_in'    => $check_in,
				'check_out'   => $check_out,
				'adult'       => $adult,
				'child'       => $child,
				'infants'     => $infants,
				'total_price' => $price,
				'due_price'   => $due,
				'tax_info' => wp_json_encode($fee_sums)
			];

			$tf_integration_order_data[] = [
				'check_in'       => $check_in,
				'check_out'      => $check_out,
				'adult'          => $adult,
				'child'          => $child,
				'infants'        => $infants,
				'total_price'    => $price,
				'due_price'      => $due,
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

			$tf_integration_order_status = [
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

			$iteminfo_keys = array_keys( $iteminfo );
			$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

			$iteminfo_values = array_values( $iteminfo );
			$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

			$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}tf_order_data
				( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$check_in,
						$check_out,
						wp_json_encode( $billinginfo ),
						wp_json_encode( $shippinginfo ),
						wp_json_encode( $iteminfo ),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						gmdate( 'Y-m-d H:i:s' )
					)
				)
			);
		}

	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tf_integration_order_status ) ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
	}
}

add_action( 'woocommerce_store_api_checkout_order_processed', 'tf_add_apartment_data_checkout_order_processed_block_checkout' );
