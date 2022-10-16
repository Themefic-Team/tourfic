<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Field: select
 */
if ( ! class_exists( 'TF_select' ) ) {
	class TF_select extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {
			echo '<select name="' . $this->field_name() . '" id="' . esc_attr( $this->field_name() ) . '" class="tf-select">';
			foreach ( $this->field['options'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->value, $key, false ) . '>' . $value . '</option>';
			}
			echo '</select>';
		}

	}
}