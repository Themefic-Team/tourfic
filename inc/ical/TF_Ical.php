<?php
defined( 'ABSPATH' ) || exit;

/**
 * TF iCal sync
 * @since 2.9.26
 * @author Foysal
 */
if ( ! class_exists( 'TF_Ical' ) ) {
	class TF_Ical {

		private static $instance = null;

		/**
		 * Singleton instance
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			add_action('wp_ajax_tf_import_ical', array($this, 'tf_import_ical'));
		}

		public function tf_import_ical() {
			$response = array();
			$ical_url = $_POST['ical_url'];
			$ical_url = esc_url_raw( $ical_url );

			//add context to read file

			echo json_encode($response);
			wp_die();
		}
	}
}

TF_Hotel_Backend_Booking::instance();