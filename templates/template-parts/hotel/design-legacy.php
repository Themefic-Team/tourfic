<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Hotel\Pricing;
use \Tourfic\Classes\Hotel\Hotel;

$tf_booking_type = '1';
$tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_ext_booking_code = '';
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
	$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
	$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
	$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
    $tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
    $tf_ext_booking_code = !empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
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
$price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';
?>
<div class="tf-single-template__legacy">
	<?php do_action( 'tf_before_container' ); ?>

    <!-- Start title area -->
    <div class="tf-title-area tf-hotel-title sp-b-20">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <span class="post-type"><?php esc_html_e( 'Hotel', 'tourfic' ) ?></span>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Address::render(); ?>
                </div>

                <div class="tf-title-right">
					<?php \Tourfic\App\Templates\Components\Global\Single\Wishlist::render(['icon_type' => 'simple']); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Share::render(['share_style' => 'style3']); ?>

                    <div class="reserve-button">
                        <a href="#rooms" class="tf-btn-flip" data-back="<?php esc_attr_e( 'View Rooms', 'tourfic' ); ?>" data-front="<?php esc_attr_e( 'Reserve Now', 'tourfic' ); ?>"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End title area -->

    <!-- Hero Start -->
    <div class="hero-section">
        <div class="tf-container">
            <div class="hero-section-wrap">
                <div class="hero-left">
                    <?php 
                        \Tourfic\App\Templates\Components\Global\Single\Gallery::render(['gallery_style' => 'style2']); 
                        
                        \Tourfic\App\Templates\Components\Global\Single\Description::render([
                            'limit_content' => 'no',
                            'wrapper_open' => '<div class="tf-mt-16">',
                            'wrapper_close' => '</div>'
                        ]); 
                        
                        \Tourfic\App\Templates\Components\Global\Single\Feature::render([
                            'wrapper_open' => '<div class="tf-pt-16 tf-pb-30">', 
                            'wrapper_close' => '</div>'
                        ]); 
                    ?>
                </div>
                <div class="hero-right">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Map::render(['show_icon' => 'no', 'design' => 'design-2']); ?>
					
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ||  $tf_booking_type == 3 ) : ?>
                        <div class="tf-hero-booking">
							<?php Hotel::tf_hotel_sidebar_booking_form(); ?>
                        </div>
					<?php endif; ?>
                    <?php if( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && !empty( $tf_ext_booking_code )) : ?>
                        <div id="tf-external-booking-embaded-form" class="tf-hero-booking">
                            <?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ) ?>
                        </div>
                    <?php endif; ?>
					<?php
					$places_section_title = ! empty( $meta["section-title"] ) ? $meta["section-title"] : __( "What's around?", 'tourfic' );
					$places_meta          = ! empty( $meta["nearby-places"] ) ? Helper::tf_data_types($meta["nearby-places"]) : array();
					?>
					<?php if ( count( $places_meta ) > 0 ) : ?> <!-- nearby places - start -->
                        <div class="nearby-container">
                            <div class="nearby-container-inner">
								<?php if ( ! empty( $places_section_title ) ): ?>
                                    <h3 class="section-heading"><?php echo esc_html( $places_section_title ); ?></h3>
								<?php endif; ?>
                                <ul>
									<?php foreach ( $places_meta as $place ) {
										$place_icon = '<i class="' . $place['place-icon'] . '"></i>';
										?>
                                        <li>
                                            <span>
                                                <?php echo wp_kses_post( $place_icon ); ?><?php echo esc_html( $place["place-title"] ); ?>
                                            </span>
                                            <span>
                                                <?php echo esc_html( $place["place-dist"] ); ?>
                                            </span>
                                        </li>
									<?php }; ?>
                                </ul>
                            </div>
                        </div>
					<?php endif; ?> <!-- nearby places - end -->

                    <!-- Hotel Single Widget Hook are - start -->
                    <div class="tf-hotel-single-custom-widget-wrap tf-single-widgets">
						<?php do_action( "tf_hotel_single_widgets" ); ?>
                        <?php do_action( "tf_single_hotel_sidebar_area_with_args", $post_id ); ?>
                    </div>
                    <!-- Hotel Single Widget Hook are - end -->
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>

	<?php if ( $rooms ) :

		//getting only selected features for rooms
		$rm_features = [];
		foreach ( $rooms as $_room ) {
			$room = get_post_meta($_room->ID, 'tf_room_opt', true);
			//merge for each room's selected features
			if ( ! empty( $room['features'] ) ) {
				$rm_features = array_unique( array_merge( $rm_features, $room['features'] ) );
			}
		}
		?>
        <!-- Start Room Section -->
        <div class="tf-room-section sp-50">
            <div class="tf-container">
                <h2 class="section-heading"><?php echo ! empty( $meta['room-section-title'] ) ? esc_html( $meta['room-section-title'] ) : ''; ?></h2>
                <!-- Hooked in feature filter action -->
				<?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>
                <div class="tf-room-type" id="rooms">
                    <div class="tf-room-table hotel-room-wrap">
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                            </div>
                        </div>
                        <table class="availability-table" cellpadding="0" cellspacing="0">
                            <thead>
                            <tr>
                                <th class="description"><?php esc_html_e( 'Room Details', 'tourfic' ); ?></th>
	                            <?php if ( $total_room_option_count > 0 ) : ?>
                                    <th class="options"><?php esc_html_e( 'Options', 'tourfic' ); ?></th>
                                <?php endif; ?>
                                <th class="pax"><?php esc_html_e( 'Pax', 'tourfic' ); ?></th>
								<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                                    <th class="pricing"><?php esc_html_e( 'Price', 'tourfic' ); ?></th>
								<?php endif; ?>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Start Single Room -->
							<?php foreach ( $rooms as $_room ) {
								$room = get_post_meta($_room->ID, 'tf_room_opt', true);
								$enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
								if ( $enable == '1' ) {
									$unique_id         = ! empty( $room['unique_id'] ) ? $room['unique_id'] : '';
									$footage         = ! empty( $room['footage'] ) ? $room['footage'] : '';
									$bed             = ! empty( $room['bed'] ) ? $room['bed'] : '';
									$adult_number    = ! empty( $room['adult'] ) ? $room['adult'] : '0';
									$child_number    = ! empty( $room['child'] ) ? $room['child'] : '0';
									$total_person    = $adult_number + $child_number;
									$pricing_by      = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
									$avil_by_date    = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;
									$multi_by_date   = ! empty( $room['price_multi_day'] ) ?  $room['price_multi_day'] : false;
									$child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
									$room_options    = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

									// Hotel Room Discount Data
									$hotel_discount_type   = ! empty( $room["discount_hotel_type"] ) ? $room["discount_hotel_type"] : "none";
									$hotel_discount_amount = ! empty( $room["discount_hotel_price"] ) ? $room["discount_hotel_price"] : 0;

									?>
                                    <tr>
                                        <td class="description" rowspan="<?php echo $room_options ? count( $room_options ) : 1; ?>">
                                            <div class="tf-room-type">
                                                <div class="tf-room-title">
													<?php
													$tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
													if ( $tour_room_details_gall ) {
														$tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
													}
													if ( $tour_room_details_gall ){
														?>
                                                        <h3><a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $_room->ID ) : '' ?>"
                                                               data-hotel="<?php echo esc_attr( $post_id ); ?>">
																<?php echo esc_html( get_the_title($_room->ID) ); ?>
                                                            </a></h3>

                                                        <div id="tour_room_details_qv" class="">

                                                        </div>
													<?php } else{ ?>
                                                    <h3><?php echo esc_html( get_the_title($_room->ID) ); ?><h3>
                                                            <?php } ?>
                                                </div>
                                                <div class="bed-facilities"><p><?php echo wp_kses_post( get_post_field('post_content', $_room->ID) ); ?></p></div>
                                            </div>

											<?php if ( $footage ) { ?>
                                                <div class="tf-tooltip tf-d-ib">
                                                    <div class="room-detail-icon">
                                            <span class="room-icon-wrap"><i
                                                        class="fas fa-ruler-combined"></i></span>
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
                                                        <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo esc_html( $bed ); ?></span>
                                                    </div>
                                                    <div class="tf-top">
														<?php esc_html_e( 'Number of Beds', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
											<?php } ?>

											<?php if ( ! empty( $room['features'] ) ) { ?>
                                                <div class="room-features">
                                                    <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
                                                    </div>
                                                    <ul class="room-feature-list">
														<?php
														foreach ( $room['features'] as $feature ) {

															$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
															if ( ! empty( $room_f_meta ) ) {
																$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
															}
															if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' ) {
																$room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
															} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' ) {
																$room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
															} else {
																$room_feature_icon = '<i class="fas fa-bread-slice"></i>';
															}

															$room_term = get_term( $feature );
															if ( ! empty( $room_term->name ) ) {
																?>
                                                                <li class="tf-tooltip">
																	<?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                                    <div class="tf-top">
																		<?php echo esc_html( $room_term->name ); ?>
                                                                        <i class="tool-i"></i>
                                                                    </div>
                                                                </li>
															<?php }
														} ?>
                                                    </ul>
                                                </div>
											<?php } ?>
                                        </td>
									<?php
									if ( $pricing_by == '3' && !empty($room_options) ):
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
												<?php if ( $adult_number ) { ?>
                                                    <div class="tf-tooltip tf-d-b">
                                                        <div class="room-detail-icon">
                                                            <span class="room-icon-wrap">
                                                                <i class="fas fa-male"></i>
                                                                <i class="fas fa-female"></i>
                                                            </span>
                                                            <span class="icon-text tf-d-b">x<?php echo esc_html($adult_number); ?></span>
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
                                                            <span class="icon-text tf-d-b">x<?php echo esc_html($child_number); ?></span>
                                                        </div>
                                                        <div class="tf-top">
															<?php
															if ( ! empty( $child_age_limit ) ) {
                                                                /* translators: Children age limit */
																printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html($child_age_limit) );
															} else {
																esc_html_e( 'Number of Children', 'tourfic' );
															}
															?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </div>
												<?php } ?>
                                            </td>
											<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                                                <td class="pricing">
                                                    <div class="tf-price-column">
	                                                    <?php Pricing::instance(get_the_ID(), $_room->ID)->get_per_price_html($room_option_key); ?>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                            <td class="reserve tf-t-c">
                                                <div class="tf-btn-wrap">
													<?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) ): ?>
                                                        <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf_btn tf_btn_full" target="_blank">
															<?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                        </a>
													<?php else: ?>
                                                        <button class="tf_btn tf_btn_full hotel-room-availability" type="submit">
															<?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                                        </button>
													<?php endif; ?>
                                                </div>
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
											<?php if ( $adult_number ) { ?>
                                                <div class="tf-tooltip tf-d-b">
                                                    <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                                                            class="fas fa-female"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo esc_html($adult_number); ?></span>
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
                                                        <span class="icon-text tf-d-b">x<?php echo esc_html($child_number); ?></span>
                                                    </div>
                                                    <div class="tf-top">
														<?php
														if ( ! empty( $child_age_limit ) ) {
                                                            /* translators: Children age limit */
															printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html($child_age_limit) );
														} else {
															esc_html_e( 'Number of Children', 'tourfic' );
														}
														?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
											<?php } ?>
                                        </td>
										<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                                        <td class="pricing">
                                            <div class="tf-price-column">
												<?php Pricing::instance(get_the_ID(), $_room->ID)->get_per_price_html(); ?>
                                            </div>
                                        </td>
									<?php endif; ?>
                                        <td class="reserve tf-t-c">
                                            <div class="tf-btn-wrap">
												<?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) && $tf_ext_booking_type == 1 ): ?>
                                                    <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf_btn tf_btn_full" target="_blank">
														<?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                    </a>
												<?php elseif( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && !empty( $tf_ext_booking_code ) ): ?>
                                                    <a href="<?php echo esc_url( "#tf-external-booking-embaded-form" ); ?>" class="tf_btn tf_btn_full" target="_blank">
														<?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                    </a>
												<?php else: ?>
                                                    <button class="tf_btn tf_btn_full hotel-room-availability" type="submit">
														<?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                                    </button>
												<?php endif; ?>
                                            </div>
                                        </td>
                                        </tr>
									<?php endif;
								}
							}
							?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Room Section -->
	<?php endif; ?>

    <?php \Tourfic\App\Templates\Components\Global\Single\Amenities::render(['amenities_style' => 'style2', 'container' => 'yes']); ?>

    <!-- FAQ section Start -->
	<?php if ( $faqs ): ?>
        <div class="tf-hotel-faqs-section sp-50 tf-template-section">
            <div class="tf-container">
                <h2 class="section-heading tf-section-title"><?php echo ! empty( $meta['faq-section-title'] ) ? esc_html( $meta['faq-section-title'] ) : ''; ?></h2>
                <div class="tf-section-flex tf-flex">
					<?php
					$tf_enquiry_section_status = ! empty( $meta['h-enquiry-section'] ) ? $meta['h-enquiry-section'] : "";
					$tf_enquiry_section_icon   = ! empty( $meta['h-enquiry-option-icon'] ) ? esc_html( $meta['h-enquiry-option-icon'] ) : '';
					$tf_enquiry_section_title  = ! empty( $meta['h-enquiry-option-title'] ) ? esc_html( $meta['h-enquiry-option-title'] ) : '';
					$tf_enquiry_section_des    = ! empty( $meta['h-enquiry-option-content'] ) ? esc_html( $meta['h-enquiry-option-content'] ) : '';
					$tf_enquiry_section_button = ! empty( $meta['h-enquiry-option-btn'] ) ? esc_html( $meta['h-enquiry-option-btn'] ) : '';

					if ( ! empty( $tf_enquiry_section_status ) && ( ! empty( $tf_enquiry_section_icon ) || ! empty( $tf_enquiry_section_title ) || ! empty( $enquery_button_text ) ) ) {
						?>
                        <div class="tf-hotel-enquiry">
                            <div class="tf-ask-enquiry">
                                <div class="default-enquiry-title-section">
                                    <?php
                                    if ( ! empty( $tf_enquiry_section_icon ) ) {
                                        ?>
                                        <i class="<?php echo esc_attr( $tf_enquiry_section_icon ); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                    if ( ! empty( $tf_enquiry_section_title ) ) {
                                        ?>
                                        <h3><?php echo esc_html( $tf_enquiry_section_title ); ?></h3>
                                        <?php
                                    }
                                    ?>

                                </div>
                                <?php
                                if ( ! empty( $tf_enquiry_section_des ) ) {
                                    ?>
                                    <p><?php echo wp_kses_post( $tf_enquiry_section_des ); ?></p>
                                    <?php
                                }
                                if ( ! empty( $tf_enquiry_section_button ) ) {
                                    ?>
                                    <div class="tf-btn-wrap"><a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn tf_btn_full"><span><?php echo esc_html( $tf_enquiry_section_button ); ?></span></a></div>
                                    <?php
                                }
                                ?>

                            </div>
                        </div>

					<?php } ?>
                    <div class="tf-faq-items-wrapper">
						<?php foreach ( $faqs as $key => $faq ): ?>
                            <div id="tf-faq-item">
                                <div class="tf-faq-title">
                                    <h4><?php echo esc_html( $faq['title'] ); ?></h4>
                                    <i class="fas fa-angle-down arrow"></i>
                                </div>
                                <div class="tf-faq-desc">
                                    <p><?php echo wp_kses_post( $faq['description'] ); ?></p>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>
    <!-- FAQ section end -->

    <?php if ( ! defined( 'TF_PRO' ) && ( $address ) ) { ?>
        <div class="map-for-mobile">
            <div class="show-on-map">
                <div class="tf-container">
                    <div class="tf-btn-wrap">
                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>" target="_blank" class="tf_btn tf_btn_full">
                            <span>
                                <i class="fas fa-map-marker-alt"></i>
                                <?php esc_html_e( 'Show on map', 'tourfic' ); ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || ( ! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
        <div class="popupmap-for-mobile">
            <div class="tf-container">
                <?php
                if ( $tf_openstreet_map != "default" ) { ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="150"
                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>">
                            <div class="tf-btn-wrap">
                                <span class="tf_btn tf_btn_secondary tf_btn_full"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                            </div>
                        </a>

                    </div>
                <?php } ?>
                <?php if ( $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) { ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <div id="mobile-hotel-location" style="height: 130px;"></div>

                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>">
                            <div class="tf-btn-wrap">
                                <span class="tf_btn tf_btn_secondary tf_btn_full"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                            </div>
                        </a>
                    </div>
                <?php } ?>
                <?php if ( $tf_openstreet_map == "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ) { ?>

                    <div class="tf-hotel-location-preview show-on-map">

                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="150" style="border:0;"
                                allowfullscreen="" loading="lazy"></iframe>

                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>">
                            <div class="tf-btn-wrap">
                                <span class="tf_btn tf_btn_secondary tf_btn_full"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                            </div>
                        </a>

                    </div>

                <?php } ?>
                <div style="display: none;" id="tf-hotel-google-maps">
                    <div class="tf-hotel-google-maps-container">
                        <?php
                        if ( ! empty( $address ) ) {
                            ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=15&output=embed" width="100%" height="550" style="border:0;"
                                    allowfullscreen="" loading="lazy"></iframe>
                        <?php } else { ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=15&output=embed" width="100%"
                                    height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Start Review Section -->
	<?php if ( ! $disable_review_sec == 1 ) { ?>
        <div id="tf-review" class="review-section sp-50">
            <div class="tf-container">
                <div class="reviews">
                    <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<?php comments_template(); ?>
                </div>
            </div>
        </div>
	<?php } ?>
    <!-- End Review Section -->

    
	<?php
    \Tourfic\App\Templates\Components\Global\Single\Terms_And_Conditions::render(
        [
            'wrapper_open' => '<div class="toc-section sp-50"><div class="tf-container">',
            'wrapper_close' => '</div></div>',
        ]
    );
	?>

	<?php do_action( 'tf_after_container' ); ?>
</div>