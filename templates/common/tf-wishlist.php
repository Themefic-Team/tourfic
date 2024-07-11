<?php
/*
Template Name: Tourfic - Wishlist
*/
get_header();
?>
    <div class="tf-container tf-mt-40 tf-mb-40">
		<?php
		while ( have_posts() ) :
			the_post();

			echo do_shortcode( "[tf-wishlist]" );
			the_content();
		endwhile;
		?>
    </div>
<?php
get_footer();
