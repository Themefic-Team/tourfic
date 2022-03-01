<?php
/**
 * Hotel Locations Shortcode
 */
function shortcode_hotel_locations( $atts, $content = null ){

    // Shortcode extract
    extract(
      shortcode_atts(
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'ids' => '',
          ),
        $atts
      )
    );

    // 1st search on hotel_location taxonomy
    $locations = get_terms( array(
        'taxonomy' => 'hotel_location',
        'orderby' => $orderby,
        'order' => $order,
        'hide_empty' => $hide_empty, //can be 1, '1' too
        'hierarchical' => 0, //can be 1, '1' too
        'search' => '',
        'number' => 6,
        'include' => $ids,
        //'name__like' => '',
    ) );

    ob_start();

    if ( $locations ) { ?>
        <section id="recomended_section_wrapper">
            <div class="recomended_inner">

            <?php foreach( $locations as $term ) {

                $meta = get_term_meta( $term->term_id, 'hotel_location', true );
                $image_url = $meta['image']['url'];
                $term_link = get_term_link( $term ); ?>

                <div class="single_recomended_item">
                    <a href="<?php echo $term_link; ?>">
                        <div class="single_recomended_content" style="background-image: url(<?php echo $image_url; ?>);">
                            <div class="recomended_place_info_header">
                                <h3><?php _e( $term->name ); ?></h3>
                                <p><?php printf( esc_html__( "%s properties", 'tourfic' ), $term->count); ?></p>
                            </div>
                        </div>
                    </a>
                </div>

            <?php } ?>

            </div>
        </section>

    <?php }
    return ob_get_clean();
}
add_shortcode('hotel_locations', 'shortcode_hotel_locations');


/**
 * Tour destinations shortcode
 */
function shortcode_tour_destinations( $atts, $content = null ){

    // Shortcode extract
    extract(
      shortcode_atts(
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'ids' => '',
          ),
        $atts
      )
    );

    // 1st search on Destination taxonomy
    $destinations = get_terms( array(
        'taxonomy' => 'tour_destination',
        'orderby' => $orderby,
        'order' => $order,
        'hide_empty' => $hide_empty, //can be 1, '1' too
        'hierarchical' => 0, //can be 1, '1' too
        'search' => '',
        'number' => 6,
        'include' => $ids,
        //'name__like' => '',
    ) );

    shuffle($destinations);
    ob_start();

    if ( $destinations ) { ?>
        <section id="recomended_section_wrapper">
            <div class="recomended_inner">

            <?php foreach( $destinations as $term ) {

                $meta = get_term_meta( $term->term_id, 'tour_destination', true );
                $image_url = !empty($meta['image']['url']) ? $meta['image']['url'] : '';
                $term_link = get_term_link( $term );

                if ( is_wp_error( $term_link ) ) {
                    continue;
                } ?>

                <div class="single_recomended_item">
                    <a href="<?php echo $term_link; ?>">
                        <div class="single_recomended_content" style="background-image: url(<?php echo $image_url; ?>);">
                            <div class="recomended_place_info_header">
                                <h3><?php _e( $term->name ); ?></h3>
                                <p><?php printf( esc_html__( "%s properties", 'tourfic' ), $term->count); ?></p>
                            </div>
                        </div>
                    </a>
                </div>

            <?php } ?>

            </div>
        </section>
    <?php }
    return ob_get_clean();
}
add_shortcode('tour_destinations', 'shortcode_tour_destinations');

/**
 * Tours Shortcode
 */
