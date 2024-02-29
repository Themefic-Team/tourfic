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
                'name' => 'Tour Destinations',
                'singular_name' => 'Tour Destinations',
                'taxonomy' => 'tour_destination',
            ),
            array(
                'name' => 'Tour Attractions',
                'singular_name' => 'Tour Attractions',
                'taxonomy' => 'tour_attraction',
            ),
            array(
                'name' => 'Tour Activities',
                'singular_name' => 'Tour Activity',
                'taxonomy' => 'tour_activities',
            ),
            array(
                'name' => 'Tour Features',
                'singular_name' => 'Tour Feature',
                'taxonomy' => 'tour_features',
            ),
            array(
                'name' => 'Tour Types',
                'singular_name' => 'Tour Type',
                'taxonomy' => 'tour_type',
            )
        );

		parent::__construct( $tour_args, $tax_args );


		add_action( 'init', array( $this, 'tf_cpt_taxonomy' ) );
		
	}

}
