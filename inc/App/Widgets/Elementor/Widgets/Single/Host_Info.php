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
		$this->tf_host_info_style_controls();
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

    protected function tf_host_info_style_controls() {
		$this->start_controls_section( 'host_info_style_section', [
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
