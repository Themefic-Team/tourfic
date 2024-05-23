<?php

namespace Tourfic\Admin\Backend_Booking;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use \Tourfic\Core\TF_Backend_Booking;

class TF_Apartment_Backend_Booking extends TF_Backend_Booking {

    use \Tourfic\Traits\Singleton;

    protected array $args = array(
        'name' => 'apartment',
        'prefix' => 'tf-apartment',
        'post_type' => 'tf_apartment',
        'caps' => 'edit_tf_apartments'
    );

    public function __construct(){

        parent::__construct($this->args);

        $this->set_settings_fields();

        // actions
        add_action('wp_ajax_tf_check_available_apartment', array($this, 'wp_ajax_tf_check_available_apartment_callback'));
        add_action('wp_ajax_tf_check_apartment_aditional_fees', array($this, 'wp_ajax_tf_check_apartment_aditional_fees_callback'));
    }

    private function get_apartment_meta_options($id, $key = '') {
		if(empty($id) || $id == null || $id == "undefined" || $id == 0) {
			return;
		}

		$meta = get_post_meta( $id, 'tf_apartment_opt', true );

		if(!empty($key)) {
			return $meta[$key];
		} else {
			return $meta;
		}
	}

    function set_settings_fields() {
        $this->settings = array(
            'tf_booking_fields' => array(
                'title'  => esc_html__( 'Booking Information', 'tourfic' ),
                'fields' => array(
                    array(
                        'id'      => 'tf_apartment_date',
                        'label'   => esc_html__( 'Date', 'tourfic' ),
                        'class'   => 'tf-field-class',
                        'type'    => 'date',
                        'format'  => 'Y/m/d',
                        'range'   => true,
                        'minDate' => 'today',
                    ),
                    array(
                        'id'          => 'tf_available_apartments',
                        'label'       => esc_html__( 'Available Apartments', 'tourfic' ),
                        'type'        => 'select2',
                        'class'   => 'tf-field-class',
                        'options'     => 'posts',
                        'query_args'  => array(
                            'post_type'      => $this->args['post_type'],
                            'posts_per_page' => - 1,
                            'post_status'    => 'publish',
                        ),
                        'field_width' => 50,
                    ),
                    array(
                        'id'          => 'tf_apartment_additional_fees',
                        'label'       => esc_html__( 'Additional Fees', 'tourfic' ),
                        'class'       => 'tf-field-class',
                        'type'        => 'select2',
                        'options'     => 'posts',
                        'attributes'  => array( 'disabled' => 'disabled' ),
                        'placeholder' => esc_html__( 'Please choose the apartment first', 'tourfic' ),
                        'field_width' => 50,
                    ),
                    array(
                        'id'          => 'tf_apartment_adults_number',
                        'label'       => esc_html__( 'Adults', 'tourfic' ),
                        'type'        => 'number',
                        'attributes'  => array(
                            'min' => 1,
                        ),
                        'field_width' => 33,
                    ),
                    array(
                        'id'          => 'tf_apartment_children_number',
                        'label'       => esc_html__( 'Children', 'tourfic' ),
                        'type'        => 'number',
                        'attributes'  => array(
                            'min' => '0',
                        ),
                        'field_width' => 33,
                    ),
                    array(
                        'id'          => 'tf_apartment_infant_number',
                        'label'       => esc_html__( 'Infant', 'tourfic' ),
                        'type'        => 'number',
                        'attributes'  => array(
                            'min' => '0',
                        ),
                        'field_width' => 33,
                    ),
                ),
            ),
        );
        $this->set_settings( $this->settings );
    }

    public function wp_ajax_tf_check_available_apartment_callback() {
		// Add nonce for security and authentication.
		check_ajax_referer('updates', '_nonce');

		$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$from = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$to   = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

		$loop = new \WP_Query( array(
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );

		$period = '';
		if ( ! empty( $from ) && ! empty( $to ) ) {
			$period = new \DatePeriod(
				new \DateTime( $from ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $to ) ? $to : '23:59:59' )
			);
		}

		$check_in_out = "$from - $to";

		if ( $loop->have_posts() ) {
			$not_found = [];
			while ( $loop->have_posts() ) {
				$loop->the_post();
				tf_filter_apartment_by_date( $period, $not_found, array( 1, 0, 0, $check_in_out ) );
			}

			$tf_total_filters = [];

			foreach ( $not_found as $filter_post ) {
				if ( $filter_post['found'] != 1 ) {
					$tf_total_filters[ $filter_post['post_id'] ] = get_the_title( $filter_post['post_id'] );
				}
			}
		}
		wp_reset_postdata();

		wp_send_json_success( array(
			'apartments' => $tf_total_filters,
		) );
	}

    public function wp_ajax_tf_check_apartment_aditional_fees_callback() {
		// Add nonce for security and authentication.
		check_ajax_referer('updates', '_nonce');

		$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : 0;
		$from = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$to   = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

			// Additional Fees data
		
			if($apartment_id != 0) {
			$additional_fees = !empty( $this->get_apartment_meta_options( $apartment_id, "additional_fees" ) ) ? $this->get_apartment_meta_options( $apartment_id, "additional_fees" ) : array();
			}

		$all_fees = [];
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($additional_fees)) {
			if ( count( $additional_fees ) > 0 ) {
				foreach ( $additional_fees as $fees ) {
					$all_fees[] = array(
						"label" => !empty($fees["additional_fee_label"]) ? $fees["additional_fee_label"] : '',
						"fee"   => !empty($fees["additional_fee"]) ? $fees["additional_fee"] : 0,
						"price" =>!empty($fees["additional_fee"]) ? wc_price( $fees["additional_fee"] ) : wc_price( 0 ),
						"type"  => !empty( $fees["fee_type"] ) ? $fees["fee_type"] : '',
					);
				}
			}
		} else {
			$additional_fee_label = !empty( $this->get_apartment_meta_options( $apartment_id ) [ "additional_fee_label" ] ) ? $this->get_apartment_meta_options( $apartment_id, "additional_fee_label" ) : '';
			$additional_fee_amount = !empty( $this->get_apartment_meta_options( $apartment_id )["additional_fee"] ) ? $this->get_apartment_meta_options( $apartment_id, "additional_fee" ) : 0;
			$additional_fee_type = !empty( $this->get_apartment_meta_options( $apartment_id )[ "fee_type" ] ) ? $this->get_apartment_meta_options( $apartment_id, "fee_type" ) : '';

			if($additional_fee_amount != 0) {
				$all_fees[] = array(
					"label" => $additional_fee_label ?? "",
					"fee"   => $additional_fee_amount,
					"price" => wc_price( $additional_fee_amount ),
					"type"  => $additional_fee_type,
				);
			}
		}

		wp_reset_postdata();

		if(!empty($all_fees)) {
			wp_send_json_success( array(
				'additional_fees' => $all_fees,
			) );
		} else {
			wp_send_json_error( array(
				'additional_fees' => array(
					'msg' => esc_html__( 'There are no additional fees', 'tourfic' )
				)
			) );
		}
	}

    function check_avaibility_callback(){}
    function check_price_callback(){}
    function booking_callback(){}
}