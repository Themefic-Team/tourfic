<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Description
 */
class Description extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-description';
	}

	public function get_title() {
		return esc_html__( 'Description', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-text';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'description',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-description'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-description/before-style-controls', $this );
		$this->tf_description_style_controls();
		do_action( 'tf/single-description/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_description_content',[
            'label' => esc_html__('Description', 'tourfic'),
        ]);

        do_action( 'tf/single-description/before-content/controls', $this );

		$this->add_responsive_control('description-align',[
			'label' => esc_html__('Alignment', 'tourfic'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'left' => [
					'title' => esc_html__('Left', 'tourfic'),
					'icon' => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'tourfic'),
					'icon' => 'eicon-text-align-center',
				],
				'right' => [
					'title' => esc_html__('Right', 'tourfic'),
					'icon' => 'eicon-text-align-right',
				],
			],
			'toggle' => true,
			'selectors' => [
				'{{WRAPPER}} .tf-head-title' => 'text-align: {{VALUE}};',
			]
		]);

	    do_action( 'tf/single-description/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_description_style_controls() {
		$this->start_controls_section( 'description_style', [
			'label' => esc_html__( 'Description Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_description_color', [
			'label'     => esc_html__( 'Description Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-post-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Description Typography', 'tourfic' ),
			'name'     => "tf_description_typography",
			'selector' => "{{WRAPPER}} .tf-post-title",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        ?>
        <div class="tf-single-template__two tf-single-description">
            <div class="tf-short-description">
                <?php 
                if(strlen(get_the_content()) > 300 ){
                    echo esc_html( wp_strip_all_tags(\Tourfic\Classes\Helper::tourfic_character_limit_callback(get_the_content(), 300)) ) . '<span class="tf-see-description">See more</span>';
                }else{
                    the_content(); 
                }
                ?>
            </div>
            <div class="tf-full-description">
                <?php 
                    the_content();
                    echo '<span class="tf-see-less-description"> See less</span>';
                ?>
            </div>
        </div>
        <?php
	}
}
