<div class="tf-booking-details-preview">
    <div class="tf-details-preview-header">
        <div class="tf-back">
            <a href="<?php echo get_admin_url( null, 'edit.php?post_type=tf_hotel&page=tf_hotel_booking' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M15 18L9 12L15 6" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
                <?php _e("Back", "tourfic"); ?>
            </a>
        </div>
        <?php 
        global $wpdb;
        $tf_order_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE id = %s AND order_id = %s",sanitize_key( $_GET['book_id'] ), sanitize_key( $_GET['order_id'] ) ) );
        ?>
        <div class="tf-title">
            <h2><?php echo esc_html( get_the_title( $tf_order_details->post_id ) ); ?></h2>
        </div>
        <div class="tf-booking-id-author">
            <ul>
                <li><?php _e("Booking ID", "tourfic"); ?>: #<?php echo esc_html( $tf_order_details->order_id ); ?></li>
                <li>|</li>
                <li><?php _e("Booking created", "tourfic"); ?>: <?php echo date('F d, Y',strtotime($tf_order_details->order_date)); ?></li>
                <li>|</li>
                <li><?php _e("Booking by", "tourfic"); ?>: <span style="text-transform: capitalize;">
                <?php 
                    $tf_booking_by = get_user_by('id', $tf_order_details->customer_id);
                    if("offline"==$tf_order_details->payment_method && empty($tf_booking_by)){
                        echo "Administrator";
                    }else{
                        echo !empty($tf_booking_by->roles[0]) ? $tf_booking_by->roles[0] : 'Administrator';
                    }
                ?>
                </span>
                </li>
            </ul>
        </div>
    </div>
    <div class="tf-booking-details-preview-box">
        <div class="tf-booking-details">
            
            <!-- Booking Details -->
            <div class="customers-order-date details-box">
                <h4>
                    <?php _e("Booking details", "tourfic"); ?>
                </h4>
                <div class="tf-grid-box tf-customer-details-boxs">
                    <?php
                    $tf_billing_details = json_decode($tf_order_details->billing_details);
                    if(!empty($tf_billing_details)){ ?>
                    <div class="tf-grid-single">
                        <h3><?php _e("Customer details", "tourfic"); ?></h3>
                        <div class="tf-single-box">
                            <table class="table" cellpadding="0" callspacing="0">
                                <?php 
                                foreach($tf_billing_details as $key=>$customer_info){ ?>
                                <tr>
                                    <th><?php echo str_replace("_"," ",esc_html( $key )); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html( $customer_info ); ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                    <?php } ?>
                    <?php
                    $tf_tour_details = json_decode($tf_order_details->order_details);
                    if(!empty( $tf_tour_details )){ ?>
                    <div class="tf-grid-single">
                        <h3><?php _e("Other details", "tourfic"); ?></h3>
                        <div class="tf-single-box">
                            <table class="table">
                                
                                <tr>
                                    <th><?php _e("Name", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html( get_the_title( $tf_order_details->post_id ) ); ?></td>
                                </tr>
                                <?php if ( !empty($tf_tour_details->check_in) ) { ?>
                                <tr>
                                    <th><?php _e("Checkin", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html($tf_tour_details->check_in); ?></td>
                                </tr>
                                <?php } ?>
                                <?php if ( !empty($tf_tour_details->check_out) ) { ?>
                                <tr>
                                    <th><?php _e("Checkout", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html($tf_tour_details->check_out); ?></td>
                                </tr>
                                <?php } ?>
                                <?php if ( !empty($tf_tour_details->room) ) { ?>
                                <tr>
                                    <th><?php _e("Room", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html($tf_tour_details->room); ?></td>
                                </tr>
                                <?php } ?>
                                <?php 
                                $book_adult  = !empty( $tf_tour_details->adult ) ? $tf_tour_details->adult : '';
                                if(!empty($book_adult)){
                                    $tf_total_adult = explode( " × ", $book_adult );
                                } ?>
                                <tr>
                                    <th><?php _e("Adult", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo !empty($tf_total_adult[0]) ? esc_html($tf_total_adult[0]) : 0; ?></td>
                                </tr>
                                
                                <?php 
                                $book_children  = !empty( $tf_tour_details->child ) ? $tf_tour_details->child : '';
                                if(!empty($book_children)){
                                    $tf_total_children = explode( " × ", $book_children );
                                } ?>
                                <tr>
                                    <th><?php _e("Child", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo !empty($tf_total_children[0]) ? esc_html($tf_total_children[0]) : 0; ?></td>
                                </tr>

                                <?php if ( !empty($tf_tour_details->children_ages) ) { ?>
                                <tr>
                                    <th><?php _e("Child Ages", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html($tf_tour_details->children_ages); ?></td>
                                </tr>
                                <?php } ?>

                                <?php if ( !empty($tf_tour_details->airport_service_type) ) { ?>
                                <tr>
                                    <th><?php _e("Airport Service Type", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html($tf_tour_details->airport_service_type); ?></td>
                                </tr>
                                <?php } ?>

                            </table>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Pricing Details -->
            <div class="customers-order-date details-box">
                <h4>
                    <?php _e("Pricing details", "tourfic"); ?>
                </h4>
                <div class="tf-grid-box tf-customer-pricing-box">

                    <div class="tf-grid-single">
                        <div class="tf-single-box">
                            <table class="table">
                                
                                <tr>
                                    <th><?php _e("Payment method", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                    <?php 
                                        if ( ! function_exists( 'tf_get_payment_method_full_name' ) ) {
                                            function tf_get_payment_method_full_name( $sort_name ) {
                                                $payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
                                
                                                if ( isset( $payment_gateways[ $sort_name ] ) ) {
                                                    return $payment_gateways[ $sort_name ]->title;
                                                } else {
                                                    return 'Offline Payment';
                                                }
                                            }
                                        }
                                        $sort_name = $tf_order_details->payment_method;
                                        echo tf_get_payment_method_full_name( $sort_name );
                                    ?>
                                    </td>
                                </tr>
                                <?php if ( !empty($tf_tour_details->airport_service_fee) ) { ?>
                                <tr>
                                    <th><?php _e("Airport Service Fee", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo wc_price($tf_tour_details->airport_service_fee); ?></td>
                                </tr>
                                <?php } ?>
                                <?php 
                                if(!empty($tf_tour_details->total_price)){ ?>
                                <tr>
                                    <th><?php _e("Total", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo wc_price($tf_tour_details->total_price); ?></td>
                                </tr>
                                <?php } ?>
                                <?php 
                                if(!empty($tf_tour_details->due_price)){ ?>
                                <tr>
                                    <th><?php _e("Due Price", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo wc_price($tf_tour_details->due_price); ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>

                    <div class="tf-grid-single">
                        <div class="tf-single-box">
                            <table class="table" cellpadding="0" callspacing="0">
                                <tr>
                                    <th><?php _e("Checked status", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                        <?php 
                                            if( !empty($tf_order_details->checkinout) ){
                                                if( "in"==$tf_order_details->checkinout ){
                                                    _e("Checked in", "tourfic");
                                                }elseif( "out"==$tf_order_details->checkinout ){
                                                    _e("Checked Out", "tourfic");
                                                }elseif( "not"==$tf_order_details->checkinout ){
                                                    _e("Not checked in", "tourfic");
                                                }
                                            }else{
                                                _e("Not checked in", "tourfic");
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php 
                                $tf_checkinout_by = !empty($tf_order_details->checkinout_by) ? json_decode($tf_order_details->checkinout_by) : '';
                                ?>
                                <tr>
                                    <th><?php _e("Checked in by", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                        <?php
                                        if(!empty($tf_checkinout_by->userid)){
                                            $tf_checkin_by = get_user_by('id', $tf_checkinout_by->userid);
                                            echo !empty($tf_checkin_by->display_name) ? esc_html($tf_checkin_by->display_name) : "";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e("Checked Time", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                        <?php
                                        if(!empty($tf_checkinout_by->time)){
                                            echo !empty($tf_checkinout_by->time) ? esc_html($tf_checkinout_by->time) : "";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="tf-booking-actions">
            <div class="tf-filter-selection">
                <h3><?php _e("Actions", "tourfic"); ?></h3>
                <div class="tf-order-status-filter tf-order-ostatus">
                    <label>
                        <span>
                            <?php 
                                if( !empty($tf_order_details->ostatus) ){
                                    if( "trash"==$tf_order_details->ostatus ){
                                        _e("Trash", "tourfic");
                                    }elseif( "processing"==$tf_order_details->ostatus ){
                                        _e("Processing", "tourfic");
                                    }elseif( "on-hold"==$tf_order_details->ostatus ){
                                        _e("On Hold", "tourfic");
                                    }elseif( "completed"==$tf_order_details->ostatus ){
                                        _e("Complete", "tourfic");
                                    }elseif( "cancelled"==$tf_order_details->ostatus ){
                                        _e("Cancelled", "tourfic");
                                    }elseif( "refunded"==$tf_order_details->ostatus ){
                                        _e("Refund", "tourfic");
                                    }
                                }else{
                                    _e("Processing", "tourfic");
                                }
                            ?>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <ul>
                        <li data-value="trash"><?php _e("Trash", "tourfic"); ?></li>
                        <li data-value="processing"><?php _e("Processing", "tourfic"); ?></li>
                        <li data-value="on-hold"><?php _e("On Hold", "tourfic"); ?></li>
                        <li data-value="completed"><?php _e("Complete", "tourfic"); ?></li>
                        <li data-value="cancelled"><?php _e("Cancelled", "tourfic"); ?></li>
                        <li data-value="refunded"><?php _e("Refund", "tourfic"); ?></li>
                    </ul>
                </div>
            </div>

            <div class="tf-filter-selection">
                <h3><?php _e("Checked in status", "tourfic"); ?></h3>
                <div class="tf-order-status-filter tf-order-checkinout-status">
                    <label>
                        <span>
                            <?php 
                                if( !empty($tf_order_details->checkinout) ){
                                    if( "in"==$tf_order_details->checkinout ){
                                        _e("Checked in", "tourfic");
                                    }elseif( "out"==$tf_order_details->checkinout ){
                                        _e("Checked Out", "tourfic");
                                    }elseif( "not"==$tf_order_details->checkinout ){
                                        _e("Not checked in", "tourfic");
                                    }
                                }else{
                                    _e("Not checked in", "tourfic");
                                }
                            ?>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <input type="hidden" id="tf_email_order_id" value="<?php echo !empty($_GET['order_id']) ? esc_html( $_GET['order_id'] ) : ''; ?>">
                    <input type="hidden" class="tf_single_order_id" name="order_id" value="<?php echo $tf_order_details->id; ?>">
                    <ul>
                        <li class="checkin" data-value="in"><?php _e("Checked in", "tourfic"); ?></li>
                        <li class="checkout" data-value="out"><?php _e("Checked Out", "tourfic"); ?></li>
                        <li class="checkout" data-value="not"><?php _e("Not checked in", "tourfic"); ?></li>
                    </ul>
                </div>
            </div>

            <div class="tf-filter-selection">
                <h3><?php _e("Sent order mail", "tourfic"); ?></h3>
                <div class="tf-order-status-filter tf-order-email-resend">
                    <label>
                        <span><?php _e("Resend Order Mail", "tourfic"); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <ul>
                        <li data-value="customer"><?php _e("Customer", "tourfic"); ?></li>
                        <?php 
                        $tf_vendor_id = get_post_field ('post_author', $tf_order_details->post_id);
                        //get user role by id
                        $tf_user = get_user_by( 'id', $tf_vendor_id );
                        $tf_user_role = !empty( $tf_user->roles[0] ) ? $tf_user->roles[0] : '';
                        //check if user role is vendor
                        if( $tf_user_role == 'tf_vendor' ){
                        ?>
                            <li data-value="vendor"><?php _e("Vendor", "tourfic"); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="tf-preloader-box">
    <div class="tf-loader-preview">
        <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="Loader">
    </div>
</div>