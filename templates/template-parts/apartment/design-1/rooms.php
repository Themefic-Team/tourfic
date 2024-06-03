<?php 

use \Tourfic\Classes\Helper;

if ( isset( $meta['rooms'] ) && ! empty( Helper::tf_data_types( $meta['rooms'] ) ) ) : ?>
<div class="tf-apartment-rooms-section" id="tf-apartment-rooms">
    <div class="tf-apartment-room-details">
    <h4><?php echo esc_html( $meta['room_details_title'] ) ?></h4>
    <div class="tf-apartment-room-slider">
    <?php foreach ( Helper::tf_data_types( $meta['rooms'] ) as $key => $room ) : ?>
        <div class="tf-apartment-room-item">
            <div class="tf-apartment-room-item-thumb">
                <a href="#" class="tf-apt-room-qv-desgin-1" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                    <img src="<?php echo !empty($room['thumbnail']) ? esc_url( $room['thumbnail'] ) : esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" ?>" alt="room-thumbnail">
                </a>
            </div>
            <div class="tf-apartment-room-item-content">
                <a href="#" class="tf-apt-room-qv-desgin-1" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                    <span><?php echo esc_html( $room['title'] ) ?></span>
                </a>
                <?php echo ! empty( $room['subtitle'] ) ? '<p>' . esc_html( $room['subtitle'] ) . '</p>' : ''; ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <!-- Loader Image -->
    <div id="tour_room_details_loader">
        <div id="tour-room-details-loader-img">
            <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
        </div>
    </div>
    </div>
</div>
<?php endif ?>