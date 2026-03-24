<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>

<div class="tf-archive-template__two">

    <?php
    use \Tourfic\Classes\Helper;
    use Tourfic\Classes\Apartment\Components\Archive\Listings;

    $tf_apartment_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
    ?>
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty($tf_apartment_arc_banner) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($tf_apartment_arc_banner).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
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
        <?php do_action( 'tf_before_container' ); ?>
        <div class="tf-container">
        
            <!-- Hotel details Srart -->
            <div class="tf-archive-details tf-details" id="tf-hotel-overview">

                <div class="tf-details-left tf-result-previews">
                    <span class="tf-modify-search-btn">
                        <?php esc_html_e("Modify search", "tourfic"); ?>
                    </span>
                    <?php Helper::tf_archive_sidebar_search_form('tf_apartment'); ?> 
                    
                    <?php Listings::render_design_1(); ?>

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