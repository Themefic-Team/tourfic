<?php
get_header();


$bricks_data = \Bricks\Helpers::get_bricks_data( get_the_ID(), 'content' );
return \Bricks\Frontend::render_content( $bricks_data );

get_footer();
?>