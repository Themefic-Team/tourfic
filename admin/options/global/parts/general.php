<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'id'     => 'general',
    'title'  => __( 'General', TFD ),
    'icon'   => 'fas fa-cogs',
    'fields' => array(

      array(
        'id'       => 'disable-services',
        'type'     => 'checkbox',
        'title'    => __('Disable Services', TFD ),
        'subtitle' => __('Disable or hide the services you don\'t need by ticking the checkbox', TFD ),
        'options'  => array(
            'hotel' => __('Hotel', TFD ),
            'tour'  => __('Tour', TFD ),
        ),
        ),

    )
    
) );
