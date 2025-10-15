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
    
        //Template Widgets
        if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
            //Archive
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Archive\Search_Form::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Archive\Listings::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Archive\Sidebar::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Archive\Banner::instance() );

            //Single
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Title::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Description::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Address::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Map::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Wishlist::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Share::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Thumbnail::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Gallery::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Gallery_Button::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Video_Button::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Booking_Form::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\FAQ::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Feature::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Amenities::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Nearby_Places::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Review::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Rooms::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Enquiry::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Terms_And_Conditions::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Sticky_Nav::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Tour_Information::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Tour_Info_Cards::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Highlights::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Included_Excluded::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Itinerary::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Tour_Contact_Information::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\House_Rules::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Host_Info::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Car_Info::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Car_Benefits::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Car_Driver_Info::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Car_Contact_Info::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Related_Post::instance() );
            $widgets_manager->register( \Tourfic\App\Widgets\Elementor\Widgets\Single\Tour_Price::instance() );
        }
    }

}