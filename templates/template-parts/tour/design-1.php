<div class="tf-single-page tf-template-global">
        <div class="tf-tour-single">
            <div class="tf-template-container">
                <div class="tf-container-inner">
                    <!-- Single Tour Heading Section start -->
                    <div class="tf-section tf-single-head">
                        <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                            <div class="tf-head-title">
                                <h1><?php the_title(); ?></h1>
                                <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?php if ( $location ) {
                                        echo '<a href="#tf-tour-map"><p>' . $location . '.</p></a>';
                                    } ?>
                                </div>
                            </div>
                            <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
                                <?php
                                // Wishlist
                                if ( tfopt( 'wl-bt-for' ) && in_array( '2', tfopt( 'wl-bt-for' ) ) ) { 
                                    if ( is_user_logged_in() ) {
                                    if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
                                ?>
                                <div class="tf-icon tf-wishlist-box">
                                <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                                </div>
                                <?php } } else{ 
                                if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {    
                                ?>
                                <div class="tf-icon tf-wishlist-box">
                                <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                                                            data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>"
                                                                            data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) {
                                            echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
                                        } ?>></i>
                                </div>
                                <?php } } } ?>
                                <div class="tf-icon tf-social-box">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Tour Heading Section End -->

                    <!-- Single Tour Body details start -->
                    <div class="tf-single-details-wrapper tf-mrtop-30">
                        <div class="tf-single-details-inner tf-flex">
                            <div class="tf-column tf-tour-details-left">

                            <?php 
                            if( !empty(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout']) ){
                                foreach(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout'] as $section){
                                    if( !empty($section['tour-section-status']) && $section['tour-section-status']=="1" && !empty($section['tour-section-slug']) ){
                                        include TF_TEMPLATE_PART_PATH . 'tour/design-1/'.$section['tour-section-slug'].'.php';
                                    }
                                }
                            }
                            ?>
                                
                            </div>
    
                            <!-- SIdebar Tour single -->
                            <div class="tf-column tf-tour-details-right">
                               <div class="tf-tour-booking-box tf-box">
                                    <!-- Tourfic Pricing Head -->
                                    <div class="tf-booking-form-data">
                                        <div class="tf-booking-block">
                                            <div class="tf-booking-price tf-padbtm-12">
                                            <?php 
                                            $tour_price = [];
                                            $tf_pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                            $custom_pricing_by_rule = !empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : '';
                                            if( $tf_pricing_rule  && $tf_pricing_rule == 'group' ){
                                                
                                                if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                                                    $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                                                    if ($custom_availability) {
                                                        foreach ( $meta['cont_custom_date'] as $repval ) {
                                        
                                                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                                                if(! empty( $repval['group_price'] )){
                                                                    $tour_price[] = $repval['group_price'];
                                                                }
                                                            }
                                                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                                                if(!empty($repval['adult_price']) && !$disable_adult){
                                                                    $tour_price[] = $repval['adult_price'];
                                                                }
                                                                if(!empty($repval['child_price']) && !$disable_child){
                                                                    $tour_price[] = $repval['child_price'];
                                                                }
                                                                if(!empty($repval['infant_price']) && !$disable_infant){
                                                                    $tour_price[] = $repval['infant_price'];
                                                                }
                                                            }
                                                            
                                                        }
                                                    }else{
                                                        if(!empty($meta['group_price'])){
                                                            $tour_price[] = $meta['group_price'];
                                                        }
                                                    }
                                                }
                                                
                                            }
                                            if( $tf_pricing_rule  && $tf_pricing_rule == 'person' ){
                                
                                                if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                                                    $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                                                    if ($custom_availability) {
                                                        foreach ( $meta['cont_custom_date'] as $repval ) {
                                                            
                                                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                                                if(! empty( $repval['group_price'] )){
                                                                    $tour_price[] = $repval['group_price'];
                                                                }
                                                            }
                                                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                                                if(!empty($repval['adult_price']) && !$disable_adult){
                                                                    $tour_price[] = $repval['adult_price'];
                                                                }
                                                                if(!empty($repval['child_price']) && !$disable_child){
                                                                    $tour_price[] = $repval['child_price'];
                                                                }
                                                                if(!empty($repval['infant_price']) && !$disable_infant){
                                                                    $tour_price[] = $repval['infant_price'];
                                                                }
                                                            }
                                                        }
                                                    }else{
                                                        if(!empty($meta['adult_price']) && !$disable_adult){
                                                            $tour_price[] = $meta['adult_price'];
                                                        }
                                                        if(!empty($meta['child_price']) && !$disable_child){
                                                            $tour_price[] = $meta['child_price'];
                                                        }
                                                        if(!empty($meta['infant_price']) && !$disable_infant){
                                                            $tour_price[] = $meta['infant_price'];
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                                <p> <span><?php _e("From","tourfic"); ?></span> 
                                                <?php 
                                                if(!empty($tour_price)){
                                                    echo $lowest_price = strip_tags( wc_price( min( $tour_price ) ) );
                                                }
                                                ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
    
                                    <!-- Tourfic Booking form -->
                                    <div class="tf-booking-form">
                                        <div class="tf-booking-form-inner tf-mrtop-24">
                                            <h3><?php _e("Book This Tour","tourfic"); ?></h3>
                                            <?php echo tf_single_tour_booking_form( $post->ID ); ?>
                                            
                                        </div>
                                    </div>
                               </div>
                               <?php
                                if (  $email || $phone || $fax || $website) {
                                ?>
                               <div class="tf-tour-booking-advantages tf-box tf-mrtop-30">
                                    <div class="tf-head-title">
                                        <h3><?php echo __( 'Contact Information' , 'tourfic' ) ?></h3>
                                    </div>
                                    <div class="tf-booking-advantage-items">
                                        <ul class="tf-list">
                                            <?php 
                                            if(!empty($phone)){ ?>
                                                <li> <i class="fa-solid fa-headphones"></i> <a href="tel:<?php echo esc_html( $phone ) ?>"><?php echo esc_html( $phone ) ?></a> </li>
                                            <?php } ?>
                                            <?php 
                                            if(!empty($email)){ ?>
                                                <li> <i class="fa-solid fa-envelope"></i> <a href="mailto:<?php echo esc_html( $email ) ?>"><?php echo esc_html( $email ) ?></a> </li>
                                            <?php } ?>
                                            <?php 
                                            if(!empty($website)){ ?>
                                                <li> <i class="fa-solid fa-link"></i> <a target="_blank" href="<?php echo esc_html( $website ) ?>"><?php echo esc_html( $website ) ?></a> </li>
                                            <?php } ?>
                                            <?php 
                                            if(!empty($fax)){ ?>
                                                <li> <i class="fa-solid fa-fax"></i> <a href="tel:<?php echo esc_html( $fax ) ?>"><?php echo esc_html( $fax ) ?></a> </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- Single Tour Body details End -->
                </div>
                <!--  Upcoming tours  -->
            </div>
        </div>

        <?php
        if ( ! $disable_related_tour == '1' ) {
        $related_tour_type = tfopt('rt_display');
        $args  = array(
            'post_type'      => 'tf_tours',
            'post_status'    => 'publish',
            'posts_per_page' => 8,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'tour_destination',
                    'field'    => 'slug',
                    'terms'    => $first_destination_slug,
                ),
            ),
        );
        //show related tour based on selected tours
        $selected_ids = tfopt('tf-related-tours');
        $args['post__not_in'] = array( $post_id );

        if( $related_tour_type == 'selected' && defined( 'TF_PRO' ) ){
            $args['post__in'] = $selected_ids;
        }
        $tours = new WP_Query( $args );
        if ( $tours->have_posts() ) {
        ?>

        <!-- Tourfic upcomming tours tours -->
        <div class="upcomming-tours">
            <div class="tf-template-container">
                <div class="tf-container-inner">
                    <div class="section-title">
                        <h2 class="tf-title"><?php !empty(tfopt('rt-title')) ? esc_html_e(tfopt('rt-title'), "tourfic") : _e("Related Tour","tourfic"); ?></h2>
                        <?php 
                        if( !empty( tfopt('rt-description') ) ){ ?>
                            <p><?php esc_html_e( tfopt('rt-description'), "tourfic" ) ?></p>
                        <?php } ?>
                    </div>
                    <div class="tf-upcomming-tours-list-outter tf-mrtop-40 tf-flex tf-flex-gap-24">
                        <?php
                        while ( $tours->have_posts() ) {
                            $tours->the_post();

                            $post_id                = get_the_ID();
                            $destinations           = get_the_terms( $post_id, 'tour_destination' );
                            $first_destination_name = $destinations[0]->name;
                            $related_comments       = get_comments( array( 'post_id' => $post_id ) );
                            $meta                   = get_post_meta( $post_id, 'tf_tours_opt', true );
                            $pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                            $disable_adult          = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
                            $disable_child          = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
                            $tour_price             = new Tour_Price( $meta );
                        ?>
                        <div class="tf-post-box-lists">
                            <div class="tf-post-single-box">
                                <div class="tf-image-data">
                                    <img src="<?php echo !empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : TF_ASSETS_APP_URL.'/images/feature-default.jpg'; ?>" alt="">
                                    <div class="tf-meta-data-price">
                                        <?php _e("From", "tourfic"); ?> 
                                        <span>
                                        <?php if ( $pricing_rule == 'group' ) {
                                            echo $tour_price->wc_sale_group ?? $tour_price->wc_group;
                                        } else if ( $pricing_rule == 'person' ) {
                                            if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                echo $tour_price->wc_sale_adult ?? $tour_price->wc_adult;
                                            } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                echo $tour_price->wc_sale_child ?? $tour_price->wc_child;

                                            }
                                        }
                                        ?>
                                        </span> 
                                    </div>
                                </div>
                                <div class="tf-meta-info tf-mrtop-30">
                                    <div class="tf-meta-location">
                                        <i class="fa-solid fa-location-dot"></i> <?php echo $first_destination_name; ?>
                                    </div>
                                    <div class="tf-meta-title">
                                        <h2><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php }
        wp_reset_postdata();
        ?>
        <?php } ?>
    </div>