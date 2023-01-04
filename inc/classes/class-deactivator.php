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
				'search'   => array(
					'name'    => _x( 'tf-search', 'Page slug', 'tourfic' ),
					'title'   => _x( 'TF Search', 'Page title', 'tourfic' ),
					'content' => '[' . apply_filters( 'tf_search_shortcode_tag', 'tf_search' ) . ']',
				),
				'wishlist' => array(
					'name'    => _x( 'tf-wishlist', 'Page slug', 'tourfic' ),
					'title'   => _x( 'TF Wishlist', 'Page title', 'tourfic' ),
					'content' => '[' . apply_filters( 'tf_wishlist_shortcode_tag', 'tf_wishlist' ) . ']',
				),
			);
			foreach ( $pages as $key => $page ) {
				$page = get_page_by_path( $page['name'] );
				if ( $page ) {
					wp_delete_post( $page->ID, true );
				}
			}
		}
	}
}

TF_Deactivator::instance();