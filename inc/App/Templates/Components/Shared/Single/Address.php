<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;
use \Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Address Component
 * Shared markup for Elementor and Bricks Address widgets
 */
class Address {

	/**
	 * Static render method for Address component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$show_location = Helper::get_switcher_value( $settings, 'show_location', 'yes', $builder );
        $design  	   = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';
		$address       = '';
		$first_location_url  = '';
		$first_location_name = '';

		// Get address and location data based on post type
		if ( 'tf_hotel' === $post_type ) {
			$post_meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$locations = get_the_terms( $post_id, 'hotel_location' );

			if ( $locations && ! is_wp_error( $locations ) ) {
				$first_location_id   = $locations[0]->term_id;
				$first_location_term = get_term( $first_location_id );
				$first_location_name = $locations[0]->name;
				$first_location_url  = get_term_link( $first_location_term );
			}

			if ( ! empty( $post_meta['map'] ) && Helper::tf_data_types( $post_meta['map'] ) ) {
				$address = ! empty( Helper::tf_data_types( $post_meta['map'] )['address'] ) ? Helper::tf_data_types( $post_meta['map'] )['address'] : '';
			}
		} elseif ( 'tf_tours' === $post_type ) {
			$post_meta = get_post_meta( $post_id, 'tf_tours_opt', true );
			if ( ! empty( $post_meta['location'] ) && Helper::tf_data_types( $post_meta['location'] ) ) {
				$address = ! empty( Helper::tf_data_types( $post_meta['location'] )['address'] ) ? Helper::tf_data_types( $post_meta['location'] )['address'] : '';
			}
		} elseif ( 'tf_apartment' === $post_type ) {
			$post_meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
			if ( ! empty( $post_meta['map'] ) && Helper::tf_data_types( $post_meta['map'] ) ) {
				$address = ! empty( Helper::tf_data_types( $post_meta['map'] )['address'] ) ? Helper::tf_data_types( $post_meta['map'] )['address'] : '';
			}
		} else {
			return;
		}

		//Address icon
        $address_icon_html = ($design == 'design-1') ? '<i class="fa-solid fa-location-dot"></i>' : '<i class="ri-map-pin-line"></i>';
		if ( 'elementor' === $builder && class_exists( '\Elementor\Icons_Manager' ) ) {
			$address_icon_migrated = isset($settings['__fa4_migrated']['address_icon']);
			$address_icon_is_new = empty($settings['address_icon_comp']);

			if ( $address_icon_is_new || $address_icon_migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['address_icon'], [ 'aria-hidden' => 'true' ] );
				$address_icon_html = ob_get_clean();
			} else{
				$address_icon_html = '<i class="' . esc_attr( $settings['address_icon_comp'] ) . '"></i>';
			}
		} elseif ( 'bricks' == $builder ) {
			if ( ! empty( $settings['address_icon']['library'] ) && ! empty( $settings['address_icon']['icon'] ) ) {
				$address_icon_html = '<i class="' . esc_attr( $settings['address_icon']['icon'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['address_icon']['class'] ) ) {
				$address_icon_html = '<i class="' . esc_attr( $settings['address_icon']['class'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['address_icon'] ) && is_string( $settings['address_icon'] ) ) {
				$address_icon_html = '<i class="' . esc_attr( $settings['address_icon'] ) . '" aria-hidden="true"></i>';
			}
		}
		?>
		<div class="tf-title-meta tf-map-link tf-flex tf-flex-align-center tf-flex-gap-8">
			<?php if ( ! empty( $address ) ) {
				echo '<div class="tf-address">';
				echo wp_kses( $address_icon_html, Helper::tf_custom_wp_kses_allow_tags() );
				echo wp_kses_post( $address );
				echo '</div>';
			} ?>
			<?php if ( 'tf_hotel' === $post_type && $design !== 'design-2' && 'yes' ==$show_location && ! empty( $first_location_url ) ) : ?>
				<a href="<?php echo esc_url( $first_location_url ); ?>" class="more-hotel tf-d-ib">
					<?php
					/* translators: %s location name */
					printf( esc_html__( ' - Show more hotels in %s', 'tourfic' ), esc_html( $first_location_name ) );
					?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
