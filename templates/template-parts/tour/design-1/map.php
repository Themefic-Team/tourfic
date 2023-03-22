<!-- Tourfic Map -->
<?php if ( ($location && $itinerary_map != 1) || ! $itineraries): ?>
<div class="tf-trip-map-wrapper tf-mrtop-70" id="tf-tour-map">
    <h2 class="tf-title"> <?php _e( "Maps", 'tourfic' ); ?> </h2>
    <div class="tf-map-area tf-mrtop-30">
    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>
<?php endif; ?>