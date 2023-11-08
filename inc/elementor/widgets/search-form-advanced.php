<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search Form Advanced
 */
class TF_Search_advanced extends \Elementor\Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tourfic-search-advanced';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Tourfic Search Form (Advanced)', 'tourfic' );
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
			'all'       => __( 'All', 'tourfic' ),
			'hotel'     => __( 'Hotel', 'tourfic' ),
			'tour'      => __( 'Tour', 'tourfic' ),
			'apartment' => __( 'Apartment', 'tourfic' ),
		);

		if ( defined( 'TF_PRO' ) ) {
			$types['booking']   = __( 'Booking.com', 'tourfic' );
			$types['tp-flight'] = __( 'TravelPayouts Flight', 'tourfic' );
			$types['tp-hotel']  = __( 'TravelPayouts Hotel', 'tourfic' );
		}

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
				'label' => __( 'Content', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'tf_search_title',
			[
				'label' => __( 'Title', 'tourfic' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 1,
			]
		);


		$this->add_control(
			'tf_search_subtitle',
			[
				'label' => __( 'Subtitle', 'tourfic' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 2,
			]
		);

		$this->add_control(
			'type',
			[
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'label'   => esc_html__( 'Type', 'tourfic' ),
				'options' => $this->tf_search_types(),
				'default' => 'all',
			]
		);

		$this->add_control(
			'full-width',
			[
				'label'        => esc_html__( 'Full Width', 'tourfic' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'tourfic' ),
				'label_off'    => esc_html__( 'No', 'tourfic' ),
				'return_value' => true,
				'default'      => false,
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'tf_search_style_section',
			[
				'label' => __( 'Style', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Title Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-title h2',
			]
		);
		$this->add_control(
			'tf_search_title_color',
			[
				'label'     => __( 'Title Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
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
				'name'     => 'subtitle_typography',
				'label'    => __( 'Subtitle Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-subtitle',
			]
		);

		$this->add_control(
			'tf_search_subtitle_color',
			[
				'label'     => __( 'Subtitle Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
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
		$settings           = $this->get_settings_for_display();
		$tf_search_title    = $settings['tf_search_title'];
		$tf_search_subtitle = $settings['tf_search_subtitle'];
		$type               = implode( ',', $settings['type'] );
		$full_width         = $settings['full-width'];

		echo do_shortcode( '[tf_search_form title="' . $tf_search_title . '" subtitle="' . $tf_search_subtitle . '" type="' . $type . '" fullwidth="' . $full_width . '" advanced="enabled"]' );

	}


}
