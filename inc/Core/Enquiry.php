<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

abstract class Enquiry {

	protected array $actions = [];
	protected array $args;
	protected array $settings;

	function __contruct( array $args ) {
		$this->args = $args;
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
	}

	abstract function add_submenu();
}