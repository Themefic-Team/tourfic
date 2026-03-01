<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

abstract class TF_Backend_Booking {

	protected array $actions = [];
	protected array $args;
	protected array $settings;
	protected float $price;

	private array $booking_customers_fields;

	public function __construct( array $args ) {
		// backend booking arguments
		$this->args = $args;

		// Add actions
		add_action( 'admin_menu', array( $this, 'tf_backend_booking_menu' ) );
		add_action( 'tf_before_' . $this->args["name"] . '_booking_details', array( $this, 'tf_backend_booking_button' ) );

	}

	final function set_customers_fields() {
		$current_user                   = wp_get_current_user();
		$this->booking_customers_fields = array(
			'tf_booking_customer_fields' => array(
				'title'  => esc_html__( 'Customer Information', 'tourfic' ),
				'fields' => array(
					array(
						'id'         => $this->args["post_type"] . '_booked_by',
						'label'      => esc_html__( 'Booked By', 'tourfic' ),
						'type'       => 'text',
						'default'    => $current_user->display_name ?: $current_user->user_login,
						'attributes' => array(
							'readonly' => 'readonly',
						),
					),
					array(
						'id'          => 'tf_customer_first_name',
						'label'       => esc_html__( 'First Name', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer First Name', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_customer_last_name',
						'label'       => esc_html__( 'Last Name', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Last Name', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_customer_email',
						'label'       => esc_html__( 'Email', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Email', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_customer_phone',
						'label'       => esc_html__( 'Phone', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Phone', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_customer_country',
						'label'       => esc_html__( 'Country / Region', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Country', 'tourfic' ),
						'field_width' => 33.33,
					),
					array(
						'id'          => 'tf_customer_address',
						'label'       => esc_html__( 'Address', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Address', 'tourfic' ),
						'field_width' => 33.33,
					),
					array(
						'id'          => 'tf_customer_address_2',
						'label'       => esc_html__( 'Address 2', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Address 2', 'tourfic' ),
						'field_width' => 33.33,
					),
					array(
						'id'          => 'tf_customer_city',
						'label'       => esc_html__( 'Town / City', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer City', 'tourfic' ),
						'field_width' => 33,
					),
					array(
						'id'          => 'tf_customer_state',
						'label'       => esc_html__( 'State', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer State', 'tourfic' ),
						'field_width' => 33,
					),
					array(
						'id'          => 'tf_customer_zip',
						'label'       => esc_html__( 'Postcode / ZIP', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Zip', 'tourfic' ),
						'field_width' => 33,
					),
				),
			),
		);
	}

	protected function set_settings( array $settings ) {
		$this->set_customers_fields();
		// $this->booking_customers_fields
		$this->settings = array_merge( $this->booking_customers_fields, $settings );
	}

	final function tf_backend_booking_button() {
		$edit_url = admin_url( 'edit.php?post_type=' . $this->args["post_type"] . '&page=' . $this->args["prefix"] . '-backend-booking' );
		?>
        <a href="<?php echo esc_url( $edit_url ); ?>" class="button button-primary tf-booking-btn"><?php esc_html_e( 'Add New Booking', 'tourfic' ); ?></a>
		<?php
	}

	final function tf_backend_booking_menu() {
		add_submenu_page(
			'edit.php?post_type=' . $this->args['post_type'],
			esc_html__( 'Add New Booking', 'tourfic' ),
			esc_html__( 'Add New Booking', 'tourfic' ),
			$this->args['caps'],
			$this->args["prefix"] . '-backend-booking',
			array( $this, 'tf_backend_booking_page' ),
		);
	}

	final function tf_backend_booking_page() {
		if ( ! Helper::tf_is_woo_active() ) {
			?>
            <div class="tf-field-notice-inner tf-notice-danger" style="margin-top: 20px;">
				<?php esc_html_e( 'Please install and activate WooCommerce plugin to use this feature.', 'tourfic' ); ?>
            </div>
			<?php
			return;
		}

		echo '<div class="tf-setting-dashboard">';
		Helper::tf_dashboard_header();
		$booking_form_class = sprintf( esc_html('tf-backend-%s-booking'), $this->args["name"] );
		/* translators: %s Service Name. */
		$booking_form_title = sprintf( esc_html__( 'Add New %s Booking', 'tourfic' ), ucfirst( $this->args["name"] ) );

		// Filters to change booking form title and class
		$booking_form_title = apply_filters('tf_' . $this->args["name"] . '_backend_booking_form_title', $booking_form_title);
		$booking_form_class = apply_filters('tf_' . $this->args["name"] . '_backend_booking_form_class', $booking_form_class);

		// before form action hook
		do_action( 'tf_before_' . $this->args["name"] . '_backend_booking_form');

		?>
		
        <form method="post" action="" class="<?php echo esc_attr( $booking_form_class ); ?>" enctype="multipart/form-data">
            <h1><?php echo esc_html( $booking_form_title ); ?></h1>
			<?php
			$tf_backend_booking_form_fields = apply_filters( 'tf_' . $this->args["name"] . '_backend_booking_form_card', $this->settings);
			foreach ( $tf_backend_booking_form_fields as $id => $tf_backend_booking_form_field ) : ?>
				<?php do_action( 'tf_before_' . $this->args["name"] . '_each_backend_booking_form_card'); ?>
                <div class="tf-backend-booking-card-wrap">
                    <h3 class="tf-backend-booking-card-title"><?php echo esc_html( $tf_backend_booking_form_field['title'] ); ?></h3>

                    <div class="tf-booking-fields-wrapper">
                        <div class="tf-booking-fields">
							<?php
							if ( ! empty( $tf_backend_booking_form_field['fields'] ) ):
								foreach ( $tf_backend_booking_form_field['fields'] as $field ) :

									$default = isset( $field['default'] ) ? $field['default'] : '';
									$value   = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : $default;

									$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
									$tf_option->field( $field, $value, '' );

								endforeach;
							endif; ?>
                        </div>
                    </div>
                </div>
			<?php endforeach; ?>
			<?php wp_nonce_field( 'tf_backend_booking_nonce_action', 'tf_backend_booking_nonce' ); ?>

            <!-- Footer -->
            <div class="tf-backend-booking-footer">
                <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn" id="tf-backend-<?php echo esc_html( $this->args["name"] ); ?>-book-btn"><?php esc_html_e( 'Book Now', 'tourfic' ); ?></button>
            </div>
        </form>
		<?php 
		// after form action hook
		do_action( 'tf_after_' . $this->args["name"] . '_backend_booking_form');
		
		?>
		<?php
		echo '</div>';
	}

	abstract protected function check_avaibility_callback();

	abstract protected function check_price_callback();

	abstract protected function backend_booking_callback();

}