<?php
// don't load directly

use Mpdf\Tag\Em;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_select2' ) ) {
	class TF_select2 extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {

			if(empty($this->field['options']) && empty($this->field['options_callback'])) {
				return;
			}

			$args = wp_parse_args( $this->field, array(
				'placeholder' => '',
				'multiple'    => false,
			) );

			if(isset($this->field['options_callback']) && is_callable($this->field['options_callback'])) {
				$this->field['options'] = call_user_func($this->field['options_callback']);
			}

			$placeholder = ( ! empty( $args['placeholder'] ) ) ? $args['placeholder'] : '';
			$multiple    = ( ! empty( $args['multiple'] ) ) ? 'multiple' : '';

			if(!empty($args['query_args']) && $args['options'] == 'posts'){
				$posts = get_posts($args['query_args']);
				$args['options'] = array();
				foreach($posts as $post){
					$args['options'][$post->ID] = (empty($post->post_title)) ? 'No title ('.$post->ID.')' : $post->post_title;
				}
			}

			if(!empty($args['query_args']) && $args['options'] == 'terms'){
				$terms = get_terms($args['query_args']);
				$args['options'] = array();
				foreach($terms as $term){
					$args['options'][$term->term_id] = $term->name;
				}
			}

			$field_name = !empty($this->field['multiple']) ? $this->field_name() . '[]' : $this->field_name();
			$tf_select2_unique_id = str_replace( array("[","]"),"_",esc_attr( $this->field_name() ) );
			$parent_class = ( ! empty( $this->parent_field ) ) ? 'tf-select2-parent' : 'tf-select2';
			$parent_class = ( isset( $this->field['select2'] ) ) ? 'tf-select2' : $parent_class ;

			$inline_delete = !empty($args['inline_delete']) ? 'yes' : 'no';

			echo '<div class="tf-select-box-option"><select name="' . esc_attr($field_name) . '" id="' . esc_attr($tf_select2_unique_id) . '" class=" tf-select-two '.esc_attr($parent_class).' " data-delete="' . esc_attr( $inline_delete ) . '" data-placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr($multiple) . ' '. wp_kses_post($this->field_attributes()) .'>';
			if( is_array( $args['options'] )) {
				foreach ( $args['options'] as $key => $value ) {
					$data_edit = $disable = '';
					if(!empty($this->field['multiple']) && is_array( $this->value ) && in_array( $key, $this->value )){
						$selected = 'selected';
					} else {
						$selected = selected( $this->value, $key, false );
					}
					if($this->field['options'] == 'posts' && $this->field['id'] == 'tf_hotel' && $placeholder){
						echo '<option value="">' . esc_html( $placeholder ) . '</option>';
					}
					if($this->field['options'] == 'posts' && $this->field['id'] == 'tf_rooms'){
						$hotel_id = get_the_ID();
						$room_meta = get_post_meta($key, 'tf_room_opt', true);
						if(! empty( $room_meta['tf_hotel'] ) && $room_meta['tf_hotel'] != $hotel_id){
							$disable = 'disabled';
						}
						$data_edit = 'data-edit-url='. esc_url( get_edit_post_link( $key ) ). '';
					}
					echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr($selected) . ' '.esc_attr($data_edit).' '.esc_attr($disable).'>' . esc_html( $value ) . '</option>';
				}
			}
			echo '</select>';
			if(!empty($args['query_args']) && isset($args['inline_add_new']) && $args['inline_add_new']){
				$add_new_button_text = !empty($args['add_button_text']) ? esc_html($args['add_button_text']) : esc_html__('Add New', 'tourfic');
				echo '<div class="tf-add-category" data-value=""><span class="tf-admin-btn"><i class="ri-add-fill"></i> '. $add_new_button_text .'</span></div>';
			}
			echo '</div>';
			
			//category popup
			if(!empty($args['query_args']) && $this->field['options'] == 'terms' && isset($args['inline_add_new']) && $args['inline_add_new']){
				echo '<div class="tf-popup-box">
					<div class="tf-add-category-box">
					<div class="tf-add-category-box-header">
						<h3>Add New Category</h3>
						<span class="tf-add-category-box-close">
							<i class="fa-solid fa-xmark"></i>
						</span>
					</div>

					<input type="hidden" id="category_name" value="'.$args['query_args']['taxonomy'].'">
					<input type="hidden" id="category_select_field_name" value="'. esc_attr($tf_select2_unique_id) .'">


					<div class="tf-add-category-box-content">
						<div class="tf-single-category-box">
							<label>Name</label>
							<input type="text" id="category_title">
						</div>

						<div class="tf-single-category-box">
							<label>Parent</label>
							<select id="parent_category">
							<option value="">--Select--</option>';
								
							// Loop through the query_args to populate the select options
							foreach($args['options'] as $value => $label){
								echo '<option value="'. htmlspecialchars($value) .'">'. htmlspecialchars($label) .'</option>';
							}

						echo '</select>
						</div>

						<button class="tf-admin-btn tf-btn-secondary tf-category-button">Add</button>
					</div>
				</div>
				</div>';
			}

			//post popup
			if(!empty($args['query_args']) && $this->field['options'] == 'posts' && isset($args['inline_add_new']) && $args['inline_add_new']){
				$post_type_key = !empty($args['query_args']['post_type']) ? $args['query_args']['post_type'] : '';
				if(!empty($post_type_key)){
					$post_type_object = get_post_type_object($post_type_key);
					$post_type_label = $post_type_object->labels->singular_name;
				}
				?>
				<div class="tf-popup-box">
					<div class="tf-add-category-box">
						<div class="tf-add-category-box-header">
							<h3><?php echo sprintf(esc_html__('Add New %s', 'tourfic'), $post_type_label) ?></h3>
							<span class="tf-add-category-box-close">
								<i class="fa-solid fa-xmark"></i>
							</span>
						</div>

						<input type="hidden" class="post_type" value="<?php echo esc_attr($args['query_args']['post_type']); ?>">
						<input type="hidden" class="post_select_field_name" value="<?php echo esc_attr($tf_select2_unique_id) ?>">
						<input type="hidden" class="field_id" value="<?php echo esc_attr($this->field['id']) ?>">
						<input type="hidden" class="post_id" value="<?php echo esc_attr(get_the_ID()) ?>">


						<div class="tf-add-category-box-content">
							<div class="tf-single-category-box">
								<label><?php echo esc_html('Title', 'tourfic'); ?></label>
								<input type="text" class="post_title" placeholder="<?php echo esc_attr__('Add title', 'tourfic'); ?>">
							</div>

							<button class="tf-admin-btn tf-btn-secondary tf-add-new-post-button"><?php echo esc_html__('Add New', 'tourfic'); ?></button>
						
							<?php if($this->field['id'] == 'tf_rooms'): ?>
							<div class="tf-single-category-box" style="margin-top: 16px;">
								<div class="tf-field-notice-inner tf-notice-info" style="padding: 12px 24px;border-radius:4px;">
									<div class="tf-field-notice-content has-content">
										<?php echo esc_html__('A new room will be created, After creation save the hotel and click the room edit icon to set the room’s details', 'tourfic'); ?>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php
			}
		}

		//sanitize
		public function sanitize() {
			$value = $this->value;
			if ( ! empty( $this->field['multiple'] ) && is_array( $this->value ) ) {
				$value = array_map( 'sanitize_text_field', $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			return $value;
		}

	}
}