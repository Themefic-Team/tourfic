<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use PHP_CodeSniffer\Util\Help;
use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_textarea' ) ) {
	class TF_textarea extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {

			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';

			if( $this->field['id'] == "booking-code" ) {
				echo '<textarea name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '"' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'>' . wp_kses( $this->value, Helper::tf_custom_wp_kses_allow_tags() ) . '</textarea>';
			} else {
				echo '<textarea name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '"' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'>' . esc_html($this->value) . '</textarea>';
			}
		}

		public function sanitize() {
			if( $this->field['id'] == "booking-code" ) {
				// return wp_kses( $this->value, Helper::tf_custom_wp_kses_allow_tags() );
				return $this->value;

				
			}else {
				return wp_kses_post( $this->value );
			}
		}

		private function tf_kses_allowed_tags() {
			$allowed_tags = wp_kses_allowed_html( 'post' );
			$allowed_tags['script'] = array(
				'src'   => true,
				'type'  => true,
				'class' => true,
				'id'    => true,
			);
			$allowed_tags['style'] = true;
		}
	}
}