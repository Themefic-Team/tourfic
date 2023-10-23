<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_aptAvailabilityCal' ) ) {
	class TF_aptAvailabilityCal extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			global $post;
			$post_type = get_post_type( $post->ID );
			if ( $post_type !== 'tf_apartment' ) {
				return;
			}
			$meta  = get_post_meta( $post->ID, 'tf_apartment_opt', true );
            $pricing_type = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
			?>
            <div class="tf-apt-cal-wrap">
                <div class='tf-apt-cal'></div>
                <div class="tf-apt-cal-field" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">

                    <div class="tf-field-text" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check In', 'tourfic' ); ?></label>
                        <input readonly="readonly" type="text" class="tf_apt_check_in" name="tf_apt_check_in" placeholder="<?php echo __( 'Check In', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Check Out', 'tourfic' ); ?></label>
                        <input readonly="readonly" type="text" class="tf_apt_check_out" name="tf_apt_check_out" placeholder="<?php echo __( 'Check Out', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-night" style="display: <?php echo $pricing_type == 'per_night' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Price', 'tourfic' ); ?></label>
                        <input type="number" min="0" name="tf_apt_price" placeholder="<?php echo __( 'Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_type == 'per_person' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Adult Price', 'tourfic' ); ?></label>
                        <input type="number" min="0" name="tf_apt_adult_price" placeholder="<?php echo __( 'Adult Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_type == 'per_person' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Child Price', 'tourfic' ); ?></label>
                        <input type="number" min="0" name="tf_apt_child_price" placeholder="<?php echo __( 'Child Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_type == 'per_person' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Infant Price', 'tourfic' ); ?></label>
                        <input type="number" min="0" name="tf_apt_infant_price" placeholder="<?php echo __( 'Infant Price', 'tourfic' ); ?>">
                    </div>

                    <div class="tf-field-select" style="width: calc(50% - 5px)">
                        <label class="tf-field-label"><?php echo __( 'Status', 'tourfic' ); ?></label>
                        <select name="tf_apt_status" class="tf-select">
                            <option value="available"><?php echo __( 'Available', 'tourfic' ); ?></option>
                            <option value="unavailable"><?php echo __( 'Unavailable', 'tourfic' ); ?></option>
                        </select>
                    </div>

                    <div style="width: 100%">
                        <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                        <input type="hidden" name="apartment_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <span class="tf_apt_cal_update button button-primary button-large"><?php echo __( 'Save Calendar', 'tourfic' ); ?></span>
                    </div>

                </div>
                <input type="hidden" class="apt_availability" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>" value='<?php echo $this->value; ?>'/>
            </div>
			<?php
		}
	}
}