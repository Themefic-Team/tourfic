<?php
/**
 * Template: Hotel Location Archive
 */

get_header();

$term = get_queried_object();
$post_type = 'tf_hotel';
$taxonomy = $term->taxonomy;
$taxonomy_name = $term->name;
$taxonomy_slug = $term->slug;
$max = '8';

$tf_location_meta      = get_term_meta( $term->term_id, 'tf_hotel_location', true );
$tf_location_image = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : '';

$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';

if( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template=="design-1" ){
?>
<div class="tf-archive-page tf-template-global tf-archive-design-1">
    <div class="tf-container">
		<h3><?php echo $taxonomy_name; ?></h3>
        <div class="tf-row tf-archive-inner tf-flex">
		<?php require_once TF_TEMPLATE_PART_PATH . 'archive.php'; ?>
		<!-- SideBar-->
		<div class="tf-column tf-sidebar tf-archive-right">
			<?php tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug); ?>
		</div>
		</div>
	</div>
</div>
<?php } elseif( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template=="design-2" ){ ?>

<div class="tf-template-3">
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty($tf_location_image) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($tf_location_image).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content tf-archive-hero-content">
                <div class="tf-head-title">
                    <h1><?php echo $taxonomy_name; ?></h1>
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
                        <?php _e("Modify search", "tourfic"); ?>
                    </span>
                    <!-- Booking form Start -->
                    <div class="tf-archive-search-form tf-booking-form-wrapper">
                        <form action="<?php echo tf_booking_search_action(); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
                            <?php tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug); ?>
                        </form>
                    </div>
                    <!-- Booking form end --> 
					<?php require_once TF_TEMPLATE_PART_PATH . 'archive.php'; ?>
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

<?php } else{ ?>
<div class="tf-main-wrapper" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf-container">
		<h3><?php echo $taxonomy_name; ?></h3>
		<div class="search-result-inner">

			<div class="tf-search-left">
				<?php require_once TF_TEMPLATE_PART_PATH . 'archive.php'; ?>
			</div>

			<div class="tf-search-right">
				<?php tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug); ?>
			</div>

		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php
}
get_footer();