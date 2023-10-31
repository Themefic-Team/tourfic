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
                            <a href="#tf-map"><?php echo esc_html( $location ); ?></a>
                        </div>
                    </div>
                    <div class="tf-hero-gallery-videos">
                        <?php
                        $tours_video = ! empty( $meta['tour_video'] ) ? $meta['tour_video'] : '';
                        if ( !empty($tours_video) ) { ?>
                        <div class="tf-hero-video tf-popup-buttons">
                            <a class="tf-tour-video" id="featured-video" href="<?php echo esc_url($tours_video); ?>" data-fancybox="tour-video">
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
            <div class="tf-details" id="tf-tour-overview">
                <div class="tf-details-left">

                    <?php 
                    if( !empty(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout-part-1']) ){
                        foreach(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout-part-1'] as $section){
                            if( !empty($section['tour-section-status']) && $section['tour-section-status']=="1" && !empty($section['tour-section-slug']) ){
                                include TF_TEMPLATE_PART_PATH . 'tour/design-2/'.$section['tour-section-slug'].'.php';
                            }
                        }
                    }else{
                        include TF_TEMPLATE_PART_PATH . 'tour/design-2/description.php';
                        include TF_TEMPLATE_PART_PATH . 'tour/design-2/information.php';
                        include TF_TEMPLATE_PART_PATH . 'tour/design-2/highlights.php';
                        include TF_TEMPLATE_PART_PATH . 'tour/design-2/include-exclude.php';
                        include TF_TEMPLATE_PART_PATH . 'tour/design-2/itinerary.php';
                        include TF_TEMPLATE_PART_PATH . 'tour/design-2/calendar.php';
                    }
                    ?>
                    
                </div>
                <div class="tf-details-right tf-sitebar-widgets">
                    <div class="tf-search-date-wrapper tf-single-widgets">
                        <h2 class="tf-section-title"><?php _e("Available Date", "tourfic"); ?></h2>
                        <?php echo tf_single_tour_booking_form( $post->ID ); ?>
                    </div>
                    <div class="tf-reviews tf-single-widgets">
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
                        <script>
                            $(".tf-template-3 .tf-review-open").click(function () {
                                $(".tf-template-3 .tf-review-form-wrapper").slideToggle();
                            });
                        </script>
                    </div>
                </div>
            </div>
            <!-- Hotel details End -->
            <?php 
            if( !empty(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout-part-2']) ){
                foreach(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout-part-2'] as $section){
                    if( !empty($section['tour-section-status']) && $section['tour-section-status']=="1" && !empty($section['tour-section-slug']) ){
                        include TF_TEMPLATE_PART_PATH . 'tour/design-2/'.$section['tour-section-slug'].'.php';
                    }
                }
            }else{
                include TF_TEMPLATE_PART_PATH . 'tour/design-2/faq.php';
                include TF_TEMPLATE_PART_PATH . 'tour/design-2/review.php';
                include TF_TEMPLATE_PART_PATH . 'tour/design-2/trams-condition.php';
            }
            ?>

            <!-- Tour Gallery PopUp Starts -->
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
            <!-- Tour Gallery PopUp end -->

        </div>
    </div>
    <!--Content section end -->

</div>