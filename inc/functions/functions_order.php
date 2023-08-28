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

		/**
		 * Get current logged in user
		 */

		if ( file_exists( TF_INC_PATH . 'functions/class.tf_tour.php' ) ) {
			require_once TF_INC_PATH . 'functions/class.tf_tour.php';
		} else {
			tf_file_missing( TF_INC_PATH . 'functions/class.tf_tour.php' );
		}

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
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$tf_orders_select    = array(
					'select' => "*",
					'query'  => "post_type = 'tour' ORDER BY order_id DESC"
				);
				$tours_orders_result = tourfic_order_table_data( $tf_orders_select );
			} else {
				$tf_orders_select    = array(
					'select' => "*",
					'query'  => "post_type = 'tour' ORDER BY order_id DESC LIMIT 15"
				);
				$tours_orders_result = tourfic_order_table_data( $tf_orders_select );
			}
		}
		if ( $current_user_role == 'tf_vendor' ) {
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

				$tf_orders_select    = array(
					'select'    => "*",
					'post_type' => "tour",
					'author'    => $current_user_id,
					'limit'     => ""
				);
				$tours_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );

			} else {
				$tf_orders_select    = array(
					'select'    => "*",
					'post_type' => "tour",
					'author'    => $current_user_id,
					'limit'     => "LIMIT 15"
				);
				$tours_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			}
		}

		?>

        <div class="wrap" style="margin-right: 20px;">
            <div id="tf-booking-status-loader">
                <img src="<?php echo TF_ASSETS_URL; ?>app/images/loader.gif" alt="Loader">
            </div>
            <h1 class="wp-heading-inline"><?php _e( 'Tour Booking Details', 'tourfic' ); ?></h1>
			<?php
			/**
			 * Before Tour booking details table hook
			 * @hooked tf_before_tour_booking_details - 10
			 * @since 2.9.18
			 */
			do_action( 'tf_before_tour_booking_details' );

			?>
            <hr class="wp-header-end">

			<?php
			/**
			 * Booking Data showing from tourfic table
			 * @since 2.9.26
			 */
			$tours_orders_result = new DBTFTOURTable( $tours_orders_result );
			$tours_orders_result->prepare_items();
			$tours_orders_result->display();
			?>
        </div>

	<?php }
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
		if ( file_exists( TF_INC_PATH . 'functions/class.tf_hotel.php' ) ) {
			require_once TF_INC_PATH . 'functions/class.tf_hotel.php';
		} else {
			tf_file_missing( TF_INC_PATH . 'functions/class.tf_hotel.php' );
		}

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
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$tf_orders_select    = array(
					'select' => "*",
					'query'  => "post_type = 'hotel' ORDER BY order_id DESC"
				);
				$hotel_orders_result = tourfic_order_table_data( $tf_orders_select );
			} else {
				$tf_orders_select    = array(
					'select' => "*",
					'query'  => "post_type = 'hotel' ORDER BY order_id DESC LIMIT 15"
				);
				$hotel_orders_result = tourfic_order_table_data( $tf_orders_select );
			}
		}
		if ( $current_user_role == 'tf_vendor' ) {
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$tf_orders_select    = array(
					'select'    => "*",
					'post_type' => "hotel",
					'author'    => $current_user_id,
					'limit'     => ""
				);
				$hotel_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			} else {
				$tf_orders_select    = array(
					'select'    => "*",
					'post_type' => "hotel",
					'author'    => $current_user_id,
					'limit'     => "LIMIT 15"
				);
				$hotel_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			}
		}

		?>

        <div class="wrap" style="margin-right: 20px;">
            <h1 class="wp-heading-inline"><?php _e( 'Hotel Booking Details', 'tourfic' ); ?></h1>
			<?php
			/**
			 * Before Tour booking details table hook
			 * @hooked tf_before_hotel_booking_details - 10
			 * @since 2.9.18
			 */
			do_action( 'tf_before_hotel_booking_details' );

			?>
            <hr class="wp-header-end">
			<?php
			/**
			 * Booking Data showing from tourfic table
			 * @since 2.9.26
			 */
			$hotel_order_results = new DBTFHOTELTable( $hotel_orders_result );
			$hotel_order_results->prepare_items();
			$hotel_order_results->display();
			?>
        </div>

	<?php }
}


/**
 * Apartment booking page callback function
 *
 * Display all the order details
 */
if ( ! function_exists( 'tf_apartment_booking_page_callback' ) ) {
	function tf_apartment_booking_page_callback() {
		/**
		 * Get current logged in user
		 */
		if ( file_exists( TF_INC_PATH . 'functions/class.tf_apartment.php' ) ) {
			require_once TF_INC_PATH . 'functions/class.tf_apartment.php';
		} else {
			tf_file_missing( TF_INC_PATH . 'functions/class.tf_apartment.php' );
		}

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

		if ( $current_user_role == 'administrator' ) {
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$tf_orders_select        = array(
					'select' => "*",
					'query'  => "post_type = 'apartment' ORDER BY order_id DESC"
				);
				$apartment_orders_result = tourfic_order_table_data( $tf_orders_select );
			} else {
				$tf_orders_select        = array(
					'select' => "*",
					'query'  => "post_type = 'apartment' ORDER BY order_id DESC LIMIT 15"
				);
				$apartment_orders_result = tourfic_order_table_data( $tf_orders_select );
			}
		}
		if ( $current_user_role == 'tf_vendor' ) {
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$tf_orders_select        = array(
					'select'    => "*",
					'post_type' => "apartment",
					'author'    => $current_user_id,
					'limit'     => ""
				);
				$apartment_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			} else {
				$tf_orders_select        = array(
					'select'    => "*",
					'post_type' => "apartment",
					'author'    => $current_user_id,
					'limit'     => "LIMIT 15"
				);
				$apartment_orders_result = tourfic_vendor_order_table_data( $tf_orders_select );
			}
		}
		?>

        <div class="wrap" style="margin-right: 20px;">
            <h1 class="wp-heading-inline"><?php _e( 'Apartment Booking Details', 'tourfic' ); ?></h1>
			<?php
			/**
			 * Before Tour booking details table hook
			 * @hooked tf_before_apartment_booking_details - 10
			 * @since 2.9.18
			 */
			do_action( 'tf_before_apartment_booking_details' );

			?>
            <hr class="wp-header-end">
			<?php
			/**
			 * Booking Data showing from tourfic table
			 * @since 2.9.26
			 */
			$apartment_order_results = new DBTFAPARTMENTTable( $apartment_orders_result );
			$apartment_order_results->prepare_items();
			$apartment_order_results->display();
			?>
        </div>

	<?php }
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
				( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
				array(
					$order_data['order_id'],
					sanitize_key( $order_data['post_id'] ),
					$order_data['post_type'],
					$order_data['room_number'],
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

add_action( 'admin_head', 'tf_booking_order_table_column' );
if ( ! function_exists( 'tf_booking_order_table_column' ) ) {
	function tf_booking_order_table_column() {
		$page = ( isset( $_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if ( 'tf_hotel_booking' != $page && 'tf_tours_booking' != $page ) {
			return;
		}

		echo '<style type="text/css">';
		echo '.wp-list-table .column-order_id { width: 90px; }';
		echo '.wp-list-table .column-oedit { width: 50px; }';
		echo '</style>';
	}
}