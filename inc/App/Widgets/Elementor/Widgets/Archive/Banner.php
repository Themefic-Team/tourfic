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
class Banner extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-archive-banner';
	}

	public function get_title() {
		return esc_html__( 'Archive Banner', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-site-search';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'banner',
			'tourfic',
			'tf'
        ];
    }

	protected function register_controls() {
		do_action( 'tf/banner/before-style-controls', $this );
		$this->tf_style_general_controls();
		do_action( 'tf/banner/after-style-controls', $this );
	}

    protected function tf_style_general_controls() {
		$this->start_controls_section( 'banner_style_general', [
			'label' => __( 'General', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control('banner_height',[
			'label'      => __('Height', 'tourfic'),
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
			'default'   => [
				'unit' => 'px',
				'size' => 500,
			],
			'size_units' => ['px', 'em', '%'],
			'selectors'  => [
				'{{WRAPPER}} .tf-hero-section-wrap .tf-hero-content' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .tf-archive-car-banner' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
			],
		]);
		
        $this->add_control( 'tf_banner_title_color', [
			'label'     => __( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-hero-section-wrap .tf-hero-content.tf-archive-hero-content .tf-head-title h1' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-archive-car-banner .tf-banner-content h1' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => __( 'Typography', 'tourfic' ),
			'name'     => "tf_banner_title_typography",
			'selector' => "{{WRAPPER}} .tf-hero-section-wrap .tf-hero-content.tf-archive-hero-content .tf-head-title h1,
                            {{WRAPPER}} .tf-archive-car-banner .tf-banner-content h1",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings           = $this->get_settings_for_display();
        $post_type = get_post_type();
        $term = get_queried_object();
        $banner_title = '';
        $banner_thumbnail = '';
        
        if(is_post_type_archive('tf_hotel')){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] : '';
            $banner_title = esc_html__("Hotels", "tourfic");
        } elseif($post_type == 'tf_hotel' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="hotel_location"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_hotel_location', true );
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : '';
            $banner_title = $term->name;
        } elseif($post_type == 'tf_hotel' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_hotel" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] : '';
            $banner_title = $term->name;
        }

        if(is_post_type_archive('tf_tours')){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] : '';
            $banner_title = esc_html__("Tours", "tourfic");
        } elseif($post_type == 'tf_tours' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="tour_destination"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_tour_destination', true );
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : '';
            $banner_title = $term->name;
        } elseif($post_type == 'tf_tours' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_tours" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] : '';
            $banner_title = $term->name;
        }

        if(is_post_type_archive('tf_apartment')){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
            $banner_title = esc_html__("Apartments", "tourfic");
        } elseif($post_type == 'tf_apartment' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="apartment_location"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_apartment_location', true );
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : '';
            $banner_title = $term->name;
        } elseif($post_type == 'tf_apartment' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_apartment" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
            $banner_title = $term->name;
        }

        if(is_post_type_archive('tf_carrental') || $post_type == 'tf_carrental'){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
            $banner_title = esc_html__("Cars", "tourfic");
        } elseif($post_type == 'tf_carrental' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="carrental_location"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_carrental_location', true );
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : '';
            $banner_title = $term->name;
        } elseif($post_type == 'tf_carrental' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="carrental_brand"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_carrental_brand', true );
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : '';
            $banner_title = $term->name;
        } elseif($post_type == 'tf_carrental' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_carrental" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
            $banner_title = $term->name;
        }
        ?>
        <?php if($post_type == 'tf_carrental') : ?>
            <div class="tf-archive-template__one" style="padding: 0;">
                <div class="tf-archive-car-banner" style="<?php echo !empty($banner_thumbnail) ? 'background-image: url('.esc_url($banner_thumbnail).');' : ''; ?> margin: 0;">
                    <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
                        <h1><?php echo esc_html($banner_title); ?></h1>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="tf-archive-template__two" style="padding: 0;">
                <div class="tf-hero-section-wrap" style="<?php echo !empty($banner_thumbnail) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($banner_thumbnail).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
                    <div class="tf-container">
                        <div class="tf-hero-content tf-archive-hero-content tf-banner-content">
                            <div class="tf-head-title">
                                <h1><?php echo esc_html($banner_title); ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php
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
