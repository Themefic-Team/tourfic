<?php
// don't load directly
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

			echo '<select name="' . esc_attr($field_name) . '" id="' . esc_attr($tf_select2_unique_id) . '" class=" tf-select-two '.esc_attr($parent_class).' " data-placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr($multiple) . ' '. wp_kses_post($this->field_attributes()) .'>';
			if( is_array( $args['options'] )) {
				foreach ( $args['options'] as $key => $value ) {
					if(!empty($this->field['multiple']) && is_array( $this->value ) && in_array( $key, $this->value )){
						$selected = 'selected';
					} else {
						$selected = selected( $this->value, $key, false );
					}
					echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr($selected) . '>' . esc_html( $value ) . '</option>';
				}
			}
			echo '</select>';
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