function tourfic_tours_shortcode( $atts, $content = null ){
    extract(
        shortcode_atts(
          array(
              'title'  => '',  //title populer section
              'subtitle'  => '',   // Sub title populer section
              'count'  => 10,
              'slidestoshow'  => 5,
            ),
          $atts
        )
    );

    $args = array(
        'post_type' => 'tf_hotel',
        'post_status' => 'publish',
        'posts_per_page' => $count,
    );

    ob_start();

    $hotel_loop = new WP_Query( $args );

    // Generate an Unique ID
    $thisid = uniqid('tfpopular_');

    ?>
    <?php if ( $hotel_loop->have_posts() ) : ?>
    <!-- Populer Destinaiton -->
    <section id="populer_section_wrapper">
        <div class="populer_inner">
            <div class="populer_section_heading">
                <?php if (!empty($title)){ ?>
                  <h3><?php echo esc_html($title) ?></h3>
                <?php }?>
                <?php if (!empty($subtitle)){ ?>
                  <p><?php echo esc_html($subtitle) ?></p>
                <?php }?>
            </div>

            <div class="popupler_widget_wrapper">
                <div id="<?php echo $thisid; ?>" class="populer_widget_inner">
                    <?php while ( $hotel_loop->have_posts() ) : $hotel_loop->the_post(); ?>
                        <div class="single_populer_item">
                            <a href="<?php the_permalink(); ?>">
                              <div class="populer_item_img" style="background-image: url(<?php the_post_thumbnail_url(); ?>);">
                              </div>
                              <div class="tourfic_location_widget_meta">
                                  <p class="tourfic_widget_location_title"><?php the_title(); ?></p>
                              </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        jQuery(document).ready(function() {
            jQuery('#<?php echo $thisid; ?>').not('.slick-initialized').slick({
        dots: false,
        infinite: true,
        slidesToShow: <?php echo $slidestoshow; ?>,
        slidesToScroll: 1,
        autoplay:true,
        //autoplaySpeed:2500,
        arrows:false,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]

      });
        });
        
    </script>
    <?php endif; wp_reset_postdata(); ?>

    <?php return ob_get_clean();
}

add_shortcode('tf_tours', 'tourfic_tours_shortcode');

/**
 * Tours grid Shortcode
 */
function tf_tours_grid_shortcode( $atts, $content = null ){
    extract(
        shortcode_atts(
          array(
              'title'  => '',  //title populer section
              'subtitle'  => '',   // Sub title populer section
              'count'  => 10,
              'slidestoshow'  => 5,
            ),
          $atts
        )
    );

    $args = array(
        'post_type' => 'tf_tours',
        'post_status' => 'publish',
        'posts_per_page' => $count,
    );

    ob_start();

    $hotel_loop = new WP_Query( $args );

    // Generate an Unique ID
    $thisid = uniqid('tfpopular_');

    ?>
    <?php if ( $hotel_loop->have_posts() ) : ?>
    <!-- Populer Destinaiton -->
    <section id="populer_section_wrapper">
        <div class="populer_inner">
            <div class="populer_section_heading">
                <?php if (!empty($title)){ ?>
                  <h3><?php echo esc_html($title) ?></h3>
                <?php }?>
                <?php if (!empty($subtitle)){ ?>
                  <p><?php echo esc_html($subtitle) ?></p>
                <?php }?>
            </div>

            <div class="popupler_widget_wrapper">
                <div id="<?php echo $thisid; ?>" class="populer_widget_inner">
                    <?php while ( $hotel_loop->have_posts() ) : $hotel_loop->the_post(); ?>
                        <div class="single_populer_item">
                            <a href="<?php the_permalink(); ?>">
                              <div class="populer_item_img" style="background-image: url(<?php the_post_thumbnail_url(); ?>);">
                              </div>
                              <div class="tourfic_location_widget_meta">
                                  <p class="tourfic_widget_location_title"><?php the_title(); ?></p>
                              </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        jQuery(document).ready(function() {
            jQuery('#<?php echo $thisid; ?>').not('.slick-initialized').slick({
        dots: false,
        infinite: true,
        slidesToShow: <?php echo $slidestoshow; ?>,
        slidesToScroll: 1,
        autoplay:true,
        //autoplaySpeed:2500,
        arrows:false,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]

      });
        });
       
    </script>
    <?php endif; wp_reset_postdata(); ?>

    <?php return ob_get_clean();
}

add_shortcode('tf_tours_grid', 'tf_tours_grid_shortcode');

/**
 * Search Shortcode Function
 */
