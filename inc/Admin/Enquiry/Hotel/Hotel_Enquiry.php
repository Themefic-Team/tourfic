<?php
namespace Tourfic\Admin\Enquiry\Hotel;
defined( 'ABSPATH' ) || exit;

class Hotel_Enquiry extends \Tourfic\Admin\Enquiry\Enquiry
{
    use \Tourfic\Traits\Singleton;

    public function __construct()
    {

        $enquiry_args = array(
            'post_type' => 'tf_hotel',
            'menu_title' => 'Hotel Enquiry Details',
            'menu_slug' => 'tf_hotel_enquiry',
            'capability' => 'edit_tf_hotels',
            'enquiry_title' => 'Hotel'
        );

        $current_user = wp_get_current_user();
        // get user id
        $current_user_id = $current_user->ID;
        // get user role
        $current_user_role = $current_user->roles[0];
        global $wpdb;
        $table_name = $wpdb->prefix . 'tf_enquiry_data';

        if ( $current_user_role == 'administrator' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $tf_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC", 'tf_tours' ), ARRAY_A );
        } elseif ( $current_user_role == 'administrator' ) {
            $tf_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_type = %s ORDER BY id DESC LIMIT 15", 'tf_tours' ), ARRAY_A );
        }
        
        parent::__construct($tf_enquiry_result, $enquiry_args);

    }

}
