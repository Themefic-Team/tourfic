<?php

namespace Tourfic\Admin\Backend_Booking;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Tourfic\Classes\Helper;
use \Tourfic\Core\TF_Backend_Booking;

class TF_Tour_Backend_Booking extends TF_Backend_Booking {

	use \Tourfic\Traits\Singleton;

	protected array $args = array(
		'name' => 'tour',
        'prefix' => 'tf-tour',
        'post_type' => 'tf_tours',
        'caps' => 'edit_tf_tourss',
	);

	// TODO: Need to change the Booked by field name tf_tour_booked_by to tf_tours_booked_by in js

	function set_settings_fields() {
		$this->settings = array(
			'tf_booking_fields'          => array(
				'title'  => esc_html__( 'Booking Information', 'tourfic' ),
				'fields' => array(
					array(
						'id'         => 'tf_available_tours',
						'label'      => esc_html__( 'Available Tours', 'tourfic' ),
						'type'       => 'select2',
						'options'    => 'posts',
						'query_args' => array(
							'post_type'      => 'tf_tours',
							'posts_per_page' => - 1,
							'post_status'    => 'publish',
						),
					),
					array(
						'id'    => 'tf_tour_date',
						'label' => esc_html__( 'Date', 'tourfic' ),
						'type'  => 'date',
						'minDate' => 'today',
						'format'  => 'Y/m/d',
					),
					array(
						'id'          => 'tf_tour_adults_number',
						'label'       => esc_html__( 'Adults', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => '0',
						),
						'field_width' => 33.33,
					),
					array(
						'id'          => 'tf_tour_children_number',
						'label'       => esc_html__( 'Children', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => '0',
						),
						'field_width' => 33.33,
					),
					array(
						'id'          => 'tf_tour_infants_number',
						'label'       => esc_html__( 'Infants', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => '0',
						),
						'field_width' => 33.33,
					),
					array(
						'id'      => 'tf-pro-notice',
						'type'    => 'notice',
						'class'   => 'tf-pro-notice',
						'notice'  => 'info',
						'icon'    => 'ri-information-fill',
						'content' => wp_kses_post( __( 'We\'re offering some extra booking features like <b>tour time</b> and <b>tour extra features</b> in our pro plan. <a href="https://tourfic.com/" target="_blank"> Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' ) ),
					),
				),
			),
		);

		$tf_tour_time = array(
			'id'          => 'tf_tour_time',
			'label'       => esc_html__( 'Tour Time', 'tourfic' ),
			'type'        => 'select',
			'options'     => array(
				'10:00 AM' => '10:00 AM',
			),
			'placeholder' => esc_html__( 'Select Time', 'tourfic' ),
			'field_width' => 50,
		);

		$tf_tour_extras = array(
			'id'          => 'tf_tour_extras',
			'label'       => esc_html__( 'Tour Extras', 'tourfic' ),
			'type'        => 'select2',
			'multiple'    => true,
			'options'     => 'posts',
			'attributes'  => array(
				'disabled' => 'disabled',
			),
			'field_width' => 50,
		);

		if( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			array_pop( $this->settings['tf_booking_fields']['fields']);
			array_push( $this->settings['tf_booking_fields']['fields'], $tf_tour_time );
			array_push( $this->settings['tf_booking_fields']['fields'], $tf_tour_extras );
		}


		$this->set_settings( $this->settings);
	}

	public function __construct() {
		$this->set_settings_fields();

		parent::__construct($this->args);

		add_action( 'wp_ajax_tf_tour_date_time_update', array( $this, 'tf_tour_date_time_update' ) );
		add_action( 'wp_ajax_tf_backend_tour_booking', array( $this, 'backend_booking_callback' ) );
	}

	public function tf_tour_date_time_update() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$tour_id      = isset( $_POST['tour_id'] ) ? sanitize_text_field( $_POST['tour_id'] ) : '';
		$meta         = get_post_meta( $tour_id, 'tf_tours_opt', true );
		$tour_type    = ! empty( $meta['type'] ) ? $meta['type'] : '';
		$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : '';

