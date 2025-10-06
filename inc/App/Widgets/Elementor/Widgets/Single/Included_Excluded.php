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
 * Included Excluded
 */
class Included_Excluded extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-included-excluded';
	}

	public function get_title() {
		return esc_html__( 'Included Excluded', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-check';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'included excluded',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-inc-exc'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-included-excluded/before-style-controls', $this );
		$this->tf_included_excluded_style_controls();
		do_action( 'tf/single-included-excluded/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_included_excluded_content',[
            'label' => esc_html__('Included Excluded', 'tourfic'),
        ]);

        do_action( 'tf/single-included-excluded/before-content/controls', $this );

		$this->add_control('included_excluded_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
                'style3' => esc_html__('Style 3', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-included-excluded/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_included_excluded_style_controls() {
		$this->start_controls_section( 'included_excluded_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( "bg_color", [
			'label'     => __( 'Card Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-trip-feature-blocks .tf-feature-block" => 'background-color: {{VALUE}};',
			],
		] );

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
        $inc             = !empty(Helper::tf_data_types($meta['inc'])) ? Helper::tf_data_types($meta['inc']) : null;
        $exc             = !empty(Helper::tf_data_types($meta['exc'])) ? Helper::tf_data_types($meta['exc']) : null;
        $inc_icon        = ! empty( $meta['inc_icon'] ) ? $meta['inc_icon'] : null;
        $exc_icon        = ! empty( $meta['exc_icon'] ) ? $meta['exc_icon'] : null;
        $style = !empty($settings['included_excluded_style']) ? $settings['included_excluded_style'] : 'style1';
       
        if($style == 'style1' && ($inc || $exc)){ ?>
            <div class="tf-single-template__one">
                <div class="tf-inex-wrapper tf-template-section">
                    <div class="tf-inex-inner tf-flex tf-flex-gap-24">
                        <?php if ( $inc ) { ?>
                        <div class="tf-inex tf-tour-include tf-box">
                            <h2 class="tf-section-title"><?php esc_html_e( 'Included', 'tourfic' ); ?></h2>
                            <ul class="tf-list">
                                <?php
                                foreach ( $inc as $key => $val ) {
                                ?>
                                <li>
                                    <i class="<?php echo !empty($inc_icon) ? esc_attr( $inc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                                    <?php echo wp_kses_post($val['inc']); ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                        <?php if ( $exc ) { ?>
                        <div class="tf-inex tf-tour-exclude tf-box">
                            <h2 class="tf-section-title"><?php esc_html_e( 'Excluded', 'tourfic' ); ?></h2>
                            <ul class="tf-list">
                                <?php foreach ( $exc as $key => $val ) { ?>
                                <li>
                                    <i class="<?php echo !empty($exc_icon) ? esc_attr( $exc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                                    <?php echo wp_kses_post($val['exc']); ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php 
        } elseif($style == 'style2' && ($inc || $exc)){
            ?>
            <div class="tf-single-template__two">
                <div class="tf-include-exclude-wrapper">
                    <h2 class="tf-section-title"><?php esc_html_e("Include/Exclude", "tourfic"); ?></h2>
                    <div class="tf-include-exclude-innter">
                        <?php if ( $inc ) { ?>
                        <div class="tf-include">
                            <ul>
                                <?php foreach ( $inc as $key => $val ) { ?>
                                <li>
                                    <i class="<?php echo !empty($inc_icon) ? esc_attr( $inc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                                    <?php echo wp_kses_post($val['inc']); ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                        <?php if ( $exc ) { ?>
                        <div class="tf-exclude">
                            <ul>
                                <?php foreach ( $exc as $key => $val ) { ?>
                                <li>
                                    <i class="<?php echo !empty($exc_icon) ? esc_attr( $exc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                                    <?php echo wp_kses_post($val['exc']); ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
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
