<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

# Tour Price
if ( file_exists( TF_INC_PATH . 'classes/class-tour-price.php' ) ) {
	require_once TF_INC_PATH . 'classes/class-tour-price.php';
} else {
	tf_file_missing(TF_INC_PATH . 'classes/class-tour-price.php');
}

/**
 * TF Promo Banner Class 
 * @author Sydur Rahman
 */
if ( file_exists( TF_INC_PATH . 'classes/class-promo-notice.php' ) ) {
	require_once TF_INC_PATH . 'classes/class-promo-notice.php';
} else {
	tf_file_missing(TF_INC_PATH . 'classes/class-promo-notice.php');
}

/**
 * TF Backend Booking
 * @author Foysal
 */
if(is_admin()){
	if ( file_exists( TF_INC_PATH . 'backend-booking/TF_Hotel_Backend_Booking.php' ) ) {
		require_once TF_INC_PATH . 'backend-booking/TF_Hotel_Backend_Booking.php';
	} else {
		tf_file_missing(TF_INC_PATH . 'backend-booking/TF_Hotel_Backend_Booking.php');
	}

	if ( file_exists( TF_INC_PATH . 'backend-booking/TF_Tour_Backend_Booking.php' ) ) {
		require_once TF_INC_PATH . 'backend-booking/TF_Tour_Backend_Booking.php';
	} else {
		tf_file_missing(TF_INC_PATH . 'backend-booking/TF_Tour_Backend_Booking.php');
	}
	
	if ( file_exists( TF_INC_PATH . 'backend-booking/TF_Apartment_Backend_Booking.php' ) ) {
		require_once TF_INC_PATH . 'backend-booking/TF_Apartment_Backend_Booking.php';
	} else {
		tf_file_missing(TF_INC_PATH . 'backend-booking/TF_Apartment_Backend_Booking.php');
	}
}

/**
 * TF Demo Importer Class
 * @author Foysal
 */
if ( file_exists( TF_INC_PATH . 'classes/class-tf-demo-importer.php' ) ) {
	require_once TF_INC_PATH . 'classes/class-tf-demo-importer.php';
} else {
	tf_file_missing( TF_INC_PATH . 'classes/class-tf-demo-importer.php' );
}