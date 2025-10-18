<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * FAQ
 */
class FAQ extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	public function get_name() {
		return 'tf-single-faq';
	}

	public function get_title() {
		return esc_html__( 'FAQ', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-help-o';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'frequently asked questions',
            'faq',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-faq'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-faq/before-style-controls', $this );
		$this->tf_faq_style_controls();
		$this->tf_tab_style_controls();
		$this->tf_tab_content_style_controls();
		do_action( 'tf/single-faq/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_faq_content',[
            'label' => esc_html__('FAQ', 'tourfic'),
        ]);

        do_action( 'tf/single-faq/before-content/controls', $this );
		
		$this->add_control('faq_style',[
            'label' => esc_html__('FAQ Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
                'style3' => esc_html__('Style 3', 'tourfic'),
                'style4' => esc_html__('Style 4', 'tourfic'),
            ],
        ]);

        $this->add_control('tf_faq_icon_postion',[
            'label'        => esc_html__('Toggle Icon Postion', 'tourfic'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Right', 'tourfic'),
            'label_off'    => __('Left', 'tourfic'),
            'default'      => 'right',
            'return_value' => 'right',
        ]);

        $this->add_control('tf_faq_item_divider',[
            'label'        => esc_html__('Item Devider', 'tourfic'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'tourfic'),
            'label_off'    => __('No', 'tourfic'),
            'default'      => 'yes',
            'return_value' => 'yes',
            'condition' => [
				'faq_style' => ['style1'],
			],
        ]);

        $this->add_control('open_icon',[
            'label' => esc_html__('Open Tab Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-minus',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'open_icon_comp',
        ]);

        $this->add_control('close_icon',[
            'label' => esc_html__('Close Tab Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-plus',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'close_icon_comp',
        ]);

	    do_action( 'tf/single-faq/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_faq_style_controls() {
		$this->start_controls_section( 'faq_title_style', [
			'label' => esc_html__( 'FAQ Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title",
		]);

        $this->add_responsive_control('title_align',[
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
            'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} h2.section-heading' => 'text-align: {{VALUE}};',
			],
		]);

        $this->add_responsive_control( "title_margin", [
			'label'      => __( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => $this->tf_apply_dim( 'margin' ),
				'{{WRAPPER}} h2.section-heading' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->end_controls_section();
	}

    protected function tf_tab_style_controls(){
        $this->start_controls_section('tf_section_faq_tab_style_settings',[
            'label' => esc_html__('Tab Style', 'tourfic'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(),[
            'name'     => 'tf_faq_tab_title_typography',
            'selector' => '{{WRAPPER}} .tf-faq-label, {{WRAPPER}} .tf-questions .tf-questions-col .tf-question .tf-faq-head .tf-faq-label',
        ]);
        $this->add_responsive_control('tf_faq_tab_icon_size',[
            'label'      => __('Icon Size', 'tourfic'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'size' => 16,
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 100,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                '{{WRAPPER}} .tf-faq-head i'   => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .tf-faq-head svg'   => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} #tf-faq-item .tf-faq-title svg'   => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('tf_faq_tab_icon_gap',[
            'label'      => __('Icon Gap', 'tourfic'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'size' => 16,
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 100,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                '{{WRAPPER}} .tf-faq-single-inner .tf-faq-collaps' => 'gap: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .tf-faq-col .tf-faq-head' => 'gap: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .tf-questions-col .tf-question .tf-faq-head' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // after change toggle icon postion, tab icon will be also change postion then this control will be work
        $this->add_responsive_control(
            'tf_faq_tab_item_gap',
            [
                'label'      => __('Item Gap', 'tourfic'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .tf-faq-single'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tf-faq-inner .tf-faq-single-inner'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tf-faq-inner .active .tf-faq-single-inner'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tf-questions-col .tf-question'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tf_faq_tab_padding',
            [
                'label'      => esc_html__('Padding', 'tourfic'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );

        $this->start_controls_tabs('tf_faq_header_tabs');
        # Normal State Tab
        $this->start_controls_tab('tf_faq_header_normal', [
            'label' => esc_html__('Normal', 'tourfic'),
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
        ]);

        $this->add_control( 'tf_faq_tab_bgtype', [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-faq-single .tf-faq-head" => 'background-color: {{VALUE}};',
			],
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
		]);
        $this->add_control('tf_faq_tab_text_color',[
            'label'     => esc_html__('Text Color', 'tourfic'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tf-faq-single .tf-faq-head .tf-faq-label' => 'color: {{VALUE}};',
                '{{WRAPPER}} .tf-questions-col .tf-question .tf-faq-head span.tf-faq-label' => 'color: {{VALUE}};',
                '{{WRAPPER}} #tf-faq-item .tf-faq-title.active h4' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_control('tf_faq_tab_icon_color',[
            'label'     => esc_html__('Icon Color', 'tourfic'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tf-faq-single .tf-faq-head i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .tf-faq-single .tf-faq-head svg' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .tf-questions-col .tf-question .tf-faq-head i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .tf-questions-col .tf-question .tf-faq-head svg' => 'fill: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(),[
            'name'     => 'tf_faq_tab_border',
            'label'    => esc_html__('Border', 'tourfic'),
            'selector' => '{{WRAPPER}} .tf-faq-single .tf-faq-head',
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
        ]);
        $this->add_responsive_control(
            'tf_faq_tab_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'tourfic'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );
        $this->end_controls_tab();

        # Hover State Tab
        $this->start_controls_tab(
            'tf_faq_header_hover',
            [
                'label' => esc_html__('Hover', 'tourfic'),
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );

        $this->add_control( 'tf_faq_tab_bgtype_hover', [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-faq-single .tf-faq-head:hover" => 'background-color: {{VALUE}};',
			],
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
		]);
        $this->add_control(
            'tf_faq_tab_text_color_hover',
            [
                'label'     => esc_html__('Text Color', 'tourfic'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head:hover .tf-faq-label' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );
        $this->add_control(
            'tf_faq_tab_icon_color_hover',
            [
                'label'     => esc_html__('Icon Color', 'tourfic'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head:hover svg' => 'fill: {{VALUE}}',
                ],
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );
        $this->add_control( 'tf_faq_tab_border_hover', [
			'label'     => __( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-faq-single .tf-faq-head:hover" => 'border-color: {{VALUE}};',
			],
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
		] );
        $this->end_controls_tab();

        #Active State Tab
        $this->start_controls_tab(
            'tf_faq_header_active',
            [
                'label' => esc_html__('Active', 'tourfic'),
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );
        $this->add_control( 'tf_faq_tab_bgtype_active', [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-faq-single .tf-faq-head.active" => 'background-color: {{VALUE}};',
			],
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
		]);
        $this->add_control(
            'tf_faq_tab_text_color_active',
            [
                'label'     => esc_html__('Text Color', 'tourfic'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head.active'                           => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head.active .tf-faq-label' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );
        $this->add_control(
            'tf_faq_tab_icon_color_active',
            [
                'label'     => esc_html__('Icon Color', 'tourfic'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head.active i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tf-faq-single .tf-faq-head.active svg' => 'fill: {{VALUE}}',
                ],
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );
        $this->add_control( 'tf_faq_tab_border_active', [
			'label'     => __( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-faq-single .tf-faq-head.active" => 'border-color: {{VALUE}};',
			],
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
		] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function tf_tab_content_style_controls(){
        $this->start_controls_section('tf_section_faq_tab_content_style_settings',[
            'label' => esc_html__('Content Style', 'tourfic'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'faq_content_bgtype', [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-faq-single .tf-faq-content" => 'background-color: {{VALUE}};',
			],
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
		]);

        $this->add_control('faq_content_text_color',[
            'label'     => esc_html__('Text Color', 'tourfic'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tf-faq-single .tf-faq-content' => 'color: {{VALUE}};',
                '{{WRAPPER}} .tf-questions-col .tf-question .tf-question-desc' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(),[
            'name'     => 'tf_faq_content_typography',
            'selector' => '{{WRAPPER}} .tf-faq-single .tf-faq-content, {{WRAPPER}} .tf-questions-col .tf-question .tf-question-desc',
        ]);
        $this->add_responsive_control('tf_faq_content_padding',[
            'label'      => esc_html__('Padding', 'tourfic'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors'  => [
                '{{WRAPPER}} .tf-faq-single .tf-faq-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'faq_style' => ['style1', 'style3'],
            ],
        ]);
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'tf_faq_content_border',
                'label'    => esc_html__('Border', 'tourfic'),
                'selector' => '{{WRAPPER}} .tf-faq-single .tf-faq-content',
                'condition' => [
                    'faq_style' => ['style1', 'style3'],
                ],
            ]
        );
        $this->end_controls_section();
    }

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();

        $description_key = 'description';
        if($post_type == 'tf_tours'){
            $description_key = 'desc';
        }

        if($post_type == 'tf_hotel'){
            $meta = get_post_meta($post_id, 'tf_hotels_opt', true);
			$faqs = ! empty( Helper::tf_data_types($meta['faq']) ) ? Helper::tf_data_types($meta['faq']) : '';
            $title = !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : '';
            
        } elseif($post_type == 'tf_tours'){
            $meta = get_post_meta($post_id, 'tf_tours_opt', true);
			$faqs = ! empty( Helper::tf_data_types($meta['faqs']) ) ? Helper::tf_data_types($meta['faqs']) : '';
            $title = !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : '';
            
        } elseif($post_type == 'tf_apartment'){
            $meta = get_post_meta($post_id, 'tf_apartment_opt', true);
			$faqs = ! empty( Helper::tf_data_types($meta['faq']) ) ? Helper::tf_data_types($meta['faq']) : '';
            $title = !empty($meta['faq_title']) ? esc_html($meta['faq_title']) : '';
            $desc = !empty($meta['faq_desc']) ? esc_html($meta['faq_desc']) : '';
            
        } elseif($post_type == 'tf_carrental'){
            $meta = get_post_meta($post_id, 'tf_carrental_opt', true);
			$faqs = ! empty( Helper::tf_data_types($meta['faq']) ) ? Helper::tf_data_types($meta['faq']) : '';
            $title = !empty($meta['faq_sec_title']) ? esc_html($meta['faq_sec_title']) : '';
            
        } else {
			return;
		}

        //faq style
        $style = !empty($settings['faq_style']) ? $settings['faq_style'] : 'style1';
        $item_divider = isset($settings['tf_faq_item_divider']) ? $settings['tf_faq_item_divider'] : '';
        
        if ($style == 'style1') {
            ?>
            <div class="tf-single-faq-section tf-single-faq-style1">
                <?php echo ! empty( $title ) ? '<h2 class="tf-title tf-section-title">' . esc_html( $title ) . '</h2>' : ''; ?>
				<?php echo isset($desc) && ! empty( $desc ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>

                <div class="tf-faq-inner">
                    <?php 
                    $faq_key = 1;    
                    foreach ( $faqs as $key => $faq ): ?>
                    <div class="tf-faq-single <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?> <?php echo $item_divider=='yes' ? esc_attr( 'has-devider' ) : ''; ?>">
                        <div class="tf-faq-single-inner">
                            <div class="tf-faq-collaps tf-faq-head <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?> <?php echo $settings['tf_faq_icon_postion'] === 'right' ? esc_attr('tf-faq-icon-right'): ''; ?>">
                                <?php if ($settings['tf_faq_icon_postion'] === '') {
                                    echo '<div class="faq-icon">';
                                    $this->tf_faq_toggle_icon($settings);
                                    echo '</div>'; 
                                }?>

                                <span class="tf-faq-label"><?php echo esc_html( $faq['title'] ); ?></span> 

                                <?php if ($settings['tf_faq_icon_postion'] === 'right') {
                                    echo '<div class="faq-icon">';
                                    $this->tf_faq_toggle_icon($settings); 
                                    echo '</div>'; 
                                }?>
                            </div>
                            <div class="tf-faq-content" style="<?php echo $faq_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                                <p><?php echo wp_kses_post( $faq[$description_key] ); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php $faq_key++; endforeach; ?>
                </div>
            </div>
            <?php
        } elseif ($style == 'style2') {
            ?>
            <div class="tf-single-template__two tf-questions-wrapper tf-single-faq-style2" id="tf-hotel-faq">
                <?php echo ! empty( $title ) ? '<h2 class="tf-section-title">' . esc_html( $title ) . '</h2>' : ''; ?>
				<?php echo isset($desc) && ! empty( $desc ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>

                <div class="tf-questions">
                    <?php 
                    if (count($faqs) >= 2) {
                        $faqchunks = array_chunk($faqs, ceil(count($faqs) / 2), true);
                        $faqfirstArray = $faqchunks[0];
                        $faqsecondArray = $faqchunks[1];
                        
                    }else{
                        $faqfirstArray = $faqs;
                    }
                    ?>
                    <?php if(!empty($faqfirstArray)){ ?>
                    <div class="tf-questions-col">
                        <?php foreach ($faqfirstArray as $key => $faq) { ?>
                        <div class="tf-question <?php echo $key == 0 ? 'tf-active' : ''; ?>">
                            <div class="tf-faq-head <?php echo $settings['tf_faq_icon_postion'] === 'right' ? esc_attr('tf-faq-icon-right'): ''; ?>">
                                <?php if ($settings['tf_faq_icon_postion'] === '') {
                                    echo '<div class="faq-icon">';
                                    $this->tf_faq_toggle_icon($settings);
                                    echo '</div>'; 
                                }?>

                                <span class="tf-faq-label"><?php echo esc_html( $faq['title'] ); ?></span>

                                <?php if ($settings['tf_faq_icon_postion'] === 'right') {
                                    echo '<div class="faq-icon">';
                                    $this->tf_faq_toggle_icon($settings); 
                                    echo '</div>'; 
                                }?>
                            </div>
                            <div class="tf-question-desc" style="<?php echo $key == 0 ? 'display: block;' : ''; ?>">
                                <?php echo wp_kses_post( $faq[$description_key] ); ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } if(!empty($faqsecondArray)){ ?>
                    <div class="tf-questions-col">
                        <?php foreach ($faqsecondArray as $key => $faq) { ?>
                        <div class="tf-question <?php echo $key == 0 ? 'tf-active' : ''; ?>">
                            <div class="tf-faq-head <?php echo $settings['tf_faq_icon_postion'] === 'right' ? esc_attr('tf-faq-icon-right'): ''; ?>">
                                <?php if ($settings['tf_faq_icon_postion'] === '') {
                                    echo '<div class="faq-icon">';
                                    $this->tf_faq_toggle_icon($settings);
                                    echo '</div>'; 
                                }?>

                                <span class="tf-faq-label"><?php echo esc_html( $faq['title'] ); ?></span>

                                <?php if ($settings['tf_faq_icon_postion'] === 'right') {
                                    echo '<div class="faq-icon">';
                                    $this->tf_faq_toggle_icon($settings); 
                                    echo '</div>'; 
                                }?>
                            </div>
                            <div class="tf-question-desc" style="<?php echo $key == 0 ? 'display: block;' : ''; ?>">
                                <?php echo wp_kses_post( $faq[$description_key] ); ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        } elseif ($style == 'style3') {
            ?>
            <div class="tf-single-faq-section tf-car-faq-section tf-single-faq-style3">
                <?php echo ! empty( $title ) ? '<h2 class="tf-title tf-section-title">' . esc_html( $title ) . '</h2>' : ''; ?>
				<?php echo isset($desc) && ! empty( $desc ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>
                
                <?php 
                $faq_key = 1;
                foreach ( $faqs as $key => $faq ): ?>
                    <div class="tf-faq-col tf-faq-single <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
                        <?php if(!empty($faq['title'])){ ?>
                            <div class="tf-faq-head <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?> <?php echo $settings['tf_faq_icon_postion'] === 'right' ? esc_attr('tf-faq-icon-right'): ''; ?>">
                                <?php if ($settings['tf_faq_icon_postion'] === '') {
                                    $this->tf_faq_toggle_icon($settings); 
                                }?>
                                <span class="tf-faq-label"><?php echo esc_html($faq['title']); ?></span>
                                <?php if ($settings['tf_faq_icon_postion'] === 'right') {
                                    $this->tf_faq_toggle_icon($settings); 
                                }?>
                            </div>
                        <?php } ?>

                        <div class="tf-question-desc tf-faq-content" style="<?php echo $faq_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                            <?php echo wp_kses_post( $faq[$description_key] ); ?>
                        </div>
                    </div>
                <?php $faq_key++; endforeach; ?>
            </div>
			<?php
        } elseif ($style == 'style4') {
            ?>
            <div class="tf-single-template__legacy tf-faq-wrapper tf-apartment-faq tf-single-faq-style4">
                <div class="tf-faq-sec-title">
					<?php echo ! empty( $title ) ? '<h2 class="section-heading">' . esc_html( $title ) . '</h2>' : ''; ?>
					<?php echo isset($desc) && ! empty( $desc ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>
                </div>

                <div class="tf-faq-content-wrapper">
                    <div class="tf-faq-items-wrapper">
						<?php foreach ( Helper::tf_data_types( $meta['faq'] ) as $key => $faq ): ?>
                            <div id="tf-faq-item">
                                <?php if(!empty($faq['title'])){ ?>
                                    <div class="tf-faq-title tf-faq-head <?php echo $key==0 ? esc_attr( 'active' ) : ''; ?> <?php echo $settings['tf_faq_icon_postion'] === 'right' ? esc_attr('tf-faq-icon-right'): ''; ?>">
                                        <?php if ($settings['tf_faq_icon_postion'] === '') {
                                            $this->tf_faq_toggle_icon($settings); 
                                        }?>
                                        <h4 class="tf-faq-label"><?php echo esc_html($faq['title']); ?></h4>
                                        <?php if ($settings['tf_faq_icon_postion'] === 'right') {
                                            $this->tf_faq_toggle_icon($settings); 
                                        }?>
                                    </div>
                                <?php } ?>
                                
                                <div class="tf-faq-desc" <?php echo $key === 0 ? 'style="display: block;"' : ''; ?>>
									<?php echo wp_kses_post( $faq[$description_key] ); ?>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
			<?php
        }
	}

    protected function tf_faq_toggle_icon($settings){
        $open_icon_migrated = isset($settings['__fa4_migrated']['open_icon']);
        $open_icon_is_new = empty($settings['open_icon_comp']);
        $close_icon_migrated = isset($settings['__fa4_migrated']['close_icon']);
        $close_icon_is_new = empty($settings['close_icon_comp']);
        
        echo '<span class="tf-faq-open-icon">';
        if ( $open_icon_is_new || $open_icon_migrated ) {
            if ( 'svg' === $settings['open_icon']['library'] ) {
                echo '<span class="fa-toggle fa-toggle-svg">';
                Icons_Manager::render_icon( $settings['open_icon'] );
                echo '</span>';
            }else{
                Icons_Manager::render_icon( $settings['open_icon'], [ 'aria-hidden' => 'true', 'class' => "fa-toggle" ] );
            }
        } else {
            echo '<i class="' . esc_attr( $settings['open_icon_comp'] ) . ' fa-toggle"></i>';
        }
        echo '</span>';

        echo '<span class="tf-faq-close-icon">';
        if ( $close_icon_is_new || $close_icon_migrated ) {
            if ( 'svg' === $settings['close_icon']['library'] ) {
                echo '<span class="fa-toggle fa-toggle-svg">';
                Icons_Manager::render_icon( $settings['close_icon'] );
                echo '</span>';
            }else{
                Icons_Manager::render_icon( $settings['close_icon'], [ 'aria-hidden' => 'true', 'class' => "fa-toggle" ] );
            }
        } else {
            echo '<i class="' . esc_attr( $settings['close_icon_comp'] ) . ' fa-toggle"></i>';
        }
        echo '</span>';
    }
}
