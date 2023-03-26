<!-- Tour itenarary -->
<?php
if ( function_exists('is_tf_pro') && is_tf_pro() ) {
    do_action( 'after_itinerary_builder', $itineraries, $itinerary_map );
} else {
?>
<!-- Travel Itinerary section Start -->
<?php if ( $itineraries ) { ?>
<div class="tf-itinerary-wrapper tf-mrbottom-70">
    <div class="section-title">
        <h2 class="tf-title"><?php _e("Travel Itinerary","tourfic"); ?></h2>
    </div>
    <div class="tf-itinerary-box tf-box tf-mrtop-30">
        <div class="tf-itinerary-items">
            <?php 
            $itineray_key = 1;
            foreach ( $itineraries as $itinerary ) {
            ?>
            <div class="tf-single-itinerary-item <?php echo $itineray_key==1 ? esc_attr( 'active' ) : ''; ?>">
                <div class="tf-itinerary-title">
                    <h3>
                        <span class="accordion-checke"></span>
                        <span class="itinerary-day"><?php echo esc_html( $itinerary['time'] ) ?> - </span> <?php echo esc_html( $itinerary['title'] ); ?>
                    </h3>
                </div>
                <div class="tf-itinerary-content-box">
                    <div class="tf-itinerary-content tf-mrtop-16 tf-flex-gap-16 tf-flex">
                        <?php if ( $itinerary['image'] ) { ?>
                            <div class="tf-itinerary-content-img">
                                <img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php _e("Itinerary Image","tourfic"); ?>" />
                            </div>
                        <?php } ?>
                        <div class="<?php echo !empty($itinerary['image']) ? esc_attr('tf-itinerary-content-details') : ''; ?>">
                        <p><?php _e( $itinerary['desc'] ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $itineray_key++; } ?>
        </div>
    </div>
</div>

<?php }} ?>