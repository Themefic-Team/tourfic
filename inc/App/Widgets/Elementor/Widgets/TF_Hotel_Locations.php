<?php

namespace Tourfic\App\Widgets\Elementor\Widgets;
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Hotel Locations
 *
 */
class TF_Hotel_Locations extends \Elementor\Widget_Base {

	use \Tourfic\Traits\Singleton;

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'hotel-locations';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Hotel Locations', 'tourfic' );
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
			'hotel_location_content',
			[
				'label' => esc_html__( 'Content', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ids',
			[
				'label'       => esc_html__( 'Hotel Location Ids', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => esc_html__( 'Specify the ids of the locations which you want to show. Separated by commas (,). Default to blank', 'tourfic' ),
			]
		);

		//post limit
		$this->add_control(
			'limit',
			[
				'label'       => esc_html__( 'Number of Locations', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'step'        => 1,
				'min'         => 1,
				'default'     => 6,
				'description' => esc_html__( 'Number of locations to show. Default to 6', 'tourfic' ),
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'tourfic' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => esc_html__( 'Ascending', 'tourfic' ),
					'DESC' => esc_html__( 'Descending', 'tourfic' ),
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order By', 'tourfic' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'none'       => esc_html__( 'None', 'tourfic' ),
					'type'       => esc_html__( 'Type', 'tourfic' ),
					'title'      => esc_html__( 'Title', 'tourfic' ),
					'name'       => esc_html__( 'Name', 'tourfic' ),
					'date'       => esc_html__( 'Date', 'tourfic' ),
					'ID'         => esc_html__( 'ID', 'tourfic' ),
					'menu_order' => esc_html__( 'Menu Order', 'tourfic' ),
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
			'hotel_location_style',
			[
				'label' => esc_html__( 'Style', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'hotel_location_title_typography',
				'label'    => esc_html__( 'Location Title Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .recomended_place_info_header h3',
			]
		);

		$this->add_control(
			'hotel_location_title_color',
			[
				'label'     => esc_html__( 'Location Title Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .recomended_place_info_header h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hotel_location_title_hover_color',
			[
				'label'     => esc_html__( 'Location Title Hover Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .single_recomended_item:hover .recomended_place_info_header h3' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'hotel_location_subtitle_typography',
				'label'    => esc_html__( 'Location Subitle Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .recomended_place_info_header p',
			]
		);

		$this->add_control(
			'hotel_location_subtitle_color',
			[
				'label'     => esc_html__( 'Location Subtitle Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .recomended_place_info_header p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hotel_location_subtitle_hover_color',
			[
				'label'     => esc_html__( 'Location Subtitle Hover Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .single_recomended_item:hover .recomended_place_info_header p' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'tf_info_options',
			[
				'label'     => esc_html__( 'Info Background', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'tf_info_background',
				'label'    => esc_html__( 'Background Color', 'tourfic' ),
				'types'    => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .recomended_place_info_header',
			]
		);
		$this->add_control(
			'tf_info_hover_options',
			[
				'label'     => esc_html__( 'Info Hover Background', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'tf_info_hover_background',
				'label'    => esc_html__( 'Background Color', 'tourfic' ),
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

		echo do_shortcode( '[hotel_locations' . $ids_txt . ' hide_empty="' . $hide_empty . '" limit="' . $settings['limit'] . '" order="' . $settings['order'] . '" orderby="' . $settings['orderby'] . '"]' );

	}

}
