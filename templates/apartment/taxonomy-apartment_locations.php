<?php
/**
 * Template: Apartment Location Archive
 * @author Foysal
 */

 if(wp_is_block_theme()){
    wp_head();
    block_header_area();
}else{
    get_header();
}

use \Tourfic\Classes\Helper;

if ( !Helper::tf_is_woo_active() ) {
    ?>
    <div class="tf-container">
        <div class="tf-notice tf-notice-danger">
            <?php esc_html_e( 'Please install and activate WooCommerce plugin to use this feature.', 'tourfic' ); ?>
        </div>
    </div>
    <?php
    get_footer();
    return;
}

$term = get_queried_object();
$post_type = 'tf_apartment';
$taxonomy = $term->taxonomy;
$taxonomy_name = $term->name;
$taxonomy_slug = $term->slug;
$max = '8';

$tf_location_meta      = get_term_meta( $term->term_id, 'tf_apartment_location', true );
$tf_location_image = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : '';

$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';
if( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template=="design-1" ){
?>

<div class="tf-archive-template__two">
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty($tf_location_image) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($tf_location_image).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content tf-archive-hero-content">
                <div class="tf-head-title">
                    <h1><?php echo esc_html( $taxonomy_name ); ?></h1>
                </div>
            </div>
        </div>
    </div>
    <!--Hero section End -->

    <!--Content section end -->
    <div class="tf-content-wrapper">
        <div class="tf-container">
        
            <!-- Hotel details Srart -->
            <div class="tf-archive-details tf-details" id="tf-hotel-overview">                    
                
                <div class="tf-details-left tf-result-previews">
                    <span class="tf-modify-search-btn">
                        <?php esc_html_e("Modify search", "tourfic"); ?>
                    </span>
                    <?php Helper::tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug); ?>
					<?php require_once TF_TEMPLATE_PART_PATH . 'archive.php'; ?>
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
    <?php }elseif( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template=="design-2" ){?>
    <div class="tf-archive-template__three">

        <div class="tf-content-wrapper">
            <?php
            do_action('tf_before_container');
            $post_count = $GLOBALS['wp_query']->post_count;
            $tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
            $tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
            ?>

            <div class="tf-archive-search-form tf-booking-form-wrapper">
                <div class="tf-container">
                    <?php Helper::tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug); ?>
                </div>
            </div>

            <?php require_once TF_TEMPLATE_PART_PATH . 'archive.php'; ?>
        </div>
        <!--Content section end -->

    </div>

<?php }else{ ?>
<div class="tf-main-wrapper tf-archive-template__legacy" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf-container">
		<h3><?php echo esc_html( $taxonomy_name ); ?></h3>
		<div class="search-result-inner">

			<div class="tf-search-left">
				<?php require_once TF_TEMPLATE_PART_PATH . 'archive.php'; ?>
			</div>

			<div class="tf-search-right">
				<?php Helper::tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug); ?>
                <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                    <div id="tf__booking_sidebar">
                        <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                    </div>
                <?php } ?>
            </div>

		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php
}
if(wp_is_block_theme()){
    wp_footer();
    block_footer_area();
 }else{
	get_footer();
 }