<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Field: text
 */
if ( ! class_exists( 'TF_checkbox' ) ) {
	class TF_checkbox extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {
			if ( isset( $this->field['options'] ) ) {
				$inline = ( isset( $this->field['inline'] ) && $this->field['inline'] ) ? 'tf-inline' : '';
				echo '<ul class="tf-checkbox-group ' . esc_attr( $inline ) . '">';
				foreach ( $this->field['options'] as $key => $value ) {
					$checked = ( is_array( $this->value ) && in_array( $key, $this->value ) ) ? ' checked' : '';
					echo '<li><label><input type="checkbox" name="' . $this->field_name() . '[]" value="' . esc_attr( $key ) . '" ' . $checked . '/>' . $value . '</label></li>';
				}
				echo '</ul>';
			} else {
				echo '<label><input type="checkbox" name="' . $this->field_name() . '" value="1" ' . checked( $this->value, 1, false ) . '> ' . $this->field['label'] . '</label>';
			}
		}

		public function sanitize() {
			$value = ( is_array( $this->value ) ) ? array_map( 'sanitize_text_field', $this->value ) : sanitize_text_field( $this->value );

			return $value;
		}
	}
}