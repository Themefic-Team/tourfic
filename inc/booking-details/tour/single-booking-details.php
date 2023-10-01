<div class="tf-booking-details-preview">
    <div class="tf-details-preview-header">
        <div class="tf-back">
            <a href="">
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
                        echo $tf_booking_by->roles[0];
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
                <div class="tf-grid-box">
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
                                $book_adult  = !empty( $tf_tour_details->adult ) ? $tf_tour_details->adult : '';
                                if(!empty($book_adult)){
                                    list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
                                } ?>
                                <tr>
                                    <th><?php _e("Adult", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo !empty($tf_total_adult) ? esc_html($tf_total_adult) : 0; ?></td>
                                </tr>
                                
                                <?php 
                                $book_children  = !empty( $tf_tour_details->child ) ? $tf_tour_details->child : '';
                                if(!empty($book_children)){
                                    list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
                                } ?>
                                <tr>
                                    <th><?php _e("Child", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo !empty($tf_total_children) ? esc_html($tf_total_children) : 0; ?></td>
                                </tr>

                                <?php 
                                $book_infants  = !empty( $tf_tour_details->infants ) ? $tf_tour_details->infants : '';
                                if(!empty($book_infants)){
                                    list( $tf_total_infants, $tf_infants_string ) = explode( " × ", $book_infants );
                                } ?>
                                <tr>
                                    <th><?php _e("Infant", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td><?php echo !empty($tf_total_infants) ? esc_html($tf_total_infants) : 0; ?></td>
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
                                    <td><?php echo esc_html($tf_tour_details->tour_extra); ?></td>
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
                    $tf_visitors_details = json_decode($tf_tour_details->visitor_details);
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
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20.7914 12.6075C21.0355 12.3982 21.1575 12.2936 21.2023 12.1691C21.2415 12.0598 21.2415 11.9403 21.2023 11.831C21.1575 11.7065 21.0355 11.6019 20.7914 11.3926L12.3206 4.13202C11.9004 3.77182 11.6903 3.59172 11.5124 3.58731C11.3578 3.58348 11.2101 3.6514 11.1124 3.77128C11 3.90921 11 4.18595 11 4.73942V9.03468C8.86532 9.40813 6.91159 10.4898 5.45971 12.1139C3.87682 13.8846 3.00123 16.176 3 18.551V19.163C4.04934 17.8989 5.35951 16.8766 6.84076 16.166C8.1467 15.5395 9.55842 15.1684 11 15.0706V19.2607C11 19.8141 11 20.0909 11.1124 20.2288C11.2101 20.3487 11.3578 20.4166 11.5124 20.4128C11.6903 20.4084 11.9004 20.2283 12.3206 19.8681L20.7914 12.6075Z" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 7V5.2C18 4.0799 18 3.51984 17.782 3.09202C17.5903 2.71569 17.2843 2.40973 16.908 2.21799C16.4802 2 15.9201 2 14.8 2H9.2C8.0799 2 7.51984 2 7.09202 2.21799C6.71569 2.40973 6.40973 2.71569 6.21799 3.09202C6 3.51984 6 4.0799 6 5.2V7M6 18C5.07003 18 4.60504 18 4.22354 17.8978C3.18827 17.6204 2.37962 16.8117 2.10222 15.7765C2 15.395 2 14.93 2 14V11.8C2 10.1198 2 9.27976 2.32698 8.63803C2.6146 8.07354 3.07354 7.6146 3.63803 7.32698C4.27976 7 5.11984 7 6.8 7H17.2C18.8802 7 19.7202 7 20.362 7.32698C20.9265 7.6146 21.3854 8.07354 21.673 8.63803C22 9.27976 22 10.1198 22 11.8V14C22 14.93 22 15.395 21.8978 15.7765C21.6204 16.8117 20.8117 17.6204 19.7765 17.8978C19.395 18 18.93 18 18 18M15 10.5H18M9.2 22H14.8C15.9201 22 16.4802 22 16.908 21.782C17.2843 21.5903 17.5903 21.2843 17.782 20.908C18 20.4802 18 19.9201 18 18.8V17.2C18 16.0799 18 15.5198 17.782 15.092C17.5903 14.7157 17.2843 14.4097 16.908 14.218C16.4802 14 15.9201 14 14.8 14H9.2C8.0799 14 7.51984 14 7.09202 14.218C6.71569 14.4097 6.40973 14.7157 6.21799 15.092C6 15.5198 6 16.0799 6 17.2V18.8C6 19.9201 6 20.4802 6.21799 20.908C6.40973 21.2843 6.71569 21.5903 7.09202 21.782C7.51984 22 8.07989 22 9.2 22Z" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </h4>
                <div class="tf-grid-box">

                    <div class="tf-grid-single">
                        <h3><?php _e("Your voucher", "tourfic"); ?></h3>
                        <div class="tf-single-box tf-voucher-preview">
                            <?php 
                            $meta = get_post_meta( $tf_order_details->post_id, 'tf_tours_opt', true );
                            $tour_ides = !empty($tf_tour_details->unique_id) ? $tf_tour_details->unique_id : '';
                            // Address
                            $location = isset( $meta['text_location'] ) ? $meta['text_location'] : '';
                            if( !empty($meta['location']) && tf_data_types($meta['location'])){
                                $location = !empty( tf_data_types($meta['location'])['address'] ) ? tf_data_types($meta['location'])['address'] : $location;
                            }
                            // Tour Date
                            $tour_date = $tf_tour_details->tour_date;
                            if ( $tour_date ) {
                                list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
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
                                        if(!empty($tf_total_adult)){ ?>
                                            <h5><?php _e("Adult:", "tourfic"); ?> <?php echo esc_html( $tf_total_adult ) ?></h5>
                                            <?php
                                        }
                                        if(!empty($tf_total_children)){ ?>
                                            <h5><?php _e("Child:", "tourfic"); ?> <?php echo esc_html( $tf_total_children ) ?></h5>
                                            <?php
                                        }
                                        if(!empty($tf_total_infants)){
                                            ?>
                                            <h5><?php _e("Infant:", "tourfic"); ?> <?php echo esc_html( $tf_total_infants ) ?></h5>
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
                            <?php } ?>
                        </div>
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
                                <tr>
                                    <th><?php _e("Checked in by", "tourfic"); ?></th>
                                    <td>:</td>
                                    <td>
                                        <?php
                                        if(!empty($tf_order_details->checkinout_by)){
                                            $tf_checkin_by = get_user_by('id', $tf_order_details->checkinout_by);
                                            echo !empty($tf_checkin_by->display_name) ? esc_html($tf_checkin_by->display_name) : "";
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
                <div class="tf-order-status-filter">
                    <label>
                        <span><?php _e("Bulk action", "tourfic"); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <ul>
                        <li><?php _e("Trash", "tourfic"); ?></li>
                        <li><?php _e("Processing", "tourfic"); ?></li>
                        <li><?php _e("On Hold", "tourfic"); ?></li>
                        <li><?php _e("Complete", "tourfic"); ?></li>
                        <li><?php _e("Cancelled", "tourfic"); ?></li>
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
                <div class="tf-order-status-filter">
                    <label>
                        <span><?php _e("Order status", "tourfic"); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <ul>
                        <li><?php _e("Trash", "tourfic"); ?></li>
                        <li><?php _e("Processing", "tourfic"); ?></li>
                        <li><?php _e("On Hold", "tourfic"); ?></li>
                        <li><?php _e("Complete", "tourfic"); ?></li>
                        <li><?php _e("Cancelled", "tourfic"); ?></li>
                    </ul>
                </div>
            </div>

            <div class="tf-filter-selection">
                <div class="tf-refund-btn">
                    <a href="#">
                        <?php _e("Refund", "tourfic"); ?>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="visitor-details-edit-form">
    <form class="visitor-details-edit-popup">
        <div class="tf-booking-times">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                </svg>
            </span>
        </div>
        <div class="visitor-details-popup">
        <input type="hidden" class="tf_single_order_id" name="order_id" value="<?php echo $tf_order_details->id; ?>">
        <?php 
        for($traveller_in = 1; $traveller_in < $visitor_count; $traveller_in++){ ?>
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
        <div class="tf-quick-view-times">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                </svg>
            </span>
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
                    if(!empty($tf_total_adult)){ ?>
                        <h5><?php _e("Adult:", "tourfic"); ?> <?php echo esc_html( $tf_total_adult ) ?></h5>
                        <?php
                    }
                    if(!empty($tf_total_children)){ ?>
                        <h5><?php _e("Child:", "tourfic"); ?> <?php echo esc_html( $tf_total_children ) ?></h5>
                        <?php
                    }
                    if(!empty($tf_total_infants)){
                        ?>
                        <h5><?php _e("Infant:", "tourfic"); ?> <?php echo esc_html( $tf_total_infants ) ?></h5>
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