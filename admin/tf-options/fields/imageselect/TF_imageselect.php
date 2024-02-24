<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_imageselect' ) ) {
	class TF_imageselect extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {
			if ( isset( $this->field['options'] ) ) {
				$inline = ( isset( $this->field['inline'] ) && $this->field['inline'] ) ? 'tf-inline' : '';
				echo '<ul class="tf-image-radio-group ' . esc_attr( $inline ) . '">';
				foreach ( $this->field['options'] as $key => $value ) {
					$checked = $key == $this->value ? ' checked' : '';
                    ?>
                    <li>
                    <label class="tf-image-checkbox">
                    <?php echo '<input type="radio" id="' . $this->field_name() . '[' . $key . ']" name="' . $this->field_name() . '" data-depend-id="' . esc_attr( $this->field['id'] ) . '' . $this->parent_field . '" value="' . esc_attr( $key ) . '" ' . $checked . ' '. $this->field_attributes() .'/>';
                    ?>
                        <img src="<?php echo esc_url($value['url']); ?>" alt="<?php echo esc_attr($value['title']); ?>">
                    </label>  
                    </li>

                <?php
				}
				echo '</ul>';
			}
		}
	}
}