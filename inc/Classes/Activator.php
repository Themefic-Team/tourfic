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
		add_filter( 'template_include', array( $this, 'load_page_templates'), 999 );
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
				'name'    => esc_html(_x( 'tf-search', 'Page slug', 'tourfic' )),
				'title'   => esc_html(_x( 'TF Search', 'Page title', 'tourfic' )),
				'content' => '',
			),
			'search_form'        => array(
				'name'    => esc_html(_x( 'tf-search-form', 'Page slug', 'tourfic' )),
				'title'   => esc_html(_x( 'TF Search Form', 'Page title', 'tourfic' )),
				'content' => "[tourfic_search_form style='default' type='all' fullwidth='true' title='' subtitle='' classes='' advanced='enabled']",
			),
			'wishlist'           => array(
				'name'    => esc_html(_x( 'tf-wishlist', 'Page slug', 'tourfic' )),
				'title'   => esc_html(_x( 'TF Wishlist', 'Page title', 'tourfic' )),
				'content' => '',
			),
		);
		$pages = apply_filters( 'tourfic_activation_pages', $pages );

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
			$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s LIMIT 1;", $slug ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
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

		return apply_filters( 'tourfic_page_templates', $templates, $wp_theme, $post, $post_type );
	}

	/**
	 * Load page template
	 * @since 1.0.0
	 */
	function load_page_templates( $page_template ) {

		if ( get_page_template_slug() == 'tf-search' ) {
			$theme_files     = TOURFIC_PATH . 'templates/common/search-results.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TOURFIC_PATH . 'templates/common/search-results.php';
			}
		}

		if ( get_page_template_slug() == 'tf-wishlist' ) {
			$theme_files     = TOURFIC_PATH . 'templates/common/tf-wishlist.php';
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TOURFIC_PATH . 'templates/common/tf-wishlist.php';
			}
		}

		$extension_template = apply_filters( 'tourfic_page_template_path', '', get_page_template_slug(), $page_template );
		if ( is_string( $extension_template ) && file_exists( $extension_template ) ) {
			return $extension_template;
		}

		return $page_template;
	}

	public function add_post_state( $post_states, $post ) {
		$page_options = apply_filters(
			'tourfic_page_option_names',
			array( 'tourfic_search_page_id', 'tf_search_form_page_id', 'tourfic_wishlist_page_id' )
		);
		$page_ids     = array_map( 'get_option', $page_options );

		if ( in_array( $post->ID, array_map( 'intval', $page_ids ), true ) ) {
			$post_states[] = '<div class="tf-post-states">' . esc_html__( 'Tourfic', 'tourfic' ) . '</div>';
		}

		return $post_states;
	}
}
