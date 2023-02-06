<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * TF Frontend Dashboard
 * @author Foysal
 */
if ( ! class_exists( 'TF_Frontend_Dashboard' ) ) {
	class TF_Frontend_Dashboard {

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
			define( 'TF_FD_PATH', TF_PATH . 'inc/frontend-dashboard/' );
			define( 'TF_FD_CLASSES_PATH', TF_PATH . 'inc/frontend-dashboard/classes/' );
			define( 'TF_FD_TEMPLATE_PATH', TF_PATH . 'inc/frontend-dashboard/template-parts/' );
			define( 'TF_FD_VIEWS_PATH', TF_PATH . 'inc/frontend-dashboard/views/' );

			$this->tf_fd_load_files();

			//wp topbar menu
			add_action( 'admin_bar_menu', array( $this, 'tf_add_topbar_menu' ), 999 );
			add_action( 'wp_enqueue_scripts', array( $this, 'tf_frontend_dashboard_enqueue_scripts' ), 9999 );

			register_activation_hook( TF_PATH . 'tourfic.php', array( $this, 'tf_create_frontend_dashboard_page' ) );
			register_deactivation_hook( TF_PATH . 'tourfic.php', array( $this, 'tf_delete_frontend_dashboard_page' ) );
			add_filter( 'display_post_states', array( $this, 'tf_add_post_state' ), 10, 2 );

		}

		/*
		 * Load files
		 * @author Foysal
		 */
		public function tf_fd_load_files() {
			require_once TF_FD_CLASSES_PATH . 'TF_FD_Shortcode.php';
		}

		/*
		 * Frontend Dashboard topbar menu
		 * @author Foysal
		 */
		public function tf_add_topbar_menu( $admin_bar ) {
			$args = array(
				'id'    => 'tf-frontend-dashboard',
				'title' => '<span class="ab-icon dashicons dashicons-admin-generic"></span><span class="ab-label">' . esc_html__( 'TF Dashboard', 'tourfic' ) . '</span>',
				'href'  => admin_url( 'admin.php?page=tf-frontend-dashboard' ),
				'meta'  => array(
					'title' => __( 'Tourfic', 'tourfic' ),
				),
			);
			$admin_bar->add_node( $args );
		}

		/**
		 * Create Frontend Dashboard page
		 * @author Foysal
		 */
		function tf_create_frontend_dashboard_page() {
			$tf_frontend_dashboard_page = get_option( 'tf_frontend_dashboard_page' );
			if ( empty( $tf_frontend_dashboard_page ) ) {
				$tf_frontend_dashboard_page = wp_insert_post( array(
					'post_title'     => __( 'TF Frontend Dashboard', 'tourfic' ),
					'post_content'   => '[tf_frontend_dashboard]',
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
					'post-name'      => 'tf-frontend-dashboard'
				) );
				update_option( 'tf_frontend_dashboard_page', $tf_frontend_dashboard_page );
				flush_rewrite_rules();
			}
		}

		/**
		 * Delete Frontend Dashboard page
		 * @author Foysal
		 */
		function tf_delete_frontend_dashboard_page() {
			$tf_frontend_dashboard_page = get_option( 'tf_frontend_dashboard_page' );
			if ( ! empty( $tf_frontend_dashboard_page ) ) {
				wp_delete_post( $tf_frontend_dashboard_page, true );
				delete_option( 'tf_frontend_dashboard_page' );
			}
		}

		/**
		 * Display post states in frontend dashboard page
		 * @author Foysal
		 */
		function tf_add_post_state( $post_states, $post ) {
			$tf_frontend_dashboard_page = get_option( 'tf_frontend_dashboard_page' );
			if ( $tf_frontend_dashboard_page == $post->ID ) {
				$post_states['tf_frontend_dashboard_page'] = '<div class="tf-post-states">' . __( 'Tourfic', 'tourfic' ) . '</div>';
			}

			return $post_states;
		}

		/*
		 * Frontend dashboard enqueue scripts
		 * @author Foysal
		 */
		public function tf_frontend_dashboard_enqueue_scripts() {
//			wp_enqueue_style( 'tf-fd-bootstrap', TF_FD_ASSETS_URL . '/css/bootstrap.min.css', null, TOURFIC );
//			wp_enqueue_style( 'tf-fd-icons', TF_FD_ASSETS_URL . '/css/icons.min.css', null, TOURFIC );
//			wp_enqueue_style( 'tf-fd-app', TF_FD_ASSETS_URL . '/css/app.min.css', null, TOURFIC );
//
//			wp_enqueue_script( 'tf-fd-bootstrap', TF_FD_ASSETS_URL . '/libs/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), TOURFIC, true );
//			wp_enqueue_script( 'tf-fd-metisMenu', TF_FD_ASSETS_URL . '/libs/metismenu/metisMenu.min.js', array( 'jquery' ), TOURFIC, true );
//			wp_enqueue_script( 'tf-fd-simplebar', TF_FD_ASSETS_URL . '/libs/simplebar/simplebar.min.js', array( 'jquery' ), TOURFIC, true );
//			wp_enqueue_script( 'tf-fd-apexcharts', 'https://cdn.jsdelivr.net/npm/apexcharts', array( 'jquery' ), TOURFIC, true );
//			wp_enqueue_script( 'tf-fd-app', TF_FD_ASSETS_URL . '/js/app.js', array( 'jquery' ), TOURFIC, true );
		}

		/**
		 * Get templates
		 * @param string $template_name
		 * @author Foysal
		 */
		function tf_get_fd_template( $template_name ) {
			$template_path = TF_FD_TEMPLATE_PATH . $template_name;
			if ( file_exists( $template_path ) ) {
				include $template_path;
			}
		}

		/**
		 * Get Views
		 * @param string $endpoint
		 * @author Foysal
		 */
		function tf_fd_views( $endpoint ) {
			$endpoint_path = TF_FD_VIEWS_PATH . $endpoint . '.php';
			if ( file_exists( $endpoint_path ) ) {
				include $endpoint_path;
			}
		}

		/**
		 * Get Menus
		 * @author Foysal
		 */
		function get_tf_fd_menus() {
			$tf_fd_menus = apply_filters( 'tf_fd_menus',
				array(
					'tf-dashboard' => array(
						'label'    => __( 'Dashboard', 'tourfic' ),
						'url'      => $this->get_tf_fd_page_url(),
						'icon'     => 'cube',
						'priority' => 5
					),
					'tf-hotel'     => array(
						'label'    => __( 'Hotel', 'tourfic' ),
						'url'      => '#',
						'icon'     => 'cube',
						'priority' => 10,
						'sub_menu' => array(
							'tf-hotel-list' => array(
								'label'    => __( 'Hotel List', 'tourfic' ),
								'url'      => $this->tf_get_endpoint_url('hotel-list', '', $this->get_tf_fd_page_url()),
								'icon'     => 'cube',
								'priority' => 5
							),
							'tf-hotel-add'  => array(
								'label'    => __( 'Add Hotel', 'tourfic' ),
								'url'      => $this->tf_get_endpoint_url('hotel-add', '', $this->get_tf_fd_page_url()),
								'icon'     => 'cube',
								'priority' => 10
							),
						)
					),
				)
			);

			uasort( $tf_fd_menus, array( &$this, 'tf_sort_by_priority' ) );

			return $tf_fd_menus;
		}

		/**
		 * Sorts array of custom fields by priority value.
		 * @param array $a
		 * @param array $b
		 * @return int
		 */
		function tf_sort_by_priority( $a, $b ) {
			if ( ! isset( $a['priority'] ) || ! isset( $b['priority'] ) || $a['priority'] === $b['priority'] ) {
				return 0;
			}

			return ( $a['priority'] < $b['priority'] ) ? - 1 : 1;
		}

		function get_tf_fd_page_url( $language_code = '' ) {
			$tf_frontend_dashboard_page = get_option( 'tf_frontend_dashboard_page' );
			if ( isset( $tf_frontend_dashboard_page ) && $tf_frontend_dashboard_page ) {
				if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
					if ( ! $language_code ) {
						global $sitepress;
						$language_code = $sitepress->get_current_language();
					}

					if ( $language_code ) {
						if ( defined( 'DOING_AJAX' ) ) {
							do_action( 'wpml_switch_language', $language_code );
						}

						$tf_fd_page = get_permalink( icl_object_id( $tf_frontend_dashboard_page, 'page', true, $language_code ) );
						$tf_fd_page = apply_filters( 'wpml_permalink', $tf_fd_page, $language_code );

						return $tf_fd_page;
					} else {
						return get_permalink( icl_object_id( $tf_frontend_dashboard_page, 'page', true ) );
					}
				} else {
					return get_permalink( $tf_frontend_dashboard_page );
				}
			}

			return false;
		}

		/**
		 * Get endpoint URL.
		 * Gets the URL for an endpoint, which varies depending on permalink settings.
		 *
		 * @param string $endpoint
		 * @param string $value
		 * @param string $permalink
		 *
		 * @return string
		 */
		function tf_get_endpoint_url( $endpoint, $value = '', $permalink = '', $lang_code = '' ) {
			global $post;
			if ( ! $permalink ) {
				$permalink = apply_filters( 'tf_get_base_permalink', get_permalink( $post ) );
			}

			$endpoint              = apply_filters( 'tf_dashboard_modified_endpoint_slug', $endpoint );

			if ( get_option( 'permalink_structure' ) ) {
				if ( strstr( $permalink, '?' ) ) {
					$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
					$permalink    = current( explode( '?', $permalink ) );
				} else {
					$query_string = '';
				}
				$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
			} else {
				$url = add_query_arg( $endpoint, $value, $permalink );
			}

			return apply_filters( 'tf_get_endpoint_url', $url, $endpoint, $value, $permalink );
		}
	}
}

TF_Frontend_Dashboard::get_instance();