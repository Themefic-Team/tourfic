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
                    <div class="tf-apt-cal-field">

                        <div class="tf-field-text" style="width: calc(50% - 12px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Check In', 'tourfic' ); ?></label>
                            <div class="tf-field-text-group">
                                <i class="fa-solid fa-calendar-days"></i>
                                <input readonly="readonly" type="text" class="tf_apt_check_in" name="tf_apt_check_in" placeholder="<?php echo esc_html__( 'Check In', 'tourfic' ); ?>">
                            </div>
                        </div>

                        <div class="tf-field-text" style="width: calc(50% - 12px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Check Out', 'tourfic' ); ?></label>
                            <div class="tf-field-text-group">
                                <i class="fa-solid fa-calendar-days"></i>
                                <input readonly="readonly" type="text" class="tf_apt_check_out" name="tf_apt_check_out" placeholder="<?php echo esc_html__( 'Check Out', 'tourfic' ); ?>">
                            </div>
                        </div>

                        <div class="tf-field-number tf-price-by-night" style="display: <?php echo esc_attr( $pricing_type == 'per_night' ? 'block' : 'none' ) ?>; width: calc(50% - 12px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_price" placeholder="<?php echo esc_html__( 'Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-number tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'per_person' ? 'block' : 'none' ) ?>; width: calc(50% - 12px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_adult_price" placeholder="<?php echo esc_html__( 'Adult Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-number tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'per_person' ? 'block' : 'none' ) ?>; width: calc(50% - 12px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_child_price" placeholder="<?php echo esc_html__( 'Child Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-number tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'per_person' ? 'block' : 'none' ) ?>; width: calc(50% - 12px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Infant Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_apt_infant_price" placeholder="<?php echo esc_html__( 'Infant Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-select" style="width: calc(50% - 12px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Status', 'tourfic' ); ?></label>
                            <select name="tf_apt_status" class="tf-select">
                                <option value="available"><?php echo esc_html__( 'Available', 'tourfic' ); ?></option>
                                <option value="unavailable"><?php echo esc_html__( 'Unavailable', 'tourfic' ); ?></option>
                            </select>
                        </div>

                        <div class="tf-reset-confirmation-box">
                            <div class="tf-confirmation-content">
                                <div class="tf-confirmation-header">
                                    <h3><?php echo esc_html__( 'Are you sure you want to reset this calendar?', 'tourfic' ); ?></h3>
                                    <span class="tf_reset_confirmation_close">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18 6L6 18M6 6L18 18" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="tf-confirmation-actions">
                                    <button class="tf-cancel-btn"><?php echo esc_html__( 'Cancel', 'tourfic' ); ?></button>
                                    <button class="tf-confirmed-btn"><?php echo esc_html__( 'Confirmed', 'tourfic' ); ?></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tf-calendar-save tf-save-calendar">
                            <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                            <input type="hidden" name="apartment_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                            <span class="tf_apt_cal_update button button-primary button-large"><?php echo esc_html__( 'Save Calendar', 'tourfic' ); ?></span>
                            <span class="tf_apt_cal_reset button button-secondary button-large"><?php echo esc_html__( 'Reset Calendar', 'tourfic' ); ?></span>
                        </div>
                    </div>


                    <input type="hidden" class="apt_availability" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>" value='<?php echo esc_attr( $this->value ); ?>'/>
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