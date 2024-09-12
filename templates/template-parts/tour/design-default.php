<?php

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Tour\Tour_Price;

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
<div class="tf-main-wrapper">
    <?php do_action( 'tf_before_container' ); ?>
    <!-- Hero section Start -->
    <div class="tf-hero-wrapper">
        <div class="tf-container">
            <div class="tf-hero-content" style="background-image: url(<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>);">
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
                    <div class="tf-wishlist">
                        <?php
                        // Wishlist
                        if($disable_wishlist_tour==0){
                            if ( is_user_logged_in() ) {
                            if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) { ?>
                                <span class="single-tour-wish-bt"><i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"  data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                    echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                                } ?>></i></span>
                            <?php }
                            } else {
                            if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) { ?>
                            <span class="single-tour-wish-bt"><i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                            } ?>></i></span>
                            <?php } } ?>
                        <?php }else{
                        if ( Helper::tfopt( 'wl-bt-for' ) && in_array( '2', Helper::tfopt( 'wl-bt-for' ) ) ) {
                            if ( is_user_logged_in() ) {
                                if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) {
                                    ?>
                                    <span class="single-tour-wish-bt"><i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"  data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                        echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                                    } ?>></i></span>
                                    <?php
                                }
                            } else {
                                if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) {
                                    ?>
                                    <span class="single-tour-wish-bt"><i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>" data-id="<?php echo esc_attr($post_id) ?>" data-type="<?php echo esc_attr($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
                                        echo 'data-page-title="' . esc_attr(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
                                    } ?>></i></span>
                                    <?php
                                }
                            }
                        } }
                        ?>
                    </div>
                </div>
	            <?php if( ($tf_booking_type == 2 && $tf_hide_booking_form !== '1') || $tf_booking_type == 1 || $tf_booking_type == 3) : ?>
                    <div class="tf-tours-form-wrap">
                        <?php echo wp_kses(Tour::tf_single_tour_booking_form( $post->ID ), Helper::tf_custom_wp_kses_allow_tags()); ?>
                    </div>
                <?php endif; ?>
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
                    <h1><?php the_title(); ?></h1>
                    <!-- Start map link -->
                    <div class="tf-map-link" id="tf-map-location" data-location="<?php echo esc_attr( $location ) ?>">
                        <?php if ( $location ) {
                            echo '<a href="#tour-map"><span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . wp_kses_post($location) . '.</span></a>';
                        } ?>
                    </div>
                    <!-- End map link -->
                </div>

                <div class="tf-title-right" style="align-items: flex-end">
                    <?php if(($tf_booking_type == 2 && $tf_hide_price !== '1') || $tf_booking_type == 1 || $tf_booking_type == 3) : ?>
                        <div class="tf-single-tour-pricing">
                            <?php if ( $pricing_rule == 'group' ) { ?>

                                <div class="tf-price group-price">
                                    <span class="sale-price">
                                        <?php echo !empty($tour_price->wc_sale_group) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group); ?>
                                    </span>
                                    <?php echo ( $discount_type != 'none' ) ? '<del>' . wp_kses_post($tour_price->wc_group) . '</del>' : ''; ?>
                                </div>

                            <?php } elseif ( $pricing_rule == 'person' ) { ?>

                                <?php if ( ! $disable_adult && ! empty( $tour_price->adult ) ) { ?>

                                    <div class="tf-price adult-price">
                                        <span class="sale-price">
                                            <?php echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult); ?>
                                        </span>
                                        <?php echo ( $discount_type != 'none' ) ? '<del>' . wp_kses_post($tour_price->wc_adult) . '</del>' : ''; ?>
                                    </div>

                                <?php }
                                if ( ! $disable_child && ! empty( $tour_price->child ) ) { ?>

                                    <div class="tf-price child-price tf-d-n">
                                        <span class="sale-price">
                                            <?php echo !empty($tour_price->wc_sale_child) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child); ?>
                                        </span>
                                        <?php echo ( $discount_type != 'none' ) ? '<del>' . wp_kses_post($tour_price->wc_child) . '</del>' : ''; ?>
                                    </div>

                            <?php }
                            if ( !$disable_adult && (! $disable_infant && ! empty( $tour_price->infant )) ) { ?>

                                    <div class="tf-price infant-price tf-d-n">
                                        <span class="sale-price">
                                            <?php echo !empty($tour_price->wc_sale_infant) ? wp_kses_post($tour_price->wc_sale_infant) : wp_kses_post($tour_price->wc_infant); ?>
                                        </span>
                                        <?php echo ( $discount_type != 'none' ) ? '<del>' . wp_kses_post($tour_price->wc_infant) . '</del>' : ''; ?>
                                    </div>

                                <?php } ?>
                                <?php
                            }
                            ?>
                            <ul class="tf-price-tab">
                                <?php
                                if ( $pricing_rule == 'group' ) {

                                    echo '<li id="group" class="active">' . esc_html__( "Group", "tourfic" ) . '</li>';

                                } elseif ( $pricing_rule == 'person' ) {

                                if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                    echo '<li id="adult" class="active">' . esc_html__( "Adult", "tourfic" ) . '</li>';
                                }
                                if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                    echo '<li id="child">' . esc_html__( "Child", "tourfic" ) . '</li>';
                                }
                                if ( !$disable_adult && (! $disable_infant && ! empty( $tour_price->infant )) ) {
                                    echo '<li id="infant">' . esc_html__( "Infant", "tourfic" ) . '</li>';
                                }

                                }
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if ($tf_booking_type == 2 && $tf_hide_booking_form == 1):?>
                        <a href="<?php echo esc_url($tf_booking_url) ?>" target="_blank" class="tf_button btn-styled" style="margin-left: 16px;"><?php esc_html_e('Book now', 'tourfic'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End title area -->

    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>

    <!-- Start description -->
    <div class="description-section sp-30">
        <div class="tf-container">
            <div class="desc-wrap">
                <?php the_content(); ?>
            </div>

            <!-- Square block section Start -->
            <?php if ( $tour_duration || $info_tour_type || $group_size || $language ) { ?>
                <div class="tf-square-block sp-20">
                    <div class="tf-square-block-content">
                        <?php if ( $tour_duration ) { ?>
                            <div class="tf-single-square-block first">
                                <i class="fas fa-clock"></i>
                                <h4><?php echo esc_html__( 'Duration', 'tourfic' ); ?></h4>
                                <p><?php echo esc_html( $tour_duration ); ?>
                                <span> 
                                    <?php
                                    if( $tour_duration > 1  ){
                                        $dur_string = 's';
                                        $duration_time_html = $duration_time . $dur_string;
                                    }else{
                                        $duration_time_html = $duration_time;
                                    }
                                        echo " " . esc_html( $duration_time_html )?>
                                </span></p>
                                <?php if( $night ){ ?>
                                <p>
                                    <?php echo esc_html( $night_count ); ?>
                                    <span>
                                        <?php
                                        if(!empty($night_count)){
                                            if( $night_count > 1  ){
                                                echo esc_html__( 'Nights', 'tourfic' );
                                            }else{
                                                echo esc_html__( 'Night', 'tourfic'  );
                                            }	
                                        }										
                                        ?>
                                    </span>
                                </p>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ( $info_tour_type ) {

	                        if ( gettype( $info_tour_type ) === 'string' ) {
		                        $info_tour_type = ucfirst( esc_html( $info_tour_type ) );
	                        } else if ( gettype( $info_tour_type ) === 'array' ) {
		                        $tour_types =[];
		                        $types = ! empty( get_the_terms( $post_id, 'tour_type' ) ) ? get_the_terms( $post_id, 'tour_type' ) : '';
		                        if ( ! empty( $types ) ) {
			                        foreach ( $types as $type ) {
				                        $tour_types[] = $type->name;
			                        }
		                        }
                                $info_tour_type = implode( ', ', $tour_types );
	                        }
                            ?>
                            <div class="tf-single-square-block second">
                                <i class="fas fa-map"></i>
                                <h4><?php echo esc_html__( 'Tour Type', 'tourfic' ); ?></h4>
                                <p><?php echo esc_html( $info_tour_type ); ?></p>
                            </div>
                        <?php } ?>
                        <?php if ( $group_size ) { ?>
                            <div class="tf-single-square-block third">
                                <i class="fas fa-users"></i>
                                <h4><?php echo esc_html__( 'Group Size', 'tourfic' ); ?></h4>
                                <p><?php echo esc_html( $group_size ) ?></p>
                            </div>
                        <?php } ?>
                        <?php if ( $language ) { ?>
                            <div class="tf-single-square-block fourth">
                                <i class="fas fa-language"></i>
                                <h4><?php echo esc_html__( 'Language', 'tourfic' ); ?></h4>
                                <p><?php echo esc_html( $language ) ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <!-- Square block section End -->
        </div>
    </div>
    <!-- End description -->
    
    <?php if ( $highlights ) : ?>
    <!-- Highlight section Start -->
    <div class="tf-highlight-wrapper gray-wrap sp-50">
        <div class="tf-container">
            <div class="tf-highlight-content">
                <div class="tf-highlight-item">
                    <div class="tf-highlight-text">
                        <h2 class="section-heading"><?php echo !empty($meta['highlights-section-title']) ? esc_html($meta['highlights-section-title']) : ''; ?></h2>
                        <p><?php echo wp_kses_post($highlights); ?></p>
                    </div>
                    <?php if ( ! empty( $meta['hightlights_thumbnail'] ) ): ?>
                        <div class="tf-highlight-image">
                            <img src="<?php echo esc_url( $meta['hightlights_thumbnail'] ); ?>" alt="">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Highlight section end -->
    <?php endif; ?>

    <?php if( function_exists( 'is_tf_pro' ) && is_tf_pro() ) : ?> 
        <?php if ( $features && !empty($meta["features"])) { ?>
        <!-- Start features -->
        <div class="tf_features sp-50">
            <div class="tf-container">
                <?php if (!empty($meta["tour-features-section-title"])) : ?>
                    <h3 class="tf-title tf-section-title"><?php echo esc_html( $meta["tour-features-section-title"], 'tourfic' ); ?></h3>
                <?php endif; ?>
                <div class="tf-feature-list">
                    <?php foreach ( $features as $feature ) {
                        $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tour_features', true );
                        $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                        if ( $f_icon_type == 'fa' ) {
                            $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                        } elseif ( $f_icon_type == 'c' ) {
                            $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                        } ?>

                        <div class="single-feature-box">
                            <?php echo ( !empty($feature_meta['icon-c']) || !empty($feature_meta['icon-fa']) ) ? wp_kses_post($feature_icon) : ''; ?>
                            <p class="feature-list-title"><?php echo esc_html($feature->name); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- End features -->
    <?php } ?>
    <?php endif; ?>
    <!-- Include-Exclude section Start -->
    <?php
    if ( $inc || $exc ) :
        $inc_exc_bg = ! empty( $meta['include-exclude-bg'] ) ? $meta['include-exclude-bg'] : '';
        ?>
        <div class="tf-inc-exc-wrapper sp-70" style="background-image: url(<?php echo esc_url( $inc_exc_bg ) ?>);">
            <div class="tf-container">
                <div class="tf-inc-exc-content">
                    <?php if ( $inc ) { ?>
                        <div class="tf-include-section <?php echo esc_attr( $custom_inc_icon ); ?>">
                            <h4><?php esc_html_e( 'Included', 'tourfic' ); ?></h4>
                            <ul>
                                <?php
                                foreach ( $inc as $key => $val ) {
                                    echo "<li><i class='" . esc_attr( $inc_icon ) . "'></i>" . wp_kses_post($val['inc']) . "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    <?php } ?>
                    <?php if ( $exc ) { ?>
                        <div class="tf-exclude-section <?php echo esc_attr( $custom_exc_icon ); ?>">
                            <h4><?php esc_html_e( 'Excluded', 'tourfic' ); ?></h4>
                            <ul>
                                <?php
                                foreach ( $exc as $key => $val ) {
                                    echo "<li><i class='" . esc_attr( $exc_icon ) . "'></i>" . wp_kses_post($val['exc']) . "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Include-Exclude section End -->

    <!-- Travel Itinerary section Start -->
    <?php
    if ( function_exists('is_tf_pro') && is_tf_pro() ) {
        do_action( 'after_itinerary_builder', $itineraries, $itinerary_map );
    } else {
        ?>
        <!-- Travel Itinerary section Start -->
        <?php if ( $itineraries ) { ?>
            <div class="tf-travel-itinerary-wrapper gray-wrap sp-50">
                <div class="tf-container">
                    <div class="tf-travel-itinerary-content">
                        <h2 class="section-heading"><?php echo !empty($meta['itinerary-section-title']) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
                        <div class="tf-travel-itinerary-items-wrapper">
                            <?php 
                            foreach ( $itineraries as $itinerary ) {
                            ?>
                                <div id="tf-accordion-wrapper">
                                    <div class="tf-accordion-head">
                                        <div class="tf-travel-time">
                                            <span><?php echo esc_html( $itinerary['time'] ) ?></span>
                                        </div>
                                        <h4><?php echo esc_html( $itinerary['title'] ); ?></h4>
                                        <i class="fas fa-angle-down arrow"></i>
                                    </div>
                                    <div class="tf-accordion-content">
                                        <div class="tf-travel-desc">
                                            <?php if ( $itinerary['image'] ) {
                                                echo '<div class="tf-ititnerary-img"><a class="tf-itinerary-gallery" href="' . esc_url( $itinerary['image'] ) . '"><img src="' . esc_url( $itinerary['image'] ) . '"></a></div>';
                                            } ?>
                                            <div class="trav-cont tf-travel-description">
                                                <p><?php echo wp_kses_post( $itinerary['desc'] ); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ( $location && $itinerary_map != 1 ): ?>
            <div id="tour-map" class="tf-map-wrapper">
                <div class="tf-container">
                    <div class="tf-row">
                        <?php if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) && empty($tf_google_map_key) ) {  ?>
                            <div id="tour-location" style="height: 500px;"></div>
                            <script>
                            const map = L.map('tour-location').setView([<?php echo esc_html($location_latitude); ?>, <?php echo esc_html($location_longitude); ?>], <?php echo esc_html($location_zoom); ?>);

                            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 20,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            const marker = L.marker([<?php echo esc_html($location_latitude); ?>, <?php echo esc_html($location_longitude); ?>], {alt: '<?php echo esc_html($location); ?>'}).addTo(map)
                                .bindPopup('<?php echo esc_html($location); ?>');
                            </script>
                        <?php } ?>
                        <?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) && empty($tf_google_map_key) ) {  ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                        <?php if( $tf_openstreet_map!="default" && !empty($tf_google_map_key) ){ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php
        }
    } 
    ?>
    <!-- Travel Itinerary section End -->

    <!-- Map Section Start -->
    <?php if ( $location && $itinerary_map != 1 && ! $itineraries ): ?>
        <div class="tf-map-wrapper">
            <div class="tf-container">
                <div class="tf-row">
                    <?php if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) ) {  ?>
                        <div id="tour-location" style="height: 500px;"></div>
                        <script>
                        const map = L.map('tour-location').setView([<?php echo esc_html($location_latitude); ?>, <?php echo esc_html($location_longitude); ?>], <?php echo esc_html($location_zoom); ?>);

                        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 20,
                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(map);

                        const marker = L.marker([<?php echo esc_html($location_latitude); ?>, <?php echo esc_html($location_longitude); ?>], {alt: '<?php echo esc_html($location); ?>'}).addTo(map)
                            .bindPopup('<?php echo esc_html($location); ?>');
                        </script>
                    <?php } ?>
                    <?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) && empty($tf_google_map_key) ) {  ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?>
                    <?php if( $tf_openstreet_map!="default" && !empty($tf_google_map_key) ){ ?>
                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?> 
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Map Section End -->

    <!-- FAQ section Start -->
    <?php if ( $faqs ): ?>
        <div class="tf-faq-wrapper tour-faq sp-50">
            <div class="tf-container">
                <div class="tf-faq-sec-title">
                    <h2 class="section-heading"><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h2>
                    <p><?php esc_html_e( "Let’s clarify your confusions. Here are some of the Frequently Asked Questions which most of our client asks.", 'tourfic' ); ?></p>
                </div>

                <div class="tf-faq-content-wrapper">
                    <?php 
                    $tf_enquiry_section_status = !empty($meta['t-enquiry-section']) ? $meta['t-enquiry-section'] : "";
                    $tf_enquiry_section_icon = !empty($meta['t-enquiry-option-icon']) ? esc_html($meta['t-enquiry-option-icon']) : '';
                    $tf_enquiry_section_title = !empty($meta['t-enquiry-option-title']) ? esc_html($meta['t-enquiry-option-title']) : '';
                    $tf_enquiry_section_des = !empty($meta['t-enquiry-option-content']) ? esc_html($meta['t-enquiry-option-content']) : '';
                    $enquery_button_text = !empty($meta['t-enquiry-option-btn']) ? esc_html($meta['t-enquiry-option-btn']) : '';

                    if(!empty($tf_enquiry_section_status) && ( !empty($tf_enquiry_section_icon) || !empty($tf_enquiry_section_title) || !empty($enquery_button_text))){
                    ?>
                    
                    <div class="tf-ask-question">
                        <div class="default-enquiry-title-section">
                            <?php
                            if(!empty($tf_enquiry_section_icon)) {
                                ?>
                                <i class="<?php echo esc_attr($tf_enquiry_section_icon) ?>" aria-hidden="true"></i>
                                <?php
                            }
                            if(!empty($tf_enquiry_section_title)) {
                                ?>
                                <h3><?php echo esc_html($tf_enquiry_section_title) ?></h3>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if(!empty($tf_enquiry_section_des)) {
                            ?>
                                <p><?php echo wp_kses_post($tf_enquiry_section_des); ?></p>
                            <?php
                            }
                            ?>
                        <?php 
                        if(!empty($enquery_button_text)) {
                            ?>
                            <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="btn-styled">
                                <span><?php echo esc_html($enquery_button_text); ?>
                            </span></a></div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php } ?>
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

    <!-- Start TOC Content -->
    <?php if ( $terms_and_conditions ) : ?>
        <div class="toc-section gray-wrap sp-50">
            <div class="tf-container">
                <div class="tf-toc-wrap">
                    <h2 class="section-heading"><?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : ''; ?></h2>
                    <div class="tf-toc-inner">
                        <?php echo wp_kses_post(wpautop( $terms_and_conditions )); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- End TOC Content -->

    <!-- Start Review Section -->
    <?php if ( ! $disable_review_sec == 1 ) { ?>
        <div id="tf-review" class="review-section sp-50">
            <div class="tf-container">
                <div class="reviews">
                    <h2 class="section-heading"><?php echo !empty($meta['review-section-title']) ? esc_html($meta['review-section-title']) : ''; ?></h2>
                    <?php comments_template(); ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- End Review Section -->

    <!-- Tours suggestion section Start -->
    <?php if ( ! $disable_related_tour == '1' ) {

        $related_tour_type = Helper::tfopt('rt_display');
        $args  = array(
            'post_type'      => 'tf_tours',
            'post_status'    => 'publish',
            'posts_per_page' => 8,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'tour_destination',
                    'field'    => 'slug',
                    'terms'    => $first_destination_slug,
                ),
            ),
        );
        //show related tour based on selected tours
        $selected_ids = !empty(Helper::tfopt( 'tf-related-tours' )) ? Helper::tfopt( 'tf-related-tours' ) : array();

        if ( $related_tour_type == 'selected') {
            if(in_array($post_id, $selected_ids)) {
                $index = array_search($post_id, $selected_ids);

	            $current_post_id = array($selected_ids[$index]);

                unset($selected_ids[$index]);
            } else{
                $current_post_id = array($post_id);
            }

            if(count($selected_ids) > 0) {
                $args['post__in'] = $selected_ids;
            } else {
                $args['post__in'] = array(-1);
            }
        } else {
	        $current_post_id = array($post_id);
        }

        $tours = new WP_Query( $args );

        $all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function($id) use ($current_post_id) {
			return $id != $current_post_id[0];
		});

        if ( $tours->have_posts() ) {
            ?>
            <div class="tf-suggestion-wrapper gray-wrap sp-50">
                <div class="tf-container">
                    <div class="tf-slider-content-wrapper">
                        <div class="tf-suggestion-sec-head">
                            <?php 
                            if( !empty( Helper::tfopt('rt-title') ) ){ ?>
                                <h2 class="section-heading"><?php echo esc_html( Helper::tfopt('rt-title') ) ?></h2>
                            <?php } ?>
                            <?php 
                            if( !empty( Helper::tfopt('rt-description') ) ){ ?>
                                <p><?php echo wp_kses_post( Helper::tfopt('rt-description') ) ?></p>
                            <?php } ?>
                        </div>

                        <div class="tf-slider-items-wrapper">
                            <?php
                            while ( $tours->have_posts() ) {
                                $tours->the_post();

                                if( is_array( $all_tour_ids ) && in_array(get_the_ID(), $all_tour_ids) ):

                                    $selected_post_id       = get_the_ID();
                                    $destinations           = get_the_terms( $selected_post_id, 'tour_destination' );
                                    $first_destination_name = $destinations[0]->name;
                                    $related_comments       = get_comments( array( 'post_id' => $selected_post_id ) );
                                    $meta                   = get_post_meta( $selected_post_id, 'tf_tours_opt', true );
                                    $pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                    $disable_adult          = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
                                    $disable_child          = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
                                    $tour_price             = new Tour_Price( $meta );
                                    ?>
                                    <div class="tf-slider-item" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url( $selected_post_id, 'full' )); ?>);">
                                        <div class="tf-slider-content">
                                            <div class="tf-slider-desc">
                                                <h3>
                                                    <a href="<?php the_permalink($selected_post_id) ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode(get_the_title( $selected_post_id )), 35 ) ); ?></a>
                                                    <span><?php echo esc_html($first_destination_name); ?></span>
                                                </h3>
                                            </div>
                                            <div class="tf-suggestion-rating">
                                                <div class="tf-suggestion-price">
                                        <span>
                                        <?php if ( $pricing_rule == 'group' ) {
                                            echo !empty( $tour_price->wc_sale_group ) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group);
                                        } else if ( $pricing_rule == 'person' ) {
                                            if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                            } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                            }
                                        }
                                        ?>
                                        </span>
                                                </div>
                                                <?php
                                                if ( $related_comments ) {
                                                    ?>
                                                    <div class="tf-slider-rating-star">
                                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $related_comments )); ?></span>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        wp_reset_postdata();
        ?>
    <?php } ?>
    <!-- Tours suggestion section End -->
    <?php do_action( 'tf_after_container' ); ?>
</div>