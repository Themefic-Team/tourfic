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
									<span class="tf-repeater-icon-move">
										<svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M4.83333 10.5C5.29357 10.5 5.66667 10.1269 5.66667 9.66667C5.66667 9.20643 5.29357 8.83333 4.83333 8.83333C4.3731 8.83333 4 9.20643 4 9.66667C4 10.1269 4.3731 10.5 4.83333 10.5Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M4.83333 4.66667C5.29357 4.66667 5.66667 4.29357 5.66667 3.83333C5.66667 3.3731 5.29357 3 4.83333 3C4.3731 3 4 3.3731 4 3.83333C4 4.29357 4.3731 4.66667 4.83333 4.66667Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M4.83333 16.3333C5.29357 16.3333 5.66667 15.9602 5.66667 15.5C5.66667 15.0398 5.29357 14.6667 4.83333 14.6667C4.3731 14.6667 4 15.0398 4 15.5C4 15.9602 4.3731 16.3333 4.83333 16.3333Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M9.83333 10.5C10.2936 10.5 10.6667 10.1269 10.6667 9.66667C10.6667 9.20643 10.2936 8.83333 9.83333 8.83333C9.3731 8.83333 9 9.20643 9 9.66667C9 10.1269 9.3731 10.5 9.83333 10.5Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M9.83333 4.66667C10.2936 4.66667 10.6667 4.29357 10.6667 3.83333C10.6667 3.3731 10.2936 3 9.83333 3C9.3731 3 9 3.3731 9 3.83333C9 4.29357 9.3731 4.66667 9.83333 4.66667Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M9.83333 16.3333C10.2936 16.3333 10.6667 15.9602 10.6667 15.5C10.6667 15.0398 10.2936 14.6667 9.83333 14.6667C9.3731 14.6667 9 15.0398 9 15.5C9 15.9602 9.3731 16.3333 9.83333 16.3333Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</span>
									<div class="tf-repeater-header-info">
										<span class="tf-repeater-title"><?php echo !empty($value[$field_title]) && is_string($value[$field_title]) ? esc_html($value[$field_title]) : esc_html($label) ?>  </span>
										<div class="tf-repeater-icon-absulate">
											<?php
											foreach ( $this->field['fields'] as $rkey => $re_field ) :
												if($rkey==0 && $re_field['type'] == 'switch' && $this->field['enable_disable']){
													if(!empty($this->parent_field)){
														$parent_field = $this->parent_field.'[' . $this->field['id'] . '][' . $key . ']';
													}else{
														$parent_field = '[' . $this->field['id'] . '][' . $key . ']';
													}

													$id = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][00]' . '[' . $re_field['id'] . ']' : $this->field['id'] . '[00]' . '[' . $re_field['id'] . ']';

													if(!empty($this->field['related_name'])){
														$related_name = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['related_name'] . '][00]' . '[' . $re_field['related_name'] . ']' : $this->field['related_name'] . '[00]' . '[' . $re_field['related_name'] . ']'; 
													}

													if ( isset( $tf_meta_box_value[ $id ] ) ) {
														$value = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : '';
													} else {
														$value = ( isset( $re_field['id'] ) && isset( $data[ $key ][ $re_field['id'] ] ) ) ? $data[ $key ][ $re_field['id'] ] : '';
													}

													if(!empty($this->field['related_name'])){
														if ( isset( $tf_meta_box_value[ $related_name ] ) ) {
															$related_value = isset( $tf_meta_box_value[ $related_name ] ) ? $tf_meta_box_value[ $related_name ] : '';
														} else {
															$related_value = ( isset( $re_field['related_name'] ) && isset( $data[ $key ][ $re_field['related_name'] ] ) ) ? $data[ $key ][ $re_field['related_name'] ] : '';
														}
													}else{
														$related_value = '';
													}

													if(isset($re_field['validate']) && $re_field['validate'] == 'no_space_no_special'){
														//remove special characters, replace space with underscore and convert to lowercase
														$value = sanitize_title(str_replace(' ', '_', strtolower($value)));
													}

													$value = ($re_field['type'] == 'text' || $re_field['type'] == 'textarea') ? stripslashes($value) : $value;

													$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
													$tf_option->field( $re_field, $value, $this->settings_id, $parent_field, $related_value);
												}
											endforeach;
											?>
											<span class="tf-repeater-icon tf-repeater-icon-collapse">
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M8 13.332H14" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M11 2.33218C11.2652 2.06697 11.6249 1.91797 12 1.91797C12.1857 1.91797 12.3696 1.95455 12.5412 2.02562C12.7128 2.09669 12.8687 2.20086 13 2.33218C13.1313 2.4635 13.2355 2.61941 13.3066 2.79099C13.3776 2.96257 13.4142 3.14647 13.4142 3.33218C13.4142 3.5179 13.3776 3.7018 13.3066 3.87338C13.2355 4.04496 13.1313 4.20086 13 4.33218L4.66667 12.6655L2 13.3322L2.66667 10.6655L11 2.33218Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M10 3.33203L12 5.33203" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
											<?php
											if(empty($this->field['drag_only']) || !$this->field['drag_only']){
											?>
											<span class="tf-repeater-icon tf-repeater-icon-clone" data-repeater-max = "<?php if(isset($this->field['max'])){ echo esc_attr($this->field['max']); }  ?>">
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<g clip-path="url(#clip0_986_42872)">
													<path d="M13.3335 5.33203H6.66683C5.93045 5.33203 5.3335 5.92898 5.3335 6.66536V13.332C5.3335 14.0684 5.93045 14.6654 6.66683 14.6654H13.3335C14.0699 14.6654 14.6668 14.0684 14.6668 13.332V6.66536C14.6668 5.92898 14.0699 5.33203 13.3335 5.33203Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M2.66683 10.6654C1.9335 10.6654 1.3335 10.0654 1.3335 9.33203V2.66536C1.3335 1.93203 1.9335 1.33203 2.66683 1.33203H9.3335C10.0668 1.33203 10.6668 1.93203 10.6668 2.66536" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</g>
												<defs>
													<clipPath id="clip0_986_42872">
													<rect width="16" height="16" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</span>
											<?php } ?>

											<span class="tf-repeater-icon tf-repeater-icon-delete">
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M2 4H14" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M12.6668 4V13.3333C12.6668 14 12.0002 14.6667 11.3335 14.6667H4.66683C4.00016 14.6667 3.3335 14 3.3335 13.3333V4" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M5.3335 3.9987V2.66536C5.3335 1.9987 6.00016 1.33203 6.66683 1.33203H9.3335C10.0002 1.33203 10.6668 1.9987 10.6668 2.66536V3.9987" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
										</div>
									</div>
								</div>
                                <div class="tf-repeater-content-wrap hide" style="display: none">
									<?php
									foreach ( $this->field['fields'] as $rkey => $re_field ) :
										if($rkey==0 && $re_field['type'] == 'switch' && $this->field['enable_disable'])
                    					continue;
										if(!empty($this->parent_field)){
											$parent_field = $this->parent_field.'[' . $this->field['id'] . '][' . $key . ']';
										}else{
											$parent_field = '[' . $this->field['id'] . '][' . $key . ']';
										}

										$id = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][00]' . '[' . $re_field['id'] . ']' : $this->field['id'] . '[00]' . '[' . $re_field['id'] . ']';
										
										if(!empty($re_field['related_name'])){
											$related_name = ( ! empty( $this->settings_id ) ) && !empty($this->field['related_name']) ? $this->settings_id . '[' . $this->field['related_name'] . '][00]' . '[' . $re_field['related_name'] . ']' : ''; 
										}

										if ( isset( $tf_meta_box_value[ $id ] ) ) {
											$value = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : '';
										} else {
											$value = ( isset( $re_field['id'] ) && isset( $data[ $key ][ $re_field['id'] ] ) ) ? $data[ $key ][ $re_field['id'] ] : '';
										}

										if(!empty($re_field['related_name'])){
											if ( isset( $tf_meta_box_value[ $related_name ] ) ) {
												$related_value = isset( $tf_meta_box_value[ $related_name ] ) ? $tf_meta_box_value[ $related_name ] : '';
											} else {
												$related_value = ( isset( $re_field['related_name'] ) && isset( $data[ $key ][ $re_field['related_name'] ] ) ) ? $data[ $key ][ $re_field['related_name'] ] : '';
											}
										}else{
											$related_value = '';
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
									<?php if($this->field['id']=='package_pricing'){ ?>
									<div class="tf-action-button-group">
										<span class="tf_tour_package_cancel button button-secondary"><?php echo esc_html__('Cancel', 'tourfic'); ?></span>
										<span class="tf_tour_package_save button button-primary"><?php echo esc_html__('Save', 'tourfic'); ?></span>
									</div>
									<?php } ?>
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
							<span class="tf-repeater-icon-move">
								<svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M4.83333 10.5C5.29357 10.5 5.66667 10.1269 5.66667 9.66667C5.66667 9.20643 5.29357 8.83333 4.83333 8.83333C4.3731 8.83333 4 9.20643 4 9.66667C4 10.1269 4.3731 10.5 4.83333 10.5Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M4.83333 4.66667C5.29357 4.66667 5.66667 4.29357 5.66667 3.83333C5.66667 3.3731 5.29357 3 4.83333 3C4.3731 3 4 3.3731 4 3.83333C4 4.29357 4.3731 4.66667 4.83333 4.66667Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M4.83333 16.3333C5.29357 16.3333 5.66667 15.9602 5.66667 15.5C5.66667 15.0398 5.29357 14.6667 4.83333 14.6667C4.3731 14.6667 4 15.0398 4 15.5C4 15.9602 4.3731 16.3333 4.83333 16.3333Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M9.83333 10.5C10.2936 10.5 10.6667 10.1269 10.6667 9.66667C10.6667 9.20643 10.2936 8.83333 9.83333 8.83333C9.3731 8.83333 9 9.20643 9 9.66667C9 10.1269 9.3731 10.5 9.83333 10.5Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M9.83333 4.66667C10.2936 4.66667 10.6667 4.29357 10.6667 3.83333C10.6667 3.3731 10.2936 3 9.83333 3C9.3731 3 9 3.3731 9 3.83333C9 4.29357 9.3731 4.66667 9.83333 4.66667Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M9.83333 16.3333C10.2936 16.3333 10.6667 15.9602 10.6667 15.5C10.6667 15.0398 10.2936 14.6667 9.83333 14.6667C9.3731 14.6667 9 15.0398 9 15.5C9 15.9602 9.3731 16.3333 9.83333 16.3333Z" stroke="#95A3B2" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<div class="tf-repeater-header-info">
								<span class="tf-repeater-title"><?php if(isset($this->field['label'])){ echo esc_html($this->field['label']); }  ?></span>
								<div class="tf-repeater-icon-absulate">
									
									<?php
									foreach ( $this->field['fields'] as $rkey => $re_field ) :
										if($rkey==0 && $re_field['type'] == 'switch' && $this->field['enable_disable']){
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
										}
									endforeach;
									?>
										<span class="tf-repeater-icon tf-repeater-icon-collapse <?php echo $this->field['id']=='package_pricing' ? esc_attr('package-action-hide') : ''; ?>">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M8 13.332H14" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M11 2.33218C11.2652 2.06697 11.6249 1.91797 12 1.91797C12.1857 1.91797 12.3696 1.95455 12.5412 2.02562C12.7128 2.09669 12.8687 2.20086 13 2.33218C13.1313 2.4635 13.2355 2.61941 13.3066 2.79099C13.3776 2.96257 13.4142 3.14647 13.4142 3.33218C13.4142 3.5179 13.3776 3.7018 13.3066 3.87338C13.2355 4.04496 13.1313 4.20086 13 4.33218L4.66667 12.6655L2 13.3322L2.66667 10.6655L11 2.33218Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M10 3.33203L12 5.33203" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</span>
										<?php
										if(empty($this->field['drag_only']) || !$this->field['drag_only']){
										?>
										<span class="tf-repeater-icon tf-repeater-icon-clone <?php echo $this->field['id']=='package_pricing' ? esc_attr('package-action-hide') : ''; ?>" data-repeater-max = "<?php if(isset($this->field['max'])){ echo esc_attr($this->field['max']); }  ?>">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<g clip-path="url(#clip0_986_42872)">
												<path d="M13.3335 5.33203H6.66683C5.93045 5.33203 5.3335 5.92898 5.3335 6.66536V13.332C5.3335 14.0684 5.93045 14.6654 6.66683 14.6654H13.3335C14.0699 14.6654 14.6668 14.0684 14.6668 13.332V6.66536C14.6668 5.92898 14.0699 5.33203 13.3335 5.33203Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M2.66683 10.6654C1.9335 10.6654 1.3335 10.0654 1.3335 9.33203V2.66536C1.3335 1.93203 1.9335 1.33203 2.66683 1.33203H9.3335C10.0668 1.33203 10.6668 1.93203 10.6668 2.66536" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</g>
											<defs>
												<clipPath id="clip0_986_42872">
												<rect width="16" height="16" fill="white"/>
												</clipPath>
											</defs>
											</svg>
										</span>
										<?php } ?>
										<span class="tf-repeater-icon tf-repeater-icon-delete <?php echo $this->field['id']=='package_pricing' ? esc_attr('package-action-hide') : ''; ?>">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M2 4H14" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M12.6668 4V13.3333C12.6668 14 12.0002 14.6667 11.3335 14.6667H4.66683C4.00016 14.6667 3.3335 14 3.3335 13.3333V4" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M5.3335 3.9987V2.66536C5.3335 1.9987 6.00016 1.33203 6.66683 1.33203H9.3335C10.0002 1.33203 10.6668 1.9987 10.6668 2.66536V3.9987" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</span>

								</div>
							</div>
						</div>
                        <div class="tf-repeater-content-wrap">

							<?php foreach ( $this->field['fields'] as $key => $re_field ) {
								if($key==0 && $re_field['type'] == 'switch' && $this->field['enable_disable'])
								continue;
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
							<?php if($this->field['id']=='package_pricing'){ ?>
							<div class="tf-action-button-group">
								<span class="tf_tour_package_deleted button button-secondary"><?php echo esc_html__('Cancel', 'tourfic'); ?></span>
								<span class="tf_tour_package_save button button-primary"><?php echo esc_html__('Save', 'tourfic'); ?></span>
							</div>
							<?php } ?>

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
