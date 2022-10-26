<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Settings' ) ) {
	class TF_Settings {

		public $option_id = null;
		public $option_title = null;
		public $option_sections = array();

		public function __construct( $key, $params = array() ) {
			$this->option_id       = $key;
			$this->option_title    = $params['title'];
			$this->option_sections = $params['sections'];

			//options
			add_action( 'admin_menu', array( $this, 'tf_options' ) );

			//save options
			add_action( 'admin_init', array( $this, 'save_options' ) );

		}

		public static function option( $key, $params = array() ) {
			return new self( $key, $params );
		}

		/**
		 * Options Page
		 * @author Foysal
		 */
		public function tf_options() {
			add_menu_page(
				$this->option_title,
				$this->option_title,
				'manage_options',
				$this->option_id,
				array( $this, 'tf_options_page' ),
				'dashicons-admin-generic',
				5
			);

			//sections as submenus
			if ( ! empty( $this->option_sections ) ) {
				foreach ( $this->option_sections as $key => $section ) {
					add_submenu_page(
						$this->option_id,
						$section['title'],
						$section['title'],
						'manage_options',
						$this->option_id . '#' . sanitize_title( $key ),
						'__return_null'
					);
				}

				//remove first submenu
				remove_submenu_page( $this->option_id, $this->option_id );
			}
		}

		/**
		 * Options Page
		 * @author Foysal
		 */
		public function tf_options_page() {

			// Retrieve an existing value from the database.
			$tf_option_value = get_option( $this->option_id );

			// Set default values.
			if ( empty( $tf_option_value ) ) {
				$tf_option_value = array();
			}
			if ( empty( $this->option_sections ) ) {
				return;
			}
			?>
            <div class="tf-admin-option-wrapper">
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="tf-admin-option">
                        <div class="tf-admin-tab tf-option-nav">
							<?php
							$section_count = 0;
							foreach ( $this->option_sections as $key => $section ) : ?>
                                <a href="#<?php echo $key; ?>" class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>" onclick="openTab(event, '<?php echo esc_attr( $key ) ?>')"
                                   data-tab="<?php echo esc_attr( $key ) ?>">
									<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
									<?php echo $section['title']; ?>
                                </a>
								<?php $section_count ++; endforeach; ?>
                        </div>

                        <div class="tf-tab-wrapper">
							<?php
							$content_count = 0;
							foreach ( $this->option_sections as $key => $section ) : ?>
                                <div id="<?php echo $key; ?>" class="tf-tab-content <?php echo $content_count == 0 ? 'active' : ''; ?>">

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

                    <!-- save -->
                    <div class="tf-admin-option-footer">
                        <button type="submit" class="button button-primary button-large"><?php _e( 'Save Changes', 'tourfic' ); ?></button>
                    </div>

					<?php wp_nonce_field( 'tf_option_nonce_action', 'tf_option_nonce' ); ?>
                </form>
            </div>

			<?php
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
								$data       = $fieldClass == 'TF_repeater' || $fieldClass == 'TF_map' || $fieldClass == 'TF_tab' || $fieldClass == 'TF_color' ? serialize( $data ) : $data;

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
	}
}
