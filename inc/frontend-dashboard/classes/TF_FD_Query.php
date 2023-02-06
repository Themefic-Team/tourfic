<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * TF Frontend Dashboard
 * @author Foysal
 */
if ( ! class_exists( 'TF_FD_Query' ) ) {
	class TF_FD_Query {

		/*
		 * instance
		 */
		private static $instance = null;
		public $query_vars = array();

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			add_action( 'init', array( $this, 'add_endpoints' ) );
//			if ( ! is_admin() ) {
				add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
				add_action( 'parse_request', array( $this, 'parse_request' ), 0 );
//			}
			$this->init_query_vars();
		}

		/**
		 * Init query vars by loading options.
		 */
		public function init_query_vars() {

			// Query vars to add to WP.
			$this->query_vars = apply_filters( 'tf_fd_query_vars', array(
				'tf-dashboard',
				'tf-hotel',
				'tf-hotel-list',
				'tf-hotel-add',
			) );
		}

		/**
		 * Get page title for an endpoint.
		 *
		 * @param string
		 *
		 * @return string
		 */
		public function get_endpoint_title( $endpoint ) {
			global $wp;

			switch ( $endpoint ) {
				case 'tf-dashboard' :
					$title = __( 'Dashboard', 'tourfic' );
					break;
				case 'tf-hotel' :
					$title = __( 'Hotel', 'tourfic' );
					break;
				case 'tf-hotel-list' :
					$title = __( 'Hotel List', 'tourfic' );
					break;
				case 'tf-hotel-add' :
					$title = __( 'Add Hotel', 'tourfic' );
					break;
				default :
					$title = apply_filters( 'tf_fd_endpoint_' . $endpoint . '_title', ucfirst( $endpoint ) );
					break;
			}

			$title = apply_filters( 'tf_fd_endpoint_title', $title, $endpoint );

			return $title;
		}

		/**
		 * Endpoint mask describing the places the endpoint should be added.
		 * @return int
		 * @since 1.0.0
		 */
		protected function get_endpoints_mask() {
			return EP_PAGES;
		}

		/**
		 * Add endpoints for query vars.
		 */
		public function add_endpoints() {
			$mask = $this->get_endpoints_mask();

			foreach ( $this->query_vars as $key => $var ) {
				if ( ! empty( $var ) ) {
					add_rewrite_endpoint( $var, $mask );
				}
			}
		}

		/**
		 * Add query vars.
		 *
		 * @access public
		 *
		 * @param array $vars
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			foreach ( $this->query_vars as $var ) {
				$vars[] = $var;
			}

			return $vars;
		}

		/**
		 * Get query vars.
		 *
		 * @return array
		 */
		public function get_query_vars() {
			return $this->query_vars;
		}

		/**
		 * Get query current active query var.
		 *
		 * @return string
		 */
		public function get_current_endpoint() {
			global $wp;
			foreach ( $this->get_query_vars() as $value ) {
				if ( isset( $wp->query_vars[ $value ] ) ) {
					return $value;
				}
			}

			return '';
		}

		/**
		 * Parse the request and look for query vars - endpoints may not be supported.
		 */
		public function parse_request() {
			global $wp;

			// Map query vars to their keys, or get them if endpoints are not supported
			foreach ( $this->query_vars as $var ) {
				if ( isset( $_GET[ $var ] ) ) {
					$wp->query_vars[ $var ] = wc_clean( $_GET[ $var ] );
				} elseif ( isset( $wp->query_vars[ $var ] ) ) {
					$wp->query_vars[ $var ] = $wp->query_vars[ $var ];
				}
			}
		}
	}
}