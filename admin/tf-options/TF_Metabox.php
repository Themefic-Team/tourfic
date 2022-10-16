<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_Metabox {

	private static $instance = null;
	public static $metabox_id = null;
	public static $metabox_title = null;
	public static $metabox_post_type = null;
	public static $metabox_sections = array();

	public function __construct($key, $params = array()) {
		$defaults = array(
			'title'     => '',
			'post_type' => 'post',
			'sections'  => array(),
		);

		$params = wp_parse_args( $params, $defaults );

		self::$metabox_id        = $key;
		self::$metabox_title     = $params['title'];
		self::$metabox_post_type = $params['post_type'];
		self::$metabox_sections  = $params['sections'];

		//load fields
		$this->load_fields();

		//metaboxes
		add_action( 'add_meta_boxes', array( $this, 'tf_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	public static function metabox($key, $params = array()) {
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
		add_meta_box( self::$metabox_id, self::$metabox_title, array( $this, 'tf_meta_box_content' ), self::$metabox_post_type, 'normal', 'high', );
	}

	public function tf_meta_box_content( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'tf_meta_box_nonce_action', 'tf_meta_box_nonce' );

		// Retrieve an existing value from the database.
		$tf_meta_box_value = get_post_meta( $post->ID, self::$metabox_id, true );

		// Set default values.
		if ( empty( $tf_meta_box_value ) ) {
			$tf_meta_box_value = array();
		}

		// Form fields.
		?>
        <table class="form-table">
			<?php foreach ( self::$metabox_sections as $section ) : ?>
                <tr>
                    <td><h2><?php echo $section['title']; ?></h2></td>
                    <td>
                        <table class="form-table">
							<?php foreach ( $section['fields'] as $field ) :
								$id = self::$metabox_id . '[' . $field['id'] . ']';
								$value = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : '';
								?>
                                <tr>
                                    <th>
                                        <label for="<?php echo esc_attr( $id ) ?>"><?php echo esc_html( $field['title'] ) ?></label>
                                    </th>
                                    <td>
										<?php
										$fieldClass = 'TF_' . $field['type'];
										if ( class_exists( $fieldClass ) ) {
											$_field = new $fieldClass( $field, $value, self::$metabox_id );
											$_field->render();
										} else {
											echo '<p>' . __( 'Field not found!', 'tourfic' ) . '</p>';
										}
										?>
                                        <p class="description"><?php echo wp_kses_post( $field['description'] ) ?></p>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                        </table>
                    </td>
                </tr>

			<?php endforeach; ?>
        </table>
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
		$metabox_request   = ( ! empty( $_POST[ self::$metabox_id ] ) ) ? $_POST[ self::$metabox_id ] : array();
		foreach ( self::$metabox_sections as $section ) {
			if ( ! empty( $section['fields'] ) ) {
				foreach ( $section['fields'] as $field ) {
					$tf_meta_box_value[ $field['id'] ] = isset( $metabox_request[ $field['id'] ] ) ? $metabox_request[ $field['id'] ] : '';
				}
			}
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, self::$metabox_id, $tf_meta_box_value );
	}

}


