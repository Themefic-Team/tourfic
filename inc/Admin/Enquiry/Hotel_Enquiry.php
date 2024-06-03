<?php

namespace Tourfic\Admin\Enquiry;

defined( 'ABSPATH' ) || exit;

use Tourfic\Admin\TF_List_Table;

class Hotel_Enquiry extends \Tourfic\Core\Enquiry {

    use \Tourfic\Traits\Singleton;

	function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=tf_hotel',
			apply_filters( 'tf_hotel_enquiry_page_title', esc_html__( 'Hotel Enquiry Details', 'tourfic' ) ),
			apply_filters( 'tf_hotel_enquiry_menu_title', esc_html__( 'Enquiry Details', 'tourfic' ) ),
			'edit_tf_hotels',
			'tf_hotel_enquiry',
			array( $this, 'enquiry_page_callback' )
		);
	}

	function enquiry_page_callback() {
		?>
        <div class="wrap" style="margin-right: 20px;">
            <?php do_action( 'tf_before_hotel_enquiry_details' ); ?>
            <h1 class="wp-heading-inline"><?php echo apply_filters( 'tf_hotel_enquiry_title', esc_html__( 'Hotel Enquiry Details', 'tourfic' ) ); ?></h1>
			<?php
			/**
			 * Before enquiry details table hook
			 * @hooked tf_before_tour_booking_details - 10
			 * @since 2.9.18
			 */
			do_action( 'tf_before_enquiry_details' );

			$current_user = wp_get_current_user();
			$current_user_role = $current_user->roles[0];
			global $wpdb;

			if ( $current_user_role == 'administrator' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s ORDER BY id DESC", 'tf_hotel' ), ARRAY_A );
			} elseif ( $current_user_role == 'administrator' ) {
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s ORDER BY id DESC LIMIT 15", 'tf_hotel' ), ARRAY_A );
			}

			$hotel_enquiry_results = new TF_List_Table( $hotel_enquiry_result );
			$hotel_enquiry_results->prepare_items();
			$hotel_enquiry_results->display();

            do_action( 'tf_after_hotel_enquiry_details' );
			?>
        </div>
		<?php
	}

}