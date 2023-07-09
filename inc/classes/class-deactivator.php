<?php
defined( 'ABSPATH' ) || exit;
/**
 * Deactivator Class
 * @since 2.9.3
 * @author Foysal
 */
if ( ! class_exists( 'TF_Deactivator' ) ) {
	class TF_Deactivator {

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
				'search' => 'tf-search',
				'wishlist' => 'tf-wishlist',
				'login' => 'tf-login',
				'register' => 'tf-register'
			);
			foreach ( $pages as $key => $page ) {
				$page = get_page_by_path( $page );
				if ( $page ) {
					wp_delete_post( $page->ID, true );
					delete_option( 'tf_' . $key . '_page_id' );
				}
			}
		}
	}
}

TF_Deactivator::instance();