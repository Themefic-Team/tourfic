<?php
namespace Tourfic\Classes;


defined( 'ABSPATH' ) || exit;

class Base {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		$this->init();
	}

	public function init() {
		\Tourfic\Admin\TF_Options\TF_Options::instance();
	}
}

