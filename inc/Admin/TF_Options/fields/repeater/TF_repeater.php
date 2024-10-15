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
					$tf_rep_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
						return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $this->value );

					$data = unserialize( $tf_rep_value );
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
								if( "cont_custom_date" == $this->field['id'] ){
									$value[$field_title] = esc_html__('Custom Dates', 'tourfic');
								}
							?>
                            <div class="tf-single-repeater tf-single-repeater-<?php echo esc_attr($this->field['id']);?>">
							<input type="hidden" name="tf_parent_field" value="<?php echo esc_attr($this->parent_field); ?>">
							<input type="hidden" name="tf_repeater_count" value="<?php echo esc_attr($key); ?>">
							<input type="hidden" name="tf_current_field" value="<?php echo esc_attr($this->field['id']);?>">
								<div class="tf-repeater-header">
									<span class="tf-repeater-icon tf-repeater-icon-collapse">
										<i class="fa-solid fa-angle-down"></i>
									</span>
									<span class="tf-repeater-title"><?php echo !empty($value[$field_title]) ? esc_html($value[$field_title]) : esc_html($label) ?>  </span>
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
										if ( isset( $tf_meta_box_value[ $id ] ) ) {
											$value = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : '';
										} else {
											$value = ( isset( $re_field['id'] ) && isset( $data[ $key ][ $re_field['id'] ] ) ) ? $data[ $key ][ $re_field['id'] ] : '';
										}

										if(isset($re_field['validate']) && $re_field['validate'] == 'no_space_no_special'){
											//remove special characters, replace space with underscore and convert to lowercase
											$value = sanitize_title(str_replace(' ', '_', strtolower($value)));
										}

										$value = ($re_field['type'] == 'text' || $re_field['type'] == 'textarea') ? stripslashes($value) : $value;

										$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
										$tf_option->field( $re_field, $value, $this->settings_id, $parent_field);
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
								echo esc_html($this->field['button_title']);
							}else{
								echo '<i class="fa-solid fa-plus"></i>';
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
