<?php
if ( $comments ) { ?>
<!-- Hotel reviews Srart -->
<div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">         
    <h2 class="tf-section-title"><?php _e("Guest reviews", "tourfic"); ?></h2> 
    <p><?php _e("Total", "tourfic"); ?> <?php tf_based_on_text( count( $comments ) ); ?></p>
    <div class="tf-reviews-slider">
        <?php
        foreach ( $comments as $comment ) {
        // Get rating details
        $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
        if ( $tf_overall_rate == false ) {
            $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
            $tf_overall_rate = tf_average_ratings( $tf_comment_meta );
        }
        $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
        $c_rating  = tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

        // Comment details
        $c_avatar      = get_avatar( $comment, '56' );
        $c_author_name = $comment->comment_author;
        $c_date        = $comment->comment_date;
        $c_content     = $comment->comment_content;
        ?>
        <div class="tf-reviews-item">
            <div class="tf-reviews-avater">
                <?php echo $c_avatar; ?>
            </div>
            <div class="tf-reviews-text">
                <h3><?php echo $c_rating; ?></h3>
                <span class="tf-reviews-meta"><?php echo $c_author_name; ?>, <?php echo date("F Y", strtotime($c_date)); ?></span>
                <p><?php echo tourfic_character_limit_callback($c_content, 180); ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<!--Content reviews end -->
<?php } ?>