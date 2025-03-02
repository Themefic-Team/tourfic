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

            <div class="tf-slider-items-wrapper">
                <?php while ( $hotel_loop->have_posts() ) {
                    $hotel_loop->the_post();
                                $post_id                = get_the_ID();
                                $related_comments_hotel       = get_comments( array( 'post_id' => $post_id ) );
                    ?>
                    <div class="tf-slider-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
                                <?php if ($related_comments_hotel) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating($related_comments_hotel); ?></span>
                                    </div>											
								<?php }?>
                                <p><?php echo wp_trim_words(get_the_content(), 10); ?></p>
                            </div>
                        </div>
                    </div>
                <?php }	?>
            </div>
        </div>
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

    $tour_loop = new WP_Query( $args );

    // Generate an Unique ID
    $thisid = uniqid('tfpopular_');

    ?>
    <?php if ( $tour_loop->have_posts() ) : ?>
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


            <div class="tf-slider-items-wrapper">
                <?php while ( $tour_loop->have_posts() ) { 
                    $tour_loop->the_post();
                                $post_id                = get_the_ID();
                                $related_comments       = get_comments( array( 'post_id' => $post_id ) );
                        ?>
                    <div class="tf-slider-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>  
                                </h3>
                                <?php if ($related_comments) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating($related_comments); ?></span>
                                    </div>											
								<?php }?>	
                                <p><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
                                
                            </div>
                        </div>
                    </div>
                <?php }	?>
            </div>
        </div>
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
            'advanced' => '',
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
        $disable_services_info = tfopt('disable-services');
        if ($type == 'all') {
        ?>

        <div class="tf-booking-form-tab">
            <?php 
            if(empty($disable_services_info)){ ?>
                <button class="tf-tablinks active" onclick="tfOpenForm(event, 'tf-hotel-booking-form')"><?php _e('Hotel', 'tourfic'); ?></button>
                <button class="tf-tablinks" onclick="tfOpenForm(event, 'tf-tour-booking-form')"><?php _e('Tour', 'tourfic'); ?></button>
            <?php 
            }
            if(tfopt('disable-services') && !in_array('hotel', tfopt('disable-services'))) { ?>
                <button class="tf-tablinks active" onclick="tfOpenForm(event, 'tf-hotel-booking-form')"><?php _e('Hotel', 'tourfic'); ?></button>
            <?php } ?>
            <?php if(tfopt('disable-services') && !in_array('tour', tfopt('disable-services'))) { ?>
                <button class="tf-tablinks active" onclick="tfOpenForm(event, 'tf-tour-booking-form')"><?php _e('Tour', 'tourfic'); ?></button>
            <?php } ?>
        </div>
        <?php if(empty($disable_services_info)){ ?>
        <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent">             
            <?php 
            if ( !defined( 'TF_PRO' ) ){
                if($advanced=="enabled"){
                    tf_hotel_advanced_search_form_horizontal( $classes, $title, $subtitle );
                }else{    
                    tf_hotel_search_form_horizontal( $classes, $title, $subtitle ); 
                }
            }else{
                tf_hotel_advanced_search_form_horizontal( $classes, $title, $subtitle );
            }
            ?>
        </div>
        <div id="tf-tour-booking-form" class="tf-tabcontent">
            <?php 
            if($advanced=="enabled"){
                tf_tour_advanced_search_form_horizontal( $classes, $title, $subtitle );
            }else{    
                tf_tour_search_form_horizontal( $classes, $title, $subtitle ); 
            }
            ?>
        </div>
        <?php } if(tfopt('disable-services') && !in_array('hotel', tfopt('disable-services'))) { ?>
            <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent">             
                <?php 
                if($advanced=="enabled"){
                    tf_hotel_advanced_search_form_horizontal( $classes, $title, $subtitle );
                }else{    
                    tf_hotel_search_form_horizontal( $classes, $title, $subtitle ); 
                }
                ?>
            </div>
        
        <?php } if(tfopt('disable-services') && !in_array('tour', tfopt('disable-services'))) { ?>
            <div id="tf-tour-booking-form" style="display:block" class="tf-tabcontent">
                <?php 
                if($advanced=="enabled"){
                    tf_tour_advanced_search_form_horizontal( $classes, $title, $subtitle );
                }else{    
                    tf_tour_search_form_horizontal( $classes, $title, $subtitle ); 
                }
                ?>
            </div>
        <?php
        }
        } else if ($type == 'hotel'){
        if(empty($disable_services_info)){  ?>

        <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent">             
            <?php 
            if($advanced=="enabled"){
                tf_hotel_advanced_search_form_horizontal( $classes, $title, $subtitle );
            }else{    
                tf_hotel_search_form_horizontal( $classes, $title, $subtitle ); 
            }
            ?> 
        </div>
        <?php
        }
        if(tfopt('disable-services') && !in_array('hotel', tfopt('disable-services'))) {
        ?>

        <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent">             
        <?php 
            if($advanced=="enabled"){
                tf_hotel_advanced_search_form_horizontal( $classes, $title, $subtitle );
            }else{    
                tf_hotel_search_form_horizontal( $classes, $title, $subtitle ); 
            }
            ?> 
        </div>

        <?php
        } } else if ($type == 'tour'){
        if(empty($disable_services_info)){  ?>
            <div id="tf-tour-booking-form" style="display:block" class="tf-tabcontent">
            <?php 
                if($advanced=="enabled"){
                    tf_tour_advanced_search_form_horizontal( $classes, $title, $subtitle );
                }else{    
                    tf_tour_search_form_horizontal( $classes, $title, $subtitle ); 
                }
            ?>
            </div>
        <?php
        }
        if(tfopt('disable-services') && !in_array('tour', tfopt('disable-services'))) {
        ?>

        <div id="tf-tour-booking-form" style="display:block" class="tf-tabcontent">
        <?php 
            if($advanced=="enabled"){
                tf_tour_advanced_search_form_horizontal( $classes, $title, $subtitle );
            }else{    
                tf_tour_search_form_horizontal( $classes, $title, $subtitle ); 
            }
        ?>
        </div>

        <?php
        } }
        ?>

    </div>

    <?php tourfic_fullwidth_container_end( $fullwidth ); ?>

    <?php return ob_get_clean();
}
add_shortcode('tf_search_form', 'tf_search_form_shortcode');
// Old shortcode
add_shortcode('tf_search', 'tf_search_form_shortcode');

