<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Field: text
 */
if ( ! class_exists( 'TF_image' ) ) {
	class TF_image extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {
			$type = ( ! empty( $this->field['type'] ) ) ? $this->field['type'] : 'text';
			echo '<div class="tf-fieldset-media-preview ' . str_replace(array("[","]"),"_",esc_attr( $this->field_name() ) ) . '"></div><div class="tf-fieldset-media">
			<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" placeholder="'. $this->field['placeholder'] .'" value="' . $this->value . '"  /><a href="#" tf-field-name="' . esc_attr( $this->field_name() ) . '" class="tf-media-upload button button-primary button-large">upload</a></div>';
		}

		//sanitize
		public function sanitize() {
			var_dump($this->value);
			wp_die();
			return sanitize_url( $this->value );
		}

	}
}