<?php

namespace Tourfic\Admin\Backend_Booking;

defined( 'ABSPATH' ) || exit;

use Mpdf\Tag\Em;
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
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['apartment_id']) ) : 0;
		$meta = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		$from         = isset( $_POST['from'] ) ? sanitize_text_field( wp_unslash( $_POST['from']) ) : '';
		$to           = isset( $_POST['to'] ) ? sanitize_text_field( wp_unslash( $_POST['to']) ) : '';

		$additional_fees = ! empty( $meta["additional_fees"] ) ? $meta["additional_fees"] : array();

		$all_fees = [];
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


			if ( ! empty( $apartment_price_type ) && $apartment_price_type == "per_night" ) {
				$apartment_pricing = intval( (int) $apartment_price * (int) $day_diff["days"] );
			} else {
				$apartment_pricing = intval( ( ( (int) $apartment_adult_price * (int) $adult_count ) + ( (int) $apartment_child_price * (int) $child_count ) + ( (int) $apartment_infant_price * (int) $infant_count ) ) * $day_diff["days"] );
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
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['apartment_id']) ) : '';
		$from         = isset( $_POST['from'] ) ? sanitize_text_field( wp_unslash( $_POST['from']) ) : '';
		$to           = isset( $_POST['to'] ) ? sanitize_text_field( wp_unslash( $_POST['to']) ) : '';

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


		echo wp_json_encode( $response );
		die();
	}

	function check_price_callback() {
	}
}