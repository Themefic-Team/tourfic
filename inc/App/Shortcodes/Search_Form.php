<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Apartment\Apartment;

class Search_Form extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_search_form';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'style'     => 'default',
					'type'      => 'all',
					'hotel_tab_title' => esc_html__("Hotel", 'tourfic'),
					'tour_tab_title' => esc_html__("Tour", 'tourfic'),
					'apartment_tab_title' => esc_html__("Apartment", 'tourfic'),
					'title'     => '',
					'subtitle'  => '',
					'classes'   => '',
					'fullwidth' => '',
					'advanced'  => '',
					'author'    => '',
					'design'	=> 1
				),
				$atts
			)
		);

		if ( $style == 'default' ) {
			$classes .= " default-form ";
		}

		$type             = explode( ',', $type );
		$disable_services = Helper::tfopt( 'disable-services' ) ? Helper::tfopt( 'disable-services' ) : array();
		$child_age_limit  = Helper::tfopt( 'enable_child_age_limit' ) ? Helper::tfopt( 'enable_child_age_limit' ) : '';
		if ( $child_age_limit == '1' ) {
			$child_age_limit = ' child-age-limited';
		} else {
			$child_age_limit = '';
		}

		ob_start();
		?>

		<?php if ( $fullwidth == "true" ) : ?>
            <!-- Start Fullwidth Wrap -->
            <div class="tf_tf_booking-widget-wrap" data-fullwidth="true">
            <div class="tf_custom-container">
            <div class="tf_custom-inner">

		<?php endif; ?>
		<div id="tf-booking-search-tabs" class="<?php echo esc_attr($classes) ?> <?php echo 2==$design ? esc_attr('tf-shortcode-design-2-tab') : ''; ?>">

			<?php if ( $title ): ?>
				<div class="tf_widget-title"><h2><?php echo esc_html( $title ); ?></h2></div>
			<?php endif; ?>

			<?php if ( $subtitle ): ?>
				<div class="tf_widget-subtitle"><p><?php echo esc_html( $subtitle ); ?></p></div>
			<?php endif; ?>
			<!-- Booking Form Tabs -->
			<div class="tf-booking-form-tab">
				<?php do_action( 'tf_before_booking_form_tab', $type ) ?>

				<?php if ( ! in_array( 'hotel', $disable_services ) && Helper::tf_is_search_form_tab_type( 'hotel', $type ) && ! Helper::tf_is_search_form_single_tab( $type ) ) : ?>
					<button class="tf-tablinks btn-styled active" data-form-id="tf-hotel-booking-form"><?php esc_html_e( apply_filters("tf_hotel_search_form_tab_button_text", $hotel_tab_title) , 'tourfic' ); ?></button>
				<?php endif; ?>

				<?php if ( ! in_array( 'tour', $disable_services ) && Helper::tf_is_search_form_tab_type( 'tour', $type ) && ! Helper::tf_is_search_form_single_tab( $type ) ) : ?>
					<button class="tf-tablinks btn-styled" data-form-id="tf-tour-booking-form"><?php esc_html_e( apply_filters("tf_tour_search_form_tab_button_text",$tour_tab_title ) , 'tourfic' ); ?></button>
				<?php endif ?>

				<?php if ( ! in_array( 'apartment', $disable_services ) && Helper::tf_is_search_form_tab_type( 'apartment', $type ) && ! Helper::tf_is_search_form_single_tab( $type ) ) : ?>
					<button class="tf-tablinks btn-styled" data-form-id="tf-apartment-booking-form"><?php esc_html_e( apply_filters("tf_apartment_search_form_tab_button_text", $apartment_tab_title ) , 'tourfic' ); ?></button>
				<?php endif ?>

				<?php do_action( 'tf_after_booking_form_tab', $type ) ?>
			</div>

			<?php if ( ! Helper::tf_is_search_form_single_tab( $type ) ): ?>
				<!-- Booking Form tabs mobile version -->
				<div class="tf-booking-form-tab-mobile">
					<select name="tf-booking-form-tab-select" id="">
						<?php do_action( 'tf_before_booking_form_mobile_tab', $type ) ?>

						<?php if ( ! in_array( 'hotel', $disable_services ) && Helper::tf_is_search_form_tab_type( 'hotel', $type ) && ! Helper::tf_is_search_form_single_tab( $type ) ) : ?>
							<option value="tf-hotel-booking-form"><?php esc_html_e( apply_filters("tf_hotel_search_form_tab_button_text", $hotel_tab_title) , 'tourfic' ); ?></option>
						<?php endif; ?>
						<?php if ( ! in_array( 'tour', $disable_services ) && Helper::tf_is_search_form_tab_type( 'tour', $type ) && ! Helper::tf_is_search_form_single_tab( $type ) ) : ?>
							<option value="tf-tour-booking-form"><?php esc_html_e( apply_filters("tf_tour_search_form_tab_button_text",$tour_tab_title) , 'tourfic' ); ?></option>
						<?php endif ?>
						<?php if ( ! in_array( 'apartment', $disable_services ) && Helper::tf_is_search_form_tab_type( 'apartment', $type ) && ! Helper::tf_is_search_form_single_tab( $type ) ) : ?>
							<option value="tf-apartment-booking-form"><?php esc_html_e( apply_filters("tf_apartment_search_form_tab_button_text", $apartment_tab_title) , 'tourfic' ); ?></option>
						<?php endif ?>

						<?php do_action( 'tf_after_booking_form_mobile_tab', $type ) ?>
					</select>
				</div>
			<?php endif; ?>

			<!-- Booking Forms -->
			<div class="tf-booking-forms-wrapper">
				<?php
				do_action( 'tf_before_booking_form', $classes, $title, $subtitle, $type );

				if ( ! in_array( 'hotel', $disable_services ) && Helper::tf_is_search_form_tab_type( 'hotel', $type ) ) {
					?>
					<div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent <?php echo esc_attr( $child_age_limit ); ?>">
						<?php
						Hotel::tf_hotel_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design );
						?>
					</div>
					<?php
				}
				if ( ! in_array( 'tour', $disable_services ) && Helper::tf_is_search_form_tab_type( 'tour', $type ) ) {
					?>
					<div id="tf-tour-booking-form" class="tf-tabcontent" <?php echo Helper::tf_is_search_form_single_tab( $type ) ? 'style="display:block"' : '' ?><?php echo esc_attr( $child_age_limit ); ?>>
						<?php \Tourfic\Classes\Tour\Tour::tf_tour_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design ); ?>
					</div>
					<?php
				}
				if ( ! in_array( 'apartment', $disable_services ) && Helper::tf_is_search_form_tab_type( 'apartment', $type ) ) {
					?>
					<div id="tf-apartment-booking-form" class="tf-tabcontent" <?php echo Helper::tf_is_search_form_single_tab( $type ) ? 'style="display:block"' : '' ?><?php echo esc_attr( $child_age_limit ); ?>>
						<?php
						if ( $advanced == "enabled" ) {
							$advanced_opt = true;
							Apartment::tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced_opt, $design );
						} else {
							$advanced_opt = false;
							Apartment::tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced_opt, $design );
						}
						?>
					</div>
					<?php
				}

				do_action( 'tf_after_booking_form', $classes, $title, $subtitle, $type );
				?>
			</div>

		</div>
		<?php if ( $fullwidth == "true" ) : ?>
            </div>
            </div>
            </div>
            <!-- Close Fullwidth Wrap -->
		<?php endif;

		return ob_get_clean();
	}
}