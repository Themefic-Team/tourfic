<?php

use Tourfic\App\TF_Review;

if ( $comments && $disable_review_sec != 1 ) { ?>
<!-- apartment reviews Srart -->
<div class="tf-reviews-wrapper tf-section" id="tf-apartment-reviews">         
    <h2 class="tf-section-title"><?php echo !empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title']) : ''; ?></h2>
    <p><?php esc_html_e("Total", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
    <div class="tf-reviews-slider">
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
        $c_avatar      = get_avatar( $comment, '56' );
        $c_author_name = $comment->comment_author;
        $c_date        = $comment->comment_date;
        $c_content     = $comment->comment_content;
        ?>
        <div class="tf-reviews-item">
            <div class="tf-reviews-avater">
                <?php echo wp_kses_post($c_avatar); ?>
            </div>
            <div class="tf-reviews-text">
                <span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
                <span class="tf-reviews-meta"><?php echo esc_html( $c_author_name ); ?>, <?php echo esc_html( gmdate("F Y", strtotime($c_date)) ); ?></span>
                <p><?php echo wp_kses_post( \Tourfic\Classes\Helper::tourfic_character_limit_callback($c_content, 180) ); ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<!--Content reviews end -->
<?php } ?>