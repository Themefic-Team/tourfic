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
            $import_url       = admin_url( 'admin-ajax.php'); 
            $current_settings = get_option($this->settings_id); 
            $current_settings = isset($current_settings) && !empty($current_settings) ? json_encode($current_settings) : '';   
            //var_dump(get_option('tf_settings'));
            //$current_settings = get_option('tf_settings'); 
            //print_r($current_settings);         

            $placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
            echo '<textarea class="tf-exp-imp-field" cols="50" rows="15" name="tf_import_option" id="' . esc_attr( $this->field_name() ) . '"' . $placeholder . ' '. $this->field_attributes() .'> </textarea>';
            // echo '<a href="' . $import_url . '" class="tf-import-btn button button-primary">' . __( 'Import', 'tourfic' ) . '</a>';
            echo '<button type="submit" class="tf-import-btn tf-admin-btn tf-btn-secondary" data-option="'.esc_attr( $this->settings_id ).'" data-submit-type="tf_import_data">' . __( 'Import', 'ultimate-addons-cf7' ) . '</button>';
            echo '<hr>';
            echo '<textarea cols="50" rows="15" class="tf-exp-imp-field"  name="tf_export_option" id="' . esc_attr( $this->field_name() ) . '"' . $placeholder . ' '. $this->field_attributes() .'disabled >' . $current_settings . '</textarea>';
            echo '<a href="#" class="tf-export-btn tf-admin-btn tf-btn-secondary">' . __( 'Export', 'tourfic' ) . '</a>';

        }
    }
}