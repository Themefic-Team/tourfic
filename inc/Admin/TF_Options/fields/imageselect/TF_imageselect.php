<?php
// don't load directly

use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_imageselect' ) ) {
	class TF_imageselect extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			if ( isset( $this->field['options'] ) ) {
				$multiple = isset( $this->field['multiple'] ) ? $this->field['multiple'] : false;
				$img_width = ( isset( $this->field['img-width'] ) && $this->field['img-width'] ) ? $this->field['img-width']. 'px' : '240px';
				$img_height = ( isset( $this->field['img-height'] ) && $this->field['img-height'] ) ? $this->field['img-height']. 'px' : '180px';
				Helper::tf_var_dump($this->value);
				echo '<ul class="tf-image-radio-group">';
				foreach ( $this->field['options'] as $key => $value ) {
					if($multiple){
						$checked = ( is_array( $this->value ) && in_array( $key, $this->value ) ) ? ' checked' : '';
					} else {
						$checked = $key == $this->value ? ' checked' : '';
					}
                    $disable_checked = !empty($value['disabled']) ? ' disabled' : '';
					?>
                    <li class="<?php echo isset( $value['is_pro'] ) && $value['is_pro'] ? 'tf-pro-item' : '' ?>">
                        <label class="tf-image-checkbox">
							<?php if ( isset( $value['is_pro'] ) && $value['is_pro'] ): ?>
                                <span><?php echo esc_html__('Pro', 'tourfic') ?></span>
                            <?php else : 
								if($multiple){
									echo '<input type="checkbox" id="' . esc_attr( $this->field_name() ) . '[' . esc_attr( $key ) . ']" name="' . esc_attr( $this->field_name() ) . '[]" data-depend-id="' . esc_attr( $this->field['id'] ) . '' . esc_attr( $this->parent_field ) . '" value="' . esc_attr( $key ) . '" ' . esc_attr( $checked ) . ' ' . esc_attr($disable_checked). ' '. wp_kses_post( $this->field_attributes() ) . '/>';
								} else {
									echo '<input type="radio" id="' . esc_attr( $this->field_name() ) . '[' . esc_attr( $key ) . ']" name="' . esc_attr( $this->field_name() ) . '" data-depend-id="' . esc_attr( $this->field['id'] ) . '' . esc_attr( $this->parent_field ) . '" value="' . esc_attr( $key ) . '" ' . esc_attr( $checked ) . ' ' . esc_attr($disable_checked). ' '. wp_kses_post( $this->field_attributes() ) . '/>';
								}
								?>
							<?php endif; ?>

                            <img 
								src="<?php echo esc_url( $value['url'] ); ?>" 
								alt="<?php echo esc_attr( $value['title'] ); ?>" 
								style="width: <?php echo esc_attr($img_width); ?>; height: <?php echo esc_attr($img_height); ?>"
							/>
							<span class="tf-circle-check"></span>
							<?php if(!empty($value['title'])){ ?>
								<span class="tf-template-title"><?php echo esc_html($value['title']); ?></span>
							<?php } ?>
                        </label>
                    </li>
					<?php
				}
				echo '</ul>';
			}
		}
	}
}