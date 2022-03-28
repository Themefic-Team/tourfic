<?php
/**
 * Template: Single Hotel
 */

get_header();

/**
 * Query start
 */
while ( have_posts() ) : the_post(); 

/**
 * Settings
 */
$s_share = !empty(tfopt('h-share')) ? tfopt('h-share') : '';
$s_review = !empty(tfopt('h-review')) ? tfopt('h-review') : '';

/**
 * Assign all values to variables
 * 
 */

// get post id
$post_id   = get_the_ID();

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

/**
 * Get hotel meta values
 */
$meta = get_post_meta( get_the_ID(), 'tf_hotel', true );

// Location
$address  = !empty($meta['address']) ? $meta['address'] : '';
$map      = !empty($meta['map']) ? $meta['map'] : '';

// Hotel Detail
//$featured = !empty($meta['featured']) ? $meta['featured'] : '';
//$logo     = !empty($meta['logo']) ? $meta['logo'] : '';
$gallery  = !empty($meta['gallery']) ? $meta['gallery'] : '';
if ($gallery) {
	// Comma seperated list to array
	$gallery_ids = explode( ',', $gallery );
}
$video    = !empty($meta['video']) ? $meta['video'] : '';
//$rating   = !empty($meta['rating']) ? $meta['rating'] : '';

// Contact Information
// $c_email = !empty($meta['c-email']) ? $meta['c-email'] : '';
// $c_web   = !empty($meta['c-web']) ? $meta['c-web'] : '';
// $c_phone = !empty($meta['c-phone']) ? $meta['c-phone'] : '';
// $c_fax   = !empty($meta['c-fax']) ? $meta['c-fax'] : '';

// Check in/out Time
// $full_day  = !empty($meta['full-day']) ? $meta['full-day'] : '';
// $check_in  = !empty($meta['check-in']) ? $meta['check-in'] : '';
// $check_out = !empty($meta['check-out']) ? $meta['check-out'] : '';

// Room Details
$rooms = !empty($meta['room']) ? $meta['room'] : '';

// FAQ
$faqs = !empty($meta['faq']) ? $meta['faq'] : '';

// Terms & condition
$tc = !empty($meta['tc']) ? $meta['tc'] : '';


$share_text = get_the_title();
$share_link = esc_url( home_url("/?p=").get_the_ID() );