function tf_search_form_shortcode( $atts, $content = null ){
    extract(
      shortcode_atts(
        array(
            'style'  => 'default', //recomended, populer
            'type'   => 'all',
            'title'  => '',  //title populer section
            'subtitle'  => '',   // Sub title populer section
            'classes'  => '',
            'fullwidth'  => '',
          ),
        $atts
      )
    );

    if ( $style == 'default' ) {
        $classes = " default-form ";
    }

    ob_start();
    ?>


    <?php tourfic_fullwidth_container_start( $fullwidth ); ?>
    <div id="tf-booking-search-tabs">
        <!-- Start Booking widget -->
        <div class="tf-booking-form-tab">
            <?php if( $type == 'hotel' || $type == 'all') { ?>
                <button class="tf-tablinks active" onclick="tfOpenForm(event, 'tf-hotel-booking-form')">Hotel</button>
            <?php } ?>
            <?php if( $type == 'tour' || $type == 'all') { ?>
                <button class="tf-tablinks" onclick="tfOpenForm(event, 'tf-tour-booking-form')">Tours</button>
            <?php } ?>
        </div>
        <?php if( $type == 'hotel' || $type == 'all') { ?>           
            <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent">
                <!--Added hotel search widget--> 
                <?php tf_hotel_search_form( $classes, $title, $subtitle ); ?>
            </div>
        <?php } ?>
        <?php if( $type == 'tour' || $type == 'all') { ?>
        <div id="tf-tour-booking-form" class="tf-tabcontent">
             <!--Added tours search widget--> 
            <?php tourfic_search_widget_tour( $classes, $title, $subtitle ); ?>
        </div>
        <?php } ?>
    </div>
    <!-- End Booking widget -->

    <?php tourfic_fullwidth_container_end( $fullwidth ); ?>

    <?php return ob_get_clean();
}
add_shortcode('tf_search_form', 'tf_search_form_shortcode');

/**
 * Search Result Shortcode Function
 */
function tourfic_search_result_shortcode( $atts, $content = null ){
    
    $relation = tfopt( 'search_relation', 'AND' );

    // Unwanted Slashes Remove
    if ( isset( $_GET ) ) {
        $_GET = array_map( 'stripslashes_deep', $_GET );
    }
    //Show both Hotel and Tourfic posts in the search result
    $post_type = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();

    if($post_type == 'page' && get_query_var( 'hotel_location' ) == ''){
        $post_type = 'tf_hotel';
    }else if( $post_type == 'page' && get_query_var( 'tour_destination' ) == '' ){
        $post_type = 'tf_tours';
    }

    $taxonomy = $post_type == 'tf_tours' ? 'tour_destination' : 'hotel_location';
    if( isset($_GET['tour_destination']) ){
        $dest = $_GET['tour_destination'];
    }else{
        $dest = get_query_var('tour_destination');
    }

    // Shortcode extract
    extract(
      shortcode_atts(
        array(
            'style'  => 'default',
            'max'  => '50',
            'search' => isset( $_GET['location'] ) ? $_GET['location'] : $dest,
          ),
        $atts
      )
    );
    
    if( $search == '' ){
        //if( isset(get_query_var( 'destination' ))){
            $search = get_query_var( 'location' );
        //}else {
            //$search = '';
        //}
    }
    // Propertise args
    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => $max,
    );
    // 1st search on Destination taxonomy
    $destinations = get_terms( array(
        'taxonomy' => $taxonomy,
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => 0, //can be 1, '1' too
        'hierarchical' => 0, //can be 1, '1' too
        'search' => $search,
        //'name__like' => '',
    ) );

    if ( $destinations ) {
        // Define Featured Category IDs first
        $destinations_ids = array();
       
        // Creating loop to insert IDs to array.
        foreach( $destinations as $cat ) {
            $destinations_ids[] = $cat->term_id;
        }
        $args['tax_query'] = array(
            'relation' => $relation,
            array(
                'taxonomy' => $taxonomy,
                'terms'    => $destinations_ids,
            )
        );
    } else {
        $args['s'] = $search;
    }
    $loop = new WP_Query( $args );

    ob_start(); ?>

    <!-- Start Content -->
    <div class="tf_search_result">
        <div class="tf-action-top">
            <div class="tf-list-grid">
                <a href="#list-view" data-id="list-view" class="change-view" title="List View"><?php echo tourfic_get_svg('list_view'); ?></a>
                <a href="#grid-view" data-id="grid-view" class="change-view" title="Grid View"><?php echo tourfic_get_svg('grid_view'); ?></a>
            </div>
        </div>
        <div class="archive_ajax_result">
            <?php if ( $loop->have_posts() ) : ?>
                <?php
                    while ( $loop->have_posts() ) : $loop->the_post(); 

                        if( $post_type == 'tf_hotel' ){
                            tourfic_archive_single(); 
                        }elseif( $post_type == 'tf_tours' ){
                            //tour archive single gird/section added
                            tf_tours_archive_single();
                        }
                        
                    endwhile;
                    else : 
                        get_template_part( 'template-parts/content', 'none' ); 
                    endif; 
                ?>
        </div>
        <div class="tf_posts_navigation">
            <?php tourfic_posts_navigation(); ?>
        </div>

    </div>
    <!-- End Content -->

    <?php wp_reset_postdata(); ?>
    <?php return ob_get_clean();
}
add_shortcode('tf_search_result', 'tourfic_search_result_shortcode');


