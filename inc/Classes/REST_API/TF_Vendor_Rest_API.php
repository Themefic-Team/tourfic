<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Vendor_Rest_API' ) ) {
	class TF_Vendor_Rest_API extends TF_Rest_API {

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

		public function __construct() {
			parent::__construct();
		}

		/*
		 * Get TF Reports data
		 * @auther Foysal
		 */
		public function tf_get_tf_reports( $request ) {
			$current_user_id = get_current_user_id();
			global $wpdb;
			if ( $this->user_has_role( $current_user_id, 'administrator' ) || $this->user_has_role( $current_user_id, 'tf_manager' ) ) {
				$tf_vendor_payouts            = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_withdraw WHERE  wstatus = %s", "completed" ), ARRAY_A );
				$tf_vendor_order_paid_earning = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_withdraw  where wstatus = %s and MONTH(udate)=MONTH(now()) and YEAR(udate)=YEAR(now())", "completed" ), ARRAY_A );
				$tf_vendor_total_order_amount = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount), SUM(comission) FROM {$wpdb->prefix}tf_vendor_balance_history where wstatus = %s and MONTH(odate)=MONTH(now()) and YEAR(odate)=YEAR(now())", "completed" ), ARRAY_A );
				$tf_admin_total_commission    = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(comission) FROM {$wpdb->prefix}tf_vendor_balance_history where wstatus = %s", "completed" ), ARRAY_A );
				$tf_admin_total_commission    = ! empty( $tf_admin_total_commission ) ? strip_tags( wc_price( $tf_admin_total_commission[0]['SUM(comission)'] ) ) : strip_tags( wc_price( 0 ) );

			} elseif ( $this->user_has_role( $current_user_id, 'tf_vendor' ) ) {

				$tf_vendor_payouts            = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_withdraw WHERE vendor_id=%d AND wstatus = %s", $current_user_id, "completed" ), ARRAY_A );
				$tf_vendor_order_paid_earning = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_withdraw WHERE vendor_id=%d AND wstatus = %s AND MONTH(udate)=MONTH(now()) AND YEAR(udate)=YEAR(now())", $current_user_id, "completed" ), ARRAY_A );
				$tf_vendor_total_order_amount = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount), SUM(comission) FROM {$wpdb->prefix}tf_vendor_balance_history WHERE vendor_id=%d AND wstatus = %s AND MONTH(odate)=MONTH(now()) AND YEAR(odate)=YEAR(now())", $current_user_id, "completed" ), ARRAY_A );
				$tf_admin_total_commission    = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_balance_history WHERE vendor_id=%d AND wstatus = %s", $current_user_id, "completed" ), ARRAY_A );
				$tf_admin_total_commission    = ! empty( $tf_admin_total_commission ) ? strip_tags( wc_price( $tf_admin_total_commission[0]['SUM(amount)'] ) ) : strip_tags( wc_price( 0 ) );
			}

			$tf_vendor_payouts            = ! empty( $tf_vendor_payouts ) ? strip_tags( wc_price( $tf_vendor_payouts[0]['SUM(amount)'] ) ) : strip_tags( wc_price( 0 ) );
			$tf_vendor_order_paid_earning = ! empty( $tf_vendor_order_paid_earning ) ? strip_tags( wc_price( $tf_vendor_order_paid_earning[0]['SUM(amount)'] ) ) : strip_tags( wc_price( 0 ) );
			$tf_vendor_total_order_amount = ! empty( $tf_vendor_total_order_amount ) ? strip_tags( wc_price( $tf_vendor_total_order_amount[0]['SUM(amount)'] + $tf_vendor_total_order_amount[0]['SUM(comission)'] ) ) : strip_tags( wc_price( 0 ) );

			$report_data = array(
				'total_payouts'                 => $tf_vendor_payouts,
				'total_payouts_this_month'      => $tf_vendor_order_paid_earning,
				'total_order_amount_this_month' => $tf_vendor_total_order_amount,
				'total_earnings_this_month'     => $tf_admin_total_commission,
			);

			return $report_data;
		}

		/*
		 * Get TF Vendor Reports
		 * @auther Foysal
		 */
		public function tf_get_tf_vendor_reports( $request ) {
			$user_id = $request->get_param( 'user_id' );

			global $wpdb;
			$tf_vendor_order_earning = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_balance_history WHERE wstatus = %s AND vendor_id = %s", "completed", $user_id ), ARRAY_A );
			if ( ! empty( $tf_vendor_order_earning ) ) {
				$tf_vendor_order_amount  = $tf_vendor_order_earning[0]['SUM(amount)'];
				$tf_vendor_order_earning = strip_tags( wc_price( $tf_vendor_order_earning[0]['SUM(amount)'] ) );
			} else {
				$tf_vendor_order_amount  = 0;
				$tf_vendor_order_earning = strip_tags( wc_price( 0 ) );
			}

			$tf_vendor_order_paid_earning = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_withdraw WHERE  wstatus = %s AND vendor_id = %s", "completed", $user_id ), ARRAY_A );
			if ( ! empty( $tf_vendor_order_paid_earning ) ) {
				$tf_vendor_order_paid_amount  = $tf_vendor_order_paid_earning[0]['SUM(amount)'];
				$tf_vendor_order_paid_earning = strip_tags( wc_price( $tf_vendor_order_paid_earning[0]['SUM(amount)'] ) );
			} else {
				$tf_vendor_order_paid_amount  = 0;
				$tf_vendor_order_paid_earning = strip_tags( wc_price( 0 ) );
			}

			/*Average*/
			$tf_vendor_order_earning_avg = $wpdb->get_results( $wpdb->prepare( "SELECT AVG(amount) FROM {$wpdb->prefix}tf_vendor_balance_history WHERE  wstatus = %s AND vendor_id = %s", "completed", $user_id ), ARRAY_A );
			if ( ! empty( $tf_vendor_order_earning_avg ) ) {
				$tf_vendor_earning_avg       = $tf_vendor_order_earning_avg[0]['AVG(amount)'];
				$tf_vendor_order_earning_avg = strip_tags( wc_price( $tf_vendor_order_earning_avg[0]['AVG(amount)'] ) );
			} else {
				$tf_vendor_earning_avg       = 0;
				$tf_vendor_order_earning_avg = strip_tags( wc_price( 0 ) );
			}

			$tf_vendor_order_paid_earning_avg = $wpdb->get_results( $wpdb->prepare( "SELECT AVG(amount) FROM {$wpdb->prefix}tf_vendor_withdraw WHERE  wstatus = %s AND vendor_id = %s", "completed", $user_id ), ARRAY_A );
			if ( ! empty( $tf_vendor_order_paid_earning_avg ) ) {
				$tf_vendor_paid_earning_avg       = $tf_vendor_order_paid_earning_avg[0]['AVG(amount)'];
				$tf_vendor_order_paid_earning_avg = strip_tags( wc_price( $tf_vendor_order_paid_earning_avg[0]['AVG(amount)'] ) );
			} else {
				$tf_vendor_paid_earning_avg       = 0;
				$tf_vendor_order_paid_earning_avg = strip_tags( wc_price( 0 ) );
			}

			$vendor_reports   = function_exists( 'tf_vendor_reports' ) ? tf_vendor_reports($user_id) : array();

			$report_data = array(
				'earning'            => $tf_vendor_order_earning,
				'paid_earning'       => $tf_vendor_order_paid_earning,
				'unpaid_earning'     => strip_tags( wc_price( $tf_vendor_order_amount - $tf_vendor_order_paid_amount ) ),
				'avg_earning'        => $tf_vendor_order_earning_avg,
				'avg_paid_earning'   => $tf_vendor_order_paid_earning_avg,
				'avg_unpaid_earning' => strip_tags( wc_price( $tf_vendor_earning_avg - $tf_vendor_paid_earning_avg ) ),
				'earning_reports'    => $vendor_reports,
			);

			return $report_data;
		}

		/*
		 * Get commissions
		 * @auther Foysal
		 */
		public function tf_get_commissions( $request ) {
			$user_id          = $request->get_param( 'user_id' );
			$commissions_data = array();
			global $wpdb;
			if ( ! empty( $user_id ) && $this->user_has_role( $user_id, 'tf_vendor' ) ) {
				$tf_commissions = $wpdb->get_results( $wpdb->prepare( "SELECT order_id, SUM(amount), SUM(comission), odate FROM {$wpdb->prefix}tf_vendor_balance_history WHERE wstatus=%s AND vendor_id=%s GROUP BY order_id", "completed", $user_id ), ARRAY_A );
			} else {
				$tf_commissions = $wpdb->get_results( $wpdb->prepare( "SELECT order_id, SUM(amount), SUM(comission), odate FROM {$wpdb->prefix}tf_vendor_balance_history WHERE wstatus=%s GROUP BY order_id", "completed" ), ARRAY_A );
			}

			if ( ! empty( $tf_commissions ) ) {
				foreach ( $tf_commissions as $item ) {
					$commissions_data[] = array(
						'order_id'           => $item['order_id'],
						'order_type'         => get_post_meta( $item['order_id'], '_order_type', true ),
						'total_order_amount' => wc_price( $item['SUM(amount)'] + $item['SUM(comission)'] ),
						'admin_commission'   => wc_price( $item['SUM(comission)'] ),
						'vendor_earning'     => wc_price( $item['SUM(amount)'] ),
						'odate'              => date( "M d, Y", strtotime( $item['odate'] ) ),
						'edit_url'           => get_admin_url( null, 'post.php?post=' . sanitize_key( $item['order_id'] ) . '&action=edit' ),
					);
				}
			}

			return rest_ensure_response( $commissions_data );
		}

		/*
		 * Get payouts
		 * @auther Foysal
		 */
		public function tf_get_payouts( $request ) {
			$user_id      = $request->get_param( 'user_id' );
			$payouts_data = array();
			global $wpdb;
			if ( ! empty( $user_id ) && $this->user_has_role( $user_id, 'tf_vendor' ) ) {
				$tf_payouts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_withdraw WHERE vendor_id=%s", $user_id ), ARRAY_A );
			} else {
				$tf_payouts = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}tf_vendor_withdraw", ARRAY_A );
			}

			if ( ! empty( $tf_payouts ) ) {
				foreach ( $tf_payouts as $item ) {
					$vendor_info    = get_user_by( 'id', $item['vendor_id'] );
					$payouts_data[] = array(
						'id'             => $item['id'],
						'vendor_id'      => sprintf( '%s (%s)', $item['vendor_id'], $vendor_info->display_name ),
						'withdraw_date'  => date( "M d, Y", strtotime( $item['udate'] ) ),
						'amount'         => wc_price( $item['amount'] ),
						'payment_method' => $item['payment_method'],
						'payment_status' => $item['wstatus'],
						'note'           => $item['note'],
						'release_date'   => date( "M d, Y", strtotime( $item['rdate'] ) ),
					);
				}
			}

			return rest_ensure_response( $payouts_data );
		}

		/*
		 * Get payout
		 * @auther Foysal
		 */
		public function tf_get_payout( $request ) {
			$id = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			global $wpdb;
			$tf_payout = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_withdraw WHERE id = %s", $id ) );

			if ( ! empty( $tf_payout ) ) {
				$payout_data = array(
					'id'             => $tf_payout->id,
					'vendor_id'      => (int) $tf_payout->vendor_id,
					'payment_amount' => $tf_payout->amount,
					'payment_date'   => $tf_payout->udate,
					'release_date'   => $tf_payout->rdate,
					'payment_method' => $tf_payout->payment_method,
					'payment_status' => $tf_payout->wstatus,
					'payment_note'   => $tf_payout->note,
				);
			}

			return rest_ensure_response( $payout_data );
		}

		/*
		 * Add payout
		 * @auther Foysal
		 */
		public function tf_add_payout( $request ) {
			$vendor_id      = ! empty( $request->get_param( 'vendor_id' ) ) ? $request->get_param( 'vendor_id' ) : '';
			$payment_amount = ! empty( $request->get_param( 'payment_amount' ) ) ? $request->get_param( 'payment_amount' ) : '';
			$payment_date   = ! empty( $request->get_param( 'payment_date' ) ) ? $request->get_param( 'payment_date' ) : '';
			$release_date   = ! empty( $request->get_param( 'release_date' ) ) ? $request->get_param( 'release_date' ) : '';
			$payment_method = ! empty( $request->get_param( 'payment_method' ) ) ? $request->get_param( 'payment_method' ) : '';
			$payment_note   = ! empty( $request->get_param( 'payment_note' ) ) ? $request->get_param( 'payment_note' ) : '';

			global $wpdb;
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}tf_vendor_withdraw
							( vendor_id, amount, payment_method, note, wstatus, udate, rdate )
							VALUES ( %d, %s, %s, %s, %s, %s, %s )", array(
				floatval( $vendor_id ),
				floatval( $payment_amount ),
				sanitize_title( $payment_method ),
				sanitize_text_field( $payment_note ),
				"pending",
				$payment_date,
				$release_date
			) ) );

			if ( $wpdb->insert_id ) {
				return rest_ensure_response( array(
					'status'  => true,
					'message' => esc_html__( 'Payout added successfully', 'tourfic' )
				) );
			} else {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Something went wrong', 'tourfic' )
				) );
			}
		}

		/*
		 * Update payout
		 * @auther Foysal
		 */
		public function tf_update_payout( $request ) {
			$id             = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$vendor_id      = ! empty( $request->get_param( 'vendor_id' ) ) ? $request->get_param( 'vendor_id' ) : '';
			$payment_amount = ! empty( $request->get_param( 'payment_amount' ) ) ? $request->get_param( 'payment_amount' ) : '';
			$payment_date   = ! empty( $request->get_param( 'payment_date' ) ) ? $request->get_param( 'payment_date' ) : '';
			$release_date   = ! empty( $request->get_param( 'release_date' ) ) ? $request->get_param( 'release_date' ) : '';
			$payment_method = ! empty( $request->get_param( 'payment_method' ) ) ? $request->get_param( 'payment_method' ) : '';
			$payment_note   = ! empty( $request->get_param( 'payment_note' ) ) ? $request->get_param( 'payment_note' ) : '';

			global $wpdb;
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}tf_vendor_withdraw SET
							vendor_id = %d,
							amount = %s,
							payment_method = %s,
							note = %s,
							udate = %s,
							rdate = %s
							WHERE id = %d", array(
				floatval( $vendor_id ),
				floatval( $payment_amount ),
				sanitize_title( $payment_method ),
				sanitize_text_field( $payment_note ),
				$payment_date,
				$release_date,
				$id
			) ) );

			return rest_ensure_response( array(
				'status'  => true,
				'message' => esc_html__( 'Payout updated successfully', 'tourfic' ),
			) );
		}

		/*
		 * Update payout status
		 * @auther Foysal
		 */
		public function tf_update_payout_status( $request ) {
			$id             = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$payment_status = ! empty( $request->get_param( 'payment_status' ) ) ? $request->get_param( 'payment_status' ) : '';

			global $wpdb;
			$tf_vendor_widthdraw_list    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_withdraw WHERE id = %s", $id ) );
			if ( ! empty( $tf_vendor_widthdraw_list ) ) {
				if ( $payment_status === 'completed' ) {

					$tf_vendor_checked = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_balance WHERE vendor_id = %s", $tf_vendor_widthdraw_list->vendor_id ) );
					if ( ! empty( $tf_vendor_checked ) ) {
						$tf_vendor_amount          = floatval( $tf_vendor_checked->total_amount ) - floatval( $tf_vendor_widthdraw_list->amount );
						$tf_vendor_withdraw_amount = floatval( $tf_vendor_checked->total_withdraw ) + floatval( $tf_vendor_widthdraw_list->amount );
						$tf_pending_amount         = floatval( $tf_vendor_checked->pending_amount ) - floatval( $tf_vendor_widthdraw_list->amount );
						$tf_withdrawable_amount    = floatval( $tf_vendor_checked->withdrawable_amount ) - floatval( $tf_vendor_widthdraw_list->amount );
						// Amount table update
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}tf_vendor_balance SET total_amount=%s, pending_amount=%s, withdrawable_amount=%s, total_withdraw=%s WHERE vendor_id=%s", $tf_vendor_amount, $tf_pending_amount, $tf_withdrawable_amount, $tf_vendor_withdraw_amount, $tf_vendor_widthdraw_list->vendor_id ) );

						// Withdraw table update
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}tf_vendor_withdraw SET wstatus=%s, rdate=%s WHERE id=%s", sanitize_title( $payment_status ), date( 'Y-m-d' ), $tf_vendor_widthdraw_list->id ) );
					}
				} else {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}tf_vendor_withdraw SET
							wstatus = %s
							WHERE id = %d", array(
						sanitize_title( $payment_status ),
						$id
					) ) );
				}
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => esc_html__( 'Payout status updated successfully', 'tourfic' )
			) );
		}
	}
}

TF_Vendor_Rest_API::get_instance();