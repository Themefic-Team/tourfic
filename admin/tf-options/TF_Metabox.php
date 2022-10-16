<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_Metabox {

	private static $instance = null;
	public static $metaboxes = array();
	public static $metabox_id = null;
	public static $metabox_title = null;
	public static $metabox_post_type = null;
	public static $metabox_sections = array();
	public static $metabox_fields = array();

	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct( $key, $params = array() ) {
		self::$metabox_id        = $key;
		self::$metabox_title     = $params['title'];
		self::$metabox_post_type = $params['post_type'];
		self::$metabox_sections  = $params['sections'];
		self::$metabox_fields    = $params['fields'];

		//load fields
		$this->load_fields();

		//metaboxes
		add_action( 'add_meta_boxes', array( $this, 'tf_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}


	// Include files
	public function load_fields() {

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
		add_meta_box(
			self::$metabox_id,
			self::$metabox_title,
			array( $this, 'tf_meta_box_content' ),
			self::$metabox_post_type,
			'normal',
			'high',

		);
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
		echo '<table class="form-table">';
		foreach ( self::$metabox_sections as $section ) {
			echo '<tr>';
			echo '<td><h2>' . $section['title'] . '</h2></td>';
			echo '<td>';
			echo '<table class="form-table">';
			foreach ( $section['fields'] as $field ) {
				$id    = self::$metabox_id . '[' . $field['id'] . ']';
				$value = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : '';

				echo '<tr>';
				echo '<th><label for="' . $id . '">' . $field['title'] . '</label></th>';
				echo '<td>';

				$fieldClass = 'TF_' . $field['type'];
				if ( class_exists( $fieldClass ) ) {
					$_field = new $fieldClass($field, $value);
					$_field->render();
				} else {
					echo '<p>' . __( 'Field not found!', 'tourfic' ) . '</p>';
				}
				echo '<p class="description">' . $field['description'] . '</p>';
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</td>';
			echo '</tr>';

		}
		echo '</table>';
	}

	public function save_metabox( $post_id, $post ) {
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
		foreach ( self::$metabox_sections as $section ) {
			if ( ! empty( $section['fields'] ) ) {
				foreach ( $section['fields'] as $field ) {
					$id = self::$metabox_id . '[' . $field['id'] . ']';
//					tf_var_dump( $_POST );
//				exit();
					$tf_meta_box_value[ $field['id'] ] = isset( $_POST[ $id ] ) ? $_POST[ $id ] : '';
				}
			}
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, self::$metabox_id, $tf_meta_box_value );
	}

}

new TF_Metabox( 'tf_hotels', array(
	'title'     => 'Hotel Settings',
	'post_type' => 'tf_hotel',
	'sections'  => array(
		'section_1' => array(
			'title'  => 'Section 1',
			'fields' => array(
				array(
					'id'    => 'address',
					'title' => 'Address',
					'type'  => 'text',
					'description' => 'Address of the hotel',
				),
				array(
					'id'    => 'phone',
					'title' => 'Phone',
					'type'  => 'textarea',
					'description' => 'Phone of the hotel',
				),
				array(
					'id'    => 'email',
					'title' => 'Email',
					'type'  => 'select',
					'options' => array(
						'1' => 'Option 1',
						'2' => 'Option 2',
						'3' => 'Option 3',
					),
				),
			),
		),
	),
) );