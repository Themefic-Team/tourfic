<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Hotel\Pricing;
use \Tourfic\Classes\Hotel\Hotel;

$tf_booking_type = '1';
$tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_ext_booking_code = '';
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
	$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
	$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
	$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
    $tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
    $tf_ext_booking_code = !empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
}
if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
	$external_search_info = array(
		'{adult}'    => ! empty( $adult ) ? $adult : 1,
		'{child}'    => ! empty( $child ) ? $child : 0,
		'{checkin}'  => ! empty( $check_in ) ? $check_in : gmdate( 'Y-m-d' ),
		'{checkout}' => ! empty( $check_out ) ? $check_out : gmdate( 'Y-m-d', strtotime( '+1 day' ) ),
		'{room}'     => ! empty( $room_selected ) ? $room_selected : 1,
	);
	if ( ! empty( $tf_booking_attribute ) ) {
		$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
		if ( ! empty( $tf_booking_query_url ) ) {
			$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
		}
	}
}

$total_room_option_count = Tourfic\Classes\Room\Room::get_room_options_count($rooms);
$price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';
?>
<div class="tf-single-template__legacy">
	<?php do_action( 'tf_before_container' ); ?>

    <!-- Start title area -->
    <div class="tf-title-area tf-hotel-title sp-b-20">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <span class="post-type"><?php esc_html_e( 'Hotel', 'tourfic' ) ?></span>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Address::render(); ?>
                </div>

                <div class="tf-title-right">
					<?php \Tourfic\App\Templates\Components\Global\Single\Wishlist::render(['icon_type' => 'simple']); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Share::render(['share_style' => 'style3']); ?>

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
                        \Tourfic\App\Templates\Components\Global\Single\Gallery::render(['gallery_style' => 'style2']); 
                        
                        \Tourfic\App\Templates\Components\Global\Single\Description::render([
                            'limit_content' => 'no',
                            'wrapper_open' => '<div class="tf-mt-16">',
                            'wrapper_close' => '</div>'
                        ]); 
                        
                        \Tourfic\App\Templates\Components\Global\Single\Feature::render([
                            'wrapper_open' => '<div class="tf-pt-16 tf-pb-30">', 
                            'wrapper_close' => '</div>'
                        ]); 
                    ?>
                </div>
                <div class="hero-right">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Map::render(['show_icon' => 'no', 'design' => 'design-2']); ?>
					
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ||  $tf_booking_type == 3 ) : ?>
                        <div class="tf-hero-booking">
							<?php Hotel::tf_hotel_sidebar_booking_form(); ?>
                        </div>
					<?php endif; ?>
                    <?php if( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && !empty( $tf_ext_booking_code )) : ?>
                        <div id="tf-external-booking-embaded-form" class="tf-hero-booking">
                            <?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ) ?>
                        </div>
                    <?php endif; ?>
					<?php
					$places_section_title = ! empty( $meta["section-title"] ) ? $meta["section-title"] : __( "What's around?", 'tourfic' );
					$places_meta          = ! empty( $meta["nearby-places"] ) ? Helper::tf_data_types($meta["nearby-places"]) : array();
					?>
					<?php if ( count( $places_meta ) > 0 ) : ?> <!-- nearby places - start -->
                        <div class="nearby-container">
                            <div class="nearby-container-inner">
								<?php if ( ! empty( $places_section_title ) ): ?>
                                    <h3 class="section-heading"><?php echo esc_html( $places_section_title ); ?></h3>
								<?php endif; ?>
                                <ul>
									<?php foreach ( $places_meta as $place ) {
										$place_icon = '<i class="' . $place['place-icon'] . '"></i>';
										?>
                                        <li>
                                            <span>
                                                <?php echo wp_kses_post( $place_icon ); ?><?php echo esc_html( $place["place-title"] ); ?>
                                            </span>
                                            <span>
                                                <?php echo esc_html( $place["place-dist"] ); ?>
                                            </span>
                                        </li>
									<?php }; ?>
                                </ul>
                            </div>
                        </div>
					<?php endif; ?> <!-- nearby places - end -->

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
    \Tourfic\App\Templates\Components\Global\Single\Rooms::render([
        'room_style' => 'style3',
        'container'  => 'yes',
        'wrapper'    => 'no',
        'wrapper_open' => '<div class="sp-50">',
        'wrapper_close' => '</div>'
    ]); 
    
    \Tourfic\App\Templates\Components\Global\Single\Amenities::render([
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
					<?php \Tourfic\App\Templates\Components\Global\Single\Enquiry::render([
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

    <!-- Start Review Section -->
	<?php if ( ! $disable_review_sec == 1 ) { ?>
        <div id="tf-review" class="review-section sp-50">
            <div class="tf-container">
                <div class="reviews">
                    <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<?php comments_template(); ?>
                </div>
            </div>
        </div>
	<?php } ?>
    <!-- End Review Section -->

	<?php
    \Tourfic\App\Templates\Components\Global\Single\Terms_And_Conditions::render([
        'wrapper_open' => '<div class="toc-section sp-50"><div class="tf-container">',
        'wrapper_close' => '</div></div>',
    ]);
	?>

	<?php do_action( 'tf_after_container' ); ?>
</div>