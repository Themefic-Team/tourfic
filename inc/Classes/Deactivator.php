<?php

namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

/**
 * Deactivator Class
 * @since 2.9.3
 * @author Foysal
 */
class Deactivator {

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
		register_deactivation_hook( TF_PATH . 'tourfic.php', array( $this, 'deactivate' ) );
	}

	/**
	 * Plugin deactivation hook
	 * @since 1.0.0
	 */
	public function deactivate() {
		// Delete Tourfic Pages
		$this->delete_pages();
	}

	/**
	 * Delete Tourfic Pages
	 * @since 1.0.0
	 */
	private function delete_pages() {
		$pages = array(
			'search',
			'wishlist',
			'login',
			'register',
			'email_verification',
			'dashboard',
			'qr_code_scanner',
		);
		foreach ( $pages as $page ) {
			if ( is_page( get_option( 'tf_' . $page . '_page_id' ) ) ) {
				wp_delete_post( get_option( 'tf_' . $page . '_page_id' ), true );
				delete_option( 'tf_' . $page . '_page_id' );
			}
		}
	}
}