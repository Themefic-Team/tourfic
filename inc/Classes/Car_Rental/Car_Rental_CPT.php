<?php

namespace Tourfic\Classes\Car_Rental;
use \Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;
class Car_Rental_CPT extends \Tourfic\Core\Post_Type {

	use \Tourfic\Traits\Singleton;

	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->set_post_args( array(
			'name'          => esc_html__('Car Rentals', 'tourfic' ),
			'singular_name' => esc_html__('Car Rental', 'tourfic' ),
			'slug'          => 'tf_carrental',
			'menu_icon'     => 'dashicons-car',
			'menu_position' => 26.5,
			'supports'      => apply_filters( 'tf_carrental_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
			'capability'    => array( 'tf_carrental', 'tf_carrentals' ),
			'rewrite_slug'  => $this->get_carrental_slug(),
		))->set_tax_args( array(
			array(
				'name'          => esc_html__('Locations', 'tourfic' ),
				'singular_name' => esc_html__('Location', 'tourfic' ),
				'taxonomy'      => 'carrental_location',
				'rewrite_slug'  => apply_filters( 'tf_carrental_location_slug', 'carrental-location' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
				'show_in_menu' => true
			),
			array(
				'name'          => esc_html__('Brand / Make', 'tourfic' ),
				'singular_name' => esc_html__('Brand / Make', 'tourfic' ),
				'taxonomy'      => 'carrental_brand',
				'rewrite_slug'  => apply_filters( 'tf_carrental_brand_slug', 'carrental-brand' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
				'show_in_menu' => true
			),
			array(
				'name'          => esc_html__('Fuel Type', 'tourfic' ),
				'singular_name' => esc_html__('Fuel Type', 'tourfic' ),
				'taxonomy'      => 'carrental_fuel_type',
				'rewrite_slug'  => apply_filters( 'tf_carrental_fuel_type_slug', 'carrental-features' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
				'show_in_menu' => false
			),
			array(
				'name'          => esc_html__('Category', 'tourfic' ),
				'singular_name' => esc_html__('Category', 'tourfic' ),
				'taxonomy'      => 'carrental_category',
				'rewrite_slug'  => apply_filters( 'tf_carrental_category_slug', 'carrental-category' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
				'show_in_menu' => true
			),
			array(
				'name'          => esc_html__('Year', 'tourfic' ),
				'singular_name' => esc_html__('Year', 'tourfic' ),
				'taxonomy'      => 'carrental_engine_year',
				'rewrite_slug'  => apply_filters( 'tf_carrental_engine_year_slug', 'carrental-year' ),
				'capability'  => array(
					'assign_terms' => 'edit_tf_carrental',
					'edit_terms'   => 'edit_tf_carrental',
				),
				'show_in_menu' => false
			)
		));
		
		add_action( 'init', array( $this, 'tf_post_type_taxonomy_register' ) );
	}

	private function get_carrental_slug() {
		$tf_carrental_setting_permalink_slug = ! empty( Helper::tfopt( 'car-permalink-setting' ) ) ? Helper::tfopt( 'car-permalink-setting' ) : "cars";

		update_option( "car_slug", $tf_carrental_setting_permalink_slug );

		return apply_filters( 'tf_car_slug', get_option( 'car_slug' ) );
	}

}
