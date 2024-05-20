<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

class Migrator {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Helper;

	public function __construct() {
		add_action( 'init', array( $this, 'tf_permalink_settings_migration' ) );
		add_action( 'init', array( $this, 'tf_template_3_migrate_data' ) );
		add_action( 'init', array( $this, 'tf_migrate_option_data' ) );
		add_action( 'init', array( $this, 'tf_migrate_data' ) );
	}

	function tf_permalink_settings_migration() {

		if ( empty( get_option( 'tf_permalink_settings_migration' ) ) ) {

			$options              = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
			$hotel_permalink_slug = ! empty( get_option( 'hotel_slug' ) ) ? get_option( 'hotel_slug' ) : '';
			$tour_permalink_slug  = ! empty( get_option( 'tour_slug' ) ) ? get_option( 'tour_slug' ) : '';
			$apt_permalink_slug   = ! empty( get_option( 'apartment_slug' ) ) ? get_option( 'apartment_slug' ) : '';

			if ( ! empty( $hotel_permalink_slug ) ) {
				$options["hotel-permalink-setting"] = $hotel_permalink_slug;
			}

			if ( ! empty( $tour_permalink_slug ) ) {
				$options["tour-permalink-setting"] = $tour_permalink_slug;
			}

			if ( ! empty( $apt_permalink_slug ) ) {
				$options["apartment-permalink-setting"] = $apt_permalink_slug;
			}

			update_option( 'tf_settings', $options );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_permalink_settings_migration', 1 );

		}
	}

	/**
	 * Template 3 Default Data Migration v2.10.3
	 *
	 * run once
	 */
	function tf_template_3_migrate_data() {

		// Hotel & Tour
		if ( empty( get_option( 'tf_template_3_migrate_data' ) ) ) {

			$options = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();

			$options["tf-template"]["single-hotel-layout"] = array(
				array(
					"hotel-section"        => "Description",
					"hotel-section-slug"   => "description",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Features",
					"hotel-section-slug"   => "features",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Room",
					"hotel-section-slug"   => "rooms",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "FAQ",
					"hotel-section-slug"   => "faq",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Review",
					"hotel-section-slug"   => "review",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Terms & Conditions",
					"hotel-section-slug"   => "trams-condition",
					"hotel-section-status" => "1"
				)
			);

			$options["tf-template"]["single-hotel-layout-part-1"] = array(
				array(
					"hotel-section"        => "Description",
					"hotel-section-slug"   => "description",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Features",
					"hotel-section-slug"   => "features",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Room",
					"hotel-section-slug"   => "rooms",
					"hotel-section-status" => "1"
				)
			);
			$options["tf-template"]["single-hotel-layout-part-2"] = array(
				array(
					"hotel-section"        => "Facilities",
					"hotel-section-slug"   => "facilities",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Review",
					"hotel-section-slug"   => "review",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "FAQ",
					"hotel-section-slug"   => "faq",
					"hotel-section-status" => "1"
				),
				array(
					"hotel-section"        => "Terms & Conditions",
					"hotel-section-slug"   => "trams-condition",
					"hotel-section-status" => "1"
				)
			);

			$options["tf-template"]["single-tour-layout"] = array(
				array(
					"tour-section"        => "Gallery",
					"tour-section-slug"   => "gallery",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Price",
					"tour-section-slug"   => "price",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Description",
					"tour-section-slug"   => "description",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Information",
					"tour-section-slug"   => "information",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Highlights",
					"tour-section-slug"   => "highlights",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Include Exclude",
					"tour-section-slug"   => "include-exclude",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Itinerary",
					"tour-section-slug"   => "itinerary",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Map",
					"tour-section-slug"   => "map",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "FAQ",
					"tour-section-slug"   => "faq",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Terms & Conditions",
					"tour-section-slug"   => "trams-condition",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Review",
					"tour-section-slug"   => "review",
					"tour-section-status" => "1"
				)
			);


			$options["tf-template"]["single-tour-layout-part-1"] = array(
				array(
					"tour-section"        => "Description",
					"tour-section-slug"   => "description",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Information",
					"tour-section-slug"   => "information",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Highlights",
					"tour-section-slug"   => "highlights",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Include & Exclude",
					"tour-section-slug"   => "include-exclude",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Itinerary",
					"tour-section-slug"   => "itinerary",
					"tour-section-status" => "1"
				)
			);

			$options["tf-template"]["single-tour-layout-part-2"] = array(
				array(
					"tour-section"        => "FAQ",
					"tour-section-slug"   => "faq",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Review",
					"tour-section-slug"   => "review",
					"tour-section-status" => "1"
				),
				array(
					"tour-section"        => "Terms & Conditions",
					"tour-section-slug"   => "trams-condition",
					"tour-section-status" => "1"
				)
			);


			update_option( 'tf_settings', $options );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_template_3_migrate_data', 1 );

		}

		// Apartment
		if ( empty( get_option( 'tf_template_2_apartment_migrate_data' ) ) ) {

			$options = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();

			$options["tf-template"]["single-aprtment-layout-part-1"] = array(
				array(
					"aprtment-section"        => "Description",
					"aprtment-section-slug"   => "description",
					"aprtment-section-status" => "1"
				),
				array(
					"aprtment-section"        => "Highlights",
					"aprtment-section-slug"   => "features",
					"aprtment-section-status" => "1"
				),
				array(
					"aprtment-section"        => "Apartment Rooms",
					"aprtment-section-slug"   => "rooms",
					"aprtment-section-status" => "1"
				),
				array(
					"aprtment-section"        => "Place offer",
					"aprtment-section-slug"   => "offer",
					"aprtment-section-status" => "1"
				),
				array(
					"aprtment-section"        => "House Rules",
					"aprtment-section-slug"   => "rules",
					"aprtment-section-status" => "1"
				),
				array(
					"aprtment-section"        => "Amenities",
					"aprtment-section-slug"   => "facilities",
					"aprtment-section-status" => "1"
				)
			);
			$options["tf-template"]["single-aprtment-layout-part-2"] = array(
				array(
					"aprtment-section"        => "Review",
					"aprtment-section-slug"   => "review",
					"aprtment-section-status" => "1"
				),
				array(
					"aprtment-section"        => "FAQ",
					"aprtment-section-slug"   => "faq",
					"aprtment-section-status" => "1"
				),
				array(
					"aprtment-section"        => "Terms & Conditions",
					"aprtment-section-slug"   => "trams-condition",
					"aprtment-section-status" => "1"
				)
			);

			update_option( 'tf_settings', $options );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_template_2_apartment_migrate_data', 1 );

		}
	}

