<?php

namespace Tourfic\Classes\Room;

use Tourfic\Classes\Helper;
use \Tourfic\Classes\Room\Pricing;
use Tourfic\App\TF_Review;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Icons_Manager;
use Tourfic\Classes\Hotel\Hotel;

defined( 'ABSPATH' ) || exit;

class Room {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		\Tourfic\Classes\Room\Room_CPT::instance();

		add_action( 'wp_ajax_tf_remove_room_order_ids', array( $this, 'tf_remove_room_order_ids' ) );
		add_action( 'wp_ajax_tf_room_search', array( $this, 'tf_room_search_ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_room_search', array( $this, 'tf_room_search_ajax_callback' ) );
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
		$tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = $tf_booking_type = '';
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

		$meta_disable_review 			  = !empty($meta["disable-room-review"]) ? $meta["disable-room-review"] : 0;
		$tfopt_disable_review 			  = !empty(Helper::tfopt("disable-room-review")) ? Helper::tfopt("disable-room-review") : 0;
		$disable_review 				  = $tfopt_disable_review == 1 || $meta_disable_review == 1 ? true : $tfopt_disable_review;

		//elementor settings
		$show_image = isset($settings['show_image']) ? $settings['show_image'] : 'yes';
		$discount_tag = isset($settings['discount_tag']) ? $settings['discount_tag'] : 'yes';
		$show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
		$title_length = isset($settings['title_length']) ? absint($settings['title_length']) : 55;
		$show_excerpt = isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
		$excerpt_length = isset($settings['excerpt_length']) ? absint($settings['excerpt_length']) : 100;
		$show_features = isset($settings['show_features']) ? $settings['show_features'] : 'yes';
		$features_count = isset($settings['features_count']) ? absint($settings['features_count']) : 4;
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
			if ( $pricing_by == '3' && ! empty( $room_options ) ):
			?>
            <div class="tf-room-item-card tf-room-item-card-with-multiple-options">
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
					<div class="tf-room-options tf-room-options-collapsed">
						<?php 
						foreach ( $room_options as $room_option_key => $room_option ):
							$url = add_query_arg( array(
								'room-option' => $room_option_key,
							), $url);
							?>
							<div class="tf-room-option tf-room-option-item">
								<div class="tf-room-option-left">
									<h3><?php echo !empty($room_option['option_title']) ? esc_html($room_option['option_title']) : ''; ?></h3>
									<?php if ( $show_features == 'yes' && ! empty( $room_option['room-facilities'] ) ) :
										echo '<ul class="tf-room-features">';
										$option_facilities_key = 0;
										foreach ( $room_option['room-facilities'] as $room_facility ) :
											if ( $option_facilities_key < $features_count ) {
											?>
											<li>
												<span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
												<span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
											</li>
										<?php }
										$option_facilities_key++;
										endforeach;
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

						if(! empty( $room_options ) && count( $room_options ) > 2){
						echo '
						<div class="tf-room-view-more-wrap">
							<span class="tf-room-view-more">
								' . esc_html__( 'View More Pricing', 'tourfic' ) . '
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M5 12H19M12 5V19" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
						</div>';
						}
						?>
					</div>
                </div>
            </div>
			<?php else: ?>
            <div class="tf-room-item-card tf-room-item-card-single-option">
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
				</div>

                <div class="tf-room-item-card-right" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">

					<div class="tf-room-option">
						<div class="tf-room-option-left">
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

							<?php if ( $show_features == 'yes' && ! empty( $meta['features'] ) ) :
								echo '<ul class="tf-room-features">';
								$feature_key = 0;
								foreach ( $meta['features'] as $feature_id ) {
									if ( $feature_key < $features_count ) {
										$feature_meta = get_term_meta( $feature_id, 'tf_hotel_feature', true );
										$feature = get_term( $feature_id );
										$f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
										if ( $f_icon_type == 'fa' && ! empty( $feature_meta['icon-fa'] ) ) {
											$feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
										}
										if ( $f_icon_type == 'c' && ! empty( $feature_meta['icon-c'] ) ) {
											$feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
										}
										?>
										<li>
											<?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
											<?php echo esc_html($feature->name); ?>
										</li>
								<?php }
									$feature_key++;
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
                </div>
            </div>
			<?php endif; ?>
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
			$avil_by_date = !empty( $room_meta['avail_date'] ) ? json_decode($room_meta['avail_date'], true) : [];

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
				$dates  = !empty( $room_meta['avail_date'] ) ? json_decode($room_meta['avail_date'], true) : [];
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
			$avil_by_date = !empty( $room_meta['avail_date'] ) ? json_decode($room_meta['avail_date'], true) : [];

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
				$dates  = !empty( $room_meta['avail_date'] ) ? $room_meta['avail_date'] : [];

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
		$pricing_by = ! empty( $meta["pricing-by"] ) ? $meta["pricing-by"] : 1;
		$hotel_id = ! empty( $meta['tf_hotel'] ) ? $meta['tf_hotel'] : '';
		$hotel_meta = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
		
		// Single Template Style
		$tf_room_layout_conditions = ! empty( $meta['tf_single_room_layout_opt'] ) ? $meta['tf_single_room_layout_opt'] : 'global';
		if ( "single" == $tf_room_layout_conditions ) {
			$tf_room_single_template = ! empty( $meta['tf_single_room_template'] ) ? $meta['tf_single_room_template'] : 'design-1';
		}
		$tf_room_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-room'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-room'] : 'design-1';

		$tf_room_selected_check = ! empty( $tf_room_single_template ) ? $tf_room_single_template : $tf_room_global_template;
		$tf_room_selected_template = !empty($design) ? $design : $tf_room_selected_check;
		$tf_room_book_button_text = ! empty( Helper::tfopt( 'room_booking_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'room_booking_button_text' ) ) ) : esc_html__( 'Book Now', 'tourfic' );
		
		$enable_availability = ! empty( $meta['avil_by_date'] ) ? $meta['avil_by_date'] : '';
		$room_availability   = ! empty( $meta['avail_date'] ) ? $meta['avail_date'] : '';
		$num_room_available = ! empty( $meta['num-room'] ) ? (int) $meta['num-room'] : 1;
		$reduce_num_room    = ! empty( $meta['reduce_num_room'] ) ? $meta['reduce_num_room'] : false;
		$multi_by_date_ck = ! empty( $meta['price_multi_day'] ) ? ! empty( $meta['price_multi_day'] ) : false;
		$booked_dates        = self::tf_room_booked_days( $hotel_id );

		$room_disable_dates = [];
		$room_enable_dates = [];
		if ( $enable_availability === '1' && ! empty( $room_availability ) && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$room_availability_arr = json_decode( $room_availability, true );
			//iterate all the available disabled dates
			if ( ! empty( $room_availability_arr ) && is_array( $room_availability_arr ) ) {
				foreach ( $room_availability_arr as $date ) {
					if ( $date['status'] === 'unavailable' ) {
						$room_disable_dates[$date['check_in']] = $date['check_in'];
					}
					if ( $date['status'] === 'available' ) {
						$room_enable_dates[$date['check_in']] = $date['check_in'];
					}
				}
			}
		}
	
		$room_booked_per_day = [];
		if ( ! empty( $booked_dates ) && is_array( $booked_dates ) ) {
			foreach ( $booked_dates as $booking ) {

				$check_in  = strtotime( $booking['check_in'] );
				$check_out = strtotime( $booking['check_out'] );
				$rooms     = (int) $booking['room'];

				// Loop day by day (checkout excluded)
				for ( $day = $check_in; $day < $check_out; $day = strtotime('+1 day', $day) ) {

					$date = date('Y/m/d', $day);

					if ( ! isset( $room_booked_per_day[ $date ] ) ) {
						$room_booked_per_day[ $date ] = 0;
					}

					$room_booked_per_day[ $date ] += $rooms;
				}
			}
		}

		$room_available_per_day = [];
		foreach ( $room_booked_per_day as $date => $booked ) {
			$room_available_per_day[ $date ] = max( $num_room_available - $booked, 0 );

			// if rooms booked >= total rooms → disable date
			if ( $booked >= $num_room_available ) {
				unset( $room_enable_dates[ $date ] );
			}
		}

		$room_disable_dates = [];
		foreach ( $room_available_per_day as $date => $available ) {
			if ( $available <= 0 ) {
				$room_disable_dates[] = $date;
			}
		}

		$hotel_service_avail = ! empty( $hotel_meta['airport_service'] ) ? $hotel_meta['airport_service'] : '';
		$hotel_service_type  = ! empty( $hotel_meta['airport_service_type'] ) ? $hotel_meta['airport_service_type'] : '';
		$room_book_by        = ! empty( $hotel_meta['booking-by'] ) ? $hotel_meta['booking-by'] : 1;
		$room_book_url       = ! empty( $hotel_meta['booking-url'] ) ? $hotel_meta['booking-url'] : '';

		if ( $tf_room_selected_template == "design-1" ) { ?>
			<form class="tf-hotel-booking-sidebar tf-booking-form tf-room-booking-form tf-room" method="get" autocomplete="off">
				<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
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
										<span class="tf-room-guest"><?php echo !empty( $guests ) ? esc_html( $guests ) : '1'; ?></span>
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
													<path d="M4.16666 10H15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
											<input type="tel" name="adult" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1'; ?>" readonly>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 9.99996H15.8333M9.99999 4.16663V15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</div>
									</div>
								
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 10H15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
											<input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>" readonly>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 9.99996H15.8333M9.99999 4.16663V15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tf-submit-button room-submit-wrap">
						<?php $ptype = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash($_GET['type']) ) : get_post_type(); ?>
						<input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
						<input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>"/>
						<input type="hidden" name="room_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
						<input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>"/>
						<input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>"/>
						<input type="hidden" name="single_room" value="1"/>

						<?php if( $pricing_by == 3 ) :?>
							<a class="tf_btn tf_btn_full tf_btn_rounded" href="#tf-room-options"><?php echo esc_html( $tf_room_book_button_text ); ?></a>
							<button class="tf-hotel-booking-popup-btn" type="submit" style="display: none;"></button>
							<input type="hidden" name="option_id" value=""/>
						<?php else: ?>
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro()) : ?>
								<button class="tf_btn tf_btn_full tf_btn_rounded tf-submit tf-hotel-booking-popup-btn" href="javascript:;"><?php echo esc_html( $tf_room_book_button_text ); ?></button>
							<?php else: ?>
								<button class="tf_btn tf_btn_full tf_btn_rounded tf-submit hotel-room-book" type="submit"><?php echo esc_html( $tf_room_book_button_text ); ?></button>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<div class="tf-room-booking-popup"></div>
					<script>
						(function ($) {
							$(document).ready(function () {

								// flatpickr locale first day of Week
								<?php Helper::tf_flatpickr_locale( "root" ); ?>

								$(document).on('click', ".tf-room-booking-box #check_out_date", function () {
									$(this).closest('.tf-single-booking-box-wrapper').find(".tf-search-input.form-control").click();
								});

								$("[name='check-in-out-date']").flatpickr({
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
									disable: [
										<?php foreach ( $room_disable_dates as $date ) : ?>
											"<?php echo esc_html( $date ); ?>",
										<?php endforeach; ?>
									],
									<?php if ( $enable_availability === '1' && ! empty( $room_enable_dates ) ) : ?>
									enable: [
										<?php foreach ( array_unique( $room_enable_dates ) as $date ) : ?>
											"<?php echo esc_js( $date ); ?>",
										<?php endforeach; ?>
									],
									<?php elseif ( $enable_availability === '1' && empty( $room_enable_dates ) ): ?>
									enable: [],
									<?php endif; ?>

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

	/**
	 * Room Search form
	 *
	 * Horizontal
	 *
	 * Called in shortcodes
	 */
	static function tf_room_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design ) {

		// Adults
		$adults = ! empty( $_GET['adults'] ) ? absint( wp_unslash( $_GET['adults'] ) ) : '';
		// children
		$child = ! empty( $_GET['children'] ) ? absint( wp_unslash( $_GET['children'] ) ) : '';
		// room
		$room = ! empty( $_GET['room'] ) ? absint( wp_unslash( $_GET['room'] ) ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( wp_unslash( $_GET['check-in-out-date'] ) ) : '';

		// date format for users output
		$room_date_format_for_users   = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		$disable_room_child_search = ! empty( Helper::tfopt( 'disable_room_child_search' ) ) ? Helper::tfopt( 'disable_room_child_search' ) : '';

		if ( ! empty( $advanced ) && $advanced == 'enabled' ) {
			$classes .= ' tf-advanced-search-enabled';
		}
		if ( ! empty( $design ) && 2 == $design ) {
			?>
            <form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2 <?php echo esc_attr( $classes ); ?>" id="tf_room_aval_check" method="get" autocomplete="off"
                  action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
                <div class="tf_hotel_searching">
                    <div class="tf_form_innerbody">
                        <div class="tf_form_fields">
                            <div class="tf_checkin_date">
                                <label class="tf_label_checkin tf_room_check_in_out_date">
                                    <span class="tf-label"><?php esc_html_e( 'Check in', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkin_dates">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
											<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
                                        </div>

                                    </div>
                                </label>

                                <input type="hidden" name="check-in-out-date" class="tf-room-check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_room_search' ) ? 'required' : ''; ?>>
                            </div>

                            <div class="tf_checkin_date tf_room_check_in_out_date">
                                <label class="tf_label_checkin">
                                    <span class="tf-label"><?php esc_html_e( 'Check Out', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkout_dates">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
											<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="tf_guest_info tf_selectperson-wrap">
                                <label class="tf_label_checkin tf_input-inner">
                                    <span class="tf-label"><?php esc_html_e( 'Guests & rooms', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_guest_calculation">
                                            <div class="tf_guest_number">
                                                <span class="guest"><?php esc_html_e( '1', 'tourfic' ); ?></span>
                                                <span class="label"><?php esc_html_e( 'Guest', 'tourfic' ); ?></span>
                                            </div>
                                            <div class="tf_guest_number">
                                                <span class="room"><?php esc_html_e( '1', 'tourfic' ); ?></span>
                                                <span class="label"><?php esc_html_e( 'Room', 'tourfic' ); ?></span>
                                            </div>
                                        </div>
                                        <div class="tf_check_arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>

                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php echo esc_html__( 'Adults', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" class="adults-style2" name="adults" id="adults" min="1" value="1" readonly>
                                                <div class="acr-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec child-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" name="children" class="childs-style2" id="children" min="0" value="0" readonly>
                                                <div class="acr-inc child-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Rooms', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" name="room" class="rooms-style2" id="room" min="1" value="1" readonly>
                                                <div class="acr-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tf_availability_checker_box">
                            <input type="hidden" name="type" value="tf_room" class="tf-post-type"/>
							<?php if ( $author ) { ?>
                                <input type="hidden" name="tf-author" value="<?php echo esc_attr( $author ); ?>" class="tf-post-type"/>
							<?php } ?>
                            <button class="tf_btn">
								<?php echo esc_html__("Check", "tourfic"); ?>
								<span><?php echo esc_html__("availability", "tourfic"); ?></span>
							</button>
                        </div>
                    </div>
                </div>

            </form>
            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr locale first day of Week
						<?php Helper::tf_flatpickr_locale( "root" ); ?>

                        $(".tf_room_check_in_out_date").on("click", function () {
                            $(".tf-room-check-in-out-date").trigger("click");
                        });
                        $(".tf-room-check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",

                            // flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            }
                        });

                        function dateSetToFields(selectedDates, instance) {
                            if (selectedDates.length === 2) {
                                const monthNames = [
                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                if (selectedDates[0]) {
                                    const startDate = selectedDates[0];
                                    $(".tf_room_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
                                    $(".tf_room_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
                                }
                                if (selectedDates[1]) {
                                    const endDate = selectedDates[1];
                                    $(".tf_room_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
                                    $(".tf_room_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
                                }
                            }
                        }

                    });
                })(jQuery);
            </script>
		<?php }elseif( !empty($design) && 3==$design ){ ?>
			<form class="tf-archive-search-box-wrapper <?php echo esc_attr( $classes ); ?>" id="tf_room_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
				<div class="tf-date-selection-form">
					<div class="tf-date-select-box tf-flex tf-flex-gap-8">

						<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
							<div class="tf-select-date">
								<div class="tf-flex tf-flex-gap-4">
									<div class="icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>
									<div class="info-select">
										<h5><?php esc_html_e("Check-in & Check-out Date", "tourfic"); ?></h5>
										<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_room_search' ) ? 'required' : ''; ?>>
									</div>
								</div>
							</div>

						</div>

						<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
							<div class="tf-select-date">
								<div class="tf-flex tf-flex-gap-4">
									<div class="icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M9.99992 10.8333C12.3011 10.8333 14.1666 8.96785 14.1666 6.66667C14.1666 4.36548 12.3011 2.5 9.99992 2.5C7.69873 2.5 5.83325 4.36548 5.83325 6.66667C5.83325 8.96785 7.69873 10.8333 9.99992 10.8333ZM9.99992 10.8333C11.768 10.8333 13.4637 11.5357 14.714 12.786C15.9642 14.0362 16.6666 15.7319 16.6666 17.5M9.99992 10.8333C8.23181 10.8333 6.53612 11.5357 5.28587 12.786C4.03563 14.0362 3.33325 15.7319 3.33325 17.5" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>
									<div class="info-select">
										<h5><?php esc_html_e("Guests & Rooms", "tourfic"); ?></h5>
										<div class="tf_selectperson-wrap">
											<div class="tf_input-inner">
												<div class="adults-text"><?php echo esc_html__( '1 Adults', 'tourfic' ); ?></div>
												<?php if ( empty( $disable_room_child_search ) ) : ?>
													<div class="person-sep"></div>
													<div class="child-text"><?php echo esc_html__( '0 Children', 'tourfic' ); ?></div>
												<?php endif; ?>
												<div class="person-sep"></div>
												<div class="room-text"><?php echo esc_html__( '1 Room', 'tourfic' ); ?></div>
											</div>

											<div class="tf_acrselection-wrap">
												<div class="tf_acrselection-inner">
													<div class="tf_acrselection">
														<div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
														<div class="acr-select">
															<div class="acr-dec">-</div>
															<input type="number" name="adults" id="adults" min="1" value="1" readonly>
															<div class="acr-inc">+</div>
														</div>
													</div>
													<?php if ( empty( $disable_room_child_search ) ) : ?>
														<div class="tf_acrselection">
															<div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
															<div class="acr-select">
																<div class="acr-dec">-</div>
																<input type="number" name="children" id="children" min="0" value="0">
																<div class="acr-inc">+</div>
															</div>
														</div>
													<?php endif; ?>
													<div class="tf_acrselection">
														<div class="acr-label"><?php esc_html_e( 'Rooms', 'tourfic' ); ?></div>
														<div class="acr-select">
															<div class="acr-dec">-</div>
															<input type="number" name="room" id="room" min="1" value="1">
															<div class="acr-inc">+</div>
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="tf-driver-location-box">
						<div class="tf-submit-button">
							<input type="hidden" name="type" value="tf_room" class="tf-post-type"/>
							<button type="submit" class="tf_btn tf-flex-align-center"><?php echo esc_html( apply_filters("tf_hotel_search_form_submit_button_text", esc_html__('Search', 'tourfic') ) ); ?> <i class="ri-search-line"></i></button>
						</div>
					</div>
				</div>
            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {
						// flatpickr First Day of Week
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

						$("#tf_room_aval_check #check-in-out-date").flatpickr({
							enableTime: false,
							mode: "range",
							dateFormat: "Y/m/d",
							altInput: true,
							altFormat: '<?php echo esc_html( $room_date_format_for_users ); ?>',
							minDate: "today",

							// flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

							onReady: function (selectedDates, dateStr, instance) {
								instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							},
							onChange: function (selectedDates, dateStr, instance) {
								instance.element.value = dateStr.replace(/[a-z]+/g, '-');
								instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
							}
						});
					});
                })(jQuery);
            </script>
        <?php } elseif (!empty($design) && 4 == $design) { ?>
            <form class="tf-archive-search-box-wrapper tf-search__form tf-shortcode-design-4 <?php echo esc_attr($classes); ?>" id="tf_room_aval_check" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <fieldset class="tf-search__form__fieldset">

                    <div class="tf-search__form__fieldset__middle">
                        <!-- Adult Person -->
                        <div class="tf-search__form__group tf_selectperson-wrap">
                            <label for="tf-search__form-adult" class="tf-search__form__label">
                                <?php echo esc_html_e('Adult Person', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field tf-mx-width">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="40" viewBox="0 0 41 40" fill="none">
                                        <path d="M20.2222 20C22.3439 20 24.3787 19.1571 25.879 17.6569C27.3793 16.1566 28.2222 14.1217 28.2222 12C28.2222 9.87827 27.3793 7.84344 25.879 6.34315C24.3787 4.84285 22.3439 4 20.2222 4C18.1004 4 16.0656 4.84285 14.5653 6.34315C13.065 7.84344 12.2222 9.87827 12.2222 12C12.2222 14.1217 13.065 16.1566 14.5653 17.6569C16.0656 19.1571 18.1004 20 20.2222 20ZM17.3659 23C11.2097 23 6.22217 27.9875 6.22217 34.1437C6.22217 35.1687 7.05342 36 8.07842 36H32.3659C33.3909 36 34.2222 35.1687 34.2222 34.1437C34.2222 27.9875 29.2347 23 23.0784 23H17.3659Z" fill="#3E64E0" />
                                    </svg>
                                </div>
                                <div class="tf-search__form__field__incdec">
                                    <input type="number" name="adults" id="adults" class="tf-search__form__field__input field--title" min="1" value="1">
                                    <span class="tf-search__form__field__incdre__inc form--span acr-inc">
										<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
											<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
											<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
                                    <span class="tf-search__form__field__incdre__dec form--span acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
										<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
										<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
									</svg>
								</span>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>

                        <!-- Children -->
                        <?php if (empty($disable_room_child_search)) : ?>
                            <div class="tf-search__form__group tf_selectperson-wrap">
                                <label for="tf-search__form-children" class="tf-search__form__label">
                                    <?php echo esc_html_e('Children', 'tourfic'); ?>
                                </label>
                                <div class="tf-search__form__field tf-mx-width">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="40" viewBox="0 0 26 40" fill="none">
                                            <path d="M7.99873 5C7.99873 3.67392 8.52552 2.40215 9.4632 1.46447C10.4009 0.526784 11.6727 0 12.9987 0C14.3248 0 15.5966 0.526784 16.5343 1.46447C17.472 2.40215 17.9987 3.67392 17.9987 5C17.9987 6.32608 17.472 7.59785 16.5343 8.53553C15.5966 9.47322 14.3248 10 12.9987 10C11.6727 10 10.4009 9.47322 9.4632 8.53553C8.52552 7.59785 7.99873 6.32608 7.99873 5ZM11.7487 30V37.5C11.7487 38.8828 10.6315 40 9.24873 40C7.86592 40 6.74873 38.8828 6.74873 37.5V22.4844L5.11592 25.0781C4.38155 26.25 2.83467 26.5938 1.67061 25.8594C0.506547 25.125 0.147172 23.5859 0.881547 22.4219L3.99873 17.4766C5.94405 14.375 9.34248 12.5 12.9987 12.5C16.655 12.5 20.0534 14.375 21.9987 17.4688L25.1159 22.4219C25.8503 23.5938 25.4987 25.1328 24.3347 25.8672C23.1706 26.6016 21.6237 26.25 20.8894 25.0859L19.2487 22.4844V37.5C19.2487 38.8828 18.1315 40 16.7487 40C15.3659 40 14.2487 38.8828 14.2487 37.5V30H11.7487Z" fill="#3E64E0" />
                                        </svg>
                                    </div>
                                    <div class="tf-search__form__field__incdec">
                                        <input type="number" name="children" id="children" class="tf-search__form__field__input field--title" min="0" value="0">
                                        <span class="tf-search__form__field__incdre__inc form--span acr-inc">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
												<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
											</svg>
										</span>
                                        <span class="tf-search__form__field__incdre__dec form--span acr-dec">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
												<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
											</svg>
										</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Divider -->
                            <div class="tf-search__form__divider"></div>
                        <?php endif; ?>
                        <!-- Rooms -->
                        <div class="tf-search__form__group tf_selectperson-wrap">
                            <label for="tf-search__form-rooms" class="tf-search__form__label">
                                <?php echo esc_html_e('Rooms', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field tf-mx-width">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="40" viewBox="0 0 41 40" fill="none">
                                        <path d="M38.99 19.9635C38.99 21.1586 38.0002 22.0947 36.8784 22.0947H34.7667L34.8129 32.7309C34.8129 32.9102 34.7997 33.0894 34.7799 33.2687V34.3443C34.7799 35.8116 33.5987 37 32.1403 37H31.0845C31.0119 37 30.9393 37 30.8668 36.9934C30.7744 37 30.682 37 30.5896 37L28.4449 36.9934H26.8612C25.4028 36.9934 24.2216 35.8049 24.2216 34.3376V32.7442V28.495C24.2216 27.3199 23.278 26.3704 22.11 26.3704H17.8867C16.7186 26.3704 15.775 27.3199 15.775 28.495V32.7442V34.3376C15.775 35.8049 14.5938 36.9934 13.1354 36.9934H11.5517H9.44663C9.34765 36.9934 9.24866 36.9867 9.14968 36.9801C9.07049 36.9867 8.9913 36.9934 8.91212 36.9934H7.85629C6.39792 36.9934 5.21672 35.8049 5.21672 34.3376V26.9016C5.21672 26.8418 5.21672 26.7754 5.22331 26.7157V22.0881H3.11166C1.92385 22.0881 1 21.1586 1 19.9568C1 19.3593 1.19797 18.8282 1.65989 18.3634L18.5729 3.53115C19.0349 3.06639 19.5628 3 20.0247 3C20.4866 3 21.0146 3.13279 21.4105 3.46475L38.2642 18.37C38.7921 18.8348 39.056 19.3659 38.99 19.9635Z" fill="#3E64E0" />
                                    </svg>
                                </div>
                                <div class="tf-search__form__field__incdec">
                                    <input type="number" name="room" id="room" class="tf-search__form__field__input field--title" min="1" value="1">
                                    <span class="tf-search__form__field__incdre__inc form--span acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
										<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
										<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
                                    <span class="tf-search__form__field__incdre__dec form--span acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
										<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
										<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
									</svg>
								</span>
                                </div>
                            </div>
                        </div>
                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>
                        <!-- Check-in -->
                        <div class="tf-search__form__group tf-checkin-group">
                            <div class="tf_check_inout_dates">
                                <label for="tf-search__form-checkin" class="tf-search__form__label">
                                    <?php echo esc_html_e('Check-In', 'tourfic'); ?>
                                </label>
                                <div class="tf-search__form__field">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                            <g clip-path="url(#clip0_2862_2140)">
                                                <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2862_2140">
                                                    <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="tf_checkin_dates tf-flex tf-flex-align-center">
                                        <span class="date field--title"><?php echo esc_html(gmdate('d')); ?></span>
                                        <div class="tf-search__form__field__mthyr">
                                            <span class="month form--span"><?php echo esc_html(gmdate('M')); ?></span>
                                            <span class="year form--span"><?php echo esc_html(gmdate('Y')); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="check-in-out-date" class="tf-check-in-out-date tf-check-inout-hidden" value="<?php echo esc_attr(gmdate('Y/m/d') . ' - ' . gmdate('Y/m/d', strtotime('+1 day'))); ?>" onkeypress="return false;" placeholder="<?php esc_attr_e('Check-in - Check-out', 'tourfic'); ?>" <?php echo Helper::tfopt('date_room_search') ? 'required' : ''; ?>>
                        </div>
                        <!-- label to -->
                        <div class="tf_checkin_to_label">
                            <?php echo esc_html_e('To', 'tourfic'); ?>
                        </div>
                        <!-- Check-out -->
                        <div class="tf-search__form__group tf_check_inout_dates tf-checkout-group">
                            <label for="tf-search__form-checkout" class="tf-search__form__label">
                                <?php echo esc_html_e('Check-Out', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                        <g clip-path="url(#clip0_2862_2140)">
                                            <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_2862_2140">
                                                <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="tf_checkout_dates tf-flex tf-flex-align-center">
                                    <span class="date field--title"><?php echo esc_html(gmdate('d', strtotime('+1 day'))); ?></span>
                                    <div class="tf-search__form__field__mthyr">
                                        <span class="month form--span"><?php echo esc_html(gmdate('M'), strtotime('+1 day')); ?></span>
                                        <span class="year form--span"><?php echo esc_html(gmdate('Y'), strtotime('+1 day')); ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tf-search__form__fieldset__right">
                        <!-- Submit Button -->
                        <input type="hidden" name="type" value="tf_room" class="tf-post-type" />
                        <button type="submit" class="tf-search__form__submit tf_btn">
                            <?php echo esc_html(apply_filters("tf_hotel_search_form_submit_button_text", 'Search')); ?>
                            <svg class="tf-search__form__submit__icon" width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                </fieldset>
            </form>

            <script>
                (function($) {
                    $(document).ready(function() {
                        // flatpickr locale first day of Week
                        <?php Helper::tf_flatpickr_locale("root"); ?>

                        $(".tf-shortcode-design-4#tf_room_aval_check .tf_check_inout_dates").on("click", function() {
                            $(".tf-shortcode-design-4#tf_room_aval_check .tf-check-in-out-date").trigger("click");
                        });

						// today + tomorrow
						const today = new Date();
						const tomorrow = new Date();
						tomorrow.setDate(today.getDate() + 1);

                        $(".tf-shortcode-design-4#tf_room_aval_check .tf-check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",
							defaultDate: [today, tomorrow],
                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function(selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                            onChange: function(selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            }
                        });

                        function dateSetToFields(selectedDates, instance) {
                            if (selectedDates.length === 2) {
                                const monthNames = [
                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                if (selectedDates[0]) {
                                    const startDate = selectedDates[0];
                                    $(".tf-shortcode-design-4#tf_room_aval_check .tf_checkin_dates span.date").html(startDate.getDate());
                                    $(".tf-shortcode-design-4#tf_room_aval_check .tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
                                    $(".tf-shortcode-design-4#tf_room_aval_check .tf_checkin_dates span.year").html(startDate.getFullYear());
                                }
                                if (selectedDates[1]) {
                                    const endDate = selectedDates[1];
                                    $(".tf-shortcode-design-4#tf_room_aval_check .tf_checkout_dates span.date").html(endDate.getDate());
                                    $(".tf-shortcode-design-4#tf_room_aval_check .tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
                                    $(".tf-shortcode-design-4#tf_room_aval_check .tf_checkout_dates span.year").html(endDate.getFullYear());
                                }
                            }
                        }
                    });
                })(jQuery);
            </script>
        <?php } elseif (!empty($design) && 5 == $design) { ?>
            <form class="tf-archive-search-box-wrapper tf-search__form tf-shortcode-design-5 <?php echo esc_attr($classes); ?>" id="tf_room_aval_check" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <?php
				$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '1';
				$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '0';
				$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '1';
				$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
				$check_in_out_arr = explode( ' - ', $check_in_out ); 
				$check_in = !empty($check_in_out_arr[0]) ? $check_in_out_arr[0] : ''; 
				$check_out = !empty($check_in_out_arr[1]) ? $check_in_out_arr[1] : ''; 
				$date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
				?>
				<div class="tf-archive-search-box-wrapper tf-flex tf-flex-space-bttn tf-flex-align-center">
					<div class="tf-select-date">
						<div class="tf-flex tf-flex-gap-4 tf-flex-direction-column">
							<label for="tf-checkin-date"><?php esc_html_e("Check in", "tourfic"); ?></label>
							<div class="info-select tf-booking-date-wrap tf-search-field tf-flex tf-flex-space-bttn tf-flex-align-center">
								<input type="text" class="tf-search-input" name="tf-check-in" id="tf-check-in" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo !empty($check_in) ? esc_html(sanitize_text_field( wp_unslash($check_in) )) : esc_attr(gmdate($date_format_for_users, strtotime('+1 day'))) ?>" readonly>
								<input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo !empty($check_in_out) ? esc_html(sanitize_text_field( wp_unslash($check_in_out) )) : esc_attr(gmdate('Y/m/d', strtotime('+1 day')) . ' - ' . gmdate('Y/m/d', strtotime('+2 day'))); ?>" style="display: none;">
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
								<input type="text" class="tf-search-input" name="tf-check-out" id="tf-check-out" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo !empty($check_out) ? esc_html(sanitize_text_field( wp_unslash($check_out) )) : esc_attr(gmdate($date_format_for_users, strtotime('+2 day'))); ?>">
								<svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
						</div>
					</div>
					<div class="tf-form-bottom-left">
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
						<div class="tf-select-guests">
							<div class="tf-flex tf-flex-gap-4 tf-flex-direction-column">
								<label for="guests">
									<?php esc_html_e("Guests", "tourfic"); ?>
								</label>
								<div class="tf_acrselection tf-search-field tf-booking-adult-child-infant">
									<div class="acr-select">
										<span class="tf-guest"><?php esc_html_e( "1", "tourfic" ); ?></span>
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
													<path d="M4.16666 10H15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
											<input type="tel" name="adults" id="adults" min="1" value="<?php echo esc_attr($adults); ?>" readonly>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 9.99996H15.8333M9.99999 4.16663V15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</div>
									</div>
								
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 10H15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
											<input type="tel" name="childrens" id="children" min="0" value="<?php echo esc_attr($child); ?>" readonly>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<path d="M4.16666 9.99996H15.8333M9.99999 4.16663V15.8333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tf-submit-button">
						<input type="hidden" name="type" value="tf_room" class="tf-post-type"/>
						<button class="tf-filter-rooms tf_btn tf_btn_rounded tf-flex-align-center"><?php esc_html_e("Check availability", "tourfic"); ?></button>
					</div>

					<script>
						(function ($) {
							$(document).ready(function () {

								// flatpickr locale first day of Week
								<?php Helper::tf_flatpickr_locale( "root" ); ?>

								$(".tf-shortcode-design-5#tf_room_aval_check #tf-check-out").on('click', function () {
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
									defaultDate: <?php echo ! empty( $check_in_out ) ? wp_json_encode( explode( '-', $check_in_out ) ) : '[' . wp_json_encode( gmdate( 'Y/m/d', strtotime( '+1 day' ) ) ) . ', ' . wp_json_encode( gmdate( 'Y/m/d', strtotime( '+2 day' ) ) ) . ']' ; ?>,
								});

								function dateSetToFields(selectedDates, instance) {
									const format = '<?php echo esc_html( $date_format_for_users ); ?>';
									if (selectedDates.length === 2) {
										if (selectedDates[0]) {
											let checkInDate = instance.formatDate(selectedDates[0], format);
											$(".tf-shortcode-design-5#tf_room_aval_check #tf-check-in").val(checkInDate);
										}

										if (selectedDates[1]) {
											let checkOutDate = instance.formatDate(selectedDates[1], format);
											$(".tf-shortcode-design-5#tf_room_aval_check #tf-check-out").val(checkOutDate);
										}
									}
								}
							});
						})(jQuery);

					</script>
				</div>
            </form>
        <?php } else { ?>
            <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_room_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
                <div class="tf_homepage-booking">

                    <div class="tf_selectperson-wrap">
                        <div class="tf_input-inner">
                        <span class="tf_person-icon tf-search-form-field-icon">
                            <i class="fas fa-user"></i>
                        </span>
                            <div class="adults-text"><?php echo esc_html__( '1 Adults', 'tourfic' ); ?></div>
							<?php if ( empty( $disable_room_child_search ) ) : ?>
                                <div class="person-sep"></div>
                                <div class="child-text"><?php echo esc_html__( '0 Children', 'tourfic' ); ?></div>
							<?php endif; ?>
                            <div class="person-sep"></div>
                            <div class="room-text"><?php echo esc_html__( '1 Room', 'tourfic' ); ?></div>
                        </div>

                        <div class="tf_acrselection-wrap">
                            <div class="tf_acrselection-inner">
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php echo esc_html__( 'Adults', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="1" value="1" readonly>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
								<?php if ( empty( $disable_room_child_search ) ) : ?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="children" id="children" min="0" value="0">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
								<?php endif; ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( 'Rooms', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="room" id="room" min="1" value="1">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tf_selectdate-wrap">
                        <div class="tf_input-inner">
                            <div class="tf_form-row">
                                <label class="tf_label-row">
                                    <span class="tf-label"><?php esc_html_e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="far fa-calendar-alt"></i>
                                        </div>
                                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                               placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_room_search' ) ? 'required' : ''; ?>>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

					<?php if ( ! empty( $advanced ) && "enabled" == $advanced ) { ?>
                        <div class="tf_selectdate-wrap tf_more_info_selections">
                            <div class="tf_input-inner">
                                <label class="tf_label-row" style="width: 100%;">
                                    <span class="tf-label"><?php esc_html_e( 'More', 'tourfic' ); ?></span>
                                    <span style="text-decoration: none; display: block; cursor: pointer;"><?php echo esc_html( apply_filters("tf_search_form_advance_filter_label", 'Filter') ); ?>  <i class="fas fa-angle-down"></i></span>
                                </label>
                            </div>
                            <div class="tf-more-info">
                                <h3 class="tf_advance_search_form_price_filter_label"><?php esc_html_e( 'Filter Price', 'tourfic' ); ?></h3>
                                <div class="tf-filter-price-range">
                                    <div class="tf-room-filter-range"></div>
                                </div>

								<h3 class="tf_advance_search_form_feature_filter_label" style="margin-top: 20px"><?php esc_html_e( 'Room Features', 'tourfic' ); ?></h3>
								<?php
								$tf_room_feature = get_terms( array(
									'taxonomy'     => 'hotel_feature',
									'orderby'      => 'title',
									'order'        => 'ASC',
									'hide_empty'   => true,
									'hierarchical' => 0,
								) );
								if ( $tf_room_feature ) : ?>
                                    <div class="tf-hotel-features" style="overflow: hidden">
										<?php foreach ( $tf_room_feature as $term ) : ?>
                                            <div class="form-group form-check">
                                                <input type="checkbox" name="features[]" class="form-check-input" value="<?php echo esc_html( $term->slug ); ?>" id="room_<?php echo esc_html( $term->slug ); ?>">
                                                <label class="form-check-label" for="room_<?php echo esc_html( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                            </div>
										<?php endforeach; ?>
                                    </div>
								<?php endif; ?>

                                <h3 class="tf_advance_search_form_feature_filter_label" style="margin-top: 20px"><?php esc_html_e( 'Room Types', 'tourfic' ); ?></h3>
								<?php
								$tf_room_type = get_terms( array(
									'taxonomy'     => 'room_type',
									'orderby'      => 'title',
									'order'        => 'ASC',
									'hide_empty'   => true,
									'hierarchical' => 0,
								) );
								if ( $tf_room_type ) : ?>
                                    <div class="tf-hotel-features" style="overflow: hidden">
										<?php foreach ( $tf_room_type as $term ) : ?>
                                            <div class="form-group form-check">
                                                <input type="checkbox" name="room_types[]" class="form-check-input" value="<?php echo esc_html( $term->slug ); ?>" id="<?php echo esc_html( $term->slug ); ?>">
                                                <label class="form-check-label" for="<?php echo esc_html( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                            </div>
										<?php endforeach; ?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf_submit-wrap">
                        <input type="hidden" name="type" value="tf_room" class="tf-post-type"/>
						<?php
						if ( $author ) { ?>
                            <input type="hidden" name="tf-author" value="<?php echo esc_attr( $author ); ?>" class="tf-post-type"/>
						<?php } ?>
                        <button class="tf_btn tf-submit" type="submit"><?php echo esc_html(apply_filters("tf_room_search_form_submit_button_text", esc_html__('Search', 'tourfic' ))); ?></button>
                    </div>

                </div>

            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr First Day of Week
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

						const regexMap = {
                            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                        };
                        const dateRegex = regexMap['<?php echo esc_attr($room_date_format_for_users); ?>'];

                        $("#tf_room_aval_check #check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            altInput: true,
                            altFormat: '<?php echo esc_html( $room_date_format_for_users ); ?>',
                            minDate: "today",

                            // flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                    return `${date1} - ${date2}`;
                                });
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
									return `${date1} - ${date2}`;
								});
								instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
									return `${d1} - ${d2}`;
								});
                            }
                        });

                    });
                })(jQuery);
            </script>
			<?php
		}
	}

	/*
     * Room search ajax
     * @since 2.9.7
     * @author Foysal
     */
	function tf_room_search_ajax_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		if ( Helper::tfopt( 'date_room_search' ) && ( ! isset( $_POST['check-in-out-date'] ) || empty( $_POST['check-in-out-date'] ) ) ) {
			$response['message'] = esc_html__( 'Please select check in and check out date', 'tourfic' );
		}

		// Whitelist fields
		$allowed_fields = [
			'adults',
			'children',
			'room',
			'check-in-out-date',
			'features',
			'room_types',
			'type',
			'from',
			'to',
			'_nonce',
		];

		$fields = [];

		foreach ( $allowed_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				if ( is_array( $_POST[ $key ] ) ) {
					$fields[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) );
				} else {
					$fields[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
				}
			}
		}

		// Only if conditions pass
		if ( ( Helper::tfopt( 'date_room_search' ) && ! empty( $fields['check-in-out-date'] ) ) || ( ! Helper::tfopt( 'date_room_search' ) ) ) {

			$response['query_string'] = http_build_query( $fields );
			$response['status']       = 'success';
		}

		echo wp_json_encode( $response );
		wp_die();
	}

	public static function tf_room_booked_days( $post_id ) {
		$wc_orders = wc_get_orders( array(
			'post_status' => array( 'wc-completed' ),
			'limit'       => - 1,
		) );

		$booked_days = array();
		foreach ( $wc_orders as $wc_order ) {
			$order_items = $wc_order->get_items();

			foreach ( $order_items as $item_id => $item ) {
				$item_post_id = wc_get_order_item_meta( $item_id, '_post_id', true );
				if ( $item_post_id == $post_id ) {
					$check_in = wc_get_order_item_meta( $item_id, 'check_in', true );
					$check_out = wc_get_order_item_meta( $item_id, 'check_out', true );
					$room = wc_get_order_item_meta( $item_id, 'number_room_booked', true );
					
					if ( ! empty( $check_in ) && !empty($check_out) && !empty($room) ) {
						$booked_days[]     = array(
							'check_in'  => $check_in,
							'check_out' => $check_out,
							'room' => $room,
						);
					}
				}
			}
		}

		return $booked_days;
	}
}