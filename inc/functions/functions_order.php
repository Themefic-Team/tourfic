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
		$current_user_role = ! empty( $current_user->roles[0] ) ? $current_user->roles[0] : '';
		if ( $current_user_role == 'administrator' ) {
			// Tour booking
			add_submenu_page( 'edit.php?post_type=tf_tours', __( 'Tour Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_tourss', 'tf_tours_booking', 'tf_tour_booking_page_callback' );

			// Hotel booking
			add_submenu_page( 'edit.php?post_type=tf_hotel', __( 'Hotel Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_hotels', 'tf_hotel_booking', 'tf_hotel_booking_page_callback' );

			//Apartment booking
			add_submenu_page( 'edit.php?post_type=tf_apartment', __( 'Apartment Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_apartments', 'tf_apartment_booking', 'tf_apartment_booking_page_callback' );
		}
		if ( $current_user_role == 'tf_vendor' ) {
			if ( ! empty( tf_data_types( tfopt( 'multi-vendor-setings' ) )['vendor-booking-history'] ) && tf_data_types( tfopt( 'multi-vendor-setings' ) )['vendor-booking-history'] == '1' ) {
				// Tour booking
				add_submenu_page( 'edit.php?post_type=tf_tours', __( 'Tour Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_tourss', 'tf_tours_booking', 'tf_tour_booking_page_callback' );

				// Hotel booking
				add_submenu_page( 'edit.php?post_type=tf_hotel', __( 'Hotel Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_hotels', 'tf_hotel_booking', 'tf_hotel_booking_page_callback' );

				//Apartment booking
				add_submenu_page( 'edit.php?post_type=tf_apartment', __( 'Apartment Booking Details', 'tourfic' ), __( 'Booking Details', 'tourfic' ), 'edit_tf_apartments', 'tf_apartment_booking', 'tf_apartment_booking_page_callback' );
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

		if(!empty($_GET['order_id']) && !empty($_GET['action']) && !empty($_GET['book_id'])){
			/**
			 * Booking Details showing new template
			 * @since 2.10.0
			 */
			if ( file_exists( TF_INC_PATH . 'booking-details/tour/single-booking-details.php' ) ) {
				require_once TF_INC_PATH . 'booking-details/tour/single-booking-details.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'booking-details/tour/single-booking-details.php' );
			}
		}else{
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

		global $wpdb;
		$table_name = $wpdb->prefix . 'tf_order_data';
		if ( $current_user_role == 'administrator' ) {

			// Filter Perameters
			$checkinout_perms = !empty($_GET['checkinout']) ? $_GET['checkinout'] : '';
			$tf_post_perms = !empty($_GET['post']) ? $_GET['post'] : '';
			$tf_payment_perms = !empty($_GET['payment']) ? $_GET['payment'] : '';

			$tf_filter_query = "";
			if($checkinout_perms){
				$tf_filter_query .= " AND checkinout = '$checkinout_perms'";
			}
			if($tf_post_perms){
				$tf_filter_query .= " AND post_id = '$tf_post_perms'";
			}
			if($tf_payment_perms){
				$tf_filter_query .= " AND ostatus = '$tf_payment_perms'";
			}

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

				if (isset($_GET['paged'])) {
					$paged = $_GET['paged'];
				} else {
					$paged = 1;
				}
				$no_of_booking_per_page = 20;
				$offset = ($paged-1) * $no_of_booking_per_page;

				$tf_booking_details_select    = array(
					'select' => "*",
					'post_type' => 'tour',
					'query'  => " $tf_filter_query ORDER BY id DESC"
				);
				
				$tours_tour_booking_result = tourfic_order_table_data( $tf_booking_details_select );
				$total_rows = !empty(count($tours_tour_booking_result)) ? count($tours_tour_booking_result) : 0;
				$total_pages = ceil($total_rows / $no_of_booking_per_page);
				
				$tf_orders_select    = array(
					'select' => "*",
					'post_type' => 'tour',
					'query'  => " $tf_filter_query ORDER BY id DESC LIMIT $offset, $no_of_booking_per_page"
				);
				
				$tours_orders_result = tourfic_order_table_data( $tf_orders_select );
			} else {
				$tf_orders_select    = array(
					'select' => "*",
					'post_type' => 'tour',
					'query'  => " $tf_filter_query ORDER BY id DESC LIMIT 15"
				);
				$tours_orders_result = tourfic_order_table_data( $tf_orders_select );
			}
		}

		?>

        <div class="wrap tf_booking_details_wrap" style="margin-right: 20px;">
            <div id="tf-booking-status-loader">
                <img src="<?php echo TF_ASSETS_URL; ?>app/images/loader.gif" alt="Loader">
            </div>
			<div class="tf_booking_wrap_header">
				<h1 class="wp-heading-inline"><?php _e( 'Tour Booking Details', 'tourfic' ); ?></h1>
				<div class="tf_header_wrap_button">
					<?php
					/**
					 * Before Tour booking details table hook
					 * @hooked tf_before_tour_booking_details - 10
					 * @since 2.9.18
					 */
					do_action( 'tf_before_tour_booking_details' );
					?>
				</div>
			</div>
            <hr class="wp-header-end">

			<?php
			/**
			 * Booking Data showing new template
			 * @since 2.9.26
			 */
			if ( file_exists( TF_INC_PATH . 'booking-details/tour/booking-details.php' ) ) {
				require_once TF_INC_PATH . 'booking-details/tour/booking-details.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'booking-details/tour/booking-details.php' );
			}
			?>
        </div>

	<?php }
	}
}

/**
 * Hotel booking page callback function
 *
 * Display all the order details
 */
if ( ! function_exists( 'tf_hotel_booking_page_callback' ) ) {
	function tf_hotel_booking_page_callback() {
		if(!empty($_GET['order_id']) && !empty($_GET['action']) && !empty($_GET['book_id'])){
			/**
			 * Booking Details showing new template
			 * @since 2.10.0
			 */
			if ( file_exists( TF_INC_PATH . 'booking-details/hotel/single-booking-details.php' ) ) {
				require_once TF_INC_PATH . 'booking-details/hotel/single-booking-details.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'booking-details/hotel/single-booking-details.php' );
			}
		}else{
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

			if ( $current_user_role == 'administrator' ) {

				// Filter Perameters
				$checkinout_perms = !empty($_GET['checkinout']) ? $_GET['checkinout'] : '';
				$tf_post_perms = !empty($_GET['post']) ? $_GET['post'] : '';
				$tf_payment_perms = !empty($_GET['payment']) ? $_GET['payment'] : '';

				$tf_filter_query = "";
				if($checkinout_perms){
					$tf_filter_query .= " AND checkinout = '$checkinout_perms'";
				}
				if($tf_post_perms){
					$tf_filter_query .= " AND post_id = '$tf_post_perms'";
				}
				if($tf_payment_perms){
					$tf_filter_query .= " AND ostatus = '$tf_payment_perms'";
				}

				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

					if (isset($_GET['paged'])) {
						$paged = $_GET['paged'];
					} else {
						$paged = 1;
					}
					
					$no_of_booking_per_page = 20;
					$offset = ($paged-1) * $no_of_booking_per_page;

					$tf_booking_details_select    = array(
						'select' => "*",
						'post_type' => 'hotel',
						'query'  => " $tf_filter_query ORDER BY id DESC"
					);
					
					$tf_hotel_booking_result = tourfic_order_table_data( $tf_booking_details_select );
					$total_rows = !empty(count($tf_hotel_booking_result)) ? count($tf_hotel_booking_result) : 0;
					$total_pages = ceil($total_rows / $no_of_booking_per_page);
					
					$tf_orders_select    = array(
						'select' => "*",
						'post_type' => 'hotel',
						'query'  => " $tf_filter_query ORDER BY id DESC LIMIT $offset, $no_of_booking_per_page"
					);
					
					$hotel_orders_result = tourfic_order_table_data( $tf_orders_select );

				} else {
					$tf_orders_select    = array(
						'select' => "*",
						'post_type' => 'hotel',
						'query'  => " ORDER BY order_id DESC LIMIT 15"
					);
					$hotel_orders_result = tourfic_order_table_data( $tf_orders_select );
				}
			}

		?>

		<div class="wrap tf_booking_details_wrap" style="margin-right: 20px;">
            <div id="tf-booking-status-loader">
                <img src="<?php echo TF_ASSETS_URL; ?>app/images/loader.gif" alt="Loader">
            </div>
			<div class="tf_booking_wrap_header">
				<h1 class="wp-heading-inline"><?php _e( 'Hotel Booking Details', 'tourfic' ); ?></h1>
				<div class="tf_header_wrap_button">
					<?php
					/**
					 * Before Hotel booking details table hook
					 * @hooked tf_before_hotel_booking_details - 10
					 * @since 2.9.18
					 */
					do_action( 'tf_before_hotel_booking_details' );
					?>
				</div>
			</div>
            <hr class="wp-header-end">

			<?php
			/**
			 * Booking Data showing new template
			 * @since 2.9.26
			 */
			if ( file_exists( TF_INC_PATH . 'booking-details/hotel/booking-details.php' ) ) {
				require_once TF_INC_PATH . 'booking-details/hotel/booking-details.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'booking-details/hotel/booking-details.php' );
			}
			?>
        </div>

	<?php } }
}


/**
 * Apartment booking page callback function
 *
 * Display all the order details
 */
if ( ! function_exists( 'tf_apartment_booking_page_callback' ) ) {
	function tf_apartment_booking_page_callback() {
		if(!empty($_GET['order_id']) && !empty($_GET['action']) && !empty($_GET['book_id'])){
			/**
			 * Booking Details showing new template
			 * @since 2.10.0
			 */
			if ( file_exists( TF_INC_PATH . 'booking-details/apartment/single-booking-details.php' ) ) {
				require_once TF_INC_PATH . 'booking-details/apartment/single-booking-details.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'booking-details/apartment/single-booking-details.php' );
			}
		}else{

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

		// Filter Perameters
		$checkinout_perms = !empty($_GET['checkinout']) ? $_GET['checkinout'] : '';
		$tf_post_perms = !empty($_GET['post']) ? $_GET['post'] : '';
		$tf_payment_perms = !empty($_GET['payment']) ? $_GET['payment'] : '';

		$tf_filter_query = "";
		if($checkinout_perms){
			$tf_filter_query .= " AND checkinout = '$checkinout_perms'";
		}
		if($tf_post_perms){
			$tf_filter_query .= " AND post_id = '$tf_post_perms'";
		}
		if($tf_payment_perms){
			$tf_filter_query .= " AND ostatus = '$tf_payment_perms'";
		}

		if ( $current_user_role == 'administrator' ) {
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

				if (isset($_GET['paged'])) {
					$paged = $_GET['paged'];
				} else {
					$paged = 1;
				}
				
				$no_of_booking_per_page = 20;
				$offset = ($paged-1) * $no_of_booking_per_page;

				$tf_booking_details_select    = array(
					'select' => "*",
					'post_type' => 'apartment',
					'query'  => " $tf_filter_query ORDER BY id DESC"
				);
				
				$tf_hotel_booking_result = tourfic_order_table_data( $tf_booking_details_select );
				$total_rows = !empty(count($tf_hotel_booking_result)) ? count($tf_hotel_booking_result) : 0;
				$total_pages = ceil($total_rows / $no_of_booking_per_page);
				
				$tf_orders_select    = array(
					'select' => "*",
					'post_type' => 'apartment',
					'query'  => " $tf_filter_query ORDER BY id DESC LIMIT $offset, $no_of_booking_per_page"
				);
				
				$apartment_orders_result = tourfic_order_table_data( $tf_orders_select );

			} else {
				$tf_orders_select        = array(
					'select' => "*",
					'post_type' => 'apartment',
					'query'  => " ORDER BY id DESC LIMIT 15"
				);
				$apartment_orders_result = tourfic_order_table_data( $tf_orders_select );
			}
		}
		
		?>

		<div class="wrap tf_booking_details_wrap" style="margin-right: 20px;">
            <div id="tf-booking-status-loader">
                <img src="<?php echo TF_ASSETS_URL; ?>app/images/loader.gif" alt="Loader">
            </div>
			<div class="tf_booking_wrap_header">
				<h1 class="wp-heading-inline"><?php _e( 'Apartment Booking Details', 'tourfic' ); ?></h1>
				<div class="tf_header_wrap_button">
					<?php
					/**
					 * Before Apartment booking details table hook
					 * @hooked tf_before_apartment_booking_details - 10
					 * @since 2.9.18
					 */
					do_action( 'tf_before_apartment_booking_details' );
					?>
				</div>
			</div>
            <hr class="wp-header-end">

			<?php
			/**
			 * Booking Data showing new template
			 * @since 2.9.26
			 */
			if ( file_exists( TF_INC_PATH . 'booking-details/apartment/booking-details.php' ) ) {
				require_once TF_INC_PATH . 'booking-details/apartment/booking-details.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'booking-details/apartment/booking-details.php' );
			}
			?>
        </div>

	<?php } }
}

/**
 * Add _order_type order meta from line order meta
 */
if ( ! function_exists( 'tf_add_order_type_order_meta' ) ) {
	function tf_add_order_type_order_meta( $order_id ) {

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item_key => $item_values ) {
			$item_data = $item_values->get_data();

			// Assign _order_type meta in line order meta
			if ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'tour' ) {
				update_post_meta( $order_id, '_order_type', 'tour' );
			} elseif ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'hotel' ) {
				update_post_meta( $order_id, '_order_type', 'hotel' );
			} elseif ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'apartment' ) {
				update_post_meta( $order_id, '_order_type', 'apartment' );
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
if ( ! function_exists( 'tf_custom_query_var_get_orders' ) ) {
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

/*
 * TF set order
 * @author Foysal
 * @since 2.9.26
 */
if ( ! function_exists( 'tf_set_order' ) ) {
	function tf_set_order( $order_data ) {
		global $wpdb;
		$table_name    = $wpdb->prefix . 'tf_order_data';
		$all_order_ids = $wpdb->get_col( "SELECT order_id FROM $table_name" );
		do {
			$order_id = mt_rand( 10000000, 99999999 );
		} while ( in_array( $order_id, $all_order_ids ) );

		$defaults = array(
			'order_id'         => $order_id,
			'post_id'          => 0,
			'post_type'        => '',
			'room_number'      => 0,
			'room_id'          => 0,
			'check_in'         => '',
			'check_out'        => '',
			'billing_details'  => '',
			'shipping_details' => '',
			'order_details'    => '',
			'customer_id'      => 1,
			'payment_method'   => 'cod',
			'status'           => 'processing',
			'order_date'       => date( 'Y-m-d H:i:s' ),
		);

		$order_data = wp_parse_args( $order_data, $defaults );

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $table_name
				( order_id, post_id, post_type, room_number, room_id, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
				array(
					$order_data['order_id'],
					sanitize_key( $order_data['post_id'] ),
					$order_data['post_type'],
					$order_data['room_number'],
					$order_data['room_id'],
					$order_data['check_in'],
					$order_data['check_out'],
					json_encode( $order_data['billing_details'] ),
					json_encode( $order_data['shipping_details'] ),
					json_encode( $order_data['order_details'] ),
					$order_data['customer_id'],
					$order_data['payment_method'],
					$order_data['status'],
					$order_data['order_date']
				)
			)
		);

		return $order_id;
	}
}

/*
 * TF get all order id
 * @author Foysal
 * @since 2.9.26
 */
if ( ! function_exists( 'tf_get_all_order_id' ) ) {
	function tf_get_all_order_id() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'tf_order_data';
		$order_ids  = $wpdb->get_col( "SELECT order_id FROM $table_name" );

		return $order_ids;
	}
}

/*
* Admin order data migration
* @author Jahid
*/

function tf_admin_order_data_migration(){

	/**
	 * Order Data
	 * Create Order Data Database
	 * @author jahid
	 */
	global $wpdb;
	$order_table_name = $wpdb->prefix.'tf_order_data';
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$sql = "CREATE TABLE IF NOT EXISTS $order_table_name (
		 id bigint(20) NOT NULL AUTO_INCREMENT,
		 order_id bigint(20) NOT NULL,
		 post_id bigint(20) NOT NULL,
		 post_type varchar(255),
		 room_number varchar(255) NULL,
         room_id varchar(255) NULL,
		 check_in date NOT NULL,  
		 check_out date NULL,
         checkinout varchar(255) NULL,
         checkinout_by varchar(255) NULL,
		 billing_details text,
		 shipping_details text,
		 order_details text,
		 customer_id bigint(11) NOT NULL,
		 payment_method varchar(255),
		 ostatus varchar(255),
		 order_date datetime NOT NULL,
		 PRIMARY KEY  (id)
	 ) $charset_collate;";
	dbDelta( $sql );

	if ( empty( get_option( 'tf_old_order_data_migrate' ) ) ) {

		$tf_old_order_limit = new WC_Order_Query( array (
			'limit' => -1,
			'orderby' => 'date',
			'order' => 'ASC',
			'return' => 'ids',
		) );
		$order = $tf_old_order_limit->get_orders();

		foreach ( $order as $item_id => $item ) {
			$itemmeta = wc_get_order( $item);
			if ( is_a( $itemmeta, 'WC_Order_Refund' ) ) {
				$itemmeta = wc_get_order( $itemmeta->get_parent_id() );
			}
			$tf_ordering_date =  $itemmeta->get_date_created();

			//Order Data Insert
			$billinginfo = [
				'billing_first_name' => !empty($itemmeta->get_billing_first_name()) ? $itemmeta->get_billing_first_name() : '',
				'billing_last_name' => !empty($itemmeta->get_billing_last_name()) ? $itemmeta->get_billing_last_name() : '',
				'billing_company' => !empty($itemmeta->get_billing_company()) ? $itemmeta->get_billing_company() : '',
				'billing_address_1' => !empty($itemmeta->get_billing_address_1()) ? $itemmeta->get_billing_address_1() : '',
				'billing_address_2' => !empty($itemmeta->get_billing_address_2()) ? $itemmeta->get_billing_address_2() : '',
				'billing_city' => !empty($itemmeta->get_billing_city()) ? $itemmeta->get_billing_city() : '',
				'billing_state' => !empty($itemmeta->get_billing_state()) ? $itemmeta->get_billing_state() : '',
				'billing_postcode' => !empty($itemmeta->get_billing_postcode()) ? $itemmeta->get_billing_postcode() : '',
				'billing_country' => !empty($itemmeta->get_billing_country()) ? $itemmeta->get_billing_country() : '',
				'billing_email' => !empty($itemmeta->get_billing_email()) ? $itemmeta->get_billing_email() : '',
				'billing_phone' => !empty($itemmeta->get_billing_phone()) ? $itemmeta->get_billing_phone() : ''
			];

			$shippinginfo = [
				'shipping_first_name' => !empty($itemmeta->get_shipping_first_name()) ? $itemmeta->get_shipping_first_name() : '',
				'shipping_last_name' => !empty($itemmeta->get_shipping_last_name()) ? $itemmeta->get_shipping_last_name() : '',
				'shipping_company' => !empty($itemmeta->get_shipping_company()) ? $itemmeta->get_shipping_company() : '',
				'shipping_address_1' => !empty($itemmeta->get_shipping_address_1()) ? $itemmeta->get_shipping_address_1() : '',
				'shipping_address_2' => !empty($itemmeta->get_shipping_address_2()) ? $itemmeta->get_shipping_address_2() : '',
				'shipping_city' => !empty($itemmeta->get_shipping_city()) ? $itemmeta->get_shipping_city() : '',
				'shipping_state' => !empty($itemmeta->get_shipping_state()) ? $itemmeta->get_shipping_state() : '',
				'shipping_postcode' => !empty($itemmeta->get_shipping_postcode()) ? $itemmeta->get_shipping_postcode() : '',
				'shipping_country' => !empty($itemmeta->get_shipping_country()) ? $itemmeta->get_shipping_country() : '',
				'shipping_phone' => !empty($itemmeta->get_shipping_phone()) ? $itemmeta->get_shipping_phone() : ''
			];

			foreach ( $itemmeta->get_items() as $item_key => $item_values ) {
				$order_type   = wc_get_order_item_meta( $item_key, '_order_type', true );
				if("hotel"==$order_type){
					$post_id   = wc_get_order_item_meta( $item_key, '_post_id', true );
					$unique_id = wc_get_order_item_meta( $item_key, '_unique_id', true );
					$room_selected = wc_get_order_item_meta( $item_key, 'number_room_booked', true );
					$check_in = wc_get_order_item_meta( $item_key, 'check_in', true );
					$check_out = wc_get_order_item_meta( $item_key, 'check_out', true );
					$price = $itemmeta->get_subtotal();
					$due = wc_get_order_item_meta( $item_key, 'due', true );
					$room_name = wc_get_order_item_meta( $item_key, 'room_name', true );
					$adult = wc_get_order_item_meta( $item_key, 'adult', true );
					$child = wc_get_order_item_meta( $item_key, 'child', true );
					$children_ages = wc_get_order_item_meta( $item_key, 'Children Ages', true );
					$airport_service_type = wc_get_order_item_meta( $item_key, 'Airport Service', true );
					$airport_service_fee = wc_get_order_item_meta( $item_key, 'Airport Service Fee', true );

					$iteminfo = [
						'room' => $room_selected,
						'room_unique_id' => $unique_id,
						'check_in' => $check_in,
						'check_out' => $check_out,
						'room_name' => $room_name,
						'adult' => $adult,
						'child' => $child,
						'children_ages' => $children_ages,
						'airport_service_type' => $airport_service_type,
						'airport_service_fee' => $airport_service_fee,
						'total_price' => $price,
						'due_price' => $due,
					];

					$iteminfo_keys = array_keys($iteminfo);
					$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

					$iteminfo_values = array_values($iteminfo);
					$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

					$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

					global $wpdb;
					$table_name = $wpdb->prefix.'tf_order_data';
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO $table_name
						( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
							array(
								$item,
								sanitize_key( $post_id ),
								$order_type,
								$room_selected,
								$check_in,
								$check_out,
								json_encode($billinginfo),
								json_encode($shippinginfo),
								json_encode($iteminfo),
								$itemmeta->get_customer_id(),
								$itemmeta->get_payment_method(),
								$itemmeta->get_status(),
								$tf_ordering_date->date('Y-m-d H:i:s')
							)
						)
					);
				}
				if("tour"==$order_type){
					$post_id   = wc_get_order_item_meta( $item_key, '_tour_id', true );
					$tour_date = wc_get_order_item_meta( $item_key, 'Tour Date', true );
					$tour_time = wc_get_order_item_meta( $item_key, 'Tour Time', true );
					$price = $itemmeta->get_subtotal();
					$due = wc_get_order_item_meta( $item_key, 'Due', true );
					$tour_extra = wc_get_order_item_meta( $item_key, 'Tour Extra', true );
					$adult = wc_get_order_item_meta( $item_key, 'Adults', true );
					$child = wc_get_order_item_meta( $item_key, 'Children', true );
					$infants = wc_get_order_item_meta( $item_key, 'Infants', true );
					$datatype_check = preg_match("/-/", $tour_date);
					if ( !empty($tour_date) && !empty($datatype_check) ) {
						list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
					}
					if ( !empty($tour_date) && empty($datatype_check) ) {
						$tour_in = date( "Y-m-d", strtotime( $tour_date ) );
						$tour_out = "0000-00-00";
					}


					$iteminfo = [
						'tour_date' => $tour_date,
						'tour_time' => $tour_time,
						'tour_extra' => $tour_extra,
						'adult' => $adult,
						'child' => $child,
						'infants' => $infants,
						'total_price' => $price,
						'due_price' => $due,
					];

					$iteminfo_keys = array_keys($iteminfo);
					$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

					$iteminfo_values = array_values($iteminfo);
					$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

					$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

					global $wpdb;
					$table_name = $wpdb->prefix.'tf_order_data';
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO $table_name
						( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
							array(
								$item,
								sanitize_key( $post_id ),
								$order_type,
								date( "Y-m-d", strtotime( $tour_in ) ),
								date( "Y-m-d", strtotime( $tour_out ) ),
								json_encode($billinginfo),
								json_encode($shippinginfo),
								json_encode($iteminfo),
								$itemmeta->get_customer_id(),
								$itemmeta->get_payment_method(),
								$itemmeta->get_status(),
								$tf_ordering_date->date('Y-m-d H:i:s')
							)
						)
					);
				}
			}

		}
		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_old_order_data_migrate', 1 );
	}
}

