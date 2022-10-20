<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_select2' ) ) {
	class TF_select2 extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {

			$args = wp_parse_args( $this->field, array(
				'placeholder' => '',
				'multiple'    => false,
			) );

			$placeholder = ( ! empty( $args['placeholder'] ) ) ? $args['placeholder'] : '';
			$multiple    = ( ! empty( $args['multiple'] ) ) ? 'multiple' : '';

			echo '<select name="' . $this->field_name() . '" id="' . esc_attr( $this->field_name() ) . '" class="tf-select2" data-placeholder="' . esc_attr( $placeholder ) . '" ' . $multiple . '>';
			foreach ( $this->field['options'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->value, $key, false ) . '>' . esc_html( $value ) . '</option>';
			}
			echo '</select>';
		}

	}
}