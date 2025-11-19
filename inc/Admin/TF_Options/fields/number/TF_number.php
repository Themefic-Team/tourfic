<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_number' ) ) {
	class TF_number extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '', $related_value = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field, $related_value );
		}

		public function render() {
			$type = ( ! empty( $this->field['type'] ) ) ? $this->field['type'] : 'number';
			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
			$related_placeholder = ( ! empty( $this->field['related_placeholder'] ) ) ? 'placeholder="' . $this->field['related_placeholder'] . '"' : '';

			if(empty($this->field['range']) && !empty($this->field['related'])){ 
				echo '<div class="tf-unit-price-box"><input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr($this->value) . '" ' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'/>';

				echo '<select name="' . esc_attr($this->related_field_name()) . '" id="' . esc_attr( $this->related_field_name() ) . '">';

				if( isset($this->field['related_options']) && is_array( $this->field['related_options'] )) {
					foreach ( $this->field['related_options'] as $key => $value ) {
						if($key !== '') {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->related_value, $key, false ) . '>' . esc_html( $value ) . '</option>';
						} else {
							//disable empty value
							echo '<option value="" disabled>' . esc_html( $value ) . '</option>';
						}
					}
				}
				echo '</select></div>';
			}else if(empty($this->field['related']) && !empty($this->field['range'])){ 
				$field_icon = !empty($this->field['icon']) ? '<i class="'.$this->field['icon'].'"></i>' : '';
				echo '<div class="tf-number-range">
				<div class="tf-number-field-box">'. wp_kses_post($field_icon) . '
				<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr($this->value) . '" ' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'/></div>';

				echo '<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M15.5 6.66797L18.8333 10.0013L15.5 13.3346" stroke="#95A3B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M2.1665 10H18.8332" stroke="#95A3B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>';

				echo '<div class="tf-number-field-box">'. wp_kses_post($field_icon) . '
				<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->related_field_name() ) . '" id="' . esc_attr( $this->related_field_name() ) . '" value="' . esc_attr($this->related_value) . '" ' . wp_kses_post($related_placeholder) . ' '. wp_kses_post($this->field_attributes()) .'/>';
				echo '</div></div>';
			}else{
				echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr($this->value) . '" ' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'/>';
			}
		}

	}
}