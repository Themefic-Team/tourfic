<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Integration_Rest_API' ) ) {
	class TF_Integration_Rest_API extends TF_Rest_API {

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
		 * Get google access token url
		 * @author Foysal
		 */
		public function tf_get_google_access_token_url( $request ) {
            $user_id   = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			$access_url = TF_GoogleCalendar::instance($user_id)->GetAccessTokenUrl();

            return $access_url;
		}

		/*
		 * Reset google access token
		 * @author Foysal
		 */
		public function tf_reset_google_access_token( $request ) {
            $user_id   = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			delete_user_meta($user_id, '_tf_integration_settings');
		}
	}
}

TF_Integration_Rest_API::get_instance();