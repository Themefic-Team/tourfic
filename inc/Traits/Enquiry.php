<?php

namespace Tourfic\Traits;

trait Enquiry {
    function tf_add_enquiry_submenu() {
		$current_user = wp_get_current_user();
		// get user role
		$current_user_role = ! empty( $current_user->roles[0] ) ? $current_user->roles[0] : '';
		if ( ! empty( $current_user_role ) && ( $current_user_role == 'administrator' ) ) {

			if ( $current_user_role == 'administrator' ) {
				// Tour enquiry
				add_submenu_page( 'edit.php?post_type=tf_tours', __( 'Tour Enquiry Details', 'tourfic' ), __( 'Enquiry Details', 'tourfic' ), 'edit_tf_tourss', 'tf_tours_enquiry', 'tf_tour_enquiry_page_callback' );

				// Hotel enquiry
				add_submenu_page( 'edit.php?post_type=tf_hotel', __( 'Hotel Enquiry Details', 'tourfic' ), __( 'Enquiry Details', 'tourfic' ), 'edit_tf_hotels', 'tf_hotel_enquiry', 'tf_hotel_enquiry_page_callback' );

				//Apartment enquiry
				add_submenu_page( 'edit.php?post_type=tf_apartment', __( 'Apartment Enquiry Details', 'tourfic' ), __( 'Enquiry Details', 'tourfic' ), 'edit_tf_apartments', 'tf_apartment_enquiry', 'tf_apartment_enquiry_page_callback' );
			}
			
		}
	}

    function tf_tour_enquiry_page_callback() {
		?>
        <div class="wrap" style="margin-right: 20px;">
            <h1 class="wp-heading-inline"><?php _e( 'Tour Enquiry Details', 'tourfic' ); ?></h1>

			<?php
			/**
			 * Before enquiry details table hook
			 * @hooked tf_before_tour_booking_details - 10
			 * @since 2.9.18
			 */
			do_action( 'tf_before_enquiry_details' );

			if ( file_exists( TF_INC_PATH . 'functions/class.tf_enquiry.php' ) ) {
				require_once TF_INC_PATH . 'functions/class.tf_enquiry.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'functions/class.tf_enquiry.php' );
			}
			$current_user = wp_get_current_user();
			// get user id
			$current_user_id = $current_user->ID;
			// get user role
			$current_user_role = $current_user->roles[0];
			global $wpdb;
			$table_name = $wpdb->prefix . 'tf_enquiry_data';

			if ( $current_user_role == 'administrator' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$tour_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC", 'tf_tours' ), ARRAY_A );
			} elseif ( $current_user_role == 'administrator' ) {
				$tour_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC LIMIT 15", 'tf_tours' ), ARRAY_A );
			}
			
			$tour_enquiry_results = new DBTFTable( $tour_enquiry_result );
			$tour_enquiry_results->prepare_items();
			$tour_enquiry_results->display();
			?>
        </div>
		<?php
	}

    function tf_hotel_enquiry_page_callback() {
		?>
        <div class="wrap" style="margin-right: 20px;">
            <h1 class="wp-heading-inline"><?php _e( 'Hotel Enquiry Details', 'tourfic' ); ?></h1>
			<?php
			/**
			 * Before enquiry details table hook
			 * @hooked tf_before_tour_booking_details - 10
			 * @since 2.9.18
			 */
			do_action( 'tf_before_enquiry_details' );
			if ( file_exists( TF_INC_PATH . 'functions/class.tf_enquiry.php' ) ) {
				require_once TF_INC_PATH . 'functions/class.tf_enquiry.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'functions/class.tf_enquiry.php' );
			}
			$current_user = wp_get_current_user();
			// get user id
			$current_user_id = $current_user->ID;
			// get user role
			$current_user_role = $current_user->roles[0];
			global $wpdb;
			$table_name = $wpdb->prefix . 'tf_enquiry_data';

			if ( $current_user_role == 'administrator' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC", 'tf_hotel' ), ARRAY_A );
			} elseif ( $current_user_role == 'administrator' ) {
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC LIMIT 15", 'tf_hotel' ), ARRAY_A );
			}

			$hotel_enquiry_results = new DBTFTable( $hotel_enquiry_result );
			$hotel_enquiry_results->prepare_items();
			$hotel_enquiry_results->display();
			?>
        </div>
		<?php
	}

    function tf_apartment_enquiry_page_callback() {
		?>
        <div class="wrap" style="margin-right: 20px;">
            <h1 class="wp-heading-inline"><?php _e( 'Apartment Enquiry Details', 'tourfic' ); ?></h1>
			<?php
			do_action( 'tf_before_enquiry_details' );
			if ( file_exists( TF_INC_PATH . 'functions/class.tf_enquiry.php' ) ) {
				require_once TF_INC_PATH . 'functions/class.tf_enquiry.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'functions/class.tf_enquiry.php' );
			}
			$current_user = wp_get_current_user();
			// get user id
			$current_user_id = $current_user->ID;
			// get user role
			$current_user_role = $current_user->roles[0];
			global $wpdb;
			$table_name = $wpdb->prefix . 'tf_enquiry_data';

			if ( $current_user_role == 'administrator' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$apartment_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC", 'tf_apartment' ), ARRAY_A );
			} elseif ( $current_user_role == 'administrator' ) {
				$apartment_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC LIMIT 15", 'tf_apartment' ), ARRAY_A );
			}

			$apartment_enquiry_results = new DBTFTable( $apartment_enquiry_result );
			$apartment_enquiry_results->prepare_items();
			$apartment_enquiry_results->display();
			?>
        </div>
		<?php
	}
}