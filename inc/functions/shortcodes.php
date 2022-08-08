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
                $image_url = !empty($meta['image']['url']) ? $meta['image']['url'] : TF_ASSETS_URL . 'img/img-not-available.svg';
                $term_link = get_term_link( $term ); ?>

                <div class="single_recomended_item">
                    <a href="<?php echo $term_link; ?>">
                        <div class="single_recomended_content" style="background-image: url(<?php echo $image_url; ?>);">
                            <div class="recomended_place_info_header">
                                <h3><?php _e( $term->name ); ?></h3>
                                <p><?php printf( esc_html__( "%s hotels", 'tourfic' ), $term->count); ?></p>
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
                $image_url = !empty($meta['image']['url']) ? $meta['image']['url'] : TF_ASSETS_URL . 'img/img-not-available.svg';
                $term_link = get_term_link( $term );

                if ( is_wp_error( $term_link ) ) {
                    continue;
                } ?>

                <div class="single_recomended_item">
                    <a href="<?php echo $term_link; ?>">
                        <div class="single_recomended_content" style="background-image: url(<?php echo $image_url; ?>);">
                            <div class="recomended_place_info_header">
                                <h3><?php _e( $term->name ); ?></h3>
                                <p><?php printf( esc_html__( "%s tours", 'tourfic' ), $term->count); ?></p>
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
        <div class="tf-widget-slider recent-hotel-slider">
            <div class="tf-heading">
                <?php
                if (!empty($title)){
                    echo '<h2>' .esc_html($title). '</h2>';
                }
                if (!empty($subtitle)){
                    echo '<p>' .esc_html($subtitle). '</p>';
                }
                ?>
            </div>

            <div class="tf-slider-wrapper">
                <div id="<?php echo $thisid; ?>" class="tf-slider-inner">
                    <?php while ( $hotel_loop->have_posts() ) : $hotel_loop->the_post(); ?>
                        <div class="tf-single">
                            <a href="<?php the_permalink(); ?>">
                            <img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                              <div class="tf-single-meta">
                                  <p class="tf-title"><?php the_title(); ?></p>
                              </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

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
        adaptiveHeight: false,
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
    <?php endif;
    wp_reset_postdata(); ?>

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
        <div class="tf-widget-slider recent-tour-slider">
            <div class="tf-heading">
                <?php
                if (!empty($title)){
                    echo '<h2>' .esc_html($title). '</h2>';
                }
                if (!empty($subtitle)){
                    echo '<p>' .esc_html($subtitle). '</p>';
                }
                ?>
            </div>

            <div class="tf-slider-wrapper">
                <div id="<?php echo $thisid; ?>" class="tf-slider-inner">
                    <?php while ( $hotel_loop->have_posts() ) : $hotel_loop->the_post(); ?>
                        <div class="tf-single">
                            <a href="<?php the_permalink(); ?>">
                            <img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                              <div class="tf-single-meta">
                                  <p class="tf-title"><?php the_title(); ?></p>
                              </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

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
        adaptiveHeight: true,
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
    <?php endif;
    wp_reset_postdata(); ?>

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
 * Advance Search from Shortcode Function
 */
function tf_advance_search_form_shortcode(){
    
    ob_start();
?>
<div id="tf-booking-search-tabs">
    <div id="tf-tour-booking-form">
    <form class="tf_booking-widget default-form" id="tf_tour_aval_check" method="get" autocomplete="off" action="">

        <div class="tf_homepage-booking">
            <div class="tf_destination-wrap">
                <div class="tf_input-inner">
                <div class="tf_form-row">
                        <label class="tf_label-row">
                            <span class="tf-label"><?php _e('Destination', 'tourfic'); ?>:</span>
                            <div class="tf_form-inner tf-d-g">
                                <i class="fas fa-search"></i>
                                <input type="text" required id="tf-destination-adv" class="tf-advance-destination" placeholder="<?php _e('Enter Destination', 'tourfic'); ?>" value="">
                                <input type="hidden" name="place" class="tf-place-input" />                    
                                <div class="ui-widget ui-widget-content results tf-hotel-results">
                                </div>
                            </div>
                            
                        </label>
                    </div>
                </div>
            </div>
            

            <div class="tf_selectperson-wrap">
                <div class="tf_input-inner">
                    <span class="tf_person-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <div class="adults-text">1 <?php _e('Adults', 'tourfic'); ?></div>
                    <div class="person-sep"></div>
                    <div class="child-text">0 <?php _e('Children', 'tourfic'); ?></div>
                    <div class="person-sep"></div>
                    <div class="infant-text">0 <?php _e('Infant', 'tourfic'); ?></div>
                </div>
                <div class="tf_acrselection-wrap">
                    <div class="tf_acrselection-inner">
                        <div class="tf_acrselection">
                            <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                    <input type="number" name="adults" id="adults" min="1" value="1">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                        <div class="tf_acrselection">
                            <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                    <input type="number" name="children" id="children" min="0" value="0">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                        <div class="tf_acrselection">
                            <div class="acr-label"><?php _e('Infant', 'tourfic'); ?></div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                    <input type="number" name="infant" id="infant" min="0" value="0">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tf_selectdate-wrap">
            <!-- @KK Merged two inputs into one  -->
            <div class="tf_input-inner">
                <label class="tf_label-row">
                            <span class="tf-label"><?php _e('Check-in & Check-out date', 'tourfic'); ?></span>
                            <div class="tf_form-inner tf-d-g">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e('Select Date', 'tourfic'); ?>" <?php echo tfopt('date_tour_search')? 'required' : ''; ?>>
                            </div>
                        </label>
                </div>
            </div>
            
            <div class="tf_selectdate-wrap tf_more_info_selections">
            <div class="tf_input-inner">
                <label class="tf_label-row" style="width: 100%;">
                    <span class="tf-label"><?php _e('More', 'tourfic'); ?></span>
                    <span style="text-decoration: none; display: block; cursor: pointer;"><?php _e('More', 'tourfic'); ?>  <i class="fas fa-angle-down"></i></a>
                </label>
            </div>
            <div class="tf-more-info">
            <span><?php _e('Filter Price', 'tourfic'); ?></span>
            <div class="tf-filter-price-range">
                <div class="tf-filter-range"></div>
            </div>

            <span><?php _e('Hotel Features', 'tourfic'); ?></span>
                <?php
                $tf_hotelfeature = get_terms( array(
                    'taxonomy' => 'hotel_feature',
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'hide_empty' => true,
                    'hierarchical' => 0,
                ) );
                if ( $tf_hotelfeature ) { ?>
                <?php foreach( $tf_hotelfeature as $term ) { ?>
                    <div class="form-group form-check">
                        <input type="checkbox" name="features[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                        <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                    </div>
                <?php } } ?>
            </div>
            </div>

            <div class="tf_submit-wrap">
                <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
                <button class="tf_button tf-submit tf-tours-btn" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
            </div>

        </div>
        
        </form>
        <script>
        (function($) {
        $(document).ready(function() {

            $("#tf_tour_aval_check #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                onReady: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
                onChange: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
            });

        });
        })(jQuery);
        </script>
    </div>
