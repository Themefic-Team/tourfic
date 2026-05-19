<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Tour\Tour_Price;
use \Tourfic\Classes\Tour\Pricing;

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
<div class="tf-single-template__legacy">
    <?php do_action( 'tf_before_container' ); ?>
    <!-- Hero section Start -->
    <div class="tf-hero-wrapper">
        <div class="tf-container">
            <div class="tf-hero-content" style="background-image: url(<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'images/feature-default.jpg'); ?>);">
                <div class="tf-hero-top">
                    <div class="tf-top-review">
                        <?php if ( $comments && ! $disable_review_sec == '1' ) { ?>
                            <a href="#tf-review">
                                <div class="tf-single-rating">
                                    <i class="fas fa-star"></i> <span><?php echo wp_kses_post(TF_Review::tf_total_avg_rating( $comments )); ?></span> (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Wishlist::render(['design' => 'design-3']); ?>
                    
                </div>
	            <?php \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(['booking_form_style' => 'style3']); ?>
                <div class="tf-hero-bottom-area">
                    <?php
                    $tour_video = ! empty( $meta['tour_video'] ) ? $meta['tour_video'] : '';
                    if ( !empty($tour_video) ) {
                        ?>
                        <div class="tf-hero-btm-icon tf-tour-video" data-fancybox="tour-video" href="<?php echo esc_url($tour_video); ?>">
                            <i class="fab fa-youtube"></i>
                        </div>
                    <?php }
                    // Gallery
                    if ( ! empty( $gallery_ids ) ) {
                        foreach ( $gallery_ids as $key => $gallery_item_id ) {
                            $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                            if ( $key === array_key_first( $gallery_ids ) ) {
                                ?>
                                <div data-fancybox="tour-gallery" class="tf-hero-btm-icon tf-tour-gallery" data-src="<?php echo esc_url($image_url); ?>">
                                    <i class="far fa-image"></i>
                            </div>
                         <?php } else {
                                echo '<a data-fancybox="tour-gallery" href="' . esc_url($image_url) . '" style="display:none;"></a>';
                            }
                        }
                    }
                    ?>
                    <?php

                        if (  $email || $phone || $fax || $website) {
                            ?>
                            <div class="tf-hero-btm-icon tf-tour-info" data-fancybox data-src="#tf-contact-info" href="<?php echo esc_url($tour_video); ?>">
                            <i class="fa fa-circle-info"></i>
                            </div>
                            <div class="tf-contact-info-wrapper" id="tf-contact-info" style="display:none">
                                <div class="tf-contact-info">
                                    <h3><?php echo !empty($meta['contact-info-section-title']) ? esc_html($meta['contact-info-section-title']) : ''; ?></h3>
                                    <?php 
                                    if(!empty($email)){ ?>
                                        <div class="tf-email">
                                            <strong><?php echo esc_html__( 'Email:', 'tourfic' ) ?></strong>
                                            <p><a href="mailto:<?php echo esc_html( $email ) ?>"><?php echo esc_html( $email ) ?></a></p>
                                        </div>
                                    <?php } ?>
                                    <?php 
                                    if(!empty($phone)){ ?>
                                        <div class="tf-phone">
                                            <strong><?php echo esc_html__( 'Phone:', 'tourfic' ) ?></strong>
                                            <p><a href="tel:<?php echo esc_html( $phone ) ?>"><?php echo esc_html( $phone ) ?></a></p>
                                        </div>
                                    <?php } ?>
                                    <?php 
                                    if(!empty($fax)){ ?>
                                        <div class="tf-fax">
                                            <strong><?php echo esc_html__( 'Fax:', 'tourfic' ) ?></strong>
                                            <p><a href="tel:<?php echo esc_html( $fax ) ?>"><?php echo esc_html( $fax ) ?></a></p>
                                        </div>
                                    <?php } ?>
                                    <?php 
                                    if(!empty($website)){ ?>
                                        <div class="tf-website">
                                            <strong><?php echo esc_html__( 'Website:', 'tourfic' ) ?></strong>
                                            <p><a target="_blank" href="<?php echo esc_html( $website ) ?>"><?php echo esc_html( $website ) ?></a></p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php }	?>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero section end -->

    <!-- Start title area -->
    <div class="tf-title-area tf-tour-title sp-30">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Address::render(); ?>
                </div>

                <div class="tf-title-right" style="align-items: flex-end">
                    <?php \Tourfic\App\Templates\Components\Tour\Single\Tour_Price::render() ?>
                    
                    <?php if ($tf_booking_type == 2 && $tf_hide_booking_form == 1):?>
                        <a href="<?php echo esc_url($tf_booking_url) ?>" target="_blank" class="tf_btn" style="margin-left: 16px;"><?php echo esc_html($tf_tour_single_book_now_text); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End title area -->

    <!-- Start description -->
    <div class="description-section sp-30">
        <div class="tf-container">
            <?php 
                \Tourfic\App\Templates\Components\Shared\Single\Description::render([
                    'limit_content' => 'no',
                    'wrapper_open' => '<div class="tf-mt-16">',
                    'wrapper_close' => '</div>'
                ]); 

                \Tourfic\App\Templates\Components\Tour\Single\Tour_Info_Cards::render([
                    'info_cards_style' => 'style2',
                    'wrapper_open' => '<div class="sp-20">', 
                    'wrapper_close' => '</div>'
                ]); 
            ?>
        </div>
    </div>
    <!-- End description -->
    
    <?php
    \Tourfic\App\Templates\Components\Shared\Single\Highlights::render([
        'highlights_style' => 'style2',
        'container' => 'yes',
        'wrapper_class' => 'sp-50',
    ]);
    
    if( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
        \Tourfic\App\Templates\Components\Shared\Single\Feature::render([
            'wrapper_open' => '<div class="sp-50"><div class="tf-container">', 
            'wrapper_close' => '</div></div>'
        ]);
    }

    \Tourfic\App\Templates\Components\Shared\Single\Included_Excluded::render([
        'included_excluded_style' => 'style3',
        'wrapper' => 'no',
    ]);
    
    \Tourfic\App\Templates\Components\Shared\Single\Itinerary::render([
        'itinerary_style' => 'style3',
        'wrapper' => 'no',
    ]); 
    ?>

    <!-- FAQ section Start -->
    <?php if ( $faqs ): ?>
        <div class="tf-faq-wrapper tour-faq sp-50">
            <div class="tf-container">
                <div class="tf-faq-sec-title">
                    <h2 class="section-heading"><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h2>
                    <p><?php esc_html_e( "Let’s clarify your confusions. Here are some of the Frequently Asked Questions which most of our client asks.", 'tourfic' ); ?></p>
                </div>

                <div class="tf-section-flex tf-flex">
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Enquiry::render([
                        'wrapper_open' => '<div class="tf-tour-enquiry">',
                        'wrapper_close' => '</div>',
                    ]); ?>
                    <div class="tf-faq-items-wrapper">
                        <?php foreach ( $faqs as $key => $faq ): ?>
                            <div id="tf-faq-item">
                                <div class="tf-faq-title">
                                    <h4><?php echo esc_html( $faq['title'] ); ?></h4>
                                    <i class="fas fa-angle-down arrow"></i>
                                </div>
                                <div class="tf-faq-desc">
                                    <?php echo wp_kses_post( $faq['desc'] ); ?>
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
    \Tourfic\App\Templates\Components\Shared\Single\Terms_And_Conditions::render([
        'wrapper_open' => '<div class="toc-section sp-50"><div class="tf-container">',
        'wrapper_close' => '</div></div>',
    ]);

    \Tourfic\App\Templates\Components\Shared\Single\Review::render([
        'review_style' => 'design-3',
        'container' => 'yes',
        'wrapper' => 'no',
        'wrapper_open' => '<div class="sp-50">',
        'wrapper_close' => '</div>',
    ]);
	
	\Tourfic\App\Templates\Components\Shared\Single\Related_Post::render([
		'related_post_style' => 'style3', 
		'container' => 'yes',
        'wrapper' => 'no',
        'wrapper_class' => 'sp-50',
	]); 
	?>
    <?php do_action( 'tf_after_container' ); ?>
</div>