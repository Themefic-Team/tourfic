<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Hotel Tour review slider
 * @since 2.8.9
 * @author Abu Hena
 */
class TF_Reviews_Slider extends \Elementor\Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'review-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Review Slider', 'tourfic' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel';
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

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
        
		$this->start_controls_section(
			'content',
			[
				'label' => __( 'Slider Settings', 'tourfic' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'type',
			[
				'label' => esc_html__( 'Reviews Type', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'description' => __( 'Choose the reviews type you want to show.', 'tourfic' ),
				'options' => [
					'tf_hotel' => 'Hotel',
					'tf_tours' => 'Tour',
				],
				'default' => 'tf_hotel'
			]
		);

		$this->add_control(
			'count',
			[
				'label' => esc_html__( 'Total Reviews', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Number of total reviews to show. Min 3.', 'tourfic' ),
				'min' => 1,
				'step' => 1,
				'default' => 3,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'arrows',
			[
				'label' => esc_html__( 'Slider Arrows', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
			]
		);
		$this->add_control(
			'dots',
			[
				'label' => esc_html__( 'Slider Dots', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
			]
		);
		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__( 'Autoplay Speed', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 2000,
			]
		);

		$this->add_control(
			'infinite',
			[
				'label' => esc_html__( 'Infinite Slider', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'Enable Infinite Slider', 'tourfic' ),
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
		$type = $settings['type'];
		$count = $settings['count'];
		$arrows = $settings['arrows'];
		$arrows == 'yes' ? $arrows = 'true' : $arrows = 'false';
		$dots = $settings['dots'];
		$dots == 'yes' ? $dots = 'true' : $dots = 'false';
		$autoplay = $settings['autoplay'];
		$autoplay == 'yes' ? $autoplay = 'true' : $autoplay = 'false';
		$autoplay_speed = $settings['autoplay_speed'];
		$infinite = $settings['infinite'];
		$infinite == 'yes' ? $infinite = 'true' : $infinite = 'false';

        echo do_shortcode('[tf_reviews type="'.$type.'" count="' .$count. '" autoplay="'.$autoplay.'" arrows="'.$arrows.'" dots="'.$dots.'" speed="'.$autoplay_speed.'" infinite="'.$infinite.'"]');


	}


}
