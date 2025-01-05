<!--Overview Start -->
<div class="tf-overview-wrapper">
    <div class="tf-overview-description">
        <div class="tf-short-description">
			<?php
			if ( strlen( get_the_content() ) > 300 ) {
				echo wp_kses_post( wp_strip_all_tags( \Tourfic\Classes\Helper::tourfic_character_limit_callback( get_the_content(), 300 ) ) ) . '<span class="tf-see-description">' . esc_html__( 'See more', 'tourfic' ) . '</span>';
			} else {
				the_content();
			}
			?>
        </div>
        <div class="tf-full-description">
			<?php
			the_content();
			echo '<span class="tf-see-less-description">' . esc_html__( 'See less', 'tourfic' ) . '</span>';
			?>
        </div>
    </div>
</div>
<!--Overview End -->