<?php use \Tourfic\App\TF_Review;  ?>

<!-- Tour Gallery Section -->
<div class="tf-hero-gallery tf-mb-30 tf-template-section">
<div class="tf-gallery-featured">
    <img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>" alt="<?php esc_html_e( 'Tour Image', 'tourfic' ); ?>">
    <div class="featured-meta-gallery-videos">
        <div class="featured-column tf-gallery-box">
            <?php 
            if ( ! empty( $gallery_ids ) ) {
            ?>
            <a id="featured-gallery" href="#" class="tf-tour-gallery">
                <i class="fa-solid fa-camera-retro"></i><?php echo esc_html__("Gallery","tourfic"); ?>
            </a>
            <?php 
            }
            ?>

        </div>
        <?php
        $tour_video = ! empty( $meta['tour_video'] ) ? $meta['tour_video'] : '';
        if ( !empty($tour_video) ) { ?>
        <div class="featured-column tf-video-box">
            <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video" href="<?php echo esc_url($tour_video); ?>">
                <i class="fa-solid fa-video"></i> <?php echo esc_html__("Video","tourfic"); ?>
            </a>
        </div>
        <?php } ?>
    </div>
    <div class="tf-single-review-box">
    <?php if ( ! $disable_review_sec == '1' ) { ?>
        <?php
        if($comments){ ?>
        <a href="#tf-review" class="tf-single-rating">
            <span><?php echo wp_kses_post(TF_Review::tf_total_avg_rating( $comments )); ?></span> (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
        </a>
        <?php }else{ ?>
            <a href="#tf-review" class="tf-single-rating">
                <span><?php esc_html_e( "0.0", "tourfic" ) ?></span> (<?php esc_html_e( "0 review", "tourfic" ) ?>)
            </a>
        <?php } ?>
    <?php } ?>
    </div>
</div>
<div class="tf-gallery">
    <?php 
    $gallery_count = 1;
        if ( ! empty( $gallery_ids ) ) {
        foreach ( $gallery_ids as $key => $gallery_item_id ) {
        $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
    ?>
    <a class="<?php echo $gallery_count==5 ? esc_attr( 'tf-gallery-more' ) : ''; ?> " href="<?php echo esc_url($image_url); ?>" id="tour-gallery" data-fancybox="tour-gallery"><img src="<?php echo esc_url($image_url); ?>"></a>
    <?php $gallery_count++; } } ?>
</div>
</div>