<div class="tf-booking-details-preview">
    <div class="tf-details-preview-header">
        <div class="tf-back">
            <a href="<?php echo get_admin_url( null, 'edit.php?post_type=tf_tours&page=tf_tours_booking' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M15 18L9 12L15 6" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
                <?php _e("Back", "tourfic"); ?>
            </a>
        </div>
        <input type="hidden" id="tf_email_order_id" value="<?php echo !empty($_GET['order_id']) ? esc_html( $_GET['order_id'] ) : ''; ?>">
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
                                <?php if ( !empty($tf_tour_details->tour_date) ) { ?>
                                <tr>
                                    <th><?php _e("Tour Date", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html($tf_tour_details->tour_date); ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <th><?php _e("Name", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html( get_the_title( $tf_order_details->post_id ) ); ?></td>
                                </tr>
                                <?php if ( !empty($tf_tour_details->tour_time) ) { ?>
                                <tr>
                                    <th><?php _e("Time", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo esc_html($tf_tour_details->tour_time); ?></td>
                                </tr>
                                <?php } ?>
                                <?php 
                                $tf_total_visitor = 0;
                                $book_adult  = !empty( $tf_tour_details->adult ) ? $tf_tour_details->adult : '';
                                if(!empty($book_adult)){
                                    $tf_total_adult = explode( " × ", $book_adult );
                                } ?>
                                <tr>
                                    <th><?php _e("Adult", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                        <?php if(!empty($tf_total_adult[0])) {
                                            echo esc_html($tf_total_adult[0]); 
                                            $tf_total_visitor += $tf_total_adult[0];
                                        }else{
                                            echo esc_html(0);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                
                                <?php 
                                $book_children  = !empty( $tf_tour_details->child ) ? $tf_tour_details->child : '';
                                if(!empty($book_children)){
                                    $tf_total_children = explode( " × ", $book_children );
                                } ?>
                                <tr>
                                    <th><?php _e("Child", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                        <?php if(!empty($tf_total_children[0])) {
                                            echo esc_html($tf_total_children[0]); 
                                            $tf_total_visitor += $tf_total_children[0];
                                        }else{
                                            echo esc_html(0);
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <?php 
                                $book_infants  = !empty( $tf_tour_details->infants ) ? $tf_tour_details->infants : '';
                                if(!empty($book_infants)){
                                    $tf_total_infants = explode( " × ", $book_infants );
                                } ?>
                                <tr>
                                    <th><?php _e("Infant", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                        <?php if(!empty($tf_total_infants[0])) {
                                            echo esc_html($tf_total_infants[0]); 
                                            $tf_total_visitor += $tf_total_infants[0];
                                        }else{
                                            echo esc_html(0);
                                        }
                                        ?>    
                                    </td>
                                </tr>

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
                <div class="tf-grid-box tf-pricing-grid-box">

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
                                <?php 
                                if(!empty($tf_tour_details->tour_extra)){
                                ?>
                                <tr>
                                    <th><?php _e("Extra", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo $tf_tour_details->tour_extra; ?></td>
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

                </div>
            </div>

            <!-- Visitor Details -->
            <div class="customers-order-date details-box">
                <h4>
                    <?php _e("Visitor details", "tourfic"); ?>
                    <div class="others-button visitor_edit">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M2.39662 15.0963C2.43491 14.7517 2.45405 14.5794 2.50618 14.4184C2.55243 14.2755 2.61778 14.1396 2.70045 14.0142C2.79363 13.8729 2.91621 13.7503 3.16136 13.5052L14.1666 2.49992C15.0871 1.57945 16.5795 1.57945 17.4999 2.49993C18.4204 3.4204 18.4204 4.91279 17.4999 5.83326L6.49469 16.8385C6.24954 17.0836 6.12696 17.2062 5.98566 17.2994C5.86029 17.3821 5.72433 17.4474 5.58146 17.4937C5.42042 17.5458 5.24813 17.5649 4.90356 17.6032L2.08325 17.9166L2.39662 15.0963Z" stroke="#003C79" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php _e("Edit", "tourfic"); ?>
                        </span>
                    </div>
                </h4>
                <div class="tf-grid-box tf-visitor-grid-box">
                    <?php 
                    $tf_visitors_details = !empty($tf_tour_details->visitor_details) ? json_decode($tf_tour_details->visitor_details) : '';
                    $traveler_fields = !empty(tfopt('without-payment-field')) ? tf_data_types(tfopt('without-payment-field')) : '';
                    if(!empty($tf_visitors_details)){
                        $visitor_count = 1;
                        foreach($tf_visitors_details as $visitor){
                    ?>
                    <div class="tf-grid-single">
                        <h3><?php _e("Visitor ".$visitor_count, "tourfic"); ?></h3>
                        <div class="tf-single-box">
                            <table class="table" cellpadding="0" callspacing="0">
                                <?php 
                                if(!empty($traveler_fields)){
                                    foreach($traveler_fields as $field){
                                ?>
                                <tr>
                                    <th><?php echo esc_html( $field['reg-field-label'] ); ?></th>
                                    <td>:</td>
                                    <td><?php
                                    $field_key = $field['reg-field-name'];
                                    if("array"!=gettype($visitor->$field_key)){
                                        echo esc_html( $visitor->$field_key );
                                    }else{
                                        echo implode(",", $visitor->$field_key );
                                    }
                                    ?>
                                    </td>
                                </tr>
                                <?php } }else{ ?>
                                    <tr>
                                        <th><?php _e("Full Name", "tourfic"); ?></th>
                                        <td>:</td>
                                        <td><?php echo !empty($visitor->tf_full_name) ? esc_html( $visitor->tf_full_name ) : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e("Date of birth", "tourfic"); ?></th>
                                        <td>:</td>
                                        <td><?php echo !empty($visitor->tf_dob) ? esc_html( $visitor->tf_dob ) : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e("NID", "tourfic"); ?></th>
                                        <td>:</td>
                                        <td><?php echo !empty($visitor->tf_nid) ? esc_html( $visitor->tf_nid ) : ''; ?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                    <?php $visitor_count++; } } ?>
                    
                </div>
            </div>

            
            <!-- Voucher details -->
            <div class="customers-order-date details-box">
                <h4>
                    <?php _e("Voucher details", "tourfic"); ?>
                    <div class="others-button">
                        <?php 
                        $tf_qr_download_link = !empty($tf_tour_details->unique_id) ? $tf_tour_details->unique_id : '';
                        if(!empty($tf_qr_download_link)){
                        ?>
                        <a href="<?php echo !empty($tf_qr_download_link) ? site_url().'?qr_id='.$tf_qr_download_link : '#'; ?>" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M13 10H18L12 16L6 10H11V3H13V10ZM4 19H20V12H22V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V12H4V19Z" fill="#003C79"/>
                            </svg>
                        </a>
                        <?php } ?>
                    </div>
                </h4>
                <div class="tf-grid-box">

                    <div class="tf-grid-single">
                        <h3><?php _e("Your voucher", "tourfic"); ?></h3>
                        <?php 
                            $meta = get_post_meta( $tf_order_details->post_id, 'tf_tours_opt', true );
                            $tour_ides = !empty($tf_tour_details->unique_id) ? $tf_tour_details->unique_id : '';
                            // Address
                            $location = '';
                            if( !empty($meta['location']) && tf_data_types($meta['location'])){
                                $location = !empty( tf_data_types($meta['location'])['address'] ) ? tf_data_types($meta['location'])['address'] : $location;
                            }
                            // Tour Date
                            $tour_date = $tf_tour_details->tour_date;
                            if ( $tour_date ) {
                                $tour_date_duration = explode( ' - ', $tour_date );
                                if(!empty($tour_date_duration[0])){
                                    $tour_in = $tour_date_duration[0];
                                }
                                if(!empty($tour_date_duration[1])){
                                    $tour_out = $tour_date_duration[1];
                                }
                            }
                            $tour_duration = !empty($tour_out) ? date('d F, Y', strtotime($tour_in)).' - '. date('d F, Y', strtotime($tour_out)) : date('d F, Y', strtotime($tour_in));
                            $tour_time = !empty($tf_tour_details->tour_time) ? $tf_tour_details->tour_time : '';

                            // Contact Information
                            $tour_email    = ! empty( $meta['email'] ) ? $meta['email'] : '';
                            $tour_phone    = ! empty( $meta['phone'] ) ? $meta['phone'] : '';

                            $width = '120';
                            $height = '120'; 
                            $uri = $tour_ides;
                            $title = get_the_title( $tf_order_details->post_id );
                            
                            $tf_qr_watermark = ! empty( tfopt( 'qr_background' ) ) ? tfopt( 'qr_background' ) : TF_ASSETS_APP_URL.'images/ticket-banner.png';
                            if(!empty($tour_ides)){
                        ?>
                        <div class="tf-single-box tf-voucher-preview">
                            <div class="tf-visitor-vouchers" style="background-image: url(<?php echo esc_url($tf_qr_watermark); ?>);">
                                <div class="tf-voucher-header">
                                    <?php
                                    $tf_qr_logo = ! empty( tfopt( 'qr_logo' ) ) ? tfopt( 'qr_logo' ) : '';
                                    if(!empty($tf_qr_logo)){ ?>
                                    <img style="max-width: 140px;" src="<?php echo esc_url($tf_qr_logo); ?>" />
                                    <?php } 
                                    $tf_ticket_prefix = ! empty( tfopt( "qr-ticket-prefix" ) ) ? tfopt( "qr-ticket-prefix" ).'-' : "";
                                    $tf_ticket_title = ! empty( tfopt( "qr-ticket-title" ) ) ? tfopt( "qr-ticket-title" ) : "Booking ID";
                                    ?>
                                    <div class="title">
                                        <h1><?php echo esc_html( $title ); ?></h1>
                                        <span>
                                            <?php echo esc_html( $tf_ticket_title ) .': '. esc_html( $tf_ticket_prefix.$tour_ides ); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="tf-voucher-qr-code">
                                    <div class="time-info">
                                        <h5><?php _e("Date:", "tourfic"); ?> <b><?php echo esc_html( $tour_duration ); ?></b></h5>
                                        <?php if(!empty($tour_time)){ ?>
                                            <h5><?php _e("Time:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $tour_time ); ?></b></h5>
                                        <?php } ?>
                                        <h5><?php _e("Address:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $location ) ?></b></h5>
                                    </div>
                                    <img style="border: 1px solid #ccc;" src="//chart.apis.google.com/chart?cht=qr&chs=<?php echo $width;?>x<?php echo $height; ?>&chl=<?php echo htmlspecialchars($uri); ?>&choe=UTF-8" alt="<?php echo htmlspecialchars( $title ); ?>"/>
                                </div>
                                <div class="tf-voucher-billing-info">
                                    <div class="tf-billing-details">
                                        <?php 
                                        $billing_first_name = !empty($tf_billing_details->billing_first_name) ? $tf_billing_details->billing_first_name : '';
                                        $billing_last_name = !empty($tf_billing_details->billing_last_name) ? $tf_billing_details->billing_last_name : '';
                                        ?>
                                        <h5><?php _e("Name:", "tourfic"); ?> <?php echo esc_html( $billing_first_name.' '.$billing_last_name ); ?></h5>
                                        <h5><?php _e("Price:", "tourfic"); ?> <?php echo wc_price( $tf_tour_details->total_price ) ?></h5>
                                        <?php if(!empty($tf_tour_details->due_price)){ ?>
                                        <h5><?php _e("Due Price:", "tourfic"); ?> <?php echo wc_price( $tf_tour_details->due_price ) ?></h5>
                                        <?php } ?>
                                        <h5 style="text-transform: uppercase;"><?php _e("Payment Status:", "tourfic"); ?> <?php echo esc_html( $tf_order_details->payment_method ) ?></h5>
                                        <?php 
                                        if(!empty($tf_total_adult[0])){ ?>
                                            <h5><?php _e("Adult:", "tourfic"); ?> <?php echo esc_html( $tf_total_adult[0] ) ?></h5>
                                            <?php
                                        }
                                        if(!empty($tf_total_children[0])){ ?>
                                            <h5><?php _e("Child:", "tourfic"); ?> <?php echo esc_html( $tf_total_children[0] ) ?></h5>
                                            <?php
                                        }
                                        if(!empty($tf_total_infants[0])){
                                            ?>
                                            <h5><?php _e("Infant:", "tourfic"); ?> <?php echo esc_html( $tf_total_infants[0] ) ?></h5>
                                            <?php
                                        } ?>
                                    </div>
                                    <div class="tf-cta-info">
                                    <?php
                                    if(!empty($tour_phone) || !empty($tour_email)){ ?>
                                        <h4><b><?php _e("Contact Information:", "tourfic"); ?></b></h4>
                                        <h5><?php _e("For any inquiries or assistance,", "tourfic"); ?></h5>
                                        <h5><?php _e("Phone:", "tourfic"); ?> <?php echo esc_html( $tour_phone ) ?></h5>
                                        <h5><?php _e("Email:", "tourfic"); ?> <?php echo esc_html( $tour_email ) ?></h5>
                                        <?php
                                    } ?>
                                    </div>
                                </div>
                                <div class="tf-voucher-footer-qoute">
                                    <?php
                                    $tf_ticket_qottation = ! empty( tfopt( "qr-ticket-content" ) ) ? tfopt( "qr-ticket-content" ) : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."; ?>
                                    <p><?php echo esc_html( $tf_ticket_qottation ); ?></p>
                                </div>
                            </div>
                            <div class="tf-preview-btn">
                                <a href="#"><?php _e("Preview", "tourfic"); ?></a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="tf-grid-single">
                        <h3><?php _e("Others information", "tourfic"); ?></h3>
                        <div class="tf-single-box tf-checkin-by">
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
<div class="visitor-details-edit-form">
    <form class="visitor-details-edit-popup">
        <div class="tf-visitor-details-edit-header">
            <h2>
                <?php _e("Edit visitor details", "tourfic"); ?>
            </h2>
            <div class="tf-booking-times">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                    <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                    <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                    </svg>
                </span>
            </div>
        </div>
        
        <div class="visitor-details-popup">
        <input type="hidden" class="tf_single_order_id" name="order_id" value="<?php echo $tf_order_details->id; ?>">
        <?php 
        for($traveller_in = 1; $traveller_in <= $tf_total_visitor; $traveller_in++){ ?>
            <div class="tf-single-tour-traveller tf-single-travel">
                <h4><?php _e( 'Traveler '.$traveller_in, 'tourfic' ); ?></h4>
                <div class="traveller-info">
                <?php
                if(empty($traveler_fields)){ ?>
                <div class="traveller-single-info">
                    <label for="tf_full_name<?php echo esc_attr($traveller_in); ?>"><?php _e( 'Full Name', 'tourfic' ); ?></label>
                    <input type="text" name="traveller[<?php echo esc_attr($traveller_in); ?>][tf_full_name]" id="tf_full_name<?php echo esc_attr($traveller_in); ?>" data-required="1" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{'tf_full_name'}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{'tf_full_name'} ) : '' ?>" />
                    
                </div>
                <div class="traveller-single-info">
                    <label for="tf_dob<?php echo esc_attr($traveller_in); ?>"><?php _e( 'Date of birth', 'tourfic' ); ?></label>
                    <input type="date" name="traveller[<?php echo esc_attr($traveller_in); ?>][tf_dob]" id="tf_dob<?php echo esc_attr($traveller_in); ?>" data-required="1" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{'tf_dob'}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{'tf_dob'} ) : '' ?>"/>
                    
                </div>
                <div class="traveller-single-info">
                    <label for="tf_nid<?php echo esc_attr($traveller_in); ?>"><?php _e( 'NID', 'tourfic' ); ?></label>
                    <input type="text" name="traveller[<?php echo esc_attr($traveller_in); ?>][tf_nid]" id="tf_nid<?php echo esc_attr($traveller_in); ?>" data-required="1" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{'tf_nid'}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{'tf_nid'} ) : '' ?>"/>
                    
                </div>
            <?php
            }else{
                foreach($traveler_fields as $field){
                    if("text"==$field['reg-fields-type'] || "email"==$field['reg-fields-type'] || "date"==$field['reg-fields-type']){
                        $field_keys = $field['reg-field-name'];
                        ?>
                        <div class="traveller-single-info">
                            <label for="<?php echo $field['reg-field-name'].$traveller_in ?>"><?php echo sprintf( __( '%s', 'tourfic' ),$field['reg-field-label']); ?></label>
                            <input type="<?php echo $field['reg-fields-type']; ?>" name="traveller[<?php echo $traveller_in; ?>][<?php echo $field['reg-field-name']; ?>]" id="<?php echo $field['reg-field-name'].$traveller_in; ?>" value="<?php echo !empty($tf_visitors_details->{$traveller_in}->{$field_keys}) ? esc_attr( $tf_visitors_details->{$traveller_in}->{$field_keys} ) : '' ?>" />
                        </div>
                    <?php
                    }
                    if("select"==$field['reg-fields-type'] && !empty($field['reg-options'])){
                        $field_keys = $field['reg-field-name'];
                    ?>
                    <div class="traveller-single-info">
                        <label for="<?php echo $field['reg-field-name'].$traveller_in ?>">
                            <?php echo sprintf( __( '%s', 'tourfic' ),$field['reg-field-label']); ?>
                        </label>
                        <select id="<?php echo $field['reg-field-name'].$traveller_in ?>" name="traveller[<?php echo $traveller_in; ?>][<?php echo $field['reg-field-name']; ?>]">
                        <option value=""><?php echo sprintf( __( 'Select One', 'tourfic' )); ?></option>
                        <?php
                        foreach($field['reg-options'] as $sfield){
                            if(!empty($sfield['option-label']) && !empty($sfield['option-value'])){ ?>
                                <option value="<?php echo $sfield['option-value']; ?>" <?php echo !empty($tf_visitors_details->{$traveller_in}->{$field_keys}) && $sfield['option-value']==$tf_visitors_details->{$traveller_in}->{$field_keys} ? esc_attr( 'selected' ) : '' ?>><?php echo $sfield['option-label']; ?></option>';
                            <?php
                            }
                        } ?>
                        </select>
                    </div>
                    <?php
                    }
                    if(("checkbox"==$field['reg-fields-type'] || "radio"==$field['reg-fields-type']) && !empty($field['reg-options'])){
                        $field_keys = $field['reg-field-name'];
                        $tf_fields_values = !empty($tf_visitors_details->{$traveller_in}->{$field_keys}) ? $tf_visitors_details->{$traveller_in}->{$field_keys} : [''];
                    ?>
                        
                    <div class="traveller-single-info">
                    <label for="<?php echo $field['reg-field-name'].$traveller_in ?>">
                    <?php echo sprintf( __( '%s', 'tourfic' ),$field['reg-field-label']); ?>
                    </label>
                        <?php
                        foreach($field['reg-options'] as $sfield){
                            if(!empty($sfield['option-label']) && !empty($sfield['option-value'])){
                                ?>
                                <div class="tf-single-checkbox">
                                    <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="traveller[<?php echo $traveller_in; ?>][<?php echo $field['reg-field-name']; ?>][]" id="<?php echo $sfield['option-value'].$traveller_in; ?>" value="<?php echo $sfield['option-value']; ?>" <?php echo in_array($sfield['option-value'], $tf_fields_values) ? esc_attr( 'checked' ) : ''; ?> />
                                    <label for="<?php echo $sfield['option-value'].$traveller_in; ?>">
                                    <?php echo sprintf( __( '%s', 'tourfic' ),$sfield['option-label']); ?>
                                    </label>
                                </div>
                                <?php
                            }
                        } ?>
                        </div>
                    <?php
                    }
                }
            }
            ?>
            </div>
            </div>
        <?php } ?>
        </div>
        <div class="details-update-btn">
            <button type="submit"><?php _e("Update", "tourfic"); ?></button>
        </div>
    </form>
</div>

<!-- Voucher Quick View -->
<?php
if(!empty($tour_ides)){
?>
<div class="tf-voucher-quick-view-box">
    <div class="voucher-quick-view">
        <div class="tf-voucher-details-preview-header">
            <h2>
                <?php _e("Voucher", "tourfic"); ?>
            </h2>
            <div class="tf-quick-view-times">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                    <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                    <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                    </svg>
                </span>
            </div>
        </div>
        
        <div class="tf-visitor-vouchers" style="background-image: url(<?php echo esc_url($tf_qr_watermark); ?>);">
            <div class="tf-voucher-header">
                <?php
                $tf_qr_logo = ! empty( tfopt( 'qr_logo' ) ) ? tfopt( 'qr_logo' ) : '';
                if(!empty($tf_qr_logo)){ ?>
                <img style="max-width: 140px;" src="<?php echo esc_url($tf_qr_logo); ?>" />
                <?php } 
                $tf_ticket_prefix = ! empty( tfopt( "qr-ticket-prefix" ) ) ? tfopt( "qr-ticket-prefix" ).'-' : "";
                $tf_ticket_title = ! empty( tfopt( "qr-ticket-title" ) ) ? tfopt( "qr-ticket-title" ) : "Booking ID";
                ?>
                <div class="title">
                    <h1><?php echo esc_html( $title ); ?></h1>
                    <span>
                        <?php echo esc_html( $tf_ticket_title ) .': '. esc_html( $tf_ticket_prefix.$tour_ides ); ?>
                    </span>
                </div>
            </div>
            <div class="tf-voucher-qr-code">
                <div class="time-info">
                    <h5><?php _e("Date:", "tourfic"); ?> <b><?php echo esc_html( $tour_duration ); ?></b></h5>
                    <?php if(!empty($tour_time)){ ?>
                        <h5><?php _e("Time:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $tour_time ); ?></b></h5>
                    <?php } ?>
                    <h5><?php _e("Address:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $location ) ?></b></h5>
                </div>
                <img style="border: 1px solid #ccc;" src="//chart.apis.google.com/chart?cht=qr&chs=<?php echo $width;?>x<?php echo $height; ?>&chl=<?php echo htmlspecialchars($uri); ?>&choe=UTF-8" alt="<?php echo htmlspecialchars( $title ); ?>"/>
            </div>
            <div class="tf-voucher-billing-info">
                <div class="tf-billing-details">
                    <?php 
                    $billing_first_name = !empty($tf_billing_details->billing_first_name) ? $tf_billing_details->billing_first_name : '';
                    $billing_last_name = !empty($tf_billing_details->billing_last_name) ? $tf_billing_details->billing_last_name : '';
                    ?>
                    <h5><?php _e("Name:", "tourfic"); ?> <?php echo esc_html( $billing_first_name.' '.$billing_last_name ); ?></h5>
                    <h5><?php _e("Price:", "tourfic"); ?> <?php echo wc_price( $tf_tour_details->total_price ) ?></h5>
                    <?php if(!empty($tf_tour_details->due_price)){ ?>
                    <h5><?php _e("Due Price:", "tourfic"); ?> <?php echo wc_price( $tf_tour_details->due_price ) ?></h5>
                    <?php } ?>
                    <h5 style="text-transform: uppercase;"><?php _e("Payment Status:", "tourfic"); ?> <?php echo esc_html( $tf_order_details->payment_method ) ?></h5>
                    <?php 
                    if(!empty($tf_total_adult[0])){ ?>
                        <h5><?php _e("Adult:", "tourfic"); ?> <?php echo esc_html( $tf_total_adult[0] ) ?></h5>
                        <?php
                    }
                    if(!empty($tf_total_children[0])){ ?>
                        <h5><?php _e("Child:", "tourfic"); ?> <?php echo esc_html( $tf_total_children[0] ) ?></h5>
                        <?php
                    }
                    if(!empty($tf_total_infants[0])){
                        ?>
                        <h5><?php _e("Infant:", "tourfic"); ?> <?php echo esc_html( $tf_total_infants[0] ) ?></h5>
                        <?php
                    } ?>
                </div>
                <div class="tf-cta-info">
                <?php
                if(!empty($tour_phone) || !empty($tour_email)){ ?>
                    <h4><b><?php _e("Contact Information:", "tourfic"); ?></b></h4>
                    <h5><?php _e("For any inquiries or assistance,", "tourfic"); ?></h5>
                    <h5><?php _e("Phone:", "tourfic"); ?> <?php echo esc_html( $tour_phone ) ?></h5>
                    <h5><?php _e("Email:", "tourfic"); ?> <?php echo esc_html( $tour_email ) ?></h5>
                    <?php
                } ?>
                </div>
            </div>
            <div class="tf-voucher-footer-qoute">
                <?php
                $tf_ticket_qottation = ! empty( tfopt( "qr-ticket-content" ) ) ? tfopt( "qr-ticket-content" ) : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."; ?>
                <p><?php echo esc_html( $tf_ticket_qottation ); ?></p>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="tf-preloader-box">
    <div class="tf-loader-preview">
        <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="Loader">
    </div>
</div>