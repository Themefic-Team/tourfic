<?php
defined( 'ABSPATH' ) || exit;

if ( function_exists('is_tf_pro') && is_tf_pro() ) {
    do_action( 'after_itinerary_builder', $itineraries, $itinerary_map );
} else {
?>
<?php if ( $itineraries ) { ?>
<div class="tf-itinerary-wrapper" id="tf-tour-itinerary">
    <div class="section-title">
        <h2 class="tf-title tf-section-title"><?php echo !empty($meta['itinerary-section-title']) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
    </div>
    <div class="tf-itinerary-wrapper">

    <?php
    foreach ( $itineraries as $itinerary ) {
    ?>
        <div class="tf-single-itinerary">
            <div class="tf-itinerary-title">
                <span class="tf-head-title">
                    <span class="tf-itinerary-time">
                        <?php echo esc_html( $itinerary['time'] ) ?>
                    </span>
                    <span class="tf-itinerary-title-text">
                        <?php echo esc_html( $itinerary['title'] ); ?>
                    </span>
                </span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="tf-itinerary-content-wrap" style="display: none;">
                <div class="tf-itinerary-content">
                    <div class="tf-itinerary-content-details">
                        <?php echo wp_kses_post( $itinerary['desc'] ); ?>
                    </div>
                    <?php if ( $itinerary['image'] ) { ?>
                    <div class="tf-itinerary-content-images">
                        <img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php esc_html_e("Itinerary Image","tourfic"); ?>" />
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
        
    </div>

</div>
<?php } ?>
<?php 
if ( $location && $itinerary_map != 1 ){
    \Tourfic\App\Templates\Components\Global\Single\Map::render([
        'wrapper_open' => '<div class="tf-mt-16 tf-mb-30">',
        'wrapper_close' => '</div>'
    ], '', '450px', false);
} 
?>
<?php } ?>