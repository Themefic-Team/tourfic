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
							\Tourfic\App\Templates\Components\Tour\Single\Tour_Contact_Information::render([
                                'wrapper_open' => '<div class="tf-mt-30">',
                                'wrapper_close' => '</div>',
                            ]);

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

        <?php \Tourfic\App\Templates\Components\Global\Single\Related_Post::render(['container' => 'yes']); ?>
    </div>
</div>