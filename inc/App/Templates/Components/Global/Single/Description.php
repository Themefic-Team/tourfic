<?php

namespace Tourfic\App\Templates\Components\Global\Single;

use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Description Component
 * Shared markup for Elementor and Bricks Description widgets
 */
class Description {

	/**
	 * Static render method for Description component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = 'elementor' ) {
		$post_type = get_post_type();
		$limit_content = Helper::get_switcher_value( $settings, 'limit_content', 'yes', $builder );
		$content_length = ! empty( $settings['content_length'] ) ? $settings['content_length'] : 300;
		
		if ( 'tf_apartment' === $post_type ) {
			$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
			$description_title = ! empty( $meta['description_title'] ) ? esc_html( $meta['description_title'] ) : '';
			echo '<h2 class="section-heading">' . esc_html( $description_title ) . '</h2>';
		}
		
        if ( $limit_content ) : ?>
			<div class="tf-short-description tf-post-content">
				<?php
				$content = get_the_content();
				if ( strlen( $content ) > $content_length ) {
					echo esc_html( wp_strip_all_tags( Helper::tourfic_character_limit_callback( $content, $content_length ) ) ) . '<span class="tf-see-description">' . esc_html__( 'See more', 'tourfic' ) . '</span>';
				} else {
					the_content();
				}
				?>
			</div>
			<div class="tf-full-description tf-post-content">
				<?php
				the_content();
				echo '<span class="tf-see-less-description">' . esc_html__( 'See less', 'tourfic' ) . '</span>';
				?>
			</div>
		<?php else : ?>
			<div class="tf-post-content">
				<?php the_content(); ?>
			</div>
		<?php endif;
	}
}
