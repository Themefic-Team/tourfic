<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'id'     => 'search',
    'title'  => __( 'Search', 'tourfic' ),
    'icon'   => 'fas fa-search',
    'fields' => array(

        array(
            'id'          => 'search-result-page',
            'type'        => 'select',
            'placeholder' => __('Select a page', 'tourfic' ),
            'chosen'      => true,
            'ajax'        => true,
            'title'       => __( 'Select Search Result Page', 'tourfic' ),
            'desc'        => __( 'Page template: <code>Tourfic - Search Result</code> must be selected', 'tourfic' ),
            'options'     => 'pages',
            
        ),

    )
) );
?>