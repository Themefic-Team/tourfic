<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_callback' ) ) {
	class TF_callback extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			if ( isset( $this->field['function'] ) && is_callable( $this->field['function'] )) {
                call_user_func( $this->field['function'] );
            } else {
				if( is_array( $this->field['function'] ) ) {
					error_log("TF_callback Error: Callback not callable. Value: " . print_r($this->field['function'], true));
					echo '<div class="error"><p>' . esc_html__( 'Callback function is not callable.', 'tourfic' ) . '</p></div>';
				} else {
					error_log("TF_callback Error: Function {$this->field['function']} not found.");
					echo '<div class="error"><p>' . sprintf( esc_html__( 'Function %s not found.', 'tourfic' ), esc_html( $this->field['function'] ) ) . '</p></div>';
				}
			}
		}

	}
}