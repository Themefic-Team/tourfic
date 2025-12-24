<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;
if ( $comments && $disable_review_sec != 1 ) { ?>
<!-- Hotel reviews Srart -->
<div class="tf-reviews-wrapper" id="tf-hotel-reviews">         
    <h2 class="tf-section-title"><?php echo !empty( $meta['review-section-title'] ) ? esc_html($meta['review-section-title']) : ''; ?></h2>
    
    <div class="tf-review-items">
        <?php
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
            $c_avatar      = get_avatar( $comment, '64' );
            $c_author_name = $comment->comment_author;
            $c_date        = $comment->comment_date;
            $c_content     = $comment->comment_content;
            ?>
            <div class="tf-reviews-item">
                <div class="tf-reviews-item-header">
                    <div class="tf-reviews-item-header-left">
                        <div class="tf-reviews-avater"><?php echo wp_kses_post($c_avatar); ?></div>
                        <div class="tf-reviews-avater-right">
                            <span class="tf-reviews-name"><?php echo esc_html($c_author_name); ?></span>
                            <span class="tf-review-rating"><?php echo wp_kses_post($c_rating); ?></span>
                        </div>
                    </div>
                    <div class="tf-reviews-item-header-right">
                        <?php echo wp_kses_post(gmdate("d M, Y", strtotime($c_date))); ?>
                    </div>
                </div>
                <div class="tf-reviews-text">
                    <p><?php echo wp_kses_post($c_content); ?></p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<!--Content reviews end -->
<?php } ?>