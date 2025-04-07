<?php

namespace Tourfic\App\Widgets\Elementor\Widgets;

use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search Form Horizontal
 */
class Search_Form extends \Elementor\Widget_Base {

	use \Tourfic\Traits\Singleton;

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tf-search-form';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Tourfic Search Form', 'tourfic' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-site-search';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'tourfic' ];
	}

	public function tf_search_types() {
		$types = array(
			'hotel'     => esc_html__( 'Hotel', 'tourfic' ),
			'tour'      => esc_html__( 'Tour', 'tourfic' ),
			'apartment' => esc_html__( 'Apartment', 'tourfic' ),
			'carrentals' => esc_html__( 'Car', 'tourfic' ),
		);

		return $types;
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'tf_search_content_section',
			[
				'label' => esc_html__( 'Content', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'tf_search_title',
			[
				'label' => esc_html__( 'Title', 'tourfic' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 1,
			]
		);

		$this->add_control(
			'tf_search_subtitle',
			[
				'label' => esc_html__( 'Subtitle', 'tourfic' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 2,
			]
		);

		$this->add_control(
			'service',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => esc_html__( 'Service', 'tourfic' ),
				'options'  => [
					'tf_hotel'     => esc_html__( 'Hotel', 'tourfic' ),
					'tf_tours'     => esc_html__( 'Tour', 'tourfic' ),
					'tf_apartment' => esc_html__( 'Apartment', 'tourfic' ),
					'tf_carrental' => esc_html__( 'Car', 'tourfic' ),
				],
				'default'  => 'tf_hotel',
			]
		);
		
		// Design options for Hotel
		$this->add_control(
			'design_hotel',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
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
			]
		);
		
		// Design options for Tour
		$this->add_control(
			'design_tour',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
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
			]
		);
		
		// Design options for Apartment
		$this->add_control(
			'design_apartment',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
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
			]
		);
		
		// Design options for Car Rental
		$this->add_control(
			'design_car',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => esc_html__( 'Design', 'tourfic' ),
				'options'  => [
					'design-1' => esc_html__( 'Design 1', 'tourfic' ),
				],
				'default'  => 'design-1',
				'condition' => [
					'service' => 'tf_carrental',
				],
			]
		);

		

		$this->end_controls_section();


		$this->start_controls_section(
			'tf_search_style_section',
			[
				'label' => esc_html__( 'Style', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-title h2',
			]
		);
		$this->add_control(
			'tf_search_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf_widget-title h2' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tf_subhr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Subtitle Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-subtitle',
			]
		);

		$this->add_control(
			'tf_search_subtitle_color',
			[
				'label'     => esc_html__( 'Subtitle Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf_widget-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings           = $this->get_settings_for_display();
		$tf_search_title    = $settings['tf_search_title'];
		$tf_search_subtitle = $settings['tf_search_subtitle'];
		$service            = !empty( $settings['service'] ) ? $settings['service'] : 'tf_hotel';
		$design_hotel       = !empty( $settings['design_hotel'] ) ? $settings['design_hotel'] : 'design-1';
		$design_tour        = !empty( $settings['design_tour'] ) ? $settings['design_tour'] : 'design-1';
		$design_apartment   = !empty( $settings['design_apartment'] ) ? $settings['design_apartment'] : 'design-1';
		$design_car   		= !empty( $settings['design_car'] ) ? $settings['design_car'] : 'design-1';
		if($service == 'tf_hotel'){
			$design = $design_hotel;
		} elseif($service == 'tf_tours'){
			$design = $design_tour;
		} elseif($service == 'tf_apartment'){
			$design = $design_apartment;
		} elseif($service == 'tf_carrental'){
			$design = $design_car;
		}

		Helper::tf_archive_sidebar_search_form($service, '', '', '', $design);
	}


}
