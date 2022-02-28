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
class TOURFIC_Search extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tourfic-search';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Tourfic search', 'tourfic' );
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
			'tf_search_content_section',
			[
				'label' => __( 'Content', 'tourfic' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        
        $this->add_control(
			'tf_search_title',
			[
				'label' => __( 'Title', 'toufic' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Default title', 'toufic' ),
				'placeholder' => __( 'Type your title here', 'toufic' ),
			]
		);        


		$this->add_control(
			'tf_search_subtitle',
			[
				'label' => __( 'Subtitle', 'toufic' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Default description', 'toufic' ),
				'placeholder' => __( 'Type your description here', 'toufic' ),
			]
		);		



		$this->end_controls_section();


		$this->start_controls_section(
			'tf_search_style_section',
			[
				'label' => __( 'Style', 'tourfic' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Title Typography', 'torfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-title h2',
			]
		);
		$this->add_control(
			'tf_search_title_color',
			[
				'label' => __( 'Title Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
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
				'name' => 'subtitle_typography',
				'label' => __( 'Subtitle Typography', 'torfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-subtitle',
			]
		);

		$this->add_control(
			'tf_search_subtitle_color',
			[
				'label' => __( 'Subtitle Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
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
		$settings = $this->get_settings_for_display();
        $tf_search_title = $settings['tf_search_title'];
        $tf_search_subtitle = $settings['tf_search_subtitle'];
      


      
        echo do_shortcode('[tf_search_form title="'.$tf_search_title.'" subtitle="'.$tf_search_subtitle.'" ]');


	}


}
