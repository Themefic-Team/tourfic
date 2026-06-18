<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Enquiry_Rest_API' ) ) {
	class TF_Enquiry_Rest_API extends TF_Rest_API {

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
			$where  = array( 'post_type = %s' );
			$values = array( $post_type );

			if ( ! empty( $post_id ) ) {
				$where[]  = 'post_id = %d';
				$values[] = $post_id;
			}

			if ( ! empty( $filters ) ) {
				if ( 'not-replied' === $filters ) {
					$where[]  = 'enquiry_status != %s';
					$values[] = 'replied';
				} elseif ( 'not-responded' === $filters ) {
					$where[]  = 'enquiry_status != %s';
					$values[] = 'responded';
				} else {
					$where[]  = 'enquiry_status = %s';
					$values[] = $filters;
				}
			}

			if ( $this->tf_current_user_can_manage_records() ) {
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE " . implode( ' AND ', $where ) . " ORDER BY id DESC", $values ), ARRAY_A );
			} elseif ( $this->user_has_role( $current_user_id, 'tf_vendor' ) ) {
				$where[]  = 'author_id = %d';
				$values[] = $current_user_id;
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE " . implode( ' AND ', $where ) . " ORDER BY id DESC", $values ), ARRAY_A );
			} else {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.', 'tourfic' ), array( 'status' => 403 ) );
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
            global $wpdb;
			$id    = absint( $request->get_param( 'id' ) );
			$enquiry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE id = %d", $id ), ARRAY_A );
			if ( empty( $enquiry ) ) {
				return new WP_Error( 'tf_enquiry_not_found', esc_html__( 'Enquiry not found.', 'tourfic' ), array( 'status' => 404 ) );
			}
			if ( ! $this->tf_current_user_can_access_enquiry( $enquiry ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this enquiry.', 'tourfic' ), array( 'status' => 403 ) );
			}

			$reply_data = !empty( $enquiry["reply_data"] ) ? json_decode($enquiry["reply_data"], true) : array();

			if(class_exists('\Tourfic\Core\Enquiry')){
            	$date_time_format = \Tourfic\Core\Enquiry::convert_to_wp_timezone( $enquiry["created_at"] );
			} else {
				$date_time_format = self::convert_to_wp_timezone( $enquiry["created_at"] );
			}

            list($date, $time) = explode(" ", $date_time_format);
            $formatted_date = date( "M d, Y", strtotime($date));
            $formatted_time = date( "h:i:s A", strtotime($time));

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

TF_Enquiry_Rest_API::get_instance();
