<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\App\TF_Review;

if ( ! class_exists( 'TF_Rest_API' ) ) {
	class TF_Rest_API {

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
			// Route registration is handled by the route manager.
		}

		/*
		 * Get TF Settings data
		 * @author Foysal
		 */
		public function tf_get_tf_settings( $request ) {
			$options           = get_option( 'tf_settings' );
			$unserialize_array = array( 'itinerary-builder-setings', 'amenities_cats' );
			foreach ( $unserialize_array as $item ) {
				if ( ! empty( $options[ $item ] ) && is_serialized( $options[ $item ] ) ) {
					$options[ $item ] = unserialize( $options[ $item ] );
				}
			}

			return $options;
		}

		function tf_get_post_review( $post_id ) {
			$tf_overall_rate = '';
			$ratting         = 0;
			$review_text     = '';
			$comments        = get_comments( [ 'post_id' => $post_id, 'status' => 'approve' ] );
			TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rate );
			if ( $comments ) {
				$ratting     = TF_Review::tf_average_ratings( array_values( $tf_overall_rate ?? [] ) );
				$review_text = sprintf( esc_html( _nx( '%1$s review', '%1$s reviews', count( $comments ), 'comments title', 'tourfic' ) ), number_format_i18n( count( $comments ) ) );
			}

			return array( 'post_reviews' => $ratting, 'review_text' => $review_text );
		}

		function user_has_role( $user_id, $role_name ) {
			if(empty($user_id)){
				return false;
			}
			$user_meta  = get_userdata( $user_id );
			$user_roles = $user_meta->roles;

			return in_array( $role_name, $user_roles );
		}

		function tf_get_post_author_id( $post_id ) {
			$author_id = get_post_field( 'post_author', $post_id );

			return $author_id;
		}

		function tf_get_post_author_name( $post_id ) {
			$author_id   = get_post_field( 'post_author', $post_id );
			$author_name = get_the_author_meta( 'display_name', $author_id );

			return $author_name;
		}

		/*
		 * Get post terms by post id separated by comma
		 * */
		function tf_get_post_terms( $post_id, $taxonomy ) {
			$terms      = get_the_terms( $post_id, $taxonomy );
			$term_names = [];
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$term_names[] = !empty( $term->name ) ? $term->name : '';
				}
			}

			return implode( ', ', $term_names );
		}

		/*
		 * Permission Callback
		 * @auther Foysal
		 */
		public function tf_permission_callback( WP_REST_Request $request ) {
			if ( is_user_logged_in() ) {
				return true;
			} else {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}
		}

		/*
		 * Permission Callback for admin
		 * @auther Foysal
		 */
		public function tf_admin_permission_callback( WP_REST_Request $request ) {
			$current_user_id = get_current_user_id();
			if ( is_user_logged_in() && $this->user_has_role( $current_user_id, 'administrator' ) || $this->user_has_role( $current_user_id, 'tf_manager' ) ) {
				return true;
			} else {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}
		}


		static function convert_to_wp_timezone($date_string, $format = 'Y-m-d H:i:s') {
			$timezone = wp_timezone();
			
			try {
				$date = new \DateTime($date_string, new \DateTimeZone('UTC'));
		
				$date->setTimezone($timezone);
				
				return $date->format($format);
			} catch (\Exception $e) {
				return 'Invalid date';
			}
		}

		static function time_elapsed_string($datetime) {

			$timezone = wp_timezone();
			
			$now = new \DateTime('now', $timezone);
			
			$ago = new \DateTime($datetime, $timezone);
			
			$diff = $now->diff($ago);
		
			if ($diff->h == 0 && $diff->d == 0) {
				$minutes = $diff->i;
				if ($minutes == 0) {
					return esc_html__('Just now', 'tourfic');
				} elseif ($minutes == 1) {
					return '1 minute ago';
				} else {
					return sprintf(esc_html__('%s minutes ago', 'tourfic'), $minutes);
				}
			}
		
			if ($diff->d == 0) {
				return 'Today ' . $ago->format('h:i A');
			}
		
			if ($diff->d == 1) {
				return 'Yesterday ' . $ago->format('h:i A');
			}
	
			if ($diff->d > 1) {
				return $ago->format('M d, Y h:i A');
			}
		}
	}
}

TF_Rest_API::get_instance();