<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Gallery
 */
class Gallery extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-gallery';
	}

	public function get_title() {
		return esc_html__( 'Gallery', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'gallery',
			'tourfic',
			'media',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-gallery'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-gallery/before-style-controls', $this );
		$this->tf_gallery_style_controls();
		do_action( 'tf/single-gallery/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_gallery_content',[
            'label' => esc_html__('Gallery', 'tourfic'),
        ]);

        do_action( 'tf/single-gallery/before-content/controls', $this );

        $this->add_control('gallery_style',[
            'label' => esc_html__('Gallery Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1 - Bottom Nav', 'tourfic'),
                'style2' => esc_html__('Style 2 - Slider', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-gallery/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_gallery_style_controls() {
		$this->start_controls_section( 'share_style_section', [
			'label' => esc_html__( 'Share Icon Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_responsive_control( "tf_share_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
			],
			'range'      => [
				'px' => [
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .share-toggle i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-share-toggle i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .share-toggle svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-share-toggle svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

        $this->add_responsive_control( "tf_share_icon_box_size", [
			'label'      => esc_html__( 'Box Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
			],
			'range'      => [
				'px' => [
					'min'  => 30,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .share-toggle" => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
				"{{WRAPPER}} .tf-share-toggle" => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
			],
            'condition' => [
				'share_style' => ['style1', 'style2'],
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();

        //gallery style
        $style = !empty($settings['gallery_style']) ? $settings['gallery_style'] : 'style1';
        
       
        // Style 1: Dropdown with icons only
        if ($style == 'style1') {
            ?>
            
            <?php
        }
        // Style 2: Off-canvas style
        elseif ($style == 'style2') {
            ?>
            
            <?php
        }
        // Style 3: Dropdown with text labels
        elseif ($style == 'style3') {
            ?>

            <?php
        }
    }

    /**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property, $important = false ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} " . ($important ? '!important' : '') . ";";
	}
}
