<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'id'     => 'search',
    'title'  => __( 'Search', TFD ),
    'icon'   => 'fas fa-search',
    'fields' => array(

    array(
        'id'          => 'search-result-page',
        'type'        => 'select',
        'placeholder' => __('Select a page', TFD ),
        'chosen'      => true,
        'ajax'        => true,
        'title'       => __( 'Select Search Result Page', TFD ),
        'desc'        => __( 'Page template: <code>Tourfic - Search Result</code> must be selected', TFD ),
        'options'     => 'pages',
        
    ),
    array(
        'id'          => 'search_relation',
        'type'        => 'select',
        'title'       => __( 'Search Result Relation', TFD ),
        'desc'        => __( 'Search result relation with search widget and filters. OR means matched any query, AND means matched all query.', TFD ),
        'chosen'      => true,
        'placeholder' => __('Select an option', TFD ),
        'options'     => array(
        'AND' => 'AND',
        'OR'  => 'OR',
        ),
        'default' => 'AND'
    ),

    )
) );
