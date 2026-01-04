<?php

namespace Tourfic\Classes\Room;

use Tourfic\Classes\Helper;
use \Tourfic\Classes\Room\Pricing;
use Tourfic\App\TF_Review;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Icons_Manager;

defined( 'ABSPATH' ) || exit;

class Room {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		\Tourfic\Classes\Room\Room_CPT::instance();

		add_action( 'wp_ajax_tf_remove_room_order_ids', array( $this, 'tf_remove_room_order_ids' ) );
	}

	static function get_hotel_rooms( $hotel_id ) {
		$args = array(
			'post_type'      => 'tf_room',
			'posts_per_page' => - 1,
		);

		$rooms = get_posts( $args );

		$hotel_rooms = array();
		foreach ( $rooms as $room ) {
			$room_meta = get_post_meta( $room->ID, 'tf_room_opt', true );
			if ( ! empty( $room_meta['tf_hotel'] ) && $room_meta['tf_hotel'] == $hotel_id ) {
				$hotel_rooms[] = $room;
			}
		}

		return $hotel_rooms;

	}

	static function get_hotel_id_for_assigned_room( $room_id ) {
		$args = array(
			'post_type'      => 'tf_hotel',
			'posts_per_page' => - 1,
		);

		$hotels = get_posts( $args );

		foreach ( $hotels as $hotel ) {
			$hotel_meta = get_post_meta( $hotel->ID, 'tf_hotels_opt', true );
			if ( ! empty( $hotel_meta['tf_rooms'] ) && is_array($hotel_meta['tf_rooms']) ) {
				if(in_array($room_id, $hotel_meta['tf_rooms'])){
					return $hotel->ID;
				}
			}
		}
	}

	static function get_hotel_id_by_room_id( $room_id ) {
		$meta = get_post_meta( $room_id, 'tf_room_opt', true );
		return ! empty( $meta['tf_hotel'] ) ? $meta['tf_hotel'] : '';
	}

	static function get_room_options_count( $rooms ) {
		$total_room_option_count = 0;
		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $room ) {
				$room_meta = get_post_meta( $room->ID, 'tf_room_opt', true );
				$enable    = ! empty( $room_meta['enable'] ) ? $room_meta['enable'] : '';
				if ( $enable == '1' ) {
					$room_options            = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
					$total_room_option_count += count( $room_options );
				}
			}
		}

		return $total_room_option_count;
	}

	/**
	 * Ajax remove room order ids
	 */

	function tf_remove_room_order_ids() {
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'updates' ) ) {
			return;
		}

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		# Get post id
		$room_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
		# Get hotel meta
		$meta = get_post_meta( $room_id, 'tf_room_opt', true );

		# Set order id field's value to blank
		$meta['order_id'] = '';
		# Update whole hotel meta
		update_post_meta( $room_id, 'tf_room_opt', $meta );

		# Send success message
		wp_send_json_success( array(
			'message' => esc_html__( "Order ids removed successfully!", "tourfic" ),
		) );

		wp_die();
	}

	/**
	 * Room Archive Single Item Layout
	 */
	static function tf_room_archive_single_item( $adults = '', $child = '', $room = '', $check_in_out = '', $startprice = '', $endprice = '', $settings = [] ) {

		$post_id = get_the_ID();
		$meta     = get_post_meta( $post_id, 'tf_room_opt', true );

		$hotel_id = !empty($meta['tf_hotel']) ? $meta['tf_hotel']: '';
		$hotel_meta = !empty($hotel_id) ? get_post_meta( $hotel_id, 'tf_hotels_opt', true ) : [];

		if ( empty( $adults ) ) {
			$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		}
		if ( empty( $child ) ) {
			$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		}
		if ( empty( $room ) ) {
			$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
		}
		if ( empty( $check_in_out ) ) {
			$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
		}

		if ( $check_in_out ) {
			$form_check_in      = substr( $check_in_out, 0, 10 );
			$form_check_in_stt  = strtotime( $form_check_in );
			$form_check_out     = substr( $check_in_out, 14, 10 );
			$form_check_out_stt = strtotime( $form_check_out );
		}

		if ( ! empty( $check_in_out ) ) {
			list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
		}

		if ( ! empty( $check_in_out ) ) {
			$period = new \DatePeriod(
				new \DateTime( $tf_form_start ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
			);
		} else {
			$period = '';
		}

		// Single link
		$url = get_the_permalink();
		$url = add_query_arg( array(
			'adults'            => $adults,
			'children'          => $child,
			'room'              => $room,
			'check-in-out-date' => $check_in_out
		), $url );

		$design = !empty($settings['design_room']) ? $settings['design_room'] : '';
		$tf_room_arc_selected_template = !empty($design) ? $design : (! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['room-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['room-archive'] : 'design-1');

		$tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_ext_booking_code = '';
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_booking_type      = ! empty( $hotel_meta['booking-by'] ) ? $hotel_meta['booking-by'] : 1;
			$tf_booking_url       = ! empty( $hotel_meta['booking-url'] ) ? esc_url( $hotel_meta['booking-url'] ) : '';
			$tf_booking_query_url = ! empty( $hotel_meta['booking-query'] ) ? $hotel_meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
			$tf_booking_attribute = ! empty( $hotel_meta['booking-attribute'] ) ? $hotel_meta['booking-attribute'] : '';
			$tf_hide_price        = ! empty( $hotel_meta['hide_price'] ) ? $hotel_meta['hide_price'] : '';
		}
		if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
			$external_search_info = array(
				'{adult}'    => ! empty( $adult ) ? $adult : 1,
				'{child}'    => ! empty( $child ) ? $child : 0,
				'{checkin}'  => ! empty( $check_in ) ? $check_in : gmdate( 'Y-m-d' ),
				'{checkout}' => ! empty( $check_out ) ? $check_out : gmdate( 'Y-m-d', strtotime( '+1 day' ) ),
				'{room}'     => ! empty( $room_selected ) ? $room_selected : 1,
			);
			if ( ! empty( $tf_booking_attribute ) ) {
				$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
				if ( ! empty( $tf_booking_query_url ) ) {
					$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
				}
			}
		}

		$pricing_by   = ! empty( $meta['pricing-by'] ) ? $meta['pricing-by'] : '';
		$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];
        $min_price_arr = Pricing::instance($post_id)->get_min_price($period);
		$min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
		$min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

		$meta_disable_review 			  = !empty($meta["h-review"]) ? $meta["h-review"] : 0;
		$tfopt_disable_review 			  = !empty(Helper::tfopt("h-review")) ? Helper::tfopt("h-review") : 0;
		$disable_review 				  = $tfopt_disable_review == 1 || $meta_disable_review == 1 ? true : $tfopt_disable_review;

		//elementor settings
		$show_image = isset($settings['show_image']) ? $settings['show_image'] : 'yes';
		$discount_tag = isset($settings['discount_tag']) ? $settings['discount_tag'] : 'yes';
		$show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
		$title_length = isset($settings['title_length']) ? absint($settings['title_length']) : 55;
		$show_excerpt = isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
		$excerpt_length = isset($settings['excerpt_length']) ? absint($settings['excerpt_length']) : 100;
		$show_review = isset($settings['show_review']) ? $settings['show_review'] : 'yes';
		$show_price = isset($settings['show_price']) ? $settings['show_price'] : 'yes';
		$show_view_details = isset($settings['show_view_details']) ? $settings['show_view_details'] : 'yes';
		$view_details_text = isset($settings['view_details_text']) ? sanitize_text_field($settings['view_details_text']) : esc_html__('Book Now', 'tourfic');

		// Thumbnail
		$thumbnail_html = '';
		if ( !empty($settings) && $show_image == 'yes' ) {
			$settings[ 'image_size_customize' ] = [
				'id' => get_post_thumbnail_id(),
			];
			$settings['image_size_customize_size'] = $settings['image_size'];
			$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );

			if ( "" === $thumbnail_html && 'yes' === $settings['show_fallback_img'] && !empty( $settings['fallback_img']['url'] ) ) {
				$settings[ 'image_size_customize' ] = [
					'id' => $settings['fallback_img']['id'],
				];
				$settings['image_size_customize_size'] = $settings['image_size'];
				$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );
			} elseif("" === $thumbnail_html && 'yes' !== $settings['show_fallback_img']) {
				$thumbnail_html = '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
			}
		}

		if ( $tf_room_arc_selected_template == "design-1" ) {
			?>
            <div class="tf-room-item-card">
				<div class="tf-room-item-card-left">
					<!-- Thumbnail -->
					<?php if($show_image == 'yes'): ?>
					<div class="tf-room-gallery">
						<div class="tf-room-thumb">
							<?php
							if ( ! empty( $thumbnail_html ) ) {
								echo wp_kses_post( $thumbnail_html );
							} elseif ( has_post_thumbnail() ) {
								the_post_thumbnail( 'full' );
							} else {
								echo '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
							}
							?>
						</div>

						<!-- Review -->
						<?php if( $show_review == 'yes' && $disable_review != true ): ?>
							<div class="tf-room-ratings">
								<?php TF_Review::tf_archive_single_rating('', $design); ?>
								<i class="fa-solid fa-star"></i>
							</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<div class="tf-room-left-card-content">
						<!-- Title -->
						<?php if( $show_title == 'yes' ): ?>
						<h2 class="tf-room-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), $title_length ) ); ?></a></h2>
						<?php endif; ?>

						<!-- Excerpt -->
						<?php if($show_excerpt == 'yes') : ?>
							<div class="tf-details">
								<p><?php echo wp_kses_post( substr( wp_strip_all_tags( get_the_content() ), 0, $excerpt_length ) ) . '...'; ?></p>
							</div>
						<?php endif; ?>

						<a href="<?php echo esc_url( $url ); ?>" class="tf-room-details-url"><?php esc_html_e( "Room Details", "tourfic" ); ?></a>
					</div>
				</div>

                <div class="tf-room-item-card-right" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">
					<?php if ( $pricing_by == '3' && ! empty( $room_options ) ):
						echo '<div class="tf-room-options tf-room-options-collapsed">';
							foreach ( $room_options as $room_option_key => $room_option ):
								$url = add_query_arg( array(
									'room-option' => $room_option_key,
								), $url);
								?>
                                <div class="tf-room-option tf-room-option-item">
									<div class="tf-room-option-left">
										<h3><?php echo !empty($room_option['option_title']) ? esc_html($room_option['option_title']) : ''; ?></h3>
										<?php if ( ! empty( $room_option['room-facilities'] ) ) :
											echo '<ul class="tf-room-features">';
											foreach ( $room_option['room-facilities'] as $room_facility ) :
												?>
												<li>
													<span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
													<span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
												</li>
											<?php endforeach;
											echo '</ul>';
										endif; ?>
									</div>

									<div class="tf-room-option-right">
										<div class="tf-room-price-wrap">
											<!-- Discount -->
											<?php if ( $discount_tag == 'yes' && ! empty( $min_discount_amount ) ) : ?>
												<div class="tf-room-off">
													<span>
														<?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( " Off ", "tourfic" ); ?>
													</span>
												</div>
											<?php endif; ?>

											<!-- Price -->
											<?php if($show_price == 'yes') : ?>
											<div class="tf-room-price"><?php Pricing::instance( $post_id )->get_per_price_html( $room_option_key, 'design-2' ); ?></div>
											<?php endif; ?>
										</div>

										<!-- View Details -->
										<?php if($show_view_details == 'yes') : ?>
											<a href="<?php echo esc_url( $url ); ?>" class="tf_btn tf_btn_rounded tf_btn_large tf_btn_sharp"><?php echo esc_html( $view_details_text ); ?></a>
										<?php endif; ?>
									</div>
								</div>
								<?php
							endforeach;
							echo '
							<div class="tf-room-view-more-wrap">
								<span class="tf-room-view-more">
									' . esc_html__( 'View More Pricing', 'tourfic' ) . '
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M5 12H19M12 5V19" stroke="#EE5509" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</div>';
							echo '</div>';
						else: ?>
						<div class="tf-room-option">
							<div class="tf-room-option-left">
								<?php if ( ! empty( $meta['features'] ) ) :
									echo '<ul class="tf-room-features">';
									$tf_room_fec_key = 1;

									foreach ( $meta['features'] as $feature ) {
										if ( $tf_room_fec_key < 6 ) {
											$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
											if ( ! empty( $room_f_meta ) ) {
												$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
											}
											if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
												$room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
											} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
												$room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
											}

											$room_term = get_term( $feature ); ?>
											<li>
												<?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
												<?php echo ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
											</li>
										<?php }
										$tf_room_fec_key ++;
									}
									echo '</ul>';
								endif; ?>
							</div>

							<div class="tf-room-option-right">
								<div class="tf-room-price-wrap">
									<!-- Discount -->
									<?php if ( $discount_tag == 'yes' && ! empty( $min_discount_amount ) ) : ?>
										<div class="tf-room-off">
											<span>
												<?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( " Off ", "tourfic" ); ?>
											</span>
										</div>
									<?php endif; ?>

									<!-- Price -->
									<?php if($show_price == 'yes') : ?>
									<div class="tf-room-price"><?php Pricing::instance( $post_id )->get_per_price_html( '', 'design-2' ); ?></div>
									<?php endif; ?>
								</div>

								<!-- View Details -->
								<?php if($show_view_details == 'yes') : ?>
									<a href="<?php echo esc_url( $url ); ?>" class="tf_btn tf_btn_rounded tf_btn_large tf_btn_sharp"><?php echo esc_html( $view_details_text ); ?></a>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
                </div>
            </div>
		<?php
		}
	}

	/**
	 * Filter rooms on search result page by checkin checkout dates set by backend
	 *
	 *
	 * @param \DatePeriod $period collection of dates by user input;
	 * @param array $not_found collection of rooms exists
	 * @param array $data user input for sidebar form
	 *
	 * @author foysal
	 *
	 */
	static function tf_filter_room_by_date( $period, array &$not_found, array $data = [] ): void {
		
		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $room, $check_in_out ] = $data;
		}

		$room_meta = get_post_meta( get_the_ID(), 'tf_room_opt', true );

		// Remove disabled rooms
		if(!empty($room_meta['enable']) && $room_meta['enable'] == '0'){
			$not_found[] = get_the_ID();
			return;
		}
		
		// If no room return
		if ( empty( $room_meta ) ) {
			return;
		}

		// Set initial room availability status
		$has_room = false;

		// If adult and child number validation is true proceed
		if ( ! empty( $room_meta['adult'] ) && $room_meta['adult'] >= $adults && ! empty( $room_meta['child'] ) && $room_meta['child'] >= $child && ! empty( $room_meta['num-room'] ) && $room_meta['num-room'] >= $room ) {

			// Check custom date range status of room
			$avil_by_date = !empty( $room_meta['avil_by_date'] ) ? $room_meta['avil_by_date'] : [];

			// Check if any room available without custom date range
			if ( empty( $avil_by_date ) ) {

				if ( ! empty( $startprice ) && ! empty( $endprice ) ) {

					if('2'==$room_meta['pricing-by']){
						if ( ! empty( $room_meta['adult_price'] ) ) {
							if ( $startprice <= $room_meta['adult_price'] && $room_meta['adult_price'] <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $room_meta['child_price'] ) ) {
							if ( $startprice <= $room_meta['child_price'] && $room_meta['child_price'] <= $endprice ) {
								$has_room = true;
							}
						}
					}
					if('1'==$room_meta['pricing-by']){
						if ( ! empty( $room_meta['price'] ) ) {
							if ( $startprice <= $room_meta['price'] && $room_meta['price'] <= $endprice ) {
								$has_room = true;
							}
						}
					}
					if($room_meta['pricing-by']== '3'){
						$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
						foreach ( $room_options as $room_option_key => $room_option ) {
							$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
							if ( $option_price_type == 'per_room' ) {
								$room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							} elseif ( $option_price_type == 'per_person' ) {
								$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
							}
							if ( ! empty( $room_price ) ) {
								if ( $startprice <= $room_price && $room_price <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $option_adult_price ) ) {
								if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $option_child_price ) ) {
								if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
									$has_room = true;
								}
							}
						}
					}
				}else{
					$has_room = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates  = !empty( $room_meta['avil_by_date'] ) ? $room_meta['avil_by_date'] : [];
				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];

								$options_count = $sdate['options_count'] ?? 0;
								for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
                                    if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
	                                    $tf_check_in_date_price['tf_option_room_price_' . $i]  = ! empty( $sdate[ 'tf_option_room_price_' . $i ] ) ? $sdate[ 'tf_option_room_price_' . $i ] : 0;
                                    } else if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
	                                    $tf_check_in_date_price['tf_option_adult_price_' . $i] = ! empty( $sdate[ 'tf_option_adult_price_' . $i ] ) ? $sdate[ 'tf_option_adult_price_' . $i ] : 0;
	                                    $tf_check_in_date_price['tf_option_child_price_' . $i] = ! empty( $sdate[ 'tf_option_child_price_' . $i ] ) ? $sdate[ 'tf_option_child_price_' . $i ] : 0;
                                    }
								}
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				$tf_common_dates = array_intersect( $availability_dates, $searching_period );

				//Initial matching date array
				$show_hotel = [];

				if ( count( $tf_common_dates ) === count( $searching_period ) ) {
					$show_hotel[] = 1;
				}

				// If any date range matches show hotel
				if ( ! empty( $show_hotel ) && ! in_array( 0, $show_hotel ) ) {
					if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
						$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];

						if ( ! empty( $tf_check_in_date_price['adult_price'] ) ) {
							if ( $startprice <= $tf_check_in_date_price['adult_price'] && $tf_check_in_date_price['adult_price'] <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $tf_check_in_date_price['child_price'] ) ) {
							if ( $startprice <= $tf_check_in_date_price['child_price'] && $tf_check_in_date_price['child_price'] <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $tf_check_in_date_price['price'] ) ) {
							if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
								$has_room = true;
							}
						}

						foreach ( $room_options as $room_option_key => $room_option ) {
							if ( ! empty( $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] ) ) {
								if ( $startprice <= $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] ) ) {
								if ( $startprice <= $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] ) ) {
								if ( $startprice <= $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] <= $endprice ) {
									$has_room = true;
								}
							}
						}
					} else {
						$has_room = true;
					}
				}
			}

		}

		// If adult and child number validation is true proceed
		if ( ! empty( $room_meta['adult'] ) && $room_meta['adult'] >= $adults && empty( $room_meta['child'] ) && $room_meta['child'] >= $child && ! empty( $room_meta['num-room'] ) && $room_meta['num-room'] >= $room ) {
		
			// Check custom date range status of room
			$avil_by_date = !empty( $room_meta['avil_by_date'] ) ? $room_meta['avil_by_date'] : [];

			// Check if any room available without custom date range
			if ( empty( $avil_by_date ) ) {

				if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
					
					if('2'==$room_meta['pricing-by']){
						if ( ! empty( $room_meta['adult_price'] ) ) {
							if ( $startprice <= $room_meta['adult_price'] && $room_meta['adult_price'] <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $room_meta['child_price'] ) ) {
							if ( $startprice <= $room_meta['child_price'] && $room_meta['child_price'] <= $endprice ) {
								$has_room = true;
							}
						}
					}
					if('1'==$room_meta['pricing-by']){
						if ( ! empty( $room_meta['price'] ) ) {
							if ( $startprice <= $room_meta['price'] && $room_meta['price'] <= $endprice ) {
								$has_room = true;
							}
						}
					}
					if($room_meta['pricing-by']== '3'){
						$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
						foreach ( $room_options as $room_option_key => $room_option ) {
							$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
							if ( $option_price_type == 'per_room' ) {
								$room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							} elseif ( $option_price_type == 'per_person' ) {
								$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
							}
							if ( ! empty( $room_price ) ) {
								if ( $startprice <= $room_price && $room_price <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $option_adult_price ) ) {
								if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $option_child_price ) ) {
								if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
									$has_room = true;
								}
							}
						}
					}
				}else{
					$has_room = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates  = !empty( $room_meta['avil_by_date'] ) ? $room_meta['avil_by_date'] : [];

				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];

								$options_count = $sdate['options_count'] ?? 0;
								for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
									if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
										$tf_check_in_date_price['tf_option_room_price_' . $i]  = ! empty( $sdate[ 'tf_option_room_price_' . $i ] ) ? $sdate[ 'tf_option_room_price_' . $i ] : 0;
									} else if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
										$tf_check_in_date_price['tf_option_adult_price_' . $i] = ! empty( $sdate[ 'tf_option_adult_price_' . $i ] ) ? $sdate[ 'tf_option_adult_price_' . $i ] : 0;
										$tf_check_in_date_price['tf_option_child_price_' . $i] = ! empty( $sdate[ 'tf_option_child_price_' . $i ] ) ? $sdate[ 'tf_option_child_price_' . $i ] : 0;
									}
								}
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				$tf_common_dates = array_intersect( $availability_dates, $searching_period );

				//Initial matching date array
				$show_hotel = [];

				if ( count( $tf_common_dates ) === count( $searching_period ) ) {
					$show_hotel[] = 1;
				}

				// If any date range matches show hotel
				if ( ! in_array( 0, $show_hotel ) ) {
					if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
						
						$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];

						if ( ! empty( $tf_check_in_date_price['adult_price'] ) ) {
							if ( $startprice <= $tf_check_in_date_price['adult_price'] && $tf_check_in_date_price['adult_price'] <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $tf_check_in_date_price['child_price'] ) ) {
							if ( $startprice <= $tf_check_in_date_price['child_price'] && $tf_check_in_date_price['child_price'] <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $tf_check_in_date_price['price'] ) ) {
							if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
								$has_room = true;
							}
						}

						foreach ( $room_options as $room_option_key => $room_option ) {
							if ( ! empty( $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] ) ) {
								if ( $startprice <= $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] ) ) {
								if ( $startprice <= $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] <= $endprice ) {
									$has_room = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] ) ) {
								if ( $startprice <= $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] <= $endprice ) {
									$has_room = true;
								}
							}
						}
					} else {
						$has_room = true;
					}
				}

			}

		}

		// Conditional hotel showing
		if ( $has_room ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}

	/**
	 * Filter rooms on search result page without checkin checkout dates
	 *
	 *
	 * @param \DatePeriod $period collection of dates by user input;
	 * @param array $not_found collection of rooms exists
	 * @param array $data user input for sidebar form
	 *
	 * @author foysal
	 *
	 */
	static function tf_filter_room_without_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $room, $check_in_out ] = $data;
		}

		$room_meta = get_post_meta( get_the_ID(), 'tf_room_opt', true );

		// Remove disabled rooms
		if(!empty($room_meta['enable']) && $room_meta['enable'] == '0'){
			$not_found[] = get_the_ID();
			return;
		}
		
		// If no room return
		if ( empty( $room_meta ) ) {
			return;
		}

		// Set initial room availability status
		$has_room = false;

		// If adult and child number validation is true proceed
		if ( ! empty( $room_meta['adult'] ) && $room_meta['adult'] >= $adults && ! empty( $room_meta['child'] ) && $room_meta['child'] >= $child && ! empty( $room_meta['num-room'] ) && $room_meta['num-room'] >= $room ) {

			if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
				if ( ! empty( $room_meta['adult_price'] ) ) {
					if ( $startprice <= $room_meta['adult_price'] && $room_meta['adult_price'] <= $endprice ) {
						$has_room = true;
					}
				}
				if ( ! empty( $room_meta['child_price'] ) ) {
					if ( $startprice <= $room_meta['child_price'] && $room_meta['child_price'] <= $endprice ) {
						$has_room = true;
					}
				}
				if ( ! empty( $room_meta['price'] ) ) {
					if ( $startprice <= $room_meta['price'] && $room_meta['price'] <= $endprice ) {
						$has_room = true;
					}
				}

				if($room_meta['pricing-by']== '3'){
					$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
					foreach ( $room_options as $room_option_key => $room_option ) {
						$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
						if ( $option_price_type == 'per_room' ) {
							$room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
						} elseif ( $option_price_type == 'per_person' ) {
							$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
							$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
						}
						if ( ! empty( $room_price ) ) {
							if ( $startprice <= $room_price && $room_price <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $option_adult_price ) ) {
							if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $option_child_price ) ) {
							if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
								$has_room = true;
							}
						}
					}
				}
			} else {
				$has_room = true; // Show that hotel
			}

		}
		if ( ! empty( $room_meta['adult'] ) && $room_meta['adult'] >= $adults && empty( $room_meta['child'] ) && $room_meta['child'] >= $child && ! empty( $room_meta['num-room'] ) && $room_meta['num-room'] >= $room ) {
			if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
				
				if ( ! empty( $room_meta['adult_price'] ) ) {
					if ( $startprice <= $room_meta['adult_price'] && $room_meta['adult_price'] <= $endprice ) {
						$has_room = true;
					}
				}
				if ( ! empty( $room_meta['child_price'] ) ) {
					if ( $startprice <= $room_meta['child_price'] && $room_meta['child_price'] <= $endprice ) {
						$has_room = true;
					}
				}
				if ( ! empty( $room_meta['price'] ) ) {
					if ( $startprice <= $room_meta['price'] && $room_meta['price'] <= $endprice ) {
						$has_room = true;
					}
				}

				if($room_meta['pricing-by']== '3'){
					$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
					foreach ( $room_options as $room_option_key => $room_option ) {
						$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
						if ( $option_price_type == 'per_room' ) {
							$room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
						} elseif ( $option_price_type == 'per_person' ) {
							$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
							$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
						}
						if ( ! empty( $room_price ) ) {
							if ( $startprice <= $room_price && $room_price <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $option_adult_price ) ) {
							if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
								$has_room = true;
							}
						}
						if ( ! empty( $option_child_price ) ) {
							if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
								$has_room = true;
							}
						}
					}
				}
			} else {
				$has_room = true; // Show that hotel
			}
		}

		// Conditional hotel showing
		if ( $has_room ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}

	static function tf_room_sidebar_booking_form( $b_check_in = '', $b_check_out = '', $design = '' ) {

		$children_ages = isset( $_GET['children_ages'] ) ? sanitize_text_field($_GET['children_ages']) : '';
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		$guests = intval( $adults ) + intval( $child );
		$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '1';
		$room_option = ! empty( $_GET['room-option'] ) ? sanitize_text_field( $_GET['room-option'] ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
		$check_in_out_dates = ! empty( $check_in_out ) ? array_map( 'trim', explode( '-', $check_in_out ) ) : [];
		
		//get features
		$features = ! empty( $_GET['features'] ) ? sanitize_text_field( $_GET['features'] ) : '';

		// date format for users output
		$date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		$meta = get_post_meta( get_the_ID(), 'tf_room_opt', true );
		$unique_id = ! empty( $meta['unique_id'] ) ? $meta['unique_id'] : '';
		$hotel_id = ! empty( $meta['tf_hotel'] ) ? $meta['tf_hotel'] : '';
		
		// Single Template Style
		$tf_room_layout_conditions = ! empty( $meta['tf_single_room_layout_opt'] ) ? $meta['tf_single_room_layout_opt'] : 'global';
		if ( "single" == $tf_room_layout_conditions ) {
			$tf_room_single_template = ! empty( $meta['tf_single_room_template'] ) ? $meta['tf_single_room_template'] : 'design-1';
		}
		$tf_room_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-room'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-room'] : 'design-1';

		$tf_room_selected_check = ! empty( $tf_room_single_template ) ? $tf_room_single_template : $tf_room_global_template;
		$tf_room_selected_template = !empty($design) ? $design : $tf_room_selected_check;
		$tf_room_book_button_text = ! empty( Helper::tfopt( 'room_booking_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'room_booking_button_text' ) ) ) : esc_html__( 'Book Now', 'tourfic' );
		
		if ( $tf_room_selected_template == "design-1" ) { ?>
			<form class="tf-hotel-booking-sidebar tf-booking-form" method="get" autocomplete="off">
				<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
				<div id="tour_room_details_loader">
					<div id="tour-room-details-loader-img">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
					</div>
				</div>

				<div class="tf-single-booking-box-wrapper tf-room tf-flex tf-flex-space-bttn tf-flex-align-center">
					<div class="tf-select-date">
						<div class="tf-flex tf-flex-gap-4 tf-flex-direction-column">
							<label for="tf-checkin-date">
								<?php esc_html_e("Check in", "tourfic"); ?>
							</label>
							<div class="info-select tf-booking-date-wrap tf-search-field tf-flex tf-flex-space-bttn tf-flex-align-center">
								<input type="text" class="tf-search-input" name="check_in_date" id="check_in_date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo !empty($check_in_out_dates) && is_array($check_in_out_dates) ? esc_attr($check_in_out_dates[0]) : ''; ?>" readonly>
                                <input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo !empty($check_in_out_dates) && is_array($check_in_out_dates) ? esc_attr($check_in_out_dates[0] . ' - ' . $check_in_out_dates[1]) : ''; ?>" style="display: none;">
								<svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
						</div>
					</div>
					<div class="tf-select-date">
						<div class="tf-flex tf-flex-gap-4 tf-flex-direction-column">
							<label for="tf-checkout-date">
								<?php esc_html_e("Check out", "tourfic"); ?>
							</label>
							<div class="info-select tf-booking-date-wrap tf-search-field tf-flex tf-flex-space-bttn tf-flex-align-center">
								<input type="text" class="tf-search-input" name="check_out_date" id="check_out_date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo !empty($check_in_out_dates) && is_array($check_in_out_dates) ? esc_attr($check_in_out_dates[1]) : ''; ?>">
								<svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
						</div>
					</div>
					<?php if ( isset( $meta['pricing-by'] ) && $meta['pricing-by'] == '3' ) : ?>
					<div class="tf-select-date">
						<div class="tf-flex tf-flex-gap-4 tf-flex-direction-column">
							<label for="tf-checkout-date"><?php esc_html_e("Room Option", "tourfic"); ?></label>
							<select name="option_id" class="info-select tf-search-field">
								<option value=""><?php esc_html_e("Select Room Option", "tourfic"); ?></option>
								<?php
								$room_meta = get_post_meta( get_the_ID(), 'tf_room_opt', true );
								$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
								if ( ! empty( $room_options ) ) {
									foreach ( $room_options as $option_key => $option ) {
										$option_title = ! empty( $option['option_title'] ) ? $option['option_title'] : '';
										$selected = ( $room_option == $option_key ) ? 'selected' : '';
										echo '<option value="'. esc_attr($unique_id . '_' . $option_key) . '" ' . esc_attr( $selected ) . '>' . esc_html( $option_title ) . '</option>';
									}
								}
								?>
							</select>
						</div>
					</div>
					<?php endif; ?>
					<div class="tf-single-booking-box-bottom">
						<div class="tf-select-room">
							<div class="tf-flex tf-flex-gap-4 tf-flex-direction-column">
								<label for="tf-rooms-number">
									<?php esc_html_e("Rooms", "tourfic"); ?>
								</label>
							
								<div class="tf_acrselection tf-search-field">
									<div class="acr-select">
										<div class="acr-dec">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M4.16602 10H15.8327" stroke="#F8FDFD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
										<input type="tel" name="room" id="room" min="1" value="<?php echo esc_attr($room); ?>" readonly="">
										<div class="acr-inc">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M4.16699 10.0001H15.8337M10.0003 4.16675V15.8334" stroke="#F8FDFD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tf-select-guests tf-booking-form-guest-and-room">
							<div class="tf-flex tf-flex-gap-4 tf-flex-direction-column">
								<label for="guests">
									<?php esc_html_e("Guests", "tourfic"); ?>
								</label>
								<div class="tf_acrselection tf-search-field tf-booking-adult-child-infant">
									<div class="acr-select">
										<span class="tf-guest"><?php echo !empty( $guests ) ? esc_html( $guests ) : '1'; ?></span>
										<div class="tf-archive-guest-info">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 7.5L10 12.5L15 7.5" stroke="#F8FDFD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
									</div>
								</div>
							</div>
							<div class="tf_acrselection-wrap">
								<div class="tf_acrselection-inner">
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 10H15.8333" stroke="#EE5509" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
											<input type="tel" name="adult" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1'; ?>" readonly>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 9.99996H15.8333M9.99999 4.16663V15.8333" stroke="#EE5509" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</div>
									</div>
								
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 10H15.8333" stroke="#EE5509" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
											<input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>" readonly>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 9.99996H15.8333M9.99999 4.16663V15.8333" stroke="#EE5509" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tf-submit-button">
						<?php $ptype = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash($_GET['type']) ) : get_post_type(); ?>
						<input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
						<input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>"/>
						<input type="hidden" name="room_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
						<input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>"/>
						<input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>"/>
						<input type="hidden" name="single_room" value="1"/>
						<button class="tf_btn tf_btn_full tf_btn_rounded tf-submit hotel-room-book" type="submit"><?php echo esc_html( $tf_room_book_button_text ); ?></button>
					</div>

					<script>
						(function ($) {
							$(document).ready(function () {

								// flatpickr locale first day of Week
								<?php Helper::tf_flatpickr_locale( "root" ); ?>

								$(".tf-room-booking-box #check_out_date").on('click', function () {
									$(".tf-search-input.form-control").click();
								});
								$("#check-in-out-date").flatpickr({
									enableTime: false,
									mode: "range",
									dateFormat: "Y/m/d",
									minDate: "today",
									altInput: true,
									altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
									showMonths: $(window).width() >= 1240 ? 2 : 1,

									// flatpickr locale
									<?php Helper::tf_flatpickr_locale(); ?>

									onReady: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
										instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
										dateSetToFields(selectedDates, instance);
									},
									onChange: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
										instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
										dateSetToFields(selectedDates, instance);
									},
									<?php if(! empty( $check_in_out )){ ?>
									defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
									<?php } ?>
								});

								function dateSetToFields(selectedDates, instance) {
									const format = '<?php echo esc_html( $date_format_for_users ); ?>';
									if (selectedDates.length === 2) {
										if (selectedDates[0]) {
											let checkInDate = instance.formatDate(selectedDates[0], format);
											$(".tf-room-booking-box #check_in_date").val(checkInDate);
										}

										if (selectedDates[1]) {
											let checkOutDate = instance.formatDate(selectedDates[1], format);
											$(".tf-room-booking-box #check_out_date").val(checkOutDate);
										}
									}
								}
							});
						})(jQuery);

					</script>
				</div>
			</form>
		<?php }
	}

	public static function tf_room_feature_count( $term_id ) {
		global $wpdb;

		$term_id = absint( $term_id );
		if ( ! $term_id ) {
			return 0;
		}

		// Meta value is serialized array like: a:3:{i:0;s:2:"34";...}
		$like = '%"' . $wpdb->esc_like( (string) $term_id ) . '"%';

		$sql = $wpdb->prepare(
			"
			SELECT COUNT(DISTINCT p.ID)
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm
				ON pm.post_id = p.ID
			WHERE p.post_type = %s
				AND p.post_status = %s
				AND pm.meta_key = %s
				AND pm.meta_value LIKE %s
			",
			'tf_room',
			'publish',
			'tf_search_features',
			$like
		);

		return (int) $wpdb->get_var( $sql );
	}

	static function tf_term_count( $filter, $destination, $default_count ) {

		if ( $destination == '' ) {
			return $default_count;
		}

		$term_count = array();

		$args = array(
			'post_type'      => 'tf_room',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'room_type',
					'field'    => 'slug',
					'terms'    => $destination
				)
			)
		);

		$loop = new \WP_Query( $args );

		if ( $loop->have_posts() ) :
			while ( $loop->have_posts() ) : $loop->the_post();

				if ( has_term( $filter, 'room_type', get_the_ID() ) == true ) {
					$term_count[] = 'true';
				}

			endwhile;
		endif;

		return count( $term_count );

		wp_reset_postdata();
	}
}