<?php
namespace Tourfic\Classes;


defined( 'ABSPATH' ) || exit;

class Base {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Enquiry;
	use \Tourfic\Traits\Helpers;

	public function __construct() {
		$this->init();
	}

	public function init() {
		\Tourfic\Admin\Functions::instance();
		\Tourfic\Admin\TF_Options\TF_Options::instance();
		\Tourfic\Classes\Hotel\Hotel_CPT::instance();
		//\Tourfic\App\Apartment\Apartment::instance();
	}

	function init_hooks(){
		add_action( 'admin_menu', array($this, 'tf_add_enquiry_submenu') );
	}
}

