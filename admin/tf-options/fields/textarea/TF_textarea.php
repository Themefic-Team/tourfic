<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_textarea' ) ) {
	class TF_textarea extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {
			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
			echo '<textarea name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '"' . $placeholder . ' '. $this->field_attributes() .'>' . $this->value . '</textarea>';
		}

	}
}