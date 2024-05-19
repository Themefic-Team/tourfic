<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Fields' ) ) {
	class TF_Fields {

		public $field;
		public $value;
		public $settings_id;
		public $parent_field;

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

		public function field_attributes( $custom_atts = array() ) {

			$attributes = ( ! empty( $this->field['attributes'] ) ) ? $this->field['attributes'] : array();
			$attributes = wp_parse_args( $attributes, $custom_atts );

			$atts = '';
			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $key => $value ) {
					$atts .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
				}
			}

			return $atts;
		}

		//sanitize
		public function sanitize() {
			return stripslashes(sanitize_text_field( $this->value ));
		}


	}
}