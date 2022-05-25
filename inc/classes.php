<?php
/**
 * Base Class File
 */

# Tour Price
if ( file_exists( TF_INC_PATH . 'classes/class-tour-price.php' ) ) {
    require_once TF_INC_PATH . 'classes/class-tour-price.php';
} else {
    tf_file_missing(TF_INC_PATH . 'classes/class-tour-price.php');
}
?>