<?php

namespace Tourfic\App\Widgets\Elementor\Widgets;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Hotel Tour review slider
 * @since 2.8.9
 * @author Abu Hena
 */
class TF_Reviews_Slider extends \Elementor\Widget_Base {

	use \Tourfic\Traits\Singleton;

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
		return esc_html__( 'Review Slider', 'tourfic' );
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
				'label' => esc_html__( 'Slider Settings', 'tourfic' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'type',
			[
				'label' => esc_html__( 'Reviews Type', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'description' => esc_html__( 'Choose the reviews type you want to show.', 'tourfic' ),
				'options' => [
					'tf_hotel' => 'Hotel',
					'tf_tours' => 'Tour',
					'tf_apartment' => 'Apartment',
					'tf_carrental' => 'Car',
				],
				'default' => 'tf_hotel'
			]
		);

		$this->add_control(
			'count',
			[
				'label' => esc_html__( 'Total Reviews', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => esc_html__( 'Number of total reviews to show. Min 3.', 'tourfic' ),
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
				'description' => esc_html__( 'Enable Infinite Slider', 'tourfic' ),
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
		$dots = !empty($settings['dots']) && 'yes'==$settings['dots'] ? esc_attr('true') : esc_attr('false');
		$autoplay = $settings['autoplay'];
		$autoplay == 'yes' ? $autoplay = 'true' : $autoplay = 'false';
		$autoplay_speed = $settings['autoplay_speed'];
		$infinite = $settings['infinite'];
		$infinite == 'yes' ? $infinite = 'true' : $infinite = 'false';

        echo do_shortcode('[tf_reviews type="'.$type.'" count="' .$count. '" autoplay="'.$autoplay.'" arrows="'.$arrows.'" dots="'.$dots.'" speed="'.$autoplay_speed.'" infinite="'.$infinite.'"]');
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>

		<script>
			jQuery('.tf-reviews-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            autoplaySpeed: 2000,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        });
		</script>
	<?php endif;

	}

}
