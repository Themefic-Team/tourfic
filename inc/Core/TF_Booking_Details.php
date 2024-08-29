<?php

namespace Tourfic\Core;
use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

abstract Class TF_Booking_Details {
    use \Tourfic\Traits\Singleton;

    protected array $booking_args;

    public function __construct( $booking_args = array() ) {
        $this->booking_args = $booking_args;

        add_action( 'admin_menu', [ $this, 'tf_add_booking_details_submenu' ] );   

        // Ajax
        add_action( 'wp_ajax_tf_order_status_edit', array( $this, 'tf_order_status_edit_function' ) );
        add_action( 'wp_ajax_tf_visitor_details_edit', array( $this, 'tf_visitor_details_edit_function') );
        add_action( 'wp_ajax_tf_checkinout_details_edit', array( $this, 'tf_checkinout_details_edit_function' ) );
        add_action( 'wp_ajax_tf_order_bulk_action_edit', array( $this, 'tf_order_bulk_action_edit_function' ) );
    }

    public function tf_add_booking_details_submenu() {
        $booking_args = $this->booking_args;

        add_submenu_page(
            'edit.php?post_type=' . $booking_args['post_type'],
            $booking_args['menu_title'],
            __( 'Booking Details', 'tourfic' ),
            $booking_args['capability'],
            $booking_args['menu_slug'],
            array( $this, 'tf_booking_page_callback' )
        );
    }

    public function tf_booking_page_callback() {
        
        $booking_type = ! empty( $this->booking_args["booking_type"] ) ? $this->booking_args["booking_type"] : '';

        if ( ! empty( $_GET['order_id'] ) && ! empty( $_GET['action'] ) && ! empty( $_GET['book_id'] ) ) {

			global $wpdb;
			$tf_order_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE id = %s AND order_id = %s", sanitize_key( $_GET['book_id'] ), sanitize_key( $_GET['order_id'] ) ) );

			$this->tf_single_booking_details( $booking_type, $tf_order_details );

		} else {
			$current_user = wp_get_current_user();

			// get user role
			$current_user_role = $current_user->roles[0];

			// if is not desired user role die
			if ( $current_user_role == 'administrator' || $current_user_role == 'tf_vendor' ) {
			} else {
				wp_die( esc_html__( 'You are not allowed in this page', 'tourfic' ) );
			}

			if ( $current_user_role == 'administrator' ) {

				// Filter Perameters
				$checkinout_perms = ! empty( $_GET['checkinout'] ) ? $_GET['checkinout'] : '';
				$tf_post_perms    = ! empty( $_GET['post'] ) ? $_GET['post'] : '';
				$tf_payment_perms = ! empty( $_GET['payment'] ) ? $_GET['payment'] : '';

				$tf_filter_query = "";
				if ( $checkinout_perms ) {
					$tf_filter_query .= " AND checkinout = '$checkinout_perms'";
				}
				if ( $tf_post_perms ) {
					$tf_filter_query .= " AND post_id = '$tf_post_perms'";
				}
				if ( $tf_payment_perms ) {
					$tf_filter_query .= " AND ostatus = '$tf_payment_perms'";
				}

				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

					if ( isset( $_GET['paged'] ) ) {
						$paged = $_GET['paged'];
					} else {
						$paged = 1;
					}

					$no_of_booking_per_page = 20;
					$offset                 = ( $paged - 1 ) * $no_of_booking_per_page;

					$tf_booking_details_select = array(
						'select'    => "*",
						'post_type' => $booking_type,
						'query'     => " $tf_filter_query ORDER BY id DESC"
					);

					$tf_hotel_booking_result = Helper::tourfic_order_table_data( $tf_booking_details_select );
					$total_rows              = ! empty( count( $tf_hotel_booking_result ) ) ? count( $tf_hotel_booking_result ) : 0;
					$total_pages             = ceil( $total_rows / $no_of_booking_per_page );

					$tf_orders_select = array(
						'select'    => "*",
						'post_type' => $booking_type,
						'query'     => " $tf_filter_query ORDER BY id DESC LIMIT $offset, $no_of_booking_per_page"
					);

					$tf_order_details_result = Helper::tourfic_order_table_data( $tf_orders_select );

				} else {
					$tf_orders_select        = array(
						'select'    => "*",
						'post_type' => $booking_type,
						'query'     => " $tf_filter_query ORDER BY id DESC LIMIT 15"
					);
					$tf_order_details_result = Helper::tourfic_order_table_data( $tf_orders_select );
				}
			?>
            <div class="wrap tf_booking_details_wrap" style="margin-right: 20px;">
                <div id="tf-booking-status-loader">
                    <img src="<?php echo esc_url(TF_ASSETS_URL); ?>app/images/loader.gif" alt="Loader">
                </div>
                <div class="tf_booking_wrap_header">
                    <?php 
                    $heading_title = sprintf(" %s %s", $this->booking_args['booking_title'], "Booking Details" );
                    ?>
                    <h1 class="wp-heading-inline"><?php esc_html_e(apply_filters( $this->booking_args["post_type"] . '_booking_details_main_title', $heading_title), 'tourfic'); ?></h1>
                    <div class="tf_header_wrap_button">
                        <?php
                        /**
                         * Before Hotel booking details table hook
                         * @hooked tf_before_hotel_booking_details - 10
                         * @since 2.9.18
                         */
                        if ( "tf_hotel" == $this->booking_args['post_type'] ) {
                            do_action( 'tf_before_hotel_booking_details' );
                        }

                        /**
                         * Before Tour booking details table hook
                         * @hooked tf_before_tour_booking_details - 10
                         * @since 2.9.18
                         */
                        if ( "tf_tours" == $this->booking_args['post_type'] ) {
                            do_action( 'tf_before_tour_booking_details' );
                        }

                        /**
                         * Before Apartment booking details table hook
                         * @hooked tf_before_apartment_booking_details - 10
                         * @since 2.9.18
                         */
                        if ( "tf_apartment" == $this->booking_args['post_type'] ) {
                            do_action( 'tf_before_apartment_booking_details' );
                        }

                        ?>
                    </div>
                </div>
                <hr class="wp-header-end">

                <?php
                /**
                 * Booking Data showing new template
                 * @since 2.9.26
                 */
                $this->tf_booking_details_list( $booking_type, $tf_order_details_result );
                ?>
            </div>

            <?php }
        }
    }

    function tf_booking_details_list( $booking_type, $tf_order_details_result ) {
		?>
        <div class="tf-booking-header-filter">
            <div class="tf-left-search-filter">
                <div class="tf-bulk-action-form">
                    <div class="tf-filter-options">
                        <div class="tf-order-status-filter">
                            <select class="tf-tour-filter-options tf-filter-bulk-option">
                                <option value=""><?php esc_html_e( "Bulk action", "tourfic" ); ?></option>
                                <option value="trash"><?php esc_html_e( "Trash", "tourfic" ); ?></option>
                                <option value="processing"><?php esc_html_e( "Processing", "tourfic" ); ?></option>
                                <option value="on-hold"><?php esc_html_e( "On Hold", "tourfic" ); ?></option>
                                <option value="completed"><?php esc_html_e( "Complete", "tourfic" ); ?></option>
                                <option value="cancelled"><?php esc_html_e( "Cancelled", "tourfic" ); ?></option>
                            </select>
                        </div>
                    </div>
                    <button class="tf-order-status-filter-btn">
						<?php esc_html_e( "Apply", "tourfic" ); ?>
                    </button>
                </div>

                <div class="tf-filter-options">
                    <div class="tf-order-status-filter">
                        <select class="tf-tour-filter-options tf-order-payment-status">
                            <option value=""><?php esc_html_e( "Order status", "tourfic" ); ?></option>
                            <option value="processing" <?php echo ! empty( $_GET['payment'] ) && "processing" == $_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( "Processing", "tourfic" ); ?></option>
                            <option value="on-hold" <?php echo ! empty( $_GET['payment'] ) && "on-hold" == $_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( "On Hold", "tourfic" ); ?></option>
                            <option value="completed" <?php echo ! empty( $_GET['payment'] ) && "completed" == $_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( "Complete", "tourfic" ); ?></option>
                            <option value="cancelled" <?php echo ! empty( $_GET['payment'] ) && "cancelled" == $_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( "Cancelled", "tourfic" ); ?></option>
                            <option value="refunded" <?php echo ! empty( $_GET['payment'] ) && "refunded" == $_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( "Refund", "tourfic" ); ?></option>
                        </select>
                    </div>
                </div>

				<?php if ( "tf_hotel" == $this->booking_args['post_type'] || "tf_tours" == $this->booking_args['post_type'] ) { ?>
                    <div class="tf-filter-options">
                        <div class="tf-order-status-filter">
                            <select class="tf-tour-checkinout-options">
                                <option value=""><?php esc_html_e( "Checked in status", "tourfic" ); ?></option>
                                <option value="in" <?php echo ! empty( $_GET['checkinout'] ) && "in" == $_GET['checkinout'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( "Checked in", "tourfic" ); ?></option>
                                <option value="out" <?php echo ! empty( $_GET['checkinout'] ) && "out" == $_GET['checkinout'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( "Checked out", "tourfic" ); ?></option>
                            </select>
                        </div>
                    </div>
				<?php } ?>

                <div class="tf-filter-options">
                    <div class="tf-order-status-filter">
						<?php
						if ( "tf_hotel" == $this->booking_args['post_type'] ) {
							$tf_postwise_filter_class = 'tf-hotel-id-filter-options';
						} elseif ( "tf_tours" == $this->booking_args['post_type'] ) {
							$tf_postwise_filter_class = 'tf-post-id-filter-options';
						} elseif ( "tf_apartment" == $this->booking_args['post_type'] ) {
							$tf_postwise_filter_class = 'tf-apartment-id-filter-options';
						} else {
							$tf_postwise_filter_class = '';
						}
						?>
                        <select class="tf-tour-filter-options <?php echo esc_attr( $tf_postwise_filter_class ); ?>">
                            <option value=""><?php echo esc_html( $this->booking_args['booking_title'] ); ?> <?php esc_html_e( "name", "tourfic" ); ?></option>
							<?php
							$tf_posts_list       = array(
								'posts_per_page' => - 1,
								'post_type'      => $this->booking_args['post_type'],
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
            <form class="tf-right-search-filter">
                <input type="number" value="<?php echo ! empty( $_GET['post'] ) ? esc_attr( $_GET['post'] ) : ''; ?>" placeholder="Search by <?php echo esc_html( $this->booking_args['booking_title'] ); ?> ID"
                       id="tf-searching-key">
                <button class="tf-search-by-id" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M17.5 17.5L14.5834 14.5833M16.6667 9.58333C16.6667 13.4954 13.4954 16.6667 9.58333 16.6667C5.67132 16.6667 2.5 13.4954 2.5 9.58333C2.5 5.67132 5.67132 2.5 9.58333 2.5C13.4954 2.5 16.6667 5.67132 16.6667 9.58333Z"
                              stroke="#87888B" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>
        </div>

        <?php do_action( $this->booking_args["post_type"] . '_before_booking_order_table'); ?>

        <div class="<?php echo apply_filters( $this->booking_args["post_type"] . '_booking_oder_table_class', "tf-order-table-responsive") ?>">
            <table class="wp-list-table table" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <td id="cb">
                        <div class="tf-checkbox-listing">
                            <input id="cb-select-all-1" type="checkbox">
                        </div>
                    </td>
                    <th id="order_id">
						<?php esc_html_e( "ID", "tourfic" ); ?>
                    </th>
                    <th id="odetails">
						<?php echo esc_html($this->booking_args["booking_title"]) ?><?php esc_html_e( " name", "tourfic" ); ?>
                    </th>
                    <th id="cdetails">
						<?php esc_html_e( "Customer details", "tourfic" ); ?>
                    </th>
                    <th id="odate">
						<?php esc_html_e( "Booking date", "tourfic" ); ?>
                    </th>
                    <th id="check_status">
						<?php esc_html_e( "Checked in status", "tourfic" ); ?>
                    </th>
                    <th id="ostatus">
						<?php esc_html_e( "Order status", "tourfic" ); ?>
                    </th>
                    <th id="action">
						<?php esc_html_e( "Action", "tourfic" ); ?>
                    </th>
                </tr>
                </thead>

                <tbody>
				<?php
				$tf_key = 1;
				foreach ( $tf_order_details_result as $tf_order ) { ?>
                    <tr>
                        <th class="check-column">
                            <div class="tf-checkbox-listing">
                                <input type="checkbox" name="order_id[]" value="<?php echo esc_html( $tf_order['id'] ); ?>">
                            </div>
                        </th>
                        <td>
                            <a href="<?php echo esc_url(admin_url()); ?>edit.php?post_type=<?php echo esc_attr($this->booking_args['post_type']); ?>&amp;page=<?php echo esc_attr($this->booking_args['menu_slug']); ?>&amp;order_id=<?php echo esc_attr( $tf_order['order_id'] ); ?>&amp;book_id=<?php echo esc_attr( $tf_order['id'] ); ?>&amp;action=preview">
								<?php echo esc_html( $tf_order['order_id'] ); ?>
                            </a>
                        </td>
                        <td>
							<?php echo esc_html(get_the_title( $tf_order['post_id'] )); ?>
                        </td>
                        <td>
							<?php
							$billing_info       = json_decode( $tf_order['billing_details'] );
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
							echo wp_kses_post( $billing_details );
							?>
                        </td>
                        <td>
							<?php echo esc_html(gmdate( 'F d, Y', strtotime( $tf_order['order_date'] ) )); ?>
                        </td>
                        <td>
							<?php
							if ( ! empty( $tf_order['checkinout'] ) ) {
								if ( "in" == $tf_order['checkinout'] ) {
									echo wp_kses_post( '<span class="checkinout checkin">Checked in</span>' );
								}
								if ( "out" == $tf_order['checkinout'] ) {
									echo wp_kses_post( '<span class="checkinout checkout">Checked out</span>' );
								}
								if ( "not" == $tf_order['checkinout'] ) {
									echo wp_kses_post( '<span class="checkinout checkout">Not checked in</span>' );
								}
							} else {
								echo wp_kses_post( '<span class="checkinout checkout">Not checked in</span>' );
							}
							?>
                        </td>
                        <td style="text-transform: capitalize;">
							<?php echo esc_html( $tf_order['ostatus'] ); ?>
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
                <tfoot>
                <tr>
                    <th colspan="8">
                        <ul class="tf-booking-details-pagination">
							<?php if ( ! empty( $paged ) && $paged >= 2 ) { ?>
                                <li><a href="<?php echo esc_url(tf_booking_details_pagination( $paged - 1 )); ?>">
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
                                            <a href="<?php echo esc_url(tf_booking_details_pagination( $i )); ?>"><?php echo esc_html($i); ?></a>
                                        </li>
									<?php } else { ?>
                                        <li>
                                            <a href="<?php echo esc_url(tf_booking_details_pagination( $i )); ?>"><?php echo esc_html($i); ?></a>
                                        </li>
									<?php }
								}
							}
							if ( ! empty( $total_pages ) && ! empty( $paged ) && $paged < $total_pages ) {
								?>
                                <li><a href="<?php echo esc_url(tf_booking_details_pagination( $paged + 1 )); ?>"><?php esc_html_e( "Next", "tourfic" ); ?>
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

        <?php do_action( $this->booking_args["post_type"] . '_after_booking_order_table'); ?>

        <div class="tf-preloader-box">
            <div class="tf-loader-preview">
                <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="Loader">
            </div>
        </div>
		<?php
	}

	function tf_single_booking_details( $booking_type, $tf_order_details ) { ?>
        <div class="tf-booking-details-preview">
            <div class="tf-details-preview-header">
                <div class="tf-back">
                    <a href="<?php echo esc_url(get_admin_url( null, 'edit.php?post_type=' . $this->booking_args["post_type"] . '&page=' . $this->booking_args["menu_slug"] )); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                        <?php esc_html_e("Back", "tourfic"); ?>
                    </a>
                </div>
                <?php
                global $wpdb;
                $tf_order_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE id = %s AND order_id = %s",sanitize_key( $_GET['book_id'] ), sanitize_key( $_GET['order_id'] ) ) );
                ?>
                <input type="hidden" id="tf_email_order_id" value="<?php echo !empty($_GET['order_id']) ? esc_html( $_GET['order_id'] ) : ''; ?>">
                <div class="tf-title">
                    <h2><?php echo esc_html( get_the_title( $tf_order_details->post_id ) ); ?></h2>
                </div>
                <div class="tf-booking-id-author">
                    <ul>
                        <li><?php esc_html_e("Booking ID", "tourfic"); ?>: #<?php echo esc_html( $tf_order_details->order_id ); ?></li>
                        <li>|</li>
                        <li><?php esc_html_e("Booking created", "tourfic"); ?>: <?php echo esc_html(gmdate('F d, Y',strtotime($tf_order_details->order_date))); ?></li>
                        <li>|</li>
                        <li><?php esc_html_e("Booking by", "tourfic"); ?>: <span style="text-transform: capitalize;">
                            <?php 
                                $tf_booking_by = get_user_by('id', $tf_order_details->customer_id);
                                if("offline"==$tf_order_details->payment_method && empty($tf_booking_by)){
                                    echo "Administrator";
                                }else{
                                    echo !empty($tf_booking_by->roles[0]) ? esc_html($tf_booking_by->roles[0]) : 'Administrator';
                                }
                            ?>
                            </span>
                        </li>
                        <?php do_action($this->booking_args["post_type"] . '_single_booking_details_after_title_text'); ?>
                    </ul>
                </div>
            </div>
            <div class="tf-booking-details-preview-box">
                <div class="tf-booking-details">


                <?php do_action( 'tf_' . $this->booking_args["booking_type"] . '_single_booking_details_card_first'); ?>
                    
                    <!-- Booking Details -->
                    <div class="customers-order-date details-box">
                        <h4>
                            <?php apply_filters( 'tf_' . $this->booking_args["booking_type"] . 'booking_details_customer_section_title_change',  esc_html_e("Booking details", "tourfic") ); ?>
                        </h4>
                        <div class="tf-grid-box tf-customer-details-boxs">
                            <?php
                            $tf_billing_details = json_decode($tf_order_details->billing_details);
                            if(!empty($tf_billing_details)){ ?>
                            <div class="tf-grid-single">
                                <h3><?php esc_html_e("Customer details", "tourfic"); ?></h3>
                                <div class="tf-single-box">
                                    <table class="table" cellpadding="0" callspacing="0">
                                        <?php 
                                        foreach($tf_billing_details as $key=>$customer_info){ ?>
                                        <tr>
                                            <th><?php echo esc_html(str_replace("_"," ", $key )); ?></th>
                                            <td>:</td>
                                            <td><?php echo esc_html( $customer_info ); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <?php } 
                            
                            $tf_tour_details = json_decode($tf_order_details->order_details);
                            if(!empty( $tf_tour_details )){ ?>
                            <div class="tf-grid-single">
                                <h3><?php esc_html_e("Other details", "tourfic"); ?></h3>
                                <div class="tf-single-box">
                                    <table class="table">

                                        <!-- Service Name -->
                                        <tr>
                                            <th><?php esc_html_e("Name", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td><?php echo esc_html( get_the_title( $tf_order_details->post_id ) ); ?></td>
                                        </tr>

                                        <!-- Tour Date -->
                                        <?php if ( !empty($tf_tour_details->tour_date) ) { ?>
                                            <tr>
                                                <th><?php esc_html_e("Tour Date", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo esc_html($tf_tour_details->tour_date); ?></td>
                                            </tr>
                                        <?php } ?>

                                        <!-- Checkin Date -->
                                        <?php if(!empty($tf_tour_details->check_in)) : ?>
                                            <tr>
                                                <th><?php esc_html_e("Checkin", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo esc_html($tf_tour_details->check_in); ?></td>
                                            </tr>
                                        <?php endif; ?>  
                                        
                                        <!-- Checkout Date -->
                                        <?php if(!empty($tf_tour_details->check_out)) : ?>
                                            <tr>
                                                <th><?php esc_html_e("Checkout", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo esc_html($tf_tour_details->check_out); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        
                                        <!-- Room Name -->
                                        <?php if(!empty($tf_tour_details->room_name)) : ?>
                                            <tr>
                                                <th><?php esc_html_e("Room Name", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo esc_html($tf_tour_details->room_name); ?></td>
                                            </tr>
                                        <?php endif; ?>

	                                    <?php if ( !empty($tf_tour_details->option) ) { ?>
                                            <tr>
                                                <th><?php _e("Option", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo esc_html($tf_tour_details->option); ?></td>
                                            </tr>
	                                    <?php } ?>
                                        
                                        <!-- Room Count -->
                                        <?php if(!empty($tf_tour_details->room)) : ?>
                                            <tr>
                                                <th><?php esc_html_e("Room", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo esc_html($tf_tour_details->room); ?></td>
                                            </tr>
                                        <?php endif; ?>

                                        <!-- Tour Time -->

                                        <?php if ( !empty($tf_tour_details->tour_time) ) { ?>
                                        <tr>
                                            <th><?php esc_html_e("Time", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td><?php echo esc_html($tf_tour_details->tour_time); ?></td>
                                        </tr>
                                        <?php } ?>

                                        <!-- Adults Count -->
                                        <?php 
                                        $tf_total_visitor = 0;
                                        $book_adult  = !empty( $tf_tour_details->adult ) ? $tf_tour_details->adult : '';
                                        if(!empty($book_adult)){
                                            $tf_total_adult = explode( " × ", $book_adult );
                                        } ?>
                                        <tr>
                                            <th><?php esc_html_e("Adult", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td>
                                                <?php if(!empty($tf_total_adult[0])) {
                                                    echo esc_html($tf_total_adult[0]); 
                                                    $tf_total_visitor += $tf_total_adult[0];
                                                }else{
                                                    echo esc_html(0);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        
                                        <?php 
                                        $book_children  = !empty( $tf_tour_details->child ) ? $tf_tour_details->child : '';
                                        if(!empty($book_children)){
                                            $tf_total_children = explode( " × ", $book_children );
                                        } ?>
                                        <tr>
                                            <th><?php esc_html_e("Child", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td>
                                                <?php if(!empty($tf_total_children[0])) {
                                                    echo esc_html($tf_total_children[0]); 
                                                    $tf_total_visitor += $tf_total_children[0];
                                                }else{
                                                    echo esc_html(0);
                                                }
                                                ?>
                                            </td>
                                        </tr>

                                        <?php 
                                        $book_infants  = !empty( $tf_tour_details->infants ) ? $tf_tour_details->infants : '';
                                        if(!empty($book_infants)){
                                            $tf_total_infants = explode( " × ", $book_infants );
                                            ?>
                                            <tr>
                                                <th><?php esc_html_e("Infant", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td>
                                                    <?php if(!empty($tf_total_infants[0])) {
                                                        echo esc_html($tf_total_infants[0]); 
                                                        $tf_total_visitor += $tf_total_infants[0];
                                                    }else{
                                                        echo esc_html(0);
                                                    }
                                                    ?>    
                                                </td>
                                            </tr>
                                       <?php } ?>

                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Pricing Details -->
                    <div class="customers-order-date details-box">
                        <h4>
                            <?php echo esc_html( apply_filters( 'tf_' . $this->booking_args["booking_type"] . 'booking_details_pricing_section_title_change',  __( "Pricing details",  "tourfic"  ))); ?>
                        </h4>
                        <div class="tf-grid-box tf-pricing-grid-box">

                            <div class="tf-grid-single">
                                <div class="tf-single-box">
                                    <table class="table">
                                        
                                        <tr>
                                            <th><?php esc_html_e("Payment method", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td>
                                            <?php 
                                                if ( ! function_exists( 'tf_get_payment_method_full_name' ) ) {
                                                    function tf_get_payment_method_full_name( $sort_name ) {
                                                        $payment_gateways = \WC_Payment_Gateways::instance()->get_available_payment_gateways();
                                        
                                                        if ( isset( $payment_gateways[ $sort_name ] ) ) {
                                                            return $payment_gateways[ $sort_name ]->title;
                                                        } else {
                                                            return 'Offline Payment';
                                                        }
                                                    }
                                                }
                                                $sort_name = $tf_order_details->payment_method;
                                                echo esc_html(tf_get_payment_method_full_name( $sort_name ));
                                            ?>
                                            </td>
                                        </tr>
                                        <?php 
                                        if(!empty($tf_tour_details->tour_extra)){
                                        ?>
                                        <tr>
                                            <th><?php esc_html_e("Extra", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td><?php echo wp_kses_post($tf_tour_details->tour_extra); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php 
                                        if(!empty($tf_tour_details->total_price)){ ?>
                                        <tr>
                                            <th><?php esc_html_e("Total", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td><?php echo wp_kses_post(wc_price($tf_tour_details->total_price)); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php $taxs = !empty($tf_tour_details->tax_info) ? json_decode($tf_tour_details->tax_info, true) : array();
                                        $taxs_summations = 0;
                                        foreach ( $taxs as $label => $sum ) {
                                            $taxs_summations += $sum;
                                        }
                                        ?>
                                        <?php 
                                        if(!empty($taxs_summations)){ ?>
                                        <tr>
                                            <th><?php esc_html_e("Tax", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td><?php echo wp_kses_post(wc_price($taxs_summations)); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php 
                                        if(!empty($tf_tour_details->due_price)){ ?>
                                        <tr>
                                            <th><?php esc_html_e("Due Price", "tourfic"); ?></th>
                                            <td>:</td>
                                            <td><?php echo wp_kses_post($tf_tour_details->due_price); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tf_order_details->post_type == 'tour' ) { ?>
                    <!-- Visitor Details -->
                    <div class="customers-order-date details-box">
                        <h4>
                            <?php apply_filters( 'tf_' . $this->booking_args["booking_type"] . 'booking_details_visitor_section_title_change',  $tf_order_details->post_type == 'tour' ? esc_html_e("Visitor details", "tourfic") : esc_html_e("Guest details", "tourfic") ); ?>
                            <div class="others-button visitor_edit">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M2.39662 15.0963C2.43491 14.7517 2.45405 14.5794 2.50618 14.4184C2.55243 14.2755 2.61778 14.1396 2.70045 14.0142C2.79363 13.8729 2.91621 13.7503 3.16136 13.5052L14.1666 2.49992C15.0871 1.57945 16.5795 1.57945 17.4999 2.49993C18.4204 3.4204 18.4204 4.91279 17.4999 5.83326L6.49469 16.8385C6.24954 17.0836 6.12696 17.2062 5.98566 17.2994C5.86029 17.3821 5.72433 17.4474 5.58146 17.4937C5.42042 17.5458 5.24813 17.5649 4.90356 17.6032L2.08325 17.9166L2.39662 15.0963Z" stroke="#003C79" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php esc_html_e("Edit", "tourfic"); ?>
                                </span>
                            </div>
                        </h4>
                        <div class="tf-grid-box tf-visitor-grid-box">
                            <?php 
                            $tf_visitors_details = !empty($tf_tour_details->visitor_details) ? json_decode($tf_tour_details->visitor_details) : '';
                            $traveler_fields = !empty(Helper::tfopt('without-payment-field')) ? Helper::tf_data_types(Helper::tfopt('without-payment-field')) : '';
                            if(!empty($tf_visitors_details)){
                                $visitor_count = 1;
                                foreach($tf_visitors_details as $visitor){
                            ?>
                            <div class="tf-grid-single">
                                <?php /* translators: %s Visitor. */ ?>
                                <h3><?php echo sprintf( esc_html__("Visitor %s", "tourfic"), $visitor_count ); ?></h3>
                                <div class="tf-single-box">
                                    <table class="table" cellpadding="0" callspacing="0">
                                        <?php 
                                        if(!empty($traveler_fields)){
                                            foreach($traveler_fields as $field){
                                        ?>
                                        <tr>
                                            <th><?php echo esc_html( $field['reg-field-label'] ); ?></th>
                                            <td>:</td>
                                            <td><?php
                                            $field_key = $field['reg-field-name'];
                                            if("array"!=gettype($visitor->$field_key)){
                                                echo esc_html( $visitor->$field_key );
                                            }else{
                                                echo esc_html( implode(",", $visitor->$field_key ) );
                                            }
                                            ?>
                                            </td>
                                        </tr>
                                        <?php } }else{ ?>
                                            <tr>
                                                <th><?php esc_html_e("Full Name", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo !empty($visitor->tf_full_name) ? esc_html( $visitor->tf_full_name ) : ''; ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php esc_html_e("Date of birth", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo !empty($visitor->tf_dob) ? esc_html( $visitor->tf_dob ) : ''; ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php esc_html_e("NID", "tourfic"); ?></th>
                                                <td>:</td>
                                                <td><?php echo !empty($visitor->tf_nid) ? esc_html( $visitor->tf_nid ) : ''; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <?php $visitor_count++; } } ?>
                            
                        </div>
                    </div>

                    <?php } ?>

                    <!-- Check in out status -->
                    <?php $this->check_in_out_status( $tf_order_details ); ?>
                    
                    <!-- Voucher details -->
                    <?php 
                    $this->voucher_details( $tf_tour_details, $tf_order_details, $tf_billing_details );
                    ?>

                </div>
                <div class="tf-booking-actions">
                    <div class="tf-filter-selection">
                        <h3><?php esc_html_e("Actions", "tourfic"); ?></h3>
                        <div class="tf-order-status-filter tf-order-ostatus">
                            <label>
                                <span>
                                    <?php 
                                        if( !empty($tf_order_details->ostatus) ){
                                            if( "trash"==$tf_order_details->ostatus ){
                                                esc_html_e("Trash", "tourfic");
                                            }elseif( "processing"==$tf_order_details->ostatus ){
                                                esc_html_e("Processing", "tourfic");
                                            }elseif( "on-hold"==$tf_order_details->ostatus ){
                                                esc_html_e("On Hold", "tourfic");
                                            }elseif( "completed"==$tf_order_details->ostatus ){
                                                esc_html_e("Complete", "tourfic");
                                            }elseif( "cancelled"==$tf_order_details->ostatus ){
                                                esc_html_e("Cancelled", "tourfic");
                                            }elseif( "refunded"==$tf_order_details->ostatus ){
                                                esc_html_e("Refund", "tourfic");
                                            }
                                        }else{
                                            esc_html_e("Processing", "tourfic");
                                        }
                                    ?>
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </label>
                            <ul>
                                <li data-value="trash"><?php esc_html_e("Trash", "tourfic"); ?></li>
                                <li data-value="processing"><?php esc_html_e("Processing", "tourfic"); ?></li>
                                <li data-value="on-hold"><?php esc_html_e("On Hold", "tourfic"); ?></li>
                                <li data-value="completed"><?php esc_html_e("Complete", "tourfic"); ?></li>
                                <li data-value="cancelled"><?php esc_html_e("Cancelled", "tourfic"); ?></li>
                                <li data-value="refunded"><?php esc_html_e("Refund", "tourfic"); ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="tf-filter-selection">
                        <h3><?php esc_html_e("Checked in status", "tourfic"); ?></h3>
                        <div class="tf-order-status-filter tf-order-checkinout-status">
                            <label>
                                <span>
                                    <?php 
                                        if( !empty($tf_order_details->checkinout) ){
                                            if( "in"==$tf_order_details->checkinout ){
                                                esc_html_e("Checked in", "tourfic");
                                            }elseif( "out"==$tf_order_details->checkinout ){
                                                esc_html_e("Checked Out", "tourfic");
                                            }elseif( "not"==$tf_order_details->checkinout ){
                                                esc_html_e("Not checked in", "tourfic");
                                            }
                                        }else{
                                            esc_html_e("Not checked in", "tourfic");
                                        }
                                    ?>
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </label>
                            <input type="hidden" id="tf_email_order_id" value="<?php echo !empty($_GET['order_id']) ? esc_html( $_GET['order_id'] ) : ''; ?>">
                            <input type="hidden" class="tf_single_order_id" name="order_id" value="<?php echo esc_attr($tf_order_details->id); ?>">
                            <ul>
                                <li class="checkin" data-value="in"><?php esc_html_e("Checked in", "tourfic"); ?></li>
                                <li class="checkout" data-value="out"><?php esc_html_e("Checked Out", "tourfic"); ?></li>
                                <li class="checkout" data-value="not"><?php esc_html_e("Not checked in", "tourfic"); ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="tf-filter-selection">
                        <h3><?php esc_html_e("Sent order mail", "tourfic"); ?></h3>
                        <div class="tf-order-status-filter tf-order-email-resend">
                            <label>
                                <span><?php esc_html_e("Resend Order Mail", "tourfic"); ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </label>
                            <ul>
                                <li data-value="customer"><?php esc_html_e("Customer", "tourfic"); ?></li>
                                <?php 
                                $tf_vendor_id = get_post_field ('post_author', $tf_order_details->post_id);
                                //get user role by id
                                $tf_user = get_user_by( 'id', $tf_vendor_id );
                                $tf_user_role = !empty( $tf_user->roles[0] ) ? $tf_user->roles[0] : '';
                                //check if user role is vendor
                                if( $tf_user_role == 'tf_vendor' ){
                                ?>
                                    <li data-value="vendor"><?php esc_html_e("Vendor", "tourfic"); ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="tf-preloader-box">
            <div class="tf-loader-preview">
                <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="Loader">
            </div>
        </div>

		<?php if ( "tf_tours" == $this->booking_args['post_type'] ) { ?>
            <div class="visitor-details-edit-form">
                <form class="visitor-details-edit-popup">
                    <div class="tf-visitor-details-edit-header">
                        <h2>
                            <?php esc_html_e("Edit visitor details", "tourfic"); ?>
                        </h2>
                        <div class="tf-booking-times">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                                <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                                <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <div class="visitor-details-popup">
                    <input type="hidden" class="tf_single_order_id" name="order_id" value="<?php echo esc_attr($tf_order_details->id); ?>">
                    <?php 
                    for($traveller_in = 1; $traveller_in <= $tf_total_visitor; $traveller_in++){ ?>
                        <div class="tf-single-tour-traveller tf-single-travel">
                            <h4><?php echo esc_html__( 'Traveler ', 'tourfic' ) . esc_html($traveller_in) ?></h4>
                            <div class="traveller-info">
                            <?php
                            if(empty($traveler_fields)){ ?>
                            <div class="traveller-single-info">
                                <label for="tf_full_name<?php echo esc_attr($traveller_in); ?>"><?php esc_html_e( 'Full Name', 'tourfic' ); ?></label>
                                <input type="text" name="traveller[<?php echo esc_attr($traveller_in); ?>][tf_full_name]" id="tf_full_name<?php echo esc_attr($traveller_in); ?>" data-required="1" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{'tf_full_name'}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{'tf_full_name'} ) : '' ?>" />
                                
                            </div>
                            <div class="traveller-single-info">
                                <label for="tf_dob<?php echo esc_attr($traveller_in); ?>"><?php esc_html_e( 'Date of birth', 'tourfic' ); ?></label>
                                <input type="date" name="traveller[<?php echo esc_attr($traveller_in); ?>][tf_dob]" id="tf_dob<?php echo esc_attr($traveller_in); ?>" data-required="1" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{'tf_dob'}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{'tf_dob'} ) : '' ?>"/>
                                
                            </div>
                            <div class="traveller-single-info">
                                <label for="tf_nid<?php echo esc_attr($traveller_in); ?>"><?php esc_html_e( 'NID', 'tourfic' ); ?></label>
                                <input type="text" name="traveller[<?php echo esc_attr($traveller_in); ?>][tf_nid]" id="tf_nid<?php echo esc_attr($traveller_in); ?>" data-required="1" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{'tf_nid'}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{'tf_nid'} ) : '' ?>"/>
                                
                            </div>
                        <?php
                        }else{
                            foreach($traveler_fields as $field){
                                if("text"==$field['reg-fields-type'] || "email"==$field['reg-fields-type'] || "date"==$field['reg-fields-type']){
                                    $field_keys = $field['reg-field-name'];
                                    ?>
                                    <div class="traveller-single-info">
                                        <label for="<?php echo esc_attr($field['reg-field-name']).esc_attr($traveller_in) ?>"><?php echo esc_html( $field['reg-field-label'] ); ?></label>
                                        <input type="<?php echo esc_attr($field['reg-fields-type']); ?>" name="traveller[<?php echo esc_attr($traveller_in); ?>][<?php echo esc_attr($field['reg-field-name']); ?>]" id="<?php echo esc_attr($field['reg-field-name']).esc_attr($traveller_in); ?>" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{$field_keys}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{$field_keys} ) : '' ?>" />
                                    </div>
                                <?php
                                }
                                if("select"==$field['reg-fields-type'] && !empty($field['reg-options'])){
                                    $field_keys = $field['reg-field-name'];
                                ?>
                                <div class="traveller-single-info">
                                    <label for="<?php echo esc_attr($field['reg-field-name']).esc_attr($traveller_in) ?>">
                                        <?php echo esc_html( $field['reg-field-label'] ); ?>
                                    </label>
                                    <select id="<?php echo esc_attr($field['reg-field-name']).esc_attr($traveller_in) ?>" name="traveller[<?php echo esc_attr($traveller_in); ?>][<?php echo esc_attr($field['reg-field-name']); ?>]">
                                    <option value=""><?php echo esc_html__( 'Select One', 'tourfic' ); ?></option>
                                    <?php
                                    foreach($field['reg-options'] as $sfield){
                                        if(!empty($sfield['option-label']) && !empty($sfield['option-value'])){ ?>
                                            <option value="<?php echo esc_attr($sfield['option-value']); ?>" <?php echo !empty($tf_visitors_details->{$traveller_in}->{$field_keys}) && $sfield['option-value']==$tf_visitors_details->{$traveller_in}->{$field_keys} ? esc_attr( 'selected' ) : '' ?>><?php echo esc_html($sfield['option-label']); ?></option>';
                                        <?php
                                        }
                                    } ?>
                                    </select>
                                </div>
                                <?php
                                }
                                if(("checkbox"==$field['reg-fields-type'] || "radio"==$field['reg-fields-type']) && !empty($field['reg-options'])){
                                    $field_keys = $field['reg-field-name'];
                                    $tf_fields_values = !empty($tf_visitors_details->{$traveller_in}->{$field_keys}) ? $tf_visitors_details->{$traveller_in}->{$field_keys} : [''];
                                ?>
                                    
                                <div class="traveller-single-info">
                                <label for="<?php echo esc_attr($field['reg-field-name']).esc_attr($traveller_in) ?>">
                                <?php echo esc_html($field['reg-field-label']); ?>
                                </label>
                                    <?php
                                    foreach($field['reg-options'] as $sfield){
                                        if(!empty($sfield['option-label']) && !empty($sfield['option-value'])){
                                            ?>
                                            <div class="tf-single-checkbox">
                                                <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="traveller[<?php echo esc_attr($traveller_in); ?>][<?php echo esc_attr($field['reg-field-name']); ?>][]" id="<?php echo esc_attr($sfield['option-value'].$traveller_in); ?>" value="<?php echo esc_attr($sfield['option-value']); ?>" <?php echo in_array($sfield['option-value'], $tf_fields_values) ? esc_attr( 'checked' ) : ''; ?> />
                                                <label for="<?php echo esc_attr($sfield['option-value'].$traveller_in); ?>">
                                                <?php echo esc_html($sfield['option-label']); ?>
                                                </label>
                                            </div>
                                            <?php
                                        }
                                    } ?>
                                    </div>
                                <?php
                                }
                            }
                        }
                        ?>
                        </div>
                        </div>
                    <?php } ?>
                    </div>
                    <div class="details-update-btn">
                        <button type="submit"><?php esc_html_e("Update", "tourfic"); ?></button>
                    </div>
                </form>
            </div>
		<?php } ?>
        <!-- Voucher Quick View -->
		<?php
        $tour_ides = !empty($tf_tour_details->unique_id) ? $tf_tour_details->unique_id : '';
		if ( ! empty( $tour_ides ) ) {
            $this->voucher_quick_view( $tf_tour_details, $tf_order_details, $tf_billing_details );
		}
	}

    abstract function voucher_details ($tf_tour_details, $tf_order_details, $tf_billing_details);
    abstract function voucher_quick_view( $tf_tour_details, $tf_order_details, $tf_billing_details );
    abstract function check_in_out_status( $tf_order_details );
    // Pagination Function

    function tf_booking_details_pagination($page){
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

    // Ajax Callback Function

    function tf_order_status_edit_function() {

        // Add nonce for security and authentication.
        check_ajax_referer('updates', '_ajax_nonce');
        
        // Order Id
        $tf_order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : "";
        // status Value
        $tf_status = !empty($_POST['status']) ? $_POST['status'] : "";
    
        global $wpdb;
        $tf_order = $wpdb->get_row( $wpdb->prepare( "SELECT id, order_id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );
    
        // Order Status Update into Database
        if(!empty($tf_order)){
            $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET ostatus=%s WHERE id=%s", sanitize_title( $tf_status ), sanitize_key($tf_order_id))
            );
    
            // Woocommerce status
            $order = wc_get_order($tf_order->order_id);
            if (!empty($order)) {
                $order->update_status( sanitize_key($tf_status), '', true );
            }
        }
        
        die();
    }

    function tf_visitor_details_edit_function() {

        // Add nonce for security and authentication.
        check_ajax_referer('updates', '_ajax_nonce');
    
        // Order Id
        $tf_order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : "";
        // Visitor Details
        $tf_visitor_details = !empty($_POST['traveller']) ? $_POST['traveller'] : "";
    
        global $wpdb;
        $tf_order = $wpdb->get_row( $wpdb->prepare( "SELECT id,order_details FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );
        $tf_order_details = json_decode($tf_order->order_details);
        $tf_order_details->visitor_details = wp_json_encode($tf_visitor_details);
    
        // Visitor Details Update
        if(!empty($tf_order)){
            $wpdb->query(
                $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET order_details=%s WHERE id=%s", wp_json_encode($tf_order_details), sanitize_key($tf_order_id))
            );
        }
        die();
    }

    function tf_checkinout_details_edit_function() {

        // Add nonce for security and authentication.
        check_ajax_referer('updates', '_ajax_nonce');
    
        // Order Id
        $tf_order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : "";
        // Checkinout Value
        $tf_checkinout = !empty($_POST['checkinout']) ? $_POST['checkinout'] : "";
    
        /**
         * Get current logged in user
        */
        $current_user = wp_get_current_user();
        // get user id
        $current_user_id = $current_user->ID;
        $ft_checkinout_by = array(
            'userid' => $current_user_id,
            'time'   => gmdate("d F, Y h:i:s a")
        );
    
        global $wpdb;
        $tf_order = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );
        $tf_woo_order_id = $wpdb->get_row( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );
        $tf_order_post_type = $wpdb->get_row( $wpdb->prepare( "SELECT post_type FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );
    
        $tf_order_uni_id = !empty($tf_woo_order_id) ? get_option("tf_order_uni_" . $tf_woo_order_id->order_id) : "";
    
        // Checkinout Status Update into Database
        if(!empty($tf_order)){
            $wpdb->query(
                $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET checkinout=%s, checkinout_by=%s WHERE id=%s", sanitize_title( $tf_checkinout ), wp_json_encode( $ft_checkinout_by ), sanitize_key($tf_order_id))
            );
            if(!empty( $tf_order_uni_id ) && $tf_order_post_type->post_type =='tour' ){
                if($tf_checkinout == "in") {
                    update_option("tf_" . $tf_order_uni_id, "in");
                } else {
                    update_option("tf_" . $tf_order_uni_id, "");
                }
            }
        }
        die();
    }

    function tf_order_bulk_action_edit_function() {

        // Add nonce for security and authentication.
        check_ajax_referer('updates', '_ajax_nonce');
        
        // Order Id
        $tf_orders = !empty($_POST['orders']) ? $_POST['orders'] : "";
        // status Value
        $tf_status = !empty($_POST['status']) ? $_POST['status'] : "";
    
        global $wpdb;
        foreach($tf_orders as $order){
            if("trash"==$tf_status){
                $wpdb->query(
                    $wpdb->prepare( "DELETE FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $order ) )
                );
            }else{
                $tf_single_order = $wpdb->get_row( $wpdb->prepare( "SELECT id, order_id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $order ) ) );
    
                // Order Status Update into Database
                if(!empty($tf_single_order)){
                    $wpdb->query(
                    $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET ostatus=%s WHERE id=%s", sanitize_title( $tf_status ), sanitize_key($order))
                    );
    
                    // Woocommerce status
                    $order = wc_get_order($tf_single_order->order_id);
                    if (!empty($order)) {
                        $order->update_status( sanitize_key($tf_status), '', true );
                    }
                }
            }
        }
        die();
    }
}

