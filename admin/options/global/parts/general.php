<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'id'     => 'general',
    'title'  => __( 'General', 'tourfic' ),
    'icon'   => 'fas fa-cogs',
    'fields' => array(

      array(
        'id'       => 'disable-services',
        'type'     => 'checkbox',
        'title'    => __('Disable Services', 'tourfic' ),
        'subtitle' => __('Disable or hide the services you don\'t need by ticking the checkbox', 'tourfic' ),
        'options'  => array(
            'hotel' => __('Hotel', 'tourfic' ),
            'tour'  => __('Tour', 'tourfic' ),
        ),
        ),

    )
    
) );
?>