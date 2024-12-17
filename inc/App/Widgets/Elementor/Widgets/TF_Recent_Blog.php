<?php

namespace Tourfic\App\Widgets\Elementor\Widgets;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Recent Blog
 * @since 2.9.0
 * @author Abu Hena
 */
class TF_Recent_Blog extends \Elementor\Widget_Base {

	use \Tourfic\Traits\Singleton;

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'recent-blog';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Recent Blog', 'tourfic' );
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
				'label' => esc_html__( 'Settings', 'tourfic' ),
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

		//get the location IDs
		$cats = get_terms( array(
			'taxonomy' => 'category',
			'orderby'    => 'count',
			'hide_empty' => 0,
		) );
		
		$term_ids = [];
		foreach($cats as $cat){
			$term_ids[$cat->term_id]  = $cat->name;
		}

		$this->add_control(
			'cats',
			[
				'label'       => esc_html__( 'Categories', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'description' => esc_html__( 'Choose category.', 'tourfic' ),
				'options'     => $term_ids,
				'multiple' => true,
			]
		);

		$this->add_control(
			'count',
			[
				'label'       => esc_html__( 'Total Blogs', 'tourfic' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => esc_html__( 'Number of total blogs. Min 3.', 'tourfic' ),
				'min'         => 1,
				'default'     => 3,
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
				'selector' => '{{WRAPPER}} .tf-recent-blog-wrapper .tf-heading h2',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-recent-blog-wrapper .tf-heading h2' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .tf-recent-blog-wrapper .tf-heading p',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => esc_html__( 'Subtitle Color', 'tourfic' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-recent-blog-wrapper .tf-heading p' => 'color: {{VALUE}}',
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
		$cats = $settings['cats'];
		if(is_array($cats)){
			$cats = implode(',',$cats);
		}
        echo do_shortcode('[tf_recent_blog title="'.$title.'" subtitle="'.$subtitle.'" cats="'.$cats.'" count="' .$count. '"]');


	}


}
