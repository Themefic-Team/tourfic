<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_editor' ) ) {
	class TF_editor extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
            $tf_editor_unique_id = str_replace( array("[","]"),"_",esc_attr( $this->field_name() ) );
            ob_start();
                wp_editor($this->value, $tf_editor_unique_id, array(
                'wpautop' => true,
                'media_buttons' => true,
                'textarea_rows' => 10,
                'textarea_name' => $this->field_name()
                )
            );
            $output = ob_get_clean();
            echo $output;
		}

        public function sanitize() {
			return wp_kses_post($this->value);
		}
	}
}