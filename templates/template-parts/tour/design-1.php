<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Tour\Tour_Price;
use \Tourfic\Classes\Tour\Pricing;

$tourfic_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
$tourfic_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
$tourfic_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
$tourfic_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
$tourfic_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
$tourfic_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
if( 2==$tourfic_booking_type && !empty($tourfic_booking_url) ){
	$tourfic_external_search_info = array(
		'{adult}'    => !empty($adults) ? $adults : 1,
		'{child}'    => !empty($children) ? $children : 0,
		'{infant}'     => !empty($infant) ? $infant : 0,
		'{booking_date}' => !empty($tour_date) ? $tour_date : '',
	);
	if(!empty($tourfic_booking_attribute)){
		$tourfic_booking_query_url = str_replace(array_keys($tourfic_external_search_info), array_values($tourfic_external_search_info), $tourfic_booking_query_url);
		if( !empty($tourfic_booking_query_url) ){
			$tourfic_booking_url = $tourfic_booking_url.'/?'.$tourfic_booking_query_url;
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
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Address::render(); ?>
                        </div>
                        <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
							<?php \Tourfic\App\Templates\Components\Shared\Single\Wishlist::render(); ?>
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Share::render(); ?>
                        </div>
                    </div>
                </div>
                <!-- Single Tour Heading Section End -->

                <!-- Single Tour Body details start -->
                <div class="tf-single-details-wrapper tf-mt-30">
                    <div class="tf-single-details-inner tf-flex">
                        <div class="tf-tour-details-left">
							<?php
                            $tourfic_avail_prices = Pricing::instance( $post_id )->get_avail_price();
							if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout'] ) ) {
								foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout'] as $tourfic_section ) {
									if ( ! empty( $tourfic_section['status'] ) && $tourfic_section['status'] == "1" && ! empty( $tourfic_section['slug'] ) ) {
										include TF_TEMPLATE_PART_PATH . 'tour/design-1/' . $tourfic_section['slug'] . '.php';
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
							<?php
                            \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(['wrapper' => 'no']);
                            
							\Tourfic\App\Templates\Components\Tour\Single\Tour_Contact_Information::render([
                                'wrapper_open' => '<div class="tf-mt-30">',
                                'wrapper_close' => '</div>',
                            ]);

                            \Tourfic\App\Templates\Components\Shared\Single\Enquiry::render([
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
                                        <?php \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(['wrapper' => 'no']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Tour Body details End -->
                </div>
            </div>
        </div>

        <?php \Tourfic\App\Templates\Components\Shared\Single\Related_Post::render(['container' => 'yes']); ?>
    </div>
</div>
