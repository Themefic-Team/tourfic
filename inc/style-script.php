<?php
defined( 'ABSPATH' ) || exit;

/**
 *	Enqueue frontend scripts
 */
if ( !function_exists('tourfic_enqueue_scripts') ) {
	function tourfic_enqueue_scripts(){

		global $detect, $dedicated_mobile;

        $tf_min_css = !empty(tfopt( 'css_min' )) ? '.min' : '';
		wp_enqueue_style( 'tf-common-style', TF_ASSETS_URL . 'css/common.css', null, TOURFIC );
		if ( get_post_type() == 'tf_hotel' ){
			wp_enqueue_style( 'tf-hotel-style', TF_ASSETS_URL . 'css/hotel' . $tf_min_css . '.css', null, TOURFIC );
		}
		if ( get_post_type() == 'tf_tours' ){
			wp_enqueue_style( 'tf-tour-style', TF_ASSETS_URL . 'css/tour' . $tf_min_css . '.css', null, TOURFIC );
		}

		// Inline script parent
		wp_register_script( 'tourfic-inline-scripts', '' );
		wp_enqueue_script( 'tourfic-inline-scripts' );
	}
	add_action( 'wp_enqueue_scripts', 'tourfic_enqueue_scripts', 9999 );
}

/*
 * 
 * CUSTOM CSS
 * 
 */

