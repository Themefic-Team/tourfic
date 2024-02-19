<?php
/*
Template Name: Tourfic - Wishlist
*/
get_header();
while ( have_posts() ) :
	the_post();

	echo do_shortcode("[tf-wishlist]");
	the_content();
endwhile;
get_footer();
