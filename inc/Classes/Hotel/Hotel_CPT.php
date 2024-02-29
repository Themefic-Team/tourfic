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
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 26.2,
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
            ),
            array(
                'name' => 'Types',
                'singular_name' => 'Type',
                'taxonomy' => 'hotel_type',
            )
        );

		parent::__construct( $hotel_args, $tax_args );


		add_action( 'init', array( $this, 'tf_cpt_taxonomy' ) );
		
	}

}
