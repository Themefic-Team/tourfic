<?php

namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

/**
 * Activator Class
 * @since 2.9.3
 * @author Foysal
 */
class Activator {

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
		add_action( 'init', array( $this, 'activate' ) );

		// add post state
		add_filter( 'display_post_states', array( $this, 'add_post_state' ), 10, 2 );

		// set page template
		add_filter( 'theme_page_templates', array( $this, 'set_page_template' ), 10, 4 );
		add_filter( 'page_template', array( $this, 'load_page_templates' ) );
	}

	/**
	 * Plugin activation hook
	 * @since 1.0.0
	 */
	public function activate() {
		// Create Tourfic Pages
		$this->create_pages();
		flush_rewrite_rules();
	}

	/**
	 * Create Tourfic Pages
	 * @since 1.0.0
	 */
	private function create_pages() {
		$pages = array(
			'search'             => array(
				'name'    => _x( 'tf-search', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF Search', 'Page title', 'tourfic' ),
				'content' => '',
			),
			'search_form'        => array(
				'name'    => _x( 'tf-search-form', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF Search Form', 'Page title', 'tourfic' ),
				'content' => "[tf_search_form style='default' type='all' fullwidth='true' title='' subtitle='' classes='' advanced='enabled']",
			),
			'wishlist'           => array(
				'name'    => _x( 'tf-wishlist', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF Wishlist', 'Page title', 'tourfic' ),
				'content' => '',
			),
			'login'              => array(
				'name'    => _x( 'tf-login', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF Login', 'Page title', 'tourfic' ),
				'content' => '',
				'pro'     => true,
			),
			'register'           => array(
				'name'    => _x( 'tf-register', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF Register', 'Page title', 'tourfic' ),
				'content' => '',
				'pro'     => true,
			),
			'email_verification' => array(
				'name'    => _x( 'tf-email-verification', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF Email Verification', 'Page title', 'tourfic' ),
				'content' => "Please don't edit this page or don't change title/slug. This page reserved for Tourfic Email Verification.",
				'pro'     => true,
			),
			'dashboard'          => array(
				'name'    => _x( 'tf-dashboard', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF Dashboard', 'Page title', 'tourfic' ),
				'content' => '',
				'pro'     => true,
			),
			'qr_code_scanner'    => array(
				'name'    => _x( 'tf-qr-code-scanner', 'Page slug', 'tourfic' ),
				'title'   => _x( 'TF QR Code Scanner', 'Page title', 'tourfic' ),
				'content' => '',
				'pro'     => true,
			),
		);

		foreach ( $pages as $key => $page ) {
			if ( ! empty( $page['pro'] ) && ! function_exists( 'is_tf_pro' ) ) {
				continue;
			}
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
		update_post_meta( $page_id, '_wp_page_template', $slug );

		return $page_id;
	}

	/**
	 * Set page template
	 * @since 1.0.0
	 */
	public function set_page_template( $templates, $wp_theme, $post, $post_type ) {
		$templates['tf-search']             = 'Tourfic - Search Results';
		$templates['tf-wishlist']           = 'Tourfic - Wishlist';
		$templates['tf-login']              = 'Tourfic - Login';
		$templates['tf-register']           = 'Tourfic - Register';
		$templates['tf-email-verification'] = 'Tourfic - Email Verification';
		$templates['tf-dashboard']          = 'Tourfic - Dashboard';
		$templates['tf-qr-code-scanner']    = 'Tourfic - QR Code Scanner';

		return $templates;
	}

	/**
	 * Load page template
	 * @since 1.0.0
	 */
	function load_page_templates( $page_template ) {

		if ( get_page_template_slug() == 'tf-search' ) {
			$theme_files     = TF_PATH . 'templates/common/search-results.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_PATH . 'templates/common/search-results.php';
			}
		}

		if ( get_page_template_slug() == 'tf-wishlist' ) {
			$theme_files     = TF_PATH . 'templates/common/tf-wishlist.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_PATH . 'templates/common/tf-wishlist.php';
			}
		}

		if ( get_page_template_slug() == 'tf-login' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$theme_files     = TF_PRO_INC_PATH . 'templates/tf-login.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_PRO_INC_PATH . 'templates/tf-login.php';
			}
		}

		if ( get_page_template_slug() == 'tf-register' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$theme_files     = TF_PRO_INC_PATH . 'templates/tf-register.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_PRO_INC_PATH . 'templates/tf-register.php';
			}
		}

		if ( get_page_template_slug() == 'tf-email-verification' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$theme_files     = TF_PRO_TEMP_PATH . '/email-verification.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_PRO_TEMP_PATH . '/email-verification.php';
			}
		}

		if ( get_page_template_slug() == 'tf-dashboard' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$theme_files     = TF_PRO_INC_PATH . 'frontend-dashboard/template-parts/page-templates/frontend-dashboard.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_PRO_INC_PATH . 'frontend-dashboard/template-parts/page-templates/frontend-dashboard.php';
			}
		}

		if ( get_page_template_slug() == 'tf-qr-code-scanner' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$theme_files     = TF_PRO_INC_PATH . 'templates/qr-code-scanner.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_PRO_INC_PATH . 'templates/qr-code-scanner.php';
			}
		}

		return $page_template;
	}

	public function add_post_state( $post_states, $post ) {
		if ( $post->ID == get_option( 'tf_search_page_id' ) ||
		     $post->ID == get_option( 'tf_search_form_page_id' ) ||
		     $post->ID == get_option( 'tf_wishlist_page_id' ) ||
		     $post->ID == get_option( 'tf_login_page_id' ) ||
		     $post->ID == get_option( 'tf_register_page_id' ) ||
		     $post->ID == get_option( 'tf_email_verification_page_id' ) ||
		     $post->ID == get_option( 'tf_dashboard_page_id' ) ||
		     $post->ID == get_option( 'tf_qr_code_scanner_page_id' )
		) {
			$post_states[] = '<div class="tf-post-states">' . esc_html__( 'Tourfic', 'tourfic' ) . '</div>';
		}

		return $post_states;
	}
}