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

		// get post id
		$post_id = get_the_ID();
		//Get hotel_feature
		$features = ! empty( get_the_terms( $post_id, 'hotel_feature' ) ) ? get_the_terms( $post_id, 'hotel_feature' ) : '';
		$meta     = get_post_meta( $post_id, 'tf_hotels_opt', true );

		// Location
		if ( ! empty( $meta['map'] ) && Helper::tf_data_types( $meta['map'] ) ) {
			$address = ! empty( Helper::tf_data_types( $meta['map'] )['address'] ) ? Helper::tf_data_types( $meta['map'] )['address'] : '';
		}
		// Rooms
		$b_rooms = Room::get_hotel_rooms( $post_id );
		// Gallery Image
		$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
		if ( $gallery ) {
			$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
		}

		// Archive Page Minimum Price
		$archive_page_price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';

		// Featured
		$featured            = ! empty( $meta['featured'] ) ? $meta['featured'] : '';
		$hotel_multiple_tags = isset($meta['tf-hotel-tags']) && is_array($meta['tf-hotel-tags']) ? Helper::tf_data_types($meta['tf-hotel-tags']) : array();
		/**
		 * All values from URL
		 */
		// Adults
		if ( empty( $adults ) ) {
			$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		}
		// children
		if ( empty( $child ) ) {
			$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		}

		/**
		 * get children ages
		 * @since 2.8.6
		 */
		$children_ages_array = array();
		if ( isset( $_GET['children_ages'] ) && is_array( $_GET['children_ages'] ) ) {
			$children_ages_array = array_map(
				'absint', // or sanitize_text_field if values aren’t numbers
				wp_unslash( $_GET['children_ages'] )
			);
		}

		
		// room
		if ( empty( $room ) ) {
			$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
		}
		// Check-in & out date
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

        $min_price_arr = Pricing::instance($post_id)->get_min_price($period);
		$min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
		$min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

		$meta_disable_review 			  = !empty($meta["h-review"]) ? $meta["h-review"] : 0;
		$tfopt_disable_review 			  = !empty(Helper::tfopt("h-review")) ? Helper::tfopt("h-review") : 0;
		$disable_review 				  = $tfopt_disable_review == 1 || $meta_disable_review == 1 ? true : $tfopt_disable_review;

		//elementor settings
		$show_image = isset($settings['show_image']) ? $settings['show_image'] : 'yes';
		$featured_badge = isset($settings['featured_badge']) ? $settings['featured_badge'] : 'yes';
		$discount_tag = isset($settings['discount_tag']) ? $settings['discount_tag'] : 'yes';
		$promotional_tags = isset($settings['promotional_tags']) ? $settings['promotional_tags'] : 'yes';
		$gallery_switch = isset($settings['gallery']) ? $settings['gallery'] : 'yes';
		$show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
		$title_length = isset($settings['title_length']) ? absint($settings['title_length']) : 55;
		$show_excerpt = isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
		$excerpt_length = isset($settings['excerpt_length']) ? absint($settings['excerpt_length']) : 100;
		$show_location = isset($settings['show_location']) ? $settings['show_location'] : 'yes';
		$location_length = isset($settings['location_length']) ? absint($settings['location_length']) : 120;
		$show_features = isset($settings['show_features']) ? $settings['show_features'] : 'yes';
		$features_count = isset($settings['features_count']) ? absint($settings['features_count']) : 4;
		$show_review = isset($settings['show_review']) ? $settings['show_review'] : 'yes';
		$show_price = isset($settings['show_price']) ? $settings['show_price'] : 'yes';
		$show_view_details = isset($settings['show_view_details']) ? $settings['show_view_details'] : 'yes';
		$view_details_text = isset($settings['view_details_text']) ? sanitize_text_field($settings['view_details_text']) : esc_html__('View Details', 'tourfic');

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

		//Location icon
		$location_icon_html = '<i class="fa-solid fa-location-dot"></i>';
		if(!empty($settings) && $show_location == 'yes'){
			$location_icon_migrated = isset($settings['__fa4_migrated']['location_icon']);
			$location_icon_is_new = empty($settings['location_icon_comp']);

			if ( $location_icon_is_new || $location_icon_migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['location_icon'], [ 'aria-hidden' => 'true' ] );
				$location_icon_html = ob_get_clean();
			} else{
				$location_icon_html = '<i class="' . esc_attr( $settings['location_icon_comp'] ) . '"></i>';
			}
		}

		//Featured badge
		$featured_badge_text = !empty( $meta['featured_text'] ) ? esc_html( $meta['featured_text'] ) : esc_html( "HOT DEAL" );

		if ( $tf_room_arc_selected_template == "design-2" ) {
			?>
            <div class="tf-item-card tf-flex tf-item-hotel">
				<!-- Thumbnail -->
				<?php if($show_image == 'yes'): ?>
                <div class="tf-item-featured">
                    <div class="tf-tag-items">
						<div class="tf-features-box tf-flex">
							<!-- Discount -->
							<?php if ( $discount_tag == 'yes' && ! empty( $min_discount_amount ) ) { ?>
								<div class="tf-discount">
									<?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( " Off ", "tourfic" ); ?>
								</div>
							<?php } ?>

							<!-- Featured badge -->
							<?php if ( $featured_badge == 'yes' && $featured ): ?>
                                <div class="tf-feature tf-flex"><?php echo esc_html( $featured_badge_text ); ?></div>
							<?php endif; ?>
                        </div>

						<!-- Promotional Tags -->
						<?php
						if ( $promotional_tags == 'yes' && sizeof( $hotel_multiple_tags ) > 0 ) {
							foreach ( $hotel_multiple_tags as $tag ) {
								$hotel_tag_name       = ! empty( $tag['hotel-tag-title'] ) ? esc_html( $tag['hotel-tag-title'] ) : '';
								$tag_background_color = ! empty( $tag["hotel-tag-color-settings"]["background"] ) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
								$tag_font_color       = ! empty( $tag["hotel-tag-color-settings"]["font"] ) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

								if ( ! empty( $hotel_tag_name ) ) {
									echo wp_kses_post(
										'<div class="tf-multiple-tag-item" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . '">
											<span class="tf-multiple-tag">' . esc_html( $hotel_tag_name ) . '</span>
										</div>'
									);
								}
							}
						}
						?>
                    </div>
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( ! empty( $thumbnail_html ) ) {
							echo wp_kses_post( $thumbnail_html );
						} elseif ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
				<?php endif; ?>

                <div class="tf-item-details" style="<?php echo $show_image != 'yes' ? 'flex-basis: 100%;' : ''; ?>">
					<!-- Location -->
					<?php if ( $show_location == 'yes' && ! empty( $address ) ) : ?>
                        <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
							<?php echo wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                            <p><?php echo esc_html( Helper::tourfic_character_limit_callback( $address, $location_length ) ); ?></p>
                        </div>
					<?php endif; ?>

					<!-- Title -->
					<?php if( $show_title == 'yes' ): ?>
						<div class="tf-title tf-mt-16">
							<h2><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), $title_length ) ); ?></a></h2>
						</div>
					<?php endif; ?>

					<!-- Review -->
					<?php if( $show_review == 'yes' && $disable_review != true ): ?>
						<?php TF_Review::tf_archive_single_rating('', $design); ?>
					<?php endif; ?>

					<!-- Features -->
					<?php if ( $show_features == 'yes' && $features ) : ?>
                        <div class="tf-archive-features tf-mt-16">
                            <ul>
								<?php foreach ( $features as $tfkey => $feature ) {
									$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
									if ( ! empty( $feature_meta ) ) {
										$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
									}
									if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
										$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
									} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
										$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
									}
									if ( $tfkey < $features_count ) {
										?>
                                        <li class="tf-feature-lists">
											<?php
											if ( ! empty( $feature_icon ) ) {
												echo wp_kses_post( $feature_icon );
											} ?>
											<?php echo esc_html( $feature->name ); ?>
                                        </li>
									<?php }
								} ?>
								<?php
								if ( ! empty( $features ) ) {
									if ( count( $features ) > $features_count ) {
										echo '<span>More....</span>';
									}
								}
								?>
                            </ul>
                        </div>
					<?php endif; ?>

					<!-- Excerpt -->
					<?php if($show_excerpt == 'yes') : ?>
						<div class="tf-details tf-mt-16">
							<p><?php echo wp_kses_post( substr( wp_strip_all_tags( get_the_content() ), 0, $excerpt_length ) ) . '...'; ?></p>
						</div>
					<?php endif; ?>

					<!-- Price & View Details -->
                    <div class="tf-post-footer tf-flex tf-flex-align-center tf-flex-space-bttn tf-mt-16">
						<!-- Price -->
						<?php if($show_price == 'yes') : ?>
							<div class="tf-pricing">
								<?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html($period)); ?>
							</div>
						<?php endif; ?>

						<!-- View Details -->
						<?php if($show_view_details == 'yes') : ?>
							<div class="tf-booking-bttns">
								<a class="tf_btn tf_btn_lite" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $view_details_text ); ?></a>
							</div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
		<?php } elseif ( $tf_room_arc_selected_template == "design-1" ) {
			$first_gallery_image = explode( ',', $gallery );
			?>
            <div class="tf-available-room">
				<!-- Thumbnail -->
				<?php if($show_image == 'yes'): ?>
                <div class="tf-available-room-gallery">
                    <div class="tf-room-gallery">
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
					<?php if ( $gallery_switch == 'yes' && ! empty( $gallery_ids ) ) { ?>
                        <div data-id="<?php echo esc_attr( get_the_ID() ); ?>" data-type="tf_hotel" class="tf-room-gallery tf-popup-buttons tf-hotel-room-popup"
                             style="<?php echo ! empty( $first_gallery_image[0] ) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.70) 0%, rgba(48, 40, 28, 0.70) 100%), url(' . esc_url( wp_get_attachment_image_url( $first_gallery_image[0] ) ) . '), lightgray 50% / cover no-repeat; background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="content">
                                    <path id="Rectangle 2111"
                                          d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Rectangle 2109"
                                          d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4"
                                          stroke-width="1.5" stroke-linejoin="round"></path>
                                    <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </div>
					<?php } ?>
                    <div class="tf-available-labels">
						<!-- Featured badge -->
						<?php if ( $featured_badge == 'yes' && $featured ): ?>
                            <span class="tf-available-labels-featured"><?php echo esc_html( $featured_badge_text ); ?></span>
						<?php endif; ?>

						<!-- Promotional Tags -->
						<?php
						if ( $promotional_tags == 'yes' && sizeof( $hotel_multiple_tags ) > 0 ) {
							foreach ( $hotel_multiple_tags as $tag ) {
								$hotel_tag_name       = ! empty( $tag['hotel-tag-title'] ) ? esc_html( $tag['hotel-tag-title'] ) : '';
								$tag_background_color = ! empty( $tag["hotel-tag-color-settings"]["background"] ) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
								$tag_font_color       = ! empty( $tag["hotel-tag-color-settings"]["font"] ) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

								if ( ! empty( $hotel_tag_name ) ) {
									echo '<span class="tf-multiple-tag" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . '">'
										. wp_kses_post( $hotel_tag_name ) .
									'</span>';
								}
							}
						}
						?>
                    </div>

					<!-- Review -->
                    <?php if( $show_review == 'yes' && $disable_review != true ): ?>
						<div class="tf-available-ratings">
							<?php TF_Review::tf_archive_single_rating('', $design); ?>
							<i class="fa-solid fa-star"></i>
						</div>
					<?php endif; ?>
                </div>
				<?php endif; ?>
                <div class="tf-available-room-content" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">
                    <div class="tf-available-room-content-left">
                        <div class="tf-card-heading-info">
							<!-- Title & Location -->
                            <div class="tf-section-title-and-location">
								<!-- Title -->
								<?php if( $show_title == 'yes' ): ?>
                                <h2 class="tf-section-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), $title_length ) ); ?></a></h2>
								<?php endif; ?>

								<!-- Location -->
								<?php if ( $show_location == 'yes' && ! empty( $address ) ) : ?>
                                    <div class="tf-title-location">
                                        <div class="location-icon"><?php echo wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?></div>
                                        <span><?php echo esc_html( Helper::tourfic_character_limit_callback( esc_html( $address ), $location_length ) ); ?></span>
                                    </div>
								<?php endif; ?>
                            </div>

							<!-- Mobile Price -->
                            <div class="tf-mobile tf-pricing-info">
								<!-- Discount -->
								<?php if ( $discount_tag == 'yes' && ! empty( $min_discount_amount ) ) { ?>
                                    <div class="tf-available-room-off">
                                        <span>
                                            <?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( " Off ", "tourfic" ); ?>
                                        </span>
                                    </div>
								<?php } ?>

								<?php if($show_price == 'yes') : ?>
                                <div class="tf-available-room-price">
									<span class="tf-price-from">
									<?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html($period)); ?>
									</span>
                                </div>
								<?php endif; ?>
                            </div>
                        </div>

                        <!-- Features -->
						<?php if ( $show_features == 'yes' && $features ) : ?>
                            <ul class="features">
								<?php foreach ( $features as $tfkey => $feature ) {
									$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
									if ( ! empty( $feature_meta ) ) {
										$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
									}
									if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
										$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
									} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
										$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
									}
									if ( $tfkey < $features_count ) { ?>
                                        <li>
											<?php
											if ( ! empty( $feature_icon ) ) {
												echo wp_kses_post( $feature_icon );
											} ?>
											<?php echo esc_html( $feature->name ); ?>
                                        </li>
									<?php } ?>
								<?php } ?>
								<?php if ( count( $features ) > $features_count ) { ?>
                                    <li><a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( "View More", "tourfic" ); ?></a></li>
								<?php } ?>
                            </ul>
						<?php endif; ?>
                    </div>
                    <div class="tf-available-room-content-right">

                        <div class="tf-card-pricing-heading">
							<!-- Discount -->
							<?php if ( $discount_tag == 'yes' && ! empty( $min_discount_amount ) ) : ?>
                                <div class="tf-available-room-off">
                                    <span>
                                        <?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( " Off ", "tourfic" ); ?>
                                    </span>
                                </div>
							<?php endif; ?>

							<!-- Price -->
							<?php if($show_price == 'yes') : ?>
                            <div class="tf-available-room-price">
								<span class="tf-price-from"><?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html($period)); ?></span>
                            </div>
							<?php endif; ?>
                        </div>

						<!-- View Details -->
						<?php if($show_view_details == 'yes') : ?>
                        	<a href="<?php echo esc_url( $url ); ?>" class="tf_btn tf_btn_large tf_btn_sharp"><?php echo esc_html( $view_details_text ); ?></a>
						<?php endif; ?>
					</div>
                </div>
            </div>
		<?php
		}
	}
}