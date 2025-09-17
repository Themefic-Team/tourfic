<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>
<div class="tf-car-location" id="tf-location">
    <?php if(!empty($location_title)){ ?>   
    <h3><?php echo esc_html($location_title); ?></h3>
    <?php } ?>

    <div class="tf-car-location-map">
        <?php if ( $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude) ) {  ?>
        <div id="car-location" style="height: 260px;"></div>
        <script>
            const map = L.map('car-location').setView([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], <?php echo esc_html($address_zoom); ?>);

            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], {alt: '<?php echo esc_html($address); ?>'}).addTo(map)
                .bindPopup('<?php echo esc_html($address); ?>');
        </script>
        <?php }else{ ?>
            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&output=embed" width="100%" height="260" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        <?php } ?>
    </div>
</div>