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
		add_action( 'tourfic_before_' . $this->args["name"] . '_booking_details', array( $this, 'tf_backend_booking_button' ) );

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

	protected function can_book_other_listings() {
		$post_type = get_post_type_object( $this->args['post_type'] );

		return current_user_can( 'tf_manager_options' )
			|| ( $post_type && current_user_can( $post_type->cap->edit_others_posts ) );
	}

	protected function can_book_listing( $post_id ) {
		$post = get_post( $post_id );

		return current_user_can( $this->args['caps'] ) && $post
			&& $this->args['post_type'] === $post->post_type && 'publish' === $post->post_status
			&& ( (int) $post->post_author === get_current_user_id() || $this->can_book_other_listings() );
	}

	protected function authorize_listing( $post_id, $booking = false ) {
		if ( ! $this->can_book_listing( $post_id ) ) {
			$this->request_error( esc_html__( 'You do not have permission to book this listing.', 'tourfic' ), $booking );
		}
	}

	protected function listing_query_args() {
		$args = array(
			'post_type'      => $this->args['post_type'],
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		if ( ! $this->can_book_other_listings() ) {
			$args['author'] = get_current_user_id();
		}

		return $args;
	}

	protected function customer_request_schema() {
		return array(
			'tf_customer_first_name' => 'text',
			'tf_customer_last_name'  => 'text',
			'tf_customer_email'      => 'email',
			'tf_customer_phone'      => 'text',
			'tf_customer_country'    => 'text',
			'tf_customer_address'    => 'text',
			'tf_customer_address_2'  => 'text',
			'tf_customer_city'       => 'text',
			'tf_customer_state'      => 'text',
			'tf_customer_zip'        => 'text',
		);
	}

	protected function scope_listing_field( array $field ) {
		// CPT capabilities are available at render time, not during plugin construction.
		if ( $this->args['post_type'] === ( $field['query_args']['post_type'] ?? '' ) && ! $this->can_book_other_listings() ) {
			$field['query_args']['author'] = get_current_user_id();
		}

		return $field;
	}

	/**
	 * Read only declared fields after checking the form's nonce and capability.
	 */
	protected function read_request( array $schema, $booking = false ) {
		if ( $booking ) {
			check_ajax_referer( 'tf_backend_booking_nonce_action', 'tf_backend_booking_nonce' );
			$schema = array_merge( $this->customer_request_schema(), $schema );
		} else {
			check_ajax_referer( 'updates', '_nonce' );
		}
		if ( ! current_user_can( $this->args['caps'] ) ) {
			$this->request_error( esc_html__( 'You do not have permission to access this resource.', 'tourfic' ), $booking );
		}

		$request = array();
		foreach ( $schema as $key => $type ) {
			// Each declared value is unslashed once and validated by its field type before use.
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
			$request[ $key ] = $this->sanitize_request_value( $value, $type );
			if ( null === $request[ $key ] ) {
				$this->request_error( esc_html__( 'Please enter valid booking details.', 'tourfic' ), $booking, $key );
			}
		}
		if ( $booking ) {
			$user = wp_get_current_user();
			$request[ $this->args['post_type'] . '_booked_by' ] = sanitize_text_field( $user->display_name ?: $user->user_login );
		}

		return $request;
	}

	protected function sanitize_request_value( $value, $type ) {
		if ( 'date_range' === $type ) {
			if ( ! is_array( $value ) ) {
				return null;
			}
			$from = $this->sanitize_request_value( $value['from'] ?? '', 'date' );
			$to   = $this->sanitize_request_value( $value['to'] ?? '', 'date' );
			return $from && $to && strtotime( $to ) > strtotime( $from ) ? array( 'from' => $from, 'to' => $to ) : null;
		}
		if ( 'extra_ids' === $type ) {
			if ( '' === $value ) {
				return array();
			}
			if ( ! is_array( $value ) ) {
				return null;
			}
			$ids = array();
			foreach ( $value as $id ) {
				$id = $this->sanitize_request_value( $id, 'text' );
				if ( null === $id || '' === $id ) {
					return null;
				}
				$ids[] = $id;
			}
			return array_values( array_unique( $ids ) );
		}
		if ( ! is_string( $value ) && ! is_int( $value ) ) {
			return null;
		}
		$value = (string) $value;
		if ( in_array( $type, array( 'positive', 'number', 'selection' ), true ) ) {
			if ( '' === $value && 'positive' !== $type ) {
				return 'selection' === $type ? '' : 0;
			}
			if ( ! preg_match( '/^\d+$/D', $value ) || (float) $value >= PHP_INT_MAX ) {
				return null;
			}
			return 'positive' !== $type || (int) $value > 0 ? (int) $value : null;
		}
		if ( 'optional_date' === $type && '' === $value ) {
			return '';
		}
		if ( 'date' === $type || 'optional_date' === $type ) {
			if ( ! preg_match( '~^(\d{4})([/-])(\d{1,2})\2(\d{1,2})$~D', $value, $parts )
				|| ! checkdate( (int) $parts[3], (int) $parts[4], (int) $parts[1] ) ) {
				return null;
			}
			return $value;
		}
		if ( 'email' === $type ) {
			return '' === $value || is_email( $value ) ? sanitize_email( $value ) : null;
		}
		return 'text' === $type ? sanitize_text_field( $value ) : null;
	}

	protected function validate_date_range( $from, $to, $booking = false ) {
		if ( strtotime( $to ) <= strtotime( $from ) ) {
			$this->request_error( esc_html__( 'Check-out must be after check-in.', 'tourfic' ), $booking );
		}
	}

	protected function request_error( $message, $booking = false, $field = '' ) {
		if ( ! $booking ) {
			wp_send_json_error( $message );
		}
		// Booking callers parse a text response; preserve their existing envelope and content type.
		$response = array( 'success' => false, 'message' => $message );
		if ( $field ) {
			$response['fieldErrors'][ $field . '_error' ] = $message;
		}
		echo wp_json_encode( $response );
		wp_die();
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
		if ( ! current_user_can( $this->args['caps'] ) ) {
			wp_die( esc_html__( 'You do not have permission to access this resource.', 'tourfic' ) );
		}
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
		$booking_form_title = apply_filters('tourfic_' . $this->args["name"] . '_backend_booking_form_title', $booking_form_title);
		$booking_form_class = apply_filters('tourfic_' . $this->args["name"] . '_backend_booking_form_class', $booking_form_class);

		// before form action hook
		do_action( 'tourfic_before_' . $this->args["name"] . '_backend_booking_form');

		?>
		
        <form method="post" action="" class="<?php echo esc_attr( $booking_form_class ); ?>" enctype="multipart/form-data">
            <h1><?php echo esc_html( $booking_form_title ); ?></h1>
			<?php
			$tf_backend_booking_form_fields = apply_filters( 'tourfic_' . $this->args["name"] . '_backend_booking_form_card', $this->settings);
			foreach ( $tf_backend_booking_form_fields as $id => $tf_backend_booking_form_field ) : ?>
				<?php do_action( 'tourfic_before_' . $this->args["name"] . '_each_backend_booking_form_card'); ?>
                <div class="tf-backend-booking-card-wrap">
                    <h3 class="tf-backend-booking-card-title"><?php echo esc_html( $tf_backend_booking_form_field['title'] ); ?></h3>

                    <div class="tf-booking-fields-wrapper">
                        <div class="tf-booking-fields">
							<?php
							if ( ! empty( $tf_backend_booking_form_field['fields'] ) ):
								foreach ( $tf_backend_booking_form_field['fields'] as $field ) :
									$field = $this->scope_listing_field( $field );

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
		do_action( 'tourfic_after_' . $this->args["name"] . '_backend_booking_form');
		
		?>
		<?php
		echo '</div>';
	}

	abstract protected function check_avaibility_callback();

	abstract protected function check_price_callback();

	abstract protected function backend_booking_callback();

}
