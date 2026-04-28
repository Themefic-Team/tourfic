<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Rental_Rest_API' ) ) {
	class TF_Rental_Rest_API extends TF_Rest_API {

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
			add_action( 'rest_api_init', array( $this, 'add_rental_meta_to_rest_api' ) );
		}

		/*
		 * Get Rentals
		 * @author Foysal
		 */
		public function tf_get_rentals( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'author' ) ? $request->get_param( 'author' ) : get_current_user_id();

			$query_rentals = new \WP_Query( array(
				'post_type'      => 'tf_carrental',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) || $this->user_has_role( $author, 'tf_manager' ) ? '' : $author,
				'paged'          => $page,
			) );
			$rentals       = array();
			if ( $query_rentals->have_posts() ) {
				while ( $query_rentals->have_posts() ) {
					$query_rentals->the_post();
					$rental_id = get_the_ID();

					$rental_data   = array();
					
					$rental_data['id']             = $rental_id;
					$rental_data['permalink']      = get_permalink( $rental_id );
					$rental_data['title']          = get_the_title( $rental_id );
					$rental_data['content']        = get_the_content( $rental_id );
					$rental_data['status']         = get_post_status( $rental_id );
					$rental_data['author']         = get_the_author_meta( 'display_name', get_post_field( 'post_author', $rental_id ) );
					$rental_data['carrental_location'] = $this->tf_get_post_terms( $rental_id, 'carrental_location' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_location' ) : '—';
					$rental_data['carrental_brand'] = $this->tf_get_post_terms( $rental_id, 'carrental_brand' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_brand' ) : '—';
					$rental_data['carrental_fuel_type'] = $this->tf_get_post_terms( $rental_id, 'carrental_fuel_type' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_fuel_type' ) : '—';
					$rental_data['carrental_engine_year'] = $this->tf_get_post_terms( $rental_id, 'carrental_engine_year' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_engine_year' ) : '—';
					$rental_data['carrental_category']  = $this->tf_get_post_terms( $rental_id, 'carrental_category' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_category' ) : '—';
					$rental_data['date']           = get_the_date( '', $rental_id );
					$rental_data['featured_image'] = get_the_post_thumbnail_url( $rental_id );
					$rental_data['tf_carrental_opt']  = get_post_meta( $rental_id, 'tf_carrental_opt', true );
					$rentals[]                     = $rental_data;
				}
			}
			wp_reset_postdata();
			$rentals = array(
				'rentals' => $rentals,
				'total'  => $query_rentals->found_posts,
			);

			return $rentals;
		}

		/*
		 * Add Rental
		 * @author Foysal
		 */
		public function tf_add_rental( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$rental_id = wp_insert_post( array(
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_type'    => 'tf_carrental',
				'post_status'  => $user->has_cap( 'publish_tf_carrentals' ) ? 'publish' : 'pending',
				'post_author'  => get_current_user_id(),
			) );

			if ( $rental_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $rental_id, $request['featured_media'] );
				}
				if ( isset( $request['carRentalLocations'] ) && ! empty( $request['carRentalLocations'] ) ) {
					$carRentalLocations = array_map( 'intval', $request['carRentalLocations'] );
					wp_set_object_terms( $rental_id, $carRentalLocations, 'carrental_location' );
				}
				if ( isset( $request['carRentalCategories'] ) && ! empty( $request['carRentalCategories'] ) ) {
					$carRentalCategories = array_map( 'intval', $request['carRentalCategories'] );
					wp_set_object_terms( $rental_id, $carRentalCategories, 'carrental_category' );
				}

				if ( isset( $request['tf_carrental_opt'] ) ) {
					update_post_meta( $rental_id, 'tf_carrental_opt', $request['tf_carrental_opt'] );
				}
			}

			return $rental_id;
		}

		/*
		 * Update Rental
		 * @author Foysal
		 */
		public function tf_update_rental( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$rental_id    = $request['id'];
			$post_status = get_post_status( $rental_id );
			$rental       = array(
				'ID'           => $rental_id,
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_status'  => $post_status == 'publish' ? 'publish' : ( $user->has_cap( 'publish_tf_carrentals' ) ? 'publish' : 'pending' ),
				'post_type'    => 'tf_carrental',
				'post_author'  => $this->user_has_role( get_current_user_id(), 'administrator' ) ? $this->tf_get_post_author_id( $rental_id ) : get_current_user_id(),
			);

			$rental_id = wp_update_post( $rental );

			if ( $rental_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $rental_id, $request['featured_media'] );
				}
				if ( isset( $request['carRentalLocations'] ) && ! empty( $request['carRentalLocations'] ) ) {
					$carRentalLocations = array_map( 'intval', $request['carRentalLocations'] );
					wp_set_object_terms( $rental_id, $carRentalLocations, 'carrental_location' );
				}
				if ( isset( $request['carRentalCategories'] ) && ! empty( $request['carRentalCategories'] ) ) {
					$carRentalCategories = array_map( 'intval', $request['carRentalCategories'] );
					wp_set_object_terms( $rental_id, $carRentalCategories, 'carrental_category' );
				}

				if ( isset( $request['tf_carrental_opt'] ) ) {
					update_post_meta( $rental_id, 'tf_carrental_opt', $request['tf_carrental_opt'] );
				}
			}

			return $rental_id;
		}

		/*
		 * Update Rental Status
		 * @auther Foysal
		 */
		public function tf_update_rental_status( $request ) {
			$current_user_id = get_current_user_id();
			$id              = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$rental_status    = ! empty( $request->get_param( 'rental_status' ) ) ? $request->get_param( 'rental_status' ) : '';
			$user            = get_user_by( 'id', $current_user_id );

			if ( $user->has_cap( 'publish_tf_carrentals' ) ) {
				$rental = array(
					'ID'          => $id,
					'post_status' => $rental_status,
				);

				$rental_id = wp_update_post( $rental );
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => esc_html__('Car Rental status updated successfully.', 'tourfic'),
			) );
		}

		/*
		 * Get Rental order data
		 * @author Foysal
		 */
		public function tf_get_rental_orders( $request ) {
			$current_user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			if ( $this->user_has_role( $current_user_id, 'administrator' ) || $this->user_has_role( $current_user_id, 'tf_manager' ) ) {

				$tf_orders_select = array(
					'select'    => "*",
					'post_type' => 'car',
					'query'     => " ORDER BY order_date DESC"
				);

				$rental_orders_result = Helper::tourfic_order_table_data( $tf_orders_select );
			}
			if ( $this->user_has_role( $current_user_id, 'tf_vendor' ) ) {

				$tf_orders_select = array(
					'select'    => "*",
					'post_type' => "car",
					'author'    => $current_user_id,
					'limit'     => ""
				);

				$rental_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			}
			$orders_data = array();
			foreach ( $rental_orders_result as $order ) {
				//payment method
				$payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
				if ( isset( $payment_gateways[ $order['payment_method'] ] ) ) {
					$order['payment_method'] = $payment_gateways[ $order['payment_method'] ]->title;
				} else {
					$order['payment_method'] = esc_html__( 'Offline Payment', 'tourfic' );
				}

				//total price
				$order_details = json_decode( $order['order_details'] );
				$rental_price   = "";
				if ( ! empty( $order_details->total_price ) ) {
					$rental_price .= '<b>' . esc_html__( "Total", "tourfic" ) . ': </b>' . wc_price( $order_details->total_price ) . '<br>';
				}
				if ( ! empty( $order_details->due ) ) {
					$rental_price .= '<b>' . esc_html__( "Due", "tourfic" ) . ': </b>' . $order_details->due . '<br>';
				}
				$order['total_price'] = $rental_price;

				$orders_data[] = $order;
			}

			return array(
				'orders' => $orders_data,
				'total'  => count( $orders_data ),
			);
		}

		/*
		 * Get Rental order details rental-order/{id}
		 * @author Foysal
		 */
		public function tf_get_rental_order_details( $request ) {
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
			if ( ! empty( $order_details->due ) ) {
				$order['due_price'] = wc_price( $order_details->due );
			}

			//rental order details
			if ( ! empty( $order['post_id'] ) ) {
				$order['order_detail']['rental_name'] = '<a href="' . get_the_permalink( $order['post_id'] ) . '" target="_blank">' . get_the_title( $order['post_id'] ) . '</a>';
			}
			if ( ! empty( $order_details->pickup_location ) ) {
				$order['order_detail']['pickup_location'] = $order_details->pickup_location;
			}
			if ( ! empty( $order_details->pickup_date ) ) {
				$order['order_detail']['pickup_date'] = $order_details->pickup_date;
			}
			if ( ! empty( $order_details->pickup_time ) ) {
				$order['order_detail']['pickup_time'] = $order_details->pickup_time;
			}
			if ( ! empty( $order_details->dropoff_location ) ) {
				$order['order_detail']['dropoff_location'] = $order_details->dropoff_location;
			}
			if ( ! empty( $order_details->dropoff_date ) ) {
				$order['order_detail']['dropoff_date'] = $order_details->dropoff_date;
			}
			if ( ! empty( $order_details->dropoff_time ) ) {
				$order['order_detail']['dropoff_time'] = $order_details->dropoff_time;
			}
			if ( ! empty( $order_details->extra ) ) {
				$order['order_detail']['extra'] = $order_details->extra;
			}
			if ( ! empty( $order_details->protection ) ) {
				$order['order_detail']['protection'] = $order_details->protection;
			}
			if ( ! empty( $order['check_in'] ) ) {
				$order['check_in'] = $order['check_in'];
			}
			if ( ! empty( $order['check_out'] ) ) {
				$order['check_out'] = $order['check_out'];
			}

			return $order;
		}

		/*
		 * Add rental meta to /wp-json/wp/v2/tf_carrental api
		 * @author Foysal
		 */
		function add_rental_meta_to_rest_api() {
			register_rest_field( 'tf_carrental', 'tf_carrental_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_carrental_opt  = get_post_meta( $post_arr['id'], 'tf_carrental_opt', true );
					$unserialize_array = array(
						'map',
					);
					foreach ( $unserialize_array as $item ) {
						if ( ! empty( $tf_carrental_opt[ $item ] ) && is_serialized( $tf_carrental_opt[ $item ] ) ) {
							$tf_carrental_opt[ $item ] = unserialize( $tf_carrental_opt[ $item ] );
						}
					}

					return $tf_carrental_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_rental', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}
	}
}

TF_Rental_Rest_API::get_instance();