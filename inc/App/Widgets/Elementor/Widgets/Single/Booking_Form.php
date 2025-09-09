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
use Tourfic\Classes\Tour\Pricing as tourPricing;
use \Tourfic\Classes\Car_Rental\Pricing as carPricing;
use Tourfic\Classes\Tour\Tour;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Booking Form
 */
class Booking_Form extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-booking-form';
	}

	public function get_title() {
		return esc_html__( 'Booking Form', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'booking form',
			'tourfic',
			'reservation',
			'tf'
        ];
    }

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-booking-form/before-style-controls', $this );
		$this->tf_booking_form_style_controls();
		do_action( 'tf/single-booking-form/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_booking_form_content',[
            'label' => esc_html__('Booking Form', 'tourfic'),
        ]);

        do_action( 'tf/single-booking-form/before-content/controls', $this );

        $this->add_control('booking_form_style',[
            'label' => esc_html__('Booking Form Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
                'style3' => esc_html__('Style 3', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-booking-form/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_booking_form_style_controls() {
		$this->start_controls_section( 'booking_form_style_section', [
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
				'booking_form_style' => ['style1'],
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

		if($this->post_type == 'tf_hotel'){
			$this->tf_hotel_booking_form($settings);
        } elseif($this->post_type == 'tf_tours'){
			$this->tf_tour_booking_form($settings);
        } elseif($this->post_type == 'tf_apartment'){
			$this->tf_apartment_booking_form($settings);
        } elseif($this->post_type == 'tf_carrental'){
			$this->tf_car_booking_form($settings);
        } else {
			return;
		}   
    }

	private function tf_hotel_booking_form($settings) {
        $style = !empty($settings['booking_form_style']) ? $settings['booking_form_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
		$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
		$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
		$tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
		$tf_ext_booking_code = !empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
		
		if ($style == 'style1') {
            ?>
			<div class="tf-single-booking-form__style-1 tf-single-template__one">
				<?php if(($tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1) || ($tf_booking_type == 1) || $tf_booking_type == 3) :?>
					<div class="tf-tour-booking-box tf-box">
						<?php Hotel::tf_hotel_sidebar_booking_form('', '', 'design-1'); ?>
					</div>
				<?php endif; ?>
				<?php if( !empty($tf_ext_booking_code) && $tf_ext_booking_type == 2 ) : ?>
					<div id="tf-external-booking-embaded-form" class="tf-tour-booking-box tf-box">
						<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags()); ?>
					</div>
				<?php endif; ?>
			</div>
            <?php
        } elseif ($style == 'style2') {
            ?>
			<div class="tf-single-booking-form__style-2 tf-single-template__two">
				<div id="room-availability">
					<span id="availability" class="tf-modify-search-btn">
						<?php esc_html_e( "Modify search", "tourfic" ); ?>
					</span>
					<!--Booking form start -->
					<?php if( ($tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 || $tf_booking_type == 3) : ?>
						<div class="tf-booking-form-wrapper">
							<?php Hotel::tf_hotel_sidebar_booking_form('', '', 'design-2'); ?>
						</div>
					<?php endif; ?>
					<?php if( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && !empty($tf_ext_booking_code )): ?>
						<div id="tf-external-booking-embaded-form" class="tf-booking-form-wrapper">
							<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ); ?>
						</div>
					<?php endif; ?>
					<!-- Booking form end -->

				</div>
			</div>
			<?php
        } elseif ($style == 'style3') {
            ?>
			<div class="tf-single-booking-form__style-3 tf-single-template__legacy">
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ||  $tf_booking_type == 3 ) : ?>
					<div class="tf-hero-booking">
						<?php Hotel::tf_hotel_sidebar_booking_form('', '', 'default'); ?>
					</div>
				<?php endif; ?>
				<?php if( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && !empty( $tf_ext_booking_code )) : ?>
					<div id="tf-external-booking-embaded-form" class="tf-hero-booking">
						<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ) ?>
					</div>
				<?php endif; ?>
			</div>
			<?php
        }
	}

	private function tf_tour_booking_form($settings) {
        $style = !empty($settings['booking_form_style']) ? $settings['booking_form_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_tours_opt', true);
		$avail_prices = tourPricing::instance( $this->post_id )->get_avail_price();
		$disable_adult  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
		$tf_tour_single_book_now_text = isset($meta['single_tour_booking_form_button_text']) && ! empty( $meta['single_tour_booking_form_button_text'] ) ? stripslashes( sanitize_text_field( $meta['single_tour_booking_form_button_text'] ) ) : esc_html__( "Book Now", 'tourfic' );
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
			$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
			$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
			$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
			$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
			$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
		}
		if( 2==$tf_booking_type && !empty($tf_booking_url) ){
			$external_search_info = array(
				'{adult}'    => !empty($adults) ? $adults : 1,
				'{child}'    => !empty($children) ? $children : 0,
				'{infant}'     => !empty($infant) ? $infant : 0,
				'{booking_date}' => !empty($tour_date) ? $tour_date : '',
			);
			if(!empty($tf_booking_attribute)){
				$tf_booking_query_url = str_replace(array_keys($external_search_info), array_values($external_search_info), $tf_booking_query_url);
				if( !empty($tf_booking_query_url) ){
					$tf_booking_url = $tf_booking_url.'/?'.$tf_booking_query_url;
				}
			}
		}
		
		if ($style == 'style1') {
            ?>
			<div class="tf-single-booking-form__style-1 tf-single-template__one">
				<div class="tf-tour-booking-box tf-box">
					<?php
					$hide_price = !empty( Helper::tfopt( 't-hide-start-price' ) ) ? Helper::tfopt( 't-hide-start-price' ) : '';
					if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) :
						if ( isset( $hide_price ) && $hide_price !== '1' ) : ?>
							<!-- Tourfic Pricing Head -->
							<div class="tf-booking-form-data">
								<div class="tf-booking-block">
									<div class="tf-booking-price">
									<?php
									$tour_price = [];
									$tf_pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
									$tour_single_price_settings = !empty(Helper::tfopt('tour_archive_price_minimum_settings')) ? Helper::tfopt('tour_archive_price_minimum_settings') : 'all';
									
									$min_sale_price = null;
									if( $tf_pricing_rule  && $tf_pricing_rule == 'person' ){
										if($tour_single_price_settings == 'all') {
											if(!empty($avail_prices['adult_price']) && !$disable_adult){
												$tour_price[] = $avail_prices['adult_price'];
												$min_sale_price = $avail_prices['sale_adult_price'];
											}
											if(!empty($avail_prices['child_price']) && !$disable_child){
												$tour_price[] = $avail_prices['child_price'];
												if( $avail_prices['sale_child_price'] < $min_sale_price ){
													$min_sale_price = $avail_prices['sale_child_price'];
												}
											}
										}
										if($tour_single_price_settings == "adult") {
											if(!empty($avail_prices['adult_price']) && !$disable_adult){
												$tour_price[] = $avail_prices['adult_price'];
												$min_sale_price = $avail_prices['sale_adult_price'];
											}
										}
										if($tour_single_price_settings == "child") {
											if(!empty($avail_prices['child_price']) && !$disable_adult){
												$tour_price[] = $avail_prices['child_price'];
												$min_sale_price = $avail_prices['sale_child_price'];
											}
										}
									}
									if( $tf_pricing_rule  && $tf_pricing_rule == 'group' ){
										if(!empty($avail_prices['group_price'])){
											$tour_price[] = $avail_prices['group_price'];
											$min_sale_price = $avail_prices['sale_group_price'];
										}
									}
									if( $tf_pricing_rule  && $tf_pricing_rule == 'package' ){
										if($tour_single_price_settings == 'all') {
											if(!empty($avail_prices['adult_price']) && !$disable_adult){
												$tour_price[] = $avail_prices['adult_price'];
												$min_sale_price = $avail_prices['sale_adult_price'];
											}
											if(!empty($avail_prices['child_price']) && !$disable_child){
												$tour_price[] = $avail_prices['child_price'];
												if( $avail_prices['sale_child_price'] < $min_sale_price ){
													$min_sale_price = $avail_prices['sale_child_price'];
												}
											}
										}
										if($tour_single_price_settings == "adult") {
											if(!empty($avail_prices['adult_price']) && !$disable_adult){
												$tour_price[] = $avail_prices['adult_price'];
												$min_sale_price = $avail_prices['sale_adult_price'];
											}
										}
										if($tour_single_price_settings == "child") {
											if(!empty($avail_prices['child_price']) && !$disable_adult){
												$tour_price[] = $avail_prices['child_price'];
												$min_sale_price = $avail_prices['sale_child_price'];
											}
										}
										if(!empty($avail_prices['group_price'])){
											$tour_price[] = $avail_prices['group_price'];
											if( $avail_prices['sale_group_price'] < $min_sale_price ){
												$min_sale_price = $avail_prices['sale_group_price'];
											}
										}
									}
									?>
										<p> <span><?php esc_html_e("From","tourfic"); ?></span>

										<?php
										//get the lowest price from all available room price
										$tf_tour_min_price      = !empty($tour_price) ? min( $tour_price ) : 0;
										
										if ( ! empty( $min_sale_price ) ) {
											echo wp_kses_post(wp_strip_all_tags(wc_price($tf_tour_min_price))). " " . "<span><del>" . wp_kses_post(wp_strip_all_tags(wc_price( $min_sale_price ))) . "</del></span>";
										} else {
											echo wp_kses_post(wp_strip_all_tags(wc_price($tf_tour_min_price)));
										}
										?>
										</p>
									</div>
								</div>
							</div>
						<?php endif;
					endif; ?>
					<!-- Tourfic Booking form -->
					<div class="tf-booking-form">
						<div class="tf-booking-form-inner tf-mt-24 <?php echo $tf_booking_type == 2 && $tf_hide_price !== '1' ? 'tf-mt-24' : '' ?>">
							<h3><?php echo ! empty( $meta['booking-section-title'] ) ? esc_html( $meta['booking-section-title'] ) : ''; ?></h3>
							<?php
							if( ($tf_booking_type == 2 && $tf_hide_booking_form !== '1') || $tf_booking_type == 1 || $tf_booking_type == 3) {
								echo wp_kses(Tour::tf_single_tour_booking_form( $this->post_id, 'design-1' ), Helper::tf_custom_wp_kses_allow_tags());
							}
							?>
							<?php if ($tf_booking_type == 2 && $tf_hide_booking_form == 1):?>
								<a href="<?php echo esc_url($tf_booking_url) ?>" target="_blank" class="tf_btn tf_btn_large" style="margin-top: 10px;"><?php echo esc_html($tf_tour_single_book_now_text); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
            <?php
        } elseif ($style == 'style2') {
            ?>
			<div class="tf-single-booking-form__style-2 tf-single-template__two">
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
					<div class="tf-search-date-wrapper tf-single-widgets">
						<h3 class="tf-section-title"><?php echo ! empty( $meta["booking-section-title"] ) ? esc_html( $meta["booking-section-title"] ) : ''; ?></h3>
						<?php echo wp_kses( Tour::tf_single_tour_booking_form( $this->post_id, 'design-2' ), Helper::tf_custom_wp_kses_allow_tags() ); ?>
					</div>
				<?php endif; ?>

				<!-- Tour External Booking From -->
				<?php if ( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ): ?>
					<div class="tour-external-booking-form tf-single-widgets">
						<h2 class="tf-section-title"><?php esc_html_e( "Book This Tour", "tourfic" ); ?></h2>
						<div class="tf-btn-wrap">
							<a href="<?php echo esc_url( $tf_booking_url ) ?>" target="_blank" class="tf_btn tf_btn_full tf_btn_sharp tf-tour-external-booking-button"
								style="margin-top: 10px;"><?php echo esc_html( $tf_tour_single_book_now_text ); ?></a>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php
        } elseif ($style == 'style3') {
            ?>
			<div class="tf-single-booking-form__style-3 tf-single-template__legacy">
	            <?php if( ($tf_booking_type == 2 && $tf_hide_booking_form !== '1') || $tf_booking_type == 1 || $tf_booking_type == 3) : ?>
                    <div class="tf-tours-form-wrap">
                        <?php echo wp_kses(Tour::tf_single_tour_booking_form( $this->post_id, 'default' ), Helper::tf_custom_wp_kses_allow_tags()); ?>
                    </div>
                <?php endif; ?>
			</div>
			<?php
        }
	}

	private function tf_apartment_booking_form($settings) {
        $style = !empty($settings['booking_form_style']) ? $settings['booking_form_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);
		$s_review  = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
		$disable_review_sec  = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
		$disable_review_sec  = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;
		
		/**
		 * Review query
		 */
		$args           = array(
			'post_id' => $this->post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		);
		$comments_query = new \WP_Comment_Query( $args );
		$comments       = $comments_query->comments;
		
		if ($style == 'style1') {
            ?>
			<div class="tf-single-booking-form__style-1 tf-single-template__two">
				<div class="tf-search-date-wrapper tf-single-widgets">
					<?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec, 'design-1' ); ?>
				</div>
			</div>
            <?php
        } elseif ($style == 'style2') {
            ?>
			<div class="tf-single-booking-form__style-default tf-single-template__legacy">
				<div class="apartment-booking-form">
					<?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec, 'default' ); ?>
				</div>
			</div>
			<?php
        }
	}

	private function tf_car_booking_form($settings) {
        $style = !empty($settings['booking_form_style']) ? $settings['booking_form_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_carrental_opt', true);
		// Car Deposit
		$car_allow_deposit = ! empty( $meta['allow_deposit'] ) ? $meta['allow_deposit'] : '';
		$car_deposit_type = ! empty( $meta['deposit_type'] ) ? $meta['deposit_type'] : 'none';
		$car_deposit_amount = ! empty( $meta['deposit_amount'] ) ? $meta['deposit_amount'] : '';
		// Booking
		$car_booking_by = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : '1';
		//instructions
		$car_instructions_section_status = ! empty( $meta['instructions_section'] ) ? $meta['instructions_section'] : '';
		$car_instructions_content = ! empty( $meta['instructions_content'] ) ? $meta['instructions_content'] : '';
		// Car Extras
		$car_extra_sec_title  = ! empty( $meta['car_extra_sec_title'] ) ? $meta['car_extra_sec_title'] : '';
		$car_extras = ! empty( $meta['extras'] ) ? $meta['extras'] : '';

		$tf_pickup_date = !empty($_GET['pickup_date']) ? $_GET['pickup_date'] : '';
		$tf_dropoff_date = !empty($_GET['dropoff_date']) ? $_GET['dropoff_date'] : '';


		// Pull options from settings or set fallback values
		$disable_car_time_slot = !empty(Helper::tfopt('disable-car-time-slots')) ? boolval(Helper::tfopt('disable-car-time-slots')) : false;
		$car_time_slots = !empty(Helper::tfopt('car_time_slots')) ? Helper::tfopt('car_time_slots') : '';
		$unserialize_car_time_slots = !empty($car_time_slots) ? unserialize($car_time_slots) : array();

		$time_interval = 30;
		$start_time_str = '00:00';
		$end_time_str   = '23:30';
		$default_time_str = '10:00';
		$next_current_day = date('l', strtotime('+1 day'));

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
		$default_time = date('g:i A', strtotime($default_time_str));

		// Use selected time from GET or fall back to default
		$selected_pickup_time = !empty($_GET['pickup_time']) ? esc_html($_GET['pickup_time']) : $default_time;
		$selected_dropoff_time = !empty($_GET['dropoff_time']) ? esc_html($_GET['dropoff_time']) : $default_time;
		$total_prices = carPricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $start_time_str, $end_time_str); 
		
		// if ($style == 'style1') {
		?>
		<div class="tf-single-booking-form__style-1 tf-single-template__one">
			<?php do_action("tf_car_before_single_booking_form"); ?>
			<div class="tf-car-booking-form">

				<div class="tf-price-header tf-mb-30">
					<h2><?php esc_html_e("Total:", "tourfic"); ?> 
					<?php if(!empty($total_prices['regular_price'])){ ?><del><?php echo wc_price($total_prices['regular_price']); ?></del>  <?php } ?>
					<?php echo $total_prices['sale_price'] ? wc_price($total_prices['sale_price']) : '' ?> <?php if(!empty($total_prices['type'])){ ?><small class="pricing-type">/ <?php echo esc_html($total_prices['type']); ?></small> <?php } ?></h2>
					<p><?php echo carPricing::is_taxable($meta); ?></p>
				</div>

				<?php if(function_exists( 'is_tf_pro' ) && is_tf_pro()){ ?>
				<div class="tf-extra-added-info">
					<div class="tf-extra-added-box tf-flex tf-flex-gap-16 tf-flex-direction-column">
						<h3><?php esc_html_e("Extras added", "tourfic"); ?></h3>
						<div class="tf-added-extra tf-flex tf-flex-gap-16 tf-flex-direction-column">
							
						</div>
					</div>
				</div>
				<?php } ?>


				<div class="tf-date-select-box">

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_257_3711)">
										<path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
									<defs>
										<clipPath id="clip0_257_3711">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Pick-up", "tourfic"); ?></h5>
									<input type="text" placeholder="Pick Up Location" id="tf_pickup_location" value="<?php echo !empty($_GET['pickup']) ? esc_html($_GET['pickup']) : ''; ?>" />
									<input type="hidden" id="tf_pickup_location_id" value="<?php echo !empty($_GET['pickup']) ? esc_html($_GET['pickup']) : ''; ?>" />
								</div>
							</div>
						</div>

						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_257_3711)">
										<path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
									<defs>
										<clipPath id="clip0_257_3711">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Drop-off", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Location" id="tf_dropoff_location" value="<?php echo !empty($_GET['dropoff']) ? esc_html($_GET['dropoff']) : ''; ?>" />
									<input type="hidden" id="tf_dropoff_location_id" value="<?php echo !empty($_GET['dropoff']) ? esc_html($_GET['dropoff']) : ''; ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Pick-up date", "tourfic"); ?></h5>
									<input type="text" placeholder="Pick Up Date" id="tf_pickup_date" class="tf_pickup_date" value="<?php echo !empty($_GET['pickup_date']) ? esc_html($_GET['pickup_date']) : date('Y/m/d', strtotime('+1 day')); ?>" />
								</div>
							</div>
						</div>

						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_257_3728)">
										<path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
									<defs>
										<clipPath id="clip0_257_3728">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Time", "tourfic"); ?></h5>
									<div class="selected-pickup-time">
										<div class="text">
											<?php echo esc_html($selected_pickup_time); ?>
										</div>
										<div class="icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
									</div>
									<input type="hidden" name="tf_pickup_time" class="tf_pickup_time" id="tf_pickup_time" value="<?php echo esc_attr($selected_pickup_time); ?>">
									<div class="tf-select-time">
										<ul class="time-options-list tf-pickup-time">
											<?php
												for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
													$time_label = date("g:i A", $time);
													$selected = ($selected_pickup_time === $time_label) ? 'selected' : '';
													echo '<li value="' . esc_attr($time_label) . '" ' . $selected . '>' . esc_html($time_label) . '</li>';
												}
											?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Drop-off date", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Date" id="tf_dropoff_date" class="tf_dropoff_date" value="<?php echo !empty($_GET['dropoff_date']) ? esc_html($_GET['dropoff_date']) : date('Y/m/d', strtotime('+2 day')); ?>" />
								</div>
							</div>
						</div>

						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_257_3728)">
										<path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
									<defs>
										<clipPath id="clip0_257_3728">
										<rect width="20" height="20" fill="white"/>
										</clipPath>
									</defs>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Time", "tourfic"); ?></h5>
									<div class="selected-dropoff-time">
										<div class="text">
											<?php echo esc_html($selected_dropoff_time); ?>
										</div>
										<div class="icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
									</div>
									<input type="hidden" value="<?php echo esc_attr($this->post_id); ?>" id="post_id" />
									<input type="hidden" name="tf_dropoff_time" class="tf_dropoff_time" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
									<div class="tf-select-time">
										<ul class="time-options-list tf-dropoff-time">
											<?php
												for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
													$time_label = date("g:i A", $time);
													$selected = ($selected_dropoff_time === $time_label) ? 'selected' : '';
													echo '<li value="' . esc_attr($time_label) . '" ' . $selected . '>' . esc_html($time_label) . '</li>';
												}
											?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tf-form-submit-btn">
						<div class="error-notice"></div>
						<?php 
						if($car_deposit_type=='fixed'){
							$due_amount = $car_deposit_amount;
						}
						if($car_deposit_type=='percent'){
							$due_amount = ($total_prices['sale_price'] * $car_deposit_amount)/100;
						}
						if( function_exists( 'is_tf_pro' ) && is_tf_pro() && '2'==$car_booking_by ){ ?>
							<button class="tf-flex tf-flex-align-center tf-flex-justify-center booking-process tf-final-step tf-flex-gap-8">
								<?php esc_html_e( apply_filters("tf_car_booking_form_submit_button_text", 'Continue' ), 'tourfic' ); ?>
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</button>
						<?php }else{ ?>
							<?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($car_allow_deposit) && $car_deposit_type!='none' && !empty($car_deposit_amount) ){  ?>
								<div class="tf-partial-payment-button tf-flex tf-flex-direction-column tf-flex-gap-16">
									<button class="tf-flex tf-flex-align-center tf-partial-button tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('yes'); ?>">
										<?php esc_html_e( 'Part Pay', 'tourfic' ); ?> <?php echo wc_price($due_amount); ?>
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M11.3299 10.3541L11.6835 10.0006L11.3299 9.64703L7.55867 5.87577L8.03008 5.40437L12.6263 10.0006L8.03008 14.5967L7.55867 14.1253L11.3299 10.3541Z" fill="#566676" stroke="#0866C4"/>
										</svg>
									</button>

									<button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('no'); ?>">
										<?php esc_html_e( 'Full Pay', 'tourfic' ); ?> <?php echo wc_price($total_prices['sale_price']); ?>
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</button>
								</div>
							<?php }else{ ?>
								<button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>">
									<?php esc_html_e( apply_filters("tf_car_booking_form_submit_button_text", 'Continue' ), 'tourfic' ); ?>
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</button>
							<?php } ?>
						<?php } ?>
					</div>
					<?php if($car_instructions_section_status){ ?>
						<div class="tf-instraction-btn tf-mt-16">
							<span class="tf-instraction-showing"><?php esc_html_e("Pick-up and Drop-off instructions", "tourfic"); ?></span>
							
							<div class="tf-car-instraction-popup">
								<div class="tf-instraction-popup-warp">

									<div class="tf-instraction-popup-header tf-flex tf-flex-align-center tf-flex-space-bttn">
										<h3><?php esc_html_e("Pick-up and Drop-off instructions", "tourfic"); ?></h3>
										<div class="tf-close-popup">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M15 5L5 15M5 5L15 15" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
									</div>

									<?php if(!empty($car_instructions_content)): ?>
										<div class="tf-instraction-content-wraper">
											<?php echo $car_instructions_content; ?>
										</div>    
									<?php endif; ?>

								</div>
							</div>
						</div>
					<?php } ?>

					<?php do_action( 'tf_car_cancellation', $this->post_id ); ?>
				</div>
				<div class="tf-mobile-booking-btn">
					<button><?php esc_html_e("Book Now", "tourfic"); ?></button>
				</div>
				<div class="tf-car-booking-popup">
					<div class="tf-booking-popup-warp">

						<div class="tf-booking-popup-header tf-flex tf-flex-align-center tf-flex-space-bttn">
							<h3><?php esc_html_e("Additional information", "tourfic"); ?></h3>
							<div class="tf-close-popup">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M15 5L5 15M5 5L15 15" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
						</div>

						<div class="tf-booking-content-wraper">

						</div>

					</div>
				</div>

				<div class="tf-withoutpayment-booking-confirm">
					<div class="tf-confirm-popup">
						<div class="tf-booking-times">
								<span>
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
									<path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
									<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
									</svg>
								</span>
						</div>
						<img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/thank-you.gif" alt="Thank You">
						<h2>
							<?php
							$booking_confirmation_msg = ! empty( Helper::tfopt( 'car-booking-confirmation-msg' ) ) ? Helper::tfopt( 'car-booking-confirmation-msg' ) : 'Booked Successfully';
							echo esc_html( $booking_confirmation_msg );
							?>
						</h2>
					</div>
				</div>

				<?php do_action( 'tf_car_extras', $car_extras, $this->post_id, $car_extra_sec_title ); ?>

				<?php if(!empty($car_driver_incude) && !empty($car_driverinfo_status)){ ?>
				<div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16 tf-mb-30">
					<div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
						<?php if(!empty($driver_sec_title)){ ?>   
							<h3><?php echo esc_html($driver_sec_title); ?></h3>
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
				<?php } ?>

				<?php if(!empty($car_information_section_status)){ ?>
				<div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16">
					<div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
						<?php if(!empty($owner_sec_title)){ ?>   
							<h3><?php echo esc_html($owner_sec_title); ?></h3>
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
				<?php } ?>

			</div>
			<?php do_action("tf_car_after_single_booking_form"); ?>
		</div>
		<script>
			(function ($) {
				$(document).ready(function () {
					// flatpickr locale first day of Week
					<?php Helper::tf_flatpickr_locale( "root" ); ?>

					$(".tf-single-template__one #tf_dropoff_date").on("click", function () {
						$(".tf-single-template__one #tf_pickup_date").trigger("click");
					});
					$(".tf-single-template__one #tf_pickup_date").flatpickr({
						enableTime: false,
						mode: "range",
						dateFormat: "Y/m/d",
						minDate: "today",
						// flatpickr locale
						<?php Helper::tf_flatpickr_locale(); ?>

						onReady: function (selectedDates, dateStr, instance) {
							dateSetToFields(selectedDates, instance);
						},
						onChange: function (selectedDates, dateStr, instance) {
							dateSetToFields(selectedDates, instance);
						},
						<?php if(! empty( $check_in_out )){ ?>
							defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
						<?php } ?>
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const startDay = flatpickr.formatDate(selectedDates[0], "l");
							const endDay = flatpickr.formatDate(selectedDates[1], "l");
							if (selectedDates[0]) {
								const startDate = flatpickr.formatDate(selectedDates[0], "Y/m/d");
								$(".tf-single-template__one #tf_pickup_date").val(startDate);
							}
							if (selectedDates[1]) {
								const endDate = flatpickr.formatDate(selectedDates[1], "Y/m/d");
								$(".tf-single-template__one #tf_dropoff_date").val(endDate);
							}

							$.ajax({
								url: <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ) ?>,
								type: 'POST',
								data: {
									action: 'get_car_time_slots',
									pickup_day: startDay,
									drop_day: endDay
								},
								success: function(response) {
								}
							});
						}
					}
				});
			})(jQuery);
		</script>
		<?php
        // }
	}

    /**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property, $important = false ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} " . ($important ? '!important' : '') . ";";
	}
}
