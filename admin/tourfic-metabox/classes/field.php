<?php 
class Field {

  // public function __construct($field_array) {
  //   $this->field_array = $field_array;
  // }
  public static function rander($key, $sectionid, $sections) {

    foreach ($sections['fields'] as $value) {
      $type = sanitize_text_field ( $value['type'] );
      if( !empty($type) ){
        if ( file_exists( TF_ADMIN_PATH . 'tourfic-metabox/classes/fields/'.$type.'/'.$type.'.php' ) ) {
          require_once TF_ADMIN_PATH . 'tourfic-metabox/classes/fields/'.$type.'/'.$type.'.php';
        }
      }else{
        echo "Field Not founds";
      }

      
    }
    
  }
}

?>