<!--Overview Start -->
<div class="tf-overview-wrapper">
    <div class="tf-overview-description">
        <?php 
        if(strlen(get_the_content()) > 300 ){
            echo tourfic_character_limit_callback(get_the_content(), 300);
        }else{
            the_content(); 
        }
        ?>
    </div>
</div>
<!--Overview End -->