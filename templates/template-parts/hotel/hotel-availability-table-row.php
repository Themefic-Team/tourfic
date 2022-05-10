<tr>
    <td class="description">
        <div class="tf-room-type">
            <div class="tf-room-title"><?php echo esc_html( $room['title'] ); ?></div>
            <div class="bed-facilities"><?php echo $room['description']; ?></div>
        </div>

        <div class="tf-room-title">
            <?php esc_html_e( 'Key Features', 'tourfic' );?>
        </div>

        <?php if ( $footage ) {?>
            <div class="tf-tooltip tf-d-ib">
                <div class="room-detail-icon">
                    <span class="room-icon-wrap"><i class="fas fa-ruler-combined"></i></span>
                    <span class="icon-text tf-d-b"><?php echo $footage; ?> sft</span>
                </div>
                <div class="tf-top">
                    <?php _e( 'Room Footage', 'tourfic' );?>
                    <i class="tool-i"></i>
                </div>
            </div>
        <?php }
        if ( $bed ) {?>
            <div class="tf-tooltip tf-d-ib">
                <div class="room-detail-icon">
                    <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                    <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                </div>
                <div class="tf-top">
                    <?php _e( 'No. Beds', 'tourfic' );?>
                    <i class="tool-i"></i>
                </div>
            </div>
        <?php }?>

        <div class="room-features">
            <div class="tf-room-title"><?php esc_html_e( 'Amenities', 'tourfic' );?></div>
            <ul class="room-feature-list">

                <?php foreach ( $room['features'] as $feature ) {

                        $room_f_meta = get_term_meta( $feature, 'hotel_feature', true );

                        if ( $room_f_meta['icon-type'] == 'fa' ) {
                            $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                        } elseif ( $room_f_meta['icon-type'] == 'c' ) {
                            $room_feature_icon = '<img src="' . $room_f_meta['icon-c']["url"] . '" style="min-width: ' . $room_f_meta['dimention']["width"] . 'px; height: ' . $room_f_meta['dimention']["width"] . 'px;" />';
                        }

                    $room_term = get_term( $feature );?>
                    <li class="tf-tooltip">
                        <?php echo $room_feature_icon; ?>
                        <div class="tf-top">
                            <?php echo $room_term->name; ?>
                            <i class="tool-i"></i>
                        </div>
                    </li>
                <?php }?>
            </ul>
        </div>
    </td>
    <td class="pax">

        <?php if ( $adult_number ) {?>
            <div class="tf-tooltip tf-d-b">
                <div class="room-detail-icon">
                    <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                    <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                </div>
                <div class="tf-top">
                    <?php _e( 'No. Adults', 'tourfic' );?>
                    <i class="tool-i"></i>
                </div>
            </div>
        <?php }
        if ( $child_number ) {?>
            <div class="tf-tooltip tf-d-b">
                <div class="room-detail-icon">
                    <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                    <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                </div>
                <div class="tf-top">
                    <?php _e( 'No. Children', 'tourfic' );?>
                    <i class="tool-i"></i>
                </div>
            </div>
        <?php }?>
    </td>
    <td class="pricing">
        <div class="tf-price-column">
            <span class="tf-price"><?php echo wc_price( $price ); ?></span>
            <?php if ( $pricing_by == '1' ) { ?>
                <div class="price-per-night"><?php $days > 0 ? esc_html_e( 'for '.$days .' nights', 'tourfic' ) :  esc_html_e( 'per night', 'tourfic' );?></div>
            <?php } else {?>
                <div class="price-per-night"><?php $days > 0 ? esc_html_e( 'for '.$days .' nights', 'tourfic' ) : esc_html_e( 'per person/night', 'tourfic' );?></div>
            <?php }?>
        </div>
    </td>
    <td class="reserve">
        <form class="tf-room">
            <?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' );?>

            <div class="room-selection-wrap">
                <select name="hotel_room_selected" id="hotel-room-selected">
                    <?php
                        foreach ( range( 1, $number_of_rooms, 1 ) as $value ) {
                            echo '<option>' . $value . '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="room-submit-wrap">
                <input type="hidden" name="post_id" value="<?php echo $form_post_id; ?>">
                <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
                <input type="hidden" name="unique_id" value="<?php echo $unique_id; ?>">
                <input type="hidden" name="location" value="<?php echo $first_location_name; ?>">
                <input type="hidden" name="adult" value="<?php echo $form_adult; ?>">
                <input type="hidden" name="child" value="<?php echo $form_child; ?>">
                <input type="hidden" name="check_in_date" value="<?php echo $form_check_in; ?>">
                <input type="hidden" name="check_out_date" value="<?php echo $form_check_out; ?>">
                <button class="hotel-room-book" type="submit"><?php _e( 'I\'ll reserve', 'tourfic' );?></button>
            </div>
            <div class="tf_desc"></div>
        </form>
    </td>
</tr>
