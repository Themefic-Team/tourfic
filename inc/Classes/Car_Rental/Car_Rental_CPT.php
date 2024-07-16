<?php

namespace Tourfic\Classes\Car_Rental;
use \Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;
class Car_Rental_CPT extends \Tourfic\Classes\Post_Type {

	use \Tourfic\Traits\Singleton;

	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->set_post_args( array(
			'name'          => esc_html__('Car Rental', 'tourfic' ),
			'singular_name' => esc_html__('Car Rental', 'tourfic' ),
			'slug'          => 'tf_carrental',
			'menu_icon'     => 'dashicons-car',
			'menu_position' => 26.5,
			'supports'      => apply_filters( 'tf_carrental_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
			'capability'    => array( 'tf_carrental', 'tf_carrentals' ),
			'rewrite_slug'  => $this->get_carrental_slug(),
		))->set_tax_args( array(
			array(
				'name'          => esc_html__('Model', 'tourfic' ),
				'singular_name' => esc_html__('Model', 'tourfic' ),
				'taxonomy'      => 'carrental_model',
				'rewrite_slug'  => apply_filters( 'tf_carrental_model_slug', 'carrental-model' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
			),
			array(
				'name'          => esc_html__('Features', 'tourfic' ),
				'singular_name' => esc_html__('Features', 'tourfic' ),
				'taxonomy'      => 'carrental_features',
				'rewrite_slug'  => apply_filters( 'tf_carrental_features_slug', 'carrental-features' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
			),
			array(
				'name'          => esc_html__('Type', 'tourfic' ),
				'singular_name' => esc_html__('Type', 'tourfic' ),
				'taxonomy'      => 'carrental_type',
				'rewrite_slug'  => apply_filters( 'tf_carrental_type_slug', 'carrental-type' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
			)
		));
		
		add_action( 'init', array( $this, 'tf_post_type_taxonomy_register' ) );
	}

	private function get_carrental_slug() {
		$tf_carrental_setting_permalink_slug = ! empty( Helper::tfopt( 'carrental-permalink-setting' ) ) ? Helper::tfopt( 'carrental-permalink-setting' ) : "carrental";

		update_option( "carrental_slug", $tf_carrental_setting_permalink_slug );

		return apply_filters( 'tf_carrental_slug', get_option( 'carrental_slug' ) );
	}

}
