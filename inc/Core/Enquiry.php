<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

abstract class Enquiry {

	private int $last_id = 0;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
		add_action( 'wp_footer', array($this, 'tourfic_ask_question') );
		add_action( 'wp_ajax_tf_ask_question', array($this, 'tourfic_ask_question_ajax') );
		add_action( 'wp_ajax_nopriv_tf_ask_question', array($this, 'tourfic_ask_question_ajax') );

		add_action( 'wp_ajax_tf_enquiry_bulk_action', array($this, 'tf_enquiry_bulk_action_callback') );
		add_action( 'wp_ajax_tf_enquiry_filter_post', array($this, 'tf_enquiry_filter_post_callback') );
		add_action( 'wp_ajax_tf_enquiry_reply_email', array($this, 'tf_enquiry_reply_email_callback') );
		add_action( 'wp_ajax_tf_enquiry_filter_mail', array($this, 'tf_enquiry_filter_mail_callback') );

		if( is_plugin_active( 'tourfic-email-piping/tourfic-email-piping.php' ) ) {
			add_filter( 'cron_schedules', array($this, 'tf_enquiry_response_schedule') );
			add_action( 'init', array($this, 'tf_enquiry_response_schedule_event') );
			add_action( 'tf_enquiry_response_schedule', array($this, 'tf_enquiry_response_schedule_callback') );
		} else {
			self::tf_enquiry_update_response_unschedule();
		}
}

	abstract public function add_submenu();

	public function enquiry_header_filter_options( array $args) {
		?>
			<div class="tf-booking-header-filter">
				<input class="enquiry-post-type" type="hidden" value="<?php echo esc_attr( $args["post_type"]) ?>">
				<div class="tf-left-search-filter">
					<div class="tf-bulk-action-form">
						<div class="tf-filter-options">
							<div class="tf-order-status-filter">
								<select class="tf-tour-filter-options tf-filter-bulk-option tf-filter-bulk-option-enquiry">
									<option value=""><?php esc_html_e( "Bulk action", "tourfic" ); ?></option>
									<option value="trash"><?php esc_html_e( "Trash", "tourfic" ); ?></option>
									<option value="mark-as-read"><?php esc_html_e( "Mark as Read", "tourfic" ); ?></option>
								</select>
							</div>
						</div>
						<button class="tf-order-status-filter-btn">
							<?php esc_html_e( "Apply", "tourfic" ); ?>
						</button>
						<div class="tf-filter-options">
							<div class="tf-order-status-filter-reset-btn-wrapper">
								<div class="tf-order-status-filter-reset-btn">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										<mask id="mask0_265_944" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
											<rect width="24" height="24" fill="#D9D9D9"/>
										</mask>
										<g mask="url(#mask0_265_944)">
											<path d="M11 20.95C8.98333 20.7 7.3125 19.8208 5.9875 18.3125C4.6625 16.8042 4 15.0333 4 13C4 11.9 4.21667 10.8458 4.65 9.8375C5.08333 8.82917 5.7 7.95 6.5 7.2L7.925 8.625C7.29167 9.19167 6.8125 9.85 6.4875 10.6C6.1625 11.35 6 12.15 6 13C6 14.4667 6.46667 15.7625 7.4 16.8875C8.33333 18.0125 9.53333 18.7 11 18.95V20.95ZM13 20.95V18.95C14.45 18.6833 15.6458 17.9917 16.5875 16.875C17.5292 15.7583 18 14.4667 18 13C18 11.3333 17.4167 9.91667 16.25 8.75C15.0833 7.58333 13.6667 7 12 7H11.925L13.025 8.1L11.625 9.5L8.125 6L11.625 2.5L13.025 3.9L11.925 5H12C14.2333 5 16.125 5.775 17.675 7.325C19.225 8.875 20 10.7667 20 13C20 15.0167 19.3375 16.7792 18.0125 18.2875C16.6875 19.7958 15.0167 20.6833 13 20.95Z" fill="#1D2327"/>
										</g>
									</svg>
									<h3 class="tf-enquiry-reset-button-text"><?php esc_html_e("Reset", 'tourfic'); ?></h3>
								</div>
							</div>
						</div>
						<div class="tf-filter-options">
							<div class="tf-order-status-filter">
								<select class="tf-tour-filter-options tf-filter-hotel-name tf-enquiry-filter-<?php esc_html_e( !empty($args['name']) ? strtolower($args['name']) : ''); ?>-name">
									<?php $hotel_enquiry_title = esc_html__( sprintf( "%s Enquiry Details", $args['name'] ), 'tourfic' ); ?>
									<option value=""><?php esc_html($hotel_enquiry_title); ?></option>
									<?php
									$tf_posts_list       = array(
										'posts_per_page' => - 1,
										'post_type'      => $args['post_type'],
										'post_status'    => 'publish'
									);
									$tf_posts_list_query = new \WP_Query( $tf_posts_list );
									if ( $tf_posts_list_query->have_posts() ):
										while ( $tf_posts_list_query->have_posts() ) : $tf_posts_list_query->the_post();
											?>
											<option value="<?php echo esc_attr(get_the_ID()); ?>" <?php echo ! empty( $_GET['post'] ) && get_the_ID() == $_GET['post'] ? esc_attr( 'selected' ) : ''; ?>><?php echo esc_html(get_the_title()); ?></option>
										<?php
										endwhile;
									endif;
									wp_reset_query();
									?>

								</select>
							</div>
						</div>

						<div class="tf-filter-options">
							<div class="tf-order-status-filter">
							<select class="tf-tour-filter-options tf-filter-mail-option-enquiry">
									<option value=""><?php esc_html_e( "Filters", "tourfic" ); ?></option>
									<option value="unread"><?php esc_html_e( "Unread", "tourfic" ); ?></option>
									<?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
										<option value="replied"><?php esc_html_e( "Replied", "tourfic" ); ?></option>
										<option value="not-replied"><?php esc_html_e( "Not Replied", "tourfic" ); ?></option>
										
										<?php if( is_plugin_active( 'tourfic-email-piping/tourfic-email-piping.php' ) ) : ?>
											<option value="responded"><?php esc_html_e( "Responded", "tourfic" ); ?></option>
											<option value="not-responded"><?php esc_html_e( "Not Responded", "tourfic" ); ?></option>
										<?php endif; ?>

									<?php endif; ?>
								</select>
							</div>
						</div>

					</div>
				</div>
				<form class="tf-enquiry-right-search-filter">
					<input type="number" value="" placeholder="<?php esc_html_e("Search by " . $args["name"] . " ID") ?>" id="tf-searching-enquiry-key">
					<button class="tf-search-by-id" type="submit">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
							<path d="M17.5 17.5L14.5834 14.5833M16.6667 9.58333C16.6667 13.4954 13.4954 16.6667 9.58333 16.6667C5.67132 16.6667 2.5 13.4954 2.5 9.58333C2.5 5.67132 5.67132 2.5 9.58333 2.5C13.4954 2.5 16.6667 5.67132 16.6667 9.58333Z" stroke="#87888B" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
						</svg>
					</button>
				</form>
			</div>
		<?php 
	}

	public function enquiry_details_list(array $data, $total_pages = 1) {

		$post_type = !empty($data) ? $data[0]["post_type"] : '';

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

			if ( isset( $_GET['paged'] ) ) {
				$paged = $_GET['paged'];
			} else {
				$paged = 1;
			}
		}
		?>
		<div class="<?php echo apply_filters( $post_type . '_booking_oder_table_class', "tf-order-table-responsive") ?> tf-enquiry-table">
            <table class="wp-list-table table" cellpadding="0" cellspacing="0">
                <thead>
					<tr>
						<td id="cb">
							<div class="tf-checkbox-listing">
								<input id="cb-select-all-1" type="checkbox">
								<?php esc_html_e( "Name", "tourfic" ); ?>
							</div>
						</td>
						<th id="uemail">
							<?php esc_html_e( "Email", "tourfic" ); ?>
						</th>
						<th id="post_name">
							<?php !empty( $data ) && $data[0]["post_type"] == "tf_tours" ? esc_html_e("Tour Name", "tourfic") : ( !empty( $data ) && $data[0]["post_type"] == 'tf_hotel' ? esc_html_e('Hotel Name', 'tourfic') : esc_html_e('Apartment Name', 'tourfic') ); ?>
						</th>
						<th id="description">
							<?php esc_html_e( "Message", "tourfic" ); ?>
						</th>
						<th id="massage-date-time">
							<?php esc_html_e( "Date and Time", "tourfic" ); ?>
						</th>
						<th id="enquiry-action">
							<?php esc_html_e( "Action", "tourfic" ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$tf_key = 1;
				if( !empty( $data )) :
					foreach ( $data as $enquiry ) { ?>
						<?php 
							$tr_unread_class = $enquiry["status"] == 'unread' ? 'tf-enquiry-unread' : ( $enquiry["status"] == 'responded' && function_exists( 'is_tf_pro' ) && is_tf_pro() ? 'tf-enquiry-responded' : '' );
							$submit_time = self::convert_to_wp_timezone($enquiry["submit_time"]);
						
						?>
						<tr class="<?php echo esc_attr($tr_unread_class); ?> tf-enquiry-single-row">
							<input type="hidden" class="tf-enquiry-id" value="<?php echo esc_html($enquiry["id"]); ?>">
							<th class="check-column">
								<div class="table-name-column">
									<div class="tf-checkbox-listing">
										<input id="tf-enquiry-name-checkbox" class="tf-enquiry-name-checkbox" type="checkbox" name="order_id[]" value="<?php echo esc_html( $enquiry['id'] ); ?>">
									</div>
									<?php echo $enquiry["uname"] ? esc_html($enquiry["uname"]) : ''; ?>
								</div>
							</th>
							<td>
								<?php echo $enquiry["uemail"] ? esc_html($enquiry["uemail"]): ''; ?>
							</td>
							<td>
								<?php echo $enquiry["post_title"] ? esc_html($enquiry["post_title"]) : ''; ?>
							</td>
							<td>
								<?php echo $enquiry["description"] ? (esc_html( strlen( $enquiry["description"] ) > 100 ? esc_html( Helper::tourfic_character_limit_callback( $enquiry["description"], 100 ) ) : esc_html( $enquiry["description"] ) )) : ''; ?>
							</td>
							<td class="time-n-date">
								<?php 
								$date_format = !empty(Helper::tfopt("tf-date-format-for-users")) ? Helper::tfopt("tf-date-format-for-users") : get_option('date_format');
								list($date, $time) = explode(" ", $submit_time);
								$formateed_time = date( get_option('time_format'), strtotime($time));
								$formateed_date = date( $date_format, strtotime($date));
								?>
								<div class="email-time-date">
									<span class="email-date"><?php echo $formateed_date ? esc_html( $formateed_date ) : ''; ?></span>
									<span class="email-time"><?php echo $formateed_time ? esc_html( $formateed_time ) : ''; ?></span>
								</div>
							</td>
							
							<td>
								<?php
								$actions_details = '<a href="' . admin_url() . 'edit.php?post_type=' . $enquiry["post_type"] . '&amp;page=' . $enquiry["post_type"] . '_enquiry' . '&amp;enquiry_id=' . $enquiry["id"] . '&amp;action=preview" class="tf_booking_details_view"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M2.42012 12.7132C2.28394 12.4975 2.21584 12.3897 2.17772 12.2234C2.14909 12.0985 2.14909 11.9015 2.17772 11.7766C2.21584 11.6103 2.28394 11.5025 2.42012 11.2868C3.54553 9.50484 6.8954 5 12.0004 5C17.1054 5 20.4553 9.50484 21.5807 11.2868C21.7169 11.5025 21.785 11.6103 21.8231 11.7766C21.8517 11.9015 21.8517 12.0985 21.8231 12.2234C21.785 12.3897 21.7169 12.4975 21.5807 12.7132C20.4553 14.4952 17.1054 19 12.0004 19C6.8954 19 3.54553 14.4952 2.42012 12.7132Z" stroke="#1D2327" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M12.0004 15C13.6573 15 15.0004 13.6569 15.0004 12C15.0004 10.3431 13.6573 9 12.0004 9C10.3435 9 9.0004 10.3431 9.0004 12C9.0004 13.6569 10.3435 15 12.0004 15Z" stroke="#1D2327" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg></a>';
								echo wp_kses($actions_details, Helper::tf_custom_wp_kses_allow_tags());
								?>
							</td>
						</tr>
						<?php if ( ! defined( 'TF_PRO' ) && $tf_key == 15 ) { ?>
							<tr class="pro-row" style="text-align: center; background-color: #ededf8">
								<td colspan="8" style="text-align: center;">
									<a href="https://tourfic.com/" target="_blank">
										<h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;"><?php esc_html_e( 'Upgrade to Pro Version to See More', 'tourfic' ); ?></h3>
									</a>
								</td>
							</tr>
							<tr class="pro-row pro-notice-row" style="text-align: center; background-color: #ededf8">
								<td colspan="8" style="text-align: center;">
									<div class="tf-field tf-field-notice tf-pro-notice " style="width:100%;">
										<div class="tf-fieldset">
											<div class="tf-field-notice-inner tf-notice-info">
												<div class="tf-field-notice-icon">
													<i class="ri-information-fill"></i>
												</div>
												<div class="tf-field-notice-content has-content">
												<?php echo wp_kses_post(sprintf(__("We're offering some extra filter features like %1s replied %2s, %1s not replied %2s, %1s not responded %2s, and %1s not responded %2s in our pro plan.", 'tourfic'), '<b>', '</b>', '<b>', '</b>', '<b>', '</b>', '<b>', '</b>')) ?> <a href="https://themefic.com/tourfic/pricing" target="_blank"> <?php echo esc_html__("Upgrade to our pro package today to take advantage of these fantastic options!", 'tourfic') ?></a>                
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						<?php break;}
						$tf_key ++;
					} ?>
				<?php else: ?>
					<tr class="no-result-found" style="text-align: center">
						<td colspan="8" style="text-align: center;">
							<h3 style="margin: 15px 0;"><?php esc_html_e( 'No Enquiry Found', 'tourfic' ); ?></h3>
						</td>
					</tr>
				<?php endif; ?>
				
                </tbody>
				<tfoot>
					<tr>
						<th colspan="8">
							<ul class="tf-booking-details-pagination">
								<?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
									<?php if ( ! empty( $paged ) && $paged >= 2 ) { ?>
									<li><a href="<?php echo esc_url($this->enquiry_details_pagination( $paged - 1 )); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<path d="M15.8333 10.0001H4.16663M4.16663 10.0001L9.99996 15.8334M4.16663 10.0001L9.99996 4.16675" stroke="#1D2327" stroke-width="1.67" stroke-linecap="round"
													stroke-linejoin="round"/>
											</svg><?php esc_html_e( "Previous", "tourfic" ); ?></a></li>
									<?php }
									if ( ! empty( $total_pages ) && $total_pages > 1 ) {
										for ( $i = 1; $i <= $total_pages; $i ++ ) {
											if ( $i == $paged ) {
												?>
												<li class="active">
													<a href="<?php echo esc_url($this->enquiry_details_pagination( $i )); ?>"><?php echo esc_html($i); ?></a>
												</li>
											<?php } else { ?>
												<li>
													<a href="<?php echo esc_url($this->enquiry_details_pagination( $i )); ?>"><?php echo esc_html($i); ?></a>
												</li>
											<?php }
										}
									}
									if ( ! empty( $total_pages ) && ! empty( $paged ) && $paged < $total_pages ) {
										?>
										<li><a href="<?php echo esc_url($this->enquiry_details_pagination( $paged + 1 )); ?>"><?php esc_html_e( "Next", "tourfic" ); ?>
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16669 10.0001H15.8334M15.8334 10.0001L10 4.16675M15.8334 10.0001L10 15.8334" stroke="#1D2327" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</a></li>
									<?php } ?>
								<?php endif; ?>
							</ul>
						</th>
					</tr>
                </tfoot>
			</table>
		</div>
		<?php 
	}

	function enquiry_details_pagination($page){
        $currentURL = home_url($_SERVER['REQUEST_URI']);
        $BaseURL = strtok($currentURL, '?');
        $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        
        parse_str($queryString, $currentURLParams);

        if (array_key_exists('paged', $currentURLParams)) {
            $currentURLParams['paged'] = $page;
            $updatedQuery = http_build_query($currentURLParams);
            return $updatedUrl = $BaseURL . '?' . $updatedQuery;
        } else {
            return $updatedUrl = $currentURL . '&paged=' . $page;
        }
    }

	public function single_enquiry_details($data) {

		global $wpdb, $current_user;

		$server_data = !empty( $data["server_data"] ) ? json_decode($data["server_data"], true) : array();

		$date_time_format = self::convert_to_wp_timezone( $data["created_at"] );

		list($date, $time) = explode(" ", $date_time_format);
		$formateed_date = date( "M d, Y", strtotime($date));
		$formateed_time = date( "h:i:s A", strtotime($time));
		$reply_data = !empty( $data["reply_data"] ) ? json_decode($data["reply_data"], true) : array();
		$reply_user = isset( $_POST['user_name'] ) ? sanitize_text_field( $_POST['user_name'] ) : '';
		$current_user = wp_get_current_user();
		$_SESSION["WP"]["userId"] = $current_user->ID;
		$email_body_setting = !empty( Helper::tfopt("tf-email-piping")["email_body_type"] ) ? Helper::tfopt("tf-email-piping")["email_body_type"] : 'text';

		?>
		<div class="wrap tf_booking_details_wrap tf-enquiry-details-wrap" style="margin-right: 20px;">
		<div id="tf-enquiry-status-loader">
			<img src="<?php echo esc_url(TF_ASSETS_URL); ?>app/images/loader.gif" alt="Loader">
		</div>
			<!-- Header Wrap - Start -->
			<hr class="wp-header-end">
			<div class="tf_booking_wrap_header">
				<div class="tf-enquiry-single-header-details">
					<div class="tf-single-enquiry-header-logo">
						<img src="<?php echo esc_url( esc_url(TF_ASSETS_APP_URL.'images/tourfic-logo-icon-blue.png') ); ?>" alt="<?php esc_html_e( get_the_title($data["post_id"])) ?>">
					</div>
					<h1 class="wp-heading-inline"> <?php esc_html_e( get_the_title($data["post_id"])) . esc_html_e(" / ID #") . esc_html_e($data["id"]) ?></h1>
				</div>
			</div>
			<!-- Header Wrap - End -->
			 <!-- Back Button - Start -->
			<div class="tf-single-enquiry-header-buttons">
				<div class="tf-enquiry-single-back-button">
					<a href="<?php echo esc_url(admin_url('edit.php?post_type=' . $data["post_type"] . '&page=' . $data["post_type"] . '_enquiry')); ?>" class="tf-enquiry-back-btn"><i class="ri-arrow-left-line"></i><?php esc_html_e('Back', 'tourfic'); ?></a>
				</div>
				<?php if( is_plugin_active( 'tourfic-email-piping/tourfic-email-piping.php' ) ) : ?>
					<div class="tf-enquiry-single-back-button tf-enquiry-single-sync">
						<div class="tf-enquiry-single-sync-button" data-button-name="tf-enquiry-single-sysnc-button"><?php echo esc_html__("Sync Mail", 'tourfic'); ?></div>
					</div>
				<?php endif; ?>
			</div>
			<!-- Back Button - End -->
			<!-- Enquiry Details - Start -->
			<div class="tf-enquiry-single-details-wrapper"> <!-- Enquiry Details Main Wrapper - Start -->
				<div class="tf-single-enquiry-left"> <!-- Enquiry Details Left - Start -->
					<div class="tf-enquiry-details"> <!-- Enquiry mail Details Wrapper - Start -->
						<div class="tf-enquiry-details-single-heading">
							<h2><?php esc_html_e('Details', 'tourfic'); ?></h2>
						</div>
						<hr class="enqury-single-title-line">
						<div class="tf-single-enquiry-details-content">
							<div class="tf-single-enquiry-details-name">
								<span class="tf-single-enquiry-details-label"> <?php esc_html_e("Name", 'tourfic') ?></span>
								<span class="tf-single-enquiry-details-value" data-enquiry-copy-text="<?php echo esc_html($data["uname"]); ?>"> <?php echo esc_html($data["uname"]); ?><i class="ri-file-copy-line tf-single-enquiry-copy-btn"></i></span>
							</div>
							<div class="tf-single-enquiry-details-email">
								<span class="tf-single-enquiry-details-label"> <?php esc_html_e("Email", 'tourfic') ?></span>
								<span class="tf-single-enquiry-details-value" data-enquiry-copy-text="<?php echo esc_html($data["uemail"]); ?>"> <?php echo esc_html($data["uemail"]); ?><i class="ri-file-copy-line tf-single-enquiry-copy-btn"></i></span>
							</div>
							<div class="tf-single-enquiry-details-message">
								<span class="tf-single-enquiry-details-label"> <?php esc_html_e("Message", 'tourfic') ?></span>
								<span class="tf-single-enquiry-details-value"> <?php echo esc_html($data["udescription"]); ?></span>
							</div>
						</div>
					</div> <!-- Enquiry mail Details Wrapper - End -->
					<?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
						<?php if( count($reply_data) == 0 ): ?>
							<div class="tf-single-enquiry-reply-mail-button">
								<span> <?php esc_html_e( "Reply to Email", 'tourfic') ?> </span>
								<i class="ri-mail-line"></i>
							</div>
						<?php endif; ?>
						<?php $display = count($reply_data) > 0 ? '' : 'display:none;' ?>
						<div class="tf-enquiry-details tf-single-enquiry-reply-wrapper" style="<?php echo esc_attr($display); ?>"> <!-- Enquiry mail Reply Wrapper - Start -->
							<div class="tf-enquiry-details-single-heading">
								<h2><?php esc_html_e('To:', 'tourfic') ; ?> <span class="tf-single-enquiry-reply-mail"> <?php esc_html_e( $data["uemail"]); ?> </span></h2>
							</div>
							<hr class="enqury-single-title-line">
							<div class="tf-single-enquiry-accordion">
								<?php if( !empty($reply_data) && is_array($reply_data)): ?>
									<?php foreach($reply_data as $key => $reply): ?>
										<div class="tf-single-enquiry-accordion-item">
											<input type="hidden" class="tf-single-enquiry-accordion-id" value="<?php echo esc_attr($key) ?>">
											<div class="tf-single-enquiry-accordion-head">
												<div class="tf-single-enquiry-accordion-head-left">
													<?php if( !empty( $reply["type"]) && "response" == $reply["type"]) : ?>
														<i class="ri-reply-all-line tf-enquiry-response-user"></i>
														<?php echo !empty( $reply["reply_user"]) ? $reply["reply_user"] : esc_html__("User", "tourfic"); ?>
													<?php else: ?>
														<i class="ri-reply-all-line"></i>
														<?php esc_html_e("You", 'tourfic') ?>
													<?php endif; ?>
													<span class="tf-single-accordion-dash"><?php esc_html_e('―', 'tourfic') ?></span>
													<span class="tf-single-accordion-subject">
														<?php if( $email_body_setting == 'html'): ?>
															<?php echo !empty($reply["reply_message_html"]) ? ( wp_kses_post( strlen( strip_tags( $reply["reply_message_html"] ) ) > 75 ? esc_html__( Helper::tourfic_character_limit_callback( strip_tags( $reply["reply_message_html"] ), 75 ) , 'tourfic' ) : esc_html__( strip_tags( $reply["reply_message_html"] ) ), 'tourfic' )) : esc_html__( Helper::tourfic_character_limit_callback( strip_tags( $reply["reply_message"] ), 75 ), "tourfic"); ?>
														<?php elseif( $email_body_setting == 'text'): ?>
															<?php echo !empty($reply["reply_message_text"]) ? ( wp_kses_post( strlen( $reply["reply_message_text"] ) > 75 ? esc_html__( Helper::tourfic_character_limit_callback( $reply["reply_message_text"], 75 ) , 'tourfic' ) : esc_html__( $reply["reply_message_text"] ) , 'tourfic' )) :  esc_html__( Helper::tourfic_character_limit_callback( strip_tags( $reply["reply_message"] ), 75 ), "tourfic"); ?>
														<?php endif; ?>
													</span>
												</div>
												<div class="tf-single-enquiry-accordion-head-right">
													<?php // esc_html_e( date( "M d, Y h:i:s A", strtotime($reply["submit_time"])) ); ?>
													<?php  esc_html_e( self::time_elapsed_string( $reply["submit_time"]) ); ?>
												</div>
											</div>
											<div id="tf-single-enquiry-accordion-<?php echo esc_attr($key) ?>" class="tf-single-enquiry-collapse">
												<div class="tf-single-accordion-body">
													<?php
														if( $email_body_setting == 'html' ) {
															echo !empty($reply["reply_message_html"]) ? wp_kses_post($reply["reply_message_html"]) : $reply["reply_message"];
														} else if( $email_body_setting == 'text' ) {
															echo !empty($reply["reply_message_text"]) ? wp_kses_post(wpautop($reply["reply_message_text"])) : $reply["reply_message"];
														}
													?>
												</div>
											</div>
											<hr>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
							<div class="tf-single-enquiry-details-content">
								<?php if( count($reply_data) > 0 ): ?>
									<div class="tf-single-enquiry-reply-another-mail-button">
										<span> <?php esc_html_e( "Send Another Mail", 'tourfic') ?> </span>
										<i class="ri-mail-line"></i>
									</div>
								<?php endif; ?>
								<form id="tf-single-enquiry-reply-form" method="post" style="<?php echo count($reply_data) > 0 ? 'display:none;' : '' ?>" action>
									<?php 
										$editor_id = 'tf-enquiry-reply-editor';
										$settings = array(
											'textarea_name' => 'my_custom_textarea_name',
											'textarea_rows' => 20,
											'media_buttons' => false,
											'wpautop' => true,
											'teeny' => true,  
										);

										wp_editor('', $editor_id, $settings);
									?>

									<input type="hidden" class="tf-enquiry-reply-email" value="<?php echo esc_html($data["uemail"]); ?>">
									<input type="hidden" class="tf-enquiry-reply-name" value="<?php echo esc_html($data["uname"]); ?>">
									<input type="hidden" class="tf-enquiry-reply-id" value="<?php echo esc_html($data["id"]); ?>">
									<input type="hidden" class="tf-enquiry-reply-post-id" value="<?php echo esc_html($data["post_id"]); ?>">
									<input type="hidden" name="tf-enquiry-reply-post-userID" value="<?php echo $_SESSION['WP']['userId']; ?>" />

									<button class="tf-enquiry-reply-button" type="submit"> 
										<?php esc_html_e('Send', 'tourfic') ?>
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<g clip-path="url(#clip0_2455_191)">
												<path d="M18.3333 1.66667L12.5 18.3333L9.16662 10.8333L1.66663 7.5L18.3333 1.66667Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
												<path d="M18.3333 1.66667L9.16663 10.8333" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
											</g>
											<defs>
												<clipPath id="clip0_2455_191">
													<rect width="20" height="20" fill="white"></rect>
												</clipPath>
											</defs>
										</svg>
									</button>
								</form>
							</div>
						</div> <!-- Enquiry mail Reply Wrapper - End -->
					<?php else: ?>
						<div class="tf-field tf-field-notice tf-pro-notice " style="width:100%;">
							<div class="tf-fieldset">
				            	<div class="tf-field-notice-inner tf-notice-info">
									<div class="tf-field-notice-icon">
										<i class="ri-information-fill"></i>
									</div>
                					<div class="tf-field-notice-content has-content">
									<?php echo wp_kses_post(sprintf(__("We're offering some exiting features like %1s sending reply from enquiry details page %2s and %1s get replies using email piping %2s in our pro plan.", 'tourfic'), '<b>', '</b>', '<b>', '</b>')) ?> <a href="https://themefic.com/tourfic/pricing" target="_blank"> <?php echo esc_html__("Upgrade to our pro package today to take advantage of these fantastic options!", 'tourfic') ?></a>                
									</div>
            					</div>
			            	</div>
			        	</div>
					<?php endif; ?>
				</div> <!-- Enquiry Details Left - End -->
				<div class="tf-single-enquiry-right"> <!-- Enquiry Details Right - Start -->
					<div class="tf-enquiry-single-log-details">
						<div class="tf-singe-enquiry-log-details-heading">
							<h2> <?php esc_html_e("Log Details #", 'tourfic') . esc_html_e($data["id"]); ?> </h2>
							<div class="enquiry-details-status">
								<svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
									<circle cx="3" cy="3" r="3" fill="#27BE69"/>
								</svg>

								<div class="enquiry-status-value"> <?php !empty( $data["enquiry_status"] ) ? esc_html_e(ucfirst( $data["enquiry_status"] ), 'tourfic') : '' ?> </div>
							</div>
						</div>
						<hr class="enqury-single-title-line">
						<div class="tf-single-enquiry-log-details-content">
							<?php if( !empty( $server_data )): ?>
								<div class="tf-single-enquiry-log-details-single"> <!-- Single Log Details IP - Start -->
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
										<g clip-path="url(#clip0_660_8611)">
											<path d="M15 6.66669C15 10.4167 10 14.1667 10 14.1667C10 14.1667 5 10.4167 5 6.66669C5 5.3406 5.52678 4.06883 6.46447 3.13115C7.40215 2.19347 8.67392 1.66669 10 1.66669C11.3261 1.66669 12.5979 2.19347 13.5355 3.13115C14.4732 4.06883 15 5.3406 15 6.66669Z" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M10 8.33333C10.9205 8.33333 11.6667 7.58714 11.6667 6.66667C11.6667 5.74619 10.9205 5 10 5C9.07957 5 8.33337 5.74619 8.33337 6.66667C8.33337 7.58714 9.07957 8.33333 10 8.33333Z" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M7.36246 11.6667H4.16663C3.99707 11.6759 3.83438 11.7367 3.70035 11.841C3.56631 11.9452 3.46732 12.088 3.41663 12.25L1.74996 17.25C1.66663 17.3334 1.66663 17.4167 1.66663 17.5C1.66663 18 1.99996 18.3334 2.49996 18.3334H17.5C18 18.3334 18.3333 18 18.3333 17.5C18.3333 17.4167 18.3333 17.3334 18.25 17.25L16.5833 12.25C16.5326 12.088 16.4336 11.9452 16.2996 11.841C16.1655 11.7367 16.0028 11.6759 15.8333 11.6667H12.6375" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</g>
										<defs>
											<clipPath id="clip0_660_8611">
												<rect width="20" height="20" fill="white"/>
											</clipPath>
										</defs>
									</svg>
									<span class="tf-single-enquiry-log-details-single-value" data-enquiry-copy-text="<?php echo esc_attr($server_data["ip_address"]); ?>"> <?php echo esc_html($server_data["ip_address"]); ?> </span>
									<span class="tf-single-enquiry-log-details-single-copy"> <i class="ri-file-copy-line tf-single-enquiry-copy-btn"></i></span>
								</div>
							
								<div class="tf-single-enquiry-log-details-single"> <!-- Single Log Details Browser - Start -->
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
										<g clip-path="url(#clip0_660_8615)">
											<path d="M9.99996 18.3334C14.6023 18.3334 18.3333 14.6024 18.3333 10C18.3333 5.39765 14.6023 1.66669 9.99996 1.66669C5.39759 1.66669 1.66663 5.39765 1.66663 10C1.66663 14.6024 5.39759 18.3334 9.99996 18.3334Z" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M9.99996 1.66669C7.86015 3.91348 6.66663 6.8973 6.66663 10C6.66663 13.1027 7.86015 16.0866 9.99996 18.3334C12.1398 16.0866 13.3333 13.1027 13.3333 10C13.3333 6.8973 12.1398 3.91348 9.99996 1.66669Z" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M1.66663 10H18.3333" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</g>
										<defs>
											<clipPath id="clip0_660_8615">
											<rect width="20" height="20" fill="white"/>
											</clipPath>
										</defs>
									</svg>
									<span class="tf-single-enquiry-log-details-single-value"> <?php echo esc_html__($server_data["browser_name"], 'tourfic'); ?> </span>
								</div>
								<div class="tf-single-enquiry-log-details-single"> <!-- Single Log Details Device - Start -->
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
										<path d="M15 1.66669H5.00004C4.07957 1.66669 3.33337 2.41288 3.33337 3.33335V16.6667C3.33337 17.5872 4.07957 18.3334 5.00004 18.3334H15C15.9205 18.3334 16.6667 17.5872 16.6667 16.6667V3.33335C16.6667 2.41288 15.9205 1.66669 15 1.66669Z" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M10 15H10.0083" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<span class="tf-single-enquiry-log-details-single-value"> <?php echo esc_html($server_data["os_name"]); ?> </span>
								</div>
							<?php endif; ?>	
							<div class="tf-single-enquiry-log-details-single"> <!-- Single Log Details Date - Start -->
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<path d="M15.8333 3.33331H4.16667C3.24619 3.33331 2.5 4.07951 2.5 4.99998V16.6666C2.5 17.5871 3.24619 18.3333 4.16667 18.3333H15.8333C16.7538 18.3333 17.5 17.5871 17.5 16.6666V4.99998C17.5 4.07951 16.7538 3.33331 15.8333 3.33331Z" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M13.3334 1.66669V5.00002" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M6.66663 1.66669V5.00002" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M2.5 8.33331H17.5" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M6.66663 11.6667H6.67663" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M10 11.6667H10.01" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M13.3334 11.6667H13.3434" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M6.66663 15H6.67663" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M10 15H10.01" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M13.3334 15H13.3434" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<span class="tf-single-enquiry-log-details-single-value"> <?php echo esc_html($formateed_date); ?> </span>
							</div>
							<div class="tf-single-enquiry-log-details-single"> <!-- Single Log Details Time - Start -->
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<g clip-path="url(#clip0_660_8624)">
										<path d="M9.99996 18.3334C14.6023 18.3334 18.3333 14.6024 18.3333 10C18.3333 5.39765 14.6023 1.66669 9.99996 1.66669C5.39759 1.66669 1.66663 5.39765 1.66663 10C1.66663 14.6024 5.39759 18.3334 9.99996 18.3334Z" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M10 5V10L13.3333 11.6667" stroke="#5D5676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
									<defs>
										<clipPath id="clip0_660_8624">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
								</svg>
								<span class="tf-single-enquiry-log-details-single-value"> <?php echo esc_html( $formateed_time ); ?> </span>
							</div>
						</div>
					</div>
				</div>
			 </div> <!-- Enquiry Details Main Wrapper - End -->
		</div>

		<?php
	}

	static function time_elapsed_string($datetime) {

		$timezone = wp_timezone();
		
		$now = new \DateTime('now', $timezone);
		
		$ago = new \DateTime($datetime, $timezone);
		
		$diff = $now->diff($ago);
	
		if ($diff->h == 0 && $diff->d == 0) {
			$minutes = $diff->i;
			if ($minutes == 0) {
				return esc_html__('Just now', 'tourfic');
			} elseif ($minutes == 1) {
				return '1 minute ago';
			} else {
				return sprintf(esc_html__('%s minutes ago', 'tourfic'), $minutes);
			}
		}
	
		if ($diff->d == 0) {
			return 'Today ' . $ago->format('h:i A');
		}
	
		if ($diff->d == 1) {
			return 'Yesterday ' . $ago->format('h:i A');
		}

		if ($diff->d > 1) {
			return $ago->format('M d, Y h:i A');
		}
	}

	static function convert_to_wp_timezone($date_string, $format = 'Y-m-d H:i:s') {
		$timezone = wp_timezone();
		
		try {
			$date = new \DateTime($date_string, new \DateTimeZone('UTC'));
	
			$date->setTimezone($timezone);
			
			return $date->format($format);
		} catch (\Exception $e) {
			return 'Invalid date';
		}
	}
	
	
	
	public function enquiry_table_data( $post_type = '', $post_id = '', $status = '', $offset = 0, $per_page = 0 ) {

		 global $wpdb;
		 $query = "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE ";
		 $enquiry_data = array();
 
		 if( !empty($post_type) ) {
			$query .= sprintf(' post_type = "%s"', $post_type);
		 }

		if(!empty($post_type) && ( !empty($post_id) || !empty($status)) ) {
			$query .= ' AND';
		}
		 
		if( !empty($post_id) ) {
			$query.= sprintf(' post_id = %d', $post_id );
		 }

		 if( !empty($post_type) && !empty($post_id) && !empty($status) ) {
			$query .= ' AND';
		 }
		 
		 if( !empty($status) ) {

			if( $status == 'not-replied') {
				$query.= sprintf(' enquiry_status != "%s"', 'replied' );
			} elseif( $status == 'not-responded') {
				$query.= sprintf(' enquiry_status != "%s"', 'responded' );
			} else {
				$query.= sprintf(' enquiry_status = "%s"', $status );
			}
		 }

		 $query .= " ORDER BY id DESC";

		 if( !empty( $offset ) && !empty( $per_page ) ) {
			$query .= sprintf(' LIMIT %d, %d', $offset, $per_page);
		 }

		 $results = $wpdb->get_results( $wpdb->prepare( $query ), ARRAY_A );

		 if( !empty($results) ) {
			foreach( $results as $result ) {
				$enquiry_data[] = array(
					'id' => $result['id'],
					'post_title' => get_the_title($result['post_id']),
					'post_type' => $result['post_type'],
					'uname' => $result['uname'],
					'uemail' => $result['uemail'],
					'description' => $result['udescription'],
					'status' => $result['enquiry_status'],
					'submit_time' => $result['created_at']
				);
			}
		 }

		return $enquiry_data;

	}

	function tourfic_ask_question() {
		?>
		<div id="tf-ask-question" style="display: none;">
			<div class="tf-aq-overlay"></div>
			<div class="tf-aq-outer">
			<span class="close-aq">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
					<path d="M15 5L5 15" stroke="#4E667E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M5 5L15 15" stroke="#4E667E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
				<div class="tf-aq-inner">
					<div class="tf-ask-question-head-content">
						<div class="ask-question-svg">
							<svg xmlns="http://www.w3.org/2000/svg" width="82" height="65" viewBox="0 0 82 65" fill="none">
								<path d="M39.8433 2.87109C51.7608 2.87109 58.7471 8.37858 58.7471 17.8305C58.7471 29.748 46.4165 32.1321 46.4165 40.763H32.6912C32.6912 28.5165 41.7326 27.2009 41.7326 20.8724C41.7326 18.4883 40.5827 17.42 38.1171 17.42C35.0752 17.42 33.678 19.4751 33.678 23.9143H19.5396C19.5396 10.681 27.0206 2.87109 39.8433 2.87109ZM47.7348 53.5041C47.7348 58.5169 44.2824 61.9693 39.2697 61.9693C34.3384 61.9693 30.8861 58.5169 30.8861 53.5041C30.8861 48.5729 34.3384 45.1206 39.2697 45.1206C44.2824 45.1206 47.7348 48.5729 47.7348 53.5041Z"
								      fill="#CFE6FC"/>
								<path d="M41.688 1C53.6056 1 60.5919 6.50748 60.5919 15.9594C60.5919 27.8769 48.2612 30.261 48.2612 38.8919H34.5359C34.5359 26.6455 43.5774 25.3298 43.5774 19.0013C43.5774 16.6173 42.4274 15.5489 39.9618 15.5489C36.92 15.5489 35.5227 17.604 35.5227 22.0432H21.3843C21.3869 8.80731 28.8653 1 41.688 1ZM49.5795 51.6331C49.5795 56.6458 46.1272 60.0982 41.1144 60.0982C36.1832 60.0982 32.7308 56.6458 32.7308 51.6331C32.7308 46.7018 36.1832 43.2495 41.1144 43.2495C46.1272 43.2468 49.5795 46.6992 49.5795 51.6331Z"
								      stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
								<path d="M24.1691 51.6832C19.7378 56.1144 15.0908 56.6644 11.5753 53.1515C7.14403 48.7202 10.8411 43.2496 7.63346 40.0393L12.7383 34.9344C17.2933 39.4893 14.4198 43.3391 16.7723 45.6941C17.659 46.5809 18.4827 46.5493 19.401 45.6336C20.5325 44.5021 20.2878 43.218 18.6379 41.5681L23.8954 36.3106C28.8161 41.2313 28.9371 46.9151 24.1691 51.6832ZM2.40754 35.7896C0.541888 33.924 0.541888 31.3584 2.40754 29.4927C4.24161 27.6587 6.80984 27.6587 8.67286 29.5243C10.5069 31.3584 10.5069 33.9266 8.67286 35.7607C6.80984 37.6237 4.24161 37.6237 2.40754 35.7896Z"
								      fill="#CFE6FC"/>
								<path d="M24.1795 53.064C19.7483 57.4953 15.1012 58.0452 11.5857 54.5323C7.15447 50.1011 10.8516 44.6304 7.64391 41.4202L12.7488 36.3153C17.3037 40.8702 14.4302 44.7199 16.7827 47.075C17.6695 47.9618 18.4931 47.9302 19.4114 47.0145C20.5429 45.883 20.2982 44.5989 18.6483 42.949L23.9058 37.6915C28.8239 42.6122 28.9476 48.296 24.1795 53.064ZM2.41535 37.1705C0.549701 35.3048 0.549701 32.7392 2.41535 30.8736C4.24942 29.0395 6.81765 29.0395 8.68067 30.9052C10.5147 32.7392 10.5147 35.3075 8.68067 37.1415C6.81765 39.0046 4.24942 39.0046 2.41535 37.1705Z"
								      stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
								<path d="M75.1344 30.6488C79.9683 34.638 80.9577 39.2113 77.7921 43.0452C73.8029 47.8791 68.0034 44.7188 65.1141 48.2185L59.5461 43.6241C63.6458 38.6561 67.7534 41.1506 69.8717 38.585C70.669 37.6193 70.5611 36.8009 69.5612 35.9747C68.327 34.9563 67.0719 35.3221 65.5878 37.122L59.8566 32.3855C64.2879 27.0201 69.9348 26.357 75.1344 30.6488ZM61.3828 53.8234C59.704 55.8574 57.1489 56.1021 55.1149 54.4233C53.115 52.7734 52.8703 50.2157 54.5491 48.1843C56.199 46.1845 58.7567 45.9397 60.7566 47.5922C62.7906 49.2684 63.0353 51.8235 61.3828 53.8234Z"
								      fill="#CFE6FC"/>
								<path d="M76.5077 30.506C81.3416 34.4951 82.331 39.0685 79.1654 42.9024C75.1762 47.7363 69.3767 44.576 66.4874 48.0757L60.9194 43.4813C65.0191 38.5133 69.1267 41.0078 71.245 38.4422C72.0423 37.4765 71.9344 36.6581 70.9345 35.8319C69.7003 34.8135 68.4452 35.1793 66.9611 36.9792L61.2273 32.2453C65.6612 26.8747 71.3081 26.2116 76.5077 30.506ZM62.7561 53.6806C61.0773 55.7146 58.5222 55.9593 56.4882 54.2805C54.4883 52.6306 54.2436 50.0729 55.9224 48.0415C57.5723 46.0416 60.13 45.7969 62.1299 47.4494C64.1639 49.1256 64.4086 51.6807 62.7561 53.6806Z"
								      stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
								<path d="M14.8461 23.9229C13.7856 22.073 12.7252 20.2231 11.6647 18.3733C11.4121 17.9338 10.7306 18.3285 10.9832 18.7706C12.0437 20.6205 13.1041 22.4703 14.1645 24.3202C14.4172 24.7623 15.0987 24.3649 14.8461 23.9229Z"
								      fill="#1B334B"/>
								<path d="M17.0346 22.8627C16.9057 21.2681 16.7794 19.6708 16.6505 18.0762C16.611 17.5736 15.8216 17.5683 15.8611 18.0762C15.99 19.6708 16.1163 21.2681 16.2452 22.8627C16.2873 23.3679 17.0768 23.3705 17.0346 22.8627Z"
								      fill="#1B334B"/>
								<path d="M12.3674 25.4572C11.2912 24.7546 10.2176 24.0494 9.14137 23.3468C8.71508 23.0679 8.31775 23.7521 8.74403 24.0283C9.82027 24.7309 10.8939 25.4361 11.9701 26.1387C12.3938 26.4176 12.7911 25.7335 12.3674 25.4572Z"
								      fill="#1B334B"/>
								<path d="M63.754 20.4706C64.3934 19.1075 65.0302 17.7419 65.6696 16.3788C65.8828 15.9209 65.2039 15.521 64.9881 15.9815C64.3487 17.3445 63.7119 18.7102 63.0724 20.0733C62.8567 20.5311 63.5382 20.9311 63.754 20.4706Z"
								      fill="#1B334B"/>
								<path d="M65.024 22.4106C66.7818 21.2449 68.5369 20.0819 70.2947 18.9162C70.7157 18.6372 70.321 17.9531 69.8973 18.2346C68.1396 19.4003 66.3844 20.5634 64.6267 21.7291C64.203 22.008 64.5977 22.6922 65.024 22.4106Z"
								      fill="#1B334B"/>
								<path d="M66.0904 24.4261C67.5955 24.2129 69.1007 23.9998 70.6084 23.7866C71.1084 23.7156 70.8953 22.9551 70.3979 23.0262C68.8928 23.2393 67.3876 23.4524 65.8798 23.6656C65.3799 23.734 65.593 24.4945 66.0904 24.4261Z"
								      fill="#1B334B"/>
								<path d="M50.6624 59.706C51.9308 61.4164 53.2017 63.1268 54.47 64.8346C54.77 65.2372 55.4542 64.8451 55.1516 64.4372C53.8832 62.7268 52.6123 61.0164 51.344 59.3087C51.044 58.9034 50.3572 59.2981 50.6624 59.706Z"
								      fill="#1B334B"/>
								<path d="M53.1403 58.175C54.2166 58.8776 55.2902 59.5828 56.3664 60.2854C56.7927 60.5643 57.19 59.8802 56.7637 59.6039C55.6875 58.9013 54.6139 58.1961 53.5377 57.4935C53.1114 57.2146 52.7167 57.8961 53.1403 58.175Z"
								      fill="#1B334B"/>
								<path d="M26.5213 58.8527C25.3767 60.6236 24.232 62.3919 23.0874 64.1628C22.8111 64.5917 23.4926 64.9864 23.7689 64.5601C24.9135 62.7892 26.0582 61.0209 27.2029 59.25C27.4791 58.8237 26.7976 58.4264 26.5213 58.8527Z"
								      fill="#1B334B"/>
								<path d="M24.8085 57.3504C23.5139 58.1477 22.2192 58.945 20.922 59.7397C20.4904 60.0054 20.8851 60.6896 21.3193 60.4212C22.6139 59.6239 23.9086 58.8266 25.2058 58.0319C25.64 57.7661 25.2427 57.082 24.8085 57.3504Z"
								      fill="#1B334B"/>
							</svg>
						</div>
						<h3><?php esc_html_e( 'Your Question', 'tourfic' ); ?></h3>
					</div>

					<form id="ask-question" action="" method="post">
						<div class="tf-aq-field">
							<label for="your-name"><?php esc_html_e( 'Name', 'tourfic' ); ?></label>
							<input type="text" name="your-name" placeholder="<?php esc_attr_e( 'Type full name', 'tourfic' ); ?>" required/>
						</div>
						<div class="tf-aq-field">
							<label for="your-email"><?php esc_html_e( 'Email', 'tourfic' ); ?></label>
							<input type="email" name="your-email" placeholder="<?php esc_attr_e( 'Type email', 'tourfic' ); ?>" required/>
						</div>
						<div class="tf-aq-field">
							<label for="your-question"><?php esc_html_e( 'Question', 'tourfic' ); ?></label>
							<textarea placeholder="<?php esc_attr_e( 'Type here...', 'tourfic' ); ?>" name="your-question" required></textarea>
						</div>
						<div class="tf-aq-field">
							<button type="reset" class="screen-reader-text"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
							<button type="submit" form="ask-question" class="button tf_button tf-ask-question-submit"><?php esc_html_e( 'Submit', 'tourfic' ); ?></button>
							<input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
							<?php wp_nonce_field( 'ask_question_nonce' ); ?>
							<div class="response"></div>
						</div>
					</form>

				</div>
			</div>
		</div>
		<?php
	}

	function tourfic_ask_question_ajax() {

		$response = array();

		if ( ! check_ajax_referer( 'ask_question_nonce' ) ) {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'Security error! Reload the page and try again.', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		}

		$name     = isset( $_POST['your-name'] ) ? sanitize_text_field( $_POST['your-name'] ) : null;
		$email    = isset( $_POST['your-email'] ) ? sanitize_email( $_POST['your-email'] ) : null;
		$question = isset( $_POST['your-question'] ) ? sanitize_text_field( $_POST['your-question'] ) : null;
		$from = "From: " . get_option( 'blogname' ) . " <" . get_option( 'admin_email' ) . ">\r\n";

		$post_id    = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : null;
		$post_title = get_the_title( $post_id );

		// Server Details
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$browser_name = $this->tf_get_browser_name( $user_agent );
		$os_name = php_uname( 's' );
		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';

		$server_details = array(
			'browser_name' => $browser_name,
			'os_name'      => $os_name,
			'ip_address'   => $ip_address,
			'time'		=> date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) )
		);



		//post author mail
		$author_id   = get_post_field( 'post_author', $post_id );
		$author_mail = get_the_author_meta( 'user_email', $author_id );

		// Post Author Data
		$author_data = get_userdata( $author_id );

		// Enquiry Store on Database
		$tf_post_author_id = get_post_field( 'post_author', $post_id );
		$tf_user_meta      = get_userdata( $tf_post_author_id );
		$tf_user_roles     = $tf_user_meta->roles;

		/**
		 * Enquiry Pabbly Integration
		 * @author Jahid
		 */
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			do_action( 'enquiry_pabbly_form_trigger', $post_id, $name, $email, $question );
			do_action( 'enquiry_zapier_form_trigger', $post_id, $name, $email, $question );
		}

		if ( "tf_hotel" == get_post_type( $post_id ) ) {
			$send_email_to[] = ! empty( Helper::tfopt( 'h-enquiry-email' ) ) ? sanitize_email( Helper::tfopt( 'h-enquiry-email' ) ) : sanitize_email( get_option( 'admin_email' ) );
		} elseif ( "tf_apartment" == get_post_type( $post_id ) ) {
			$send_email_to[] = ! empty( $author_mail ) ? sanitize_email( $author_mail ) : sanitize_email( get_option( 'admin_email' ) );
		} else {
			$send_email_to[] = ! empty( Helper::tfopt( 't-enquiry-email' ) ) ? sanitize_email( Helper::tfopt( 't-enquiry-email' ) ) : sanitize_email( get_option( 'admin_email' ) );
		}

		$tf_vendor_email_enable_setting = ! empty( Helper::tfopt( 'email_template_settings' )['enable_vendor_enquiry_email'] ) ? Helper::tfopt( 'email_template_settings' )['enable_vendor_enquiry_email'] : 0;


		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $tf_vendor_email_enable_setting == 1 ) ) {
			if ( in_array( "tf_vendor", $tf_user_roles ) ) {
				if ( "tf_hotel" == get_post_type( $post_id ) ) {
					$send_email_to[] = ! empty( $author_mail ) ? $author_mail : '';
				} elseif ( "tf_tours" == get_post_type( $post_id ) ) {
					$send_email_to[] = ! empty( $author_mail ) ? $author_mail : '';
				}
			}
		}

		$subject     = esc_html__( 'Someone asked question on: ', 'tourfic' ) . esc_html( $post_title );
		$message     = "{$question}";
		$headers[]   = 'Content-Type: text/html; charset=UTF-8';
		$headers[]   = $from;

		$user_permission = Helper::tf_data_types( Helper::tfopt( 'tf_user_permission' ) );
		$vendor_access = isset( $user_permission['vendor_can_manage'] ) ? $user_permission['vendor_can_manage'] : array();
		$manager_access = isset( $user_permission['manager_can_manage'] ) ? $user_permission['manager_can_manage'] : array();


		if ( ( !empty( $vendor_access ) && ! in_array( 'enquiry_email', $vendor_access )) || ( !empty( $manager_access ) && ! in_array( 'enquiry_email', $manager_access ) ) ) {
			$headers[] = 'Reply-To: ' . get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>';
		} else {
			$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
		}

		$attachments = array();


		if ( wp_mail( $send_email_to, $subject, $message, $headers, $attachments ) ) {
			
			$response['status'] = 'sent';
			$response['msg']    = esc_html__( 'Your question has been sent!', 'tourfic' );

			// Data Store to the DB
			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}tf_enquiry_data
				( post_id, post_type, uname, uemail, udescription, author_id, author_roles, enquiry_status, server_data, created_at )
				VALUES ( %d, %s, %s, %s, %s, %d, %s, %s, %s, %s )",
					array(
						sanitize_key( $post_id ),
						get_post_type( $post_id ),
						$name,
						$email,
						$question,
						sanitize_key( $tf_post_author_id ),
						$tf_user_roles[0],
						'unread',
						json_encode( $server_details ),
						current_time('mysql')
					)
				)
			);
			$this->last_id = $wpdb->insert_id;
		} else {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'Message sent failed!', 'tourfic' );
		}

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tf_vendor_email_enable_setting != 1 ) {
			if ( in_array( "tf_vendor", $tf_user_roles ) ) {
				if( self::tf_vendor_default_enquiry_mail( $author_mail, $post_id, $name, $question, $this->last_id ) ) {
				} else {
					$response['status'] = 'error';
					$response['msg']    = esc_html__( 'Message sent failed!', 'tourfic' );
				}
			}
		}

		echo wp_json_encode( $response );

		die();
	}

	function tf_enquiry_bulk_action_callback() {
		$response = array();

		if ( ! check_ajax_referer('updates', '_ajax_nonce') ) {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'Security error! Reload the page and try again.', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		}

		$enquiry_ids = isset( $_POST['selected_items'] ) ? $_POST['selected_items'] : array();
		$bulk_action = isset( $_POST['bulk_action'] ) ? $_POST['bulk_action'] : '';

		if ( empty( $enquiry_ids ) ) {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'No enquiry selected!', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		}

		if ( empty( $bulk_action ) ) {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'Select an action first!', 'tourfic' );
			echo wp_json_encode( $response );
			wp_die();
		}

		global $wpdb;

		if ( 'trash' == $bulk_action ) {
			foreach ( $enquiry_ids as $enquiry_id ) {
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM {$wpdb->prefix}tf_enquiry_data WHERE id=%d",
						$enquiry_id
					)
				);
			}
		} else if( 'mark-as-read' == $bulk_action ) {
			foreach ( $enquiry_ids as $enquiry_id ) {
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}tf_enquiry_data SET enquiry_status=%s WHERE id=%d",
						'read',
						$enquiry_id
					)
				);
			}
		}
	}

	function tf_enquiry_filter_post_callback() {

		$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
		$filter = isset( $_POST['filter'] ) ? sanitize_text_field ( $_POST['filter'] ) : '';

		$enquiry_data = $this->enquiry_table_data( $post_type, $post_id, $filter );

		$this->enquiry_details_list( $enquiry_data );

		wp_reset_postdata();

		wp_die();
	}

	function tf_enquiry_reply_email_callback() {

		$response = array();
		global $wpdb;
		global $current_user;
		$reply_data = $wpdb->get_results( "SELECT reply_data FROM {$wpdb->prefix}tf_enquiry_data WHERE id= " . $_POST['enquiry_id'] );
		$reply_data = json_decode( $reply_data[0]->reply_data, true );

		check_ajax_referer('updates', '_ajax_nonce');

		$reply_mail = isset( $_POST['reply_mail'] ) ? sanitize_text_field( $_POST['reply_mail'] ) : '';
		$reply_message = isset( $_POST['reply_message'] ) ? wp_kses_post( $_POST['reply_message'] ) : '';
		$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
		$post_type = !empty( $post_id ) ? get_post_type( $post_id ) : '';
		$enquiry_id = isset( $_POST['enquiry_id'] ) ? sanitize_text_field( $_POST['enquiry_id'] ) : '';

		$connection_type = !empty( Helper::tfopt("tf-email-piping")["connection_type"]) ? Helper::tfopt("tf-email-piping")["connection_type"] : 'imap';
		$connection_mail = $enquiry_mail_setting = '';
		if( $connection_type == 'imap' ) {
			$connection_mail = !empty( Helper::tfopt("tf-email-piping")["imap-email-address"]) ? Helper::tfopt("tf-email-piping")["imap-email-address"] : '';
		} elseif( $connection_type == 'gmail' ) {
			$connection_mail = !empty( Helper::tfopt("tf-email-piping")["gmail-address"]) ? Helper::tfopt("tf-email-piping")["gmail-address"] : '';
		}

		if( $post_type == 'tf_hotel') {
			$enquiry_mail_setting = !empty( Helper::tfopt("h-enquiry-email") ) ? Helper::tfopt("h-enquiry-email") : '';
		} elseif( $post_type == 'tf_tours') {
			$enquiry_mail_setting = !empty( Helper::tfopt("t-enquiry-email") ) ? Helper::tfopt("t-enquiry-email") : '';
		} elseif( $post_type == 'tf_apartment') {
			$enquiry_mail_setting = !empty( Helper::tfopt("a-enquiry-email") ) ? Helper::tfopt("a-enquiry-email") : '';
		}

		$reply_to_email = !empty( $connection_mail ) ? $connection_mail : ( !empty( $enquiry_mail_setting ) ? $enquiry_mail_setting : get_option( 'admin_email' ) );
		$replay_data_last_index = !empty( $reply_data ) ? count( $reply_data ) - 1 : 0;

		// author data
		$author_id   = get_post_field( 'post_author', $post_id );
		$author_mail = get_the_author_meta( 'user_email', $author_id );

		// Post data
		$tf_post_author_id = get_post_field( 'post_author', $post_id );
		$tf_user_meta      = get_userdata( $tf_post_author_id );
		$tf_user_roles     = $tf_user_meta->roles;

		if( empty( $reply_message ) ) {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'Reply Message is Required!', 'tourfic' );

		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'You do not have permission to perform this action.', 'tourfic' );
			$response['msg']    = esc_html__( 'You do not have permission to perform this action.', 'tourfic' );
		}

		if( !empty( $reply_mail ) && !empty( $reply_message ) ) {
			$to = $reply_mail;
			$from = "From: " . get_option( 'blogname' ) . " <" . get_option( 'admin_email' ) . ">\r\n";
			$subject = esc_html__("Re: Response to Your Enquiry About ", 'tourfic') . esc_html( get_the_title( $post_id ) ) . " - " . esc_html("#".$enquiry_id);
			$header_uid = rand( 10000000, 99999999 );
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$headers[] = $from;
			$headers[] = 'Reply-To: ' . $reply_to_email;
			$headers[] = 'x-tourfic-uid: ' . $header_uid;

			if( !empty( $reply_data[$replay_data_last_index]["message_id"] ) ) {
				$headers[] = 'In-Reply-To: ' . $reply_data[$replay_data_last_index]["message_id"];
			}

			if( !empty( $reply_data[$replay_data_last_index]["references"] ) ) {
				$headers[] = 'References: ' . $reply_data[$replay_data_last_index]["references"];
			}

			$reply_footer = '';

			if( is_plugin_active( 'tourfic-email-piping/tourfic-email-piping.php' ) ) {
				$reply_footer = "<br><p>" . esc_html__("Please reply to this email to update your enquiry.",'tourfic') . "</p>";
			}

			$send_mail = wp_mail( $to, $subject, $reply_message . $reply_footer, $headers );
			
			$submit_time = date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) );

			$reply_data[] = array(
				'reply_user' => !empty( $current_user->display_name ) ? $current_user->display_name : '',
				'reply_mail' => $reply_mail,
				'reply_message' => $reply_message,
				'reply_subject' => $subject,
				'reply_uid' => $header_uid,
				'submit_time' => $submit_time
			);
			
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}tf_enquiry_data SET enquiry_status = %s, reply_data = %s WHERE id = %d",
					'replied',
					json_encode( $reply_data ),
					$enquiry_id
				)
			);

			if ( $send_mail ) {
				$response['status'] = 'success';
				$response['msg']    = esc_html__( 'Email sent successfully!', 'tourfic' );
			} else {
				$response['status'] = 'error';
				$response['msg']    = esc_html__( 'Error sending email. Check your server configurations', 'tourfic' );
			}

		}


		echo wp_json_encode( $response );
		wp_die();
	}

	function tf_enquiry_filter_mail_callback() {
		$filter = isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] )  : '';
		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
		$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';

		$enquiry_data = $this->enquiry_table_data( $post_type, $post_id, $filter );
		$total_data = !empty(count( $enquiry_data )) ? count( $enquiry_data ) : 0;
		$per_page = 20;

		$this->enquiry_details_list( $enquiry_data );

		wp_reset_postdata();

		wp_die();

		wp_die();
	}

	static function tf_vendor_default_enquiry_mail( $vendor_mail, $post_id, $name, $body, $last_id = '', $type = "new" ) {

		$author_name = get_the_author_meta('display_name', get_post_field('post_author', $post_id));
		$post_name = get_the_title( $post_id );
		$post_type = get_post_type( $post_id ) == "tf_tours" ? esc_html__("Tour", 'tourfic') : ( get_post_type( $post_id ) == "tf_hotel" ? esc_html__( "Hotel", 'tourfic') : esc_html__( "Apartment", 'tourfic') );
		$subject = "";

		if( $type == "new" ) {
			$subject .= sprintf( esc_html__( "New Enquiry #%1s: Someone Asked Question about your %2s - %3s  ", 'tourfic' ), $last_id, $post_type, $post_name );
		} elseif( $type == "reply" ) {
			$subject .= sprintf( esc_html__( "New Response #%1s: A New Response is Added to Enquiry Details", 'tourfic' ), $last_id );
		}

		$from = "From: " . get_option( 'blogname' ) . " <" . get_option( 'admin_email' ) . ">\r\n";
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = $from;
		$headers[] = 'Reply-To: no-reply@' . parse_url( site_url() )["host"];
		$dashboard_link = get_option("tf_dashboard_page_id") ? get_permalink(get_option("tf_dashboard_page_id")) : site_url('my-account/');
		$email_content = '';


		ob_start();
		?>
			<div style="width:100%">
				<p> <?php echo esc_html__("Hello", 'tourfic') ?> <b><?php echo esc_html( $author_name ) ?></b></p>
				<?php if( $type = 'new'): ?>
					<p> <?php echo esc_html__("A new enquiry has been added to your dashboard", 'tourfic') ?> <?php echo !empty($name) ? esc_html__('by ', 'tourfic') . $name : '' ?></p>
				<?php elseif( $type == "reply"): ?>
					<p> <?php echo esc_html__("A new response has been added to", 'tourfic'); ?> #<?php echo esc_html($last_id) ?> <?php echo !empty($name) ? esc_html__('by ', 'tourfic') . $name : '' ?></p>
				<?php endif; ?>
				<?php if( !empty( $body )): ?>
					<h4>Enquiry Body</h4>
					<p><?php echo wp_kses_post( $body ) ?></p>
				<?php endif; ?>
				<p><?php esc_html__("Click here to go to the enquiry page:", 'tourfic') ?> <b><a href="<?php echo esc_url( $dashboard_link . "#/" . strtolower($post_type) . '-' . 'enquiries' . '/' . $last_id)  ?>"><?php echo esc_html__("View Enquiry", 'tourfic'); ?></a></b></p>
			</div>
			<div style="width:100%">
				<p><?php esc_html__("Reply the enquiry using dashboard reply form", 'tourfic') ?></p>
			</div>
		<?php
		$email_content = ob_get_clean();

		return wp_mail( $vendor_mail, $subject, $email_content, $headers );
	}
	
	function tf_get_browser_name( $user_agent ) {
		if ( preg_match( '/MSIE/i', $user_agent ) && ! preg_match( '/Opera/i', $user_agent ) ) {
			return 'Internet Explorer';
		} elseif ( preg_match( '/Firefox/i', $user_agent ) ) {
			return 'Firefox';
		} elseif ( preg_match( '/Chrome/i', $user_agent ) ) {
			return 'Chrome';
		} elseif ( preg_match( '/Safari/i', $user_agent ) ) {
			return 'Safari';
		} elseif ( preg_match( '/Opera/i', $user_agent ) ) {
			return 'Opera';
		} elseif ( preg_match( '/Netscape/i', $user_agent ) ) {
			return 'Netscape';
		}
		return 'Unknown';
	}

	function tf_enquiry_response_schedule( $schedules ) {
		$interval = 60;

		$schedules['tf_enquiry_response_schedule'] = array(
            'interval' => $interval,
            'display' => esc_attr__( 'Tourfic Response Scheduler', 'tourfic' )
        );

		return $schedules;

	}

	function tf_enquiry_response_schedule_event() {
		if ( ! wp_next_scheduled( 'tf_enquiry_response_schedule' ) ) {
            wp_schedule_event( time(), 'tf_enquiry_response_schedule', 'tf_enquiry_response_schedule' );
        }
	}

	function tf_enquiry_response_schedule_callback() {

		if( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		} else {
			self::tf_enquiry_update_response_unschedule();
		}


		if( empty( get_option("tfep_enquiry_update_response") )) {
			return;
		}

		global $wpdb;

		$response_data = get_option("tfep_enquiry_update_response");

		$enquiry_id = $response_data["enquiry_id"];
		$enquiry_details = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data where id= $enquiry_id", ARRAY_A );
		$enquiry_details = !empty($enquiry_details) ? $enquiry_details[0] : array();

		if( $enquiry_details["author_roles"] != "tf_vendor" ) {
			return;
		}

		if( $enquiry_details["enquiry_status"] == "unread" && $enquiry_details["enquiry_status"] == "read" ) {
			return;
		}

		if( empty( $enquiry_details["reply_data"] ) ) {
			return;
		}

		$reply_data = json_decode( $enquiry_details["reply_data"], true );

		$last_reply = end($reply_data);

		$vendor_mail = get_the_author_meta("user_email", $enquiry_details["author_id"]);
		$post_id = $enquiry_details["post_id"];
		$name = !empty( $last_reply["name"] ) ? $last_reply["name"] : '';
		$body = !empty( $last_reply["reply_message_html"] ) ? $last_reply["reply_message_html"] : '';

		if( $response_data["notified"] == "false") {
			$reply_to_vendor = self::tf_vendor_default_enquiry_mail($vendor_mail, $post_id, $name, $body, $enquiry_id, 'reply');
			if( $reply_to_vendor ) {
				update_option("tfep_enquiry_update_response", '');
			}
		}
	}

	public static function tf_enquiry_update_response_unschedule() {
        $timestamp = wp_next_scheduled( 'tf_enquiry_response_schedule' );
        if( $timestamp ) {
            wp_unschedule_event( $timestamp, 'tf_enquiry_response_schedule' );
        }
    }

}