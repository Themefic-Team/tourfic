<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
?>

<div class="tf-single-template__legacy">
	<?php do_action( 'tf_before_container' ); ?>

    <div class="tf-title-area tf-apartment-title">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
                    <div class="tf-title-left-bottom">
						<?php \Tourfic\App\Templates\Components\Shared\Single\Address::render(); ?>
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
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Share::render(['share_style' => 'style3', 'design' => 'design-2']); ?>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Wishlist::render(['icon_type' => 'simple']); ?>
                </div>
            </div>
        </div>
    </div>

    <?php \Tourfic\App\Templates\Components\Shared\Single\Gallery::render(['gallery_style' => 'style3']); ?>

    <div class="content-feature-section">
        <div class="tf-container">
            <div class="tf-apartment-content-wrapper">
                <div class="tf-apartment-left">

					<?php 
                        \Tourfic\App\Templates\Components\Shared\Single\Highlights::render(['highlights_style' => 'style2']);

                        \Tourfic\App\Templates\Components\Shared\Single\Description::render([
                            'show_title' => 'yes',
                            'limit_content' => 'no',
                            'wrapper_open' => '<div class="tf-mb-50">',
                            'wrapper_close' => '</div>'
                        ]); 

                        \Tourfic\App\Templates\Components\Shared\Single\Rooms::render([
                            'room_style' => 'style2', 
                            'wrapper' => 'no'
                        ]);

                        \Tourfic\App\Templates\Components\Shared\Single\Amenities::render(['amenities_style' => 'style2']); 
                        ?>
                </div>
                <!-- Host details -->
                <div class="tf-apartment-right">
					<?php 
                    \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render([
                        'booking_form_style' => 'style2',
                        'wrapper' => 'no',
                    ]);

                    \Tourfic\App\Templates\Components\Apartment\Single\Host_Info::render(); 
                    ?>
                </div>
            </div>
        </div>
    </div>

	<?php if ( ! empty( $map['address'] ) || isset( $meta['surroundings_places'] ) && ! empty( Helper::tf_data_types( $meta['surroundings_places'] ) ) ): ?>
        <div id="apartment-map" class="tf-apartment-map-wrapper">
            <div class="tf-container">
                <div class="tf-row">
                    <div class="tf-map-content-wrapper <?php echo empty( $map['address'] ) || empty( $meta['surroundings_places'] ) ? 'tf-map-content-full' : ''; ?> <?php echo ! function_exists( 'is_tf_pro' ) ? 'tf-map-content-full' : '' ?>">
						<?php \Tourfic\App\Templates\Components\Shared\Single\Map::render(['design' => 'design-2'], '', '600px'); ?>

						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ){
                            \Tourfic\App\Templates\Components\Shared\Single\Nearby_Places::render([
                                'nearby_places_style' => 'style2',
                            ]);
                        } ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php 
    \Tourfic\App\Templates\Components\Shared\Single\Review::render([
        'review_style' => 'design-1',
        'container' => 'yes',
        'wrapper' => 'no',
        'wrapper_open' => '<div class="tf-apartment-review">',
        'wrapper_close' => '</div>',
    ]);
 
    \Tourfic\App\Templates\Components\Shared\Single\House_Rules::render([
        'house_rules_style' => 'style2', 
        'container' => 'yes',
    ]); 
   
    \Tourfic\App\Templates\Components\Shared\Single\FAQ::render([
        'wrapper_open' => '<div class="tf-faq-wrapper tf-apartment-faq">',
        'wrapper_close' => '</div>',
        'wrapper' => 'no',
        'container' => 'yes',
        'tf_faq_icon_postion' => 'left',
        'faq_style' => 'style4',
        'show_description' => 'yes',
    ]);
    
    \Tourfic\App\Templates\Components\Shared\Single\Enquiry::render([
        'icon_type' => 'simple',
        'enquiry_style' => 'style2',
        'container' => 'yes',
    ]);
	
    \Tourfic\App\Templates\Components\Shared\Single\Terms_And_Conditions::render([
        'wrapper_open' => '<div class="toc-section apartment-toc"><div class="tf-container">',
        'wrapper_close' => '</div></div>',
    ]);

	\Tourfic\App\Templates\Components\Shared\Single\Related_Post::render([
		'related_post_style' => 'style2', 
		'container' => 'yes',
        'wrapper' => 'no',
	]); 
    
    do_action( 'tf_after_container' ); 
    ?>
</div>