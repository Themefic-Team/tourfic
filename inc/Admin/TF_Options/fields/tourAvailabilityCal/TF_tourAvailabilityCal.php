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
            $group_package_option = ! empty( $meta['allow_package_pricing'] ) ? $meta['allow_package_pricing'] : '';
            $group_package_pricing = ! empty( $meta['group_package_pricing'] ) ? $meta['group_package_pricing'] : '';
            $tour_avail_type = ! empty( $meta['type'] ) ? $meta['type'] : 'continuous';
			if ( Helper::tf_is_woo_active() ) {
				?>
                <div class="tf-tour-cal-wrap">
                    <div class='tf-tour-cal'></div>
                    <div class="tf-tour-cal-field">
                        <div class="bulk-popup-content">

                            <div class="tf-bulk-edit-header">
                                <h3><?php echo esc_html__( 'Bulk price edit', 'tourfic' ); ?></h3>
                                <span class="tf_tour_bulk_close">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 6L6 18M6 6L18 18" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="tf-bulk-repeater-section">
                                <div class="tf-field tf-field-checkbox tf-weeks-checkbox" style="width:100%; padding: 0;">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Days of Week', 'tourfic' ); ?></label>
                                    <div class="tf-fieldset">
                                        <ul class="tf-checkbox-group tf-inline" style="margin-bottom: 0">
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_week[0]" name="tf_tour_repeat_week[]" class="tf-group-checkbox" value="0">
                                                <label for="tf_tour_repeat_week[0]"><?php echo esc_html__( 'Sunday', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_week[1]" name="tf_tour_repeat_week[]" class="tf-group-checkbox" value="1">
                                                <label for="tf_tour_repeat_week[1]"><?php echo esc_html__( 'Monday', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_week[2]" name="tf_tour_repeat_week[]" class="tf-group-checkbox" value="2">
                                                <label for="tf_tour_repeat_week[2]"><?php echo esc_html__( 'Tuesday', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_week[3]" name="tf_tour_repeat_week[]" class="tf-group-checkbox" value="3">
                                                <label for="tf_tour_repeat_week[3]"><?php echo esc_html__( 'Wednesday', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_week[4]" name="tf_tour_repeat_week[]" class="tf-group-checkbox" value="4">
                                                <label for="tf_tour_repeat_week[4]"><?php echo esc_html__( 'Thrusday', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_week[5]" name="tf_tour_repeat_week[]" class="tf-group-checkbox" value="5">
                                                <label for="tf_tour_repeat_week[5]"><?php echo esc_html__( 'Friday', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_week[6]" name="tf_tour_repeat_week[]" class="tf-group-checkbox" value="6">
                                                <label for="tf_tour_repeat_week[6]"><?php echo esc_html__( 'Saturday', 'tourfic' ); ?></label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="tf-field tf-field-checkbox tf-days-checkbox" style="width:100%; padding: 0;">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Days of Month', 'tourfic' ); ?></label>
                                    <div class="tf-fieldset">
                                        <ul class="tf-checkbox-group tf-inline" style="margin-bottom: 0">
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[1]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="1">
                                                <label for="tf_tour_repeat_day[1]"><?php echo esc_html__( '1', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[2]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="2">
                                                <label for="tf_tour_repeat_day[2]"><?php echo esc_html__( '2', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[3]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="3">
                                                <label for="tf_tour_repeat_day[3]"><?php echo esc_html__( '3', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[4]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="4">
                                                <label for="tf_tour_repeat_day[4]"><?php echo esc_html__( '4', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[5]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="5">
                                                <label for="tf_tour_repeat_day[5]"><?php echo esc_html__( '5', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[6]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="6">
                                                <label for="tf_tour_repeat_day[6]"><?php echo esc_html__( '6', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[7]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="7">
                                                <label for="tf_tour_repeat_day[7]"><?php echo esc_html__( '7', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[8]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="8">
                                                <label for="tf_tour_repeat_day[8]"><?php echo esc_html__( '8', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[9]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="9">
                                                <label for="tf_tour_repeat_day[9]"><?php echo esc_html__( '9', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[10]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="10">
                                                <label for="tf_tour_repeat_day[10]"><?php echo esc_html__( '10', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[11]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="11">
                                                <label for="tf_tour_repeat_day[11]"><?php echo esc_html__( '11', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[12]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="12">
                                                <label for="tf_tour_repeat_day[12]"><?php echo esc_html__( '12', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[13]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="13">
                                                <label for="tf_tour_repeat_day[13]"><?php echo esc_html__( '13', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[14]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="14">
                                                <label for="tf_tour_repeat_day[14]"><?php echo esc_html__( '14', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[15]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="15">
                                                <label for="tf_tour_repeat_day[15]"><?php echo esc_html__( '15', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[16]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="16">
                                                <label for="tf_tour_repeat_day[16]"><?php echo esc_html__( '16', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[17]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="17">
                                                <label for="tf_tour_repeat_day[17]"><?php echo esc_html__( '17', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[18]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="18">
                                                <label for="tf_tour_repeat_day[18]"><?php echo esc_html__( '18', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[19]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="19">
                                                <label for="tf_tour_repeat_day[19]"><?php echo esc_html__( '19', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[20]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="20">
                                                <label for="tf_tour_repeat_day[20]"><?php echo esc_html__( '20', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[21]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="21">
                                                <label for="tf_tour_repeat_day[21]"><?php echo esc_html__( '21', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[22]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="22">
                                                <label for="tf_tour_repeat_day[22]"><?php echo esc_html__( '22', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[23]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="23">
                                                <label for="tf_tour_repeat_day[23]"><?php echo esc_html__( '23', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[24]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="24">
                                                <label for="tf_tour_repeat_day[24]"><?php echo esc_html__( '24', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[25]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="25">
                                                <label for="tf_tour_repeat_day[25]"><?php echo esc_html__( '25', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[26]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="26">
                                                <label for="tf_tour_repeat_day[26]"><?php echo esc_html__( '26', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[27]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="27">
                                                <label for="tf_tour_repeat_day[27]"><?php echo esc_html__( '27', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[28]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="28">
                                                <label for="tf_tour_repeat_day[28]"><?php echo esc_html__( '28', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[29]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="29">
                                                <label for="tf_tour_repeat_day[29]"><?php echo esc_html__( '29', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[30]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="30">
                                                <label for="tf_tour_repeat_day[30]"><?php echo esc_html__( '30', 'tourfic' ); ?></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="tf_tour_repeat_day[31]" name="tf_tour_repeat_day[]" class="tf-group-checkbox" value="31">
                                                <label for="tf_tour_repeat_day[31]"><?php echo esc_html__( '31', 'tourfic' ); ?></label>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                                <div class="tf-field tf-field-checkbox tf-months-checkbox" style="width:100%; padding: 0;">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Months (*)', 'tourfic' ); ?></label>
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

                                <div class="tf-field tf-field-checkbox tf-years-checkbox" style="width:100%; padding: 0;">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Years (*)', 'tourfic' ); ?></label>
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
                            </div>

                            <div class="tf-date-time-selection">
                                <div class="tf-field-text tf-check-dates" style="width: calc(50% - 12px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Check In', 'tourfic' ); ?></label>
                                    <div class="tf-field-text-group">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <input readonly="readonly" type="text" class="tf_tour_check_in" name="tf_tour_check_in" placeholder="<?php echo esc_html__( 'Check In', 'tourfic' ); ?>">
                                    </div>
                                </div>

                                <div class="tf-field-text tf-check-dates" style="width: calc(50% - 12px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Check Out', 'tourfic' ); ?></label>
                                    <div class="tf-field-text-group">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <input readonly="readonly" type="text" class="tf_tour_check_out" name="tf_tour_check_out" placeholder="<?php echo esc_html__( 'Check Out', 'tourfic' ); ?>">
                                    </div>
                                </div>

                                <div class="tf-field tf-field-repeater" style="width:100%; display: <?php echo esc_attr( ($tour_avail_type =='fixed' || $tour_avail_type =='continuous') ? 'block' : 'none' ) ?>">
                                    <div class="tf-fieldset">
                                        <div id="tf-repeater-1" class="tf-repeater allowed_time" data-max-index="0">
                                        <div class="tf-repeater-wrap tf_tour_allowed_times tf-repeater-wrap-allowed_time ui-sortable">

                                        </div>
                                        <div class=" tf-single-repeater-clone tf-single-repeater-clone-allowed_time">
                                            <div class="tf-single-repeater tf-single-repeater-allowed_time">
                                                <input type="hidden" name="tf_parent_field" value="">
                                                <input type="hidden" name="tf_repeater_count" value="0">
                                                <input type="hidden" name="tf_current_field" value="allowed_time">
                                                <div class="tf-repeater-content-wrap">
                                                    <div class="tf-field tf-field-time" style="width: calc(50% - 6px);">
                                                        <div class="tf-fieldset">
                                                            <input type="text" name="allowed_time[time][]" placeholder="Select Time" value="" class="flatpickr flatpickr-input" data-format="h:i K" readonly="readonly">
                                                            <i class="fa-regular fa-clock"></i>
                                                        </div>
                                                    </div>
                                                    <div class="tf-field tf-field-number" style="width: calc(50% - 6px);">
                                                        <div class="tf-fieldset">
                                                            <input type="number" name="allowed_time[cont_max_capacity][]" id="allowed_time[cont_max_capacity]" value="" placeholder="<?php echo esc_html__( 'Maximum Capacity', 'tourfic' ); ?>">
                                                        </div>
                                                    </div>
                                                    <span class="tf-repeater-icon tf-repeater-icon-delete">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15 5L5 15" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M5 5L15 15" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tf-repeater-add tf-repeater-add-allowed_time">
                                            <span data-repeater-id="allowed_time" data-repeater-max="" class="tf-repeater-icon tf-repeater-icon-add tf-repeater-add-allowed_time">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1017_2374)">
                                                        <path d="M9.99984 18.3346C14.6022 18.3346 18.3332 14.6037 18.3332 10.0013C18.3332 5.39893 14.6022 1.66797 9.99984 1.66797C5.39746 1.66797 1.6665 5.39893 1.6665 10.0013C1.6665 14.6037 5.39746 18.3346 9.99984 18.3346Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M6.6665 10H13.3332" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M10 6.66797V13.3346" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1017_2374">
                                                        <rect width="20" height="20" fill="white"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                <?php echo esc_html__( 'Add Start Time', 'tourfic' ); ?> 
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-field-select" style="width: 100%">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Status', 'tourfic' ); ?></label>
                                    <select name="tf_tour_status" class="tf-select">
                                        <option value="available"><?php echo esc_html__( 'Available', 'tourfic' ); ?></option>
                                        <option value="unavailable"><?php echo esc_html__( 'Unavailable', 'tourfic' ); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="tf-field-text tf-tour-group-pricing" style="display: <?php echo esc_attr($pricing_type == 'group' && ( empty($group_package_option) || empty($group_package_pricing) ) ? 'block' : 'none' ) ?>; width: 100%">
                                <label class="tf-field-label"><?php echo esc_html__( 'Price', 'tourfic' ); ?></label>
                                <input type="number" min="0" name="tf_tour_price" placeholder="<?php echo esc_html__( 'Price', 'tourfic' ); ?>">
                            </div>
                            <div class="tf-tour-limitation-group">
                                <div class="tf-field-text tf-tour-person-pricing" style="display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                                    <input type="number" min="0" name="tf_tour_adult_price" placeholder="<?php echo esc_html__( 'Adult Price', 'tourfic' ); ?>">
                                </div>

                                <div class="tf-field-text tf-tour-person-pricing" style="display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                                    <input type="number" min="0" name="tf_tour_child_price" placeholder="<?php echo esc_html__( 'Child Price', 'tourfic' ); ?>">
                                </div>

                                <div class="tf-field-text tf-tour-person-pricing" style="display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Infant Price', 'tourfic' ); ?></label>
                                    <input type="number" min="0" name="tf_tour_infant_price" placeholder="<?php echo esc_html__( 'Infant Price', 'tourfic' ); ?>">
                                </div>
                            </div>
                            <div class="tf-tour-limitation-group" style="display: <?php echo $pricing_type == 'person' || (empty($group_package_option) || empty($group_package_pricing)) ? 'flex' : 'none' ?>">
                                <div class="tf-field-text tf-tour-limitation" style="display: <?php echo esc_attr( ( $tour_avail_type=='fixed' || $tour_avail_type=='continuous' ) && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Minimum Person (Required for Search)', 'tourfic' ); ?></label>
                                    <input type="number" min="0" name="tf_tour_min_person">
                                </div>
                                <div class="tf-field-text tf-tour-limitation" style="display: <?php echo esc_attr( ( $tour_avail_type=='fixed' || $tour_avail_type=='continuous' ) && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Maximum Person (Required for Search)', 'tourfic' ); ?></label>
                                    <input type="number" min="0" name="tf_tour_max_person">
                                </div>
                                <div class="tf-field-text tf-tour-limitation" style="display: <?php echo esc_attr( ( $tour_avail_type=='fixed' || $tour_avail_type=='continuous' ) && ( $pricing_type == 'person' || $pricing_type == 'group' ) ? 'block' : 'none' ) ?>; width: calc(33% - 4px)">
                                    <label class="tf-field-label"><?php echo esc_html__( 'Maximum Capacity of this tour', 'tourfic' ); ?></label>
                                    <input type="number" min="0" name="tf_tour_max_capacity">
                                </div>
                            </div>

                            <?php if ( $pricing_type == 'package' ) { ?>
                            <div class="tf-single-options tf-tour-packages">
                                <?php
                                if ( ! empty( $tour_package_options ) ) {
                                    foreach ( $tour_package_options as $key => $room_option ) {
                                        $option_pricing_type = ! empty( $room_option['pricing_type'] ) ? $room_option['pricing_type'] : 'person';
                                        ?>
                                        <div class="tf-single-option tf-single-package">
                                            <div class="tf-field-switch">
                                                <label for="tf_package_option_<?php echo esc_attr( $key ); ?>" class="tf-field-label"><?php echo esc_html( $room_option['pack_title'] ); ?></label>
                                                
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
                                ?>
                            </div>
                            <?php } ?>

                            <?php 
                            if ( $pricing_type == 'group' && !empty($group_package_option) && !empty($group_package_pricing) ) {  
                            ?>
                            <div class="tf-single-options tf-group-packages">
                                    <?php
                                    foreach ( $group_package_pricing as $key => $pack ) {
                                    ?>
                                        <div class="tf-single-option tf-single-package">
                                            <div class="tf-field-switch">
                                                <label for="tf_package_option_<?php echo esc_attr( $key ); ?>" class="tf-field-label"><?php echo esc_html( $room_option['pack_title'] ); ?></label>
                                                
                                            </div>
                                            <div class="tf-form-fields">
                                                <div class="tf-field-text tf_option_pricing_type_group">
                                                    <label class="tf-field-label"><?php echo esc_html__( 'Minimum Person', 'tourfic' ); ?></label>
                                                    <div class="tf-fieldset">
                                                        <input type="number" min="0" name="tf_option_min_person_<?php echo esc_attr( $key ); ?>" />
                                                    </div>
                                                </div>
                                                <div class="tf-field-text tf_option_pricing_type_group">
                                                    <label class="tf-field-label"><?php echo esc_html__( 'Maximum Person', 'tourfic' ); ?></label>
                                                    <div class="tf-fieldset">
                                                        <input type="number" min="0" name="tf_option_max_person_<?php echo esc_attr( $key ); ?>" />
                                                    </div>
                                                </div>
                                                <div class="tf-field-text tf_option_pricing_type_group">
                                                    <label class="tf-field-label"><?php echo esc_html__( 'Group Price', 'tourfic' ); ?></label>
                                                    <div class="tf-fieldset">
                                                        <input type="number" min="0" name="tf_option_group_price_<?php echo esc_attr( $key ); ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="tf_option_title_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr($room_option['pack_title']); ?>"/>
                                            <input type="hidden" name="tf_option_pricing_type_<?php echo esc_attr( $key ); ?>" value="group"/>
                                        </div>
                                    <?php
                                    }
                                ?>
                            </div>
                            <?php } ?>

                            <div class="tf-reset-confirmation-box">
                                <div class="tf-confirmation-content">
                                    <h3><?php echo esc_html__( 'Are you sure you want to reset this calendar?', 'tourfic' ); ?></h3>
                                    <div class="tf-confirmation-actions">
                                        <button class="tf-cancel-btn"><?php echo esc_html__( 'Cancel', 'tourfic' ); ?></button>
                                        <button class="tf-confirmed-btn"><?php echo esc_html__( 'Confirmed', 'tourfic' ); ?></button>
                                    </div>
                                </div>
                            </div>

                            <div style="width: 100%; display: flex; justify-content: space-between;">
                                <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                                <input type="hidden" name="tour_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                                <input type="hidden" name="bulk_edit_option" class="tf_bulk_edit_option">
                                <div class="tf-save-calendar">
                                    <span class="tf_tour_cal_update button button-primary button-large"><?php echo esc_html__( 'Save', 'tourfic' ); ?></span>

                                    <span class="tf_tour_cal_reset button button-secondary button-large"><?php echo esc_html__( 'Reset Calendar', 'tourfic' ); ?></span>
                                </div>
                                <span class="tf_tour_cal_bulk_edit button button-secondary button-large">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 20H21" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16.5 3.50023C16.8978 3.1024 17.4374 2.87891 18 2.87891C18.2786 2.87891 18.5544 2.93378 18.8118 3.04038C19.0692 3.14699 19.303 3.30324 19.5 3.50023C19.697 3.69721 19.8532 3.93106 19.9598 4.18843C20.0665 4.4458 20.1213 4.72165 20.1213 5.00023C20.1213 5.2788 20.0665 5.55465 19.9598 5.81202C19.8532 6.06939 19.697 6.30324 19.5 6.50023L7 19.0002L3 20.0002L4 16.0002L16.5 3.50023Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15 5L18 8" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>    
                                    <?php echo esc_html__( 'Bulk Edit', 'tourfic' ); ?>
                                </span>
                            </div>

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