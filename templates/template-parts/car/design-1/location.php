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
        <?php }else{ ?>
            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&output=embed" width="100%" height="260" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        <?php } ?>
    </div>
</div>