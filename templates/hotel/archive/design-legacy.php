<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\App\Templates\Components\Shared\Archive\Sidebar;
use Tourfic\App\Templates\Components\Hotel\Archive\Listings;
?>

<div class="tf-main-wrapper tf-archive-template__legacy" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf-container">
		<div class="search-result-inner">

        	<?php Listings::render_design_legacy(); ?>

			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php Helper::tf_archive_sidebar_search_form('tf_hotel'); ?>
				<?php Sidebar::render( ['design' => 'design-1'] ); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
