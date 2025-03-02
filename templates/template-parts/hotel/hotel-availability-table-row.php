<?php 
$custom_adult_number     = !empty( $room['adult'] ) ? $room['adult'] : 0;

if($form_adult == $custom_adult_number ){
?>
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
            <h3><a href="#" class="tf-room-detail-qv" data-roomid="<?php echo esc_attr($room_id); ?>" data-uniqid="<?php echo !empty($room['unique_id']) ? $room['unique_id'] : '' ?>" data-hotel="<?php echo $form_post_id; ?>" style="text-decoration: underline;">
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

                        if(!empty($room_f_meta)){
                            if ( $room_f_meta['icon-type'] == 'fa' ) {
                                $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                            } elseif ( $room_f_meta['icon-type'] == 'c' ) {
                                $room_feature_icon = '<img src="' . $room_f_meta['icon-c']["url"] . '" style="min-width: ' . $room_f_meta['dimention']["width"] . 'px; height: ' . $room_f_meta['dimention']["width"] . 'px;" />';
                            }
                        }

                    $room_term = get_term( $feature );?>
                    <li class="tf-tooltip">
                        <?php echo !empty($room_f_meta) ? $room_feature_icon : ''; ?>
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
            
                <div class="price-per-night">
                <b style="font-size: 17px; text-transform: uppercase;">
                <?php
                if(defined( 'TF_PRO' )){ ?>
                    <?php $days > 0 ? esc_html_e( 'FULL STAY PER PERSON', 'tourfic' ) :  esc_html_e( 'FULL STAY PER PERSON', 'tourfic' );?>
                <?php } ?>
                </b>
                </div>
                <?php 
                if(defined( 'TF_PRO' )){ 
                ?>

                    <?php 
                    if($days==8){
                    $tf8days  = !empty($room['tf-8-days']) ? $room['tf-8-days'] : ''; 
                    if(!empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold'])){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" id="tf-hotel-duration" value="8" checked> <?php esc_html_e( '8 DAYS', 'tourfic' ); ?>  <?php echo $tf8days['tf-room'] ? wc_price($tf8days['tf-room']) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf8days['tf-breakfast'])){ ?>
                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf8days['tf-breakfast'] ? wc_price($tf8days['tf-breakfast']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-breakfast" ></span>
                            <?php } ?>
                            <?php if(!empty($tf8days['tf-half-b'])){ ?>
                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf8days['tf-half-b'] ? wc_price($tf8days['tf-half-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-half-b" ></span>
                            <?php } ?>
                            <?php if(!empty($tf8days['tf-full-b'])){ ?>
                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf8days['tf-full-b'] ? wc_price($tf8days['tf-full-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-full-b" ></span>
                            <?php } ?>
                        </div>
                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf8days['tf-inclusive'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf8days['tf-inclusive'] ? wc_price($tf8days['tf-inclusive']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive" ></span>
                            <?php } ?>
                            <?php if(!empty($tf8days['tf-inclusive-gold'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf8days['tf-inclusive-gold'] ? wc_price($tf8days['tf-inclusive-gold']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive-gold" ></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } } ?>

                    <?php 
                    if($days==15){
                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                    if(!empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold'])){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" id="tf-hotel-duration" value="16" checked> <?php esc_html_e( '16 DAYS', 'tourfic' ); ?>  <?php echo $tf16days['tf-room'] ? wc_price($tf16days['tf-room']) : '';  ?></span>
                        
                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf16days['tf-breakfast'])){ ?>
                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf16days['tf-breakfast'] ? wc_price($tf16days['tf-breakfast']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-breakfast" ></span>
                            <?php } ?>
                            <?php if(!empty($tf16days['tf-half-b'])){ ?>
                            
                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf16days['tf-half-b'] ? wc_price($tf16days['tf-half-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-half-b" ></span>
                            <?php } ?>

                            <?php if(!empty($tf16days['tf-full-b'])){ ?>
                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf16days['tf-full-b'] ? wc_price($tf16days['tf-full-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-full-b" ></span>
                            <?php } ?>
                        </div>
                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf16days['tf-inclusive'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf16days['tf-inclusive'] ? wc_price($tf16days['tf-inclusive']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive" ></span>
                            <?php } ?>
                            <?php if(!empty($tf16days['tf-inclusive-gold'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf16days['tf-inclusive-gold'] ? wc_price($tf16days['tf-inclusive-gold']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive-gold" ></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } } ?>

                    <?php 
                    if($days==22){
                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                    if(!empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold'])){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" id="tf-hotel-duration" value="24" checked> <?php esc_html_e( '24 DAYS', 'tourfic' ); ?>  <?php echo $tf24days['tf-room'] ? wc_price($tf24days['tf-room']) : '';  ?></span>
                        

                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf24days['tf-breakfast'])){ ?>
                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf24days['tf-breakfast'] ? wc_price($tf24days['tf-breakfast']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-breakfast" ></span>
                            <?php } ?>
                            <?php if(!empty($tf24days['tf-half-b'])){ ?>
                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf24days['tf-half-b'] ? wc_price($tf24days['tf-half-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-half-b" ></span>
                            <?php } ?>
                            <?php if(!empty($tf24days['tf-full-b'])){ ?>
                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf24days['tf-full-b'] ? wc_price($tf24days['tf-full-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-full-b" ></span>
                            <?php } ?>
                        </div>
                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf24days['tf-inclusive'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf24days['tf-inclusive'] ? wc_price($tf24days['tf-inclusive']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive" ></span>
                            <?php } ?>
                            <?php if(!empty($tf24days['tf-inclusive-gold'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf24days['tf-inclusive-gold'] ? wc_price($tf24days['tf-inclusive-gold']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive-gold" ></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } } ?>

                    <?php 
                    if($days==29){
                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                    if(!empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold'])){
                    ?>
                    <div class="tf-single-prices">
                        <span><input type="radio" id="tf-hotel-duration" value="32" checked> <?php esc_html_e( '32 DAYS', 'tourfic' ); ?>  <?php echo $tf32days['tf-room'] ? wc_price($tf32days['tf-room']) : '';  ?></span>
                        

                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf32days['tf-breakfast'])){ ?>
                            <span><?php esc_html_e( 'Con Desayuno', 'tourfic' ); ?> <br><?php echo $tf32days['tf-breakfast'] ? wc_price($tf32days['tf-breakfast']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-breakfast" ></span>
                            <?php } ?>
                            <?php if(!empty($tf32days['tf-half-b'])){ ?>
                            <span><?php esc_html_e( 'Con Media Pensión', 'tourfic' ); ?> <br><?php echo $tf32days['tf-half-b'] ? wc_price($tf32days['tf-half-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-half-b" ></span>
                            <?php } ?>
                            <?php if(!empty($tf32days['tf-full-b'])){ ?>
                            <span><?php esc_html_e( 'Pensión Completa', 'tourfic' ); ?> <br><?php echo $tf32days['tf-full-b'] ? wc_price($tf32days['tf-full-b']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-full-b" ></span>
                            <?php } ?>
                        </div>
                        <div class="tf-single-prices-item">
                            <?php if(!empty($tf32days['tf-inclusive'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluido', 'tourfic' ); ?> <br><?php echo $tf32days['tf-inclusive'] ? wc_price($tf32days['tf-inclusive']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive" ></span>
                            <?php } ?>
                            <?php if(!empty($tf32days['tf-inclusive-gold'])){ ?>
                            <span class="inclusive"><?php esc_html_e( 'Todo Incluído Plus, Lavandería incluída', 'tourfic' ); ?> <br><?php echo $tf32days['tf-inclusive-gold'] ? wc_price($tf32days['tf-inclusive-gold']) : '';  ?> <br> <input type="radio" name="tf-meals-info" id="tf-meals-info" value="tf-inclusive-gold" ></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                            
                    <?php
                } }
                ?>

            
        </div>
    </td>
    <td class="reserve">
		
						
        <form class="tf-room">
            <?php wp_nonce_field( 'check_room_booking_nonce', 'tf_room_booking_nonce' );?>
            <?php 
            
            $custom_avil_by_date = !empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : '';
            $custom_order_ids = !empty($room['order_id']) ? $room['order_id'] : '';
            $custom_reduce_num_room  = !empty($room['reduce_num_room']) ? $room['reduce_num_room'] : '';
            
            if($custom_avil_by_date){
                $custom_number_orders = 0;
                if( !empty( $custom_order_ids ) && defined( 'TF_PRO' ) && $custom_reduce_num_room == true ) {

                    # Convert order ids to array
                    $custom_order_ids = explode(',', $custom_order_ids);

                    # Run foreach loop through oder ids
                    foreach( $custom_order_ids as $order_id ) {

                        # Get $order object from order ID
                        $order = wc_get_order( $order_id );

                        # Get Only the completed orders
                        if ( $order && $order->get_status() == 'completed' ) {

                            # Get and Loop Over Order Items
                            foreach ( $order->get_items() as $item_id => $item ) {

                                /**
                                 * Order item data
                                 */                                          
                                $custom_ordered_number_of_room = $item->get_meta( 'number_room_booked', true );

                                // $custom_repeat_by_date = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                
                                // foreach($custom_repeat_by_date as $custom_single_date_range) {               
                                                                
                                //     $customstartdatesearch = array_search($custom_single_date_range["availability"]["from"],$durationdate,true);
                                //     $customenddatesearch = array_search($custom_single_date_range["availability"]["to"],$durationdate,true);

                                //     if( !empty($customstartdatesearch) || !empty($customenddatesearch) ) {
                                        
                                //         $custom_num_room_available = !empty($custom_single_date_range['room_number']) ? $custom_single_date_range['room_number'] : 1;
                                        
                                //         $custom_startorderdatesearch = array_search($item->get_meta( 'check_in', true ),$durationdate,true);
                                //         $custtom_enddateordersearch = array_search($item->get_meta( 'check_out', true ),$durationdate,true);
                                //         if( !empty($custom_startorderdatesearch) || !empty($custtom_enddateordersearch) ) {
                                //         $custom_number_orders = $custom_number_orders + $custom_ordered_number_of_room;
                                //         }
                                                                                        
                                //     }
                                // }


                                $tfcustom_repeat_by_date_period = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                                if(!empty($tfcustom_repeat_by_date_period)){
                                    foreach($tfcustom_repeat_by_date_period as $custom_single_date_range) {               

                                    $availbilityperiod = new DatePeriod(
                                        new DateTime( $custom_single_date_range["availability"]["from"] . ' 00:00' ),
                                        new DateInterval( 'P1D' ),
                                        new DateTime( $custom_single_date_range["availability"]["to"] . ' 23:59' )
                                    );

                                    $availbilitydurationdate = [];
                                    foreach ( $availbilityperiod as $date ) {
                                        $availbilitydurationdate[$date->format( 'Y/m/d')] = $date->format( 'Y/m/d');
                                    }
                                    
                                    $customavail_result = array_intersect($availbilitydurationdate,$durationdate);
                                    
                                    if( !empty($customavail_result) ) {
                                        $custom_num_room_available = !empty($custom_single_date_range['room_number']) ? $custom_single_date_range['room_number'] : 1; 
                                        
                                        $custom_startorderdatesearch = array_search($item->get_meta( 'check_in', true ),$durationdate,true);
                                        $custtom_enddateordersearch = array_search($item->get_meta( 'check_out', true ),$durationdate,true);
                                        if( !empty($custom_startorderdatesearch) || !empty($custtom_enddateordersearch) ) {
                                        $custom_number_orders = $custom_number_orders + $custom_ordered_number_of_room;
                                        }
                                    }

                                    }
                                }


                            }
                        }

                    }   

                    if(!empty($custom_num_room_available)){
                        # Calculate available room number after order
                        $custom_num_room_available = 0 - $custom_number_orders; // Calculate
                        $custom_num_room_available = max($custom_num_room_available, 0); // If negetive value make that 0
                    }else{
                        $custom_num_room_available = !empty($room['num-room']) ? $room['num-room'] : 1;
                        $custom_num_room_available = $custom_num_room_available - $custom_number_orders; // Calculate
                        $custom_num_room_available = max($custom_num_room_available, 0); // If 
                    }


                }else{

                    $tfcustom_repeat_by_date_period = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                    $tfcustom_rept_check = 0;
                    if(!empty($tfcustom_repeat_by_date_period)){
                        foreach($tfcustom_repeat_by_date_period as $custom_single_date_range) {               

                        $availbilityperiod = new DatePeriod(
                            new DateTime( $custom_single_date_range["availability"]["from"] . ' 00:00' ),
                            new DateInterval( 'P1D' ),
                            new DateTime( $custom_single_date_range["availability"]["to"] . ' 23:59' )
                        );

                        $availbilitydurationdate = [];
                        foreach ( $availbilityperiod as $date ) {
                            $availbilitydurationdate[$date->format( 'Y/m/d')] = $date->format( 'Y/m/d');
                        }
                        
                        $customavail_result = array_intersect($availbilitydurationdate,$durationdate);
                        
                        if( !empty($customavail_result) ) {
                            $custom_num_room_available = !empty($custom_single_date_range['room_number']) ? $custom_single_date_range['room_number'] : 0;
                            $tfcustom_rept_check+=1;                                               
                        }

                        }
                    }
                    if( $tfcustom_rept_check < 1 ){
                        $custom_num_room_available = !empty($room['num-room']) ? $room['num-room'] : 1;
                    }



                    
                    // $custom_repeat_by_date = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                    // foreach($custom_repeat_by_date as $custom_single_date_range) {               
                                                    
                    //     // $customstartdatesearch = array_search($custom_single_date_range["availability"]["from"],$durationdate,true);
                    //     // $customenddatesearch = array_search($custom_single_date_range["availability"]["to"],$durationdate,true);

                    //     if( !empty($customavail_result) ) {
                    //         $custom_num_room_available = !empty($custom_single_date_range['room_number']) ? $custom_single_date_range['room_number'] : 1;
                                                                            
                    //     }else{
                    //         $custom_num_room_available = !empty($room['num-room']) ? $room['num-room'] : 1;
                    //     }
                    
                    // }

                }
            }else{

                $custom_number_orders = 0;
                if( !empty( $custom_order_ids ) && defined( 'TF_PRO' ) && $custom_reduce_num_room == true ) {

                    # Convert order ids to array
                    $custom_order_ids = explode(',', $custom_order_ids);

                    # Run foreach loop through oder ids
                    foreach( $custom_order_ids as $order_id ) {

                        # Get $order object from order ID
                        $order = wc_get_order( $order_id );

                        # Get Only the completed orders
                        if ( $order && $order->get_status() == 'completed' ) {

                            # Get and Loop Over Order Items
                            foreach ( $order->get_items() as $item_id => $item ) {

                                /**
                                 * Order item data
                                 */                                          
                                $custom_ordered_number_of_room = $item->get_meta( 'number_room_booked', true );

                                $custom_num_room_available = !empty($room['num-room']) ? $room['num-room'] : 1;               
                                          
                                $custom_number_orders = $custom_number_orders + $custom_ordered_number_of_room;
                                  
                            }
                        }

                    }   

                    if(!empty($custom_num_room_available)){
                        # Calculate available room number after order
                        $custom_num_room_available = $custom_num_room_available - $custom_number_orders; // Calculate
                        $custom_num_room_available = max($custom_num_room_available, 0); // If negetive value make that 0
                    }


                }else{
                    $custom_num_room_available = !empty($room['num-room']) ? $room['num-room'] : 1;
                }
            }
            if(!empty($custom_num_room_available)){ ?>
            <span class="num-room-title"><?php _e("Number of Rooms", "tourfic"); ?></span>
                <div class="room-selection-wrap">
                    <select name="hotel_room_selected" id="hotel-room-selected">
                        <?php
                            foreach ( range( 1, $custom_num_room_available) as $value ) {
                                echo '<option>' . $value . '</option>';
                            }
                        ?>
                    </select>
                </div>
            <?php } ?>
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
                <input type="hidden" id="hotel_meal_val">
                <?php 
                $tour_hotel_service_avail = !empty($meta['airport_service']) ? $meta['airport_service'] : '';
                $tour_hotel_service_type = !empty($meta['airport_service_type']) ? $meta['airport_service_type'] : '';
                
                if(defined( 'TF_PRO' ) && !empty($tour_hotel_service_avail) && !empty($tour_hotel_service_type)){
                ?>
                <?php 
                if(!empty($order_ids)){
                if(!empty($custom_num_room_available)){ ?>
                    <?php if($days<=29){ ?>
				
						<?php if($days==8){
							 if(!empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold'])){ ?>
						 <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==15){
                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                    if(!empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold'])){ ?>
				<button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				<?php if($days==22){
                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                    if(!empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==29){
                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                    if(!empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
                    <?php }else{ ?>
                        <div class="price-per-night"><?php _e( 'Please Select Date within 29 days', 'tourfic' );?></div>
                    <?php } ?>
                <?php }else{ ?>
                    <button class="btn-styled tf-sml-btn" disabled><?php _e( 'Room Not Available', 'tourfic' );?></button>
                <?php } ?>
                <?php }else{ ?>
                    <?php if($days<=29){ ?>
				
                        <?php if($days==8){
							 if(!empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==15){
                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                    if(!empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				<?php if($days==22){
                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                    if(!empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==29){
                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                    if(!empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
                    <?php }else{ ?>
                        <div class="price-per-night"><?php _e( 'Please Select Date within 29 days', 'tourfic' );?></div>
                    <?php } ?>
                <?php } ?>
                
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
                <?php 
                if(!empty($order_ids)){
                if(!empty($custom_num_room_available)){ ?>
                    <?php if($days<=29){ ?>
                    
				<?php if($days==8){
							 if(!empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==15){
                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                    if(!empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				<?php if($days==22){
                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                    if(!empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==29){
                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                    if(!empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
                    <?php }else{ ?>
                        <div class="price-per-night"><?php _e( 'Please Select Date within 29 days', 'tourfic' );?></div>
                    <?php } ?>
                <?php }else{ ?>
                    <button class="btn-styled tf-sml-btn " disabled><?php _e( 'Room Not Available', 'tourfic' );?></button>
                <?php } ?>
                <?php }else{ ?>
                    <?php if($days<=29){ 
                    if(!empty($custom_num_room_available)){    
                    ?>
                    <?php if($days==8){
							 if(!empty($tf8days['tf-room']) || !empty($tf8days['tf-breakfast']) || !empty($tf8days['tf-half-b']) || !empty($tf8days['tf-full-b']) || !empty($tf8days['tf-inclusive']) || !empty($tf8days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==15){
                    $tf16days  = !empty($room['tf-16-days']) ? $room['tf-16-days'] : ''; 
                    if(!empty($tf16days['tf-room']) || !empty($tf16days['tf-breakfast']) || !empty($tf16days['tf-half-b']) || !empty($tf16days['tf-full-b']) || !empty($tf16days['tf-inclusive']) || !empty($tf16days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				<?php if($days==22){
                    $tf24days  = !empty($room['tf-24-days']) ? $room['tf-24-days'] : ''; 
                    if(!empty($tf24days['tf-room']) || !empty($tf24days['tf-breakfast']) || !empty($tf24days['tf-half-b']) || !empty($tf24days['tf-full-b']) || !empty($tf24days['tf-inclusive']) || !empty($tf24days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
				<?php if($days==29){
                    $tf32days  = !empty($room['tf-32-days']) ? $room['tf-32-days'] : ''; 
                    if(!empty($tf32days['tf-room']) || !empty($tf32days['tf-breakfast']) || !empty($tf32days['tf-half-b']) || !empty($tf32days['tf-full-b']) || !empty($tf32days['tf-inclusive']) || !empty($tf32days['tf-inclusive-gold'])){ ?>
                        <button class="hotel-room-book btn-styled tf-sml-btn" type="submit"><?php _e( 'Add to Cart', 'tourfic' );?></button>
                        <a href="<?php echo wc_get_checkout_url(); ?>" class="hotel-room-complete"><?php _e( 'Complete your order', 'tourfic' );?></a>
					<?php }else{ ?>
					<button class="btn-styled tf-sml-btn tf-sml-btn-no-pack" disabled><?php _e( 'No Package Available', 'tourfic' );?></button>
					<?php } } ?>
				
                    <?php }else{ ?>
                    <button class="btn-styled tf-sml-btn" disabled><?php _e( 'Room Not Available', 'tourfic' );?></button>
                    <?php } ?>
                    
                    <?php }else{ ?>
                        <div class="price-per-night"><?php _e( 'Please Select Date within 29 days', 'tourfic' );?></div>
                    <?php } ?>
                <?php }} ?>
            </div>
            <div class="tf_desc"></div>
        </form>
    </td>
</tr>
<?php } ?>