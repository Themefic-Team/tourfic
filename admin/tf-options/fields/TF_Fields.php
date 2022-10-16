<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Fields' ) ) {
	class TF_Fields {

		public function __construct( $field = array(), $value = '', $settings_id = '' ) {
			$this->field       = $field;
			$this->value       = $value;
			$this->settings_id = $settings_id;
		}

		public function field_name() {

			$field_id   = ( ! empty( $this->field['id'] ) ) ? $this->field['id'] : '';
			$field_name = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $field_id . ']' : $field_id;

			return $field_name;

		}
	}
}