<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Fields' ) ) {
	class TF_Fields {

		public function __construct( $field = array(), $value = '', $settings_id = '', $parent_field = '') {
			$this->field       = $field;
			$this->value       = $value;
			$this->settings_id = $settings_id;
			$this->parent_field = $parent_field;
		}

		public function field_name() {

			$field_id   = ( ! empty( $this->field['id'] ) ) ? $this->field['id'] : '';
			if(!empty($field_id)){ 
				$field_name = ( ! empty( $this->settings_id ) ) ? $this->settings_id . $this->parent_field . '[' . $field_id . ']' : $field_id;
			}else{ 
				$field_name = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $field_id . ']' : $field_id;
			}

			return $field_name;

		}

		//sanitize
		public function sanitize() {
			return sanitize_text_field( $this->value );
		}


	}
}