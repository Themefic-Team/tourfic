<?php 

$places_section_title = !empty($meta["section-title"]) ? $meta["section-title"] : "What's around?";
$places_meta = !empty($meta["nearby-places"]) ? $meta["nearby-places"] : array();

?>
<div class="tf-hotel-single-places  tf-mb-50 tf-template-section">
    <h2 class="tf-title tf-section-title"><?php echo __($places_section_title ,'tourfic') ?></h2>
    <ul>
    <?php foreach ( $places_meta as $feature ) {
         $feature_icon = '<i class="' . $feature['place-icon'] . '"></i>';
         ?>
         <li>
            <span> <?php echo $feature_icon; ?> <?php echo $feature["place-title"] ?></span> 
            <span> <?php echo $feature["place-dist"] ?></span>
        </li>
        <?php } ;?>
    </ul>
</div>