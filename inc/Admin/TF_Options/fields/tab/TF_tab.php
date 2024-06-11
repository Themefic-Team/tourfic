<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_text' ) ) {
	class TF_tab extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			?>
            <div id="<?php echo isset( $this->field['id'] ) ? esc_attr( $this->field['id'] ) : '' ?>" class="tf-tablist">

				<?php if ( count( $this->field['tabs'] ) > 1 ): ?>
                    <ul class="tf-nav-tabs">
						<?php if ( isset( $this->field['tabs'] ) && is_array( $this->field['tabs'] ) ): ?>
							<?php foreach ( $this->field['tabs'] as $key => $value ): ?>
                                <li class="tf-tab-item <?php if ( $key == 0 ) {
									echo "show";
								} ?>" data-tab-id="<?php if ( isset( $value['id'] ) ) {
									echo esc_attr( $value['id'] );
								} ?>"><?php echo esc_html($value['title']) ?></li>
							<?php endforeach; ?>
						<?php endif; ?>
                    </ul>
				<?php endif; ?>
                <div class="tf-tab-field-content">
					<?php if ( isset( $this->field['tabs'] ) && is_array( $this->field['tabs'] ) ): ?>
						<?php foreach ( $this->field['tabs'] as $key => $value ): ?>
                            <div class="tf-tab-item-content <?php echo $key == 0 ? "show" : '' ?>" data-tab-id="<?php echo isset( $value['id'] ) ? esc_attr( $value['id'] ) : '' ?>">
								<?php
								
								foreach ( $value['fields'] as $key => $field ) {
									
									$parent  = '[' . $this->field['id'] . ']';
									$default = isset( $field['default'] ) ? $field['default'] : '';
									$value   = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : $default;

									if ( ! empty( $this->value ) ) {
										
										$data = ( ! is_array( $this->value ) ) ? unserialize( $this->value ) : $this->value;
										if ( is_array( $data ) ) {
											if ( isset( $data[ $field['id'] ] ) ) {

												$value = ( isset( $field['id'] ) ) ? $data[ $field['id'] ] : '';

												$value = ($field['type'] == 'text' || $field['type'] == 'textarea') ? stripslashes($value) : $value;
											} else {
												$value = '';
											}
										}
									}
									
									// sanitize Wp Editor Field
									$value = ( $field['type'] == 'editor' ) ? wp_kses_post($value) : $value;

									$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
									$tf_option->field( $field, $value, $this->settings_id, $parent );
								}
								?>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
                </div>
            </div>
			<?php
		}
		public function sanitize() {
			return $this->value;
		}
	}		

}