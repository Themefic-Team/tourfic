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
			if ( empty( $user_meta ) || empty( $user_meta->roles ) ) {
				return false;
			}
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
			if ( is_user_logged_in() && ( $this->user_has_role( $current_user_id, 'administrator' ) || $this->user_has_role( $current_user_id, 'tf_manager' ) ) ) {
				return true;
			} else {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}
		}

		public function tf_user_permission_callback( WP_REST_Request $request ) {
			if ( ! is_user_logged_in() ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.', 'tourfic' ), array( 'status' => 403 ) );
			}

			if ( $this->tf_current_user_can_access_user( $request->get_param( 'id' ) ) ) {
				return true;
			}

			return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this user.', 'tourfic' ), array( 'status' => 403 ) );
		}

		public function tf_order_permission_callback( WP_REST_Request $request ) {
			return $this->tf_admin_vendor_permission_callback();
		}

		public function tf_enquiry_permission_callback( WP_REST_Request $request ) {
			return $this->tf_admin_vendor_permission_callback();
		}

		protected function tf_admin_vendor_permission_callback() {
			$current_user_id = get_current_user_id();

			if (
				is_user_logged_in()
				&& (
					$this->user_has_role( $current_user_id, 'administrator' )
					|| $this->user_has_role( $current_user_id, 'tf_manager' )
					|| $this->user_has_role( $current_user_id, 'tf_vendor' )
				)
			) {
				return true;
			}

			return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.', 'tourfic' ), array( 'status' => 403 ) );
		}

		protected function tf_current_user_can_manage_records() {
			$current_user_id = get_current_user_id();

			return $this->user_has_role( $current_user_id, 'administrator' ) || $this->user_has_role( $current_user_id, 'tf_manager' );
		}

		protected function tf_current_user_can_access_user( $user_id ) {
			$user_id         = absint( $user_id );
			$current_user_id = get_current_user_id();

			if ( empty( $user_id ) || empty( $current_user_id ) ) {
				return false;
			}

			if ( $user_id === $current_user_id ) {
				return true;
			}

			return $this->tf_current_user_can_manage_records()
				|| current_user_can( 'list_users' )
				|| current_user_can( 'edit_user', $user_id );
		}

		protected function tf_current_user_can_manage_vendor_record( $post_id = 0, $author_id = 0 ) {
			$current_user_id = get_current_user_id();

			if ( ! $this->user_has_role( $current_user_id, 'tf_vendor' ) ) {
				return false;
			}

			if ( ! empty( $author_id ) && absint( $author_id ) === $current_user_id ) {
				return true;
			}

			return ! empty( $post_id ) && absint( get_post_field( 'post_author', absint( $post_id ) ) ) === $current_user_id;
		}

		protected function tf_current_user_can_access_order( $order ) {
			if ( $this->tf_current_user_can_manage_records() ) {
				return true;
			}

			return ! empty( $order['post_id'] ) && $this->tf_current_user_can_manage_vendor_record( $order['post_id'] );
		}

		protected function tf_current_user_can_access_enquiry( $enquiry ) {
			if ( $this->tf_current_user_can_manage_records() ) {
				return true;
			}

			$post_id   = ! empty( $enquiry['post_id'] ) ? $enquiry['post_id'] : 0;
			$author_id = ! empty( $enquiry['author_id'] ) ? $enquiry['author_id'] : 0;

			return $this->tf_current_user_can_manage_vendor_record( $post_id, $author_id );
		}

		protected function tf_order_post_types() {
			return array_keys( $this->tf_order_post_type_map() );
		}

		protected function tf_order_post_type_map() {
			return array(
				'hotel'        => 'hotel',
				'tf_hotel'     => 'hotel',
				'tour'         => 'tour',
				'tf_tours'     => 'tour',
				'apartment'    => 'apartment',
				'tf_apartment' => 'apartment',
				'car'          => 'car',
				'tf_carrental' => 'car',
			);
		}

		protected function tf_normalize_order_post_type( $post_type ) {
			$post_type = sanitize_key( $post_type );
			$post_map  = $this->tf_order_post_type_map();

			return ! empty( $post_map[ $post_type ] ) ? $post_map[ $post_type ] : $post_type;
		}

		protected function tf_enquiry_post_types() {
			return array( 'tf_hotel', 'tf_tours', 'tf_apartment' );
		}

		protected function tf_checkinout_statuses() {
			return array( 'in', 'out', 'not' );
		}

		protected function tf_order_statuses() {
			return array( 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed', 'trash' );
		}

		protected function tf_enquiry_status_filters() {
			return array( 'read', 'unread', 'replied', 'responded', 'not-replied', 'not-responded' );
		}

		protected function tf_validate_allowed_param( WP_REST_Request $request, $param, $allowed, $required = false ) {
			$value = $request->get_param( $param );

			if ( '' === $value || null === $value ) {
				if ( $required ) {
					return new WP_Error(
						'tf_rest_invalid_param',
						sprintf( esc_html__( '%s is required.', 'tourfic' ), esc_html( $param ) ),
						array( 'status' => 400 )
					);
				}

				return '';
			}

			if ( ! is_scalar( $value ) ) {
				return new WP_Error(
					'tf_rest_invalid_param',
					sprintf( esc_html__( 'Invalid %s value.', 'tourfic' ), esc_html( $param ) ),
					array( 'status' => 400 )
				);
			}

			$value = sanitize_key( $value );
			if ( ! in_array( $value, $allowed, true ) ) {
				return new WP_Error(
					'tf_rest_invalid_param',
					sprintf( esc_html__( 'Invalid %s value.', 'tourfic' ), esc_html( $param ) ),
					array( 'status' => 400 )
				);
			}

			return $value;
		}

		protected function tf_get_rest_absint_param( WP_REST_Request $request, $param ) {
			$value = $request->get_param( $param );

			if ( '' === $value || null === $value ) {
				return 0;
			}

			if ( ! is_scalar( $value ) || ! is_numeric( $value ) ) {
				return new WP_Error(
					'tf_rest_invalid_param',
					sprintf( esc_html__( 'Invalid %s value.', 'tourfic' ), esc_html( $param ) ),
					array( 'status' => 400 )
				);
			}

			$value = absint( $value );
			if ( empty( $value ) ) {
				return new WP_Error(
					'tf_rest_invalid_param',
					sprintf( esc_html__( 'Invalid %s value.', 'tourfic' ), esc_html( $param ) ),
					array( 'status' => 400 )
				);
			}

			return $value;
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
