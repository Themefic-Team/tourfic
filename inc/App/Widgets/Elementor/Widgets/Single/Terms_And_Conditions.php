<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Terms_And_Conditions
 */
class Terms_And_Conditions extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-terms-and-conditions';
	}

	public function get_title() {
		return esc_html__( 'Terms & Conditions', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-text';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'terms and conditions',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-terms-and-conditions'];
	}

	protected function register_controls() {

		//$this->tf_content_layout_controls();

		do_action( 'tf/single-terms-and-conditions/before-style-controls', $this );
		$this->tf_terms_and_conditions_style_controls();
		do_action( 'tf/single-terms-and-conditions/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_terms_and_conditions_content',[
            'label' => esc_html__('Terms & Conditions', 'tourfic'),
        ]);

        do_action( 'tf/single-terms-and-conditions/before-content/controls', $this );
		

	    do_action( 'tf/single-terms-and-conditions/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_terms_and_conditions_style_controls() {
		$this->start_controls_section( 'terms_style', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_control( 'tf_title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Title', 'tourfic' ),
		] );

        $this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title",
		]);

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

        $this->add_control( 'tf_content_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Content', 'tourfic' ),
		] );

		$this->add_control( 'tf_terms_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-toc-content' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_terms_typography",
			'selector' => "{{WRAPPER}} .tf-toc-content",
		]);

		$this->add_responsive_control('content-align',[
			'label' => esc_html__('Content Alignment', 'tourfic'),
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
				'{{WRAPPER}} .tf-toc-content' => 'text-align: {{VALUE}};',
			]
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();

        if($post_type == 'tf_hotel'){
            $meta = get_post_meta($post_id, 'tf_hotels_opt', true);
            $tc_title = !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : esc_html__("Hotel Terms & Conditions","tourfic");
	        $tc = ! empty( $meta['tc'] ) ? $meta['tc'] : '';

        } elseif($post_type == 'tf_tours'){
			$meta = get_post_meta($post_id, 'tf_tours_opt', true);
            $tc_title = !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : esc_html__("Tour Terms & Conditions","tourfic");
	        $tc = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';
			
        } elseif($post_type == 'tf_apartment'){
			$meta = get_post_meta($post_id, 'tf_apartment_opt', true);
			$tc_title = !empty($meta['terms_title']) ? esc_html($meta['terms_title']) : esc_html__("Terms & Conditions","tourfic");
	        $tc = ! empty( $meta['terms_and_conditions'] ) ? $meta['terms_and_conditions'] : '';
			
        } elseif($post_type == 'tf_carrental'){
			$meta = get_post_meta($post_id, 'tf_carrental_opt', true);
            $tc_title = ! empty( $meta['car-tc-section-title'] ) ? $meta['car-tc-section-title'] : '';
            $tc = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';
			
        } else {
			return;
		}

        if ( !empty($tc) && $post_type == 'tf_carrental') { ?>
			<div class="tf-single-template__one tf-single-car-terms-and-conditions-style-1">
				<div class="tf-car-conditions-section" id="tf-tc">
					<?php if(!empty($tc_title)){ ?>
						<h3><?php echo esc_html($tc_title); ?></h3>
					<?php } ?>
					<table>
						<?php 
						foreach($tc as $singletc){ ?>
						<tr>
							<th><?php echo !empty($singletc['title']) ? esc_html($singletc['title']) : ''; ?></th>
							<td><?php echo !empty($singletc['content']) ? wp_kses_post($singletc['content']) : ''; ?></td>
						</tr>
						<?php } ?>
					</table>
				</div>
            </div>
		<?php }elseif(!empty($tc)){ ?>
			<div class="tf-toc-wrapper" id="tf-hotel-policies">
				<div class="tf-section-head">
					<h2 class="tf-section-title"><?php echo esc_html($tc_title); ?></h2>
				</div>
				<div class="tf-toc-content">
					<?php echo wp_kses_post(wpautop( $tc )); ?>
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
}
