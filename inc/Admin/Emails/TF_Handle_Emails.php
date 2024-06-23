<?php
namespace Tourfic\Admin\Emails;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_Handle_Emails {

	use \Tourfic\Traits\Singleton;

    //free email settings
    protected static $tf_email_settings;
    //Pro metabox email settings
    protected static $tf_mb_email_settings;
    //Pro email template settings
    protected static $tf_email_template_settings;

    /**
     * Constructor
     */
    public function __construct() {
        self::$tf_email_settings = Helper::tfopt( 'email-settings' ) ? Helper::tfopt( 'email-settings' ) : array();
        self::$tf_email_template_settings = !empty( Helper::tfopt( 'email_template_settings' ) ) ? Helper::tfopt( 'email_template_settings' ) : array();
        
        
        //send mail if Tourfic pro is active
        //send confirmation mail
        add_action( 'woocommerce_thankyou', array( $this, 'send_email' ), 10, 1 );
        //send pro confirmation mail
        add_action( 'woocommerce_thankyou', array( $this, 'send_confirmation_email_pro' ), 10, 1 );
        //send cancellation mail
        add_action( 'woocommerce_order_status_cancelled', array( $this, 'send_cancellation_email_pro' ), 10, 1 );
        //Offline Payment send confirmation mail
        add_action( 'tf_offline_payment_booking_confirmation', array( $this,'tf_offline_booking_confirmation_callback'), 10, 2 );

        // Order Email resend confirmation mail
        add_action( 'wp_ajax_tf_order_status_email_resend', array( $this,'tf_order_status_email_resend_function' ) );

    }

    /**
     * email body open markup
     * @param  string $brand_logo
     * @param  string $order_email_heading
     * @param  string $email_heading_bg
     */
    public function email_body_open( $brand_logo, $order_email_heading, $email_heading_bg){
        //email body open
        $email_body_open = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="preconnect" href="https://fonts.googleapis.com"></head><body style="font-family: Inter,sans-serif;font-size: 16px; color: #9C9C9C; margin: 0; padding: 0;">
           <div style="width: 100%; max-width: 600px; margin: 0 auto;">
               <div style="background-color: ' . esc_attr( $email_heading_bg ) . '; color: #fff; padding: 20px;">';
        if (!empty( $brand_logo ) && $brand_logo != '' ) {
            $email_body_open .= '<div style="text-align:center;width:200px;margin: 0 auto;"><img width="200" src="' . esc_url( $brand_logo ) . '" alt="logo" /></div>';
        }
        $email_body_open .= '<div class="heading" style="text-align: center;font-family: Inter,sans-serif">
           <h1 style="font-size: 32px; line-height: 40px; font-weight: 500; letter-spacing: 2px; margin: 20px 0; color: #ffffff;font-family: Inter,sans-serif">
           ' . $order_email_heading . '
           </h1>
           <h2 style="font-size:16px;font-weight:500;line-height:20px;color:#ffffff;font-family: Inter,sans-serif">
                ' . __('Order Number : ', 'tourfic') . '#{booking_id}
           </h2>
       </div>';
        $email_body_open .= '</div>';
        return $email_body_open;
    }

    /**
     * email body close markup
     */
    public function email_body_close(){
        //email body close
        $email_body_close = '</div></body></html>';
        return $email_body_close;
    }


    /**
     * Replace all available mail tags
     * @param  string $template
     * @param  int $order_id
     * @return string
     * @since  2.9.17
     */
    public function replace_mail_tags( $template, $order_id ) {

        $order                  = wc_get_order( $order_id );
        $order_data             = $order->get_data();
        $order_items            = $order->get_items();
        $order_items_data       = array();
        $order_subtotal         = $order->get_subtotal();
        $order_total            = $order->get_total();
        $order_billing_name     = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $order_billing_address  = $order->get_billing_address_1() . ' ' . $order->get_billing_address_2();
        $order_billing_email    = $order->get_billing_email();
        $order_billing_phone    = $order->get_billing_phone();
        $order_billing_city     = $order->get_billing_city();
        $order_billing_country  = $order->get_billing_country();
        $order_billing_postcode = $order->get_billing_postcode();
        $payment_method_title   = $order->get_payment_method_title();
        $order_status           = $order->get_status();
        $order_date_created     = $order->get_date_created();
        //payment method
        $order_url = get_edit_post_link( $order_id );
        //get order items details as table format so we can use it in email template
        foreach ( $order_items as $item_id => $item_data ) {
            $item_name         = $item_data->get_name();
            $item_quantity     = $item_data->get_quantity();
            $item_total        = $item_data->get_total();
            $item_subtotal     = $item_data->get_subtotal();
            $item_subtotal_tax = $item_data->get_subtotal_tax();
            $item_total_tax    = $item_data->get_total_tax();
            $item_taxes        = $item_data->get_taxes();
            $item_meta_data    = $item_data->get_meta_data();

            $item_meta_data_array = array();
            foreach ( $item_meta_data as $meta_data ) {
                $item_meta_data_array[] = array(
                    'key'   => $meta_data->key,
                    'value' => $meta_data->value,
                );
            }
            $order_items_data[] = array(
                'item_name'         => $item_name,
                'item_quantity'     => $item_quantity,
                'item_total'        => $item_total,
                'item_subtotal'     => $item_subtotal,
                'item_meta_data'    => $item_meta_data_array,
            );

        }
       

        global $wpdb;
        $taxs_summations = 0;
        $tf_book_orders = $wpdb->get_results( $wpdb->prepare( "SELECT order_details FROM {$wpdb->prefix}tf_order_data WHERE order_id = %s", $order_id ), ARRAY_A );
        if(!empty($tf_book_orders)){
            foreach($tf_book_orders as $sbook){
                $tf_order_details = !empty($sbook['order_details']) ? json_decode($sbook['order_details']) : '';

                $taxs = !empty($tf_order_details->tax_info) ? json_decode($tf_order_details->tax_info,true) : [];
                if(!empty($taxs)){
                    foreach ( $taxs as $label => $sum ) {
                        $taxs_summations += $sum;
                    }
                }
            }
        }

        $booking_details = '<table width="100%" style="max-width: 600px;border-collapse: collapse; color: #5A5A5A; font-family: Inter,sans-serif;"><thead><tr><th align="left" style="color:#0209AF;">Item Name</th><th align="center" style="color:#0209AF;">Quantity</th><th align="right" style="color:#0209AF;">Price</th></tr></thead><tbody style="border-bottom: 1px solid #D9D9D9">';
        foreach ( $order_items_data as $item ) {
            $booking_details .= '<tr>';
            $booking_details .= '<td style="padding: 15px 0;text-align: left;padding-top: 15px;padding-bottom: 15px;line-height: 1.7;">' . $item['item_name'];
            //item meta data except _order_type,_post_author,_tour_id php loop
            foreach ( $item['item_meta_data'] as $meta_data ) {
                if ( $meta_data['key'] != '_order_type' && $meta_data['key'] != '_post_author' && $meta_data['key'] != '_tour_id' && $meta_data['key'] != '_post_id' && $meta_data['key'] != '_unique_id' && $meta_data['key'] != '_tour_unique_id' && $meta_data['key'] != '_visitor_details' ) {
                    if("room_name"==$meta_data['key']){
                        $tf_email_key = "Room Name";
                    }elseif("number_room_booked"==$meta_data['key']){
                        $tf_email_key = "Booked Room";
                    }elseif("adult"==$meta_data['key']){
                        $tf_email_key = "Adult";
                    }elseif("child"==$meta_data['key']){
                        $tf_email_key = "Child";
                    }elseif("check_in"==$meta_data['key']){
                        $tf_email_key = "Check In";
                    }elseif("check_out"==$meta_data['key']){
                        $tf_email_key = "Check Out";
                    }elseif("due"==$meta_data['key']){
                        $tf_email_key = "Due";
                    }elseif("adults"==$meta_data['key']){
                        $tf_email_key = "Adult";
                    }elseif("children"==$meta_data['key']){
                        $tf_email_key = "Child";
                    }elseif("infant"==$meta_data['key']){
                        $tf_email_key = "Infant";
                    }elseif("check_in_out_date"==$meta_data['key']){
                        $tf_email_key = "Check In & Check Out";
                    }else{
                        $tf_email_key = $meta_data['key'];
                    }
                    $booking_details .= '<br><strong>' . $tf_email_key . '</strong>: ' . $meta_data['value'];
                }
            }

            $booking_details .= '</td>';
            $booking_details .= '<td align="center">' . $item['item_quantity'] . '</td>';
            $booking_details .= '<td align="right"><b>' . wc_price( $item['item_subtotal'] ) . '</b></td>';
            $booking_details .= '</tr>';

        }
        $booking_details .= '</tbody>';
        $booking_details .= '<tfoot><tr><th colspan="2" align="left" style="padding-bottom:10px;padding-top:10px;">Sub-Total</th>';
        $booking_details .= '<td align="right"><b>' . wc_price( $order_subtotal ) . '</b></td></tr>';
        //payment method
        $booking_details .= '<tr style="border-bottom: 1px solid #D9D9D9;"><th colspan="2" align="left" style="padding-bottom:10px">Payment Method</th>';
        $booking_details .= '<td align="right"><b>' . $payment_method_title . '</b></td></tr>';
        //Tax
        if(!empty($taxs_summations)){
            $booking_details .= '<tr style="border-bottom: 1px solid #D9D9D9;"><th colspan="2" align="left" style="padding-bottom:10px">Tax</th>';
            $booking_details .= '<td align="right"><b>' . wc_price($taxs_summations) . '</b></td></tr>';
        }
        //total
        $booking_details .= '<tr><th colspan="2" align="left" style="padding-bottom:10px; font-weight: 900;">Total Amount</th>';
        $booking_details .= '<td align="right"><b style="font-weight: 900;">' . wc_price( $order_total ) . '</b></td></tr>';
        $booking_details .= '</tfoot>';

        $booking_details .= '</table>';
        //booking details end

        //customer details
        $customer_details = '<table style="max-width: 600px;border-collapse: collapse; color: #5A5A5A; font-family: Inter,sans-serif;"><tbody><tr><td style="padding: 15px 0;text-align: left;">';
        $customer_details .= '<strong>Customer Name:</strong> ' . $order_billing_name . '<br>';
        $customer_details .= '<strong>Customer Address:</strong> ' . $order_billing_address . '<br>';
        $customer_details .= '<strong>Customer Email:</strong> ' . $order_billing_email . '<br>';
        $customer_details .= '<strong>Customer Phone:</strong> ' . $order_billing_phone . '<br>';
        $customer_details .= '<strong>Customer City:</strong> ' . $order_billing_city . '<br>';
        $customer_details .= '<strong>Customer Country:</strong> ' . $order_billing_country . '<br>';
        $customer_details .= '<strong>Customer Postcode:</strong> ' . $order_billing_postcode . '<br>';
        $customer_details .= '</td></tr></tbody></table>';
        //customer details end

        // QR Code PDF Downloader Button

        $tf_order_id = get_option('tf_order_uni_'.$order_id );

        $tf_ticket_download = '';
        if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
            if(!empty($tf_order_id)){
                $tf_order = wc_get_order( $order_id );
                if(!empty($tf_order)){
                    foreach ( $tf_order->get_items() as $item_id => $item ) {
                    $order_type = $item->get_meta( '_order_type', true );
                    $tour_ides = $item->get_meta( '_tour_unique_id', true );
                        if("tour"==$order_type){
                            $tf_tour_id   = $item->get_meta( '_tour_id', true );
                            $tf_ticket_download .= '<table width="100%" style="margin: 10px 0;font-family: Inter,sans-serif;"><tr><td style="padding-bottom:10px;padding-top:10px;"><a href="'. get_bloginfo('url').'?qr_id='.$tour_ides.'" target="_blank" style="display: inline-block; padding: 10px 15px; background-color: #0209AF; color: #fff; text-decoration: none;">Download Voucher '.get_the_title( $tf_tour_id ).'</a><tr><td></table>';
                        }
                    }
                }
            }
        }