if( !function_exists( 'tf_custom_css' ) ){
	function tf_custom_css(){
		// Store as PHP variables
		// Common CSS
		$tf_primary_color_reg = !empty(tfopt( 'tourfic-button-color' )['regular']) ? tfopt( 'tourfic-button-color' )['regular'] : '';
		$tf_primary_color_hov = !empty(tfopt( 'tourfic-button-color' )['hover']) ? tfopt( 'tourfic-button-color' )['hover'] : '';
		$tf_primary_bg_color_reg = !empty(tfopt( 'tourfic-button-bg-color' )['regular']) ? tfopt( 'tourfic-button-bg-color' )['regular'] : '';
		$tf_primary_bg_color_hov = !empty(tfopt( 'tourfic-button-bg-color' )['hover']) ? tfopt( 'tourfic-button-bg-color' )['hover'] : '';
		$tf_sidebar_gradient_one_reg = !empty(tfopt( 'tourfic-sidebar-booking' )['gradient_one_reg']) ? tfopt( 'tourfic-sidebar-booking' )['gradient_one_reg'] : '';
		$tf_sidebar_gradient_two_reg = !empty(tfopt( 'tourfic-sidebar-booking' )['gradient_two_reg']) ? tfopt( 'tourfic-sidebar-booking' )['gradient_two_reg'] : '';
		$tf_faq_color = !empty(tfopt( 'tourfic-faq-style' )['faq_color']) ? tfopt( 'tourfic-faq-style' )['faq_color'] : '';
		$tf_faq_icon_color = !empty(tfopt( 'tourfic-faq-style' )['faq_icon_color']) ? tfopt( 'tourfic-faq-style' )['faq_icon_color'] : '';
		$tf_faq_border_color = !empty(tfopt( 'tourfic-faq-style' )['faq_border_color']) ? tfopt( 'tourfic-faq-style' )['faq_border_color'] : '';
		$tf_rating_color = !empty(tfopt( 'tourfic-review-style' )['rating_color']) ? tfopt( 'tourfic-review-style' )['rating_color'] : '';
		$tf_rating_bg_color = !empty(tfopt( 'tourfic-review-style' )['rating_bg_color']) ? tfopt( 'tourfic-review-style' )['rating_bg_color'] : '';
		$tf_param_bg_color = !empty(tfopt( 'tourfic-review-style' )['param_bg_color']) ? tfopt( 'tourfic-review-style' )['param_bg_color'] : '';
		$tf_param_txt_color = !empty(tfopt( 'tourfic-review-style' )['param_txt_color']) ? tfopt( 'tourfic-review-style' )['param_txt_color'] : '';
		$tf_param_single_bg_color = !empty(tfopt( 'tourfic-review-style' )['param_single_bg_color']) ? tfopt( 'tourfic-review-style' )['param_single_bg_color'] : '';
		$tf_review_color = !empty(tfopt( 'tourfic-review-style' )['review_color']) ? tfopt( 'tourfic-review-style' )['review_color'] : '';
		$tf_review_bg_color = !empty(tfopt( 'tourfic-review-style' )['review_bg_color']) ? tfopt( 'tourfic-review-style' )['review_bg_color'] : '';
		
		$output = '';
		
		// Common CSS
		if( $tf_primary_color_reg  ) { $output .= '
			.tf_button, .tf-btn-flip:before, .tf-btn-flip, .btn-styled, .tf-review-form-container .tf-review-submit input[type="submit"] {color: '.$tf_primary_color_reg.';}
			.acr-inc, .tf-ask-question h3:before {color: '.$tf_primary_color_reg.';}
		'; }
		if( $tf_primary_color_hov  ) { $output .= '
			.tf_button:hover, .btn-styled:hover, .tf-btn-flip:after, .tf-review-form-container .tf-review-submit input[type="submit"]:hover {color: '.$tf_primary_color_hov.';}
		'; }
		if( $tf_primary_bg_color_reg  ) { $output .= '
			.tf_button, .tf-btn-flip:before, .btn-styled, .tf-review-form-container .tf-review-submit input[type="submit"] {background: '.$tf_primary_bg_color_reg.';}
			.tf_button, .btn-styled, .acr-dec, .acr-inc, .tf-review-form-container .tf-review-submit input[type="submit"] {border-color: '.$tf_primary_bg_color_reg.';}
		'; }
		if( $tf_primary_bg_color_hov  ) { $output .= '
			.tf_button:hover, .btn-styled:hover, .tf-btn-flip:after, .tf-review-form-container .tf-review-submit input[type="submit"]:hover {background: '.$tf_primary_bg_color_hov.';}
			.tf_button:hover, .btn-styled:hover, .tf-review-form-container .tf-review-submit input[type="submit"]:hover {border-color: '.$tf_primary_bg_color_hov.';}
		'; }
		if( $tf_sidebar_gradient_one_reg && $tf_sidebar_gradient_two_reg  ) { $output .= '
			.tf_booking-widget {background: linear-gradient(to bottom, '.$tf_sidebar_gradient_one_reg.' 0, '.$tf_sidebar_gradient_two_reg.' 100%);}
		'; }
		if( $tf_faq_color OR $tf_faq_icon_color OR $tf_faq_border_color ) { $output .= '
			.tf-faq-title h4 {
				color: '.$tf_faq_color.';
			}
			#tf-faq-item {
				border-color: '.$tf_faq_border_color.';
			}
			#tf-faq-item .arrow {
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
			.tf-review-container .tf-review-progress-bar .tf-single .tf-p-bar {
				background: '.$tf_param_single_bg_color.';
			}
			.tf-review-container .tf-review-progress-bar {
				background: '.$tf_param_bg_color.';
				border-color: '.$tf_param_bg_color.';
			}
			.tf-review-container .tf-review-progress-bar .tf-single .tf-text, .tf-review-container .tf-review-progress-bar .tf-single .tf-p-b-rating {
				color: '.$tf_param_txt_color.';
			}
			.tf-review-container .tf-total-review .tf-total-average div, .tf-archive-rating {
				background: '.$tf_rating_bg_color.'!important;
				color: '.$tf_rating_color.'!important;
			}
		'; }
		
		wp_add_inline_style( 'tf-common-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_custom_css', 99999 );

if( !function_exists( 'tf_hotel_css' ) ){
	function tf_hotel_css(){
		// Store as PHP variables
		// Hotel CSS
		$tf_hotel_type_color = !empty(tfopt( 'tourfic-hotel-type-bg-color' )['regular']) ? tfopt( 'tourfic-hotel-type-bg-color' )['regular'] : '';
		$tf_hotel_type_bg_color = !empty(tfopt( 'tourfic-hotel-type-bg-color' )['hover']) ? tfopt( 'tourfic-hotel-type-bg-color' )['hover'] : '';
		$tf_share_color_reg = !empty(tfopt( 'tourfic-hotel-share-icon' )['regular']) ? tfopt( 'tourfic-hotel-share-icon' )['regular'] : '';
		$tf_share_color_hov = !empty(tfopt( 'tourfic-hotel-share-icon' )['hover']) ? tfopt( 'tourfic-hotel-share-icon' )['hover'] : '';
		$tf_gradient_one_reg = !empty(tfopt( 'tourfic-hotel-map-button' )['gradient_one_reg']) ? tfopt( 'tourfic-hotel-map-button' )['gradient_one_reg'] : '';
		$tf_gradient_two_reg = !empty(tfopt( 'tourfic-hotel-map-button' )['gradient_two_reg']) ? tfopt( 'tourfic-hotel-map-button' )['gradient_two_reg'] : '';
		$tf_gradient_one_hov = !empty(tfopt( 'tourfic-hotel-map-button' )['gradient_one_hov']) ? tfopt( 'tourfic-hotel-map-button' )['gradient_one_hov'] : '';
		$tf_gradient_two_hov = !empty(tfopt( 'tourfic-hotel-map-button' )['gradient_two_hov']) ? tfopt( 'tourfic-hotel-map-button' )['gradient_two_hov'] : '';
		$tf_hotel_features = !empty(tfopt( 'tourfic-hotel-features-color' )['regular']) ? tfopt( 'tourfic-hotel-features-color' )['regular'] : '';
		$tf_hotel_table_color = !empty(tfopt( 'tourfic-hotel-table-style' )['table_color']) ? tfopt( 'tourfic-hotel-table-style' )['table_color'] : '';
		$tf_hotel_table_bg_color = !empty(tfopt( 'tourfic-hotel-table-style' )['table_bg_color']) ? tfopt( 'tourfic-hotel-table-style' )['table_bg_color'] : '';
		$tf_hotel_table_border_color = !empty(tfopt( 'tourfic-hotel-table-style' )['table_border_color']) ? tfopt( 'tourfic-hotel-table-style' )['table_border_color'] : '';
		
		$output = '';
		
		// Hotel CSS
		if( $tf_hotel_type_color  ) { $output .= '
			.tf-title-left span.post-type {color: '.$tf_hotel_type_color.';}
		'; }
		if( $tf_hotel_type_bg_color  ) { $output .= '
			.tf-title-left span.post-type {background: '.$tf_hotel_type_bg_color.';}
		'; }
		if( $tf_share_color_reg  ) { $output .= '
			.tf-share .share-toggle i {color: '.$tf_share_color_reg.';}
		'; }
		if( $tf_share_color_hov  ) { $output .= '
			.tf-share .share-toggle i:hover {color: '.$tf_share_color_hov.';}
		'; }
		if( $tf_gradient_one_reg && $tf_gradient_two_reg  ) { $output .= '
			.show-on-map .btn-styled {background: linear-gradient(to bottom, '.$tf_gradient_one_reg.' 0, '.$tf_gradient_two_reg.' 100%);}
		'; }
		if( $tf_gradient_one_hov && $tf_gradient_two_hov  ) { $output .= '
			.show-on-map .btn-styled:hover {background: linear-gradient(to bottom, '.$tf_gradient_one_hov.' 0, '.$tf_gradient_two_hov.' 100%);}
		'; }
		if( $tf_hotel_features  ) { $output .= '
			.tf_features i, .tf-archive-desc i {color: '.$tf_hotel_features.'!important;}
		'; }
		if( $tf_hotel_table_color OR $tf_hotel_table_bg_color ) { $output .= '
			.availability-table thead {
				color: '.$tf_hotel_table_color.';
				background: '.$tf_hotel_table_bg_color.';
			}
		'; }
		if( $tf_hotel_table_border_color  ) { $output .= '
			.availability-table td, .availability-table td.reserve {border-color: '.$tf_hotel_table_border_color.';}
		'; }
		
		wp_add_inline_style( 'tf-hotel-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_hotel_css', 99999 );

if( !function_exists( 'tf_tour_css' ) ){
	function tf_tour_css(){
		// Store as PHP variables
		// Tour CSS
		$tf_tour_sale_price = !empty(tfopt( 'tourfic-tour-pricing-color' )['sale_price']) ? tfopt( 'tourfic-tour-pricing-color' )['sale_price'] : '';
		$tf_tour_org_price = !empty(tfopt( 'tourfic-tour-pricing-color' )['org_price']) ? tfopt( 'tourfic-tour-pricing-color' )['org_price'] : '';
		$tf_tour_tab_text = !empty(tfopt( 'tourfic-tour-pricing-color' )['tab_text']) ? tfopt( 'tourfic-tour-pricing-color' )['tab_text'] : '';
		$tf_tour_tab_bg = !empty(tfopt( 'tourfic-tour-pricing-color' )['tab_bg']) ? tfopt( 'tourfic-tour-pricing-color' )['tab_bg'] : '';
		$tf_tour_active_tab_text = !empty(tfopt( 'tourfic-tour-pricing-color' )['active_tab_text']) ? tfopt( 'tourfic-tour-pricing-color' )['active_tab_text'] : '';
		$tf_tour_active_tab_bg = !empty(tfopt( 'tourfic-tour-pricing-color' )['active_tab_bg']) ? tfopt( 'tourfic-tour-pricing-color' )['active_tab_bg'] : '';
		$tf_tour_tab_border = !empty(tfopt( 'tourfic-tour-pricing-color' )['tab_border']) ? tfopt( 'tourfic-tour-pricing-color' )['tab_border'] : '';
		$tf_tour_icon_color = !empty(tfopt( 'tourfic-tour-info-color' )['icon_color']) ? tfopt( 'tourfic-tour-info-color' )['icon_color'] : '';
		$tf_tour_heading_color = !empty(tfopt( 'tourfic-tour-info-color' )['heading_color']) ? tfopt( 'tourfic-tour-info-color' )['heading_color'] : '';
		$tf_tour_text_color = !empty(tfopt( 'tourfic-tour-info-color' )['text_color']) ? tfopt( 'tourfic-tour-info-color' )['text_color'] : '';
		$tf_tour_bg_one = !empty(tfopt( 'tourfic-tour-info-color' )['bg_one']) ? tfopt( 'tourfic-tour-info-color' )['bg_one'] : '';
		$tf_tour_bg_two = !empty(tfopt( 'tourfic-tour-info-color' )['bg_two']) ? tfopt( 'tourfic-tour-info-color' )['bg_two'] : '';
		$tf_tour_bg_three = !empty(tfopt( 'tourfic-tour-info-color' )['bg_three']) ? tfopt( 'tourfic-tour-info-color' )['bg_three'] : '';
		$tf_tour_bg_four = !empty(tfopt( 'tourfic-tour-info-color' )['bg_four']) ? tfopt( 'tourfic-tour-info-color' )['bg_four'] : '';
		$tf_tour_btn_col = !empty(tfopt( 'tourfic-tour-sticky-booking' )['btn_col']) ? tfopt( 'tourfic-tour-sticky-booking' )['btn_col'] : '';
		$tf_tour_btn_bg = !empty(tfopt( 'tourfic-tour-sticky-booking' )['btn_bg']) ? tfopt( 'tourfic-tour-sticky-booking' )['btn_bg'] : '';
		$tf_tour_btn_hov_bg = !empty(tfopt( 'tourfic-tour-sticky-booking' )['btn_hov_bg']) ? tfopt( 'tourfic-tour-sticky-booking' )['btn_hov_bg'] : '';
		$tf_tour_btn_hov_col = !empty(tfopt( 'tourfic-tour-sticky-booking' )['btn_hov_col']) ? tfopt( 'tourfic-tour-sticky-booking' )['btn_hov_col'] : '';
		$tf_tour_form_background = !empty(tfopt( 'tourfic-tour-sticky-booking' )['form_background']) ? tfopt( 'tourfic-tour-sticky-booking' )['form_background'] : '';
		$tf_tour_form_border = !empty(tfopt( 'tourfic-tour-sticky-booking' )['form_border']) ? tfopt( 'tourfic-tour-sticky-booking' )['form_border'] : '';
		$tf_inc_gradient_one_reg = !empty(tfopt( 'tourfic-include-exclude' )['gradient_one_reg']) ? tfopt( 'tourfic-include-exclude' )['gradient_one_reg'] : '';
		$tf_inc_gradient_two_reg = !empty(tfopt( 'tourfic-include-exclude' )['gradient_two_reg']) ? tfopt( 'tourfic-include-exclude' )['gradient_two_reg'] : '';
		$tf_inc_heading_color = !empty(tfopt( 'tourfic-include-exclude' )['heading_color']) ? tfopt( 'tourfic-include-exclude' )['heading_color'] : '';
		$tf_inc_text_color = !empty(tfopt( 'tourfic-include-exclude' )['text_color']) ? tfopt( 'tourfic-include-exclude' )['text_color'] : '';
		$tf_itin_time_day_txt = !empty(tfopt( 'tourfic-tour-itinerary' )['time_day_txt']) ? tfopt( 'tourfic-tour-itinerary' )['time_day_txt'] : '';
		$tf_itin_time_day_bg = !empty(tfopt( 'tourfic-tour-itinerary' )['time_day_bg']) ? tfopt( 'tourfic-tour-itinerary' )['time_day_bg'] : '';
		$tf_itin_heading_color = !empty(tfopt( 'tourfic-tour-itinerary' )['heading_color']) ? tfopt( 'tourfic-tour-itinerary' )['heading_color'] : '';
		$tf_itin_text_color = !empty(tfopt( 'tourfic-tour-itinerary' )['text_color']) ? tfopt( 'tourfic-tour-itinerary' )['text_color'] : '';
		$tf_itin_bg_color = !empty(tfopt( 'tourfic-tour-itinerary' )['bg_color']) ? tfopt( 'tourfic-tour-itinerary' )['bg_color'] : '';
		$tf_itin_icon_color = !empty(tfopt( 'tourfic-tour-itinerary' )['icon_color']) ? tfopt( 'tourfic-tour-itinerary' )['icon_color'] : '';
		
		$output = '';
		
		// Tour CSS
		if( $tf_tour_sale_price OR $tf_tour_org_price OR $tf_tour_tab_text OR $tf_tour_tab_bg OR $tf_tour_tab_border) { $output .= '
			.tf-single-tour-pricing .tf-price span.sale-price {
				color: '.$tf_tour_sale_price.';
			}
			.tf-single-tour-pricing .tf-price {
				color: '.$tf_tour_org_price.';
			}
			.tf-single-tour-pricing .tf-price-tab li {
				color: '.$tf_tour_tab_text.';
			}
			.tf-single-tour-pricing .tf-price-tab li {
				background: '.$tf_tour_tab_bg.';
			}
			.tf-single-tour-pricing .tf-price-tab li.active {
				color: '.$tf_tour_active_tab_text.';
			}
			.tf-single-tour-pricing .tf-price-tab li.active {
				background: '.$tf_tour_active_tab_bg.';
			}
			.tf-single-tour-pricing .tf-price-tab, .tf-single-tour-pricing .tf-price-tab li:nth-child(2), .tf-single-tour-pricing .tf-price-tab li:nth-child(3) {
				border-color: '.$tf_tour_tab_border.';
			}
		'; }

		if( $tf_tour_icon_color OR $tf_tour_heading_color OR $tf_tour_text_color OR $tf_tour_bg_one OR $tf_tour_bg_two OR $tf_tour_bg_three OR $tf_tour_bg_four) { $output .= '
			.tf-single-square-block i {
				color: '.$tf_tour_icon_color.';
			}
			.tf-single-square-block h4 {
				color: '.$tf_tour_heading_color.';
			}
			.tf-single-square-block {
				color: '.$tf_tour_text_color.';
			}
			.tf-single-square-block.first {
				background: '.$tf_tour_bg_one.';
			}
			.tf-single-square-block.second {
				background: '.$tf_tour_bg_two.';
			}
			.tf-single-square-block.third {
				background: '.$tf_tour_bg_three.';
			}
			.tf-single-square-block.fourth {
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
			.tf-include-section, .tf-exclude-section {
				background-image: linear-gradient(to right, '.$tf_inc_gradient_one_reg.', '.$tf_inc_gradient_two_reg.');
				color: '.$tf_inc_text_color.';
			}
			.tf-inc-exc-content h4 {
				color: '.$tf_inc_heading_color.';
			}
		'; }
		if( $tf_itin_time_day_txt OR $tf_itin_time_day_bg OR $tf_itin_heading_color OR $tf_itin_text_color OR $tf_itin_bg_color OR $tf_itin_icon_color) { $output .= '
			.tf-travel-time span {
				color: '.$tf_itin_time_day_txt.';
			}
			.tf-travel-time {
				background: '.$tf_itin_time_day_bg.';
			}
			.tf-accordion-head h4, .tf-accordion-head h4:hover {
				color: '.$tf_itin_heading_color.';
			}
			.tf-travel-desc {
				color: '.$tf_itin_text_color.';
			}
			#tf-accordion-wrapper .tf-accordion-content, #tf-accordion-wrapper .tf-accordion-head {
				background: '.$tf_itin_bg_color.';
			}
			#tf-accordion-wrapper .arrow-animate, #tf-accordion-wrapper .arrow {
				color: '.$tf_itin_icon_color.';
			}
		'; }
		
		wp_add_inline_style( 'tf-tour-style', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'tf_tour_css', 99999 );
?>