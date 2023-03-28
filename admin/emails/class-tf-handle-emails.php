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
        add_action( 'woocommerce_thankyou', array( $this, 'send_email' ), 10, 1 );
        //add_action( 'woocommerce_order_status_completed', array( $this, 'send_email' ), 10, 1 );

    }

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

    /**
     * Send Email
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function send_email( $order_id ){
        
        $email_settings = self::$tf_email_settings;
        //get order details
        $order = wc_get_order( $order_id );
        $order_data = $order->get_data();
        $order_items = $order->get_items();
        $order_subtotal = $order->get_subtotal();
        $order_total = $order->get_total();        
        $order_billing_email = $order->get_billing_email();
        $order_billing_phone = $order->get_billing_phone();
        $order_payment_method = $order->get_payment_method();
        $payment_method_title = $order->get_payment_method_title();
        $order_shipping_method = $order->get_shipping_method();
        $order_currency = $order->get_currency();
        $order_status = $order->get_status();
        $order_date_created = $order->get_date_created();
        $order_items_data = array();
       
        //payment method
        $get_post_edit_link = get_edit_post_link( $order_id );
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

        $booking_details = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;"><thead><tr><th>Item Name</th><th>Quantity</th><th>Price</th></tr></thead>';
        foreach( $order_items_data as $item ){
            $booking_details .= '<tbody><tr>';
            $booking_details .= '<td>'.$item['item_name'];
            //item meta data except _order_type,_post_author,_tour_id php loop
            foreach( $item['item_meta_data'] as $meta_data ){
                if( $meta_data['key'] != '_order_type' && $meta_data['key'] != '_post_author' && $meta_data['key'] != '_tour_id' ){
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
            $booking_details .= '<td>'.$item['item_quantity'].'</td>';
            $booking_details .= '<td>'.wc_price($item['item_subtotal']).'</td><tbody>';
        } 
        $booking_details .= '<tfoot><tr><th colspan="2" align="left">Subtotal</th>';
        $booking_details .= '<td>'.wc_price($order_subtotal).'</td></tr>';
        //payment method
        $booking_details .= '<tr><th colspan="2" align="left">Payment Method</th>';
        $booking_details .= '<td>'.$payment_method_title.'</td></tr>';
        //total
        $booking_details .= '<tr><th colspan="2" align="left">Total</th>';
        $booking_details .= '<td>'.wc_price($order_total).'</td></tr>';
       
        $booking_details .= '</table>';
        //booking details end
       
        //admin email settings
        $send_notifcation             = !empty($email_settings['send_notification'] ) ? $email_settings['send_notification'] : 'no';
        $sale_notification_email      = !empty($email_settings['sale_notification_email'] ) ? $email_settings['sale_notification_email'] : get_bloginfo('admin_email');
        $admin_email_disable          = !empty($email_settings['admin_email_disable'] ) ? $email_settings['admin_email_disable'] : false;
        $admin_email_subject          = !empty($email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] . "#" . $order_id: '';
        $email_from_name              = !empty($email_settings['email_from_name'] ) ? $email_settings['email_from_name'] : get_bloginfo('name');
        $email_from_email             = !empty($email_settings['email_from_email'] ) ? $email_settings['email_from_email'] : get_bloginfo('admin_email');
        $email_content_type           = !empty($email_settings['email_content_type'] ) ? $email_settings['email_content_type'] : 'html';
        $email_body_open              = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body><div style="line-height: 2em; margin: 0 auto; background: #fafafa; padding: 50px; width: 600px;">';
        $admin_booking_email_template = !empty($email_settings['admin_booking_email_template'] ) ? $email_settings['admin_booking_email_template'] : '';

        //all mail tags mapping
        $tf_all_mail_tags = array(
            '{booking_id}'         => $order_id,
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
        $admin_email_booking_body_full = wp_kses_post( $admin_email_booking_body_full );
        //mail headers
        $charset = apply_filters( 'tourfic_mail_charset','Content-Type: text/html; charset=UTF-8') ;
        $headers = $charset . "\r\n";
        $headers.= "MIME-Version: 1.0" . "\r\n";
        $headers.= "From: $email_from_name <$email_from_email>" . "\r\n";
        $headers.= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
        $headers.= "X-Mailer: PHP/" . phpversion() . "\r\n";

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
                    wp_mail( $sale_notification_email, $admin_email_subject, $admin_email_booking_body_full, $headers );

                }
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
            

            $vendor_email_booking_body_full = wp_kses_post( html_entity_decode( $vendor_email_booking_body_full, 3, 'UTF-8' ) );
            // var_dump($vendors_email);
            // wp_die();
            //send mail to vendor
            if( ! empty( $vendors_email ) ){
                foreach ( $vendors_email as $key => $vendor_email ) {
                    wp_mail( $vendor_email, $vendor_email_subject, $vendor_email_booking_body_full, $headers );
                }
            }

       }

        //customer email settings
        $customer_email_address =  $order_billing_email;
        $customer_email_subject = !empty( $email_settings['customer_confirm_email_subject'] ) ? $email_settings['customer_email_subject']  : '';
        $customer_confirm_email_template = !empty($email_settings['customer_confirm_email_template'] ) ? $email_settings['customer_confirm_email_template'] : '';
        //send mail to customer 
        if( !empty( $customer_confirm_email_template ) ){
            //replace mail tags to actual value
            $customer_confirm_email_template = str_replace( array_keys( $tf_all_mail_tags ), array_values( $tf_all_mail_tags ), $customer_confirm_email_template );

            $customer_email_body_full = $email_body_open . $customer_confirm_email_template . $email_body_close;
            $customer_email_body_full = wp_kses_post(  $customer_email_body_full );
            wp_mail( $customer_email_address, $customer_email_subject, $customer_email_body_full, $headers );
        }

       
    }
}
//call the class
new TF_Handle_Emails();