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
<?php 

$tf_plugin_installed = get_option('tourfic_template_installed'); 
if (!empty($tf_plugin_installed)) {
	$tf_tour_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
}else{
	$tf_tour_arc_selected_template = 'default';
}

if( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template=="design-1" ) ){
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
get_footer('tourfic');