</div>
<?php

return ob_get_clean();
}
add_shortcode('tf_advance_search', 'tf_advance_search_form_shortcode');

/**
 * Search Result Shortcode Function
 */
function tf_search_result_shortcode( $atts, $content = null ){

    // Unwanted Slashes Remove
    if ( isset( $_GET ) ) {
        $_GET = array_map( 'stripslashes_deep', $_GET );
    }
    
    // Get post type
    $post_type = isset( $_GET['type'] ) ? sanitize_text_field($_GET['type']) : '';
    if(empty($post_type)) {
        _e('<h3>Please select fields from the search form!</h3>', 'tourfic');
        return;
    }
    // Get hotel location or tour destination
    $taxonomy = $post_type == 'tf_hotel' ? 'hotel_location' : 'tour_destination';
    // Get place
    $place = isset( $_GET['place'] ) ? sanitize_text_field($_GET['place']) : '';
    // Get Adult
    $adults = isset( $_GET['adults'] ) ? sanitize_text_field($_GET['adults']) : '';
    // Get Child
    $child = isset( $_GET['children'] ) ? sanitize_text_field($_GET['children']) : '';
    // Get Room
    $room = isset( $_GET['room'] ) ? sanitize_text_field($_GET['room']) : '';
    // Get date
    $check_in_out = isset( $_GET['check-in-out-date'] ) ? sanitize_text_field($_GET['check-in-out-date']) : '';

    $data = array($adults, $child, $room, $check_in_out);

    $paged          = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
    $checkInOutDate = !empty( $_GET['check-in-out-date']) ? explode( ' - ', $_GET['check-in-out-date'] ) : '';
    if(!empty($checkInOutDate)) {
        $period         = new DatePeriod(
            new DateTime( $checkInOutDate[0] ),
            new DateInterval( 'P1D' ),
            new DateTime( $checkInOutDate[1] .  '23:59' )
        );
    } else {
        $period = '';
    }
    
    // Main Query args
    $args = array(
        'post_type'   => $post_type,
        'post_status' => 'publish',
        'paged'       => $paged,
    );

    $taxonomy_query = new WP_Term_Query(array(
        'taxonomy'   => $taxonomy,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => false,
        'slug'       => sanitize_title($place, ''),
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
                <a href="#list-view" data-id="list-view" class="change-view" title="<?php _e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
                <a href="#grid-view" data-id="grid-view" class="change-view" title="<?php _e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
            </div>
        </div>
        <div class="archive_ajax_result">
            <?php
                if ( $loop->have_posts() ) {
                    $not_found = [];
                    while ( $loop->have_posts() ) {
                        $loop->the_post();

                        if ( $post_type == 'tf_hotel' ) {

                            if( empty( $check_in_out ) ) {
                                $not_found[] = 0;
                                tf_hotel_archive_single_item();
                            } else {
                                tf_filter_hotel_by_date( $period, $not_found, $data );
                            }

                        } else {

                            if( empty( $check_in_out ) ) {
                                $not_found[] = 0;
                                tf_tour_archive_single_item();
                            } else {
                                tf_filter_tour_by_date( $period, $not_found, $data );
                            }                        

                        }

                    }

                    if ( !in_array( 0, $not_found ) ) {
                        echo '<div class="tf-nothing-found">' . __( 'Nothing Found! Select another dates', 'tourfic' ) . '</div>';
                    }
                } else {
                    echo '<div class="tf-nothing-found">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
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
