<div class="tf-template-3">
<!--Hero section start -->
<div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background-image: url('.esc_url(get_the_post_thumbnail_url()).');' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
    <div class="tf-container">
        <div class="tf-hero-content">
            <div class="tf-wish-and-share">
                <?php
                // Wishlist
                if ( tfopt( 'wl-bt-for' ) && in_array( '2', tfopt( 'wl-bt-for' ) ) ) { 
                    if ( is_user_logged_in() ) {
                    if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                </a>
                <?php } } else{ 
                if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {    
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                </a>
                <?php } } } ?>
            
                
                <!-- Share Section -->
                <?php if ( ! $disable_share_opt == '1' ) { ?>
                <div class="tf-share">
                    <a href="#dropdown-share-center" class="share-toggle tf-icon tf-social-box"
                    data-toggle="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M14 4.33203C14 5.4366 13.1046 6.33203 12 6.33203C10.8954 6.33203 10 5.4366 10 4.33203C10 3.22746 10.8954 2.33203 12 2.33203C13.1046 2.33203 14 3.22746 14 4.33203Z" stroke="#FDF9F4" stroke-width="1.5"/>
                        <path d="M6 8C6 9.10457 5.10457 10 4 10C2.89543 10 2 9.10457 2 8C2 6.89543 2.89543 6 4 6C5.10457 6 6 6.89543 6 8Z" stroke="#FDF9F4" stroke-width="1.5"/>
                        <path d="M14 11.6641C14 12.7686 13.1046 13.6641 12 13.6641C10.8954 13.6641 10 12.7686 10 11.6641C10 10.5595 10.8954 9.66406 12 9.66406C13.1046 9.66406 14 10.5595 14 11.6641Z" stroke="#FDF9F4" stroke-width="1.5"/>
                        <path d="M5.81836 7.16371L10.1517 5.16406M5.81836 8.83073L10.1517 10.8304" stroke="#FDF9F4" stroke-width="1.5"/>
                        </svg>
                    </a>

                    <div id="dropdown-share-center" class="share-tour-content">
                        <div class="tf-dropdown-share-content">
                            <h4><?php _e("Share with friends", "tourfic"); ?></h4>
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
            <div class="tf-hero-bottom-area">
                <div class="tf-head-title">
                    <h1><?php echo get_the_title(); ?></h1>
                    <div class="tf-title-meta">
                        <i class="ri-map-pin-line"></i>
                        <a href="#tf-map"><?php echo esc_html( $address ); ?></a>
                    </div>
                </div>
                <div class="tf-hero-gallery-videos">
                    <?php
                    $hotel_video = ! empty( $meta['video'] ) ? $meta['video'] : '';
                    if ( !empty($hotel_video) ) { ?>
                    <div class="tf-hero-video tf-popup-buttons">
                        <a class="tf-tour-video" id="featured-video" href="<?php echo esc_url($hotel_video); ?>" data-fancybox="tour-video" >
                            <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Vector 3570" d="M10.5 5L12.5 5" stroke="#FDF9F4" stroke-width="1.5" stroke-linecap="round"/>
                            <path id="Rectangle 368" d="M1.5 8C1.5 4.70017 1.5 3.05025 2.52513 2.02513C3.55025 1 5.20017 1 8.5 1H9.5C12.7998 1 14.4497 1 15.4749 2.02513C16.5 3.05025 16.5 4.70017 16.5 8V10C16.5 13.2998 16.5 14.9497 15.4749 15.9749C14.4497 17 12.7998 17 9.5 17H8.5C5.20017 17 3.55025 17 2.52513 15.9749C1.5 14.9497 1.5 13.2998 1.5 10V8Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 369" d="M16.5 5.90585L16.6259 5.80196C18.7417 4.05623 19.7996 3.18336 20.6498 3.60482C21.5 4.02628 21.5 5.42355 21.5 8.21808V9.78192C21.5 12.5765 21.5 13.9737 20.6498 14.3952C19.7996 14.8166 18.7417 13.9438 16.6259 12.198L16.5 12.0941" stroke="#FDF9F4" stroke-width="1.5" stroke-linecap="round"/>
                            </g>
                            </svg>
                        </a>
                    </div>
                    <?php } ?>
                    <?php 
                    if ( ! empty( $gallery_ids ) ) {
                    ?>
                    <div class="tf-hero-hotel tf-popup-buttons">
                        <a href="#">
                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"/>
                            <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            </svg>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Hero section End -->


<!--Content section end -->
<div class="tf-content-wrapper">
    
    <div class="tf-container">
    
    <!-- Hotel details Srart -->
    <div class="tf-details" id="tf-hotel-overview">
        <div class="tf-details-left">
            <!-- menu section Start -->
            <div class="tf-details-menu">
                <ul>
                    <li><a class="tf-hashlink" href="#tf-hotel-overview">Overview</a></li>
                    <li><a href="#tf-hotel-rooms">Rooms</a></li>
                    <li><a href="#tf-hotel-facilities">Facilities</a></li>
                    <li><a href="#tf-hotel-reviews">Reviews</a></li>
                    <li><a href="#tf-hotel-faq">FAQ's</a></li>
                    <li><a href="#tf-hotel-policies">Policies</a></li>
                </ul>
            </div>
            <!-- menu section End -->


            <!--Overview Start -->
            <div class="tf-overview-wrapper">
                <div class="tf-overview-description">
                    <?php the_content(); ?>
                </div>
            </div>
            <!--Overview End -->
            
            <!--Popular Features -->
            <div class="tf-overview-wrapper">
                <div class="tf-overview-popular-facilities">
                    <h3><?php _e("Popular facilities", "tourfic"); ?></h3>
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
            </div>
            <!--Popular Features -->

            <!--Booking form start -->
            <div id="availability" class="tf-booking-form-wrapper">
                <?php tf_hotel_sidebar_booking_form(); ?>
            </div>
            <!-- Booking form end -->

            <!--Available rooms start -->
            <div class="tf-available-rooms-wrapper" id="tf-hotel-rooms">
                <div class="tf-available-rooms-head">
                    <h2 class=""><?php _e("Available rooms", "tourfic"); ?></h2>
                    <div class="tf-filter">
                        <img src="./assets/image/filter.png" alt="">
                    </div>
                </div>
                
                <!--Available rooms start -->
                <div class="tf-available-rooms tf-rooms" id="rooms">
                <!-- Loader Image -->
                <div id="tour_room_details_loader">
                    <div id="tour-room-details-loader-img">
                        <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                    </div>
                </div>
                <?php if ( $rooms ) : ?>
                <?php foreach ( $rooms as $room_id => $room ) {
                $enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
                if ( $enable == '1' ) {
                    $footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
                    $bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
                    $adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
                    $child_number = ! empty( $room['child'] ) ? $room['child'] : '0';
                    $total_person = $adult_number + $child_number;
                    $pricing_by   = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                    $avil_by_date = ! empty( $room['avil_by_date'] ) ? ! empty( $room['avil_by_date'] ) : false;
                    $multi_by_date = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
                    $child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";

                    // Hotel Room Discount Data
                    $hotel_discount_type = !empty($room["discount_hotel_type"]) ? $room["discount_hotel_type"] : "none";
                    $hotel_discount_amount = !empty($room["discount_hotel_price"]) ? $room["discount_hotel_price"] : '';

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
                                foreach($prices as $value) {
                                    if($hotel_discount_type == "percent") {
                                        $discount_prices[] = floatval( preg_replace( '/[^\d.]/', '', number_format( $value - ( ( $value / 100 ) * $hotel_discount_amount ), 2 ) ) );
                                    } else if( $hotel_discount_type == "fixed") {
                                        $discount_prices[] = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $value - $hotel_discount_amount ), 2 ) ) );;
                                    }
                                }
                                $discount_price = $discount_prices ? ( min( $discount_prices ) != max( $discount_prices ) ? wc_format_price_range( min( $discount_prices ), max( $discount_prices ) ) : wc_price( min( $discount_prices ) ) ) : "";
                                $price = $prices ? ( min( $prices ) != max( $prices ) ? wc_format_price_range( min( $prices ), max( $prices ) ) : wc_price( min( $prices ) ) ) : wc_price( 0 );
                            } else {
                                $price = ! empty( $range_price[0] ) ? $range_price[0] : 0;
                                if($hotel_discount_type == "percent") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $price - ( ( $price / 100 ) * $hotel_discount_amount ), 2 ) ) );
                                    $discount_price = wc_price($discount_price);
                                }
                                if($hotel_discount_type == "fixed") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $price - $hotel_discount_amount ), 2 ) ) );;
                                    $discount_price = wc_price($discount_price);
                                }
                                $price = wc_price( $price );
                            }
                        }else{
                            if ( $pricing_by == '1' ) {
                                $price = ! empty( $room['price'] ) ? $room['price'] : '0.0';
                                if($hotel_discount_type == "percent") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $price - ( ( $price / 100 ) * $hotel_discount_amount ), 2 ) ) );
                                    $discount_price = wc_price($discount_price);
                                }else if($hotel_discount_type == "fixed") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $price - $hotel_discount_amount ), 2 ) ) );;
                                    $discount_price = wc_price($discount_price);
                                }
                                $price = wc_price($price);
                            } else {
                                $price = ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0';
                                if($hotel_discount_type == "percent") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $price - ( ( $price / 100 ) * $hotel_discount_amount ), 2 ) ) );
                                    $discount_price = wc_price($discount_price);
                                }else if($hotel_discount_type == "fixed") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $price - $hotel_discount_amount ), 2 ) ) );;
                                    $discount_price = wc_price($discount_price);
                                }
                                $price = wc_price($price);
                            }
                        }
                    } else {
                        if ( $pricing_by == '1' ) {
                            $price = ! empty( $room['price'] ) ? $room['price'] : '0.0';
                            if($hotel_discount_type == "percent") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $price - ( ( $price / 100 ) * $hotel_discount_amount ), 2 ) ) );
                                $discount_price = wc_price($discount_price);
                            }
                            if($hotel_discount_type == "fixed") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $price - $hotel_discount_amount ), 2 ) ) );;
                                $discount_price = wc_price($discount_price);
                            }
                            $price = wc_price( $price );
                        } else {
                            $price =! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0';
                            if($hotel_discount_type == "percent") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $price - ( ( $price / 100 ) * $hotel_discount_amount ), 2 ) ) );
                                $discount_price = wc_price($discount_price);
                            } else if($hotel_discount_type == "fixed") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $price - $hotel_discount_amount ), 2 ) );
                                $discount_price = wc_price($discount_price);
                            }
                            $price = wc_price( $price );
                        }
                    }
                    ?>
                    <div class="tf-available-room">
                        <div class="tf-available-room-gallery">
                            <?php 

                            $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                            if ( $tour_room_details_gall ) {
                                $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                            }

                            $room_preview_img = ! empty( $room['room_preview_img'] ) ? $room['room_preview_img'] : '';
                            if(!empty($room_preview_img)){ ?>                     
                                <div class="tf-room-gallery">
                                    <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php _e("Room Image","tourfic"); ?>">
                                </div> 
                            <?php } ?>
                            <?php 
                                if ( ! empty( $tf_room_gallery_ids ) ) {
                                $gallery_limit = 1;
                                foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
                                $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                if($gallery_limit<3){
                            ?>
                            <?php 
                            if(count($tf_room_gallery_ids) > 1){ ?>
                                <?php if(1==$gallery_limit){ ?>
                                <div class="tf-room-gallery">
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php _e("Room Image","tourfic"); ?>">                               
                                </div>
                                <?php } ?>
                                <?php if(2==$gallery_limit){ ?>                     
                                <div class="tf-room-gallery tf-popup-buttons tf-room-detail-popup" data-uniqid="<?php echo !empty($room['unique_id']) ? $room['unique_id'].$room_id : '' ?>" data-hotel="<?php echo $post_id; ?>" style="background-image: url('<?php echo esc_url( $image_url ); ?>'); ">                                
                                <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="content">
                                    <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                                    <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </g>
                                </svg>
                                </div>
                                <?php } ?>
                            <?php } ?>

                            <?php } $gallery_limit++; } } ?>  
                        </div>
                        <div class="tf-available-room-content">
                            <div class="tf-available-room-content-left">
                                <h2 class="tf-section-title"><?php echo esc_html( $room['title'] ); ?></h2>
                                <ul>
                                    <?php if ( $footage ) { ?>
                                        <li><i class="fas fa-ruler-combined"></i> <?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></li>
                                    <?php } ?>
                                    <?php if ( $bed ) { ?>
                                        <li><i class="fas fa-bed"></i> <?php echo $bed; ?><?php _e( ' Number of Beds', 'tourfic' ); ?></li>
                                    <?php } ?>
                                    <?php if ( $adult_number ) { ?>
                                        <li><i class="fas fa-male"></i><i
                                        class="fas fa-female"></i> <?php echo $adult_number; ?><?php _e( ' Adults', 'tourfic' ); ?></li>
                                    <?php } ?>
                                    <?php if ( $child_number ) { ?>
                                        <li><i class="fas fa-baby"></i><?php echo $child_number; ?><?php _e( ' Children', 'tourfic' ); ?></li>
                                    <?php } ?>
                                    <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo !empty($room['unique_id']) ? $room['unique_id'].$room_id : '' ?>" data-hotel="<?php echo $post_id; ?>"><?php _e("View room details", "tourfic"); ?></a></li>
                                    
                                </ul>
                                <h4><?php _e("Other benefits", "tourfic"); ?></h4>
                                <ul>
                                <?php 
                                if( !empty($room['features']) ){
                                $tf_room_fec_key = 1;
                                foreach ( $room['features'] as $feature ) {
                                if ( $tf_room_fec_key < 6 ) {
                                $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                if ( ! empty( $room_f_meta ) ) {
                                    $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                }
                                if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && !empty($room_f_meta['icon-fa']) ) {
                                    $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] )) {
                                    $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                }

                                $room_term = get_term( $feature ); ?>
                                <li>
                                    <?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
                                    <?php echo $room_term->name; ?>
                                </li>
                                <?php } $tf_room_fec_key++; } } ?>
                                <?php 
                                if(!empty($room['features'])){
                                    if(count($room['features']) >= 6){
                                    ?>
                                    
                                    <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo !empty($room['unique_id']) ? $room['unique_id'].$room_id : '' ?>" data-hotel="<?php echo $post_id; ?>"><?php _e("See all benefits", "tourfic"); ?></a></li>
                                    <?php
                                    }
                                }
                                ?>
                                </ul>
                            </div>
                            <div class="tf-available-room-content-right">
                                <div class="tf-cancellation-refundable-text">
                                    <span><?php _e("Free cancellation", "tourfic"); ?> 
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                        <path d="M7.99967 15.1673C4.31777 15.1673 1.33301 12.1825 1.33301 8.50065C1.33301 4.81875 4.31777 1.83398 7.99967 1.83398C11.6815 1.83398 14.6663 4.81875 14.6663 8.50065C14.6663 12.1825 11.6815 15.1673 7.99967 15.1673ZM7.99967 13.834C10.9452 13.834 13.333 11.4462 13.333 8.50065C13.333 5.55513 10.9452 3.16732 7.99967 3.16732C5.05415 3.16732 2.66634 5.55513 2.66634 8.50065C2.66634 11.4462 5.05415 13.834 7.99967 13.834ZM7.33301 5.16732H8.66634V6.50065H7.33301V5.16732ZM7.33301 7.83398H8.66634V11.834H7.33301V7.83398Z" fill="#595349"/>
                                        </svg>
                                    </span>
                                    <span><?php _e("Free refundable", "tourfic"); ?>  
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                        <path d="M7.99967 15.1673C4.31777 15.1673 1.33301 12.1825 1.33301 8.50065C1.33301 4.81875 4.31777 1.83398 7.99967 1.83398C11.6815 1.83398 14.6663 4.81875 14.6663 8.50065C14.6663 12.1825 11.6815 15.1673 7.99967 15.1673ZM7.99967 13.834C10.9452 13.834 13.333 11.4462 13.333 8.50065C13.333 5.55513 10.9452 3.16732 7.99967 3.16732C5.05415 3.16732 2.66634 5.55513 2.66634 8.50065C2.66634 11.4462 5.05415 13.834 7.99967 13.834ZM7.33301 5.16732H8.66634V6.50065H7.33301V5.16732ZM7.33301 7.83398H8.66634V11.834H7.33301V7.83398Z" fill="#595349"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="tf-available-room-off">
                                    <?php 
                                    if(!empty($hotel_discount_type) && !empty($hotel_discount_amount) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type )){ ?>
                                    <span>
                                        <?php echo ("percent" == $hotel_discount_type) ? esc_html($hotel_discount_amount).'% off' : wc_price($hotel_discount_amount). 'off'; ?>
                                    </span>

                                    <?php } ?>
                                </div>
                                <div class="tf-available-room-price">

                                <?php
                                if ( $pricing_by == '1' ) {
                                    if(!empty($discount_price )) {
                                        ?>
                                        <span class="tf-price">
                                            <span><del><?php echo $price; ?></del> <?php echo $discount_price; ?></span>
                                            <?php 
                                            if($multi_by_date){
                                                esc_html_e( '/per night', 'tourfic' );
                                            }else{
                                                esc_html_e( '/per day', 'tourfic' );
                                            } ?>
                                        </span>
                                        <?php
                                        $discount_price = "";
                                    } else if($hotel_discount_type == "none") {
                                        ?>
                                        <span class="tf-price">
                                            <span><?php echo $price; ?></span>
                                            <?php 
                                            if($multi_by_date){
                                                esc_html_e( '/per night', 'tourfic' );
                                            }else{
                                                esc_html_e( '/per day', 'tourfic' );
                                            } ?>
                                        </span>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                } else {
                                    if(!empty($discount_price )) {
                                        ?>
                                        <span class="tf-price">
                                            <span><del><?php echo $price; ?></del> <?php echo $discount_price; ?></span>
                                            <?php 
                                            if($multi_by_date){
                                                esc_html_e( '/per person/night', 'tourfic' );
                                            }else{
                                                esc_html_e( '/per person/day', 'tourfic' );
                                            } ?>
                                        </span>
                                        <?php
                                        $discount_price = "";
                                    } else if($hotel_discount_type == "none") {
                                        ?>
                                        <span class="tf-price">
                                            <span><?php echo $price; ?></span>
                                            <?php 
                                            if($multi_by_date){
                                                esc_html_e( '/per person/night', 'tourfic' );
                                            }else{
                                                esc_html_e( '/per person/day', 'tourfic' );
                                            } ?>
                                        </span>
                                        <?php
                                    }
                                    ?>
                                    
                                    <?php
                                }
                                ?>
                                </div>
                                                            
                                <a href="#availability" class="availability"><?php _e("Check Availability", "tourfic"); ?></a>
                            </div>

                        </div>
                    </div>
                    <?php } } ?>
                <?php endif; ?>

                </div>
                <!-- Available rooms end -->

            </div>
            <!-- Available rooms end -->



        </div>
        <div class="tf-details-right tf-sitebar-widgets">
            <div class="tf-whats-around tf-single-widgets">
                <h2 class="tf-section-title">What’s around?</h2>
                <ul>
                    <li>
                        <span><i class="fa-solid fa-umbrella-beach"></i> Patenga sea beach</span>
                        <span>5 km in drive</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-mosque"></i> Al aksha mosque</span>
                        <span>10 min walking</span>
                    </li>
                    
                    <li>
                        <span><i class="fa-solid fa-plane-up"></i> Singapore airport</span>
                        <span>2 km in drive</span>
                    </li>
                    
                    <li>
                        <span><i class="fa-solid fa-shop"></i> Jamuna future park</span>
                        <span>3 km in drive</span>
                    </li>
                </ul>
            </div>  
            
            <div class="tf-location tf-single-widgets">
                <h2 class="tf-section-title"><?php _e("Location", "tourfic"); ?></h2>
                <?php if ( !defined( 'TF_PRO' ) ) { ?>
                    <?php 
                    if( $address && $tf_openstreet_map!="default" && ( empty($address_latitude) || empty($address_longitude) ) ){ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } elseif( $address && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {
                    ?>
                        <div id="hotel-location" style="height: 250px"></div>
                        <script>
                            const map = L.map('hotel-location').setView([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], <?php echo $address_zoom; ?>);

                            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 20,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            const marker = L.marker([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], {alt: '<?php echo $address; ?>'}).addTo(map)
                                .bindPopup('<?php echo $address; ?>');
                        </script>
                    <?php }else{ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?>
                <?php } ?>
                
            </div>   
            
            
            <div class="tf-location tf-single-widgets">
                <?php
                global $current_user;
                // Check if user is logged in
                $is_user_logged_in = $current_user->exists();
                $post_id           = $post->ID;
                // Get settings value
                $tf_ratings_for = tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                $tf_settings_base = ! empty ( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
                if ( $comments ) {
                    $tf_overall_rate        = [];
                    tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                    tf_get_review_fields( $fields );
                ?>
                <h2 class="tf-section-title"><?php _e("Overall reviews", "tourfic"); ?></h2>
                <div class="tf-review-data-inner">
                    <div class="tf-review-data">
                        <div class="tf-review-data-average">
                            <h2><span>
                                <?php _e( sprintf( '%.1f', $total_rating ) ); ?>
                            </span>/<?php echo $tf_settings_base; ?></h2>
                        </div>
                        <div class="tf-review-all-info">
                            <p><?php _e("Excellent", "tourfic"); ?> <span><?php _e("Total", "tourfic"); ?> <?php tf_based_on_text( count( $comments ) ); ?></span></p>
                        </div>
                    </div>
                    <div class="tf-review-data-features">
                        <div class="tf-percent-progress">
                        <?php 
                        if ( $tf_overall_rate ) {
                        foreach ( $tf_overall_rate as $key => $value ) {
                        if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                            continue;
                        }
                        $value = tf_average_ratings( $value );
                        ?>
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label"><?php esc_html_e( $key, "tourfic" ); ?></p>
                                    <p class="feature-rating"> <?php echo $value; ?></p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: <?php echo tf_average_rating_percent( $value, tfopt( 'r-base' ) ); ?>%"></span>
                                </div>
                            </div>
                            <?php } } ?>
                                   
                        </div>
                    </div>
                </div>
                <a class="tf-all-reviews" href="#"><?php _e("See all reviews", "tourfic"); ?></a>
                <?php } ?>
                <button class="tf-review-open button">
                    <?php _e("Leave your review", "tourfic"); ?>
                </button>
                <?php
                // Review moderation notice
                echo tf_pending_review_notice( $post_id );
                ?>
                <?php
                if ( ! empty( $tf_ratings_for ) ) {
                    if ( $is_user_logged_in ) {
                    if ( in_array( 'li', $tf_ratings_for ) && ! tf_user_has_comments() ) {
                    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php _e("Leave your review", "tourfic"); ?></h3>
                    <p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php tf_review_form(); ?>
                </div>
                <?php
		            }
	            } else {
		        if ( in_array( 'lo', $tf_ratings_for ) ) {
			    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php _e("Leave your review", "tourfic"); ?></h3>
                    <p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php tf_review_form(); ?>
                </div>
                <?php } } } ?>
            </div>       
        </div>        
    </div>        
    <!-- Hotel details End -->
    
    <?php 
        if( !empty(tf_data_types(tfopt( 'tf-template' ))['single-hotel-layout']) ){
            foreach(tf_data_types(tfopt( 'tf-template' ))['single-hotel-layout'] as $section){
                if( !empty($section['hotel-section-status']) && $section['hotel-section-status']=="1" && !empty($section['hotel-section-slug']) ){
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/'.$section['hotel-section-slug'].'.php';
                }
            }
        }else{
            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/description.php';
            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/features.php';
            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/rooms.php';
            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/faq.php';
            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/review.php';
            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/trams-condition.php';
        }
        ?>
        
    <!-- Hotel facilities Srart -->
    <div class="tf-facilities-wrapper" id="tf-hotel-facilities">              
        <h2 class="tf-section-title">Property facilities</h2>                
        <div class="tf-facilities">  
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-headset"></i> Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-chess"></i>Sports and Leisure</h4>
                <ul>
                    <li>Table tennis</li>
                    <li>Coffee shop</li>
                    <li>BBQ facilities</li>
                    <li>Garden & terrace</li>
                    <li>Gym</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-brands fa-gripfire"></i>Safety & security</h4>
                <ul>
                    <li>Fire extinguishers</li>
                    <li>CCTV in common areas</li>
                    <li>Smoke alarms</li>
                    <li>Key access</li>
                    <li>Doctor/nurse on call</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-network-wired"></i>Internet access</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>

            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-chess"></i>Sports and Leisure</h4>
                <ul>
                    <li>Table tennis</li>
                    <li>Coffee shop</li>
                    <li>BBQ facilities</li>
                    <li>Garden & terrace</li>
                    <li>Gym</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-brands fa-gripfire"></i>Safety & security</h4>
                <ul>
                    <li>Fire extinguishers</li>
                    <li>CCTV in common areas</li>
                    <li>Smoke alarms</li>
                    <li>Key access</li>
                    <li>Doctor/nurse on call</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-network-wired"></i>Internet access</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
        </div>
        <h2 class="tf-section-title">Room facilities</h2>                
        <div class="tf-facilities">  
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
        </div>
    </div>
    <!--Content facilities end -->

    <?php
    if ( $comments ) { ?>
    <!-- Hotel reviews Srart -->
    <div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">         
        <h2 class="tf-section-title"><?php _e("Guest reviews", "tourfic"); ?></h2> 
        <p><?php _e("Total", "tourfic"); ?> <?php tf_based_on_text( count( $comments ) ); ?></p>
        <div class="tf-reviews-slider">
            <?php
            foreach ( $comments as $comment ) {
            // Get rating details
            $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
            if ( $tf_overall_rate == false ) {
                $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                $tf_overall_rate = tf_average_ratings( $tf_comment_meta );
            }
            $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
            $c_rating  = tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

            // Comment details
            $c_avatar      = get_avatar( $comment, '56' );
            $c_author_name = $comment->comment_author;
            $c_date        = $comment->comment_date;
            $c_content     = $comment->comment_content;
            ?>
            <div class="tf-reviews-item">
                <div class="tf-reviews-avater">
                    <?php echo $c_avatar; ?>
                </div>
                <div class="tf-reviews-text">
                    <h3><?php echo $c_rating; ?></h3>
                    <span class="tf-reviews-meta"><?php echo $c_author_name; ?>, <?php echo $c_date; ?></span>
                    <p><?php echo $c_content; ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <!--Content reviews end -->
    <?php } ?>


    <?php if ( $faqs ): ?>
    <!-- Hotel Questions Srart -->
    <div class="tf-questions-wrapper tf-section" id="tf-hotel-faq">
        <h2 class="tf-section-title">
        <?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : __( "Faq’s", 'tourfic' ); ?>
        </h2>            
        <div class="tf-questions">
            <div class="tf-questions-col">
                <?php 
                $faq_key = 1;    
                foreach ( $faqs as $key => $faq ): ?>
                <div class="tf-question <?php echo $faq_key==1 ? esc_attr( 'tf-active' ) : ''; ?>">
                    <div class="tf-faq-head">
                        <h3><?php echo esc_html( $faq['title'] ); ?>
                        <i class="fa-solid fa-chevron-down"></i></h3>
                    </div>
                    <div class="tf-question-desc" style="<?php echo $faq_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                        <?php echo wp_kses_post( $faq['description'] ); ?>
                    </div>
                </div>
                <?php $faq_key++; endforeach; ?>
                
            </div>
            
        </div>
    </div>

    <!-- Hotel Questions end -->
    <?php endif; ?>

    <?php if ( $tc ) { ?>
    <!-- Hotel Policies Starts -->        
    <div class="tf-policies-wrapper tf-section" id="tf-hotel-policies">            
        <h2 class="tf-section-title">
            <?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Hotel Terms & Conditions","tourfic"); ?>
        </h2>  
        <div class="tf-policies">
            <?php echo wpautop( $tc ); ?>
        </div>
    </div>
    <!-- Hotel Policies end -->
    <?php } ?>

    <?php 
    if ( ! empty( $gallery_ids ) ) {
    ?>
    <!-- Hotel PopUp Starts -->       
    <div class="tf-popup-wrapper tf-hotel-popup">
        <div class="tf-popup-inner">
            
            <div class="tf-popup-body">
                <?php 
                    if ( ! empty( $gallery_ids ) ) {
                    foreach ( $gallery_ids as $key => $gallery_item_id ) {
                    $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="" class="tf-popup-image">
                <?php } } ?>
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Hotel PopUp end -->  
    <?php } ?>


    <!-- Room PopUp Starts -->        
    <div class="tf-popup-wrapper tf-room-popup">
        
    </div>
    <!-- Room PopUp end --> 


    </div>
</div>
<!--Content section end -->
</div>