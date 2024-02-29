<?php
namespace Tourfic\Classes\Tour;
defined( 'ABSPATH' ) || exit;

class Tour_CPT extends \Tourfic\Classes\Post_Type {

    use \Tourfic\Traits\Singleton;
	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$tour_args = array(
			'name'          => 'Tours',
			'singular_name' => 'Tour',
			'slug'          => 'tf_tours',
			'menu_icon'     => 'dashicons-location-alt',
			'menu_position' => 26.3,
            'supports'      => apply_filters( 'tf_tours_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
		);

        $tax_args = array(
            array(
                'name' => 'Destinations',
                'singular_name' => 'Destinations',
                'taxonomy' => 'tour_destination',
            ),
            array(
                'name' => 'Attractions',
                'singular_name' => 'Attractions',
                'taxonomy' => 'tour_attraction',
            ),
            array(
                'name' => 'Activities',
                'singular_name' => 'Activity',
                'taxonomy' => 'tour_activities',
            ),
            array(
                'name' => 'Features',
                'singular_name' => 'Feature',
                'taxonomy' => 'tour_features',
            ),
            array(
                'name' => 'Types',
                'singular_name' => 'Type',
                'taxonomy' => 'tour_type',
            )
        );

		parent::__construct( $tour_args, $tax_args );


		add_action( 'init', array( $this, 'tf_cpt_taxonomy' ) );
		
	}

}
