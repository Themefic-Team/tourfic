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
 * Tour Information
 */
class Tour_Information extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-tour-information';
	}

	public function get_title() {
		return esc_html__( 'Tour Information', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'tour information',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-tour-information'];
	}

	protected function register_controls() {

		// $this->tf_content_layout_controls();

		do_action( 'tf/single-tour-information/before-style-controls', $this );
		$this->tf_card_style_controls();
		$this->tf_info_items_style_controls();
		$this->tf_person_card_style_controls();
		$this->tf_price_style_controls();
		do_action( 'tf/single-tour-information/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_tour_information_content',[
            'label' => esc_html__('Tour Information', 'tourfic'),
        ]);

        do_action( 'tf/single-tour-information/before-content/controls', $this );

		

	    do_action( 'tf/single-tour-information/after-content/controls', $this );

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
				"{{WRAPPER}} .tf-trip-info" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( 'card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-trip-info" => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .tf-trip-info",
		] );

		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-info" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .tf-trip-info',
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
				"{{WRAPPER}} .tf-trip-info .tf-short-info ul li" => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
				'{{WRAPPER}} .tf-trip-info .tf-short-info ul li i' => 'color: {{VALUE}};',
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
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-info .tf-short-info ul li i" => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
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
				'{{WRAPPER}} .tf-trip-info .tf-short-info ul li' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Label Typography', 'tourfic' ),
			'name'     => "tf_item_label_typography",
			'selector' => "{{WRAPPER}} .tf-trip-info .tf-short-info ul li",
		]);
		
		$this->end_controls_section();
	}

    protected function tf_person_card_style_controls() {
		$this->start_controls_section( 'person_card_style', [
			'label' => esc_html__( 'Traveller Category Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( "person_card_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-info .person-info" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( 'person_card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-trip-info .person-info" => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "person_card_border",
			'selector' => "{{WRAPPER}} .tf-trip-info .person-info",
		] );

		$this->add_control( "person_card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-info .person-info" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

		$this->add_responsive_control( "tf_tcategory_items_gap", [
			'label'      => esc_html__( 'Items gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-person-info ul" => 'gap: {{SIZE}}{{UNIT}};',
			],
		] );

        $this->add_control( "tcategory_icon_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Icon', 'tourfic' ),
		] );

        $this->add_responsive_control( "tcategory_icon_size", [
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
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-trip-info .person-info i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "tcategory_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-trip-info .person-info i" => 'color: {{VALUE}}',
			],
		] );

        $this->add_control( "tcategory_label_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Label', 'tourfic' ),
		] );

        $this->add_control( 'tcategory_label_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-trip-info .person-info p' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tcategory_label_typography",
			'selector' => "{{WRAPPER}} .tf-trip-info .person-info p",
		]);
		
		$this->end_controls_section();
	}

    protected function tf_price_style_controls() {
		$this->start_controls_section( 'price_style', [
			'label' => esc_html__( 'Price Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

        $this->add_control( 'price_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-trip-info .tf-trip-pricing .tf-price-amount' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "price_typography",
			'selector' => "{{WRAPPER}} .tf-trip-info .tf-trip-pricing .tf-price-amount",
		]);

        $this->add_control( "price_label_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Label', 'tourfic' ),
		] );

        $this->add_control( 'price_label_color', [
			'label'     => esc_html__( 'Label Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-trip-info .tf-trip-pricing .tf-price-label, .tf-trip-info .tf-trip-pricing .tf-price-label-bttm' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Label Typography', 'tourfic' ),
			'name'     => "price_label_typography",
			'selector' => "{{WRAPPER}} .tf-trip-info .tf-trip-pricing .tf-price-label, .tf-trip-info .tf-trip-pricing .tf-price-label-bttm",
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
        $avail_prices = Pricing::instance( $this->post_id )->get_avail_price();
	    $meta = get_post_meta( $this->post_id, 'tf_tours_opt', true );
        $tour_duration = ! empty( $meta['duration'] ) ? $meta['duration'] : '';
	    $tour_refund_policy = ! empty( $meta['refund_des'] ) ? $meta['refund_des'] : '';
	    $duration_time = ! empty( $meta['duration_time'] ) ? $meta['duration_time'] : 'Day';
        $pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
        $group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
        $adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
        $children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
        $infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
        $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
        $tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
        $disable_adult  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
        $disable_child  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
        $disable_infant = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

		?>
        <div class="tf-trip-info tf-box tf-template-section">
            <div class="tf-trip-info-inner tf-flex tf-flex-space-bttn tf-flex-align-center tf-flex-gap-8">
                <!-- Single Tour short details -->
                <div class="tf-short-info">
                    <ul class="tf-list">
                        <?php 
                        if ( ! empty( $tour_duration ) ) { ?>
                            <li class="tf-flex tf-flex-gap-8">
                                <i class="fa-regular fa-clock"></i>
                                <?php echo esc_html( $tour_duration ); ?>
                                <?php
                                if ( $tour_duration > 1 ) {
                                    $dur_string         = 's';
                                    $_duration_time = $duration_time . $dur_string;
                                } else {
                                    $_duration_time = $duration_time;
                                }
                                echo " " . esc_html( $_duration_time );
                                ?>
                            </li>
                        <?php }

                            
                        $tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];
                        $allow_package_pricing = ! empty( $meta['allow_package_pricing'] ) ? $meta['allow_package_pricing'] : '';
                        $group_package_pricing = ! empty( $meta['group_package_pricing'] ) ? $meta['group_package_pricing'] : '';
                        $tf_package_pricing = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';

                        $tf_max_people = [];
                        $tf_max_capacity = [];
                        if( !empty($tour_availability_data) && ('person'==$pricing_rule || 'group'==$pricing_rule) ){
                            foreach ($tour_availability_data as $data) {
                                if ($data['status'] !== 'available') {
                                    continue;
                                }
                
                                if($data['pricing_type'] == 'person'){
                                    if (!empty($data['max_person'])) {
                                        $tf_max_people [] = $data['max_person'];
                                    } 
                                    if (!empty($data['max_capacity'])) {
                                        $tf_max_capacity [] = $data['max_capacity'];
                                    } 
                                }
                
                                if($data['pricing_type'] == 'group' && !empty($allow_package_pricing) && !empty($group_package_pricing) ){
                                    if(!empty($data['options_count'])){
                                        for($i = 0; $i < $data['options_count']; $i++){
                                            if (!empty($data['tf_option_max_person_'.$i])) {
                                                $tf_max_people [] = $data['tf_option_max_person_'.$i];
                                            }
                                        }
                                    }
                                }
                
                                if($data['pricing_type'] == 'group' && (empty($allow_package_pricing) || empty($group_package_pricing)) ){
                                    if (!empty($data['max_person'])) {
                                        $tf_max_people [] = $data['max_person'];
                                    } 
                                    if (!empty($data['max_capacity'])) {
                                        $tf_max_capacity [] = $data['max_capacity'];
                                    }
                                }
                                

                            }
                        }

                        if('package'==$pricing_rule && !empty($tf_package_pricing)){
                            foreach($tf_package_pricing as $package){
                                if (!empty($package['max_adult'])) {
                                    $tf_max_people [] = $package['max_adult'];
                                } 
                            }
                        }

                        if ( ! empty( $tf_max_capacity ) ) {
                            $tf_tour_booking_limit = max( $tf_max_capacity );
                        }
                        if ( ! empty( $tf_max_people ) ) {
                            $max_people = max( $tf_max_people );
                        }

                        if ( ! empty( $tf_tour_booking_limit ) || ! empty( $max_people ) ) { ?>
                            <li class="tf-flex tf-flex-gap-8">
                                <i class="fa-solid fa-people-group"></i>
                                <?php if ( ! empty( $tf_tour_booking_limit ) ) {
                                    echo esc_html__( "Maximum Capacity: ", "tourfic" );
                                    echo esc_html($tf_tour_booking_limit);
                                } else {
                                    echo esc_html__( "Maximum Allowed Per Booking: ", "tourfic" );
                                    echo esc_html($max_people);
                                } ?>
                            </li>
                        <?php }

                        if ( ! empty( $tour_refund_policy ) ) { ?>
                            <li class="tf-flex tf-flex-gap-8">
                                <i class="fa-solid fa-person-walking-arrow-loop-left"></i>
                                <?php echo esc_html( $tour_refund_policy ); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : 
                    $adult_price = !empty($avail_prices['adult_price']) ? $avail_prices['adult_price'] : $adult_price;
                    $child_price = !empty($avail_prices['child_price']) ? $avail_prices['child_price'] : $children_price;
                    $infant_price = !empty($avail_prices['infant_price']) ? $avail_prices['infant_price'] : $infant_price;
                    $group_price = !empty($avail_prices['group_price']) ? $avail_prices['group_price'] : $group_price;
                ?>
                    <!-- Single Tour Person details -->
                    <div class="tf-trip-person-info tf-flex tf-flex-gap-12">
                        <ul class="tf-flex tf-flex-gap-12">
                            <?php
                            if ( $pricing_rule == 'group' ) {

                                echo '<li data="group" class="person-info active"><i class="fa-solid fa-users"></i><p>' . esc_html__( "Group", "tourfic" ) . '</p></li>';

                            } elseif ( $pricing_rule == 'person' ) {

                                if ( ! $disable_adult && ! empty( $adult_price ) ) {
                                    echo '<li data="adult" class="person-info active"><i class="fa-solid fa-user"></i><p>' . esc_html__( "Adult", "tourfic" ) . '</p></li>';
                                }
                                if ( ! $disable_child && ! empty( $child_price ) ) {
                                    $active_class = $disable_adult || empty( $adult_price) ? 'active' : '';
                                    echo '<li data="child" class="person-info '. esc_attr($active_class) .'"><i class="fa-solid fa-child"></i><p>' . esc_html__( "Child", "tourfic" ) . '</p></li>';
                                }
                                if ( ! $disable_adult && ( ! $disable_infant && ! empty( $infant_price ) ) ) {
                                    $active_class = ($disable_adult || empty( $adult_price)) && ($disable_child || empty( $child_price )) ? 'active' : '';
                                    echo '<li data="infant" class="person-info '. esc_attr($active_class) .'"><i class="fa-solid fa-baby"></i><p>' . esc_html__( "Infant", "tourfic" ) . '</p></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <?php if ( $pricing_rule == 'group' ) { ?>
                        <div class="tf-trip-pricing tf-flex tf-group active">
                            <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                            <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($group_price)); ?></span>
                            <span class="tf-price-label-bttm"><?php esc_html_e( "Per Group", "tourfic" ); ?></span>
                        </div>
                    <?php } elseif ( $pricing_rule == 'person' ) { ?>
                        <?php if ( ! $disable_adult && ! empty( $adult_price ) ) { ?>
                            <div class="tf-trip-pricing tf-flex tf-adult active">
                                <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                                <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($adult_price)); ?></span>
                                <span class="tf-price-label-bttm"><?php esc_html_e( "Per Adult", "tourfic" ); ?></span>
                            </div>
                        <?php }
                        if ( ! $disable_child && ! empty( $child_price ) ) { ?>
                            <div class="tf-trip-pricing tf-flex tf-child <?php echo $disable_adult || empty( $adult_price ) ? esc_attr('active') : ''; ?>">
                                <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                                <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($child_price)); ?></span>
                                <span class="tf-price-label-bttm"><?php esc_html_e( "Per Child", "tourfic" ); ?></span>
                            </div>
                        <?php }
                        if ( ! $disable_adult && ( ! $disable_infant && ! empty( $infant_price ) ) ) { ?>
                            <div class="tf-trip-pricing tf-flex tf-infant <?php echo ($disable_adult || empty( $adult_price)) && ($disable_child || empty( $child_price )) ? esc_attr('active') : ''; ?>">
                                <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                                <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($infant_price)); ?></span>
                                <span class="tf-price-label-bttm"><?php esc_html_e( "Per Infant", "tourfic" ); ?></span>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
