<div class="tf-template-3">
<?php 

use \Tourfic\Classes\Helper;
// Check nonce security
if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_GET['_nonce'])), 'tf_ajax_nonce' ) ) {
	return;
}
if( !empty($_GET['type']) && $_GET['type']=="tf_tours" ){
	$tf_search_result_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] : '';
}elseif( !empty($_GET['type']) && $_GET['type']=="tf_hotel" ){
	$tf_search_result_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] : '';
}else{
    $tf_search_result_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
}
?>
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty($tf_search_result_banner) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($tf_search_result_banner).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content tf-archive-hero-content">
                <div class="tf-head-title">
                    <h1><?php echo !empty($_GET['place-name']) ? esc_html( $_GET['place-name'] ) : '' ?></h1>
                    <?php if( !empty($_GET['type']) && "tf_tours"==$_GET['type'] ){ 
                    $tf_adults = !empty($_GET['adults']) ? absint( sanitize_key($_GET['adults']) ) : 0;
                    $tf_children = !empty($_GET['children']) ? absint( sanitize_key($_GET['children']) ) : 0;
                    ?>
                        <div class="tf-title-meta">
                            <p>( <?php echo esc_html( $tf_adults + $tf_children ); ?> <?php esc_html_e("Guest", "tourfic"); ?>, <?php echo !empty($_GET['check-in-out-date']) ? esc_html( $_GET['check-in-out-date'] ) : '' ?> )</p>
                        </div>
                    <?php } if( !empty($_GET['type']) && "tf_hotel"==$_GET['type'] ){ ?>
                    <div class="tf-title-meta">
                        <p>( <?php echo !empty($_GET['room']) ? esc_html( $_GET['room'] ) : '0' ?> <?php esc_html_e("room", "tourfic"); ?>, <?php echo !empty($_GET['check-in-out-date']) ? esc_html( $_GET['check-in-out-date'] ) : '' ?> )</p>
                    </div>
                    <?php } ?>
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
                        <form action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
                            <?php Helper::tf_search_result_sidebar_form( 'archive' ); ?>
                        </form>
                    </div>
                    <!-- Booking form end -->        
                    <?php echo do_shortcode("[tf_search_result]"); ?>
                </div>
                <div class="tf-details-right tf-sitebar-widgets tf-archive-right">
                    <div class="tf-filter-wrapper">
                        <div class="tf-filter-title">
                            <h2 class="tf-section-title"><?php esc_html_e("Filter", "tourfic"); ?></h2>
                            <button class="filter-reset-btn" style="display: block;"><?php esc_html_e("Reset", "tourfic"); ?></button>
                        </div>

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