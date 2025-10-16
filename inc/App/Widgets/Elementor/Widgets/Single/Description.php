<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Description
 */
class Description extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	public function get_name() {
		return 'tf-single-description';
	}

	public function get_title() {
		return esc_html__( 'Description', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-text';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'description',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-description'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-description/before-style-controls', $this );
		$this->tf_description_style_controls();
		do_action( 'tf/single-description/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_description_content',[
            'label' => esc_html__('Description', 'tourfic'),
        ]);

        do_action( 'tf/single-description/before-content/controls', $this );

		$this->add_control('limit_content',[
			'label' => esc_html__('Limit Content', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Yes', 'tourfic'),
			'label_off' => esc_html__('No', 'tourfic'),
			'return_value' => 'yes',
			'default' => '',
		]);

		$this->add_control('content_length', [
            'label' => __('Content Length', 'tourfic'),
            'type' => Controls_Manager::NUMBER,
            'default' => 300,
            'min' => 1,
            'max' => 5000,
            'step' => 1,
			'condition' => [
				'limit_content' => 'yes',
			],
        ]);

		$this->add_responsive_control('description-align',[
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
			'selectors' => [
				'{{WRAPPER}} .tf-single-description' => 'text-align: {{VALUE}};',
			]
		]);

	    do_action( 'tf/single-description/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_description_style_controls() {
		$this->start_controls_section( 'description_style', [
			'label' => esc_html__( 'Description Style', 'tourfic' ),
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

		$this->add_control( 'tf_desc_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Description', 'tourfic' ),
		] );

		$this->add_control( 'tf_description_color', [
			'label'     => esc_html__( 'Description Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-post-content' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Description Typography', 'tourfic' ),
			'name'     => "tf_description_typography",
			'selector' => "{{WRAPPER}} .tf-post-content",
		]);

		$this->end_controls_section();
	}

	protected function render() {
        $post_type = get_post_type();
		$settings  = $this->get_settings_for_display();
        $limit_content   = !empty( $settings['limit_content'] ) ? $settings['limit_content'] : '';
        $content_length   = !empty( $settings['content_length'] ) ? $settings['content_length'] : '300';
        ?>
        <div class="tf-single-template__two tf-single-description">
			<?php 
			if($post_type == 'tf_apartment'){
				$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
				$description_title = ! empty( $meta['description_title'] ) ? esc_html( $meta['description_title'] ) : '';
				echo '<h2 class="section-heading">'. esc_html($description_title) .'</h2>';
			} 
			?>
			<?php if($limit_content == 'yes'):  ?>
            <div class="tf-short-description tf-post-content">
                <?php 
                if(strlen(get_the_content()) > $content_length ){
                    echo esc_html( wp_strip_all_tags(\Tourfic\Classes\Helper::tourfic_character_limit_callback(get_the_content(), $content_length)) ) . '<span class="tf-see-description">See more</span>';
                }else{
                    the_content(); 
                }
                ?>
            </div>
            <div class="tf-full-description tf-post-content">
                <?php 
                    the_content();
                    echo '<span class="tf-see-less-description"> See less</span>';
                ?>
            </div>
			<?php else: ?>
				<div class="tf-post-content">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
        </div>
        <?php
	}
}
