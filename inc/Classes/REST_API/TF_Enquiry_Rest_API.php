<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'Tourfic_Enquiry_Rest_API' ) ) {
	class Tourfic_Enquiry_Rest_API extends Tourfic_Rest_API {

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

		/*
		 * Enquiries
		 * @author Foysal
		 */
		public function tf_get_enquiries( $request ) {
			$permission = $this->tf_enquiry_permission_callback( $request );
			if ( is_wp_error( $permission ) ) {
				return $permission;
			}
			$current_user_id = get_current_user_id();
			$post_type       = $this->tf_validate_allowed_param( $request, 'post_type', $this->tf_enquiry_post_types(), true );
			$post_id         = $this->tf_get_rest_absint_param( $request, 'post_id' );
			$filters         = $this->tf_validate_allowed_param( $request, 'filters', $this->tf_enquiry_status_filters() );

			foreach ( array( $post_type, $post_id, $filters ) as $validation_error ) {
				if ( is_wp_error( $validation_error ) ) {
					return $validation_error;
				}
			}

			global $wpdb;
			$where = array( $wpdb->prepare( 'post_type = %s', $post_type ) );

			if ( ! empty( $post_id ) ) {
				$where[] = $wpdb->prepare( 'post_id = %d', $post_id );
			}

			if ( ! empty( $filters ) ) {
				if ( 'not-replied' === $filters ) {
					$where[] = $wpdb->prepare( 'enquiry_status != %s', 'replied' );
				} elseif ( 'not-responded' === $filters ) {
					$where[] = $wpdb->prepare( 'enquiry_status != %s', 'responded' );
				} else {
					$where[] = $wpdb->prepare( 'enquiry_status = %s', $filters );
				}
			}

			if ( ! $this->tf_current_user_can_manage_records( $post_type, 'enquiry' ) ) {
				$where[] = $wpdb->prepare(
					"(author_id = %d OR post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s))",
					$current_user_id,
					$current_user_id,
					$post_type
				);
			}

			$hotel_enquiry_result = $this->tf_read_record_data( function() use ( $wpdb, $where ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Every dynamic clause is prepared above.
				return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE " . implode( ' AND ', $where ) . ' ORDER BY id DESC', ARRAY_A );
			} );
			if ( is_wp_error( $hotel_enquiry_result ) ) {
				return $hotel_enquiry_result;
			}

			$enquirys_data = array();
			foreach ( $hotel_enquiry_result as $enquiry ) {		
                //post title
				$enquiry['post_title'] = esc_html(get_the_title( $enquiry['post_id'] ));

				$enquirys_data[] = $enquiry;
			}

			return $enquirys_data;
		}

        /* 
         * Enquiry Details
         * @author Foysal
         */
        public function tf_get_enquiry_details( $request ){
			$permission = $this->tf_enquiry_permission_callback( $request );
			if ( is_wp_error( $permission ) ) {
				return $permission;
			}
			$id = $this->tf_get_rest_absint_param( $request, 'id' );
			if ( is_wp_error( $id ) || ! $id ) {
				return new WP_Error( 'rest_invalid_param', esc_html__( 'A valid record ID is required.', 'tourfic' ), array( 'status' => 400 ) );
			}
			global $wpdb;
			$enquiry = $this->tf_read_record_data( function() use ( $wpdb, $id ) {
				return $wpdb->get_row( $wpdb->prepare( "SELECT id, post_id, post_type, author_id FROM {$wpdb->prefix}tf_enquiry_data WHERE id = %d", $id ), ARRAY_A );
			} );
			if ( is_wp_error( $enquiry ) ) {
				return $enquiry;
			}
			if ( empty( $enquiry ) ) {
				return new WP_Error( 'tf_enquiry_not_found', esc_html__( 'Enquiry not found.', 'tourfic' ), array( 'status' => 404 ) );
			}
			if ( ! $this->tf_current_user_can_access_enquiry( $enquiry ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this enquiry.', 'tourfic' ), array( 'status' => 403 ) );
			}

			$enquiry = $this->tf_read_record_data( function() use ( $wpdb, $id ) {
				return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE id = %d", $id ), ARRAY_A );
			} );
			if ( is_wp_error( $enquiry ) ) {
				return $enquiry;
			}
			if ( ! $enquiry || ! $this->tf_current_user_can_access_enquiry( $enquiry ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'The record is no longer accessible.', 'tourfic' ), array( 'status' => 403 ) );
			}

			$reply_data = !empty( $enquiry["reply_data"] ) ? json_decode($enquiry["reply_data"], true) : array();

			if(class_exists('\Tourfic\Core\Enquiry')){
            	$date_time_format = \Tourfic\Core\Enquiry::convert_to_wp_timezone( $enquiry["created_at"] );
			} else {
				$date_time_format = self::convert_to_wp_timezone( $enquiry["created_at"] );
			}

            list($date, $time) = explode(" ", $date_time_format);
			$formatted_date = wp_date( 'M d, Y', strtotime( $date ) );
			$formatted_time = wp_date( 'h:i:s A', strtotime( $time ) );

            $enquiry['formatted_date'] = $formatted_date;
            $enquiry['formatted_time'] = $formatted_time;

			if( !empty($reply_data) && is_array($reply_data)){
				foreach($reply_data as $key => $reply){
					if(class_exists('\Tourfic\Core\Enquiry')){
						$reply_data[$key]['formatted_submit_time'] = \Tourfic\Core\Enquiry::time_elapsed_string( $reply["submit_time"]);
					} else{
						$reply_data[$key]['formatted_submit_time'] = self::time_elapsed_string( $reply["submit_time"]);
					}
				}
			}

			$enquiry['reply_data'] = json_encode($reply_data);

			return $enquiry;
        }
	}
}

Tourfic_Enquiry_Rest_API::get_instance();
