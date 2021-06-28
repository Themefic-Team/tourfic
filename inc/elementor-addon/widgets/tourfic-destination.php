<?php
namespace ElementorTourfic\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor BEAF Slider
 *
 * Elementor widget for BEAF Slider.
 *
 */
class TOURFIC_Destination extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tourfic-destination';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Tourfic Destination', 'tourfic' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-map-pin';
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
		return [ 'general' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */


	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
        
        
		$this->start_controls_section(
			'tf_destination_content_section',
			[
				'label' => __( 'Style', 'tourfic' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        


		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'tf_destination_title_typography',
				'label' => __( 'Destination Title Typography', 'torfic' ),
				'selector' => '{{WRAPPER}} .recomended_place_info_header h3',
			]
		);

		$this->add_control(
			'tf_destination_title_color',
			[
				'label' => __( 'Destination title Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .recomended_place_info_header h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tf_destination_title_hover_color',
			[
				'label' => __( 'Destination Title Hover Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_2,
				],
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .single_recomended_item:hover .recomended_place_info_header h3' => 'color: {{VALUE}}',
				],
			]
		);



		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'tf_destination_subtitle_typography',
				'label' => __( 'Destination Subitle Typography', 'torfic' ),
				'selector' => '{{WRAPPER}} .recomended_place_info_header p',
			]
		);

		$this->add_control(
			'tf_destination_subtitle_color',
			[
				'label' => __( 'Destination Subtitle Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_3,
				],
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .recomended_place_info_header p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tf_destination_subtitle_hover_color',
			[
				'label' => __( 'Destination Subtitle Hover Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_4,
				],
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .single_recomended_item:hover .recomended_place_info_header p' => 'color: {{VALUE}}',
				],
			]
		);



		$this->add_control(
			'tf_info_options',
			[
				'label' => __( 'Info Background', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'tf_info_background',
				'label' => __( 'Background Color', 'tourfic' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .recomended_place_info_header',
			]
		);		
		$this->add_control(
			'tf_info_hover_options',
			[
				'label' => __( 'Info Hover Background', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'tf_info_hover_background',
				'label' => __( 'Background Color', 'tourfic' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .single_recomended_item:hover .recomended_place_info_header',
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

        echo do_shortcode('[tourfic_destinations]');


	}


}
