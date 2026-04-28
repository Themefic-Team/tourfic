<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

if ( ! class_exists( 'TF_Hotel_Rest_API' ) ) {
	class TF_Hotel_Rest_API extends TF_Rest_API {

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

		public function __construct() {
			parent::__construct();
			add_action( 'rest_api_init', array( $this, 'add_hotel_meta_to_rest_api' ) );
		}

		/*
		 * Get Hotels
		 * @author Foysal
		 */
		public function tf_get_hotels( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'user' ) ? $request->get_param( 'user' ) : get_current_user_id();

			$query_hotels = new WP_Query( array(
				'post_type'      => 'tf_hotel',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) || $this->user_has_role( $author, 'tf_manager' ) ? '' : $author,
				'paged'          => $page,
			) );
			$hotels       = array();
			if ( $query_hotels->have_posts() ) {
				while ( $query_hotels->have_posts() ) {
					$query_hotels->the_post();
					$hotel_id = get_the_ID();

					$hotel_data   = array();
					$hotel_review = $this->tf_get_post_review( $hotel_id );
					$start_price  = $this->tf_get_hotel_starting_price( $hotel_id );

					$hotel_data['id']             = $hotel_id;
					$hotel_data['permalink']      = get_permalink( $hotel_id );
					$hotel_data['title']          = get_the_title( $hotel_id );
					$hotel_data['content']        = get_the_content( $hotel_id );
					$hotel_data['status']         = get_post_status( $hotel_id );
					$hotel_data['author']         = get_the_author_meta( 'display_name', get_post_field( 'post_author', $hotel_id ) );
					$hotel_data['hotel_location'] = $this->tf_get_post_terms( $hotel_id, 'hotel_location' ) ? $this->tf_get_post_terms( $hotel_id, 'hotel_location' ) : '—';
					$hotel_data['hotel_feature']  = $this->tf_get_post_terms( $hotel_id, 'hotel_feature' ) ? $this->tf_get_post_terms( $hotel_id, 'hotel_feature' ) : '—';
					$hotel_data['hotel_type']     = $this->tf_get_post_terms( $hotel_id, 'hotel_type' ) ? $this->tf_get_post_terms( $hotel_id, 'hotel_type' ) : '—';
					$hotel_data['date']           = get_the_date( '', $hotel_id );
					$hotel_data['featured_image'] = get_the_post_thumbnail_url( $hotel_id );
					$hotel_data['tf_hotels_opt']  = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
					$hotel_data['reviews']        = [
						'hotel_reviews' => $hotel_review['post_reviews'],
						'review_text'   => $hotel_review['review_text'],
					];
					$hotel_data['start_price']    = $start_price;
					$hotel_data['rooms']    	  = Room::get_hotel_rooms($hotel_id);
					$hotels[]                     = $hotel_data;
				}
			}
			wp_reset_postdata();
			$hotels = array(
				'hotels' => $hotels,
				'total'  => $query_hotels->found_posts,
			);

			return $hotels;
		}

		/*
		 * Add Hotel
		 * @author Foysal
		 */
		public function tf_add_hotel( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$hotel_id = wp_insert_post( array(
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_type'    => 'tf_hotel',
				'post_status'  => $user->has_cap( 'publish_tf_hotels' ) ? 'publish' : 'pending',
				'post_author'  => get_current_user_id(),
			) );

			if ( $hotel_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $hotel_id, $request['featured_media'] );
				}
				if ( isset( $request['hotelLocations'] ) && ! empty( $request['hotelLocations'] ) ) {
					$hotelLocations = array_map( 'intval', $request['hotelLocations'] );
					wp_set_object_terms( $hotel_id, $hotelLocations, 'hotel_location' );
				}
				if ( isset( $request['hotelFeatures'] ) && ! empty( $request['hotelFeatures'] ) ) {
					$hotelFeatures = array_map( 'intval', $request['hotelFeatures'] );
					wp_set_object_terms( $hotel_id, $hotelFeatures, 'hotel_feature' );
				}
				if ( isset( $request['hotelTypes'] ) && ! empty( $request['hotelTypes'] ) ) {
					$hotelTypes = array_map( 'intval', $request['hotelTypes'] );
					wp_set_object_terms( $hotel_id, $hotelTypes, 'hotel_type' );
				}

				if ( isset( $request['tf_hotels_opt'] ) ) {
					update_post_meta( $hotel_id, 'tf_hotels_opt', $request['tf_hotels_opt'] );
				}
			}

			return $hotel_id;
		}

		/*
		 * Update Hotel
		 * @author Foysal
		 */
		public function tf_update_hotel( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$hotel_id    = $request['id'];
			$post_status = get_post_status( $hotel_id );
			$hotel       = array(
				'ID'           => $hotel_id,
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_status'  => $post_status == 'publish' ? 'publish' : ( $user->has_cap( 'publish_tf_hotels' ) ? 'publish' : 'pending' ),
				'post_type'    => 'tf_hotel',
				'post_author'  => $this->user_has_role( get_current_user_id(), 'administrator' ) ? $this->tf_get_post_author_id( $hotel_id ) : get_current_user_id(),
			);

			$hotel_id = wp_update_post( $hotel );

			if ( $hotel_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $hotel_id, $request['featured_media'] );
				}
				if ( isset( $request['hotelLocations'] ) && ! empty( $request['hotelLocations'] ) ) {
					$hotelLocations = array_map( 'intval', $request['hotelLocations'] );
					wp_set_object_terms( $hotel_id, $hotelLocations, 'hotel_location' );
				}
				if ( isset( $request['hotelFeatures'] ) && ! empty( $request['hotelFeatures'] ) ) {
					$hotelFeatures = array_map( 'intval', $request['hotelFeatures'] );
					wp_set_object_terms( $hotel_id, $hotelFeatures, 'hotel_feature' );
				}
				if ( isset( $request['hotelTypes'] ) && ! empty( $request['hotelTypes'] ) ) {
					$hotelTypes = array_map( 'intval', $request['hotelTypes'] );
					wp_set_object_terms( $hotel_id, $hotelTypes, 'hotel_type' );
				}

				if ( isset( $request['tf_hotels_opt'] ) ) {
					update_post_meta( $hotel_id, 'tf_hotels_opt', $request['tf_hotels_opt'] );
				}
			}

			return $hotel_id;
		}

		/*
		 * Update Hotel Status
		 * @auther Foysal
		 */
		public function tf_update_hotel_status( $request ) {
			$current_user_id = get_current_user_id();
			$id              = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$hotel_status    = ! empty( $request->get_param( 'hotel_status' ) ) ? $request->get_param( 'hotel_status' ) : '';
			$user            = get_user_by( 'id', $current_user_id );

			if ( $user->has_cap( 'publish_tf_hotels' ) ) {
				$hotel = array(
					'ID'          => $id,
					'post_status' => $hotel_status,
				);

				$hotel_id = wp_update_post( $hotel );
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => 'Hotel status updated successfully.',
			) );
		}

		/*
		 * Get Hotel order data
		 * @author Foysal
		 */
		public function tf_get_hotel_orders( $request ) {
			$current_user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			if ( $this->user_has_role( $current_user_id, 'administrator' ) || $this->user_has_role( $current_user_id, 'tf_manager' ) ) {

				$tf_orders_select = array(
					'select'    => "*",
					'post_type' => 'hotel',
					'query'     => " ORDER BY order_date DESC"
				);

				$hotel_orders_result = Helper::tourfic_order_table_data( $tf_orders_select );
			}
			if ( $this->user_has_role( $current_user_id, 'tf_vendor' ) ) {

				$tf_orders_select = array(
					'select'    => "*",
					'post_type' => "hotel",
					'author'    => $current_user_id,
					'limit'     => ""
				);

				$hotel_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			}
			$orders_data = array();
			foreach ( $hotel_orders_result as $order ) {
				//payment method
				$payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
				if ( isset( $payment_gateways[ $order['payment_method'] ] ) ) {
					$order['payment_method'] = $payment_gateways[ $order['payment_method'] ]->title;
				} else {
					$order['payment_method'] = esc_html__( 'Offline Payment', 'tourfic' );
				}

				//total price
				$order_details = json_decode( $order['order_details'] );
				$hotel_price   = "";
				if ( ! empty( $order_details->total_price ) ) {
					$hotel_price .= '<b>' . esc_html__( "Total", "tourfic" ) . ': </b>' . wc_price( $order_details->total_price ) . '<br>';
				}
				if ( ! empty( $order_details->due_price ) ) {
					$hotel_price .= '<b>' . esc_html__( "Due", "tourfic" ) . ': </b>' . $order_details->due_price . '<br>';
				}
				$order['total_price'] = $hotel_price;

				$orders_data[] = $order;
			}

			return array(
				'orders' => $orders_data,
				'total'  => count( $orders_data ),
			);
		}

		/*
		 * Get Hotel order details hotel-order/{id}
		 * @author Foysal
		 */
		public function tf_get_hotel_order_details( $request ) {
			global $wpdb;
			$id    = $request->get_param( 'id' );
			$order = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE id = %d", $id ), ARRAY_A );
			//billing details
			$billing_info       = json_decode( $order['billing_details'] );
			$billing_first_name = ! empty( $billing_info->billing_first_name ) ? $billing_info->billing_first_name : '';
			$billing_last_name  = ! empty( $billing_info->billing_last_name ) ? $billing_info->billing_last_name : '';
			$customer_name      = $billing_first_name . ' ' . $billing_last_name;
			$customer_email     = ! empty( $billing_info->billing_email ) ? $billing_info->billing_email : '';
			$customer_phone     = ! empty( $billing_info->billing_phone ) ? $billing_info->billing_phone : '';

			$customer_address_1       = ! empty( $billing_info->billing_address_1 ) ? $billing_info->billing_address_1 . ',' : '';
			$customer_address_2       = ! empty( $billing_info->billing_address_2 ) ? $billing_info->billing_address_2 . ',' : '';
			$customer_address_city    = ! empty( $billing_info->billing_city ) ? $billing_info->billing_city . ',' : '';
			$customer_address_country = ! empty( WC()->countries->countries[ $billing_info->billing_country ] ) ? WC()->countries->countries[ $billing_info->billing_country ] : '';

			$customer_address = $customer_address_1 . $customer_address_2 . '<br>' . $customer_address_city . $customer_address_country;

			$order['billing'] = array(
				'customer_name'    => $customer_name,
				'customer_email'   => $customer_email,
				'customer_phone'   => $customer_phone,
				'customer_address' => $customer_address,
			);

			//payment method
			$payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
			if ( isset( $payment_gateways[ $order['payment_method'] ] ) ) {
				$order['payment_method'] = $payment_gateways[ $order['payment_method'] ]->title;
			} else {
				$order['payment_method'] = 'Unknown Payment Method';
			}

			//total price
			$order_details = json_decode( $order['order_details'] );
			if ( ! empty( $order_details->total_price ) ) {
				$order['total_price'] = wc_price( $order_details->total_price );
			}
			if ( ! empty( $order_details->due_price ) ) {
				$order['due_price'] = wc_price( $order_details->due_price );
			}

			//hotel order details
			if ( ! empty( $order['post_id'] ) ) {
				$order['order_detail']['hotel_name'] = '<a href="' . get_the_permalink( $order['post_id'] ) . '" target="_blank">' . get_the_title( $order['post_id'] ) . '</a>';
			}
			if ( ! empty( $order_details->room_name ) ) {
				$order['order_detail']['room_name'] = $order_details->room_name;
			}
			if ( ! empty( $order_details->room ) ) {
				$order['order_detail']['room'] = $order_details->room;
			}
			if ( ! empty( $order_details->adult ) ) {
				$order['order_detail']['adult'] = $order_details->adult;
			}
			if ( ! empty( $order_details->child ) ) {
				$order['order_detail']['child'] = $order_details->child;
			}
			if ( ! empty( $order_details->children_ages ) ) {
				$order['order_detail']['children_ages'] = $order_details->children_ages;
			}
			if ( ! empty( $order_details->check_in ) ) {
				$order['order_detail']['check_in'] = $order_details->check_in;
			}
			if ( ! empty( $order_details->check_out ) ) {
				$order['order_detail']['check_out'] = $order_details->check_out;
			}
			if ( ! empty( $order_details->airport_service_type ) ) {
				$order['order_detail']['airport_service_type'] = $order_details->airport_service_type;
			}
			if ( ! empty( $order_details->airport_service_fee ) ) {
				$order['order_detail']['airport_service_fee'] = $order_details->airport_service_fee;
			}

			return $order;
		}

		/*
		 * Add hotel meta to /wp-json/wp/v2/tf_hotel api
		 * @author Foysal
		 */
		function add_hotel_meta_to_rest_api() {
			register_rest_field( 'tf_hotel', 'tf_hotels_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_hotels_opt     = get_post_meta( $post_arr['id'], 'tf_hotels_opt', true );
					$unserialize_array = array(
						'map',
						'airport_pickup_price',
						'airport_dropoff_price',
						'airport_pickup_dropoff_price'
					);
					foreach ( $unserialize_array as $item ) {
						if ( ! empty( $tf_hotels_opt[ $item ] ) && is_serialized( $tf_hotels_opt[ $item ] ) ) {
							$tf_hotels_opt[ $item ] = unserialize( $tf_hotels_opt[ $item ] );
						}
					}

					return $tf_hotels_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_hotel', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//hotel reviews
			register_rest_field( 'tf_hotel', 'reviews', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_post_review( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//hotel start price
			register_rest_field( 'tf_hotel', 'start_price', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_hotel_starting_price( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}

		function tf_get_hotel_starting_price( $post_id ) {
			$room_price = [];
			$meta       = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$rooms      = get_hotel_rooms( $post_id );
			if ( ! empty( $rooms ) ):
				foreach ( $rooms as $_room ) {
					$room       = get_post_meta( $_room->ID, 'tf_room_opt', true );
					$pricing_by = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : 1;
					if ( $pricing_by == 1 ) {
						$price        = ! empty( $room['price'] ) ? $room['price'] : '';
						$room_price[] = $price;
					} else if ( $pricing_by == 2 ) {
						$adult_price  = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
						$room_price[] = $adult_price;
					}
				}
			endif;

			return ! empty( $room_price ) ? wc_price( min( $room_price ) ) : '';
		}

		/*
		 * Get Hotel Room Availability
		 * @author Foysal
		 */
		function tf_get_hotel_room_availability( $request ) {
			$id = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';

			if ( $id !== 'undefined' ) {
				$room_meta       = get_post_meta( $id, 'tf_room_opt', true );
				$room_avail_data = isset( $room_meta['avail_date'] ) && ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
			} else {
				$room_avail_data = get_option( 'tf_hotel_avail_date' );
				delete_option( 'tf_hotel_avail_date' );
			}

			if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
				$room_avail_data = array_values( $room_avail_data );
				$room_avail_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					if ( $item['price_by'] == '1' ) {
						$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] );
					} elseif ( $item['price_by'] == '2' ) {
						$item['title'] = __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );
					} elseif ( $item['price_by'] == '3' ) {
						$item['title'] = '';
						if ( ! empty( $item['options_count'] ) ) {
							for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
								if ( $item[ 'tf_room_option_' . $i ] == '1' && $item[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item[ 'tf_option_title_' . $i ] . '<br>';
									$item['title'] .= __( 'Price: ', 'tourfic' ) . wc_price( $item[ 'tf_option_room_price_' . $i ] ) . '<br><br>';
								} else if ( $item[ 'tf_room_option_' . $i ] == '1' && $item[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item[ 'tf_option_title_' . $i ] . '<br>';
									$item['title'] .= __( 'Adult: ', 'tourfic' ) . wc_price( $item[ 'tf_option_adult_price_' . $i ] ) . '<br>';
									$item['title'] .= __( 'Child: ', 'tourfic' ) . wc_price( $item[ 'tf_option_child_price_' . $i ] ) . '<br><br>';
								}
							}
						}
					}

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $room_avail_data );
			} else {
				$room_avail_data = [];
			}

			return $room_avail_data;
		}

		/*
		 * Update Hotel Room Availability
		 * @author Foysal
		 */
		function tf_update_hotel_room_availability( $request ) {
			$id            = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$price_by      = ! empty( $request->get_param( 'price_by' ) ) ? $request->get_param( 'price_by' ) : '';
			$check_in      = ! empty( $request->get_param( 'check_in' ) ) ? $request->get_param( 'check_in' ) : '';
			$check_out     = ! empty( $request->get_param( 'check_out' ) ) ? $request->get_param( 'check_out' ) : '';
			$price         = ! empty( $request->get_param( 'price' ) ) ? $request->get_param( 'price' ) : '';
			$adult_price   = ! empty( $request->get_param( 'adult_price' ) ) ? $request->get_param( 'adult_price' ) : '';
			$child_price   = ! empty( $request->get_param( 'child_price' ) ) ? $request->get_param( 'child_price' ) : '';
			$status        = ! empty( $request->get_param( 'status' ) ) ? $request->get_param( 'status' ) : '';
			$avail_date    = ! empty( $request->get_param( 'avail_date' ) ) ? $request->get_param( 'avail_date' ) : '';
			$options_count = ! empty( $request->get_param( 'options_count' ) ) ? intval($request->get_param( 'options_count' )) : 0;

			if(empty($id)){
				return rest_ensure_response( [
					'status'  => false,
					'message' => __( 'Publish the Room First!', 'tourfic' )
				] );
			}

			if ( empty( $check_in ) || empty( $check_out ) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' )
				) );
			}

			$check_in  = strtotime( $check_in );
			$check_out = strtotime( $check_out );
			if ( $check_in > $check_out ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Check in date must be less than check out date.', 'tourfic' )
				) );
			}

			$room_avail_data = [];
			for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
				$tf_room_date = date( 'Y/m/d', $i );
				$tf_room_data = [
					'check_in'    => $tf_room_date,
					'check_out'   => $tf_room_date,
					'price_by'    => $price_by,
					'price'       => $price,
					'adult_price' => $adult_price,
					'child_price' => $child_price,
					'status'      => $status
				];

				if ( $options_count != 0 ) {
					$options_data = [
						'options_count' => $options_count,
					];
					for ( $j = 0; $j <= $options_count - 1; $j ++ ) {
						$options_data[ 'tf_room_option_' . $j ]         = ! empty( $request->get_param( 'tf_room_option_' . $j ) ) ? sanitize_text_field( $request->get_param( 'tf_room_option_' . $j ) ) : '';
						$options_data[ 'tf_option_title_' . $j ]        = ! empty( $request->get_param( 'tf_option_title_' . $j ) ) ? sanitize_text_field( $request->get_param( 'tf_option_title_' . $j ) ) : '';
						$options_data[ 'tf_option_pricing_type_' . $j ] = ! empty( $request->get_param( 'tf_option_pricing_type_' . $j ) ) ? sanitize_text_field( $request->get_param( 'tf_option_pricing_type_' . $j ) ) : '';
						$options_data[ 'tf_option_room_price_' . $j ]   = ! empty( $request->get_param( 'tf_option_room_price_' . $j ) ) ? sanitize_text_field( $request->get_param( 'tf_option_room_price_' . $j ) ) : '';
						$options_data[ 'tf_option_adult_price_' . $j ]  = ! empty( $request->get_param( 'tf_option_adult_price_' . $j ) ) ? sanitize_text_field( $request->get_param( 'tf_option_adult_price_' . $j ) ) : '';
						$options_data[ 'tf_option_child_price_' . $j ]  = ! empty( $request->get_param( 'tf_option_child_price_' . $j ) ) ? sanitize_text_field( $request->get_param( 'tf_option_child_price_' . $j ) ) : '';
					}
				}
				if ( ! empty( $options_data ) ) {
					$tf_room_data = array_merge( $tf_room_data, $options_data );
				}

				$room_avail_data[ $tf_room_date ] = $tf_room_data;
			}

			if ( ! empty( $id ) ) {
				$room_meta  = get_post_meta( $id, 'tf_room_opt', true );
				$avail_date = gettype($room_meta['avail_date']) == 'string' ? json_decode( $room_meta['avail_date'], true ) : $room_meta['avail_date'];
				if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
					$room_avail_data = array_merge( $avail_date, $room_avail_data );
				}
				$room_meta['avail_date'] = wp_json_encode( $room_avail_data );
				update_post_meta( $id, 'tf_room_opt', $room_meta );
			} else {
				$avail_date = json_decode( stripslashes( $avail_date ), true );
				if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
					$room_avail_data = array_merge( $avail_date, $room_avail_data );
				}
				update_option( 'tf_hotel_avail_date', $room_avail_data );
			}

			if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
				$room_events_data = array_values( $room_avail_data );
				$room_events_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					if ( $item['price_by'] == '1' ) {
						$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] );
					} elseif ( $item['price_by'] == '2' ) {
						$item['title'] = __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );
					} elseif ( $item['price_by'] == '3' ) {
						$item['title'] = '';
						if ( ! empty( $item['options_count'] ) ) {
							for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
								if ( $item[ 'tf_room_option_' . $i ] == '1' && $item[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item[ 'tf_option_title_' . $i ] . '<br>';
									$item['title'] .= __( 'Price: ', 'tourfic' ) . wc_price( $item[ 'tf_option_room_price_' . $i ] ) . '<br><br>';
								} else if ( $item[ 'tf_room_option_' . $i ] == '1' && $item[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item[ 'tf_option_title_' . $i ] . '<br>';
									$item['title'] .= __( 'Adult: ', 'tourfic' ) . wc_price( $item[ 'tf_option_adult_price_' . $i ] ) . '<br>';
									$item['title'] .= __( 'Child: ', 'tourfic' ) . wc_price( $item[ 'tf_option_child_price_' . $i ] ) . '<br><br>';
								}
							}
						}
					}

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $room_events_data );
			} else {
				$room_events_data = [];
			}

			return rest_ensure_response( array(
				'status'             => true,
				'message'            => esc_html__( 'Availability updated successfully.', 'tourfic' ),
				'avail_date'         => $room_events_data,
				'avail_date_encoded' => json_encode( $room_avail_data ),
			) );
		}

		/*
		 * Delete Hotel Room Availability
		 * @author Foysal
		 */
		function tf_delete_hotel_room_availability( $request ) {
			$room_id  = $request->get_param( 'id' );
			$room_data = get_post_meta( $room_id, 'tf_room_opt', true );
			$room_data['avail_date'] = [];

			update_post_meta( $room_id, 'tf_room_opt', $room_data );

			return rest_ensure_response( array(
				'status'             => true,
				'message'            => esc_html__( 'Availability reset successfully.', 'tourfic' ),
				'avail_date'         => [],
				'avail_date_encoded' => json_encode([]),
			) );
		}

		/*
		 * Import Hotel iCal
		 * @author Foysal
		 */
		function tf_hotel_ical_import( $request ) {
			$ical_url   = ! empty( $request->get_param( 'ical_url' ) ) ? $request->get_param( 'ical_url' ) : '';
			$hotel_id   = ! empty( $request->get_param( 'hotel_id' ) ) ? $request->get_param( 'hotel_id' ) : '';
			$room_index = intval( $request->get_param( 'room_index' ) );
			$pricing_by = ! empty( $request->get_param( 'pricing_by' ) ) ? $request->get_param( 'pricing_by' ) : '';

			if ( empty( $ical_url ) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please enter iCal URL.', 'tourfic' )
				) );
			}

			$hotel_avail_data = get_post_meta( $hotel_id, 'tf_hotels_opt', true );

			try {
				$ical = new TF_FD_ICal_Reader( $ical_url );

				if ( ! empty( $ical ) ) {
					$events = $ical->events();

					if ( ! empty( $events ) && is_array( $events ) ) {
						$date_keys = function_exists( 'tf_ical_get_unavailable_date_keys' ) ? tf_ical_get_unavailable_date_keys( $events ) : array();

						$room_avail_data = [];
						foreach ( $date_keys as $date_key ) {
							$room_avail_data[ $date_key ] = array(
								'check_in'    => $date_key,
								'check_out'   => $date_key,
								'price_by'    => $pricing_by,
								'price'       => '',
								'adult_price' => '',
								'child_price' => '',
								'status'      => 'unavailable',
							);
						}

						if ( ! empty( $hotel_avail_data ) ) {
							$avail_date = json_decode( $hotel_avail_data['room'][ $room_index ]['avail_date'], true );
							if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
								$room_avail_data = array_merge( $avail_date, $room_avail_data );
							}
							$hotel_avail_data['room'][ $room_index ]['avail_date'] = json_encode( $room_avail_data );
							update_post_meta( $hotel_id, 'tf_hotels_opt', $hotel_avail_data );
						}

						if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
							$room_events_data = array_values( $room_avail_data );
							$room_events_data = array_map( function ( $item ) {
								$item['start'] = date( 'Y-m-d', strtotime( $item['check_in'] ) );
								$item['title'] = esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>' . esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );

								if ( $item['status'] == 'unavailable' ) {
									$item['display'] = 'background';
									$item['color']   = '#003c79';
								}

								return $item;
							}, $room_events_data );
						} else {
							$room_events_data = [];
						}

						return rest_ensure_response( array(
							'status'             => true,
							'message'            => esc_html__( 'iCal imported successfully.', 'tourfic' ),
							'avail_date'         => $room_events_data,
							'avail_date_encoded' => json_encode( $room_avail_data ),
						) );
					}
				} else {
					return rest_ensure_response( array(
						'status'  => 'error',
						'message' => esc_html__( 'Failed to create iCal object.', 'tourfic' )
					) );
				}
			} catch ( Exception $e ) {
				return rest_ensure_response( array(
					'status'  => 'error',
					'message' => $e->getMessage()
				) );
			}
		}
	}
}

TF_Hotel_Rest_API::get_instance();
