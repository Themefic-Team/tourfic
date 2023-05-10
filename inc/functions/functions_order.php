<?php
defined( 'ABSPATH' ) || exit;

/**
 * Add different booking submenu under each post types
 *
 * tf_tours
 * tf_hotel
 */
if ( ! function_exists( 'tf_add_order_submenu' ) ) {
	function tf_add_order_submenu() {
		$current_user = wp_get_current_user();
		// get user role
		$current_user_role = !empty($current_user->roles[0]) ? $current_user->roles[0] : '';
		if($current_user_role == 'administrator'){
			// Tour booking
			add_submenu_page( 'edit.php?post_type=tf_tours', __( 'Tour Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_tourss', 'tf_tours_booking', 'tf_tour_booking_page_callback' );

			// Hotel booking
			add_submenu_page( 'edit.php?post_type=tf_hotel', __( 'Hotel Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_hotels', 'tf_hotel_booking', 'tf_hotel_booking_page_callback' );
		}
		if($current_user_role == 'tf_vendor'){
			if ( ! empty( tf_data_types(tfopt( 'multi-vendor-setings' ))['vendor-booking-history'] ) && tf_data_types(tfopt( 'multi-vendor-setings' ))['vendor-booking-history'] == '1' ) {
				// Tour booking
				add_submenu_page( 'edit.php?post_type=tf_tours', __( 'Tour Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_tourss', 'tf_tours_booking', 'tf_tour_booking_page_callback' );

				// Hotel booking
				add_submenu_page( 'edit.php?post_type=tf_hotel', __( 'Hotel Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_hotels', 'tf_hotel_booking', 'tf_hotel_booking_page_callback' );
			}
		}
	}

	add_action( 'admin_menu', 'tf_add_order_submenu' );
}
/**
 * Tours booking page callback function
 *
 * Display all the order details
 */
if ( ! function_exists( 'tf_tour_booking_page_callback' ) ) {
function tf_tour_booking_page_callback() {

	/**
	 * Get current logged in user
	 */
	$current_user = wp_get_current_user();
	// get user id
	$current_user_id = $current_user->ID;
	// get user role
	$current_user_role = $current_user->roles[0];

	// if is not desired user role die
	if ( $current_user_role == 'administrator' || $current_user_role == 'tf_vendor' ) {
	} else {
		wp_die( __( 'You are not allowed in this page', 'tourfic' ) );
	}

	/**
	 * Main query
	 */
	// get current page number
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	// post per page limit
	$limit = 20;
	// Query
	if ( $current_user_role == 'administrator' ) {
		$query_orders = wc_get_orders( array( 'numberposts' => $limit, 'paginate' => true, '_order_type' => 'tour', 'paged' => $pagenum ) );
	}
	if ( $current_user_role == 'tf_vendor' ) {
		$query_orders = wc_get_orders( array( 'numberposts' => $limit, 'paginate' => true, '_order_type' => 'tour', '_post_author' => $current_user_id, 'paged' => $pagenum ) );
	}
	$offset       = ( $pagenum - 1 ) * $limit;

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	} else {
		$query_orders->orders = array_slice( $query_orders->orders, 0, 15 );
		$query_orders->total  = count( $query_orders->orders );
	}

	// Total number of items
	$total = $query_orders->total;
	// Number of pages
	$num_of_pages = ceil( $total / $limit );
	// Pagination
	$page_links = paginate_links( array(
		'base'      => add_query_arg( 'pagenum', '%#%' ),
		'format'    => '',
		'prev_text' => __( '&laquo;', 'tourfic' ),
		'next_text' => __( '&raquo;', 'tourfic' ),
		'total'     => $num_of_pages,
		'current'   => $pagenum
	) );
	?>

    <div class="wrap" style="margin-right: 20px;">
        <h1 class="wp-heading-inline"><?php _e( 'Tour Booking Details', 'tourfic' ); ?></h1>
		<a href="<?php echo admin_url( 'admin.php?page=tf_tours_booking&export=csv&type=tour' ); ?>" class="button button-primary page-title-action"><?php _e( 'Export', 'tourfic' ); ?></a>
        <hr class="wp-header-end">
        <form id="posts-filter">
            <div class="tablenav top">
				<?php if ( $page_links ) { ?>
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $query_orders->total; ?><?php _e( 'items', 'tourfic' ); ?></span>
                        <span class="pagination-links">
                        <?php echo $page_links; ?>
                    </span>
                    </div>
                    <br class="clear">
				<?php } ?>
            </div>

            <table class="wp-list-table widefat fixed striped table-view-list pages tf-order-data-table">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'tourfic' ) ?></label><input id="cb-select-all-1"
                                                                                                                                                                                          type="checkbox"></td>
                    <th class="manage-column column-title column-primary sortable desc" style="width: 4%;">
                        <span><?php _e( 'Order ID', 'tourfic' ); ?></span>
                    </th>
                    <th class="manage-column sortable" style="width: 25%;">
                        <a href="#"><span><?php _e( 'Customer Details', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
                    <th class="manage-column sortable" style="width: 25%;">
                        <a href="#"><span><?php _e( 'Tour Details', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
                    <th class="manage-column sortable" style="width: 10%;">
                        <a href="#"><span><?php _e( 'Order Date', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
                    <th class="manage-column sortable" style="width: 8%;">
                        <a href="#"><?php _e( 'Total Price', 'tourfic' ); ?></a>
                    </th>
                    <th class="manage-column sortable" style="width: 8%;">
                        <a href="#"><?php _e( 'Status', 'tourfic' ); ?></a>
                    </th>
                    <th class="manage-column sortable" style="width: 12%;">
                        <a href="#"><span><?php _e( 'Payment Method', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
					<?php if ( $current_user_role == 'administrator' ) { ?>
                        <th class="manage-column sortable" style="width: 8%;"></th>
					<?php } ?>
                </tr>
                </thead>

                <tbody>

				<?php
				// Get orders
				$orders = $query_orders->orders;
				foreach ( $orders as $key=> $order ) {
					if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
						tf_tour_order_single_row( $order );
					} else {
						if ( $key == 14) {
							tf_tour_order_single_row( $order );
							echo '<tr class="pro-row" style="text-align: center; background-color: #ededf8"><td colspan="9"><a href="https://tourfic.com/" target="_blank"><h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;">' . __( 'Upgrade to Pro Version to see more', 'tourfic' ) . '</h3></a></td></tr>';
						} else {
							tf_tour_order_single_row( $order );
						}
					}
				}
				?>

                </tbody>
            </table>

            <div class="tablenav bottom">
				<?php if ( $page_links ) { ?>
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $query_orders->total; ?><?php _e( 'items', 'tourfic' ); ?></span>
                        <span class="pagination-links">
                        <?php echo $page_links; ?>
                    </span>
                    </div>
                    <br class="clear">
				<?php } ?>
            </div>
        </form>
    </div>

    <script>
        jQuery(document).ready(function ($) {

            $(".page-numbers").addClass("button tablenav-pages-navspan");
            $(".current").addClass("disabled");

        });
    </script>
<?php }
}

if(!function_exists('tf_tour_order_single_row')){
function tf_tour_order_single_row($order){
	/**
	 * Get current logged in user
	 */
	$current_user = wp_get_current_user();
	// get user id
	$current_user_id = $current_user->ID;
	// get user role
	$current_user_role = $current_user->roles[0];
	/**
	 * Get order data
	 *
	 * https://stackoverflow.com/a/50364348
	 */
	$order_data = $order->get_data();
	// Assign individual order data
	$order_id             = $order_data['id'];
	$customer_name        = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];
	$customer_email       = $order_data['billing']['email'];
	$customer_phone       = $order_data['billing']['phone'];
	$customer_address     = $order_data['billing']['address_1'] . ', ' . $order_data['billing']['address_2'] . ', ' . $order_data['billing']['city'] . ', ' . WC()->countries->countries[ $order_data['billing']['country'] ];
	$order_date           = $order_data['date_created']->date( 'Y-m-d H:i:s' );
	$order_total          = $order_data['currency'] . ' ' . $order->get_total();
	$order_status         = $order_data['status'];
	$order_payment_method = $order_data['payment_method_title'];

	?>
    <tr id="" class="iedit author-self level-0 type-page status-publish hentry entry">
        <th scope="row" class="check-column">
            <label class="screen-reader-text" for="cb-select-93"><?php _e( 'Select Email Verification', 'tourfic' ); ?></label>
            <input id="cb-select-93" type="checkbox" name="post[]" value="93">
            <div class="locked-indicator">
                <span class="locked-indicator-icon" aria-hidden="true"></span>
                <span class="screen-reader-text"><?php _e( '“Email Verification” is locked', 'tourfic' ); ?></span>
            </div>
        </th>
        <td><?php echo $order_id; ?></td>
        <td>
			<?php
			if ( $customer_name ) {
				echo '<b>' . __( "Name", "tourfic" ) . ': </b>' . $customer_name . '<br>';
			}
			if ( $customer_email ) {
				echo '<b>' . __( "E-mail", "tourfic" ) . ': </b>' . $customer_email . '<br>';
			}
			if ( $customer_phone ) {
				echo '<b>' . __( "Phone", "tourfic" ) . ': </b>' . $customer_phone . '<br>';
			}
			if ( $customer_address ) {
				echo '<b>' . __( "Address", "tourfic" ) . ': </b>' . $customer_address . '<br>';
			}
			?>
        </td>
        <td><?php
			// Get order item metas

			foreach ( $order->get_items() as $item_key => $item_values ) {
				$tour_id   = wc_get_order_item_meta( $item_key, '_tour_id', true );
				$order_type = $item_values->get_meta( '_order_type', true );
				$tour_name = esc_html( get_the_title( $tour_id ) );
				$tour_url  = esc_url( get_permalink( $tour_id ) );
				$tour_date = ! empty( wc_get_order_item_meta( $item_key, 'Tour Date', true ) ) ? wc_get_order_item_meta( $item_key, 'Tour Date', true ) : '';
				$tour_time = ! empty( wc_get_order_item_meta( $item_key, 'Tour Time', true ) ) ? wc_get_order_item_meta( $item_key, 'Tour Time', true ) : '';
				$adult     = ! empty( wc_get_order_item_meta( $item_key, 'Adults', true ) ) ? wc_get_order_item_meta( $item_key, 'Adults', true ) : '';
				$children  = ! empty( wc_get_order_item_meta( $item_key, 'Children', true ) ) ? wc_get_order_item_meta( $item_key, 'Children', true ) : '';
				$infant    = ! empty( wc_get_order_item_meta( $item_key, 'Infants', true ) ) ? wc_get_order_item_meta( $item_key, 'Infants', true ) : '';
				$due       = ! empty( wc_get_order_item_meta( $item_key, 'Due', true ) ) ? wc_get_order_item_meta( $item_key, 'Due', true ) : false;
				if(!empty($order_type) && "tour"==$order_type){
					echo '<b>' . __( "Tour Name", "tourfic" ) . ': </b><a href="' . $tour_url . '" target="_blank">' . $tour_name . '</a><br>';

					if ( $tour_date ) {
						echo '<b>' . __( "Tour Date", "tourfic" ) . ': </b>' . $tour_date . '<br>';
					}

					if ( $tour_time ) {
						echo '<b>' . __( "Tour Time", "tourfic" ) . ': </b>' . $tour_time . '<br>';
					}

					if ( $adult ) {
						echo '<b>' . __( "Adult Number", "tourfic" ) . ': </b>' . $adult . '<br>';
					}

					if ( $children ) {
						echo '<b>' . __( "Children Number", "tourfic" ) . ': </b>' . $children . '<br>';
					}

					if ( $infant ) {
						echo '<b>' . __( "Infant Number", "tourfic" ) . ': </b>' . $infant . '<br>';
					}
				}
			} ?>
        </td>
        <td><?php
			echo $order_date;
			?></td>
        <td>
			<?php echo $order_total; ?>
			<?php if ( ! empty( $due ) ) { ?>
                <br>
                <b><?php echo __( "Due", "tourfic" ); ?>: </b><?php echo $due; ?>
			<?php } ?>
        </td>
        <td><?php
			echo $order_status;
			?></td>
        <td><?php
			echo $order_payment_method;
			?></td>
		<?php if ( $current_user_role == 'administrator' ) { ?>
            <td>
                <a href="<?php echo get_admin_url( null, 'post.php?post=' . $order_id . '&action=edit' ); ?>" class="button button-secondary"><?php _e( 'Edit', 'tourfic' ); ?></a>
            </td>
		<?php } ?>
    </tr>
	<?php
}
}

/**
 * Hotel booking page callback function
 *
 * Display all the order details
 */
if ( ! function_exists( 'tf_hotel_booking_page_callback' ) ) {
function tf_hotel_booking_page_callback() {
	/**
	 * Get current logged in user
	 */
	$current_user = wp_get_current_user();
	// get user id
	$current_user_id = $current_user->ID;
	// get user role
	$current_user_role = $current_user->roles[0];

	// if is not desired user role die
	if ( $current_user_role == 'administrator' || $current_user_role == 'tf_vendor' ) {
	} else {
		wp_die( __( 'You are not allowed in this page', 'tourfic' ) );
	}

	/**
	 * Main query
	 */
	// get current page number
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	// post per page limit
	$limit = 20;
	// Query
	if ( $current_user_role == 'administrator' ) {
		$query_orders = wc_get_orders( array( 'numberposts' => $limit, 'paginate' => true, '_order_type' => 'hotel', 'paged' => $pagenum ) );
	}
	if ( $current_user_role == 'tf_vendor' ) {
		$query_orders = wc_get_orders( array( 'numberposts' => $limit, 'paginate' => true, '_order_type' => 'hotel', '_post_author' => $current_user_id, 'paged' => $pagenum ) );
	}
	$offset       = ( $pagenum - 1 ) * $limit;

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	} else {
		$query_orders->orders = array_slice( $query_orders->orders, 0, 15 );
		$query_orders->total  = count( $query_orders->orders );
	}

	// Total number of items
	$total = $query_orders->total;
	// Number of pages
	$num_of_pages = ceil( $total / $limit );
	// Pagination
	$page_links = paginate_links( array(
		'base'      => add_query_arg( 'pagenum', '%#%' ),
		'format'    => '',
		'prev_text' => __( '&laquo;', 'tourfic' ),
		'next_text' => __( '&raquo;', 'tourfic' ),
		'total'     => $num_of_pages,
		'current'   => $pagenum
	) );
	?>

    <div class="wrap" style="margin-right: 20px;">
        <h1 class="wp-heading-inline"><?php _e( 'Hotel Booking Details', 'tourfic' ); ?></h1>
        <hr class="wp-header-end">
        <form id="posts-filter">
            <div class="tablenav top">
				<?php if ( $page_links ) { ?>
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $query_orders->total; ?><?php _e( 'items', 'tourfic' ); ?></span>
                        <span class="pagination-links">
                        <?php echo $page_links; ?>
                    </span>
                    </div>
                    <br class="clear">
				<?php } ?>
            </div>

            <table class="wp-list-table widefat fixed striped table-view-list pages tf-order-data-table">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th class="manage-column column-title column-primary sortable desc" style="width: 4%;">
                        <span><?php _e( 'Order ID', 'tourfic' ); ?></span>
                    </th>
                    <th class="manage-column sortable" style="width: 25%;">
                        <a href="#"><span><?php _e( 'Customer Details', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
                    <th class="manage-column sortable" style="width: 25%;">
                        <a href="#"><span><?php _e( 'Order Details', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
                    <th class="manage-column sortable" style="width: 10%;">
                        <a href="#"><span><?php _e( 'Order Date', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
                    <th class="manage-column sortable" style="width: 8%;">
                        <a href="#"><?php _e( 'Total Price', 'tourfic' ); ?></a>
                    </th>
                    <th class="manage-column sortable" style="width: 8%;">
                        <a href="#"><?php _e( 'Status', 'tourfic' ); ?></a>
                    </th>
                    <th class="manage-column sortable" style="width: 12%;">
                        <a href="#"><span><?php _e( 'Payment Method', 'tourfic' ); ?></span><span class="sorting-indicator"></span></a>
                    </th>
					<?php if ( $current_user_role == 'administrator' ) { ?>
                        <th class="manage-column sortable" style="width: 8%;"></th>
					<?php } ?>
                </tr>
                </thead>

                <tbody>

				<?php
				// Get orders
				$orders = $query_orders->orders;
				foreach ( $orders as $key=> $order ) {
					if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
						tf_hotel_order_single_row( $order );
					} else {
						if ( $key == 14) {
							tf_hotel_order_single_row( $order );
							echo '<tr class="pro-row" style="text-align: center; background-color: #ededf8"><td colspan="9"><a href="https://tourfic.com/" target="_blank"><h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;">' . __( 'Upgrade to Pro Version to see more', 'tourfic' ) . '</h3></a></td></tr>';
						} else {
							tf_hotel_order_single_row( $order );
						}
					}
				}
				?>

                </tbody>
            </table>

            <div class="tablenav bottom">
				<?php if ( $page_links ) { ?>
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $query_orders->total; ?><?php _e( 'items', 'tourfic' ); ?></span>
                        <span class="pagination-links">
                        <?php echo $page_links; ?>
                    </span>
                    </div>
                    <br class="clear">
				<?php } ?>
            </div>
        </form>
    </div>

    <script>
        jQuery(document).ready(function ($) {

            $(".page-numbers").addClass("button tablenav-pages-navspan");
            $(".current").addClass("disabled");

        });
    </script>
<?php }
}

if(!function_exists('tf_hotel_order_single_row')){
function tf_hotel_order_single_row($order){

	/**
	 * Get current logged in user
	 */
	$current_user = wp_get_current_user();
	// get user id
	$current_user_id = $current_user->ID;
	// get user role
	$current_user_role = $current_user->roles[0];

	/**
	 * Get order data
	 *
	 * https://stackoverflow.com/a/50364348
	 */
	$order_data = $order->get_data();

	// Assign individual order data
	$order_id             = $order_data['id'];
	$customer_name        = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];
	$customer_email       = $order_data['billing']['email'];
	$customer_phone       = $order_data['billing']['phone'];
	$customer_address     = $order_data['billing']['address_1'] . ', ' . $order_data['billing']['address_2'] . ',<br>' . $order_data['billing']['city'] . ', ' . WC()->countries->countries[ $order_data['billing']['country'] ];
	$order_date           = $order_data['date_created']->date( 'Y-m-d H:i:s' );
	$order_total          = $order_data['currency'] . ' ' . $order->get_total();
	$order_status         = $order_data['status'];
	$order_payment_method = $order_data['payment_method_title'];
	?>
    <tr id="" class="iedit author-self level-0 type-page status-publish hentry entry">
        <th scope="row" class="check-column">
            <label class="screen-reader-text" for="cb-select-93">Select Email Verification</label>
            <input id="cb-select-93" type="checkbox" name="post[]" value="93">
            <div class="locked-indicator">
                <span class="locked-indicator-icon" aria-hidden="true"></span>
                <span class="screen-reader-text">“Email Verification” is locked</span>
            </div>
        </th>
        <td><?php
			echo $order_id;
			?></td>
        <td>
			<?php
			if ( $customer_name ) {
				echo '<b>' . __( "Name", "tourfic" ) . ': </b>' . $customer_name . '<br>';
			}
			if ( $customer_email ) {
				echo '<b>' . __( "E-mail", "tourfic" ) . ': </b>' . $customer_email . '<br>';
			}
			if ( $customer_phone ) {
				echo '<b>' . __( "Phone", "tourfic" ) . ': </b>' . $customer_phone . '<br>';
			}
			if ( $customer_address ) {
				echo '<b>' . __( "Address", "tourfic" ) . ': </b>' . $customer_address . '<br>';
			}
			?>
        </td>
        <td><?php


			// Get order item metas
			foreach ( $order->get_items() as $item_key => $item_values ) {

				$post_id             = wc_get_order_item_meta( $item_key, '_post_id', true );
				$order_type 		 = $item_values->get_meta( '_order_type', true );
				$hotel_name          = esc_html( get_the_title( $post_id ) );
				$hotel_url           = esc_url( get_permalink( $post_id ) );
				$room_name           = ! empty( wc_get_order_item_meta( $item_key, 'room_name', true ) ) ? wc_get_order_item_meta( $item_key, 'room_name', true ) : '';
				$room_booked         = ! empty( wc_get_order_item_meta( $item_key, 'number_room_booked', true ) ) ? wc_get_order_item_meta( $item_key, 'number_room_booked', true ) : '';
				$check_in            = ! empty( wc_get_order_item_meta( $item_key, 'check_in', true ) ) ? wc_get_order_item_meta( $item_key, 'check_in', true ) : '';
				$check_out           = ! empty( wc_get_order_item_meta( $item_key, 'check_out', true ) ) ? wc_get_order_item_meta( $item_key, 'check_out', true ) : '';
				$adult               = ! empty( wc_get_order_item_meta( $item_key, 'adult', true ) ) ? wc_get_order_item_meta( $item_key, 'adult', true ) : '';
				$child               = ! empty( wc_get_order_item_meta( $item_key, 'child', true ) ) ? wc_get_order_item_meta( $item_key, 'child', true ) : '';
				$due                 = ! empty( wc_get_order_item_meta( $item_key, 'due', true ) ) ? wc_get_order_item_meta( $item_key, 'due', true ) : null;
				$airport_service     = ! empty( wc_get_order_item_meta( $item_key, 'Airport Service', true ) ) ? wc_get_order_item_meta( $item_key, 'Airport Service', true ) : 'No';
				$airport_service_fee = ! empty( wc_get_order_item_meta( $item_key, 'Airport Service Fee', true ) ) ? wc_get_order_item_meta( $item_key, 'Airport Service Fee', true ) : '';
				if(!empty($order_type) && "hotel"==$order_type){
					echo '<b>' . __( "Hotel Name", "tourfic" ) . ': </b><a href="' . $hotel_url . '" target="_blank">' . $hotel_name . '</a><br>';

					if ( $room_name ) {
						echo '<b>' . __( "Room", "tourfic" ) . ': </b>' . $room_name . '<br>';
					}

					if ( $room_booked ) {
						echo '<b>' . __( "Room Booked", "tourfic" ) . ': </b>' . $room_booked . '<br>';
					}

					if ( $adult ) {
						echo '<b>' . __( "Adult Number", "tourfic" ) . ': </b>' . $adult . '<br>';
					}

					if ( $child ) {
						echo '<b>' . __( "Children Number", "tourfic" ) . ': </b>' . $child . '<br>';
					}

					if ( $check_in ) {
						echo '<b>' . __( "Check-in", "tourfic" ) . ': </b>' . $check_in . '<br>';
					}

					if ( $check_out ) {
						echo '<b>' . __( "Check-out", "tourfic" ) . ': </b>' . $check_out . '<br>';
					}
					if ( ! empty( $airport_service ) ) {
						echo '<b>' . __( "Airport Service", "tourfic" ) . ': </b>' . $airport_service . '<br>';
					}
					if ( ! empty( $airport_service_fee ) ) {
						echo '<b>' . __( "Airport Service Fee", "tourfic" ) . ': </b>' . $airport_service_fee . '<br>';
					}
				}

			} ?>
        </td>
        <td><?php
			echo $order_date;
			?></td>
        <td>
			<?php echo $order_total; ?>
			<?php if ( ! empty( $due ) ) {
				echo '<br/><b>' . __( "Due", "tourfic" ) . ': </b> <b>' . $due . '</b><br>';
			} ?>
        </td>
        <td><?php
			echo $order_status;
			?></td>
        <td><?php
			echo $order_payment_method;
			?></td>
		<?php if ( $current_user_role == 'administrator' ) { ?>
            <td>
                <a href="<?php echo get_admin_url( null, 'post.php?post=' . $order_id . '&action=edit' ); ?>" class="button button-secondary"><?php _e( 'Edit', 'tourfic' ); ?></a>
            </td>
		<?php } ?>
    </tr>
    <?php
}
}

/**
 * Add _order_type order meta from line order meta
 */
if(!function_exists('tf_add_order_type_order_meta')) {
	function tf_add_order_type_order_meta( $order_id ) {

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item_key => $item_values ) {
			$item_data = $item_values->get_data();

			// Assign _order_type meta in line order meta
			if ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'tour' ) {
				update_post_meta( $order_id, '_order_type', 'tour' );
			} elseif ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'hotel' ) {
				update_post_meta( $order_id, '_order_type', 'hotel' );
			}

			// Assign _post_author meta in line order meta
			$post_author = wc_get_order_item_meta( $item_key, '_post_author', true );
			update_post_meta( $order_id, '_post_author', $post_author );
		}
	}
	add_action( 'woocommerce_checkout_update_order_meta', 'tf_add_order_type_order_meta' );
}

/**
 * Add custom query var in WooCommerce get orders query
 *
 * _order_type, _post_author
 *
 * https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query#adding-custom-parameter-support
 */
if(!function_exists('tf_custom_query_var_get_orders')) {
	function tf_custom_query_var_get_orders( $query, $query_vars ) {

		if ( ! empty( $query_vars['_order_type'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_order_type',
				'value' => esc_attr( $query_vars['_order_type'] ),
			);
		}

		if ( ! empty( $query_vars['_post_author'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_post_author',
				'value' => esc_attr( $query_vars['_post_author'] ),
			);
		}

		return $query;
	}

	add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'tf_custom_query_var_get_orders', 10, 2 );
}