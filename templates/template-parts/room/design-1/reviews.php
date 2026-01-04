<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;

?>
<div class="tf-review-mobile-widget tf-sitebar-widgets">
    <?php
    if($disable_review_sec != 1) :
    global $current_user;
    // Check if user is logged in
    $is_user_logged_in = $current_user->exists();
    $post_id           = $post->ID;
    // Get settings value
    $tf_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
    $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
    if ( $comments ) {
        $tf_overall_rate        = [];
        TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
        TF_Review::tf_get_review_fields( $fields );
    ?>
    <h2 class="tf-section-title"><?php esc_html_e("Overall reviews", "tourfic"); ?></h2>
    <div class="tf-review-data-inner">
        <div class="tf-review-data">
            <div class="tf-review-data-average">
                <span class="avg-review"><span>
                    <?php echo esc_html(sprintf( '%.1f', $total_rating )); ?>
                </span>/ <?php echo wp_kses_post($tf_settings_base); ?></span>
            </div>
            <div class="tf-review-all-info">
                <p><?php esc_html_e("Excellent", "tourfic"); ?> <span><?php esc_html_e("Total ", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
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
                        <p class="feature-rating"> <?php echo wp_kses_post($value); ?></p>
                    </div>
                    <div class="tf-progress-bar">
                        <span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) )); ?>%"></span>
                    </div>
                </div>
                <?php } } ?>
            </div>
        </div>
    </div>
    <a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e("See all reviews", "tourfic"); ?></a>
    <?php } ?>

    <?php
    $tf_comment_counts = get_comments( array(
        'post_id' => $post_id,
        'user_id' => $current_user->ID,
        'count'   => true,
    ) );
    ?>
    <?php if( empty($tf_comment_counts) && $tf_comment_counts == 0 ) : ?>
        <button class="tf_btn tf_btn_rounded tf_btn_full tf_btn_large tf-review-open">
        <?php esc_html_e("Leave your review", "tourfic"); ?>
    </button>
    <?php endif; ?>
    <?php
    // Review moderation notice
    echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '');
    ?>
    <?php
    if ( ! empty( $tf_ratings_for ) ) {
        if ( $is_user_logged_in ) {
        if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
        ?>
    <div class="tf-review-form-wrapper" action="">
        <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
        <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
        <?php TF_Review::tf_review_form(); ?>
    </div>
    <?php
        }
    } else {
    if ( in_array( 'lo', $tf_ratings_for ) ) {
    ?>
    <div class="tf-review-form-wrapper" action="">
        <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
        <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
        <?php TF_Review::tf_review_form(); ?>
    </div>
    <?php } } } ?>
    <?php endif; ?>
</div>
<?php
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