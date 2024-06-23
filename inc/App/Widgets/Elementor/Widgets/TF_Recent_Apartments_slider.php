<?php

namespace Tourfic\App\Widgets\Elementor\Widgets;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Recent Apartment Slider
 * @author Foysal
 */
class TF_Recent_Apartments_slider extends \Elementor\Widget_Base {

	use \Tourfic\Traits\Singleton;

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'recent-apartment-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Recent Apartments Slider', 'tourfic' );
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
				'label' => esc_html__( 'Content', 'tourfic' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 1,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => esc_html__( 'Sub-Title', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 2,
			]
		);

		$this->add_control(
			'count',
			[
				'label' => esc_html__( 'Total Apartments', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => esc_html__( 'Number of total apartments to show. Min 3. Default to 7', 'tourfic' ),
				'min' => 3,
				'step' => 1,
				'default' => 7,
			]
		);

		$this->add_control(
			'slidestoshow',
			[
				'label' => esc_html__( 'Slide to Show', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => esc_html__( 'Number of apartments to show on the slider at a time. Min 1. Default to 3', 'tourfic' ),
				'min' => 1,
				'step' => 1,
				'default' => 3,
			]
		);
        

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Style', 'tourfic' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Title Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf-widget-slider .tf-heading h2',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
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
				'name' => 'subtitle_typography',
				'label' => esc_html__( 'Subtitle Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf-widget-slider .tf-heading p',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => esc_html__( 'Subtitle Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
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
		$settings = $this->get_settings_for_display();
		$title = $settings['title'];
		$subtitle = $settings['subtitle'];
		$count = $settings['count'];
		$slidestoshow = $settings['slidestoshow'];

        echo do_shortcode('[tf_recent_apartment title="' .$title. '" subtitle="' .$subtitle. '" count="' .$count. '" slidestoshow="' .$slidestoshow. '"]');

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
			<script>
				jQuery('.recent-apartment-slider .tf-slider-items-wrapper').slick({
					dots: true,
					arrows: false,
					infinite: true,
					speed: 300,
					//autoplay: true,
					autoplaySpeed: 2000,
					slidesToShow: 3,
					slidesToScroll: 1,
					responsive: [
						{
							breakpoint: 1024,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 1,
								infinite: true,
								dots: true
							}
						},
						{
							breakpoint: 600,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1
							}
						},
						{
							breakpoint: 480,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
					]
				});
			</script>
		<?php endif;
	}
}
