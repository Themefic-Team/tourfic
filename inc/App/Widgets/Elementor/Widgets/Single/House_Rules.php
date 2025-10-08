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
 * House Rules
 */
class House_Rules extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-house-rules';
	}

	public function get_title() {
		return esc_html__( 'House Rules', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-check-circle-o';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'tour info cards',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-house-rules'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-house-rules/before-style-controls', $this );
		$this->tf_house_rules_style_controls();
		do_action( 'tf/single-house-rules/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_house_rules_content',[
            'label' => esc_html__('House Rules', 'tourfic'),
        ]);

        do_action( 'tf/single-house-rules/before-content/controls', $this );

        $this->add_control('house_rules_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-house-rules/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_house_rules_style_controls() {
		$this->start_controls_section( 'house_rules_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control( "card_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-feature-blocks .tf-feature-block" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( "bg_color", [
			'label'     => __( 'Card Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-trip-feature-blocks .tf-feature-block" => 'background-color: {{VALUE}};',
			],
		] );

        $this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .tf-trip-feature-blocks .tf-feature-block",
		] );
		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-feature-blocks .tf-feature-block" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .tf-trip-feature-blocks .tf-feature-block',
		]);

        // $this->add_control( "btn_color", [
		// 	'label'     => __( 'Text Color', 'tourfic' ),
		// 	'type'      => Controls_Manager::COLOR,
		// 	'selectors' => [
		// 		"{{WRAPPER}} .tf-single-action-btns a" => 'color: {{VALUE}};',
		// 		"{{WRAPPER}} .tf-single-action-btns a svg path" => 'fill: {{VALUE}};',
		// 	],
		// ] );

        // $this->add_group_control( Group_Control_Typography::get_type(), [
		// 	'name'     => "btn_typography",
		// 	'selector' => "{{WRAPPER}} .tf-single-action-btns a",
		// ] );

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type !== 'tf_apartment'){
            return;
        }
	    $meta = get_post_meta( $this->post_id, 'tf_apartment_opt', true );
        $style = !empty($settings['house_rules_style']) ? $settings['house_rules_style'] : 'style1';
        $included_house_rules = array();
        $not_included_house_rules = array();
        foreach ( Helper::tf_data_types( $meta['house_rules'] ) as $house_rule ) {
            if ( isset( $house_rule['include'] ) && $house_rule['include'] == '1' ) {
                $included_house_rules[] = $house_rule;
            } else {
                $not_included_house_rules[] = $house_rule;
            }
        }

        if ( $style == 'style1' && ! empty( Helper::tf_data_types( $meta['house_rules'] ) ) ) {  
            ?>
            <div class="tf-single-template__two">
                <div class="tf-aprtment-rules-section" id="tf-apartment-rules">
                    <h2><?php echo ! empty( $meta['house_rules_title'] ) ? esc_html($meta['house_rules_title']) : ''; ?></h2>
                    <div class="aprtment-inc-exc <?php echo empty( $included_house_rules ) || empty( $not_included_house_rules ) ? esc_attr('tf-inc-exc-full') : ''; ?>">
                        <?php if ( ! empty( $included_house_rules ) ): ?>
                        <div class="aprtment-single-rules">
                            <ul>
                                <?php
                                foreach ( $included_house_rules as $item ) { ?>
                                <li>
                                    <div class="rules-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M7.77945 9.03072L8.68236 9.85296L14.0956 4.92324L15 5.74677L8.68236 11.5L4.6129 7.79409L5.51721 6.97057L6.87591 8.20789L7.77945 9.03072ZM7.78054 7.38408L10.9475 4.5L11.8493 5.32124L8.68236 8.20527L7.78054 7.38408ZM5.97299 10.6772L5.06944 11.5L1 7.79409L1.90432 6.97057L2.80787 7.79345L2.80711 7.79409L5.97299 10.6772Z" fill="#22C55E"/>
                                        </svg>
                                    </div>
                                    <div class="rules-content">
                                        <?php echo ! empty( $item['title'] ) ? '<span>' . esc_html( $item['title'] ) . '</span>' : ''; ?>
                                        <?php echo ! empty( $item['desc'] ) ? '<p>' . wp_kses_post( $item['desc'] ) . '</p>' : ''; ?>
                                    </div>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $not_included_house_rules ) ): ?>
                        <div class="aprtment-single-rules">
                            <ul>
                                <?php
                                foreach ( $not_included_house_rules as $item ) { ?>
                                <li>
                                    <div class="rules-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M7.99967 14.6667C4.31777 14.6667 1.33301 11.6819 1.33301 8.00004C1.33301 4.31814 4.31777 1.33337 7.99967 1.33337C11.6815 1.33337 14.6663 4.31814 14.6663 8.00004C14.6663 11.6819 11.6815 14.6667 7.99967 14.6667ZM7.99967 13.3334C10.9452 13.3334 13.333 10.9456 13.333 8.00004C13.333 5.05452 10.9452 2.66671 7.99967 2.66671C5.05415 2.66671 2.66634 5.05452 2.66634 8.00004C2.66634 10.9456 5.05415 13.3334 7.99967 13.3334ZM5.33301 9.33337H10.6663V10.6667H5.33301V9.33337ZM5.33301 7.33337C4.78072 7.33337 4.33301 6.88564 4.33301 6.33337C4.33301 5.78109 4.78072 5.33337 5.33301 5.33337C5.88529 5.33337 6.33301 5.78109 6.33301 6.33337C6.33301 6.88564 5.88529 7.33337 5.33301 7.33337ZM10.6663 7.33337C10.1141 7.33337 9.66634 6.88564 9.66634 6.33337C9.66634 5.78109 10.1141 5.33337 10.6663 5.33337C11.2186 5.33337 11.6663 5.78109 11.6663 6.33337C11.6663 6.88564 11.2186 7.33337 10.6663 7.33337Z" fill="#F01616"/>
                                        </svg>
                                    </div>
                                    <div class="rules-content">
                                        <?php echo ! empty( $item['title'] ) ? '<span>' . esc_html( $item['title'] ) . '</span>' : ''; ?>
                                        <?php echo ! empty( $item['desc'] ) ? '<p>' . wp_kses_post( $item['desc'] ) . '</p>' : ''; ?>
                                    </div>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php 
        } elseif($style == 'style2' && ! empty( Helper::tf_data_types( $meta['house_rules'] ) )) {
            ?>
            <div class="tf-single-template__legacy">
                <div class="tf-house-rules">
                    <h2 class="section-heading"><?php echo ! empty( $meta['house_rules_title'] ) ? esc_html( $meta['house_rules_title'] ) : ''; ?></h2>
                    <div class="tf-house-rules-wrapper <?php echo empty( $included_house_rules ) || empty( $not_included_house_rules ) ? 'tf-house-rules-full' : ''; ?>">
                        <?php if ( ! empty( $included_house_rules ) ): ?>
                            <ul class="tf-included-house-rules">
                                <?php
                                foreach ( $included_house_rules as $item ) {
                                    echo sprintf( '<li><h6>%s</h6> <span>%s</span></li>', wp_kses_post( $item['title'] ), wp_kses_post( $item['desc'] ) );
                                }
                                ?>
                            </ul>
                        <?php endif; ?>
                        <?php if ( ! empty( $not_included_house_rules ) ): ?>
                            <ul class="tf-not-included-house-rules">
                                <?php
                                foreach ( $not_included_house_rules as $item ) {
                                    echo sprintf( '<li><h6>%s</h6> <span>%s</span></li>', wp_kses_post( $item['title'] ), wp_kses_post( $item['desc'] ) );
                                }
                                ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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

	/**
	 * Get the current post type being previewed in Elementor editor
	 */
	protected function get_current_post_type() {
		// Check if we're in Elementor editor and have a preview post ID
		if (isset($_GET['tf_preview_post_id']) && !empty($_GET['tf_preview_post_id'])) {
			$preview_post_id = intval($_GET['tf_preview_post_id']);
			$preview_post = get_post($preview_post_id);
			
			if ($preview_post && in_array($preview_post->post_type, ['tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental'])) {
				return $preview_post->post_type;
			}
		}
		
		// Fallback to regular post type detection
		return get_post_type();
	}
}
