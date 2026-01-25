<?php 
// Don't load directly

use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

if ( isset( $meta['room-options'] ) && ! empty( Helper::tf_data_types( $meta['room-options'] ) ) ):
    ?>
    <div class="tf-room-options">
        
    </div>
<?php endif; ?>