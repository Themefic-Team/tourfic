<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_color' ) ) {
	class TF_color extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr($this->value) . '" class="tf-color" data-alpha-enabled="true" />';
		}

	}
}