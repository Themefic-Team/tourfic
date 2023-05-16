<?php 
//don't load directly
defined( 'ABSPATH' ) || exit;
//backup import export field
if ( ! class_exists( 'TF_Backup' ) ) {
    class TF_Backup extends TF_Fields {
        public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
            parent::__construct( $field, $value, $settings_id, $parent_field  );
        }
        public function render() {
            $nonce = wp_create_nonce( 'tf_backup_nonce' );
            $backup_url = admin_url( 'admin-ajax.php?action=tf_backup&nonce=' . $nonce );
            $import_url = admin_url( 'admin-ajax.php?action=tf_import&nonce=' . $nonce );

            $placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
            echo '<textarea name="tf_import_option" id="' . esc_attr( $this->field_name() ) . '"' . $placeholder . ' '. $this->field_attributes() .'>' . $this->value . '</textarea>';
            echo '<a href="' . $import_url . '" class="tf-import-btn button button-primary">' . __( 'Import', 'tourfic' ) . '</a>';
            echo '<hr>';
            echo '<textarea name="tf_export_option" id="' . esc_attr( $this->field_name() ) . '"' . $placeholder . ' '. $this->field_attributes() .'>' . json_encode( get_option('tf_settings'),JSON_FORCE_OBJECT ). '</textarea>';
            echo '<a href="' . $backup_url . '" class="tf-backup-btn button button-primary">' . __( 'Export', 'tourfic' ) . '</a>';

        }
    }
}