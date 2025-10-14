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
 * car driver info
 */
class Car_Driver_Info extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-car-driver-info';
	}

	public function get_title() {
		return esc_html__( 'Car Driver Info', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-alert';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'car driver info',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-car-driver-info'];
	}

	protected function register_controls() {

		// $this->tf_content_layout_controls();

		do_action( 'tf/single-car-driver-info/before-style-controls', $this );
		$this->tf_car_driver_info_style_controls();
		do_action( 'tf/single-car-driver-info/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_car_driver_info_content',[
            'label' => esc_html__('Car Driver Info', 'tourfic'),
        ]);

        do_action( 'tf/single-car-driver-info/before-content/controls', $this );

        $post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic')
		];
		$this->add_control('car_driver_info_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => $options,
        ]);

	    do_action( 'tf/single-car-driver-info/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_car_driver_info_style_controls() {
		$this->start_controls_section( 'car_driver_info_style_section', [
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

        // Driver Info 
		$driver_sec_title = ! empty( $meta['driver_sec_title'] ) ? $meta['driver_sec_title'] : '';
		$car_driver_incude = ! empty( $meta['driver_included'] ) ? $meta['driver_included'] : '';
		$car_driverinfo_status = ! empty( $meta['car_driverinfo_section'] ) ? $meta['car_driverinfo_section'] : '';
		$driver_name = ! empty( $meta['driver_name'] ) ? $meta['driver_name'] : '';
		$driver_email = ! empty( $meta['driver_email'] ) ? $meta['driver_email'] : '';
		$driver_phone = ! empty( $meta['driver_phone'] ) ? $meta['driver_phone'] : '';
		$driver_age = ! empty( $meta['driver_age'] ) ? $meta['driver_age'] : '';
		$driver_address = ! empty( $meta['driver_address'] ) ? $meta['driver_address'] : '';
		$driver_image = ! empty( $meta['driver_image'] ) ? $meta['driver_image'] : '';
        
        if(!empty($car_driver_incude) && !empty($car_driverinfo_status)){ ?>
			<div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16">
				<div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
					<?php if(!empty($driver_sec_title)){ ?>   
						<h3 class="tf-section-title"><?php echo esc_html($driver_sec_title); ?></h3>
					<?php } ?>
					<span>
					<i class="ri-shield-check-line"></i> <?php esc_html_e("Verified", "tourfic"); ?>
					</span>
				</div>
				<div class="tf-driver-photo tf-flex tf-flex-gap-16">
					<?php if(!empty($driver_image)){ ?>
					<img src="<?php echo esc_url($driver_image); ?>">
					<?php } ?>
					<div class="tf-driver-info">
						<?php if(!empty($driver_name)){ ?>
						<h4><?php echo esc_attr($driver_name); ?></h4>
						<?php } ?>
						<?php if(!empty($driver_age)){ ?>
							<p> <?php esc_html_e("Age", "tourfic"); ?> <?php echo esc_attr($driver_age); ?> <?php esc_html_e("Years", "tourfic"); ?>
							</p>
						<?php } ?>

						<div class="tf-driver-contact-info">
							<ul class="tf-flex tf-flex-gap-16">
								<?php if(!empty($driver_email)){ ?>
								<li>
									<a href="mailto: <?php echo esc_attr($driver_email); ?>">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M18.3333 5.8335L10.8583 10.5835C10.601 10.7447 10.3036 10.8302 9.99996 10.8302C9.69636 10.8302 9.3989 10.7447 9.14163 10.5835L1.66663 5.8335M3.33329 3.3335H16.6666C17.5871 3.3335 18.3333 4.07969 18.3333 5.00016V15.0002C18.3333 15.9206 17.5871 16.6668 16.6666 16.6668H3.33329C2.41282 16.6668 1.66663 15.9206 1.66663 15.0002V5.00016C1.66663 4.07969 2.41282 3.3335 3.33329 3.3335Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<div class="tf-tooltip-info">
											<p><?php echo esc_attr($driver_email); ?></p>
										</div>
									</a>
								</li>
								<?php } ?>
								<?php if(!empty($driver_phone)){ ?>
								<li>
									<a href="tel: <?php echo esc_attr($driver_phone); ?>">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M18.3333 14.0999V16.5999C18.3343 16.832 18.2867 17.0617 18.1937 17.2744C18.1008 17.487 17.9644 17.6779 17.7934 17.8348C17.6224 17.9917 17.4205 18.1112 17.2006 18.1855C16.9808 18.2599 16.7478 18.2875 16.5167 18.2666C13.9523 17.988 11.4892 17.1117 9.32498 15.7083C7.31151 14.4288 5.60443 12.7217 4.32499 10.7083C2.91663 8.53426 2.04019 6.05908 1.76665 3.48325C1.74583 3.25281 1.77321 3.02055 1.84707 2.80127C1.92092 2.58199 2.03963 2.38049 2.19562 2.2096C2.35162 2.03871 2.54149 1.90218 2.75314 1.80869C2.9648 1.7152 3.1936 1.6668 3.42499 1.66658H5.92499C6.32941 1.6626 6.72148 1.80582 7.02812 2.06953C7.33476 2.33324 7.53505 2.69946 7.59165 3.09992C7.69717 3.89997 7.89286 4.68552 8.17499 5.44158C8.2871 5.73985 8.31137 6.06401 8.24491 6.37565C8.17844 6.68729 8.02404 6.97334 7.79998 7.19992L6.74165 8.25825C7.92795 10.3445 9.65536 12.072 11.7417 13.2583L12.8 12.1999C13.0266 11.9759 13.3126 11.8215 13.6243 11.755C13.9359 11.6885 14.26 11.7128 14.5583 11.8249C15.3144 12.107 16.0999 12.3027 16.9 12.4083C17.3048 12.4654 17.6745 12.6693 17.9388 12.9812C18.203 13.2931 18.3435 13.6912 18.3333 14.0999Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<div class="tf-tooltip-info">
											<p><?php echo esc_attr($driver_phone); ?></p>
										</div>
									</a>
								</li>
								<?php } ?>
								<?php if(!empty($driver_address)){ ?>
								<li>
									<a href="https://maps.google.com/maps?q=<?php echo esc_html($driver_address); ?>" target="_blank">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M16.6667 8.33317C16.6667 12.494 12.0509 16.8273 10.5009 18.1657C10.3565 18.2742 10.1807 18.333 10 18.333C9.81938 18.333 9.6436 18.2742 9.49921 18.1657C7.94921 16.8273 3.33337 12.494 3.33337 8.33317C3.33337 6.56506 4.03575 4.86937 5.286 3.61913C6.53624 2.36888 8.23193 1.6665 10 1.6665C11.7682 1.6665 13.4638 2.36888 14.7141 3.61913C15.9643 4.86937 16.6667 6.56506 16.6667 8.33317Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M10 10.8332C11.3808 10.8332 12.5 9.71388 12.5 8.33317C12.5 6.95246 11.3808 5.83317 10 5.83317C8.61933 5.83317 7.50004 6.95246 7.50004 8.33317C7.50004 9.71388 8.61933 10.8332 10 10.8332Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<div class="tf-tooltip-info">
											<p><?php echo esc_attr($driver_address); ?></p>
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
