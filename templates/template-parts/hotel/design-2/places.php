<?php 

$places_section_title = !empty($meta["section-title"]) ? $meta["section-title"] : "";
$places_meta = !empty($meta["nearby-places"]) ? $meta["nearby-places"] : array();
?>

<?php if( count($places_meta) > 0 ) : ?>
    <div class="tf-hotel-single-places  tf-mb-50 tf-template-section">
        <?php if(!empty($places_section_title) ) : ?>
            <h2 class="tf-title tf-section-title"><?php echo esc_html($places_section_title) ?></h2>
        <?php endif; ?>
        <ul>
        <?php foreach ( $places_meta as $place ) {
            $place_icon = '<i class="' . $place['place-icon'] . '"></i>';
            ?>
            <li>
                <span> <?php echo wp_kses_post($place_icon); ?> <?php echo esc_html($place["place-title"]) ?></span>
                <span> <?php echo esc_html($place["place-dist"]) ?></span>
            </li>
            <?php } ;?>
        </ul>
    </div>
<?php endif; ?>
