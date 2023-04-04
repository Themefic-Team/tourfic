<?php 
/**
 * Tourfic Handle Emails Class for admin/vendors/customers
 * @author: Abu Hena
 * @package: TourFic
 * @since: 2.3.0
 * 
 */
class TF_Handle_Emails{

    protected static $tf_email_settings;
    /**
     * Constructor
     */
    public function __construct(){
        self::$tf_email_settings = tfopt('email-settings')  ? tfopt('email-settings') : array(); 
        //send mail after new woocommerce order thankyou page
        add_action( 'phpmailer_init', array( $this, 'tf_send_attachment' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'send_email' ), 10, 1 );
        add_action( 'woocommerce_order_status_completed', array( $this, 'send_email' ), 10, 1 );

    }

    /**
     * Get email template
     * @param string $template_type
     * @param string $template
     * @param string $sendto
     * @since 2.3.0
     * 
     */
    public static function get_email_template( $template_type = 'order', $template = '', $sendto = 'admin' ){

        $email_settings = self::$tf_email_settings;
        $templates = array(
            'order' => array(
                'admin'    => !empty( $email_settings['admin_booking_email_template'] ) ? $email_settings['admin_booking_email_template'] : '',
                'customer' => !empty( $email_settings['customer_booking_email_template'] ) ? $email_settings['customer_booking_email_template'] : '',
            ),
            'order_confirmation' => array(
                'admin'    => !empty( $email_settings['admin_confirmation_email_template'] ) ? $email_settings['admin_confirmation_email_template'] : '',
                'customer' => !empty( $email_settings['customer_confirmation_email_template'] ) ? $email_settings['customer_confirmation_email_template'] : '',
            )
		);

		$content = ! empty( $templates[ $template_type ][ $sendto ] ) ? $templates[ $template_type ][ $sendto ] : '';

		if ( ! empty( $content ) ) {
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
    public static function get_emails_strings( $template_type, $sendto = 'admin', $string = 'heading'  ){
        $strings = apply_filters(
            'tf_email_strings',
            array(
                'order' => array(
                    'admin' => array(
						'heading'         => __( 'New Order Received','tourfic' ),
						'greeting'        => __( 'Dear Admin,', 'tourfic' ),
						'greeting_byline' => __( 'A new booking has been made on your website. Booking details are listed below.', 'tourfic' ),
                    ),
                    'vendor' => array(
                        'heading'         => __( 'New Order Received','tourfic' ),
                        'greeting'        => __( 'Dear Vendor,', 'tourfic' ),
                        'greeting_byline' => __( 'A new booking has been made on your website. Booking details are listed below.', 'tourfic' ),
                    ),
                    'customer' => array(
                        'heading'         => __( 'Booking Confirmation','tourfic' ),
                        'greeting'        => __( 'Dear Customer,', 'tourfic' ),
                        'greeting_byline' => __( 'A new booking has been made on your website. Booking details are listed below.', 'tourfic' ),

                    ),
                ),
                'order_confirmation' => array(
                    'admin'    => array(
						'heading'         => __( 'A Payment has been received for {booking_id}', 'tourfic' ),
						'greeting'        => __( 'Dear Admin,', 'tourfic' ),
						'greeting_byline' => __( 'A payment has been received for {booking_id}. The payment details are listed below.', 'tourfic' ),
					),
                    'vendor'   => array(
                        'heading'         => __( 'A Payment has been received for {booking_id}', 'tourfic' ),
                        'greeting'        => __( 'Dear Vendor,', 'tourfic' ),
                        'greeting_byline' => __( 'A payment has been received for {booking_id}. The payment details are listed below.', 'tourfic' ),
                    ),
					'customer' => array(
						'heading'         => __( 'Your booking has been confirmed.', 'tourfic' ),
						'greeting'        => __( 'Dear {name},', 'tourfic' ),
						'greeting_byline' => __( 'Your booking has been confirmed. Your booking and payment information is listed below.', 'tourfic' ),
					),
                )
        
            ), 
        );
        if( isset( $strings[$template_type][$sendto][$string] ) ){
            return $strings[$template_type][$sendto][$string];
        }
        return false;


    }
    public static function tf_send_attachment( ) {
        $email_settings = self::$tf_email_settings;
        $brand_logo = !empty($email_settings['brand_logo'] ) ? $email_settings['brand_logo'] : '';
        if( ! empty( $brand_logo ) ){
            $logo_id = attachment_url_to_postid( $brand_logo );
       
            $brand_logo_path = get_attached_file( $logo_id ); //phpmailer will load this file
            $uid = 'logo-uid'; //will map it to this UID
            global $phpmailer;
            $phpmailer->AddEmbeddedImage($brand_logo_path, $uid);
        }else{
            return;
        }
        
    } 
    /**
     * Send Email
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function send_email( $order_id ){
       
        $email_settings = self::$tf_email_settings;
        $order_email_heading = !empty( $email_settings['order_email_heading'] ) ? $email_settings['order_email_heading'] : '';
        //get order details
        $order = wc_get_order( $order_id );
        $order_data = $order->get_data();
        $order_items = $order->get_items();
        $order_subtotal = $order->get_subtotal();
        $order_total = $order->get_total();        
        $order_billing_name = $order->get_billing_first_name() .' '. $order->get_billing_last_name();
        $order_billing_address = $order->get_billing_address_1() . ' ' . $order->get_billing_address_2();
        $order_billing_email = $order->get_billing_email();
        $order_billing_phone = $order->get_billing_phone();
        $order_billing_city = $order->get_billing_city();
        $order_billing_country = $order->get_billing_country();
        $order_billing_postcode = $order->get_billing_postcode();
        $order_payment_method = $order->get_payment_method();
        $payment_method_title = $order->get_payment_method_title();
        $order_shipping_method = $order->get_shipping_method();
        $order_currency = $order->get_currency();
        $order_status = $order->get_status();
        $order_date_created = $order->get_date_created();
        $order_items_data = array();
        //payment method
        $order_url = get_edit_post_link( $order_id );
        //get order items details as table format so we can use it in email template
        foreach( $order_items as $item_id => $item_data ){
            $item_name = $item_data->get_name();
            $item_quantity = $item_data->get_quantity();
            $item_total = $item_data->get_total();
            $item_subtotal = $item_data->get_subtotal();
            $item_subtotal_tax = $item_data->get_subtotal_tax();
            $item_total_tax = $item_data->get_total_tax();
            $item_taxes = $item_data->get_taxes();
            $item_meta_data = $item_data->get_meta_data();            
           
            $item_meta_data_array = array();
            foreach( $item_meta_data as $meta_data ){
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
                'item_subtotal_tax' => $item_subtotal_tax,
                'item_total_tax'    => $item_total_tax,
                'item_taxes'        => $item_taxes,
                'item_meta_data'    => $item_meta_data_array,
            );
           
        }
        //authors email array
        $vendors_email = array();

        $booking_details = '<table width="100%" style="max-width: 600px;border-collapse: collapse; color: #5A5A5A;"><thead><tr><th align="left">Item Name</th><th align="center">Quantity</th><th align="right">Price</th></tr></thead><tbody style="border-bottom: 2px solid #D9D9D9">';
        foreach( $order_items_data as $item ){
            $booking_details .= '<tr>';
            $booking_details .= '<td style="padding-left:10px;adding: 15px 0;text-align: left;">'.$item['item_name'];
            //item meta data except _order_type,_post_author,_tour_id php loop
            foreach( $item['item_meta_data'] as $meta_data ){
                if( $meta_data['key'] != '_order_type' && $meta_data['key'] != '_post_author' && $meta_data['key'] != '_tour_id' && $meta_data['key'] != '_post_id' && $meta_data['key'] != '_unique_id' ){
                    $booking_details .= '<br><strong>'.$meta_data['key'].'</strong>: '.$meta_data['value'];
                }
                //identify vendor details
                if( $meta_data['key'] == '_post_author' ){
                    $author_id = $meta_data['value'];
                    $author_name = get_the_author_meta( 'display_name', $author_id );
                    $author_email = get_the_author_meta( 'user_email', $author_id );
                    //get user role
                    $user_data = get_userdata( $author_id );
                    $user_roles = $user_data->roles;
                    if( in_array( 'tf_vendor', $user_roles ) ){
                        array_push( $vendors_email, $author_email );
                    }                    
                }
            }            
           
            $booking_details .= '</td>';
            $booking_details .= '<td align="center">'.$item['item_quantity'].'</td>';
            $booking_details .= '<td align="right">'.wc_price($item['item_subtotal']).'</td>';
            $booking_details .= '</tr>';

        } 
        $booking_details .= '</tbody>';
        $booking_details .= '<tfoot><tr><th colspan="2" align="left">Subtotal</th>';
        $booking_details .= '<td align="right">'.wc_price($order_subtotal).'</td></tr>';
        //payment method
        $booking_details .= '<tr><th colspan="2" align="left">Payment Method</th>';
        $booking_details .= '<td align="right">'.$payment_method_title.'</td></tr>';
        //total
        $booking_details .= '<tr><th colspan="2" align="left">Total</th>';
        $booking_details .= '<td align="right">'.wc_price($order_total).'</td></tr>';
        $booking_details .= '</tfoot>';
       
        $booking_details .= '</table>';
        //booking details end

        //customer details
        $customer_details = '<table style="max-width: 600px;border-collapse: collapse; color: #5A5A5A;"><tbody><tr><td style="padding: 15px 0;text-align: left;">';
        $customer_details .= '<strong>Customer Name:</strong> '.$order_billing_name.'<br>';
        $customer_details .= '<strong>Customer Address:</strong> '.$order_billing_address.'<br>';
        $customer_details .= '<strong>Customer Email:</strong> '.$order_billing_email.'<br>';
        $customer_details .= '<strong>Customer Phone:</strong> '.$order_billing_phone.'<br>';
        $customer_details .= '<strong>Customer City:</strong> '.$order_billing_city.'<br>';
        $customer_details .= '<strong>Customer Country:</strong> '.$order_billing_country.'<br>';
        $customer_details .= '<strong>Customer Postcode:</strong> '.$order_billing_postcode.'<br>';
        $customer_details .= '</td></tr></tbody></table>';
        //customer details end
        //admin email settings

        // Set up the attachments for the brand logo and header image
        if( !empty($brand_logo_path) ){
            $brand_logo_attachment = array(
                'content' => file_get_contents( $brand_logo_path ),
                'mime-type' => 'image/jpeg', // Update the MIME type as needed
                'name' => basename( $brand_logo_path ),
                'data' => $brand_logo_path,
                'cid' => 'brand-logo', // Add a unique identifier for the image
            );            

            //Add the attachments to the email data
            $attachments = array($brand_logo_attachment);
        }else{
            $attachments = array();
        }
        $brand_logo              = !empty( $email_settings['brand_logo'] ) ? $email_settings['brand_logo'] : '';
        $send_notifcation        = !empty( $email_settings['send_notification'] ) ? $email_settings['send_notification'] : 'no';
        $sale_notification_email = !empty( $email_settings['sale_notification_email'] ) ? $email_settings['sale_notification_email'] : get_bloginfo( 'admin_email' );
        $admin_email_disable     = !empty( $email_settings['admin_email_disable'] ) ? $email_settings['admin_email_disable'] : false;
        $admin_email_subject     = !empty( $email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] . " # " . $order_id : '';
        $email_from_name         = !empty( $email_settings['email_from_name'] ) ? $email_settings['email_from_name'] : get_bloginfo( 'name' );
        $email_from_email        = !empty( $email_settings['email_from_email'] ) ? $email_settings['email_from_email'] : get_bloginfo( 'admin_email' );
        $email_content_type      = !empty( $email_settings['email_content_type'] ) ? $email_settings['email_content_type'] : 'text/html';

        //mail headers
        $charset = apply_filters( 'tourfic_mail_charset','Content-Type: '.$email_content_type.'; charset=UTF-8') ;
        $headers = $charset . "\r\n";
        $headers.= "MIME-Version: 1.0" . "\r\n";
        $headers.= "From: $email_from_name <$email_from_email>" . "\r\n";
        $headers.= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
        $headers.= "X-Mailer: PHP/" . phpversion() . "\r\n";
        

        $email_body_open              = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><body style="font-family: Work sans, sans-serif; font-size: 16px; color: #9C9C9C; margin: 0; padding: 0;">
        <div style="width: 100%; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #0209AF; color: #fff; padding: 20px;">';
        if( !empty( $brand_logo ) ){
            $logo_id = attachment_url_to_postid( $brand_logo );
            $brand_logo_path = file_get_contents( get_attached_file( $logo_id ) );
            $email_body_open .= '<div style="text-align:left;width:200px;"><img src="cid:logo-uid" alt="logo" /></div>';
        }
        $email_body_open .= '<div class="heading" style="text-align: center;">
        <h1 style="font-size: 32px; line-height: 40px; font-weight: 400; letter-spacing: 2px; margin: 20px 0; color: #ffffff;">
        '.$order_email_heading.'
        </h1>
        <h2 style="font-size:16px;font-weight:500;line-height:20px;color:#ffffff;">
             '. __( 'Order number : ','tourfic' ) . '#{booking_id}
        </h2>
    </div>';
        $email_body_open .= '</div>';
        $email_body_open = str_replace( '{booking_id}', $order_id, $email_body_open );
        $admin_booking_email_template = !empty( $email_settings['admin_booking_email_template'] ) ? $email_settings['admin_booking_email_template'] : '';
        $vendor_booking_email_template = !empty($email_settings['vendor_booking_email_template'] ) ? $email_settings['vendor_booking_email_template'] : '';
        //send attachment to mail from settings image field
        
        //all mail tags mapping
        $tf_all_mail_tags = array(
            '{booking_id}'       => $order_id,
            '{booking_url}'      => $order_url,
            '{booking_details}'  => $booking_details,
            '{fullname}'         => $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'],
            '{user_email}'       => $order_billing_email,
            '{billing_address}'  => $order_data['billing']['address_1'] . ' ' . $order_data['billing']['address_2'],
            '{city}'             => $order_data['billing']['city'],
            '{billing_state}'    => $order_data['billing']['state'],
            '{billing_zip}'      => $order_data['billing']['postcode'],
            '{country}'          => $order_data['billing']['country'],
            '{phone}'            => $order_data['billing']['phone'],
            '{shipping_address}' => $order_data['shipping']['address_1'] . ' ' . $order_data['shipping']['address_2'],
            '{shipping_city}'    => $order_data['shipping']['city'],
            '{shipping_state}'   => $order_data['shipping']['state'],
            '{shipping_zip}'     => $order_data['shipping']['postcode'],
            '{shipping_country}' => $order_data['shipping']['country'],
            '{shipping_phone}'   => $order_data['shipping']['phone'],
            '{payment_method}'   => $payment_method_title,
            '{order_total}'      => wc_price($order_total),
            '{order_subtotal}'   => wc_price($order_subtotal),
            '{order_date}'       => $order_date_created,
            '{order_status}'     => $order_status,
            '{site_name}'        => get_bloginfo('name'),
            '{site_url}'         => get_bloginfo('url'),
            '{site_email}'       => get_bloginfo('admin_email'),
        );

        $admin_booking_email_template = str_replace( array_keys( $tf_all_mail_tags ), array_values( $tf_all_mail_tags ), $admin_booking_email_template );
        

        
        $email_body_close = '</div></body></html>';
        $admin_email_booking_body_full = $email_body_open . $admin_booking_email_template . $email_body_close;
        //decode entity
        $admin_email_booking_body_full = wp_kses_post( html_entity_decode( $admin_email_booking_body_full, '3' , 'UTF-8' ) );
    
       // echo html_entity_decode( wp_kses_post($admin_email_booking_body_full) );
        //echo $admin_email_booking_body_full;
        //wp_die();   

       
        //check if admin emails disable
        if( isset( $admin_email_disable ) && $admin_email_disable == false ){
            if( !empty( $admin_booking_email_template ) ){
                //send multiple emails to multiple admins
                if( strpos( $sale_notification_email, ',') !== false ){
                    $sale_notification_email = explode(',', $sale_notification_email);
                    $sale_notification_email = str_replace(' ', '', $sale_notification_email);
                    foreach ( $sale_notification_email as $key => $email_address ) {
                        wp_mail( $email_address, $admin_email_subject, $admin_email_booking_body_full, $headers );
                    }            
                }else{
                    //send admin email
                    wp_mail( $sale_notification_email, $admin_email_subject, $admin_email_booking_body_full, $headers, $attachments );

                }
            }else{
                //send static default mail
                $default_mail  = '<p>'. __( 'Dear Admin', 'tourfic' ) .'</p></br>';
                $default_mail .= '<p>'. __( 'You have received a new booking. The details are as follows:', 'tourfic' ) .'</p></br>';
                $default_mail .= __( '{booking_details}', 'tourfic' ) .'</br>';
                $default_mail .= __( '<strong>Customer details</strong>', 'tourfic' ) .'</br>';
                $default_mail .= __( '{customer_details}', 'tourfic' ) .'</br>';
                $default_mail .= __( '<p>Thank you</p>', 'tourfic' ) ;
                $default_mail .= __( 'Regards', 'tourfic' ) .'</br>';
                $default_mail .= __( '{site_name}', 'tourfic' ) .'</br>';

                $default_mail = str_replace( '{customer_details}', $customer_details, $default_mail );
                $default_mail = str_replace( '{site_name}', get_bloginfo('name'), $default_mail );
                $default_mail = str_replace( '{booking_details}', $booking_details, $default_mail );

                wp_mail( $sale_notification_email, $admin_email_subject, $default_mail, $headers);

            }
        }

        //send mail to vendor
       if( ! empty( $send_notifcation ) && $send_notifcation == 'admin_vendor' ){

            $vendor_email_subject = !empty( $email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject']  : '';
            $vendor_booking_email_template = !empty($email_settings['vendor_booking_email_template'] ) ? $email_settings['vendor_booking_email_template'] : '';
            
            //replace mail tags to actual value
            $vendor_booking_email_template = str_replace( array_keys( $tf_all_mail_tags ), array_values( $tf_all_mail_tags ), $vendor_booking_email_template );

            $vendor_email_booking_body_full = $email_body_open . $vendor_booking_email_template . $email_body_close;
            //send mail in plain text and html conditionally 
            $vendor_email_booking_body_full = html_entity_decode( $vendor_email_booking_body_full, 3, 'UTF-8' ) ;
            if( !empty( $vendor_booking_email_template ) ){
            
                //send mail to vendor
                if( ! empty( $vendors_email ) ){
                    foreach ( $vendors_email as $key => $vendor_email ) {
                        wp_mail( $vendor_email, $vendor_email_subject, $vendor_email_booking_body_full, $headers);
                    }
                }
            }else{
                //send default mail
                $default_mail = '<p>'. __( 'Dear Admin', 'tourfic' ) .'</p></br>';
                $default_mail .= '<p>'. __( 'You have received a new booking. The details are as follows:', 'tourfic' ) .'</p></br>';
                $default_mail .= __( '{booking_details}', 'tourfic' ) .'</br>';
                $default_mail .= __( '<strong>Customer details</strong>', 'tourfic' ) .'</br>';
                $default_mail .= __( '{customer_details}', 'tourfic' ) .'</br>';
                $default_mail .= __( 'Thank you', 'tourfic' ) .'</br>';
                $default_mail .= __( 'Regards', 'tourfic' ) .'</br>';
                $default_mail .= __( '{site_name}', 'tourfic' ) .'</br>';

                $default_mail = str_replace( '{customer_details}', $customer_details, $default_mail );
                $default_mail = str_replace( '{booking_details}', $booking_details, $default_mail );
                $default_mail = str_replace( '{site_name}', get_bloginfo('name'), $default_mail );

                if( ! empty( $vendors_email ) ){
                    foreach ( $vendors_email as $key => $vendor_email ) {
                        wp_mail( $vendor_email, $vendor_email_subject, $default_mail, $headers);
                    }
                }
            }

       }

        //customer email settings
        $customer_email_address =  $order_billing_email;
        $customer_email_subject = !empty( $email_settings['customer_confirm_email_subject'] ) ? $email_settings['customer_confirm_email_subject']  : '';
        $customer_email_subject = str_replace( '{booking_id}', $order_id, $customer_email_subject );
        $customer_confirm_email_template = !empty($email_settings['customer_confirm_email_template'] ) ? $email_settings['customer_confirm_email_template'] : '';
        //send mail to customer 
        if( !empty( $customer_confirm_email_template ) ){
            //replace mail tags to actual value
            $customer_confirm_email_template = str_replace( array_keys( $tf_all_mail_tags ), array_values( $tf_all_mail_tags ), $customer_confirm_email_template );

            $customer_email_body_full = $email_body_open . $customer_confirm_email_template . $email_body_close;
            $customer_email_body_full = $customer_email_body_full;
            wp_mail( $customer_email_address, $customer_email_subject, $customer_email_body_full, $headers );
        }else{
            //send default mail
            $default_mail = '<p>'. __( 'Dear', 'tourfic' ) .' {fullname}</p></br>';
            $default_mail .= '<p>'. __( 'Thank you for your booking. The details are as follows:', 'tourfic' ) .'</p></br>';
            $default_mail .= __( '{booking_details}', 'tourfic' ) .'</br>';
            $default_mail .= __( '<strong>Shipping Details</strong>', 'tourfic' ) .'</br>';
            $default_mail .= __( '{customer_details}', 'tourfic' ) .'</br>';
            $default_mail .= __( 'Thank you', 'tourfic' ) .'</br>';
            $default_mail .= __( 'Regards', 'tourfic' ) .'</br>';
            $default_mail .= __( '{site_name}', 'tourfic' ) .'</br>';

            $default_mail = str_replace( '{customer_details}', $customer_details, $default_mail );
            $default_mail = str_replace( '{fullname}', $order_billing_name, $default_mail );
            $default_mail = str_replace( '{booking_details}', $booking_details, $default_mail );
            $default_mail = str_replace( '{site_name}', get_bloginfo('name'), $default_mail );

            wp_mail( $customer_email_address, $customer_email_subject, $default_mail, $headers );
        }
    }

}
//init the class
new TF_Handle_Emails();