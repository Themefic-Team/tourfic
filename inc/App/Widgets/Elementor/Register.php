<?php

namespace Tourfic\App\Widgets\Elementor;

// don't load directly
defined( 'ABSPATH' ) || exit;

class Register {

    use \Tourfic\Traits\Singleton;

    public function __construct() {
        add_action( 'elementor/elements/categories_registered', array( $this, 'tf_add_elementor_widget_categories' ) );
        add_action( 'elementor/widgets/register', array( $this, 'tf_register_widget' ) );
    }

    public function tf_add_elementor_widget_categories( $elements_manager ) {

        $elements_manager->add_category(
            'tourfic',
            [
                'title' => esc_html__( 'Tourfic', 'tourfic' ),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    public function tf_register_widget( $widgets_manager ) {
    
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Search_horizontal::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Wishlist::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Reviews_Slider::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Recent_Blog::instance() );
        //Hotel
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Hotel_Locations::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Recent_Hotels_slider::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Hotel_Grid_Slider::instance() );
        //Tour
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Tour_Destinations::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Recent_Tours_slider::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Tour_Grid_Slider::instance() );
        //Apartment
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Apartment_Locations::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Recent_Apartments_slider::instance() );
        $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\TF_Apartment_Grid_Slider::instance() );
    
    }

}