add_action( 'admin_init', 'tf_admin_order_data_migration' );

/*
* Admin order data new field "checkinout & checkinout_by" added
* @author Jahid
*/
if ( ! function_exists( 'tf_admin_table_alter_order_data' ) ) {
	function tf_admin_table_alter_order_data() {
		global $wpdb;
		$order_table_name = $wpdb->prefix . 'tf_order_data';
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		// Check if the 'checkinout' & 'checkinout_by' column exists before attempting to add it
		if ( !$wpdb->get_var("SHOW COLUMNS FROM $order_table_name LIKE 'checkinout'") &&
		!$wpdb->get_var("SHOW COLUMNS FROM $order_table_name LIKE 'checkinout_by'") ) {
			$sql = "ALTER TABLE $order_table_name 
					ADD COLUMN checkinout varchar(255) NULL,
					ADD COLUMN checkinout_by varchar(255) NULL";
			$wpdb->query($sql);
		}

        // Check if the 'room_id' column exists before attempting to add it
        if ( !$wpdb->get_var("SHOW COLUMNS FROM $order_table_name LIKE 'room_id'") ) {
            $sql = "ALTER TABLE $order_table_name 
                    ADD COLUMN room_id varchar(255) NULL";
            $wpdb->query($sql);
        }
	}
}
add_action('admin_init', 'tf_admin_table_alter_order_data');

