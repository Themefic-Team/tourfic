<?php

namespace Tourfic\Admin\Booking_Details;

defined( 'ABSPATH' ) || exit;

class Tour_Booking_Details extends \Tourfic\Core\TF_Booking_Details {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		$booking_args = array(
			'post_type'    => 'tf_tours',
			'menu_title'   => 'Tour Booking Details',
			'menu_slug'    => 'tf_tours_booking',
			'capability'   => 'edit_tf_tourss',
			'booking_type' => 'tour',
			'booking_title'=> 'Tour',
		);

		parent::__construct( $booking_args );
	}

	public function voucher_details( $tf_tour_details, $tf_order_details, $tf_billing_details ) {
		do_action( 'tourfic_tour_booking_voucher_details', $tf_tour_details, $tf_order_details, $tf_billing_details );
	}

	public function voucher_quick_view( $tf_tour_details, $tf_order_details, $tf_billing_details ) {
		do_action( 'tourfic_tour_booking_voucher_quick_view', $tf_tour_details, $tf_order_details, $tf_billing_details );
	}

	public function check_in_out_status( $tf_order_details ) {
		?>
		<div class="customers-order-date details-box">
			<div class="tf-grid-box">
				<div class="tf-grid-single">
					<h4><?php esc_html_e( 'Others information', 'tourfic' ); ?></h4>
					<div class="tf-single-box tf-checkin-by">
						<table class="table" cellpadding="0" cellspacing="0">
							<tr>
								<th><?php esc_html_e( 'Checked status', 'tourfic' ); ?></th>
								<td>:</td>
								<td>
									<?php
									if ( ! empty( $tf_order_details->checkinout ) ) {
										if ( 'in' === $tf_order_details->checkinout ) {
											esc_html_e( 'Checked in', 'tourfic' );
										} elseif ( 'out' === $tf_order_details->checkinout ) {
											esc_html_e( 'Checked Out', 'tourfic' );
										} elseif ( 'not' === $tf_order_details->checkinout ) {
											esc_html_e( 'Not checked in', 'tourfic' );
										}
									} else {
										esc_html_e( 'Not checked in', 'tourfic' );
									}
									?>
								</td>
							</tr>
							<?php
							$tf_checkinout_by = ! empty( $tf_order_details->checkinout_by ) ? json_decode( $tf_order_details->checkinout_by ) : '';
							?>
							<tr>
								<th><?php esc_html_e( 'Checked in by', 'tourfic' ); ?></th>
								<td>:</td>
								<td>
									<?php
									if ( ! empty( $tf_checkinout_by->userid ) ) {
										$tf_checkin_by = get_user_by( 'id', $tf_checkinout_by->userid );
										echo ! empty( $tf_checkin_by->display_name ) ? esc_html( $tf_checkin_by->display_name ) : '';
									}
									?>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Checked Time', 'tourfic' ); ?></th>
								<td>:</td>
								<td>
									<?php
									if ( ! empty( $tf_checkinout_by->time ) ) {
										echo esc_html( $tf_checkinout_by->time );
									}
									?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
