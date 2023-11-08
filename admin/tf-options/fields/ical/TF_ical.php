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
			if ( $post_type !== 'tf_hotel' ) {
				return;
			}
			$meta  = get_post_meta( $post->ID, 'tf_hotels_opt', true );
			$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                = unserialize( $tf_hotel_rooms_value );
			}

			$room_index = str_replace( array( '[', ']', 'room' ), '', $this->parent_field );
			$pricing_by = ! empty( $rooms[ $room_index ]['pricing-by'] ) ? $rooms[ $room_index ]['pricing-by'] : '1';

			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
			echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '" ' . $placeholder . ' ' . $this->field_attributes() . '/>';

			if ( isset( $this->field['button_text'] ) && ! empty( $this->field['button_text'] ) ) {
				$button_class = 'button button-primary button-large';
				$button_class .= isset( $this->field['button_class'] ) ? ' ' . $this->field['button_class'] : '';
				echo '<a href="#" class="' . $button_class . '" style="margin-top: 16px;" data-room-index="' . $room_index . '" data-pricing-by="' . $pricing_by . '">' . $this->field['button_text'] . '</a>';
			}
		}

	}
}