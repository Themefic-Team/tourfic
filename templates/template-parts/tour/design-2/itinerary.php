<?php
if ( function_exists('is_tf_pro') && is_tf_pro() ) {
    do_action( 'after_itinerary_builder', $itineraries, $itinerary_map );
} else {
?>
<?php if ( $itineraries ) { ?>
<div class="tf-itinerary-wrapper" id="tf-tour-itinerary">
    <div class="section-title">
        <h2 class="tf-title tf-section-title"><?php _e("Travel Itinerary", "tourfic"); ?></h2>
    </div>
    <div class="tf-itinerary-wrapper">

    <?php
    foreach ( $itineraries as $itinerary ) {
    ?>
        <div class="tf-single-itinerary">
            <div class="tf-itinerary-title">
                <h4>
                    <span class="tf-itinerary-time">
                        <?php echo esc_html( $itinerary['time'] ) ?>
                    </span>
                    <span class="tf-itinerary-title-text">
                        <?php echo esc_html( $itinerary['title'] ); ?>
                    </span>
                </h4>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="tf-itinerary-content-wrap" style="display: none;">
                <div class="tf-itinerary-content">
                    <div class="tf-itinerary-content-details">
                    <?php _e( $itinerary['desc'] ); ?>
                    </div>
                    <?php if ( $itinerary['image'] ) { ?>
                    <div class="tf-itinerary-content-images">
                        <img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php _e("Itinerary Image","tourfic"); ?>" />
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
        
    </div>
    <?php if ( $location && $itinerary_map != 1 ): ?>
    <!-- Map start -->
    <div id="tf-map" class="tf-itinerary-map">
    <?php if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) && empty($tf_google_map_key) ) {  ?>
        <div id="tour-location" style="height: 450px;"></div>
        <script>
        const map = L.map('tour-location').setView([<?php echo $location_latitude; ?>, <?php echo $location_longitude; ?>], <?php echo $location_zoom; ?>);

        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const marker = L.marker([<?php echo $location_latitude; ?>, <?php echo $location_longitude; ?>], {alt: '<?php echo $location; ?>'}).addTo(map)
            .bindPopup('<?php echo $location; ?>');
        </script>
    <?php } ?>
    <?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) && empty($tf_google_map_key) ) {  ?>
        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    <?php } ?>
    <?php if( $tf_openstreet_map!="default" && !empty($tf_google_map_key) ){ ?>
    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    <?php } ?>
    </div>
    <!-- Map End -->
    <?php endif; ?>
</div>
<?php } ?>
<?php } ?>