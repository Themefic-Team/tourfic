<?php 
/**
 * TourFic Handle Emails Class for admin/vendors/customers
 * @author: Abu Hena
 * @package: TourFic
 * @since: 2.3.0
 * 
 */
class TF_Handle_Emails{

    /**
     * Constructor
     */
    public function __construct(){
        //send mail after new woocommerce order thankyou page
        add_action( 'woocommerce_thankyou', array( $this, 'send_email' ), 10, 1 );
        //add_action( 'woocommerce_order_status_completed', array( $this, 'send_email' ), 10, 1 );

    }

    public static function get_email_template( $template_type = 'order', $template = '', $sendto = 'admin' ){
    
        $settings = tfopt('email-settings')  ? tfopt('email-settings') : array();
        // $templates = array(
        //     'order' => array(
        //         'admin' => $settings['admin_email_template'],
        //         'customer'
        //     ),
        //     'order_confirmation' => array(
        //         'admin',
        //         'customer'
        //     )
		// );

		//$content = ! empty( $templates[ $template_type ][ $sendto ] ) ? $templates[ $template_type ][ $sendto ] : '';

		// if ( ! empty( $content ) ) {
		// 	return $content;
		// }
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
        tf_var_dump($this->$email_settings);
        //get order details
        $order = wc_get_order( $order_id );
        $order_data = $order->get_data();
        $order_items = $order->get_items();
        $order_items_data = array();
        foreach( $order_items as $item_id => $item_data ){
            $order_items_data[$item_id] = $item_data->get_data();
        }
        //$order_billing_address = $order->get_billing_address();
        //$order_shipping_address = $order->get_shipping_address();
        $order_billing_email = $order->get_billing_email();
        $order_billing_phone = $order->get_billing_phone();
        $order_payment_method = $order->get_payment_method();
        $order_payment_method_title = $order->get_payment_method_title();
        $order_shipping_method = $order->get_shipping_method();
        //$order_shipping_method_title = $order->get_shipping_method_title();
        $order_total = $order->get_total();
        $order_currency = $order->get_currency();
        $order_status = $order->get_status();
        $order_date_created = $order->get_date_created();
        $order_date_modified = $order->get_date_modified();

       
        //admin email settings
        $send_notifcation = !empty($email_settings['send_notification'] ) ? $email_settings['send_notification'] : 'no';
        $sale_notification_email = !empty($email_settings['sale_notification_email'] ) ? $email_settings['sale_notification_email'] : get_bloginfo('admin_email');
        $admin_email_disable = !empty($email_settings['admin_email_disable'] ) ? $email_settings['admin_email_disable'] : 'no';
        $admin_email_subject = !empty($email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] . "#" . $order_id: '';
        $email_from_name = !empty($email_settings['email_from_name'] ) ? $email_settings['email_from_name'] : get_bloginfo('name');
        $email_from_email = !empty($email_settings['email_from_email'] ) ? $email_settings['email_from_email'] : get_bloginfo('admin_email');
        $email_content_type = !empty($email_settings['email_content_type'] ) ? $email_settings['email_content_type'] : 'html';
        $email_body_open = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>';
        $admin_email_template = !empty($email_settings['admin_email_template'] ) ? $email_settings['admin_email_template'] : '';
        $email_body_close = '</body></html>';
        $email_body_full = $email_body_open . $admin_email_template . $email_body_close;
        //mail headers
        $charset = apply_filters( 'tourfic_mail_charset','Content-Type: text/html; charset=UTF-8') ;
        $headers = $charset . "\r\n";
        $headers.= "MIME-Version: 1.0" . "\r\n";
        $headers.= "From: $email_from_name <$email_from_email>" . "\r\n";
        $headers.= "Reply-To: $email_from_name <$email_from_email>" . "\r\n";
        $headers.= "X-Mailer: PHP/" . phpversion() . "\r\n";

        wp_die( $email_body_full);
        wp_mail( $sale_notification_email, $admin_email_subject, $admin_email_template, $headers );

        //customer email settings
        $customer_email_address =  $order_billing_email;
        $customer_email_subject = !empty($email_settings['customer_email_subject'] ) ? $email_settings['customer_email_subject']  : '';
        $customer_email_body = !empty($email_settings['customer_email_body'] ) ? $email_settings['customer_email_body'] : '';
        $customer_email_content_type = !empty($email_settings['customer_email_content_type'] ) ? $email_settings['customer_email_content_type'] : 'html';

       
    }
}
//call the class
new TF_Handle_Emails();