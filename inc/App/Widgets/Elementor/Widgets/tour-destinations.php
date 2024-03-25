<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Tour Destinations
 *
 */
class TF_Tour_Destinations extends \Elementor\Widget_Base {

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
		return __( 'Tour Destinations', 'tourfic' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-google-maps';
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
			'tour_destination_content',
			[
				'label' => __( 'Content', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ids',
			[
				'label'       => esc_html__( 'Tour Destination Ids', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => esc_html__( 'Specify the ids of the destinations which you want to show. Separated by commas (,). Default to blank', 'tourfic' ),
			]
		);

		$this->add_control(
			'limit',
			[
				'label'       => esc_html__( 'Number of Destinations', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'step'        => 1,
				'min'         => 1,
				'default'     => 6,
				'description' => esc_html__( 'Number of destinations to show. Default to 6', 'tourfic' ),
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'tourfic' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => __( 'Ascending', 'tourfic' ),
					'DESC' => __( 'Descending', 'tourfic' ),
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'tourfic' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'none'       => __( 'None', 'tourfic' ),
					'type'       => __( 'Type', 'tourfic' ),
					'title'      => __( 'Title', 'tourfic' ),
					'name'       => __( 'Name', 'tourfic' ),
					'date'       => __( 'Date', 'tourfic' ),
					'ID'         => __( 'ID', 'tourfic' ),
					'menu_order' => __( 'Menu Order', 'tourfic' ),
				],
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label'        => esc_html__( 'Hide Empty', 'tourfic' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'tourfic' ),
				'label_off'    => esc_html__( 'No', 'tourfic' ),
				'return_value' => 1,
				'default'      => 0,
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'tour_destination_style',
			[
				'label' => __( 'Style', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'tf_destination_title_typography',
				'label'    => __( 'Destination Title Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .recomended_place_info_header h3',
			]
		);

		$this->add_control(
			'tf_destination_title_color',
			[
				'label'     => __( 'Destination Title Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .recomended_place_info_header h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tf_destination_title_hover_color',
			[
				'label'     => __( 'Destination Title Hover Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_2,
				],
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .single_recomended_item:hover .recomended_place_info_header h3' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'tf_destination_subtitle_typography',
				'label'    => __( 'Destination Subitle Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .recomended_place_info_header p',
			]
		);

		$this->add_control(
			'tf_destination_subtitle_color',
			[
				'label'     => __( 'Destination Subtitle Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_3,
				],
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .recomended_place_info_header p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tf_destination_subtitle_hover_color',
			[
				'label'     => __( 'Destination Subtitle Hover Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_4,
				],
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .single_recomended_item:hover .recomended_place_info_header p' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'tf_info_options',
			[
				'label'     => __( 'Info Background', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'tf_info_background',
				'label'    => __( 'Background Color', 'tourfic' ),
				'types'    => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .recomended_place_info_header',
			]
		);
		$this->add_control(
			'tf_info_hover_options',
			[
				'label'     => __( 'Info Hover Background', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'tf_info_hover_background',
				'label'    => __( 'Background Color', 'tourfic' ),
				'types'    => [ 'classic', 'gradient', 'video' ],
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

		$settings   = $this->get_settings_for_display();
		$ids        = $settings['ids'];
		$ids_txt    = ! empty( $ids ) ? ' ids="' . $ids . '"' : '';
		$hide_empty = $settings['hide_empty'];

		echo do_shortcode( '[tour_destinations' . $ids_txt . ' hide_empty="' . $hide_empty . '" limit="' . $settings['limit'] . '" order="' . $settings['order'] . '" orderby="' . $settings['orderby'] . '"]' );

	}


}
