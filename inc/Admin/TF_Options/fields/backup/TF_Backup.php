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
            $current_settings = isset($current_settings) && !empty($current_settings) ? wp_json_encode($current_settings) : '';
	        //get data size in KB or MB
			$data_size = strlen($current_settings) > 1024 ? round(strlen($current_settings) / 1024, 2) . ' KB' : strlen($current_settings) . ' Bytes';
	        $data_size = strlen($current_settings) > 1048576 ? round(strlen($current_settings) / 1048576, 2) . ' MB' : $data_size;

	        //if $current_settings data length is more than 500 then trim it
	        $current_settings = strlen($current_settings) > 1000 ? substr($current_settings, 0, 1000) . '...' : $current_settings;

            $placeholder = ( ! empty( $this->field['placeholder'] ) ) ? 'placeholder="' . $this->field['placeholder'] . '"' : '';
            echo '<textarea class="tf-exp-imp-field" cols="50" rows="10" name="tf_import_option" id="' . esc_attr( $this->field_name() ) . '"' . esc_attr($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'> </textarea>';
            // echo '<a href="' . $import_url . '" class="tf-import-btn button button-primary">' . __( 'Import', 'tourfic' ) . '</a>';
            echo '<button type="submit" class="tf-import-btn tf-admin-btn tf-btn-secondary" data-option="'.esc_attr( $this->settings_id ).'" data-submit-type="tf_import_data">' . esc_html__( 'Import', 'ultimate-addons-cf7' ) . '</button>';
            echo '<hr>';
            echo '<textarea cols="50" rows="10" class="tf-exp-imp-field"  name="tf_export_option" id="' . esc_attr( $this->field_name() ) . '"' . esc_attr($placeholder) . ' '. wp_kses_post($this->field_attributes()) .'disabled >' . wp_kses_post($current_settings) . '</textarea>';
            echo '<a href="#" class="tf-export-btn tf-admin-btn tf-btn-secondary">' . esc_html__( 'Export', 'tourfic' ) . '</a> </br>';
			//warning message about full data export
	        echo '<div class="tf-field-notice-inner tf-notice-info" style="display: inline-block">'.esc_html__('Note: Exporting full data may take time depending on the data size. Please be patient and do not close the browser until the process is complete. Your data size is: ', 'tourfic').'<strong>'.$data_size.'</strong></div>';
        }
    }
}