<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Archive;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search Form Horizontal
 */
class Search_Form extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-search-form';
	}

	public function get_title() {
		return esc_html__( 'Archive Search Form', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-site-search';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'hotels',
            'tours',
            'apartments',
            'cars',
            'rentals',
            'services',
			'tourfic',
			'tf'
        ];
    }

	protected function register_controls() {

		$this->tf_content_layout_controls();
        $this->tf_search_field_controls();

		do_action( 'tf/search/before-style-controls', $this );
		$this->tf_style_general_controls();
		$this->tf_style_input_labels_controls();
		$this->tf_style_input_fields_controls();
		$this->tf_style_search_button_controls();
		do_action( 'tf/search/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_search_layouts',[
            'label' => esc_html__('Service & Layouts', 'tourfic'),
        ]);

        do_action( 'tf/search/before-layout/controls', $this );

        //service
		$this->add_control('service',[
            'type'     => Controls_Manager::SELECT,
            'label'    => esc_html__( 'Service', 'tourfic' ),
            'options'  => [
                'tf_hotel'     => esc_html__( 'Hotel', 'tourfic' ),
                'tf_tours'     => esc_html__( 'Tour', 'tourfic' ),
                'tf_apartment' => esc_html__( 'Apartment', 'tourfic' ),
                'tf_carrental' => esc_html__( 'Car', 'tourfic' ),
            ],
            'default'  => 'tf_hotel',
        ]);
		
		// Design options for Hotel
		$this->add_control('design_hotel',[
            'type'     => Controls_Manager::SELECT,
            'label'    => esc_html__( 'Design', 'tourfic' ),
            'options'  => [
                'design-1' => esc_html__( 'Design 1', 'tourfic' ),
                'design-2' => esc_html__( 'Design 2', 'tourfic' ),
                'design-3' => esc_html__( 'Design 3', 'tourfic' ),
                'default'  => esc_html__( 'Legacy', 'tourfic' ),
            ],
            'default'  => 'design-1',
            'condition' => [
                'service' => 'tf_hotel',
            ],
        ]);
		
		// Design options for Tour
		$this->add_control('design_tours',[
            'type'     => Controls_Manager::SELECT,
            'label'    => esc_html__( 'Design', 'tourfic' ),
            'options'  => [
                'design-1' => esc_html__( 'Design 1', 'tourfic' ),
                'design-2' => esc_html__( 'Design 2', 'tourfic' ),
                'design-3' => esc_html__( 'Design 3', 'tourfic' ),
                'default'  => esc_html__( 'Legacy', 'tourfic' ),
            ],
            'default'  => 'design-1',
            'condition' => [
                'service' => 'tf_tours',
            ],
        ]);
		
		// Design options for Apartment
		$this->add_control('design_apartment',[
            'type'     => Controls_Manager::SELECT,
            'label'    => esc_html__( 'Design', 'tourfic' ),
            'options'  => [
                'design-1' => esc_html__( 'Design 1', 'tourfic' ),
                'design-2' => esc_html__( 'Design 2', 'tourfic' ),
                'default'  => esc_html__( 'Legacy', 'tourfic' ),
            ],
            'default'  => 'design-1',
            'condition' => [
                'service' => 'tf_apartment',
            ],
        ]);
		
		// Design options for Car Rental
		$this->add_control('design_carrental',[
            'type'     => Controls_Manager::SELECT,
            'label'    => esc_html__( 'Design', 'tourfic' ),
            'options'  => [
                'design-1' => esc_html__( 'Design 1', 'tourfic' ),
            ],
            'default'  => 'design-1',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

	    do_action( 'tf/search/after-layout/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_search_field_controls(){
        $this->start_controls_section('tf_search_fields',[
            'label' => esc_html__('Search Fields', 'tourfic'),
        ]);

        //location//destination
        $this->add_control('location_head',[
            'label' => esc_html__('Location', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        $this->add_control('location_icon',[
            'label' => esc_html__('Location Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-map-marker-alt',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'location_icon_comp',
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        $this->add_control('loc_label', [
            'label' => esc_html__('Location Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => 'Location',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'design-3'],
                'tf_tours' => ['design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ]),
        ]);
        
        $this->add_control('loc_placeholder_text', [
            'label' => esc_html__('Placeholder Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Enter Location',
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        //adult
        $this->add_control('adult_head',[
            'label' => esc_html__('Adult', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        $this->add_control('adult_icon',[
            'label' => esc_html__('Adult Icon', 'tourfic'),
            'default' => [
                'value' => 'far fa-user',
                'library' => 'fa-regular',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'adult_icon_comp',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ]),
        ]);
        
        $this->add_control('adult_label', [
            'label' => esc_html__('Adult Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Adult',
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        //children
        $this->add_control('children_head',[
            'label' => esc_html__('Children', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        $this->add_control('children_icon',[
            'label' => esc_html__('Children Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-child',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'children_icon_comp',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ]),
        ]);
        
        $this->add_control('children_label', [
            'label' => esc_html__('Children Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Children',
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        //infant
        $this->add_control('infant_head',[
            'label' => esc_html__('Infant', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'conditions' => $this->tf_display_conditionally([
                'tf_apartment' => ['default'],
            ]),
        ]);

        $this->add_control('infant_icon', [
            'label' => esc_html__('Infant Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-child',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'infant_icon_comp',
            'conditions' => $this->tf_display_conditionally([
                'tf_apartment' => ['default'],
            ]),
        ]);
        
        $this->add_control('infant_label', [
            'label' => esc_html__('Infant Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Infant',
            'conditions' => $this->tf_display_conditionally([
                'tf_apartment' => ['default'],
            ]),
        ]);

        //room
        $this->add_control('room_head',[
            'label' => esc_html__('Room', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'condition' => [
                'service' => 'tf_hotel',
            ],
        ]);

        $this->add_control('room_icon',[
            'label' => esc_html__('Room Icon', 'tourfic'),
            'default' => [
                'value' => 'fa fa-building',
                'library' => 'fa',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'room_icon_comp',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
            ]),
        ]);
        
        $this->add_control('room_label', [
            'label' => esc_html__('Room Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Room',
            'condition' => [
                'service' => 'tf_hotel',
            ],
        ]);

        //Selector
        $this->add_control('selector_head',[
            'label' => esc_html__('Selector', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'conditions' => $this->tf_display_conditionally([
                'tf_carrental!' => ['design-1'],
            ]),
        ]);

        $this->add_control('selector_icon',[
            'label' => esc_html__('Selector Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-users',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'selector_icon_comp',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
        ]);
        
        $this->add_control('selector_label', [
            'label' => esc_html__('Selector Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Guests & rooms',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'design-3'],
                'tf_tours' => ['design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2']
            ]),
        ]);

        //date
        $this->add_control('date_head',[
            'label' => esc_html__('Date', 'tourfic'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('date_icon',[
            'label' => esc_html__('Date Icon', 'tourfic'),
            'default' => [
                'value' => 'far fa-calendar-alt',
                'library' => 'fa-regular',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'date_icon_comp',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-3', 'default'],
                'tf_tours' => ['design-1', 'design-3', 'default'],
                'tf_apartment' => ['design-2', 'default'],
            ]),
        ]);
        
        $this->add_control('date_label', [
            'label' => esc_html__('Date Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Date',
            'conditions' => $this->tf_display_conditionally([
                'tf_tours' => ['design-2', 'design-3'],
            ]),
        ]);
        
        $this->add_control('checkin_label', [
            'label' => esc_html__('Checkin Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Check in',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2']
            ]),
        ]);
        
        $this->add_control('checkout_label', [
            'label' => esc_html__('Checkout Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Check out',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2']
            ]),
        ]);

        $this->add_control('date_placeholder_text', [
            'label' => esc_html__('Placeholder Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Select Date',
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-3', 'default'],
                'tf_tours' => ['design-1', 'design-3', 'default'],
                'tf_apartment' => ['design-2', 'default']
            ]),
        ]);

        /* Car Controls */
        //Pick-up
        $this->add_control('pickup_head',[
            'label' => esc_html__('Pick-up Location', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('pickup_location_icon',[
            'label' => esc_html__('Pick-up Location Icon', 'tourfic'),
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'pickup_location_icon_comp',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('pickup_loc_label', [
            'label' => esc_html__('Pick-up Location Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => 'Pick-up',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        $this->add_control('pickup_loc_placeholder_text', [
            'label' => esc_html__('Pick-up Placeholder Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Pick Up Location',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        //Drop-off
        $this->add_control('dropoff_head',[
            'label' => esc_html__('Drop-off Location', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('dropoff_location_icon',[
            'label' => esc_html__('Drop-off Location Icon', 'tourfic'),
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'dropoff_location_icon_comp',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('dropoff_loc_label', [
            'label' => esc_html__('Drop-off Location Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => 'Drop-off',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        $this->add_control('dropoff_loc_placeholder_text', [
            'label' => esc_html__('Drop-off Placeholder Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Drop Off Location',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        //Pick-up date & time
        $this->add_control('pickup_date_head',[
            'label' => esc_html__('Pick-up Date & Time', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('pickup_date_icon',[
            'label' => esc_html__('Pick-up Date Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-calendar-alt',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'pickup_date_icon_comp',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        $this->add_control('pickup_date_label', [
            'label' => esc_html__('Pick-up Date Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Pick-up Date',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('pickup_date_placeholder_text', [
            'label' => esc_html__('Pick-up Date Placeholder Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Pick Up Date',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('pickup_time_icon',[
            'label' => esc_html__('Pick-up Time Icon', 'tourfic'),
            'default' => [
                'value' => 'far fa-clock',
                'library' => 'fa-regular',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'pickup_time_icon_comp',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        $this->add_control('pickup_time_label', [
            'label' => esc_html__('Pick-up Time Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Time',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        //Drop-off date & time
        $this->add_control('dropoff_date_head',[
            'label' => esc_html__('Drop-off Date & Time', 'tourfic'),
            'type' => Controls_Manager::HEADING,
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('dropoff_date_icon',[
            'label' => esc_html__('Drop-off Date Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-calendar-alt',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'dropoff_date_icon_comp',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        $this->add_control('dropoff_date_label', [
            'label' => esc_html__('Drop-off Date Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Drop-off Date',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('dropoff_date_placeholder_text', [
            'label' => esc_html__('Drop-off Date Placeholder Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Drop Off Date',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('dropoff_time_icon',[
            'label' => esc_html__('Drop-off Time Icon', 'tourfic'),
            'default' => [
                'value' => 'far fa-clock',
                'library' => 'fa-regular',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'dropoff_time_icon_comp',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);
        
        $this->add_control('dropoff_time_label', [
            'label' => esc_html__('Drop-off Time Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Time',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('return_same_location_text', [
            'label' => esc_html__('Return in Same Location Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Return in the same location',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('search_icon',[
            'label' => esc_html__('Search Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-search',
                'library' => 'fas',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'search_icon_comp',
            'condition' => [
                'service' => 'tf_carrental',
            ],
        ]);

        $this->add_control('btn_text', [
            'label' => esc_html__('Button Text', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default'   => 'Check Availability',
        ]);

	    do_action( 'tf/search/fields/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_style_general_controls() {
		$this->start_controls_section( 'search_style_general', [
			'label' => esc_html__( 'General', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		$this->add_responsive_control( "tf_form_wrap_width", [
			'label'           => esc_html__( 'Width', 'tourfic' ),
			'type'            => Controls_Manager::SLIDER,
			'size_units'      => [
				'px',
				'%',
			],
			'range'           => [
				'px'  => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'   => [
					'min' => 0,
					'max' => 100,
				],
			],
			'desktop_default' => [
				'unit' => '%',
				'size' => 100,
			],
			'selectors'       => [
				"{{WRAPPER}} .tf_archive_search_result" => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( "tf_form_wrap_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_archive_search_result" => $this->tf_apply_dim( 'padding' ),
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "tf_form_wrap_border",
			'selector'  => "{{WRAPPER}} .tf_archive_search_result",
		] );
		$this->add_control( "tf_form_wrap_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_archive_search_result" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
        $this->add_control( 'tf_form_wrap_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_archive_search_result" => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'label'    => esc_html__( 'Form Shadow', 'tourfic' ),
			'name'     => 'tf_form_wrap_shadow',
			'selector' => "{{WRAPPER}} .tf_archive_search_result",
			'exclude'  => [
				'box_shadow_position',
			],
		] );
		$this->end_controls_section();
	}

    protected function tf_style_input_labels_controls() {
		$this->start_controls_section( 'section_style_form_labels', [
			'label' => esc_html__( 'Form Labels', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Label Typography', 'tourfic' ),
			'name'     => "tf_label_typography",
			'selector' => "{{WRAPPER}} .tf-field .acr-label, {{WRAPPER}} span.tf-booking-form-title, {{WRAPPER}} .tf-search-field-label, {{WRAPPER}} .tf-select-date .info-select label, {{WRAPPER}} .tf-driver-location ul li label",
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
                'tf_carrental' => ['design-1'],
            ]),
		]);

		$this->add_control( 'tf_input_field_color', [
			'label'     => esc_html__( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-field-group .tf-field" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf_acrselection .acr-select input[type=number]" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf_acrselection .acr-inc" => 'color: {{VALUE}}; border-color: {{VALUE}};',
				"{{WRAPPER}} .tf_acrselection .acr-dec" => 'color: {{VALUE}}; border-color: {{VALUE}};',
				"{{WRAPPER}} span.tf-booking-form-title" => 'color: {{VALUE}};', //design-2
				"{{WRAPPER}} .tf-search-field-label" => 'color: {{VALUE}};', //design-3
				"{{WRAPPER}} .tf_form-inner select" => 'color: {{VALUE}};', //default
				"{{WRAPPER}} .tf-select-date .info-select label" => 'color: {{VALUE}};', //car design-1
				"{{WRAPPER}} .tf-driver-location ul li label" => 'color: {{VALUE}};', //car design-1
				"{{WRAPPER}} .tf-driver-location ul li label .tf-checkmark" => 'border-color: {{VALUE}};', //car design-1
			],
		] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Placeholder Typography', 'tourfic' ),
			'name'     => "tf_placeholder_typography",
			'selector' => "{{WRAPPER}} .tf-booking-date-wrap span, 
                            {{WRAPPER}} span.tf-booking-date, 
                            {{WRAPPER}} .tf-booking-form .tf-booking-form-fields .tf-booking-form-guest-and-room .tf-booking-form-guest-and-room-inner .tf-booking-guest-and-room-wrap.tf-archive-guest-info span, 
                            {{WRAPPER}} .tf-booking-guest-and-room-wrap, 
                            {{WRAPPER}} .tf-search-input, 
                            {{WRAPPER}} .tf-archive-guest-info",
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'design-3'],
                'tf_tours' => ['design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ]),
		] );
		
        $this->add_control( 'tf_input_field_placeholder_color', [
			'label'     => esc_html__( 'Placeholder Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-field-group input.tf-field::placeholder" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-booking-location-wrap input.tf-field::placeholder, {{WRAPPER}} .tf-booking-date-wrap span, {{WRAPPER}} .tf-booking-guest-and-room-wrap, {{WRAPPER}} .tf-booking-guest-and-room-wrap span" => 'color: {{VALUE}} !important;', //design-2
				"{{WRAPPER}} .tf-booking-date-wrap svg path, {{WRAPPER}} .tf-booking-guest-and-room-wrap svg path" => 'fill: {{VALUE}} !important;', //design-2
				"{{WRAPPER}} .tf-search-field .tf-search-input::placeholder, {{WRAPPER}} .tf-archive-guest-info" => 'color: {{VALUE}} !important;', //design-3
				"{{WRAPPER}} .tf_form-inner input[type=text]::placeholder" => 'color: {{VALUE}} !important;', //legacy
				"{{WRAPPER}} .tf-select-date .info-select input[type=text]::placeholder" => 'color: {{VALUE}} !important;', //legacy
			],
		] );

		$this->add_responsive_control( "tf_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-field .acr-label i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-field-group i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-field .acr-label svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-field-group svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-booking-location-wrap i" => 'font-size: {{SIZE}}{{UNIT}}', //design-2
				"{{WRAPPER}} .tf-booking-location-wrap svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}', //design-2
				"{{WRAPPER}} .tf-search-field-icon i" => 'font-size: {{SIZE}}{{UNIT}}', //design-3
				"{{WRAPPER}} .tf-search-field-icon svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}', //design-3
				"{{WRAPPER}} .tf_form-inner i" => 'font-size: {{SIZE}}{{UNIT}}', //design-3
				"{{WRAPPER}} .tf_form-inner svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}', //design-3
				"{{WRAPPER}} .tf-date-single-select .tf-select-date i" => 'font-size: {{SIZE}}{{UNIT}}', //design-3
				"{{WRAPPER}} .tf-date-single-select .tf-select-date svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}', //design-3
			],
		] );

		$this->add_control( "tf_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-field .acr-label i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-field-group i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-field .acr-label svg path" => 'fill: {{VALUE}}',
				"{{WRAPPER}} .tf-field-group svg path" => 'fill: {{VALUE}}',
				"{{WRAPPER}} .tf-booking-location-wrap i" => 'color: {{VALUE}}', //design-2
				"{{WRAPPER}} .tf-booking-location-wrap svg path" => 'fill: {{VALUE}}', //design-2
				"{{WRAPPER}} .tf-search-field-icon i" => 'color: {{VALUE}}', //design-3
				"{{WRAPPER}} .tf-search-field-icon svg path" => 'fill: {{VALUE}}', //design-3
				"{{WRAPPER}} .tf_form-inner i" => 'color: {{VALUE}}', //design-3
				"{{WRAPPER}} .tf_form-inner svg path" => 'fill: {{VALUE}}', //design-3
				"{{WRAPPER}} .tf-date-single-select .tf-select-date i" => 'color: {{VALUE}}', //design-3
				"{{WRAPPER}} .tf-date-single-select .tf-select-date svg path" => 'fill: {{VALUE}}', //design-3
			],
		] );

		$this->add_responsive_control( "tc_icon_gap", [
			'label'     => esc_html__( 'Icon Gap', 'tourfic' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'default'   => [
				'unit' => 'px',
				'size' => 5,
			],
			'selectors' => [
				"{{WRAPPER}} .tf-field .acr-label i" => 'margin-right: {{SIZE}}px;',
				"{{WRAPPER}} .tf-field-group i" => 'margin-right: {{SIZE}}px;',
				"{{WRAPPER}} .tf-field .acr-label svg" => 'margin-right: {{SIZE}}px;',
				"{{WRAPPER}} .tf-field-group svg" => 'margin-right: {{SIZE}}px;',
				"{{WRAPPER}} .tf-booking-location-wrap i" => 'margin-right: {{SIZE}}px;',
				"{{WRAPPER}} .tf-booking-location-wrap svg" => 'margin-right: {{SIZE}}px;',
				"{{WRAPPER}} .tf-search-fields .tf-search-field" => 'gap: {{SIZE}}px;',
				"{{WRAPPER}} .tf_form-inner" => 'gap: {{SIZE}}px;',
				"{{WRAPPER}} .tf-date-single-select .tf-select-date .tf-flex-gap-4" => 'gap: {{SIZE}}px;',
			],
		] );

		$this->end_controls_section();
	}

    protected function tf_style_input_fields_controls() {
		$this->start_controls_section( 'section_style_form_fields', [
			'label' => esc_html__( 'Form Fields', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		
		$this->add_control( 'tf_input_wrapper_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Input Wrapper', 'tourfic' ),
            'condition' => [
                'service' => 'tf_carrental',
            ],
		] );
		$this->add_responsive_control( "tf_input_wrap_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-date-select-box .tf-date-single-select" => $this->tf_apply_dim( 'padding' ),
			],
            'condition' => [
                'service' => 'tf_carrental',
            ],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "tf_input_wrapper_border",
			'selector'  => "{{WRAPPER}} .tf-date-select-box .tf-date-single-select",
            'condition' => [
                'service' => 'tf_carrental',
            ],
		] );
		$this->add_control( "tf_input_wrapper_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-date-select-box .tf-date-single-select" => $this->tf_apply_dim( 'border-radius' ),
			],
            'condition' => [
                'service' => 'tf_carrental',
            ],
		] );
        $this->add_control( 'tf_input_wrapper_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-date-select-box .tf-date-single-select" => 'background-color: {{VALUE}};',
			],
            'condition' => [
                'service' => 'tf_carrental',
            ],
		]);
		
		$this->add_control( 'tf_form_input_fields_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Form Input Fields', 'tourfic' ),
		] );

        $this->start_controls_tabs( "tabs_form_fields_padding" );
		$this->start_controls_tab( "tab_form_field_input_padding", [
			'label' => esc_html__( 'Input', 'tourfic' ),
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1'],
                'tf_tours' => ['design-1'],
                'tf_apartment' => ['default']
            ]),
		] );
		$this->add_responsive_control( "tf_form_field_input_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-field-group input.tf-field" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-booking-form-fields .tf-booking-form-location .tf-booking-location-wrap input" => $this->tf_apply_dim( 'padding' ), //design-2
				"{{WRAPPER}} .tf-search-field .tf-search-input" => $this->tf_apply_dim( 'padding' ), //design-3
				"{{WRAPPER}} .tf_form-row .tf_form-inner" => $this->tf_apply_dim( 'padding' ), //default
				"{{WRAPPER}} .tf-select-date .info-select input" => $this->tf_apply_dim( 'padding' ), //car design-1
			],
		] );
        $this->end_controls_tab();

		$this->start_controls_tab( "tab_form_field_selector_padding", [
			'label' => esc_html__( 'Selector', 'tourfic' ),
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1'],
                'tf_tours' => ['design-1'],
                'tf_apartment' => ['default']
            ]),
		] );
        $this->add_responsive_control( "tf_form_field_selector_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-field-group.tf_acrselection .tf-field" => $this->tf_apply_dim( 'padding' ),
			],
            'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1'],
                'tf_tours' => ['design-1'],
                'tf_apartment' => ['default']
            ]),
		] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->add_control( 'tf_field_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-field-group .tf-field" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-booking-form-fields .tf-booking-form-location .tf-booking-location-wrap" => 'background-color: {{VALUE}};', //design-2
				"{{WRAPPER}} .tf-search-field .tf-search-input" => 'background-color: {{VALUE}};', //design-3
				"{{WRAPPER}} .tf_form-row .tf_form-inner" => 'background-color: {{VALUE}};', //default
				"{{WRAPPER}} .tf_form-row .tf_form-inner select option" => 'background-color: {{VALUE}};', //default
				"{{WRAPPER}} .tf-select-date .info-select input" => 'background-color: {{VALUE}};', //car design-1
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "tf_field_border",
			'selector' => "{{WRAPPER}} .tf-field-group .tf-field, {{WRAPPER}} .tf-booking-form-fields .tf-booking-form-location .tf-booking-location-wrap, {{WRAPPER}} .tf-search-field .tf-search-input, {{WRAPPER}} .tf_form-row .tf_form-inner, {{WRAPPER}} .tf-select-date .info-select input",
		] );
		$this->add_control( "tf_field_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-field-group .tf-field" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-booking-form-fields .tf-booking-form-location .tf-booking-location-wrap" => $this->tf_apply_dim( 'border-radius' ), //design-2
				"{{WRAPPER}} .tf-search-field .tf-search-input" => $this->tf_apply_dim( 'border-radius' ), //design-3
				"{{WRAPPER}} .tf_form-row .tf_form-inner" => $this->tf_apply_dim( 'border-radius' ), //default
				"{{WRAPPER}} .tf-select-date .info-select input" => $this->tf_apply_dim( 'border-radius' ), //car design-1
			],
		] );
		$this->end_controls_section();
	}

    protected function tf_style_search_button_controls() {
		$this->start_controls_section( "section_style_search_btn", [
			'label'      => esc_html__( 'Search Button', 'tourfic' ),
			'tab'        => Controls_Manager::TAB_STYLE,
		] );
		
		$this->add_responsive_control( "search_btn_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => $this->tf_apply_dim( 'margin' ),
			],
		] );
		$this->add_responsive_control( "search_btn_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => $this->tf_apply_dim( 'padding' ),
			],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => "search_btn_typography",
			'selector' => "{{WRAPPER}} .tf_btn",
		] );
		
		$this->add_control( "tabs_search_btn_colors_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => esc_html__( 'Colors & Border', 'tourfic' ),
			'separator' => 'before',
		] );

		$this->start_controls_tabs( "tabs_search_btn_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_search_btn_normal", [
			'label' => esc_html__( 'Normal', 'tourfic' ),
		] );
		$this->add_control( "btn_color", [
			'label'     => esc_html__( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-submit-button button i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-submit-button button svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( 'btn_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "btn_border",
			'selector' => "{{WRAPPER}} .tf_btn",
		] );
		$this->add_control( "btn_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_search_button_hover", [
			'label' => esc_html__( 'Hover', 'tourfic' ),
		] );
		$this->add_control( "button_color_hover", [
			'label'     => esc_html__( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-submit-button button:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-submit-button button:hover svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( 'btn_bg_color_hover', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_control( 'btn_border_color_hover', [
			'label'     => esc_html__( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'border-color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Button tabs--------*/

		$this->add_responsive_control( "search_btn_width", [
			'label'      => esc_html__( 'Button width', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 800,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => 'width: {{SIZE}}{{UNIT}};',
			],
			'separator'  => 'before',
		] );
		$this->add_responsive_control( "search_btn_height", [
			'label'      => esc_html__( 'Button Height', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 500,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings           = $this->get_settings_for_display();
		$service            = !empty( $settings['service'] ) ? $settings['service'] : 'tf_hotel';
		$design_hotel       = !empty( $settings['design_hotel'] ) ? $settings['design_hotel'] : 'design-1';
		$design_tour        = !empty( $settings['design_tours'] ) ? $settings['design_tours'] : 'design-1';
		$design_apartment   = !empty( $settings['design_apartment'] ) ? $settings['design_apartment'] : 'design-1';
		$design_car   		= !empty( $settings['design_carrental'] ) ? $settings['design_carrental'] : 'design-1';
		if($service == 'tf_hotel'){
			$design = $design_hotel;
		} elseif($service == 'tf_tours'){
			$design = $design_tour;
		} elseif($service == 'tf_apartment'){
			$design = $design_apartment;
		} elseif($service == 'tf_carrental'){
			$design = $design_car;
		}

		$place = ($service == 'tf_hotel') ? 'tf-location' : 'tf-destination';
		if ( $service == 'tf_apartment' ) {
			$place = 'tf-apartment-location';
		}
		$place_text            = $service == 'tf_hotel' ? esc_html__( 'Enter Location', 'tourfic' ) : esc_html__( 'Enter Destination', 'tourfic' );
		$date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		$hotel_location_field_required   = ! empty( Helper::tfopt( "required_location_hotel_search" ) ) ? Helper::tfopt( "required_location_hotel_search" ) : 0;
		$tour_location_field_required    = ! empty( Helper::tfopt( "required_location_tour_search" ) ) ? Helper::tfopt( "required_location_tour_search" ) : 0;

		$hotel_location_field_required   = ! empty( Helper::tfopt( "required_location_hotel_search" ) ) ? Helper::tfopt( "required_location_hotel_search" ) : 0;
		$tour_location_field_required    = ! empty( Helper::tfopt( "required_location_tour_search" ) ) ? Helper::tfopt( "required_location_tour_search" ) : 0;
		$disable_child_search            = ! empty( Helper::tfopt( 'disable_child_search' ) ) ? Helper::tfopt( 'disable_child_search' ) : '';
		$disable_infant_search           = ! empty( Helper::tfopt( 'disable_infant_search' ) ) ? Helper::tfopt( 'disable_infant_search' ) : '';
		$disable_hotel_child_search      = ! empty( Helper::tfopt( 'disable_hotel_child_search' ) ) ? Helper::tfopt( 'disable_hotel_child_search' ) : '';
		$disable_apartment_child_search  = ! empty( Helper::tfopt( 'disable_apartment_child_search' ) ) ? Helper::tfopt( 'disable_apartment_child_search' ) : '';
		$disable_apartment_infant_search = ! empty( Helper::tfopt( 'disable_apartment_infant_search' ) ) ? Helper::tfopt( 'disable_apartment_infant_search' ) : '';

        // Pull options from settings or set fallback values
        $disable_car_time_slot = !empty(Helper::tfopt('disable-car-time-slots')) ? boolval(Helper::tfopt('disable-car-time-slots')) : false;
        $time_interval = 30;
        $start_time_str = '00:00';
        $end_time_str   = '23:30';
        $default_time_str = '10:00';
        if($disable_car_time_slot){
            $time_interval = !empty(Helper::tfopt('car_time_interval')) ? intval(Helper::tfopt('car_time_interval')) : 30;
            $start_time_str = !empty(Helper::tfopt('car_start_time')) ? Helper::tfopt('car_start_time') : '00:00';
            $end_time_str   = !empty(Helper::tfopt('car_end_time')) ? Helper::tfopt('car_end_time') : '23:30';
        }

        if ( strtotime($start_time_str) >= strtotime('10:00') ) {
            $default_time_str = $start_time_str;
        }
        // Convert string times to timestamps
        $start_time = strtotime($start_time_str);
        $end_time   = strtotime($end_time_str);
        $default_time = gmdate('g:i A', strtotime($default_time_str));

        // Use selected time from GET or fall back to default
        $selected_pickup_time = !empty($_GET['pickup-time']) ? esc_html(sanitize_text_field( wp_unslash($_GET['pickup-time']))) : $default_time;
        $selected_dropoff_time = !empty($_GET['dropoff-time']) ? esc_html(sanitize_text_field( wp_unslash($_GET['dropoff-time']))) : $default_time;

		if ( ( $service == 'tf_hotel' && $design == "design-1" ) || ( $service == 'tf_tours' && $design == "design-1" ) ) {
			?>
            <form action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>" method="get" autocomplete="off" class="tf-archive-booking-form__style-1 tf-box-wrapper tf-box tf_archive_search_result tf-hotel-side-booking">
                <div class="tf-field-group tf-destination-box" <?php echo ( $service == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1 ) || ( $service == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                    <?php
                    $location_icon_migrated = isset($settings['__fa4_migrated']['location_icon']);
                    $location_icon_is_new = empty($settings['location_icon_comp']);

                    if ( $location_icon_is_new || $location_icon_migrated ) {
                        Icons_Manager::render_icon( $settings['location_icon'], [ 'aria-hidden' => 'true' ] );
                    } else {
                        ?>
                        <i class="<?php echo esc_attr($settings['location_icon_comp']); ?>" aria-hidden="true"></i>
                        <?php
                    }?>

                    <?php if ( is_post_type_archive( "tf_hotel" ) ) { ?>
                        <input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
                    <?php } elseif ( is_post_type_archive( "tf_tours" ) ) { ?>
                        <input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
                    <?php } else { ?>
                        <input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>"
                                value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
                    <?php } ?>
                    <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>

                </div>
                <div class="tf-field-group tf-mt-16 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <?php
                            $adult_icon_migrated = isset($settings['__fa4_migrated']['adult_icon']);
                            $adult_icon_is_new = empty($settings['adult_icon_comp']);

                            if ( $adult_icon_is_new || $adult_icon_migrated ) {
                                Icons_Manager::render_icon( $settings['adult_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['adult_icon_comp']); ?>" aria-hidden="true"></i>
                                <?php
                            }?>
                            <?php echo !empty($settings['adult_label']) ? esc_html($settings['adult_label']) : esc_html__('Adult', 'tourfic'); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="1" value="1">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
                <?php if ( ( $service == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
                            ($service == 'tf_tours' && empty( $disable_child_search ))
                ) { ?>
                <div class="tf-field-group tf-mt-16 tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <?php
                            $children_icon_migrated = isset($settings['__fa4_migrated']['children_icon']);
                            $children_icon_is_new = empty($settings['children_icon_comp']);

                            if ( $children_icon_is_new || $children_icon_migrated ) {
                                Icons_Manager::render_icon( $settings['children_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['children_icon_comp']); ?>" aria-hidden="true"></i>
                                <?php
                            }?>
                            <?php echo !empty($settings['children_label']) ? esc_html($settings['children_label']) : esc_html__('Children', 'tourfic'); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="childrens" id="children" min="0" value="0">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php if ( $service !== 'tf_tours' ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <?php
                                $room_icon_migrated = isset($settings['__fa4_migrated']['room_icon']);
                                $room_icon_is_new = empty($settings['room_icon_comp']);

                                if ( $room_icon_is_new || $room_icon_migrated ) {
                                    Icons_Manager::render_icon( $settings['room_icon'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($settings['room_icon_comp']); ?>" aria-hidden="true"></i>
                                    <?php
                                }?>
                                <?php echo !empty($settings['room_label']) ? esc_html($settings['room_label']) : esc_html__('Room', 'tourfic'); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="room" id="room" min="1" value="1">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="tf-field-group tf-mt-8">
                    <?php
                    $date_icon_migrated = isset($settings['__fa4_migrated']['date_icon']);
                    $date_icon_is_new = empty($settings['date_icon_comp']);

                    if ( $date_icon_is_new || $date_icon_migrated ) {
                        Icons_Manager::render_icon( $settings['date_icon'], [ 'aria-hidden' => 'true' ] );
                    } else {
                        ?>
                        <i class="<?php echo esc_attr($settings['date_icon_comp']); ?>" aria-hidden="true"></i>
                        <?php
                    }?>
                    <input type="text" class="tf-field time" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                            placeholder="<?php echo !empty($settings['date_placeholder_text']) ? esc_attr($settings['date_placeholder_text']) : esc_html__( 'Select Date', 'tourfic' ); ?>" required value="">
                </div>
                <div class="tf_booking-dates">
                    <div class="tf_label-row"></div>
                </div>
                <div class="tf-booking-bttns tf-mt-24">
                    <input type="hidden" name="type" value="<?php echo esc_attr( $service ); ?>" class="tf-post-type"/>
                    <button class="tf_btn tf_btn_full tf-submit"><?php echo !empty($settings['btn_text']) ? esc_html($settings['btn_text']) : esc_html__( 'Check Availability', 'tourfic' ); ?></button>
                </div>
            </form>
            <script>
                (function ($) {
                    $(document).ready(function () {
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

                        $(document).on("focus", ".tf-hotel-side-booking #check-in-out-date", function (e) {
                            const regexMap = {
                                'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                                'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                                'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                                'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                                'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                            };
                            const dateRegex = regexMap['<?php echo esc_html($date_format_for_users); ?>'];
                            let calander = flatpickr(this, {
                                enableTime: false,
                                minDate: "today",
                                mode: "range",
                                dateFormat: "Y/m/d",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

                                // flatpickr locale
								<?php Helper::tf_flatpickr_locale(); ?>

                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                        return `${d1} - ${d2}`;
                                    });
                                },
                            });

                            // open flatpickr on focus
                            calander.open();
                        })
                    });
                })(jQuery);
            </script>
			<?php
		} elseif ( ( $service == 'tf_hotel' && $design == "design-2" ) || ( $service == 'tf_tours' && $design == "design-2" ) || ( $service == 'tf_apartment' && $design == "design-1" )) { ?>
            <div class="tf-archive-template__two" style="padding: 0;">
                <span class="tf-modify-search-btn">
                    <?php esc_html_e("Modify search", "tourfic"); ?>
                </span>
                <div class="tf-archive-booking-form__style-2 tf-archive-search-form tf-booking-form-wrapper">
                    <form action="<?php echo esc_url(  Helper::tf_booking_search_action() ); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
                        <div class="tf-booking-form-fields <?php echo $service == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
                            <div class="tf-booking-form-location" <?php echo ( $service == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1 ) || ( $service == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                                <span class="tf-booking-form-title"><?php echo !empty($settings['loc_label']) ? esc_html($settings['loc_label']) : esc_html__('Location', 'tourfic'); ?></span>
                                <label for="tf-search-location" class="tf-booking-location-wrap">
                                    <?php
                                    $location_icon_migrated = isset($settings['__fa4_migrated']['location_icon']);
                                    $location_icon_is_new = empty($settings['location_icon_comp']);

                                    if ( $location_icon_is_new || $location_icon_migrated ) {
                                        Icons_Manager::render_icon( $settings['location_icon'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['location_icon_comp']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }?>
                                    <?php if ( is_post_type_archive( "tf_hotel" ) ) { ?>
                                        <input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                            placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
                                    <?php } elseif ( is_post_type_archive( "tf_tours" ) ) { ?>
                                        <input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                            placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
                                    <?php } else { ?>
                                        <input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>"
                                            value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
                                    <?php } ?>
                                    <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>
                                </label>
                            </div>

                            <?php if ( $service == 'tf_hotel' || $service == 'tf_apartment' ) { ?>
                                <div class="tf-booking-form-checkin">
                                    <span class="tf-booking-form-title"><?php echo !empty($settings['checkin_label']) ? esc_html($settings['checkin_label']) : esc_html__( "Check in", "tourfic" ); ?></span>
                                    <div class="tf-booking-date-wrap">
                                        <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                        <span class="tf-booking-month">
                                            <span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                            <path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                        placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
                                </div>
                                <div class="tf-booking-form-checkout">
                                    <span class="tf-booking-form-title"><?php echo !empty($settings['checkout_label']) ? esc_html($settings['checkout_label']) : esc_html__( "Check out", "tourfic" ); ?></span>
                                    <div class="tf-booking-date-wrap">
                                        <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                        <span class="tf-booking-month">
                                            <span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                            <path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
                                            </svg>
                                        </span>
                                    </div>
                                    
                                </div>
                            <?php } ?>

                            <?php if ( $service == 'tf_tours' ) { ?>
                                <div class="tf-booking-form-checkin">
                                    <span class="tf-booking-form-title"><?php echo !empty($settings['date_label']) ? esc_html($settings['date_label']) : esc_html__( "Date", "tourfic" ); ?></span>
                                    <div class="tf-tour-searching-date-block">
                                        <div class="tf-booking-date-wrap tf-tour-start-date">
                                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                            <span class="tf-booking-month">
                                                <span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
                                            </span>
                                        </div>
                                        <div class="tf-duration">
                                            <span>-</span>
                                        </div>
                                        <div class="tf-booking-date-wrap tf-tour-end-date">
                                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                            <span class="tf-booking-month">
                                        <span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                        <path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
                                        </svg>
                                    </span>
                                        </div>
                                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                            placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="tf-booking-form-guest-and-room">
                                <?php if ( $service == 'tf_hotel' ) { ?>
                                    <div class="tf-booking-form-guest-and-room-inner">
                                        <span class="tf-booking-form-title"><?php echo !empty($settings['selector_label']) ? esc_html($settings['selector_label']) : esc_html__('Guests & rooms', 'tourfic'); ?></span>
                                        <div class="tf-booking-guest-and-room-wrap tf-archive-guest-info">
                                            <span class="tf-guest"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "guest", "tourfic" ); ?> <span
                                                    class="tf-room"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "rooms", "tourfic" ); ?>
                                        </div>
                                        <div class="tf-arrow-icons">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                                <path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
                                            </svg>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="tf-booking-form-guest-and-room-inner">
                                        <span class="tf-booking-form-title"><?php echo !empty($settings['selector_label']) ? esc_html($settings['selector_label']) : esc_html__('Guests', 'tourfic'); ?></span>
                                        <div class="tf-booking-guest-and-room-wrap">
                                            <span class="tf-guest tf-booking-date">
                                                <?php esc_html_e( "01", "tourfic" ); ?>
                                            </span>
                                            <span class="tf-booking-month">
                                                <span><?php esc_html_e( "guest", "tourfic" ); ?></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                                <path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php echo !empty($settings['adult_label']) ? esc_html($settings['adult_label']) : esc_html__('Adults', 'tourfic'); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" name="adults" id="adults" min="1" value="1" readonly>
                                                <div class="acr-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ( ( $service == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
                                                ($service == 'tf_tours' && empty( $disable_child_search )) ||
                                                ( $service == 'tf_apartment' && empty( $disable_apartment_child_search ) )
                                        ) { ?>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php echo !empty($settings['children_label']) ? esc_html($settings['children_label']) : esc_html__('Children', 'tourfic'); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" name="childrens" id="children" min="0" value="0" readonly>
                                                <div class="acr-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ( $service == 'tf_hotel' ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php echo !empty($settings['room_label']) ? esc_html($settings['room_label']) : esc_html__('Rooms', 'tourfic'); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="room" id="room" min="1" value="1" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tf-booking-form-submit">
                            <input type="hidden" name="type" value="<?php echo esc_attr( $service ); ?>" class="tf-post-type"/>
                            <button class="tf_btn tf_btn_large tf_btn_sharp tf-submit"><?php echo !empty($settings['btn_text']) ? esc_html($settings['btn_text']) : esc_html__( 'Check Availability', 'tourfic' ); ?></button>
                        </div>

                        <?php if ( $service == 'tf_tours' ) { ?>
                            <script>
                                (function ($) {
                                    $(document).ready(function () {
                                        // flatpickr locale first day of Week
                                        <?php Helper::tf_flatpickr_locale( "root" ); ?>

                                        $(".tf-archive-booking-form__style-2 .tf-booking-date-wrap").on("click", function () {

                                            $("#check-in-out-date").trigger("click");
                                        });
                                        $("#check-in-out-date").flatpickr({
                                            enableTime: false,
                                            mode: "range",
                                            dateFormat: "Y/m/d",
                                            minDate: "today",

                                            // flatpickr locale
                                            <?php Helper::tf_flatpickr_locale(); ?>

                                            onReady: function (selectedDates, dateStr, instance) {
                                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                                    return `${date1} - ${date2}`;
                                                });
                                                dateSetToFields(selectedDates, instance);
                                            },
                                            onChange: function (selectedDates, dateStr, instance) {
                                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                                    return `${date1} - ${date2}`;
                                                });
                                                dateSetToFields(selectedDates, instance);
                                            },
                                            <?php
                                            if(! empty( $check_in_out )){ ?>
                                            defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                                            <?php } ?>
                                        });

                                        function dateSetToFields(selectedDates, instance) {
                                            if (selectedDates.length === 2) {
                                                const monthNames = [
                                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                                ];
                                                if (selectedDates[0]) {
                                                    const startDate = selectedDates[0];
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-date").html(startDate.getDate());
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                                                }
                                                if (selectedDates[1]) {
                                                    const endDate = selectedDates[1];
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-date").html(endDate.getDate());
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                                                }
                                            }
                                        }

                                    });
                                })(jQuery);
                            </script>
                        <?php } ?>

                        <?php if ( $service == 'tf_hotel' || $service == 'tf_apartment' ) { ?>
                            <script>
                                (function ($) {
                                    $(document).ready(function () {
                                        const regexMap = {
                                            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                                            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                                            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                                            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                                            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                                        };
                                        const dateRegex = regexMap['<?php echo esc_html($date_format_for_users); ?>'];

                                        // flatpickr locale first day of Week
                                        <?php Helper::tf_flatpickr_locale( "root" ); ?>

                                        $(".tf-archive-booking-form__style-2 .tf-booking-date-wrap").on("click", function () {

                                            $("#check-in-out-date").trigger("click");
                                        });
                                        $("#check-in-out-date").flatpickr({
                                            enableTime: false,
                                            mode: "range",
                                            dateFormat: "Y/m/d",
                                            minDate: "today",

                                            // flatpickr locale
                                            <?php Helper::tf_flatpickr_locale(); ?>

                                            onReady: function (selectedDates, dateStr, instance) {
                                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                                    return `${date1} - ${date2}`;
                                                });
                                                dateSetToFields(selectedDates, instance);
                                            },
                                            onChange: function (selectedDates, dateStr, instance) {
                                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                                    return `${date1} - ${date2}`;
                                                });
                                                dateSetToFields(selectedDates, instance);
                                            },
                                            <?php
                                            if(! empty( $check_in_out )){ ?>
                                            defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                                            <?php } ?>
                                        });

                                        function dateSetToFields(selectedDates, instance) {
                                            if (selectedDates.length === 2) {
                                                const monthNames = [
                                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                                ];
                                                if (selectedDates[0]) {
                                                    const startDate = selectedDates[0];
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                                                }
                                                if (selectedDates[1]) {
                                                    const endDate = selectedDates[1];
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
                                                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                                                }
                                            }
                                        }

                                    });
                                })(jQuery);
                            </script>
                        <?php } ?>
                    </form>
                </div>
            </div>
		<?php 
		} elseif ( $service == 'tf_carrental' && $design == "design-1" ) { 
            //Pickup Location icon
            $pickup_location_icon_html = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3711)">
                                            <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3711">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                        </svg>';
            if(!empty($settings['pickup_location_icon']['value'])){
                $pickup_location_icon_migrated = isset($settings['__fa4_migrated']['pickup_location_icon']);
                $pickup_location_icon_is_new = empty($settings['pickup_location_icon_comp']);

                if ( $pickup_location_icon_is_new || $pickup_location_icon_migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $settings['pickup_location_icon'], [ 'aria-hidden' => 'true' ] );
                    $pickup_location_icon_html = ob_get_clean();
                } else{
                    $pickup_location_icon_html = '<i class="' . esc_attr( $settings['pickup_location_icon_comp'] ) . '"></i>';
                }
            }
            //Dropoff Location icon
            $dropoff_location_icon_html = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3711)">
                                            <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3711">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                        </svg>';
            if(!empty($settings['dropoff_location_icon']['value'])){
                $dropoff_location_icon_migrated = isset($settings['__fa4_migrated']['dropoff_location_icon']);
                $dropoff_location_icon_is_new = empty($settings['dropoff_location_icon_comp']);

                if ( $dropoff_location_icon_is_new || $dropoff_location_icon_migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $settings['dropoff_location_icon'], [ 'aria-hidden' => 'true' ] );
                    $dropoff_location_icon_html = ob_get_clean();
                } else{
                    $dropoff_location_icon_html = '<i class="' . esc_attr( $settings['dropoff_location_icon_comp'] ) . '"></i>';
                }
            }
            //Pickup Date icon
            $pickup_date_icon_html = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>';
            if(!empty($settings['pickup_date_icon']['value'])){
                $pickup_date_icon_migrated = isset($settings['__fa4_migrated']['pickup_date_icon']);
                $pickup_date_icon_is_new = empty($settings['pickup_date_icon_comp']);

                if ( $pickup_date_icon_is_new || $pickup_date_icon_migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $settings['pickup_date_icon'], [ 'aria-hidden' => 'true' ] );
                    $pickup_date_icon_html = ob_get_clean();
                } else{
                    $pickup_date_icon_html = '<i class="' . esc_attr( $settings['pickup_date_icon_comp'] ) . '"></i>';
                }
            }
            //Pickup Time icon
            $pickup_time_icon_html = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_257_3728)">
                                                <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_257_3728">
                                                <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                            </svg>';
            if(!empty($settings['pickup_time_icon']['value'])){
                $pickup_time_icon_migrated = isset($settings['__fa4_migrated']['pickup_time_icon']);
                $pickup_time_icon_is_new = empty($settings['pickup_time_icon_comp']);

                if ( $pickup_time_icon_is_new || $pickup_time_icon_migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $settings['pickup_time_icon'], [ 'aria-hidden' => 'true' ] );
                    $pickup_time_icon_html = ob_get_clean();
                } else{
                    $pickup_time_icon_html = '<i class="' . esc_attr( $settings['pickup_time_icon_comp'] ) . '"></i>';
                }
            }
            //Drop-off Date icon
            $dropoff_date_icon_html = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>';
            if(!empty($settings['dropoff_date_icon']['value'])){
                $dropoff_date_icon_migrated = isset($settings['__fa4_migrated']['dropoff_date_icon']);
                $dropoff_date_icon_is_new = empty($settings['dropoff_date_icon_comp']);

                if ( $dropoff_date_icon_is_new || $dropoff_date_icon_migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $settings['dropoff_date_icon'], [ 'aria-hidden' => 'true' ] );
                    $dropoff_date_icon_html = ob_get_clean();
                } else{
                    $dropoff_date_icon_html = '<i class="' . esc_attr( $settings['dropoff_date_icon_comp'] ) . '"></i>';
                }
            }
            //Drop-off Time icon
            $dropoff_time_icon_html = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3728)">
                                            <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3728">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                        </svg>';
            if(!empty($settings['dropoff_time_icon']['value'])){
                $dropoff_time_icon_migrated = isset($settings['__fa4_migrated']['dropoff_time_icon']);
                $dropoff_time_icon_is_new = empty($settings['dropoff_time_icon_comp']);

                if ( $dropoff_time_icon_is_new || $dropoff_time_icon_migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $settings['dropoff_time_icon'], [ 'aria-hidden' => 'true' ] );
                    $dropoff_time_icon_html = ob_get_clean();
                } else{
                    $dropoff_time_icon_html = '<i class="' . esc_attr( $settings['dropoff_time_icon_comp'] ) . '"></i>';
                }
            }
            //Search icon
            $search_icon_html = '<i class="ri-search-line"></i>';
            if(!empty($settings['search_icon']['value'])){
                $search_icon_migrated = isset($settings['__fa4_migrated']['search_icon']);
                $search_icon_is_new = empty($settings['search_icon_comp']);

                if ( $search_icon_is_new || $search_icon_migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $settings['search_icon'], [ 'aria-hidden' => 'true' ] );
                    $search_icon_html = ob_get_clean();
                } else{
                    $search_icon_html = '<i class="' . esc_attr( $settings['search_icon_comp'] ) . '"></i>';
                }
            }
            ?>
            <div class="tf-archive-booking-form__style-1 tf-archive-search-box tf-car-archive-search-box ">
                <div class="tf-archive-search-box-wrapper tf_archive_search_result">
                    <div class="tf-date-select-box tf-flex tf-flex-gap-8">
                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn tf-pick-drop-location active">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <?php echo wp_kses( $pickup_location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                    </div>
                                    <div class="info-select">
                                        <label><?php echo !empty($settings['pickup_loc_label']) ? esc_attr($settings['pickup_loc_label']) : esc_html__( 'Pick-up', 'tourfic' ); ?></label>
                                        <input type="text" placeholder="<?php echo !empty($settings['pickup_loc_placeholder_text']) ? esc_attr($settings['pickup_loc_placeholder_text']) : esc_html__( 'Pick Up Location', 'tourfic' ); ?>" id="tf_pickup_location" value="<?php echo !empty($_GET['pickup-name']) ? esc_html(sanitize_text_field( wp_unslash($_GET['pickup-name']) ) ) : '' ?>" />
                                        <input type="hidden" id="tf_pickup_location_id" value="<?php echo !empty($_GET['pickup']) ? esc_html(sanitize_text_field( wp_unslash($_GET['pickup']) ) ) : '' ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <?php echo wp_kses( $dropoff_location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                    </div>
                                    <div class="info-select">
                                        <label><?php echo !empty($settings['dropoff_loc_label']) ? esc_attr($settings['dropoff_loc_label']) : esc_html__( 'Drop-off', 'tourfic' ); ?></label>
                                        <input type="text" placeholder="<?php echo !empty($settings['dropoff_loc_placeholder_text']) ? esc_attr($settings['dropoff_loc_placeholder_text']) : esc_html__( 'Drop Off Location', 'tourfic' ); ?>" id="tf_dropoff_location" value="<?php echo !empty($_GET['dropoff-name']) ? esc_html(sanitize_text_field( wp_unslash($_GET['dropoff-name']))) : '' ?>" />
                                        <input type="hidden" id="tf_dropoff_location_id" value="<?php echo !empty($_GET['dropoff']) ? esc_html(sanitize_text_field( wp_unslash($_GET['dropoff']))) : '' ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <?php echo wp_kses( $pickup_date_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                    </div>
                                    <div class="info-select">
                                        <label><?php echo !empty($settings['pickup_date_label']) ? esc_attr($settings['pickup_date_label']) : esc_html__( 'Pick-up date', 'tourfic' ); ?></label>
                                        <input type="text" placeholder="<?php echo !empty($settings['pickup_date_placeholder_text']) ? esc_attr($settings['pickup_date_placeholder_text']) : esc_html__( 'Pick Up Date', 'tourfic' ); ?>" class="tf_pickup_date" value="<?php echo !empty($_GET['pickup_date']) ? esc_html(sanitize_text_field( wp_unslash($_GET['pickup_date']))) : esc_html(gmdate('Y/m/d', strtotime('+1 day'))); ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <?php echo wp_kses( $pickup_time_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php echo !empty($settings['pickup_time_label']) ? esc_attr($settings['pickup_time_label']) : esc_html__( 'Time', 'tourfic' ); ?></h5>
                                        <div class="selected-pickup-time">
                                            <div class="text">
                                                <?php echo esc_html($selected_pickup_time); ?>
                                            </div>
                                            <div class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" name="tf_pickup_time" class="tf_pickup_time" id="tf_pickup_time" value="<?php echo esc_attr($selected_pickup_time); ?>">
                                        <div class="tf-select-time">
                                            <ul class="time-options-list tf-pickup-time">
                                                <?php
                                                    for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                        $time_label = gmdate("g:i A", $time);
                                                        $selected = ($selected_pickup_time === $time_label) ? 'selected' : '';
                                                        echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                    }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <?php echo wp_kses( $dropoff_date_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                    </div>
                                    <div class="info-select">
                                        <label><?php echo !empty($settings['dropoff_date_label']) ? esc_attr($settings['dropoff_date_label']) : esc_html__( 'Drop-off date', 'tourfic' ); ?></label>
                                        <input type="text" placeholder="<?php echo !empty($settings['dropoff_date_placeholder_text']) ? esc_attr($settings['dropoff_date_placeholder_text']) : esc_html__( 'Drop Off Date', 'tourfic' ); ?>" class="tf_dropoff_date" value="<?php echo !empty($_GET['dropoff-date']) ? esc_html(sanitize_text_field( wp_unslash($_GET['dropoff-date']))) : esc_html(gmdate('Y/m/d', strtotime('+2 day'))) ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <?php echo wp_kses( $dropoff_time_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php echo !empty($settings['dropoff_time_label']) ? esc_attr($settings['dropoff_time_label']) : esc_html__( 'Time', 'tourfic' ); ?></h5>
                                        <div class="selected-dropoff-time">
                                            <div class="text">
                                                <?php echo esc_html($selected_dropoff_time); ?>
                                            </div>
                                            <div class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" name="tf_dropoff_time" class="tf_dropoff_time" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
                                        <div class="tf-select-time">
                                            <ul class="time-options-list tf-dropoff-time">
                                                <?php
                                                    for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                        $time_label = gmdate("g:i A", $time);
                                                        $selected = ($selected_dropoff_time === $time_label) ? 'selected' : '';
                                                        echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
                                                    }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tf-driver-location-box tf-flex tf-flex-space-bttn tf-flex-align-center">
                        <div class="tf-driver-location">
                            <?php
                            $car_driver_min_age      = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] : 18;
                            $car_driver_max_age      = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] : 40;
                            ?>
                            <ul>
                                <li>
                                    <label><?php echo !empty($settings['return_same_location_text']) ? esc_attr($settings['return_same_location_text']) : esc_html__( 'Return in the same location', 'tourfic' ); ?>
                                        <input type="checkbox" name="same_location" checked>
                                        <span class="tf-checkmark"></span>
                                    </label>
                                </li>
                                <li>
                                    <label><?php esc_html_e("Age of driver ", "tourfic"); ?>
                                    <?php echo esc_attr($car_driver_min_age); ?>-<?php echo esc_attr($car_driver_max_age); ?>?
                                        <input type="checkbox" name="driver_age" checked>
                                        <span class="tf-checkmark"></span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="tf-submit-button">
                            <input type="hidden" class="tf-post-type" value="<?php echo esc_attr("tf_carrental"); ?>">
                            <button class="tf_btn tf-filter-cars"><?php echo !empty($settings['btn_text']) ? esc_html($settings['btn_text']) : esc_html__( 'Search', 'tourfic' ); ?> <?php echo wp_kses( $search_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?></button>
                        </div>

                        <script>
                            (function ($) {
                                $(document).ready(function () {

                                    // flatpickr locale first day of Week
                                    <?php Helper::tf_flatpickr_locale('root'); ?>

                                    // Initialize the pickup date picker
                                    var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
                                        enableTime: false,
                                        dateFormat: "Y/m/d",
                                        minDate: "today",
                                        disableMobile: "true",

                                        // flatpickr locale
                                        <?php Helper::tf_flatpickr_locale(); ?>

                                        onReady: function (selectedDates, dateStr, instance) {
                                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                        },
                                        onChange: function (selectedDates, dateStr, instance) {
                                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                            // Update minDate for the dropoff date picker
                                            dropoffFlatpickr.set("minDate", dateStr);
                                        }
                                    });

                                    // Initialize the dropoff date picker
                                    var dropoffFlatpickr = $(".tf_dropoff_date").flatpickr({
                                        enableTime: false,
                                        dateFormat: "Y/m/d",
                                        minDate: "today",
                                        disableMobile: "true",

                                        // flatpickr locale
                                        <?php Helper::tf_flatpickr_locale(); ?>

                                        onReady: function (selectedDates, dateStr, instance) {
                                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                        },
                                        onChange: function (selectedDates, dateStr, instance) {
                                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                        }
                                    });
                                });
                            })(jQuery);

                        </script>
                    </div>
                </div>
            </div>
        <?php } elseif ( ( $service == 'tf_hotel' && $design == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
            ( $service == 'tf_tours' && $design == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
            ( $service == 'tf_apartment' && $design == "design-2" && function_exists( 'is_tf_pro' ) && is_tf_pro())
        ){
            ?>
            <form class="tf-archive-booking-form__style-3 tf_archive_search_result tf-hotel-side-booking tf-booking-form" action="<?php echo esc_url(  Helper::tf_booking_search_action() ); ?>" method="get" autocomplete="off">
                <div class="tf-search-fields <?php echo $service == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
                    <div class="tf-search-field">
                        <div class="tf-search-field-icon">
                            <?php
                            $location_icon_migrated = isset($settings['__fa4_migrated']['location_icon']);
                            $location_icon_is_new = empty($settings['location_icon_comp']);

                            if ( $location_icon_is_new || $location_icon_migrated ) {
                                Icons_Manager::render_icon( $settings['location_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['location_icon_comp']); ?>" aria-hidden="true"></i>
                                <?php
                            }?>
                        </div>
                        <label for="<?php echo esc_attr($place); ?>" class="tf-search-field-content">
                            <span class="tf-search-field-label"><?php echo !empty($settings['loc_label']) ? esc_html($settings['loc_label']) : esc_html__('Location', 'tourfic'); ?></span>
                            <input type="text" required="" id="<?php echo esc_attr($place); ?>" class="tf-search-input" placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr($taxonomy_name) : ''; ?>">
                            <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr($taxonomy_slug) : ''; ?>"/>
                        </label>
                    </div>
                    <div class="tf-search-field-divider"></div>
                    <?php if ( $service == 'tf_hotel' || $service == 'tf_apartment' ) { ?>
                        <div class="tf-search-field-checkinout">
                            <div class="tf-search-field tf-search-field-checkin">
                                <div class="tf-search-field-icon">
                                    <?php
                                    $date_icon_migrated = isset($settings['__fa4_migrated']['date_icon']);
                                    $date_icon_is_new = empty($settings['date_icon_comp']);

                                    if ( $date_icon_is_new || $date_icon_migrated ) {
                                        Icons_Manager::render_icon( $settings['date_icon'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['date_icon_comp']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }?>
                                </div>
                                <label class="tf-search-field-content" for='tf-check-out'>
                                    <span class="tf-search-field-label"><?php echo !empty($settings['checkin_label']) ? esc_html($settings['checkin_label']) : esc_html__( "Check in", "tourfic" ); ?></span>
                                    <input type="text" class="tf-search-input" name="tf-check-in" id="tf-check-in" onkeypress="return false;" placeholder="<?php echo !empty($settings['date_placeholder_text']) ? esc_attr($settings['date_placeholder_text']) : esc_html__( 'Select Date', 'tourfic' ); ?>" value="" readonly>
                                    <input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php echo !empty($settings['date_placeholder_text']) ? esc_attr($settings['date_placeholder_text']) : esc_html__( 'Select Date', 'tourfic' ); ?>" value="">
                                </label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                <path d="M11.2824 7.83327L7.70637 4.25726L8.64917 3.31445L13.8346 8.49993L8.64917 13.6853L7.70637 12.7425L11.2824 9.1666H3.16797V7.83327H11.2824Z" fill="#6E655E"/>
                            </svg>
                            <div class="tf-search-field tf-search-field-checkout">
                                <div class="tf-search-field-icon">
                                    <?php
                                    $date_icon_migrated = isset($settings['__fa4_migrated']['date_icon']);
                                    $date_icon_is_new = empty($settings['date_icon_comp']);

                                    if ( $date_icon_is_new || $date_icon_migrated ) {
                                        Icons_Manager::render_icon( $settings['date_icon'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['date_icon_comp']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }?>
                                </div>
                                <label class="tf-search-field-content" for='tf-check-out'>
                                    <span class="tf-search-field-label"><?php echo !empty($settings['checkout_label']) ? esc_html($settings['checkout_label']) : esc_html__( "Check out", "tourfic" ); ?></span>
                                    <input type="text" class="tf-search-input" name="tf-check-out" id="tf-check-out" onkeypress="return false;" placeholder="<?php echo !empty($settings['date_placeholder_text']) ? esc_attr($settings['date_placeholder_text']) : esc_html__( 'Select Date', 'tourfic' ); ?>" value="" readonly>
                                </label>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ( $service == 'tf_tours' ) { ?>
                        <div class="tf-search-field-checkinout">
                            <div class="tf-search-field">
                                <div class="tf-search-field-icon">
                                    <?php
                                    $date_icon_migrated = isset($settings['__fa4_migrated']['date_icon']);
                                    $date_icon_is_new = empty($settings['date_icon_comp']);

                                    if ( $date_icon_is_new || $date_icon_migrated ) {
                                        Icons_Manager::render_icon( $settings['date_icon'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['date_icon_comp']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }?>
                                </div>
                                <label class="tf-search-field-content" for="check-in-out-date">
                                    <span class="tf-search-field-label"><?php echo !empty($settings['date_label']) ? esc_html($settings['date_label']) : esc_html__( "Date", "tourfic" ); ?></span>
                                    <input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php echo !empty($settings['date_placeholder_text']) ? esc_attr($settings['date_placeholder_text']) : esc_html__( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr($check_in_out) . '"' : '' ?>>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="tf-search-field-divider"></div>
                    <div class="tf-search-guest-and-room">
                        <?php if ( $service == 'tf_hotel' ) { ?>
                            <div class="tf-search-field">
                                <div class="tf-search-field-icon">
                                    <?php
                                    $selector_icon_migrated = isset($settings['__fa4_migrated']['selector_icon']);
                                    $selector_icon_is_new = empty($settings['selector_icon_comp']);

                                    if ( $selector_icon_is_new || $selector_icon_migrated ) {
                                        Icons_Manager::render_icon( $settings['selector_icon'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($settings['selector_icon_comp']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }?>
                                </div>
                                <div class="tf-search-field-content">
                                    <span class="tf-search-field-label"><?php echo !empty($settings['selector_label']) ? esc_html($settings['selector_label']) : esc_html__('Guests & rooms', 'tourfic'); ?></span>
                                    <div class="tf-archive-guest-info">
                                        <span class="tf-guest"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "guest", "tourfic" ); ?>
                                        <span class="tf-room"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "rooms", "tourfic" ); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="tf-search-field">
                                <div class="tf-search-field-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M10.5013 8.83341C12.3422 8.83341 13.8346 7.34103 13.8346 5.50008C13.8346 3.65913 12.3422 2.16675 10.5013 2.16675C8.66035 2.16675 7.16797 3.65913 7.16797 5.50008C7.16797 7.34103 8.66035 8.83341 10.5013 8.83341ZM5.08464 11.3334C6.23523 11.3334 7.16797 10.4007 7.16797 9.25008C7.16797 8.09949 6.23523 7.16675 5.08464 7.16675C3.93404 7.16675 3.0013 8.09949 3.0013 9.25008C3.0013 10.4007 3.93404 11.3334 5.08464 11.3334ZM18.0013 9.25008C18.0013 10.4007 17.0686 11.3334 15.918 11.3334C14.7674 11.3334 13.8346 10.4007 13.8346 9.25008C13.8346 8.09949 14.7674 7.16675 15.918 7.16675C17.0686 7.16675 18.0013 8.09949 18.0013 9.25008ZM10.5013 9.66675C12.8025 9.66675 14.668 11.5322 14.668 13.8334V18.8334H6.33464V13.8334C6.33464 11.5322 8.20012 9.66675 10.5013 9.66675ZM4.66797 13.8333C4.66797 13.2559 4.75186 12.6981 4.90812 12.1714L4.76684 12.1837C3.30549 12.3421 2.16797 13.5799 2.16797 15.0833V18.8333H4.66797V13.8333ZM18.8346 18.8333V15.0833C18.8346 13.5316 17.6229 12.2628 16.0945 12.1714C16.2507 12.6981 16.3346 13.2559 16.3346 13.8333V18.8333H18.8346Z"
                                            fill="#6E655E"/>
                                    </svg>
                                </div>

                                <div class="tf-search-field-content">
                                    <span class="tf-search-field-label"><?php echo !empty($settings['selector_label']) ? esc_html($settings['selector_label']) : esc_html__('Persons', 'tourfic'); ?></span>
                                    <div class="tf-archive-guest-info">
                                        <span class="tf-adult"><?php esc_html_e( "1", "tourfic" ); ?></span> <?php echo !empty($settings['adult_label']) ? esc_html(strtolower($settings['adult_label'])) : esc_html__('adult', 'tourfic'); ?>
                                        <?php if ( ($service == 'tf_tours' && empty( $disable_child_search )) ||
                                                ( $service == 'tf_apartment' && empty( $disable_apartment_child_search ) )
                                        ) { ?>
                                        , <span class="tf-children"><?php esc_html_e( "0", "tourfic" ); ?></span> <?php echo !empty($settings['children_label']) ? esc_html(strtolower($settings['children_label'])) : esc_html__('children', 'tourfic'); ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="tf_acrselection-wrap">
                            <div class="tf_acrselection-inner">
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php echo !empty($settings['adult_label']) ? esc_html($settings['adult_label']) : esc_html__('Adult', 'tourfic'); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"></rect>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="adults" id="adults" min="1" value="1" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <?php if ( ( $service == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
                                        ($service == 'tf_tours' && empty( $disable_child_search )) ||
                                        ( $service == 'tf_apartment' && empty( $disable_apartment_child_search ) )
                                ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php echo !empty($settings['children_label']) ? esc_html($settings['children_label']) : esc_html__('Children', 'tourfic'); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"></rect>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="childrens" id="children" min="0" value="0" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if ( $service == 'tf_hotel' ) { ?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php echo !empty($settings['room_label']) ? esc_html($settings['room_label']) : esc_html__('Rooms', 'tourfic'); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <g clip-path="url(#clip0_3229_13094)">
                                                        <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_3229_13094">
                                                            <rect width="20" height="20" fill="white"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <input type="tel" name="room" id="room" min="1" value="1" readonly>
                                            <div class="acr-inc">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <g clip-path="url(#clip0_3229_13100)">
                                                        <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_3229_13100">
                                                            <rect width="20" height="20" fill="white"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tf-booking-form-submit">
                    <input type="hidden" name="type" value="<?php echo esc_attr($service); ?>" class="tf-post-type"/>
                    <button class="tf_btn tf-submit"><?php echo !empty($settings['btn_text']) ? esc_html($settings['btn_text']) : esc_html__( 'Search Now', 'tourfic' ); ?></button>
                </div>

                <?php if ( $service == 'tf_hotel' || $service == 'tf_tours' || $service == 'tf_apartment' ) : ?>
                    <script>
                        (function ($) {
                            $(document).ready(function () {
                                // flatpickr locale first day of Week
                                <?php Helper::tf_flatpickr_locale( "root" ); ?>

                                $(".tf-archive-booking-form__style-3 #tf-check-out").on('click', function () {
                                    $(".tf-search-input.form-control").click();
                                });

                                $("#check-in-out-date").flatpickr({
                                    enableTime: false,
                                    mode: "range",
                                    dateFormat: "Y/m/d",
                                    minDate: "today",
                                    altInput: true,
                                    altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                                    showMonths: $(window).width() >= 1240 ? 2 : 1,

                                    // flatpickr locale
                                    <?php Helper::tf_flatpickr_locale(); ?>

                                    onReady: function (selectedDates, dateStr, instance) {
                                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                        dateSetToFields(selectedDates, instance);
                                    },
                                    onChange: function (selectedDates, dateStr, instance) {
                                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                        instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                                        dateSetToFields(selectedDates, instance);
                                    },
                                    <?php if(! empty( $check_in_out )){ ?>
                                    defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                                    <?php } ?>
                                });

                                function dateSetToFields(selectedDates, instance) {
                                    const format = '<?php echo esc_html( $date_format_for_users ); ?>';
                                    if (selectedDates.length === 2) {
                                        if (selectedDates[0]) {
                                            let checkInDate = instance.formatDate(selectedDates[0], format);
                                            $(".tf-archive-booking-form__style-3 #tf-check-in").val(checkInDate);
                                        }

                                        if (selectedDates[1]) {
                                            let checkOutDate = instance.formatDate(selectedDates[1], format);
                                            $(".tf-archive-booking-form__style-3 #tf-check-out").val(checkOutDate);
                                        }
                                    }
                                }

                            });
                        })(jQuery);
                    </script>
                <?php endif; ?>
            </form>
		<?php } else { ?>
            <form class="tf_archive_search_result tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
                  action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner" <?php echo ( $service == 'tf_hotel' && Helper::tfopt( "hide_hotel_location_search" ) == 1 && Helper::tfopt( "required_location_hotel_search" ) != 1 ) || ( $service == 'tf_tours' && Helper::tfopt( "hide_tour_location_search" ) == 1 && Helper::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                            <?php
                            $location_icon_migrated = isset($settings['__fa4_migrated']['location_icon']);
                            $location_icon_is_new = empty($settings['location_icon_comp']);

                            if ( $location_icon_is_new || $location_icon_migrated ) {
                                Icons_Manager::render_icon( $settings['location_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['location_icon_comp']); ?>" aria-hidden="true"></i>
                                <?php
                            }?>

							<?php if ( is_post_type_archive( "tf_hotel" ) ) { ?>
                                <input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class=""
                                       placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
							<?php } elseif ( is_post_type_archive( "tf_tours" ) ) { ?>
                                <input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class=""
                                       placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
							<?php } else { ?>
                                <input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="" placeholder="<?php echo !empty($settings['loc_placeholder_text']) ? esc_attr($settings['loc_placeholder_text']) : esc_attr( $place_text ); ?>"
                                       value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
							<?php } ?>

                            <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>
                        </div>
                    </label>
                </div>

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <?php
                            $adult_icon_migrated = isset($settings['__fa4_migrated']['adult_icon']);
                            $adult_icon_is_new = empty($settings['adult_icon_comp']);
                            
                            if ( $adult_icon_is_new || $adult_icon_migrated ) {
                                Icons_Manager::render_icon( $settings['adult_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['adult_icon_comp']); ?>" aria-hidden="true"></i>
                                <?php
                            }?>
                            <select name="adults" id="adults" class="">
								<option value="1">1 <?php echo !empty($settings['adult_label']) ? esc_html($settings['adult_label']) : esc_html__('Adult', 'tourfic'); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) { ?>
									<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?> <?php echo !empty($settings['adult_label']) ? esc_html($settings['adult_label']) : esc_html__('Adults', 'tourfic'); ?></option>
								<?php } ?>
                            </select>
                        </div>
                    </label>
                </div>

	            <?php if ( ( $service == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
	                       ($service == 'tf_tours' && empty( $disable_child_search )) ||
	                       ( $service == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	            ) { ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <?php
                            $children_icon_migrated = isset($settings['__fa4_migrated']['children_icon']);
                            $children_icon_is_new = empty($settings['children_icon_comp']);

                            if ( $children_icon_is_new || $children_icon_migrated ) {
                                Icons_Manager::render_icon( $settings['children_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['children_icon_comp']); ?>" aria-hidden="true"></i>
                                <?php
                            }?>
                            <select name="children" id="children" class="">
								<option value="0">0 <?php echo !empty($settings['children_label']) ? esc_html($settings['children_label']) : esc_html__('Children', 'tourfic'); ?></option>
								<?php foreach ( range( 1, 8 ) as $value ) { ?>
									<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?> <?php echo !empty($settings['children_label']) ? esc_html($settings['children_label']) : esc_html__('Children', 'tourfic'); ?></option>
								<?php } ?>
                            </select>
                        </div>
                    </label>
                </div>
                <?php } ?>

				<?php if ( $service == 'tf_apartment' ): ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <?php
                                $infant_icon_migrated = isset($settings['__fa4_migrated']['infant_icon']);
                                $infant_icon_is_new = empty($settings['infant_icon_comp']);

                                if ( $infant_icon_is_new || $infant_icon_migrated ) {
                                    Icons_Manager::render_icon( $settings['infant_icon'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($settings['infant_icon_comp']); ?>" aria-hidden="true"></i>
                                    <?php
                                }?>
                                <select name="infant" id="infant" class="">
                                    <option value="0">0 <?php echo !empty($settings['infant_label']) ? esc_html($settings['infant_label']) : esc_html__('Infant', 'tourfic'); ?></option>
									<?php foreach ( range( 1, 8 ) as $value ) { ?>
										<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?> <?php echo !empty($settings['infant_label']) ? esc_html($settings['infant_label']) : esc_html__('Infant', 'tourfic'); ?></option>
									<?php } ?>
                                </select>
                            </div>
                        </label>
                    </div>
				<?php endif; ?>

				<?php if ( $service == 'tf_hotel' ) { ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <?php
                                $room_icon_migrated = isset($settings['__fa4_migrated']['room_icon']);
                                $room_icon_is_new = empty($settings['room_icon_comp']);

                                if ( $room_icon_is_new || $room_icon_migrated ) {
                                    Icons_Manager::render_icon( $settings['room_icon'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($settings['room_icon_comp']); ?>" aria-hidden="true"></i>
                                    <?php
                                }?>
                                <select name="room" id="room" class="">
									<option value="1">1 <?php echo !empty($settings['room_label']) ? esc_html($settings['room_label']) : esc_html__('Room', 'tourfic'); ?></option>
									<?php foreach ( range( 2, 8 ) as $value ) { ?>
										<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?> <?php echo !empty($settings['room_label']) ? esc_html($settings['room_label']) : esc_html__('Rooms', 'tourfic'); ?></option>
									<?php } ?>
                                </select>
                            </div>
                        </label>
                    </div>
				<?php } ?>
                <div class="tf_booking-dates">
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <?php
                                $date_icon_migrated = isset($settings['__fa4_migrated']['date_icon']);
                                $date_icon_is_new = empty($settings['date_icon_comp']);

                                if ( $date_icon_is_new || $date_icon_migrated ) {
                                    Icons_Manager::render_icon( $settings['date_icon'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($settings['date_icon_comp']); ?>" aria-hidden="true"></i>
                                    <?php
                                }?>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php echo !empty($settings['date_placeholder_text']) ? esc_attr($settings['date_placeholder_text']) : esc_html__( 'Select Date', 'tourfic' ); ?>" required value="">
                            </div>
                        </label>
                    </div>
                </div>

                <div class="tf_form-row">
                    <input type="hidden" name="type" value="<?php echo esc_attr( $service ); ?>" class="tf-post-type"/>
                    <button class="tf_btn tf_btn_full tf-submit" type="submit"><?php echo !empty($settings['btn_text']) ? esc_html($settings['btn_text']) : esc_html__( 'Check Availability', 'tourfic' ); ?></button>
                </div>
            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

                        $(document).on("focus", ".tf-hotel-side-booking #check-in-out-date", function (e) {
                            const regexMap = {
                                'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                                'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                                'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                                'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                                'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                            };
                            const dateRegex = regexMap['<?php echo esc_html($date_format_for_users); ?>'];
                            let calander = flatpickr(this, {
                                enableTime: false,
                                minDate: "today",
                                mode: "range",
                                dateFormat: "Y/m/d",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

                                // flatpickr locale
								<?php Helper::tf_flatpickr_locale(); ?>

                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    })
                                    instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                        return `${d1} - ${d2}`;
                                    })
                                },
                            });
                        });
                    });
                })(jQuery);
            </script>
		<?php }
	}

    /**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};";
	}

    /**
     * Generates conditional display rules for controls based on service and design
     * 
     * @param array $design Array of design conditions in format ['service' => 'design_value']
     * @return array Condition array for Elementor controls
     */
    protected function tf_display_conditionally($design) {
        $terms = [];
        
        foreach ($design as $service_key => $design_values) {
            // Detect if this is a "NOT" condition
            $is_not = false;
            if ( substr( $service_key, -1 ) === '!' ) {
                $is_not = true;
                $service = rtrim( $service_key, '!' );
            } else {
                $service = $service_key;
            }
            
            // Convert to array if it's not already
            $design_values = (array) $design_values;
            $design_control = 'design_' . str_replace('tf_', '', $service);

            foreach ($design_values as $design_value) {
                $terms[] = [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'service',
                            'operator' => $is_not ? '!=' : '==',
                            'value' => $service,
                        ],
                        [
                            'name' => $design_control,
                            'operator' => '==',
                            'value' => $design_value,
                        ],
                    ],
                ];
            }
        }

        return [
            'relation' => 'or',
            'terms' => $terms,
        ];
    }
}
