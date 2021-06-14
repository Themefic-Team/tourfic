<?php
/**
 * Destination Shortcode Function
 */
function tourfic_destinations_shortcode( $atts, $content = null ){

    // Shortcode extract
    extract(
      shortcode_atts(
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
          ),
        $atts
      )
    );

    // Propertise args
    $args = array(
        'post_type' => 'tourfic',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    // 1st search on Destination taxonomy
    $destinations = get_terms( array(
        'taxonomy' => 'destination',
        'orderby' => $orderby,
        'order' => $order,
        'hide_empty' => $hide_empty, //can be 1, '1' too
        'hierarchical' => 0, //can be 1, '1' too
        'search' => '',
        'number' => 6,
        //'name__like' => '',
    ) );

    ob_start();

    if ( $destinations ) : ?>
    <!-- Recommended destinations  -->
    <section id="recomended_section_wrapper">
        <div class="recomended_inner">
        <?php foreach( $destinations as $term ) :
            $image_id = get_term_meta( $term->term_id, 'category-image-id', true );
            $term_link = get_term_link( $term );

            if ( is_wp_error( $term_link ) ) {
                continue;
            }
            ?>

            <div class="single_recomended_item">
                <a href="<?php echo tourfic_booking_search_action(); ?>?destination=<?php _e( $term->slug ); ?>">
                  <div class="single_recomended_content" style="background-image: url(<?php echo wp_get_attachment_url( $image_id ); ?>);">
                    <div class="recomended_place_info_header">
                      <h3><?php _e($term->name); ?></h3>
                      <p><?php printf( esc_html__( "%s properties", 'tourfic' ), $term->count); ?></p>
                    </div>
                    <?php if( $term->description ): ?>
                        <div class="recomended_place_info_footer">
                            <p><?php echo nl2br($term->description); ?></p>
                        </div>
                    <?php endif; ?>
                  </div>
                </a>
            </div>

        <?php endforeach; ?>
        </div>
     </section>
    <!-- Recommended destinations  End-->
    <?php endif; ?>
    <?php return ob_get_clean();
}
add_shortcode('tourfic_destinations', 'tourfic_destinations_shortcode');

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
              'slidesToShow'  => 5,
            ),
          $atts
        )
    );

    $args = array(
        'post_type' => 'tourfic',
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
        jQuery('#<?php echo $thisid; ?>').slick({
        dots: false,
        infinite: true,
        slidesToShow: <?php echo $slidesToShow; ?>,
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
    </script>
    <?php endif; wp_reset_postdata(); ?>

    <?php return ob_get_clean();
}

add_shortcode('tf_tours', 'tourfic_tours_shortcode');


/**
 * Search Shortcode Function
 */
function tourfic_search_shortcode( $atts, $content = null ){
    extract(
      shortcode_atts(
        array(
            'style'  => 'default', //recomended, populer
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

    ob_start(); ?>


    <?php tourfic_fullwidth_container_start( $fullwidth ); ?>

    <!-- Start Booking widget -->
    <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" method="get" autocomplete="off" action="<?php echo tourfic_booking_search_action(); ?>">

        <?php if( $title ): ?>
            <div class="tf_widget-title"><?php esc_html_e( $title ); ?></div>
        <?php endif; ?>

        <?php if( $subtitle ): ?>
            <div class="tf_widget-subtitle"><?php esc_html_e( $subtitle ); ?></div>
        <?php endif; ?>


        <div class="tf_homepage-booking">

            <div class="tf_destination-wrap">
                <div class="tf_input-inner">
                    <!-- Start form row -->
                    <?php tourfic_booking_widget_field(
                        array(
                            'type' => 'text',
                            'svg_icon' => 'search',
                            'name' => 'destination',
                            'label' => 'Destination/property name:',
                            'placeholder' => 'Destination',
                            'required' => 'true',
                        )
                    ); ?>
                    <!-- End form row -->
                </div>
            </div>

            <div class="tf_selectdate-wrap">

                <div class="tf_input-inner">
                    <span class="tf_date-icon">
                        <?php echo tourfic_get_svg('calendar_today'); ?>
                    </span>
                    <div class="checkin-date-text">Check-in</div>
                    <div class="date-sep"></div>
                    <div class="checkout-date-text">Check-out</div>
                </div>

                <div class="tf_date-wrap-srt screen-reader-text">
                <!-- Start form row -->
                <?php tourfic_booking_widget_field(
                    array(
                        'type' => 'text',
                        'svg_icon' => '',
                        'name' => 'check-in-date',
                        'placeholder' => 'Check-in date',
                        'label' => 'Check-in date',
                        'required' => 'true',
                        'disabled' => 'true',
                    )
                ); ?>

                <?php tourfic_booking_widget_field(
                    array(
                        'type' => 'text',
                        'svg_icon' => '',
                        'name' => 'check-out-date',
                        'placeholder' => 'Check-out date',
                        'required' => 'true',
                        'disabled' => 'true',
                        'label' => 'Check-out date',
                    )
                ); ?>
                </div>

            </div>

            <div class="tf_selectperson-wrap">

                <div class="tf_input-inner">
                    <span class="tf_person-icon">
                        <?php echo tourfic_get_svg('person'); ?>
                    </span>
                    <div class="adults-text">2 Adults</div>
                    <div class="person-sep"></div>
                    <div class="child-text">0 Childreen</div>
                    <div class="person-sep"></div>
                    <div class="room-text">1 Room</div>
                </div>

                <div class="tf_acrselection-wrap">
                    <div class="tf_acrselection-inner">

                        <div class="tf_acrselection">
                            <div class="acr-label">Adults</div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="adults" id="adults" min="1" value="2">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                        <div class="tf_acrselection">
                            <div class="acr-label">Children</div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="children" id="children" min="0" value="0">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                        <div class="tf_acrselection">
                            <div class="acr-label">Rooms</div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="room" id="room" min="1" value="1">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="tf_submit-wrap">
                <button class="tf_button tf-submit" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
            </div>

        </div>

    </form>
    <!-- End Booking widget -->

    <?php tourfic_fullwidth_container_end( $fullwidth ); ?>

    <?php return ob_get_clean();
}
add_shortcode('tf_search', 'tourfic_search_shortcode');

/**
 * Search Result Shortcode Function
 */
function tourfic_search_result_shortcode( $atts, $content = null ){
    global $tourfic_opt;
    $relation = isset( $tourfic_opt['search_relation'] ) ? esc_attr( $tourfic_opt['search_relation'] ) : "AND";

    // Unwanted Slashes Remove
    if ( isset( $_GET ) ) {
        $_GET = array_map( 'stripslashes_deep', $_GET );
    }

    // Shortcode extract
    extract(
      shortcode_atts(
        array(
            'style'  => 'default',
            'max'  => '50',
            'search' => isset( $_GET['destination'] ) ? $_GET['destination'] : '',
          ),
        $atts
      )
    );

    // Propertise args
    $args = array(
        'post_type' => 'tourfic',
        'post_status' => 'publish',
        'posts_per_page' => $max,
    );

    // 1st search on Destination taxonomy
    $destinations = get_terms( array(
        'taxonomy' => 'destination',
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
                'taxonomy' => 'destination',
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
                <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                    <?php tourfic_archive_single(); ?>
                <?php endwhile; ?>
            <?php else : ?>
                <?php get_template_part( 'template-parts/content', 'none' ); ?>
            <?php endif; ?>
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
    global $tourfic_opt;
    $relation = isset( $tourfic_opt['search_relation'] ) ? esc_attr( $tourfic_opt['search_relation'] ) : "AND";
    $filter_relation = isset( $tourfic_opt['filter_relation'] ) ? esc_attr( $tourfic_opt['filter_relation'] ) : "OR";

    $search = ( $_POST['dest'] ) ? sanitize_text_field( $_POST['dest'] ) : null;
    $filters = ( $_POST['filters'] ) ? explode(',', sanitize_text_field( $_POST['filters'] )) : null;

    // Propertise args
    $args = array(
        'post_type' => 'tourfic',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );


    if ( $search ) {

        // 1st search on Destination taxonomy
        $destinations = get_terms( array(
            'taxonomy' => 'destination',
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

            $args['tax_query']['relation'] = $relation;
            $args['tax_query'][] = array(
                'taxonomy' => 'destination',
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

    $loop = new WP_Query( $args ); ?>
    <?php if ( $loop->have_posts() ) : ?>
        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
            <?php tourfic_archive_single(); ?>
        <?php endwhile; ?>
    <?php else : ?>
        <?php get_template_part( 'template-parts/content', 'none' ); ?>
    <?php endif; ?>
    <?php wp_reset_postdata();

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