		// Same Day Booking
		$disable_same_day = ! empty( $meta['disable_same_day'] ) ? $meta['disable_same_day'] : '';
		if ( $tour_type == 'fixed' ) {
			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_fixed_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_fixed_date  = unserialize( $tf_tour_fixed_avail );
				$departure_date      = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
				$return_date         = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
				$min_people          = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
				$max_people          = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
			} else {
				$departure_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
				$return_date    = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
				$min_people     = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
				$max_people     = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
			}

		} elseif ( $tour_type == 'continuous' ) {

			$disabled_day  = ! empty( $meta['disabled_day'] ) ? $meta['disabled_day'] : '';
			$disable_range = ! empty( $meta['disable_range'] ) ? $meta['disable_range'] : '';
			if ( ! empty( $disable_range ) && gettype( $disable_range ) == "string" ) {
				$disable_range_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $disable_range );
				$disable_range          = unserialize( $disable_range_unserial );
			}
			$disable_specific = ! empty( $meta['disable_specific'] ) ? $meta['disable_specific'] : '';
			$disable_specific = str_replace( ', ', '", "', $disable_specific );

			if ( $custom_avail == true ) {

				$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';

				if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
					$cont_custom_date_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $cont_custom_date );
					$cont_custom_date          = unserialize( $cont_custom_date_unserial );
				}
			}
		}

		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$group_price          = ! empty( $meta['group_price'] ) ? $meta['group_price'] : false;
		$adult_price          = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
		$child_price          = ! empty( $meta['child_price'] ) ? $meta['child_price'] : false;
		$infant_price         = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
		$tour_extras          = isset( $meta['tour-extra'] ) ? $meta['tour-extra'] : null;
		if ( ! empty( $tour_extras ) && gettype( $tour_extras ) == "string" ) {

			$tour_extras_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $tour_extras );
			$tour_extras          = unserialize( $tour_extras_unserial );
		}
		$allowed_times = [];
		if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {

			$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['cont_custom_date'] );
			$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );

			if ( ! empty( $tf_tour_unserial_custom_date ) ) {
				if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						return $times[] = [
							'date'  => $v['date'],
							'times' => array_map( function ( $v ) {
								return $v['time'];
							}, $v['allowed_time'] ?? [] )
						];
					}, $tf_tour_unserial_custom_date );
				}
			}
		} else {
			if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					if ( ! empty( $v['date'] ) ) {
						return $times[] = [
							'date'  => $v['date'],
							'times' => array_map( function ( $v ) {
								return $v['time'];
							}, $v['allowed_time'] ?? [] )
						];
					}
				}, $meta['cont_custom_date'] );
			}
		}

		if ( ! empty( $meta['allowed_time'] ) && gettype( $meta['allowed_time'] ) == "string" ) {

			$tf_tour_unserial_custom_time = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['allowed_time'] );
			$tf_tour_unserial_custom_time = unserialize( $tf_tour_unserial_custom_time );
			if ( ! empty( $tf_tour_unserial_custom_time ) ) {
				if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						return $v['time'];
					}, $tf_tour_unserial_custom_time ?? [] );
				}
			}
		} else {
			if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					return $v['time'];
				}, $meta['allowed_time'] ?? [] );
			}
		}
		// Single Template Check
		$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
		if ( "single" == $tf_tour_layout_conditions ) {
			$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
		}
		$tf_tour_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';

		$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;

		$tf_tour_selected_template = $tf_tour_selected_check;

		if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {

			$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['cont_custom_date'] );
			$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );

			if ( ! empty( $tf_tour_unserial_custom_date ) ) {
				if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						return $times[] = [
							'date'  => $v['date'],
							'times' => array_map( function ( $v ) {
								return $v['time'];
							}, $v['allowed_time'] ?? [] )
						];
					}, $tf_tour_unserial_custom_date );
				}
			}

		} else {
			if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					if ( ! empty( $v['date'] ) ) {
						return $times[] = [
							'date'  => $v['date'],
							'times' => array_map( function ( $v ) {
								return $v['time'];
							}, $v['allowed_time'] ?? [] )
						];
					}
				}, $meta['cont_custom_date'] );
			}

		}

		if ( ! empty( $meta['allowed_time'] ) && gettype( $meta['allowed_time'] ) == "string" ) {

			$tf_tour_unserial_custom_time = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['allowed_time'] );
			$tf_tour_unserial_custom_time = unserialize( $tf_tour_unserial_custom_time );
			if ( ! empty( $tf_tour_unserial_custom_time ) ) {
				if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						return $v['time'];
					}, $tf_tour_unserial_custom_time ?? [] );
				}
			}
		} else {
			if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					return $v['time'];
				}, $meta['allowed_time'] ?? [] );
			}
		}
		$tour_extras_select_array = [];
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_extras ) {
			if (
				( ! empty( $tour_extras[0]['title'] ) && ! empty( $tour_extras[0]['desc'] ) && ! empty( $tour_extras[0]['price'] ) ) ||
				( ! empty( $tour_extras[1]['title'] ) && ! empty( $tour_extras[1]['desc'] ) && ! empty( $tour_extras[1]['price'] ) )
			) {
				foreach ( $tour_extras as $extrakey => $tour_extra ) {
					$pricetype                             = ! empty( $tour_extra['price_type'] ) ? $tour_extra['price_type'] : 'fixed';
					$tour_extra_pricetype                  = $pricetype === "fixed" ? esc_html( "(Fixed Price)" ) : esc_html( "(Per Person Price)" );
					$tour_extras_select_array[ $extrakey ] = $tour_extra['title'] . $tour_extra_pricetype . ' - ' . wp_strip_all_tags( wc_price( $tour_extra['price'] ) );
				}
			}
		}

		echo wp_json_encode( array(
			'tour_type'                 => $tour_type,
			'custom_avail'              => $custom_avail,
			'cont_custom_date'          => $meta['cont_custom_date'],
			'departure_date'            => $departure_date,
			'return_date'               => $return_date,
			'min_people'                => $min_people,
			'max_people'                => $max_people,
			'disabled_day'              => $disabled_day,
			'disable_range'             => $disable_range,
			'disable_specific'          => $disable_specific,
			'disable_specific2'         => explode( ',', $meta['disable_specific'] ),
			'disable_same_day'          => $disable_same_day,
			'disable_adult_price'       => $disable_adult_price,
			'disable_child_price'       => $disable_child_price,
			'disable_infant_price'      => $disable_infant_price,
			'pricing_rule'              => $pricing_rule,
			'group_price'               => $group_price,
			'adult_price'               => $adult_price,
			'child_price'               => $child_price,
			'infant_price'              => $infant_price,
			'tour_extras_array'         => $tour_extras_select_array,
			'allowed_times'             => $allowed_times,
			'tf_tour_selected_template' => $tf_tour_selected_template,
		) );

		wp_die();
	}

    function backend_booking_callback(){
		// Add nonce for security and authentication.
		check_ajax_referer( 'tf_backend_booking_nonce_action', 'tf_backend_booking_nonce' );

		$response = array(
			'success' => false,
		);

		$field = [];
		foreach ( $_POST as $key => $value ) {
			$field[ $key ] = $value;
		}

		$required_fields = array(
			'tf_tours_booked_by',
			'tf_customer_first_name',
			'tf_customer_email',
			'tf_customer_phone',
			'tf_customer_country',
			'tf_customer_address',
			'tf_customer_city',
			'tf_customer_state',
			'tf_customer_zip',
			'tf_tour_date',
			'tf_available_tours',
			'tf_tour_adults_number'
		);


		foreach ( $required_fields as $required_field ) {
			if ( empty( $field[ $required_field ] ) ) {
				$response['fieldErrors'][ $required_field . '_error' ] = esc_html__( 'The field is required', 'tourfic' );
			}
		}

		if ( ! array_key_exists( "fieldErrors", $response ) || ! $response['fieldErrors'] ) {
			$res              = $this->tf_get_tour_total_price( intval( $field['tf_available_tours'] ), $field['tf_tour_date'], $field['tf_tour_time'] ?? '', $field['tf_tour_extras'] ?? '', intval( $field['tf_tour_adults_number'] ), intval( $field['tf_tour_children_number'] ), intval( $field['tf_tour_infants_number'] ) );
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

			if ( $field['tf_tour_date'] ) {
				list( $tour_in, $tour_out ) = explode( ' - ', $field['tf_tour_date'] );
			}

			$order_details = [
				'order_by'    => $field['tf_tour_booked_by'],
				'tour_date'   => $res['tour_date'],
				'tour_time'   => $res['tf_tour_time_title'],
				'tour_extra'  => $res['tf_tour_extra_title'],
				'adult'       => $field['tf_tour_adults_number'],
				'child'       => $field['tf_tour_children_number'],
				'infants'     => $field['tf_tour_infants_number'],
				'total_price' => $res['tf_tour_price'],
				'due_price'   => '',
			];

			$order_data = array(
				'post_id'          => intval( $field['tf_available_tours'] ),
				'post_type'        => 'tour',
				'room_number'      => null,
				'check_in'         => $tour_in,
				'check_out'        => $tour_out,
				'billing_details'  => $billing_details,
				'shipping_details' => $shipping_details,
				'order_details'    => $order_details,
				'payment_method'   => "offline",
				'status'           => 'processing',
				'order_date'       => gmdate( 'Y-m-d H:i:s' ),
			);
			if ( ! array_key_exists( 'errors', $res['response'] ) || count( $res['response']['errors'] ) == 0 ) {
				Helper::tf_set_order( $order_data );
				$response['success'] = true;
				$response['message'] = esc_html__( 'Your booking has been successfully submitted.', 'tourfic' );
			} else {
				$response['errors'] = $res['response']['errors'];
			}
		}

		echo wp_json_encode( $response );
		die();
	}

	public function tf_get_tour_total_price( $post_id, $tour_date, $tour_time, $tours_extra, $adults, $children, $infant ) {
		$response = array();

		$meta                 = get_post_meta( $post_id, 'tf_tours_opt', true );
		$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

		// People number
		$total_people         = $adults + $children + $infant;
		$total_people_booking = $adults + $children;

		/**
		 * If fixed is selected but pro is not activated
		 * show error
		 * @return
		 */
		if ( $tour_type == 'fixed' && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
			$response['errors'][] = esc_html__( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
			$response['status']   = 'error';
			echo wp_json_encode( $response );
			die();

			return;
		}

		if ( $tour_type == 'fixed' ) {

			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_fixed_avail   = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_fixed_date    = unserialize( $tf_tour_fixed_avail );
				$start_date            = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
				$end_date              = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
				$min_people            = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
				$max_people            = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
				$tf_tour_booking_limit = ! empty( $tf_tour_fixed_date['max_capacity'] ) ? $tf_tour_fixed_date['max_capacity'] : 0;
			} else {
				$start_date            = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
				$end_date              = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
				$min_people            = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
				$max_people            = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
				$tf_tour_booking_limit = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : 0;
			}


			// Fixed tour maximum capacity limit

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $start_date ) && ! empty( $end_date ) ) {

				// Tour Order retrieve from Tourfic Order Table
				$tf_orders_select    = array(
					'select'    => "post_id,order_details",
					'post_type' => 'tour',
					'query'     => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = Helper::tourfic_order_table_data( $tf_orders_select );

				$tf_total_adults    = 0;
				$tf_total_childrens = 0;

				foreach ( $tf_tour_book_orders as $order ) {
					$tour_id       = $order['post_id'];
					$order_details = json_decode( $order['order_details'] );
					$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
					list( $tf_booking_start, $tf_booking_end ) = explode( " - ", $tf_tour_date );
					if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_booking_start ) && $start_date == $tf_booking_start && ! empty( $tf_booking_end ) && $end_date == $tf_booking_end ) {
						$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
						if ( ! empty( $book_adult ) ) {
							list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
							$tf_total_adults += $tf_total_adult;
						}

						$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
						if ( ! empty( $book_children ) ) {
							list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
							$tf_total_childrens += $tf_total_children;
						}
					}
				}

				$tf_total_people = $tf_total_adults + $tf_total_childrens;

				if ( ! empty( $tf_tour_booking_limit ) ) {
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;
					if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Tour', 'tourfic' );
					}
					if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
						// translators: %1$s is the number of available seats */
						$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Tour', 'tourfic' ), $tf_today_limit );
					}
				}
			}

		} elseif ( $tour_type == 'continuous' ) {

			$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

			if ( $custom_avail == true ) {

				$pricing_rule     = $meta['custom_pricing_by'];
				$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
				if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
					$tf_tour_conti_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $cont_custom_date );
					$cont_custom_date    = unserialize( $tf_tour_conti_avail );
				}

			} elseif ( $custom_avail == false ) {

				$min_people          = ! empty( $meta['cont_min_people'] ) ? $meta['cont_min_people'] : '';
				$max_people          = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : '';
				$allowed_times_field = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';


				// Daily Tour Booking Capacity && Tour Order retrive from Tourfic Order Table
				$tf_orders_select    = array(
					'select'    => "post_id,order_details",
					'post_type' => 'tour',
					'query'     => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = Helper::tourfic_order_table_data( $tf_orders_select );

				$tf_total_adults    = 0;
				$tf_total_childrens = 0;

				if ( empty( $allowed_times_field ) || $tour_time == null ) {
					$tf_tour_booking_limit = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : 0;

					foreach ( $tf_tour_book_orders as $order ) {
						$tour_id       = $order['post_id'];
						$order_details = json_decode( $order['order_details'] );
						$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
						$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

						if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
							$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
							if ( ! empty( $book_adult ) ) {
								list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
								$tf_total_adults += $tf_total_adult;
							}

							$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
							if ( ! empty( $book_children ) ) {
								list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
								$tf_total_childrens += $tf_total_children;
							}
						}
					}
				} else {
					if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
						$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
					}

					if ( ! empty( $allowed_times_field[ $tour_time ]['cont_max_capacity'] ) ) {
						$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['cont_max_capacity'];

						foreach ( $tf_tour_book_orders as $order ) {
							$tour_id       = $order['post_id'];
							$order_details = json_decode( $order['order_details'] );
							$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
							$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

							if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
								$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
								if ( ! empty( $book_adult ) ) {
									list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
									$tf_total_adults += $tf_total_adult;
								}

								$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
								if ( ! empty( $book_children ) ) {
									list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
									$tf_total_childrens += $tf_total_children;
								}
							}
						}
					}
				}
				$tf_total_people = $tf_total_adults + $tf_total_childrens;

				if ( ! empty( $tf_tour_booking_limit ) ) {
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

					if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
					}
					if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
						// translators: %1$s is the number of available seats */
						$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
					}
				}
			}

		}

		/**
		 * If continuous custom availability is selected but pro is not activated
		 *
		 * Show error
		 *
		 * @return
		 */
		if ( $tour_type == 'continuous' && $custom_avail == true && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
			$response['errors'][] = esc_html__( 'Custom Continous Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
			$response['status']   = 'error';
			echo wp_json_encode( $response );
			die();

			return;
		}


		if ( $tour_type == 'continuous' ) {
			$start_date = $end_date = $tour_date;
		}

		// Tour extra
		$tour_extra_total     = 0;
		$tour_extra_title_arr = [];
		$tour_extra_meta      = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
		if ( ! empty( $tour_extra_meta ) ) {
			foreach ( $tours_extra as $extra ) {
				$tour_extra_pricetype = ! empty( $tour_extra_meta[ $extra ]['price_type'] ) ? $tour_extra_meta[ $extra ]['price_type'] : 'fixed';
				if ( $tour_extra_pricetype == "fixed" ) {
					if ( ! empty( $tour_extra_meta[ $extra ]['title'] ) && ! empty( $tour_extra_meta[ $extra ]['price'] ) ) {
						$tour_extra_total       += $tour_extra_meta[ $extra ]['price'];
						$tour_extra_title_arr[] = $tour_extra_meta[ $extra ]['title'] . " (Fixed: " . wp_strip_all_tags( wc_price( $tour_extra_meta[ $extra ]['price'] ) ) . ")";
					}
				} else {
					if ( ! empty( $tour_extra_meta[ $extra ]['price'] ) && ! empty( $tour_extra_meta[ $extra ]['title'] ) ) {
						$tour_extra_total       += ( $tour_extra_meta[ $extra ]['price'] * $total_people );
						$tour_extra_title_arr[] = $tour_extra_meta[ $extra ]['title'] . " (Per Person: " . wp_strip_all_tags( wc_price( $tour_extra_meta[ $extra ]['price'] ) ) . '*' . $total_people . "=" . wp_strip_all_tags( wc_price( $tour_extra_meta[ $extra ]['price'] * $total_people ) ) . ")";
					}
				}
			}
		}

		$tour_extra_title = ! empty( $tour_extra_title_arr ) ? implode( ",", $tour_extra_title_arr ) : '';

		/**
		 * People 0 number validation
		 */
		if ( $total_people == 0 ) {
			$response['errors'][] = esc_html__( 'Please Select Adults/Children/Infant required', 'tourfic' );
		}

		/**
		 * People number validation
		 *
		 */
		if ( $tour_type == 'fixed' ) {

			/* translators: %s minimum people */
			$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );

			/* translators: %s maximum people */
			$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

			if ( $total_people < $min_people && $min_people > 0 ) {
				/* translators: %s minimum people */
				$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

			} else if ( $total_people > $max_people && $max_people > 0 ) {
				/* translators: %s maximum people */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

			}

		} elseif ( $tour_type == 'continuous' && $custom_avail == false ) {

			/* translators: %s minimum people */
			$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );

			/* translators: %s maximum people */
			$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

			if ( $total_people < $min_people && $min_people > 0 ) {
				/* translators: %s minimum people */
				$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

			} else if ( $total_people > $max_people && $max_people > 0 ) {
				/* translators: %s maximum people */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

			}

		} elseif ( $tour_type == 'continuous' && $custom_avail == true ) {

			foreach ( $cont_custom_date as $item ) {

				// Backend continuous date values
				$back_date_from     = ! empty( $item['date']['from'] ) ? $item['date']['from'] : '';
				$back_date_to       = ! empty( $item['date']['from'] ) ? $item['date']['to'] : '';
				$back_date_from_stt = strtotime( str_replace( '/', '-', $back_date_from ) );
				$back_date_to_stt   = strtotime( str_replace( '/', '-', $back_date_to ) );
				// frontend selected date value
				$front_date = strtotime( str_replace( '/', '-', $tour_date ) );
				// Backend continuous min/max people values
				$min_people = ! empty( $item['min_people'] ) ? $item['min_people'] : '';
				$max_people = ! empty( $item['max_people'] ) ? $item['max_people'] : '';

				/* translators: %s minimum people */
				$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );

				/* translators: %s maximum people */
				$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


				// Compare backend & frontend date values to show specific people number error
				if ( $front_date >= $back_date_from_stt && $front_date <= $back_date_to_stt ) {
					if ( $total_people < $min_people && $min_people > 0 ) {
						/* translators: %1$s minimum people, %2$s date from, %3$s date to */
						$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ), $min_text, $back_date_from, $back_date_to );
					}
					if ( $total_people > $max_people && $max_people > 0 ) {
						/* translators: %1$s maximum people, %2$s date from, %3$s date to */
						$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ), $max_text, $back_date_from, $back_date_to );
					}

					$allowed_times_field = ! empty( $item['allowed_time'] ) ? $item['allowed_time'] : '';

					// Daily Tour Booking Capacity && tour order retrive form tourfic order table
					$tf_orders_select    = array(
						'select'    => "post_id,order_details",
						'post_type' => 'tour',
						'query'     => " AND ostatus = 'completed' ORDER BY order_id DESC"
					);
					$tf_tour_book_orders = Helper::tourfic_order_table_data( $tf_orders_select );

					$tf_total_adults    = 0;
					$tf_total_childrens = 0;

					if ( empty( $allowed_times_field ) || $tour_time == null ) {
						$tf_tour_booking_limit = ! empty( $item['max_capacity'] ) ? $item['max_capacity'] : '';

						foreach ( $tf_tour_book_orders as $order ) {
							$tour_id       = $order['post_id'];
							$order_details = json_decode( $order['order_details'] );
							$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
							$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

							if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
								$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
								if ( ! empty( $book_adult ) ) {
									list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
									$tf_total_adults += $tf_total_adult;
								}

								$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
								if ( ! empty( $book_children ) ) {
									list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
									$tf_total_childrens += $tf_total_children;
								}
							}
						}

					} else {
						if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
							$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
						}

						if ( ! empty( $allowed_times_field[ $tour_time ]['max_capacity'] ) ) {
							$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['max_capacity'];

							foreach ( $tf_tour_book_orders as $order ) {
								$tour_id       = $order['post_id'];
								$order_details = json_decode( $order['order_details'] );
								$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
								$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

								if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
									$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
									if ( ! empty( $book_adult ) ) {
										list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
										$tf_total_adults += $tf_total_adult;
									}

									$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
									if ( ! empty( $book_children ) ) {
										list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
										$tf_total_childrens += $tf_total_children;
									}
								}
							}
						}
					}
					$tf_total_people = $tf_total_adults + $tf_total_childrens;

					if ( ! empty( $tf_tour_booking_limit ) ) {
						$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

						if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
							$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
						}
						if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
							// translators: %1$s is the number of available seats */
							$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
						}
					}
				}
			}
		}

		/**
		 * Check errors
		 */
		/* Minimum days to book before departure */
		$min_days_before_book = ! empty( $meta['min_days_before_book'] ) ? $meta['min_days_before_book'] : '0';
		/* translators: %s minimum days before booking */
		$min_days_before_book_text = sprintf( _n( '%s day', '%s days', $min_days_before_book, 'tourfic' ), $min_days_before_book );
		$today_stt                 = new \DateTime( gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d' ) ) ) );
		$tour_date_stt             = new \DateTime( gmdate( 'Y-m-d', strtotime( $start_date ) ) );
		$day_difference            = $today_stt->diff( $tour_date_stt )->days;


		if ( $day_difference < $min_days_before_book ) {
			// translators: %1$s is the number of days */
			$response['errors'][] = sprintf( esc_html__( 'Present date to booking date required minimum %1$s gap', 'tourfic' ), $min_days_before_book_text );
		}
		if ( ! $start_date ) {
			$response['errors'][] = esc_html__( 'You must select booking date', 'tourfic' );
		}
		if ( ! $post_id ) {
			$response['errors'][] = esc_html__( 'Unknown Error! Please try again.', 'tourfic' );
		}

		/**
		 * Price by date range
		 *
		 * Tour type continuous and custom availability is true
		 */
		$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
		if ( ! empty( $tf_cont_custom_date ) && gettype( $tf_cont_custom_date ) == "string" ) {
			$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $tf_cont_custom_date );
			$tf_cont_custom_date       = unserialize( $tf_tour_conti_custom_date );
		}

		$tour = strtotime( $tour_date );
		if ( isset( $custom_avail ) && true == $custom_avail ) {
			$seasional_price = array_values( array_filter( $tf_cont_custom_date, function ( $value ) use ( $tour ) {
				$seasion_start = strtotime( $value['date']['from'] );
				$seasion_end   = strtotime( $value['date']['to'] );

				return $seasion_start <= $tour && $seasion_end >= $tour;
			} ) );
		}


		if ( $tour_type === 'continuous' && ! empty( $tf_cont_custom_date ) && ! empty( $seasional_price ) ) {
			$group_price    = $seasional_price[0]['group_price'];
			$adult_price    = $seasional_price[0]['adult_price'];
			$children_price = $seasional_price[0]['child_price'];
			$infant_price   = $seasional_price[0]['infant_price'];
		} else {
			$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
			$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
			$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
			$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
		}

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type == 'continuous' ) {
			$tf_allowed_times = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
			if ( ! empty( $tf_allowed_times ) && gettype( $tf_allowed_times ) == "string" ) {
				$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $tf_allowed_times );
				$tf_allowed_times          = unserialize( $tf_tour_conti_custom_date );
			}

			if ( $custom_avail == false && ! empty( $tf_allowed_times ) && empty( $tour_time_title ) ) {
				$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
			}
			if ( $custom_avail == true && ! empty( $seasional_price[0]['allowed_time'] ) && empty( $tour_time_title ) ) {
				$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
			}
		}

		if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'person' ) {

			if ( ! $disable_adult_price && $adults > 0 && empty( $adult_price ) ) {
				$response['errors'][] = esc_html__( 'Adult price is blank!', 'tourfic' );
			}
			if ( ! $disable_child_price && $children > 0 && empty( $children_price ) ) {
				$response['errors'][] = esc_html__( 'Childern price is blank!', 'tourfic' );
			}
			if ( ! $disable_infant_price && $infant > 0 && empty( $infant_price ) ) {
				$response['errors'][] = esc_html__( 'Infant price is blank!', 'tourfic' );
			}
			if ( $infant > 0 && ! empty( $infant_price ) && ! $adults ) {
				$response['errors'][] = esc_html__( 'Infant without adults is not allowed!', 'tourfic' );
			}

		} else if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'group' ) {

			if ( empty( $group_price ) ) {
				$response['errors'][] = esc_html__( 'Group price is blank!', 'tourfic' );
			}
		}

		/**
		 * If no errors then process
		 *
		 * Store custom data in array
		 * Add to cart with custom data
		 */
		if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {
			# Discount informations
			$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
			$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

			# Calculate discounted price
			if ( $discount_type == 'percent' ) {
				$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 ) ) );
				$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 ) ) );
				$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 ) ) );
				$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 ) ) );
			} elseif ( $discount_type == 'fixed' ) {
				$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 ) ) );
				$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 ) ) );
				$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 ) ) );
				$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 ) ) );
			}

			# Set pricing based on pricing rule
			if ( $pricing_rule == 'group' ) {
				$tf_tour_price = $group_price;
			} else {
				$tf_tour_price = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
			}

		}

		return array(
			'response'            => $response,
			'tf_tour_price'       => $tf_tour_price ? $tf_tour_price + $tour_extra_total : 0,
			'tf_tour_extra_title' => $tour_extra_title,
			'tf_tour_time_title'  => ! empty( $tour_time_title ) ? $tour_time_title : '',
			'start_date'          => $start_date,
			'end_date'            => $end_date,
			'tour_date'           => $tour_date,
		);
	}

	function check_avaibility_callback(){}
    function check_price_callback(){}
}