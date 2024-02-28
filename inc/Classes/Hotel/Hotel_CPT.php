<?php
namespace Tourfic\Classes\Hotel;
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
			'name'          => 'Hotels',
			'singular_name' => 'Hotel',
			'slug'          => 'tf_hotel',
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 5,
            'supports'      => apply_filters( 'tf_hotel_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
		);

        $tax_args = array(
            array(
                'name' => 'Locations',
                'singular_name' => 'Location',
                'taxonomy' => 'hotel_location',
            ),
            array(
                'name' => 'Features',
                'singular_name' => 'Feature',
                'taxonomy' => 'hotel_feature',
            )
        );

		parent::__construct( $hotel_args, $tax_args );


		add_action( 'init', array( $this, 'tf_cpt_taxonomy' ) );
		
	}

}
