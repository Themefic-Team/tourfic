<?php
defined( 'ABSPATH' ) || exit;
/**
 * Activator Class
 * @since 2.9.3
 * @author Foysal
 */
if ( ! class_exists( 'TF_Activator' ) ) {
	class TF_Activator {

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
			register_activation_hook( TF_PATH . 'tourfic.php', array( $this, 'activate' ) );

			// add post state
			add_filter( 'display_post_states', array( $this, 'add_post_state' ), 10, 2 );

			// set page template
			add_filter( 'page_template', array( $this, 'set_page_template' ) );
		}

		/**
		 * Plugin activation hook
		 * @since 1.0.0
		 */
		public function activate() {
			// Create Tourfic Pages
			$this->create_pages();

		}

		/**
		 * Create Tourfic Pages
		 * @since 1.0.0
		 */
		private function create_pages() {
			$pages = array(
				'search'   => array(
					'name'    => _x( 'tf-search', 'Page slug', 'tourfic' ),
					'title'   => _x( 'TF Search', 'Page title', 'tourfic' ),
					'content' => '[' . apply_filters( 'tf_search_shortcode_tag', 'tf_search' ) . ']',
				),
				'wishlist' => array(
					'name'    => _x( 'tf-wishlist', 'Page slug', 'tourfic' ),
					'title'   => _x( 'TF Wishlist', 'Page title', 'tourfic' ),
					'content' => '[' . apply_filters( 'tf_wishlist_shortcode_tag', 'tf-wishlist' ) . ']',
				),
			);

			foreach ( $pages as $key => $page ) {
				$this->create_page( esc_sql( $page['name'] ), 'tf_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? $page['parent'] : '' );
			}
		}

		/**
		 * Create a page and store the ID in an option.
		 *
		 * @param mixed $slug Slug for the new page
		 * @param string $option Option name to store the page's ID
		 * @param string $page_title (default: '') Title for the new page
		 * @param string $page_content (default: '') Content for the new page
		 * @param int $post_parent (default: 0) Parent for the new page
		 *
		 * @return int page ID
		 */
		private function create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
			global $wpdb;

			$option_value = get_option( $option );

			if ( $option_value > 0 && get_post( $option_value ) ) {
				return - 1;
			}

			$page_found = null;

			if ( $slug ) {
				$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s LIMIT 1;", $slug ) );
			}

			if ( $page_found ) {
				if ( ! $option ) {
					return $page_found;
				}
				update_option( $option, $page_found );

				return $page_found;
			}

			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed',
			);
			$page_id   = wp_insert_post( $page_data );

			update_option( $option, $page_id );

			return $page_id;
		}

		/**
		 * Set page template
		 * @since 1.0.0
		 */
		public function set_page_template( $page_template ) {
			if ( is_page( 'tf-search' ) ) {
				$page_template = TF_PATH . 'templates/common/search-results.php';
			}

			return $page_template;
		}

		public function add_post_state( $post_states, $post ) {
			if ( $post->ID == get_option( 'tf_search_page_id' )  || $post->ID == get_option( 'tf_wishlist_page_id' ) ) {
				$post_states[] = '<div class="tf-post-states">' . __( 'Tourfic', 'tourfic' ) . '</div>';
			}

			return $post_states;
		}
	}
}

TF_Activator::instance();