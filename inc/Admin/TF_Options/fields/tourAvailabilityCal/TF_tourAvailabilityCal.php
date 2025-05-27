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
            $tour_avail_type = ! empty( $meta['type'] ) ? $meta['type'] : 'continuous';
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

                        <div class="tf-field-text tf-tour-group-pricing" style="display: <?php echo esc_attr( $tour_avail_type!='fixed' && $pricing_type == 'group' ? 'block' : 'none' ) ?>; width: 100%">
                            <label class="tf-field-label"><?php echo esc_html__( 'Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_price" placeholder="<?php echo esc_html__( 'Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-tour-person-pricing" style="display: <?php echo esc_attr( $tour_avail_type!='fixed' && $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_adult_price" placeholder="<?php echo esc_html__( 'Adult Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-tour-person-pricing" style="display: <?php echo esc_attr( $tour_avail_type!='fixed' && $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_child_price" placeholder="<?php echo esc_html__( 'Child Price', 'tourfic' ); ?>">
                        </div>

                        <div class="tf-field-text tf-tour-person-pricing" style="display: <?php echo esc_attr( $tour_avail_type!='fixed' && $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Infant Price', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_infant_price" placeholder="<?php echo esc_html__( 'Infant Price', 'tourfic' ); ?>">
                        </div>
                        
                        <div class="tf-field-text tf-tour-limitation" style="display: <?php echo esc_attr( ( $tour_avail_type=='fixed' || $tour_avail_type=='continuous' ) && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Minimum Person (Required for Search)', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_min_person">
                        </div>
                        <div class="tf-field-text tf-tour-limitation" style="display: <?php echo esc_attr( ( $tour_avail_type=='fixed' || $tour_avail_type=='continuous' ) && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Maximum Person (Required for Search)', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_max_person">
                        </div>
                        <div class="tf-field-text tf-tour-limitation" style="display: <?php echo esc_attr( ( $tour_avail_type=='fixed' || $tour_avail_type=='continuous' ) && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                            <label class="tf-field-label"><?php echo esc_html__( 'Maximum Capacity', 'tourfic' ); ?></label>
                            <input type="number" min="0" name="tf_tour_max_capacity">
                        </div>

                        <div class="tf-field tf-field-checkbox tf-months-checkbox" style="width:100%; padding: 0; display: <?php echo esc_attr( ( $tour_avail_type=='fixed' || $tour_avail_type=='continuous' ) && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>">
                            <label class="tf-field-label"><?php echo esc_html__( 'Repeat Month', 'tourfic' ); ?></label>
                            <div class="tf-fieldset">
                                <ul class="tf-checkbox-group tf-inline" style="margin-bottom: 0">
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[01]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="01">
                                        <label for="tf_tour_repeat_month[01]"><?php echo esc_html__( 'January', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[02]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="02">
                                        <label for="tf_tour_repeat_month[02]"><?php echo esc_html__( 'February', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[03]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="03">
                                        <label for="tf_tour_repeat_month[03]"><?php echo esc_html__( 'March', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[04]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="04">
                                        <label for="tf_tour_repeat_month[04]"><?php echo esc_html__( 'April', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[05]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="05">
                                        <label for="tf_tour_repeat_month[05]"><?php echo esc_html__( 'May', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[06]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="06">
                                        <label for="tf_tour_repeat_month[06]"><?php echo esc_html__( 'June', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[07]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="07">
                                        <label for="tf_tour_repeat_month[07]"><?php echo esc_html__( 'July', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[08]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="08">
                                        <label for="tf_tour_repeat_month[08]"><?php echo esc_html__( 'August', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[09]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="09">
                                        <label for="tf_tour_repeat_month[09]"><?php echo esc_html__( 'September', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[10]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="10">
                                        <label for="tf_tour_repeat_month[10]"><?php echo esc_html__( 'October', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[11]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="11">
                                        <label for="tf_tour_repeat_month[11]"><?php echo esc_html__( 'November', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_month[12]" name="tf_tour_repeat_month[]" class="tf-group-checkbox" value="12">
                                        <label for="tf_tour_repeat_month[12]"><?php echo esc_html__( 'December', 'tourfic' ); ?></label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tf-field tf-field-checkbox tf-years-checkbox" style="width:100%; padding: 0; display: <?php echo esc_attr( ($tour_avail_type =='fixed' || $tour_avail_type =='continuous') && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>">
                            <label class="tf-field-label"><?php echo esc_html__( 'Repeat Year', 'tourfic' ); ?></label>
                            <div class="tf-fieldset">
                                <ul class="tf-checkbox-group tf-inline" style="margin-bottom: 0">
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_year[2025]" name="tf_tour_repeat_year[]" class="tf-group-checkbox" value="2025">
                                        <label for="tf_tour_repeat_year[2025]"><?php echo esc_html__( '2025', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_year[2026]" name="tf_tour_repeat_year[]" class="tf-group-checkbox" value="2026">
                                        <label for="tf_tour_repeat_year[2026]"><?php echo esc_html__( '2026', 'tourfic' ); ?></label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="tf_tour_repeat_year[2027]" name="tf_tour_repeat_year[]" class="tf-group-checkbox" value="2027">
                                        <label for="tf_tour_repeat_year[2027]"><?php echo esc_html__( '2027', 'tourfic' ); ?></label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tf-field tf-field-repeater" style="width:100%; display: <?php echo esc_attr( ($tour_avail_type =='fixed' || $tour_avail_type =='continuous') && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>">
                            <label for="allowed_time[0][allowed_time]" class="tf-field-label"><?php echo esc_html__( 'Allowed Time', 'tourfic' ); ?> </label>
                            <div class="tf-fieldset">
                                <div id="tf-repeater-1" class="tf-repeater allowed_time" data-max-index="0">
                                <div class="tf-repeater-wrap tf_tour_allowed_times tf-repeater-wrap-allowed_time ui-sortable">

                                </div>
                                <div class=" tf-single-repeater-clone tf-single-repeater-clone-allowed_time">
                                    <div class="tf-single-repeater tf-single-repeater-allowed_time">
                                    <input type="hidden" name="tf_parent_field" value="">
                                    <input type="hidden" name="tf_repeater_count" value="0">
                                    <input type="hidden" name="tf_current_field" value="allowed_time">
                                    <div class="tf-repeater-header">
                                        <span class="tf-repeater-icon tf-repeater-icon-collapse">
                                        <i class="fa-solid fa-angle-up"></i>
                                        </span>
                                        <span class="tf-repeater-title"><?php echo esc_html__( 'Allowed Time', 'tourfic' ); ?></span>
                                        <div class="tf-repeater-icon-absulate">
                                        <span class="tf-repeater-icon tf-repeater-icon-move">
                                            <i class="fa-solid fa-up-down-left-right"></i>
                                        </span>
                                        <span class="tf-repeater-icon tf-repeater-icon-clone" data-repeater-max="">
                                            <i class="fa-solid fa-copy"></i>
                                        </span>
                                        <span class="tf-repeater-icon tf-repeater-icon-delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="tf-repeater-content-wrap">
                                        <div class="tf-field tf-field-time" style="width: calc(50% - 6px);">
                                        <label class="tf-field-label"> <?php echo esc_html__( 'Time', 'tourfic' ); ?> </label>
                                        
                                        <div class="tf-fieldset">
                                            <input type="text" name="allowed_time[time][]" placeholder="Select Time" value="" class="flatpickr flatpickr-input" data-format="h:i K" readonly="readonly">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                        </div>
                                        <div class="tf-field tf-field-number" style="width: calc(50% - 6px);">
                                        <label class="tf-field-label"> <?php echo esc_html__( 'Maximum Capacity', 'tourfic' ); ?> </label>
                                        
                                        <div class="tf-fieldset">
                                            <input type="number" name="allowed_time[cont_max_capacity][]" id="allowed_time[cont_max_capacity]" value="">
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="tf-repeater-add tf-repeater-add-allowed_time">
                                    <span data-repeater-id="allowed_time" data-repeater-max="" class="tf-repeater-icon tf-repeater-icon-add tf-repeater-add-allowed_time"><?php echo esc_html__( 'Add New Time', 'tourfic' ); ?> </span>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-single-options tf-tour-packages">
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