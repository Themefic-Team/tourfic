<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_ical' ) ) {
	class TF_ical extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			global $post;
			$post_type = get_post_type( $post->ID );
			$room_index = $pricing_type = '';
			if ( $post_type === 'tf_room' ) {

				$room = get_post_meta($post->ID, 'tf_room_opt', true);
				$pricing_type = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '1';
			} elseif ( $post_type === 'tf_apartment' ) {
				$meta  = get_post_meta( $post->ID, 'tf_apartment_opt', true );
				$pricing_type = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
			}

			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
			echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '" ' . wp_kses_post($placeholder) . ' ' . wp_kses_post($this->field_attributes()) . '/>';

			if ( isset( $this->field['button_text'] ) && ! empty( $this->field['button_text'] ) ) {
				$button_class = 'button button-primary button-large';
				$button_class .= isset( $this->field['button_class'] ) ? ' ' . $this->field['button_class'] : '';
				echo '<a href="#" class="' . esc_attr($button_class) . '" style="margin-top: 16px;" data-room-index="' . esc_attr($room_index) . '" data-pricing-type="' . esc_attr($pricing_type) . '">' . esc_html($this->field['button_text']) . '</a>';
			}
		}

	}
}