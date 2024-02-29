<?php
namespace Tourfic\Classes\Apartment;
defined( 'ABSPATH' ) || exit;

class Apartment_CPT extends \Tourfic\Classes\Post_Type {

    use \Tourfic\Traits\Singleton;
	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$tour_args = array(
			'name'          => 'Apartments',
			'singular_name' => 'Apartment',
			'slug'          => 'tf_apartment',
			'menu_icon'     => 'dashicons-admin-home',
			'menu_position' => 26.4,
            'supports'      => apply_filters( 'tf_apartment_supports', array(
                'title',
                'editor',
                'thumbnail',
                'comments',
                'author'
            ) ),
		);

        $tax_args = array(
            array(
                'name' => 'Locations',
                'singular_name' => 'Location',
                'taxonomy' => 'apartment_location',
            ),
            array(
                'name' => 'Features',
                'singular_name' => 'Feature',
                'taxonomy' => 'apartment_feature',
            ),
            array(
                'name' => 'Types',
                'singular_name' => 'Type',
                'taxonomy' => 'apartment_type',
            )
        );

		parent::__construct( $tour_args, $tax_args );


		add_action( 'init', array( $this, 'tf_cpt_taxonomy' ) );
		
	}

}
