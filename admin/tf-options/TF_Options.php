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
			add_action( 'admin_enqueue_scripts', array( $this, 'tf_options_enqueue_scripts' ) );
		}

		/**
		 * Load files
		 * @author Foysal
		 */
		public function load_files() {
			// Metaboxes Class
			require_once TF_ADMIN_PATH . 'tf-options/Classes/TF_Metabox.php';
			// Settings Class
			require_once TF_ADMIN_PATH . 'tf-options/Classes/TF_Settings.php';
            //Taxonomy Class
			require_once TF_ADMIN_PATH . 'tf-options/Classes/TF_Taxonomy_Metabox.php';

		}

		/**
		 * Load metaboxes
		 * @author Foysal
		 */
		public function load_metaboxes() {
			$metaboxes = glob( TF_ADMIN_PATH . 'tf-options/metaboxes/*.php' );
			if ( ! empty( $metaboxes ) ) {
				foreach ( $metaboxes as $metabox ) {
					$metabox_name = basename( $metabox, '.php' );
					if ( ! class_exists( $metabox_name ) ) {
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
			$options = glob( TF_ADMIN_PATH . 'tf-options/options/*.php' );
			if ( ! empty( $options ) ) {
				foreach ( $options as $option ) {
					$option_name = basename( $option, '.php' );
					if ( ! class_exists( $option_name ) ) {
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
			$taxonomies = glob( TF_ADMIN_PATH . 'tf-options/taxonomies/*.php' );
			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$taxonomy_name = basename( $taxonomy, '.php' );
					if ( ! class_exists( $taxonomy_name ) ) {
						require_once $taxonomy;
					}
				}
			}
		}

		/**
		 * Enqueue scripts
		 * @author Foysal
		 */
		public function tf_options_enqueue_scripts() {
			//Css
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'tf-fontawesome-4', '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-fontawesome-5', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-fontawesome-6', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-remixicon', '//cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css', array(), TOURFIC );
			wp_enqueue_style( 'tf-options', TF_ADMIN_URL . 'tf-options/assets/css/tf-options.css', array(), TOURFIC );

			//Js
			wp_enqueue_script( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js', array( 'jquery' ), TOURFIC, true );
			wp_enqueue_script( 'tf-select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), TOURFIC, true );
			wp_enqueue_script( 'wp-color-picker-alpha', TF_ADMIN_URL . 'tf-options/assets/js/wp-color-picker-alpha.js', array( 'jquery', 'wp-color-picker' ), TOURFIC, true );
			wp_enqueue_script( 'tf-options', TF_ADMIN_URL . 'tf-options/assets/js/tf-options.js', array( 'jquery', 'wp-color-picker' ), TOURFIC, true );
			wp_localize_script( 'tf-options', 'tf_options', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'tf_options_nonce' ),
			) );

			wp_enqueue_script( 'tf-leaflet', esc_url( 'https://cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.js' ), array( 'jquery' ), '1.9', true );
			wp_enqueue_style( 'tf-leaflet', esc_url( 'https://cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.css' ), array(), '1.9' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

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

			$class    = isset( $field['class'] ) ? $field['class'] : '';
			$is_pro   = isset( $field['is_pro'] ) ? $field['is_pro'] : '';
			$badge_up = isset( $field['badge_up'] ) ? $field['badge_up'] : '';

			if ( isset( $field['is_pro'] ) || isset( $field['badge_up'] ) ) {
				$class .= 'tf-csf-disable tf-csf-pro';
			}
			$tf_meta_box_dep_value = get_post_meta( get_the_ID(  ), $settings_id, true );

			$depend     = '';

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

				$depend .= ' data-controller="' . esc_attr( $data_controller ) . '"';
				$depend .= ' data-condition="' . esc_attr( $data_condition ) . '"';
				$depend .= ' data-value="' . esc_attr( $data_value ) . '"';
				$depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';

				$visible = ( ! empty( $depend_visible ) ) ? ' csf-depend-visible' : ' csf-depend-hidden';
			}
			?>
			<?php 
			if( !empty($data_value) ){  
				$tfcheck_type = gettype($tf_meta_box_dep_value[$data_controller]);
			}
			?>
            <div class="tf-field tf-field-<?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $class ); ?> <?php echo !empty($visible) ? $visible : ''; ?>" <?php echo !empty($depend) ? $depend : ''; ?> <?php echo !empty($data_value) && $tfcheck_type== "string" && $tf_meta_box_dep_value[$data_controller]!=$data_value ? 'style="display:none"' : ''; ?> <?php echo !empty($data_value) && $tfcheck_type== "array" && !in_array ( $data_value, $tf_meta_box_dep_value[$data_controller] ) ? 'style="display:none"' : ''; ?> >

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

	}
}

TF_Options::instance();