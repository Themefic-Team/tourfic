<?php

namespace Tourfic\App\Widgets\Bricks;

// don't load directly
defined( 'ABSPATH' ) || exit;

class Init {

    use \Tourfic\Traits\Singleton;

    public function __construct() {
        add_action( 'init', array( $this, 'tf_add_bricks_addon' ), 11 );
    }

    function tf_add_bricks_addon(){
        $element_files = [
            __DIR__ . '/Tour/Description.php',
        ];
    
        foreach ( $element_files as $file_class ) {
            \Bricks\Elements::register_element( $file_class );
        }
    }
    

}