<?php

namespace Tourfic\App\Widgets;

// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\App\Widgets\Elementor\TF_Widget_Register;

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
        TF_Widget_Register::instance();
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
            Tourfic_Ask_Question::instance(),
            Tourfic_Similar_Tours::instance(),
            TF_Hotel_Feature_Filter::instance(),
            TF_Hotel_Type_Filter::instance(),
            TF_Tour_Feature_Filter::instance(),
            TF_Tour_Attraction_Filter::instance(),
            TF_Tour_Activities_Filter::instance(),
            TF_Tour_Type_Filter::instance(),
            TF_Apartment_Features_Filter::instance(),
            TF_Apartment_Type_Filter::instance(),
            Tourfic_Price_Filter::instance()
        );
        foreach ( $custom_widgets as $key => $widget ) {
            register_widget( $widget );
        }

    }

}