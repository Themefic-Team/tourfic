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

<div class="tf-rooms-sections tf-mb-50 tf-template-section">
    <h2 class="section-heading tf-section-title"><?php echo !empty($meta['room-section-title']) ? esc_html($meta['room-section-title']) : __( 'Available Rooms', 'tourfic' ); ?></h2>
    <?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>

    <div class="tf-rooms" id="rooms">
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
                    <th class="description" colspan="3"><?php _e( 'Room Details', 'tourfic' ); ?></th>
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
                    $multi_by_date = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
                    $child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";

	                // Hotel Room Discount Data
	                $hotel_discount_type = !empty($room["discount_hotel_type"]) ? $room["discount_hotel_type"] : "none";
	                $hotel_discount_amount = !empty($room["discount_hotel_price"]) ? $room["discount_hotel_price"] : 0;

	                if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avil_by_date == '1' ) {
	                    $avail_date = ! empty( $room['avail_date'] ) ? json_decode($room['avail_date'], true) : [];
	                    if ($pricing_by == '1') {
		                    $prices = array();

		                    foreach ($avail_date as $date => $data) {
			                    if ($data['status'] == 'available') {
				                    $prices[] = $data['price'];
			                    }
		                    }
	                    } else {
		                    $prices = array();

		                    foreach ($avail_date as $date => $data) {
			                    if ($data['status'] == 'available') {
				                    $prices[] = $data['adult_price'];
			                    }
		                    }
	                    }
                        if ( ! empty( $prices ) ) {
                            $range_price = [];
                            foreach ( $prices as $single ) {
                                if ( ! empty( $single ) ) {
                                    $range_price[] = $single;
                                }
                            }
                            if ( sizeof( $range_price ) > 1 ) {
                                foreach($prices as $value) {
                                    if($hotel_discount_type == "percent") {
                                        $discount_prices[] = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $value - ( ( (int) $value / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
                                    } else if( $hotel_discount_type == "fixed") {
                                        $discount_prices[] = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $value - (int) $hotel_discount_amount ), 2 ) ) );;
                                    }
                                }
                                $discount_price = !empty($discount_prices) ? ( min( $discount_prices ) != max( $discount_prices ) ? wc_format_price_range( min( $discount_prices ), max( $discount_prices ) ) : wc_price( min( $discount_prices ) ) ) : "";
                                $price = $prices ? ( min( $prices ) != max( $prices ) ? wc_format_price_range( min( $prices ), max( $prices ) ) : wc_price( min( $prices ) ) ) : wc_price( 0 );
                            } else {
                                $price = ! empty( $range_price[0] ) ? $range_price[0] : 0;
                                if($hotel_discount_type == "percent") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
                                    $discount_price = wc_price($discount_price);
                                }
                                if($hotel_discount_type == "fixed") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) );;
                                    $discount_price = wc_price($discount_price);
                                }
                                $price = wc_price( $price );
                            }
                        }else{
                            if ( $pricing_by == '1' ) {
                                $price = ! empty( $room['price'] ) ? $room['price'] : '0.0';
                                if($hotel_discount_type == "percent") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
                                    $discount_price = wc_price($discount_price);
                                }else if($hotel_discount_type == "fixed") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) );;
                                    $discount_price = wc_price($discount_price);
                                }
                                $price = wc_price($price);
                            } else {
                                $price = ! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0';
                                if($hotel_discount_type == "percent") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
                                    $discount_price = wc_price($discount_price);
                                }else if($hotel_discount_type == "fixed") {
                                    $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ((int) $price - (int) $hotel_discount_amount ), 2 ) ) );;
                                    $discount_price = wc_price($discount_price);
                                }
                                $price = wc_price($price);
                            }
                        }
                    } else {
                        if ( $pricing_by == '1' ) {
                            $price = ! empty( $room['price'] ) ? $room['price'] : '0.0';
                            if($hotel_discount_type == "percent") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
                                $discount_price = wc_price($discount_price);
                            }
                            if($hotel_discount_type == "fixed") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) );;
                                $discount_price = wc_price($discount_price);
                            }
                            $price = wc_price( $price );
                        } else {
                            $price =! empty( $room['adult_price'] ) ? $room['adult_price'] : '0.0';
                            if($hotel_discount_type == "percent") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
                                $discount_price = wc_price($discount_price);
                            } else if($hotel_discount_type == "fixed") {
                                $discount_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - (int) $hotel_discount_amount ), 2 ) );
                                $discount_price = wc_price($discount_price);
                            }
                            $price = wc_price( $price );
                        }
                    }
                    ?>
                    <tr>
                        <td class="description">
                            <div class="tf-room-description-box tf-flex">
                                <?php
                                    $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                    if ( $tour_room_details_gall ) {
                                        $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                    }
                                ?>

                                <?php
                                $room_preview_img = ! empty( $room['room_preview_img'] ) ? $room['room_preview_img'] : '';
                                if(!empty($room_preview_img)){ ?>
                                <div class="tf-room-preview-img">
                                    <?php
                                    if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_room_details_gall ){
                                    ?>
                                    <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $key : '' ?>"
                                                data-hotel="<?php echo $post_id; ?>">
                                        <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php _e("Room Image","tourfic"); ?>">
                                        <!-- <span><?php //_e("Best Offer", "tourfic"); ?></span> -->
                                    </a>
                                    <?php
                                    }else{ ?>
                                    <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php _e("Room Image","tourfic"); ?>">
                                    <!-- <span><?php //_e("Best Offer", "tourfic"); ?></span> -->
                                    <?php } ?>
                                </div>
                                <?php } ?>
                                <div class="tf-features-infos" style="<?php echo !empty($room_preview_img) ? 'width: 70%' : ''; ?>">
                                    <div class="tf-room-type">
                                        <div class="tf-room-title">
                                            <?php
                                            if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_room_details_gall ){
                                            ?>
                                            <h3>
                                                <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $key : '' ?>"
                                                data-hotel="<?php echo $post_id; ?>"><?php echo esc_html( $room['title'] ); ?></a>
                                            </h3>
                                            <?php
                                            }else{ ?>
                                            <h3><?php echo esc_html( $room['title'] ); ?></h3>
                                            <?php } ?>
                                        </div>
                                        <div class="bed-facilities">
                                            <p>
                                            <?php echo substr(wp_strip_all_tags($room['description']), 0, 120). '...'; ?>
                                            </p>
                                        </div>
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
                                        $tf_room_fec_key = 1;
                                        foreach ( $room['features'] as $feature ) {
                                        if ( $tf_room_fec_key < 5 ) {
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
                                        <?php } $tf_room_fec_key++; } } ?>
                                        <?php
                                        if(!empty($room['features'])){
                                            if(count($room['features']) > 3){
                                                echo '<span>More....</span>';
                                            }
                                        }
                                        ?>
                                    </ul>

                                    <?php
                                    if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_room_details_gall ){
                                        ?>
                                        <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $key : '' ?>"
                                                data-hotel="<?php echo $post_id; ?>" style="text-decoration: underline;">
                                                <?php _e("Room Photos & Details","tourfic"); ?>
                                            </a>

                                        <div id="tour_room_details_qv" class=" <?php echo $tf_hotel_selected_template == "design-1" ? "tf-hotel-design-1-popup" : ""; ?>">

                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                        </td>
                        <td class="pax">
                            <div style="text-align:center; width: 100%;"><?php echo __("Pax:", "tourfic"); ?></div>
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
                                        <?php
                                        if(!empty($child_age_limit)){
                                            printf(__('Children Age Limit %s Years', 'tourfic'), $child_age_limit);
                                        }else{
                                            _e( 'Number of Children', 'tourfic' );
                                        }
                                        ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>
                        <td class="reserve tf-t-c">
                            <?php
                            if ( $pricing_by == '1' ) {
                                if(!empty($discount_price )) {
                                    ?>
                                    <span class="tf-price"><del><?php echo $price; ?></del> <?php echo $discount_price; ?></span>
                                    <?php
                                    $discount_price = "";
                                } else if($hotel_discount_type == "none") {
                                    ?>
                                    <span class="tf-price"><?php echo $price; ?></span>
                                    <?php
                                }
                                ?>
                                <div class="price-per-night">
                                    <?php
                                    if($multi_by_date){
                                        esc_html_e( 'per night', 'tourfic' );
                                    }else{
                                        esc_html_e( 'per day', 'tourfic' );
                                    } ?>
                                </div>
                                <?php
                            } else {
                                if(!empty($discount_price )) {
                                    ?>
                                    <span class="tf-price"><del><?php echo $price; ?></del> <?php echo $discount_price; ?></span>
                                    <?php
                                    $discount_price = "";
                                } else if($hotel_discount_type == "none") {
                                    ?>
                                    <span class="tf-price"><?php echo $price; ?></span>
                                    <?php
                                }
                                ?>

                                <div class="price-per-night">
                                    <?php
                                    if($multi_by_date){
                                        esc_html_e( 'per person/night', 'tourfic' );
                                    }else{
                                        esc_html_e( 'per person/day', 'tourfic' );
                                    } ?>
                                </div>
                                <?php
                            }
                            ?>

                            <button class="hotel-room-availability tf-btn-normal btn-secondary" type="submit" style="margin: 0 auto;">
                                <?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                            </button>

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