?>
<div class="tourfic-wrap default-style" data-fullwidth="true">
    <?php do_action( 'tf_before_container' ); ?>
    <div class="tf_container">
        <div class="tf_row">
            <div class="tf_content tf_content-full mb-15">
                <!-- Start title area -->
                <div class="tf_title-area">
                    <h2 class="tf_title"><?php the_title(); ?></h2>
                    <div class="tf_title-right">
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
                        &nbsp;
                        <?php if($s_share && $s_share == '1') {} else { ?>
                        <!-- Share Section -->
                        <div class="share-tour">
                            <a href="#dropdown_share_center" class="share-toggle"
                                data-toggle="true"><?php echo tourfic_get_svg('share'); ?></a>
                            <div id="dropdown_share_center" class="share-tour-content">
                                <ul class="tf-dropdown__content">
                                    <li>
                                        <a href="http://www.facebook.com/share.php?u=<?php _e( $share_link ); ?>"
                                            class="tf-dropdown__item" target="_blank">
                                            <span
                                                class="tf-dropdown__item-content"><?php echo tourfic_get_svg('facebook'); ?>
                                                <?php esc_html_e( 'Share on Facebook', 'tourfic' ); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="http://twitter.com/share?text=<?php _e( $share_text ); ?>&url=<?php _e( $share_link ); ?>"
                                            class="tf-dropdown__item" target="_blank">
                                            <span
                                                class="tf-dropdown__item-content"><?php echo tourfic_get_svg('twitter'); ?>
                                                <?php esc_html_e( 'Share on Twitter', 'tourfic' ); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="share_center_copy_form tf-dropdown__item" title="Share this link"
                                            aria-controls="share_link_button">
                                            <label class="share_center_copy_label"
                                                for="share_link_input"><?php esc_html_e( 'Share this link', 'tourfic' ); ?></label>
                                            <input type="text" id="share_link_input"
                                                class="share_center_url share_center_url_input"
                                                value="<?php _e( $share_link ); ?>" readonly>
                                            <button id="share_link_button" class="share_center_copy_cta" tabindex="0"
                                                role="button">
                                                <span
                                                    class="tf-button__text share_center_copy_message"><?php esc_html_e( 'Copy link', 'tourfic' ); ?></span>
                                                <span
                                                    class="tf-button__text share_center_copied_message"><?php esc_html_e( 'Copied!', 'tourfic' ); ?></span>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- End Share Section -->
                        <?php } ?>

                        <?php if (!empty($map["address"])) { ?>
                        <div class="show-on-map">
                            <a href="https://www.google.com/maps/search/<?php echo $map["address"]; ?>" target="_blank"
                                class="tf_button btn-outline button"><?php esc_html_e( 'Show on map', 'tourfic' ); ?></a>
                        </div>
                        <?php } ?>
                        <div class="reserve-button">
                            <a href="#rooms" class="tf_button button"><?php esc_html_e( 'Reserve', 'tourfic' ); ?></a>
                        </div>
                    </div>
                </div>
                <!-- End title area -->

                <?php if ($locations) { ?>
                <!-- Start map link -->
                <div class="tf_map-link">
                    <?php if($address) {
                        echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' .$address. ' – </span>';
                    } ?>

                    <a href="<?php echo $first_location_url; ?>" class="tf-d-ib">                      
                        <?php printf(__('Show more hotels in %s', 'tourfic'),$first_location_name); ?>
                    </a>
                </div>
                <!-- End map link -->
                <?php } ?>
            </div>
        </div>

        <div class="tf_row">
            <!-- Start Content -->
            <div class="tf_content">
                <?php if ( ! empty( $gallery_ids ) ) { ?>
                <!-- Start gallery -->
                <div class="tf_gallery-wrap">
                    <div class="list-single-main-media fl-wrap" id="sec1">
                        <div class="single-slider-wrapper fl-wrap">
                            <div class="tf_slider-for fl-wrap">
                                <?php foreach ( $gallery_ids as $attachment_id ) {
									echo '<div class="slick-slide-item">';
										echo '<a href="'.wp_get_attachment_url( $attachment_id, 'tf_gallery_thumb' ).'" class="slick-slide-item-link" data-fancybox="hotel-gallery">';
											echo wp_get_attachment_image( $attachment_id, 'tf_gallery_thumb' );
										echo '</a>';
									echo '</div>';
								} ?>
                            </div>
                            <div class="swiper-button-prev sw-btn"><i class="fa fa-angle-left"></i></div>
                            <div class="swiper-button-next sw-btn"><i class="fa fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                <!-- End gallery-->
                <?php } ?>

                <!-- Start description -->
                <div class="tf_contents">
                    <div class="listing-title">
                        <h4><?php esc_html_e( 'Description', 'tourfic' ); ?></h4>
                    </div>
                    <?php the_content(); ?>
                </div>
                <!-- End description -->

                <?php if( $features ) { ?>
                <!-- Start features -->
                <div class="tf_features">
                    <div class="listing-title">
                        <h4><?php esc_html_e( 'Features', 'tourfic' ); ?></h4>
                    </div>

                    <div class="tf_feature_list">
                        <?php foreach($features as $feature) {
							$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'hotel_feature', true );
                            $f_icon_type = !empty($feature_meta['icon-type']) ? $feature_meta['icon-type'] : '';
							if ($f_icon_type == 'fa') {
								$feature_icon = '<i class="' .$feature_meta['icon-fa']. '"></i>';
							} elseif ($f_icon_type == 'c') {
								$feature_icon = '<img src="' .$feature_meta['icon-c']["url"]. '" style="width: ' .$feature_meta['dimention']["width"]. 'px; height: ' .$feature_meta['dimention']["width"]. 'px;" />';
							} ?>

                        <div class="single_feature_box">
                            <?php echo $feature_icon; ?>
                            <p class="feature_list_title"><?php echo $feature->name; ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- End features -->
                <?php } ?>

                <?php if( $rooms ) { ?>
                <!-- Start Room Type -->
                <div class="tf_room-type" id="rooms">
                    <div class="listing-title">
                        <h4><?php esc_html_e( 'Availability', 'tourfic' ); ?></h4>
                    </div>
                    <div class="tf_room-table hotel-room-wrap">
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
                                <?php foreach ($rooms as $room) {

								$enable = !empty($room['enable']) ? $room['enable'] : '';

								if ($enable == '1') {

									$footage = !empty($room['footage']) ? $room['footage'] : '';
									$bed = !empty($room['bed']) ? $room['bed'] : '';
									$adult_number = !empty($room['adult']) ? $room['adult'] : '0';
									$child_number = !empty($room['child']) ? $room['child'] : '0';
									$total_person = $adult_number + $child_number;	
									$pricing_by = !empty($room['pricing-by']) ? $room['pricing-by'] : '';										
							?>
                                <tr>
                                    <td class="description">
                                        <div class="tf-room-type">
                                            <div class="tf-room-title"><?php echo esc_html( $room['title'] ); ?></div>
                                            <div class="bed-facilities"><?php echo $room['description']; ?></div>
                                        </div>

                                        <div class="tf-room-title">
                                            <?php esc_html_e( 'Key Features', 'tourfic' ); ?>
                                        </div>

                                        <?php if ($footage) { ?>
                                        <div class="tf-tooltip tf-d-ib">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i
                                                        class="fas fa-ruler-combined"></i></span>
                                                <span class="icon-text tf-d-b"><?php echo $footage; ?> sft</span>
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
                                            <div class="tf-room-title"><?php esc_html_e( 'Amenities', 'tourfic' ); ?>
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
                                            <?php if ($pricing_by == '1') { ?>
                                            <span class="tf-price"><?php echo wc_price( $room['price'] ); ?></span>
                                            <div class="price-per-night"><?php esc_html_e( 'per night', 'tourfic' ); ?>
                                            </div>
                                            <?php } elseif ($pricing_by == '2') { ?>
                                            <span
                                                class="tf-price"><?php echo wc_price( $room['adult_price'] ); ?></span>
                                            <div class="price-per-night">
                                                <?php esc_html_e( 'per person/night', 'tourfic' ); ?></div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td class="reserve tf-t-c">
                                        <button class="hotel-room-availability"
                                            type="submit"><?php _e('Check Availability', 'tourfic'); ?></button>
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
                <!-- End Room Type -->
                <?php } ?>

                <?php if( $faqs ) { ?>
                <!-- Start FAQ -->
                <div class="tf_contents faqs">
                    <div class="highlights-title">
                        <h4><?php esc_html_e( 'FAQs', 'tourfic' ); ?></h4>
                    </div>

                    <div class="tf-faqs">
                        <?php foreach ( $faqs as $faq ): ?>
                        <div class="tf-single-faq">
                            <div class="tf-tours_faq_icon">
                                <i class="far fa-question-circle" aria-hidden="true"></i>
                            </div>
                            <div class="tf-tours_single_faq_inner">
                                <div class="faq-head">
                                    <?php esc_html_e( $faq['title'] ); ?>
                                    <span class="faq-indicator">
                                        <i class="fas fa-minus" aria-hidden="true"></i>
                                        <i class="fas fa-plus" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div class="faq-content"><?php _e( $faq['description'] ); ?></div>
                            </div>

                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- End FAQ -->
                <?php } ?>

                <?php if($s_review && $s_review == '1') {} else { ?>
                <!-- Start Review Content -->
                <div class="tf_contents reviews">
                    <div class="highlights-title">
                        <h4><?php esc_html_e( 'Reviews', 'tourfic' ); ?></h4>
                    </div>

                    <?php if ( comments_open() || get_comments_number() ) {
						comments_template();
					} ?>
                </div>
                <!-- End Review Content -->
                <?php } ?>

                <?php if ($tc) { ?>
                <!-- Start TOC Content -->
                <div class="tf_toc-wrap">
                    <div class="tf_toc-inner">
                        <?php echo wpautop($tc); ?>
                    </div>
                </div>
                <!-- End TOC Content -->
                <?php } ?>
            </div>
            <!-- End Content -->

            <!-- Start Sidebar -->
            <div class="tf_sidebar">
                <?php tf_hotel_sidebar_booking_form(); ?>
            </div>
            <!-- End Sidebar -->
        </div>

    </div>
    <?php do_action( 'tf_after_container' ); ?>
</div>
<?php endwhile; ?>
<?php
get_footer();
