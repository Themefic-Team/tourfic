<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Taxonomy_Metabox' ) ) {
	class TF_Taxonomy_Metabox {

		public $taxonomy_id = null;
		public $taxonomy_title = null;
		public $taxonomy = null;
		public $taxonomy_fields = array();

		public function __construct( $key, $params = array() ) {
			$defaults = array(
				'title'    => '',
				'taxonomy' => 'category',
				'sections' => array(),
			);

			$params = wp_parse_args( $params, $defaults );

			$this->taxonomy_id     = $key;
			$this->taxonomy_title  = $params['title'];
			$this->taxonomy        = $params['taxonomy'];
			$this->taxonomy_fields = $params['fields'];

			//load fields
			$this->load_fields();

			//taxonomies
			add_action( $this->taxonomy . '_edit_form_fields', array( $this, 'tf_taxonomy_content' ), 10, 2 );
			add_action( $this->taxonomy . '_add_form_fields', array( $this, 'tf_taxonomy_content' ), 10, 2 );
			add_action( 'created_' . $this->taxonomy, array( $this, 'save_taxonomy' ), 10, 2 );
			add_action( 'edited_' . $this->taxonomy, array( $this, 'save_taxonomy' ), 10, 2 );

		}

		public static function taxonomy( $key, $params = array() ) {
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

		/*
		 * Metabox Content
		 * @author Foysal
		 */
		public function tf_taxonomy_content( $term ) {
			// Add nonce for security and authentication.
			wp_nonce_field( 'tf_taxonomy_nonce_action', 'tf_taxonomy_nonce' );

			// Retrieve an existing value from the database.
			$is_term           = ( is_object( $term ) && isset( $term->taxonomy ) ) ? true : false;
			$term_id           = ( $is_term ) ? $term->term_id : 0;
			$tf_taxonomy_value = get_term_meta( $term_id, $this->taxonomy_id, true );

			// Set default values.
			if ( empty( $tf_taxonomy_value ) ) {
				$tf_taxonomy_value = array();
			}
			if ( empty( $this->taxonomy_fields ) ) {
				return;
			}

			// Form fields.

			?>
            <tr>
                <td colspan="2">
                    <div class="tf-admin-meta-box tf-taxonomy-metabox">
                        <div class="tf-tab-wrapper">
							<?php
							foreach ( $this->taxonomy_fields as $key => $field ) {
								$default = isset( $field['default'] ) ? $field['default'] : '';
								$value   = isset( $tf_taxonomy_value[ $field['id'] ] ) ? $tf_taxonomy_value[ $field['id'] ] : $default;

								$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
								$tf_option->field( $field, $value, $this->taxonomy_id );
							}
							?>
                        </div>
                    </div>
                </td>
            </tr>
			<?php
		}

		/*
		 * Save Metabox
		 * @author Foysal
		 */
		public function save_taxonomy( $term_id ) {

			// Check if a nonce is valid.
			if ( !isset($_POST['tf_taxonomy_nonce']) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['tf_taxonomy_nonce'])), 'tf_taxonomy_nonce_action' ) ) {
				return;
			}

			$tf_taxonomy_value = array();
			$taxonomy_request  = ( ! empty( $_POST[ $this->taxonomy_id ] ) ) ? $_POST[ $this->taxonomy_id ] : array();

			if ( ! empty( $taxonomy_request ) && ! empty( $this->taxonomy_fields ) ) {
				foreach ( $this->taxonomy_fields as $field ) {

					if ( ! empty( $field['id'] ) ) {
						$data = isset( $taxonomy_request[ $field['id'] ] ) ? $taxonomy_request[ $field['id'] ] : '';

						$fieldClass = 'TF_' . $field['type'];
						$data       = $fieldClass == 'TF_repeater' || $fieldClass == 'TF_map' || $fieldClass == 'TF_tab' || $fieldClass == 'TF_color' ? serialize( $data ) : $data;

						if ( class_exists( $fieldClass ) ) {
							$_field                            = new $fieldClass( $field, $data, $this->taxonomy_id );
							$tf_taxonomy_value[ $field['id'] ] = $_field->sanitize();
						}

					}
				}
			}

			if ( ! empty( $tf_taxonomy_value ) ) {
				update_term_meta( $term_id, $this->taxonomy_id, $tf_taxonomy_value );
			} else {
				delete_term_meta( $term_id, $this->taxonomy_id );
			}

		}

	}
}