	/**
	 * Migrate data from v2.0.4 to v2.1.0
	 *
	 * run once
	 */
	function tf_migrate_data() {
		if ( get_option( 'tf_migrate_data_204_210' ) < 1 ) {

			global $wpdb;
			// $wpdb->update( $wpdb->posts, [ 'post_type' => 'tf_hotel' ], [ 'post_type' => 'tourfic' ] );
			// $wpdb->update( $wpdb->term_taxonomy, [ 'taxonomy' => 'hotel_location' ], [ 'taxonomy' => 'destination' ] );
			// $wpdb->update( $wpdb->term_taxonomy, [ 'taxonomy' => 'hotel_feature' ], [ 'taxonomy' => 'tf_filters' ] );


			/** Hotels Migrations */
			$hotels = get_posts( [ 'post_type' => 'tf_hotel', 'numberposts' => - 1, ] );


			foreach ( $hotels as $hotel ) {
				$old_meta = get_post_meta( $hotel->ID );
				if ( ! empty( $old_meta['tf_hotel'] ) ) {
					continue;
				}
				$new_meta = [];
				if ( ! empty( $old_meta['formatted_location'] ) ) {
					$new_meta['address'] = join( ',', $old_meta['formatted_location'] );
				}
				if ( ! empty( $old_meta['tf_gallery_ids'] ) ) {
					$new_meta['gallery'] = join( ',', $old_meta['tf_gallery_ids'] );
				}
				if ( ! empty( $old_meta['additional_information'] ) ) {
					$new_meta['highlights'] = $old_meta['additional_information'];
				}
				if ( ! empty( $old_meta['terms_and_conditions'] ) ) {
					$new_meta['tc'] = join( ' ', $old_meta['terms_and_conditions'] );
				}

				if ( ! empty( $old_meta['tf_room'] ) ) {
					$rooms = unserialize( $old_meta['tf_room'][0] );
					foreach ( $rooms as $room ) {
						$new_meta['room'][] = [
							"enable"      => "1",
							"title"       => $room['name'],
							"adult"       => $room['pax'],
							"description" => $room['short_desc'],
							"pricing-by"  => "1",
							"price"       => $room['sale_price'] ?? $room['price'],
						];
					}
				}

				if ( ! empty( $old_meta['tf_faqs'] ) ) {
					$faqs = unserialize( $old_meta['tf_faqs'][0] );
					foreach ( $faqs as $faq ) {
						$new_meta['faq'][] = [
							'title'       => $faq['name'],
							'description' => $faq['desc'],
						];
					}
				}

				update_post_meta(
					$hotel->ID,
					'tf_hotel',
					$new_meta
				);

			}

			/** Hotels Location Taxonomy Migration */
			$hotel_locations = get_terms( [
				'taxonomy'   => 'hotel_location',
				'hide_empty' => false,
			] );

			foreach ( $hotel_locations as $hotel_location ) {

				$old_locations_meta = get_term_meta(
					$hotel_location->term_id,
					'category-image-id',
					true
				);
				$new_meta           = [
					"image" => [
						"url"         => wp_get_attachment_url( $old_locations_meta ),
						"id"          => $old_locations_meta,
						"width"       => "1920",
						"height"      => "1080",
						"thumbnail"   => wp_get_attachment_thumb_url( $old_locations_meta ),
						"alt"         => "",
						"title"       => "",
						"description" => ""
					]
				];
				// If the meta field for the term does not exist, it will be added.
				update_term_meta(
					$hotel_location->term_id,
					"hotel_location",
					$new_meta
				);
			}

			/** Tour Destinations Image Fix */
			$tour_destinations = get_terms( [
				'taxonomy'   => 'tour_destination',
				'hide_empty' => false,
			] );

			foreach ( $tour_destinations as $tour_destination ) {
				$old_term_metadata = get_term_meta( $tour_destination->term_id, 'tour_destination_meta', true )['tour_destination_meta'] ?? null;
				if ( ! empty( $old_term_metadata ) ) {
					$image_id = attachment_url_to_postid( $old_term_metadata );
					$new_meta = [
						"image" => [
							"url"         => wp_get_attachment_url( $image_id ),
							"id"          => $image_id,
							"width"       => "1920",
							"height"      => "1080",
							"thumbnail"   => wp_get_attachment_thumb_url( $image_id ),
							"alt"         => "",
							"title"       => "",
							"description" => ""
						]
					];
					// If the meta field for the term does not exist, it will be added.
					update_term_meta(
						$tour_destination->term_id,
						"tour_destination",
						$new_meta
					);
				}
			}

			/** Tour Type Fix */
			$tours = get_posts( [ 'post_type' => 'tf_tours', 'numberposts' => - 1, ] );
			foreach ( $tours as $tour ) {
				$old_meta             = get_post_meta( $tour->ID );
				$tour_options         = unserialize( $old_meta['tf_tours_option'][0] );
				$tour_options['type'] = 'continuous';
				update_post_meta(
					$tour->ID,
					'tf_tours_option',
					$tour_options
				);
			}


			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_migrate_data_204_210', 1 );

		}
	}

