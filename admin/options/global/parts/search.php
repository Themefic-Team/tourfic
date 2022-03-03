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
    array(
        'id'          => 'search_relation',
        'type'        => 'select',
        'title'       => __( 'Search Result Relation', 'tourfic' ),
        'desc'        => __( 'Search result relation with search widget and filters. OR means matched any query, AND means matched all query.', 'tourfic' ),
        'chosen'      => true,
        'placeholder' => __('Select an option', 'tourfic' ),
        'options'     => array(
        'AND' => 'AND',
        'OR'  => 'OR',
        ),
        'default' => 'AND'
    ),

    )
) );
?>