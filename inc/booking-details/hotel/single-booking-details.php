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
                <div class="tf-grid-box">

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
                                    <td><?php echo esc_html(wc_price($tf_tour_details->airport_service_fee)); ?></td>
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
                <div class="tf-order-status-filter">
                    <label>
                        <span><?php _e("Order Mail", "tourfic"); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#F0F0F1" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <ul>
                        <li><?php _e("Customer", "tourfic"); ?></li>
                        <li><?php _e("Customer + Vendor", "tourfic"); ?></li>
                    </ul>
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