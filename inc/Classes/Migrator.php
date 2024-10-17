<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Room\Room;

class Migrator {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_action( 'init', array( $this, 'tf_permalink_settings_migration' ) );
		add_action( 'init', array( $this, 'tf_template_3_migrate_data' ) );
		add_action( 'init', array( $this, 'tf_migrate_option_data' ) );
		add_action( 'init', array( $this, 'tf_migrate_data' ) );
		if ( Helper::tf_is_woo_active() ) {
			add_action( 'admin_init', array( $this, 'tf_admin_order_data_migration' ) );
		}
		add_action( 'admin_init', array( $this, 'tf_hotel_room_migrate' ) );
		add_action( 'init', array( $this, 'tf_rooms_data_add_in_hotel' ) );
//		add_action( 'admin_init', array( $this, 'tf_search_keys_migrate' ) );
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
		if ( empty( get_option( 'tf_template_3_migrate_data' ) ) || ( ! empty( get_option( 'tf_template_3_migrate_data' ) ) && get_option( 'tf_template_3_migrate_data' ) < 2 ) ) {

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
					"hotel-section"        => "Facilities",
					"hotel-section-slug"   => "facilities",
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
			update_option( 'tf_template_3_migrate_data', 2 );

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

				$old_locations_meta = '';
				if ( ! is_array( $hotel_location ) ) {
					$old_locations_meta = get_term_meta(
						$hotel_location->term_id,
						'category-image-id',
						true
					);
				}

				$new_meta = [
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
				if ( ! is_array( $hotel_location ) ) {
					update_term_meta(
						$hotel_location->term_id,
						"hotel_location",
						$new_meta
					);
				}
			}

			/** Tour Destinations Image Fix */
			$tour_destinations = get_terms( [
				'taxonomy'   => 'tour_destination',
				'hide_empty' => false,
			] );

			foreach ( $tour_destinations as $tour_destination ) {
				if ( ! is_array( $tour_destination ) ) {
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
				$old_term_metadata = '';
				if ( ! is_array( $tour_destination ) ) {
					$old_term_metadata = get_term_meta( $tour_destination->term_id, 'tour_destination', true );
				}

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
				$old_term_metadata = '';
				if ( ! is_array( $tour_destination ) ) {
					$old_term_metadata = get_term_meta( $_hotel_location->term_id, 'hotel_location', true );
				}
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
				$old_term_metadata = '';
				if ( ! is_array( $tour_destination ) ) {
					$old_term_metadata = get_term_meta( $_hotel_feature->term_id, 'hotel_feature', true );
				}
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

	function tf_admin_order_data_migration() {
		if ( empty( get_option( 'tf_old_order_data_migrate' ) ) ) {

			$tf_old_order_limit = new \WC_Order_Query( array(
				'limit'   => - 1,
				'orderby' => 'date',
				'order'   => 'ASC',
				'return'  => 'ids',
			) );
			$order              = $tf_old_order_limit->get_orders();

			foreach ( $order as $item_id => $item ) {
				$itemmeta = wc_get_order( $item );
				if ( is_a( $itemmeta, 'WC_Order_Refund' ) ) {
					$itemmeta = wc_get_order( $itemmeta->get_parent_id() );
				}
				$tf_ordering_date = $itemmeta->get_date_created();

				//Order Data Insert
				$billinginfo = [
					'billing_first_name' => ! empty( $itemmeta->get_billing_first_name() ) ? $itemmeta->get_billing_first_name() : '',
					'billing_last_name'  => ! empty( $itemmeta->get_billing_last_name() ) ? $itemmeta->get_billing_last_name() : '',
					'billing_company'    => ! empty( $itemmeta->get_billing_company() ) ? $itemmeta->get_billing_company() : '',
					'billing_address_1'  => ! empty( $itemmeta->get_billing_address_1() ) ? $itemmeta->get_billing_address_1() : '',
					'billing_address_2'  => ! empty( $itemmeta->get_billing_address_2() ) ? $itemmeta->get_billing_address_2() : '',
					'billing_city'       => ! empty( $itemmeta->get_billing_city() ) ? $itemmeta->get_billing_city() : '',
					'billing_state'      => ! empty( $itemmeta->get_billing_state() ) ? $itemmeta->get_billing_state() : '',
					'billing_postcode'   => ! empty( $itemmeta->get_billing_postcode() ) ? $itemmeta->get_billing_postcode() : '',
					'billing_country'    => ! empty( $itemmeta->get_billing_country() ) ? $itemmeta->get_billing_country() : '',
					'billing_email'      => ! empty( $itemmeta->get_billing_email() ) ? $itemmeta->get_billing_email() : '',
					'billing_phone'      => ! empty( $itemmeta->get_billing_phone() ) ? $itemmeta->get_billing_phone() : ''
				];

				$shippinginfo = [
					'shipping_first_name' => ! empty( $itemmeta->get_shipping_first_name() ) ? $itemmeta->get_shipping_first_name() : '',
					'shipping_last_name'  => ! empty( $itemmeta->get_shipping_last_name() ) ? $itemmeta->get_shipping_last_name() : '',
					'shipping_company'    => ! empty( $itemmeta->get_shipping_company() ) ? $itemmeta->get_shipping_company() : '',
					'shipping_address_1'  => ! empty( $itemmeta->get_shipping_address_1() ) ? $itemmeta->get_shipping_address_1() : '',
					'shipping_address_2'  => ! empty( $itemmeta->get_shipping_address_2() ) ? $itemmeta->get_shipping_address_2() : '',
					'shipping_city'       => ! empty( $itemmeta->get_shipping_city() ) ? $itemmeta->get_shipping_city() : '',
					'shipping_state'      => ! empty( $itemmeta->get_shipping_state() ) ? $itemmeta->get_shipping_state() : '',
					'shipping_postcode'   => ! empty( $itemmeta->get_shipping_postcode() ) ? $itemmeta->get_shipping_postcode() : '',
					'shipping_country'    => ! empty( $itemmeta->get_shipping_country() ) ? $itemmeta->get_shipping_country() : '',
					'shipping_phone'      => ! empty( $itemmeta->get_shipping_phone() ) ? $itemmeta->get_shipping_phone() : ''
				];

				foreach ( $itemmeta->get_items() as $item_key => $item_values ) {
					$order_type = wc_get_order_item_meta( $item_key, '_order_type', true );
					if ( "hotel" == $order_type ) {
						$post_id              = wc_get_order_item_meta( $item_key, '_post_id', true );
						$unique_id            = wc_get_order_item_meta( $item_key, '_unique_id', true );
						$room_selected        = wc_get_order_item_meta( $item_key, 'number_room_booked', true );
						$check_in             = wc_get_order_item_meta( $item_key, 'check_in', true );
						$check_out            = wc_get_order_item_meta( $item_key, 'check_out', true );
						$price                = $itemmeta->get_subtotal();
						$due                  = wc_get_order_item_meta( $item_key, 'due', true );
						$room_name            = wc_get_order_item_meta( $item_key, 'room_name', true );
						$adult                = wc_get_order_item_meta( $item_key, 'adult', true );
						$child                = wc_get_order_item_meta( $item_key, 'child', true );
						$children_ages        = wc_get_order_item_meta( $item_key, 'Children Ages', true );
						$airport_service_type = wc_get_order_item_meta( $item_key, 'Airport Service', true );
						$airport_service_fee  = wc_get_order_item_meta( $item_key, 'Airport Service Fee', true );

						$iteminfo = [
							'room'                 => $room_selected,
							'room_unique_id'       => $unique_id,
							'check_in'             => $check_in,
							'check_out'            => $check_out,
							'room_name'            => $room_name,
							'adult'                => $adult,
							'child'                => $child,
							'children_ages'        => $children_ages,
							'airport_service_type' => $airport_service_type,
							'airport_service_fee'  => $airport_service_fee,
							'total_price'          => $price,
							'due_price'            => $due,
						];

						$iteminfo_keys = array_keys( $iteminfo );
						$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

						$iteminfo_values = array_values( $iteminfo );
						$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

						$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

						global $wpdb;
						$wpdb->query(
							$wpdb->prepare(
								"INSERT INTO {$wpdb->prefix}tf_order_data
						( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
								array(
									$item,
									sanitize_key( $post_id ),
									$order_type,
									$room_selected,
									$check_in,
									$check_out,
									wp_json_encode( $billinginfo ),
									wp_json_encode( $shippinginfo ),
									wp_json_encode( $iteminfo ),
									$itemmeta->get_customer_id(),
									$itemmeta->get_payment_method(),
									$itemmeta->get_status(),
									$tf_ordering_date->date( 'Y-m-d H:i:s' )
								)
							)
						);
					}
					if ( "tour" == $order_type ) {
						$post_id        = wc_get_order_item_meta( $item_key, '_tour_id', true );
						$tour_date      = wc_get_order_item_meta( $item_key, 'Tour Date', true );
						$tour_time      = wc_get_order_item_meta( $item_key, 'Tour Time', true );
						$price          = $itemmeta->get_subtotal();
						$due            = wc_get_order_item_meta( $item_key, 'Due', true );
						$tour_extra     = wc_get_order_item_meta( $item_key, 'Tour Extra', true );
						$adult          = wc_get_order_item_meta( $item_key, 'Adults', true );
						$child          = wc_get_order_item_meta( $item_key, 'Children', true );
						$infants        = wc_get_order_item_meta( $item_key, 'Infants', true );
						$datatype_check = preg_match( "/-/", $tour_date );
						if ( ! empty( $tour_date ) && ! empty( $datatype_check ) ) {
							list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
						}
						if ( ! empty( $tour_date ) && empty( $datatype_check ) ) {
							$tour_in  = gmdate( "Y-m-d", strtotime( $tour_date ) );
							$tour_out = "0000-00-00";
						}


						$iteminfo = [
							'tour_date'   => $tour_date,
							'tour_time'   => $tour_time,
							'tour_extra'  => $tour_extra,
							'adult'       => $adult,
							'child'       => $child,
							'infants'     => $infants,
							'total_price' => $price,
							'due_price'   => $due,
						];

						$iteminfo_keys = array_keys( $iteminfo );
						$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

						$iteminfo_values = array_values( $iteminfo );
						$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

						$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

						global $wpdb;
						$wpdb->query(
							$wpdb->prepare(
								"INSERT INTO {$wpdb->prefix}tf_order_data
						( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
								array(
									$item,
									sanitize_key( $post_id ),
									$order_type,
									gmdate( "Y-m-d", strtotime( $tour_in ) ),
									gmdate( "Y-m-d", strtotime( $tour_out ) ),
									wp_json_encode( $billinginfo ),
									wp_json_encode( $shippinginfo ),
									wp_json_encode( $iteminfo ),
									$itemmeta->get_customer_id(),
									$itemmeta->get_payment_method(),
									$itemmeta->get_status(),
									$tf_ordering_date->date( 'Y-m-d H:i:s' )
								)
							)
						);
					}
				}

			}
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_old_order_data_migrate', 1 );
		}
	}


	/*
	 * Hotel room migrate
	 */
	public function tf_hotel_room_migrate() {
		if ( empty( get_option( 'tf_room_data_migration' ) ) ) {
			$this->regenerate_room_meta();
			update_option( 'tf_room_data_migration', 1 );
		}
	}

	function regenerate_room_meta() {

		$args  = array(
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish',
			'posts_per_page' => - 1
		);
		$posts = new \WP_Query( $args );
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$post_id = get_the_ID();
				$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );

				$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
				if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
					$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $rooms );
					$rooms                = unserialize( $tf_hotel_rooms_value );
				}

				$current_user_id = get_current_user_id();
				foreach ( $rooms as $room ) {
					$post_data        = array(
						'post_type'    => 'tf_room',
						'post_title'   => ! empty( $room['title'] ) ? $room['title'] : 'No Title',
						'post_status'  => 'publish',
						'post_author'  => $current_user_id,
						'post_content' => ! empty( $room['description'] ) ? $room['description'] : '',
					);
					$room['tf_hotel'] = $post_id;

					$room_post_id = wp_insert_post( $post_data );
					update_post_meta( $room_post_id, 'tf_room_opt', $room );

					//insert thumbnail if has 'room_preview_img' key which return url
					if ( ! empty( $room['room_preview_img'] ) ) {
						$attachment_id = attachment_url_to_postid( $room['room_preview_img'] );
						if ( ! empty( $attachment_id ) ) {
							set_post_thumbnail( $room_post_id, $attachment_id );
						}
					}
				}

			}
		}
	}

