<?php

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Hotel\Hotel;

$tf_booking_type = '1';
$tf_hide_booking_form = '';
$tf_ext_booking_type = '';
$tf_ext_booking_code = '';
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
    $tf_ext_booking_code = !empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
}
?>
<div class="tf-single-page tf-template-global tf-hotel-design-1">
    <div class="tf-tour-single">
        <div class="tf-template-container">
            <div class="tf-container-inner">
                <!-- Single Hotel Heading Section start -->
                <div class="tf-section tf-single-head">
                    <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                        <div class="tf-head-title">
                            <h1><?php the_title(); ?></h1>
                            <?php if( !empty($locations )) : ?>
                                <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                                <?php if ( $locations ) { ?>
                                    <?php if ( !empty( $address ) ) {
                                        echo '<i class="fa-solid fa-location-dot"></i> ' . wp_kses_post($address) . ' –';
                                    } ?>
                                    <a href="<?php echo esc_url($first_location_url); ?>" class="more-hotel tf-d-ib">
                                        <?php
                                        /* translators: %s location name */
                                        printf( esc_html__( 'Show more hotels in %s', 'tourfic' ), esc_html($first_location_name) );
                                        ?>
                                    </a>
                            <?php } ?>
                            </div>
                        <?php endif; ?>
                        </div>
                        <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
                            <?php
                            // Wishlist
                            if($disable_wishlist_sec==0){
                                if ( is_user_logged_in() ) {
                                    if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) { ?>
                                    <div class="tf-icon tf-wishlist-box">
                                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) { echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"'; } ?>></i>
                                    </div>
                            <?php } }else{ 
                                if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) {   ?>
                                    <div class="tf-icon tf-wishlist-box">
                                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                        echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                                    } ?>></i>
                                    </div>
                                <?php }} ?>
                            <?php
                            }else{ 
                            if ( Helper::tfopt( 'wl-bt-for' ) && in_array( '1', Helper::tfopt( 'wl-bt-for' ) ) ) { 
                                if ( is_user_logged_in() ) {
                                if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) {
                            ?>
                            <div class="tf-icon tf-wishlist-box">
                            <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) { echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"'; } ?>></i>
                            </div>
                            <?php } } else{ 
                            if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) {    
                            ?>
                            <div class="tf-icon tf-wishlist-box">
                            <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                            } ?>></i>
                            </div>
                            <?php } } } } ?>

                            <!-- Share Section -->
                            <?php if ( ! $disable_share_opt == '1' ) { ?>
                            <div class="tf-share">
                                <a href="#dropdown-share-center" class="share-toggle tf-icon tf-social-box"
                                data-toggle="true">
                                <i class="fa-solid fa-share-nodes"></i>
                                </a>

                                <div id="dropdown-share-center" class="share-tour-content">
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
                </div>
                <!-- Single Hotel Heading Section End -->

                <!-- Single Hotel Body details start -->
                <div class="tf-single-details-wrapper tf-mt-30 tf-mb-40">
                    <div class="tf-single-details-inner tf-flex">
                        <div class="tf-column tf-tour-details-left">
                            <!-- Hotel Gallery Section -->
                            <div class="tf-hero-gallery tf-mrbottom-30">
                            <div class="tf-gallery-featured <?php echo empty($gallery_ids) ? esc_attr('tf-without-gallery-featured') : ''; ?>">
                                <img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>" alt="<?php esc_html_e( 'Hotel Image', 'tourfic' ); ?>">
                                <div class="featured-meta-gallery-videos">
                                    <div class="featured-column tf-gallery-box">
                                        <?php 
                                        if ( ! empty( $gallery_ids ) ) {
                                        ?>
                                        <a id="featured-gallery" href="#" class="tf-tour-gallery">
                                            <i class="fa-solid fa-camera-retro"></i><?php echo esc_html__("Gallery","tourfic"); ?>
                                        </a>
                                        <?php 
                                        }
                                        ?>

                                    </div>
                                    <?php
                                    $hotel_video = ! empty( $meta['video'] ) ? $meta['video'] : '';
                                    if ( !empty($hotel_video) ) { ?>
                                    <div class="featured-column tf-video-box">
                                        <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video" href="<?php echo esc_url($hotel_video); ?>">
                                            <i class="fa-solid fa-video"></i> <?php echo esc_html__("Video","tourfic"); ?>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="tf-single-review-box">
                                <?php if ( ! $disable_review_sec == '1' ) { ?>
                                    <?php
                                    if($comments){ ?>
                                    <a href="#tf-review" class="tf-single-rating">
                                        <span><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments )); ?></span> (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
                                    </a>
                                    <?php }else{ ?>
                                        <a href="#tf-review" class="tf-single-rating">
                                            <span><?php esc_html_e( "0.0", "tourfic" ) ?></span> (<?php esc_html_e( "0 review", "tourfic" ) ?>)
                                        </a>
                                    <?php } ?>
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
                                <a class="<?php echo $gallery_count==5 ? esc_attr( 'tf-gallery-more' ) : ''; ?> " id="tour-gallery" href="<?php echo esc_url($image_url); ?>" data-fancybox="tour-gallery"><img src="<?php echo esc_url($image_url); ?>"></a>
                                <?php $gallery_count++; } } ?>
                            </div>
                            </div>
                        </div>

                        <!-- SIdebar Tour single -->
                        <div class="tf-column tf-tour-details-right">
	                        <?php if(($tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1) || $tf_booking_type == 1) :?>
                                <div class="tf-tour-booking-box tf-box">
                                    <?php Hotel::tf_hotel_sidebar_booking_form(); ?>
                                </div>
                            <?php endif; ?>
                            <?php if( !empty($tf_ext_booking_code) && $tf_ext_booking_type == 2 ) : ?>
                                <div id="tf-external-booking-embaded-form" class="tf-tour-booking-box tf-box">
                                    <?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags()); ?>
                                </div>
                            <?php endif; ?>
                            <div class="tf-hotel-location-map">
                                <?php if ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map!="default" && (empty($address_latitude) || empty($address_longitude)) ) { ?>
                                    <div class="tf-hotel-location-preview show-on-map">
                                    <iframe src="https://maps.google.com/maps?q=<?php echo wp_kses_post($address); ?>&output=embed" width="100%" height="258" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                        <a href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>" class="map-pre" target="_blank"><i class="fa-solid fa-location-dot"></i></a>
                                    </div>
                                <?php } elseif ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {  ?>
                                    <div class="tf-hotel-location-preview show-on-map">
                                        <div id="hotel-location"></div>
                                    </div>
                                    <script>
                                        const map = L.map('hotel-location').setView([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], <?php echo esc_html($address_zoom); ?>);

                                        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                            maxZoom: 20,
                                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                        }).addTo(map);

                                        const marker = L.marker([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], {alt: '<?php echo esc_html($address); ?>'}).addTo(map)
                                            .bindPopup('<?php echo esc_html($address); ?>');
                                    </script>
                                <?php } elseif ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map=="default" && (empty($address_latitude) || empty($address_longitude)) ) {  ?>
                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="258" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                <?php } ?>

                                <!-- Pro Code -->
                                <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || (! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
                                    <?php 
                                    if( $tf_openstreet_map!="default" ){ ?>
                                    <div class="tf-hotel-location-preview show-on-map">
                                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="290"
                                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                                        <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="javascript:;">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </a>

                                    </div>
                                    <?php } ?>
                                    <?php if (  $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude) ) {  ?>
                                        <div class="tf-hotel-location-preview show-on-map">
                                            <div id="hotel-location"></div>
                                            <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="javascript:;">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </a>
                                        </div>
                                        <script>
                                            const map = L.map('hotel-location').setView([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], <?php echo esc_html($address_zoom); ?>);

                                            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                maxZoom: 20,
                                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                            }).addTo(map);

                                            const marker = L.marker([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], {alt: '<?php echo esc_html($address); ?>'}).addTo(map)
                                                .bindPopup('<?php echo esc_html($address); ?>');
                                        </script>
                                    <?php } ?>

                                    <?php if (  $tf_openstreet_map=="default" && (empty($address_latitude) || empty($address_longitude)) ) {  ?>
                                        <div class="tf-hotel-location-preview show-on-map">
                                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="290"
                                                    style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                                            <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="javascript:;">
                                                <i class="fa-solid fa-location-dot"></i>
                                            </a>

                                        </div>
                                    <?php } ?>

                                    <div style="display: none;" id="tf-hotel-google-maps">
                                        <div class="tf-hotel-google-maps-container">
                                            <?php
                                            if ( ! empty( $address ) ) { ?>
                                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;"
                                                        allowfullscreen="" loading="lazy"></iframe>
                                            <?php } else { ?>
                                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=17&output=embed" width="100%" height="550"
                                                        style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- Hotel Single Widget Hook are - start -->
                            <div class="tf-hotel-single-custom-widget-wrap">
                                <?php do_action( "tf_hotel_single_widgets" ); ?>
                            </div>
                            <!-- Hotel Single Widget Hook are - end -->
                        </div>
                    </div>
                </div>
               
                <?php 
                if(file_exists(TF_TEMPLATE_PART_PATH . 'hotel/design-1/places.php')) {
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/places.php';
                }
                ?>

                <?php 
                if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout']) ){
                    foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout'] as $section){
                        if( !empty($section['hotel-section-status']) && $section['hotel-section-status']=="1" && !empty($section['hotel-section-slug']) ){
                            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/'.$section['hotel-section-slug'].'.php';
                        }
                    }
                }else{
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/description.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/features.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/rooms.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/facilities.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/faq.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/review.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/trams-condition.php';
                }
                ?>
            </div>
            
        </div>
    </div>

    
</div>