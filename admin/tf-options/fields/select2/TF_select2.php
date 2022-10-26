<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_select2' ) ) {
	class TF_select2 extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field  );
		}

		public function render() {

			$args = wp_parse_args( $this->field, array(
				'placeholder' => '',
				'multiple'    => false,
			) );

			$placeholder = ( ! empty( $args['placeholder'] ) ) ? $args['placeholder'] : '';
			$multiple    = ( ! empty( $args['multiple'] ) ) ? 'multiple' : '';

			if(!empty($args['query_args']) && $args['options'] == 'posts'){
				$posts = get_posts($args['query_args']);
				$args['options'] = array();
				foreach($posts as $post){
					$args['options'][$post->ID] = $post->post_title;
				}
			}

			if(!empty($args['query_args']) && $args['options'] == 'terms'){
				$terms = get_terms($args['query_args']);
				$args['options'] = array();
				foreach($terms as $term){
					$args['options'][$term->term_id] = $term->name;
				}
			}

			echo '<select name="' . $this->field_name() . '[]" id="' . esc_attr( $this->field_name() ) . '" class="tf-select2" data-placeholder="' . esc_attr( $placeholder ) . '" ' . $multiple . '>';
			foreach ( $args['options'] as $key => $value ) {
				$selected = ( is_array( $this->value ) && in_array( $key, $this->value ) ) ? 'selected' : '';
				echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $value ) . '</option>';
			}
			echo '</select>';
		}

		//sanitize
		public function sanitize() {
			$value = $this->value;
			if ( ! empty( $this->field['multiple'] ) ) {
				$value = array_map( 'sanitize_text_field', $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			return $value;
		}

	}
}