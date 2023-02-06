<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * TF Frontend Dashboard
 * @author Foysal
 */
if ( ! class_exists( 'TF_FD_Shortcode' ) ) {
	class TF_FD_Shortcode {

		/*
		 * instance
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			add_shortcode( 'tf_frontend_dashboard', array( $this, 'tf_fd_shortcode' ) );
		}

		public function tf_fd_shortcode( $atts ) {
			$atts = shortcode_atts( array(
				'page' => 'dashboard',
			), $atts, 'tf_fd' );

//			$tf_fd = TF_Frontend_Dashboard::get_instance();
//			$tf_fd->tf_fd_load_template( $atts['page'] );

			?>
            <div id="tf-dashboard"></div>
			<?php
		}
	}
}

TF_FD_Shortcode::get_instance();