<?php
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Apartment\Apartment;
use \Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
?>

<div class="tf-template-3 tf-hotel-single tf-apartment-single">
<!--Hero section start -->
<div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url(get_the_post_thumbnail_url()).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background-color: rgba(48, 40, 28, 0.30); background-image: url('.esc_url(TF_ASSETS_APP_URL . '/images/feature-default.jpg').');' ?>">
    <div class="tf-container">
        <div class="tf-hero-content">
            <div class="tf-wish-and-share">
                <?php
                // Wishlist
                if ( Helper::tfopt( 'wl-bt-for' ) && in_array( '2', Helper::tfopt( 'wl-bt-for' ) ) ) {
                    if ( is_user_logged_in() ) {
                    if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) {
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( "wishlist-nonce" ) ) ?>" data-id="<?php echo esc_attr( $post_id ) ?>" data-type="<?php echo esc_attr( $post_type ) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) { echo 'data-page-title="' . esc_attr( get_the_title( Helper::tfopt( 'wl-page' ) ) ) . '" data-page-url="' . esc_url( get_permalink( Helper::tfopt( 'wl-page' ) ) ) . '"'; } ?>></i>
                </a>
                <?php } } else{
                if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) {
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( "wishlist-nonce" ) ) ?>" data-id="<?php echo esc_attr( $post_id ) ?>" data-type="<?php echo esc_attr( $post_type ) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) { echo 'data-page-title="' . esc_attr( get_the_title( Helper::tfopt( 'wl-page' ) ) ) . '" data-page-url="' . esc_url( get_permalink( Helper::tfopt( 'wl-page' ) ) ) . '"'; } ?>></i>
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
                    <h1><?php echo esc_html( get_the_title() ); ?></h1>
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
<div class="tf-content-wrapper tf-single-pb-56">

    <div class="tf-container">

    <!-- Hotel details Srart -->
    <div class="tf-details" id="tf-apartment-overview">
        <div class="tf-details-left">
            <!-- menu section Start -->
            <div class="tf-details-menu">
                <ul>
                    <li><a class="tf-hashlink" href="#tf-apartment-overview">
                        <?php esc_html_e("Overview", "tourfic"); ?>
                    </a></li>

                    <?php if( !empty( $meta["rooms"])) : ?>
                        <li><a href="#tf-apartment-rooms">
                            <?php esc_html_e("Rooms", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["house_rules"])) : ?>
                        <li><a href="#tf-apartment-rules">
                            <?php esc_html_e("House Rules", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if(!empty( $meta["faq"])) : ?>
                        <li><a href="#tf-apartment-faq">
                            <?php esc_html_e("FAQ's", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty($comments) ) : ?>
                        <li><a href="#tf-apartment-reviews">
                            <?php esc_html_e("Reviews", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>

                    <?php if( !empty( $meta["terms_and_conditions"]) ) : ?>
                        <li><a href="#tf-apartment-policies">
                            <?php esc_html_e("Policies", "tourfic"); ?>
                        </a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- menu section End -->


            <?php
            if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-1']) ){
                foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-1'] as $section){
                    if( !empty($section['aprtment-section-status']) && $section['aprtment-section-status']=="1" && !empty($section['aprtment-section-slug']) ){
                        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/'.$section['aprtment-section-slug'].'.php';
                    }
                }
            }else{
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/description.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/features.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/rooms.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/offer.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/rules.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/facilities.php';
            }
            ?>



        </div>
        <div class="tf-details-right tf-sitebar-widgets">
            <div class="tf-search-date-wrapper tf-single-widgets">
                <?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec ); ?>
            </div>

            <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && isset( $meta['surroundings_places'] ) && ! empty( Helper::tf_data_types( $meta['surroundings_places'] ) ) ): ?>
            <div class="tf-whats-around tf-single-widgets">
                <?php if ( ! empty( $meta['surroundings_sec_title'] ) ): ?>
                    <h2 class="tf-section-title"><?php echo esc_html( $meta['surroundings_sec_title'] ); ?></h2>
                <?php endif; ?>
                <ul>
                    <?php foreach ( Helper::tf_data_types( $meta['surroundings_places'] ) as $surroundings_place ) : ?>
                    <?php if ( isset( $surroundings_place['places'] ) && ! empty( Helper::tf_data_types( $surroundings_place['places'] ) ) ): ?>
                    <?php foreach ( Helper::tf_data_types( $surroundings_place['places'] ) as $place ): ?>
                    <li>
                        <span>
                        <?php if(!empty($surroundings_place['place_criteria_icon'])){ ?>
                        <i class="<?php echo esc_attr( $surroundings_place['place_criteria_icon'] ); ?>"></i>
                        <?php } ?>
                        <?php echo esc_html( $surroundings_place['place_criteria_label'] ); ?>
                        </span>
                        <span><?php echo esc_html( $place['place_name'] ) ?> (<?php echo esc_html( $place['place_distance'] ) ?>)</span>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div id="hotel-map-location" class="tf-location tf-single-widgets">
                <h2 class="tf-section-title"><?php echo ! empty( $meta['location_title'] ) ? esc_html( $meta['location_title'] ) : ''; ?></h2>
                <?php if ( !defined( 'TF_PRO' ) ) { ?>
                    <?php
                    if( $address && $tf_openstreet_map!="default" && ( empty($address_latitude) || empty($address_longitude) ) ){ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address ); ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } elseif( $address && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {
                    ?>
                        <div id="hotel-location" style="height: 250px"></div>
                        <script>
                            const map = L.map('hotel-location').setView([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], <?php echo esc_html( $address_zoom ); ?>);

                            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 20,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            const marker = L.marker([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], {alt: '<?php echo esc_html( $address ); ?>'}).addTo(map)
                                .bindPopup('<?php echo esc_html( $address ); ?>');
                        </script>
                    <?php }else{ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address ); ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?>
                <?php }else{ ?>
                    <?php
                    if( $address && $tf_openstreet_map!="default" && ( empty($address_latitude) || empty($address_longitude) ) ){ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address ); ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } elseif( $address && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {
                    ?>
                        <div id="hotel-location" style="height: 250px"></div>
                        <script>
                            const map = L.map('hotel-location').setView([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], <?php echo esc_html( $address_zoom ); ?>);

                            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 20,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            const marker = L.marker([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], {alt: '<?php echo esc_html( $address ); ?>'}).addTo(map)
                                .bindPopup('<?php echo esc_html( $address ); ?>');
                        </script>
                    <?php }else{ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address ); ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?>
                <?php } ?>
            </div>


            <div class="tf-location tf-single-widgets">
                <?php if( $disable_review_sec != 1 ) :
                global $current_user;
                // Check if user is logged in
                $is_user_logged_in = $current_user->exists();
                $post_id           = $post->ID;
                // Get settings value
                $tf_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
                if ( $comments ) {
                    $tf_overall_rate        = [];
                    TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                    TF_Review::tf_get_review_fields( $fields );
                ?>
                <h2 class="tf-section-title"><?php esc_html_e("Overall reviews", "tourfic"); ?></h2>
                <div class="tf-review-data-inner">
                    <div class="tf-review-data">
                        <div class="tf-review-data-average">
                            <span class="avg-review"><span>
                                <?php echo esc_html(sprintf( '%.1f', $total_rating )); ?>
                            </span>/ <?php echo esc_html( $tf_settings_base ); ?></span>
                        </div>
                        <div class="tf-review-all-info">
                            <p><?php esc_html_e("Excellent", "tourfic"); ?> <span><?php esc_html_e("Total ", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
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
                        $value = TF_Review::tf_average_ratings( $value );
                        ?>
                            <div class="tf-progress-item">
                                <div class="tf-review-feature-label">
                                    <p class="feature-label"><?php echo esc_html($key); ?></p>
                                    <p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: <?php echo esc_html( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
                                </div>
                            </div>
                            <?php } } ?>

                        </div>
                    </div>
                </div>
                <a class="tf-all-reviews" href="#tf-apartment-reviews"><?php esc_html_e("See all reviews", "tourfic"); ?></a>
                <?php } ?>
                <?php
                $tf_comment_counts = get_comments( array(
                    'post_id' => $post_id,
                    'user_id' => $current_user->ID,
                    'count'   => true,
                ) );
                ?>
                <?php if( empty($tf_comment_counts) && $tf_comment_counts == 0 ) : ?>
                    <button class="tf-review-open button">
                        <?php esc_html_e("Leave your review", "tourfic"); ?>
                    </button>
                <?php endif; ?>
                <?php
                // Review moderation notice
                echo wp_kses_post(TF_Review::tf_pending_review_notice( $post_id ) ?? '');
                ?>
                <?php
                if ( ! empty( $tf_ratings_for ) ) {
                    if ( $is_user_logged_in ) {
                    if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                    <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php TF_Review::tf_review_form(); ?>
                </div>
                <?php
		            }
	            } else {
		        if ( in_array( 'lo', $tf_ratings_for ) ) {
			    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                    <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php TF_Review::tf_review_form(); ?>
                </div>
                <?php } } } ?>
                <?php endif; ?>

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
                        <i class="<?php echo esc_attr( $tf_enquiry_section_icon ); ?>" aria-hidden="true"></i>
                        <?php
                    }
                    if(!empty($tf_enquiry_section_title)) {
                        ?>
                        <h3><?php echo  esc_html( $tf_enquiry_section_title ); ?></h3>
                        <?php
                    }
                    if(!empty($tf_enquiry_section_cont)) {
                        ?>
                        <p><?php echo esc_html( $tf_enquiry_section_cont );  ?></p>
                        <?php
                    }
                    if( !empty( $tf_enquiry_section_button )) {
                        ?>
                        <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-send-inquiry-btn"><span><?php echo esc_html( $tf_enquiry_section_button ); ?></span></a></div>
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
    if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-2']) ){
        foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-2'] as $section){
            if( !empty($section['aprtment-section-status']) && $section['aprtment-section-status']=="1" && !empty($section['aprtment-section-slug']) ){
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/'.$section['aprtment-section-slug'].'.php';
            }
        }
    }else{
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/review.php';
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/faq.php';
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/trams-condition.php';
    }
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
        'tax_query'      => array( // WPCS: slow query ok.
            array(
                'taxonomy' => 'apartment_location',
                'field'    => 'term_id',
                'terms'    => wp_list_pluck( $locations, 'term_id' ),
            ),
        ),
    );
    $related_apartment = new WP_Query( $args );
    if ( $related_apartment->have_posts() ) { ?>
        <!-- Tourfic related tours tours -->
        <div class="tf-related-tours">
            <div class="tf-container">
                <div class="tf-container-inner">
                    <div class="section-title">
                        <h2 class="tf-title"><?php echo ! empty( $meta['related_apartment_title'] ) ? esc_html( $meta['related_apartment_title'] ) : ''; ?></h2>
                    </div>
                    <div class="tf-design-3-slider-items-wrapper tf-upcomming-tours-list-outter tf-flex tf-flex-gap-24">
                        <?php
                        while ( $related_apartment->have_posts() ) {
                            $related_apartment->the_post();

                            $selected_design_post_id = get_the_ID();
                            $destinations           = get_the_terms( $selected_design_post_id, 'apartment_location' );
                            $first_destination_name = $destinations[0]->name;
                            $meta                   = get_post_meta( $selected_design_post_id, 'tf_apartment_opt', true );
                            $apartment_min_price = Apt_Pricing::instance( $selected_design_post_id )->get_min_max_price();

                            $pricing_type = ! empty( $meta['pricing_type'] ) && "per_person" == $meta['pricing_type'] ? esc_html__("Person", "tourfic") : esc_html__("Night", "tourfic");
                            if(!in_array($selected_design_post_id, array($post_id))){
                            ?>
                                <div class="tf-slider-item tf-post-box-lists">
                                    <div class="tf-post-single-box">
                                        <div class="tf-image-data">
                                            <img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $selected_design_post_id, 'full' )  ): esc_url(TF_ASSETS_APP_URL . '/images/feature-default.jpg'); ?>" alt="">
                                        </div>
                                        <div class="tf-meta-info">
                                            <div class="meta-content">
                                                <div class="tf-meta-title">
                                                    <h2><a href="<?php echo esc_url( get_permalink($selected_design_post_id) ) ?>">
                                                    <?php echo esc_html( Helper::tourfic_character_limit_callback(get_the_title($selected_design_post_id), 35) ); ?>
                                                    </a></h2>
                                                    <div class="tf-meta-data-price">
                                                        <span><?php echo !empty($apartment_min_price["min"]) ? wp_kses_post(wc_price($apartment_min_price["min"])) : wp_kses_post(wc_price(0));
                                                        ?></span><span class="pricing_calc_type">/<?php echo esc_html( $pricing_type ); ?></span>
                                                    </div>
                                                </div>
                                                <div class="tf-meta-location">
                                                    <i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $first_destination_name ); ?>
                                                </div>
                                            </div>
                                            <a class="see-details" href="<?php echo esc_url( get_permalink($selected_design_post_id) ) ?>">
                                                <?php esc_html_e("See details", "tourfic"); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

</div>