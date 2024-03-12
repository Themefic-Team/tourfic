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
            global $wpdb;
            $import_url       = admin_url( 'admin-ajax.php');
            $current_settings = $wpdb->get_results( $wpdb->prepare("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s", 'tf_settings'), ARRAY_A );

            if( !empty( $current_settings ) ){
                $current_settings = $current_settings[0]['option_value'];
            }else{
                $current_settings = '';
            }      

            $placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
            echo '<textarea class="tf-exp-imp-field" cols="50" rows="15" name="tf_import_option" id="' . esc_attr( $this->field_name() ) . '"' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'> </textarea>';
            echo '<a href="' . esc_url($import_url) . '" class="tf-import-btn button button-primary">' . esc_html__( 'Import', 'tourfic' ) . '</a>';
            echo '<hr>';
            echo '<textarea cols="50" rows="15" class="tf-exp-imp-field"  name="tf_export_option" id="' . esc_attr( $this->field_name() ) . '"' . wp_kses_post($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'disabled >' . esc_html($current_settings) . '</textarea>';
            echo '<a href="#" class="tf-export-btn button button-primary">' . esc_html__( 'Export', 'tourfic' ) . '</a>';

        }
    }
}