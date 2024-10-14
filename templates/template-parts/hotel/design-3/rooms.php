<?php

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Pricing;
use \Tourfic\Classes\Hotel\Hotel;

//getting only selected features for rooms
$rm_features = [];
if ( ! empty( $rooms ) ) {
	foreach ( $rooms as $_room ) {
		$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
		//merge for each room's selected features
		if ( ! empty( $room['features'] ) ) {
			$rm_features = array_unique( array_merge( $rm_features, $room['features'] ) );
		}
	}
}

$tf_booking_type        = '1';
$tf_booking_url         = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_ext_booking_code = '';
$tf_hide_external_price = "1";
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type        = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_booking_url         = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
	$tf_booking_query_url   = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
	$tf_booking_attribute   = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
	$tf_hide_booking_form   = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_hide_price          = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
	$tf_hide_external_price = ! empty( $meta["booking-by"] ) && $meta["booking-by"] == 2 ? ( ! empty( $meta["hide_external_price"] ) ? $meta["hide_external_price"] : true ) : true;
	$tf_ext_booking_type    = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
	$tf_ext_booking_code    = ! empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
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
$price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';
$feature_filter = ! empty( Helper::tfopt( 'feature-filter' ) ) ? Helper::tfopt( 'feature-filter' ) : false;
?>
<!--Available rooms start -->
<div class="tf-room-section tf-template-section" id="tf-hotel-rooms">
    <div class="tf-available-rooms-head">
        <h3 class="tf-section-title">
            <?php //echo sprintf( esc_html__( 'Total %s Room Types', 'tourfic' ), count( $rooms ) ) ?>
            <?php echo ! empty( $meta["room-section-title"] ) ? esc_html( $meta["room-section-title"] ) : ''; ?>
        </h3>

        <?php if ( ! empty( $rm_features ) && $feature_filter ): ?>
            <span class="tf-filter"><i class="ri-equalizer-line"></i></span>
        <?php endif; ?>
    </div>
	<?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>
    <!--Available rooms start -->
    <div class="tf-available-rooms tf-rooms" id="rooms">
        <!-- Loader Image -->
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
            </div>
        </div>
		<?php if ( $rooms ) : ?>
			<?php foreach ( $rooms as $_room ) {
				$room_id = $_room->ID;
				$room    = get_post_meta( $_room->ID, 'tf_room_opt', true );
				$enable  = ! empty( $room['enable'] ) ? $room['enable'] : '';
				if ( $enable == '1' ) {
					$footage         = ! empty( $room['footage'] ) ? $room['footage'] : '';
					$bed             = ! empty( $room['bed'] ) ? $room['bed'] : '';
					$adult_number    = ! empty( $room['adult'] ) ? $room['adult'] : '0';
					$child_number    = ! empty( $room['child'] ) ? $room['child'] : '0';
					$total_person    = $adult_number + $child_number;
					$pricing_by      = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
					$avil_by_date    = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;
					$multi_by_date   = ! empty( $room['price_multi_day'] ) ? $room['price_multi_day'] : false;
					$child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
					$room_options    = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

					// Hotel Room Discount Data
					$hotel_discount_type   = ! empty( $room["discount_hotel_type"] ) ? $room["discount_hotel_type"] : "none";
					$hotel_discount_amount = ! empty( $room["discount_hotel_price"] ) ? $room["discount_hotel_price"] : 0;
					$room_gallerys         = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
					$room_preview_img      = get_the_post_thumbnail_url( $room_id, 'full' );

					?>
                    <div class="tf-available-room">
                        <div class="tf-available-room-gallery">
                            <div class="tf-room-gallery <?php echo empty( $room_gallerys ) ? esc_attr( 'tf-no-room-gallery' ) : ''; ?>">
								<?php if ( ! empty( $hotel_discount_type ) && ! empty( $hotel_discount_amount ) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type ) ) { ?>
                                    <div class="tf-available-room-off">
										<?php echo ( "percent" == $hotel_discount_type ) ? esc_html( $hotel_discount_amount ) . '% off' : wp_kses_post( wc_price( $hotel_discount_amount ) . ' off' ); ?>
                                    </div>
								<?php } ?>
								<?php if ( ! empty( $room_preview_img ) ) : ?>
                                    <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
								<?php else: ?>
                                    <img src="<?php echo esc_url( TF_ASSETS_APP_URL . '/images/feature-default.jpg' ) ?>" alt="room-thumb"/>
								<?php endif; ?>
                            </div>

							<?php
							if ( ! empty( $room_gallerys ) ) {
								$tf_room_gallery_ids = explode( ',', $room_gallerys );

								foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
									$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
									if ( $key <= 3 ) { ?>
                                        <div class="tf-room-gallery">
                                            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php _e( "Room Image", "tourfic" ); ?>">

											<?php if ( $key == 3 ) { ?>
                                                <div class="tf-room-gallery-overlay">
                                                    <a href="#" class="tf-room-modal-btn" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $room_id : '' ?>"
                                                       data-hotel="<?php echo $post_id; ?>">
                                                        <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g id="content">
                                                                <path id="Rectangle 2111"
                                                                      d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                                                      stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                <path id="Rectangle 2109"
                                                                      d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                                                      stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                <path id="Vector"
                                                                      d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17"
                                                                      stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                                                                <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </g>
                                                        </svg>
                                                    </a>
                                                </div>
											<?php } ?>
                                        </div>
										<?php
									}
								}
								?>
							<?php } ?>
                        </div>
						<?php
						if ( $pricing_by == '3' && ! empty( $room_options ) ):
							echo '<div class="tf-available-room-contents">';
							echo '<h4 class="tf-hotel-room-title">' . esc_html( get_the_title( $room_id ) ) . wp_kses_post(Helper::edit_link($room_id)) . '</h4>';
							foreach ( $room_options as $room_option_key => $room_option ):
								?>
                                <div class="tf-available-room-content tf-room-options-content">
                                    <div class="tf-available-room-content-top">
                                        <div class="tf-available-room-content-left">
                                            <h4><?php echo esc_html( $room_option['option_title'] ); ?></h4>
                                            <ul class="tf-option-list">
		                                        <?php if ( ! empty( $room_option['room-facilities'] ) ) :
			                                        foreach ( $room_option['room-facilities'] as $room_facility ) :
				                                        ?>
                                                        <li>
                                                            <span class="room-extra-icon"><i class="<?php echo !empty($room_facility['room_facilities_icon']) ? esc_attr( $room_facility['room_facilities_icon'] ) : 'ri-arrow-right-double-line'; ?>"></i></span>
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
                                                <li>
                                                    <a href="#" class="tf-room-modal-btn" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                       data-hotel="<?php echo esc_attr( $post_id ); ?>">
				                                        <?php esc_html_e( "See more", "tourfic" ); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tf-available-room-content-right">
											<?php if ( $tf_hide_external_price ) : ?>
                                                <div class="tf-available-room-price">
													<?php Pricing::instance( get_the_ID(), $room_id )->get_per_price_html($room_option_key); ?>
                                                </div>
											<?php endif; ?>
                                            <a href="<?php echo $tf_booking_type == 2 ? ( ! empty( $tf_booking_url ) && $tf_ext_booking_type == 1 ? esc_url( $tf_booking_url ) : ( $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ? esc_url( "#tf-external-booking-embaded-form" ) : '' ) ) : esc_url( '#room-availability' ) ?>"
                                               class="availability"><?php $tf_booking_type == 2 ? ( ! empty( $tf_booking_url ) && ( $tf_hide_booking_form == 1 && $tf_ext_booking_type == 1 ) ? esc_html_e( 'Book Now', 'tourfic' ) : ( $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ? esc_html_e( "Book Now", "tourfic" ) : esc_html_e( "Check Availability", "tourfic" ) ) ) : esc_html_e( "Check Availability", "tourfic" ) ?></a>
                                        </div>
                                    </div>
									<?php if ( ! empty( $room['features'] ) ) : ?>
                                        <div class="tf-available-room-content-bottom">
                                            <span class="tf-room-features-title"><?php echo esc_html__( "Features", "tourfic" ); ?></span>
                                            <ul class="tf-room-features-list">
												<?php
												$tf_room_fec_key = 1;
												foreach ( $room['features'] as $feature ) {
													$room_feature_icon = '<i class="ri-checkbox-circle-line"></i>';
													if ( $tf_room_fec_key < 9 ) {
														$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
														if ( ! empty( $room_f_meta ) ) {
															$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';

															if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
																$room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
															} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
																$room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
															}
														}

														$room_term = get_term( $feature ); ?>
                                                        <li>
															<?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
															<?php echo ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
                                                        </li>
													<?php }
													$tf_room_fec_key ++;
												}

												if ( count( $room['features'] ) >= 9 ) { ?>
                                                    <li><a href="#" class="tf-room-modal-btn" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                           data-hotel="<?php echo esc_attr( $post_id ); ?>"><?php esc_html_e( "See all features", "tourfic" ); ?></a></li>
												<?php } ?>
                                            </ul>
                                        </div>
									<?php endif; ?>
                                </div>
							<?php
							endforeach;
							echo '</div>';
						else:
							?>
                            <div class="tf-available-room-content">
                                <div class="tf-available-room-content-top">
                                    <div class="tf-available-room-content-left">
                                        <h4 class="tf-hotel-room-title">
                                            <?php echo esc_html( get_the_title( $room_id ) ); ?>
                                            <?php echo wp_kses_post(Helper::edit_link($room_id)) ?>
                                        </h4>
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
                                            <li>
                                                <a href="#" class="tf-room-modal-btn" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                   data-hotel="<?php echo esc_attr( $post_id ); ?>">
					                                <?php esc_html_e( "See more", "tourfic" ); ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tf-available-room-content-right">
		                                <?php if ( $tf_hide_external_price ) : ?>
                                            <div class="tf-available-room-price">
				                                <?php Pricing::instance( get_the_ID(), $room_id )->get_per_price_html(); ?>
                                            </div>
		                                <?php endif; ?>
                                        <a href="<?php echo $tf_booking_type == 2 ? ( ! empty( $tf_booking_url ) && $tf_ext_booking_type == 1 ? esc_url( $tf_booking_url ) : ( $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ? esc_url( "#tf-external-booking-embaded-form" ) : '' ) ) : esc_url( '#room-availability' ) ?>"
                                           class="availability"><?php $tf_booking_type == 2 ? ( ! empty( $tf_booking_url ) && ( $tf_hide_booking_form == 1 && $tf_ext_booking_type == 1 ) ? esc_html_e( 'Book Now', 'tourfic' ) : ( $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ? esc_html_e( "Book Now", "tourfic" ) : esc_html_e( "Check Availability", "tourfic" ) ) ) : esc_html_e( "Check Availability", "tourfic" ) ?></a>
                                    </div>
                                </div>
                                <?php if ( ! empty( $room['features'] ) ) : ?>
                                    <div class="tf-available-room-content-bottom">
                                        <span class="tf-room-features-title"><?php echo esc_html__( "Features", "tourfic" ); ?></span>
                                        <ul class="tf-room-features-list">
                                            <?php
                                            $tf_room_fec_key = 1;
                                            foreach ( $room['features'] as $feature ) {
	                                            $room_feature_icon = '<i class="ri-checkbox-circle-line"></i>';
                                                if ( $tf_room_fec_key < 9 ) {
                                                    $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                                    if ( ! empty( $room_f_meta ) ) {
                                                        $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';

                                                        if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
                                                            $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                                        } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
                                                            $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                                        }
                                                    }

                                                    $room_term = get_term( $feature ); ?>
                                                    <li>
                                                        <?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                        <?php echo ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
                                                    </li>
                                                <?php }
                                                $tf_room_fec_key ++;
                                            }

                                            if ( count( $room['features'] ) >= 9 ) { ?>
                                                <li><a href="#" class="tf-room-modal-btn" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                       data-hotel="<?php echo esc_attr( $post_id ); ?>"><?php esc_html_e( "See all features", "tourfic" ); ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
						<?php endif; ?>
                    </div>
				<?php }
			} ?>
		<?php endif; ?>

    </div>
    <!-- Available rooms end -->

</div>
<!-- Available rooms end -->