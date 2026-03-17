<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Hotel\Components\Archive\Listings;
?>

<div class="tf-main-wrapper tf-archive-template__legacy" data-fullwidth="true">
	<?php
		do_action( 'tf_before_container' );
		$post_count = $GLOBALS['wp_query']->post_count;
	?>
	<div class="tf-container">
		<div class="search-result-inner">

        	<?php Listings::render_design_legacy(); ?>

			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php Helper::tf_archive_sidebar_search_form('tf_hotel'); ?>
				<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                    <div id="tf__booking_sidebar">
                        <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                    </div>
                <?php } ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
