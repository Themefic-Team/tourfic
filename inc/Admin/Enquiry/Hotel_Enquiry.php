<?php

namespace Tourfic\Admin\Enquiry;
defined( 'ABSPATH' ) || exit;

class Hotel_Enquiry extends \Tourfic\Core\Enquiry {
	use \Tourfic\Traits\Singleton;

	public function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=tf_hotel',
			apply_filters( 'tf_hotel_enquiry_page_title', esc_html__( 'Hotel Enquiry Details', 'tourfic' ) ),
			apply_filters( 'tf_hotel_enquiry_menu_title', esc_html__( 'Enquiry Details', 'tourfic' ) ),
			'edit_tf_hotels',
			'tf_hotel_enquiry',
			array( $this, 'tf_enquiry_page_callback' )
		);
	}

	public function tf_enquiry_page_callback() {
		?>
        <div class="wrap" style="margin-right: 20px;">
            <h1 class="wp-heading-inline"><?php echo esc_html( apply_filters( 'tf_hotel_enquiry_page_heading', __( 'Hotel Enquiry Details', 'tourfic' ) ) ); ?></h1>
			<?php
			do_action( 'tf_before_enquiry_details' ); //old hook
			do_action( 'tf_before_hotel_enquiry_details' );
			$this->enquiry_table('tf_hotel');
            do_action( 'tf_after_hotel_enquiry_details');
			?>
        </div>
		<?php
	}
}