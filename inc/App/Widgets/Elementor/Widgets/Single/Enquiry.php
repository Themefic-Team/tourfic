<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\App\TF_Review;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Enquiry
 */
class Enquiry extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-enquiry';
	}

	public function get_title() {
		return esc_html__( 'Enquiry', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-help-o';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'ask-question',
            'enquiry',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-enquiry'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-enquiry/before-style-controls', $this );
		$this->tf_enquiry_style_controls();
		do_action( 'tf/single-enquiry/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_enquiry_content',[
            'label' => esc_html__('Content', 'tourfic'),
        ]);

        do_action( 'tf/single-enquiry/before-content/controls', $this );

        $this->add_control('enquiry_style',[
            'label' => esc_html__('Enquiry Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-enquiry/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_enquiry_style_controls() {
		$this->start_controls_section( 'enquiry_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_responsive_control( "tf_icon_size", [
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
				"{{WRAPPER}} .tf-ask-enquiry i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-apartment-question-icon i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

        $this->add_control( 'tf_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-ask-enquiry i' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-apartment-question-icon i' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control( 'tf_title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Title', 'tourfic' ),
		] );

        $this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-enquiry-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-enquiry-title",
		]);

        $this->add_control( 'tf_content_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Content', 'tourfic' ),
		] );

		$this->add_control( 'tf_enquiry_content_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-enquiry-content' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_enquiry_content_typography",
			'selector' => "{{WRAPPER}} .tf-enquiry-content",
		]);

        $this->add_control( 'tf_btn_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Button', 'tourfic' ),
		] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => "btn_typography",
			'selector' => "{{WRAPPER}} .tf_btn",
		] );

		$this->start_controls_tabs( "tabs_btn_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_btn_normal", [
			'label' => __( 'Normal', 'tourfic' ),
		] );
		$this->add_control( "btn_color", [
			'label'     => __( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf_btn svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_bg_color", [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "btn_border",
			'selector' => "{{WRAPPER}} .tf_btn",
		] );
		$this->add_control( "btn_border_radius", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_button_hover", [
			'label' => __( 'Hover', 'tourfic' ),
		] );
		$this->add_control( "button_color_hover", [
			'label'     => __( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf_btn:hover svg path" => 'fill: {{VALUE}};',
			],
		] );
		
		$this->add_control( "btn_hover_bg_color", [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_hover_border_color", [
			'label'     => __( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'border-color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Button tabs--------*/

		$this->add_responsive_control( "btn_width", [
			'label'      => esc_html__( 'Button width', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 800,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => 'width: {{SIZE}}{{UNIT}};',
			],
			'separator'  => 'before',
		] );
		$this->add_responsive_control( "btn_height", [
			'label'      => esc_html__( 'Button Height', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 500,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();
        $style = !empty($settings['enquiry_style']) ? $settings['enquiry_style'] : 'style1';

		if($post_type == 'tf_hotel'){
            $meta = get_post_meta($post_id, 'tf_hotels_opt', true);
            $tf_enquiry_section_status = !empty($meta['h-enquiry-section']) ? $meta['h-enquiry-section'] : "";
            $tf_enquiry_section_icon = !empty($meta['h-enquiry-option-icon']) ? esc_html($meta['h-enquiry-option-icon']) : '';
            $tf_enquiry_section_title = !empty($meta['h-enquiry-option-title']) ? esc_html($meta['h-enquiry-option-title']) : '';
            $tf_enquiry_section_cont = !empty($meta['h-enquiry-option-content']) ? esc_html($meta['h-enquiry-option-content']) : '';
            $tf_enquiry_section_button = !empty($meta['h-enquiry-option-btn']) ? esc_html($meta['h-enquiry-option-btn']) : '';

        } elseif($post_type == 'tf_tours'){
			$meta = get_post_meta($post_id, 'tf_tours_opt', true);
            $tf_enquiry_section_status = ! empty( $meta['t-enquiry-section'] ) ? $meta['t-enquiry-section'] : "";
            $tf_enquiry_section_icon = ! empty( $meta['t-enquiry-option-icon'] ) ? esc_html( $meta['t-enquiry-option-icon'] ) : '';
            $tf_enquiry_section_title = ! empty( $meta['t-enquiry-option-title'] ) ? esc_html( $meta['t-enquiry-option-title'] ) : '';
            $tf_enquiry_section_cont = ! empty( $meta['t-enquiry-option-content'] ) ? esc_html( $meta['t-enquiry-option-content'] ) : '';
            $tf_enquiry_section_button = ! empty( $meta['t-enquiry-option-btn'] ) ? esc_html( $meta['t-enquiry-option-btn'] ) : '';
			
        } elseif($post_type == 'tf_apartment'){
			$meta = get_post_meta($post_id, 'tf_apartment_opt', true);
            $tf_enquiry_section_status = ! empty( $meta['enquiry-section'] ) ? $meta['enquiry-section'] : '';
            $tf_enquiry_section_icon   = ! empty( $meta['apartment-enquiry-icon'] ) ? $meta['apartment-enquiry-icon'] : '';
            $tf_enquiry_section_title  = ! empty( $meta['enquiry-title'] ) ? $meta['enquiry-title'] : '';
            $tf_enquiry_section_cont   = ! empty( $meta['enquiry-content'] ) ? $meta['enquiry-content'] : '';
            $tf_enquiry_section_button = ! empty( $meta['enquiry-btn'] ) ? $meta['enquiry-btn'] : '';
			
        } elseif($post_type == 'tf_carrental'){
			$meta = get_post_meta($post_id, 'tf_carrental_opt', true);
            
        } else {
			return;
		}
		if ( $style == 'style1' && $tf_enquiry_section_status == '1' ) :?>
        <div class="tf-single-enquiry-style-1 tf-ask-enquiry">
            <?php if (!empty($tf_enquiry_section_icon)) { ?>
                <i class="<?php echo esc_attr($tf_enquiry_section_icon); ?>" aria-hidden="true"></i>
                <?php
            }
            if(!empty($tf_enquiry_section_title)) {
                ?>
                <h3 class="tf-enquiry-title"><?php echo wp_kses_post($tf_enquiry_section_title); ?></h3>
                <?php
            }
            if(!empty($tf_enquiry_section_cont)) {
                ?>
                    <p class="tf-enquiry-content"><?php echo wp_kses_post($tf_enquiry_section_cont);  ?></p>
                <?php
            }
            if( !empty( $tf_enquiry_section_button )) {
                ?>
                <div class="tf-btn-wrap">
                    <a href="javaScript:void(0);" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn">
                        <span><?php echo esc_html($tf_enquiry_section_button); ?></span>
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
		<?php 
		elseif ($style == 'style2' && $tf_enquiry_section_status == '1' ) : ?>
		<div class="tf-single-enquiry-style-2 apartment-question">
			<div class="tf-question-left">
				<?php if ( ! empty( $tf_enquiry_section_icon ) ) : ?>
					<div class="tf-apartment-question-icon">
						<i class="<?php echo esc_attr( $tf_enquiry_section_icon ); ?>" aria-hidden="true"></i>
					</div>
				<?php endif; ?>
				<div class="tf-question-left-inner">
					<div class="default-enquiry-title-section">
						<?php if ( ! empty( $tf_enquiry_section_title ) ) {?>
							<h2 class="tf-enquiry-title"><?php echo esc_html( $tf_enquiry_section_title ) ?></h2>
						<?php } ?>
					</div>
					<?php if ( ! empty( $tf_enquiry_section_cont ) ) {?>
						<p class="tf-enquiry-content"><?php echo wp_kses_post( $tf_enquiry_section_cont ); ?></p>
					<?php } ?>
				</div>
			</div>
			<?php if ( ! empty( $tf_enquiry_section_button ) ) {?>
				<div class="tf-btn-wrap">
					<a href="#" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn tf_btn_large">
						<span><?php echo wp_kses_post( $tf_enquiry_section_button ) ?></span>
					</a>
				</div>
			<?php } ?>
		</div>
		<?php
		endif; 
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
