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
 * Gallery
 */
class Gallery_Button extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	public function get_name() {
		return 'tf-single-gallery-button';
	}

	public function get_title() {
		return esc_html__( 'Gallery Button', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-button';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'gallery',
			'tourfic',
			'media',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-action-btns'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-gallery-button/before-style-controls', $this );
		$this->tf_gallery_button_style_controls();
		do_action( 'tf/single-gallery-button/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_gallery_button_content',[
            'label' => esc_html__('Gallery Button', 'tourfic'),
        ]);

        do_action( 'tf/single-gallery-bitton/before-content/controls', $this );

		$this->add_control('style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

        $this->add_control('icon',[
            'label' => esc_html__('Icon', 'tourfic'),
            'default' => [
                'value' => 'fas fa-camera-retro',
                'library' => 'fa-solid',
            ],
            'label_block' => true,
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'icon_comp',
        ]);

        $this->add_control('label', [
            'label' => esc_html__('Label', 'tourfic'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => 'Gallery',
            'condition' => [
				'style' => ['style1'],
			],
        ]);

	    do_action( 'tf/single-gallery-bitton/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_gallery_button_style_controls() {
		$this->start_controls_section( 'gallery_button_style_section', [
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
				"{{WRAPPER}} .tf-single-action-btns a i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-single-action-btns a svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-hero-hotel a i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-hero-hotel a svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => "btn_typography",
			'selector' => "{{WRAPPER}} .tf-single-action-btns a",
            'condition' => [
				'style' => ['style1'],
			],
		] );
		
		$this->add_control( "tabs_btn_colors_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Colors & Border', 'tourfic' ),
			'separator' => 'before',
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
				"{{WRAPPER}} .tf-single-action-btns a" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-single-action-btns a svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-hero-hotel" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-hero-hotel svg" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_bg_color", [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-single-action-btns a" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-hero-hotel" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "btn_border",
			'selector' => "{{WRAPPER}} .tf-single-action-btns a, {{WRAPPER}} .tf-hero-hotel",
		] );
		$this->add_control( "btn_border_radius", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-single-action-btns a" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-hero-hotel" => $this->tf_apply_dim( 'border-radius' ),
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
				"{{WRAPPER}} .tf-single-action-btns a:hover" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-single-action-btns a:hover svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-hero-hotel:hover a" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-hero-hotel:hover svg" => 'fill: {{VALUE}};',
			],
		] );
		
		$this->add_control( "btn_hover_bg_color", [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-single-action-btns a:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-hero-hotel:hover" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_hover_border_color", [
			'label'     => __( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-single-action-btns a:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-hero-hotel:hover" => 'border-color: {{VALUE}};',
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
				"{{WRAPPER}} .tf-single-action-btns a" => 'width: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-hero-hotel" => 'width: {{SIZE}}{{UNIT}};',
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
				"{{WRAPPER}} .tf-single-action-btns a" => 'height: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-hero-hotel" => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();
		$style = !empty($settings['style']) ? $settings['style'] : 'style1';

		if($post_type == 'tf_hotel'){
            $meta = get_post_meta($post_id, 'tf_hotels_opt', true);
			$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}

        } elseif($post_type == 'tf_tours'){
			$meta = get_post_meta($post_id, 'tf_tours_opt', true);
			$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : array();
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery );
			}
			
        } elseif($post_type == 'tf_apartment'){
			$meta = get_post_meta($post_id, 'tf_apartment_opt', true);
			$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}
			
        } else {
			return;
		}
		if ( $style == 'style1' && ! empty( $gallery_ids ) ) :?>
			<div class="tf-single-action-btns featured-column tf-gallery-box">
				<a id="featured-gallery" href="#" class="tf-tour-gallery">
					<?php
					$icon_migrated = isset($settings['__fa4_migrated']['icon']);
					$icon_is_new = empty($settings['icon_comp']);

					if ( $icon_is_new || $icon_migrated ) {
						Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						?>
						<i class="<?php echo esc_attr($settings['icon_comp']); ?>" aria-hidden="true"></i>
						<?php
					}?>
					<?php echo isset($settings['label']) ? esc_html($settings['label']) : esc_html__('Gallery', 'tourfic'); ?>
				</a>
			</div>
		<?php elseif($style == 'style2' && ! empty( $gallery_ids )) : ?>
			<div class="tf-single-template__two tf-single-action-btns-style2">
				<div class="tf-hero-hotel tf-popup-buttons">
					<a href="#">
						<?php
						$icon_migrated = isset($settings['__fa4_migrated']['icon']);
						$icon_is_new = empty($settings['icon_comp']);

						if ( $icon_is_new || $icon_migrated ) {
							Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
						} else {
							?>
							<i class="<?php echo esc_attr($settings['icon_comp']); ?>" aria-hidden="true"></i>
							<?php
						}?>
					</a>
				</div>

				<div class="tf-popup-wrapper tf-hotel-popup">
					<div class="tf-popup-inner">
						<div class="tf-popup-body">
							<?php
							if ( ! empty( $gallery_ids ) ) {
								foreach ( $gallery_ids as $key => $gallery_item_id ) {
									$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
									?>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="" class="tf-popup-image">
								<?php }
							} ?>
						</div>
						<div class="tf-popup-close">
							<i class="fa-solid fa-xmark"></i>
						</div>
					</div>
				</div>
			</div>
		<?php
		endif; 
    }
}
