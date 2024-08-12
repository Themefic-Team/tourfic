<div class="tf-short-description">
<?php 
if(strlen(get_the_content()) > 300 ){
    echo esc_html( wp_strip_all_tags(\Tourfic\Classes\Helper::tourfic_character_limit_callback(get_the_content(), 300)) ) . '<span class="tf-see-description">See more</span>';
}else{
    the_content(); 
}
?>
</div>