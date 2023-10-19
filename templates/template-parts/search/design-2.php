<div class="tf-template-3">


    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="background: rgba(48, 40, 28, 0.30);">
        <div class="tf-container">
            <div class="tf-hero-content tf-archive-hero-content">
                <div class="tf-head-title">
                    <h1><?php echo !empty($_GET['place-name']) ? esc_html( $_GET['place-name'] ) : '' ?></h1>
                    <div class="tf-title-meta">
                        <p>( <?php echo !empty($_GET['room']) ? esc_html( $_GET['room'] ) : '0' ?> <?php _e("room", "tourfic"); ?>, <?php echo !empty($_GET['check-in-out-date']) ? esc_html( $_GET['check-in-out-date'] ) : '' ?> )</p>
                    </div>
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
                <!-- Booking form Start -->
                <div class="tf-archive-search-form tf-booking-form-wrapper">
                    <form action="<?php echo tf_booking_search_action(); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
                        <?php tf_search_result_sidebar_form( 'archive' ); ?>
                    </form>
                </div>
                <!-- Booking form end -->                    


                <div class="tf-details-left tf-result-previews">
                    <?php echo do_shortcode("[tf_search_result]"); ?>
                </div>
                <div class="tf-details-right tf-sitebar-widgets tf-archive-right">
                    <div class="tf-filter-wrapper">
                        <div class="tf-filter-title">
                            <h2 class="tf-section-title"><?php _e("Filter", "tourfic"); ?></h2>
                            <button><?php _e("Reset", "tourfic"); ?></button>
                        </div>   
                        <?php //tf_search_result_sidebar_form( 'archive' ); ?>

                        <?php if ( is_active_sidebar( 'tf_search_result' ) ) { ?>
                            <div id="tf__booking_sidebar">
                                <?php dynamic_sidebar( 'tf_search_result' ); ?>
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