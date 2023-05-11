<?php
//don't allow direct access via url
defined( 'ABSPATH' ) || exit;

//export table data into csv
if( ! function_exists('tf_export_order_data') ){
    function tf_export_order_data(){
        if( isset( $_GET['export']) && $_GET['export'] == 'csv'){
            global $wpdb;
            $table_name = $wpdb->prefix . 'tf_order_data';
            $separator = ",";
            $genarate_date = date('Y-m-d');
            $filename = 'tourfic-order-data-'.$genarate_date.'.csv';
            $export_data = $wpdb->get_results("SELECT * FROM $table_name");
            foreach($export_data as $row){
                echo $row;

            }
            var_dump($export_data);
            $csv_fields = array();
            $csv_fields[] = 'ID';
            $csv_fields[] = 'post_id';
            $csv_fields[] = 'post_type';
            $csv_fields[] = 'author_id';
            $csv_fields[] = 'order_id';
            $csv_fields[] = 'order_date';
            $csv_fields[] = 'order_status';
            $csv_fields[] = 'order_total';
            $csv_fields[] = 'order_currency';
            $csv_fields[] = 'order_discount';




            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('cache-Control: private', false);
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\";");
            header("Content-Transfer-Encoding: binary");

        }



    }
}



