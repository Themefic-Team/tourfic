<?php
defined( 'ABSPATH' ) || exit;

/*
 * 
 * CUSTOM CSS
 * 
 */

if( !function_exists( 'tf_custom_css' ) ){
	function tf_custom_css(){
		// Store as PHP variables
		// Template 1 Global CSS
		$tf_template1_global_reg = !empty(tf_data_types(tfopt( 'tourfic-design1-global-color' ))['gcolor']) ? tf_data_types(tfopt( 'tourfic-design1-global-color' ))['gcolor'] : '';
		$tf_template1_p_global_reg = !empty(tf_data_types(tfopt( 'tourfic-design1-p-global-color' ))['pgcolor']) ? tf_data_types(tfopt( 'tourfic-design1-p-global-color' ))['pgcolor'] : '#36383C';

		// Common CSS
		$tf_primary_color_reg = !empty(tf_data_types(tfopt( 'tourfic-button-color' ))['regular']) ? tf_data_types(tfopt( 'tourfic-button-color' ))['regular'] : '';
		$tf_primary_color_hov = !empty(tf_data_types(tfopt( 'tourfic-button-color' ))['hover']) ? tf_data_types(tfopt( 'tourfic-button-color' ))['hover'] : '';
		$tf_primary_bg_color_reg = !empty(tf_data_types(tfopt( 'tourfic-button-bg-color' ))['regular']) ? tf_data_types(tfopt( 'tourfic-button-bg-color' ))['regular'] : '';
		$tf_primary_bg_color_hov = !empty(tf_data_types(tfopt( 'tourfic-button-bg-color' ))['hover']) ? tf_data_types(tfopt( 'tourfic-button-bg-color' ))['hover'] : '';
		$tf_sidebar_gradient_one_reg = !empty(tf_data_types(tfopt( 'tourfic-sidebar-booking' ))['gradient_one_reg']) ? tf_data_types(tfopt( 'tourfic-sidebar-booking' ))['gradient_one_reg'] : '';
		$tf_sidebar_gradient_two_reg = !empty(tf_data_types(tfopt( 'tourfic-sidebar-booking' ))['gradient_two_reg']) ? tf_data_types(tfopt( 'tourfic-sidebar-booking' ))['gradient_two_reg'] : '';
		$tf_faq_color = !empty(tf_data_types(tfopt( 'tourfic-faq-style' ))['faq_color']) ? tf_data_types(tfopt( 'tourfic-faq-style' ))['faq_color'] : '';
		$tf_faq_icon_color = !empty(tf_data_types(tfopt( 'tourfic-faq-style' ))['faq_icon_color']) ? tf_data_types(tfopt( 'tourfic-faq-style' ))['faq_icon_color'] : '';
		$tf_faq_border_color = !empty(tf_data_types(tfopt( 'tourfic-faq-style' ))['faq_border_color']) ? tf_data_types(tfopt( 'tourfic-faq-style' ))['faq_border_color'] : '';
		$tf_rating_color = !empty(tf_data_types(tfopt( 'tourfic-review-style' ))['rating_color']) ? tf_data_types(tfopt( 'tourfic-review-style' ))['rating_color'] : '';
		$tf_rating_bg_color = !empty(tf_data_types(tfopt( 'tourfic-review-style' ))['rating_bg_color']) ? tf_data_types(tfopt( 'tourfic-review-style' ))['rating_bg_color'] : '';
		$tf_param_bg_color = !empty(tf_data_types(tfopt( 'tourfic-review-style' ))['param_bg_color']) ? tf_data_types(tfopt( 'tourfic-review-style' ))['param_bg_color'] : '';
		$tf_param_txt_color = !empty(tf_data_types(tfopt( 'tourfic-review-style' ))['param_txt_color']) ? tf_data_types(tfopt( 'tourfic-review-style' ))['param_txt_color'] : '';
		$tf_param_single_bg_color = !empty(tf_data_types(tfopt( 'tourfic-review-style' ))['param_single_bg_color']) ? tf_data_types(tfopt( 'tourfic-review-style' ))['param_single_bg_color'] : '';
		$tf_review_color = !empty(tf_data_types(tfopt( 'tourfic-review-style' ))['review_color']) ? tf_data_types(tfopt( 'tourfic-review-style' ))['review_color'] : '';
		$tf_review_bg_color = !empty(tf_data_types(tfopt( 'tourfic-review-style' ))['review_bg_color']) ? tf_data_types(tfopt( 'tourfic-review-style' ))['review_bg_color'] : '';

		// Global Font Family
		$tf_global_font_family = tfopt('global-body-fonts-family') ? str_replace('_', ' ', tfopt('global-body-fonts-family')) : 'Default';
		$tf_global_heading_font_family = tfopt('global-heading-fonts-family') ? str_replace('_', ' ', tfopt('global-heading-fonts-family')) : 'Default';

		// Global Typography P
		$tf_global_font_p = tfopt('global-p') ? tfopt('global-p') : 16;
		$tf_global_font_weight_p = tfopt('global-p-weight') ? tfopt('global-p-weight') : 400;
		$tf_global_font_style_p = tfopt('global-p-style') ? tfopt('global-p-style') : 'normal';
		$tf_global_line_height_p = tfopt('global-p-line-height') ? tfopt('global-p-line-height') : 1.5;

		// Global Typography H1
		$tf_global_font_h1 = tfopt('global-h1') ? tfopt('global-h1') : 38;
		$tf_global_font_weight_h1 = tfopt('global-h1-weight') ? tfopt('global-h1-weight') : 500;
		$tf_global_font_style_h1 = tfopt('global-h1-style') ? tfopt('global-h1-style') : 'normal';$tf_global_line_height_h1 = tfopt('global-h1-line-height') ? tfopt('global-h1-line-height') : 1.2;

		// Global Typography H2
		$tf_global_font_h2 = tfopt('global-h2') ? tfopt('global-h2') : 30;
		$tf_global_font_weight_h2 = tfopt('global-h2-weight') ? tfopt('global-h2-weight') : 500;
		$tf_global_font_style_h2 = tfopt('global-h2-style') ? tfopt('global-h2-style') : 'normal';$tf_global_line_height_h2 = tfopt('global-h2-line-height') ? tfopt('global-h2-line-height') : 1.2;

		// Global Typography H3
		$tf_global_font_h3 = tfopt('global-h3') ? tfopt('global-h3') : 24;
		$tf_global_font_weight_h3 = tfopt('global-h3-weight') ? tfopt('global-h3-weight') : 500;
		$tf_global_font_style_h3 = tfopt('global-h3-style') ? tfopt('global-h3-style') : 'normal';$tf_global_line_height_h3 = tfopt('global-h3-line-height') ? tfopt('global-h3-line-height') : 1.2;

		// Global Typography H4
		$tf_global_font_h4 = tfopt('global-h4') ? tfopt('global-h4') : 20;
		$tf_global_font_weight_h4 = tfopt('global-h4-weight') ? tfopt('global-h4-weight') : 500;
		$tf_global_font_style_h4 = tfopt('global-h4-style') ? tfopt('global-h4-style') : 'normal';$tf_global_line_height_h4 = tfopt('global-h4-line-height') ? tfopt('global-h4-line-height') : 1.2;

		// Global Typography H5
		$tf_global_font_h5 = tfopt('global-h5') ? tfopt('global-h5') : 18;
		$tf_global_font_weight_h5 = tfopt('global-h5-weight') ? tfopt('global-h5-weight') : 500;
		$tf_global_font_style_h5 = tfopt('global-h5-style') ? tfopt('global-h5-style') : 'normal';$tf_global_line_height_h5 = tfopt('global-h5-line-height') ? tfopt('global-h5-line-height') : 1.2;

		// Global Typography H6
		$tf_global_font_h6 = tfopt('global-h6') ? tfopt('global-h6') : 14;
		$tf_global_font_weight_h6 = tfopt('global-h6-weight') ? tfopt('global-h6-weight') : 500;
		$tf_global_font_style_h6 = tfopt('global-h6-style') ? tfopt('global-h6-style') : 'normal';$tf_global_line_height_h6 = tfopt('global-h6-line-height') ? tfopt('global-h6-line-height') : 1.2;

		// Button
		$tf_global_button_size = tfopt('button-font-size') ? tfopt('button-font-size') : 14;
		$tf_global_button_line_height = tfopt('button-line-height') ? tfopt('button-line-height') : 1.2;

		// Template 3 Global Settings
		$tf_global_bg_clr_t3 = !empty(tf_data_types(tfopt( 'tourfic-template3-bg' ))['template3-bg']) ? tf_data_types(tfopt( 'tourfic-template3-bg' ))['template3-bg'] : '';
		$tf_global_highlight_clr_t3 = !empty(tf_data_types(tfopt('tourfic-template3-bg'))["template3-highlight"]) ? tf_data_types(tfopt('tourfic-template3-bg'))["template3-highlight"] : '';

		$output = '';
		
		// Template 1 Global CSS
		if( !empty( $tf_template1_global_reg ) ){ $output .= '
			.tf-template-global .tf-bttn-normal.bttn-primary,
			.tf-template-global .tf-archive-head .active,
			.tf-template-global .tf-item-featured .tf-features-box .tf-featur,
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::after,
			.tf-template-global .tf-itinerary-wrapper.tf-mb-50 .tf-itinerary-downloader-option,
			.tf-template-global .tf-rooms-sections .tf-rooms .tf-availability-table>thead,
			.tf-template-global .tf-hotel-location-map .tf-hotel-location-preview a i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature,
			.tf-template-global .tf-review-wrapper .tf-review-form .tf-review-form-container .tf-review-submit input[type="submit"],
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider__knob,
			.tf-template-global .tf-search-results-list #tf_posts_navigation_bar .page-numbers.current,
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider__tooltip,
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider_dark .al-range-slider__bar,
			.tf-template-global .tf-btn .btn-primary,
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table>tbody tr td .hotel-room-book,
			.tf-template-global .tf-archive-right .tf-booking-bttns button,
			.tf-hotel-services-wrap.tf-hotel-service-design-1 .tf_button_group button,
			.tf-tours-booking-deposit.tf-tours-booking-design-1 .tf_button_group button{
				background: '.$tf_template1_global_reg.' !important;
			}';
		}
		if( !empty($tf_global_font_family) && $tf_global_font_family!="Default" && $tf_global_heading_font_family!="Default" ){
			$output .= '
			.tf-container-inner,
			.tf-main-wrapper,
			.tf-container,
			#tour_room_details_qv,
			#tf-hotel-services,
			#tf-hotel-services span,
			#tf-hotel-services select,
			#tour-deposit,
			#tour-deposit .tf_button_group button,
			#tf-hotel-services .tf_button_group button,
			#tf-ask-question button,
			#tf-ask-question input,
			#tf-ask-question textarea,
			.tf-withoutpayment-booking{
				font-family: "'.$tf_global_font_family.'", sans-serif !important;
			}';
		}
		if( !empty($tf_template1_p_global_reg) ){
			$output .= '
			.tf-container-inner p,
			.tf-main-wrapper p,
			.tf-container p,
			.tf-template-3,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-overview-description .tf-full-description,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-overview-description .tf-short-description,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-highlights-wrapper .ft-highlights-details p,
			.tf-template-3 .tf-bottom-booking-bar .tf-booking-form-fields .tf-booking-form-guest-and-room .tf-booking-form-title,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block .tf-feature-block-details p,
			.tf-template-3 .tf_tours_booking .tf-field-calander .tf-field,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question .tf-question-desc,
			.tf-template-3 .tf-policies-wrapper .tf-policies p,
			#tour_room_details_qv p{
				color: '.$tf_template1_p_global_reg.'
			}';
		}
		
		if( !empty($tf_global_heading_font_family) && $tf_global_heading_font_family!="Default" && $tf_global_font_family!="Default"){
			$output .= '
			.tf-container-inner h1,
			.tf-main-wrapper h1,
			.tf-container h1,
			.tf-container-inner h2,
			.tf-main-wrapper h2,
			.tf-container h2,
			.tf-container-inner h3,
			.tf-main-wrapper h3,
			.tf-container h3,
			#tour_room_details_qv h3,
			.tf-container-inner h4,
			.tf-main-wrapper h4,
			#tour_room_details_qv h4,
			.tf-container h4,
			#tf-ask-question h4,
			.tf-container-inner h5,
			.tf-main-wrapper h5,
			.tf-container h5,
			.tf-container-inner h6,
			.tf-main-wrapper h6,
			.tf-container h6{
				font-family: "'.$tf_global_heading_font_family.'", sans-serif !important;
			}';
		}

		// Global typo for P
		if( !empty($tf_global_font_p) || !empty($tf_global_font_weight_p) || !empty($tf_global_font_style_p) || !empty($tf_global_line_height_p) ){
			$output .= '
			.tf-container-inner p,
			.tf-main-wrapper p,
			#tour_room_details_qv p,
			.tf-container p{
				font-weight: '.$tf_global_font_weight_p.' !important;
				font-size: '.$tf_global_font_p.'px !important;
				font-style: '.$tf_global_font_style_p.' !important;
				line-height: '.$tf_global_line_height_p.' !important;
			}';
		}

		// Global typo for H1
		if( !empty($tf_global_font_h1) || !empty($tf_global_font_weight_h1) || !empty($tf_global_font_style_h1) || !empty($tf_global_line_height_h1) ){
			$output .= '
			.tf-container-inner h1,
			.tf-main-wrapper h1,
			.tf-container h1{
				font-weight: '.$tf_global_font_weight_h1.' !important;
				font-size: '.$tf_global_font_h1.'px !important;
				font-style: '.$tf_global_font_style_h1.' !important;
				line-height: '.$tf_global_line_height_h1.' !important;
			}';
		}

		// Global typo for H2
		if( !empty($tf_global_font_h2) || !empty($tf_global_font_weight_h2) || !empty($tf_global_font_style_h2) || !empty($tf_global_line_height_h2) ){
			$output .= '
			.tf-container-inner h2,
			.tf-main-wrapper h2,
			.tf-container h2{
				font-weight: '.$tf_global_font_weight_h2.' !important;
				font-size: '.$tf_global_font_h2.'px !important;
				font-style: '.$tf_global_font_style_h2.' !important;
				line-height: '.$tf_global_line_height_h2.' !important;
			}';
		}

		// Global typo for H3
		if( !empty($tf_global_font_h3) || !empty($tf_global_font_weight_h3) || !empty($tf_global_font_style_h3) || !empty($tf_global_line_height_h3) ){
			$output .= '
			.tf-container-inner h3,
			.tf-main-wrapper h3,
			#tour_room_details_qv h3,
			.tf-container h3{
				font-weight: '.$tf_global_font_weight_h3.' !important;
				font-size: '.$tf_global_font_h3.'px !important;
				font-style: '.$tf_global_font_style_h3.' !important;
				line-height: '.$tf_global_line_height_h3.' !important;
			}';
		}

		// Global typo for H4
		if( !empty($tf_global_font_h4) || !empty($tf_global_font_weight_h4) || !empty($tf_global_font_style_h4) || !empty($tf_global_line_height_h4) ){
			$output .= '
			.tf-container-inner h4,
			.tf-main-wrapper h4,
			#tf-ask-question h4,
			#tour_room_details_qv h4,
			.tf-container h4{
				font-weight: '.$tf_global_font_weight_h4.' !important;
				font-size: '.$tf_global_font_h4.'px !important;
				font-style: '.$tf_global_font_style_h4.' !important;
				line-height: '.$tf_global_line_height_h4.' !important;
			}';
		}

		// Global typo for H5
		if( !empty($tf_global_font_h5) || !empty($tf_global_font_weight_h5) || !empty($tf_global_font_style_h5) || !empty($tf_global_line_height_h5) ){
			$output .= '
			.tf-container-inner h5,
			.tf-main-wrapper h5,
			.tf-container h5{
				font-weight: '.$tf_global_font_weight_h5.' !important;
				font-size: '.$tf_global_font_h5.'px !important;
				font-style: '.$tf_global_font_style_h5.' !important;
				line-height: '.$tf_global_line_height_h5.' !important;
			}';
		}

		// Global typo for H6
		if( !empty($tf_global_font_h6) || !empty($tf_global_font_weight_h6) || !empty($tf_global_font_style_h6) || !empty($tf_global_line_height_h6) ){
			$output .= '
			.tf-container-inner h6,
			.tf-main-wrapper h6,
			.tf-container h6{
				font-weight: '.$tf_global_font_weight_h6.' !important;
				font-size: '.$tf_global_font_h6.'px !important;
				font-style: '.$tf_global_font_style_h6.' !important;
				line-height: '.$tf_global_line_height_h6.' !important;
			}';
		}

		// Global Button
		if( !empty($tf_global_button_size) || !empty($tf_global_button_line_height) ){
			$output .= '
			.tf-btn-normal,
			.btn-styled{
				font-size: '.$tf_global_button_size.'px !important;
				line-height: '.$tf_global_button_line_height.' !important;
			}';
		}

		if( !empty( $tf_template1_global_reg ) ){ $output .= '
			.tf-template-global .tf-archive-head i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-details .tf-post-footer .tf-pricing span,
			.tf-template-global .tf-single-head i,
			.tf-template-global .tf-trip-info li i,
			.tf-template-global .tf-trip-feature-blocks .tf-feature-block i,
			.tf-template-global .tf-tour-details-right .tf-tour-booking-advantages li i,
			.tf-template-global .tf-ask-enquiry i,
			.tf-template-global .tf-list li i,
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item .itinerary-day,
			.tf-template-global .tf-itinerary-wrapper.tf-mrbottom-70 .tf-itinerary-downloader-option a,
			.tf-template-global .tf-review-wrapper .tf-review-data .tf-review-data-average p,
			.tf-template-global .tf-review-wrapper .tf-review-data .tf-review-all-info li,
			.tf-template-global .tf-single-head .more-hotel,
			.tf-template-global .tf-head-info .tf-dropdown-share-content h4,
			.tf-template-global .tf-head-info .tf-dropdown-share-content ul li button span,
			.tf-template-global .itinerary-downloader-right a,
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table>tbody tr td .tf-features-infos > a ,
			.upcomming-tours .tf-meta-data-price span,
			.upcomming-tours .tf-meta-location i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-details i,
			.tf-archive-design-1 .tf-archive-right #tf__booking_sidebar .widget .tf-filter a{
				color: '.$tf_template1_global_reg.' !important;
			}';
		}

		if( !empty( $tf_template1_global_reg ) ){ $output .= '
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::before,
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider__knob,
			.tf-tours-booking-deposit.tf-tours-booking-design-1 .tf_button_group button,
			.tf-template-global .tf-review-wrapper .tf-review-form .tf-review-form-container .tf-review-submit input[type="submit"]{
				border: 1px solid '.$tf_template1_global_reg.';
				border-color: '.$tf_template1_global_reg.' !important;
			}
			.tf-aq-outer span.close-aq {background: '.$tf_template1_global_reg.' !important;}
			.tf-aq-field .btn-styled {background: '.$tf_template1_global_reg.' !important;}';

		}

		// Common CSS
		if( $tf_primary_color_reg  ) { $output .= '
			.tf_button, 
			.tf-btn-flip:before, 
			.tf-btn-flip, 
			.btn-styled, 
			.tf-review-form-container .tf-review-submit input[type="submit"], 
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn,
			.tf-bttn-normal.bttn-primary, 
			.tf-bttn-normal.bttn-secondary, 
			.tf-template-global .tf-archive-head .active, 
			.tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit,
			.tf-template-3 .tf-modify-search-btn{
				color: '.$tf_primary_color_reg.' !important;
			}
			.tf-ask-question div i:before {
				color: '.$tf_primary_color_reg.' !important;
			}
		'; }
		if( $tf_primary_color_hov  ) { $output .= '
			.tf_button:hover, 
			.btn-styled:hover, 
			.tf-btn-flip:after, 
			.tf-review-form-container .tf-review-submit input[type="submit"]:hover, 
			.tf-bttn-normal.bttn-primary:hover, 
			.tf-bttn-normal.bttn-secondary:hover,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a:hover,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability:hover,
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn:hover,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel:hover,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button:hover,
			.tf-template-3 .tf-modify-search-btn:hover {
				color: '.$tf_primary_color_hov.' !important;
			}
		'; }
		if( $tf_primary_bg_color_reg  ) { $output .= '
			.tf_button, 
			.tf-btn-flip:before, 
			.tf-btn-flip:after, 
			.btn-styled, 
			.tf-review-form-container .tf-review-submit input[type="submit"], 
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn,
			.tf-bttn-normal.bttn-primary, 
			.tf-bttn-normal.bttn-secondary, 
			.tf-template-global .tf-archive-head .active, 
			.tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit,
			.tf-template-3 .tf-modify-search-btn {
				background: '.$tf_primary_bg_color_reg.' !important;
			}
			.tf_button, 
			.btn-styled, 
			.tf-review-form-container .tf-review-submit input[type="submit"],
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a {
				border-color: '.$tf_primary_bg_color_reg.' !important;
			}
		'; }
		if( $tf_primary_bg_color_hov  ) { $output .= '
			.tf_button:hover, 
			.btn-styled:hover, 
			.tf-review-form-container 
			.tf-review-submit input[type="submit"]:hover, 
			.tf-bttn-normal.bttn-primary:hover, 
			.tf-bttn-normal.bttn-secondary:hover,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a:hover,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability:hover,
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn:hover,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel:hover,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button:hover,
			.tf-template-3 .tf-modify-search-btn:hover {
				background: '.$tf_primary_bg_color_hov.' !important;
			}
			.tf_button:hover, 
			.btn-styled:hover, .tf-review-form-container .tf-review-submit input[type="submit"]:hover,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a:hover{
				border-color: '.$tf_primary_bg_color_hov.';
			}
		'; }
		if( $tf_sidebar_gradient_one_reg && $tf_sidebar_gradient_two_reg  ) { $output .= '
			.tf_booking-widget, .tf-tour-details-right .tf-tour-booking-box, 
			.tf-template-global .tf-box-wrapper.tf-box,
			.tf-template-3 .tf-booking-form-wrapper,
			.tf-template-3 .tf-search-date-wrapper.tf-single-widgets {background: linear-gradient(to bottom, '.$tf_sidebar_gradient_one_reg.' 0, '.$tf_sidebar_gradient_two_reg.' 100%);}
		'; }
		if( $tf_faq_color OR $tf_faq_icon_color OR $tf_faq_border_color ) { $output .= '
			.tf-faq-title h4,
			.tf-single-page .tf-faq-wrapper .tf-faq-single-inner h3,
			.tf-template-global .tf-faq-wrapper .tf-faq-single-inner .tf-faq-collaps h4,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question .tf-faq-head h3 {
				color: '.$tf_faq_color.';
			}
			#tf-faq-item,
			.tf-single-page .tf-faq-wrapper .tf-faq-inner .tf-faq-single {
				border-color: '.$tf_faq_border_color.';
			}
			#tf-faq-item .arrow,
			.tf-single-page .tf-faq-wrapper .tf-faq-single-inner .tf-faq-collaps .faq-icon i.fa-plus,
			.tf-single-page .tf-faq-wrapper .tf-faq-inner .active .tf-faq-single-inner .tf-faq-collaps .faq-icon i.fa-minus,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question i {
				color: '.$tf_faq_icon_color.';
			}
		'; }

		if( $tf_faq_border_color ) { $output .= '
			.tf-hotel-design-1 .tf-hotel-faqs-section .tf-hotel-faqs .tf-faq-wrapper .tf-faq-single .tf-faq-single-inner{
				border: 1px solid '.$tf_faq_border_color.';
			}
		'; }

		if( $tf_review_bg_color OR $tf_review_color OR $tf_param_single_bg_color OR $tf_param_bg_color OR $tf_rating_bg_color OR $tf_rating_color) { $output .= '
			.tf-single-review .tf-single-details,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item {
				background: '.$tf_review_bg_color.';
			}
			.tf-single-review .tf-review-details .tf-name, 
			.tf-single-review .tf-review-details .tf-date, 
			.tf-single-review .tf-review-details .tf-rating-stars, 
			.tf-single-review .tf-review-details .tf-rating-stars i, 
			.tf-single-review .tf-review-details .tf-description p,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-author-name h3,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-message p,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-ratings i,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li i,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item .tf-reviews-text h3,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item .tf-reviews-text p,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item .tf-reviews-text span{
				color: '.$tf_review_color.' !important;
			}
			.tf-review-container .tf-review-progress-bar .tf-single .tf-p-bar,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-data-features .tf-progress-bar,
			.tf-template-global .tf-review-wrapper .tf-review-data .tf-review-data-features .percent-progress,
			.tf-template-3 .tf-single-widgets .tf-review-data-features span.percent-progress {
				background: '.$tf_param_single_bg_color.';
			}
			.tf-review-container .tf-review-progress-bar,
			.tf-single-page .tf-review-wrapper .tf-review-data,
			.tf-template-global .tf-review-wrapper .tf-review-data.tf-box .tf-review-data-features{
				background: '.$tf_param_bg_color.';
				border-color: '.$tf_param_bg_color.';
			}
			.tf-review-container .tf-review-progress-bar .tf-single .tf-text, .tf-review-container .tf-review-progress-bar .tf-single .tf-p-b-rating,
			.tf-single-page .tf-review-wrapper .tf-review-data p,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-all-info li,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-all-info li i {
				color: '.$tf_param_txt_color.';
			}
			.tf-review-container .tf-total-review .tf-total-average div, .tf-archive-rating, .tf-single-page .tf-review-wrapper .tf-review-data .tf-review-data-average p,
			.tf-template-3 .tf-review-data .tf-review-data-average {
				background: '.$tf_rating_bg_color.'!important;
				color: '.$tf_rating_color.'!important;
			}
		'; }
		if( $tf_param_bg_color ) { $output .= '
			.tf-template-3 .tf-single-widgets .tf-review-data-features{
				background: '.$tf_param_bg_color.';
				border-color: '.$tf_param_bg_color.';
				padding: 32px;
				border-radius: 5px;
				margin-top: 10px;
			}
		'; }
		if( $tf_rating_bg_color || $tf_rating_color) { $output .= '
			.tf-template-3 .tf-review-data .tf-review-data-average {
				background: '.$tf_rating_bg_color.'!important;
				color: '.$tf_rating_color.'!important;
				border-radius: 5px;
				padding: 10px;
			}
			.tf-template-3 .tf-review-data .tf-review-data-average span{
				color: '.$tf_rating_color.'!important;
			}
		'; }
		if( $tf_review_bg_color) { $output .= '
			.tf-single-page .tf-review-reply .tf-review-reply-data {
				background: '.$tf_review_bg_color.';
				padding: 20px;
				border-radius: 5px;
				margin: 10px 0px;
			}
			.tf-single-page .tf-review-reply .tf-review-details h3,
			.tf-single-page .tf-review-reply .tf-review-details p,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li i,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-ratings,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-ratings i{
				color: '.$tf_review_color.' !important;
			}
		'; }

		// Template 3 Global Colors
		if(!empty($tf_global_bg_clr_t3)) {
			$output .= '
			.tf-template-3 .tf-related-tours .tf-slider-item .tf-meta-info,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question.tf-active,
			.tf-template-3 .tf_tours_booking .tf-field-calander .tf-field,
			.tf-template-3 .tf-search-date-wrapper .acr-select input[type=tel],
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room,
			.tf-template-3 {
				background: '.$tf_global_bg_clr_t3.' !important;
			}
			';
		}

		if(!empty($tf_global_highlight_clr_t3)) {
			$output .= '
			.tf-template-3 .tf-booking-form-wrapper,
			.tf-template-3 .tf-available-rooms-wrapper,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-details-menu,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-right.tf-archive-right,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-highlights-wrapper,
			.tf-template-3 .tf-bottom-booking-bar,
			.tf-template-3 .tf-related-tours,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block,
			.tf-template-3 .tf-search-date-wrapper,
			.tf-template-3 button.tf-review-open.button, .tf-template-3 .tf-reting-field button,
			.tf-template-3 .tf-review-form-wrapper,
			.tf-template-3 .tf-section {
				background: '.$tf_global_highlight_clr_t3.' !important;
			}
			';
		}
		
		wp_add_inline_style( 'tf-app-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_custom_css', 99999 );

if( !function_exists( 'tf_hotel_css' ) ){
	function tf_hotel_css(){ 
		// Store as PHP variables
		// Hotel CSS
		$tf_hotel_type_color = !empty(tf_data_types(tfopt( 'tourfic-hotel-type-bg-color' ))['regular']) ? tf_data_types(tfopt( 'tourfic-hotel-type-bg-color' ))['regular'] : '';
		$tf_hotel_type_bg_color = !empty(tf_data_types(tfopt( 'tourfic-hotel-type-bg-color' ))['hover']) ? tf_data_types(tfopt( 'tourfic-hotel-type-bg-color' ))['hover'] : '';
		$tf_share_color_reg = !empty(tf_data_types(tfopt( 'tourfic-hotel-share-icon' ))['regular']) ? tf_data_types(tfopt( 'tourfic-hotel-share-icon' ))['regular'] : '';
		$tf_share_color_hov = !empty(tf_data_types(tfopt( 'tourfic-hotel-share-icon' ))['hover']) ? tf_data_types(tfopt( 'tourfic-hotel-share-icon' ))['hover'] : '';
		$tf_gradient_one_reg = !empty(tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_one_reg']) ? tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_one_reg'] : '';
		$tf_gradient_two_reg = !empty(tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_two_reg']) ? tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_two_reg'] : '';
		$tf_gradient_one_hov = !empty(tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_one_hov']) ? tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_one_hov'] : '';
		$tf_gradient_two_hov = !empty(tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_two_hov']) ? tf_data_types(tfopt( 'tourfic-hotel-map-button' ))['gradient_two_hov'] : '';
		$tf_map_text_color = !empty(tf_data_types(tfopt( 'tourfic-hotel-map-button-text' ))['regular']) ? tf_data_types(tfopt( 'tourfic-hotel-map-button-text' ))['regular'] : '';
		$tf_hotel_features = !empty(tf_data_types(tfopt( 'tourfic-hotel-features-color' ))['regular']) ? tf_data_types(tfopt( 'tourfic-hotel-features-color' ))['regular'] : '';
		$tf_hotel_table_color = !empty(tf_data_types(tfopt( 'tourfic-hotel-table-style' ))['table_color']) ? tf_data_types(tfopt( 'tourfic-hotel-table-style' ))['table_color'] : '';
		$tf_hotel_table_bg_color = !empty(tf_data_types(tfopt( 'tourfic-hotel-table-style' ))['table_bg_color']) ? tf_data_types(tfopt( 'tourfic-hotel-table-style' ))['table_bg_color'] : '';
		$tf_hotel_table_border_color = !empty(tf_data_types(tfopt( 'tourfic-hotel-table-style' ))['table_border_color']) ? tf_data_types(tfopt( 'tourfic-hotel-table-style' ))['table_border_color'] : '';
		
		$output = '';
		
		// Hotel CSS
		if( $tf_hotel_type_color  ) { $output .= '
			.tf-title-left span.post-type {color: '.$tf_hotel_type_color.';}
		'; }
		if( $tf_hotel_type_bg_color  ) { $output .= '
			.tf-title-left span.post-type {background: '.$tf_hotel_type_bg_color.';}
		'; }
		if( $tf_share_color_reg  ) { $output .= '
			.tf-share .share-toggle i, .tf-single-page .tf-section.tf-single-head .tf-share > a i,
			.tf-template-3 .tf-hero-section-wrap .tf-container .tf-hero-content .tf-wish-and-share a.share-toggle i {color: '.$tf_share_color_reg.' !important;}
		'; }
		if( $tf_share_color_hov  ) { $output .= '
			.tf-share .share-toggle i:hover, .tf-single-page .tf-section.tf-single-head .tf-share > a i:hover,
			.tf-template-3 .tf-hero-section-wrap .tf-container .tf-hero-content .tf-wish-and-share a.share-toggle:hover i {color: '.$tf_share_color_hov.' !important;}
		'; }
		if( $tf_gradient_one_reg && $tf_gradient_two_reg  ) { $output .= '
			.show-on-map .btn-styled, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i {background: linear-gradient(to bottom, '.$tf_gradient_one_reg.' 0, '.$tf_gradient_two_reg.' 100%) !important;}
		'; }
		if( $tf_gradient_one_hov && $tf_gradient_two_hov  ) { $output .= '
			.show-on-map .btn-styled:hover, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i:hover {background: linear-gradient(to bottom, '.$tf_gradient_one_hov.' 0, '.$tf_gradient_two_hov.' 100%) !important;}
		'; }
		if( $tf_map_text_color  ) { $output .= '
			.show-on-map .btn-styled, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i {color: '.$tf_map_text_color.';}
		'; }
		if( $tf_hotel_features  ) { $output .= '
			.tf_features i, 
			.tf-archive-desc i, 
			.tf-single-page .tf-hotel-single-features ul li i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-details .tf-archive-features i,
			.tf-hotel-design-1 .tf-rooms .tf-features-infos ul li,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-overview-popular-facilities > ul li i {color: '.$tf_hotel_features.'!important;}
		'; }
		if( $tf_hotel_table_color OR $tf_hotel_table_bg_color ) { $output .= '
			.availability-table thead{
				color: '.$tf_hotel_table_color.';
				background: '.$tf_hotel_table_bg_color.';
			}
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table>thead tr th{
				background: '.$tf_hotel_table_bg_color.';
				color: '.$tf_hotel_table_color.';
				border-radius: 0px;
			}
		'; }
		if( $tf_hotel_table_color ) { $output .= '
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table>thead tr th{
				color: '.$tf_hotel_table_color.';
			}
		'; }
		if( $tf_hotel_table_border_color  ) { $output .= '
			.availability-table td, .availability-table th, .availability-table td.reserve, .tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table>tr>td {border-color: '.$tf_hotel_table_border_color.';}
		'; }

		if( $tf_hotel_table_border_color  ) { $output .= '
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table {border: 1px solid '.$tf_hotel_table_border_color.'; border-collapse: inherit;}
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table thead tr th{
				border-color: '.$tf_hotel_table_border_color.';
			}
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table tbody tr td{
				border-color: '.$tf_hotel_table_border_color.';
			}
		'; }
		if( $tf_hotel_table_border_color  ) { $output .= '
			
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room{
				border: 1px solid '.$tf_hotel_table_border_color.';
			}
		'; }
		
		wp_add_inline_style( 'tf-app-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_hotel_css', 99999 );

if( !function_exists( 'tf_tour_css' ) ){
	function tf_tour_css(){
		// Store as PHP variables
		// Tour CSS
		$tf_tour_sale_price = !empty(tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['sale_price']) ? tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['sale_price'] : '';
		$tf_tour_org_price = !empty(tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['org_price']) ? tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['org_price'] : '';
		$tf_tour_tab_text = !empty(tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['tab_text']) ? tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['tab_text'] : '';
		$tf_tour_tab_bg = !empty(tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['tab_bg']) ? tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['tab_bg'] : '';
		$tf_tour_active_tab_text = !empty(tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['active_tab_text']) ? tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['active_tab_text'] : '';
		$tf_tour_active_tab_bg = !empty(tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['active_tab_bg']) ? tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['active_tab_bg'] : '';
		$tf_tour_tab_border = !empty(tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['tab_border']) ? tf_data_types(tfopt( 'tourfic-tour-pricing-color' ))['tab_border'] : '';
		$tf_tour_icon_color = !empty(tf_data_types(tfopt( 'tourfic-tour-info-color' ))['icon_color']) ? tf_data_types(tfopt( 'tourfic-tour-info-color' ))['icon_color'] : '';
		$tf_tour_heading_color = !empty(tf_data_types(tfopt( 'tourfic-tour-info-color' ))['heading_color']) ? tf_data_types(tfopt( 'tourfic-tour-info-color' ))['heading_color'] : '';
		$tf_tour_text_color = !empty(tf_data_types(tfopt( 'tourfic-tour-info-color' ))['text_color']) ? tf_data_types(tfopt( 'tourfic-tour-info-color' ))['text_color'] : '';
		$tf_tour_bg_one = !empty(tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_one']) ? tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_one'] : '';
		$tf_tour_bg_two = !empty(tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_two']) ? tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_two'] : '';
		$tf_tour_bg_three = !empty(tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_three']) ? tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_three'] : '';
		$tf_tour_bg_four = !empty(tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_four']) ? tf_data_types(tfopt( 'tourfic-tour-info-color' ))['bg_four'] : '';
		$tf_tour_btn_col = !empty(tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_col']) ? tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_col'] : '';
		$tf_tour_btn_bg = !empty(tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_bg']) ? tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_bg'] : '';
		$tf_tour_btn_hov_bg = !empty(tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_hov_bg']) ? tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_hov_bg'] : '';
		$tf_tour_btn_hov_col = !empty(tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_hov_col']) ? tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['btn_hov_col'] : '';
		$tf_tour_form_background = !empty(tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['form_background']) ? tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['form_background'] : '';
		$tf_tour_form_border = !empty(tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['form_border']) ? tf_data_types(tfopt( 'tourfic-tour-sticky-booking' ))['form_border'] : '';
		$tf_inc_gradient_one_reg = !empty(tf_data_types(tfopt( 'tourfic-include-exclude' ))['gradient_one_reg']) ? tf_data_types(tfopt( 'tourfic-include-exclude' ))['gradient_one_reg'] : '';
		$tf_inc_gradient_two_reg = !empty(tf_data_types(tfopt( 'tourfic-include-exclude' ))['gradient_two_reg']) ? tf_data_types(tfopt( 'tourfic-include-exclude' ))['gradient_two_reg'] : '';
		$tf_inc_heading_color = !empty(tf_data_types(tfopt( 'tourfic-include-exclude' ))['heading_color']) ? tf_data_types(tfopt( 'tourfic-include-exclude' ))['heading_color'] : '';
		$tf_inc_text_color = !empty(tf_data_types(tfopt( 'tourfic-include-exclude' ))['text_color']) ? tf_data_types(tfopt( 'tourfic-include-exclude' ))['text_color'] : '';
		$tf_itin_time_day_txt = !empty(tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['time_day_txt']) ? tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['time_day_txt'] : '';
		$tf_itin_time_day_bg = !empty(tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['time_day_bg']) ? tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['time_day_bg'] : '';
		$tf_itin_heading_color = !empty(tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['heading_color']) ? tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['heading_color'] : '';
		$tf_itin_text_color = !empty(tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['text_color']) ? tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['text_color'] : '';
		$tf_itin_bg_color = !empty(tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['bg_color']) ? tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['bg_color'] : '';
		$tf_itin_icon_color = !empty(tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['icon_color']) ? tf_data_types(tfopt( 'tourfic-tour-itinerary' ))['icon_color'] : '';
		
		$output = '';
		
		// Tour CSS
		if( $tf_tour_sale_price OR $tf_tour_org_price OR $tf_tour_tab_text OR $tf_tour_tab_bg OR $tf_tour_tab_border) { $output .= '
			.tf-single-tour-pricing .tf-price span.sale-price,
			.tf-single-page .tf-trip-info .tf-trip-pricing .tf-price-amount {
				color: '.$tf_tour_sale_price.';
			}
			.tf-single-tour-pricing .tf-price {
				color: '.$tf_tour_org_price.';
			}
			.tf-single-tour-pricing .tf-price-tab li,
			.tf-single-page .tf-trip-info .person-info,
			.tf-single-page .tf-trip-info .person-info p {
				color: '.$tf_tour_tab_text.';
			}
			.tf-single-tour-pricing .tf-price-tab li,
			.tf-single-page .tf-trip-info .person-info {
				background: '.$tf_tour_tab_bg.';
			}
			.tf-single-tour-pricing .tf-price-tab li.active,
			.tf-single-page .tf-trip-info .person-info.active {
				color: '.$tf_tour_active_tab_text.';
			}
			.tf-single-tour-pricing .tf-price-tab li.active,
			.tf-single-page .tf-trip-info .person-info.active {
				background: '.$tf_tour_active_tab_bg.';
			}
			.tf-single-tour-pricing .tf-price-tab, .tf-single-tour-pricing .tf-price-tab li:nth-child(2), .tf-single-tour-pricing .tf-price-tab li:nth-child(3) {
				border-color: '.$tf_tour_tab_border.';
			}
		'; }

		if( $tf_tour_icon_color OR $tf_tour_heading_color OR $tf_tour_text_color OR $tf_tour_bg_one OR $tf_tour_bg_two OR $tf_tour_bg_three OR $tf_tour_bg_four) { $output .= '
			.tf-single-square-block i,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block i,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block i {
				color: '.$tf_tour_icon_color.';
			}
			.tf-single-square-block h4,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block h3,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block .tf-feature-block-details h5 {
				color: '.$tf_tour_heading_color.';
			}
			.tf-single-square-block p,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block p,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block .tf-feature-block-details p {
				color: '.$tf_tour_text_color.';
			}
			.tf-single-square-block.first,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-first,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(1) {
				background: '.$tf_tour_bg_one.';
			}
			.tf-single-square-block.second,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-second,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(2) {
				background: '.$tf_tour_bg_two.';
			}
			.tf-single-square-block.third,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-third,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(3) {
				background: '.$tf_tour_bg_three.';
			}
			.tf-single-square-block.fourth,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-tourth,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(4) {
				background: '.$tf_tour_bg_four.';
			}
		'; }

		if( $tf_tour_btn_col OR $tf_tour_btn_bg OR $tf_tour_btn_hov_col OR $tf_tour_btn_hov_bg OR $tf_tour_form_background OR $tf_tour_form_border) { $output .= '
			.tf-tours-fixed .btn-styled {
				color: '.$tf_tour_btn_col.';
				border-color: '.$tf_tour_btn_bg.';
				background: '.$tf_tour_btn_bg.';
			}
			.tf-tours-fixed .btn-styled:hover {
				color: '.$tf_tour_btn_hov_col.';
				border-color: '.$tf_tour_btn_hov_bg.';
				background: '.$tf_tour_btn_hov_bg.';
			}
			.tf-tour-booking-wrap.tf-tours-fixed {
				background: '.$tf_tour_form_background.';
				border-color: '.$tf_tour_form_border.';
			}
		'; }

		if( $tf_inc_gradient_one_reg OR $tf_inc_gradient_two_reg OR $tf_inc_heading_color OR $tf_inc_text_color) { $output .= '
			.tf-include-section, 
			.tf-exclude-section, 
			.tf-single-page .tf-inex-wrapper .tf-inex,
			.tf-template-3 .tf-include-exclude-wrapper .tf-include-exclude-innter > div{
				background-image: linear-gradient(to right, '.$tf_inc_gradient_one_reg.', '.$tf_inc_gradient_two_reg.');
				color: '.$tf_inc_text_color.';
			}
			.tf-inc-exc-content h4,
			.tf-single-page .tf-inex-wrapper .tf-inex h3 {
				color: '.$tf_inc_heading_color.';
			}
		'; }
		if( $tf_inc_gradient_one_reg OR $tf_inc_gradient_two_reg ) { $output .= '
			.tf-template-3 .tf-include-exclude-wrapper .tf-include-exclude-innter > div{
				padding: 15px;
			}
		'; }
		if( $tf_itin_time_day_txt OR $tf_itin_time_day_bg OR $tf_itin_heading_color OR $tf_itin_text_color OR $tf_itin_bg_color OR $tf_itin_icon_color) { $output .= '
			.tf-travel-time span,
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item .itinerary-day,
			.tf-template-3 .tf-itinerary-wrapper span.tf-itinerary-time {
				color: '.$tf_itin_time_day_txt.' !important;
			}
			.tf-travel-time,
			.tf-template-3 .tf-itinerary-wrapper span.tf-itinerary-time {
				background: '.$tf_itin_time_day_bg.';
			}
			.tf-accordion-head h4, 
			.tf-accordion-head h4:hover,
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item h3,
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-itinerary-box .tf-single-itinerary-item h4,
			.tf-template-3 .tf-itinerary-wrapper .tf-itinerary-title h4 {
				color: '.$tf_itin_heading_color.';
			}
			.tf-travel-desc,
			.tf-single-page .tf-itinerary-content-details p {
				color: '.$tf_itin_text_color.';
			}
			#tf-accordion-wrapper .tf-accordion-content, 
			#tf-accordion-wrapper .tf-accordion-head, 
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-itinerary-box,
			.tf-template-3 .tf-itinerary-wrapper .tf-single-itinerary {
				background: '.$tf_itin_bg_color.';
			}
			#tf-accordion-wrapper .arrow-animate, #tf-accordion-wrapper .arrow,
			.tf-template-3 .tf-itinerary-wrapper .tf-itinerary-title i {
				color: '.$tf_itin_icon_color.';
			}
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::before,
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-single-itinerary-item .accordion-checke::before {
				border: 1px solid '.$tf_itin_icon_color.' !important;
			}
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::after {
				background: '.$tf_itin_icon_color.' !important;
			}
		'; }

		wp_add_inline_style( 'tf-app-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_tour_css', 99999 );

/*
 * Apartment Custom CSS
 * @author: Foysal
 */
if ( ! function_exists( 'tf_apartment_css' ) ) {
	function tf_apartment_css() {
		//amenities
		$amenities_bg           = ! empty( tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_bg'] ) ? tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_bg'] : '';
		$amenities_border_color = ! empty( tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_border_color'] ) ? tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_border_color'] : '';
		$amenities_text         = ! empty( tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_text'] ) ? tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_text'] : '';
		$amenities_icon         = ! empty( tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_icon'] ) ? tf_data_types( tfopt( 'apartment-amenities' ) )['amenities_icon'] : '';
		//features
		$features_bg           = ! empty( tf_data_types( tfopt( 'apartment-features' ) )['features_bg'] ) ? tf_data_types( tfopt( 'apartment-features' ) )['features_bg'] : '';
		$features_border_color = ! empty( tf_data_types( tfopt( 'apartment-features' ) )['features_border_color'] ) ? tf_data_types( tfopt( 'apartment-features' ) )['features_border_color'] : '';
		$features_text         = ! empty( tf_data_types( tfopt( 'apartment-features' ) )['features_text'] ) ? tf_data_types( tfopt( 'apartment-features' ) )['features_text'] : '';
		$features_icon         = ! empty( tf_data_types( tfopt( 'apartment-features' ) )['features_icon'] ) ? tf_data_types( tfopt( 'apartment-features' ) )['features_icon'] : '';
		//booking form
		$form_heading_color = ! empty( tf_data_types( tfopt( 'booking-form-design' ) )['form_heading_color'] ) ? tf_data_types( tfopt( 'booking-form-design' ) )['form_heading_color'] : '';
		$form_bg            = ! empty( tf_data_types( tfopt( 'booking-form-design' ) )['form_bg'] ) ? tf_data_types( tfopt( 'booking-form-design' ) )['form_bg'] : '';
		$form_border_color  = ! empty( tf_data_types( tfopt( 'booking-form-design' ) )['form_border_color'] ) ? tf_data_types( tfopt( 'booking-form-design' ) )['form_border_color'] : '';
		$form_text          = ! empty( tf_data_types( tfopt( 'booking-form-design' ) )['form_text'] ) ? tf_data_types( tfopt( 'booking-form-design' ) )['form_text'] : '';
		$form_fields_bg     = ! empty( tf_data_types( tfopt( 'booking-form-design' ) )['form_fields_bg'] ) ? tf_data_types( tfopt( 'booking-form-design' ) )['form_fields_bg'] : '';
		$form_fields_border = ! empty( tf_data_types( tfopt( 'booking-form-design' ) )['form_fields_border'] ) ? tf_data_types( tfopt( 'booking-form-design' ) )['form_fields_border'] : '';
		$form_fields_text   = ! empty( tf_data_types( tfopt( 'booking-form-design' ) )['form_fields_text'] ) ? tf_data_types( tfopt( 'booking-form-design' ) )['form_fields_text'] : '';
		//Host
		$host_heading_color = ! empty( tf_data_types( tfopt( 'host-card-design' ) )['host_heading_color'] ) ? tf_data_types( tfopt( 'host-card-design' ) )['host_heading_color'] : '';
		$host_bg            = ! empty( tf_data_types( tfopt( 'host-card-design' ) )['host_bg'] ) ? tf_data_types( tfopt( 'host-card-design' ) )['host_bg'] : '';
		$host_border_color  = ! empty( tf_data_types( tfopt( 'host-card-design' ) )['host_border_color'] ) ? tf_data_types( tfopt( 'host-card-design' ) )['host_border_color'] : '';
		$host_text          = ! empty( tf_data_types( tfopt( 'host-card-design' ) )['host_text'] ) ? tf_data_types( tfopt( 'host-card-design' ) )['host_text'] : '';

		$output = '';
		if ( $amenities_bg || $amenities_border_color || $amenities_text || $amenities_icon ) {
			$output .= '
			.tf-apartment .apartment-amenities ul {
				background-color: ' . $amenities_bg . ';
				border-color: ' . $amenities_border_color . ';
			}
			.tf-apartment .apartment-amenities ul li {
				color: ' . $amenities_text . ';
			}
			.tf-apartment .apartment-amenities ul li i {
				color: ' . $amenities_icon . ';
			}
		';
		}
		if ( $features_bg || $features_border_color || $features_text || $features_icon ) {
			$output .= '
			.tf-apartment .key-features ul {
				background-color: ' . $features_bg . ';
				border-color: ' . $features_border_color . ';
			}
			.tf-apartment .key-features ul li {
				color: ' . $features_text . ';
			}
			.tf-apartment .key-features ul li i {
				color: ' . $features_icon . ';
			}
		';
		}
		if ( $form_heading_color || $form_bg || $form_border_color || $form_text || $form_fields_bg || $form_fields_border || $form_fields_text ) {
			$output .= '
			#tf-apartment-booking h4 {
				color: ' . $form_heading_color . ';
			}
			#tf-apartment-booking {
				background-color: ' . $form_bg . ';
				border-color: ' . $form_border_color . ';
			}
			#tf-apartment-booking .tf-apartment-form-header .tf-apartment-price-per-night span:not(.woocommerce-Price-amount,.woocommerce-Price-currencySymbol),
			#tf-apartment-booking .tf-apartment-form-header .tf-apartment-price-per-night span,
			#tf-apartment-booking .tf-apartment-form-header .tf-single-rating {
				color: ' . $form_text . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields{
				background-color: ' . $form_fields_bg . ';
				border-color: ' . $form_fields_border . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields .tf-apartment-guest-row{
				border-top-color: ' . $form_fields_border . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields .tf_booking-dates .tf-check-in-out-date #check-in-out-date::-webkit-input-placeholder{
				color: ' . $form_fields_text . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields .tf-apartment-guest-row .tf-label,
			.adults-text, .person-sep, .child-text, .room-text, .infant-text,
			#tf-apartment-booking .tf-apartment-form-fields .tf_booking-dates .tf-check-in-out-date #check-in-out-date,
			#tf-apartment-booking .tf-apartment-form-fields .tf_booking-dates .tf-check-in-out-date .tf-label{
				color: ' . $form_fields_text . ';
			}
			';
		}
		if ( $host_heading_color || $host_bg || $host_border_color || $host_text ) {
			$output .= '
			.host-details {
				background-color: ' . $host_bg . ';
				border-color: ' . $host_border_color . ';
			}
			.host-details .host-meta h4 {
				color: ' . $host_heading_color . ';
			}
			.host-details,
			.host-details .host-bottom p,
			.tf-host-rating-wrapper h6{
				color: ' . $host_text . ';
			}
			';
		}

		wp_add_inline_style( 'tf-app-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_apartment_css', 99999 );

