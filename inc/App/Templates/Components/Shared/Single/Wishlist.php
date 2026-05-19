<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;
use Tourfic\App\Wishlist as Wishlist_Class;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Wishlist Component
 * Shared markup for Elementor and Bricks Wishlist widgets
 */
class Wishlist {

	/**
	 * Static render method for Wishlist component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id            = get_the_ID();
		$post_type          = get_post_type();
		$has_in_wishlist    = Wishlist_Class::tf_has_item_in_wishlist( $post_id );
        $design  	        = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';
		$disable_wishlist_sec = 0;

		// Get post meta based on post type
		if ( 'tf_hotel' === $post_type ) {
			$post_meta            = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$disable_wishlist_sec = ! empty( $post_meta['h-wishlist'] ) ? $post_meta['h-wishlist'] : 0;
		} elseif ( 'tf_tours' === $post_type ) {
			$post_meta            = get_post_meta( $post_id, 'tf_tours_opt', true );
			$disable_wishlist_sec = ! empty( $post_meta['t-wishlist'] ) ? $post_meta['t-wishlist'] : 0;
		} elseif ( 'tf_carrental' === $post_type ) {
			$post_meta            = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$disable_wishlist_sec = ! empty( $post_meta['c-wishlist'] ) ? $post_meta['c-wishlist'] : 0;
		} elseif ( 'tf_apartment' === $post_type ) {
			$disable_wishlist_sec = 0;
		} else {
			return;
		}

		// Generate wishlist icon classes and data attributes
		$wishlist_icon_classes = $has_in_wishlist ? 'tf-text-red remove-wishlist' : 'add-wishlist';
		$wishlist_data_attrs    = sprintf(
			'data-nonce="%s" data-id="%s" data-type="%s" data-icon="%s" data-active-icon="%s"',
			esc_attr( wp_create_nonce( 'wishlist-nonce' ) ),
			esc_attr( $post_id ),
			esc_attr( $post_type ),
			esc_attr( ! empty( $settings['wishlist_icon']['icon'] ) ? $settings['wishlist_icon']['icon'] : 'far fa-heart' ),
			esc_attr( ! empty( $settings['wishlist_active_icon']['icon'] ) ? $settings['wishlist_active_icon']['icon'] : 'fas fa-heart' )
		);

		// Add page data if available
		if ( Helper::tfopt( 'wl-page' ) ) {
			$wishlist_data_attrs .= sprintf(
				' data-page-title="%s" data-page-url="%s"',
				esc_html( get_the_title( Helper::tfopt( 'wl-page' ) ) ),
				esc_url( get_permalink( Helper::tfopt( 'wl-page' ) ) )
			);
		}

		// Get icon values for rendering
		$current_icon = $has_in_wishlist 
			? ( ! empty( $settings['wishlist_active_icon']['icon'] ) ? $settings['wishlist_active_icon']['icon'] : 'fas fa-heart' )
			: ( ! empty( $settings['wishlist_icon']['icon'] ) ? $settings['wishlist_icon']['icon'] : 'far fa-heart' );

		// Build the complete wishlist icon HTML
		$wishlist_icon_html = sprintf(
			'<i class="%s %s" %s></i>',
			$current_icon,
			$wishlist_icon_classes,
			$wishlist_data_attrs
		);

		// Icon type
		$icon_type = ! empty( $settings['icon_type'] ) ? $settings['icon_type'] : 'rounded';

		// Render wishlist if not disabled and conditions are met
		if ( $disable_wishlist_sec != 1 && Helper::tfopt( 'wl-bt-for' ) && in_array( '1', Helper::tfopt( 'wl-bt-for' ) ) ) {
			$show_for_logged_in  = is_user_logged_in() && Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) );
			$show_for_logged_out = ! is_user_logged_in() && Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) );
			$icon_class          = 'rounded' === $icon_type ? 'tf-icon tf-wishlist-icon' : 'tf-wishlist-icon tf-wishlist-button';

			if ( $design === 'design-1' && ( $show_for_logged_in || $show_for_logged_out ) ) {
				echo '<div class="' . esc_attr( $icon_class ) . '">';
				echo wp_kses( $wishlist_icon_html, Helper::tf_custom_wp_kses_allow_tags() );
                echo ($post_type == 'tf_apartment' && empty( $builder )) ? '<span class="tf-wishlist-text">' . esc_html__( 'Save', 'tourfic' ) . '</span>' : '';
				echo '</div>';
			} elseif ( $design === 'design-2' && ( $show_for_logged_in || $show_for_logged_out ) ) {
                echo '<a class="' . esc_attr( $icon_class ) . ' tf-wishlist">';
                echo wp_kses( $wishlist_icon_html, Helper::tf_custom_wp_kses_allow_tags() );
                echo '</a>';
            } elseif ( $design === 'design-3' && ( $show_for_logged_in || $show_for_logged_out ) ) {
                echo '<span class="' . esc_attr( $icon_class ) . ' single-tour-wish-bt">';
                echo wp_kses( $wishlist_icon_html, Helper::tf_custom_wp_kses_allow_tags() );
                echo '</span>';
            }
		}
	}
}
