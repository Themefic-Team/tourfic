<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Options' ) ) {
	class TF_Options {

		private static $instance = null;

		/**
		 * Singleton instance
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			//load files
			$this->load_files();

			//load metaboxes
			$this->load_metaboxes();

			//load options
			$this->load_options();

			//load taxonomy
			$this->load_taxonomy();

			//enqueue scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'tf_options_admin_enqueue_scripts' ),9 );
			add_action( 'wp_enqueue_scripts', array( $this, 'tf_options_wp_enqueue_scripts' ) );
		}

		public function tf_options_version() {
			return '1.0.0';
		}

		public function tf_options_file_path( $file_path = '' ) {
			return plugin_dir_path( __FILE__ ) . $file_path;
		}

		public function tf_options_file_url( $file_url = '' ) {
			return plugin_dir_url( __FILE__ ) . $file_url;
		}

		/**
		 * Load files
		 * @author Foysal
		 */
		public function load_files() {
			// Metaboxes Class
			require_once $this->tf_options_file_path( 'classes/TF_Metabox.php' );
			// Settings Class
			require_once $this->tf_options_file_path( 'classes/TF_Settings.php' );
			//Shortcodes Class
			require_once $this->tf_options_file_path( 'classes/TF_Shortcodes.php' );
			//Taxonomy Class
			require_once $this->tf_options_file_path( 'classes/TF_Taxonomy_Metabox.php' );

		}

		/**
		 * Load metaboxes
		 * @author Foysal
		 */
		public function load_metaboxes() {
			if ( $this->is_tf_pro_active() ) {
				$metaboxes = glob( TF_PRO_ADMIN_PATH . 'tf-options/metaboxes/*.php' );
			} else {
				$metaboxes = glob( $this->tf_options_file_path( 'metaboxes/*.php' ) );
			}

			/*if( !empty( $pro_metaboxes ) ) {
				$metaboxes = array_merge( $metaboxes, $pro_metaboxes );
			}*/
			if ( ! empty( $metaboxes ) ) {
				foreach ( $metaboxes as $metabox ) {
					if ( file_exists( $metabox ) ) {
						require_once $metabox;
					}
				}
			}
		}

		/**
		 * Load Options
		 * @author Foysal
		 */
		public function load_options() {
			if ( $this->is_tf_pro_active() ) {
				$options = glob( TF_PRO_ADMIN_PATH . 'tf-options/options/*.php' );
			} else {
				$options = glob( $this->tf_options_file_path( 'options/*.php' ) );
			}

			if ( ! empty( $options ) ) {
				foreach ( $options as $option ) {
					if ( file_exists( $option ) ) {
						require_once $option;
					}
				}
			}
		}

		/**
		 * Load Taxonomy
		 * @author Foysal
		 */
		public function load_taxonomy() {
			if ( $this->is_tf_pro_active() ) {
				$taxonomies = glob( TF_PRO_ADMIN_PATH . 'tf-options/taxonomies/*.php' );
			} else {
				$taxonomies = glob( $this->tf_options_file_path( 'taxonomies/*.php' ) );
			}

			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					if ( file_exists( $taxonomy ) ) {
						require_once $taxonomy;
					}
				}
			}
		}

		/**
		 * Admin Enqueue scripts
		 * @author Foysal
		 */
		public function tf_options_admin_enqueue_scripts( $screen ) {
			global $post_type;
			$tf_options_screens   = array(
				'toplevel_page_tf_settings',
				'tourfic-settings_page_tf_get_help',
				'tourfic-settings_page_tf_license_info',
				'tourfic-settings_page_tf_dashboard',
				'tourfic-settings_page_tf_shortcodes',
				'tourfic-vendor_page_tf_vendor_reports',
				'tourfic-vendor_page_tf_vendor_list',
				'tourfic-vendor_page_tf_vendor_commissions',
				'tourfic-vendor_page_tf_vendor_withdraw',
				'tf_hotel_page_tf-hotel-backend-booking',
				'tf_tours_page_tf-tour-backend-booking'
			);
			$tf_options_post_type = array( 'tf_hotel', 'tf_tours', 'tf_apartment' );
			$admin_date_format_for_users  = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";
			if("tourfic-settings_page_tf_dashboard"==$screen){
				//Order Data Retrive
				$tf_old_order_limit = new WC_Order_Query( array(
					'limit'   => - 1,
					'orderby' => 'date',
					'order'   => 'ASC',
					'return'  => 'ids',
				) );
				$order              = $tf_old_order_limit->get_orders();
				// Booking Month
				$tf_co1  = 0;
				$tf_co2  = 0;
				$tf_co3  = 0;
				$tf_co4  = 0;
				$tf_co5  = 0;
				$tf_co6  = 0;
				$tf_co7  = 0;
				$tf_co8  = 0;
				$tf_co9  = 0;
				$tf_co10 = 0;
				$tf_co11 = 0;
				$tf_co12 = 0;
				// Booking Cancel Month
				$tf_cr1  = 0;
				$tf_cr2  = 0;
				$tf_cr3  = 0;
				$tf_cr4  = 0;
				$tf_cr5  = 0;
				$tf_cr6  = 0;
				$tf_cr7  = 0;
				$tf_cr8  = 0;
				$tf_cr9  = 0;
				$tf_cr10 = 0;
				$tf_cr11 = 0;
				$tf_cr12 = 0;
				foreach ( $order as $item_id => $item ) {
					$itemmeta         = wc_get_order( $item );
					$tf_ordering_date = $itemmeta->get_date_created();
					if ( $tf_ordering_date->date( 'n-y' ) == '1-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co1 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr1 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '2-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co2 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr2 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '3-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co3 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr3 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '4-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co4 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr4 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '5-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co5 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr5 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '6-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co6 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr6 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '7-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co7 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr7 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '8-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co8 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr8 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '9-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co9 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr9 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '10-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co10 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr10 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '11-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co11 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr11 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '12-' . date( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co12 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr12 += 1;
						}
					}
				}
				$tf_complete_orders = [ $tf_co1, $tf_co2, $tf_co3, $tf_co4, $tf_co5, $tf_co6, $tf_co7, $tf_co8, $tf_co9, $tf_co10, $tf_co11, $tf_co12 ];
				$tf_cancel_orders   = [ $tf_cr1, $tf_cr2, $tf_cr3, $tf_cr4, $tf_cr5, $tf_cr6, $tf_cr7, $tf_cr8, $tf_cr9, $tf_cr10, $tf_cr11, $tf_cr12 ];
				$tf_chart_enable    = 1;
			}


			//Css

			//Color-Picker Css
			wp_enqueue_style( 'wp-color-picker' );
			if ( in_array( $screen, $tf_options_screens ) || in_array( $post_type, $tf_options_post_type ) ) {

				wp_enqueue_style( 'tf-fontawesome-4', '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), $this->tf_options_version() );
				wp_enqueue_style( 'tf-fontawesome-5', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), $this->tf_options_version() );
				wp_enqueue_style( 'tf-fontawesome-6', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css', array(), $this->tf_options_version() );
				wp_enqueue_style( 'tf-remixicon', '//cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css', array(), $this->tf_options_version() );
				wp_enqueue_style( 'tf-select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), $this->tf_options_version() );
				wp_enqueue_style( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css', array(), $this->tf_options_version() );
			}

			//Js
			if ( in_array( $screen, $tf_options_screens ) || in_array( $post_type, $tf_options_post_type ) ) {
				wp_enqueue_script( 'Chart-js', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js', array( 'jquery' ), '2.6.0', true );
				wp_enqueue_script( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js', array( 'jquery' ), $this->tf_options_version(), true );
				wp_enqueue_script( 'tf-select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), $this->tf_options_version(), true );


				$tf_google_map = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( tfopt( 'google-page-option' ) ) ? tfopt( 'google-page-option' ) : "false";
				if ( $tf_google_map != "googlemap" ) {
					wp_enqueue_script( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.js' ), array( 'jquery' ), '1.9', true );
					wp_enqueue_style( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.css' ), array(), '1.9' );
				}
				wp_enqueue_script( 'jquery-ui-autocomplete' );

				if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
					wp_enqueue_script( 'jquery-ui-sortable' );
				}
				wp_enqueue_media();
				wp_enqueue_editor();
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			$tf_google_map = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( tfopt( 'google-page-option' ) ) ? tfopt( 'google-page-option' ) : "false";
			wp_localize_script( 'tf-admin', 'tf_options', array(
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'tf_options_nonce' ),
				'gmaps'             => $tf_google_map,
				'tf_complete_order' => isset( $tf_complete_orders ) ? $tf_complete_orders : '',
				'tf_cancel_orders'  => isset( $tf_cancel_orders ) ? $tf_cancel_orders : '',
				'tf_chart_enable'   => isset( $tf_chart_enable ) ? $tf_chart_enable : '',
				'tf_admin_date_format' => $admin_date_format_for_users
			) );
		}

		/**
		 * Dequeue scripts
		 */
		public function tf_options_admin_dequeue_scripts( $screen ) {
			global $post_type;
			$tf_options_post_type = array( 'tf_hotel', 'tf_tours', 'tf_apartment' );

			if ( $screen == 'toplevel_page_tf_settings' || in_array( $post_type, $tf_options_post_type ) ) {
				wp_dequeue_script( 'theplus-admin-js-pro' );
			}
		}

		/**
		 * Enqueue scripts
		 * @author Foysal
		 */
		public function tf_options_wp_enqueue_scripts() {
			wp_enqueue_style( 'tf-fontawesome-4', '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), $this->tf_options_version() );
			wp_enqueue_style( 'tf-fontawesome-5', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), $this->tf_options_version() );
			wp_enqueue_style( 'tf-fontawesome-6', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css', array(), $this->tf_options_version() );
			wp_enqueue_style( 'tf-remixicon', '//cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css', array(), $this->tf_options_version() );
		}

		/*
		 * Field Base
		 * @author Foysal
		 */
		public function field( $field, $value, $settings_id = '', $parent = '' ) {
			if ( $field['type'] == 'repeater' ) {
				$id = ( ! empty( $settings_id ) ) ? $settings_id . '[' . $field['id'] . '][0]' . '[' . $field['id'] . ']' : $field['id'] . '[0]' . '[' . $field['id'] . ']';
			} else {
				$id = $settings_id . '[' . $field['id'] . ']';
			}

			$class = isset( $field['class'] ) ? $field['class'] : '';

			$is_pro   = isset( $field['is_pro'] ) ? $field['is_pro'] : '';
			$badge_up = isset( $field['badge_up'] ) ? $field['badge_up'] : '';

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$is_pro = false;
			}
			if ( $is_pro == true ) {
				$class .= ' tf-field-disable tf-field-pro';
			}
			if ( $badge_up == true ) {
				$class .= ' tf-field-disable tf-field-upcoming';
			}
			$tf_meta_box_dep_value = get_post_meta( get_the_ID(), $settings_id, true );


			$depend = '';
			if ( ! empty( $field['dependency'] ) ) {

				$dependency      = $field['dependency'];
				$depend_visible  = '';
				$data_controller = '';
				$data_condition  = '';
				$data_value      = '';
				$data_global     = '';

				if ( is_array( $dependency[0] ) ) {
					$data_controller = implode( '|', array_column( $dependency, 0 ) );
					$data_condition  = implode( '|', array_column( $dependency, 1 ) );
					$data_value      = implode( '|', array_column( $dependency, 2 ) );
					$data_global     = implode( '|', array_column( $dependency, 3 ) );
					$depend_visible  = implode( '|', array_column( $dependency, 4 ) );
				} else {
					$data_controller = ( ! empty( $dependency[0] ) ) ? $dependency[0] : '';
					$data_condition  = ( ! empty( $dependency[1] ) ) ? $dependency[1] : '';
					$data_value      = ( ! empty( $dependency[2] ) ) ? $dependency[2] : '';
					$data_global     = ( ! empty( $dependency[3] ) ) ? $dependency[3] : '';
					$depend_visible  = ( ! empty( $dependency[4] ) ) ? $dependency[4] : '';
				}

				$depend .= ' data-controller="' . esc_attr( $data_controller ) . '' . $parent . '"';
				$depend .= ' data-condition="' . esc_attr( $data_condition ) . '"';
				$depend .= ' data-value="' . esc_attr( $data_value ) . '"';
				$depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';

				$visible = ( ! empty( $depend_visible ) ) ? ' tf-depend-visible' : ' tf-depend-hidden';
			}

			//field width
			$field_width = isset( $field['field_width'] ) && ! empty( $field['field_width'] ) ? esc_attr( $field['field_width'] ) : '100';
			if ( $field_width == '100' ) {
				$field_style = 'width:100%;';
			} else {
				$field_style = 'width:calc(' . $field_width . '% - 10px);';
			}
			?>

            <div class="tf-field tf-field-<?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $class ); ?> <?php echo ! empty( $visible ) ? $visible : ''; ?>" <?php echo ! empty( $depend ) ? $depend : ''; ?>
                 style="<?php echo esc_attr( $field_style ); ?>">

				<?php if ( ! empty( $field['label'] ) ): ?>
                    <label for="<?php echo esc_attr( $id ) ?>" class="tf-field-label">
						<?php echo esc_html( $field['label'] ) ?>
						<?php if ( $is_pro ): ?>
                            <div class="tf-csf-badge"><span class="tf-pro"><?php _e( "Pro", "tourfic" ); ?></span></div>
						<?php endif; ?>
						<?php if ( $badge_up ): ?>
                            <div class="tf-csf-badge"><span class="tf-upcoming"><?php _e( "Upcoming", "tourfic" ); ?></span></div>
						<?php endif; ?>
                    </label>
				<?php endif; ?>

				<?php if ( ! empty( $field['subtitle'] ) ) : ?>
                    <span class="tf-field-sub-title"><?php echo wp_kses_post( $field['subtitle'] ) ?></span>
				<?php endif; ?>

                <div class="tf-fieldset">
					<?php
					$fieldClass = 'TF_' . $field['type'];
					if ( class_exists( $fieldClass ) ) {
						$_field = new $fieldClass( $field, $value, $settings_id, $parent );
						$_field->render();
					} else {
						echo '<p>' . __( 'Field not found!', 'tourfic' ) . '</p>';
					}
					?>
                </div>
				<?php if ( ! empty( $field['description'] ) ): ?>
                    <p class="description"><?php echo wp_kses_post( $field['description'] ) ?></p>
				<?php endif; ?>
            </div>
			<?php
		}

		public function is_tf_pro_active() {
			if ( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) && defined( 'TF_PRO' ) ) {
				return true;
			}

			return false;
		}

	}
}

TF_Options::instance();