        $replacements = array(
            '{booking_id}'       => $order_id,
            '{booking_url}'      => $order_url,
            '{booking_details}'  => $booking_details,
            '{fullname}'         => $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'],
            '{user_email}'       => $order_data['billing']['email'],
            '{billing_address}'  => $order_data['billing']['address_1'] . ' ' . $order_data['billing']['address_2'],
            '{city}'             => $order_data['billing']['city'],
            '{billing_state}'    => $order_data['billing']['state'],
            '{billing_zip}'      => $order_data['billing']['postcode'],
            '{country}'          => $order_data['billing']['country'],
            '{phone}'            => $order_data['billing']['phone'],
            '{payment_method}'   => $order_data['payment_method_title'],
            '{order_total}'      => wc_price($order_total),
            '{order_subtotal}'   => wc_price($order_subtotal),
            '{order_date}'       => $order_date_created,
            '{order_status}'     => $order_status,
            '{site_name}'        => get_bloginfo( 'name' ),
            '{site_url}'         => get_bloginfo( 'url' ),
            '{tour_voucher_downloader}' => $tf_ticket_download,
        );

        $tags = array_keys($replacements);
        $values = array_values($replacements);

        return str_replace( $tags, $values, $template );
    }

    /**
     * Get email template
     * @param string $template_type
     * @param string $template
     * @param string $sendto
     * @since 2.3.0
     *
     */
    public static function get_email_template( $template_type = 'order', $template = '', $sendto = 'admin' ) {
        $email_settings = self::$tf_email_settings;
        $templates      = array(
            'order'              => array(
                'admin'    => !empty( $email_settings['admin_booking_email_template'] ) ? $email_settings['admin_booking_email_template'] : '',
                'customer' => !empty( $email_settings['customer_booking_email_template'] ) ? $email_settings['customer_booking_email_template'] : '',
            ),
            'order_confirmation' => array(
                'admin'    => !empty( $email_settings['admin_confirmation_email_template'] ) ? $email_settings['admin_confirmation_email_template'] : '',
                'customer' => !empty( $email_settings['customer_confirmation_email_template'] ) ? $email_settings['customer_confirmation_email_template'] : '',
            ),
            'cancellation'      => array(
                'admin'    => !empty( $email_settings['admin_cancellation_email_template'] ) ? $email_settings['admin_cancellation_email_template'] : '',
                'customer' => !empty( $email_settings['customer_cancellation_email_template'] ) ? $email_settings['customer_cancellation_email_template'] : '',
            ),
        );

        $content = !empty( $templates[$template_type][$sendto] ) ? $templates[$template_type][$sendto] : '';

        if ( !empty( $content ) ) {
            return $content;
        }
        if ( empty( $template ) ) {
            switch ( $template_type ) {
            case 'order':
                $template = 'booking/notification.php';
                break;
            case 'order_confirmation':
                $template = 'booking/confirmation.php';
                break;
            case 'cancellation':
                $template = 'booking/cancellation.php';
                break;
            default:
                $template = 'booking/notification.php';
                break;
            }
        }

        $args = array(
            'send_to' => $sendto,
            'strings' => self::get_emails_strings( $template_type, $sendto ),
        );

        //include email template
        $template_path = TF_EMAIL_TEMPLATES_PATH . $template;
        ob_start();
        include $template_path;
        $template = ob_get_clean();
        return $template;

    }

    //method get strings
    public static function get_emails_strings( $template_type, $sendto = 'admin', $string = 'heading' ) {
        $strings = apply_filters(
            'tf_email_strings',
            array(
                'order'              => array(
                    'admin'    => array(
                        'heading'         => __( 'New Order Received', 'tourfic' ),
                        'greeting'        => __( 'Dear Admin,', 'tourfic' ),
                        'greeting_byline' => __( 'A new booking has been made on your website. Booking details are listed below.', 'tourfic' ),
                    ),
                    'vendor'   => array(
                        'heading'         => __( 'New Order Received', 'tourfic' ),
                        'greeting'        => __( 'Dear Vendor,', 'tourfic' ),
                        'greeting_byline' => __( 'A new booking has been made on your website. Booking details are listed below.', 'tourfic' ),
                    ),
                    'customer' => array(
                        'heading'         => __( 'Booking Confirmation', 'tourfic' ),
                        'greeting'        => __( 'Dear Customer,', 'tourfic' ),
                        'greeting_byline' => __( 'A new booking has been made on your website. Booking details are listed below.', 'tourfic' ),

                    ),
                ),
                'order_confirmation' => array(
                    'admin'    => array(
                        'heading'         => __( 'A Payment has been received for #{booking_id}', 'tourfic' ),
                        'greeting'        => __( 'Dear Admin,', 'tourfic' ),
                        'greeting_byline' => __( 'A payment has been received for #{booking_id}. The payment details are listed below.', 'tourfic' ),
                    ),
                    'vendor'   => array(
                        'heading'         => __( 'A Payment has been received for #{booking_id}', 'tourfic' ),
                        'greeting'        => __( 'Dear Vendor,', 'tourfic' ),
                        'greeting_byline' => __( 'A payment has been received for #{booking_id}. The payment details are listed below.', 'tourfic' ),
                    ),
                    'customer' => array(
                        'heading'         => __( 'Your booking has been confirmed.', 'tourfic' ),
                        'greeting'        => __( 'Dear {fullname},', 'tourfic' ),
                        'greeting_byline' => __( 'Your booking has been confirmed. Your booking and payment information is listed below.', 'tourfic' ),
                    ),
                ),
                'cancellation'  => array(
                    'admin'    => array(
                        'heading'         => __( 'A booking has been cancelled', 'tourfic' ),
                        'greeting'        => __( 'Dear Admin,', 'tourfic' ),
                        'greeting_byline' => __( 'A booking has been cancelled. The booking details are listed below.', 'tourfic' ),
                    ),
                    'vendor'   => array(
                        'heading'         => __( 'A booking has been cancelled', 'tourfic' ),
                        'greeting'        => __( 'Dear Vendor,', 'tourfic' ),
                        'greeting_byline' => __( 'A booking has been cancelled. The booking details are listed below.', 'tourfic' ),
                    ),
                    'customer' => array(
                        'heading'         => __( 'Your booking has been cancelled.', 'tourfic' ),
                        'greeting'        => __( 'Dear {fullname},', 'tourfic' ),
                        'greeting_byline' => __( 'Your booking has been cancelled. Your booking and payment information is listed below.', 'tourfic' ),
                    ),
                ),

            ),
        );
        if ( isset( $strings[$template_type][$sendto][$string] ) ) {
            return $strings[$template_type][$sendto][$string];
        }
        return false;

    }
    public static function tf_send_attachment() {
        $email_settings = self::$tf_email_settings;
        $brand_logo     = !empty( $email_settings['brand_logo'] ) ? $email_settings['brand_logo'] : '';
        if ( !empty( $brand_logo ) ) {
            $logo_id = attachment_url_to_postid( $brand_logo );

            $brand_logo_path = get_attached_file( $logo_id ); //phpmailer will load this file
            $uid             = 'logo-uid'; //will map it to this UID
            global $phpmailer;
            $phpmailer->AddEmbeddedImage( $brand_logo_path, $uid );
        } else {
            return;
        }

    }
    /**
     * Get Vendor Emails
     * 
     * @param int $order_id
     * 
     * @return array
     */
    public function tf_get_vendor_emails( $order_id ) {
        $order         = wc_get_order( $order_id );
        $order_items   = $order->get_items();
        $vendor_emails = array();
    
        foreach ( $order_items as $item_id => $item ) {
            $meta_data = $item->get_meta_data();
            foreach ( $meta_data as $meta ) {
                if ( $meta->key == '_post_author' ) {
                    $vendor_id       = $meta->value;
                    $vendor          = get_userdata( $vendor_id );
                    $vendor_emails[] = $vendor->user_email;
                }
            }
        }
    
        return $vendor_emails;
    }
    
    /**
     * Send Email
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function send_email( $order_id ) {
        if( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ){
            return;
        }
        //get order details
        $order                   = wc_get_order( $order_id );
        $order_billing_email     = $order->get_billing_email();
        $email_settings          = self::$tf_email_settings;
        $order_email_heading     = !empty( $email_settings['order_email_heading'] ) ? $email_settings['order_email_heading'] : __( 'Your order received' , 'tourfic' );
        $brand_logo              = !empty( $email_settings['brand_logo'] ) ? $email_settings['brand_logo'] : '';
        $email_heading_bg        = !empty( $email_settings['email_heading_bg'] ) ? $email_settings['email_heading_bg']['bg_color'] : '#0209AF';
        $send_notifcation        = !empty( $email_settings['send_notification'] ) ? $email_settings['send_notification'] : '';
        $sale_notification_email = !empty( $email_settings['sale_notification_email'] ) ? $email_settings['sale_notification_email'] : get_bloginfo( 'admin_email' );
        $admin_email_disable     = !empty( $email_settings['admin_email_disable'] ) ? $email_settings['admin_email_disable'] : false;
        $admin_email_subject     = !empty( $email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] . " # " . $order_id :  __( 'Your email subject','tourfic' );
        $email_from_name         = !empty( $email_settings['email_from_name'] ) ? $email_settings['email_from_name'] : get_bloginfo( 'name' );
        $email_from_email        = !empty( $email_settings['email_from_email'] ) ? $email_settings['email_from_email'] : get_bloginfo( 'admin_email' );
        $email_content_type      = !empty( $email_settings['email_content_type'] ) ? $email_settings['email_content_type'] : 'text/html';

        //mail headers
        $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
        $headers  = $charset . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
        $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        //email body started
        $email_body_open = $this->email_body_open( $brand_logo, $order_email_heading, $email_heading_bg );

        $email_body_open               = str_replace( '{booking_id}', $order_id, $email_body_open );
        $admin_booking_email_template  = !empty( $email_settings['admin_booking_email_template'] ) ? $email_settings['admin_booking_email_template'] : $this->get_email_template( 'order_confirmation', '', 'admin');
        $vendor_booking_email_template = !empty( $email_settings['vendor_booking_email_template'] ) ? $email_settings['vendor_booking_email_template'] : $this->get_email_template( 'order_confirmation', '', 'vendor');
       
        //replace mail tags
        $admin_booking_email_template = $this->replace_mail_tags( $admin_booking_email_template , $order_id );
        //email body ended
        $email_body_close  = $this->email_body_close();
        $admin_email_booking_body_full = $email_body_open . $admin_booking_email_template . $email_body_close;

        //check if admin emails disable
        if ( isset( $admin_email_disable ) && $admin_email_disable == false ) {
            if ( !empty( $admin_booking_email_template ) ) {
                //send multiple emails to multiple admins
                if ( strpos( $sale_notification_email, ',' ) !== false ) {
                    $sale_notification_email = explode( ',', $sale_notification_email );
                    $sale_notification_email = str_replace( ' ', '', $sale_notification_email );
                    foreach ( $sale_notification_email as $key => $email_address ) {
                        wp_mail( $email_address, $admin_email_subject, wp_kses_post($admin_email_booking_body_full), $headers );
                    }
                } else {
                    //send admin email
                    wp_mail( $sale_notification_email, $admin_email_subject, wp_kses_post($admin_email_booking_body_full), $headers );

                }
            } else {
                //send static default mail
                $default_mail = '<p>' . __( 'Dear Admin', 'tourfic' ) . '</p></br>';
                $default_mail .= '<p>' . __( 'You have received a new booking. The details are as follows:', 'tourfic' ) . '</p></br>';
                $default_mail .= __( '{booking_details}', 'tourfic' ) . '</br>';
                $default_mail .= '<strong>' . __( 'Customer details', 'tourfic' ) . '</strong>' . '</br>';
                $default_mail .= __( '{customer_details}', 'tourfic' ) . '</br>';
                $default_mail .= '<p>' . __( 'Thank you', 'tourfic' ) . '</p>';
                $default_mail .= __( 'Regards', 'tourfic' ) . '</br>';
                $default_mail .= __( '{site_name}', 'tourfic' ) . '</br>';

                $default_mail = $this->replace_mail_tags( $default_mail , $order_id );

                wp_mail( $sale_notification_email, $admin_email_subject, $default_mail, $headers );

            }
        }

        //send mail to vendor
        if ( !empty( $send_notifcation ) && $send_notifcation == 'admin_vendor' ) {

            $vendor_email_subject          = !empty( $email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] :  __( 'Your email subject','tourfic' );;
            $vendor_from_name              = !empty( $email_settings['vendor_from_name'] ) ? $email_settings['vendor_from_name'] : '';
            $vendor_from_email             = !empty( $email_settings['vendor_from_email'] ) ? $email_settings['vendor_from_email'] : '';
            $vendor_booking_email_template = !empty( $email_settings['vendor_booking_email_template'] ) ? $email_settings['vendor_booking_email_template'] : $this->get_email_template( 'order_confirmation', '', 'vendor');;

            //replace mail tags to actual value
            $vendor_booking_email_template  = $this->replace_mail_tags( $vendor_booking_email_template , $order_id );
            $vendor_email_booking_body_full = $email_body_open . $vendor_booking_email_template . $email_body_close;
            
            if ( !empty( $vendor_booking_email_template ) ) {
                //send mail to vendor
                $vendors_email = $this->tf_get_vendor_emails( $order_id );
                if ( !empty( $vendors_email ) ) {
                    foreach ( $vendors_email as $key => $vendor_email ) {
                        wp_mail( $vendor_email, $vendor_email_subject, wp_kses_post($vendor_email_booking_body_full), $headers );
                    }
                }
            } else {
                //send default mail
                $default_mail = '<p>' . __( 'Dear Admin', 'tourfic' ) . '</p></br>';
                $default_mail .= '<p>' . __( 'You have received a new booking. The details are as follows:', 'tourfic' ) . '</p></br>';
                $default_mail .= __( '{booking_details}', 'tourfic' ) . '</br>';
                $default_mail .=  '<strong>' . __( 'Customer details', 'tourfic' ) . '</strong>' . '</br>';
                $default_mail .= __( '{customer_details}', 'tourfic' ) . '</br>';
                $default_mail .= __( 'Thank you', 'tourfic' ) . '</br>';
                $default_mail .= __( 'Regards', 'tourfic' ) . '</br>';
                $default_mail .= __( '{site_name}', 'tourfic' ) . '</br>';

                $default_mail = $this->replace_mail_tags( $default_mail , $order_id );
                $vendors_email = $this->tf_get_vendor_emails( $order_id );
                if ( !empty( $vendors_email ) ) {
                    foreach ( $vendors_email as $key => $vendor_email ) {
                        wp_mail( $vendor_email, $vendor_email_subject, $default_mail, $headers );
                    }
                }
            }

        }
        //customer email settings
        $customer_email_address          = $order_billing_email;
        $disable_customer_email          = !empty( $email_settings['customer_email_disable'] ) ? $email_settings['customer_email_disable'] : false;
        $customer_email_subject          = !empty( $email_settings['customer_confirm_email_subject'] ) ? $email_settings['customer_confirm_email_subject'] :  __( 'Your email subject','tourfic' );;
        $customer_email_subject          = str_replace( '{booking_id}', $order_id, $customer_email_subject );
        $customer_from_name              = !empty( $email_settings['customer_from_name'] ) ? $email_settings['customer_from_name'] : '';
        $customer_from_email             = !empty( $email_settings['customer_from_email'] ) ? $email_settings['customer_from_email'] : '';
        $customer_confirm_email_template = !empty( $email_settings['customer_confirm_email_template'] ) ? $email_settings['customer_confirm_email_template'] : $this->get_email_template( 'order_confirmation', '', 'customer');;
        $headers .= "From: {$customer_from_name} <{$customer_from_email}>" . "\r\n";
        //send mail to customer
        if ( $disable_customer_email == false ) {
            if ( !empty( $customer_confirm_email_template ) ) {
                //replace mail tags to actual value
                $customer_confirm_email_template = $this->replace_mail_tags( $customer_confirm_email_template , $order_id );

                $customer_email_body_full = $email_body_open . $customer_confirm_email_template . $email_body_close;
                //send mail in plain text and html conditionally
                if ( $email_content_type == 'text/plain' ) {
                    $customer_email_body_full = wp_strip_all_tags( $customer_email_body_full );
                } else {
                    $customer_email_body_full = wp_kses_post( $customer_email_body_full );
                }
                wp_mail( $customer_email_address, $customer_email_subject, $customer_email_body_full, $headers );
            } else {
                //send default mail
                $default_mail = '<p>' . __( 'Dear', 'tourfic' ) . ' {fullname}</p></br>';
                $default_mail .= '<p>' . __( 'Thank you for your booking. The details are as follows:', 'tourfic' ) . '</p></br>';
                $default_mail .= __( '{booking_details}', 'tourfic' ) . '</br>';
                $default_mail .= '<strong>' .__( 'Shipping Details', 'tourfic' ) . '</strong>' . '</br>';
                $default_mail .= __( '{customer_details}', 'tourfic' ) . '</br>';
                $default_mail .= __( 'Thank you', 'tourfic' ) . '</br>';
                $default_mail .= __( 'Regards', 'tourfic' ) . '</br>';
                $default_mail .= __( '{site_name}', 'tourfic' ) . '</br>';

                $default_mail = $this->replace_mail_tags( $default_mail , $order_id );

                wp_mail( $customer_email_address, $customer_email_subject, $default_mail, $headers );
            }
        }
    }

    /**
     * Send email when order status is confirmed
     * @param  [int] $order_id [pass the order id]
     *
     */
    public function send_confirmation_email_pro( $order_id ){
        if( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) :
            //get order details
            $order = wc_get_order( $order_id );
            //get customer email
            $order_billing_email    = $order->get_billing_email();

            //email body ended
            $email_template_settings           = $this::$tf_email_template_settings;
            $enable_admin_conf_email           = !empty( $email_template_settings['enable_admin_conf_email'] ) ? $email_template_settings['enable_admin_conf_email'] : '';
            $enable_vendor_conf_email          = !empty( $email_template_settings['enable_vendor_conf_email'] ) ? $email_template_settings['enable_vendor_conf_email'] : '';
            $enable_customer_conf_email        = !empty( $email_template_settings['enable_customer_conf_email'] ) ? $email_template_settings['enable_customer_conf_email'] : '';
            $admin_confirmation_template_id    = !empty( $email_template_settings['admin_confirmation_email_template'] ) ? $email_template_settings['admin_confirmation_email_template'] : '';
            $vendor_confirmation_template_id   = !empty( $email_template_settings['vendor_confirmation_email_template'] ) ? $email_template_settings['vendor_confirmation_email_template'] : '';
            $customer_confirmation_template_id = !empty( $email_template_settings['customer_confirmation_email_template'] ) ? $email_template_settings['customer_confirmation_email_template'] : '';
        

            if( ! empty ( $enable_admin_conf_email ) && $enable_admin_conf_email == 1 ){
                //email settings metabox value
                if( ! empty ( $admin_confirmation_template_id ) ){

                    //get the mail template content   
                    $admin_confirmation_email_template   = get_post( $admin_confirmation_template_id );
                    $admin_confirmation_template_content = !empty( $admin_confirmation_email_template->post_content ) ? $admin_confirmation_email_template->post_content : $this->get_email_template( 'order_confirmation', '', 'admin' );
                    $admin_confirmation_template_content = $this->replace_mail_tags( $admin_confirmation_template_content, $order_id );
                    
                    $meta                    = get_post_meta( $admin_confirmation_template_id, 'tf_email_templates_metabox', true );
                    $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                    $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                    $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order confirmed', 'tourfic-pro' );;
                    $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                    $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                    $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                    $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                    $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                   
                    //mail headers
                    $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                    $headers  = $charset . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    //email body open
                    $email_body_open                     = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                    $email_body_open                     = str_replace( '{booking_id}', $order_id, $email_body_open );
                    $admin_confirmation_template_content = $this->replace_mail_tags( $admin_confirmation_template_content, $order_id );
                    $email_body_close                    = $this->email_body_close();
                    $admin_email_booking_body_full       = $email_body_open . $admin_confirmation_template_content . $email_body_close;

                    //send multiple emails to multiple admins
                    if ( strpos( $sale_notification_email, ',' ) !== false ) {
                        $sale_notification_email = explode( ',', $sale_notification_email );
                        $sale_notification_email = str_replace( ' ', '', $sale_notification_email );
                        foreach ( $sale_notification_email as $key => $email_address ) {
                            wp_mail( $email_address, $email_subject, $admin_email_booking_body_full, $headers );
                        }
                    } else {
                        //send admin email
                        wp_mail( $sale_notification_email, $email_subject, $admin_email_booking_body_full, $headers );

                    }
                } 
            }
           
            if( function_exists( 'is_tf_pro' ) && is_tf_pro() ){
                //send vendor confirmation email template
                if( ! empty ( $enable_vendor_conf_email ) && $enable_vendor_conf_email == 1 ){
                    //email settings metabox value
                    if( ! empty ( $vendor_confirmation_template_id ) ){
                        //get the mail template content   
                        $vendor_confirmation_email_template   = get_post( $vendor_confirmation_template_id );
                        $vendor_confirmation_template_content = !empty( $vendor_confirmation_email_template->post_content ) ? $vendor_confirmation_email_template->post_content : ' ';
                        $vendor_confirmation_template_content = $this->replace_mail_tags( $vendor_confirmation_template_content, $order_id );
                        
                        $meta                    = get_post_meta( $vendor_confirmation_template_id, 'tf_email_templates_metabox', true );
                        $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                        $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                        $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order confirmed', 'tourfic-pro' );;
                        $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                        $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                        $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                        $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                        $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                        //mail headers
                        $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                        $headers  = $charset . "\r\n";
                        $headers .= "MIME-Version: 1.0" . "\r\n";
                        $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                        //email body open
                        $email_body_open                      = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg );
                        $email_body_open                      = str_replace( '{booking_id}', $order_id, $email_body_open );
                        $vendor_confirmation_template_content = $this->replace_mail_tags( $vendor_confirmation_template_content, $order_id );
                        $email_body_close                     = $this->email_body_close();
                        $vendor_email_booking_body_full       = $email_body_open . $vendor_confirmation_template_content . $email_body_close;
                        $vendors_email  = $this->tf_get_vendor_emails( $order_id );
                        //send mail to vendor
                        if ( !empty( $vendors_email ) ) {
                            foreach ( $vendors_email as $key => $vendor_email ) {
                               //get user role by email
                                $user = get_user_by( 'email', $vendor_email );
                                $user_role = !empty( $user->roles[0] ) ? $user->roles[0] : '';
                                //check if user role is vendor
                                if( $user_role == 'tf_vendor' ){
                                    wp_mail( $vendor_email, $email_subject, $vendor_email_booking_body_full, $headers );
                                }
                               
                            }
                        }
                    }
                }
            }
            //send customer confirmation email template
            if( ! empty ( $enable_customer_conf_email ) && $enable_customer_conf_email == 1 ){
                //email settings metabox value
                if( ! empty ( $customer_confirmation_template_id ) ){
                    //echo "hels";
                    //get the mail template content   
                    $customer_confirmation_email_template   = get_post( $customer_confirmation_template_id );
                    $customer_confirmation_template_content = !empty( $customer_confirmation_email_template->post_content ) ? $customer_confirmation_email_template->post_content : $this->get_email_template( 'order_confirmation','', 'customer');
                    $customer_confirmation_template_content = $this->replace_mail_tags( $customer_confirmation_template_content, $order_id );
                    $meta                    = get_post_meta( $customer_confirmation_template_id, 'tf_email_templates_metabox', true );
                    $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                    $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                    $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] : __( 'Your order received', 'tourfic-pro' );
                    $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                    $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                    $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                    $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                    $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                    //mail headers
                    $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                    $headers  = $charset . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    //email body open
                    $email_body_open                        = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                    $email_body_open                        = str_replace( '{booking_id}', $order_id, $email_body_open );
                    $customer_confirmation_template_content = $this->replace_mail_tags( $customer_confirmation_template_content, $order_id );
                    $email_body_close                       = $this->email_body_close();
                    $customer_email_booking_body_full       = $email_body_open . $customer_confirmation_template_content . $email_body_close;
                    
                    //send mail to customer
                    wp_mail( $order_billing_email, $email_subject, $customer_email_booking_body_full, $headers );
                }
            }
               
        endif;
        
    }

    /**
     * Send mail when order cancelled
     * @param  int $order_id
     * @return void
     */
    public function send_cancellation_email_pro( $order_id ){
        if( function_exists( 'is_tf_pro' ) && is_tf_pro() ):
            //get order details
            $order = wc_get_order( $order_id );
            //get customer email
            $order_billing_email    = $order->get_billing_email();

            //email body ended
            $email_template_settings           = $this::$tf_email_template_settings;
            $enable_admin_canc_email           = ! empty( $email_template_settings['enable_admin_canc_email'] ) ? $email_template_settings['enable_admin_canc_email'] : '';
            $enable_vendor_canc_email          = ! empty( $email_template_settings['enable_vendor_canc_email'] ) ? $email_template_settings['enable_vendor_canc_email'] : '';
            $enable_customer_canc_email        = ! empty( $email_template_settings['enable_customer_canc_email'] ) ? $email_template_settings['enable_customer_canc_email'] : '';
            $admin_cancellation_template_id    = ! empty( $email_template_settings['admin_cancellation_email_template'] ) ? $email_template_settings['admin_cancellation_email_template'] : '';
            $vendor_cancellation_template_id   = ! empty( $email_template_settings['vendor_cancellation_email_template'] ) ? $email_template_settings['vendor_cancellation_email_template'] : '';
            $customer_cancellation_template_id = ! empty( $email_template_settings['customer_cancellation_email_template'] ) ? $email_template_settings['customer_cancellation_email_template'] : '';
            //send admin cancellation email template
            if( ! empty ( $enable_admin_canc_email ) && $enable_admin_canc_email == 1 ){
                //email settings metabox value
                if( ! empty ( $admin_cancellation_template_id ) ){
                    //get the mail template content   
                    $admin_cancellation_email_template   = get_post( $admin_cancellation_template_id );
                    $admin_cancellation_template_content = !empty( $admin_cancellation_email_template->post_content ) ? $admin_cancellation_email_template->post_content : $this->get_email_template( 'cancellation', '', 'admin' );
                    $admin_cancellation_template_content = $this->replace_mail_tags( $admin_cancellation_template_content, $order_id );
                    
                    $meta                    = get_post_meta( $admin_cancellation_template_id, 'tf_email_templates_metabox', true );
                    $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                    $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : get_bloginfo( 'admin_email' );
                    $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order cancelled', 'tourfic-pro' );
                    $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                    $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                    $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                    $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                    $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                    
                    //mail headers
                    $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                    $headers  = $charset . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    
                    //email body open
                    $email_body_open                     = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                    $email_body_open                     = str_replace( '{booking_id}', $order_id, $email_body_open );
                    $admin_cancellation_template_content = $this->replace_mail_tags( $admin_cancellation_template_content, $order_id );
                    $email_body_close                    = $this->email_body_close();
                    $admin_email_cancellation_body_full  = $email_body_open . $admin_cancellation_template_content . $email_body_close;
                    
                    //send mail to admin
                    wp_mail( $sale_notification_email, $email_subject, $admin_email_cancellation_body_full, $headers );            
                }
            }
            //send vendor cancellation email template
            if( ! empty ( $enable_vendor_canc_email ) && $enable_vendor_canc_email == 1 ){
                //email settings metabox value
                if( ! empty ( $vendor_cancellation_template_id ) ){
                    //get the mail template content   
                    $vendor_cancellation_email_template   = get_post( $vendor_cancellation_template_id );
                    $vendor_cancellation_template_content = !empty( $vendor_cancellation_email_template->post_content ) ? $vendor_cancellation_email_template->post_content : $this->get_email_template( 'cancellation','','vendor' );
                    $vendor_cancellation_template_content = $this->replace_mail_tags( $vendor_cancellation_template_content, $order_id );
                    
                    $meta                    = get_post_meta( $vendor_cancellation_template_id, 'tf_email_templates_metabox', true );
                    $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                    $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                    $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order cancelled', 'tourfic-pro' );
                    $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                    $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                    $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                    $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                    $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                    
                    //mail headers
                    $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                    $headers  = $charset . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    
                    //email body open
                    $email_body_open                      = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg );
                    $email_body_open                      = str_replace( '{booking_id}', $order_id, $email_body_open );
                    $vendor_cancellation_template_content = $this->replace_mail_tags( $vendor_cancellation_template_content, $order_id );
                    $email_body_close                     = $this->email_body_close();
                    $vendor_email_cancellation_body_full  = $email_body_open . $vendor_cancellation_template_content . $email_body_close;   //send mail to vendor
                    $vendors_email                        = $this->tf_get_vendor_emails( $order_id );
                    foreach( $vendors_email as $key => $vendor_email ){
                        wp_mail( $vendor_email, $email_subject, $vendor_email_cancellation_body_full, $headers );
                    }
                }
            }
            //send customer cancellation email template
            if( ! empty( $enable_customer_canc_email ) && $enable_customer_canc_email == 1 ){
                if( ! empty( $customer_cancellation_template_id )){
                    //get the mail template content   
                    $customer_cancellation_email_template   = get_post( $customer_cancellation_template_id );
                    $customer_cancellation_template_content = ! empty( $customer_cancellation_email_template->post_content ) ? $customer_cancellation_email_template->post_content : $this->get_email_template( 'cancellation','','customer' );
                    $customer_cancellation_template_content = $this->replace_mail_tags( $customer_cancellation_template_content, $order_id );
                   
                    
                    $meta                    = get_post_meta( $customer_cancellation_template_id, 'tf_email_templates_metabox', true );
                    $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                    $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : $order_billing_email;
                    $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order cancelled', 'tourfic-pro' );
                    $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                    $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                    $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                    $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : '';
                    $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                    
                    //mail headers
                    $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                    $headers  = $charset . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    
                    //email body open
                    $email_body_open                        = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                    $email_body_open                        = str_replace( '{booking_id}', $order_id, $email_body_open );
                    $customer_cancellation_template_content = $this->replace_mail_tags( $customer_cancellation_template_content, $order_id );
                    $email_body_close                       = $this->email_body_close();
                    $customer_email_cancellation_body_full  = $email_body_open . $customer_cancellation_template_content . $email_body_close;
                    //send mail to customer
                    wp_mail( $order_billing_email, $email_subject, $customer_email_cancellation_body_full, $headers );

                }
            }
        endif;

    }


    /**
     * Send email when offline payment tour booked
     * @author Jahid
     * @since 2.9.26
     *
     */
    public function tf_offline_booking_confirmation_callback( $order_id, $order_data ){
        if( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) :
 
            //get customer email
            $order_billing_email    = !empty($order_data['shipping_details']['tf_email']) ? $order_data['shipping_details']['tf_email'] : '';
            $order_items    = !empty($order_data['order_details']) ? $order_data['order_details'] : '';
            $booking_details = '<div style="padding-left: 40px; padding-right: 40px; margin: 0 auto; padding-top: 30px; border: 1px solid #ddd;">
            <h3 class="greeting" style="margin: 0; padding: 0; color: #5a5a5a; font-family: Work Sans,sans-serif; font-size: 24px;">Dear,</h3>
            <p style="margin: 10px 0;">A new booking has been made on your website. Booking details are listed below.</p>';
            $booking_details .= '<div class="order-table"><table width="100%" style="max-width: 600px;border-collapse: collapse; color: #5A5A5A;"><thead><tr><th align="left" style="color:#0209AF;">Item Name</th><th align="center" style="color:#0209AF;">Quantity</th><th align="right" style="color:#0209AF;">Price</th></tr></thead><tbody style="border-bottom: 2px solid #D9D9D9">';
            
            
            $booking_details .= '<tr>';
            $booking_details .= '<td style="padding: 15px 0;text-align: left;padding-top: 15px;padding-bottom: 15px;line-height: 1.7;">' . get_the_title( $order_data['post_id'] );
            
            if ( !empty($order_items['tour_date']) ) {
                $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Date:</strong> ' . $order_items['tour_date'];
            }
            if ( !empty($order_items['tour_time']) ) {
                $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Time:</strong> ' . $order_items['tour_time'];
            }
            if ( !empty($order_items['tour_extra']) ) {
                $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Extra:</strong> ' . $order_items['tour_extra'];
            }
            if ( !empty($order_items['adult']) ) {
                $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Adult:</strong> ' . $order_items['adult'];
            }
            if ( !empty($order_items['child']) ) {
                $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Child:</strong> ' . $order_items['child'];
            }
            if ( !empty($order_items['infants']) ) {
                $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Infants:</strong> ' . $order_items['infants'];
            }
            $booking_details .= '</td>';
            $booking_details .= '<td align="center">1</td>';
            $booking_details .= '<td align="right">' . wc_price( $order_items['total_price'] ) . '</td>';
            $booking_details .= '</tr>';

            
            $booking_details .= '</tbody>';
            $booking_details .= '<tfoot><tr><th colspan="2" align="left" style="padding-bottom:10px;padding-top:10px;">Subtotal</th>';
            $booking_details .= '<td align="right">' . wc_price( $order_items['total_price'] ) . '</td></tr>';
            //payment method
            $booking_details .= '<tr><th colspan="2" align="left" style="padding-bottom:10px">Payment Method</th>';
            $booking_details .= '<td align="right">' . $order_data['payment_method'] . '</td></tr>';
            //total
            $booking_details .= '<tr><th colspan="2" align="left" style="padding-bottom:10px">Total</th>';
            $booking_details .= '<td align="right">' . wc_price( $order_items['total_price'] ) . '</td></tr>';
            $booking_details .= '</tfoot>';

            $booking_details .= '</table></div>';
            //booking details end

            //customer details Start
            $tf_booking_fields = !empty(Helper::tfopt( 'book-confirm-field' )) ? Helper::tf_data_types(Helper::tfopt( 'book-confirm-field' )) : '';
            
            $booking_details .= '<table style="width: 100%; max-width: 600px; margin-top: 15px; margin-bottom: 15px; border: none;"><tbody><tr><td style="background-color: #f2f9fe; padding: 20px; float: left;"> <h3 style="font-size: 16px; font-weight: bold; color: #0209af; margin: 0;">Billing address</h3>';
            if(!empty($tf_booking_fields)){
                foreach($tf_booking_fields as $single){
                    if(!empty($single['reg-field-label']) && !empty($order_data['shipping_details'][$single['reg-field-name']])){
                        $booking_details .= '<strong>'.$single['reg-field-label'].':</strong> ' . $order_data['shipping_details'][$single['reg-field-name']] . '<br>';
                    }
                }
            }else{
                if(!empty($order_data['shipping_details']['tf_first_name'])){
                    $booking_details .= '<strong>First Name:</strong> ' . $order_data['shipping_details']['tf_first_name'] . '<br>';
                }
                if(!empty($order_data['shipping_details']['tf_last_name'])){
                    $booking_details .= '<strong>Last Name:</strong> ' . $order_data['shipping_details']['tf_last_name'] . '<br>';
                }
                if(!empty($order_data['shipping_details']['tf_email'])){
                    $booking_details .= '<strong>Email:</strong> ' . $order_data['shipping_details']['tf_email'] . '<br>';
                }
                if(!empty($order_data['shipping_details']['tf_phone'])){
                    $booking_details .= '<strong>Phone:</strong> ' . $order_data['shipping_details']['tf_phone'] . '<br>';
                }
                if(!empty($order_data['shipping_details']['tf_country'])){
                    $booking_details .= '<strong>Country:</strong> ' . $order_data['shipping_details']['tf_country'] . '<br>';
                }
                if(!empty($order_data['shipping_details']['tf_postcode'])){
                    $booking_details .= '<strong>Postcode/ZIP:</strong> ' . $order_data['shipping_details']['tf_postcode'] . '<br>';
                }
            }
            $booking_details .= '</td></tr></tbody></table><p style="margin:10px 0;">Thank you for booking.</p></div>';

            //customer details end
            
            //email body ended
            $email_template_settings           = $this::$tf_email_template_settings;
            $enable_admin_conf_email           = !empty( $email_template_settings['enable_admin_conf_email'] ) ? $email_template_settings['enable_admin_conf_email'] : '';
            $enable_vendor_conf_email          = !empty( $email_template_settings['enable_vendor_conf_email'] ) ? $email_template_settings['enable_vendor_conf_email'] : '';
            $enable_customer_conf_email        = !empty( $email_template_settings['enable_customer_conf_email'] ) ? $email_template_settings['enable_customer_conf_email'] : '';
            $admin_confirmation_template_id    = !empty( $email_template_settings['admin_confirmation_email_template'] ) ? $email_template_settings['admin_confirmation_email_template'] : '';
            $vendor_confirmation_template_id   = !empty( $email_template_settings['vendor_confirmation_email_template'] ) ? $email_template_settings['vendor_confirmation_email_template'] : '';
            $customer_confirmation_template_id = !empty( $email_template_settings['customer_confirmation_email_template'] ) ? $email_template_settings['customer_confirmation_email_template'] : '';
        

            if( ! empty ( $enable_admin_conf_email ) && $enable_admin_conf_email == 1 ){
                //email settings metabox value
                if( ! empty ( $admin_confirmation_template_id ) ){

                    //get the mail template content   
                    
                    $meta                    = get_post_meta( $admin_confirmation_template_id, 'tf_email_templates_metabox', true );
                    $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                    $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                    $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order confirmed', 'tourfic-pro' );;
                    $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                    $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                    $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                    $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                    $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                   
                    //mail headers
                    $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                    $headers  = $charset . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    //email body open
                    $email_body_open                     = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                    $email_body_open                     = str_replace( '{booking_id}', $order_id, $email_body_open );
                    $email_body_close                    = $this->email_body_close();
                    $admin_email_booking_body_full       = $email_body_open . $booking_details . $email_body_close;

                    //send multiple emails to multiple admins
                    if ( strpos( $sale_notification_email, ',' ) !== false ) {
                        $sale_notification_email = explode( ',', $sale_notification_email );
                        $sale_notification_email = str_replace( ' ', '', $sale_notification_email );
                        foreach ( $sale_notification_email as $key => $email_address ) {
                            wp_mail( $email_address, $email_subject, $admin_email_booking_body_full, $headers );
                        }
                    } else {
                        //send admin email
                        wp_mail( $sale_notification_email, $email_subject, $admin_email_booking_body_full, $headers );

                    }
                } 
            }
           
            if( function_exists( 'is_tf_pro' ) && is_tf_pro() ){
                //send vendor confirmation email template
                if( ! empty ( $enable_vendor_conf_email ) && $enable_vendor_conf_email == 1 ){
                    //email settings metabox value
                    if( ! empty ( $vendor_confirmation_template_id ) ){
                        //get the mail template content   
                        
                        $meta                    = get_post_meta( $vendor_confirmation_template_id, 'tf_email_templates_metabox', true );
                        $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                        $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                        $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order confirmed', 'tourfic-pro' );;
                        $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                        $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                        $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                        $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                        $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                        //mail headers
                        $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                        $headers  = $charset . "\r\n";
                        $headers .= "MIME-Version: 1.0" . "\r\n";
                        $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                        //email body open
                        $email_body_open                      = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg );
                        $email_body_open                      = str_replace( '{booking_id}', $order_id, $email_body_open );
                        $email_body_close                     = $this->email_body_close();
                        $vendor_email_booking_body_full       = $email_body_open . $booking_details . $email_body_close;

                        //send mail to vendor
                        $author_id = get_post_field ('post_author', $order_data['post_id']);
                        //get user role by id
                        $user = get_user_by( 'id', $author_id );
                        $user_role = !empty( $user->roles[0] ) ? $user->roles[0] : '';
                        //check if user role is vendor
                        if( $user_role == 'tf_vendor' ){
                            wp_mail( $user->user_email, $email_subject, $vendor_email_booking_body_full, $headers );
                        }
                               
                    }
                }
            }
            //send customer confirmation email template
            if( ! empty ( $enable_customer_conf_email ) && $enable_customer_conf_email == 1 ){
                //email settings metabox value
                if( ! empty ( $customer_confirmation_template_id ) ){

                    //get the mail template content   
                    $meta                    = get_post_meta( $customer_confirmation_template_id, 'tf_email_templates_metabox', true );
                    $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                    $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                    $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] : __( 'Your order received', 'tourfic-pro' );
                    $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                    $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                    $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                    $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                    $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                    //mail headers
                    $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                    $headers  = $charset . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    //email body open
                    $email_body_open                        = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                    $email_body_open                        = str_replace( '{booking_id}', $order_id, $email_body_open );
                    $email_body_close                       = $this->email_body_close();
                    $customer_email_booking_body_full       = $email_body_open . $booking_details . $email_body_close;
                    
                    //send mail to customer
                    wp_mail( $order_billing_email, $email_subject, $customer_email_booking_body_full, $headers );
                }
            }
               
        endif;
        
    }

    /**
     * Send email when order email resend
     * @author Jahid
     * @since 2.10.1
     *
     */
    public function tf_order_status_email_resend_function(){

        // Add nonce for security and authentication.
	    check_ajax_referer('updates', '_ajax_nonce');
        
        
        $tf_mail_type = !empty($_POST['status']) ? esc_attr( $_POST['status'] ) : '';
        $order_id = !empty($_POST['order_id']) ? esc_attr( $_POST['order_id'] ) : '';
        $db_id = !empty($_POST['id']) ? esc_attr( $_POST['id'] ) : '';

        global $wpdb;
        $tf_db_order = $wpdb->get_row( $wpdb->prepare( "SELECT id, billing_details, shipping_details, order_details, payment_method FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $db_id ) ) );
        
        // Offline Order Email
        if(!empty($tf_db_order) && "offline"==$tf_db_order->payment_method){

            if( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) {
 
                //get customer email
                $order_customer_data = json_decode($tf_db_order->shipping_details);
                $order_customer_bill = json_decode($tf_db_order->billing_details);
                $order_customer_billing_email = !empty($order_customer_bill->billing_email) ? $order_customer_bill->billing_email : "";
                $order_billing_email    = !empty($order_customer_data->tf_email) ? $order_customer_data->tf_email : $order_customer_billing_email;
                $order_items    = json_decode($tf_db_order->order_details);
                $booking_details = '<div style="padding-left: 40px; padding-right: 40px; margin: 0 auto; padding-top: 30px; border: 1px solid #ddd;">
                <h3 class="greeting" style="margin: 0; padding: 0; color: #5a5a5a; font-family: Work Sans,sans-serif; font-size: 24px;">Dear,</h3>
                <p style="margin: 10px 0;">A new booking has been made on your website. Booking details are listed below.</p>';
                $booking_details .= '<div class="order-table"><table width="100%" style="max-width: 600px;border-collapse: collapse; color: #5A5A5A;"><thead><tr><th align="left" style="color:#0209AF;">Item Name</th><th align="center" style="color:#0209AF;">Quantity</th><th align="right" style="color:#0209AF;">Price</th></tr></thead><tbody style="border-bottom: 2px solid #D9D9D9">';
                
                
                $booking_details .= '<tr>';
                $booking_details .= '<td style="padding: 15px 0;text-align: left;padding-top: 15px;padding-bottom: 15px;line-height: 1.7;">' . get_the_title( $tf_db_order->post_id );
                
                if ( !empty($order_items->tour_date) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Date:</strong> ' . $order_items->tour_date;
                }
                if ( !empty($order_items->tour_time) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Time:</strong> ' . $order_items->tour_time;
                }
                if ( !empty($order_items->tour_extra) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Extra:</strong> ' . $order_items->tour_extra;
                }
                if ( !empty($order_items->adult) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Adult:</strong> ' . $order_items->adult;
                }
                if ( !empty($order_items->child) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Child:</strong> ' . $order_items->child;
                }
                if ( !empty($order_items->infants) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Infants:</strong> ' . $order_items->infants;
                }
                $booking_details .= '</td>';
                $booking_details .= '<td align="center">1</td>';
                $booking_details .= '<td align="right">' . wc_price( $order_items->total_price ) . '</td>';
                $booking_details .= '</tr>';
    
                
                $booking_details .= '</tbody>';
                $booking_details .= '<tfoot><tr><th colspan="2" align="left" style="padding-bottom:10px;padding-top:10px;">Subtotal</th>';
                $booking_details .= '<td align="right">' . wc_price( $order_items->total_price ) . '</td></tr>';
                //payment method
                $booking_details .= '<tr><th colspan="2" align="left" style="padding-bottom:10px">Payment Method</th>';
                $booking_details .= '<td align="right">' . $tf_db_order->payment_method . '</td></tr>';
                //total
                $booking_details .= '<tr><th colspan="2" align="left" style="padding-bottom:10px">Total</th>';
                $booking_details .= '<td align="right">' . wc_price( $order_items->total_price ) . '</td></tr>';
                $booking_details .= '</tfoot>';
    
                $booking_details .= '</table></div>';
                
    
                //customer details Start
                $tf_booking_fields = !empty(Helper::tfopt( 'book-confirm-field' )) ? Helper::tf_data_types(Helper::tfopt( 'book-confirm-field' )) : '';
                
                $booking_details .= '<table style="width: 100%; max-width: 600px; margin-top: 15px; margin-bottom: 15px; border: none;"><tbody><tr><td style="background-color: #f2f9fe; padding: 20px; float: left;"> <h3 style="font-size: 16px; font-weight: bold; color: #0209af; margin: 0;">Billing address</h3>';
                if(!empty($tf_booking_fields)){
                    foreach($tf_booking_fields as $single){
                        $tf_details_field_key = $single['reg-field-name'];
                        if(!empty($single['reg-field-label']) && !empty($order_customer_data->$tf_details_field_key)){
                            $booking_details .= '<strong>'.$single['reg-field-label'].':</strong> ' . $order_customer_data->$tf_details_field_key . '<br>';
                        }
                    }
                }else{
                    if(!empty($order_customer_data->tf_first_name)){
                        $booking_details .= '<strong>First Name:</strong> ' . $order_customer_data->tf_first_name . '<br>';
                    }
                    if(!empty($order_customer_data->tf_last_name)){
                        $booking_details .= '<strong>Last Name:</strong> ' . $order_customer_data->tf_last_name . '<br>';
                    }
                    if(!empty($order_customer_data->tf_email)){
                        $booking_details .= '<strong>Email:</strong> ' . $order_customer_data->tf_email . '<br>';
                    }
                    if(!empty($order_customer_data->tf_phone)){
                        $booking_details .= '<strong>Phone:</strong> ' . $order_customer_data->tf_phone . '<br>';
                    }
                    if(!empty($order_customer_data->tf_country)){
                        $booking_details .= '<strong>Country:</strong> ' . $order_customer_data->tf_country . '<br>';
                    }
                    if(!empty($order_customer_data->tf_postcode)){
                        $booking_details .= '<strong>Postcode/ZIP:</strong> ' . $order_customer_data->tf_postcode . '<br>';
                    }
                }
                $booking_details .= '</td></tr></tbody></table><p style="margin:10px 0;">Thank you for booking.</p></div>';
                
                //email body ended
                $email_template_settings           = $this::$tf_email_template_settings;
                $vendor_confirmation_template_id   = !empty( $email_template_settings['vendor_confirmation_email_template'] ) ? $email_template_settings['vendor_confirmation_email_template'] : '';
                $customer_confirmation_template_id = !empty( $email_template_settings['customer_confirmation_email_template'] ) ? $email_template_settings['customer_confirmation_email_template'] : '';
            

                //send vendor confirmation email template
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "vendor" ){
                    //email settings metabox value
                    if( ! empty ( $vendor_confirmation_template_id ) ){
                        //get the mail template content   
                        $meta                    = get_post_meta( $vendor_confirmation_template_id, 'tf_email_templates_metabox', true );
                        $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                        $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order confirmed', 'tourfic-pro' );;
                        $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                        $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                        $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                        $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                        $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                        //mail headers
                        $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                        $headers  = $charset . "\r\n";
                        $headers .= "MIME-Version: 1.0" . "\r\n";
                        $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                        //email body open
                        $email_body_open                      = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg );
                        $email_body_open                      = str_replace( '{booking_id}', $order_id, $email_body_open );
                        $email_body_close                     = $this->email_body_close();
                        $vendor_email_booking_body_full       = $email_body_open . $booking_details . $email_body_close;

                        //send mail to vendor
                        $author_id = get_post_field ('post_author', $tf_db_order->post_id);
                        //get user role by id
                        $user = get_user_by( 'id', $author_id );
                        $user_role = !empty( $user->roles[0] ) ? $user->roles[0] : '';
                        //check if user role is vendor
                        if( $user_role == 'tf_vendor' ){
                            wp_mail( $user->user_email, $email_subject, $vendor_email_booking_body_full, $headers );
                        }
                                
                    }
                }

                //send customer confirmation email template
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "customer" ){
                    //email settings metabox value
                    if( ! empty ( $customer_confirmation_template_id ) ){
    
                        //get the mail template content   
                        $meta                    = get_post_meta( $customer_confirmation_template_id, 'tf_email_templates_metabox', true );
                        $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                        $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] : __( 'Your order received', 'tourfic-pro' );
                        $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                        $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                        $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                        $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                        $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                        //mail headers
                        $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                        $headers  = $charset . "\r\n";
                        $headers .= "MIME-Version: 1.0" . "\r\n";
                        $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                        //email body open
                        $email_body_open                        = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                        $email_body_open                        = str_replace( '{booking_id}', $order_id, $email_body_open );
                        $email_body_close                       = $this->email_body_close();
                        $customer_email_booking_body_full       = $email_body_open . $booking_details . $email_body_close;
                        
                        //send mail to customer
                        wp_mail( $order_billing_email, $email_subject, $customer_email_booking_body_full, $headers );
                    }
                }
                   
            }else{

                //get customer email
                $order_customer_data = json_decode($tf_db_order->shipping_details);
                $order_billing_email    = $order_customer_data->tf_email;
                $order_items    = json_decode($tf_db_order->order_details);
                $booking_details = '<div style="padding-left: 40px; padding-right: 40px; margin: 0 auto; padding-top: 30px; border: 1px solid #ddd;">
                <h3 class="greeting" style="margin: 0; padding: 0; color: #5a5a5a; font-family: Work Sans,sans-serif; font-size: 24px;">Dear,</h3>
                <p style="margin: 10px 0;">A new booking has been made on your website. Booking details are listed below.</p>';
                $booking_details .= '<div class="order-table"><table width="100%" style="max-width: 600px;border-collapse: collapse; color: #5A5A5A;"><thead><tr><th align="left" style="color:#0209AF;">Item Name</th><th align="center" style="color:#0209AF;">Quantity</th><th align="right" style="color:#0209AF;">Price</th></tr></thead><tbody style="border-bottom: 2px solid #D9D9D9">';
                
                
                $booking_details .= '<tr>';
                $booking_details .= '<td style="padding: 15px 0;text-align: left;padding-top: 15px;padding-bottom: 15px;line-height: 1.7;">' . get_the_title( $tf_db_order->post_id );
                
                if ( !empty($order_items->tour_date) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Date:</strong> ' . $order_items->tour_date;
                }
                if ( !empty($order_items->tour_time) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Time:</strong> ' . $order_items->tour_time;
                }
                if ( !empty($order_items->tour_extra) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Tour Extra:</strong> ' . $order_items->tour_extra;
                }
                if ( !empty($order_items->adult) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Adult:</strong> ' . $order_items->adult;
                }
                if ( !empty($order_items->child) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Child:</strong> ' . $order_items->child;
                }
                if ( !empty($order_items->infants) ) {
                    $booking_details .= '<br><strong style="font-family:Work Sans,sans-serif;">Infants:</strong> ' . $order_items->infants;
                }
                $booking_details .= '</td>';
                $booking_details .= '<td align="center">1</td>';
                $booking_details .= '<td align="right">' . wc_price( $order_items->total_price ) . '</td>';
                $booking_details .= '</tr>';
    
                
                $booking_details .= '</tbody>';
                $booking_details .= '<tfoot><tr><th colspan="2" align="left" style="padding-bottom:10px;padding-top:10px;">Subtotal</th>';
                $booking_details .= '<td align="right">' . wc_price( $order_items->total_price ) . '</td></tr>';
                //payment method
                $booking_details .= '<tr><th colspan="2" align="left" style="padding-bottom:10px">Payment Method</th>';
                $booking_details .= '<td align="right">' . $tf_db_order->payment_method . '</td></tr>';
                //total
                $booking_details .= '<tr><th colspan="2" align="left" style="padding-bottom:10px">Total</th>';
                $booking_details .= '<td align="right">' . wc_price( $order_items->total_price ) . '</td></tr>';
                $booking_details .= '</tfoot>';
    
                $booking_details .= '</table></div>';
                
    
                //customer details Start
                $tf_booking_fields = !empty(Helper::tfopt( 'book-confirm-field' )) ? Helper::tf_data_types(Helper::tfopt( 'book-confirm-field' )) : '';
                
                $booking_details .= '<table style="width: 100%; max-width: 600px; margin-top: 15px; margin-bottom: 15px; border: none;"><tbody><tr><td style="background-color: #f2f9fe; padding: 20px; float: left;"> <h3 style="font-size: 16px; font-weight: bold; color: #0209af; margin: 0;">Billing address</h3>';
                if(!empty($tf_booking_fields)){
                    foreach($tf_booking_fields as $single){
                        $tf_details_field_key = $single['reg-field-name'];
                        if(!empty($single['reg-field-label']) && !empty($order_customer_data->$tf_details_field_key)){
                            $booking_details .= '<strong>'.$single['reg-field-label'].':</strong> ' . $order_customer_data->$tf_details_field_key . '<br>';
                        }
                    }
                }else{
                    if(!empty($order_customer_data->tf_first_name)){
                        $booking_details .= '<strong>First Name:</strong> ' . $order_customer_data->tf_first_name . '<br>';
                    }
                    if(!empty($order_customer_data->tf_last_name)){
                        $booking_details .= '<strong>Last Name:</strong> ' . $order_customer_data->tf_last_name . '<br>';
                    }
                    if(!empty($order_customer_data->tf_email)){
                        $booking_details .= '<strong>Email:</strong> ' . $order_customer_data->tf_email . '<br>';
                    }
                    if(!empty($order_customer_data->tf_phone)){
                        $booking_details .= '<strong>Phone:</strong> ' . $order_customer_data->tf_phone . '<br>';
                    }
                    if(!empty($order_customer_data->tf_country)){
                        $booking_details .= '<strong>Country:</strong> ' . $order_customer_data->tf_country . '<br>';
                    }
                    if(!empty($order_customer_data->tf_postcode)){
                        $booking_details .= '<strong>Postcode/ZIP:</strong> ' . $order_customer_data->tf_postcode . '<br>';
                    }
                }
                $booking_details .= '</td></tr></tbody></table><p style="margin:10px 0;">Thank you for booking.</p></div>';


                //get order details
                
                $email_settings          = self::$tf_email_settings;
                $order_email_heading     = !empty( $email_settings['order_email_heading'] ) ? $email_settings['order_email_heading'] : __( 'Your order received' , 'tourfic' );
                $brand_logo              = !empty( $email_settings['brand_logo'] ) ? $email_settings['brand_logo'] : '';
                $email_heading_bg        = !empty( $email_settings['email_heading_bg'] ) ? $email_settings['email_heading_bg']['bg_color'] : '#0209AF';
                
                $email_from_name         = !empty( $email_settings['email_from_name'] ) ? $email_settings['email_from_name'] : get_bloginfo( 'name' );
                $email_from_email        = !empty( $email_settings['email_from_email'] ) ? $email_settings['email_from_email'] : get_bloginfo( 'admin_email' );
                $email_content_type      = !empty( $email_settings['email_content_type'] ) ? $email_settings['email_content_type'] : 'text/html';

                //mail headers
                $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                $headers  = $charset . "\r\n";
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

                //email body started
                $email_body_open = $this->email_body_open( $brand_logo, $order_email_heading, $email_heading_bg );
                $email_body_open               = str_replace( '{booking_id}', $order_id, $email_body_open );
            
                //email body ended
                $email_body_close  = $this->email_body_close();
                

                //send mail to vendor
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "vendor" ){

                    $vendor_email_subject          = !empty( $email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] :  __( 'Your email subject','tourfic' );;
                    $vendor_email_booking_body_full = $email_body_open . $booking_details . $email_body_close;
                    
                    //send mail to vendor

                    $author_id = get_post_field ('post_author', $tf_db_order->post_id);
                    //get user role by id
                    $user = get_user_by( 'id', $author_id );
                    $user_role = !empty( $user->roles[0] ) ? $user->roles[0] : '';
                    //check if user role is vendor
                    if( $user_role == 'tf_vendor' ){
                        wp_mail( $user->user_email, $email_subject, $vendor_email_booking_body_full, $headers );
                    }

                }

                //customer email settings
                $customer_email_address          = $order_billing_email;
                $customer_email_subject          = !empty( $email_settings['customer_confirm_email_subject'] ) ? $email_settings['customer_confirm_email_subject'] :  __( 'Your email subject','tourfic' );;
                $customer_email_subject          = str_replace( '{booking_id}', $order_id, $customer_email_subject );
                $customer_from_name              = !empty( $email_settings['customer_from_name'] ) ? $email_settings['customer_from_name'] : '';
                $customer_from_email             = !empty( $email_settings['customer_from_email'] ) ? $email_settings['customer_from_email'] : '';
                
                $headers .= "From: {$customer_from_name} <{$customer_from_email}>" . "\r\n";

                //send mail to customer
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "customer" ){

                    $customer_email_body_full = $email_body_open . $booking_details . $email_body_close;
                    //send mail in plain text and html conditionally
                    if ( $email_content_type == 'text/plain' ) {
                        $customer_email_body_full = wp_strip_all_tags( $customer_email_body_full );
                    } else {
                        $customer_email_body_full = wp_kses_post( $customer_email_body_full );
                    }
                    wp_mail( $customer_email_address, $customer_email_subject, $customer_email_body_full, $headers );
                }

            }
        }

        if(!empty($tf_db_order) && "offline"!=$tf_db_order->payment_method){
            if( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) {
                //get order details
                $order = wc_get_order( $order_id );
                //get customer email
                $order_billing_email    = $order->get_billing_email();

                //email body ended
                $email_template_settings           = $this::$tf_email_template_settings;
                $vendor_confirmation_template_id   = !empty( $email_template_settings['vendor_confirmation_email_template'] ) ? $email_template_settings['vendor_confirmation_email_template'] : '';
                $customer_confirmation_template_id = !empty( $email_template_settings['customer_confirmation_email_template'] ) ? $email_template_settings['customer_confirmation_email_template'] : '';
            

                //send vendor confirmation email template
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "vendor" ){
                    //email settings metabox value
                    if( ! empty ( $vendor_confirmation_template_id ) ){
                        //get the mail template content   
                        $vendor_confirmation_email_template   = get_post( $vendor_confirmation_template_id );
                        $vendor_confirmation_template_content = !empty( $vendor_confirmation_email_template->post_content ) ? $vendor_confirmation_email_template->post_content : ' ';
                        $vendor_confirmation_template_content = $this->replace_mail_tags( $vendor_confirmation_template_content, $order_id );
                        
                        $meta                    = get_post_meta( $vendor_confirmation_template_id, 'tf_email_templates_metabox', true );
                        $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                        $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] :  __( 'Your order confirmed', 'tourfic-pro' );;
                        $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                        $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                        $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                        $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                        $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                        //mail headers
                        $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                        $headers  = $charset . "\r\n";
                        $headers .= "MIME-Version: 1.0" . "\r\n";
                        $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                        //email body open
                        $email_body_open                      = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg );
                        $email_body_open                      = str_replace( '{booking_id}', $order_id, $email_body_open );
                        $vendor_confirmation_template_content = $this->replace_mail_tags( $vendor_confirmation_template_content, $order_id );
                        $email_body_close                     = $this->email_body_close();
                        $vendor_email_booking_body_full       = $email_body_open . $vendor_confirmation_template_content . $email_body_close;
                        $vendors_email  = $this->tf_get_vendor_emails( $order_id );
                        //send mail to vendor
                        if ( !empty( $vendors_email ) ) {
                            foreach ( $vendors_email as $key => $vendor_email ) {
                            //get user role by email
                                $user = get_user_by( 'email', $vendor_email );
                                $user_role = !empty( $user->roles[0] ) ? $user->roles[0] : '';
                                //check if user role is vendor
                                if( $user_role == 'tf_vendor' ){
                                    wp_mail( $vendor_email, $email_subject, $vendor_email_booking_body_full, $headers );
                                }
                            
                            }
                        }
                    }
                }
                
                //send customer confirmation email template
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "customer" ){
                    //email settings metabox value
                    if( ! empty ( $customer_confirmation_template_id ) ){
                        //echo "hels";
                        //get the mail template content   
                        $customer_confirmation_email_template   = get_post( $customer_confirmation_template_id );
                        $customer_confirmation_template_content = !empty( $customer_confirmation_email_template->post_content ) ? $customer_confirmation_email_template->post_content : $this->get_email_template( 'order_confirmation','', 'customer');
                        $customer_confirmation_template_content = $this->replace_mail_tags( $customer_confirmation_template_content, $order_id );
                        $meta                    = get_post_meta( $customer_confirmation_template_id, 'tf_email_templates_metabox', true );
                        $brand_logo              = ! empty( $meta['brand_logo'] ) ? $meta['brand_logo'] : '';
                        $sale_notification_email = ! empty( $meta['sale_notification_email'] ) ? $meta['sale_notification_email'] : '';
                        $email_subject           = ! empty( $meta['email_subject'] ) ? $meta['email_subject'] : __( 'Your order received', 'tourfic-pro' );
                        $email_from_name         = ! empty( $meta['email_from_name'] ) ? $meta['email_from_name'] : '';
                        $email_from_email        = ! empty( $meta['email_from_email'] ) ? $meta['email_from_email'] : '';
                        $order_email_heading     = ! empty( $meta['order_email_heading'] ) ? $meta['order_email_heading'] : '';
                        $email_header_bg         = ! empty( $meta['email_header_bg'] ) ? $meta['email_header_bg'] : array();
                        $email_header_bg         = ! empty( $email_header_bg['bg_color'] ) ? $email_header_bg['bg_color'] : '#0209af';
                        //mail headers
                        $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                        $headers  = $charset . "\r\n";
                        $headers .= "MIME-Version: 1.0" . "\r\n";
                        $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                        //email body open
                        $email_body_open                        = $this->email_body_open( $brand_logo, $order_email_heading, $email_header_bg);
                        $email_body_open                        = str_replace( '{booking_id}', $order_id, $email_body_open );
                        $customer_confirmation_template_content = $this->replace_mail_tags( $customer_confirmation_template_content, $order_id );
                        $email_body_close                       = $this->email_body_close();
                        $customer_email_booking_body_full       = $email_body_open . $customer_confirmation_template_content . $email_body_close;
                        
                        //send mail to customer
                        wp_mail( $order_billing_email, $email_subject, $customer_email_booking_body_full, $headers );
                    }
                }
                
            }else{

                //get order details
                $order                   = wc_get_order( $order_id );
                $order_billing_email     = $order->get_billing_email();
                $email_settings          = self::$tf_email_settings;
                $order_email_heading     = !empty( $email_settings['order_email_heading'] ) ? $email_settings['order_email_heading'] : __( 'Your order received' , 'tourfic' );
                $brand_logo              = !empty( $email_settings['brand_logo'] ) ? $email_settings['brand_logo'] : '';
                $email_heading_bg        = !empty( $email_settings['email_heading_bg'] ) ? $email_settings['email_heading_bg']['bg_color'] : '#0209AF';
                
                $email_from_name         = !empty( $email_settings['email_from_name'] ) ? $email_settings['email_from_name'] : get_bloginfo( 'name' );
                $email_from_email        = !empty( $email_settings['email_from_email'] ) ? $email_settings['email_from_email'] : get_bloginfo( 'admin_email' );
                $email_content_type      = !empty( $email_settings['email_content_type'] ) ? $email_settings['email_content_type'] : 'text/html';

                //mail headers
                $charset  = apply_filters( 'tourfic_mail_charset', 'Content-Type: text/html; charset=UTF-8' );
                $headers  = $charset . "\r\n";
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "From: $email_from_name <$email_from_email>" . "\r\n";
                $headers .= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

                //email body started
                $email_body_open = $this->email_body_open( $brand_logo, $order_email_heading, $email_heading_bg );

                $email_body_open               = str_replace( '{booking_id}', $order_id, $email_body_open );
                $admin_booking_email_template  = !empty( $email_settings['admin_booking_email_template'] ) ? $email_settings['admin_booking_email_template'] : $this->get_email_template( 'order_confirmation', '', 'admin');
                $vendor_booking_email_template = !empty( $email_settings['vendor_booking_email_template'] ) ? $email_settings['vendor_booking_email_template'] : $this->get_email_template( 'order_confirmation', '', 'vendor');
            
                //replace mail tags
                $admin_booking_email_template = $this->replace_mail_tags( $admin_booking_email_template , $order_id );
                //email body ended
                $email_body_close  = $this->email_body_close();
                

                //send mail to vendor
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "vendor" ){

                    $vendor_email_subject          = !empty( $email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] :  __( 'Your email subject','tourfic' );;
                    $vendor_booking_email_template = !empty( $email_settings['vendor_booking_email_template'] ) ? $email_settings['vendor_booking_email_template'] : $this->get_email_template( 'order_confirmation', '', 'vendor');;

                    //replace mail tags to actual value
                    $vendor_booking_email_template  = $this->replace_mail_tags( $vendor_booking_email_template , $order_id );
                    $vendor_email_booking_body_full = $email_body_open . $vendor_booking_email_template . $email_body_close;
                    
                    if ( !empty( $vendor_booking_email_template ) ) {
                        //send mail to vendor
                        $vendors_email = $this->tf_get_vendor_emails( $order_id );
                        if ( !empty( $vendors_email ) ) {
                            foreach ( $vendors_email as $key => $vendor_email ) {
                                wp_mail( $vendor_email, $vendor_email_subject, wp_kses_post($vendor_email_booking_body_full), $headers );
                            }
                        }
                    } else {
                        //send default mail
                        $default_mail = '<p>' . __( 'Dear Admin', 'tourfic' ) . '</p></br>';
                        $default_mail .= '<p>' . __( 'You have received a new booking. The details are as follows:', 'tourfic' ) . '</p></br>';
                        $default_mail .= __( '{booking_details}', 'tourfic' ) . '</br>';
                        $default_mail .= '<strong>' . __( 'Customer details', 'tourfic' ) . '</strong>' . '</br>';
                        $default_mail .= __( '{customer_details}', 'tourfic' ) . '</br>';
                        $default_mail .= __( 'Thank you', 'tourfic' ) . '</br>';
                        $default_mail .= __( 'Regards', 'tourfic' ) . '</br>';
                        $default_mail .= __( '{site_name}', 'tourfic' ) . '</br>';

                        $default_mail = $this->replace_mail_tags( $default_mail , $order_id );
                        $vendors_email = $this->tf_get_vendor_emails( $order_id );
                        if ( !empty( $vendors_email ) ) {
                            foreach ( $vendors_email as $key => $vendor_email ) {
                                wp_mail( $vendor_email, $vendor_email_subject, $default_mail, $headers );
                            }
                        }
                    }

                }

                //customer email settings
                $customer_email_address          = $order_billing_email;
                $customer_email_subject          = !empty( $email_settings['customer_confirm_email_subject'] ) ? $email_settings['customer_confirm_email_subject'] :  __( 'Your email subject','tourfic' );;
                $customer_email_subject          = str_replace( '{booking_id}', $order_id, $customer_email_subject );
                $customer_from_name              = !empty( $email_settings['customer_from_name'] ) ? $email_settings['customer_from_name'] : '';
                $customer_from_email             = !empty( $email_settings['customer_from_email'] ) ? $email_settings['customer_from_email'] : '';
                $customer_confirm_email_template = !empty( $email_settings['customer_confirm_email_template'] ) ? $email_settings['customer_confirm_email_template'] : $this->get_email_template( 'order_confirmation', '', 'customer');;
                $headers .= "From: {$customer_from_name} <{$customer_from_email}>" . "\r\n";

                //send mail to customer
                if( ! empty ( $tf_mail_type ) && $tf_mail_type == "customer" ){
                    if ( !empty( $customer_confirm_email_template ) ) {
                        //replace mail tags to actual value
                        $customer_confirm_email_template = $this->replace_mail_tags( $customer_confirm_email_template , $order_id );

                        $customer_email_body_full = $email_body_open . $customer_confirm_email_template . $email_body_close;
                        //send mail in plain text and html conditionally
                        if ( $email_content_type == 'text/plain' ) {
                            $customer_email_body_full = wp_strip_all_tags( $customer_email_body_full );
                        } else {
                            $customer_email_body_full = wp_kses_post( $customer_email_body_full );
                        }
                        wp_mail( $customer_email_address, $customer_email_subject, $customer_email_body_full, $headers );
                    } else {
                        //send default mail
                        $default_mail = '<p>' . __( 'Dear', 'tourfic' ) . ' {fullname}</p></br>';
                        $default_mail .= '<p>' . __( 'Thank you for your booking. The details are as follows:', 'tourfic' ) . '</p></br>';
                        $default_mail .= __( '{booking_details}', 'tourfic' ) . '</br>';
                        $default_mail .= '<strong>' . __( 'Shipping Details', 'tourfic' ) . '</strong>' . '</br>';
                        $default_mail .= __( '{customer_details}', 'tourfic' ) . '</br>';
                        $default_mail .= __( 'Thank you', 'tourfic' ) . '</br>';
                        $default_mail .= __( 'Regards', 'tourfic' ) . '</br>';
                        $default_mail .= __( '{site_name}', 'tourfic' ) . '</br>';

                        $default_mail = $this->replace_mail_tags( $default_mail , $order_id );

                        wp_mail( $customer_email_address, $customer_email_subject, $default_mail, $headers );
                    }
                }
            }
        }

        die();
    }

}