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
			$defaults = array(
				'title'     => '',
				'post_type' => 'post',
				'sections'  => array(),
			);

			$params = wp_parse_args( $params, $defaults );

			$this->metabox_id        = $key;
			$this->metabox_title     = $params['title'];
			$this->metabox_post_type = $params['post_type'];
			$this->metabox_sections  = $params['sections'];

			//load fields
			$this->load_fields();

			//metaboxes
			add_action( 'add_meta_boxes', array( $this, 'tf_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
		}

		public static function metabox( $key, $params = array() ) {
			return new self( $key, $params );
		}

		// Include fields
		public function load_fields() {

			// Fields Class
			require_once TF_ADMIN_PATH . 'tf-options/fields/TF_Fields.php';

			$fields = glob( TF_ADMIN_PATH . 'tf-options/fields/*/TF_*.php' );

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
		 *
		 * @since 1.0.0
		 */
		public function tf_meta_box() {
			add_meta_box( $this->metabox_id, $this->metabox_title, array( $this, 'tf_meta_box_content' ), $this->metabox_post_type, 'normal', 'high', );
		}

		public function tf_meta_box_content( $post ) {
			// Add nonce for security and authentication.
			wp_nonce_field( 'tf_meta_box_nonce_action', 'tf_meta_box_nonce' );

			// Retrieve an existing value from the database.
			$tf_meta_box_value = get_post_meta( $post->ID, $this->metabox_id, true );

			// Set default values.
			if ( empty( $tf_meta_box_value ) ) {
				$tf_meta_box_value = array();
			}

			// Form fields.
			?>
            <div class="tf-admin-meta-box">
                <div class="tf-admin-tab">
					<?php foreach ( $this->metabox_sections as $key => $section ) : ?>
                        <a class="tf-tablinks" onclick="openTab(event, '<?php echo esc_attr( $key ) ?>')"><?php echo esc_html( $section['title'] ); ?></a>
					<?php endforeach; ?>
                </div>

                <div class="tf-tab-wrapper">
					<?php foreach ( $this->metabox_sections as $key => $section ) : ?>
                        <div id="<?php echo esc_attr( $key ) ?>" class="tf-tab-content">

							<?php foreach ( $section['fields'] as $field ) :
								$id = $this->metabox_id . '[' . $field['id'] . ']';
								$value = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : '';
								?>

                                <div class="tf-field tf-field-<?php echo esc_attr( $field['type'] ); ?>">
                                    <label for="<?php echo esc_attr( $id ) ?>" class="tf-field-title"><?php echo esc_html( $field['title'] ) ?></label>
									<?php if ( !empty( $field['subtitle'] ) ) : ?>
                                        <span class="tf-field-sub-title"><?php echo wp_kses_post( $field['subtitle'] ) ?></span>
									<?php endif; ?>

                                    <div class="tf-fieldset">
										<?php
										$fieldClass = 'TF_' . $field['type'];
										if ( class_exists( $fieldClass ) ) {
											$_field = new $fieldClass( $field, $value, $this->metabox_id );
											$_field->render();
										} else {
											echo '<p>' . __( 'Field not found!', 'tourfic' ) . '</p>';
										}
										?>
                                    </div>
                                    <p class="description"><?php echo !empty($field['description']) ? wp_kses_post( $field['description'] ) : '' ?></p>
                                </div>

							<?php endforeach; ?>

                        </div>
					<?php endforeach; ?>
                </div>

            </div>
			<?php
		}

		public function save_metabox( $post_id ) {
			// Add nonce for security and authentication.
			$nonce_name   = isset( $_POST['tf_meta_box_nonce'] ) ? $_POST['tf_meta_box_nonce'] : '';
			$nonce_action = 'tf_meta_box_nonce_action';

			// Check if a nonce is set.
			if ( ! isset( $nonce_name ) ) {
				return;
			}

			// Check if a nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
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
			$metabox_request   = ( ! empty( $_POST[ $this->metabox_id ] ) ) ? $_POST[ $this->metabox_id ] : array();
			// var_dump($metabox_request);
			// exit();
			if ( ! empty( $metabox_request ) && ! empty( $this->metabox_sections ) ) {
				foreach ( $this->metabox_sections as $section ) {

					if ( ! empty( $section['fields'] ) ) {
						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {
								$data = isset( $metabox_request[ $field['id'] ] ) ? $metabox_request[ $field['id'] ] : '';

								$fieldClass = 'TF_' . $field['type'];
								if ( class_exists( $fieldClass ) ) {
									$_field                            = new $fieldClass( $field, $data, $this->metabox_id );
									$tf_meta_box_value[ $field['id'] ] = $_field->sanitize();
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

	}
}


