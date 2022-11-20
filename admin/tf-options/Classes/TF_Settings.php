<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Settings' ) ) {
	class TF_Settings {

		public $option_id = null;
		public $option_title = null;
		public $option_icon = null;
		public $option_position = null;
		public $option_sections = array();

		public function __construct( $key, $params = array() ) {
			$this->option_id       = $key;
			$this->option_title    = ! empty( $params['title'] ) ? $params['title'] : '';
			$this->option_icon     = ! empty( $params['icon'] ) ? $params['icon'] : '';
			$this->option_position = ! empty( $params['position'] ) ? $params['position'] : 5;
			$this->option_sections = ! empty( $params['sections'] ) ? $params['sections'] : array();

			// run only is admin panel options, avoid performance loss
			$this->pre_tabs     = $this->pre_tabs( $this->option_sections );
			$this->pre_fields   = $this->pre_fields( $this->option_sections );
			$this->pre_sections = $this->pre_sections( $this->option_sections );

			//options
			add_action( 'admin_menu', array( $this, 'tf_options' ) );

			//save options
			add_action( 'admin_init', array( $this, 'save_options' ) );

			//ajax save options
			add_action( 'wp_ajax_tf_options_save', array( $this, 'tf_ajax_save_options' ) );
		}

		public static function option( $key, $params = array() ) {
			return new self( $key, $params );
		}

		public function pre_tabs( $sections ) {

			$result  = array();
			$parents = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['parent'] ) ) {
					$parents[ $section['parent'] ][ $key ] = $section;
					unset( $sections[ $key ] );
				}
			}

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $key ) && ! empty( $parents[ $key ] ) ) {
					$section['sub_section'] = $parents[ $key ];
				}
				$result[ $key ] = $section;
			}

			return $result;
		}

		public function pre_fields( $sections ) {

			$result = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$result[] = $field;
					}
				}
			}

			return $result;
		}

		public function pre_sections( $sections ) {

			$result = array();

			foreach ( $this->pre_tabs as $tab ) {
				if ( ! empty( $tab['subs'] ) ) {
					foreach ( $tab['subs'] as $sub ) {
						$sub['ptitle'] = $tab['title'];
						$result[]      = $sub;
					}
				}
				if ( empty( $tab['subs'] ) ) {
					$result[] = $tab;
				}
			}

			return $result;
		}

		/**
		 * Options Page menu
		 * @author Foysal
		 */
		public function tf_options() {
			add_menu_page(
				$this->option_title,
				$this->option_title,
				'manage_options',
				$this->option_id,
				array( $this, 'tf_options_page' ),
				$this->option_icon,
				$this->option_position
			);

            //Dashboard submenu
			add_submenu_page(
				$this->option_id,
				__('Dashboard', 'tourfic'),
				__('Dashboard', 'tourfic'),
				'manage_options',
				$this->option_id . '&dashboard=1',
				'__return_null',
			);

			//sections as submenus
			if ( ! empty( $this->pre_tabs ) ) {
				foreach ( $this->pre_tabs as $key => $section ) {
					$parent_tab_key = ! empty( $section['fields'] ) ? $key : array_key_first( $section['sub_section'] );
					add_submenu_page(
						$this->option_id,
						$section['title'],
						$section['title'],
						'manage_options',
						$this->option_id . '#tab=' . esc_attr( $parent_tab_key ),
						'__return_null',
					);
				}
            }

			// remove first submenu
			remove_submenu_page( $this->option_id, $this->option_id );

		}

		/**
		 * Options Page HTML
		 * @author Jahid, Foysal
		 */
		public function tf_dashboard_page() {
            $current_page_url = $this->get_current_page_url();
            $query_string = $this->get_query_string($current_page_url);

			?>
            <div class="tf-deshboard-wrapper" style="display: <?php echo isset($query_string['dashboard']) ? 'block' : 'none' ?>">
                <div class="tf-deshboard-version">
                    <span><?php _e( "Tourfic", "tourfic" ); ?><div class="version"><?php echo esc_attr( TOURFIC ); ?></div></span>
                </div>
                <div class="tf-deshboard-overview">
                    <div class="tf-details-overview">
						<span class="tf-details-overview-title">
							<?php _e( "Hotels Overview", "tourfic" ); ?>
						</span>
                        <div class="tf-details-overview-items">
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-hotel"></i></span><?php _e( "Hotels", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_hotels = array(
										'post_type'      => 'tf_hotel',
										'post_status'    => 'publish',
										'posts_per_page' => - 1
									);
									echo count( $tf_total_hotels );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit.php?post_type=tf_hotel'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-door-closed"></i></span><?php _e( "Features", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_features = get_terms( [
										'taxonomy'   => 'hotel_feature',
										'hide_empty' => false,
									] );
									echo count( $tf_total_features );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit-tags.php?taxonomy=hotel_feature&post_type=tf_hotel'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-door-open"></i></span><?php _e( "Location", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_locations = get_terms( [
										'taxonomy'   => 'hotel_location',
										'hide_empty' => false,
									] );
									echo count( $tf_total_locations );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit-tags.php?taxonomy=hotel_location&post_type=tf_hotel'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                        </div>


                        <span class="tf-details-overview-title">
							<?php _e( "Tours Overview", "tourfic" ); ?>
						</span>
                        <div class="tf-details-overview-items">
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-hiking"></i></span><?php _e( "Tours", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_tours = array(
										'post_type'      => 'tf_tours',
										'post_status'    => 'publish',
										'posts_per_page' => - 1
									);
									echo count( $tf_total_tours );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit.php?post_type=tf_tours'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-map-marker"></i></span><?php _e( "Destinations", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_destinations = get_terms( [
										'taxonomy'   => 'tour_destination',
										'hide_empty' => false,
									] );
									echo count( $tf_total_destinations );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit-tags.php?taxonomy=tour_destination&post_type=tf_tours'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-heartbeat"></i></span><?php _e( "Activities", "tourfic" ); ?><p><?php _e( "Upcoming", "tourfic" ); ?></p></h3>
                                <span><?php echo esc_html( "0" ); ?></span>
                            </div>
                        </div>


                        <span class="tf-details-overview-title">
							<?php _e( "Common Overview", "tourfic" ); ?>
						</span>
                        <div class="tf-details-overview-items">
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-book"></i></span><?php _e( "Bookings", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_order_query_orders = wc_get_orders( array(
											'limit'  => - 1,
											'type'   => 'shop_order',
											'status' => array( 'wc-completed' ),
										)
									);
									echo count( $tf_order_query_orders );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit.php?post_status=wc-completed&post_type=shop_order'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-user-alt"></i></span><?php _e( "Customers", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_customer_query = new WP_User_Query(
										array(
											'role' => 'customer',
										)
									);
									echo count( $tf_customer_query->get_results() );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'users.php?role=customer'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-question"></i></span><?php _e( "Enquiries", "tourfic" ); ?><p><?php _e( "Upcoming", "tourfic" ); ?></p></h3>
                                <span><?php echo esc_html( "0" ); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="tf-details-instractions">
						<div class="tf-video-instractions">
							<span class="tf-details-overview-title">
								<?php _e( "Video Instruction", "tourfic" ); ?>
							</span>
							<iframe width="100%" height="300" src="https://www.youtube.com/embed/xeVkabWobDU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>

						<div class="tf-community-info">
							<span class="tf-details-overview-title">
								<?php _e( "Facebook Community", "tourfic" ); ?>
							</span>
							<div class="tf-facebook-community">
								<div class="icon">
									<i class="fab fa-facebook-f"></i>
								</div>
								<span><?php echo sprintf( "Join our <a target='_blank' href='https://www.facebook.com/groups/tourfic'>Tourfic - Travel Booking Solution for Woocommerce Community </a> Facebook Group for your query or share your thoughts about the plugin with user and us." ); ?></span>
							</div>
							<span class="tf-details-overview-title">
								<?php _e( "Plugin Documentation", "tourfic" ); ?>
							</span>
							<div class="tf-plugin-documentation">
								<div class="tf-plugin-details-info">
									<i class="fa-solid fa-file-alt"></i>
									<span><?php _e( "You’ll get every detailed document regarding the plugin in our documentation website described by our Engineers.", "tourfic" ); ?></span>
								</div>
								<div class="tf-plugin-document-link">
									<?php echo sprintf( "<a target='_blank' href='https://themefic.com/docs/tourfic/'>Visit Documentation</a>" ); ?>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * Options Page
		 * @author Foysal
		 */
		public function tf_options_page() {

			// Retrieve an existing value from the database.
			$tf_option_value = get_option( $this->option_id );
			$current_page_url = $this->get_current_page_url();
			$query_string = $this->get_query_string($current_page_url);

			// Set default values.
			if ( empty( $tf_option_value ) ) {
				$tf_option_value = array();
			}

			$this->tf_dashboard_page();

            $ajax_save_class = 'tf-ajax-save';

			if ( ! empty( $this->option_sections ) ) :
				?>
                <div class="tf-option-wrapper" style="display: <?php echo !isset($query_string['dashboard']) ? 'block' : 'none' ?>">
                    <form method="post" action="" class="tf-option-form <?php echo esc_attr($ajax_save_class) ?>" enctype="multipart/form-data">
                        <!-- Header -->
                        <div class="tf-option-header">
                            <div class="tf-option-header-left">
                                <h2>
								<img src="<?php echo TF_ASSETS_URL; ?>img/tourfic-logo.webp" alt="Tourfic">
								<a href="#" class="tf-mobile-tabs"><i class="fa-solid fa-bars"></i></a>
								</h2>
                            </div>
                            <div class="tf-option-header-right">
                                <div class="tf-option-header-actions">
                                    <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php esc_attr_e( 'Save', 'tourfic' ); ?></button>
                                </div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="tf-option">
                            <div class="tf-admin-tab tf-option-nav">
								<?php
								$section_count = 0;
								foreach ( $this->pre_tabs as $key => $section ) :
									$parent_tab_key = ! empty( $section['fields'] ) ? $key : array_key_first( $section['sub_section'] );
									?>
                                    <div class="tf-admin-tab-item<?php echo ! empty( $section['sub_section'] ) ? ' tf-has-submenu' : '' ?>">
                                        <a href="#<?php echo esc_attr( $parent_tab_key ); ?>"
                                           class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
                                           data-tab="<?php echo esc_attr( $parent_tab_key ) ?>">
											<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
											<?php echo $section['title']; ?>
                                        </a>

										<?php if ( ! empty( $section['sub_section'] ) ): ?>
                                            <ul class="tf-submenu">
												<?php foreach ( $section['sub_section'] as $sub_key => $sub ): ?>
                                                    <li>
                                                        <a href="#<?php echo esc_attr( $sub_key ); ?>"
                                                           class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
                                                           data-tab="<?php echo esc_attr( $sub_key ) ?>">
														<span class="tf-tablinks-inner">
                                                            <?php echo ! empty( $sub['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $sub['icon'] ) . '"></i></span>' : ''; ?>
                                                            <?php echo $sub['title']; ?>
                                                        </span>
                                                        </a>
                                                    </li>
												<?php endforeach; ?>
                                            </ul>
										<?php endif; ?>
                                    </div>
									<?php $section_count ++; endforeach; ?>
                            </div>

                            <div class="tf-tab-wrapper">
								<?php
								$content_count = 0;
								foreach ( $this->option_sections as $key => $section ) : ?>
                                    <div id="<?php echo esc_attr( $key ) ?>" class="tf-tab-content <?php echo $content_count == 0 ? 'active' : ''; ?>">

										<?php
										if ( ! empty( $section['fields'] ) ):
											foreach ( $section['fields'] as $field ) :

												$default = isset( $field['default'] ) ? $field['default'] : '';
												$value   = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : $default;

												$tf_option = new TF_Options();
												$tf_option->field( $field, $value, $this->option_id );
											endforeach;
										endif; ?>

                                    </div>
									<?php $content_count ++; endforeach; ?>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="tf-option-footer">
                            <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php _e( 'Save', 'tourfic' ); ?></button>
                        </div>

						<?php wp_nonce_field( 'tf_option_nonce_action', 'tf_option_nonce' ); ?>
                    </form>
                </div>
			<?php
			endif;
		}

		/**
		 * Save Options
		 * @author Foysal
		 */
		public function save_options() {

			// Add nonce for security and authentication.
			$nonce_name   = isset( $_POST['tf_option_nonce'] ) ? $_POST['tf_option_nonce'] : '';
			$nonce_action = 'tf_option_nonce_action';

			// Check if a nonce is set.
			if ( ! isset( $nonce_name ) ) {
				return;
			}

			// Check if a nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
				return;
			}

			$tf_option_value = array();
			$option_request  = ( ! empty( $_POST[ $this->option_id ] ) ) ? $_POST[ $this->option_id ] : array();
			if ( ! empty( $option_request ) && ! empty( $this->option_sections ) ) {
				foreach ( $this->option_sections as $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {
								$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';

								$fieldClass = 'TF_' . $field['type'];
								if($fieldClass != 'TF_file'){
									$data       = $fieldClass == 'TF_repeater' || $fieldClass == 'TF_map' || $fieldClass == 'TF_tab' || $fieldClass == 'TF_color' ? serialize( $data ) : $data;
								}
								if($fieldClass == 'TF_file'){
									$tf_upload_dir = wp_upload_dir();

									if ( ! empty( $tf_upload_dir['basedir'] ) ) {
									$tf_itinerary_fonts = $tf_upload_dir['basedir'].'/itinerary-fonts';
									if ( ! file_exists( $tf_itinerary_fonts ) ) {
									wp_mkdir_p( $tf_itinerary_fonts );
									}
									if (!empty($_FILES['file'])) {
										$tf_fonts_extantions = array('application/octet-stream');
										for($i = 0; $i < count($_FILES['file']['name']); $i++) {
										if (in_array($_FILES['file']['type'][$i], $tf_fonts_extantions)) {
											$tf_font_filename = $_FILES['file']['name'][$i];
											move_uploaded_file($_FILES['file']['tmp_name'][$i], $tf_itinerary_fonts .'/'. $tf_font_filename);
											}
										}
									}
									}
								}

								if ( class_exists( $fieldClass ) ) {
									$_field                          = new $fieldClass( $field, $data, $this->option_id );
									$tf_option_value[ $field['id'] ] = $_field->sanitize();
								}

							}
						}
					}
				}
			}

			if ( ! empty( $tf_option_value ) ) {
				update_option( $this->option_id, $tf_option_value );
			} else {
				delete_option( $this->option_id );
			}
		}

		/*
		 * Ajax Save Options
		 * @author Foysal
		 */
		public function tf_ajax_save_options() {
			$response    = [
				'status'  => 'error',
				'message' => __( 'Something went wrong!', 'tourfic' ),
			];

            if( ! empty( $_POST['tf_option_nonce'] ) && wp_verify_nonce( $_POST['tf_option_nonce'], 'tf_option_nonce_action' ) ) {
                $this->save_options();
                $response = [
                    'status'  => 'success',
                    'message' => __( 'Options saved successfully!', 'tourfic' ),
                ];
            }

            echo json_encode( $response );
            wp_die();
		}

		/*
		 * Get current page url
		 * @return string
		 * @author Foysal
		 */
		public function get_current_page_url() {
            $page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            return $page_url;
        }

        /*
         * Get query string from url
         * @return array
         * @author Foysal
         */
        public function get_query_string( $url ) {
	        $url_parts = parse_url( $url );
	        parse_str( $url_parts['query'], $query_string );

            return $query_string;
        }
	}
}
