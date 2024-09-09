<?php
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
global $current_user;
// Check if user is logged in
$is_user_logged_in = $current_user->exists();
$post_id           = $post->ID;
// Get settings value
$tf_ratings_for   = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
$tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;

$tf_comment_counts = get_comments( array(
    'post_id' => $post_id,
    'user_id' => $current_user->ID,
    'count'   => true,
) );

?>
<div class="tf-review-section">
<?php if ( $comments ) {
$tf_overall_rate = [];
TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
TF_Review::tf_get_review_fields( $fields );
?>
<h3 class="tf-section-title"><?php esc_html_e( "Review Scores", "tourfic" ); ?></h3>
<div class="tf-review-data-inner">

    <div class="tf-review-data">
        <div class="tf-review-data-average">
            <span class="avg-review tf-flex tf-flex-align-center tf-flex-gap-8">
                <?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
                <i class="fa fa-star"></i>
            </span>
            <div class="tf-review-all-info">
                <p><?php esc_html_e( "From ", "tourfic" ); ?><?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
            </div>
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
                        <div class="tf-progress-bar">
                            <span class="percent-progress" style="width: <?php echo esc_attr( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
                        </div>
                        <div class="tf-review-feature-label">
                            <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                            <p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
                        </div>
                    </div>
                <?php }
            } ?>

        </div>
    </div>
</div>
<?php } ?>
<?php
// Review moderation notice
echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' );
?>
<?php

if ( ! empty( $tf_ratings_for ) && empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) {
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