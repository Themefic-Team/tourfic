<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Field: text
 */
if ( ! class_exists( 'Tourfic_image' ) ) {
	class Tourfic_image extends Tourfic_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {
			$default = isset( $this->field['default'] ) ? $this->field['default'] : '';
			$value = !empty( $this->value ) ? $this->value : $default;
			echo '<div class="tf-fieldset-media-preview tf-fieldset-media-preview ' . esc_attr( str_replace(array("[","]","-"),"_",esc_attr( $this->field_name() ) ) ) . '">';
			if(!empty($value)){
			echo '<div class="tf-image-close" tf-field-name='. esc_attr( $this->field_name() ) .'>✖</div><img src='. esc_url($value) . ' />
			';
			}
			echo '</div>
			<div class="tf-fieldset-media">
			<input type="text" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_url($value) . '" disabled="disabled" /><a href="#" tf-field-name="' . esc_attr( $this->field_name() ) . '" class="tf-media-upload button button-primary button-large">' . esc_html__( "Upload","tourfic" ) . '</a></div>
			<input type="hidden" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_url($value) . '"  />';
		}

		//sanitize
		public function sanitize() {
			return sanitize_url( $this->value );
		}

	}
}