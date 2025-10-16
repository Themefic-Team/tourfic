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
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

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
            <h3 class="tf-section-title"><?php echo esc_html($benefits_sec_title); ?></h3>
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
}
