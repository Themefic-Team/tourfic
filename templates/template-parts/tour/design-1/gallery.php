<!-- Tour Gallery Section -->
<div class="tf-hero-gallery">
<div class="tf-gallery-featured">
    <img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : TF_ASSETS_APP_URL.'/images/feature-default.jpg'; ?>" alt="<?php _e( 'Tour Image', 'tourfic' ); ?>">
    <div class="featured-meta-gallery-videos">
        <div class="featured-column tf-gallery-box">
            <?php 
            if ( ! empty( $gallery_ids ) ) {
            foreach ( $gallery_ids as $key => $gallery_item_id ) {
            $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
            if ( $key === array_key_first( $gallery_ids ) ) {
            ?>
            <a id="featured-gallery" href="#" data-fancybox="tour-gallery" class="tf-tour-gallery" data-src="<?php echo esc_url($image_url); ?>">
                <i class="fa-solid fa-camera-retro"></i><?php echo __("Gallery","tourfic"); ?>
            </a>
            <?php 
                }}}
            ?>

        </div>
        <?php
        $tour_video = ! empty( $meta['tour_video'] ) ? $meta['tour_video'] : '';
        if ( !empty($tour_video) ) { ?>
        <div class="featured-column tf-video-box">
            <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video" href="<?php echo esc_url($tour_video); ?>">
                <i class="fa-solid fa-video"></i> <?php echo __("Video","tourfic"); ?>
            </a>
        </div>
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
    <a class="<?php echo $gallery_count==5 ? esc_attr( 'tf-gallery-more' ) : ''; ?> " href="<?php echo esc_url($image_url); ?>" data-fancybox="tour-gallery"><img src="<?php echo esc_url($image_url); ?>"></a>
    <?php $gallery_count++; } } ?>
</div>
</div>