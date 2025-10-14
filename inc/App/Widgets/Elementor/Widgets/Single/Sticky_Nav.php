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
use \Tourfic\Classes\Car_Rental\Pricing;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Sticky nav
 */
class Sticky_Nav extends Widget_Base {

	use \Tourfic\Traits\Singleton;

    protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-sticky-nav';
	}

	public function get_title() {
		return esc_html__( 'Sticky Nav', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-form-vertical';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'sticky nav',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-sticky-nav'];
	}

	protected function register_controls() {

		do_action( 'tf/single-sticky-nav/before-style-controls', $this );
		$this->tf_sticky_nav_style_controls();
		do_action( 'tf/single-sticky-nav/after-style-controls', $this );
	}

    protected function tf_sticky_nav_style_controls() {
		$this->start_controls_section( 'sticky-nav-style', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_sticky_nav_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-details-menu' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control( 'tf_sticky_nav_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-details-menu ul li a' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control( 'tf_sticky_nav_active_color', [
			'label'     => esc_html__( 'Active Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-details-menu ul li a.tf-hashlink' => 'color: {{VALUE}}; border-color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_sticky_nav_typography",
			'selector' => "{{WRAPPER}} .tf-details-menu ul li a",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        $comments_query = new \WP_Comment_Query( array(
            'post_id' => $this->post_id,
            'status'  => 'approve',
            'type'    => 'comment',
        ) );
        $comments       = $comments_query->comments;

		if($this->post_type == 'tf_hotel'){
			$this->tf_hotel_sticky_nav($comments);
        } elseif($this->post_type == 'tf_tours'){
			$this->tf_tour_sticky_nav($comments);
        } elseif($this->post_type == 'tf_apartment'){
			$this->tf_apartment_sticky_nav($comments);
        } elseif($this->post_type == 'tf_carrental'){
			$this->tf_car_sticky_nav($comments);
        } else {
			return;
		}
	}

    private function tf_hotel_sticky_nav($comments) {
		$meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
        ?>
        <div class="tf-single-hotel-sticky-nav tf-single-template__two">
            <div class="tf-details-menu tf-hotel-details-menu">
                <ul>
                    <li><a class="tf-hashlink" href="#tf-hotel-overview">
                        <?php esc_html_e("Overview", "tourfic"); ?>
                    </a></li>

                    <?php if( !empty($meta['tf_rooms']))  : ?>
                        <li><a href="#tf-hotel-rooms">
                            <?php esc_html_e("Rooms", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["hotel-facilities"] )) : ?>
                        <li><a href="#tf-hotel-facilities">
                            <?php esc_html_e("Facilities", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $comments )) : ?>
                        <li><a href="#tf-hotel-reviews">
                            <?php esc_html_e("Reviews", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["faq"] )) : ?>
                        <li><a href="#tf-hotel-faq">
                            <?php esc_html_e("FAQ's", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["tc"] )): ?>
                        <li><a href="#tf-hotel-policies">
                            <?php esc_html_e("Policies", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php
	}

	private function tf_tour_sticky_nav($comments) {
		$meta = get_post_meta($this->post_id, 'tf_tours_opt', true);
        ?>
        <div class="tf-single-tour-sticky-nav tf-single-template__two">
            <div class="tf-details-menu tf-tour-details-menu">
                <ul>
                    <li><a class="tf-hashlink" href="#tf-tour-overview">
                        <?php esc_html_e("Overview", "tourfic"); ?>
                    </a></li>

                    <?php if( !empty( $meta["itinerary"] )) : ?>
                        <li><a href="#tf-tour-itinerary">
                            <?php esc_html_e("Tour Plan", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["faqs"] )) : ?>
                        <li><a href="#tf-tour-faq">
                            <?php esc_html_e("FAQ's", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if(!empty( $meta["terms_conditions"] ) ) : ?>
                        <li><a href="#tf-tour-policies">
                            <?php esc_html_e("Policies", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if(!empty($comments)) : ?>
                        <li><a href="#tf-tour-reviews">
                            <?php esc_html_e("Reviews", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php
	}

	private function tf_apartment_sticky_nav($comments) {
		$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);
        ?>
        <div class="tf-single-apartment-sticky-nav tf-single-template__two">
            <div class="tf-details-menu tf-apartment-details-menu">
                <ul>
                    <li><a class="tf-hashlink" href="#tf-apartment-overview">
                        <?php esc_html_e("Overview", "tourfic"); ?>
                    </a></li>

                    <?php if( !empty( $meta["rooms"])) : ?>
                        <li><a href="#tf-apartment-rooms">
                            <?php esc_html_e("Rooms", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["house_rules"])) : ?>
                        <li><a href="#tf-apartment-rules">
                            <?php esc_html_e("House Rules", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if(!empty( $meta["faq"])) : ?>
                        <li><a href="#tf-apartment-faq">
                            <?php esc_html_e("FAQ's", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty($comments) ) : ?>
                        <li><a href="#tf-apartment-reviews">
                            <?php esc_html_e("Reviews", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["terms_and_conditions"]) ) : ?>
                        <li><a href="#tf-apartment-policies">
                            <?php esc_html_e("Policies", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php
	}

	private function tf_car_sticky_nav() {
		$meta = get_post_meta($this->post_id, 'tf_carrental_opt', true);
	    $benefits = ! empty( $meta['benefits'] ) ? $meta['benefits'] : '';
        $includes = ! empty( $meta['inc'] ) ? $meta['inc'] : '';
        $excludes = ! empty( $meta['exc'] ) ? $meta['exc'] : '';
        $address = !empty( Helper::tf_data_types($meta['map'])['address'] ) ? Helper::tf_data_types($meta['map'])['address'] : '';
        $faqs = ! empty( $meta['faq'] ) ? $meta['faq'] : '';
        $tc = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';

        $tf_pickup_date = !empty($_GET['pickup_date']) ? sanitize_text_field( wp_unslash($_GET['pickup_date']) ) : '';
        $tf_dropoff_date = !empty($_GET['dropoff_date']) ? sanitize_text_field( wp_unslash($_GET['dropoff_date']) ) : '';
        // Pull options from settings or set fallback values
        $disable_car_time_slot = !empty(Helper::tfopt('disable-car-time-slots')) ? boolval(Helper::tfopt('disable-car-time-slots')) : false;
        $car_time_slots = !empty(Helper::tfopt('car_time_slots')) ? Helper::tfopt('car_time_slots') : '';
        $unserialize_car_time_slots = !empty($car_time_slots) ? unserialize($car_time_slots) : array();

        $time_interval = 30;
        $start_time_str = '00:00';
        $end_time_str   = '23:30';
        $default_time_str = '10:00';
        $next_current_day = gmdate('l', strtotime('+1 day'));
        $date_format_for_users         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

        if($disable_car_time_slot){
            $time_interval = !empty(Helper::tfopt('car_time_interval')) ? intval(Helper::tfopt('car_time_interval')) : 30;
            if (!empty($unserialize_car_time_slots)) {
                foreach ($unserialize_car_time_slots as $slot) {
                    if (isset($slot['day']) && strtolower($slot['day']) == strtolower($next_current_day)) {
                        $start_time_str = !empty($slot['pickup_time']) ? $slot['pickup_time'] : $start_time_str;
                        $end_time_str   = !empty($slot['drop_time']) ? $slot['drop_time'] : $end_time_str;
                        if ( strtotime($start_time_str) >= strtotime('10:00') ) {
                            $default_time_str = $start_time_str;
                        }
                        break; 
                    }
                }
            }
        }

        // Convert string times to timestamps
        $start_time = strtotime($start_time_str);
        $end_time   = strtotime($end_time_str);
        $default_time = gmdate('g:i A', strtotime($default_time_str));

        // Use selected time from GET or fall back to default
        $selected_pickup_time = !empty($_GET['pickup_time']) ? sanitize_text_field( wp_unslash($_GET['pickup_time']) ) : $default_time;
        $selected_dropoff_time = !empty($_GET['dropoff_time']) ? sanitize_text_field( wp_unslash($_GET['dropoff_time']) ) : $default_time;

        $total_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $start_time_str, $end_time_str); 
		?>
        <div class="tf-single-car-sticky-nav tf-single-template__one">
            <div class="tf-single-booking-bar">
                <div class="tf-container">
                    <div class="tf-top-booking-bar tf-flex tf-flex-space-bttn tf-flex-align-center">
                        <div class="tf-details-menu">
                            <ul>
                            <?php if( !empty(Helper::get_status_by_label('Description', 'car')) ){ ?>
                                <li class="active" data-menu="<?php echo esc_attr('tf-description'); ?>">
                                    <a class="tf-hashlink" href="#tf-description">
                                        <?php esc_html_e("Description", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if( !empty(Helper::get_status_by_label('Car info', 'car')) ){ ?>
                                <li data-menu="<?php echo esc_attr('tf-car-info'); ?>">
                                    <a class="tf-hashlink" href="#tf-car-info">
                                        <?php esc_html_e("Car info", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if(!empty(Helper::get_status_by_label('Benefits', 'car')) && !empty($benefits)){ ?>
                                <li data-menu="<?php echo esc_attr('tf-benefits'); ?>">
                                    <a class="tf-hashlink" href="#tf-benefits">
                                        <?php esc_html_e("Benefits", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if(!empty(Helper::get_status_by_label('Include/Exclude', 'car')) && (!empty($includes) || !empty($excludes))){ ?>
                                <li data-menu="<?php echo esc_attr('tf-inc-exc'); ?>">
                                    <a class="tf-hashlink" href="#tf-inc-exc">
                                        <?php esc_html_e("Include/Excluce", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if(!empty(Helper::get_status_by_label('Location', 'car')) && !empty($address)){ ?>
                                <li data-menu="<?php echo esc_attr('tf-location'); ?>">
                                    <a class="tf-hashlink" href="#tf-location">
                                        <?php esc_html_e("Location", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if(!empty(Helper::get_status_by_label('Review', 'car')) ){ ?>
                                <li data-menu="<?php echo esc_attr('tf-reviews'); ?>">
                                    <a class="tf-hashlink" href="#tf-reviews">
                                        <?php esc_html_e("Reviews", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if(!empty(Helper::get_status_by_label('FAQs', 'car')) && !empty($faqs)){ ?>
                                <li data-menu="<?php echo esc_attr('tf-faq'); ?>">
                                    <a class="tf-hashlink" href="#tf-faq">
                                        <?php esc_html_e("FAQ's", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if(!empty(Helper::get_status_by_label('Terms & Conditions', 'car')) && !empty($tc)){ ?>
                                <li data-menu="<?php echo esc_attr('tf-tc'); ?>">
                                    <a class="tf-hashlink" href="#tf-tc">
                                        <?php esc_html_e("Terms & Conditions", "tourfic"); ?>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="tf-top-bar-booking tf-flex tf-flex-gap-32">
                            <div class="tf-price-header">
                                <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                                <?php echo $total_prices['sale_price'] ? wp_kses_post( wc_price($total_prices['sale_price']) ) : '' ?></h2>
                                <p><?php echo wp_kses_post(Pricing::is_taxable($meta)); ?></p>
                            </div>
                            <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 tf-back-to-booking">
                                <?php echo esc_html( apply_filters("tf_car_booking_form_submit_button_text", 'Continue' ) ); ?>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tf-details-menu tf-car-details-menu">
                <ul>
                    <li class="active" data-menu="<?php echo esc_attr('tf-description'); ?>">
                        <a class="tf-hashlink" href="#tf-description">
                            <?php esc_html_e("Description", "tourfic"); ?>
                        </a>
                    </li>
                    <li data-menu="<?php echo esc_attr('tf-car-info'); ?>">
                        <a class="tf-hashlink" href="#tf-car-info">
                            <?php esc_html_e("Car info", "tourfic"); ?>
                        </a>
                    </li>
                    <?php if(!empty($benefits)){ ?>
                    <li data-menu="<?php echo esc_attr('tf-benefits'); ?>">
                        <a class="tf-hashlink" href="#tf-benefits">
                            <?php esc_html_e("Benefits", "tourfic"); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(!empty($includes) || !empty($excludes)){ ?>
                    <li data-menu="<?php echo esc_attr('tf-inc-exc'); ?>">
                        <a class="tf-hashlink" href="#tf-inc-exc">
                            <?php esc_html_e("Include/Excluce", "tourfic"); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(!empty($address)){ ?>
                    <li data-menu="<?php echo esc_attr('tf-location'); ?>">
                        <a class="tf-hashlink" href="#tf-location">
                            <?php esc_html_e("Location", "tourfic"); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <li data-menu="<?php echo esc_attr('tf-reviews'); ?>">
                        <a class="tf-hashlink" href="#tf-reviews">
                            <?php esc_html_e("Reviews", "tourfic"); ?>
                        </a>
                    </li>
                    <?php if(!empty($faqs)){ ?>
                    <li data-menu="<?php echo esc_attr('tf-faq'); ?>">
                        <a class="tf-hashlink" href="#tf-faq">
                            <?php esc_html_e("FAQ's", "tourfic"); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(!empty($tc)){ ?>
                    <li data-menu="<?php echo esc_attr('tf-tc'); ?>">
                        <a class="tf-hashlink" href="#tf-tc">
                            <?php esc_html_e("Terms & Conditions", "tourfic"); ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
		<?php
        
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
