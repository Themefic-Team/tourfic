<!-- Tourfic Map -->
<?php if ( ($location && $itinerary_map != 1) || ! $itineraries): ?>
<div class="tf-trip-map-wrapper tf-mb-50" id="tf-tour-map">
    <h2 class="tf-title"><?php echo !empty($meta['map-section-title']) ? esc_html($meta['map-section-title']) : __("Maps","tourfic"); ?></h2>
    <div class="tf-map-area tf-mt-30">
    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>
<?php endif; ?>