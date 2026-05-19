<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Elementor\Icons_Manager;
use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Map Component
 * Shared markup for Elementor and Bricks Map widgets
 */
class Map {

	/**
	 * Static render method for Map component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '', $height = '', $title = true ) {
		$post_type = get_post_type();
        $wrapper_open          = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close         = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';
        
		if ( 'tf_hotel' === $post_type ) {
			self::tf_hotel_map( $settings, $builder, $height, $title );
		} elseif ( 'tf_tours' === $post_type ) {
			self::tf_tour_map( $settings, $builder, $height, $title );
		} elseif ( 'tf_apartment' === $post_type ) {
			self::tf_apartment_map( $settings, $builder, $height, $title );
		} elseif ( 'tf_carrental' === $post_type ) {
			self::tf_car_map( $settings, $builder, $height, $title );
		} else {
			return;
		}

        echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	private static function tf_hotel_map( $settings, $builder, $height = '' ) {
        $design  	           = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';
		$show_icon             = Helper::get_switcher_value( $settings, 'show_icon', 'yes', $builder );
		$show_title 		   = Helper::get_switcher_value( $settings, 'show_title', 'yes', $builder );
		$wrapper 			   = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'no';
		$tf_openstreet_map     = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : 'default';
		$meta                  = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
		$address               = '';
		$address_latitude      = '';
		$address_longitude     = '';
		$address_zoom          = '';

		if ( ! empty( $meta['map'] ) && Helper::tf_data_types( $meta['map'] ) ) {
			$address           = ! empty( Helper::tf_data_types( $meta['map'] )['address'] ) ? Helper::tf_data_types( $meta['map'] )['address'] : '';
			$address_latitude  = ! empty( Helper::tf_data_types( $meta['map'] )['latitude'] ) ? Helper::tf_data_types( $meta['map'] )['latitude'] : '';
			$address_longitude = ! empty( Helper::tf_data_types( $meta['map'] )['longitude'] ) ? Helper::tf_data_types( $meta['map'] )['longitude'] : '';
			$address_zoom      = ! empty( Helper::tf_data_types( $meta['map'] )['zoom'] ) ? Helper::tf_data_types( $meta['map'] )['zoom'] : '';
		}

		// Map icon
		$map_icon_html = '<i class="fas fa-map-marker-alt"></i>';
		if ( 'elementor' === $builder && class_exists( '\Elementor\Icons_Manager' ) ) {
			$map_icon_migrated = isset( $settings['__fa4_migrated']['map_icon'] );
			$map_icon_is_new   = empty( $settings['map_icon_comp'] );

			if ( $map_icon_is_new || $map_icon_migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['map_icon'], [ 'aria-hidden' => 'true' ] );
				$map_icon_html = ob_get_clean();
			} else {
				$map_icon_html = '<i class="' . esc_attr( $settings['map_icon_comp'] ) . '"></i>';
			}
		} elseif ( 'bricks' === $builder ) {
			if ( ! empty( $settings['map_icon']['library'] ) && ! empty( $settings['map_icon']['icon'] ) ) {
				$map_icon_html = '<i class="' . esc_attr( $settings['map_icon']['icon'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['map_icon']['class'] ) ) {
				$map_icon_html = '<i class="' . esc_attr( $settings['map_icon']['class'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['map_icon'] ) && is_string( $settings['map_icon'] ) ) {
				$map_icon_html = '<i class="' . esc_attr( $settings['map_icon'] ) . '" aria-hidden="true"></i>';
			}
		}
		?>
		<div id="hotel-map-location" class="tf-location tf-single-widgets">
            <?php if ( $show_title == 'yes' ) : ?>
                <h3 class="tf-section-title"><?php esc_html_e("Location", "tourfic"); ?></h3>
            <?php endif; ?>
			<div class="tf-hotel-location-map tf-single-map">
				<?php if(!defined( 'TF_PRO' ) && $design == 'design-2') : ?>
					<div class="show-on-map">
						<div class="tf-btn-wrap"><a href="https://www.google.com/maps/search/<?php echo esc_attr( $address ); ?>" target="_blank" class="tf_btn tf_btn_full"><span><i class="fas fa-map-marker-alt"></i><?php esc_html_e( 'Show on map', 'tourfic' ); ?></span></a></div>
					</div>
				<?php else: ?>
					<?php if ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map!="default" && (empty($address_latitude) || empty($address_longitude)) ) { ?>
						<div class="tf-hotel-location-preview show-on-map">
							<iframe src="https://maps.google.com/maps?q=<?php echo wp_kses_post($address); ?>&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php if ( $show_icon == 'yes' ) : ?>
								<a href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>" class="map-pre" target="_blank"><i class="fa-solid fa-location-dot"></i></a>
							<?php endif; ?>
						</div>
					<?php } elseif ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {  ?>
						<div class="tf-hotel-location-preview show-on-map">
							<div id="hotel-location" style="height: <?php echo esc_attr( $height ); ?>;"></div>
						</div>
					<?php } elseif ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map=="default" && (empty($address_latitude) || empty($address_longitude)) ) {  ?>
						<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
					<?php } ?>
				<?php endif; ?>

				<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || ( ! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
					<?php if ( 'default' !== $tf_openstreet_map ) { ?>
						<div class="tf-hotel-location-preview show-on-map">
							<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

							<?php if ( $show_icon == 'yes' ) : ?>
							<a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post( $address ); ?>">
								<?php echo wp_kses( $map_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
							</a>
							<?php endif; ?>
						</div>
					<?php } ?>

					<?php if ( 'default' === $tf_openstreet_map && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) { ?>
						<div class="tf-hotel-location-preview show-on-map">
							<div id="hotel-location" class="tf-single-map-div" style="height: <?php echo esc_attr( $height ); ?>;"></div>

							<?php if ( $show_icon == 'yes' ) : ?>
							<a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post( $address ); ?>">
								<?php echo wp_kses( $map_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
							</a>
							<?php endif; ?>
						</div>
					<?php } ?>

					<?php if ( 'default' === $tf_openstreet_map && ( empty( $address_latitude ) || empty( $address_longitude ) ) ) { ?>
						<div class="tf-hotel-location-preview show-on-map">
							<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

							<?php if ( $show_icon == 'yes' ) : ?>
							<a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post( $address ); ?>">
								<?php echo wp_kses( $map_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
							</a>
							<?php endif; ?>
						</div>
					<?php } ?>

					<div style="display: none;" id="tf-hotel-google-maps">
						<div class="tf-hotel-google-maps-container">
							<?php if ( ! empty( $address ) ) { ?>
								<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( '#', '', $address ) ); ?>&z=17&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php } else { ?>
								<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=17&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>

		<?php if ( 'elementor' === $builder && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
		<script>
			jQuery(document).ready(function ($) {
				'use strict';
			
				if ($('#hotel-location').length) {
					const map = L.map('hotel-location').setView([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>], <?php echo esc_attr( $address_zoom ); ?>);

					const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
						maxZoom: 20,
						attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
					}).addTo(map);

					const marker = L.marker([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>]).addTo(map);
				}
			});	
		</script>
		<?php endif;
	}

	private static function tf_tour_map( $settings, $builder, $height = '' ) {
		$show_title = Helper::get_switcher_value( $settings, 'show_title', 'yes', $builder );
		$tf_openstreet_map  = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : 'default';
		$tf_google_map_key  = ! empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';
		$meta               = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
		$address            = '';
		$address_latitude   = '';
		$address_longitude  = '';
		$address_zoom       = '';

		if ( ! empty( $meta['location'] ) && Helper::tf_data_types( $meta['location'] ) ) {
			$address            = ! empty( Helper::tf_data_types( $meta['location'] )['address'] ) ? Helper::tf_data_types( $meta['location'] )['address'] : '';
			$address_latitude   = ! empty( Helper::tf_data_types( $meta['location'] )['latitude'] ) ? Helper::tf_data_types( $meta['location'] )['latitude'] : '';
			$address_longitude  = ! empty( Helper::tf_data_types( $meta['location'] )['longitude'] ) ? Helper::tf_data_types( $meta['location'] )['longitude'] : '';
			$address_zoom       = ! empty( Helper::tf_data_types( $meta['location'] )['zoom'] ) ? Helper::tf_data_types( $meta['location'] )['zoom'] : '';
		}
		$itinerary_map = ! empty( Helper::tfopt('itinerary_map') ) && function_exists('is_tf_pro') && is_tf_pro() ? Helper::tfopt('itinerary_map') : 0;
		$itineraries     = !empty($meta['itinerary']) ? Helper::tf_data_types( $meta['itinerary'] ) : null;

		// if ( $itinerary_map == 1 && $itineraries ){
		// 	return;
		// }
		?>
		<?php if ( ! empty( $meta['location'] ) ) : ?>
			<div class="tf-trip-map-wrapper tf-single-map" id="tf-tour-map">
                <?php if ( $show_title == 'yes' ) : ?>
					<h2 class="tf-title tf-section-title"><?php echo ! empty( $meta['map-section-title'] ) ? esc_html( $meta['map-section-title'] ) : ''; ?></h2>
                <?php endif; ?>
				<div class="tf-map-area">
					<?php if ( 'default' === $tf_openstreet_map && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) { ?>
						<div id="tour-location" class="tf-single-map-div" style="height: <?php echo esc_attr( $height ); ?>;"></div>
					<?php } ?>
					<?php if ( 'default' === $tf_openstreet_map && ( empty( $address_latitude ) || empty( $address_longitude ) ) ) { ?>
						<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( '#', '', $address ) ); ?>&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
					<?php } ?>
					<?php if ( 'default' !== $tf_openstreet_map && ! empty( $tf_google_map_key ) ) { ?>
					    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( '#', '', $address ) ); ?>&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
					<?php } ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( 'elementor' === $builder && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
		<script>
			jQuery(document).ready(function ($) {
				'use strict';
			
				if ($('#tour-location').length) {
					const map = L.map('tour-location').setView([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>], <?php echo esc_attr( $address_zoom ); ?>);
					
					const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
						maxZoom: 20,
						attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
					}).addTo(map);

					const marker = L.marker([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>]).addTo(map);
				}
			});	
		</script>
		<?php endif;
	}

	private static function tf_apartment_map( $settings, $builder, $height = '', $title = true ) {
        $design  	        = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';
		$show_title 		= Helper::get_switcher_value( $settings, 'show_title', 'yes', $builder );
		$tf_openstreet_map  = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : 'default';
		$tf_google_map_key  = ! empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';
		$meta               = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
		$map                = ! empty( $meta['map'] ) ? Helper::tf_data_types( $meta['map'] ) : '';
		$address            = '';
		$address_latitude   = '';
		$address_longitude  = '';
		$address_zoom       = '';

		if ( ! empty( $meta['map'] ) && Helper::tf_data_types( $meta['map'] ) ) {
			$address            = ! empty( Helper::tf_data_types( $meta['map'] )['address'] ) ? Helper::tf_data_types( $meta['map'] )['address'] : '';
			$address_latitude   = ! empty( Helper::tf_data_types( $meta['map'] )['latitude'] ) ? Helper::tf_data_types( $meta['map'] )['latitude'] : '';
			$address_longitude  = ! empty( Helper::tf_data_types( $meta['map'] )['longitude'] ) ? Helper::tf_data_types( $meta['map'] )['longitude'] : '';
			$address_zoom       = ! empty( Helper::tf_data_types( $meta['map'] )['zoom'] ) ? Helper::tf_data_types( $meta['map'] )['zoom'] : '';
		}
		?>
		<?php if ( ! empty( $map['address'] ) ) : ?>
			<div class="tf-apartment-map tf-single-map">
                <?php if( $design == 'design-1' && $show_title == 'yes'): ?>
					<h3 class="tf-section-title"><?php echo ! empty( $meta['location_title'] ) ? esc_html( $meta['location_title'] ) : ''; ?></h3>
                <?php elseif( $show_title == 'yes' ): ?>
					<h2 class="section-heading"><?php echo ! empty( $meta['location_title'] ) ? esc_html( $meta['location_title'] ) : ''; ?></h2>
                <?php endif; ?>   

				<?php if ( 'default' === $tf_openstreet_map && ! empty( $map['latitude'] ) && ! empty( $map['longitude'] ) ) { ?>
					<div id="apartment-location" class="tf-single-map-div" style="height: <?php echo esc_attr( $height ); ?>;"></div>
				<?php } elseif ( 'default' !== $tf_openstreet_map && ! empty( $tf_google_map_key ) ) { ?>
					<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( '#', '', $map['address'] ) ); ?>&output=embed" width="100%" height="<?php echo esc_attr( $height ); ?>" style="border:0;"
							allowfullscreen=""
							loading="lazy"></iframe>
				<?php } ?>
			</div>
		<?php endif; ?>

		<?php if ( 'elementor' === $builder && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
		<script>
			jQuery(document).ready(function ($) {
				'use strict';

				if ($('#apartment-location').length) {
					const map = L.map('apartment-location').setView([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>], <?php echo esc_attr( $address_zoom ); ?>);

					const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
						maxZoom: 20,
						attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
					}).addTo(map);

					const marker = L.marker([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>]).addTo(map);
				}
			});	
		</script>
		<?php endif;
	}

	private static function tf_car_map( $settings, $builder, $height = '' ) {
		$show_title = Helper::get_switcher_value( $settings, 'show_title', 'yes', $builder );
		$tf_openstreet_map  = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : 'default';
		$meta               = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
		$map                = ! empty( $meta['map'] ) ? Helper::tf_data_types( $meta['map'] ) : '';
		$location_title     = ! empty( $meta['location_title'] ) ? $meta['location_title'] : '';
		$address            = '';
		$address_latitude   = '';
		$address_longitude  = '';
		$address_zoom       = '';

		if ( ! empty( $meta['map'] ) && Helper::tf_data_types( $meta['map'] ) ) {
			$address            = ! empty( Helper::tf_data_types( $meta['map'] )['address'] ) ? Helper::tf_data_types( $meta['map'] )['address'] : '';
			$address_latitude   = ! empty( Helper::tf_data_types( $meta['map'] )['latitude'] ) ? Helper::tf_data_types( $meta['map'] )['latitude'] : '';
			$address_longitude  = ! empty( Helper::tf_data_types( $meta['map'] )['longitude'] ) ? Helper::tf_data_types( $meta['map'] )['longitude'] : '';
			$address_zoom       = ! empty( Helper::tf_data_types( $meta['map'] )['zoom'] ) ? Helper::tf_data_types( $meta['map'] )['zoom'] : '';
		}
		?>
		<?php if ( ! empty( $map['address'] ) ) : ?>
			<div class="tf-car-location" id="tf-location">
				<?php echo ( $show_title == 'yes' && ! empty( $location_title ) ) ? '<h3>' . esc_html( $location_title ) . '</h3>' : ''; ?>

				<div class="tf-car-location-map">
					<?php if ( 'default' === $tf_openstreet_map && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) { ?>
						<div id="car-location"></div>
					<?php } else { ?>
						<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( '#', '', $address ) ); ?>&output=embed" width="100%" height="260" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
					<?php } ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( 'elementor' === $builder && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
		<script>
			jQuery(document).ready(function ($) {
				'use strict';
			
				if ($('#car-location').length) {
					const map = L.map('car-location').setView([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>], <?php echo esc_attr( $address_zoom ); ?>);

					const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
						maxZoom: 20,
						attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
					}).addTo(map);

					const marker = L.marker([<?php echo esc_attr( $address_latitude ); ?>, <?php echo esc_attr( $address_longitude ); ?>]).addTo(map);
				}
			});	
		</script>
		<?php endif;
	}
}
