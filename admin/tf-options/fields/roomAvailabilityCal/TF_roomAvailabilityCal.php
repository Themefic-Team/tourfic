<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_roomAvailabilityCal' ) ) {
	class TF_roomAvailabilityCal extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			global $post;

            $post_type = get_post_type( $post->ID );
			if ( $post_type !== 'tf_room' ) {
				return;
			}
			$meta  = get_post_meta( $post->ID, 'tf_rooms_opt', true );
			$pricing_by = ! empty( $meta['pricing-by'] ) ? $meta['pricing-by'] : '1';
			?>
            <div class="tf-room-cal-wrap">
                <div class='tf-room-cal'></div>
                <div class="tf-room-cal-field" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">

                    <div class="tf-field-text" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check In', 'tourfic' ); ?></label>
                        <input readonly="readonly" type="text" class="tf_room_check_in" name="tf_room_check_in" placeholder="<?php echo __( 'Check In', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check Out', 'tourfic' ); ?></label>
                        <input readonly="readonly" type="text" class="tf_room_check_out" name="tf_room_check_out" placeholder="<?php echo __( 'Check Out', 'tourfic' ); ?>">
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
                        <input type="hidden" name="room_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <span class="tf_room_single_cal_update button button-primary button-large"><?php echo __( 'Save Calendar', 'tourfic' ); ?></span>
                    </div>

                </div>
                <input type="hidden" class="avail_date" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>" value='<?php echo $this->value; ?>'/>
            </div>
			<?php
		}
	}
}