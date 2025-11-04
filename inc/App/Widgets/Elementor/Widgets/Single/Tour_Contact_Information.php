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
use Tourfic\Classes\Apartment\Apartment;
use Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Tour\Pricing;
use \Tourfic\Classes\Car_Rental\Pricing as carPricing;
use Tourfic\Classes\Tour\Tour;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Tour Contact Information
 */
class Tour_Contact_Information extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-tour-contact-information';
	}

	public function get_title() {
		return esc_html__( 'Tour Contact Information', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-table-of-contents';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'tour contact information',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-tour-contact-information'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-tour-contact-information/before-style-controls', $this );
		$this->tf_card_style_controls();
		$this->tf_title_style_controls();
		$this->tf_info_items_style_controls();
		do_action( 'tf/single-tour-contact-information/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_tour_contact_information_content',[
            'label' => esc_html__('Tour Contact Information', 'tourfic'),
        ]);

        do_action( 'tf/single-tour-contact-information/before-content/controls', $this );

		$this->add_control('icon_style',[
            'label' => esc_html__('Icon Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-tour-contact-information/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_card_style_controls() {
		$this->start_controls_section( 'card_style', [
			'label' => esc_html__( 'Card Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( "card_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-tour-booking-advantages" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( 'card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-tour-booking-advantages" => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .tf-tour-booking-advantages",
		] );

		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-tour-booking-advantages" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .tf-tour-booking-advantages',
		]);
		
		$this->end_controls_section();
	}

    protected function tf_title_style_controls() {
		$this->start_controls_section( 'title_style_section', [
			'label' => esc_html__( 'Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
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
				'{{WRAPPER}} .tf-tour-booking-advantages .tf-head-title h3' => 'text-align: {{VALUE}};',
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
				'{{WRAPPER}} .tf-tour-booking-advantages .tf-head-title h3' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-tour-booking-advantages .tf-head-title h3' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-tour-booking-advantages .tf-head-title h3",
		]);

		$this->end_controls_section();
	}

    protected function tf_info_items_style_controls() {
		$this->start_controls_section( 'content_style', [
			'label' => esc_html__( 'Items Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

        $this->add_responsive_control( "tf_items_gap", [
			'label'      => esc_html__( 'Items gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-tour-booking-advantages ul li" => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( "icon_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Icon', 'tourfic' ),
		] );

		$this->add_control( 'tf_item_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-tour-booking-advantages ul li i' => 'color: {{VALUE}};',
			],
		]);

        $this->add_responsive_control( "item_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 35,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-tour-booking-advantages ul li i" => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( "item_label_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Label', 'tourfic' ),
		] );

		$this->add_control( 'tf_item_label_color', [
			'label'     => esc_html__( 'Label Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-tour-booking-advantages ul li a' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Label Typography', 'tourfic' ),
			'name'     => "tf_item_label_typography",
			'selector' => "{{WRAPPER}} .tf-tour-booking-advantages ul li a",
		]);
		
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
        $email         = ! empty( $meta['email'] ) ? $meta['email'] : '';
        $phone         = ! empty( $meta['phone'] ) ? $meta['phone'] : '';
        $fax           = ! empty( $meta['fax'] ) ? $meta['fax'] : '';
        $website       = ! empty( $meta['website'] ) ? $meta['website'] : '';
		$style = !empty($settings['icon_style']) ? $settings['icon_style'] : 'style1';
		
        if ( $email || $phone || $fax || $website ) {
            ?>
            <div class="tf-tour-booking-advantages tf-box">
                <div class="tf-head-title">
                    <h3><?php echo ! empty( $meta['contact-info-section-title'] ) ? esc_html( $meta['contact-info-section-title'] ) : ''; ?></h3>
                </div>
                <div class="tf-booking-advantage-items">
                    <ul class="tf-list tf-icon-<?php echo esc_attr($style); ?>">
                        <?php
                        if ( ! empty( $phone ) ) { ?>
                            <li><i class="fa-solid fa-headphones"></i> <a href="tel:<?php echo esc_html( $phone ) ?>"><?php echo esc_html( $phone ) ?></a></li>
                        <?php } ?>
                        <?php
                        if ( ! empty( $email ) ) { ?>
                            <li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?php echo esc_html( $email ) ?>"><?php echo esc_html( $email ) ?></a></li>
                        <?php } ?>
                        <?php
                        if ( ! empty( $website ) ) { ?>
                            <li><i class="fa-solid fa-link"></i> <a target="_blank" href="<?php echo esc_html( $website ) ?>"><?php echo esc_html( $website ) ?></a></li>
                        <?php } ?>
                        <?php
                        if ( ! empty( $fax ) ) { ?>
                            <li><i class="fa-solid fa-fax"></i> <a href="tel:<?php echo esc_html( $fax ) ?>"><?php echo esc_html( $fax ) ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        <?php }
    }
}
