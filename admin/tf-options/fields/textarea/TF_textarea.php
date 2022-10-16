<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Field: text
 */
if ( ! class_exists( 'TF_textarea' ) ) {
	class TF_textarea extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {
			echo '<textarea name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '">' . $this->value . '</textarea>';
		}

	}
}