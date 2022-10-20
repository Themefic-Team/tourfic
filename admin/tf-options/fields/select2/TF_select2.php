<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_select2' ) ) {
	class TF_select2 extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {

			echo '<select name="' . $this->field_name() . '" id="' . esc_attr( $this->field_name() ) . '" class="tf-select" multiple="multiple">';
			if ( ! empty( $this->field['placeholder'] ) ) {
				echo '<option value="">' . esc_html( $this->field['placeholder'] ) . '</option>';
			}
			foreach ( $this->field['options'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->value, $key, false ) . '>' . esc_html( $value ) . '</option>';
			}
			echo '</select>';
		}

	}
}