<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_User_Rest_API' ) ) {
	class TF_User_Rest_API extends TF_Rest_API {

		protected $log_reg_settings = array();

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
		 * Get Users
		 * @author Foysal
		 */
		public function tf_get_users( $request ) {
			$user_roles = $request->get_param( 'roles' ) ? $request->get_param( 'roles' ) : array();
			$users      = get_users( array(
				'role__in' => $user_roles,
				'number'   => - 1,
			) );

			$users_data = array();

			foreach ( $users as $user ) {
				global $wpdb;
				$tf_vendor_order_earning = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_balance_history WHERE wstatus = %s AND vendor_id = %s", "completed", $user->ID ), ARRAY_A );
				$total_earning           = ! empty( $tf_vendor_order_earning ) ? wc_price( $tf_vendor_order_earning[0]['SUM(amount)'] ) : wc_price( 0 );

				$tf_vendor_balace  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_balance WHERE vendor_id = %s", $user->ID ) );
				$total_earning_int = ! empty( $tf_vendor_balace->total_amount ) ? $tf_vendor_balace->total_amount : 0;
				$total_withdraw    = ! empty( $tf_vendor_balace->total_withdraw ) ? wc_price( $tf_vendor_balace->total_withdraw ) : wc_price( 0 );
				$vendor_status     = get_user_meta( $user->ID, 'tf_vendor_approval', true );
				$users_data[]      = array(
					'id'                 => $user->ID,
					'username'           => $user->user_login,
					'email'              => $user->user_email,
					'name'               => $user->display_name,
					'first_name'         => $user->user_firstname,
					'last_name'          => $user->user_lastname,
					'phone'              => get_user_meta( $user->ID, 'tf_user_phone', true ),
					'url'                => $user->user_url,
					'description'        => $user->description,
					'link'               => get_author_posts_url( $user->ID ),
					'locale'             => get_user_locale( $user ),
					'nickname'           => $user->user_nicename,
					'slug'               => $user->user_nicename,
					'roles'              => $user->roles,
					'status'             => $vendor_status === 'enabled' ? esc_html__( 'Approved', 'tourfic' ) : esc_html__( 'Pending', 'tourfic' ),
					'registered_date'    => date( "M d, Y", strtotime( $user->user_registered ) ),
					'total_earning_int'  => $total_earning_int,
					'total_earning'      => $total_earning,
					'total_withdraw'     => $total_withdraw,
					'capabilities'       => $user->allcaps,
					'extra_capabilities' => $user->caps,
					'avatar_urls'        => array(
						'24'  => get_avatar_url( $user->ID, array( 'size' => 24 ) ),
						'48'  => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
						'96'  => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
						'256' => get_avatar_url( $user->ID, array( 'size' => 256 ) ),
					),
					'avatar'             => get_user_meta( $user->ID, 'tf_user_image', true ),
					'cover_pic'          => get_user_meta( $user->ID, 'tf_cover_photo', true ),
					'is_super_admin'     => is_super_admin( $user->ID ),
				);
			}

			return rest_ensure_response( $users_data );
		}

		/*
		 * Get User
		 * @author Foysal
		 */
		public function tf_get_user( $request ) {
			$user_id = $request->get_param( 'id' );
			$user    = get_user_by( 'id', $user_id );

			if ( ! $user ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}

			$user_data = array(
				'id'                 => $user->ID,
				'username'           => $user->user_login,
				'name'               => $user->display_name,
				'first_name'         => $user->user_firstname,
				'last_name'          => $user->user_lastname,
				'email'              => $user->user_email,
				'url'                => $user->user_url,
				'description'        => $user->description,
				'link'               => get_author_posts_url( $user->ID ),
				'locale'             => get_user_locale( $user ),
				'nickname'           => $user->user_nicename,
				'slug'               => $user->user_nicename,
				'roles'              => $user->roles,
				'registered_date'    => $user->user_registered,
				'is_super_admin'     => is_super_admin( $user->ID ),
				'capabilities'       => $user->allcaps,
				'extra_capabilities' => $user->caps,
				'avatar_urls'        => array(
					'24'  => get_avatar_url( $user->ID, array( 'size' => 24 ) ),
					'48'  => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
					'96'  => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
					'256' => get_avatar_url( $user->ID, array( 'size' => 256 ) ),
				),
				'avatar'             => get_user_meta( $user->ID, 'tf_user_image', true ),
				'cover_pic'          => get_user_meta( $user->ID, 'tf_cover_photo', true ),
				'tf_user_phone'      => get_user_meta( $user->ID, 'tf_user_phone', true )
			);

			if ( $this->user_has_role( $user_id, 'tf_vendor' ) ) {
				global $wpdb;
				$tf_vendor_balace  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_balance WHERE vendor_id = %s", $user->ID ) );
				$total_earning     = ! empty( $tf_vendor_balace->total_amount ) ? wc_price( $tf_vendor_balace->total_amount ) : wc_price( 0 );
				$total_earning_int = ! empty( $tf_vendor_balace->total_amount ) ? $tf_vendor_balace->total_amount : 0;
				$total_withdraw    = ! empty( $tf_vendor_balace->total_withdraw ) ? wc_price( $tf_vendor_balace->total_withdraw ) : wc_price( 0 );
				$vendor_status     = get_user_meta( $user->ID, 'tf_vendor_approval', true );

				$user_data['legal_name']                    = get_user_meta( $user->ID, 'tf_is_name', true );
				$user_data['paypal_info']                   = get_user_meta( $user->ID, 'tf_paypal_info', true );
				$user_data['payoneer_info']                 = get_user_meta( $user->ID, 'tf_payoneer_info', true );
				$user_data['wise_info']                     = get_user_meta( $user->ID, 'tf_wise_info', true );
				$user_data['bank_info']                     = get_user_meta( $user->ID, 'tf_bank_info', true );
				$user_data['commission']                    = get_user_meta( $user->ID, 'tf_user_commission', true );
				$user_data['tf_vendor_enabled']             = get_user_meta( $user->ID, 'tf_vendor_approval', true ) == 'enabled' ? '1' : '0';
				$user_data['tf_vendor_posts']               = get_user_meta( $user->ID, 'tf_will_autopublish', true ) == 1 ? '0' : '1';
				$user_data['tf_vendor_featured_permission'] = get_user_meta( $user->ID, 'tf_vendor_featured_permission', true );
				$user_data['tf_vendor_featured_limit']      = get_user_meta( $user->ID, 'tf_vendor_featured_limit', true );
				$user_data['total_earning_int']             = $total_earning_int;
				$user_data['total_earning']                 = $total_earning;
				$user_data['total_withdraw']                = $total_withdraw;
				$user_data['status']                        = $vendor_status;

				$vendor_fields = ! empty( Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] ) ? Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] : '';
				if ( is_array( $vendor_fields ) && ! empty( $vendor_fields ) ) {
					foreach ( $vendor_fields as $field ) {
						if ( $field['reg-fields-type'] == 'checkbox' ) {
							$checkbox_data                         = get_user_meta( $user->ID, $field['reg-field-name'], true );
							$user_data[ $field['reg-field-name'] ] = ! empty( $checkbox_data ) ? explode( '|', $checkbox_data ) : array();
						} else {
							$user_data[ $field['reg-field-name'] ] = get_user_meta( $user->ID, $field['reg-field-name'], true );
						}
					}
				}

				//google data
				$_tf_integration_settings = is_array( get_user_meta( $user->ID, '_tf_integration_settings', true ) ) ? get_user_meta( $user->ID, '_tf_integration_settings', true ) : array();
				$user_data['tf_google_client_id'] = get_user_meta( $user->ID, 'tf_google_client_id', true );
				$user_data['tf_google_secret_key'] = get_user_meta( $user->ID, 'tf_google_secret_key', true );
				$user_data['tf_google_redirect_url'] = site_url().'/wp-json/tourfic/v1/integration/google-api';
				$user_data['tf_google_refresh_token'] = isset( $_tf_integration_settings['google_calendar']['tf_google_calendar']['refresh_token'] ) ? $_tf_integration_settings['google_calendar']['tf_google_calendar']['refresh_token'] : '';
			}

			return rest_ensure_response( $user_data );
		}

		/*
		 * User Bookings
		 * @auther Foysal
		 */
		public function tf_user_bookings( $request ) {
			$current_user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			$booking_type    = $request->get_param( 'booking_type' ) ? $request->get_param( 'booking_type' ) : 'all';
			$disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : [];

			if ( $this->user_has_role( $current_user_id, 'customer' ) ) {

				if($booking_type == 'all'){
					$post_types = array();
					if (! in_array('hotel', $disable_services)){
						$post_types[] = 'hotel';
					}
					if (! in_array('tour', $disable_services)){
						$post_types[] = 'tour';
					}
					if (! in_array('apartment', $disable_services)){
						$post_types[] = 'apartment';
					}
					if (! in_array('carrentals', $disable_services)){
						$post_types[] = 'car';
					}
				} else {
					$post_types = array( $booking_type );
				}

				$tf_orders_select = array(
					'select'      => "*",
					'post_type'   =>  $post_types,
					'customer_id' => $current_user_id,
					'limit'       => ""
				);

				$user_hotel_orders_result = tourfic_get_user_order_table_data( $tf_orders_select );
			}
			$orders_data = array();
			foreach ( $user_hotel_orders_result as $order ) {
				//payment method
				$payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
				if ( isset( $payment_gateways[ $order['payment_method'] ] ) ) {
					$order['payment_method'] = $payment_gateways[ $order['payment_method'] ]->title;
				} else {
					$order['payment_method'] = __( 'Offline Payment', 'tourfic' );
				}

				//total price
				$order_details = json_decode( $order['order_details'] );
				$hotel_price   = "";
				if ( ! empty( $order_details->total_price ) ) {
					$hotel_price .= '<b>' . __( "Total", "tourfic" ) . ': </b>' . wc_price( $order_details->total_price ) . '<br>';
				}
				if ( ! empty( $order_details->due_price ) ) {
					$hotel_price .= '<b>' . __( "Due", "tourfic" ) . ': </b>' . $order_details->due_price . '<br>';
				}
				$order['total_price'] = $hotel_price;

				$orders_data[] = $order;
			}

			return $orders_data;

		}

		/*
		 * User Wishlist
		 * @auther Foysal
		 */
		public function tf_user_wishlist( $request ) {
			$current_user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			$remove          = $request->get_param( 'remove' ) ? $request->get_param( 'remove' ) : false;
			$postId          = $request->get_param( 'post_id' ) ? $request->get_param( 'post_id' ) : '';
			$wishlist_data = array();
			
			if ( $this->user_has_role( $current_user_id, 'customer' ) ) {
				$wishlist_items = get_user_meta( $current_user_id, 'wishlist_item', false );

				if ( $remove == true && ! empty( $postId ) ) {
					$post_id = array_search($postId, array_column($wishlist_items, 'post_id'));
					delete_user_meta($current_user_id, 'wishlist_item', $wishlist_items[$post_id]);
				}

				$wishlist_items = get_user_meta( $current_user_id, 'wishlist_item', false );
				
				foreach ( $wishlist_items as $item ) {
					$wishlist_data[] = get_post( $item['post_id'] );
				}
			}

			return $wishlist_data;
		}
	}
}

TF_User_Rest_API::get_instance();
