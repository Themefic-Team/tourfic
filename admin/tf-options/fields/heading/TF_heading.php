<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_heading' ) ) {
	class TF_heading extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {
			echo !empty($this->field['content']) ? wp_kses_post( $this->field['content'] ) : '';
		}

	}
}