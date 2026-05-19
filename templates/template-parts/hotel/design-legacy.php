<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
?>
<div class="tf-single-template__legacy">
	<?php do_action( 'tf_before_container' ); ?>

    <!-- Start title area -->
    <div class="tf-title-area tf-hotel-title sp-b-20">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <span class="post-type"><?php esc_html_e( 'Hotel', 'tourfic' ) ?></span>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Address::render(); ?>
                </div>

                <div class="tf-title-right">
					<?php \Tourfic\App\Templates\Components\Shared\Single\Wishlist::render(['icon_type' => 'simple']); ?>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Share::render(['share_style' => 'style3']); ?>

                    <div class="reserve-button">
                        <a href="#rooms" class="tf-btn-flip" data-back="<?php esc_attr_e( 'View Rooms', 'tourfic' ); ?>" data-front="<?php esc_attr_e( 'Reserve Now', 'tourfic' ); ?>"></a>
                    </div>
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
                    <?php 
                        \Tourfic\App\Templates\Components\Shared\Single\Gallery::render(['gallery_style' => 'style2']); 
                        
                        \Tourfic\App\Templates\Components\Shared\Single\Description::render([
                            'limit_content' => 'no',
                            'wrapper_open' => '<div class="tf-mt-16">',
                            'wrapper_close' => '</div>'
                        ]); 
                        
                        \Tourfic\App\Templates\Components\Shared\Single\Feature::render([
                            'wrapper_open' => '<div class="tf-pt-16 tf-pb-30">', 
                            'wrapper_close' => '</div>'
                        ]); 
                    ?>
                </div>
                <div class="hero-right">
                    <?php 
                    \Tourfic\App\Templates\Components\Shared\Single\Map::render([
                        'show_icon' => 'no',
                        'show_title' => 'no',
                        'design' => 'design-2'
                    ]); 
                    
                    \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render([
                        'booking_form_style' => 'style3',
                        'wrapper' => 'no',
                    ]);

                    \Tourfic\App\Templates\Components\Shared\Single\Nearby_Places::render([
                        'nearby_places_style' => 'style2',
                        'wrapper_open' => '<div class="nearby-container"><div class="nearby-container-inner">',
                        'wrapper_close' => '</div></div>',
                    ]);
                    ?>

                    <!-- Hotel Single Widget Hook are - start -->
                    <div class="tf-hotel-single-custom-widget-wrap tf-single-widgets">
						<?php do_action( "tf_hotel_single_widgets" ); ?>
                        <?php do_action( "tf_single_hotel_sidebar_area_with_args", $post_id ); ?>
                    </div>
                    <!-- Hotel Single Widget Hook are - end -->
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>

    <?php 
    \Tourfic\App\Templates\Components\Shared\Single\Rooms::render([
        'room_style' => 'style3',
        'container'  => 'yes',
        'wrapper'    => 'no',
        'wrapper_open' => '<div class="sp-50">',
        'wrapper_close' => '</div>'
    ]); 
    
    \Tourfic\App\Templates\Components\Shared\Single\Amenities::render([
        'amenities_style' => 'style2', 
        'container' => 'yes'
    ]); 
    ?>

    <!-- FAQ section Start -->
	<?php if ( $faqs ): ?>
        <div class="tf-hotel-faqs-section sp-50 tf-template-section">
            <div class="tf-container">
                <h2 class="section-heading tf-section-title"><?php echo ! empty( $meta['faq-section-title'] ) ? esc_html( $meta['faq-section-title'] ) : ''; ?></h2>
                <div class="tf-section-flex tf-flex">
					<?php \Tourfic\App\Templates\Components\Shared\Single\Enquiry::render([
                        'wrapper_open' => '<div class="tf-hotel-enquiry">',
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
                                    <p><?php echo wp_kses_post( $faq['description'] ); ?></p>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>
    <!-- FAQ section end -->

    <?php if ( ! defined( 'TF_PRO' ) && ( $address ) ) { ?>
        <div class="map-for-mobile">
            <div class="show-on-map">
                <div class="tf-container">
                    <div class="tf-btn-wrap">
                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>" target="_blank" class="tf_btn tf_btn_full">
                            <span>
                                <i class="fas fa-map-marker-alt"></i>
                                <?php esc_html_e( 'Show on map', 'tourfic' ); ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || ( ! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
        <div class="popupmap-for-mobile">
            <div class="tf-container">
                <?php
                if ( $tf_openstreet_map != "default" ) { ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="150"
                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>">
                            <div class="tf-btn-wrap">
                                <span class="tf_btn tf_btn_secondary tf_btn_full"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                            </div>
                        </a>

                    </div>
                <?php } ?>
                <?php if ( $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) { ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <div id="mobile-hotel-location" style="height: 130px;"></div>

                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>">
                            <div class="tf-btn-wrap">
                                <span class="tf_btn tf_btn_secondary tf_btn_full"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                            </div>
                        </a>
                    </div>
                <?php } ?>
                <?php if ( $tf_openstreet_map == "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ) { ?>

                    <div class="tf-hotel-location-preview show-on-map">

                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="150" style="border:0;"
                                allowfullscreen="" loading="lazy"></iframe>

                        <a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>">
                            <div class="tf-btn-wrap">
                                <span class="tf_btn tf_btn_secondary tf_btn_full"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                            </div>
                        </a>

                    </div>

                <?php } ?>
                <div style="display: none;" id="tf-hotel-google-maps">
                    <div class="tf-hotel-google-maps-container">
                        <?php
                        if ( ! empty( $address ) ) {
                            ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=15&output=embed" width="100%" height="550" style="border:0;"
                                    allowfullscreen="" loading="lazy"></iframe>
                        <?php } else { ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=15&output=embed" width="100%"
                                    height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

	<?php 
    \Tourfic\App\Templates\Components\Shared\Single\Review::render([
        'review_style' => 'design-3',
        'container' => 'yes',
        'wrapper' => 'no',
        'wrapper_open' => '<div class="sp-50">',
        'wrapper_close' => '</div>',
    ]);
    
    \Tourfic\App\Templates\Components\Shared\Single\Terms_And_Conditions::render([
        'wrapper_open' => '<div class="toc-section sp-50"><div class="tf-container">',
        'wrapper_close' => '</div></div>',
    ]);
	?>

	<?php do_action( 'tf_after_container' ); ?>
</div>