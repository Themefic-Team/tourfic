<?php

namespace Tourfic\App\Widgets\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search Form Horizontal
 */
class Toggle extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-toggle';
	}

	public function get_title() {
		return esc_html__( 'Archive Toggle', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-post-list';
	}

	public function get_categories() {
		return [ 'tourfic' ];
	}

	public function get_keywords(){
        return [
            'hotels',
            'tours',
            'apartments',
            'cars',
            'rentals',
            'services',
        ];
    }

	protected function register_controls() {
        $this->tf_content_layout_controls();

		do_action( 'tf/toggle/before-style-controls', $this );
		$this->tf_toggle_btn_style_controls();
		do_action( 'tf/toggle/after-style-controls', $this );
	}
	
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$design        = !empty( $settings['design'] ) ? $settings['design'] : 'design-1';
		$active_layout = isset( $settings['active_layout'] ) ? $settings['active_layout'] : 'list';
        if($design == 'design-1'):
		?>
            <div class="tf-toggle__style-1">
                <div class="tf-icon tf-serach-layout-list tf-list-active tf-grid-list-layout <?php echo $active_layout=="list" ? esc_attr('active') : ''; ?>" data-id="list-view">
                    <div class="defult-view">
                        <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="12" height="2" fill="white"/>
                        <rect x="14" width="2" height="2" fill="white"/>
                        <rect y="5" width="12" height="2" fill="white"/>
                        <rect x="14" y="5" width="2" height="2" fill="white"/>
                        <rect y="10" width="12" height="2" fill="white"/>
                        <rect x="14" y="10" width="2" height="2" fill="white"/>
                        </svg>
                    </div>
                    <div class="active-view">
                        <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="12" height="2" fill="#0E3DD8"/>
                        <rect x="14" width="2" height="2" fill="#0E3DD8"/>
                        <rect y="5" width="12" height="2" fill="#0E3DD8"/>
                        <rect x="14" y="5" width="2" height="2" fill="#0E3DD8"/>
                        <rect y="10" width="12" height="2" fill="#0E3DD8"/>
                        <rect x="14" y="10" width="2" height="2" fill="#0E3DD8"/>
                        </svg>
                    </div>
                </div>
                <div class="tf-icon tf-serach-layout-grid tf-grid-list-layout <?php echo $active_layout=="grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
                    <div class="defult-view">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="10" width="2" height="2" fill="#0E3DD8"/>
                        <rect x="10" y="5" width="2" height="2" fill="#0E3DD8"/>
                        <rect x="10" y="10" width="2" height="2" fill="#0E3DD8"/>
                        <rect x="5" width="2" height="2" fill="#0E3DD8"/>
                        <rect x="5" y="5" width="2" height="2" fill="#0E3DD8"/>
                        <rect x="5" y="10" width="2" height="2" fill="#0E3DD8"/>
                        <rect width="2" height="2" fill="#0E3DD8"/>
                        <rect y="5" width="2" height="2" fill="#0E3DD8"/>
                        <rect y="10" width="2" height="2" fill="#0E3DD8"/>
                        </svg>
                    </div>
                    <div class="active-view">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="10" width="2" height="2" fill="white"/>
                        <rect x="10" y="5" width="2" height="2" fill="white"/>
                        <rect x="10" y="10" width="2" height="2" fill="white"/>
                        <rect x="5" width="2" height="2" fill="white"/>
                        <rect x="5" y="5" width="2" height="2" fill="white"/>
                        <rect x="5" y="10" width="2" height="2" fill="white"/>
                        <rect width="2" height="2" fill="white"/>
                        <rect y="5" width="2" height="2" fill="white"/>
                        <rect y="10" width="2" height="2" fill="white"/>
                        </svg>
                    </div>
                </div>
            </div>
        <?php elseif($design == 'design-2'): ?>
            <ul class="tf-archive-view tf-toggle__style-2">  
                <li class="tf-archive-view-item tf-archive-list-view <?php echo $active_layout == "list" ? esc_attr('active') : ''; ?>" data-id="list-view">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M1.33398 7.59996C1.33398 6.82778 1.49514 6.66663 2.26732 6.66663H13.734C14.5062 6.66663 14.6673 6.82778 14.6673 7.59996V8.39996C14.6673 9.17214 14.5062 9.33329 13.734 9.33329H2.26732C1.49514 9.33329 1.33398 9.17214 1.33398 8.39996V7.59996Z"
                            stroke="#6E655E" stroke-linecap="round"/>
                        <path d="M1.33398 2.26665C1.33398 1.49447 1.49514 1.33331 2.26732 1.33331H13.734C14.5062 1.33331 14.6673 1.49447 14.6673 2.26665V3.06665C14.6673 3.83882 14.5062 3.99998 13.734 3.99998H2.26732C1.49514 3.99998 1.33398 3.83882 1.33398 3.06665V2.26665Z"
                            stroke="#6E655E" stroke-linecap="round"/>
                        <path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
                            stroke="#6E655E" stroke-linecap="round"/>
                    </svg>
                </li>
                <li class="tf-archive-view-item tf-archive-grid-view <?php echo $active_layout == "grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M1.33398 12C1.33398 10.973 1.33398 10.4595 1.56514 10.0823C1.69448 9.87127 1.87194 9.69381 2.08301 9.56447C2.46021 9.33331 2.97369 9.33331 4.00065 9.33331C5.02761 9.33331 5.54109 9.33331 5.9183 9.56447C6.12936 9.69381 6.30682 9.87127 6.43616 10.0823C6.66732 10.4595 6.66732 10.973 6.66732 12C6.66732 13.0269 6.66732 13.5404 6.43616 13.9176C6.30682 14.1287 6.12936 14.3062 5.9183 14.4355C5.54109 14.6666 5.02761 14.6666 4.00065 14.6666C2.97369 14.6666 2.46021 14.6666 2.08301 14.4355C1.87194 14.3062 1.69448 14.1287 1.56514 13.9176C1.33398 13.5404 1.33398 13.0269 1.33398 12Z"
                            stroke="#6E655E" stroke-width="1.2"/>
                        <path d="M9.33398 12C9.33398 10.973 9.33398 10.4595 9.56514 10.0823C9.69448 9.87127 9.87194 9.69381 10.083 9.56447C10.4602 9.33331 10.9737 9.33331 12.0007 9.33331C13.0276 9.33331 13.5411 9.33331 13.9183 9.56447C14.1294 9.69381 14.3068 9.87127 14.4362 10.0823C14.6673 10.4595 14.6673 10.973 14.6673 12C14.6673 13.0269 14.6673 13.5404 14.4362 13.9176C14.3068 14.1287 14.1294 14.3062 13.9183 14.4355C13.5411 14.6666 13.0276 14.6666 12.0007 14.6666C10.9737 14.6666 10.4602 14.6666 10.083 14.4355C9.87194 14.3062 9.69448 14.1287 9.56514 13.9176C9.33398 13.5404 9.33398 13.0269 9.33398 12Z"
                            stroke="#6E655E" stroke-width="1.2"/>
                        <path d="M1.33398 3.99998C1.33398 2.97302 1.33398 2.45954 1.56514 2.08233C1.69448 1.87127 1.87194 1.69381 2.08301 1.56447C2.46021 1.33331 2.97369 1.33331 4.00065 1.33331C5.02761 1.33331 5.54109 1.33331 5.9183 1.56447C6.12936 1.69381 6.30682 1.87127 6.43616 2.08233C6.66732 2.45954 6.66732 2.97302 6.66732 3.99998C6.66732 5.02694 6.66732 5.54042 6.43616 5.91762C6.30682 6.12869 6.12936 6.30615 5.9183 6.43549C5.54109 6.66665 5.02761 6.66665 4.00065 6.66665C2.97369 6.66665 2.46021 6.66665 2.08301 6.43549C1.87194 6.30615 1.69448 6.12869 1.56514 5.91762C1.33398 5.54042 1.33398 5.02694 1.33398 3.99998Z"
                            stroke="#6E655E" stroke-width="1.2"/>
                        <path d="M9.33398 3.99998C9.33398 2.97302 9.33398 2.45954 9.56514 2.08233C9.69448 1.87127 9.87194 1.69381 10.083 1.56447C10.4602 1.33331 10.9737 1.33331 12.0007 1.33331C13.0276 1.33331 13.5411 1.33331 13.9183 1.56447C14.1294 1.69381 14.3068 1.87127 14.4362 2.08233C14.6673 2.45954 14.6673 2.97302 14.6673 3.99998C14.6673 5.02694 14.6673 5.54042 14.4362 5.91762C14.3068 6.12869 14.1294 6.30615 13.9183 6.43549C13.5411 6.66665 13.0276 6.66665 12.0007 6.66665C10.9737 6.66665 10.4602 6.66665 10.083 6.43549C9.87194 6.30615 9.69448 6.12869 9.56514 5.91762C9.33398 5.54042 9.33398 5.02694 9.33398 3.99998Z"
                            stroke="#6E655E" stroke-width="1.2"/>
                    </svg>
                </li>
            </ul>
        <?php else: ?>
            <div class="tf-toggle__style-2">
                <a href="#list-view" data-id="list-view" class="change-view <?php echo $active_layout=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
                <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $active_layout=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
            </div>
        <?php endif;
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_listing_toggle',[
			'label' => esc_html__('Content', 'tourfic'),
		]);

        do_action( 'tf/before-toggle/controls', $this );
		
		// Design options
		$this->add_control('design',[
			'type'     => Controls_Manager::SELECT,
			'label'    => esc_html__( 'Design', 'tourfic' ),
			'options'  => [
				'design-1' => esc_html__( 'Design 1', 'tourfic' ),
				'design-2' => esc_html__( 'Design 2', 'tourfic' ),
				'design-3' => esc_html__( 'Design 3', 'tourfic' ),
			],
			'default'  => 'design-1',
		]);

        $this->add_control('active_layout',[
			'label'   => __( 'Active Layout', 'tourfic' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'grid' => [
					'title' => esc_html__( 'Grid', 'tourfic' ),
					'icon'  => 'eicon-gallery-grid',
				],
				'list' => [
					'title' => esc_html__( 'List', 'tourfic' ),
					'icon'  => 'eicon-post-list',
				],
			],
			'default' => 'list',
			'toggle'  => false,
		]);

	    do_action( 'tf/after-toggle/controls', $this );

        $this->end_controls_section();
    }

	protected function tf_toggle_btn_style_controls() {
		$this->start_controls_section( 'toggle_btn_style', [
			'label' => __( 'Toggle Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		
		$this->add_responsive_control( "toggle_btn_width", [
			'label'      => esc_html__( 'Button width', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => 'width: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => 'width: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => 'width: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( "toggle_btn_height", [
			'label'      => esc_html__( 'Button Height', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => 'height: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => 'height: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => 'height: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( "toggle_margin", [
			'label'      => __( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => $this->tf_apply_dim( 'margin' ),
			],
		] );

		$this->start_controls_tabs( "tabs_toggle_icon_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_toggle_icon_normal", [
			'label' => __( 'Normal', 'tourfic' ),
		] );
		$this->add_control( 'tf_toggle_icon_color', [
			'label'     => __( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-icon i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon svg path, {{WRAPPER}} .tf-archive-head .tf-icon svg rect" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => "toggle_icon_bg_color",
			'label'    => __( 'Background Color', 'tourfic' ),
			'types'    => [
				'classic',
				'gradient',
			],
			'selector' => "{{WRAPPER}} .tf-archive-head .tf-icon, 
							{{WRAPPER}} .tf-archive-view li.tf-archive-view-item, 
							{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view, 
							{{WRAPPER}} .tf-archive-header .tf-archive-view ul li",
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "toggle_icon_border",
			'selector' => "{{WRAPPER}} .tf-archive-head .tf-icon, 
							{{WRAPPER}} .tf-archive-view li.tf-archive-view-item, 
							{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view, 
							{{WRAPPER}} .tf-archive-header .tf-archive-view ul li",
		] );
		$this->add_control( "toggle_icon_border_radius", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_toggle_icon_hover", [
			'label' => __( 'Active/Hover', 'tourfic' ),
		] );
		$this->add_control( "toggle_icon_color_hover", [
			'label'     => __( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-icon.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon.active svg path, {{WRAPPER}} .tf-archive-head .tf-icon.active svg rect" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon:hover svg path, {{WRAPPER}} .tf-archive-head .tf-icon:hover svg rect" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => "toggle_icon_bg_color_hover",
			'label'    => __( 'Background Color', 'tourfic' ),
			'types'    => [
				'classic',
				'gradient',
			],
			'selector' => "{{WRAPPER}} .tf-archive-head .tf-icon:hover, 
							{{WRAPPER}} .tf-archive-head .tf-icon.active, 
							{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover, 
							{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active, 
							{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover, 
							{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active, 
							{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover, 
							{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active",
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "toggle_icon_border_hover",
			'selector' => "{{WRAPPER}} .tf-archive-head .tf-icon:hover, 
							{{WRAPPER}} .tf-archive-head .tf-icon.active, 
							{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover, 
							{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active, 
							{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover, 
							{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active, 
							{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover, 
							{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active",
		] );
		$this->add_control( "toggle_icon_border_radius_hover", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon:hover" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-head .tf-icon.active" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active" => $this->tf_apply_dim( 'border-radius' ),
			],
		]);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Button tabs--------*/

		$this->end_controls_section();
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
