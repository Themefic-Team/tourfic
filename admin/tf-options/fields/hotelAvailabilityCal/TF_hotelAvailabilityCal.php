<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_hotelAvailabilityCal' ) ) {
	class TF_hotelAvailabilityCal extends TF_Fields {

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
			$pricing_by = ! empty( $rooms[ $room_index ]['pricing-by'] ) ? $rooms[ $room_index ]['pricing-by'] : '';
			?>
            <div class="calendar-wrapper">
                <div class='calendar-content'></div>
                <div class="calendar-form" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">

                    <div class="tf-field-text" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check In', 'tourfic' ); ?></label>
                        <input readonly="readonly" type="text" name="calendar_check_in" placeholder="<?php echo __( 'Check In', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check Out', 'tourfic' ); ?></label>
                        <input readonly="readonly" type="text" name="calendar_check_out" placeholder="<?php echo __( 'Check Out', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-room" style="display: <?php echo $pricing_by == '1' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Price ($)', 'tourfic' ); ?></label>
                        <input type="text" name="calendar_price" placeholder="<?php echo __( 'Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_by == '2' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Adult Price ($)', 'tourfic' ); ?></label>
                        <input type="text" name="calendar_adult_price" placeholder="<?php echo __( 'Adult Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_by == '2' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Child Price ($)', 'tourfic' ); ?></label>
                        <input type="text" name="calendar_child_price" placeholder="<?php echo __( 'Child Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-select" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Status', 'tourfic' ); ?></label>
                        <select name="calendar_status" class="tf-select">
                            <option value="available"><?php echo __( 'Available', 'tourfic' ); ?></option>
                            <option value="unavailable"><?php echo __( 'Unavailble', 'tourfic' ); ?></option>
                        </select>
                    </div>

                    <div style="width: 100%">
                        <input type="submit" id="calendar_submit" class="button button-primary" name="calendar_submit" value="<?php echo __( 'Update', 'tourfic' ); ?>">
                    </div>

                </div>
            </div>
			<?php
		}
	}
}