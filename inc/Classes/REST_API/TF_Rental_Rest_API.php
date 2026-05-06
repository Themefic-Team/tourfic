<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Rental_Rest_API' ) ) {
	class TF_Rental_Rest_API extends TF_Rest_API {

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
			add_action( 'rest_api_init', array( $this, 'add_rental_meta_to_rest_api' ) );
		}

		/*
		 * Get Rentals
		 * @author Foysal
		 */
		public function tf_get_rentals( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'author' ) ? $request->get_param( 'author' ) : get_current_user_id();

			$query_rentals = new \WP_Query( array(
				'post_type'      => 'tf_carrental',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) || $this->user_has_role( $author, 'tf_manager' ) ? '' : $author,
				'paged'          => $page,
			) );
			$rentals       = array();
			if ( $query_rentals->have_posts() ) {
				while ( $query_rentals->have_posts() ) {
					$query_rentals->the_post();
					$rental_id = get_the_ID();

					$rental_data   = array();
					
					$rental_data['id']             = $rental_id;
					$rental_data['permalink']      = get_permalink( $rental_id );
					$rental_data['title']          = get_the_title( $rental_id );
					$rental_data['content']        = get_the_content( $rental_id );
					$rental_data['status']         = get_post_status( $rental_id );
					$rental_data['author']         = get_the_author_meta( 'display_name', get_post_field( 'post_author', $rental_id ) );
					$rental_data['carrental_location'] = $this->tf_get_post_terms( $rental_id, 'carrental_location' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_location' ) : '—';
					$rental_data['carrental_brand'] = $this->tf_get_post_terms( $rental_id, 'carrental_brand' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_brand' ) : '—';
					$rental_data['carrental_fuel_type'] = $this->tf_get_post_terms( $rental_id, 'carrental_fuel_type' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_fuel_type' ) : '—';
					$rental_data['carrental_engine_year'] = $this->tf_get_post_terms( $rental_id, 'carrental_engine_year' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_engine_year' ) : '—';
					$rental_data['carrental_category']  = $this->tf_get_post_terms( $rental_id, 'carrental_category' ) ? $this->tf_get_post_terms( $rental_id, 'carrental_category' ) : '—';
					$rental_data['date']           = get_the_date( '', $rental_id );
					$rental_data['featured_image'] = get_the_post_thumbnail_url( $rental_id );
					$rental_data['tf_carrental_opt']  = get_post_meta( $rental_id, 'tf_carrental_opt', true );
					$rentals[]                     = $rental_data;
				}
			}
			wp_reset_postdata();
			$rentals = array(
				'rentals' => $rentals,
				'total'  => $query_rentals->found_posts,
			);

			return $rentals;
		}

		/*
		 * Add rental meta to /wp-json/wp/v2/tf_carrental api
		 * @author Foysal
		 */
		function add_rental_meta_to_rest_api() {
			register_rest_field( 'tf_carrental', 'tf_carrental_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_carrental_opt  = get_post_meta( $post_arr['id'], 'tf_carrental_opt', true );
					$unserialize_array = array(
						'map',
					);
					foreach ( $unserialize_array as $item ) {
						if ( ! empty( $tf_carrental_opt[ $item ] ) && is_serialized( $tf_carrental_opt[ $item ] ) ) {
							$tf_carrental_opt[ $item ] = unserialize( $tf_carrental_opt[ $item ] );
						}
					}

					return $tf_carrental_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_rental', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}
	}
}

TF_Rental_Rest_API::get_instance();