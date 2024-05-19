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