function tf_hotel_register_panel_callback( ){
    ob_start(); 
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        if (in_array('customer', $current_user->roles)) {
            $login_url = site_url('/my-account/');
        }else{
            $login_url = admin_url();
        }
    ?>
    <div class="tf-inline-btn tf-btn">
        <a href="<?php echo esc_url($login_url); ?>" class="btn-styled">Registre su hotel</a>
    </div>
    <?php } return ob_get_clean();
}
add_shortcode('tf_hotel_register_panel', 'tf_hotel_register_panel_callback');
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
    if ( !defined( 'TF_PRO' ) ){
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

        
        // Price Range
        $startprice = isset( $_GET['from'] ) ? absint(sanitize_key($_GET['from'])) : '';
        $endprice = isset( $_GET['to'] ) ? absint(sanitize_key($_GET['to'])) : '';

        if(!empty($startprice) && !empty($endprice)){
            if($_GET['type']=="tf_tours"){
                $data = array($adults, $child, $check_in_out, $startprice, $endprice);
            }else{
                $data = array($adults, $child, $room, $check_in_out, $startprice, $endprice);
            }
        }else{
            $data = array($adults, $child, $room, $check_in_out);
        }



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
    }
    
    $paged          = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
    // Main Query args
    $args = array(
        'post_type'   => 'tf_hotel',
        'post_status' => 'publish',
        'paged'       => $paged,
    );

    $country = isset( $_GET['country'] ) ? sanitize_text_field($_GET['country']) : '';

    if ( $country ) {

        $args['tax_query']['relation'] = "AND";
        $args['tax_query'][] = array(
            'taxonomy' => 'hotel_country',
            'field' => 'slug',
            'terms'    => $country,
        );
    }
    // hotel month
    if ( $_GET['month'] ) {

        $args['tax_query']['relation'] = "AND";
        $args['tax_query'][] = array(
            'taxonomy' => 'hotel_month',
            'field' => 'name',
            'terms'    => $_GET['month'],
        );
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