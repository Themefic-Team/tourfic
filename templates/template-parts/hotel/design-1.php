<div class="tf-single-page tf-template-global tf-hotel-design-1">
    <div class="tf-tour-single">
        <div class="tf-template-container">
            <div class="tf-container-inner">
                <!-- Single Hotel Heading Section start -->
                <div class="tf-section tf-single-head">
                    <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                        <div class="tf-head-title">
                            <h1><?php

use Mpdf\Tag\Em;

 the_title(); ?></h1>
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
                        </div>
                    </div>
                </div>
                <!-- Single Hotel Body details End -->

                <div class="tf-hotel-descriptions tf-mrbottom-70">
                <?php the_content(); ?>
                </div>
                
                <?php if ( $features ) { ?>
                <div class="tf-hotel-single-features">
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
            </div>
            
        </div>
    </div>

    
</div>