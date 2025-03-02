<?php
/**
 * Template: Single Hotel (Full Width)
 */

get_header();

/**
 * Query start
 */
while ( have_posts() ) : the_post(); 

// get post id
$post_id = $post->ID;

/**
 * Review query
 */
$args = array( 
	'post_id' => $post_id,
	'status'  => 'approve',
	'type'    => 'comment',
);
$comments_query = new WP_Comment_Query( $args ); 
$comments = $comments_query->comments;

/**
 * Get hotel meta values
 */
$meta = get_post_meta( $post_id, 'tf_hotel', true );

$disable_share_opt = !empty($meta['h-share']) ? $meta['h-share'] : '';
$disable_review_sec = !empty($meta['h-review']) ? $meta['h-review'] : '';

/**
 * Get global settings value
 */
$s_share = !empty(tfopt('h-share')) ? tfopt('h-share') : 0;
$s_review = !empty(tfopt('h-review')) ? tfopt('h-review') : 0;

/**
 * Disable Share Option
 */
$disable_share_opt = !empty($disable_share_opt) ? $disable_share_opt : $s_share;

/**
 * Disable Review Section
 */
$disable_review_sec = !empty($disable_review_sec) ? $disable_review_sec : $s_review;

/**
 * Assign all values to variables
 * 
 */

// Wishlist
$post_type = str_replace('tf_', '', get_post_type());
$has_in_wishlist = tf_has_item_in_wishlist($post_id);

/**
 * Get locations
 * 
 * hotel_location
 */
$locations = !empty(get_the_terms( $post_id, 'hotel_location' )) ? get_the_terms( $post_id, 'hotel_location' ) : '';
if ($locations) {
	$first_location_id   = $locations[0]->term_id;
	$first_location_term = get_term( $first_location_id );
	$first_location_name = $locations[0]->name;
    $first_location_slug = $locations[0]->slug;
	$first_location_url  = get_term_link( $first_location_term );
}

/**
 * Get features
 * 
 * hotel_feature
 */
$features  = !empty(get_the_terms( $post_id, 'hotel_feature' )) ? get_the_terms( $post_id, 'hotel_feature' ) : '';

// Location
$address  = !empty($meta['address']) ? $meta['address'] : '';
$map      = !empty($meta['map']) ? $meta['map'] : '';

// Hotel Detail
$gallery  = !empty($meta['gallery']) ? $meta['gallery'] : '';
if ($gallery) {
	$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
}
$video    = !empty($meta['video']) ? $meta['video'] : '';
// Room Details
$rooms = !empty($meta['room']) ? $meta['room'] : '';
// FAQ
$faqs = !empty($meta['faq']) ? $meta['faq'] : '';
// Terms & condition
$tc = !empty($meta['tc']) ? $meta['tc'] : '';

