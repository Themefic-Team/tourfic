<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

use Tourfic\Classes\Helper;

/**
 *
 * Field: repeater
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'TF_Repeater' ) ) {
	class TF_Repeater extends TF_Fields {
		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field);
		}
		public function render() {
			$max_index = 0;
            $label = ( ! empty( $this->field['label'] ) ) ? $this->field['label'] : '';
            $field_title = ( ! empty( $this->field['field_title'] ) ) ? $this->field['field_title'] : $label;

			if ( ! empty( $this->value ) ){
				
				if(!is_array($this->value)){
					$tf_rep_value = preg_replace_callback('!s:(\d+):"(.*?)";!', function ($match) {
						return 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $this->value);

					if (@unserialize($tf_rep_value) !== false || $tf_rep_value === 'b:0;') {
						$data = @unserialize($tf_rep_value);
					} else {
						$data = [];
					}
				}else{
					$data = $this->value;
				}
				
				$max_index = ! empty($data) && is_array($data) ? max(array_keys($data)) : 0;
			}
			?>
            <div id="tf-repeater-1" class="tf-repeater <?php echo esc_attr($this->field['id']);?>" data-max-index="<?php echo esc_attr($max_index); ?>">
                <div class="tf-repeater-wrap tf-repeater-wrap-<?php echo esc_attr($this->field['id']);?>">
					<?php if ( ! empty( $this->value ) ):
						$num = 0;
					 	if(is_array($data)):
							
							foreach ( $data as $key => $value ) :
							?>
                            <div class="tf-single-repeater tf-single-repeater-<?php echo esc_attr($this->field['id']);?>">
							<input type="hidden" name="tf_parent_field" value="<?php echo esc_attr($this->parent_field); ?>">
							<input type="hidden" name="tf_repeater_count" value="<?php echo esc_attr($key); ?>">
							<input type="hidden" name="tf_current_field" value="<?php echo esc_attr($this->field['id']);?>">
								<div class="tf-repeater-header">
									<span class="tf-repeater-icon tf-repeater-icon-collapse">
										<i class="fa-solid fa-angle-down"></i>
									</span>
									<span class="tf-repeater-title"><?php echo !empty($value[$field_title]) && is_string($value[$field_title]) ? esc_html($value[$field_title]) : esc_html($label) ?>  </span>
									<div class="tf-repeater-icon-absulate">
										<span class="tf-repeater-icon tf-repeater-icon-move">
											<i class="fa-solid fa-up-down-left-right"></i>
										</span>
										<?php
										if(empty($this->field['drag_only']) || !$this->field['drag_only']){
										?>
										<span class="tf-repeater-icon tf-repeater-icon-clone" data-repeater-max = "<?php if(isset($this->field['max'])){ echo esc_attr($this->field['max']); }  ?>">
											<i class="fa-solid fa-copy"></i> 
										</span>
										<span class="tf-repeater-icon tf-repeater-icon-delete">
											<i class="fa-solid fa-trash"></i>
										</span>
										<?php } ?>
									</div>
								</div>
                                <div class="tf-repeater-content-wrap hide" style="display: none">
									<?php
									foreach ( $this->field['fields'] as $re_field ) :

										if($re_field['type'] == 'editor'){
											$re_field['wp_editor'] = 'wp_editor';
										}
										if($re_field['type'] == 'select2'){
											$re_field['select2'] = 'select2';
										}

										if(!empty($this->parent_field)){
											$parent_field = $this->parent_field.'[' . $this->field['id'] . '][' . $key . ']';
										}else{
											$parent_field = '[' . $this->field['id'] . '][' . $key . ']';
										}

										$id = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][00]' . '[' . $re_field['id'] . ']' : $this->field['id'] . '[00]' . '[' . $re_field['id'] . ']';

										$related_name = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['related_name'] . '][00]' . '[' . $re_field['related_name'] . ']' : $this->field['related_name'] . '[00]' . '[' . $re_field['related_name'] . ']'; 

										if ( isset( $tf_meta_box_value[ $id ] ) ) {
											$value = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : '';
										} else {
											$value = ( isset( $re_field['id'] ) && isset( $data[ $key ][ $re_field['id'] ] ) ) ? $data[ $key ][ $re_field['id'] ] : '';
										}

										if ( isset( $tf_meta_box_value[ $related_name ] ) ) {
											$related_value = isset( $tf_meta_box_value[ $related_name ] ) ? $tf_meta_box_value[ $related_name ] : '';
										} else {
											$related_value = ( isset( $re_field['related_name'] ) && isset( $data[ $key ][ $re_field['related_name'] ] ) ) ? $data[ $key ][ $re_field['related_name'] ] : '';
										}

										if(isset($re_field['validate']) && $re_field['validate'] == 'no_space_no_special'){
											//remove special characters, replace space with underscore and convert to lowercase
											$value = sanitize_title(str_replace(' ', '_', strtolower($value)));
										}

										$value = ($re_field['type'] == 'text' || $re_field['type'] == 'textarea') ? stripslashes($value) : $value;

										$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
										$tf_option->field( $re_field, $value, $this->settings_id, $parent_field, $related_value);
									endforeach;
									$num ++;
									?>
                                </div>
                            </div>
						<?php endforeach; endif; endif; ?>
                </div>
                <div class=" tf-single-repeater-clone tf-single-repeater-clone-<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">
                    <div class="tf-single-repeater tf-single-repeater-<?php echo esc_attr($this->field['id']);?>">

					<input type="hidden" name="tf_parent_field" value="<?php if(isset($this->parent_field)){ echo esc_attr($this->parent_field); }  ?>">
					<input type="hidden" name="tf_repeater_count" value="0">
					<input type="hidden" name="tf_current_field" value="<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">

						<div class="tf-repeater-header">
							<span class="tf-repeater-icon tf-repeater-icon-collapse">
								<i class="fa-solid fa-angle-up"></i> 
							</span>
							<span class="tf-repeater-title"><?php if(isset($this->field['label'])){ echo esc_html($this->field['label']); }  ?></span>
							<div class="tf-repeater-icon-absulate">
								<span class="tf-repeater-icon tf-repeater-icon-move">
									<i class="fa-solid fa-up-down-left-right"></i>
								</span>
								<?php
								if(empty($this->field['drag_only']) || !$this->field['drag_only']){
								?>
								<span class="tf-repeater-icon tf-repeater-icon-clone" data-repeater-max = "<?php if(isset($this->field['max'])){ echo esc_attr($this->field['max']); }  ?>">
									<i class="fa-solid fa-copy"></i> 
								</span>
								<span class="tf-repeater-icon tf-repeater-icon-delete">
									<i class="fa-solid fa-trash"></i>
								</span>
								<?php } ?>
							</div>
						</div>
                        <div class="tf-repeater-content-wrap">

							<?php foreach ( $this->field['fields'] as $key => $re_field ) {
								if(!empty($this->parent_field)){
									$parent = $this->parent_field.'[' . $this->field['id'] . '][00]';
								}else{
									$parent    = '[' . $this->field['id'] . '][00]';
								}
								$id        = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][00]' . '[' . $re_field['id'] . ']' : $this->field['id'] . '[00]' . '[' . $re_field['id'] . ']';
								$default = isset( $re_field['default'] ) ? $re_field['default'] : '';
                                $value     = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : $default;
								if(isset($re_field['validate']) && $re_field['validate'] == 'no_space_no_special'){
									//remove special characters, replace space with underscore and convert to lowercase
									$value = sanitize_title(str_replace(' ', '_', strtolower($value)));
								}
								$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
								$tf_option->field( $re_field, $value, '_____' . $this->settings_id, $parent );
							} ?>
                        </div>
                    </div>

                </div>
				<?php
				if(empty($this->field['drag_only']) || !$this->field['drag_only']){
				?>
                <div class="tf-repeater-add tf-repeater-add-<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">
				
					<span data-repeater-id = "<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>" data-repeater-max = "<?php if(isset($this->field['max'])){ echo esc_attr($this->field['max']); }  ?>" class="tf-repeater-icon tf-repeater-icon-add tf-repeater-add-<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">
						<?php
							if(isset($this->field['button_title']) && !empty($this->field['button_title'])){
								echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0_1017_2374)">
									<path d="M9.99984 18.3346C14.6022 18.3346 18.3332 14.6037 18.3332 10.0013C18.3332 5.39893 14.6022 1.66797 9.99984 1.66797C5.39746 1.66797 1.6665 5.39893 1.6665 10.0013C1.6665 14.6037 5.39746 18.3346 9.99984 18.3346Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M6.6665 10H13.3332" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M10 6.66797V13.3346" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
								<defs>
									<clipPath id="clip0_1017_2374">
									<rect width="20" height="20" fill="white"/>
									</clipPath>
								</defs>
								</svg>';
								echo esc_html($this->field['button_title']);
							}else{
								echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0_1017_2374)">
									<path d="M9.99984 18.3346C14.6022 18.3346 18.3332 14.6037 18.3332 10.0013C18.3332 5.39893 14.6022 1.66797 9.99984 1.66797C5.39746 1.66797 1.6665 5.39893 1.6665 10.0013C1.6665 14.6037 5.39746 18.3346 9.99984 18.3346Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M6.6665 10H13.3332" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M10 6.66797V13.3346" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
								<defs>
									<clipPath id="clip0_1017_2374">
									<rect width="20" height="20" fill="white"/>
									</clipPath>
								</defs>
								</svg>';
							}
						?>
						
					</span>
				</div>
				<?php } ?>
            </div>
			<?php

		}
		public function sanitize() {
			return $this->value;
		}
	}
}
