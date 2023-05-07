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
		$tf_global_font_family = tfopt('global-body-fonts-family') ? tfopt('global-body-fonts-family') : 'Jost';
		$tf_global_heading_font_family = tfopt('global-heading-fonts-family') ? tfopt('global-heading-fonts-family') : 'Jost';

		// Global Typography P
		$tf_global_font_p = tfopt('global-p') ? tfopt('global-p') : 14;
		$tf_global_font_weight_p = tfopt('global-p-weight') ? tfopt('global-p-weight') : 400;
		$tf_global_font_style_p = tfopt('global-p-style') ? tfopt('global-p-style') : 'normal';

		// Global Typography H1
		$tf_global_font_h1 = tfopt('global-h1') ? tfopt('global-h1') : 38;
		$tf_global_font_weight_h1 = tfopt('global-h1-weight') ? tfopt('global-h1-weight') : 500;
		$tf_global_font_style_h1 = tfopt('global-h1-style') ? tfopt('global-h1-style') : 'normal';

		// Global Typography H2
		$tf_global_font_h2 = tfopt('global-h2') ? tfopt('global-h2') : 30;
		$tf_global_font_weight_h2 = tfopt('global-h2-weight') ? tfopt('global-h2-weight') : 500;
		$tf_global_font_style_h2 = tfopt('global-h2-style') ? tfopt('global-h2-style') : 'normal';

		// Global Typography H3
		$tf_global_font_h3 = tfopt('global-h3') ? tfopt('global-h3') : 24;
		$tf_global_font_weight_h3 = tfopt('global-h3-weight') ? tfopt('global-h3-weight') : 500;
		$tf_global_font_style_h3 = tfopt('global-h3-style') ? tfopt('global-h3-style') : 'normal';

		// Global Typography H4
		$tf_global_font_h4 = tfopt('global-h4') ? tfopt('global-h4') : 20;
		$tf_global_font_weight_h4 = tfopt('global-h4-weight') ? tfopt('global-h4-weight') : 500;
		$tf_global_font_style_h4 = tfopt('global-h4-style') ? tfopt('global-h4-style') : 'normal';

		// Global Typography H5
		$tf_global_font_h5 = tfopt('global-h5') ? tfopt('global-h5') : 18;
		$tf_global_font_weight_h5 = tfopt('global-h5-weight') ? tfopt('global-h5-weight') : 500;
		$tf_global_font_style_h5 = tfopt('global-h5-style') ? tfopt('global-h5-style') : 'normal';

		// Global Typography H6
		$tf_global_font_h6 = tfopt('global-h6') ? tfopt('global-h6') : 14;
		$tf_global_font_weight_h6 = tfopt('global-h6-weight') ? tfopt('global-h6-weight') : 500;
		$tf_global_font_style_h6 = tfopt('global-h6-style') ? tfopt('global-h6-style') : 'normal';

		$output = '';
		
		// Template 1 Global CSS
		if( !empty( $tf_template1_global_reg ) ){ $output .= '
			.tf-template-global .tf-bttn-normal.bttn-primary,
			.tf-template-global .tf-archive-head .active,
			.tf-template-global .tf-item-featured .tf-features-box .tf-featur,
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::after,
			.tf-template-global .tf-itinerary-wrapper.tf-mrbottom-70 .tf-itinerary-downloader-option,
			.tf-template-global .tf-review-wrapper .tf-review-data .tf-review-data-features .percent-progress,
			.tf-template-global .tf-rooms-sections .tf-rooms .tf-availability-table thead,
			.tf-template-global .tf-hotel-location-map .tf-hotel-location-preview a i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature,
			.tf-template-global .tf-review-wrapper .tf-review-form .tf-review-form-container .tf-review-submit input[type="submit"] {
				background: '.$tf_template1_global_reg.';
			}';
		}
		if( !empty($tf_global_font_family) ){
			$output .= '
			.tf-container-inner,
			.tf-main-wrapper{
				font-family: '.$tf_global_font_family.'
			}';
		}
		if( !empty($tf_global_heading_font_family) ){
			$output .= '
			h1,h2,h3,h4,h5,h6{
				font-family: '.$tf_global_heading_font_family.'
			}';
		}

		// Global typo for P
		if( !empty($tf_global_font_p) || !empty($tf_global_font_weight_p) || !empty($tf_global_font_style_p) ){
			$output .= '
			p{
				font-weight: '.$tf_global_font_weight_p.' !important;
				font-size: '.$tf_global_font_p.'px !important;
				font-style: '.$tf_global_font_style_p.' !important
			}';
		}

		// Global typo for H1
		if( !empty($tf_global_font_h1) || !empty($tf_global_font_weight_h1) || !empty($tf_global_font_style_h1) ){
			$output .= '
			h1{
				font-weight: '.$tf_global_font_weight_h1.' !important;
				font-size: '.$tf_global_font_h1.'px !important;
				font-style: '.$tf_global_font_style_h1.' !important
			}';
		}

		// Global typo for H2
		if( !empty($tf_global_font_h2) || !empty($tf_global_font_weight_h2) || !empty($tf_global_font_style_h2) ){
			$output .= '
			h2{
				font-weight: '.$tf_global_font_weight_h2.' !important;
				font-size: '.$tf_global_font_h2.'px !important;
				font-style: '.$tf_global_font_style_h2.' !important
			}';
		}

		// Global typo for H3
		if( !empty($tf_global_font_h3) || !empty($tf_global_font_weight_h3) || !empty($tf_global_font_style_h3) ){
			$output .= '
			h3{
				font-weight: '.$tf_global_font_weight_h3.' !important;
				font-size: '.$tf_global_font_h3.'px !important;
				font-style: '.$tf_global_font_style_h3.' !important
			}';
		}

		// Global typo for H4
		if( !empty($tf_global_font_h4) || !empty($tf_global_font_weight_h4) || !empty($tf_global_font_style_h4) ){
			$output .= '
			h4{
				font-weight: '.$tf_global_font_weight_h4.' !important;
				font-size: '.$tf_global_font_h4.'px !important;
				font-style: '.$tf_global_font_style_h4.' !important
			}';
		}

		// Global typo for H5
		if( !empty($tf_global_font_h5) || !empty($tf_global_font_weight_h5) || !empty($tf_global_font_style_h5) ){
			$output .= '
			h5{
				font-weight: '.$tf_global_font_weight_h5.' !important;
				font-size: '.$tf_global_font_h5.'px !important;
				font-style: '.$tf_global_font_style_h5.' !important
			}';
		}

		// Global typo for H6
		if( !empty($tf_global_font_h6) || !empty($tf_global_font_weight_h6) || !empty($tf_global_font_style_h6) ){
			$output .= '
			h6{
				font-weight: '.$tf_global_font_weight_h6.' !important;
				font-size: '.$tf_global_font_h6.'px !important;
				font-style: '.$tf_global_font_style_h6.' !important
			}';
		}

		if( !empty( $tf_template1_global_reg ) ){ $output .= '
			.tf-template-global .tf-archive-head i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-details i,
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
			.tf-template-global .tf-head-info .tf-dropdown-share-content ul li button span
			 {
				color: '.$tf_template1_global_reg.';
			}';
		}

		if( !empty( $tf_template1_global_reg ) ){ $output .= '
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::before{
				border: 1px solid '.$tf_template1_global_reg.';
			}';
		}

		// Common CSS
		if( $tf_primary_color_reg  ) { $output .= '
			.tf_button, .tf-btn-flip:before, .tf-btn-flip, .btn-styled, .tf-review-form-container .tf-review-submit input[type="submit"], .tf-bttn-normal.bttn-primary, .tf-bttn-normal.bttn-secondary, .tf-template-global .tf-archive-head .active, .tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature, {color: '.$tf_primary_color_reg.' !important;}
			.tf-ask-question h3:before {color: '.$tf_primary_color_reg.';}
		'; }
		if( $tf_primary_color_hov  ) { $output .= '
			.tf_button:hover, .btn-styled:hover, .tf-btn-flip:after, .tf-review-form-container .tf-review-submit input[type="submit"]:hover, .tf-bttn-normal.bttn-primary:hover, .tf-bttn-normal.bttn-secondary:hover {color: '.$tf_primary_color_hov.' !important;}
		'; }
		if( $tf_primary_bg_color_reg  ) { $output .= '
			.tf_button, .tf-btn-flip:before, .btn-styled, .tf-review-form-container .tf-review-submit input[type="submit"], .tf-bttn-normal.bttn-primary, .tf-bttn-normal.bttn-secondary, .tf-template-global .tf-archive-head .active, .tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature {background: '.$tf_primary_bg_color_reg.' !important;}
			.tf_button, .btn-styled, .tf-review-form-container .tf-review-submit input[type="submit"] {border-color: '.$tf_primary_bg_color_reg.';}
		'; }
		if( $tf_primary_bg_color_hov  ) { $output .= '
			.tf_button:hover, .btn-styled:hover, .tf-btn-flip:after, .tf-review-form-container .tf-review-submit input[type="submit"]:hover, .tf-bttn-normal.bttn-primary:hover, .tf-bttn-normal.bttn-secondary:hover {background: '.$tf_primary_bg_color_hov.' !important;;}
			.tf_button:hover, .btn-styled:hover, .tf-review-form-container .tf-review-submit input[type="submit"]:hover {border-color: '.$tf_primary_bg_color_hov.';}
		'; }
		if( $tf_sidebar_gradient_one_reg && $tf_sidebar_gradient_two_reg  ) { $output .= '
			.tf_booking-widget, .tf-tour-details-right .tf-tour-booking-box, .tf-template-global .tf-box-wrapper.tf-box {background: linear-gradient(to bottom, '.$tf_sidebar_gradient_one_reg.' 0, '.$tf_sidebar_gradient_two_reg.' 100%);}
		'; }
		if( $tf_faq_color OR $tf_faq_icon_color OR $tf_faq_border_color ) { $output .= '
			.tf-faq-title h4,
			.tf-single-page .tf-faq-wrapper .tf-faq-single-inner h3 {
				color: '.$tf_faq_color.';
			}
			#tf-faq-item,
			.tf-single-page .tf-faq-wrapper .tf-faq-inner .tf-faq-single {
				border-color: '.$tf_faq_border_color.';
			}
			#tf-faq-item .arrow,
			.tf-single-page .tf-faq-wrapper .tf-faq-single-inner .tf-faq-collaps .faq-icon i.fa-plus {
				color: '.$tf_faq_icon_color.';
			}
		'; }

		if( $tf_review_bg_color OR $tf_review_color OR $tf_param_single_bg_color OR $tf_param_bg_color OR $tf_rating_bg_color OR $tf_rating_color) { $output .= '
			.tf-single-review .tf-single-details {
				background: '.$tf_review_bg_color.';
			}
			.tf-single-review .tf-review-details .tf-name, .tf-single-review .tf-review-details .tf-date, .tf-single-review .tf-review-details .tf-rating-stars, .tf-single-review .tf-review-details .tf-description {
				color: '.$tf_review_color.';
			}
			.tf-review-container .tf-review-progress-bar .tf-single .tf-p-bar,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-data-features .tf-progress-bar {
				background: '.$tf_param_single_bg_color.';
			}
			.tf-review-container .tf-review-progress-bar,
			.tf-single-page .tf-review-wrapper .tf-review-data {
				background: '.$tf_param_bg_color.';
				border-color: '.$tf_param_bg_color.';
			}
			.tf-review-container .tf-review-progress-bar .tf-single .tf-text, .tf-review-container .tf-review-progress-bar .tf-single .tf-p-b-rating,
			.tf-single-page .tf-review-wrapper .tf-review-data p,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-all-info li,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-all-info li i {
				color: '.$tf_param_txt_color.';
			}
			.tf-review-container .tf-total-review .tf-total-average div, .tf-archive-rating, .tf-single-page .tf-review-wrapper .tf-review-data .tf-review-data-average p {
				background: '.$tf_rating_bg_color.'!important;
				color: '.$tf_rating_color.'!important;
			}
		'; }
		if( $tf_review_bg_color) { $output .= '
			.tf-single-page .tf-review-reply .tf-review-reply-data {
				background: '.$tf_review_bg_color.';
				padding: 20px;
				border-radius: 5px;
			}
			.tf-single-page .tf-review-reply .tf-review-details h3,
			.tf-single-page .tf-review-reply .tf-review-details p,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li i{
				color: '.$tf_review_color.';
			}
		'; }
		
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
			.tf-share .share-toggle i, .tf-single-page .tf-section.tf-single-head .tf-share a i {color: '.$tf_share_color_reg.';}
		'; }
		if( $tf_share_color_hov  ) { $output .= '
			.tf-share .share-toggle i:hover, .tf-single-page .tf-section.tf-single-head .tf-share a i:hover {color: '.$tf_share_color_hov.';}
		'; }
		if( $tf_gradient_one_reg && $tf_gradient_two_reg  ) { $output .= '
			.show-on-map .btn-styled, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i {background: linear-gradient(to bottom, '.$tf_gradient_one_reg.' 0, '.$tf_gradient_two_reg.' 100%);}
		'; }
		if( $tf_gradient_one_hov && $tf_gradient_two_hov  ) { $output .= '
			.show-on-map .btn-styled:hover, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i:hover {background: linear-gradient(to bottom, '.$tf_gradient_one_hov.' 0, '.$tf_gradient_two_hov.' 100%);}
		'; }
		if( $tf_map_text_color  ) { $output .= '
			.show-on-map .btn-styled, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i {color: '.$tf_map_text_color.';}
		'; }
		if( $tf_hotel_features  ) { $output .= '
			.tf_features i, .tf-archive-desc i, .tf-single-page .tf-hotel-single-features ul li {color: '.$tf_hotel_features.'!important;}
		'; }
		if( $tf_hotel_table_color OR $tf_hotel_table_bg_color ) { $output .= '
			.availability-table thead,
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table thead {
				color: '.$tf_hotel_table_color.';
				background: '.$tf_hotel_table_bg_color.';
			}
		'; }
		if( $tf_hotel_table_color ) { $output .= '
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table thead tr th{
				color: '.$tf_hotel_table_color.';
			}
		'; }
		if( $tf_hotel_table_border_color  ) { $output .= '
			.availability-table td, .availability-table td.reserve, .tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table tr td, .tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table {border-color: '.$tf_hotel_table_border_color.';}
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
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block i {
				color: '.$tf_tour_icon_color.';
			}
			.tf-single-square-block h4,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block h3 {
				color: '.$tf_tour_heading_color.';
			}
			.tf-single-square-block,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block p {
				color: '.$tf_tour_text_color.';
			}
			.tf-single-square-block.first,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-first {
				background: '.$tf_tour_bg_one.';
			}
			.tf-single-square-block.second,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-second {
				background: '.$tf_tour_bg_two.';
			}
			.tf-single-square-block.third,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-third {
				background: '.$tf_tour_bg_three.';
			}
			.tf-single-square-block.fourth,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-tourth {
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
			.tf-include-section, .tf-exclude-section, .tf-single-page .tf-inex-wrapper .tf-inex {
				background-image: linear-gradient(to right, '.$tf_inc_gradient_one_reg.', '.$tf_inc_gradient_two_reg.');
				color: '.$tf_inc_text_color.';
			}
			.tf-inc-exc-content h4,
			.tf-single-page .tf-inex-wrapper .tf-inex h3 {
				color: '.$tf_inc_heading_color.';
			}
		'; }
		if( $tf_itin_time_day_txt OR $tf_itin_time_day_bg OR $tf_itin_heading_color OR $tf_itin_text_color OR $tf_itin_bg_color OR $tf_itin_icon_color) { $output .= '
			.tf-travel-time span,
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item .itinerary-day {
				color: '.$tf_itin_time_day_txt.';
			}
			.tf-travel-time {
				background: '.$tf_itin_time_day_bg.';
			}
			.tf-accordion-head h4, 
			.tf-accordion-head h4:hover,
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item h3 {
				color: '.$tf_itin_heading_color.';
			}
			.tf-travel-desc,
			.tf-single-page .tf-itinerary-content-details p {
				color: '.$tf_itin_text_color.';
			}
			#tf-accordion-wrapper .tf-accordion-content, #tf-accordion-wrapper .tf-accordion-head {
				background: '.$tf_itin_bg_color.';
			}
			#tf-accordion-wrapper .arrow-animate, #tf-accordion-wrapper .arrow {
				color: '.$tf_itin_icon_color.';
			}
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::before,
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item .accordion-checke::before {
				border: 1px solid '.$tf_itin_icon_color.';
			}
			.tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::after {
				background: '.$tf_itin_icon_color.';
			}
		'; }
		
		wp_add_inline_style( 'tf-app-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_tour_css', 99999 );
?>