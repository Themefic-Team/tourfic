<?php

namespace Tourfic\Classes\Room;

use Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Pricing;
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
        $min_price_arr = Pricing::instance($hotel_id)->get_min_price($period);
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
						echo '<div class="tf-room-options">';
							foreach ( $room_options as $room_option_key => $room_option ):
								?>
                                <div class="tf-room-option">
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
											<div class="tf-room-price"><?php Pricing::instance( $hotel_id, $post_id )->get_per_price_html( $room_option_key, 'design-2' ); ?></div>
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
									<div class="tf-room-price"><?php Pricing::instance( $hotel_id, $post_id )->get_per_price_html( '', 'design-2' ); ?></div>
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
}