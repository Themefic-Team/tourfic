<?php

namespace Tourfic\Admin\Enquiry;
defined( 'ABSPATH' ) || exit;

class Apartment_Enquiry extends \Tourfic\Core\Enquiry {
	use \Tourfic\Traits\Singleton;

	public function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=tf_apartment',
			apply_filters( 'tourfic_apartment_enquiry_page_title', esc_html__( 'Apartment Enquiry Details', 'tourfic' ) ),
			apply_filters( 'tourfic_apartment_enquiry_menu_title', esc_html__( 'Enquiry Details', 'tourfic' ) ),
			'edit_tf_apartments',
			'tf_apartment_enquiry',
			array( $this, 'tf_enquiry_page_callback' )
		);
	}

	public function tf_enquiry_page_callback() {
		$enquiry_id = absint( filter_input( INPUT_GET, 'enquiry_id', FILTER_VALIDATE_INT ) );
		$action     = sanitize_key( (string) filter_input( INPUT_GET, 'action', FILTER_UNSAFE_RAW ) );

		if ( $enquiry_id || $action ) {
			$this->single_enquiry_details( $this->tf_get_enquiry_for_admin_view( 'tf_apartment' ) );
        } else {
			if ( ! $this->tf_current_user_can_manage_enquiry_post_type( 'tf_apartment' ) ) {
				wp_die( esc_html__( 'You are not allowed to access this page.', 'tourfic' ), 403 );
			}

			$paged = max( 1, absint( filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT ) ) );
			if ( $paged > 1 ) {
				check_admin_referer( 'tourfic_filter_enquiries_tf_apartment' );
			}

            ?>
                <div class="wrap tf_booking_details_wrap tf-enquiry-details-wrap" style="margin-right: 20px;">
                    <div id="tf-enquiry-status-loader">
                        <img src="<?php echo esc_url(TOURFIC_ASSETS_URL); ?>app/images/loader.gif" alt="Loader">
                    </div>
                    <hr class="wp-header-end">
                    <div class="tf_enquiry_details_wrap_header">
						<h1 class="wp-heading-inline"><?php echo esc_html( apply_filters( 'tourfic_apartment_enquiry_page_heading', esc_html__( 'Apartment Enquiry Details', 'tourfic' ) ) ); ?></h1>
                        <div class="tf_header_wrap_button">
                            <?php
                                do_action( 'tourfic_before_enquiry_details' ); //old hook
								do_action( 'tourfic_before_apartment_enquiry_details' );
                                do_action( 'tourfic_after_apartment_enquiry_details');
                            ?>
                        </div>
                    </div>
                    <?php 
                    $filter_options = array(
                        "name" => 'Apartment',
                        "post_type" => 'tf_apartment',
                    );
                    $this->enquiry_header_filter_options($filter_options); 
                    
					$author_id    = $this->tf_current_user_can_manage_all_enquiries( 'tf_apartment' ) ? 0 : get_current_user_id();
					$enquiry_data = $this->enquiry_table_data( 'tf_apartment', '', '', 0, 0, $author_id );
                    $total_data = ! empty( count( $enquiry_data ) ) ? count( $enquiry_data ) : 0;;
                    $per_page = 20;
                    $offset = ( $paged - 1 ) * $per_page;
                    $enquiry_data = array_slice($enquiry_data, $offset, $per_page);
                    $total_pages  = !empty( $total_data ) ? ceil( $total_data / $per_page ) : 1;
					$this->enquiry_details_list( $enquiry_data, $total_pages, $paged );
                    ?>
                </div>
            <?php
        }
	}
}
