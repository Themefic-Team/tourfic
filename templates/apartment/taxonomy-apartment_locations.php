<?php
/**
 * Template: Apartment Location Archive
 * @author Foysal
 */

get_header();

$term = get_queried_object();
$post_type = 'tf_apartment';
$taxonomy = $term->taxonomy;
$taxonomy_name = $term->name;
$taxonomy_slug = $term->slug;
$max = '8';
?>

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
get_footer();