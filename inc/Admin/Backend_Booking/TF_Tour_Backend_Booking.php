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
						'placeholder' => esc_html__( 'Select Tour', 'tourfic' ),
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
				),
			),
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
		$tf_tour_packages = array(
			'id'          => 'tf_tour_packages',
			'label'       => esc_html__( 'Tour Packages', 'tourfic' ),
			'type'        => 'select',
			'options'     => array(
				'' => 'No Package Available',
			),
			'placeholder' => esc_html__( 'Select Package', 'tourfic' ),
			'field_width' => 50,
		);

		array_push( $this->settings['tf_booking_fields']['fields'], $tf_tour_extras );
		array_push( $this->settings['tf_booking_fields']['fields'], $tf_tour_packages );
		$this->settings['tf_booking_fields']['fields'] = apply_filters(
			'tourfic_tour_backend_booking_fields',
			$this->settings['tf_booking_fields']['fields']
		);


		$this->set_settings( $this->settings);
	}

	public function __construct() {
		$this->set_settings_fields();

		parent::__construct($this->args);

		add_action( 'wp_ajax_tourfic_tour_date_time_update', array( $this, 'tf_tour_date_time_update' ) );
		add_action( 'wp_ajax_tourfic_backend_tour_booking', array( $this, 'backend_booking_callback' ) );
	}

	public function tf_tour_date_time_update() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$tour_id      = isset( $_POST['tour_id'] ) ? absint( wp_unslash( $_POST['tour_id'] ) ) : 0;
		$meta         = get_post_meta( $tour_id, 'tf_tours_opt', true );

		// Same Day Booking
		$disable_same_day = ! empty( $meta['disable_same_day'] ) ? $meta['disable_same_day'] : '';

		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$group_price          = ! empty( $meta['group_price'] ) ? $meta['group_price'] : false;
		$adult_price          = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
		$child_price          = ! empty( $meta['child_price'] ) ? $meta['child_price'] : false;
		$infant_price         = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
		$tour_extras          = apply_filters( 'tourfic_tour_extra_meta', null, $tour_id, $meta );


		// Single Template Check
		$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
		if ( "single" == $tf_tour_layout_conditions ) {
			$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
		}
		$tf_tour_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';

		$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;

		$tf_tour_selected_template = $tf_tour_selected_check;

		$tour_extras_select_array = [];
		if ( $tour_extras ) {
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

		$tour_packages_select_array = [];
		if('package'==$pricing_rule){
			$package_pricing = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';
			if(!empty($package_pricing)){
				foreach ( $package_pricing as $pack => $package ) {
					$package_status = ! empty( $package['pack_status'] ) ? $package['pack_status'] : '';
					$package_title = ! empty( $package['pack_title'] ) ? $package['pack_title'] : '';
					if(!empty($package_status) && !empty($package_title)){
						$tour_packages_select_array[$pack] = $package_title;
					}
				}
			}
		}

		$tour_availability = array();
		if ( ! empty( $meta['tour_availability'] ) ) {
			if ( is_array( $meta['tour_availability'] ) ) {
				$tour_availability = $meta['tour_availability'];
			} elseif ( is_string( $meta['tour_availability'] ) ) {
				$decoded           = json_decode( $meta['tour_availability'], true );
				$tour_availability = is_array( $decoded ) ? $decoded : array();
			}
		}

		$core_availability = array();
		$core_rule_keys    = array_flip(
			array(
				'check_in',
				'check_out',
				'pricing_type',
				'adult_price',
				'child_price',
				'infant_price',
				'min_person',
				'max_person',
				'max_capacity',
				'status',
			)
		);
		foreach ( $tour_availability as $availability_key => $availability_rule ) {
			if ( ! is_array( $availability_rule ) ) {
				continue;
			}

			$core_availability[ $availability_key ] = array_intersect_key( $availability_rule, $core_rule_keys );
		}

		$form_data = array(
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
			'tour_packages_array' 		=> $tour_packages_select_array,
			'tf_tour_selected_template' => $tf_tour_selected_template,
			'tour_availability'         => $core_availability,
		);
		$form_data = apply_filters( 'tourfic_tour_backend_booking_form_data', $form_data, $tour_availability, $meta, $tour_id );

		wp_send_json( $form_data );
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
			$selected_package = isset( $field['tf_tour_packages'] ) && '' !== $field['tf_tour_packages'] ? absint( $field['tf_tour_packages'] ) : '';
			if ( '' === (string) $selected_package ) {
				$selected_package = $this->tf_get_default_tour_package_id( intval( $field['tf_available_tours'] ) );
			}
			$res              = $this->tf_get_tour_total_price( intval( $field['tf_available_tours'] ), $field['tf_tour_date'], $field['tf_tour_time'] ?? '', $field['tf_tour_extras'] ?? '', intval( $field['tf_tour_adults_number'] ), intval( $field['tf_tour_children_number'] ), intval( $field['tf_tour_infants_number'] ), $selected_package );
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

				$tour_in  = sanitize_text_field( $res['start_date'] ?? $field['tf_tour_date'] );
				$tour_out = sanitize_text_field( $res['end_date'] ?? $field['tf_tour_date'] );

			$tf_package_title = '';
			if ( '' !== (string) $selected_package ) {
				$selected_package_data = $this->tf_get_tour_package_title( intval( $field['tf_available_tours'] ), $selected_package );
				$tf_package_title      = ! empty( $selected_package_data['tf_tour_package_title'] ) ? $selected_package_data['tf_tour_package_title'] : '';
			}

			$order_details = [
				'order_by'    => $field['tf_tours_booked_by'],
				'tour_date'   => $res['tour_date'],
				'tour_time'   => $res['tf_tour_time_title'],
				'tour_extra'  => $res['tf_tour_extra_title'],
				'package'     => $tf_package_title,
				'adult'       => $field['tf_tour_adults_number'],
				'child'       => $field['tf_tour_children_number'],
				'infants'     => $field['tf_tour_infants_number'],
				'total_price' => $res['tf_tour_price'],
				'due_price'   => '',
				'unique_id'   => wp_rand(),
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
				$order_id = Helper::tf_set_order( $order_data );
				if ( ! empty( $order_id ) ) {
					do_action( 'tourfic_offline_payment_booking_confirmation', $order_id, $order_data );
				}

				if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-integration' ) )['tf-new-order-google-calendar'] ) && Helper::tf_data_types( Helper::tfopt( 'tf-integration' ) )['tf-new-order-google-calendar'] == "1" ) {

					/**
					 * Filters the data passed to the Google Calendar integration.
					 *
					 * @param int    $order_id   The order ID.
					 * @param array  $order_data The items in the order.
					 * @param string $type Order type
					 */
					apply_filters( 'tourfic_after_booking_completed_calendar_data', $order_id, $order_data, '' );
				}
				$response['success'] = true;
				$response['message'] = esc_html__( 'Your booking has been successfully submitted.', 'tourfic' );
			} else {
				$response['errors'] = $res['response']['errors'];
			}
		}

		echo wp_json_encode( $response );
		die();
	}

	public function tf_get_tour_total_price( $post_id, $tour_date, $tour_time, $tours_extra, $adults, $children, $infant, $selected_package = '' ) {
		$response = array();
		$tour_date = sanitize_text_field( $tour_date );
		$tour_time = sanitize_text_field( $tour_time );

		$meta                 = get_post_meta( $post_id, 'tf_tours_opt', true );
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
		$allow_package_pricing = ! empty( $meta['allow_package_pricing'] ) ? $meta['allow_package_pricing'] : '';
		$group_package_pricing = ! empty( $meta['group_package_pricing'] ) ? $meta['group_package_pricing'] : '';
		$tf_package_pricing    = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : array();

		// People number
		$total_people         = $adults + $children + $infant;
		$total_people_booking = $adults + $children;
		$tour_extra_selection = Helper::tf_sanitize_tour_extra_selection( $tours_extra );
		$tours_extra          = $tour_extra_selection['extras'];

		$tour_availability = '';
		if ( ! empty( $meta['tour_availability'] ) ) {
			if ( is_array( $meta['tour_availability'] ) ) {
				$tour_availability = $meta['tour_availability'];
			} elseif ( is_string( $meta['tour_availability'] ) ) {
				$decoded = json_decode( $meta['tour_availability'], true );
				$tour_availability = is_array( $decoded ) ? $decoded : '';
			}
		}

		$matched_availability = Helper::tf_get_tour_matched_availability( $tour_availability, $tour_date, 'available' );

			$is_date_unavailable = ! empty( $tour_availability ) && empty( $matched_availability );
			if ( $is_date_unavailable ) {
				$response['errors'][] = esc_html__( 'This tour is unavailable for the selected date.', 'tourfic' );
			}
			$schedule_context    = apply_filters(
				'tourfic_tour_booking_schedule_context',
				array(
					'skip_core'  => false,
					'start_date' => $tour_date,
					'end_date'   => $tour_date,
					'time_title' => '',
					'errors'     => array(),
				),
				array(
					'post_id'              => $post_id,
					'meta'                 => $meta,
					'tour_date'            => $tour_date,
					'tour_time'            => $tour_time,
					'matched_availability' => $matched_availability,
					'is_date_unavailable'  => $is_date_unavailable,
					'pricing_rule'         => $pricing_rule,
					'selected_package'     => (string) $selected_package,
					'total_people'         => $total_people,
					'total_people_booking' => $total_people_booking,
				)
			);
			$start_date         = sanitize_text_field( $schedule_context['start_date'] ?? $tour_date );
			$end_date           = sanitize_text_field( $schedule_context['end_date'] ?? $tour_date );
			$tour_time_title    = sanitize_text_field( $schedule_context['time_title'] ?? '' );
			$skip_core_schedule = ! empty( $schedule_context['skip_core'] );
			if ( ! empty( $schedule_context['errors'] ) && is_array( $schedule_context['errors'] ) ) {
				$response['errors'] = array_merge( $response['errors'] ?? array(), $schedule_context['errors'] );
			}

			if ( ! $skip_core_schedule && 'package' !== $pricing_rule && ! empty( $matched_availability ) ) {
				$min_people = absint( $matched_availability['min_person'] ?? 0 );
				$max_people = absint( $matched_availability['max_person'] ?? 0 );
				/* translators: %s: Minimum number of people. */
				$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
				/* translators: %s: Maximum number of people. */
				$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

				if ( 0 < $min_people && $total_people < $min_people ) {
					$response['errors'][] = sprintf(
						/* translators: 1: Minimum people, 2: Availability start date, 3: Availability end date. */
						esc_html__( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ),
						$min_text,
						sanitize_text_field( $matched_availability['check_in'] ?? '' ),
						sanitize_text_field( $matched_availability['check_out'] ?? '' )
					);
				}
				if ( 0 < $max_people && $total_people > $max_people ) {
					$response['errors'][] = sprintf(
						/* translators: 1: Maximum people, 2: Availability start date, 3: Availability end date. */
						esc_html__( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ),
						$max_text,
						sanitize_text_field( $matched_availability['check_in'] ?? '' ),
						sanitize_text_field( $matched_availability['check_out'] ?? '' )
					);
				}

				$booking_limit = absint( $matched_availability['max_capacity'] ?? 0 );
				if ( 0 < $booking_limit ) {
					$booked_people = 0;
					$orders        = Helper::tourfic_order_table_data(
						array(
							'select'    => 'post_id,order_details',
							'post_type' => 'tour',
							'where'     => array(
								'ostatus' => 'completed',
							),
							'orderby'   => 'order_id',
							'order'     => 'DESC',
						)
					);

					foreach ( $orders as $order ) {
						$order_details = json_decode( $order['order_details'] ?? '' );
						$order_date    = sanitize_text_field( $order_details->tour_date ?? '' );
						if ( absint( $order['post_id'] ?? 0 ) !== $post_id || $tour_date !== $order_date ) {
							continue;
						}

						foreach ( array( 'adult', 'child' ) as $people_key ) {
							$people_value = sanitize_text_field( $order_details->{$people_key} ?? '' );
							$people_parts = explode( ' × ', $people_value );
							$booked_people += absint( $people_parts[0] ?? 0 );
						}
					}

					$remaining_people = $booking_limit - $booked_people;
					if ( $booked_people >= $booking_limit ) {
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
					} elseif ( $remaining_people < $total_people_booking ) {
						$response['errors'][] = sprintf(
							/* translators: %s: Remaining adult/child capacity. */
							esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ),
							$remaining_people
						);
					}
				}
			}

		// Tour extra
		$tour_extra_total     = 0;
		$tour_extra_title_arr = [];
		$tour_extra_meta      = apply_filters( 'tourfic_tour_extra_meta', null, $post_id, $meta );
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


		if ( $pricing_rule == 'group' && ! empty( $allow_package_pricing ) && ! empty( $group_package_pricing ) && ! empty( $matched_availability ) ) {
			$max_allowed = 0;
			$matched_price = '';
			$found_valid_option = false;

			$package_count = is_array( $tf_package_pricing ) ? count( $tf_package_pricing ) : 0;
			$options_count = ! empty( $matched_availability['options_count'] ) ? (int) $matched_availability['options_count'] : $package_count;

			for ( $i = 0; $i < $options_count; $i++ ) {
				$availability_min = isset( $matched_availability[ 'tf_option_min_person_' . $i ] ) ? $matched_availability[ 'tf_option_min_person_' . $i ] : '';
				$availability_max = isset( $matched_availability[ 'tf_option_max_person_' . $i ] ) ? $matched_availability[ 'tf_option_max_person_' . $i ] : '';
				$package_min      = ! empty( $tf_package_pricing[ $i ]['group_tabs'][2]['min_person'] ) ? $tf_package_pricing[ $i ]['group_tabs'][2]['min_person'] : 0;
				$package_max      = ! empty( $tf_package_pricing[ $i ]['group_tabs'][3]['max_person'] ) ? $tf_package_pricing[ $i ]['group_tabs'][3]['max_person'] : 0;

				$min = '' !== $availability_min ? (int) $availability_min : (int) $package_min;
				$max = '' !== $availability_max ? (int) $availability_max : (int) $package_max;

				$price = ! empty( $matched_availability[ 'tf_option_group_price_' . $i ] ) ? $matched_availability[ 'tf_option_group_price_' . $i ] : '';
				if ( '' === $price && ! empty( $tf_package_pricing[ $i ]['group_tabs'][1]['group_price'] ) ) {
					$price = $tf_package_pricing[ $i ]['group_tabs'][1]['group_price'];
				}

				// Keep track of the highest max_person across all options
				if ( $max > $max_allowed ) {
					$max_allowed = $max;
				}

				// Find a matching price bracket
				if ( $max > 0 && $total_people >= $min && $total_people <= $max ) {
					$found_valid_option = true;
					$matched_price = $price;
					break;
				}
			}

			if ( $max_allowed > 0 && $total_people > $max_allowed ) {
				/* translators: %s minimum people allowed */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_allowed );
			} elseif ( $found_valid_option ) {
				$group_price = $matched_price;
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

		$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
		$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;

		/**
		 * Price by Type
		 */
		
		$group_price    = ! empty( $matched_availability['price'] ) ? $matched_availability['price'] : $group_price;
		$adult_price    = ! empty( $matched_availability['adult_price'] ) ? $matched_availability['adult_price'] : $adult_price;
		$children_price = ! empty( $matched_availability['child_price'] ) ? $matched_availability['child_price'] : $children_price ;
		$infant_price   = ! empty( $matched_availability['infant_price'] ) ? $matched_availability['infant_price'] : $infant_price;

		if ( $pricing_rule == 'group' && ! empty( $allow_package_pricing ) && ! empty( $group_package_pricing ) && ! empty( $matched_availability ) ) {
			$max_allowed = 0;
			$matched_price = '';
			$found_valid_option = false;

			$package_count = is_array( $tf_package_pricing ) ? count( $tf_package_pricing ) : 0;
			$options_count = ! empty( $matched_availability['options_count'] ) ? (int) $matched_availability['options_count'] : $package_count;

			for ( $i = 0; $i < $options_count; $i++ ) {
				$availability_min = isset( $matched_availability[ 'tf_option_min_person_' . $i ] ) ? $matched_availability[ 'tf_option_min_person_' . $i ] : '';
				$availability_max = isset( $matched_availability[ 'tf_option_max_person_' . $i ] ) ? $matched_availability[ 'tf_option_max_person_' . $i ] : '';
				$package_min      = ! empty( $tf_package_pricing[ $i ]['group_tabs'][2]['min_person'] ) ? $tf_package_pricing[ $i ]['group_tabs'][2]['min_person'] : 0;
				$package_max      = ! empty( $tf_package_pricing[ $i ]['group_tabs'][3]['max_person'] ) ? $tf_package_pricing[ $i ]['group_tabs'][3]['max_person'] : 0;

				$min = '' !== $availability_min ? (int) $availability_min : (int) $package_min;
				$max = '' !== $availability_max ? (int) $availability_max : (int) $package_max;

				$price = ! empty( $matched_availability[ 'tf_option_group_price_' . $i ] ) ? $matched_availability[ 'tf_option_group_price_' . $i ] : '';
				if ( '' === $price && ! empty( $tf_package_pricing[ $i ]['group_tabs'][1]['group_price'] ) ) {
					$price = $tf_package_pricing[ $i ]['group_tabs'][1]['group_price'];
				}

				// Keep track of the highest max_person across all options
				if ( $max > $max_allowed ) {
					$max_allowed = $max;
				}

				// Find a matching price bracket
				if ( $max > 0 && $total_people >= $min && $total_people <= $max ) {
					$found_valid_option = true;
					$matched_price = $price;
					break;
				}
			}

			if ( $max_allowed > 0 && $total_people > $max_allowed ) {
				/* translators: %s minimum people allowed */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_allowed );
			} elseif ( $found_valid_option ) {
				$group_price = $matched_price;
			}
		}

		if ( 'package' == $pricing_rule ) {
			if ( '' === (string) $selected_package && ! empty( $tf_package_pricing ) && is_array( $tf_package_pricing ) ) {
				foreach ( $tf_package_pricing as $package_index => $package_data ) {
					if ( ! empty( $package_data['pack_status'] ) ) {
						$selected_package = $package_index;
						break;
					}
				}
			}

			$single_package = ! empty( $tf_package_pricing[ $selected_package ] ) ? $tf_package_pricing[ $selected_package ] : array();
			$pricing_type   = ! empty( $single_package['pricing_type'] ) ? $single_package['pricing_type'] : '';

			if ( 'person' == $pricing_type ) {
				$pack_default_adult   = ! empty( $single_package['adult_tabs'][1]['adult_price'] ) ? $single_package['adult_tabs'][1]['adult_price'] : 0;
				$pack_default_child   = ! empty( $single_package['child_tabs'][1]['child_price'] ) ? $single_package['child_tabs'][1]['child_price'] : 0;
				$pack_default_infant  = ! empty( $single_package['infant_tabs'][1]['infant_price'] ) ? $single_package['infant_tabs'][1]['infant_price'] : 0;
				$adult_price          = ! empty( $matched_availability[ 'tf_option_adult_price_' . $selected_package ] ) ? $matched_availability[ 'tf_option_adult_price_' . $selected_package ] : $pack_default_adult;
				$children_price       = ! empty( $matched_availability[ 'tf_option_child_price_' . $selected_package ] ) ? $matched_availability[ 'tf_option_child_price_' . $selected_package ] : $pack_default_child;
				$infant_price         = ! empty( $matched_availability[ 'tf_option_infant_price_' . $selected_package ] ) ? $matched_availability[ 'tf_option_infant_price_' . $selected_package ] : $pack_default_infant;
			}
			if ( 'group' == $pricing_type ) {
				$pack_default_group = ! empty( $single_package['group_tabs'][1]['group_price'] ) ? $single_package['group_tabs'][1]['group_price'] : 0;
				$group_price        = ! empty( $matched_availability[ 'tf_option_group_price_' . $selected_package ] ) ? $matched_availability[ 'tf_option_group_price_' . $selected_package ] : $pack_default_group;
			}
		}

		if ( $pricing_rule == 'person' ) {

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
	
		} else if ( $pricing_rule == 'group' ) {
	
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
			$allow_discount    = ! empty( $meta['allow_discount'] ) ? $meta['allow_discount'] : '';
			$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
			$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

			# Calculate discounted price
			if ( !empty($allow_discount) && $discount_type == 'percent' ) {
				$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 ) ) );
				$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 ) ) );
				$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 ) ) );
				$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 ) ) );
			} elseif ( !empty($allow_discount) && $discount_type == 'fixed' ) {
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

		$stored_tour_date = $tour_date;
		if ( $start_date && $end_date && $start_date !== $end_date ) {
			$stored_tour_date = $start_date . ' - ' . $end_date;
		}

		return array(
			'response'            => $response,
			'tf_tour_price'       => !empty($tf_tour_price) ? $tf_tour_price + $tour_extra_total : 0,
			'tf_tour_extra_title' => $tour_extra_title,
			'tf_tour_time_title'  => ! empty( $tour_time_title ) ? $tour_time_title : '',
			'start_date'          => $start_date,
			'end_date'            => $end_date,
			'tour_date'           => $stored_tour_date,
		);
	}

	public function tf_get_tour_package_title( $post_id, $package_id ) {
		$meta            = get_post_meta( $post_id, 'tf_tours_opt', true );
		$package_pricing = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';
		
		return array(
			'tf_tour_package_title' => !empty($package_pricing[$package_id]['pack_title']) ? $package_pricing[$package_id]['pack_title'] : '',
		);
	}

	public function tf_get_default_tour_package_id( $post_id ) {
		$meta            = get_post_meta( $post_id, 'tf_tours_opt', true );
		$package_pricing = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : array();

		if ( is_array( $package_pricing ) ) {
			foreach ( $package_pricing as $package_index => $package_data ) {
				if ( ! empty( $package_data['pack_status'] ) ) {
					return $package_index;
				}
			}
		}

		return '';
	}

	function check_avaibility_callback(){}
    function check_price_callback(){}
}
