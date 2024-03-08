<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_color' ) ) {
	class TF_color extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			$color_value  = ( ! is_array( $this->value ) ) ? unserialize( $this->value ) : $this->value;

			if ( isset( $this->field['colors'] ) && $this->field['multiple'] ) {
				$inline = ( isset( $this->field['inline'] ) && $this->field['inline'] ) ? 'tf-inline' : '';
				echo '<ul class="tf-color-group ' . esc_attr( $inline ) . '">';

				foreach ( $this->field['colors'] as $key => $value ) {
					$_value = ( ! empty( $color_value[ $key ] ) ) ? $color_value[ $key ] : '';
					echo '<li>';
					echo '<label for="' . esc_attr( $this->field_name() ) . '[' . esc_attr($key) . ']">' . esc_html( $value ) .'</label>';
					echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '[' . esc_attr($key) . ']" id="' . esc_attr( $this->field_name() ) . '[' . esc_attr($key) . ']" value="' . esc_attr( $_value ) . '" class="tf-color" '. wp_kses_post($this->field_attributes()) .'/>';
					echo '</li>';
				}
				echo '</ul>';
			} else {
				echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $color_value ) . '" class="tf-color" '. wp_kses_post($this->field_attributes()) .'/>';
			}
		}

	}
}