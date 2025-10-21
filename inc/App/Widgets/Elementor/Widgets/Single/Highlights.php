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
 * Highlights
 */
class Highlights extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-highlights';
	}

	public function get_title() {
		return esc_html__( 'Highlights', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-kit-details';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'highlights',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-highlights'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-highlights/before-style-controls', $this );
		$this->tf_section_title_style_controls();
		$this->tf_card_style_controls();
		$this->tf_thumbnail_style_controls();
		$this->tf_icon_style_controls();
		$this->tf_title_style_controls();
		$this->tf_content_style_controls();
		do_action( 'tf/single-highlights/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_highlights_content',[
            'label' => esc_html__('Highlights', 'tourfic'),
        ]);

        do_action( 'tf/single-highlights/before-content/controls', $this );

		$this->add_control('service',[
			'label' => esc_html__( 'Service', 'tourfic' ),
			'type' => \Elementor\Controls_Manager::HIDDEN,
			'default' => $this->get_current_post_type(),
		]);

		$this->add_control('highlights_style',[
            'label' => esc_html__('Highlights Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-highlights/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_section_title_style_controls() {
		$this->start_controls_section( 'section_title_style', [
			'label' => esc_html__( 'Section Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally_single([
     			'tf_apartment' => [
     			    'highlights_style' => ['style2'],
     			],
     		]),
		] );

		$this->add_responsive_control('section_title_align',[
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

        $this->add_control( 'tf_section_title_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-text .section-heading' => 'color: {{VALUE}};',
				'{{WRAPPER}} .section-heading' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_section_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title, {{WRAPPER}} h2.section-heading",
		]);

		$this->add_responsive_control( "section_title_margin", [
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

	protected function tf_card_style_controls() {
		$this->start_controls_section( 'card_style', [
			'label' => esc_html__( 'Card Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally_single([
     			'tf_tours' => ['highlights_style' => ['style1']],
     			'tf_apartment' => ['highlights_style' => ['style1', 'style2']],
     		]),
		] );

		$this->add_responsive_control( "card_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-highlights-wrapper" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-features-block-wrapper .tf-feature-block" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( 'card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-highlights-wrapper" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-features-block-wrapper .tf-feature-block" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight" => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .tf-highlights-wrapper,
						   {{WRAPPER}} .tf-features-block-wrapper .tf-feature-block,
						   {{WRAPPER}} .tf-apt-highlights .tf-apt-highlight",
		] );

		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-highlights-wrapper" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-features-block-wrapper .tf-feature-block" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .tf-highlights-wrapper,
						   {{WRAPPER}} .tf-features-block-wrapper .tf-feature-block,
						   {{WRAPPER}} .tf-apt-highlights .tf-apt-highlight',
		]);
		
		$this->end_controls_section();
	}

	protected function tf_thumbnail_style_controls() {
		$this->start_controls_section( 'thumbnail_style', [
			'label' => esc_html__( 'Thumbnail Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally_single([
     			'tf_tours' => [
     			    'highlights_style' => ['style1', 'style2'],
     			],
     		]),
		] );

		$this->add_responsive_control('image_align',[
			'label' => esc_html__('Alignment', 'tourfic'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'row' => [
					'title' => esc_html__('Left', 'tourfic'),
					'icon' => 'eicon-text-align-left',
				],
				'row-reverse' => [
					'title' => esc_html__('Right', 'tourfic'),
					'icon' => 'eicon-text-align-right',
				],
			],
			'toggle' => true,
            'selectors'  => [
				'{{WRAPPER}} .tf-highlights-wrapper .tf-highlights-inner' => 'flex-direction: {{VALUE}};',
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item' => 'flex-direction: {{VALUE}};',
			],
		]);
		
		$this->add_responsive_control('thumbnail_height',[
			'label'      => esc_html__('Thumbnail Height', 'tourfic'),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 800,
					'step' => 1,
				],
				'em' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
				'%'  => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'size_units' => ['px', 'em', '%'],
			'selectors'  => [
				'{{WRAPPER}} .tf-highlights-wrapper .tf-highlights-inner .tf-highlights-icon img' => 'height: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-image img' => 'height: {{SIZE}}{{UNIT}};'
			],
		]);
		
		$this->add_responsive_control('thumbnail_width',[
			'label'      => esc_html__('Thumbnail Width', 'tourfic'),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 800,
					'step' => 1,
				],
				'em' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
				'%'  => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'size_units' => ['px', 'em', '%'],
			'selectors'  => [
				'{{WRAPPER}} .tf-highlights-wrapper .tf-highlights-inner .tf-highlights-icon img' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-image img' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control( "featured_badge_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-highlights-wrapper .tf-highlights-inner .tf-highlights-icon img' => $this->tf_apply_dim( 'padding' ),
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-image img' => $this->tf_apply_dim( 'padding' ),
			],
		]);

		$this->add_control( 'featured_badge_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-highlights-wrapper .tf-highlights-inner .tf-highlights-icon img' => 'background: {{VALUE}};',
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-image img' => 'background: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "featured_badge_border",
			'selector' => "{{WRAPPER}} .tf-highlights-wrapper .tf-highlights-inner .tf-highlights-icon img,
						   {{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-image img",
		]);

		$this->add_control( "thumbnail_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-highlights-wrapper .tf-highlights-inner .tf-highlights-icon img' => $this->tf_apply_dim( 'border-radius' ),
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-image img' => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

		$this->end_controls_section();
	}

	protected function tf_icon_style_controls() {
		$this->start_controls_section( 'icon_style', [
			'label' => esc_html__( 'Icon Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally_single([
     			'tf_apartment' => [
     			    'highlights_style' => ['style1', 'style2'],
     			],
     		]),
		] );

		$this->add_responsive_control( "tf_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-features-block-wrapper .tf-feature-block i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight .tf-apt-highlight-icon i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "tf_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-features-block-wrapper .tf-feature-block i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight .tf-apt-highlight-icon i" => 'color: {{VALUE}}',
			],
		] );

		$this->add_responsive_control( "tc_icon_gap", [
			'label'     => esc_html__( 'Icon Gap', 'tourfic' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors' => [
				"{{WRAPPER}} .tf-features-block-wrapper .tf-feature-block" => 'gap: {{SIZE}}px;',
				"{{WRAPPER}} .tf-apt-highlight .tf-apt-highlight-top" => 'gap: {{SIZE}}px;',
			],
		] );

		$this->end_controls_section();
	}

    protected function tf_title_style_controls() {
		$this->start_controls_section( 'title_style', [
			'label' => esc_html__( 'Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

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
				'{{WRAPPER}} .tf-feature-block .tf-feature-block-details h5' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight h4' => 'text-align: {{VALUE}};',
			],
		]);

        $this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-highlight-wrapper .tf-highlight-item .tf-highlight-text .section-heading' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-feature-block .tf-feature-block-details h5' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight h4' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title, {{WRAPPER}} h2.section-heading, 
						   {{WRAPPER}} .tf-feature-block .tf-feature-block-details h5,
						   {{WRAPPER}} .tf-apt-highlights .tf-apt-highlight h4",
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
				'{{WRAPPER}} .tf-feature-block .tf-feature-block-details h5' => $this->tf_apply_dim( 'margin' ),
				'{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight h4' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->end_controls_section();
	}

    protected function tf_content_style_controls() {
        $this->start_controls_section( 'content_style', [
			'label' => esc_html__( 'Content Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control('content_align',[
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
				'{{WRAPPER}} .highlights-list' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} .tf-highlight-description' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} .tf-feature-block .tf-feature-block-details p' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight p' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_control( 'tf_content_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .highlights-list' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-highlight-description' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-feature-block .tf-feature-block-details p' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-apt-highlights .tf-apt-highlight p' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_content_typography",
			'selector' => "{{WRAPPER}} .highlights-list, {{WRAPPER}} .tf-highlight-description, 
						   {{WRAPPER}} .tf-feature-block .tf-feature-block-details p,
						   {{WRAPPER}} .tf-apt-highlights .tf-apt-highlight p",
		]);

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type == 'tf_tours'){
            $this->tf_tour_highlight($settings);
        }elseif($this->post_type == 'tf_apartment'){
            $this->tf_apartment_highlight($settings);
        }else{
            return;
        }
    }

	private function tf_tour_highlight($settings) {
	    $meta = get_post_meta( $this->post_id, 'tf_tours_opt', true );
        $highlights = ! empty( $meta['additional_information'] ) ? $meta['additional_information'] : '';
		$style = !empty($settings['highlights_style']) ? $settings['highlights_style'] : 'style1';

		if($style == 'style1' && $highlights){ ?>
			<div class="tf-single-template__one tf-tour-highlights-style1">
				<div class="tf-highlights-wrapper tf-box tf-template-section">
					<div class="tf-highlights-inner tf-flex">
						<div class="tf-highlights-icon">
							<?php if ( ! empty( $meta['hightlights_thumbnail'] ) ): ?>
								<img src="<?php echo esc_url( $meta['hightlights_thumbnail'] ); ?>" alt="<?php esc_html_e( 'Highlights Icon', 'tourfic' ); ?>" />
							<?php else: ?>
								<img src="<?php echo esc_url(TF_ASSETS_APP_URL).'images/tour-highlights.png' ?>" alt="<?php esc_html_e( 'Highlights Icon', 'tourfic' ); ?>" />
							<?php endif; ?>
						</div>
						<div class="ft-highlights-details">
							<h2 class="tf-section-title"><?php echo !empty($meta['highlights-section-title']) ? esc_html($meta['highlights-section-title']) : ''; ?></h2>
							<div class="highlights-list"><?php echo wp_kses_post($highlights); ?></div>
						</div>
					</div>
				</div>
			</div>
        	<?php 
		} elseif ($style == 'style2') {
        	?>
            <div class="tf-single-template__legacy tf-tour-highlights-style2">
				<div class="tf-highlight-wrapper">
					<div class="tf-highlight-content">
						<div class="tf-highlight-item">
							<div class="tf-highlight-text">
								<h2 class="section-heading"><?php echo !empty($meta['highlights-section-title']) ? esc_html($meta['highlights-section-title']) : ''; ?></h2>
								<div class="tf-highlight-description">
									<?php echo wp_kses_post($highlights); ?>
								</div>
							</div>
							<?php if ( ! empty( $meta['hightlights_thumbnail'] ) ): ?>
								<div class="tf-highlight-image">
									<img src="<?php echo esc_url( $meta['hightlights_thumbnail'] ); ?>" alt="">
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
            <?php
        }
	}

	private function tf_apartment_highlight($settings) {
	    $meta = get_post_meta( $this->post_id, 'tf_apartment_opt', true );
		$style = !empty($settings['highlights_style']) ? $settings['highlights_style'] : 'style1';
		$tf_highlights_count = count(Helper::tf_data_types( $meta['highlights'] ));

		if($style == 'style1' && ! empty( Helper::tf_data_types( $meta['highlights'] ) )){ ?>
			<div class="tf-single-template__two tf-apartment-highlights-style1">
				<div class="tf-overview-wrapper">
					<div class="<?php echo $tf_highlights_count > 4 ? esc_attr('tf-features-block-slides tf-slick-slider') : esc_attr('tf-features-block-wrapper'); ?> tf-informations-secations">
						
						<?php
						foreach ( Helper::tf_data_types( $meta['highlights'] ) as $highlight ) :
						if ( empty( $highlight['title'] ) ) {
							continue;
						}
						?>
						<div class="tf-feature-block">
							<?php echo ! empty( $highlight['icon'] ) ? "<i class='" . esc_attr( $highlight['icon'] ) . "'></i>" : ''; ?>
							<div class="tf-feature-block-details">
								<h5><?php echo esc_html( $highlight['title'] ); ?></h5>
								<?php 
								echo ! empty( $highlight['subtitle'] ) ? '<p>' . esc_html( $highlight['subtitle'] ) . '</p>' : ''; ?>
							</div>
						</div>
						<?php endforeach; ?>
						
					</div>
				</div>
			</div>
        	<?php 
		} elseif ($style == 'style2' && ! empty( Helper::tf_data_types( $meta['highlights'] ) )) {
        	?>
            <div class="tf-single-template__legacy tf-apartment-highlights-style2">
				<div class="tf-apt-highlights-wrapper">
					<?php if ( ! empty( $meta['highlights_title'] ) ): ?>
						<h2 class="section-heading"><?php echo esc_html( $meta['highlights_title'] ) ?></h2>
					<?php endif; ?>

					<div class="tf-apt-highlights <?php echo count( Helper::tf_data_types( $meta['highlights'] ) ) > 3 ? 'tf-apt-highlights-slider tf-slick-slider' : ''; ?>">
						<?php
						foreach ( Helper::tf_data_types( $meta['highlights'] ) as $highlight ) :
							if ( empty( $highlight['title'] ) ) {
								continue;
							}
							?>
							<div class="tf-apt-highlight">
								<div class="tf-apt-highlight-top">
									<?php echo ! empty( $highlight['icon'] ) ? "<div class='tf-apt-highlight-icon'><i class='" . esc_attr( $highlight['icon'] ) . "'></i></div>" : ''; ?>
									<h4><?php echo esc_html( $highlight['title'] ); ?></h4>
								</div>
								<?php echo ! empty( $highlight['subtitle'] ) ? '<p>' . esc_html( $highlight['subtitle'] ) . '</p>' : ''; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
            <?php
        }
	}
}
