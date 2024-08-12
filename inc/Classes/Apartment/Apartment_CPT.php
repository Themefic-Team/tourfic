<?php

namespace Tourfic\Classes\Apartment;
use \Tourfic\Classes\Helper;
use \Tourfic\Admin\Menu_Icon;

defined( 'ABSPATH' ) || exit;
class Apartment_CPT extends \Tourfic\Core\Post_Type {

	use \Tourfic\Traits\Singleton;

	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->set_post_args( array(
			'name'          => esc_html__('Apartments', 'tourfic' ),
			'singular_name' => esc_html__('Apartment', 'tourfic' ),
			'slug'          => 'tf_apartment',
			'menu_icon'     => Menu_Icon::$apt_icon,
			'menu_position' => 26.4,
			'supports'      => apply_filters( 'tf_apartment_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
			'capability'    => array( 'tf_apartment', 'tf_apartments' ),
			'rewrite_slug'  => $this->get_apartment_slug(),
		))->set_tax_args( array(
			array(
				'name'          => esc_html__('Locations', 'tourfic' ),
				'singular_name' => esc_html__('Location', 'tourfic' ),
				'taxonomy'      => 'apartment_location',
				'rewrite_slug'  => apply_filters( 'tf_apartment_location_slug', 'apartment-location' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_apartment',
					'edit_terms'   => 'edit_tf_apartment',
				),
			),
			array(
				'name'          => esc_html__('Features', 'tourfic' ),
				'singular_name' => esc_html__('Feature', 'tourfic'),
				'taxonomy'      => 'apartment_feature',
				'rewrite_slug'  => apply_filters( 'tf_apartment_feature_slug', 'apartment-feature' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_apartment',
					'edit_terms'   => 'edit_tf_apartment',
				),
			),
			array(
				'name'          => esc_html__('Types', 'tourfic' ),
				'singular_name' => esc_html__('Type', 'tourfic' ),
				'taxonomy'      => 'apartment_type',
				'rewrite_slug'  => apply_filters( 'tf_apartment_type_slug', 'apartment-type' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_apartment',
					'edit_terms'   => 'edit_tf_apartment',
				),
			)
		));
		
		add_action( 'init', array( $this, 'tf_post_type_taxonomy_register' ) );
	}

	private function get_apartment_slug() {
		$tf_apartment_setting_permalink_slug = ! empty( Helper::tfopt( 'apartment-permalink-setting' ) ) ? Helper::tfopt( 'apartment-permalink-setting' ) : "apartments";

		update_option( "apartment_slug", $tf_apartment_setting_permalink_slug );

		return apply_filters( 'tf_apartment_slug', get_option( 'apartment_slug' ) );
	}

}
