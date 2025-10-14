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

        $post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic')
		];
		if($post_type == 'tf_tours'){
			$options['style2'] = esc_html__('Style 2', 'tourfic');
			$options['style3'] = esc_html__('Style 3', 'tourfic');
		}
		$this->add_control('included_excluded_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => $options,
        ]);

	    do_action( 'tf/single-included-excluded/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_included_excluded_style_controls() {
		$this->start_controls_section( 'included_excluded_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Title', 'tourfic' ),
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

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} h2.section-heading' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title, {{WRAPPER}} .section-heading",
		]);

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type == 'tf_tours'){
            $meta = get_post_meta( $this->post_id, 'tf_tours_opt', true );
            $inc             = !empty(Helper::tf_data_types($meta['inc'])) ? Helper::tf_data_types($meta['inc']) : null;
            $exc             = !empty(Helper::tf_data_types($meta['exc'])) ? Helper::tf_data_types($meta['exc']) : null;
            $inc_icon        = ! empty( $meta['inc_icon'] ) ? $meta['inc_icon'] : null;
            $exc_icon        = ! empty( $meta['exc_icon'] ) ? $meta['exc_icon'] : null;
            $custom_inc_icon = ! empty( $inc_icon ) ? "custom-inc-icon" : '';
            $custom_exc_icon = ! empty( $exc_icon ) ? "custom-exc-icon" : '';
            $inc_exc_bg = ! empty( $meta['include-exclude-bg'] ) ? $meta['include-exclude-bg'] : '';
        }elseif($this->post_type == 'tf_carrental'){
            $meta = get_post_meta( $this->post_id, 'tf_carrental_opt', true );
            $inc_exc_status = ! empty( $meta['inc_exc_section'] ) ? $meta['inc_exc_section'] : '';
            $includes = ! empty( $meta['inc'] ) ? $meta['inc'] : '';
            $include_icon = ! empty( $meta['inc_icon'] ) ? $meta['inc_icon'] : '';
            $excludes = ! empty( $meta['exc'] ) ? $meta['exc'] : '';
            $exclude_icon = ! empty( $meta['exc_icon'] ) ? $meta['exc_icon'] : '';
            $inc_sec_title = ! empty( $meta['inc_sec_title'] ) ? $meta['inc_sec_title'] : '';
            $exc_sec_title = ! empty( $meta['exc_sec_title'] ) ? $meta['exc_sec_title'] : '';
        } else {
            return;
        }
        $style = !empty($settings['included_excluded_style']) ? $settings['included_excluded_style'] : 'style1';
       
        if($this->post_type == 'tf_tours' && $style == 'style1' && ($inc || $exc)){ ?>
            <div class="tf-single-template__one tf-single-tour-inc-exc-style1">
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
        } elseif($this->post_type == 'tf_tours' && $style == 'style2' && ($inc || $exc)){
            ?>
            <div class="tf-single-template__two tf-single-tour-inc-exc-style2">
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
        } elseif($this->post_type == 'tf_tours' && $style == 'style3' && ($inc || $exc)){
            ?>
            <div class="tf-single-template__legacy tf-single-tour-inc-exc-legacy">
                <div class="tf-inc-exc-wrapper sp-70" style="background-image: url(<?php echo esc_url( $inc_exc_bg ) ?>);">
                    <div class="tf-container">
                        <div class="tf-inc-exc-content">
                            <?php if ( $inc ) { ?>
                                <div class="tf-include-section <?php echo esc_attr( $custom_inc_icon ); ?>">
                                    <h2 class="tf-section-title"><?php esc_html_e( 'Included', 'tourfic' ); ?></h2>
                                    <ul>
                                        <?php
                                        foreach ( $inc as $key => $val ) {
                                            echo "<li><i class='" . esc_attr( $inc_icon ) . "'></i>" . wp_kses_post($val['inc']) . "</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            <?php } ?>
                            <?php if ( $exc ) { ?>
                                <div class="tf-exclude-section <?php echo esc_attr( $custom_exc_icon ); ?>">
                                    <h2 class="tf-section-title"><?php esc_html_e( 'Excluded', 'tourfic' ); ?></h2>
                                    <ul>
                                        <?php
                                        foreach ( $exc as $key => $val ) {
                                            echo "<li><i class='" . esc_attr( $exc_icon ) . "'></i>" . wp_kses_post($val['exc']) . "</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } elseif($this->post_type == 'tf_carrental' && $style == 'style1' && !empty($inc_exc_status) &&($includes || $excludes)){
            ?>
            <div class="tf-car-inc-exc-section" id="tf-inc-exc">
                <div class="tf-inc-exe tf-flex tf-flex-gap-16">
                    <?php if(!empty($includes)){ ?>
                    <div class="tf-inc-list">
                        <?php if(!empty($inc_sec_title)){ ?>   
                            <h3 class="tf-section-title"><?php echo esc_html($inc_sec_title); ?></h3>
                        <?php } ?>
                        <ul class="tf-flex tf-flex-gap-16 tf-flex-direction-column">
                        <?php foreach($includes as $inc){ ?>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-8">
                                <i class="<?php echo !empty($include_icon) ? esc_attr($include_icon) : 'ri-check-double-line'; ?>"></i>
                                <?php echo !empty($inc['title']) ? esc_html($inc['title']) : ''; ?>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>
                    <?php if(!empty($excludes)){ ?>
                    <div class="tf-exc-list">
                        <?php if(!empty($exc_sec_title)){ ?>   
                            <h3 class="tf-section-title"><?php echo esc_html($exc_sec_title); ?></h3>
                        <?php } ?>
                        <ul class="tf-flex tf-flex-gap-16 tf-flex-direction-column">
                            <?php foreach($excludes as $exc){ ?>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-8">
                                <i class="<?php echo !empty($exclude_icon) ? esc_attr($exclude_icon) : 'ri-close-circle-line'; ?>"></i>
                                <?php echo !empty($exc['title']) ? esc_html($exc['title']) : ''; ?>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>

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
