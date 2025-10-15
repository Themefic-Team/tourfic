<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\Classes\Tour\Pricing;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Tour Price
 */
class Tour_Price extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-tour-price';
	}

	public function get_title() {
		return esc_html__( 'Tour Price', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-price-list';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'tour price',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-tour-price'];
	}

	protected function register_controls() {

		//$this->tf_content_layout_controls();

		do_action( 'tf/single-tour-price/before-style-controls', $this );
		$this->tf_tour_price_style_controls();
		do_action( 'tf/single-tour-price/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_tour_price_content',[
            'label' => esc_html__('Tour Price', 'tourfic'),
        ]);

        do_action( 'tf/single-tour-price/before-content/controls', $this );

        

	    do_action( 'tf/single-tour-price/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_tour_price_style_controls() {
		$this->start_controls_section( 'tour_price_style_section', [
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

        if($this->post_type !== 'tf_tours'){
            return;
        }
	    $meta = get_post_meta( $this->post_id, 'tf_tours_opt', true );
        $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
        $tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
        $pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
        $disable_adult  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
        $disable_child  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
        $disable_infant = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
       
        $avail_prices = Pricing::instance( $this->post_id )->get_avail_price();
        if(($tf_booking_type == 2 && $tf_hide_price !== '1') || $tf_booking_type == 1 || $tf_booking_type == 3) : ?>
            <div class="tf-single-tour-pricing">
                <?php if ( $pricing_rule == 'group' ) { ?>

                    <div class="tf-price group-price">
                        <span class="sale-price">
                            <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['group_price']))) ?>
                        </span>
                        <?php echo ( !empty($avail_prices['sale_group_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_group_price']))) . '</del>' : ''; ?>
                    </div>

                <?php } elseif ( $pricing_rule == 'person' ) { ?>

                    <?php if ( ! $disable_adult && ! empty( $avail_prices['adult_price'] ) ) { ?>

                        <div class="tf-price adult-price">
                            <span class="sale-price">
                                <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['adult_price']))); ?>
                            </span>
                            <?php echo ( !empty($avail_prices['sale_adult_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_adult_price']))) . '</del>' : ''; ?>
                        </div>

                    <?php }
                    if ( ! $disable_child && ! empty( $avail_prices['child_price'] ) ) { ?>

                        <div class="tf-price child-price tf-d-n">
                            <span class="sale-price">
                                <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['child_price']))); ?>
                            </span>
                            <?php echo ( !empty($avail_prices['sale_child_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_child_price']))) . '</del>' : ''; ?>
                        </div>

                <?php }
                if ( !$disable_adult && (! $disable_infant && ! empty( $avail_prices['infant_price'] )) ) { ?>

                        <div class="tf-price infant-price tf-d-n">
                            <span class="sale-price">
                                <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['infant_price']))); ?>
                            </span>
                            <?php echo ( !empty($avail_prices['sale_infant_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_infant_price']))) . '</del>' : ''; ?>
                        </div>

                    <?php } ?>
                    <?php
                }
                ?>
                <ul class="tf-price-tab">
                    <?php
                    if ( $pricing_rule == 'group' ) {

                        echo '<li id="group" class="active">' . esc_html__( "Group", "tourfic" ) . '</li>';

                    } elseif ( $pricing_rule == 'person' ) {

                    if ( ! $disable_adult && ! empty( $avail_prices['adult_price'] ) ) {
                        echo '<li id="adult" class="active">' . esc_html__( "Adult", "tourfic" ) . '</li>';
                    }
                    if ( ! $disable_child && ! empty( $avail_prices['child_price'] ) ) {
                        echo '<li id="child">' . esc_html__( "Child", "tourfic" ) . '</li>';
                    }
                    if ( !$disable_adult && (! $disable_infant && ! empty( $avail_prices['infant_price'] )) ) {
                        echo '<li id="infant">' . esc_html__( "Infant", "tourfic" ) . '</li>';
                    }

                    }
                    ?>
                </ul>
            </div>
        <?php endif;
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
