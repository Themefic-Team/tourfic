<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Apartment Grid slider by location
 * @author Foysal
 */
class TF_Apartment_Grid_Slider extends \Elementor\Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'apartment-grid-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Apartments by Location', 'tourfic' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
				'label' => __( 'Settings', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'tourfic' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 1,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => esc_html__( 'Sub-Title', 'tourfic' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 2,
			]
		);

		//get the location IDs
		$locations = get_terms( 'apartment_location', array(
			'orderby'    => 'count',
			'hide_empty' => 0,
		) );

		$term_ids = [];
		foreach ( $locations as $location ) {
			$term_ids[ $location->term_id ] = $location->name;
		}
		$this->add_control(
			'locations',
			[
				'label'       => esc_html__( 'Locations', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $term_ids,
				'multiple'    => true,
			]
		);

		$this->add_control(
			'count',
			[
				'label'       => esc_html__( 'Total Apartments', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 3,
			]
		);
		$this->add_control(
			'style',
			[
				'label'       => esc_html__( 'Apartment Layout', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'grid'   => __( 'Grid', 'tourfic' ),
					'slider' => __( 'Slider', 'tourfic' ),
				),
				'default'     => 'grid'
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
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
				'selector' => '{{WRAPPER}} .tf-widget-slider .tf-heading h2',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Title Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-widget-slider .tf-heading h2' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .tf-widget-slider .tf-heading p',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => __( 'Subtitle Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-widget-slider .tf-heading p' => 'color: {{VALUE}}',
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
		$settings  = $this->get_settings_for_display();
		$title     = $settings['title'];
		$subtitle  = $settings['subtitle'];
		$count     = $settings['count'];
		$style     = $settings['style'];
		$locations = $settings['locations'];
		if ( is_array( $locations ) ) {
			$locations = implode( ',', $locations );
		}
		echo do_shortcode( '[tf_apartment title="' . $title . '" subtitle="' . $subtitle . '" locations="' . $locations . '" style="' . $style . '" count="' . $count . '"]' );


	}


}