	/* 
	 * Rooms data add in hotel tf_rooms field
	 * @author Foysal
	 */
	function tf_rooms_data_add_in_hotel(){
		if ( empty( get_option( 'tf_room_data_add_in_hotel' ) ) ) {
			$args  = array(
				'post_type'      => 'tf_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );
	
					$rooms = Room::get_hotel_rooms( $post_id );
					if(!empty($rooms)){
						$room_ids = array_column($rooms, 'ID');
						$meta['tf_rooms'] = $room_ids;

						update_post_meta($post_id, 'tf_hotels_opt', $meta);
					}
				}
			}

			update_option( 'tf_room_data_add_in_hotel', 1 );
		}
	}

	public function tf_search_keys_migrate() {
		$tf_hotel_search_keys_migration     = ! empty( get_option( 'tf_hotel_search_keys_migration' ) ) ? get_option( 'tf_hotel_search_keys_migration' ) : 0;
		$tf_room_search_keys_migration     = ! empty( get_option( 'tf_room_search_keys_migration' ) ) ? get_option( 'tf_room_search_keys_migration' ) : 0;
		$tf_tour_search_keys_migration      = ! empty( get_option( 'tf_tour_search_keys_migration' ) ) ? get_option( 'tf_tour_search_keys_migration' ) : 0;
		$tf_apartment_search_keys_migration = ! empty( get_option( 'tf_apartment_search_keys_migration' ) ) ? get_option( 'tf_apartment_search_keys_migration' ) : 0;
		if ( $tf_hotel_search_keys_migration < 1 ) {
			$this->regenerate_search_keys( 'tf_hotel' );
			update_option( 'tf_hotel_search_keys_migration', $tf_hotel_search_keys_migration + 1 );
		}
		if ( $tf_room_search_keys_migration < 1 ) {
			$this->regenerate_search_keys( 'tf_room' );
			update_option( 'tf_room_search_keys_migration', $tf_room_search_keys_migration + 1 );
		}
//		if ( $tf_tour_search_keys_migration < 1 ) {
//			$this->regenerate_search_keys( 'tf_tours' );
//			update_option( 'tf_tour_search_keys_migration', $tf_tour_search_keys_migration + 1 );
//		}
//		if ( $tf_apartment_search_keys_migration < 1 ) {
//			$this->regenerate_search_keys( 'tf_apartment' );
//			update_option( 'tf_apartment_search_keys_migration', $tf_apartment_search_keys_migration + 1 );
//		}
	}

	function regenerate_search_keys( $type ) {
		if ( "tf_hotel" == $type ) {

			$searchable_keys = [
				'featured',
				'booking-by',
				'map'
			];
			$args            = array(
				'post_type'      => 'tf_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();

		}
		if ( "tf_room" == $type ) {

			$searchable_keys = [
				'unique_id',
				'order_id',
				'adult',
				'child',
				'pricing-by',
				'price',
				'adult_price',
				'child_price',
				'num-room',
				'reduce_num_room',
				'avail_date'
			];
			$args            = array(
				'post_type'      => 'tf_room',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();

		}
		if ( "tf_tours" == $type ) {
			$searchable_keys = [
				'tour_as_featured',
				'location',
				'pricing',
				'adult_price',
				'child_price',
				'infant_price',
				'group_price',
				'type',
				'cont_min_people',
				'cont_max_people',
				'cont_max_capacity',
				'disable_range',
				'custom_avail',
				'cont_custom_date',
				'disable_specific',
				'fixed_availability'
			];
			$args            = array(
				'post_type'      => 'tf_tours',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_tours_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, 'tf_search_' . $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();
		}
		if ( "tf_apartment" == $type ) {
			$searchable_keys = [
				'apartment_as_featured',
				'map',
				'pricing_type',
				'price_per_night',
				'adult_price',
				'child_price',
				'infant_price',
				'min_stay',
				'max_adults',
				'max_children',
				'max_infants',
				'apt_availability'
			];
			$args            = array(
				'post_type'      => 'tf_apartment',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_apartment_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, 'tf_search_' . $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();
		}
	}
}