<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_map' ) ) {
	class TF_map extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
            echo '<div class="csf--map-search">';
            echo '<input type="text" name="' . esc_attr( $this->field_name( '[address]' ) ) . '" id="' . esc_attr( $this->field_name( '[address]' ) ) . '" placeholder="Search..." />';
            echo '</div>';
              
		}

	}
}