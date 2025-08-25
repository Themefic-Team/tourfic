<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Metabox' ) ) {
	class TF_Metabox {

		public $metabox_id = null;
		public $metabox_title = null;
		public $metabox_post_type = null;
		public $metabox_sections = array();

		public function __construct( $key, $params = array() ) {

			$this->metabox_id        = $key;
			$this->metabox_title     = ! empty( $params['title'] ) ? apply_filters( $key . '_title', $params['title'] ) : '';
			$this->metabox_post_type = $params['post_type'];
			$this->metabox_sections  = ! empty( $params['sections'] ) ? apply_filters( $key . '_sections', $params['sections'] ) : array();

			add_action( 'add_meta_boxes', array( $this, 'tf_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );

			//load fields
			$this->load_fields();
		}

		public static function metabox( $key, $params = array() ) {
			return new self( $key, $params );
		}

		/*
		 * Load fields
		 * @author Foysal
         */
		public function load_fields() {

			// Fields Class
			require_once TF_ADMIN_PATH . 'TF_Options/fields/TF_Fields.php';

			$fields = glob( TF_ADMIN_PATH . 'TF_Options/fields/*/TF_*.php' );

			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					$field_name = basename( $field, '.php' );
					if ( ! class_exists( $field_name ) ) {
						require_once $field;
					}
				}
			}

		}

		/**
		 * Metaboxes
		 * @author Foysal
		 */
		public function tf_meta_box() {
			if ( get_post_type() !== $this->metabox_post_type ) {
				return;
			}

			add_meta_box( $this->metabox_id, "<a href='#' class='tf-mobile-tabs'><i class='fa-solid fa-bars'></i></a>" . $this->metabox_title, array(
				$this,
				'tf_meta_box_content'
			), $this->metabox_post_type, 'normal', 'high', );
		}

		/*
		 * Metabox Content
		 * @author Foysal
		 */
		public function tf_meta_box_content( $post ) {
			// Add nonce for security and authentication.
			wp_nonce_field( 'tf_meta_box_nonce_action', 'tf_meta_box_nonce' );

			// Retrieve an existing value from the database.
			$tf_meta_box_value = get_post_meta( $post->ID, $this->metabox_id, true );

			// Set default values.
			if ( empty( $tf_meta_box_value ) ) {
				$tf_meta_box_value = array();
			}
			if ( empty( $this->metabox_sections ) ) {
				return;
			}
			?>
            <div class="tf-admin-meta-box">
                <div class="tf-admin-tab">
					<?php
					$section_count = 0;
					foreach ( $this->metabox_sections as $key => $section ) : ?>
                        <a class="tf-tablinks <?php echo esc_attr($section_count == 0 ? 'active' : ''); ?>" data-tab="<?php echo esc_attr( $key ) ?>">
							<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
							<?php echo esc_html( $section['title'] ); ?>
                        </a>
						<?php $section_count ++;
                    endforeach; ?>
                </div>

                <div class="tf-tab-wrapper">
					<?php $content_count = 0;
					foreach ( $this->metabox_sections as $key => $section ) : ?>
                        <div id="<?php echo esc_attr( $key ) ?>" class="tf-tab-content <?php echo esc_attr($content_count == 0 ? 'active' : ''); ?>">

							<?php
							if ( ! empty( $section['fields'] ) ):
								foreach ( $section['fields'] as $field ) :

									$default = isset( $field['default'] ) ? $field['default'] : '';
									$value   = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : $default;

									$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
									$tf_option->field( $field, $value, $this->metabox_id );
								endforeach;
							endif; ?>

                        </div>
						<?php $content_count ++; endforeach; ?>
                </div>

            </div>
			<?php
		}

		/*
		 * Save Metabox
		 * @author Foysal
		 */
		public function save_metabox( $post_id ) {
			// Check if a nonce is valid.
			if ( !isset($_POST['tf_meta_box_nonce']) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['tf_meta_box_nonce'])), 'tf_meta_box_nonce_action' ) ) {
				return;
			}

			// Check if the user has permissions to save data.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Check if it's not an autosave.
			if ( wp_is_post_autosave( $post_id ) ) {
				return;
			}

			// Check if it's not a revision.
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}

			$tf_meta_box_value = array();
			$metabox_request = array();
			if ( ! empty( $_POST[ $this->metabox_id ] ) ) {
				$metabox_request = $this->recursive_sanitize( wp_unslash( $_POST[ $this->metabox_id ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}

			if ( ! empty( $metabox_request ) && ! empty( $this->metabox_sections ) ) {
				foreach ( $this->metabox_sections as $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {
								$data = isset( $metabox_request[ $field['id'] ] ) ? $metabox_request[ $field['id'] ] : '';

								$fieldClass = 'TF_' . $field['type'];
								$data       = $fieldClass == 'TF_map' ||  $fieldClass == 'TF_color' ? serialize( $data ) : $data;

								if ( class_exists( $fieldClass ) ) {
									$_field                            = new $fieldClass( $field, $data, $this->metabox_id );
									$tf_meta_box_value[ $field['id'] ] = $_field->sanitize();

									if( !empty($field['is_search_able']) ){
										update_post_meta( $post_id, 'tf_search_'.$field['id'], $_field->sanitize() );
									}
								}

							}
						}
					}
				}
			}

			if ( ! empty( $tf_meta_box_value ) ) {
				update_post_meta( $post_id, $this->metabox_id, $tf_meta_box_value );
			} else {
				delete_post_meta( $post_id, $this->metabox_id );
			}

			
		}

		/**
		 * Recursively sanitize an array or a scalar value.
		 *
		 * @param mixed $data
		 * @return mixed
		 */
		private function recursive_sanitize( $data ) {
			if ( is_array( $data ) ) {
				return array_map( array( $this, 'recursive_sanitize' ), $data );
			}

			// Default sanitization for scalar values
			return sanitize_text_field( wp_unslash( $data ) );
		}


	}
}


