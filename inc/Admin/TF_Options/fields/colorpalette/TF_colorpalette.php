<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_colorpalette' ) ) {
	class TF_colorpalette extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			if ( isset( $this->field['options'] ) ) {
				$inline = ( isset( $this->field['inline'] ) && $this->field['inline'] ) ? 'tf-inline' : '';
				echo '<ul class="tf-colors-radio-group ' . esc_attr( $inline ) . '">';
				foreach ( $this->field['options'] as $key => $value ) {
					$checked = $key == $this->value ? ' checked' : '';
                    $disable_checked = !empty($value['disabled']) ? ' disabled' : '';
					?>
                    <li class="<?php echo isset( $value['is_pro'] ) && $value['is_pro'] ? 'tf-pro-item' : '' ?>">
                        <label class="tf-colors-checkbox">

                            <div class="tf-colors-box">
                                <?php if(!empty($value['colors'])){
                                    foreach($value['colors'] as $color){ ?>
                                    <span style="background-color: <?php echo esc_attr($color); ?>"></span>
                                    <?php
                                    }
                                }
                                ?>
                            </div>

                            <div class="tf-color-footer">
                                <?php if(!empty($value['title'])){ ?>
                                <span class="tf-template-title"><?php echo esc_html($value['title']); ?></span>
                                <?php } ?>

                                <?php echo '<input type="radio" id="' . esc_attr( $this->field_name() ) . '[' . esc_attr( $key ) . ']" name="' . esc_attr( $this->field_name() ) . '" data-depend-id="' . esc_attr( $this->field['id'] ) . '' . esc_attr( $this->parent_field ) . '" value="' . esc_attr( $key ) . '" ' . esc_attr( $checked ) . ' ' . esc_attr($disable_checked). ' '. wp_kses_post( $this->field_attributes() ) . '/>'; ?>
                                <div class="tf-color-checkmark"></div>
                            </div>

                        </label>
                    </li>

					<?php
				}
				echo '</ul>';
			}
		}
	}
}