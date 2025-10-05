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

		$this->tf_content_layout_controls();

		do_action( 'tf/single-tour-information/before-style-controls', $this );
		$this->tf_tour_information_style_controls();
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

    protected function tf_tour_information_style_controls() {
		$this->start_controls_section( 'tour_information_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_responsive_control( "tf_nav_item_gap", [
			'label'      => esc_html__( 'Nav Items Gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
			],
			'range'      => [
				'px' => [
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-single-gallery__style-1.tf-hero-gallery .tf-gallery" => 'gap: {{SIZE}}{{UNIT}};',
			],
            'condition' => [
				'tour_information_style' => ['style1'],
			],
		] );

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
