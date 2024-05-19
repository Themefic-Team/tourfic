<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_radio' ) ) {
	class TF_radio extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {
			if ( isset( $this->field['options'] ) ) {
				$inline = ( isset( $this->field['inline'] ) && $this->field['inline'] ) ? 'tf-inline' : '';
				echo '<ul class="tf-radio-group ' . esc_attr( $inline ) . '">';
				foreach ( $this->field['options'] as $key => $value ) {
					$checked = $key == $this->value ? ' checked' : '';
					echo '<li><input type="radio" id="' . esc_attr($this->field_name()) . '[' . esc_attr($key) . ']" name="' . esc_attr($this->field_name()) . '" data-depend-id="' . esc_attr( $this->field['id'] ) . '' . esc_attr($this->parent_field) . '" value="' . esc_attr( $key ) . '" ' . esc_attr($checked) . ' '. wp_kses_post($this->field_attributes()) .'/><label for="' . esc_attr($this->field_name()) . '[' . esc_attr($key) . ']">' . esc_attr($value) . '</label></li>';
				}
				echo '</ul>';
			} else {
				echo '<input type="radio" id="' . esc_attr($this->field_name()) . '" name="' . esc_attr($this->field_name()) . '" data-depend-id="' . esc_attr( $this->field['id'] ) . '' . esc_attr($this->parent_field) . '" value="1" ' . checked( esc_attr($this->value), 1, false ) . ' '. wp_kses_post($this->field_attributes()) .'/><label for="' . esc_attr($this->field_name()) . '">' . esc_html($this->field['title']) . '</label>';
			}
		}
	}
}