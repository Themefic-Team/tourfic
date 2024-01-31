<div class="tf-template-3">

    <?php 
    $tf_hotel_arc_banner = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] : '';
    ?>
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty($tf_hotel_arc_banner) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($tf_hotel_arc_banner).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content tf-archive-hero-content">
                <div class="tf-head-title">
                    <h1><?php _e("Hotels", "tourfic"); ?></h1>
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
                        <?php _e("Modify search", "tourfic"); ?>
                    </span>
                    <!-- Booking form Start -->
                    <div class="tf-archive-search-form tf-booking-form-wrapper">
                        <form action="<?php echo tf_booking_search_action(); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
                            <?php tf_archive_sidebar_search_form('tf_hotel'); ?>
                        </form>
                    </div>
                    <!-- Booking form end -->  
                    
                    <!--Available rooms start -->
                    <div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
                        <div class="tf-archive-available-rooms-head tf-available-rooms-head">
                            <h2 class="tf-total-results"><?php _e("Total", "tourfic"); ?> <span><?php echo $post_count; ?></span> <?php _e("hotels available", "tourfic"); ?></h2>
                            <div class="tf-archive-filter-showing">
                                <i class="ri-equalizer-line"></i>
                            </div>
                        </div>
                        
                        <!-- Loader Image -->
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                            </div>
                        </div>
                        
                        <!--Available rooms start -->
                        <div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">

                            <?php
                            if ( have_posts() ) {
                                while ( have_posts() ) {
                                    the_post();
                                    tf_hotel_archive_single_item();
                                }
                            } else {
                                echo '<div class="tf-nothing-found" data-post-count="0" >' .__("No Tours Found!", "tourfic"). '</div>';
                            }
                            ?>
                            <?php 
                            if(tourfic_posts_navigation()){ ?>
                            <div class="tf-pagination-bar">
                                <?php tourfic_posts_navigation(); ?>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- Available rooms end -->

                    </div>
                    <!-- Available rooms end -->

                </div>
                <div class="tf-details-right tf-sitebar-widgets tf-archive-right">
                    <div class="tf-filter-wrapper">
                        <div class="tf-filter-title">
                            <h2 class="tf-section-title"><?php _e("Filter", "tourfic"); ?></h2>
                            <button class="filter-reset-btn"><?php _e("Reset", "tourfic"); ?></button>
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