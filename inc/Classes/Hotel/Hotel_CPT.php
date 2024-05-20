<?php

namespace Tourfic\Classes\Hotel;
use \Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

class Hotel_CPT extends \Tourfic\Classes\Post_Type {

	use \Tourfic\Traits\Singleton;

	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$hotel_args = array(
			'name'          => esc_html__('Hotels', 'tourfic'),
			'singular_name' => esc_html__('Hotel', 'tourfic'),
			'slug'          => 'tf_hotel',
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 26.2,
			'supports'      => apply_filters( 'tf_hotel_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
			'capability'    => array( 'tf_hotel', 'tf_hotels' ),
			'rewrite_slug'  => $this->get_hotel_slug(),
		);

		$tax_args = array(
			array(
				'name'          => esc_html__('Locations', 'tourfic'),
				'singular_name' => esc_html__('Location', 'tourfic'),
				'taxonomy'      => 'hotel_location',
				'rewrite_slug'  => apply_filters( 'tf_hotel_location_slug', 'hotel-location' ),
				'capability'    => array(
					'assign_terms' => 'edit_tf_hotel',
					'edit_terms'   => 'edit_tf_hotel',
				),
			),
			array(
				'name'          => esc_html__('Features', 'tourfic'),
				'singular_name' => esc_html__('Feature', 'tourfic'),
				'taxonomy'      => 'hotel_feature',
				'rewrite_slug'  => apply_filters( 'tf_hotel_feature_slug', 'hotel-feature' ),
				'capability'    => array(
					'assign_terms' => 'edit_tf_hotel',
					'edit_terms'   => 'edit_tf_hotel',
				),
			),
			array(
				'name'          => esc_html__('Types', 'tourfic'),
				'singular_name' => esc_html__('Type', 'tourfic'),
				'taxonomy'      => 'hotel_type',
				'rewrite_slug'  => apply_filters( 'tf_hotel_type_slug', 'hotel-type' ),
				'capability'    => array(
					'assign_terms' => 'edit_tf_hotel',
					'edit_terms'   => 'edit_tf_hotel',
				),
			)
		);

		parent::__construct( $hotel_args, $tax_args );

		add_action( 'init', array( $this, 'tf_post_type_taxonomy_register' ) );
	}

	private function get_hotel_slug() {
		$tf_hotel_setting_permalink_slug = ! empty( Helper::tfopt( 'hotel-permalink-setting' ) ) ? Helper::tfopt( 'hotel-permalink-setting' ) : "hotels";

		update_option( "hotel_slug", $tf_hotel_setting_permalink_slug );

		return apply_filters( 'tf_hotel_slug', get_option( "hotel_slug" ) );
	}

}
