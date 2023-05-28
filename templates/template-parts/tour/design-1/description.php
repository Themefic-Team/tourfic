
<!-- Single trip description -->
<div class="tf-trip-description tf-mb-40">
    <h2 class="tf-title"><?php echo !empty($meta['description-section-title']) ? esc_html($meta['description-section-title']) : __("Description","tourfic"); ?></h2>
    <?php the_content(); ?>
</div>