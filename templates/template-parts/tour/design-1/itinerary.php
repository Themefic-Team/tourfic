<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>

<!-- Travel Itinerary section Start -->
<?php if ( $itineraries ) { ?>
<div class="tf-itinerary-wrapper tf-mb-50 tf-template-section">
    <div class="section-title">
        <h2 class="tf-title tf-section-title"><?php echo !empty( $meta['itinerary-section-title'] ) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
    </div>
    <div class="tf-itinerary-box tf-box">
        <div class="tf-itinerary-items">
            <?php 
            $itineray_key = 1;
            foreach ( $itineraries as $itinerary ) {
            ?>
            <div class="tf-single-itinerary-item <?php echo $itineray_key==1 ? esc_attr( 'active' ) : ''; ?>">
                <div class="tf-itinerary-title">
                    <h4>
                        <span class="accordion-checke"></span>
                        <span class="itinerary-day"><?php echo esc_html( $itinerary['time'] ) ?> - </span> <?php echo esc_html( $itinerary['title'] ); ?>
                    </h4>
                </div>
                <div class="tf-itinerary-content-box" style="<?php echo $itineray_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                    <div class="tf-itinerary-content tf-mt-16 tf-flex-gap-16 tf-flex">
                        <?php if ( $itinerary['image'] ) { ?>
                            <div class="tf-itinerary-content-img">
                                <img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php esc_html_e("Itinerary Image","tourfic"); ?>" />
                            </div>
                        <?php } ?>
                        <div class="<?php echo !empty($itinerary['image']) ? esc_attr('tf-itinerary-content-details') : ''; ?>">
                        <p><?php echo wp_kses_post( wpautop($itinerary['desc']) ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $itineray_key++; } ?>
        </div>
    </div>
</div>
<?php if ( $location && $itinerary_map != 1 ): ?>
<div class="tf-trip-map-wrapper tf-mb-50 tf-template-section" id="tf-tour-map">
    <h2 class="tf-title tf-section-title"><?php echo !empty($meta['map-section-title']) ? esc_html($meta['map-section-title']) : ''; ?></h2>
    <div class="tf-map-area">
        <?php if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) && empty($tf_google_map_key) ) {  ?>
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

<?php } ?>