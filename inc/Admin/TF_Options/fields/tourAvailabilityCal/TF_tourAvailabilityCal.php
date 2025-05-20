<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_tourAvailabilityCal' ) ) {
	class TF_tourAvailabilityCal extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			global $post;
			$post_type = get_post_type( $post->ID );
			if ( $post_type !== 'tf_tours' ) {
				return;
			}
			$meta         = get_post_meta( $post->ID, 'tf_tours_opt', true );
			$pricing_type = ! empty( $meta['pricing'] ) ? $meta['pricing'] : 'person';
            $tour_package_options = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : [];
			if ( Helper::tf_is_woo_active() ) {
				?>
                <div class="tf-tour-cal-wrap">
                    <div class='tf-tour-cal'></div>
                    <div class="tf-tour-cal-field" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">

                        <div class="tf-field-text" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Check In', 'tourfic' ); ?></label>
                            <input readonly="readonly" type="text" class="tf_tour_check_in" name="tf_tour_check_in" placeholder="<?php echo esc_html__( 'Check In', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Check Out', 'tourfic' ); ?></label>
                            <input readonly="readonly" type="text" class="tf_tour_check_out" name="tf_tour_check_out" placeholder="<?php echo esc_html__( 'Check Out', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-night" style="display: <?php echo esc_attr( $pricing_type == 'group' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_price" placeholder="<?php echo esc_html__( 'Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_adult_price" placeholder="<?php echo esc_html__( 'Adult Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_child_price" placeholder="<?php echo esc_html__( 'Child Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Infant Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_infant_price" placeholder="<?php echo esc_html__( 'Infant Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-single-options">
						<?php if ( $pricing_type == 'package' ) {
							if ( ! empty( $tour_package_options ) ) {
								foreach ( $tour_package_options as $key => $room_option ) {
									$option_pricing_type = ! empty( $room_option['pricing_type'] ) ? $room_option['pricing_type'] : 'person';
									?>
                                    <div class="tf-single-option tf-single-package">
                                        <div class="tf-field-switch">
                                            <label for="tf_package_option_<?php echo esc_attr( $key ); ?>" class="tf-field-label"><?php echo esc_html( $room_option['pack_title'] ); ?></label>
                                            <div class="tf-fieldset">
                                                <label for="tf_package_option_<?php echo esc_attr( $key ); ?>" class="tf-switch-label" style="width: 80px">
                                                    <input type="checkbox" id="tf_package_option_<?php echo esc_attr( $key ); ?>" name="tf_package_option_<?php echo esc_attr( $key ); ?>" value="1" class="tf-switch"
                                                           checked="checked">
                                                    <span class="tf-switch-slider">
                                                        <span class="tf-switch-on"><?php echo esc_html__('Enable', 'tourfic') ?></span>
                                                        <span class="tf-switch-off"><?php echo esc_html__('Disable', 'tourfic') ?></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tf-form-fields">
                                            <div class="tf-field-text tf_option_pricing_type_group" style="display: <?php echo $option_pricing_type == 'group' ? 'block' : 'none' ?>; width: 100%">
                                                <label class="tf-field-label"><?php echo esc_html__( 'Group Price', 'tourfic' ); ?></label>
                                                <div class="tf-fieldset">
                                                    <input type="number" min="0" name="tf_option_group_price_<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr__( 'Group Price', 'tourfic' ); ?>">
                                                </div>
                                            </div>
                                            <div class="tf-field-text tf_option_pricing_type_person" style="display: <?php echo $option_pricing_type == 'person' ? 'block' : 'none' ?>;">
                                                <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                                                <div class="tf-fieldset">
                                                    <input type="number" min="0" name="tf_option_adult_price_<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr__( 'Adult Price', 'tourfic' ); ?>">
                                                </div>
                                            </div>
                                            <div class="tf-field-text tf_option_pricing_type_person" style="display: <?php echo $option_pricing_type == 'person' ? 'block' : 'none' ?>;">
                                                <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                                                <div class="tf-fieldset">
                                                    <input type="number" min="0" name="tf_option_child_price_<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr__( 'Child Price', 'tourfic' ); ?>">
                                                </div>
                                            </div>
                                            <div class="tf-field-text tf_option_pricing_type_person" style="display: <?php echo $option_pricing_type == 'person' ? 'block' : 'none' ?>;">
                                                <label class="tf-field-label"><?php echo esc_html__( 'Infant Price', 'tourfic' ); ?></label>
                                                <div class="tf-fieldset">
                                                    <input type="number" min="0" name="tf_option_infant_price_<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr__( 'Infant Price', 'tourfic' ); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="tf_option_title_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr($room_option['pack_title']); ?>"/>
                                        <input type="hidden" name="tf_option_pricing_type_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr($option_pricing_type); ?>"/>
                                    </div>
									<?php
								}
							}
						} ?>
                        </div>

                        <div class="tf-field-select" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Status', 'tourfic' ); ?></label>
                            <select name="tf_tour_status" class="tf-select">
                                <option value="available"><?php echo esc_html__( 'Available', 'tourfic' ); ?></option>
                                <option value="unavailable"><?php echo esc_html__( 'Unavailable', 'tourfic' ); ?></option>
                            </select>
                        </div>

                        <div style="width: 100%">
                            <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                            <input type="hidden" name="tour_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                            <span class="tf_tour_cal_update button button-primary button-large"><?php echo esc_html__( 'Save Calendar', 'tourfic' ); ?></span>
                        </div>

                    </div>
                    <input type="hidden" class="tour_availability" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>"
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