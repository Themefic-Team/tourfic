<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;

class Reviews extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_reviews';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'type'           => 'tf_hotel',
					'number'         => '10',
					'count'          => '3',
					'speed'          => '2000',
					'arrows'         => 'false',
					'dots'           => 'true',
					'autoplay'       => 'false',
					'slidesToScroll' => 1,
					'infinite'       => 'false',
				),
				$atts
			)
		);
		$type == "hotel" ? $type = "tf_hotel" : $type == '';
		$type == "tour" ? $type = "tf_tours" : $type == '';
		$type == "apartment" ? $type = "tf_apartment" : $type == '';
		ob_start();
		?>
		<div class="tf-single-review tf-reviews-slider">

			<?php
			$args     = array(
				'post_type' => $type,
				'number'    => $number,
			);
			$comments = get_comments( $args );


			if ( $comments ) {
				foreach ( $comments as $comment ) {
					// Get rating details
					$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
					if ( $tf_overall_rate == false ) {
						$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
						$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
					}
					$base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
					$c_rating  = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

					// Comment details
					$c_avatar      = get_avatar( $comment, '56' );
					$c_author_name = $comment->comment_author;
					$c_date        = $comment->comment_date;
					$c_content     = $comment->comment_content;
					?>
					<div class="tf-single-details">
						<div class="tf-review-avatar"><?php echo wp_kses_post( $c_avatar ); ?></div>
						<div class="tf-review-details">
							<div class="tf-name"><?php echo esc_html( $c_author_name ); ?></div>
							<div class="tf-date"><?php echo esc_html( $c_date ); ?></div>
							<div class="tf-rating-stars">
								<?php echo wp_kses_post( $c_rating ); ?>
							</div>
							<div class="tf-description"><?php echo wp_kses_post( wp_trim_words( $c_content, 25 ) ); ?></div>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
		<script>
            /**
             * Init the reviews slider
             */
            jQuery('document').ready(function ($) {

                $(".tf-reviews-slider").each(function () {
                    var $this = $(this);
                    $this.slick({
                        dots: <?php echo wp_json_encode( filter_var( $dots, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                        arrows: <?php echo wp_json_encode( filter_var( $arrows, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                        slidesToShow: <?php echo (int) absint( $count ); ?>,
                        infinite: <?php echo wp_json_encode( filter_var( $infinite, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                        speed: <?php echo (int) absint( $speed ); ?>,
                        autoplay: <?php echo wp_json_encode( filter_var( $autoplay, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                        autoplaySpeed: <?php echo (int) absint( $speed ); ?>,
                        slidesToScroll: <?php echo (int) absint( $slidesToScroll ); ?>,
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 1,
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                })
            })
		</script>
		<?php
		return ob_get_clean();
	}
}