<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Field: text
 */
if ( ! class_exists( 'TF_image' ) ) {
	class TF_image extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {
			echo '<div class="tf-fieldset-media-preview tf-fieldset-media-preview ' . esc_attr( str_replace(array("[","]","-"),"_",esc_attr( $this->field_name() ) ) ) . '">';
			if(!empty($this->value)){
			echo '<div class="tf-image-close" tf-field-name='. esc_attr( $this->field_name() ) .'>✖</div><img src='. esc_url($this->value) . ' />
			';
			}
			echo '</div>
			<div class="tf-fieldset-media">
			<input type="text" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_url($this->value) . '" disabled="disabled" /><a href="#" tf-field-name="' . esc_attr( $this->field_name() ) . '" class="tf-media-upload button button-primary button-large">' . esc_html( "Upload","tourfic" ) . '</a></div>
			<input type="hidden" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_url($this->value) . '"  />';
		}

		//sanitize
		public function sanitize() {
			return sanitize_url( $this->value );
		}

	}
}