	/*
	 * TF Options Migrator
	 * @author: Sydur Rahman
	 * */
	function tf_migrate_option_data() {

		if ( empty( get_option( 'tf_migrate_data_204_210_2022' ) ) ) {

			/** Tours Migrations */
			$tours = get_posts( [ 'post_type' => 'tf_tours', 'numberposts' => - 1, ] );
			foreach ( $tours as $tour ) {
				$old_meta = get_post_meta( $tour->ID );
				if ( ! empty( $old_meta['tf_tours_option'] ) ) {
					$tour_options = unserialize( $old_meta['tf_tours_option'][0] );

					if ( isset( $tour_options['hightlights_thumbnail'] ) && is_array( $tour_options['hightlights_thumbnail'] ) ) {
						$tour_options['hightlights_thumbnail'] = $tour_options['hightlights_thumbnail']['url'];
					}
					if ( isset( $tour_options['include-exclude-bg'] ) && is_array( $tour_options['include-exclude-bg'] ) ) {
						$tour_options['include-exclude-bg'] = $tour_options['include-exclude-bg']['url'];
					}
					update_post_meta(
						$tour->ID,
						'tf_tours_opt',
						$tour_options
					);
				}

			}
			/** Tour Destinations Image Fix */
			$tour_destinations = get_terms( [
				'taxonomy'   => 'tour_destination',
				'hide_empty' => false,
			] );


			foreach ( $tour_destinations as $tour_destination ) {
				$old_term_metadata = get_term_meta( $tour_destination->term_id, 'tour_destination', true );

				if ( ! empty( $old_term_metadata ) ) {
					if ( isset( $old_term_metadata['image'] ) && is_array( $old_term_metadata['image'] ) ) {
						$old_term_metadata['image'] = $old_term_metadata['image']['url'];
					}

					// If the meta field for the term does not exist, it will be added.
					update_term_meta(
						$tour_destination->term_id,
						"tf_tour_destination",
						$old_term_metadata
					);
				}
			}

			/** Hotel Migrations */
			$hotels = get_posts( [ 'post_type' => 'tf_hotel', 'numberposts' => - 1, ] );

			foreach ( $hotels as $hotel ) {
				$old_meta = get_post_meta( $hotel->ID );
				if ( ! empty( $old_meta['tf_hotel'] ) ) {
					$hotel_options = unserialize( $old_meta['tf_hotel'][0] );


					// $tour_options = serialize( $tour_options );
					update_post_meta(
						$hotel->ID,
						'tf_hotels_opt',
						$hotel_options
					);
				}

			}

			/** Hotel Location Image Fix */
			$hotel_location = get_terms( [
				'taxonomy'   => 'hotel_location',
				'hide_empty' => false,
			] );


			foreach ( $hotel_location as $_hotel_location ) {
				$old_term_metadata = get_term_meta( $_hotel_location->term_id, 'hotel_location', true );
				if ( ! empty( $old_term_metadata ) ) {
					if ( isset( $old_term_metadata['image'] ) && is_array( $old_term_metadata['image'] ) ) {
						$old_term_metadata['image'] = $old_term_metadata['image']['url'];
					}

					// If the meta field for the term does not exist, it will be added.
					update_term_meta(
						$_hotel_location->term_id,
						"tf_hotel_location",
						$old_term_metadata
					);
				}
			}

			/** Hotel Feature Image Fix */
			$hotel_feature = get_terms( [
				'taxonomy'   => 'hotel_feature',
				'hide_empty' => false,
			] );


			foreach ( $hotel_feature as $_hotel_feature ) {
				$old_term_metadata = get_term_meta( $_hotel_feature->term_id, 'hotel_feature', true );
				if ( ! empty( $old_term_metadata ) ) {
					if ( isset( $old_term_metadata['icon-c'] ) && is_array( $old_term_metadata['icon-c'] ) ) {
						$old_term_metadata['icon-c'] = $old_term_metadata['icon-c']['url'];
					}
					if ( isset( $old_term_metadata['dimention'] ) && is_array( $old_term_metadata['dimention'] ) ) {
						$old_term_metadata['dimention'] = $old_term_metadata['dimention']['width'];
					}

					// If the meta field for the term does not exist, it will be added.
					update_term_meta(
						$_hotel_feature->term_id,
						"tf_hotel_feature",
						$old_term_metadata
					);
				}
			}


			/** settings option migration */
			// company_logo
			$old_setting_option = get_option( 'tourfic_opt' );
			if ( isset( $old_setting_option['itinerary-builder-setings']['company_logo'] ) && is_array( $old_setting_option['itinerary-builder-setings']['company_logo'] ) ) {
				$old_setting_option['itinerary-builder-setings']['company_logo'] = $old_setting_option['itinerary-builder-setings']['company_logo']['url'];
			}
			if ( isset( $old_setting_option['itinerary-builder-setings']['expert_logo'] ) && is_array( $old_setting_option['itinerary-builder-setings']['expert_logo'] ) ) {
				$old_setting_option['itinerary-builder-setings']['expert_logo'] = $old_setting_option['itinerary-builder-setings']['expert_logo']['url'];
			}
			update_option( 'tf_settings', $old_setting_option );

			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_migrate_data_204_210_2022', 2 );
		}


		if ( empty( get_option( 'tf_license_data_migrate_data_204_210_2022' ) ) ) {

			/** License Migrate */

			$old_setting_option = get_option( 'tourfic_opt' );
			if ( ! empty( $old_setting_option['license-key'] ) && ! empty( $old_setting_option['license-email'] ) ) {
				$tf_settings['license-key']   = $old_setting_option['license-key'];
				$tf_settings['license-email'] = $old_setting_option['license-email'];
				update_option( 'tf_license_settings', $tf_settings ) || add_option( 'tf_license_settings', $tf_settings );
			} else {
				$tf_setting_option            = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
				$tf_settings['license-key']   = ! empty( $tf_setting_option['license-key'] ) ? $tf_setting_option['license-key'] : '';
				$tf_settings['license-email'] = ! empty( $tf_setting_option['license-email'] ) ? $tf_setting_option['license-email'] : '';
				update_option( 'tf_license_settings', $tf_settings ) || add_option( 'tf_license_settings', $tf_settings );
			}

			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_license_data_migrate_data_204_210_2022', 2 );
		}


	}
}