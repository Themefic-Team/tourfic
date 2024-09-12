<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

abstract class Enquiry {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
		add_action( 'wp_footer', array($this, 'tourfic_ask_question') );
		add_action( 'wp_ajax_tf_ask_question', array($this, 'tourfic_ask_question_ajax') );
		add_action( 'wp_ajax_nopriv_tf_ask_question', array($this, 'tourfic_ask_question_ajax') );
	}

	abstract public function add_submenu();

	function enquiry_table($post_type = 'tf_hotel'){
		global $wpdb;
		$current_user = wp_get_current_user();
		$current_user_role = $current_user->roles[0];

		if ( $current_user_role == 'administrator' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s ORDER BY id DESC", $post_type ), ARRAY_A );
		} elseif ( $current_user_role == 'administrator' ) {
			$enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s ORDER BY id DESC LIMIT 15", $post_type ), ARRAY_A );
		}

		$enquiry_results = new \Tourfic\Admin\TF_List_Table( $enquiry_result );
		$enquiry_results->prepare_items();
		$enquiry_results->display();
	}


	public function enquiry_header_filter_options( array $args) {
		?>
			<div class="tf-booking-header-filter">
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
						<div class="tf-filter-options">
							<div class="tf-order-status-filter">
								<select class="tf-tour-filter-options tf-filter-hotel-name">
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

					</div>
				</div>
				<form class="tf-right-search-filter">
					<input type="number" value="" placeholder="<?php esc_html_e("Search by " . $args["name"] . " ID") ?>" id="tf-searching-key">
					<button class="tf-search-by-id" type="submit">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
							<path d="M17.5 17.5L14.5834 14.5833M16.6667 9.58333C16.6667 13.4954 13.4954 16.6667 9.58333 16.6667C5.67132 16.6667 2.5 13.4954 2.5 9.58333C2.5 5.67132 5.67132 2.5 9.58333 2.5C13.4954 2.5 16.6667 5.67132 16.6667 9.58333Z" stroke="#87888B" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
						</svg>
					</button>
				</form>
			</div>
		<?php 
	}

	public function enquiry_details_list(array $data) {
		?>
		<div class="<?php echo apply_filters( $data["post_type"] . '_booking_oder_table_class', "tf-order-table-responsive") ?>">
            <table class="wp-list-table table" cellpadding="0" cellspacing="0">
                <thead>
					<tr>
						<td id="cb">
							<div class="tf-checkbox-listing">
								<input id="cb-select-all-1" type="checkbox">
							</div>
						</td>
						<th id="order_id">
							<?php esc_html_e( "Name", "tourfic" ); ?>
						</th>
						<th id="uemail">
							<?php esc_html_e( "Email", "tourfic" ); ?>
						</th>
						<th id="cdetails">
							<?php $data[0]["post_type"] == "tf_tours" ? esc_html_e("Tour Name", "tourfic") : ( $data[0]["post_type"] == 'tf_hotel' ? esc_html_e('Hotel Name', 'tourfic') : esc_html_e('Apartment Name', 'tourfic') ); ?>
						</th>
						<th id="check_status">
							<?php esc_html_e( "Message", "tourfic" ); ?>
						</th>
						<th id="ostatus">
							<?php esc_html_e( "Date and Time", "tourfic" ); ?>
						</th>
						<th id="action">
							<?php esc_html_e( "Action", "tourfic" ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$tf_key = 1;
				foreach ( $data as $enquiry ) { ?>
                    <tr>
                        <th class="check-column">
                            <div class="tf-checkbox-listing">
                                <input type="checkbox" name="order_id[]" value="<?php echo esc_html( $enquiry['id'] ); ?>">
                            </div>
                        </th>
                        <td>
							<?php echo esc_html($enquiry["uname"]); ?>
                        </td>
                        <td>
							<?php echo esc_html($enquiry["uemail"]); ?>
                        </td>
                        <td>
							<?php echo esc_html($enquiry["post_title"]); ?>
                        </td>
                        <td>
							<?php echo esc_html( $enquiry['description'] ); ?>
                        </td>
						<td>
							<?php echo esc_html( $enquiry['submit_time'] ); ?>
                        </td>
                        
                        <td>
							<?php
							$actions_details = '<a href="' . admin_url() . 'edit.php?post_type=' . $this->booking_args['post_type'] . '&amp;page=' . $this->booking_args['menu_slug'] . '&amp;order_id=' . $tf_order['order_id'] . '&amp;book_id=' . $tf_order['id'] . '&amp;action=preview" class="tf_booking_details_view"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.82924 16.1427L8.31628 17.238C8.46106 17.5641 8.69734 17.8412 8.99647 18.0356C9.29559 18.23 9.6447 18.3335 10.0015 18.3334C10.3582 18.3335 10.7073 18.23 11.0065 18.0356C11.3056 17.8412 11.5419 17.5641 11.6866 17.238L12.1737 16.1427C12.3471 15.754 12.6387 15.43 13.007 15.2167C13.3777 15.0029 13.8065 14.9119 14.232 14.9566L15.4237 15.0834C15.7784 15.1209 16.1364 15.0547 16.4543 14.8929C16.7721 14.731 17.0362 14.4803 17.2144 14.1714C17.3929 13.8626 17.4779 13.5086 17.4592 13.1525C17.4405 12.7963 17.3188 12.4532 17.1089 12.1649L16.4033 11.1955C16.1521 10.8477 16.0178 10.4291 16.02 10.0001C16.0199 9.57224 16.1554 9.15537 16.407 8.80934L17.1126 7.8399C17.3225 7.55154 17.4442 7.20847 17.4629 6.85231C17.4816 6.49615 17.3966 6.1422 17.2181 5.83341C17.0399 5.52444 16.7758 5.27382 16.458 5.11194C16.1401 4.95005 15.7821 4.88386 15.4274 4.92138L14.2357 5.04823C13.8102 5.09292 13.3814 5.00185 13.0107 4.78804C12.6417 4.57362 12.35 4.24788 12.1774 3.85749L11.6866 2.76212C11.5419 2.43606 11.3056 2.15901 11.0065 1.96458C10.7073 1.77015 10.3582 1.66669 10.0015 1.66675C9.6447 1.66669 9.29559 1.77015 8.99647 1.96458C8.69734 2.15901 8.46106 2.43606 8.31628 2.76212L7.82924 3.85749C7.65668 4.24788 7.36497 4.57362 6.99591 4.78804C6.62526 5.00185 6.19647 5.09292 5.77091 5.04823L4.57554 4.92138C4.22081 4.88386 3.86282 4.95005 3.54497 5.11194C3.22711 5.27382 2.96305 5.52444 2.7848 5.83341C2.60632 6.1422 2.52128 6.49615 2.54002 6.85231C2.55876 7.20847 2.68046 7.55154 2.89035 7.8399L3.59591 8.80934C3.84753 9.15537 3.98302 9.57224 3.98295 10.0001C3.98302 10.4279 3.84753 10.8448 3.59591 11.1908L2.89035 12.1603C2.68046 12.4486 2.55876 12.7917 2.54002 13.1479C2.52128 13.504 2.60632 13.858 2.7848 14.1667C2.96323 14.4756 3.22732 14.726 3.54513 14.8879C3.86294 15.0498 4.22084 15.1161 4.57554 15.0788L5.76721 14.9519C6.19276 14.9072 6.62155 14.9983 6.99221 15.2121C7.36265 15.4259 7.65571 15.7517 7.82924 16.1427Z" stroke="#1D2327" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.99998 12.5001C11.3807 12.5001 12.5 11.3808 12.5 10.0001C12.5 8.61937 11.3807 7.50008 9.99998 7.50008C8.61926 7.50008 7.49998 8.61937 7.49998 10.0001C7.49998 11.3808 8.61926 12.5001 9.99998 12.5001Z" stroke="#1D2327" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg></a>';
							echo wp_kses($actions_details, Helper::tf_custom_wp_kses_allow_tags());
							?>
                        </td>
                    </tr>
					<?php
					if ( ! defined( 'TF_PRO' ) && $tf_key == 15 ) { ?>
                        <tr class="pro-row" style="text-align: center; background-color: #ededf8">
                            <td colspan="8" style="text-align: center;">
                                <a href="https://tourfic.com/" target="_blank">
                                    <h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;"><?php esc_html_e( 'Upgrade to Pro Version to See More', 'tourfic' ); ?></h3>
                                </a>
                            </td>
                        </tr>
					<?php }
					$tf_key ++;
				} ?>
                </tbody>
			</table>
		</div>
		<?php
	}
	
	public function enquiry_table_data( $post_type = '', $post_id = '' ) {

		 global $wpdb;
		 $query = "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE ";
		 $enquiry_data = array();
 
		 if( !empty($post_type) ) {
			$query .= sprintf(' post_type = "%s"', $post_type);
		 } else if( !empty($post_id) ) {
			$query.= sprintf(' post_id = %d', $post_id);
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

		$user_permission = Helper::tf_data_types( tfopt( 'tf_user_permission' ) );
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
			( post_id, post_type, uname, uemail, udescription, author_id, author_roles, created_at )
			VALUES ( %d, %s, %s, %s, %s, %d, %s, %s )",
					array(
						sanitize_key( $post_id ),
						get_post_type( $post_id ),
						$name,
						$email,
						$question,
						sanitize_key( $tf_post_author_id ),
						$tf_user_roles[0],
						current_time('mysql')
					)
				)
			);
		} else {
			$response['status'] = 'error';
			$response['msg']    = esc_html__( 'Message sent failed!', 'tourfic' );
		}

		echo wp_json_encode( $response );

		die();
	}

}