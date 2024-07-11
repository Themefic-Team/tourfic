<?php

namespace Tourfic\App\Widgets;

// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\App\Widgets\Elementor\Register;
use Tourfic\App\Widgets\TF_Widgets;

class TF_Widget_Base {

    use \Tourfic\Traits\Singleton;

    public function __construct() {
        add_action( 'init', array( $this, 'tf_add_elelmentor_addon' ) );
        add_action( 'widgets_init', array($this,  'tourfic_sidebar_widgets_init' ), 100 );
    }

    function tf_add_elelmentor_addon() {

        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        // Include Widget files
        Register::instance();
    }

    /**
 * Add Tourfic sidebar.
 */
    function tourfic_sidebar_widgets_init() {

        register_sidebar( array(
            'name'          => esc_html__( 'TOURFIC: Archive Sidebar', 'tourfic' ),
            'id'            => 'tf_archive_booking_sidebar',
            'description'   => esc_html__( 'Widgets in this area will be shown on tourfic archive/search page', 'tourfic' ),
            'before_widget' => '<div id="%1$s" class="tf_widget widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<div class="tf-widget-title"><span>',
            'after_title'   => '</span><i class="fa fa-angle-up"></i></div>',
        ) );

        register_sidebar( array(
            'name'          => esc_html__( 'Tourfic: Search Result Sidebar', 'tourfic' ),
            'id'            => 'tf_search_result',
            'description'   => esc_html__( 'Widgets in this area will be shown on tourfic search page', 'tourfic' ),
            'before_widget' => '<div id="%1$s" class="tf_widget widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<div class="tf-widget-title"><span>',
            'after_title'   => '</span><i class="fa fa-angle-up"></i></div>',
        ) );

        // Register Custom Widgets
        $custom_widgets = array(
            TF_Widgets\Ask_Question::instance(),
            TF_Widgets\Similar_Tours::instance(),
            TF_Widgets\Hotel_Feature_Filter::instance(),
            TF_Widgets\Hotel_Type_Filter::instance(),
            TF_Widgets\Tour_Feature_Filter::instance(),
            TF_Widgets\Tour_Attraction_Filter::instance(),
            TF_Widgets\Tour_Activities_Filter::instance(),
            TF_Widgets\Tour_Type_Filter::instance(),
            TF_Widgets\Apartment_Features_Filter::instance(),
            TF_Widgets\Apartment_Type_Filter::instance(),
            TF_Widgets\Price_Filter::instance()
        );
        foreach ( $custom_widgets as $key => $widget ) {
            register_widget( $widget );
        }
    }
}