<?php
namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

class TF_API_Bootstrap {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		if ( ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) || class_exists( 'TF_FD_API_Routes' ) ) {
			return;
		}

		$this->load_api_classes();

		if ( class_exists( 'TF_API_Routes' ) ) {
			\TF_API_Routes::get_instance();
		}
	}

	private function load_api_classes() {
		$base_path = TF_INC_PATH . 'Classes/REST_API/';
		$files     = array(
			'TF_Rest_API.php',
			'TF_Tour_Rest_API.php',
			'TF_Apartment_Rest_API.php',
			'TF_Rental_Rest_API.php',
			'TF_Hotel_Rest_API.php',
			'TF_Room_Rest_API.php',
			'TF_Booking_Rest_API.php',
			'TF_Enquiry_Rest_API.php',
			'TF_User_Rest_API.php',
			'TF_API_Routes.php',
		);

		foreach ( $files as $file ) {
			$file_path = $base_path . $file;
			if ( file_exists( $file_path ) ) {
				require_once $file_path;
			}
		}
	}
}
