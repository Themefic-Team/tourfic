<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Apartment_Rest_API' ) ) {
	class TF_Apartment_Rest_API extends TF_Rest_API {

		/*
		 * instance
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			parent::__construct();
			add_action( 'rest_api_init', array( $this, 'add_apartment_meta_to_rest_api' ) );
		}

		/*
		 * Get Apartments
		 * @author Foysal
		 */
		public function tf_get_apartments( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'user' ) ? $request->get_param( 'user' ) : get_current_user_id();

			$query_apartments = new WP_Query( array(
				'post_type'      => 'tf_apartment',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) ? '' : $author,
				'paged'          => $page,
			) );
			$apartments       = array();
			if ( $query_apartments->have_posts() ) {
				while ( $query_apartments->have_posts() ) {
					$query_apartments->the_post();
					$apartment_id = get_the_ID();

					$apartment_data                       = array();
					$apartment_data['id']                 = $apartment_id;
					$apartment_data['permalink']          = get_permalink( $apartment_id );
					$apartment_data['title']              = get_the_title( $apartment_id );
					$apartment_data['content']            = get_the_content( $apartment_id );
					$apartment_data['status']             = get_post_status( $apartment_id );
					$apartment_data['author']             = get_the_author_meta( 'display_name', get_post_field( 'post_author', $apartment_id ) );
					$apartment_data['apartment_location'] = $this->tf_get_post_terms( $apartment_id, 'apartment_location' ) ? $this->tf_get_post_terms( $apartment_id, 'apartment_location' ) : '—';
					$apartment_data['apartment_feature']  = $this->tf_get_post_terms( $apartment_id, 'apartment_feature' ) ? $this->tf_get_post_terms( $apartment_id, 'apartment_feature' ) : '—';
					$apartment_data['apartment_type']     = $this->tf_get_post_terms( $apartment_id, 'apartment_type' ) ? $this->tf_get_post_terms( $apartment_id, 'apartment_type' ) : '—';
					$apartment_data['date']               = get_the_date( '', $apartment_id );
					$apartment_data['featured_image']     = get_the_post_thumbnail_url( $apartment_id );
					$apartments[]                         = $apartment_data;
				}
			}
			wp_reset_postdata();
			$apartments = array(
				'apartments' => $apartments,
				'total'      => $query_apartments->found_posts,
			);

			return $apartments;
		}

		/*
		 * Add apartment meta to /wp-json/wp/v2/tf_apartment api
		 * @author Foysal
		 */
		function add_apartment_meta_to_rest_api() {
			register_rest_field( 'tf_apartment', 'tf_apartment_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_apartment_opt  = get_post_meta( $post_arr['id'], 'tf_apartment_opt', true );
					$unserialize_array = array(
						'map',
					);
					foreach ( $unserialize_array as $item ) {
						if ( ! empty( $tf_apartment_opt[ $item ] ) && is_serialized( $tf_apartment_opt[ $item ] ) ) {
							$tf_apartment_opt[ $item ] = unserialize( $tf_apartment_opt[ $item ] );
						}
					}

					return $tf_apartment_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_apartment', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//apartment reviews
			register_rest_field( 'tf_apartment', 'reviews', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_post_review( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//apartment start price
			register_rest_field( 'tf_apartment', 'start_price', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_apartment_starting_price( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}

		function tf_get_apartment_starting_price( $post_id ) {
			$room_price = [];
			$meta       = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$rooms      = ! empty( $meta['room'] ) ? $meta['room'] : array();
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_apartment_b_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                      = unserialize( $tf_apartment_b_rooms_value );
			}
			if ( ! empty( $rooms ) ):
				foreach ( $rooms as $room ) {
					$pricing_by = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : 1;
					if ( $pricing_by == 1 ) {
						$price        = ! empty( $room['price'] ) ? $room['price'] : '';
						$room_price[] = $price;
					} else if ( $pricing_by == 2 ) {
						$adult_price  = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
						$room_price[] = $adult_price;
					}
				}
			endif;

			return ! empty( $room_price ) ? wc_price( min( $room_price ) ) : '';
		}

		/*
		 * Get Apartment Availability
		 * @author Foysal
		 */
		function tf_get_apartment_availability( $request ) {
			$apartment_id = ! empty( $request->get_param( 'apartment_id' ) ) ? $request->get_param( 'apartment_id' ) : '';

			if ( $apartment_id !== 'undefined' ) {
				$apartment_data        = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
				$apt_availability_data = isset( $apartment_data['apt_availability'] ) && ! empty( $apartment_data['apt_availability'] ) ? json_decode( $apartment_data['apt_availability'], true ) : [];
			} else {
				$apt_availability_data = get_option( 'tf_apt_availability' );
				delete_option( 'tf_apt_availability' );
			}

			if ( ! empty( $apt_availability_data ) && is_array( $apt_availability_data ) ) {
				$apt_availability_data = array_values( $apt_availability_data );
				$apt_availability_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					$item['title']    = $item['pricing_type'] == 'per_night' ? esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . esc_html__( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $apt_availability_data );
			} else {
				$apt_availability_data = [];
			}

			return $apt_availability_data;
		}
	}
}

TF_Apartment_Rest_API::get_instance();
