<div class="tf-single-page tf-template-global tf-hotel-design-1">
    <div class="tf-tour-single">
        <div class="tf-template-container">
            <div class="tf-container-inner">
                <!-- Single Hotel Heading Section start -->
                <div class="tf-section tf-single-head">
                    <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                        <div class="tf-head-title">
                            <h1><?php the_title(); ?></h1>
                            <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                                <?php if ( $locations ) { ?>
                                    <?php if ( $address ) {
                                        echo '<i class="fa-solid fa-location-dot"></i> ' . $address . ' –';
                                    } ?>
                                    <a href="<?php echo $first_location_url; ?>" class="more-hotel tf-d-ib">
                                        <?php printf( __( 'Show more hotels in %s', 'tourfic' ), $first_location_name ); ?>
                                    </a>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
                            <?php
                            // Wishlist
                            if ( tfopt( 'wl-bt-for' ) && in_array( '2', tfopt( 'wl-bt-for' ) ) ) { 
                                if ( is_user_logged_in() ) {
                                if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
                            ?>
                            <div class="tf-icon tf-wishlist-box">
                            <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                            </div>
                            <?php } } else{ 
                            if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {    
                            ?>
                            <div class="tf-icon tf-wishlist-box">
                            <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                                                        data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>"
                                                                        data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) {
                                        echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
                                    } ?>></i>
                            </div>
                            <?php } } } ?>

                            <!-- Share Section -->
                            <?php if ( ! $disable_share_opt == '1' ) { ?>
                            <div class="tf-share">
                                <a href="#dropdown-share-center" class="share-toggle tf-icon tf-social-box"
                                data-toggle="true">
                                <i class="fa-solid fa-share-nodes"></i>
                                </a>
                                <div id="dropdown-share-center" class="share-tour-content">
                                    <ul class="tf-dropdown-content">
                                        <li>
                                            <a href="http://www.facebook.com/share.php?u=<?php echo esc_url( $share_link ); ?>"
                                            class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-facebook-square"></i>
                                            <?php esc_html_e( 'Share on Facebook', 'tourfic' ); ?>
                                        </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://twitter.com/share?text=<?php echo esc_attr( $share_text ); ?>&url=<?php echo esc_url( $share_link ); ?>"
                                            class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-twitter-square"></i>
                                            <?php esc_html_e( 'Share on Twitter', 'tourfic' ); ?>
                                        </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.linkedin.com/cws/share?url=<?php echo esc_url( $share_link ); ?>"
                                            class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-linkedin"></i>
                                            <?php esc_html_e( 'Share on Linkedin', 'tourfic' ); ?>
                                        </span>
                                            </a>
                                        </li>
                                        <?php $share_image_link = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ); ?>
                                        <li>
                                            <a href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url( $share_link ); ?>&media=<?php echo esc_url( get_the_post_thumbnail_url() ); ?>&description=<?php echo esc_attr( $share_text ); ?>"
                                            class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-pinterest"></i>
                                            <?php esc_html_e( 'Share on Pinterest', 'tourfic' ); ?>
                                        </span>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="share-center-copy-form tf-dropdown-item" title="<?php esc_attr_e( 'Share this link', 'tourfic' ); ?>"
                                                aria-controls="share_link_button">
                                                <label class="share-center-copy-label"
                                                    for="share_link_input"><?php esc_html_e( 'Share this link', 'tourfic' ); ?></label>
                                                <input type="text" id="share_link_input"
                                                    class="share-center-url share-center-url-input"
                                                    value="<?php echo esc_attr( $share_link ); ?>" readonly>
                                                <button id="share_link_button" class="tf_button share-center-copy-cta" tabindex="0"
                                                        role="button">
                                                    <span class="tf-button-text share-center-copy-message"><?php esc_html_e( 'Copy link', 'tourfic' ); ?></span>
                                                    <span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- End Share Section -->
                        </div>
                    </div>
                </div>
                <!-- Single Hotel Heading Section End -->

                <!-- Single Hotel Body details start -->
                <div class="tf-single-details-wrapper tf-mrtop-30">
                    <div class="tf-single-details-inner tf-flex">
                        <div class="tf-column tf-tour-details-left">
                            <!-- Hotel Gallery Section -->
                            <div class="tf-hero-gallery tf-mrbottom-30">
                            <div class="tf-gallery-featured">
                                <img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : TF_ASSETS_APP_URL.'/images/feature-default.jpg'; ?>" alt="<?php _e( 'Hotel Image', 'tourfic' ); ?>">
                                <div class="featured-meta-gallery-videos">
                                    <div class="featured-column tf-gallery-box">
                                        <?php 
                                        if ( ! empty( $gallery_ids ) ) {
                                        foreach ( $gallery_ids as $key => $gallery_item_id ) {
                                        $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                        if ( $key === array_key_first( $gallery_ids ) ) {
                                        ?>
                                        <a id="featured-gallery" href="#" data-fancybox="tour-gallery" class="tf-tour-gallery" data-src="<?php echo esc_url($image_url); ?>">
                                            <i class="fa-solid fa-camera-retro"></i><?php echo __("Gallery","tourfic"); ?>
                                        </a>
                                        <?php 
                                            }}}
                                        ?>

                                    </div>
                                    <?php
                                    $hotel_video = ! empty( $meta['video'] ) ? $meta['video'] : '';
                                    if ( !empty($hotel_video) ) { ?>
                                    <div class="featured-column tf-video-box">
                                        <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video" href="<?php echo esc_url($hotel_video); ?>">
                                            <i class="fa-solid fa-video"></i> <?php echo __("Video","tourfic"); ?>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="tf-gallery">
                                <?php 
                                $gallery_count = 1;
                                    if ( ! empty( $gallery_ids ) ) {
                                    foreach ( $gallery_ids as $key => $gallery_item_id ) {
                                    $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                ?>
                                <a class="<?php echo $gallery_count==5 ? esc_attr( 'tf-gallery-more' ) : ''; ?> " href="<?php echo esc_url($image_url); ?>" data-fancybox="tour-gallery"><img src="<?php echo esc_url($image_url); ?>"></a>
                                <?php $gallery_count++; } } ?>
                            </div>
                            </div>
                        </div>

                        <!-- SIdebar Tour single -->
                        <div class="tf-column tf-tour-details-right">
                            <div class="tf-tour-booking-box tf-box">
                                <?php tf_hotel_sidebar_booking_form(); ?>
                            </div>
                            <div class="tf-hotel-location-map">
                                <?php if ( !defined( 'TF_PRO' ) && ( $address ) ) { ?>
                                    <div class="tf-hotel-location-preview show-on-map">
                                    <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="290"
                                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                        <a href="https://www.google.com/maps/search/<?php echo $address; ?>" target="_blank"><i class="fa-solid fa-location-dot"></i></a>
                                    </div>
                                <?php } ?>
                                <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $map["address"] ) || (! empty( $map["latitude"] ) && ! empty( $map["longitude"] ) ) ) ) { ?>
                                    <div class="tf-hotel-location-preview show-on-map">
                                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $map["latitude"] ); ?>,<?php echo esc_attr( $map["longitude"] ); ?>&output=embed" width="100%" height="290"
                                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                                        <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </a>

                                    </div>
                                    <div style="display: none;" id="tf-hotel-google-maps">
                                        <div class="tf-hotel-google-maps-container">
                                            <?php
                                            if ( ! empty( $map["address"] ) ) { ?>
                                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $map["address"] ) ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;"
                                                        allowfullscreen="" loading="lazy"></iframe>
                                            <?php } else { ?>
                                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $map["latitude"] ); ?>,<?php echo esc_attr( $map["longitude"] ); ?>&z=17&output=embed" width="100%" height="550"
                                                        style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Single Hotel Body details End -->

                <div class="tf-hotel-descriptions tf-mrbottom-70">
                <?php the_content(); ?>
                </div>
                
                <?php if ( $features ) { ?>
                <div class="tf-hotel-single-features tf-mrbottom-70">
                    <h2 class="tf-title"><?php echo __("Popular Features","tourfic"); ?></h2>
                    <ul>
                    <?php foreach ( $features as $feature ) {
                        $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
                        $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                        if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                            $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                        } elseif ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                            $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                        } ?>

                        <li>
                            <?php echo $feature_icon ?? ''; ?>
                            <?php echo $feature->name; ?>
                        </li>
                    <?php } ?>
                    </ul>
                </div>
                <?php } ?>
                
                <div class="tf-hotel-faqs-section tf-mrbottom-70">
                    <h2 class="tf-title" ><?php _e( "Faq’s", 'tourfic' ); ?></h2>
                    <div class="tf-section-flex tf-flex">
                        <div class="tf-hotel-enquiry">
                            <div class="tf-ask-enquiry">
                                <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                <h3><?php _e( "Have a question in mind", 'tourfic' ); ?></h3>
                                <p><?php _e( "Looking for more info? Send a question to the property to find out more.", 'tourfic' ); ?></p>
                                <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-bttn-normal bttn-primary"><span><?php esc_html_e( 'Ask a Question', 'tourfic' ); ?></span></a></div>
                            </div>
                        </div>
                        <div class="tf-hotel-faqs">
                            <!-- tourfic FAQ -->
                            <div class="tf-faq-wrapper">
                                <div class="tf-faq-inner">
                                    <?php if ( $faqs ): ?>
                                        <?php 
                                        $faq_key = 1;    
                                        foreach ( $faqs as $key => $faq ): ?>
                                        <div class="tf-faq-single <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
                                            <div class="tf-faq-single-inner">
                                                <div class="tf-faq-collaps tf-flex tf-flex-align-center tf-flex-space-bttn">
                                                    <h3><?php echo esc_html( $faq['title'] ); ?></h3> 
                                                    <div class="faq-icon"><i class="fa-solid fa-plus"></i><i class="fa-solid fa-minus"></i></div>
                                                </div>
                                                <div class="tf-faq-content tf-mrtop-24">
                                                <?php echo wp_kses_post( $faq['description'] ); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $faq_key++; endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ( ! $disable_review_sec == 1 ) { ?>
                <div class="tf-review-wrapper tf-mrbottom-70">
                    <!-- Tourfic review features ratting -->
                    <div class="tf-average-review">
                        <div class="tf-section-head">
                            <h2 class="tf-title"><?php _e("Average Guest Reviews","tourfic"); ?></h2>
                        </div>
                    </div>
                    
                    <?php comments_template(); ?>
                    
                </div>
                <?php } ?>
                
                <?php if ( $tc ) { ?>
                <!-- Tourfic Hotel Terms and conditions -->
                <div class="tf-toc-wrapper tf-mrbottom-70">
                    <div class="tf-section-head">
                        <h2 class="tf-title"><?php echo __("Hotel Terms & Conditions","tourfic"); ?></h2>
                        <?php echo wpautop( $tc ); ?>
                    </div>
                </div>
                <?php } ?>

                <?php if ( $rooms ) :
    
                //getting only selected features for rooms
                $rm_features = [];
                foreach ( $rooms as $key => $room ) {
                    //merge for each room's selected features
                    if(!empty($room['features'])){
                        $rm_features = array_unique(array_merge( $rm_features, $room['features'])) ;
                    }
                }
                ?>

                <div class="tf-rooms-sections tf-mrbottom-70">
                    <h2 class="section-heading"><?php esc_html_e( 'Available Rooms', 'tourfic' ); ?></h2>
                    <?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>
                    <div class="tf-rooms">
                        <!-- Loader Image -->
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                            </div>
                        </div>

                        <!-- Room Table -->
                        <table class="tf-availability-table">
                            <thead>
                                <tr>
                                    <th class="description" colspan="4"><?php _e( 'Room Details', 'tourfic' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <!-- Start Single Room -->
                            <?php foreach ( $rooms as $key => $room ) {
                                $enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
                                if ( $enable == '1' ) {
                                    $footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
                                    $bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
                                    $adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
                                    $child_number = ! empty( $room['child'] ) ? $room['child'] : '0';
                                    $total_person = $adult_number + $child_number;
                                    $pricing_by   = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                                    $avil_by_date = ! empty( $room['avil_by_date'] ) ? ! empty( $room['avil_by_date'] ) : false;

                                    if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avil_by_date == true ) {
                                        $repeat_by_date = ! empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                                        if ( $pricing_by == '1' ) {
                                            $prices = wp_list_pluck( $repeat_by_date, 'price' );
                                        } else {
                                            $prices = wp_list_pluck( $repeat_by_date, 'adult_price' );
                                        }
                                        if ( ! empty( $prices ) ) {
                                            $range_price = [];
                                            foreach ( $prices as $single ) {
                                                if ( ! empty( $single ) ) {
                                                    $range_price[] = $single;
                                                }
                                            }
                                            if ( sizeof( $range_price ) > 1 ) {
                                                $price = $prices ? ( min( $prices ) != max( $prices ) ? wc_format_price_range( min( $prices ), max( $prices ) ) : wc_price( min( $prices ) ) ) : wc_price( 0 );
                                            } else {
                                                $price = ! empty( $range_price[0] ) ? wc_price( $range_price[0] ) : wc_price( 0 );
                                            }
                                        }else{
                                            if ( $pricing_by == '1' ) {
                                                $price = wc_price( ! empty( $room['price'] ) ? $room['price'] : '0.0' );
                                            } else {
                                                $price = wc_price( ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0' );
                                            }
                                        }
                                    } else {
                                        if ( $pricing_by == '1' ) {
                                            $price = wc_price( ! empty( $room['price'] ) ? $room['price'] : '0.0' );
                                        } else {
                                            $price = wc_price( ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0' );
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="description">
                                            <div class="tf-room-type">
                                                <div class="tf-room-title">
                                                    <?php
                                                    $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                                    if ( $tour_room_details_gall ) {
                                                        $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                                    }
                                                    if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_room_details_gall ){
                                                        ?>
                                                        <h3><a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $key : '' ?>"
                                                                data-hotel="<?php echo $post_id; ?>" style="text-decoration: underline;">
                                                                <?php echo esc_html( $room['title'] ); ?>
                                                            </a></h3>

                                                        <div id="tour_room_details_qv" class="tf-reg-wrap">

                                                        </div>
                                                    <?php } else{ ?>
                                                    <h3><?php echo esc_html( $room['title'] ); ?><h3>
                                                            <?php
                                                            }
                                                            ?>
                                                </div>
                                                <div class="bed-facilities"><?php _e( $room['description'] ); ?></div>
                                            </div>

                                            <?php if ( $footage ) { ?>
                                                <div class="tf-tooltip tf-d-ib">
                                                    <div class="room-detail-icon">
                                            <span class="room-icon-wrap"><i
                                                        class="fas fa-ruler-combined"></i></span>
                                                        <span class="icon-text tf-d-b"><?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></span>
                                                    </div>
                                                    <div class="tf-top">
                                                        <?php _e( 'Room Footage', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
                                            <?php }
                                            if ( $bed ) { ?>
                                                <div class="tf-tooltip tf-d-ib">
                                                    <div class="room-detail-icon">
                                                        <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                                                    </div>
                                                    <div class="tf-top">
                                                        <?php _e( 'Number of Beds', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if ( ! empty( $room['features'] ) ) { ?>
                                                <div class="room-features">
                                                    <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
                                                    </div>
                                                    <ul class="room-feature-list">

                                                        <?php foreach ( $room['features'] as $feature ) {

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

                                                            $room_term = get_term( $feature ); ?>
                                                            <li class="tf-tooltip">
                                                                <?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
                                                                <div class="tf-top">
                                                                    <?php echo $room_term->name; ?>
                                                                    <i class="tool-i"></i>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td class="pax">
                                            <?php if ( $adult_number ) { ?>
                                                <div class="tf-tooltip tf-d-b">
                                                    <div class="room-detail-icon">
                                            <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                                                        class="fas fa-female"></i></span>
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
                                                        <?php _e( 'Number of Children', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td class="pricing">
                                            <div class="tf-price-column">
                                                <?php
                                                if ( $pricing_by == '1' ) {
                                                    ?>
                                                    <span class="tf-price"><?php echo $price; ?></span>
                                                    <div class="price-per-night">
                                                        <?php esc_html_e( 'per night', 'tourfic' ); ?>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <span class="tf-price"><?php echo $price; ?></span>
                                                    <div class="price-per-night">
                                                        <?php esc_html_e( 'per person/night', 'tourfic' ); ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="reserve tf-t-c">
                                            <div class="tf-btn">
                                                <button class="btn-styled hotel-room-availability tf-sml-btn"
                                                        type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Start Room Section -->
                <div class="tf-room-section sp-50">
                    <div class="tf-container">
                        <h2 class="section-heading"><?php esc_html_e( 'Available Rooms', 'tourfic' ); ?></h2>
                        <!-- Hooked in feature filter action -->
                        <?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>
                        <div class="tf-room-type" id="rooms">
                            <div class="tf-room-table hotel-room-wrap">
                                <div id="tour_room_details_loader">
                                    <div id="tour-room-details-loader-img">
                                        <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                                    </div>
                                </div>
                                <table class="availability-table">
                                    <thead>
                                    <tr>
                                        <th class="description"><?php _e( 'Room Details', 'tourfic' ); ?></th>
                                        <th class="pax"><?php _e( 'Pax', 'tourfic' ); ?></th>
                                        <th class="pricing"><?php _e( 'Price', 'tourfic' ); ?></th>
                                        <th class="reserve"><?php _e( 'Select Rooms', 'tourfic' ); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Start Single Room -->
                                    <?php foreach ( $rooms as $key => $room ) {
                                        $enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
                                        if ( $enable == '1' ) {
                                            $footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
                                            $bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
                                            $adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
                                            $child_number = ! empty( $room['child'] ) ? $room['child'] : '0';
                                            $total_person = $adult_number + $child_number;
                                            $pricing_by   = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                                            $avil_by_date = ! empty( $room['avil_by_date'] ) ? ! empty( $room['avil_by_date'] ) : false;

                                            if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avil_by_date == true ) {
                                                $repeat_by_date = ! empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                                                if ( $pricing_by == '1' ) {
                                                    $prices = wp_list_pluck( $repeat_by_date, 'price' );
                                                } else {
                                                    $prices = wp_list_pluck( $repeat_by_date, 'adult_price' );
                                                }
                                                if ( ! empty( $prices ) ) {
                                                    $range_price = [];
                                                    foreach ( $prices as $single ) {
                                                        if ( ! empty( $single ) ) {
                                                            $range_price[] = $single;
                                                        }
                                                    }
                                                    if ( sizeof( $range_price ) > 1 ) {
                                                        $price = $prices ? ( min( $prices ) != max( $prices ) ? wc_format_price_range( min( $prices ), max( $prices ) ) : wc_price( min( $prices ) ) ) : wc_price( 0 );
                                                    } else {
                                                        $price = ! empty( $range_price[0] ) ? wc_price( $range_price[0] ) : wc_price( 0 );
                                                    }
                                                }else{
                                                    if ( $pricing_by == '1' ) {
                                                        $price = wc_price( ! empty( $room['price'] ) ? $room['price'] : '0.0' );
                                                    } else {
                                                        $price = wc_price( ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0' );
                                                    }
                                                }
                                            } else {
                                                if ( $pricing_by == '1' ) {
                                                    $price = wc_price( ! empty( $room['price'] ) ? $room['price'] : '0.0' );
                                                } else {
                                                    $price = wc_price( ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0' );
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td class="description">
                                                    <div class="tf-room-type">
                                                        <div class="tf-room-title">
                                                            <?php
                                                            $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                                            if ( $tour_room_details_gall ) {
                                                                $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                                            }
                                                            if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_room_details_gall ){
                                                                ?>
                                                                <h3><a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $key : '' ?>"
                                                                        data-hotel="<?php echo $post_id; ?>" style="text-decoration: underline;">
                                                                        <?php echo esc_html( $room['title'] ); ?>
                                                                    </a></h3>

                                                                <div id="tour_room_details_qv" class="tf-reg-wrap">

                                                                </div>
                                                            <?php } else{ ?>
                                                            <h3><?php echo esc_html( $room['title'] ); ?><h3>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                        </div>
                                                        <div class="bed-facilities"><?php _e( $room['description'] ); ?></div>
                                                    </div>

                                                    <?php if ( $footage ) { ?>
                                                        <div class="tf-tooltip tf-d-ib">
                                                            <div class="room-detail-icon">
                                                    <span class="room-icon-wrap"><i
                                                                class="fas fa-ruler-combined"></i></span>
                                                                <span class="icon-text tf-d-b"><?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></span>
                                                            </div>
                                                            <div class="tf-top">
                                                                <?php _e( 'Room Footage', 'tourfic' ); ?>
                                                                <i class="tool-i"></i>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                    if ( $bed ) { ?>
                                                        <div class="tf-tooltip tf-d-ib">
                                                            <div class="room-detail-icon">
                                                                <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                                                <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                                                            </div>
                                                            <div class="tf-top">
                                                                <?php _e( 'Number of Beds', 'tourfic' ); ?>
                                                                <i class="tool-i"></i>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if ( ! empty( $room['features'] ) ) { ?>
                                                        <div class="room-features">
                                                            <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
                                                            </div>
                                                            <ul class="room-feature-list">

                                                                <?php foreach ( $room['features'] as $feature ) {

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

                                                                    $room_term = get_term( $feature ); ?>
                                                                    <li class="tf-tooltip">
                                                                        <?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
                                                                        <div class="tf-top">
                                                                            <?php echo $room_term->name; ?>
                                                                            <i class="tool-i"></i>
                                                                        </div>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="pax">
                                                    <?php if ( $adult_number ) { ?>
                                                        <div class="tf-tooltip tf-d-b">
                                                            <div class="room-detail-icon">
                                                    <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                                                                class="fas fa-female"></i></span>
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
                                                                <?php _e( 'Number of Children', 'tourfic' ); ?>
                                                                <i class="tool-i"></i>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="pricing">
                                                    <div class="tf-price-column">
                                                        <?php
                                                        if ( $pricing_by == '1' ) {
                                                            ?>
                                                            <span class="tf-price"><?php echo $price; ?></span>
                                                            <div class="price-per-night">
                                                                <?php esc_html_e( 'per night', 'tourfic' ); ?>
                                                            </div>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <span class="tf-price"><?php echo $price; ?></span>
                                                            <div class="price-per-night">
                                                                <?php esc_html_e( 'per person/night', 'tourfic' ); ?>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="reserve tf-t-c">
                                                    <div class="tf-btn">
                                                        <button class="btn-styled hotel-room-availability tf-sml-btn"
                                                                type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
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
            </div>
            
        </div>
    </div>

    
</div>