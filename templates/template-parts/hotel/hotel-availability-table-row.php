<?php

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Pricing;

$total_dis_dates = [];
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $room['avail_date'] ) ) {
	$avail_dates = json_decode( $room['avail_date'], true );
	//iterate all the available disabled dates
	if ( ! empty( $avail_dates ) ) {
		foreach ( $avail_dates as $date ) {
			if ( $date['status'] === 'unavailable' ) {
				$total_dis_dates[] = $date['check_in'];
			}
		}
	}
}
$tf_room_disable_date           = array_intersect( $avail_durationdate, $total_dis_dates );
$room_book_by                   = ! empty( $room['booking-by'] ) ? $room['booking-by'] : 1;
$room_book_url                  = ! empty( $room['booking-url'] ) ? $room['booking-url'] : '';
$tf_hotel_reserve_button_text   = ! empty( Helper::tfopt( 'hotel_booking_form_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'hotel_booking_form_button_text' ) ) ) : esc_html__( "Reserve Now", 'tourfic' );
$room_options                   = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

if ( $tf_hotel_selected_template_check == "design-1" ) {
	if ( empty( $tf_room_disable_date ) ) {
		?>
        <tr>
        <td class="description" rowspan="<?php echo ( $pricing_by == '3' && ! empty( $room_options ) ) ? count( $room_options ) : 1; ?>">
            <div class="tf-room-description-box tf-flex">
				<?php
				$room_preview_img       = get_the_post_thumbnail_url( $room_id, 'full' );
				$tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
				if ( $tour_room_details_gall ) {
					$tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
				}

				if ( ! empty( $room_preview_img ) ) { ?>
                    <div class="tf-room-preview-img">
						<?php
						if ( $tour_room_details_gall ) {
							?>
                            <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                               data-hotel="<?php echo esc_attr( $hotel_id ); ?>"
                               style="text-decoration: underline;">
                                <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                            </a>
						<?php } else { ?>
                            <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
						<?php } ?>
                    </div>
				<?php } ?>
                <div class="tf-features-infos" style="<?php echo ! empty( $room_preview_img ) ? 'width: 70%' : ''; ?>">
                    <div class="tf-room-type">
                        <div class="tf-room-title">
							<?php
							if ( $tour_room_details_gall ) {
								?>
                                <h3>
                                    <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                       data-hotel="<?php echo esc_attr( $hotel_id ); ?>"
                                       style="text-decoration: none;"><?php echo esc_html( get_the_title( $room_id ) ); ?></a>
                                </h3>
								<?php
							} else { ?>
                                <h3><?php echo esc_html( get_the_title( $room_id ) ); ?></h3>
							<?php } ?>
                        </div>
						<?php if ( ! empty( get_post_field( 'post_content', $room_id ) ) ): ?>
                            <div class="bed-facilities">
                                <p><?php echo wp_kses_post( substr( wp_strip_all_tags( get_post_field( 'post_content', $room_id ) ), 0, 120 ) . '...' ); ?> </p>
                            </div>
						<?php endif; ?>
                    </div>
                    <ul>
						<?php if ( $footage ) { ?>
                            <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $bed ) { ?>
                            <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Number of Beds', 'tourfic' ); ?></li>
						<?php } ?>
						<?php
						if ( ! empty( $room['features'] ) ) {
							$tf_room_fec_key = 1;
							foreach ( $room['features'] as $feature ) {
								if ( $tf_room_fec_key < 5 ) {
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
										<?php echo esc_html( $room_term->name ); ?>
                                    </li>
								<?php }
								$tf_room_fec_key ++;
							}
						} ?>
						<?php
						if ( ! empty( $room['features'] ) ) {
							if ( count( $room['features'] ) > 3 ) {
								echo '<span>More....</span>';
							}
						}
						?>
                    </ul>

					<?php
					if ( $tour_room_details_gall ) {
						?>
                        <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                           data-hotel="<?php echo esc_attr( $hotel_id ); ?>"
                           style="text-decoration: underline;">
							<?php esc_html_e( "Room Photos & Details", "tourfic" ); ?>
                        </a>
                        <div id="tour_room_details_qv" class=" <?php echo $tf_hotel_selected_template_check == "design-1" ? "tf-hotel-design-1-popup" : ""; ?>">

                        </div>
					<?php } ?>
                </div>
            </div>

        </td>
		<?php
		if ( $pricing_by == '3' && ! empty( $room_options ) ):
			$option_price = 0;
			$option_adult_price = 0;
			$option_child_price = 0;
			foreach ( $room_options as $room_option_key => $room_option ):
                $option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
				$has_option  = [];

				if ( $avil_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
					if(!$multi_by_date_ck){
						if ( $tf_startdate && $tf_enddate ) {
							// Check availability by date option
							$period = new \DatePeriod(
								new \DateTime( $tf_startdate . ' 00:00' ),
								new \DateInterval( 'P1D' ),
								new \DateTime( $tf_enddate . ' 23:59' )
							);
						}
					}else{
						if ( $tf_startdate && $tf_enddate ) {
							$period = new \DatePeriod(
								new \DateTime( $tf_startdate . ' 00:00' ),
								new \DateInterval( 'P1D' ),
								new \DateTime( $tf_enddate . ' 00:00' )
							);
						}
					}

					// split date range
					$check_in  = strtotime( $form_start . ' 00:00' );
					$check_out = strtotime( $form_end . ' 00:00' );
					$price = $price_by_date = $d_price = $d_price_by_date = 0;

					// extract price from available room options
					foreach ( $period as $date ) {

						$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
							if( $date_availability['status'] == 'available' ){
								$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
								$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

								return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
							} else {
								return false;
							}
						} ) );

						if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {

							$d_price_by_date = 0;
                            $data          = $available_rooms[0];
                            $options_count = $data['options_count'] ?? 0;

                            if($data[ 'tf_room_option_' . $room_option_key ] == ''){
	                            $has_option[] = 0;
                            }

                            if ( $data[ 'tf_room_option_' . $room_option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $room_option_key ] == 'per_room' ) {
	                            $room_price = $price_by_date = ! empty( $data[ 'tf_option_room_price_' . $room_option_key ] ) ? $data[ 'tf_option_room_price_' . $room_option_key ] : 0;

                                $d_room_price = $d_price_by_date = !empty($room_price) ? Pricing::apply_discount($room_price, $hotel_discount_type, $hotel_discount_amount) : 0;
	                            $d_price += $d_room_price;
	                            $has_option[] = 1;
                            } else if ( $data[ 'tf_room_option_' . $room_option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $room_option_key ] == 'per_person' ) {
                                $adult_price = ! empty( $data[ 'tf_option_adult_price_' . $room_option_key ] ) ? $data[ 'tf_option_adult_price_' . $room_option_key ] : 0;
                                $child_price = ! empty( $data[ 'tf_option_child_price_' . $room_option_key ] ) ? $data[ 'tf_option_child_price_' . $room_option_key ] : 0;
                                $price_by_date = ( $adult_price * $form_adult ) + ( $child_price * $form_child );

	                            $d_adult_price = !empty($adult_price) ? Pricing::apply_discount($adult_price, $hotel_discount_type, $hotel_discount_amount) : 0;
	                            $d_child_price = !empty($child_price) ? Pricing::apply_discount($child_price, $hotel_discount_type, $hotel_discount_amount) : 0;
	                            $d_price_by_date = ( $d_adult_price * $form_adult ) + ( $d_child_price * $form_child );
                                $d_price += $d_price_by_date;
	                            $has_option[] = 1;
                            }
							$price 			+= $price_by_date;
						} else {
							$has_option[] = 0;
						}
					}

				} else{
					if ( $option_price_type === 'per_room' ) {
						$option_price = $price_by_date = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
					} elseif ( $option_price_type === 'per_person' ) {
						$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
						$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;

						$price_by_date = ( ( $option_adult_price * $form_adult ) + ( $option_child_price * $form_child ) );
					}

					if ( $option_price_type == 'per_room' ) {
						$d_room_price = $d_price_by_date = Pricing::apply_discount( $option_price, $hotel_discount_type, $hotel_discount_amount );
					} elseif ( $option_price_type == 'per_person' ) {
						$d_room_adult_price = Pricing::apply_discount( $option_adult_price, $hotel_discount_type, $hotel_discount_amount );
						$d_room_child_price = Pricing::apply_discount( $option_child_price, $hotel_discount_type, $hotel_discount_amount );

						$d_price_by_date = ( ( $d_room_adult_price * $form_adult ) + ( $d_room_child_price * $form_child ) );
					}

					$price   = $price_by_date * $days;
					$d_price = $d_price_by_date * $days;
				}

                Helper::tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit, $d_price );
				if ( ! in_array( 0, $has_option ) ) {
				?>
                <td class="options">
                    <ul>
						<?php if ( ! empty( $room_option['room-facilities'] ) ) :
							foreach ( $room_option['room-facilities'] as $room_facility ) :
								?>
                                <li>
                                    <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                    <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                                </li>
							<?php endforeach;
						endif; ?>
                    </ul>
                </td>
                <td class="pax">
                    <div style="text-align:center; width: 100%;"><?php echo esc_html__( "Pax:", "tourfic" ); ?></div>
					<?php if ( $adult_number ) { ?>
                        <div class="tf-tooltip tf-d-b">
                            <div class="room-detail-icon">
                                <span class="room-icon-wrap">
                                    <i class="fas fa-male"></i>
                                    <i class="fas fa-female"></i>
                                </span>
                                <span class="icon-text tf-d-b">x<?php echo esc_html( $adult_number ); ?></span>
                            </div>
                            <div class="tf-top">
								<?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                                <i class="tool-i"></i>
                            </div>
                        </div>
					<?php }
					if ( $child_number ) { ?>
                        <div class="tf-tooltip tf-d-b">
                            <div class="room-detail-icon">
                                <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                <span class="icon-text tf-d-b">x<?php echo esc_html( $child_number ); ?></span>
                            </div>
                            <div class="tf-top">
								<?php
								if ( ! empty( $child_age_limit ) ) {
									/* translators: %s: Child Age Limit */
									printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html( $child_age_limit ) );
								} else {
									esc_html_e( 'Number of Children', 'tourfic' );
								}
								?>
                                <i class="tool-i"></i>
                            </div>
                        </div>
					<?php } ?>
                </td>
                <td class="reserve tf-t-c">
                    <div class="tf-price-column">
						<?php
						if ( ! empty( $d_price ) && $hotel_discount_type != "none" ) {
							?>
                            <span class="tf-price"><del><?php echo wp_kses_post( wc_price( $price ) ); ?></del> <?php echo wp_kses_post( wc_price( $d_price ) ); ?></span>
							<?php
							$price = $d_price = "";
						} else if ( $hotel_discount_type == "none" || empty( $d_price ) ) {
							?>
                            <span class="tf-price"><?php echo wp_kses_post( wc_price( $price ) ); ?></span>
							<?php
							$price = '';
						}
						if ( $pricing_by == '1' ) { ?>
                            <div class="price-per-night">
								<?php
								if ( $multi_by_date_ck ) {
									/* translators: %s: Days */
									$days > 0 ? printf( esc_html__( 'for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per night', 'tourfic' );
								} else {
									/* translators: %s: Days */
									$days > 0 ? printf( esc_html__( 'for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per day', 'tourfic' );
								}
								?>
                            </div>
						<?php } else { ?>
                            <div class="price-per-night">
								<?php
								if ( $multi_by_date_ck ) {
									/* translators: %s: Days */
									$days > 0 ? printf( esc_html__( 'for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/night', 'tourfic' );
								} else {
									/* translators: %s: Days */
									$days > 0 ? printf( esc_html__( 'for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/day', 'tourfic' );
								}
								?>
                            </div>
						<?php } ?>

						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>
                            <span class="tf-price tf-deposit-amount-<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?> tf-hotel-deposit-hide" style="display: none;"><?php echo wp_kses_post( wc_price( $deposit_amount ) ); ?></span>
                            <div class="price-per-night tf-deposit-amount-<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?> tf-hotel-deposit-hide" style="display: none;"><?php esc_html_e( 'Need to be deposited', 'tourfic' ) ?></div>
						<?php } ?>
                    </div>
                    <form class="tf-room">
						<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
                        <span><?php esc_html_e( 'Select Rooms', 'tourfic' ); ?></span>
                        <div class="room-selection-wrap tf-field-group">
                            <select name="hotel_room_selected" class="tf-field" id="hotel-room-selected">
								<?php
								foreach ( range( 0, $num_room_available ) as $value ) {
									echo '<option>' . esc_html( $value ) . '</option>';
								}
								?>
                            </select>
                        </div>
                        <div class="room-submit-wrap">
                            <div class="roomselectissue"></div>
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) && ( $room["deposit_type"] != "none" ) ) { ?>

                                <div class="room-deposit-wrap">
                                    <input type="checkbox" id="tf-make-deposit<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>" name="make_deposit" value="<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>">
                                    <label for="tf-make-deposit<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>"><?php esc_html_e( "I'll make a Partial Payment", "tourfic" ) ?></label><br>
                                </div>
							<?php } ?>

                            <input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>">
                            <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                            <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                            <input type="hidden" name="option_id" value="<?php echo $unique_id . '_' . $room_option_key; ?>">
                            <input type="hidden" name="location" value="<?php echo esc_attr( $first_location_name ); ?>">
                            <input type="hidden" name="adult" value="<?php echo esc_attr( $form_adult ); ?>">
                            <input type="hidden" name="child" value="<?php echo esc_attr( $form_child ); ?>">
                            <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>">
                            <input type="hidden" name="check_in_date" value="<?php echo esc_attr( $form_check_in ); ?>">
                            <input type="hidden" name="check_out_date" value="<?php echo esc_attr( $form_check_out ); ?>">
                            <input type="hidden" id="hotel_roomid">
                            <input type="hidden" id="hotel_room_number">
                            <input type="hidden" id="hotel_room_uniqueid">
                            <input type="hidden" id="hotel_room_depo" value="false">
							<?php
							$tour_hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
							$tour_hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tour_hotel_service_avail ) && ! empty( $tour_hotel_service_type ) && ( $room_book_by != 2 || empty( $room_book_url ) ) ) {
								?>
                                <a class="tf_air_service tf-btn-normal btn-secondary" href="javascript:;" data-room="<?php echo esc_attr( $room_id ); ?>"><?php esc_html_e( 'I\'ll reserve', 'tourfic' ); ?></a>


                                <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap tf-hotel-service-design-1 tf-room" data-id="<?php echo esc_attr( $room_id ) ?>">
                                    <div class="tf-hotel-services">
                                        <div class="tf-hotel-services-text">
                                            <h3><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_title', esc_html__( 'Add Service to your Booking.', 'tourfic' ) ) ); ?></h3>
                                            <p><?php esc_html( Helper::tfopt( 'hotel_service_popup_subtile', esc_html__( 'Select the services you want to add to your booking.', 'tourfic' ) ) ); ?></p>
                                        </div>
                                        <div class="tf-hotel-service">
                                            <label><?php esc_html_e( 'Pickup & Drop-off Service', 'tourfic' ); ?></label>
                                            <select id="airport-service" name="airport_service">
                                                <option value="none"><?php esc_html_e( 'No Service', 'tourfic' ); ?></option>
												<?php
												foreach ( $tour_hotel_service_type as $single_service_type ) { ?>
                                                    <option value="<?php echo esc_attr( $single_service_type ); ?>">
														<?php
														if ( "pickup" == $single_service_type ) {
															esc_html_e( 'Pickup Service', 'tourfic' );
														}
														if ( "dropoff" == $single_service_type ) {
															esc_html_e( 'Drop-off Service', 'tourfic' );
														}
														if ( "both" == $single_service_type ) {
															esc_html_e( 'Pickup & Drop-off Service', 'tourfic' );
														}
														?>
                                                    </option>
												<?php } ?>
                                            </select>
                                            <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                            <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                            <input type="hidden" name="option_id" value="<?php echo $unique_id . '_' . $room_option_key; ?>">
                                            <input type="hidden" id="hotel-post-id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                        </div>
                                        <div class="tf-airport-pickup-response"></div>
                                        <div class="tf_button_group">
                                            <button class="hotel-room-book tf-btn-normal btn-primary" type="submit"
                                                    style="width: 100%"><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_action', esc_html__( 'Continue to booking', 'tourfic' ) ) ); ?></button>
                                        </div>
                                    </div>
                                </div>

							<?php } else { ?>
                                <button class="hotel-room-book tf-btn-normal btn-primary" type="submit"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></button>
							<?php } ?>
                        </div>
                        <div class="tf_desc"></div>
						<?php //tf_hotel_without_booking_popup( $hotel_id, $room_id, $form_adult, $form_child );
						?>
                    </form>
                </td>
                </tr>
                <?php } ?>

				<?php if ( $room_option_key < count( $room_options ) - 1 ) : ?>
                <tr>
			<?php endif;
			endforeach;
		else:
			?>
			<?php if ( $total_room_option_count > 0 ) : ?>
                <td class="options"></td>
            <?php endif; ?>
            <td class="pax">
                <div style="text-align:center; width: 100%;"><?php echo esc_html__( "Pax:", "tourfic" ); ?></div>
				<?php if ( $adult_number ) { ?>
                    <div class="tf-tooltip tf-d-b">
                        <div class="room-detail-icon">
                            <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                            <span class="icon-text tf-d-b">x<?php echo esc_html( $adult_number ); ?></span>
                        </div>
                        <div class="tf-top">
							<?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                            <i class="tool-i"></i>
                        </div>
                    </div>
				<?php }
				if ( $child_number ) { ?>
                    <div class="tf-tooltip tf-d-b">
                        <div class="room-detail-icon">
                            <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                            <span class="icon-text tf-d-b">x<?php echo esc_html( $child_number ); ?></span>
                        </div>
                        <div class="tf-top">
							<?php
							if ( ! empty( $child_age_limit ) ) {
								/* translators: %s: Child Age Limit */
								printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html( $child_age_limit ) );
							} else {
								esc_html_e( 'Number of Children', 'tourfic' );
							}
							?>
                            <i class="tool-i"></i>
                        </div>
                    </div>
				<?php } ?>
            </td>
            <td class="reserve tf-t-c">
                <div class="tf-price-column">
					<?php
					if ( ! empty( $d_price ) && $hotel_discount_type != "none" ) {
						?>
                        <span class="tf-price"><del><?php echo wp_kses_post( wc_price( $price ) ); ?></del> <?php echo wp_kses_post( wc_price( $d_price ) ); ?></span>
						<?php
						$d_price = "";
					} else if ( $hotel_discount_type == "none" || empty( $d_price ) ) {
						?>
                        <span class="tf-price"><?php echo wp_kses_post( wc_price( $price ) ); ?></span>
						<?php
					}
					if ( $pricing_by == '1' ) { ?>
                        <div class="price-per-night">
							<?php
							if ( $multi_by_date_ck ) {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per night', 'tourfic' );
							} else {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per day', 'tourfic' );
							}
							?>
                        </div>
					<?php } else { ?>
                        <div class="price-per-night">
							<?php
							if ( $multi_by_date_ck ) {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/night', 'tourfic' );
							} else {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/day', 'tourfic' );
							}
							?>
                        </div>
					<?php } ?>

					<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>
                        <span class="tf-price tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide" style="display: none;"><?php echo wp_kses_post( wc_price( $deposit_amount ) ); ?></span>
                        <div class="price-per-night tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide" style="display: none;"><?php esc_html_e( 'Need to be deposited', 'tourfic' ) ?></div>
					<?php } ?>
                </div>
                <form class="tf-room">
					<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
                    <span><?php esc_html_e( 'Select Rooms', 'tourfic' ); ?></span>
                    <div class="room-selection-wrap tf-field-group">
                        <select name="hotel_room_selected" class="tf-field" id="hotel-room-selected">
							<?php
							foreach ( range( 0, $num_room_available ) as $value ) {
								echo '<option>' . esc_html( $value ) . '</option>';
							}
							?>
                        </select>
                    </div>
                    <div class="room-submit-wrap">
                        <div class="roomselectissue"></div>
						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) && ( $room["deposit_type"] != "none" ) ) { ?>

                            <div class="room-deposit-wrap">
                                <input type="checkbox" id="tf-make-deposit<?php echo esc_attr( $room_id ) ?>" name="make_deposit" value="<?php echo esc_attr( $room_id ) ?>">
                                <label for="tf-make-deposit<?php echo esc_attr( $room_id ) ?>"><?php esc_html_e( "I'll make a Partial Payment", "tourfic" ) ?></label><br>
                            </div>
						<?php } ?>

                        <input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>">
                        <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                        <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                        <input type="hidden" name="location" value="<?php echo esc_attr( $first_location_name ); ?>">
                        <input type="hidden" name="adult" value="<?php echo esc_attr( $form_adult ); ?>">
                        <input type="hidden" name="child" value="<?php echo esc_attr( $form_child ); ?>">
                        <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>">
                        <input type="hidden" name="check_in_date" value="<?php echo esc_attr( $form_check_in ); ?>">
                        <input type="hidden" name="check_out_date" value="<?php echo esc_attr( $form_check_out ); ?>">
                        <input type="hidden" id="hotel_roomid">
                        <input type="hidden" id="hotel_room_number">
                        <input type="hidden" id="hotel_room_uniqueid">
                        <input type="hidden" id="hotel_room_depo" value="false">
						<?php
						$tour_hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
						$tour_hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
						if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tour_hotel_service_avail ) && ! empty( $tour_hotel_service_type ) && ( $room_book_by != 2 || empty( $room_book_url ) ) ) {
							?>
                            <a class="tf_air_service tf-btn-normal btn-secondary" href="javascript:;" data-room="<?php echo esc_attr( $room_id ); ?>"><?php esc_html_e( 'I\'ll reserve', 'tourfic' ); ?></a>


                            <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap tf-hotel-service-design-1 tf-room" data-id="<?php echo esc_attr( $room_id ) ?>">
                                <div class="tf-hotel-services">
                                    <div class="tf-hotel-services-text">
                                        <h3><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_title', esc_html__( 'Add Service to your Booking.', 'tourfic' ) ) ); ?></h3>
                                        <p><?php esc_html( Helper::tfopt( 'hotel_service_popup_subtile', esc_html__( 'Select the services you want to add to your booking.', 'tourfic' ) ) ); ?></p>
                                    </div>
                                    <div class="tf-hotel-service">
                                        <label><?php esc_html_e( 'Pickup & Drop-off Service', 'tourfic' ); ?></label>
                                        <select id="airport-service" name="airport_service">
                                            <option value="none"><?php esc_html_e( 'No Service', 'tourfic' ); ?></option>
											<?php
											foreach ( $tour_hotel_service_type as $single_service_type ) { ?>
                                                <option value="<?php echo esc_attr( $single_service_type ); ?>">
													<?php
													if ( "pickup" == $single_service_type ) {
														esc_html_e( 'Pickup Service', 'tourfic' );
													}
													if ( "dropoff" == $single_service_type ) {
														esc_html_e( 'Drop-off Service', 'tourfic' );
													}
													if ( "both" == $single_service_type ) {
														esc_html_e( 'Pickup & Drop-off Service', 'tourfic' );
													}
													?>
                                                </option>
											<?php } ?>
                                        </select>
                                        <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                        <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                        <input type="hidden" id="hotel-post-id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                    </div>
                                    <div class="tf-airport-pickup-response"></div>
                                    <div class="tf_button_group">
                                        <button class="hotel-room-book tf-btn-normal btn-primary" type="submit"
                                                style="width: 100%"><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_action', esc_html__( 'Continue to booking', 'tourfic' ) ) ); ?></button>
                                    </div>
                                </div>
                            </div>

						<?php } else { ?>
                            <button class="hotel-room-book tf-btn-normal btn-primary" type="submit"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></button>
						<?php } ?>
                    </div>
                    <div class="tf_desc"></div>
					<?php //tf_hotel_without_booking_popup( $hotel_id, $room_id, $form_adult, $form_child );
					?>
                </form>
            </td>
            </tr>
		<?php
		endif;
	}
} elseif ( $tf_hotel_selected_template_check == "design-2" ) {
	if ( empty( $tf_room_disable_date ) ) {
		?>
        <div class="tf-available-room tf-desktop-room">
            <div class="tf-available-room-gallery">
				<?php
				$tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
				if ( $tour_room_details_gall ) {
					$tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
				}
				$room_preview_img = get_the_post_thumbnail_url( $room_id, 'full' );
				if ( ! empty( $room_preview_img ) ) { ?>
                    <div class="tf-room-gallery">
                        <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                    </div>
				<?php } ?>
				<?php
				if ( ! empty( $tf_room_gallery_ids ) ) {
					$gallery_limit = 1;
					foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
						$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
						if ( $gallery_limit < 3 ) {
							?>
							<?php
							if ( count( $tf_room_gallery_ids ) > 1 ) { ?>
								<?php if ( 1 == $gallery_limit ) { ?>
                                    <div class="tf-room-gallery">
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                    </div>
								<?php } ?>
								<?php if ( 2 == $gallery_limit ) { ?>
                                    <div class="tf-room-gallery tf-popup-buttons tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                         data-hotel="<?php echo esc_attr( $hotel_id ); ?>" style="background-image: url('<?php echo esc_url( $image_url ); ?>'); ">
                                        <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="content">
                                                <path id="Rectangle 2111"
                                                      d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                                      stroke="#FDF9F4" stroke-width="1.5"></path>
                                                <path id="Rectangle 2109"
                                                      d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                                      stroke="#FDF9F4" stroke-width="1.5"></path>
                                                <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17"
                                                      stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                                                <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </g>
                                        </svg>
                                    </div>
								<?php } ?>
							<?php } ?>

						<?php }
						$gallery_limit ++;
					}
				} ?>
            </div>
		<?php
		if ( $pricing_by == '3' && ! empty( $room_options ) ):
			echo '<div class="tf-available-room-contents">';
			echo '<h2 class="tf-section-title">' . esc_html( get_the_title( $room_id ) ) . '</h2>';
			foreach ( $room_options as $room_option_key => $room_option ):
				$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
				$has_option  = [];

				if ( $avil_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
					if(!$multi_by_date_ck){
						if ( $tf_startdate && $tf_enddate ) {
							// Check availability by date option
							$period = new \DatePeriod(
								new \DateTime( $tf_startdate . ' 00:00' ),
								new \DateInterval( 'P1D' ),
								new \DateTime( $tf_enddate . ' 23:59' )
							);
						}
					}else{
						if ( $tf_startdate && $tf_enddate ) {
							$period = new \DatePeriod(
								new \DateTime( $tf_startdate . ' 00:00' ),
								new \DateInterval( 'P1D' ),
								new \DateTime( $tf_enddate . ' 00:00' )
							);
						}
					}

					// split date range
					$check_in  = strtotime( $form_start . ' 00:00' );
					$check_out = strtotime( $form_end . ' 00:00' );
					$price = $price_by_date = $d_price = $d_price_by_date = 0;

					// extract price from available room options
					foreach ( $period as $date ) {

						$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
							if( $date_availability['status'] == 'available' ){
								$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
								$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

								return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
							} else {
								return false;
							}
						} ) );

						if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {

							$d_price_by_date = 0;
							$data          = $available_rooms[0];
							$options_count = $data['options_count'] ?? 0;

							if($data[ 'tf_room_option_' . $room_option_key ] == ''){
								$has_option[] = 0;
							}

							if ( $data[ 'tf_room_option_' . $room_option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $room_option_key ] == 'per_room' ) {
								$room_price = $price_by_date = ! empty( $data[ 'tf_option_room_price_' . $room_option_key ] ) ? $data[ 'tf_option_room_price_' . $room_option_key ] : 0;

								$d_room_price = $d_price_by_date = !empty($room_price) ? Pricing::apply_discount($room_price, $hotel_discount_type, $hotel_discount_amount) : 0;
								$d_price += $d_room_price;
								$has_option[] = 1;
							} else if ( $data[ 'tf_room_option_' . $room_option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $room_option_key ] == 'per_person' ) {
								$adult_price = ! empty( $data[ 'tf_option_adult_price_' . $room_option_key ] ) ? $data[ 'tf_option_adult_price_' . $room_option_key ] : 0;
								$child_price = ! empty( $data[ 'tf_option_child_price_' . $room_option_key ] ) ? $data[ 'tf_option_child_price_' . $room_option_key ] : 0;
								$price_by_date = ( $adult_price * $form_adult ) + ( $child_price * $form_child );

								$d_adult_price = !empty($adult_price) ? Pricing::apply_discount($adult_price, $hotel_discount_type, $hotel_discount_amount) : 0;
								$d_child_price = !empty($child_price) ? Pricing::apply_discount($child_price, $hotel_discount_type, $hotel_discount_amount) : 0;
								$d_price_by_date = ( $d_adult_price * $form_adult ) + ( $d_child_price * $form_child );
								$d_price += $d_price_by_date;
								$has_option[] = 1;
							}
							$price 			+= $price_by_date;
						} else {
							$has_option[] = 0;
						}
					}

				} else{
					if ( $option_price_type === 'per_room' ) {
						$option_price = $price_by_date = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
					} elseif ( $option_price_type === 'per_person' ) {
						$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
						$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;

						$price_by_date = ( ( $option_adult_price * $form_adult ) + ( $option_child_price * $form_child ) );
					}

					if ( $option_price_type == 'per_room' ) {
						$d_room_price = $d_price_by_date = Pricing::apply_discount( $option_price, $hotel_discount_type, $hotel_discount_amount );
					} elseif ( $option_price_type == 'per_person' ) {
						$d_room_adult_price = Pricing::apply_discount( $option_adult_price, $hotel_discount_type, $hotel_discount_amount );
						$d_room_child_price = Pricing::apply_discount( $option_child_price, $hotel_discount_type, $hotel_discount_amount );

						$d_price_by_date = ( ( $d_room_adult_price * $form_adult ) + ( $d_room_child_price * $form_child ) );
					}

					$price   = $price_by_date * $days;
					$d_price = $d_price_by_date * $days;
				}

				Helper::tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit, $d_price );
				if ( ! in_array( 0, $has_option ) ) {
				?>
                <div class="tf-available-room-content tf-room-options-content">
                    <div class="tf-room-options-content-inner">
                        <div class="tf-available-room-content-left">
                            <h4><?php echo esc_html( $room_option['option_title'] ); ?></h4>
                            <ul class="tf-option-list">
                                <?php if ( ! empty( $room_option['room-facilities'] ) ) :
                                    foreach ( $room_option['room-facilities'] as $room_facility ) :
                                        ?>
                                        <li>
                                            <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                            <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                                        </li>
                                    <?php endforeach;
                                endif; ?>
                            </ul>
                            <ul class="tf-room-info-list">
                                <?php if ( $footage ) { ?>
                                    <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
                                <?php } ?>
                                <?php if ( $bed ) { ?>
                                    <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
                                <?php } ?>
                                <?php if ( $adult_number ) { ?>
                                    <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?><?php esc_html_e( ' Adults', 'tourfic' ); ?></li>
                                <?php } ?>
                                <?php if ( $child_number ) { ?>
                                    <li><i class="ri-user-smile-line"></i> <?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
                                <?php } ?>
                                <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                       data-hotel="<?php echo esc_attr( $hotel_id ); ?>"><?php esc_html_e( "View room details", "tourfic" ); ?></a></li>

                            </ul>
                            <span class="tf-others-benefits-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                            <ul>
                                <?php
                                if ( ! empty( $room['features'] ) ) {
                                    $tf_room_fec_key = 1;
                                    foreach ( $room['features'] as $feature ) {
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
                                } ?>
                                <?php
                                if ( ! empty( $room['features'] ) ) {
                                    if ( count( $room['features'] ) >= 6 ) {
                                        ?>

                                        <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                               data-hotel="<?php echo esc_attr( $hotel_id ); ?>"><?php esc_html_e( "See all benefits", "tourfic" ); ?></a></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <form class="reserve tf-room tf-available-room-content-right">
                            <?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
                            <?php
                            if ( ! empty( $hotel_discount_type ) && ! empty( $hotel_discount_amount ) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type ) ) { ?>
                                <div class="tf-available-room-off">
                                <span>
                                    <?php echo ( "percent" == $hotel_discount_type ) ? esc_html( $hotel_discount_amount ) . '% off' : wp_kses_post( wc_price( $hotel_discount_amount ) ) . 'off'; ?>
                                </span>
                                </div>
                            <?php } ?>

                            <div class="tf-available-room-price">
                                <input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                <input type="hidden" name="option_id" value="<?php echo $unique_id . '_' . $room_option_key; ?>">
                                <input type="hidden" name="location" value="<?php echo esc_attr( $first_location_name ); ?>">
                                <input type="hidden" name="adult" value="<?php echo esc_attr( $form_adult ); ?>">
                                <input type="hidden" name="child" value="<?php echo esc_attr( $form_child ); ?>">
                                <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>">
                                <input type="hidden" name="check_in_date" value="<?php echo esc_attr( $form_check_in ); ?>">
                                <input type="hidden" name="check_out_date" value="<?php echo esc_attr( $form_check_out ); ?>">
                                <input type="hidden" id="hotel_roomid">
                                <input type="hidden" id="hotel_room_number">
                                <input type="hidden" id="hotel_room_uniqueid">
                                <?php if ( $hotel_discount_type != 'none' && ! empty( $hotel_discount_amount ) ) { ?>
                                    <span class="tf-price">
                                        <span class="discount-price">
                                            <del><?php echo wp_kses_post( wc_price( $price ) ); ?></del>
                                        </span>
                                        <span class="sale-price">
                                            <?php echo wp_kses_post( wc_price( $d_price ) ); ?>
                                        </span>
                                    </span>
                                    <?php
                                } else {
                                    ?>
                                    <span class="tf-price">
                                        <span class="sale-price">
                                            <?php echo wp_kses_post( wc_price( $price ) ); ?>
                                        </span>
                                    </span>
                                    <?php
                                }
                                ?>
                                <div class="tf-available-room-purchase-summery">
                                    <div class="price-per-night">
                                        <?php
                                        if ( $multi_by_date_ck ) {
                                            /* translators: %s: Days */
                                            $days > 0 ? printf( esc_html__( ' / for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/night', 'tourfic' );
                                        } else {
                                            /* translators: %s: Days */
                                            $days > 0 ? printf( esc_html__( ' /for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/day', 'tourfic' );
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tf-available-room-select">
                                <span><?php esc_html_e( "Select your Room", "tourfic" ); ?></span>
                                <select name="hotel_room_selected" id="hotel-room-selected" style="background-image: url(<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/select-arrow-dark.svg);">
                                    <?php
                                    foreach ( range( 0, $num_room_available ) as $value ) {
                                        echo '<option>' . esc_html( $value ) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="room-submit-wrap">
                                <div class="tf-deposit-content">
                                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>
                                        <span class="tf-price tf-deposit-amount-<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?> tf-hotel-deposit-hide" style="display: none;"><?php echo wp_kses_post( wc_price( $deposit_amount ) ); ?></span>
                                        <div class="price-per-night tf-deposit-amount-<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?> tf-hotel-deposit-hide"
                                             style="display: none;"><?php esc_html_e( 'Need to be deposited', 'tourfic' ) ?></div>
                                    <?php } ?>

                                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) && ( $room["deposit_type"] != "none" ) ) { ?>

                                        <div class="room-deposit-wrap">
                                            <input type="checkbox" id="tf-make-deposit<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>" name="make_deposit" value="<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>">
                                            <label for="tf-make-deposit<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>"><?php esc_html_e( "I'll make a Partial Payment", "tourfic" ) ?></label><br>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                                $tour_hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
                                $tour_hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
                                if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tour_hotel_service_avail ) && ! empty( $tour_hotel_service_type ) && ( $room_book_by != 2 || empty( $room_book_url ) ) ) {
                                    ?>
                                    <input type="hidden" id="hotel_room_depo" value="false">
                                    <div class="roomselectissue"></div>
                                    <a class="tf_air_service" href="javascript:;" data-room="<?php echo esc_attr( $room_id ); ?>"><?php esc_html_e( 'Continue', 'tourfic' ); ?></a>

                                    <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap tf-hotel-service-design-1 tf-room" data-id="<?php echo esc_attr( $room_id ) ?>">
                                        <div class="tf-hotel-services">
                                            <div class="tf-hotel-services-text">
                                                <h3><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_title', esc_html__( 'Add Service to your Booking.', 'tourfic' ) ) ); ?></h3>
                                                <p><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_subtile', esc_html__( 'Select the services you want to add to your booking.', 'tourfic' ) ) ); ?></p>
                                            </div>
                                            <div class="tf-hotel-service">
                                                <label><?php esc_html_e( 'Pickup & Drop-off Service', 'tourfic' ); ?></label>
                                                <select id="airport-service" name="airport_service">
                                                    <option value="none"><?php esc_html_e( 'No Service', 'tourfic' ); ?></option>
                                                    <?php
                                                    foreach ( $tour_hotel_service_type as $single_service_type ) { ?>
                                                        <option value="<?php echo esc_attr( $single_service_type ); ?>">
                                                            <?php
                                                            if ( "pickup" == $single_service_type ) {
                                                                esc_html_e( 'Pickup Service', 'tourfic' );
                                                            }
                                                            if ( "dropoff" == $single_service_type ) {
                                                                esc_html_e( 'Drop-off Service', 'tourfic' );
                                                            }
                                                            if ( "both" == $single_service_type ) {
                                                                esc_html_e( 'Pickup & Drop-off Service', 'tourfic' );
                                                            }
                                                            ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                                <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                                <input type="hidden" name="option_id" value="<?php echo $unique_id . '_' . $room_option_key; ?>">
                                                <input type="hidden" id="hotel-post-id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                            </div>
                                            <div class="tf-airport-pickup-response"></div>
                                            <div class="tf_button_group">
                                                <button class="hotel-room-book" type="submit"
                                                        style="width: 100%"><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_action', esc_html__( 'Continue to booking', 'tourfic' ) ) ); ?></button>
                                            </div>
                                        </div>
                                    </div>

                                <?php } else { ?>
                                    <button class="hotel-room-book" type="submit"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
                    <?php
				}
			endforeach;
			echo '</div>';
		else:
			?>
            <div class="tf-available-room-content">
                <div class="tf-available-room-content-left">
                    <h2 class="tf-section-title"><?php echo esc_html( get_the_title( $room_id ) ); ?></h2>
                    <ul>
						<?php if ( $footage ) { ?>
                            <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $bed ) { ?>
                            <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $adult_number ) { ?>
                            <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?><?php esc_html_e( ' Adults', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $child_number ) { ?>
                            <li><i class="ri-user-smile-line"></i><?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
						<?php } ?>
                        <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                               data-hotel="<?php echo esc_attr( $hotel_id ); ?>"><?php esc_html_e( "View room details", "tourfic" ); ?></a></li>

                    </ul>
                    <span class="tf-others-benefits-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                    <ul>
						<?php
						if ( ! empty( $room['features'] ) ) {
							$tf_room_fec_key = 1;
							foreach ( $room['features'] as $feature ) {
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
										<?php echo esc_html( $room_term->name ); ?>
                                    </li>
								<?php }
								$tf_room_fec_key ++;
							}
						} ?>
						<?php
						if ( ! empty( $room['features'] ) ) {
							if ( count( $room['features'] ) >= 6 ) {
								?>

                                <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                       data-hotel="<?php echo esc_attr( $hotel_id ); ?>"><?php esc_html_e( "See all benefits", "tourfic" ); ?></a></li>
								<?php
							}
						}
						?>
                    </ul>
                </div>
                <form class="reserve tf-room tf-available-room-content-right">
					<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>
					<?php
					if ( ! empty( $hotel_discount_type ) && ! empty( $hotel_discount_amount ) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type ) ) { ?>
                        <div class="tf-available-room-off">
                            <span>
                                <?php echo ( "percent" == $hotel_discount_type ) ? esc_html( $hotel_discount_amount ) . '% off' : wp_kses_post( wc_price( $hotel_discount_amount ) ) . 'off'; ?>
                            </span>
                        </div>
					<?php } ?>

                    <div class="tf-available-room-price">
                        <input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>">
                        <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                        <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                        <input type="hidden" name="location" value="<?php echo esc_attr( $first_location_name ); ?>">
                        <input type="hidden" name="adult" value="<?php echo esc_attr( $form_adult ); ?>">
                        <input type="hidden" name="child" value="<?php echo esc_attr( $form_child ); ?>">
                        <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>">
                        <input type="hidden" name="check_in_date" value="<?php echo esc_attr( $form_check_in ); ?>">
                        <input type="hidden" name="check_out_date" value="<?php echo esc_attr( $form_check_out ); ?>">
                        <input type="hidden" id="hotel_roomid">
                        <input type="hidden" id="hotel_room_number">
                        <input type="hidden" id="hotel_room_uniqueid">
						<?php
						if ( $pricing_by == '1' ) {
							if ( $hotel_discount_type != 'none' && ! empty( $hotel_discount_amount ) ) {
								?>
                                <span class="tf-price">
                                    <span class="discount-price">
                                        <del><?php echo wp_kses_post( wc_price( $price ) ); ?></del>
                                    </span>
                                    <span class="sale-price">
                                        <?php echo wp_kses_post( wc_price( $d_price ) ); ?>
                                    </span>
                                </span>
								<?php
							} else {
								?>
                                <span class="tf-price">
                                    <span class="sale-price">
                                        <?php echo wp_kses_post( wc_price( $price ) ); ?>
                                    </span>
                                </span>
								<?php
							}
							?>
							<?php
						} else {
							if ( $hotel_discount_type != 'none' && ! empty( $hotel_discount_amount ) ) {
								?>
                                <span class="tf-price">
                                    <span class="discount-price">
                                        <del><?php echo wp_kses_post( wc_price( $price ) ); ?></del>
                                    </span>
                                    <span class="sale-price">
                                        <?php echo wp_kses_post( wc_price( $d_price ) ); ?>
                                    </span>
                                </span>
								<?php
							} else {
								?>
                                <span class="tf-price">
                                    <span class="sale-price">
                                        <?php echo wp_kses_post( wc_price( $price ) ); ?>
                                    </span>
                                </span>
								<?php
							}
							?>
							<?php
						}
						?>
                        <div class="tf-available-room-purchase-summery">
							<?php
							if ( $pricing_by == '1' ) { ?>
                                <div class="price-per-night">
									<?php
									if ( $multi_by_date_ck ) {
										/* translators: %s: Days */
										$days > 0 ? printf( esc_html__( ' / for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per night', 'tourfic' );
									} else {
										/* translators: %s: Days */
										$days > 0 ? printf( esc_html__( ' / for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per day', 'tourfic' );
									}
									?>
                                </div>
							<?php } else { ?>
                                <div class="price-per-night">
									<?php
									if ( $multi_by_date_ck ) {
										/* translators: %s: Days */
										$days > 0 ? printf( esc_html__( ' / for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/night', 'tourfic' );
									} else {
										/* translators: %s: Days */
										$days > 0 ? printf( esc_html__( ' /for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/day', 'tourfic' );
									}
									?>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div class="tf-available-room-select">
                        <span><?php esc_html_e( "Select your Room", "tourfic" ); ?></span>
                        <select name="hotel_room_selected" id="hotel-room-selected" style="background-image: url(<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/select-arrow-dark.svg);">
							<?php
							foreach ( range( 0, $num_room_available ) as $value ) {
								echo '<option>' . esc_html( $value ) . '</option>';
							}
							?>
                        </select>
                    </div>

                    <div class="room-submit-wrap">
                        <div class="tf-deposit-content">
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>
                                <span class="tf-price tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide" style="display: none;"><?php echo wp_kses_post( wc_price( $deposit_amount ) ); ?></span>
                                <div class="price-per-night tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide"
                                     style="display: none;"><?php esc_html_e( 'Need to be deposited', 'tourfic' ) ?></div>
							<?php } ?>

							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) && ( $room["deposit_type"] != "none" ) ) { ?>

                                <div class="room-deposit-wrap">
                                    <input type="checkbox" id="tf-make-deposit<?php echo esc_attr( $room_id ) ?>" name="make_deposit" value="<?php echo esc_attr( $room_id ) ?>">
                                    <label for="tf-make-deposit<?php echo esc_attr( $room_id ) ?>"><?php esc_html_e( "I'll make a Partial Payment", "tourfic" ) ?></label><br>
                                </div>
							<?php } ?>
                        </div>
						<?php
						$tour_hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
						$tour_hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
						if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tour_hotel_service_avail ) && ! empty( $tour_hotel_service_type ) && ( $room_book_by != 2 || empty( $room_book_url ) ) ) {
							?>
                            <input type="hidden" id="hotel_room_depo" value="false">
                            <div class="roomselectissue"></div>
                            <a class="tf_air_service" href="javascript:;" data-room="<?php echo esc_attr( $room_id ); ?>"><?php esc_html_e( 'Continue', 'tourfic' ); ?></a>

                            <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap tf-hotel-service-design-1 tf-room" data-id="<?php echo esc_attr( $room_id ) ?>">
                                <div class="tf-hotel-services">
                                    <div class="tf-hotel-services-text">
                                        <h3><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_title', esc_html__( 'Add Service to your Booking.', 'tourfic' ) ) ); ?></h3>
                                        <p><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_subtile', esc_html__( 'Select the services you want to add to your booking.', 'tourfic' ) ) ); ?></p>
                                    </div>
                                    <div class="tf-hotel-service">
                                        <label><?php esc_html_e( 'Pickup & Drop-off Service', 'tourfic' ); ?></label>
                                        <select id="airport-service" name="airport_service">
                                            <option value="none"><?php esc_html_e( 'No Service', 'tourfic' ); ?></option>
											<?php
											foreach ( $tour_hotel_service_type as $single_service_type ) { ?>
                                                <option value="<?php echo esc_attr( $single_service_type ); ?>">
													<?php
													if ( "pickup" == $single_service_type ) {
														esc_html_e( 'Pickup Service', 'tourfic' );
													}
													if ( "dropoff" == $single_service_type ) {
														esc_html_e( 'Drop-off Service', 'tourfic' );
													}
													if ( "both" == $single_service_type ) {
														esc_html_e( 'Pickup & Drop-off Service', 'tourfic' );
													}
													?>
                                                </option>
											<?php } ?>
                                        </select>
                                        <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                        <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                        <input type="hidden" id="hotel-post-id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                    </div>
                                    <div class="tf-airport-pickup-response"></div>
                                    <div class="tf_button_group">
                                        <button class="hotel-room-book" type="submit"
                                                style="width: 100%"><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_action', esc_html__( 'Continue to booking', 'tourfic' ) ) ); ?></button>
                                    </div>
                                </div>
                            </div>

						<?php } else { ?>
                            <button class="hotel-room-book" type="submit"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></button>
						<?php } ?>
                    </div>
                </form>
            </div>
		<?php endif; ?>
        </div>

        <div class="tf-available-room tf-tabs-room">
            <div class="tf-available-room-gallery">
				<?php
				$tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
				if ( $tour_room_details_gall ) {
					$tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
				}
				$room_preview_img = get_the_post_thumbnail_url( $room_id, 'full' );
				if ( ! empty( $room_preview_img ) ) { ?>
                    <div class="tf-room-image">
						<?php
						if ( ! empty( $hotel_discount_type ) && ! empty( $hotel_discount_amount ) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type ) ) { ?>
                            <div class="tf-available-room-off">
                                <span>
                                    <?php echo ( "percent" == $hotel_discount_type ) ? esc_html( $hotel_discount_amount ) . '% off' : wp_kses_post( wc_price( $hotel_discount_amount ) ) . 'off'; ?>
                                </span>
                            </div>
						<?php } ?>
                        <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                    </div>
				<?php } ?>
                <div class="tf-room-gallerys">
					<?php
					if ( ! empty( $tf_room_gallery_ids ) ) {
						$gallery_limit = 1;
						foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
							$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
							if ( $gallery_limit < 3 ) {
								?>
								<?php
								if ( count( $tf_room_gallery_ids ) > 1 ) { ?>
									<?php if ( 1 == $gallery_limit ) { ?>
                                        <div class="tf-room-gallery">
                                            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                        </div>
									<?php } ?>
									<?php if ( 2 == $gallery_limit ) { ?>
                                        <div class="tf-room-gallery tf-popup-buttons tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                             data-hotel="<?php echo esc_attr( $hotel_id ); ?>" style="background-image: url('<?php echo esc_url( $image_url ); ?>'); ">
                                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="content">
                                                    <path id="Rectangle 2111"
                                                          d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                                    <path id="Rectangle 2109"
                                                          d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                                    <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17"
                                                          stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                                                    <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </g>
                                            </svg>
                                        </div>
									<?php } ?>
								<?php } ?>

							<?php }
							$gallery_limit ++;
						}
					} ?>
                </div>
            </div>
            <div class="tf-available-room-content">
                <div class="tf-available-room-content-left">
                    <div class="room-heading-price">
                        <h2 class="tf-section-title"><?php echo esc_html( get_the_title( $room_id ) ); ?></h2>
                        <div class="tf-available-room-price">

							<?php
							if ( $pricing_by == '1' ) {
								if ( $hotel_discount_type != 'none' && ! empty( $hotel_discount_amount ) ) {
									?>
                                    <span class="tf-price">
                                        <span class="discount-price">
                                            <del><?php echo wp_kses_post( wc_price( $price ) ); ?></del>
                                        </span>
                                        <span class="sale-price">
                                            <?php echo wp_kses_post( wc_price( $d_price ) ); ?>
                                        </span>
                                    </span>
									<?php
								} else {
									?>
                                    <span class="tf-price">
                                        <span class="sale-price">
                                            <?php echo wp_kses_post( wc_price( $price ) ); ?>
                                        </span>
                                    </span>
									<?php
								}
								?>
								<?php
							} else {
								if ( $hotel_discount_type != 'none' && ! empty( $hotel_discount_amount ) ) {
									?>
                                    <span class="tf-price">
                            <span class="discount-price">
                                <del><?php echo wp_kses_post( wc_price( $price ) ); ?></del>
                            </span>
                            <span class="sale-price">
                                <?php echo wp_kses_post( wc_price( $d_price ) ); ?>
                            </span>
                        </span>
									<?php
								} else {
									?>
                                    <span class="tf-price">
                            <span class="sale-price">
                                <?php echo wp_kses_post( wc_price( $price ) ); ?>
                            </span>
                        </span>
									<?php
								}
								?>
								<?php
							}
							?>
                            <div class="tf-available-room-purchase-summery">
								<?php
								if ( $pricing_by == '1' ) { ?>
                                    <div class="price-per-night">
										<?php
										if ( $multi_by_date_ck ) {
											/* translators: %s: Days */
											$days > 0 ? printf( esc_html__( ' / for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per night', 'tourfic' );
										} else {
											/* translators: %s: Days */
											$days > 0 ? printf( esc_html__( ' / for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per day', 'tourfic' );
										}
										?>
                                    </div>
								<?php } else { ?>
                                    <div class="price-per-night">
										<?php
										if ( $multi_by_date_ck ) {
											/* translators: %s: Days */
											$days > 0 ? printf( esc_html__( ' / for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/night', 'tourfic' );
										} else {
											/* translators: %s: Days */
											$days > 0 ? printf( esc_html__( ' /for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/day', 'tourfic' );
										}
										?>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                    <ul>
						<?php if ( $footage ) { ?>
                            <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $bed ) { ?>
                            <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $adult_number ) { ?>
                            <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?><?php esc_html_e( ' Adults', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $child_number ) { ?>
                            <li><i class="ri-user-smile-line"></i><?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
						<?php } ?>
                        <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                               data-hotel="<?php echo esc_attr( $hotel_id ); ?>"><?php esc_html_e( "View room details", "tourfic" ); ?></a></li>

                    </ul>
                    <span class="tf-others-benefits-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                    <ul>
						<?php
						if ( ! empty( $room['features'] ) ) {
							$tf_room_fec_key = 1;
							foreach ( $room['features'] as $feature ) {
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
										<?php echo esc_html( $room_term->name ); ?>
                                    </li>
								<?php }
								$tf_room_fec_key ++;
							}
						} ?>
						<?php
						if ( ! empty( $room['features'] ) ) {
							if ( count( $room['features'] ) >= 6 ) {
								?>

                                <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                       data-hotel="<?php echo esc_attr( $hotel_id ); ?>"><?php esc_html_e( "See all benefits", "tourfic" ); ?></a></li>
								<?php
							}
						}
						?>
                    </ul>
                </div>
                <form class="reserve tf-room tf-available-room-content-right">
					<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>

                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>">
                    <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                    <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                    <input type="hidden" name="location" value="<?php echo esc_attr( $first_location_name ); ?>">
                    <input type="hidden" name="adult" value="<?php echo esc_attr( $form_adult ); ?>">
                    <input type="hidden" name="child" value="<?php echo esc_attr( $form_child ); ?>">
                    <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>">
                    <input type="hidden" name="check_in_date" value="<?php echo esc_attr( $form_check_in ); ?>">
                    <input type="hidden" name="check_out_date" value="<?php echo esc_attr( $form_check_out ); ?>">
                    <input type="hidden" id="hotel_roomid">
                    <input type="hidden" id="hotel_room_number">
                    <input type="hidden" id="hotel_room_uniqueid">

                    <div class="tf-available-room-select">
                        <span><?php esc_html_e( "Select your Room", "tourfic" ); ?></span>
                        <select name="hotel_room_selected" id="hotel-room-selected" style="background-image: url(<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/select-arrow-dark.svg);">
							<?php
							foreach ( range( 0, $num_room_available ) as $value ) {
								echo '<option>' . esc_html( $value ) . '</option>';
							}
							?>
                        </select>
                    </div>

                    <div class="room-submit-wrap">
                        <div class="tf-deposit-content">
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>
                                <span class="tf-price tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide" style="display: none;"><?php echo wp_kses_post( wc_price( $deposit_amount ) ); ?></span>
                                <div class="price-per-night tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide"
                                     style="display: none;"><?php esc_html_e( 'Need to be deposited', 'tourfic' ) ?></div>
							<?php } ?>

							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) && ( $room["deposit_type"] != "none" ) ) { ?>

                                <div class="room-deposit-wrap">
                                    <input type="checkbox" id="tf-make-deposit<?php echo esc_attr( $room_id ) ?>" name="make_deposit" value="<?php echo esc_attr( $room_id ) ?>">
                                    <label for="tf-make-deposit<?php echo esc_attr( $room_id ) ?>"><?php esc_html_e( "I'll make a Partial Payment", "tourfic" ) ?></label><br>
                                </div>
							<?php } ?>
                        </div>
						<?php
						$tour_hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
						$tour_hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
						if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tour_hotel_service_avail ) && ! empty( $tour_hotel_service_type ) && ( $room_book_by != 2 || empty( $room_book_url ) ) ) {
							?>
                            <input type="hidden" id="hotel_room_depo" value="false">
                            <div class="roomselectissue"></div>
                            <a class="tf_air_service" href="javascript:;" data-room="<?php echo esc_attr( $room_id ); ?>"><?php esc_html_e( 'Continue', 'tourfic' ); ?></a>

                            <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap tf-hotel-service-design-1 tf-room" data-id="<?php echo esc_attr( $room_id ) ?>">
                                <div class="tf-hotel-services">
                                    <div class="tf-hotel-services-text">
                                        <h3><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_title', esc_html__( 'Add Service to your Booking.', 'tourfic' ) ) ); ?></h3>
                                        <p><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_subtile', esc_html__( 'Select the services you want to add to your booking.', 'tourfic' ) ) ); ?></p>
                                    </div>
                                    <div class="tf-hotel-service">
                                        <label><?php esc_html_e( 'Pickup & Drop-off Service', 'tourfic' ); ?></label>
                                        <select id="airport-service" name="airport_service">
                                            <option value="none"><?php esc_html_e( 'No Service', 'tourfic' ); ?></option>
											<?php
											foreach ( $tour_hotel_service_type as $single_service_type ) { ?>
                                                <option value="<?php echo esc_attr( $single_service_type ); ?>">
													<?php
													if ( "pickup" == $single_service_type ) {
														esc_html_e( 'Pickup Service', 'tourfic' );
													}
													if ( "dropoff" == $single_service_type ) {
														esc_html_e( 'Drop-off Service', 'tourfic' );
													}
													if ( "both" == $single_service_type ) {
														esc_html_e( 'Pickup & Drop-off Service', 'tourfic' );
													}
													?>
                                                </option>
											<?php } ?>
                                        </select>
                                        <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                        <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                        <input type="hidden" id="hotel-post-id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                    </div>
                                    <div class="tf-airport-pickup-response"></div>
                                    <div class="tf_button_group">
                                        <button class="hotel-room-book" type="submit"
                                                style="width: 100%"><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_action', esc_html__( 'Continue to booking', 'tourfic' ) ) ); ?></button>
                                    </div>
                                </div>
                            </div>

						<?php } else { ?>
                            <button class="hotel-room-book" type="submit"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></button>
						<?php } ?>
                    </div>
                </form>
            </div>
        </div>
		<?php
	}
} else {
	if ( empty( $tf_room_disable_date ) ) {
		?>
        <tr>
        <td class="description" rowspan="<?php echo ( $pricing_by == '3' && ! empty( $room_options ) ) ? count( $room_options ) : 1; ?>">
            <div class="tf-room-type">
                <div class="tf-room-title">
					<?php
					$tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
					if ( $tour_room_details_gall ) {
						$tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
					}
					if ( $tour_room_details_gall ) {
						?>
                        <h3><a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                               data-hotel="<?php echo esc_attr( $hotel_id ); ?>" style="text-decoration: underline;">
								<?php echo esc_html( get_the_title( $room_id ) ); ?>
                            </a></h3>

                        <div id="tour_room_details_qv" class=""></div>
					<?php } else { ?>
                        <h3><?php echo esc_html( get_the_title( $room_id ) ); ?></h3>
						<?php
					}
					?>
                </div>
				<?php if ( ! empty( get_post_field( 'post_content', $room_id ) ) ): ?>
                    <div class="bed-facilities"><p><?php echo wp_kses_post( get_post_field( 'post_content', $room_id ) ); ?></p></div>
				<?php endif; ?>
            </div>

			<?php if ( $footage ) { ?>
                <div class="tf-tooltip tf-d-ib">
                    <div class="room-detail-icon">
                        <span class="room-icon-wrap"><i class="ri-pencil-ruler-2-line"></i></span>
                        <span class="icon-text tf-d-b"><?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></span>
                    </div>
                    <div class="tf-top">
						<?php esc_html_e( 'Room Footage', 'tourfic' ); ?>
                        <i class="tool-i"></i>
                    </div>
                </div>
			<?php }
			if ( $bed ) { ?>
                <div class="tf-tooltip tf-d-ib">
                    <div class="room-detail-icon">
                        <span class="room-icon-wrap"><i class="ri-hotel-bed-line"></i></i></span>
                        <span class="icon-text tf-d-b">x<?php echo esc_html( $bed ); ?></span>
                    </div>
                    <div class="tf-top">
						<?php esc_html_e( 'Number of Beds', 'tourfic' ); ?>
                        <i class="tool-i"></i>
                    </div>
                </div>
			<?php } ?>

            <div class="room-features">
                <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4></div>
                <ul class="room-feature-list">

					<?php
					if ( ! empty( $room['features'] ) ) {
						foreach ( $room['features'] as $feature ) {

							$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );

							if ( ! empty( $room_f_meta['icon-type'] ) && $room_f_meta['icon-type'] == 'fa' ) {
								$room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
							} elseif ( ! empty( $room_f_meta['icon-type'] ) && $room_f_meta['icon-type'] == 'c' ) {
								$room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
							} else {
								$room_feature_icon = '<i class="fas fa-bread-slice"></i>';
							}


							$room_term = get_term( $feature );
							if ( ! empty( $room_term->name ) ) {
								?>
                                <li class="tf-tooltip">
									<?php echo wp_kses_post( $room_feature_icon ); ?>
                                    <div class="tf-top">
										<?php echo esc_html( $room_term->name ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </li>
							<?php }
						}
					} ?>
                </ul>
            </div>
        </td>
		<?php
		if ( $room_options && $pricing_by == '3' ):
			$option_price = 0;
			$option_adult_price = 0;
			$option_child_price = 0;
			foreach ( $room_options as $room_option_key => $room_option ):
                $option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
				$has_option  = [];

				if ( $avil_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
					if(!$multi_by_date_ck){
						if ( $tf_startdate && $tf_enddate ) {
							// Check availability by date option
							$period = new \DatePeriod(
								new \DateTime( $tf_startdate . ' 00:00' ),
								new \DateInterval( 'P1D' ),
								new \DateTime( $tf_enddate . ' 23:59' )
							);
						}
					}else{
						if ( $tf_startdate && $tf_enddate ) {
							$period = new \DatePeriod(
								new \DateTime( $tf_startdate . ' 00:00' ),
								new \DateInterval( 'P1D' ),
								new \DateTime( $tf_enddate . ' 00:00' )
							);
						}
					}

					// split date range
					$check_in  = strtotime( $form_start . ' 00:00' );
					$check_out = strtotime( $form_end . ' 00:00' );
					$price = $price_by_date = $d_price = $d_price_by_date = 0;

					// extract price from available room options
					foreach ( $period as $date ) {

						$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
							if( $date_availability['status'] == 'available' ){
								$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
								$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

								return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
							} else {
								return false;
							}
						} ) );

						if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {

							$d_price_by_date = 0;
							$data          = $available_rooms[0];
							$options_count = $data['options_count'] ?? 0;

							if($data[ 'tf_room_option_' . $room_option_key ] == ''){
								$has_option[] = 0;
							}

							if ( $data[ 'tf_room_option_' . $room_option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $room_option_key ] == 'per_room' ) {
								$room_price = $price_by_date = ! empty( $data[ 'tf_option_room_price_' . $room_option_key ] ) ? $data[ 'tf_option_room_price_' . $room_option_key ] : 0;

								$d_room_price = $d_price_by_date = !empty($room_price) ? Pricing::apply_discount($room_price, $hotel_discount_type, $hotel_discount_amount) : 0;
								$d_price += $d_room_price;
								$has_option[] = 1;
							} else if ( $data[ 'tf_room_option_' . $room_option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $room_option_key ] == 'per_person' ) {
								$adult_price = ! empty( $data[ 'tf_option_adult_price_' . $room_option_key ] ) ? $data[ 'tf_option_adult_price_' . $room_option_key ] : 0;
								$child_price = ! empty( $data[ 'tf_option_child_price_' . $room_option_key ] ) ? $data[ 'tf_option_child_price_' . $room_option_key ] : 0;
								$price_by_date = ( $adult_price * $form_adult ) + ( $child_price * $form_child );

								$d_adult_price = !empty($adult_price) ? Pricing::apply_discount($adult_price, $hotel_discount_type, $hotel_discount_amount) : 0;
								$d_child_price = !empty($child_price) ? Pricing::apply_discount($child_price, $hotel_discount_type, $hotel_discount_amount) : 0;
								$d_price_by_date = ( $d_adult_price * $form_adult ) + ( $d_child_price * $form_child );
								$d_price += $d_price_by_date;
								$has_option[] = 1;
							}
							$price 			+= $price_by_date;
						} else {
							$has_option[] = 0;
						}
					}

				} else{
					if ( $option_price_type === 'per_room' ) {
						$option_price = $price_by_date = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
					} elseif ( $option_price_type === 'per_person' ) {
						$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
						$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;

						$price_by_date = ( ( $option_adult_price * $form_adult ) + ( $option_child_price * $form_child ) );
					}

					if ( $option_price_type == 'per_room' ) {
						$d_room_price = $d_price_by_date = Pricing::apply_discount( $option_price, $hotel_discount_type, $hotel_discount_amount );
					} elseif ( $option_price_type == 'per_person' ) {
						$d_room_adult_price = Pricing::apply_discount( $option_adult_price, $hotel_discount_type, $hotel_discount_amount );
						$d_room_child_price = Pricing::apply_discount( $option_child_price, $hotel_discount_type, $hotel_discount_amount );

						$d_price_by_date = ( ( $d_room_adult_price * $form_adult ) + ( $d_room_child_price * $form_child ) );
					}

					$price   = $price_by_date * $days;
					$d_price = $d_price_by_date * $days;
				}

                Helper::tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit, $d_price );
				if ( ! in_array( 0, $has_option ) ) {
                ?>
                <td class="options">
                    <ul>
						<?php if ( ! empty( $room_option['room-facilities'] ) ) :
							$facility_price = 0;
							foreach ( $room_option['room-facilities'] as $room_facility ) :
								?>
                                <li>
                                    <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                    <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                                </li>
							<?php endforeach;
						endif; ?>
                    </ul>
                </td>
                <td class="pax">

					<?php if ( $adult_number ) { ?>
                        <div class="tf-tooltip tf-d-b">
                            <div class="room-detail-icon">
                                <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                                <span class="icon-text tf-d-b">x<?php echo esc_html( $adult_number ); ?></span>
                            </div>
                            <div class="tf-top">
								<?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                                <i class="tool-i"></i>
                            </div>
                        </div>
					<?php }
					if ( $child_number ) { ?>
                        <div class="tf-tooltip tf-d-b">
                            <div class="room-detail-icon">
                                <span class="room-icon-wrap"><i class="ri-user-smile-line"></i></span>
                                <span class="icon-text tf-d-b">x<?php echo esc_html( $child_number ); ?></span>
                            </div>
                            <div class="tf-top">
								<?php
								if ( ! empty( $child_age_limit ) ) {
									/* translators: %s: Child Age Limit */
									printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html( $child_age_limit ) );
								} else {
									esc_html_e( 'Number of Children', 'tourfic' );
								}
								?>
                                <i class="tool-i"></i>
                            </div>
                        </div>
					<?php } ?>
                </td>
                <td class="pricing">
                    <div class="tf-price-column">
						<?php
						if ( ! empty( $d_price ) ) {
							?>
                            <span class="tf-price"><del><?php echo wp_kses_post( wc_price( $price ) ); ?></del> <?php echo wp_kses_post( wc_price( $d_price ) ); ?></span>
							<?php
							$d_price = "";
						} else if ( $hotel_discount_type == "none" || empty( $d_price ) ) {
							?>
                            <span class="tf-price"><?php echo wp_kses_post( wc_price( $price ) ); ?></span>
							<?php
						}
						?>
                        <div class="price-per-night">
                            <?php
                            if ( $multi_by_date_ck ) {
                                /* translators: %s: Days */
                                $days > 0 ? printf( esc_html__( 'for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/night', 'tourfic' );
                            } else {
                                /* translators: %s: Days */
                                $days > 0 ? printf( esc_html__( 'for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/day', 'tourfic' );
                            }
                            ?>
                        </div>

						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>
                            <span class="tf-price tf-deposit-amount-<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?> tf-hotel-deposit-hide" style="display: none;"><?php echo wp_kses_post( wc_price( $deposit_amount ) ); ?></span>
                            <div class="price-per-night tf-deposit-amount-<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?> tf-hotel-deposit-hide" style="display: none;"><?php esc_html_e( 'Need to be deposited', 'tourfic' ) ?></div>
						<?php } ?>
                    </div>
                </td>
                <td class="reserve">
                    <form class="tf-room">
						<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>

                        <div class="room-selection-wrap">
                            <select name="hotel_room_selected" id="hotel-room-selected">
								<?php
								foreach ( range( 0, $num_room_available ) as $value ) {
									echo '<option>' . esc_html( $value ) . '</option>';
								}
								?>
                            </select>
                        </div>
                        <div class="room-submit-wrap">
                            <div class="roomselectissue"></div>
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>

                                <div class="room-deposit-wrap">
                                    <input type="checkbox" id="tf-make-deposit<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>" name="make_deposit" value="<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>">
                                    <label for="tf-make-deposit<?php echo esc_attr( $room_id.'_'.$room_option_key ) ?>"><?php esc_html_e( "I'll make a Partial Payment", "tourfic" ) ?></label><br>
                                </div>
							<?php } ?>

                            <input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>">
                            <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                            <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                            <input type="hidden" name="option_id" value="<?php echo $unique_id . '_' . $room_option_key; ?>">
                            <input type="hidden" name="location" value="<?php echo esc_attr( $first_location_name ); ?>">
                            <input type="hidden" name="adult" value="<?php echo esc_attr( $form_adult ); ?>">
                            <input type="hidden" name="child" value="<?php echo esc_attr( $form_child ); ?>">
                            <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>">
                            <input type="hidden" name="check_in_date" value="<?php echo esc_attr( $form_check_in ); ?>">
                            <input type="hidden" name="check_out_date" value="<?php echo esc_attr( $form_check_out ); ?>">
                            <input type="hidden" id="hotel_roomid">
                            <input type="hidden" id="hotel_room_number">
                            <input type="hidden" id="hotel_room_uniqueid">
                            <input type="hidden" id="hotel_room_depo" value="false">
							<?php
							$tour_hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
							$tour_hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';

							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tour_hotel_service_avail ) && ! empty( $tour_hotel_service_type ) && ( $room_book_by != 2 || empty( $room_book_url ) ) ) {
								?>
                                <a class="tf_air_service tf-sml-btn btn-styled" href="javascript:;" data-room="<?php echo esc_attr( $room_id ); ?>"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></a>

                                <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap tf-room" data-id="<?php echo esc_attr( $room_id ) ?>">
                                    <div class="tf-hotel-services">
                                        <div class="tf-hotel-services-text">
                                            <h3><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_title', esc_html__( 'Add Service to your Booking.', 'tourfic' ) ) ); ?></h3>
                                            <p><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_subtile', esc_html__( 'Select the services you want to add to your booking.', 'tourfic' ) ) ); ?></p>
                                        </div>
                                        <div class="tf-hotel-service">
                                            <label><?php esc_html_e( 'Pickup & Drop-off Service', 'tourfic' ); ?></label>
                                            <select id="airport-service" name="airport_service">
                                                <option value="none"><?php esc_html_e( 'No Service', 'tourfic' ); ?></option>
												<?php
												foreach ( $tour_hotel_service_type as $single_service_type ) { ?>
                                                    <option value="<?php echo esc_attr( $single_service_type ); ?>">
														<?php
														if ( "pickup" == $single_service_type ) {
															esc_html_e( 'Pickup Service', 'tourfic' );
														}
														if ( "dropoff" == $single_service_type ) {
															esc_html_e( 'Drop-off Service', 'tourfic' );
														}
														if ( "both" == $single_service_type ) {
															esc_html_e( 'Pickup & Drop-off Service', 'tourfic' );
														}
														?>
                                                    </option>
												<?php } ?>
                                            </select>
                                            <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                            <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                            <input type="hidden" name="option_id" value="<?php echo $unique_id . '_' . $room_option_key; ?>">
                                            <input type="hidden" id="hotel-post-id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                        </div>
                                        <div class="tf-airport-pickup-response"></div>
                                        <div class="tf_button_group">
                                            <button class="hotel-room-book btn-styled"
                                                    type="submit"><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_action', esc_html__( 'Continue to booking', 'tourfic' ) ) ); ?></button>
                                        </div>
                                    </div>
                                </div>

							<?php } else { ?>
                                <button class="hotel-room-book btn-styled tf-sml-btn tf-hotel-booking-popup-btn" type="submit"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></button>
							<?php } ?>
                        </div>
                        <div class="tf_desc"></div>
						<?php //tf_hotel_without_booking_popup( $hotel_id, $room_id, $form_adult, $form_child );
						?>
                    </form>
                </td>
                </tr>
				<?php } ?>

				<?php if ( $room_option_key < count( $room_options ) - 1 ) : ?>
                <tr>
			<?php endif;
			endforeach;
		else:
			?>
			<?php if ( $total_room_option_count > 0 ) : ?>
                <td class="options"></td>
            <?php endif; ?>
            <td class="pax">

				<?php if ( $adult_number ) { ?>
                    <div class="tf-tooltip tf-d-b">
                        <div class="room-detail-icon">
                            <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                            <span class="icon-text tf-d-b">x<?php echo esc_html( $adult_number ); ?></span>
                        </div>
                        <div class="tf-top">
							<?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                            <i class="tool-i"></i>
                        </div>
                    </div>
				<?php }
				if ( $child_number ) { ?>
                    <div class="tf-tooltip tf-d-b">
                        <div class="room-detail-icon">
                            <span class="room-icon-wrap"><i class="ri-user-smile-line"></i></span>
                            <span class="icon-text tf-d-b">x<?php echo esc_html( $child_number ); ?></span>
                        </div>
                        <div class="tf-top">
							<?php
							if ( ! empty( $child_age_limit ) ) {
								/* translators: %s: Child Age Limit */
								printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html( $child_age_limit ) );
							} else {
								esc_html_e( 'Number of Children', 'tourfic' );
							}
							?>
                            <i class="tool-i"></i>
                        </div>
                    </div>
				<?php } ?>
            </td>
            <td class="pricing">
                <div class="tf-price-column">
					<?php
					if ( ! empty( $d_price ) ) {
						?>
                        <span class="tf-price"><del><?php echo wp_kses_post( wc_price( $price ) ); ?></del> <?php echo wp_kses_post( wc_price( $d_price ) ); ?></span>
						<?php
						$d_price = "";
					} else if ( $hotel_discount_type == "none" || empty( $d_price ) ) {
						?>
                        <span class="tf-price"><?php echo wp_kses_post( wc_price( $price ) ); ?></span>
						<?php
					}
					?>
					<?php
					if ( $pricing_by == '1' ) { ?>
                        <div class="price-per-night">
							<?php
							if ( $multi_by_date_ck ) {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per night', 'tourfic' );
							} else {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per day', 'tourfic' );
							}
							?>
                        </div>
					<?php } else { ?>
                        <div class="price-per-night">
							<?php
							if ( $multi_by_date_ck ) {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s nights', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/night', 'tourfic' );
							} else {
								/* translators: %s: Days */
								$days > 0 ? printf( esc_html__( 'for %s days', 'tourfic' ), esc_html( $days ) ) : esc_html_e( 'per person/day', 'tourfic' );
							}
							?>
                        </div>
					<?php } ?>

					<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>
                        <span class="tf-price tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide" style="display: none;"><?php echo wp_kses_post( wc_price( $deposit_amount ) ); ?></span>
                        <div class="price-per-night tf-deposit-amount-<?php echo esc_attr( $room_id ) ?> tf-hotel-deposit-hide" style="display: none;"><?php esc_html_e( 'Need to be deposited', 'tourfic' ) ?></div>
					<?php } ?>
                </div>
            </td>
            <td class="reserve">
                <form class="tf-room">
					<?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' ); ?>

                    <div class="room-selection-wrap">
                        <select name="hotel_room_selected" id="hotel-room-selected">
							<?php
							foreach ( range( 0, $num_room_available ) as $value ) {
								echo '<option>' . esc_html( $value ) . '</option>';
							}
							?>
                        </select>
                    </div>
                    <div class="room-submit-wrap">
                        <div class="roomselectissue"></div>
						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) { ?>

                            <div class="room-deposit-wrap">
                                <input type="checkbox" id="tf-make-deposit<?php echo esc_attr( $room_id ) ?>" name="make_deposit" value="<?php echo esc_attr( $room_id ) ?>">
                                <label for="tf-make-deposit<?php echo esc_attr( $room_id ) ?>"><?php esc_html_e( "I'll make a Partial Payment", "tourfic" ) ?></label><br>
                            </div>
						<?php } ?>

                        <input type="hidden" name="post_id" value="<?php echo esc_attr( $hotel_id ); ?>">
                        <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                        <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                        <input type="hidden" name="location" value="<?php echo esc_attr( $first_location_name ); ?>">
                        <input type="hidden" name="adult" value="<?php echo esc_attr( $form_adult ); ?>">
                        <input type="hidden" name="child" value="<?php echo esc_attr( $form_child ); ?>">
                        <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>">
                        <input type="hidden" name="check_in_date" value="<?php echo esc_attr( $form_check_in ); ?>">
                        <input type="hidden" name="check_out_date" value="<?php echo esc_attr( $form_check_out ); ?>">
                        <input type="hidden" id="hotel_roomid">
                        <input type="hidden" id="hotel_room_number">
                        <input type="hidden" id="hotel_room_uniqueid">
                        <input type="hidden" id="hotel_room_depo" value="false">
						<?php
						$tour_hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
						$tour_hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';

						if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tour_hotel_service_avail ) && ! empty( $tour_hotel_service_type ) && ( $room_book_by != 2 || empty( $room_book_url ) ) ) {
							?>
                            <a class="tf_air_service tf-sml-btn btn-styled" href="javascript:;" data-room="<?php echo esc_attr( $room_id ); ?>"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></a>

                            <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap tf-room" data-id="<?php echo esc_attr( $room_id ) ?>">
                                <div class="tf-hotel-services">
                                    <div class="tf-hotel-services-text">
                                        <h3><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_title', esc_html__( 'Add Service to your Booking.', 'tourfic' ) ) ); ?></h3>
                                        <p><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_subtile', esc_html__( 'Select the services you want to add to your booking.', 'tourfic' ) ) ); ?></p>
                                    </div>
                                    <div class="tf-hotel-service">
                                        <label><?php esc_html_e( 'Pickup & Drop-off Service', 'tourfic' ); ?></label>
                                        <select id="airport-service" name="airport_service">
                                            <option value="none"><?php esc_html_e( 'No Service', 'tourfic' ); ?></option>
											<?php
											foreach ( $tour_hotel_service_type as $single_service_type ) { ?>
                                                <option value="<?php echo esc_attr( $single_service_type ); ?>">
													<?php
													if ( "pickup" == $single_service_type ) {
														esc_html_e( 'Pickup Service', 'tourfic' );
													}
													if ( "dropoff" == $single_service_type ) {
														esc_html_e( 'Drop-off Service', 'tourfic' );
													}
													if ( "both" == $single_service_type ) {
														esc_html_e( 'Pickup & Drop-off Service', 'tourfic' );
													}
													?>
                                                </option>
											<?php } ?>
                                        </select>
                                        <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>">
                                        <input type="hidden" name="unique_id" value="<?php echo esc_attr( $unique_id ); ?>">
                                        <input type="hidden" id="hotel-post-id" value="<?php echo esc_attr( $hotel_id ); ?>">
                                    </div>
                                    <div class="tf-airport-pickup-response"></div>
                                    <div class="tf_button_group">
                                        <button class="hotel-room-book btn-styled"
                                                type="submit"><?php echo esc_html( Helper::tfopt( 'hotel_service_popup_action', esc_html__( 'Continue to booking', 'tourfic' ) ) ); ?></button>
                                    </div>
                                </div>
                            </div>

						<?php } else { ?>
                            <button class="hotel-room-book btn-styled tf-sml-btn tf-hotel-booking-popup-btn" type="submit"><?php echo esc_html( $tf_hotel_reserve_button_text ); ?></button>
						<?php } ?>
                    </div>
                    <div class="tf_desc"></div>
					<?php //tf_hotel_without_booking_popup( $hotel_id, $room_id, $form_adult, $form_child );
					?>
                </form>
            </td>
            </tr>
		<?php
		endif;
	}
} ?>
