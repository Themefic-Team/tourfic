<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_Options {

	private static $instance = null;

	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {

	}

}

TF_Options::instance();