/**
 * Filter Ajax
 */
function tourfic_trigger_filter_ajax(){

    $relation = tfopt( 'search_relation', 'AND' );
    $filter_relation = tfopt( 'filter_relation', 'OR' );

    $search = ( $_POST['dest'] ) ? sanitize_text_field( $_POST['dest'] ) : null;
    $filters = ( $_POST['filters'] ) ? explode(',', sanitize_text_field( $_POST['filters'] )) : null;
    $features = ( $_POST['features'] ) ? explode(',', sanitize_text_field( $_POST['features'] )) : null;
    $posttype = $_POST['type']  ? sanitize_text_field( $_POST['type'] ): 'tf_hotel';
    $taxonomy = $posttype == 'tf_tours' ? $taxonomy = 'tour_destination' : 'hotel_location';

    // Propertise args
    $args = array(
        'post_type' => $posttype,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );


    if ( $search ) {

        // 1st search on Destination taxonomy
        $destinations = new WP_Term_Query( array(
            'taxonomy' => $taxonomy,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0, //can be 1, '1' too
            'hierarchical' => 0, //can be 1, '1' too
            // 'search' => "$search",
            'name' => "$search",
        ) );

        if ( $destinations ) {
            // Define Featured Category IDs first
            $destinations_ids = array();

            // Creating loop to insert IDs to array.
            foreach( $destinations->get_terms() as $cat ) {
                $destinations_ids[] = $cat->term_id;
            }

            $args['tax_query']['relation'] = $relation;
            $args['tax_query'][] = array(
                'taxonomy' => $taxonomy,
                'terms'    => $destinations_ids,
            );

        } else {
            $args['s'] = $search;
        }
    }

    if ( $filters ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'tf_filters',
                'terms'    => $filters,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($filters as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'tf_filters',
                    'terms'    => array($term_id),
                );
            }

        }

    }
    
    //Query for the features filter of tours
    if ( $features ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'tf_feature',
                'terms'    => $features,
            );
        } else {
            $args['tax_query']['tf_feature']['relation'] = 'AND';

            foreach ($filters as $key => $term_id) {
                $args['tax_query']['tf_feature'][] = array(
                    'taxonomy' => 'tf_feature',
                    'terms'    => array($term_id),
                );
            }

        }

    }
    
    $loop = new WP_Query( $args ); ?>
    <?php if ( $loop->have_posts() ) : 
        while ( $loop->have_posts() ) : $loop->the_post(); 
            if( $posttype == 'tf_tours' ){
                //include the tours search result and archive layout
                tf_tours_archive_single();
            }else{
                tourfic_archive_single();
            }  
        endwhile; 
     else : 
        get_template_part( 'template-parts/content', 'none' );
     endif; 
    wp_reset_postdata();

    die();
}
add_action( 'wp_ajax_nopriv_tf_trigger_filter', 'tourfic_trigger_filter_ajax' );
add_action( 'wp_ajax_tf_trigger_filter', 'tourfic_trigger_filter_ajax' );

// TF Icon List Shortcode
add_shortcode('tf_list','tourfic_icon_list_shortcode');
function tourfic_icon_list_shortcode( $atts, $content = null ) {
    // Params extraction
    extract(
        shortcode_atts(
            array(
                'icon'   => '',
                'text'   => '',
            ),
            $atts
        )
    );
    ob_start();?>
    <li><i class="fa  <?php esc_attr_e($icon); ?> "></i> <?php _e($text); ?></li>
    <?php return ob_get_clean();
}
