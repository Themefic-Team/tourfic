<?php
// don't load directly

use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_text' ) ) {
	class TF_tab extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			$tf_disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : [];
			?>
            <div id="<?php echo isset( $this->field['id'] ) ? esc_attr( $this->field['id'] ) : '' ?>" class="tf-tablist">

				<?php if ( count( $this->field['tabs'] ) > 1 ): ?>
                    <ul class="tf-nav-tabs">
						<?php if ( isset( $this->field['tabs'] ) && is_array( $this->field['tabs'] ) ): ?>
							<?php $active_tab = false; // Flag to track if an active tab has been set ?>
							<?php foreach ( $this->field['tabs'] as $key => $value ): ?>
								<?php 
								if(isset( $value['post_dependency'] ) && !empty( $value['post_dependency'] )){
									if(!empty( $tf_disable_services ) && in_array( $value['post_dependency'], $tf_disable_services )){
										continue;
									}
								}

								// Set the first visible tab as active
								$active_class = '';
								if (!$active_tab) {
									$active_class = 'show';
									$active_tab = true; // Mark that an active tab has been set
								}
								?>
                                <li class="tf-tab-item <?php echo esc_attr($active_class) ?>" 
								data-tab-id="<?php echo  isset( $value['id'] ) ? esc_attr( $value['id'] ) : ''?>">
									<?php echo esc_html($value['title']) ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
                    </ul>
				<?php endif; ?>
                <div class="tf-tab-field-content">
					<?php if ( isset( $this->field['tabs'] ) && is_array( $this->field['tabs'] ) ): ?>
						<?php $active_tab_content = false; // Flag to track if an active tab has been set ?>
						<?php foreach ( $this->field['tabs'] as $key => $value ): ?>
							<?php 
							if(isset( $value['post_dependency'] ) && !empty( $value['post_dependency'] )){
								if(!empty( $tf_disable_services ) && in_array( $value['post_dependency'], $tf_disable_services )){
									continue;
								}
							}

							// Set the first visible tab as active
							$active_class = '';
							if (!$active_tab_content) {
								$active_class = 'show';
								$active_tab_content = true; // Mark that an active tab has been set
							}
							?>
                            <div class="tf-tab-item-content <?php echo esc_attr($active_class) ?>" data-tab-id="<?php echo isset( $value['id'] ) ? esc_attr( $value['id'] ) : '' ?>">
								<?php
								$parent_id = !empty( $value['id'] ) ? $value['id'] : '';
								
								foreach ( $value['fields'] as $key => $field ) {
									
									$parent  = '[' . $this->field['id'] . ']';
									$default = isset( $field['default'] ) ? $field['default'] : '';
									$value   = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : $default;
									$layout_ids = array('single-hotel-layout', 'single-hotel-layout-part-1', 'single-hotel-layout-part-2', 'single-tour-layout', 'single-tour-layout-part-1', 'single-tour-layout-part-2', 'single-aprtment-layout-part-1', 'single-aprtment-layout-part-2', 'single-car-layout');

									if ( ! empty( $this->value ) ) {
										
										$data = ( ! is_array( $this->value ) ) ? unserialize( $this->value ) : $this->value;
										if ( is_array( $data ) ) {
											if ( isset( $data[ $field['id'] ] ) ) {

												if(!empty($field['id']) && in_array($field['id'], $layout_ids)){
													$value = !empty( $data[ $field['id'] ] ) ? $data[ $field['id'] ] : $default;
												} else {
													$value = ( isset( $field['id'] ) ) ? $data[ $field['id'] ] : '';
												}
												
												$value = ($field['type'] == 'text' || $field['type'] == 'textarea') ? stripslashes($value) : $value;
											} else {
												$value = $default;
											}
										}
									}
									
									// sanitize Wp Editor Field
									$value = ( $field['type'] == 'editor' ) ? wp_kses_post($value) : $value;

									$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
									$tf_option->field( $field, $value, $this->settings_id, $parent );
								}
								?>
								<?php do_action("tf-" . $parent_id . '-after-tab-content') ?>
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