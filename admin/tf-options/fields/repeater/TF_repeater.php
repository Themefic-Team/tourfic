<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
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
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() { 
			?>
            <div id="tf-repeater-1" class="tf-repeater <?php echo $this->field['id'];?>">
                <div class="tf-repeater-wrap tf-repeater-wrap-<?php echo $this->field['id'];?>"> 
					<?php if ( ! empty( $this->value ) ):
						$num = 0;
						if(!is_array($this->value)){
							$data = unserialize( $this->value );
						}else{
							$data = $this->value;
						}
					 	if(is_array($data)):
							foreach ( $data as $key => $value ) :
							?>
                            <div class="tf-single-repeater tf-single-repeater-<?php echo $this->field['id'];?>">
							<input type="hidden" name="tf_parent_field" value="<?php echo $this->parent_field; ?>">
							<input type="hidden" name="tf_current_field" value="<?php echo $this->field['id'];?>">
								<div class="tf-repeater-header">
									<span class="tf-repeater-icon tf-repeater-icon-collapse">
										<i class="fa-solid fa-angle-down"></i>
									</span>
									<span class="tf-repeater-title"><?php echo $this->field['label'] ?>  </span>
									<div class="tf-repeater-icon-absulate">
										<span class="tf-repeater-icon tf-repeater-icon-move">
											<i class="fa-solid fa-up-down-left-right"></i>
										</span>
										<span class="tf-repeater-icon tf-repeater-icon-clone">
											<i class="fa-solid fa-copy"></i> 
										</span>
										<span class="tf-repeater-icon tf-repeater-icon-delete">
											<i class="fa-solid fa-trash"></i>
										</span>
									</div>
								</div>
                                <div class="tf-repeater-content-wrap">
									<?php
									foreach ( $this->field['fields'] as $re_field ) :
										if(!empty($this->parent_field)){
											$parent_field = $this->parent_field.'[' . $this->field['id'] . '][' . $key . ']';
										}else{ 
											$parent_field = '[' . $this->field['id'] . '][' . $key . ']';
										}
										$id = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][0]' . '[' . $re_field['id'] . ']' : $this->field['id'] . '[0]' . '[' . $re_field['id'] . ']';
										if ( isset( $tf_meta_box_value[ $id ] ) ) {
											$value = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : '';
										} else {
											$value = ( isset( $re_field['id'] ) && isset( $data[ $key ][ $re_field['id'] ] ) ) ? $data[ $key ][ $re_field['id'] ] : '';
										}

										$tf_option = new TF_Options();
										$tf_option->field( $re_field, $value, $this->settings_id, $parent_field ); 
									endforeach;
									$num ++;
									?>
                                </div>
                            </div>
						<?php endforeach; endif; endif; ?>
                </div>
                <div class=" tf-single-repeater-clone tf-single-repeater-clone-<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">
                    <div class="tf-single-repeater tf-single-repeater-<?php echo $this->field['id'];?>">
					
					<input type="hidden" name="tf_parent_field" value="<?php if(isset($this->parent_field)){ echo esc_attr($this->parent_field); }  ?>"> 
					<input type="hidden" name="tf_current_field" value="<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">
						<div class="tf-repeater-header">
							<span class="tf-repeater-icon tf-repeater-icon-collapse">
								<i class="fa-solid fa-angle-down"></i> 
							</span>
							<span class="tf-repeater-title"><?php if(isset($this->field['label'])){ echo esc_html($this->field['label']); }  ?></span>
							<div class="tf-repeater-icon-absulate">
								<span class="tf-repeater-icon tf-repeater-icon-move">
									<i class="fa-solid fa-up-down-left-right"></i>
								</span>
								<span class="tf-repeater-icon tf-repeater-icon-clone">
									<i class="fa-solid fa-copy"></i> 
								</span>
								<span class="tf-repeater-icon tf-repeater-icon-delete">
									<i class="fa-solid fa-trash"></i>
								</span>
							</div>
						</div>
                        <div class="tf-repeater-content-wrap">

							<?php foreach ( $this->field['fields'] as $key => $re_field ) {
								if(!empty($this->parent_field)){
									$parent = $this->parent_field.'[' . $this->field['id'] . '][0]';
								}else{ 
									$parent    = '[' . $this->field['id'] . '][0]';
								}
								
								$id        = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][0]' . '[' . $re_field['id'] . ']' : $this->field['id'] . '[0]' . '[' . $re_field['id'] . ']';
								$value     = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : '';
								$tf_option = new TF_Options();
								$tf_option->field( $re_field, $value, '_____' . $this->settings_id, $parent );


							} ?>
                        </div>
                    </div>

                </div>

                <div class="tf-repeater-add tf-repeater-add-<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">
					<span data-repeater-id = "<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>" class="tf-repeater-icon tf-repeater-icon-add tf-repeater-add-<?php if(isset($this->field['id'])){ echo esc_attr($this->field['id']); }  ?>">
						<?php 
							if(isset($this->field['button_title']) && !empty($this->field['button_title'])){
								echo  $this->field['button_title'];
							}else{
								echo '<i class="fa-solid fa-plus"></i>';
							}
							 
						?>
						
					</span>
				</div>
            </div>
			<?php

		}

		public function enqueue() {

			if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}

		}
	}
}
