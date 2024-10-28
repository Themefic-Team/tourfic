<?php

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Tour\Tour_Price;

$tf_booking_type = '1';
$tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = '';
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
?>
<div class="tf-single-page tf-template-global tf-tour-design-1">
    <div class="tf-tour-single">
        <div class="tf-template-container">
            <div class="tf-container-inner">
                <!-- Single Tour Heading Section start -->
                <div class="tf-section tf-single-head">
                    <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                        <div class="tf-head-title">
                            <h1><?php the_title(); ?></h1>
                            <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                                <?php if ( !empty($location) ) { ?>
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?php
									echo '<a href="#tf-tour-map">' . wp_kses_post($location) . '.</a>';
								 }; ?>
                            </div>
                        </div>
                        <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
							<?php
							// Wishlist
                            if($disable_wishlist_tour==0){
                                
                                if ( is_user_logged_in() ) {
                                if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) { ?>
                                    <div class="tf-icon tf-wishlist-box">
                                        <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                            data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                            echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                                        } ?>></i>
                                    </div>
                                <?php }
								} else {
								if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) { ?>
                                    <div class="tf-icon tf-wishlist-box">
                                        <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                            data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>"
                                            data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                            echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                                        } ?>></i>
                                    </div>
                                <?php } } ?>
                            <?php }else{
							if ( Helper::tfopt( 'wl-bt-for' ) && in_array( '2', Helper::tfopt( 'wl-bt-for' ) ) ) {
								if ( is_user_logged_in() ) {
									if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) {
										?>
                                        <div class="tf-icon tf-wishlist-box">
                                            <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                               data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
												echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
											} ?>></i>
                                        </div>
									<?php }
								} else {
									if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) {
										?>
                                        <div class="tf-icon tf-wishlist-box">
                                            <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                               data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>"
                                               data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
												echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
											} ?>></i>
                                        </div>
									<?php }
								}
							}} ?>

                            <!-- Share Section -->
							<?php if ( ! $disable_share_opt == '1' ) { ?>
                                <div class="tf-share">
                                    <a href="#dropdown-share-center" class="share-toggle tf-icon tf-social-box"
                                       data-toggle="true">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </a>
                                    <div id="dropdown-share-center" class="share-tour-content">
                                        <div class="tf-dropdown-share-content">
                                            <h4><?php esc_html_e( "Share with friends", "tourfic" ); ?></h4>
                                            <ul>
                                                <li>
                                                    <a href="http://www.facebook.com/share.php?u=<?php echo esc_url( $share_link ); ?>"
                                                       class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-facebook"></i>
                                                </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="http://twitter.com/share?text=<?php echo esc_attr( $share_text ); ?>&url=<?php echo esc_url( $share_link ); ?>"
                                                       class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-twitter-square"></i>
                                                </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="https://www.linkedin.com/cws/share?url=<?php echo esc_url( $share_link ); ?>"
                                                       class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-linkedin"></i>
                                                </span>
                                                    </a>
                                                </li>
												<?php $share_image_link = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ); ?>
                                                <li>
                                                    <a href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url( $share_link ); ?>&media=<?php echo esc_url( get_the_post_thumbnail_url() ); ?>&description=<?php echo esc_attr( $share_text ); ?>"
                                                       class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-pinterest"></i>
                                                </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <div title="<?php esc_attr_e( 'Share this link', 'tourfic' ); ?>"
                                                         aria-controls="share_link_button">
                                                        <button id="share_link_button" class="tf_button share-center-copy-cta" tabindex="0"
                                                                role="button">
                                                            <i class="fa fa-link" aria-hidden="true"></i>

                                                            <span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
                                                        </button>
                                                        <input type="text" id="share_link_input"
                                                               class="share-center-url share-center-url-input"
                                                               value="<?php echo esc_attr( $share_link ); ?>" readonly style="opacity: 0; width: 0px !important;margin: 0px">

                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
							<?php } ?>
                            <!-- End Share Section -->
                        </div>
                    </div>
                </div>
                <!-- Single Tour Heading Section End -->

                <!-- Single Tour Body details start -->
                <div class="tf-single-details-wrapper tf-mt-30">
                    <div class="tf-single-details-inner tf-flex">
                        <div class="tf-column tf-tour-details-left">

							<?php
							if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout'] ) ) {
								foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout'] as $section ) {
									if ( ! empty( $section['tour-section-status'] ) && $section['tour-section-status'] == "1" && ! empty( $section['tour-section-slug'] ) ) {
										include TF_TEMPLATE_PART_PATH . 'tour/design-1/' . $section['tour-section-slug'] . '.php';
									}
								}
							} else {
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/gallery.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/price.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/description.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/information.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/highlights.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/include-exclude.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/itinerary.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/map.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/faq.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/trams-condition.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/review.php';
							}
							?>
                        </div>

                        <!-- SIdebar Tour single -->
                        <div class="tf-column tf-tour-details-right">
                            <div class="tf-tour-booking-box tf-box">
								<?php
								$hide_price = !empty( Helper::tfopt( 't-hide-start-price' ) ) ? Helper::tfopt( 't-hide-start-price' ) : '';
								if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) :
                                    if ( isset( $hide_price ) && $hide_price !== '1' ) : ?>
                                        <!-- Tourfic Pricing Head -->
                                        <div class="tf-booking-form-data">
                                            <div class="tf-booking-block">
                                                <div class="tf-booking-price tf-padbtm-12">
                                                <?php
                                                $tour_price = [];
                                                $tf_pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                                $tour_single_price_settings = !empty(Helper::tfopt('tour_archive_price_minimum_settings')) ? Helper::tfopt('tour_archive_price_minimum_settings') : 'all';

                                                $custom_pricing_by_rule = !empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : '';
                                                if( $tf_pricing_rule  && $tf_pricing_rule == 'group' ){

                                                    if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                                                        $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                                                        if ($custom_availability) {
                                                            if(is_array($meta['cont_custom_date'])) {
                                                                foreach ( $meta['cont_custom_date'] as $repval ) {

                                                                if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                                                    if(! empty( $repval['group_price'] )){
                                                                        $tour_price[] = $repval['group_price'];
                                                                    }
                                                                }
                                                                if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                                                    if($tour_single_price_settings == "all") {
                                                                        if(!empty($repval['adult_price']) && !$disable_adult){
                                                                            $tour_price[] = $repval['adult_price'];
                                                                        }
                                                                        if(!empty($repval['child_price']) && !$disable_child){
                                                                            $tour_price[] = $repval['child_price'];
                                                                        }
                                                                    }
                                                                    if($tour_single_price_settings == 'adult') {
                                                                        if(!empty($repval['adult_price']) && !$disable_adult){
                                                                            $tour_price[] = $repval['adult_price'];
                                                                        }
                                                                    }
                                                                    if($tour_single_price_settings == 'child') {
                                                                        if(!empty($repval['child_price']) && !$disable_child){
                                                                            $tour_price[] = $repval['child_price'];
                                                                        }
                                                                    }
                                                                }

                                                            }
                                                            }
                                                            
                                                        }else{
                                                            if(!empty($meta['group_price'])){
                                                                $tour_price[] = $meta['group_price'];
                                                            }
                                                        }
                                                    } else {
                                                        if(!empty($meta['group_price'])){
                                                            $tour_price[] = $meta['group_price'];
                                                        }
                                                    }

                                                }
                                                if( $tf_pricing_rule  && $tf_pricing_rule == 'person' ){

                                                    if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                                                        $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                                                        if ($custom_availability && is_array($meta['cont_custom_date'])) {
                                                            foreach ( $meta['cont_custom_date'] as $repval ) {

                                                                if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                                                    if(! empty( $repval['group_price'] )){
                                                                        $tour_price[] = $repval['group_price'];
                                                                    }
                                                                }
                                                                if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                                                    if($tour_single_price_settings == "all") {
                                                                        if(!empty($repval['adult_price']) && !$disable_adult){
                                                                            $tour_price[] = $repval['adult_price'];
                                                                        }
                                                                        if(!empty($repval['child_price']) && !$disable_child){
                                                                            $tour_price[] = $repval['child_price'];
                                                                        }
                                                                    }
                                                                    if($tour_single_price_settings == "adult") {
                                                                        if(!empty($repval['adult_price']) && !$disable_adult){
                                                                            $tour_price[] = $repval['adult_price'];
                                                                        }
                                                                    }
                                                                    if($tour_single_price_settings == "child") {
                                                                        if(!empty($repval['child_price']) && !$disable_adult){
                                                                            $tour_price[] = $repval['child_price'];
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }else{
                                                            if($tour_single_price_settings == 'all') {
                                                                if(!empty($meta['adult_price']) && !$disable_adult){
                                                                    $tour_price[] = $meta['adult_price'];
                                                                }
                                                                if(!empty($meta['child_price']) && !$disable_child){
                                                                    $tour_price[] = $meta['child_price'];
                                                                }
                                                            }
                                                            if($tour_single_price_settings == "adult") {
                                                                if(!empty($meta['adult_price']) && !$disable_adult){
                                                                    $tour_price[] = $meta['adult_price'];
                                                                }
                                                            }
                                                            if($tour_single_price_settings == "child") {
                                                                if(!empty($meta['child_price']) && !$disable_adult){
                                                                    $tour_price[] = $meta['child_price'];
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        if($tour_single_price_settings == 'all') {
                                                            if(!empty($meta['adult_price']) && !$disable_adult){
                                                                $tour_price[] = $meta['adult_price'];
                                                            }
                                                            if(!empty($meta['child_price']) && !$disable_child){
                                                                $tour_price[] = $meta['child_price'];
                                                            }
                                                        }
                                                        if($tour_single_price_settings == "adult") {
                                                            if(!empty($meta['adult_price']) && !$disable_adult){
                                                                $tour_price[] = $meta['adult_price'];
                                                            }
                                                        }
                                                        if($tour_single_price_settings == "child") {
                                                            if(!empty($meta['child_price']) && !$disable_adult){
                                                                $tour_price[] = $meta['child_price'];
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                    <p> <span><?php esc_html_e("From","tourfic"); ?></span>

                                                    <?php
                                                    //get the lowest price from all available room price
                                                    $tf_tour_min_price      = !empty($tour_price) ? min( $tour_price ) : 0;
                                                    $tf_tour_full_price     = !empty($tour_price) ? min( $tour_price ) : 0;
                                                    $tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
                                                    $tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';
                                                    if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {
                                                        if ( $tf_tour_discount_type == "percent" ) {
                                                            $tf_tour_min_discount = ( $tf_tour_min_price * (int) $tf_tour_discount_price ) / 100;
                                                            $tf_tour_min_price    = $tf_tour_min_price - $tf_tour_min_discount;
                                                        }
                                                        if ( $tf_tour_discount_type == "fixed" ) {
                                                            $tf_tour_min_discount = $tf_tour_discount_price;
                                                            $tf_tour_min_price    = $tf_tour_min_price - (int) $tf_tour_discount_price;
                                                        }
                                                    }
                                                    $lowest_price = wp_strip_all_tags(wc_price( $tf_tour_min_price ));
                                                    
                                                    if ( ! empty( $tf_tour_min_discount ) ) {
                                                        echo wp_kses_post($lowest_price). " " . "<span><del>" . wp_kses_post(wp_strip_all_tags(wc_price( $tf_tour_full_price ))) . "</del></span>";
                                                    } else {
                                                        echo wp_kses_post($lowest_price);
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
		                                    echo wp_kses(Tour::tf_single_tour_booking_form( $post->ID ), Helper::tf_custom_wp_kses_allow_tags());
                                        }
                                        ?>
	                                    <?php if ($tf_booking_type == 2 && $tf_hide_booking_form == 1):?>
                                            <a href="<?php echo esc_url($tf_booking_url) ?>" target="_blank" class="tf-btn-normal btn-primary" style="margin-top: 10px;"><?php esc_html_e('Book now', 'tourfic'); ?></a>
	                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
							<?php
							if ( $email || $phone || $fax || $website ) {
								?>
                                <div class="tf-tour-booking-advantages tf-box tf-mt-30">
                                    <div class="tf-head-title">
                                        <h3><?php echo ! empty( $meta['contact-info-section-title'] ) ? esc_html( $meta['contact-info-section-title'] ) : ''; ?></h3>
                                    </div>
                                    <div class="tf-booking-advantage-items">
                                        <ul class="tf-list">
											<?php
											if ( ! empty( $phone ) ) { ?>
                                                <li><i class="fa-solid fa-headphones"></i> <a href="tel:<?php echo esc_html( $phone ) ?>"><?php echo esc_html( $phone ) ?></a></li>
											<?php } ?>
											<?php
											if ( ! empty( $email ) ) { ?>
                                                <li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?php echo esc_html( $email ) ?>"><?php echo esc_html( $email ) ?></a></li>
											<?php } ?>
											<?php
											if ( ! empty( $website ) ) { ?>
                                                <li><i class="fa-solid fa-link"></i> <a target="_blank" href="<?php echo esc_html( $website ) ?>"><?php echo esc_html( $website ) ?></a></li>
											<?php } ?>
											<?php
											if ( ! empty( $fax ) ) { ?>
                                                <li><i class="fa-solid fa-fax"></i> <a href="tel:<?php echo esc_html( $fax ) ?>"><?php echo esc_html( $fax ) ?></a></li>
											<?php } ?>
                                        </ul>
                                    </div>
                                </div>
							<?php } ?>
							<?php
							$tf_enquiry_section_status = ! empty( $meta['t-enquiry-section'] ) ? $meta['t-enquiry-section'] : "";
                            $tf_enquiry_section_icon = ! empty( $meta['t-enquiry-option-icon'] ) ? esc_html( $meta['t-enquiry-option-icon'] ) : '';
                            $tf_enquiry_section_title = ! empty( $meta['t-enquiry-option-title'] ) ? esc_html( $meta['t-enquiry-option-title'] ) : '';
                            $tf_enquiry_section_des = ! empty( $meta['t-enquiry-option-content'] ) ? esc_html( $meta['t-enquiry-option-content'] ) : '';
                            $tf_enquiry_section_button = ! empty( $meta['t-enquiry-option-btn'] ) ? esc_html( $meta['t-enquiry-option-btn'] ) : '';

							if ( ! empty( $tf_enquiry_section_status ) ) {
								?>
                                <!-- Enquiry box -->
                                <div class="tf-tour-booking-advantages tf-box tf-mt-30">
                                    <div class="tf-ask-enquiry">
                                        <?php 
                                        if(!empty($tf_enquiry_section_icon)) {
                                            ?>
                                            <i class="<?php echo esc_attr($tf_enquiry_section_icon); ?>" aria-hidden="true"></i>
                                            <?php
                                        }
                                        if(!empty($tf_enquiry_section_title)) {
                                            ?>
                                            <h3><?php echo esc_html($tf_enquiry_section_title); ?></h3>
                                            <?php
                                        }
                                        if(!empty($tf_enquiry_section_des)) {
                                            ?>
                                            <p><?php echo wp_kses_post($tf_enquiry_section_des); ?></p>
                                            <?php
                                        }
                                        if(!empty($tf_enquiry_section_button)) {
                                            ?>
                                            <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-btn-normal btn-primary"><span>
                                        <?php echo esc_html($tf_enquiry_section_button); ?>
                                        </span></a></div>
                                        <?php 
                                        }
                                        ?>
                                        
                                    </div>
                                </div>
							<?php } ?>
                        </div>

                        <!-- Responsive booking Modal -->
                        <div class="tf-modal" id="tf-tour-booking-modal">
                            <div class="tf-modal-dialog">
                                <div class="container tf-modal-content">
                                    <div class="tf-modal-header">
                                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                                    </div>
                                    <div class="tf-modal-body">
                                        <div class="tf-tour-booking-box tf-box">
                                            <!-- Tourfic Pricing Head -->
                                            <div class="tf-booking-form-data">
                                                <div class="tf-booking-block">
                                                    <div class="tf-booking-price tf-padbtm-12">
														<?php
														$tour_price             = [];
														$tf_pricing_rule        = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
														$custom_pricing_by_rule = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : '';
														if ( $tf_pricing_rule && $tf_pricing_rule == 'group' ) {

															if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
																$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
																if ( $custom_availability ) {
																	foreach ( $meta['cont_custom_date'] as $repval ) {

																		if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
																			if ( ! empty( $repval['group_price'] ) ) {
																				$tour_price[] = $repval['group_price'];
																			}
																		}
																		if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
																			if ( ! empty( $repval['adult_price'] ) && ! $disable_adult ) {
																				$tour_price[] = $repval['adult_price'];
																			}
																			if ( ! empty( $repval['child_price'] ) && ! $disable_child ) {
																				$tour_price[] = $repval['child_price'];
																			}
																			if ( ! empty( $repval['infant_price'] ) && ! $disable_infant ) {
																				$tour_price[] = $repval['infant_price'];
																			}
																		}

																	}
																} else {
																	if ( ! empty( $meta['group_price'] ) ) {
																		$tour_price[] = $meta['group_price'];
																	}
																}
															} else {
                                                                if ( ! empty( $meta['group_price'] ) ) {
                                                                    $tour_price[] = $meta['group_price'];
                                                                }
                                                            }

														}
														if ( $tf_pricing_rule && $tf_pricing_rule == 'person' ) {

															if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
																$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
																if ( $custom_availability ) {
																	foreach ( $meta['cont_custom_date'] as $repval ) {

																		if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
																			if ( ! empty( $repval['group_price'] ) ) {
																				$tour_price[] = $repval['group_price'];
																			}
																		}
																		if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
																			if ( ! empty( $repval['adult_price'] ) && ! $disable_adult ) {
																				$tour_price[] = $repval['adult_price'];
																			}
																			if ( ! empty( $repval['child_price'] ) && ! $disable_child ) {
																				$tour_price[] = $repval['child_price'];
																			}
																			if ( ! empty( $repval['infant_price'] ) && ! $disable_infant ) {
																				$tour_price[] = $repval['infant_price'];
																			}
																		}
																	}
																} else {
																	if ( ! empty( $meta['adult_price'] ) && ! $disable_adult ) {
																		$tour_price[] = $meta['adult_price'];
																	}
																	if ( ! empty( $meta['child_price'] ) && ! $disable_child ) {
																		$tour_price[] = $meta['child_price'];
																	}
																	if ( ! empty( $meta['infant_price'] ) && ! $disable_infant ) {
																		$tour_price[] = $meta['infant_price'];
																	}
																}
															} else {
                                                                if ( ! empty( $meta['adult_price'] ) && ! $disable_adult ) {
                                                                    $tour_price[] = $meta['adult_price'];
                                                                }
                                                                if ( ! empty( $meta['child_price'] ) && ! $disable_child ) {
                                                                    $tour_price[] = $meta['child_price'];
                                                                }
                                                                if ( ! empty( $meta['infant_price'] ) && ! $disable_infant ) {
                                                                    $tour_price[] = $meta['infant_price'];
                                                                }
                                                            }
														}
														?>
                                                        <p><span><?php esc_html_e( "From", "tourfic" ); ?></span>
                                                        <?php
                                                            //get the lowest price from all available room price
                                                            $tf_tour_min_price      = !empty($tour_price) ? min( $tour_price ) : 0;
                                                            $tf_tour_full_price     = !empty($tour_price) ? min( $tour_price ) : 0;
                                                            $tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
                                                            $tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';
                                                            if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {
                                                                if ( $tf_tour_discount_type == "percent" ) {
                                                                    $tf_tour_min_discount = ( $tf_tour_min_price * (int) $tf_tour_discount_price ) / 100;
                                                                    $tf_tour_min_price    = $tf_tour_min_price - $tf_tour_min_discount;
                                                                }
                                                                if ( $tf_tour_discount_type == "fixed" ) {
                                                                    $tf_tour_min_discount = $tf_tour_discount_price;
                                                                    $tf_tour_min_price    = $tf_tour_min_price - (int) $tf_tour_discount_price;
                                                                }
                                                            }
                                                            $lowest_price = wp_strip_all_tags(wc_price( $tf_tour_min_price ));
                                                            
                                                            if ( ! empty( $tf_tour_min_discount ) ) {
                                                                echo wp_kses_post($lowest_price). " " . "<span><del>" . wp_kses_post(wp_strip_all_tags(wc_price( $tf_tour_full_price ))) . "</del></span>";
                                                            } else {
                                                                echo wp_kses_post($lowest_price);
                                                            }
                                                        ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tourfic Booking form -->
                                            <div class="tf-booking-form">
                                                <div class="tf-booking-form-inner tf-mt-24">
                                                    <h3><?php echo ! empty( $meta['booking-section-title'] ) ? esc_html( $meta['booking-section-title'] ) : ''; ?></h3>
													<?php echo wp_kses(Tour::tf_single_tour_booking_form( $post->ID ), Helper::tf_custom_wp_kses_allow_tags()); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Tour Body details End -->
                </div>
            </div>
        </div>

		<?php
		if ( ! $disable_related_tour == '1' ) {
			$related_tour_type = Helper::tfopt( 'rt_display' );
			$args              = array(
				'post_type'      => 'tf_tours',
				'post_status'    => 'publish',
				'posts_per_page' => 8,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'tax_query'      => array( // WPCS: slow query ok.
					array(
						'taxonomy' => 'tour_destination',
						'field'    => 'slug',
						'terms'    => $first_destination_slug,
					),
				),
			);

			//show related tour based on selected tours
			$selected_ids = !empty(Helper::tfopt( 'tf-related-tours' )) ? Helper::tfopt( 'tf-related-tours' ) : array();

			if ( $related_tour_type == 'selected') {
                if(in_array($post_id, $selected_ids)) {
                    $index = array_search($post_id, $selected_ids);

                    $current_post_id = array($selected_ids[$index]);

                    unset($selected_ids[$index]);
                } else {
                    $current_post_id = array($post_id);
                }

                if(count($selected_ids) > 0) {
                    $args['post__in'] = $selected_ids;
                } else {
                    $args['post__in'] = array(-1);
                }
			} else {
				$current_post_id = array($post_id);
            }

			$tours = new WP_Query( $args );

            $all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function($id) use ($current_post_id) {
                return $id != $current_post_id[0];
            });

			if ( $tours->have_posts() ) {
				?>
                    <!-- Tourfic upcomming tours tours -->
                    <div class="upcomming-tours">
                        <div class="tf-template-container">
                            <div class="tf-container-inner">
                                <div class="section-title">
                                    <h2 class="tf-title"><?php echo ! empty( Helper::tfopt( 'rt-title' ) ) ? esc_html( Helper::tfopt( 'rt-title' )) : ''; ?></h2>
                                    <?php
                                    if ( ! empty( Helper::tfopt( 'rt-description' ) ) ) { ?>
                                        <p><?php echo wp_kses_post(Helper::tfopt( 'rt-description')) ?></p>
                                    <?php } ?>
                                </div>
                                <div class="tf-slider-items-wrapper tf-upcomming-tours-list-outter tf-mt-40 tf-flex tf-flex-gap-24">
                                    <?php
                                    while ( $tours->have_posts() ) {
                                        $tours->the_post();
                                        if( is_array($all_tour_ids) && in_array(get_the_ID(), $all_tour_ids) ):
                                            $selected_design_post_id = get_the_ID();
                                            $destinations           = get_the_terms( $selected_design_post_id, 'tour_destination' );

                                            $first_destination_name = $destinations[0]->name;
                                            $related_comments       = get_comments( array( 'post_id' => $selected_design_post_id ) );
                                            $meta                   = get_post_meta( $selected_design_post_id, 'tf_tours_opt', true );
                                            $pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                            $disable_adult          = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
                                            $disable_child          = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
                                            $tour_price             = new Tour_Price( $meta );
                                            ?>
                                            <div class="tf-slider-item tf-post-box-lists">
                                                <div class="tf-post-single-box">
                                                    <div class="tf-image-data">
                                                        <img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) ? esc_url(get_the_post_thumbnail_url( $selected_design_post_id, 'full' )) : esc_url(TF_ASSETS_APP_URL . '/images/feature-default.jpg'); ?>"
                                                            alt="">
                                                        <div class="tf-meta-data-price">
                                                            <?php esc_html_e( "From", "tourfic" ); ?>
                                                            <span>
                                                <?php if ( $pricing_rule == 'group' ) {
                                                    echo !empty( $tour_price->wc_sale_group ) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group);
                                                } else if ( $pricing_rule == 'person' ) {
                                                    if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                        echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                                    } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                        echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                                    }
                                                }
                                                ?>
                                                </span>
                                                        </div>
                                                    </div>
                                                    <div class="tf-meta-info tf-mt-30">
                                                        <div class="tf-meta-location">
                                                            <i class="fa-solid fa-location-dot"></i> <?php echo esc_html($first_destination_name); ?>
                                                        </div>
                                                        <div class="tf-meta-title">
                                                            <h2><a href="<?php the_permalink($selected_design_post_id) ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode(get_the_title( $selected_design_post_id )), 35 ) ); ?></a></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
			<?php }
			wp_reset_postdata();
			?>
		<?php } ?>
    </div>
</div>