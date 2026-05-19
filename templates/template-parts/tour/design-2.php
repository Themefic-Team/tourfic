<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Tour\Tour_Price;

$tf_booking_type = '1';
$tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = '';
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
	$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
	$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
	$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
}
if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
	$external_search_info = array(
		'{adult}'        => ! empty( $adults ) ? $adults : 1,
		'{child}'        => ! empty( $children ) ? $children : 0,
		'{infant}'       => ! empty( $infant ) ? $infant : 0,
		'{booking_date}' => ! empty( $tour_date ) ? $tour_date : '',
	);
	if ( ! empty( $tf_booking_attribute ) ) {
		$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
		if ( ! empty( $tf_booking_query_url ) ) {
			$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
		}
	}
}
?>

<div class="tf-single-template__two">

    <!--Hero section start -->
    <div class="tf-hero-section-wrap"
         style="<?php echo ! empty( get_the_post_thumbnail_url() ) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url(' . esc_url( get_the_post_thumbnail_url() ) . '), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content">
                <div class="tf-wish-and-share">
					<?php \Tourfic\App\Templates\Components\Shared\Single\Wishlist::render(['design' => 'design-2']); ?>
					<?php \Tourfic\App\Templates\Components\Shared\Single\Share::render(['share_style' => 'style2']); ?>
                </div>
                <div class="tf-hero-bottom-area">
                    <div class="tf-head-title">
                        <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
						<?php \Tourfic\App\Templates\Components\Shared\Single\Address::render(['design' => 'design-2']); ?>
                    </div>
                    <div class="tf-hero-gallery-videos">
						<?php \Tourfic\App\Templates\Components\Shared\Single\Video_Button::render(['design' => 'design-2'], '', false); ?>
                    	<?php \Tourfic\App\Templates\Components\Shared\Single\Gallery_Button::render(['style' => 'style2']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Hero section End -->


    <!--Content section end -->
    <div class="tf-content-wrapper tf-single-pb-56">

        <div class="tf-container">

            <!-- Hotel details Srart -->
            <div class="tf-details" id="tf-tour-overview">
                <div class="tf-details-left">
					<?php
					if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout-part-1'] ) ) {
						foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout-part-1'] as $section ) {
							if ( ! empty( $section['status'] ) && $section['status'] == "1" && ! empty( $section['slug'] ) ) {
								include TF_TEMPLATE_PART_PATH . 'tour/design-2/' . $section['slug'] . '.php';
							}
						}
					} else {
						include TF_TEMPLATE_PART_PATH . 'tour/design-2/description.php';
						include TF_TEMPLATE_PART_PATH . 'tour/design-2/information.php';
						include TF_TEMPLATE_PART_PATH . 'tour/design-2/highlights.php';
						include TF_TEMPLATE_PART_PATH . 'tour/design-2/include-exclude.php';
						include TF_TEMPLATE_PART_PATH . 'tour/design-2/itinerary.php';
					}
					?>
                </div>
                <div class="tf-details-right tf-sitebar-widgets">
					<?php 
					\Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(['booking_form_style' => 'style2']);
					
					\Tourfic\App\Templates\Components\Tour\Single\Tour_Contact_Information::render([
						'icon_style' => 'style2',
						'wrapper_open' => '<div class="tf-single-widgets">',
						'wrapper_close' => '</div>',
					]);

					\Tourfic\App\Templates\Components\Shared\Single\Review::render([
						'review_style' => 'design-2',
						'show_review_states' => 'yes',
						'show_reviews' => 'no',
						'show_review_form' => 'yes',
					]);
					
					\Tourfic\App\Templates\Components\Shared\Single\Enquiry::render([
						'icon_type' => 'simple',
						'wrapper_class' => 'tf-send-inquiry tf-single-widgets',
						'button_class' => 'tf_btn_large tf_btn_sharp',
					]);
					?>
                </div>
            </div>
            <!-- Hotel details End -->
			<?php
			if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout-part-2'] ) ) {
				foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour-layout-part-2'] as $section ) {
					if ( ! empty( $section['status'] ) && $section['status'] == "1" && ! empty( $section['slug'] ) ) {
						include TF_TEMPLATE_PART_PATH . 'tour/design-2/' . $section['slug'] . '.php';
					}
				}
			} else {
				include TF_TEMPLATE_PART_PATH . 'tour/design-2/faq.php';
				include TF_TEMPLATE_PART_PATH . 'tour/design-2/review.php';
				include TF_TEMPLATE_PART_PATH . 'tour/design-2/trams-condition.php';
			}
			?>

            <!-- Tour Gallery PopUp Starts -->
            <div class="tf-popup-wrapper tf-hotel-popup">
                <div class="tf-popup-inner">
                    <div class="tf-popup-body">
						<?php
						if ( ! empty( $gallery_ids ) ) {
							foreach ( $gallery_ids as $key => $gallery_item_id ) {
								$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
								?>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="" class="tf-popup-image">
							<?php }
						} ?>
                    </div>
                    <div class="tf-popup-close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>
            </div>
            <!-- Tour Gallery PopUp end -->

        </div>
    </div>
    <!--Content section end -->

	<?php 
	\Tourfic\App\Templates\Components\Shared\Single\Related_Post::render([
		'related_post_style' => 'style2', 
		'container' => 'yes'
	]); 
	?>
</div>