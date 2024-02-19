
<!-- Single trip description -->
<div class="tf-trip-description tf-mb-40 tf-template-section">
    <h2 class="tf-title tf-section-title"><?php echo !empty($meta['description-section-title']) ? esc_html($meta['description-section-title']) : ''; ?></h2>
    <?php the_content(); ?>
</div>