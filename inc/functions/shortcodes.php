<?php
/**
 * Hotel Locations Shortcode
 */
function hotel_locations_shortcode( $atts, $content = null ){

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
add_shortcode('hotel_locations', 'hotel_locations_shortcode');
// Old compatibility
add_shortcode('tourfic_destinations', 'hotel_locations_shortcode');


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
 * Recent Hotel Slider
 */
function tf_recent_hotel_shortcode( $atts, $content = null ){
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
        'post_type'      => 'tf_hotel',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
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
add_shortcode('tf_recent_hotel', 'tf_recent_hotel_shortcode');
// old
add_shortcode('tf_tours', 'tf_recent_hotel_shortcode');

/**
 * Recent Tour
 */
function tf_recent_tour_shortcode( $atts, $content = null ){
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
        'post_type'      => 'tf_tours',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
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
add_shortcode('tf_recent_tour', 'tf_recent_tour_shortcode');
// Old
add_shortcode('tf_tours_grid', 'tf_recent_tour_shortcode');

/**
 * Search form
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

        <?php
        if ($type == 'all') {
        ?>

        <div class="tf-booking-form-tab">
            <button class="tf-tablinks active" onclick="tfOpenForm(event, 'tf-hotel-booking-form')"><?php _e('Hotel', 'tourfic'); ?></button>
            <button class="tf-tablinks" onclick="tfOpenForm(event, 'tf-tour-booking-form')"><?php _e('Tour', 'tourfic'); ?></button>
        </div>

        <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent">             
            <?php tf_hotel_search_form_horizontal( $classes, $title, $subtitle ); ?>
        </div>

        <div id="tf-tour-booking-form" class="tf-tabcontent">
            <?php tf_tour_search_form_horizontal( $classes, $title, $subtitle ); ?>
        </div>

        <?php
        } else if ($type == 'hotel'){
        ?>

        <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent">             
            <?php tf_hotel_search_form_horizontal( $classes, $title, $subtitle ); ?>
        </div>

        <?php
        } else if ($type == 'tour'){
        ?>

        <div id="tf-tour-booking-form" style="display:block" class="tf-tabcontent">
            <?php tf_tour_search_form_horizontal( $classes, $title, $subtitle ); ?>
        </div>

        <?php
        }
        ?>

    </div>

    <?php tourfic_fullwidth_container_end( $fullwidth ); ?>

    <?php return ob_get_clean();
}
add_shortcode('tf_search_form', 'tf_search_form_shortcode');
// Old shortcode
add_shortcode('tf_search', 'tf_search_form_shortcode');

/**
 * Search Result Shortcode Function
 */
function tf_search_result_shortcode( $atts, $content = null ){

    // Unwanted Slashes Remove
    if ( isset( $_GET ) ) {
        $_GET = array_map( 'stripslashes_deep', $_GET );
    }
    
    // Get post type
    $post_type = isset( $_GET['type'] ) ? $_GET['type'] : '';
    // Get hotel location or tour destination
    $taxonomy = $post_type == 'tf_hotel' ? 'hotel_location' : 'tour_destination';
    // Get place
    $place = isset( $_GET['place'] ) ? $_GET['place'] : '';

    $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
    
    // Main Query args
    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'paged'          => $paged,
    );

    $taxonomy_query = new WP_Term_Query(array(
        'taxonomy'               => $taxonomy,
        'orderby'                => 'name',
        'order'                  => 'ASC',
        'hide_empty'             => false,
        'slug' => sanitize_title($place, ''),
    ));

    if ($taxonomy_query) {

        $place_ids = array();

        // Place IDs array
        foreach($taxonomy_query->get_terms() as $term){ 
            $place_ids[] = $term->term_id;
        }

        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => $taxonomy,
                'terms'    => $place_ids,
            )
        );

    } else {
        $args['s'] = $place;
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
            <?php
            if ( $loop->have_posts() ) {               
                while ( $loop->have_posts() ) {
                    $loop->the_post(); 

                    if( $post_type == 'tf_hotel' ){
                        tf_hotel_archive_single_item(); 
                    }elseif( $post_type == 'tf_tours' ){
                        //tour archive single gird/section added
                        tf_tour_archive_single_item();
                    }
                      
                }
            } else {
                echo '<div class="tf-nothing-found">Nothing Found!</div>';
            } 
            ?>
        </div>
        <div class="tf_posts_navigation">
            <?php tourfic_posts_navigation($loop); ?>
        </div>

    </div>
    <!-- End Content -->

    <?php wp_reset_postdata(); ?>
    <?php return ob_get_clean();
}
add_shortcode('tf_search_result', 'tf_search_result_shortcode');

?>
