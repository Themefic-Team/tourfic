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

			//enqueue scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'tf_options_enqueue_scripts' ) );
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

		/**
		 * Enqueue scripts
		 * @since 1.0.0
		 */
		public function tf_options_enqueue_scripts() {
			//Css
			wp_enqueue_style( 'tf-fontawesome', TF_ADMIN_URL . 'tf-options/assets/css/all.min.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-remixicon', TF_ADMIN_URL . 'tf-options/assets/css/remixicon.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-flatpickr', TF_ADMIN_URL . 'tf-options/assets/css/flatpickr.min.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-options', TF_ADMIN_URL . 'tf-options/assets/css/tf-options.css', array(), TOURFIC );
			//Js
			wp_enqueue_script( 'tf-flatpickr', TF_ADMIN_URL . 'tf-options/assets/js/flatpickr.min.js', array( 'jquery' ), TOURFIC, true );
			wp_enqueue_script( 'tf-options', TF_ADMIN_URL . 'tf-options/assets/js/tf-options.js', array( 'jquery' ), TOURFIC, true );
		}


		
	}
}

TF_Options::instance();