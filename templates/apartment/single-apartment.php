<?php
/**
 * Template: Single Apartment (Full Width)
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
 * Get apartment meta values
 */
$meta = get_post_meta( $post_id, 'tf_apartment', true );

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
 * Get features
 * 
 * apartment_feature
 */
$features  = !empty(get_the_terms( $post_id, 'apartment_feature' )) ? get_the_terms( $post_id, 'apartment_feature' ) : '';

// Location
$address  = !empty($meta['address']) ? $meta['address'] : '';
$map      = !empty($meta['map']) ? $meta['map'] : '';

// Apartment Detail
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

<div class="tf-main-wrapper apartment-wrap">
    <?php do_action( 'tf_before_container' ); ?>

    <!-- Start title area -->
    <div class="tf-title-area tf-apartment-title sp-40">
         <div class="tf-container">
             <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <h1><?php the_title(); ?></h1>
                    <!-- Start map link -->
                        <div class="tf-map-link"> 
                            <p><span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> Aftab nagar, Badda, Dhaka, Bangladesh.</p>
                        </div>
                    <!-- End map link -->
                </div>
             
                <div class="tf-title-right">
                    <div class="tf-top-review">
                        <a href="#tf-review">
                            <div class="tf-single-rating">
                                <i class="fas fa-star"></i> <span><?php echo tf_total_avg_rating($comments); ?></span> (<?php tf_based_on_text(count($comments)); ?>)
                            </div>
                        </a>
                    </div>
                                            

                    <!-- Share Section -->
                    <?php if(!$disable_share_opt == '1') { ?>
                    <div class="tf-share">
                        <a href="#dropdown-share-center" class="share-toggle"
                            data-toggle="true"><i class="fas fa-share-alt"></i> Share</a>
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

                     <!-- Wishlist Section -->
                    <?php
                    if(tfopt('wl-bt-for') && in_array('1', tfopt('wl-bt-for'))) {
                        if ( is_user_logged_in() ) {
                            if(tfopt('wl-for') && in_array('li', tfopt('wl-for'))) {
                            ?>
                                <a class="tf-wishlist-button" title="<?php _e('Click to toggle wishlist', 'tourfic'); ?>"><i class="<?php echo $has_in_wishlist ? 'fas tf-text-red remove-wishlist' : 'far add-wishlist'  ?> fa-heart" data-nonce="<?php echo wp_create_nonce("wishlist-nonce") ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if(tfopt('wl-page')) { echo 'data-page-title="' .get_the_title(tfopt('wl-page')). '" data-page-url="' .get_permalink(tfopt('wl-page')). '"'; } ?>> Save</i></a>
                            <?php
                            }
                        } else {
                            if(tfopt('wl-for') && in_array('lo', tfopt('wl-for'))) {
                            ?>
                                <a class="tf-wishlist-button" title="<?php _e('Click to toggle wishlist', 'tourfic'); ?>"><i class="<?php echo $has_in_wishlist ? 'fas tf-text-red remove-wishlist' : 'far add-wishlist'  ?> fa-heart" data-nonce="<?php echo wp_create_nonce("wishlist-nonce") ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if(tfopt('wl-page')) { echo 'data-page-title="' .get_the_title(tfopt('wl-page')). '" data-page-url="' .get_permalink(tfopt('wl-page')). '"'; } ?>> Save</i></a>
                            <?php
                            }
                        }
                    }
                    ?> 
                     <!-- Wishlist Section -->
                </div>
            </div>  
        </div>
    </div>
    <!-- End title area -->

    <!-- Start Hero Section -->
    <div class="hero-section tf-apartment">
        <div class="tf-container">
            <div class="hero-wrapper">
                <div class="hero-left">
                    <div class="hero-first-image">
                        <img src="https://cdn.pixabay.com/photo/2016/11/18/17/20/living-room-1835923_960_720.jpg" alt="">
                    </div>
                </div>
                <div class="hero-right">
                    <div class="hero-second-image">
                        <img src="https://cdn.pixabay.com/photo/2017/01/30/10/03/book-2020460_960_720.jpg" alt="">
                    </div>
                    <div class="hero-third-image">
                        <img src="https://cdn.pixabay.com/photo/2018/01/26/08/15/dining-room-3108037_960_720.jpg" alt="">
                        <a href="#" class="tf_button btn-styled">All Photos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <!-- Start Content & Feature Section -->
    <div class="content-feature-section tf-apartment">
        <div class="tf-container">
            <div class="cf-wrapper">
                <div class="cf-left">
                    <div class="apt-description">
                        <?php the_content(); ?>
                    </div>
                    <div class="key-features sp-t-40">
                        <h2 class="section-heading">Key Features</h2>
                        <div class="features-details">
                            <ul>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 3 Bedrooms</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 2 Attached Baths</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                            </ul>
                        </div>
                    </div>
                    <div class="apartment-amenities sp-t-40">
                        <h2 class="section-heading">Amenities</h2>
                        <div class="features-details amenities-details">
                            <ul>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 3 Bedrooms</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 2 Attached Baths</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                                <li><i class="fab fa-facebook-square"></i> 1500 sft</li>
                            </ul>
                        </div>
                    </div>
                    <div class="apartment-options sp-t-40">
                        <h2 class="section-heading">What you will get here</h2>
                            <div class="tf-apartment-option-slider-wrapper">
                                <div class="tf-apartment-option-slider-item">
                                    <div class="tf-apartment-option-slider-content">
                                        <img src="https://cdn.pixabay.com/photo/2016/10/18/09/02/hotel-1749602_960_720.jpg" alt="">
                                        <div class="tf-apartment-option-slider-desc">
                                            <h3>Drawing Space</h3>
                                            <p>2 Double Bed</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Remove all the below codes when you run the loop. These are added for demo purpose -->
                                <div class="tf-apartment-option-slider-item">
                                    <div class="tf-apartment-option-slider-content">
                                        <img src="https://cdn.pixabay.com/photo/2016/04/15/11/48/hotel-1330850_960_720.jpg" alt="">
                                        <div class="tf-apartment-option-slider-desc">
                                            <h3>Library</h3>
                                            <p>Awesome library space for guest</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-apartment-option-slider-item">
                                    <div class="tf-apartment-option-slider-content">
                                        <img src="https://cdn.pixabay.com/photo/2014/05/18/19/15/walkway-347319_960_720.jpg" alt="">
                                        <div class="tf-apartment-option-slider-desc">
                                            <h3>Deluxe Bathroom</h3>
                                            <p>Watch Tiktok on Bathroom</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-apartment-option-slider-item">
                                    <div class="tf-apartment-option-slider-content">
                                        <img src="https://cdn.pixabay.com/photo/2015/01/10/11/39/hotel-595121_960_720.jpg" alt="">
                                        <div class="tf-apartment-option-slider-desc">
                                            <h3>Eita ekta hudai heading</h3>
                                            <p>Kemon asen shobai, valo?</p>
                                        </div>
                                    </div>
                                </div>
                                 <!-- Remove all the above codes when you run the loop. These are added for demo purpose -->
                            </div>
                    </div>
                </div>
                <div class="cf-right">
                    <div class="host-details">
                        <div class="host-top">
                            <img src="https://cdn.pixabay.com/photo/2018/01/06/09/25/hijab-3064633_960_720.jpg" alt="">
                            <div class="host-meta">
                                <h4>Hosted by Jorina Bua</h4>
                                <p>Joined in <span>January 2022</span></p>
                                <p><i class="fas fa-star"></i> <span>4.5</span> (150 Reviews)</p>
                            </div>
                        </div>
                        <div class="host-bottom">
                            <p class="host-desc">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters.</p>
                            <ul>
                                <li>Language: <span>Bangla, English, Hindi</span></li>
                                <li>Response Time: <span>Within a Day</span></li>
                            </ul>
                            <a href="" class="tf_button btn-styled"><i class="far fa-comments"></i> Contact Host</a>
                        </div>
                    </div>
                    <div class="apartment-booking-form">
                        <h3>Form for Booking</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content & Feature Section -->

    <!-- Start Review Section -->
    <?php if(!$disable_review_sec == 1) { ?>
    <div id="tf-review" class="review-section sp-50">
        <div class="tf-container">
            <div class="reviews">
                <h2 class="section-heading"><?php esc_html_e( 'Guest Reviews', 'tourfic' ); ?></h2>
                <?php comments_template(); ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <!-- End Review Section -->

    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>
    
    <?php do_action( 'tf_after_container' ); ?>
</div>
<?php endwhile; ?>
<?php
get_footer();
