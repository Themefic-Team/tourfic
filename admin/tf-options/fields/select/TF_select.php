<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_select' ) ) {
	class TF_select extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {

			if ( empty( $this->field['options'] ) && empty( $this->field['options_callback'] ) ) {
				return;
			}

			if ( isset( $this->field['options_callback'] ) && is_callable( $this->field['options_callback'] ) ) {
				$this->field['options'] = call_user_func( $this->field['options_callback'] );
			}

			if ( ! empty( $this->field['query_args'] ) && $this->field['options'] == 'posts' ) {
				$posts                  = get_posts( $this->field['query_args'] );
				$this->field['options'] = array();
				foreach ( $posts as $post ) {
					$this->field['options'][ $post->ID ] = ( empty( $post->post_title ) ) ? 'No title (' . $post->ID . ')' : $post->post_title;
				}
			}

			if ( ! empty( $this->field['query_args'] ) && $this->field['options'] == 'terms' ) {
				$terms                  = get_terms( $this->field['query_args'] );
				$this->field['options'] = array();
				foreach ( $terms as $term ) {
					$this->field['options'][ $term->term_id ] = $term->name;
				}
			}

			echo '<select name="' . $this->field_name() . '" id="' . esc_attr( $this->field_name() ) . '" data-depend-id="' . esc_attr( $this->field['id'] ) . '' . $this->parent_field . '" class="tf-select"  '. $this->field_attributes() .'>';
			if ( ! empty( $this->field['placeholder'] ) ) {
				echo '<option value="">' . esc_html( $this->field['placeholder'] ) . '</option>';
			}
			foreach ( $this->field['options'] as $key => $value ) {
				if($key !== '') {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->value, $key, false ) . '>' . esc_html( $value ) . '</option>';
				} else {
					//disable empty value
					echo '<option value="" disabled>' . esc_html( $value ) . '</option>';
				}
			}
			echo '</select>';
		}

	}
}