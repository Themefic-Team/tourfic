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
	require_once( __DIR__ . '/widgets/tourfic-tours.php' );
	require_once( __DIR__ . '/widgets/tour-destinations.php' );
	require_once( __DIR__ . '/widgets/hotel-locations.php' );

	$widgets_manager->register( new \TF_Search_horizontal() );
	$widgets_manager->register( new \TF_Tour_Destinations() );
	$widgets_manager->register( new \TF_Hotel_Locations() );
	$widgets_manager->register( new \TOURFIC_Slider() );

}
add_action( 'elementor/widgets/register', 'tf_register_widget' );
?>