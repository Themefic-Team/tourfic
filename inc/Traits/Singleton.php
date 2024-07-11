<?php

namespace Tourfic\Traits;
defined( 'ABSPATH' ) || exit;

/**
 * Singleton trait
 * get instance
 *
 * @since 1.0.0
 */
trait Singleton {

	private static $instance;

	/**
	 * @return static
	 */
	public static function instance(...$args) {
		if(!self::$instance) {
			self::$instance = new self(...$args);
		}

		return self::$instance;
	}
}