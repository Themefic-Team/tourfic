<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\App\TF_Review;
use Tourfic\Classes\Apartment\Apartment;
use Tourfic\Classes\Car_Rental\Pricing as carPricing;
use Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Room\Pricing as roomPricing;
use Tourfic\Classes\Room\Room;
use Tourfic\Classes\Tour\Pricing as tourPricing;
use Tourfic\Classes\Tour\Tour;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Booking Form Component
 */
class Booking_Form {

	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'tf_hotel' === $post_type ) {
			self::tf_hotel_booking_form( $post_id, $settings );
		} elseif ( 'tf_room' === $post_type ) {
			self::tf_room_booking_form( $post_id, $settings );
		} elseif ( 'tf_tours' === $post_type ) {
			self::tf_tour_booking_form( $post_id, $settings );
		} elseif ( 'tf_apartment' === $post_type ) {
			self::tf_apartment_booking_form( $post_id, $settings );
		} elseif ( 'tf_carrental' === $post_type ) {
			self::tf_car_booking_form( $post_id, $settings );
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	private static function tf_hotel_booking_form( $post_id, $settings ) {
		$style                = ! empty( $settings['booking_form_style'] ) ? $settings['booking_form_style'] : 'style1';
		$meta                 = get_post_meta( $post_id, 'tf_hotels_opt', true );
        $tf_booking_type = '1';
        $tf_hide_booking_form = $tf_ext_booking_type = $tf_ext_booking_code = '';
        if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
            $tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
            $tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
            $tf_ext_booking_code = !empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
        }
        $wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		if ( 'style1' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-hotel-booking-form__style-1 tf-single-template__one">' : ''; ?>
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
					<div class="tf-tour-booking-box tf-box">
						<?php Hotel::tf_hotel_sidebar_booking_form( '', '', 'design-1' ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $tf_ext_booking_code ) && $tf_ext_booking_type == 2 ) : ?>
					<div id="tf-external-booking-embaded-form" class="tf-tour-booking-box tf-box">
						<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ); ?>
					</div>
				<?php endif; ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style2' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-hotel-booking-form__style-2 tf-single-template__two">' : ''; ?>
				<div id="room-availability">
					<span id="availability" class="tf-modify-search-btn"><?php esc_html_e( 'Modify search', 'tourfic' ); ?></span>
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
						<div class="tf-booking-form-wrapper">
							<?php Hotel::tf_hotel_sidebar_booking_form( '', '', 'design-2' ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ) : ?>
						<div id="tf-external-booking-embaded-form" class="tf-booking-form-wrapper">
							<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style3' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-hotel-booking-form__style-3 tf-single-template__legacy">' : ''; ?>
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
					<div class="tf-hero-booking">
						<?php Hotel::tf_hotel_sidebar_booking_form( '', '', 'default' ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ) : ?>
					<div id="tf-external-booking-embaded-form" class="tf-hero-booking">
						<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ); ?>
					</div>
				<?php endif; ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	private static function tf_room_booking_form( $post_id, $settings ) {
        $meta = get_post_meta( get_the_ID(), 'tf_room_opt', true );
        $pricing_by = ! empty( $meta["pricing-by"] ) ? $meta["pricing-by"] : 1;
		$style       = ! empty( $settings['booking_form_style'] ) ? $settings['booking_form_style'] : 'style1';
		$room_option = ! empty( $_GET['room-option'] ) ? sanitize_text_field( wp_unslash( $_GET['room-option'] ) ) : '';
        $wrapper     = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		if ( 'style1' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-room-booking-form__style-1 tf-single-template__two">' : ''; ?>
                <?php if ( $pricing_by != '3' ) : ?>
				    <div class="tf-room-price"><?php roomPricing::instance( $post_id )->get_per_price_html( $room_option, 'design-2' ); ?></div>
                <?php endif; ?>
				<div class="tf-room-booking-box">
					<?php Room::tf_room_sidebar_booking_form( '', '', 'design-1' ); ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	private static function tf_tour_booking_form( $post_id, $settings ) {
		$style                          = ! empty( $settings['booking_form_style'] ) ? $settings['booking_form_style'] : 'style1';
		$meta                           = get_post_meta( $post_id, 'tf_tours_opt', true );
		$avail_prices                   = tourPricing::instance( $post_id )->get_avail_price();
		$disable_adult                  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child                  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$tf_tour_single_book_now_text  = isset( $meta['single_tour_booking_form_button_text'] ) && ! empty( $meta['single_tour_booking_form_button_text'] ) ? stripslashes( sanitize_text_field( $meta['single_tour_booking_form_button_text'] ) ) : esc_html__( 'Book Now', 'tourfic' );
		$adults                        = ! empty( $_GET['adults'] ) ? sanitize_text_field( wp_unslash( $_GET['adults'] ) ) : 1;
		$children                      = ! empty( $_GET['children'] ) ? sanitize_text_field( wp_unslash( $_GET['children'] ) ) : 0;
		$infant                        = ! empty( $_GET['infant'] ) ? sanitize_text_field( wp_unslash( $_GET['infant'] ) ) : 0;
		$tour_date                     = ! empty( $_GET['tour_date'] ) ? sanitize_text_field( wp_unslash( $_GET['tour_date'] ) ) : '';
        $wrapper                        = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		$tf_booking_type = '1';
        $tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = '';
        if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
            $tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
            $tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
            $tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
            $tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
            $tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
        }
        if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
            $external_search_info = array(
                '{adult}'        => ! empty( $adults ) ? $adults : 1,
                '{child}'        => ! empty( $children ) ? $children : 0,
                '{infant}'       => ! empty( $infant ) ? $infant : 0,
                '{booking_date}' => ! empty( $tour_date ) ? $tour_date : '',
            );
            if ( ! empty( $tf_booking_attribute ) ) {
                $tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
                if ( ! empty( $tf_booking_query_url ) ) {
                    $tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
                }
            }
        }

		if ( 'style1' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-tour-booking-form__style-1 tf-single-template__one">' : ''; ?>
				<div class="tf-tour-booking-box tf-box">
					<?php
					$hide_price = ! empty( Helper::tfopt( 't-hide-start-price' ) ) ? Helper::tfopt( 't-hide-start-price' ) : '';
					if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) :
						if ( isset( $hide_price ) && $hide_price !== '1' ) :
							?>
							<div class="tf-booking-form-data">
								<div class="tf-booking-block">
									<div class="tf-booking-price">
										<p><span><?php esc_html_e( 'From', 'tourfic' ); ?></span>
										<?php
										$tour_price                  = [];
										$tf_pricing_rule            = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
										$tour_single_price_settings = ! empty( Helper::tfopt( 'tour_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'tour_archive_price_minimum_settings' ) : 'all';
										$min_sale_price             = null;

										if ( $tf_pricing_rule && 'person' === $tf_pricing_rule ) {
											if ( 'all' === $tour_single_price_settings ) {
												if ( ! empty( $avail_prices['adult_price'] ) && ! $disable_adult ) {
													$tour_price[]     = $avail_prices['adult_price'];
													$min_sale_price = $avail_prices['sale_adult_price'];
												}
												if ( ! empty( $avail_prices['child_price'] ) && ! $disable_child ) {
													$tour_price[] = $avail_prices['child_price'];
													if ( $avail_prices['sale_child_price'] < $min_sale_price ) {
														$min_sale_price = $avail_prices['sale_child_price'];
													}
												}
											}
											if ( 'adult' === $tour_single_price_settings && ! empty( $avail_prices['adult_price'] ) && ! $disable_adult ) {
												$tour_price[]     = $avail_prices['adult_price'];
												$min_sale_price = $avail_prices['sale_adult_price'];
											}
											if ( 'child' === $tour_single_price_settings && ! empty( $avail_prices['child_price'] ) && ! $disable_adult ) {
												$tour_price[]     = $avail_prices['child_price'];
												$min_sale_price = $avail_prices['sale_child_price'];
											}
										}

										if ( $tf_pricing_rule && 'group' === $tf_pricing_rule && ! empty( $avail_prices['group_price'] ) ) {
											$tour_price[]     = $avail_prices['group_price'];
											$min_sale_price = $avail_prices['sale_group_price'];
										}

										if ( $tf_pricing_rule && 'package' === $tf_pricing_rule ) {
											if ( 'all' === $tour_single_price_settings ) {
												if ( ! empty( $avail_prices['adult_price'] ) && ! $disable_adult ) {
													$tour_price[]     = $avail_prices['adult_price'];
													$min_sale_price = $avail_prices['sale_adult_price'];
												}
												if ( ! empty( $avail_prices['child_price'] ) && ! $disable_child ) {
													$tour_price[] = $avail_prices['child_price'];
													if ( $avail_prices['sale_child_price'] < $min_sale_price ) {
														$min_sale_price = $avail_prices['sale_child_price'];
													}
												}
											}
											if ( 'adult' === $tour_single_price_settings && ! empty( $avail_prices['adult_price'] ) && ! $disable_adult ) {
												$tour_price[]     = $avail_prices['adult_price'];
												$min_sale_price = $avail_prices['sale_adult_price'];
											}
											if ( 'child' === $tour_single_price_settings && ! empty( $avail_prices['child_price'] ) && ! $disable_adult ) {
												$tour_price[]     = $avail_prices['child_price'];
												$min_sale_price = $avail_prices['sale_child_price'];
											}
											if ( ! empty( $avail_prices['group_price'] ) ) {
												$tour_price[] = $avail_prices['group_price'];
												if ( $avail_prices['sale_group_price'] < $min_sale_price ) {
													$min_sale_price = $avail_prices['sale_group_price'];
												}
											}
										}

										$tf_tour_min_price = ! empty( $tour_price ) ? min( $tour_price ) : 0;

										if ( ! empty( $min_sale_price ) ) {
											echo wp_kses_post( wp_strip_all_tags( wc_price( $tf_tour_min_price ) ) ) . ' <span><del>' . wp_kses_post( wp_strip_all_tags( wc_price( $min_sale_price ) ) ) . '</del></span>';
										} else {
											echo wp_kses_post( wp_strip_all_tags( wc_price( $tf_tour_min_price ) ) );
										}
										?>
										</p>
									</div>
								</div>
							</div>
							<?php
						endif;
					endif;
					?>
					<div class="tf-booking-form">
						<div class="tf-booking-form-inner tf-mt-24 <?php echo $tf_booking_type == 2 && $tf_hide_price !== '1' ? 'tf-mt-24' : ''; ?>">
							<h3><?php echo ! empty( $meta['booking-section-title'] ) ? esc_html( $meta['booking-section-title'] ) : ''; ?></h3>
							<?php
							if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) {
								echo wp_kses( Tour::tf_single_tour_booking_form( $post_id, 'design-1' ), Helper::tf_custom_wp_kses_allow_tags() );
							}
							?>
							<?php if ( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ) : ?>
								<a href="<?php echo esc_url( $tf_booking_url ); ?>" target="_blank" class="tf_btn tf_btn_large" style="margin-top: 10px;"><?php echo esc_html( $tf_tour_single_book_now_text ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div id="tour_room_details_loader">
					<div id="tour-room-details-loader-img">
						<img src="<?php echo esc_url( TF_ASSETS_APP_URL ); ?>images/loader.gif" alt="">
					</div>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style2' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-tour-booking-form__style-2 tf-single-template__two">' : ''; ?>
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
					<div class="tf-search-date-wrapper tf-single-widgets">
						<h3 class="tf-section-title"><?php echo ! empty( $meta['booking-section-title'] ) ? esc_html( $meta['booking-section-title'] ) : ''; ?></h3>
						<?php echo wp_kses( Tour::tf_single_tour_booking_form( $post_id, 'design-2' ), Helper::tf_custom_wp_kses_allow_tags() ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ) : ?>
					<div class="tour-external-booking-form tf-single-widgets">
						<h2 class="tf-section-title"><?php echo ! empty( $meta['booking-section-title'] ) ? esc_html( $meta['booking-section-title'] ) : ''; ?></h2>
						<div class="tf-btn-wrap">
							<a href="<?php echo esc_url( $tf_booking_url ); ?>" target="_blank" class="tf_btn tf_btn_full tf_btn_sharp tf-tour-external-booking-button" style="margin-top: 10px;"><?php echo esc_html( $tf_tour_single_book_now_text ); ?></a>
						</div>
					</div>
				<?php endif; ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style3' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-tour-booking-form__style-3 tf-single-template__legacy">' : ''; ?>
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
					<div class="tf-tours-form-wrap">
						<?php echo wp_kses( Tour::tf_single_tour_booking_form( $post_id, 'default' ), Helper::tf_custom_wp_kses_allow_tags() ); ?>
					</div>
				<?php endif; ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	private static function tf_apartment_booking_form( $post_id, $settings ) {
		$style              = ! empty( $settings['booking_form_style'] ) ? $settings['booking_form_style'] : 'style1';
		$meta               = get_post_meta( $post_id, 'tf_apartment_opt', true );
		$s_review           = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
		$disable_review_sec = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;
        $wrapper            = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		$args           = [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		];
		$comments_query = new \WP_Comment_Query( $args );
		$comments       = $comments_query->comments;

		if ( 'style1' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-apartment-booking-form__style-1 tf-single-template__two">' : ''; ?>
				<div class="tf-search-date-wrapper tf-single-widgets">
					<?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec, 'design-1' ); ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style2' === $style ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-apartment-booking-form__style-2 tf-single-template__legacy">' : ''; ?>
				<div class="apartment-booking-form">
					<?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec, 'default' ); ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	private static function tf_car_booking_form( $post_id, $settings ) {
		$meta                            = get_post_meta( $post_id, 'tf_carrental_opt', true );
		$car_allow_deposit               = ! empty( $meta['allow_deposit'] ) ? $meta['allow_deposit'] : '';
		$car_deposit_type                = ! empty( $meta['deposit_type'] ) ? $meta['deposit_type'] : 'none';
		$car_deposit_amount              = ! empty( $meta['deposit_amount'] ) ? $meta['deposit_amount'] : '';
		$car_booking_by                  = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : '1';
		$car_instructions_section_status = ! empty( $meta['instructions_section'] ) ? $meta['instructions_section'] : '';
		$car_instructions_content        = ! empty( $meta['instructions_content'] ) ? $meta['instructions_content'] : '';
		$car_extra_sec_title             = ! empty( $meta['car_extra_sec_title'] ) ? $meta['car_extra_sec_title'] : '';
		$car_extras                      = ! empty( $meta['extras'] ) ? $meta['extras'] : '';
		$tf_pickup_date                  = ! empty( $_GET['pickup_date'] ) ? sanitize_text_field( wp_unslash( $_GET['pickup_date'] ) ) : '';
		$tf_dropoff_date                 = ! empty( $_GET['dropoff_date'] ) ? sanitize_text_field( wp_unslash( $_GET['dropoff_date'] ) ) : '';
		$check_in_out                    = '';
		$car_protection_section_status   = '';
		$car_protections                 = [];

        $booking_btn_text = !empty(Helper::tfopt('car_booking_form_button_text')) ? Helper::tfopt('car_booking_form_button_text') : esc_html__('Continue', 'tourfic');
		$disable_car_time_slot    = ! empty( Helper::tfopt( 'disable-car-time-slots' ) ) ? (bool) Helper::tfopt( 'disable-car-time-slots' ) : false;
		$car_time_slots           = ! empty( Helper::tfopt( 'car_time_slots' ) ) ? Helper::tfopt( 'car_time_slots' ) : '';
		$unserialize_time_slots   = ! empty( $car_time_slots ) ? unserialize( $car_time_slots ) : [];
		$time_interval            = 30;
		$start_time_str           = '00:00';
		$end_time_str             = '23:30';
		$default_time_str         = '10:00';
		$next_current_day         = gmdate( 'l', strtotime( '+1 day' ) );

		if ( $disable_car_time_slot ) {
			$time_interval = ! empty( Helper::tfopt( 'car_time_interval' ) ) ? intval( Helper::tfopt( 'car_time_interval' ) ) : 30;
			if ( ! empty( $unserialize_time_slots ) ) {
				foreach ( $unserialize_time_slots as $slot ) {
					if ( isset( $slot['day'] ) && strtolower( $slot['day'] ) === strtolower( $next_current_day ) ) {
						$start_time_str = ! empty( $slot['pickup_time'] ) ? $slot['pickup_time'] : $start_time_str;
						$end_time_str   = ! empty( $slot['drop_time'] ) ? $slot['drop_time'] : $end_time_str;
						if ( strtotime( $start_time_str ) >= strtotime( '10:00' ) ) {
							$default_time_str = $start_time_str;
						}
						break;
					}
				}
			}
		}

		$start_time            = strtotime( $start_time_str );
		$end_time              = strtotime( $end_time_str );
		$default_time          = gmdate( 'g:i A', strtotime( $default_time_str ) );
		$selected_pickup_time  = ! empty( $_GET['pickup_time'] ) ? sanitize_text_field( wp_unslash( $_GET['pickup_time'] ) ) : $default_time;
		$selected_dropoff_time = ! empty( $_GET['dropoff_time'] ) ? sanitize_text_field( wp_unslash( $_GET['dropoff_time'] ) ) : $default_time;
		$total_prices          = carPricing::set_total_price( $meta, $tf_pickup_date, $tf_dropoff_date, $start_time_str, $end_time_str );
		$wrapper               = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
		?>
		<?php echo 'yes' === $wrapper ? '<div class="tf-single-car-booking-form__style-1 tf-single-template__one">' : ''; ?>
            <div class="tf-price-header tf-mb-30">
                <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                <?php if(!empty($total_prices['regular_price'])){ ?><del><?php echo wp_kses_post(wc_price($total_prices['regular_price'])); ?></del>  <?php } ?>
                <?php echo $total_prices['sale_price'] ? wp_kses_post(wc_price($total_prices['sale_price'])) : '' ?> <?php if( empty($tf_pickup_date) && !empty($total_prices['type'])){ ?>
                    <small class="pricing-type">/ <?php echo esc_html($total_prices['type']); ?></small> 
                    <?php } ?></h2>
                <p><?php echo wp_kses_post(carPricing::is_taxable($meta)); ?></p>
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
                                <input type="text" placeholder="<?php echo esc_attr__("Pick Up Location", "tourfic"); ?>" id="tf_pickup_location" value="<?php echo !empty($_GET['pickup']) ? esc_html(get_term_by( 'slug', sanitize_text_field( wp_unslash($_GET['pickup']) ), 'carrental_location' )->name) : ''; ?>" />
                                <input type="hidden" id="tf_pickup_location_id" value="<?php echo !empty($_GET['pickup']) ? esc_html(sanitize_text_field( wp_unslash($_GET['pickup']) )) : ''; ?>" />
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
                                <input type="text" placeholder="<?php echo esc_attr__( 'Drop Off Location', 'tourfic' ); ?>" id="tf_dropoff_location" value="<?php echo !empty($_GET['dropoff']) ? esc_html(get_term_by( 'slug', sanitize_text_field( wp_unslash($_GET['dropoff']) ), 'carrental_location' )->name) : ''; ?>" />
                                <input type="hidden" id="tf_dropoff_location_id" value="<?php echo !empty($_GET['dropoff']) ? esc_html(sanitize_text_field( wp_unslash($_GET['dropoff']) )) : ''; ?>" />
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
                                <input type="text" placeholder="<?php esc_html_e("Pick Up Date", "tourfic"); ?>" id="tf_pickup_date" class="tf_pickup_date" />
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
                                                $time_label = gmdate("g:i A", $time);
                                                $selected = ($selected_pickup_time === $time_label) ? 'selected' : '';
                                                echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
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
                                <input type="text" placeholder="Drop Off Date" id="tf_dropoff_date" class="tf_dropoff_date" />
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
                                <input type="hidden" value="<?php echo esc_attr($post_id); ?>" id="post_id" />
                                <input type="hidden" name="tf_dropoff_time" class="tf_dropoff_time" id="tf_dropoff_time" value="<?php echo esc_attr($selected_dropoff_time); ?>">
                                <div class="tf-select-time">
                                    <ul class="time-options-list tf-dropoff-time">
                                        <?php
                                            for ($time = $start_time; $time <= $end_time; $time += $time_interval * 60) {
                                                $time_label = gmdate("g:i A", $time);
                                                $selected = ($selected_dropoff_time === $time_label) ? 'selected' : '';
                                                echo '<li value="' . esc_attr($time_label) . '" ' . esc_attr($selected) . '>' . esc_html($time_label) . '</li>';
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
                        <button class="tf_btn tf-flex tf-flex-align-center tf-flex-justify-center booking-process tf-final-step tf-flex-gap-8">
                            <?php echo esc_html( apply_filters("tf_car_booking_form_submit_button_text", $booking_btn_text ), 'tourfic' ); ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    <?php }else{ ?>
                        <?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($car_allow_deposit) && $car_deposit_type!='none' && !empty($car_deposit_amount) ){  ?>
                            <div class="tf-partial-payment-button tf-flex tf-flex-direction-column tf-flex-gap-16">
                                <button class="tf_btn tf-flex tf-flex-align-center tf-partial-button tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('yes'); ?>">
                                    <?php esc_html_e( 'Part Pay', 'tourfic' ); ?> <?php echo wp_kses_post(wc_price($due_amount)); ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.3299 10.3541L11.6835 10.0006L11.3299 9.64703L7.55867 5.87577L8.03008 5.40437L12.6263 10.0006L8.03008 14.5967L7.55867 14.1253L11.3299 10.3541Z" fill="#566676" stroke="#0866C4"/>
                                    </svg>
                                </button>

                                <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('no'); ?>">
                                    <?php esc_html_e( 'Full Pay', 'tourfic' ); ?> <?php echo wp_kses_post(wc_price($total_prices['sale_price'])); ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        <?php }else{ ?>
                            <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>">
                                <?php echo esc_html( apply_filters("tf_car_booking_form_submit_button_text", esc_html__('Continue', 'tourfic') ) ); ?>
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
                                        <?php echo wp_kses_post($car_instructions_content); ?>
                                    </div>    
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php do_action( 'tf_car_cancellation', $post_id ); ?>
            </div>
            <div class="tf-mobile-booking-btn">
                <div class="tf-price-header">
                    <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                    <?php if(!empty($total_prices['regular_price'])){ ?><del><?php echo wp_kses_post(wc_price($total_prices['regular_price'])); ?></del>  <?php } ?>
                    <?php echo $total_prices['sale_price'] ? wp_kses_post(wc_price($total_prices['sale_price'])) : '' ?> <?php if(!empty($total_prices['type'])){ ?><small class="pricing-type">/ <?php echo esc_html($total_prices['type']); ?></small> <?php } ?></h2>
                    <p><?php echo wp_kses_post(carPricing::is_taxable($meta)); ?></p>
                </div>
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
                        $booking_confirmation_msg = ! empty( Helper::tfopt( 'car-booking-confirmation-msg' ) ) ? Helper::tfopt( 'car-booking-confirmation-msg' ) : esc_html__('Booked Successfully', 'tourfic');
                        echo esc_html( $booking_confirmation_msg );
                        ?>
                    </h2>
                </div>
            </div>

            <?php do_action( 'tf_car_extras', $car_extras, $post_id, $car_extra_sec_title ); ?>
		<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
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
	}
}