<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tourfic_callback' ) ) {
	class Tourfic_callback extends Tourfic_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			if ( isset( $this->field['function'] ) ) {
                call_user_func( $this->field['function'] );
            }
		}

	}
}