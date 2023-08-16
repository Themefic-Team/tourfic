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
		if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
			$tours_orders_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY order_id DESC", 'tour' ), ARRAY_A );
		}else{
			$tours_orders_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY order_id DESC LIMIT 15", 'tour' ), ARRAY_A );
		}
	}
	if ( $current_user_role == 'tf_vendor' ) {
		if (function_exists('is_tf_pro') && is_tf_pro()) {
			$query = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE post_type = %s AND post_id IN (
					SELECT ID FROM {$wpdb->posts} WHERE post_author = %d
				) ORDER BY order_id DESC",
				'tour', $current_user_id
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE post_type = %s AND post_id IN (
					SELECT ID FROM {$wpdb->posts} WHERE post_author = %d
				) ORDER BY order_id DESC LIMIT 15",
				'tour', $current_user_id
			);
		}
		$tours_orders_result = $wpdb->get_results($query, ARRAY_A);
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

	global $wpdb;
	$table_name = $wpdb->prefix . 'tf_order_data';
	if ( $current_user_role == 'administrator' ) {
		if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
			$hotel_orders_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY order_id DESC", 'hotel' ), ARRAY_A );
		}else{
			$hotel_orders_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY order_id DESC LIMIT 15", 'hotel' ), ARRAY_A );
		}
	}
	if ( $current_user_role == 'tf_vendor' ) {
		if (function_exists('is_tf_pro') && is_tf_pro()) {
			$query = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE post_type = %s AND post_id IN (
					SELECT ID FROM {$wpdb->posts} WHERE post_author = %d
				) ORDER BY order_id DESC",
				'hotel', $current_user_id
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE post_type = %s AND post_id IN (
					SELECT ID FROM {$wpdb->posts} WHERE post_author = %d
				) ORDER BY order_id DESC LIMIT 15",
				'hotel', $current_user_id
			);
		}
		$hotel_orders_result = $wpdb->get_results($query, ARRAY_A);
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