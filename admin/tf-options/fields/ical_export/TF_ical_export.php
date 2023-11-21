<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_ical_export' ) ) {
	class TF_ical_export extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			global $post;
			$post_type  = get_post_type( $post->ID );
			$room_index = $room_id = '';
			if ( $post_type === 'tf_hotel' ) {

				$meta  = get_post_meta( $post->ID, 'tf_hotels_opt', true );
				$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
				if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
					$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $rooms );
					$rooms                = unserialize( $tf_hotel_rooms_value );
				}
				$room_index = str_replace( array( '[', ']', 'room' ), '', $this->parent_field );
				$room_id    = ! empty( $rooms[ $room_index ]['unique_id'] ) ? $rooms[ $room_index ]['unique_id'] : '';
			} elseif ( $post_type === 'tf_apartment' ) {
				$meta = get_post_meta( $post->ID, 'tf_apartment_opt', true );
			}

			$query_args = array(
				'feed'    => 'tf-ical',
				'post_id' => $post->ID,
				'room_id' => $room_id,
			);

			$export_url = add_query_arg( $query_args, site_url( '/' ) );

			$placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
			echo '<input type="text" readonly name="' . esc_attr( $this->field_name() ) . '" id="' . esc_attr( $this->field_name() ) . '" value="' . esc_url( $export_url ) . '" ' . $placeholder . ' ' . $this->field_attributes() . '/>';

			if ( isset( $this->field['button_text'] ) && ! empty( $this->field['button_text'] ) ) {
				$button_class = 'button button-primary button-large';
				$button_class .= isset( $this->field['button_class'] ) ? ' ' . $this->field['button_class'] : '';
				echo '<a href="' . esc_url( $export_url ) . '" target="_blank" class="' . $button_class . '" style="margin-top: 16px;">' . $this->field['button_text'] . '</a>';
			}
		}

	}
}