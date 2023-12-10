<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
/**
 * Base Class File
 */

/**
 * Activator Class
 * @author Foysal
 */
if ( file_exists( TF_INC_PATH . 'classes/class-activator.php' ) ) {
	require_once TF_INC_PATH . 'classes/class-activator.php';
} else {
	tf_file_missing(TF_INC_PATH . 'classes/class-activator.php');
}

/**
 * Deactivator Class
 * @author Foysal
 */
if ( file_exists( TF_INC_PATH . 'classes/class-deactivator.php' ) ) {
	require_once TF_INC_PATH . 'classes/class-deactivator.php';
} else {
	tf_file_missing(TF_INC_PATH . 'classes/class-deactivator.php');
}

# Tour Price
if ( file_exists( TF_INC_PATH . 'classes/class-tour-price.php' ) ) {
	require_once TF_INC_PATH . 'classes/class-tour-price.php';
} else {
	tf_file_missing(TF_INC_PATH . 'classes/class-tour-price.php');
}

/**
 * Setup Wizard Class
 * @author Foysal
 */
if(is_admin()){
	if ( file_exists( TF_INC_PATH . 'classes/class-setup-wizard.php' ) ) {
		require_once TF_INC_PATH . 'classes/class-setup-wizard.php';
	} else {
		tf_file_missing(TF_INC_PATH . 'classes/class-setup-wizard.php');
	}
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
}