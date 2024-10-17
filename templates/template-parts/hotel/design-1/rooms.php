<?php
use \Tourfic\Classes\Hotel\Pricing;
use \Tourfic\Classes\Helper;
if ( $rooms ) :
	$price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';

//getting only selected features for rooms
	$rm_features = [];
	foreach ( $rooms as $_room ) {
		$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
		//merge for each room's selected features
		if ( ! empty( $room['features'] ) ) {
			$rm_features = array_unique( array_merge( $rm_features, $room['features'] ) );
		}
	}

	$tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_ext_booking_code = '';
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
		$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
		$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
		$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
		$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
		$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
		$tf_ext_booking_type  = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
		$tf_ext_booking_code  = ! empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
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

	$total_room_option_count = Tourfic\Classes\Room\Room::get_room_options_count($rooms);
	?>

    <div class="tf-rooms-sections tf-mb-50 tf-template-section">
        <h2 class="section-heading tf-section-title"><?php echo ! empty( $meta['room-section-title'] ) ? esc_html( $meta['room-section-title'] ) : ''; ?></h2>
		<?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>

        <div class="tf-rooms" id="rooms">
            <!-- Loader Image -->
            <div id="tour_room_details_loader">
                <div id="tour-room-details-loader-img">
                    <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                </div>
            </div>

            <!-- Room Table -->
            <table class="tf-availability-table" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th class="description" colspan="4"><?php esc_html_e( 'Room Details', 'tourfic' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <!-- Start Single Room -->
				<?php foreach ( $rooms as $_room ) {
					$room_id = $_room->ID;
					$room    = get_post_meta( $room_id, 'tf_room_opt', true );
					$enable  = ! empty( $room['enable'] ) ? $room['enable'] : '';
					if ( $enable == '1' ) {
						$footage                 = ! empty( $room['footage'] ) ? $room['footage'] : '';
						$bed                     = ! empty( $room['bed'] ) ? $room['bed'] : '';
						$adult_number            = ! empty( $room['adult'] ) ? $room['adult'] : '0';
						$child_number            = ! empty( $room['child'] ) ? $room['child'] : '0';
						$total_person            = $adult_number + $child_number;
						$pricing_by              = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
						$avil_by_date            = ! empty( $room['avil_by_date'] ) ? ! empty( $room['avil_by_date'] ) : false;
						$multi_by_date           = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
						$child_age_limit         = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
						$room_options            = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

						// Hotel Room Discount Data
						$hotel_discount_type   = ! empty( $room["discount_hotel_type"] ) ? $room["discount_hotel_type"] : "none";
						$hotel_discount_amount = ! empty( $room["discount_hotel_price"] ) ? $room["discount_hotel_price"] : 0;
                        ?>
                        <tr>
                        <td class="description" rowspan="<?php echo ( $pricing_by == '3' && ! empty( $room_options ) ) ? count( $room_options ) : 1; ?>">
                            <div class="tf-room-description-box tf-flex">
								<?php
								$tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
								if ( $tour_room_details_gall ) {
									$tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
								}
								?>

								<?php
								$room_preview_img = get_the_post_thumbnail_url( $room_id, 'full' );
								if ( ! empty( $room_preview_img ) ) { ?>
                                    <div class="tf-room-preview-img">
										<?php
										if ( $tour_room_details_gall ) {
											?>
                                            <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                               data-hotel="<?php echo esc_attr( $post_id ); ?>">
                                                <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                                <!-- <span><?php //esc_html_e("Best Offer", "tourfic");
												?></span> -->
                                            </a>
											<?php
										} else { ?>
                                            <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                            <!-- <span><?php //esc_html_e("Best Offer", "tourfic");
											?></span> -->
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
                                                       data-hotel="<?php echo esc_attr( $post_id ); ?>"><?php echo esc_html( get_the_title( $room_id ) ); ?></a>
                                                </h3>
												<?php
											} else { ?>
                                                <h3><?php echo esc_html( get_the_title( $room_id ) ); ?></h3>
											<?php } ?>
                                        </div>
										<?php if ( ! empty( get_post_field( 'post_content', $room_id ) ) ) : ?>
                                            <div class="bed-facilities">
                                                <p>
													<?php echo wp_kses_post( substr( wp_strip_all_tags( get_post_field( 'post_content', $room_id ) ), 0, 120 ) . '...' ); ?>
                                                </p>
                                            </div>
										<?php endif; ?>
                                    </div>
                                    <ul>
										<?php if ( $footage ) { ?>
                                            <li><i class="fas fa-ruler-combined"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
										<?php } ?>
										<?php if ( $bed ) { ?>
                                            <li><i class="fas fa-bed"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Number of Beds', 'tourfic' ); ?></li>
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
														<?php echo isset( $room_term->name ) && ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
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
                                           data-hotel="<?php echo esc_attr( $post_id ); ?>" style="text-decoration: underline;">
											<?php esc_html_e( "Room Photos & Details", "tourfic" ); ?>
                                        </a>

                                        <div id="tour_room_details_qv" class=" <?php echo $tf_hotel_selected_template == "design-1" ? "tf-hotel-design-1-popup" : ""; ?>">

                                        </div>
									<?php } ?>
                                </div>
                            </div>
                        </td>

						<?php
						if ( $pricing_by == '3' && ! empty( $room_options ) ):
							foreach ( $room_options as $room_option_key => $room_option ):
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
                                    <div style="text-align:center; width: 100%;"><?php echo __( "Pax:", "tourfic" ); ?></div>
									<?php if ( $adult_number ) { ?>
                                        <div class="tf-tooltip tf-d-b">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap">
                                                    <i class="fas fa-male"></i>
                                                    <i class="fas fa-female"></i>
                                                </span>
                                                <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                                            </div>
                                            <div class="tf-top">
												<?php _e( 'Number of Adults', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
									<?php }
									if ( $child_number ) { ?>
                                        <div class="tf-tooltip tf-d-b">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                                <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                                            </div>
                                            <div class="tf-top">
												<?php
												if ( ! empty( $child_age_limit ) ) {
													printf( __( 'Children Age Limit %s Years', 'tourfic' ), $child_age_limit );
												} else {
													_e( 'Number of Children', 'tourfic' );
												}
												?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
									<?php } ?>
                                </td>
                                <td class="reserve tf-t-c">
									<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) {
										Pricing::instance( get_the_ID(), $_room->ID )->get_per_price_html( $room_option_key );
									} ?>
									<?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) ): ?>
                                        <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf-btn-normal btn-secondary" target="_blank">
											<?php esc_html_e( 'Book Now', 'tourfic' ); ?>
                                        </a>
									<?php else: ?>
                                        <button class="hotel-room-availability tf-btn-normal btn-secondary" type="submit" style="margin: 0 auto;">
											<?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                        </button>
									<?php endif; ?>
                                </td>
                                </tr>

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
                                <div style="text-align:center; width: 100%;"><?php echo __( "Pax:", "tourfic" ); ?></div>
								<?php if ( $adult_number ) { ?>
                                    <div class="tf-tooltip tf-d-b">
                                        <div class="room-detail-icon">
                                            <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                                            <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                                        </div>
                                        <div class="tf-top">
											<?php _e( 'Number of Adults', 'tourfic' ); ?>
                                            <i class="tool-i"></i>
                                        </div>
                                    </div>
								<?php }
								if ( $child_number ) { ?>
                                    <div class="tf-tooltip tf-d-b">
                                        <div class="room-detail-icon">
                                            <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                            <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                                        </div>
                                        <div class="tf-top">
											<?php
											if ( ! empty( $child_age_limit ) ) {
												printf( __( 'Children Age Limit %s Years', 'tourfic' ), $child_age_limit );
											} else {
												_e( 'Number of Children', 'tourfic' );
											}
											?>
                                            <i class="tool-i"></i>
                                        </div>
                                    </div>
								<?php } ?>
                            </td>
                            <td class="reserve tf-t-c">
								<?php
								if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) {
                                    Pricing::instance(get_the_ID(), $room_id)->get_per_price_html();
								}
								?>
								<?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) && $tf_ext_booking_type == 1 ): ?>
                                    <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf-btn-normal btn-secondary" target="_blank">
										<?php esc_html_e( 'Book Now', 'tourfic' ); ?>
                                    </a>
								<?php elseif ( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ): ?>
                                    <a href="<?php echo esc_url( "#tf-external-booking-embaded-form" ); ?>" class="tf-btn-normal btn-secondary" target="_blank">
										<?php esc_html_e( 'Book Now', 'tourfic' ); ?>
                                    </a>
								<?php else: ?>
                                    <button class="hotel-room-availability tf-btn-normal btn-secondary" type="submit" style="margin: 0 auto;">
										<?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                    </button>
								<?php endif; ?>
                            </td>
                            </tr>
						<?php
						endif;
					}
				}
				?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<!-- End Room Section -->