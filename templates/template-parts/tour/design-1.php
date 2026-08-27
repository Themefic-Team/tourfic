<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Tour\Tour_Price;
use \Tourfic\Classes\Tour\Pricing;

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
                            $tourfic_avail_prices = Pricing::instance( $tourfic_post_id )->get_avail_price();
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
