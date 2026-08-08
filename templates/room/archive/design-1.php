<?php

use \Tourfic\Classes\Helper;
use Tourfic\App\Templates\Components\Room\Archive\Listings;

defined( 'ABSPATH' ) || exit;
?>

<div class="tf-archive-template__one sp-0">
    <?php Helper::tf_archive_sidebar_search_form('tf_room'); ?>
    <?php Listings::render_design_1(); ?>
</div>
