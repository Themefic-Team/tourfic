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
                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) { ?>
                    <div class="tf-tour-reset-refresh">
                        <span class="tf_tour_cal_bulk_edit button button-secondary button-large">
                            <?php echo esc_html__( 'Bulk Add', 'tourfic' ); ?>
                        </span>
                        <span class="tf_tour_cal_refresh button button-secondary button-large">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="Refresh">
                            <path id="Vector" d="M5 7.99999L8.99999 12H5.99999C5.99999 15.31 8.69 18 12 18C13.01 18 13.97 17.75 14.8 17.3L16.26 18.76C15.03 19.54 13.57 20 12 20C7.58 20 4 16.42 4 12H1L5 7.99999ZM18 12C18 8.68999 15.31 6 12 6C10.99 6 10.03 6.25 9.2 6.7L7.73999 5.23999C8.96999 4.45999 10.43 4 12 4C16.42 4 20 7.57999 20 12H23L19 16L15 12H18Z" fill="#003c79"/>
                            </g>
                            </svg>
                        </span>
                    </div>
                    <?php } ?>
                    <div class='tf-tour-cal'></div>
                    <div class="tf-tour-cal-field">

                        <div class="bulk-popup-content">
                            <div class="bulk-popup-content-box">

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
                                        <label class="tf-field-label"><?php echo esc_html__( 'Tour starts', 'tourfic' ); ?></label>
                                        <div class="tf-field-text-group">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <input readonly="readonly" type="text" class="tf_tour_check_in" name="tf_tour_check_in" placeholder="<?php echo esc_html__( 'Select tour start date', 'tourfic' ); ?>">
                                        </div>
                                    </div>

                                    <div class="tf-field-text tf-check-dates" style="width: calc(50% - 12px)">
                                        <label class="tf-field-label"><?php echo esc_html__( 'Tour ends', 'tourfic' ); ?></label>
                                        <div class="tf-field-text-group">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <input readonly="readonly" type="text" class="tf_tour_check_out" name="tf_tour_check_out" placeholder="<?php echo esc_html__( 'Select tour end date', 'tourfic' ); ?>">
                                        </div>
                                    </div>

                                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) { ?>
                                    <div class="tf-field tf-field-repeater" style="width:100%; display: <?php echo esc_attr( $pricing_type == 'person' || $pricing_type == 'group' ? 'block' : 'none' ) ?>;">
                                        <div class="tf-fieldset">
                                            <div id="tf-repeater-1" class="tf-repeater allowed_time" data-max-index="0">
                                            <div class="tf-repeater-wrap tf_tour_allowed_times tf_tour_saved_allowed_times tf-repeater-wrap-allowed_time ui-sortable">

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
                                    <?php } ?>

                                    <div class="tf-field-select" style="width: 100%">
                                        <label class="tf-field-label"><?php echo esc_html__( 'Booking status', 'tourfic' ); ?></label>
                                        <select name="tf_tour_status" class="tf-select">
                                            <option value="available"><?php echo esc_html__( 'Available', 'tourfic' ); ?></option>
                                            <option value="unavailable"><?php echo esc_html__( 'Unavailable', 'tourfic' ); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="tf-field tf-field-accordion tf-field-group-box tf-show-for-person tf-show-for-group" style="width: 100%; display: <?php echo esc_attr( $pricing_type == 'person' || $pricing_type == 'group' ? 'block' : 'none' ) ?>;">
                                    <div class="tf-fieldset">

                                        <div id="adult_tabs" class="tf-tab-switch-box tf-show-for-person active-repeater"  style="width: 100%; display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>;">
                                            <div class="tf-tab-field-header">
                                                <div class="tf-field-collapas">
                                                    <div class="field-label"><?php echo esc_html__( 'Adult', 'tourfic' ); ?></div>
                                                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                </div>
                                            </div>

                                            <div class="tf-tab-field-content">
                                                <div class="tf-field tf-field-number" style="width: 100%;">
                                                    <label for="" class="tf-field-label">
                                                    <?php echo esc_html__( 'Price for Adult', 'tourfic' ); ?>
                                                        <span class="tf-desc-tooltip">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_1017_4247)">
                                                                    <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_1017_4247">
                                                                        <rect width="16" height="16" fill="white"/>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                            <div class="tf-desc-tooltip-content">
                                                            <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                            </div>
                                                        </span>
                                                    </label>

                                                    <div class="tf-fieldset">
                                                        <input type="number" name="tf_tour_adult_price" min="0">
                                                    </div>
                                                </div>
                                            </div> <!-- .tf-tab-field-content -->
                                        </div> <!-- #adult_tabs -->

                                        <div id="child_tabs" class="tf-tab-switch-box tf-show-for-person active-repeater"  style="width: 100%; display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>;">
                                            <div class="tf-tab-field-header">
                                                <div class="tf-field-collapas">
                                                    <div class="field-label"><?php echo esc_html__( 'Child', 'tourfic' ); ?></div>
                                                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                </div>
                                            </div>

                                            <div class="tf-tab-field-content">
                                                <div class="tf-field tf-field-number" style="width: 100%;">
                                                    <label for="" class="tf-field-label">
                                                    <?php echo esc_html__( 'Price for Child', 'tourfic' ); ?>
                                                        <span class="tf-desc-tooltip">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_1017_4247)">
                                                                    <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_1017_4247">
                                                                        <rect width="16" height="16" fill="white"/>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                            <div class="tf-desc-tooltip-content">
                                                            <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                            </div>
                                                        </span>
                                                    </label>

                                                    <div class="tf-fieldset">
                                                        <input type="number" name="tf_tour_child_price" min="0">
                                                    </div>
                                                </div>
                                            </div> <!-- .tf-tab-field-content -->
                                        </div> <!-- #child_tabs -->

                                        <div id="infant_tabs" class="tf-tab-switch-box tf-show-for-person active-repeater"  style="width: 100%; display: <?php echo esc_attr( $pricing_type == 'person' ? 'block' : 'none' ) ?>;">
                                            <div class="tf-tab-field-header">
                                                <div class="tf-field-collapas">
                                                    <div class="field-label"><?php echo esc_html__( 'Infant', 'tourfic' ); ?></div>
                                                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                </div>
                                            </div>

                                            <div class="tf-tab-field-content">
                                                <div class="tf-field tf-field-number" style="width: 100%;">
                                                    <label for="" class="tf-field-label">
                                                    <?php echo esc_html__( 'Price for Infant', 'tourfic' ); ?>
                                                        <span class="tf-desc-tooltip">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_1017_4247)">
                                                                    <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_1017_4247">
                                                                        <rect width="16" height="16" fill="white"/>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                            <div class="tf-desc-tooltip-content">
                                                            <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                            </div>
                                                        </span>
                                                    </label>

                                                    <div class="tf-fieldset">
                                                        <input type="number" name="tf_tour_infant_price" min="0">
                                                    </div>
                                                </div>
                                            </div> <!-- .tf-tab-field-content -->
                                        </div> <!-- #infant_tabs -->

                                        <div id="group_tabs" class="tf-tab-switch-box tf-show-for-group active-repeater" style="width: 100%; display: <?php echo esc_attr( $pricing_type == 'group' ? 'block' : 'none' ) ?>;">
                                            <div class="tf-tab-field-header">
                                                <div class="tf-field-collapas">
                                                    <div class="field-label"><?php echo esc_html__( 'Group', 'tourfic' ); ?></div>
                                                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                </div>
                                            </div>

                                            <div class="tf-tab-field-content">
                                                <div class="tf-field tf-field-number" style="width: 100%;">
                                                    <label for="" class="tf-field-label">
                                                    <?php echo esc_html__( 'Price for group', 'tourfic' ); ?>
                                                        <span class="tf-desc-tooltip">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_1017_4247)">
                                                                    <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_1017_4247">
                                                                        <rect width="16" height="16" fill="white"/>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                            <div class="tf-desc-tooltip-content">
                                                            <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                            </div>
                                                        </span>
                                                    </label>

                                                    <div class="tf-fieldset">
                                                        <input type="number" name="tf_tour_price" min="0">
                                                    </div>
                                                </div>
                                            </div> <!-- .tf-tab-field-content -->
                                        </div> <!-- #group_tabs -->
                                        
                                        <div class="tf-field tf-field-number" style="width:calc(33% - 10px);">
                                            <label for="" class="tf-field-label">
                                                <?php echo esc_html__( 'Min', 'tourfic' ); ?>
                                            </label>    
                                            <div class="tf-fieldset">
                                                <input type="number" name="tf_tour_min_person" id="tf_tour_min_person" value="" min="0">            
                                            </div>
                                            <span class="tf-field-sub-title">
                                                <?php echo esc_html__( 'Minimum Person (Required for Search)', 'tourfic' ); ?>
                                            </span>
                                        </div>

                                        <div class="tf-field tf-field-number" style="width:calc(33% - 10px);">
                                            <label for="" class="tf-field-label">
                                                <?php echo esc_html__( 'Max', 'tourfic' ); ?>
                                            </label>    
                                            <div class="tf-fieldset">
                                                <input type="number" name="tf_tour_max_person" id="tf_tour_max_person" value="" min="0">            
                                            </div>
                                            <span class="tf-field-sub-title">
                                                <?php echo esc_html__( 'Maximum Person (Required for Search)', 'tourfic' ); ?>
                                            </span>
                                        </div>

                                        <div class="tf-field tf-field-number" style="width:calc(33% - 10px);">
                                            <label for="" class="tf-field-label">
                                                <?php echo esc_html__( 'Capacity', 'tourfic' ); ?>
                                            </label>    
                                            <div class="tf-fieldset">
                                                <input type="number" name="tf_tour_max_capacity" id="tf_tour_max_capacity" value="" min="0">            
                                            </div>
                                            <span class="tf-field-sub-title">
                                                <?php echo esc_html__( 'Maximum Capacity of this tour', 'tourfic' ); ?>
                                            </span>
                                        </div>
                                    
                                    </div>
                                </div>

                                <div class="tf-field-repeater tf-package-field-repeater tf-show-for-package" style="width: 100%; display: <?php echo $pricing_type == 'package' && function_exists( 'is_tf_pro' ) && is_tf_pro() ? esc_attr('block') : esc_attr('none'); ?>">
                                    <div class="tf-repeater">
                                        <div class="tf-field" style="padding-top: 0px">
                                            <label class="tf-field-label"><?php echo esc_html__('Packages', 'tourfic'); ?></label>
                                            <div class="tf-field-sub-title">
                                                <?php echo esc_html__('You can add, customize any packages from here.', 'tourfic'); ?>
                                            </div>
                                        </div>
                                        <div class="tf-repeater-wrap">
                                        <?php
                                        if ( ! empty( $tour_package_options ) ) {
                                            foreach ( $tour_package_options as $key => $item ) {
                                                if(empty($item['pack_status']) || empty($item['pack_title'])){
                                                    continue;
                                                }
                                                $option_pricing_type = ! empty( $item['pricing_type'] ) ? $item['pricing_type'] : 'person';
                                                ?>

                                                <div class="tf-single-repeater">
                                                    <div class="tf-repeater-header">
                                                        <div class="tf-repeater-header-info">
                                                            <span class="tf-repeater-title tf-avail-repeater-title"><?php echo esc_html( $item['pack_title'] ); ?></span>
                                                            <div class="tf-repeater-icon-absulate">
                                                                <span class="tf-repeater-icon tf-repeater-icon-collapse tf-avail-repeater-collapse">
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M8 13.332H14" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                        <path d="M11 2.33218C11.2652 2.06697 11.6249 1.91797 12 1.91797C12.1857 1.91797 12.3696 1.95455 12.5412 2.02562C12.7128 2.09669 12.8687 2.20086 13 2.33218C13.1313 2.4635 13.2355 2.61941 13.3066 2.79099C13.3776 2.96257 13.4142 3.14647 13.4142 3.33218C13.4142 3.5179 13.3776 3.7018 13.3066 3.87338C13.2355 4.04496 13.1313 4.20086 13 4.33218L4.66667 12.6655L2 13.3322L2.66667 10.6655L11 2.33218Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                        <path d="M10 3.33203L12 5.33203" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="tf-repeater-content-wrap" style="display: none;">
                                                        <div class="tf-field tf-field-accordion" style="width: 100%;">
                                                            <div class="tf-fieldset">

                                                                <div id="adult_tabs" class="tf-tab-switch-box"  style="display: <?php echo $option_pricing_type == 'person' && !empty($item['adult_tabs'][0]['disable_adult_price']) ? 'block' : 'none' ?>;">
                                                                    <div class="tf-tab-field-header">
                                                                        <div class="tf-field-collapas">
                                                                            <div class="field-label"><?php echo esc_html__( 'Adult', 'tourfic' ); ?></div>
                                                                            <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                                        </div>
                                                                    </div>

                                                                    <div class="tf-tab-field-content">
                                                                        <div class="tf-field tf-field-number" style="width: 100%;">
                                                                            <label for="tf_tours_opt[adult_tabs][adult_price]" class="tf-field-label">
                                                                            <?php echo esc_html__( 'Price for Adult', 'tourfic' ); ?>
                                                                                <span class="tf-desc-tooltip">
                                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <g clip-path="url(#clip0_1017_4247)">
                                                                                            <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                        </g>
                                                                                        <defs>
                                                                                            <clipPath id="clip0_1017_4247">
                                                                                                <rect width="16" height="16" fill="white"/>
                                                                                            </clipPath>
                                                                                        </defs>
                                                                                    </svg>
                                                                                    <div class="tf-desc-tooltip-content">
                                                                                    <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                                                    </div>
                                                                                </span>
                                                                            </label>

                                                                            <div class="tf-fieldset">
                                                                                <input type="number" name="tf_option_adult_price_<?php echo esc_attr( $key ); ?>" min="0">
                                                                            </div>
                                                                        </div>
                                                                    </div> <!-- .tf-tab-field-content -->
                                                                </div> <!-- #adult_tabs -->

                                                                <div id="child_tabs" class="tf-tab-switch-box"  style="display: <?php echo $option_pricing_type == 'person' && !empty($item['child_tabs'][0]['disable_child_price']) ? 'block' : 'none' ?>;">
                                                                    <div class="tf-tab-field-header">
                                                                        <div class="tf-field-collapas">
                                                                            <div class="field-label"><?php echo esc_html__( 'Child', 'tourfic' ); ?></div>
                                                                            <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                                        </div>
                                                                    </div>

                                                                    <div class="tf-tab-field-content">
                                                                        <div class="tf-field tf-field-number" style="width: 100%;">
                                                                            <label for="" class="tf-field-label">
                                                                            <?php echo esc_html__( 'Price for Child', 'tourfic' ); ?>
                                                                                <span class="tf-desc-tooltip">
                                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <g clip-path="url(#clip0_1017_4247)">
                                                                                            <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                        </g>
                                                                                        <defs>
                                                                                            <clipPath id="clip0_1017_4247">
                                                                                                <rect width="16" height="16" fill="white"/>
                                                                                            </clipPath>
                                                                                        </defs>
                                                                                    </svg>
                                                                                    <div class="tf-desc-tooltip-content">
                                                                                    <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                                                    </div>
                                                                                </span>
                                                                            </label>

                                                                            <div class="tf-fieldset">
                                                                                <input type="number" name="tf_option_child_price_<?php echo esc_attr( $key ); ?>" min="0">
                                                                            </div>
                                                                        </div>
                                                                    </div> <!-- .tf-tab-field-content -->
                                                                </div> <!-- #child_tabs -->

                                                                <div id="infant_tabs" class="tf-tab-switch-box"  style="display: <?php echo $option_pricing_type == 'person' && !empty($item['infant_tabs'][0]['disable_infant_price']) ? 'block' : 'none' ?>;">
                                                                    <div class="tf-tab-field-header">
                                                                        <div class="tf-field-collapas">
                                                                            <div class="field-label"><?php echo esc_html__( 'Infant', 'tourfic' ); ?></div>
                                                                            <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                                        </div>
                                                                    </div>

                                                                    <div class="tf-tab-field-content">
                                                                        <div class="tf-field tf-field-number" style="width: 100%;">
                                                                            <label for="" class="tf-field-label">
                                                                            <?php echo esc_html__( 'Price for Infant', 'tourfic' ); ?>
                                                                                <span class="tf-desc-tooltip">
                                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <g clip-path="url(#clip0_1017_4247)">
                                                                                            <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                        </g>
                                                                                        <defs>
                                                                                            <clipPath id="clip0_1017_4247">
                                                                                                <rect width="16" height="16" fill="white"/>
                                                                                            </clipPath>
                                                                                        </defs>
                                                                                    </svg>
                                                                                    <div class="tf-desc-tooltip-content">
                                                                                    <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                                                    </div>
                                                                                </span>
                                                                            </label>

                                                                            <div class="tf-fieldset">
                                                                                <input type="number" name="tf_option_infant_price_<?php echo esc_attr( $key ); ?>" min="0">
                                                                            </div>
                                                                        </div>
                                                                    </div> <!-- .tf-tab-field-content -->
                                                                </div> <!-- #infant_tabs -->

                                                                <div id="group_tabs" class="tf-tab-switch-box"  style="display: <?php echo $option_pricing_type == 'group' ? 'block' : 'none' ?>;">
                                                                    <div class="tf-tab-field-header">
                                                                        <div class="tf-field-collapas">
                                                                            <div class="field-label"><?php echo esc_html__( 'Group', 'tourfic' ); ?></div>
                                                                            <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                                        </div>
                                                                    </div>

                                                                    <div class="tf-tab-field-content">
                                                                        <div class="tf-field tf-field-number" style="width: 100%;">
                                                                            <label for="" class="tf-field-label">
                                                                            <?php echo esc_html__( 'Price for Group', 'tourfic' ); ?>
                                                                                <span class="tf-desc-tooltip">
                                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <g clip-path="url(#clip0_1017_4247)">
                                                                                            <path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                        </g>
                                                                                        <defs>
                                                                                            <clipPath id="clip0_1017_4247">
                                                                                                <rect width="16" height="16" fill="white"/>
                                                                                            </clipPath>
                                                                                        </defs>
                                                                                    </svg>
                                                                                    <div class="tf-desc-tooltip-content">
                                                                                    <?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
                                                                                    </div>
                                                                                </span>
                                                                            </label>

                                                                            <div class="tf-fieldset">
                                                                                <input type="number" name="tf_option_group_price_<?php echo esc_attr( $key ); ?>" min="0">
                                                                            </div>
                                                                        </div>
                                                                        <?php 
                                                                        if(!empty($item['group_tabs'][4]['group_discount'])){ ?>
                                                                        <div class="tf-field tf-field-repeater" style="width:100%;">
                                                                            <div class="tf-fieldset">
                                                                                <div id="tf-repeater-1" class="tf-repeater group_discount_package" data-max-index="0">
                                                                                <div class="tf-repeater-wrap tf-repeater-wrap-group_discount_package ui-sortable tf-group-discount-package_<?php echo esc_attr( $key ); ?>">

                                                                                </div>
                                                                                <div class=" tf-single-repeater-clone tf-single-repeater-clone-group_discount_package">
                                                                                    <div class="tf-single-repeater tf-single-repeater-group_discount_package">
                                                                                    <input type="hidden" name="tf_parent_field" value="[group_tabs]">
                                                                                    <input type="hidden" name="tf_repeater_count" value="0">
                                                                                    <input type="hidden" name="tf_current_field" value="group_discount_package">
                                                                                    
                                                                                    <div class="tf-repeater-content-wrap" style="display: none;">
                                                                                        <div class="tf-field tf-field-number  " style="width:calc(66% - 10px);">
                                                                                            
                                                                                        <div class="tf-fieldset">
                                                                                            <div class="tf-number-range">
                                                                                            <div class="tf-number-field-box">
                                                                                                <i class="fa-regular fa-user"></i>
                                                                                                <input type="number" name="tf_option_<?php echo esc_attr( $key ); ?>_group_discount[min_person][]" value="" min="0" placeholder="<?php echo esc_html('Min Person', 'tourfic'); ?>">
                                                                                            </div>
                                                                                            <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                <path d="M15.5 6.66797L18.8333 10.0013L15.5 13.3346" stroke="#95A3B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                                <path d="M2.1665 10H18.8332" stroke="#95A3B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                            </svg>
                                                                                            <div class="tf-number-field-box">
                                                                                                <i class="fa-regular fa-user"></i>
                                                                                                <input type="number" name="tf_option_<?php echo esc_attr( $key ); ?>_group_discount[max_person][]" value="" min="0" placeholder="<?php echo esc_html('Max Person', 'tourfic'); ?>">
                                                                                            </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        </div>
                                                                                        <div class="tf-field tf-field-number  " style="width:calc(33% - 10px);">
                                                                                        <div class="tf-fieldset">
                                                                                            <input type="number" name="tf_option_<?php echo esc_attr( $key ); ?>_group_discount[price][]" value="" min="0" placeholder="<?php echo esc_html('Price', 'tourfic'); ?>">
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
                                                                                <div class="tf-repeater-add tf-repeater-add-group_discount_package">
                                                                                    <span data-repeater-id="group_discount_package" data-repeater-max="" class="tf-repeater-icon tf-repeater-icon-add tf-repeater-add-group_discount_package">
                                                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <g clip-path="url(#clip0_1017_2374)">
                                                                                        <path d="M9.99984 18.3346C14.6022 18.3346 18.3332 14.6037 18.3332 10.0013C18.3332 5.39893 14.6022 1.66797 9.99984 1.66797C5.39746 1.66797 1.6665 5.39893 1.6665 10.0013C1.6665 14.6037 5.39746 18.3346 9.99984 18.3346Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                        <path d="M6.6665 10H13.3332" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                        <path d="M10 6.66797V13.3346" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                        </g>
                                                                                        <defs>
                                                                                        <clipPath id="clip0_1017_2374">
                                                                                            <rect width="20" height="20" fill="white"></rect>
                                                                                        </clipPath>
                                                                                        </defs>
                                                                                    </svg><?php echo esc_html('Add New Discount', 'tourfic'); ?></span>
                                                                                </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php } ?>


                                                                    </div> <!-- .tf-tab-field-content -->
                                                                </div> <!-- #group_tabs -->

                                                                <!-- repeated package times -->
                                                                <div class="tf-field tf-field-repeater tf-package-time-fields" style="width:100%; display: <?php echo esc_attr( ($tour_avail_type =='fixed' || $tour_avail_type =='continuous') ? 'block' : 'none' ) ?>">
                                                                    <div class="tf-fieldset">
                                                                        <div id="tf-repeater-1" class="tf-repeater allowed_time" data-max-index="0">
                                                                        <div class="tf-repeater-wrap tf_tour_allowed_times tf_tour_allowed_times tf-repeater-wrap-allowed_time ui-sortable tf-tour-package-allowed-time_<?php echo esc_attr( $key ); ?>">

                                                                        </div>
                                                                        <div class=" tf-single-repeater-clone tf-single-repeater-clone-allowed_time">
                                                                            <div class="tf-single-repeater tf-single-repeater-allowed_time">
                                                                                <input type="hidden" name="tf_parent_field" value="">
                                                                                <input type="hidden" name="tf_repeater_count" value="0">
                                                                                <input type="hidden" name="tf_current_field" value="allowed_time">
                                                                                <div class="tf-repeater-content-wrap">
                                                                                    <div class="tf-field tf-field-time" style="width: calc(50% - 6px);">
                                                                                        <div class="tf-fieldset">
                                                                                            <input type="text" name="tf_option_<?php echo esc_attr( $key ); ?>_allowed_time[time][]" placeholder="Select Time" value="" class="flatpickr flatpickr-input" data-format="h:i K" readonly="readonly">
                                                                                            <i class="fa-regular fa-clock"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="tf-field tf-field-number" style="width: calc(50% - 6px);">
                                                                                        <div class="tf-fieldset">
                                                                                            <input type="number" name="tf_option_<?php echo esc_attr( $key ); ?>_allowed_time[cont_max_capacity][]" id="allowed_time[cont_max_capacity]" value="" placeholder="<?php echo esc_html__( 'Maximum Capacity', 'tourfic' ); ?>">
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
                                                                        <div class="tf-repeater-add tf-repeater-add-allowed_time tf-package-add-allowed-time">
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

                                                                <input type="hidden" name="tf_option_title_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr($item['pack_title']); ?>"/>
                                                                <input type="hidden" name="tf_option_pricing_type_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr($option_pricing_type); ?>"/>
                                                            </div>
                                                        </div> <!-- .tf-field-accordion -->
                                                    </div> <!-- .tf-repeater-content-wrap -->
                                                </div> <!-- .tf-single-repeater -->

                                                <?php
                                            }
                                        }
                                        ?>
                                        </div> <!-- .tf-repeater-wrap -->
                                    </div> <!-- .tf-repeater -->
                                </div> <!-- .tf-field-repeater -->


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

                                <div style="width: 100%; display: flex; justify-content: space-between;">
                                    <input type="hidden" name="new_post" value="<?php echo $this->value ? 'false' : 'true'; ?>">
                                    <input type="hidden" name="tour_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                                    <input type="hidden" name="bulk_edit_option" class="tf_bulk_edit_option">
                                    <div class="tf-save-calendar">
                                        <span class="tf_tour_cal_update button button-primary button-large"><?php echo esc_html__( 'Save', 'tourfic' ); ?></span>

                                        <span class="tf_tour_cal_reset button button-secondary button-large"><?php echo esc_html__( 'Reset Calendar', 'tourfic' ); ?></span>
                                    </div>
                                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) { ?>
                                    <span class="tf_tour_cal_bulk_edit button button-secondary button-large">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 20H21" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16.5 3.50023C16.8978 3.1024 17.4374 2.87891 18 2.87891C18.2786 2.87891 18.5544 2.93378 18.8118 3.04038C19.0692 3.14699 19.303 3.30324 19.5 3.50023C19.697 3.69721 19.8532 3.93106 19.9598 4.18843C20.0665 4.4458 20.1213 4.72165 20.1213 5.00023C20.1213 5.2788 20.0665 5.55465 19.9598 5.81202C19.8532 6.06939 19.697 6.30324 19.5 6.50023L7 19.0002L3 20.0002L4 16.0002L16.5 3.50023Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M15 5L18 8" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>    
                                        <?php echo esc_html__( 'Bulk Add', 'tourfic' ); ?>
                                    </span>
                                    <?php } ?>
                                </div>

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