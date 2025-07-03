<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Archive;

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
 * Search Form Horizontal
 */
class Sidebar extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-sidebar';
	}

	public function get_title() {
		return esc_html__( 'Tourfic Sidebar', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-sidebar';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'sidebar',
			'tourfic',
			'tf'
        ];
    }

    public function get_sidebar_options(){
        global $wp_registered_sidebars;

        $sidebar_options = [];

        if (!empty($wp_registered_sidebars)) {
            foreach ($wp_registered_sidebars as $sidebar) {
                $sidebar_options[$sidebar['id']] = $sidebar['name'];
            }
        }

        return $sidebar_options;
    }

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/sidebar/before-style-controls', $this );
		$this->tf_sidebar_style_controls();
		$this->tf_widget_style_controls();
		do_action( 'tf/sidebar/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_sidebar_content',[
            'label' => esc_html__('Sidebar', 'tourfic'),
        ]);

        do_action( 'tf/sidebar/before-content/controls', $this );

        //sidebar
		$this->add_control('sidebar',[
            'type'     => Controls_Manager::SELECT,
            'label'    => esc_html__( 'Sidebar', 'tourfic' ),
            'options'  => $this->get_sidebar_options(),
        ]);
		
		// Design options
		$this->add_control('design',[
            'type'     => Controls_Manager::SELECT,
            'label'    => esc_html__( 'Design', 'tourfic' ),
            'options'  => [
                'design-1' => esc_html__( 'Design 1', 'tourfic' ),
                'design-2' => esc_html__( 'Design 2', 'tourfic' ),
            ],
            'default'  => 'design-1',
        ]);

	    do_action( 'tf/sidebar/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_sidebar_style_controls() {
		$this->start_controls_section( 'sidebar_style_general', [
			'label' => __( 'Sidebar Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( "tf_sidebar_margin", [
			'label'      => __( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-sidebar__design-1 #tf__booking_sidebar" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-sidebar__design-1 .tf-car-archive-sidebar" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-sidebar__design-2 .tf-archive-right" => $this->tf_apply_dim( 'margin' ),
			],
		] );
		$this->add_responsive_control( "tf_sidebar_padding", [
			'label'      => __( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-sidebar__design-1 #tf__booking_sidebar" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-sidebar__design-1 .tf-car-archive-sidebar" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-sidebar__design-2 .tf-archive-right" => $this->tf_apply_dim( 'padding' ),
			],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'      => "tf_sidebar_bg_color",
			'label'     => __( 'Background Color', 'tourfic' ),
			'types'     => [
				'classic',
				'gradient',
			],
			'selector'  => "{{WRAPPER}} .tf-sidebar__design-1 #tf__booking_sidebar,
                            {{WRAPPER}} .tf-sidebar__design-1 .tf-car-archive-sidebar,
                            {{WRAPPER}} .tf-sidebar__design-2 .tf-archive-right",
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "tf_sidebar_border",
			'selector'  => "{{WRAPPER}} .tf-sidebar__design-1 #tf__booking_sidebar,
                            {{WRAPPER}} .tf-sidebar__design-1 .tf-car-archive-sidebar,
                            {{WRAPPER}} .tf-sidebar__design-2 .tf-archive-right",
		] );
		$this->add_control( "tf_sidebar_border_radius", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-sidebar__design-1 #tf__booking_sidebar" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-sidebar__design-1 .tf-car-archive-sidebar" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-sidebar__design-2 .tf-archive-right" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'label'    => __( 'Box Shadow', 'tourfic' ),
			'name'     => 'tf_sidebar_shadow',
			'selector' => "{{WRAPPER}} .tf-sidebar__design-1 #tf__booking_sidebar,
                            {{WRAPPER}} .tf-sidebar__design-1 .tf-car-archive-sidebar,
                            {{WRAPPER}} .tf-sidebar__design-2 .tf-archive-right",
			'exclude'  => [
				'box_shadow_position',
			],
		] );
        
		$this->end_controls_section();
	}

    protected function tf_widget_style_controls() {
		$this->start_controls_section( 'widget_style', [
			'label' => __( 'Widget Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_responsive_control( "widget_gap", [
			'label'      => esc_html__( 'Widget Gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 20,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .widget" => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control( 'tf_widget_title_color', [
			'label'     => __( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-widget-title span' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => __( 'Title Typography', 'tourfic' ),
			'name'     => "tf_widget_title_typography",
			'selector' => "{{WRAPPER}} .tf-widget-title span",
		]);

		$this->add_responsive_control( "widget_title_gap", [
			'label'      => esc_html__( 'Title Gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-widget-title span" => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$design    = !empty( $settings['design'] ) ? $settings['design'] : 'design-1';
		$sidebar   = !empty( $settings['sidebar'] ) ? $settings['sidebar'] : '';
		
        if($design == 'design-1'): ?>
            <div class="tf-sidebar__design-1">
                <?php
                if ( is_post_type_archive('tf_carrental') ) : ?>
                    <div class="tf-car-archive-sidebar">
                        <div class="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h4><?php esc_html_e("Filter", "tourfic") ?></h4>
                            <button class="filter-reset-btn"><?php esc_html_e("Reset", "tourfic"); ?></button>
                        </div>
                        <?php if ( is_active_sidebar( $sidebar ) ) { ?>
                            <?php dynamic_sidebar( $sidebar ); ?>
                        <?php } ?>
                    </div>
                <?php else : ?>
                    <?php if ( is_active_sidebar( $sidebar ) ) { ?>
                        <div id="tf__booking_sidebar">
                            <?php dynamic_sidebar( $sidebar ); ?>
                        </div>
                    <?php } ?>
                <?php endif; ?>
            </div>
        <?php elseif($design == 'design-2'): ?>
            <div class="tf-sidebar__design-2">
                <div class="tf-details-right tf-sitebar-widgets tf-archive-right">
                    <div class="tf-filter-wrapper">
                        <div class="tf-filter-title">
                            <h2 class="tf-section-title"><?php esc_html_e("Filter", "tourfic"); ?></h2>
                            <button class="filter-reset-btn"><?php esc_html_e("Reset", "tourfic"); ?></button>
                        </div>   
                        <?php if ( is_active_sidebar( $sidebar ) ) { ?>
                        <div id="tf__booking_sidebar">
                            <?php dynamic_sidebar( $sidebar ); ?>
                        </div>
                        <?php } ?>
                    </div> 
                </div>
            </div>
        <?php endif;
	}

    /**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};";
	}
}
