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
 * car benefits
 */
class Car_Benefits extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-car-benefits';
	}

	public function get_title() {
		return esc_html__( 'Car Benefits', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-alert';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'car benefits',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-car-benefits'];
	}

	protected function register_controls() {

		// $this->tf_content_layout_controls();

		do_action( 'tf/single-car-benefits/before-style-controls', $this );
		$this->tf_car_benefits_style_controls();
		do_action( 'tf/single-car-benefits/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_car_benefits_content',[
            'label' => esc_html__('Car Benefits', 'tourfic'),
        ]);

        do_action( 'tf/single-car-benefits/before-content/controls', $this );

        $post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic')
		];
		$this->add_control('car_benefits_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => $options,
        ]);

	    do_action( 'tf/single-car-benefits/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_car_benefits_style_controls() {
		$this->start_controls_section( 'car_benefits_style_section', [
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

        if($this->post_type !== 'tf_carrental'){
            return;
        }
	    $meta = get_post_meta( $this->post_id, 'tf_carrental_opt', true );

        // Benefits 
        $benefits_status = ! empty( $meta['benefits_section'] ) ? $meta['benefits_section'] : '';
        $benefits_sec_title = ! empty( $meta['benefits_sec_title'] ) ? $meta['benefits_sec_title'] : '';
        $benefits = ! empty( $meta['benefits'] ) ? $meta['benefits'] : '';
        
        if(!empty($benefits_status) && !empty($benefits)){ ?>
        <div class="tf-car-benefits" id="tf-benefits">
            <?php if(!empty($benefits_sec_title)){ ?>   
            <h3><?php echo esc_html($benefits_sec_title); ?></h3>
            <?php } ?>

            <ul>
                <?php foreach($benefits as $singlebenefit){ ?>
                <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                <i class="<?php echo !empty($singlebenefit['icon']) ? esc_attr($singlebenefit['icon']) : 'ri-check-double-line'; ?>"></i>
                <?php echo !empty($singlebenefit['title']) ? esc_html($singlebenefit['title']) : ''; ?>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php }
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
