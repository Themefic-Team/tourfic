<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

class Helper {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Helper;

	public function __construct() {
		add_filter( 'body_class', array( $this, 'tf_templates_body_class' ) );
	}

	/**
	 * Template 3 Compatible to others Themes
	 *
	 * @since 2.10.8
	 */
	function tf_templates_body_class( $classes ) {

		$tf_tour_arc_selected_template      = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template     = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		$tf_apartment_arc_selected_template = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] : 'default';
		$tf_hotel_global_template           = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-hotel'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
		$tf_tour_global_template            = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-tour'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
		$tf_apartment_global_template       = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-apartment'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-apartment'] : 'default';

		if ( is_post_type_archive( 'tf_tours' ) || is_tax( 'tour_destination' ) ) {
			if ( 'design-2' == $tf_tour_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_post_type_archive( 'tf_hotel' ) || is_tax( 'hotel_location' ) ) {
			if ( 'design-2' == $tf_hotel_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_post_type_archive( 'tf_apartment' ) || is_tax( 'apartment_location' ) ) {
			if ( 'design-1' == $tf_apartment_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_singular( 'tf_hotel' ) ) {
			$meta                       = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
			$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
			if ( "single" == $tf_hotel_layout_conditions ) {
				$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
			}
			$tf_hotel_selected_check = ! empty( $tf_hotel_single_template ) ? $tf_hotel_single_template : $tf_hotel_global_template;
			if ( 'design-2' == $tf_hotel_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_singular( 'tf_tours' ) ) {
			$meta                      = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
			$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
			if ( "single" == $tf_tour_layout_conditions ) {
				$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
			}
			$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;
			if ( 'design-2' == $tf_tour_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_singular( 'tf_apartment' ) ) {
			$meta                          = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
			$tf_aprtment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
			if ( "single" == $tf_aprtment_layout_conditions ) {
				$tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
			}
			$tf_apartment_selected_check = ! empty( $tf_apartment_single_template ) ? $tf_apartment_single_template : $tf_apartment_global_template;
			if ( 'design-1' == $tf_apartment_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		$tf_search_result_page_id = ! empty( self::tfopt( 'search-result-page' ) ) ? self::tfopt( 'search-result-page' ) : '';
		if ( ! empty( $tf_search_result_page_id ) ) {
			$tf_search_result_page_slug = get_post_field( 'post_name', $tf_search_result_page_id );
		}
		if ( ! empty( $tf_search_result_page_slug ) ) {
			$tf_current_page_id = get_post_field( 'post_name', get_the_ID() );
			if ( $tf_search_result_page_slug == $tf_current_page_id ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		return $classes;
	}

	static function get_terms_dropdown( $taxonomy, $args = array() ) {
		$defaults = array(
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		);
		$args     = wp_parse_args( $args, $defaults );

		$terms = get_terms( $taxonomy, $args );

		$term_dropdown = array();
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_dropdown[ $term->slug ] = $term->name;
			}
		}

		return $term_dropdown;
	}
}