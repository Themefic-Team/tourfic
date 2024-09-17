<?php

namespace Tourfic\Admin\Backend_Booking;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use \Tourfic\Core\TF_Backend_Booking;
use \Tourfic\Classes\Apartment\Pricing as APT_Price;
use \Tourfic\Classes\Apartment\Apartment;

class TF_Apartment_Backend_Booking extends TF_Backend_Booking {

	use \Tourfic\Traits\Singleton;

	protected array $args = array(
		'name'      => 'apartment',
		'prefix'    => 'tf-apartment',
		'post_type' => 'tf_apartment',
		'caps'      => 'edit_tf_apartments'
	);

	public function __construct() {

		parent::__construct( $this->args );

		$this->set_settings_fields();

		// actions
		add_action( 'wp_ajax_tf_check_available_apartment', array( $this, 'check_avaibility_callback' ) );
		add_action( 'wp_ajax_tf_check_apartment_aditional_fees', array( $this, 'tf_check_apartment_aditional_fees_callback' ) );
		add_action( 'wp_ajax_tf_backend_apartment_booking', array( $this, 'backend_booking_callback' ) );
	}

	function set_settings_fields() {
		$this->settings = array(
			'tf_booking_fields' => array(
				'title'  => esc_html__( 'Booking Information', 'tourfic' ),
				'fields' => array(
					array(
						'id'      => 'tf_apartment_date',
						'label'   => esc_html__( 'Date', 'tourfic' ),
						'class'   => 'tf-field-class',
						'type'    => 'date',
						'format'  => 'Y/m/d',
						'range'   => true,
						'minDate' => 'today',
					),
					array(
						'id'          => 'tf_available_apartments',
						'label'       => esc_html__( 'Available Apartments', 'tourfic' ),
						'type'        => 'select2',
						'class'       => 'tf-field-class',
						'options'     => 'posts',
						'query_args'  => array(
							'post_type'      => $this->args['post_type'],
							'posts_per_page' => - 1,
							'post_status'    => 'publish',
						),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_apartment_additional_fees',
						'label'       => esc_html__( 'Additional Fees', 'tourfic' ),
						'class'       => 'tf-field-class',
						'type'        => 'select2',
						'options'     => 'posts',
						'attributes'  => array( 'disabled' => 'disabled' ),
						'placeholder' => esc_html__( 'Please choose the apartment first', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_apartment_adults_number',
						'label'       => esc_html__( 'Adults', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => 1,
						),
						'field_width' => 33,
					),
					array(
						'id'          => 'tf_apartment_children_number',
						'label'       => esc_html__( 'Children', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => '0',
						),
						'field_width' => 33,
					),
					array(
						'id'          => 'tf_apartment_infant_number',
						'label'       => esc_html__( 'Infant', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => '0',
						),
						'field_width' => 33,
					),
				),
			),
		);
		$this->set_settings( $this->settings );
	}

	public function tf_check_apartment_aditional_fees_callback() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : 0;
		$meta = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		$from         = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$to           = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

		$additional_fees = ! empty( $meta["additional_fees"] ) ? $meta["additional_fees"] : array();

		$all_fees = [];
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $additional_fees ) ) {
			if ( count( $additional_fees ) > 0 ) {
				foreach ( $additional_fees as $fees ) {
					$all_fees[] = array(
						"label" => ! empty( $fees["additional_fee_label"] ) ? $fees["additional_fee_label"] : '',
						"fee"   => ! empty( $fees["additional_fee"] ) ? $fees["additional_fee"] : 0,
						"price" => ! empty( $fees["additional_fee"] ) ? wc_price( $fees["additional_fee"] ) : wc_price( 0 ),
						"type"  => ! empty( $fees["fee_type"] ) ? $fees["fee_type"] : '',
					);
				}
			}
		} else {
			$additional_fee_label  = ! empty( $meta["additional_fee_label"] ) ? $meta["additional_fee_label"] : '';
			$additional_fee_amount = ! empty( $meta["additional_fee"] ) ? $meta["additional_fee"] : 0;
			$additional_fee_type   = ! empty( $meta["fee_type"] ) ? $meta["fee_type"] : '';

			if ( $additional_fee_amount != 0 ) {
				$all_fees[] = array(
					"label" => $additional_fee_label ?? "",
					"fee"   => $additional_fee_amount,
					"price" => wc_price( $additional_fee_amount ),
					"type"  => $additional_fee_type,
				);
			}
		}

		wp_reset_postdata();

		if ( ! empty( $all_fees ) ) {
			wp_send_json_success( array(
				'additional_fees' => $all_fees,
			) );
		} else {
			wp_send_json_error( array(
				'additional_fees' => array(
					'msg' => esc_html__( 'There are no additional fees', 'tourfic' )
				)
			) );
		}
	}

	public function apartment_day_diference_calculation( $check_in, $check_out ) {
		if ( ! empty( $check_in ) && ! empty( $check_out ) ) {
			$check_in_stt  = ! empty( $check_in ) ? strtotime( $check_in . ' +1 day' ) : 0;
			$check_out_stt = ! empty( $check_out ) ? strtotime( $check_out ) : 0;
			$days          = ! empty( $check_in_stt ) && ! empty( $check_out_stt ) ? ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 : 0;

			$tfperiod = new \DatePeriod(
				new \DateTime( $check_in . ' 00:00' ),
				new \DateInterval( 'P1D' ),
				new \DateTime( $check_out . ' 23:59' )
			);
		}

		return array(
			"days"   => ! empty( $days ) ? $days : 0,
			"period" => ! empty( $tfperiod ) ? $tfperiod : 0
		);
	}

	function get_total_apartment_price( $id, $check_in, $check_out, $adult_count, $child_count, $infant_count, $addional_fees ) {

		$adult_count  = ! empty( $adult_count ) ? $adult_count : 0;
		$child_count  = ! empty( $child_count ) ? $child_count : 0;
		$infant_count = ! empty( $infant_count ) ? $infant_count : 0;
		$day_diff     = ! empty( $this->apartment_day_diference_calculation( $check_in, $check_out ) ) ? $this->apartment_day_diference_calculation( $check_in, $check_out ) : 0;

		if ( ! empty( $id ) ) {

			$meta = get_post_meta( $id, 'tf_apartment_opt', true );

			$availability_switch    = ! empty( $meta["enable_availability"] ) ? $meta["enable_availability"] : '';
			$apt_availability       = ! empty( $meta["apt_availability"] ) ? $meta["apt_availability"] : '';
			$apartment_price_type   = ! empty( $meta["pricing_type"] ) ? $meta["pricing_type"] : 'per_night';
			$apartment_price        = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
			$apartment_adult_price  = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
			$apartment_child_price  = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
			$apartment_infant_price = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
			$discount_type          = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : 'none';
			$discount_amount        = ! empty( $meta['discount'] ) && $discount_type != 'none' ? $meta['discount'] : 0;
		}

		$apartment_pricing = 0;

		if ( $availability_switch === '1' && ! empty( $apt_availability ) && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$apartment_avail = json_decode( $apt_availability, true );

			if ( ! empty( $apartment_avail ) ) {
				foreach ( $apartment_avail as $date ) {
					if ( ! empty( $date["check_in"] ) && ( $date["check_in"] == $check_in ) ) {
						if ( $date['pricing_type'] == "per_night" ) {
							$apartment_pricing = ! empty( $date["price"] ) ? intval( (int) $date["price"] * $day_diff["days"] ) : 0;
						} else {
							$apartment_adult_price  = ! empty( $date["adult_price"] ) ? $date["adult_price"] : 0;
							$apartment_child_price  = ! empty( $date["child_price"] ) ? $date["child_price"] : 0;
							$apartment_infant_price = ! empty( $date["infant_price"] ) ? $date["infant_price"] : 0;

							$apartment_pricing = intval( ( ( (int) $apartment_adult_price * (int) $adult_count ) + ( (int) $apartment_child_price * (int) $child_count ) + ( (int) $apartment_infant_price * (int) $infant_count ) ) * $day_diff["days"] );
						}
					}
				}
			}
		} else {
			if ( ! empty( $apartment_price_type ) && $apartment_price_type == "per_night" ) {
				$apartment_pricing = intval( (int) $apartment_price * (int) $day_diff["days"] );
			} else {
				$apartment_pricing = intval( ( ( (int) $apartment_adult_price * (int) $adult_count ) + ( (int) $apartment_child_price * (int) $child_count ) + ( (int) $apartment_infant_price * (int) $infant_count ) ) * $day_diff["days"] );
			}
		}

		// Discount Calculation

		if ( ! empty( $discount_type ) && ! empty( $discount_amount ) ) {
			if ( $discount_type == "percent" ) {
				$apartment_pricing = ! empty( $apartment_pricing ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( $apartment_pricing - ( ( $apartment_pricing / 100 ) * $discount_amount ), 2 ) ) ) : 0;
			} else if ( $discount_type == "fixed" ) {
				$apartment_pricing = ! empty( $apartment_pricing ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( $apartment_pricing - $discount_amount ), 2 ) ) : 0;
			}
		}

		// additional fees Calculation 
		if ( is_array( $addional_fees ) ) {

			if ( $addional_fees && ! empty( $addional_fees ) && $apartment_pricing > 0 ) {
				foreach ( $addional_fees as $fees ) {
					if ( ! empty( $fees["additional_fee"] ) ) {
						if ( ! empty( $fees["fee_type"] ) && $fees["fee_type"] == "per_stay" ) {
							$apartment_pricing += $fees["additional_fee"];
						}
						if ( ! empty( $fees["fee_type"] ) && $fees["fee_type"] == "per_person" ) {
							$apartment_pricing += intval( (int) $fees["additional_fee"] * ( (int) $adult_count + (int) $child_count + (int) $infant_count ) );
						}
						if ( ! empty( $fees["fee_type"] ) && $fees["fee_type"] == "per_night" ) {
							$apartment_pricing += ! empty( $day_diff["days"] ) ? intval( (int) $fees["additional_fee"] * $day_diff["days"] ) : $fees["additional_fee"];
						}
					}
				}
			}
		}

		return $apartment_pricing;
	}

	function check_avaibility_callback() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$from         = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$to           = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

		$loop = new \WP_Query( array(
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );

		$period = '';
		if ( ! empty( $from ) && ! empty( $to ) ) {
			$period = new \DatePeriod(
				new \DateTime( $from ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $to ) ? $to : '23:59:59' )
			);
		}

		$check_in_out = "$from - $to";

		if ( $loop->have_posts() ) {
			$not_found = [];
			while ( $loop->have_posts() ) {
				$loop->the_post();
				Apartment::tf_filter_apartment_by_date( $period, $not_found, array( 1, 0, 0, $check_in_out ) );
			}

			$tf_total_filters = [];

			foreach ( $not_found as $filter_post ) {
				if ( $filter_post['found'] != 1 ) {
					$tf_total_filters[ $filter_post['post_id'] ] = get_the_title( $filter_post['post_id'] );
				}
			}
		}
		wp_reset_postdata();

		wp_send_json_success( array(
			'apartments' => $tf_total_filters,
		) );
	}

	function backend_booking_callback() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'tf_backend_booking_nonce_action', 'tf_backend_booking_nonce' );

		$response = array(
			'success' => false,
		);

		$field = [];
		foreach ( $_POST as $key => $value ) {
			if ( $key === 'tf_apartment_date' ) {
				$field[ $key ]['from'] = sanitize_text_field( $value['from'] );
				$field[ $key ]['to']   = sanitize_text_field( $value['to'] );
			} else {
				$field[ $key ] = $value;
			}
		}

		$required_fields = array(
			'tf_apartment_booked_by',
			'tf_customer_first_name',
			'tf_customer_email',
			'tf_customer_phone',
			'tf_customer_country',
			'tf_customer_address',
			'tf_customer_city',
			'tf_customer_state',
			'tf_customer_zip',
			'tf_apartment_date',
			'tf_available_apartments',
			'tf_apartment_adults_number'
		);

		foreach ( $required_fields as $required_field ) {
			if ( $required_field === 'tf_apartment_date' ) {
				if ( empty( $field[ $required_field ]['from'] ) ) {
					$response['fieldErrors'][ $required_field . '[from]_error' ] = esc_html__( 'The field is required', 'tourfic' );
				}
				if ( empty( $field[ $required_field ]['to'] ) ) {
					$response['fieldErrors'][ $required_field . '[to]_error' ] = esc_html__( 'The field is required', 'tourfic' );
				}
			} else {
				if ( empty( $field[ $required_field ] ) ) {
					$response['fieldErrors'][ $required_field . '_error' ] = esc_html__( 'The field is required', 'tourfic' );
				}
			}
		}

		$apt_id       = ! empty( $field['tf_available_apartments'] ) ? intval( $field['tf_available_apartments'] ) : 0;
		$adult_count  = ! empty( $field['tf_apartment_adults_number'] ) ? intval( $field['tf_apartment_adults_number'] ) : 0;
		$child_count  = ! empty( $field['tf_apartment_children_number'] ) ? intval( $field['tf_apartment_children_number'] ) : 0;
		$infant_count = ! empty( $field['tf_apartment_infant_number'] ) ? intval( $field['tf_apartment_infant_number'] ) : 0;
		$check_from   = ! empty( $field['tf_apartment_date']['from'] ) ? $field['tf_apartment_date']['from'] : '';
		$check_to     = ! empty( $field['tf_apartment_date']['to'] ) ? $field['tf_apartment_date']['to'] : '';
		$apt_data     = get_post_meta( $apt_id, 'tf_apartment_opt', true );

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$additional_fees = ! empty( $apt_data['additional_fees'] ) ? $apt_data['additional_fees'] : array();
		} else {
			$additional_fees [] = array(
				"additional_fee_label" => ! empty( $apt_data['additional_fee_label'] ) ? $apt_data['additional_fee_label'] : '',
				"additional_fee"       => ! empty( $apt_data['additional_fee'] ) ? $apt_data['additional_fee'] : 0,
				"fee_type"             => ! empty( $apt_data['fee_type'] ) ? $apt_data['fee_type'] : '',
			);
		}

		if( !empty( $apt_data["enable_availability"]) && $apt_data["enable_availability"] == 1 ) {
			// $total_price = $this->get_total_apartment_price( $apt_id, $check_from, $check_to, $adult_count, $child_count, $infant_count, $additional_fees );
			$total_price = APT_Price::instance( $apt_id )->set_dates( $check_from, $check_to)->set_persons( $adult_count, $child_count, $infant_count )->get_availability();
		} else {
			$total_price = APT_Price::instance( $apt_id )->set_dates( $check_from, $check_to)->set_persons( $adult_count, $child_count, $infant_count )->set_total_price()->get_total_price();
		}


		if ( $apt_data['max_adults'] < $adult_count ) {
			/* translators: %s max adults */
			$response['fieldErrors']['tf_apartment_adults_number_error'] = sprintf( esc_html__( "You can't book more than %s adults", 'tourfic' ), $apt_data['max_adults'] ? $apt_data['max_adults'] : 0 );
		}
		if ( $apt_data['max_children'] < $child_count ) {
			/* translators: %s max children */
			$response['fieldErrors']['tf_apartment_children_number_error'] = sprintf( esc_html__( "You can't book more than %s children", 'tourfic' ), $apt_data['max_children'] ? $apt_data['max_children'] : 0 );
		}
		if ( $apt_data['max_infants'] < $infant_count ) {
			/* translators: %s max infants */
			$response['fieldErrors']['tf_apartment_infant_number_error'] = sprintf( esc_html__( "You can't book more than %s infants", 'tourfic' ), $apt_data['max_infants'] ? $apt_data['max_infants'] : 0 );
		}

		if ( ! array_key_exists( "fieldErrors", $response ) || ! $response['fieldErrors'] ) {
			
			$billing_details  = array(
				'billing_first_name' => $field['tf_customer_first_name'],
				'billing_last_name'  => $field['tf_customer_last_name'],
				'billing_company'    => '',
				'billing_address_1'  => $field['tf_customer_address'],
				'billing_address_2'  => $field['tf_customer_address_2'],
				'billing_city'       => $field['tf_customer_city'],
				'billing_state'      => $field['tf_customer_state'],
				'billing_postcode'   => $field['tf_customer_zip'],
				'billing_country'    => $field['tf_customer_country'],
				'billing_email'      => $field['tf_customer_email'],
				'billing_phone'      => $field['tf_customer_phone'],
			);
			$shipping_details = array(
				'shipping_first_name' => $field['tf_customer_first_name'],
				'shipping_last_name'  => $field['tf_customer_last_name'],
				'shipping_company'    => '',
				'shipping_address_1'  => $field['tf_customer_address'],
				'shipping_address_2'  => $field['tf_customer_address_2'],
				'shipping_city'       => $field['tf_customer_city'],
				'shipping_state'      => $field['tf_customer_state'],
				'shipping_postcode'   => $field['tf_customer_zip'],
				'shipping_country'    => $field['tf_customer_country'],
				'shipping_phone'      => $field['tf_customer_phone'],
				'tf_email'            => $field['tf_customer_email'],
			);
			$order_details    = [
				'order_by'      => $field['tf_apartment_booked_by'],
				'check_in'      => $field['tf_apartment_date']['from'],
				'check_out'     => $field['tf_apartment_date']['to'],
				'adult'         => $field['tf_apartment_adults_number'],
				'child'         => $field['tf_apartment_children_number'],
				'infant'        => $field['tf_apartment_infant_number'],
				'children_ages' => '',
				'total_price'   => $total_price,
				'due_price'     => '',
			];

			$order_data = array(
				'post_id'          => intval( $field['tf_available_apartments'] ),
				'post_type'        => 'apartment',
				'room_number'      => null,
				'check_in'         => $check_from,
				'check_out'        => $check_to,
				'billing_details'  => $billing_details,
				'shipping_details' => $shipping_details,
				'order_details'    => $order_details,
				'payment_method'   => "offline",
				'status'           => 'processing',
				'order_date'       => gmdate( 'Y-m-d H:i:s' ),
			);

			Helper::tf_set_order( $order_data );

			$response['success'] = true;
			$response['message'] = esc_html__( 'Your booking has been successfully submitted.', 'tourfic' );
		}


		echo wp_json_encode( $response );
		die();
	}

	function check_price_callback() {
	}
}