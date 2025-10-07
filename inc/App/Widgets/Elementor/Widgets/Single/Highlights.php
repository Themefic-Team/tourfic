<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Highlights
 */
class Highlights extends Widget_Base {

	use \Tourfic\Traits\Singleton;

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
		$this->tf_highlights_style_controls();
		do_action( 'tf/single-highlights/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_highlights_content',[
            'label' => esc_html__('Highlights', 'tourfic'),
        ]);

        do_action( 'tf/single-highlights/before-content/controls', $this );

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

    protected function tf_highlights_style_controls() {
		$this->start_controls_section( 'highlights_style_section', [
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
        $highlights = ! empty( $meta['additional_information'] ) ? $meta['additional_information'] : '';
		$style = !empty($settings['highlights_style']) ? $settings['highlights_style'] : 'style1';
       
        if($style == 'style1' && $highlights){ ?>
			<div class="tf-single-template__one">
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
            <div class="tf-single-template__legacy">
				<div class="tf-highlight-wrapper">
					<div class="tf-highlight-content">
						<div class="tf-highlight-item">
							<div class="tf-highlight-text">
								<h2 class="section-heading"><?php echo !empty($meta['highlights-section-title']) ? esc_html($meta['highlights-section-title']) : ''; ?></h2>
								<?php echo wp_kses_post($highlights); ?>
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
