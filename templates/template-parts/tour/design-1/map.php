<!-- Tourfic Map -->
<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

if ( $location && $itinerary_map != 1 && ! $itineraries ): ?>
<div class="tf-trip-map-wrapper tf-mb-50 tf-template-section" id="tf-tour-map">
    <h2 class="tf-title tf-section-title"><?php echo !empty($meta['map-section-title']) ? esc_html($meta['map-section-title']) : ''; ?></h2>
    <div class="tf-map-area">
        <?php if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) ) {  ?>
            <div id="tour-location"></div>
        <?php } ?>
        <?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) && empty($tf_google_map_key) ) {  ?>
            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        <?php } ?>
        <?php if( $tf_openstreet_map!="default" && !empty($tf_google_map_key) ){ ?>
        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        <?php } ?>
    </div>
</div>
<?php endif; ?>