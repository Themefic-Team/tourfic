<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Itinerary Component
 */
class Itinerary {

	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type( $post_id );

		if ( 'tf_tours' !== $post_type ) {
			return;
		}

		$meta          = get_post_meta( $post_id, 'tf_tours_opt', true );
		$itineraries   = ! empty( Helper::tf_data_types( $meta['itinerary'] ) ) ? Helper::tf_data_types( $meta['itinerary'] ) : null;

		if ( empty( $itineraries ) ) {
			return;
		}

		$itinerary_map = ! empty( Helper::tfopt( 'itinerary_map' ) ) && function_exists( 'is_tf_pro' ) && is_tf_pro() ? Helper::tfopt( 'itinerary_map' ) : 0;
		$style         = ! empty( $settings['itinerary_style'] ) ? $settings['itinerary_style'] : 'style1';
		$tf_openstreet_map         = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : 'default';
		$tf_google_map_key         = ! empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';

		$location            = '';
		$location_latitude   = '';
		$location_longitude  = '';
		if ( ! empty( $meta['location'] ) && Helper::tf_data_types( $meta['location'] ) ) {
			$location           = ! empty( Helper::tf_data_types( $meta['location'] )['address'] ) ? Helper::tf_data_types( $meta['location'] )['address'] : $location;
			$location_latitude  = ! empty( Helper::tf_data_types( $meta['location'] )['latitude'] ) ? Helper::tf_data_types( $meta['location'] )['latitude'] : '';
			$location_longitude = ! empty( Helper::tf_data_types( $meta['location'] )['longitude'] ) ? Helper::tf_data_types( $meta['location'] )['longitude'] : '';
		}

