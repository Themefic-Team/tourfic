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
			$pricing_by = ! empty( $rooms[ $room_index ]['pricing-by'] ) ? $rooms[ $room_index ]['pricing-by'] : '1';
			?>
            <div class="tf-room-cal-wrap">
                <div class='tf-room-cal'></div>
                <div class="tf-room-cal-field" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">

                    <div class="tf-field-date" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check In', 'tourfic' ); ?></label>
                        <div class="tf-fieldset" style="margin-top: 0;">
                            <input readonly="readonly" type="text" class="tf_room_check_in" name="tf_room_check_in" placeholder="<?php echo __( 'Check In', 'tourfic' ); ?>">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>

                    <div class="tf-field-date" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check Out', 'tourfic' ); ?></label>
                        <div class="tf-fieldset" style="margin-top: 0;">
                            <input readonly="readonly" type="text" class="tf_room_check_out" name="tf_room_check_out" placeholder="<?php echo __( 'Check Out', 'tourfic' ); ?>">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>

                    <div class="tf-field-text tf-price-by-room" style="display: <?php echo $pricing_by == '1' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Price', 'tourfic' ); ?></label>
                        <input type="number" min="0" name="tf_room_price" placeholder="<?php echo __( 'Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_by == '2' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Adult Price', 'tourfic' ); ?></label>
                        <input type="number" min="0" name="tf_room_adult_price" placeholder="<?php echo __( 'Adult Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_by == '2' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Child Price', 'tourfic' ); ?></label>
                        <input type="number" min="0" name="tf_room_child_price" placeholder="<?php echo __( 'Child Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-select" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Status', 'tourfic' ); ?></label>
                        <select name="tf_room_status" class="tf-select">
                            <option value="available"><?php echo __( 'Available', 'tourfic' ); ?></option>
                            <option value="unavailable"><?php echo __( 'Unavailable', 'tourfic' ); ?></option>
                        </select>
                    </div>

                    <div style="width: 100%">
                        <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                        <input type="hidden" name="hotel_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <input type="hidden" name="room_index" value="<?php echo esc_attr( $room_index ); ?>">
                        <span class="tf_room_cal_update button button-primary button-large"><?php echo __( 'Save Calendar', 'tourfic' ); ?></span>
                    </div>

                </div>
                <input type="hidden" class="avail_date" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>" value='<?php echo $this->value; ?>'/>
            </div>
			<?php
		}
	}
}