<div class="tf-template-3 tf-hotel-single">
<!--Hero section start -->
<div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url(get_the_post_thumbnail_url()).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
    <div class="tf-container">
        <div class="tf-hero-content">
            <div class="tf-wish-and-share">
                <?php
                // Wishlist
                if ( tfopt( 'wl-bt-for' ) && in_array( '2', tfopt( 'wl-bt-for' ) ) ) { 
                    if ( is_user_logged_in() ) {
                    if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                </a>
                <?php } } else{ 
                if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {    
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                </a>
                <?php } } } ?>
            
                
                <!-- Share Section -->
                <?php if ( ! $disable_share_opt == '1' ) { ?>
                <div class="tf-share tf-off-canvas-share-box">
                    <ul class="tf-off-canvas-share">
                        <li>
                            <a href="http://www.facebook.com/share.php?u=<?php echo esc_url( $share_link ); ?>"
                            class="tf-dropdown-item" target="_blank">
                        <span class="tf-dropdown-item-content">
                            <i class="fab fa-facebook"></i>
                        </span>
                            </a>
                        </li>
                        <li>
                            <a href="http://twitter.com/share?text=<?php echo esc_attr( $share_text ); ?>&url=<?php echo esc_url( $share_link ); ?>"
                            class="tf-dropdown-item" target="_blank">
                        <span class="tf-dropdown-item-content">
                            <i class="fab fa-twitter"></i>
                        </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.linkedin.com/cws/share?url=<?php echo esc_url( $share_link ); ?>"
                            class="tf-dropdown-item" target="_blank">
                        <span class="tf-dropdown-item-content">
                            <i class="fab fa-linkedin"></i>
                        </span>
                            </a>
                        </li>
                        <?php $share_image_link = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ); ?>
                        <li>
                            <a href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url( $share_link ); ?>&media=<?php echo esc_url( get_the_post_thumbnail_url() ); ?>&description=<?php echo esc_attr( $share_text ); ?>"
                            class="tf-dropdown-item" target="_blank">
                        <span class="tf-dropdown-item-content">
                            <i class="fab fa-pinterest"></i>
                        </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="share_link_button" class="share-center-copy-cta">
                            <i class="ri-links-line"></i>
                                <span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
                            </a>
                            <input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr( $share_link ); ?>" readonly style="opacity: 0; width: 0px !important;margin: 0px">
                        </li>
                    </ul>
                    <a href="#dropdown-share-center" class="tf-share-toggle tf-icon tf-social-box"
                    data-toggle="true">
                        <i class="ri-share-line"></i>
                    </a>
                </div>
                <?php } ?>
                <!-- End Share Section -->
                
            </div>
            <div class="tf-hero-bottom-area">
                <div class="tf-head-title">
                    <h1><?php echo get_the_title(); ?></h1>
                    <?php if(!empty($address)) { ?>
                    <div class="tf-title-meta">
                        <i class="ri-map-pin-line"></i>
                        <a href="#hotel-map-location"><?php echo esc_html( $address ); ?></a>
                    </div>
                    <?php } ?>
                </div>
                <div class="tf-hero-gallery-videos">
                    <?php 
                    if ( ! empty( $gallery_ids ) ) {
                    ?>
                    <div class="tf-hero-hotel tf-popup-buttons">
                        <a href="#">
                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"/>
                            <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            </svg>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Hero section End -->


<!--Content section end -->
<div class="tf-content-wrapper">
    
    <div class="tf-container">
    
    <!-- Hotel details Srart -->
    <div class="tf-details" id="tf-hotel-overview">
        <div class="tf-details-left">
            <!-- menu section Start -->
            <div class="tf-details-menu">
                <ul>
                    <li><a class="tf-hashlink" href="#tf-hotel-overview">
                        <?php _e("Overview", "tourfic"); ?>
                    </a></li>
                    <li><a href="#tf-hotel-facilities">
                        <?php _e("Facilities", "tourfic"); ?>
                    </a></li>
                    <li><a href="#tf-hotel-faq">
                        <?php _e("FAQ's", "tourfic"); ?>
                    </a></li>
                    <li><a href="#tf-hotel-reviews">
                        <?php _e("Reviews", "tourfic"); ?>
                    </a></li>
                    <li><a href="#tf-hotel-policies">
                        <?php _e("Policies", "tourfic"); ?>
                    </a></li>
                </ul>
            </div>
            <!-- menu section End -->


            <?php 
            // if( !empty(tf_data_types(tfopt( 'tf-template' ))['single-hotel-layout-part-1']) ){
            //     foreach(tf_data_types(tfopt( 'tf-template' ))['single-hotel-layout-part-1'] as $section){
            //         if( !empty($section['hotel-section-status']) && $section['hotel-section-status']=="1" && !empty($section['hotel-section-slug']) ){
            //             include TF_TEMPLATE_PART_PATH . 'hotel/design-2/'.$section['hotel-section-slug'].'.php';
            //         }
            //     }
            // }else{
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/description.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/facilities.php';
            // }
            ?>



        </div>
        <div class="tf-details-right tf-sitebar-widgets">
            <div class="tf-search-date-wrapper tf-single-widgets">
                <?php tf_apartment_single_booking_form( $comments, $disable_review_sec ); ?>
            </div>
            
            <div id="hotel-map-location" class="tf-location tf-single-widgets">
                <h2 class="tf-section-title"><?php _e("Location", "tourfic"); ?></h2>
                <?php if ( !defined( 'TF_PRO' ) ) { ?>
                    <?php 
                    if( $address && $tf_openstreet_map!="default" && ( empty($address_latitude) || empty($address_longitude) ) ){ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } elseif( $address && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {
                    ?>
                        <div id="hotel-location" style="height: 250px"></div>
                        <script>
                            const map = L.map('hotel-location').setView([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], <?php echo $address_zoom; ?>);

                            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 20,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            const marker = L.marker([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], {alt: '<?php echo $address; ?>'}).addTo(map)
                                .bindPopup('<?php echo $address; ?>');
                        </script>
                    <?php }else{ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?>
                <?php }else{ ?>
                    <?php 
                    if( $address && $tf_openstreet_map!="default" && ( empty($address_latitude) || empty($address_longitude) ) ){ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } elseif( $address && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {
                    ?>
                        <div id="hotel-location" style="height: 250px"></div>
                        <script>
                            const map = L.map('hotel-location').setView([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], <?php echo $address_zoom; ?>);

                            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 20,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            const marker = L.marker([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], {alt: '<?php echo $address; ?>'}).addTo(map)
                                .bindPopup('<?php echo $address; ?>');
                        </script>
                    <?php }else{ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?>
                <?php } ?>
            </div>   
            
            
            <div class="tf-location tf-single-widgets">
                <?php
                global $current_user;
                // Check if user is logged in
                $is_user_logged_in = $current_user->exists();
                $post_id           = $post->ID;
                // Get settings value
                $tf_ratings_for = tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                $tf_settings_base = ! empty ( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
                if ( $comments ) {
                    $tf_overall_rate        = [];
                    tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                    tf_get_review_fields( $fields );
                ?>
                <h2 class="tf-section-title"><?php _e("Overall reviews", "tourfic"); ?></h2>
                <div class="tf-review-data-inner">
                    <div class="tf-review-data">
                        <div class="tf-review-data-average">
                            <h2><span>
                                <?php _e( sprintf( '%.1f', $total_rating ) ); ?>
                            </span>/<?php echo $tf_settings_base; ?></h2>
                        </div>
                        <div class="tf-review-all-info">
                            <p><?php _e("Excellent", "tourfic"); ?> <span><?php _e("Total", "tourfic"); ?> <?php tf_based_on_text( count( $comments ) ); ?></span></p>
                        </div>
                    </div>
                    <div class="tf-review-data-features">
                        <div class="tf-percent-progress">
                        <?php 
                        if ( $tf_overall_rate ) {
                        foreach ( $tf_overall_rate as $key => $value ) {
                        if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                            continue;
                        }
                        $value = tf_average_ratings( $value );
                        ?>
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label"><?php esc_html_e( $key, "tourfic" ); ?></p>
                                    <p class="feature-rating"> <?php echo $value; ?></p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: <?php echo tf_average_rating_percent( $value, tfopt( 'r-base' ) ); ?>%"></span>
                                </div>
                            </div>
                            <?php } } ?>
                                   
                        </div>
                    </div>
                </div>
                <a class="tf-all-reviews" href="#tf-hotel-reviews"><?php _e("See all reviews", "tourfic"); ?></a>
                <?php } ?>
                <button class="tf-review-open button">
                    <?php _e("Leave your review", "tourfic"); ?>
                </button>
                <?php
                // Review moderation notice
                echo tf_pending_review_notice( $post_id );
                ?>
                <?php
                if ( ! empty( $tf_ratings_for ) ) {
                    if ( $is_user_logged_in ) {
                    if ( in_array( 'li', $tf_ratings_for ) && ! tf_user_has_comments() ) {
                    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php _e("Leave your review", "tourfic"); ?></h3>
                    <p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php tf_review_form(); ?>
                </div>
                <?php
		            }
	            } else {
		        if ( in_array( 'lo', $tf_ratings_for ) ) {
			    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php _e("Leave your review", "tourfic"); ?></h3>
                    <p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php tf_review_form(); ?>
                </div>
                <?php } } } ?>

                <!-- Enquery Section -->
                <?php 
                $tf_enquiry_section_status = !empty($meta['enquiry-section']) ? $meta['enquiry-section'] : "";
                $tf_enquiry_section_icon = !empty($meta['apartment-enquiry-icon']) ? esc_html($meta['apartment-enquiry-icon']) : '';
                $tf_enquiry_section_title = !empty($meta['enquiry-title']) ? esc_html($meta['enquiry-title']) : '';
                $tf_enquiry_section_cont = !empty($meta['enquiry-content']) ? esc_html($meta['enquiry-content']) : '';
                $tf_enquiry_section_button = !empty($meta['enquiry-btn']) ? esc_html($meta['enquiry-btn']) : '';
                if(!empty($tf_enquiry_section_status) && ( !empty($tf_enquiry_section_icon) || !empty($tf_enquiry_section_title) || !empty($enquery_button_text))){
                ?>
                <div class="tf-send-inquiry">
                    <?php 
                    if (!empty($tf_enquiry_section_icon)) {
                        ?>
                        <i class="<?php echo $tf_enquiry_section_icon; ?>" aria-hidden="true"></i>
                        <?php
                    }
                    if(!empty($tf_enquiry_section_title)) {
                        ?>
                        <h3><?php echo  $tf_enquiry_section_title; ?></h3>
                        <?php
                    }
                    if(!empty($tf_enquiry_section_cont)) {
                        ?>
                        <p><?php echo $tf_enquiry_section_cont;  ?></p>
                        <?php
                    }
                    if( !empty( $tf_enquiry_section_button )) {
                        ?>
                        <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-send-inquiry-btn"><span><?php echo $tf_enquiry_section_button; ?></span></a></div>
                        <?php
                    }
                    ?>
                </div>
                <?php } ?>
            </div>       
        </div>        
    </div>        
    <!-- Hotel details End -->
    
    <?php 
    // if( !empty(tf_data_types(tfopt( 'tf-template' ))['single-hotel-layout-part-2']) ){
    //     foreach(tf_data_types(tfopt( 'tf-template' ))['single-hotel-layout-part-2'] as $section){
    //         if( !empty($section['hotel-section-status']) && $section['hotel-section-status']=="1" && !empty($section['hotel-section-slug']) ){
    //             include TF_TEMPLATE_PART_PATH . 'hotel/design-2/'.$section['hotel-section-slug'].'.php';
    //         }
    //     }
    // }else{
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/review.php';
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/faq.php';
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/trams-condition.php';
    // }
    ?>

    <?php 
    if ( ! empty( $gallery_ids ) ) {
    ?>
    <!-- Hotel PopUp Starts -->       
    <div class="tf-popup-wrapper tf-hotel-popup">
        <div class="tf-popup-inner">
            
            <div class="tf-popup-body">
                <?php 
                    if ( ! empty( $gallery_ids ) ) {
                    foreach ( $gallery_ids as $key => $gallery_item_id ) {
                    $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="" class="tf-popup-image">
                <?php } } ?>
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Hotel PopUp end -->  
    <?php } ?>


    <!-- Room PopUp Starts -->        
    <div class="tf-popup-wrapper tf-room-popup">
        
    </div>
    <!-- Room PopUp end --> 


    </div>
</div>
<!--Content section end -->

<?php
if ( $disable_related_sec !== '1' ) {
    $args              = array(
        'post_type'      => 'tf_apartment',
        'post_status'    => 'publish',
        'posts_per_page' => 8,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post__not_in'   => array( $post_id ),
        'tax_query'      => array(
            array(
                'taxonomy' => 'apartment_location',
                'field'    => 'term_id',
                'terms'    => wp_list_pluck( $locations, 'term_id' ),
            ),
        ),
    );
    $related_apartment = new WP_Query( $args );
    if ( $related_apartment->have_posts() ) {
        if($related_apartment->found_posts > 0) : ?>

            <!-- Tourfic related tours tours -->
            <div class="tf-related-tours">
                <div class="tf-container">
                    <div class="tf-container-inner">
                        <div class="section-title">
                            <h2 class="tf-title"><?php _e( "You may also like", "tourfic" ); ?></h2>
                            
                        </div>
                        <div class="tf-design-3-slider-items-wrapper tf-upcomming-tours-list-outter tf-flex tf-flex-gap-24">
                            <?php
                            while ( $related_apartment->have_posts() ) {
                                $related_apartment->the_post();

                                $selected_design_post_id = get_the_ID();
                                $destinations           = get_the_terms( $selected_design_post_id, 'apartment_location' );
                                $first_destination_name = $destinations[0]->name;
                                $meta                   = get_post_meta( $selected_design_post_id, 'tf_apartment_opt', true );
                                $apartment_min_price = get_apartment_min_max_price( get_the_ID() );
                                ?>
                                <div class="tf-slider-item tf-post-box-lists">
                                    <div class="tf-post-single-box">
                                        <div class="tf-image-data">
                                            <img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) ? get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) : TF_ASSETS_APP_URL . '/images/feature-default.jpg'; ?>" alt="">
                                            
                                        </div>
                                        <div class="tf-meta-info">
                                            <div class="tf-meta-title">
                                                <h2><a href="<?php echo get_permalink($selected_design_post_id) ?>">
                                                <?php echo tourfic_character_limit_callback(get_the_title($selected_design_post_id), 35); ?>
                                                </a></h2>
                                                <div class="tf-meta-data-price">
                                                    <span>
                                                    <?php
                                                    echo !empty($apartment_min_price["min"]) ? wc_price($apartment_min_price["min"]) : '';
                                                    ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="tf-meta-location">
                                                <i class="fa-solid fa-location-dot"></i> <?php echo $first_destination_name; ?>
                                            </div>
                                            <a class="see-details" href="<?php echo get_permalink($selected_design_post_id) ?>">
                                                <?php _e("See details", "tourfic"); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php }
    wp_reset_postdata();
    ?>
<?php } ?>

</div>