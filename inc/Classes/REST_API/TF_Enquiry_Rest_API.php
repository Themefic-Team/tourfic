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
			$current_user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			$post_type = $request->get_param( 'post_type' ) ? $request->get_param( 'post_type' ) : '';
			$post_id = $request->get_param( 'post_id' ) ? $request->get_param( 'post_id' ) : '';
			$filters = $request->get_param( 'filters' ) ? $request->get_param( 'filters' ) : '';

            $tf_filter_query = "";
            if ( $post_id ) {
                $tf_filter_query .= " AND post_id = '$post_id'";
            }
			if( !empty($filters) ) {
				if( $filters == 'not-replied') {
					$tf_filter_query .= sprintf(' AND enquiry_status != "%s"', 'replied' );
				} elseif( $filters == 'not-responded') {
					$tf_filter_query .= sprintf(' AND enquiry_status != "%s"', 'responded' );
				} else {
					$tf_filter_query .= sprintf(' AND enquiry_status = "%s"', $filters );
				}
			}

			global $wpdb;
			if ( $this->user_has_role( $current_user_id, 'administrator' ) || $this->user_has_role( $current_user_id, 'tf_manager' ) ) {
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s {$tf_filter_query} ORDER BY id DESC", $post_type ), ARRAY_A );
			} elseif ( $this->user_has_role( $current_user_id, 'tf_vendor' ) ) {
				$hotel_enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s {$tf_filter_query} AND author_id = %d ORDER BY id DESC", $post_type, $current_user_id ), ARRAY_A );
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
			$id    = $request->get_param( 'id' );
			$enquiry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE id = %d", $id ), ARRAY_A );
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