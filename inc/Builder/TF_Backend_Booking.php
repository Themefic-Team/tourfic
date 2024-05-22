<?php

namespace Tourfic\Builder;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Backend booking core class
 * @package Tourfic\Builder
 */

 abstract class TF_Backend_Booking {

    protected array $actions = [];
    protected array $args = [];
    protected array $settings = array();

    private array $booking_customers_fields;

    final function customers_fields() {
        $current_user = wp_get_current_user();
        $this->booking_customers_fields = array (
            'tf_booking_customer_fields' => array(
				'title'  => esc_html__( 'Customer Information', 'tourfic' ),
				'fields' => array(
					array(
						'id'         => $this->args["name"] . '_booked_by',
						'label'      => esc_html__( 'Booked By', 'tourfic' ),
						'type'       => 'text',
						'default'    => $current_user->display_name ?: $current_user->user_login,
						'attributes' => array(
							'readonly' => 'readonly',
						),
					),
					array(
						'id'          => $this->args["name"] . '_customer_first_name',
						'label'       => esc_html__( 'First Name', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer First Name', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => $this->args["name"] . '_customer_last_name',
						'label'       => esc_html__( 'Last Name', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Last Name', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => $this->args["name"] . '_customer_email',
						'label'       => esc_html__( 'Email', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Email', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => $this->args["name"] . '_customer_phone',
						'label'       => esc_html__( 'Phone', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Phone', 'tourfic' ),
						'field_width' => 50,
					),
					array(
						'id'          => $this->args["name"] . '_customer_country',
						'label'       => esc_html__( 'Country / Region', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Country', 'tourfic' ),
						'field_width' => 33.33,
					),
					array(
						'id'          => $this->args["name"] . '_customer_address',
						'label'       => esc_html__( 'Address', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Address', 'tourfic' ),
						'field_width' => 33.33,
					),
					array(
						'id'          => $this->args["name"] . '_customer_address_2',
						'label'       => esc_html__( 'Address 2', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Address 2', 'tourfic' ),
						'field_width' => 33.33,
					),
					array(
						'id'          => $this->args["name"] . '_customer_city',
						'label'       => esc_html__( 'Town / City', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer City', 'tourfic' ),
						'field_width' => 33,
					),
					array(
						'id'          => $this->args["name"] . '_customer_state',
						'label'       => esc_html__( 'State', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer State', 'tourfic' ),
						'field_width' => 33,
					),
					array(
						'id'          => $this->args["name"] . '_customer_zip',
						'label'       => esc_html__( 'Postcode / ZIP', 'tourfic' ),
						'type'        => 'text',
						'placeholder' => esc_html__( 'Enter Customer Zip', 'tourfic' ),
						'field_width' => 33,
					),
				),
			),
        );
    }

    public function set_actions(array $actions) {
        $this->actions = $actions;
        return $this;
    }

    public function set_settings(array $settings) {
        $this->settings = array_merge($this->booking_customers_fields, $settings);
        return $this;
    }

    public function __construct( ) {
        foreach( $this->actions as $action ) {
            add_action( $action, [ $this, $action . '_callback' ] );
        }
    }

    public function check_avaibility_callback() {
        // Check avaibility
    }
    public function check_price_callback() {
        // Check Check and calculate price
    }
    public function booking_callback() {
        // all booking process
    }

}