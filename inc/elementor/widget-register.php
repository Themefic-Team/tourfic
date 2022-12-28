<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Tourfic Widget Category in Elementor
 */
function tf_add_elementor_widget_categories( $elements_manager ) {

	$elements_manager->add_category(
		'tourfic',
		[
			'title' => esc_html__( 'Tourfic', 'tourfic' ),
			'icon' => 'fa fa-plug',
		]
	);

}
add_action( 'elementor/elements/categories_registered', 'tf_add_elementor_widget_categories' );


/**
 * Register Tourfic Elementor Widgets
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function tf_register_widget( $widgets_manager ) {

	
	require_once( __DIR__ . '/widgets/search-form-horizontal.php' );
	require_once( __DIR__ . '/widgets/wishlist.php' );
	require_once( __DIR__ . '/widgets/review-slider.php' );
	require_once( __DIR__ . '/widgets/recent-blog.php' );
	//Hotel
	require_once( __DIR__ . '/widgets/hotel-locations.php' );
	require_once( __DIR__ . '/widgets/recent-hotels-slider.php' );
	require_once( __DIR__ . '/widgets/hotel-grid-slider.php' );
	//Tour
	require_once( __DIR__ . '/widgets/tour-destinations.php' );
	require_once( __DIR__ . '/widgets/recent-tours-slider.php' );
	require_once( __DIR__ . '/widgets/tour-grid-slider.php' );
	//Apartment
	require_once( __DIR__ . '/widgets/apartment-locations.php' );
	require_once( __DIR__ . '/widgets/recent-apartments-slider.php' );
	require_once( __DIR__ . '/widgets/apartment-grid-slider.php' );

	$widgets_manager->register( new \TF_Search_horizontal() );
	$widgets_manager->register( new \TF_Wishlist() );
	$widgets_manager->register( new \TF_Reviews_Slider() );
	$widgets_manager->register( new \TF_Recent_Blog() );
	//Hotel
	$widgets_manager->register( new \TF_Hotel_Locations() );
	$widgets_manager->register( new \TF_Recent_Hotels_slider() );
	$widgets_manager->register( new \TF_Hotel_Grid_Slider() );
	//Tour
	$widgets_manager->register( new \TF_Tour_Destinations() );
	$widgets_manager->register( new \TF_Recent_Tours_slider() );
	$widgets_manager->register( new \TF_Tour_Grid_Slider() );
	//Apartment
	$widgets_manager->register( new \TF_Apartment_Locations() );
	$widgets_manager->register( new \TF_Recent_Apartments_slider() );
	$widgets_manager->register( new \TF_Apartment_Grid_Slider() );

}
add_action( 'elementor/widgets/register', 'tf_register_widget' );
?>