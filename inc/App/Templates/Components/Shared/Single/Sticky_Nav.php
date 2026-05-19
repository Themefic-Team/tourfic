<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Car_Rental\Pricing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Sticky Nav Component
 */
class Sticky_Nav {

	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		$comments_query = new \WP_Comment_Query(
			[
				'post_id' => $post_id,
				'status'  => 'approve',
				'type'    => 'comment',
			]
		);
		$comments = $comments_query->comments;

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'tf_hotel' === $post_type ) {
			self::tf_hotel_sticky_nav( $post_id, $comments );
		} elseif ( 'tf_tours' === $post_type ) {
			self::tf_tour_sticky_nav( $post_id, $comments );
		} elseif ( 'tf_apartment' === $post_type ) {
			self::tf_apartment_sticky_nav( $post_id, $comments );
		} elseif ( 'tf_carrental' === $post_type ) {
			self::tf_car_sticky_nav( $post_id );
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	private static function tf_hotel_sticky_nav( $post_id, $comments ) {
		$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
		?>
		<div class="tf-single-hotel-sticky-nav tf-single-template__two">
			<div class="tf-details-menu tf-hotel-details-menu">
				<ul>
					<?php if ( ! empty( Helper::get_status_by_label( 'Description', 'hotel' ) ) ) : ?>
						<li><a class="tf-hashlink" href="#tf-hotel-overview"><?php esc_html_e( 'Overview', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Room', 'hotel' ) ) && ! empty( $meta['tf_rooms'] ) ) : ?>
						<li><a href="#tf-hotel-rooms"><?php esc_html_e( 'Rooms', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Facilities', 'hotel' ) ) && ! empty( $meta['hotel-facilities'] ) ) : ?>
						<li><a href="#tf-hotel-facilities"><?php esc_html_e( 'Facilities', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Review', 'hotel' ) ) && ! empty( $comments ) ) : ?>
						<li><a href="#tf-hotel-reviews"><?php esc_html_e( 'Reviews', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'FAQ', 'hotel' ) ) && ! empty( $meta['faq'] ) ) : ?>
						<li><a href="#tf-hotel-faq"><?php esc_html_e( "FAQ's", 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Terms & Conditions', 'hotel' ) ) && ! empty( $meta['tc'] ) ) : ?>
						<li><a href="#tf-hotel-policies"><?php esc_html_e( 'Policies', 'tourfic' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	private static function tf_tour_sticky_nav( $post_id, $comments ) {
		$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
		?>
		<div class="tf-single-tour-sticky-nav tf-single-template__two">
			<div class="tf-details-menu tf-tour-details-menu">
				<ul>
					<?php if ( ! empty( Helper::get_status_by_label( 'Description', 'tour' ) ) ) : ?>
						<li><a class="tf-hashlink" href="#tf-tour-overview"><?php esc_html_e( 'Overview', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Itinerary', 'tour' ) ) && ! empty( $meta['itinerary'] ) ) : ?>
						<li><a href="#tf-tour-itinerary"><?php esc_html_e( 'Tour Plan', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'FAQ', 'tour' ) ) && ! empty( $meta['faqs'] ) ) : ?>
						<li><a href="#tf-tour-faq"><?php esc_html_e( "FAQ's", 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Terms & Conditions', 'tour' ) ) && ! empty( $meta['terms_conditions'] ) ) : ?>
						<li><a href="#tf-tour-policies"><?php esc_html_e( 'Policies', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Review', 'tour' ) ) && ! empty( $comments ) ) : ?>
						<li><a href="#tf-tour-reviews"><?php esc_html_e( 'Reviews', 'tourfic' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	private static function tf_apartment_sticky_nav( $post_id, $comments ) {
		$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
		?>
		<div class="tf-single-apartment-sticky-nav tf-single-template__two">
			<div class="tf-details-menu tf-apartment-details-menu">
				<ul>
					<?php if ( ! empty( Helper::get_status_by_label( 'Description', 'apartment' ) ) ) : ?>
						<li><a class="tf-hashlink" href="#tf-apartment-overview"><?php esc_html_e( 'Overview', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Apartment Rooms', 'apartment' ) ) && ! empty( $meta['rooms'] ) ) : ?>
						<li><a href="#tf-apartment-rooms"><?php esc_html_e( 'Rooms', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'House Rules', 'apartment' ) ) && ! empty( $meta['house_rules'] ) ) : ?>
						<li><a href="#tf-apartment-rules"><?php esc_html_e( 'House Rules', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'FAQ', 'apartment' ) ) && ! empty( $meta['faq'] ) ) : ?>
						<li><a href="#tf-apartment-faq"><?php esc_html_e( "FAQ's", 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Review', 'apartment' ) ) && ! empty( $comments ) ) : ?>
						<li><a href="#tf-apartment-reviews"><?php esc_html_e( 'Reviews', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Terms & Conditions', 'apartment' ) ) && ! empty( $meta['terms_and_conditions'] ) ) : ?>
						<li><a href="#tf-apartment-policies"><?php esc_html_e( 'Policies', 'tourfic' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	private static function tf_car_sticky_nav( $post_id ) {
		$meta      = get_post_meta( $post_id, 'tf_carrental_opt', true );
		$benefits  = ! empty( $meta['benefits'] ) ? $meta['benefits'] : '';
		$includes  = ! empty( $meta['inc'] ) ? $meta['inc'] : '';
		$excludes  = ! empty( $meta['exc'] ) ? $meta['exc'] : '';
		$map_data  = Helper::tf_data_types( $meta['map'] ?? [] );
		$address   = ! empty( $map_data['address'] ) ? $map_data['address'] : '';
		$faqs      = ! empty( $meta['faq'] ) ? $meta['faq'] : '';
		$tc        = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';
		$pickup_date_query = ! empty( $_GET['pickup_date'] ) ? sanitize_text_field( wp_unslash( $_GET['pickup_date'] ) ) : '';
		if ( empty( $pickup_date_query ) && ! empty( $_GET['pickup-date'] ) ) {
			$pickup_date_query = sanitize_text_field( wp_unslash( $_GET['pickup-date'] ) );
		}
		$dropoff_date_query = ! empty( $_GET['dropoff_date'] ) ? sanitize_text_field( wp_unslash( $_GET['dropoff_date'] ) ) : '';
		if ( empty( $dropoff_date_query ) && ! empty( $_GET['dropoff-date'] ) ) {
			$dropoff_date_query = sanitize_text_field( wp_unslash( $_GET['dropoff-date'] ) );
		}

		$tf_pickup_date  = ! empty( $pickup_date_query ) && function_exists( 'tf_normalize_date' ) ? tf_normalize_date( $pickup_date_query ) : $pickup_date_query;
		$tf_dropoff_date = ! empty( $dropoff_date_query ) && function_exists( 'tf_normalize_date' ) ? tf_normalize_date( $dropoff_date_query ) : $dropoff_date_query;

		$disable_car_time_slot    = ! empty( Helper::tfopt( 'disable-car-time-slots' ) ) ? boolval( Helper::tfopt( 'disable-car-time-slots' ) ) : false;
		$car_time_slots           = ! empty( Helper::tfopt( 'car_time_slots' ) ) ? Helper::tfopt( 'car_time_slots' ) : '';
		$unserialize_time_slots   = ! empty( $car_time_slots ) ? unserialize( $car_time_slots ) : [];

		$time_interval     = 30;
		$start_time_str    = '00:00';
		$end_time_str      = '23:30';
		$default_time_str  = '10:00';
		$next_current_day  = gmdate( 'l', strtotime( '+1 day' ) );

		if ( $disable_car_time_slot ) {
			$time_interval = ! empty( Helper::tfopt( 'car_time_interval' ) ) ? intval( Helper::tfopt( 'car_time_interval' ) ) : 30;

			if ( ! empty( $unserialize_time_slots ) ) {
				foreach ( $unserialize_time_slots as $slot ) {
					if ( isset( $slot['day'] ) && strtolower( $slot['day'] ) === strtolower( $next_current_day ) ) {
						$start_time_str = ! empty( $slot['pickup_time'] ) ? $slot['pickup_time'] : $start_time_str;
						$end_time_str   = ! empty( $slot['drop_time'] ) ? $slot['drop_time'] : $end_time_str;
						if ( strtotime( $start_time_str ) >= strtotime( '10:00' ) ) {
							$default_time_str = $start_time_str;
						}
						break;
					}
				}
			}
		}

		$default_time = gmdate( 'g:i A', strtotime( $default_time_str ) );
		$selected_pickup_time = ! empty( $_GET['pickup_time'] ) ? sanitize_text_field( wp_unslash( $_GET['pickup_time'] ) ) : '';
		if ( empty( $selected_pickup_time ) && ! empty( $_GET['pickup-time'] ) ) {
			$selected_pickup_time = sanitize_text_field( wp_unslash( $_GET['pickup-time'] ) );
		}
		if ( empty( $selected_pickup_time ) ) {
			$selected_pickup_time = $default_time;
		}

		$selected_dropoff_time = ! empty( $_GET['dropoff_time'] ) ? sanitize_text_field( wp_unslash( $_GET['dropoff_time'] ) ) : '';
		if ( empty( $selected_dropoff_time ) && ! empty( $_GET['dropoff-time'] ) ) {
			$selected_dropoff_time = sanitize_text_field( wp_unslash( $_GET['dropoff-time'] ) );
		}
		if ( empty( $selected_dropoff_time ) ) {
			$selected_dropoff_time = $default_time;
		}

		$total_prices = Pricing::set_total_price( $meta, $tf_pickup_date, $tf_dropoff_date, $selected_pickup_time, $selected_dropoff_time );
		$show_total_regular_price = ! empty( $total_prices['regular_price'] ) && (float) $total_prices['regular_price'] > (float) $total_prices['sale_price'];
		$display_total_price = ! empty( $total_prices['sale_price'] ) ? $total_prices['sale_price'] : ( ! empty( $total_prices['regular_price'] ) ? $total_prices['regular_price'] : 0 );
		?>
		<div class="tf-single-car-sticky-nav tf-single-template__one sp-0">
			<div class="tf-details-menu tf-car-details-menu">
				<ul>
					<?php if ( ! empty( Helper::get_status_by_label( 'Description', 'car' ) ) ) : ?>
						<li class="active" data-menu="<?php echo esc_attr( 'tf-description' ); ?>"><a class="tf-hashlink" href="#tf-description"><?php esc_html_e( 'Description', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Car info', 'car' ) ) ) : ?>
						<li data-menu="<?php echo esc_attr( 'tf-car-info' ); ?>"><a class="tf-hashlink" href="#tf-car-info"><?php esc_html_e( 'Car info', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Benefits', 'car' ) ) && ! empty( $benefits ) ) : ?>
						<li data-menu="<?php echo esc_attr( 'tf-benefits' ); ?>"><a class="tf-hashlink" href="#tf-benefits"><?php esc_html_e( 'Benefits', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Include/Exclude', 'car' ) ) && ( ! empty( $includes ) || ! empty( $excludes ) ) ) : ?>
						<li data-menu="<?php echo esc_attr( 'tf-inc-exc' ); ?>"><a class="tf-hashlink" href="#tf-inc-exc"><?php esc_html_e( 'Include/Exclude', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Location', 'car' ) ) && ! empty( $address ) ) : ?>
						<li data-menu="<?php echo esc_attr( 'tf-location' ); ?>"><a class="tf-hashlink" href="#tf-location"><?php esc_html_e( 'Location', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Review', 'car' ) ) ) : ?>
						<li data-menu="<?php echo esc_attr( 'tf-reviews' ); ?>"><a class="tf-hashlink" href="#tf-reviews"><?php esc_html_e( 'Reviews', 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'FAQs', 'car' ) ) && ! empty( $faqs ) ) : ?>
						<li data-menu="<?php echo esc_attr( 'tf-faq' ); ?>"><a class="tf-hashlink" href="#tf-faq"><?php esc_html_e( "FAQ's", 'tourfic' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( Helper::get_status_by_label( 'Terms & Conditions', 'car' ) ) && ! empty( $tc ) ) : ?>
						<li data-menu="<?php echo esc_attr( 'tf-tc' ); ?>"><a class="tf-hashlink" href="#tf-tc"><?php esc_html_e( 'Terms & Conditions', 'tourfic' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php

		unset( $time_interval, $selected_pickup_time, $selected_dropoff_time );
	}
}
