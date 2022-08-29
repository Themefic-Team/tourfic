<tr>
    <td class="description">
        <div class="tf-room-type">
            <div class="tf-room-title">
            <?php 
            $tour_room_details_gall = !empty($room['gallery']) ? $room['gallery'] : '';
            if ($tour_room_details_gall) {
                $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
            }
            if (defined( 'TF_PRO' ) && $tour_room_details_gall){ 
            ?>	
            <h3><a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo !empty($room['unique_id']) ? $room['unique_id'] : '' ?>" data-hotel="<?php echo $form_post_id; ?>" style="text-decoration: underline;">
                <?php echo esc_html( $room['title'] ); ?>
            </a></h3>

            <div id="tour_room_details_qv" class="tf-reg-wrap" >
                                                        
            </div>
            <?php } else{ ?>
                <h3><?php echo esc_html( $room['title'] ); ?></h3>
                <?php   
            }
            ?>
            </div>
            <div class="bed-facilities"><?php _e( $room['description'] ); ?></div>
        </div>

        <?php if ( $footage ) {?>
            <div class="tf-tooltip tf-d-ib">
                <div class="room-detail-icon">
                    <span class="room-icon-wrap"><i class="fas fa-ruler-combined"></i></span>
                    <span class="icon-text tf-d-b"><?php echo $footage; ?> <?php _e( 'sft', 'tourfic' ); ?></span>
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
            <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' );?></h4></div>
            <ul class="room-feature-list">

                <?php 
                if( !empty( $room['features'] ) ){
                foreach ( $room['features'] as $feature ) {

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
                <?php } } ?>
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
            <span class="tf-price"><?php
                if(!defined( 'TF_PRO' )){ echo wc_price( $price ); } ?></span>
            <?php if ( $pricing_by == '1' ) { ?>
                <div class="price-per-night">
                <?php
                if(!defined( 'TF_PRO' )){ ?>
                    <?php $days > 0 ? esc_html_e( 'for '.$days .' nights', 'tourfic' ) :  esc_html_e( 'per night', 'tourfic' );?>
                <?php } ?>
                <?php
                if(defined( 'TF_PRO' )){ ?>
                    <?php $days > 0 ? esc_html_e( 'per person/night', 'tourfic' ) :  esc_html_e( 'per days', 'tourfic' );?>
                <?php } ?>
                </div>

                <?php 
                if(defined( 'TF_PRO' )){ 
                ?>
                    <?php 
                    $tf8days  = !empty($room['tf-8-days']) ? $room['tf-8-days'] : ''; 
                    if(!empty($tf8days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><?php esc_html_e( '8 DAYS', 'tourfic' ); ?>  <?php echo $tf8days['tf-room'] ? wc_price($tf8days['tf-room']) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf8days['tf-breakfast'] ? wc_price($tf8days['tf-breakfast']) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf8days['tf-half-b'] ? wc_price($tf8days['tf-half-b']) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf8days['tf-full-b'] ? wc_price($tf8days['tf-full-b']) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>

                    <?php 
                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                    if(!empty($tf16days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><?php esc_html_e( '16 DAYS', 'tourfic' ); ?>  <?php echo $tf16days['tf-room'] ? wc_price($tf16days['tf-room']) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf16days['tf-breakfast'] ? wc_price($tf16days['tf-breakfast']) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf16days['tf-half-b'] ? wc_price($tf16days['tf-half-b']) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf16days['tf-full-b'] ? wc_price($tf16days['tf-full-b']) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>

                    <?php 
                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                    if(!empty($tf24days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><?php esc_html_e( '24 DAYS', 'tourfic' ); ?>  <?php echo $tf24days['tf-room'] ? wc_price($tf24days['tf-room']) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf24days['tf-breakfast'] ? wc_price($tf24days['tf-breakfast']) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf24days['tf-half-b'] ? wc_price($tf24days['tf-half-b']) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf24days['tf-full-b'] ? wc_price($tf24days['tf-full-b']) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>

                    <?php 
                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                    if(!empty($tf32days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><?php esc_html_e( '32 DAYS', 'tourfic' ); ?>  <?php echo $tf32days['tf-room'] ? wc_price($tf32days['tf-room']) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf32days['tf-breakfast'] ? wc_price($tf32days['tf-breakfast']) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf32days['tf-half-b'] ? wc_price($tf32days['tf-half-b']) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf32days['tf-full-b'] ? wc_price($tf32days['tf-full-b']) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>
                            
                    <?php
                }
                ?>
            <?php } else {?>
                <div class="price-per-night">
                <?php
                if(!defined( 'TF_PRO' )){ ?>
                    <?php $days > 0 ? esc_html_e( 'for '.$days .' nights', 'tourfic' ) :  esc_html_e( 'per person/night', 'tourfic' );?>
                <?php } ?>
                <?php
                if(defined( 'TF_PRO' )){ ?>
                    <?php $days > 0 ? esc_html_e( 'per person/night', 'tourfic' ) :  esc_html_e( 'per person/night', 'tourfic' );?>
                <?php } ?>

                </div>
                <?php 
                if(defined( 'TF_PRO' )){ 
                ?>
                    <?php 
                    $tf8days  = !empty($room['tf-8-days']) ? $room['tf-8-days'] : ''; 
                    if(!empty($tf8days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" name="<?php echo $unique_id; ?>" id="tf-hotel-duration" value="8"> <?php esc_html_e( '8 DAYS', 'tourfic' ); ?>  <?php echo $tf8days['tf-room'] ? wc_price($tf8days['tf-room']*$form_adult) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf8days['tf-breakfast'] ? wc_price($tf8days['tf-breakfast']*$form_adult) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf8days['tf-half-b'] ? wc_price($tf8days['tf-half-b']*$form_adult) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf8days['tf-full-b'] ? wc_price($tf8days['tf-full-b']*$form_adult) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>

                    <?php 
                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                    if(!empty($tf16days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" name="<?php echo $unique_id; ?>" id="tf-hotel-duration" value="16"> <?php esc_html_e( '16 DAYS', 'tourfic' ); ?>  <?php echo $tf16days['tf-room'] ? wc_price($tf16days['tf-room']*$form_adult) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf16days['tf-breakfast'] ? wc_price($tf16days['tf-breakfast']*$form_adult) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf16days['tf-half-b'] ? wc_price($tf16days['tf-half-b']*$form_adult) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf16days['tf-full-b'] ? wc_price($tf16days['tf-full-b']*$form_adult) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>

                    <?php 
                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                    if(!empty($tf24days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" name="<?php echo $unique_id; ?>" id="tf-hotel-duration" value="24"> <?php esc_html_e( '24 DAYS', 'tourfic' ); ?>  <?php echo $tf24days['tf-room'] ? wc_price($tf24days['tf-room']*$form_adult) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf24days['tf-breakfast'] ? wc_price($tf24days['tf-breakfast']*$form_adult) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf24days['tf-half-b'] ? wc_price($tf24days['tf-half-b']*$form_adult) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf24days['tf-full-b'] ? wc_price($tf24days['tf-full-b']*$form_adult) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>

                    <?php 
                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                    if(!empty($tf32days)){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" name="<?php echo $unique_id; ?>" id="tf-hotel-duration" value="32"> <?php esc_html_e( '32 DAYS', 'tourfic' ); ?>  <?php echo $tf32days['tf-room'] ? wc_price($tf32days['tf-room']*$form_adult) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <span><?php esc_html_e( 'Breakfast', 'tourfic' ); ?> <br><?php echo $tf32days['tf-breakfast'] ? wc_price($tf32days['tf-breakfast']*$form_adult) : '';  ?></span>
                            
                            <span><?php esc_html_e( 'Half B.', 'tourfic' ); ?> <br><?php echo $tf32days['tf-half-b'] ? wc_price($tf32days['tf-half-b']*$form_adult) : '';  ?></span>

                            <span><?php esc_html_e( 'Full B.', 'tourfic' ); ?> <br><?php echo $tf32days['tf-full-b'] ? wc_price($tf32days['tf-full-b']*$form_adult) : '';  ?></span>
                        </div>
                    </div>
                    <?php } ?>
                            
                    <?php
                }
                ?>
            <?php }?>

            <?php if (defined( 'TF_PRO' ) && $has_deposit == true &&  !empty($deposit_amount)) { ?>
                <span class="tf-price tf-deposit-amount-<?php echo $room_id ?>" style="display: none;"><?php echo wc_price( $deposit_amount ); ?></span>
                <div class="price-per-night tf-deposit-amount-<?php echo $room_id ?> " style="display: none;">need to be deposited</div>
            <?php } ?>
        </div>
    </td>
    <td class="reserve">
        <form class="tf-room">
            <?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' );?>

            <div class="room-selection-wrap">
                <select name="hotel_room_selected" id="hotel-room-selected">
                    <?php
                        foreach ( range( 0, $num_room_available) as $value ) {
                            echo '<option>' . $value . '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="room-submit-wrap">
            <div class="roomselectissue"></div>
            <?php if (defined( 'TF_PRO' ) && $has_deposit == true &&  !empty($deposit_amount) ) { ?>
                
                <div class="room-deposit-wrap">
                    <input type="checkbox" id="tf-make-deposit" name="make_deposit" value="<?php echo $room_id ?>">
                    <label for="tf-make-deposit">I'll make a partial payment</label><br>
                </div>
	        <?php } ?>

                <input type="hidden" name="post_id" value="<?php echo $form_post_id; ?>">
                <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
                <input type="hidden" name="unique_id" value="<?php echo $unique_id; ?>">
                <input type="hidden" name="location" value="<?php echo $first_location_name; ?>">
                <input type="hidden" name="adult" value="<?php echo $form_adult; ?>">
                <input type="hidden" name="child" value="<?php echo $form_child; ?>">
                <input type="hidden" name="check_in_date" value="<?php echo $form_check_in; ?>">
                <input type="hidden" name="check_out_date" value="<?php echo $form_check_out; ?>">
                <input type="hidden" id="hotel_roomid">
                <input type="hidden" id="hotel_room_number">
                <input type="hidden" id="hotel_room_uniqueid">
                <input type="hidden" id="hotel_room_depo" value="false">
                <?php 
                $tour_hotel_service_avail = !empty($meta['airport_service']) ? $meta['airport_service'] : '';
                $tour_hotel_service_type = !empty($meta['airport_service_type']) ? $meta['airport_service_type'] : '';
                
                if(defined( 'TF_PRO' ) && !empty($tour_hotel_service_avail) && !empty($tour_hotel_service_type)){
                ?>
                <a class="tf_air_service tf-sml-btn btn-styled" href="javascript:;" data-room="<?php echo $room_id; ?>"><?php _e( 'I\'ll reserve', 'tourfic' );?></a>
                
                
                <div style="display: none;" id="tf-hotel-services" class="tf-hotel-services-wrap" data-id="<?php echo $room_id ?>">
                    <div class="tf-hotel-services">
                        <div class="tf-hotel-services-text">
                            <h3><?php _e(tfopt('hotel_service_popup_title', 'Add Service to your Booking.'), 'tourfic');?></h3>
                            <p><?php _e(tfopt('deposit-subtitle', 'Select the services you want to add to your booking.'), 'tourfic');?></p>
                        </div>
                        <div class="tf-hotel-service">
                            <label><?php _e('Pickup & Dropoff Service', 'tourfic');?></label>
                            <select id="airport-service" name="airport_service">
                                <option value="none"><?php _e('No Service', 'tourfic');?></option>
                                <?php 
                                foreach($tour_hotel_service_type as $single_service_type){ ?>
                                <option value="<?php echo $single_service_type; ?>">
                                <?php 
                                if("pickup"==$single_service_type){
                                    _e('Pickup Service', 'tourfic');
                                }
                                if("dropoff"==$single_service_type){
                                    _e('Dropoff Service', 'tourfic');
                                }
                                if("both"==$single_service_type){
                                    _e('Pickup & Dropoff Service', 'tourfic');
                                }
                                ?>
                                </option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
                            <input type="hidden" id="hotel-post-id" value="<?php echo $form_post_id; ?>">
                        </div>
                        <div class="tf-airport-pickup-response"> </div>
                        <div class="tf_button_group">
                        <button class="hotel-room-book btn-styled" type="submit"><?php _e(tfopt('hotel_service_popup_action', 'Continue to booking'), 'tourfic');?></button>
                        </div>
                    </div>
                </div>
                
                <?php }else{ ?>
                <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'I\'ll reserve', 'tourfic' );?></button>
                <?php } ?>
            </div>
            <div class="tf_desc"></div>
        </form>
    </td>
</tr>
