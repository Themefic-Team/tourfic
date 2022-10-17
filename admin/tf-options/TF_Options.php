<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Options' ) ) {
	class TF_Options {

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
			//load files
			$this->load_files();

			//load metaboxes
			$this->load_metaboxes();
		}

		/**
		 * Load files
		 * @since 1.0.0
		 */
		public function load_files() {
			// Metaboxes Class
			require_once TF_ADMIN_PATH . 'tf-options/Classes/TF_Metabox.php';
			// Settings Class
			require_once TF_ADMIN_PATH . 'tf-options/Classes/TF_Settings.php';
		}

		/**
		 * Load metaboxes
		 * @since 1.0.0
		 */
		public function load_metaboxes() {
			$metaboxes = glob( TF_ADMIN_PATH . 'tf-options/metaboxes/*.php' );
			if ( ! empty( $metaboxes ) ) {
				foreach ( $metaboxes as $metabox ) {
					$metabox_name = basename( $metabox, '.php' );
					if ( ! class_exists( $metabox_name ) ) {
						require_once $metabox;
					}
				}
			}
		}

	}
}

TF_Options::instance();