<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_User_Rest_API' ) ) {
	class TF_User_Rest_API extends TF_Rest_API {

		protected $log_reg_settings = array();

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
		 * Get Users
		 * @author Foysal
		 */
		public function tf_get_users( $request ) {
			$user_roles = $request->get_param( 'roles' ) ? $request->get_param( 'roles' ) : array();
			$users      = get_users( array(
				'role__in' => $user_roles,
				'number'   => - 1,
			) );

			$users_data = array();

			foreach ( $users as $user ) {
				global $wpdb;
				$tf_vendor_order_earning = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}tf_vendor_balance_history WHERE wstatus = %s AND vendor_id = %s", "completed", $user->ID ), ARRAY_A );
				$total_earning           = ! empty( $tf_vendor_order_earning ) ? wc_price( $tf_vendor_order_earning[0]['SUM(amount)'] ) : wc_price( 0 );

				$tf_vendor_balace  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_balance WHERE vendor_id = %s", $user->ID ) );
				$total_earning_int = ! empty( $tf_vendor_balace->total_amount ) ? $tf_vendor_balace->total_amount : 0;
				$total_withdraw    = ! empty( $tf_vendor_balace->total_withdraw ) ? wc_price( $tf_vendor_balace->total_withdraw ) : wc_price( 0 );
				$vendor_status     = get_user_meta( $user->ID, 'tf_vendor_approval', true );
				$users_data[]      = array(
					'id'                 => $user->ID,
					'username'           => $user->user_login,
					'email'              => $user->user_email,
					'name'               => $user->display_name,
					'first_name'         => $user->user_firstname,
					'last_name'          => $user->user_lastname,
					'phone'              => get_user_meta( $user->ID, 'tf_user_phone', true ),
					'url'                => $user->user_url,
					'description'        => $user->description,
					'link'               => get_author_posts_url( $user->ID ),
					'locale'             => get_user_locale( $user ),
					'nickname'           => $user->user_nicename,
					'slug'               => $user->user_nicename,
					'roles'              => $user->roles,
					'status'             => $vendor_status === 'enabled' ? esc_html__( 'Approved', 'tourfic' ) : esc_html__( 'Pending', 'tourfic' ),
					'registered_date'    => date( "M d, Y", strtotime( $user->user_registered ) ),
					'total_earning_int'  => $total_earning_int,
					'total_earning'      => $total_earning,
					'total_withdraw'     => $total_withdraw,
					'capabilities'       => $user->allcaps,
					'extra_capabilities' => $user->caps,
					'avatar_urls'        => array(
						'24'  => get_avatar_url( $user->ID, array( 'size' => 24 ) ),
						'48'  => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
						'96'  => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
						'256' => get_avatar_url( $user->ID, array( 'size' => 256 ) ),
					),
					'avatar'             => get_user_meta( $user->ID, 'tf_user_image', true ),
					'cover_pic'          => get_user_meta( $user->ID, 'tf_cover_photo', true ),
					'is_super_admin'     => is_super_admin( $user->ID ),
				);
			}

			return rest_ensure_response( $users_data );
		}

		/*
		 * Get User
		 * @author Foysal
		 */
		public function tf_get_user( $request ) {
			$user_id = $request->get_param( 'id' );
			$user    = get_user_by( 'id', $user_id );

			if ( ! $user ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}

			$user_data = array(
				'id'                 => $user->ID,
				'username'           => $user->user_login,
				'name'               => $user->display_name,
				'first_name'         => $user->user_firstname,
				'last_name'          => $user->user_lastname,
				'email'              => $user->user_email,
				'url'                => $user->user_url,
				'description'        => $user->description,
				'link'               => get_author_posts_url( $user->ID ),
				'locale'             => get_user_locale( $user ),
				'nickname'           => $user->user_nicename,
				'slug'               => $user->user_nicename,
				'roles'              => $user->roles,
				'registered_date'    => $user->user_registered,
				'is_super_admin'     => is_super_admin( $user->ID ),
				'capabilities'       => $user->allcaps,
				'extra_capabilities' => $user->caps,
				'avatar_urls'        => array(
					'24'  => get_avatar_url( $user->ID, array( 'size' => 24 ) ),
					'48'  => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
					'96'  => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
					'256' => get_avatar_url( $user->ID, array( 'size' => 256 ) ),
				),
				'avatar'             => get_user_meta( $user->ID, 'tf_user_image', true ),
				'cover_pic'          => get_user_meta( $user->ID, 'tf_cover_photo', true ),
				'tf_user_phone'      => get_user_meta( $user->ID, 'tf_user_phone', true )
			);

			if ( $this->user_has_role( $user_id, 'tf_vendor' ) ) {
				global $wpdb;
				$tf_vendor_balace  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_balance WHERE vendor_id = %s", $user->ID ) );
				$total_earning     = ! empty( $tf_vendor_balace->total_amount ) ? wc_price( $tf_vendor_balace->total_amount ) : wc_price( 0 );
				$total_earning_int = ! empty( $tf_vendor_balace->total_amount ) ? $tf_vendor_balace->total_amount : 0;
				$total_withdraw    = ! empty( $tf_vendor_balace->total_withdraw ) ? wc_price( $tf_vendor_balace->total_withdraw ) : wc_price( 0 );
				$vendor_status     = get_user_meta( $user->ID, 'tf_vendor_approval', true );

				$user_data['legal_name']                    = get_user_meta( $user->ID, 'tf_is_name', true );
				$user_data['paypal_info']                   = get_user_meta( $user->ID, 'tf_paypal_info', true );
				$user_data['payoneer_info']                 = get_user_meta( $user->ID, 'tf_payoneer_info', true );
				$user_data['wise_info']                     = get_user_meta( $user->ID, 'tf_wise_info', true );
				$user_data['bank_info']                     = get_user_meta( $user->ID, 'tf_bank_info', true );
				$user_data['commission']                    = get_user_meta( $user->ID, 'tf_user_commission', true );
				$user_data['tf_vendor_enabled']             = get_user_meta( $user->ID, 'tf_vendor_approval', true ) == 'enabled' ? '1' : '0';
				$user_data['tf_vendor_posts']               = get_user_meta( $user->ID, 'tf_will_autopublish', true ) == 1 ? '0' : '1';
				$user_data['tf_vendor_featured_permission'] = get_user_meta( $user->ID, 'tf_vendor_featured_permission', true );
				$user_data['tf_vendor_featured_limit']      = get_user_meta( $user->ID, 'tf_vendor_featured_limit', true );
				$user_data['total_earning_int']             = $total_earning_int;
				$user_data['total_earning']                 = $total_earning;
				$user_data['total_withdraw']                = $total_withdraw;
				$user_data['status']                        = $vendor_status;

				$vendor_fields = ! empty( Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] ) ? Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] : '';
				if ( is_array( $vendor_fields ) && ! empty( $vendor_fields ) ) {
					foreach ( $vendor_fields as $field ) {
						if ( $field['reg-fields-type'] == 'checkbox' ) {
							$checkbox_data                         = get_user_meta( $user->ID, $field['reg-field-name'], true );
							$user_data[ $field['reg-field-name'] ] = ! empty( $checkbox_data ) ? explode( '|', $checkbox_data ) : array();
						} else {
							$user_data[ $field['reg-field-name'] ] = get_user_meta( $user->ID, $field['reg-field-name'], true );
						}
					}
				}

				//google data
				$_tf_integration_settings = is_array( get_user_meta( $user->ID, '_tf_integration_settings', true ) ) ? get_user_meta( $user->ID, '_tf_integration_settings', true ) : array();
				$user_data['tf_google_client_id'] = get_user_meta( $user->ID, 'tf_google_client_id', true );
				$user_data['tf_google_secret_key'] = get_user_meta( $user->ID, 'tf_google_secret_key', true );
				$user_data['tf_google_redirect_url'] = site_url().'/wp-json/tourfic/v1/integration/google-api';
				$user_data['tf_google_refresh_token'] = isset( $_tf_integration_settings['google_calendar']['tf_google_calendar']['refresh_token'] ) ? $_tf_integration_settings['google_calendar']['tf_google_calendar']['refresh_token'] : '';
			}

			return rest_ensure_response( $user_data );
		}

		/*
		 * Insert User
		 * @auther Foysal
		 */
		public function tf_insert_user( $request ) {
			$avatar                        = ! empty( $request['avatar'] ) ? $request['avatar'] : '';
			$cover_pic                     = ! empty( $request['cover_pic'] ) ? $request['cover_pic'] : '';
			$username                      = ! empty( $request['username'] ) ? $request['username'] : '';
			$first_name                    = ! empty( $request['first_name'] ) ? $request['first_name'] : '';
			$last_name                     = ! empty( $request['last_name'] ) ? $request['last_name'] : '';
			$email                         = ! empty( $request['email'] ) ? $request['email'] : '';
			$tf_user_phone                 = ! empty( $request['tf_user_phone'] ) ? $request['tf_user_phone'] : '';
			$url                           = ! empty( $request['url'] ) ? $request['url'] : '';
			$description                   = ! empty( $request['description'] ) ? $request['description'] : '';
			$legal_name                    = ! empty( $request['legal_name'] ) ? $request['legal_name'] : '';
			$paypal_info                   = ! empty( $request['paypal_info'] ) ? $request['paypal_info'] : '';
			$payoneer_info                 = ! empty( $request['payoneer_info'] ) ? $request['payoneer_info'] : '';
			$wise_info                     = ! empty( $request['wise_info'] ) ? $request['wise_info'] : '';
			$bank_info                     = ! empty( $request['bank_info'] ) ? $request['bank_info'] : '';
			$commission                    = ! empty( $request['commission'] ) ? $request['commission'] : '';
			$tf_vendor_enabled             = ! empty( $request['tf_vendor_enabled'] ) ? $request['tf_vendor_enabled'] : '';
			$tf_vendor_posts               = ! empty( $request['tf_vendor_posts'] ) ? $request['tf_vendor_posts'] : '';
			$tf_vendor_featured_permission = ! empty( $request['tf_vendor_featured_permission'] ) ? $request['tf_vendor_featured_permission'] : '';
			$tf_vendor_featured_limit      = ! empty( $request['tf_vendor_featured_limit'] ) ? $request['tf_vendor_featured_limit'] : '';
			$password                      = ! empty( $request['password'] ) ? $request['password'] : '';
			$confirm_password              = ! empty( $request['confirm_password'] ) ? $request['confirm_password'] : '';
			$role                          = ! empty( $request['role'] ) ? $request['role'] : '';

			//check email
			$email_exists = email_exists( $email );

			if ( $email_exists ) {
				$response['status']  = false;
				$response['message'] = esc_html__( 'Email already exists.', 'tourfic' );

				return rest_ensure_response( $response );
			}

			//check username
			$username_exists = username_exists( $username );
			if ( $username_exists ) {
				$response['status']  = false;
				$response['message'] = esc_html__( 'Username already exists.', 'tourfic' );

				return rest_ensure_response( $response );
			}

			//check password
			if ( $password !== $confirm_password ) {
				$response['status']  = false;
				$response['message'] = esc_html__( 'Password and confirm password not matched.', 'tourfic' );

				return rest_ensure_response( $response );
			}

			$vendor_fields      = ! empty( Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] ) ? Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] : '';
			$vendor_fields_data = [];
			if ( ! empty( $vendor_fields ) ) {
				foreach ( $vendor_fields as $key => $field ) {
					if ( $field['reg-fields-type'] == 'checkbox' ) {
						$vendor_fields_data[ $field['reg-field-name'] ] = implode( "|", $request[ $field['reg-field-name'] ] );
					} else {
						$vendor_fields_data[ $field['reg-field-name'] ] = sanitize_text_field( $request[ $field['reg-field-name'] ] );
					}
				}
			}

			if ( $password === $confirm_password ) {
				//insert user
				$user_id = wp_insert_user( array(
					'user_login' => $username,
					'user_email' => $email,
					'user_pass'  => $password,
					'user_url'   => $url,
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'role'       => $role,
				) );

				if ( ! empty( $user_id ) ) {

					//update user meta
					update_user_meta( $user_id, 'tf_user_image', $avatar );
					update_user_meta( $user_id, 'tf_cover_photo', $cover_pic );
					update_user_meta( $user_id, 'tf_user_phone', $tf_user_phone );
					update_user_meta( $user_id, 'tf_user_commission', $commission );
					update_user_meta( $user_id, 'tf_user_bio', $description );
					update_user_meta( $user_id, 'description', $description );
					update_user_meta( $user_id, 'tf_is_name', $legal_name );
					update_user_meta( $user_id, 'tf_paypal_info', $paypal_info );
					update_user_meta( $user_id, 'tf_payoneer_info', $payoneer_info );
					update_user_meta( $user_id, 'tf_wise_info', $wise_info );
					update_user_meta( $user_id, 'tf_bank_info', $bank_info );

					$vendor_status = ! empty( $tf_vendor_enabled ) && $tf_vendor_enabled == '1' ? 'enabled' : 'disabled';
					global $wpdb;
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}tf_vendor_balance ( vendor_id, wstatus, created_date ) VALUES ( %d, %s, %s )", array(
						$user_id,
						$vendor_status,
						date( 'Y-m-d' )
					) ) );
					update_user_meta( $user_id, "tf_vendor_approval", $vendor_status );

					update_user_meta( $user_id, "tf_vendor_featured_permission", $tf_vendor_featured_permission );
					update_user_meta( $user_id, "tf_vendor_featured_limit", $tf_vendor_featured_limit );

					// Post Approval
					$autopublish         = 0;
					$tf_reg_user_update = new WP_User( $user_id );
					if ( $tf_reg_user_update instanceof WP_User && $tf_reg_user_update->exists() ) {
						if ( $tf_vendor_posts == '1' ) {
							$tf_reg_user_update->add_cap( 'publish_tf_hotels', false );
							$tf_reg_user_update->add_cap( 'publish_tf_tourss', false );
							$tf_reg_user_update->add_cap( 'publish_tf_apartments', false );
							$tf_reg_user_update->add_cap( 'publish_tf_carrentals', false );
							$autopublish = 0;
						} else {
							$tf_reg_user_update->add_cap( 'publish_tf_hotels' );
							$tf_reg_user_update->add_cap( 'publish_tf_tourss' );
							$tf_reg_user_update->add_cap( 'publish_tf_apartments' );
							$tf_reg_user_update->add_cap( 'publish_tf_carrentals' );
							$autopublish = 1;
						}
					}
					update_user_meta( $user_id, 'tf_will_autopublish', $autopublish );

					//update vendor fields
					if ( ! empty( $vendor_fields_data ) ) {
						foreach ( $vendor_fields_data as $key => $data ) {
							update_user_meta( $user_id, $key, $data );
						}
					}

					$response['status']  = true;
					$response['message'] = esc_html__( 'User created successfully.', 'tourfic' );
				} else {
					$response['status']  = false;
					$response['message'] = esc_html__( 'Something went wrong.', 'tourfic' );
				}
			}

			return rest_ensure_response( $response );
		}

		/*
		 * Update User
		 * @author Foysal
		 */
		public function tf_update_user( $request ) {
			$response     = array(
				'status'  => false,
				'message' => esc_html__( 'Something went wrong. Please try again later.', 'tourfic' ),
				'data'    => array(),
			);
			$user_id      = $request->get_param( 'id' );
			$user         = get_user_by( 'id', $user_id );
			$profile_edit = $request->get_param( 'profile_edit' );

			if ( ! $user ) {
				$response['message'] = esc_html__( 'You are not authorized to access this endpoint.', 'tourfic' );
			}

			$avatar                        = ! empty( $request['avatar'] ) ? $request['avatar'] : '';
			$cover_pic                     = ! empty( $request['cover_pic'] ) ? $request['cover_pic'] : '';
			$first_name                    = ! empty( $request['first_name'] ) ? $request['first_name'] : '';
			$last_name                     = ! empty( $request['last_name'] ) ? $request['last_name'] : '';
			$email                         = ! empty( $request['email'] ) ? $request['email'] : '';
			$tf_user_phone                 = ! empty( $request['tf_user_phone'] ) ? $request['tf_user_phone'] : '';
			$url                           = ! empty( $request['url'] ) ? $request['url'] : '';
			$description                   = ! empty( $request['description'] ) ? $request['description'] : '';
			$legal_name                    = ! empty( $request['legal_name'] ) ? $request['legal_name'] : '';
			$paypal_info                   = ! empty( $request['paypal_info'] ) ? $request['paypal_info'] : '';
			$payoneer_info                 = ! empty( $request['payoneer_info'] ) ? $request['payoneer_info'] : '';
			$wise_info                     = ! empty( $request['wise_info'] ) ? $request['wise_info'] : '';
			$bank_info                     = ! empty( $request['bank_info'] ) ? $request['bank_info'] : '';
			$commission                    = ! empty( $request['commission'] ) ? $request['commission'] : '';
			$tf_vendor_enabled             = ! empty( $request['tf_vendor_enabled'] ) ? $request['tf_vendor_enabled'] : '';
			$tf_vendor_posts               = ! empty( $request['tf_vendor_posts'] ) ? $request['tf_vendor_posts'] : '';
			$tf_vendor_featured_permission = ! empty( $request['tf_vendor_featured_permission'] ) ? $request['tf_vendor_featured_permission'] : '';
			$tf_vendor_featured_limit      = ! empty( $request['tf_vendor_featured_limit'] ) ? $request['tf_vendor_featured_limit'] : '';
			$current_password              = ! empty( $request['current_password'] ) ? $request['current_password'] : '';
			$new_password                  = ! empty( $request['new_password'] ) ? $request['new_password'] : '';
			$confirm_password              = ! empty( $request['confirm_password'] ) ? $request['confirm_password'] : '';
			$tf_google_client_id           = ! empty( $request['tf_google_client_id'] ) ? $request['tf_google_client_id'] : '';
			$tf_google_secret_key          = ! empty( $request['tf_google_secret_key'] ) ? $request['tf_google_secret_key'] : '';
			$tf_google_redirect_url        = ! empty( $request['tf_google_redirect_url'] ) ? $request['tf_google_redirect_url'] : '';

			$user_args = array(
				'ID'          => $user_id,
				'first_name'  => $first_name,
				'last_name'   => $last_name,
				'description' => $description,
				'user_email'  => $email,
				'user_url'    => $url,
			);

			if ( ! $profile_edit ) {
				$user_args['user_pass'] = $new_password;
			}

			//update user
			$update_user_id = wp_update_user( $user_args );

			//update avatar
			update_user_meta( $user_id, 'tf_user_image', $avatar );
			update_user_meta( $user_id, 'tf_cover_photo', $cover_pic );
			update_user_meta( $user_id, 'tf_user_phone', $tf_user_phone );
			update_user_meta( $user_id, 'tf_user_bio', $description );
			update_user_meta( $user_id, 'description', $description );

			if ( $this->user_has_role( $user_id, 'tf_vendor' ) ) {
				update_user_meta( $user_id, 'tf_user_commission', $commission );
				update_user_meta( $user_id, 'tf_is_name', $legal_name );
				update_user_meta( $user_id, 'tf_paypal_info', $paypal_info );
				update_user_meta( $user_id, 'tf_payoneer_info', $payoneer_info );
				update_user_meta( $user_id, 'tf_wise_info', $wise_info );
				update_user_meta( $user_id, 'tf_bank_info', $bank_info );

				$vendor_status = ! empty( $tf_vendor_enabled ) && $tf_vendor_enabled == '1' ? 'enabled' : 'disabled';
				global $wpdb;
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}tf_vendor_balance ( vendor_id, wstatus, created_date ) VALUES ( %d, %s, %s )", array(
					$user_id,
					$vendor_status,
					date( 'Y-m-d' )
				) ) );
				update_user_meta( $user_id, "tf_vendor_approval", $vendor_status );

				update_user_meta( $user_id, "tf_vendor_featured_permission", $tf_vendor_featured_permission );
				update_user_meta( $user_id, "tf_vendor_featured_limit", $tf_vendor_featured_limit );

					// Post Approval
					$autopublish         = 0;
					$tf_reg_user_update = new WP_User( $user_id );
					if ( $tf_reg_user_update instanceof WP_User && $tf_reg_user_update->exists() ) {
						if ( $tf_vendor_posts == '1' ) {
							$tf_reg_user_update->add_cap( 'publish_tf_hotels', false );
							$tf_reg_user_update->add_cap( 'publish_tf_tourss', false );
							$tf_reg_user_update->add_cap( 'publish_tf_apartments', false );
							$tf_reg_user_update->add_cap( 'publish_tf_carrentals', false );
							$autopublish = 0;
						} else {
							$tf_reg_user_update->add_cap( 'publish_tf_hotels' );
							$tf_reg_user_update->add_cap( 'publish_tf_tourss' );
							$tf_reg_user_update->add_cap( 'publish_tf_apartments' );
							$tf_reg_user_update->add_cap( 'publish_tf_carrentals' );
							$autopublish = 1;
						}
					}
					update_user_meta( $user_id, 'tf_will_autopublish', $autopublish );


				$vendor_fields      = ! empty( Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] ) ? Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] : '';
				$vendor_fields_data = [];
				if ( ! empty( $vendor_fields ) ) {
					foreach ( $vendor_fields as $key => $field ) {
						if ( $field['reg-fields-type'] == 'checkbox' ) {
							$vendor_fields_data[ $field['reg-field-name'] ] = implode( "|", $request[ $field['reg-field-name'] ] );
						} else {
							$vendor_fields_data[ $field['reg-field-name'] ] = sanitize_text_field( $request[ $field['reg-field-name'] ] );
						}
					}
				}

				//update vendor fields
				if ( ! empty( $vendor_fields_data ) ) {
					foreach ( $vendor_fields_data as $key => $data ) {
						update_user_meta( $user_id, $key, $data );
					}
				}

				//google integration
				update_user_meta( $user_id, 'tf_google_client_id', $tf_google_client_id );
				update_user_meta( $user_id, 'tf_google_secret_key', $tf_google_secret_key );
				update_user_meta( $user_id, 'tf_google_redirect_url', $tf_google_redirect_url );
			}

			if ( ! empty( $user_id ) ) {
				$user      = get_user_by( 'id', $update_user_id );
				$user_data = array(
					'id'                 => $user->ID,
					'username'           => $user->user_login,
					'name'               => $user->display_name,
					'first_name'         => $user->user_firstname,
					'last_name'          => $user->user_lastname,
					'email'              => $user->user_email,
					'url'                => $user->user_url,
					'description'        => $user->description,
					'link'               => get_author_posts_url( $user->ID ),
					'locale'             => get_user_locale( $user ),
					'nickname'           => $user->user_nicename,
					'slug'               => $user->user_nicename,
					'roles'              => $user->roles,
					'registered_date'    => $user->user_registered,
					'capabilities'       => $user->allcaps,
					'extra_capabilities' => $user->caps,
					'avatar_urls'        => array(
						'24'  => get_avatar_url( $user->ID, array( 'size' => 24 ) ),
						'48'  => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
						'96'  => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
						'256' => get_avatar_url( $user->ID, array( 'size' => 256 ) ),
					),
					'avatar'             => get_user_meta( $user->ID, 'tf_user_image', true ),
					'cover_pic'          => get_user_meta( $user->ID, 'tf_cover_photo', true ),
					'tf_user_phone'      => get_user_meta( $user->ID, 'tf_user_phone', true ),
					'is_super_admin'     => is_super_admin( $user->ID ),
				);

				if ( $this->user_has_role( $user_id, 'tf_vendor' ) ) {
					global $wpdb;
					$tf_vendor_balace  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_vendor_balance WHERE vendor_id = %s", $user->ID ) );
					$total_earning     = ! empty( $tf_vendor_balace->total_amount ) ? wc_price( $tf_vendor_balace->total_amount ) : wc_price( 0 );
					$total_earning_int = ! empty( $tf_vendor_balace->total_amount ) ? $tf_vendor_balace->total_amount : 0;
					$total_withdraw    = ! empty( $tf_vendor_balace->total_withdraw ) ? wc_price( $tf_vendor_balace->total_withdraw ) : wc_price( 0 );
					$vendor_status     = get_user_meta( $user->ID, 'tf_vendor_approval', true );

					$user_data['legal_name']                    = get_user_meta( $user->ID, 'tf_is_name', true );
					$user_data['paypal_info']                   = get_user_meta( $user->ID, 'tf_paypal_info', true );
					$user_data['payoneer_info']                 = get_user_meta( $user->ID, 'tf_payoneer_info', true );
					$user_data['wise_info']                     = get_user_meta( $user->ID, 'tf_wise_info', true );
					$user_data['bank_info']                     = get_user_meta( $user->ID, 'tf_bank_info', true );
					$user_data['commission']                    = get_user_meta( $user->ID, 'tf_user_commission', true );
					$user_data['tf_vendor_enabled']             = get_user_meta( $user->ID, 'tf_vendor_approval', true ) == 'enabled' ? '1' : '0';
					$user_data['tf_vendor_posts']               = get_user_meta( $user->ID, 'tf_will_autopublish', true ) == 1 ? '0' : '1';
					$user_data['tf_vendor_featured_permission'] = get_user_meta( $user->ID, 'tf_vendor_featured_permission', true );
					$user_data['tf_vendor_featured_limit']      = get_user_meta( $user->ID, 'tf_vendor_featured_limit', true );
					$user_data['total_earning_int']             = $total_earning_int;
					$user_data['total_earning']                 = $total_earning;
					$user_data['total_withdraw']                = $total_withdraw;
					$user_data['status']                        = $vendor_status;

					$vendor_fields = ! empty( Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] ) ? Helper::tf_data_types( tfopt( 'log_reg_settings' ) )['vendor-registration'] : '';
					foreach ( $vendor_fields as $field ) {
						if ( $field['reg-fields-type'] == 'checkbox' ) {
							$checkbox_data                         = get_user_meta( $user->ID, $field['reg-field-name'], true );
							$user_data[ $field['reg-field-name'] ] = ! empty( $checkbox_data ) ? explode( '|', $checkbox_data ) : array();
						} else {
							$user_data[ $field['reg-field-name'] ] = get_user_meta( $user->ID, $field['reg-field-name'], true );
						}
					}

					//google data
					$user_data['tf_google_client_id'] = get_user_meta( $user->ID, 'tf_google_client_id', true );
					$user_data['tf_google_secret_key'] = get_user_meta( $user->ID, 'tf_google_secret_key', true );
					$user_data['tf_google_redirect_url'] = get_user_meta( $user->ID, 'tf_google_redirect_url', true );
				}

				$response['status']  = true;
				$response['message'] = esc_html__( 'Profile updated successfully.', 'tourfic' );
				$response['data']    = $user_data;
			}

			if ( $profile_edit && ! empty( $current_password ) && ! empty( $new_password ) && ! empty( $confirm_password ) ) {
				//check current password
				$check_password = wp_check_password( $current_password, $user->user_pass, $user->ID );

				if ( $check_password ) {
					if ( $new_password === $confirm_password ) {
						//update password
						wp_set_password( $new_password, $user->ID );

						$response['status']       = true;
						$response['message']      = esc_html__( 'Password updated successfully.', 'tourfic' );
						$response['redirect_url'] = isset( $this->log_reg_settings['login_page'] ) ? get_permalink( $this->log_reg_settings['login_page'] ) : get_permalink( get_option( 'tf_login_page_id' ) );
					} else {
						$response['status']  = false;
						$response['message'] = esc_html__( 'New password and confirm password not matched.', 'tourfic' );
					}
				} else {
					$response['status']  = false;
					$response['message'] = esc_html__( 'Current password not matched.', 'tourfic' );
				}
			}

			return rest_ensure_response( $response );
		}

		/*
		 * Update User Status
		 * @auther Foysal
		 */
		public function tf_update_user_status( $request ) {
			$id          = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$user_status = ! empty( $request->get_param( 'user_status' ) ) ? $request->get_param( 'user_status' ) : '';

			if ( empty( $id ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}

			$user = get_user_by( 'id', $id );

			if ( ! $user ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}

			if ( $user_status == 'enabled' ) {
				update_user_meta( $id, 'tf_vendor_approval', 'enabled' );
			} else {
				update_user_meta( $id, 'tf_vendor_approval', 'disabled' );
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => esc_html__( 'User status updated successfully.', 'tourfic' ),
			) );
		}

		/*
		 * Logout User
		 * @auther Foysal
		 */
		public function tf_logout_user( $request ) {
			$user = wp_get_current_user();

			if ( ! $user ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not authorized to access this endpoint.' ), array( 'status' => 403 ) );
			}

			wp_logout();

			return array(
				'success'      => true,
				'message'      => esc_html__( 'You are logged out successfully.', 'tourfic' ),
				'redirect_url' => isset( $this->log_reg_settings['login_page'] ) ? get_permalink( $this->log_reg_settings['login_page'] ) : get_permalink( get_option( 'tf_login_page_id' ) ),
			);
		}

		/*
		 * User Bookings
		 * @auther Foysal
		 */
		public function tf_user_bookings( $request ) {
			$current_user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			$booking_type    = $request->get_param( 'booking_type' ) ? $request->get_param( 'booking_type' ) : 'all';
			$disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : [];

			if ( $this->user_has_role( $current_user_id, 'customer' ) ) {

				if($booking_type == 'all'){
					$post_types = array();
					if (! in_array('hotel', $disable_services)){
						$post_types[] = 'hotel';
					}
					if (! in_array('tour', $disable_services)){
						$post_types[] = 'tour';
					}
					if (! in_array('apartment', $disable_services)){
						$post_types[] = 'apartment';
					}
					if (! in_array('carrentals', $disable_services)){
						$post_types[] = 'car';
					}
				} else {
					$post_types = array( $booking_type );
				}

				$tf_orders_select = array(
					'select'      => "*",
					'post_type'   =>  $post_types,
					'customer_id' => $current_user_id,
					'limit'       => ""
				);

				$user_hotel_orders_result = tourfic_get_user_order_table_data( $tf_orders_select );
			}
			$orders_data = array();
			foreach ( $user_hotel_orders_result as $order ) {
				//payment method
				$payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
				if ( isset( $payment_gateways[ $order['payment_method'] ] ) ) {
					$order['payment_method'] = $payment_gateways[ $order['payment_method'] ]->title;
				} else {
					$order['payment_method'] = __( 'Offline Payment', 'tourfic' );
				}

				//total price
				$order_details = json_decode( $order['order_details'] );
				$hotel_price   = "";
				if ( ! empty( $order_details->total_price ) ) {
					$hotel_price .= '<b>' . __( "Total", "tourfic" ) . ': </b>' . wc_price( $order_details->total_price ) . '<br>';
				}
				if ( ! empty( $order_details->due_price ) ) {
					$hotel_price .= '<b>' . __( "Due", "tourfic" ) . ': </b>' . $order_details->due_price . '<br>';
				}
				$order['total_price'] = $hotel_price;

				$orders_data[] = $order;
			}

			return $orders_data;

		}

		/*
		 * User Wishlist
		 * @auther Foysal
		 */
		public function tf_user_wishlist( $request ) {
			$current_user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
			$remove          = $request->get_param( 'remove' ) ? $request->get_param( 'remove' ) : false;
			$postId          = $request->get_param( 'post_id' ) ? $request->get_param( 'post_id' ) : '';

			if ( $this->user_has_role( $current_user_id, 'customer' ) ) {
				$wishlist_items = get_user_meta( $current_user_id, 'wishlist_item', false );

				if ( $remove == true && ! empty( $postId ) ) {
					$post_id = array_search($postId, array_column($wishlist_items, 'post_id'));
					delete_user_meta($current_user_id, 'wishlist_item', $wishlist_items[$post_id]);
				}

				$wishlist_items = get_user_meta( $current_user_id, 'wishlist_item', false );
				$wishlist_data = array();
				foreach ( $wishlist_items as $item ) {
					$wishlist_data[] = get_post( $item['post_id'] );
				}
			}

			return $wishlist_data;
		}
	}
}

TF_User_Rest_API::get_instance();
