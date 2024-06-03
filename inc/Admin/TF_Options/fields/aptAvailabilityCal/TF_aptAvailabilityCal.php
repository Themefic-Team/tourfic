<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

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
			$meta         = get_post_meta( $post->ID, 'tf_apartment_opt', true );
			$pricing_type = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
			if ( Helper::tf_is_woo_active() ) {
				?>
                <div class="tf-apt-cal-wrap">
                    <div class='tf-apt-cal'></div>
                    <div class="tf-apt-cal-field" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">

                        <div class="tf-field-text" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Check In', 'tourfic' ); ?></label>
                            <input readonly="readonly" type="text" class="tf_apt_check_in" name="tf_apt_check_in" placeholder="<?php echo esc_html__( 'Check In', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Check Out', 'tourfic' ); ?></label>
                            <input readonly="readonly" type="text" class="tf_apt_check_out" name="tf_apt_check_out" placeholder="<?php echo esc_html__( 'Check Out', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-night" style="display: <?php echo esc_attr( $pricing_type == 'per_night' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_price" placeholder="<?php echo esc_html__( 'Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'per_person' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_adult_price" placeholder="<?php echo esc_html__( 'Adult Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'per_person' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_child_price" placeholder="<?php echo esc_html__( 'Child Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'per_person' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Infant Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_infant_price" placeholder="<?php echo esc_html__( 'Infant Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-select" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Status', 'tourfic' ); ?></label>
                            <select name="tf_apt_status" class="tf-select">
                                <option value="available"><?php echo esc_html__( 'Available', 'tourfic' ); ?></option>
                                <option value="unavailable"><?php echo esc_html__( 'Unavailable', 'tourfic' ); ?></option>
                            </select>
                        </div>

                        <div style="width: 100%">
                            <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                            <input type="hidden" name="apartment_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                            <span class="tf_apt_cal_update button button-primary button-large"><?php echo esc_html__( 'Save Calendar', 'tourfic' ); ?></span>
                        </div>

                    </div>
                    <input type="hidden" class="apt_availability" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>"
                           value='<?php echo esc_attr( $this->value ); ?>'/>
                </div>
				<?php
			} else {
				?>
                <div class="tf-container">
                    <div class="tf-notice tf-notice-danger">
						<?php esc_html_e( 'Please install and activate WooCommerce plugin to use this feature.', 'tourfic' ); ?>
                    </div>
                </div>
				<?php
			}
		}
	}
}