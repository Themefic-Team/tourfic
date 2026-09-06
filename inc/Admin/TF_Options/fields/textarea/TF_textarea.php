<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tourfic_textarea' ) ) {
	class Tourfic_textarea extends Tourfic_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {

			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . esc_attr( $this->field['placeholder'] ) . '"' : '';

			echo '<textarea name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" ' . wp_kses_post( $placeholder ) . ' ' . wp_kses_post( $this->field_attributes() ) . '>' . esc_textarea( $this->value ) . '</textarea>';
		}

		public function sanitize() {
			return wp_kses_post( $this->value );
		}
	}
}