$share_text = get_the_title();
$share_link = get_permalink($post_id);
?>
<div class="tf-main-wrapper">
    <?php do_action( 'tf_before_container' ); ?>

    <!-- Start title area -->
    <div class="tf-title-area tf-hotel-title sp-20">
         <div class="tf-container">
             <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <h1><?php the_title(); ?></h1>
                    <!-- Start map link -->
                    <?php if ($locations) { ?>
                        <div class="tf-map-link"> 
                            <?php if($address) {
                                echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' .$address. ' – </span>';
                            } ?>

                            <a href="<?php echo $first_location_url; ?>" class="more-hotel tf-d-ib">                      
                                <?php printf(__('Show more Properties', 'tourfic')); ?>
                            </a>
                        </div>
                    <?php } ?>
                    <!-- End map link -->
                </div>
             
                <div class="tf-title-right">
                    <?php
                    // Wishlist
                    if(tfopt('wl-bt-for') && in_array('1', tfopt('wl-bt-for'))) {
                        if ( is_user_logged_in() ) {
                            if(tfopt('wl-for') && in_array('li', tfopt('wl-for'))) {
                            ?>
                                <a class="tf-wishlist-button" title="<?php _e('Click to toggle wishlist', 'tourfic'); ?>"><i class="<?php echo $has_in_wishlist ? 'fas tf-text-red remove-wishlist' : 'far add-wishlist'  ?> fa-heart" data-nonce="<?php echo wp_create_nonce("wishlist-nonce") ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if(tfopt('wl-page')) { echo 'data-page-title="' .get_the_title(tfopt('wl-page')). '" data-page-url="' .get_permalink(tfopt('wl-page')). '"'; } ?>></i></a>
                            <?php
                            }
                        } else {
                            if(tfopt('wl-for') && in_array('lo', tfopt('wl-for'))) {
                            ?>
                                <a class="tf-wishlist-button" title="<?php _e('Click to toggle wishlist', 'tourfic'); ?>"><i class="<?php echo $has_in_wishlist ? 'fas tf-text-red remove-wishlist' : 'far add-wishlist'  ?> fa-heart" data-nonce="<?php echo wp_create_nonce("wishlist-nonce") ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if(tfopt('wl-page')) { echo 'data-page-title="' .get_the_title(tfopt('wl-page')). '" data-page-url="' .get_permalink(tfopt('wl-page')). '"'; } ?>></i></a>
                            <?php
                            }
                        }
                    }
                    ?>                          

                    <!-- Share Section -->
                    <?php if(!$disable_share_opt == '1') { ?>
                    <div class="tf-share">
                        <a href="#dropdown-share-center" class="share-toggle"
                            data-toggle="true"><i class="fas fa-share-alt"></i></a>
                        <div id="dropdown-share-center" class="share-tour-content">
                            <ul class="tf-dropdown-content">
                                <li>
                                    <a href="http://www.facebook.com/share.php?u=<?php _e( $share_link ); ?>"
                                        class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-facebook-square"></i>
                                            <?php esc_html_e( 'Share on Facebook', 'tourfic' ); ?>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="http://twitter.com/share?text=<?php _e( $share_text ); ?>&url=<?php _e( $share_link ); ?>"
                                        class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-twitter-square"></i>
                                            <?php esc_html_e( 'Share on Twitter', 'tourfic' ); ?>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.linkedin.com/cws/share?url=<?php _e( $share_link ); ?>"
                                        class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-linkedin"></i>
                                            <?php esc_html_e( 'Share on Linkedin', 'tourfic' ); ?>
                                        </span>
                                    </a>
                                </li>
                                <?php 
                                    $share_image_link = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
                                    ?>
                                <li>
                                    <a href="http://pinterest.com/pin/create/button/?url=<?php _e( $share_link ); ?>&media=<?php _e( $share_image_link[0]); ?>&description=<?php _e( $share_text ); ?>"
                                        class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-pinterest"></i>
                                            <?php esc_html_e( 'Share on Pinterest', 'tourfic' ); ?>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <div class="share-center-copy-form tf-dropdown-item" title="Share this link"
                                        aria-controls="share_link_button">
                                        <label class="share-center-copy-label"
                                            for="share_link_input"><?php esc_html_e( 'Share this link', 'tourfic' ); ?></label>
                                        <input type="text" id="share_link_input"
                                            class="share-center-url share-center-url-input"
                                            value="<?php _e( $share_link ); ?>" readonly>
                                        <button id="share_link_button" class="tf_button share-center-copy-cta" tabindex="0"
                                            role="button">
                                            <span
                                                class="tf-button-text share-center-copy-message"><?php esc_html_e( 'Copy link', 'tourfic' ); ?></span>
                                            <span
                                                class="tf-button-text share-center-copied-message"><?php esc_html_e( '
                                                Link Copied!', 'tourfic' ); ?></span>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php } ?>
                    <!-- End Share Section -->
                    
<!--                     <div class="reserve-button">
                        <a href="#rooms" class="tf-btn-flip" data-back="View Rooms" data-front="Reserve Now"></a>
                    </div> -->
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
                    <div class="tf-top-review">
                        <?php if($comments && !$disable_review_sec == '1') { ?>
                            <a href="#tf-review">
                                <div class="tf-single-rating">
                                    <i class="fas fa-star"></i> <span><?php echo tf_total_avg_rating($comments); ?></span> (<?php tf_based_on_text(count($comments)); ?>)
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                    <!-- Start Gallery -->
                    <?php if ( ! empty( $gallery_ids ) ) { ?>
                        <div class="tf-gallery-wrap">
                            <div class="list-single-main-media fl-wrap" id="sec1">
                                <div class="single-slider-wrapper fl-wrap">
                                    <div class="tf_slider-for fl-wrap">
                                        <?php foreach ( $gallery_ids as $attachment_id ) {
                                            echo '<div class="slick-slide-item">';
                                                echo '<a href="'.wp_get_attachment_url( $attachment_id, 'full' ).'" class="slick-slide-item-link" data-fancybox="hotel-gallery">';
                                                    echo wp_get_attachment_image( $attachment_id, 'full' );
                                                echo '</a>';
                                            echo '</div>';
                                        } ?>
                                    </div>
                                    <div class="swiper-button-prev sw-btn"><i class="fa fa-angle-left"></i></div>
                                    <div class="swiper-button-next sw-btn"><i class="fa fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                     <?php } else{ ?>
                        <div class="tf-gallery-wrap">
                            <div class="list-single-main-media fl-wrap" id="sec1">
                                <div class="single-slider-wrapper fl-wrap">
                                    <div class="tf_slider-for fl-wrap">
                                        <?php
                                            echo '<div class="slick-slide-item">';
                                                echo '<a href="'.get_the_post_thumbnail_url( $post_id,'full' ).'" class="slick-slide-item-link" data-fancybox="hotel-gallery">';
                                                    echo get_the_post_thumbnail( $post_id,'full' );
                                                echo '</a>';
                                            echo '</div>';
                                        ?>
                                    </div>
                                    <div class="swiper-button-prev sw-btn"><i class="fa fa-angle-left"></i></div>
                                    <div class="swiper-button-next sw-btn"><i class="fa fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                     <!-- End gallery-->
                     <div class="map-for-mobile">
                        <?php if (!defined( 'TF_PRO' ) && ($address)) { ?>
                        <div class="show-on-map">
                            <div class="tf-btn"><a href="https://www.google.com/maps/search/<?php echo $address; ?>" target="_blank" class="btn-styled"><span><i class="fas fa-map-marker-alt"></i><?php esc_html_e( 'Show on map', 'tourfic' ); ?></span></a> </div>
                        </div>
                        <?php } ?>
                     </div>
                     <?php if (defined( 'TF_PRO' ) && ( !empty($map["address"]) || !empty($map["latitude"]) || !empty($map["longitude"]) )) { ?>
                        <div class="popupmap-for-mobile">
                            <div class="tf-hotel-location-preview show-on-map">
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $map["latitude"] ); ?>,<?php echo esc_attr( $map["longitude"] ); ?>&output=embed" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                
                                <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                                    <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                                </a>
                                
                            </div>
                            <div style="display: none;" id="tf-hotel-google-maps">
                                <div class="tf-hotel-google-maps-container">
                                    <?php 
                                    if(!empty($map["address"])){ ?>
                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace("#","",$map["address"]) ) ; ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                    <?php } else{ ?>
                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $map["latitude"] ); ?>,<?php echo esc_attr( $map["longitude"] ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="hero-right">
                    <?php if (!defined( 'TF_PRO' ) && ($address)) { ?>
                    <div class="show-on-map">
                        <div class="tf-btn"><a href="https://www.google.com/maps/search/<?php echo $address; ?>" target="_blank" class="btn-styled"><span><i class="fas fa-map-marker-alt"></i><?php esc_html_e( 'Show on map', 'tourfic' ); ?></span></a> </div>
                    </div>
                    <?php } ?>
                    <?php if (defined( 'TF_PRO' ) && ( !empty($map["address"]) || !empty($map["latitude"]) || !empty($map["longitude"]) )) { ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $map["latitude"] ); ?>,<?php echo esc_attr( $map["longitude"] ); ?>&output=embed" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        
                        <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                            <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                        </a>
                        
                    </div>
                    <div style="display: none;" id="tf-hotel-google-maps">
                        <div class="tf-hotel-google-maps-container">
                            <?php 
                            if(!empty($map["address"])){ ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace("#","",$map["address"]) ) ; ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            <?php } else{ ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $map["latitude"] ); ?>,<?php echo esc_attr( $map["longitude"] ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            <?php } ?>
                        </div>
                    </div>
                     <?php } ?>
                    <div class="hero-booking">
                        <?php tf_hotel_sidebar_booking_form(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->
    
    <!-- Start description -->
    <div class="description-section sp-50">
        <div class="tf-container">
            <div class="tf-ct-pricebox">
            <h2 class="section-heading"><?php esc_html_e( 'Tariffs', 'tourfic' ); ?></h2>
            <span class="tf-person-title"><?php esc_html_e( 'Per Person', 'tourfic' ); ?></span>
            <div class="tf-ct-price-grid">
                <?php 
                if(defined( 'TF_PRO' ) && !empty($rooms)){ 
                foreach ($rooms as $room) {
                    ?>
                    <table class="table tf-single-pricebox">
						<tr style="border: 1px solid #080994;">
						<?php 
                            if(!empty($room['title'])){ ?>
                            <td colspan="5" style="background: #FFFFCF; text-align: center;color: #080994; border: 1px solid #080994;">
                                <?php echo esc_html( $room['title'] ); ?>
                            </td>
                            <?php } ?>
						</tr>
                        <tr style="background: #FEFFF4; color: #080994;">
                            
                            <?php 
                            $tf8days  = !empty($room['tf-8-days']) ? $room['tf-8-days'] : ''; 
                            $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                            $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                            $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                            if( !empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold']) || !empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold']) || !empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold']) || !empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold']) ){ ?>
                            <td>
                                <table>
                                    <tr>
                                        <td></td>
                                    </tr>
                                    <?php 
                                    if( !empty($tf8days['tf-room']) || !empty($tf16days['tf-room']) || !empty($tf24days['tf-room']) || !empty($tf32days['tf-room']) ){ ?>
                                    <tr>
                                        <td><?php esc_html_e( 'Alojamiento', 'tourfic' ); ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php 
                                    if( !empty($tf8days['tf-breakfast']) || !empty($tf16days['tf-breakfast']) || !empty($tf24days['tf-breakfast']) || !empty($tf32days['tf-breakfast']) ){ ?>
                                    <tr>
                                        <td><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php 
                                    if( !empty($tf8days['tf-half-b']) || !empty($tf16days['tf-half-b']) || !empty($tf24days['tf-half-b']) || !empty($tf32days['tf-half-b']) ){ ?>
                                    <tr>
                                        <td><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php 
                                    if( !empty($tf8days['tf-full-b']) || !empty($tf16days['tf-full-b']) || !empty($tf24days['tf-full-b']) || !empty($tf32days['tf-full-b']) ){ ?>
                                    <tr>
                                        <td><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php 
                                    if( !empty($tf8days['tf-inclusive']) || !empty($tf16days['tf-inclusive']) || !empty($tf24days['tf-inclusive']) || !empty($tf32days['tf-inclusive']) ){ ?>
                                    <tr>
                                        <td><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php 
                                    if( !empty($tf8days['tf-inclusive-gold']) || !empty($tf16days['tf-inclusive-gold']) || !empty($tf24days['tf-inclusive-gold']) || !empty($tf32days['tf-inclusive-gold']) ){ ?>
                                    <tr>
                                        <td><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </td>
                            <?php } ?>
                            <?php  
                            if(!empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold'])){
                            ?>
                            <td>
                            <table>
                                <tr>
                                    <td><?php esc_html_e( '8 DAYS', 'tourfic' ); ?></td>
                                </tr>
                                <?php if( !empty($tf8days['tf-room']) ){ ?>
                                <tr>
                                    <td><?php echo $tf8days['tf-room'] ? wc_price($tf8days['tf-room']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf8days['tf-breakfast']) ){ ?>
                                <tr>
                                    <td><?php echo $tf8days['tf-breakfast'] ? wc_price($tf8days['tf-breakfast']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf8days['tf-half-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf8days['tf-half-b'] ? wc_price($tf8days['tf-half-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf8days['tf-full-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf8days['tf-full-b'] ? wc_price($tf8days['tf-full-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf8days['tf-inclusive']) ){ ?>
                                <tr>
                                    <td><?php echo $tf8days['tf-inclusive'] ? wc_price($tf8days['tf-inclusive']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf8days['tf-inclusive-gold']) ){ ?>
                                <tr>
                                    <td><?php echo $tf8days['tf-inclusive-gold'] ? wc_price($tf8days['tf-inclusive-gold']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            </td>
                            <?php } ?>

                            <?php 
                            if(!empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold'])){
                            ?>
                            <td>
                            <table>
                                <tr>
                                    <td><?php esc_html_e( '16 DAYS', 'tourfic' ); ?></td>
                                </tr>
                                <?php if( !empty($tf16days['tf-room']) ){ ?>
                                <tr>
                                    <td><?php echo $tf16days['tf-room'] ? wc_price($tf16days['tf-room']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf16days['tf-breakfast']) ){ ?>
                                <tr>
                                    <td><?php echo $tf16days['tf-breakfast'] ? wc_price($tf16days['tf-breakfast']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf16days['tf-half-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf16days['tf-half-b'] ? wc_price($tf16days['tf-half-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf16days['tf-full-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf16days['tf-full-b'] ? wc_price($tf16days['tf-full-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf16days['tf-inclusive']) ){ ?>
                                <tr>
                                    <td><?php echo $tf16days['tf-inclusive'] ? wc_price($tf16days['tf-inclusive']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf16days['tf-inclusive-gold']) ){ ?>
                                <tr>
                                    <td><?php echo $tf16days['tf-inclusive-gold'] ? wc_price($tf16days['tf-inclusive-gold']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            </td>
                            <?php } ?>

                            <?php 
                            if(!empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold'])){
                            ?>
                            <td>
                            <table>
                                <tr>
                                    <td><?php esc_html_e( '24 DAYS', 'tourfic' ); ?></td>
                                </tr>
                                <?php if( !empty($tf24days['tf-room']) ){ ?>
                                <tr>
                                    <td><?php echo $tf24days['tf-room'] ? wc_price($tf24days['tf-room']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf24days['tf-breakfast']) ){ ?>
                                <tr>
                                    <td><?php echo $tf24days['tf-breakfast'] ? wc_price($tf24days['tf-breakfast']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf24days['tf-half-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf24days['tf-half-b'] ? wc_price($tf24days['tf-half-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf24days['tf-full-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf24days['tf-full-b'] ? wc_price($tf24days['tf-full-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf24days['tf-inclusive']) ){ ?>
                                <tr>
                                    <td><?php echo $tf24days['tf-inclusive'] ? wc_price($tf24days['tf-inclusive']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf24days['tf-inclusive-gold']) ){ ?>
                                <tr>
                                    <td><?php echo $tf24days['tf-inclusive-gold'] ? wc_price($tf24days['tf-inclusive-gold']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            </td>
                            <?php } ?>

                            <?php 
                            if(!empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold'])){
                            ?>
                            <td>
                            <table>
                                <tr>
                                    <td><?php esc_html_e( '32 DAYS', 'tourfic' ); ?></td>
                                </tr>
                                <?php if( !empty($tf32days['tf-room']) ){ ?>
                                <tr>
                                    <td><?php echo $tf32days['tf-room'] ? wc_price($tf32days['tf-room']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf32days['tf-breakfast']) ){ ?>
                                <tr>
                                    <td><?php echo $tf32days['tf-breakfast'] ? wc_price($tf32days['tf-breakfast']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf32days['tf-half-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf32days['tf-half-b'] ? wc_price($tf32days['tf-half-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf32days['tf-full-b']) ){ ?>
                                <tr>
                                    <td><?php echo $tf32days['tf-full-b'] ? wc_price($tf32days['tf-full-b']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf32days['tf-inclusive']) ){ ?>
                                <tr>
                                    <td><?php echo $tf32days['tf-inclusive'] ? wc_price($tf32days['tf-inclusive']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                                <?php if( !empty($tf32days['tf-inclusive-gold']) ){ ?>
                                <tr>
                                    <td><?php echo $tf32days['tf-inclusive-gold'] ? wc_price($tf32days['tf-inclusive-gold']) : '';  ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            </td>
                            <?php } ?>
                        </tr>
                    </table>
                    <?php
                }
                }
                ?>
                </div>
            </div>
            <div class="desc-wrap">
                <?php the_content(); ?>
            </div>
             <!-- Start features -->
            <?php if( $features ) { ?>
                <div class="tf_features">
                    <h3 class="section-heading"><?php esc_html_e( 'Popular Features', 'tourfic' ); ?></h3>
                    <div class="tf-feature-list">
                        <?php foreach($features as $feature) {
                            $feature_meta = get_term_meta( $feature->term_id, 'hotel_feature', true );
                            $f_icon_type = !empty($feature_meta['icon-type']) ? $feature_meta['icon-type'] : '';
                            if ($f_icon_type == 'fa') {
                                $feature_icon = '<i class="' .$feature_meta['icon-fa']. '"></i>';
                            } elseif ($f_icon_type == 'c') {
                                $feature_icon = '<img src="' .$feature_meta['icon-c']["url"]. '" style="width: ' .$feature_meta['dimention']["width"]. 'px; height: ' .$feature_meta['dimention']["width"]. 'px;" />';
                            } ?>

                        <div class="single-feature-box">
                            <?php echo $feature_icon ?? ''; ?>
                            <p class="feature-list-title"><?php echo $feature->name; ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <!-- End features -->
        </div>
    </div>
    <!-- End description -->
    
    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>
    
    <!-- Start Room Section -->
    <div class="tf-room-section sp-50">
        <div class="tf-container">
            <?php if( $rooms ) { ?>
                <div class="tf-room-type" id="rooms">
                <h2 class="section-heading"><?php esc_html_e( 'Available Rooms', 'tourfic' ); ?></h2>
                    <div class="tf-room-table hotel-room-wrap">
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo TF_ASSETS_URL ?>img/loader.gif" alt="">
                            </div>
                        </div>
                        <table class="availability-table">
                            <thead>
                                <tr>
                                    <th class="description"><?php _e( 'ROOM DETAILS', 'tourfic' ); ?></th>
                                    <th class="pax"><?php _e( 'PERSONS', 'tourfic' ); ?></th>
                                    <th class="pricing"><?php _e( 'Price', 'tourfic' ); ?></th>
                                    <th class="reserve"><?php _e( 'Select Rooms', 'tourfic' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Start Single Room -->
                                <?php foreach ($rooms as $room_id => $room) {
								$enable = !empty($room['enable']) ? $room['enable'] : '';
								if ($enable == '1') {
									$footage = !empty($room['footage']) ? $room['footage'] : '';
									$bed = !empty($room['bed']) ? $room['bed'] : '';
									$adult_number = !empty($room['adult']) ? $room['adult'] : '0';
									$child_number = !empty($room['child']) ? $room['child'] : '0';
									$total_person = $adult_number + $child_number;	
									$pricing_by = !empty($room['pricing-by']) ? $room['pricing-by'] : '';
                                    $avil_by_date = !empty( $room['avil_by_date'] ) ? !empty( $room['avil_by_date'] ) : false;

                                    // if($avil_by_date == true) {
                                    //     $repeat_by_date = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                                    //     if ($pricing_by == '1') {
                                    //         $prices = wp_list_pluck( $repeat_by_date, 'price' );                                        
                                    //     } else {
                                    //         $prices = wp_list_pluck( $repeat_by_date, 'adult_price' );                                        
                                    //     }

                                    // $price = min( $prices ) != max( $prices ) ? wc_format_price_range( min( $prices ), max( $prices ) ) : wc_price( min( $prices ) );
                                    // } else {
                                    //     if ($pricing_by == '1') {
                                    //         $price = wc_price( !empty($room['price']) ? $room['price'] : '0.0' );
                                    //     } else {
                                    //         $price = wc_price( !empty($room['adult_price']) ? $room['adult_price'] : '0.0' );
                                    //     }
                                    // }									
							    ?>
                                <tr>
                                    <td class="description">
                                        <div class="tf-room-type">
                                            <div class="tf-room-title">
                                            <?php 
                                            $tour_room_details_gall = !empty($room['gallery']) ? $room['gallery'] : '';
                                            if ($tour_room_details_gall) {
                                                $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                            }
                                            if (defined( 'TF_PRO' ) && $tour_room_details_gall){ 
                                            ?>	
                                            <h3><a href="#" class="tf-room-detail-qv" data-roomid="<?php echo esc_attr($room_id); ?>" data-uniqid="<?php echo !empty($room['unique_id']) ? $room['unique_id'] : '' ?>" data-hotel="<?php echo $post_id; ?>" style="text-decoration: underline;">
                                                <?php echo esc_html( $room['title'] ); ?>
                                            </a></h3>
                                            
                                            <div id="tour_room_details_qv" class="tf-reg-wrap" >
                                                                                        
                                            </div>
                                            <?php } else{ ?>
                                                <h3><?php echo esc_html( $room['title'] ); ?><h3>
                                             <?php   
                                            }
                                            ?>
                                            </div>
                                            <div class="bed-facilities"><?php _e( $room['description'] ); ?></div>
                                        </div>

                                        <?php if ($footage) { ?>
                                        <div class="tf-tooltip tf-d-ib">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i
                                                        class="fas fa-ruler-combined"></i></span>
                                                <span class="icon-text tf-d-b"><?php echo $footage; ?> <?php _e( 'sft', 'tourfic' ); ?></span>
                                            </div>
                                            <div class="tf-top">
                                                <?php _e( 'Room Footage', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
                                        <?php }
										if ($bed) { ?>
                                        <div class="tf-tooltip tf-d-ib">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                                <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                                            </div>
                                            <div class="tf-top">
                                                <?php _e( 'No. Beds', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php if(!empty($room['features'])) { ?>
                                        <div class="room-features">
                                            <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
                                            </div>
                                            <ul class="room-feature-list">

                                                <?php foreach ($room['features'] as $feature) {
													
													$room_f_meta = get_term_meta( $feature, 'hotel_feature', true );

                                                    $room_icon_type = !empty($room_f_meta['icon-type']) ? $room_f_meta['icon-type'] : '';

													if ($room_icon_type == 'fa') {
														$room_feature_icon = '<i class="' .$room_f_meta['icon-fa']. '"></i>';
													} elseif ($room_icon_type == 'c') {
														$room_feature_icon = '<img src="' .$room_f_meta['icon-c']["url"]. '" style="min-width: ' .$room_f_meta['dimention']["width"]. 'px; height: ' .$room_f_meta['dimention']["width"]. 'px;" />';
													}

													$room_term = get_term( $feature ); ?>
                                                <li class="tf-tooltip">
                                                    <?php echo !empty($room_feature_icon) ? $room_feature_icon : ''; ?>
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
                                        <?php if ($adult_number) { ?>
                                        <div class="tf-tooltip tf-d-b">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                                                        class="fas fa-female"></i></span>
                                                <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                                            </div>
                                            <div class="tf-top">
                                                <?php _e( 'No. Adults', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
                                        <?php }
										if ($child_number) { ?>
                                        <div class="tf-tooltip tf-d-b">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                                <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                                            </div>
                                            <div class="tf-top">
                                                <?php _e( 'No. Children', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </td>
                                    <td class="pricing">
                                        <div class="tf-price-column">
                                            
                                                <div class="price-per-night">
                                                <b style="font-size: 17px; text-transform: uppercase;">
                                                    <?php esc_html_e( 'FULL STAY PER PERSON', 'tourfic' ); ?>
                                                </b>
                                                </div>
                                                <?php 
                                                if(defined( 'TF_PRO' )){ 
                                                    ?>
                                                    <?php 
                                                    $tf8days  = !empty($room['tf-8-days']) ? $room['tf-8-days'] : ''; 
                                                    if(!empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold'])){
                                                    ?>
                                                    <div class="tf-single-prices">
                                                        <span><?php esc_html_e( '8 DAYS', 'tourfic' ); ?>  <?php echo $tf8days['tf-room'] ? wc_price($tf8days['tf-room']) : '';  ?></span>
                                                        
                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf8days['tf-breakfast'])){ ?>
                                                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf8days['tf-breakfast'] ? wc_price($tf8days['tf-breakfast']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf8days['tf-half-b'])){ ?>
                                                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf8days['tf-half-b'] ? wc_price($tf8days['tf-half-b']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf8days['tf-full-b'])){ ?>
                                                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf8days['tf-full-b'] ? wc_price($tf8days['tf-full-b']) : '';  ?></span>
                                                            <?php } ?>
                                                        </div>

                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf8days['tf-inclusive'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf8days['tf-inclusive'] ? wc_price($tf8days['tf-inclusive']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf8days['tf-inclusive-gold'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf8days['tf-inclusive-gold'] ? wc_price($tf8days['tf-inclusive-gold']) : '';  ?></span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <?php } ?>

                                                    <?php 
                                                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                                                    if(!empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold'])){
                                                    ?>
                                                    <div class="tf-single-prices">
                                                        <span><?php esc_html_e( '16 DAYS', 'tourfic' ); ?>  <?php echo $tf16days['tf-room'] ? wc_price($tf16days['tf-room']) : '';  ?></span>
                                                        
                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf16days['tf-breakfast'])){ ?>
                                                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf16days['tf-breakfast'] ? wc_price($tf16days['tf-breakfast']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf16days['tf-half-b'])){ ?>
                                                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf16days['tf-half-b'] ? wc_price($tf16days['tf-half-b']) : '';  ?></span>
                                                            <?php } ?>

                                                            <?php if(!empty($tf16days['tf-full-b'])){ ?>
                                                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf16days['tf-full-b'] ? wc_price($tf16days['tf-full-b']) : '';  ?></span>
                                                            <?php } ?>
                                                        </div>

                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf16days['tf-inclusive'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf16days['tf-inclusive'] ? wc_price($tf16days['tf-inclusive']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf16days['tf-inclusive-gold'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf16days['tf-inclusive-gold'] ? wc_price($tf16days['tf-inclusive-gold']) : '';  ?></span>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                    <?php } ?>

                                                    <?php 
                                                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                                                    if(!empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold'])){
                                                    ?>
                                                    <div class="tf-single-prices">
                                                        <span><?php esc_html_e( '24 DAYS', 'tourfic' ); ?>  <?php echo $tf24days['tf-room'] ? wc_price($tf24days['tf-room']) : '';  ?></span>
                                                        
                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf24days['tf-breakfast'])){ ?>
                                                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf24days['tf-breakfast'] ? wc_price($tf24days['tf-breakfast']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf24days['tf-half-b'])){ ?>
                                                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf24days['tf-half-b'] ? wc_price($tf24days['tf-half-b']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf24days['tf-full-b'])){ ?>
                                                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf24days['tf-full-b'] ? wc_price($tf24days['tf-full-b']) : '';  ?></span>
                                                            <?php } ?>
                                                        </div>

                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf24days['tf-inclusive'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf24days['tf-inclusive'] ? wc_price($tf24days['tf-inclusive']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf24days['tf-inclusive-gold'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf24days['tf-inclusive-gold'] ? wc_price($tf24days['tf-inclusive-gold']) : '';  ?></span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <?php } ?>

                                                    <?php 
                                                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                                                    if(!empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold'])){
                                                    ?>
                                                    <div class="tf-single-prices">
                                                        <span><?php esc_html_e( '32 DAYS', 'tourfic' ); ?>  <?php echo $tf32days['tf-room'] ? wc_price($tf32days['tf-room']) : '';  ?></span>
                                                        
                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf32days['tf-breakfast'])){ ?>
                                                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf32days['tf-breakfast'] ? wc_price($tf32days['tf-breakfast']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf32days['tf-half-b'])){ ?>
                                                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf32days['tf-half-b'] ? wc_price($tf32days['tf-half-b']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf32days['tf-full-b'])){ ?>
                                                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf32days['tf-full-b'] ? wc_price($tf32days['tf-full-b']) : '';  ?></span>
                                                            <?php } ?>
                                                        </div>

                                                        <div class="tf-single-prices-item">
                                                            <?php if(!empty($tf32days['tf-inclusive'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf32days['tf-inclusive'] ? wc_price($tf32days['tf-inclusive']) : '';  ?></span>
                                                            <?php } ?>
                                                            <?php if(!empty($tf32days['tf-inclusive-gold'])){ ?>
                                                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf32days['tf-inclusive-gold'] ? wc_price($tf32days['tf-inclusive-gold']) : '';  ?></span>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                            
                                                    <?php
                                                }
                                                ?>
                                            
                                        </div>
                                    </td>
                                    <td class="reserve tf-t-c">
                                     <div class="tf-btn"><button class="btn-styled hotel-room-availability tf-sml-btn"
                type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' );?></button></div>
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
                <?php } ?>
        </div>
    </div>
   <!-- End Room Section -->

    <!-- FAQ section Start -->
		<?php if( $faqs ): ?>
		<div class="tf-faq-wrapper hotel-faq sp-50">
			<div class="tf-container">
				<div class="tf-faq-sec-title">
                    <h2 class="section-heading"><?php esc_html_e( 'FAQs', 'tourfic' ); ?></h2>
				</div>
                
				<div class="tf-faq-content-wrapper">
                    <div class="tf-ask-question">
						<h3><?php _e( "Have a question in mind", 'tourfic' ); ?></h3>
						<p><?php _e( "Looking for more info? Send a question to the property to find out more.", 'tourfic' ); ?></p>
						<div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="btn-styled"><span><?php esc_html_e( 'Ask a Question', 'tourfic' ); ?></span></a> </div>
					</div>

					<div class="tf-faq-items-wrapper">
						<?php foreach ( $faqs as $key => $faq ): ?>
							<div id="tf-faq-item">
								<div class="tf-faq-title">
									<h4><?php esc_html_e( $faq['title'] ); ?></h4>
									<i class="fas fa-angle-down arrow"></i>
								</div>
								<div class="tf-faq-desc">
									<p><?php _e( $faq['description'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<!-- FAQ section end -->
	
	<?php
	if ( is_user_logged_in() ) {
		$tf_product_list = [];
		$args = array(
			'customer_id' => get_current_user_id(),
			'limit' => -1,
			'type'=> 'shop_order',
			'status'=> array( 'wc-completed' ),
		);
		$query_orders = wc_get_orders($args);

		foreach ( $query_orders as $order ) {
			$items = $order->get_items();
			foreach ( $items as $item ) {
				$tf_hotel_ord = $item->get_meta( '_post_id', true );
				$tf_product_list[$tf_hotel_ord] = $tf_hotel_ord;
			}	   
		}
	?>
	<!-- Start Review Section -->
	<?php if (in_array($post_id, $tf_product_list)) { ?>
	
    <div id="tf-review" class="review-section sp-50">
        <div class="tf-container">
            <div class="reviews">
                <h2 class="section-heading"><?php esc_html_e( 'Guest Reviews', 'tourfic' ); ?></h2>
                <?php comments_template(); ?>
            </div>
        </div>
    </div>
    <?php } } ?>
    <!-- End Review Section -->

    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>

    <!-- Start TOC Content -->
    <?php if ($tc) { ?>
    <div class="toc-section sp-50">
        <div class="tf-container">
            <div class="tf-toc-wrap">
            <h2 class="section-heading"><?php esc_html_e( 'Hotel Terms & Conditions', 'tourfic' ); ?></h2>
                <div class="tf-toc-inner">
                    <?php echo wpautop($tc); ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <!-- End TOC Content -->
    
    <?php do_action( 'tf_after_container' ); ?>
</div>
<?php endwhile; ?>
<?php
get_footer();
