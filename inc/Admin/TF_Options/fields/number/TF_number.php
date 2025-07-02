<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_number' ) ) {
	class TF_number extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			$type = ( ! empty( $this->field['type'] ) ) ? $this->field['type'] : 'number';
			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
			if($this->field['related']){ ?>
				<div class="tf-unit-price-box">
				<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>" value="<?php echo esc_attr($this->value); ?>" <?php echo wp_kses_post($placeholder); ?><?php echo wp_kses_post($this->field_attributes()); ?> />
				</div>
			<?php 
				echo '<select name="' . esc_attr($this->field['related_name']) . '" id="' . esc_attr( $this->field['related_name'] ) . '">';

				if( isset($this->field['related_options']) && is_array( $this->field['related_options'] )) {
					foreach ( $this->field['related_options'] as $key => $value ) {
						if($key !== '') {
							echo '<option value="' . esc_attr( $key ) . '" >' . esc_html( $value ) . '</option>';
						} else {
							//disable empty value
							echo '<option value="" disabled>' . esc_html( $value ) . '</option>';
						}
					}
				}
				echo '</select>';
			}else{
				echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr($this->value) . '" ' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'/>';
			}
		}

	}
}