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
use \Tourfic\Classes\Apartment\Apartment;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * apartment host info
 */
class Host_Info extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-apartment-host-info';
	}

	public function get_title() {
		return esc_html__( 'Host Info', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-person';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'apartment host info',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-host-info'];
	}

	protected function register_controls() {

		// $this->tf_content_layout_controls();

		do_action( 'tf/single-host-info/before-style-controls', $this );
		$this->tf_card_style_controls();
		$this->tf_avatar_style_controls();
		$this->tf_title_style_controls();
		$this->tf_content_style_controls();
		$this->tf_button_style_controls();
		do_action( 'tf/single-host-info/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_host_info_content',[
            'label' => esc_html__('Host Info', 'tourfic'),
        ]);

        do_action( 'tf/single-host-info/before-content/controls', $this );

        $post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic')
		];
		$this->add_control('host_info_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => $options,
        ]);

	    do_action( 'tf/single-host-info/after-content/controls', $this );

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
				"{{WRAPPER}} .host-details" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( 'card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .host-details" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .host-details",
		] );
		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .host-details" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .host-details',
		]);
		
		$this->end_controls_section();
	}

	protected function tf_avatar_style_controls() {
		$this->start_controls_section( 'avatart_style', [
			'label' => esc_html__( 'Avatar Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		
		$this->add_responsive_control( "tf_avatar_size", [
			'label'      => esc_html__( 'Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
			],
			'range'      => [
				'px' => [
					'min'  => 20,
					'max'  => 400,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .host-details .host-top img" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( "avatar_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .host-details .host-top img' => $this->tf_apply_dim( 'border-radius'),
			],
		] );

		$this->end_controls_section();
	}

    protected function tf_title_style_controls() {
		$this->start_controls_section( 'title_style', [
			'label' => esc_html__( 'Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

        $this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .host-details .host-meta h4' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .host-details .host-meta h4",
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
				'{{WRAPPER}} .host-details .host-meta h4' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->end_controls_section();
	}

    protected function tf_content_style_controls() {
        $this->start_controls_section( 'content_style', [
			'label' => esc_html__( 'Content Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'tf_content_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-apartment-joined-text' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_content_typography",
			'selector' => "{{WRAPPER}} .tf-apartment-joined-text",
		]);
		$this->end_controls_section();
	}

    protected function tf_button_style_controls() {
        $this->start_controls_section( 'button_style', [
			'label' => esc_html__( 'Button Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => "btn_typography",
			'selector' => "{{WRAPPER}} .tf_btn",
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
				"{{WRAPPER}} .tf_btn" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf_btn svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_bg_color", [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "btn_border",
			'selector' => "{{WRAPPER}} .tf_btn",
		] );
		$this->add_control( "btn_border_radius", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn" => $this->tf_apply_dim( 'border-radius' ),
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
				"{{WRAPPER}} .tf_btn:hover" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf_btn:hover svg path" => 'fill: {{VALUE}};',
			],
		] );
		
		$this->add_control( "btn_hover_bg_color", [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_hover_border_color", [
			'label'     => __( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover" => 'border-color: {{VALUE}};',
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
				"{{WRAPPER}} .tf_btn" => 'width: {{SIZE}}{{UNIT}};',
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
				"{{WRAPPER}} .tf_btn" => 'height: {{SIZE}}{{UNIT}};',
			],
		] );
		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type !== 'tf_apartment'){
            return;
        }
	    $meta = get_post_meta( $this->post_id, 'tf_apartment_opt', true );
        $post_author_id = get_post_field( 'post_author', get_the_ID() );
        $author_info    = get_userdata( $post_author_id );
        ?>
        <div class="host-details">
            <div class="host-top">
                <img src="<?php echo esc_url( get_avatar_url( $post_author_id ) ); ?>" alt="">
                <div class="host-meta">
                    <?php echo sprintf( '<h4>%s %s</h4>', esc_html__( 'Hosted by', 'tourfic' ), esc_html( $author_info->display_name ) ); ?>
                    <?php echo sprintf( '<span class="tf-apartment-joined-text">%s <span>:</span> <span>%s</span></span>', esc_html__( 'Joined', 'tourfic' ), wp_kses_post( gmdate( 'F Y', strtotime( $author_info->user_registered ) ) ) ); ?>
                    <?php Apartment::tf_apartment_host_rating( $post_author_id ) ?>
                </div>
            </div>
            <div class="host-bottom">
                <?php if(!empty( get_the_author_meta( 'description', $post_author_id ))) : ?>
                    <h5><?php echo esc_html__( "During Your Stay", 'tourfic' ); ?></h5>
                    <p class="host-desc">
                        <?php echo wp_kses_post( get_the_author_meta( 'description', $post_author_id ) ); ?>
                    </p>
                <?php endif; ?>

                <ul>
                    <?php
                    if ( ! empty( get_the_author_meta( 'language', $post_author_id ) ) ) {
                        echo sprintf( '<li>%s <span>%s</span></li>', esc_html__( 'Language: ', 'tourfic' ), wp_kses_post( get_the_author_meta( 'language', $post_author_id ) ) );
                    }
                    ?>
                </ul>
                <a href="javaScript:void(0);" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn tf_btn_white tf_btn_full"><i class="fas fa-phone"></i><?php esc_html_e( 'Contact Host', 'tourfic' ) ?></a>
            </div>
        </div>
        <?php
    }
}
