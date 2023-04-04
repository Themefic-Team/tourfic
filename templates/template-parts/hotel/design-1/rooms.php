<?php if ( $rooms ) :

//getting only selected features for rooms
$rm_features = [];
foreach ( $rooms as $key => $room ) {
    //merge for each room's selected features
    if(!empty($room['features'])){
        $rm_features = array_unique(array_merge( $rm_features, $room['features'])) ;
    }
}
?>

<div class="tf-rooms-sections tf-mrbottom-70">
    <h2 class="section-heading"><?php esc_html_e( 'Available Rooms', 'tourfic' ); ?></h2>
    <?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>
    <div class="tf-rooms">
        <!-- Loader Image -->
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
            </div>
        </div>

        <!-- Room Table -->
        <table class="tf-availability-table">
            <thead>
                <tr>
                    <th class="description" colspan="4"><?php _e( 'Room Details', 'tourfic' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <!-- Start Single Room -->
            <?php foreach ( $rooms as $key => $room ) {
                $enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
                if ( $enable == '1' ) {
                    $footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
                    $bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
                    $adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
                    $child_number = ! empty( $room['child'] ) ? $room['child'] : '0';
                    $total_person = $adult_number + $child_number;
                    $pricing_by   = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                    $avil_by_date = ! empty( $room['avil_by_date'] ) ? ! empty( $room['avil_by_date'] ) : false;

                    if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avil_by_date == true ) {
                        $repeat_by_date = ! empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                        if ( $pricing_by == '1' ) {
                            $prices = wp_list_pluck( $repeat_by_date, 'price' );
                        } else {
                            $prices = wp_list_pluck( $repeat_by_date, 'adult_price' );
                        }
                        if ( ! empty( $prices ) ) {
                            $range_price = [];
                            foreach ( $prices as $single ) {
                                if ( ! empty( $single ) ) {
                                    $range_price[] = $single;
                                }
                            }
                            if ( sizeof( $range_price ) > 1 ) {
                                $price = $prices ? ( min( $prices ) != max( $prices ) ? wc_format_price_range( min( $prices ), max( $prices ) ) : wc_price( min( $prices ) ) ) : wc_price( 0 );
                            } else {
                                $price = ! empty( $range_price[0] ) ? wc_price( $range_price[0] ) : wc_price( 0 );
                            }
                        }else{
                            if ( $pricing_by == '1' ) {
                                $price = wc_price( ! empty( $room['price'] ) ? $room['price'] : '0.0' );
                            } else {
                                $price = wc_price( ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0' );
                            }
                        }
                    } else {
                        if ( $pricing_by == '1' ) {
                            $price = wc_price( ! empty( $room['price'] ) ? $room['price'] : '0.0' );
                        } else {
                            $price = wc_price( ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0' );
                        }
                    }
                    ?>
                    <tr>
                        <td class="description">
                            <div class="tf-room-description-box">
                                <div class="tf-features-infos">
                                    <div class="tf-room-type">
                                        <div class="tf-room-title">
                                            <h3><?php echo esc_html( $room['title'] ); ?><h3>
                                        </div>
                                        <div class="bed-facilities"><?php _e( $room['description'] ); ?></div>
                                    </div>
                                    <ul>
                                        <?php if ( $footage ) { ?>
                                            <li><i class="fas fa-ruler-combined"></i> <?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></li>
                                        <?php } ?>
                                        <?php if ( $bed ) { ?>
                                            <li><i class="fas fa-bed"></i> <?php echo $bed; ?><?php _e( ' Number of Beds', 'tourfic' ); ?></li>
                                        <?php } ?>
                                        <?php 
                                        if( !empty($room['features']) ){
                                        foreach ( $room['features'] as $feature ) {
                                        $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                        if ( ! empty( $room_f_meta ) ) {
                                            $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                        }
                                        if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && !empty($room_f_meta['icon-fa']) ) {
                                            $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                        } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] )) {
                                            $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                        }

                                        $room_term = get_term( $feature ); ?>
                                        <li>
                                            <?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
                                            <?php echo $room_term->name; ?>
                                        </li>
                                        <?php } } ?>
                                    </ul>

                                    <?php
                                    $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                    if ( $tour_room_details_gall ) {
                                        $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                    }
                                    if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_room_details_gall ){
                                        ?>
                                        <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $key : '' ?>"
                                                data-hotel="<?php echo $post_id; ?>" style="text-decoration: underline;">
                                                <?php _e("Room Photos & Details","tourfic"); ?>
                                            </a>

                                        <div id="tour_room_details_qv" class="tf-reg-wrap">

                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            
                        </td>
                        <td class="pax">
                            <?php if ( $adult_number ) { ?>
                                <div class="tf-tooltip tf-d-b">
                                    <div class="room-detail-icon">
                            <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                                        class="fas fa-female"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                                    </div>
                                    <div class="tf-top">
                                        <?php _e( 'Number of Adults', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
                            <?php }
                            if ( $child_number ) { ?>
                                <div class="tf-tooltip tf-d-b">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                                    </div>
                                    <div class="tf-top">
                                        <?php _e( 'Number of Children', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>
                        <td class="pricing">
                            <div class="tf-price-column">
                                <?php
                                if ( $pricing_by == '1' ) {
                                    ?>
                                    <span class="tf-price"><?php echo $price; ?></span>
                                    <div class="price-per-night">
                                        <?php esc_html_e( 'per night', 'tourfic' ); ?>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <span class="tf-price"><?php echo $price; ?></span>
                                    <div class="price-per-night">
                                        <?php esc_html_e( 'per person/night', 'tourfic' ); ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td class="reserve tf-t-c">
                            <div class="tf-btn">
                                <button class="hotel-room-availability tf-bttn-normal bttn-secondary"
                                        type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<!-- End Room Section -->