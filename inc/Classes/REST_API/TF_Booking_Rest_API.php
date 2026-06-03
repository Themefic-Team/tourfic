<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Booking_Rest_API' ) ) {
	class TF_Booking_Rest_API extends TF_Rest_API {

		/*
		 * instance
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/*
		 * Get order data
		 * @author Foysal
		 */
		public function tf_get_orders( $request ) {
			$current_user_id = get_current_user_id();
			$post_type       = $this->tf_validate_allowed_param( $request, 'post_type', $this->tf_order_post_types(), true );
			$post_id         = $this->tf_get_rest_absint_param( $request, 'post_id' );
			$checkinout      = $this->tf_validate_allowed_param( $request, 'checkinout', $this->tf_checkinout_statuses() );
			$order_status    = $this->tf_validate_allowed_param( $request, 'order_status', $this->tf_order_statuses() );

			foreach ( array( $post_type, $post_id, $checkinout, $order_status ) as $validation_error ) {
				if ( is_wp_error( $validation_error ) ) {
					return $validation_error;
				}
			}
			$post_type = $this->tf_normalize_order_post_type( $post_type );

			$filters = array();
			if ( ! empty( $checkinout ) ) {
				$filters['checkinout'] = $checkinout;
			}
			if ( ! empty( $post_id ) ) {
				$filters['post_id'] = $post_id;
			}
			if ( ! empty( $order_status ) ) {
				$filters['ostatus'] = $order_status;
			}

			$orders_result = array();
			if ( $this->tf_current_user_can_manage_records() ) {

				$tf_orders_select = array(
					'select'    => "*",
					'post_type' => $post_type,
					'where'     => $filters,
					'orderby'   => 'order_date',
					'order'     => 'DESC'
				);

				$orders_result = Helper::tourfic_order_table_data( $tf_orders_select );
			} elseif ( $this->user_has_role( $current_user_id, 'tf_vendor' ) ) {

				$tf_orders_select = array(
					'select'    => "*",
					'post_type' => $post_type,
					'author'    => $current_user_id,
					'where'     => $filters,
					'orderby'   => 'order_date',
					'order'     => 'DESC',
					'limit'     => ""
				);
                
				$orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			} else {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.', 'tourfic' ), array( 'status' => 403 ) );
			}
            $events = array();
			$orders_data = array();
			foreach ( $orders_result as $order ) {		
                $billing_info  = json_decode( $order['billing_details'] );
                $order_details = json_decode( $order['order_details'] );

                //post title
				$order['post_title'] = esc_html(get_the_title( $order['post_id'] ));

                //customer details
                $billing_details    = "";
                $billing_first_name = ! empty( $billing_info->billing_first_name ) ? $billing_info->billing_first_name : '';
                $billing_last_name  = ! empty( $billing_info->billing_last_name ) ? $billing_info->billing_last_name : '';
                $customer_name      = $billing_first_name . ' ' . $billing_last_name;
                $customer_email     = ! empty( $billing_info->billing_email ) ? $billing_info->billing_email : '';
                if ( $customer_name ) {
                    $billing_details .= $customer_name . '<br>';
                }
                if ( $customer_email ) {
                    $billing_details .= '<span>' . $customer_email . '</span>';
                }
                $order['customer_details'] = wp_kses_post( $billing_details );

                //Booking Date
                $order['booking_date'] = esc_html(gmdate( 'F d, Y', strtotime( $order['order_date'] ) ));

                //order check in out status
                if ( ! empty( $order['checkinout'] ) ) {
                    if ( "in" == $order['checkinout'] ) {
                        $checked_in_status = esc_html__('Checked in', 'tourfic');
                    }
                    if ( "out" == $order['checkinout'] ) {
                        $checked_in_status = esc_html__('Checked out', 'tourfic');
                    }
                    if ( "not" == $order['checkinout'] ) {
                        $checked_in_status = esc_html__('Not checked in', 'tourfic');
                    }
                } else {
                    $checked_in_status = esc_html__('Not checked in', 'tourfic');
                }
                $order['checked_in_status'] = $checked_in_status;

                $book_adult  = !empty( $order_details->adult ) ? $order_details->adult : '';
                if(!empty($book_adult)){
                    $tf_total_adult = explode( " × ", $book_adult );
                }
                $book_children  = !empty( $order_details->child ) ? $order_details->child : '';
                if(!empty($book_children)){
                    $tf_total_children = explode( " × ", $book_children );
                }
                $book_infants  = !empty( $order_details->infants ) ? $order_details->infants : '';
                if(!empty($book_infants)){
                    $tf_total_infants = explode( " × ", $book_infants );
                }

                //calendar event
                $events[] = array(
					'title' => '#'.$order['order_id'].' '.html_entity_decode(get_the_title($order['post_id'])),
					'start' => $order['check_in'],
					'end' => $order['check_out'],
					'id' => $order['id'],
					'status' => $order['ostatus'],
					'post_type' => $post_type,
					'billing_info' => $billing_info,
					'order_details' => $order_details,
					'adult_count' => !empty($tf_total_adult[0]) ? $tf_total_adult[0] : '',
					'children_count' => !empty($tf_total_children[0]) ? $tf_total_children[0] : '',
					'infants_count' => !empty($tf_total_infants[0]) ? $tf_total_infants[0] : '',
                    'popup_title' => esc_html( get_the_title( $order['post_id'] ) ),
                    'classNames' => ['tf-order-'.$order['ostatus']]
				);

				$orders_data[] = $order;
			}

			return array(
                'data' => $orders_data,
                'events' => $events
            );
		}

		/*
		 * Get order details order/{id}
		 * @author Foysal
		 */
		public function tf_get_order_details( $request ) {
			global $wpdb;
			$id    = absint( $request->get_param( 'id' ) );
			$order = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE id = %d", $id ), ARRAY_A );
			if ( empty( $order ) ) {
				return new WP_Error( 'tf_order_not_found', esc_html__( 'Order not found.', 'tourfic' ), array( 'status' => 404 ) );
			}
			if ( ! $this->tf_current_user_can_access_order( $order ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this order.', 'tourfic' ), array( 'status' => 403 ) );
			}

			$order_details = json_decode( $order['order_details'] );
			
            //post title
            if ( ! empty( $order['post_id'] ) ) {
				$order['order_detail']['post_name'] = '<a href="' . get_the_permalink( $order['post_id'] ) . '" target="_blank">' . get_the_title( $order['post_id'] ) . '</a>';
			}

            //order date
            if ( ! empty( $order['order_date'] ) ) {
				$order['order_detail']['order_date'] = esc_html(gmdate('F d, Y',strtotime($order['order_date'])));
            }

            //booked by
            $tf_booking_by = get_user_by('id', $order['customer_id']);
            if("offline"==$order['payment_method'] && empty($tf_booking_by)){
                $order['order_detail']['booked_by'] = "Administrator";
            }else{
                $order['order_detail']['booked_by'] = !empty($tf_booking_by->roles[0]) ? esc_html($tf_booking_by->roles[0]) : 'Administrator';
            }

            //payment method
			$payment_gateways = \WC_Payment_Gateways::instance()->get_available_payment_gateways();
			if ( isset( $payment_gateways[ $order['payment_method'] ] ) ) {
				$order['payment_method'] = $payment_gateways[ $order['payment_method'] ]->title;
			} else {
				$order['payment_method'] = 'Offline Payment';
			}

            //tax info
            $taxs = !empty($order['order_detail']['tax_info']) ? json_decode($order['order_detail']['tax_info'], true) : array();
            $taxs_summations = 0;
            foreach ( $taxs as $sum ) {
                $taxs_summations += $sum;
            }
            
            if(!empty($taxs_summations)){
                $order['tax_info'] = wp_kses_post(wc_price($taxs_summations));
            }

            //total price
			if ( ! empty( $order_details->total_price ) ) {
				$order['total_price'] = wc_price( $order_details->total_price );
			}

            //due price
			if ( ! empty( $order_details->due_price ) ) {
				$order['due_price'] = wc_price( $order_details->due_price );
			}

            //checked in out
            if( !empty($order['checkinout']) ){
                if( "in"==$order['checkinout'] ){
                    $order['checked_status'] = esc_html__("Checked in", "tourfic");
                } elseif( "out"==$order['checkinout'] ){
                    $order['checked_status'] = esc_html__("Checked Out", "tourfic");
                } elseif( "not"==$order['checkinout'] ){
                    $order['checked_status'] = esc_html__("Not checked in", "tourfic");
                }
            }else{
                $order['checked_status'] = esc_html__("Not checked in", "tourfic");
            }

            //checked in out by
            $tf_checkinout_by = !empty($order['checkinout_by']) ? json_decode($order['checkinout_by']) : '';
            if(!empty($tf_checkinout_by->userid)){
                $tf_checkin_by = get_user_by('id', $tf_checkinout_by->userid);
                $order['checkinout_by'] = !empty($tf_checkin_by->display_name) ? esc_html($tf_checkin_by->display_name) : "";
            }

            if(!empty($tf_checkinout_by->time)){
                $order['checkinout_time'] = !empty($tf_checkinout_by->time) ? esc_html($tf_checkinout_by->time) : "";
            }

            //order author role
            if(!empty($order['post_id'])){
                $order_author_id = get_post_field ('post_author', $order['post_id']);
                $order_author = get_user_by( 'id', $order_author_id );
                $order_author_role = !empty( $order_author->roles[0] ) ? $order_author->roles[0] : '';

                $order['order_author_role'] = $order_author_role;
            }

            // Tour Date
            if($order['post_type'] == 'tour'){
                $tour_meta = get_post_meta( $order['post_id'], 'tf_tours_opt', true );
                $tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
                if ( $tour_date ) {
                    $tour_date_duration = explode( ' - ', $tour_date );
                    if(!empty($tour_date_duration[0])){
                        $tour_in = $tour_date_duration[0];
                    }
                    if(!empty($tour_date_duration[1])){
                        $tour_out = $tour_date_duration[1];
                    }
                } else {
                    $tour_in = $order_details->check_in;
                    $tour_out = $order_details->check_out;
                }
                $order['tour_duration'] = !empty($tour_out) && !empty( $tour_in ) ? gmdate('d F, Y', strtotime($tour_in)).' - '. gmdate('d F, Y', strtotime($tour_out)) : gmdate('d F, Y', strtotime($tour_in));
                $order['tour_email']    = ! empty( $tour_meta['email'] ) ? $tour_meta['email'] : '';
                $order['tour_phone']    = ! empty( $tour_meta['phone'] ) ? $tour_meta['phone'] : '';
                if( !empty($tour_meta['location']) && Helper::tf_data_types($tour_meta['location'])){
                    $order['tour_location'] = !empty( Helper::tf_data_types($tour_meta['location'])['address'] ) ? Helper::tf_data_types($tour_meta['location'])['address'] : '';
                }
            }

            //total person
            $total_person = 0;
			$adult_count  = array();
			$child_count  = array();
			$infant_count = array();
            if(!empty($order_details->adult)){
                $adult_count = explode( " × ", $order_details->adult );
                $total_person += $adult_count[0] ? $adult_count[0] : 0;
            }
            if(!empty($order_details->child)){
                $child_count = explode( " × ", $order_details->child );
                $total_person += $child_count[0] ? $child_count[0] : 0;
            }
            if(!empty($order_details->infants)){
                $infant_count = explode( " × ", $order_details->infants );
                $total_person += $infant_count[0] ? $infant_count[0] : 0;
            }
            $order['adult_count'] = ! empty( $adult_count[0] ) ? $adult_count[0] : '';
            $order['child_count'] = ! empty( $child_count[0] ) ? $child_count[0] : '';
            $order['infant_count'] = ! empty( $infant_count[0] ) ? $infant_count[0] : '';
            $order['total_person'] = $total_person;

			return $order;
		}
	}
}

TF_Booking_Rest_API::get_instance();
