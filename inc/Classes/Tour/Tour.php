<?php

namespace Tourfic\Classes\Tour;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Tour\Tour_Price;
use \Tourfic\Classes\Tour\Pricing;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Icons_Manager;

class Tour {

	use \Tourfic\Traits\Singleton;

	public function __construct() {
		\Tourfic\Classes\Tour\Tour_CPT::instance();

		if ( Helper::tf_is_woo_active() ) {
			if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-tour.php' ) ) {
				require_once TF_INC_PATH . 'functions/woocommerce/wc-tour.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-tour.php' );
			}
		}
		$tf_tours_autodrafts = ! empty( Helper::tfopt( 't-auto-draft' ) ) ? Helper::tfopt( 't-auto-draft' ) : '';
		if ( ! empty( $tf_tours_autodrafts ) ) {
			add_action( 'tf_everydate_cron_job', 'tf_every_date_function' );
		}

		add_action( 'wp_ajax_tf_tour_search', array( $this, 'tf_tour_search_ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_tour_search', array( $this, 'tf_tour_search_ajax_callback' ) );
		add_action( 'wp', array( $this, 'tf_setup_everydate_cron_job' ) );
		add_action( 'init', array( $this, 'tf_tours_custom_status_creation' ) );
		add_action( 'admin_footer-edit.php', array( $this, 'tf_tours_custom_status_add_in_quick_edit' ) );
		add_action( 'admin_footer-post.php', array( $this, 'tf_tours_custom_status_add_in_post_page' ) );
		add_action( 'admin_footer-post-new.php', array( $this, 'tf_tours_custom_status_add_in_post_page' ) );
		add_action( 'wp_after_insert_post', array( $this, 'tf_tour_type_assign_taxonomies'), 100, 3 );
		add_action( 'wp_ajax_nopriv_tf_tour_booking_popup', array( $this, 'tf_tour_booking_popup_callback' ) );
		add_action( 'wp_ajax_tf_tour_booking_popup', array( $this, 'tf_tour_booking_popup_callback' ) );
	}

	/**
	 * Tour Search form
	 *
	 * Horizontal
	 *
	 * Called in shortcodes
	 */
	static function tf_tour_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design ) {

		// date Format for User Output
		$tour_date_format_for_users   = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$disable_child_search         = ! empty( Helper::tfopt( 'disable_child_search' ) ) ? Helper::tfopt( 'disable_child_search' ) : '';
		$disable_infant_search        = ! empty( Helper::tfopt( 'disable_infant_search' ) ) ? Helper::tfopt( 'disable_infant_search' ) : '';
		$tour_location_field_required = ! empty( Helper::tfopt( 'tour_location_field_required' ) ) ? Helper::tfopt( 'tour_location_field_required' ) : '';
		if ( ! empty( $design ) && 2 == $design ) {
			?>
            <form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
                <div class="tf_hotel_searching">
                    <div class="tf_form_innerbody">
                        <div class="tf_form_fields">
                            <div class="tf_destination_fields">
                                <label class="tf_label_location">
                                    <span class="tf-label"><?php esc_html_e( 'Destination', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners tf_form-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z"
                                                  fill="#FAEEDD"/>
                                        </svg>
                                        <input type="text" name="place-name" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="tf-destination" class=""
                                               placeholder="<?php esc_html_e( 'Enter Destination', 'tourfic' ); ?>" value="">
                                        <input type="hidden" name="place" id="tf-search-tour" class="tf-place-input"/>
                                    </div>
                                </label>
                            </div>

                            <div class="tf_checkin_date">
                                <label class="tf_label_checkin tf_tour_check_in_out_date">
                                    <span class="tf-label"><?php esc_html_e( 'Start Date', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkin_dates">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
											<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
                                        </div>
                                    </div>
                                </label>

                                <input type="text" name="check-in-out-date" class="tf-tour-check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_tour_search' ) ? 'required' : ''; ?>>
                            </div>

                            <div class="tf_checkin_date tf_tour_check_in_out_date">
                                <label class="tf_label_checkin">
                                    <span class="tf-label"><?php esc_html_e( 'End Date', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkout_dates">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
											<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="tf_guest_info tf_selectperson-wrap">
                                <label class="tf_label_checkin tf_input-inner">
                                    <span class="tf-label"><?php esc_html_e( 'Guests', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_guest_calculation">
                                            <div class="tf_guest_number">
                                                <span class="guest"><?php esc_html_e( '1', 'tourfic' ); ?></span>
                                                <span class="label"><?php esc_html_e( 'Guests', 'tourfic' ); ?></span>
                                            </div>
                                        </div>
                                        <div class="tf_check_arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>

                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" class="adults-style2" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1'; ?>" readonly>
                                                <div class="acr-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
										<?php
										if ( empty( $disable_child_search ) ) {
											?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="children" class="childs-style2" id="children" min="0" value="0" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php }
										if ( empty( $disable_infant_search ) ) {
											?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="infant" class="infant-style2" id="infant" min="0" value="0" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tf_availability_checker_box">
                            <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
							<?php
							if ( $author ) { ?>
                                <input type="hidden" name="tf-author" value="<?php echo esc_attr( $author ); ?>" class="tf-post-type"/>
							<?php } ?>
                            <button><?php echo esc_html_e( "Check availability", "tourfic" ); ?></button>
                        </div>
                    </div>
                </div>

            </form>
            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr locale first day of Week
						<?php Helper::tf_flatpickr_locale( "root" ); ?>

                        $(".tf_tour_check_in_out_date").on("click", function () {
                            $(".tf-tour-check-in-out-date").trigger("click");
                        });
                        $(".tf-tour-check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",

                            // flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                        });

                        function dateSetToFields(selectedDates, instance) {
                            if (selectedDates.length === 2) {
                                const monthNames = [
                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                if (selectedDates[0]) {
                                    const startDate = selectedDates[0];
                                    $(".tf_tour_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
                                    $(".tf_tour_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
                                }
                                if (selectedDates[1]) {
                                    const endDate = selectedDates[1];
                                    $(".tf_tour_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
                                    $(".tf_tour_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
                                }
                            }
                        }

                    });
                })(jQuery);
            </script>
		<?php }elseif( !empty($design) && 3==$design ){ ?>

			<form class="tf-archive-search-box-wrapper <?php echo esc_attr( $classes ); ?>" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">

				<div class="tf-date-selection-form">
				<div class="tf-date-select-box tf-flex tf-flex-gap-8">
					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn tf-pick-drop-location full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_257_3711)">
                                        <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_257_3711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Destination", "tourfic"); ?></h5>
									<input type="text" name="place-name" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="tf-destination" class="" placeholder="<?php esc_html_e( 'Enter Destination', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" id="tf-search-tour" class="tf-place-input"/>
								</div>
							</div>
						</div>

					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Check-in & Check-out Date", "tourfic"); ?></h5>
									<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                           placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_tour_search' ) ? 'required' : ''; ?>>
								</div>
							</div>
						</div>

					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9.99992 10.8333C12.3011 10.8333 14.1666 8.96785 14.1666 6.66667C14.1666 4.36548 12.3011 2.5 9.99992 2.5C7.69873 2.5 5.83325 4.36548 5.83325 6.66667C5.83325 8.96785 7.69873 10.8333 9.99992 10.8333ZM9.99992 10.8333C11.768 10.8333 13.4637 11.5357 14.714 12.786C15.9642 14.0362 16.6666 15.7319 16.6666 17.5M9.99992 10.8333C8.23181 10.8333 6.53612 11.5357 5.28587 12.786C4.03563 14.0362 3.33325 15.7319 3.33325 17.5" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Guests", "tourfic"); ?></h5>
									<div class="tf_selectperson-wrap">
										<div class="tf_input-inner">
											<div class="adults-text"><?php esc_html_e( '1 Adults', 'tourfic' ); ?></div>
											<?php
											if ( empty( $disable_child_search ) ) {
												?>
												<div class="person-sep"></div>
												<div class="child-text"><?php esc_html_e( '0 Children', 'tourfic' ); ?></div>
											<?php }
											if ( empty( $disable_infant_search ) ) {
												?>
												<div class="person-sep"></div>
												<div class="infant-text"><?php esc_html_e( '0 Infant', 'tourfic' ); ?></div>
											<?php } ?>
										</div>
										<div class="tf_acrselection-wrap">
											<div class="tf_acrselection-inner">
												<div class="tf_acrselection">
													<div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
													<div class="acr-select">
														<div class="acr-dec">-</div>
														<input type="number" name="adults" id="adults" min="1" value="1">
														<div class="acr-inc">+</div>
													</div>
												</div>
												<?php
												if ( empty( $disable_child_search ) ) {
													?>
													<div class="tf_acrselection">
														<div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
														<div class="acr-select">
															<div class="acr-dec">-</div>
															<input type="number" name="children" id="children" min="0" value="0">
															<div class="acr-inc">+</div>
														</div>
													</div>
												<?php }
												if ( empty( $disable_infant_search ) ) {
													?>
													<div class="tf_acrselection">
														<div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
														<div class="acr-select">
															<div class="acr-dec">-</div>
															<input type="number" name="infant" id="infant" min="0" value="0">
															<div class="acr-inc">+</div>
														</div>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="tf-driver-location-box">
					<div class="tf-submit-button">
						<input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
						<button type="submit" class="tf_btn tf-flex-align-center"><?php echo esc_html( apply_filters("tf_tour_search_form_submit_button_text", 'Search' ) ); ?>  <i class="ri-search-line"></i></button>
					</div>
				</div>
				</div>
            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {
						// flatpickr first day of Week
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

						$("#tf_tour_aval_check #check-in-out-date").flatpickr({
							enableTime: false,
							mode: "range",
							altInput: true,
							dateFormat: "Y/m/d",
							altFormat: '<?php echo esc_attr( $tour_date_format_for_users ); ?>',
							minDate: "today",

							// flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

							onReady: function (selectedDates, dateStr, instance) {
								instance.element.value = dateStr.replace(/[a-z]+/g, '-');
								instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
							},
							onChange: function (selectedDates, dateStr, instance) {
								instance.element.value = dateStr.replace(/[a-z]+/g, '-');
								instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
							},
						});
					});
                })(jQuery);
            </script>

		<?php } elseif (!empty($design) && 4 == $design) { ?>
			<form class="tf-archive-search-box-wrapper tf-search__form tf-shortcode-design-4 <?php echo esc_attr($classes); ?>" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
				<fieldset class="tf-search__form__fieldset">
					<!-- Destination -->
					<div class="tf-search__form__fieldset__left">
						<label for="tf-search__form-destination" class="tf-search__form__label">
							<?php echo esc_html_e("Destinations", "tourfic"); ?>
						</label>
						<div class="tf-search__form__field">
							<input type="text" name="place-name" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="tf-destination" class="tf-search__form__input" placeholder="<?php esc_html_e('Where you wanna stay?', 'tourfic'); ?>" value="">
							<input type="hidden" name="place" id="tf-search-tour" class="tf-place-input" />
							<span class="tf-search__form__field__icon icon--location">
								<svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M5.25 15.625C3.625 13.5938 0 8.75 0 6C0 2.6875 2.65625 0 6 0C9.3125 0 12 2.6875 12 6C12 8.75 8.34375 13.5938 6.71875 15.625C6.34375 16.0938 5.625 16.0938 5.25 15.625ZM6 8C7.09375 8 8 7.125 8 6C8 4.90625 7.09375 4 6 4C4.875 4 4 4.90625 4 6C4 7.125 4.875 8 6 8Z" fill="white" />
								</svg>
							</span>
						</div>
					</div>

					<div class="tf-search__form__fieldset__middle">
						<!-- Adult Person -->
						<div class="tf-search__form__group tf_selectperson-wrap">
							<label for="tf-search__form-adult" class="tf-search__form__label">
								<?php echo esc_html_e('Adult Person', 'tourfic'); ?>
							</label>
							<div class="tf-search__form__field tf-mx-width">
								<div class="tf-search__form__field__icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="41" height="40" viewBox="0 0 41 40" fill="none">
										<path d="M20.2222 20C22.3439 20 24.3787 19.1571 25.879 17.6569C27.3793 16.1566 28.2222 14.1217 28.2222 12C28.2222 9.87827 27.3793 7.84344 25.879 6.34315C24.3787 4.84285 22.3439 4 20.2222 4C18.1004 4 16.0656 4.84285 14.5653 6.34315C13.065 7.84344 12.2222 9.87827 12.2222 12C12.2222 14.1217 13.065 16.1566 14.5653 17.6569C16.0656 19.1571 18.1004 20 20.2222 20ZM17.3659 23C11.2097 23 6.22217 27.9875 6.22217 34.1437C6.22217 35.1687 7.05342 36 8.07842 36H32.3659C33.3909 36 34.2222 35.1687 34.2222 34.1437C34.2222 27.9875 29.2347 23 23.0784 23H17.3659Z" fill="#3E64E0" />
									</svg>
								</div>
								<div class="tf-search__form__field__incdec">
									<input type="number" name="adults" id="adults" class="tf-search__form__field__input field--title" min="1" value="1">
									<span class="tf-search__form__field__incdre__inc form--span acr-inc">
										<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
											<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
											<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
									<span class="tf-search__form__field__incdre__dec form--span acr-dec">
										<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
											<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
											<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
										</svg>
									</span>
								</div>
							</div>
						</div>

						<!-- Divider -->
						<div class="tf-search__form__divider"></div>

						<!-- Children -->
						<?php if (empty($disable_child_search)) : ?>
							<div class="tf-search__form__group tf_selectperson-wrap">
								<label for="tf-search__form-children" class="tf-search__form__label">
									<?php echo esc_html_e('Children', 'tourfic'); ?>
								</label>
								<div class="tf-search__form__field tf-mx-width">
									<div class="tf-search__form__field__icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="26" height="40" viewBox="0 0 26 40" fill="none">
											<path d="M7.99873 5C7.99873 3.67392 8.52552 2.40215 9.4632 1.46447C10.4009 0.526784 11.6727 0 12.9987 0C14.3248 0 15.5966 0.526784 16.5343 1.46447C17.472 2.40215 17.9987 3.67392 17.9987 5C17.9987 6.32608 17.472 7.59785 16.5343 8.53553C15.5966 9.47322 14.3248 10 12.9987 10C11.6727 10 10.4009 9.47322 9.4632 8.53553C8.52552 7.59785 7.99873 6.32608 7.99873 5ZM11.7487 30V37.5C11.7487 38.8828 10.6315 40 9.24873 40C7.86592 40 6.74873 38.8828 6.74873 37.5V22.4844L5.11592 25.0781C4.38155 26.25 2.83467 26.5938 1.67061 25.8594C0.506547 25.125 0.147172 23.5859 0.881547 22.4219L3.99873 17.4766C5.94405 14.375 9.34248 12.5 12.9987 12.5C16.655 12.5 20.0534 14.375 21.9987 17.4688L25.1159 22.4219C25.8503 23.5938 25.4987 25.1328 24.3347 25.8672C23.1706 26.6016 21.6237 26.25 20.8894 25.0859L19.2487 22.4844V37.5C19.2487 38.8828 18.1315 40 16.7487 40C15.3659 40 14.2487 38.8828 14.2487 37.5V30H11.7487Z" fill="#3E64E0" />
										</svg>
									</div>
									<div class="tf-search__form__field__incdec">
										<input type="number" name="children" id="children" class="tf-search__form__field__input field--title" min="0" value="0">
										<span class="tf-search__form__field__incdre__inc form--span acr-inc">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
												<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
											</svg>
										</span>
										<span class="tf-search__form__field__incdre__dec form--span acr-dec">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
												<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
											</svg>
										</span>
									</div>
								</div>
							</div>
							<!-- Divider -->
							<div class="tf-search__form__divider"></div>
						<?php endif; ?>
						<!-- Infant -->
						<?php if (empty($disable_infant_search)): ?>
							<div class="tf-search__form__group tf_selectperson-wrap">
								<label for="tf-search__form-infant" class="tf-search__form__label">
									<?php echo esc_html_e('Infant', 'tourfic'); ?>
								</label>
								<div class="tf-search__form__field tf-mx-width">
									<div class="tf-search__form__field__icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="31" height="40" viewBox="0 0 31 40" fill="none">
											<g clip-path="url(#clip0_2862_2125)">
												<path d="M20.5 5C20.5 3.67392 19.9732 2.40215 19.0355 1.46447C18.0978 0.526784 16.8261 0 15.5 0C14.1739 0 12.9021 0.526784 11.9644 1.46447C11.0268 2.40215 10.5 3.67392 10.5 5C10.5 6.32608 11.0268 7.59785 11.9644 8.53553C12.9021 9.47322 14.1739 10 15.5 10C16.8261 10 18.0978 9.47322 19.0355 8.53553C19.9732 7.59785 20.5 6.32608 20.5 5ZM12.4453 13.2266C10.5937 12.5703 8.96872 11.3281 7.85153 9.64844L6.32809 7.35938C5.56247 6.21094 4.01559 5.90625 2.85934 6.67188C1.70309 7.4375 1.39841 8.99219 2.16403 10.1406L3.69528 12.4219C5.10934 14.5391 7.03903 16.2031 9.24997 17.2969V37.5C9.24997 38.8828 10.3672 40 11.75 40C13.1328 40 14.25 38.8828 14.25 37.5V30H16.75V37.5C16.75 38.8828 17.8672 40 19.25 40C20.6328 40 21.75 38.8828 21.75 37.5V17.3125C24.0234 16.2031 26 14.4844 27.4297 12.2969L28.8515 10.1172C29.6015 8.96094 29.2734 7.41406 28.1172 6.65625C26.9609 5.89844 25.414 6.22656 24.6562 7.39062L23.2343 9.5625C21.5312 12.1719 18.6328 13.75 15.5156 13.75C14.5312 13.75 13.5703 13.5938 12.6562 13.2969C12.5859 13.2734 12.5156 13.2422 12.4453 13.2266Z" fill="#3E64E0" />
											</g>
											<defs>
												<clipPath id="clip0_2862_2125">
													<rect width="30" height="40" fill="white" transform="translate(0.5)" />
												</clipPath>
											</defs>
										</svg>
									</div>
									<div class="tf-search__form__field__incdec">
										<input type="number" name="infant" class="infant-style2 tf-search__form__field__input field--title" id="infant" min="0" value="0">
										<span class="tf-search__form__field__incdre__inc form--span acr-inc">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
												<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
											</svg>
										</span>
										<span class="tf-search__form__field__incdre__dec form--span acr-dec">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
												<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
											</svg>
										</span>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<!-- Divider -->
						<div class="tf-search__form__divider"></div>
						<!-- Check-in -->
						<div class="tf-search__form__group tf-checkin-group">
							<div class="tf_tour_check_in_out_date">
								<label for="tf-search__form-checkin" class="tf-search__form__label">
									<?php echo esc_html_e('Select Date', 'tourfic'); ?>
								</label>
								<div class="tf-search__form__field">
									<div class="tf-search__form__field__icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
											<g clip-path="url(#clip0_2862_2140)">
												<path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
											</g>
											<defs>
												<clipPath id="clip0_2862_2140">
													<rect width="35" height="40" fill="white" transform="translate(0.777832)" />
												</clipPath>
											</defs>
										</svg>
									</div>
									<div class="tf_checkin_dates tf-flex tf-flex-align-center">
										<span class="date field--title"><?php echo esc_html(gmdate('d')); ?></span>
										<div class="tf-search__form__field__mthyr">
											<span class="month form--span"><?php echo esc_html(gmdate('M')); ?></span>
											<span class="year form--span"><?php echo esc_html(gmdate('Y')); ?></span>
										</div>
									</div>

								</div>
							</div>
							<input type="hidden" name="check-in-out-date" class="tf-tour-check-in-out-date tf-check-inout-hidden" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_html_e('Select Date', 'tourfic'); ?>" <?php echo Helper::tfopt('date_tour_search') ? 'required' : ''; ?>>
						</div>
						<!-- label to -->
						<div class="tf_checkin_to_label">
							<?php echo esc_html_e('To', 'tourfic'); ?>
						</div>
						<!-- Check-out -->
						<div class="tf-search__form__group tf_tour_check_in_out_date tf-checkout-group">
							<label for="tf-search__form-checkout" class="tf-search__form__label" style="visibility: hidden;">
								<?php echo esc_html_e('Select Date', 'tourfic'); ?>
							</label>
							<div class="tf-search__form__field">
								<div class="tf-search__form__field__icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
										<g clip-path="url(#clip0_2862_2140)">
											<path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
										</g>
										<defs>
											<clipPath id="clip0_2862_2140">
												<rect width="35" height="40" fill="white" transform="translate(0.777832)" />
											</clipPath>
										</defs>
									</svg>
								</div>
								<div class="tf_checkout_dates tf-flex tf-flex-align-center">
									<span class="date field--title"><?php echo esc_html(gmdate('d')); ?></span>
									<div class="tf-search__form__field__mthyr">
										<span class="month form--span"><?php echo esc_html(gmdate('M')); ?></span>
										<span class="year form--span"><?php echo esc_html(gmdate('Y')); ?></span>
									</div>
								</div>

							</div>
						</div>
					</div>
					<div class="tf-search__form__fieldset__right">
						<!-- Submit Button -->
						<input type="hidden" name="type" value="tf_tours" class="tf-post-type" />
						<button type="submit" class="tf-search__form__submit tf_btn">
							<?php echo esc_html(apply_filters("tf_tour_search_form_submit_button_text", 'Search')); ?>
							<svg class="tf-search__form__submit__icon" width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="white" />
							</svg>
						</button>
					</div>
				</fieldset>
			</form>
			<script>
				(function($) {
					$(document).ready(function() {
						// flatpickr locale first day of Week
						<?php Helper::tf_flatpickr_locale("root"); ?>

						$(".tf_tour_check_in_out_date").on("click", function() {
							$(".tf-tour-check-in-out-date").trigger("click");
						});
						$(".tf-tour-check-in-out-date").flatpickr({
							enableTime: false,
							mode: "range",
							dateFormat: "Y/m/d",
							minDate: "today",

							// flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

							onReady: function(selectedDates, dateStr, instance) {
								instance.element.value = dateStr.replace(/[a-z]+/g, '-');
								dateSetToFields(selectedDates, instance);
							},
							onChange: function(selectedDates, dateStr, instance) {
								instance.element.value = dateStr.replace(/[a-z]+/g, '-');
								dateSetToFields(selectedDates, instance);
							}
						});

						function dateSetToFields(selectedDates, instance) {
							if (selectedDates.length === 2) {
								const monthNames = [
									"Jan", "Feb", "Mar", "Apr", "May", "Jun",
									"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
								];
								if (selectedDates[0]) {
									const startDate = selectedDates[0];
									$(".tf_checkin_dates span.date").html(startDate.getDate());
									$(".tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
									$(".tf_checkin_dates span.year").html(startDate.getFullYear());
								}
								if (selectedDates[1]) {
									const endDate = selectedDates[1];
									$(".tf_checkout_dates span.date").html(endDate.getDate());
									$(".tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
									$(".tf_checkout_dates span.year").html(endDate.getFullYear());
								}
							}
						}
					});
				})(jQuery);
			</script>


		<?php } else { ?>
            <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
                <div class="tf_homepage-booking">
					<?php if ( Helper::tfopt( 'hide_tour_location_search' ) != 1 || Helper::tfopt( 'required_location_tour_search' ) ): ?>
                        <div class="tf_destination-wrap">
                            <div class="tf_input-inner">
                                <div class="tf_form-row">
                                    <label class="tf_label-row">
                                        <span class="tf-label"><?php esc_html_e( 'Destination', 'tourfic' ); ?>:</span>
                                        <div class="tf_form-inner">
                                            <div class="tf-search-form-field-icon">
                                                <i class="fas fa-search"></i>
                                            </div>
											<?php if ( ( empty( $advanced ) || ! empty( $advanced ) ) && "enabled" != $advanced ) { ?>
                                                <input type="text" name="place-name" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="tf-destination" class=""
                                                       placeholder="<?php esc_html_e( 'Enter Destination', 'tourfic' ); ?>" value="">
                                                <input type="hidden" name="place" id="tf-search-tour" class="tf-place-input"/>
											<?php }
											if ( ! empty( $advanced ) && "enabled" == $advanced ) { ?>
                                                <input type="text" name="place-name" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="tf-tour-location-adv" class="tf-tour-preview-place"
                                                       placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>">
                                                <input type="hidden" name="place" id="tf-tour-place">
                                                <div class="tf-hotel-results tf-tour-results">
                                                    <ul id="ui-id-2">
														<?php
														$tf_tour_destination = get_terms( array(
															'taxonomy'     => 'tour_destination',
															'orderby'      => 'title',
															'order'        => 'ASC',
															'hide_empty'   => false,
															'hierarchical' => 0,
														) );
														if ( $tf_tour_destination ) {
															foreach ( $tf_tour_destination as $term ) {
																if ( ! empty( $term->name ) ) {
																	?>
                                                                    <li data-name="<?php echo esc_attr( $term->name ); ?>" data-slug="<?php echo esc_attr( $term->slug ); ?>"><i
                                                                                class="fa fa-map-marker"></i><?php echo esc_attr( $term->name ); ?></li>
																	<?php
																}
															}
														}
														?>
                                                    </ul>
                                                </div>
											<?php } ?>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
					<?php endif; ?>

                    <div class="tf_selectperson-wrap">
                        <div class="tf_input-inner">
                        <span class="tf_person-icon tf-search-form-field-icon">
                            <i class="fas fa-user"></i>
                        </span>
                            <div class="adults-text"><?php esc_html_e( '1 Adults', 'tourfic' ); ?></div>
							<?php
							if ( empty( $disable_child_search ) ) {
								?>
                                <div class="person-sep"></div>
                                <div class="child-text"><?php esc_html_e( '0 Children', 'tourfic' ); ?></div>
							<?php }
							if ( empty( $disable_infant_search ) ) {
								?>
                                <div class="person-sep"></div>
                                <div class="infant-text"><?php esc_html_e( '0 Infant', 'tourfic' ); ?></div>
							<?php } ?>
                        </div>
                        <div class="tf_acrselection-wrap">
                            <div class="tf_acrselection-inner">
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="1" value="1">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
								<?php
								if ( empty( $disable_child_search ) ) {
									?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="children" id="children" min="0" value="0">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
								<?php }
								if ( empty( $disable_infant_search ) ) {
									?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="infant" id="infant" min="0" value="0">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="tf_selectdate-wrap">
                        <!-- @KK Merged two inputs into one  -->
                        <div class="tf_input-inner">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                           placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_tour_search' ) ? 'required' : ''; ?>>
                                </div>
                            </label>
                        </div>
                    </div>

					<?php if ( ! empty( $advanced ) && "enabled" == $advanced ) { ?>
                        <div class="tf_selectdate-wrap tf_more_info_selections">
                            <div class="tf_input-inner">
                                <label class="tf_label-row" style="width: 100%;">
                                    <span class="tf-label"><?php esc_html_e( 'More', 'tourfic' ); ?></span>
                                    <span style="text-decoration: none; display: block; cursor: pointer;"><?php esc_html_e( 'Filter', 'tourfic' ); ?>  <i class="fas fa-angle-down"></i></span>
                                </label>
                            </div>
                            <div class="tf-more-info">
                                <h3><?php esc_html_e( 'Filter Price', 'tourfic' ); ?></h3>
                                <div class="tf-filter-price-range">
                                    <div class="tf-tour-filter-range"></div>
                                </div>
                                <h3 style="margin-top: 20px"><?php esc_html_e( 'Tour Types', 'tourfic' ); ?></h3>
								<?php
								$tf_tour_type = get_terms( array(
									'taxonomy'     => 'tour_type',
									'orderby'      => 'title',
									'order'        => 'ASC',
									'hide_empty'   => true,
									'hierarchical' => 0,
								) );
								if ( $tf_tour_type ) : ?>
                                    <div class="tf-tour-types" style="overflow: hidden">
										<?php foreach ( $tf_tour_type as $term ) : ?>
                                            <div class="form-group form-check">
                                                <input type="checkbox" name="types[]" class="form-check-input" value="<?php echo esc_attr( $term->slug ); ?>" id="<?php echo esc_attr( $term->slug ); ?>">
                                                <label class="form-check-label" for="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                            </div>
										<?php endforeach; ?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf_submit-wrap">
                        <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
						<?php
						if ( $author ) { ?>
                            <input type="hidden" name="tf-author" value="<?php echo esc_attr( $author ); ?>" class="tf-post-type"/>
						<?php } ?>
                        <button class="tf_btn tf-submit" type="submit"><?php echo esc_html(apply_filters("tf_tour_search_form_submit_button_text", esc_html__('Search', 'tourfic' ))); ?></button>
                    </div>

                </div>

            </form>
            <script>
                (function ($) {
                    $(document).ready(function () {

						const regexMap = {
                            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                        };
                        const dateRegex = regexMap['<?php echo esc_attr($tour_date_format_for_users); ?>'];

                        // flatpickr first day of Week
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

                        $("#tf_tour_aval_check #check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            altInput: true,
                            dateFormat: "Y/m/d",
                            altFormat: '<?php echo esc_attr( $tour_date_format_for_users ); ?>',
                            minDate: "today",

                            // flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
								return `${date1} - ${date2}`;
								});
								instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
									return `${d1} - ${d2}`;
								});
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
									return `${date1} - ${date2}`;
								});
								instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
									return `${d1} - ${d2}`;
								});
                            },
                        });

                    });
                })(jQuery);
            </script>
			<?php
		}
	}

	/**
	 * Single Tour Booking Bar
	 *
	 * Single Tour Page
	 */
	static function tf_single_tour_booking_form( $post_id ) {

		// Value from URL
		// Adults
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		// children
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		// room
		$infant = ! empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';

		$meta      = get_post_meta( $post_id, 'tf_tours_opt', true );
		$tour_type = ! empty( $meta['type'] ) ? $meta['type'] : '';
		// Continuous custom availability
		$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : '';

		# Get Pricing
		$tour_price = new Tour_Price( $meta );
		// Date format for Users Oputput
		$tour_date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$tf_tour_single_book_now_text = isset($meta['single_tour_booking_form_button_text']) && ! empty( $meta['single_tour_booking_form_button_text'] ) ? stripslashes( sanitize_text_field( $meta['single_tour_booking_form_button_text'] ) ) : esc_html__( "Book Now", 'tourfic' );

		// Same Day Booking
		$disable_same_day = ! empty( $meta['disable_same_day'] ) ? $meta['disable_same_day'] : '';
		if ( $tour_type == 'fixed' ) {
			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_fixed_avail        = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_fixed_date         = unserialize( $tf_tour_fixed_avail );
				$departure_date             = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
				$return_date                = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
				$min_people                 = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
				$max_people                 = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
				$repeated_fixed_tour_switch = ! empty( $tf_tour_fixed_date['fixed_availability']["tf-repeat-months-switch"] ) ? $tf_tour_fixed_date['fixed_availability']["tf-repeat-months-switch"] : 0;
				$tour_repeat_months         = ! empty( $tf_tour_fixed_date['fixed_availability']['tf-repeat-months-checkbox'] ) ? $tf_tour_fixed_date['fixed_availability']['tf-repeat-months-checkbox'] : array();
			} else {
				$departure_date             = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
				$return_date                = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
				$min_people                 = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
				$max_people                 = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
				$repeated_fixed_tour_switch = ! empty( $meta['fixed_availability']["tf-repeat-months-switch"] ) ? $meta['fixed_availability']["tf-repeat-months-switch"] : 0;
				$tour_repeat_months         = $repeated_fixed_tour_switch && ! empty( $meta['fixed_availability']['tf-repeat-months-checkbox'] ) ? $meta['fixed_availability']['tf-repeat-months-checkbox'] : array();
			}

		} elseif ( $tour_type == 'continuous' ) {

			$disabled_day  = ! empty( $meta['disabled_day'] ) ? $meta['disabled_day'] : '';
			$disable_range = ! empty( $meta['disable_range'] ) ? $meta['disable_range'] : '';
			if ( ! empty( $disable_range ) && gettype( $disable_range ) == "string" ) {
				$disable_range_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $disable_range );
				$disable_range          = unserialize( $disable_range_unserial );

			}
			$disable_specific = ! empty( $meta['disable_specific'] ) ? $meta['disable_specific'] : '';
			$disable_specific = str_replace( ', ', '", "', $disable_specific );

			if ( $custom_avail == true ) {

				$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';

				if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
					$cont_custom_date_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $cont_custom_date );
					$cont_custom_date          = unserialize( $cont_custom_date_unserial );

				}

			}

		}

		$disable_adult_price    = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price    = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price   = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
		$pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$group_price            = ! empty( $meta['group_price'] ) ? $meta['group_price'] : false;
		$adult_price            = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
		$child_price            = ! empty( $meta['child_price'] ) ? $meta['child_price'] : false;
		$infant_price           = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
		$tour_extras            = isset( $meta['tour-extra'] ) ? $meta['tour-extra'] : null;
		$tf_hide_external_price = true;

		if ( ! empty( $tour_extras ) && gettype( $tour_extras ) == "string" ) {

			$tour_extras_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $tour_extras );
			$tour_extras          = unserialize( $tour_extras_unserial );

		}
		$times = [];
		if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {

			$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['cont_custom_date'] );
			$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );

			if ( ! empty( $tf_tour_unserial_custom_date ) ) {
				if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						return $times[] = [
							'date'  => $v['date'],
							'times' => array_map( function ( $v ) {
								return $v['time'];
							}, $v['allowed_time'] ?? [] )
						];
					}, $tf_tour_unserial_custom_date );
				}
			}

		} else {
			if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					if ( ! empty( $v['date'] ) ) {
						return $times[] = [
							'date'  => $v['date'],
							'times' => array_map( function ( $v ) {
								return $v['time'];
							}, $v['allowed_time'] ?? [] )
						];
					}
				}, $meta['cont_custom_date'] );
			}

		}
		if ( $tour_type == 'continuous' && $custom_avail == true ) {
			$pricing_rule = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : 'person';
		}
		if ( ! empty( $meta['allowed_time'] ) && gettype( $meta['allowed_time'] ) == "string" ) {

			$tf_tour_unserial_custom_time = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['allowed_time'] );
			$tf_tour_unserial_custom_time = unserialize( $tf_tour_unserial_custom_time );
			if ( ! empty( $tf_tour_unserial_custom_time ) ) {
				if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						return $v['time'];
					}, $tf_tour_unserial_custom_time ?? [] );
				}
			}
		} else {
			if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					return $v['time'];
				}, $meta['allowed_time'] ?? [] );
			}
		}
		// Single Template Check
		$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
		if ( "single" == $tf_tour_layout_conditions ) {
			$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
		}
		$tf_tour_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';

		$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;

		$tf_tour_selected_template = $tf_tour_selected_check;

		$tf_tour_global_book_now_text = ! empty( Helper::tfopt( 'tour_booking_form_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'tour_booking_form_button_text' ) ) ) : esc_html__( "Book Now", 'tourfic' );
		$tf_tour_single_book_now_text = isset($meta['single_tour_booking_form_button_text']) && ! empty( $meta['single_tour_booking_form_button_text'] ) ? stripslashes( sanitize_text_field( $meta['single_tour_booking_form_button_text'] ) ) : esc_html__( "Book Now", 'tourfic' );
		$tf_tour_book_now_text = isset($meta['single_tour_booking_form_button_text']) && !empty($tf_tour_single_book_now_text) ? $tf_tour_single_book_now_text : $tf_tour_global_book_now_text;

		$tf_booking_type = '1';
		$tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_ext_booking_type = $tf_hide_price = $tf_booking_code = '';
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
			$tf_ext_booking_type  = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
			$tf_booking_code      = ! empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
			$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
			$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
			$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
			$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
			$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
		}

		ob_start();
		if ( $tf_tour_selected_template == "design-1" ) {
			if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== 1 && $tf_ext_booking_type !== '2' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
                <form class="tf_tours_booking tf_tours_main_booking">

					<?php
					// if ( self::tf_booking_popup( $post_id ) ) {
					// 	echo wp_kses( self::tf_booking_popup( $post_id ), Helper::tf_custom_wp_kses_allow_tags() );
					// }
					?>
                    <div class="tf-field-group tf-mt-8">
                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                        <input type='text' name='check-in-out-date' id='check-in-out-date' class='tf-field tours-check-in-out' onkeypress="return false;" placeholder='<?php esc_html_e( "Select Date", "tourfic" ); ?>'
                               value='' required/>
                    </div>
					<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' &&  !empty( $allowed_times ) ) { ?>
                        <div class="tf-field-group check-in-time-div tf-mt-8" id="" style="display: none;">
                            <i class="fa-regular fa-clock"></i>
                            <select class="tf-field" name="check-in-time" id="" style="min-width: 100px;"></select>
                        </div>
					<?php } ?>

                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                    <div class="tf-booking-person tf-mt-30">
                        <div class="tf-form-title">
                            <p><?php esc_html_e( "Person Info", "tourfic" ); ?></p>
                        </div>
						<?php if ( $custom_avail == true ) {

							if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) :
								?>
                                <div class="tf-field-group tf-mt-16 tf_acrselection">
                                    <div class="tf-field tf-flex">
                                        <div class="acr-label tf-flex">
                                            <i class="fa-regular fa-user"></i>
											<?php esc_html_e( 'Adults', 'tourfic' ); ?>
                                        </div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>
							<?php endif; ?>

							<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="tf-field-group tf-mt-16 tf_acrselection">
                                    <div class="tf-field tf-flex">
                                        <div class="acr-label tf-flex">
                                            <i class="fa-solid fa-child"></i>
											<?php esc_html_e( 'Children', 'tourfic' ); ?>
                                        </div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>

							<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                <div class="tf-field-group tf-mt-16 tf_acrselection">
                                    <div class="tf-field tf-flex">
                                        <div class="acr-label tf-flex">
                                            <i class="fa-solid fa-baby"></i>
											<?php esc_html_e( 'Infant', 'tourfic' ); ?>
                                        </div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>

						<?php } else { ?>

							<?php if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) : ?>
                                <div class="tf-field-group tf-mt-16 tf_acrselection">
                                    <div class="tf-field tf-flex">
                                        <div class="acr-label tf-flex">
                                            <i class="fa-regular fa-user"></i>
											<?php esc_html_e( 'Adults', 'tourfic' ); ?>
                                        </div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>
							<?php endif; ?>

							<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="tf-field-group tf-mt-16 tf_acrselection">
                                    <div class="tf-field tf-flex">
                                        <div class="acr-label tf-flex">
                                            <i class="fa-solid fa-child"></i>
											<?php esc_html_e( 'Children', 'tourfic' ); ?>
                                        </div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>

							<?php } ?>

							<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                <div class="tf-field-group tf-mt-16 tf_acrselection">
                                    <div class="tf-field tf-flex">
                                        <div class="acr-label tf-flex">
                                            <i class="fa-solid fa-baby"></i>
											<?php esc_html_e( 'Infant', 'tourfic' ); ?>
                                        </div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>

						<?php }; ?>
						<?php
						?>
                    </div>

                    <div class="tf-tours-booking-btn tf-booking-bttns tf-mt-30">
						<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                            <div class="tf-btn-wrap">
                                <a href="#" class="tf_btn tf_btn_full tf-booking-popup-btn" type="submit"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                            </div>
						<?php endif; ?>
						<?php
						if ( self::tf_booking_popup( $post_id ) ) {
							echo wp_kses( self::tf_booking_popup( $post_id ), Helper::tf_custom_wp_kses_allow_tags() );
						}
						?>
                    </div>

                </form>


				<form class="tf_tours_booking tf_tours_bottom_booking">
                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                    <!-- bottom bar -->
                    <div class="tf-bottom-booking-bar">
                        <div class="tf-bottom-booking-fields">
							<?php if (
								( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) ||
								( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) ||
								( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) ||
								( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ||
								( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ||
								( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false )
							): ?>
                                <div class="tf_selectperson-wrap tf-bottom-booking-field">
                                    <div class="tf-bottom-booking-field-icon">
                                        <i class="ri-user-line"></i>
                                    </div>
                                    <div class="tf_input-inner">
										<?php if ( $custom_avail == true ) { ?>
											<?php if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                                <div class="adults-text"><?php echo ( ! empty( $adults ) ? esc_attr( $adults ) : '0' ) . ' ' . esc_html__( "Adults", "tourfic" ); ?></div>
											<?php } ?>

											<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
												<?php if ( ! $disable_adult_price && $adult_price != false ) : ?>
                                                    <div class="person-sep"></div>
												<?php endif; ?>
                                                <div class="child-text"><?php echo ( ! empty( $child ) ? esc_attr( $child ) : '0' ) . ' ' . esc_html__( "Children", "tourfic" ); ?></div>
											<?php } ?>

											<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
												<?php if ( ( ! $disable_adult_price && $adult_price != false ) || ( ! $disable_child_price && $child_price != false ) ) : ?>
                                                    <div class="person-sep"></div>
												<?php endif; ?>
                                                <div class="infant-text"><?php echo ( ! empty( $infant ) ? esc_attr( $infant ) : '0' ) . ' ' . esc_html__( "Infant", "tourfic" ); ?></div>
											<?php } ?>

										<?php } else { ?>

											<?php if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                                <div class="adults-text"><?php echo ( ! empty( $adults ) ? esc_attr( $adults ) : '0' ) . ' ' . esc_html__( "Adults", "tourfic" ); ?></div>
											<?php } ?>

											<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
												<?php if ( ! $disable_adult_price && $adult_price != false ) : ?>
                                                    <div class="person-sep"></div>
												<?php endif; ?>
                                                <div class="child-text"><?php echo ( ! empty( $child ) ? esc_attr( $child ) : '0' ) . ' ' . esc_html__( "Children", "tourfic" ); ?></div>
											<?php } ?>

											<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
												<?php if ( ( ! $disable_adult_price && $adult_price != false ) || ( ! $disable_child_price && $child_price != false ) ) : ?>
                                                    <div class="person-sep"></div>
												<?php endif; ?>
                                                <div class="infant-text"><?php echo ( ! empty( $infant ) ? esc_attr( $infant ) : '0' ) . ' ' . esc_html__( "Infant", "tourfic" ); ?></div>
											<?php } ?>

										<?php } ?>
                                    </div>
                                    <div class="tf_acrselection-wrap" style="display: none;">
                                        <div class="tf_acrselection-inner">
											<?php if ( $custom_avail == true ) { ?>
												<?php if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                                    <div class="tf_acrselection">
                                                        <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                                        <div class="acr-select">
                                                            <div class="acr-dec">-</div>
                                                            <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>">
                                                            <div class="acr-inc">+</div>
                                                        </div>
                                                    </div>
												<?php } ?>

												<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                                    <div class="tf_acrselection">
                                                        <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                        <div class="acr-select">
                                                            <div class="acr-dec">-</div>
                                                            <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                                            <div class="acr-inc">+</div>
                                                        </div>
                                                    </div>
												<?php } ?>

												<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                                    <div class="tf_acrselection">
                                                        <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                        <div class="acr-select">
                                                            <div class="acr-dec">-</div>
                                                            <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>">
                                                            <div class="acr-inc">+</div>
                                                        </div>
                                                    </div>
												<?php } ?>

											<?php } else { ?>

												<?php if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                                    <div class="tf_acrselection">
                                                        <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                                        <div class="acr-select">
                                                            <div class="acr-dec">-</div>
                                                            <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>">
                                                            <div class="acr-inc">+</div>
                                                        </div>
                                                    </div>
												<?php } ?>

												<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                                    <div class="tf_acrselection">
                                                        <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                        <div class="acr-select">
                                                            <div class="acr-dec">-</div>
                                                            <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                                            <div class="acr-inc">+</div>
                                                        </div>
                                                    </div>
												<?php } ?>

												<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                                    <div class="tf_acrselection">
                                                        <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                        <div class="acr-select">
                                                            <div class="acr-dec">-</div>
                                                            <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>">
                                                            <div class="acr-inc">+</div>
                                                        </div>
                                                    </div>
												<?php } ?>
											<?php } ?>
                                        </div>
                                    </div>
                                </div>
							<?php endif; ?>

                            <div class="tf-bottom-booking-field">
                                <div class="tf-bottom-booking-field-icon">
                                    <i class="ri-calendar-todo-line"></i>
                                </div>
                                <input type="text" class="tf-field tours-check-in-out" placeholder="<?php esc_html_e( "Select Date", "tourfic" ); ?>" value="" required/>
                            </div>

							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' && !empty($allowed_times)) { ?>
                                <div class="tf-bottom-booking-field check-in-time-div" id="" style="display: none;">
                                    <div class="tf-bottom-booking-field-icon">
                                        <i class="ri-time-line"></i>
                                    </div>
                                    <select class="tf-field" name="check-in-time" id=""></select>
                                </div>
							<?php } ?>
                        </div>

                        <div class="tf-tours-booking-btn tf-booking-bttns">
							<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                                <div class="tf-btn-wrap">
                                    <a href="#" class="tf_btn tf-booking-popup-btn" type="submit"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
									<a href="#" class="tf_btn tf-booking-mobile-btn"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
                </form>

			<?php endif; ?>
			<?php if ( $tf_booking_type == 2 && $tf_ext_booking_type == '2' && ! empty( $tf_booking_code ) ) :
				echo wp_kses( $tf_booking_code, Helper::tf_custom_wp_kses_allow_tags() );
			endif; ?>


		<?php } elseif ( $tf_tour_selected_template == "design-2" ) { ?>
			<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== 1 && $tf_ext_booking_type !== '2' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
                <form class="tf_tours_booking tf_tours_main_booking">

					<?php
					// if ( self::tf_booking_popup( $post_id ) ) {
					// 	echo wp_kses( self::tf_booking_popup( $post_id ), Helper::tf_custom_wp_kses_allow_tags() );
					// }
					?>
                    <div class="tf-field-group tf-mt-8 tf-field-calander">
                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                        <input type='text' name='check-in-out-date' id='check-in-out-date' class='tf-field tours-check-in-out' onkeypress="return false;" placeholder='<?php esc_html_e( "Select Date", "tourfic" ); ?>'
                               value='' required/>
                    </div>
					<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' && !empty($allowed_times)) { ?>
                        <div class="tf-field-group check-in-time-div tf-mt-8 tf-field-calander" id="" style="display: none;">
                            <i class="fa-regular fa-clock"></i>
                            <select class="tf-field" name="check-in-time" id="" style="min-width: 100px;"></select>
                        </div>
					<?php } ?>

                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                    <div class="tf-booking-person tf-tour-booking-box">
                        <div class="tf-form-title">
                            <h3 class="tf-person-info-title"><?php esc_html_e( "Person Info", "tourfic" ); ?></h3>
                        </div>
						<?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                            <div class="tf-field-group tf-mt-16 tf_acrselection">
                                <div class="tf-field tf-flex">
                                    <div class="acr-label tf-flex">
										<?php esc_html_e( 'Adults', 'tourfic' ); ?>
										<?php
										$tf_hide_external_price = ! empty( $meta["booking-by"] ) && $meta["booking-by"] == 2 ? ! $meta["hide_price"] : true;

										if ( $tf_hide_external_price ) : ?>
                                            <div class="acr-adult-price">
												<?php if ( $pricing_rule == 'person' && ( ! empty( $tour_price->wc_sale_adult ) || ! empty( $tour_price->wc_adult ) ) ) {
													echo ! empty( $tour_price->wc_sale_adult ) ? '<del>' . esc_html( wp_strip_all_tags( $tour_price->wc_adult ) ) . '</del>' . " " . wp_kses_post( $tour_price->wc_sale_adult ) : wp_kses_post( $tour_price->wc_adult );
												} ?>
                                            </div>
										<?php endif; ?>
                                    </div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php } ?>

						<?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                            <div class="tf-field-group tf-mt-16 tf_acrselection">
                                <div class="tf-field tf-flex">
                                    <div class="acr-label tf-flex">
										<?php esc_html_e( 'Children', 'tourfic' ); ?>
										<?php if ( $tf_hide_external_price ) : ?>
                                            <div class="acr-child-price">
												<?php if ( $pricing_rule == 'person' && ( ! empty( $tour_price->wc_sale_child ) || ! empty( $tour_price->wc_child ) ) ) {
													echo ! empty( $tour_price->wc_sale_child ) ? '<del>' . esc_html( wp_strip_all_tags( $tour_price->wc_child ) ) . '</del>' . " " . wp_kses_post( $tour_price->wc_sale_child ) : wp_kses_post( $tour_price->wc_child );
												} ?>
                                            </div>
										<?php endif; ?>
                                    </div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php } ?>
						<?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                            <div class="tf-field-group tf-mt-16 tf_acrselection">
                                <div class="tf-field tf-flex">
                                    <div class="acr-label tf-flex">
										<?php esc_html_e( 'Infant', 'tourfic' ); ?>
										<?php if ( $tf_hide_external_price ) : ?>
                                            <div class="acr-infant-price">
												<?php if ( $pricing_rule == 'person' && ( ! empty( $tour_price->wc_sale_infant ) || ! empty( $tour_price->wc_infant ) ) ) {
													echo ! empty( $tour_price->wc_sale_infant ) ? '<del>' . esc_html( wp_strip_all_tags( $tour_price->wc_infant ) ) . '</del>' . " " . wp_kses_post( $tour_price->wc_sale_infant ) : wp_kses_post( $tour_price->wc_infant );
												} ?>
                                            </div>
										<?php endif; ?>
                                    </div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php } ?>
                    </div>

                    <div class="tf-tours-booking-btn tf-booking-bttns">
						<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                            <div class="tf-btn-wrap">
                                <a href="#" class="tf_btn tf_btn_full tf_btn_sharp tf-booking-popup-btn" type="submit"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                            </div>
						<?php endif; ?>
						<?php
						if ( self::tf_booking_popup( $post_id ) ) {
							echo wp_kses( self::tf_booking_popup( $post_id ), Helper::tf_custom_wp_kses_allow_tags() );
						}
						?>
                    </div>

                    <!-- bottom bar -->
					<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                        <div class="tf-mobile-booking-btn">
							<span>
								<?php echo esc_html( $tf_tour_book_now_text ); ?>
							</span>
                        </div>
					<?php endif; ?>
                    <div class="tf-bottom-booking-bar">

                        <div class="tf-booking-form-fields">

                            <div class="tf-booking-form-checkinout">
                                <span class="tf-booking-form-title"><?php esc_html_e( "Select Date", "tourfic" ); ?></span>
                                <div class="tf-booking-date-wrap">
                                    <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                    <span class="tf-booking-month">
										<span><?php esc_html_e( "Month", "tourfic" ); ?></span>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
										<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
										</svg>
									</span>
                                </div>
                                <input type="text" class="tf-field tours-check-in-out" placeholder="<?php esc_html_e( "Select Date", "tourfic" ); ?>" value="" required/>

                            </div>
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' && !empty($allowed_times)) { ?>
                                <div class="tf-bottom-booking-field check-in-time-div" id="" style="display: none;">
                                    <select class="tf-field" name="check-in-time" id=""></select>
                                </div>
							<?php } ?>
                            <div class="tf-booking-form-guest-and-room">

                                <div class="tf-booking-form-guest-and-room-inner">
                                    <span class="tf-booking-form-title"><?php esc_html_e( "Guests", "tourfic" ); ?></span>
                                    <div class="tf-booking-guest-and-room-wrap">
									<span class="tf-guest tf-booking-date">
										<?php esc_html_e( "00", "tourfic" ); ?>
									</span>
                                        <span class="tf-booking-month">
										<span><?php esc_html_e( "Guest", "tourfic" ); ?></span>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
										<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
										</svg>
									</span>
                                    </div>
                                </div>

                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
										<?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
										<?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
										<?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-tours-booking-btn tf-booking-bttns">
							<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                                <div class="tf-btn-wrap">
                                    <a href="#" class="tf_btn tf_btn_sharp tf_btn_large tf-booking-popup-btn" type="submit"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                                    <a href="#" class="tf_btn tf_btn_sharp tf_btn_large tf-booking-mobile-btn"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>

                </form>

				<form class="tf_tours_booking tf_tours_bottom_booking">
                    
                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                    
					

                    <!-- bottom bar -->
					<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                        <div class="tf-mobile-booking-btn">
							<span>
								<?php echo esc_html( $tf_tour_book_now_text ); ?>
							</span>
                        </div>
					<?php endif; ?>
                    <div class="tf-bottom-booking-bar">

                        <div class="tf-booking-form-fields">

                            <div class="tf-booking-form-checkinout">
                                <span class="tf-booking-form-title"><?php esc_html_e( "Select Date", "tourfic" ); ?></span>
                                <div class="tf-booking-date-wrap">
                                    <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                    <span class="tf-booking-month">
										<span><?php esc_html_e( "Month", "tourfic" ); ?></span>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
										<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
										</svg>
									</span>
                                </div>
                                <input type="text" class="tf-field tours-check-in-out" placeholder="<?php esc_html_e( "Select Date", "tourfic" ); ?>" value="" required/>

                            </div>
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' && !empty($allowed_times)) { ?>
                                <div class="tf-bottom-booking-field check-in-time-div" id="" style="display: none;">
                                    <select class="tf-field" name="check-in-time" id=""></select>
                                </div>
							<?php } ?>
                            <div class="tf-booking-form-guest-and-room">

                                <div class="tf-booking-form-guest-and-room-inner">
                                    <span class="tf-booking-form-title"><?php esc_html_e( "Guests", "tourfic" ); ?></span>
                                    <div class="tf-booking-guest-and-room-wrap">
									<span class="tf-guest tf-booking-date">
										<?php esc_html_e( "00", "tourfic" ); ?>
									</span>
                                        <span class="tf-booking-month">
										<span><?php esc_html_e( "Guest", "tourfic" ); ?></span>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
										<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
										</svg>
									</span>
                                    </div>
                                </div>

                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
										<?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
										<?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
										<?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13094)">
                                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13094">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <input type="tel" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>" readonly>
                                                    <div class="acr-inc">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <g clip-path="url(#clip0_3229_13100)">
                                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3229_13100">
                                                                    <rect width="20" height="20" fill="white"></rect>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-tours-booking-btn tf-booking-bttns">
							<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                                <div class="tf-btn-wrap">
                                    <a href="#" class="tf_btn tf_btn_sharp tf_btn_large tf-booking-popup-btn" type="submit"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                                    <a href="#" class="tf_btn tf_btn_sharp tf_btn_large tf-booking-mobile-btn"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>

                </form>
			<?php endif; ?>

			<?php if ( $tf_booking_type == 2 && $tf_ext_booking_type == '2' && ! empty( $tf_booking_code ) ) :
				echo wp_kses( $tf_booking_code, Helper::tf_custom_wp_kses_allow_tags() );
			endif; ?>
			<?php if ( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ): ?>
                <div class="tf-btn-wrap">
                    <a href="<?php echo esc_url( $tf_booking_url ) ?>" target="_blank" class="tf_btn tf_btn_full tf_btn_sharp tf-tour-external-booking-button" style="margin-top: 10px;"><?php echo esc_html( $tf_tour_single_book_now_text); ?></a>
                </div>
				<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                    <div class="tf-mobile-booking-btn">
						<span>
							<a href="<?php echo esc_url( $tf_booking_url ) ?>" target="_blank" class="tf_btn tf_btn_full tf_btn_sharp tf-tour-external-booking-button"
                               style="margin-top: 10px;"><?php echo esc_html( $tf_tour_single_book_now_text); ?></a>
						</span>
                    </div>
				<?php endif; ?>
			<?php endif; ?>
		<?php } else { ?>
            <div class="tf-tour-booking-wrap">
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== 1 && $tf_ext_booking_type !== '2' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
                    <form class="tf_tours_booking">
                        <div class="tf_selectperson-wrap">
                            <div class="tf_input-inner">
							<span class="tf_person-icon">
								<i class="fas fa-user"></i>
							</span>
								<?php if ( $custom_avail == true ) { ?>

									<?php if ( ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                        <div class="adults-text"><?php echo ( ! empty( $adults ) ? esc_attr( $adults ) : '0' ) . ' ' . esc_html__( "Adults", "tourfic" ); ?></div>
									<?php } ?>

									<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                        <div class="person-sep"></div>
                                        <div class="child-text"><?php echo ( ! empty( $child ) ? esc_attr( $child ) : '0' ) . ' ' . esc_html__( "Children", "tourfic" ); ?></div>
									<?php } ?>

									<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                        <div class="person-sep"></div>
                                        <div class="infant-text"><?php echo ( ! empty( $infant ) ? esc_attr( $infant ) : '0' ) . ' ' . esc_html__( "Infant", "tourfic" ); ?></div>
									<?php } ?>

								<?php } else { ?>

									<?php if ( ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                        <div class="adults-text"><?php echo ( ! empty( $adults ) ? esc_attr( $adults ) : '0' ) . ' ' . esc_html__( "Adults", "tourfic" ); ?></div>
									<?php } ?>

									<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                        <div class="person-sep"></div>
                                        <div class="child-text"><?php echo ( ! empty( $child ) ? esc_attr( $child ) : '0' ) . ' ' . esc_html__( "Children", "tourfic" ); ?></div>
									<?php } ?>

									<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                        <div class="person-sep"></div>
                                        <div class="infant-text"><?php echo ( ! empty( $infant ) ? esc_attr( $infant ) : '0' ) . ' ' . esc_html__( "Infant", "tourfic" ); ?></div>
									<?php } ?>

								<?php }; ?>
                            </div>
                            <div class="tf_acrselection-wrap" style="display: none;">
                                <div class="tf_acrselection-inner">

									<?php if ( $custom_avail == true ) { ?>

										<?php if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>">
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
										<?php } ?>

										<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
										<?php } ?>

										<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>">
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
										<?php } ?>

									<?php } else { ?>

										<?php if ( ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '0'; ?>">
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
										<?php } ?>

										<?php if ( ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
										<?php } ?>

										<?php if ( ! $disable_adult_price && ( ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) ) { ?>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0'; ?>">
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
										<?php } ?>

									<?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class='tf_form-row'>
                            <label class='tf_label-row'>
                                <div class='tf_form-inner'>
                                    <input type='text' name='check-in-out-date' id='check-in-out-date' class='tours-check-in-out' onkeypress="return false;" placeholder='<?php esc_html_e( "Select Date", "tourfic" ); ?>'
                                           value=''
                                           required/>
                                </div>
                            </label>
                        </div>

						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' ) { ?>
                            <div class='tf_form-row check-in-time-div' id="" style="display: none;">
                                <label class='tf_label-row'>
                                    <div class='tf_form-inner'>
                                        <select name="check-in-time" id="" style="min-width: 100px;">
                                        </select>
                                    </div>
                                </label>
                            </div>
						<?php } ?>

                        <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                        <div class="tf-tours-booking-btn">
							<?php if ( ! empty( $tf_tour_book_now_text ) ) : ?>
                                <div class="tf-btn-wrap">
                                    <a href="#" class="tf_btn tf_btn_full tf-booking-popup-btn"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
                                </div>
							<?php endif; ?>
                        </div>
						<?php
						if ( self::tf_booking_popup( $post_id ) ) {
							echo wp_kses( self::tf_booking_popup( $post_id ), Helper::tf_custom_wp_kses_allow_tags() );
						}
						?>
                    </form>
				<?php endif; ?>
				<?php if ( $tf_booking_type == 2 && $tf_ext_booking_type == '2' && ! empty( $tf_booking_code ) ) :
					echo wp_kses( $tf_booking_code, Helper::tf_custom_wp_kses_allow_tags() );
				endif; ?>
				<div class="tf-booking-mobile-btn">
					<a href="#" class="tf_btn"><?php echo esc_html( $tf_tour_book_now_text ); ?></a>
				</div>
            </div>
		<?php }

		return ob_get_clean();
	}

    static function fixed_tour_start_date_changer( $date, $months ) {
	    if ( ( count( $months ) > 0 ) && ! empty( $date ) ) {
		    preg_match( '/(\d{4})\/(\d{2})\/(\d{2})/', $date, $matches );

		    foreach ( $months as $month ) {

			    if ( $month < gmdate( 'm' ) && $matches[1] < gmdate( 'Y' ) ) {
				    $year = $matches[1] + 1;

			    } else {
				    $year = $matches[1];
			    }


			    $day_selected      = gmdate( 'd', strtotime( $date ) );
			    $last_day_of_month = gmdate( 't', strtotime( gmdate( 'Y' ) . '-' . $month . '-01' ) );
			    $matches[2]        = $month;
			    $changed_date      = sprintf( "%s/%s/%s", $year, $matches[2], $matches[3] );

			    if ( ( $day_selected == "31" ) && ( $last_day_of_month != "31" ) ) {
				    $new_months[] = gmdate( 'Y/m/d', strtotime( $changed_date . ' -1 day' ) );
			    } else {
				    $new_months[] = $changed_date;
			    }
		    }
		    $new_months[] = $matches[0];

		    return $new_months;

	    } else {
		    return array();
	    }
    }

    static function tf_nearest_default_day( $dates ) {
	    if ( count( $dates ) > 0 ) {

		    $today              = time();
		    $nearestDate        = null;
		    $smallestDifference = null;

		    foreach ( $dates as $date ) {
			    $dateTime   = strtotime( $date );
			    $difference = abs( $today - $dateTime );

			    if ( $dateTime > $today ) {
				    if ( $smallestDifference === null || $difference < $smallestDifference ) {
					    $smallestDifference = $difference;
					    $nearestDate        = $date;
				    }
			    }
		    }

		    return $nearestDate;
	    }
    }

    static function partial_payment_tag_replacement( $text, $arr ) {
	    if ( ! empty( $arr ) ) {
		    $tag   = array_keys( $arr );
		    $value = array_values( $arr );
	    }

	    return str_replace( $tag, $value, $text );
    }

	static function tf_booking_popup( $post_id ) {
		?>
        <!-- Loader Image -->
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="Loader">
            </div>
        </div>
        <div class="tf-withoutpayment-booking-confirm">
            <div class="tf-confirm-popup">
                <div class="tf-booking-times">
						<span>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
							<path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
							<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
							</svg>
						</span>
                </div>
                <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/thank-you.gif" alt="Thank You">
                <div class="tf-tour-without-payment-booking-confirm-msg-container">
					<?php
					$booking_confirmation_msg = ! empty( Helper::tfopt( 'booking-confirmation-msg' ) ) ? Helper::tfopt( 'booking-confirmation-msg' ) : '<h2>Booked Successfully</h2>';
					echo wp_kses_post( $booking_confirmation_msg );
					?>
                </div>
            </div>
        </div>
        <div class="tf-withoutpayment-booking">
            <div class="tf-withoutpayment-popup">
                <div class="tf-booking-tabs">
                    <div class="tf-booking-tab-menu">
                        <ul>
							<?php
							$meta        = get_post_meta( $post_id, 'tf_tours_opt', true );
							$tour_extras = function_exists( 'is_tf_pro' ) && is_tf_pro() && isset( $meta['tour-extra'] ) ? $meta['tour-extra'] : null;
							if ( ! empty( $tour_extras ) && gettype( $tour_extras ) == "string" ) {

								$tour_extras_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
									return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
								}, $tour_extras );
								$tour_extras          = unserialize( $tour_extras_unserial );

							}
							$traveller_info_coll_global = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'disable_traveller_info' ) ) ? Helper::tfopt( 'disable_traveller_info' ) : '';

							$traveller_info_coll = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['tour-traveler-info'] ) ? $meta['tour-traveler-info'] : $traveller_info_coll_global;

							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_extras ) { ?>
                                <li class="tf-booking-step tf-booking-step-1 active">
                                    <i class="ri-price-tag-3-line"></i> <?php echo esc_html__( "Tour extra", "tourfic" ); ?>
                                </li>
							<?php }
							if ( $traveller_info_coll ) {
								?>
                                <li class="tf-booking-step tf-booking-step-2 <?php echo empty( $tour_extras ) ? esc_attr( 'active' ) : ''; ?> ">
                                    <i class="ri-group-line"></i> <?php echo esc_html__( "Traveler details", "tourfic" ); ?>
                                </li>
							<?php }
							$tf_booking_by = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $tf_booking_by ) {
								?>
                                <li class="tf-booking-step tf-booking-step-<?php echo empty( $traveller_info_coll ) ? esc_attr( "2" ) : esc_attr( "3" ); ?> <?php echo empty( $tour_extras ) && empty( $traveller_info_coll ) ? esc_attr( 'active' ) : ''; ?>">
                                    <i class="ri-calendar-check-line"></i> <?php echo esc_html__( "Booking Confirmation", "tourfic" ); ?>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                    <div class="tf-booking-times">
							<span>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
								<path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
								<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
								</svg>
							</span>
                    </div>
                </div>
                <div class="tf-booking-content-summery">

                    <!-- Popup Tour Extra -->
					<?php
					// $popup_extra_default_text = "Here we include our tour extra services. If you want take any of the service. Start and end in Edinburgh! With the In-depth Cultural";
					$tour_popup_extra_text = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'tour_popup_extras_text' ) ) ? Helper::tfopt( 'tour_popup_extras_text' ) : '';
					$traveler_details_text = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'tour_traveler_details_text' ) ) ? Helper::tfopt( 'tour_traveler_details_text' ) : '';
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_extras ) { ?>
                        <div class="tf-booking-content show tf-booking-content-1">
                            <p><?php echo esc_html( $tour_popup_extra_text ); ?></p>
                            <div class="tf-booking-content-extra">
								<?php
								if ( ( ! empty( $tour_extras[0]['title'] ) && ! empty( $tour_extras[0]['price'] ) ) || ! empty( $tour_extras[1]['title'] ) && ! empty( $tour_extras[1]['price'] ) ) {
									?>
									<?php foreach ( $tour_extras as $extrakey => $tour_extra ) {
										if ( ! empty( $tour_extra['title'] ) && ! empty( $tour_extra['price'] ) ) {
											$tour_extra_pricetype = ! empty( $tour_extra['price_type'] ) ? $tour_extra['price_type'] : 'fixed';
											?>
                                            <div class="tf-single-tour-extra tour-extra-single">
                                                <label for="extra<?php echo esc_attr( $extrakey ); ?>">
                                                    <div class="tf-extra-check-box">
                                                        <input type="checkbox" value="<?php echo esc_attr( $extrakey ); ?>" data-title="<?php echo esc_attr( $tour_extra['title'] ); ?>"
                                                               id="extra<?php echo esc_attr( $extrakey ); ?>" name="tf-tour-extra">
                                                        <span class="tf-checkmark"></span>
                                                    </div>
                                                    <div class="tf-extra-content">
                                                        <h5><?php echo esc_html( $tour_extra['title'] ); ?> <?php echo $tour_extra_pricetype == "fixed" ? esc_html( "(Fixed Price)" ) : ( $tour_extra_pricetype == "person" ? esc_html( "(Per Person Price)" ) : esc_html( "(Per unit Price)" ) ); ?>
                                                            <span><?php echo wp_kses_post( wc_price( $tour_extra['price'] ) ); ?></span></h5>
														<?php
														if ( ! empty( $tour_extra['desc'] ) ) { ?>
                                                            <p><?php echo esc_html( $tour_extra['desc'] ); ?></p>
														<?php } ?>

                                                    </div>
                                                </label>
												<?php if ( $tour_extra_pricetype == "quantity" ) : ?>
                                                    <div class="tf-field-group tf-mt-16 tf_quantity-acrselection">
                                                        <div class="tf-field quanity-acr-fields">

                                                            <div class="quanity-acr-label">
																<?php echo esc_html__( "Select Quantity", "tourfic" ); ?>
                                                            </div>

                                                            <div class="quanity-acr-select tf-flex">
                                                                <div class="quanity-acr-dec">-</div>
                                                                <input type="number" name="extra-quantity" id="extra-quantity" min="1" value="1">
                                                                <div class="quanity-acr-inc">+</div>
                                                            </div>

                                                        </div>

                                                    </div>
												<?php endif; ?>
                                            </div>
										<?php }
									} ?>
								<?php } ?>

                            </div>
                        </div>
					<?php }
					if ( $traveller_info_coll ) {
						?>

                        <!-- Popup Traveler Info -->
                        <div class="tf-booking-content tf-booking-content-2 <?php echo empty( $tour_extras ) ? esc_attr( 'show' ) : ''; ?>">
                            <p><?php echo esc_html( $traveler_details_text ); ?></p>
                            <div class="tf-booking-content-traveller">
                                <div class="tf-traveller-info-box"></div>
                            </div>
                        </div>
					<?php }
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $tf_booking_by ) {
						?>

                        <!-- Popup Booking Confirmation -->
                        <div class="tf-booking-content tf-booking-content-<?php echo empty( $traveller_info_coll ) ? esc_attr( "2" ) : esc_attr( "3" ); ?> <?php echo empty( $tour_extras ) && empty( $traveller_info_coll ) ? esc_attr( 'show' ) : ''; ?>">
                            <p><?php echo esc_html( $traveler_details_text ); ?></p>
                            <div class="tf-booking-content-traveller">
                                <div class="tf-single-tour-traveller">
                                    <h4><?php echo esc_html__( "Billing details", "tourfic" ); ?></h4>
                                    <div class="traveller-info billing-details">
										<?php
										$confirm_book_fields = ! empty( Helper::tfopt( 'book-confirm-field' ) ) ? Helper::tf_data_types( Helper::tfopt( 'book-confirm-field' ) ) : '';
										if ( empty( $confirm_book_fields ) ) {
											?>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_first_name"><?php echo esc_html__( "First Name", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_first_name]" id="tf_first_name" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_first_name"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_last_name"><?php echo esc_html__( "Last Name", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_last_name]" id="tf_last_name" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_last_name"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_email"><?php echo esc_html__( "Email", "tourfic" ); ?></label>
                                                <input type="email" name="booking_confirm[tf_email]" id="tf_email" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_email"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_phone"><?php echo esc_html__( "Phone", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_phone]" id="tf_phone" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_phone"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_country"><?php echo esc_html__( "Country", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_country]" id="tf_country" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_country"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_street_address"><?php echo esc_html__( "Street address", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_street_address]" id="tf_street_address" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_street_address"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_town_city"><?php echo esc_html__( "Town / City", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_town_city]" id="tf_town_city" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_town_city"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_state_country"><?php echo esc_html__( "State / County", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_state_country]" id="tf_state_country" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_state_country"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_postcode"><?php echo esc_html__( "Postcode / ZIP", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_postcode]" id="tf_postcode" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_postcode"></div>
                                            </div>
										<?php } else {
											foreach ( $confirm_book_fields as $field ) {
												if ( "text" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"><?php echo esc_html( $field['reg-field-label'] ); ?></label>
                                                        <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]"
                                                               id="<?php echo esc_attr( $field['reg-field-name'] ); ?>" data-required="<?php echo isset( $field['reg-field-required'] ) ? esc_attr( $field['reg-field-required'] ) : ''; ?>"/>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
												if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
															<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                        </label>
                                                        <select name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]" id="<?php echo esc_attr( $field['reg-field-name'] ); ?>"
                                                                data-required="<?php echo esc_attr( $field['reg-field-required'] ); ?>">
                                                            <option value="">
																<?php echo sprintf( esc_html__( 'Select One', 'tourfic' ) ); ?>
                                                            </option>
															<?php
															foreach ( $field['reg-options'] as $sfield ) {
																if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                    <option value="<?php echo esc_attr( $sfield['option-value'] ); ?>"><?php echo esc_html( $sfield['option-label'] ); ?></option>
																<?php }
															} ?>
                                                        </select>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
												if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
															<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                        </label>
														<?php
														foreach ( $field['reg-options'] as $sfield ) {
															if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                <div class="tf-single-checkbox">
                                                                    <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>][]"
                                                                           id="<?php echo esc_attr( $sfield['option-value'] ); ?>" value="<?php echo esc_html( $sfield['option-value'] ); ?>"
                                                                           data-required="<?php echo esc_attr( $field['reg-field-required'] ); ?>"/>
                                                                    <label for="<?php echo esc_attr( $sfield['option-value'] ); ?>">
																		<?php echo esc_html( $sfield['option-label'] ); ?>
                                                                    </label>
                                                                </div>
															<?php }
														} ?>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
											}
										} ?>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php } ?>

                    <!-- Popup Booking Summary -->
                    <div class="tf-booking-summery" style="<?php echo empty( $tour_extras ) && empty( $traveller_info_coll ) && 3 != $tf_booking_by ? esc_attr( "width: 100%;" ) : ''; ?>">
                        <div class="tf-booking-fixed-summery">
                            <h5><?php echo esc_html__( "Booking Summary", "tourfic" ); ?></h5>
                            <h4><?php echo esc_html( get_the_title( $post_id ) ); ?></h4>
                        </div>
                        <div class="tf-booking-traveller-info">

                        </div>
                    </div>
                </div>

                <!-- Popup Footer Control & Partial Payment -->
                <div class="tf-booking-pagination">
					<?php
					if ( ! empty( $meta['is_taxable'] ) ) { ?>
                        <div class="tf-tax-notice">
                            <span>"<?php esc_html_e( "Taxes will be calculated during checkout", "tourfic" ); ?>"</span>
                        </div>
					<?php } ?>
					<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['allow_deposit'] ) && $meta['allow_deposit'] == '1' && ! empty( $meta['deposit_amount'] ) && 3 != $tf_booking_by ) {
						$tf_deposit_amount              = array(
							"{amount}" => $meta['deposit_type'] == 'fixed' ? wp_kses_post( wc_price( $meta['deposit_amount'] ) ) : $meta['deposit_amount'] . '%'
						);
						$tf_partial_payment_label       = ! empty( Helper::tfopt( "deposit-title" ) ) ? Helper::tfopt( "deposit-title" ) : '';
						$tf_partial_payment_description = ! empty( Helper::tfopt( "deposit-subtitle" ) ) ? Helper::tfopt( "deposit-subtitle" ) : '';
						?>
                        <div class="tf-diposit-switcher">
                            <label class="switch">
                                <input type="checkbox" name="deposit" class="diposit-status-switcher">
                                <span class="switcher round"></span>
                            </label>

                            <div class="tooltip-box">
								<?php if ( ! empty( $tf_partial_payment_label ) ) { ?>
                                    <h4><?php echo wp_kses_post( self::partial_payment_tag_replacement( $tf_partial_payment_label, $tf_deposit_amount ) ) ?></h4>
								<?php }
								if ( ! empty( $tf_partial_payment_description ) ) { ?>
                                    <div class="tf-info-btn">
                                        <i class="fa fa-circle-exclamation tooltip-title-box" style="padding-left: 5px; padding-top: 5px" title=""></i>
                                        <div class="tf-tooltip"><?php echo wp_kses_post( $tf_partial_payment_description ) ?></div>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
					<?php } ?>
					<?php if ( empty( $tour_extras ) && 3 != $tf_booking_by && empty( $traveller_info_coll ) ) { ?>
                        <div class="tf-control-pagination show">
                            <button type="submit" class="tf_btn"><?php echo esc_html__( "Continue", "tourfic" ); ?></button>
                        </div>
						<?php
					}
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $tour_extras ) ) { ?>
                        <div class="tf-control-pagination show tf-pagination-content-1">
							<?php
							if ( 3 != $tf_booking_by && empty( $traveller_info_coll ) ) { ?>
                                <button type="submit" class="tf_btn"><?php echo esc_html__( "Continue", "tourfic" ); ?></button>
							<?php } else { ?>
                                <a href="#" class="tf-next-control tf-tabs-control tf_btn"
                                   data-step="2"><?php echo esc_html__( "Continue", "tourfic" ); ?></a>
							<?php } ?>
                        </div>
					<?php }
					if ( $traveller_info_coll ) { ?>

                        <!-- Popup Traveler Info -->
                        <div class="tf-control-pagination tf-pagination-content-2 <?php echo empty( $tour_extras ) ? esc_attr( 'show' ) : ''; ?>">
							<?php
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_extras ) { ?>
                                <a href="#" class="tf-back-control tf-step-back" data-step="1"><i class="fa fa-angle-left"></i><?php echo esc_html__( "Back", "tourfic" ); ?></a>
							<?php }
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $tf_booking_by ) {
								?>
                                <a href="#" class="tf-next-control tf-tabs-control tf_btn tf-traveller-error" data-step="3"><?php echo esc_html__( "Continue", "tourfic" ); ?></a>
							<?php } else { ?>
                                <button type="submit" class="tf-traveller-error tf_btn"><?php echo esc_html__( "Continue", "tourfic" ); ?></button>
							<?php } ?>
                        </div>
					<?php }
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $tf_booking_by ) {
						?>

                        <!-- Popup Booking Confirmation -->
                        <div class="tf-control-pagination tf-pagination-content-<?php echo empty( $traveller_info_coll ) ? esc_attr( "2" ) : esc_attr( "3" ); ?> <?php echo empty( $tour_extras ) && empty( $traveller_info_coll ) ? esc_attr( 'show' ) : ''; ?>">
							<?php
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $tour_extras || $traveller_info_coll ) ) { ?>
                                <a href="#" class="tf-back-control tf-step-back" data-step="<?php echo empty( $traveller_info_coll ) ? esc_attr( "1" ) : esc_attr( "2" ); ?>"><i class="fa fa-angle-left"></i><?php echo esc_html__( "Back", "tourfic" ); ?></a>
							<?php } ?>
                            <button type="submit" class="tf-book-confirm-error tf_btn"><?php echo esc_html__( "Continue", "tourfic" ); ?></button>
                        </div>
					<?php } ?>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Tours Archive
	 */
	static function tf_tour_archive_single_item( $adults = '', $child = '', $check_in_out = '', $startprice = '', $endprice = '', $settings = [] ) {

		// get post id
		$post_id = get_the_ID();
		//Get hotel meta values
		$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

		// Location
		if ( ! empty( $meta['location'] ) && Helper::tf_data_types( $meta['location'] ) ) {
			$location = ! empty( Helper::tf_data_types( $meta['location'] )['address'] ) ? Helper::tf_data_types( $meta['location'] )['address'] : '';
		}
		// Featured
		$featured            = ! empty( $meta['tour_as_featured'] ) ? $meta['tour_as_featured'] : '';
		$tours_multiple_tags = isset($meta['tf-tour-tags']) && is_array($meta['tf-tour-tags'])? Helper::tf_data_types($meta['tf-tour-tags']) : array();

		// Gallery Image
		$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : '';
		if ( $gallery ) {
			$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
		}

		// Informations
		$tour_duration      = ! empty( $meta['duration'] ) ? $meta['duration'] : '';
		$tour_duration_time = ! empty( $meta['duration_time'] ) ? $meta['duration_time'] : '';
		$group_size         = ! empty( $meta['group_size'] ) ? $meta['group_size'] : '';
		$features           = ! empty( $meta['features'] ) ? $meta['features'] : '';

		// Adults
		if ( empty( $adults ) ) {
			$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		}
		// children
		if ( empty( $child ) ) {
			$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		}
		// room
		$infant = ! empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
		// Check-in & out date
		if ( empty( $check_in_out ) ) {
			$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
		}

		$disable_adult_price              = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price              = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price             = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
		$pricing_rule                     = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$custom_pricing_by_rule           = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : '';
		$group_price                      = ! empty( $meta['group_price'] ) ? $meta['group_price'] : false;
		$adult_price                      = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
		$child_price                      = ! empty( $meta['child_price'] ) ? $meta['child_price'] : false;
		$infant_price                     = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
		$tour_archive_page_price_settings = ! empty( Helper::tfopt( 'tour_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'tour_archive_price_minimum_settings' ) : 'all';
		$meta_disable_review 			  = !empty($meta["t-review"]) ? $meta["t-review"] : 0;
		$tfopt_disable_review 			  = !empty(Helper::tfopt("t-review")) ? Helper::tfopt("t-review") : 0;
		$disable_review 				  = $tfopt_disable_review == 1 || $meta_disable_review == 1 ? true : $tfopt_disable_review;


		if ( ! empty( $check_in_out ) ) {
			list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
		}

		if ( ! empty( $check_in_out ) ) {
			$period = new \DatePeriod(
				new \DateTime( $tf_form_start ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
			);
		} else {
			$period = '';
		}


		// Single link
		$url = get_the_permalink();
		$url = add_query_arg( array(
			'adults'   => $adults,
			'children' => $child,
			'infant'   => $infant
		), $url );

		// Tour Starting Price
		$tour_price = [];
		if ( $pricing_rule && $pricing_rule == 'group' ) {
			if ( ! empty( $check_in_out ) ) {
				if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
					$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
					if ( $custom_availability ) {
						foreach ( $meta['cont_custom_date'] as $repval ) {
							//Initial matching date array
							$show_tour = [];
							$dates     = $repval['date'];
							// Check if any date range match with search form date range and set them on array
							if ( ! empty( $period ) ) {
								foreach ( $period as $date ) {
									$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
								}
							}
							if ( ! in_array( 0, $show_tour ) ) {
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
									if ( ! empty( $repval['group_price'] ) ) {
										$tour_price[] = $repval['group_price'];
									}
								}
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
									if ( $tour_archive_page_price_settings == "all" ) {
										if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['adult_price'];
										}
										if ( ! empty( $repval['child_price'] ) && ! $disable_child_price ) {
											$tour_price[] = $repval['child_price'];
										}
									}
									if ( $tour_archive_page_price_settings == "adult" ) {
										if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['adult_price'];
										}
									}
									if ( $tour_archive_page_price_settings == "child" ) {
										if ( ! empty( $repval['child_price'] ) && ! $disable_child_price ) {
											$tour_price[] = $repval['child_price'];
										}
									}
								}
							}
						}
					} else {
						if ( ! empty( $meta['group_price'] ) ) {
							$tour_price[] = $meta['group_price'];
						}
					}
				} else {
					if ( ! empty( $meta['group_price'] ) ) {
						$tour_price[] = $meta['group_price'];
					}
				}
			} else {
				if ( ! empty( $meta['group_price'] ) ) {
					$tour_price[] = $meta['group_price'];
				}
			}
		}
		if ( $pricing_rule && $pricing_rule == 'person' ) {
			if ( ! empty( $check_in_out ) ) {
				if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
					$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
					if ( $custom_availability ) {
						foreach ( $meta['cont_custom_date'] as $repval ) {
							//Initial matching date array
							$show_tour = [];
							$dates     = $repval['date'];
							// Check if any date range match with search form date range and set them on array
							if ( ! empty( $period ) ) {
								foreach ( $period as $date ) {
									$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
								}
							}
							if ( ! in_array( 0, $show_tour ) ) {
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
									if ( ! empty( $repval['group_price'] ) ) {
										$tour_price[] = $repval['group_price'];
									}
								}
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
									if ( $tour_archive_page_price_settings == "all" ) {
										if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['adult_price'];
										}
										if ( ! empty( $repval['child_price'] ) && ! $disable_child_price ) {
											$tour_price[] = $repval['child_price'];
										}
									}
									if ( $tour_archive_page_price_settings == "adult" ) {
										if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['adult_price'];
										}
									}
									if ( $tour_archive_page_price_settings == "child" ) {
										if ( ! empty( $repval['child_price'] ) && ! $disable_child_price ) {
											$tour_price[] = $repval['child_price'];
										}
									}
								}
							}
						}
					} else {
						if ( $tour_archive_page_price_settings == "all" ) {
							if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['adult_price'];
							}
							if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['child_price'];
							}
						}

						if ( $tour_archive_page_price_settings == "adult" ) {
							if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['adult_price'];
							}
						}

						if ( $tour_archive_page_price_settings == "child" ) {
							if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['child_price'];
							}
						}
					}
				} else {
					if ( $tour_archive_page_price_settings == "all" ) {
						if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['adult_price'];
						}
						if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['child_price'];
						}
					}

					if ( $tour_archive_page_price_settings == "adult" ) {
						if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['adult_price'];
						}
					}

					if ( $tour_archive_page_price_settings == "child" ) {
						if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['child_price'];
						}
					}
				}
			} else {
				if ( $tour_archive_page_price_settings == "all" ) {
					if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
						$tour_price[] = $meta['adult_price'];
					}
					if ( ! empty( $meta['child_price'] ) && ! $disable_child_price ) {
						$tour_price[] = $meta['child_price'];
					}
				}
				if ( $tour_archive_page_price_settings == "adult" ) {
					if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
						$tour_price[] = $meta['adult_price'];
					}
				}
				if ( $tour_archive_page_price_settings == "child" ) {
					if ( ! empty( $meta['child_price'] ) && ! $disable_child_price ) {
						$tour_price[] = $meta['child_price'];
					}
				}
			}
		}

		$design = !empty($settings['design_tours']) ? $settings['design_tours'] : '';
		$tf_tour_arc_selected_template = !empty($design) ? $design : (! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1');
		$tf_discount_type   = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$tf_discount_amount = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

		//elementor settings
		$show_image = isset($settings['show_image']) ? $settings['show_image'] : 'yes';
		$featured_badge = isset($settings['featured_badge']) ? $settings['featured_badge'] : 'yes';
		$discount_tag = isset($settings['discount_tag']) ? $settings['discount_tag'] : 'yes';
		$promotional_tags = isset($settings['promotional_tags']) ? $settings['promotional_tags'] : 'yes';
		$gallery_switch = isset($settings['gallery']) ? $settings['gallery'] : 'yes';
		$show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
		$title_length = isset($settings['title_length']) ? absint($settings['title_length']) : 55;
		$show_excerpt = isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
		$excerpt_length = isset($settings['excerpt_length']) ? absint($settings['excerpt_length']) : 100;
		$show_location = isset($settings['show_location']) ? $settings['show_location'] : 'yes';
		$location_length = isset($settings['location_length']) ? absint($settings['location_length']) : 120;
		$show_features = isset($settings['show_features']) ? $settings['show_features'] : 'yes';
		$features_count = isset($settings['features_count']) ? absint($settings['features_count']) : 4;
		$show_review = isset($settings['show_review']) ? $settings['show_review'] : 'yes';
		$show_price = isset($settings['show_price']) ? $settings['show_price'] : 'yes';
		$tour_infos = isset($settings['tour_infos']) ? $settings['tour_infos'] : 'yes';
		$show_view_details = isset($settings['show_view_details']) ? $settings['show_view_details'] : 'yes';
		$view_details_text = isset($settings['view_details_text']) ? sanitize_text_field($settings['view_details_text']) : esc_html__('View Details', 'tourfic');

		// Thumbnail
		$thumbnail_html = '';
		if ( !empty($settings) && $show_image == 'yes' ) {
			$settings[ 'image_size_customize' ] = [
				'id' => get_post_thumbnail_id(),
			];
			$settings['image_size_customize_size'] = $settings['image_size'];
			$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );
			
			if ( "" === $thumbnail_html && 'yes' === $settings['show_fallback_img'] && !empty( $settings['fallback_img']['url'] ) ) {
				$settings[ 'image_size_customize' ] = [
					'id' => $settings['fallback_img']['id'],
				];
				$settings['image_size_customize_size'] = $settings['image_size'];
				$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );
			} elseif("" === $thumbnail_html && 'yes' !== $settings['show_fallback_img']) {
				$thumbnail_html = '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
			}
		}

		//Location icon
		$location_icon_html = '<i class="fa-solid fa-location-dot"></i>';
		if(!empty($settings) && $show_location == 'yes'){
			$location_icon_migrated = isset($settings['__fa4_migrated']['location_icon']);
			$location_icon_is_new = empty($settings['location_icon_comp']);

			if ( $location_icon_is_new || $location_icon_migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['location_icon'], [ 'aria-hidden' => 'true' ] );
				$location_icon_html = ob_get_clean();
			} else{
				$location_icon_html = '<i class="' . esc_attr( $settings['location_icon_comp'] ) . '"></i>';
			}
		}

		//Featured badge
		$featured_badge_text = !empty( $meta['featured_text'] ) ? esc_html( $meta['featured_text'] ) : esc_html( "HOT DEAL" );

		if ( $tf_tour_arc_selected_template == "design-1" ) {
			?>
            <div class="tf-item-card tf-flex">
				<!-- Thumbnail -->
				<?php if($show_image == 'yes'): ?>
                <div class="tf-item-featured">
                    <div class="tf-tag-items">
                        <div class="tf-features-box tf-flex">
							<?php if ( $discount_tag == 'yes' && ! empty( $tf_discount_type ) && $tf_discount_type != "none" && ! empty( $tf_discount_amount ) ) { ?>
                                <div class="tf-discount"><?php echo $tf_discount_type == "percent" ? esc_attr( $tf_discount_amount ) . "%" : wp_kses_post( wc_price( $tf_discount_amount ) ); ?><?php esc_html_e( " Off", "tourfic" ); ?></div>
							<?php } ?>

							<?php if ( $featured_badge == 'yes' && $featured ): ?>
                                <div class="tf-feature"><?php echo esc_html( $featured_badge_text ); ?></div>
							<?php endif; ?>
                        </div>
						<?php
						if ( $promotional_tags == 'yes' && sizeof( $tours_multiple_tags ) > 0 ) {
							foreach ( $tours_multiple_tags as $tag ) {
								$tour_tag_name        = ! empty( $tag['tour-tag-title'] ) ? esc_html( $tag['tour-tag-title'] ) : '';
								$tag_background_color = ! empty( $tag["tour-tag-color-settings"]["background"] ) ? $tag["tour-tag-color-settings"]["background"] : "#003162";
								$tag_font_color       = ! empty( $tag["tour-tag-color-settings"]["font"] ) ? $tag["tour-tag-color-settings"]["font"] : "#fff";

								if ( ! empty( $tour_tag_name ) ) {
									?>
                                    <div class="tf-multiple-tag-item" style="color: <?php echo esc_attr( $tag_font_color ) ?>; background-color: <?php echo esc_attr( $tag_background_color ); ?>; ">
                                        <span class="tf-multiple-tag"><?php echo esc_html( $tour_tag_name ) ?></span>
                                    </div>
									<?php
								}
							}
						}
						?>
                    </div>
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( ! empty( $thumbnail_html ) ) {
							echo wp_kses_post( $thumbnail_html );
						} elseif ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
				<?php endif; ?>

                <div class="tf-item-details" style="<?php echo $show_image != 'yes' ? 'flex-basis: 100%;' : ''; ?>">
					<!-- Location -->
					<?php if ( $show_location == 'yes' && ! empty( $location ) ) : ?>
                        <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
							<?php echo wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                            <p><?php echo esc_html( Helper::tourfic_character_limit_callback( $location, $location_length ) ); ?></p>
                        </div>
					<?php endif; ?>

					<!-- Title -->
					<?php if( $show_title == 'yes' ): ?>
                    <div class="tf-title tf-mt-16">
                        <h2><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), $title_length ) ); ?></a></h2>
                    </div>
					<?php endif; ?>

					<!-- Review -->
					<?php if( $show_review == 'yes' && $disable_review != true ): ?>
						<?php TF_Review::tf_archive_single_rating('', $design); ?>
					<?php endif; ?>

					<!-- Excerpt -->
					<?php if($show_excerpt == 'yes') : ?>
                    <div class="tf-details tf-mt-16">
                        <p><?php echo wp_kses_post( substr( wp_strip_all_tags( get_the_content() ), 0, $excerpt_length ) . '...' ); ?></p>
                    </div>
					<?php endif; ?>
					
					<!-- Price & View Details -->
                    <div class="tf-post-footer tf-flex tf-flex-align-center tf-flex-space-bttn tf-mt-16">
						<!-- Price -->
						<?php if($show_price == 'yes') : ?>
                        <div class="tf-pricing">
							<?php
							//get the lowest price from all available room price
							$tf_tour_min_price      = ! empty( $tour_price ) ? min( $tour_price ) : 0;
							$tf_tour_full_price     = ! empty( $tour_price ) ? min( $tour_price ) : 0;
							$tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
							$tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : 0;
							if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {

								if ( $tf_tour_discount_type == "percent" ) {
									$tf_tour_min_discount = ( $tf_tour_min_price * $tf_tour_discount_price ) / 100;
									$tf_tour_min_price    = (int) $tf_tour_min_price - $tf_tour_min_discount;
								}
								if ( $tf_tour_discount_type == "fixed" ) {
									$tf_tour_min_discount = $tf_tour_discount_price;
									$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_discount_price;
								}
							}
							$lowest_price = wc_price( $tf_tour_min_price );
							echo esc_html__( "From ", "tourfic" ) . wp_kses_post( $lowest_price ) . " ";
							if ( ! empty( $tf_tour_min_discount ) ) {
								echo "<del>" . wp_kses_post( wc_price( $tf_tour_full_price ) ) . "</del>";
							}
							?>
                        </div>
						<?php endif; ?>
						
						<!-- View Details -->
						<?php if($show_view_details == 'yes') : ?>
                        <div class="tf-booking-bttns">
                            <a class="tf_btn tf_btn_lite" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $view_details_text ); ?></a>
                        </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
			<?php
		} elseif ( $tf_tour_arc_selected_template == "design-2" ) {
			$first_gallery_image = explode( ',', $gallery );
			?>
            <div class="tf-available-room">
				<!-- Thumbnail -->
				<?php if($show_image == 'yes'): ?>
                <div class="tf-available-room-gallery">
                    <div class="tf-room-gallery">
						<?php
						if ( ! empty( $thumbnail_html ) ) {
							echo wp_kses_post( $thumbnail_html );
						} elseif ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </div>
					<?php if ( $gallery_switch == 'yes' && ! empty( $gallery_ids ) ) { ?>
                        <div data-id="<?php echo esc_html( get_the_ID() ); ?>" data-type="tf_tours" class="tf-room-gallery tf-popup-buttons tf-hotel-room-popup"
                             style="<?php echo ! empty( $first_gallery_image[0] ) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.70) 0%, rgba(48, 40, 28, 0.70) 100%), url(' . esc_url( wp_get_attachment_image_url( $first_gallery_image[0] ) ) . '), lightgray 50% / cover no-repeat; background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="content">
                                    <path id="Rectangle 2111"
                                          d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Rectangle 2109"
                                          d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4"
                                          stroke-width="1.5" stroke-linejoin="round"></path>
                                    <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </div>
					<?php } ?>
                    <div class="tf-available-labels">
						<?php if ( $featured_badge == 'yes' && $featured ): ?>
                            <span class="tf-available-labels-featured"><?php echo esc_html( $featured_badge_text ); ?></span>
						<?php endif; ?>
						<?php
						if ( $promotional_tags == 'yes' && sizeof( $tours_multiple_tags ) > 0 ) {
							foreach ( $tours_multiple_tags as $tag ) {
								$tour_tag_name        = ! empty( $tag['tour-tag-title'] ) ? esc_html( $tag['tour-tag-title'] ) : '';
								$tag_background_color = ! empty( $tag["tour-tag-color-settings"]["background"] ) ? $tag["tour-tag-color-settings"]["background"] : "#003162";
								$tag_font_color       = ! empty( $tag["tour-tag-color-settings"]["font"] ) ? $tag["tour-tag-color-settings"]["font"] : "#fff";

								if ( ! empty( $tour_tag_name ) ) {
									echo '<span class="tf-multiple-tag" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . ' ">' . esc_html( $tour_tag_name ) . '</span>';
								}
							}
						}
						?>
                    </div>

                    <!-- Review -->
                    <?php if( $show_review == 'yes' && $disable_review != true ): ?>
						<div class="tf-available-ratings">
							<?php TF_Review::tf_archive_single_rating('', $design); ?>
							<i class="fa-solid fa-star"></i>
						</div>
                    <?php endif; ?>
                </div>
				<?php endif; ?>
                <div class="tf-available-room-content" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">
                    <div class="tf-available-room-content-left">
                        <div class="tf-card-heading-info">
							<!-- Title & Location -->
                            <div class="tf-section-title-and-location">
								<!-- Title -->
								<?php if( $show_title == 'yes' ): ?>
                                <h2 class="tf-section-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), $title_length ) ); ?></a></h2>
								<?php endif; ?>

								<!-- Location -->
								<?php if ( $show_location == 'yes' && ! empty( $location ) ) : ?>
                                    <div class="tf-title-location">
                                        <div class="location-icon"><?php echo wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?></div>
                                        <span><?php echo esc_html( Helper::tourfic_character_limit_callback( $location, $location_length ) ); ?></span>
                                    </div>
								<?php endif; ?>
                            </div>

							<!-- Mobile Price -->
                            <div class="tf-mobile tf-pricing-info">
								<?php if ( $discount_tag == 'yes' && ! empty( $tf_discount_type ) && $tf_discount_type != "none" && ! empty( $tf_discount_amount ) ) { ?>
                                    <div class="tf-available-room-off">
										<span>
											<?php echo $tf_discount_type == "percent" ? esc_html( $tf_discount_amount . "%" ) : wp_kses_post( wc_price( $tf_discount_amount ) ); ?><?php esc_html_e( " Off", "tourfic" ); ?>
										</span>
                                    </div>
								<?php } ?>

								<?php if($show_price == 'yes') : ?>
                                <div class="tf-available-room-price">
									<span class="tf-price-from">
									<?php
									//get the lowest price from all available room price
									$tf_tour_min_price      = ! empty( $tour_price ) ? min( $tour_price ) : 0;
									$tf_tour_full_price     = ! empty( $tour_price ) ? min( $tour_price ) : 0;
									$tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
									$tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : 0;
									if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {

										if ( $tf_tour_discount_type == "percent" ) {
											$tf_tour_min_discount = ( $tf_tour_min_price * $tf_tour_discount_price ) / 100;
											$tf_tour_min_price    = (int) $tf_tour_min_price - $tf_tour_min_discount;
										}
										if ( $tf_tour_discount_type == "fixed" ) {
											$tf_tour_min_discount = $tf_tour_discount_price;
											$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_discount_price;
										}
									}
									$lowest_price = wc_price( $tf_tour_min_price );
									echo esc_html__( "From ", "tourfic" ) . wp_kses_post( $lowest_price ) . " ";
									if ( ! empty( $tf_tour_min_discount ) ) {
										echo "<del>" . wp_kses_post( wc_price( $tf_tour_full_price ) ) . "</del>";
									}
									?>
									</span>
                                </div>
								<?php endif; ?>
                            </div>
                        </div>
                        <ul>
							<?php if($tour_infos == 'yes') : ?>
							<?php if ( ! empty( $group_size ) ) { ?>
                                <li>
                                    <i class="ri-team-line"></i> <?php esc_html_e( "Max", "tourfic" ); ?> <?php echo esc_html( $group_size ); ?> <?php esc_html_e( "people", "tourfic" ); ?>
                                </li>
							<?php }
							if ( ! empty( $tour_duration ) ) { ?>
								<?php
									$tour_duration_time = $tour_duration > 1 ? $tour_duration_time . 's' : $tour_duration_time;
								?>
                                <li>
                                    <i class="ri-history-fill"></i> <?php echo esc_html( $tour_duration ); ?> <?php echo esc_html( $tour_duration_time ); ?>
                                </li>
							<?php } ?>
							<?php endif; ?>

							<!-- Features -->
							<?php
							if ( $show_features == 'yes' && $features ) {
								foreach ( $features as $tfkey => $feature ) {
									$feature_meta = get_term_meta( $feature, 'tour_features', true );
									if ( ! empty( $feature_meta ) ) {
										$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
									}
									if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
										$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
									} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
										$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
									}

									$features_details = get_term( $feature );
									if ( $tfkey < $features_count ) {
										?>
                                        <li>
											<?php
											if ( ! empty( $feature_icon ) ) {
												echo wp_kses_post( $feature_icon );
											} ?>
											<?php echo ! empty( $features_details->name ) ? esc_html( $features_details->name ) : ''; ?>
                                        </li>
									<?php }
								}
							} ?>
                        </ul>

                    </div>
                    <div class="tf-available-room-content-right">
                        <div class="tf-card-pricing-heading">
							<?php if ( $discount_tag == 'yes' && ! empty( $tf_discount_type ) && $tf_discount_type != "none" && ! empty( $tf_discount_amount ) ) { ?>
                                <div class="tf-available-room-off">
									<span>
										<?php echo $tf_discount_type == "percent" ? esc_attr( $tf_discount_amount ) . "%" : wp_kses_post( wc_price( $tf_discount_amount ) ); ?><?php esc_html_e( " Off", "tourfic" ); ?>
									</span>
                                </div>
							<?php } ?>

							<!-- Price -->
							<?php if($show_price == 'yes') : ?>
                            <div class="tf-available-room-price">
								<span class="tf-price-from">
								<?php
								//get the lowest price from all available room price
								$tf_tour_min_price      = ! empty( $tour_price ) ? min( $tour_price ) : 0;
								$tf_tour_full_price     = ! empty( $tour_price ) ? min( $tour_price ) : 0;
								$tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
								$tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : 0;
								if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {

									if ( $tf_tour_discount_type == "percent" ) {
										$tf_tour_min_discount = ( $tf_tour_min_price * $tf_tour_discount_price ) / 100;
										$tf_tour_min_price    = (int) $tf_tour_min_price - $tf_tour_min_discount;
									}
									if ( $tf_tour_discount_type == "fixed" ) {
										$tf_tour_min_discount = $tf_tour_discount_price;
										$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_discount_price;
									}
								}
								$lowest_price = wc_price( $tf_tour_min_price );

								if ( ! empty( $tf_tour_min_discount ) ) {
									echo esc_html__( "From ", "tourfic" ) . " " . "<del>" . wp_kses_post( wp_strip_all_tags( wc_price( $tf_tour_full_price ) ) ) . "</del>" . " " . wp_kses_post( $lowest_price );
								} else {
									echo esc_html__( "From ", "tourfic" ) . wp_kses_post( $lowest_price ) . " ";
								}
								?>
								</span>
                            </div>
							<?php endif; ?>
                        </div>

						<!-- View Details -->
						<?php if($show_view_details == 'yes') : ?>
                        <a href="<?php echo esc_url( $url ); ?>" class="tf_btn tf_btn_large tf_btn_sharp"><?php echo esc_html( $view_details_text ); ?></a>
						<?php endif; ?>
					</div>
                </div>
            </div>
			<?php
		} elseif ( $tf_tour_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) {
			$first_gallery_image = explode( ',', $gallery );
			?>
            <div class="tf-archive-hotel" data-id="<?php echo esc_attr(get_the_ID()); ?>">
				<!-- Thumbnail -->
				<?php if($show_image == 'yes'): ?>
                <div class="tf-archive-hotel-thumb">
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( ! empty( $thumbnail_html ) ) {
							echo wp_kses_post( $thumbnail_html );
						} elseif ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>

					<div class="tf-tag-items">
						<!-- Discount -->
						<?php if ( $discount_tag == 'yes' && ! empty( $tf_discount_type ) && $tf_discount_type != "none" && ! empty( $tf_discount_amount ) ) {?>
							<div class="tf-tag-item tf-tag-item-discount">
								<?php echo $tf_discount_type == "percent" ? esc_attr( $tf_discount_amount ) . "%" : wp_kses_post( wc_price( $tf_discount_amount ) ); ?><?php esc_html_e( " Off", "tourfic" ); ?>
							</div>
						<?php } ?>
						<?php if ( $featured_badge == 'yes' && $featured ): ?>
							<div class="tf-tag-item tf-tag-item-featured"><?php echo esc_html( $featured_badge_text ); ?></div>
						<?php endif; ?>
						<?php
						if ( $promotional_tags == 'yes' && sizeof( $tours_multiple_tags ) > 0 ) {
							foreach ( $tours_multiple_tags as $tag ) {
								$tour_tag_name        = ! empty( $tag['tour-tag-title'] ) ? esc_html( $tag['tour-tag-title'] ) : '';
								$tag_background_color = ! empty( $tag["tour-tag-color-settings"]["background"] ) ? $tag["tour-tag-color-settings"]["background"] : "#003162";
								$tag_font_color       = ! empty( $tag["tour-tag-color-settings"]["font"] ) ? $tag["tour-tag-color-settings"]["font"] : "#fff";

								if ( ! empty( $tour_tag_name ) ) {
									echo '<span class="tf-tag-item tf-multiple-tag" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . ' ">' . esc_html( $tour_tag_name ) . '</span>';
								}
							}
						}
						?>
					</div>
                </div>
				<?php endif; ?>

                <div class="tf-archive-hotel-content" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">
                    <div class="tf-archive-hotel-content-left">
						<!-- Location -->
						<?php if ( $show_location == 'yes' && ! empty( $location ) ) : ?>
                            <div class="tf-title-location">
								<div class="location-icon">
									<?php echo wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                </div>
                                <span><?php echo wp_kses_post(Helper::tourfic_character_limit_callback( esc_html( $location ), $location_length )); ?></span>
                            </div>
						<?php endif; ?>
						
						<!-- Title -->
						<?php if( $show_title == 'yes' ): ?>
                        <h4 class="tf-section-title">
                            <a href="<?php echo esc_url( $url ); ?>">
								<?php echo wp_kses_post(Helper::tourfic_character_limit_callback( get_the_title(), $title_length )); ?>
                            </a>
                        </h4>
						<?php endif; ?>
						
                        <ul class="features">
							<?php if($tour_infos == 'yes') : ?>
                            <?php if ( ! empty( $group_size ) ) { ?>
                                <li>
                                    <i class="ri-team-line"></i> <?php esc_html_e( "Max", "tourfic" ); ?> <?php echo esc_html( $group_size ); ?> <?php esc_html_e( "people", "tourfic" ); ?>
                                </li>
                            <?php }
                            if ( ! empty( $tour_duration ) ) { ?>
                                <li>
                                    <?php $tour_duration_time = $tour_duration > 1 ? $tour_duration_time . 's' : $tour_duration_time; ?>
                                    <i class="ri-history-fill"></i> <?php echo esc_html( $tour_duration ); ?> <?php echo esc_html( $tour_duration_time ); ?>
                                </li>
                            <?php } ?>
							<?php endif; ?>

                            <?php
                            if ( $show_features == 'yes' && $features ) {
                                foreach ( $features as $tfkey => $feature ) {
                                    $feature_meta = get_term_meta( $feature, 'tour_features', true );
                                    if ( ! empty( $feature_meta ) ) {
                                        $f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                                    }
                                    if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
                                        $feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
                                    } elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
                                        $feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
                                    }

                                    $features_details = get_term( $feature );
                                    if ( $tfkey < $features_count ) {
                                        ?>
                                        <li>
                                            <?php
                                            if ( ! empty( $feature_icon ) ) {
                                                echo wp_kses_post( $feature_icon );
                                            } ?>
                                            <?php echo ! empty( $features_details->name ) ? esc_html( $features_details->name ) : ''; ?>
                                        </li>
                                    <?php }
                                }
                            } ?>
                        </ul>
						
						<!-- Review -->
						<?php if( $show_review == 'yes' && $disable_review != true ): ?>
							<?php TF_Review::tf_archive_single_rating('', $design); ?>
						<?php endif; ?>
                    </div>
                    <div class="tf-archive-hotel-content-right">
						<!-- Price -->
						<?php if($show_price == 'yes') : ?>
                        <div class="tf-archive-hotel-price">
							<?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html()); ?>
                        </div>
						<?php endif; ?>
						
						<!-- View Details -->
						<?php if($show_view_details == 'yes') : ?>
                        <a href="<?php echo esc_url( $url ); ?>" class="tf_btn tf_btn_gray tf_btn_small"><?php echo esc_html( $view_details_text ); ?></a>
						<?php endif; ?>
					</div>
                </div>
            </div>
			<?php
		} else {
			?>
            <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
                <div class="single-tour-inner">
					<?php if ( $show_image == 'yes' && $featured_badge == 'yes' && $featured ): ?>
                        <div class="tf-featured-badge">
                            <span><?php echo esc_html( $featured_badge_text ); ?></span>
                        </div>
					<?php endif; ?>

					<!-- Thumbnail -->
					<?php if($show_image == 'yes'): ?>
                    <div class="tourfic-single-left">
                        <div class="default-tags-container">
							<?php
							if ( $promotional_tags == 'yes' && sizeof( $tours_multiple_tags ) > 0 ) {
								foreach ( $tours_multiple_tags as $tag ) {
									$hotel_tag_name       = ! empty( $tag['tour-tag-title'] ) ? esc_html( $tag['tour-tag-title'] ) : '';
									$tag_background_color = ! empty( $tag["tour-tag-color-settings"]["background"] ) ? $tag["tour-tag-color-settings"]["background"] : "#003162";
									$tag_font_color       = ! empty( $tag["tour-tag-color-settings"]["font"] ) ? $tag["tour-tag-color-settings"]["font"] : "#fff";

									if ( ! empty( $hotel_tag_name ) ) {
										echo '<span class="default-single-tag" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . '">' . esc_html( $hotel_tag_name ) . '</span>';
									}
								}
							}
							?>
                        </div>
                        <a href="<?php echo esc_url( $url ); ?>">
							<?php
							if ( ! empty( $thumbnail_html ) ) {
								echo wp_kses_post( $thumbnail_html );
							} elseif ( has_post_thumbnail() ) {
								the_post_thumbnail( 'full' );
							} else {
								echo '<img width="100%" height="100%" src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
							}
							?>
                        </a>
                    </div>
					<?php endif; ?>
					
                    <div class="tourfic-single-right">
                        <div class="tf_property_block_main_row">
                            <div class="tf_item_main_block">
								<!-- Title -->
								<?php if( $show_title == 'yes' ): ?>
                                <div class="tf-hotel__title-wrap tf-tours-title-wrap">
									<h3 class="tourfic_hotel-title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), $title_length ) ); ?></a></h3>
                                </div>
								<?php endif; ?>

								<!-- Location -->
								<?php
								if ( $show_location == 'yes' && $location ) {
									echo '<div class="tf-map-link">';
									echo '<span class="tf-d-ib">' . wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ) . wp_kses_post(Helper::tourfic_character_limit_callback( esc_html( $location ), $location_length )) . '</span>';
									echo '</div>';
								}
								?>
                            </div>
							
							<!-- Reivew -->
							<?php if($show_review == 'yes' && $disable_review != true ): ?>
								<?php TF_Review::tf_archive_single_rating('', $design); ?>
							<?php endif; ?>
                        </div>
						
						<!-- Excerpt -->
						<?php if($show_excerpt == 'yes') : ?>
                        <div class="tf-tour-desc">
                            <p><?php echo wp_kses_post( substr( wp_strip_all_tags( get_the_content() ), 0, $excerpt_length ) . '...' ); ?></p>
                        </div>
						<?php endif; ?>

						<!-- View Details -->
						<?php if($show_view_details == 'yes') : ?>
                        <div class="availability-btn-area tour-search">
                            <a href="<?php echo esc_url( $url ); ?>" class="tf_btn"><?php echo esc_html( $view_details_text ); ?></a>
                        </div>
						<?php endif; ?>

						<?php
						$tour_price = [];
						if ( $pricing_rule && $pricing_rule == 'group' ) {
							if ( ! empty( $check_in_out ) ) {
								if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
									$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
									if ( $custom_availability ) {
										foreach ( $meta['cont_custom_date'] as $repval ) {
											//Initial matching date array
											$show_tour = [];
											$dates     = $repval['date'];
											// Check if any date range match with search form date range and set them on array
											if ( ! empty( $period ) ) {
												foreach ( $period as $date ) {
													$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
												}
											}
											if ( ! in_array( 0, $show_tour ) ) {
												if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
													if ( ! empty( $repval['group_price'] ) ) {
														$tour_price[] = $repval['group_price'];
													}
												}
												if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
													if ( $tour_archive_page_price_settings == "all" ) {
														if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
															$tour_price[] = $repval['adult_price'];
														}
														if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
															$tour_price[] = $repval['child_price'];
														}
													}
													if ( $tour_archive_page_price_settings == "adult" ) {
														if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
															$tour_price[] = $repval['adult_price'];
														}
													}
													if ( $tour_archive_page_price_settings == "child" ) {
														if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
															$tour_price[] = $repval['child_price'];
														}
													}
												}
											}
										}
									} else {
										if ( ! empty( $meta['group_price'] ) ) {
											$tour_price[] = $meta['group_price'];
										}
									}
								} else {
									if ( ! empty( $meta['group_price'] ) ) {
										$tour_price[] = $meta['group_price'];
									}
								}
							} else {
								if ( ! empty( $meta['group_price'] ) ) {
									$tour_price[] = $meta['group_price'];
								}
							}
						}
						if ( $pricing_rule && $pricing_rule == 'person' ) {
							if ( ! empty( $check_in_out ) ) {
								if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
									$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
									if ( $custom_availability ) {
										foreach ( $meta['cont_custom_date'] as $repval ) {
											//Initial matching date array
											$show_tour = [];
											$dates     = $repval['date'];
											// Check if any date range match with search form date range and set them on array
											if ( ! empty( $period ) ) {
												foreach ( $period as $date ) {
													$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
												}
											}
											if ( ! in_array( 0, $show_tour ) ) {
												if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
													if ( ! empty( $repval['group_price'] ) ) {
														$tour_price[] = $repval['group_price'];
													}
												}
												if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
													if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
														if ( $tour_archive_page_price_settings == "all" ) {
															if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
																$tour_price[] = $repval['adult_price'];
															}
															if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
																$tour_price[] = $repval['child_price'];
															}
														}
														if ( $tour_archive_page_price_settings == "adult" ) {
															if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
																$tour_price[] = $repval['adult_price'];
															}
														}
														if ( $tour_archive_page_price_settings == "child" ) {
															if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
																$tour_price[] = $repval['child_price'];
															}
														}
													}
												}
											}
										}
									} else {
										if ( $tour_archive_page_price_settings == "all" ) {
											if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $meta['adult_price'];
											}
											if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $meta['child_price'];
											}
										}

										if ( $tour_archive_page_price_settings == "adult" ) {
											if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $meta['adult_price'];
											}
										}

										if ( $tour_archive_page_price_settings == "child" ) {
											if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $meta['child_price'];
											}
										}
									}
								} else {
									if ( $tour_archive_page_price_settings == "all" ) {
										if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $meta['adult_price'];
										}
										if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $meta['child_price'];
										}
									}

									if ( $tour_archive_page_price_settings == "adult" ) {
										if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $meta['adult_price'];
										}
									}

									if ( $tour_archive_page_price_settings == "child" ) {
										if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $meta['child_price'];
										}
									}
								}
							} else {
								if ( $tour_archive_page_price_settings == "all" ) {
									if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
										$tour_price[] = $meta['adult_price'];
									}
									if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
										$tour_price[] = $meta['child_price'];
									}
								}
								if ( $tour_archive_page_price_settings == "adult" ) {
									if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
										$tour_price[] = $meta['adult_price'];
									}
								}
								if ( $tour_archive_page_price_settings == "child" ) {
									if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
										$tour_price[] = $meta['child_price'];
									}
								}
							}
						}

						$hide_price = Helper::tfopt( 't-hide-start-price' );
						if ( isset( $hide_price ) && $hide_price !== '1' && ! empty( $tour_price ) && $show_price == 'yes') :
							?>
                            <div class="tf-tour-price">
								<?php

								//get the lowest price from all available room price
								$tf_tour_min_price      = min( $tour_price );
								$tf_tour_full_price     = min( $tour_price );
								$tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
								$tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';
								if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {
									if ( $tf_tour_discount_type == "percent" ) {
										$tf_tour_min_discount = ( $tf_tour_min_price * $tf_tour_discount_price ) / 100;
										$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_min_discount;
									}
									if ( $tf_tour_discount_type == "fixed" ) {
										$tf_tour_min_discount = $tf_tour_discount_price;
										$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_discount_price;
									}
								}
								$lowest_price = wc_price( $tf_tour_min_price );
								echo esc_html__( "From ", "tourfic" ) . wp_kses_post( $lowest_price );
								if ( ! empty( $tf_tour_min_discount ) ) {
									echo "<del>" . wp_kses_post( wc_price( $tf_tour_full_price ) ) . "</del>";
								}
								?>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
			<?php
		}
	}

	/**
	 * Filter tours on search result page by checkin checkout dates set by backend
	 *
	 *
	 * @param \DatePeriod $period collection of dates by user input;
	 * @param array $not_found collection of tour exists
	 * @param array $data user input for sidebar form
	 *
	 * @author devkabir, fida
	 *
	 */
	static function tf_filter_tour_by_date( $period, &$total_posts, array &$not_found, array $data = [] ): void {
		if ( isset( $data[3] ) && isset( $data[4] ) ) {
			[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $check_in_out ] = $data;
		}
		// Get tour meta options
		$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

		// Set initial tour availability status
		$has_tour = false;

		$tf_searching_period = [];
		foreach ( $period as $date ) {
			$tf_searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
		}

		// Total People
		$total_people = intval( $adults ) + intval( $child );

		if ( ! empty( $meta['type'] ) && $meta['type'] === 'fixed' ) {

			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_unserial_fixed_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_unserial_fixed_date = unserialize( $tf_tour_unserial_fixed_date );
				$fixed_availability          = ! empty( $tf_tour_unserial_fixed_date ) ? $tf_tour_unserial_fixed_date['date'] : [];
			} else {
				$fixed_availability          = ! empty( $meta['fixed_availability'] ) ? $meta['fixed_availability']['date'] : [];
				$tf_tour_unserial_fixed_date = $meta['fixed_availability'];
			}

			$people_counter = 0;

			// Max & Min People Check
			if ( ! empty( $tf_tour_unserial_fixed_date['max_seat'] ) && $tf_tour_unserial_fixed_date['max_seat'] >= $total_people && $tf_tour_unserial_fixed_date['max_seat'] != 0 && ! empty( $tf_tour_unserial_fixed_date['min_seat'] ) && $tf_tour_unserial_fixed_date['min_seat'] <= $total_people && $tf_tour_unserial_fixed_date['min_seat'] != 0 ) {
				$people_counter ++;
			}
			if ( $people_counter > 0 ) {
				$show_fixed_tour = [];

				if ( ! empty( $fixed_availability['from'] ) && array_key_exists( $fixed_availability['from'], $tf_searching_period ) ) {
					$show_fixed_tour[] = 1;
				}

				if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
					if ( ! empty( $meta['adult_price'] ) ) {
						if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
							$has_tour = ! empty( $show_fixed_tour ) && ! in_array( 0, $show_fixed_tour );
						}
					}
					if ( ! empty( $meta['child_price'] ) ) {
						if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
							$has_tour = ! empty( $show_fixed_tour ) && ! in_array( 0, $show_fixed_tour );
						}
					}
					if ( ! empty( $meta['infant_price'] ) ) {
						if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
							$has_tour = ! empty( $show_fixed_tour ) && ! in_array( 0, $show_fixed_tour );
						}
					}
					if ( ! empty( $meta['group_price'] ) ) {
						if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
							$has_tour = ! empty( $show_fixed_tour ) && ! in_array( 0, $show_fixed_tour );
						}
					}
				} else {
					$has_tour = ! empty( $show_fixed_tour ) && ! in_array( 0, $show_fixed_tour );
				}
			}
		}

		if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {

			$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

			if ( $custom_availability ) {

				if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {
					$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $meta['cont_custom_date'] );
					$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );
					$custom_dates                 = wp_list_pluck( $tf_tour_unserial_custom_date, 'date' );
				} else {
					$custom_dates = wp_list_pluck( $meta['cont_custom_date'], 'date' );
				}
				$people_counter = 0;
				if ( ! empty( $meta['cont_custom_date'] ) ) {
					foreach ( $meta['cont_custom_date'] as $minmax ) {
						// Max & Min People Check
						if ( ! empty( $minmax['max_people'] ) && $minmax['max_people'] >= $total_people && $minmax['max_people'] != 0 && ! empty( $minmax['min_people'] ) && $minmax['min_people'] <= $total_people && $minmax['min_people'] != 0 ) {
							$people_counter ++;
						}
					}
				}
				if ( $people_counter > 0 ) {
					foreach ( $custom_dates as $custom_date ) {
						$show_continuous_tour = [];
						if ( ! empty( $custom_date['from'] ) && array_key_exists( $custom_date['from'], $tf_searching_period ) ) {
							$show_continuous_tour[] = 1;
						}

						if ( ! empty( $show_continuous_tour ) && ! in_array( 0, $show_continuous_tour ) ) {
							if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
								foreach ( $meta['cont_custom_date'] as $single_avail ) {
									if ( ! empty( $single_avail['adult_price'] ) ) {
										if ( $startprice <= $single_avail['adult_price'] && $single_avail['adult_price'] <= $endprice ) {
											$has_tour = true;
										}
									}
									if ( ! empty( $single_avail['child_price'] ) ) {
										if ( $startprice <= $single_avail['child_price'] && $single_avail['child_price'] <= $endprice ) {
											$has_tour = true;
										}
									}
									if ( ! empty( $single_avail['infant_price'] ) ) {
										if ( $startprice <= $single_avail['infant_price'] && $single_avail['infant_price'] <= $endprice ) {
											$has_tour = true;
										}
									}
									if ( ! empty( $single_avail['group_price'] ) ) {
										if ( $startprice <= $single_avail['group_price'] && $single_avail['group_price'] <= $endprice ) {
											$has_tour = true;
										}
									}
								}
							} else {
								$has_tour = true;
							}
							break;
						}
					}
				}

			} else {
				$tf_disable_dates = [];
				array_key_exists( 'disable_specific', $meta ) ? $tf_disable_dates = explode( ", ", $meta['disable_specific'] ) : '';
				if ( ! empty( $meta['disable_range'] ) && gettype( $meta['disable_range'] ) == "string" ) {
					$tf_tour_unserial_disable_date_range = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $meta['disable_range'] );
					$tf_tour_unserial_disable_date_range = unserialize( $tf_tour_unserial_disable_date_range );
					$tf_disable_range_dates              = wp_list_pluck( $tf_tour_unserial_disable_date_range, 'date' );
				} else {
					$tf_disable_range_dates = wp_list_pluck( $meta['disable_range'], 'date' );
				}

				$tf_disable_ranges = [];
				if ( ! empty( $tf_disable_range_dates ) ) {
					foreach ( $tf_disable_range_dates as $disable_range ) {
						// Create DateTime objects for the start and end dates of the range
						$start = new \DateTime( $disable_range["from"] );
						$end   = new \DateTime( $disable_range["to"] );

						// Iterate over each day in the range and add it to the tf_disable_ranges array
						while ( $start <= $end ) {
							$tf_disable_ranges[] = $start->format( "Y/m/d" );
							$start->add( new \DateInterval( "P1D" ) );
						}
					}
				}
				$people_counter = 0;

				// Max & Min People Check
				if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] >= $total_people && $meta['cont_max_people'] != 0 && ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] <= $total_people && $meta['cont_min_people'] != 0 ) {
					$people_counter ++;
				}
				if ( $people_counter > 0 ) {
					if ( ! empty( $tf_disable_dates ) || ! empty( $tf_disable_ranges ) ) {
						$tf_all_disable_dates = array_merge( $tf_disable_dates, $tf_disable_ranges );
						$tf_disable_found     = false;

						foreach ( $period as $date ) {
							if ( in_array( $date->format( 'Y/m/d' ), $tf_all_disable_dates ) ) {
								$tf_disable_found = true;
								break;
							}
						}

						if ( $tf_disable_found ) {
							$has_tour = false;
						} else {
							if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
								if ( ! empty( $meta['adult_price'] ) ) {
									if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
								if ( ! empty( $meta['child_price'] ) ) {
									if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
								if ( ! empty( $meta['infant_price'] ) ) {
									if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
								if ( ! empty( $meta['group_price'] ) ) {
									if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
							} else {
								$has_tour = true;
							}
						}
					} else {
						if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
							if ( ! empty( $meta['adult_price'] ) ) {
								if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['child_price'] ) ) {
								if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['infant_price'] ) ) {
								if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['group_price'] ) ) {
								if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
						} else {
							$has_tour = true;
						}
					}
				}

			}

		}
		if ( $has_tour ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);

		}
	}

	/**
	 * Filter tours on search result page by without date dates set by backend
	 *
	 *
	 * @param \DatePeriod $period collection of dates by user input;
	 * @param array $not_found collection of tour exists
	 * @param array $data user input for sidebar form
	 *
	 * @author Jahid
	 *
	 */
	static function tf_filter_tour_by_without_date( $period, &$total_posts, array &$not_found, array $data = [] ): void {
		if ( isset( $data[3] ) && isset( $data[4] ) ) {
			[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $check_in_out ] = $data;
		}
		// Get tour meta options
		$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

		// Set initial tour availability status
		$has_tour = false;

		if ( ! empty( $meta['type'] ) && $meta['type'] === 'fixed' ) {

			$show_fixed_tour = [];

			if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
				if ( ! empty( $meta['adult_price'] ) ) {
					if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
				if ( ! empty( $meta['child_price'] ) ) {
					if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
				if ( ! empty( $meta['infant_price'] ) ) {
					if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
				if ( ! empty( $meta['group_price'] ) ) {
					if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
			} else {
				$has_tour = true;
			}

		}

		if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {

			$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
			if ( $custom_availability ) {

				if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {
					$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $meta['cont_custom_date'] );
					$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );
					$custom_dates                 = wp_list_pluck( $tf_tour_unserial_custom_date, 'date' );
				} else {
					$custom_dates = wp_list_pluck( $meta['cont_custom_date'], 'date' );
				}

				foreach ( $custom_dates as $custom_date ) {

					$show_continuous_tour = [];

					if ( ! in_array( 0, $show_continuous_tour ) ) {
						if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
							if ( ! empty( $meta['adult_price'] ) ) {
								if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['child_price'] ) ) {
								if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['infant_price'] ) ) {
								if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['group_price'] ) ) {
								if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
						} else {
							$has_tour = true;
						}

						break;

					}

				}

			} else {

				if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
					if ( ! empty( $meta['adult_price'] ) ) {
						if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
							$has_tour = true;
						}
					}
					if ( ! empty( $meta['child_price'] ) ) {
						if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
							$has_tour = true;
						}
					}
					if ( ! empty( $meta['infant_price'] ) ) {
						if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
							$has_tour = true;
						}
					}
					if ( ! empty( $meta['group_price'] ) ) {
						if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
							$has_tour = true;
						}
					}
				} else {
					$has_tour = true;
				}

			}

		}
		if ( $has_tour ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);

		}
	}

	/*
     * Tour search ajax
     * @since 2.9.7
     * @author Foysal
     */
	function tf_tour_search_ajax_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		if ( Helper::tfopt( 'required_location_tour_search' ) && ( ! isset( $_POST['place'] ) || empty( $_POST['place'] ) ) ) {
			$response['message'] = esc_html__( 'Please enter your location', 'tourfic' );
		} elseif ( Helper::tfopt( 'date_tour_search' ) && ( ! isset( $_POST['check-in-out-date'] ) || empty( $_POST['check-in-out-date'] ) ) ) {
			$response['message'] = esc_html__( 'Please select a date', 'tourfic' );
		}

		// Whitelist only fields needed for search
		$allowed_fields = [
			'place-name',
			'place',
			'adults',
			'children',
			'infant',
			'check-in-out-date',
			'type',
			'types',
			'from',
			'to',
			'_nonce',
		];

		$fields = [];
		foreach ( $allowed_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				if ( is_array( $_POST[ $key ] ) ) {
					$fields[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) );
				} else {
					$fields[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
				}
			}
		}

		// Success response if validation passes
		if ( ( Helper::tfopt( 'date_tour_search' ) && ! empty( $fields['check-in-out-date'] ) )
			|| ( ! Helper::tfopt( 'date_tour_search' ) && ( ! Helper::tfopt( 'required_location_tour_search' ) || ! empty( $fields['place'] ) ) ) ) {

			$response['query_string'] = http_build_query( $fields );
			$response['status']       = 'success';
		}

		echo wp_json_encode( $response );
		wp_die();
	}

	/*
    * Tour will be auto draft after Expire
    * Author: Jahid
    */
	function tf_setup_everydate_cron_job() {
		if ( ! wp_next_scheduled( 'tf_everydate_cron_job' ) ) {
			wp_schedule_event( strtotime( 'midnight' ), 'daily', 'tf_everydate_cron_job' );
		}
	}

	function tf_every_date_function() {

		$args      = array(
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		);
		$tour_loop = new \WP_Query( $args );
		while ( $tour_loop->have_posts() ) : $tour_loop->the_post();
			$post_id = get_the_ID();
			$meta    = get_post_meta( $post_id, 'tf_tours_opt', true );

			if ( $meta['type'] == "fixed" ) {
				if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
					$tf_tour_unserial_fixed_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $meta['fixed_availability'] );
					$tf_tour_unserial_fixed_date = unserialize( $tf_tour_unserial_fixed_date );
					$fixed_availability          = ! empty( $tf_tour_unserial_fixed_date ) ? $tf_tour_unserial_fixed_date['date'] : [];
				} else {
					$fixed_availability = ! empty( $meta['fixed_availability'] ) ? $meta['fixed_availability']['date'] : [];
				}
				if ( ! empty( $fixed_availability ) ) {
					$show_fixed_tour   = [];
					$show_fixed_tour[] = intval( strtotime( gmdate( 'Y-m-d' ) ) >= strtotime( $fixed_availability['from'] ) && strtotime( gmdate( 'Y-m-d' ) ) <= strtotime( $fixed_availability['to'] ) );
					if ( empty( $show_fixed_tour['0'] ) ) {
						$tf_tour_data = array(
							'ID'          => $post_id,
							'post_status' => 'expired',
						);
						wp_update_post( $tf_tour_data );
					}
				}
			}
		endwhile;
		wp_reset_postdata();

	}

	/*
    * Tour Expired Status Add
    * Author: Jahid
    */
	function tf_tours_custom_status_creation() {
		register_post_status( 'expired', array(
			'label'                     => esc_html(_x( 'Expired', 'post', 'tourfic')),
			/* translators: %s: number of posts */
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'tourfic'),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true
		) );
	}

	function tf_tours_custom_status_add_in_quick_edit() {
		global $post;
		if ( ! empty( $post->post_type ) && $post->post_type == 'tf_tours' ) {
			echo "<script>
    jQuery(document).ready( function() {
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"expired\">Expired</option>' );      
    }); 
    </script>";
		}
	}

	function tf_tours_custom_status_add_in_post_page() {
		global $post;
		if ( $post->post_type == 'tf_tours' ) {
			echo "<script>
        jQuery(document).ready( function() {        
            jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"expired\">Expired</option>' );
        });
        </script>";
		}
	}

	/**
	 * Assign taxonomy(tour_type & tour_features) from the single post metabox
	 * to a Tour when updated or published
	 * @return array();
	 * @author Foysal
	 * @since 2.9.23
	 */
	function tf_tour_type_assign_taxonomies( $post_id, $post, $old_status ) {
		if ( 'tf_tours' !== $post->post_type ) {
			return;
		}
		$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
		//types
		if ( ! empty( $meta['tour_types'] ) && is_array( $meta['tour_types'] ) ) {
			$tour_types = array_map( 'intval', $meta['tour_types'] );
			wp_set_object_terms( $post_id, $tour_types, 'tour_type' );
		}

		// features
		if ( ! empty( $meta['features'] ) && is_array( $meta['features'] ) ) {
			$features = array_map( 'intval', $meta['features'] );
			wp_set_object_terms( $post_id, $features, 'tour_features' );
		}
	}

	function tf_tour_booking_popup_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		$response             = array();
		$adults               = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : 0;
		$children             = isset( $_POST['children'] ) ? intval( sanitize_text_field( $_POST['children'] ) ) : 0;
		$infant               = isset( $_POST['infant'] ) ? intval( sanitize_text_field( $_POST['infant'] ) ) : 0;
		$total_people         = $adults + $children + $infant;
		$total_people_booking = $adults + $children;
		// Tour date
		$tour_date = ! empty( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
		$tour_time = isset( $_POST['check_in_time'] ) ? sanitize_text_field( $_POST['check_in_time'] ) : null;


		$post_id              = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : '';
		$meta                 = get_post_meta( $post_id, 'tf_tours_opt', true );
		$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

		/**
		 * If fixed is selected but pro is not activated
		 *
		 * show error
		 *
		 * @return
		 */
		if ( $tour_type == 'fixed' && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
			$response['errors'][] = esc_html__( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
			$response['status']   = 'error';
			echo wp_json_encode( $response );
			die();

			return;
		}

		if ( $tour_type == 'fixed' ) {

			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_fixed_avail   = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_fixed_date    = unserialize( $tf_tour_fixed_avail );
				$start_date            = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
				$end_date              = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
				$min_people            = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
				$max_people            = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
				$tf_tour_booking_limit = ! empty( $tf_tour_fixed_date['max_capacity'] ) ? $tf_tour_fixed_date['max_capacity'] : 0;
			} else {
				$start_date            = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
				$end_date              = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
				$min_people            = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
				$max_people            = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
				$tf_tour_booking_limit = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : 0;
			}


			// Fixed tour maximum capacity limit

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $start_date ) && ! empty( $end_date ) ) {

				// Tour Order retrive from Tourfic Order Table

				$tf_orders_select    = array(
					'select'    => "post_id,order_details",
					'post_type' => 'tour',
					'query'     => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = Helper::tourfic_order_table_data( $tf_orders_select );

				$tf_total_adults    = 0;
				$tf_total_childrens = 0;

				foreach ( $tf_tour_book_orders as $order ) {
					$tour_id       = $order['post_id'];
					$order_details = json_decode( $order['order_details'] );
					$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
					list( $tf_booking_start, $tf_booking_end ) = explode( " - ", $tf_tour_date );
					if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_booking_start ) && $start_date == $tf_booking_start && ! empty( $tf_booking_end ) && $end_date == $tf_booking_end ) {
						$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
						if ( ! empty( $book_adult ) ) {
							list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
							$tf_total_adults += $tf_total_adult;
						}

						$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
						if ( ! empty( $book_children ) ) {
							list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
							$tf_total_childrens += $tf_total_children;
						}
					}
				}

				$tf_total_people = $tf_total_adults + $tf_total_childrens;

				if ( ! empty( $tf_tour_booking_limit ) ) {
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;
					if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Tour', 'tourfic' );
					}
					if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
						/* translators: %1$s: available seats */
						$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Tour', 'tourfic' ), $tf_today_limit );
					}
				}
			}

		} elseif ( $tour_type == 'continuous' ) {

			$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

			if ( $custom_avail == true ) {

				$pricing_rule     = $meta['custom_pricing_by'];
				$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
				if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
					$tf_tour_conti_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $cont_custom_date );
					$cont_custom_date    = unserialize( $tf_tour_conti_avail );
				}

			} elseif ( $custom_avail == false ) {

				$min_people          = ! empty( $meta['cont_min_people'] ) ? $meta['cont_min_people'] : '';
				$max_people          = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : '';
				$allowed_times_field = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';


				// Daily Tour Booking Capacity && Tour Order retrive from Tourfic Order Table
				$tf_orders_select    = array(
					'select'    => "post_id,order_details",
					'post_type' => 'tour',
					'query'     => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = Helper::tourfic_order_table_data( $tf_orders_select );

				$tf_total_adults    = 0;
				$tf_total_childrens = 0;

				if ( empty( $allowed_times_field ) || $tour_time == null ) {
					$tf_tour_booking_limit = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : 0;
					foreach ( $tf_tour_book_orders as $order ) {
						$tour_id       = $order['post_id'];
						$order_details = json_decode( $order['order_details'] );
						$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
						$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

						if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
							$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
							if ( ! empty( $book_adult ) ) {
								list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
								$tf_total_adults += $tf_total_adult;
							}

							$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
							if ( ! empty( $book_children ) ) {
								list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
								$tf_total_childrens += $tf_total_children;
							}
						}
					}
				} else {
					if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
						$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
					}

					if ( ! empty( $allowed_times_field[ $tour_time ]['cont_max_capacity'] ) ) {
						$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['cont_max_capacity'];

						foreach ( $tf_tour_book_orders as $order ) {
							$tour_id       = $order['post_id'];
							$order_details = json_decode( $order['order_details'] );
							$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
							$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

							if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
								$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
								if ( ! empty( $book_adult ) ) {
									list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
									$tf_total_adults += $tf_total_adult;
								}

								$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
								if ( ! empty( $book_children ) ) {
									list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
									$tf_total_childrens += $tf_total_children;
								}
							}
						}

					}
				}
				$tf_total_people = $tf_total_adults + $tf_total_childrens;

				if ( ! empty( $tf_tour_booking_limit ) ) {
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

					if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
					}
					if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
						/* translators: %1$s: available seats */
						$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
					}
				}
			}

		}

		/**
		 * If continuous custom availability is selected but pro is not activated
		 *
		 * Show error
		 *
		 * @return
		 */
		if ( $tour_type == 'continuous' && $custom_avail == true && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
			$response['errors'][] = esc_html__( 'Custom Continous Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
			$response['status']   = 'error';
			echo wp_json_encode( $response );
			die();

			return;
		}


		if ( $tour_type == 'continuous' ) {
			$start_date = $end_date = $tour_date;
		}

		/**
		 * People 0 number validation
		 *
		 */
		if ( $total_people == 0 ) {
			$response['errors'][] = esc_html__( 'Please Select Adults/Children/Infant required', 'tourfic' );
		}

		/**
		 * People number validation
		 *
		 */
		if ( $tour_type == 'fixed' ) {

			/* translators: %s: number of people */
			$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );

			/* translators: %s: number of people */
			$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

			if ( $total_people < $min_people && $min_people > 0 ) {
				/* translators: %1$s: minimum number of people */
				$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

			} else if ( $total_people > $max_people && $max_people > 0 ) {
				/* translators: %1$s: maximum number of people */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

			}

		} elseif ( $tour_type == 'continuous' && $custom_avail == false ) {

			/* translators: %s: number of people */
			$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );

			/* translators: %s: number of people */
			$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

			if ( $total_people < $min_people && $min_people > 0 ) {
				/* translators: %s: minimum number of people */
				$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

			} else if ( $total_people > $max_people && $max_people > 0 ) {
				/* translators: %s: maximum number of people */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

			}

		} elseif ( $tour_type == 'continuous' && $custom_avail == true ) {

			foreach ( $cont_custom_date as $item ) {

				// Backend continuous date values
				$back_date_from     = ! empty( $item['date']['from'] ) ? $item['date']['from'] : '';
				$back_date_to       = ! empty( $item['date']['from'] ) ? $item['date']['to'] : '';
				$back_date_from_stt = strtotime( str_replace( '/', '-', $back_date_from ) );
				$back_date_to_stt   = strtotime( str_replace( '/', '-', $back_date_to ) );
				// frontend selected date value
				$front_date = strtotime( str_replace( '/', '-', $tour_date ) );
				// Backend continuous min/max people values
				$min_people = ! empty( $item['min_people'] ) ? $item['min_people'] : '';
				$max_people = ! empty( $item['max_people'] ) ? $item['max_people'] : '';
				/* translators: %s: minimum number of people */
				$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
				/* translators: %s: maximum number of people */
				$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


				// Compare backend & frontend date values to show specific people number error
				if ( $front_date >= $back_date_from_stt && $front_date <= $back_date_to_stt ) {
					if ( $total_people < $min_people && $min_people > 0 ) {
						/* translators: %1$s: minimum number of people, %2$s: start date, %3$s: end date */
						$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ), $min_text, $back_date_from, $back_date_to );

					}
					if ( $total_people > $max_people && $max_people > 0 ) {
						/* translators: %1$s: maximum number of people, %2$s: start date, %3$s: end date */
						$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ), $max_text, $back_date_from, $back_date_to );

					}


					$allowed_times_field = ! empty( $item['allowed_time'] ) ? $item['allowed_time'] : '';

					// Daily Tour Booking Capacity && tour order retrive form tourfic order table
					$tf_orders_select    = array(
						'select'    => "post_id,order_details",
						'post_type' => 'tour',
						'query'     => " AND ostatus = 'completed' ORDER BY order_id DESC"
					);
					$tf_tour_book_orders = Helper::tourfic_order_table_data( $tf_orders_select );

					$tf_total_adults    = 0;
					$tf_total_childrens = 0;

					if ( empty( $allowed_times_field ) || $tour_time == null ) {
						$tf_tour_booking_limit = ! empty( $item['max_capacity'] ) ? $item['max_capacity'] : '';

						foreach ( $tf_tour_book_orders as $order ) {
							$tour_id       = $order['post_id'];
							$order_details = json_decode( $order['order_details'] );
							$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
							$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

							if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
								$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
								if ( ! empty( $book_adult ) ) {
									list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
									$tf_total_adults += $tf_total_adult;
								}

								$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
								if ( ! empty( $book_children ) ) {
									list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
									$tf_total_childrens += $tf_total_children;
								}
							}
						}

					} else {
						if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
							$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
						}

						if ( ! empty( $allowed_times_field[ $tour_time ]['max_capacity'] ) ) {
							$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['max_capacity'];

							foreach ( $tf_tour_book_orders as $order ) {
								$tour_id       = $order['post_id'];
								$order_details = json_decode( $order['order_details'] );
								$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
								$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

								if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
									$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
									if ( ! empty( $book_adult ) ) {
										list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
										$tf_total_adults += $tf_total_adult;
									}

									$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
									if ( ! empty( $book_children ) ) {
										list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
										$tf_total_childrens += $tf_total_children;
									}
								}
							}

						}
					}
					$tf_total_people = $tf_total_adults + $tf_total_childrens;

					if ( ! empty( $tf_tour_booking_limit ) ) {
						$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

						if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
							$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
						}
						if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
							/* translators: %1$s: available seats */
							$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
						}
					}
				}

			}

		}

		/**
		 * Check errors
		 *
		 */
		/* Minimum days to book before departure */
		$min_days_before_book = ! empty( $meta['min_days_before_book'] ) ? $meta['min_days_before_book'] : '0';
		/* translators: %s: minimum days to book before departure */
		$min_days_before_book_text = sprintf( _n( '%s day', '%s days', $min_days_before_book, 'tourfic' ), $min_days_before_book );
		$today_stt                 = new \DateTime( gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d' ) ) ) );
		$tour_date_stt             = new \DateTime( gmdate( 'Y-m-d', strtotime( $start_date ) ) );
		$day_difference            = $today_stt->diff( $tour_date_stt )->days;
		$adult_required_chield     = ! empty( $meta["require_adult_child_booking"] ) ? $meta["require_adult_child_booking"] : 0;


		if ( $day_difference < $min_days_before_book ) {
			/* translators: %1$s: minimum days to book before departure */
			$response['errors'][] = sprintf( esc_html__( 'Present date to booking date required minimum %1$s gap', 'tourfic' ), $min_days_before_book_text );
		}
		if ( ! $start_date ) {
			$response['errors'][] = esc_html__( 'You must select booking date', 'tourfic' );
		}
		if ( ! $post_id ) {
			$response['errors'][] = esc_html__( 'Unknown Error! Please try again.', 'tourfic' );
		}

		/**
		 * Price by date range
		 *
		 * Tour type continuous and custom availability is true
		 */
		$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
		if ( ! empty( $tf_cont_custom_date ) && gettype( $tf_cont_custom_date ) == "string" ) {
			$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $tf_cont_custom_date );
			$tf_cont_custom_date       = unserialize( $tf_tour_conti_custom_date );
		}

		$tour = strtotime( $tour_date );
		if ( isset( $custom_avail ) && true == $custom_avail ) {
			$seasional_price = array_values( array_filter( $tf_cont_custom_date, function ( $value ) use ( $tour ) {
				$seasion_start = strtotime( $value['date']['from'] );
				$seasion_end   = strtotime( $value['date']['to'] );

				return $seasion_start <= $tour && $seasion_end >= $tour;
			} ) );
		}


		if ( $tour_type === 'continuous' && ! empty( $tf_cont_custom_date ) && ! empty( $seasional_price ) ) {

			$group_price    = ! empty( $seasional_price[0]['group_price'] ) ? $seasional_price[0]['group_price'] : 0;
			$adult_price    = ! empty( $seasional_price[0]['adult_price'] ) ? $seasional_price[0]['adult_price'] : 0;
			$children_price = ! empty( $seasional_price[0]['child_price'] ) ? $seasional_price[0]['child_price'] : 0;
			$infant_price   = ! empty( $seasional_price[0]['infant_price'] ) ? $seasional_price[0]['infant_price'] : 0;

		} else {

			$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
			$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
			$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
			$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;

		}

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type == 'continuous' ) {
			$tf_allowed_times = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
			if ( ! empty( $tf_allowed_times ) && gettype( $tf_allowed_times ) == "string" ) {
				$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $tf_allowed_times );
				$tf_allowed_times          = unserialize( $tf_tour_conti_custom_date );
			}

			if ( $custom_avail == false && ! empty( $tf_allowed_times ) && empty( $tour_time_title ) ) {
				$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
			}
			if ( $custom_avail == true && ! empty( $seasional_price[0]['allowed_time'] ) && empty( $tour_time_title ) ) {
				$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
			}
		}

		if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'person' ) {

			if ( ! $disable_adult_price && $adults > 0 && empty( $adult_price ) ) {
				$response['errors'][] = esc_html__( 'Adult price is blank!', 'tourfic' );
			}
			if ( ! $disable_child_price && $children > 0 && empty( $children_price ) ) {
				$response['errors'][] = esc_html__( 'Childern price is blank!', 'tourfic' );
			}
			if ( ! $disable_infant_price && $infant > 0 && empty( $infant_price ) ) {
				$response['errors'][] = esc_html__( 'Infant price is blank!', 'tourfic' );
			}
			if ( $infant > 0 && ! empty( $infant_price ) && ! $adults ) {
				$response['errors'][] = esc_html__( 'Infant without adults is not allowed!', 'tourfic' );
			}

			if ( $adult_required_chield && $children > 0 && ! empty( $children_price ) && empty( $adults ) ) {
				$response['errors'][] = esc_html__( 'An adult is required for children booking!', 'tourfic' );
			}

		} else if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'group' ) {

			if ( empty( $group_price ) ) {
				$response['errors'][] = esc_html__( 'Group price is blank!', 'tourfic' );
			}

		}

		// Tour extra
		$tour_extra_total     = 0;
		$tour_extra_title_arr = [];
		$tour_extra_meta      = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
		if ( ! empty( $tour_extra_meta ) ) {
			$tf_tour_extra = !empty($_POST['tour_extra']) ? sanitize_text_field($_POST['tour_extra']) : '';
			$tours_extra         = !empty($tf_tour_extra) ? explode( ',', $tf_tour_extra ) : [];
			$tf_tour_extra_quantity = !empty($_POST['tour_extra_quantity']) ? sanitize_text_field($_POST['tour_extra_quantity']) : '';
			$tour_extra_quantity = !empty($tf_tour_extra_quantity) ? explode( ',', $tf_tour_extra_quantity ) : [];

			if(!empty($tours_extra)){
				foreach ( $tours_extra as $extra_key => $extra ) {
					$tour_extra_pricetype = ! empty( $tour_extra_meta[ $extra ]['price_type'] ) ? $tour_extra_meta[ $extra ]['price_type'] : 'fixed';
					if ( $tour_extra_pricetype == "fixed" ) {
						if ( ! empty( $tour_extra_meta[ $extra ]['title'] ) && ! empty( $tour_extra_meta[ $extra ]['price'] ) ) {
							$tour_extra_total       += $tour_extra_meta[ $extra ]['price'];
							$tour_extra_title_arr[] = array(
								'title' => $tour_extra_meta[ $extra ]['title'],
								'price' => $tour_extra_meta[ $extra ]['price']
							);
						}
					} else if ( $tour_extra_pricetype == "quantity" ) {
						if ( ! empty( $tour_extra_meta[ $extra ]['title'] ) && ! empty( $tour_extra_meta[ $extra ]['price'] ) ) {
							$tour_extra_total       += $tour_extra_meta[ $extra ]['price'] * $tour_extra_quantity[ $extra_key ];
							$tour_extra_title_arr[] = array(
								'title' => $tour_extra_meta[ $extra ]['title'] . " x " . $tour_extra_quantity[ $extra_key ],
								'price' => $tour_extra_meta[ $extra ]['price'] * $tour_extra_quantity[ $extra_key ]
							);
						}
					} else {
						if ( ! empty( $tour_extra_meta[ $extra ]['price'] ) && ! empty( $tour_extra_meta[ $extra ]['title'] ) ) {
							$tour_extra_total       += ( $tour_extra_meta[ $extra ]['price'] * $total_people );
							$tour_extra_title_arr[] = array(
								'title' => $tour_extra_meta[ $extra ]['title'],
								'price' => $tour_extra_meta[ $extra ]['price'] * $total_people
							);
						}
					}
				}
			}
		}

		if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {


			# Discount informations
			$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
			$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

			# Calculate discounted price
			if ( $discount_type == 'percent' ) {

				$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 ) ) );
				$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 ) ) );
				$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 ) ) );
				$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 ) ) );

			} elseif ( $discount_type == 'fixed' ) {

				$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 ) ) );
				$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 ) ) );
				$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 ) ) );
				$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 ) ) );

			}


			# Set pricing based on pricing rule
			if ( $pricing_rule == 'group' ) {
				$tf_tours_data_price = $group_price;
			} else {
				$tf_tours_data_price = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
			}
			if ( ! empty( $_POST['deposit'] ) && $_POST['deposit'] == "true" ) {
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['allow_deposit'] ) && $meta['allow_deposit'] == '1' && ! empty( $meta['deposit_amount'] ) ) {

					if ( ! empty( $meta['deposit_type'] ) && $meta['deposit_type'] == 'fixed' ) {
						$tf_deposit_amount   = ! empty( $meta['deposit_amount'] ) ? $meta['deposit_amount'] : 0;
						$tf_due_amount       = $tf_tours_data_price - $tf_deposit_amount;
						$tf_tours_data_price = $tf_deposit_amount;
					} else {
						$tf_deposit_amount   = ! empty( $meta['deposit_amount'] ) ? ( $tf_tours_data_price * $meta['deposit_amount'] ) / 100 : 0;
						$tf_due_amount       = $tf_tours_data_price - $tf_deposit_amount;
						$tf_tours_data_price = $tf_deposit_amount;
					}
				}
			}
			$traveller_info_fields = ! empty( Helper::tfopt( 'without-payment-field' ) ) ? Helper::tf_data_types( Helper::tfopt( 'without-payment-field' ) ) : '';

			$response['traveller_info']    = '';
			$response['traveller_summery'] = '';
			for ( $traveller_in = 1; $traveller_in <= $total_people; $traveller_in ++ ) {
				$response['traveller_info'] .= '<div class="tf-single-tour-traveller tf-single-travel">
                <h4>' . sprintf( esc_html__( 'Traveler ', 'tourfic' ) ) . $traveller_in . '</h4>
                <div class="traveller-info">';
				if ( empty( $traveller_info_fields ) ) {
					$response['traveller_info'] .= '<div class="traveller-single-info">
                        <label for="tf_full_name' . $traveller_in . '">' . sprintf( esc_html__( 'Full Name', 'tourfic' ) ) . '</label>
                        <input type="text" name="traveller[' . $traveller_in . '][tf_full_name]" id="tf_full_name' . $traveller_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_full_name' . $traveller_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_dob' . $traveller_in . '">' . sprintf( esc_html__( 'Date of birth', 'tourfic' ) ) . '</label>
                        <input type="date" name="traveller[' . $traveller_in . '][tf_dob]" id="tf_dob' . $traveller_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_dob' . $traveller_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_nid' . $traveller_in . '">' . sprintf( esc_html__( 'NID', 'tourfic' ) ) . '</label>
                        <input type="text" name="traveller[' . $traveller_in . '][tf_nid]" id="tf_nid' . $traveller_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_nid' . $traveller_in . '"></div>
                    </div>
                    ';
				} else {
					foreach ( $traveller_info_fields as $field ) {
						if ( "text" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) {
							$reg_field_required         = ! empty( $field['reg-field-required'] ) ? $field['reg-field-required'] : '';
							$response['traveller_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $traveller_in . '">' . esc_html( $field['reg-field-label'] ) . '</label>
                                <input type="' . $field['reg-fields-type'] . '" name="traveller[' . $traveller_in . '][' . $field['reg-field-name'] . ']" data-required="' . $reg_field_required . '" id="' . $field['reg-field-name'] . $traveller_in . '" />
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $traveller_in . '"></div>
                            </div>';
						}
						if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) {
							$response['traveller_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $traveller_in . '">' . esc_html( $field['reg-field-label'] ) . '</label>
                                <select id="' . $field['reg-field-name'] . $traveller_in . '" name="traveller[' . $traveller_in . '][' . $field['reg-field-name'] . ']" data-required="' . $field['reg-field-required'] . '"><option value="">' . sprintf( esc_html__( 'Select One', 'tourfic' ) ) . '</option>';
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
									$response['traveller_info'] .= '<option value="' . $sfield['option-value'] . '">' . $sfield['option-label'] . '</option>';
								}
							}
							$response['traveller_info'] .= '</select>
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $traveller_in . '"></div>
                            </div>';
						}
						if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) {
							$response['traveller_info'] .= '
                            <div class="traveller-single-info">
                            <label for="' . $field['reg-field-name'] . $traveller_in . '">' . esc_html( $field['reg-field-label'] ) . '</label>
                            ';
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
									$response['traveller_info'] .= '
                                        <div class="tf-single-checkbox">
                                        <input type="' . esc_attr( $field['reg-fields-type'] ) . '" name="traveller[' . $traveller_in . '][' . $field['reg-field-name'] . '][]" id="' . $sfield['option-value'] . $traveller_in . '" value="' . $sfield['option-value'] . '" data-required="' . $field['reg-field-required'] . '" />
                                        <label for="' . $sfield['option-value'] . $traveller_in . '">' . esc_html( $sfield['option-label'] ) . '</label></div>';
								}
							}
							$response['traveller_info'] .= '
                            <div class="error-text" data-error-for="' . $field['reg-field-name'] . $traveller_in . '"></div>
                            </div>';
						}
					}
				}

				$response['traveller_info'] .= '</div>
            </div>';
				$tour_date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
			}
			$response['traveller_summery'] .= '<h6>' . esc_html__("On ", 'tourfic') . self::tf_date_format_user( $tour_date, $tour_date_format_for_users ) . '</h6>';
			$response['traveller_summery'] .= !empty( $tour_time_title ) ? '<h6>' . esc_html__("At ", 'tourfic') . $tour_time_title . '</h6>' : '';
			$response['traveller_summery'] .= '
        <table class="table" style="width: 100%">
            <thead>
                <tr>
                    <th align="left">' . sprintf( esc_html__( 'Traveller', 'tourfic' ) ) . '</th>
                    <th align="right">' . sprintf( esc_html__( 'Price', 'tourfic' ) ) . '</th>
                </tr>
            </thead>
            <tbody>';
			if ( ! empty( $pricing_rule ) && $pricing_rule == "person" ) {
				if ( ! empty( $adult_price ) && ! empty( $adults ) ) {
					$response['traveller_summery'] .= '<tr>
                        <td align="left">' . $adults . esc_html__( ' adults', 'tourfic' ) . ' (' . wc_price( $adult_price ) . '/' . $pricing_rule . ')</td>
                        <td align="right">' . wc_price( $adult_price * $adults ) . '</td>
                    </tr>';
				}
				if ( ! empty( $children_price ) && ! empty( $children ) ) {
					$response['traveller_summery'] .= '<tr>
                        <td align="left">' . $children . esc_html__( ' children', 'tourfic' ) . ' (' . wc_price( $children_price ) . '/' . $pricing_rule . ')</td>
                        <td align="right">' . wc_price( $children_price * $children ) . '</td>
                    </tr>';
				}
				if ( ! empty( $infant_price ) && ! empty( $infant ) ) {
					$response['traveller_summery'] .= '<tr>
                        <td align="left">' . $infant . esc_html__( ' infants', 'tourfic' ) . ' (' . wc_price( $infant_price ) . '/' . $pricing_rule . ')</td>
                        <td align="right">' . wc_price( $infant_price * $infant ) . '</td>
                    </tr>';
				}
			} else {
				if ( ! empty( $group_price ) ) {
					$response['traveller_summery'] .= '<tr>
                        <td align="left">' . esc_html__( 'Group Price', 'tourfic' ) . '</td>
                        <td align="right">' . wc_price( $group_price ) . '</td>
                    </tr>';
				}
			}
			if ( ! empty( $tour_extra_title_arr ) ) {
				foreach ( $tour_extra_title_arr as $extra_info ) {
					if ( ! empty( $extra_info['title'] ) && ! empty( $extra_info['price'] ) ) {
						$response['traveller_summery'] .= '<tr>
						<td align="left">' . esc_html( $extra_info['title'] ) . '</td>
						<td align="right">' . wc_price( $extra_info['price'] ) . '</td>
					</tr>';
					}
				}
			}
			if ( ! empty( $tf_due_amount ) ) {
				$response['traveller_summery'] .= '<tr>
                    <td align="left">' . esc_html__( 'Due', 'tourfic' ) . '</td>
                    <td align="right">' . wc_price( $tf_due_amount ) . '</td>
                </tr>';
			}

			$response['traveller_summery'] .= '</tbody>
            <tfoot>
                <tr>
                    <th align="left">' . esc_html__( 'Total', 'tourfic' ) . '</th>
                    <th align="right">' . wc_price( $tf_tours_data_price + $tour_extra_total ) . '</th>
                </tr>
            </tfoot>
        </table>';

		} else {
			# Show errors
			$response['status'] = 'error';

		}

		echo wp_json_encode( $response );
		die();
	}

    static function tf_date_format_user( $date, $format ) {
	    if ( ! empty( $date ) && ! empty( $format ) ) {
		    if ( str_contains( $date, " - " ) == true ) {
			    list( $first_date, $last_date ) = explode( " - ", $date );
			    $first_date = gmdate( $format, strtotime( $first_date ) );
			    $last_date  = gmdate( $format, strtotime( $last_date ) );

			    return "{$first_date} - {$last_date}";
		    } else {
			    return gmdate( $format, strtotime( $date ) );
		    }
	    } else {
		    return;
	    }
    }

	static function template( $type = 'archive', $post_id = '' ) {
		$tour_template = '';
		$post_id        = ! empty( $post_id ) ? $post_id : '';

		if ( $type == 'archive' ) {
			$tour_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		} elseif ( $type == 'single' && $post_id ) {
			$meta = get_post_meta( $post_id, 'tf_tours_opt', true );

			$layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
			if ( "single" == $layout_conditions ) {
				$single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
			}
			$global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
			$tour_template  = ! empty( $single_template ) ? $single_template : $global_template;
		} elseif ( $type == 'single' ) {
			$tour_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
		}

		return $tour_template;
	}
}