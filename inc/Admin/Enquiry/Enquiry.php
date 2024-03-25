<?php

namespace Tourfic\Admin\Enquiry;
defined( 'ABSPATH' ) || exit;

class Enquiry {
	use \Tourfic\Traits\Singleton;
	private $data;
	private $enquiry_args;

	public function __construct($data = array(), $booking_args = array())
    {
        $this->enquiry_args = $booking_args;
        $this->data = $data;
		add_action( 'admin_menu', array($this,'tf_add_enquiry_details_submenu'));
	}

	public function tf_add_enquiry_details_submenu(){
		
		$enquiry_args = $this->enquiry_args;
		add_submenu_page( 'edit.php?post_type='.$enquiry_args['post_type'],
			__( $enquiry_args['menu_title'], 'tourfic' ),
			__('Enquiry Details', 'tourfic' ),
			$enquiry_args['capability'],
			$enquiry_args['menu_slug'],
			array($this,'tf_enquiry_page_callback')
		);
	}

	public function tf_enquiry_page_callback(){ ?>
		<div class="wrap tf_booking_details_wrap" style="margin-right: 20px;">
		<div id="tf-booking-status-loader">
			<img src="<?php echo TF_ASSETS_URL; ?>app/images/loader.gif" alt="Loader">
		</div>
		<div class="tf_booking_wrap_header">
			<h1 class="wp-heading-inline"><?php _e( $this->enquiry_args['enquiry_title'].' Enquiry Details', 'tourfic' ); ?></h1>
			<div class="tf_header_wrap_button">
				<?php
				/**
				 * Before enquiry details table hook
				 * @hooked tf_before_tour_booking_details - 10
				 * @since 2.9.18
				 */
				do_action( 'tf_before_enquiry_details' );
				?>
			</div>
		</div>
		<hr class="wp-header-end">
		<?php $this->tf_enquiry_details_list($this->data); ?>
		</div>
		<?php
	}


	public function tf_enquiry_details_list($data){ ?>
	<div class="tf-order-table-responsive">
	<table class="wp-list-table table" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
			<td id="cb" style="width:60px; box-sizing: border-box;">
				<div class="tf-checkbox-listing">
					<input id="cb-select-all-1" type="checkbox">
				</div>
			</td>
			<th id="ename">
				<?php _e( "Name", "tourfic" ); ?>
			</th>
			<th id="eemail">
				<?php _e( "Email", "tourfic" ); ?>
			</th>
			<th id="emessage">
				<?php _e( "Message", "tourfic" ); ?>
			</th>
			<th id="edate">
				<?php _e( "Date & Time", "tourfic" ); ?>
			</th>
			<th id="action">
				<?php _e( "Action", "tourfic" ); ?>
			</th>
		</tr>
		</thead>

		<tbody>
		<?php
		$tf_key = 1;
		foreach ( $data as $senquiry ) { ?>
			<tr>
				<th class="check-column">
					<div class="tf-checkbox-listing">
						<input type="checkbox" name="order_id[]" value="<?php echo esc_html( $senquiry['id'] ); ?>">
					</div>
				</th>
				<td>
					<?php echo esc_html($senquiry['uname']); ?>
				</td>
				<td>
					<?php echo esc_html($senquiry['uemail']); ?>
				</td>
				<td>
					<?php echo esc_html($senquiry['udescription']); ?>
				</td>
				<td>
					<?php echo date( 'F d, Y', strtotime( $senquiry['created_at'] ) ); ?>
				</td>
				<td>
					<?php
					$actions_details = '<a href="action=preview" class="tf_booking_details_view"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<path d="M2.42012 12.7132C2.28394 12.4975 2.21584 12.3897 2.17772 12.2234C2.14909 12.0985 2.14909 11.9015 2.17772 11.7766C2.21584 11.6103 2.28394 11.5025 2.42012 11.2868C3.54553 9.50484 6.8954 5 12.0004 5C17.1054 5 20.4553 9.50484 21.5807 11.2868C21.7169 11.5025 21.785 11.6103 21.8231 11.7766C21.8517 11.9015 21.8517 12.0985 21.8231 12.2234C21.785 12.3897 21.7169 12.4975 21.5807 12.7132C20.4553 14.4952 17.1054 19 12.0004 19C6.8954 19 3.54553 14.4952 2.42012 12.7132Z" stroke="#1D2327" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M12.0004 15C13.6573 15 15.0004 13.6569 15.0004 12C15.0004 10.3431 13.6573 9 12.0004 9C10.3435 9 9.0004 10.3431 9.0004 12C9.0004 13.6569 10.3435 15 12.0004 15Z" stroke="#1D2327" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				  </svg></a>';
					echo $actions_details;
					?>
				</td>
			</tr>
			<?php
			if ( ! defined( 'TF_PRO' ) && $tf_key == 15 ) { ?>
				<tr class="pro-row" style="text-align: center; background-color: #ededf8">
					<td colspan="8" style="text-align: center;">
						<a href="https://tourfic.com/" target="_blank">
							<h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;"><?php _e( 'Upgrade to Pro Version to See More', 'tourfic' ); ?></h3>
						</a>
					</td>
				</tr>
			<?php }
			$tf_key ++;
		} ?>
		</tbody>
		<tfoot>
		<tr>
			<th colspan="8">
				<ul class="tf-booking-details-pagination">
					<?php if ( ! empty( $paged ) && $paged >= 2 ) { ?>
						<li><a href="<?php echo tf_booking_details_pagination( $paged - 1 ); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<path d="M15.8333 10.0001H4.16663M4.16663 10.0001L9.99996 15.8334M4.16663 10.0001L9.99996 4.16675" stroke="#1D2327" stroke-width="1.67" stroke-linecap="round"
											stroke-linejoin="round"/>
								</svg><?php _e( "Previous", "tourfic" ); ?></a></li>
					<?php }
					if ( ! empty( $total_pages ) && $total_pages > 1 ) {
						for ( $i = 1; $i <= $total_pages; $i ++ ) {
							if ( $i == $paged ) {
								?>
								<li class="active">
									<a href="<?php echo tf_booking_details_pagination( $i ); ?>"><?php echo $i; ?></a>
								</li>
							<?php } else { ?>
								<li>
									<a href="<?php echo tf_booking_details_pagination( $i ); ?>"><?php echo $i; ?></a>
								</li>
							<?php }
						}
					}
					if ( ! empty( $total_pages ) && ! empty( $paged ) && $paged < $total_pages ) {
						?>
						<li><a href="<?php echo tf_booking_details_pagination( $paged + 1 ); ?>"><?php _e( "Next", "tourfic" ); ?>
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<path d="M4.16669 10.0001H15.8334M15.8334 10.0001L10 4.16675M15.8334 10.0001L10 15.8334" stroke="#1D2327" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</a></li>
					<?php } ?>
				</ul>
			</th>
		</tr>
		</tfoot>
	</table>
</div>

<div class="tf-preloader-box">
	<div class="tf-loader-preview">
		<img src="<?php echo TF_APP_ASSETS_URL ?>images/loader.gif" alt="Loader">
	</div>
</div>
<?php

	}
}