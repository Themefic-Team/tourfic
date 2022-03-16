<?php
/**
 * Template: Tour Destination Archive
 */

get_header();

$term = get_queried_object();
$post_type = 'tf_tours';
$taxonomy = $term->taxonomy;
$taxonomy_name = $term->name;
$taxonomy_slug = $term->slug;
$max = '2';
?>

<div class="tourfic-wrap" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf_container">
		<h3><?php echo $taxonomy_name; ?></h3>
		<div class="tf_row">

			<div class="tf_content">
				<?php require_once TF_TEMPLATE_PART_PATH . 'archive.php'; ?>
			</div>

			<div class="tf_sidebar">
				<?php tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug); ?>
			</div>

		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php
get_footer('tourfic');