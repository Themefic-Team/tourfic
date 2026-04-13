<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Apartment\Apartment;
?>

<div class="tf-single-template__legacy">
	<?php do_action( 'tf_before_container' ); ?>

    <div class="tf-title-area tf-apartment-title">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
                    <div class="tf-title-left-bottom">
						<?php \Tourfic\App\Templates\Components\Global\Single\Address::render(); ?>
						<?php if ( $comments && ! $disable_review_sec == '1' ): ?>
                            <div class="tf-top-review">
                                <a href="#tf-review">
                                    <div class="tf-single-rating">
                                        <i class="fas fa-star"></i>
                                        <span><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments ) ); ?></span>
                                        (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
                                    </div>
                                </a>
                            </div>
						<?php endif; ?>
                    </div>
                </div>

                <div class="tf-title-right">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Share::render(['share_style' => 'style3', 'design' => 'design-2']); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Wishlist::render(['icon_type' => 'simple']); ?>
                </div>
            </div>
        </div>
    </div>

    <?php \Tourfic\App\Templates\Components\Global\Single\Gallery::render(['gallery_style' => 'style3']); ?>

    <div class="content-feature-section">
        <div class="tf-container">
            <div class="tf-apartment-content-wrapper">
                <div class="tf-apartment-left">

					<?php 
                        \Tourfic\App\Templates\Components\Global\Single\Highlights::render([
                            'highlights_style' => 'style2'
                        ]);

                        \Tourfic\App\Templates\Components\Global\Single\Description::render([
                            'show_title' => 'yes',
                            'limit_content' => 'no',
                            'wrapper_open' => '<div class="tf-mb-50">',
                            'wrapper_close' => '</div>'
                        ]); 
                    ?>

					<?php \Tourfic\App\Templates\Components\Global\Single\Rooms::render(['room_style' => 'style2', 'wrapper' => 'no']); ?>

                    <?php \Tourfic\App\Templates\Components\Global\Single\Amenities::render(['amenities_style' => 'style2']); ?>
                </div>
                <!-- Host details -->
                <div class="tf-apartment-right">
                    <div class="apartment-booking-form">
						<?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec ); ?>
                    </div>

					<?php \Tourfic\App\Templates\Components\Apartment\Single\Host_Info::render(); ?>
                </div>
            </div>
        </div>
    </div>

	<?php if ( ! empty( $map['address'] ) || isset( $meta['surroundings_places'] ) && ! empty( Helper::tf_data_types( $meta['surroundings_places'] ) ) ): ?>
        <div id="apartment-map" class="tf-apartment-map-wrapper">
            <div class="tf-container">
                <div class="tf-row">
                    <div class="tf-map-content-wrapper <?php echo empty( $map['address'] ) || empty( $meta['surroundings_places'] ) ? 'tf-map-content-full' : ''; ?> <?php echo ! function_exists( 'is_tf_pro' ) ? 'tf-map-content-full' : '' ?>">
						<?php \Tourfic\App\Templates\Components\Global\Single\Map::render(['design' => 'design-2'], '', '600px'); ?>

						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ){
                            \Tourfic\App\Templates\Components\Global\Single\Nearby_Places::render([
                                'nearby_places_style' => 'style2',
                            ]);
                        } ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php if ( $disable_review_sec !== '1' ) : ?>
        <div id="tf-review" class="review-section tf-apartment-review">
            <div class="tf-container">
                <div class="reviews">
                    <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<?php comments_template(); ?>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php 
    \Tourfic\App\Templates\Components\Global\Single\House_Rules::render([
        'house_rules_style' => 'style2', 
        'container' => 'yes',
    ]); 
    ?>

	<?php if ( isset( $meta['faq'] ) && ! empty( Helper::tf_data_types( $meta['faq'] ) ) ): ?>
        <!-- FAQ section Start -->
        <div class="tf-faq-wrapper tf-apartment-faq">
            <div class="tf-container">
                <div class="tf-faq-sec-title">
					<?php echo ! empty( $meta['faq_title'] ) ? '<h2 class="section-heading">' . esc_html( $meta['faq_title'] ) . '</h2>' : ''; ?>
					<?php echo ! empty( $meta['faq_desc'] ) ? '<p>' . wp_kses_post( $meta['faq_desc'] ) . '</p>' : ''; ?>
                </div>

                <div class="tf-faq-content-wrapper">
                    <div class="tf-faq-items-wrapper">
						<?php foreach ( Helper::tf_data_types( $meta['faq'] ) as $key => $faq ): ?>
                            <div id="tf-faq-item">
                                <div class="tf-faq-title <?php echo $key === 0 ? esc_attr( 'active' ) : ''; ?>">
                                    <svg class="tf-faq-minus" xmlns="http://www.w3.org/2000/svg" width="19" height="1" viewBox="0 0 19 1" fill="none">
                                        <rect width="19" height="1" fill="#2979FF"/>
                                    </svg>
                                    <svg class="tf-faq-plus" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                        <rect y="9" width="19" height="1" fill="#2979FF"/>
                                        <rect x="9" width="1" height="19" fill="#2979FF"/>
                                    </svg>
                                    <h4><?php echo esc_html( $faq['title'] ); ?></h4>
                                </div>
                                <div class="tf-faq-desc" <?php echo $key === 0 ? 'style="display: block;"' : ''; ?>>
									<?php echo wp_kses_post( $faq['description'] ); ?>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- FAQ section end -->
	<?php endif; ?>

	<?php
    \Tourfic\App\Templates\Components\Global\Single\Enquiry::render([
        'icon_type' => 'simple',
        'enquiry_style' => 'style2',
        'container' => 'yes',
    ]);
	
    \Tourfic\App\Templates\Components\Global\Single\Terms_And_Conditions::render([
        'wrapper_open' => '<div class="toc-section apartment-toc"><div class="tf-container">',
        'wrapper_close' => '</div></div>',
    ]);

	\Tourfic\App\Templates\Components\Global\Single\Related_Post::render([
		'related_post_style' => 'style2', 
		'container' => 'yes',
        'wrapper' => 'no',
	]); 
    
    do_action( 'tf_after_container' ); 
    ?>
</div>