		$wrapper_open          = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close         = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style ) {
			self::render_style1( $settings, $style, $itineraries, $itinerary_map, $tf_openstreet_map, $tf_google_map_key, $location, $location_latitude, $location_longitude );
		} elseif ( 'style2' === $style ) {
			self::render_style2( $settings, $style, $itineraries, $itinerary_map, $tf_openstreet_map, $tf_google_map_key, $location, $location_latitude, $location_longitude );
		} elseif ( 'style3' === $style ) {
			self::render_style3( $settings, $style, $itineraries, $itinerary_map, $tf_openstreet_map, $tf_google_map_key, $location, $location_latitude, $location_longitude );
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	private static function render_style1( $settings, $style, $itineraries, $itinerary_map, $tf_openstreet_map, $tf_google_map_key, $location, $location_latitude, $location_longitude ) {
		$itinerary_data = [];
		$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
		?>
		<div class="tf-single-template__one tf-single-itinerary-style1 sp-0">
			<?php
			if ( function_exists('is_tf_pro') && is_tf_pro() ) {
				do_action( 'after_itinerary_builder', $itineraries, $itinerary_map, $settings, $style );
			} else {
				if ( $itineraries ) { ?>
				<div class="tf-itinerary-wrapper tf-mb-50 tf-template-section">
					<div class="section-title">
						<h2 class="tf-title tf-section-title"><?php echo !empty( $meta['itinerary-section-title'] ) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
					</div>
					<div class="tf-itinerary-box tf-box">
						<div class="tf-itinerary-items">
							<?php 
							$itineray_key = 1;
							foreach ( $itineraries as $itinerary ) {
							?>
							<div class="tf-single-itinerary-item <?php echo $itineray_key==1 ? esc_attr( 'active' ) : ''; ?>">
								<div class="tf-itinerary-title">
									<h4>
										<span class="accordion-checke"></span>
										<span class="itinerary-day"><?php echo esc_html( $itinerary['time'] ) ?> - </span> <?php echo esc_html( $itinerary['title'] ); ?>
									</h4>
								</div>
								<div class="tf-itinerary-content-box" style="<?php echo $itineray_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
									<div class="tf-itinerary-content tf-mt-16 tf-flex-gap-16 tf-flex">
										<?php if ( $itinerary['image'] ) { ?>
											<div class="tf-itinerary-content-img">
												<img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php esc_html_e("Itinerary Image","tourfic"); ?>" />
											</div>
										<?php } ?>
										<div class="<?php echo !empty($itinerary['image']) ? esc_attr('tf-itinerary-content-details') : ''; ?>">
										<p><?php echo wp_kses_post( wpautop($itinerary['desc']) ); ?></p>
										</div>
									</div>
								</div>
							</div>
							<?php $itineray_key++; } ?>
						</div>
					</div>
				</div>
				<?php if ( $location && $itinerary_map != 1 ): ?>
					<div class="tf-trip-map-wrapper tf-mb-50 tf-template-section" id="tf-tour-map">
						<h2 class="tf-title tf-section-title"><?php echo !empty($meta['map-section-title']) ? esc_html($meta['map-section-title']) : ''; ?></h2>
						<div class="tf-map-area">
							<?php if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) && empty($tf_google_map_key) ) {  ?>
								<div id="tour-location"></div>
							<?php } ?>
							<?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) && empty($tf_google_map_key) ) {  ?>
								<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php } ?>
							<?php if( $tf_openstreet_map!="default" && !empty($tf_google_map_key) ){ ?>
							<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php } ?>
						</div>
					</div>
				<?php endif; ?>
			<?php }
			}
			?>
		</div>
		<?php
	}

	private static function render_style2( $settings, $style, $itineraries, $itinerary_map, $tf_openstreet_map, $tf_google_map_key, $location, $location_latitude, $location_longitude ) {
		$itinerary_data = [];
		$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
		?>
		<div class="tf-single-template__two tf-single-itinerary-style2">
			<?php
			if ( function_exists('is_tf_pro') && is_tf_pro() ) {
				do_action( 'after_itinerary_builder', $itineraries, $itinerary_map, $settings, $style );
			} else {
				if ( $itineraries ) { ?>
					<div class="tf-itinerary-wrapper" id="tf-tour-itinerary">
						<div class="section-title">
							<h2 class="tf-title tf-section-title"><?php echo !empty($meta['itinerary-section-title']) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
						</div>
						<div class="tf-itinerary-wrapper">
							<?php foreach ( $itineraries as $itinerary ) { ?>
								<div class="tf-single-itinerary">
									<div class="tf-itinerary-title">
										<span class="tf-head-title">
											<span class="tf-itinerary-time">
												<?php echo esc_html( $itinerary['time'] ) ?>
											</span>
											<span class="tf-itinerary-title-text">
												<?php echo esc_html( $itinerary['title'] ); ?>
											</span>
										</span>
										<i class="fa-solid fa-chevron-down"></i>
									</div>
									<div class="tf-itinerary-content-wrap" style="display: none;">
										<div class="tf-itinerary-content">
											<div class="tf-itinerary-content-details">
												<?php echo wp_kses_post( $itinerary['desc'] ); ?>
											</div>
											<?php if ( $itinerary['image'] ) { ?>
											<div class="tf-itinerary-content-images">
												<img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php esc_html_e("Itinerary Image","tourfic"); ?>" />
											</div>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php 
				} 
				if ( $location && $itinerary_map != 1 ){
					\Tourfic\App\Templates\Components\Shared\Single\Map::render([
						'wrapper_open' => '<div class="tf-mt-16 tf-mb-30">',
						'wrapper_close' => '</div>',
						'show_title' => 'no',
					], '', '450px');
				} 
			}
			?>
		</div>
		<?php
	}

	private static function render_style3( $settings, $style, $itineraries, $itinerary_map, $tf_openstreet_map, $tf_google_map_key, $location, $location_latitude, $location_longitude ) {
		$itinerary_data = [];
		$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
		$wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
		?>
		<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-single-itinerary-legacy sp-0">' : ''; ?>
			<?php
			if ( function_exists('is_tf_pro') && is_tf_pro() ) {
				do_action( 'after_itinerary_builder', $itineraries, $itinerary_map, $settings, $style );
			} else {
				if ( $itineraries ) { ?>
					<div class="tf-travel-itinerary-wrapper sp-50">
						<div class="tf-container">
							<div class="tf-travel-itinerary-content">
								<h2 class="section-heading"><?php echo !empty($meta['itinerary-section-title']) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
								<div class="tf-travel-itinerary-items-wrapper">
									<?php 
									foreach ( $itineraries as $itinerary ) {
									?>
										<div id="tf-accordion-wrapper">
											<div class="tf-accordion-head">
												<div class="tf-travel-time">
													<span><?php echo esc_html( $itinerary['time'] ) ?></span>
												</div>
												<h4><?php echo esc_html( $itinerary['title'] ); ?></h4>
												<i class="fas fa-angle-down arrow"></i>
											</div>
											<div class="tf-accordion-content">
												<div class="tf-travel-desc">
													<?php if ( $itinerary['image'] ) {
														echo '<div class="tf-ititnerary-img"><a class="tf-itinerary-gallery" href="' . esc_url( $itinerary['image'] ) . '"><img src="' . esc_url( $itinerary['image'] ) . '"></a></div>';
													} ?>
													<div class="trav-cont tf-travel-description">
														<p><?php echo wp_kses_post( $itinerary['desc'] ); ?></p>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php if ( $location && $itinerary_map != 1){
						\Tourfic\App\Templates\Components\Shared\Single\Map::render([
							'show_title' => 'no',
						], '', '600px');
					} ?>
				<?php
				}
			} 
			?>
		<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
		<?php
	}
}