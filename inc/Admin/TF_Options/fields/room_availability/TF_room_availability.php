<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_room_availability' ) ) {
	class TF_room_availability extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			global $post;
			$post_type = get_post_type( $post->ID );
			if ( $post_type !== 'tf_room' ) {
				return;
			}
			$meta = get_post_meta( $post->ID, 'tf_room_opt', true );

			$pricing_by   = ! empty( $meta['pricing-by'] ) ? $meta['pricing-by'] : '1';
			$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];
			if ( Helper::tf_is_woo_active() ) {
				?>
                <div class="tf-room-cal-wrap">
                    <div class='tf-room-cal'></div>
                    <div class="tf-room-cal-field" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">

                        <div class="tf-field-date" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'From', 'tourfic' ); ?></label>
                            <div class="tf-fieldset" style="margin-top: 0;">
                                <input readonly="readonly" type="text" class="tf_room_check_in" name="tf_room_check_in" placeholder="<?php echo esc_html__( 'From', 'tourfic' ); ?>">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                        </div>

                        <div class="tf-field-date" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'To', 'tourfic' ); ?></label>
                            <div class="tf-fieldset" style="margin-top: 0;">
                                <input readonly="readonly" type="text" class="tf_room_check_out" name="tf_room_check_out" placeholder="<?php echo esc_html__( 'To', 'tourfic' ); ?>">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                        </div>

                        <div class="tf-field-text tf-price-by-room" style="display: <?php echo $pricing_by == '1' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_room_price" placeholder="<?php echo esc_html__( 'Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_by == '2' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_room_adult_price" placeholder="<?php echo esc_html__( 'Adult Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-price-by-person" style="display: <?php echo $pricing_by == '2' ? 'block' : 'none' ?>; width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_room_child_price" placeholder="<?php echo esc_html__( 'Child Price', 'tourfic' ); ?>">
                        </div>
                        <div class="tf-single-options">
						<?php if ( $pricing_by == '3' ) {
							if ( ! empty( $room_options ) ) {
								foreach ( $room_options as $key => $room_option ) {
									$option_pricing_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
									?>
                                    <div class="tf-single-option">
                                        <div class="tf-field-switch">
                                            <label for="tf_room_option_<?php echo esc_attr( $key ); ?>" class="tf-field-label"><?php echo esc_html( $room_option['option_title'] ); ?></label>
                                            <div class="tf-fieldset">
                                                <label for="tf_room_option_<?php echo esc_attr( $key ); ?>" class="tf-switch-label" style="width: 80px">
                                                    <input type="checkbox" id="tf_room_option_<?php echo esc_attr( $key ); ?>" name="tf_room_option_<?php echo esc_attr( $key ); ?>" value="1" class="tf-switch"
                                                           checked="checked">
                                                    <span class="tf-switch-slider">
                                                        <span class="tf-switch-on"><?php echo esc_html__('Enable', 'tourfic') ?></span>
                                                        <span class="tf-switch-off"><?php echo esc_html__('Disable', 'tourfic') ?></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tf-field-text tf_option_pricing_type_room" style="display: <?php echo $option_pricing_type == 'per_room' ? 'block' : 'none' ?>; width: calc(100% - 90px)">
                                            <label class="tf-field-label"><?php echo esc_html__( 'Room Price', 'tourfic' ); ?></label>
                                            <div class="tf-fieldset">
                                                <input type="number" min="0" name="tf_option_room_price_<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr__( 'Room Price', 'tourfic' ); ?>">
                                            </div>
                                        </div>
                                        <div class="tf-field-text tf_option_pricing_type_person" style="display: <?php echo $option_pricing_type == 'per_person' ? 'block' : 'none' ?>; width: calc((100% - 80px)/2 - -5px)">
                                            <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                                            <div class="tf-fieldset">
                                                <input type="number" min="0" name="tf_option_adult_price_<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr__( 'Adult Price', 'tourfic' ); ?>">
                                            </div>
                                        </div>
                                        <div class="tf-field-text tf_option_pricing_type_person" style="display: <?php echo $option_pricing_type == 'per_person' ? 'block' : 'none' ?>; width: calc((100% - 80px)/2 - -5px)">
                                            <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                                            <div class="tf-fieldset">
                                                <input type="number" min="0" name="tf_option_child_price_<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr__( 'Child Price', 'tourfic' ); ?>">
                                            </div>
                                        </div>
                                        <input type="hidden" name="tf_option_title_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr($room_option['option_title']); ?>"/>
                                        <input type="hidden" name="tf_option_pricing_type_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr($option_pricing_type); ?>"/>
                                    </div>
									<?php
								}
							}
						} ?>
                        </div>
                        <div class="tf-field-select" style="width: calc(50% - 5px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Status', 'tourfic' ); ?></label>
                            <select name="tf_room_status" class="tf-select">
                                <option value="available"><?php echo esc_html__( 'Available', 'tourfic' ); ?></option>
                                <option value="unavailable"><?php echo esc_html__( 'Unavailable', 'tourfic' ); ?></option>
                            </select>
                        </div>

                        <div style="width: 100%">
                            <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                            <input type="hidden" name="room_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                            <span class="tf_room_cal_update button button-primary button-large"><?php echo esc_html__( 'Save Calendar', 'tourfic' ); ?></span>
                        </div>

                    </div>
                    <input type="hidden" class="avail_date" name="<?php echo esc_attr( $this->field_name() ); ?>" id="<?php echo esc_attr( $this->field_name() ); ?>" value='<?php echo esc_html( $this->value ); ?>'/>
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