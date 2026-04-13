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
					<?php \Tourfic\App\Templates\Components\Global\Single\Wishlist::render(['design' => 'design-2']); ?>
					<?php \Tourfic\App\Templates\Components\Global\Single\Share::render(['share_style' => 'style2']); ?>
                </div>
                <div class="tf-hero-bottom-area">
                    <div class="tf-head-title">
                        <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
						<?php \Tourfic\App\Templates\Components\Global\Single\Address::render(['design' => 'design-2']); ?>
                    </div>
                    <div class="tf-hero-gallery-videos">
						<?php \Tourfic\App\Templates\Components\Global\Single\Video_Button::render(['design' => 'design-2'], '', false); ?>
                    	<?php \Tourfic\App\Templates\Components\Global\Single\Gallery_Button::render(['style' => 'style2']); ?>
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
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
                        <div class="tf-search-date-wrapper tf-single-widgets">
                            <h3 class="tf-section-title"><?php echo ! empty( $meta["booking-section-title"] ) ? esc_html( $meta["booking-section-title"] ) : ''; ?></h3>
							<?php echo wp_kses( Tour::tf_single_tour_booking_form( $post->ID ), Helper::tf_custom_wp_kses_allow_tags() ); ?>
                        </div>
					<?php endif; ?>

                    <!-- Tour External Booking From -->
					<?php if ( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ): ?>
                        <div class="tour-external-booking-form tf-single-widgets">
                            <h2 class="tf-section-title"><?php esc_html_e( "Book This Tour", "tourfic" ); ?></h2>
                            <div class="tf-btn-wrap">
                                <a href="<?php echo esc_url( $tf_booking_url ) ?>" target="_blank" class="tf_btn tf_btn_full tf_btn_sharp tf-tour-external-booking-button"
                                   style="margin-top: 10px;"><?php echo esc_html( $tf_tour_single_book_now_text ); ?></a>
                            </div>
                        </div>
					<?php endif; ?>

					<?php
					\Tourfic\App\Templates\Components\Tour\Single\Tour_Contact_Information::render([
						'icon_style' => 'style2',
						'wrapper_open' => '<div class="tf-single-widgets">',
						'wrapper_close' => '</div>',
					]);
					?>
					
					<?php if ( $disable_review_sec != 1 ): ?>
                        <div class="tf-reviews tf-single-widgets">
							<?php
							global $current_user;
							// Check if user is logged in
							$is_user_logged_in = $current_user->exists();
							$post_id           = $post->ID;
							// Get settings value
							$tf_ratings_for   = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
							$tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
							if ( $comments ) {
								$tf_overall_rate = [];
								TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
								TF_Review::tf_get_review_fields( $fields );
								?>
                                <h3 class="tf-section-title"><?php esc_html_e( "Overall reviews", "tourfic" ); ?></h3>
                                <div class="tf-review-data-inner">
                                    <div class="tf-review-data">
                                        <div class="tf-review-data-average">
                                        <span class="avg-review"><span>
                                            <?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
                                        </span>/ <?php echo wp_kses_post( $tf_settings_base ); ?></span>
                                        </div>
                                        <div class="tf-review-all-info">
                                            <p><?php esc_html_e( "Excellent", "tourfic" ); ?> <span><?php esc_html_e( "Total ", "tourfic" ); ?><?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="tf-review-data-features">
                                        <div class="tf-percent-progress">
											<?php
											if ( $tf_overall_rate ) {
												foreach ( $tf_overall_rate as $key => $value ) {
													if ( empty( $value ) || ! in_array( $key, $fields ) ) {
														continue;
													}
													$value = TF_Review::tf_average_ratings( $value );
													?>
                                                    <div class="tf-progress-item">
                                                        <div class="tf-review-feature-label">
                                                            <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                                            <p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
                                                        </div>
                                                        <div class="tf-progress-bar">
                                                            <span class="percent-progress" style="width: <?php echo esc_attr( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
                                                        </div>
                                                    </div>
												<?php }
											} ?>

                                        </div>
                                    </div>
                                </div>
                                <a class="tf-all-reviews" href="#tf-tour-reviews"><?php esc_html_e( "See all reviews", "tourfic" ); ?></a>
							<?php } ?>
							<?php
							$tf_comment_counts = get_comments( array(
								'post_id' => $post_id,
								'user_id' => $current_user->ID,
								'count'   => true,
							) );
							?>
							<?php if ( empty( $tf_comment_counts ) && $tf_comment_counts == 0 ): ?>
                                <button class="tf_btn tf_btn_secondary tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
									<?php esc_html_e( "Leave your review", "tourfic" ); ?>
                                </button>
							<?php endif; ?>
							<?php
							// Review moderation notice
							echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' );
							?>
							<?php
							if ( ! empty( $tf_ratings_for ) ) {
								if ( $is_user_logged_in ) {
									if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
										?>
                                        <div class="tf-review-form-wrapper" action="">
                                            <h3><?php esc_html_e( "Leave your review", "tourfic" ); ?></h3>
                                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
											<?php TF_Review::tf_review_form(); ?>
                                        </div>
										<?php
									}
								} else {
									if ( in_array( 'lo', $tf_ratings_for ) ) {
										?>
                                        <div class="tf-review-form-wrapper" action="">
                                            <h3><?php esc_html_e( "Leave your review", "tourfic" ); ?></h3>
                                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
											<?php TF_Review::tf_review_form(); ?>
                                        </div>
									<?php }
								}
							} ?>

                        </div>
					<?php endif; ?>
                    
					<?php
					\Tourfic\App\Templates\Components\Global\Single\Enquiry::render([
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
	\Tourfic\App\Templates\Components\Global\Single\Related_Post::render([
		'related_post_style' => 'style2', 
		'container' => 'yes'
	]); 
	?>
</div>