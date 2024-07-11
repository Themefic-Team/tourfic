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

	function tf_enquiry_page_callback() {
		?>
        <div class="wrap" style="margin-right: 20px;">
            <h1 class="wp-heading-inline"><?php echo esc_html( apply_filters( 'tf_apartment_enquiry_page_heading', __( 'Apartment Enquiry Details', 'tourfic' ) ) ); ?></h1>
			<?php
			do_action( 'tf_before_enquiry_details' ); //old hook
			do_action( 'tf_before_apartment_enquiry_details' );
			$this->enquiry_table('tf_apartment');
			do_action( 'tf_after_apartment_enquiry_details');
			?>
        </div>
		<?php
	}
}