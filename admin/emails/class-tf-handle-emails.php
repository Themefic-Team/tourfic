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
    /**
     * Send Email
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function send_email( $order_id ){
        $email_settings = unserialize( tfopt('email-settings') ) ? unserialize( tfopt('email-settings') ) : array();
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
        $admin_email_subject = !empty($email_settings['admin_email_subject'] ) ? $email_settings['admin_email_subject'] . $order_id: '';
        $email_from_name = !empty($email_settings['email_from'] ) ? $email_settings['email_from'] : get_bloginfo('name');
        $email_from_email = !empty($email_settings['email_from_email'] ) ? $email_settings['email_from_email'] : get_bloginfo('admin_email');
        $email_body = !empty($email_settings['email_body'] ) ? $email_settings['email_body'] : '';
        $email_content_type = !empty($email_settings['email_content_type'] ) ? $email_settings['email_content_type'] : 'html';
        
        //customer email settings
        $customer_email_address =  $order_billing_email;
        $customer_email_subject = !empty($email_settings['customer_email_subject'] ) ? $email_settings['customer_email_subject']  : '';
        $customer_email_body = !empty($email_settings['customer_email_body'] ) ? $email_settings['customer_email_body'] : '';

        // //validate email settings
        // if( empty($email_settings['email_to']) || empty($email_settings['email_subject']) || empty($email_settings['email_message']) ){
        //     return;
        // }
        //tf_var_dump($email_settings);
        //wp_die('exit');
        //$to = $email_settings['email_to'];
        //$order = wc_get_order( $order_id );

        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $sale_notification_email, $admin_email_subject, $email_body, $headers );
    }
}
//call the class
new TF_Handle_Emails();