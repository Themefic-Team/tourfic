<?php

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Car_Rental\Pricing;
use \Tourfic\App\TF_Review;
?>
<?php
$tf_pickup_date = !empty($_GET['pickup_date']) ? $_GET['pickup_date'] : '';
$tf_dropoff_date = !empty($_GET['dropoff_date']) ? $_GET['dropoff_date'] : '';
$tf_pickup_time = !empty($_GET['pickup_time']) ? $_GET['pickup_time'] : '';
$tf_dropoff_time = !empty($_GET['dropoff_time']) ? $_GET['dropoff_time'] : '';
$total_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time); 
$tf_cars_slug = get_option('car_slug');
?>
<div class="tf-single-car-section">
    <div class="tf-single-booking-bar">
        <div class="tf-car-template-container">
            <div class="tf-top-booking-bar tf-flex tf-flex-space-bttn tf-flex-align-center">
                <div class="tf-details-menu">
                    <ul>
                        <li class="active" data-menu="<?php echo esc_attr('tf-description'); ?>">
                            <a class="tf-hashlink" href="#tf-description">
                                <?php esc_html_e("Description", "tourfic"); ?>
                            </a>
                        </li>
                        <li data-menu="<?php echo esc_attr('tf-car-info'); ?>">
                            <a class="tf-hashlink" href="#tf-car-info">
                                <?php esc_html_e("Car info", "tourfic"); ?>
                            </a>
                        </li>
                        <li data-menu="<?php echo esc_attr('tf-benefits'); ?>">
                            <a class="tf-hashlink" href="#tf-benefits">
                                <?php esc_html_e("Benefits", "tourfic"); ?>
                            </a>
                        </li>
                        <li data-menu="<?php echo esc_attr('tf-inc-exc'); ?>">
                            <a class="tf-hashlink" href="#tf-inc-exc">
                                <?php esc_html_e("Include/Excluce", "tourfic"); ?>
                            </a>
                        </li>
                        <li data-menu="<?php echo esc_attr('tf-location'); ?>">
                            <a class="tf-hashlink" href="#tf-location">
                                <?php esc_html_e("Location", "tourfic"); ?>
                            </a>
                        </li>
                        <li data-menu="<?php echo esc_attr('tf-reviews'); ?>">
                            <a class="tf-hashlink" href="#tf-reviews">
                                <?php esc_html_e("Reviews", "tourfic"); ?>
                            </a>
                        </li>
                        <li data-menu="<?php echo esc_attr('tf-faq'); ?>">
                            <a class="tf-hashlink" href="#tf-faq">
                                <?php esc_html_e("FAQ's", "tourfic"); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tf-top-bar-booking tf-flex tf-flex-gap-32">
                    <div class="tf-price-header">
                        <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                        <?php echo $total_prices['sale_price'] ? wc_price($total_prices['sale_price']) : '' ?></h2>
                        <p><?php echo Pricing::is_taxable($meta); ?></p>
                    </div>
                    <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 tf-back-to-booking">
                        <?php esc_html_e( apply_filters("tf_car_booking_form_submit_button_text", 'Continue' ), 'tourfic' ); ?>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tf-car-template-container">
        <div class="tf-container-inner">
            <div class="tf-single-car-details-warper">
                <div class="tf-car-details-column">
                    <div class="tf-car-title">
                        <h1><?php the_title(); ?></h1>
                        <div class="breadcrumb">
                            <ul>
                                <li><a href="<?php echo site_url(); ?>"><?php esc_html_e( "Home", "tourfic" ) ?></a></li>
                                <li>/</li>
                                <li><a href="<?php echo site_url(); ?>/<?php echo esc_attr($tf_cars_slug); ?>"><?php esc_html_e( "Cars", "tourfic" ) ?></a></li>
                                <li>/</li>
                                <li><?php the_title(); ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tf-car-hero-gallery">
                        <div class="tf-featured-car">
                            <img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>" alt="<?php esc_html_e( 'Car Image', 'tourfic' ); ?>">

                            <div class="tf-featured-reviews">
                                <a href="#tf-review" class="tf-single-rating">
                                    <span>
                                        <?php 
                                        if($comments){
                                        echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments )); 
                                        }else{ 
                                        ?>
                                        0.0
                                        <?php } ?>
                                        <i class="fa-solid fa-star"></i>
                                    </span> (<?php echo Pricing::get_total_trips($post_id); ?> <?php esc_html_e( "trips", "tourfic" ) ?>)
                                </a>
                            </div>
                            
                            <div class="tf-wish-and-share">
                            <?php
                            // Wishlist
                            if ( Helper::tfopt( 'wl-bt-for' ) && in_array( '2', Helper::tfopt( 'wl-bt-for' ) ) ) { 
                                if ( is_user_logged_in() ) {
                                if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) {
                            ?>
                            <a class="tf-icon tf-wishlist-box tf-wishlist <?php echo $has_in_wishlist ? esc_attr('actives') : '' ?>">
                                <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) { echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"'; } ?>></i>
                            </a>
                            <?php } } else{ 
                            if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) {    
                            ?>
                            <a class="tf-icon tf-wishlist-box tf-wishlist <?php echo $has_in_wishlist ? esc_attr('actives') : '' ?>">
                                <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) { echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"'; } ?>></i>
                            </a>
                            <?php } } } ?>
                            
                            <!-- Share Section -->
                            <?php 
                            if ( ! $disable_share_opt == '1' ) { ?>
                            <div class="tf-share tf-off-canvas-share-box">
                                
                                <a href="#" class="tf-share-toggle tf-icon tf-social-box"
                                data-toggle="true">
                                    <i class="ri-share-line"></i>
                                </a>
                                <div id="dropdown-share-center" class="share-car-content">
                                    <div class="tf-dropdown-share-content">
                                        <h4><?php esc_html_e("Share with friends", "tourfic"); ?></h4>
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

                        <div class="tf-gallery tf-flex tf-flex-gap-16">
                        <?php 
                        $gallery_count = 1;
                            if ( ! empty( $gallery_ids ) ) {
                            foreach ( $gallery_ids as $key => $gallery_item_id ) {
                            $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                        ?>
                            <a class="<?php echo $gallery_count==4 ? esc_attr( 'tf-gallery-more' ) : ''; ?> " href="<?php echo esc_url($image_url); ?>" id="tour-gallery" data-fancybox="tour-gallery">
                                <img src="<?php echo esc_url($image_url); ?>">
                            </a>
                        <?php $gallery_count++; } } ?>
                        </div>
                    </div>

                    <div class="tf-details-menu">
                        <ul>
                            <li class="active" data-menu="<?php echo esc_attr('tf-description'); ?>">
                                <a class="tf-hashlink" href="#tf-description">
                                    <?php esc_html_e("Description", "tourfic"); ?>
                                </a>
                            </li>
                            <li data-menu="<?php echo esc_attr('tf-car-info'); ?>">
                                <a class="tf-hashlink" href="#tf-car-info">
                                    <?php esc_html_e("Car info", "tourfic"); ?>
                                </a>
                            </li>
                            <li data-menu="<?php echo esc_attr('tf-benefits'); ?>">
                                <a class="tf-hashlink" href="#tf-benefits">
                                    <?php esc_html_e("Benefits", "tourfic"); ?>
                                </a>
                            </li>
                            <li data-menu="<?php echo esc_attr('tf-inc-exc'); ?>">
                                <a class="tf-hashlink" href="#tf-inc-exc">
                                    <?php esc_html_e("Include/Excluce", "tourfic"); ?>
                                </a>
                            </li>
                            <li data-menu="<?php echo esc_attr('tf-location'); ?>">
                                <a class="tf-hashlink" href="#tf-location">
                                    <?php esc_html_e("Location", "tourfic"); ?>
                                </a>
                            </li>
                            <li data-menu="<?php echo esc_attr('tf-reviews'); ?>">
                                <a class="tf-hashlink" href="#tf-reviews">
                                    <?php esc_html_e("Reviews", "tourfic"); ?>
                                </a>
                            </li>
                            <li data-menu="<?php echo esc_attr('tf-faq'); ?>">
                                <a class="tf-hashlink" href="#tf-faq">
                                    <?php esc_html_e("FAQ's", "tourfic"); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tf-template-part tf-flex tf-flex-gap-32 tf-flex-direction-column">
                        <?php
                        if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-car-layout'] ) ) {
                            foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-car-layout'] as $section ) {
                                if ( ! empty( $section['car-section-status'] ) && $section['car-section-status'] == "1" && ! empty( $section['car-section-slug'] ) ) {
                                    include TF_TEMPLATE_PART_PATH . 'car/design-1/' . $section['car-section-slug'] . '.php';
                                }
                            }
                        } else {
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/description.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/car-info.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/benefits.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/inc-exc.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/location.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/reviews.php';
                            include TF_TEMPLATE_PART_PATH . 'car/design-1/faq.php';
                        }
                        ?>
                    </div>

                </div>
                <?php do_action("tf_car_before_single_booking_form"); ?>
                <div class="tf-car-booking-form">

                    <div class="tf-price-header tf-mb-30">
                        <h2><?php esc_html_e("Total:", "tourfic"); ?> 
                        <?php if(!empty($total_prices['regular_price'])){ ?><del><?php echo wc_price($total_prices['regular_price']); ?></del>  <?php } ?>
                        <?php echo $total_prices['sale_price'] ? wc_price($total_prices['sale_price']) : '' ?></h2>
                        <p><?php echo Pricing::is_taxable($meta); ?></p>
                    </div>

                    <?php if(function_exists( 'is_tf_pro' ) && is_tf_pro()){ ?>
                    <div class="tf-extra-added-info">
                        <div class="tf-extra-added-box tf-flex tf-flex-gap-16 tf-flex-direction-column">
                            <h3><?php esc_html_e("Extras added", "tourfic"); ?></h3>
                            <div class="tf-added-extra tf-flex tf-flex-gap-16 tf-flex-direction-column">
                                
                            </div>
                        </div>
                    </div>
                    <?php } ?>


                    <div class="tf-date-select-box">

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 17.4161L14.1247 13.2913C16.4028 11.0133 16.4028 7.31977 14.1247 5.04171C11.8467 2.76365 8.15327 2.76365 5.87521 5.04171C3.59715 7.31977 3.59715 11.0133 5.87521 13.2913L10 17.4161ZM10 19.7731L4.6967 14.4698C1.76777 11.5408 1.76777 6.79214 4.6967 3.8632C7.62563 0.934271 12.3743 0.934271 15.3033 3.8632C18.2322 6.79214 18.2322 11.5408 15.3033 14.4698L10 19.7731ZM10 10.8332C10.9205 10.8332 11.6667 10.087 11.6667 9.1665C11.6667 8.24603 10.9205 7.49984 10 7.49984C9.0795 7.49984 8.33333 8.24603 8.33333 9.1665C8.33333 10.087 9.0795 10.8332 10 10.8332ZM10 12.4998C8.15905 12.4998 6.66667 11.0074 6.66667 9.1665C6.66667 7.32555 8.15905 5.83317 10 5.83317C11.8409 5.83317 13.3333 7.32555 13.3333 9.1665C13.3333 11.0074 11.8409 12.4998 10 12.4998Z" fill="#566676"/>
                                        </svg>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php esc_html_e("Pick-up", "tourfic"); ?></h5>
                                        <input type="text" placeholder="Pick Up Location" id="tf_pickup_location" value="<?php echo !empty($_GET['pickup']) ? esc_html($_GET['pickup']) : '' ?>" />
                                        <input type="hidden" id="tf_pickup_location_id" value="<?php echo !empty($_GET['pickup']) ? esc_html($_GET['pickup']) : '' ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 17.4161L14.1247 13.2913C16.4028 11.0133 16.4028 7.31977 14.1247 5.04171C11.8467 2.76365 8.15327 2.76365 5.87521 5.04171C3.59715 7.31977 3.59715 11.0133 5.87521 13.2913L10 17.4161ZM10 19.7731L4.6967 14.4698C1.76777 11.5408 1.76777 6.79214 4.6967 3.8632C7.62563 0.934271 12.3743 0.934271 15.3033 3.8632C18.2322 6.79214 18.2322 11.5408 15.3033 14.4698L10 19.7731ZM10 10.8332C10.9205 10.8332 11.6667 10.087 11.6667 9.1665C11.6667 8.24603 10.9205 7.49984 10 7.49984C9.0795 7.49984 8.33333 8.24603 8.33333 9.1665C8.33333 10.087 9.0795 10.8332 10 10.8332ZM10 12.4998C8.15905 12.4998 6.66667 11.0074 6.66667 9.1665C6.66667 7.32555 8.15905 5.83317 10 5.83317C11.8409 5.83317 13.3333 7.32555 13.3333 9.1665C13.3333 11.0074 11.8409 12.4998 10 12.4998Z" fill="#566676"/>
                                        </svg>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php esc_html_e("Drop-off", "tourfic"); ?></h5>
                                        <input type="text" placeholder="Drop Off Location" id="tf_dropoff_location" value="<?php echo !empty($_GET['dropoff']) ? esc_html($_GET['dropoff']) : '' ?>" />
                                        <input type="hidden" id="tf_dropoff_location_id" value="<?php echo !empty($_GET['pickup']) ? esc_html($_GET['pickup']) : '' ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.49996 0.833496V2.50016H12.5V0.833496H14.1666V2.50016H17.5C17.9602 2.50016 18.3333 2.87326 18.3333 3.3335V16.6668C18.3333 17.1271 17.9602 17.5002 17.5 17.5002H2.49996C2.03973 17.5002 1.66663 17.1271 1.66663 16.6668V3.3335C1.66663 2.87326 2.03973 2.50016 2.49996 2.50016H5.83329V0.833496H7.49996ZM16.6666 9.16683H3.33329V15.8335H16.6666V9.16683ZM6.66663 10.8335V12.5002H4.99996V10.8335H6.66663ZM10.8333 10.8335V12.5002H9.16662V10.8335H10.8333ZM15 10.8335V12.5002H13.3333V10.8335H15ZM5.83329 4.16683H3.33329V7.50016H16.6666V4.16683H14.1666V5.8335H12.5V4.16683H7.49996V5.8335H5.83329V4.16683Z" fill="#566676"/>
                                        </svg>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php esc_html_e("Pick-up date", "tourfic"); ?></h5>
                                        <input type="text" placeholder="Pick Up Date" class="tf_pickup_date" value="<?php echo !empty($_GET['pickup_date']) ? esc_html($_GET['pickup_date']) : '' ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.99996 18.3332C5.39758 18.3332 1.66663 14.6022 1.66663 9.99984C1.66663 5.39746 5.39758 1.6665 9.99996 1.6665C14.6023 1.6665 18.3333 5.39746 18.3333 9.99984C18.3333 14.6022 14.6023 18.3332 9.99996 18.3332ZM9.99996 16.6665C13.6819 16.6665 16.6666 13.6818 16.6666 9.99984C16.6666 6.31794 13.6819 3.33317 9.99996 3.33317C6.31806 3.33317 3.33329 6.31794 3.33329 9.99984C3.33329 13.6818 6.31806 16.6665 9.99996 16.6665ZM10.8333 9.99984H14.1666V11.6665H9.16662V5.83317H10.8333V9.99984Z" fill="#566676"/>
                                        </svg>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php esc_html_e("Time", "tourfic"); ?></h5>
                                        <input type="text" placeholder="Pick Up Time" class="tf_pickup_time" value="<?php echo !empty($_GET['pickup_time']) ? esc_html($_GET['pickup_time']) : '' ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.49996 0.833496V2.50016H12.5V0.833496H14.1666V2.50016H17.5C17.9602 2.50016 18.3333 2.87326 18.3333 3.3335V16.6668C18.3333 17.1271 17.9602 17.5002 17.5 17.5002H2.49996C2.03973 17.5002 1.66663 17.1271 1.66663 16.6668V3.3335C1.66663 2.87326 2.03973 2.50016 2.49996 2.50016H5.83329V0.833496H7.49996ZM16.6666 9.16683H3.33329V15.8335H16.6666V9.16683ZM6.66663 10.8335V12.5002H4.99996V10.8335H6.66663ZM10.8333 10.8335V12.5002H9.16662V10.8335H10.8333ZM15 10.8335V12.5002H13.3333V10.8335H15ZM5.83329 4.16683H3.33329V7.50016H16.6666V4.16683H14.1666V5.8335H12.5V4.16683H7.49996V5.8335H5.83329V4.16683Z" fill="#566676"/>
                                        </svg>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php esc_html_e("Drop-off date", "tourfic"); ?></h5>
                                        <input type="text" placeholder="Drop Off Date" class="tf_dropoff_date" value="<?php echo !empty($_GET['dropoff_date']) ? esc_html($_GET['dropoff_date']) : '' ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4">
                                    <div class="icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.99996 18.3332C5.39758 18.3332 1.66663 14.6022 1.66663 9.99984C1.66663 5.39746 5.39758 1.6665 9.99996 1.6665C14.6023 1.6665 18.3333 5.39746 18.3333 9.99984C18.3333 14.6022 14.6023 18.3332 9.99996 18.3332ZM9.99996 16.6665C13.6819 16.6665 16.6666 13.6818 16.6666 9.99984C16.6666 6.31794 13.6819 3.33317 9.99996 3.33317C6.31806 3.33317 3.33329 6.31794 3.33329 9.99984C3.33329 13.6818 6.31806 16.6665 9.99996 16.6665ZM10.8333 9.99984H14.1666V11.6665H9.16662V5.83317H10.8333V9.99984Z" fill="#566676"/>
                                        </svg>
                                    </div>
                                    <div class="info-select">
                                        <h5><?php esc_html_e("Time", "tourfic"); ?></h5>
                                        <input type="text" placeholder="Drop Off Time" class="tf_dropoff_time" value="<?php echo !empty($_GET['dropoff_time']) ? esc_html($_GET['dropoff_time']) : '' ?>" />
                                        <input type="hidden" value="<?php echo esc_attr($post_id); ?>" id="post_id" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tf-form-submit-btn">
                            <div class="error-notice"></div>
                            <?php 
                            if($car_deposit_type=='fixed'){
                                $due_amount = $car_deposit_amount;
                            }
                            if($car_deposit_type=='percent'){
                                $due_amount = ($total_prices['sale_price'] * $car_deposit_amount)/100;
                            }
                            if( function_exists( 'is_tf_pro' ) && is_tf_pro() && '2'==$car_booking_by ){ ?>
                                <button class="tf-flex tf-flex-align-center tf-flex-justify-center booking-process tf-final-step tf-flex-gap-8">
                                    <?php esc_html_e( apply_filters("tf_car_booking_form_submit_button_text", 'Continue' ), 'tourfic' ); ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            <?php }else{ ?>
                                <?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($car_allow_deposit) && $car_deposit_type!='none' && !empty($car_deposit_amount) ){  ?>
                                    <div class="tf-partial-payment-button tf-flex tf-flex-direction-column tf-flex-gap-16">
                                        <button class="tf-flex tf-flex-align-center tf-partial-button tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('yes'); ?>">
                                            <?php esc_html_e( 'Part Pay', 'tourfic' ); ?> <?php echo wc_price($due_amount); ?>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.3299 10.3541L11.6835 10.0006L11.3299 9.64703L7.55867 5.87577L8.03008 5.40437L12.6263 10.0006L8.03008 14.5967L7.55867 14.1253L11.3299 10.3541Z" fill="#566676" stroke="#0866C4"/>
                                            </svg>
                                        </button>

                                        <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>" data-partial="<?php echo esc_attr('no'); ?>">
                                            <?php esc_html_e( 'Full Pay', 'tourfic' ); ?> <?php echo wc_price($total_prices['sale_price']); ?>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                <?php }else{ ?>
                                    <button class="tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-gap-8 <?php echo (empty($car_protection_section_status) || empty($car_protections)) && '3'!=$car_booking_by ? esc_attr('booking-process tf-final-step') : esc_attr('tf-car-booking'); ?>">
                                        <?php esc_html_e( apply_filters("tf_car_booking_form_submit_button_text", 'Continue' ), 'tourfic' ); ?>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="tf-instraction-btn tf-mt-16">
                            <span class="tf-instraction-showing"><?php esc_html_e("Instructions", "tourfic"); ?></span>
                            
                            <div class="tf-car-instraction-popup">
                                <div class="tf-instraction-popup-warp">

                                    <div class="tf-instraction-popup-header tf-flex tf-flex-align-center tf-flex-space-bttn">
                                        <div class="tf-close-popup">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15 5L5 15M5 5L15 15" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="tf-instraction-content-wraper">
                                        <?php echo $car_instructions_content; ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <script>
                        (function ($) {
                        $(document).ready(function () {

                            // flatpickr locale first day of Week
                            <?php Helper::tf_flatpickr_locale( "root" ); ?>

                            // Initialize the pickup date picker
                            var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
                                enableTime: false,
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
                                <?php Helper::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    // Update minDate for the dropoff date picker
                                    dropoffFlatpickr.set("minDate", dateStr);
                                }
                            });

                            // Initialize the dropoff date picker
                            var dropoffFlatpickr = $(".tf_dropoff_date").flatpickr({
                                enableTime: false,
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
                                <?php Helper::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                }
                            });

                            // Initialize the pickup time picker
                            var pickupTimeFlatpickr = $(".tf_pickup_time").flatpickr({
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: "H:i",

                                // flatpickr locale
                                <?php Helper::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    // Update minDate for the dropoff date picker
                                    dropoffTimeFlatpickr.set("minTime", dateStr);
                                }
                            });

                            var dropoffTimeFlatpickr = $(".tf_dropoff_time").flatpickr({
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: "H:i",
                                // flatpickr locale
                                <?php Helper::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    // Update minDate for the dropoff date picker
                                    dropoffFlatpickr.set("minDate", dateStr);
                                }
                            });

                        });
                    })(jQuery);

                    </script>
                        
                    </div>

                    <div class="tf-car-booking-popup">
                        <div class="tf-booking-popup-warp">

                            <div class="tf-booking-popup-header tf-flex tf-flex-align-center tf-flex-space-bttn">
                                <h3><?php esc_html_e("Additional information", "tourfic"); ?></h3>
                                <div class="tf-close-popup">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 5L5 15M5 5L15 15" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="tf-booking-content-wraper">

                            </div>

                        </div>
                    </div>

                    <?php do_action( 'tf_car_extras', $car_extras, $post_id ); ?>

                    <?php if(!empty($car_driverinfo_status)){ ?>
                    <div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16 tf-mb-30">
                        <div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h3><?php esc_html_e("Driver details", "tourfic"); ?></h3>
                            <span>
                            <i class="ri-shield-check-line"></i> <?php esc_html_e("Verified", "tourfic"); ?>
                            </span>
                        </div>
                        <div class="tf-driver-photo tf-flex tf-flex-gap-16">
                            <?php if(!empty($driver_image)){ ?>
                            <img src="<?php echo esc_url($driver_image); ?>">
                            <?php } ?>
                            <div class="tf-driver-info">
                                <?php if(!empty($driver_name)){ ?>
                                <h4><?php echo esc_attr($driver_name); ?></h4>
                                <?php } ?>
                                <?php if(!empty($driver_age)){ ?>
                                   <p> <?php esc_html_e("Age", "tourfic"); ?> <?php echo esc_attr($driver_age); ?> <?php esc_html_e("Years", "tourfic"); ?>
                                    </p>
                                <?php } ?>

                                <div class="tf-driver-contact-info">
                                    <ul class="tf-flex tf-flex-gap-16">
                                        <?php if(!empty($driver_email)){ ?>
                                        <li>
                                            <a href="mailto: <?php echo esc_attr($driver_email); ?>">
                                                <i class="ri-mail-line"></i>
                                                <div class="tf-tooltip-info">
                                                    <p><?php echo esc_attr($driver_email); ?></p>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if(!empty($driver_phone)){ ?>
                                        <li>
                                            <a href="tel: <?php echo esc_attr($driver_phone); ?>">
                                                <i class="ri-phone-line"></i>
                                                <div class="tf-tooltip-info">
                                                    <p><?php echo esc_attr($driver_phone); ?></p>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if(!empty($driver_address)){ ?>
                                        <li>
                                            <a href="https://maps.google.com/maps?q=<?php echo esc_html($driver_address); ?>" target="_blank">
                                                <i class="ri-map-pin-user-line"></i>
                                                <div class="tf-tooltip-info">
                                                    <p><?php echo esc_attr($driver_address); ?></p>
                                                </div>
                                            </a>

                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if(!empty($car_information_section_status)){ ?>
                    <div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16">
                        <div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h3><?php esc_html_e("Owner Information", "tourfic"); ?></h3>
                        </div>
                        <div class="tf-driver-photo tf-flex tf-flex-gap-16">
                            <?php if(!empty($car_owner_owner_image)){ ?>
                            <img src="<?php echo esc_url($car_owner_owner_image); ?>">
                            <?php } ?>
                            <div class="tf-driver-info">
                                <?php if(!empty($car_owner_name)){ ?>
                                <h4><?php echo esc_attr($car_owner_name); ?></h4>
                                <?php } ?>

                                <div class="tf-driver-contact-info">
                                    <ul class="tf-flex tf-flex-gap-16">
                                        <?php if(!empty($car_owner_email)){ ?>
                                        <li>
                                            <a href="mailto: <?php echo esc_attr($car_owner_email); ?>">
                                                <i class="ri-mail-line"></i>
                                                <div class="tf-tooltip-info">
                                                    <p><?php echo esc_attr($car_owner_email); ?></p>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if(!empty($car_owner_phone)){ ?>
                                        <li>
                                            <a href="tel: <?php echo esc_attr($car_owner_phone); ?>">
                                                <i class="ri-phone-line"></i>
                                                <div class="tf-tooltip-info">
                                                    <p><?php echo esc_attr($car_owner_phone); ?></p>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if(!empty($car_owner_fax)){ ?>
                                        <li>
                                            <a href="tel: <?php echo esc_attr($car_owner_fax); ?>">
                                                <i class="fa-solid fa-fax"></i>
                                                <div class="tf-tooltip-info">
                                                    <p><?php echo esc_attr($car_owner_fax); ?></p>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if(!empty($car_owner_website)){ ?>
                                        <li>
                                            <a href="<?php echo esc_url($car_owner_website); ?>">
                                                <i class="ri-link"></i>
                                                <div class="tf-tooltip-info">
                                                    <p><?php echo esc_url($car_owner_website); ?></p>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
                <?php do_action("tf_car_after_single_booking_form"); ?>
            </div>
            <?php 
            if(!empty($tc)){ ?>
            <div class="tf-car-conditions-section">
                <?php if(!empty($tc_title)){ ?>
                <h3><?php echo esc_html($tc_title); ?></h3>
                <?php } ?>
                <table>
                    <?php 
                    foreach($tc as $singletc){ ?>
                    <tr>
                        <th><?php echo !empty($singletc['title']) ? esc_html($singletc['title']) : ''; ?></th>
                        <td><?php echo !empty($singletc['content']) ? wp_kses_post($singletc['content']) : ''; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
