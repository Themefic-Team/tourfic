<?php

namespace Tourfic\Admin\Enquiry;
defined( 'ABSPATH' ) || exit;

class Apartment_Enquiry extends \Tourfic\Core\Enquiry {
	use \Tourfic\Traits\Singleton;

	public function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=tf_apartment',
			apply_filters( 'tf_apartment_enquiry_page_title', esc_html__( 'Apartment Enquiry Details', 'tourfic' ) ),
			apply_filters( 'tf_apartment_enquiry_menu_title', esc_html__( 'Enquiry Details', 'tourfic' ) ),
			'edit_tf_apartments',
			'tf_apartment_enquiry',
			array( $this, 'tf_enquiry_page_callback' )
		);
	}

	public function tf_enquiry_page_callback() {

        global $wpdb;

        if( !empty($_GET['enquiry_id'] ) && !empty($_GET['action'] )  ){
            
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}tf_enquiry_data SET enquiry_status=%s WHERE id=%d",
                    'read',
                    sanitize_key( $_GET['enquiry_id'] )
                )
            );

            $data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE id = %s", sanitize_key( $_GET['enquiry_id'] ) ), ARRAY_A );

            $this->single_enquiry_details( $data );
        } else {
            ?>
                <div class="wrap tf_booking_details_wrap tf-enquiry-details-wrap" style="margin-right: 20px;">
                    <div id="tf-enquiry-status-loader">
                        <img src="<?php echo esc_url(TF_ASSETS_URL); ?>app/images/loader.gif" alt="Loader">
                    </div>
                    <div class="tf_booking_wrap_header">
						<h1 class="wp-heading-inline"><?php echo esc_html( apply_filters( 'tf_apartment_enquiry_page_heading', __( 'Apartment Enquiry Details', 'tourfic' ) ) ); ?></h1>
                        <div class="tf_header_wrap_button">
                            <?php
                                do_action( 'tf_before_enquiry_details' ); //old hook
								do_action( 'tf_before_apartment_enquiry_details' );
                                do_action( 'tf_after_apartment_enquiry_details');
                            ?>
                        </div>
                    </div>
                    <?php 
                    $filter_options = array(
                        "name" => 'Apartment',
                        "post_type" => 'tf_apartment',
                    );
                    $this->enquiry_header_filter_options($filter_options); 
                    
                    $enquiry_data = $this->enquiry_table_data('tf_apartment');
                    $this->tf_single_enquiry_details();
                    $this->enquiry_details_list($enquiry_data);
                    ?>
                </div>
                <hr class="wp-header-end">
            <?php
        }
	}
}