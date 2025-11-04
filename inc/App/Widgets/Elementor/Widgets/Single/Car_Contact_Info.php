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
 * car contact info
 */
class Car_Contact_Info extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-car-contact-info';
	}

	public function get_title() {
		return esc_html__( 'Car Contact Info', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-alert';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'car contact info',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-car-driver-info'];
	}

	protected function register_controls() {

		// $this->tf_content_layout_controls();

		do_action( 'tf/single-car-contact-info/before-style-controls', $this );
		$this->tf_card_style_controls();
		$this->tf_title_style_controls();
		$this->tf_avatar_style_controls();
		$this->tf_content_style_controls();
		$this->tf_social_style_controls();
		do_action( 'tf/single-car-contact-info/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_car_contact_info_content',[
            'label' => esc_html__('Car Contact Info', 'tourfic'),
        ]);

        do_action( 'tf/single-car-contact-info/before-content/controls', $this );

        $post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic')
		];
		$this->add_control('car_contact_info_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => $options,
        ]);

	    do_action( 'tf/single-car-contact-info/after-content/controls', $this );

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
				'{{WRAPPER}} .tf-driver-details .tf-driver-details-header h3' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-driver-details .tf-driver-details-header h3",
		]);

		$this->end_controls_section();
	}

    protected function tf_card_style_controls() {
		$this->start_controls_section( 'card_style', [
			'label' => esc_html__( 'Card Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control( "card_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-driver-details" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( 'card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-driver-details" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .tf-driver-details",
		] );
		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-driver-details" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .tf-driver-details',
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
				"{{WRAPPER}} .tf-driver-details .tf-driver-photo img" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
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
				'{{WRAPPER}} .tf-driver-details .tf-driver-photo img' => $this->tf_apply_dim( 'border-radius'),
			],
		] );

		$this->end_controls_section();
	}

    protected function tf_content_style_controls() {
        $this->start_controls_section( 'content_style', [
			'label' => esc_html__( 'Content Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

        $this->add_control( "name_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Name', 'tourfic' ),
		] );

		$this->add_control( 'tf_name_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-driver-details .tf-driver-photo .tf-driver-info h4' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_name_typography",
			'selector' => "{{WRAPPER}} .tf-driver-details .tf-driver-photo .tf-driver-info h4",
		]);

		$this->end_controls_section();
	}

    protected function tf_social_style_controls() {
        $this->start_controls_section( 'social_style', [
			'label' => esc_html__( 'Social Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

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
				"{{WRAPPER}} .tf-driver-details .tf-driver-photo .tf-driver-info .tf-driver-contact-info ul li svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

        $this->add_control( 'tf_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-driver-details .tf-driver-photo .tf-driver-info .tf-driver-contact-info ul li svg path" => 'stroke: {{VALUE}};',
			],
		] );

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

        // Information
        $car_information_section_status = ! empty( $meta['information_section'] ) ? $meta['information_section'] : '';
        $car_owner_name = ! empty( $meta['owner_name'] ) ? $meta['owner_name'] : '';
        $car_owner_email = ! empty( $meta['email'] ) ? $meta['email'] : '';
        $car_owner_phone = ! empty( $meta['phone'] ) ? $meta['phone'] : '';
        $car_owner_website = ! empty( $meta['website'] ) ? $meta['website'] : '';
        $car_owner_fax = ! empty( $meta['fax'] ) ? $meta['fax'] : '';
        $car_owner_owner_image = ! empty( $meta['owner_image'] ) ? $meta['owner_image'] : '';
        $owner_sec_title  = ! empty( $meta['owner_sec_title'] ) ? $meta['owner_sec_title'] : '';
        
        if(!empty($car_information_section_status)){ ?>
        <div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16">
            <div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                <?php if(!empty($owner_sec_title)){ ?>   
                    <h3 class="tf-section-title"><?php echo esc_html($owner_sec_title); ?></h3>
                <?php } ?>
            </div>
            <div class="tf-driver-photo tf-flex tf-flex-gap-16">
                <?php if(!empty($car_owner_owner_image)){ ?>
                <img src="<?php echo esc_url($car_owner_owner_image); ?>">
                <?php } ?>
                <div class="tf-driver-info">
                    <?php if(!empty($car_owner_name)){ ?>
                    <h4><?php echo esc_attr($car_owner_name); ?></h4>
                    <?php } ?>

                    <div class="tf-driver-contact-info">
                        <ul class="tf-flex tf-flex-gap-16">
                            <?php if(!empty($car_owner_email)){ ?>
                            <li>
                                <a href="mailto: <?php echo esc_attr($car_owner_email); ?>">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.3333 5.8335L10.8583 10.5835C10.601 10.7447 10.3036 10.8302 9.99996 10.8302C9.69636 10.8302 9.3989 10.7447 9.14163 10.5835L1.66663 5.8335M3.33329 3.3335H16.6666C17.5871 3.3335 18.3333 4.07969 18.3333 5.00016V15.0002C18.3333 15.9206 17.5871 16.6668 16.6666 16.6668H3.33329C2.41282 16.6668 1.66663 15.9206 1.66663 15.0002V5.00016C1.66663 4.07969 2.41282 3.3335 3.33329 3.3335Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="tf-tooltip-info">
                                        <p><?php echo esc_attr($car_owner_email); ?></p>
                                    </div>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(!empty($car_owner_phone)){ ?>
                            <li>
                                <a href="tel: <?php echo esc_attr($car_owner_phone); ?>">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.3333 14.0999V16.5999C18.3343 16.832 18.2867 17.0617 18.1937 17.2744C18.1008 17.487 17.9644 17.6779 17.7934 17.8348C17.6224 17.9917 17.4205 18.1112 17.2006 18.1855C16.9808 18.2599 16.7478 18.2875 16.5167 18.2666C13.9523 17.988 11.4892 17.1117 9.32498 15.7083C7.31151 14.4288 5.60443 12.7217 4.32499 10.7083C2.91663 8.53426 2.04019 6.05908 1.76665 3.48325C1.74583 3.25281 1.77321 3.02055 1.84707 2.80127C1.92092 2.58199 2.03963 2.38049 2.19562 2.2096C2.35162 2.03871 2.54149 1.90218 2.75314 1.80869C2.9648 1.7152 3.1936 1.6668 3.42499 1.66658H5.92499C6.32941 1.6626 6.72148 1.80582 7.02812 2.06953C7.33476 2.33324 7.53505 2.69946 7.59165 3.09992C7.69717 3.89997 7.89286 4.68552 8.17499 5.44158C8.2871 5.73985 8.31137 6.06401 8.24491 6.37565C8.17844 6.68729 8.02404 6.97334 7.79998 7.19992L6.74165 8.25825C7.92795 10.3445 9.65536 12.072 11.7417 13.2583L12.8 12.1999C13.0266 11.9759 13.3126 11.8215 13.6243 11.755C13.9359 11.6885 14.26 11.7128 14.5583 11.8249C15.3144 12.107 16.0999 12.3027 16.9 12.4083C17.3048 12.4654 17.6745 12.6693 17.9388 12.9812C18.203 13.2931 18.3435 13.6912 18.3333 14.0999Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="tf-tooltip-info">
                                        <p><?php echo esc_attr($car_owner_phone); ?></p>
                                    </div>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(!empty($car_owner_fax)){ ?>
                            <li>
                                <a href="tel: <?php echo esc_attr($car_owner_fax); ?>">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_918_9083)">
                                        <path d="M4.99996 14.9998H3.33329C2.89127 14.9998 2.46734 14.8242 2.15478 14.5117C1.84222 14.1991 1.66663 13.7752 1.66663 13.3332V9.1665C1.66663 8.72448 1.84222 8.30055 2.15478 7.98799C2.46734 7.67543 2.89127 7.49984 3.33329 7.49984H16.6666C17.1087 7.49984 17.5326 7.67543 17.8451 7.98799C18.1577 8.30055 18.3333 8.72448 18.3333 9.1665V13.3332C18.3333 13.7752 18.1577 14.1991 17.8451 14.5117C17.5326 14.8242 17.1087 14.9998 16.6666 14.9998H15M4.99996 7.49984V2.49984C4.99996 2.27882 5.08776 2.06686 5.24404 1.91058C5.40032 1.7543 5.61228 1.6665 5.83329 1.6665H14.1666C14.3876 1.6665 14.5996 1.7543 14.7559 1.91058C14.9122 2.06686 15 2.27882 15 2.49984V7.49984M5.83329 11.6665H14.1666C14.6269 11.6665 15 12.0396 15 12.4998V17.4998C15 17.9601 14.6269 18.3332 14.1666 18.3332H5.83329C5.37306 18.3332 4.99996 17.9601 4.99996 17.4998V12.4998C4.99996 12.0396 5.37306 11.6665 5.83329 11.6665Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_918_9083">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
                                    <div class="tf-tooltip-info">
                                        <p><?php echo esc_attr($car_owner_fax); ?></p>
                                    </div>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(!empty($car_owner_website)){ ?>
                            <li>
                                <a href="<?php echo esc_url($car_owner_website); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-link"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                    <div class="tf-tooltip-info">
                                        <p><?php echo esc_url($car_owner_website); ?></p>
                                    </div>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php }
    }
}
