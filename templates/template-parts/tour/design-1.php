<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
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
<div class="tf-single-template__one">
    <div class="tf-tour-single">
        <div class="tf-container">
            <div class="tf-container-inner">
                <!-- Single Tour Heading Section start -->
                <div class="tf-section tf-single-head">
                    <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                        <div class="tf-head-title">
                            <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
                            <?php \Tourfic\App\Templates\Components\Global\Single\Address::render(); ?>
                        </div>
                        <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
							<?php \Tourfic\App\Templates\Components\Global\Single\Wishlist::render(); ?>
                            <?php \Tourfic\App\Templates\Components\Global\Single\Share::render(); ?>
                        </div>
                    </div>
                </div>
                <!-- Single Tour Heading Section End -->

                <!-- Single Tour Body details start -->
                <div class="tf-single-details-wrapper tf-mt-30">
                    <div class="tf-single-details-inner tf-flex">
                        <div class="tf-tour-details-left">
							<?php
                            $avail_prices = Pricing::instance( $post_id )->get_avail_price();
							if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout'] ) ) {
								foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout'] as $section ) {
									if ( ! empty( $section['status'] ) && $section['status'] == "1" && ! empty( $section['slug'] ) ) {
										include TF_TEMPLATE_PART_PATH . 'tour/design-1/' . $section['slug'] . '.php';
									}
								}
							} else {
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/gallery.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/price.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/description.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/information.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/highlights.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/include-exclude.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/itinerary.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/map.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/faq.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/trams-condition.php';
								include TF_TEMPLATE_PART_PATH . 'tour/design-1/review.php';
							}
							?>
                        </div>

                        <!-- SIdebar Tour single -->
                        <div class="tf-tour-details-right">
                            <div class="tf-tour-booking-box tf-box">
								<?php
								$hide_price = !empty( Helper::tfopt( 't-hide-start-price' ) ) ? Helper::tfopt( 't-hide-start-price' ) : '';
								if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) :
                                    if ( isset( $hide_price ) && $hide_price !== '1' ) : ?>
                                        <!-- Tourfic Pricing Head -->
                                        <div class="tf-booking-form-data">
                                            <div class="tf-booking-block">
                                                <div class="tf-booking-price">
                                                <?php
                                                $tour_price = [];
                                                $tf_pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                                $tour_single_price_settings = !empty(Helper::tfopt('tour_archive_price_minimum_settings')) ? Helper::tfopt('tour_archive_price_minimum_settings') : 'adult';
                                                
                                                $min_sale_price = null;
                                                if( $tf_pricing_rule  && $tf_pricing_rule == 'person' ){
                                                    if($tour_single_price_settings == 'all') {
                                                        if(!empty($avail_prices['adult_price']) && !$disable_adult){
                                                            $tour_price[] = $avail_prices['adult_price'];
                                                            $min_sale_price = $avail_prices['sale_adult_price'];
                                                        }
                                                        if(!empty($avail_prices['child_price']) && !$disable_child){
                                                            $tour_price[] = $avail_prices['child_price'];
                                                            if( $avail_prices['sale_child_price'] < $min_sale_price ){
                                                                $min_sale_price = $avail_prices['sale_child_price'];
                                                            }
                                                        }
                                                    }
                                                    if($tour_single_price_settings == "adult") {
                                                        if(!empty($avail_prices['adult_price']) && !$disable_adult){
                                                            $tour_price[] = $avail_prices['adult_price'];
                                                            $min_sale_price = $avail_prices['sale_adult_price'];
                                                        }
                                                    }
                                                    if($tour_single_price_settings == "child") {
                                                        if(!empty($avail_prices['child_price']) && !$disable_adult){
                                                            $tour_price[] = $avail_prices['child_price'];
                                                            $min_sale_price = $avail_prices['sale_child_price'];
                                                        }
                                                    }
                                                }
                                                if( $tf_pricing_rule  && $tf_pricing_rule == 'group' ){
                                                    if(!empty($avail_prices['group_price'])){
                                                        $tour_price[] = $avail_prices['group_price'];
                                                        $min_sale_price = $avail_prices['sale_group_price'];
                                                    }
                                                }
                                                if( $tf_pricing_rule  && $tf_pricing_rule == 'package' ){
                                                    if($tour_single_price_settings == 'all') {
                                                        if(!empty($avail_prices['adult_price']) && !$disable_adult){
                                                            $tour_price[] = $avail_prices['adult_price'];
                                                            $min_sale_price = $avail_prices['sale_adult_price'];
                                                        }
                                                        if(!empty($avail_prices['child_price']) && !$disable_child){
                                                            $tour_price[] = $avail_prices['child_price'];
                                                            if( $avail_prices['sale_child_price'] < $min_sale_price ){
                                                                $min_sale_price = $avail_prices['sale_child_price'];
                                                            }
                                                        }
                                                    }
                                                    if($tour_single_price_settings == "adult") {
                                                        if(!empty($avail_prices['adult_price']) && !$disable_adult){
                                                            $tour_price[] = $avail_prices['adult_price'];
                                                            $min_sale_price = $avail_prices['sale_adult_price'];
                                                        }
                                                    }
                                                    if($tour_single_price_settings == "child") {
                                                        if(!empty($avail_prices['child_price']) && !$disable_adult){
                                                            $tour_price[] = $avail_prices['child_price'];
                                                            $min_sale_price = $avail_prices['sale_child_price'];
                                                        }
                                                    }
                                                    if(!empty($avail_prices['group_price'])){
                                                        $tour_price[] = $avail_prices['group_price'];
                                                        if( $avail_prices['sale_group_price'] < $min_sale_price ){
                                                            $min_sale_price = $avail_prices['sale_group_price'];
                                                        }
                                                    }
                                                }
                                                ?>
                                                    <p> <span><?php esc_html_e("From","tourfic"); ?></span>

                                                    <?php
                                                    //get the lowest price from all available room price
                                                    $tf_tour_min_price      = !empty($tour_price) ? min( $tour_price ) : 0;
                                                    
                                                    if ( ! empty( $min_sale_price ) ) {
                                                        echo wp_kses_post(wp_strip_all_tags(wc_price($tf_tour_min_price))). " " . "<span><del>" . wp_kses_post(wp_strip_all_tags(wc_price( $min_sale_price ))) . "</del></span>";
                                                    } else {
                                                        echo wp_kses_post(wp_strip_all_tags(wc_price($tf_tour_min_price)));
                                                    }
                                                    ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif;
                                endif; ?>
                                <!-- Tourfic Booking form -->
                                <div class="tf-booking-form">
                                    <div class="tf-booking-form-inner tf-mt-24 <?php echo $tf_booking_type == 2 && $tf_hide_price !== '1' ? 'tf-mt-24' : '' ?>">
                                        <h3><?php echo ! empty( $meta['booking-section-title'] ) ? esc_html( $meta['booking-section-title'] ) : ''; ?></h3>
	                                    <?php
                                        if( ($tf_booking_type == 2 && $tf_hide_booking_form !== '1') || $tf_booking_type == 1 || $tf_booking_type == 3) {
		                                    echo wp_kses(Tour::tf_single_tour_booking_form( $post->ID ), Helper::tf_custom_wp_kses_allow_tags());
                                        }
                                        ?>
	                                    <?php if ($tf_booking_type == 2 && $tf_hide_booking_form == 1):?>
                                            <a href="<?php echo esc_url($tf_booking_url) ?>" target="_blank" class="tf_btn tf_btn_large" style="margin-top: 10px;"><?php echo esc_html($tf_tour_single_book_now_text); ?></a>
	                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
							<?php
							if ( $email || $phone || $fax || $website ) {
								?>
                                <div class="tf-tour-booking-advantages tf-box tf-mt-30">
                                    <div class="tf-head-title">
                                        <h3><?php echo ! empty( $meta['contact-info-section-title'] ) ? esc_html( $meta['contact-info-section-title'] ) : ''; ?></h3>
                                    </div>
                                    <div class="tf-booking-advantage-items">
                                        <ul class="tf-list">
											<?php
											if ( ! empty( $phone ) ) { ?>
                                                <li><i class="fa-solid fa-headphones"></i> <a href="tel:<?php echo esc_html( $phone ) ?>"><?php echo esc_html( $phone ) ?></a></li>
											<?php } ?>
											<?php
											if ( ! empty( $email ) ) { ?>
                                                <li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?php echo esc_html( $email ) ?>"><?php echo esc_html( $email ) ?></a></li>
											<?php } ?>
											<?php
											if ( ! empty( $website ) ) { ?>
                                                <li><i class="fa-solid fa-link"></i> <a target="_blank" href="<?php echo esc_html( $website ) ?>"><?php echo esc_html( $website ) ?></a></li>
											<?php } ?>
											<?php
											if ( ! empty( $fax ) ) { ?>
                                                <li><i class="fa-solid fa-fax"></i> <a href="tel:<?php echo esc_html( $fax ) ?>"><?php echo esc_html( $fax ) ?></a></li>
											<?php } ?>
                                        </ul>
                                    </div>
                                </div>
							<?php } ?>
                            
							<?php
                            \Tourfic\App\Templates\Components\Global\Single\Enquiry::render([
                                'icon_type' => 'simple',
                                'wrapper_open' => '<div class="tf-tour-booking-advantages tf-box tf-mt-30">',
                                'wrapper_close' => '</div>',
                            ]);
							?>
                        </div>

                        <!-- Responsive booking Modal -->
                        <div class="tf-modal" id="tf-tour-booking-modal">
                            <div class="tf-modal-dialog">
                                <div class="tf-modal-content">
                                    <div class="tf-modal-header">
                                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                                    </div>
                                    <div class="tf-modal-body">
                                        <div class="tf-tour-booking-box tf-box">
                                            <!-- Tourfic Pricing Head -->
                                            <div class="tf-booking-form-data">
                                                <div class="tf-booking-block">
                                                    <div class="tf-booking-price">
                                                        <p><span><?php esc_html_e( "From", "tourfic" ); ?></span>
                                                        <?php
                                                            //get the lowest price from all available room price
                                                            $tour_price = isset($tour_price) && is_array($tour_price) ? $tour_price : [];
                                                            $tf_tour_min_price      = !empty($tour_price) ? min( $tour_price ) : 0;
                                                            $lowest_price = wp_strip_all_tags(wc_price( $tf_tour_min_price ));
                                                            
                                                            if ( ! empty( $min_sale_price ) ) {
                                                                echo wp_kses_post($lowest_price). " " . "<span><del>" . wp_kses_post(wp_strip_all_tags(wc_price( $min_sale_price ))) . "</del></span>";
                                                            } else {
                                                                echo wp_kses_post($lowest_price);
                                                            }
                                                        ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tourfic Booking form -->
                                            <div class="tf-booking-form">
                                                <div class="tf-booking-form-inner tf-mt-24">
                                                    <h3><?php echo ! empty( $meta['booking-section-title'] ) ? esc_html( $meta['booking-section-title'] ) : ''; ?></h3>
													<?php echo wp_kses(Tour::tf_single_tour_booking_form( $post->ID ), Helper::tf_custom_wp_kses_allow_tags()); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Tour Body details End -->
                </div>
            </div>
        </div>

		<?php
		if ( ! $disable_related_tour == '1' ) {
			$related_tour_type = Helper::tfopt( 'rt_display' );
			$args              = array(
				'post_type'      => 'tf_tours',
				'post_status'    => 'publish',
				'posts_per_page' => 8,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'tax_query'      => array( // WPCS: slow query ok.
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
                } else {
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
                    <!-- Tourfic upcomming tours tours -->
                    <div class="upcomming-tours">
                        <div class="tf-container">
                            <div class="tf-container-inner">
                                <div class="section-title">
                                    <h2 class="tf-title"><?php echo ! empty( Helper::tfopt( 'rt-title' ) ) ? esc_html( Helper::tfopt( 'rt-title' )) : ''; ?></h2>
                                    <?php
                                    if ( ! empty( Helper::tfopt( 'rt-description' ) ) ) { ?>
                                        <p><?php echo wp_kses_post(Helper::tfopt( 'rt-description')) ?></p>
                                    <?php } ?>
                                </div>
                                <div class="tf-slider-items-wrapper tf-slick-slider tf-upcomming-tours-list-outter tf-mt-40 tf-flex tf-flex-gap-24">
                                    <?php
                                    while ( $tours->have_posts() ) {
                                        $tours->the_post();
                                        if( is_array($all_tour_ids) && in_array(get_the_ID(), $all_tour_ids) ):
                                            $selected_design_post_id = get_the_ID();
                                            $destinations           = get_the_terms( $selected_design_post_id, 'tour_destination' );

                                            $first_destination_name = $destinations[0]->name;
                                            $related_comments       = get_comments( array( 'post_id' => $selected_design_post_id ) );
                                            $meta                   = get_post_meta( $selected_design_post_id, 'tf_tours_opt', true );
                                            $pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                            $disable_adult          = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
                                            $disable_child          = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
                                            $tour_price             = new Tour_Price( $meta );
                                            ?>
                                            <div class="tf-slider-item tf-post-box-lists">
                                                <div class="tf-post-single-box">
                                                    <div class="tf-image-data">
                                                        <img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) ? esc_url(get_the_post_thumbnail_url( $selected_design_post_id, 'full' )) : esc_url(TF_ASSETS_APP_URL . 'images/feature-default.jpg'); ?>"
                                                            alt="">
                                                        <div class="tf-meta-data-price">
                                                            <?php esc_html_e( "From", "tourfic" ); ?>
                                                            <span>
                                                <?php if ( $pricing_rule == 'group' ) {
                                                    echo !empty( $tour_price->wc_sale_group ) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group);
                                                } else if ( $pricing_rule == 'person' ) {
                                                    if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                        echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                                    } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                        echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                                    }
                                                } else if ( $pricing_rule == 'package' ) {
                                                    if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                        echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                                    } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                        echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                                    }
                                                }
                                                ?>
                                                </span>
                                                        </div>
                                                    </div>
                                                    <div class="tf-meta-info tf-mt-30">
                                                        <div class="tf-meta-location">
                                                            <i class="fa-solid fa-location-dot"></i> <?php echo esc_html($first_destination_name); ?>
                                                        </div>
                                                        <div class="tf-meta-title">
                                                            <h2><a href="<?php the_permalink($selected_design_post_id) ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode(get_the_title( $selected_design_post_id )), 35 ) ); ?></a></h2>
                                                        </div>
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
    </div>
</div>