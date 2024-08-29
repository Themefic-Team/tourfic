<div class="tf-template-3">

    <?php
    use \Tourfic\Classes\Helper; 
    use \Tourfic\Classes\Apartment\Apartment; 
    
    $tf_hotel_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
    ?>
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty($tf_hotel_arc_banner) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($tf_hotel_arc_banner).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content tf-archive-hero-content">
                <div class="tf-head-title">
                    <h1><?php esc_html_e("Apartment", "tourfic"); ?></h1>
                </div>
            </div>
        </div>
    </div>
    <!--Hero section End -->


    <!--Content section end -->
    <div class="tf-content-wrapper">
        <?php
            do_action( 'tf_before_container' );
            $post_count = $GLOBALS['wp_query']->post_count;
        ?>
        <div class="tf-container">
        
            <!-- Hotel details Srart -->
            <div class="tf-archive-details tf-details" id="tf-hotel-overview">

                <div class="tf-details-left tf-result-previews">
                    <span class="tf-modify-search-btn">
                        <?php esc_html_e("Modify search", "tourfic"); ?>
                    </span>
                    <!-- Booking form Start -->
                    <div class="tf-archive-search-form tf-booking-form-wrapper">
                        <form action="<?php echo esc_url( Helper::tf_booking_search_action()); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
                            <?php Helper::tf_archive_sidebar_search_form('tf_apartment'); ?>
                        </form>
                    </div>
                    <!-- Booking form end -->  
                    
                    <!--Available rooms start -->
                    <div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
                        <div class="tf-archive-available-rooms-head tf-available-rooms-head">
                            <span class="tf-total-results"><?php esc_html_e("Total", "tourfic"); ?> <span><?php echo esc_html( $post_count ); ?></span> <?php esc_html_e("apartments available", "tourfic"); ?></span>
                            <div class="tf-archive-filter-showing">
                                <i class="ri-equalizer-line"></i>
                            </div>
                        </div>
                        
                        <!-- Loader Image -->
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                            </div>
                        </div>

                        <?php do_action("tf_apartment_archive_card_items_before"); ?>
                        
                        <!--Available rooms start -->
                        <div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">

                            <?php
                            if ( have_posts() ) {
                                while ( have_posts() ) {
                                    the_post();
                                    $apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
                                    if ( !empty($apartment_meta[ "apartment_as_featured" ] )) {
                                        echo apply_filters("tf_apartment_archive_single_featured_card_design_one", Apartment::tf_apartment_archive_single_item());
                                    }
                                } 
                                while ( have_posts() ) {
                                    the_post();
                                    $apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
                                    if ( empty($apartment_meta[ "apartment_as_featured" ] )) {
                                        echo apply_filters("tf_apartment_archive_single_card_design_one", Apartment::tf_apartment_archive_single_item());
                                    }
                                }
                            } else {
                                echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
                            }
                            ?>
                            <?php 
                            if(Helper::tourfic_posts_navigation()){ ?>
                            <div class="tf-pagination-bar">
                                <?php Helper::tourfic_posts_navigation(); ?>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- Available rooms end -->

                        <?php do_action("tf_apartment_archive_card_items_after"); ?>

                    </div>
                    <!-- Available rooms end -->

                </div>
                <div class="tf-details-right tf-sitebar-widgets tf-archive-right">
                    <div class="tf-filter-wrapper">
                        <div class="tf-filter-title">
                            <h2 class="tf-section-title"><?php esc_html_e("Filter", "tourfic"); ?></h2>
                            <button class="filter-reset-btn"><?php esc_html_e("Reset", "tourfic"); ?></button>
                        </div>   
                        <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                        <div id="tf__booking_sidebar">
                            <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                        </div>
                        <?php } ?>
        
                    </div> 

                    
    
                </div>        
            </div>        
            <!-- Hotel details End -->

        </div>
    </div>
    <!--Content section end -->

    
    <!-- Hotel PopUp Starts -->       
    <div class="tf-popup-wrapper tf-hotel-popup">
        <div class="tf-popup-inner">
            <div class="tf-popup-body">
                
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Hotel PopUp end -->  

</div>